@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Finance Settings') }}</h4>
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

			<form action="{{ route('admin.finance.settings.store') }}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="card pt-4">	
					<div class="card-body">				

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">			
								<div class="input-box">	
									<h6>{{ __('Default Currency') }} <span class="text-muted">({{ __('Payments/Plans/System/Payouts') }})</span></h6>
									<select id="currency" name="currency" class="form-select" data-placeholder="Choose Default Currency:">			
										@foreach(config('currencies.all') as $key => $value)
											<option value="{{ $key }}" @if(config('payment.default_system_currency') == $key) selected @endif>{{ $value['name'] }} - {{ $key }} ({!! $value['symbol'] !!})</option>
										@endforeach
									</select>									
									@error('currency')
										<p class="text-danger">{{ $errors->first('currency') }}</p>
									@enderror
								</div> 						
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Tax Rate') }} (%)</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('tax') is-danger @enderror" id="tax" name="tax" placeholder="Enter Tax Rate" value="{{ config('payment.payment_tax')}}">
									</div>
									@error('tax')
										<p class="text-danger">{{ $errors->first('tax') }}</p>
									@enderror 
								</div>							
							</div>	
							
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Decimal Points in Prices') }} <span class="text-muted">({{ __('.00') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="chat-feature-user" name="decimal-points" class="form-select" data-placeholder="{{ __('Allow/Deny Decimal Points in Prices') }}">
										<option value='allow' @if (config('payment.decimal_points') == 'allow') selected @endif>{{ __('Allow') }}</option>
										<option value='deny' @if (config('payment.decimal_points') == 'deny') selected @endif> {{ __('Deny') }}</option>																															
									</select>
								</div>
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('AI Vendor Service Costs') }}</h6>
										<a href="{{ route('admin.finance.settings.costs') }}" class="btn btn-primary ripple pl-7 pr-7" >{{ __('Update AI Vendor Service Costs') }}</a>		
								</div>
							</div>
						</div>

						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary" style="min-width: 200px">{{ __('Save') }}</button>							
						</div>					
					</div>
				</div>

				<div class="card border-0">
					<div class="card-header border-0 justify-content-center">
						<h3 class="card-title mt-5 mb-5 text-muted">{{ __('Payment Gateways') }}</h3>
					</div>
					<div class="card-body pb-6">
						<div class="row" id="gateways">
							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/paypal')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/paypal.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Paypal') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Paypal API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/stripe')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/stripe.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Stripe') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Stripe API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/paystack')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/paystack.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Paystack') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Paystack API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/razorpay')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/razorpay.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Razorpay') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Razorpay API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/mollie')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/mollie.jpg')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Mollie') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Mollie API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/flutterwave')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/flutterwave.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Flutterwave') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Flutterwave API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/yookassa')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/yookassa.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Yookassa') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Yookassa API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/paddle')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/paddle.webp')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Paddle') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Paddle API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/braintree')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/braintree.svg')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Braintree') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Braintree API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/iyzico')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/iyzico.svg')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Iyzico') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Iyzico API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/midtrans')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/midtrans.jpeg')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Midtrans') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Midtrans API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/coinbase')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/coinbase.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Coinbase') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Coinbase API settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							<div class="col-md-6 col-sm-12">
								<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/bank')}}'">
									<div class="card-body p-5 d-flex">
										<div class="extension-icon">
											<img src="{{theme_url('img/payments/bank.png')}}" class="mr-4" alt="" style="width: 40px;">												
										</div>
										<div class="extension-title">
											<div class="d-flex">
												<h6 class="fs-15 font-weight-bold mb-3">{{ __('Bank Transfer') }}</h6>
											</div>
											<p class="fs-12 mb-0 text-muted">{{ __('Bank Transfer settings and configuration')}}</p>
										</div>
									</div>							
								</div>
							</div>

							@if (App\Services\HelperService::extensionCoinremitter())
								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/coinremitter')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/payments/coinremitter.webp')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Coinremitter') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Coinremitter API settings and configuration')}}</p>
											</div>
										</div>							
									</div>
								</div>
							@endif

							@if (App\Services\HelperService::extensionWallet())
								@if (App\Services\HelperService::extensionWalletFeature())							
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/wallet')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/payments/wallet.avif')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Wallet') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Wallet settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif
							@endif

							@if (App\Services\HelperService::extensionAwdpay())
								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/finance/settings/awdpay')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/payments/awdpay.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Awdpay') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Awdpay API settings and configuration')}}</p>
											</div>
										</div>							
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>		
			
			</form>
				
		</div>
		
	</div>
	<!-- END ALL PAYMENT CONFIGURATIONS -->	

@endsection
