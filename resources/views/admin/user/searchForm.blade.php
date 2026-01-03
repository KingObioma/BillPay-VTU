<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Name')" name="name" value="{{ isset($search['name']) ? $search['name'] : '' }}" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Phone')" name="phone" value="{{ isset($search['phone']) ? $search['phone'] : '' }}" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('E-mail')" name="email" value="{{ isset($search['email']) ? $search['email'] : '' }}" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Join Date')" name="created_at" id="created_at" value="{{ isset($search['created_at']) ? $search['created_at'] : '' }}" type="date" class="form-control form-control-sm" autocomplete="off">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Last login')" name="last_login_at" id="last_login_at" value="{{ isset($search['last_login_at']) ? $search['last_login_at'] : '' }}" type="date" class="form-control form-control-sm" autocomplete="off">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group search-currency-dropdown">
			<select name="status" class="form-control form-control-sm">
				<option value="">@lang('All Status')</option>
				<option value="active" {{ isset($search['status']) && $search['status'] == 'active' ? 'selected' : '' }}>@lang('Active')</option>
				<option value="inactive" {{ isset($search['status']) && $search['status'] == 'inactive' ? 'selected' : '' }}>@lang('Inactive')</option>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-sm btn-block"><i
				class="fas fa-search"></i> @lang('Search')</button>
		</div>
	</div>
</div>
