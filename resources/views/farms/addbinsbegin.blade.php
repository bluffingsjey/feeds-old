@extends('app')


@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Add Bins for <strong>{{$farm->name}}</strong>
            <span class="pull-right"><a href="/farms" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-home"></span> Back to Farms</a></span>
            </h3>
        </div>
	<div class="panel-body">
    @include('errors.list')

        <div class="col-md-10">
            <p class="text-info">
                <strong>Note:</strong> Only numbers are accepted on this field.
            </p>

            {!! Form::open(['method' => 'POST', 'url' => 'farms/addbinscreate', 'class' => 'form-inline add-bin-form']) !!}
            {!! Form::hidden('farm_id', $farm->id) !!}
            <div class="form-group">
            <input type="number" name="bins_number" class="form-control input-sm numeric bin_num" autocomplete="off" data-togle="tooltip" title="Enter the number of bins" value="0" />
            </div>
            {!! Form::button('Proceed', ['class' => 'btn btn-xs btn-success btn-proceed']) !!}

            {!! Form::close() !!}

        </div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".bin_num").keyup(function(e){
		var bin_num = $(this).val();
		// exclude 0 on keypress
		if(bin_num == 0){
			alert("0 is not allowed on this field.");
			$(this).val("");
		}
	});

  $(".btn-proceed").click(function(){

    var bin_num = $(".bin_num").val();
		// exclude 0 on keypress
		if(bin_num == 0){
			alert("0 is not allowed on this field.");
			$(".bin_num").val(0);
		} else if(bin_num == ""){
      alert("Please enter the number of bins.");
			$(".bin_num").val(0);
    } else {
      $(".add-bin-form").submit();
    }

  });
})
</script>

@stop
