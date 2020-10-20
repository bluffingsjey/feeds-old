@extends('app')


@section('content')
<div class="col-md-10">
<div class="panel panel-info">
    <div class="panel-heading">        
        <h1 class="panel-title">Comparments of <strong>{{$trucks->name}}</strong>
        <span class="pull-right" style="margin-left:5px;">
        	<a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a>
        </span>
        <span class="pull-right">
        	<a href="/compartment/batchdelete/{{$trucks->truck_id}}" class="btn btn-xs btn-danger">Delete All Compartment</a>
        </span>
        </h1>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
     
                <thead>
                    <tr>
                        <th>Compartment Number</th>
                        <th>Capacity</th>
                        <th></th>
                    </tr>
                </thead>
     
                <tbody>
                    @foreach ($compartments as $compartment)
                    <tr>
                        <td>{{ $compartment->compartment_number }}</td>
                        <td>{{ $compartment->capacity }} Tons</td>
                        <td>
                            <a href="/compartment/edit/{{$compartment->compartment_id}}" class="btn btn-xs btn-info">Edit</a>
                            <button class="btn btn-xs btn-danger view-map" data-toggle="modal" data-target="#deleteModal{{ $compartment->compartment_id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
     
            </table>
        </div>
    </div>
</div>        
</div>    

@foreach ($compartments as $compartment)
<!--Delete Modal -->
<div class="modal fade" id="deleteModal{{ $compartment->compartment_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Are you sure you want to delete this compartment?
    	</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        @if(isset($compartment))
        <a href="/compartment/delete/{{ $compartment->compartment_id }}" class="btn btn-danger">Delete</a>
        @endif
      </div>
    </div>
  </div>
</div>
@endforeach

@stop