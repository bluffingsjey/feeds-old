@extends('app')

@section('content')

<div class="col-md-10">
 	<div class="panel panel-info">
            <div class="panel-heading">
    			<h1 class="panel-title">Medication Administration <span><a href="/medication/create" class="btn btn-xs btn-success pull-right">Add Medication</a></span></h1>
 			</div>
    <div class="panel-body">        
    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped">
 
            <thead>
                <tr class="">
                	<th class="col-md-2">Name</th>
                    <th class="col-md-2">Description</th>
                    <th class="col-md-2">Medication Amount</th>
                    <th class="col-md-2"></th>
                </tr>
            </thead>
 
            <tbody>
                @forelse ($medications as $medication)
                <tr class="">
                    <td class="col-md-2">{{ $medication->med_name }}</td>
                    <td class="col-md-2">{{ $medication->med_description }}</td>
                    <td class="col-md-2">{{ $medication->med_amount }}</td>
                    <td class="col-md-2">
                        <a href="/medication/{{ $medication->med_id }}/edit" class="btn btn-xs btn-info pull-left" style="margin-right: 1px;">Edit</a>
                        <button class="btn btn-xs btn-danger view-map pull-right" data-toggle="modal" data-target="#deleteModal{{ $medication->med_id }}">Delete</button>
                    </td>
                </tr>
                @empty
                <tr class="">
                    <td class="col-md-2">None yet.</td>
                    <td class="col-md-2"></td>
                    <td class="col-md-2"></td>
                    <td class="col-md-2"></td>
                </tr>
                @endforelse
            </tbody>
 
        </table>
    </div>
    </div> 
</div>


@forelse($medications as $medication)
<div class="modal fade" id="deleteModal{{ $medication->med_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Are you sure you want to delete this medication type?<br/>
    	</p>
      </div>
      <div class="modal-footer">
        {!! Form::open(['method' => 'DELETE','action' => ['MedicationController@destroy', $medication->med_id]]) !!}
       	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@empty

@endforelse

@stop