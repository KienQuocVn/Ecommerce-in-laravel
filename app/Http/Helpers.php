<?php

use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Str;

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
        $menu = $category->getAllParentWithChild();
        return $menu;
    }

    public static function getHeaderCategory()
    {
        $category = new Category();
        // dd($category);
        $menu = $category->getAllParentWithChild();

        if ($menu) {
?>

            <li>
                <a href="javascript:void(0);">Loại<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php
                    foreach ($menu as $cat_info) {
                        if ($cat_info->child_cat->count() > 0) {
                    ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php
                                    foreach ($cat_info->child_cat as $sub_menu) {
                                    ?>
                                        <li><a href="<?php echo route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </li>
<?php
        }
    }

    public static function productCategoryList($option = 'all')
    {
        if ($option = 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    public static function postTagList($option = 'all')
    {
        if ($option = 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }
        return PostTag::has('posts')->orderBy('id', 'desc')->get();
    }

    public static function postCategoryList($option = "all")
    {
        if ($option = 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id', 'DESC')->get();
    }
    // Cart Count
    public static function cartCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }

    public static function getAllProductFromCart($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get();
        } else {
            return 0;
        }
    }
    // Total amount cart
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('amount');
        } else {
            return 0;
        }
    }
    // Wishlist Count
    public static function wishlistCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        } else {
            return 0;
        }
    }
    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('amount');
        } else {
            return 0;
        }
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

    // Total price with shipping and coupon
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


    // Admin home
    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        // return $month_data;
        $price = 0;
        foreach ($month_data as $data) {
            $price = $data->cart_info->sum('price');
        }
        return number_format((float)($price), 2, '.', '');
    }
}



if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given title and model.
     *
     * @param string $title
     * @param string $modelClass
     * @return string
     */
    function generateUniqueSlug($title, $modelClass)
    {
        $slug = Str::slug($title);
        $count = $modelClass::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}

?>