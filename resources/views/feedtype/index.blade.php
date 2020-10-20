@extends('app')

@section('content')

<div class="col-md-10">
 	<div class="panel panel-info">
            <div class="panel-heading">
    			<h1 class="panel-title">Feeds Type Administration <span><a href="/feedtype/create" class="btn btn-xs btn-success pull-right">Add Feed Type</a></span></h1>
 			</div>
    <div class="panel-body">
    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped">

            <thead>
                <tr class="">
                	<th class="col-md-2">Name</th>
                  <th class="col-md-2">Description</th>
                  <th class="col-md-2">Budgeted Amount</th>
                  <th class="col-md-2">Total Days</th>
                  <th class="col-md-2">Amount Increase Per Day</th>
                  <th class="col-md-2"></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($feedTypes as $feedType)
                <tr class="">
                    <td class="col-md-2">{{ $feedType['name'] }}</td>
                    <td class="col-md-2">{{ $feedType['description'] }}</td>
                    <td class="col-md-2">{{ $feedType['budgeted_amount'] }}</td>
                    <td class="col-md-2">{{ $feedType['total_days'] }}</td>
                    <td class="col-md-2">
                    @if($feedType['total_days'] != 0)
                      <button class="btn btn-xs btn-success view-map center" data-toggle="modal" data-target="#daysModal{{ $feedType['type_id'] }}">Days</button>
                    @else
                      none
                    @endif
                    </td>
                    <td class="col-md-2">

                        <a href="/feedtype/{{ $feedType['type_id'] }}/edit" class="btn btn-xs btn-info pull-left" style="margin-right: 1px;">Edit</a>
                        <button class="btn btn-xs btn-danger view-map pull-right" data-toggle="modal" data-target="#deleteModal{{ $feedType['type_id'] }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    </div>
</div>


@foreach($feedTypes as $feedType)
<div class="modal fade" id="daysModal{{ $feedType['type_id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update the Budgeted Amount Per Day</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="per_day_budgeted{{ $feedType['type_id']}}" action="/savebudgedtedperday" method="POST" >
          <div class="form-group">
            {!! Form::hidden('feed_type_id',$feedType['type_id']) !!}
            @for($i=1; $i != $feedType['total_days']+1; $i++)
            <label for="day1" style="margin-top:10px;" class="col-sm-2 control-label">Day {{$i}}:</label>
            <div class="col-sm-2" style="margin-top:10px;">
              {!! Form::input('number','day_'.$i,$feedType['day_'.$i],['class'=>'form-control']) !!}
            </div>
            @endfor
          </div>
        </form>
      </div>
      <div class="modal-footer">

       	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="save_per_day_{{ $feedType['type_id'] }}">Save</button>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal{{ $feedType['type_id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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


        {!! Form::open(['method' => 'DELETE','action' => ['FeedTypeController@destroy', $feedType['type_id']]]) !!}
       	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endforeach

<script type="text/javascript">

@foreach($feedTypes as $feedType)
$("#save_per_day_{{$feedType['type_id']}}").click(function(){
  //alert("testing");
  $("#per_day_budgeted{{$feedType['type_id']}}").submit();
});
@endforeach
</script>

@stop
