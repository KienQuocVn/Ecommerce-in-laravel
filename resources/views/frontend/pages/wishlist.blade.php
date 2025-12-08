@extends('frontend.layouts.master')
@section('title','Trang danh sách yêu thích')
@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="bread-inner">
					<ul class="bread-list">
						<li><a href="{{('home')}}">Trang chủ<i class="ti-arrow-right"></i></a></li>
						<li class="active"><a href="javascript:void(0);">Yêu thích</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Shopping Cart -->
<div class="shopping-cart section">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<!-- Shopping Summery -->
				<table class="table shopping-summery">
					<thead>
						<tr class="main-hading">
							<th>SẢN PHẨM</th>
							<th>TÊN</th>
							<th class="text-center">TỔNG CỘNG</th>
							<th class="text-center">THÊM VÀO GIỎ HÀNG</th>
							<th class="text-center"><i class="ti-trash remove-icon"></i></th>
						</tr>
					</thead>
					<tbody>
						@if(Helper::getAllProductFromWishlist())
						@foreach(Helper::getAllProductFromWishlist() as $key=>$wishlist)
						<tr>
							@php
							$photo=explode(',',$wishlist->product['photo']);
							$hasSize = !empty($wishlist->product['size']);
							$sizes = $hasSize ? explode(',', $wishlist->product['size']) : [];
							$sizes = array_map('trim', $sizes);
							@endphp
							<td class="image" data-title="No"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></td>
							<td class="product-des" data-title="Description">
								<p class="product-name"><a href="{{route('product-detail',$wishlist->product['slug'])}}">{{$wishlist->product['title']}}</a></p>
								<p class="product-des">{!!($wishlist['summary']) !!}</p>
							</td>
							<td class="total-amount" data-title="Total"><span>{{number_format($wishlist['amount'],0)}} VNĐ</span></td>
							<td>
								@if($hasSize)
								<a href="#"
									class="btn text-white add-to-cart-from-wishlist"
									data-toggle="modal"
									data-target="#sizeModal{{$wishlist->id}}"
									data-product-slug="{{$wishlist->product['slug']}}"
									data-product-id="{{$wishlist->product['id']}}"
									data-product-size="{{$wishlist->product['size']}}"
									data-product-title="{{$wishlist->product['title']}}">
									THÊM VÀO GIỎ HÀNG
								</a>
								@else
								<a href="#"
									class="btn text-white add-to-cart-no-size"
									data-product-slug="{{$wishlist->product['slug']}}"
									data-product-title="{{$wishlist->product['title']}}">
									THÊM VÀO GIỎ HÀNG
								</a>
								@endif
							</td>
							<td class="action" data-title="Remove"><a href="{{route('wishlist-delete',$wishlist->id)}}"><i class="ti-trash remove-icon"></i></a></td>
						</tr>
						@endforeach
						@else
						<tr>
							<td class="text-center">
								Không có danh sách mong muốn nào có sẵn. <a href="{{route('product-lists')}}" style="color:blue;">Tiếp tục mua sắm</a> </td>
						</tr>
						@endif


					</tbody>
				</table>
				<!--/ End Shopping Summery -->
			</div>
		</div>
	</div>
</div>
<!--/ End Shopping Cart -->

<!-- Start Shop Services Area  -->
<section class="shop-services section home">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-6 col-12">
				<!-- Start Single Service -->
				<div class="single-service">
					<i class="ti-rocket"></i>
					<h4>Miễn phí vận chuyển</h4>
					<p>Đơn hàng trên 100k</p>
				</div>
				<!-- End Single Service -->
			</div>
			<div class="col-lg-3 col-md-6 col-12">
				<!-- Start Single Service -->
				<div class="single-service">
					<i class="ti-reload"></i>
					<h4>Trả hàng miễn phí</h4>
					<p>Trả hàng trong vòng 30 ngày</p>
				</div>
				<!-- End Single Service -->
			</div>
			<div class="col-lg-3 col-md-6 col-12">
				<!-- Start Single Service -->
				<div class="single-service">
					<i class="ti-lock"></i>
					<h4>Thanh toán an toàn</h4>
					<p>Thanh toán an toàn 100%</p>
				</div>
				<!-- End Single Service -->
			</div>
			<div class="col-lg-3 col-md-6 col-12">
				<!-- Start Single Service -->
				<div class="single-service">
					<i class="ti-tag"></i>
					<h4>Giá tốt nhất</h4>
					<p>Giá đảm bảo</p>
				</div>
				<!-- End Single Service -->
			</div>
		</div>
	</div>
</section>
<!-- End Shop Newsletter -->

@include('frontend.layouts.newsletter')

<!-- Size Selection Modal for Wishlist -->
@if(Helper::getAllProductFromWishlist())
@foreach(Helper::getAllProductFromWishlist() as $key=>$wishlist)
@php
$hasSize = !empty($wishlist->product['size']);
$sizes = $hasSize ? explode(',', $wishlist->product['size']) : [];
$sizes = array_map('trim', $sizes);
$photo=explode(',',$wishlist->product['photo']);
$after_discount=($wishlist->product['price']-($wishlist->product['price']*$wishlist->product['discount'])/100);
@endphp
@if($hasSize)
<div class="modal fade wishlist-size-modal" id="sizeModal{{$wishlist->id}}" tabindex="-1" role="dialog" aria-labelledby="sizeModalLabel{{$wishlist->id}}">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content wishlist-modal-content">
			<button type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
				<i class="ti-close"></i>
			</button>
			<div class="modal-body wishlist-modal-body">
				<form id="addToCartForm{{$wishlist->id}}" class="add-to-cart-form-wishlist">
					@csrf
					<input type="hidden" name="slug" value="{{$wishlist->product['slug']}}">
					<input type="hidden" name="size" id="selected_size{{$wishlist->id}}" required>

					<div class="wishlist-modal-wrapper">
						<!-- Product Image -->
						<div class="wishlist-modal-image-wrapper">
							<div class="wishlist-modal-image">
								<img src="{{$photo[0]}}" alt="{{$wishlist->product['title']}}">
							</div>
						</div>

						<!-- Product Info & Options -->
						<div class="wishlist-modal-content-wrapper">
							<div class="wishlist-modal-info">
								<h3 class="product-title">{{$wishlist->product['title']}}</h3>

								<div class="product-price-wrapper">
									<div class="product-price">
										<span class="current-price">{{number_format($after_discount,0)}} VNĐ</span>
										@if($wishlist->product['discount'] > 0)
										<div class="price-info">
											<del class="old-price">{{number_format($wishlist->product['price'],0)}} VNĐ</del>
											<span class="discount-badge">-{{$wishlist->product['discount']}}%</span>
										</div>
										@endif
									</div>
								</div>

								<div class="modal-divider"></div>

								<!-- Size & Quantity Row -->
								<div class="size-quantity-row">
									<!-- Size Selection -->
									<div class="size-section">
										<label class="section-label">
											<span class="label-text">Kích thước</span>
											<span class="text-danger">*</span>
										</label>
										<div class="size-selector">
											@foreach($sizes as $size)
											<label class="size-option">
												<input type="radio" name="size_radio" value="{{trim($size)}}" required>
												<span class="size-text">{{trim($size)}}</span>
											</label>
											@endforeach
										</div>
										<div class="text-danger size-error-message" id="size-error{{$wishlist->id}}" style="display:none;">
											Vui lòng chọn size sản phẩm!
										</div>
									</div>

									<!-- Quantity -->
									<div class="quantity-section">
										<label class="section-label">
											<span class="label-text">Số lượng</span>
											<span class="text-danger">*</span>
										</label>
										<div class="quantity-input-group">
											<button type="button" class="qty-btn qty-minus" data-type="minus" data-field="quant[1]" disabled>
												<i class="ti-minus"></i>
											</button>
											<input type="text" name="quant[1]" class="qty-input" data-min="1" data-max="1000" value="1" required readonly>
											<button type="button" class="qty-btn qty-plus" data-type="plus" data-field="quant[1]">
												<i class="ti-plus"></i>
											</button>
										</div>
									</div>
								</div>
							</div>

							<!-- Action Buttons -->
							<div class="wishlist-modal-actions">
								<button type="button" class="btn-action btn-cancel" data-dismiss="modal">
									Hủy
								</button>
								<button type="button" class="btn-action btn-add-cart" id="submitCart{{$wishlist->id}}" disabled>
									<i class="ti-shopping-cart"></i>
									<span class="btn-text">Thêm vào giỏ hàng</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endif
@endforeach
@endif

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<div class="row no-gutters">
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<!-- Product Slider -->
						<div class="product-gallery">
							<div class="quickview-slider-active">
								<div class="single-slider">
									<img src="images/modal1.jpg" alt="#">
								</div>
								<div class="single-slider">
									<img src="images/modal2.jpg" alt="#">
								</div>
								<div class="single-slider">
									<img src="images/modal3.jpg" alt="#">
								</div>
								<div class="single-slider">
									<img src="images/modal4.jpg" alt="#">
								</div>
							</div>
						</div>
						<!-- End Product slider -->
					</div>
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
						<div class="quickview-content">
							<h2>Flared Shift Dress</h2>
							<div class="quickview-ratting-review">
								<div class="quickview-ratting-wrap">
									<div class="quickview-ratting">
										<i class="yellow fa fa-star"></i>
										<i class="yellow fa fa-star"></i>
										<i class="yellow fa fa-star"></i>
										<i class="yellow fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
									<a href="#"> (1 đánh giá của khách hàng)</a>
								</div>
								<div class="quickview-stock">
									<span><i class="fa fa-check-circle-o"></i> còn hàng</span>
								</div>
							</div>
							<h3>$29.00</h3>
							<div class="quickview-peragraph">
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui nemo ipsum numquam.</p>
							</div>
							<div class="size">
								<div class="row">
									<div class="col-lg-6 col-12">
										<h5 class="title">Size</h5>
										<select>
											<option selected="selected">s</option>
											<option>m</option>
											<option>l</option>
											<option>xl</option>
										</select>
									</div>
									<div class="col-lg-6 col-12">
										<h5 class="title">Color</h5>
										<select>
											<option selected="selected">orange</option>
											<option>purple</option>
											<option>black</option>
											<option>pink</option>
										</select>
									</div>
								</div>
							</div>
							<div class="quantity">
								<!-- Input Order -->
								<div class="input-group">
									<div class="button minus">
										<button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
											<i class="ti-minus"></i>
										</button>
									</div>
									<input type="text" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1">
									<div class="button plus">
										<button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
											<i class="ti-plus"></i>
										</button>
									</div>
								</div>
								<!--/ End Input Order -->
							</div>
							<div class="add-to-cart">
								<a href="#" class="btn">Thêm vào giỏ hàng</a>
								<a href="#" class="btn min"><i class="ti-heart"></i></a>
								<a href="#" class="btn min"><i class="fa fa-compress"></i></a>
							</div>
							<div class="default-social">
								<h4 class="share-now">Share:</h4>
								<ul>
									<li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
									<li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
									<li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
									<li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal end -->

@endsection
@push('styles')
<style>
	/* Wishlist Modal Styles - Professional Design */
	.wishlist-size-modal .modal-dialog {
		max-width: 600px;
		margin: 30px auto;
	}

	.wishlist-modal-content {
		border-radius: 12px;
		border: none;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
		overflow: visible;
		position: relative;
		background: #fff;
		height: 700px;
	}

	.modal-close-btn {
		position: absolute;
		top: 15px;
		right: 15px;
		width: 36px;
		height: 36px;
		border-radius: 50%;
		background: rgba(0, 0, 0, 0.05);
		border: none;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		z-index: 10;
		transition: all 0.3s ease;
		color: #666;
		font-size: 18px;
	}

	.modal-close-btn:hover {
		background: rgba(0, 0, 0, 0.1);
		transform: rotate(90deg);
		color: #333;
	}

	.wishlist-modal-body {
		padding: 0;
	}

	.wishlist-modal-wrapper {
		display: flex;
		flex-direction: column;
	}

	.wishlist-modal-image-wrapper {
		width: 100%;
		background: #f8f9fa;
		padding: 20px;
		border-radius: 12px 12px 0 0;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.wishlist-modal-image {
		width: 100%;
		max-width: 280px;
		aspect-ratio: 1;
		background: #fff;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
	}

	.wishlist-modal-image img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
	}

	.wishlist-modal-content-wrapper {
		padding: 24px;
	}

	.wishlist-modal-info {
		width: 100%;
	}

	.wishlist-modal-info .product-title {
		font-size: 20px;
		font-weight: 600;
		color: #1a1a1a;
		margin: 0 0 16px 0;
		line-height: 1.4;
		word-wrap: break-word;
	}

	.product-price-wrapper {
		margin-bottom: 20px;
	}

	.product-price {
		display: flex;
		flex-direction: column;
		gap: 8px;
	}

	.product-price .current-price {
		font-size: 28px;
		font-weight: 700;
		color: #F7941D;
		line-height: 1;
	}

	.product-price .price-info {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.product-price .old-price {
		font-size: 16px;
		color: #999;
		text-decoration: line-through;
	}

	.product-price .discount-badge {
		background: #ff4444;
		color: #fff;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 600;
		line-height: 1.4;
	}

	.modal-divider {
		height: 1px;
		background: #e9ecef;
		margin: 20px 0;
	}

	.section-label {
		display: block;
		font-size: 14px;
		font-weight: 600;
		color: #333;
		margin-bottom: 12px;
	}

	.section-label .label-text {
		margin-right: 4px;
	}

	.size-quantity-row {
		display: flex;
		gap: 24px;
		align-items: flex-start;
		margin-bottom: 24px;
	}

	.size-section {
		flex: 1;
		margin-bottom: 0;
	}

	.quantity-section {
		flex: 0 0 auto;
		margin-bottom: 0;
		min-width: 140px;
	}

	.size-selector {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
	}

	.size-option {
		position: relative;
		cursor: pointer;
	}

	.size-option input[type="radio"] {
		position: absolute;
		opacity: 0;
		width: 0;
		height: 0;
	}

	.size-option .size-text {
		display: inline-block;
		padding: 10px 20px;
		border: 1.5px solid #ddd;
		border-radius: 6px;
		background: #fff;
		transition: all 0.2s ease;
		text-align: center;
		font-weight: 500;
		color: #555;
		font-size: 14px;
		min-width: 50px;
	}

	.size-option:hover .size-text {
		border-color: #F7941D;
		background: #fff8f0;
	}

	.size-option input[type="radio"]:checked+.size-text,
	.size-option.selected .size-text {
		border-color: #F7941D;
		background: #F7941D;
		color: #fff;
		font-weight: 600;
	}

	.size-error-message {
		margin-top: 8px;
		font-size: 13px;
		color: #ff4444;
		display: block;
	}

	.quantity-input-group {
		display: inline-flex;
		align-items: center;
		border: 1.5px solid #ddd;
		border-radius: 6px;
		overflow: hidden;
		background: #fff;
		width: 130px;
	}

	.qty-btn {
		background: #f8f9fa;
		border: none;
		padding: 10px 14px;
		cursor: pointer;
		transition: all 0.2s ease;
		color: #666;
		font-size: 14px;
		display: flex;
		align-items: center;
		justify-content: center;
		min-width: 38px;
	}

	.qty-btn:hover:not(:disabled) {
		background: #F7941D;
		color: #fff;
	}

	.qty-btn:disabled {
		opacity: 0.4;
		cursor: not-allowed;
		background: #f0f0f0;
	}

	.qty-input {
		border: none;
		width: 54px;
		text-align: center;
		font-size: 15px;
		font-weight: 600;
		padding: 10px 4px;
		background: #fff;
		color: #333;
	}

	.qty-input:focus {
		outline: none;
	}

	.wishlist-modal-actions {
		display: flex;
		gap: 12px;
		margin-top: 24px;
		padding-top: 20px;
		border-top: 1px solid #e9ecef;
	}

	.btn-action {
		flex: 1;
		padding: 12px 20px;
		border-radius: 6px;
		font-weight: 600;
		font-size: 15px;
		transition: all 0.2s ease;
		border: none;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
	}

	.btn-cancel {
		background: #f8f9fa;
		color: #666;
		border: 1.5px solid #e0e0e0;
	}

	.btn-cancel:hover {
		background: #e9ecef;
		border-color: #d0d0d0;
		color: #333;
	}

	.btn-add-cart {
		background: #F7941D;
		color: #fff;
		box-shadow: 0 2px 8px rgba(247, 148, 29, 0.25);
	}

	.btn-add-cart:hover:not(:disabled) {
		background: #e67e22;
		box-shadow: 0 4px 12px rgba(247, 148, 29, 0.35);
		transform: translateY(-1px);
	}

	.btn-add-cart:disabled {
		opacity: 0.6;
		cursor: not-allowed;
		transform: none;
		box-shadow: 0 2px 8px rgba(247, 148, 29, 0.25);
	}

	.btn-add-cart:disabled:hover {
		transform: none;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.wishlist-size-modal .modal-dialog {
			max-width: 95%;
			margin: 15px auto;
		}

		.wishlist-modal-image-wrapper {
			padding: 15px;
		}

		.wishlist-modal-image {
			max-width: 100%;
		}

		.wishlist-modal-content-wrapper {
			padding: 20px;
		}

		.wishlist-modal-info .product-title {
			font-size: 18px;
		}

		.product-price .current-price {
			font-size: 24px;
		}

		.size-quantity-row {
			flex-direction: column;
			gap: 20px;
		}

		.quantity-section {
			min-width: 100%;
		}

		.wishlist-modal-actions {
			flex-direction: column;
		}

		.btn-action {
			width: 100%;
		}
	}
</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
	$(document).ready(function() {
		// Handle size selection in modal
		$('.size-option input[type="radio"]').on('change', function() {
			const selectedSize = $(this).val();
			const modalId = $(this).closest('.modal').attr('id');
			const wishlistId = modalId.replace('sizeModal', '');

			$('#selected_size' + wishlistId).val(selectedSize);

			// Update selected state
			$(this).closest('.modal').find('.size-option').removeClass('selected');
			$(this).closest('.size-option').addClass('selected');

			// Enable submit button
			$('#submitCart' + wishlistId).prop('disabled', false);
			$('#size-error' + wishlistId).hide();
		});

		// Handle form submission
		$('[id^="submitCart"]').on('click', function() {
			const wishlistId = $(this).attr('id').replace('submitCart', '');
			const form = $('#addToCartForm' + wishlistId);
			const selectedSize = $('#selected_size' + wishlistId).val();

			if (!selectedSize) {
				$('#size-error' + wishlistId).show();
				return;
			}

			// Disable button during submission
			$(this).prop('disabled', true);
			$(this).find('.btn-text').text('Đang thêm...');

			// Submit via AJAX
			$.ajax({
				url: '{{route("single-add-to-cart")}}',
				type: 'POST',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json'
				},
				data: form.serialize(),
				success: function(response) {
					if (response.success) {
						swal('Thành công!', response.message || 'Sản phẩm đã được thêm vào giỏ hàng', 'success').then(function() {
							location.reload();
						});
					} else {
						swal('Lỗi!', response.message || 'Có lỗi xảy ra', 'error');
						$('#submitCart' + wishlistId).prop('disabled', false);
						$('#submitCart' + wishlistId).find('.btn-text').text('Thêm vào giỏ hàng');
					}
				},
				error: function(xhr) {
					let errorMsg = 'Có lỗi xảy ra khi thêm vào giỏ hàng';
					if (xhr.responseJSON && xhr.responseJSON.message) {
						errorMsg = xhr.responseJSON.message;
					} else if (xhr.responseJSON && xhr.responseJSON.errors) {
						errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
					}
					swal('Lỗi!', errorMsg, 'error');
					$('#submitCart' + wishlistId).prop('disabled', false);
					$('#submitCart' + wishlistId).find('.btn-text').text('Thêm vào giỏ hàng');
				}
			});
		});

		// Reset modal when closed
		$('[id^="sizeModal"]').on('hidden.bs.modal', function() {
			const wishlistId = $(this).attr('id').replace('sizeModal', '');
			$(this).find('.size-option').removeClass('selected');
			$(this).find('input[type="radio"]').prop('checked', false);
			$('#selected_size' + wishlistId).val('');
			$('#submitCart' + wishlistId).prop('disabled', true);
			$('#submitCart' + wishlistId).find('.btn-text').text('Thêm vào giỏ hàng');
			$('#size-error' + wishlistId).hide();
			// Reset quantity
			$(this).find('.qty-input').val(1);
			$(this).find('.qty-minus').attr('disabled', true);
			$(this).find('.qty-plus').removeAttr('disabled');
		});

		// Quantity plus/minus buttons (for modal)
		$(document).on('click', '.qty-btn', function() {
			const type = $(this).attr('data-type');
			const field = $(this).attr('data-field');
			const input = $(this).siblings('.qty-input');
			const currentVal = parseInt(input.val());
			const min = parseInt(input.attr('data-min'));
			const max = parseInt(input.attr('data-max'));

			if (!isNaN(currentVal)) {
				if (type == 'minus') {
					if (currentVal > min) {
						input.val(currentVal - 1);
						$(this).siblings('.qty-plus').removeAttr('disabled');
					}
					if (parseInt(input.val()) == min) {
						$(this).attr('disabled', true);
					}
				} else if (type == 'plus') {
					if (currentVal < max) {
						input.val(currentVal + 1);
						$(this).siblings('.qty-minus').removeAttr('disabled');
					}
					if (parseInt(input.val()) == max) {
						$(this).attr('disabled', true);
					}
				}
			} else {
				input.val(min);
			}
		});

		// Initialize quantity buttons state
		$('.qty-input').each(function() {
			const val = parseInt($(this).val());
			const min = parseInt($(this).attr('data-min'));
			if (val <= min) {
				$(this).siblings('.qty-minus').attr('disabled', true);
			}
		});

		// Handle add to cart for products without size
		$('.add-to-cart-no-size').on('click', function(e) {
			e.preventDefault();
			const slug = $(this).data('product-slug');
			const title = $(this).data('product-title');

			// Disable button during submission
			$(this).prop('disabled', true).text('Đang thêm...');

			// Submit via AJAX with default size "One Size"
			$.ajax({
				url: '{{route("single-add-to-cart")}}',
				type: 'POST',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json'
				},
				data: {
					_token: '{{csrf_token()}}',
					slug: slug,
					'size': 'One Size',
					'quant[1]': 1
				},
				success: function(response) {
					if (response.success) {
						swal('Thành công!', response.message || 'Sản phẩm đã được thêm vào giỏ hàng', 'success').then(function() {
							location.reload();
						});
					} else {
						swal('Lỗi!', response.message || 'Có lỗi xảy ra', 'error');
						$('.add-to-cart-no-size').prop('disabled', false).text('THÊM VÀO GIỎ HÀNG');
					}
				},
				error: function(xhr) {
					let errorMsg = 'Có lỗi xảy ra khi thêm vào giỏ hàng';
					if (xhr.responseJSON && xhr.responseJSON.message) {
						errorMsg = xhr.responseJSON.message;
					} else if (xhr.responseJSON && xhr.responseJSON.errors) {
						errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
					}
					swal('Lỗi!', errorMsg, 'error');
					$('.add-to-cart-no-size').prop('disabled', false).text('THÊM VÀO GIỎ HÀNG');
				}
			});
		});
	});
</script>
@endpush