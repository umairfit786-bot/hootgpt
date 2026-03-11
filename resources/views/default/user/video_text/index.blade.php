@extends('layouts.app')
@section('css')
	<!-- Green Audio Players CSS -->
	<link href="{{ URL::asset('plugins/audio-player/green-audio-player.css') }}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
	<div class="row mt-24 justify-content-center">

		<div class="row no-gutters justify-content-center">
			<div class="col-lg-9 col-md-11 col-sm-12 text-center">
				<h3 class="card-title mt-2 fs-20"><i class="fa-solid fa-video-plus mr-2 text-primary"></i></i>{{ __('AI Text to Video') }}</h3>
				<h6 class="text-muted mb-7">{{ __('State-of-the-art AI Video processing for virtually any video content') }}</h6>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12">
			<div class="card border-0">
				<div class="card-header pt-4 border-0">
					<p class="fs-11 text-muted mb-0 text-left"><i class="   fa-solid fa-bolt-lightning mr-2 text-primary"></i>{{ __('Your Balance is') }} <span class="font-weight-semibold" id="balance-number">@if (auth()->user()->images == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->images + auth()->user()->images_prepaid) }}@endif {{ __('credits') }}</span></p>
				</div>
				<form id="video-form" action="" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="card-body pt-2 pl-6 pr-6 pb-5" id="">
						<div class="photo-studio-tools mb-5">
							<div class="nav-item dropdown w-100">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-display="static" data-bs-toggle="dropdown" aria-expanded="false">
									<span class="dropdown-item-icon mr-3 ml-1" id="active-template-icon"><i class="fa-solid fa-circle-video"></i></span>
									<h6 class="dropdown-item-title fs-13 font-weight-semibold" id="active-template-name">{{ __('Kling 1.6 Pro') }}</h6>	
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
									<a class="dropdown-item d-flex" href="#"  id="openai-sora-2-pro" name="{{ __('OpenAI Sora 2 Pro') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('OpenAI Sora 2 Pro') }} <span class="fs-9 text-muted">({{ $credits->openai_sora_2_pro_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="openai-sora-2" name="{{ __('OpenAI Sora 2') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('OpenAI Sora 2') }} <span class="fs-9 text-muted">({{ $credits->openai_sora_2_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="google-veo3" name="{{ __('Google Veo3') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Google Veo3') }} <span class="fs-9 text-muted">({{ $credits->google_veo3_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="kling-video-21-master" name="{{ __('Kling 2.1 Master') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 2.1 Master') }} <span class="fs-9 text-muted">({{ $credits->kling_21_master_video_image }} {{ __('credits per video') }})</span></h6>										
									</a>										
									<a class="dropdown-item d-flex" href="#"  id="kling-video" name="{{ __('Kling 1.6 Pro') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Kling 1.6 Pro') }} <span class="fs-9 text-muted">({{ $credits->kling_15_video }} {{ __('credits per video') }})</span></h6>										
									</a>	
									<a class="dropdown-item d-flex" href="#"  id="haiper-video-v2" name="{{ __('Haiper 2.5 Video') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Haiper 2.5 Video') }} <span class="fs-9 text-muted">({{ $credits->haiper_2_video }} {{ __('credits per video') }})</span></h6>										
									</a>	
									<a class="dropdown-item d-flex" href="#"  id="minimax-video" name="{{ __('MiniMax Video') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('MiniMax Video') }} <span class="fs-9 text-muted">({{ $credits->minimax_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="mochi-v1" name="{{ __('Mochi 1') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Mochi 1') }} <span class="fs-9 text-muted">({{ $credits->mochi_1_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="luma-dream-machine" name="{{ __('Luma Dream Machine') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Luma Dream Machine') }} <span class="fs-9 text-muted">({{ $credits->luma_dream_machine_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									<a class="dropdown-item d-flex" href="#"  id="hunyuan-video" name="{{ __('Hunyuan Video') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('Hunyuan Video') }} <span class="fs-9 text-muted">({{ $credits->hunyuan_video }} {{ __('credits per video') }})</span></h6>										
									</a>
									{{-- <a class="dropdown-item d-flex" href="#"  id="cogvideox-5b" name="{{ __('CogVideoX-5B') }}" icon="<i class='fa-solid fa-circle-video'></i>">
										<span class="dropdown-item-icon mr-3 ml-1 text-muted"><i class="fa-solid fa-circle-video"></i></span>
										<h6 class="dropdown-item-title fs-12">{{ __('CogVideoX-5B') }} <span class="fs-9 text-muted">({{ $credits->cogvideox_5b_video }} {{ __('credits per video') }})</span></h6>										
									</a> --}}
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">								
								<div class="input-box">								
									<h6 class="text-muted">{{ __('Title') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control" id="title" name="title" value="{{__('Untitled Video')}}">
									</div> 
								</div> 
							</div>
							<div class="col-sm-12">	
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Prompt') }}</h6>							
									<textarea class="form-control" name="prompt" rows="5" id="prompt" placeholder="{{ __('Provide your video description...') }}" required></textarea>	
								</div>											
							</div>	

							<div class="col-sm-12" id="duration_kling">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration" class="form-select">											
										<option value=5>5 ({{ __('seconds') }})</option>
										<option value=10>10 ({{ __('seconds') }})</option>																																																																				
									</select>
								</div>
							</div>

							<div class="col-sm-12" id="duration_sora">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration_sora" class="form-select">											
										<option value=4>4 ({{ __('seconds') }})</option>
										<option value=8>8 ({{ __('seconds') }})</option>																																																																				
										<option value=12>12 ({{ __('seconds') }})</option>																																																																				
									</select>
								</div>
							</div>

							<div class="col-sm-12 hidden" id="audio_veo">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Generate Audio') }}</h6>
									<select  name="audio_veo" class="form-select">												
										<option value=true>{{ __('Yes') }}</option>																																																																																							
										<option value=false>{{ __('No') }}</option>																																																																																							
									</select>
								</div>
							</div>

							<div class="col-sm-12 hidden" id="duration_haiper">
								<div class="input-box">	
									<h6 class="text-muted">{{ __('Duration') }}</h6>
									<select  name="duration_haiper" class="form-select">												
										<option value=4>4 ({{ __('seconds') }})</option>
										<option value=6>6 ({{ __('seconds') }})</option>																																																																																							
									</select>
								</div>
							</div>
	
							<div class="col-sm-12" id="aspect_ratio">
								<div class="input-box mb-2">	
									<h6 class="text-muted">{{ __('Aspect Ratio') }}</h6>
									<select  name="aspect_ratio" class="form-select">	
										<option value='16:9'>16:9</option>
										<option value='9:16'>9:16</option>
										<option value='1:1'>1:1</option>																																																																		
									</select>
								</div>
							</div>

							<div class="col-sm-12 hidden" id="aspect_ratio_luma">
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

							<div class="col-sm-12 hidden" id="aspect_ratio_sora">
								<div class="input-box mb-2">	
									<h6 class="text-muted">{{ __('Resolution') }}</h6>
									<select  name="aspect_ratio_sora" class="form-select">	
										<option value='1280x720'>1280x720</option>
										<option value='720x1280'>720x1280</option>
										<option value='1024x1792'>1024x1792</option>																																																																		
										<option value='1792x1024'>1792x1024</option>																																																																																																																																			
									</select>
								</div>
							</div>
						</div>							

						<div class="text-center mt-4 mb-2">
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
								<div class="card p-4">
									<video controls>
										<source src="{{$result->url}}" type="video/mp4">
									</video>
									<div class="text-center mt-3 relative">
										<h6 class="mb-1 font-weight-semibold">{{$result->title}}</h6>
										<p class="text-muted fs-12 mb-1">{{date('M d, Y', strtotime($result->created_at))}}</p>
										@if ($result->status == 'processing')
											<p class="text-muted fs-12 mb-0">({{__('Processing')}})</p>
										@else
											<p class="text-muted fs-12 mb-0">{{gmdate("H:i:s", $result->duration)}}</p>
										@endif 										
										<a href="" class="avatar-result-delete" data-id="{{ $result->id }}" data-tippy-content="{{ __('Delete Video Result') }}"><i class="fa-solid fa-trash-xmark"></i></a>	
										<a href="" class="avatar-result-prompt" data-tippy-content="Prompt: {{ $result->prompt }}"><i class="fa-solid text-muted fa-circle-info"></i></a>	
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
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		let active_task = 'kling-video';
		let loading = `<span class="loading">
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						</span>`;
	


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

			if (task == 'kling-video' || task == 'kling-video-21-master') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').removeClass('hidden');
				$('#aspect_ratio').removeClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'haiper-video-v2') {
				$('#duration_haiper').removeClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			} 

			if (task == 'minimax-video') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}
			
			if (task == 'mochi-v1') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'cogvideox-5b') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'luma-dream-machine') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').removeClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'hunyuan-video') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'google-veo3') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').removeClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').removeClass('hidden');
				$('#duration_sora').addClass('hidden');
				$('#aspect_ratio_sora').addClass('hidden');
			}

			if (task == 'openai-sora-2' || task == 'openai-sora-2-pro') {
				$('#duration_haiper').addClass('hidden');
				$('#duration_kling').addClass('hidden');
				$('#aspect_ratio').addClass('hidden');
				$('#aspect_ratio_luma').addClass('hidden');
				$('#audio_veo').addClass('hidden');
				$('#duration_sora').removeClass('hidden');
				$('#aspect_ratio_sora').removeClass('hidden');
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
				url: '/app/user/video/text/create',
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
					$('#image-generate').html('<i class="   fa-solid fa-wand-magic-sparkles mr-2"></i>{{ __("Generate") }}'); 
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
				url: '/app/user/video/text/delete',
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