@extends($theme.'layouts.app')
@section('title',trans('Feature'))

@section('content')
	@include($theme.'sections.feature')
	@include($theme.'sections.app-section')
	@include($theme.'sections.newsletter')
@endsection
