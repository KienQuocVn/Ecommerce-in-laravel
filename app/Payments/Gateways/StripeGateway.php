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
        $lineItems = $order->items->map(function ($item) {
            $amountCents = (int) round($item->unit_price * 100);

            return [
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $item->product->name],
                    'unit_amount'  => $amountCents,
                ],
                'quantity' => (int) $item->quantity,
            ];
        })->values()->toArray();

        $session = $this->stripe->checkout->sessions->create([
            'mode'                  => 'payment',
            'success_url'           => url('/payments/stripe/return?status=success&order_id=' . $order->id . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'            => url('/payments/stripe/return?status=cancel&order_id=' . $order->id),
            'payment_method_types'  => ['card'],
            'line_items'            => $lineItems,
            'metadata'              => ['order_id' => (string) $order->id],
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

            if ($order->status === 'paid') {
                return;
            }

            foreach ($order->items as $item) {
                Product::whereKey($item->product_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);
            }

            $order->update(['status' => 'paid']);

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