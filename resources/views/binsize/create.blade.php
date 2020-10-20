@extends('app')
@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
			<h1 class="panel-title">Create Bin Size
            	<span class="pull-right"><a href="/binsize" class="btn btn-xs btn-primary">Back to Bin Size</a></span>
            </h1>
        </div>
        <div class="panel-body">
        @include('errors.list')
        {!! Form::open(['url' => 'binsize','class' => 'form-inline']) !!}
            @include('binsize.form',['submitButtonText' => 'Add Bin Size'])
        {!! Form::close() !!}
        </div>
    </div>
</div>
@stop