{!! Form::hidden('bin_id',!empty($bin_history[0]['bin_id']) ? $bin_history[0]['bin_id'] : $bin->bin_id) !!}
{!! Form::hidden('farm_id',!empty($bin_history[0]['farm_id']) ? $bin_history[0]['farm_id'] : $bin->farm_id) !!}
{!! Form::hidden('history_id',!empty($bin_history[0]['history_id']) ? $bin_history[0]['history_id'] : NULL) !!}
<div class="form-group" style="display:none">
	{!! Form::label('bin_number', 'Bin Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
    	{!! Form::text('bin_number', $bin->bin_number, ['class' => 'form-control input-sm','placeholder'=>'Enter bin of the bin']) !!}
	</div>
</div>
<div class="form-group">
    {!! Form::label('alias', 'Alias', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
    	{!! Form::text('alias', $bin->alias, ['class' => 'form-control input-sm','placeholder'=>'Enter alias of the bin']) !!}
	</div>
</div>
<div class="form-group" style="display:none">
    {!! Form::label('num_of_pigs', 'Number of Pigs', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('num_of_pigs', $bin_history[0]['num_of_pigs'], ['class' => 'form-control input-sm','placeholder'=>'Enter number of pigs of the bin']) !!}
	</div>
</div>

<div class="form-group">
    {!! Form::label('feed_type', 'Feed Types', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
    	{!! Form::select('feed_type', $feed_type, $feed_type_history, ['class' => 'form-control input-sm','placeholder'=>'Enter feed types of the bin']) !!}
	</div>
</div>
<div class="form-group">
    {!! Form::label('bin_size', 'Bin Size', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
    	{!! Form::select('bin_size', $bin_size, array($bin->bin_size,$bin_size_name), ['class' => 'form-control input-sm','placeholder'=>'Enter bin sizes of the bin']) !!}
	</div>
</div>

<!-- Form Submit button -->
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
    	{!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control add-farm']) !!}
	</div>
</div>
