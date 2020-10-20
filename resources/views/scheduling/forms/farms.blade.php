<div class="col-sm-12">
<div class="row">
    <div  class="col-md-6">
    	<div class="alert alert-info" role="alert">Farms List</div>
        <div class="col-md-6" id="left-defaults" style="width:100%; min-height:200px; background:#000000; padding-top: 20px; border-radius:5px;">
            @forelse($farms as $farm)
            <div class="col-md-6" style="cursor:move; min-height:160px;">
                <div class="well well-lg" style="cursor:move; min-height:210px;">
                <strong>{{$farm->name}}</strong>
                <p class="text-info">{{$ctrl->pigsSum($farm->id)}} Pigs - {{$ctrl->binsCount($farm->id)}} Bins</p>
                <p class="text-info">{{$farm->address}}</p>
                <input type="hidden" name="farm_{{$farm->id}}" value="{{$farm->id}}"/>
                </div>
            </div>
            
            @empty
            
            @endforelse
        </div>
    </div>
    <div class="col-md-6">
    	<div class="alert alert-info" role="alert">Added Farms</div>
    	{!! Form::open(['url' => 'finalizesched']) !!}
        {!! Form::hidden('delivery_datetime', $delivery_datetime) !!}
        {!! Form::hidden('delivery_truck', $delivery_truck) !!}
        {!!	Form::hidden('date_string',$date_string) !!}
        <div class="col-md-6" id="right-defaults" style="width:100%; min-height:200px;  padding-top: 20px; background:#FFFFFF; border-radius:5px;"></div>
   		
    </div>  
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
      <button type="submit" class="btn btn-success btn-block">Next</button>
    </div>  
</div>  
{!! Form::close() !!}        
</div>


<script type="text/javascript">
dragula([$('left-defaults'), $('right-defaults')]);
	
function $ (id) {
  return document.getElementById(id);
}
</script>