@extends('app')

@section('content')
<div class="col-md-10">
	<div class="panel panel-info">
        <div class="panel-heading">
			<h1>{{ $farm->name }}  <span class="pull-right"><a href="/truck" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Trucks</a></span></h1>
        </div>
        <div class="panel-body">    
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
        </div>    
	</div>
</div>    
@stop