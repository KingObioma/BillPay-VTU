@extends('admin.layouts.master')
@section('page_title', __('Edit Template'))

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Edit Template')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('Edit Template')</div>
			</div>
		</div>

		<div class="card card-primary shadow m-0 m-md-4 my-4 m-md-0">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover align-items-center table-borderless">
						<thead class="thead-light">
						<tr>
							<th> @lang('SHORTCODE') </th>
							<th> @lang('DESCRIPTION') </th>
						</tr>
						</thead>
						<tbody>
						@if($emailTemplate->short_keys)
							@foreach($emailTemplate->short_keys as $key=> $value)
								<tr>
									<td>
										<pre>[[@lang($key)]]</pre>
									</td>
									<td>@lang($value)</td>
								</tr>
							@endforeach
						@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="card card-primary shadow m-0 m-md-4 my-4 m-md-0">
			<div class="card-body">
				<ul class="nav nav-tabs mb-3">
					@foreach($mailTemplates as $key=>$value)
						<li class="nav-item">
							<a href="#tab-{{$value->id}}" data-toggle="tab" aria-expanded="{{($key == 0) ? 'true':'false'}}"
							   class="nav-link {{($key == 0) ? 'active':''}}">
								<i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
								<span class="d-none d-lg-block">{{ __(optional($value->language)->name) }}</span>
							</a>
						</li>
					@endforeach
				</ul>
				<div class="tab-content">
					@foreach($mailTemplates as $key=>$value)
						<div class="tab-pane {{($key == 0) ? 'show active' : ''}}" id="tab-{{$value->id}}">
							<h3 class="card-title my-3">{{trans('Mail in')}}  {{ __(optional($value->language)->name) }}
								: {{ __($value->name) }}</h3>
							<form action="{{ route('email.template.update',$value->id) }}" method="POST"
								  class="mt-4">
								@csrf
								<div class="row justify-content-between">
									<div class="col-md-4">
										<div class="form-group">
											<label for="name">@lang('Subject')</label>
											<input type="text" name="subject" value="{{ $value->subject }}"
												   class="form-control @error('subject') is-invalid @enderror">
											<div class="invalid-feedback">@error('subject') @lang($message) @enderror</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="name">@lang('Mail from')</label>
											<input type="email" name="email_from" value="{{ $value->email_from }}"
												   class="form-control @error('email_from') is-invalid @enderror">
											<div class="invalid-feedback">@error('email_from') @lang($message)@enderror
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>@lang('Status')</label>
											<div class="selectgroup w-100">
												<label class="selectgroup-item">
													<input type="radio" name="mail_status" value="0"
														   class="selectgroup-input" {{ old('mail_status', $value->mail_status) == 0 ? 'checked' : ''}}>
													<span class="selectgroup-button">@lang('OFF')</span>
												</label>
												<label class="selectgroup-item">
													<input type="radio" name="mail_status" value="1"
														   class="selectgroup-input" {{ old('mail_status', $value->mail_status) == 1 ? 'checked' : ''}}>
													<span class="selectgroup-button">@lang('ON')</span>
												</label>
											</div>
											@error('mail_status')
												<span class="text-danger" role="alert">
													<strong>{{ __($message) }}</strong>
												</span>
											@enderror
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 form-group">
										<label>@lang('Email Body')</label>
										<textarea class="form-control summernote" name="template"
												  id="summernote" rows="20">{{ $value->template }}</textarea>
										@if($errors->has('template'))
											<div class="error text-danger">@lang($errors->first('template')) </div>
										@endif
									</div>
								</div>
								<button type="submit"
										class="btn btn-sm btn-primary btn-block mt-3">@lang('Save Changes')</button>
							</form>
						</div>
					@endforeach
				</div>
			</div>
		</div>

	</section>
</div>
@endsection

@section('scripts')
    <script>
        'use strict'
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 250,
				dialogsInBody: true,
                callbacks: {
                    onBlurCodeview: function() {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });
        });
    </script>
@endsection
