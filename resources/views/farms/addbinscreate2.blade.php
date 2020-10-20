@extends('app')

@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title" style="margin-top:0px;">ENTER INFO PER BINS 
            <span class="pull-right"><a href="/farms" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Farms</a></span>
            </h1>
        </div>
    	<div class="panel-body">    
        <p class="text-info">
            <strong>Note:</strong> Only numbers are accepted on <strong>Number of Pigs</strong> and <strong>Consumption</strong> fields.
            Please ensure that all fields of bins are filled before saving.
            @include('errors.list')
        </p>
        
    	@include('farms.binsform2')
    	</div>
    </div>    
</div>
@stop