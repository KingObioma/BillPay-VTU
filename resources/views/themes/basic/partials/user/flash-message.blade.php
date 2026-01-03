@if(session()->has('success'))
	<script>
		Notiflix.Notify.Success("{{ __(session()->get('success')) }}");
	</script>
@endif
@if(session()->has('alert'))
	<script>
		Notiflix.Notify.Failure("{{ __(session()->get('alert')) }}");
	</script>
@endif
