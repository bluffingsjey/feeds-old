@extends('app')


@section('content')
	<h1>Edit Bin</h1>
    <hr/>
    
    <!-- Form Model Binding-->
    {!! Form::model($bins,['method' => 'PATCH','action' => ['BinsController@update',$bins->bin_id]]) !!}
		
		@include('bins.form',['submitButtonText' => 'Update Bin'])

	{!! Form::close() !!}
    
    @include('errors.list')
    
@stop