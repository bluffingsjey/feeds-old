@extends('app')


@section('content')
<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
        <h1 class="panel-title">Edit Bin <span class="pull-right"><a href="/farms/viewbins/{{$farm_id}}" class="btn btn-xs btn-info pull-right"><span class="glyphicon glyphicon-home"></span> Back to Bins</a></span></h1>
    </div>
    <div class="panel-body">
    @include('errors.list')
    <!-- Form Model Binding-->
    {!! Form::model($bin,['method' => 'POST','class'=>'form-horizontal','id' => 'create_farm','action' => ['FarmsController@updateBin',$bin->id]]) !!}
		@include('farms.formbins',['submitButtonText' => 'Update Bin'])
	{!! Form::close() !!}
    </div>
</div>    
</div>
@stop