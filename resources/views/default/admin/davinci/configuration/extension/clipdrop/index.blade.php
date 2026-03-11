@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Clipdrop') }}</h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.configs')}}"> {{ __('AI Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Extensions') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-7 pl-7 pr-7 pb-6">									
					<form action="{{ route('admin.davinci.configs.clipdrop.store') }}" method="post" enctype="multipart/form-data">
						@csrf

						<div class="card shadow-0 mb-6">							
							<div class="card-body">
									<div class="row">
										<div class="col-sm-12">
											<div class="input-box mb-3">								
												<h6>{{ __('Clipdrop API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<div class="form-group">							    
													<input type="text" class="form-control @error('clipdrop_api') is-danger @enderror" id="clipdrop_api" name="clipdrop_api" value="{{ $extension->clipdrop_api }}" autocomplete="off">
													@error('clipdrop_api')
														<p class="text-danger">{{ $errors->first('clipdrop_api') }}</p>
													@enderror
												</div> 												
											</div> 
										</div>
									</div>
											
							</div>
						</div>						

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-8 pr-8 pt-2 pb-2">{{ __('Save') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection


