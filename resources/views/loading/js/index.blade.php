<script src="{{ asset('js/Simple-Daily-Schedule/js/jq.schedule.js') }}?f=<?=date("YmdHis");?>"></script>
<link rel="stylesheet" href="{{ asset('js/Simple-Daily-Schedule/css/style.css') }}?f=<?=date("YmdHis");?>">
<style type="text/css">
.active-drop-down{
	cursor: pointer !important;
    background: rgb(255, 255, 255) !important;
}
@media print {
  @page {
    size: 297mm 210mm; /* landscape */
    /* you can also specify margins here: */
    margin: 25mm;
    margin-right: 45mm; /* for compatibility with both A4 and Letter */
  }
}
.active-green {
	background: #6bff6f !important;
}
</style>

<script type="text/javascript" src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sticky.min.js') }}"></script>

<script type="text/javascript">
var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
];

function datePickerDateGetter(){
	var date_picker_date = $("#datepickerSchedTool").val() + ",{{date("Y")}}";
	var formattedDate = new Date(date_picker_date);
	var d = formattedDate.getDate();
	var m =  formattedDate.getMonth();
	m += 1;  // JavaScript months are 0-11
	var y = formattedDate.getFullYear();

	return (m + "." + d + "." + y);
}

function nextDate(date_picker_date){
	var date = new Date(date_picker_date);
	date.setDate(date.getDate() + 1);
	var nd = new Date(date);
	var d = nd.getDate();
	var m =  nd.getMonth();
	m += 1;  // JavaScript months are 0-11
	var y = nd.getFullYear();

	d_string = d.toString();
	d_length = d_string.length
	if(d_length == 1){
		d_string = "0"+d_string;
	} else {
		d_string = d_string;
	}

	$("#datepickerSchedTool").val(monthNames[date.getMonth()]+" "+d_string)

	return monthNames[date.getMonth()]+" "+d+", "+y;

}

function previousDate(date_picker_date){
	var date = new Date(date_picker_date);
	date.setDate(date.getDate() - 1);
	var nd = new Date(date);
	var d = nd.getDate();
	var m =  nd.getMonth();
	m += 1;  // JavaScript months are 0-11
	var y = nd.getFullYear();

	d_string = d.toString();
	d_length = d_string.length
	if(d_length == 1){
		d_string = "0"+d_string;
	} else {
		d_string = d_string;
	}

	$("#datepickerSchedTool").val(monthNames[date.getMonth()]+" "+d_string)

	return monthNames[date.getMonth()]+" "+d+", "+y;

}

//var newdate = new Date(date);
//newdate.setDate(newdate.getDate() - 7);
//var nd = new Date(newdate);
//alert('the new date is '+nd);

var initialLoad = true;



$(document).ready(function(){

	$('.sc_wrapper').scrollLeft(20)

	$(".btn-next").click(function(){
		var current_date_selected = datePickerDateGetter();
		var next_date = nextDate(current_date_selected);
		initData(next_date);
	});

	$(".btn-previous").click(function(){
		var current_date_selected = datePickerDateGetter();
		var previous_date = previousDate(current_date_selected);
		initData(previous_date);
	})

	$(".btn-print-preview").click(function(){
		$("#sc_main_box").css({"overflow":"hidden"});

		//$(".navbar").css({"visibility":"hidden"});
		//$(".sidebar").css({"visibility":"hidden"});
		//$(".tab-legend-eta").css({"visibility":"hidden"});
		//$("html, body").css({"height":"500px","margin":"0 !important","padding":"0 !important","overflow":"hidden"});
		//$(".sched-header-label").css({"visibility":"hidden"});
		//$(".sched-items-holder").css({"visibility":"hidden"});
		//$("#lz_eye_catcher").hide();
		//$(".sched_tool, .sched_tool *").css({"visibility":"visible"});
		$(".sched_tool").css({"position":"relative","left":"30","top":"0","width":"1020px","margin-left":"20px"});
		//$(".container").css({"height":"500px;"});


        html2canvas($(".sched_tool"), {
            onrendered: function(canvas) {

							var imgData = canvas.toDataURL('image/jpeg');

							$(".print-preview").html('<img src="' + imgData + '">');
							var windowContent = '<!DOCTYPE html>';
							windowContent += '<html>'
							windowContent += '<head>';
							windowContent += '<title>Scheduling Page</title>';
							//windowContent += '<link rel="stylesheet" type="text/css" href="'+app_url+'/css/print.css" />';
							windowContent += '</head>';
							windowContent += '<body>'
							//windowContent += canvas
							windowContent += '<img style="width:1240px; margin:0 auto;" src="' + imgData + '">';
							windowContent += '</body>';
							windowContent += '</html>';
							setTimeout(function(){
								//var printWin = window.open('', '', 'width=1600,height=800');
								//printWin.document.open();
								//printWin.document.write(windowContent);
								//printWin.document.close();
								//printWin.focus();
								//printWin.print();
								//printWin.close();
								var printWin = window//.open();
								printWin.document.open();
								printWin.document.write(windowContent);
								//printWin.document.close();
								printWin.focus();
								printWin.print();
								//printWin.close();
								setTimeout(function(){
								    $(window).one('mousemove', window.onafterprint);
								}, 100);
								//return false;
							},100)

            },
						background: "#FFF",
						height: 300,
        });

	});

	var beforePrint = function() {
    console.log("before");
	};
	var afterPrint = function() {
	    console.log("after");
			window.location.reload();
	};

	var launchedFromMenu = true;
	if (window.matchMedia) {
	    var mediaQueryList = window.matchMedia('print');
	    mediaQueryList.addListener(function(mql) {
	        if (mql.matches) {
	            if(launchedFromMenu) {
	                // https://bugs.chromium.org/p/chromium/issues/detail?id=571070
	               // alert("There is a bug in Chrome/Opera and printing via the main menu does not work properly. Please use the 'print' icon on the page.");
	            }
	            beforePrint();
	        } else {
	            afterPrint();
	        }
	    });
	}
	// These are for Firefox
	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;

	$(".btn-print").click(function(){
		PrintElem(".print-preview");
	})

	function PrintElem(elem)
	{
	    Popup($(elem).html());
	}

	function Popup(data)
	{
	    var mywindow = window.open('', 'print_div', 'height=400,width=600');
	    mywindow.document.write('<html><head><title>Print Window</title>');
	    mywindow.document.write('</head><body>');
	    mywindow.document.write('<img src="' + data + '">');
	    mywindow.document.write('</body></html>');
	    mywindow.document.close();
	    mywindow.print();
	    return true;
	}



	// sched tool data initializer
	function initData(selected_date){
		$(".loading-text").show();
		$(".btn-next").hide();
		$(".btn-previous").hide();
		$.ajax({
			url		:	app_url+'/initdata',
			data 	:	{'selected_data':selected_date},
			type	: 	"POST",
			success: function(r){
				$(".data-holder").html("");
				$(".data-holder").append(r);
			}
		});

		$.ajax({
				url		:	app_url+'/initdataBar',
				data 	:	{'selected_data':selected_date},
				type	: 	"POST",
				success: function(r){

					var deliveries_arranged = {};

					if(r == 0){
						deliveries_arranged = {
												0 : {
												  title : '',
												  schedule:[{
													start:'00:00',
													end:'24:00',
													text:'No scheduled item yet...',
												  }]
												}
											}
					} else {

						for(var i=0; i<r.length; i++){
							deliveries_arranged[i] = {};
							deliveries_arranged[i]['title'] = r[i]['title'];
							deliveries_arranged[i]['eta'] = r[i]['eta'];
							deliveries_arranged[i]['dar'] = r[i]['dar'];
							deliveries_arranged[i]['schedule'] = r[i]['schedule'];
							deliveries_arranged[i]['data'] = r[i]['data'];
						}

					}
					console.log(deliveries_arranged);
					deliveryETA(deliveries_arranged);
					driverActivityReport(deliveries_arranged);
					timeScheduleDataLoader(selected_date,deliveries_arranged);
					totalTonsInit(selected_date);
					totalTonsScheduled(selected_date);
					totalTonsDelivered(selected_date);
					$(".loading-text").hide();
					$(".btn-next").show();
					$(".btn-previous").show();
				}
		});

	}

	function deliveryETA(deliveries)
	{
		var table = "<table class='table table-striped'>";
				table += "<tr class='info'>";
				table += "<th class='text-center'>Name</th>";
				table += "<th>ETA</th>";
				table += "</tr>"
		$.each(deliveries, function(key,val){
			if(val.title == ""){
				table += "<tr class='active'>";
				table += "<td class='text-center'>none yet...</td>";
				table += "<td></td>";
				table += "</tr>";
			} else {
				table += "<tr class='active'>";
				table += "<td class='text-center'>"+val.title+"</td>";
				table += "<td>"+val.eta+"</td>";
				table += "</tr>";
			}
			console.log(val);
			if(val.title == ""){
				initialLoad = true;
			}

		});

		$("#eta").html("");
		$("#eta").append(table);


	}

	function driverActivityReport(driverActivityData)
	{
		var table = '<table class="table table-hover">';
				table += '<tr class="suceess">'
				table += '<th class="col-md-2 text-center active-green">Time</th>'
				table += '<th class="col-md-1 active-green">Truck</th>'
				table += '<th class="col-md-3 text-center active-green">Going To</th>'
				table += '<th class="col-md-1 active-green">Run Time</th>'
				table += '<th class="col-md-3 text-center active-green">Return (New Load Time)</th>'
				table += '<th class="col-md-2 text-center active-green">Actual Time Back</th>'
				table += '</tr>';

		if(driverActivityData[0]['dar'] == null){
			table += "<tr class='active'>";
			table += "<td class='col-md-2'>none...</td>";
			table += "<td></td>";
			table += "<td></td>";
			table += "<td></td>";
			table += "<td></td>";
			table += "<td></td>";
			table += "</tr>";

			$("#dar").html("");
			$("#dar").append(table);
			return false;
		}	else {
			$.each(driverActivityData[0]['dar'], function(key,val){
				if(val.driver_name == ""){
					table += "<tr class='active'>";
					table += "<td class='col-md-2'>none...</td>";
					table += "<td></td>";
					table += "<td></td>";
					table += "<td></td>";
					table += "<td></td>";
					table += "<td></td>";
					table += "</tr>";
					return false;
				} else {
					table += '<tr class="active">';
					table += '<td class="col-md-2 text-center text-success">'+val['start_time']+'</td>';
					table += '<td class="col-md-1">'+val['driver_name']+'</td>';
					table += '<td class="col-md-3 text-center">'+val['farm']+'</td>';
					table += '<td class="col-md-1">'+val['run_time']+'</td>';
					table += '<td class="col-md-3 text-center">'+val['return_time']+'</td>';
					table += '<td class="col-md-2 text-center">'+val['actual_time_back']+'</td>';
					table += '</tr>';
				}
				console.log(val);
				if(val.dar == ""){
					initialLoad = true;
				}

			});
		}


		$("#dar").html("");
		$("#dar").append(table);

	}

	$('#tab_legend').click(function (e) {
	  e.preventDefault()
		$(".sticky-wrapper").show();
		$(".btn-print-preview").show();
	});

	$('#tab_eta').click(function (e) {
	  e.preventDefault()
		$(".sticky-wrapper").show();
		$(".btn-print-preview").show();
	});

	$('#tab_dar').click(function (e) {
	  e.preventDefault()
		$(".sticky-wrapper").hide();
		$(".btn-print-preview").hide();
	});



	var date_today = $("#datepickerSchedTool").val();//new Date().toJSON().slice(0,10);

	initData(date_today);


	// scheduling page date picker
	$("#datepickerSchedTool").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'M d',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// trigger the change date
	$("#datepickerSchedTool").change(function(){

		var date_selected = $(this).val();

		initData(date_selected);

	});

	// delivery_number dropdown
	$(".container").delegate(".delivery_number","change",function(e) {
        var delivery_number = $(this).val();
		var unique_id = $(this).attr("id");
		var driver_id = $("#driver-"+unique_id).val();
		var selected_date = $("#datepickerSchedTool").val();
		var selected_index = $(this).prop("selectedIndex");

		var data = {
				'driver_id'			:	driver_id,
				'unique_id'			:	unique_id,
				'delivery_number'	:	delivery_number,
				'selected_date'		:	selected_date,
				'selected_index'	:	selected_index
			};

		$.ajax({
			url		:	app_url+"/deliveryNumberValidate",
			data 	: 	data,
			type 	: 	"POST",
			success	: function(r){
				$(".btn_sched_kb_list").hide();
				if(r.output == 0){
					$.ajax({
						url	 :	app_url+"/scheduleditemsdelivery",
						data :	data,
						type :	"POST",
						success: function(r){
							$(".btn_sched_kb_list").show();
							var deliveries_arranged = {};

							for(var i=0; i<r.length; i++){
								deliveries_arranged[i] = {};
								deliveries_arranged[i]['title'] = r[i]['title'];
								deliveries_arranged[i]['schedule'] = r[i]['schedule'];
								deliveries_arranged[i]['data'] = r[i]['data'];
							}

							timeScheduleDataLoader(selected_date,deliveries_arranged);
							initData(selected_date);
						}
					});
				} else{
					alert("already selected!");
					$("#"+unique_id).prop('selectedIndex',r.selected_index);
					initData(selected_date);
				}
			}
		});

    });


	// sched_driver dropdown
	$(".container").delegate(".sched_driver","change",function(){

		var unique_id = $(this).attr("unique_id");
		var driver_id = $("#driver-"+unique_id).val();
		var delivery_number = $("#"+unique_id).val();
		var selected_date = $("#datepickerSchedTool").val();

		var data = {
				'driver_id'			:	driver_id,
				'unique_id'			:	unique_id,
				'delivery_number'	:	delivery_number,
				'selected_date'		:	selected_date
			};


			$.ajax({
				url	 :	app_url+"/scheduleditemsdriver",
				data :	data,
				type :	"POST",
				success: function(r){
					var deliveries_arranged = {};

					for(var i=0; i<r.length; i++){
						deliveries_arranged[i] = {};
						deliveries_arranged[i]['title'] = r[i]['title'];
						deliveries_arranged[i]['schedule'] = r[i]['schedule'];
						deliveries_arranged[i]['data'] = r[i]['data'];
					}

					timeScheduleDataLoader(selected_date,deliveries_arranged);
					initData(selected_date);
				}
			});
	});

    @forelse($data as $list)

	$(".container").delegate(".btn-delsched{{$list->schedule_id}}","click",function(){

		var unique = $(this).attr("unique");

		$.ajax({

			url		:	app_url+'/schedlistindex',
			type 	: "POST",
			data 	: {'unique_id':unique},
			success: function(r){

				$("#"+unique).remove();

				var date_selected = $("#datepickerSchedTool").val();

				initData(date_selected);

			}

		});

	});

	@empty
	@endforelse

	/*
	*	total tons initializer
	*/
	function totalTonsInit(delivery_date){

		$.ajax({
			url		:	app_url+"/totaltonsinit",
			data 	: {'delivery_date': delivery_date},
			type 	: "POST",
			success	: function(r){
				$('.total_tons_sched_tool').html("");
				$('.total_tons_sched_tool').html(r);
			}
		});

	}

	/*
	*	total tons Scheduled initializer
	*/
	function totalTonsScheduled(delivery_date){

		$.ajax({
			url		:	app_url+"/totaltonsscheduled",
			data 	: {'delivery_date': delivery_date},
			type 	: "POST",
			success	: function(r){
				$('.total_tons_scheduled').html("");
				$('.total_tons_scheduled').html(r);
			}
		});

	}

	/*
	*	total tons Delivered initializer
	*/
	function totalTonsDelivered(delivery_date){

		$.ajax({
			url		:	app_url+"/totaltonsdelivered",
			data 	: {'delivery_date': delivery_date},
			type 	: "POST",
			success	: function(r){
				$('.total_tons_delivered').html("");
				$('.total_tons_delivered').html(r);
			}
		});

	}

	function timeScheduleDataLoader(selected_date,data){
		initialLoad == false;
		var main_box_offset = $(".sc_main_box").scrollLeft();

		$("#schedule").html("");

		//console.log(data)
		// Daily Schedule Plugin
		$("#schedule").timeSchedule({

			rows : data,

			// schedule start time(HH:ii)
			startTime: "00:00",

			// schedule end time(HH:ii)
			endTime: "24:00",

			// width(px)
			widthTimeX: 10,

			// cell timestamp example 10 minutes
			widthTime: 60 * 10,

			// height(px)
			timeLineY: 30,

			// options for time slots
			timeLineBorder:1,
			timeBorder:0,   // border width
			timeLinePaddingTop:0,
			timeLinePaddingBottom:0,
			headTimeBorder:0, // time border width

			// data width
			dataWidth:160,

			change: function(node,data){
				//alert("change event");
				initialLoad = false;
				//console.log(data)
				var start_hours = parseInt( data.start / 3600 ) % 24;
				var start_minutes = parseInt( data.start / 60 ) % 60;
				var end_hours = parseInt( data.end / 3600 ) % 24;
				var end_minutes = parseInt( data.end / 60 ) % 60;

				data.start = (start_hours < 10 ? "0" + start_hours : start_hours) + ":" + (start_minutes < 10 ? "0" + start_minutes : start_minutes)
				data.end = (end_hours < 10 ? "0" + end_hours : end_hours) + ":" + (end_minutes < 10 ? "0" + end_minutes : end_minutes)
				//console.log(data)

				$.ajax({
					url		: app_url+"/updatescheditems",
					data  	:	data,
					type    :	"POST",
					success: function(r){
							initData(r.delivery_date);
					}
				})
			},

			init_data: function(node,data){

			},

			click: function(node,data){
				//alert("click event");

			},

			append: function(node,data){

				if(initialLoad == true){
					$('.sc_main_box').scrollLeft(359);
				}else{
					$(".sc_main_box").scrollLeft(main_box_offset);
					console.log(main_box_offset);
				}



				if(data.data.status == 'delivered'){
					//setTimeout(function(){
						//$(".ui-resizable-disabled").addClass("delivered-items");
						//$(".test-"+node.length).addClass("delivered-items");
					//},400)
					$("#"+node.context.id).addClass("delivered-items");
					//console.log(node)
					//console.log(data.timeline)
					node.resizable('disable');
					node.draggable('disable');
				}else{
					//$(".sc_Bar").removeClass("delivered-items");
				}

				if(data.data.status == 'created'){
					//$("#"+node.context.id).addClass("pending-items");
					//node.resizable('disable');
					//node.draggable('disable');
				}

				if(data.data.status == 'ongoing'){
					$("#"+node.context.id).addClass("pending-items");
					node.resizable('disable');
					node.draggable('disable');
				}

				if(data.data.status == 'pending'){
					$("#"+node.context.id).addClass("pending-items");
					node.resizable('disable');
					node.draggable('disable');
				}

				if(data.data.status == 'unloaded'){
					$("#"+node.context.id).addClass("unloaded-items");
					node.resizable('disable');
					node.draggable('disable');
				}
			},

			time_click: function(time,data){
				//alert("time click event");
			},

		});
	}


	var sticky = new Waypoint.Sticky({
	  element: $('.sched_tool')[0]
	});

});
</script>
