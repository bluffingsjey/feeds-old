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
		url		:	app_url+'/savechangedatesched',
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




	// call the disabler on page load
	disableDropdowns();

	//call the selected loadoutbins
	selectedLoadout("{{$schedData[0]['unique_id']}}");
	selectedCompartments("{{$schedData[0]['unique_id']}}");

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

	// Hide load truck button
	//$(".btn-kb-succ").hide();

	// hide the edit batch button
	$(".editbtnkb").hide();

	// hide the delete batch button
	$(".delbtnkb").hide();

	// remove the disable state of the tickets
	$(".tickets").each(function(index, element) {
        $(element).removeAttr("disabled");
				$(element).css(active_style);
  });

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



	/*
	*	tickets on key up
	*/
	$(".tickets").each(function(index, element) {

		if($(element).val() != ""){
			var sched_id = $(element).attr("sched-id");

			$(element).keyup(function(){
				setTimeout(function(){
					console.log("sample")
					$.ajax({
						url	: app_url+"/updateticket",
						data: {'sched_id':sched_id,'ticket':$(element).val()},
						type:"post",
						success: function(r){

						}
					})
					//return false;
				},1000);
			});

		} else {

			$(element).keyup(function(e) {
				var sched_id = $(element).attr("sched-id");

				if($(element).val() != ""){
					// set the selection
					setTimeout(function(){
						$("#ticket-holder-"+sched_id).text("");
						$("#ticket-holder-"+sched_id).text("Ticket: "+$(element).val());

					},500);

					setTimeout(function(){
						$.ajax({
							url	: app_url+"/updateticket",
							data: {'sched_id':sched_id,'ticket':$(element).val()},
							type:"post",
							success: function(r){

							}
						})
						//return false;
					},1000);

				} else {

					setTimeout(function(){
						console.log("smaple")
						$.ajax({
							url	: app_url+"/updateticket",
							data: {'sched_id':sched_id,'ticket':$(element).val()},
							type:"post",
							success: function(r){

							}
						})
						//return false;
					},1000);
				}

			});
		}

    });

	$(".truck_compts").each(function(index, element) {

		$(element).change(function(e) {

			var sched_id = $(element).attr("sched-id");

				/*$.ajax({
						url :	app_url+"/loadoutbinscompartments",
						data:	{
								'sched_id'			: 	sched_id,
								'value'				:	$(element).val(),
								'element_id'		: 	$(element).attr("id"),
								'unique_id'			:	$(element).attr("unique-id"),
								'selected_index'	:	$(element).prop("selectedIndex")
								},
						type: "POST",
						success: function(r){
							if(r.message == "Compartment Already Selected"){
								$(".batchMessageHolder").html("");
								$(".batchMessageHolder").append("Compartment Already Selected");
								$("#BatchDeliveryModal").modal('show');
								$(element).prop('selectedIndex',0);
							} else if(r.message == "Compartment Already Selected Update"){
								$(".batchMessageHolder").html("");
								$(".batchMessageHolder").append("Compartment Already Selected");
								$("#BatchDeliveryModal").modal('show');
								$(element).prop('selectedIndex',r.selected);
							}
						}
					});	*/

		})

	});

	loadOutBinsSelectionList()
	/*
	*	loadout bins
	*/
	function loadOutBinsSelectionList(){
		var counter = 0;
		var selected_loadout_bins = [];
		$(".loadoutBins").each(function(index, element) {

			$(element).change(function(e) {
				console.log(index);
				var sched_id = $(element).attr("sched-id");
				/*var loadoutbin_counter = $(".lob-"+sched_id).length;

				loadoutbin_to_remove = $(this).val();
				$(".loadoutBins").each(function(i,e){

					if($(e).val() == ""){
						//$(e).find('[value="'+loadoutbin_to_remove+'"]').remove();
					}

				})


				if(counter == -1){
					counter = 0;
				}*/

				if($(element).val() != ""){

					$.ajax({
							url :	app_url+"/loadoutbinsloadcounter",
							data:	{
									'sched_id'			: 	sched_id,
									'value'				:	$(element).val(),
									'element_id'		: 	$(element).attr("id"),
									'unique_id'			:	$(element).attr("unique-id"),
									'selected_index'	:	$(element).prop("selectedIndex")
									},
							type: "POST",
							success: function(r){
								/*if(r.message == "Loadout Bin Already Selected"){
									$(".batchMessageHolder").html("");
									$(".batchMessageHolder").append("Loadout Bin Already Selected");
									$("#BatchDeliveryModal").modal('show');
									$(element).prop('selectedIndex',0);
								} else if(r.message == "Loadout Already Selected Update"){
									$(".batchMessageHolder").html("");
									$(".batchMessageHolder").append("Loadout Bin Already Selected");
									$("#BatchDeliveryModal").modal('show');
									$(element).prop('selectedIndex',r.selected);
								}*/
							}
						});


					/*if($("#summ-loadoutbin-"+sched_id).text() == ""){
						$("#summ-loadoutbin-"+sched_id).text("Loadout Bins: "+loadout_bins);
					}else{
						$("#summ-loadoutbin-"+sched_id).text("");
						$("#summ-loadoutbin-"+sched_id).text("Loadout Bins: "+loadout_bins);
					}*/




					/*if(counter == 2){
						counter = 0;
					}
					counter = counter+1;
					if(counter == loadoutbin_counter){
						setTimeout(function(){
							//$(".tc-"+sched_id).removeAttr("disabled");
							//$("summ-loadoutbin-"+sched_id).css(active_style);
						},500);
					}
					//console.log(counter);
					//console.log(loadoutbin_counter);

					if(counter == 2 && loadoutbin_counter == 1){
						setTimeout(function(){
							//$(".tc-"+sched_id).removeAttr("disabled");
							//$(".tc-"+sched_id).css(active_style);
						},500);
						counter = 1;
					}*/

				} else{
					$("#summ-loadoutbin-"+sched_id).text("");
					$("#summ-loadoutbin-"+sched_id).text("Loadout Bins: None Yet");
					/*counter = counter-1;
					if(counter < 0){
						counter = 0;
					}
					//console.log(counter)
					if(counter == 0){
						setTimeout(function(){
							//$(".tc-"+sched_id).val("");
							//$(".tc-"+sched_id).attr("disabled","true");
							//$(".tc-"+sched_id).css(inactive_style);
						},500);
					}*/
					//console.log(counter);
				}

			});
		});
	}

	/*
	*	disable all loadout bins and compartment numbers
	*/
	function disableDropdowns(){

		/*$(".loadoutBins").each(function(index, element) {
            //$(element).attr("disabled","true");
			console.log("testing")
        });*/

	}

	/*
	*	Selected Loadout Bins
	*/
	function selectedLoadout(unique_id){
		$.ajax({
				url	:	app_url+"/loadoutbins",
				data: data={'unique_id':unique_id},
				type: "POST",
				success: function(r){
					//console.log(r);
					if(r != ""){
						r.forEach(function(i){
							$("#"+i.element_id).prop("selectedIndex",i.selected_index);
						});
					}
				}
			})
	}

	/*
	*	Selected Compartments
	*/
	function selectedCompartments(unique_id){
		$.ajax({
				url	:	app_url+"/compselected",
				data: data={'unique_id':unique_id},
				type: "POST",
				success: function(r){
					//console.log(r);
					if(r != ""){
						r.forEach(function(i){
							$("#"+i.element_id).prop("selectedIndex",i.selected_index);
						});
					}
				}
			})
	}

	/*
	*	save batch tickets
	*/
	$(".container").delegate(".btn-save-tickets","click",function(){

		var tickets = [];
		var loadoutbins = [];
		var compartments = [];

		var catcher = 0;
		$(".tickets").each(function(i,e){
			if($(e).val() == ""){
				catcher = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the tickets");
				$("#BatchDeliveryModal").modal('show');
				return false;
			} else{
				tickets.push({'sched-id' : $(e).attr("sched-id"),'tickets' : $(e).val()});
			}
		});

		$(".loadoutBins").each(function(i,e){
			if($(e).val() == ""){
				catcher = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the Loadout Bins");
				$("#BatchDeliveryModal").modal('show');
				return false;
			} else{
				var sched_id = $(e).attr("sched-id");
				//$(".tc-"+sched_id).each(function(index, element) {
                    loadoutbins.push({'sched-id' : $(e).attr("sched-id"),'loadoutbin' : $(e).val()});
               // });
			}
		});

		$(".truck_compts").each(function(i,e){
			if($(e).val() == ""){
				catcher = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the Truck Compartments");
				$("#BatchDeliveryModal").modal('show');
				return false;
			} else{
				var sched_id = $(e).attr("sched-id");
				//$(".lob-"+sched_id).each(function(index, element) {
                   compartments.push({'sched-id' : $(e).attr("sched-id"),'compartment' : $(e).val()});
               // });
			}
		});

		//console.log(loadoutbins);
		//console.log(compartments);
		var batchdata = {
				'tickets'		:	tickets,
				'loadoutbins'	:	loadoutbins,
				'compartments'	:	compartments
			}


		if(catcher == 0){

			var update_batch = {};
			var loadout_holder = [];

			for(var i = 0; i < tickets.length; i++){
				update_batch[i] = {};
				update_batch[i]['sched-id'] = tickets[i]['sched-id'];
				update_batch[i]['tickets'] = tickets[i]['tickets'];
				//update_batch[i]['loadoutbins'] = loadoutbins[i]['loadoutbin'];
				update_batch[i]['compartments'] = compartments[i]['compartment'];

				if(tickets[i]['sched-id'] == loadoutbins[i]['sched-id']){
					for(var j = 0; j < loadoutbins.length; j++){
						if(tickets[i]['sched-id'] == loadoutbins[j]['sched-id']){
						 	loadout_holder.push(loadoutbins[j]['loadoutbin']);
						}
					}
				}

				update_batch[i]['loadoutbins'] = loadout_holder;
			}



			$.ajax({
					url	:	app_url+'/savebatchselection',
					data :	batchdata,
					type : "post",
					success: function(r){
						console.log(r);

						//disable the ticket, load out bins, compartments
						// remove the disable state of the tickets
						$(".tickets").each(function(index, element) {
							$(element).attr("disabled","true");
							$(element).css(inactive_style);
						});

						// remove the disable state of the loadoutBins
						$(".loadoutBins").each(function(index, element) {
							$(element).attr("disabled","true");
							$(element).css(inactive_style);
						});

						// remove the disable state of the truck_compts
						$(".truck_compts").each(function(index, element) {
							$(element).attr("disabled","true");
							$(element).css(inactive_style);
						});

						var loadout_bins = "";
						var compartments = "";

						// show the summary and load truck button
						$(".btn-save-tickets").hide(function(){

							r.forEach(function(v){
								$("#ticket-holder-"+v.sched_id).text("")
								$("#ticket-holder-"+v.sched_id).text(v.ticket)

								if(v.loadoutbins.length == 1){
									$("#summ-loadoutbin-"+v.sched_id).text("Loadout Bins: "+v.loadoutbins)
								} else {
									v.loadoutbins.forEach(function(val){
										loadout_bins = loadout_bins+","+val;
										while( loadout_bins.charAt( 0 ) === ',' )
    									loadout_bins = loadout_bins.slice( 1 );
										$("#summ-loadoutbin-"+v.sched_id).text("Loadout Bins: "+loadout_bins)
									})
									loadout_bins = "";
								}

								$("#summ-compartment-"+v.sched_id).text("")

								if(v.compartments.length == 1){
									$("#summ-compartment-"+v.sched_id).text("Compartments: "+v.compartments[0]['number'])
									console.log(v.compartments);
								} else {
									v.compartments.forEach(function(val){
										compartments = compartments+","+val.number;
										while( compartments.charAt( 0 ) === ',' )
    									compartments = compartments.slice( 1 );
										$("#summ-compartment-"+v.sched_id).text("Compartments: "+compartments)
									})
									compartments = "";
								}


							});

							$(".btn-kb-succ").show();
							$(".summdiv_b").show();
						});

					}
				});

			/*$.ajax({
				url	 :	app_url+'/loadoutsavebatch',
				data :	tickets_batch,
				type : "POST",
				success: function(data){
					var output = data;
					if(output == "success"){
						//show all edit,delete,addbatch,loadout message and loudout bins
						$(".editbtnkb").show();
						$(".delbtnkb").show();
						$(".add-batch-view").show();
						$(".bin_loadout_holder").show();
						$(".loadout-message").show();

						// add the disable state of the tickets
						$(".tickets").each(function(index, element) {
							$(element).attr("disabled","true");
						});

						//hide $(".btn-save-tickets") button
						$(".btn-save-tickets").hide();

					} else {
						//error
						$(".batchMessageHolder").html("");
						$(".batchMessageHolder").append("Something went wrong plaese try again.");
						$("#BatchDeliveryModal").modal('show');
					}
				}
			});	*/

		}

	});

	/*
	*	Load Truck functionality
	*/
	$(".container").delegate(".btn-kb-succ","click",function(){
		$(this).hide();

		var tickets = [];
		var loadoutbins = [];
		var compartments = [];
		var farms = [];
		var feed_type = [];
		var medication = [];
		var amounts = [];
		var selected_bins = [];

		var catcher_one = 0;
		var catcher_two = 0;
		var catcher_three = 0;

		/*
		$(".loadoutBins").each(function(i,e){
			if($(e).val() == ""){
				catcher_one = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the Loadout Bins");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
				return false;
			} else{
				var sched_id = $(e).attr("sched-id");
				//$(".tc-"+sched_id).each(function(index, element) {
					loadoutbins.push({'sched-id' : $(e).attr("sched-id"),'loadoutbin' : $(e).val()});
			   // });
			   catcher_one = 0;
			}
		});
		*/

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
				   compartments.push({'sched-id' : $(e).attr("sched-id"),'compartment' : $(e).val()});
			   // });
			   catcher_two = 0;
			}
		});

		$(".tickets").each(function(i,e){

			if($(e).val() == ""){
				catcher_three = 1;
				//error
				$(".batchMessageHolder").html("");
				$(".batchMessageHolder").append("Please fill out all the tickets");
				$("#BatchDeliveryModal").modal('show');
				$(".btn-kb-succ").show();
				return false;
			} else{
				var sched_id = $(e).attr("sched-id");
				var feed = $(".feed-name-"+sched_id).val();
				var medication = $("#medication-"+sched_id).val();
				var bin = $("#bin-"+sched_id).val();
				var driver_id = $(".driver-kb").val();
				tickets.push({
					'sched-id' 		: 	$(e).attr("sched-id"),
					'tickets' 		: 	$(e).val(),
					'farm'				:		$("#farm-name-"+sched_id).val(),
					'feed_type'		:		feed,
					'medication'	:		medication,
					'bins'				:		bin,
					'driver'			:		driver_id
				});
				catcher_three = 0;
			}

		});
		// validation for same compartment with total of 3 tons with multiple batch


		// update the  on change farms bins lists

		var batchdata = {
				'tickets'				:		tickets,
				'loadoutbins'		:		loadoutbins,
				'compartments'	:		compartments,
				'farms'					:		farms,
				'feed_type'			: 	feed_type,
				'medication'		:		medication,
				'amounts'				:		amounts,
				'selected_bin'	: 	selected_bins,
				'driver'				:		$(".driver-kb").val()
			}

		//console.log(batchdata)

		setTimeout(function(){

			if(catcher_one == 0 && catcher_two == 0 && catcher_three == 0){
				//console.log(catcher_one+" "+catcher_two+" "+catcher_three);

				$.ajax({
						url		:	app_url+'/loadtotruck',
						data 	:	batchdata,
						type 	: "post",
						success: function(r){
							console.log(r);

							deleteSelected()
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

	/*
	*	delete temporary selected loadout bins and compartments
	*/
	function deleteSelected(){

		$.ajax({
			url	:	app_url+"/deletetempselected",
			data : {'unique_id':"{{$schedData[0]['unique_id']}}"},
			type:"post",
			success: function(r){
				//drawChart();
			}
		})

	}

	/*
	*	Add batch funcitonality
	*	if all fields are valid then go to add batch then refresh the page
	*/
	$(".container").delegate(".addbatchbtnkb","click",function(){


		var farm = $(".ab-farm-lists").val();
		var ticket = $(".ab-ticket").val();
		var feed_type = $(".ab-feed-types").val();
		var medication = $(".ab-medications").val();
		var amount = $(".ab-amount").val();
		var bin = $(".ab-bins").val();
		var date_of_delivery = $(".ab-date-of-delivery").val();
		var driver = $(".ab-driver-id").val();
		var unique = $(".ab-unique-id").val();
		var truck_id = $(".ab-truck").val();

		var batch = {
				'farm_id'			:	farm,
				'ticket'			:	ticket,
				'feeds_type_id'		:	feed_type,
				'medication_id'		:	medication,
				'amount'			:	amount,
				'bin_id'			:	bin,
				'date_of_delivery'	:	date_of_delivery,
				'driver_id'			:	driver,
				'unique_id'			:	unique,
				'truck_id'			:	truck_id
			}

		$.ajax({
			url		:	app_url+"/addbatch",
			type	:	"POST",
			data 	:	batch,
			success: function(r){
				if(r.status == "true"){
					//apend the added batch
					addedBatch(unique);
					setTimeout(function(){
						dropdownData();
						loadoutBinSelect();
						compartmentBoxSelect();
					},1000);
					$(".bin_loadout_holder").hide();
					$(".loading-stick-circle-bin-loadout").show();
					$(".loading-stick-circle-bin-loadout").delay(1000).hide(1000,function() {
						$(".bin_loadout_holder").slideDown(200);
					});
				}else{
					//error
					$(".batchMessageHolder").html("");
					$(".batchMessageHolder").append(r.message);
					$("#BatchDeliveryModal").modal('show');
					return false;
				}
			}
		});

	});


	/*
	* Edit Batch
	*/
	$(".container").delegate(".editbtnkb","click",function(){

		$(".bin_loadout_holder").hide();

		var schedule_id = $(this).attr("sched-id");

		var active_style = {
				"background" : "#FFF",
				"cursor"	 : "pointer"
				}

		$("#farm-name-"+schedule_id).removeAttr("disabled");
		$("#farm-name-"+schedule_id).css(active_style);
		$("#sched-ticket-"+schedule_id).removeAttr("disabled");
		$("#sched-ticket-"+schedule_id).css(active_style);
		$("#feed-name-"+schedule_id).removeAttr("disabled");
		$("#feed-name-"+schedule_id).css(active_style);
		$("#medication-"+schedule_id).removeAttr("disabled");
		$("#medication-"+schedule_id).css(active_style);
		$("#amount-"+schedule_id).removeAttr("disabled");
		$("#amount-"+schedule_id).css(active_style);
		$("#bin-"+schedule_id).removeAttr("disabled");
		$("#bin-"+schedule_id).css(active_style);
		$("#loadoutBins-"+schedule_id).removeAttr("disabled");
		$("#loadoutBins-"+schedule_id).css(active_style);
		$("#truck_compts-"+schedule_id).removeAttr("disabled");
		$("#truck_compts-"+schedule_id).css(active_style);

		$("#delbtnkb-"+schedule_id).hide();

		$(this).hide(function(){
			$("#savebtn-"+schedule_id).show();
			//$(".add-batch-view").hide();
			$(".btn-kb-succ").hide();
			$(".delbtnkb").hide();
			$(".editbtnkb").hide();
		});
	});

	/*
	*	Save Batch
	*/
	$(".container").delegate(".savebtn","click",function(){

		var schedule_id = $(this).attr("sched-id");



		var unique = $(this).attr("unique-id");
		var ticket = $("#sched-ticket-"+schedule_id).val();

		var batchData = {
				'sched_id'		:	schedule_id,
				'farm'			: 	$("#farm-name-"+schedule_id).val(),
				'ticket'		:	$("#sched-ticket-"+schedule_id).val(),
				'feed_type'		:	$("#feed-name-"+schedule_id).val(),
				'medication'	:	$("#medication-"+schedule_id).val(),
				'amount'		:	$("#amount-"+schedule_id).val(),
				'bins'			:	$("#bin-"+schedule_id).val(),
				'unique_id'		:	unique
			}
		var save_holder = [];
		// save batch
		//saveBatch(batchData,unique);
		$.ajax({
			url		:	app_url+'/updatebatch',
			data 	:	batchData,
			type	: 	'POST',
			success: function(r){

				if(r.status == "true"){
					save_holder.push(r.status)
					setTimeout(function(){

						dropdownData();
						summaryRender(unique);
						loadoutBinSelect();
						compartmentBoxSelect();
						loadoutBinSelect();

					},1000);

				}else{
					//error
					$(".batchMessageHolder").html("");
					$(".batchMessageHolder").append(r.message);
					$("#BatchDeliveryModal").modal('show');
					return false;
				}

			}
		});
		setTimeout(function(){
			if(save_holder == 'true'){
				var inactive_style = {
							"background" : "#EEE",
							"cursor"	:	"not-allowed"
							}
				$("#farm-name-"+schedule_id).attr("disabled","true");
				$("#farm-name-"+schedule_id).css(inactive_style);
				$("#sched-ticket-"+schedule_id).attr("disabled","true");
				$("#sched-ticket-"+schedule_id).css(inactive_style);
				$("#feed-name-"+schedule_id).attr("disabled","true");
				$("#feed-name-"+schedule_id).css(inactive_style);
				$("#medication-"+schedule_id).attr("disabled","true");
				$("#medication-"+schedule_id).css(inactive_style);
				$("#amount-"+schedule_id).attr("disabled","true");
				$("#amount-"+schedule_id).css(inactive_style);
				$("#bin-"+schedule_id).attr("disabled","true");
				$("#bin-"+schedule_id).css(inactive_style);
				//$("#loadoutBins-"+schedule_id).attr("disabled","true");
				//$("#loadoutBins-"+schedule_id).css(inactive_style);
				$("#truck_compts-"+schedule_id).attr("disabled","true");
				$("#truck_compts-"+schedule_id).css(inactive_style);

				$(".savebtn").hide(function(){
					$("#delbtnkb-"+schedule_id).show();
					$("#editbtnkb-"+schedule_id).show();
					$(".delbtnkb").show();
					$(".editbtnkb").show();
					//$(".add-batch-view").show();
				});

				loadOutBinDisplay()
			}
		},1500);


	});

	/*
	* 	Delete Batch
	*/
	$(".container").delegate(".delbtnkb","click",function(){

		dropdownData();

		var schedule_id = $(this).attr("sched-id");
		var unique_id = $(this).attr("unique-id");
		deleteBatch(unique_id,schedule_id);

		$(".bin_loadout_holder").hide();
		$(".loading-stick-circle-bin-loadout").show();
		$(".loading-stick-circle-bin-loadout").delay(1000).hide(1000,function() {

			$(".bin_loadout_holder").slideDown(200);

		});

	});

	// Farms Lists
	$(".container").delegate(".farm-lists","change",function(){

		var farm_id = $(this).val();
		var sched_id = $(this).attr("sched-id");
		var counter = $(this).attr("counter");
		var $bin_lists = $("#bin-"+sched_id+counter);

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


		var $feeds_lists = $("#feed-name-"+sched_id+counter);

		$.ajax({
			url		:	app_url+'/feedstypelists',
			type 	:	"GET",
			data 	: 	{'id':farm_id},
			success	: function(data){
				$feeds_lists.empty();
				$feeds_lists.append("<option value=''>-</option>");
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
		var $bin_lists = $("#feed-name-"+sched_id+counter);

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

	// Add Batch Farms Lists
	$(".container").delegate(".ab-farm-lists","change",function(){

		var farm_id = $(this).val();
		var $bin_lists = $(".ab-bins");

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
		})

	});


	/*
	*	Add the added batch on the batch lists
	*/
	function addedBatch(unique){

		 $.ajax({
			url		:	app_url+"/getbatch",
			type	:	"POST",
			data 	:	data={"unique_id":unique},
			success: function(r){


				$(".batch_holder").html("");
				$(".batch_holder").append(r);

				setTimeout(function(){

					dropdownData();
					summaryRender(unique);
					loadoutBinSelect();
					compartmentBoxSelect();
					loadoutBinSelect();

				},1000)


			}
		 });

	}


	/*
	*	Delete the batch
	*/
	function deleteBatch(unique,schedule_id){

		$.ajax({
			url		:	app_url+"/delbatch",
			type	:	"POST",
			data 	:	data={"unique_id":unique,"schedule_id":schedule_id},
			success: function(r){


				$(".batch_holder").html("");
				$(".batch_holder").append(r);

				setTimeout(function(){

					dropdownData();
					summaryRender(unique);
					loadoutBinSelect();
					compartmentBoxSelect();
					loadoutBinSelect();

				},1000)


			}
		});

	}

	/*
	* Save Batch
	*/
	function saveBatch(batchData,unique){

		$.ajax({
			url		:	app_url+'/updatebatch',
			data 	:	batchData,
			type	: 	'POST',
			success: function(r){

				if(r.status == "true"){

					setTimeout(function(){

						dropdownData();
						summaryRender(unique);
						loadoutBinSelect();
						compartmentBoxSelect();
						loadoutBinSelect();

					},1000);

				}else{
					//error
					$(".batchMessageHolder").html("");
					$(".batchMessageHolder").append(r.message);
					$("#BatchDeliveryModal").modal('show');
					return false;
				}

			}
		});

	}

	/*
	*	summary render
	*/
	function summaryRender(unique_id){
		setTimeout(function(){
			$.ajax({
				url		:	app_url+'/summrender',
				data 	:	data = {'unique_id':unique_id},
				type 	:	"POST",
				success: function(result){

					$(".summdiv_b").html("");
					$(".summdiv_b").append(result);

				}
			});
		},100)
	}

	/*
	*	add loadout bin
	*/
	$(".btn-addloadout").click(function(){

		var sched_id = $(this).attr("sched-id");
		var counter = $(".lob-"+sched_id).length + 1;
		var unique = $(this).attr("unique-id");

		var loadout_bin = "<select id='loadoutBins-"+sched_id+"-"+counter+"' sched-id='"+sched_id+"' unique-id='"+unique+"'";
			loadout_bin += "class='lob-"+sched_id+" loadoutBins form-control col-md-12 input-sm form-inline'";
			loadout_bin += "style='background: #FFF; cursor:pointer'>";
			loadout_bin += "<option value='' selected='selected'>-</option>";
			for (var i = 1; i <= 12; i++){
				loadout_bin += "<option value="+i+">"+i+"</option>";
			}
			loadout_bin += "";
			loadout_bin += "</select>";

		$("#div-"+sched_id).append(loadout_bin);

		$(this).hide(function(){
			loadOutBinsSelectionList();
		});

	});

	// btn-add-compartment-data
	$(".btn-add-compartment-data").click(function(){
		alert("testing");
	});

	//console.log($(".truck_compts").length);
	// on load save the selection in the database
	/*
	sched-id,value,element_id,unique_id,selected_index
	*/
	//var defaults = [];
	$(".truck_compts").each(function( index ) {

		if($( this ).val() != "" || $( this ).val() != null){
			$( this ).val(index+1);
			var sched_id = $(this).attr("sched-id");
			var value = index+1;
			var element_id = $(this).attr("id");
			var unique_id = $(this).attr("unique-id");
			var selected_index = index+1;

			$.ajax({
					url :	app_url+"/loadoutbinscompartments",
					data:	{
							'sched_id'			: 	sched_id,
							'value'				:	value,
							'element_id'		: 	element_id,
							'unique_id'			:	unique_id,
							'selected_index'	:	selected_index
							},
					type: "POST",
					success: function(r){

					}
			});

			/*defaults.push({
							'sched_id'			: 	sched_id,
							'value'				:	value,
							'element_id'		: 	element_id,
							'unique_id'			:	unique_id,
							'selected_index'	:	selected_index
							});*/

		}

	});
	//console.log("testing");
	//console.log(defaults);
	// set the default selection
	//testingajax(defaults);

	var compartments_element = $(".truck_compts").length;
	var truck_compartments = {{count(array_slice($truck_compts,1))}};

	if(compartments_element > truck_compartments){

		alert("Unable to set load, because the distribution for compartments exceed the total compartments of the truck");
		$(".loading-panel").html("");
		$(".loading-panel").html("Redirecting to the scheduling page...");
		location.href = app_url+"/loading";

	}

});




</script>
