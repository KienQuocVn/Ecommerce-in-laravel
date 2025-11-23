<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentReturnController extends Controller
{
    public function handle(Request $req, string $provider)
    {
        try {
            $gateway = PaymentService::make($provider);
            $result = $gateway->handleReturn($req->all());

            // Lấy orderId từ nhiều nguồn
            $orderId = $req->query('order_id')
                ?? $req->input('order_id')
                ?? $req->query('orderId')
                ?? $req->input('orderId')
                ?? $req->input('vnp_TxnRef')
                ?? session()->get('order_id');

            $order = null;

            if ($orderId && $provider === 'momo') {
                // Với MoMo, orderId có thể là momo_order_id, cần tìm qua Payment record
                $payment = Payment::where('provider', 'momo')
                    ->where(function ($q) use ($orderId) {
                        $q->where('order_id', $orderId)
                            ->orWhereJsonContains('raw_payload->momo_order_id', $orderId);
                    })
                    ->first();

                if ($payment) {
                    $order = Order::find($payment->order_id);
                }
            } elseif ($orderId) {
                // Các provider khác dùng trực tiếp order ID
                $order = Order::find($orderId);
            }

            if ($order && $order->payment) {
                // Cập nhật payment record
                $order->payment->update([
                    'status' => $result['status'],
                    'transaction_id' => $result['transaction_id'] ?? $order->payment->transaction_id,
                    'raw_payload' => $req->all()
                ]);

                // Nếu thanh toán thành công, cập nhật order và trừ stock
                if ($result['status'] === 'succeeded' && $order->payment_status !== 'paid') {
                    DB::transaction(function () use ($order) {
                        // Trừ stock
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
                        $order->ensureDeliveryRecord($order->delivery_charge ?? 0);
                    });
                }
            }

            // Xóa session cart và order_id nếu thanh toán thành công
            if ($result['status'] === 'succeeded') {
                session()->forget('cart');
                session()->forget('coupon');
                session()->forget('order_id');
            }

            return view('frontend.pages.payment-result', [
                'provider' => $provider,
                'status' => $result['status'],
                'message' => $result['message'] ?? '',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment return error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'request' => $req->all()
            ]);

            return view('frontend.pages.payment-result', [
                'provider' => $provider,
                'status' => 'failed',
                'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage(),
                'order' => null,
            ]);
        }
    }
}
