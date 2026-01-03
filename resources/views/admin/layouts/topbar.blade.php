<!-- Top Navbar -->
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">

    <!--------------- Search -------------->
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="javascript:void(0)" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="javascript:void(0)" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                            class="fas fa-search"></i></a></li>
        </ul>
        <div class="search-element">
            <input class="form-control global-search" type="search" placeholder="@lang('Search')" aria-label="Search"
                   data-width="250">
            <span class="btn"><i class="fas fa-search"></i></span>
            <div class="search-backdrop d-none"></div>
            <div class="search-result d-none">
                <div class="search-header">
                    @lang('Result')
                </div>
                <div class="content"></div>
            </div>
        </div>
    </form>

    <ul class="navbar-nav navbar-right">
		<li class="dropdown dropdown-list-toggle" >
			<a href="{{route('home')}}" class="nav-link nav-link-lg">
				<i class="fas fa-globe-americas"></i>
				<div class="d-sm-none d-lg-inline-block"> @lang('Visit Site')</div>

			</a>
		</li>
        <!--------------- Notifications -------------->
        <li class="dropdown dropdown-list-toggle" id="pushNotificationArea">
            <a href="javascript:void(0)" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"
               :class="items.length ? 'beep' : '' ">
                <i class="far fa-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right shadow">
                <div class="dropdown-header text-center text-uppercase">@lang('Notifications')
                </div>
                <div class="notification-panel">
                    <div class="dropdown-list-content dropdown-list-icons" v-for="(item, index) in items" href="javascript:void(0)"
                         @click.prevent="readAt(item.id, item.description.link)">
                        <a class="dropdown-item dropdown-item-unread">
                            <div class="dropdown-item-icon bg-primary text-white">
                                <i :class="item.description.icon"></i>
                            </div>
                            <div class="dropdown-item-desc" v-cloak>
                                @{{ item.description.text }}
                                <div class="time text-primary" v-cloak>@{{ item.formatted_date }}</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="dropdown-footer text-center align-items-center single-border-top">
                    <a href="javascript:void(0)" class="notification-clear-btn no-underline text-capitalize"
                       v-if="items.length > 0" @click.prevent="readAll">@lang('Clear all')</a>
                    <a href="javascript:void(0)" class="no-underline text-capitalize"
                       v-else="">@lang('No notification found')</a>
                </div>
            </div>
        </li>

        <!--------------- User Profile Menu -------------->
        <li class="dropdown">
			<a href="javascript:void(0)" data-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                @if(Auth::check())
                    <img alt="{{ __(Auth::user()->name) }}" src="{{ getFile(optional(Auth::user()->profile)->driver,optional(Auth::user()->profile)->profile_picture) }}"
                         class="rounded-circle mr-1">

                    <div class="d-sm-none d-lg-inline-block">{{ __(optional(Auth::user())->name) }}</div>
			</a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                <a href="{{ route('admin.profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> @lang('Profile')
                </a>
                <a href="{{ route('admin.change.password') }}" class="dropdown-item has-icon">
                    <i class="fas fa-unlock-alt"></i> @lang('Change Password')
                </a>
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" type="button" class="dropdown-item has-icon text-danger"
                   data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i> @lang('Logout')
                </a>
            </div>
            @endif
        </li>
    </ul>
</nav>

<!-- Start Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger pb-2" id="logoutModalLabel">@lang('Confirmation !')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body logout-body">
                @lang('Are you sure you want to logout?')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                <a href="{{ route('logout') }}" type="button" class="btn btn-primary" onclick="event.preventDefault();
			document.getElementById('logout-form').submit();">@lang('Logout')</a>

                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Logout Modal -->
