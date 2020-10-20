<div class="row">
    <div class="col-md-6">	
        <div class="form-group">
        	{!! Form::hidden('truck_id')!!}
            {!! Form::label('name', 'Name: ', array("class" => "col-md-4 control-label grey-font")) !!}
            <div class="col-md-6">
            {!! Form::text('name', null, ['class' => 'input-sm form-control','autocomplete' => 'off']) !!}
        	</div>
        </div>
    </div>
    
    <div class="col-md-6">	
        <div class="form-group">
            {!! Form::label('capacity', 'Capacity: ', array('class'=>"col-md-4 control-label grey-font")) !!}
            <div class="col-md-6">
            {!! Form::select('capacity', (isset($capacity) ? $capacity : NULL), null, ['class' => 'form-control tag_list input-sm']) !!}
        	</div>
        </div>
    </div>
</div>

<div class="row" style="border-top: 1px solid #DDD;">
    <div class="col-md-2 col-md-offset-9 adjust-top">
        <div class="form-group">
            {!! Form::submit($submitButtonText, ['class' => 'btn btn-xs btn-success form-control']) !!}
        </div>
    </div>
</div>
