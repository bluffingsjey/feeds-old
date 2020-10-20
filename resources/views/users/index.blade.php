@extends('app')


@section('content')

<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading"> 
    	<h1 class="panel-title">Users Administration <span class="pull-right"><a href="{{url('/auth/register')}}" class="btn btn-xs btn-success">Add User</a></span></h1>
        </div>
        <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Roles</th>
                        <th>Date/Time Added</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->no_hash }}</td>
                        <td>
                            @if (!empty($user->description)) 
                                {{$user->description}} 
                            @else
                                <a href="users/assignrole/{{$user->id}}" class="btn btn-xs btn-xs btn-success">Assign Role</a>
                            @endif    
                        </td>
                        <td>{{ date('m-d-Y H:s',strtotime($user->created_at)) }}</td>
                        <td>
                            <a href="/edituserinfo/{{ $user->id }}" class="btn btn-xs btn-block btn-info pull-left" style="margin-right: 1px;">Edit Info</a>
                            <a href="/users/{{ $user->id }}/edit" class="btn btn-xs btn-block btn-info pull-left" style="margin-right: 1px; margin-bottom: 5px;">Edit Access</a>
                            {!! Form::open(['method' => 'DELETE','action' => ['UsersController@destroy', $user->id]]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-xs btn-block btn-danger']) !!}
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