@extends('frontend.layouts.master')

@section('title','Trang thanh toán ')

@section('main-content')

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Trang chủ<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0)">Thanh toán</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Start Checkout -->
<section class="shop checkout section">
    <div class="container">
        @php
        $authUser = auth()->user();
        $profileIncomplete = $authUser && $authUser->needsProfileCompletion();
        @endphp
        @if($profileIncomplete)
        <div class="alert alert-warning mb-4">
            <strong>Chưa đủ thông tin hồ sơ!</strong> Vui lòng bổ sung Tên, Họ, Email, Số điện thoại và Địa chỉ tại
            <a href="{{route('user-profile')}}" class="font-weight-bold">trang hồ sơ</a> để hệ thống tự động điền thông tin khi thanh toán.
        </div>
        @endif
        <form class="form" method="POST" action="{{route('cart.order')}}">
            @csrf
            <div class="row">

                <div class="col-lg-8 col-12">
                    <div class="checkout-form">
                        <h2>Thực hiện thanh toán tại đây</h2>
                        <p>Vui lòng đăng ký để thanh toán nhanh hơn</p>
                        <!-- Form -->
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Tên<span>*</span></label>
                                    <input type="text" name="first_name" placeholder="" value="{{old('first_name', optional($authUser)->first_name)}}">
                                    @error('first_name')
                                    <span class='text-danger'>{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Họ<span>*</span></label>
                                    <input type="text" name="last_name" placeholder="" value="{{old('last_name', optional($authUser)->last_name)}}">
                                    @error('last_name')
                                    <span class='text-danger'>{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Email <span>*</span></label>
                                    <input type="email" name="email" placeholder="" value="{{old('email', optional($authUser)->email)}}">
                                    @error('email')
                                    <span class='text-danger'>{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Số điện thoại<span>*</span></label>
                                    <input type="text" name="phone" placeholder="" required value="{{old('phone', optional($authUser)->phone)}}">
                                    @error('phone')
                                    <span class='text-danger'>{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-12">
                                <div class="form-group">
                                    <label>Địa chỉ giao hàng<span>*</span></label>
                                    <input type="text" name="address1" placeholder="" value="{{old('address1', optional($authUser)->address_line1)}}">
                                    @error('address1')
                                    <span class='text-danger'>{{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <!--/ End Form -->
                    </div>
                </div>





                <div class="col-lg-4 col-12">
                    <div class="order-details">
                        <!-- Order Widget -->
                        <div class="single-widget">
                            <h2>TỔNG CỘNG GIỎ HÀNG</h2>
                            <div class="content">
                                @php
                                $cartTotal = Helper::totalCartPrice();
                                $shippingOptions = Helper::shipping($cartTotal);
                                $availableCoupons = auth()->check() ? auth()->user()->availableCoupons()->get() : collect();
                                @endphp
                                <ul>
                                    <li class="order_subtotal" data-price="{{$cartTotal}}">Tổng phụ giỏ hàng<span>{{number_format($cartTotal,0)}} VNĐ</span></li>
                                    <li class="coupon-selection">
                                        Mã giảm giá
                                        @if($availableCoupons->count() > 0)
                                        <select name="coupon_id" id="coupon_select" class="nice-select w-100" data-cart-total="{{$cartTotal}}">
                                            <option value="">Không sử dụng mã giảm giá</option>
                                            @foreach($availableCoupons as $userCoupon)
                                            @php
                                            $coupon = $userCoupon;
                                            $discountText = $coupon->type === 'fixed'
                                            ? number_format($coupon->value, 0) . ' VNĐ'
                                            : $coupon->value . '%';
                                            $expiresText = $userCoupon->pivot->expires_at
                                            ? ' (Hết hạn: ' . \Carbon\Carbon::parse($userCoupon->pivot->expires_at)->format('d/m/Y') . ')'
                                            : '';
                                            @endphp
                                            <option value="{{$coupon->id}}"
                                                data-type="{{$coupon->type}}"
                                                data-value="{{$coupon->value}}"
                                                data-discount="{{$coupon->discount($cartTotal)}}">
                                                {{$coupon->code}} - Giảm {{$discountText}}{{$expiresText}}
                                            </option>
                                            @endforeach
                                        </select>
                                        @else
                                        <span class="text-muted small">Bạn chưa có mã giảm giá khả dụng</span>
                                        @endif
                                    </li>
                                    <li class="coupon_discount" style="display:none;">Giảm giá<span id="coupon_discount_display">0 VNĐ</span></li>
                                    <li class="shipping">
                                        Chi phí vận chuyển
                                        @if($shippingOptions->count() > 0 && Helper::cartCount()>0)
                                        <select name="shipping" id="shipping_method" class="nice-select w-100" data-cart-total="{{$cartTotal}}">
                                            <option value="">Chọn phương thức vận chuyển</option>
                                            @foreach($shippingOptions as $shipping)
                                            <option value="{{$shipping->id}}"
                                                data-strategy="{{$shipping->pricing_strategy}}"
                                                data-percentage="{{$shipping->percentage_rate}}"
                                                data-base="{{$shipping->price}}"
                                                data-price="{{$shipping->calculated_cost}}"
                                                data-estimated="{{$shipping->estimated_time}}"
                                                data-description="{{$shipping->description}}"
                                                data-supports-cod="{{$shipping->supports_cod ? '1' : '0'}}">
                                                {{$shipping->type}}
                                                ({{number_format($shipping->calculated_cost,0)}} VNĐ{{ $shipping->estimated_time ? ' • '.$shipping->estimated_time : '' }})
                                                @if($shipping->pricing_strategy === 'percentage')
                                                - {{$shipping->percentage_rate}}% đơn hàng
                                                @endif
                                            </option>
                                            @endforeach
                                        </select>
                                        <div id="selected-shipping-meta" class="shipping-meta small text-muted mt-2"></div>
                                        @else
                                        <span>Free</span>
                                        @endif
                                    </li>
                                    <li class="shipping_total">Phí vận chuyển dự kiến<span id="shipping_cost_display" data-price="0">0 VNĐ</span></li>
                                    <li class="last" id="order_total_price" data-base-total="{{$cartTotal}}">Tổng cộng<span>{{number_format($cartTotal,0)}} VNĐ</span></li>
                                </ul>
                            </div>
                        </div>
                        <!--/ End Order Widget -->
                        <!-- Order Widget -->
                        <div class="single-widget">
                            <h2>Thanh toán</h2>
                            <div class="content">
                                <div class="checkbox">
                                    <form-group>
                                        <input name="payment_method" type="radio" value="cod"> <label> Thanh toán khi nhận hàng</label><br>
                                        <input name="payment_method" type="radio" value="paypal"> <label> PayPal</label>
                                        <input name="payment_method" type="radio" value="momo"> <label> Momo</label>
                                        <input name="payment_method" type="radio" value="stripe"> <label> Stripe</label>
                                        <input name="payment_method" type="radio" value="vnpay"> <label> VNPay</label>
                                    </form-group>
                                </div>
                            </div>
                        </div>
                        <!--/ End Order Widget -->
                        <!-- Payment Method Widget -->
                        <div class="single-widget payement">
                            <div class="content">
                                <img src="{{('backend/img/payment-method.png')}}" alt="#">
                            </div>
                        </div>
                        <!--/ End Payment Method Widget -->
                        <!-- Button Widget -->
                        <div class="single-widget get-button">
                            <div class="content">
                                <div class="button">
                                    <button type="submit" class="btn">tiến hành thanh toán</button>
                                </div>
                            </div>
                        </div>
                        <!--/ End Button Widget -->
                    </div>
                </div>


            </div>
        </form>
    </div>
</section>
<!--/ End Checkout -->

<!-- Start Shop Services Area  -->
<section class="shop-services section home">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service">
                    <i class="ti-rocket"></i>
                    <h4>Miễn phí vận chuyển</h4>
                    <p>Đơn hàng trên 100k</p>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service">
                    <i class="ti-reload"></i>
                    <h4>Trả hàng miễn phí</h4>
                    <p>Trả hàng trong vòng 30 ngày</p>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service">
                    <i class="ti-lock"></i>
                    <h4>Thanh toán an toàn</h4>
                    <p>Thanh toán an toàn 100%</p>
                </div>
                <!-- End Single Service -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Service -->
                <div class="single-service">
                    <i class="ti-tag"></i>
                    <h4>Giá tốt nhất</h4>
                    <p>Giá đảm bảo</p>
                </div>
                <!-- End Single Service -->
            </div>
        </div>
    </div>
</section>
<!-- End Shop Services -->

<!-- Start Shop Newsletter  -->
<section class="shop-newsletter section">
    <div class="container">
        <div class="inner-top">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-12">
                    <!-- Start Newsletter Inner -->
                    <div class="inner">
                        <h4>Newsletter</h4>
                        <p> Subscribe to our newsletter and get <span>10%</span> off your first purchase</p>
                        <form action="mail/mail.php" method="get" target="_blank" class="newsletter-inner">
                            <input name="EMAIL" placeholder="Your email address" required="" type="email">
                            <button class="btn">Subscribe</button>
                        </form>
                    </div>
                    <!-- End Newsletter Inner -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Shop Newsletter -->
@endsection
@push('styles')
<style>
    li.shipping {
        display: inline-flex;
        width: 100%;
        font-size: 14px;
    }

    li.shipping .input-group-icon {
        width: 100%;
        margin-left: 10px;
    }

    .input-group-icon .icon {
        position: absolute;
        left: 20px;
        top: 0;
        line-height: 40px;
        z-index: 3;
    }

    .form-select {
        height: 30px;
        width: 100%;
    }

    .form-select .nice-select {
        border: none;
        border-radius: 0px;
        height: 40px;
        background: #f6f6f6 !important;
        padding-left: 45px;
        padding-right: 40px;
        width: 100%;
        white-space: normal;
        line-height: 1.4;
    }

    .list li {
        margin-bottom: 0 !important;
        white-space: normal;
        line-height: 1.3;
    }

    .list li:hover {
        background: #F7941D !important;
        color: white !important;
    }

    .form-select .nice-select::after {
        top: 14px;
    }

    .nice-select .current {
        display: block;
        white-space: normal;
        line-height: 1.3;
    }
</style>
@endpush
@push('scripts')
<script src="{{asset('frontend/js/nice-select/js/jquery.nice-select.min.js')}}"></script>
<script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("select.select2").select2();
        const $niceSelects = $('select.nice-select');
        if ($niceSelects.length) {
            $niceSelects.niceSelect();
        }
    });
</script>
<script>
    function showMe(box) {
        var checkbox = document.getElementById('shipping').style.display;
        // alert(checkbox);
        var vis = 'none';
        if (checkbox == "none") {
            vis = 'block';
        }
        if (checkbox == "block") {
            vis = "none";
        }
        document.getElementById(box).style.display = vis;
    }
</script>
<script>
    const formatCurrency = (value) => {
        const parsed = isNaN(value) ? 0 : Number(value);
        return new Intl.NumberFormat('vi-VN', {
            maximumFractionDigits: 0
        }).format(Math.round(parsed)) + ' VNĐ';
    };

    $(document).ready(function() {
        const $shippingSelect = $('#shipping_method');
        const $couponSelect = $('#coupon_select');
        const $shippingCostDisplay = $('#shipping_cost_display');
        const $couponDiscountDisplay = $('#coupon_discount_display');
        const $couponDiscountRow = $('.coupon_discount');
        const $orderTotal = $('#order_total_price');
        const $selectedMeta = $('#selected-shipping-meta');
        const subtotal = parseFloat($('.order_subtotal').data('price')) || 0;

        let couponDiscount = 0;
        let shippingCost = 0;

        const updateTotal = () => {
            const total = Math.max(0, subtotal + shippingCost - couponDiscount);
            $orderTotal.find('span').text(formatCurrency(total));
            $orderTotal.attr('data-base-total', total);
        };

        const applyCouponChange = () => {
            const $selected = $couponSelect.find('option:selected');

            if (!$selected.val()) {
                couponDiscount = 0;
                $couponDiscountRow.hide();
                updateTotal();
                return;
            }

            couponDiscount = parseFloat($selected.data('discount')) || 0;

            if (couponDiscount > 0) {
                $couponDiscountDisplay.text(formatCurrency(couponDiscount));
                $couponDiscountRow.show();
            } else {
                $couponDiscountRow.hide();
            }

            updateTotal();
        };

        const applyShippingChange = () => {
            const $selected = $shippingSelect.find('option:selected');

            if (!$selected.val()) {
                $shippingCostDisplay.attr('data-price', 0).text('0 VNĐ');
                $selectedMeta.text('');
                $orderTotal.find('span').text(formatCurrency(baseTotal));
                return;
            }

            const strategy = ($selected.data('strategy') || 'flat').toString();
            const percentage = parseFloat($selected.data('percentage')) || 0;
            const base = parseFloat($selected.data('base')) || 0;
            let cost = parseFloat($selected.data('price')) || 0;

            if (strategy === 'percentage') {
                cost = subtotal * (percentage / 100);
            } else if (strategy === 'mixed') {
                cost = base + (subtotal * (percentage / 100));
            }

            shippingCost = Math.max(0, cost);
            $shippingCostDisplay.attr('data-price', shippingCost.toFixed(2)).text(formatCurrency(shippingCost));
            updateTotal();

            const description = $selected.data('description');
            const estimated = $selected.data('estimated');
            const supportsCod = $selected.data('supports-cod');

            const metaParts = [];
            if (description) {
                metaParts.push(description);
            }

            const secondaryParts = [];
            if (estimated) {
                secondaryParts.push('Dự kiến: ' + estimated);
            }
            if (supportsCod === 1 || supportsCod === '1') {
                secondaryParts.push('Hỗ trợ COD');
            }

            if (secondaryParts.length) {
                metaParts.push(secondaryParts.join(' • '));
            }

            $selectedMeta.text(metaParts.join(' | '));
        };

        $couponSelect.on('change', applyCouponChange);
        $shippingSelect.on('change', applyShippingChange);

        if ($couponSelect.val()) {
            applyCouponChange();
        }
        if ($shippingSelect.val()) {
            applyShippingChange();
        }
    });
</script>

@endpush