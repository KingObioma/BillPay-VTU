@extends('admin.layouts.master')
@section('page_title',__(ucfirst(kebab2Title($content))))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang(kebab2Title($content))</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang(kebab2Title($content))</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang(ucfirst(kebab2Title($content)))</h6>
									<span>
										<a href="{{ route('content.create',$content) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus-circle"></i> @lang('Add New')</a>
									</span>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-striped table-hover align-items-center table-flush">
											<thead class="thead-light">
											<tr>
												<th>@lang('Title')</th>
												<th>@lang('Action')</th>
											</tr>
											</thead>
											<tbody>
											@foreach($contents as $key => $value)
												<tr>
													<td data-label="@lang('Title')">
														@if(isset($value->contentDetails[0]))
															{{ optional(optional($value->contentDetails[0])->description)->title ?? optional(optional($value->contentDetails[0])->description)->name ?? optional(optional($value->contentMedia)->description)->social_icon ?? trans('N/A') }}
														@else
														@endif
													</td>
													<td data-label="@lang('Action')">
														<a href="{{ route('content.show',$value) }}" class="btn btn-sm btn-outline-primary btn-round"><i class="fas fa-edit"></i></a>
														<a href="javascript:void(0)"
														   data-route="{{ route('content.delete',$value->id) }}"
														   data-toggle="modal"
														   data-target="#delete-modal"
														   class="btn btn-outline-danger btn-sm btn-round delete"
														><i class="fas fa-trash-alt"></i></a>
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

	<div id="delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="primary-header-modalLabel">@lang('Delete Confirmation')</h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<p>@lang('Are you sure to delete this?')</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
					<form action="" method="post" class="deleteRoute">
						@csrf
						@method('delete')
						<button type="submit" class="btn btn-primary">@lang('Yes')</button>
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection
@section('scripts')
	<script>
		'use strict'
		$(document).ready(function () {
			$(document).on('click', '.delete', function () {
				let url = $(this).data('route');
				$('.deleteRoute').attr('action', url);
			})
		});
	</script>
@endsection
