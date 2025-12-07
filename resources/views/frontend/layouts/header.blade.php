<header class="header shop">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-6">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main">
                            @php
                            $settings=DB::table('settings')->get();
                            @endphp
                            <li><i class="ti-headphone-alt"></i>@foreach($settings as $data) {{$data->phone}} @endforeach</li>
                            <li><i class="ti-email"></i> @foreach($settings as $data) {{$data->email}} @endforeach</li>
                        </ul>
                    </div>
                    <!--/ End Top Left -->
                </div>
                <div class="col-lg-6 col-md-6 col-6">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main">
                            {{--<li><i class="ti-location-pin"></i> <a href="{{route('order.track')}}">Theo dõi đơn hàng</a></li>--}}
                            {{-- <li><i class="ti-alarm-clock"></i> <a href="#">Ưu đãi hàng ngày</a></li> --}}

                            @auth
                            @if(Auth::user()->role=='admin')
                            <li><i class="ti-user"></i> <a href="{{route('admin')}}" target="_blank">Bảng điều khiển</a></li>
                            @elseif(Auth::user()->role=='shipper')
                            <li><i class="ti-truck"></i> <a href="{{route('shipper.dashboard')}}" target="_blank">Bảng điều khiển</a></li>
                            @else
                            <li><i class="ti-user"></i> <a href="{{route('user')}}" target="_blank">Bảng điều khiển</a></li>
                            @endif
                            <li><i class="ti-power-off"></i> <a href="{{route('user.logout')}}">Đăng xuất</a></li>
                            @else
                            <li><i class="ti-power-off"></i><a href="{{route('login.form')}}">Đăng nhập /</a> <a href="{{route('register.form')}}">Đăng ký</a></li>
                            @endauth
                        </ul>
                    </div>
                    <!-- End Top Right -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->

    <!-- Middle Inner - RESPONSIVE LAYOUT -->
    <div class="middle-inner">
        <div class="container">
            <!-- ============================================
                 DESKTOP LAYOUT (Hidden on mobile ≤767px)
                 ============================================ -->
            <div class="row desktop-header-layout">
                <div class="col-lg-2 col-md-2 col-12">
                    <!-- Logo -->
                    <div class="logo">
                        @php
                        $settings=DB::table('settings')->get();
                        @endphp
                        <a href="{{route('home')}}">
                            <img src="@foreach($settings as $data) {{$data->logo}} @endforeach" alt="logo">
                        </a>
                    </div>
                    <!--/ End Logo -->
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <div class="search-top">
                            <form class="search-form" method="POST" action="{{route('product.search')}}">
                                @csrf
                                <input type="text" placeholder="Tìm kiếm tại đây..." name="search">
                                <button type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Search Form -->
                    </div>
                    <!--/ End Search Form -->
                    <div class="mobile-nav"></div>
                </div>
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="search-bar-top">
                        <div class="search-bar">
                            <form method="POST" action="{{route('product.search')}}">
                                @csrf
                                <input name="search" placeholder="Tìm kiếm sản phẩm tại đây....." type="search">
                                <button class="btnn" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-12">
                    <div class="right-bar">
                        <!-- Wishlist -->
                        <div class="sinlge-bar shopping">
                            @php
                            $total_prod=0;
                            $total_amount=0;
                            @endphp
                            @if(session('wishlist'))
                            @foreach(session('wishlist') as $wishlist_items)
                            @php
                            $total_prod+=$wishlist_items['quantity'];
                            $total_amount+=$wishlist_items['amount'];
                            @endphp
                            @endforeach
                            @endif
                            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span class="total-count">{{Helper::wishlistCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                            <div class="shopping-item">
                                <div class="dropdown-cart-header">
                                    <span>{{count(Helper::getAllProductFromWishlist())}} Mặt hàng</span>
                                    <a href="{{route('wishlist')}}">Xem danh sách yêu thích</a>
                                </div>
                                <ul class="shopping-list">
                                    @foreach(Helper::getAllProductFromWishlist() as $data)
                                    @php
                                    $photo=explode(',',$data->product['photo']);
                                    @endphp
                                    <li>
                                        <a href="{{route('wishlist-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                        <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                        <p class="quantity">{{$data->quantity}} x - <span class="amount">{{number_format($data->price,0)}} VNĐ</span></p>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="bottom">
                                    <div class="total">
                                        <span>Tổng cộng</span>
                                        <span class="total-amount">{{number_format(Helper::totalWishlistPrice(),0)}} VNĐ</span>
                                    </div>
                                    <a href="{{route('cart')}}" class="btn animate">Giỏ hàng</a>
                                </div>
                            </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                        <!-- Cart -->
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon">
                                <i class="ti-bag"></i>
                                <span class="total-count cart-count">{{Helper::cartCount()}}</span>
                            </a>
                            <!-- Shopping Item -->
                            @auth
                            <div class="shopping-item">
                                <div class="dropdown-cart-header">
                                    <span>{{count(Helper::getAllProductFromCart())}} Mặt hàng</span>
                                    <a href="{{route('cart')}}">XEM GIỎ HÀNG</a>
                                </div>
                                <ul class="shopping-list">
                                    @foreach(Helper::getAllProductFromCart() as $data)
                                    @php
                                    $photo=explode(',',$data->product['photo']);
                                    @endphp
                                    <li>
                                        <a href="{{route('cart-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                        <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                        <p class="quantity">{{$data->quantity}} x - <span class="amount">{{number_format($data->price,0)}} VNĐ</span></p>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="bottom">
                                    <div class="total">
                                        <span>Tổng cộng</span>
                                        <span class="total-amount">{{number_format(Helper::totalCartPrice(),0)}} VNĐ</span>
                                    </div>
                                    <a href="{{route('checkout')}}" class="btn animate">Thanh toán</a>
                                </div>
                            </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
                 MOBILE LAYOUT (Visible only on mobile ≤767px)
                 ============================================ -->
            <!-- Row 1: Logo, Right Bar (Wishlist + Cart), Menu Toggle -->
            <div class="mobile-header-row-1">
                <!-- Mobile Logo -->
                <div class="mobile-logo">
                    @php
                    $settings=DB::table('settings')->get();
                    @endphp
                    <a href="{{route('home')}}">
                        <img src="@foreach($settings as $data) {{$data->logo}} @endforeach" alt="logo">
                    </a>
                </div>

                <!-- Mobile Actions (Right Bar + Menu Toggle) -->
                <div class="mobile-actions">
                    <!-- Mobile Right Bar -->
                    <div class="mobile-right-bar">
                        <!-- Wishlist -->
                        <div class="sinlge-bar shopping">
                            <a href="{{route('wishlist')}}" class="single-icon">
                                <i class="fa fa-heart-o"></i>
                                <span class="total-count">{{Helper::wishlistCount()}}</span>
                            </a>
                        </div>
                        <!-- Cart -->
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon">
                                <i class="ti-bag"></i>
                                <span class="total-count cart-count">{{Helper::cartCount()}}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Mobile Nav Toggle (Slicknav will be initialized here) -->
                    <div class="mobile-nav"></div>
                </div>
            </div>

            <!-- Row 2: Search Bar -->
            <div class="mobile-header-row-2">
                <div class="mobile-search-bar">
                    <form method="POST" action="{{route('product.search')}}">
                        @csrf
                        <input name="search" placeholder="Tìm kiếm sản phẩm..." type="search">
                        <button class="btnn" type="submit"><i class="ti-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Middle Inner -->

    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="menu-area">
                            <!-- Main Menu -->
                            <nav class="navbar navbar-expand-lg">
                                <div class="navbar-collapse">
                                    <div class="nav-inner">
                                        <ul class="nav main-menu menu navbar-nav">
                                            <li class="{{Request::path()=='home' ? 'active' : ''}}"><a href="{{route('home')}}">Trang chủ</a></li>
                                            <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a href="{{route('about-us')}}">Về chúng tôi</a></li>
                                            <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists')  active  @endif"><a href="{{route('product-lists')}}">Sản phẩm</a><span class="new">Mới</span></li>
                                            {{Helper::getHeaderCategory()}}
                                            <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a href="{{route('blog')}}">Blog</a></li>
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{route('contact')}}">Liên hệ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            <!--/ End Main Menu -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>



<!-- Floating Live Badge (Bottom Left) -->
<div id="live-stream-badge" style="position: fixed; bottom: 30px; left: 20px; z-index: 999; display: none;">
    <a href="#" id="live-stream-badge-link" style="background: rgba(0, 0, 0, 0.8); color: white; padding: 12px 18px; border-radius: 30px; text-decoration: none; display: flex; align-items: center; gap: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.3);">
        <span class="live-dot" style="width: 10px; height: 10px; background: #ff0000; border-radius: 50%; animation: blink 1s infinite;"></span>
        <span style="font-weight: 600;">Đang LIVE</span>
    </a>
</div>

@push('styles')
<style>
    /* Floating Live Icon (Top Left) */
    #floating-live-icon {
        position: fixed;
        top: 80px;
        left: 20px;
        z-index: 999;
        display: none;
    }

    #floating-live-link {
        background: #ff0000;
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        animation: pulse-live 2s infinite;
    }

    /* Floating Live Badge (Bottom Left) */
    #live-stream-badge {
        position: fixed;
        bottom: 30px;
        left: 20px;
        z-index: 999;
        display: none;
    }

    #live-stream-badge a {
        background: rgba(0, 0, 0, 0.85);
        color: #fff;
        padding: 12px 18px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s;
    }

    #live-stream-badge a:hover {
        transform: translateY(-2px);
    }

    .live-dot {
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        animation: blink 1s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    @keyframes pulse-live {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.6);
        }
    }

    #floating-live-icon:hover {
        transform: scale(1.1);
        transition: transform 0.3s;
    }

    /* Ensure list items don't break layout */
    .list-main li {
        display: inline-block;
        vertical-align: middle;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #floating-live-icon {
            top: 60px;
            left: 10px;
        }

        #floating-live-link {
            padding: 8px 15px;
            font-size: 12px;
        }

        #live-stream-badge {
            bottom: 20px;
            left: 10px;
        }

        #live-stream-badge a {
            padding: 10px 14px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Check for active live stream
    function checkLiveStream() {
        const floatingIcon = document.getElementById('floating-live-icon');
        const floatingLink = document.getElementById('floating-live-link');
        const liveBadge = document.getElementById('live-stream-badge');
        const liveBadgeLink = document.getElementById('live-stream-badge-link');

        fetch('/api/live-stream/status')
            .then(res => res.json())
            .then(data => {
                if (data.has_active && data.stream) {
                    const streamUrl = '/live-stream/view/' + data.stream.id;

                    if (floatingIcon && floatingLink) {
                        floatingIcon.style.display = 'block';
                        floatingLink.href = streamUrl;
                    }

                    if (liveBadge && liveBadgeLink) {
                        liveBadge.style.display = 'block';
                        liveBadgeLink.href = streamUrl;
                    }
                } else {
                    if (floatingIcon) {
                        floatingIcon.style.display = 'none';
                    }
                    if (liveBadge) {
                        liveBadge.style.display = 'none';
                    }
                }
            })
            .catch(err => console.error('Error checking live stream:', err));
    }

    // Check on page load
    checkLiveStream();

    // Check every 10 seconds
    setInterval(checkLiveStream, 10000);
</script>
@endpush