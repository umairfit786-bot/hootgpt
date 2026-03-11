@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('AI Video') }} ({{__('Image to Video')}})</h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.configs')}}"> {{ __('AI Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Extensions') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-7 pl-7 pr-7 pb-6">									
					<form action="{{ route('admin.davinci.configs.video.image.store') }}" method="post" enctype="multipart/form-data">
						@csrf

						<div class="card shadow-0 mb-7">							
							<div class="card-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="input-box">								
												<h6>{{ __('Fal AI API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span> <i class="ml-4 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('For Kling, Haiper and Luma Dream video models you need to inlcude Fal AI API key') }}."></i></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('video_image_falai_api') is-danger @enderror" id="video_image_falai_api" name="video_image_falai_api" value="{{ $extension->video_image_falai_api }}" autocomplete="off">
													@error('video_image_falai_api')
														<p class="text-danger">{{ $errors->first('video_image_falai_api') }}</p>
													@enderror
												</div> 												
											</div>  
											<div class="input-box mb-3">								
												<h6>{{ __('Stability API Token') }} <i class="ml-1 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Include Stable Diffusion API key if you want to enable their Image to Video model') }}."></i></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('video_image_stability_api') is-danger @enderror" id="video_image_stability_api" name="video_image_stability_api" value="{{ $extension->video_image_stability_api }}" autocomplete="off">
													@error('video_image_stability_api')
														<p class="text-danger">{{ $errors->first('video_image_stability_api') }}</p>
													@enderror
												</div> 												
											</div> 
										</div>
									</div>
											
							</div>
						</div>
						
						<div class="card shadow-0 mt-0 mb-7">							
							<div class="card-body">
								<div class="row">

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Image to Video Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="video_image_feature" class="custom-switch-input" @if ($extension->video_image_feature) checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Image to Video Free Tier Access') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="video_image_free_tier" class="custom-switch-input" @if ($extension->video_image_free_tier) checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>	
								</div>		
							</div>
						</div>

						<div class="card shadow-0 mt-0 mb-7">							
							<div class="card-body">
								<div class="row">

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Video Model credits per Video') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>											
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Stable Diffusion task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="stable_diffusion_video_image" name="stable_diffusion_video_image" value="{{ $cost->stable_diffusion_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 2.1 Master task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_21_master_video_image" name="kling_21_master_video_image" value="{{ $cost->kling_21_master_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 2.1 Pro task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_21_pro_video_image" name="kling_21_pro_video_image" value="{{ $cost->kling_21_pro_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 2.1 Standard task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_21_standard_video_image" name="kling_21_standard_video_image" value="{{ $cost->kling_21_standard_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 1.6 Pro task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_15_video_image" name="kling_15_video_image" value="{{ $cost->kling_15_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Luma Dream Machine task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="luma_dream_machine_video_image" name="luma_dream_machine_video_image" value="{{ $cost->luma_dream_machine_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Haiper Video v2 task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="haiper_2_video_image" name="haiper_2_video_image" value="{{ $cost->haiper_2_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Google Veo2 task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" name="google_veo2_video_image" value="{{ $cost->google_veo2_video_image }}" autocomplete="off">
											</div> 												
										</div> 
									</div>
								</div>		
							</div>
						</div>
						

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-8 pr-8 pt-2 pb-2">{{ __('Save') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection


