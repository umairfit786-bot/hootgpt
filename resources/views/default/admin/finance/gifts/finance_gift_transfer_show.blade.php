@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Transfer Details') }}</h4>
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
				<div class="card-header">
					<h3 class="card-title">{{ __('Transfer ID') }}: <span class="text-info">{{ $id->transfer_id }}</span> </h3>
				</div>
				<div class="card-body pt-5">
					
					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Transfer Amount') }}: </h6>
							<span class="fs-14 text-info">{{ $id->amount }} {{config('payment.default_system_currency')}}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Status') }}: </h6>
							<span class="fs-14">@if($id->status) {{__('Transfered')}} @else {{__('Failed')}} @endif</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Transfer Date') }}: </h6>
							<span class="fs-14">{{ $id->created_at}}</span>
						</div>
					</div>
					
					<hr>

					<h6 class="mb-4 text-muted">{{__('Sender Information')}}</h6>
					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Name') }}: </h6>
							<span class="fs-14 text-info">{{ $id->sender_username }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Email') }}: </h6>
							<span class="fs-14">{{$id->sender_email}}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Wallet Balance') }}: </h6>
							<span class="fs-14">{{ $sender->wallet}} {{config('payment.default_system_currency')}}</span>
						</div>
					</div>

					<hr>

					<h6 class="mb-4 text-muted">{{__('Receiver Information')}}</h6>

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Name') }}: </h6>
							<span class="fs-14 text-info">{{ $id->receiver_username }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Email') }}: </h6>
							<span class="fs-14">{{$id->receiver_email}}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Wallet Balance') }}: </h6>
							<span class="fs-14">{{ $receiver->wallet}} {{config('payment.default_system_currency')}}</span>
						</div>
					</div>				

					<!-- SAVE CHANGES ACTION BUTTON -->
					<div class="border-0 text-center mb-2 mt-7">
						<a href="{{ route('admin.finance.gifts') }}" class="btn btn-cancel mr-2">{{ __('Return') }}</a>						
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
