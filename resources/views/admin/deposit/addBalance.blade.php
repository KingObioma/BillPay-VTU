@extends('admin.layouts.master')
@section('page_title', __('Add Balance'))

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Add Balance')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('Add Balance')</div>
			</div>
		</div>

		<div class="row mb-3">
			<div class="container-fluid" id="container-wrapper">
				<div class="row justify-content-md-center">
					<div class="col-lg-12">
						<div class="card mb-4 card-primary shadow">
							<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
								<h6 class="m-0 font-weight-bold text-primary">@lang('Add Balance - '.$user->name)</h6>
								<a href="{{ route('user.edit',$user->id) }}" class="btn btn-sm btn-outline-primary"> <i class="fas fa-arrow-left"></i> @lang('Back')</a>
							</div>
							<div class="card-body">
								<form action="{{ route('admin.user.add.balance',$user->id) }}" method="post">
									@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="form-group search-currency-dropdown">
												<label for="currency">@lang('Currency')</label>
												<select id="currency" name="currency" class="form-control form-control-sm">
													@foreach($currencies as $key => $currency)
														<option value="{{ $currency->id }}" {{ (old('currency') == $currency->id) ? 'selected' : '' }}>{{ $currency->code }} @lang('-') {{ $currency->name }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="amount">@lang('Amount')</label>
												<input type="text" id="amount" value="{{ old('amount') }}" name="amount" placeholder="@lang('0.00')"
													   class="form-control @error('amount') is-invalid @enderror" autocomplete="off">
												<div class="invalid-feedback">
													@error('amount') @lang($message) @enderror
												</div>
												<div class="valid-feedback"></div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="note">@lang('Note')</label>
												<textarea name="note" rows="5" class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
												<div class="invalid-feedback">
													@error('note') @lang($message) @enderror
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<input type="submit" id="submit" class="btn btn-primary btn-sm btn-block" value="@lang('Send Money')">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>
</div>
@endsection
