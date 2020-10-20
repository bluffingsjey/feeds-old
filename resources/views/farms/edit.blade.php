@extends('app')


@section('content')
<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
        <h1 class="panel-title">Edit Farm <span class="pull-right"><a href="/farms" class="btn btn-xs btn-info pull-right"><span class="glyphicon glyphicon-home"></span> Back to Farms</a></span></h1>
    </div>
    <div class="panel-body">
    @include('errors.list')
    <!-- Form Model Binding-->
    {!! Form::model($farms,['method' => 'PATCH','id' => 'create_farm','action' => ['FarmsController@update',$farms->id]]) !!}
			@include('farms.form',['submitButtonText' => 'Update Farm'])
		{!! Form::close() !!}
    </div>
</div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-m-mI5Zae0JsqBeHyKh5v-lMvgCdsYmk&libraries=places&callback=initialize"></script>
@stop
