@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Paddle Settings') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Finance Settings') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<!-- ALL PAYMENT CONFIGURATIONS -->					
	<div class="row justify-content-center">

		<div class="col-lg-9 col-md-12 col-xm-12">

			<form action="{{ route('admin.finance.settings.paddle.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12 mb-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="enable-paddle" class="custom-switch-input" @if (config('services.paddle.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">Use Paddle Prepaid</span>
									</label>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group mb-4">
									<label class="custom-switch">
										<input type="checkbox" name="enable-paddle-subscription" class="custom-switch-input" @if (config('services.paddle.subscription')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">Use Paddle Subscription</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">
								<!-- SECRET ACCESS KEY -->
								<div class="input-box">								
									<h6>Paddle Vendor ID <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('paddle_vendor_id') is-danger @enderror" id="paddle_vendor_id" name="paddle_vendor_id" value="{{ config('services.paddle.vendor_id') }}" autocomplete="off">
									</div>
									@error('paddle_vendor_id')
										<p class="text-danger">{{ $errors->first('paddle_vendor_id') }}</p>
									@enderror
								</div> <!-- END SECRET ACCESS KEY -->
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">								
								<!-- ACCESS KEY -->
								<div class="input-box">								
									<h6>Paddle Vendor Auth Code <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('paddle_vendor_auth_code') is-danger @enderror" id="paddle_vendor_auth_code" name="paddle_vendor_auth_code" value="{{ config('services.paddle.vendor_auth_code') }}" autocomplete="off">
									</div> 
									@error('paddle_vendor_auth_code')
										<p class="text-danger">{{ $errors->first('paddle_vendor_auth_code') }}</p>
									@enderror
								</div> <!-- END ACCESS KEY -->
							</div>										

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Paddle Sandbox <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<select id="paddle_sandbox" name="paddle_sandbox" class="form-select" data-placeholder="{{ __('Enable/Disable Paddle Sandbox') }}:">			
										<option value=true @if (config('services.paddle.sandbox')  == true) selected @endif>{{ __('Enable') }}</option>
										<option value=false @if (config('services.paddle.sandbox')  == false) selected @endif>{{ __('Disable') }}</option>
									</select>
									@error('paddle_sandbox')
										<p class="text-danger">{{ $errors->first('paddle_sandbox') }}</p>
									@enderror
								</div> 
							</div>
							
						</div>

						<!-- SAVE CHANGES ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<a href="{{ route('admin.finance.settings') }}" class="btn ripple btn-cancel mr-2">{{ __('Return') }}</a>
							<button type="submit" class="btn ripple btn-primary">{{ __('Save') }}</button>							
						</div>
	
					</div>
				</div>
			</form>
				
		</div>
		
	</div>
	<!-- END ALL PAYMENT CONFIGURATIONS -->	

@endsection
