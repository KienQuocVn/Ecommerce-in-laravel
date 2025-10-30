<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentReturnController extends Controller
{
    public function handle(Request $req, string $provider)
{
    try {
        $gateway = PaymentService::make($provider);
        $result = $gateway->handleReturn($req->all());

        // Lấy orderId từ nhiều nguồn
        $orderId = $req->query('orderId')
            ?? $req->input('orderId')
            ?? $req->input('order_id')
            ?? $req->input('vnp_TxnRef');

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
            
            if ($order && $order->payment) {
                // Cập nhật payment record
                $order->payment->update([
                    'status' => $result['status'],
                    'transaction_id' => $result['transaction_id'] ?? $order->payment->transaction_id,
                    'raw_payload' => $req->all()
                ]);

                // Nếu thanh toán thành công, cập nhật order
                if ($result['status'] === 'succeeded') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'delivered' 
                    ]);
                }
            }
        }

        // Xóa session cart nếu thanh toán thành công
        if ($result['status'] === 'succeeded') {
            session()->forget('cart');
            session()->forget('coupon');
        }

        return view('frontend.pages.payment-result', [
            'provider' => $provider,
            'status' => $result['status'],
            'message' => $result['message'] ?? '',
            'order' => $order,
        ]);
    } catch (\Exception $e) {
        

        return view('frontend.pages.payment-result', [
            'provider' => $provider,
            'status' => 'failed',
            'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage(),
            'order' => null,
        ]);
    }
}
}