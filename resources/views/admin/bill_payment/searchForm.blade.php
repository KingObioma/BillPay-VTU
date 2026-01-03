<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('username')" name="username" value="{{ @request()->username }}" type="text"
				   class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Category')" name="category" value="{{ @request()->category }}" type="text"
				   class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Type')" name="searchType" value="{{ @request()->searchType }}" type="text"
				   class="form-control form-control-sm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group search-currency-dropdown">
			<select name="status" class="form-control form-control-sm">
				<option value="">@lang('Status')</option>
				<option
					value="generate" {{ @request()->status == 'generate' ? 'selected' : ''  }}> @lang('Generate') </option>
				<option
					value="pending" {{ @request()->status == 'pending' ? 'selected' : ''  }}> @lang('Pending') </option>
				<option
					value="processing" {{ @request()->status == 'processing' ? 'selected' : ''  }}> @lang('Processing') </option>
				<option
					value="payment_completed" {{ @request()->status == 'payment_completed' ? 'selected' : ''  }}> @lang('Payment Completed') </option>
				<option
					value="bill_completed" {{ @request()->status == 'bill_completed' ? 'selected' : ''  }}> @lang('Bill Completed') </option>
				<option
					value="bill_return" {{ @request()->status == 'bill_return' ? 'selected' : ''  }}> @lang('Bill Return') </option>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<input placeholder="@lang('Transaction Date')" name="created_at" id="created_at"
				   value="{{ $search['created_at'] ?? '' }}" type="date" class="form-control form-control-sm"
				   autocomplete="off">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-sm btn-block">@lang('Search')</button>
		</div>
	</div>
</div>
