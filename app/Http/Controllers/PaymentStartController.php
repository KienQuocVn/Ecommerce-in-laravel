<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentStartController extends Controller
{
    public function start(Request $request, string $provider)
    {
        $orderId = $request->session()->get('order_id')
            ?? $request->input('order_id')
            ?? $request->query('order_id');

        if (!$orderId) {
            session()->flash('error', 'Không tìm thấy đơn hàng');
            return redirect()->route('home');
        }

        $order = Order::find($orderId);

        if (!$order) {
            session()->flash('error', 'Đơn hàng không tồn tại');
            return redirect()->route('home');
        }

        // Lưu order_id vào session để dùng khi return
        session()->put('order_id', $order->id);

        try {
            $gateway = PaymentService::make($provider);
            $paymentUrl = $gateway->createPayment($order);

            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            Log::error('Payment start error', [
                'provider' => $provider,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Lỗi khởi tạo thanh toán: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }
}
