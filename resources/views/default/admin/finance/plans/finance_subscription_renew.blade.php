@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center"> 
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Update Subscription Credits') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.plans') }}"> {{ __('Subscription Plans') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Update Subscription Credits') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-10 col-sm-12">
			<h3 class="card-title text-center mb-4">{{ __('Subscription Plan') }}: <span class="text-primary font-weight-bold">{{ $id->plan_name }}</span></h3>		
			<div class="card border-0">
				<div class="card-body pt-5">									
					<form action="{{ route('admin.finance.plan.push', $id) }}" method="POST" enctype="multipart/form-data">
						@csrf

						<h6 class="fs-12 text-center font-weight-bold"><i class="fa-solid fa-box-circle-check text-info fs-14 mr-1 fw-2"></i>{{ __('Renew AI Credits') }}</h6>
						<p class="fs-12 text-center text-muted mb-1">{{ __('One time credit renewal for active subscribers of this subscription plan') }}</p>
						<p class="fs-12 text-center text-muted mb-5">{{ __('Total active subscribers') }} {{ $subscribers }}</p>

						<div class="row">

							<div class="col-md-6 col-sm-12">							
								<div class="prepaid-view-box p-4">	
									<div class="input-box">
										<div class="form-group mt-3">
											<label class="custom-switch">
												<input type="checkbox" name="tokens_check" class="custom-switch-input">
												<span class="custom-switch-indicator"></span>
												<span class="ml-2 text-muted">{{ __('Include in renewal') }}</span>
											</label>
										</div>
									</div>						
									<div class="input-box mb-0">							
										<h6>{{ __('Token Credits') }} </h6>
										<div class="form-group">							    
											<input type="number" class="form-control" id="tokens" name="tokens" value="{{ $id->token_credits }}">
											<span class="text-muted fs-10">{{ __('Valid for all models') }}. {{ __('Set as -1 for unlimited tokens') }}.</span>
										</div> 
										@error('images')
											<p class="text-danger">{{ $errors->first('images') }}</p>
										@enderror
									</div>
								</div> 						
							</div>

							<div class="col-md-6 col-sm-12">							
								<div class="prepaid-view-box p-4">	
									<div class="input-box">
										<div class="form-group mt-3">
											<label class="custom-switch">
												<input type="checkbox" name="images_check" class="custom-switch-input">
												<span class="custom-switch-indicator"></span>
												<span class="ml-2 text-muted">{{ __('Include in renewal') }}</span>
											</label>
										</div>
									</div>						
									<div class="input-box mb-0">							
										<h6>{{ __('Media Credits') }} </h6>
										<div class="form-group">							    
											<input type="number" class="form-control" id="images" name="images" value="{{ $id->image_credits }}">
											<span class="text-muted fs-10">{{ __('Valid for all media vendors') }}. {{ __('Set as -1 for unlimited credits') }}.</span>
										</div> 
										@error('images')
											<p class="text-danger">{{ $errors->first('images') }}</p>
										@enderror
									</div>
								</div> 						
							</div>

							<div class="col-md-6 col-sm-12">							
								<div class="prepaid-view-box p-4">	
									<div class="input-box">
										<div class="form-group mt-3">
											<label class="custom-switch">
												<input type="checkbox" name="characters_check" class="custom-switch-input">
												<span class="custom-switch-indicator"></span>
												<span class="ml-2 text-muted">{{ __('Include in renewal') }}</span>
											</label>
										</div>
									</div>						
									<div class="input-box mb-0">							
										<h6>{{ __('Characters Included') }} </h6>
										<div class="form-group">							    
											<input type="number" class="form-control" id="characters" name="characters" value="{{ $id->characters }}" placeholder="0">
											<span class="text-muted fs-10">{{ __('For AI Voiceover feature') }}. {{ __('Set as -1 for unlimited characters') }}.</span>
										</div> 
										@error('characters')
											<p class="text-danger">{{ $errors->first('characters') }}</p>
										@enderror
									</div>
								</div> 						
							</div>

							<div class="col-md-6 col-sm-12">							
								<div class="prepaid-view-box p-4">		
									<div class="input-box">
										<div class="form-group mt-3">
											<label class="custom-switch">
												<input type="checkbox" name="minutes_check" class="custom-switch-input">
												<span class="custom-switch-indicator"></span>
												<span class="ml-2 text-muted">{{ __('Include in renewal') }}</span>
											</label>
										</div>
									</div>					
									<div class="input-box mb-0">							
										<h6>{{ __('Minutes Included') }}</h6>
										<div class="form-group">							    
											<input type="number" class="form-control" id="minutes" name="minutes" value="{{ $id->minutes }}" placeholder="0">
											<span class="text-muted fs-10">{{ __('For AI Speech to Text feature') }}. {{ __('Set as -1 for unlimited minutes') }}.</span>
										</div> 
										@error('minutes')
											<p class="text-danger">{{ $errors->first('minutes') }}</p>
										@enderror
									</div>
								</div> 						
							</div>

						</div>

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<a href="{{ route('admin.finance.plans') }}" class="btn btn-cancel mr-2 pl-7 pr-7">{{ __('Return') }}</a>
							<button type="submit" class="btn btn-primary pl-7 pr-7">{{ __('Renew') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script> 
	<script>

		let list = "{{ $id->voiceover_vendors }}"
		let templates = "{{ $id->model }}"
		let chats = "{{ $id->model_chat }}"
		list = list.split(', ')
		templates = templates.split(', ')
		chats = chats.split(', ')

		$(function(){
			$("#voiceover-vendors").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(list).trigger('change.select2');

			$("#templates-models-list").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(templates).trigger('change.select2');

			$("#chats-models-list").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(chats).trigger('change.select2');
		});


		 function duration_select(value) {
			if (value == 'lifetime') {
				$('#payment-gateways').css('display', 'none');
			} else {
				$('#payment-gateways').css('display', 'block');
			}
		 }
	</script>
@endsection
