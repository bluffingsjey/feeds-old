<div class="form-group">
	{!! Form::label('username', 'Username: ',['class'=>'control-label col-sm-3']) !!}
    <div class="col-sm-6">
    {!! Form::text('username', null, ['class' => 'form-control input-sm col-sm-6']) !!}
	</div>
</div>
<div class="form-group">
    {!! Form::label('email', 'Email: ',['class'=>'control-label col-sm-3']) !!}
    <div class="col-sm-6">
    {!! Form::text('email', null, ['class' => 'form-control input-sm']) !!}
	</div>
</div>

<!-- Body Form Input-->
<div class="form-group">
	{!! Form::label('no_hash', 'Password: ',['class'=>'control-label col-sm-3']) !!}
    <div class="col-sm-6">
    {!! Form::text('no_hash', null, ['class' => 'form-control input-sm']) !!}
	</div>
</div>

<!-- Add Article Form Input-->
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-4">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
	</div>
</div>
