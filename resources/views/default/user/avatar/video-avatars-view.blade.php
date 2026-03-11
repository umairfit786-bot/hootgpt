@extends('layouts.app')

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
									<a class="side-menu__item" href="{{ route('user.extension.avatar.list.images') }}">
									<span class="side-menu__icon fa-solid fa-user-tie-hair"></span>
									<span class="side-menu__label">{{ __('Image Avatars') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item active" href="{{ route('user.extension.avatar.list.videos') }}">
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
								<h6><a href="{{ route('user.extension.avatar.list.videos')}}" class="avatar-list-return"><i class="fa-solid fa-arrow-left"></i></a>{{ $name }}</h6>
								<div class="row p-4">
									@foreach ($avatars as $avatar)
										@if(in_array($avatar->avatar_id , $favorites))
											<div class="col-md-2 col-sm-6">
												<div class="card avatar-card mb-6" data-url="{{ $avatar->preview_video_url}}">
													<div class="avatar-card-box">
														<a href="#" class="avatar-favorite marked-favorite" data-id="{{ $avatar->avatar_id }}"><i class="fa-solid fa-heart text-muted"></i></a>
														<div class="avatar-image-box">
															<img src="{{ $avatar->preview_image_url}}" class="avatar-image">												
														</div>
														<div class="avatar-info text-center pt-4 pb-4">
															<p class="mb-0 fs-10">{{ $avatar->avatar_name }}</p>
														</div>
													</div>							
												</div>
											</div>
										@endif										
									@endforeach	
									@foreach ($avatars as $avatar)
										@if(!in_array($avatar->avatar_id , $favorites))
											<div class="col-md-2 col-sm-6">
												<div class="card avatar-card mb-6" data-url="{{ $avatar->preview_video_url}}">
													<div class="avatar-card-box">
														<a href="#" class="avatar-favorite" data-id="{{ $avatar->avatar_id }}"><i class="fa-solid fa-heart text-muted"></i></a>
														<div class="avatar-image-box">
															<img src="{{ $avatar->preview_image_url}}" class="avatar-image">												
														</div>
														<div class="avatar-info text-center pt-4 pb-4">
															<p class="mb-0 fs-10">{{ $avatar->avatar_name }}</p>
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
			<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h6>{{ __('Avatar View') }}</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4 text-center">
					<video controls autoplay width="640" height="360" id="avatarVideo"> 
						<source src="" type="video/mp4" id="selected-avatar-video"> 
					</video>	
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

			

			$(document).on('click', '.avatar-favorite', function(e) {

				e.preventDefault();

				let formData = new FormData();
				formData.append("id", $(this).attr('data-id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/app/user/avatar/list/video-avatar/favorite',
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


			$(document).on('click', '.avatar-card', function(e) {

				let url = $(this).attr('data-url');
				document.getElementById("selected-avatar-video").src = url;

				var myModal = new bootstrap.Modal(document.getElementById('image-view-modal'))
				myModal.show();		
			});

			$('#image-view-modal').on('show.bs.modal', function (event) {   
				$('#avatarVideo')[0].load();
			})    

			$('#image-view-modal').on('hide.bs.modal', function (event) {   
				$('#avatarVideo')[0].pause();
			}) 

			$(document).on('click', '.avatar-card', function(e) {

				e.preventDefault();

				
				console.log('card clicked')
			});
		});
	</script>
@endsection
