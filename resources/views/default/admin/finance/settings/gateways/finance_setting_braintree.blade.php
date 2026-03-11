@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Braintree Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.braintree.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="form-group mb-4">
									<label class="custom-switch">
										<input type="checkbox" name="enable-braintree" class="custom-switch-input" @if (config('services.braintree.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">Use Braintree Prepaid</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">								
								<!-- ACCESS KEY -->
								<div class="input-box">								
									<h6>Braintree Private Key</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('braintree_private_key') is-danger @enderror" id="braintree_private_key" name="braintree_private_key" value="{{ config('services.braintree.private_key') }}" autocomplete="off">
									</div>
										@error('braintree_private_key')
										<p class="text-danger">{{ $errors->first('braintree_private_key') }}</p>
									@enderror
								</div> <!-- END ACCESS KEY -->
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">								
								<!-- ACCESS KEY -->
								<div class="input-box">								
									<h6>Braintree Public Key</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('braintree_public_key') is-danger @enderror" id="braintree_public_key" name="braintree_public_key" value="{{ config('services.braintree.public_key') }}" autocomplete="off">
									</div>
										@error('braintree_public_key')
										<p class="text-danger">{{ $errors->first('braintree_public_key') }}</p>
									@enderror
								</div> <!-- END ACCESS KEY -->
							</div>									
							<div class="col-lg-6 col-md-6 col-sm-12">		
								<div class="input-box">								
									<h6>Braintree Merchant ID</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('braintree_merchant_id') is-danger @enderror" id="braintree_merchant_id" name="braintree_merchant_id" value="{{ config('services.braintree.merchant_id') }}" autocomplete="off">
									</div>
										@error('braintree_merchant_id')
										<p class="text-danger">{{ $errors->first('braintree_merchant_id') }}</p>
									@enderror
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Braintree Environment</h6> 
									<select id="braintree" name="braintree_env" class="form-select" data-placeholder="Choose Braintree Environment:">			
										<option value="sandbox" @if (config('services.braintree.env')  == 'sandbox') selected @endif>Sandbox</option>
										<option value="production" @if (config('services.braintree.env')  == 'production') selected @endif>Production</option>
									</select>
									@error('braintree_env')
										<p class="text-danger">{{ $errors->first('braintree_env') }}</p>
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
