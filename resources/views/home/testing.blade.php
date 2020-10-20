<script type="text/javascript">
// make a loop that will handle the collapse div
/*
*	#accordion-one is the main handle accordion
*	
*/
$(document).ready(function(e) {
	
	$('.sched-form-top-fix').affix({offset: {top: 0} }); 
	
	
	@forelse($forecastingData as $farm)
	$('.farm-header-two-{{$farm['farm_id']}}').hide();
	@empty
	@endforelse
	
   function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find('i.indicator')
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
	}
	$('#accordion-one').on('hidden.bs.collapse', toggleChevron);
	$('#accordion-one').on('shown.bs.collapse', toggleChevron); 
	
	@forelse($forecastingData as $farm)
	$('.farm-heading-{{$farm['farm_id']}}').on('hidden.bs.collapse.collapse{{$farm['farm_id']}}', function () {
	  $('.farm-header-two-{{$farm['farm_id']}}').hide();
	  $('.farm-header-one-{{$farm['farm_id']}}').show();
	  return false;
	})
	@empty
	@endforelse
	  
	@forelse($forecastingData as $farm)
	$('.farm-heading-{{$farm['farm_id']}}').on('show.bs.collapse.collapse{{$farm['farm_id']}}', function () {
	  $('.farm-header-two-{{$farm['farm_id']}}').show();
	  $('.farm-header-one-{{$farm['farm_id']}}').hide();
	})
	@empty
	@endforelse
	
	@forelse($forecastingData as $farm)
		@forelse($farm['bins'] as $bin)
			$('#collapseinline{{$bin['bin_id']}}').on('hidden.bs.collapse.collapse{{$farm['farm_id']}}', function () {
			  	$('.farm-header-two-{{$farm['farm_id']}}').show();
	  			$('.farm-header-one-{{$farm['farm_id']}}').hide();
				return false;
			})
		@empty
		
		@endforelse
	@empty
	@endforelse
	
	
});
</script>


<div class="panel-group" id="accordion-one">
@forelse($forecastingData as $farm)
    <div class="farm-heading-{{$farm['farm_id']}} panel panel-primary">
      <div class="collapse{{$farm['farm_id']}} panel-heading" data-toggle="collapse" data-parent="#accordion-one" data-target="#collapse{{$farm['farm_id']}}" style="cursor: pointer">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion-one" href="#collapse{{$farm['farm_id']}}">{{$farm['name']}}</a>
        </h4>
        <div class="row farm-header-one-{{$farm['farm_id']}}">
        	<div class="col-md-12">
            	<div class="col-md-2">
            		<small>{{$farm['low_bins']}} Bins Low</small>
        		</div>
                <div class="col-md-4">
                	<div class="progress" style="margin-bottom:0px;">
          			@if($farm['bins'][0]['days_to_empty'] == 0)      		    	
                      <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 3%;">{{$farm['bins'][0]['days_to_empty']}} Days</div>
                    @elseif ($farm['bins'][0]['days_to_empty'] == 1)
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">{{$farm['bins'][0]['days_to_empty']}} Days</div>
                    @elseif ($farm['bins'][0]['days_to_empty'] == 2)
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">{{$farm['bins'][0]['days_to_empty']}} Days</div>  
                    @elseif ($farm['bins'][0]['days_to_empty'] == 3)
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">{{$farm['bins'][0]['days_to_empty']}} Days</div>  
                    @elseif ($farm['bins'][0]['days_to_empty'] == 4)
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">{{$farm['bins'][0]['days_to_empty']}} Days</div> 
                    @elseif ($farm['bins'][0]['days_to_empty'] == 5)
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">{{$farm['bins'][0]['days_to_empty']}} Days</div> 
                    @else
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">{{$farm['bins'][0]['days_to_empty']}} Days</div>  	
                    @endif 
                    </div>
                </div>
                <div class="col-md-6">
                	<small>@if($bin['days_to_empty'] == 0)
                            Empty
                            @elseif($bin['days_to_empty'] === 0)
                            Empty
                            @else
                            {{$bin['current_bin_amount_tons']}}
                            @endif
                            - {{$farm['bins'][0]['empty_date']}}</small>
                </div>
            </div>
        </div>
        <div class="row farm-header-two-{{$farm['farm_id']}}">
            <div class="col-md-12">
                <div class="col-md-2">
                    <small>Days to Empty</small>
                </div>
                <div class="col-md-3">
                    <span class="col-md-2">0</span>
                    <span class="col-md-2">1</span>
                    <span class="col-md-2">2</span>
                    <span class="col-md-2">3</span>
                    <span class="col-md-2">4</span>
                    <span class="col-md-2">5</span>
                </div>
                <div class="col-md-1">
                    <small>Amount</small>
                </div>
                <div class="col-md-2 text-center">
                    <small>Empty Date</small>
                </div>
                <div class="col-md-2 text-center">
                    <small>Last Update</small>
                </div>
                <div class="col-md-2">
                    <small>Action</small>
                </div>
            </div>
        </div>
      </div>
      <div id="collapse{{$farm['farm_id']}}" class="collapse{{$farm['farm_id']}} panel-collapse collapse">
      	<div class="panel-group" id="accordion{{$farm['farm_id']}}">
        <!--<ul class="list-group">-->
        @forelse($farm['bins'] as $bin)	
        	<!--Update # of Pigs Popup-->
            @include('home.form.pigs')
            <!--Update # of Bins Popup-->
            @include('home.form.bin')
        
          <div class="panel panel-info">
          	  <div class="panel-heading">
          		<div class="bin-collapse{{$bin['bin_id']}}" data-toggle="collapse" farm-name="{{$farm['name']}}" farm-id="{{$farm['farm_id']}}" bin-number="{{$bin['bin_number']}}" bin-id="{{$bin['bin_id']}}" data-parent="#accordion{{$farm['farm_id']}}" data-target="#collapseinline{{$bin['bin_id']}}" style="cursor: pointer">
           		</div>
                <div class="row" data-toggle="collapse" data-parent="#accordion{{$farm['farm_id']}}" data-target="#collapseinline{{$bin['bin_id']}}" style="cursor: pointer">
                    <div class="col-md-12 bin-collapse{{$bin['bin_id']}}" data-toggle="collapse" farm-name="{{$farm['name']}}" farm-id="{{$farm['farm_id']}}" bin-number="{{$bin['bin_number']}}" bin-id="{{$bin['bin_id']}}" style="z-index:0;">
                        <div class="col-md-2">
                            <a class="bin-collapse{{$bin['bin_id']}}" data-toggle="collapse" farm-name="{{$farm['name']}}" farm-id="{{$farm['farm_id']}}" bin-number="{{$bin['bin_number']}}" bin-id="{{$bin['bin_id']}}" data-parent="#accordion{{$farm['farm_id']}}" href="#collapseinline{{$bin['bin_id']}}">Bin #{{$bin['bin_number']}} - {{$bin['alias']}}</a>
                        </div>
                        <div class="col-md-3">
                            <div class="progress" style="margin-bottom:0px;">
                            @if ($bin['days_to_empty'] == 0)
                              <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 1%;"></div>
                            @elseif ($bin['days_to_empty'] == 1)
                            	<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 20%;"></div>
                            @elseif ($bin['days_to_empty'] == 2)
                          		<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 40%;"></div>  
                            @elseif ($bin['days_to_empty'] == 3)
                          		<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>  
                            @elseif ($bin['days_to_empty'] == 4)
                           		<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%;"></div> 
                            @elseif ($bin['days_to_empty'] == 5)
                           		<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div> 
                            @else
                          		<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">{{$bin['days_to_empty']}} Days</div>  	
                            @endif 
                            </div>
                        </div>
                        <div class="col-md-1">
                        	@if($bin['days_to_empty'] == 0)
                            <small>Empty</small>
                            @else
                            <small>{{$bin['current_bin_amount_tons']}}</small>
                            @endif
                        </div>
                        <div class="col-md-2 text-center">
                            <small>{{$bin['empty_date']}}</small>
                        </div>
                        <div class="col-md-2 text-center">
                        	@forelse($bin['last_update'] as $k => $v)
                            <small>{{date('m/d - h:m a',strtotime($v['update_date']))}}</small>
                            @empty
                            <small>None Yet</small>
                            @endforelse
                        </div>
                        <div class="col-md-2">
                            <small><button class="btn btn-xs btn-block btn-info btn-update-bin" data-toggle="modal" data-target="#bin-modal{{$bin['bin_id']}}" style="margin-top:5px; z-index:9;">Update Bin Level</button></small>
                        </div>
                    </div>
                </div>   
              </div>  
              <div id="collapseinline{{$bin['bin_id']}}" class="collapseinline{{$bin['bin_id']}} panel-collapse collapse panel-body">
              	<div class="col-md-12">
                	<div class="col-md-4">
                    	<dl class="">
                          <dt>Current Feed:</dt>
                          <dd>{{$bin['feed_type_name']}}</dd>
                        </dl>
                        <dl class="">
                          <dt>Next Delivery:</dt>
                          <dd>{{$bin['next_delivery']}}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                    	<dl class="">
                          <dt>Current Medication:</dt>
                          <dd>{{$bin['medication']}}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                    	<dl class="">
                          <dt>Number of Pigs: <small><button class="btn btn-xs btn-warning btn-update-bin" data-toggle="modal" data-target="#pigs-modal{{$bin['bin_id']}}">Update # of Pigs</button></small></dt>
                          <dd>{{$bin['num_of_pigs']}}</dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
                    	
                        <div class="col-md-9">
                        	<div id="curve_chart{{$bin['bin_id']}}" style="width: 610px; height: 300px;"></div>
                        </div>
                        <div class="col-md-3">
                        	<dl class="">
                              <dt>Variance:</dt>
                              <dd>{{$bin['average_variance']}}</dd>
                            </dl>
                            <dl class="">
                              <dt>Actual:</dt>
                              <dd>{{$bin['average_actual']}}</dd>
                            </dl>
                            <dl class="">
                              <dt>Budgeted:</dt>
                              <dd>{{$bin['budgeted_amount']}}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
              </div>
          </div>
          @empty
          <div class="panel panel-default">No bins for this farm.</div>
          @endforelse 
        <!--</ul>-->
        </div>
      </div>
    </div>
    
 @empty
 	<div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">No data yet.</h4>
      </div>
    </div>
 @endforelse   
</div> 


 <!-- Sticky-->
<script type="text/javascript" src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sticky.min.js') }}"></script>

<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>
          
<script type="text/javascript">

google.setOnLoadCallback(drawChart);

      function drawChart() {
        
		  @forelse($forecastingData as $farm)
		  	@forelse($farm['bins'] as $bin)
				var data{{$bin['bin_id']}} = google.visualization.arrayToDataTable([
          		["Date", "Actual (Tons)", "Budgeted (Tons)"],
				@forelse($bin['graph_data'] as $graph)
				['{{date('M j',strtotime($graph['update_date']))}}',{{round($graph['consumption']/2000,2)}},{{$bin['budgeted_amount']}}],
				@empty
				['none', 0,0],
				['none', 0,0],
				['none', 0,0],
				['none', 0,0],
				['none', 0,0],
				['none', 0,0],
		  		@endforelse	
				]);
			@empty
		  	@endforelse
		  @empty
		  @endforelse
        

        var options = {
          title: 'Consumptions History (Last 6 Updates)',
		  //chartArea:{left:30,top:50,width:"100%",height:"70%"},
		  chartArea:{left:30,top:50},
		  width: 600,
          height: 280,
		  min:0,
		  curveType: 'function',
          legend: { position: 'right' }
        };

		
		
		@forelse($forecastingData as $farm)
			@forelse($farm['bins'] as $bin)
				var chart{{$bin['bin_id']}} = new google.visualization.LineChart(document.getElementById('curve_chart{{$bin['bin_id']}}'));
				chart{{$bin['bin_id']}}.draw(data{{$bin['bin_id']}}, options);
			@empty
			
			@endforelse
		@empty
		@endforelse

        
      }
	  
$(document).ready(function(){
	
	var sticky = new Waypoint.Sticky({
	  element: $('.schedule-form-stick')[0]
	})
	
	$(".loading-stick-circle").delay(1000).fadeOut(200,function() {
		
		$(".forecasting-display").slideDown(200);	
		
	});
	
	
	// save schedule
	$("#btn-save-sched").click(function(){
		
		var schedData = {
				'farmId' 		: 	$('.farmId').val(),
				'binId'			:	$('.binId').val(),
				'farmName'		:	$('.farmName').val(),
				'binNumber'		:	$('.binNumber').val(),
				'medicationId'	:	$('.medicationId').val(),
				'feedTypeId'	:	$('.feedTypeId').val(),
				'feedAmount'	:	$('.feedAmount').val(),
				'dateTimeSched'	:	$('.dateSched').val(),
				'truckId'		:	$('.truckId').val(),
				'driverId'		:	$('.driverId').val()
		}
			
		// validation
		$.each(schedData, function(key,value){
			
			if(value == ""){
				
				console.log( key + ": " + value );
				$('.modalMessage').text("");
				$('.modalMessage').text("Incomplete schedule form");
				$("#schedModal").modal();
				
				return false;
				
			} else {
				
				$.ajax({
					url	:	'http://feeds.carrierinsite.com/saveSchedHome',
					type	:	'POST',
					data 	:	schedData,
					success: function(){
						$('.modalMessage').text("");
						$('.modalMessage').text("Bin successfully scheduled");
						$("#schedModal").modal();
					}
				});
				
				return false;
			}
			
		})
			
	})
	
	
	@forelse($forecastingData as $farm)
			@forelse($farm['bins'] as $bin)
				
				// Button update for the pigs
				$('.btn_test_pigs{{$bin['bin_id']}}').click(function(){
					var numOfPigs = $('#numberOfPigs{{$bin['bin_id']}}').val();
					alert(numOfPigs);	
				})
				
				// Button update for the bins
				$('.btn_test_bin{{$bin['bin_id']}}').click(function(){
					var amountOfBins = $('#amountOfBins{{$bin['bin_id']}}').val();
					alert(amountOfBins);	
				})
				
				// collapse bin div
				$(".bin-collapse{{$bin['bin_id']}}").click(function(){
					var data = {
							'farmId'	:	$(this).attr("farm-id"),
							'farmName'	:	$(this).attr("farm-name"),
							'binId'		:	$(this).attr("bin-id"),
							'binNumber'	:	$(this).attr("bin-number")
						}
					//Clear input feilds
					$('.farmName').val("");
					$('.farmId').val("");
					$('.binNumber').val("");
					$('.binId').val("");
					//Add the data
					$('.farmName').val(data['farmName']);
					$('.farmId').val(data['farmId']);
					$('.binNumber').val(data['binNumber']);
					$('.binId').val(data['binId']);	
					
				});
				
			@empty
			
			@endforelse
		@empty
	@endforelse	
})	  
	  
</script>