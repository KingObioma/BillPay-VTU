@extends('admin.layouts.master')
@section('page_title',__('Support Tickets'))

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Support Tickets')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Support Tickets')</div>
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
									<form action="{{ route('admin.ticket.search') }}" method="get">
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="ticket" value="{{@request()->ticket}}"
														   class="form-control form-control-sm"
														   placeholder="@lang('Ticket No')">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="email" value="{{@request()->email}}"
														   class="form-control form-control-sm"
														   placeholder="@lang('Email')">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group search-currency-dropdown">
													<select name="status" class="form-control form-control-sm">
														<option
															value="-1" {{ @request()->status == '-1' ? 'selected' : '' }}>@lang('All Ticket')</option>
														<option
															value="0" {{ @request()->status == '0' ? 'selected' : '' }}>@lang('Open Ticket')</option>
														<option
															value="1" {{ @request()->status == '1' ? 'selected' : '' }}>@lang('Answered Ticket')</option>
														<option
															value="2" {{ @request()->status == '2' ? 'selected' : '' }}>@lang('Replied Ticket')</option>
														<option
															value="3" {{ @request()->status == '3' ? 'selected' : '' }}>@lang('Closed Ticket')</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="date" class="form-control form-control-sm"
														   name="date_time"
														   id="datepicker"/>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<button type="submit" class="btn btn-sm btn-primary btn-block"><i
															class="fas fa-search"></i> @lang('Search')</button>
												</div>
											</div>
										</div>
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
									<h6 class="m-0 font-weight-bold text-primary">@lang('Tickets')</h6>
								</div>
								@if(count($tickets) > 0)
									<div class="card-body">
										<div class="table-responsive">
											<table class="table table-striped table-hover align-items-center ">
												<thead>
												<tr>
													<th>@lang('SL')</th>
													<th>@lang('Subject')</th>
													<th>@lang('User')</th>
													<th>@lang('Status')</th>
													<th>@lang('Last Reply')</th>
													<th>@lang('Action')</th>
												</tr>
												</thead>
												<tbody>
												@forelse($tickets as $key => $ticket)
													<tr>
														<td data-label="SL">
															{{loopIndex($tickets) + $key}}
														</td>
														<td data-label="Subject">
															<a href="{{ route('admin.ticket.view', $ticket->id) }}"
															   class="font-weight-bold" target="_blank">
																[{{ trans('Ticket#') . __($ticket->ticket) }}
																] {{ _($ticket->subject) }}
															</a>
														</td>

														<td data-label="Submitted By">
															@if($ticket->user_id)
																<a href="{{ route('user-profile', $ticket->user_id)}}"
																   class="text-decoration-none"
																   target="_blank">
																	<div class="d-lg-flex d-block align-items-center ">
																		<div class="mr-3"><img
																				src="{{ optional($ticket->user)->profilePicture()??asset('assets/upload/boy.png') }}"
																				alt="user"
																				class="rounded-circle" width="35"
																				data-toggle="tooltip" title=""
																				data-original-title="{{optional($ticket->user)->name}}">
																		</div>
																		<div
																			class="d-inline-flex d-lg-block align-items-center">
																			<p class="text-dark mb-0 font-16 font-weight-medium">
																				{{Str::limit(optional($ticket->user)->name?? __('N/A'),20)}}</p>
																			<span
																				class="text-muted font-14 ml-1">{{ '@'.optional($ticket->user)->username}}</span>
																		</div>
																	</div>
																</a>
															@else
																<p class="font-weight-bold"> {{ __($ticket->name) }}</p>
															@endif

														</td>

														<td data-label="@lang('Status')">
															@if($ticket->status == 0)
																<span
																	class="badge badge-light"><i class="fa fa-circle text-primary font-12"></i> @lang('Open')</span>
															@elseif($ticket->status == 1)
																<span
																	class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Answered')</span>
															@elseif($ticket->status == 2)
																<span
																	class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Customer Replied')</span>
															@elseif($ticket->status == 3)
																<span
																	class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Closed')</span>
															@endif
														</td>
														<td data-label="@lang('Last Reply')">
															{{ $ticket->last_reply->diffForHumans() }}
														</td>
														<td data-label="Action">
															<a href="{{ route('admin.ticket.view', $ticket->id) }}"
															   class="btn btn-round btn-sm btn-outline-primary" title="@lang('view')">
																<i class="fa fa-eye"></i>
															</a>
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
											{{ $tickets->appends($_GET)->links() }}
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
