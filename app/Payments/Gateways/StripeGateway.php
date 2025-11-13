<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class StripeGateway implements PaymentGateway
{
    public function __construct(private StripeClient $stripe) {}


    public function createPayment(Order $order): string
    {
        $order->load(['items.product']); // đảm bảo có dữ liệu

        $lineItems = $order->items->map(function ($item) {
            $product = $item->product;
            $productName = $product->title ?? 'Sản phẩm không xác định';
            $unitPrice = (float) ($item->price ?? 0);

            // Chuyển đổi VNĐ sang USD (tỷ giá từ config hoặc 24000 VND)
            $exchangeRate = (float) config('services.stripe.vnd_to_usd_rate', 24000);
            $priceInUsd = $unitPrice / $exchangeRate;
            $amountCents = (int) round($priceInUsd * 100);

            if ($amountCents <= 0) {
                throw new \RuntimeException("Sản phẩm {$productName} có giá không hợp lệ ({$unitPrice} VND)");
            }

            return [
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $productName],
                    'unit_amount'  => $amountCents,
                ],
                'quantity' => (int) $item->quantity,
            ];
        })->values()->toArray();

        $session = $this->stripe->checkout->sessions->create([
            'mode'                 => 'payment',
            'success_url'          => url('/payments/stripe/return?status=success&order_id=' . $order->id . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'           => url('/payments/stripe/return?status=cancel&order_id=' . $order->id),
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'metadata'             => ['order_id' => (string) $order->id],
        ]);

        // Cập nhật Payment DB
        Payment::where('order_id', $order->id)
            ->where('provider', 'stripe')
            ->update([
                'amount'   => $order->total_amount,
                'currency' => 'VND',
                'status'   => 'pending',
            ]);

        return $session->url;
    }


    public function handleReturn(array $payload): array
    {
        $status = $payload['status'] ?? 'cancel';
        if ($status === 'success') return ['status' => 'succeeded', 'transaction_id' => null, 'message' => 'Checkout completed'];
        if ($status === 'cancel')  return ['status' => 'canceled',  'transaction_id' => null, 'message' => 'User canceled'];
        return ['status' => 'failed', 'transaction_id' => null, 'message' => 'Unknown status'];
    }

    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        $type = $payload['type'] ?? '';
        if ($type !== 'checkout.session.completed') {
            return ['status' => 'ignored', 'transaction_id' => null, 'message' => 'Unhandled'];
        }

        $session = $payload['data']['object'] ?? [];
        $sessionId = $session['id'] ?? null;
        $orderId   = $session['metadata']['order_id'] ?? null;
        $pi        = $session['payment_intent'] ?? null;
        $eventId   = $payload['id'] ?? null;

        if (!$sessionId || !$orderId) {
            return ['status' => 'failed', 'transaction_id' => null, 'message' => 'Missing session/order metadata'];
        }

        DB::transaction(function () use ($orderId, $pi, $eventId) {
            $order = Order::with(['items'])->lockForUpdate()->findOrFail($orderId);

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
                ->where('provider', 'stripe')
                ->update([
                    'status'          => 'succeeded',
                    'transaction_id'  => $pi,
                    'gateway_event_id' => $eventId,
                ]);
        });

        return ['status' => 'succeeded', 'transaction_id' => $pi, 'message' => 'Stock decremented & order paid'];
    }
}
