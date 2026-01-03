@extends('admin.layouts.master')
@section('page_title',__('Edit').' '.__(menuFormater($section)))

@push('extra_styles')
	<link href="{{ asset('assets/dashboard/css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
	<div class="main-content">
		<section class="section">
			<div class="section-header">
				<h1>@lang('Edit') @lang(menuFormater($section))</h1>
				<div class="section-header-breadcrumb">
					<div class="breadcrumb-item active">
						<a href="{{ route('admin.home') }}">@lang('Dashboard')</a>
					</div>
					<div class="breadcrumb-item">@lang('Edit') @lang(menuFormater($section))</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="container-fluid" id="container-wrapper">
					<div class="row justify-content-md-center">
						<div class="col-lg-12">
							<div class="card mb-4 card-primary shadow">
								<div
									class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">@lang('Edit') @lang(menuFormater($section))</h6>

								</div>
								<div class="card-body">
									<ul class="nav nav-tabs" id="myTab" role="tablist">
										@foreach($languages as $key => $language)
											<li class="nav-item">
												<a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
												   href="#lang-tab-{{ $key }}" role="tab"
												   aria-controls="lang-tab-{{ $key }}"
												   aria-selected="{{ $loop->first ? 'true' : 'false' }}">@lang($language->name)</a>
											</li>
										@endforeach
									</ul>

									<div class="tab-content mt-2" id="myTabContent">
										@foreach($languages as $key => $language)
											<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
												 id="lang-tab-{{ $key }}" role="tabpanel">
												<form method="post"
													  action="{{ route('template.update', [$section,$language->id]) }}"
													  enctype="multipart/form-data">
													@csrf
													@method('put')
													<div class="row">
														@foreach(config("templates.$section.field_name") as $name => $type)
															@if($type == 'text')
																<div class="col-md-12">
																	<div class="form-group">
																		<label
																			for="{{ $name }}"> @lang(menuFormater($name)) </label>
																		<input type="{{ $type }}"
																			   name="{{ $name }}[{{ $language->id }}]"
																			   class="form-control @error($name.'.'.$language->id) is-invalid @enderror"
																			   value="{{ old($name.'.'.$language->id, isset($templates[$language->id]) ? optional($templates[$language->id][0]->description)->{$name} : '') }}">
																		<div class="invalid-feedback">
																			@error($name.'.'.$language->id) @lang($message)
																			@enderror
																		</div>
																		<div class="valid-feedback"></div>
																	</div>
																</div>
															@elseif($type == 'file' && $key == 0)
																<div class="col-md-3">
																	<div class="form-group mb-4">
																		<label
																			class="col-form-label">@lang(menuFormater($name))</label>
																		<div id="image-preview-{{ $name }}"
																			 class="image-preview"
																			 style="background-image: url({{getFile(@$templateMedia->driver,(isset($templateMedia->description->{$name}) ? $templateMedia->description->{$name} : 0))}});">
																			<label for="image-upload-{{ $name }}"
																				   id="image-label-{{ $name }}"
																				   class="image-label">@lang('Choose File')</label>
																			<input type="file"
																				   name="{{ $name }}[{{ $language->id }}]"
																				   class="@error($name.'.'.$language->id) is-invalid @enderror image-upload"
																				   id="image-upload-{{ $name }}"/>
																		</div>
																		<div class="invalid-feedback">
																			@error($name.'.'.$language->id) @lang($message)
																			@enderror
																		</div>
																	</div>
																</div>
															@elseif($type == 'video' && $key == 0)
																<div class="row d-flex justify-content-start ml-2">
																	<div class="col-md-6">
																		<div class="form-group">
																			<video width="198" autoplay
																				   controls="false">
																				<source
																					src="{{getFile(@$templateMedia->driver,(isset($templateMedia->description->{$name}) ? $templateMedia->description->{$name} : 0))}}"
																					type="video/mp4"/>
																			</video>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label
																				class="col-form-label">@lang(menuFormater($name))</label>
																			<div id="image-preview"
																				 class="image-preview"
																				 style="background-image: url({{getFile(@$templateMedia->driver,(isset($templateMedia->description->{$name}) ? $templateMedia->description->{$name} : 0))}});">
																				<label for="image-upload"
																					   id="image-label">@lang('Choose File')</label>
																				<input type="file"
																					   name="{{ $name }}[{{ $language->id }}]"
																					   class="@error($name.'.'.$language->id) is-invalid @enderror"
																				/>
																			</div>
																			<div class="invalid-feedback">
																				@error($name.'.'.$language->id) @lang($message)
																				@enderror
																			</div>
																		</div>
																	</div>
																</div>
															@elseif($type == 'url' && $key == 0)
																<div class="col-md-12">
																	<div class="form-group">
																		<label
																			for="{{ $name }}"> @lang(menuFormater($name)) </label>
																		<input type="{{ $type }}"
																			   name="{{ $name }}[{{ $language->id }}]"
																			   class="form-control @error($name.'.'.$language->id) is-invalid @enderror"
																			   value="{{ old($name.'.'.$language->id, isset($templateMedia->description->{$name}) ? $templateMedia->description->{$name} : '') }}">
																		<div class="invalid-feedback">
																			@error($name.'.'.$language->id) @lang($message)
																			@enderror
																		</div>
																		<div class="valid-feedback"></div>
																	</div>
																</div>
															@elseif($type == 'textarea')
																<div class="col-md-12">
																	<div class="form-group">
																		<label
																			for="{{ $name }}">@lang(menuFormater($name))</label>
																		<textarea
																			class="form-control summernote @error($name.'.'.$language->id) is-invalid @enderror"
																			name="{{ $name }}[{{ $language->id }}]"
																			rows="5">{{ old($name.'.'.$language->id, isset($templates[$language->id]) ? $templates[$language->id][0]->description->{$name} : '') }}</textarea>
																		<div class="invalid-feedback">
																			@error($name.'.'.$language->id) @lang($message)
																			@enderror
																		</div>
																	</div>
																</div>
															@endif
														@endforeach
													</div>
													<button type="submit"
															class="btn btn-primary btn-sm btn-block">@lang('Save Change')</button>
												</form>
											</div>
										@endforeach
									</div>
								</div>
							</div>
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
	<script type="text/javascript">
		'use strict';
		$(document).ready(function () {
			$('input[type="file"]').each(function (key, selector) {
				const attrId = $(selector).attr('id');
				const attrLabelId = $(selector).siblings('label').attr('id');
				const attrPreviewId = $(selector).parent().attr('id');
				$.uploadPreview({
					input_field: `#${attrId}`,
					preview_box: `#${attrPreviewId}`,
					label_field: `#${attrLabelId}`,
					label_default: "Choose File",
					label_selected: "Change File",
					no_label: false
				});
			});

		});
	</script>
@endsection
