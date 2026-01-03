@extends('admin.layouts.master')
@section('page_title',__('Dashboard'))
@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Admin Dashboard')</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Admin Dashboard')</div>
				</div>
			</div>

			<div class="row " id="firebase-app" v-if="admin_foreground == '1' || admin_background == '1'">
				<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-4 mt-0"
					 v-if="notificationPermission == 'default' && !is_notification_skipped" v-cloak>
					<div class="d-flex justify-content-between align-items-start bd-callout bd-callout-warning  shadow">
						<div>
							<i class="fas fa-info-circle mr-2"></i> @lang('Do not miss any single important notification! Allow your
                        browser to get instant push notification')
							<button id="allow-notification" class="btn btn-sm btn-primary mx-2"><i
									class="fa fa-check-circle"></i> @lang('Allow me')</button>
						</div>
						<a href="javascript:void(0)" @click.prevent="skipNotification"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-4 mt-0"
					 v-if="notificationPermission == 'denied' && !is_notification_skipped" v-cloak>
					<div class="d-flex justify-content-between align-items-start bd-callout bd-callout-warning  shadow">
						<div>
							<i class="fas fa-info-circle mr-2"></i> @lang('Please allow your browser to get instant push notification.
                        Allow it from
                        notification setting.')
						</div>
						<a href="javascript:void(0)" @click.prevent="skipNotification"><i class="fas fa-times"></i></a>
					</div>
				</div>
			</div>

			<!---------- User Statistics -------------->
			<div class="row mb-3">
				<div class="col-md-12">
					<h6 class="mb-3 text-darku">@lang('User Statistics')</h6>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-success">
							<i class="fas fa-users"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Total User')</h4>
							</div>
							<div class="card-body">
								{{ (getAmount($userRecord['totalUser']))  }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-success">
							<i class="fas fa-user-tie"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Active User')</h4>
							</div>
							<div class="card-body">
								{{ (getAmount($userRecord['activeUser']))  }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-success">
							<i class="fas fa-user-check"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Verified User')</h4>
							</div>
							<div class="card-body">
								{{ (getAmount($userRecord['verifiedUser'])) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-success">
							<i class="fas fa-user-plus"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('New User')</h4>
							</div>
							<div class="card-body">
								{{ getAmount($userRecord['todayJoin']) }}
							</div>
						</div>
					</div>
				</div>
			</div>


			<!---------- Service Statistics -------------->
			<div class="row mb-3">
				<div class="col-md-12">
					<h6 class="mb-3 text-darku">@lang('Service Statistics')</h6>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-warning">
							<i class="fas fa-stream"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Total Service')</h4>
							</div>
							<div class="card-body">
								{{ number_format($serviceRecord['totalServices']) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-warning">
							<i class="fas fa-check-circle"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Active Service')</h4>
							</div>
							<div class="card-body">
								{{ number_format($serviceRecord['activeServices']) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-warning">
							<i class="fas fa-exclamation"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Inactive Service')</h4>
							</div>
							<div class="card-body">
								{{ number_format($serviceRecord['inactiveServices']) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-warning">
							<i class="fas fa-tasks"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Active Bill Method')</h4>
							</div>
							<div class="card-body">
								{{ $activeMethod->methodName }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<!---------- Bill Statistics -------------->
			<div class="row mb-3">
				<div class="col-md-12">
					<h6 class="mb-3 text-darku">@lang('Bill Statistics')</h6>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-danger">
							<i class="fas fa-tag"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Total Bill')</h4>
							</div>
							<div class="card-body">
								{{ number_format($billRecord['totalBills'])  }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-danger">
							<i class="fas fa-check"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Completed Bill')</h4>
							</div>
							<div class="card-body">
								{{ number_format($billRecord['completeBills'])  }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-danger">
							<i class="fas fa-spinner"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Pending Bill')</h4>
							</div>
							<div class="card-body">
								{{ number_format($billRecord['pendingBills'])  }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<div class="card card-statistic-1 shadow-sm">
						<div class="card-icon bg-danger">
							<i class="fas fa-undo"></i>
						</div>
						<div class="card-wrap">
							<div class="card-header">
								<h4>@lang('Return Bill')</h4>
							</div>
							<div class="card-body">
								{{ number_format($billRecord['returnBills'])  }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<!---------- Transaction Summary -------------->
			<div class="row mb-3">
				<div class="col-md-12">
					<div class="card mb-4 shadow-sm">
						<div class="card-body">
							<h5 class="card-title">@lang('This month transactions summary')</h5>
							<div>
								<canvas id="line-chart" height="80"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!---------- Automatic & Wallet -------------->
			<div class="row mb-3">
				<div class="col-md-8">
					<div class="card mb-4 shadow-sm">
						<div class="card-body">
							<h5 class="card-title">@lang('Automatic & Wallet Pay')</h5>
							<div>
								<canvas id="line-chart-2" height="120"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card mb-4 shadow-sm">
						<div class="card-body">
							<h5 class="card-title">@lang('Gateway Used For Bill Pays')</h5>
							<div>
								<canvas id="pie-chart-2" height="255"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>



	@if($basicControl->is_active_cron_notification)
		<div class="modal fade" id="cron-info" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							<i class="fas fa-info-circle"></i>
							@lang('Cron Job Set Up Instruction')
						</h5>
						<button type="button" class="close cron-notification-close" data-dismiss="modal"
								aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<p class="bg-orange text-white p-2">
									<i>@lang('**To sending emails and updating currency rate automatically you need to setup cron job in your server. Make sure your job is running properly. We insist to set the cron job time as minimum as possible.**')</i>
								</p>
							</div>
							<div class="col-md-12 form-group">
								<label><strong>@lang('Command for Email')</strong></label>
								<div class="input-group ">
									<input type="text" class="form-control copyText"
										   value="curl -s {{ route('queue.work') }}" disabled>
									<div class="input-group-append">
										<button class="input-group-text bg-primary btn btn-primary text-white copy-btn">
											<i class="fas fa-copy"></i></button>
									</div>
								</div>
							</div>
							<div class="col-md-12 form-group">
								<label><strong>@lang('Command for Currency Rate Update')</strong></label>
								<div class="input-group ">
									<input type="text" class="form-control copyText"
										   value="curl -s {{ route('schedule:run') }}"
										   disabled>
									<div class="input-group-append">
										<button class="input-group-text bg-primary btn btn-primary text-white copy-btn">
											<i class="fas fa-copy"></i></button>
									</div>
								</div>
							</div>
							<div class="col-md-12 text-center">
								<p class="bg-dark text-white p-2">
									@lang('*To turn off this pop up go to ')
									<a href="{{route('basic.control')}}"
									   class="text-orange">@lang('Basic control')</a>
									@lang(' and disable `Cron Set Up Pop Up`.*')
								</p>
							</div>

							<div class="col-md-12">
								<p class="text-muted"><span class="text-secondary font-weight-bold">@lang('N.B'):</span>
									@lang('If you are unable to set up cron job, Here is a video tutorial for you')
									<a href="https://www.youtube.com/watch?v=wuvTRT2ety0" target="_blank"><i
											class="fab fa-youtube"></i> @lang('Click Here') </a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
@endsection

@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/Chart.min.js') }}"></script>
@endpush

@section('scripts')
	<script>
		'use strict';
		$(document).ready(function () {
			new Chart(document.getElementById("line-chart"), {
				type: 'line',
				data: {
					labels: {!! json_encode($labels) !!},
					datasets: [
						{
							data: @json($dataBillPay),
							label: "Billpay",
							borderColor: "#e67e22",
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
							data: {!! json_encode($yearAutomatic) !!},
							label: "Automatic",
							borderColor: "#7EFF2A",
							backgroundColor: "#7EFF2A",
						},
						{
							data: {!! json_encode($yearWallet) !!},
							label: "Wallet",
							borderColor: "#FF9200",
							backgroundColor: "#FF9200",
						},
					]
				}
			});

			new Chart(document.getElementById("pie-chart-2"), {
				type: 'pie',
				data: {
					labels: {!! json_encode($paymentMethodeLabel) !!},
					datasets: [{
						backgroundColor: ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50",
							"#f1c40f", "#e67e22", "#e74c3c", "#ecf0f1", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d",
							"#55efc4", "#81ecec", "#74b9ff", "#a29bfe", "#dfe6e9",
						],
						data: {!! json_encode($paymentMethodeData) !!},
					}]
				},
				options: {
					tooltips: {
						callbacks: {
							label: function (tooltipItems, data) {
								return data.labels[tooltipItems.index] + ': ' + data.datasets[0].data[tooltipItems.index] + " {{ __($basicControl->base_currency_code) }}";
							}
						}
					}
				}
			});
		});

		$(document).ready(function () {
			let isActiveCronNotification = '{{ $basicControl->is_active_cron_notification }}';
			if (isActiveCronNotification == 1)
				$('#cron-info').modal('show');
			$(document).on('click', '.copy-btn', function () {
				var _this = $(this)[0];
				var copyText = $(this).parents('.input-group-append').siblings('input');
				$(copyText).prop('disabled', false);
				copyText.select();
				document.execCommand("copy");
				$(copyText).prop('disabled', true);
				$(this).text('Coppied');
				setTimeout(function () {
					$(_this).text('');
					$(_this).html('<i class="fas fa-copy"></i>');
				}, 500)
			});
		})
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
											url: "{{ route('admin.save.token') }}",
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
					admin_foreground: '',
					admin_background: '',
					notificationPermission: Notification.permission,
					is_notification_skipped: sessionStorage.getItem('is_notification_skipped') == '1'
				},
				mounted() {
					this.admin_foreground = "{{$firebaseNotify->admin_foreground}}";
					this.admin_background = "{{$firebaseNotify->admin_background}}";
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
