@extends('app')


@section('content')


	<h1>Create Feeds Type</h1>

	<hr/>

	{!! Form::open(['url' => 'binstype']) !!}
		
		@include('feedtype.form',['submitButtonText' => 'Add Feed Type'])

	{!! Form::close() !!}
        
    @include('errors.list')
   
@stop