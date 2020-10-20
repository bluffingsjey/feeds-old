@extends('app')


@section('content')
	<h1>Edit Bins Category</h1>
    <hr/>
    
    <!-- Form Model Binding-->
    {!! Form::model($binscat,['method' => 'PATCH','action' => ['BinCatController@update',$binscat->id]]) !!}
		
		@include('bincat.form',['submitButtonText' => 'Update Bins Category'])

	{!! Form::close() !!}
    
    @include('errors.list')
    
@stop