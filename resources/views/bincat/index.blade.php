@extends('app')


@section('content')


<div class="col-md-12">
 
    <h1>Bins Category Administration <span><a href="/binscat/create" class="btn btn-xs btn-success">Add Bin Category</a></span></h1>
 
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
 
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
 
            <tbody>
                @foreach ($binsCats as $binsCat)
                <tr>
                    <td>{{ $binsCat->name }}</td>
                    <td>{{ $binsCat->description }}</td>
                    <td>
                        <a href="/binscat/{{ $binsCat->id }}/edit" class="btn btn-xs btn-info pull-left" style="margin-right: 3px;">Edit</a>
                        {!! Form::open(['method' => 'DELETE','action' => ['BinCatController@destroy', $binsCat->id]]) !!}
           				{!! Form::submit('Delete', ['class' => 'btn btn-xs btn-danger']) !!}
        				{!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
 
        </table>
    </div>
 
</div>

@stop