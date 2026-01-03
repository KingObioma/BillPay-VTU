<!-- General JS Scripts -->
<script src="{{ asset($themeTrue . 'js/jquery-3.6.1.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/select2.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/owl.carousel.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/intlTelInput.min.js')}}"></script>
<script src="{{ asset($themeTrue . 'js/dashboard.js')}}"></script>


<!-- JS Libraies -->
<script src="{{ asset('assets/dashboard/js/pusher.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/vue.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/axios.min.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/notiflix-aio-2.7.0.min.js') }}"></script>


<script>
	$(document).ready(function () {
		$(document).ajaxStart(function () {
			$('#wait').removeClass('d-none').show();
		});
		$(document).ajaxComplete(function () {
			$('#wait').hide();
		});
	});
</script>

@if(basicControl()->push_notification)
	<script>
		'use strict';
		let pushNotificationArea = new Vue({
			el: "#pushNotificationArea",
			data: {
				items: [],
			},
			mounted() {
				this.getNotifications();
				this.pushNewItem();
			},
			methods: {
				getNotifications() {
					let app = this;
					axios.get("{{ route('push.notification.show') }}")
						.then(function (res) {
							app.items = res.data;
						})
				},
				readAt(id, link) {
					let app = this;
					let url = "{{ route('push.notification.readAt', 0) }}";
					url = url.replace(/.$/, id);
					axios.get(url)
						.then(function (res) {
							if (res.status) {
								app.getNotifications();
								if (link !== '#') {
									window.location.href = link
								}
							}
						})
				},
				readAll() {
					let app = this;
					let url = "{{ route('push.notification.readAll') }}";
					axios.get(url)
						.then(function (res) {
							if (res.status) {
								app.items = [];
							}
						})
				},
				pushNewItem() {
					let app = this;
					Pusher.logToConsole = false;
					let pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
						encrypted: true,
						cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
					});
					let channel = pusher.subscribe('user-notification.' + "{{ Auth::id() }}");
					channel.bind('App\\Events\\UserNotification', function (data) {
						app.items.unshift(data.message);
					});
					channel.bind('App\\Events\\UpdateUserNotification', function (data) {
						app.getNotifications();
					});
				}
			}
		});
	</script>
@endif

<script>
	'use strict';
	// for search
	$(document).on('input', '.global-search', function () {
		var search = $(this).val().toLowerCase();

		if (search.length == 0) {
			$('.search-result').find('.content').html('');
			$(this).siblings('.search-backdrop').addClass('d-none');
			$(this).siblings('.search-result').addClass('d-none');
			return false;
		}

		$('.search-result').find('.content').html('');
		$(this).siblings('.search-backdrop').removeClass('d-none');
		$(this).siblings('.search-result').removeClass('d-none');

		var match = $('.sidebar-nav li').filter(function (idx, element) {
			if (!$(element).find('a').hasClass('has-dropdown') && !$(element).hasClass('menu-header'))
				return $(element).text().trim().toLowerCase().indexOf(search) >= 0 ? element : null;
		}).sort();

		if (match.length == 0) {
			$('.search-result').find('.content').append(`<div class="search-item"><a href="javascript:void(0)">@lang('No result found')</a></div>`);
			return false;
		}

		match.each(function (index, element) {
			var item_text = $(element).text().replace(/(\d+)/g, '').trim();
			var item_url = $(element).find('a').attr('href');
			if (item_url != '#') {
				$('.search-result').find('.content').append(`<div class="search-item"><a href="${item_url}">${item_text}</a></div>`);
			}
		});
	});
</script>

@stack('extra_scripts')
