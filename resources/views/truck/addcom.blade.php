@extends('app')

@section('content')
    
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
        	<h3 class="grey-font">Add Compartment <small>Add how many compartments for specific truck</small>
            	<span class="pull-right">
                	<a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a>
                </span>
            </h3>
        	<p class="text-info"><strong>Note:</strong> Only numbers are accepted on this field.</p>
        </div>
        <div class="panel-body">
            {!! Form::open(['url' => 'truck/addcap','class' => 'form-inline']) !!}
            {!! Form::hidden('truck_id', $truck_id) !!}
            
            {!! Form::label('compartments', 'Compartment Number: ', ['class'=>'col-sm-3']) !!}
            <div class="form-group">
                {!! Form::text("compartments", null, ["class"=>"form-control input-sm numeric com_number","autocomplete"=>"off", "data-toggle"=>"tooltip", "data-placement"=>"top", "title"=>"Enter the number of compartment for this truck"]) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Next', ['class' => 'btn btn-success form-control']) !!}
            </div>
            {!! Form::close() !!}
            @include('errors.list')
        </div>	
    </div>
</div>
   
<script type="text/javascript">
$(document).ready(function(){
	$(".com_number").keyup(function(e){
		var com_number = $(this).val();
		
		// exclude 0 on keypress
		if(com_number == 0){
			alert("0 is not allowed on this field.");
			$(this).val("");
		}
	})	
})
</script>   
   
@stop