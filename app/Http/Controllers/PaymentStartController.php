<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentStartController extends Controller
{
    public function start(Request $request, string $provider)
    {
        $orderId = $request->session()->get('order_id') ?? $request->input('order_id');
        
        if (!$orderId) {
            session()->flash('error', 'Không tìm thấy đơn hàng');
            return redirect()->route('home');
        }

        $order = Order::find($orderId);
        
        if (!$order) {
            session()->flash('error', 'Đơn hàng không tồn tại');
            return redirect()->route('home');
        }

        try {
            $gateway = PaymentService::make($provider);
            $paymentUrl = $gateway->createPayment($order);
            
            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi khởi tạo thanh toán: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }
}