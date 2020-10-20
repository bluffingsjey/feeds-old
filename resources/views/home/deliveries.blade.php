 @extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title pull-right">Deliveries</h1>
            <form class="form-inline" id="search_form">
              <div class="form-group">
                Search By:
                {!! Form::select('choose', $choose,NULL, ["class"=>"form-control input-sm", "id"=>"choose","placeholder"=>"Select Date"]) !!}
              </div>
              <div class="form-group">
                {!! Form::text('date', NULL, ["class"=>"form-control input-sm", "id"=>"search_date_deliveries","placeholder"=>"Select Date"]) !!}
              </div>
              <div class="form-group">
                {!! Form::select('farm', $farms_list, NULL, ["class"=>"form-control input-sm", "placeholder"=>"Select Farm", "id"=>"farms"]) !!}
              </div>
              <div class="form-group">
                {!! Form::select('driver', $driver, NULL, ["class"=>"form-control input-sm", "placeholder"=>"Select Driver", "id"=>"driver"]) !!}
              </div>
              <div class="form-group">
                {!! Form::text('delivery_number', "", ["class"=>"form-control input-sm", "placeholder"=>"Seach Ddelivery Number", "id"=>"delivery_number"]) !!}
              </div>
              <button type="button" id="search_btn" class="btn btn-sm btn-success btn-default">Search</button>
            </form>
        </div>

        <div class="panel-body">
        	<div class="table-responsive bg-warning">
                <table class="table table-striped deliveries-table">
                    <tr class="bg-info">
                        <th class="col-md-2 black-text">Date & Time</th>
                        <th class="col-md-5 black-text">Farms</th>
                        <th class="col-md-1 black-text">Truck</th>
                        <th class="col-md-1 black-text">Driver</th>
                        <th class="col-md-1 black-text">ID</th>
                        <th class="col-md-1 black-text">Status</th>
                        <th class="col-md-1 black-text">Action</th>
                    </tr>
                    @forelse($deliveries as $data)
                    <tr class="items-{{$data->unique_id}}">
                        <td class="col-md-2">{{date('M d Y a',strtotime($data->delivery_date))}}</td>
                        <td class="col-md-5">{!!$ctrl->getDeliveriesBinInfo($data->unique_id)!!}</td>
                        <td class="col-md-2">{{$data->truck_name}}</td>
                        <td class="col-md-1">{{$data->driver}}</td>
                        <td class="col-md-1">#{{substr($data->unique_id,0,7)}}</td>
                        <td class="col-md-1">
                        	<img class="img-{{$data->unique_id}}" src="{{ asset("images/".$ctrl->deliveriesStatus($data->unique_id)."")}}.png"/>
                        </td>
                        <td class="col-md-1">
                        	@if($data->status == 0 || $data->status == 1 || $ctrl->deliveriesStatus($data->unique_id) == "delivery_ongoing_red" || $ctrl->deliveriesStatus($data->unique_id) == "delivery_ongoing_green")
                        	<button type="button" class="btn btn-success btn-xs btn-block btn-mark-{{$data->unique_id}}" unique_id="{{$data->unique_id}}">Mark as Completed</button>
                            @endif
                            <button type="button" class="btn btn-danger btn-xs btn-block btn-delete-{{$data->unique_id}}" unique_id="{{$data->unique_id}}">Delete</button>
                        </td>
                    </tr>
                    @include('home.form.deliveryinfo')

                    @empty
                    <tr>
                        <td class="col-md-2">No data yet</td>
                        <td class="col-md-4"></td>
                        <td class="col-md-2"></td>
                        <td class="col-md-2"></td>
                        <td class="col-md-2"></td>
                    </tr>
                    @endforelse
                </table>
            </div>

            @if($load_more == "true")
            <button class="btn-block btn-success btn-md btn-loadmore" items="10">Load More</button>
            <img class="img-responsive center-block loadmore-loading" src="{{ asset("css/images/loader-stick.gif")}}"/>
            @endif
        </div>

        <div class="modal-holder"></div>

    </div>
</div>

<script type="text/javascript">

function markDelivered(unique_id){
  setTimeout(function(){
    $(".img-"+unique_id).attr("src",app_url+"/images/delivery_delivered.png");
    $(".btn-mark-"+unique_id).hide();
  },2000);

	$.ajax({
		url 	:	app_url+"/markdelivered",
		data 	: {'unique_id':unique_id},
		type 	: "POST",
		success : function(r){
			//if(r != null && r != 0){
      setTimeout(function(){
				$(".img-"+unique_id).attr("src",app_url+"/images/delivery_delivered.png");
				$(".btn-mark-"+unique_id).hide();
      },2000);
			//}
		}
	})
}

function deleteDelivered(unique_id){
	$.ajax({
		url 	:	app_url+"/deletedelivered",
		data 	: {'unique_id':unique_id},
		type 	: "POST",
		success : function(r){

		}
	})
}

function loadMoreDeliveries(items_load){
	$.ajax({
		url 	:	app_url+"/deliveriesloadmore",
		data 	: {'items_load':items_load},
		type 	: "POST",
		success : function(r){

			if(r != null){
				$(".deliveries-table").append(r);

				$(".loadmore-loading").hide(function(){
					$(".btn-loadmore").show();
				});

				var increment_items = Number(items_load) + 10;
				$(".btn-loadmore").attr("items",increment_items);
			} else {
				$(".btn-loadmore").hide();
			}

		}
	})
}

$(document).ready(function(e) {

  /*
	*	schedDateTime
	*/
	$("#search_date_deliveries").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'M d, yy',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});


	// trigger the change date
	$("#search_date_deliveries").change(function(){

    var delivery_date = $(this).val();
    // search farms
    //farms(delivery_date);
    // search drivers
    //drivers(delivery_date);
    // search delivery number
    //deliveryNumbers(delivery_date);
	});

  /*
  $("#farms").change(function(){
    var delivery_date = $("#search_date_deliveries").val();
    var farm_id = $(this).val();
    var $delivery_number = $("#delivery_number");
    var $driver = $("#driver");
    $.ajax({
      url 	:	app_url+"/farmselectdeliveriesdrivers",
  		data 	: {'delivery_date': delivery_date,'farm_id':farm_id},
  		type 	: "GET",
  		success : function(r){
        $driver.empty();
        $driver.append("<option value='please_select'>Select Driver</option>");
        $.each(r.drivers, function(k,v){
          $driver.append("<option value="+v.driver_id+">"+v.driver+"</option>");
        });

        $delivery_number.empty();
        $delivery_number.append("<option value='please_select'>Select Delivery Number</option>");
        $.each(r.unique_id, function(k,v){
          $delivery_number.append("<option value="+v.unique_id+">"+v.delivery_number+"</option>");
        });
      }
    });

  });

  $("#driver").change(function(){
    var $delivery_number = $("#delivery_number");
    var farm_id = $("#farms").val();
    var driver_id = $(this).val();
    var delivery_date = $("#search_date_deliveries").val();
    $.ajax({
      url 	:	app_url+"/driverselectdeliveriesdrivers",
  		data 	: {'delivery_date': delivery_date,'farm_id':farm_id,'driver_id':driver_id},
  		type 	: "GET",
  		success : function(r){
        $delivery_number.empty();
        $delivery_number.append("<option value='please_select'>Select Delivery Number</option>");
        $.each(r, function(k,v){
          $delivery_number.append("<option value="+v.unique_id+">"+v.delivery_number+"</option>");
        });
      }
    });
  })

  $("#delivery_number").change(function(){

  });

  function farms(delivery_date){
    var $farms = $("#farms");
    $.ajax({
      url 	:	app_url+"/deliveriesfarms",
  		data 	: {'delivery_date': delivery_date},
  		type 	: "GET",
  		success : function(r){
        $farms.empty();
        $farms.append("<option value='please_select'>Select Farm</option>");
        $.each(r, function(k,v){
          $farms.append("<option value="+v.farm_id+">"+v.farm_name+"</option>");
        });
      }
    });
  }


  function drivers(delivery_date){
    var $driver = $("#driver");
    $.ajax({
      url 	:	app_url+"/deliveriesdrivers",
  		data 	: {'delivery_date': delivery_date},
  		type 	: "GET",
  		success : function(r){
        $driver.empty();
        $driver.append("<option value='please_select'>Select Driver</option>");
        $.each(r, function(k,v){
          $driver.append("<option value="+v.driver_id+">"+v.driver+"</option>");
        });
      }
    });
  }

  function deliveryNumbers(delivery_date){
    var $delivery_number = $("#delivery_number");
    $.ajax({
      url 	:	app_url+"/deliveriesnumbers",
  		data 	: {'delivery_date': delivery_date},
  		type 	: "GET",
  		success : function(r){
        $delivery_number.empty();
        $delivery_number.append("<option value='please_select'>Select Delivery Number</option>");
        $.each(r, function(k,v){
          $delivery_number.append("<option value="+v.unique_id+">"+v.delivery_number+"</option>");
        });
      }
    });
  }
  */

  $("#search_date_deliveries").hide();
  $("#farms").hide();
  $("#driver").hide();
  $("#delivery_number").hide();

  $("#choose").change(function(){
    var choose = $(this).val();
    if(choose == "plaese_select"){
      $("#search_date_deliveries").hide();
      $("#farms").hide();
      $("#driver").hide();
      $("#delivery_number").hide();
    } else if(choose == "date"){
      $("#search_date_deliveries").show();
      $("#farms").hide();
      $("#driver").hide();
      $("#delivery_number").hide();
    } else if(choose == "farm"){
      $("#search_date_deliveries").hide();
      $("#farms").show();
      $("#driver").hide();
      $("#delivery_number").hide();
    } else if(choose == "driver"){
      $("#search_date_deliveries").hide();
      $("#farms").hide();
      $("#driver").show();
      $("#delivery_number").hide();
    } else {
      $("#search_date_deliveries").hide();
      $("#farms").hide();
      $("#driver").hide();
      $("#delivery_number").show();
    }

  });

  $("#search_btn").click(function(){
    var date = $("#search_date_deliveries").val();
    var farm = $("#farms").val();
    var driver = $("#driver").val();
    var delivery_number = $("#delivery_number").val();
    var error = "";

    if(date == "" && farm == "please_select" && driver == "please_select" && delivery_number == ""){
      error = "Please select or search atleast one search criteria";
    } else {
      error = error;
    }

    if(error == ""){
        $("#search_form").submit();
        $(".btn-loadmore").hide();
        $(".deliveries-table").html("");
        $(".deliveries-table").append("<img class='img-responsive center-block loadmore-loading' src='{{ asset('css/images/loader-stick.gif')}}'/>")
    } else {
        alert(error);
    }

  });

	$(".loadmore-loading").hide();

    @forelse($deliveries as $data)

		$(".container").delegate(".btn-mark-{{$data->unique_id}}","click",function(e) {
            var unique_id = $(this).attr("unique_id");
		    $(".img-"+unique_id).attr("src",app_url+"/css/images/loader-stick.gif");
		    $(".btn-mark-"+unique_id).hide();
		    markDelivered(unique_id);
        });

		$(".container").delegate(".btn-delete-{{$data->unique_id}}","click",function(e) {
        	var unique_id = $(this).attr("unique_id");
			deleteDelivered(unique_id)
			$(".items-"+unique_id).hide();
        });

	 @empty
	 @endforelse

	 $(".container").delegate(".btn-loadmore","click",function(){

			$(this).hide(function(){
				$(".loadmore-loading").show();
			});

			var items = $(this).attr("items");
			loadMoreDeliveries(items);

	 });
});

</script>
@stop
