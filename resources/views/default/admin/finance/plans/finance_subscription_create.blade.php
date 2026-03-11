@extends('layouts.app')
@section('css')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center"> 
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('New Subscription Plan') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.plans') }}"> {{ __('Subscription Plans') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('New Subscription Plan') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">

		<div class="col-lg-8 col-md-10 col-sm-12">
			<div class="card border-0">
				<div class="card-header border-0 pb-0">
					<h6 class="card-title fs-12 text-muted">{{ __('Create New Subscription Plan') }}</h6>
				</div>
				<div class="card-body pt-0">
					<hr class="mt-0">									
					<form action="{{ route('admin.finance.plan.store') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<div class="card mt-4 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-money-check-dollar-pen text-info fs-14 mr-1 fw-2"></i>{{ __('General Settings') }}</h6>

								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-12">						
										<div class="input-box">	
											<h6>{{ __('Plan Status') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="plan-status" name="plan-status" class="form-select" data-placeholder="{{ __('Select Plan Status') }}:">			
												<option value="active" selected>{{ __('Active') }}</option>
												<option value="hidden">{{ __('Hidden') }}</option>
												<option value="closed">{{ __('Closed') }}</option>
											</select>
											@error('plan-status')
												<p class="text-danger">{{ $errors->first('plan-status') }}</p>
											@enderror	
										</div>						
									</div>							
									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Plan Name') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="plan-name" name="plan-name" value="{{ old('plan-name') }}" required>
											</div> 
											@error('plan-name')
												<p class="text-danger">{{ $errors->first('plan-name') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Price') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="number" step="0.01" class="form-control" id="cost" name="cost" value="{{ old('cost') }}" required>
											</div> 
											@error('cost')
												<p class="text-danger">{{ $errors->first('cost') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Currency') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="currency" name="currency" class="form-select" data-placeholder="{{ __('Select Currency') }}:">			
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
											<h6>{{ __('Payment Frequence') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="frequency" name="frequency" class="form-select" data-placeholder="{{ __('Select Payment Frequency') }}:" data-callback="duration_select">		
												<option value="monthly" selected>{{ __('Monthly') }}</option>
												<option value="yearly">{{ __('Yearly') }}</option>
												<option value="lifetime">{{ __('Lifetime') }}</option>
											</select>
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Featured Plan') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="featured" name="featured" class="form-select" data-placeholder="{{ __('Select if Plan is Featured') }}:">		
												<option value=1>{{ __('Yes') }}</option>
												<option value=0 selected>{{ __('No') }}</option>
											</select>
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Free Plan') }}</h6>
											<div class="form-group">							    
												<select id="free-plan" name="free-plan" class="form-select" data-placeholder="{{ __('Make this plan a Free Plan?') }}:">			
													<option value=1>{{ ('Yes') }}</option>
													<option value=0 selected>{{ ('No') }}</option>
												</select>
											</div> 
											@error('free-plan')
												<p class="text-danger">{{ $errors->first('free-plan') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Free Plan Days') }}</h6>
											<div class="form-group">							    
												<input type="number" class="form-control" id="days" name="days" min=0 value="{{ old('days') }}">
											</div> 
											@error('days')
												<p class="text-danger">{{ $errors->first('days') }}</p>
											@enderror
										</div> 						
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-7 shadow-0" id="payment-gateways">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa fa-bank text-info fs-14 mr-1 fw-2"></i>{{ __('Payment Gateways Plan IDs') }}</h6>

								<div class="row">								
									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('PayPal Plan ID') }} <span class="text-danger">({{ __('Required for Paypal') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Paypal Plan ID in your Paypal account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="paypal_gateway_plan_id" name="paypal_gateway_plan_id" value="{{ old('paypal_gateway_plan_id') }}">
											</div> 
											@error('paypal_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('paypal_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Stripe Price ID') }} <span class="text-danger">({{ __('Required for Stripe') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Stripe Price ID in your Stripe account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="stripe_gateway_plan_id" name="stripe_gateway_plan_id" value="{{ old('stripe_gateway_plan_id') }}">
											</div> 
											@error('stripe_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('stripe_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Paystack Plan Code') }} <span class="text-danger">({{ __('Required for Paystack') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Paystack Plan ID in your Paystack account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="paystack_gateway_plan_id" name="paystack_gateway_plan_id" value="{{ old('paystack_gateway_plan_id') }}">
											</div> 
											@error('paystack_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('paystack_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Razorpay Plan ID') }} <span class="text-danger">({{ __('Required for Razorpay') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Razorpay Plan ID in your Razorpay account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="razorpay_gateway_plan_id" name="razorpay_gateway_plan_id" value="{{ old('razorpay_gateway_plan_id') }}">
											</div> 
											@error('razorpay_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('razorpay_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Flutterwave Plan ID') }} <span class="text-danger">({{ __('Required for Flutterwave') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Flutterwave Plan ID in your Flutterwave account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="flutterwave_gateway_plan_id" name="flutterwave_gateway_plan_id" value="{{ old('flutterwave_gateway_plan_id') }}">
											</div> 
											@error('flutterwave_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('flutterwave_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Paddle Plan ID') }} <span class="text-danger">({{ __('Required for Paddle') }}) <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You have to get Paddle Plan ID in your Paddle account. Refer to the documentation if you need help with creating one') }}."></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="paddle_gateway_plan_id" name="paddle_gateway_plan_id" value="{{ old('paddle_gateway_plan_id') }}">
											</div> 
											@error('paddle_gateway_plan_id')
												<p class="text-danger">{{ $errors->first('paddle_gateway_plan_id') }}</p>
											@enderror
										</div> 						
									</div>
								</div>
							</div>						
						</div>

						<div class="card mt-7 mb-7 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-box-circle-check text-info fs-14 mr-1 fw-2"></i>{{ __('Included AI Credits') }}</h6>

								<div class="row">

									<div class="col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>@if ($settings->model_credit_name == 'words') {{ __('Word Credits') }} @else {{ __('Token Credits') }} @endif<span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <span class="text-muted ml-3">({{ __('Renewed Monthly') }})</span></h6>
											<div class="form-group">							    
												<input type="number" class="form-control" id="token-credits" name="token-credits" value="0" placeholder="0">
												<span class="text-muted fs-10">{{ __('Valid for all models') }}. @if ($settings->model_credit_name == 'words') {{ __('Set as -1 for unlimited words') }} @else {{ __('Set as -1 for unlimited tokens') }} @endif.</span>
											</div> 
										</div> 						
									</div>
									
									<div class="col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Media Credits') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <span class="text-muted ml-3">({{ __('Renewed Monthly') }})</span></h6>
											<div class="form-group">							    
												<input type="number" class="form-control" id="image-credits" name="image-credits" value="0" placeholder="0">
												<span class="text-muted fs-10">{{ __('Valid for all media tasks') }}. {{ __('Set as -1 for unlimited credits') }}.</span>
											</div> 
											@error('image-credits')
												<p class="text-danger">{{ $errors->first('image-credits') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Characters Included') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <span class="text-muted ml-3">({{ __('Renewed Monthly') }})</span></h6>
											<div class="form-group">							    
												<input type="number" class="form-control" id="characters" name="characters" value="0" placeholder="0">
												<span class="text-muted fs-10">{{ __('For AI Voiceover feature') }}. {{ __('Set as -1 for unlimited characters') }}.</span>
											</div> 
											@error('characters')
												<p class="text-danger">{{ $errors->first('characters') }}</p>
											@enderror
										</div> 						
									</div>

									<div class="col-md-6 col-sm-12">							
										<div class="input-box">								
											<h6>{{ __('Minutes Included') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <span class="text-muted ml-3">({{ __('Renewed Monthly') }})</span></h6>
											<div class="form-group">							    
												<input type="number" class="form-control" id="minutes" name="minutes" value="0" placeholder="0">
												<span class="text-muted fs-10">{{ __('For AI Speech to Text feature') }}. {{ __('Set as -1 for unlimited minutes') }}.</span>
											</div> 
											@error('minutes')
												<p class="text-danger">{{ $errors->first('minutes') }}</p>
											@enderror
										</div> 						
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-7 mb-7 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-box-circle-check text-info fs-14 mr-1 fw-2"></i>{{ __('Included Features') }}</h6>

								<div class="row">	
									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Writer Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="writer-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Article Wizard Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="wizard-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Smart Editor Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="smart-editor-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Rewriter Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="rewriter-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Image Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="image-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Voiceover Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="voiceover-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Speech to Text Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="whisper-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Chat Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="chat-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Code Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="code-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal OpenAI API Usage Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-openai-api" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal Claude API Usage Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-claude-api" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal Gemini API Usage Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-gemini-api" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>
		
									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal Stable Diffusion API Usage Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-sd-api" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Vision Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="vision-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Chat Image Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="chat-image-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI File Chat Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="file-chat-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Internet Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="internet-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Web Chat Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="chat-web-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>																			

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal Custom AI Chat Bot Creation Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-chat-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Personal Custom Template Creation Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="personal-template-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Brand Voice Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="brand-voice-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>					

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Youtube Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="youtube-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI RSS Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="rss-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Integration Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="integration-feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Team Member Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="team_member_feature" class="custom-switch-input">
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card mt-7 mb-7 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-box-circle-check text-info fs-14 mr-1 fw-2"></i>{{ __('Included Extra Service Limits') }}</h6>

								<div class="row">							
									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Available Models for All Templates') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Subscribers will only have access to the selected models for all AI features related to generating text') }}."></i></h6>
											<select class="form-select" id="templates-models-list" name="templates_models_list[]" data-placeholder="{{ __('Choose Models for Templates') }}" multiple>									
												<option value='gpt-3.5-turbo-0125'>{{ __('OpenAI GPT 3.5 Turbo') }}</option>																																																																																												
												<option value='gpt-4'>{{ __('OpenAI GPT 4') }}</option>																																																																																																																																																																																																																																																							
												<option value='gpt-4o'>{{ __('OpenAI GPT 4o') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-mini'>{{ __('OpenAI GPT 4o mini') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-search-preview'>{{ __('OpenAI GPT 4o Search Preview') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-mini-search-preview'>{{ __('OpenAI GPT 4o mini Search Preview') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4-0125-preview'>{{ __('OpenAI GPT 4 Turbo') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.5-preview'>{{ __('OpenAI GPT 4.5') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1'>{{ __('OpenAI GPT 4.1') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1-mini'>{{ __('OpenAI GPT 4.1 mini') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1-nano'>{{ __('OpenAI GPT 4.1 nano') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='o1'>{{ __('OpenAI o1') }}</option>																																																																																																																																																																																																																																																						
												<option value='o1-mini'>{{ __('OpenAI o1 mini') }}</option>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																												
												<option value='o3-mini'>{{ __('OpenAI o3 mini') }}</option>																																																																																																																																																																																																																																																						
												<option value='o3'>{{ __('OpenAI o3') }}</option>																																																																																																																																																																																																																																																						
												<option value='o4-mini'>{{ __('OpenAI o4 mini') }}</option>	
												<option value="gpt-5">{{ __('GPT 5') }}</option>														
												<option value="gpt-5-mini">{{ __('GPT 5 mini') }}</option>														
												<option value="gpt-5-nano">{{ __('GPT 5 nano') }}</option>														
												<option value="gpt-5-chat-latest">{{ __('GPT 5 chat') }}</option>																																																																																																																																																																																																																																																					
												<option value='claude-sonnet-4-5'>{{ __('Claude 4.5 Sonnet') }}</option>																																																																																																																											
												<option value='claude-haiku-4-5'>{{ __('Claude 4.5 Haiku') }}</option>	
												<option value='claude-opus-4-5-20251101'>{{ __('Claude 4.5 Opus') }}</option>																																																																																																																										
												<option value='claude-opus-4-1-20250805'>{{ __('Claude 4.1 Opus') }}</option>																																																																																																																											
												<option value='claude-opus-4-20250514'>{{ __('Claude 4 Opus') }}</option>																																																																																																																											
												<option value='claude-sonnet-4-20250514'>{{ __('Claude 4 Sonnet') }}</option>																																																																																																																											
												<option value='claude-3-opus-20240229'>{{ __('Claude 3 Opus') }}</option>																																																																																																																											
												<option value='claude-3-7-sonnet-20250219'>{{ __('Claude 3.7 Sonnet') }}</option>																																																																																																																											
												<option value='claude-3-5-sonnet-20241022'>{{ __('Claude 3.5 Sonnet') }}</option>																																																																																																																											
												<option value='claude-3-5-haiku-20241022'>{{ __('Claude 3.5 Haiku') }}</option>																																																																																																																											
												<option value='gemini-1.5-pro'>{{ __('Gemini 1.5 Pro') }}</option>																																																																																																																											
												<option value='gemini-1.5-flash'>{{ __('Gemini 1.5 Flash') }}</option>																																																																																																																											
												<option value='gemini-2.0-flash'>{{ __('Gemini 2.0 Flash') }}</option>													
												<option value="gemini-2.5-flash">{{ __('Gemini 2.5 Flash') }}</option>
												<option value="gemini-2.5-flash-lite-preview-06-17">{{ __('Gemini 2.5 Flash Lite') }}</option>	
												<option value="gemini-2.5-pro">{{ __('Gemini 2.5 Pro') }}</option>																																																																																																																								
												<option value="gemini-3-pro-preview">{{ __('Gemini 3 Pro') }}</option>																																																																																																																								
												<option value='deepseek-chat'>{{ __('DeepSeek V3') }}</option>																																																																																																																											
												<option value='deepseek-reasoner'>{{ __('DeepSeek R1') }}</option>	
												<option value="grok-4-latest">{{ __('Grok 4') }}</option>
												<option value="grok-3-latest">{{ __('Grok 3') }}</option>
												<option value="grok-3-fast-latest">{{ __('Grok 3 Fast') }}</option>
												<option value="grok-3-mini-latest">{{ __('Grok 3 Mini') }}</option>
												<option value="grok-3-mini-fast-latest">{{ __('Grok 3 Mini Fast') }}</option>																																																																																																																										
												<option value='grok-2-1212'>{{ __('Grok 2') }}</option>																																																																																																																											
												<option value='grok-2-vision-1212'>{{ __('Grok 2 Vision') }}</option>
												@if (App\Services\HelperService::extensionPerplexity())	
													<option value="sonar">{{ __('Perplexity Sonar') }}</option>
													<option value="sonar-pro">{{ __('Perplexity Sonar Pro') }}</option>
													<option value="sonar-reasoning">{{ __('Perplexity Sonar Reasoning') }}</option>
													<option value="sonar-reasoning-pro">{{ __('Perplexity Sonar Reasoning Pro') }}</option>
												@endif	
												@if (App\Services\HelperService::extensionAmazonBedrock())	
													<option value="us.amazon.nova-micro-v1:0">{{ __('Nova Micro') }}</option>
													<option value="us.amazon.nova-lite-v1:0">{{ __('Nova Lite') }}</option>
													<option value="us.amazon.nova-pro-v1:0">{{ __('Nova Pro') }}</option>
												@endif																																																																																																																										
												@foreach ($models as $model)
													<option value="{{ $model->model }}"> {{ $model->description }} ({{ __('Fine Tune Model')}})</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Available Models for All Chat Bots') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Subscribers will only have access to the selected models for all AI features related to chat bots') }}."></i></h6>
											<select class="form-select" id="chats-models-list" name="chats_models_list[]" data-placeholder="{{ __('Choose Models for Chat Bots') }}" multiple>
												<option value='gpt-3.5-turbo-0125'>{{ __('OpenAI GPT 3.5 Turbo') }}</option>																																																																																												
												<option value='gpt-4'>{{ __('OpenAI GPT 4') }}</option>																																																																																																																																																																																																																																																							
												<option value='gpt-4o'>{{ __('OpenAI GPT 4o') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-mini'>{{ __('OpenAI GPT 4o mini') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-search-preview'>{{ __('OpenAI GPT 4o Search Preview') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4o-mini-search-preview'>{{ __('OpenAI GPT 4o mini Search Preview') }}</option>																																																																																																																																																																																																																																																					
												<option value='gpt-4-0125-preview'>{{ __('OpenAI GPT 4 Turbo') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.5-preview'>{{ __('OpenAI GPT 4.5') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1'>{{ __('OpenAI GPT 4.1') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1-mini'>{{ __('OpenAI GPT 4.1 mini') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='gpt-4.1-nano'>{{ __('OpenAI GPT 4.1 nano') }}</option>																																																																																																																																																																																																																																																																																															
												<option value='o1'>{{ __('OpenAI o1') }}</option>																																																																																																																																																																																																																																																						
												<option value='o1-mini'>{{ __('OpenAI o1 mini') }}</option>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
												<option value='o3-mini'>{{ __('OpenAI o3 mini') }}</option>																																																																																																																																																																																																																																																						
												<option value='o3'>{{ __('OpenAI o3') }}</option>																																																																																																																																																																																																																																																						
												<option value='o4-mini'>{{ __('OpenAI o4 mini') }}</option>	
												<option value="gpt-5">{{ __('GPT 5') }}</option>														
												<option value="gpt-5-mini">{{ __('GPT 5 mini') }}</option>														
												<option value="gpt-5-nano">{{ __('GPT 5 nano') }}</option>														
												<option value="gpt-5-chat-latest">{{ __('GPT 5 chat') }}</option>
												<option value="gpt-5.1">{{ __('GPT 5.1') }}</option>
												<option value='claude-sonnet-4-5'>{{ __('Claude 4.5 Sonnet') }}</option>																																																																																																																											
												<option value='claude-haiku-4-5'>{{ __('Claude 4.5 Haiku') }}</option>
												<option value='claude-opus-4-5-20251101'>{{ __('Claude 4.5 Opus') }}</option>
												<option value='claude-opus-4-1-20250805'>{{ __('Claude 4.1 Opus') }}</option>
												<option value='claude-opus-4-20250514'>{{ __('Claude 4 Opus') }}</option>																																																																																																																											
												<option value='claude-sonnet-4-20250514'>{{ __('Claude 4 Sonnet') }}</option>																																																																																																																																																																																																																																																				
												<option value='claude-3-opus-20240229'>{{ __('Claude 3 Opus') }}</option>	
												<option value='claude-3-7-sonnet-20250219'>{{ __('Claude 3.7 Sonnet') }}</option>																																																																																																																											
												<option value='claude-3-5-sonnet-20241022'>{{ __('Claude 3.5 Sonnet') }}</option>																																																																																																																											
												<option value='claude-3-5-haiku-20241022'>{{ __('Claude 3.5 Haiku') }}</option>																																																																																																																											
												<option value='gemini-1.5-pro'>{{ __('Gemini 1.5 Pro') }}</option>																																																																																																																											
												<option value='gemini-1.5-flash'>{{ __('Gemini 1.5 Flash') }}</option>																																																																																																																											
												<option value='gemini-2.0-flash'>{{ __('Gemini 2.0 Flash') }}</option>
												<option value="gemini-2.5-flash">{{ __('Gemini 2.5 Flash') }}</option>
												<option value="gemini-2.5-flash-lite-preview-06-17">{{ __('Gemini 2.5 Flash Lite') }}</option>	
												<option value="gemini-2.5-pro">{{ __('Gemini 2.5 Pro') }}</option>	
												<option value="gemini-3-pro-preview">{{ __('Gemini 3 Pro') }}</option>	
												<option value='deepseek-chat'>{{ __('DeepSeek V3') }}</option>																																																																																																																											
												<option value='deepseek-reasoner'>{{ __('DeepSeek R1') }}</option>	
												<option value="grok-4-1-fast-non-reasoning">{{ __('Grok 4.1 Fast') }}</option>
												<option value="grok-4-latest">{{ __('Grok 4') }}</option>
												<option value="grok-3-latest">{{ __('Grok 3') }}</option>
												<option value="grok-3-fast-latest">{{ __('Grok 3 Fast') }}</option>
												<option value="grok-3-mini-latest">{{ __('Grok 3 Mini') }}</option>
												<option value="grok-3-mini-fast-latest">{{ __('Grok 3 Mini Fast') }}</option>	
												<option value='grok-2-1212'>{{ __('Grok 2') }}</option>																																																																																																																											
												<option value='grok-2-vision-1212'>{{ __('Grok 2 Vision') }}</option>	
												@if (App\Services\HelperService::extensionPerplexity())	
													<option value="sonar">{{ __('Perplexity Sonar') }}</option>
													<option value="sonar-pro">{{ __('Perplexity Sonar Pro') }}</option>
													<option value="sonar-reasoning">{{ __('Perplexity Sonar Reasoning') }}</option>
													<option value="sonar-reasoning-pro">{{ __('Perplexity Sonar Reasoning Pro') }}</option>
												@endif	
												@if (App\Services\HelperService::extensionAmazonBedrock())	
													<option value="us.amazon.nova-micro-v1:0">{{ __('Nova Micro') }}</option>
													<option value="us.amazon.nova-lite-v1:0">{{ __('Nova Lite') }}</option>
													<option value="us.amazon.nova-pro-v1:0">{{ __('Nova Pro') }}</option>
												@endif																																																																																																																											
												@foreach ($models as $model)
													<option value="{{ $model->model }}"> {{ $model->description }} ({{ __('Fine Tune Model')}})</option>
												@endforeach
											</select>
										</div>
									</div>									

									<hr style="width: 92%" class="ml-auto mr-auto">

									<h6 class="fs-11 font-weight-bold mb-4">{{ __('Templates Access Control') }}</h6>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6 class="text-muted">{{ __('Allowed Templates Package') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="templates" name="templates" class="form-select" data-placeholder="{{ __('Set Templates Access') }}">
												<option value="all" selected>{{ __('All Templates') }}</option>																																										
												<option value="free">{{ __('Only Free Templates') }}</option>																																										
												<option value="standard"> {{ __('Up to Standard Templates') }}</option>		
												<option value="professional"> {{ __('Up to Professional Templates') }}</option>																																																												
												<option value="premium"> {{ __('Up to Premium Templates') }} ({{ __('All') }})</option>																																																												
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6 class="text-muted">{{ __('Allowed Template Categories') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('By default all template categories are accessible based on Allowed Templates Package, if you want further granularity, you can list which Template Categories are allowed as well, thus user will have access only to the selected categories of templates, otherwise leave this field empty') }}."></i></h6>
											<select class="form-select" id="template-categories" name="template_categories[]" data-placeholder="{{ __('Selected Template Categories') }}" multiple>
												<option value="all"> {{ __('All Categories') }}</option>
												@foreach ($categories as $category)
													<option value="{{ $category->code }}"> {{ __(ucfirst($category->name)) }}</option>
												@endforeach																																																																						
											</select>
										</div>
									</div>

									<hr style="width: 92%" class="ml-auto mr-auto">

									<h6 class="fs-11 font-weight-bold mb-4">{{ __('AI Chat Access Control') }}</h6>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6 class="text-muted">{{ __('Allowed AI Chats Package') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="chats" name="chats" class="form-select" data-placeholder="{{ __('Set AI Chat Type Access') }}">
												<option value="all">{{ __('All Chat Types') }}</option>
												<option value="free">{{ __('Only Free Chat Types') }}</option>																																											
												<option value="standard"> {{ __('Up to Standard Chat Types') }}</option>
												<option value="professional"> {{ __('Up to Professional Chat Types') }}</option>
												<option value="premium"> {{ __('Upto Premium Chat Types') }} ({{ __('All') }})</option>																																																														
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6 class="text-muted">{{ __('Allowed AI Chat Categories') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('By default all chat categories are accessible based on Allowed AI Chat Package, if you want further granularity, you can list which AI Chat Categories are allowed as well, thus user will have access only to the selected categories of AI chats, otherwise leave this field empty') }}."></i></h6>
											<select class="form-select" id="chat-categories" name="chat_categories[]" data-placeholder="{{ __('Selected AI Chat Categories') }}" multiple>
												<option value="all"> {{ __('All Categories') }}</option>
												@foreach ($chat_categories as $category)
													<option value="{{ $category->code }}"> {{ __(ucfirst($category->name)) }}</option>
												@endforeach																																																																						
											</select>
										</div>
									</div>

									<hr style="width: 92%" class="ml-auto mr-auto">

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Included AI Voiceover Vendors') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Only listed TTS voices of the listed vendors will be available for the subscriber. Make sure to include respective vendor API keys in the AI Settings page.') }}."></i></h6>
											<select class="form-select" id="voiceover-vendors" name="voiceover_vendors[]" data-placeholder="{{ __('Choose Voiceover vendors') }}" multiple>
												<option value='aws'>{{ __('AWS') }}</option>																															
												<option value='azure'> {{ __('Azure') }}</option>																															
												<option value='gcp'> {{ __('GCP') }}</option>																															
												<option value='openai'> {{ __('OpenAI') }}</option>																															
												<option value='elevenlabs'> {{ __('ElevenLabs') }}</option>	
												@if (App\Services\HelperService::extensionWatson())
													<option value='ibm'> {{ __('IBM') }}</option>		
												@endif		
												@if (App\Services\HelperService::extensionSpeechifyTextToSpeech())
													<option value='speechify'>{{ __('Speechify') }}</option>																																																																																				
												@endif		
												@if (App\Services\HelperService::extensionLemonfoxTextToSpeech())
													<option value='lemonfox'>{{ __('Lemonfox') }}</option>																																																																																				
												@endif																																																										
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Included AI Image Vendors') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Only listed AI Image vendors models will be available for the subscriber. Make sure to include respective vendor API keys in the AI Settings page.') }}."></i></h6>
												<select class="form-select" id="image-vendors" name="image_vendors[]" data-placeholder="{{ __('Choose AI Image vendors') }}" multiple>
													<option value='openai'>{{ __('OpenAI') }}</option>																															
													<option value='sd'> {{ __('Stable Diffusion') }}</option>	
													@if (App\Services\HelperService::extensionFlux())																														
														<option value='falai'> {{ __('Fal AI') }}</option>
													@endif	
													@if (App\Services\HelperService::extensionMidjourney())																														
														<option value='midjourney'> {{ __('Midjourney') }}</option>
													@endif		
													@if (App\Services\HelperService::extensionClipdrop())																														
														<option value='clipdrop'> {{ __('Clipdrop') }}</option>
													@endif	
													@if (App\Services\HelperService::extensionNanoBanana())
														<option value='google'> {{ __('Google') }}</option>																																																																																													
													@endif																																																																																											
												</select>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Number of Team Members') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Define how many team members a user is allowed to create under this subscription plan') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" id="team-members" name="team-members" min=0 value="0" required>
												</div> 
												@error('team-members')
													<p class="text-danger">{{ $errors->first('team-members') }}</p>
												@enderror
											</div> 						
										</div>																			

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Maximum Allowed CSV File Size') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum CSV file size limit that subscriber is allowed to process') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0.1" step="0.1" id="chat-csv-file-size" name="chat-csv-file-size" value="1.0">
													<span class="text-muted fs-10">{{ __('Maximum Size limit is in Megabytes (MB)') }}.</span>
												</div> 
												@error('chat-csv-file-size')
													<p class="text-danger">{{ $errors->first('chat-csv-file-size') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Maximum Allowed PDF File Size') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum PDF file size limit that subscriber is allowed to process') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0.1" step="0.1" id="chat-pdf-file-size" name="chat-pdf-file-size" value="1.0">
													<span class="text-muted fs-10">{{ __('Maximum Size limit is in Megabytes (MB)') }}.</span>
												</div> 
												@error('chat-pdf-file-size')
													<p class="text-danger">{{ $errors->first('chat-pdf-file-size') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Maximum Allowed Word File Size') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum Word file size limit that subscriber is allowed to process') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0.1" step="0.1" id="chat-word-file-size" name="chat-word-file-size" value="1.0">
													<span class="text-muted fs-10">{{ __('Maximum Size limit is in Megabytes (MB)') }}.</span>
												</div> 
												@error('chat-word-file-size')
													<p class="text-danger">{{ $errors->first('chat-word-file-size') }}</p>
												@enderror
											</div> 						
										</div>																					

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Image/Video/Voiceover Results Storage Period') }} <span class="text-muted">({{ __('In Days') }})</span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('After set days file results will be deleted via CRON task') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" id="file-result-duration" name="file-result-duration" value="-1">
													<span class="text-muted fs-10">{{ __('Set as -1 for unlimited storage duration') }}.</span>
												</div> 
												@error('file-result-duration')
													<p class="text-danger">{{ $errors->first('file-result-duration') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Generated Text Content Results Storage Period') }} <span class="text-muted">({{ __('In Days') }})</span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('After set days results will be deleted from database via CRON task') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" id="document-result-duration" name="document-result-duration" value="-1">
													<span class="text-muted fs-10">{{ __('Set as -1 for unlimited storage duration') }}.</span>
												</div> 
												@error('document-result-duration')
													<p class="text-danger">{{ $errors->first('document-result-duration') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Max Allowed Words Limit for All Text Results') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('OpenAI will treat this limit as a stop marker. i.e. If you set it to 500, openai will try to stop as it will create a text with 500 tokens, but it can also ignore it on some cases') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" id="tokens" name="tokens" value="4000" required>
												</div> 
												@error('tokens')
													<p class="text-danger">{{ $errors->first('tokens') }}</p>
												@enderror
											</div> 						
										</div>
									
									
								</div>
							</div>
						</div>

						<div class="card mt-7 mb-7 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-box-circle-check text-info fs-14 mr-1 fw-2"></i>{{ __('Included Extension Features') }}</h6>

								@if (App\Services\HelperService::extensionPlagiarism())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Plagiarism Check and Content Detector Extension') }}</h6>
									
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Plagiarism Checker Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="plagiarism-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Content Detector Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="detector-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Scan tasks for AI Plagiarism Checker') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="plagiarism-pages" name="plagiarism-pages" value="0">
												</div> 
												@error('plagiarism-pages')
													<p class="text-danger">{{ $errors->first('plagiarism-pages') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Scan tasks for AI Content Decoder') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="detector-pages" name="detector-pages" value="0">
												</div> 
												@error('detector-pages')
													<p class="text-danger">{{ $errors->first('detector-pages') }}</p>
												@enderror
											</div> 						
										</div>										
									</div>
								@endif
								
								@if (App\Services\HelperService::extensionVoiceClone())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Voice Clone Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Voice Clone Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="voice-clone-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Maximum Allowed Created Voice Clones') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the number of voice clones that user can create') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="voice_clone_number" name="voice_clone_number" value="0">
												</div> 
												@error('voice_clone_number')
													<p class="text-danger">{{ $errors->first('voice_clone_number') }}</p>
												@enderror
											</div> 						
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionSoundStudio())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Sound Studio Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Sound Studio Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="sound-studio-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPhotoStudio())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Photo Studio Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Photo Studio Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="photo-studio-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPebblely())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Product Photo Extension') }}</h6>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Product Photo') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="product-photo-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionVideoImage())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Video (Image to Video) Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Image to Video Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="video-image-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionVideoText())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Video (Text to Video) Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Text to Video Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="video-text-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionVideoVideo())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Video (Video to Video) Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Video to Video Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="video-video-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionWordpressIntegration())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Wordpress Integration Extension') }}</h6>
									
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Wordpress Integration Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="wordpress-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Wordpress Websites') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Number of Wordpress Websites use will be able to connect') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="wordpress-website-number" name="wordpress-website-number" value="0">
												</div> 
												@error('wordpress-website-number')
													<p class="text-danger">{{ $errors->first('wordpress-website-number') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Wordpress Posts Scheduled') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Number of active posts in the schedule queue') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="wordpress-post-number" name="wordpress-post-number" value="0">
												</div> 
												@error('wordpress-post-number')
													<p class="text-danger">{{ $errors->first('wordpress-post-number') }}</p>
												@enderror
											</div> 						
										</div>
									</div>
								@endif		
								
								@if (App\Services\HelperService::extensionAvatar())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Avatar Extension') }}</h6>
									
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Avatar Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="avatar_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Create Video from AI Avatar Videos Option') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="avatar_video_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Create Video from AI Avatar Photos Option') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="avatar_image_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Video from AI Avatar Videos Tasks') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="avatar_video_numbers" name="avatar_video_numbers" value="0">
												</div> 
												@error('avatar_video_numbers')
													<p class="text-danger">{{ $errors->first('avatar_video_numbers') }}</p>
												@enderror
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Video from AI Avatar Photos Tasks') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="avatar_image_numbers" name="avatar_image_numbers" value="0">
												</div> 
												@error('avatar_image_numbers')
													<p class="text-danger">{{ $errors->first('avatar_image_numbers') }}</p>
												@enderror
											</div> 						
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionVoiceIsolator())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Voice Isolator') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Voice Isolator Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="voice-isolator-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionFaceswap())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Faceswap Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Faceswap Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="faceswap-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionMusic())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Music Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Music Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="music-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionSEO())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('SEO Tool Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('SEO Tool Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="seo-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionSocialMedia())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Social Media Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Social Media Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="social-media-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionChatShare())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Chat Share Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Chat Share Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="chat-share-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionTextract())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Textract Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Textract Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="textract-feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionRealtimeChat())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI Realtime Voice Chat Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI Realtime Voice Chat Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="chat_realtime_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionExternalChatbot())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI External Chatbot Extension') }}</h6>

										<div class="col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI External Chatbot Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="chatbot_external_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Chatbots') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" name="chatbot_external_quantity" value="0">
												</div> 
											</div> 						
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Domains per Chatbot') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" name="chatbot_external_domains" value="0">
												</div> 
											</div> 						
										</div>
									</div>	
								@endif

								@if (App\Services\HelperService::extensionExternalChatbotAnalytics())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('AI External Chatbot Analytics Extension') }}</h6>

										<div class="col-sm-12">
											<div class="input-box">
												<h6>{{ __('AI External Chatbot Analytics Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="chatbot_external_analytics_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionSpeechToTextPro())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Speech to Text Pro Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Speech To Text Pro Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="speech_text_pro_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	
									</div>	
								@endif

								@if (App\Services\HelperService::extensionTelegramBot())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Telegram Bot Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Telegram Bot Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="telegram_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	

										<div class="col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Telegram Bot Channels') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="1" name="telegram_total_bots" value="1">
												</div> 
											</div> 						
										</div>
									</div>	
								@endif

								@if (App\Services\HelperService::extensionWhatsappBot())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Whatsapp Bot Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Whatsapp Bot Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="whatsapp_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>	

										<div class="col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Total Allowed Whatsapp Bot Channels') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="1" name="whatsapp_total_bots" value="1">
												</div> 
											</div> 						
										</div>
									</div>	
								@endif

								@if (App\Services\HelperService::extensionSpeechifyVoiceClone())
									<div class="row subscription-extension-row">	
										<h6 class="fs-12 mb-5 text-muted">{{ __('Speechify Voice Clone Extension') }}</h6>

										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="input-box">
												<h6>{{ __('Voice Clone Feature') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group mt-3">
													<label class="custom-switch">
														<input type="checkbox" name="speechify_voice_clone_feature" class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12">							
											<div class="input-box">								
												<h6>{{ __('Maximum Allowed Created Speechify Voice Clones') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the number of voice clones that user can create') }}."></i></h6>
												<div class="form-group">							    
													<input type="number" class="form-control" min="0" id="speechify_voice_clone_number" name="speechify_voice_clone_number" value="0">
												</div> 
												@error('speechify_voice_clone_number')
													<p class="text-danger">{{ $errors->first('speechify_voice_clone_number') }}</p>
												@enderror
											</div> 						
										</div>
									</div>
								@endif

								
							</div>
						</div>

						<div class="card mt-7 shadow-0">
							<div class="card-body">
								<h6 class="fs-12 font-weight-bold mb-5 plan-title-bar"><i class="fa-solid fa-filter-list text-info fs-14 mr-1 fw-2"></i>{{ __('Extra') }} <span class="text-muted">({{ __('Optional') }})</span></h6>

								<div class="row mt-6">
									<div class="col-12">
										<div class="input-box">	
											<h6>{{ __('Primary Heading') }} </h6>
											<div class="form-group">							    
												<input type="text" class="form-control" id="primary-heading" name="primary-heading" value="{{ old('primary-heading') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="row mt-6">
									<div class="col-lg-12 col-md-12 col-sm-12">	
										<div class="input-box">	
											<h6>{{ __('Plan Features') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span> <span class="text-danger ml-3">({{ __('Comma Seperated') }})</span></h6>							
											<textarea class="form-control" name="features" rows="10">{{ old('features') }}</textarea>
											@error('features')
												<p class="text-danger">{{ $errors->first('features') }}</p>
											@enderror	
										</div>											
									</div>
								</div>
							</div>
						</div>
						

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<a href="{{ route('admin.finance.plans') }}" class="btn btn-cancel mr-2 pl-7 pr-7">{{ __('Return') }}</a>
							<button type="submit" class="btn btn-primary pl-7 pr-7">{{ __('Create') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-12">
			<div class="card border-0 cost-sticky">
				<div class="card-header border-0 pb-0">
					<h6 class="card-title fs-12 text-muted">{{ __('Calculate Cost and Margin') }} (USD)</h6>
				</div>						
				<div class="card-body pt-0">		
					<hr class="mt-0">							
					<h6 class="fs-12 font-weight-semibold">{{ __('OpenAI Cost') }}:</h6>
					<ul>
						<ol class="fs-11 mb-1 text-muted">{{ __('GPT 3.5 Turbo Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gpt-3t">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('GPT 4 Turbo Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gpt-4t">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('GPT 4 Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gpt-4">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('GPT 4o Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gpt-4o">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('GPT 4o mini Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gpt-4o-mini">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('o1 mini Model') }}: <span class="text-warning cost-right-side">$<span id="cost-o1-mini">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('o1 Model') }}: <span class="text-warning cost-right-side">$<span id="cost-o1">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('o3 mini Model') }}: <span class="text-warning cost-right-side">$<span id="cost-o3-mini">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('Fine Tuned Model') }}: <span class="text-warning cost-right-side">$<span id="cost-fine-tuned">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('Whisper') }} (STT): <span class="text-warning cost-right-side">$<span id="cost-whisper">0</span></span></ol>
					</ul>
					<h6 class="fs-12 mt-3 font-weight-semibold">{{ __('Anthropic Cost') }}:</h6>
					<ul>
						<ol class="fs-11 mb-1 text-muted">{{ __('Claude 3 Opus Model') }}: <span class="text-warning cost-right-side">$<span id="cost-opus">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('Claude 3.5 Sonnet Model') }}: <span class="text-warning cost-right-side">$<span id="cost-sonnet">0</span></span></ol>
						<ol class="fs-11 mb-1 text-muted">{{ __('Claude 3.5 Haiku Model') }}: <span class="text-warning cost-right-side">$<span id="cost-haiku">0</span></span></ol>
					</ul>
					<h6 class="fs-12 mt-3 font-weight-semibold">{{ __('Gemini Cost') }}:</h6>
					<ul>
						<ol class="fs-11 mb-1 text-muted">{{ __('Gemini Pro Model') }}: <span class="text-warning cost-right-side">$<span id="cost-gemini">0</span></span></ol>
					</ul>
					<h6 class="fs-12 mt-3 font-weight-semibold">{{ __('Voiceover Cost') }}:</h6>
					<ul>
						<ol class="fs-11 mb-1 text-muted">{{ __('Characters') }} (TTS): <span class="text-warning cost-right-side">$<span id="cost-tts">0</span></span></ol>
					</ul>
					<hr>
					<h6 class="fs-12 mt-3 font-weight-semibold text-muted">{{ __('Target Price') }}: <span class="text-warning cost-right-side">$<span id="target-price">0</span></span></h6>
					<h6 class="fs-12 mt-3 font-weight-semibold text-muted">{{ __('Total Cost') }}: <span class="text-warning cost-right-side">$<span id="total-cost">0</span></span></h6>
					<h6 class="fs-12 mt-3 font-weight-semibold text-muted">{{ __('Net Profit') }}: <span class="text-warning cost-right-side">$<span id="net-profit">0</span></span></h6>
				</div>
			</div>
		</div>
			
		
	</div>
@endsection

@section('js')
	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
	<script>
		let total_cost = 0;
		let target_price = 0;
		let net_profit = 0;
		let cost_gpt_3t = 0;
		let cost_gpt_4t = 0;
		let cost_gpt_4 = 0;
		let cost_gpt_4o = 0;
		let cost_gpt_4o_mini = 0;
		let cost_o1_mini = 0;
		let cost_o1_preview = 0;
		let cost_fine_tuned = 0;
		let cost_whisper = 0;
		let cost_opus = 0;
		let cost_sonnet = 0;
		let cost_haiku = 0;
		let cost_gemini = 0;
		let cost_tts = 0;

		$("#voiceover-vendors").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$("#template-categories").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$("#chat-categories").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$("#templates-models-list").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$("#chats-models-list").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$("#image-vendors").select2({
			theme: "bootstrap-5",
			containerCssClass: "select2--small",
			dropdownCssClass: "select2--small",
		});

		$('#gpt_3_turbo').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gpt_3t }}';
			if (credits > 0) cost_gpt_3t = (credits/1000) * price; 
			if (credits == 0) cost_gpt_3t = 0; 
			let view = document.getElementById('cost-gpt-3t').innerHTML = cost_gpt_3t;
			calculateTotalCost();
		});

		$('#gpt_4_turbo').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gpt_4t }}';
			if (credits > 0) cost_gpt_4t = (credits/1000) * price; 
			if (credits == 0) cost_gpt_4t = 0; 
			let view = document.getElementById('cost-gpt-4t').innerHTML = cost_gpt_4t;
			calculateTotalCost();
		});

		$('#gpt_4').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gpt_4 }}';
			if (credits > 0) cost_gpt_4 = (credits/1000) * price; 
			if (credits == 0) cost_gpt_4 = 0; 
			let view = document.getElementById('cost-gpt-4').innerHTML = cost_gpt_4;
			calculateTotalCost();
		});

		$('#gpt_4o').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gpt_4o }}';
			if (credits > 0) cost_gpt_4o = (credits/1000) * price; 
			if (credits == 0) cost_gpt_4o = 0; 
			let view = document.getElementById('cost-gpt-4o').innerHTML = cost_gpt_4o;
			calculateTotalCost();
		});

		$('#gpt_4o_mini').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gpt_4o_mini }}';
			if (credits > 0) cost_gpt_4o_mini = (credits/1000) * price; 
			if (credits == 0) cost_gpt_4o_mini = 0; 
			let view = document.getElementById('cost-gpt-4o-mini').innerHTML = cost_gpt_4o_mini;
			calculateTotalCost();
		});

		$('#o1_mini').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->o1_mini }}';
			if (credits > 0) cost_o1_mini = (credits/1000) * price; 
			if (credits == 0) cost_o1_mini = 0; 
			let view = document.getElementById('cost-o1-mini').innerHTML = cost_o1_mini;
			calculateTotalCost();
		});

		$('#o1_preview').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->o1_preview }}';
			if (credits > 0) cost_o1_preview = (credits/1000) * price; 
			if (credits == 0) cost_o1_preview = 0; 
			let view = document.getElementById('cost-o1-preview').innerHTML = cost_o1_preview;
			calculateTotalCost();
		});

		$('#fine_tune').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->fine_tuned }}';
			if (credits > 0) cost_fine_tuned = (credits/1000) * price; 
			if (credits == 0) cost_fine_tuned = 0; 
			let view = document.getElementById('cost-fine-tuned').innerHTML = cost_fine_tuned;
			calculateTotalCost();
		});

		$('#claude_3_opus').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->claude_3_opus }}';
			if (credits > 0) cost_opus = (credits/1000) * price; 
			if (credits == 0) cost_opus = 0; 
			let view = document.getElementById('cost-opus').innerHTML = cost_opus;
			calculateTotalCost();
		});

		$('#claude_3_sonnet').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->claude_3_sonnet }}';
			if (credits > 0) cost_sonnet = (credits/1000) * price; 
			if (credits == 0) cost_sonnet = 0; 
			let view = document.getElementById('cost-sonnet').innerHTML = cost_sonnet;
			calculateTotalCost();
		});

		$('#claude_3_haiku').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->claude_3_haiku }}';
			if (credits > 0) cost_haiku = (credits/1000) * price; 
			if (credits == 0) cost_haiku = 0; 
			let view = document.getElementById('cost-haiku').innerHTML = cost_haiku;
			calculateTotalCost();
		});

		$('#gemini_pro').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->gemini_pro }}';
			if (credits > 0) cost_gemini = (credits/1000) * price; 
			if (credits == 0) cost_gemini = 0; 
			let view = document.getElementById('cost-gemini').innerHTML = cost_gemini;
			calculateTotalCost();
		});

		$('#minutes').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->whisper }}';
			if (credits > 0) cost_whisper = credits * price; 
			if (credits == 0) cost_whisper = 0; 
			let view = document.getElementById('cost-whisper').innerHTML = cost_whisper;
			calculateTotalCost();
		});

		$('#characters').on('keyup', function () {
			let credits = $(this).val();
			let price = '{{ $prices->aws_tts }}';
			if (credits > 0) cost_tts = (credits/1000000) * price; 
			if (credits == 0) cost_tts = 0; 
			let view = document.getElementById('cost-tts').innerHTML = cost_tts;
			calculateTotalCost();
		});

		$('#cost').on('keyup', function () {
			let cost = $(this).val();
			if (cost > 0) target_price = cost; 
			if (cost == 0) target_price = 0; 
			calculateTotalCost();
		});

		function duration_select(value) {
			if (value == 'lifetime') {
				$('#payment-gateways').css('display', 'none');
			} else {
				$('#payment-gateways').css('display', 'block');
			}
		} 

		function calculateTotalCost() {
			total_cost = cost_gpt_3t + cost_gpt_4t + cost_gpt_4 + cost_gpt_4o + cost_gpt_4o_mini + cost_o1_mini + cost_o1_preview + cost_fine_tuned + cost_whisper + cost_opus + cost_sonnet + cost_haiku + cost_gemini + cost_tts;
			document.getElementById('total-cost').innerHTML = total_cost;
			if (target_price > 0) {
				document.getElementById('target-price').innerHTML = target_price;
				net_profit = target_price - total_cost;
				document.getElementById('net-profit').innerHTML = net_profit;
			}
		}
	</script>
@endsection

