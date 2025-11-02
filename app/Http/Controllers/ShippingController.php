<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\Coupon;

class ShippingController extends Controller
{

    public function index()
    {
        $shipping=Shipping::orderBy('id','DESC')->paginate(10);
        return view('backend.shipping.index')->with('shippings',$shipping);
    }

    public function create()
    {
        return view('backend.shipping.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'type'=>'string|required',
            'price'=>'nullable|numeric',
            'status'=>'required|in:active,inactive'
        ]);
        $data=$request->all();
        $status=Shipping::create($data);
        if($status){
            session()->flash('success','Đã tạo thành công vận chuyển');
        }
        else{
            session()->flash('error','Lỗi, vui lòng thử lại');
        }
        return redirect()->route('shipping.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $shipping=Shipping::find($id);
        if(!$shipping){
            session()->flash('error','Không tìm thấy vận chuyển');
        }
        return view('backend.shipping.edit')->with('shipping',$shipping);
    }


    public function update(Request $request, $id)
    {
        $shipping=Shipping::find($id);
        $this->validate($request,[
            'type'=>'string|required',
            'price'=>'nullable|numeric',
            'status'=>'required|in:active,inactive'
        ]);
        $data=$request->all();
        $status=$shipping->fill($data)->save();
        if($status){
            session()->flash('success','Đã cập nhật vận chuyển thành công');
        }
        else{
            session()->flash('error','Lỗi, vui lòng thử lại');
        }
        return redirect()->route('shipping.index');
    }

    public function destroy($id)
    {
        $shipping=Shipping::find($id);
        if($shipping){
            $status=$shipping->delete();
            if($status){
                session()->flash('success','Đã xóa thành công vận chuyển');
            }
            else{
                session()->flash('error','Lỗi, vui lòng thử lại');
            }
            return redirect()->route('shipping.index');
        }
        else{
            session()->flash('error','Không tìm thấy vận chuyển');
            return redirect()->back();
        }
    }
}
