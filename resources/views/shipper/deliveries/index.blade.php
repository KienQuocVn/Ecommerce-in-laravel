@extends('shipper.layouts.master')

@section('main-content')
<div class="container-fluid">
  @include('user.layouts.notification')

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Đơn giao hàng</h1>
  </div>

  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Đơn được giao cho bạn</h6>
        </div>
        <div class="card-body">
          @if($assignedDeliveries->isEmpty())
            <p class="text-muted mb-0">Bạn chưa nhận đơn giao nào. Hãy xem danh sách chờ để nhận thêm.</p>
          @else
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($assignedDeliveries as $delivery)
                    <tr>
                      <td>{{$delivery->order->order_number}}</td>
                      <td class="small">
                        {{$delivery->order->first_name}} {{$delivery->order->last_name}}<br>
                        <span class="text-muted">{{$delivery->order->phone}}</span>
                      </td>
                      <td>{{number_format($delivery->order->total_amount,0)}} VNĐ</td>
                      <td>
                        <span class="badge badge-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'pending' ? 'secondary' : ($delivery->status === 'accepted' ? 'info' : 'warning')) }}">{{$delivery->status}}</span>
                      </td>
                      <td class="text-nowrap">
                        @if(in_array($delivery->status, ['pending', 'accepted']))
                          <form action="{{route('shipper.deliveries.progress', $delivery)}}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary mb-1">Bắt đầu giao</button>
                          </form>
                        @endif
                        @if(in_array($delivery->status, ['accepted', 'in_transit']))
                          <form action="{{route('shipper.deliveries.complete', $delivery)}}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success mb-1">Hoàn tất</button>
                          </form>
                        @endif
                        @if($delivery->status !== 'completed')
                          <form action="{{route('shipper.deliveries.cancel', $delivery)}}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="reason" value="Không phù hợp lịch trình">
                            <button type="submit" class="btn btn-sm btn-outline-danger mb-1">Huỷ</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" class="small text-muted">
                        <strong>Địa chỉ:</strong> {{$delivery->order->address1}}
                        @if($delivery->order->delivery_charge)
                          | <strong>Phí giao:</strong> {{number_format($delivery->order->delivery_charge,0)}} VNĐ
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="pagination justify-content-end">{{$assignedDeliveries->links()}}</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-5 mb-4">
      <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Đơn chờ nhận</h6>
        </div>
        <div class="card-body">
          @if($availableDeliveries->isEmpty())
            <p class="text-muted mb-0">Không có đơn chờ xử lý. Vui lòng quay lại sau.</p>
          @else
            <div class="table-responsive">
              <table class="table table-sm table-borderless">
                <tbody>
                  @foreach($availableDeliveries as $delivery)
                    <tr class="border-bottom">
                      <td>
                        <div class="font-weight-bold">{{$delivery->order->order_number}}</div>
                        <div class="small text-muted">
                          {{$delivery->order->first_name}} {{$delivery->order->last_name}} • {{number_format($delivery->order->total_amount,0)}} VNĐ
                        </div>
                        <div class="small text-muted">{{$delivery->order->address1}}</div>
                      </td>
                      <td class="text-right align-middle">
                        <form action="{{route('shipper.deliveries.accept', $delivery)}}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-primary">Nhận đơn</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="pagination justify-content-end">{{$availableDeliveries->links('pagination::bootstrap-4')}}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
