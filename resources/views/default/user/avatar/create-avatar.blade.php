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
							<form id="create-avatar-form" action="" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="row justify-content-center">
									<div class="col-sm-8">
										<div class="input-box">
											<h6 class="text-center fs-18 font-weight-bold mt-4 mb-5">{{__('Upload Photos of your Avatar')}}</h6>
											<div id="image-drop-box">
												<div class="image-drop-area text-center mt-2 file-drop-border p-6">
													<input type="file" class="main-image-input" name="file" id="file" accept=".png, .jpeg, .jpg" required>
													<div class="image-drop-icon mb-4">
														<img src="https://static.heygen.ai/heygen/hybrid_avatar/virtual-avatar-uploader.svg" alt="Uploader Icon">
													</div>
													<p class="text-dark fw-bold mb-4 mt-4">
														{{ __('Drag and drop photos to upload or') }}
														<a href="javascript:void(0);" class="text-primary">{{ __('Browse') }}</a>
													</p>
													<p class="mb-5 file-name fs-12 text-muted">
														<small>{{ __('Upload PNG, JPG') }}</small><br>
														<small>{{ __('Select recent photos of yourself (just you), showing close-up photo of your face') }}</small><br>
													</p>
													<div id="uploaded-samples"></div>
												</div>
											</div>											
										</div>
										<div class="text-center" style="position: relative; z-index:100">
											<a id="avatar-upload-button" class="avatar-action-btn text-center mt-6 mb-5 ml-auto mr-auto" href="#">{{ __('Upload Photo') }}</a>
										</div>
									</div>
									
								</div>	
							</form>								
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
	let loading = `<span class="loading">
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						</span>`;
		$(function () {

			"use strict";

			$("input[type=file]").on('change',function(){
				$('#uploaded-samples').html('');

				let newRow = '<div class="sample-line">' +
								'<div class="fs-12 text-muted mb-2" style="position:relative">'+
									'<span>'+ this.files[0].name + '</span>' +
								'</div>' +								
							'</div>';

				$("#uploaded-samples").append(newRow);			

			});


			$('#avatar-upload-button').on('click',function(e) {

				e.preventDefault();

				const form = document.getElementById("create-avatar-form");
				let data = new FormData(form);

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "POST",
					url: '/app/user/avatar/list/image-avatars/create',
					data: data,
					processData: false,
					contentType: false,
					beforeSend: function() {
						$('#avatar-upload-button').prop('disabled', true);
						let btn = document.getElementById('avatar-upload-button');					
						btn.innerHTML = loading;  
						document.querySelector('#loader-line')?.classList?.remove('hidden');  
						$('#uploaded-samples').html('');     
					},
					complete: function() {
						$('#avatar-upload-button').prop('disabled', false);
						let btn = document.getElementById('avatar-upload-button');					
						btn.innerHTML = '{{ __('Upload Photo') }}';
						document.querySelector('#loader-line')?.classList?.add('hidden');               
					},
					success: function(data) {
						if (data == 200) {
							toastr.success('{{ __('Photo successfully uploaded and can be used for video') }}');
						} else {
							toastr.warning(data['message']);
						}
					},
					error: function(data) {
						toastr.error(data);
						$('#avatar-upload-button').prop('disabled', false);
						let btn = document.getElementById('avatar-upload-button');					
						btn.innerHTML = '{{ __('Upload Photo') }}';
						document.querySelector('#loader-line')?.classList?.add('hidden');   
					}
				}).done(function(data) {})
			});

		});
	</script>
@endsection
