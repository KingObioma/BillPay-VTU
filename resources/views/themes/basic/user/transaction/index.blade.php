@extends($theme.'layouts.user')
@section('page_title',__('Transaction List'))

@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-between border-0">
			<h4>@lang('Transaction List')</h4>
			<div class="btn-area">
				<button type="button" class="cmn-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
						aria-controls="offcanvasExample">@lang('Filter')<i class="fa-regular fa-filter"></i></button>
			</div>
		</div>
		@if(count($transactions) > 0)
			<div class="card-body">
				<div class="cmn-table">
					<div class="table-responsive overflow-hidden">
						<table class="table align-middle">
							<thead>
							<tr>
								<th scope="col">@lang('Transaction ID')</th>
								<th scope="col">@lang('Amount')</th>
								<th scope="col">@lang('Type')</th>
								<th scope="col">@lang('Remark')</th>
								<th scope="col">@lang('Status')</th>
								<th scope="col">@lang('Created Time')</th>
							</tr>
							</thead>
							<tbody>
							@foreach($transactions as $key => $value)
								<tr>
									<td data-label="@lang('Transaction ID')">
										<span>{{ __(optional($value->transactional)->utr) }}</span></td>
									<td data-label="@lang('Amount')">
										<span>{{ getAmount($value->amount,2) .' '. config('basic.base_currency') }}</span>
									</td>
									<td data-label="@lang('Type')">
										<span>{{ __(str_replace('App\Models\\', '', $value->transactional_type)) }}</span>
									</td>
									<td data-label="@lang('Remark')">
										<span>{{ $value->remark }}</span>
									</td>
									<td data-label="@lang('Status')">
										@if($value->transactional->status)
											<span
												class="badge text-bg-success">@lang('Success')</span>
										@else
											<span
												class="badge text-bg-warning">@lang('Pending')</span>
										@endif
									</td>
									<td data-label="@lang('Created Time')">
										{{ dateTime($value->created_at,'d M Y H:i')}}
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@else
			@include($theme.'user.empty')
		@endif
	</div>
	{{ $transactions->appends($_GET)->links($theme.'partials.user.pagination') }}

	<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Transaction Filter')</h5>
			<button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
				<i class="fa-light fa-arrow-right"></i>
			</button>
		</div>
		<div class="offcanvas-body">
			<form action="{{ route('user.transaction.search') }}" method="get">
				<div class="row g-4">
					<div>
						<label for="TransactionId" class="form-label">@lang('Transaction Id')</label>
						<input type="text" name="utr" value="{{@request()->utr}}" class="form-control"
							   id="TransactionId">
					</div>
					<div>
						<label for="MinAmount" class="form-label">@lang('Min Amount')</label>
						<input type="text" name="min" value="{{@request()->min}}" class="form-control" id="MinAmount">
					</div>
					<div>
						<label for="MaxAmount" class="form-label">@lang('Max Amount')</label>
						<input type="text" name="max" value="{{@request()->max}}" class="form-control" id="MaxAmount">
					</div>
					<div>
						<label for="Date" class="form-label">@lang('Transaction Date')</label>
						<input type="date" name="created_at" value="{{@request()->created_at}}" class="form-control"
							   id="Date">
					</div>
					<div id="formModal">
						<label class="form-label">@lang('Type')</label>
						<select class="modal-select" name="type">
							<option value="">@lang('All Type')</option>
							<option value="BillPay" {{@request()->type == 'BillPay'? 'selected':''}}>@lang('BillPay')</option>
						</select>
					</div>
					<div class="btn-area">
						<button type="submit" class="cmn-btn">@lang('Filter')</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
