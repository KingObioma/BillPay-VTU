@extends('admin.layouts.master')
@section('page_title', __('Send Mail'))

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Send Mail')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('Send Mail')</div>
			</div>
		</div>

		<div class="row mb-3">
			<div class="container-fluid" id="container-wrapper">
				<div class="row justify-content-center">
					<div class="col-lg-12">
						<div class="card mb-4 card-primary shadow">
							<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
								<h6 class="m-0 font-weight-bold text-primary">@lang('Send mail')</h6>
							</div>
							<div class="card-body">
								<form action="{{ route('send.mail.user',$user) }}" method="POST">
									@csrf
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="name">@lang('Subject')</label>
												<input type="text" name="subject" value="{{ old('subject') }}"
													   class="form-control @error('subject') is-invalid @enderror">
												<div class="invalid-feedback">@error('subject') @lang($message) @enderror</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label>@lang('Email Body')</label>
												<textarea class="form-control summernote" name="template"
														  id="summernote" rows="20">{{ old('template') }}</textarea>
												@if($errors->has('template'))
													<div class="error text-danger">@lang($errors->first('template')) </div>
												@endif
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-sm btn-primary btn-block">
										<span>@lang('Send')</span>
									</button>
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

