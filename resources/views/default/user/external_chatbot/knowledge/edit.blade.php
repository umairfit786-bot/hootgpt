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



@section('content')

<div class="row">
	<div class="col-sm-12">
		<div class="card mb-4 border-0">
			<div class="pl-5 pl-6 border-0">
				<h4 class="mt-5 fs-20 font-weight-bold">{{__('Edit Knowledge Base')}}</h4>
				<h6 class="text-muted">{{__('Update your chatbot knowledge base content and settings')}}</h6>
			</div>
		</div>
	</div>

	<div class="col-sm-12 mt-5">
		<div class="card">
			<div class="card-body">
				<form action="{{ route('user.extension.chatbot.knowledge.update', $embedding->id) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-12">
							<div class="input-box">
								<div class="form-group">
									<label class="form-label fs-12">{{__('Knowledge Base Type')}}</label>
									<input type="text" class="form-control" value="{{ ucfirst($embedding->type) }}" disabled>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="input-box">
								<div class="form-group">
									<label class="form-label fs-12">{{__('Title')}} <span class="text-danger">*</span></label>
									<input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $embedding->title) }}" required>
									@error('title')
										<span class="invalid-feedback">{{ $message }}</span>
									@enderror
								</div>
							</div>
						</div>

						@if($embedding->type === 'text')
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('Content')}} <span class="text-danger">*</span></label>
										<textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="10" required>{{ old('content', $embedding->content) }}</textarea>
										@error('content')
											<span class="invalid-feedback">{{ $message }}</span>
										@enderror
									</div>
								</div>
							</div>
						@endif

						@if($embedding->type === 'qa')
							@php
								$qaData = json_decode($embedding->content, true);
							@endphp
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('Question')}} <span class="text-danger">*</span></label>
										<input type="text" class="form-control @error('question') is-invalid @enderror" name="question" value="{{ old('question', $qaData['question'] ?? '') }}" required>
										@error('question')
											<span class="invalid-feedback">{{ $message }}</span>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('Answer')}} <span class="text-danger">*</span></label>
										<textarea class="form-control @error('answer') is-invalid @enderror" name="answer" rows="6" required>{{ old('answer', $qaData['answer'] ?? '') }}</textarea>
										@error('answer')
											<span class="invalid-feedback">{{ $message }}</span>
										@enderror
									</div>
								</div>
							</div>
						@endif

						@if($embedding->type === 'url')
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('URL')}}</label>
										<input type="text" class="form-control" value="{{ $embedding->url }}" disabled>
										<small class="text-muted fs-12">{{__('URL cannot be changed. Delete and create new if needed.')}}</small>
									</div>
								</div>
							</div>
						@endif

						@if($embedding->type === 'pdf')
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('PDF File')}}</label>
										<input type="text" class="form-control" value="{{ $embedding->title }}" disabled>
										<small class="text-muted fs-12">{{__('PDF file cannot be changed. Delete and upload new if needed.')}}</small>
									</div>
								</div>
							</div>
						@endif

						@if($embedding->type === 'youtube')
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{__('YouTube URL')}}</label>
										<input type="text" class="form-control" value="{{ $embedding->url }}" disabled>
										<small class="text-muted">{{__('YouTube URL cannot be changed. Delete and create new if needed.')}}</small>
									</div>
								</div>
							</div>
						@endif

						<div class="col-md-12 mt-3">
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


