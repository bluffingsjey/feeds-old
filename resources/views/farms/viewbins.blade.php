@extends('app')


@section('content')

<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
    <h1 class="panel-title">Bins for <strong>{{$farm->name}}</strong>
    	<span class="pull-right"><a href="/farms" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Farms</a></span>
        <span class="pull-right" style="margin-right:10px;"><a href="/farms/addbinsbegin/{{$farm->id}}" class="btn btn-xs btn-success">Add Bin for this farm</a></span>
    </h1>
    </div>
	<div class="panel-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Bin #</th>
                    <th>Bin Name</th>
                    <th>Feed Type</th>
                    <th>Bin Size</th>
                    <th># of Pigs</th>
                    <th>Amount</th>
                    <th style="display:none">Feed Room</th>
                    <th>Bin Color</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($bins as $bin)
                <tr>
                    <td>{{$bin->bin_number}}</td>
                    <td>{{$bin->alias}}</td>
                    <td>{{$ctrl->getFeedDescription(($ctrl->recentFeedsHistory($bin->bin_id)[0]['feed_type']))}}</td>
                    <td>{{$bin->bin_size_name}}</td>
                    <td>{{$ctrl->animalGroupBinTotalPigs($bin->bin_id,$farm->id)}}</td>
                    <td>{{$ctrl->recentFeedsHistory($bin->bin_id)[0]['amount']}} Tons</td>
                    <td style="display:none">0</td>
                    <td style="background-color:{{$bin->hex_color}}"></td>
                    <td>
                    <!--<a href="/farms/{{ $farm->id }}/edit" class="btn btn-xs btn-info pull-left undone" style="margin-right: 3px;">Edit</a>-->
                     <a href="/farms/editbins?farm_id={{$farm->id}}&bin_id={{$bin->bin_id}}" class="btn btn-xs btn-info pull-left" style="margin-right: 3px;">Edit</a>
                    <a href="/farms/deletebin?farm_id={{$farm->id}}&bin_id={{$bin->bin_id}}" class="btn btn-xs btn-danger pull-left" style="margin-right: 3px;">Delete</a>
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
