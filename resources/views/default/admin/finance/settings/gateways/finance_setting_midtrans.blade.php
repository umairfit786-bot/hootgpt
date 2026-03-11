@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Midtrans Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.midtrans.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12 mb-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="enable-midtrans" class="custom-switch-input" @if (config('services.midtrans.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">Use Midtrans Prepaid</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">								
								<!-- ACCESS KEY -->
								<div class="input-box">								
									<h6>Midtrans Server Key <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('midtrans_server_key') is-danger @enderror" id="midtrans_server_key" name="midtrans_server_key" value="{{ config('services.midtrans.server_key') }}" autocomplete="off">
									</div> 
									@error('midtrans_server_key')
										<p class="text-danger">{{ $errors->first('midtrans_server_key') }}</p>
									@enderror
								</div> <!-- END ACCESS KEY -->
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<!-- SECRET ACCESS KEY -->
								<div class="input-box">								
									<h6>Midtrans Client Key <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('midtrans_client_key') is-danger @enderror" id="midtrans_client_key" name="midtrans_client_key" value="{{ config('services.midtrans.client_key') }}" autocomplete="off">
									</div> 
									@error('midtrans_client_key')
										<p class="text-danger">{{ $errors->first('midtrans_client_key') }}</p>
									@enderror
								</div> <!-- END SECRET ACCESS KEY -->
							</div>	
							
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Midtrans Merchant ID <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<div class="form-group">							    
										<input type="text" class="form-control @error('midtrans_merchant_id') is-danger @enderror" id="midtrans_merchant_id" name="midtrans_merchant_id" value="{{ config('services.midtrans.merchant_id') }}" autocomplete="off">
									</div> 
									@error('midtrans_merchant_id')
										<p class="text-danger">{{ $errors->first('midtrans_merchant_id') }}</p>
									@enderror
								</div> 
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>Midtrans Production <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<select id="midtrans-production" name="midtrans-production" class="form-select" data-placeholder="{{ __('Enable/Disable Midtrans Production') }}:">			
										<option value=true @if (config('services.midtrans.production')  == true) selected @endif>{{ __('Enable') }}</option>
										<option value=false @if (config('services.midtrans.production')  == false) selected @endif>{{ __('Disable') }}</option>
									</select>
									@error('midtrans-production')
										<p class="text-danger">{{ $errors->first('midtrans-production') }}</p>
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
