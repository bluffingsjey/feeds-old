@extends('app')


@section('content')
<div class="col-md-12">    
    <h3 class="text-left" style="color: #00A3FF;">Schedule Load</h3>
    <h4 class="text-left schedule-steps">Step 5</h4>
    <hr style="border-color:#C9C1C1;"/>
    
    <h4 class="text-center">Assign Amount of Feed on Compartment</h4>
  
		<div class="col-lg-4 col-lg-offset-4">			
            <div class="form-group">
                {!! Form::select('truck', $trucks, null, ['class' => 'assign-list form-control','autocomplete' => 'off']) !!}
            </div>
            
            <!-- Add Article Form Input-->
            <div class="form-group">
            	<a href="step3" class="btn btn-primary form-control">Back</a>
                <a href="finish" id="btn-step5" class="btn btn-primary form-control">Next</a>
            </div>
        </div>
        
    @include('errors.list')
</div>   
@stop