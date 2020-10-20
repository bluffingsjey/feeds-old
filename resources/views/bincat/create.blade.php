@extends('app')


@section('content')


	<h1>Create Bin Category Page</h1>

	<hr/>

	{!! Form::open(['url' => 'binscat']) !!}
		
		@include('bincat.form',['submitButtonText' => 'Add Bins Category'])

	{!! Form::close() !!}
        
    @include('errors.list')
   
@stop