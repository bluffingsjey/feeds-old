<script type="text/javascript">

loadResults();
/*
*	loadResults()
*
*	load the results on animal group page
*/
function loadResults(){

	var data = {
		'type'			:	$("#farm_type").val(),
		'date_from'	:	$("#date_from").val(),
		'date_to'		:	$("#date_to").val(),
		'sort'			:	$("#sort_by").val()
	}

	$.ajax({
		url		:	app_url+"/animalmovementlanding",
		data	: 	data,
		type	:	"GET",
		success	: 	function(r){
				$(".loading").hide(function(){
						$(".table_holder").html("");
						$(".table_holder").html(r);
						collapsers();
				});
		}
	});

}


// date picker
var dateFormat = "yy-mm-dd",
		from = $( "#date_from" )
			.datepicker({
				changeMonth: true,
		changeYear: true,
				numberOfMonths: 1,
		dateFormat: 'yy-mm-dd'
			})
			.on( "change", function() {
				to.datepicker( "option", "minDate", getDate( this ) );
			}),
		to = $( "#date_to" ).datepicker({
			changeMonth: true,
	changeYear: true,
			numberOfMonths: 1,
	dateFormat: 'yy-mm-dd'
		})
		.on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		});

	function getDate( element ) {
		var date;
		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		} catch( error ) {
			date = null;
		}

		return date;
	}


$(".btn-search").click(function(){

	var data = {
		'type'		:	$("#farm_type").val(),
		'date_from'	:	$("#date_from").val(),
		'date_to'	:	$("#date_to").val(),
		'sort'		:	$("#sort_by").val()
	}

	animalFilter(data)

})

var loading = "<div class='loading-stick-circle loading'>";
		loading += "<img src='/css/images/loader-stick.gif' />";
		loading += "Please wait, Rendering...";
		loading += "</div>";

var loading_transfer = "<div style='width: 230px; float: right; margin-right: -182px;'>";
		loading_transfer += "Getting Group Bins...";
		loading_transfer += "</div>";

var empty = "<div class='col-md-12'>"
		empty += "<h3 class='text-center'>No result...</h3>";
		empty += "</div>";

/*
* animalFilter(data)
*
* filter for animal group page search
*/
function animalFilter(data){
		$(".table_holder").empty();

		$(".table_holder").append(loading);

		$.ajax({
			url		:	app_url+"/animalmovementfilter",
			data	: data,
			type	:	"GET",
			success	: 	function(r){

				$(".loading").hide(function(){
					$(".table_holder").html("");
					if(r == 'none'){
						$(".table_holder").html(empty);
					}else{
						$(".table_holder").html(r);
					}
					collapsers();
				});

			}
		});
	}

$(document).ready(function(){

});


var all_data = {!!$all!!};
var all_drivers = {!!$drivers!!}

$(".container").delegate(".saveEditedTransfer","click", function(e){
	var element_ids = $(this).attr("element_ids");
	var transfer_id = $(this).attr("transfer_id");
	var total_pigs = $(this).attr("total_pigs");
	var group_name = $(this).attr("group_name");
	var transfer_data = {
		'transfer_id'					: transfer_id,
		'group_name'					:	group_name,
		'total_pigs'					:	total_pigs,
		'group_id'						:	$(this).attr("group_id"), // same as group_from
		'group_from' 					: $(this).attr("group_id"),
		'group_to_previous'		: $(".group_to_previous"+element_ids+transfer_id).val(),
		'group_to' 						: $("#group_to"+element_ids+transfer_id).val(),
		'date' 								: $(".date"+element_ids+transfer_id).val(),
		'empty_weight'				:	$(".empty_weight"+element_ids+transfer_id).val(),
		'ave_weight'					:	$(".ave_weight"+element_ids+transfer_id).val(),
		'driver_id'						:	$(".driver-"+element_ids+transfer_id).val(),
		'full_weight'					:	$(".full_weight"+element_ids+transfer_id).val(),
		'shipped'							:	$(".shipped"+element_ids+transfer_id).val(),
		'received'						:	$(".received"+element_ids+transfer_id).val(),
		'dead'								:	$(".dead"+element_ids+transfer_id).val(),
		'poor'								:	$(".poor"+element_ids+transfer_id).val(),
		'farm_count'					:	$(".farm_count"+element_ids+transfer_id).val(),
		'final_count'					:	$(".final_count"+element_ids+transfer_id).val(),
		'notes'								:	$(".notes-"+element_ids+transfer_id).val(),
		'group_type'					:	$(this).attr("group_type")
	}

	var data = {
		'group_from'		:	$(this).attr("group_id"),//$(".group_from"+element_ids+transfer_id).val(),
		'group_to'			:	$("#group_to"+element_ids+transfer_id).val(),
		'group_type'		:	$(this).attr("group_type"),
		'current_pigs'	:	total_pigs
	};
	console.log("update",transfer_data,data,element_ids,group_name);
	updateTransfer(transfer_data,transfer_data,data,element_ids,group_name)
});

$(".container").delegate(".deleteTransfer","click", function(e){
	var id = $(this).attr("transfer_id");
	var total_pigs = $(this).attr("total_pigs");
	var element_ids = $(this).attr("element_ids");
	var group_name = $(this).attr("group_name");
	var data = {
		'transfer_id'					: id,
		'group_name'					:	group_name,
		'total_pigs'					:	total_pigs,
		'group_id'						:	$(this).attr('group_id'), // same as group_from
		'group_from' 					: $(this).attr('group_id'),
		'group_to_previous'		: $(this).attr('group_from_previous'),
		'group_to' 						: $(this).attr('group_to'),
		'date' 								: $(this).attr('date'),
		'empty_weight'				:	$(this).attr('empty_weight'),
		'ave_weight'					:	$(this).attr('ave_weight'),
		'driver_id'						:	$(this).attr('driver_id'),
		'full_weight'					:	$(this).attr('full_weight'),
		'shipped'							:	$(this).attr('shipped'),
		'received'						:	$(this).attr('received'),
		'dead'								:	$(this).attr('dead'),
		'poor'								:	$(this).attr('poor'),
		'farm_count'					:	$(this).attr('farm_count'),
		'final_count'					:	$(this).attr('final_count'),
		'group_type'					:	$(this).attr("group_type")
	};
	console.log("delete",id,data,element_ids,group_name);
	deleteTransfer(data,id,data,element_ids,group_name)
});

$(".container").delegate(".finalizeTransfer","click", function(e){

		$(".transfer-alert-div-"+transfer_id).html("");

		var element_ids = $(this).attr("element_ids");
		var transfer_id = $(this).attr('transfer_id');
		var bins_to = $(".bins_to"+element_ids+transfer_id).serializeArray();
		var bins_from = $(".bins_from"+element_ids+transfer_id).serializeArray();
		var num_of_pigs = $(".num_of_pigs"+element_ids+transfer_id).serializeArray();
		var num_of_pigs_from = $(".num_of_pigs_from"+element_ids+transfer_id).serializeArray();
		var num_of_pigs_dead = $(".num_of_pigs_dead"+element_ids+transfer_id).serializeArray();
		var num_of_pigs_poor = $(".num_of_pigs_poor"+element_ids+transfer_id).serializeArray();
		var group_name = $(this).attr('group_name');
		var shipped_pigs = parseInt($(this).attr('shipped'));
		var dead_pigs = $(this).attr('dead');
		var poor_pigs = $(this).attr('poor');
		var final_count = parseInt($(this).attr('final_count'));

		console.log(num_of_pigs_from);

		var animal_data = {
			'transfer_id'					: $(this).attr('transfer_id'),
			'transfer_type'				:	$(this).attr('transfer_type'),
			'group_name'					:	$(this).attr('group_name'),
			'total_pigs'					:	$(this).attr('total_pigs'),
			'group_id'						:	$(this).attr('group_id'), // same as group_from
			'group_from' 					: $(this).attr('group_from'),
			'group_to_previous'		: $(this).attr('group_from_previous'),
			'group_to' 						: $(this).attr('group_to'),
			'date' 								: $(this).attr('date'),
			'empty_weight'				:	$(this).attr('empty_weight'),
			'ave_weight'					:	$(this).attr('ave_weight'),
			'driver_id'						:	$(this).attr('driver_id'),
			'full_weight'					:	$(this).attr('full_weight'),
			'shipped'							:	shipped_pigs,
			'received'						:	$(this).attr('received'),
			'dead'								:	dead_pigs,
			'poor'								:	poor_pigs,
			'farm_count'					:	$(this).attr('farm_count'),
			'final_count'					:	final_count,
			'group_type'					:	$(this).attr("group_type")
		};

		var data = {
			'transfer_data'				:	animal_data,
			'bins_from_pigs'			:	num_of_pigs_from,
			'bins_from'						:	bins_from,
			'bins_to'							:	bins_to,
			'num_of_pigs'					:	num_of_pigs,
			'num_of_pigs_dead'		:	num_of_pigs_dead,
			'num_of_pigs_poor'		:	num_of_pigs_poor
		};

		var total_number_of_pigs = 0;
		var bins_from_counter = [];
		var selection_bins_from_pigs = [];
		var result = [];
		var error = "";

		//console.log("bins_from",bins_from);
		//console.log("bins_to",bins_to)
		$.each(bins_from, function(k,v){
			var number_of_pigs_from = parseInt($(".bin_pig_"+element_ids+transfer_id+v['value']).text());
			var result_pigs_from = number_of_pigs_from - parseInt(num_of_pigs[k]['value']);

			var entered_pigs_from = num_of_pigs_from[k]['value']
			if(entered_pigs_from == ""){
				console.log("from");
				entered_pigs_from = 0;
			} else {
				entered_pigs_from = parseInt(num_of_pigs_from[k]['value']);
			}

			var number_of_pigs_to = num_of_pigs[k]['value'];
			if(number_of_pigs_to == ""){
				console.log("to");
				number_of_pigs_to = 0;
			} else {
				number_of_pigs_to = parseInt(num_of_pigs[k]['value']);
			}

			var dead = num_of_pigs_dead[k]['value'];
			if(dead == ""){
				dead = 0;
			} else {
				dead = parseInt(num_of_pigs_dead[k]['value'])
			}

			var poor = num_of_pigs_poor[k]['value'];
			if(poor == ""){
				poor = 0;
			} else {
				poor = parseInt(num_of_pigs_poor[k]['value']);
			}

			bins_from_counter.push(v['value']);

			result.push({
	 									 'bin_id_from'				:	v['value'],
	 									 'pigs_from'					:	number_of_pigs_from,
	 									 'entered_pigs_from'	:	entered_pigs_from,//parseInt(num_of_pigs_from[k]['value']),
	 									 'bin_id_to'					:	bins_to[k]['value'],
	 									 'entered_pigs_to'		:	number_of_pigs_to,//parseInt(num_of_pigs[k]['value']),
	 									 'dead'								:	dead,//parseInt(num_of_pigs_dead[k]['value']),
	 									 'poor'								:	poor,//parseInt(num_of_pigs_poor[k]['value'])
	 								 });

			selection_bins_from_pigs.push({'number_of_pigs_from':number_of_pigs_from});
			total_number_of_pigs = parseInt(total_number_of_pigs) + parseInt(num_of_pigs[k]['value']);
		});

		//console.log(result);

		var from_counter = arrayCounter(bins_from_counter);
		var group_from_pigs = 0;
		var group_to_pigs = 0;
		var group_to_dead_pigs = 0;
		var group_to_poor_pigs = 0;
		var group_final_count = 0;

		var pigs_from = 0;
		var pigs_to = 0;
		var dead = 0;
		var poor = 0;
		var final_pigs_to = 0;
		var total_final_pigs_to = 0;
		var final_count_pigs = 0;

		$.each(result, function(k,v){
				//if(bin_id_from == v['bin_id_from']){
					pigs_from = pigs_from + parseInt(v['entered_pigs_from']);
					pigs_to = pigs_to + parseInt(v['entered_pigs_to']);
					//final_pigs_to = parseInt(v['entered_pigs_to']) + parseInt(v['dead']) + parseInt(v['poor']);
					final_count_pigs = parseInt(v['f']) + parseInt(v['poor']);
					total_final_pigs_to = total_final_pigs_to + final_pigs_to;
					dead = dead + parseInt(v['dead']);
					poor = poor + parseInt(v['poor']);
				//}

				if(parseInt(v['entered_pigs_from']) == 0 && parseInt(v['entered_pigs_to']) == 0 && parseInt(v['dead']) == 0 && parseInt(v['poor']) != 0){
					error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs to/dead</strong> or <strong>Poor</strong> per row.</p>";
					//console.log(1)
					return false;
				}

				if(parseInt(v['entered_pigs_from']) == 0 && parseInt(v['entered_pigs_to']) == 0 && parseInt(v['dead']) != 0){
					error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs To + Poor + Dead</strong> per row.</p>";
					//console.log(2)
					return false;
				}

				if(parseInt(v['entered_pigs_from']) > parseInt(v['pigs_from'])){
					error += "<p>&#8226; The number of pigs for <strong>Pigs From</strong> should not be greater than it's available pigs per row.</p>";
					//console.log(3)
					return false;
				}

				if(parseInt(v['entered_pigs_from']) > parseInt(v['pigs_from']) && parseInt(v['entered_pigs_to']) > parseInt(v['pigs_from'])){
					error += "<p>&#8226; <strong>Pigs From</strong> is not matched <strong>Pigs To + Poor + Dead</strong> per individual row</p>";
					//console.log(4)
					return false;
				}

				if(parseInt(v['entered_pigs_from']) == 0 && parseInt(v['entered_pigs_to']) != 0){
					error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs to + Dead + Poor</strong> per row.</p>";
					//console.log(5)
					return false;
				}

				if(parseInt(v['entered_pigs_to']) > parseInt(v['entered_pigs_from'])){
					error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs to + Dead + Poor</strong> per row.</p>";
					//console.log(6)
					return false;
				}

				if(parseInt(v['entered_pigs_to']) > parseInt(v['pigs_from'])){
					error += "<p>&#8226; <strong>Pigs From</strong> is not matched <strong>Pigs To + Poor + Dead</strong> per individual row</p>";
					//console.log(7)
					return false;
				}

		});

		if(error != "") {

						var	alert = "<div class='alert alert-warning' role='alert' style='margin-left: 10px; margin-right: 10px;'>";
								alert += error;
								alert += "</div>";

						$(".transfer-alert-div-"+transfer_id).html(alert);
						return false;

		}

				group_from_pigs = group_from_pigs + pigs_from;
				group_to_pigs = group_to_pigs + pigs_to;
				group_to_dead_pigs = group_to_dead_pigs + dead;
				group_to_poor_pigs = group_to_poor_pigs + poor;
				group_final_count = pigs_to; //+ group_to_poor_pigs;//group_final_count + final_count_pigs;

				$.each(from_counter, function(bin_id_from,count){

						var bin_from_label = $(".bin_label_"+element_ids+transfer_id+bin_id_from).text();
						var number_of_pigs_from = parseInt($(".bin_pig_"+element_ids+transfer_id+bin_id_from).text());

						/*
						group_from_pigs = group_from_pigs + pigs_from;
						group_to_pigs = group_to_pigs + pigs_to;
						group_to_dead_pigs = group_to_dead_pigs + dead;
						group_to_poor_pigs = group_to_poor_pigs + poor;
						group_final_count = pigs_to; //+ group_to_poor_pigs;//group_final_count + final_count_pigs;
						*/

						var total_pigs_from = number_of_pigs_from - pigs_from;

						if(pigs_from < 0){
							//error += "<p>&#8226; The number of 'pigs from' transfer should not be greater than the available pigs for bin <strong>"+bin_from_label+"</strong>.</p>";
						}

						if(pigs_from < 0){
							//error += "<p>&#8226; The number of 'pigs from' transfer should not be greater than the available pigs for bin <strong>"+bin_from_label+"</strong>.</p>";
						}

						if(poor > pigs_from){
							//error += "<p>&#8226; The number of <strong>Poor</strong> pigs should not be greater than the <strong>Pigs From</strong>.</p>";
						}

						/*if(pigs_from == 0){
							error += "<p>&#8226; The number of 'pigs from' transfer should not be <strong>0</strong> for bin <strong>"+bin_from_label+"</strong>.</p>";
						}*/

						if(pigs_from == 0){
							error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs To + Poor + Dead</strong> per row.</p>";
							console.log(8)
							return false;
						}

						if(parseInt(pigs_to) == 0){
							//error += "<p>&#8226; total number of <strong>Pigs to/Poor</strong> should always matched the number of <strong>Final Count</strong> pigs</p>";
							error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs To + Poor + Dead</strong> per row.</p>";
							console.log(9)
							return false;
						}

						if(parseInt(pigs_to) > parseInt(pigs_from)){
							error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs To + Poor + Dead</strong> per row.</p>";
							console.log(10)
							return false;
						}

						//if(parseInt(pigs_to) > parseInt(number_of_pigs_from)){
						//	error += "<p>&#8226; Please enter the right matches of <strong>Pigs From</strong> and <strong>Pigs to/dead</strong> per row.</p>";
						//	console.log(11)
						//	return false;
						//}

				});



		if(error != "") {

						var	alert = "<div class='alert alert-warning' role='alert' style='margin-left: 10px; margin-right: 10px;'>";
								alert += error;
								alert += "</div>";

						$(".transfer-alert-div-"+transfer_id).html(alert);

		} else {

						total_pigs_to_transfer = group_to_pigs+group_to_dead_pigs+group_to_poor_pigs;

						if(total_pigs_to_transfer != group_from_pigs){
							error += "<p>&#8226; <strong>Pigs From</strong> should always matched <strong>Pigs To + Poor + Dead</strong> per individual row</p>";
						}
						console.log(shipped_pigs+" "+group_from_pigs);
						if(shipped_pigs != group_from_pigs){
							error += "<p>&#8226; total number of <strong>Pigs From</strong> should always matched the number of <strong>Shipped</strong> pigs</p>";
						}

						if(group_to_dead_pigs != dead_pigs){
							error += "<p>&#8226; The <strong>Dead</strong> pigs are not equal.</p>";
						}

						if(poor_pigs != group_to_poor_pigs){
							error += "<p>&#8226; The <strong>Poor</strong> pigs are not equal.</p>";
						}

						/*if(final_count != group_final_count){
							error += "<p>&#8226; The final count is not equal to the total shipped pigs.</p>";
						}*/

						if(final_count > group_from_pigs){
							error += "<p>&#8226; <strong>Final Count</strong> should not be greater than the total <strong>Pigs From</strong></p>";
						}



						if(final_count > shipped_pigs){
							error += "<p>&#8226; <strong>Final Count</strong> should not be greater than <strong>Shipped</strong></p>";
						}

						if(error != "") {

								var	alert = "<div class='alert alert-warning' role='alert' style='margin-left: 10px; margin-right: 10px;'>";
										alert += error;
										alert += "</div>";

								$(".transfer-alert-div-"+transfer_id).html(alert);

						} else {

								console.log("success");
								finalizeTransfer(animal_data,data,element_ids,group_name,transfer_id);

						}
		}
		//console.log(data);

		// per bin number of pigs validation
		// if 1 group from bin is selected on all group to bins
		// the number of pigs should be devided to the total number of pigs on group from bin

		// get the each total of bins from number of pigs


		// number of pigs should not be zero
		// bins from should not allowed same bins on finalize transfer
		// bins to should not allowed same bins on finalize transfer


		// console.log("finalize",data,element_ids,group_name,transfer_id);
		//
});

/*
*	arrayCounter()
*
*	count the same values of the array in js
*/
function arrayCounter(arr){
	var counts = {};

	for(var i = 0; i< arr.length; i++) {
			var num = arr[i];
			counts[num] = counts[num] ? counts[num]+1 : 1;
	}

	return counts;
}

$(".container").delegate('.btn-transfer-modal','click', function (e) {

	var element_ids = $(this).attr('element_ids');
	var transfer_id = $(this).attr('transfer_id');
	var group_from = $(this).attr('group_from');
	var group_to = $(this).attr('group_to');
	var transfer_type = $(this).attr('transfer_type');
	var farm_id_from = $(this).attr('farm_id_from');
	var farm_id_to = $(this).attr('farm_id_to');
	var transfer_info = $(".transfer-info-"+transfer_id).serializeArray();
	var error = "";

	$(".transfer-alert-div-"+transfer_id).html("");

	$.each(transfer_info, function(key,value){
		if(value['name'] != "Dead" && value['name'] != "Poor"){
			if(value['value'] == 0){
				error += "<p><strong>"+value['name']+"</strong> should not be "+value['value']+"</p>";
			}
		}
	});
	if(error != ""){
		alertTransfer(error,transfer_id,'edit-transfer');
		return false;
	} else {
		$(".alert-div-"+transfer_id).html("");
		fetchBins(element_ids,transfer_id,group_from,group_to,transfer_type,farm_id_from,farm_id_to);
	}

});

/*
*	loop for 1st load of animal groups data
*
*/
$.each(all_data, function(key,val){

	var element_ids = val['group_type']+val['group_id'];
	var group_name = val['group_name'];
	var total_pigs = val['total_pigs'];
	var available_for_transfer_pigs = parseInt($(".available_pigs-"+element_ids).val());// Number(val['total_pigs']) - Number(val['sched_pigs']);



	$(".container").delegate(".saveTransfer"+element_ids,"click", function(e){
		e.preventDefault();
		var group_id = $(this).attr("group_id");
		var group_type = $(this).attr("group_type");


		var transfer_data = {
			'group_id'			:		$(".group_from"+element_ids).val(),
			'group_from'		:		$(".group_from"+element_ids).val(),
			'group_to'			:		$(".group_to"+element_ids).val(),
			'group_type'		:		$(".group_type"+element_ids).val(),
			'driver_id'			:		$(".driver-"+element_ids).val(),
			'date'					:		$(".date"+element_ids).val(),
			'pigs'					:		$(".number_of_pigs"+element_ids).val(),
			'total_pigs'		: 	available_for_transfer_pigs,
			'current_pigs'	:		total_pigs
		};

		//console.log("save",transfer_data);
		//console.log("element_ids",element_ids);
		//console.log("group_name",group_name)
		saveTransfer(val,transfer_data,element_ids,group_name);
	});

	if(val['transfer_data'] != null){

		$.each(val['transfer_data'], function(k,v){
			/*
			$(".container").delegate('#btn-transfer-modal'+v['transfer_id'],'click', function (e) {
				fetchBins(element_ids,v['transfer_id'],v['group_from'],v['group_to'],v['transfer_type'],v['farm_id_from'],v['farm_id_to'])
			});

			$(".container").delegate(".saveEditedTransfer"+element_ids+v['transfer_id'],"click", function(e){
				var transfer_data = {
					'transfer_id'					: v['transfer_id'],
					'group_from' 					: $(".group_from"+element_ids+v['transfer_id']).val(),
					'group_to_previous'		: $(".group_to_previous"+element_ids+v['transfer_id']).val(),
					'group_to' 						: $("#group_to"+element_ids+v['transfer_id']).val(),
					'date' 								: $(".date"+element_ids+v['transfer_id']).val(),
					'empty_weight'				:	$(".empty_weight"+element_ids+v['transfer_id']).val(),
					'ave_weight'					:	$(".ave_weight"+element_ids+v['transfer_id']).val(),
					'driver_id'						:	$(".driver-"+element_ids+v['transfer_id']).val(),
					'full_weight'					:	$(".full_weight"+element_ids+v['transfer_id']).val(),
					'shipped'							:	$(".shipped"+element_ids+v['transfer_id']).val(),
					'received'						:	$(".received"+element_ids+v['transfer_id']).val(),
					'dead'								:	$(".dead"+element_ids+v['transfer_id']).val(),
					'poor'								:	$(".poor"+element_ids+v['transfer_id']).val(),
					'farm_count'					:	$(".farm_count"+element_ids+v['transfer_id']).val(),
					'final_count'					:	$(".final_count"+element_ids+v['transfer_id']).val(),
					'group_type'					:	$(this).attr("group_type")
				}

				var data = {
					'group_from'		:	$(".group_from"+element_ids+v['transfer_id']).val(),
					'group_to'			:	$("#group_to"+element_ids+v['transfer_id']).val(),
					'group_type'		:	$(this).attr("group_type"),
					'current_pigs'	:	total_pigs
				};

				updateTransfer(transfer_data,data,element_ids,group_name)
			})

			$(".container").delegate(".deleteTransfer"+element_ids+v['transfer_id'],"click", function(e){
				var id = $(this).attr("transfer_id");
				var data = {
					'group_from'		:	$(this).attr("group_from"),
					'group_to'			:	$(this).attr("group_to"),
					'group_type'		:	$(this).attr("group_type"),
					'current_pigs'	:	total_pigs
				};
				deleteTransfer(id,data,element_ids,group_name)
			})


			$(".container").delegate(".finalizeTransfer"+element_ids+v['transfer_id'],"click", function(e){
					var bins_to = $(".bins_to"+element_ids+v['transfer_id']).serializeArray();
					var bins_from = $(".bins_from"+element_ids+v['transfer_id']).serializeArray();
					var num_of_pigs = $(".num_of_pigs"+element_ids+v['transfer_id']).serializeArray();
					var data = {
						'transfer_data'	:	v,
						'bins_to'				:	bins_to,
						'bins_from'			:	bins_from,
						'num_of_pigs'		:	num_of_pigs
					};
					console.log("finalize",data,element_ids,group_name,v['transfer_id']);
					finalizeTransfer(animal_data,data,element_ids,group_name,v['transfer_id']);
			});
			*/
		});

	}

});

function hideTransfer(id){
	$.each(all_data, function(key,val){
		var ids = val['group_type']+val['group_id'];
		if(id != ids){
			$("#collapse"+ids).collapse('hide');
			$("#collapse"+id).collapse('show');
			$("#collapseme"+ids).collapse('hide');
			$("#collapseme"+id).collapse('show');
		}
	});
}

/*
*	saveTransfer()
*
*	Save the transfer created from animal group page
*/
function saveTransfer(animal_data,transfer_data,element_ids,group_name){
		var total_pigs = Number(transfer_data['pigs']) + Number(transfer_data['total_pigs']);
		var available_pigs = 0;
		if(isNaN($("#available_pigs-"+element_ids).val())){
			available_pigs = $(".saveTransfer"+element_ids).attr("available_pigs");
		} else {
			available_pigs = $("#available_pigs-"+element_ids).val();
		}
		var error = "";

		hideTransfer(element_ids);

		$(".transfer-alert-popup-"+element_ids).html("");

		if(transfer_data['group_to'] == "") {
			error += "<div class='alert alert-warning' role='alert'>";
  		error += "<p>Please select group to...</p>";
			error += "</div>";
		} else if(transfer_data['pigs'] == 0) {
			error += "<div class='alert alert-warning' role='alert'>";
  		error += "<p>Number of pigs should not be zero...</p>";
			error += "</div>";
		} else if(transfer_data['pigs'] > transfer_data['total_pigs']){
			error += "<div class='alert alert-warning' role='alert'>";
  		error += "<p>Number of pigs should not be greater than the available pigs...</p>";
			error += "</div>";
			console.log("number of pigs: " + transfer_data['pigs'] +" > "+ "total pigs: " + transfer_data['total_pigs']);
		} else if(transfer_data['pigs'] > parseInt(available_pigs)) {
			error += "<div class='alert alert-warning' role='alert'>";
  		error += "<p>Number of pigs should not be greater than the available pigs...</p>";
			error += "</div>";
			console.log("number of pigs: " + transfer_data['pigs'] +" > "+ "available pigs: " +  $("#available_pigs-"+element_ids).val())
		} else {
			error = "";
		}
		$(".transfer-alert-popup-"+element_ids).append(error);

		if(error == ""){
			$.ajax({
					url		:	app_url+"/saveTransfer",
					data	: transfer_data,
					type	:	"POST",
					success	: function(r){
						if(r=='success'){
							$("#transfer-modal"+element_ids).modal('hide');
							// update the html element on the front end

							// append to the created transfer element

							// show expanded view of the element
							$('.panel-'+element_ids).removeClass("panel-default").addClass("panel-info");
							$('.status-'+element_ids).removeClass("glyphicon-ban-circle").addClass("glyphicon-ok-circle");
							$('.status-'+element_ids).removeClass("text-danger").addClass("text-success");
							$('.inactive-'+element_ids).addClass('active-transfer');

							$("#collapse"+element_ids).collapse('show');

							// ajax function with a get function
							getCreatedTransfer(animal_data,transfer_data,element_ids,group_name);
						} else if(r=='transfer already created'){
							alert('Transfer already created');
						} else {
							alert('Something went wrong...');
						}

					}
				});
		}
}

/*
*	udpateTransfer()
*
*	Update the transfer created from animal group page
*/
function updateTransfer(animal_data,transfer_data,data,element_ids,group_name){
	console.log("animal_data",animal_data);
	console.log("transfer_data",transfer_data);
	console.log("data",data);
	console.log("element_ids",element_ids);
	console.log("group_name",group_name);

	$.ajax({
			url		:	app_url+"/updateTransfer",
			data	: transfer_data,
			type	:	"POST",
			success	: function(r){
				if(r == 'success'){
					getCreatedTransfer(animal_data,data,element_ids,group_name);
					$("#edit-modal"+transfer_data['transfer_id']).modal('hide');
				} else {
					alert("something went wrong");
				}
			}
	});

}

/*
*	udpateTransfer()
*
*	Update the transfer created from animal group page
*/
function deleteTransfer(animal_data,transfer_id,data,element_ids,group_name){
	$.ajax({
			url		:	app_url+"/deleteTransfer",
			data	: {'transfer_id':transfer_id,'data':data},
			type	:	"POST",
			success	: function(r){
					getCreatedTransfer(animal_data,data,element_ids,group_name);
					$("#delete-modal"+transfer_id).modal('hide');
			}
	});
}

/*
*	finalizeTransfer()
*
*	finalize the transfer created from animal group page
*/
function finalizeTransfer(animal_data,data,element_ids,group_name,transfer_id){
	var reloading = "<div class='loading-stick-circle loading'>";
			reloading += "<img src='/css/images/loader-stick.gif' />";
			reloading += "Please wait, Transfering Data...";
	$("#finalize-modal"+transfer_id).modal('hide');
	$(".table_holder").html("");
	$(".table_holder").html(reloading);
	$.ajax({
			url		:	app_url+"/finalizeTransfer",
			data	: data,
			type	:	"POST",
			success	: function(r){
					//getCreatedTransfer(animal_data,data,element_ids,group_name);
					location.reload();
			}
	});
}

/*
*	saveTransfer()
*
*	Save the transfer created from animal group page
*/
function getCreatedTransfer(animal_data,data,element_ids,group_name){
	$.ajax({
			url		:	app_url+"/fetchTransfer",
			data	: data,
			type	:	"GET",
			success	: function(r){
				// update the current pigs
				$('#current_pigs'+element_ids).html("");
				$('#current_pigs'+element_ids).html(r.current_pigs);

				$(".available_pigs-"+element_ids).val("");
				$(".available_pigs-"+element_ids).val(r.available_pigs);


				var bins_data = "";
				$.each(r.group_from_bins_data,function(k,v){

					bins_data += "<hr class='hr' style='margin-top: 0px; width:70%;'>";
					bins_data += "<dl class='dl-horizontal'>";
					bins_data += "<dt>Bin:</dt>";
					bins_data += "<dd>"+v.name+"</dd>";
					bins_data += "<dt>Pigs:</dt>";
					bins_data += "<dd>"+v.number_of_pigs+"</dd>";
					bins_data += "</dl>";

				});

				// update the bins data view
				$(".bins_data"+element_ids).html("");
				$(".bins_data"+element_ids).html(bins_data);



				$(".transfer_button_div"+element_ids).html("");

				if(r.transfer == 'none'){
							console.log("empty");

							$('.panel-'+element_ids).removeClass("panel-info").addClass("panel-default");
							$('.status-'+element_ids).removeClass("glyphicon-ok-circle").addClass("glyphicon-ban-circle");
							$('.status-'+element_ids).removeClass("text-success").addClass("text-danger");
							$(".panel-body"+element_ids).removeClass('active-transfer').addClass('inactive-transfer');

							var group_list = "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer btn-transfer-"+element_ids+" transfer-"+element_ids+"' data-toggle='modal' data-target='#transfer-modal"+element_ids+"' aria-label='Left Align'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>";
									group_list += "</button>";

							$(".transfer_button_div"+element_ids).append(group_list);

							$("#collapse"+element_ids).collapse('show');

							var no_transfer_lists = "<div class='panel-heading' role='tab' id='heading"+element_ids+"' style='height: 80px;'>";
									no_transfer_lists += "<h3 class='panel-title text-left text-primary'>No transfer yet for Group "+group_name+"</h3>";
									no_transfer_lists += "</div>";

							$("#with-transfer"+element_ids).html("");
							$("#with-transfer"+element_ids).removeClass('panel-info').addClass('panel-default');
							$("#with-transfer"+element_ids).append(no_transfer_lists);

							$(".inactive-"+element_ids).removeClass("active-transfer").addClass("inactive-transfer");

				} else {


					var transfer_list = "<div class='panel-heading' role='tab' id='heading"+element_ids+"' style='height: 80px;'>";
							transfer_list += "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapseme"+element_ids+"' aria-expanded='false' aria-controls='collapse"+element_ids+"' class='collapsed' style='text-decoration: none;' id='group_transfer"+element_ids+"'>";
							transfer_list += "<h3 class='panel-title text-left'><strong>Transfer for "+group_name+"</strong>";
							transfer_list += "</h3>";
							transfer_list += "<div><em>Scheduled pigs for transfer: "+r.sched_pig+"</em></div>";
							transfer_list += "<button type='button' class='btn btn-xs btn-info pull-right' aria-label='Left Align'>";
							transfer_list += "<span class='glyphicon glyphicon-resize-vertical' aria-hidden='true'></span>";
							transfer_list += "</button>";
							transfer_list += "<input type='hidden' id='available_pigs-"+element_ids+"' value='"+r.available_pigs+"'/>";
							transfer_list += "<div><em>Available pigs for transfer: "+r.available_pigs+"</em></div>";
							transfer_list += "</a>";
							transfer_list += "</div>";

							transfer_list += "<div id='collapseme"+element_ids+"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading"+element_ids+"'>";
							transfer_list += "<div class='panel-body active-transfer'>";

							$.each(r.transfer, function(k,v){
								if(v['status'] == 'finalized'){
									/*
										transfer_list += "<div class='row'>";
										transfer_list += "<div class='col-md-6'>";
										transfer_list += "<hr class='hr' style='margin-top: 0px;'>";
										transfer_list += "<dl class='dl-horizontal dl-horizontal-transfer'>";
										transfer_list += "<dt>Date:</dt>";
										transfer_list	+= "<dd>"+v['date']+"</dd>";
										transfer_list += "<dt>From:</dt>";
										transfer_list += "<dd>"+v['group_from_farm']+"</dd>";
										transfer_list += "<dt>To:</dt>";
										transfer_list += "<dd>"+v['group_to_farm']+"</dd>";
										transfer_list += "<dt>Empty Weight:</dt>";
										transfer_list += "<dd>"+v['empty_weight']+"</dd>";
										transfer_list += "<dt>Full Weight:</dt>";
										transfer_list += "<dd>"+v['full_weight']+"</dd>";
										transfer_list += "<dt>Ave Weight:</dt>";
										transfer_list += "<dd>"+v['ave_weight']+"</dd>";
										transfer_list += "<dt>Driver:</dt>";
										transfer_list += "<dd>"+driversName(v['driver_id'])+"</dd>";
										transfer_list += "</dl>";
										transfer_list += "</div>";
										transfer_list += "<div class='col-md-6'>";
										transfer_list += "<hr class='hr' style='margin-top: 0px;'>";
										transfer_list += "<dl class='dl-horizontal dl-horizontal-transfer'>";
										transfer_list += "<dt>Shipped:</dt>";
										transfer_list += "<dd>"+v['shipped']+"</dd>";
										transfer_list += "<dt>Received:</dt>";
										transfer_list += "<dd>"+v['received']+"</dd>";
										transfer_list += "<dt>Dead:</dt>";
										transfer_list += "<dd>"+v['dead']+"</dd>";
										transfer_list += "<dt>Poor:</dt>";
										transfer_list += "<dd>"+v['poor']+"</dd>";
										transfer_list += "<dt>Farm Count:</dt>";
										transfer_list += "<dd>"+v['farm_count']+"</dd>";
										transfer_list += "<dt>Final Count:</dt>";
										transfer_list += "<dd>"+v['final_count']+"</dd>";
										transfer_list += "</dl>";
										transfer_list += "<span class='btn btn-success pull-right btn-transfer' style='cursor:none;'>Finalized <span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span></span>";
										transfer_list += "</div>";
										transfer_list += "</div>";
										*/
								} else {

										transfer_list += "<div class='row'>";
										transfer_list += "<div class='alert-div-"+v['transfer_id']+" alert-transfer'></div>";
										transfer_list += "<div class='col-md-6'>";
										transfer_list += "<hr class='hr' style='margin-top: 0px;'>";
										transfer_list += "<dl class='dl-horizontal dl-horizontal-transfer'>";
										transfer_list += "<dt>Transfer #:</dt>";
										transfer_list += "<dd>"+v['transfer_number']+"</dd>";
										transfer_list += "<dt>Date:</dt>";
										transfer_list += "<dd>"+v['date']+"</dd>";
										transfer_list += "<dt>From:</dt>";
										transfer_list += "<dd>"+v['group_from_farm']+"</dd>";
										transfer_list += "<dt>To:</dt>";
										transfer_list += "<dd>"+v['group_to_farm']+"</dd>";
										transfer_list += "<dt>Empty Weight:</dt>";
										transfer_list += "<dd>"+v['empty_weight']+"</dd>";
										transfer_list += "<input type='hidden' name='Empty Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['empty_weight']+"'/>";
										transfer_list += "<dt>Full Weight:</dt>";
										transfer_list += "<dd>"+v['full_weight']+"</dd>";
										transfer_list += "<input type='hidden' name='Full Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['full_weight']+"'/>";
										transfer_list += "<dt>Ave Weight:</dt>";
										transfer_list += "<dd>"+v['ave_weight']+"</dd>";
										transfer_list += "<input type='hidden' name='Ave Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['ave_weight']+"'/>";
										transfer_list += "<dt>Driver:</dt>";
										transfer_list += "<dd>"+driversName(v['driver_id'])+"</dd>";
										transfer_list += "</dl>";
										transfer_list += "</div>";
										transfer_list += "<div class='col-md-6'>";
										transfer_list += "<hr class='hr' style='margin-top: 0px;'>";
										transfer_list += "<button type='button' style='margin-left: 5px;' class='btn btn-xs btn-danger pull-right' data-toggle='modal' data-target='#delete-modal"+v['transfer_id']+"' aria-label='Left Align'>";
										transfer_list += "<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
										transfer_list += "</button>";
										transfer_list += "<button type='button' class='btn btn-xs btn-warning pull-right' data-toggle='modal' data-target='#edit-modal"+v['transfer_id']+"' aria-label='Left Align'>";
										transfer_list += "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
										transfer_list += "</button>";
										transfer_list += "<dl class='dl-horizontal dl-horizontal-transfer'>";
										transfer_list += "<dt>Shipped:</dt>";
										transfer_list += "<dd>"+v['shipped']+"</dd>";
										transfer_list += "<input type='hidden' name='Shipped' class='transfer-info-"+v['transfer_id']+"' value='"+v['shipped']+"'/>";
										transfer_list += "<dt>Received:</dt>";
										transfer_list += "<dd>"+v['received']+"</dd>";
										transfer_list += "<input type='hidden' name='Received' class='transfer-info-"+v['transfer_id']+"' value='"+v['received']+"'/>";
										transfer_list += "<dt>Dead:</dt>";
										transfer_list += "<dd>"+v['dead']+"</dd>";
										transfer_list += "<input type='hidden' name='Dead' class='transfer-info-"+v['transfer_id']+"' value='"+v['dead']+"'/>";
										transfer_list += "<dt>Poor:</dt>";
										transfer_list += "<dd>"+v['poor']+"</dd>";
										transfer_list += "<input type='hidden' name='Poor' class='transfer-info-"+v['transfer_id']+"' value='"+v['poor']+"'/>";
										transfer_list += "<dt>Farm Count:</dt>";
										transfer_list += "<dd>"+v['farm_count']+"</dd>";
										transfer_list += "<input type='hidden' name='Farm Count' class='transfer-info-"+v['transfer_id']+"' value='"+v['farm_count']+"'/>";
										transfer_list += "<dt>Final Count:</dt>";
										transfer_list += "<dd>"+v['final_count']+"</dd>";
										transfer_list += "<input type='hidden' name='Final Count' class='transfer-info-"+v['transfer_id']+"' value='"+v['final_count']+"'/>";
										transfer_list += "</dl>";
										transfer_list += "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer btn-transfer-modal' ";
										transfer_list += "element_ids='"+element_ids+"' ";
										transfer_list += "id='btn-transfer-modal"+v['transfer_id']+"' ";
										transfer_list += "transfer_id='"+v['transfer_id']+"' ";
										transfer_list += "group_from='"+v['group_from']+"' ";
										transfer_list += "group_to='"+v['group_to']+"' ";
										transfer_list += "transfer_type='"+v['transfer_type']+"' ";
										transfer_list += "farm_id_from='"+v['farm_id_from']+"' ";
										transfer_list += "farm_id_to='"+v['farm_id_to']+"' ";
										transfer_list += "data-toggle='modal' data-target='#finalize-modal"+v['transfer_id']+"' aria-label='Left Align'>Finalize Transfer <span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span></button>";
										transfer_list += "</div>";
										transfer_list += "</div>";

										transfer_list += deleteTransferModal(element_ids,v,data);
										transfer_list += finalizeTransferModal(element_ids,v,data);

										if(data['group_type'] == 'farrowing'){
												transfer_list += editTransferModal(animal_data,v,nursery_groups,element_ids);
										} else if(data['group_type'] == 'nursery') {
												transfer_list += editTransferModal(animal_data,v,finisher_groups,element_ids);
										} else {
												transfer_list += editTransferModal(animal_data,v,"",element_ids);
										}

								}

							});


							transfer_list += "</div>";
							transfer_list += "</div>";



							$("#with-transfer"+element_ids).html("");
							$("#with-transfer"+element_ids).append(transfer_list);

							//$("#collapseme"+element_ids).collapse('show');

							$("#no-transfer"+element_ids).html("");
							$("#no-transfer"+element_ids).append(transfer_list);
							$('#no-transfer'+element_ids).removeClass("panel-default").addClass("panel-info");

							$('#no-transfer'+element_ids).attr('id',"with-transfer"+element_ids);

							$(".panel-body"+element_ids).removeClass("inactive-transfer").addClass("active-transfer");

							$('#with-transfer'+element_ids).removeClass("panel-default").addClass("panel-info");

							$("#collapseme"+element_ids).collapse('show');


							setTimeout(function(){
								if($("#available_pigs-"+element_ids).val() == 0){
									$('.btn-transfer-'+element_ids).hide();
								}
								if(Number(data['current_pigs']) == r.sched_pig || r.sched_pig > Number(data['current_pigs']) || r.available_pigs == 0){
										$('.btn-transfer-'+element_ids).hide();
								}else {
									$(".btn-transfer-"+element_ids).hide();
									var group_list = "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer btn-transfer-"+element_ids+" transfer-"+element_ids+"' data-toggle='modal' data-target='#transfer-modal"+element_ids+"' aria-label='Left Align'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>";
											group_list += "</button>";
									$(".transfer_button_div"+element_ids).html("");
									$(".transfer_button_div"+element_ids).append(group_list);
									//$("#heading"+element_ids).prepend(group_list);
								}
							},1000);
				}

			}
	});

}




function transferModal(animal_data,groups){
	var disabled = "disabled";

	var available_pigs_transfer_modal = parseInt(animal_data['total_pigs']) - parseInt(animal_data['sched_pigs']);
	var transfer_modal = "<div class='modal fade' id='transfer-modal"+animal_data['group_type']+animal_data['group_id']+"' tabindex='-1' data-backdrop='static' role='dialog' aria-labelledby='myModalLabel'>";
			transfer_modal += "<div class='modal-dialog' role='document'>";
			transfer_modal += "<div class='modal-content'>";
			transfer_modal += "<div class='modal-header'>";
			transfer_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
			transfer_modal += "<h4 class='modal-title' id='myModalLabel'>Create Transfer for "+animal_data['group_name']+"</h4>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='modal-body form-horizontal'>";
			transfer_modal += "<div class='transfer-alert-popup-"+animal_data['group_type']+animal_data['group_id']+"'></div>";
			transfer_modal += "<div class='form-group'>";

			transfer_modal += "<div class='row'>";
			transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			transfer_modal += "<p class='text-primary'>Group From</p>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='col-md-5'>";
			transfer_modal += "<input type='text' class='form-control' disabled value='"+animal_data['group_name']+"' />";
			transfer_modal += "<input type='hidden' class='form-control group_from"+animal_data['group_type']+animal_data['group_id']+"' value='"+animal_data['group_id']+"' />";
			transfer_modal += "<input type='hidden' class='form-control group_type"+animal_data['group_type']+animal_data['group_id']+"' value='"+animal_data['group_type']+"' />";
			transfer_modal += "</div>";
			transfer_modal += "</div>";

			transfer_modal += "<div class='row'>";
			transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			transfer_modal += "<p class='text-primary'>Group To</p>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='col-md-5'>";

			if(animal_data['group_type'] == "finisher" && groups == "") {
				transfer_modal += "<input type='text' class='form-control' disabled value='Market' />";
				transfer_modal += "<input type='hidden' class='form-control group_to"+animal_data['group_type']+animal_data['group_id']+"' disabled value='0' />";
				disabled = "";
			} else if(animal_data['group_type'] == "nursery" && groups == "") {
				transfer_modal += "<input type='text' class='form-control' disabled value='No Finisher Groups' />";
				transfer_modal += "<input type='hidden' class='form-control group_to"+animal_data['group_type']+animal_data['group_id']+"' disabled value='0' />";
			} else {
				transfer_modal += "<select class='form-control group_to"+animal_data['group_type']+animal_data['group_id']+"'>";
				transfer_modal += "<option value=''>Please Select...</option>";
				$.each(groups, function(k,v){
				transfer_modal += "<option value='"+v['group_id']+"'>"+v['group_name']+"</option>";
				});
				transfer_modal += "</select>";
				disabled = "";
			}

			transfer_modal += "</div>";
			transfer_modal += "</div>";

			transfer_modal += "<div class='row'>";
			transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			transfer_modal += "<p class='text-primary'>Date</p>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='col-md-5'>";
			transfer_modal += "<input type='text' class='form-control datepickerSchedTool date"+animal_data['group_type']+animal_data['group_id']+"' value='{{date("M d, Y")}}'  />";
			transfer_modal += "</div>";
			transfer_modal += "</div>";

			transfer_modal += "<div class='row'>";
			transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			transfer_modal += "<p class='text-primary'>Number of Pigs</p>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='col-md-5'>";
			transfer_modal += "<input type='number' value='0' class='form-control number_of_pigs"+animal_data['group_type']+animal_data['group_id']+"' value='' />";
			transfer_modal += "</div>";
			transfer_modal += "</div>";

			transfer_modal += "<div class='row'>";
			transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			transfer_modal += "<p class='text-primary'>Driver</p>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='col-md-5'>";
			transfer_modal += "<select class='form-control driver-"+animal_data['group_type']+animal_data['group_id']+"'>";
			$.each(all_drivers, function(key,val){
				transfer_modal += "<option value='"+val['id']+"'>"+val['username']+"</option>";
			});
			transfer_modal += "</select>";
			transfer_modal += "</div>";
			transfer_modal += "</div>";

			transfer_modal += "</div>";
			transfer_modal += "</div>";
			transfer_modal += "<div class='modal-footer'>";
			transfer_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
			transfer_modal += "<button type='button' class='btn btn-primary saveTransfer"+animal_data['group_type']+animal_data['group_id']+"' group_type='"+animal_data['group_type']+"' group_id='"+animal_data['group_id']+"' group_type='"+animal_data['group_type']+"' available_pigs='"+animal_data['total_pigs']+"' "+disabled+">Save changes</button>";
			transfer_modal += "<input type='hidden' class='available_pigs-"+animal_data['group_type']+animal_data['group_id']+"' value='"+available_pigs_transfer_modal+"' />";
			transfer_modal += "</div>";
			transfer_modal += "</div>";
			transfer_modal += "</div>";
			transfer_modal += "</div>";

			return transfer_modal;
}



function editTransferModal(animal_data,transfer_data,groups,element_ids){

	var edit_transfer_modal = "<div class='modal fade' id='edit-modal"+transfer_data['transfer_id']+"' tabindex='-1' data-backdrop='static' role='dialog' aria-labelledby='myModalLabel'>";
			edit_transfer_modal += "<div class='modal-dialog' role='document'>";
			edit_transfer_modal += "<div class='modal-content'>";
			edit_transfer_modal += "<div class='modal-header'>";
			edit_transfer_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
			edit_transfer_modal += "<h4 class='modal-title' id='myModalLabel' style='color:#000000;'>Edit Transfer for "+animal_data['group_name']+"</h4>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='modal-body form-horizontal'>";
			edit_transfer_modal += "<div class='form-group'>";

			edit_transfer_modal += "<div class='row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Group From</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='text' class='form-control' disabled value='"+transfer_data['group_name_from']+"' />";
			//edit_transfer_modal += "<input type='hidden' class='form-control group_from"+element_ids+transfer_data['transfer_id']+"' value='"+animal_data['group_id']+"' />";
			edit_transfer_modal += "<input type='hidden' class='form-control group_type"+element_ids+transfer_data['transfer_id']+"' value='"+animal_data['group_type']+"' />";
			edit_transfer_modal += "<input type='hidden' class='form-control group_to_previous"+element_ids+transfer_data['transfer_id']+"' value='"+transfer_data['group_to']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Group To</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";

			if(groups == "") {
				edit_transfer_modal += "<input type='text' class='form-control' disabled value='Market' />";
				edit_transfer_modal += "<input type='hidden' class='form-control group_to"+element_ids+transfer_data['transfer_id']+"' disabled value='0' />";
			} else {
				edit_transfer_modal += "<select class='form-control' id='group_to"+element_ids+transfer_data['transfer_id']+"'>";
				$.each(groups, function(k,v){
					if(v['group_id'] == transfer_data['group_to']){
						edit_transfer_modal += "<option value='"+v['group_id']+"' selected>"+v['group_name']+"</option>";
					} else {
						edit_transfer_modal += "<option value='"+v['group_id']+"'>"+v['group_name']+"</option>";
					}
				});
				edit_transfer_modal += "</select>";
			}

			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Date</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='text' class='form-control datepickerSchedTool date"+element_ids+transfer_data['transfer_id']+"' value='"+transfer_data['date']+"'  />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Empty Weight</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['empty_weight']+"' class='form-control empty_weight"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Full Weight</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['full_weight']+"' class='form-control full_weight"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Ave Weight</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['ave_weight']+"' class='form-control ave_weight"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Driver</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";

			edit_transfer_modal += "<select class='form-control driver-"+element_ids+transfer_data['transfer_id']+"'>";
			$.each(all_drivers,function(k,v){
				if(transfer_data['driver_id'] == v['id']){
					edit_transfer_modal += "<option value='"+v['id']+"' selected>"+v['username']+"</option>";
				} else {
					edit_transfer_modal += "<option value='"+v['id']+"'>"+v['username']+"</option>";
				}
			});
			edit_transfer_modal += "</select>";

			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Shipped</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['shipped']+"' class='form-control shipped"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Received</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['received']+"' class='form-control received"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Dead</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['dead']+"' class='form-control dead"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Poor</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['poor']+"' class='form-control poor"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Farm Count</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['farm_count']+"' class='form-control farm_count"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Final Count</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<input type='number' value='"+transfer_data['final_count']+"' class='form-control final_count"+element_ids+transfer_data['transfer_id']+"' />";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			edit_transfer_modal += "<div class='col-md-offset-2 col-md-3'>";
			edit_transfer_modal += "<p class='text-primary'>Notes</p>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='col-md-5'>";
			edit_transfer_modal += "<textarea class='form-control notes-"+element_ids+transfer_data['transfer_id']+"' >"+transfer_data['notes']+"</textarea>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "<div class='modal-footer'>";
			edit_transfer_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
			edit_transfer_modal += "<button type='button' class='btn btn-primary saveEditedTransfer saveEditedTransfer"+element_ids+transfer_data['transfer_id']+"' ";
			edit_transfer_modal += "total_pigs='"+animal_data['total_pigs']+"' ";
			edit_transfer_modal += "element_ids='"+element_ids+"' ";
			edit_transfer_modal += "transfer_id='"+transfer_data['transfer_id']+"' ";
			edit_transfer_modal += "group_name='"+animal_data['group_name']+"' ";
			edit_transfer_modal += "group_id='"+animal_data['group_id']+"' ";
			edit_transfer_modal += "group_type='"+animal_data['group_type']+"'>Save changes</button>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";
			edit_transfer_modal += "</div>";

			return edit_transfer_modal;



}


function deleteTransferModal(element_ids,transfer_data,animal_data){
	var delete_transfer_modal = "<div class='modal fade' id='delete-modal"+transfer_data['transfer_id']+"' tabindex='-1' data-backdrop='static' role='dialog' aria-labelledby='myModalLabel'>";
			delete_transfer_modal += "<div class='modal-dialog' role='document'>";
			delete_transfer_modal += "<div class='modal-content'>";
			delete_transfer_modal += "<div class='modal-header'>";
			delete_transfer_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
			delete_transfer_modal += "<h4 class='modal-title' id='myModalLabel' style='color:#000000;'></h4>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "<div class='modal-body form-horizontal'>";
			delete_transfer_modal += "<div class='form-group'>";

			delete_transfer_modal += "<div class='row'>";
			delete_transfer_modal += "<div class='col-md-offset-3 col-md-8'>";
			delete_transfer_modal += "<p class='text-primary'>Are you sure you want to delete this transfer?</p>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "</div>";

			delete_transfer_modal += "</div>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "<div class='modal-footer'>";
			delete_transfer_modal += "<button type='button' class='btn btn-danger deleteTransfer deleteTransfer"+element_ids+transfer_data['transfer_id']+"' ";
			delete_transfer_modal += "transfer_id='"+transfer_data['transfer_id']+"' ";
			delete_transfer_modal += "group_name='"+transfer_data['group_name_from']+"' ";
			delete_transfer_modal += "total_pigs='"+animal_data['total_pigs']+"' ";
			delete_transfer_modal += "group_id='"+transfer_data['group_from']+"' ";
			delete_transfer_modal += "group_from='"+transfer_data['group_from']+"' ";
			delete_transfer_modal += "group_to_previous='"+transfer_data['group_to_previous']+"' ";
			delete_transfer_modal += "group_to='"+transfer_data['group_to']+"' ";
			delete_transfer_modal += "date='"+transfer_data['date']+"' ";
			delete_transfer_modal += "empty_weight='"+transfer_data['empty_weight']+"' ";
			delete_transfer_modal += "ave_weight='"+transfer_data['ave_weight']+"' ";
			delete_transfer_modal += "driver_id='"+transfer_data['driver_id']+"' ";
			delete_transfer_modal += "full_weight='"+transfer_data['full_weight']+"' ";
			delete_transfer_modal += "shipped='"+transfer_data['shipped']+"' ";
			delete_transfer_modal += "received='"+transfer_data['received']+"' ";
			delete_transfer_modal += "dead='"+transfer_data['dead']+"' ";
			delete_transfer_modal += "poor='"+transfer_data['poor']+"' ";
			delete_transfer_modal += "farm_count='"+transfer_data['farm_count']+"' ";
			delete_transfer_modal += "final_count='"+transfer_data['final_count']+"' ";
			delete_transfer_modal += "element_ids='"+element_ids+"' ";
			delete_transfer_modal += "group_type='"+animal_data['group_type']+"' ";
			delete_transfer_modal += ">Delete</button>";
			delete_transfer_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "</div>";
			delete_transfer_modal += "</div>";

			return delete_transfer_modal;

}

function finalizeTransferModal(element_ids,transfer_data,animal_data){

	var finalize_transfer_modal = "<div class='modal fade' id='finalize-modal"+transfer_data['transfer_id']+"' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>";
			finalize_transfer_modal += "<div class='modal-dialog modal-lg' role='document'>";
			finalize_transfer_modal += "<div class='modal-content'>";
			finalize_transfer_modal += "<div class='modal-header'>";
			finalize_transfer_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
			finalize_transfer_modal += "<h4 class='modal-title text-center' id='myModalLabel' style='color:#000000;'>Finalize Transfer</h4>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='modal-body form-horizontal active-transfer'>";
			finalize_transfer_modal += "<div class='alert-div-finalize-"+transfer_data['transfer_id']+" alert-transfer'></div>";
			finalize_transfer_modal += "<div class='form-group'>";
			finalize_transfer_modal += "<div class='transfer-alert-div-"+transfer_data['transfer_id']+"'></div>";
			//<div class="alert alert-warning" role="alert" style="margin-left: 10px; margin-right: 10px;">
  		//<p>...</p>
			//</div>

			finalize_transfer_modal += "<div class='col-md-4'>";

			finalize_transfer_modal += "<div class='row'><div class='col-md-12'><h4 class='text-center'>FROM</h4></div></div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-5'>";
			finalize_transfer_modal += "<p class=''>Group</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-6'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['group_name_from']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-5'>";
			finalize_transfer_modal += "<p class=''>Farm</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-6'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['group_from_farm']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-5'>";
			finalize_transfer_modal += "<p class=''>Shipped</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-6'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['shipped']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			//fetchBinsFrom(element_ids,transfer_data['transfer_id'],transfer_data['group_from'],transfer_data['transfer_type'],transfer_data['farm_id_from']);
			finalize_transfer_modal += "<div class='transfer_bins_from"+element_ids+transfer_data['transfer_id']+"'></div>";

			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<div class='row'><div class='col-md-12'><h4 class='text-center'>TO</h4></div></div>";
			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-3'>";
			finalize_transfer_modal += "<p class=''>Group</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['group_name_to']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-3'>";
			finalize_transfer_modal += "<p class=''>Farm</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['group_to_farm']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-3'>";
			finalize_transfer_modal += "<p class=''>Dead</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['dead']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-3'>";
			finalize_transfer_modal += "<p class=''>Poor</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['poor']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";
			finalize_transfer_modal += "<div class='col-md-offset-1 col-md-3'>";
			finalize_transfer_modal += "<p class=''>Final Count</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "<div class='col-md-8'>";
			finalize_transfer_modal += "<p class=''>"+transfer_data['final_count']+"</p>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";

			//fetchBins(element_ids,transfer_data['transfer_id'],transfer_data['group_to'],transfer_data['transfer_type'],transfer_data['farm_id_to']);
			finalize_transfer_modal += "<div class='transfer_bins"+element_ids+transfer_data['transfer_id']+"'></div>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";


			finalize_transfer_modal += "<div class='modal-footer'>";
			finalize_transfer_modal += "<button type='button' class='btn btn-success finalizeTransfer finalizeTransfer"+element_ids+transfer_data['transfer_id']+"' ";
			finalize_transfer_modal += "transfer_id='"+transfer_data['transfer_id']+"' ";
			finalize_transfer_modal += "group_name='"+transfer_data['group_name_from']+"' ";
			finalize_transfer_modal += "total_pigs='"+animal_data['total_pigs']+"' ";
			finalize_transfer_modal += "group_id='"+transfer_data['group_from']+"' ";
			finalize_transfer_modal += "group_from='"+transfer_data['group_from']+"' ";
			finalize_transfer_modal += "group_to_previous='"+transfer_data['group_to_previous']+"' ";
			finalize_transfer_modal += "group_to='"+transfer_data['group_to']+"' ";
			finalize_transfer_modal += "date='"+transfer_data['date']+"' ";
			finalize_transfer_modal += "empty_weight='"+transfer_data['empty_weight']+"' ";
			finalize_transfer_modal += "ave_weight='"+transfer_data['ave_weight']+"' ";
			finalize_transfer_modal += "driver_id='"+transfer_data['driver_id']+"' ";
			finalize_transfer_modal += "full_weight='"+transfer_data['full_weight']+"' ";
			finalize_transfer_modal += "shipped='"+transfer_data['shipped']+"' ";
			finalize_transfer_modal += "received='"+transfer_data['received']+"' ";
			finalize_transfer_modal += "dead='"+transfer_data['dead']+"' ";
			finalize_transfer_modal += "poor='"+transfer_data['poor']+"' ";
			finalize_transfer_modal += "poor='"+transfer_data['notes']+"' ";
			finalize_transfer_modal += "farm_count='"+transfer_data['farm_count']+"' ";
			finalize_transfer_modal += "final_count='"+transfer_data['final_count']+"' ";
			finalize_transfer_modal += "element_ids='"+element_ids+"' ";
			finalize_transfer_modal += "group_type='"+animal_data['group_type']+"' ";
			finalize_transfer_modal += "transfer_type='"+transfer_data['transfer_type']+"' ";
			/*
			finalize_transfer_modal += "date_created='"+animal_data['date_created']+"'";
			finalize_transfer_modal += "date_to_transfer='"+animal_data['date_to_transfer']+"'";
			finalize_transfer_modal += "date_transfered='"+animal_data['date_transfered']+"'";
			finalize_transfer_modal += "farm_id='"+animal_data['farm_id']+"'";
			finalize_transfer_modal += "farm_name='"+animal_data['farm_name']+"'";
			finalize_transfer_modal += "group_id='"+animal_data['group_id']+"'";
			finalize_transfer_modal += "group_name='"+animal_data['group_name']+"'";
			finalize_transfer_modal += "group_type='"+animal_data['group_type']+"'";
			finalize_transfer_modal += "sched_pigs='"+animal_data['sched_pigs']+"'";
			finalize_transfer_modal += "total_pigs='"+animal_data['total_pigs']+"'";
			*/
			finalize_transfer_modal += ">Transfer</button>";
			finalize_transfer_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";
			finalize_transfer_modal += "</div>";



			return finalize_transfer_modal;

}

function fetchBins(element_ids,transfer_id,group_id_from,group_id_to,transfer_type,farm_id_from,farm_id_to){

	$(".transfer_bins_from"+element_ids+transfer_id).html("");
	$(".transfer_bins_from"+element_ids+transfer_id).append(loading_transfer);

	$.ajax({
		url		:	app_url+"/fetchfarmbinstransfer",
		data	: {'group_id_from':group_id_from,'group_id_to':group_id_to,'transfer_type':transfer_type,'farm_id_from':farm_id_from,'farm_id_to':farm_id_to},
		type	:	"GET",
		success	: function(r){



					var finalize_transfer_modal_from = "<br/><div class='row'><div class='col-md-offset-1 col-md-5'><p class=''>Bin/s:</p></div>";
							finalize_transfer_modal_from += "<div class='col-md-6'><p class=''>Pigs:</p></div></div>";

					$.each(r['from'], function(k,v){

							finalize_transfer_modal_from += "<div class='row edit-transfer-modal-row'>";
							finalize_transfer_modal_from += "<div class='col-md-offset-1 col-md-5'>";
							finalize_transfer_modal_from += "<p class='bin_label_"+element_ids+transfer_id+v['bin_id']+"'>"+v['bin_label']+"</p>";
							finalize_transfer_modal_from += "</div>";
							finalize_transfer_modal_from += "<div class='col-md-6'>";
							finalize_transfer_modal_from += "<p class='bin_pig_"+element_ids+transfer_id+v['bin_id']+"'>"+v['number_of_pigs']+"</p>";
							finalize_transfer_modal_from += "</div>";
							finalize_transfer_modal_from += "</div>";

					});


					$(".transfer_bins_from"+element_ids+transfer_id).html("");
					$(".transfer_bins_from"+element_ids+transfer_id).append(finalize_transfer_modal_from);

					var finalize_transfer_modal = "<br/><div class='row'><div class='col-md-2'><p class=''>Bin/s From:</p></div>";
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Pigs From:</p></div>";
							if(transfer_type != 'finisher_to_market'){
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Bin/s To:</p></div>";
							} else {
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Market:</p></div>";
							}
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Pigs To:</p></div>";
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Dead:</p></div>";
							finalize_transfer_modal += "<div class='col-md-2'><p class=''>Poor:</p></div>";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='row edit-transfer-modal-row'>";

							finalize_transfer_modal += "<div class='col-md-2 bins_from_div"+element_ids+transfer_id+"' >";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='col-md-2 bins_from_div_pigs"+element_ids+transfer_id+"' >";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='col-md-2 bins_to_div"+element_ids+transfer_id+"'>";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='col-md-2 num_of_pigs_div"+element_ids+transfer_id+"'>";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='col-md-2 num_of_pigs_dead_div"+element_ids+transfer_id+"'>";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "<div class='col-md-2 num_of_pigs_poor_div"+element_ids+transfer_id+"'>";
							finalize_transfer_modal += "</div>";

							finalize_transfer_modal += "</div>";


					$(".transfer_bins"+element_ids+transfer_id).html("");
					$(".transfer_bins"+element_ids+transfer_id).append(finalize_transfer_modal);

					var group_from_select = "";
					var group_from_input = "";
					var group_to_bins = "";
					var num_of_pigs = "";
					var num_of_dead_pigs = "";
					var num_of_poor_pigs = "";

					if(transfer_type != 'finisher_to_market'){

								//if(from is greater than to)
								if(r['from'].length > r['to'].length || r['from'].length == r['to'].length){

									$.each(r['from'], function(key,val){

										//if(val['number_of_pigs'] != 0){

												group_from_select += "<select name='"+r['from'][0]['unique_id']+"' class='form-control bins_from"+element_ids+transfer_id+"' style='margin-top:5px;'>";
													//$.each(r['from'], function(k,v){
														//if(v['number_of_pigs'] != 0){
															group_from_select += "<option value='"+val['bin_id']+"'>"+val['bin_label']+"</option>";
														//}
													//});
												group_from_select += "</select>";

												group_from_input += "<input name='num_of_pigs_from' type='number' min='0' class='form-control num_of_pigs_from"+element_ids+transfer_id+"' value='0' style='margin-top:5px;' />";

												group_to_bins += "<select name='group_to_bins"+element_ids+transfer_id+"' class='form-control bins_to"+element_ids+transfer_id+"' style='margin-top:5px;'>";
												$.each(r['to'], function(k,v){
													//group_to_bins += "<input name='group_to_bins"+element_ids+transfer_id+"' type='hidden' class='form-control bins_to"+element_ids+transfer_id+"' value='"+v['bin_id']+"' />";
													//group_to_bins += "<input type='text' disabled class='form-control' value='"+v['bin_label']+"' style='margin-top:5px;' />";
													group_to_bins += "<option value='"+v['bin_id']+"'>"+v['bin_label']+"</option>";
												});
												group_to_bins += "</select>";

												num_of_pigs += "<input name='num_of_pigs"+element_ids+transfer_id+"' type='number' min='0' value='0' class='form-control num_of_pigs"+element_ids+transfer_id+"' style='margin-top:5px;' />";
												num_of_dead_pigs += "<input name='num_of_pigs_dead' type='number' min='0' value='0' class='form-control num_of_pigs_dead"+element_ids+transfer_id+"' style='margin-top:5px;' />";
												num_of_poor_pigs += "<input name='num_of_pigs_poor' type='number' min='0' value='0' class='form-control num_of_pigs_poor"+element_ids+transfer_id+"' style='margin-top:5px;' />";

										//}

									});

								} else {

									$.each(r['to'], function(k,v){

												group_from_select += "<select name='"+r['from'][0]['unique_id']+"' class='form-control bins_from"+element_ids+transfer_id+"' style='margin-top:5px;'>";
													$.each(r['from'], function(k,v){
														if(v['number_of_pigs'] != 0){
															group_from_select += "<option value='"+v['bin_id']+"'>"+v['bin_label']+"</option>";
														}
													});
												group_from_select += "</select>";

												group_from_input += "<input name='num_of_pigs_from' type='number' min='0' class='form-control num_of_pigs_from"+element_ids+transfer_id+"' value='0' style='margin-top:5px;' />";

												//group_to_bins += "<input name='group_to_bins"+element_ids+transfer_id+"' type='hidden' class='form-control bins_to"+element_ids+transfer_id+"' value='"+v['bin_id']+"' />";
												//group_to_bins += "<input type='text' disabled class='form-control' value='"+v['bin_label']+"' style='margin-top:5px;' />";
												group_to_bins += "<select name='group_to_bins"+element_ids+transfer_id+"' class='form-control bins_to"+element_ids+transfer_id+"' style='margin-top:5px;'>";
												$.each(r['to'], function(k,v){
													group_to_bins += "<option value='"+v['bin_id']+"'>"+v['bin_label']+"</option>";
												});
												group_to_bins += "</select>";

												num_of_pigs += "<input name='num_of_pigs"+element_ids+transfer_id+"' type='number' min='0' value='0' class='form-control num_of_pigs"+element_ids+transfer_id+"' style='margin-top:5px;' />";
												num_of_dead_pigs += "<input name='num_of_pigs_dead' type='number' min='0' value='0' class='form-control num_of_pigs_dead"+element_ids+transfer_id+"' style='margin-top:5px;' />";
												num_of_poor_pigs += "<input name='num_of_pigs_poor' type='number' min='0' value='0' class='form-control num_of_pigs_poor"+element_ids+transfer_id+"' style='margin-top:5px;' />";

									});

								}

					 } else {


									 $.each(r['from'], function(key,val){

											group_from_select += "<select name='"+r['from'][0]['unique_id']+"' class='form-control bins_from"+element_ids+transfer_id+"' style='margin-top:5px;'>";
												 $.each(r['from'], function(k,v){
														 group_from_select += "<option value='"+v['bin_id']+"'>"+v['bin_label']+"</option>";
												 });
											group_from_select += "</select>";

										 	group_from_input += "<input name='num_of_pigs_from' type='number' min='0' class='form-control num_of_pigs_from"+element_ids+transfer_id+"' value='0' style='margin-top:5px;' />";

											group_to_bins += "<input name='group_to_bins"+element_ids+transfer_id+"' type='hidden' class='form-control bins_to"+element_ids+transfer_id+"' value='market' />";
											group_to_bins += "<input type='text' disabled class='form-control' value='market' style='margin-top:5px;' />";

											num_of_pigs += "<input name='num_of_pigs"+element_ids+transfer_id+"' type='number' min='0' value='0' class='form-control num_of_pigs"+element_ids+transfer_id+"' style='margin-top:5px;' />";
											num_of_dead_pigs += "<input name='num_of_pigs_dead' type='number' min='0' value='0' class='form-control num_of_pigs_dead"+element_ids+transfer_id+"' style='margin-top:5px;' />";
											num_of_poor_pigs += "<input name='num_of_pigs_poor' type='number' min='0' value='0' class='form-control num_of_pigs_poor"+element_ids+transfer_id+"' style='margin-top:5px;' />";

									 });


					 }

					 setTimeout(function(){
						 $(".bins_from_div"+element_ids+transfer_id).html("");
						 $(".bins_from_div"+element_ids+transfer_id).append(group_from_select);

						 $(".bins_from_div_pigs"+element_ids+transfer_id).html("");
						 $(".bins_from_div_pigs"+element_ids+transfer_id).append(group_from_input);

						 $(".bins_to_div"+element_ids+transfer_id).html("");
						 $(".bins_to_div"+element_ids+transfer_id).append(group_to_bins);

						 $(".num_of_pigs_div"+element_ids+transfer_id).html("");
						 $(".num_of_pigs_div"+element_ids+transfer_id).append(num_of_pigs);

						 $(".num_of_pigs_dead_div"+element_ids+transfer_id).html("");
						 $(".num_of_pigs_dead_div"+element_ids+transfer_id).append(num_of_dead_pigs);

						 $(".num_of_pigs_poor_div"+element_ids+transfer_id).html("");
						 $(".num_of_pigs_poor_div"+element_ids+transfer_id).append(num_of_poor_pigs);

					 },500);

		}
	});
}


function driversName(driver_id){
	var driver = "none";
	$.each(all_drivers, function(k,v){
		if(driver_id == v['id']){
			driver = v['username'];
		} else {
			driver = driver;
		}
	});
	return driver;
}


function alertTransfer(message,transfer_id,type){
	var alert = "<div class='alert-transfer-error'></div>";
			alert +="<div class='alert alert-warning' role='alert'>";
  		//alert += "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>";
  		alert += message;
			alert += "</div>";

			if(type == 'edit-transfer'){
				$(".alert-div-"+transfer_id).html("");
				$(".alert-div-"+transfer_id).append(alert);
			} else if(type == 'finalize-transfer'){
				$(".alert-div-finalize-"+transfer_id).html("");
				$(".alert-div-finalize-"+transfer_id).append(alert);
			}

}




</script>
