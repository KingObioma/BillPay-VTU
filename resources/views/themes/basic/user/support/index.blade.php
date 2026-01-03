@extends($theme.'layouts.user')
@section('page_title',__('Tickets Log'))

@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-between border-0">
			<h4>@lang('Tickets Log')</h4>
			<div class="btn-area">
				<a href="{{ route('user.ticket.create') }}" class="cmn-btn3 mb-1"><i
						class="fa-regular fa-plus-circle me-1"></i> @lang('New Ticket')</a>
			</div>
		</div>
		@if(count($tickets) > 0)
			<div class="card-body">
				<div class="cmn-table">
					<div class="table-responsive overflow-hidden">
						<table class="table align-middle">
							<thead>
							<tr>
								<th scope="col">@lang('Subject')</th>
								<th scope="col">@lang('Status')</th>
								<th scope="col">@lang('Last Reply')</th>
								<th scope="col">@lang('Action')</th>
							</tr>
							</thead>
							<tbody>
							@foreach($tickets as $key => $ticket)
								<tr>
									<td data-label="@lang('Subject')"><span>[{{ trans('Ticket# ').__($ticket->ticket) }}
													] {{ __($ticket->subject) }}</span></td>
									<td data-label="@lang('Status')">
										@if($ticket->status == 0)
											<span
												class="badge text-bg-success">@lang('Open')</span>
										@elseif($ticket->status == 1)
											<span
												class="badge text-bg-primary">@lang('Answered')</span>
										@elseif($ticket->status == 2)
											<span
												class="badge text-bg-warning">@lang('Replied')</span>
										@elseif($ticket->status == 3)
											<span
												class="badge text-bg-danger">@lang('Closed')</span>
										@endif
									</td>
									<td data-label="@lang('Last Reply')">
										<span>{{ __($ticket->last_reply->diffForHumans()) }}</span>
									</td>
									<td data-label="@lang('Action')">
										<a href="{{ route('user.ticket.view', $ticket->ticket) }}" class="action-btn"><i
												class="fa-regular fa-pen-to-square"></i></a>
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
	{{ $tickets->appends($_GET)->links($theme.'partials.user.pagination') }}
@endsection
