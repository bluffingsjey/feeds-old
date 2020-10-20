@extends('app')
@section('content')
<div class="col-md-10">
<div class="panel panel-info">
    <div class="panel-heading">
		<h1 class="panel-title">Create Medication
        	<span class="pull-right">
            	<a href="/medications" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Medications</a>
            </span>
        </h1>
	</div>
    <div class="panel-body">
	@include('errors.list')
    {!! Form::open(['url' => 'medication']) !!}
		@include('medication.form',['submitButtonText' => 'Add Medication'])
	{!! Form::close() !!}
	</div>        
</div>
</div>   
@stop