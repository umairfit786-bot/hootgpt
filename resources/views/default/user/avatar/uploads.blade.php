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
									<a class="side-menu__item active" href="{{ route('user.extension.avatar.uploads') }}">
									<span class="side-menu__icon fa-solid fa-cloud-arrow-up"></span>
									<span class="side-menu__label">{{ __('Uploads') }}</span></a>
								</li>
							</ul>
						</div>
						<div class="col-lg-10 col-md-9 col-sm-12">
							<form id="asset-form" action="" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="row justify-content-center">
									<div class="col-sm-12">
										<div class="input-box">
											<div id="image-drop-box">
												<div class="image-drop-area text-center mt-2 file-drop-border p-6">
													<input type="file" class="main-image-input" name="file" id="file" accept="image/jpeg, image/png, video/mp4, video/webm, audio/mpeg" required>
													<div class="image-drop-icon mb-4">
														<img src="https://static.heygen.ai/heygen/hybrid_avatar/virtual-avatar-uploader.svg" alt="Uploader Icon">
													</div>
													<p class="text-dark fw-bold mb-4 mt-4">
														{{ __('Drag and drop your assets or') }}
														<a href="javascript:void(0);" class="text-primary">{{ __('Browse') }}</a>
													</p>
													<p class="mb-5 file-name fs-12 text-muted">
														<small>{{ __('Upload Images, Videos and Audio') }}</small><br>
														<small>{{ __('These assets will be your video background images, background videos and voiceovers') }}</small><br>
													</p>
													<div id="uploaded-samples"></div>
													<div class="text-center" style="position: relative; z-index:100">
														<a id="avatar-upload-button" class="avatar-action-btn text-center mt-6 ml-auto mr-auto" href="#">{{ __('Upload Asset') }}</a>
													</div>
												</div>
											</div>											
										</div>										
									</div>

									<div class="col-sm-12 mt-6">
										<div>
											@if ($total->count()) 
												<div class="row">
													<h6 class="font-weight-bold mb-4">{{__('Image Backgrounds')}}</h6>
													<div class="row">
														@if ($images->count())
															@foreach ($images as $upload)
																@if ($upload->file_type == 'image')
																	<div class="col-md-3 col-sm-6">
																		<div class="card avatar-card mb-6" data-id="{{ $upload->file_id}}">
																			<div class="avatar-card-box">																			
																				<div class="avatar-image-box background-photos">
																					<img src="{{ $upload->file_url}}" class="avatar-image" id="{{ $upload->file_id }}_image">												
																				</div>
																				<div class="avatar-info text-center pb-4">
																					<p class="mb-0 fs-12">{{ $upload->original_name }}</p>
																				</div>
																			</div>							
																		</div>
																	</div>
																@endif																
															@endforeach
														@else
															<div class="text-center justify-content-center" style="min-height: 30px; align-items: center; display: flex">
																<div class="">
																	<img src="{{ theme_url('img/files/empty-basket.png') }}" alt="" style="max-width: 100px">
																	<p class="text-muted fs-13">{{ __('You have no background images uploaded yet') }}</p>
																</div>												
															</div>
														@endif														
													</div>
												</div>
												<hr>
												<div class="row mt-7">
													<h6 class="font-weight-bold mb-4">{{__('Video Backgrounds')}}</h6>
													<div class="row">
														@if ($videos->count())
															@foreach ($videos as $upload)
																@if ($upload->file_type == 'video')
																	<div class="col-md-3 col-sm-6">
																		<div class="card avatar-card mb-6" data-id="{{ $upload->file_id}}">
																			<div class="avatar-card-box">																				
																				<div class="avatar-image-box background-photos">
																					<video controls style="width: 100%; height: 100%; object-fit: cover">
																						<source src="{{ $upload->file_url }}" type="video/mp4">
																					</video>												
																				</div>
																				<div class="avatar-info text-center pb-4">
																					<p class="mb-0 fs-12">{{ $upload->original_name }}</p>
																				</div>
																			</div>							
																		</div>
																	</div>
																@endif																
															@endforeach
														@else
															<div class="text-center justify-content-center" style="min-height: 30px; align-items: center; display: flex">
																<div class="">
																	<img src="{{ theme_url('img/files/empty-basket.png') }}" alt="" style="max-width: 100px">
																	<p class="text-muted fs-13">{{ __('You have no background videos uploaded yet') }}</p>
																</div>												
															</div>
														@endif														
													</div>
												</div>
												<hr>
												<div class="row mt-7">
													<h6 class="font-weight-bold mb-4">{{__('Audio Files')}}</h6>
													<div class="row">
														@if ($audios->count())
															@foreach ($audios as $upload)
																@if ($upload->file_type == 'audio')
																	<div class="col-md-3 col-sm-6">
																		<div class="card mb-6 shadow-0 p-4 avatar-voice-samples-box">
																			<div class="d-flex avatar-voice-samples">
																				<div class="flex">
																					<button type="button" class="result-play text-center mr-2" title="{{__('Play Audio')}}" onclick="resultPlay(this)" src="{{ $upload->file_url }}" id="{{ $upload->file_id}}"><i class="fa fa-play table-action-buttons view-action-button"></i></button>											
																				</div>
																				<div class="flex mt-auto mb-auto">
																					<p class="mb-0 font-weight-bold fs-12">{{ $upload->original_name }}</p>
																				</div>
																																	
																			</div>							
																		</div>
																	</div>
																@endif
																
															@endforeach
														@else
															<div class="text-center justify-content-center" style="min-height: 30px; align-items: center; display: flex">
																<div class="">
																	<img src="{{ theme_url('img/files/empty-basket.png') }}" alt="" style="max-width: 100px">
																	<p class="text-muted fs-13">{{ __('You have no audio files uploaded yet') }}</p>
																</div>												
															</div>
														@endif	
													</div>
												</div>
											@else
												<div class="text-center justify-content-center" style="min-height: 450px; align-items: center; display: flex">
													<div class="">
														<img src="{{ theme_url('img/files/empty-basket.png') }}" alt="" style="max-width: 150px">
														<p class="text-muted fs-13">{{ __('You have no assets uploaded yet') }}</p>
													</div>												
												</div>												
											@endif
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
	<div class="image-modal">
		<div class="modal" id="image-view-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h6>{{ __('Background Image View') }}</h6>
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
	<script src="{{ URL::asset('plugins/audio-player/green-audio-player.js') }}"></script>
	<script src="{{ theme_url('js/audio-player.js') }}"></script>
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


			$(document).on('click', '.avatar-card', function(e) {
				let avatar_id = $(this).attr('data-id');
				let source = avatar_id + "_image";
				let src = document.getElementById(source).src;

				document.getElementById("selected-avatar-image").src = src;
				
				var myModal = new bootstrap.Modal(document.getElementById('image-view-modal'))
				myModal.show();		
			});


			$('#avatar-upload-button').on('click',function(e) {

				e.preventDefault();

				const form = document.getElementById("asset-form");
				let data = new FormData(form);

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: "POST",
					url: '/app/user/avatar/list/uploads',
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
							toastr.success('{{ __('Asset successfully uploaded') }}');
							window.location.reload();
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
