@extends($theme.'layouts.user')
@section('page_title',__('Bill Pay List'))

@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-between border-0">
			<h4>@lang('Bill Pay List')</h4>
			<div class="btn-area">
				<button type="button" class="cmn-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
						aria-controls="offcanvasExample">Filter<i class="fa-regular fa-filter"></i></button>
			</div>
		</div>
		@if(count($bills) > 0)
			<div class="card-body">
				<div class="cmn-table">
					<div class="table-responsive overflow-hidden">
						<table class="table align-middle">
							<thead>
							<tr>
								<th scope="col">@lang('SL')</th>
								<th scope="col">@lang('Category')</th>
								<th scope="col">@lang('Type')</th>
								<th scope="col">@lang('Amount')</th>
								<th scope="col">@lang('Charge')</th>
								<th scope="col">@lang('Status')</th>
								<th scope="col">@lang('Date & Time')</th>
								<th scope="col">@lang('Action')</th>
							</tr>
							</thead>
							<tbody>
							@foreach($bills as $key => $value)
								<tr>
									<td data-label="@lang('SL')">{{loopIndex($bills) + $key}}</td>
									<td data-label="@lang('Category')">{{ __(str_replace('_',' ',ucfirst($value->category_name))) }}</td>
									<td data-label="@lang('Type')">{{ __($value->type) }}</td>
									<td data-label="@lang('Amount')">{{ (getAmount($value->amount,2)).' '.__($value->currency) }}</td>
									<td data-label="@lang('Charge')"><span
											class="text-danger">{{ (getAmount($value->charge,2)).' '.__($value->currency) }}</span>
									</td>
									<td data-label="@lang('Status')">
										@if($value->status == 2)
											<span
												class="badge text-bg-warning">@lang('Pending')</span>
										@elseif($value->status == 3)
											<span class="badge text-bg-success">@lang('Completed')</span>
										@elseif($value->status == 4)
											<span class="badge text-bg-danger">@lang('Return')</span>
										@elseif($value->status == 5)
											<span class="badge text-bg-info">@lang('Processing')</span>
										@endif
									</td>
									<td data-label="@lang('Date & Time')"> {{ $value->created_at}} </td>
									<td data-label="@lang('Action')">
										<a href="{{route('pay.bill.details',$value->id)}}" class="action-btn"><i
												class="fa-regular fa-eye"></i>
										</a>
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
	{{ $bills->appends($_GET)->links($theme.'partials.user.pagination') }}

	<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter')</h5>
			<button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
				<i class="fa-light fa-arrow-right"></i>
			</button>
		</div>
		<div class="offcanvas-body">
			<form action="{{ route('pay.bill.list') }}" method="get">
				<div class="row g-4">
					<div>
						<label for="Category" class="form-label">@lang('Category')</label>
						<input type="text" name="category" value="{{@request()->category}}" class="form-control"
							   id="Category">
					</div>
					<div>
						<label for="Type" class="form-label">@lang('Type')</label>
						<input type="text" name="type" value="{{@request()->type}}" class="form-control" id="Type">
					</div>
					<div id="formModal">
						<label class="form-label">@lang('Status')</label>
						<select class="modal-select" name="status">
							<option value="">@lang('All Status')</option>
							<option value="success" {{@request()->status == 'success'? 'selected':''}}>@lang('Success')</option>
							<option value="pending" {{@request()->status == 'pending'? 'selected':''}}>@lang('Pending')</option>
							<option value="return" {{@request()->status == 'return'? 'selected':''}}>@lang('Return')</option>
						</select>
					</div>
					<div>
						<label for="Date" class="form-label">@lang('Date')</label>
						<input type="date" name="created_at" value="{{@request()->created_at}}" class="form-control"
							   id="Date">
					</div>
					<div class="btn-area">
						<button type="submit" class="cmn-btn">@lang('Filter')</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
