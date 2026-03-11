@extends('layouts.app')

@section('css')
	<!-- Datepicker CSS -->
	<link href="{{URL::asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center"> 
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Export Gift Cards') }}</h4>
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
		<div class="col-lg-4 col-md-5 col-sm-12">
			<div class="card border-0">
				<div class="card-body">									
					<form action="{{ route('admin.finance.gifts.export.generate') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<p class="text-center fs-14 text-muted mb-5">{{__('Use filters below to export preferred gift cards')}}</p>

						<div class="row">
							
							<div class="col-sm-12">
								<div class="input-box">
									<h6>{{ __('Select All') }}</h6>
									<div class="form-group">
										<label class="custom-switch">
											<input type="checkbox" name="all" class="custom-switch-input">
											<span class="custom-switch-indicator"></span>
										</label>
									</div>
								</div>
							</div>

							<div class="col-sm-12">						
								<div class="input-box">	
									<h6>{{ __('Gift Card Status') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="status" name="status" class="form-select" data-placeholder="{{ __('Select Promocode Status') }}:">			
										<option value="active" selected>{{ __('Active') }}</option>
										<option value="inactive">{{ __('Inactive') }}</option>
									</select>
								</div>						
							</div>						

							<div class="col-sm-12">							
								<div class="input-box">								
									<h6>{{ __('Gift Card Value') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="number" class="form-control" name="value" value="0">
									</div> 
								</div> 						
							</div>

							<div class="col-sm-12">						
								<div class="input-box">	
									<h6>{{ __('Gift Card Usage') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select name="usage" class="form-select">			
										<option value="unused" selected>{{ __('Unused') }}</option>
										<option value="used">{{ __('Used') }}</option>
									</select>
								</div>						
							</div>

							<div class="col-sm-12">						
								<div class="input-box">	
									<h6>{{ __('Export Format') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select name="format" class="form-select">			
										<option value="csv" selected>{{ __('CSV') }}</option>
										<option value="xls">{{ __('XLSX') }}</option>
										<option value="pdf">{{ __('PDF') }}</option>
									</select>
								</div>						
							</div>

						</div>


						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-4">
							<a href="{{ route('admin.finance.gifts') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Generate') }}</button>							
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
