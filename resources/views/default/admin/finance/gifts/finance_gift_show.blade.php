@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Show Gift Card') }}</h4>
			<ol class="breadcrumb mb-2">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
					<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.finance.gifts') }}"> {{ __('Gift Cards') }}</a></li>
				</ol>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-6 col-sm-12">
			<div class="card border-0">
				<h3 class="card-title text-center mb-5">{{ __('Gift Card Name') }}: <span class="text-info">{{ $id->name }}</span> </h3>
				<div class="card-body pt-5">		

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Code') }}: </h6>
								<span class="fs-14 text-info">{{ $id->code }}</span>
							</div>							
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Status') }}: </h6>
								<span class="fs-14">@if($id->status) {{__('Active')}} @else {{__('Inactive')}} @endif</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Expires at') }}: </h6>
								<span class="fs-14">{{ $id->valid_until}}</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Amount') }}: </h6>
								<span class="fs-14">{{ $id->amount }}{{config('payment.default_system_currency')}}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Available Quantity') }}: </h6>
								<span class="fs-14">{{ $id->usages_left }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Multi Usage') }}: </h6>
								<span class="fs-14">@if ($id->reusable == 1) {{ __('Allowed') }} @else {{ __('Not Allowed') }} @endif</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<div class="prepaid-view-box pl-5">
								<h6 class="font-weight-bold mb-1">{{ __('Notes') }}: </h6>
								<span class="fs-14">{{ $id->details ?? __('Not provided') }}</span>
							</div>
						</div>
					</div>					

					<!-- SAVE CHANGES ACTION BUTTON -->
					<div class="border-0 text-center mb-2 mt-7">
						<a href="{{ route('admin.finance.gifts') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
						<a href="{{ route('admin.finance.gifts.edit', $id) }}" class="btn btn-primary">{{ __('Edit') }}</a>						
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
