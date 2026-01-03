@extends($theme.'layouts.user')
@section('page_title',__('Dashboard'))

@push('extra_styles')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/css/daterangepicker.css') }}"/>
@endpush

@section('content')
	<div class="section dashboard" id="firebase-app">
		<div v-if="user_foreground == '1' || user_background == '1'" class="custom-card">
			<div class="col-12" v-if="notificationPermission == 'default' && !is_notification_skipped"
				 v-cloak>
				<div class="alert d-flex justify-content-between align-items-start border-color-warning" role="alert">
					<div>
						<i class="fa-light fa-triangle-exclamation"></i> @lang('Do not miss any single important notification! Allow your
                                      browser to get instant push notification')
						<button class="btn-modify mx-3" id="allow-notification">@lang('Allow me')</button>
					</div>
					<button class="close-btn pt-1" @click.prevent="skipNotification"><i
							class="fal fa-times"></i>
					</button>
				</div>
			</div>
			<div class="col-12" v-if="notificationPermission == 'denied' && !is_notification_skipped"
				 v-cloak>
				<div class="alert d-flex justify-content-between align-items-start border-color-warning" role="alert">
					<div>
						<i class="fa-light fa-triangle-exclamation"></i> @lang('Please allow your browser to get instant push notification.
										Allow it from
										notification setting.')
					</div>
					<button class="close-btn pt-1" @click.prevent="skipNotification"><i
							class="fal fa-times"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- Tab mobile view carousel start -->
		<div class="tab-mobile-view-carousel-section mb-30 d-lg-none">
			<div class="row">
				<div class="col-12">
					<div class="owl-carousel owl-theme carousel-1">
						<div class="item">
							<div class="box-card box-card1">
								<div class="box-card-header">
									<h5 class="box-card-title"><i class="fa-light fa-wallet"></i>@lang('Wallet Balance')
									</h5>
								</div>
								<div class="box-card-body">
									<h3 class="mb-0">{{config('basic.currency_symbol')}}{{getAmount(auth()->user()->balance,2)}}</h3>
									<div class="statistics">
										<p class="growth">{{config('basic.currency_symbol')}}{{getAmount($billRecord['totalWalletPays'],2)}}</p>
										<div class="time">@lang('wallet pay')</div>
									</div>
								</div>
							</div>
						</div>
						<div class="item">
							<div class="box-card box-card2">
								<div class="box-card-header">
									<h5 class="box-card-title"><i class="fa-light fa-paper-plane"></i>@lang('Pay Bill')</h5>
								</div>
								<div class="box-card-body">
									<h3 class="mb-0">{{number_format($billRecord['completeBills'])}}</h3>
									<div class="statistics">
										<p class="growth">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['completeBills'],2)}}</p>
										<div class="time">@lang('last 30 days')</div>
									</div>
								</div>
							</div>
						</div>
						<div class="item">
							<div class="box-card box-card3">
								<div class="box-card-header">
									<h5 class="box-card-title"><i class="fa-light fa-spinner"></i>@lang('Pending Bill')
									</h5>
								</div>
								<div class="box-card-body">
									<h3 class="mb-0">{{number_format($billRecord['pendingBills'])}}</h3>
									<div class="statistics">
										<p class="growth middle">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['pendingBills'],2)}}</p>
										<div class="time">@lang('last 30 days')</div>
									</div>
								</div>
							</div>
						</div>
						<div class="item">
							<div class="box-card box-card4">
								<div class="box-card-header">
									<h5 class="box-card-title"><i
											class="fa-light fa-undo"></i>@lang('Return Bill')</h5>
								</div>
								<div class="box-card-body">
									<h3 class="mb-0">{{number_format($billRecord['returnBills'])}}</h3>
									<div class="statistics">
										<p class="growth down">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['returnBills'],2)}}</p>
										<div class="time">@lang('last 30 days')</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Tab mobile view carousel end -->

		<!-- Dashboard card start -->
		<div class="col-12 d-none d-lg-block">
			<div class="row g-4">
				<div class="col-xxl-3 col-sm-6 box-item">
					<div class="box-card">
						<div class="box-card-header">
							<h5 class="box-card-title"><i class="fa-light fa-wallet"></i>@lang('Wallet Balance')
							</h5>
						</div>
						<div class="box-card-body">
							<h3 class="mb-0">{{config('basic.currency_symbol')}}{{getAmount(auth()->user()->balance,2)}}</h3>
							<div class="statistics">
								<p class="growth">{{config('basic.currency_symbol')}}{{getAmount($billRecord['totalWalletPays'],2)}}</p>
								<div class="time">@lang('wallet pay')</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxl-3 col-sm-6 box-item">
					<div class="box-card">
						<div class="box-card-header">
							<h5 class="box-card-title"><i class="fa-light fa-paper-plane"></i>@lang('Pay Bill')</h5>
						</div>
						<div class="box-card-body">
							<h3 class="mb-0">{{number_format($billRecord['completeBills'])}}</h3>
							<div class="statistics">
								<p class="growth">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['completeBills'],2)}}</p>
								<div class="time">@lang('last 30 days')</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxl-3 col-sm-6 box-item">
					<div class="box-card">
						<div class="box-card-header">
							<h5 class="box-card-title"><i class="fa-light fa-spinner"></i>@lang('Pending Bill')</h5>
						</div>
						<div class="box-card-body">
							<h3 class="mb-0">{{number_format($billRecord['pendingBills'])}}</h3>
							<div class="statistics">
								<p class="growth middle">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['pendingBills'],2)}}</p>
								<div class="time">@lang('last 30 days')</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxl-3 col-sm-6 box-item">
					<div class="box-card">
						<div class="box-card-header">
							<h5 class="box-card-title"><i
									class="fa-light fa-undo"></i>@lang('Return Bill')</h5>
						</div>
						<div class="box-card-body">
							<h3 class="mb-0">{{number_format($billRecord['returnBills'])}}</h3>
							<div class="statistics">
								<p class="growth down">{{config('basic.currency_symbol')}}{{getAmount($lastMonthBillRecord['returnBills'],2)}}</p>
								<div class="time">@lang('last 30 days')</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Dashboard card start -->

		<div class="row my-4">
			<div class="col-md-12">
				<div class="card mb-4">
					<div
						class="card-header py-3 d-flex flex-wrap flex-row align-items-center justify-content-between">
						<h5 class="card-title">@lang('Transaction Summary')</h5>
						<input type="button" class="btn btn-sm btn-base-color" name="daterange" value=""/>
					</div>
					<div class="card-body">
						<div>
							<canvas id="line-chart" height="80"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4 shadow-sm">
					<div class="card-body">
						<h5 class="card-title">@lang('Bill Statistics')</h5>
						<div>
							<canvas id="line-chart-2" height="120"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/Chart.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/dashboard/js/moment.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/dashboard/js/daterangepicker.min.js') }}"></script>
@endpush

@section('scripts')
	<script>
		'use strict';
		$(document).ready(function () {
			$('input[name="daterange"]').daterangepicker({
				opens: 'left',
				startDate: moment().startOf('month'),
				endDate: moment().endOf('month'),
				locale: {
					format: 'MMMM D, YYYY'
				}
			}, function (start, end, label) {
				getTransaction(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
			});

			function getTransaction(start, end) {
				$.ajax({
					method: "GET",
					url: "{{ route('user.get.transaction.chart') }}",
					dataType: "json",
					data: {
						'start': start,
						'end': end,
					}
				})
					.done(function (response) {
						new Chart(document.getElementById("line-chart"), {
							type: 'line',
							data: {
								labels: response.labels,
								datasets: [
									{
										data: response.dataBillPay,
										label: "Bill Pay",
										borderColor: "#875aff",
										fill: false
									},
								]
							}
						});
					});
			}

			new Chart(document.getElementById("line-chart"), {
				type: 'line',
				data: {
					labels: {!! json_encode($labels) !!},
					datasets: [
						{
							data: {!! json_encode($dataBillPay) !!},
							label: "Bill Pay",
							borderColor: "#875aff",
							fill: false
						},
					]
				}
			});

			new Chart(document.getElementById("line-chart-2"), {
				type: 'bar',
				data: {
					labels: {!! json_encode($monthLabels) !!},
					datasets: [
						{
							data: {!! json_encode($yearCompleteBills) !!},
							label: "Pay Bill",
							borderColor: "#875AFF",
							backgroundColor: "#875AFF",
						},
						{
							data: {!! json_encode($yearPendingBills) !!},
							label: "Pending Bill",
							borderColor: "#e67e22",
							backgroundColor: "#e67e22",
						},

						{
							data: {!! json_encode($yearReturnBills) !!},
							label: "Return Bill",
							borderColor: "#e52719",
							backgroundColor: "#e52719",
						},
					]
				}
			});
		});
	</script>
@endsection
@if($firebaseNotify)
	@push('extra_scripts')
		<script type="module">
			import {initializeApp} from "https://www.gstatic.com/firebasejs/9.17.1/firebase-app.js";
			import {
				getMessaging,
				getToken,
				onMessage
			} from "https://www.gstatic.com/firebasejs/9.17.1/firebase-messaging.js";

			const firebaseConfig = {
				apiKey: "{{$firebaseNotify->api_key}}",
				authDomain: "{{$firebaseNotify->auth_domain}}",
				projectId: "{{$firebaseNotify->project_id}}",
				storageBucket: "{{$firebaseNotify->storage_bucket}}",
				messagingSenderId: "{{$firebaseNotify->messaging_sender_id}}",
				appId: "{{$firebaseNotify->app_id}}",
				measurementId: "{{$firebaseNotify->measurement_id}}"
			};

			const app = initializeApp(firebaseConfig);
			const messaging = getMessaging(app);
			if ('serviceWorker' in navigator) {
				navigator.serviceWorker.register('{{ getProjectDirectory() }}' + `/firebase-messaging-sw.js`, {scope: './'}).then(function (registration) {
						requestPermissionAndGenerateToken(registration);
					}
				).catch(function (error) {
				});
			} else {
			}

			onMessage(messaging, (payload) => {
				if (payload.data.foreground || parseInt(payload.data.foreground) == 1) {
					const title = payload.notification.title;
					const options = {
						body: payload.notification.body,
						icon: payload.notification.icon,
					};
					new Notification(title, options);
				}
			});

			function requestPermissionAndGenerateToken(registration) {
				document.addEventListener("click", function (event) {
					if (event.target.id == 'allow-notification') {
						Notification.requestPermission().then((permission) => {
							if (permission === 'granted') {
								getToken(messaging, {
									serviceWorkerRegistration: registration,
									vapidKey: "{{$firebaseNotify->vapid_key}}"
								})
									.then((token) => {
										$.ajax({
											url: "{{ route('user.save.token') }}",
											method: "post",
											data: {
												token: token,
											},
											success: function (res) {
											}
										});
										window.newApp.notificationPermission = 'granted';
									});
							} else {
								window.newApp.notificationPermission = 'denied';
							}
						});
					}
				});
			}
		</script>
		<script>
			window.newApp = new Vue({
				el: "#firebase-app",
				data: {
					user_foreground: '',
					user_background: '',
					notificationPermission: Notification.permission,
					is_notification_skipped: sessionStorage.getItem('is_notification_skipped') == '1'
				},
				mounted() {
					this.user_foreground = "{{$firebaseNotify->user_foreground}}";
					this.user_background = "{{$firebaseNotify->user_background}}";
				},
				methods: {
					skipNotification() {
						sessionStorage.setItem('is_notification_skipped', '1');
						this.is_notification_skipped = true;
					}
				}
			});
		</script>
	@endpush
@endif
