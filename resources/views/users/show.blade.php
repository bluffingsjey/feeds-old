@extends('app')

@section('content')

	<h1>{{ $user->name }}</h1>
	<hr>
	<article>
		{{ $user->address }}
	</article>
        
    <div class="col-md-6 text-right">
        {!! Form::open([
            'method' => 'DELETE',
            'action' => ['UsersController@destroy', $user->id]
        ]) !!}
            {!! Form::submit('Delete this user?', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </div>

@stop