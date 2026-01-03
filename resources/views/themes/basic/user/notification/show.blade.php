@extends($theme.'layouts.user')
@section('page_title',__('Notification'))
@section('content')
	<div class="section dashboard">
		<div class="row">
			@include($theme.'user.profile.profileNav')
			<div class="account-settings-profile-section">
				<div class="card">
					<div class="card-header border-0">
						<h5 class="card-title">@lang('Notification Templates')</h5>
					</div>
					<div class="card-body pt-0">
						<p>@lang('We need permission from your browser to show notifications.') <strong>@lang('Request Permission')</strong>
						</p>
						<!-- Cmn table start -->
						<form action="{{route('user.notification')}}" method="post">
							@csrf
							<div class="cmn-table mt-20">
								<div class="table-responsive">
									<table class="table align-middle">
										<thead>
										<tr>
											<th style="width: 15%;" scope="col">@lang('Type')</th>
											<th style="width: 5%;" scope="col">✉️ @lang('Email')</th>
											<th style="width: 5%;" scope="col">📨️ @lang('Sms')</th>
											<th style="width: 5%;" scope="col">🖥 @lang('Push')</th>
											<th style="width: 3%;" scope="col">👩🏻‍💻 @lang('In App')</th>
										</tr>
										</thead>
										<tbody>
										@foreach($allTemplates as $item)
											@if($item['template_key'] != 'ADD_BALANCE_ADMIN'  && $item['template_key'] != 'SUPPORT_TICKET_CREATE' && $item['template_key'] != 'SUPPORT_TICKET_REPLIED')
												<tr>
													<td data-label="Type" scope="row">
														<div class="d-flex align-items-center">
															<span>{{$item['name']}}</span>
														</div>

													</td>
													<td data-label="✉️ Email">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox"
																   role="switch" name="email_key[]"
																   value="{{$item['template_key']}}"
																   id="flexSwitchCheckChecked" {{$item['mail_status']??'disabled'}}
																{{in_array($item['template_key'],auth()->user()->email_key??[]) ? 'checked':''}}>
														</div>
													</td>

													<td data-label="✉️ Sms">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox"
																   role="switch"
																   name="sms_key[]"
																   value="{{$item['template_key']}}"
																   id="flexSwitchCheckChecked" {{$item['sms_status']??'disabled'}}
																{{in_array($item['template_key'],auth()->user()->sms_key??[]) ? 'checked':''}}>
														</div>
													</td>
													<td data-label="🖥 Push">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox"
																   role="switch"
																   name="push_key[]"
																   value="{{$item['template_key']}}"
																   @if(isset($item['firebase_notify_status']) || isset($item['global'])) @else disabled
																   @endif
																   id="flexSwitchCheckChecked"
																{{in_array($item['template_key'],auth()->user()->push_key??[]) ? 'checked':''}}>
														</div>
													</td>

													<td data-label="👩🏻‍💻 In App">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox"
																   role="switch"
																   name="in_app_key[]"
																   value="{{$item['template_key']}}"
																   id="flexSwitchCheckChecked"
																   @if(isset($item['status']) || isset($item['global'])) @else disabled
																@endif
																{{in_array($item['template_key'],auth()->user()->in_app_key??[]) ? 'checked':''}}>
														</div>
													</td>
												</tr>
											@endif
										@endforeach
										</tbody>
									</table>
								</div>

							</div>
							<div class="btn-area mt-20">
								<button type="submit" class="cmn-btn">@lang('Save Changes')</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')

@endsection
