
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<input placeholder="@lang('User')" name="user" value="{{ @request()->user }}" type="text"
				   class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<input placeholder="@lang('Submitted Date')" name="created_at" id="created_at"
				   value="{{ $search['created_at'] ?? '' }}" type="date" class="form-control form-control-sm"
				   autocomplete="off">
		</div>
	</div>

	<div class="col-md-4">
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-sm btn-block">@lang('Search')</button>
		</div>
	</div>
</div>
