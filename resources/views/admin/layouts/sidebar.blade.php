<!-- Sidebar -->
<div class="main-sidebar sidebar-style-2 shadow-sm">
	<aside id="sidebar-wrapper">
		<div class="sidebar-brand">
			<a href="{{ route('admin.home') }}">
				<img src="{{ getFile(config('basic.default_file_driver'),config('basic.admin_logo')) }}"
					 class="dashboard-logo"
					 alt="@lang('Logo')">
			</a>
		</div>
		<div class="sidebar-brand sidebar-brand-sm">
			<a href="{{ route('admin.home') }}">
				<img src="{{ getFile(config('basic.default_file_driver'),config('basic.favicon_image')) }}"
					 class="dashboard-logo-sm"
					 alt="@lang('Logo')">
			</a>
		</div>

		<ul class="sidebar-menu">
			<li class="menu-header">@lang('Dashboard')</li>
			<li class="dropdown {{ activeMenu(['admin.home']) }}">
				<a href="{{ route('admin.home') }}" class="nav-link"><i
						class="fas fa-tachometer-alt text-primary"></i><span>@lang('Dashboard')</span></a>
			</li>

			<li class="menu-header">@lang('Manage Bill')</li>
			<li class="dropdown {{ activeMenu(['admin.bill.method.list']) }}">
				<a href="{{ route('admin.bill.method.list') }}" class="nav-link"><i
						class="fas fa-tags text-success"></i><span>@lang('Method Setup')</span></a>
			</li>
			<li class="dropdown {{ activeMenu(['admin.bill.service.list']) }}">
				<a href="{{ route('admin.bill.service.list') }}" class="nav-link"><i
						class="fas fa-stream text-primary"></i><span>@lang('Service List')</span></a>
			</li>

			<li class="dropdown {{ activeMenu(['bill.pay.list']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-credit-card text-warning"></i> <span>@lang('Bill History')</span>
				</a>
				<ul class="dropdown-menu">
					<li class="">
						<a class="nav-link" href="{{ route('bill.pay.list','all') }}">
							@lang('All Bills')
						</a>
					</li>

					<li class="">
						<a class="nav-link " href="{{ route('bill.pay.list','pending') }}">
							@lang('Pending Bills') <sup
								class="badge badge-pill badge-danger ml-1">{{\App\Models\BillPay::where('status','2')->count()??0}}</sup>
						</a>
					</li>
					<li class="">
						<a class="nav-link" href="{{ route('bill.pay.list','completed') }}">
							@lang('Complete Bills')
						</a>
					</li>
					<li class="">
						<a class="nav-link " href="{{ route('bill.pay.list','return') }}">
							@lang('Return Bills')
						</a>
					</li>
				</ul>
			</li>


			<li class="menu-header">@lang('TICKET PANEL')</li>
			<li class="dropdown {{ activeMenu(['admin.ticket','admin.ticket.view','admin.ticket.search']) }}">
				<a href="{{ route('admin.ticket') }}" class="nav-link"><i
						class="fas fa-headset text-info"></i><span>@lang('Support Tickets')</span></a>
			</li>


			<li class="menu-header">@lang('KYC Panel')</li>
			<li class="dropdown {{ activeMenu(['kyc.create']) }}">
				<a href="{{ route('kyc.create') }}" class="nav-link"><i
						class="fas fa-sticky-note text-info"></i><span>@lang('KYC Form')</span></a>
			</li>
			<li class="dropdown {{ activeMenu(['kyc.list']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-stream text-dark"></i> <span>@lang('KYC History')</span>
				</a>
				<ul class="dropdown-menu">
					<li class="">
						<a class="nav-link" href="{{ route('kyc.list','pending') }}">
							@lang('Pending KYC')
						</a>
					</li>
					<li class="">
						<a class="nav-link" href="{{ route('kyc.list','approve') }}">
							@lang('Approved KYC')
						</a>
					</li>
					<li class="">
						<a class="nav-link" href="{{ route('kyc.list','rejected') }}">
							@lang('Rejected KYC')
						</a>
					</li>
				</ul>
			</li>

			<li class="menu-header">@lang('User Panel')</li>
			<li class="dropdown {{ activeMenu(['user-list','user.search','inactive.user.search','send.mail.user','inactive.user.list']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-users text-warning"></i> <span>@lang('Manage User')</span>
				</a>
				<ul class="dropdown-menu">
					<li class="{{ activeMenu(['user-list','user.search']) }}">
						<a class="nav-link " href="{{ route('user-list') }}">
							@lang('All User')
						</a>
					</li>
					<li class="{{ activeMenu(['inactive.user.list','inactive.user.search']) }}">
						<a class="nav-link" href="{{ route('inactive.user.list') }}">
							@lang('Inactive User')
						</a>
					</li>
					<li class="{{ activeMenu(['send.mail.user']) }}">
						<a class="nav-link" href="{{ route('send.mail.user') }}">
							@lang('Send Mail All User')
						</a>
					</li>
				</ul>
			</li>


			<li class="menu-header">@lang('Transactions')</li>

			<li class="{{ activeMenu(['admin.payment.pending']) }}">
				<a class="nav-link" href="{{route('admin.payment.pending')}}">
					<i class="fas fa-spinner text-info"></i><span>@lang('Payment Request')</span>

				</a>
			</li>
			<li class="{{ activeMenu(['admin.payment.log','admin.payment.search']) }}">
				<a class="nav-link" href="{{route('admin.payment.log')}}">
					<i class="fas fa-history text-green"></i><span>@lang('Payment Log')</span>
				</a>
			</li>

			<li class="dropdown {{ activeMenu(['admin.transaction.index','admin.transaction.search']) }}">
				<a href="{{ route('admin.transaction.index') }}" class="nav-link"><i
						class="fas fa-chart-line text-warning"></i><span>@lang('Transaction List')</span></a>
			</li>


			<li class="menu-header">@lang('SETTINGS PANEL')</li>
			<li class="dropdown {{ activeMenu(['settings','seo.update','plugin.config','tawk.control','google.analytics.control','google.recaptcha.control','fb.messenger.control','service.control','logo.update','breadcrumb.update','seo.update','currency.exchange.api.config','sms.config', 'sms.template.index','sms.template.edit','voucher.settings','basic.control','securityQuestion.index','securityQuestion.create','securityQuestion.edit','pusher.config','notify.template.index','notify.template.edit','language.index','language.create', 'language.edit','language.keyword.edit', 'email.config','email.template.index','email.template.default', 'email.template.edit', 'charge.index', 'charge.edit', 'currency.index', 'currency.create', 'currency.edit', 'charge.chargeEdit' ]) }}">
				<a href="{{ route('settings') }}" class="nav-link"><i
						class="fas fa-cog text-primary"></i><span>@lang('Control Panel')</span></a>
			</li>


			<li class="dropdown {{ activeMenu(['appSetting' ]) }}">
				<a href="{{ route('appSetting') }}" class="nav-link"><i
						class="fas fa-server text-danger"></i><span>@lang('App Setting')</span></a>
			</li>

			<li class="dropdown {{ activeMenu(['payment.methods','edit.payment.methods','admin.deposit.manual.index','admin.deposit.manual.create','admin.deposit.manual.edit']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-money-check-alt text-success"></i> <span>@lang('Payment Settings')</span>
				</a>
				<ul class="dropdown-menu">
					<li class="{{ activeMenu(['payment.methods','edit.payment.methods']) }}">
						<a class="nav-link" href="{{ route('payment.methods') }}">
							@lang('Payment Methods')
						</a>
					</li>

					<li class="{{ activeMenu(['admin.deposit.manual.index','admin.deposit.manual.create','admin.deposit.manual.edit']) }}">
						<a class="nav-link" href="{{route('admin.deposit.manual.index')}}">
							@lang('Manual Gateway')
						</a>
					</li>

				</ul>
			</li>


			<li class="menu-header">@lang('MANAGE CONTENT')</li>
			<li class="dropdown {{ activeMenu(['template.show']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-newspaper text-info"></i> <span>@lang('Section Heading')</span>
				</a>
				<ul class="dropdown-menu">
					@foreach(array_diff(array_keys(config('templates')),['message','template_media']) as $name)
						<li class="{{ activeMenu(['template.show'],$name) }}">
							<a class="nav-link" href="{{ route('template.show',$name) }}">
								@lang(ucfirst(kebab2Title($name)))
							</a>
						</li>
					@endforeach
				</ul>
			</li>
			<li class="dropdown {{ activeMenu(['content.index','content.create','content.show']) }}">
				<a href="javascript:void(0)" class="nav-link has-dropdown" data-toggle="dropdown">
					<i class="fas fa-book text-pink"></i> <span>@lang('Content Settings')</span>
				</a>
				<ul class="dropdown-menu">
					@foreach(array_diff(array_keys(config('contents')),['message','content_media']) as $name)
						<li class="{{ activeMenu(['content.index','content.create','content.show'],$name) }}">
							<a class="nav-link" href="{{ route('content.index',$name) }}">
								@lang(ucfirst(kebab2Title($name)))
							</a>
						</li>
					@endforeach
				</ul>
			</li>

			@foreach(collect(config('generalsettings.settings')) as $key => $setting)
				<li class="dropdown d-none {{ isMenuActive($setting['route']) }}">
					<a href="{{ getRoute($setting['route'], $setting['route_segment'] ?? null) }}"
					   class="{{isMenuActive($setting['route'])}}"><i
							class="{{$setting['icon']}} text-info"></i><span>{{ __(getTitle($key.' '.'Settings')) }}</span></a>
				</li>
			@endforeach

			<li class="menu-header text-dark text-center">@lang('Version 2.0')</li>
		</ul>

		<div class="mt-4 mb-4 p-3 hide-sidebar-mini">
		</div>
	</aside>
</div>
