@extends('layouts.app')

@section('page-header')
	<div class="container">	
		<div class="row">
			<div class="col-sm-12 mt-4">
				<a class="text-muted fs-13 chatbot-return-back" href="{{route('user.extension.chatbot.knowledge')}}"><i class="fa-solid fa-angle-left mr-2"></i> {{__('Return to Knowledge Bases')}}</a>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')

<div class="row">
	<div class="col-sm-12">
		<div class="card mb-4 border-0">
			<div class="pl-5 pl-6 border-0">
				<h4 class="mt-5 fs-20 font-weight-bold">{{__('Manage Chatbot Attachments')}}</h4>
				<h6 class="text-muted">{{__('Attach or detach this knowledge base to chatbots')}}</h6>
			</div>
		</div>
	</div>

	<div class="col-sm-12 mt-5">
		<div class="card">
			<div class="card-body">
				<form action="{{ route('user.extension.chatbot.knowledge.attachments.update', $embedding->id) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-12">
							<div class="input-box">
								<div class="form-group">
									<label class="form-label fs-12">{{__('Knowledge Base')}}</label>
									<input type="text" class="form-control" value="{{ $embedding->title }}" disabled>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="input-box">
								<div class="form-group">
									<label class="form-label fs-12">{{__('Type')}}</label>
									<input type="text" class="form-control" value="{{ ucfirst($embedding->type) }}" disabled>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="input-box">
								<div class="form-group">
									<label class="form-label fs-12">{{__('Attached Chatbots')}} <span class="text-danger">*</span></label>
									<select class="form-control @error('chatbot_ids') is-invalid @enderror" name="chatbot_ids[]" id="chatbot_ids" multiple required>
										@foreach($chatbots as $chatbot)
											<option value="{{ $chatbot->id }}" {{ in_array($chatbot->id, old('chatbot_ids', $attachedChatbots)) ? 'selected' : '' }}>
												{{ $chatbot->chatbot_name }}
											</option>
										@endforeach
									</select>
									<small class="text-muted fs-12">{{__('Select one or more chatbots to attach this knowledge base')}}</small>
									@error('chatbot_ids')
										<span class="invalid-feedback">{{ $message }}</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="col-md-12 mb-3">
							<a href="{{ route('user.extension.chatbot.knowledge') }}" class="btn btn-cancel">{{__('Return')}}</a>
							<button type="submit" class="btn btn-primary">{{__('Update')}}</button>							
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@if(session('success'))
	<script>
		toastr.success('{{ session('success') }}');
	</script>
@endif

@if(session('error'))
	<script>
		toastr.error('{{ session('error') }}');
	</script>
@endif

@endsection

@section('js')
	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var selectedChatbots = @json($attachedChatbots);
			$('#chatbot_ids').select2({
				placeholder: '{{ __("Select chatbots") }}',
				allowClear: false,
				width: '100%'
			}).val(selectedChatbots).trigger('change');
		});
	</script>
@endsection
