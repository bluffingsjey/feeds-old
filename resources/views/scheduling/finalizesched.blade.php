@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-primary adjust-top">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-calendar fa-fw"></i> Scheduling
            	<span class="pull-right"><a href="javascript:history.go(-1)" class="btn btn-xs btn-warning">Change Farms Delivery List</a></span>
            </h1>
        </div>
        <div class="panel-body" style="background:#bce8f1;">
            <div class="alert alert-success" role="alert">Deliveries Information Summary</div>
             <div class="table-responsive">
                <table class="table table-striped">
                  <tr>
                    <th>Delivery Date</th>
                    <th>Delivery Truck</th>
                    <th>Farm Name</th>
                  </tr>
                  @forelse($farms as $farm)
                  <tr>
                    <td>{{$delivery_datetime}}</td>
                    <td>{{$delivery_truck}}</td>
                    <td>{{$farm[0]->name}}</td>
                  </tr>
                  @empty
                  <tr>
                    <td>No farms selected</td>
                    <td></td>
                    <td></td>
                  </tr>
                  @endforelse
                </table>
        	</div>
            <div class="row" style="margin-top: 10px;">
                {!! Form::open(['url' => 'saveSchedule']) !!}
                {!!	Form::hidden('date_string',$date_string)	!!}
                <div class="col-md-offset-2 col-md-8">
                  <button type="submit" class="btn btn-success btn-block">Save Schedule</button>
                </div>  
                {!! Form::close() !!}
            </div>  
        
        </div>
        
    </div>  
</div>

@stop