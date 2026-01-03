@extends('admin.user.userProfile')
@section('extra_content')
	<div class="row justify-content-md-center">
		<div class="col-lg-12">
			<div class="d-grid gap-3 gap-lg-5">
				<form action="{{route('user-transactionSearch',$user->id)}}" method="GET">
					<div class="card mb-4 card-primary shadow-sm">
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Transaction ID')" name="utr"
											   value="{{ @request()->utr }}" type="text"
											   class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Min Amount')" name="min"
											   value="{{ @request()->min }}" type="text"
											   class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Maximum Amount')" name="max"
											   value="{{ @request()->max }}" type="text"
											   class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Transaction Date')" name="created_at" id="created_at"
											   value="{{ @request()->created_at }}" type="date"
											   class="form-control form-control-sm" autocomplete="off">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<button type="submit" class="btn btn-primary btn-sm btn-block">@lang('Search')</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
				<!-- Card -->
				<div class="card">
					<!-- Header -->
					<div class="card-header card-header-content-between">
						<h4 class="card-header-title">@lang('Transaction')</h4>
					</div>
					<!-- End Header -->
					<!-- Body -->
					<div class="card-body card-body-height">
						<div class="table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th>@lang('SL')</th>
									<th>@lang('Transaction ID')</th>
									<th>@lang('Amount')</th>
									<th>@lang('Type')</th>
									<th>@lang('Remark')</th>
									<th>@lang('Status')</th>
									<th>@lang('Transaction At')</th>
								</tr>
								</thead>
								<tbody>
								@if(count($transactions)>0)
									@foreach($transactions as $key => $value)
										<tr>
											<td data-label="@lang('SL')">
												{{++$key}}
											</td>
											<td data-label="@lang('Transaction ID')">{{ __($value->transactional->utr) }}</td>
											<td data-label="@lang('Amount')">{{ (getAmount(optional($value->transactional)->amount)).' '.config('basic.base_currency') }}</td>
											<td data-label="@lang('Type')">
												{{ __(str_replace('App\Models\\', '', $value->transactional_type)) }}
											</td>
											<td data-label="@lang('Remark')">{{ $value->remark }}</td>
											<td data-label="@lang('Status')">
												@if(optional($value->transactional)->status)
													<span class="badge badge-light"><i
															class="fa fa-circle text-success font-12"></i> @lang('Success')</span>
												@else
													<span class="badge badge-light"><i
															class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
												@endif
											</td>
											<td data-label="@lang('Transaction At')"> {{dateTime($value->created_at, 'd M Y H:i')}} </td>
										</tr>
									@endforeach
								@else
									<tr>
										<th colspan="100%" class="text-center"><img
												src="{{asset('assets/upload/no-data.png')}}"
												alt="no-data"
												class="no-data-img"><br>@lang('No data found')
										</th>
									</tr>
								@endif
								</tbody>
							</table>
						</div>
					</div>
					<div class="card-footer">{{ $transactions->links() }}</div>
				</div>
				<!-- End Card -->
			</div>
		</div>
	</div>
@endsection
