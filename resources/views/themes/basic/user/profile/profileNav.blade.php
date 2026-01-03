<div class="account-settings-navbar">
	<ul class="nav">
		<li class="nav-item">
			<a class="nav-link {{menuActive('user.profile')}}" aria-current="page" href="{{route('user.profile')}}"><i
					class="fa-light fa-user"></i>@lang('profile')</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{menuActive('user.change.password')}}" aria-current="page" href="{{route('user.change.password')}}"><i
					class="fa-light fa-lock"></i>@lang('password')</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{menuActive('user.notification')}}" href="{{route('user.notification')}}"><i
					class="fa-light fa-link"></i>@lang('Notification')</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{menuActive('user.kyc')}}" href="{{route('user.kyc')}}"><i
					class="fa-light fa-link"></i> @lang('Identity Verification')</a>
		</li>
	</ul>
</div>
