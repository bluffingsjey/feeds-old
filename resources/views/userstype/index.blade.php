@extends('app')
@section('content')
<div class="col-lg-10">
	<div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-users"></i> Users Type Administration 
            <span class="pull-right"><a href="#" class="btn btn-xs btn-success undone">Add User Type</a></span>
            </h1>
        </div>
 		<div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usersTypes as $usersType)
                        <tr>
                            <td>{{ $usersType->description }}</td>
                            <td>
                                <a href="/userstype/{{ $usersType->type_id }}/edit" class="btn btn-xs btn-info pull-left undone" style="margin-right: 3px;">Edit</a>
                                {!! Form::open(['method' => 'DELETE','action' => ['FarmsController@destroy', $usersType->type_id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-xs btn-danger undone']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
 		</div>
     </div>   
</div>

@stop