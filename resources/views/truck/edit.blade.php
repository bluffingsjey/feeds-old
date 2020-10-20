@extends('app')


@section('content')

<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Edit Truck
            <span class="pull-right">
            	<a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a>
            </span>
            </h3>
        </div>
        <div class="panel-body">
        	<p class="text-info">Update the info this truck.</p>
             @include('errors.list')
            <!-- Form Model Binding-->
            {!! Form::model($truck,['method' => 'PATCH','class' => 'form-horizontal','action' => ['TruckController@update',$truck->truck_id]]) !!}
                @include('truck.form',['submitButtonText' => 'Update Truck'])
            {!! Form::close() !!}
        </div>
    </div>    
</div>    
@stop