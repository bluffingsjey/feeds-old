<script>
$( function() {
	$( document ).tooltip({
		position: {
			my: "center top+5",
			at: "center bottom",
			using: function( position, feedback ) {
				$( this ).css( position );
				$( "<div>" )
					.addClass( "arrow" )
					.addClass( feedback.vertical )
					.addClass( feedback.horizontal )
					.appendTo( this );
			}
		}
	});
} );
</script>
<style>
.ui-tooltip {
	background: #5bc0de;
	border: 2px solid white;
}
.ui-tooltip {
	padding: 10px;
	color: white;
	border-radius: 5px;
	font: bold 14px "Helvetica Neue", Sans-Serif;
	//text-transform: uppercase;
	text-align: center;
	box-shadow: 0 0 7px black;
}
</style>

<style type="text/css">
.info {
  color: #0084c7;
}
</style>

<table id="drivers_list" class="table table-bordered table-drivers-reports tablesorter">
    <tr>
        <th id="driver">
        <hr class="hr-driver">
        Driver
        <span class="glyphicon glyphicon-question-sign info pull-right" data-toggle="tooltip" data-placement="left" title="The list of drivers names"></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting driver-asc" type="driver" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting driver-desc" type="driver" sort="desc" aria-hidden="true"></span>-->
        </th>
        <th id="tons-delivered">
        <hr class="hr-tons-delivered">
        Tons Delivered
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Amount of feeds delivered into the farm.'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting tons-delivered-asc" type="tons-delivered" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting tons-delivered-desc" type="tons-delivered" sort="desc" aria-hidden="true"></span>-->
        </th>
        <th id="delivery-time">
        <hr class="hr-delivery-time">
        Delivery Time
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Average for period from when geofence shows driving leaving the Mill until geofence shows driver returning to mill. The red or green arrow resprents if they are over or under the estimated time in the Farms Administration page. For example, if a driver delivers 10 loads totaling 20 hours in time per the App geofencing and the Farms Administration page says those loads should have taken 22 hours then the equations would be: Actual Time (20hrs) - Estimated Time (22hrs) = -2hrs / 10 loads = 12 minutes under'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting delivery-time-asc" type="delivery-time" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting delivery-time-desc" type="delivery-time" sort="desc" aria-hidden="true"></span>-->
        </th>
        <th id="drive-time">
        <hr class="hr-drive-time">
        Drive Time
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Actual Time based on App geofencing, compared to Estimated Time from Google. As above, you take the total time for each and divide it by the number of deliveries. For example, if a driver drives for 20 hours total taking 10 loads and Google estimated it should have taken 15 hours for the 10 loads the equation would be: 20hrs - 15hrs = 5hrs / 10 loads = 30 minutes over.'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting drive-time-asc" type="drive-time" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting drive-time-desc" type="drive-time" sort="desc" aria-hidden="true"></span>-->
        </th>
        <th id="time-at-farm">
        <hr class="hr-time-at-farm">
        Time at Farm
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Geofence time from when driver arrives at farm to when driver leaves. As all items, this is an average. Total time at farm for period / number of deliveries for period.'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting time-at-farm-asc" type="time-at-farm" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting time-at-farm-desc" type="time-at-farm" sort="desc" aria-hidden="true"></span>-->
        </th>
        <th id="time-at-mill">
        <hr class="hr-time-at-mill">
        Time at Mill
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Geofence time from when driver arrives at mill until driver leaves again. These times are only tracked between loads...so after the last load of the day we are not counting the time the truck sits at the mill over night.
***For all calculations a "delivery" is considered a truck, if they are two farms loaded on one truck you combine the numbers. For example, if you are talking about driver time for multiple farms: Mill to Farm A + Farm A to Farm B + Farm B to Mill = total drive time for one delivery. Example for Time at Farm: time arrive at Farm A to time departing Farm A + time arrive at Farm B to time departing Farm B = Time at Farm for one delivery.'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting time-at-mill-asc" type="time-at-mill" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting time-at-mill-desc" type="time-at-mill" sort="desc" aria-hidden="true"></span>-->
        </th>
				<th id="total-miles">
        <hr class="hr-total-miles">
        Total Miles
        <span class="glyphicon glyphicon-question-sign info pull-right" rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Total miles driven.'></span>
        <!--<span class="glyphicon glyphicon-triangle-top pull-right sorting time-at-mill-asc" type="time-at-mill" sort="asc" aria-hidden="true"></span>
        <span class="glyphicon glyphicon-triangle-bottom pull-right sorting time-at-mill-desc" type="time-at-mill" sort="desc" aria-hidden="true"></span>-->
        </th>
    </tr>

    @forelse($drivers_list as $k => $v)

    <tr>
        <td>{{$v['driver']}}</td>
        <td>{{$v['tons_delivered']}}</td>
        <td>@if(date('H:i',strtotime($v['delivery_time']['delivery_time'])) == "01:00")
					00:00 Min
					@else
					{{date('H:i',strtotime($v['delivery_time']['delivery_time']))}} Min
					@endif

        	@if($v['delivery_time']['type'] == 'low')
            <span class="glyphicon glyphicon-triangle-top pull-right" aria-hidden="true" style="color:#FF0004"></span>
        	@elseif($v['delivery_time']['type'] == 'equal')
            @else
            <span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true" style="color:#00FF0D"></span>
            @endif
        </td>
        <td>{{date('H:i',strtotime($v['drive_time']['drive_time']))}} Min
        	@if($v['drive_time']['type'] == 'low')
            <span class="glyphicon glyphicon-triangle-top pull-right" aria-hidden="true" style="color:#FF0004"></span>
        	@elseif($v['drive_time']['type'] == 'equal')
            @else
            <span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true" style="color:#00FF0D"></span>
            @endif
        </td>
        <td>{{date('H:i',strtotime($v['time_at_farm']))}} Min</td>
        <td>
        	@if($v['time_at_mill'] == "")
            00:00 Min
            @else
        	{{date('H:i',strtotime($v['time_at_mill']))}} Min
            @endif
        </td>
				<td>
        	@if($v['total_miles'] == "")
            0 Mile/s
            @else
        	{{round($v['total_miles'], 2)}} Mile/s
            @endif
        </td>
    </tr>

    @empty

    <tr>
        <td>Empty</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    @endforelse


</table>

<script type="text/javascript">
$(document).ready(function(e) {
    var table = $('#drivers_list');
	$('#tons-delivered, #delivery-time, #drive-time, #time-at-farm, #time-at-mill')
	.wrapInner('<span title="sort this column"/>')
	.each(function(){

		var th = $(this),
			thIndex = th.index(),
			inverse = false;

		th.click(function(){

			table.find('td').filter(function(){

				return $(this).index() === thIndex;

			}).sortElements(function(a, b){

				return $.text([a]) > $.text([b]) ?
					inverse ? -1 : 1
					: inverse ? 1 : -1;

			}, function(){

				// parentNode is the element we want to move
				return this.parentNode;

			});

			inverse = !inverse;

		});

	});

	$('#driver')
	.wrapInner('<span title="sort this column"/>')
	.each(function(){

		var th = $(this),
			thIndex = th.index(),
			inverse = false;

		th.click(function(){

			table.find('td').filter(function(){

				return $(this).index() === thIndex;

			}).sortElements(function(a, b){

				return $.text([a]) > $.text([b]) ?
					inverse ? -1 : 1
					: inverse ? 1 : -1;


			}, function(){

				// parentNode is the element we want to move
				return this.parentNode;

			});

			inverse = !inverse;

		});

	});
});
</script>
