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
								<div class="row p-4">
									@foreach ($avatars as $avatar)
										@foreach ($avatar as $character)
										 	@if ($loop->first)
											 <div class="col-md-2 col-sm-6">
												<div class="card avatar-card mb-6" onclick="window.location.href='{{ route('user.extension.avatar.list.videos.view', $character->group) }}'">
													<div class="avatar-card-box">
														<div class="avatar-image-box">
															<img src="{{ $character->preview_image_url}}" class="avatar-image">												
														</div>
														<div class="avatar-info text-center pt-4 pb-4">
															<p class="mb-0 fs-12">{{ ucfirst($character->group) }}</p>
															<p class="mb-0 fs-11 text-muted">{{ count($avatar) }} {{__('looks')}}</p>
														</div>
													</div>							
												</div>
											</div>
											@endif
											
										@endforeach
										
									@endforeach	
								</div>	
							</div>	
							
							
						</div>
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


			// DELETE CUSTOM TEMPLATE
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
		});
	</script>
@endsection
