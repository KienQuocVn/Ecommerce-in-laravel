@extends('user.layouts.master')

@section('title','Hồ sơ quản trị')

@section('main-content')

<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
        <h4 class=" font-weight-bold">Hồ sơ</h4>
        <ul class="breadcrumbs">
            <li><a href="{{route('admin')}}" style="color:#999">Bảng điều khiển</a></li>
            <li><a href="" class="active text-primary">Trang hồ sơ</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="image">
                        @if($profile->photo)
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:80px;width:80px;margin:auto;" src="{{$profile->photo}}" alt="profile picture">
                        @else
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:80px;width:80px;margin:auto;" src="{{asset('backend/img/avatar.png')}}" alt="profile picture">
                        @endif
                    </div>
                    <div class="card-body mt-4 ml-2">
                        <h5 class="card-title text-left"><small><i class="fas fa-user"></i> {{$profile->name}}</small></h5>
                        <p class="card-text text-left"><small><i class="fas fa-envelope"></i> {{$profile->email}}</small></p>
                        <p class="card-text text-left"><small class="text-muted"><i class="fas fa-hammer"></i> {{$profile->role}}</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <form class="border px-4 pt-2 pb-3" method="POST" action="{{route('user-profile-update',$profile->id)}}">
                    @csrf
                    <div class="form-group">
                        <label for="inputFirstName" class="col-form-label">Tên <span class="text-danger">*</span></label>
                        <input id="inputFirstName" type="text" name="first_name" placeholder="Nhập tên" value="{{$profile->first_name}}" class="form-control" required>
                        @error('first_name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputLastName" class="col-form-label">Họ <span class="text-danger">*</span></label>
                        <input id="inputLastName" type="text" name="last_name" placeholder="Nhập họ" value="{{$profile->last_name}}" class="form-control" required>
                        @error('last_name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputDisplayName" class="col-form-label">Tên hiển thị</label>
                        <input id="inputDisplayName" type="text" name="name" placeholder="Tên hiển thị" value="{{$profile->name}}" class="form-control">
                        @error('name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputEmail" class="col-form-label">Email</label>
                        <input id="inputEmail" readonly type="email" name="email" placeholder="Nhập email" value="{{$profile->email}}" class="form-control">
                        @error('email')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputPhone" class="col-form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input id="inputPhone" type="text" name="phone" placeholder="Nhập số điện thoại" value="{{$profile->phone}}" class="form-control" required>
                        @error('phone')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputAddress1" class="col-form-label">Địa chỉ (dòng 1) <span class="text-danger">*</span></label>
                        <input id="inputAddress1" type="text" name="address_line1" placeholder="Nhập địa chỉ" value="{{$profile->address_line1}}" class="form-control" required>
                        @error('address_line1')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputAddress2" class="col-form-label">Địa chỉ (dòng 2)</label>
                        <input id="inputAddress2" type="text" name="address_line2" placeholder="Căn hộ, tầng..." value="{{$profile->address_line2}}" class="form-control">
                        @error('address_line2')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputCountry" class="col-form-label">Quốc gia</label>
                        <input id="inputCountry" type="text" name="country" placeholder="Quốc gia" value="{{$profile->country}}" class="form-control">
                        @error('country')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputPostal" class="col-form-label">Mã bưu chính</label>
                        <input id="inputPostal" type="text" name="post_code" placeholder="Mã bưu chính" value="{{$profile->post_code}}" class="form-control">
                        @error('post_code')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="inputPhoto" class="col-form-label">Hình ảnh</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                    <i class="fa fa-picture-o"></i> Chọn
                                </a>
                            </span>
                            <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$profile->photo}}">
                        </div>
                        @error('photo')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="role" class="col-form-label">Vai trò</label>
                        <select name="role" class="form-control">
                            <option value="">-----Select Role-----</option>
                            <option value="admin" {{(($profile->role=='admin')? 'selected' : '')}}>Admin</option>
                            <option value="user" {{(($profile->role=='user')? 'selected' : '')}}>User</option>
                            <option value="shipper" {{(($profile->role=='shipper')? 'selected' : '')}}>Shipper</option>
                        </select>
                        @error('role')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-sm">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .breadcrumbs {
        list-style: none;
    }

    .breadcrumbs li {
        float: left;
        margin-right: 10px;
    }

    .breadcrumbs li a:hover {
        text-decoration: none;
    }

    .breadcrumbs li .active {
        color: red;
    }

    .breadcrumbs li+li:before {
        content: "/\00a0";
    }

    .image {
        background: url('/backend/img/background.jpg');
        height: 150px;
        background-position: center;
        background-size: cover;
        position: relative;
    }

    .image img {
        position: absolute;
        top: 55%;
        left: 35%;
        margin-top: 30%;
    }

    i {
        font-size: 14px;
        padding-right: 8px;
    }
</style>

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush