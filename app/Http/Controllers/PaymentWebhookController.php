<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Throwable;

class PaymentWebhookController extends Controller
{
    public function stripe(Request $request)
    {
        $secret   = config('services.stripe.webhook_secret');
        $payload  = $request->getContent();
        $sig      = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (Throwable $e) {
            Log::warning('wh.invalid', ['e' => $e->getMessage()]);
            return response('Invalid', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $s = $event->data->object;
            $orderId = $s->metadata->order_id ?? null;
            $pi      = $s->payment_intent ?? null;
            $this->complete($orderId, $pi, $event->id);
            return response('OK', 200);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $piObj   = $event->data->object;
            $orderId = $piObj->metadata->order_id ?? null;
            if (!$orderId && $piObj->id) {
                try {
                    $sessions = app(StripeClient::class)->checkout->sessions->all([
                        'payment_intent' => $piObj->id,
                        'limit' => 1,
                    ]);
                    if (!empty($sessions->data[0]->metadata->order_id)) {
                        $orderId = $sessions->data[0]->metadata->order_id;
                    }
                } catch (Throwable $e) {
                    Log::warning('wh.lookup.session.failed', ['e' => $e->getMessage()]);
                }
            }

            $this->complete($orderId, $piObj->id ?? null, $event->id);
            return response('OK', 200);
        }
        return response('Ignored', 200);
    }

    private function complete(?string $orderId, ?string $pi, string $eventId): void
    {
        if (!$orderId) {
            Log::warning('wh.no_order_id', compact('eventId'));
            return;
        }
        DB::transaction(function () use ($orderId, $pi, $eventId) {
            $order = Order::with('items')->lockForUpdate()->findOrFail($orderId);
            if ($order->status === 'paid') {
                Log::info('wh.order_already_paid', ['order_id' => $order->id, 'event_id' => $eventId]);
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
                    throw new RuntimeException("Không đủ tồn kho cho sản phẩm ID {$item->product_id}");
                }
            }
            Payment::where('order_id', $order->id)
                ->where('provider', 'stripe')
                ->update([
                    'status'           => 'succeeded',
                    'transaction_id'   => $pi,
                    'raw_payload'      => null,
                    'gateway_event_id' => $eventId,
                ]);
            $order->update([
                'payment_status' => 'paid',
                'status' => 'delivered' // hoặc 'progress'
            ]);

            Log::info('wh.completed', ['order_id' => $order->id, 'event_id' => $eventId]);
        });
    }
    public function momo(Request $req)
    {
        return $this->generic('momo', $req);
    }
    
    public function vnpay(Request $req)
    {
        return $this->generic('vnpay', $req);
    }

    private function generic(string $provider, Request $req)
    {
        $gateway = PaymentService::make($provider);
        $result = $gateway->handleWebhook($req->all());
        $orderId = $req->input('orderId') ?? $req->input('vnp_TxnRef');
        if ($orderId && $order = Order::find($orderId)) {
            $payment = $order->payment;
            $payment->update([
                'status' => $result['status'],
                'transaction_id' =>
                $result['transaction_id'] ?? $payment->transaction_id,
                'raw_payload' => $req->all(),
            ]);
            $order->update(['status' =>
            $result['status'] === 'succeeded' ? 'paid' : 'failed']);
        }
        return response('OK');
    }

    public function paypalWebhook(Request $req)
    {
        $gateway = PaymentService::make('paypal');
        $data = $gateway->handleWebhook(
            $req->all(),
            $req->header('Paypal-Transmission-Sig')
        );
        return response()->json($data);
    }
}
