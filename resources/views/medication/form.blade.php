<div class="form-group">
    {!! Form::label('name', 'Name: ') !!}
    {!! Form::text('med_name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('description', 'Description: ') !!}
    {!! Form::textarea('med_description', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('medication_amount', 'Medication Amount: ') !!}
    {!! Form::text('med_amount', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>