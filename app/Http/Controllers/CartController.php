<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Services\CheckoutRecoveryService;
use Illuminate\Support\Str;
use App\Helpers\Helper;

class CartController extends Controller
{
    protected $product = null;
    protected CheckoutRecoveryService $checkoutRecovery;

    public function __construct(Product $product, CheckoutRecoveryService $checkoutRecovery)
    {
        $this->product = $product;
        $this->checkoutRecovery = $checkoutRecovery;
    }

    public function addToCart(Request $request, $slug)
    {
        $request->validate([
            'size' => 'required|string',
        ]);

        $product = Product::where('slug', $slug)->first();

        if (empty($product)) {
            session()->flash('error', 'Sản phẩm không hợp lệ');
            return back();
        }

        // Validate size
        $availableSizes = $product->size ? explode(',', $product->size) : [];
        $availableSizes = array_map('trim', $availableSizes);

        if (!in_array($request->size, $availableSizes)) {
            return back()->with('error', 'Vui lòng chọn size sản phẩm.');
        }

        $already_cart = Cart::where('user_id', auth()->user()->id)
            ->where('order_id', null)
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->first();

        if ($already_cart) {
            // Sản phẩm đã có trong giỏ với cùng size, tăng số lượng
            $already_cart->quantity = $already_cart->quantity + 1;
            $already_cart->amount = $product->price + $already_cart->amount;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Không đủ hàng!');
            }

            $already_cart->save();

            // ⭐ XÓA SẢN PHẨM NÀY KHỎI WISHLIST (nếu có)
            Wishlist::where('user_id', auth()->user()->id)
                ->where('product_id', $product->id)
                ->where('cart_id', null)
                ->delete();
        } else {
            // Thêm sản phẩm mới vào giỏ
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->size = $request->size;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = 1;
            $cart->amount = $cart->price * $cart->quantity;

            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                return back()->with('error', 'Không đủ hàng!');
            }

            $cart->save();

            Wishlist::where('user_id', auth()->user()->id)
                ->where('product_id', $product->id)
                ->where('cart_id', null)
                ->delete();
        }

        session()->flash('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công');
        return back();
    }

    public function singleAddToCart(Request $request)
    {
        // Support both product_id (AJAX) and slug (form)
        $product = null;

        // If product_id is provided (from AJAX)
        if ($request->has('product_id')) {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1',
                'size'       => 'required|string',
            ]);
            $product = Product::find($request->product_id);
        }
        // If slug is provided (from traditional form)
        elseif ($request->has('slug')) {
            $request->validate([
                'slug'      => 'required|exists:products,slug',
                'quant'     => 'required',
                'size'      => 'required|string',
            ]);
            $product = Product::where('slug', $request->slug)->first();
        } else {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng cung cấp product_id hoặc slug'], 400);
            }
            return back()->with('error', 'Sản phẩm không hợp lệ');
        }

        if (!$product) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
            }
            return back()->with('error', 'Sản phẩm không tồn tại');
        }

        // Get quantity from either source
        $quantity = $request->has('product_id')
            ? (int)$request->quantity
            : (int)$request->quant[1];

        // Validate size
        $availableSizes = $product->size ? explode(',', $product->size) : [];
        $availableSizes = array_map('trim', $availableSizes);

        // If product has no size, allow "One Size" as default
        if (empty($availableSizes)) {
            if ($request->size !== 'One Size') {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Sản phẩm này không có size. Vui lòng sử dụng size mặc định.'], 400);
                }
                return back()->with('error', 'Sản phẩm này không có size. Vui lòng sử dụng size mặc định.');
            }
        } else {
            // Product has sizes, validate the selected size
            if (!in_array($request->size, $availableSizes)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Size không hợp lệ. Vui lòng chọn size phù hợp.'], 400);
                }
                return back()->with('error', 'Size không hợp lệ. Vui lòng chọn size phù hợp.');
            }
        }

        // Check stock
        if ($product->stock < $quantity) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Hết hàng, Bạn có thể thêm sản phẩm khác.'], 400);
            }
            return back()->with('error', 'Hết hàng, Bạn có thể thêm sản phẩm khác.');
        }

        if ($quantity < 1 || !$product) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ'], 400);
            }
            return back()->with('error', 'Sản phẩm không hợp lệ');
        }

        // Check if same product with same size already in cart
        $already_cart = Cart::where('user_id', auth()->user()->id)
            ->where('order_id', null)
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->first();

        if ($already_cart) {
            $already_cart->quantity = $already_cart->quantity + $quantity;
            $already_cart->amount = ($product->price * $quantity) + $already_cart->amount;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Không đủ hàng!'], 400);
                }
                return back()->with('error', 'Không đủ hàng!');
            }

            $already_cart->save();
        } else {
            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->size = $request->size;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = $quantity;
            $cart->amount = ($product->price * $quantity);

            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Không đủ hàng!'], 400);
                }
                return back()->with('error', 'Không đủ hàng!');
            }

            $cart->save();
        }

        // Delete from wishlist after adding to cart
        Wishlist::where('user_id', auth()->user()->id)
            ->where('product_id', $product->id)
            ->where('cart_id', null)
            ->delete();

        $message = 'Sản phẩm đã được thêm vào giỏ hàng thành công.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message], 200);
        }

        return back()->with('success', $message);
    }

    public function cartDelete(Request $request)
    {
        $cart = Cart::find($request->id);
        if ($cart) {
            $cart->delete();
            session()->flash('success', 'Giỏ hàng đã được xóa thành công');
            return back();
        }
        session()->flash('error', 'Lỗi vui lòng thử lại');
        return back();
    }

    public function cartUpdate(Request $request)
    {
        // dd($request->all());
        if ($request->quant) {
            $error = array();
            $success = '';
            // return $request->quant;
            foreach ($request->quant as $k => $quant) {
                // return $k;
                $id = $request->qty_id[$k];
                // return $id;
                $cart = Cart::find($id);
                // return $cart;
                if ($quant > 0 && $cart) {
                    // return $quant;

                    if ($cart->product->stock < $quant) {
                        session()->flash('error', 'Hết hàng');
                        return back();
                    }
                    $cart->quantity = ($cart->product->stock > $quant) ? $quant  : $cart->product->stock;
                    // return $cart;

                    if ($cart->product->stock <= 0) continue;
                    $after_price = ($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = $after_price * $quant;
                    // return $cart->price;
                    $cart->save();
                    $success = 'Giỏ hàng đã được cập nhật thành công!';
                } else {
                    $error[] = 'Giỏ hàng không hợp lệ!';
                }
            }
            return back()->with($error)->with('success', $success);
        } else {
            return back()->with('Giỏ hàng không hợp lệ!');
        }
    }

    // public function addToCart(Request $request){
    //     // return $request->all();
    //     if(Auth::check()){
    //         $qty=$request->quantity;
    //         $this->product=$this->product->find($request->pro_id);
    //         if($this->product->stock < $qty){
    //             return response(['status'=>false,'msg'=>'Out of stock','data'=>null]);
    //         }
    //         if(!$this->product){
    //             return response(['status'=>false,'msg'=>'Product not found','data'=>null]);
    //         }
    //         // $session_id=session('cart')['session_id'];
    //         // if(empty($session_id)){
    //         //     $session_id=Str::random(30);
    //         //     // dd($session_id);
    //         //     session()->put('session_id',$session_id);
    //         // }
    //         $current_item=array(
    //             'user_id'=>auth()->user()->id,
    //             'id'=>$this->product->id,
    //             // 'session_id'=>$session_id,
    //             'title'=>$this->product->title,
    //             'summary'=>$this->product->summary,
    //             'link'=>route('product-detail',$this->product->slug),
    //             'price'=>$this->product->price,
    //             'photo'=>$this->product->photo,
    //         );

    //         $price=$this->product->price;
    //         if($this->product->discount){
    //             $price=($price-($price*$this->product->discount)/100);
    //         }
    //         $current_item['price']=$price;

    //         $cart=session('cart') ? session('cart') : null;

    //         if($cart){
    //             // if anyone alreay order products
    //             $index=null;
    //             foreach($cart as $key=>$value){
    //                 if($value['id']==$this->product->id){
    //                     $index=$key;
    //                 break;
    //                 }
    //             }
    //             if($index!==null){
    //                 $cart[$index]['quantity']=$qty;
    //                 $cart[$index]['amount']=ceil($qty*$price);
    //                 if($cart[$index]['quantity']<=0){
    //                     unset($cart[$index]);
    //                 }
    //             }
    //             else{
    //                 $current_item['quantity']=$qty;
    //                 $current_item['amount']=ceil($qty*$price);
    //                 $cart[]=$current_item;
    //             }
    //         }
    //         else{
    //             $current_item['quantity']=$qty;
    //             $current_item['amount']=ceil($qty*$price);
    //             $cart[]=$current_item;
    //         }

    //         session()->put('cart',$cart);
    //         return response(['status'=>true,'msg'=>'Cart successfully updated','data'=>$cart]);
    //     }
    //     else{
    //         return response(['status'=>false,'msg'=>'You need to login first','data'=>null]);
    //     }
    // }

    // public function removeCart(Request $request){
    //     $index=$request->index;
    //     // return $index;
    //     $cart=session('cart');
    //     unset($cart[$index]);
    //     session()->put('cart',$cart);
    //     return redirect()->back()->with('success','Successfully remove item');
    // }

    public function checkout(Request $request)
    {
        if (auth()->check() && Helper::cartCount() === 0) {
            $restoredOrder = $this->checkoutRecovery->tryRestoreForUser(auth()->user());
            if ($restoredOrder) {
                session()->flash('warning', 'Đã khôi phục giỏ hàng từ đơn ' . $restoredOrder . ' chưa thanh toán.');
            }
        }

        return view('frontend.pages.checkout');
    }
}
