<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VnpayGateway implements PaymentGateway
{
    protected string $tmnCode;
    protected string $secret;
    protected string $paymentUrl;
    protected string $returnUrl;
    protected int $convertRate;

    public function __construct()
    {
        $cfg = config('services.vnpay');
        $this->tmnCode    = (string) $cfg['tmn_code'];
        $this->secret     = (string) $cfg['hash_secret'];
        $this->paymentUrl = (string) $cfg['payment_url'];
        $this->returnUrl  = (string) $cfg['return_url'];
        $this->convertRate = (int)    ($cfg['convert_rate'] ?? 27000);
    }

    public function createPayment(Order $order): string
    {
        $amountVnd = (float) $order->total_amount;
        $vnpAmount = (int) round($amountVnd * 100);

        $params = [
            'vnp_Version'   => '2.1.0',
            'vnp_Command'   => 'pay',
            'vnp_TmnCode'   => $this->tmnCode,
            'vnp_Amount'    => $vnpAmount,
            'vnp_CurrCode'  => 'VND',
            'vnp_TxnRef'    => (string) $order->id,
            'vnp_OrderInfo' => 'Thanh toan don ' . $order->id,
            'vnp_OrderType' => 'other',
            'vnp_Locale'    => 'vn',
            'vnp_ReturnUrl' => $this->returnUrl,
            // 'vnp_IpAddr'    => request()?->ip() ?: '127.0.0.1',
            'vnp_IpAddr' => request()?->ip() ?? '0.0.0.0',
            'vnp_CreateDate' => now()->format('YmdHis'),

        ];

        $url = $this->signedUrl($params);

        Payment::where('order_id', $order->id)
            ->where('provider', 'vnpay')
            ->update([
                'status'         => 'pending',
                'transaction_id' => $params['vnp_TxnRef'],
            ]);

        return $url;
    }

    public function handleReturn(array $payload): array
    {
        if (!$this->verifySignature($payload)) {
            return ['status' => 'failed', 'transaction_id' => null, 'message' => 'Invalid signature'];
        }

        $code = $payload['vnp_ResponseCode'] ?? null;
        $transNo = (string) ($payload['vnp_TransactionNo'] ?? '');
        if ($code === '00') {
            return ['status' => 'succeeded', 'transaction_id' => $transNo ?: null, 'message' => 'Paid via VNPAY'];
            // return ['status' => 'processing', 'transaction_id' => $payload['vnp_TransactionNo'] ?? null, 'message' => 'Waiting IPN'];
        }

        if ($code === '24') {
            return ['status' => 'canceled', 'transaction_id' => null, 'message' => 'User canceled'];
        }

        return ['status' => 'failed', 'transaction_id' => null, 'message' => 'VNPAY code: ' . $code];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        if (!$this->verifySignature($payload)) {
            return ['status' => 'failed', 'transaction_id' => null, 'message' => 'Invalid signature'];
        }

        $orderId = (string) ($payload['vnp_TxnRef'] ?? '');
        $resp    = (string) ($payload['vnp_ResponseCode'] ?? '');
        $transNo = (string) ($payload['vnp_TransactionNo'] ?? '');
        $payAmt  = (int)    ($payload['vnp_Amount'] ?? 0);

        if ($resp !== '00') {
            return ['status' => 'failed', 'transaction_id' => $transNo ?: null, 'message' => 'Payment failed: ' . $resp];
        }



        DB::transaction(function () use ($orderId, $transNo, $payload) {
            $order = Order::with('items')->lockForUpdate()->findOrFail($orderId);
            if ($order->payment_status === 'paid') {
                return;
            }

            foreach ($order->items as $item) {
                $qty = (int) $item->quantity;
                $affected = DB::table('products')
                    ->where('id', $item->product_id)
                    ->where('stock', '>=', $qty)
                    ->update([
                        'stock' => DB::raw('stock - ' . $qty),
                    ]);
                if ($affected === 0) {
                    throw new \RuntimeException("Không đủ tồn kho cho sản phẩm ID {$item->product_id}");
                }
            }

            $order->update([
                'payment_status' => 'paid',
                'status' => 'process'
            ]);

            Payment::where('order_id', $order->id)
                ->where('provider', 'vnpay')
                ->update([
                    'status'           => 'succeeded',
                    'transaction_id'   => $transNo ?: $payload['vnp_TxnRef'] ?? null,
                    'gateway_event_id' => $payload['vnp_TransactionNo'] ?? null,
                    'raw_payload'      => $payload,
                ]);
        });

        return ['status' => 'succeeded', 'transaction_id' => $transNo ?: null, 'message' => 'Paid via VNPAY'];
    }


    private function secret(): string
    {
        return trim((string) config('services.vnpay.hash_secret'));
    }

    private function signedUrl(array $params): string
    {
        ksort($params);
        $hashData = '';
        foreach ($params as $k => $v) {
            if ($hashData !== '') $hashData .= '&';
            $hashData .= urlencode($k) . '=' . urlencode($v);
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->secret());
        Log::debug('vnp.sign.create', ['hashData' => $hashData, 'hash' => $secureHash]);
        $params['vnp_SecureHash']     = $secureHash;
        $params['vnp_SecureHashType'] = 'HMACSHA512';

        return $this->paymentUrl . '?' . http_build_query($params);
    }

    private function verifySignature(array $payload): bool
    {
        $input = [];
        foreach ($payload as $k => $v) {
            if (strpos($k, 'vnp_') === 0 && $k !== 'vnp_SecureHash' && $k !== 'vnp_SecureHashType') {
                $input[$k] = $v;
            }
        }
        ksort($input);

        $data = '';
        foreach ($input as $k => $v) {
            if ($data !== '') $data .= '&';
            $data .= urlencode($k) . '=' . urlencode($v);
        }

        $calc = hash_hmac('sha512', $data, $this->secret());
        $recv = (string)($payload['vnp_SecureHash'] ?? '');

        Log::debug('vnp.sign.verify', ['data' => $data, 'calc' => $calc, 'recv' => $recv]);

        return hash_equals($calc, $recv);
    }
}
