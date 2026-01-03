@extends('admin.layouts.master')
@section('page_title', __('All Templates'))
@push('extra_styles')
	<link rel="stylesheet" href="{{ asset('assets/dashboard/css/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('All Templates')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('All Templates')</div>
				</div>
			</div>


			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.push-notification'), 'suffix' => ''])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('List of all templates - push notify')</h6>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table
											class="table table-striped table-hover align-items-center table-flush table-sm"
											id="emailTemplate">
											<thead class="thead-light">
											<tr>
												<th>@lang('No.')</th>
												<th>@lang('Name')</th>
												<th>@lang('Status')</th>
												<th>@lang('Action')</th>
											</tr>
											</thead>
											<tbody>
											@foreach($notifyTemplates as $template)
												<tr>
													<td data-label="@lang('No.')">{{ __($loop->iteration) }}</td>
													<td data-label="@lang('Name')">{{ __($template->name) }}</td>
													<td data-label="@lang('Status')">
														@if($template->firebase_notify_status == 1)
															<span
																class="badge badge-light"><i
																	class="fa fa-circle text-success font-12"></i> @lang('Active')</span>
														@else
															<span
																class="badge badge-light"><i
																	class="fa fa-circle text-danger font-12"></i> @lang('Deactivate')</span>
														@endif
													</td>
													<td data-label="@lang('Action')">
														<a href="{{ route('push.notify.template.edit',$template->id) }}"
														   class="btn btn-sm rounded-circle btn-outline-primary"><i
																class="fas fa-edit"></i> </a>
													</td>
												</tr>
											@endforeach
											</tbody>
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
	<script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$('#emailTemplate').DataTable();
		});
	</script>
@endsection
