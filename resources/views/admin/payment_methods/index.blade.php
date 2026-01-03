@extends('admin.layouts.master')
@section('page_title', __('Payment Methods'))

@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="{{ asset('assets/dashboard/css/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Payment Methods')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Payment Methods')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Payment Methods')</h6>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table align-items-center table-bordered"
											   id="payment-method-table">
											<thead class="thead-light">
											<tr>
												<th col="scope">@lang('Name')</th>
												<th col="scope">@lang('Status')</th>
												<th col="scope">@lang('Action')</th>
											</tr>
											</thead>
											<tbody id="sortable">
											@if(count($methods) > 0)
												@foreach($methods as $method)
													<tr data-code="{{ $method->code }}">
														<td data-label="@lang('Name')">
															<a href="javascript:void(0)"
															   class="text-decoration-none">
																<div class="d-lg-flex d-block align-items-center ">
																	<div class="mr-3"><img
																			src="{{getFile($method->driver,$method->image) }}"
																			alt="user" class="rounded-circle"
																			width="40" data-toggle="tooltip"
																			title=""
																			data-original-title="{{ __($method->name) }}">
																	</div>
																	<div
																		class="d-inline-flex d-lg-block align-items-center ms-2">
																		<p class="text-dark mb-0 font-16 font-weight-medium">
																			{{ __($method->name) }}</p>
																	</div>
																</div>
															</a>
														</td>
														<td data-label="@lang('Status')">
															{!!  $method->status == 1 ? '<span class="badge badge-light"> <i class="fa fa-circle text-success font-12"></i> '.__('Active').'</span>' : '<span class="badge badge-light"> <i class="fa fa-circle text-danger font-12"></i> '.__('Inactive').'</span>' !!}
														</td>
														<td data-label="@lang('Action')">
															<a href="{{ route('edit.payment.methods', $method->id) }}"
															   class="btn btn-sm btn-outline-primary btn-round"
															   data-toggle="tooltip"
															   data-placement="top"
															   data-original-title="@lang('Edit this Payment Methods info')">
																<i class="fa fa-edit"></i>
															</a>
														</td>
													</tr>
												@endforeach
											@else
												<tr>
													<td class="text-center text-danger" colspan="8">
														@lang('No Data Found')
													</td>
												</tr>
											@endif
											</tbody>
										</table>
									</div>
									<div class="card-footer">
										<h5>
											<span class="text-primary">@lang('N.B:')</span>
											@lang('Pull up or down the rows to sort the payment gateways order that how do you want to display the payment gateways in admin and user panel.')
										</h5>
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
	<script src="{{ asset('assets/dashboard/js/jquery-ui.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$('#payment-method-table').DataTable({
				"paging": false,
				"aaSorting": [],
				"ordering": false
			});
			$("#sortable").sortable({
				update: function (event, ui) {
					var methods = [];
					$('#sortable tr').each(function (key, val) {
						let methodCode = $(val).data('code');
						methods.push(methodCode);
					});
					$.ajax({
						'url': "{{ route('sort.payment.methods') }}",
						'method': "POST",
						'data': {sort: methods},
						success(response) {
							return true;
						}
					})
				}
			});
			$("#sortable").disableSelection();
		});
	</script>
@endsection
