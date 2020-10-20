@extends('app')


@section('content')
<div class="col-md-10">
    <div class="panel panel-primary adjust-top">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-calendar fa-fw"></i> Scheduling</h1>
        </div>
        <div class="panel-body" style="background:#bce8f1;">
        	<div class="alert alert-success" role="alert">Choose Delivery Date,Time and Delivery Truck</div>
            @include('errors.list')
            {!! Form::open(['url' => 'addFarm', 'class'=>'form-horizontal']) !!}
              @include('scheduling.forms.dates')
            {!! Form::close() !!}
        </div>
    </div>  
</div>

<!--<div class="col-md-12" ng-app="handhApp" ng-controller="MainController">

<div class="row">
    <div class="panel panel-default adjust-top">
        <div class="panel-heading">
            <h3 class="grey-font">Scheduling</h3>
            <small class="text-info">Create deliveries.</small>
        </div>
        <div class="panel-body" ng-include="'js/angular_modules/partials/scheduling/form.html'"></div>
    </div> 
</div>

<div class="row">
    <div class="panel panel-default adjust-top">
        <div class="panel-heading">
            <h3 class="grey-font">Scheduled List</h3>
            <small class="text-info">Created deliveries.</small>
        </div>
        <div class="panel-body" ng-include="'js/angular_modules/partials/scheduling/list.html'"></div>
    </div> 
</div>

</div>
-->
@stop