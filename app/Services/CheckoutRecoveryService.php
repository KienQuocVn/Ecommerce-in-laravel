<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutRecoveryService
{
    /**
     * Restore cart items if the last online payment was abandoned.
     */
    public function restoreLatestPendingOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            Cart::where('order_id', $order->id)->update([
                'order_id' => null,
                'status' => 'new',
            ]);

            if ($order->delivery) {
                $order->delivery->delete();
            }

            if ($order->payment) {
                $order->payment->delete();
            }

            $order->delete();
        });
    }

    public function tryRestoreForUser(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $order = Order::with(['payment', 'delivery'])
            ->where('user_id', $user->id)
            ->where('status', 'new')
            ->where('payment_status', 'unpaid')
            ->whereIn('payment_method', ['paypal', 'momo', 'vnpay', 'stripe'])
            ->latest()
            ->first();

        if (!$order) {
            return null;
        }

        if (optional($order->payment)->status === 'succeeded') {
            return null;
        }

        $this->restoreLatestPendingOrder($order);
        session()->forget('order_id');

        return $order->order_number;
    }
}
