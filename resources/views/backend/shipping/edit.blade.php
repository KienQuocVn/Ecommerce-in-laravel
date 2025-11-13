@extends('backend.layouts.master')

@section('main-content')

<div class="card">
  <h5 class="card-header">Chỉnh sửa Vận chuyển</h5>
  <div class="card-body">
    <form method="post" action="{{route('shipping.update',$shipping->id)}}">
      @csrf
      @method('PATCH')
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="code" class="col-form-label">Mã (tuỳ chọn)</label>
            <input id="code" type="text" name="code" value="{{old('code',$shipping->code)}}" class="form-control">
            @error('code')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="inputTitle" class="col-form-label">Loại <span class="text-danger">*</span></label>
            <input id="inputTitle" type="text" name="type" placeholder="Nhập tên gói vận chuyển" value="{{old('type',$shipping->type)}}" class="form-control">
            @error('type')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="service_level" class="col-form-label">Cấp độ dịch vụ</label>
            <input id="service_level" type="text" name="service_level" value="{{old('service_level',$shipping->service_level)}}" class="form-control">
            @error('service_level')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="delivery_zone" class="col-form-label">Khu vực giao</label>
            <input id="delivery_zone" type="text" name="delivery_zone" value="{{old('delivery_zone',$shipping->delivery_zone)}}" class="form-control">
            @error('delivery_zone')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="pricing_strategy" class="col-form-label">Chiến lược giá <span class="text-danger">*</span></label>
            <select name="pricing_strategy" id="pricing_strategy" class="form-control">
              @foreach($pricingStrategies as $value => $label)
                <option value="{{$value}}" {{old('pricing_strategy',$shipping->pricing_strategy) === $value ? 'selected' : ''}}>{{$label}}</option>
              @endforeach
            </select>
            @error('pricing_strategy')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="price" class="col-form-label">Phí cố định (VNĐ)</label>
            <input id="price" type="number" min="0" step="0.01" name="price" value="{{old('price',$shipping->price)}}" class="form-control">
            <small class="form-text text-muted">Áp dụng cho chiến lược Cố định/Kết hợp.</small>
            @error('price')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="percentage_rate" class="col-form-label">Tỷ lệ %</label>
            <input id="percentage_rate" type="number" min="0" max="100" step="0.01" name="percentage_rate" value="{{old('percentage_rate',$shipping->percentage_rate)}}" class="form-control">
            @error('percentage_rate')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="min_cart_total" class="col-form-label">Giá trị đơn tối thiểu (VNĐ)</label>
            <input id="min_cart_total" type="number" min="0" step="0.01" name="min_cart_total" value="{{old('min_cart_total',$shipping->min_cart_total)}}" class="form-control">
            @error('min_cart_total')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="max_cart_total" class="col-form-label">Giá trị đơn tối đa (VNĐ)</label>
            <input id="max_cart_total" type="number" min="0" step="0.01" name="max_cart_total" value="{{old('max_cart_total',$shipping->max_cart_total)}}" class="form-control">
            @error('max_cart_total')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="estimated_time" class="col-form-label">Thời gian dự kiến</label>
            <input id="estimated_time" type="text" name="estimated_time" value="{{old('estimated_time',$shipping->estimated_time)}}" class="form-control">
            @error('estimated_time')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="priority" class="col-form-label">Ưu tiên hiển thị</label>
            <input id="priority" type="number" min="0" max="255" name="priority" value="{{old('priority',$shipping->priority)}}" class="form-control">
            @error('priority')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="status" class="col-form-label">Trạng thái <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control">
              <option value="active" {{old('status',$shipping->status)=='active' ? 'selected' : ''}}>Active</option>
              <option value="inactive" {{old('status',$shipping->status)=='inactive' ? 'selected' : ''}}>Inactive</option>
            </select>
            @error('status')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="description" class="col-form-label">Ghi chú hiển thị</label>
        <textarea id="description" name="description" rows="3" class="form-control">{{old('description',$shipping->description)}}</textarea>
        @error('description')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="supports_cod" name="supports_cod" value="1" {{old('supports_cod',$shipping->supports_cod) ? 'checked' : ''}}>
        <label class="form-check-label" for="supports_cod">Hỗ trợ thu hộ COD</label>
      </div>
      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_recommended" name="is_recommended" value="1" {{old('is_recommended',$shipping->is_recommended) ? 'checked' : ''}}>
        <label class="form-check-label" for="is_recommended">Đề xuất cho khách hàng</label>
      </div>

      <div class="form-group mb-3">
        <button class="btn btn-success" type="submit">Cập nhật</button>
      </div>
    </form>
  </div>
</div>

@endsection