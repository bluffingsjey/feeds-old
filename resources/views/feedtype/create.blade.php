@extends('app')
@section('content')
<div class="col-md-10">
<div class="panel panel-info">
    <div class="panel-heading">
		<h1 class="panel-title">Create Feeds Type
        	<span class="pull-right">
            	<a href="/feedtype" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Feed Types</a>
            </span>
        </h1>
	</div>
    <div class="panel-body">
	{{--@include('errors.list')--}}
    {!! Form::open(['url' => 'feedtype']) !!}
		@include('feedtype.form',['submitButtonText' => 'Add Feed Type'])
	{!! Form::close() !!}
	</div>
</div>
</div>
@stop
