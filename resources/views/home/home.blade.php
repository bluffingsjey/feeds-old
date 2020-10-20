@extends('app')

@section('content')
<style>
.release-notes-header {
  background: #154682;
}
.r-close {
  color: #FFFFFF;
}
.r-title {
  color: #FFFFFF;
}
.r-notes-content {
  max-height: 600px;
  overflow-y: scroll;
}
</style>
@include('home.js.home')
<div class="col-md-10">
@include('home.form.schedule')
<div class="row">

		<div class="loading-stick-circle">

        <img src="/css/images/loader-stick.gif" />
        Please wait, Rendering Forecast..

    </div>

    <div class="col-md-12 forecasting-display">
        <div class="panel panel-info">
            <div class="panel-heading">
            	<span class="col-md-3 pull-right" style=" padding: 0px; padding-left: 35px;">
                    <label class="col-sm-4 control-label" style="padding: 0px; padding-top: 5px; margin-left: 30px; text-align: right; font-weight: normal;">Sort: </label>
					<div class="col-sm-6" style="padding: 0px;">
                        <select class="form-control input-sm sort-forecasting" style="height: 22px; margin-top: 2px; line-height: 1;">
                            <option value="1" {{ $sort = ($sort_type == 1) ? "selected" : ""}}>Low Bins</option>
                            <option value="2" {{ $sort = ($sort_type == 2) ? "selected" : "" }}>A-Z Farms</option>
                        </select>
                    </div>
                    <p></p>
                </span>
                <h1 class="panel-title">Forecasting</h1>
                <p>Preview of the farms consumptions for this month.</p>
            </div>
            <div class="panel-body panel-kb">
				 			@include('home.form.alert')
         		</div>
						<!--
						<div class="panel-body panel-kb-2" style="padding:0px 6px;">

         		</div>
						-->
    </div>
</div>

</div>

<div class="modal fade release-notes-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header release-notes-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="r-close">&times;</span></button>
        <h4 class="modal-title r-title" id="myModalLabel">Software Updates</h4>
      </div>
			<input type="hidden" id="release_notes_id" />
      <div class="modal-body r-notes-content">
        ...
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>


<!-- Sticky-->
<script type="text/javascript" src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/charts-loader.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sticky.min.js') }}"></script>

<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart','bar']
            }]
          }"></script>

<script type="text/javascript">
// Load the Visualization API and the piechart package.
//google.charts.load('current', {'packages':['bar']});
google.charts.load('current', {'packages':['corechart','bar']});

var sticky = new Waypoint.Sticky({
  element: $('.schedule-form-stick')[0]
})

var sticky = new Waypoint.Sticky({
  element: $('#pending_del_kb')[0]
})


$.ajax({
	url			:	app_url+'/getreleasenotes',
	type		:	"GET",
	success	:	function(data){
		console.log(data);
		if(data != ""){
			$("#release_notes_id").val(data.id);
			$(".r-notes-content").html("");
			$(".r-notes-content").html(data.description);
			$(".release-notes-modal-lg").modal("show")
		}
	}
});

$(".r-close").click(function(){
	var release_notes_id = $("#release_notes_id").val();
	console.log(release_notes_id);
	$.ajax({
		url			:	app_url+'/updatereleasenotes',
		data		:	{release_notes_id:release_notes_id},
		type		:	"POST",
		success	:	function(data){

		}
	});
});
/*
$(".container").delegate(".loadmore-forecasting","click",function(){
	var skip = $(this).attr('skip');
	var chunk = $(this).attr('chunk');

	if(skip == chunk){
		$(".panel-kb-2").hide();
		return false;
	}
	$(".panel-kb-2").append(loadmore_loading);
	$(this).hide(function(){
		$.ajax({
			url: app_url + "/",
			method: "GET",
			data: {'skip':skip},
			success: function(r) {
				//$("#accordion-one").append(r);
				//$("#load-more-loading").hide();
				//setTimeout(function(){
				if(r != null){
					$(".loadmore-loading").hide(function(){
						$(".loadmore-forecasting").show();
					});

					var value = Number(skip)+1;
					$(".loadmore-forecasting").attr("skip",value);
					$.each( r, function( key, value ) {
						farmsHolder(value.farm_id,value.name,value.delivery_status,value.bins,value.low_bins);
					});
					chunk = Number(chunk)-1;
					if(skip == chunk){
						$(".panel-kb-2").hide();
						return false;
					}
				}

				//},1000)

			}
		});
	});
});
*/
</script>
@stop
