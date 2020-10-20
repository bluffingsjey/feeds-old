<div class="form-group">
    {!! Form::label('alis', 'Alias: ') !!}
    {!! Form::text('alias', $alias, null, ['class' => 'form-control']) !!}
</div>

<!-- Body Form Input-->
<div class="form-group">
    {!! Form::label('num_of_pigs', 'Number of Pigs: ') !!}
    {!! Form::text('num_of_pigs', null, ['class' => 'form-control']) !!}
</div>

<!-- Body Form Input-->
<div class="form-group">
    {!! Form::label('amount', 'Amount: ') !!}
    {!! Form::text('amount', null, ['class' => 'form-control']) !!}
</div>

<!-- Add Article Form Input-->
<div class="form-group">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>