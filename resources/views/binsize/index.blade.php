@extends('app')

@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title">Bin Size
                <span class="pull-right"><a href="/binsize/create" class="btn btn-xs btn-success">Add Bin Size</a></span>
            </h1>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-condensed table-bordered table-striped">
                    <thead>
                        <tr class="">
                            <th class="col-md-2">Name</th>
                            <th class="col-md-2">Ring</th>
														<th class="col-md-2">Tons</th>
                            <th class="col-md-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feedSizes as $feedSize)
                        <tr class="">
                            <td class="col-md-2">{{ $feedSize->name }}</td>
                            <td class="col-md-2">{{ $feedSize->ring }}</td>
														<td class="col-md-2">{{ $ctrl->tonsAmountExtractor($feedSize->size_id) }}</td>
                            <td class="col-md-2">
                                <a href="/binsize/{{ $feedSize->size_id }}/edit" class="btn btn-xs btn-info pull-left" style="margin-right: 1px;">Edit</a>
                                <button class="btn btn-xs btn-danger view-map pull-right" data-toggle="modal" data-target="#deleteModal{{ $feedSize->size_id }}">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
     </div>
</div>


@foreach($feedSizes as $feedSize)
<div class="modal fade" id="deleteModal{{ $feedSize->size_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Are you sure you want to delete this feed type?<br/>
    	</p>
      </div>
      <div class="modal-footer">
        {!! Form::open(['method' => 'DELETE','action' => ['BinSizeController@destroy', $feedSize->size_id]]) !!}
       	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endforeach

@stop
