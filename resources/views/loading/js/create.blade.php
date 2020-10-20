<script type="text/javascript" src="{{ asset('js/ddslick.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){
	
	/*
	*	Load Truck functionality
	*/
	$(".container").delegate(".btn-kb-succ","click",function(){
		var batch_delivery = [];
		
		@forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
		
			   var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
			   
			   var sched_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('sched-id');
			   
			   var date_of_del = '{{$date_of_sched}}';
			   
			   var truck_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('truck-id');
			   
			   var farm_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('farm-id');
			   
			   var feeds_type_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('feeds-type-id');
			   
			   var medication_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('medication-id');

			   
			   //var driver_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('driver-id');
			    var driver_id = $(".driver-kb").val();
			   
			   var ticket = $(compartment.original[0].children[compartment.selectedIndex]).attr('ticket');
			   
			   var amount = $(compartment.original[0].children[compartment.selectedIndex]).attr('amount');
			   
			   var bin_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('bin-id');
			   
			   var compartment_number = {{$comp['compartment_number']}};
			   
			   batch_delivery.push({
				   	'sched_id':sched_id,
				   	'date_of_del':date_of_del,
					'truck_id':truck_id,
					'farm_id':farm_id,
					'feeds_type_id':feeds_type_id,
					'medication_id':medication_id,
					'driver_id':driver_id,
					'ticket':ticket,
					'amount':amount,
					'bin_id':bin_id,
					'compartment_number':compartment_number,
					'unique_id'	:	$(".ab-unique-id").val()
				   });                  
			   
		@empty
		
		@endforelse	   
		
		@forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)
		
			   var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
			   
			   var sched_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('sched-id');
			   
			   var date_of_del = '{{$date_of_sched}}';
			   
			   var truck_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('truck-id');
			   
			   var farm_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('farm-id');
			   
			   var feeds_type_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('feeds-type-id');
			   
			   var medication_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('medication-id');
			   
			   //var driver_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('driver-id');
			   var driver_id = $(".driver-kb").val();
			   
			   var ticket = $(compartment.original[0].children[compartment.selectedIndex]).attr('ticket');
			   
			   var amount = $(compartment.original[0].children[compartment.selectedIndex]).attr('amount');
			   
			   var bin_id = $(compartment.original[0].children[compartment.selectedIndex]).attr('bin-id');
			   
			   var compartment_number = {{$comp['compartment_number']}};
			  
		
				 batch_delivery.push({
					'sched_id':sched_id,
				   	'date_of_del':date_of_del,
					'truck_id':truck_id,
					'farm_id':farm_id,
					'feeds_type_id':feeds_type_id,
					'medication_id':medication_id,
					'driver_id':driver_id,
					'ticket':ticket,
					'amount':amount,
					'bin_id':bin_id,
					'compartment_number':compartment_number,
					'unique_id'	:	$(".ab-unique-id").val()
				   });  
			   
		@empty
		
		@endforelse	  
		
		
		var deliveries_arranged = {};
		
		for(var i=0; i<batch_delivery.length; i++){
			deliveries_arranged[i] = {};
			//console.log(compartment[i]['date_of_del']);
			//console.log(batch_delivery[i]['date_of_del'])
			deliveries_arranged[i]['sched_id'] = batch_delivery[i]['sched_id'];
			deliveries_arranged[i]['date_of_del'] = batch_delivery[i]['date_of_del'];
			deliveries_arranged[i]['truck_id'] = batch_delivery[i]['truck_id'];
			deliveries_arranged[i]['farm_id'] = batch_delivery[i]['farm_id'];
			deliveries_arranged[i]['feeds_type_id'] = batch_delivery[i]['feeds_type_id'];
			deliveries_arranged[i]['medication_id'] = batch_delivery[i]['medication_id'];
			deliveries_arranged[i]['driver_id'] = batch_delivery[i]['driver_id'];
			deliveries_arranged[i]['ticket'] = batch_delivery[i]['ticket'];
			deliveries_arranged[i]['amount'] = batch_delivery[i]['amount'];
			deliveries_arranged[i]['bin_id'] = batch_delivery[i]['bin_id'];
			deliveries_arranged[i]['compartment_number'] = batch_delivery[i]['compartment_number'];
			deliveries_arranged[i]['unique_id'] = batch_delivery[i]['unique_id'];
			
		}
	
		console.log(deliveries_arranged)
		$.ajax({
				url		:	app_url+"/scheduling/addbatch",
				type	:	"POST",
				data 	:	deliveries_arranged,
				success: function(data){
					alert("sent");
					window.location.replace(app_url+"/deliveries");
				}
			});
		
	})
	
	// hide
	$(".truck_holder").hide();
	$(".btn-kb-succ").hide();
	$(".bin_loadout_holder").hide();
	
	// invisible div for loudoutbins
	$(".invi_div_loudoutbin").hide();
	
	// invisible div for compartments
	$(".invi_div_compartments").hide();
	
	// hide the edit batch button
	$(".editbtnkb").hide();
	
	// hide the delete batch button
	$(".delbtnkb").hide();
	
	// remove the disable state of the tickets
	$(".tickets").each(function(index, element) {
        $(element).removeAttr("disabled");
    });
	
	/*
	*	save batch tickets
	*/
	$(".container").delegate(".btn-save-tickets","click",function(){
		var batch_tickets = [];
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
				batch_tickets.push({'sched-id' : $(e).attr("sched-id"),'tickets' : $(e).val()});
			}
		});	
		
		
		if(catcher == 0){
			var tickets_batch = {};
			
			for(var i = 0; i < batch_tickets.length; i++){
				tickets_batch[i] = {};
				tickets_batch[i]['sched-id'] = batch_tickets[i]['sched-id'];
				tickets_batch[i]['tickets'] = batch_tickets[i]['tickets'];
			}
			
			$.ajax({
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
						
						// populate the tickets on the loadout bins
						
						setTimeout(function(){
							dropdownData();	
							loadOutBinDisplay()	
							loadoutBinSelect();
							compartmentBoxSelect();
						},3000);
						
						location.reload();
						
					} else {
						//error
						$(".batchMessageHolder").html("");
						$(".batchMessageHolder").append("Something went wrong plaese try again.");
						$("#BatchDeliveryModal").modal('show');	
					} 
				}	
			});	
			
					
			
		}
		
	});
	
	
	
	/*
	*	Loudout bins display
	*/
	function loadOutBinDisplay(){
	
		var tickets_without_val_count = 0;
		$(".tickets").each(function(i,e){ 
			if($(e).val() == "")tickets_without_val_count++;
		});
		
		if(tickets_without_val_count == 0){
		
			$(".loading-stick-circle-bin-loadout").show();
			$(".loading-stick-circle-bin-loadout").delay(2000).hide(2000,function() {
				$(".bin_loadout_holder").slideDown(200);
				compartmentBoxSelect();
				loadoutBinSelect()
				
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
				
			});
			
		} else {
			
			$(".loading-stick-circle-bin-loadout").hide();
			loadoutBinSelect();
			
		}
		
		
	}
	
	loadOutBinDisplay();
	
	/*
	*	Detect selected tickets on the loudout bins
	*/
	function detectSelectedTicketsLoudout(){
		
		var counter = 0;
		
		for(var i = 0; i <= 11; i++){
			
			var tickets = $("#binLoadout_"+i).data("ddslick");
			var selectedData = $(tickets.original[0].children[tickets.selectedIndex]);
			
			if(selectedData.text() != "select"){
				counter++	
			}
				
		}
		
		return counter;
		
	}
	
	
	/*
	*	Loadout Bins On Selected function
	*/
	function loadoutBinSelect(){
		//console.log("loadoutBinSelect() method called")
		var colors = {
			'0'		:	'#fca600',
			'1'		:	'#C3C50A',
			'2'		:	'#85fa00',
			'3'		:	'#00fd69',
			'4'		:	'#00ffc3',
			'5'		:	'#02c5f5',
			'6'		:	'#007fff',
			'7'		:	'#5538ff',
			'8'		:	'#bd3fff',
			'9'		:	'#fe42d5',
			'10'	:	'#ff2b7d',
			'11'	:	'#fb2700'
		};
		
		 var tickets_count = 0;
		 $(".tickets").each(function(i,e){
			if($(e).val() != "") tickets_count++;	 
		 });
		 
		 var ticket_and_color = [];
		 
		 var counter = 0;
		 for(var i = 0; i <= 11; i++){
			 
			$("#binLoadout_"+i).ddslick("destroy"); 
			
			var loadOutBinColor = $("#binLoadout_"+i).attr("color");
			
			$("#binLoadout_"+i).ddslick({
				width: 98,
				background: colors[i],
				border: 'none',
				onSelected:function(data){
					 
					 if(data.selectedData.text != "select"){
						 
						var ticket_selected = data.selectedData.text; 
						var sched_id = $(data.original[0].children[data.selectedIndex]).attr('sched-id');
						var loadoutbinnumber = $(data.original[0].children[data.selectedIndex]).attr('loudout-bin-number');
						var summ = Number(loadoutbinnumber) + 1;
						
						//loadoutBinSelectUpdate(ticket_selected,loadOutBinColor);
						console.log(data.original[0].children[data.selectedIndex]);
						// Append the loadout bin in the summary
						var summary_ticket = "Loadout Bin #: "+summ;
						//console.log(sched_id);
						$("#summ-loadoutbin-"+sched_id).html("");
						$("#summ-loadoutbin-"+sched_id).append(summary_ticket);
						
						ticket_and_color.push({'ticket':ticket_selected,'color':loadOutBinColor})
						//console.log(ticket_and_color);
						counter = counter+1;
						//console.log("tickets count: "+tickets_count);
						
						if(tickets_count == counter){
							$(".truck_holder").show();
							$(".btn-kb-succ").show();	
						}
						
						if(tickets_count == detectSelectedTicketsLoudout()){
							
							//display the truck shit	
							$(".truck_holder").show();
							
							//disable loadout bin selection
							$(".invi_div_loudoutbin").show();
							
						}
					 }
					 
				 }
			 });
			 
			 
				 
		 }// end for()
	}
	
	/*
	*	loadoutBinSelectUpdate
	*/
	function loadoutBinSelectUpdate(ticket_to_remove,binCOlor){
		
		// get all the ticket
		// get the loadoutbin id
		// disable the selected loadout bin
		// exclude the selected ticket in the loadout bin selection
		// destoy ddslick and empty the data
		// append new data
		// build ddslick
		// attach the bincolor to the ticket
		
	}
	
	/*
	*	Compartment Boxes On Selected function
	*/
	function compartmentBoxSelect(){
		
		 var truck_capacity_count = 0;
		 var counter = 0;
		 var compartment_amount = 3;
		 var avail_ton_summ = $('.avail_ton_summ').text();
		 var final_farm_sum_amount = "";
		 var selected_ticket = "";
		 
		 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
		 	truck_capacity_count = truck_capacity_count+3;
		 @empty
		 @endforelse
		 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)
		 	truck_capacity_count = truck_capacity_count+3;
		 @empty
		 @endforelse
		 
		 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
			$("#box-comp-{{$comp['compartment_number']}}").ddslick({
				width: 98,
				background: '#333',
				border: 'none',
				onSelected:function(data){
					 
					 if(data.selectedData.text != "Please Select"){
						
						var ticket_amount = $(data.original[0].children[data.selectedIndex]).attr('amount');
						var sched_id = $(data.original[0].children[data.selectedIndex]).attr('sched-id');
						var ticket_selected =  $(data.original[0].children[data.selectedIndex]).attr('ticket');
						var compartment_number = $("#box-comp-{{$comp['compartment_number']}}").closest("select").attr("comp-number");
						
						// disabler for the current compartment
						$("#comp_disabler_{{$comp['compartment_number']}}").show();
						
						//if(selected_ticket != sched_id){
							//console.log("selected ticket: "+selected_ticket+" sched_id:"+sched_id);
							$(".comp_caps_{{$comp['compartment_number']}}").text("");
							$(".comp_caps_{{$comp['compartment_number']}}").text(compartment_amount);
							
							var summ_farm_amount = $("#summ_farm_amount-"+sched_id).text();
							summ_farm_amount = (summ_farm_amount == 0 ? '' : summ_farm_amount);
							summ_farm_amount_base = $(".summ_farm_amount_base_"+sched_id).text();
							
							//console.log("base farm amount 1: "+summ_farm_amount_base);
							//console.log("sum farm amount 1: "+summ_farm_amount);
							//console.log("final farm amount 1:"+final_farm_sum_amount);
							
							// update the farm sum amount
							final_farm_sum_amount = summ_farm_amount_base - summ_farm_amount;
							
							if(summ_farm_amount == summ_farm_amount_base){ // amount is loaded from this ticket
								getSelectedCompartment(ticket_selected);
								final_farm_sum_amount = 0;							
							} else {	
								summ_farm_amount = Number(summ_farm_amount)+3;
								// validaiton for per farm summary
								summ_farm_amount = (summ_farm_amount < summ_farm_amount_base ? summ_farm_amount : summ_farm_amount_base );
								$("#summ_farm_amount-"+sched_id).text(summ_farm_amount);
								
							}
							
							//console.log("base farm amount 2: "+summ_farm_amount_base);
							//console.log("sum farm amount 2: "+summ_farm_amount);
							//console.log("final farm amount 2: "+final_farm_sum_amount);
							//console.log('Compartment Number: {{$comp['compartment_number']}}');
							//console.log('Ticket: '+ data.selectedData.text);
							
							var summ_text = "Compartment #: <span class='comtext-"+sched_id+"'>"+{{$comp['compartment_number']}}+"</span>";
							
							
							//console.log($(".comtext-"+sched_id).text());
							// clear the summary compartment
							
							if($("#summ-compartment-"+sched_id).is(':empty')){
								$("#summ-compartment-"+sched_id).html("");
								$("#summ-compartment-"+sched_id).append(summ_text)
							}else{
								//$("#summ-compartment-"+sched_id).html()+","+{{$comp['compartment_number']}};
								$(".comtext-"+sched_id).append(","+{{$comp['compartment_number']}})
								//console.log($(".comtext-"+sched_id).append(","+{{$comp['compartment_number']}}))
							}
							
							
							if(avail_ton_summ == 0){
								alert("Truck is full");
							} else {
								
								if(summ_farm_amount_base == 1){
									compartment_amount = 1;
								} else if(summ_farm_amount_base == 2){
									compartment_amount = 2;
								}else{
									if(final_farm_sum_amount == 2){
										compartment_amount = 2;	
									}else if(final_farm_sum_amount == 1){
										compartment_amount = 1;	
									}else{
										compartment_amount = 3;
									}
								}
								
								$(".comp_caps_{{$comp['compartment_number']}}").text("");
								$(".comp_caps_{{$comp['compartment_number']}}").text(compartment_amount);
								
								var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
								$(compartment.original[0].children[compartment.selectedIndex]).attr('amount',compartment_amount);
								
								avail_ton_summ = avail_ton_summ - compartment_amount;
								avail_ton_summ = (avail_ton_summ  < 0 ? 0 : avail_ton_summ);
								$('.avail_ton_summ').text("");
								$('.avail_ton_summ').text(avail_ton_summ);
								
							}
							
							setTimeout(function(){
								
								if(summ_farm_amount == summ_farm_amount_base){ // amount is loaded from this ticket
									getSelectedCompartment(ticket_selected);
									final_farm_sum_amount = 0;							
								}
								
								if(final_farm_sum_amount == 2){
									//console.log(final_farm_sum_amount);
								}else{
									//console.log("base farm amount 3: "+summ_farm_amount_base);
									//console.log("sum farm amount 3: "+summ_farm_amount);
									//console.log("final farm amount 3:"+final_farm_sum_amount);
								}
								
								compartment_amount = 3;
								
							},1000);
							
						//} //if(selected_ticket != null)	
						
						selected_ticket = sched_id;
					 	//console.log("selected ticket: "+selected_ticket+" sched_id:"+sched_id);
					 } //if(data.selectedData.text != "Please Select")
					 
				 }//onSelected:function(data)
			 });
		 @empty
		 @endforelse
		 
		 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)		 
		 	$("#box-comp-{{$comp['compartment_number']}}").ddslick({
				width: 98,
				background: '#333',
				border: 'none',
				onSelected:function(data){
					if(data.selectedData.text != "Please Select"){
						
						var ticket_amount = $(data.original[0].children[data.selectedIndex]).attr('amount');
						var sched_id = $(data.original[0].children[data.selectedIndex]).attr('sched-id');
						var ticket_selected =  $(data.original[0].children[data.selectedIndex]).attr('ticket');
						var compartment_number = $("#box-comp-{{$comp['compartment_number']}}").closest("select").attr("comp-number");
						
						// disabler for the current compartment
						$("#comp_disabler_{{$comp['compartment_number']}}").show();
						
						//if(selected_ticket != sched_id){
							console.log("selected ticket: "+selected_ticket+" sched_id:"+sched_id);
							$(".comp_caps_{{$comp['compartment_number']}}").text("");
							$(".comp_caps_{{$comp['compartment_number']}}").text(compartment_amount);
							
							var summ_farm_amount = $("#summ_farm_amount-"+sched_id).text();
							summ_farm_amount = (summ_farm_amount == 0 ? '' : summ_farm_amount);
							summ_farm_amount_base = $(".summ_farm_amount_base_"+sched_id).text();
							
							//console.log("base farm amount 1: "+summ_farm_amount_base);
							//console.log("sum farm amount 1: "+summ_farm_amount);
							//console.log("final farm amount 1:"+final_farm_sum_amount);
							
							// update the farm sum amount
							final_farm_sum_amount = summ_farm_amount_base - summ_farm_amount;
							
							if(summ_farm_amount == summ_farm_amount_base){ // amount is loaded from this ticket
								getSelectedCompartment(ticket_selected);
								final_farm_sum_amount = 0;							
							} else {	
								summ_farm_amount = Number(summ_farm_amount)+3;
								// validaiton for per farm summary
								summ_farm_amount = (summ_farm_amount < summ_farm_amount_base ? summ_farm_amount : summ_farm_amount_base );
								$("#summ_farm_amount-"+sched_id).text(summ_farm_amount);
								
							}
							
							//console.log("base farm amount 2: "+summ_farm_amount_base);
							//console.log("sum farm amount 2: "+summ_farm_amount);
							//console.log("final farm amount 2: "+final_farm_sum_amount);
							
							var summ_text = "Compartment #: <span class='comtext-"+sched_id+"'>"+{{$comp['compartment_number']}}+"</span>";
							
							
							//console.log($(".comtext-"+sched_id).text());
							// clear the summary compartment
							
							if($("#summ-compartment-"+sched_id).is(':empty')){
								$("#summ-compartment-"+sched_id).html("");
								$("#summ-compartment-"+sched_id).append(summ_text)
							}else{
								//$("#summ-compartment-"+sched_id).html()+","+{{$comp['compartment_number']}};
								$(".comtext-"+sched_id).append(","+{{$comp['compartment_number']}})
								//console.log($(".comtext-"+sched_id).append(","+{{$comp['compartment_number']}}))
							}
							
							if(avail_ton_summ == 0){
								alert("Truck is full");
							} else {
								
								if(summ_farm_amount_base == 1){
									compartment_amount = 1;
								} else if(summ_farm_amount_base == 2){
									compartment_amount = 2;
								}else{
									if(final_farm_sum_amount == 2){
										compartment_amount = 2;	
									}else if(final_farm_sum_amount == 1){
										compartment_amount = 1;	
									}else{
										compartment_amount = 3;
									}
								}
								
								$(".comp_caps_{{$comp['compartment_number']}}").text("");
								$(".comp_caps_{{$comp['compartment_number']}}").text(compartment_amount);
								
								var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
								$(compartment.original[0].children[compartment.selectedIndex]).attr('amount',compartment_amount);
								
								avail_ton_summ = avail_ton_summ - compartment_amount;
								avail_ton_summ = (avail_ton_summ  < 0 ? 0 : avail_ton_summ);
								$('.avail_ton_summ').text("");
								$('.avail_ton_summ').text(avail_ton_summ);
								
							}
							
							setTimeout(function(){
								
								if(summ_farm_amount == summ_farm_amount_base){ // amount is loaded from this ticket
									getSelectedCompartment(ticket_selected);
									final_farm_sum_amount = 0;							
								}
								
								if(final_farm_sum_amount == 2){
									//console.log(final_farm_sum_amount);
								}else{
									//console.log("base farm amount 3: "+summ_farm_amount_base);
									//console.log("sum farm amount 3: "+summ_farm_amount);
									//console.log("final farm amount 3:"+final_farm_sum_amount);
								}
								
								compartment_amount = 3;
								
							},1000);
							
						//} //if(selected_ticket != null)	
						
						selected_ticket = sched_id;
					 	//console.log("selected ticket: "+selected_ticket+" sched_id:"+sched_id);
					 } //if(data.selectedData.text != "Please Select")
				 }
			 });
		 @empty
		 @endforelse
		
	}
	
	/*
	*	get the selected compartment #
	*/	
	function getSelectedCompartment(ticket_to_remove){
		
		var dropdown_data = "";
	
			@forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
				var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
				var ticket_to_check = $(compartment.original[0].children[compartment.selectedIndex]).attr('ticket');
				
				if(ticket_to_check == null){
					// update the dropdown
					dropdown_data = dropdownDataUpdate(ticket_to_remove);	
					
					var $element = $("label.dd-option-text:contains("+ticket_to_remove+")");
						$element.parent("a").remove();
						$element.remove();
					
				} else {
					
					dropdown_data = dropdownDataUpdate(ticket_to_remove);
					
				}
			 @empty
			 @endforelse
			 
			 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)
				var compartment = $("#box-comp-{{$comp['compartment_number']}}").data("ddslick");
				var ticket_to_check = $(compartment.original[0].children[compartment.selectedIndex]).attr('ticket');
				
				if(ticket_to_check == null){
					
					// update the dropdown
					dropdown_data = dropdownDataUpdate(ticket_to_remove);	
					
					var $element = $("label.dd-option-text:contains("+ticket_to_remove+")");
						$element.parent("a").remove();
						$element.remove();
					
				} else {
					
					dropdown_data = dropdownDataUpdate(ticket_to_remove);
										
				}
			 @empty
			 @endforelse
		
	}
	
	/*
	*	ticket data removal
	*/
	function dropdownDataUpdate(ticket_to_remove){
		
		var dropdown_options = [];
		
		var $tickets = $(".tickets");
		
		//console.log($tickets.val());
		
		$tickets.each(function(index, element) {
			if($(element).val() != ticket_to_remove){
				var tickets_value = $(element).val();
				
				var sched_id = $(element).attr("sched-id");
				
				$.ajax({
					url	 :	app_url+'/requestsched',
					data :	{'sched_id':sched_id},
					type : "POST",
					success: function(data){
						var data_description = data.farm_name +" - "+ data.bin_number;
						setTimeout(function(){
							dropdown_options.push(
							{
								'ticket':tickets_value,
								'data_description':data_description,
								'data_amount':data.amount,
								'data_id':data.schedule_id,
								'date_of_delivery':data.date_of_delivery,
								'truck_id':data.truck_id,
								'farm_id':data.farm_id,
								'feeds_type_id':data.feeds_type_id,
								'medication_id':data.medication_id,
								'driver_id':data.driver_id,
								'bin_id':data.bin_id
							}
							);
						},2000)
					}
				});
			}
		});
			
		
		// append the data
		setTimeout(function(){
			
			var dropdown_loadout = "<option data-description=''>select</option>";
			$.each(dropdown_options, function(index,value){
				
				if(value.ticket != ""){
					dropdown_loadout +="<option value='"+value.ticket+"' sched-id='"+value.data_id+"'>"+value.ticket+"</option>";	
				}
				
			})
			
			var dropdown_boxes = "<option value=''>Please Select</option>";
			
			$.each(dropdown_options, function(index,value){
				
				if(value.ticket != ""){
					
					dropdown_boxes +="<option value='"+value.ticket+"' "; 
					dropdown_boxes +="amount='"+value.data_amount+"' ";
					dropdown_boxes +="sched-id='"+value.data_id+"' ";
					dropdown_boxes +="data-description='"+value.data_description+"' ";
					dropdown_boxes +="date-of-del='"+value.date_of_delivery+"' ";
					dropdown_boxes +="truck-id='"+value.truck_id+"' ";
					dropdown_boxes +="farm-id='"+value.farm_id+"' ";
					dropdown_boxes +="feeds-type-id='"+value.feeds_type_id+"' ";
					dropdown_boxes +="medication-id='"+value.medication_id+"' ";
					dropdown_boxes +="driver-id='"+value.driver_id+"' ";
					dropdown_boxes +="ticket='"+value.ticket+"' ";
					dropdown_boxes +="bin-id='"+value.bin_id+"' ";
					dropdown_boxes +="compartment-number='' >"+value.ticket+"</option>";	
					
				}
				
			})			
			 
			return dropdown_boxes;			 
				
		},3000)
		
	}
	
	
	loadoutBinSelect();
	compartmentBoxSelect();
	
	// Tickets Dropdown Data
	function dropdownData(){		
		
		var dropdown_options = [];
		
		var $tickets = $(".tickets");
		
		//console.log($tickets.val());	
		
		
		$tickets.each(function(index, element) {

			var tickets_value = $(element).val();
			
			var sched_id = $(element).attr("sched-id");
			
			$.ajax({
				url	 :	app_url+'/requestsched',
				data :	{'sched_id':sched_id},
				type : "POST",
				success: function(data){
					var data_description = data.farm_name +" - "+ data.bin_number;
					setTimeout(function(){
						dropdown_options.push(
						{
							'ticket':tickets_value,
							'data_description':data_description,
							'data_amount':data.amount,
							'data_id':data.schedule_id,
							'date_of_delivery':data.date_of_delivery,
							'truck_id':data.truck_id,
							'farm_id':data.farm_id,
							'feeds_type_id':data.feeds_type_id,
							'medication_id':data.medication_id,
							'driver_id':data.driver_id,
							'bin_id':data.bin_id
						}
						
						);
					},2000)
				}
			});
			
		});
			
		
		// append the data
		setTimeout(function(){
			
			var dropdown_loadout = "<option data-description=''>select</option>";
			$.each(dropdown_options, function(index,value){
				
				if(value.ticket != ""){
					dropdown_loadout +="<option value='"+value.ticket+"' sched-id='"+value.data_id+"'>"+value.ticket+"</option>";	
				}
				
			})
			
			var dropdown_boxes = "<option value=''>Please Select</option>";			
			$.each(dropdown_options, function(index,value){
				
				if(value.ticket != ""){
					
					dropdown_boxes +="<option value='"+value.ticket+"' "; 
					dropdown_boxes +="amount='"+value.data_amount+"' ";
					dropdown_boxes +="sched-id='"+value.data_id+"' ";
					dropdown_boxes +="data-description='"+value.data_description+"' ";
					dropdown_boxes +="date-of-del='"+value.date_of_delivery+"' ";
					dropdown_boxes +="truck-id='"+value.truck_id+"' ";
					dropdown_boxes +="farm-id='"+value.farm_id+"' ";
					dropdown_boxes +="feeds-type-id='"+value.feeds_type_id+"' ";
					dropdown_boxes +="medication-id='"+value.medication_id+"' ";
					dropdown_boxes +="driver-id='"+value.driver_id+"' ";
					dropdown_boxes +="ticket='"+value.ticket+"' ";
					dropdown_boxes +="bin-id='"+value.bin_id+"' ";
					dropdown_boxes +="compartment-number='' >"+value.ticket+"</option>";	
					
				}
				
			});
			
			
			 for(var i = 0; i <= 11; i++){				
				$("#binLoadout_"+i).ddslick("destroy");			 
			 }
			
			 var $ticketSelect = $('.dd-container');
			 $ticketSelect.each(function(index, element) {
				  // destroy the ddslick
				   $(element).ddslick("destroy");
					
			 });
			 
			 var colors = {
				'0'		:	'#fca600',
				'1'		:	'#C3C50A',
				'2'		:	'#85fa00',
				'3'		:	'#00fd69',
				'4'		:	'#00ffc3',
				'5'		:	'#02c5f5',
				'6'		:	'#007fff',
				'7'		:	'#5538ff',
				'8'		:	'#bd3fff',
				'9'		:	'#fe42d5',
				'10'	:	'#ff2b7d',
				'11'	:	'#fb2700'
			};
			
			 
			
			 for(var i = 0; i <= 11; i++){
								 
			  // remove existing options
			   $("#binLoadout_"+i).empty();
			   // append the tickets form the tickets dropbox
			   $("#binLoadout_"+i).append(dropdown_loadout);
			  
			   $("#binLoadout_"+i).ddslick({
			
					width: 98,
					background: colors[i],
					border: 'none'
					
				});	
					 
			 }
			
			 
			 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
			 	$("#box-comp-{{$comp['compartment_number']}}").empty();
				$("#box-comp-{{$comp['compartment_number']}}").append(dropdown_boxes);
				$(dropdown_boxes).attr("compartment-number",{{$comp['compartment_number']}});
				/*$("#box-comp-{{$comp['compartment_number']}}").ddslick({
						width: 98,
						background: '#333',
						border: 'none'
				});*/
			 @empty
			 @endforelse 
			 @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)
			 	$("#box-comp-{{$comp['compartment_number']}}").empty();
				$("#box-comp-{{$comp['compartment_number']}}").append(dropdown_boxes);
				$(dropdown_boxes).attr("compartment-number",{{$comp['compartment_number']}});
				/*$("#box-comp-{{$comp['compartment_number']}}").ddslick({
						width: 98,
						background: '#333',
						border: 'none'
				});	*/
			 @empty
			 @endforelse 
			 
			 
			 loadoutBinSelect();
			 compartmentBoxSelect();
			 	
		},3000)
		
	}
		
	
	
	/*
	*	Truck page1
	*/
	$(".truck1page").click(function() {
		
		$(".firstbatch_kb").hide();
		$(".secondbatch_kb").fadeIn();	
		
	});
	
	/*
	*	Truck page2
	*/
	$(".truck2page").click(function() {
		
		$(".firstbatch_kb").fadeIn();
		$(".secondbatch_kb").hide();	
		
	});
	$(".summ_kb_ton").height($(".summleft_k").height());
		
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
		
		$("#delbtnkb-"+schedule_id).hide();
		
		$(this).hide(function(){
			$("#savebtn-"+schedule_id).show();
			$(".add-batch-view").hide();	
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
				
				$(".savebtn").hide(function(){
					$("#delbtnkb-"+schedule_id).show();
					$("#editbtnkb-"+schedule_id).show();
					$(".delbtnkb").show();
					$(".editbtnkb").show();
					$(".add-batch-view").show();
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
	
	
});	




</script>