@extends($theme.'layouts.app')
@section('title',trans('About Us'))

@section('content')
	@include($theme.'sections.about-us')
	@include($theme.'sections.testimonial')
	@include($theme.'sections.faq')

	@include($theme.'sections.blog')
	@include($theme.'sections.app-section')
	@include($theme.'sections.newsletter')
@endsection
