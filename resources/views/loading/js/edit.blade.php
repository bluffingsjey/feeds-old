<script type="text/javascript" src="{{ asset('js/ddslick.js')}}"></script>
<script type="text/javascript">
function testingajax(defaults){
	$.ajax({
			url :	app_url+"/defaultcompartments",
			data:	{'sample':defaults},
			type: "POST",
			success: function(r){
			}
	});
}

function saveChangeDate(selected_date,unique_id,delivery_time){

	$.ajax({
		url		:	app_url+'/savechangedateschededited',
		data 	:	{'selected_data':selected_date,'unique_id':unique_id,'delivery_time':delivery_time},
		type	: 	"POST",
		success: function(r){

		}
	});

}


$(document).ready(function(){

	/*
	*	schedDateTime
	*/
	$("#schedDateTime").datepicker({
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
	$("#schedDateTime").change(function(){

		var date_selected = $(this).val();
		var unique_id = $(this).attr("unique_id");
		var delivery_time = $("#delivery_time").val();

		saveChangeDate(date_selected,unique_id,delivery_time);

	});




	/*
	*	duplicate array value remover
	*/
	function arrayUnique(array) {
		var a = array.concat();
		for(var i=0; i<a.length; ++i) {
			for(var j=i+1; j<a.length; ++j) {
				if(a[i] === a[j])
					a.splice(j--, 1);
			}
		}

		return a;
	}


	// active css style for select menu
	var active_style = {
				"background" : "#FFF",
				"cursor"	 : "pointer"
				}

	// inactive css style for select menu
	var inactive_style = {
				"background" : "#EEE",
				"cursor"	:	"not-allowed"
				}

	// compartment to remove
	var compartment_to_remove = "";


	// remove the disable state of the loadoutBins
	$(".loadoutBins").each(function(index, element) {
        $(element).removeAttr("disabled");
				$(element).css(active_style);
  });

	// remove the disable state of the truck_compts
	$(".truck_compts").each(function(index, element) {
        $(element).removeAttr("disabled");
				$(element).css(active_style);
  });

	// hide the add batch form
	$(".add-batch-view").hide();

	// hide the summary div holder
	//$(".summdiv_b").hide();




	$(".truck_compts").each(function(index, element) {

		$(element).change(function(e) {

			var sched_id = $(element).attr("sched-id");

		})

	});






	/*
	*	Load Truck functionality
	*/
	$(".container").delegate(".btn-kb-succ","click",function(){
		$(this).hide();

		var delivery_id = [];
		var unique_id = [];
		var compartments = [];
		var farms = [];
		var feed_type = [];
		var medication = [];
		var amounts = [];
		var selected_bins = [];
		var truck_id = [];

		var catcher_one = 0;
		var catcher_two = 0;
		var catcher_three = 0;



		// farms
		$(".farm-lists").each(function(k,v){
			if($(v).val() == ""){
				catcher_two = 1;
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the farms");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
			} else {
				farms.push($(v).val());
				delivery_id.push($(v).attr("delivery-id"));
				unique_id.push($(v).attr("unique_id"));
				truck_id.push($(v).attr("truck_id"));
				catcher_two = 0;
			}
		});

		// feed type
		$(".feed_name").each(function(k,v){
			if($(v).val() == ""){
				catcher_two = 1;
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the feed types");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
			} else {
				feed_type.push($(v).val());
				catcher_two = 0;
			}
		});

		// medication
		$(".medication").each(function(k,v){
			medication.push($(v).val())
		});

		// amounts
		$(".amounts").each(function(k,v){
			amounts.push($(v).val());
		});

		//bins
		$(".selected_bins").each(function(k,v){
			if($(v).val() == ""){
				catcher_two = 1;
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the bins");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
			} else {
				selected_bins.push($(v).val());
				catcher_two = 0;
			}
		});

		$(".truck_compts").each(function(i,e){
			if($(e).val() == ""){
				catcher_two = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the Truck Compartments");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
				return false;
			} else{
				var sched_id = $(e).attr("sched-id");
				//$(".lob-"+sched_id).each(function(index, element) {
				   compartments.push($(e).val());
			   // });
			   catcher_two = 0;
			}
		});

		// update the  on change farms bins lists
		var batchdata = {
				'delivery_id'		:		delivery_id,
				'compartments'	:		compartments,
				'farms'					:		farms,
				'feed_type'			: 	feed_type,
				'medication'		:		medication,
				'amounts'				:		amounts,
				'selected_bin'	: 	selected_bins,
				'driver'				:		$(".driver-kb").val(),
				'unique_id'			:		unique_id,
				'truck_id'			:		truck_id,
				'driver_id'			:		$(".driver-kb").val()
			}

		setTimeout(function(){

			if(catcher_one == 0 && catcher_two == 0 && catcher_three == 0){
				//console.log(catcher_one+" "+catcher_two+" "+catcher_three);

				$.ajax({
						url		:	app_url+'/loadtotruckedit',
						data 	:	batchdata,
						type 	: "post",
						success: function(r){
							//console.log(r);
							//deleteSelected()
							if(r == 1){
								$(".load_to_truck_panel").html("")
								$(".load_to_truck_panel").html("Redirecting to scheduling page, Please wait...")
								window.location.replace(app_url+"/loading");
							}else if(r == 'compartment_error'){
								//error
								$(".batchMessageHolder").html("");
								$(".batchMessageHolder").append("Compartment total amount should be less than or equal to 3.0 Tons...");
								$("#BatchDeliveryModal").modal('show');
								$(".btn-kb-succ").show();
							} else if(r == 'compartment_data_error'){
								//error
								$(".batchMessageHolder").html("");
								$(".batchMessageHolder").append("Multiple batch with same compartment should have the same farm, feed type and medication...");
								$("#BatchDeliveryModal").modal('show');
								$(".btn-kb-succ").show();
							} else{
								//error
								$(".batchMessageHolder").html("");
								$(".batchMessageHolder").append("Something went wrong please try again...");
								$("#BatchDeliveryModal").modal('show');
								$(".btn-kb-succ").show();
							}
						}
				});

			 }

		},500);


	});


	// Farms Lists
	$(".container").delegate(".farm-lists","change",function(){

		var farm_id = $(this).val();
		var sched_id = $(this).attr("delivery-id");
		var $bin_lists = $("#bin-"+sched_id);

		$.ajax({
			url		:	app_url+'/binslists',
			type 	:	"POST",
			data 	: 	{'id':farm_id},
			success	: function(data){
				$bin_lists.empty();
				$bin_lists.append("<option value=''>-</option>");
				for(i in data){
					$bin_lists.append('<option id=' + i + ' value=' + i + '>' + data[i] + '</option>');
				}
			}
		});


		var $feeds_lists = $(".feed-name-"+sched_id);

		$.ajax({
			url		:	app_url+'/feedstypelists',
			type 	:	"GET",
			data 	: 	{'id':farm_id},
			success	: function(data){
				$feeds_lists.empty();
				$feeds_lists.append("<option selected >-</option>");
				for(i in data){
					$feeds_lists.append('<option id=' + i + ' value=' + i + '>' + data[i] + '</option>');
				}
			}
		})

	});


	// bins Lists
	$(".container").delegate(".selected_bins","change",function(){

		var bin_id = $(this).val();
		var sched_id = $(this).attr("sched-id");
		var counter = $(this).attr("counter");
		var $bin_lists = $("#feed-name-"+sched_id);

		$.ajax({
			url		:	app_url+'/binslistselected',
			type 	:	"GET",
			data 	: 	{'bin_id':bin_id},
			success	: function(data){
				//console.log(data);
				$bin_lists.empty();
				$bin_lists.append(data);
				//for(i in data){
					//$bin_lists.append('<option id=' + i + ' value=' + i + '>' + data[i] + '</option>');
				//}
			}
		})

	});








});




</script>
