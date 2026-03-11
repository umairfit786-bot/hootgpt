@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Company Invoice Settings') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Invoice Settings') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection
@section('content')					
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-12 col-xm-12">
			<h3 class="card-title text-muted text-center">{{ __('Setup Your Company Information on Invoices') }}</h3>
			<div class="card border-0">
				<div class="card-body">
									
					<form action="{{ route('admin.settings.invoice.store') }}" method="POST" enctype="multipart/form-data">
						@csrf				

						<div class="row">		

							<div class="col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Company Name') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('company') is-danger @enderror" name="company" value="{{ $invoice->company ?? '' }}" required>
									</div> 
								</div> 						
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Company Website') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('website') is-danger @enderror" name="website" value="{{ $invoice->website ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-12">
								<div class="input-box">								
									<h6>{{ __('Business Address') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('address') is-danger @enderror"  name="address" value="{{ $invoice->address ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-md-4 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('City') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('city') is-danger @enderror" name="city" value="{{ $invoice->city ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-md-2 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('State') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('state') is-danger @enderror" name="state" value="{{ $invoice->state ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-md-2 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Postal Code') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('postal_code') is-danger @enderror" name="postal_code" value="{{ $invoice->postal_code ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-md-4 col-sm-12">							
								<div class="input-box">	
									<h6>{{ __('Country') }}</h6>
									<select id="invoice-country" name="country" class="form-select" data-placeholder="{{ __('Select Invoice Country') }}:">	
										@foreach(config('countries') as $value)
											<option value="{{ $value }}" @if(($invoice->country ?? '') == $value) selected @endif>{{ $value }}</option>
										@endforeach																			
									</select>
								</div> 							
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Phone Number') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('phone') is-danger @enderror" name="phone" value="{{ $invoice->phone ?? '' }}">
									</div> 
								</div> 						
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('VAT Number') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('vat_number') is-danger @enderror" name="vat_number" value="{{ $invoice->vat_number ?? '' }}">
									</div> 
								</div> 						
							</div>

						</div>

						<!-- SAVE CHANGES ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<a href="{{ route('admin.finance.dashboard') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
	
@endsection
