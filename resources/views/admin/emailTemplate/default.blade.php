@extends('admin.layouts.master')
@section('page_title', __('Edit Email Template'))

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1>@lang('Edit Email Template')</h1>
			<div class="section-header-breadcrumb">
				<div class="breadcrumb-item active">
					<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
				</div>
				<div class="breadcrumb-item">@lang('Edit Email Template')</div>
			</div>
		</div>


		<div class="section-body">
			<div class="row mt-sm-4">
				<div class="col-12 col-md-4 col-lg-3">
					@include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.email'), 'suffix' => ''])
				</div>
				<div class="col-12 col-md-8 col-lg-9">
					<div class="container-fluid" id="container-wrapper">
						<div class="card card-primary shadow">
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-hover align-items-center table-borderless">
										<thead class="thead-light">
										<tr>
											<th> @lang('SHORTCODE') </th>
											<th> @lang('DESCRIPTION') </th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td>
												<pre>@lang('[[name]]')</pre>
											</td>
											<td> @lang("User's Name will replace here.") </td>
										</tr>
										<tr>
											<td>
												<pre>@lang('[[message]]')</pre>
											</td>
											<td>@lang("Application notification message will replace here.")</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="card card-primary shadow">
							<div class="card-body">
								<form action="{{ route('email.template.default') }}" method="POST">
									@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="form-group ">
												<label>@lang('From Email')</label>
												<input type="text" name="sender_email" class="form-control"
													placeholder="@lang('Enter default form email address')"
													value="{{ $basicControl->sender_email }}">
												@error('sender_email')<span class="text-danger">@lang($message)</span>@enderror
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group ">
												<label>@lang('From Email Name')</label>
												<input type="text" name="sender_email_name" class="form-control"
													placeholder="@lang('Enter default form email name')"
													value="{{ $basicControl->sender_email_name }}">
												@error('sender_email_name')<span class="text-danger">@lang($message)</span>@enderror
											</div>
										</div>
									</div>
									<div class="form-group ">
										<label>@lang('Email Description')</label>
										<textarea class="form-control" name="email_description" id="summernote"
												placeholder="@lang('Enter default form email template')"
												rows="20">{{$basicControl->email_description}}</textarea>
									</div>
									<button type="submit"
											class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">@lang('Save Changes')</button>
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

@section('scripts')
    <script>
        "use strict";
        $(document).ready(function () {
            $('#summernote').summernote({
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
