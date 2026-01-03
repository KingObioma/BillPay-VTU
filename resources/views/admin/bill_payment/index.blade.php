@extends('admin.layouts.master')
@section('page_title',__('Bill List'))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Bill List')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Bill List')</div>
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
									<form action="{{ route('bill.pay.list') }}" method="get">
										@include('admin.bill_payment.searchForm')
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
									<h6 class="m-0 font-weight-bold text-primary">@lang('Bill List')</h6>
								</div>
								@if(count($bills)>0)
									<div class="card-body">
										<div class="table-responsive">
											<table
												class="table table-striped table-hover align-items-center table-borderless">
												<thead class="thead-light">
												<tr>
													<th>@lang('SL')</th>
													<th>@lang('Method')</th>
													<th>@lang('Category')</th>
													<th>@lang('Type')</th>
													<th>@lang('Amount')</th>
													<th>@lang('Charge')</th>
													<th>@lang('User')</th>
													<th>@lang('Status')</th>
													<th>@lang('Created time')</th>
													<th>@lang('Action')</th>
												</tr>
												</thead>
												<tbody>
												@foreach($bills as $key => $value)
													<tr>
														<td data-label="@lang('SL')">{{loopIndex($bills) + $key}}</td>
														<td data-label="@lang('Type')">{{ __(optional($value->method)->methodName) }}</td>
														<td data-label="@lang('Category')">{{ __(str_replace('_',' ',ucfirst($value->category_name))) }}</td>
														<td data-label="@lang('Type')">{{ __($value->type) }}</td>
														<td data-label="@lang('Amount')">{{ (getAmount($value->amount,2)).' '.__($value->currency) }}
															<small class="badge badge-light float-right"
																   title="@lang('In Base Currency')">
																{{config('basic.currency_symbol')}}{{getAmount($value->pay_amount_in_base,2)}}
															</small>
														</td>
														<td data-label="@lang('Charge')"><span
																class="text-danger">{{ (getAmount($value->charge,2)).' '.__($value->currency) }}</span>
														</td>
														<td data-label="@lang('User')">
															<a href="{{ route('user-profile', $value->user_id)}}"
															   class="text-decoration-none">
																<div class="d-lg-flex d-block align-items-center ">
																	<div class="mr-3"><img
																			src="{{ optional($value->user)->profilePicture()??asset('assets/upload/boy.png') }}"
																			alt="user"
																			class="rounded-circle" width="35"
																			data-toggle="tooltip" title=""
																			data-original-title="{{optional($value->user)->name?? __('N/A')}}">
																	</div>
																	<div
																		class="d-inline-flex d-lg-block align-items-center">
																		<p class="text-dark mb-0 font-16 font-weight-medium">{{Str::limit(optional($value->user)->name?? __('N/A'),20)}}</p>
																		<span
																			class="text-muted font-14 ml-1">{{ '@'.optional($value->user)->username?? __('N/A')}}</span>
																	</div>
																</div>
															</a>
														</td>
														<td data-label="@lang('Status')">
															@if($value->status == 2)
																<span
																	class="badge badge-light"><i
																		class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
															@elseif($value->status == 3)
																<span
																	class="badge badge-light"><i
																		class="fa fa-circle text-success font-12"></i> @lang('Completed')</span>
															@elseif($value->status == 4)
																<span
																	class="badge badge-light"><i
																		class="fa fa-circle text-danger font-12"></i> @lang('Return')</span>
															@elseif($value->status == 5)
																<span
																	class="badge badge-light"><i
																		class="fa fa-circle text-info font-12"></i> @lang('Processing')</span>
															@endif
														</td>
														<td data-label="@lang('Created time')"> {{ $value->created_at}} </td>
														<td data-label="@lang('Action')">
															<a href="{{ route('bill.pay.view',$value->utr) }}"
															   class="btn btn-sm btn-round btn-outline-primary"><i
																	class="fas fa-eye"></i></a>
														</td>
													</tr>
												@endforeach
												</tbody>
											</table>
										</div>
										<div class="card-footer">
											{{ $bills->links() }}
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
