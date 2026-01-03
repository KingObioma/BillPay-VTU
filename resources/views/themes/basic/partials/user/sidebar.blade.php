<!-- Sidebar section start -->
<aside id="sidebar" class="sidebar">
	<ul class="sidebar-nav" id="sidebar-nav">
		<li class="nav-item">
			<a class="nav-link {{menuActive('user.dashboard')}}" href="{{route('user.dashboard')}}">
				<i class="fa-regular fa-grid"></i>
				<span>@lang('Dashboard')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['addFund'])}}"
			   href="{{route('addFund')}}">
				<i class="fa-light fal fa-credit-card"></i>
				<span>@lang('Add Fund')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive('addFundRequest')}}"
			   href="{{route('addFundRequest')}}">
				<i class="fa-light fal fa-spinner"></i>
				<span>@lang('Fund Request')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['pay.bill','pay.bill.select','pay.bill.preview'])}}"
			   href="{{route('pay.bill')}}">
				<i class="fa-light fal fa-paper-plane"></i>
				<span>@lang('Pay Bill')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['pay.bill.list','pay.bill.details'])}}"
			   href="{{route('pay.bill.list')}}">
				<i class="fa-light fal fa-indent"></i>
				<span>@lang('Pay List')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive('pay.bill.request')}}"
			   href="{{route('pay.bill.request')}}">
				<i class="fa-light fal fa-handshake-alt"></i>
				<span>@lang('Bill Pay Request')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive('user.transaction','user.transaction.search')}}"
			   href="{{route('user.transaction')}}">
				<i class="fa-light fal fa-exchange-alt"></i>
				<span>@lang('Transaction')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['user.ticket.list','user.ticket.create','user.ticket.view'])}}"
			   href="{{route('user.ticket.list')}}">
				<i class="fa-light fal fa-user-headset"></i>
				<span>@lang('Support Ticket')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['user.profile'])}}"
			   href="{{route('user.profile')}}">
				<i class="fa-sharp fa-light fa-gear"></i>
				<span>@lang('Account Settings')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['user.twostep.security'])}}"
			   href="{{route('user.twostep.security')}}">
				<i class="fal fa-key"></i>
				<span>@lang('2 FA Security')</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{menuActive(['logout'])}}"
			   href="{{route('logout')}}"
			   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
				<i class="fa-regular fa-right-from-bracket"></i>
				<span>@lang('Sign Out')</span>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
					@csrf
				</form>
			</a>
		</li>
	</ul>
</aside>
<!-- Sidebar section end -->
