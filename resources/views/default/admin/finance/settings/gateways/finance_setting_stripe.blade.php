@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Stripe Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.stripe.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12 mb-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="enable-stripe" class="custom-switch-input" @if (config('services.stripe.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Use Stripe Prepaid') }}</span>
									</label>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group mb-4">
									<label class="custom-switch">
										<input type="checkbox" name="enable-stripe-subscription" class="custom-switch-input" @if (config('services.stripe.subscription')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Use Stripe Subscription') }}</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">								
								<!-- ACCESS KEY -->
								<div class="input-box">								
									<h6>Stripe Key <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('stripe_key') is-danger @enderror" id="stripe_key" name="stripe_key" value="{{ config('services.stripe.api_key') }}" autocomplete="off">
									</div> 
									@error('stripe_key')
										<p class="text-danger">{{ $errors->first('stripe_key') }}</p>
									@enderror
								</div> <!-- END ACCESS KEY -->
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<!-- SECRET ACCESS KEY -->
								<div class="input-box">								
									<h6>Stripe Secret Key <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('stripe_secret_key') is-danger @enderror" id="stripe_secret_key" name="stripe_secret_key" value="{{ config('services.stripe.api_secret') }}" autocomplete="off">
									</div> 
									@error('stripe_secret_key')
										<p class="text-danger">{{ $errors->first('stripe_secret_key') }}</p>
									@enderror
								</div> <!-- END SECRET ACCESS KEY -->
							</div>	
							
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Stripe Webhook URI <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('stripe_webhook_uri') is-danger @enderror" id="stripe_webhook_uri" name="stripe_webhook_uri" value="{{ config('services.stripe.webhook_uri') }}" autocomplete="off">
									</div> 
									@error('stripe_webhook_uri')
										<p class="text-danger">{{ $errors->first('stripe_webhook_uri') }}</p>
									@enderror
								</div> 
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Stripe Webhook Secret <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('stripe_webhook_secret') is-danger @enderror" id="stripe_webhook_secret" name="stripe_webhook_secret" value="{{ config('services.stripe.webhook_secret') }}" autocomplete="off">
									</div> 
									@error('stripe_webhook_secret')
										<p class="text-danger">{{ $errors->first('stripe_webhook_secret') }}</p>
									@enderror
								</div> 
							</div>
							
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Stripe Base URI <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('stripe_base_uri') is-danger @enderror" id="stripe_base_uri" name="stripe_base_uri" value="{{ config('services.stripe.base_uri') }}" autocomplete="off">
									</div> 
									@error('stripe_base_uri')
										<p class="text-danger">{{ $errors->first('stripe_base_uri') }}</p>
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
