<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\ProductReview;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusNotification;
use App\Models\User;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::getAllProduct();
        return view('backend.product.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'size' => 'nullable',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $slug = generateUniqueSlug($request->title, Product::class);
        $validatedData['slug'] = $slug;
        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = implode(',', $request->input('size'));
        } else {
            $validatedData['size'] = '';
        }

        $product = Product::create($validatedData);

        $message = $product
            ? 'Sản phẩm đã được thêm thành công'
            : 'Vui lòng thử lại!!';

        return redirect()->route('product.index')->with(
            $product ? 'success' : 'error',
            $message
        );
    }

    public function show($id)
    {
        // Implement if needed
    }

    public function edit($id)
    {
        $brands = Brand::get();
        $product = Product::findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        $items = Product::where('id', $id)->get();

        return view('backend.product.edit', compact('product', 'brands', 'categories', 'items'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'size' => 'nullable',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        // Tạo slug mới nếu title thay đổi
        if ($product->title !== $request->title) {
            $validatedData['slug'] = generateUniqueSlug($request->title, Product::class);
        }

        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = implode(',', $request->input('size'));
        } else {
            $validatedData['size'] = '';
        }

        // Kiểm tra xem có thay đổi giá hoặc thông tin quan trọng không
        $priceChanged = $product->price != $validatedData['price'];
        $discountChanged = $product->discount != $validatedData['discount'];
        $statusChanged = $product->status != $validatedData['status'];
        $stockChanged = $product->stock != $validatedData['stock'];

        $status = $product->update($validatedData);

        if ($status) {
            // Nếu thay đổi giá, giảm giá, status, hoặc stock - xóa sản phẩm khỏi các giỏ hàng và danh sách yêu thích
            if ($priceChanged || $discountChanged || $statusChanged || $stockChanged) {
                // Lấy tất cả carts chưa checkout của sản phẩm này
                $affectedCarts = Cart::where('product_id', $id)
                    ->whereNull('order_id')
                    ->get();

                // Lấy tất cả wishlists chứa sản phẩm này
                $affectedWishlists = Wishlist::where('product_id', $id)->get();

                // Kết hợp danh sách users bị ảnh hưởng từ cả cart và wishlist
                $affectedUserIds = collect();
                if ($affectedCarts->count() > 0) {
                    $affectedUserIds = $affectedUserIds->merge($affectedCarts->pluck('user_id')->unique());
                }
                if ($affectedWishlists->count() > 0) {
                    $affectedUserIds = $affectedUserIds->merge($affectedWishlists->pluck('user_id')->unique());
                }

                // Xóa tất cả carts chứa sản phẩm này
                if ($affectedCarts->count() > 0) {
                    Cart::where('product_id', $id)->whereNull('order_id')->delete();
                }

                // Xóa tất cả wishlists chứa sản phẩm này
                if ($affectedWishlists->count() > 0) {
                    Wishlist::where('product_id', $id)->delete();
                }

                // Gửi thông báo tới các customers bị ảnh hưởng
                if ($affectedUserIds->count() > 0) {
                    foreach ($affectedUserIds->unique() as $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $changeMessage = '';
                            if ($priceChanged) {
                                $changeMessage .= "Giá đã thay đổi từ " . number_format($product->getOriginal('price'), 0, ',', '.') . " VNĐ thành " . number_format($validatedData['price'], 0, ',', '.') . " VNĐ. ";
                            }
                            if ($discountChanged) {
                                $changeMessage .= "Khuyến mãi đã thay đổi. ";
                            }
                            if ($statusChanged) {
                                $changeMessage .= "Trạng thái sản phẩm đã thay đổi. ";
                            }
                            if ($stockChanged) {
                                $changeMessage .= "Số lượng tồn kho đã thay đổi. ";
                            }

                            $details = [
                                'title' => 'Sản phẩm của bạn đã thay đổi',
                                'message' => 'Sản phẩm "' . $product->title . '" trong giỏ hàng và danh sách yêu thích của bạn đã được xóa vì ' . trim($changeMessage) . 'Vui lòng thêm sản phẩm lại nếu muốn mua với thông tin mới.',
                                'actionURL' => route('product-detail', $product->slug),
                                'fas' => 'fa-exclamation-triangle'
                            ];
                            Notification::send($user, new StatusNotification($details));
                        }
                    }
                }
            }
        }

        $message = $status
            ? 'Sản phẩm đã được cập nhật thành công'
            : 'Vui lòng thử lại!!';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Kiểm tra xem sản phẩm có trong các carts chưa checkout không
        $cartCount = Cart::where('product_id', $id)->whereNull('order_id')->count();

        if ($cartCount > 0) {
            // Lấy danh sách users bị ảnh hưởng
            $affectedCarts = Cart::where('product_id', $id)->whereNull('order_id')->get();
            $affectedUserIds = $affectedCarts->pluck('user_id')->unique();

            // Xóa carts trước khi xóa product
            Cart::where('product_id', $id)->whereNull('order_id')->delete();

            // Gửi thông báo tới các customers
            foreach ($affectedUserIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $details = [
                        'title' => 'Sản phẩm bị xóa khỏi cửa hàng',
                        'message' => 'Sản phẩm "' . $product->title . '" mà bạn có trong giỏ hàng đã bị xóa khỏi cửa hàng. Sản phẩm này không còn khả dụng.',
                        'actionURL' => route('home'),
                        'fas' => 'fa-trash-alt'
                    ];
                    Notification::send($user, new StatusNotification($details));
                }
            }
        }

        // Xóa wishlists
        Wishlist::where('product_id', $id)->delete();

        // Xóa product reviews
        ProductReview::where('product_id', $id)->delete();

        // Xóa product
        $status = $product->delete();

        $message = $status
            ? 'Sản phẩm đã được xóa thành công (đã thông báo tới ' . $cartCount . ' khách hàng có sản phẩm trong giỏ)'
            : 'Lỗi khi xóa sản phẩm';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }
}
