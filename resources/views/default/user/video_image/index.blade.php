@extends('layouts.app')
@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Green Audio Players CSS -->
	<link href="{{ URL::asset('plugins/audio-player/green-audio-player.css') }}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
	<div class="row mt-24 justify-content-center">
		<div class="row no-gutters justify-content-center">
			<div class="col-lg-9 col-md-11 col-sm-12 text-center">
				<h3 class="card-title mt-2 fs-20"><i class="fa-solid fa-video mr-2 text-primary"></i></i>{{ __('AI Image to Video') }}</h3>
				<h6 class="text-muted mb-7">{{ __('Bring a life to your images with AI') }}</h6>
			</div>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-header pt-4 border-0">
					<p class="fs-11 text-muted mb-0 text-left"><i class="fa-solid fa-bolt-lightning mr-2 text-primary"></i>{{ __('Your Balance is') }} <span class="font-weight-semibold" id="balance-number">@if (auth()->user()->images == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->images + auth()->user()->images_prepaid) }}@endif {{ __('credits') }}</span></p>
				</div>
				<form id="video-form" action="" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="card-body pt-2 pl-6 pr-6 pb-5" id="">
						<div class="input-box">								
							<h6 class="text-muted">{{ __('Title') }}</h6>
							<div class="form-group">							    
								<input type="text" class="form-control" id="title" name="title" value="{{__('Untitled Video')}}">
							</div> 
						</div>
						<div class="input-box" style="position: relative">
							<h6 class="mb-0">{{ __('Target Image') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
							<div id="image-drop-box">
								<div class="image-drop-area text-center mt-2 file-drop-border">
									<input type="file" class="main-image-input" name="image" id="image" accept="image/png, image/jpeg" onchange="loadImage(event)" required>
									<div class="image-upload-icon">
										<i class="fa-solid fa-image-landscape fs-28 text-muted"></i>
									</div>
									<p class="text-dark fw-bold mb-0 mt-1">
										{{ __('Drag and drop your image or') }}
										<a href="javascript:void(0);" class="text-primary">{{ __('Browse') }}</a>
									</p>
									<p class="mb-5 file-name fs-12 text-muted">
										<small>{{ __('PNG | JPG | WEBP') }}</small>
									</p>
								</div>

								<img id="source-image-variations" class="mb-4">
							</div>
						</div>

						<div class="photo-studio-tools mb-5">
							<div class="nav-item dropdown w-100">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-display="static" data-bs-toggle="dropdown" aria-expanded="false">
									<span class="dropdown-item-icon mr-3 ml-1" id="active-template-icon"><i class="fa-solid fa-circle-video"></i></span>
									<h6 class="dropdown-item-title fs-13 font-weight-semibold" id="active-template-name">{{ __('Kling 1.6 Pro') }}</h6>	
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
									<a class="dropdown-item d-flex" href="#"  id="kling-video-21-master" name="{{ __('Kling 2.1 Master') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 2.1 Master') }} <span class="fs-9 text-muted">({{ $credits->kling_21_master_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="kling-video-21-pro" name="{{ __('Kling 2.1 Pro') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 2.1 Pro') }} <span class="fs-9 text-muted">({{ $credits->kling_21_pro_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="kling-video-21-standard" name="{{ __('Kling 2.1 Standard') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 2.1 Standard') }} <span class="fs-9 text-muted">({{ $credits->kling_21_standard_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>										
									<a class="dropdown-item d-flex" href="#"  id="kling-video" name="{{ __('Kling 1.6 Pro') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 1.6 Pro') }} <span class="fs-9 text-muted">({{ $credits->kling_15_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>	
									<a class="dropdown-item d-flex" href="#"  id="haiper-video-v2" name="{{ __('Haiper 2.5 Video') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Haiper 2.5 Video') }} <span class="fs-9 text-muted">({{ $credits->haiper_2_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>	
									<a class="dropdown-item d-flex" href="#"  id="luma-dream-machine" name="{{ __('Luma Dream Machine') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Luma Dream Machine') }} <span class="fs-9 text-muted">({{ $credits->luma_dream_machine_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="stable-diffusion" name="{{ __('Stable Diffusion') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Stable Diffusion') }} <span class="fs-9 text-muted">({{ $credits->stable_diffusion_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>	
									<a class="dropdown-item d-flex" href="#"  id="google-veo2" name="{{ __('Google Veo2') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Google Veo2') }} <span class="fs-9 text-muted">({{ $credits->google_veo2_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>								
								</div>
							</div>
						</div>

						<div class="row" id="prompt">
							<div class="col-sm-12">	
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Prompt') }}</h6>							
									<textarea class="form-control" name="prompt" rows="5" id="prompt" placeholder="{{ __('Provide your video description...') }}"></textarea>	
								</div>											
							</div>
						</div>
							

						<div class="row" id="kling-video-task">
							<div class="col-sm-12">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration_kling" class="form-select">											
										<option value=5>5 ({{ __('seconds') }})</option>
										<option value=10>10 ({{ __('seconds') }})</option>																																																																																																																																	
									</select>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="input-box mb-2">	
									<h6 class="text-muted">{{ __('Aspect Ratio') }}</h6>
									<select  name="aspect_ratio_kling" class="form-select">	
										<option value='16:9'>16:9</option>
										<option value='9:16'>9:16</option>
										<option value='1:1'>1:1</option>																																																																		
									</select>
								</div>
							</div>
						</div>

						<div class="row hidden" id="haiper-video-task">
							<div class="col-sm-12">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration_haiper" class="form-select">											
										<option value=4>4 ({{ __('seconds') }})</option>
										<option value=6>6 ({{ __('seconds') }})</option>																																																																																																																																
									</select>
								</div>
							</div>
						</div>

						<div class="row hidden" id="luma-task">
							<div class="col-sm-12">
								<div class="input-box mb-2">	
									<h6 class="text-muted">{{ __('Aspect Ratio') }}</h6>
									<select  name="aspect_ratio_luma" class="form-select">	
										<option value='16:9'>16:9</option>
										<option value='9:16'>9:16</option>
										<option value='4:3'>4:3</option>																																																																		
										<option value='3:4'>3:4</option>																																																																		
										<option value='21:9'>21:9</option>																																																																		
										<option value='9:21'>9:21</option>																																																																		
									</select>
								</div>
							</div>
						</div>		
						
						<div class="row" id="google-veo2-task">
							<div class="col-sm-12">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration_veo2" class="form-select">											
										<option value='5s'>5 ({{ __('seconds') }})</option>
										<option value='6s'>6 ({{ __('seconds') }})</option>																																																																																																																																	
										<option value='7s'>7 ({{ __('seconds') }})</option>																																																																																																																																	
										<option value='8s'>8 ({{ __('seconds') }})</option>																																																																																																																																	
									</select>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="input-box mb-2">	
									<h6 class="text-muted">{{ __('Aspect Ratio') }}</h6>
									<select  name="aspect_ratio_veo2" class="form-select">	
										<option value='auto'>{{__('Auto')}}</option>
										<option value='auto_prefer_portrait'>{{__('Portrait')}}</option>
										<option value='16:9'>16:9</option>																																																																		
										<option value='9:16'>9:16</option>																																																																		
									</select>
								</div>
							</div>
						</div>

						<div class="row hidden" id="stable-diffusion-task">
							<div class="col-sm-12">
								<div class="input-box">	
									<h6>{{ __('Seed') }} <i class="ml-1 text-dark fs-12 fa-solid fa-circle-info" data-tippy-content="{{ __('A specific value that is used to guide the randomness of the generation. Use 0 to get a random seed.') }}"></i></h6>
									<input type="number" class="form-control" name="seed" value="0">
								</div>		
							</div>
							<div class="col-lg-6 col-md-12 col-sm-12">
								<div class="video-settings-wrapper">
									<div id="form-group" class="mb-5 mt-3">
										<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Image Strength') }} <i class="ml-1 text-dark fs-12 fa-solid fa-circle-info" data-tippy-content="{{ __('How strongly the video sticks to the original image. Use lower values to allow the model more freedom to make changes and higher values to correct motion distortions.') }}"></i></h6>
										<div class="range">
											<div class="range_in">
												<input type="range" min="1" max="10" value="2" name="cfg_scale">
												<div class="slider" style="width: 20%;"></div>
											</div>
											<div class="value">2</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-12 col-sm-12">
								<div class="video-settings-wrapper">
									<div id="form-group" class="mb-5 mt-3">
										<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Motion Bucket') }} <i class="ml-1 text-dark fs-12 fa-solid fa-circle-info" data-tippy-content="{{ __('Lower values generally result in less motion in the output video, while higher values generally result in more motion.') }}"></i></h6>
										<div class="range">
											<div class="range_in">
												<input type="range" min="1" max="255" value="127" name="motion_bucket_id">
												<div class="slider" style="width: 50%;"></div>
											</div>
											<div class="value">127</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						

						<div class="text-center mt-3 mb-2">
							<button type="submit" class="btn btn-primary ripple main-action-button" id="generate" style="text-transform: none; min-width: 200px;">{{ __('Generate') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="col-lg-6 col-md-8 col-xm-12">
			@if ($results->count())
				<div id="photo-studio-result">		
					<div class="row" id="results-container">
						@foreach ($results as $result)
							<div class="col-md-6 col-sm-12">
								<div class="card p-4 border-0">
									<video controls>
										<source src="{{$result->url}}" type="video/mp4">
									</video>
									<div class="text-center mt-3 relative">
										<h6 class="mb-1 font-weight-semibold">{{$result->title}}</h6>
										<p class="text-muted fs-12 mb-1">{{date('M d, Y', strtotime($result->created_at))}}</p>
										@if ($result->status == 'processing')
											<p class="text-muted fs-12 mb-0">({{__('Processing')}})</p>
										@endif 										
										<a href="" class="avatar-result-delete" data-id="{{ $result->id }}" data-tippy-content="{{ __('Delete Video Result') }}"><i class="fa-solid fa-trash-xmark"></i></a>		
									</div>
								</div>
							</div>
						@endforeach
						
					</div>									
				</div>
			@else
				<div id="photo-studio-placeholder" class="text-center">
					<span><i class="fa-brands fa-youtube fs-40 mb-2 text-muted"></i></span>
					<h6 class="text-muted">{{ __('Start generating your video') }}</h6>
				</div>
				<div id="photo-studio-result" class="hidden">		
					<div id="results-container">
						
					</div>									
				</div>
			@endif
			
		</div>	

	</div>
</div>
@endsection
@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		let active_task = 'kling-video';
		let loading = `<span class="loading">
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						</span>`;

		$(function () {

			"use strict";

			$(".range").each(function() {
				let t = $(this),
					a = t.find("input"),
					o = a.val(),
					n = t.find(".value"),
					s = a.attr("min"),
					i = a.attr("max"),
					r = t.find(".slider");
				r.css({
					width: o * (100 * s) / i + "%"
				}), a.on("input", function() {
					o = $(this).val(), n.text(o), r.css({
						width: o * (100 * s) / i + "%"
					})
				})
			});
		});	

		var loadImage = function(event) {
			var output = document.getElementById('source-image-variations');
			output.style.display = 'block';
			output.src = URL.createObjectURL(event.target.files[0]);
			output.onload = function() {
				URL.revokeObjectURL(output.src) // free memory
			}
		};



		$('.photo-studio-tools .dropdown .dropdown-menu .dropdown-item').click(function(e){
			e.preventDefault();

			let task = $(this).attr('id');
			let name = $(this).attr('name');
			let icon = $(this).attr('icon');
			let template_icon = document.getElementById('active-template-icon');
			let template_name = document.getElementById('active-template-name');
			active_task = task;
			template_name.innerHTML = name;
			template_icon.innerHTML = icon;

			if (task == 'kling-video' || task == 'kling-video-21-standard' || task == 'kling-video-21-pro' || task == 'kling-video-21-master') {
				$('#prompt').removeClass('hidden');
				$('#kling-video-task').removeClass('hidden');
				$('#haiper-video-task').addClass('hidden');
				$('#stable-diffusion-task').addClass('hidden');
				$('#luma-task').addClass('hidden');
				$('#google-veo2-task').addClass('hidden');
			}

			if (task == 'stable-diffusion') {
				$('#prompt').addClass('hidden');
				$('#kling-video-task').addClass('hidden');
				$('#haiper-video-task').addClass('hidden');
				$('#stable-diffusion-task').removeClass('hidden');
				$('#luma-task').addClass('hidden');
				$('#google-veo2-task').addClass('hidden');
			}

			if (task == 'haiper-video-v2') {
				$('#prompt').removeClass('hidden');
				$('#kling-video-task').addClass('hidden');
				$('#haiper-video-task').removeClass('hidden');
				$('#stable-diffusion-task').addClass('hidden');
				$('#luma-task').addClass('hidden');
				$('#google-veo2-task').addClass('hidden');
			} 

			if (task == 'luma-dream-machine') {
				$('#prompt').removeClass('hidden');
				$('#luma-task').removeClass('hidden');
				$('#kling-video-task').addClass('hidden');
				$('#haiper-video-task').addClass('hidden');
				$('#stable-diffusion-task').addClass('hidden');
				$('#google-veo2-task').addClass('hidden');
			}

			if (task == 'google-veo2') {
				$('#prompt').removeClass('hidden');
				$('#luma-task').addClass('hidden');
				$('#kling-video-task').addClass('hidden');
				$('#haiper-video-task').addClass('hidden');
				$('#stable-diffusion-task').addClass('hidden');
				$('#google-veo2-task').removeClass('hidden');
			}

		});


		// SUBMIT FORM
		$('#video-form').on('submit', function(e) {

			e.preventDefault();

			let form = new FormData(this);
			form.append('model', active_task);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: '/app/user/video/create',
				data: form,
				contentType: false,
				processData: false,
				cache: false,
				beforeSend: function() {
					$('#generate').prop('disabled', true);
					let btn = document.getElementById('generate');					
					btn.innerHTML = loading;  
					document.querySelector('#loader-line')?.classList?.remove('hidden');      
				},
				complete: function() {
					document.querySelector('#loader-line')?.classList?.add('hidden'); 
					$('#generate').prop('disabled', false);
					$('#generate').html('{{ __("Generate") }}');            
				},
				success: function (data) {		
						
					if (data['status'] == 'success') {		

						$('#photo-studio-placeholder').addClass('hidden');
						$('#photo-studio-result').removeClass('hidden');

						$('#results-container').prepend(data['result']);

						toastr.success(data['message']);	
						
					} else {						
						Swal.fire('{{ __('Video Generation Error') }}', data['message'], 'warning');
					}
				},
				error: function(data) {
					$('#image-generate').prop('disabled', false);
					$('#image-generate').html('<i class=" fa-solid fa-wand-magic-sparkles mr-2"></i>{{ __("Generate") }}'); 
				}
			});
		});


		$(document).on('click', '.avatar-result-delete', function(e) {

			e.preventDefault();

			let formData = new FormData();
			formData.append("id", $(this).attr('data-id'));
			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: '/app/user/video/delete',
				data: formData,
				processData: false,
				contentType: false,
				success: function (data) {
					console.log(data)
					if (data == 200) {
						toastr.success('{{ __('Video result has been deleted successfully') }}');	
						window.location.reload();							
					} else {
						toastr.warning('{{ __('There was an error deleting the result file') }}');
					}      
				},
				error: function(data) {
					toastr.warning('{{ __('There was an error deleting the result file') }}');
				}
			})
		});
		
	</script>
@endsection