<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Newsletter\Facades\Newsletter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function index(Request $request)
    {
        $role = $request->user()->role ?? 'guest';

        switch ($role) {
            case 'admin':
                return redirect()->route('admin');
            case 'shipper':
                return redirect()->route('shipper.dashboard');
            case 'user':
                return redirect()->route('user');
            default:
                return redirect()->route('home');
        }
    }

    public function home()
    {
        $featured = Product::where('status', 'active')->where('is_featured', 1)->orderBy('price', 'DESC')->limit(2)->get();
        $posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        $banners = Banner::where('status', 'active')->limit(3)->orderBy('id', 'DESC')->get();
        $products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(8)->get();
        $category = Category::where('status', 'active')->where('is_parent', 1)->orderBy('title', 'ASC')->get();

        return view('frontend.index')
            ->with('featured', $featured)
            ->with('posts', $posts)
            ->with('banners', $banners)
            ->with('product_lists', $products)
            ->with('category_lists', $category);
    }

    public function aboutUs()
    {
        return view('frontend.pages.about-us');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function productDetail($slug)
    {
        $product_detail = Product::getProductBySlug($slug);
        return view('frontend.pages.product_detail')->with('product_detail', $product_detail);
    }

    /**
     * Hiển thị danh sách sản phẩm (List view)
     * Sử dụng để hiển thị sản phẩm dưới dạng danh sách
     */
    public function productLists()
    {
        $products = Product::where('status', 'active');

        // Lọc theo category
        if (!empty($_GET['category'])) {
            $slug = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
            $products->whereIn('cat_id', $cat_ids);
        }

        // Lọc theo brand
        if (!empty($_GET['brand'])) {
            $slugs = explode(',', $_GET['brand']);
            $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products->whereIn('brand_id', $brand_ids);
        }

        // Lọc theo size
        if (!empty($_GET['size'])) {
            $sizes = explode(',', $_GET['size']);
            $products->where(function ($query) use ($sizes) {
                foreach ($sizes as $size) {
                    $query->orWhere('size', 'like', "%{$size}%");
                }
            });
        }

        // Lọc theo condition
        if (!empty($_GET['condition'])) {
            $products->where('condition', $_GET['condition']);
        }

        // Lọc theo đánh giá
        if (!empty($_GET['rating'])) {
            $rating = (int)$_GET['rating'];
            $product_ids = DB::table('product_reviews')
                ->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('AVG(rate) >= ?', [$rating])
                ->pluck('product_id')
                ->toArray();
            $products->whereIn('id', $product_ids);
        }

        // Lọc theo giá
        if (!empty($_GET['price'])) {
            $price = explode('-', $_GET['price']);
            $products->whereBetween('price', $price);
        }

        // Sắp xếp
        if (!empty($_GET['sortBy'])) {
            switch ($_GET['sortBy']) {
                case 'title':
                case 'title_asc':
                    $products->orderBy('title', 'ASC');
                    break;
                case 'title_desc':
                    $products->orderBy('title', 'DESC');
                    break;
                case 'price':
                case 'price_asc':
                    $products->orderBy('price', 'ASC');
                    break;
                case 'price_desc':
                    $products->orderBy('price', 'DESC');
                    break;
                case 'discount':
                    $products->orderBy('discount', 'DESC');
                    break;
                case 'newest':
                    $products->orderBy('id', 'DESC');
                    break;
                case 'popular':
                    $products->withCount('carts')->orderBy('carts_count', 'DESC');
                    break;
                default:
                    $products->orderBy('id', 'DESC');
            }
        } else {
            $products->orderBy('id', 'DESC');
        }

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        // Phân trang
        $per_page = !empty($_GET['show']) ? (int)$_GET['show'] : 12;
        $products = $products->paginate($per_page);

        // Lấy danh sách sizes
        $available_sizes = Product::where('status', 'active')
            ->whereNotNull('size')
            ->get()
            ->pluck('size')
            ->flatMap(function ($size) {
                return explode(',', $size);
            })
            ->map(function ($size) {
                return trim($size);
            })
            ->unique()
            ->sort()
            ->values();

        return view('frontend.pages.product-lists')
            ->with('products', $products)
            ->with('recent_products', $recent_products)
            ->with('available_sizes', $available_sizes)
            ->with('viewMode', 'list');
    }

    // FIX: Giữ nguyên trang hiện tại khi filter
    public function productFilter(Request $request)
    {
        $data = $request->validate([
            'show' => 'nullable|integer|min:6|max:60',
            'sortBy' => 'nullable|string|max:20',
            'category' => 'nullable|array',
            'category.*' => 'string',
            'brand' => 'nullable|array',
            'brand.*' => 'string',
            'size' => 'nullable|array',
            'size.*' => 'string|max:10',
            'condition' => 'nullable|in:new,hot,sale',
            'rating' => 'nullable|integer|min:3|max:5',
            'price_range' => ['nullable', 'regex:/^\d+\-\d+$/'],
        ]);

        // Build URL parameters
        $params = [];

        if (!empty($data['show'])) {
            $params['show'] = $data['show'];
        }

        if (!empty($data['sortBy'])) {
            $params['sortBy'] = $data['sortBy'];
        }

        if (!empty($data['category'])) {
            $params['category'] = implode(',', $data['category']);
        }

        if (!empty($data['brand'])) {
            $params['brand'] = implode(',', $data['brand']);
        }

        if (!empty($data['size'])) {
            $params['size'] = implode(',', $data['size']);
        }

        if (!empty($data['condition'])) {
            $params['condition'] = $data['condition'];
        }

        if (!empty($data['rating'])) {
            $params['rating'] = $data['rating'];
        }

        if (!empty($data['price_range'])) {
            $params['price'] = $data['price_range'];
        }

        // Luôn redirect sang product-lists (chỉ sử dụng product-lists)
        return redirect()->route('product-lists', $params);
    }

    public function productSearch(Request $request)
    {
        $request->validate([
            'search' => 'required|string|max:120',
        ]);

        $search = $request->search;

        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $products = Product::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(12);

        // Lấy sizes
        $available_sizes = Product::where('status', 'active')
            ->whereNotNull('size')
            ->get()
            ->pluck('size')
            ->flatMap(function ($size) {
                return explode(',', $size);
            })
            ->map(function ($size) {
                return trim($size);
            })
            ->unique()
            ->sort()
            ->values();

        return view('frontend.pages.product-lists', [
            'products' => $products,
            'recent_products' => $recent_products,
            'available_sizes' => $available_sizes
        ]);
    }

    public function productBrand(Request $request)
    {
        $brand = Brand::where('slug', $request->slug)->first();

        if (!$brand) {
            abort(404);
        }

        // Lấy products theo brand và phân trang
        $products = Product::where('status', 'active')
            ->where('brand_id', $brand->id)
            ->orderBy('id', 'DESC')
            ->paginate(12); // ← PHÂN TRANG

        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        // Lấy sizes
        $available_sizes = Product::where('status', 'active')
            ->whereNotNull('size')
            ->get()
            ->pluck('size')
            ->flatMap(function ($size) {
                return explode(',', $size);
            })
            ->map(function ($size) {
                return trim($size);
            })
            ->unique()
            ->sort()
            ->values();

        // Luôn sử dụng product-lists
        return view('frontend.pages.product-lists', [
            'products' => $products,
            'recent_products' => $recent_products,
            'available_sizes' => $available_sizes,
            'viewMode' => 'list',
        ]);
    }

    public function productCat(Request $request)
    {
        $category = Category::getProductByCat($request->slug);

        if (!$category) {
            abort(404);
        }

        // Lấy products và phân trang
        $products = Product::where('status', 'active')
            ->where('cat_id', $category->id)
            ->orderBy('id', 'DESC')
            ->paginate(12); // ← PHÂN TRANG

        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        // Lấy sizes
        $available_sizes = Product::where('status', 'active')
            ->whereNotNull('size')
            ->get()
            ->pluck('size')
            ->flatMap(function ($size) {
                return explode(',', $size);
            })
            ->map(function ($size) {
                return trim($size);
            })
            ->unique()
            ->sort()
            ->values();

        // Luôn sử dụng product-lists
        return view('frontend.pages.product-lists', [
            'products' => $products,
            'recent_products' => $recent_products,
            'available_sizes' => $available_sizes,
            'viewMode' => 'list',
        ]);
    }

    public function productSubCat(Request $request)
    {
        $category = Category::where('slug', $request->sub_slug)->first();

        if (!$category) {
            abort(404);
        }

        // Lấy products theo sub category và phân trang
        $products = Product::where('status', 'active')
            ->where('child_cat_id', $category->id)
            ->orderBy('id', 'DESC')
            ->paginate(12); // ← PHÂN TRANG

        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        // Lấy sizes
        $available_sizes = Product::where('status', 'active')
            ->whereNotNull('size')
            ->get()
            ->pluck('size')
            ->flatMap(function ($size) {
                return explode(',', $size);
            })
            ->map(function ($size) {
                return trim($size);
            })
            ->unique()
            ->sort()
            ->values();

        // Luôn sử dụng product-lists
        return view('frontend.pages.product-lists')
            ->with('products', $products)
            ->with('recent_products', $recent_products)
            ->with('available_sizes', $available_sizes);
    }


    public function blog()
    {
        $post = Post::query();

        if (!empty($_GET['category'])) {
            $slug = explode(',', $_GET['category']);
            $cat_ids = PostCategory::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
            // XÓA DÒNG NÀY: return $cat_ids;  // <- Đây là debug code, phải xóa đi!
            $post->whereIn('post_cat_id', $cat_ids);
        }

        if (!empty($_GET['tag'])) {
            $slug = explode(',', $_GET['tag']);
            $tag_ids = PostTag::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
            $post->where('post_tag_id', $tag_ids);
        }

        if (!empty($_GET['show'])) {
            $posts = $post->where('status', 'active')->orderBy('id', 'asc')->paginate($_GET['show']);
        } else {
            $posts = $post->where('status', 'active')->orderBy('id', 'asc')->paginate(9);
        }

        $recent_posts = Post::where('status', 'active')->orderBy('id', 'asc')->limit(3)->get();
        return view('frontend.pages.blog', compact('posts', 'recent_posts'));
    }

    public function blogDetail($slug)
    {
        $post = Post::getPostBySlug($slug);
        $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        return view('frontend.pages.blog-detail', compact('post', 'recent_posts'));
    }

    public function blogSearch(Request $request)
    {
        $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        $posts = Post::where('title', 'like', '%' . $request->search . '%')
            ->orWhere('quote', 'like', '%' . $request->search . '%')
            ->orWhere('summary', 'like', '%' . $request->search . '%')
            ->orWhere('description', 'like', '%' . $request->search . '%')
            ->orWhere('slug', 'like', '%' . $request->search . '%')
            ->where('status', 'active') // Thêm điều kiện này
            ->orderBy('id', 'DESC')
            ->paginate(8);
        return view('frontend.pages.blog', compact('posts', 'recent_posts'));
    }

    public function blogFilter(Request $request)
    {
        $data = $request->all();
        $catURL = "";
        if (!empty($data['category'])) {
            foreach ($data['category'] as $category) {
                if (empty($catURL)) {
                    $catURL .= '&category=' . $category;
                } else {
                    $catURL .= ',' . $category;
                }
            }
        }

        $tagURL = "";
        if (!empty($data['tag'])) {
            foreach ($data['tag'] as $tag) {
                if (empty($tagURL)) {
                    $tagURL .= '&tag=' . $tag;
                } else {
                    $tagURL .= ',' . $tag;
                }
            }
        }
        return redirect()->route('blog', $catURL . $tagURL);
    }

    public function blogByCategory(Request $request)
    {
        $category = PostCategory::getBlogByCategory($request->slug);
        // Lấy posts theo category và phân trang
        $posts = Post::where('post_cat_id', $category->id)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->paginate(10);
        $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        return view('frontend.pages.blog', compact('posts', 'recent_posts'));
    }

    public function blogByTag($slug)
    {
        // Lấy posts theo tag slug (getBlogByTag đã paginate rồi)
        $posts = Post::getBlogByTag($slug);
        $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        // Lấy thông tin tag để hiển thị title (nếu cần)
        $tag = PostTag::where('slug', $slug)->first();

        return view('frontend.pages.blog', compact('posts', 'recent_posts', 'tag'));
    }

    // Login
    public function login()
    {
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        // Kiểm tra user có tồn tại không
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])
                ->withInput($request->only('email'));
        }

        if ($user->status !== 'active') {
            return back()
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'])
                ->withInput($request->only('email'));
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'status' => 'active'])) {
            $request->session()->regenerate();
            Session::put('user', $credentials['email']);
            session()->flash('success', 'Đăng nhập thành công');

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if ($user && $user->needsProfileCompletion()) {
                session()->flash('warning', 'Tài khoản của bạn chưa đủ thông tin. Vui lòng cập nhật hồ sơ để thanh toán nhanh hơn.');
            }

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin');
                case 'shipper':
                    return redirect()->route('shipper.dashboard');
                case 'user':
                    return redirect()->route('user');
                default:
                    return redirect()->route('home');
            }
        }

        return back()
            ->withErrors(['password' => 'Mật khẩu không đúng.'])
            ->withInput($request->only('email'));
    }

    public function logout()
    {
        Session::forget('user');
        Auth::logout();
        session()->flash('success', 'Đăng xuất thành công');
        return back();
    }

    public function register()
    {
        return view('frontend.pages.register');
    }
    public function registerSubmit(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|min:2|max:120',
            'last_name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|min:8|max:20|regex:/^[0-9+\-\s()]+$/',
            'address_line1' => 'required|string|min:5|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
        ], [
            'first_name.required' => 'Vui lòng nhập tên.',
            'first_name.min' => 'Tên phải có ít nhất 2 ký tự.',
            'last_name.required' => 'Vui lòng nhập họ.',
            'last_name.min' => 'Họ phải có ít nhất 2 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.min' => 'Số điện thoại phải có ít nhất 8 ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'address_line1.required' => 'Vui lòng nhập địa chỉ.',
            'address_line1.min' => 'Địa chỉ phải có ít nhất 5 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        try {
            $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
            $check = $this->create($data);

            if ($check) {
                Session::put('user', $data['email']);
                Auth::login($check);
                session()->flash('success', 'Đã đăng ký thành công');
                if ($check->needsProfileCompletion()) {
                    session()->flash('warning', 'Vui lòng hoàn tất hồ sơ để trải nghiệm thanh toán nhanh hơn.');
                }
                return redirect()->route('home');
            }

            session()->flash('error', 'Không thể tạo tài khoản. Vui lòng thử lại!');
            return back()->withInput();
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            return back()->withInput();
        }
    }
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address_line1' => $data['address_line1'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'status' => 'active'
        ]);
    }
    // Reset password
    public function showResetForm()
    {
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request)
    {
        if (! Newsletter::isSubscribed($request->email)) {
            Newsletter::subscribePending($request->email);
            if (Newsletter::lastActionSucceeded()) {
                session()->flash('success', 'Đã đăng ký! Vui lòng kiểm tra email của bạn');
                return redirect()->route('home');
            } else {
                Newsletter::getLastError();
                return back()->with('error', 'Có gì đó không ổn! Vui lòng thử lại');
            }
        } else {
            session()->flash('error', 'Đã đăng ký');
            return back();
        }
    }
}
