@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title">Edit User Information
            <span class="pull-right"><a href="/users" class="btn btn-xs btn-primary">Back to Users</a></span>
            </h1>
        </div>
        <div class="panel-body">
            @include('errors.list')
            <!-- Form Model Binding-->
            {!! Form::open(['url'=>'/saveinfo', 'class'=>'form-horizontal']) !!}
                {!! Form::hidden('user_id', $user_id) !!}
                <div class="form-group">
                    {!! Form::label('first_name', 'First Name: ',['class'=>'control-label col-sm-3']) !!}
                    <div class="col-sm-6">
                    {!! Form::text('first_name', $info['first_name'], ['class' => 'form-control input-sm col-sm-6']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('middle_name', 'Middle Name: ',['class'=>'control-label col-sm-3']) !!}
                    <div class="col-sm-6">
                    {!! Form::text('middle_name', $info['middle_name'], ['class' => 'form-control input-sm']) !!}
                    </div>
                </div>
                
                <!-- Body Form Input-->
                <div class="form-group">
                    {!! Form::label('last_name', 'Last Name: ',['class'=>'control-label col-sm-3']) !!}
                    <div class="col-sm-6">
                    {!! Form::text('last_name', $info['last_name'], ['class' => 'form-control input-sm']) !!}
                    </div>
                </div>
                
                <!-- Body Form Input-->
                <div class="form-group">
                    {!! Form::label('contact_number', 'Mobile Number: ',['class'=>'control-label col-sm-3']) !!}
                    <div class="col-sm-6">
                    {!! Form::input('number','contact_number', $info['contact_number'], ['class' => 'form-control input-sm']) !!}
                    </div>
                </div>
                
                <!-- Add Article Form Input-->
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-4">
                    {!! Form::submit("Save", ['class' => 'btn btn-primary form-control']) !!}
                    </div>
                </div>

        
            {!! Form::close() !!}
        </div>    
    </div>    
</div>    
@stop