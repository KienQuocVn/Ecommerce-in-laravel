@extends('frontend.layouts.master')

@section('title','SHOPFY || Trang theo dõi đơn hàng')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Trang chủ<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Theo dõi đơn hàng</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->
<section class="tracking_box_area section_gap py-5">
    <div class="container">
        <div class="tracking_box_inner">
            <p>Để theo dõi đơn hàng, vui lòng nhập ID đơn hàng vào ô bên dưới và nhấn nút "Theo dõi". Mã này đã được cung cấp cho bạn trên biên lai và trong email xác nhận mà bạn đã nhận được.</p>
            <form class="row tracking_form my-4" action="{{route('product.track.order')}}" method="post" novalidate="novalidate">
                @csrf
                <div class="col-md-8 form-group">
                    <input type="text" class="form-control p-2" name="order_number" placeholder="Nhập số đơn hàng của bạn" value="{{old('order_number', $code ?? '')}}">
                </div>
                <div class="col-md-8 form-group">
                    <button type="submit" value="submit" class="btn submit_btn">Theo dõi đơn hàng</button>
                </div>
            </form>
        </div>

        @if(isset($code) && !$order)
            <div class="alert alert-danger">Không tìm thấy đơn hàng với mã <strong>{{$code}}</strong>. Vui lòng kiểm tra lại.</div>
        @endif

        @if($order)
            @php
                $statusMap = [
                    'new' => ['Đơn hàng mới', 'primary'],
                    'process' => ['Đang xử lý', 'warning'],
                    'progress' => ['Đang chuẩn bị', 'info'],
                    'delivered' => ['Đã giao hàng', 'success'],
                    'cancel' => ['Đã huỷ', 'danger'],
                ];
                $deliveryStatusMap = [
                    'pending' => ['Chờ nhận đơn', 'secondary'],
                    'accepted' => ['Shipper đã nhận', 'info'],
                    'in_transit' => ['Đang vận chuyển', 'warning'],
                    'completed' => ['Đã giao thành công', 'success'],
                    'cancelled' => ['Shipper đã huỷ', 'danger'],
                ];
                $orderStatusMeta = $statusMap[$order->status] ?? [$order->status, 'secondary'];
                $delivery = $order->delivery;
                $deliveryMeta = $delivery ? ($deliveryStatusMap[$delivery->status] ?? [$delivery->status, 'secondary']) : null;
            @endphp
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
                        <div>
                            <h5 class="font-weight-bold mb-1">Mã đơn hàng: {{$order->order_number}}</h5>
                            <p class="mb-0 text-muted">Ngày tạo: {{$order->created_at->format('d/m/Y H:i')}}</p>
                        </div>
                        <span class="badge badge-{{$orderStatusMeta[1]}} px-3 py-2">{{$orderStatusMeta[0]}}</span>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Thông tin thanh toán</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><strong>Tổng tiền:</strong> {{number_format($order->total_amount,0)}} VNĐ</li>
                                <li><strong>Phí vận chuyển:</strong> {{number_format($order->delivery_charge ?? 0,0)}} VNĐ</li>
                                <li><strong>Phương thức:</strong> {{strtoupper($order->payment_method)}}</li>
                                <li><strong>Trạng thái thanh toán:</strong> {{$order->payment_status}}</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Thông tin giao hàng</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><strong>Người nhận:</strong> {{$order->first_name}} {{$order->last_name}}</li>
                                <li><strong>Điện thoại:</strong> {{$order->phone}}</li>
                                <li><strong>Địa chỉ:</strong> {{$order->address1}}, {{$order->address2}}, {{$order->country}}</li>
                                @if($order->shipping)
                                <li><strong>Dịch vụ:</strong> {{$order->shipping->type}}</li>
                                @endif
                                <li><strong>Mã vận đơn:</strong> {{$order->order_number}}</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Trạng thái giao hàng</h6>
                            @if($delivery)
                                <p class="mb-1"><span class="badge badge-{{$deliveryMeta[1]}}">{{$deliveryMeta[0]}}</span></p>
                                <ul class="list-unstyled small mb-0">
                                    <li><strong>Shipper:</strong> {{$delivery->shipper?->user?->name ?? 'Đang điều phối'}}</li>
                                    <li><strong>Liên hệ:</strong> {{$delivery->shipper?->phone ?? 'Đang cập nhật'}}</li>
                                    <li><strong>Điểm tín nhiệm:</strong> {{number_format($delivery->shipper?->trust_score ?? 0,2)}} / 10</li>
                                    @if($delivery->completed_at)
                                        <li><strong>Hoàn tất:</strong> {{$delivery->completed_at->format('d/m/Y H:i')}}</li>
                                    @elseif($delivery->picked_up_at)
                                        <li><strong>Lấy hàng:</strong> {{$delivery->picked_up_at->format('d/m/Y H:i')}}</li>
                                    @elseif($delivery->accepted_at)
                                        <li><strong>Nhận đơn:</strong> {{$delivery->accepted_at->format('d/m/Y H:i')}}</li>
                                    @endif
                                </ul>
                            @else
                                <p class="text-muted small mb-0">Đơn hàng đang chờ điều phối shipper.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0 font-weight-bold">Sản phẩm trong đơn</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->cart_info as $item)
                                <tr>
                                    <td>{{$item->product->title ?? 'Sản phẩm đã xoá'}}</td>
                                    <td>{{$item->quantity}}</td>
                                    <td>{{number_format($item->price,0)}} VNĐ</td>
                                    <td>{{number_format($item->amount,0)}} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection