@extends('admin.layouts.master')
@section('page_title',__('Transactions'))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Transactions')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Transactions')</div>
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
									<form action="{{ route('admin.transaction.search') }}" method="get">
										@include('admin.transaction.searchForm')
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
									<h6 class="m-0 font-weight-bold text-primary">@lang('Transactions')</h6>
								</div>
								@if(count($transactions) > 0)
									<div class="card-body">
										<div class="table-responsive">
											<table
												class="table table-striped table-hover align-items-center table-borderless">
												<thead class="thead-light">
												<tr>
													<th>@lang('SL')</th>
													<th>@lang('Transaction ID')</th>
													<th>@lang('Amount')</th>
													<th>@lang('User')</th>
													<th>@lang('Type')</th>
													<th>@lang('Remark')</th>
													<th>@lang('Status')</th>
													<th>@lang('Transaction At')</th>
												</tr>
												</thead>
												<tbody>
												@foreach($transactions as $key => $value)
													<tr>
														<td data-label="@lang('SL')">
															{{loopIndex($transactions) + $key}}
														</td>
														<td data-label="@lang('Transaction ID')">{{ __($value->transactional->utr) }}</td>
														<td data-label="@lang('Amount')">{{ (getAmount(optional($value->transactional)->amount)).' '.config('basic.base_currency') }}</td>
														<td data-label="@lang('User')">
															<a href="{{route('user-profile',$value->transactional->user->id??'1')}}"
															   class="text-decoration-none">
																<div class="d-lg-flex d-block align-items-center ">
																	<div class="mr-3"><img
																			src="{{optional(optional($value->transactional)->user)->profilePicture()}}"
																			alt="user" class="rounded-circle" width="35"
																			data-toggle="tooltip" title=""
																			data-original-title="{{ __(optional(optional($value->transactional)->user)->name ?? __('N/A')) }}">
																	</div>
																	<div
																		class="d-inline-flex d-lg-block align-items-center">
																		<p class="text-dark mb-0 font-16 font-weight-medium">
																			{{ __(optional(optional($value->transactional)->user)->name ?? __('N/A')) }}</p>
																		<span
																			class="text-muted font-14 ml-1">{{ __(optional(optional($value->transactional)->user)->username ?? __('N/A')) }}</span>
																	</div>
																</div>
															</a>
														</td>
														<td data-label="@lang('Type')">
															{{ __(str_replace('App\Models\\', '', $value->transactional_type)) }}
														</td>
														<td data-label="@lang('Remark')">{{ $value->remark }}</td>
														<td data-label="@lang('Status')">
															@if(optional($value->transactional)->status)
																<span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Success')</span>
															@else
																<span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>

															@endif
														</td>
														<td data-label="@lang('Transaction At')"> {{dateTime($value->created_at, 'd M Y H:i')}} </td>
													</tr>
												@endforeach
												</tbody>
											</table>
										</div>
										<div class="card-footer">
											{{ $transactions->links() }}
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
