@extends('layouts.app')

@section('css')
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')	
	<div class="row justify-content-center mt-5-7">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-5">
					<div class="row">
						<div class="col-lg-2 col-md-3 col-sm-12">
							<h4 class="page-title mb-0"><i class="text-primary mr-2 fs-16 fa-solid fa-aperture"></i> {{ __('AI Avatar') }}</h4>
							<h6 class="text-muted">{{ __('Create videos with AI') }}</h6>
							<a id="save-button" class="avatar-action-btn text-center mt-5" href="{{ route('user.extension.avatar') }}"><i class="fa-solid fa-clapperboard-play mr-4"></i>{{ __('Create Video') }}</a>
							<ul class="avatar-menu mt-3">
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.results') }}">
									<span class="side-menu__icon    fa-solid fa-photo-film"></span>
									<span class="side-menu__label">{{ __('Video Results') }}</span></a>
								</li>
							</ul>
							<ul class="avatar-menu">								
								<li class="side-item side-item-category mt-1 fs-12 text-muted">{{ __('Assets') }}</li>
								<li class="slide">
									<a class="side-menu__item active" href="{{ route('user.extension.avatar.list.images') }}">
									<span class="side-menu__icon fa-solid fa-user-tie-hair"></span>
									<span class="side-menu__label">{{ __('Image Avatars') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.list.videos') }}">
									<span class="side-menu__icon fa-solid fa-camcorder"></span>
									<span class="side-menu__label">{{ __('Video Avatars') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.voices') }}">
									<span class="side-menu__icon fa-solid fa-message-lines"></span>
									<span class="side-menu__label">{{ __('AI Voices') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.uploads') }}">
									<span class="side-menu__icon fa-solid fa-cloud-arrow-up"></span>
									<span class="side-menu__label">{{ __('Uploads') }}</span></a>
								</li>
							</ul>
						</div>
						<div class="col-lg-10 col-md-9 col-sm-12">
							<div class="">
								<div class="row p-4">
									<div class="col-md-2 col-sm-6">
										<div class="card avatar-card create-avatar-card mb-6" onclick="window.location.href='{{ route('user.extension.avatar.list.images.create') }}'">
											<div class="">
												<div class="avatar-image-box talking-photos text-center">
													<i class="fa-solid fa-plus text-muted"></i>	
													<p class="mb-0 fs-12 text-muted">{{ __('Create Photo Avatar') }}</p>											
												</div>
												<div class="avatar-info text-center pb-6">													
												</div>
											</div>							
										</div>
									</div>
									@foreach ($favorites as $favorite)
										@foreach ($avatarList['data']['talking_photos'] as $avatar)
											@if ($favorite == $avatar['talking_photo_id'])
												<div class="col-md-2 col-sm-6">
													<div class="card avatar-card mb-6" data-id="{{ $avatar['talking_photo_id'] }}">
														<div class="avatar-card-box">
															<a href="#" class="avatar-favorite marked-favorite" data-id="{{ $avatar['talking_photo_id'] }}"><i class="fa-solid fa-heart text-muted"></i></a>
															<div class="avatar-image-box talking-photos">
																<img src="{{ $avatar['preview_image_url']}}" class="avatar-image" id="{{ $avatar['talking_photo_id'] }}_image">												
															</div>
															<div class="avatar-info text-center pb-4">
																<p class="mb-0 fs-12">{{ $avatar['talking_photo_name'] }}</p>
															</div>
														</div>							
													</div>
												</div>
											@endif											
										@endforeach
									@endforeach	
									
									@foreach ($avatarList['data']['talking_photos'] as $avatar)
										@if(!in_array($avatar['talking_photo_id'], $favorites))			
											<div class="col-md-2 col-sm-6">
												<div class="card avatar-card mb-6" data-id="{{ $avatar['talking_photo_id'] }}">
													<div class="avatar-card-box">
														<a href="#" class="avatar-favorite" data-id="{{ $avatar['talking_photo_id'] }}"><i class="fa-solid fa-heart text-muted"></i></a>
														<div class="avatar-image-box talking-photos">
															<img src="{{ $avatar['preview_image_url']}}" class="avatar-image" id="{{ $avatar['talking_photo_id'] }}_image">												
														</div>
														<div class="avatar-info text-center pb-4">
															<p class="mb-0 fs-12">{{ $avatar['talking_photo_name'] }}</p>
														</div>
													</div>							
												</div>
											</div>
										@endif				
									@endforeach										
								</div>	
							</div>								
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="image-modal">
		<div class="modal" id="image-view-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h6>{{ __('Avatar View') }}</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">	
					<img src="" alt="" id="selected-avatar-image">					
				</div>
			</div>
			</div>
	  	</div>
	</div>
@endsection

@section('js')
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

			$(document).on('click', '.avatar-card', function(e) {
				let avatar_id = $(this).attr('data-id');
				let source = avatar_id + "_image";
				let src = document.getElementById(source).src;

				document.getElementById("selected-avatar-image").src = src;
				
				var myModal = new bootstrap.Modal(document.getElementById('image-view-modal'))
				myModal.show();		
			});


			$(document).on('click', '.avatar-favorite', function(e) {

				e.preventDefault();

				let formData = new FormData();
				formData.append("id", $(this).attr('data-id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/app/user/avatar/list/image-avatar/favorite',
					data: formData,
					processData: false,
					contentType: false,
					success: function (data) {
						console.log(data)
						if (data == 'added') {
							toastr.success('{{ __('Avatar added to the favorite list successfully') }}');	
							window.location.reload();							
						} else if (data == 'removed') {
							toastr.success('{{ __('Avatar removed from favorite list successfully') }}');
							window.location.reload();
						} else {
							toastr.warning('{{ __('There was an error editing avatar favorite status') }}');
						}      
					},
					error: function(data) {
						toastr.warning('{{ __('There was an error editing avatar favorite status') }}');
					}
				})
			});
		});
	</script>
@endsection
