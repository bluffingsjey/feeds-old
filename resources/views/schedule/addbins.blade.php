@extends('app')


@section('content')
	<h1>Add Bins for {{$farm->name}} Farm</h1>
    <hr/>
    
    {!! Form::model(['url' => ['farms']]) !!}
    		{!! Form::hidden('farm_id',$farm->id) !!}
       <div class="form-group">
            {!! Form::label('num_of_pigs', 'Number of Pigs: ') !!}
            {!! Form::text('num_of_pigs', null, ['class' => 'num_of_pigs form-control','autocomplete' => 'off']) !!}
        </div>
        
        <div class="form-group">
            {!! Form::label('consumpiton', 'Consumption: ') !!}
            {!! Form::text('consumption', null, ['class' => 'consumption form-control','autocomplete' => 'off']) !!}
        </div>
        
        <div class="form-group">
            {!! Form::label('variance', 'Variance: ') !!}
            {!! Form::text('variance', null, ['class' => 'variance form-control','autocomplete' => 'off']) !!}
        </div>
        
        <div class="form-group">
            {!! Form::label('color', 'Color: ') !!}
            {!! Form::text('color[]', null, ['class' => 'color-picker form-control','autocomplete' => 'off']) !!}
        </div>
       
        <div class="form-group">
            {!! Form::button("Add Bins", ['class' => 'btn-addbins btn btn-primary form-control']) !!}
        </div>
	{!! Form::close() !!}
    @include('errors.list')
    
@stop