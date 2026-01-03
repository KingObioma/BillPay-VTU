<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Sender')" name="sender" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Receiver')" name="receiver" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('E-mail')" name="email" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Transaction ID')" name="utr" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Min Amount')" name="min" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Maximum Amount')" name="max" value="" type="text" class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Transaction Date')" name="created_at" id="created_at" value="" type="text" class="form-control form-control-sm" autocomplete="off">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select name="currency_id" class="form-control form-control-sm">
				<option value="">@lang('Select Currency')</option>
				@foreach($currencies as $key => $currency)
					<option value="{{ $currency->id }}"> {{ $currency->code }} @lang('-') {{ $currency->name }} </option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select name="status" class="form-control form-control-sm">
				<option value="">@lang('Select Status')</option>
					<option value="1"> @lang('Success') </option>
					<option value="0"> @lang('Pending') </option>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input type="submit" value="Search" class="btn btn-primary btn-sm btn-block">
		</div>
	</div>
</div>
