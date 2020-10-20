@extends('app')


@section('content')


<div class="col-md-12">
 
    <h1>Bins Administration <span style="display:none;"><a href="/bins/create" class="btn btn-xs btn-success">Add Bin</a></span></h1>
 
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
 
            <thead>
                <tr>
                    <th>Bin name</th>
                    <th>Farm name</th>
                    <th>Number of Pigs</th>
                    <th>Amount</th>
                    <th>Bin Color</th>
                    <th></th>
                </tr>
            </thead>
 
            <tbody>
                @foreach ($bins as $bin)
                <tr>
                    <td>{{ $bin->alias }}</td>
                    <td>{{ $bin->name }}</td>
                    <td>{{ $bin->num_of_pigs }}</td>
                    <td>{{ $bin->amount }} Tons</td>
                    <td style="background-color:{{ $bin->hex_color }}"></td>
                    <td>
                        <a href="/bins/{{ $bin->bin_id }}/edit" class="btn btn-xs btn-info pull-left" style="margin-right: 3px;">Edit</a>
                        {!! Form::open(['method' => 'DELETE','action' => ['BinsController@destroy', $bin->bin_id]]) !!}
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