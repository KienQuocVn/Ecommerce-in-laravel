@extends('user.layouts.master')

@section('title','Order Detail')

@section('main-content')
<div class="card">
  <h5 class="card-header">Order <a href="{{route('order.pdf',$order->id)}}" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Tạo PDF</a>
  </h5>
  <div class="card-body">
    @if($order)
    @php
      $delivery = $order->delivery;
      $shipper = $delivery?->shipper;
      $existingReview = ($delivery && $delivery->reviews) ? $delivery->reviews->firstWhere('customer_id', auth()->id()) : null;
    @endphp
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Số đơn hàng</th>
          <th>Tên</th>
          <th>Email</th>
          <th>Số lượng</th>
          <th>Phí vận chuyển</th>
          <th>Tổng số tiền</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{$order->id}}</td>
          <td>{{$order->order_number}}</td>
          <td>{{$order->first_name}} {{$order->last_name}}</td>
          <td>{{$order->email}}</td>
          <td>{{$order->quantity}}</td>
          <td>{{number_format($order->delivery_charge ?? 0,0)}} VNĐ</td>
          <td>{{number_format($order->total_amount,0)}} VNĐ</td>
          <td>
            @if($order->status=='new')
            <span class="badge badge-primary">{{$order->status}}</span>
            @elseif($order->status=='process')
            <span class="badge badge-warning">{{$order->status}}</span>
            @elseif($order->status=='delivered')
            <span class="badge badge-success">{{$order->status}}</span>
            @else
            <span class="badge badge-danger">{{$order->status}}</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{route('order.destroy',[$order->id])}}">
              @csrf
              @method('delete')
              <button class="btn btn-danger btn-sm dltBtn" data-id="{{$order->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
            </form>
          </td>

        </tr>
      </tbody>
    </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-4">
            <div class="order-info">
              <h4 class="text-center pb-4">THÔNG TIN ĐẶT HÀNG</h4>
              <table class="table">
                <tr class="">
                  <td>Số đơn hàng</td>
                  <td> : {{$order->order_number}}</td>
                </tr>
                <tr>
                  <td>Ngày đặt hàng</td>
                  <td> : {{$order->created_at->format('D d M, Y')}} at {{$order->created_at->format('g : i a')}} </td>
                </tr>
                <tr>
                  <td>Số lượng</td>
                  <td> : {{$order->quantity}}</td>
                </tr>
                <tr>
                  <td>Trạng thái đơn hàng</td>
                  <td> : {{$order->status}}</td>
                </tr>
                <tr>
                  <td>Phí vận chuyển</td>
                  <td> : {{number_format($order->delivery_charge ?? 0,0)}} VNĐ</td>
                </tr>
                @if($order->shipping)
                <tr>
                  <td>Phương thức vận chuyển</td>
                  <td> : {{$order->shipping->type}}</td>
                </tr>
                @endif
                <tr>
                  <td>Tổng số tiền</td>
                  <td> : {{number_format($order->total_amount,0)}} VNĐ</td>
                </tr>
                <tr>
                  <td>Phương thức thanh toán</td>
                  <td> : @if($order->payment_method=='cod') Cash on Delivery @else Paypal @endif</td>
                </tr>
                <tr>
                  <td>Trạng thái thanh toán</td>
                  <td> : {{$order->payment_status}}</td>
                </tr>
              </table>
            </div>
          </div>

          <div class="col-lg-6 col-lx-4">
            <div class="shipping-info">
              <h4 class="text-center pb-4">THÔNG TIN VẬN CHUYỂN</h4>
              <table class="table">
                <tr class="">
                  <td>Họ và tên đầy đủ</td>
                  <td> : {{$order->first_name}} {{$order->last_name}}</td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td> : {{$order->email}}</td>
                </tr>
                <tr>
                  <td>Số điện thoại</td>
                  <td> : {{$order->phone}}</td>
                </tr>
                <tr>
                  <td>Địa chỉ</td>
                  <td> : {{$order->address1}}, {{$order->address2}}</td>
                </tr>
                <tr>
                  <td>Quốc gia</td>
                  <td> : {{$order->country}}</td>
                </tr>
                <tr>
                  <td>Mã bưu chính</td>
                  <td> : {{$order->post_code}}</td>
                </tr>
              </table>
            </div>
          </div>

          @if($delivery)
          <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                @php
                  $statusMap = [
                    'pending' => ['Chờ nhận', 'secondary'],
                    'accepted' => ['Đã nhận đơn', 'info'],
                    'in_transit' => ['Đang giao', 'warning'],
                    'completed' => ['Hoàn thành', 'success'],
                    'cancelled' => ['Đã huỷ', 'danger'],
                  ];
                  $statusMeta = $statusMap[$delivery->status] ?? [$delivery->status, 'secondary'];
                @endphp
                <div class="row align-items-start">
                  <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="font-weight-bold mb-3">Thông tin shipper</h5>
                    <p class="mb-1"><strong>Họ tên:</strong> {{$shipper?->user?->name ?? 'Đang cập nhật'}}</p>
                    <p class="mb-1"><strong>Liên hệ:</strong> {{$shipper?->phone ?? 'Đang cập nhật'}}</p>
                    <p class="mb-1"><strong>Phương tiện:</strong> {{$shipper?->vehicle_type ?? 'Không rõ'}}</p>
                    <p class="mb-1"><strong>Điểm tín nhiệm:</strong> {{number_format($shipper?->trust_score ?? 0,2)}} / 10</p>
                    <p class="mb-1"><strong>Trạng thái giao:</strong> <span class="badge badge-{{$statusMeta[1]}}">{{$statusMeta[0]}}</span></p>
                    <p class="mb-0"><strong>Phí giao hàng:</strong> {{number_format($delivery->delivery_fee ?? $order->delivery_charge ?? 0,0)}} VNĐ</p>
                  </div>
                  <div class="col-md-8">
                    <h5 class="font-weight-bold mb-3">Đánh giá &amp; tip cho shipper</h5>
                    @if($delivery->status === 'completed')
                      <form method="POST" action="{{route('user.delivery.review', $delivery->id)}}">
                        @csrf
                        <div class="form-row">
                          <div class="form-group col-md-4">
                            <label for="rating">Đánh giá (1-5)</label>
                            <select name="rating" id="rating" class="form-control">
                              @for($r = 1; $r <= 5; $r++)
                                <option value="{{$r}}" {{old('rating', $existingReview->rating ?? 5) == $r ? 'selected' : ''}}>{{$r}}</option>
                              @endfor
                            </select>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="tip_amount">Tip cho shipper (VNĐ)</label>
                            <input type="number" min="0" step="1000" name="tip_amount" id="tip_amount" class="form-control" value="{{old('tip_amount', $existingReview->tip_amount ?? $delivery->tip_amount ?? 0)}}">
                          </div>
                          <div class="form-group col-md-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                              <input type="checkbox" name="is_liked" id="is_liked" value="1" class="form-check-input" {{ old('is_liked', optional($existingReview)->is_liked) ? 'checked' : '' }}>
                              <label class="form-check-label" for="is_liked">Tôi thích dịch vụ này</label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="comment">Nhận xét thêm</label>
                          <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Chia sẻ trải nghiệm của bạn...">{{old('comment', $existingReview->comment ?? '')}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $existingReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}</button>
                      </form>
                    @else
                      <p class="text-muted mb-0">Bạn có thể đánh giá shipper sau khi đơn hàng được giao thành công.</p>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </section>
    @endif

  </div>
</div>
@endsection

@push('styles')
<style>
  .order-info,
  .shipping-info {
    background: #ECECEC;
    padding: 20px;
  }

  .order-info h4,
  .shipping-info h4 {
    text-decoration: underline;
  }
</style>
@endpush