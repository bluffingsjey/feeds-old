@extends('app')


@section('content')


	<h1>Create Farms Page</h1>

	<hr/>

	{!! Form::open(['url' => 'users']) !!}
		
		@include('usersinfo.form',['submitButtonText' => 'Update Info'])

	{!! Form::close() !!}
        
    @include('errors.list')
   
@stop