<div class="card card-primary shadow">
    <div class="card-header">
        <h4 class="text-primary">@lang('Jump To')</h4>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills flex-column">
            @foreach($settings as $key => $setting)
                <li class="nav-item">
					<a href="{{ getRoute($setting['route'], $setting['route_segment'] ?? null) }}" class="nav-link {{ isMenuActive($setting['route']) }}">
						<i class="{{$setting['icon']}} {{ isMenuActive($setting['route'],2) ? 'text-white' : 'text-primary' }} pr-2"></i>
						{{ __(getTitle($key.' '.$suffix)) }}
					</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
