@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
        <h1 class="panel-title">Farm Creation <span class="pull-right"><a href="/farms" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Farms</a></span></h1>
        </div>
        <div class="panel-body">
        @include('errors.list')
        {!! Form::open(['url' => 'farms', 'id' => 'create_farm']) !!}
            @include('farms.form',['submitButtonText' => 'Create Farm'])
        {!! Form::close() !!}
        </div>
    </div>
</div>    
@stop