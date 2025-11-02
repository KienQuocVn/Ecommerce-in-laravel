<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoGateway implements PaymentGateway
{
    protected string $partnerCode;
    protected string $accessKey;
    protected string $secretKey;
    protected string $endpoint;
    protected string $redirectUrl;
    protected string $ipnUrl;

    public function __construct()
    {
        $cfg = config('services.momo');
        $this->partnerCode = (string) $cfg['partner_code'];
        $this->accessKey   = (string) $cfg['access_key'];
        $this->secretKey   = (string) $cfg['secret_key'];
        $this->endpoint    = (string) $cfg['endpoint'];
        $this->redirectUrl = (string) $cfg['redirect'];
        $this->ipnUrl      = (string) $cfg['ipn'];
    }

    public function createPayment(Order $order): string
    {
        // Lấy số tiền VNĐ từ Order
        $amountVnd = (float) ($order->total_amount ?? $order->sub_total ?? 0);

        Log::info('MoMo Payment Init:', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'sub_total' => $order->sub_total,
            'amount_vnd' => $amountVnd
        ]);

        // Validate số tiền
        if ($amountVnd < 1000) {
            throw new \RuntimeException(
                "Số tiền phải >= 1,000 VNĐ. Hiện tại: " . number_format($amountVnd, 0) . " VNĐ"
            );
        }

        if ($amountVnd > 50000000) {
            throw new \RuntimeException("Số tiền không được vượt quá 50,000,000 VNĐ");
        }

        $amountVnd = (int) round($amountVnd);

        $requestId = (string) now()->timestamp . rand(1000, 9999);
        // MoMo yêu cầu orderId là số duy nhất, không có ký tự đặc biệt
        // Format: timestamp + order_id (đảm bảo unique, tối đa 50 ký tự theo MoMo docs)
        // Sử dụng format đơn giản: timestamp + order_id để đảm bảo unique
        // Format: timestamp + 5 số order_id (ví dụ: 176209102723)
        $timestamp = now()->timestamp;
        $orderId = (string) ($timestamp . str_pad((string)$order->id, 5, '0', STR_PAD_LEFT));
        $orderInfo = 'Thanh toan don hang #' . ($order->order_number ?? $orderId);
        // Lưu orderId gửi cho MoMo vào extraData để dễ dàng lookup sau
        $extraData = base64_encode(json_encode(['db_order_id' => $order->id]));

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Test',
            'storeId'     => $this->partnerCode,
            'requestType' => 'captureWallet',
            'ipnUrl'      => $this->ipnUrl,
            'redirectUrl' => $this->redirectUrl,
            'orderId'     => $orderId,
            'amount'      => (string) $amountVnd,
            'lang'        => 'vi',
            'orderInfo'   => $orderInfo,
            'requestId'   => $requestId,
            'extraData'   => $extraData,
        ];

        $signature = $this->signCreate($payload);
        $payload['signature'] = $signature;

        Log::info('MoMo Request:', [
            'endpoint' => $this->endpoint,
            'amount' => $amountVnd,
            'orderId' => $orderId,
            'requestId' => $requestId,
            'ipnUrl' => $this->ipnUrl,
            'redirectUrl' => $this->redirectUrl,
            'partnerCode' => $this->partnerCode
        ]);

        $res = Http::timeout(15)->acceptJson()->asJson()->post($this->endpoint, $payload);

        $responseData = $res->json();
        Log::info('MoMo Response:', [
            'status' => $res->status(),
            'resultCode' => $responseData['resultCode'] ?? null,
            'message' => $responseData['message'] ?? null,
            'full_response' => $responseData
        ]);

        if (!$res->successful()) {
            throw new \RuntimeException('MoMo create failed: ' . $res->body());
        }

        if (($responseData['resultCode'] ?? -1) !== 0 || empty($responseData['payUrl'])) {
            throw new \RuntimeException('MoMo rejected: ' . json_encode($responseData));
        }

        // Lưu orderId từ MoMo vào Payment để có thể lookup sau
        Payment::where('order_id', $order->id)
            ->where('provider', 'momo')
            ->update([
                'transaction_id' => $requestId,
                'status'         => 'pending',
                'raw_payload'    => ['momo_order_id' => $orderId], // Lưu để lookup
            ]);

        return $responseData['payUrl'];
    }

    public function handleReturn(array $payload): array
    {
        $resultCode = (int) ($payload['resultCode'] ?? -1);
        $message    = (string) ($payload['message'] ?? 'Unknown');
        $transId    = (string) ($payload['transId'] ?? '');
        $orderId    = (string) ($payload['orderId'] ?? '');

        Log::info('MoMo Return:', [
            'resultCode' => $resultCode,
            'orderId' => $orderId,
            'transId' => $transId,
            'message' => $message,
            'full_payload' => $payload
        ]);

        if ($resultCode === 0) {
            // Thanh toán thành công
            return [
                'status' => 'succeeded',
                'transaction_id' => $transId,
                'message' => $message ?: 'Thanh toán thành công qua MoMo'
            ];
        }

        if ($resultCode === 49) {
            return [
                'status' => 'canceled',
                'transaction_id' => null,
                'message' => 'Bạn đã hủy thanh toán'
            ];
        }

        return [
            'status' => 'failed',
            'transaction_id' => null,
            'message' => $message ?: 'Thanh toán thất bại'
        ];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        Log::info('MoMo IPN Received:', $payload);

        if (!$this->verifyIpn($payload)) {
            Log::warning('MoMo IPN: Invalid signature');
            return [
                'status' => 'failed',
                'transaction_id' => null,
                'message' => 'Invalid signature'
            ];
        }

        $resultCode = (int) ($payload['resultCode'] ?? -1);
        $orderId    = (string) ($payload['orderId'] ?? '');
        $transId    = (string) ($payload['transId'] ?? '');
        $message    = (string) ($payload['message'] ?? '');

        if ($resultCode !== 0) {
            Log::warning('MoMo IPN: Payment failed', [
                'orderId' => $orderId,
                'resultCode' => $resultCode
            ]);

            return [
                'status' => 'failed',
                'transaction_id' => $transId ?: null,
                'message' => $message ?: 'Payment failed'
            ];
        }

        DB::transaction(function () use ($orderId, $transId, $payload) {
            // Tìm order bằng cách lookup trong Payment records
            // vì orderId từ MoMo không phải là ID trong database
            $payment = Payment::where('provider', 'momo')
                ->whereJsonContains('raw_payload->momo_order_id', $orderId)
                ->first();

            if (!$payment) {
                // Fallback: thử extract từ extraData nếu có
                $extraData = $payload['extraData'] ?? '';
                if ($extraData) {
                    try {
                        $decoded = json_decode(base64_decode($extraData), true);
                        $dbOrderId = $decoded['db_order_id'] ?? null;
                        if ($dbOrderId) {
                            $payment = Payment::where('order_id', $dbOrderId)
                                ->where('provider', 'momo')
                                ->first();
                        }
                    } catch (\Exception $e) {
                        Log::warning('MoMo IPN: Failed to decode extraData', ['error' => $e->getMessage()]);
                    }
                }
            }

            if (!$payment) {
                Log::error('MoMo IPN: Cannot find payment record', ['momo_order_id' => $orderId]);
                throw new \RuntimeException('Cannot find payment record for orderId: ' . $orderId);
            }

            $order = Order::lockForUpdate()->findOrFail($payment->order_id);

            if ($order->payment_status === 'paid') {
                Log::info('MoMo IPN: Order already paid', ['order_id' => $order->id]);
                return;
            }

            $order->update([
                'payment_status' => 'paid',
                'status' => 'delivered'
            ]);

            $payment->update([
                'status'           => 'succeeded',
                'transaction_id'   => $transId ?: ($payload['requestId'] ?? $payment->transaction_id),
                'gateway_event_id' => $payload['requestId'] ?? null,
                'raw_payload'      => array_merge($payment->raw_payload ?? [], ['ipn_payload' => $payload]),
            ]);
        });

        Log::info('MoMo IPN: Payment succeeded', ['orderId' => $orderId]);

        return [
            'status' => 'succeeded',
            'transaction_id' => $transId ?: null,
            'message' => 'Paid via MoMo'
        ];
    }

    private function signCreate(array $p): string
    {
        $raw = "accessKey=" . $this->accessKey
            . "&amount=" . $p['amount']
            . "&extraData=" . $p['extraData']
            . "&ipnUrl=" . $p['ipnUrl']
            . "&orderId=" . $p['orderId']
            . "&orderInfo=" . $p['orderInfo']
            . "&partnerCode=" . $p['partnerCode']
            . "&redirectUrl=" . $p['redirectUrl']
            . "&requestId=" . $p['requestId']
            . "&requestType=" . $p['requestType'];

        return hash_hmac('sha256', $raw, $this->secretKey);
    }

    private function verifyIpn(array $p): bool
    {
        $keys = [
            'accessKey',
            'amount',
            'extraData',
            'message',
            'orderId',
            'orderInfo',
            'orderType',
            'partnerCode',
            'payType',
            'requestId',
            'responseTime',
            'resultCode',
            'transId'
        ];

        $kv = [];
        foreach ($keys as $k) {
            $kv[] = $k . '=' . ($p[$k] ?? '');
        }

        $raw = implode('&', $kv);
        $sig = hash_hmac('sha256', $raw, $this->secretKey);

        return hash_equals($sig, (string) ($p['signature'] ?? ''));
    }
}
