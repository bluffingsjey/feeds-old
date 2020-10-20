<div class="form-group">
    {!! Form::label('first_name', 'First Name: ') !!}
    {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('middle_name', 'Middle Name: ') !!}
    {!! Form::text('middle_name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('last_name', 'Last Name: ') !!}
    {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Body Form Input-->
<div class="form-group">
    {!! Form::label('contact_number', 'Contact Number: ') !!}
    {!! Form::textarea('contact_number', null, ['class' => 'form-control']) !!}
</div>

<!-- Add Article Form Input-->
<div class="form-group">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>