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
	background: #ec971f;
	border: 2px solid white;
}
.ui-tooltip {
	padding: 10px;
	color: white;
	border-radius: 5px;
	//font: bold 14px "Helvetica Neue", Sans-Serif;
	//text-transform: uppercase;
	text-align: center;
	box-shadow: 0 0 7px black;
}
</style>

<script type="text/javascript">
var farmIDs = [];
var farm_names = [];
var forecasting_data_home = {!! $forecastingData !!};

var options = {

	  title: 'Consumptions History (Last 6 Updates)',
	  //chartArea:{left:10,top:40,width:"100%",height:"50%"},
	  chartArea:{left:50,top:30},
	  width: 590,
	  height: 290,
	  min:0,
    //bars: 'horizontal'
	  //curveType: 'function',
	  //legend: { position: 'right' }
	};


/*
* Loading
*/
var loading_style = "<div class='loading-stick-circle loadingstyle' style='padding-bottom:200px;'>";
    loading_style += "<img src='/css/images/loader-stick.gif' />";
    loading_style += "Please wait...";
    loading_style += "</div>";

/*
*	Load more data forecasting
*/
var loadmore_button = "<div class='loadmore-forecasting panel panel-primary' skip='1' chunk='' style='margin-bottom: 5px;'>";
		loadmore_button += "<div class='panel-heading' data-toggle='collapse' style='cursor: pointer'>";
		loadmore_button += "<h5 class='panel-title text-center'>Load More</h5>";
		loadmore_button += "</div>";
		loadmore_button += "</div>";

var loadmore_loading = "<div class='loadmore-loading panel panel-primary' style='margin-bottom: 5px;'>";
		loadmore_loading += "<div class='panel-heading' data-toggle='collapse' style='cursor: pointer'>";
		loadmore_loading += "<h5 class='panel-title text-center'>Loading data, please wait...</h5>";
		loadmore_loading += "</div>";
		loadmore_loading += "</div>";

/*
* no manual update notification ui for bins
*/
var bins_no_manual_update = "<br/><span class='no-manual-update no-update-1' rel='tooltip' data-toggle='tooltip' data-placement='bottom' style='font-size: 10px;'>NO MANUAL UPDATE</span>";

/*
*	Forecasting Farms Holder
*/
function farmsHolder(farm_id,farm_name,delivery_status,bins,low_bins) {

var forecasting_holder = "<div class='farm-heading-"+farm_id+" panel panel-primary' style='margin-bottom: 5px;'>";
    forecasting_holder += "<div class='collapse"+farm_id+" panel-heading' data-toggle='collapse' data-parent='#accordion-one' data-target='#collapse"+farm_id+"' id='farm-div-"+farm_id+"' style='cursor: pointer'>";
		forecasting_holder += "<h4 class='panel-title' id='farm-holder-"+farm_id+"'>";
    forecasting_holder += "<a data-toggle='collapse' data-parent='#accordion-one' href='#collapse"+farm_id+"'>"+farm_name+" </a>";
    if(delivery_status > 0){
			forecasting_holder += "<span class='has-pending pending-"+farm_id+"'>PENDING</span>";
    }

		if(bins.update_type != ""){
			forecasting_holder += "<span class='no-manual-update no-update-"+farm_id+"' rel='tooltip' data-toggle='tooltip' data-placement='bottom' ";
			forecasting_holder += "bins='";
			$.each(bins.update_type, function(k,v){
				if(v.update_type != ""){
					forecasting_holder += v.update_type+",";
				}
			});
			forecasting_holder += "'";

			forecasting_holder += ">NO MANUAL UPDATE</span>";
		}
    forecasting_holder += "</h4>";


    forecasting_holder_header_one = "<div class='row farm-header-one-"+farm_id+"' style='margin-top: 5px;'>";
    forecasting_holder_header_one += "<div class='col-md-12'>";
    forecasting_holder_header_one += "<div class='col-md-2'>";
    forecasting_holder_header_one += "<small>"+bins.lowBins+" Bins Low</small>";
    forecasting_holder_header_one += "</div>";
    forecasting_holder_header_one += "<div class='col-md-4'>";
    forecasting_holder_header_one += "<div class='progress' style='margin-bottom:0px;'>";
          			if(bins[0]['first_list_days_to_empty'] == 0){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 3%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }else if (bins[0]['first_list_days_to_empty'] == 1){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 20%;'>"+bins[0]['first_list_days_to_empty']+" Day</div>";
                    }else if (bins[0]['first_list_days_to_empty'] == 2){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 40%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }else if (bins[0]['first_list_days_to_empty'] == 3){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-warning' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 60%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }else if (bins[0]['first_list_days_to_empty'] == 4){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 80%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }else if (bins[0]['first_list_days_to_empty'] == 5){
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 100%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }else{
    forecasting_holder_header_one += "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 100%;'>"+bins[0]['first_list_days_to_empty']+" Days</div>";
                    }
    forecasting_holder_header_one += "</div>";
    forecasting_holder_header_one += "</div>";
    forecasting_holder_header_one += "<div class='col-md-6'>";
		if(bins.notes != null){
		forecasting_holder_header_one += "<small>";
		forecasting_holder_header_one += "Note: ";
		forecasting_holder_header_one += bins.notes;
		forecasting_holder_header_one += "</small>";
		}
    forecasting_holder_header_one += "</div>";
    forecasting_holder_header_one += "</div>";
    forecasting_holder_header_one += "</div>";


	forecasting_holder_header_two = "<div class='row farm-header-two-"+farm_id+"'>";
    forecasting_holder_header_two += "<div class='col-md-12'>";
    forecasting_holder_header_two += "<div class='col-md-2'>";
    forecasting_holder_header_two += "<small>Days to Empty</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-3'>";
    forecasting_holder_header_two += "<span class='col-md-2'>0</span>";
    forecasting_holder_header_two += "<span class='col-md-2'>1</span>";
    forecasting_holder_header_two += "<span class='col-md-2'>2</span>";
    forecasting_holder_header_two += "<span class='col-md-2'>3</span>";
    forecasting_holder_header_two += "<span class='col-md-2'>4</span>";
    forecasting_holder_header_two += "<span class='col-md-2'>5</span>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-1'>";
    forecasting_holder_header_two += "<small>Amount</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-1 text-center'>";
    forecasting_holder_header_two += "<small>Empty Date</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-1 text-center'>";
    forecasting_holder_header_two += "<small>Incoming Delivery</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-1 text-center'>";
    forecasting_holder_header_two += "<small>Last Delivery</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-1 text-center'>";
    forecasting_holder_header_two += "<small>Last Update</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "<div class='col-md-2'>";
    forecasting_holder_header_two += "<small>Action</small>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "</div>";
    forecasting_holder_header_two += "</div>";


	forecasting_holder += forecasting_holder_header_one + forecasting_holder_header_two;

	forecasting_holder += "</div>";


    forecasting_holder_expanded = "<div id='collapse"+farm_id+"' class='collapse"+farm_id+" panel-collapse collapse'>";
    forecasting_holder_expanded += "<div class='loading-stick-circle-bins-"+farm_id+"' style='width: 200px; margin: 0 auto; padding: 20px;'>";
    forecasting_holder_expanded += "<img src='/css/images/loader-stick.gif' />";
    forecasting_holder_expanded += "<small>Loading bins data...</small>";
    forecasting_holder_expanded += "</div>";
    forecasting_holder_expanded += "</div>";

	forecasting_holder += forecasting_holder_expanded;

	forecasting_holder += "</div>";

	$(".panel-kb").append(forecasting_holder);

	$('.farm-header-two-'+farm_id).hide();

	$('.farm-heading-'+farm_id).on('hidden.bs.collapse.collapse'+farm_id, function () {
	  $('.farm-header-two-'+farm_id).hide();
	  $('.farm-header-one-'+farm_id).show();
	  return false;
	})

	$('.farm-heading-'+farm_id).on('show.bs.collapse.collapse'+farm_id, function () {
	  $('.farm-header-two-'+farm_id).show();
	  $('.farm-header-one-'+farm_id).hide();

		var bin_ids = $(".no-update-"+farm_id).attr("bins");

		if(bin_ids){
			bin_ids = bin_ids.slice(0, -1); //"[" + bin_ids + "]";
			bin_ids = bin_ids.split(",").map(Number);
		}
		if($("#collapse"+farm_id).length != 1){
				showBins(farm_id,"enable",bin_ids);
		}
		showBins(farm_id,"enable",bin_ids);

		//console.log($.parseJSON(bin_ids));
	})
	//showBins(farm_id,"disable");

}

/*
* Load the pending deliveries
*/
function loadPending(){
	$.ajax({
		url	:	app_url + '/pendingdeliveries',
		type	:	'get',
		success: function(data){
			$(".loading-stick-circle-pending").delay(60).fadeOut(10,function() {
				$('#pending_del_kb').slideDown(10,function(){
					$(this).html(data);
				});
			});
			$('#pending_del_kb').slideDown(10,function(){
				$(this).html(data);
			});
		}
	});
}

/*
*	Show Bins
*/
function showBins(farm_id,status,bin_ids){
	$.ajax({
		url	:	app_url+"/forecastingbins",
		type: 	"get",
		data: 	data={'farm_id':farm_id},
		success: function(r){


			$(".loading-stick-circle-bins-"+farm_id).hide(function() {

				$(".forecasting-display").show( function(){
					$("#collapse"+farm_id).html("");
					$("#collapse"+farm_id).html(r);
					drawChart();
					$('.lastupdate').bstooltip({
						'placement': 'bottom',
						'container':'body'
					  });

				});

				if(status == "enable"){
					setTimeout(function(){

						var $window = $(window),
							$element = $('#farm-div-'+farm_id),
							elementTop = $element.offset().top,
							elementHeight = $element.height(),
							viewportHeight = $window.height(),
							scrollIt = elementTop - ((viewportHeight - elementHeight) / 2);

						$window.scrollTop(scrollIt+200);
						//window.location.href = app_url+"#collapse"+farm_id;
					},300);
				}

				//setTimeout(function(){
				if(bin_ids){
					$.each(bin_ids, function(k,v){
						$("#a-tag-bin-"+v).append(bins_no_manual_update);
					});
				}
				//},3000);

			});
		}
	});
}

//attach bins on page load  for bin list on add batch
function loadBins(){
	setTimeout(function(){
		$('.binNumber').empty();
		$.ajax({
			url :	app_url+"/binslistshome",
			data: data = {'farmID':$('.farmName').val()},
			type:"get",
			success: function(r){
				if(r.bins != null){
					$(".binId").val(r.bin_id);
					$.each(r.bins, function(i,v){
						selected = i == r.bin_id ? 'selected' : '';
						$('.binNumber').append($('<option '+selected+'>').text(v).attr('value',i));
					})
				} else {
					$.each(r, function(i,v){
						$('.binNumber').append($('<option>').text(v).attr('value',i));
					})
				}
			}
		})
		//$(".binNumber").
	},300)
}


// attach the feed type based on the selected bin
function loadFeeds(){
	setTimeout(function(){
		$('.feedTypeId').empty();
		$.ajax({
			url :	app_url+"/feedslistshome",
			data: data = {'binID':$('.binNumber').val()},
			type:"get",
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
	},500)
}


/*
*	Hide the accordions except for the requested farm
*/
function hideAccordions(farm_id){
	$("#collapse"+farm_id).collapse('show');
	$.each( farmIDs, function( key, value ) {
	  if(value != farm_id){
		$("#collapse"+value).collapse('hide');
	  }
	});
}

/*
*	Sort Data
*/
function sortData(){
	var sort_value = $(".sort-forecasting").val();

	$.ajax({
		url		:	app_url+"/sorter",
		type 	:	"POST",
		data 	:	{'value':sort_value},
		success : 	function(r){
			location.reload();
		}
	});
}

/*
*	Toggle the accordions

function toggleChevron(e) {
	$(e.target)
	.prev('.panel-heading')
	.find('i.indicator')
	.toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}
*/

try {
	home_socket = new WebSocket(home_host);

	//Manages the open event within your client code
	home_socket.onopen = function () {
		console.log("forecasting connection open");
	};

	//Manages the message event within your client code
	home_socket.onmessage = function (msg) {

		forecasting_data = $.parseJSON(msg.data);

		$(".loadingstyle").hide(function(){

			if(forecasting_data == ""){
				$(".panel-kb").html("");
				$(".panel-kb").append("<h4 class='text-center'>No result...</h4>");
			}else {

				$.each( forecasting_data, function( key, value ) {
					farmsHolder(value.farm_id,value.name,value.delivery_status,value.bins,value.low_bins);
				});

			}

		});

		return;

	};

	//Manages the close event within your client code
	home_socket.onclose = function () {
		console.log('forecasting Connection Closed');
		return;
	};

} catch (e) {
	console.log(e);
	//alert("Chat feature not supported by this browser, please use Google Chrome...");
}

/*
*	 Load forecasting data
*/
function loadForecastingData(){
	//home_socket.onopen = function () {
		//home_socket.send(JSON.stringify(message))
		setTimeout(function(){
			$(".loadingstyle").hide(function(){
				$.each( forecasting_data_home, function( key, value ) {
					//console.log(value);
					farmsHolder(value.farm_id,value.name,value.delivery_status,value.bins,value.low_bins);
				});
				//$(".panel-kb-2").append(loadmore_button);
			});
		},500);
	//}
}

$(document).ready(function(){
	//console.log(forecasting_data_home);
	$("#search_farm").keyup(function(){
		if(event.keyCode == 13) {
			// show the loading
			$(".panel-kb").html("");
			$(".panel-kb").append(loading_style);
			$(".panel-kb-2").hide();
			message = {
				'search_query'		:	$(this).val(),
				'sort'				:	$(".sort-forecasting").val()
			};

			home_socket.send(JSON.stringify(message))

			$(".ui-menu-item").hide();
		}
	})

	loadForecastingData();

	@forelse($farms_list as $k => $v)
		@if($k != "")
		farmIDs.push({{$k}});
		farm_names.push("{{$v}}");

		$(".container").delegate("#farm-div-{{$k}}","click",function(){
			setTimeout(function(){
				var $window = $(window),
					$element = $('#farm-div-{{$k}}'),
					elementTop = $element.offset().top,
					elementHeight = $element.height(),
					viewportHeight = $window.height(),
					scrollIt = elementTop - ((viewportHeight - elementHeight) / 2);
					$window.scrollTop(scrollIt);
			},300)

			hideAccordions({{$k}});

			setTimeout(function(){
				$("#collapse").collapse('show');
				var $window = $(window),
					$element = $('#farm-div-{{$k}}'),
					elementTop = $element.offset().top,
					elementHeight = $element.height(),
					viewportHeight = $window.height(),
					scrollIt = elementTop - ((viewportHeight - elementHeight) / 2);

				$window.scrollTop(scrollIt);
			},1000);

		})

		$('.farm-header-two-{{$k}}').hide();

		$('.farm-heading-{{$k}}').on('hidden.bs.collapse.collapse{{$k}}', function () {
		  $('.farm-header-two-{{$k}}').hide();
		  $('.farm-header-one-{{$k}}').show();
		  return false;
		})

		$('.farm-heading-{{$k}}').on('show.bs.collapse.collapse{{$k}}', function () {
		  $('.farm-header-two-{{$k}}').show();
		  $('.farm-header-one-{{$k}}').hide();
			var bin_ids = $(".no-update-{{$k}}").attr("bins");
			if(bin_ids != ""){
				bin_ids = bin_ids.slice(0, -1); //"[" + bin_ids + "]";
				bin_ids = bin_ids.split(",").map(Number);
			}
			showBins({{$k}},"enable",bin_ids);
		})
		@endif
	@empty
	@endforelse


	/*
	*	Search farm button
	*/
	$(".btn-search-farm").click(function(){

		var search_query = $("#search_farm").val();

		// show the loading
		$(".panel-kb").html("");
		$(".panel-kb").append(loading_style);
		$(".panel-kb-2").hide();

		// trigger websocket request
		var msg = $(this).val();
		if (msg != "" || msg != null){
			message = {
					'search_query'		:	search_query,
					'sort'				:	$(".sort-forecasting").val()
				};
			home_socket.send(JSON.stringify(message))
		}

	})

	$(".container").delegate(".sort-forecasting","change",function(e) {

		$(".forecasting-display").hide(function(){
			$(".loading-stick-circle").show();
		});

		sortData();
		//$(".panel-kb").html("");
		//$(".panel-kb").append(loading_style);
		//$(".panel-kb-2").hide();
		/*
		message = {
			'search_query'		:	"all",
			'sort'				:	$(this).val()
		};

		home_socket.send(JSON.stringify(message))
		*/

  });

	$('.sched-form-top-fix').affix({offset: {top: 0} });
	//$('#accordion-one').on('hidden.bs.collapse', toggleChevron);
	//$('#accordion-one').on('shown.bs.collapse', toggleChevron);


	// update bin
	$(".container").delegate(".updateBin",'click',function() {

		var bin = $(this).attr("bin-number");
		var amount = $("#amountOfBins"+bin).val();
		var feedt = $(".curfeedt"+bin).text();
		var num_of_pigs = $(this).attr("pigs");

		$("#bin-modal"+bin).modal("hide");

		if(feedt != "None") {

			$("#forecasting-alert").modal("show");

			$.ajax({

				url: app_url + "/json/updatebinhistory",
				dateType: "json",
				method: "POST",
				data: {bin:bin, amount:amount,num_of_pigs:num_of_pigs},
				success: function(e) {

					if(e == "no pigs"){

						alert("There's no pigs detected on this bin, please add pigs first, before updating this bin");

					} else {

						var r = $.parseJSON(e);
						$(".mytons"+bin +" small").text(amount.replace(".0","") + " T");
						$(".myempty"+bin +" small").text(r.empty);
						$(".myprog"+bin).css("width", r.percentage + "%");
						$(".myprog"+bin).removeClass("progress-bar-warning");
						$(".myprog"+bin).removeClass("progress-bar-success");
						$(".myprog"+bin).removeClass("progress-bar-danger", function() {

							$(".myprog"+bin).addClass("progress-bar-"+r.color);

						});
						$(".myprog"+bin).text(r.text);
						$("lstupd"+bin).text(r.tdy);

						if(r.avg_variance != 0.0){
							$(".avg_variance"+bin).text(r.avg_variance+" lbs");
							$(".avg_actual"+bin).text(r.avg_actual+" lbs");
						}

						$(".amount-expanded-"+bin).text("");
						$(".amount-expanded-"+bin).text($("#amountOfBins"+bin+" option:selected").text());

						graphReload(bin,num_of_pigs);

						$("#forecasting-alert").modal('hide');
						$(".no-update-"+r.farm_id).remove();

					}

				}


			});

		} else {

			alert("Bin has no Default Feed to start with, Please load a Delivery First");

		}

	});


	// update pigs
	$(".container").delegate(".updatePig","click",function() {

		var bin = $(this).attr("bin-number");
		var farm_id = $(this).attr("farm-id");
		var numpigs = $('.numpigsupdate'+bin).map(function(){
			return this.value;
		}).get();

		var animal_unique_id = $('.numpigsupdate'+bin).map(function(){
			return $(this).attr("animal-unique-id");
		}).get();

		$.ajax({

			url: app_url + "/json/updatehistory",
			dateType: "json",
			method: "POST",
			data: {
					'bin'					:	bin,
					'farm_id'				: 	farm_id,
					'numpigs[]'				:	numpigs,
					'animal_unique_id[]'	:	animal_unique_id
					},
			success: function(e) {
					$(".pigvalue"+e[0]['bin']).text(e[0]['total_number_of_pigs']);
					$(".total-pigs"+e[0]['bin']).text(e[0]['total_number_of_pigs'])
					$("#numberOfPigs"+e[0]['bin']+"-"+e[0]['unique_id']).val(e[0]['numofpigs']);

					$(".myempty"+e[0]['bin']+" small").text(e[0]['empty']);
					$(".myprog"+e[0]['bin']).css("width", e[0]['percentage'] + "%");
					$(".myprog"+e[0]['bin']).removeClass("progress-bar-warning");
					$(".myprog"+e[0]['bin']).removeClass("progress-bar-success");
					$(".myprog"+e[0]['bin']).removeClass("progress-bar-danger", function() {

						$(".myprog"+e[0]['bin']).addClass("progress-bar-"+e[0]['color']);

					});
					$(".myprog"+e[0]['bin']).text(e[0]['text']);
					$("lstupd"+e[0]['bin']).text(e[0]['tdy']);
			}


		});
	});

	/*
	*	Truck dropdown
	*/
	$(".truckId").change(function(e) {

		var truck_data = $(this).val();

		$.ajax({
			url 	:	app_url+'/updatepdtruck',
			type 	:	'POST',
			data 	:	{'truck_id':truck_data},
			success: function(r){
				if(r == 1){
					loadPending();
				}
			}
		});

    });

	$(".loading-stick-circle").fadeOut(100,function() {

		$(".forecasting-display").slideDown(100);
		$(".panel-kb").append(loading_style);

	});


	loadPending();

	// save schedule
	$(".container").delegate("#btn-save-sched",'click',function(){

		var schedData = {
				'farmId' 			: 	$('.farmId').val(),
				'binId'				:	$('.binId').val(),
				'farmName'			:	$('.farmName').val(),
				'binNumber'			:	$('.binNumber').val(),
				'medicationId'		:	$('.medicationId').val(),
				'feedTypeId'		:	$('.feedTypeId').val(),
				'feedAmount'		:	$('.feedAmount').val(),
				'dateSched'			:	$('.dateSched').val(),
				'timeOfTheDaySched'	:	$('.time_of_the_day').val(),
				'truckId'			:	$('.truckId').val(),
				'driverId'			:	$('.driverId').val()
		}

		$(this).attr("disabled",true);
		$(this).text("");
		$(this).text("Adding batch please wait...");

		//console.log(schedData);

		var emptyData = [];
		// validation
		$.each(schedData, function(key,value){
			if(value == null || value == ""){
				emptyData.push(key);
			}
		})

		var message = "";
		var emptyLength = emptyData.length;
		for(var i = 0; i<emptyLength; i++){
			if(emptyData[i] == 'farmId'){
				message += "<br/>Farm should not be empty.";
			}
			if(emptyData[i] == 'binNumber'){
				message += "<br/>Bin should not be empty.";
			}
			/*if(emptyData[i] == 'medicationId'){
				message += "<br/>Medication should not be empty.";
			}*/
			if(emptyData[i] == 'feedTypeId'){
				message += "<br/>Feed type should not be empty.";
			}
			if(emptyData[i] == 'feedAmount'){
				message += "<br/>Amount should not be 0.";
			}
			if(emptyData[i] == 'truckId'){
				message += "<br/>Truck should not be empty.";
			}
		}

		if(message != ""){
			$('.modalMessage').text("");
			$('.modalMessage').html(message);
			$("#schedModal").modal();

			$(this).attr("disabled",false);
			$(this).text("");
			$(this).text("Add batch");
		} else {

			var tobeAddedBatchAmount = schedData['feedAmount'];
			tobeAddedBatchAmount = tobeAddedBatchAmount.replace(" Tons","");

			var batchAmount = $("#totalBatchAmount").text();

			var totalBatchAmount = parseFloat(tobeAddedBatchAmount) + parseFloat(batchAmount);

			var totalTruckCapacity = parseFloat($("#totalTruckCapacity").text());

			if(batchAmount == totalTruckCapacity){
				$('.modalMessage').text("");
				$('.modalMessage').html("The truck is full, you cannot add new batch, you can now schedule the delivery.");
				$("#schedModal").modal();
			}else{
				if(totalBatchAmount > totalTruckCapacity){
					$('.modalMessage').text("");
					$('.modalMessage').html("The total feed amount will be greater than the truck capacity, please choose lesser feed amount.");
					$("#schedModal").modal();
				}else {


					$.ajax({
						url		:	app_url + '/saveSchedHome',
						type	:	'get',
						data 	:	schedData,
						success	: 	function(e){
							$('.modalMessage').text("");
							if(e == "failed"){
								$('.modalMessage').text("Batch schedule already added!");
								$("#schedModal").modal();
							}else{
								//$('.modalMessage').text("Batch schedule added!");
							}
							$(".pending_del_kb").hide(function(){
									$(".loading-stick-circle-pending").show();
							});

							loadPending();
							$("#btn-save-sched").attr("disabled",false);
							$("#btn-save-sched").text("");
							$("#btn-save-sched").text("Add batch");
						}
					});

				}
			}
		}

	})

	// pending edit
	$(".container").delegate('.pend_edit','click',function(){
		var deliveryID = $(this).attr("delId");
		$("#editPendingBatchModal").modal();
		$(".btn-edit-batch").attr('delId',deliveryID);

		$.ajax({
			url		:	app_url+"/updatepending",
			type	:	"POST",
			data 	:	data={'del_id':deliveryID},
			success: function(r){
				$(".pending-update-holder").html("");
				$(".pending-update-holder").append(r);
			}
		});
	})

	// Pending update batch
	$(".container").delegate('.btn-update-batch','click',function(){
		// get the corresponding data
		var delivery_id = $(this).attr('del-id');
		var feed_type_id = $(".feed_type_"+delivery_id).val();
		var medication_id = $(".medication_"+delivery_id).val();
		var amount = $(".amount_"+delivery_id).val();

		var batch = {
				'delivery_id'	: 	delivery_id,
				'feed_type_id'	:	feed_type_id,
				'medication_id'	:	medication_id,
				'amount'		:	amount
			}

		console.log(batch);
		// update the batch
		$.ajax({
			url		:	app_url+"/updatesavepending",
			type 	:	"POST",
			data 	: 	batch,
			success : 	function(r){
				if(r.status == 'fail'){
					$('.alert-holder').html("");
					$('.alert-holder').append(r.message);
				} else {
					// reload the pending batch list
					loadPending();
					$("#editPendingBatchModal").modal("hide");
				}
			}
		});

	});

	// pending delete modal trigger
	$(".container").delegate('.pend_del','click',function(){
		var deliveryID = $(this).attr("delId");
		$("#delPendingBatchModal").modal();
		$(".btn-delete-batch").attr('delId',deliveryID);
	})

	// pending confirm delete
	$(".btn-delete-batch").click(function(){
		var deliveryID = $(this).attr("delid");
		console.log(deliveryID);
		$.ajax({
			url		:	app_url + '/deletebatch',
			type	:	'POST',
			data 	:	{'delivery_id' : deliveryID},
			success	: 	function(e){
				loadPending();
			}
		})
	});


	// Schedule Delivery
	$(".container").delegate('#pend_btn_sched','click',function(){

		$(this).attr("disabled","disabled");
		$(this).text("Please Wait...");


		var date_of_delivery = $(this).attr("date-of-sched");
		var truck_id = $(this).attr("truck-id");
		var driver_id = $(this).attr("driver-id");

		$.ajax({
			url		:	app_url + '/movetosched',
			type	:	'POST',
			data 	:	data={
				'date_of_delivery':date_of_delivery,
				'truck_id'	:	truck_id,
				'driver_id'	:	driver_id
				},
			success	: function(e){
				if(e == "success"){
					//$("#scheduleDeliveryModal").modal();
					loadPending();
					appendPending();
				}else{
					$('.modalMessage').text("");
					$('.modalMessage').html("Saving schedule failed, something went wrong, please try again.");
					$("#schedModal").modal();
				}
			}
		})
	});

	// go to schedule deliveries page
	$(".btn-save-batch").click(function(){
		window.location = app_url + "/loading";
	})

	// refresh forecasting page
	$(".btn-save-batch-no").click(function(){
		window.location.reload();
	});

	// call the loadbins
	loadBins()
	loadFeeds()



	// farm on add batch home
	$(".container").delegate('.farmName','change',function(){
		loadBins();
		$('.farmId').val("");
		$('.farmId').val($(this).val());

		var farm_id = $(this).val();

		$('.farm-header-two-'+farm_id).show();
		$('.farm-header-one-'+farm_id).hide();

		var bin_ids = $(".no-update-"+farm_id).attr("bins");
		if(bin_ids){
			bin_ids = bin_ids.slice(0, -1); //"[" + bin_ids + "]";
			bin_ids = bin_ids.split(",").map(Number);
		}

		showBins(farm_id,"enable",bin_ids);

		$.each( farmIDs, function( key, value ) {
		  if(value != farm_id){
			$("#collapse"+value).collapse('hide');
		  }
		});

		var $window = $(window),
			$element = $('#farm-div-'+farm_id),
			elementTop = $element.offset().top,
			elementHeight = $element.height(),
			viewportHeight = $window.height(),
			scrollIt = elementTop - ((viewportHeight - elementHeight) / 2);

		$window.scrollTop(scrollIt);

		setTimeout(function(){
			$("#collapse"+farm_id).collapse('show');
			var $window = $(window),
				$element = $('#collapse'+farm_id),
				elementTop = $element.offset().top,
				elementHeight = $element.height(),
				viewportHeight = $window.height(),
				scrollIt = elementTop - ((viewportHeight - elementHeight) / 2);

			$window.scrollTop(scrollIt);
			loadFeeds();
		},1000);

	})

	// bin on add batch home
	$(".container").delegate('.binNumber','change',function(){
		$('.binId').val("");
		$('.binId').val($(this).val());
		loadFeeds();
	})

	// feeds on add batch home
	$(".container").delegate('.feedTypeId','change',function(){
		//$('.feedTypeId').val("");
		$('.feedTypeId').val($(this).val());
	})

	/*
	*	Search Farm
	*/
	$("#search_farm").autocomplete({
		source: farm_names
    });


})

</script>
