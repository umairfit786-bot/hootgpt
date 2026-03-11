@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('AI Avatar') }}</h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
							class="fa-solid fa-microchip-ai mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.configs')}}">
						{{ __('AI Settings') }}</a></li>
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
					<form action="{{ route('admin.davinci.configs.avatar.store') }}" method="post"
						enctype="multipart/form-data">
						@csrf

						<div class="card shadow-0 mt-0 mb-7">
							<div class="card-body">
								<div class="row">

									<div class="col-sm-12">
										<div class="input-box">
											<h6>{{ __('Heygen API Key') }} <span class="text-required"><i
														class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">
												<input type="text"
													class="form-control @error('heygen_api') is-danger @enderror"
													id="heygen_api" name="heygen_api" value="{{ $extension->heygen_api }}"
													autocomplete="off">
												@error('heygen_api')
													<p class="text-danger">{{ $errors->first('heygen_api') }}</p>
												@enderror
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Avatar Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="heygen_avatar_feature"
														class="custom-switch-input" @if ($extension->heygen_avatar_feature)
														checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Avatar Free Tier Access') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="heygen_avatar_free_tier"
														class="custom-switch-input" @if ($extension->heygen_avatar_free_tier) checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Avatar Image Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="heygen_avatar_image"
														class="custom-switch-input" @if ($extension->heygen_avatar_image)
														checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Avatar Video Feature') }}</h6>
											<div class="form-group mt-3">
												<label class="custom-switch">
													<input type="checkbox" name="heygen_avatar_video"
														class="custom-switch-input" @if ($extension->heygen_avatar_video)
														checked @endif>
													<span class="custom-switch-indicator"></span>
												</label>
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="input-box">
											<h6>{{ __('Media Credits Cost per Image Avatar') }}</h6>
											<div class="form-group">
												<input type="number" min=1
													class="form-control @error('heygen_image_credit_cost') is-danger @enderror"
													id="heygen_image_credit_cost" name="heygen_image_credit_cost"
													value="{{ $extension->heygen_image_credit_cost ?? 1 }}"
													autocomplete="off">
												@error('heygen_image_credit_cost')
													<p class="text-danger">{{ $errors->first('heygen_image_credit_cost') }}</p>
												@enderror
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="input-box">
											<h6>{{ __('Media Credits Cost per Video Avatar') }}</h6>
											<div class="form-group">
												<input type="number" min=1
													class="form-control @error('heygen_video_credit_cost') is-danger @enderror"
													id="heygen_video_credit_cost" name="heygen_video_credit_cost"
													value="{{ $extension->heygen_video_credit_cost ?? 1 }}"
													autocomplete="off">
												@error('heygen_video_credit_cost')
													<p class="text-danger">{{ $errors->first('heygen_video_credit_cost') }}</p>
												@enderror
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="input-box">
											<h6>{{ __('Total Image Avatars per Free Tier User') }}</h6>
											<div class="form-group">
												<input type="number" min=0
													class="form-control @error('heygen_avatar_image_numbers') is-danger @enderror"
													id="heygen_avatar_image_numbers" name="heygen_avatar_image_numbers"
													value="{{ $extension->heygen_avatar_image_numbers }}"
													autocomplete="off">
												@error('heygen_avatar_image_numbers')
													<p class="text-danger">{{ $errors->first('heygen_avatar_image_numbers') }}
													</p>
												@enderror
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="input-box">
											<h6>{{ __('Total Video Avatars per Free Tier User') }}</h6>
											<div class="form-group">
												<input type="number" min=0
													class="form-control @error('heygen_avatar_video_numbers') is-danger @enderror"
													id="heygen_avatar_video_numbers" name="heygen_avatar_video_numbers"
													value="{{ $extension->heygen_avatar_video_numbers }}"
													autocomplete="off">
												@error('heygen_avatar_video_numbers')
													<p class="text-danger">{{ $errors->first('heygen_avatar_video_numbers') }}
													</p>
												@enderror
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>


						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit"
								class="btn ripple btn-primary pl-8 pr-8 pt-2 pb-2">{{ __('Save') }}</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
@endsection