@extends('admin.layouts.master')
@section('page_title')
	{{ trans($page_title) }}
@endsection
@section('content')
	<div class="main-content">
		<section class="section">
			@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ trans($error) }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			<div class="section-header">
				<h1>@lang("Edit Method")</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang("Edit Method")</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card card-primary shadow">
						<div
							class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">@lang("Edit Method")</h6>
							<a href="{{ route('admin.deposit.manual.index') }}"
							   class="btn btn-sm btn-outline-primary"> <i
									class="fas fa-arrow-left"></i> @lang('Back')</a>
						</div>
						<div class="card-body">
							<form method="post" action="{{ route('admin.deposit.manual.update', $method) }}"
								  class="needs-validation base-form" novalidate="" enctype="multipart/form-data">
								@csrf
								@method('put')
								<div class="row">
									<div class="form-group col-md-4">
										<label>{{ trans('Name') }}</label>
										<input type="text" class="form-control " name="name"
											   value="{{ old('name', $method->name) }}" required="">
										@if ($errors->has('name'))
											<span class="invalid-text">
                                            {{ trans($errors->first('name')) }}
                                        </span>
										@endif
									</div>

									<div class="form-group col-md-4">
										<label>{{ trans('Currency') }}</label>
										<input type="text" class="form-control " name="currency"
											   value="{{ old('currency', $method->currency) }}" required="required">

										@if ($errors->has('currency'))
											<span class="invalid-text">
                                            {{ trans($errors->first('currency')) }}
                                        </span>
										@endif
									</div>
									<div class="form-group col-md-4">
										<label>{{ trans('Convention Rate') }}</label>
										<div class="input-group ">
											<div class="input-group-prepend">
												<div class="form-control">
													1 {{ config('basic.base_currency') ?: 'USD' }} =
												</div>
											</div>
											<input type="text" class="form-control " name="convention_rate"
												   value="{{ old('convention_rate', getAmount($method->convention_rate)) }}"
												   required>
											<div class="input-group-append">
												<div class="form-control set-currency">
													{{$method->currency}}
												</div>
											</div>
										</div>

										@if ($errors->has('convention_rate'))
											<span class="invalid-text">
                                            {{ trans($errors->first('currency_symbol')) }}
                                        </span>
										@endif
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-6 col-6">
										<label>{{ trans('Minimum Deposit Amount') }}</label>
										<div class="input-group ">
											<input type="text" class="form-control " name="minimum_deposit_amount"
												   value="{{ old('minimum_deposit_amount', getAmount($method->min_amount)) }}"
												   required="">
											<div class="input-group-append">
												<div class="form-control">
													{{ config('basic.base_currency') ?? trans('USD') }}
												</div>
											</div>
										</div>

										@if ($errors->has('minimum_deposit_amount'))
											<span class="invalid-text">
                                            {{ trans($errors->first('minimum_deposit_amount')) }}
                                        </span>
										@endif
									</div>
									<div class="form-group col-md-6 col-6">
										<label>{{ trans('Maximum Deposit Amount') }}</label>
										<div class="input-group ">
											<input type="text" class="form-control " name="maximum_deposit_amount"
												   value="{{ old('maximum_deposit_amount', getAmount($method->max_amount)) }}"
												   required="">
											<div class="input-group-append">
												<div class="form-control">
													{{ config('basic.base_currency') ?? trans('USD') }}
												</div>
											</div>
										</div>

										@if ($errors->has('maximum_deposit_amount'))
											<span class="invalid-text">
                                            {{ trans($errors->first('maximum_deposit_amount')) }}
                                        </span>
										@endif
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-6 col-6">
										<label>{{ trans('Percentage Charge') }}</label>
										<div class="input-group ">
											<input type="text" class="form-control " name="percentage_charge"
												   value="{{ old('percentage_charge', getAmount($method->percentage_charge)) }}"
												   required="">
											<div class="input-group-append">
												<div class="form-control">
													{{ trans('%') }}
												</div>
											</div>
										</div>

										@if ($errors->has('percentage_charge'))
											<span class="invalid-text">
                                            {{ trans($errors->first('percentage_charge')) }}
                                        </span>
										@endif
									</div>
									<div class="form-group col-md-6 col-6">
										<label>@lang('Fixed Charge')</label>
										<div class="input-group ">
											<input type="text" class="form-control " name="fixed_charge"
												   value="{{ old('fixed_charge', getAmount($method->fixed_charge)) }}"
												   required="">
											<div class="input-group-append">
												<div class="form-control">
													{{ $basic->currency ?? trans('USD') }}
												</div>
											</div>
										</div>

										@if ($errors->has('fixed_charge'))
											<span class="invalid-text">
                                            {{ trans($errors->first('fixed_charge')) }}
                                        </span>
										@endif
									</div>
								</div>

								<div class="row justify-content-between">
									<div class="col-md-6">
										<div class="form-group mb-4">
											<label class="col-form-label">@lang('Gateway Logo')</label>
											<div id="image-preview" class="image-preview"
												 style="background-image: url({{ getFile($method->driver,$method->image) ? : 0 }});">
												<label for="image-upload"
													   id="image-label">@lang('Choose File')</label>
												<input type="file" name="image"
													   class="@error('image') is-invalid @enderror"
													   id="image-upload"/>
											</div>
											<div class="invalid-feedback">
												@error('image') @lang($message) @enderror
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-12">
									<div class="form-group ">
										<label>@lang('Note')</label>
										<textarea class="form-control summernote" name="note" id="summernote"
												  rows="15">{{ old('note', $method->note) }}</textarea>
										@error('note')
										<span class="text-danger">{{ trans($message) }}</span>
										@enderror
									</div>
								</div>
								<div class="row mt-3 justify-content-between">
									<div class="form-group col-lg-3 col-md-6">
										<label>@lang('Status')</label>

										<div class="selectgroup w-100">
											<label class="selectgroup-item">
												<input type="radio" name="status" value="0"
													   class="selectgroup-input" {{ old('status', $method->status) == 0 ? 'checked' : ''}}>
												<span class="selectgroup-button">@lang('OFF')</span>
											</label>
											<label class="selectgroup-item">
												<input type="radio" name="status" value="1"
													   class="selectgroup-input" {{ old('status', $method->status) == 1 ? 'checked' : ''}}>
												<span class="selectgroup-button">@lang('ON')</span>
											</label>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="form-group">
											<a href="javascript:void(0)" class="btn btn-success float-right mt-3"
											   id="generate"><i class="fa fa-plus-circle"></i> {{ trans('Add Field') }}
											</a>
										</div>
									</div>
								</div>

								<div class="row addedField">
									@if ($method->parameters)
										@foreach ($method->parameters as $k => $v)
											<div class="col-md-12">
												<div class="form-group">
													<div class="input-group">

														<input name="field_name[]" class="form-control" type="text"
															   value="{{ $v->field_level }}" required
															   placeholder="{{ trans('Field Name') }}">

														<select name="type[]" class="form-control  ">
															<option value="text"
																	@if ($v->type == 'text') selected @endif>
																{{ trans('Input Text') }}</option>
															<option value="textarea"
																	@if ($v->type == 'textarea') selected @endif>
																{{ trans('Textarea') }}</option>
															<option value="file"
																	@if ($v->type == 'file') selected @endif>
																{{ trans('File upload') }}</option>
														</select>

														<select name="validation[]" class="form-control  ">
															<option value="required"
																	@if ($v->validation == 'required') selected @endif>
																{{ trans('Required') }}</option>
															<option value="nullable"
																	@if ($v->validation == 'nullable') selected @endif>
																{{ trans('Optional') }}</option>
														</select>

														<span class="input-group-btn">
                                                        <button class="btn btn-danger  delete_desc" type="button">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </span>
													</div>
												</div>
											</div>
										@endforeach
									@endif

								</div>
								<button type="submit"
										class="btn btn-rounded btn-primary btn-block mt-3">@lang('Save Changes')</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
@push('extra_scripts')
	<script src="{{ asset('assets/dashboard/js/jquery.uploadPreview.min.js') }}"></script>
@endpush

@section('scripts')
	<script>
		"use strict";
		console.log("lol")
		setCurrency();
		$(document).on('change', 'input[name="currency"]', function () {
			setCurrency();
		});

		function setCurrency() {
			let currency = $('input[name="currency"]').val();
			$('.set-currency').text(currency);
		}

		$(document).on('click', '.copy-btn', function () {
			var _this = $(this)[0];
			var copyText = $(this).parents('.input-group-append').siblings('input');
			$(copyText).prop('disabled', false);
			copyText.select();
			document.execCommand("copy");
			$(copyText).prop('disabled', true);
			$(this).text('Coppied');
			setTimeout(function () {
				$(_this).text('');
				$(_this).html('<i class="fas fa-copy"></i>');
			}, 500)
		});

		$.uploadPreview({
			input_field: "#image-upload",
			preview_box: "#image-preview",
			label_field: "#image-label",
			label_default: "Choose File",
			label_selected: "Change File",
			no_label: false
		});


		$(document).ready(function (e) {

			$("#generate").on('click', function () {
				var form = `<div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="field_name[]" class="form-control " type="text" value="" required placeholder="{{ trans('Field Name') }}">

                                        <select name="type[]"  class="form-control  ">
                                            <option value="text">{{ trans('Input Text') }}</option>
                                            <option value="textarea">{{ trans('Textarea') }}</option>
                                            <option value="file">{{ trans('File upload') }}</option>
                                        </select>

                                        <select name="validation[]"  class="form-control  ">
                                            <option value="required">{{ trans('Required') }}</option>
                                            <option value="nullable">{{ trans('Optional') }}</option>
                                        </select>

                                        <span class="input-group-btn">
                                            <button class="btn btn-danger delete_desc" type="button">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> `;

				$('.addedField').append(form)
			});


			$(document).on('click', '.delete_desc', function () {
				$(this).closest('.input-group').parent().remove();
			});

			$('.summernote').summernote({
				height: 250,
				callbacks: {
					onBlurCodeview: function () {
						let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable')
							.val();
						$(this).val(codeviewHtml);
					}
				}
			});
		});
	</script>
@endsection
