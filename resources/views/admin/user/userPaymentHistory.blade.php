@extends('admin.user.userProfile')
@section('extra_content')
	<div class="row justify-content-md-center">
		<div class="col-lg-12">
			<div class="d-grid gap-3 gap-lg-5">
				<form action="{{route('user-paymentLogSearch',$user->id)}}" method="GET">
					<div class="card mb-4 card-primary shadow-sm">
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<input type="text" name="name" value="{{@request()->name}}"
											   class="form-control"
											   placeholder="@lang('Type Here')">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<select name="status" class="form-control">
											<option value="-1"
													@if(@request()->status == '-1') selected @endif>@lang('All Payment')</option>
											<option value="1"
													@if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
											<option value="2"
													@if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
											<option value="3"
													@if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
										</select>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<input type="date" class="form-control" name="date_time"
											   id="datepicker"/>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<button type="submit"
												class="btn btn-primary btn-sm btn-block"> @lang('Search') </button>
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
						<h4 class="card-header-title">@lang('Payment Log')</h4>
					</div>
					<!-- End Header -->
					<!-- Body -->
					<div class="card-body card-body-height">
						<div class="table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th scope="col">@lang('Date')</th>
									<th scope="col">@lang('Trx Number')</th>
									<th scope="col">@lang('Method')</th>
									<th scope="col">@lang('Amount')</th>
									<th scope="col">@lang('Charge')</th>
									<th scope="col">@lang('Status')</th>
									<th scope="col">@lang('Payable')</th>
								</tr>
								</thead>
								<tbody>
								@if(count($funds) > 0)
									@foreach($funds as $key => $fund)
										<tr>
											<td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
											<td data-label="@lang('Trx Number')"
												class="font-weight-bold">{{ $fund->utr }}</td>
											<td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
											<td data-label="@lang('Amount')"
												class="font-weight-bold">{{ getAmount($fund->amount,config('basic.fraction_number')) }} {{ config('basic.base_currency') }}</td>
											<td data-label="@lang('Charge')"
												class="text-success">{{ getAmount($fund->charge,config('basic.fraction_number'))}} {{ config('basic.base_currency') }}</td>
											<td data-label="@lang('Status')">
												@if($fund->status == 2)
													<span class="badge badge-light"><i
															class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
												@elseif($fund->status == 1)
													<span class="badge badge-light"><i
															class="fa fa-circle text-success font-12"></i> @lang('Approved')</span>
												@elseif($fund->status == 3)
													<span class="badge badge-light"><i
															class="fa fa-circle text-danger font-12"></i> @lang('Rejected')</span>
												@endif
											</td>
											<td data-label="@lang('Payable')"
												class="font-weight-bold">{{ getAmount($fund->payable_amount,config('basic.fraction_number')) }} {{$fund->payment_method_currency}}</td>
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
					<div class="card-footer">{{ $funds->links() }}</div>
				</div>
				<!-- End Card -->
			</div>
		</div>
	</div>
@endsection
