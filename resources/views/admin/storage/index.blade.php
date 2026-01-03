@extends('admin.layouts.master')
@section('page_title', __('Storages'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Storages')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Storages')</div>
				</div>
			</div>

			<div class="section-body">
				<div class="row mt-sm-4">
					<div class="col-12 col-md-4 col-lg-3">
						@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
					</div>
					<div class="col-12 col-md-8 col-lg-9">
						<div class="container-fluid" id="container-wrapper">
							<div class="row justify-content-md-center">
								<div class="col-lg-12">
									<div class="card mb-4 card-primary shadow">
										<div
											class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											<h6 class="m-0 font-weight-bold text-primary">@lang('Storages')</h6>
										</div>
										<div class="card-body">
											<div class="table-responsive">
												<table class="table table-hover align-items-center table-flush">
													<thead class="thead-light">
													<tr>
														<th>@lang('SL.')</th>
														<th>@lang('Logo')</th>
														<th>@lang('Status')</th>
														<th>@lang('Action')</th>
													</tr>
													</thead>
													<tbody>
													@foreach($storages as $key => $item)
														<tr>
															<td data-label="@lang('SL.')">{{ ++$key }}</td>
															<td data-label="@lang('Logo')">
																<div class="d-flex no-block align-items-center">
																	<div class="mr-2"><img
																			class="img-profile-custom rounded-circle"
																			src="{{ getFile($item->driver,config('location.storage.path'). $item->logo )}}"
																			alt="{{ __($item->name) }}"
																			class="rounded-circle" width="35"
																			height="35"></div>
																	<div class="">
																		<p class="text-dark mb-0 font-weight-medium">{{ __($item->name) }}</p>
																	</div>
																</div>
															</td>
															<td data-label="@lang('Status')">
																@if($item->status == 1)
																	<span
																		class="badge badge-light"><i
																			class="fa fa-circle text-success font-12"></i> @lang('Active')</span>
																@else
																	<span
																		class="badge badge-light"><i
																			class="fa fa-circle text-danger font-12"></i> @lang('DeActive')</span>
																@endif
															</td>
															<td data-label="@lang('Action')">
																@if($item->code != 'local')
																	<a href="{{route('storage.edit',$item->id)}}"
																	   class="btn btn-sm btn-outline-primary btn-round" title="@lang('Edit')"><i
																			class="fas fa-edit"></i> </a>
																	</a>
																@endif
																@if($item->status != 1)
																	<a href="javascript:void(0)"
																	   data-target="#set-modal"
																	   data-toggle="modal"
																	   data-route="{{route('storage.setDefault',$item->id)}}"
																	   class="btn btn-sm btn-outline-info btn-round set" title="@lang('Set as default')"><i
																			class="fas fa-thumbtack"></i>
																	</a>
																@endif
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
				</div>
			</div>
		</section>
	</div>

	<div id="set-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="primary-header-modalLabel">@lang('Confirmation !')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body text-center">
					<p>@lang('Are you sure to set this?')</p>
				</div>
				<form action="" method="post" class="setRoute">
					@csrf
					@method('post')
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary">@lang('Yes')</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		'use strict'
		$(document).on('click', '.set', function () {
			let url = $(this).data('route');
			$('.setRoute').attr('action', url);
		})
	</script>
@endsection
