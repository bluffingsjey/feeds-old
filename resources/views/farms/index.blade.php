@extends('app')


@section('content')

<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
    <h1 class="panel-title">Farms Administration <span><a href="/farms/create" class="btn btn-xs btn-success pull-right">Add Farm</a></span>
    	<span class="pull-right">
        	<form class="form-inline" style="display:none">
              <div class="form-group">
                <input type="text" class="form-control input-sm" id="inputSearch" placeholder="Search Farm">
              	<button type="submit" class="btn btn-xs btn-primary">Search</button>
              </div>
            </form>
        </span>
    </h1>
    </div>
	<div class="panel-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Delivery Time</th>
                    <th>Bins</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($farms as $farm)
                <tr>
                    <td>{{ $farm->name }}</td>
                    <td>
                        <div class="col-md-10">
                        	<p>{{ $farm->address }}</p>
                            <p><strong>Packer Farm Name: </strong>{{ $farm->packer == NULL ? "NONE" :  $farm->packer}}</p>
                            <p><strong>Type: </strong>{{ strtoupper($farm->farm_type)}}</p>
														<p><strong>Contact Number: </strong>{{ $farm->contact == NULL ? "NONE" :  $farm->contact}}</p>
                        </div>
                        <div class="col-md-2">
                        	<button class="btn btn-xs btn-info view-map pull-right" data-toggle="modal" lat="{{$farm->lattitude}}" lng="{{$farm->longtitude}}" data-target="#myModal{{ $farm->id }}">View Map</button>
                        </div>
                    </td>
                    <td class="text-center">
                    	<div class="loading-{{ $farm->id }}" style="display:none">

                            <img src="/css/images/loader-stick.gif" />
                            Please wait...

                        </div>

                    	<span class="status{{ $farm->id }}">{{ $status = $farm->status == 0 ? 'Inactive' : 'Active' }}</span>
                        <span class="glyphicon glyphicon-time act_date-{{ $farm->id }}" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Activation Date: {{date("M d",strtotime($farm->reactivation_date))}}" style="display:{{ $status = $farm->status == 0 ? 'block' : 'none' }}"></span>
                    	<button class="btn btn-block btn-xs btn-warning" id="turnoffbtn-{{$farm->id}}" data-toggle="modal" data-target="#turnOffModal{{ $farm->id }}" style="display:{{ $status = $farm->status == 0 ? 'none' : 'block' }}">Turn Off</button>
                        <button class="btn btn-block btn-xs btn-default" id="turnonbtn-{{$farm->id}}" style="display:{{ $status = $farm->status == 0 ? 'block' : 'none' }}">Turn On</button>
                    </td>
                    <td>{{ $farm->delivery_time }} Hour/s</td>
                    <td class="text-center">
                    	{{ $farm->totalBins }}
                    	<a href="/farms/viewbins/{{ $farm->id }}" class="btn btn-xs btn-info" style="margin-right: 3px;">View Bins</a>
                    </td>
                    <td>
                        <a href="/farms/addbinsbegin/{{ $farm->id }}" class="btn btn-block btn-xs btn-success" style="margin-right: 3px;">Add Bins</a>
                        <a href="/farms/{{ $farm->id }}/edit" class="btn btn-block btn-xs btn-primary" style="margin-right: 3px;">Edit</a>
                        <button class="btn btn-block btn-xs btn-danger view-map pull-right" data-toggle="modal" data-target="#deleteModal{{ $farm->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
</div>


@foreach ($farms as $farm)
<!-- Map Modal -->
<div class="modal fade" id="myModal{{$farm->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Map View for <strong>{{$farm->name}}</strong></h4>
      </div>
      <div class="modal-body">
        <div id="my-map{{$farm->id}}" style="width: 100%; height:400px; background:#DDD;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="deleteModal{{ $farm->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Are you sure you want to delete this farm?<br/>
        	<small class="text-danger">All bins under this farm will be deleted.</small>
    	</p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <a href="/farms/delete/{{ $farm->id }}" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<!--Turn Off Modal -->
<div class="modal fade" id="turnOffModal{{ $farm->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H&H Farm</h4>
      </div>
      <div class="modal-body">
        <p>
        	Choose a date of reactivation<br/>
        	<small class="text-danger">Note: once deactivated, this farm will not be shown in the forecasting page.</small>
    	</p>
        <div class="date_picker_sched form-inline">
            <input type="text" class="form-control input-sm" id="datepickerTurnOff{{ $farm->id }}" value="{{date("M d",strtotime(date("M d") . "+1 days"))}}" placeholder="Select Date">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger btn-turn-off{{ $farm->id }}">Turn Off</a>
      </div>
    </div>
  </div>
</div>
@endforeach



<script type="text/javascript">
var lat;
var lng;

function rebuildCache(){

	$.ajax({
			url		:	app_url+"/cachebuilder",
			type 	:	"GET",
			success: function(r){
            	console.log(r)
			}
		})

}

$(document).ready(function(e) {
	rebuildCache();
	$('.view-map').click(function(){
		lat = $(this).attr('lat');
		lng = $(this).attr('lng');

	});

	var arraypots = ["{{date('Y-m-d')}}"];

	@foreach ($farms as $farm)

	// scheduling page date picker
	$("#datepickerTurnOff{{ $farm->id }}").datepicker({
		controlType: 'select',
		oneLine: true,
		minDate:0,
		dateFormat: 'M d',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShowDay: function(date){
			var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
        	return [ arraypots.indexOf(string) == -1 ]
		},
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// btn turn off
	$(".btn-turn-off{{ $farm->id }}").click(function(e) {

		var farm_id = {{ $farm->id }}

		var reactivation_date = $("#datepickerTurnOff{{ $farm->id }}").val();

		$.ajax({
			url		:	app_url+"/turnoff",
			data 	:	{'reactivation_date':reactivation_date,'farm_id':farm_id},
			type 	:	"POST",
			success: function(r){

				// rebuild cache
				rebuildCache();

				$("#turnOffModal"+farm_id).modal("hide");
				$("#turnoffbtn-"+farm_id).hide();
				$(".status"+farm_id).text("");

				// loading
				$(".loading-"+farm_id).show();
				$(".loading-"+farm_id).delay(15000).fadeOut(200,function() {
						$("#turnonbtn-"+farm_id).show();
						$(".status"+farm_id).text("inactive");
						$(".act_date-"+farm_id).show();
						$(".act_date-"+farm_id).attr("title","Activation Date: "+reactivation_date);
				})

			}
		})

    });

	// btn turn on
	$(".container").delegate("#turnonbtn-{{ $farm->id }}","click",function(e) {
        var farm_id = {{ $farm->id }}

		$.ajax({
			url		:	app_url+"/turnon",
			data 	:	{'farm_id':farm_id},
			type 	:	"POST",
			success: function(r){

				// rebuild cache
				rebuildCache();

				$(".status"+farm_id).text("");
				$(".act_date-"+farm_id).hide();
				$("#turnonbtn-"+farm_id).hide();

				// loading
				$(".loading-"+farm_id).show();
				$(".loading-"+farm_id).delay(15000).fadeOut(200,function() {
					$("#turnoffbtn-"+farm_id).show();
					$(".status"+farm_id).text("active");
				})


			}
		})

    });

	@endforeach
});

	// Google map
	@foreach ($farms as $farm)
	var map{{$farm->id}};
	var defaultLoc{{$farm->id}} = {lat: {{$farm->lattitude}}, lng: {{$farm->longtitude}}}

	$(document).ready(function(e) {
		$('#myModal{{$farm->id}}').on('shown.bs.modal', function () {
			google.maps.event.trigger(map{{$farm->id}}, "resize");
			map{{$farm->id}} = new google.maps.Map(document.getElementById('my-map{{$farm->id}}'), {
				center: defaultLoc{{$farm->id}},
				zoom: 10,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
			placeMarker{{$farm->id}}(defaultLoc{{$farm->id}});
		});
	});

	@endforeach



	var markers = [];


function initialize() {
@foreach ($farms as $farm)
  map{{$farm->id}} = new google.maps.Map(document.getElementById('my-map{{$farm->id}}'), {
	center: defaultLoc{{$farm->id}},
	zoom: 8,
	mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  placeMarker{{$farm->id}}(defaultLoc{{$farm->id}});
@endforeach
}

@foreach ($farms as $farm)
function placeMarker{{$farm->id}}(location) {
	var marker = new google.maps.Marker({
		position: location,
		map: map{{$farm->id}}
	});
	markers.push(marker);
}
@endforeach
</script>



@stop
