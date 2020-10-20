@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-primary adjust-top">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-calendar fa-fw"></i> Scheduling
            	<span class="pull-right"><a href="/scheduling" class="btn btn-xs btn-warning">Change Delivery Date</a></span>
            </h1>
        </div>
        <div class="panel-body" style="background:#bce8f1;">
        <div class="alert alert-success" role="alert">Add Farms for Deliveries<br/>
        	This is your selected delivery date: <strong>{{$delivery_datetime}}</strong><br/>
            note: Drag and Drop the farms to the added farms box.
        </div>
            @include('errors.list')
            @include('scheduling.forms.farms')
        </div>
    </div>  
</div>

@stop