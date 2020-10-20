@extends('app')

@section('content')

	<h1>{{ $farm->name }}</h1>
	<hr>
	<article>
		{{ $farm->address }}
	</article>
    
    @unless ($farm->tags->isEmpty())
        <h5>Tags:</h5>
        
        <ul>
            @foreach($farm->tags as $tag)
                <li>{{ $tag->name }}</li>
            @endforeach
        </ul>
	@endunless
    
    <div class="col-md-6 text-right">
        {!! Form::open([
            'method' => 'DELETE',
            'action' => ['FarmsController@destroy', $farm->id]
        ]) !!}
            {!! Form::submit('Delete this farm?', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </div>

@stop