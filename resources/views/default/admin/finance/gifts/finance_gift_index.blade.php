@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Gift Cards') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Gift Cards') }}</a></li>
			</ol>
		</div>
		<div class="page-rightheader">
			<a href="{{ route('admin.finance.gifts.export') }}" class="btn btn-primary mt-1">{{ __('Export Gift Cards') }}</a>
			<a href="{{ route('admin.finance.gifts.create') }}" class="btn btn-primary mt-1">{{ __('Create New Gift Card') }}</a>
		</div>
	</div>	
	<!-- END PAGE HEADER -->
@endsection

@section('content')	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ __('All Gift Cards') }}</h3>
				</div>
				<div class="card-body pt-2">
					<div class="row justify-content-center">
						<div class="col-sm-12">
							<div class="row mb-4">
								<div class="col-lg col-md-6 col-sm-12 dashboard-border-right mt-auto mb-auto text-center">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Total Gift Cards') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">{{ $total['cards']}}</h4>										
								</div>

								<div class="col-lg col-md-6 col-sm-12 dashboard-border-right mt-auto mb-auto text-center">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Total Active Cards') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">{{ $total['active']}}</h4>										
								</div>
								
								<div class="col-lg col-md-6 col-sm-12 dashboard-border-right mt-auto mb-auto text-center">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Total Redeemed Cards') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">{{ $total['redeemed']}}</h4>										
								</div>

								<div class="col-lg col-md-6 col-sm-12  mt-auto mb-auto text-center">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Total Applied Funds') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">{{ $total['funds']}} {{config('payment.default_system_currency')}}</h4>										
								</div>								
							</div>
							<hr class="mb-6">
						</div>									
					</div>
					<!-- SET DATATABLE -->
					<table id='promocodesAdminTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="10%">{{ __('Card Name') }}</th>
									<th width="10%">{{ __('Code') }}</th>
									<th width="10%">{{ __('Status') }}</th>
									<th width="10%">{{ __('Amount') }}</th>
									<th width="7%">{{ __('Quantity Left') }}</th>																																												
									<th width="10%">{{ __('Valid Until') }}</th>
									<th width="10%">{{ __('Created On') }}</th>
									<th width="7%">{{ __('Actions') }}</th>
								</tr>
							</thead>
					</table> <!-- END SET DATATABLE -->

				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ __('Gift Cards Usage') }}</h3>
				</div>
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='usageTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="10%">{{ __('User') }}</th>
									<th width="10%">{{ __('Gift Card') }}</th>									
									<th width="10%">{{ __('Amount') }}</th>
									<th width="10%">{{ __('Status') }}</th>
									<th width="10%">{{ __('Redeemed On') }}</th>
									<th width="1%">{{ __('Actions') }}</th>
								</tr>
							</thead>
					</table> <!-- END SET DATATABLE -->

				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ __('Transfers by Users') }}</h3>
				</div>
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='transferTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="10%">{{ __('Transfer ID') }}</th>
									<th width="10%">{{ __('Sent By') }}</th>
									<th width="10%">{{ __('Sent To') }}</th>									
									<th width="10%">{{ __('Amount') }}</th>
									<th width="10%">{{ __('Status') }}</th>
									<th width="10%">{{ __('Transfer Date') }}</th>
									<th width="3%">{{ __('Actions') }}</th>
								</tr>
							</thead>
					</table> <!-- END SET DATATABLE -->

				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";
			
			// INITILIZE DATATABLE
			var table = $('#promocodesAdminTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				"order": [[ 7, "asc" ]],
				language: {
					"emptyTable": "<div><br>{{ __('No gift cards created yet') }}</div>",
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
				ajax: "{{ route('admin.finance.gifts') }}",
				columns: [{
						data: 'name',
						name: 'name',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-code',
						name: 'custom-code',
						orderable: false,
						searchable: true
					},
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-value',
						name: 'custom-value',
						orderable: true,
						searchable: true
					},
					{
						data: 'usages_left',
						name: 'usages_left',
						orderable: true,
						searchable: true
					},
					{
						data: 'valid-until',
						name: 'valid-until',
						orderable: true,
						searchable: true
					},		
					{
						data: 'created-on',
						name: 'created-on',
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


			var table2 = $('#usageTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				"order": [[ 5, "desc" ]],
				language: {
					"emptyTable": "<div><br>{{ __('No gift cards redeemed yet') }}</div>",
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
				ajax: "{{ route('admin.finance.gifts.redeemed') }}",
				columns: [{
						data: 'user',
						name: 'user',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-code',
						name: 'custom-code',
						orderable: false,
						searchable: true
					},					
					{
						data: 'custom-value',
						name: 'custom-value',
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
						data: 'created-on',
						name: 'created-on',
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


			var table3 = $('#transferTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				"order": [[ 5, "desc" ]],
				language: {
					"emptyTable": "<div><br>{{ __('No user transfers conducted yet') }}</div>",
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
				ajax: "{{ route('admin.finance.gifts.transfer') }}",
				columns: [{
						data: 'transfer_id',
						name: 'transfer_id',
						orderable: true,
						searchable: true
					},
					{
						data: 'sender',
						name: 'sender',
						orderable: true,
						searchable: true
					},
					{
						data: 'receiver',
						name: 'receiver',
						orderable: false,
						searchable: true
					},					
					{
						data: 'amount',
						name: 'amount',
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
						data: 'created-on',
						name: 'created-on',
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

			
			$(document).on('click', '.deleteButton', function(e) {

				e.preventDefault();

				Swal.fire({
					title: '{{ __('Confirm Gift Card Deletion') }}',
					text: '{{ __('It will permanently delete this gift card') }}',
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
							method: 'post',
							url: 'gift-card/delete',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data == 'success') {
									toastr.success('{{ __('Gift card has been successfully deleted') }}');
									$("#promocodesAdminTable").DataTable().ajax.reload();								
								} else {
									toastr.error('{{ __('There was an error while deleting this gift card') }}');
								}      
							},
							error: function(data) {
								Swal.fire('Oops...','Something went wrong!', 'error')
							}
						})
					} 
				})
			});


			$(document).on('click', '.deleteUsageButton', function(e) {

				e.preventDefault();

				Swal.fire({
					title: '{{ __('Confirm Gift Card Usage Deletion') }}',
					text: '{{ __('It will permanently delete this gift card usage record') }}',
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
							method: 'post',
							url: 'gift-card/redeemed/delete',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data == 'success') {
									toastr.success('{{ __('Gift card usage record has been successfully deleted') }}');
									$("#usageTable").DataTable().ajax.reload();								
								} else {
									toastr.error('{{ __('There was an error while deleting this gift card usage record') }}');
								}      
							},
							error: function(data) {
								Swal.fire('Oops...','Something went wrong!', 'error')
							}
						})
					} 
				})
			});

		});
	</script>
@endsection