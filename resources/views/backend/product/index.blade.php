@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Product Lists</h6>
      <a href="{{route('product.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Product</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($products)>0)
        <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Loại</th>
              <th>Được giới thiệu</th>
              <th>Giá</th>
              <th>Giảm giá</th>
              <th>Kích cỡ</th>
              <th>Tình trạng</th>
              <th>Thương hiệu</th>
              <th>Tồn kho</th>
              <th>Hình ảnh</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Loại</th>
              <th>Được giới thiệu</th>
              <th>Giá</th>
              <th>Giảm giá</th>
              <th>Kích cỡ</th>
              <th>Tình trạng</th>
              <th>Thương hiệu</th>
              <th>Tồn kho</th>
              <th>Hình ảnh</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </tfoot>
          <tbody>

            @foreach($products as $product)
              @php
              $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
              // dd($sub_cat_info);
              $brands=DB::table('brands')->select('title')->where('id',$product->brand_id)->get();
              @endphp
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->title}}</td>
                    <td>{{$product->cat_info['title']}}
                      <sub>
                          {{$product->sub_cat_info->title ?? ''}}
                      </sub>
                    </td>
                    <td>{{(($product->is_featured==1)? 'Yes': 'No')}}</td>
                    <td>Rs. {{$product->price}} /-</td>
                    <td>  {{$product->discount}}% OFF</td>
                    <td>{{$product->size}}</td>
                    <td>{{$product->condition}}</td>
                    <td> {{ucfirst($product->brand->title)}}</td>
                    <td>
                      @if($product->stock>0)
                      <span class="badge badge-primary">{{$product->stock}}</span>
                      @else
                      <span class="badge badge-danger">{{$product->stock}}</span>
                      @endif
                    </td>
                    <td>
                        @if($product->photo)
                            @php
                              $photo=explode(',',$product->photo);
                              // dd($photo);
                            @endphp
                            <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px" alt="{{$product->photo}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                        @endif
                    </td>
                    <td>
                        @if($product->status=='active')
                            <span class="badge badge-success">{{$product->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$product->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                      @csrf
                      @method('delete')
                          <button class="btn btn-danger btn-sm dltBtn" data-id={{$product->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$products->links()}}</span>
        @else
          <h6 class="text-center">No Products found!!! Please create Product</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert2.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate {
          display: none;
      }
      .zoom {
        transition: transform .2s; /* Animation */
      }
      .zoom:hover {
        transform: scale(5);
      }
  </style>
@endpush

@push('scripts')
  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert2.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>
      $(document).ready(function() {
          // Khởi tạo DataTable
          $('#product-dataTable').DataTable({
              scrollX: false,
              columnDefs: [
                  {
                      orderable: false,
                      targets: [10, 11, 12]
                  }
              ]
          });

          // Cấu hình CSRF token cho AJAX
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          // Xử lý sự kiện xóa sản phẩm
          $('.dltBtn').click(function(e) {
              e.preventDefault();
              var form = $(this).closest('form');
              var dataID = $(this).data('id');

              Swal.fire({
                  title: "Bạn có chắc chắn?",
                  text: "Dữ liệu đã xóa sẽ không thể khôi phục!",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Có, xóa nó!",
                  cancelButtonText: "Hủy"
              }).then((result) => {
                  if (result.isConfirmed) {
                      form.submit();
                      Swal.fire(
                          "Đã xóa!",
                          "Sản phẩm đã được xóa thành công.",
                          "success"
                      );
                  } else {
                      Swal.fire(
                          "Đã hủy!",
                          "Dữ liệu của bạn vẫn an toàn.",
                          "info"
                      );
                  }
              });
          });
      });

      // Hàm deleteData
      function deleteData(id) {
          var form = $(`form[action*="${id}"]`);
          Swal.fire({
              title: "Bạn có chắc chắn?",
              text: "Dữ liệu đã xóa sẽ không thể khôi phục!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Có, xóa nó!",
              cancelButtonText: "Hủy"
          }).then((result) => {
              if (result.isConfirmed) {
                  form.submit();
                  Swal.fire(
                      "Đã xóa!",
                      "Sản phẩm đã được xóa thành công.",
                      "success"
                  );
              } else {
                  Swal.fire(
                      "Đã hủy!",
                      "Dữ liệu của bạn vẫn an toàn.",
                      "info"
                  );
              }
          });
      }
  </script>
@endpush