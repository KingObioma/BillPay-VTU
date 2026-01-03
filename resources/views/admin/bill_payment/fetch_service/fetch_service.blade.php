@extends('admin.layouts.master')
@section('page_title',__('Service List'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Service List')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Service List')</div>
				</div>
			</div>

			<div class="row mb-3" id="bulkCheck">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Service List')</h6>
									<button class="btn btn-primary bulk-yes">@lang('Bulk Add')</button>
								</div>

								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-striped table-hover align-items-center table-flush">
											<thead class="thead-light">
											@include('admin.bill_payment.fetch_service.dynamic.'.$billMethod->code)
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
@push('extra_scripts')
	<script>
		'use strict'
		var api_service = '{{$api_service}}'
		var methodId = '{{$billMethod->id}}'
		$(document).on('click', '.bulk-yes', function (e) {
			Notiflix.Loading.Standard('Please wait, Its take few time');
			e.preventDefault();
			var allVals = [];
			$(".row-tic:checked").each(function () {
				allVals.push($(this).attr('data-id'));
			});

			var res = allVals;

			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('admin.bill.add.bulk.service') }}",
				data: {res: res, api_service: api_service, methodId: methodId},
				dataType: 'json',
				type: "post",
				success: function (data) {
					Notiflix.Loading.Remove();
					if (data.status == 'success') {
						window.location.href = data.route;
					}
					if (data.status == 'error') {
						Notiflix.Notify.Failure("You are not selected any value");
					}
				},
			});
		});

		$(document).on('click', '#singleAdd', function () {
			Notiflix.Loading.Standard('Please wait, Its take few time');
			var res = $(this).data('resource');
			var _this = this;

			$.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
				url: "{{ route('admin.bill.add.service') }}",
				data: {res: res, api_service: api_service, methodId: methodId},
				dataType: 'json',
				type: "post",
				success: function (data) {
					Notiflix.Loading.Remove();
					if (data.status == 'success') {
						$(_this).attr('class', 'btn btn-sm btn-success')
						$(_this).prop("disabled", true)
						$(_this).text('Added')
						Notiflix.Notify.Success("Successfully Added");
					}
					if (data.status == 'error') {
						Notiflix.Notify.Failure(data.msg);
					}
				}
			});
		});

		$(document).on('click', '#check-all', function () {
			$('input:checkbox').not(this).prop('checked', this.checked);
		});

		$(document).on('change', ".row-tic", function () {
			let length = $(".row-tic").length;
			let checkedLength = $(".row-tic:checked").length;
			if (length == checkedLength) {
				$('#check-all').prop('checked', true);
			} else {
				$('#check-all').prop('checked', false);
			}
		});
	</script>
@endpush
