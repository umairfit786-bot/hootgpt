@extends('layouts.app')

@section('content')	
	<div class="row justify-content-center mt-5-7">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body p-6">
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
									<a class="side-menu__item" href="{{ route('user.extension.avatar.uploads') }}">
									<span class="side-menu__icon fa-solid fa-cloud-arrow-up"></span>
									<span class="side-menu__label">{{ __('Uploads') }}</span></a>
								</li>
	
							</ul>
						</div>
						<div class="col-lg-10 col-md-9 col-sm-12">
							<div class="row">
								<div class="col-lg-5 col-sm-12">
									<h6 class="fs-12 plan-title-bar"><span class="font-weight-bold">{{__('Step 1')}}</span> : {{__('Select your Avatar Video')}}</h6>
									<div class="avatar-images-border-line">
										<div class="row p-4">
											@foreach ($avatars as $avatar)
												@if(in_array($avatar->avatar_id , $favorites))
													<div class="col-md-6 col-sm-12">
														<div class="card avatar-card mb-6" data-id="{{ $avatar->avatar_id }}">
															<div class="avatar-card-box">
																<a href="#" class="avatar-favorite marked-favorite" data-id="{{ $avatar->avatar_id }}"><i class="fa-solid fa-heart text-muted"></i></a>
																<div class="avatar-image-box">
																	<img src="{{ $avatar->preview_image_url}}" class="avatar-image" id="{{ $avatar->avatar_id }}_image">												
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
													<div class="col-md-6 col-sm-12">
														<div class="card avatar-card mb-6" data-id="{{ $avatar->avatar_id }}">
															<div class="avatar-card-box">
																<a href="#" class="avatar-favorite" data-id="{{ $avatar->avatar_id }}"><i class="fa-solid fa-heart text-muted"></i></a>
																<div class="avatar-image-box">
																	<img src="{{ $avatar->preview_image_url}}" class="avatar-image" id="{{ $avatar->avatar_id }}_image">												
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

								<div class="col-lg-7 col-sm-12">
									<form id="generate-avatar-form" action="{{ route('user.extension.avatar.video.create.store') }}" method="POST" enctype="multipart/form-data">
										@csrf
										<div class="ml-5 mr-0 mb-5 mt-0">
											<div class="selected-avatar-image-box">
												<img src="" alt="" id="selected-avatar-image">
											</div>

											<h6 class="fs-12 plan-title-bar"><span class="font-weight-bold">{{__('Step 2')}}</span> : {{__('Select your voice and enter script text')}}</h6>
											<div class="avatar-input-text mb-6">
												<div id="form-group" class="mb-5">
													<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Audio Voice') }}</h6>
													<div class="d-flex">
														<div class="flex w-100">
															<select id="voice" name="voice" class="form-select" onchange="voice_select()">
																@foreach ($favorite_voices as $favorite)
																	@foreach ($voices['data']['voices'] as $voice)
																		@if ($favorite == $voice['voice_id'])
																			<option value={{$voice['voice_id']}} data-src={{$voice['preview_audio']	}} id={{$voice['voice_id']}}>{{ $voice['language']}} - {{$voice['name']}} ({{$voice['gender']}}) ({{__('Favorite')}})</option>
																		@endif
																	@endforeach
																@endforeach
																@foreach ($voices['data']['voices'] as $voice)
																	@if(!in_array($voice['voice_id'], $favorite_voices))
																		<option value={{$voice['voice_id']}} data-src={{$voice['preview_audio']	}} id={{$voice['voice_id']}}>{{ $voice['language']}} - {{$voice['name']}} ({{$voice['gender']}})</option>
																	@endif
																@endforeach
															</select>
														</div>
														<div class="flex">
															<button class="btn btn-special create-project ml-4" type="button" onclick="previewPlay(this)" 
															@foreach ($voices['data']['voices'] as $voice)
																@if ($loop->first)
																	src="{{$voice['preview_audio']	}}" 
																@else
																	@break
																@endif																
															@endforeach															
															type="audio/mpeg" id="preview" data-tippy-content="{{ __('Preview Selected Voice') }}"><i class="fa-solid fa-volume-high"></i></button>
														</div>
													</div>
												</div>
												<div class="input-box mb-0">	
													<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Video Script Text') }}</h6>									
													<div class="form-group">	
														<textarea class="form-control" name="text" id="text" rows="3" placeholder="Enter your text to voice script with up to 1500 characters long" required></textarea>
													</div>
												</div>
											</div>

											<h6 class="fs-12 plan-title-bar"><span class="font-weight-bold">{{__('Step 3')}}</span> : {{__('Set Your Video Settings')}}</h6>
											<div class="avatar-input-text">
												<div class="input-box mb-5">
													<div id="form-group">	
														<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Video Title') }}</h6>
														<input id="title" name="title" type="text" class="form-control" placeholder="Provide title for the video">
													</div>
												</div>

												<div id="form-group" class="mb-5">
													<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Video Dimension') }}</h6>
													<select id="dimension" name="dimension" class="form-select">													
														<option value="landscape">{{__('Landscape')}}</option>			
														<option value="portrait">{{__('Portrait')}}</option>			
													</select>
												</div>

												<div id="form-group" class="mb-5">
													<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Avatar Style') }}</h6>
													<select id="avatar_style" name="avatar_style" class="form-select">													
														<option value="normal">{{__('Normal')}}</option>			
														<option value="closeUp">{{__('closeUp')}}</option>			
														<option value="circle">{{__('Circle')}}</option>			
													</select>
												</div>

												<div class="avatar-input-text">
													<div class="input-box mb-4">	
														<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Background Color') }}</h6>
														<div class="form-group" style="position: relative">
															<input type="color" id="colorPicker" style="position: absolute; top: 8px; left: 1rem;width: 30px" value="#007bff">
															<input id="background_color" name="background_color" type="text" class="form-control" value="#007bff" style="padding-left: 3.5rem;">
														</div>
													</div>		

													<p class="text-muted fs-12 text-center mb-0">{{__('or')}}</p>
													<div id="form-group" class="mb-5">	
														<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Background Image') }}</h6>
														<select id="background_image" name="background_image" class="form-select">																
															<option value="none">{{__('None')}}</option>
															@foreach ($backgrounds as $background)
																<option value="{{$background->file_id}}">{{ $background->original_name}}</option>
															@endforeach					
														</select>
													</div>

													<p class="text-muted fs-12 text-center mb-0">{{__('or')}}</p>
													<div class="input-box mb-0">
														<div id="form-group">	
															<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Background Image URL') }}</h6>
															<input id="background_image_url" name="background_image_url" type="text" class="form-control" placeholder="Enter your image url link">
														</div>
													</div>
												</div>
		
											</div>
										</div>
										<div class="border-0 text-center mb-4 mt-1">
											<button type="button" class="btn ripple btn-primary pl-9 pr-9 pt-3 pb-3 fs-12" style="min-width: 300px;" id="generate">{{ __('Generate Video') }}</button>							
										</div>	
									</form>
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
	let loading = `<span class="loading">
					<span style="background-color: #fff;"></span>
					<span style="background-color: #fff;"></span>
					<span style="background-color: #fff;"></span>
					</span>`;
		$(function () {			

			"use strict";
			let avatar_id = '';
			const colorPicker = document.getElementById('colorPicker');
        	const colorValue = document.getElementById('background_color');

			colorPicker.addEventListener('input', function() {
				colorValue.value = colorPicker.value;
			});


			$(document).on('click', '.avatar-card', function(e) {
				avatar_id = $(this).attr('data-id');
				let source = avatar_id + "_image";
				let src = document.getElementById(source).src;
				
				document.getElementById("selected-avatar-image").src = src;
			});


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
						if (data == 'added') {
							toastr.success('{{ __('Avatar added to the favorite list successfully') }}');							
						} else if (data == 'removed') {
							toastr.success('{{ __('Avatar removed from favorite list successfully') }}');
						} else {
							toastr.warning('{{ __('There was an error editing avatar favorite status') }}');
						}      
					},
					error: function(data) {
						toastr.warning('{{ __('There was an error editing avatar favorite status') }}');
					}
				})
			});


			$('#generate').on('click',function(e) {

				e.preventDefault();

				const form = document.getElementById("generate-avatar-form");
				let formData = new FormData(form);
				if (avatar_id == '') {
					toastr.warning('{{ __('Make sure to select your avatar image first') }}');
				} else {
					formData.append("avatar_id", avatar_id);

					$.ajax({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						method: 'POST',
						url: $('#generate-avatar-form').attr('action'),
						data: formData,
						processData: false,
						contentType: false,
						beforeSend: function() {					
							$('#generate').prop('disabled', true);
							let btn = document.getElementById('generate');					
							btn.innerHTML = loading;  
							document.querySelector('#loader-line')?.classList?.remove('opacity-on');
						},		
						success: function(data) {

							$('#generate').prop('disabled', false);
							let btn = document.getElementById('generate');					
							btn.innerHTML = '{{ __('Generate Video') }}';
							document.querySelector('#loader-line')?.classList?.add('hidden'); 

							if (data == 200) {
								toastr.success('{{ __('Video generation task successfully created') }}');
							}

							if (data == 502) {
								toastr.success('{{ __('Not enough credits to generate Avatar from Video, please upgrade') }}');
							}

						},
						error: function(data) {
							toastr.error('{{ __('There was an issue with generating video task') }}');
						}
					}).done(function(data) {})
				}
				
			});
		});


		let audio = new Audio();
		let current = '';
		function voice_select() {
			let voice = document.getElementById("voice").value;
			let url = $('#' + voice).attr('data-src');
			document.getElementById('preview').setAttribute("src", url);
		}


		function previewPlay(element){

			let src = $(element).attr('src');
			let type = $(element).attr('type');
			let id = $(element).attr('id');

			let isPlaying = false;

			audio.src = src;
			audio.type= type;    

			if (current == id) {
				audio.pause();
				isPlaying = false;
				document.getElementById(id).innerHTML = '<i class="fa-solid fa-volume-high"></i>';
				current = '';

			} else {    
				if(isPlaying) {
					audio.pause();
					isPlaying = false;
					document.getElementById(id).innerHTML = '<i class="fa-solid fa-volume-high"></i>';
					current = '';
				} else {
					audio.play();
					isPlaying = true;
					if (current) {
						document.getElementById(current).innerHTML = '<i class="fa-solid fa-volume-high"></i>';
					}
					document.getElementById(id).innerHTML = '<i class="fa-solid fa-volume-slash"></i>';
					current = id;
				}
			}

			audio.addEventListener('ended', (event) => {
				document.getElementById(id).innerHTML = '<i class="fa-solid fa-volume-high"></i>';
				isPlaying = false;
				current = '';
			});      
				
		}
	</script>
@endsection



