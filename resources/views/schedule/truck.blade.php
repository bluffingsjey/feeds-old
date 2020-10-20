@extends('app')


@section('content')
<div class="col-md-12"> 	
    <h3 class="text-left" style="color: #00A3FF;">Schedule Load</h3>
    <h4 class="text-left schedule-steps">Step 4: Assign a Truck</h4>
    <hr style="border-color:#C9C1C1;"/>
    
    <h4 class="text-center">Assign a Truck</h4>
  
		<div class="col-lg-4 col-lg-offset-4">		
            <div class="form-group">
                {!! Form::select('compartment', $compartments, null, ['class' => 'form-control']) !!}
            </div>
            
            <!-- Add Article Form Input-->
            <div class="form-group">
                <a href="final" id="btn-step4" class="btn btn-primary form-control">Next</a>
            </div>
		</div>
        
    @include('errors.list')
</div>   
@stop