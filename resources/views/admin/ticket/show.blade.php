@extends('admin.layouts.master')
@section('page_title', __("Ticket: # $ticket->ticket"))
@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/file.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/dashboard/css/chat.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang("Ticket: # $ticket->ticket")</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang("Ticket: # $ticket->ticket")</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row">
						<div class="col-sm-12">
							<div class="card mb-4 card-primary shadow">
								<div class="card-body">
									<h5 class="card-title  ">
										<div class="row justify-content-between align-items-center">
											<div class="col-sm-10">
												@if($ticket->status == 0)
													<span class="badge badge-pill badge-primary">@lang('Open')</span>
												@elseif($ticket->status == 1)
													<span
														class="badge badge-pill badge-success">@lang('Answered')</span>
												@elseif($ticket->status == 2)
													<span
														class="badge badge-pill badge-dark">@lang('Customer Replied')</span>
												@elseif($ticket->status == 3)
													<span class="badge badge-pill badge-danger">@lang('Closed')</span>
												@endif
												[{{trans('Ticket#'). __($ticket->ticket) }}] {{ __($ticket->subject) }}
											</div>
											<div class="col-sm-2 text-sm-right mt-sm-0 mt-3">
												<button type="button" class="btn btn-outline-dark btn-sm"
														data-toggle="modal" data-target="#closeTicketModal">
													<i class="fas fa-trash-alt"></i> {{ trans('Close') }}
												</button>
											</div>
										</div>
									</h5>


									<div class="card-body border mx-2">
										<form class="form-row" action="{{ route('admin.ticket.reply', $ticket->id)}}"
											  method="post" enctype="multipart/form-data">
											@csrf
											@method('PUT')
											<div class="col-sm-10 col-12">
												<div class="form-group mt-0 mb-0">
													<textarea name="message" class="form-control ticket-box"
															  id="textarea1" placeholder="@lang('Type Here')"
															  rows="3">{{ old('message') }}</textarea>
												</div>
												@error('message')
												<span class="text-danger">@lang($message)</span>
												@enderror
											</div>
											<div class="col-sm-2 col-12">
												<div
													class="justify-content-sm-end justify-content-start mt-sm-0 mt-2 align-items-center d-flex h-100">
													<div class="upload-btn">
														<div class="btn btn-primary new-file-upload mr-3"
															 title="{{ trans('Image Upload') }}">
															<a href="javascript:void(0)">
																<i class="fa fa-image"></i>
															</a>
															<input type="file" name="attachments[]" id="upload"
																   class="upload-box" multiple
																   placeholder="@lang('Upload File')">
														</div>
														<p class="text-danger select-files-count"></p>
													</div>
													<button type="submit" title="{{ trans('Reply') }}"
															name="replayTicket" value="1"
															class="btn btn-sm btn-success button-round float-right text-white">
														<i class="fas fa-paper-plane"></i>
													</button>
												</div>
												@error('attachments')
												<span class="text-danger">@lang($message)</span>
												@enderror
											</div>
										</form>
									</div>

									<div class="card-footer pt-0 border-top-0">
										@if(count($ticket->messages) > 0)
											<div class="row justify-content-center">
												<div class="col-md-12 chat">
													<div class="col-inside-lg decor-default">
														<div class="chat-body">
															@foreach($ticket->messages as $item)
																@if($item->admin_id == null)
																	<div
																		class="answer {{ Auth::getDefaultDriver() == 'admin' ? 'right' : 'left' }}">
																		<div class="avatar">
																			<img
																				src="{{ getFile(optional($ticket->user->profile)->driver,optional($ticket->user->profile)->profile_picture) }}"
																				alt="{{ __(optional($ticket->user)->name) }}">
																		</div>
																		<div
																			class="name">{{ __(optional($ticket->user)->username) }}</div>
																		<div class="delete-message-right">
																			<div
																				class="text">{{ __($item->message) }}</div>
																			<button data-id="{{ $item->id }}"
																					type="button"
																					data-toggle="modal"
																					data-target="#DelMessage"
																					class="delete-message btn btn-sm btn-default align-items-center mt-1">
																				<i class="fas fa-trash-alt"></i>
																			</button>
																		</div>
																		@if(0 < count($item->attachments))
																			<div class="my-3 d-flex ">
																				@foreach($item->attachments as $k => $image)
																					<a href="{{ route('admin.ticket.download',encrypt($image->id)) }}"
																					   class="mr-3 ">
																						<i class="fa fa-file"></i> @lang('File(s)') {{ ++$k}}
																					</a>
																				@endforeach
																			</div>
																		@endif
																		<div
																			class="time mt-1">{{ __($item->created_at->format('d M, Y h:i A')) }}</div>
																	</div>
																@else
																	<div
																		class="answer {{ Auth::getDefaultDriver() == 'admin' ? 'left' : 'right' }}">
																		<div class="avatar">
																			<img
																				src="{{ getFile(optional($item->admin->profile)->driver,optional($item->admin->profile)->profile_picture) }}"
																				alt="{{ __(optional($item->admin)->name) }}">
																		</div>
																		<div
																			class="name">{{ __(optional($item->admin)->name) }}</div>
																		<div class="delete-message-left">
																			<div
																				class="text">{{ __($item->message) }}</div>
																			<button data-id="{{$item->id}}"
																					type="button"
																					data-toggle="modal"
																					data-target="#DelMessage"
																					class="delete-message btn btn-sm btn-default align-items-center mt-1">
																				<i class="fas fa-trash-alt"></i>
																			</button>
																		</div>
																		@if(0 < count($item->attachments))
																			<div
																				class="my-3 d-flex justify-content-end">
																				@foreach($item->attachments as $k => $image)
																					<a href="{{route('admin.ticket.download',encrypt($image->id))}}"
																					   class="mr-3">
																						<i class="fa fa-file"></i> @lang('File(s)') {{ __(++$k) }}
																					</a>
																				@endforeach
																			</div>
																		@endif
																		<div
																			class="time mt-1">{{ __($item->created_at->format('d M, Y h:i A')) }}</div>
																	</div>
																@endif
															@endforeach
														</div>
													</div>
												</div>
											</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>
	</div>
	<div class="modal fade" id="DelMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header text-danger">
					<h5 class="modal-title"><i class="fas fa-info-circle"></i> @lang('Confirmation !')</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						×
					</button>
				</div>
				<div class="modal-body">
					<p class="text-dark">@lang('Are you confirm to delete the message?')</p>
				</div>
				<div class="modal-footer">
					<form method="post" action="{{ route('admin.ticket.delete')}}">
						@csrf
						<input type="hidden" name="message_id" class="message_id">
						<button type="button" class="btn btn-outline-primary"
								data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary"> @lang('Yes') </button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="closeTicketModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="{{ route('admin.ticket.reply', $ticket->id) }}">
					@csrf
					@method('PUT')
					<div class="modal-header text-danger">
						<h5 class="modal-title"><i class="fas fa-info-circle"></i> @lang('Confirmation !')</h5>
						<button type="button" class="close close-button" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<p class="text-dark">@lang('Are you want to close this ticket?')</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-primary"
								data-dismiss="modal">@lang('Close')</button>
						<button type="submit" class="btn btn-primary" name="replayTicket"
								value="2">@lang("Confirm")</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/jquery.nicescroll.min.js') }}"></script>
@endpush
@section('scripts')
	<script>
		'use strict';
		$(document).ready(function () {
			$('.delete-message').on('click', function (e) {
				$('.message_id').val($(this).data('id'));
			})

			$(document).on('change', '#upload', function () {
				var fileCount = $(this)[0].files.length;
				$('.select-files-count').text(fileCount + ' file(s) selected')
			})


			$(".chat").niceScroll();
		});
	</script>
@endsection


