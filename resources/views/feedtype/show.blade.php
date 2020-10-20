@extends('app')

@section('content')

	<h1>{{ $farm->name }}</h1>
	<hr>
	<article>
		{{ $farm->address }}
	</article>

@stop