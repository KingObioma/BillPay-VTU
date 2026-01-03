@extends('admin.user.userProfile')
@section('extra_content')
	<div class="row justify-content-md-center">
		<div class="col-lg-12">
			<div class="d-grid gap-3 gap-lg-5">
				<form action="{{route('user-billPay',$user->id)}}" method="GET">
					<div class="card mb-4 card-primary shadow-sm">
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">@lang('Search')</h6>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Category')" name="category" value="{{ @request()->category }}" type="text"
											   class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Type')" name="searchType" value="{{ @request()->searchType }}" type="text"
											   class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group search-currency-dropdown">
										<select name="status" class="form-control form-control-sm">
											<option value="">@lang('Status')</option>
											<option
												value="generate" {{ @request()->status == 'generate' ? 'selected' : ''  }}> @lang('Generate') </option>
											<option
												value="pending" {{ @request()->status == 'pending' ? 'selected' : ''  }}> @lang('Pending') </option>
											<option
												value="processing" {{ @request()->status == 'processing' ? 'selected' : ''  }}> @lang('Processing') </option>
											<option
												value="payment_completed" {{ @request()->status == 'payment_completed' ? 'selected' : ''  }}> @lang('Payment Completed') </option>
											<option
												value="bill_completed" {{ @request()->status == 'bill_completed' ? 'selected' : ''  }}> @lang('Bill Completed') </option>
											<option
												value="bill_return" {{ @request()->status == 'bill_return' ? 'selected' : ''  }}> @lang('Bill Return') </option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input placeholder="@lang('Transaction Date')" name="created_at" id="created_at"
											   value="{{ $search['created_at'] ?? '' }}" type="date" class="form-control form-control-sm"
											   autocomplete="off">
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
						<h4 class="card-header-title">@lang('Bill Pay')</h4>
					</div>
					<!-- End Header -->
					<!-- Body -->
					<div class="card-body card-body-height">
						<div class="table-responsive">
							<table class="table">
								<thead>
								<tr>
									<th>@lang('SL')</th>
									<th>@lang('Method')</th>
									<th>@lang('Category')</th>
									<th>@lang('Type')</th>
									<th>@lang('Amount')</th>
									<th>@lang('Charge')</th>
									<th>@lang('Status')</th>
									<th>@lang('Created time')</th>
								</tr>
								</thead>
								<tbody>
								@if(count($bills) > 0)
									@foreach($bills as $key => $value)
										<tr>
											<td data-label="@lang('SL')">{{++$key}}</td>
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
					<div class="card-footer">{{ $bills->links() }}</div>
				</div>
				<!-- End Card -->
			</div>
		</div>
	</div>
@endsection
