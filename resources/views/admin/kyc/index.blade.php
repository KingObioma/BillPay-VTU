@extends('admin.layouts.master')
@section('page_title', __('KYC List'))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('KYC List')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('KYC List')</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow-sm">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
								</div>
								<div class="card-body">
									<form action="{{ route('kyc.list') }}" method="get">
										@include('admin.kyc.searchForm')
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('KYC List')</h6>
								</div>
								@if(count($kycLists) > 0)
									<div class="card-body">
										<div class="table-responsive">
											<table
												class="table table-striped table-hover align-items-center table-borderless">
												<thead class="thead-light">
												<tr>
													<th>@lang('SL')</th>
													<th>@lang('User')</th>
													<th>@lang('Status')</th>
													<th>@lang('Submitted At')</th>
													<th>@lang('Action')</th>
												</tr>
												</thead>
												<tbody>
												@forelse($kycLists as $key => $value)
													<tr>
														<td data-label="@lang('SL')">
															{{loopIndex($kycLists) + $key}}
														</td>
														<td data-label="@lang('User')">
															<a href="{{ route('user-profile', $value->user_id)}}"
															   class="text-decoration-none">
																<div class="d-lg-flex d-block align-items-center ">
																	<div class="mr-3"><img
																			src="{{ getFile(optional(optional($value->user)->profile)->driver,optional(optional($value->user)->profile)->profile_picture) }}"
																			alt="user"
																			class="rounded-circle" width="35"
																			data-toggle="tooltip" title=""
																			data-original-title="{{optional($value->user)->name}}">
																	</div>
																	<div
																		class="d-inline-flex d-lg-block align-items-center">
																		<p class="text-dark mb-0 font-16 font-weight-medium">{{Str::limit(optional($value->user)->name?? __('N/A'),20)}}</p>
																		<span
																			class="text-muted font-14 ml-1">{{ '@'.optional($value->user)->username}}</span>
																	</div>
																</div>
															</a>
														</td>
														<td data-label="@lang('Status')">
															@if($value->status == '1')
																<span
																	class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Approved')</span>
															@elseif($value->status == '0')
																<span
																	class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
															@elseif($value->status == '2')
																<span
																	class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Rejected')</span>
															@endif
														</td>
														<td data-label="@lang('Submitted At')">{{dateTime($value->created_at,'d/m/Y H:i')}}</td>
														<td data-label="@lang('Action')">
															<a href="{{route('kyc.view',$value->id)}}"
															   data-toggle="tooltip"
															   data-original-title="@lang('View')"
															   class="btn btn-round btn-outline-primary btn-sm"><i
																	class="fas fa-eye"></i></a>
														</td>
													</tr>
												@empty
													<tr>
														<th colspan="100%"
															class="text-center">@lang('No data found')</th>
													</tr>
												@endforelse
												</tbody>
											</table>
										</div>
										<div class="card-footer">
											{{ $kycLists->links() }}
										</div>
									</div>
								@else
									@include('empty')
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>
	</div>
@endsection

@push('extra_scripts')

@endpush
