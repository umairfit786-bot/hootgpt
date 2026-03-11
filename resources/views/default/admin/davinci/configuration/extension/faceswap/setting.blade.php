@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Faceswap') }}</h4>
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
					<form action="{{ route('admin.davinci.configs.faceswap.store') }}" method="post" enctype="multipart/form-data">
						@csrf

						<div class="card shadow-0 mb-7">							
							<div class="card-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="input-box">								
												<h6>{{ __('Faceswap API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('faceswap_piapi_api') is-danger @enderror" id="faceswap_piapi_api" name="faceswap_piapi_api" value="{{ $extension->faceswap_piapi_api }}" autocomplete="off">
													@error('faceswap_piapi_api')
														<p class="text-danger">{{ $errors->first('faceswap_piapi_api') }}</p>
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
											<h6>{{ __('Faceswap Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="faceswap_feature" class="custom-switch-input" @if ($extension->faceswap_feature) checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Faceswap Free Tier Access') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="faceswap_free_tier" class="custom-switch-input" @if ($extension->faceswap_free_tier) checked @endif>
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
											<h6>{{ __('Media credits per Task') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>											
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="input-box">								
											<h6>{{ __('Faceswap task credits') }}</h6>
											<div class="form-group">							    
												<input type="number" min=1 class="form-control" name="faceswap" value="{{ $cost->faceswap }}" autocomplete="off">
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


