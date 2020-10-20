@extends('app')

@section('content')
<div class="col-md-12">
	<h3 class="text-left" style="color: #00A3FF;">Schedule Load</h3>
    <h4 class="text-left schedule-steps">Step 1: Pick a Delivery Date</h4>
    <hr style="border-color:#C9C1C1;"/>
    
    {!! Form::open(array('url'=>'scheduling/step2','method'=>'POST', 'id'=>'myform', 'class' => 'form-horizontal')) !!}
		<div class="col-lg-4 col-lg-offset-4">
            <div class="form-group">
            	<label for="inputEmail3" class="col-sm-4 control-label">Delivery Date</label>
                <div class="col-sm-8">
                	{!! Form::text('datepicker', null, ['id' => 'datepicker','class' => 'form-control','autocomplete' => 'off']) !!}
                </div>
            </div>
            
        	<!-- Add Article Form Input-->
            <div class="form-group">
                <!--<a href="step2" id="btn-step1" class="btn btn-primary form-control">Next</a>-->
                {!! Form::submit('Next', ['class' => 'btn btn-primary form-control', 'id' => 'btn-step1']) !!}
            </div>
        </div>

	{!! Form::close() !!}
        
    @include('errors.list')
</div>
@stop

@section('footer')

@endsection    