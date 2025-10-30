<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaypalGateway implements PaymentGateway
{
    private string $base;
    private string $clientId;
    private string $clientSecret;
    private float $vndToUsdRate = 24000; // Tỷ giá VND sang USD (có thể config trong .env)

    public function __construct()
    {
        $mode = config('services.paypal.mode', 'sandbox');
        $this->base = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->clientId     = (string) config('services.paypal.client_id');
        $this->clientSecret = (string) config('services.paypal.client_secret');
        
        // Lấy tỷ giá từ config nếu có
        $this->vndToUsdRate = (float) config('services.paypal.vnd_to_usd_rate', 24000);
    }

    public function createPayment(Order $order): string
    {
        // **SỬA LỖI: Dùng total_amount thay vì amount**
        $amountVND = (float) $order->total_amount;
        
        // **CHUYỂN ĐỔI VND SANG USD**
        $amountUSD = $amountVND / $this->vndToUsdRate;
        $amountUSD = number_format($amountUSD, 2, '.', '');

        $token  = $this->getAccessToken();

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $order->id,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $amountUSD,
                ],
                'description' => 'Order #' . $order->order_number,
            ]],
            'application_context' => [
                'brand_name'  => config('app.name', 'MyShop'),
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url'  => route('payments.return', ['provider' => 'paypal']) . '?status=success&order_id=' . $order->id,
                'cancel_url'  => route('payments.return', ['provider' => 'paypal']) . '?status=cancel&order_id=' . $order->id,
            ],
        ];

        $res = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post($this->base . '/v2/checkout/orders', $payload);

        if (!$res->successful()) {
            throw new RuntimeException('PayPal create order failed: ' . $res->body());
        }

        $data = $res->json();
        $approve = collect($data['links'] ?? [])->first(fn($l) => ($l['rel'] ?? '') === 'approve');

        if (!$approve || empty($approve['href'])) {
            throw new RuntimeException('PayPal approve URL not found.');
        }

        // Cập nhật transaction_id
        Payment::where('order_id', $order->id)
            ->where('provider', 'paypal')
            ->update([
                'transaction_id' => $data['id'] ?? null,
                'currency' => 'USD', // Lưu currency là USD
                'amount' => $amountUSD, // Lưu số tiền USD
            ]);

        return $approve['href'];
    }

    public function handleReturn(array $payload): array
    {
        $status  = $payload['status'] ?? 'cancel';
        $orderId = $payload['order_id'] ?? null;
        $token   = $payload['token'] ?? null;

        if ($status !== 'success' || !$orderId || !$token) {
            return [
                'status' => $status === 'cancel' ? 'canceled' : 'failed',
                'transaction_id' => null,
                'message' => $status === 'cancel' ? 'Người dùng đã hủy thanh toán' : 'Thiếu thông tin đơn hàng',
            ];
        }

        $access = $this->getAccessToken();

        $res = Http::withToken($access)
            ->acceptJson()
            ->withBody('{}', 'application/json')
            ->send('POST', $this->base . "/v2/checkout/orders/{$token}/capture");

        if (!$res->successful()) {
            return [
                'status' => 'failed',
                'transaction_id' => null,
                'message' => 'Capture failed: ' . $res->body(),
            ];
        }

        $pp = $res->json();
        if (($pp['status'] ?? '') !== 'COMPLETED') {
            return [
                'status' => 'failed',
                'transaction_id' => $pp['id'] ?? null,
                'message' => 'Capture not completed: ' . ($pp['status'] ?? 'unknown'),
            ];
        }

        $captureId = $pp['purchase_units'][0]['payments']['captures'][0]['id'] ?? ($pp['id'] ?? null);

        // Xử lý trong transaction
        DB::transaction(function () use ($orderId, $captureId, $pp) {
            $order = Order::with('items')->lockForUpdate()->findOrFail($orderId);
            
            // Kiểm tra đơn hàng đã thanh toán chưa
            if ($order->payment_status === 'paid' || $order->status === 'paid') {
                return;
            }

            // Giảm tồn kho
            foreach ($order->items as $item) {
                $affected = DB::table('products')
                    ->where('id', $item->product_id)
                    ->where('stock', '>=', (int) $item->quantity)
                    ->decrement('stock', (int) $item->quantity);
                
                if ($affected === 0) {
                    throw new RuntimeException("Không đủ tồn kho cho sản phẩm ID {$item->product_id}");
                }
            }

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 'process',
                'payment_status' => 'paid'
            ]);

            // Cập nhật payment
            Payment::where('order_id', $order->id)
                ->where('provider', 'paypal')
                ->update([
                    'status'           => 'succeeded',
                    'transaction_id'   => $captureId,
                    'gateway_event_id' => $pp['id'] ?? null,
                    'raw_payload'      => $pp,
                ]);
        });

        return [
            'status' => 'succeeded',
            'transaction_id' => $captureId,
            'message' => 'Thanh toán thành công',
        ];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        // PayPal webhook không bắt buộc, vì đã xử lý trong handleReturn
        return [
            'status' => 'ignored', 
            'transaction_id' => null, 
            'message' => 'Webhook not used'
        ];
    }

    private function getAccessToken(): string
    {
        $res = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->acceptJson()
            ->asForm()
            ->post($this->base . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        if (!$res->successful()) {
            throw new RuntimeException('PayPal OAuth failed: ' . $res->body());
        }
        
        return (string) ($res->json()['access_token'] ?? '');
    }
}