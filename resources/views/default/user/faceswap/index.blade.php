@extends('layouts.app')
@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Green Audio Players CSS -->
	<link href="{{ URL::asset('plugins/audio-player/green-audio-player.css') }}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
	<div class="row mt-24 justify-content-center">
		<div class="row no-gutters justify-content-center">
			<div class="col-lg-9 col-md-11 col-sm-12 text-center">
				<h3 class="card-title mt-2 fs-20"><i class="fa-solid fa-masks-theater mr-2 text-primary"></i></i>{{ __('Faceswap') }}</h3>
				<h6 class="text-muted mb-7">{{ __('Seamlessly swap faces in your images') }}</h6>
			</div>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-header pt-4 border-0">
					<p class="fs-11 text-muted mb-0 text-left"><i class="fa-solid fa-bolt-lightning mr-2 text-primary"></i>{{ __('Your Balance is') }} <span class="font-weight-semibold" id="balance-number">@if (auth()->user()->images == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->images + auth()->user()->images_prepaid) }}@endif {{ __('credits') }}</span></p>
				</div>
				<form id="swap-form" action="" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="card-body pt-2 pl-6 pr-6 pb-5" id="">
						<div class="input-box">								
							<h6 class="text-muted">{{ __('Title') }}</h6>
							<div class="form-group">							    
								<input type="text" class="form-control" id="title" name="title" value="{{__('Untitled Image')}}">
							</div> 
						</div>
						<div class="input-box">
							<h6 class="text-muted">{{ __('Target Image') }} <i class="ml-1 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('The face in this image will be swapped out with the swap image face') }}."></i></h6>
							<div id="image-drop-box">
								<div class="image-drop-area text-center mt-2 file-drop-border" >
									<input type="file" class="main-image-input" name="target_image" id="target_image" accept="image/png, image/jpeg" onchange="loadTarget(event)" required>
									<div class="image-upload-icon">
										<i class="fa-solid fa-image-landscape fs-28 text-muted"></i>
									</div>
									<p class="text-dark fw-bold mb-0 mt-1">
										{{ __('Drag and drop your target image or') }}
										<a href="javascript:void(0);" class="text-primary">{{ __('Browse') }}</a>
									</p>
									<p class="mb-5 file-name fs-12 text-muted">
										<small>{{ __('PNG | JPG') }}</small>
									</p>
								</div>

								<img id="target-image-view" class="mb-4 mt-4 ml-auto mr-auto">
							</div>
						</div>

						<div class="input-box">
							<h6 class="text-muted">{{ __('Swap Image') }} <i class="ml-1 text-dark fs-13 fa-solid fa-circle-info" style="z-index: 100" data-tippy-content="{{ __('The face in this image will be swapped onto the target image face') }}."></i></h6>
							<div id="image-drop-box">
								<div class="image-drop-area text-center mt-2 file-drop-border">
									<input type="file" class="main-image-input" name="swap_image" id="swap_image" accept="image/png, image/jpeg" onchange="loadSwap(event)" required>
									<div class="image-upload-icon">
										<i class="fa-solid fa-image-landscape fs-28 text-muted"></i>
									</div>
									<p class="text-dark fw-bold mb-0 mt-1">
										{{ __('Drag and drop your swap image or') }}
										<a href="javascript:void(0);" class="text-primary">{{ __('Browse') }}</a>
									</p>
									<p class="mb-5 file-name fs-12 text-muted">
										<small>{{ __('PNG | JPG') }}</small>
									</p>
								</div>

								<img id="swap-image-view" class="mb-4 mt-4 ml-auto mr-auto">
							</div>
						</div>
						

						<div class="text-center mt-3 mb-2">
							<button type="submit" class="btn btn-primary ripple main-action-button" id="generate" style="text-transform: none; min-width: 200px;">{{ __('Swap Faces') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="col-lg-6 col-md-8 col-xm-12">
			@if ($results->count())
				<div id="photo-studio-result">		
					<div class="row" id="results-container">
						@foreach ($results as $result)
							<div class="col-md-6 col-sm-12">
								<div class="card p-4 border-0">
									<img src="{{$result->result_image}}">
									<div class="text-center mt-3 relative">
										<h6 class="mb-1 font-weight-semibold">{{$result->title}}</h6>
										<p class="text-muted fs-12 mb-1">{{date('M d, Y', strtotime($result->created_at))}}</p>
										@if ($result->status == 'processing')
											<p class="text-muted fs-12 mb-0">({{__('Processing')}})</p>
										@endif 										
										<a href="" class="avatar-result-delete" data-id="{{ $result->id }}" data-tippy-content="{{ __('Delete Video Result') }}"><i class="fa-solid fa-trash-xmark"></i></a>		
									</div>
								</div>
							</div>
						@endforeach
						
					</div>									
				</div>
			@else
				<div id="photo-studio-placeholder" class="text-center">
					<span><i class="fa-solid fa-masks-theater fs-40 mb-2 text-muted"></i></span>
					<h6 class="text-muted">{{ __('Start swapping faces') }}</h6>
				</div>
				<div id="photo-studio-result" class="hidden">		
					<div id="results-container">
						
					</div>									
				</div>
			@endif
			
		</div>	

	</div>
</div>
@endsection
@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		let loading = `<span class="loading">
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						</span>`;

		var loadTarget = function(event) {
			var output = document.getElementById('target-image-view');
			output.style.display = 'block';
			output.src = URL.createObjectURL(event.target.files[0]);
			output.onload = function() {
				URL.revokeObjectURL(output.src) // free memory
			}
		};

		var loadSwap = function(event) {
			var output = document.getElementById('swap-image-view');
			output.style.display = 'block';
			output.src = URL.createObjectURL(event.target.files[0]);
			output.onload = function() {
				URL.revokeObjectURL(output.src) // free memory
			}
		};



		// SUBMIT FORM
		$('#swap-form').on('submit', function(e) {

			e.preventDefault();

			let form = new FormData(this);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: '/app/user/faceswap/create',
				data: form,
				contentType: false,
				processData: false,
				cache: false,
				beforeSend: function() {
					$('#generate').prop('disabled', true);
					let btn = document.getElementById('generate');					
					btn.innerHTML = loading;  
					document.querySelector('#loader-line')?.classList?.remove('hidden');      
				},
				complete: function() {
					document.querySelector('#loader-line')?.classList?.add('hidden'); 
					$('#generate').prop('disabled', false);
					$('#generate').html('{{ __("Swap Faces") }}');            
				},
				success: function (data) {		
						
					if (data['status'] == 'success') {		

						$('#photo-studio-placeholder').addClass('hidden');
						$('#photo-studio-result').removeClass('hidden');

						$('#results-container').prepend(data['result']);

						toastr.success(data['message']);	
						
					} else {						
						Swal.fire('{{ __('Swap Generation Error') }}', data['message'], 'warning');
					}
				},
				error: function(data) {
					$('#image-generate').prop('disabled', false);
					$('#image-generate').html('<i class=" fa-solid fa-wand-magic-sparkles mr-2"></i>{{ __("Swap Faces") }}'); 
				}
			});
		});


		$(document).on('click', '.avatar-result-delete', function(e) {

			e.preventDefault();

			let formData = new FormData();
			formData.append("id", $(this).attr('data-id'));
			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: '/app/user/faceswap/delete',
				data: formData,
				processData: false,
				contentType: false,
				success: function (data) {
					console.log(data)
					if (data == 200) {
						toastr.success('{{ __('Result has been deleted successfully') }}');	
						window.location.reload();							
					} else {
						toastr.warning('{{ __('There was an error deleting the result') }}');
					}      
				},
				error: function(data) {
					toastr.warning('{{ __('There was an error deleting the result') }}');
				}
			})
		});
		
	</script>
@endsection