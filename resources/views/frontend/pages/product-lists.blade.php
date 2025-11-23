@extends('frontend.layouts.master')

@section('title','SHOPFY || DANH SÁCH SẢN PHẨM')

@section('main-content')

<!-- Breadcrumbs -->
<div class="breadcrumbs">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="bread-inner">
					<ul class="bread-list">
						<li><a href="{{route('home')}}">Trang chủ<i class="ti-arrow-right"></i></a></li>
						<li class="active"><a href="javascript:void(0);">Danh sách sản phẩm</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<form action="{{route('shop.filter')}}" method="POST" id="filterForm">
	@csrf
	<section class="product-area shop-sidebar shop-list shop section">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-4 col-12">
					<div class="shop-sidebar">
						<!-- Single Widget -->
						<div class="single-widget category">
							<h3 class="title">Thể loại</h3>
							<ul class="categor-list">
								@php
								$menu=App\Models\Category::getAllParentWithChild();
								@endphp
								@if($menu)
								<li>
									@foreach($menu as $cat_info)
									@if($cat_info->child_cat->count()>0)
								<li>
									<a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a>
									<ul>
										@foreach($cat_info->child_cat as $sub_menu)
										<li><a href="{{route('product-sub-cat',[$cat_info->slug,$sub_menu->slug])}}">{{$sub_menu->title}}</a></li>
										@endforeach
									</ul>
								</li>
								@else
								<li>
									<a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a>
								</li>
								@endif
								@endforeach
								</li>
								@endif
							</ul>
						</div>
						<!--/ End Single Widget -->

						<!-- Shop By Price -->
						<div class="single-widget range">
							<h3 class="title">Khoảng giá</h3>
							<div class="price-filter">
								<div class="price-filter-inner">
									@php
									$max=DB::table('products')->max('price');
									@endphp
									<div id="slider-range" data-min="0" data-max="{{$max}}"></div>
									<div class="product_filter">
										<div class="label-input">
											<span>Phạm vi:</span>
											<input type="text" id="amount" readonly />
											<input type="hidden" name="price_range" id="price_range" value="@if(!empty($_GET['price'])){{$_GET['price']}}@endif" />
										</div>
										<button type="submit" class="filter_button">Áp dụng</button>
									</div>
								</div>
							</div>
						</div>
						<!--/ End Shop By Price -->

						<!-- Filter by Size -->
						@if(isset($available_sizes) && count($available_sizes) > 0)
						<div class="single-widget category">
							<h3 class="title">Kích thước</h3>
							<ul class="categor-list size-filter">
								@php
								$selected_sizes = !empty($_GET['size']) ? explode(',', $_GET['size']) : [];
								@endphp
								@foreach($available_sizes as $size)
								<li>
									<label class="checkbox-label">
										<input type="checkbox" name="size[]" value="{{$size}}"
											{{in_array($size, $selected_sizes) ? 'checked' : ''}}>
										<span>{{$size}}</span>
									</label>
								</li>
								@endforeach
							</ul>
							<button type="submit" class="filter_button mt-2">Áp dụng</button>
						</div>
						@endif
						<!--/ End Filter by Size -->

						<!-- Filter by Condition -->
						<div class="single-widget category">
							<h3 class="title">Trạng thái</h3>
							<ul class="categor-list">
								@php
								$conditions = [
								'new' => 'Mới nhất',
								'hot' => 'Hot - Bán chạy',
								'sale' => 'Giảm giá'
								];
								$selected_condition = $_GET['condition'] ?? '';
								@endphp
								@foreach($conditions as $key => $label)
								<li>
									<label class="checkbox-label">
										<input type="radio" name="condition" value="{{$key}}"
											{{$selected_condition == $key ? 'checked' : ''}}
											onchange="this.form.submit();">
										<span>{{$label}}</span>
									</label>
								</li>
								@endforeach
								@if($selected_condition)
								<li>
									<a href="javascript:void(0)" onclick="clearCondition()" class="text-danger">
										<i class="fa fa-times"></i> Xóa lọc
									</a>
								</li>
								@endif
							</ul>
						</div>
						<!--/ End Filter by Condition -->

						<!-- Filter by Rating -->
						<div class="single-widget category">
							<h3 class="title">Đánh giá</h3>
							<ul class="categor-list rating-filter">
								@php
								$selected_rating = $_GET['rating'] ?? '';
								@endphp
								@for($i = 5; $i >= 3; $i--)
								<li>
									<label class="checkbox-label">
										<input type="radio" name="rating" value="{{$i}}"
											{{$selected_rating == $i ? 'checked' : ''}}
											onchange="this.form.submit();">
										<span>
											@for($j = 1; $j <= $i; $j++)
												<i class="fa fa-star text-warning"></i>
												@endfor
												@for($j = $i + 1; $j <= 5; $j++)
													<i class="fa fa-star-o"></i>
													@endfor
													trở lên
										</span>
									</label>
								</li>
								@endfor
								@if($selected_rating)
								<li>
									<a href="javascript:void(0)" onclick="clearRating()" class="text-danger">
										<i class="fa fa-times"></i> Xóa lọc
									</a>
								</li>
								@endif
							</ul>
						</div>
						<!--/ End Filter by Rating -->

						<!-- Recent Products -->
						<div class="single-widget recent-post">
							<h3 class="title">Sản phẩm gần đây</h3>
							@foreach($recent_products as $product)
							@php
							$photo=explode(',',$product->photo);
							$org=($product->price-($product->price*$product->discount)/100);
							@endphp
							<div class="single-post first">
								<div class="image">
									<img src="{{$photo[0]}}" alt="{{$product->title}}">
								</div>
								<div class="content">
									<h5><a href="{{route('product-detail',$product->slug)}}">{{$product->title}}</a></h5>
									<p class="price">
										<del class="text-muted">{{number_format($product->price,0)}} VNĐ</del>
										{{number_format($org,0)}} VNĐ
									</p>
								</div>
							</div>
							@endforeach
						</div>
						<!--/ End Recent Products -->

						<!-- Brands -->
						<div class="single-widget category">
							<h3 class="title">Thương hiệu</h3>
							<ul class="categor-list">
								@php
								$brands=DB::table('brands')->orderBy('title','ASC')->where('status','active')->get();
								@endphp
								@foreach($brands as $brand)
								<li><a href="{{route('product-brand',$brand->slug)}}">{{$brand->title}}</a></li>
								@endforeach
							</ul>
						</div>
						<!--/ End Brands -->
					</div>
				</div>

				<div class="col-lg-9 col-md-8 col-12">
					<div class="row">
						<div class="col-12">
							<!-- Shop Top -->
							<div class="shop-top">
								<div class="shop-shorter">
									<!-- SẮP XẾP THEO -->
									<div class="single-shorter">
										<label><i class="fa fa-sort-amount-desc"></i> Sắp xếp:</label>
										<select class='sortBy' name='sortBy' onchange="this.form.submit();">
											<option value="">Mặc định</option>
											<optgroup label="Theo tên">
												<option value="title" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='title' ) selected @endif>Tên: A → Z</option>
												<option value="title_desc" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='title_desc' ) selected @endif>Tên: Z → A</option>
											</optgroup>
											<optgroup label="Theo giá">
												<option value="price_asc" @if(!empty($_GET['sortBy']) && in_array($_GET['sortBy'], ['price', 'price_asc' ])) selected @endif>Giá: Thấp → Cao</option>
												<option value="price_desc" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='price_desc' ) selected @endif>Giá: Cao → Thấp</option>
											</optgroup>
											<optgroup label="Khác">
												<option value="discount" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='discount' ) selected @endif>Giảm giá nhiều nhất</option>
												<option value="popular" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='popular' ) selected @endif>Bán chạy nhất</option>
												<option value="newest" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='newest' ) selected @endif>Mới nhất</option>
											</optgroup>
										</select>
									</div>

									<!-- NÚT XÓA BỘ LỌC -->
									@if(!empty($_GET) && count(array_filter($_GET)) > 0)
									<div class="single-shorter">
										<a href="{{route('product-lists')}}" class="btn btn-sm text-white">
											<i class="fa fa-times"></i> Xóa tất cả bộ lọc
										</a>
									</div>
									@endif
								</div>

								@php
								$currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
								$query = request()->query();
								$activeView = $viewMode ?? ($currentRoute === 'product-grids' ? 'grid' : 'list');
								$gridUrl = in_array($currentRoute, ['product-grids', 'product-lists'])
								? route('product-grids', $query)
								: request()->fullUrlWithQuery(array_merge($query, ['view' => 'grid']));
								$listUrl = in_array($currentRoute, ['product-grids', 'product-lists'])
								? route('product-lists', $query)
								: request()->fullUrlWithQuery(array_merge($query, ['view' => 'list']));
								@endphp
								<ul class="view-mode">
									<li class="{{ $activeView === 'grid' ? 'active' : '' }}"><a href="{{ $gridUrl }}"><i class="fa fa-th-large"></i></a></li>
									<li class="{{ $activeView === 'list' ? 'active' : '' }}"><a href="{{ $activeView === 'list' ? 'javascript:void(0)' : $listUrl }}"><i class="fa fa-th-list"></i></a></li>
								</ul>
							</div>
							<!--/ End Shop Top -->

							<!-- HIỂN THỊ THÔNG TIN BỘ LỌC -->
							@if(!empty($_GET) && count(array_filter($_GET)) > 0)
							<div class="filter-info">
								<p><strong>Đang lọc:</strong>
									@if(!empty($_GET['price']))
									<span class="badge badge-primary">Giá: {{$_GET['price']}} VNĐ</span>
									@endif
									@if(!empty($_GET['size']))
									<span class="badge badge-info">Size: {{$_GET['size']}}</span>
									@endif
									@if(!empty($_GET['condition']))
									<span class="badge badge-warning">{{ucfirst($_GET['condition'])}}</span>
									@endif
									@if(!empty($_GET['rating']))
									<span class="badge badge-success">{{$_GET['rating']}}⭐ trở lên</span>
									@endif
								</p>
							</div>
							@endif
						</div>
					</div>

					<div class="row">
						@if(count($products)>0)
						@foreach($products as $product)
						<!-- Start Single List -->
						<div class="col-12">
							<div class="row">
								<div class="col-lg-4 col-md-6 col-sm-6">
									<div class="single-product">
										<div class="product-img">
											<a href="{{route('product-detail',$product->slug)}}">
												@php
												$photo=explode(',',$product->photo);
												@endphp
												<img class="default-img" src="{{$photo[0]}}" alt="{{$product->title}}">
												<img class="hover-img" src="{{$photo[0]}}" alt="{{$product->title}}">
												@if($product->discount)
												<span class="price-dec">-{{$product->discount}}%</span>
												@endif
												@if($product->condition == 'new')
												<span class="new">NEW</span>
												@elseif($product->condition == 'hot')
												<span class="hot">HOT</span>
												@endif
											</a>
											<div class="button-head">
												<div class="product-action">
													<a data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Xem nhanh</span></a>
													<a title="Wishlist" href="{{route('add-to-wishlist',$product->slug)}}" class="wishlist"><i class=" ti-heart "></i><span>Yêu thích</span></a>
												</div>
												<div class="product-action-2">
													<a title="Thêm vào giỏ hàng" href="{{route('add-to-cart',$product->slug)}}">Thêm vào giỏ</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-8 col-md-6 col-12">
									<div class="list-content">
										<div class="product-content">
											<h3 class="title"><a href="{{route('product-detail',$product->slug)}}">{{$product->title}}</a></h3>
											<div class="product-price">
												@php
												$after_discount=($product->price-($product->price*$product->discount)/100);
												@endphp
												<span class="new-price">{{number_format($after_discount,0)}} VNĐ</span>
												@if($product->discount > 0)
												<del class="old-price">{{number_format($product->price,0)}} VNĐ</del>
												@endif
											</div>
										</div>
										<p class="des pt-2">{!! html_entity_decode($product->summary) !!}</p>
										<a href="{{route('add-to-cart',$product->slug)}}" class="btn btn-primary">Mua ngay</a>
									</div>
								</div>
							</div>
						</div>
						<!-- End Single List -->
						@endforeach
						@else
						<div class="col-12">
							<div class="alert alert-warning text-center" style="margin:50px auto;">
								<i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
								<h4>Không tìm thấy sản phẩm nào</h4>
								<p>Vui lòng thử điều chỉnh bộ lọc của bạn</p>
								<a href="{{route('product-lists')}}" class="btn btn-primary">Xóa bộ lọc</a>
							</div>
						</div>
						@endif
					</div>

					<div class="row">
						<div class="col-md-12 justify-content-center d-flex">
							{{$products->appends($_GET)->links()}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</form>

<!-- Modal (giống product-grids) -->
@if($products)
@foreach($products as $key=>$product)
<div class="modal fade" id="{{$product->id}}" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<div class="row no-gutters">
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<div class="product-gallery">
							<div class="quickview-slider-active">
								@php
								$photo=explode(',',$product->photo);
								@endphp
								@foreach($photo as $data)
								<div class="single-slider">
									<img src="{{$data}}" alt="{{$product->title}}">
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<div class="quickview-content">
							<h2>{{$product->title}}</h2>
							<div class="quickview-ratting-review">
								<div class="quickview-ratting-wrap">
									<div class="quickview-ratting">
										@php
										$rate=DB::table('product_reviews')->where('product_id',$product->id)->avg('rate');
										$rate_count=DB::table('product_reviews')->where('product_id',$product->id)->count();
										@endphp
										@for($i=1; $i<=5; $i++)
											@if($rate>=$i)
											<i class="yellow fa fa-star"></i>
											@else
											<i class="fa fa-star"></i>
											@endif
											@endfor
									</div>
									<a href="#"> ({{$rate_count}} đánh giá)</a>
								</div>
								<div class="quickview-stock">
									@if($product->stock >0)
									<span><i class="fa fa-check-circle-o"></i> {{$product->stock}} còn hàng</span>
									@else
									<span><i class="fa fa-times-circle-o text-danger"></i> Hết hàng</span>
									@endif
								</div>
							</div>
							@php
							$after_discount=($product->price-($product->price*$product->discount)/100);
							@endphp
							<h3>
								<small><del class="text-muted">{{number_format($product->price,0)}} VNĐ</del></small>
								{{number_format($after_discount,0)}} VNĐ
							</h3>
							<div class="quickview-peragraph">
								<p>{!! html_entity_decode($product->summary) !!}</p>
							</div>
							@if($product->size)
							<div class="size">
								<h5 class="title">Size</h5>
								<select>
									@php
									$sizes=explode(',',$product->size);
									@endphp
									@foreach($sizes as $size)
									<option>{{$size}}</option>
									@endforeach
								</select>
							</div>
							@endif
							<form action="{{route('single-add-to-cart')}}" method="POST">
								@csrf
								<div class="quantity">
									<div class="input-group">
										<div class="button minus">
											<button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
												<i class="ti-minus"></i>
											</button>
										</div>
										<input type="hidden" name="slug" value="{{$product->slug}}">
										<input type="text" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1">
										<div class="button plus">
											<button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
												<i class="ti-plus"></i>
											</button>
										</div>
									</div>
								</div>
								<div class="add-to-cart">
									<button type="submit" class="btn">Thêm vào giỏ hàng</button>
									<a href="{{route('add-to-wishlist',$product->slug)}}" class="btn min"><i class="ti-heart"></i></a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endforeach
@endif

@endsection

@push('styles')
<style>
	.pagination {
		display: inline-flex;
	}

	.filter_button {
		text-align: center;
		background: #F7941D;
		padding: 10px 20px;
		margin-top: 10px;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		width: 100%;
		transition: all 0.3s;
	}

	.filter_button:hover {
		background: #e67e22;
		transform: translateY(-2px);
	}

	.checkbox-label {
		display: flex;
		align-items: center;
		cursor: pointer;
		padding: 5px 0;
	}

	.checkbox-label input {
		margin-right: 8px;
	}

	.size-filter li {
		display: inline-block;
		width: 48%;
	}

	.rating-filter .fa-star {
		font-size: 14px;
	}

	.filter-info {
		background: #f8f9fa;
		padding: 15px;
		border-radius: 5px;
		margin-bottom: 20px;
	}

	.filter-info .badge {
		margin-right: 8px;
		padding: 8px 12px;
		font-size: 13px;
	}

	.shop-top {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 15px;
	}

	.shop-shorter {
		display: flex;
		gap: 15px;
		flex-wrap: wrap;
		align-items: center;
	}

	.single-shorter {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.single-shorter label {
		margin: 0;
		font-weight: 600;
		white-space: nowrap;
	}

	.single-shorter select {
		min-width: 180px;
		padding: 8px 12px;
		border: 1px solid #ddd;
		border-radius: 5px;
	}

	.product-price {
		display: flex;
		align-items: center;
		gap: 10px;
		margin-top: 10px;
	}

	.new-price {
		font-size: 20px;
		font-weight: bold;
		color: #F7941D;
	}

	.old-price {
		font-size: 16px;
		color: #999;
	}

	.price-dec {
		position: absolute;
		top: 10px;
		right: 10px;
		background: #e74c3c;
		color: white;
		padding: 5px 10px;
		border-radius: 3px;
		font-size: 12px;
		font-weight: bold;
	}

	.single-product .new {
		position: absolute;
		top: 10px;
		left: 10px;
		background: #27ae60;
		color: white;
		padding: 5px 10px;
		border-radius: 3px;
		font-size: 11px;
		font-weight: bold;
	}

	.single-product .hot {
		position: absolute;
		top: 10px;
		left: 10px;
		background: #e67e22;
		color: white;
		padding: 5px 10px;
		border-radius: 3px;
		font-size: 11px;
		font-weight: bold;
	}

	@media (max-width: 768px) {
		.shop-top {
			flex-direction: column;
			align-items: flex-start;
		}

		.shop-shorter {
			width: 100%;
			flex-direction: column;
		}

		.single-shorter {
			width: 100%;
		}

		.single-shorter select {
			width: 100%;
		}
	}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
	$(document).ready(function() {
		// Jquery UI slider
		if ($("#slider-range").length > 0) {
			const max_value = parseInt($("#slider-range").data('max')) || 500;
			const min_value = parseInt($("#slider-range").data('min')) || 0;
			const currency = $("#slider-range").data('currency') || '';
			let price_range = min_value + '-' + max_value;

			if ($("#price_range").length > 0 && $("#price_range").val()) {
				price_range = $("#price_range").val().trim();
			}

			let price = price_range.split('-');
			$("#slider-range").slider({
				range: true,
				min: min_value,
				max: max_value,
				values: price,
				slide: function(event, ui) {
					$("#amount").val(ui.values[0] + " -  " + ui.values[1]);
					$("#price_range").val(ui.values[0] + "-" + ui.values[1]);
				}
			});
		}

		if ($("#amount").length > 0) {
			$("#amount").val($("#slider-range").slider("values", 0) +
				"  -  " + $("#slider-range").slider("values", 1));
		}
	});

	// Clear condition filter
	function clearCondition() {
		$('input[name="condition"]').prop('checked', false);
		$('#filterForm').submit();
	}

	// Clear rating filter
	function clearRating() {
		$('input[name="rating"]').prop('checked', false);
		$('#filterForm').submit();
	}
</script>
@endpush