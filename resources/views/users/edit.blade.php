@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title">Edit User
            <span class="pull-right"><a href="/users" class="btn btn-xs btn-primary">Back to Users</a></span>
            </h1>
        </div>
        <div class="panel-body">
            @include('errors.list')
            <!-- Form Model Binding-->
            {!! Form::model($users,['method' => 'PATCH','class'=>'form-horizontal','action' => ['UsersController@update',$users->id]]) !!}
                
                @include('users.form',['submitButtonText' => 'Update User'])
        
            {!! Form::close() !!}
        </div>    
    </div>    
</div>    
@stop