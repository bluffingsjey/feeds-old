@extends('app')


@section('content')
	<h1>Edit: {!! $farms->title !!}</h1>
    <hr/>
    
    <!-- Form Model Binding-->
    {!! Form::model($farms,['method' => 'PATCH','action' => ['FarmsController@update',$farms->id]]) !!}
		
		@include('farms.form',['submitButtonText' => 'Update Farms'])

	{!! Form::close() !!}
    
    @include('errors.list')
    
@stop