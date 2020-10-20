@extends('app')


@section('content')

<div class="col-lg-10">
<div class="panel panel-info">
    <div class="panel-heading">
        <h1 class="panel-title">Trucks Administration <span class="pull-right"><a href="/truck/create" class="btn btn-xs btn-success">Add Truck</a></span></h1>
    </div>
    <div class="panel-body">    
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
 
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Compartment</th>
                    <th></th>
                </tr>
            </thead>
 
            <tbody>
                @foreach ($trucks as $truck)
                <tr>
                    <td>{{ $truck->name }}</td>
                    <td>{{ $truck->capacity }} Tons</td>
                    <td>{{ $truck->compartment }}
                    @if($truck->compartment == 0)
                    	<small class="text-danger pull-right">No compartment yet</small>
                    @else
                    	<a href="/trucks/compartments/{{$truck->truck_id}}" class="btn btn-xs btn-info pull-right" style="margin-right: 3px;">View Compartments</a></td>
                    @endif
                    <td>
                    	@if($truck->comcapacity >= $truck->capacity)
                    	<small class="text-success">Compartments Full</small>
                        @else
                        <a href="/truck/addcom/{{ $truck->truck_id }}" class="btn btn-xs btn-info" style="margin-right: 3px;">Add Compartment</a>
                        @endif
                        <button class="btn btn-xs btn-danger  pull-right" data-toggle="modal" data-target="#deleteModal{{$truck->truck_id}}">Delete</button>
                        <a href="/truck/{{ $truck->truck_id }}/edit" class="btn btn-xs btn-info pull-right" style="margin-right: 3px;">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
 
        </table>
    </div>
    </div>
</div> 
</div>


<!--Delete Modal -->
@foreach ($trucks as $truck)
<div class="modal fade" id="deleteModal{{$truck->truck_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Are you sure you want to delete this truck?<br/>
        	<small class="text-danger">All compartments under this truck will be deleted.</small>
    	</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <a href="/truck/delete/{{ $truck->truck_id }}" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>
@endforeach

@stop