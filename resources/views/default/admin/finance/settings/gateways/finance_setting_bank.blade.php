@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Bank Transer Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.bank.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card border-0">
					<div class="card-body p-6">							
						<div class="row">
							<div class="col-md-6 col-sm-12 mb-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="enable-bank" class="custom-switch-input" @if (config('services.banktransfer.enable')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Use Bank Transfer Prepaid') }}</span>
									</label>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group mb-4">
									<label class="custom-switch">
										<input type="checkbox" name="enable-bank-subscription" class="custom-switch-input" @if (config('services.banktransfer.subscription')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Use Bank Transfer Subscription') }}</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">								
								<div class="input-box">								
									<h6>{{ __('Customer Payment Intructions') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<textarea class="form-control" name="bank_instructions" id="bank_instructions" rows="6">{{ $bank['bank_instructions'] }}</textarea> 
									@error('bank_instructions')
										<p class="text-danger">{{ $errors->first('bank_instructions') }}</p>
									@enderror
								</div>
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Bank Account Requisites') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
									<textarea class="form-control" name="bank_requisites" id="bank_requisites" rows="6">{{ $bank['bank_requisites'] }}</textarea>
									@error('bank_requisites')
										<p class="text-danger">{{ $errors->first('bank_requisites') }}</p>
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
