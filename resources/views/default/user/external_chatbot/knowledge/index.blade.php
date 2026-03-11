@extends('layouts.app')

@section('page-header')
	<div class="container">	
		<div class="row">
			<div class="col-sm-12 mt-4">
				<a class="text-muted fs-13 chatbot-return-back" href="{{route('user.extension.chatbot')}}"><i class="fa-solid fa-angle-left mr-2"></i> {{__('Return to Chatbots')}}</a>
			</div>
		</div>
	</div>
@endsection

@section('css')
<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('content')


<div class="row">
	<div class="col-sm-12">
		<div class="card mb-4 border-0">
			<div class="pl-5 pl-6 border-0">
				<h4 class="mt-5 fs-20 font-weight-bold">{{__('Chatbot Knowledge Bases')}}</h4>
				<h6 class="text-muted">{{__('Control all your external chatbot knowledge bases from central place')}}</h6>
			</div>
		</div>
	</div>

	<div class="col-sm-12 mt-5">
			<div class="card">
				<div class="card-body pt-2">
					<table id='allKnowledge' class='table' width='100%'>
						<thead>
							<tr>							
								<th width="5%">{{ __('Training Type') }}</th>
								<th width="10%">{{ __('Title') }}</th>	
								<th width="3%">{{ __('Status') }}</th> 									   										 						           	
								<th width="3%">{{ __('Trained On') }}</th> 									   										 						           	
								<th width="2%">{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
</div>
@endsection

@section('js')
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

			let table = $('#allKnowledge').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: {
					details: {type: 'column'}
				},
				"order": [[3, "asc"]],
				language: {
					"emptyTable": "<div><img id='no-results-img' src='{{ theme_url('img/files/no-result.png') }}'><br>{{ __('No knowledge bases added yet') }}</div>",
					"info": "{{ __('Showing page') }} _PAGE_ {{ __('of') }} _PAGES_",
					search: "<i class='fa fa-search search-icon'></i>",
					lengthMenu: '_MENU_ ',
					paginate : {
						first    : '<i class="fa fa-angle-double-left"></i>',
						last     : '<i class="fa fa-angle-double-right"></i>',
						previous : '<i class="fa fa-angle-left"></i>',
						next     : '<i class="fa fa-angle-right"></i>'
					}
				},
				pagingType : 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: "{{ route('user.extension.chatbot.knowledge') }}",
				columns: [					
					{
						data: 'type',
						name: 'type',
						orderable: true,
						searchable: true
					},				
					{
						data: 'title',
						name: 'title',
						orderable: true,
						searchable: true
					},	
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},	
					{
						data: 'trained',
						name: 'trained',
						orderable: true,
						searchable: true
					},														
					{
						data: 'actions',
						name: 'actions',
						orderable: false,
						searchable: false
					},
				]
			});

			// DELETE CUSTOM TEMPLATE
			$(document).on('click', '.delete', function(e) {

				e.preventDefault();

				Swal.fire({
					title: '{{ __('Confirm Knowledge Base Deletion') }}',
					text: '{{ __('It will permanently delete this knowledge base') }}',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: '{{ __('Delete') }}',
					reverseButtons: true,
				}).then((result) => {
					if (result.isConfirmed) {
						var formData = new FormData();
						formData.append("id", $(this).attr('id'));
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'POST',
							url: 'knowledge/delete',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data == 'success') {
									Swal.fire('{{__('Knowledge Base Deleted')}}', '{{ __('Knowledge Base has been successfully deleted') }}', 'success');	
									$("#allKnowledge").DataTable().ajax.reload();								
								} else {
									Swal.fire('{{ __('Knowledge Base Delete Failed') }}', '{{ __('There was an error while deleting this knowledge base') }}', 'error');
								}      
							},
							error: function(data) {
								Swal.fire({ type: 'error', title: 'Oops...', text: 'Something went wrong!' })
							}
						})
					} 
				})
			});
		});
	</script>
@endsection

