@extends('app')
@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
			<h1 class="panel-title">Edit Medication
            	<span class="pull-right"><a href="/medication" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Medication</a></span>
            </h1>
        </div>
        <div class="panel-body">    
   			 @include('errors.list')
            <!-- Form Model Binding-->
            {!! Form::model($medication,['method' => 'PATCH','action' => ['MedicationController@update',$medication->med_id]]) !!}
                @include('medication.form',['submitButtonText' => 'Update Bin Type'])
            {!! Form::close() !!}
    	</div>
    </div>    
</div>    
@stop