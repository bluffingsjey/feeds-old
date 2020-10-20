@extends('app')
@section('content')
<div class="col-md-10">
<div class="col-md-6">
 <div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><i class="fa fa-calendar fa-fw"></i> Load Items</h1>
    </div>
    <div class="panel-body" id="bin-items">
        <div class="row">
            <div class="col-md-2"><span class="col-md-12"><strong>Farm</strong></span></div>
            <div class="col-md-2"><span class="col-md-12"><strong>Batch</strong></span></div>
            <div class="col-md-2"><span class="col-md-12"><strong>Feed Type</strong></span></div>
            <div class="col-md-2"><span class="col-md-12"><strong>Amount</strong></span></div>
            <div class="col-md-2"><span class="col-md-12"><strong>Bin(s)</strong></span></div>
            <div class="col-md-2 text-left"><strong>Color</strong></span></div>
        </div>
        @forelse($schedData as $k => $d)
        <div class="row load-table drag-item">
        	{!! Form::hidden('batch_'.$k,$d->batch,['class'=>'batch_'.$k]) !!}
        	{!!	Form::hidden('truck_id_'.$k,$truck_id,['class'=>'truck_id_'.$k])!!}
            {!! Form::hidden('farmId_'.$k,$d->farmId,['class'=>'farmId_'.$k]) !!}
            {!! Form::hidden('feedType_'.$k,$d->feedType,['class'=>'feedType_'.$k]) !!}
            {!! Form::hidden('medId_'.$k,$d->medId,['class'=>'medId_'.$k]) !!}
            {!! Form::hidden('amount_'.$k,$d->amount,['class'=>'amount_'.$k]) !!}
            {!! Form::hidden('bins_id_'.$k,$d->bins,['class'=>'bins_id_'.$k]) !!}
            <div class="col-md-2"><span class="col-md-12 small">{{$d->farmName}}</span></div>
            <div class="col-md-2"><span class="col-md-12 small">{{$d->batch}}</span></div>
            <div class="col-md-2"><span class="col-md-12 small">{{$d->feedName}}</span></div>
            <div class="col-md-2"><span class="col-md-12 small">{{$d->amount}}</span></div>
            <div class="col-md-2"><span class="col-md-12 small">{{$d->bins_number}}</span></div>
            <div class="col-md-2"><span class="col-md-12" style="background:{{$ctrl->getBinsColor($d->bins)}}; height: 20px; border: 1px solid #DDD;"></span></div>
        </div>
        @empty
        <div class="row load-table">
            <div class="col-md-2">No schedule yet</div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
        </div>
        @endforelse
        
    </div>
 </div>
</div>

<div class="col-md-6"> 
{!! Form::open(['action'=>'ScheduleController@saveCompartmentSelection','id'=>'loadTruckForm']) !!}
	{!! Form::hidden('schedule_id',$schedule_id) !!}
    {!! Form::hidden('truck_driver',$truck_driver) !!}
 <div class="panel panel-primary">
 	<div class="panel-heading">
    	<h1 class="panel-title">Truck Compartments</h1>
    </div>
    <div class="panel-body">
    @forelse($compartments as $compartment)
    	<div class="row load-table compartments" style="border: 1px solid #aaaaaa;">
            <div class="col-md-12" id="compartments{{$compartment->compartment_id}}">	
            {!! Form::hidden('compartment_'.$compartment->compartment_number,$compartment->compartment_number,['class'=>'compartment_'.$compartment->compartment_number]) !!}
			{{$compartment->compartment_number}}</div>
        </div>
    @empty    
        <div class="row load-table">
            <div class="col-md-12">No Compartments</div>
        </div>
    @endforelse
    </div>
 </div>
 
 <div class="row" style="margin-top:10px; margin-bottom:10px; display:none;">
     <div class="col-md-12">
        <div class="col-md-2 truck-front" style="margin-right:10px; z-index:1;">
            <img src="{{ asset('images/truck-front.png') }}" style="z-index:1"/>
        </div>
        @forelse($compartments as $compartment)
            <div class="col-md-1" style="border: 5px solid #aaaaaa; height: 115px; margin-left: 5px; border-radius: 2px; z-index:1;">
                {{$compartment->compartment_number}}
            </div>
        @empty    
            <div class="col-md-2">
                No Compartments
            </div>
        @endforelse	 
        <div class=" col-md-2 truck-back">
            <img src="{{ asset('images/truck-back.png') }}" style="z-index:0; margin-left: -360px;"/>
        </div>
     </div>   
 </div>
</div>

</div>

<div class="row">
    <button type="button" class="btn-save-compartment btn-lg btn-success pull-right">Load Truck</button>
</div>

<!-- Modal -->
<div class="modal fade" id="loadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farms</h4>
      </div>
      <div class="modal-body">
        Please attach all the load items
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{!! Form::close() !!}

<script type="text/javascript">
$(document).ready(function() {
	
	dragula([$('right-defaults'),
	@forelse($compartments as $compartment)
	$('compartments{{$compartment->compartment_id}}'),
	@empty
	@endforelse
	$('bin-items')])
	
		
	function $ (id) {
	  return document.getElementById(id);
	}
	
});

$('.btn-save-compartment').click(function(){
jQuery.ajaxSettings.traditional = true;

// #bin-items
var input = $('#bin-items').find(":input").length;
if(input == 0){

var truck_compartments = [];
var bins_holder = [];
var compartments_holder = [];	
var inputs = [];

var compartment_numbers	= "";
// make all bins getter
@forelse($compartments as $compartment)

console.log($('#compartments{{$compartment->compartment_id}}').find(":hidden").length);

	if(($('#compartments{{$compartment->compartment_id}}').find(":hidden").length) == 1) {
		$('#compartments{{$compartment->compartment_id}}').find(":hidden").remove();
	}
	
	if(($('#compartments{{$compartment->compartment_id}}').find(":hidden").length) > 1) {
		$('#compartments{{$compartment->compartment_id}}').find(":hidden").each(function(){
			var input_names = $(this).attr("name");
			var input_values = $('.'+input_names).val();
			var compartment_numbers = {{$compartment->compartment_id}};
			//console.log(compartment_numbers);
			//console.log({{$compartment->compartment_id}}+'_'+ $(this).attr("name"));
			//inputs[input_names] = input_values;
			
			
				bins_holder.push({ [input_names] : input_values+'_'+compartment_numbers});
						
		});
	}
@empty
	
@endforelse	

console.log(JSON.stringify(bins_holder));
		

	$.ajax({
			url: app_url + '/loading/saveCompartmentSelection',
			type: 'POST',
			dataType: "json",
			data:{'data':JSON.stringify(bins_holder)},
			success: function(c){
				
			}				
		})
$("#loadTruckForm").submit();
} else {
	
	// error
	$("#loadModal").modal();
	
}



})	


</script>
@stop