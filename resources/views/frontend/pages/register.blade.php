@extends('frontend.layouts.master')

@section('title','SHOPFY || Trang đăng ký')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Trang chủ<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Đăng ký</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Shop Login -->
<section class="shop login section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-12">
                <div class="login-form">
                    <h2>Đăng ký</h2>
                    <p>Vui lòng đăng ký để thanh toán nhanh hơn</p>
                    <!-- Form -->
                    <form class="form" method="post" action="{{route('register.submit')}}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tên<span>*</span></label>
                                    <input type="text" name="name" placeholder="" required="required" value="{{old('name')}}">
                                    @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Email<span>*</span></label>
                                    <input type="text" name="email" placeholder="" required="required" value="{{old('email')}}">
                                    @error('email')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Mật khẩu<span>*</span></label>
                                    <input type="password" name="password" placeholder="" required="required" value="{{old('password')}}">
                                    @error('password')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu<span>*</span></label>
                                    <input type="password" name="password_confirmation" placeholder="" required="required" value="{{old('password_confirmation')}}">
                                    @error('password_confirmation')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group login-btn">
                                    <button class="btn btn-full-width" type="submit">Đăng ký</button>
                                    <!-- <a href="{{route('login.form')}}" class="btn">Đăng nhập</a> -->
                                    <p class="or-separator fw-bold">OR</p>
                                    <div class="social-login-buttons"> 
                                        <a href="{{route('login.redirect','facebook')}}" class="social-login-btn facebook">
                                            <img src="{{ asset('storage/photos/1/facebook.png') }}" alt="Facebook" class="social-icon">
                                            <span>Facebook</span>
                                        </a>
                                        <a href="{{route('login.redirect','google')}}" class="social-login-btn google">
                                            <img src="{{ asset('storage/photos/1/google.png') }}" alt="Google" class="social-icon"> 
                                            <span>Google</span>
                                        </a>
                                        
                                    </div>
                                </div>
                                Bạn đã có tài khoản?
                                <a href="{{route('login.form')}}" class="fw-bold" style="font-weight: bold;">Đăng nhập</a>
                            </div>
                        </div>
                    </form>
                    <!--/ End Form -->
                </div>
            </div>
        </div>
    </div>
</section>
<!--/ End Login -->
@endsection

@push('styles')
<style>
    .shop.login .form .btn {
        margin-right: 0;
        margin-bottom: 10px; 
    }

    .social-login-buttons {
        display: flex; 
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap; 
        margin-top: 15px; 
        margin-bottom: 20px; 
    }

    .social-login-btn {
        display: flex; 
        align-items: center; 
        padding: 8px 15px;
        border: 1px solid #ccc; 
        border-radius: 5px;
        background-color: #fff; 
        color: #333; 
        text-decoration: none; 
        font-size: 14px; 
        transition: all 0.3s ease; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
    }

    .social-login-btn:hover {
        background-color: #f8f8f8; 
        border-color: #999; 
        color: #000;
    }

    .social-login-btn .social-icon {
        height: 20px; 
        width: 20px;
        margin-right: 8px; 
        vertical-align: middle; 
    }

    .btn-full-width {
        width: 100%;
        margin: 0 !important; 
        border-radius: 0;
    }

    .or-separator {
        text-align: center;
        font-weight: bold; 
        margin: 15px 0; 
        font-size: 14px;
    }
</style>
@endpush