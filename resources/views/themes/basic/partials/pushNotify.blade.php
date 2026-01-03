<li class="nav-item dropdown" id="pushNotificationArea" v-cloak>
	<a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
		<i class="fa-light fa-bell"></i>
		<span v-if="items.length > 0" class="badge badge-number" v-cloak>@{{items.length}}</span>
	</a>

	<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
		<li class="notification-item" v-for="(item, index) in items"
			@click.prevent="readAt(item.id, item.description.link)">
			<a href="javascript:void(0)">
				<i class="fa-regular fa-circle-check text-success"></i>
				<div>
					<p>@{{item.description.text}}</p>
					<p>@{{ item.formatted_date }}</p>
				</div>
			</a>
		</li>

		<li class="dropdown-footer">
			<a href="javascript:void(0)" v-if="items.length > 0" @click.prevent="readAll">@lang('Clear all')</a>
			<a href="javascript:void(0)" v-if="items.length == 0"
			   @click.prevent="readAll">@lang('You have no notifications')</a>
		</li>
	</ul>
</li>

