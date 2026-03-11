@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('AI Video') }} ({{__('Text to Video')}})</h4>
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
				<div class="card-body pb-6">									
					<form action="{{ route('admin.davinci.configs.video.text.store') }}" method="post" enctype="multipart/form-data">
						@csrf

						<div class="card shadow-0 mb-7">							
							<div class="card-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="input-box mb-4">								
												<h6>{{ __('Fal AI API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('video_text_falai_api') is-danger @enderror" id="video_text_falai_api" name="video_text_falai_api" value="{{ $extension->video_text_falai_api }}" autocomplete="off">
													@error('video_text_falai_api')
														<p class="text-danger">{{ $errors->first('video_text_falai_api') }}</p>
													@enderror
												</div> 												
											</div> 
										</div>

										<div class="col-sm-12">
											<div class="input-box mb-4">								
												<h6>{{ __('OpenAI API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('video_text_openai_api') is-danger @enderror" id="video_text_openai_api" name="video_text_openai_api" value="{{ $extension->video_text_openai_api }}" autocomplete="off">
													@error('video_text_openai_api')
														<p class="text-danger">{{ $errors->first('video_text_openai_api') }}</p>
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
											<h6>{{ __('AI Text to Video Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="video_text_feature" class="custom-switch-input" @if ($extension->video_text_feature) checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Text to Video Free Tier Access') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="video_text_free_tier" class="custom-switch-input" @if ($extension->video_text_free_tier) checked @endif>
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
											<h6>{{ __('OpenAI Sora 2 Pro task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" name="openai_sora_2_pro_video" value="{{ $cost->openai_sora_2_pro_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('OpenAI Sora 2 task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" name="openai_sora_2_video" value="{{ $cost->openai_sora_2_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Google Veo3 task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" name="google_veo3_video" value="{{ $cost->google_veo3_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 2.1 Master task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_21_master_video" name="kling_21_master_video" value="{{ $cost->kling_21_master_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>

									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Kling 1.6 Pro task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="kling_15_video" name="kling_15_video" value="{{ $cost->kling_15_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>	
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Haiper 2.0 Video task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="haiper_2_video" name="haiper_2_video" value="{{ $cost->haiper_2_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>	
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('MiniMax Video task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="minimax_video" name="minimax_video" value="{{ $cost->minimax_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Mochi 1 task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="mochi_1_video" name="mochi_1_video" value="{{ $cost->mochi_1_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Luma Dream Machine task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="luma_dream_machine_video" name="luma_dream_machine_video" value="{{ $cost->luma_dream_machine_video }}" autocomplete="off">
											</div> 												
										</div> 
									</div>
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Hunyuan Video task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" id="hunyuan_video" name="hunyuan_video" value="{{ $cost->hunyuan_video }}" autocomplete="off">
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


