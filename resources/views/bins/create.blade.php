@extends('app')


@section('content')


<h1>Create Bins Page</h1>

	<hr/>

	{!! Form::open(['url' => 'bins']) !!}
		
		@include('bins.form',['submitButtonText' => 'Add Farm'])

	{!! Form::close() !!}
        
    @include('errors.list')


@stop