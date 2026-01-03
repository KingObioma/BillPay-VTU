@extends($theme.'layouts.user')
@section('page_title', __("Ticket# "). __($ticket->ticket))

@section('content')
	<div class="section dashboard">
		<div class="row">
			<div class="support-ticket-section">
				<div class="row g-4">
					<div class="col-lg-12">
						<div class="inbox-wrapper">
							<!-- top bar -->
							<div class="top-bar">
								@if($admin)
									<div>
										<img class="user img-fluid"
											 src="{{getFile(optional($admin->profile)->driver,optional($admin->profile)->profile_picture)}}"
											 alt="{{$admin->name}}"/>
										<span class="name">{{ucfirst($admin->name)}}</span>
									</div>
								@endif
								@if($ticket->status == 0)
									<span class="badge text-bg-success">@lang('Open')</span>
								@elseif($ticket->status == 1)
									<span
										class="badge text-bg-primary">@lang('Answered')</span>
								@elseif($ticket->status == 2)
									<span
										class="badge text-bg-warning">@lang('Customer Replied')</span>
								@elseif($ticket->status == 3)
									<span class="badge text-bg-danger">@lang('Closed')</span>
								@endif
								<div>
									@if($ticket->status != 3)
										<button class="close-btn" id="infoBtn" data-bs-toggle="modal"
												data-bs-target="#closeTicketModal">
											<i class="fal fa-close"></i>
										</button>
									@endif
								</div>
							</div>

							<!-- chats -->
							<div class="chats">
								@if(count($ticket->messages) > 0)
									@foreach($ticket->messages as $item)
										@if($item->admin_id == null)
											<div class="chat-box this-side">
												<div class="text-wrapper">
													<p class="name">{{ __(optional($ticket->user)->username) }}</p>
													<div class="text">
														<p>{{ __($item->message) }}</p>
													</div>
													@if(0 < count($item->attachments))
														@foreach($item->attachments as $k => $image)
															<div class="file">
																<a href="{{ route('user.ticket.download',encrypt($image->id)) }}">
																	<i class="fal fa-file"></i>
																	<span>@lang('File(s)') {{ __(++$k) }}</span>
																</a>
															</div>
														@endforeach
													@endif
													<span
														class="time">{{ __($item->created_at->format('d M, Y h:i A')) }}</span>
												</div>
												<div class="img">
													<img class="img-fluid"
														 src="{{getFile(optional($ticket->user->profile)->driver,optional(optional($ticket->user)->profile)->profile_picture)}}"
														 alt="..."/>
												</div>
											</div>
										@else
											<div class="chat-box opposite-side">
												<div class="img">
													<img class="img-fluid"
														 src="{{ getFile(optional($item->admin->profile)->driver,optional($item->admin->profile)->profile_picture)}}"
														 alt="..."/>
												</div>
												<div class="text-wrapper">
													<p class="name">{{ __(optional($item->admin)->name) }}</p>
													<div class="text">
														<p>
															{{ __($item->message) }}
														</p>
													</div>
													@if(0 < count($item->attachments))
														@foreach($item->attachments as $k => $image)
															<div class="file">
																<a href="{{ route('user.ticket.download',encrypt($image->id)) }}">
																	<i class="fal fa-file"></i>
																	<span>@lang('File(s)') {{ __(++$k) }}</span>
																</a>
															</div>
														@endforeach
													@endif
													<span
														class="time">{{ __($item->created_at->format('d M, Y h:i A')) }}</span>
												</div>
											</div>
										@endif
									@endforeach
								@endif
							</div>
							<!-- typing area -->
							<form class="form-row" action="{{ route('user.ticket.reply', $ticket->id)}}"
								  method="post"
								  enctype="multipart/form-data">
								@csrf
								@method('PUT')
								@if($ticket->status != 3)
									<div class="typing-area">
										<div class="img-preview">
											<button class="delete">
												<i class="fal fa-times" aria-hidden="true"></i>
											</button>
											<img
												id="attachment"
												src="{{getFile('local','dummy')}}"
												alt="..."
												class="img-fluid insert"/>
										</div>
										<div class="input-group">
											<div>
												<button class="upload-img send-file-btn">
													<i class="fal fa-paperclip" aria-hidden="true"></i>
													<input
														class="form-control"
														accept="image/*"
														type="file"
														name="attachments[]"
														onchange="previewImage('attachment')"
													/>
												</button>
												<p class="text-danger select-files-count"></p>
											</div>
											<input type="text" name="message" value="{{ old('message') }}"
												   class="form-control"/>
											<button type="submit" name="replayTicket" value="1" class="submit-btn">
												<i class="fal fa-paper-plane" aria-hidden="true"></i>
											</button>
										</div>
									</div>
								@endif
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="closeTicketModal" tabindex="-1" aria-labelledby="describeModalLabel"
		 aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="describeModalLabel"> @lang('Confirmation !')</h4>
					<button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
						<i class="fal fa-times"></i>
					</button>
				</div>
				<form method="post" action="{{ route('user.ticket.reply', $ticket->id) }}">
					@csrf
					@method('PUT')
					<div class="modal-body">
						<p>
							@lang('Are you want to close ticket')?
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="cmn-btn3"
								data-bs-dismiss="modal">@lang('Close')</button>
						<button type="submit" name="replayTicket"
								value="2" class="cmn-btn">@lang("Confirm")</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		'use strict';

		$(document).on('change', '#upload', function () {
			let fileCount = $(this)[0].files.length;
			$('.select-files-count').text(fileCount + ' file(s) selected')
		});


	</script>
	@if ($errors->any())
		@php
			$collection = collect($errors->all());
			$errors = $collection->unique();
		@endphp
		<script>
			"use strict";
			@foreach ($errors as $error)
			Notiflix.Notify.Failure("{{ trans($error) }}");
			@endforeach
		</script>
	@endif
@endsection


