@extends('frontend.layouts.master')

@section('title', 'Kết quả thanh toán')

@section('main-content')
<div class="container" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mt-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="mb-4">Kết quả thanh toán</h2>
                    
                    @if($status === 'succeeded')
                        <div class="alert alert-success">
                            <h4 class="mt-3">Thanh toán thành công!</h4>
                            <p class="mb-0">Đơn hàng của bạn đã được thanh toán qua <strong>{{ strtoupper($provider) }}</strong></p>
                        </div>
                    @elseif($status === 'processing')
                        <div class="alert alert-info">
                            <i class="fas fa-clock fa-4x mb-3 text-info"></i>
                            <h4 class="mt-3">Đang xử lý thanh toán</h4>
                            <p class="mb-0">Giao dịch của bạn đang được xác nhận. Vui lòng chờ trong giây lát...</p>
                        </div>
                    @elseif($status === 'canceled')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
                            <h4 class="mt-3">Thanh toán đã bị hủy</h4>
                            <p class="mb-0">Bạn đã hủy giao dịch thanh toán</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle fa-4x mb-3 text-danger"></i>
                            <h4 class="mt-3">Thanh toán thất bại</h4>
                            <p class="mb-0">{{ $message ?? 'Có lỗi xảy ra trong quá trình thanh toán' }}</p>
                        </div>
                    @endif

                    @if($order)
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5 class="mb-3">Thông tin đơn hàng</h5>
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <p class="mb-2"><strong>Mã đơn hàng:</strong></p>
                                    <p class="mb-2"><strong>Tổng tiền:</strong></p>
                                    <p class="mb-2"><strong>Trạng thái:</strong></p>
                                </div>
                                <div class="col-md-6 text-left">
                                    <p class="mb-2">{{ $order->order_number }}</p>
                                    <p class="mb-2">{{ number_format($order->total_amount, 0) }} VNĐ</p>
                                    <p class="mb-2">
                                        @if($status === 'succeeded')
                                            <span class="badge badge-success">Đã thanh toán</span>
                                        @elseif($status === 'processing')
                                            <span class="badge badge-info">Đang xử lý</span>
                                        @elseif($status === 'canceled')
                                            <span class="badge badge-warning">Đã hủy</span>
                                        @else
                                            <span class="badge badge-danger">Thất bại</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-lg mr-2 text-white">
                             Về trang chủ
                        </a>
                        @if(in_array($status, ['succeeded', 'processing']) && $order)
                            <a href="{{ route('user.order.show', $order->id) }}" class="btn text-white btn-lg">
                                 Xem đơn hàng
                            </a>
                        @elseif(in_array($status, ['canceled', 'failed']))
                            <a href="{{ route('checkout') }}" class="btn text-white btn-lg">
                                 Thử lại
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection