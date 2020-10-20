@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="grey-font">Enter Capacity Per Truck Compartment
            	<span class="pull-right">
                	<a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a>
                </span>
            </h3>
            <h4 class="grey-font">Total Truck Capacity: <span class="totalCapacity">{{ $capacity }} Ton/s</span></h4>
            <p class="text-danger"><strong>Note:</strong> The total compartments capacity should be equal to the trucks capacity and the compartments should not be equal to zero(0).</p>
        </div>
        <div class="panel-body">
            {!! Form::open(['url' => 'truck/storeCompartments']) !!}
                {!! Form::hidden('compartmentTotal',$compartments) !!}
                {!! Form::hidden('truckId',$truck_id) !!}
                {!! Form::hidden('truck_capacity', $capacity) !!}
                <div class="form-horizontal">
                    <div class="row">
                        @for ($i = 1; $i <= $compartments; $i++)
                        <div class="col-md-6">	
                            <div class="form-group">
                                <label for="inputCompartment{{$i}}" class="col-md-4 control-label grey-font">Compartment # {{ $i }}</label>
                                <div class="col-md-6">
                                    {!! Form::select('compartment_'.$i,  $capacity_list, null, ['id' => 'com_'.$i,'class' => 'form-control tag_list input-sm']) !!}
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                    <div class="row" style="border-top: 1px solid #DDD;">
                        <div class="col-md-2 col-md-offset-9 adjust-top">
                                {!! Form::button('Save', ['class' => 'btn btn-xs btn-success form-control btn-addcom']) !!}
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            
            @include('errors.list')
        </div>
    </div>
</div>

<!--Modal for validation-->
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farms</h4>
      </div>
      <div class="modal-body">
      <p>Truck Capacity : {{$capacity}} Tons <br/>
      Total Compartments Capacity : <span class="comTotalCap"></span> Tons<br/><br/>
      </p>
      <p class="text-danger comMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    
@stop
