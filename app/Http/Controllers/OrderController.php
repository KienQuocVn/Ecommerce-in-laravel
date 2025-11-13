<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\User;
use PDF;
use Illuminate\Support\Facades\Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::orderBy('id', 'DESC')->paginate(10);
        return view('backend.order.index')->with('orders', $orders);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'address2' => 'string|nullable',
            'coupon' => 'nullable|numeric',
            'phone' => 'numeric|required',
            'post_code' => 'string|nullable',
            'email' => 'string|required',
            'payment_method' => 'required|in:cod,paypal,stripe,momo,vnpay'
        ]);

        if (empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())) {
            session()->flash('error', 'Giỏ hàng trống!');
            return back();
        }

        $order = new Order();
        $order_data = $request->all();
        $order_data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
        $order_data['user_id'] = $request->user()->id;
        $order_data['shipping_id'] = $request->shipping;

        $shipping = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity'] = Helper::cartCount();

        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }

        // Tính tổng tiền
        if ($request->shipping) {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0] - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0];
            }
        } else {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice();
            }
        }

        $order_data['status'] = "new";
        $method = (string) $request->input('payment_method', 'cod');

        if (in_array($method, ['paypal', 'stripe', 'momo', 'vnpay'])) {
            $order_data['payment_method'] = $method;
            $order_data['payment_status'] = 'unpaid';
        } else {
            $order_data['payment_method'] = 'cod';
            $order_data['payment_status'] = 'unpaid';
        }

        $order->fill($order_data);
        $status = $order->save();

        if ($status) {
            // **TẠO PAYMENT RECORD**
            Payment::create([
                'order_id' => $order->id,
                'provider' => $order->payment_method,
                'status' => 'pending',
                'amount' => $order->total_amount,
                'currency' => 'VND',
            ]);

            // Gửi thông báo cho admin
            $users = User::where('role', 'admin')->first();
            if ($users) {
                $details = [
                    'title' => 'New order created',
                    'actionURL' => route('order.show', $order->id),
                    'fas' => 'fa-file-alt'
                ];
                Notification::send($users, new StatusNotification($details));
            }

            // Link cart items với order
            Cart::where('user_id', auth()->user()->id)
                ->where('order_id', null)
                ->update(['order_id' => $order->id]);

            // Nếu thanh toán online, chuyển hướng đến gateway
            if (in_array($order->payment_method, ['paypal', 'stripe', 'momo', 'vnpay'])) {
                // Lưu order_id vào session
                session()->put('order_id', $order->id);
                return redirect()->route('payments.start', ['provider' => $order->payment_method]);
            } else {
                // COD: xóa session và về trang chủ
                session()->forget('cart');
                session()->forget('coupon');
                session()->flash('success', 'Đơn hàng của bạn đã được đặt thành công');
                return redirect()->route('home');
            }
        }

        session()->flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        return back();
    }


    public function show($id)
    {
        $order = Order::find($id);
        return view('backend.order.show')->with('order', $order);
    }

    public function edit($id)
    {
        $order = Order::find($id);
        return view('backend.order.edit')->with('order', $order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $this->validate($request, [
            'status' => 'required|in:new,process,delivered,cancel'
        ]);
        $data = $request->all();
        if ($request->status == 'delivered') {
            foreach ($order->cart as $cart) {
                $product = $cart->product;
                // return $product;
                $product->stock -= $cart->quantity;
                $product->save();
            }
        }
        $status = $order->fill($data)->save();
        if ($status) {
            session()->flash('success', 'Đã cập nhật đơn hàng thành công');
        } else {
            session()->flash('error', 'Lỗi khi cập nhật đơn hàng');
        }
        return redirect()->route('order.index');
    }


    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            $status = $order->delete();
            if ($status) {
                session()->flash('success', 'Đơn hàng đã xóa thành công');
            } else {
                session()->flash('error', 'Không thể xóa đơn hàng');
            }
            return redirect()->route('order.index');
        } else {
            session()->flash('error', 'Không tìm thấy đơn hàng');
            return redirect()->back();
        }
    }

    public function orderTrack()
    {
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request)
    {
        $order = Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();
        if ($order) {
            if ($order->status == "new") {
                session()->flash('success', 'Đơn hàng của bạn đã được đặt. Vui lòng chờ.');
                return redirect()->route('home');
            } elseif ($order->status == "process") {
                session()->flash('success', 'Đơn hàng của bạn đang được xử lý, vui lòng đợi.');
                return redirect()->route('home');
            } elseif ($order->status == "delivered") {
                session()->flash('success', 'Đơn hàng của bạn đã được giao thành công.');
                return redirect()->route('home');
            } else {
                session()->flash('error', 'Đơn hàng của bạn đã bị hủy. Vui lòng thử lại');
                return redirect()->route('home');
            }
        } else {
            session()->flash('error', 'Số đơn hàng không hợp lệ, vui lòng thử lại');
            return back();
        }
    }

    public function incomeChart(Request $request)
    {
        $year = \Carbon\Carbon::now()->year;
        $items = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'delivered')->get()
            ->groupBy(function ($d) {
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                $m = intval($month);
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = (!empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }
}
