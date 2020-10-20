@extends('app')


@section('content')
<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
        <h1 class="panel-title">Edit Bin Type <span class="pull-right"><a href="/binsize" class="btn btn-xs btn-info pull-right"><span class="glyphicon glyphicon-home"></span> Back to Bin Size</a></span></h1>
    </div>
    <div class="panel-body">	
    @include('errors.list')
    <!-- Form Model Binding-->
    {!! Form::model($binsizes,['method' => 'POST','url'=>'binsize/update','action' => ['BinSizeController@update',$binsizes->type_id]]) !!}
    	{!! Form::hidden('size_id',$binsizes->type_id) !!}
		@include('binsize.form',['submitButtonText' => 'Update Bin Size'])
	{!! Form::close() !!}
	</div>
</div>    
</div>
    
@stop