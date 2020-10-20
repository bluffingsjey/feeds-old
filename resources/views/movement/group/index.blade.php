@extends('app')


@section('content')

<style type="text/css">
</style>
<div class="col-md-10">
    
<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
            	<h3 class="panel-title text-center">Farrowing</h3>
            </div>
            <div class="panel-body">
            <p>This is when the pigs are born and raise to different farm bins.</p>
            <p><a href="{{url('/farrowing')}}" class="btn btn-block btn-success" role="button">View</a></p>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
            	<h3 class="panel-title text-center">Nursery</h3>
            </div>
            <div class="panel-body">
            <p>The combination of transfered multiple farrowing groups.</p>
            <p><a href="{{url('/nursery')}}" class="btn btn-block btn-success" role="button">View</a></p>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
            	<h3 class="panel-title text-center">Finisher</h3>
            </div>
            <div class="panel-body">
            <p>The combination of transfered multiple nursery groups.</p>
            <p><a href="{{url('/finisher')}}" class="btn btn-block btn-success" role="button">View</a></p>
            </div>
        </div>
    </div>
</div>

</div>

<script type="text/javascript">
$(document).ready(function(e) {
   
});
</script>

@stop