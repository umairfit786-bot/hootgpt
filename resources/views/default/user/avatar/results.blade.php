@extends('layouts.app')

@section('css')
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')	
	<div class="row justify-content-center mt-5-7">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-5">
					<div class="row">
						<div class="col-lg-2 col-md-3 col-sm-12">
							<h4 class="page-title mb-0"><i class="text-primary mr-2 fs-16 fa-solid fa-aperture"></i> {{ __('AI Avatar') }}</h4>
							<h6 class="text-muted">{{ __('Create videos with AI') }}</h6>
							<a id="save-button" class="avatar-action-btn text-center mt-5" href="{{ route('user.extension.avatar') }}"><i class="fa-solid fa-clapperboard-play mr-4"></i>{{ __('Create Video') }}</a>
							<ul class="avatar-menu mt-3">
								<li class="slide">
									<a class="side-menu__item active" href="{{ route('user.extension.avatar.results') }}">
									<span class="side-menu__icon    fa-solid fa-photo-film"></span>
									<span class="side-menu__label">{{ __('Video Results') }}</span></a>
								</li>
							</ul>
							<ul class="avatar-menu">								
								<li class="side-item side-item-category mt-1 fs-12 text-muted">{{ __('Assets') }}</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.list.images') }}">
									<span class="side-menu__icon fa-solid fa-user-tie-hair"></span>
									<span class="side-menu__label">{{ __('Image Avatars') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.list.videos') }}">
									<span class="side-menu__icon fa-solid fa-camcorder"></span>
									<span class="side-menu__label">{{ __('Video Avatars') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.voices') }}">
									<span class="side-menu__icon fa-solid fa-message-lines"></span>
									<span class="side-menu__label">{{ __('AI Voices') }}</span></a>
								</li>
								<li class="slide">
									<a class="side-menu__item" href="{{ route('user.extension.avatar.uploads') }}">
									<span class="side-menu__icon fa-solid fa-cloud-arrow-up"></span>
									<span class="side-menu__label">{{ __('Uploads') }}</span></a>
								</li>
							</ul>
						</div>
						<div class="col-lg-10 col-md-9 col-sm-12">
							<div class="">
								<div class="row p-4">

										@foreach ($results as $result)
											@if ($result->status == 'completed')
												<div class="col-md-3 col-sm-6">
													<div class="card p-4 shadow-0">
														<video controls>
															<source src="{{ URL::asset($result->video_url)}}" type="video/mp4">
														</video>	
														<div class="text-center mt-3 relative">
															<h6 class="mb-1 font-weight-semibold">{{$result->title}}</h6>
															<p class="text-muted fs-12 mb-1">{{date('M d, Y', strtotime($result->created_at))}}</p>
															<p class="text-muted fs-12 mb-0">{{gmdate("H:i:s", $result->duration)}}</p>
															<a href="" class="avatar-result-delete" data-id="{{ $result->id }}" data-tippy-content="{{ __('Delete Video Result') }}"><i class="fa-solid fa-trash-xmark"></i></a>	
														</div>	
																	
													</div>
												</div>
											@endif											
										@endforeach

										@foreach ($results as $result)
											@if ($result->status == 'processing')
												<div class="col-md-3 col-sm-6">
													<div class="card p-4 shadow-0">
														<video controls>
															<source src="{{ URL::asset($result->video_url)}}" type="video/mp4">
														</video>	
														<div class="text-center mt-3">
															<h6 class="mb-1 font-weight-semibold">{{$result->title}}</h6>
															<p class="text-muted fs-12 mb-1">{{date('M d, Y', strtotime($result->created_at))}}</p>
															<p class="text-muted fs-12 mb-0">00:00:00</p>
															<p class="text-muted fs-12 mb-0">({{__('Processing...')}})</p>
														</div>					
													</div>
												</div>
											@endif											
										@endforeach
																			
								</div>	
							</div>								
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

			$(document).on('click', '.avatar-result-delete', function(e) {

				e.preventDefault();

				let formData = new FormData();
				formData.append("id", $(this).attr('data-id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/app/user/avatar/result/delete',
					data: formData,
					processData: false,
					contentType: false,
					success: function (data) {
						console.log(data)
						if (data == 200) {
							toastr.success('{{ __('Video result has been deleted successfully') }}');	
							window.location.reload();							
						} else {
							toastr.warning('{{ __('There was an error deleting the result file') }}');
						}      
					},
					error: function(data) {
						toastr.warning('{{ __('There was an error deleting the result file') }}');
					}
				})
			});
		});
	</script>
@endsection
