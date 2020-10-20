 @forelse($data as $list)
<div class="kv-sched-view table-load-kb-view col-md-12 col-lg-12 ">

    <div class="col-md-1">{{$list->delivery_date}}</div>
    <div class="col-md-3">{!!$list->farm_name!!}</div>
    <div class="col-md-2">{!! $list->delivery_time !!}</div>
    <div class="col-md-1">{{str_replace("Truck","",$list->truck_name)}}</div>
    @if($list->status == 0)

    <div class="col-md-2">{!! Form::select("truck_driver",array(0=>'-')+$drivers,$list->selected_driver,['class'=>'form-control input-sm active-drop-down sched_driver',"id"=>"driver-".$list->unique_id,"unique_id"=>$list->unique_id]) !!}</div>
    <div class="col-md-1"  style="width: 125px;">{!! Form::select("delivery_number",$delivery_count,$list->selected_delivery,["class"=>"form-control input-sm active-drop-down delivery_number del_number-".$list->unique_id,"id"=>$list->unique_id]) !!}</div>
    <div class="col-md-2 action_sched_kb" style="width: 100px;">

        {!!	Form::open(['url'=>url().'/loading/createload', 'class'=>'form-'.$list->unique_id, 'method'=>'GET']) !!}
        {!! Form::hidden('unique_id', $list->unique_id) !!}
        {!!	Form::hidden('truck_id', $list->truck_id) !!}
        {!! Form::submit('Create Load',['class'=>'btn_sched_kb_list', 'unique_id'=>$list->unique_id]) !!}
        {!! Form::close() !!}

        <button type="button" class="btn-default btn-xs btn-delsched{{$list->schedule_id}}" unique="{{$list->unique_id}}"
        style="background: #FFF; color: #FF0000; border: none; font-size: 12px; font-weight: bold;">Delete</button>

    </div>

    @else

    <div class="col-md-2">{!! Form::select("truck_driver",array(0=>'-')+$drivers,$list->selected_driver,['class'=>'form-control input-sm active-drop-down sched_driver',"id"=>"driver-".$list->unique_id,"unique_id"=>$list->unique_id, "disabled"=>"disabled","style"=>"background:#DDD !important"]) !!}</div>
    <div class="col-md-1"  style="width: 125px;">{!! Form::select("delivery_number",$delivery_count,$list->selected_delivery,["class"=>"form-control input-sm active-drop-down delivery_number del_number-".$list->unique_id,"id"=>$list->unique_id, "disabled"=>"disabled","style"=>"background:#DDD !important"]) !!}</div>
    <div class="col-md-2 action_sched_kb" style="width: 100px;">
        <button type="button" class="btn-default btn-xs btn-delsched{{$list->schedule_id}}" unique="{{$list->unique_id}}"
        style="background: #FFF; color: #FF0000; border: none; font-size: 12px; font-weight: bold;">Delete</button>
        @if($list->sched_tool_status == 'created')
        <button type="button" class="btn-default btn-xs btn-edit-sched{{$list->schedule_id}}" unique="{{$list->unique_id}}"
        style="background: #FFF; color: #5cb85c; border: none; font-size: 12px; font-weight: bold;">Edit</button>
        @endif
    </div>

    @endif
</div>

@empty
<div class="table-load-kb-view col-md-12 col-lg-12 ">
    <div class="col-lg-12 col-md-12">No Entry</div>
</div>
@endforelse

<script type="text/javascript">

$(document).ready(function(){

  @forelse($data as $list)

	$(".container").delegate(".btn-delsched{{$list->schedule_id}}","click",function(){

    $(".sched-items-holder").html("");
    $(".sched-items-holder").append("<img class='img-responsive center-block loadmore-loading' src='{{ asset('css/images/loader-stick.gif')}}'/>");

		var unique = $(this).attr("unique");

		$.ajax({

			url		:	app_url+'/schedlistindex',
			type 	: "POST",
			data 	: {'unique_id':unique},
			success: function(r){

				$("#"+unique).remove();
				window.location.replace(app_url+"/loading");

			}

		});

	});


  $(".container").delegate(".btn-edit-sched{{$list->schedule_id}}","click",function(){

		var unique = $(this).attr("unique");
  	window.location.replace(app_url+"/schededitlist/"+unique);

	});

	@empty
	@endforelse

	$(".btn_sched_kb_list").click(function(e){
		e.preventDefault();

		var unique_id = $(this).attr("unique_id");
		var delivery_number = $(".del_number-"+unique_id).val();
		var delivery_driver = $("#driver-"+unique_id).val();

		if(delivery_number == 0 || delivery_driver == 0){
			alert("Please choose driver and/or delivery number");
			return false;
		} else {
			$(".form-"+unique_id).submit();
		}
	});

});
</script>
