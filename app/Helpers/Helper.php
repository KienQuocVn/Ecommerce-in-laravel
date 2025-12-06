<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Wishlist;
use App\Models\LiveStream;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;

class Helper
{
    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }

    public static function getAllCategory()
    {
        $category = new Category();
        return $category->getAllParentWithChild();
    }

    public static function getHeaderCategory()
    {
        $category = new Category();
        $menu = $category->getAllParentWithChild();

        if ($menu) {
?>
            <li>
                <a href="javascript:void(0);">Loại<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php foreach ($menu as $cat_info) : ?>
                        <?php if ($cat_info->child_cat->count() > 0) : ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php foreach ($cat_info->child_cat as $sub_menu) : ?>
                                        <li><a href="<?php echo route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </li>
<?php
        }
    }

    public static function productCategoryList($option = 'all')
    {
        if ($option === 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }

        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    public static function postTagList($option = 'all')
    {
        if ($option === 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }

        return PostTag::has('posts')->orderBy('id', 'desc')->get();
    }

    public static function postCategoryList($option = 'all')
    {
        if ($option === 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }

        return PostCategory::has('posts')->orderBy('id', 'DESC')->get();
    }

    public static function cartCount($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            return Cart::where('user_id', $user_id)
                ->whereNull('order_id')
                ->sum('quantity');
        }

        return 0;
    }

    public static function getAllProductFromCart($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            return Cart::with('product')
                ->where('user_id', $user_id)
                ->whereNull('order_id')
                ->get();
        }

        return collect();
    }

    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            // Tính tổng giá với giảm giá từ database
            $cartItems = Cart::with('product')
                ->where('user_id', $user_id)
                ->whereNull('order_id')
                ->get();

            $total = 0;
            foreach ($cartItems as $cart) {
                $original_price = $cart->product['price'] ?? 0;
                $discount = $cart->product['discount'] ?? 0;
                $discounted_price = $original_price - ($original_price * $discount / 100);
                $total += $discounted_price * $cart->quantity;
            }

            return $total;
        }

        return 0;
    }

    public static function wishlistCount($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            return Wishlist::where('user_id', $user_id)
                ->whereNull('cart_id')
                ->sum('quantity');
        }

        return 0;
    }

    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            return Wishlist::with('product')
                ->where('user_id', $user_id)
                ->whereNull('cart_id')
                ->get();
        }

        return collect();
    }

    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id === '') {
                $user_id = auth()->user()->id;
            }

            // Tính tổng giá với giảm giá từ database
            $wishlistItems = Wishlist::with('product')
                ->where('user_id', $user_id)
                ->whereNull('cart_id')
                ->get();

            $total = 0;
            foreach ($wishlistItems as $wishlist) {
                $original_price = $wishlist->product['price'] ?? 0;
                $discount = $wishlist->product['discount'] ?? 0;
                $discounted_price = $original_price - ($original_price * $discount / 100);
                $total += $discounted_price;
            }

            return $total;
        }

        return 0;
    }

    public static function shipping(?float $cartTotal = null)
    {
        $cartTotal = $cartTotal ?? self::totalCartPrice();

        return Shipping::active()
            ->availableForTotal($cartTotal)
            ->orderBy('priority')
            ->orderBy('price')
            ->get()
            ->map(function (Shipping $shipping) use ($cartTotal) {
                $shipping->calculated_cost = $shipping->calculateCost($cartTotal);

                return $shipping;
            });
    }

    public static function recommendedProductsForUser($user, int $limit = 6)
    {
        if (!$user) {
            return collect();
        }

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->whereNotNull('order_id')
            ->whereHas('order', function ($query) {
                $query->where('status', 'delivered');
            })
            ->get();

        $categoryIds = $cartItems->pluck('product.cat_id')->filter()->unique()->values();
        $purchasedIds = $cartItems->pluck('product_id')->filter()->unique()->values();
        $prices = $cartItems->pluck('product.price')->filter()->map(fn($price) => (float) $price)->values();

        $query = Product::where('status', 'active');

        if ($categoryIds->isNotEmpty()) {
            $query->whereIn('cat_id', $categoryIds->toArray());
        }

        if ($purchasedIds->isNotEmpty()) {
            $query->whereNotIn('id', $purchasedIds->toArray());
        }

        if ($prices->isNotEmpty()) {
            $average = $prices->avg();
            $min = max(0, $average * 0.7);
            $max = $average * 1.3;
            $query->whereBetween('price', [$min, $max]);
        }

        $recommendations = $query->orderByDesc('is_featured')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($recommendations->count() < $limit) {
            $fallback = Product::where('status', 'active')
                ->whereNotIn('id', $purchasedIds->toArray())
                ->withCount('carts')
                ->orderBy('carts_count', 'desc')
                ->orderBy('discount', 'desc')
                ->limit($limit - $recommendations->count())
                ->get();
            $recommendations = $recommendations->merge($fallback)->unique('id')->take($limit);
        }

        return $recommendations;
    }

    public static function grandPrice($orderId)
    {
        $order = Order::with('shipping')->find($orderId);

        if (!$order) {
            return 0;
        }

        $subTotal = (float) $order->sub_total;
        $shippingCost = 0;

        if ($order->shipping) {
            $shippingCost = $order->shipping->calculateCost($subTotal);
        }

        return round($subTotal + $shippingCost, 2);
    }

    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        $price = 0;

        foreach ($month_data as $data) {
            $price = $data->cart_info->sum('price');
        }

        return number_format((float) $price, 2, '.', '');
    }

    public static function getImageUrl($path, $default = '/images/default.jpg')
    {
        return CloudinaryService::getImageUrl($path, $default);
    }

    public static function hasActiveLiveStream()
    {
        return LiveStream::hasActive();
    }

    public static function getActiveLiveStream()
    {
        return LiveStream::getActive();
    }
}
