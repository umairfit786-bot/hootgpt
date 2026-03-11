@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Show Transaction Details') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.transactions') }}"> {{ __('Transactions') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Show Transaction Details') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-6 col-xm-12">
			<h3 class="card-title text-center mb-5">{{ __('Transaction') }} ID: <span class="text-info">{{ $id->order_id }}</span></h3>
			<div class="card overflow-hidden border-0">
				<div class="card-body pt-5">		

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Transaction Date') }} </h6>
								<span class="fs-14">{{ date_format($id->created_at, 'd M Y, H:i A') }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Total Price') }} </h6>
								<span class="fs-14">{{ ucfirst($id->price) }} {{ $id->currency }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Payment Status') }} </h6>
								<span class="fs-14">{{ __(ucfirst($id->status)) }}</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Plan Name') }} </h6>
								<span class="fs-14">{{ ucfirst($id->plan_name) }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Payment Gateway') }} </h6>
								<span class="fs-14">{{ $id->gateway }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('Payment Frequency') }} </h6>
								<span class="fs-14">{{ ucfirst($id->frequency)}}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('User Name') }} </h6>
								<span class="fs-14">{{ $user->name }}</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="prepaid-view-box text-center">
								<h6 class="font-weight-bold mb-1">{{ __('User Email') }} </h6>
								<span class="fs-14">{{ $user->email }}</span>
							</div>
						</div>
					</div>	

					<div class="prepaid-view-box text-center mt-5">
						<div class="row pt-5 pb-5">
							<div class="col-md-6 col-12">
								<h6 class="font-weight-bold mb-2">{{ __('Transaction Invoice') }}: </h6>
								<a href="{{ route('user.payments.invoice.show', $id->order_id) }}" target="_blank" class="btn btn-primary pl-5 pr-5" style="width: 200px">{{ __('Download Invoice') }}</a><br>						
								<a href="{{ route('user.payments.invoice.send', $id->order_id) }}" class="btn btn-primary pl-5 pr-5 mt-3" style="width: 200px">{{ __('Send Invoice') }}</a>						
							</div>
							@if ($id->gateway == 'BankTransfer')
								<div class="col-md-6 col-12">
									<h6 class="font-weight-bold mb-2">{{ __('Payment Confirmation') }}: </h6>
									@if (is_null($id->invoice))
										<span class="fs-14">{{ __('User did not upload any payment confirmation yet') }}</span>
									@else
										<a href="{{ URL::asset($id->invoice) }}" download class="btn btn-primary pl-5 pr-5">{{ __('Download Confirmation') }}</a>	
									@endif
														
								</div>
							@endif
						</div>	
					</div>	

					<!-- SAVE CHANGES ACTION BUTTON -->
					<div class="border-0 text-center mb-2 mt-7">
						<a href="{{ route('admin.finance.transactions') }}" class="btn btn-cancel pl-7 pr-7">{{ __('Return') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

