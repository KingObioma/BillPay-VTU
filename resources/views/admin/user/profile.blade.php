@extends('admin.user.userProfile')
@section('extra_content')
	<div class="row justify-content-md-center">
		<div class="col-lg-4">
			<div class="card mb-3 mb-lg-5">

				<!-- Header -->
				<div class="card-header">
					<h4 class="card-header-title">@lang('Profile')</h4>
				</div>

				<div class="card-body">
					<ul class="list-unstyled list-py-2 text-dark mb-0">
						<li class="pb-3"><b class="card-subtitle">@lang('About')</b></li>
						<li><span><i class="fas fa-user"></i> {{$user->name}}</span></li>
						<li><span><i class="fas fa-wallet"></i> <span
									class="text-dark font-weight-bold">@lang('Balance -')</span> {{getAmount($user->balance,config('basic.fraction_number'))}} {{config('basic.base_currency')}}</span>
						</li>
						<li class="py-3"><b class="card-subtitle">@lang('Contacts')</b></li>
						<li><span><i
									class="fas fa-phone"></i>{{optional($user->profile)->phone_code}} {{optional($user->profile)->phone}}</span>
						</li>
						<li><span><i class="fas fa-envelope"></i> {{$user->email}}</span>
						</li>
						<li><span><i class="fas fa-map"></i> {{optional($user->profile)->address}}  </span>
						</li>
					</ul>
				</div>
			</div>
			<!-- End Body -->
			<div class="card card-body mb-3 mb-lg-5">
				<!-- Header -->
				<div class="card-body">
					<div class="row justify-content-center p-5">
						<div class="mb-3 text-center">
							<img
								src="https://script.bugfinder.net/starryhire/assets/global/tfa.svg"
								alt="verfication_img" class="img-fluid w-50 mx-auto">
						</div>
						<div class="text-center">
							<h4>@lang('2-step verification')</h4>
							<p>@lang('2-step verification protect account from various kind of illegal access.')</p>
							<form
								action="{{route('user-twoFaStatus',$user->id)}}"
								method="GET">
								<button type="submit" class="btn btn-primary btn-sm btn-block">
									{{$user->two_fa?'Disable Now':'Enable Now'}}
								</button>
							</form>
						</div>
					</div>
				</div>
				<!-- End Body -->
			</div>
		</div>
		<div class="col-lg-8">
			<div class="d-grid gap-3 gap-lg-5">
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
				</div>
				<!-- End Card -->
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
				</div>
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
				</div>
			</div>
		</div>
	</div>
@endsection
