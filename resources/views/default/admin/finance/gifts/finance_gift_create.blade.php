@extends('layouts.app')

@section('css')
	<!-- Datepicker CSS -->
	<link href="{{URL::asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center"> 
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('New Gift Card') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.finance.gifts') }}"> {{ __('Gift Cards') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-6 col-sm-12">
			<div class="card border-0">
				<div class="card-body">									
					<form action="{{ route('admin.finance.gifts.store') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<p class="text-center fs-14 text-muted mb-6">{{__('Create a new gift card by filling all required fields')}}</p>

						<div class="row">

							<div class="col-lg-6 col-md-6 col-sm-12">				
								<div class="input-box">	
									<h6>{{ __('Gift Card Name') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
									</div>
									@error('name')
										<p class="text-danger">{{ $errors->first('name') }}</p>
									@enderror
								</div> 							
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">						
								<div class="input-box">	
									<h6>{{ __('Status') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="status" name="status" class="form-select" data-placeholder="{{ __('Select Promocode Status') }}:">			
										<option value="active" selected>{{ __('Active') }}</option>
										<option value="inactive">{{ __('Inactive') }}</option>
									</select>
									@error('status')
										<p class="text-danger">{{ $errors->first('status') }}</p>
									@enderror	
								</div>						
							</div>
						
						</div>

						<div class="row mt-2">							

							<div class="col-lg-6 col-md-6 col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Amount') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Total money amount that will be added to user balance') }}"></i></h6>
									<div class="form-group">							    
										<input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" required>
									</div> 
									@error('amount')
										<p class="text-danger">{{ $errors->first('amount') }}</p>
									@enderror
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Usage Quantity') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Total number of cards with the same code') }}"></i></h6>
									<div class="form-group">							    
										<input type="number" class="form-control" min="1" name="quantity" value="{{ old('quantity') }}">
									</div> 
									@error('quantity')
										<p class="text-danger">{{ $errors->first('quantity') }}</p>
									@enderror
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Multi Usage by the same User') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Allow or Deny the same gift card usage by the same user multiple times') }}"></i></h6>
									<select id="multi_use" name="usage" class="form-select" data-placeholder="{{ __('Set Multi Usage by the same User') }}:">			
										<option value=1>{{ __('Allow') }}</option>
										<option value=0 selected>{{ __('Deny') }}</option>
									</select> 
									@error('usage')
										<p class="text-danger">{{ $errors->first('usage') }}</p>
									@enderror
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Valid Until') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group" id="datepicker-container">							    
										<input type="text" class="form-control" placeholder="YYYY-MM-DD" id="valid-until" name="valid-until" value="{{ old('valid-until') }}" required>
									</div> 
									@error('valid-until')
										<p class="text-danger">{{ $errors->first('valid-until') }}</p>
									@enderror
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">	
								<div class="input-box">	
									<h6>{{ __('Notes') }} <span class="text-muted">({{__('Optional')}})</span></h6>							
									<textarea class="form-control" name="notes" rows="1">{{ old('notes') }}</textarea>	
								</div>											
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Total Quantity') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Total number of cards with different code') }}"></i></h6>
									<div class="form-group">							    
										<input type="number" class="form-control" name="total" min="1" value="{{ old('total') }}">
									</div> 
								</div> 						
							</div>
						</div>


						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-4">
							<a href="{{ route('admin.finance.gifts') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Create') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<!-- Bootstrap Datepicker JS -->
	<script src="{{URL::asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>	
	<script>
		$(function(){

			'use strict';

			$('#datepicker-container input').datepicker({
				autoclose: true,
				todayHighlight: true,
				toggleActive: true,
				format: 'yyyy-mm-dd',
				orientation: "bottom"
			});			
		});

		function singleUsageCheck(value) {

			"use strict";
			console.log(value)

			if (value == 0) {
				document.getElementById('quantity').disabled = false;
			} else {
				document.getElementById('quantity').disabled = true;
			}
		}
	</script>
@endsection
