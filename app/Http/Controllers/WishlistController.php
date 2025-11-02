<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    protected $product = null;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function wishlist(Request $request)
    {
        if (empty($request->slug)) {
            session()->flash('error', 'Sản phẩm không hợp lệ');
            return back();
        }
        $product = Product::where('slug', $request->slug)->first();
        if (empty($product)) {
            session()->flash('error', 'Sản phẩm không hợp lệ');
            return back();
        }

        $already_wishlist = Wishlist::where('user_id', auth()->user()->id)->where('cart_id', null)->where('product_id', $product->id)->first();
        if ($already_wishlist) {
            session()->flash('error', 'Bạn đã thêm vào danh sách yêu thích');
            return back();
        } else {

            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product->id;
            $wishlist->price = ($product->price - ($product->price * $product->discount) / 100);
            $wishlist->quantity = 1;
            $wishlist->amount = $wishlist->price * $wishlist->quantity;
            if ($wishlist->product->stock < $wishlist->quantity || $wishlist->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');
            $wishlist->save();
        }
        session()->flash('success', 'Sản phẩm đã được thêm vào danh sách yêu thích thành công');
        return back();
    }

    public function wishlistDelete(Request $request)
    {
        $wishlist = Wishlist::find($request->id);
        if ($wishlist) {
            $wishlist->delete();
            session()->flash('success', 'Danh sách yêu thích đã được xóa thành công');
            return back();
        }
        session()->flash('error', 'Lỗi vui lòng thử lại');
        return back();
    }
}
