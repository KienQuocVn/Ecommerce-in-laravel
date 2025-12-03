<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Cart;

class CouponController extends Controller
{

    public function index()
    {
        $coupon = Coupon::orderBy('id', 'DESC')->paginate('10');
        return view('backend.coupon.index')->with('coupons', $coupon);
    }


    public function create()
    {
        return view('backend.coupon.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'string|required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();
        $status = Coupon::create($data);
        if ($status) {
            session()->flash('success', 'Đã thêm phiếu giảm giá thành công');
        } else {
            session()->flash('error', 'Vui lòng thử lại!');
        }
        return redirect()->route('coupon.index');
    }

    public function show($id) {}

    public function edit($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            return view('backend.coupon.edit')->with('coupon', $coupon);
        } else {
            return view('backend.coupon.index')->with('error', 'Không tìm thấy phiếu giảm giá');
        }
    }


    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        $this->validate($request, [
            'code' => 'string|required',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive'
        ]);
        $data = $request->all();

        $status = $coupon->fill($data)->save();
        if ($status) {
            session()->flash('success', 'Phiếu giảm giá đã được cập nhật thành công');
        } else {
            session()->flash('error', 'Vui lòng thử lại!');
        }
        return redirect()->route('coupon.index');
    }


    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            session()->flash('error', 'Phiếu giảm giá không tìm thấy');
            return redirect()->back();
        }

        // Kiểm tra xem coupon có được dùng trong orders không
        $usedCount = \App\Models\Order::where('coupon_id', $id)->count();
        if ($usedCount > 0) {
            session()->flash('warning', 'Phiếu giảm giá này đã được sử dụng trong ' . $usedCount . ' đơn hàng. Hãy cẩn thận khi xóa.');
        }

        $status = $coupon->delete();
        if ($status) {
            session()->flash('success', 'Phiếu giảm giá đã được xóa thành công');
        } else {
            session()->flash('error', 'Lỗi, vui lòng thử lại');
        }
        return redirect()->route('coupon.index');
    }

    public function couponStore(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon) {
            session()->flash('error', 'Mã giảm giá không hợp lệ, vui lòng thử lại');
            return back();
        }
        if ($coupon) {
            $total_price = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->sum('price');
            session()->put('coupon', [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'value' => $coupon->discount($total_price)
            ]);
            session()->flash('success', 'Phiếu giảm giá đã được áp dụng thành công');
            return redirect()->back();
        }
    }
}
