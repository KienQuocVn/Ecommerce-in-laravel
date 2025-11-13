@extends('shipper.layouts.master')

@section('main-content')
<div class="container-fluid">
  @include('user.layouts.notification')

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bảng điều khiển shipper</h1>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Đơn đang chờ</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['pending']}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đang thực hiện</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['in_progress']}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hoàn thành</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['completed']}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-check-circle fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn chờ nhận</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$stats['available_pool']}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-5 mb-4">
      <div class="card shadow">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Điểm tín nhiệm và đánh giá</h6>
        </div>
        <div class="card-body">
          <p class="mb-2">Điểm tín nhiệm hiện tại: <strong>{{number_format($shipper->trust_score, 2)}}</strong>/10</p>
          <p class="mb-2">Đánh giá trung bình: <strong>{{number_format($shipper->average_rating, 2)}}</strong>/5</p>
          <p class="mb-2">Đơn hoàn thành: <strong>{{$shipper->completed_deliveries}}</strong></p>
          <p class="mb-0">Đơn huỷ: <strong>{{$shipper->cancelled_deliveries}}</strong></p>
        </div>
      </div>
    </div>

    <div class="col-lg-7 mb-4">
      <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Đơn gần đây</h6>
          <a href="{{route('shipper.deliveries.index')}}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="card-body">
          @if($recentDeliveries->isEmpty())
            <p class="text-muted mb-0">Chưa có đơn giao nào gần đây.</p>
          @else
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentDeliveries as $delivery)
                    <tr>
                      <td>{{$delivery->order->order_number}}</td>
                      <td>{{$delivery->order->first_name}} {{$delivery->order->last_name}}</td>
                      <td><span class="badge badge-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'pending' ? 'secondary' : 'info') }}">{{$delivery->status}}</span></td>
                      <td>{{$delivery->updated_at->diffForHumans()}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
