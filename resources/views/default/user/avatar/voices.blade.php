@extends('layouts.app')

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
									<a class="side-menu__item" href="{{ route('user.extension.avatar.results') }}">
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
									<a class="side-menu__item active" href="{{ route('user.extension.avatar.voices') }}">
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
							<div class="col-sm-12">
								<div id="voice-search-panel">									
									<h6 class="text-muted mb-3 mt-3">{{ __('Search for your AI Voice that you want to use for your Avatar Video') }}</h6>
									<div class="search-template">
										<div class="input-box">								
											<div class="form-group">							    
												<input type="text" class="form-control" id="search-template" placeholder="{{ __('Search for your AI Voice...') }}">
											</div> 
										</div> 
									</div>
									
								</div>
							</div>
							<div class="">
								<div class="row p-4" id="voices-list">
									@foreach ($favorites as $favorite)
										@foreach ($voices['data']['voices'] as $voice)
											@if ($favorite == $voice['voice_id'])
												<div class="col-md-3 col-sm-6">
													<div class="card mb-6 shadow-0 p-4 avatar-voice-samples-box">
														<a href="#" class="avatar-favorite marked-favorite" data-id="{{ $voice['voice_id'] }}"><i class="fa-solid fa-heart text-muted"></i></a>
														<div class="d-flex avatar-voice-samples">
															<div class="flex">
																<button type="button" class="result-play text-center mr-2" title="{{__('Play Audio')}}" onclick="resultPlay(this)" src="{{ $voice['preview_audio'] }}" id="{{ $voice['voice_id']}}"><i class="fa fa-play table-action-buttons view-action-button"></i></button>											
															</div>
															<div class="flex mt-auto mb-auto">
																<h6 class="mb-2 font-weight-bold fs-12">{{ $voice['language'] }}</h6>
																<p class="mb-0 fs-11 text-muted">{{ $voice['name'] }} ({{ucfirst($voice['gender'])}})</p>
															</div>
																												
														</div>							
													</div>
												</div>
											@endif											
										@endforeach
									@endforeach	

									@foreach ($voices['data']['voices'] as $voice)
										@if(!in_array($voice['voice_id'], $favorites))	
											<div class="col-md-3 col-sm-6">
												<div class="card mb-6 shadow-0 p-4 avatar-voice-samples-box">
													<a href="#" class="avatar-favorite" data-id="{{ $voice['voice_id'] }}"><i class="fa-solid fa-heart text-muted"></i></a>
													<div class="d-flex avatar-voice-samples">
														<div class="flex">
															<button type="button" class="result-play text-center mr-2" title="{{__('Play Audio')}}" onclick="resultPlay(this)" src="{{ $voice['preview_audio'] }}" id="{{ $voice['voice_id']}}"><i class="fa fa-play table-action-buttons view-action-button"></i></button>											
														</div>
														<div class="flex mt-auto mb-auto">
															<h6 class="mb-2 font-weight-bold fs-12">{{ $voice['language'] }}</h6>
															<p class="mb-0 fs-11 text-muted">{{ $voice['name'] }} ({{ucfirst($voice['gender'])}})</p>
														</div>
																											
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
	<script src="{{ URL::asset('plugins/audio-player/green-audio-player.js') }}"></script>
	<script src="{{ theme_url('js/audio-player.js') }}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";


			$(document).on('click', '.avatar-favorite', function(e) {

				e.preventDefault();

				let formData = new FormData();
				formData.append("id", $(this).attr('data-id'));
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'post',
					url: '/app/user/avatar/list/voice/favorite',
					data: formData,
					processData: false,
					contentType: false,
					success: function (data) {
		
						if (data == 'added') {
							toastr.success('{{ __('Voice added to the favorite list successfully') }}');	
							window.location.reload();							
						} else if (data == 'removed') {
							toastr.success('{{ __('Voice removed from favorite list successfully') }}');
							window.location.reload();
						} else {
							toastr.warning('{{ __('There was an error editing voice favorite status') }}');
						}      
					},
					error: function(data) {
						toastr.warning('{{ __('There was an error editing voice favorite status') }}');
					}
				})
			});

			$(document).on('keyup', '#search-template', function () {

				var searchTerm = $(this).val().toLowerCase();
				$('#voices-list').find('> div').each(function () {
					if ($(this).filter(function() {
						return (($(this).find('h6').text().toLowerCase().indexOf(searchTerm) > -1) || ($(this).find('p').text().toLowerCase().indexOf(searchTerm) > -1));
					}).length > 0 || searchTerm.length < 1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		});
	</script>
@endsection
