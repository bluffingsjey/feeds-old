@extends('app')

@section('content')
<div class="col-md-12">	
    <h3 class="text-left" style="color: #00A3FF; margin-top:0px;">Schedule Load</h3>
    <h4 class="text-left schedule-steps">Step 2: Select Farms</h4>
    <hr style="border-color:#C9C1C1;"/>
    <div class="alert alert-info" role="alert">
    	<strong>Hi! {{session()->get('dateS')}}</strong> Please drag and drop the farms from <strong>Farms List</strong> to the <strong>Selected Farms</strong> box.
    </div>
    
	        <div class="row">
            	<div class="col-md-12">
                    <div class="col-md-6">
                        <h5 class="text-center farms-lists-title">Farms List</h5>
                        <div class="col-md-12 schedule-farms-lists"  id="left-farm">
                        @foreach ($farms as $farm)
                            <div class="col-md-6" style="padding-right: 1px; padding-left: 1px;">
                                <div class="thumbnail img-circle farms-lists-items">
                                    <div class="caption" style="color:#FFF;">
                                       <h4>{{$farm->name}}</h4>
                                       <p style="font-size: 12px;">{{$farm->address}}</p>
                                       <input type="hidden" name="farm_{{$farm->id}}" value="{{$farm->id}}"/>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
     {!! Form::open(array('url'=>'scheduling/step3','method'=>'POST', 'id'=>'myFarmForm')) !!}               
                    <div class="col-md-6">
                        <h5 class="text-center farms-lists-title">Selected Farms</h5>
                        <div class="col-md-12 schedule-farms-lists" id="right-farm">
                        </div>
                    </div>
                </div>
            </div> 
            
            <!--<div class="form-group">
              {!! Form::label('dateSched','Date Scheduled: ') !!}
              {!! Form::text('dateSched', $schedDate, ['class' => 'form-control']) !!}  
            </div>-->
        <div class="row" style="margin-top: 10px;">   
            <div class="col-md-2">   
                <a href="step1" class="btn btn-primary form-control">Back</a>
            </div>
            <div class="col-md-2 col-md-offset-8">
                {!! Form::submit('Next', ['class' => 'btn btn-primary form-control', 'id' => 'btn-step2']) !!}
            </div>
        </div>
        
	{!! Form::close() !!}
        
    @include('errors.list')
</div>
@stop