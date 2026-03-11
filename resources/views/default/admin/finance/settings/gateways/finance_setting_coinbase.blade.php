@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Coinbase Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.coinbase.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="form-group mb-4">
									<label class="custom-switch">
										<input type="checkbox" name="enable-coinbase" class="custom-switch-input" @if (config('services.coinbase.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">Use Coinbase Prepaid</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">		
								<div class="input-box">								
									<h6>Coinbase API Key</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('coinbase_api_key') is-danger @enderror" id="coinbase_api_key" name="coinbase_api_key" value="{{ config('services.coinbase.api_key') }}" autocomplete="off">
									</div>
										@error('coinbase_api_key')
										<p class="text-danger">{{ $errors->first('coinbase_api_key') }}</p>
									@enderror
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">		
								<div class="input-box">								
									<h6>Coinbase Webhook Secret</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('coinbase_webhook_secret') is-danger @enderror" id="coinbase_webhook_secret" name="coinbase_webhook_secret" value="{{ config('services.coinbase.webhook_secret') }}" autocomplete="off">
									</div>
										@error('coinbase_webhook_secret')
										<p class="text-danger">{{ $errors->first('coinbase_webhook_secret') }}</p>
									@enderror
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">		
								<div class="input-box">								
									<h6>Coinbase Webhook URI</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('coinbase_webhook_uri') is-danger @enderror" id="coinbase_webhook_uri" name="coinbase_webhook_uri" value="{{ config('services.coinbase.webhook_uri') }}" autocomplete="off">
									</div>
										@error('coinbase_webhook_uri')
										<p class="text-danger">{{ $errors->first('coinbase_webhook_uri') }}</p>
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
