@extends('app')
@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Create Truck <span class="pull-right"><a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a></span></h3>
        </div>
        <div class="panel-body">
        	<p class="text-info">Enter information for this truck.</p>
        	@include('errors.list')
            {!! Form::open(['url' => 'truck', 'class' => 'form-horizontal']) !!}
                @include('truck.form',['submitButtonText' => 'Add Truck'])
            {!! Form::close() !!}
        </div>
     </div>   
</div>   
@stop