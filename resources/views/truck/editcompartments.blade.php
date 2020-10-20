@extends('app')


@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="grey-font">Edit Truck Compartment <strong>{{ $truck->name }}</strong>
            <span class="pull-right">
            	<a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a>
            </span>
            </h3>
            <p class="text-info">Update information for this compartment.</p>
            <h4 class="grey-font">Total Truck Capacity: <span class="totalCapacity">{{$truck->capacity}} </span>Ton/s</h4>
            <h4 class="grey-font">Total Compartment Capacity: <span class="totalCapacity">{{$compartments_capacity}} </span>Ton/s</h4>
            <p class="text-danger"><strong>Note:</strong> The compartment capacity should not be more than the total truck capacity.</p>
        	@include('errors.list')
        </div>
        <div class="panel-body">
        <!-- Form Model Binding-->
        {!! Form::open(['method' => 'POST', 'url' => 'compartment/update', 'class' => 'form-horizontal']) !!}
		{!! Form::hidden('truck_id', $truck->truck_id) !!}
        {!! Form::hidden('compartment_id', $compartment->compartment_id) !!}
        
        <div class="row">
        	<div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('CompartmentNumber', 'Compartment Number: ', array('class'=>"col-md-4 control-label grey-font")) !!}
                    <div class="col-md-6">
                    {!! Form::text('compartment_number', $compartment->compartment_number, ['class' => 'input-sm form-control','placeholder'=>'Enter name of the farm', 'disabled']) !!}
                	</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('capacity', 'Capacity: ', array('class'=>"col-md-4 control-label grey-font")) !!}
                    <div class="col-md-6">
                    {!! Form::select('capacity',  $capacity, null, ['class' => 'form-control tag_list input-sm']) !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row" style="border-top: 1px solid #DDD;">
        	<div class="col-md-2 col-md-offset-9 adjust-top">
                <div class="form-group">
                    {!! Form::submit('Update Compartment', ['class' => 'btn btn-xs btn-primary form-control add-farm']) !!}
                </div>
            </div>
        </div>

        {!! Form::close() !!}
         
		</div>
    </div>    
</div>
@stop