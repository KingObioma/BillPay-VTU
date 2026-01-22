@extends($theme.'layouts.app')
@section('title',trans('Home'))

@section('content')

	@include($theme.'sections.hero')
	@include($theme.'sections.feature')
    {{-- No --}}
	@include($theme.'sections.about-us')

	@include($theme.'sections.how-it-work')
    {{-- No --}}
	@include($theme.'sections.why-choose-us')
	@include($theme.'sections.testimonial')
	@include($theme.'sections.faq')


	@include($theme.'sections.blog')
    {{-- No --}}
	@include($theme.'sections.app-section')
    {{-- No buttons--}}
	@include($theme.'sections.newsletter')

@endsection
