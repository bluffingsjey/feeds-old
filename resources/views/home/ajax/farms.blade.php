<div style="margin-top:5px;" {{$counter_forecast = $counter + 1}}></div>
@forelse($forecastingData as $farm)
<div class="farm-heading-{{$farm['farm_id']}} panel panel-primary">
  <div class="collapse{{$farm['farm_id']}} panel-heading" data-toggle="collapse" data-parent="#accordion-one" data-target="#collapse{{$farm['farm_id']}}" style="cursor: pointer">
	<h4 class="panel-title">
	  <a data-toggle="collapse" data-parent="#accordion-one" href="#collapse{{$farm['farm_id']}}">{{$farm['name']}}</a>
	  @if($farm['delivery_status'] > 0)
	  <span class="has-pending">PENDING</span>
	  @endif
	</h4>
	<div class="row farm-header-one-{{$farm['farm_id']}}" style="margin-top: 5px;">
		<div class="col-md-12">
			<div class="col-md-2">
				<small>{{$farm['low_bins']}} Bins Low</small>
			</div>
			<div class="col-md-4">
				<div class="progress" style="margin-bottom:0px;">
				@if($farm['bins'][0]['first_list_days_to_empty'] == 0)      		    	
				  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 3%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div>
				@elseif ($farm['bins'][0]['first_list_days_to_empty'] == 1)
					<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Day</div>
				@elseif ($farm['bins'][0]['first_list_days_to_empty'] == 2)
					<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div>  
				@elseif ($farm['bins'][0]['first_list_days_to_empty'] == 3)
					<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div>  
				@elseif ($farm['bins'][0]['first_list_days_to_empty'] == 4)
					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div> 
				@elseif ($farm['bins'][0]['first_list_days_to_empty'] == 5)
					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div> 
				@else
					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">{{$farm['bins'][0]['first_list_days_to_empty']}} Days</div>  	
				@endif 
				</div>
			</div>
			<div class="col-md-6">

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
			<div class="col-md-1 text-center">
				<small>Empty Date</small>
			</div>
			<div class="col-md-1 text-center">
				<small>Incoming Delivery</small>
			</div>
			<div class="col-md-1 text-center">
				<small>Last Delivery</small>
			</div>
			<div class="col-md-1 text-center">
				<small>Last Update</small>
			</div>
			<div class="col-md-2">
				<small>Action</small>
			</div>
		</div>
	</div>
  </div>
  <div id="collapse{{$farm['farm_id']}}" class="collapse{{$farm['farm_id']}} panel-collapse collapse">
	<div class="loading-stick-circle-bins-{{$farm['farm_id']}}" style="width: 200px; margin: 0 auto; padding: 20px;">

		<img src="/css/images/loader-stick.gif" />
		<small>Loading bins data...</small>

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
@if($chunk > $counter_forecast)
<div class="panel panel-success load-more-holder" counter="{{$counter}}">
  <div class="panel-heading" data-toggle="collapse" style="cursor: pointer">
	<h4 class="panel-title text-center">
	  <small>Load More</small>
	</h4>
  </div>
</div>
@endif
 

<script type="text/javascript">
$(document).ready(function(){
	// attach the feed type based on the selected bin
	function loadFeeds(){
		setTimeout(function(){
			$('.feedTypeId').empty();
			$.ajax({
				url :	app_url+"/feedslistshome",
				data: data = {'binID':$('.binNumber').val()},
				type:"post",
				success: function(r){
					if(r.feeds != null){
						$(".feedId").val(r.feed_id);
						$.each(r.feeds, function(i,v){
							selected = i == r.feed_id ? 'selected' : '';
							$('.feedTypeId').append($('<option '+selected+'>').text(v).attr('value',i));
						})
					} else {
						$.each(r, function(i,v){
							$('.feedTypeId').append($('<option>').text(v).attr('value',i));
						})
					}
				}	
			})
			//$(".binNumber").	
		},1000)
	}
	
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
	  showBins({{$farm['farm_id']}});
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
	
	
	@forelse($forecastingData as $farm)
		@forelse($farm['bins'] as $bin)

			// Button update for the pigs
			$(".container").delegate('.btn_test_pigs{{$bin['bin_id']}}','click',function(){
				var numOfPigs = $('#numberOfPigs{{$bin['bin_id']}}').val();
				alert(numOfPigs);	
			})

			// Button update for the bins
			$(".container").delegate('.btn_test_bin{{$bin['bin_id']}}','click',function(){
				var amountOfBins = $('#amountOfBins{{$bin['bin_id']}}').val();
				alert(amountOfBins);	
			})

			// collapse bin div
			$(".container").delegate(".bin-collapse{{$bin['bin_id']}}",'click',function(){
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
				//$('.farmName').val(data['farmName']);
				$('.farmId').val(data['farmId']);
				//$('.binNumber').val(data['binNumber']);
				$('.binId').val(data['binId']);	

				// make the farm name and bin number a select menu

				$.ajax({
					url	:	app_url+"/farmandbins",
					data: data,
					type: "post",
					success: function(r){
						$('.feedTypeId').empty();
						$('.farmName').empty();
						$('.binNumber').empty();

						$.each(r.farms, function(i,v){
							selected = (i == data['farmId'] ? 'selected' : '');
							$('.farmName').append($('<option '+selected+'>').text(v).attr('value',i));
						})
						$.each(r.bins, function(i,v){
							selected = (i == data['binId'] ? 'selected' : '');
							$('.binNumber').append($('<option '+selected+'>').text(v).attr('value',i));
						})
						/*$.each(r.amounts, function(i,v){
							selected = (i == data['binId'] ? 'slected' : '');
							$('.feedTypeId').append($('<option '+selected+'>').text(v).attr('value',i))	
						})*/
						loadFeeds();
					}	
				})					

			});

		@empty

		@endforelse
		
		
	@empty
	
	@endforelse	
	
});	

</script>