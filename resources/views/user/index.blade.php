@extends('user.layouts.master')

@section('main-content')
<div class="container-fluid">
  @include('user.layouts.notification')
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bảng điều khiển khách hàng</h1>
    <a href="{{route('user.order.index')}}" class="btn btn-sm btn-primary"><i class="fas fa-receipt mr-1"></i> Xem tất cả đơn hàng</a>
  </div>

  @php
    $tiers = config('loyalty.tiers');
    $currentTierKey = $tierMeta['key'] ?? 'bronze';
    $tierKeys = array_keys($tiers);
    $currentIndex = array_search($currentTierKey, $tierKeys);
    $nextTierKey = ($currentIndex !== false && isset($tierKeys[$currentIndex + 1])) ? $tierKeys[$currentIndex + 1] : null;
    $nextTier = $nextTierKey ? $tiers[$nextTierKey] + ['key' => $nextTierKey] : null;
    $remainingOrders = $nextTier ? max(0, ($nextTier['min_orders'] ?? 0) - ($user->total_orders ?? 0)) : 0;
    $remainingSpent = $nextTier ? max(0, ($nextTier['min_spent'] ?? 0) - ($user->total_spent ?? 0)) : 0;
  @endphp

  <div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Hạng thành viên</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$tierMeta['name'] ?? 'Bronze'}}</div>
              <p class="mt-2 mb-0 small text-muted">{{$tierMeta['benefits'] ?? 'Ưu đãi thành viên mới'}}</p>
              @if($nextTier)
                <p class="small mb-0 mt-2">Cần thêm <strong>{{max(0,$remainingOrders)}}</strong> đơn hoặc <strong>{{number_format(max(0,$remainingSpent),0)}} VNĐ</strong> để lên hạng <strong>{{$nextTier['name']}}</strong>.</p>
              @else
                <p class="small mb-0 mt-2 text-success"><i class="fas fa-crown mr-1"></i>Bạn đang ở hạng cao nhất!</p>
              @endif
            </div>
            <div class="col-auto">
              <i class="fas fa-medal fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng chi tiêu</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($user->total_spent ?? 0,0)}} VNĐ</div>
              <p class="small text-muted mb-0 mt-2">Đã hoàn thành {{$user->total_orders ?? 0}} đơn hàng.</p>
              @if($user->last_order_at)
                <p class="small text-muted mb-0">Đơn gần nhất: {{\Carbon\Carbon::parse($user->last_order_at)->format('d/m/Y')}}</p>
              @endif
            </div>
            <div class="col-auto">
              <i class="fas fa-coins fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-12 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Điểm tích luỹ</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$user->loyalty_points ?? 0}} điểm</div>
              <p class="small text-muted mt-2 mb-0">Sử dụng điểm để đổi quà tặng hoặc voucher giảm giá trong các chương trình ưu đãi.</p>
            </div>
            <div class="col-auto">
              <i class="fas fa-gift fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Đơn hàng đang xử lý</h6>
          <a href="{{route('user.order.index')}}" class="small">Xem tất cả</a>
        </div>
        <div class="card-body p-0">
          @if($activeOrders->isEmpty())
            <p class="text-muted text-center my-4">Hiện tại bạn chưa có đơn hàng nào đang được xử lý.</p>
          @else
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>Mã</th>
                    <th>Ngày</th>
                    <th>Trạng thái</th>
                    <th>Giao hàng</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($activeOrders as $order)
                  @php
                    $deliveryStatusMap = [
                      'pending' => ['Chờ nhận', 'secondary'],
                      'accepted' => ['Đã nhận', 'info'],
                      'in_transit' => ['Đang giao', 'warning'],
                      'completed' => ['Hoàn thành', 'success'],
                      'cancelled' => ['Huỷ', 'danger'],
                    ];
                    $deliveryMeta = $order->delivery ? ($deliveryStatusMap[$order->delivery->status] ?? [$order->delivery->status, 'secondary']) : null;
                  @endphp
                  <tr>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->created_at->format('d/m/Y')}}</td>
                    <td><span class="badge badge-info text-uppercase">{{$order->status}}</span></td>
                    <td>
                      @if($deliveryMeta)
                        <span class="badge badge-{{$deliveryMeta[1]}}">{{$deliveryMeta[0]}}</span>
                      @else
                        <span class="badge badge-light text-dark">Đang điều phối</span>
                      @endif
                    </td>
                    <td class="text-right"><a href="{{route('user.order.show',$order->id)}}" class="btn btn-sm btn-outline-primary">Chi tiết</a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-success">Lịch sử đơn hàng đã hoàn thành</h6>
          <a href="{{route('user.order.index')}}" class="small">Xem chi tiết</a>
        </div>
        <div class="card-body p-0">
          @if($recentCompletedOrders->isEmpty())
            <p class="text-muted text-center my-4">Bạn chưa có đơn hàng nào được giao thành công.</p>
          @else
            <div class="list-group list-group-flush">
              @foreach($recentCompletedOrders as $order)
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1">{{$order->order_number}}</h6>
                    <p class="mb-1 small text-muted">Tổng cộng: {{number_format($order->total_amount,0)}} VNĐ</p>
                    <p class="mb-0 small text-muted">Giao ngày: {{$order->updated_at->format('d/m/Y H:i')}}</p>
                  </div>
                  <a href="{{route('user.order.show',$order->id)}}" class="btn btn-sm btn-outline-success">Đánh giá</a>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Gợi ý dành riêng cho bạn</h6>
      <a href="{{route('product-grids')}}" class="small">Khám phá thêm</a>
    </div>
    <div class="card-body">
      @if($recommendedProducts->isEmpty())
        <p class="text-muted mb-0">Chúng tôi đang cập nhật gợi ý cho bạn. Hãy tiếp tục mua sắm để nhận các đề xuất phù hợp hơn.</p>
      @else
        <div class="row">
          @foreach($recommendedProducts as $product)
          @php
            $photos = $product->photo ? explode(',', $product->photo) : [];
            $photo = count($photos) ? $photos[0] : asset('frontend/img/no-image.png');
          @endphp
          <div class="col-xl-2 col-md-3 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
              <a href="{{route('product-detail',$product->slug)}}" target="_blank" class="text-decoration-none text-dark">
                <img src="{{$photo}}" class="card-img-top" alt="{{$product->title}}">
                <div class="card-body p-3">
                  <h6 class="card-title mb-1">{{$product->title}}</h6>
                  <p class="card-text text-primary font-weight-bold mb-0">{{number_format($product->price,0)}} VNĐ</p>
                </div>
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  const url = "{{route('product.order.income')}}";

  // Set new default font family and font color to mimic Bootstrap's default styling
  Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#858796';

  function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
      };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  }

  // Area Chart Example
  var ctx = document.getElementById("myAreaChart");

  axios.get(url)
    .then(function(response) {
      const data_keys = Object.keys(response.data);
      const data_values = Object.values(response.data);


      var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data_keys, //["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
            label: "Earnings",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: data_values, //[0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 44660],
          }],
        },
        options: {
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 10,
              right: 25,
              top: 25,
              bottom: 0
            }
          },
          scales: {
            xAxes: [{
              time: {
                unit: 'date'
              },
              gridLines: {
                display: false,
                drawBorder: false
              },
              ticks: {
                maxTicksLimit: 7
              }
            }],
            yAxes: [{
              ticks: {
                maxTicksLimit: 5,
                padding: 10,
                // Include a dollar sign in the ticks
                callback: function(value, index, values) {
                  return '$' + number_format(value);
                }
              },
              gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2]
              }
            }],
          },
          legend: {
            display: false
          },
          tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
              label: function(tooltipItem, chart) {
                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
              }
            }
          }
        }
      });











    })
    .catch(function(error) {
      //   vm.answer = 'Error! Could not reach the API. ' + error
      console.log(error)
    });
</script>
@endpush