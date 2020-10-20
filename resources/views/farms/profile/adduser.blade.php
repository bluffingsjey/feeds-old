@extends('app')

@section('content')

<div class="col-md-10">
<h3>Add Farmer  <span><a href="{{url('/farmsprofile')}}" class="btn btn-sm btn-success pull-right">Back to Farm Profiles</a></span></h3>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
        <div class="panel-heading"><strong>Available Farmers</strong></div>
        <div class="panel-body">
        	<div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($farmers as $farmer)
                    <tr>
                        <td>{{$farmer->username}}</td>
                        <td>{{$farmer->no_hash}}</td>
                        <td>
                        	{!! Form::open(['method' => 'post','action' => ['FarmsController@saveFarmer']]) !!}
                            {!! Form::hidden('farm_id',$id)!!}
                            {!! Form::hidden('farmer_id',$farmer->id)!!}
                            {!! Form::submit('Add to Farm', ['class' => 'btn btn-xs btn-info']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @empty
                	<tr>
                        <td>No available farmers yet.</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
             	</tbody>
             </table>
           </div>
		</div>
    </div>
  </div>  
</div>

</div>
@stop
