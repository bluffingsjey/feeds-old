@extends('app')


@section('content')


	<h1>Create Farms Page</h1>

	<hr/>

	{!! Form::open(['url' => 'farms']) !!}
		
		@include('farms.form',['submitButtonText' => 'Add Farm'])

	{!! Form::close() !!}
        
    @include('errors.list')
   
@stop