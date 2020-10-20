<style type="text/css">
	.dl-horizontal-transfer dt{
		width: 100px !important;
		line-height: 2 !important;
	}
	.dl-horizontal-transfer dd{
		margin-left: 105px !important;
		line-height: 2 !important;
	}
	.final-count-transfer {
		width: 50px;
		border-radius: 5px;
		height: 25px;
	}
	.groups-column{
		padding-right: 2px !important;
	}
	.transfer-column{
		padding-left: 2px !important;
	}
	.active-transfer {
		background-color: #0084C7 !important;
		color: #fff !important;
	}
	.edit-transfer-modal-row {
		margin-top: 3px !important;
	}
	.active-transfer-modal {
		color: #000000 !important;
    background: #F5F5F5;
	}
	.alert-transfer {
		margin-left: 20px;
    margin-right: 20px;
	}
	.alert-transfer-error{
		padding-top: 10px;
    border-top: 1px solid white;
	}

</style>



<!--Groups-->
<div class="col-md-6 groups-column">

	<h4 class="text-center text-primary">Groups:</h4>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"></div>

</div>

<!--Transfer Items-->
<div class="col-md-6 transfer-column">

	<h4 class="text-center text-primary">Transfer: 0</h4>
	<div class="panel-group" id="accordions" role="tablist" aria-multiselectable="true"></div>

</div>

<script type="text/javascript">

function createdDate(date_data){
  result = $.format.date(date_data, "MMM dd, yyyy");
  return result;
}

var output = {!!$output!!}
var nursery_groups = {!!$nursery_groups!!}
var finisher_groups = {!!$finisher_groups!!}


function collapsers(){

	$.each(output, function(key,val){
		$(".container").delegate("#group_"+val['group_type']+val['group_id'],"click",function(){

			hideTransfer(val['group_type']+val['group_id']);
			$("#collapseme"+val['group_type']+val['group_id']).collapse('toggle');

		});
		$(".container").delegate("#group_transfer"+val['group_type']+val['group_id'],"click",function(){

			hideTransfer(val['group_type']+val['group_id']);
			$("#collapse"+val['group_type']+val['group_id']).collapse('toggle');

		});
	});

};

function hideTransfer(id){
	$.each(output, function(key,val){
		var ids = val['group_type']+val['group_id'];
		if(id != ids){
			$("#collapse"+ids).collapse('hide');
			$("#collapse"+id).collapse('show');
			$("#collapseme"+id).collapse('show');
			$("#collapseme"+ids).collapse('hide');
		}
	});
}

var group_lists = "";
var transfer_lists = "";
$.each(output, function(key,val){

	var element_ids = val['group_type']+val['group_id'];
	var days_remaining = Number(val['date_to_transfer']);

	if(val['status'] == 'entered' || val['status'] == 'pending') {

			group_lists += "<div class='panel panel-default panel-"+element_ids+"' style='margin-bottom: 5px;'>";
			group_lists += "<div class='panel-heading' role='tab' id='heading"+element_ids+"'>";
			group_lists += "<div class='transfer_button_div"+element_ids+"'>";
			group_lists += "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer transfer-"+element_ids+"' data-toggle='modal' data-target='#transfer-modal"+element_ids+"'  aria-label='Left Align'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>";
			group_lists += "</button>";
			group_lists += "</div>";
			group_lists += "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapse"+element_ids+"' aria-expanded='false' aria-controls='collapse"+element_ids+"' class='collapsed' style='text-decoration: none;' id='group_"+element_ids+"'>";
			group_lists += "<h3 class='panel-title text-left'>";
			group_lists += "<span class='text-danger glyphicon glyphicon-ban-circle status-"+element_ids+"' aria-hidden='true'></span>";
			group_lists += "<strong> "+val['group_name']+" </strong>";
			group_lists += "<span class='badge text-success badge-"+val['group_type']+"'>"+val['group_type']+"</span>";
			group_lists += "</h3>";
			group_lists += "<div><em>Days Remaining: "+val['date_to_transfer']+"</em></div>";
			group_lists += "<button type='button' class='btn btn-xs btn-info pull-right' aria-label='Left Align'>";
			group_lists += "<span class='glyphicon glyphicon-resize-vertical' aria-hidden='true'></span>";
			group_lists += "</button>";
			group_lists += "<div><em>Current Pigs: <span id='current_pigs"+element_ids+"'>"+val['total_pigs']+"</span></em></div>";
			group_lists += "</a>";
			group_lists += "</div>";

			group_lists += "<div id='collapse"+element_ids+"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading"+element_ids+"'>";
			group_lists += "<div class='panel-body inactive-"+element_ids+"'>";
			group_lists += "<div class='col-md-12'>";
			group_lists += "<hr class='hr' style='margin-top: 0px;'>";
			group_lists += "<button type='button' class='btn btn-xs btn-warning pull-right' aria-label='Left Align' style='display:none'>";
			group_lists += "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
			group_lists += "</button>";

			group_lists += "<dl class='dl-horizontal'>";
			group_lists += "<dt>Created:</dt>";
			group_lists += "<dd>"+createdDate(val['date_created'])+"</dd>";
			group_lists += "<dt>Farm:</dt>";
			group_lists += "<dd>"+val['farm_name']+"</dd>";
			group_lists += "</dl>";

			group_lists += "<div class='bins_data"+element_ids+"'>";
			$.each(val['bin_data'], function(k,v){
				group_lists += "<hr class='hr' style='margin-top: 0px; width:70%;'>";
				group_lists += "<dl class='dl-horizontal'>";
				group_lists += "<dt>Bin:</dt>";
				group_lists += "<dd>"+v['alias_label']+"</dd>";
				group_lists += "<dt>Pigs:</dt>";
				group_lists += "<dd>"+v['number_of_pigs']+"</dd>";
				group_lists += "</dl>";
			})
			group_lists += "</div>";

			group_lists += "</div>";
			group_lists += "</div>";
			group_lists += "</div>";
			group_lists += "</div>";


			transfer_lists += "<div class='panel panel-default' id='no-transfer"+element_ids+"' style='margin-bottom: 5px;'>";
			transfer_lists += "<div class='panel-heading' role='tab' id='heading"+element_ids+"' style='height: 80px;'>";
			transfer_lists += "<h3 class='panel-title text-left text-primary'>No transfer yet for Group "+val['group_name']+"</h3>";
			transfer_lists += "</div>";
			transfer_lists += "</div>";

	} else {

		var available_for_transfer_pigs = Number(val['total_pigs']) - Number(val['sched_pigs'])


			group_lists += "<div class='panel panel-info panel-"+element_ids+"'' style='margin-bottom: 5px;'>";
			group_lists += "<div class='panel-heading' role='tab' id='heading"+element_ids+"'>";

			if(available_for_transfer_pigs > 0){
			group_lists += "<div class='transfer_button_div"+element_ids+"'>";
			group_lists += "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer btn-transfer-"+element_ids+" transfer-"+element_ids+"' data-toggle='modal' data-target='#transfer-modal"+element_ids+"' aria-label='Left Align'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>";
			group_lists += "</button>";
			group_lists += "</div>";
			}

			group_lists += "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapse"+element_ids+"' aria-expanded='false' aria-controls='collapse"+element_ids+"' class='collapsed' style='text-decoration: none;' id='group_"+element_ids+"'>";
			group_lists += "<h3 class='panel-title text-left'>";
			group_lists += "<span class='text-success glyphicon glyphicon-ok-circle status-"+element_ids+"' aria-hidden='true'></span>";
			group_lists += "<strong> "+val['group_name']+" </strong>";
			group_lists += "<span class='badge text-success badge-"+val['group_type']+"'>"+val['group_type']+"</span>";
			group_lists += "</h3>";

			group_lists += "<div><em>Days Remaining: "+val['date_to_transfer']+"</em></div>";
			group_lists += "<button type='button' class='btn btn-xs btn-info pull-right' aria-label='Left Align'>";
			group_lists += "<span class='glyphicon glyphicon-resize-vertical' aria-hidden='true'></span>";
			group_lists += "</button>";
			group_lists += "<div><em>Current Pigs: "+val['total_pigs']+"</em></div>";
			group_lists += "</a>";
			group_lists += "</div>";

			group_lists += "<div id='collapse"+element_ids+"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading"+element_ids+"'>";
			group_lists += "<div class='panel-body panel-body"+element_ids+" active-transfer'>";
			group_lists += "<div class='col-md-12'>";
			group_lists += "<hr class='hr' style='margin-top: 0px;'>";
			group_lists += "<button type='button' class='btn btn-xs btn-warning pull-right' aria-label='Left Align' style='display:none'>";
			group_lists += "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
			group_lists += "</button>";

			group_lists += "<dl class='dl-horizontal'>";
			group_lists += "<dt>Created:</dt>";
			group_lists += "<dd>"+createdDate(val['date_created'])+"</dd>";
			group_lists += "<dt>Farm:</dt>";
			group_lists += "<dd>"+val['farm_name']+"</dd>";
			group_lists += "</dl>";

			group_lists += "<div class='bins_data"+element_ids+"'>";
			$.each(val['bin_data'], function(k,v){
				group_lists += "<hr class='hr' style='margin-top: 0px; width:70%;'>";
				group_lists += "<dl class='dl-horizontal'>";
				group_lists += "<dt>Bin:</dt>";
				group_lists += "<dd>"+v['alias_label']+"</dd>";
				group_lists += "<dt>Pigs:</dt>";
				group_lists += "<dd>"+v['number_of_pigs']+"</dd>";
				group_lists += "</dl>";
			})
			group_lists += "</div>";

			group_lists += "</div>";
			group_lists += "</div>";
			group_lists += "</div>";
			group_lists += "</div>";

			console.log(val)

			transfer_lists += "<div class='panel panel-info' id='with-transfer"+element_ids+"' style='margin-bottom: 5px;'>";
			transfer_lists += "<div class='panel-heading' role='tab' id='heading"+element_ids+"' style='height: 80px;'>";
			transfer_lists += "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapseme"+element_ids+"' aria-expanded='false' aria-controls='collapse"+element_ids+"' class='collapsed' style='text-decoration: none;' id='group_transfer"+element_ids+"'>";
			transfer_lists += "<h3 class='panel-title text-left'><strong>Transfer for "+val['group_name']+"</strong>";
			transfer_lists += "<button type='button' style='display:none' class='btn btn-primary btn-xs pull-right btn-transfer' nursery-id='' aria-label='Left Align'>Finalize Transfer <span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span></button>";
			transfer_lists += "</h3>";
			transfer_lists += "<div><em>Scheduled pigs for transfer: "+val['sched_pigs']+"</em></div>";
			transfer_lists += "<button type='button' class='btn btn-xs btn-info pull-right' aria-label='Left Align'>";
			transfer_lists += "<span class='glyphicon glyphicon-resize-vertical' aria-hidden='true'></span>";
			transfer_lists += "</button>";
			transfer_lists += "<input type='hidden' id='available_pigs-"+element_ids+"' value='"+available_for_transfer_pigs+"'/>";
			transfer_lists += "<div><em>Available pigs for transfer: "+available_for_transfer_pigs+"</em></div>";
			transfer_lists += "</a>";
			transfer_lists += "</div>";

			transfer_lists += "<div id='collapseme"+element_ids+"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading"+element_ids+"'>";
			transfer_lists += "<div class='panel-body active-transfer'>";


			if(val['transfer_data'] != null){
					$.each(val['transfer_data'], function(k,v){

						if(v['status'] == 'finalized'){
							/*
								transfer_lists += "<div class='row'>";
								transfer_lists += "<div class='col-md-6'>";
								transfer_lists += "<hr class='hr' style='margin-top: 0px;'>";
								transfer_lists += "<dl class='dl-horizontal dl-horizontal-transfer'>";
								transfer_lists += "<dt>Date:</dt>";
								transfer_lists+= "<dd>"+v['date']+"</dd>";
								transfer_lists += "<dt>From:</dt>";
								transfer_lists += "<dd>"+v['group_from_farm']+"</dd>";
								transfer_lists += "<dt>To:</dt>";
								transfer_lists += "<dd>"+v['group_to_farm']+"</dd>";
								transfer_lists += "<dt>Empty Weight:</dt>";
								transfer_lists += "<dd>"+v['empty_weight']+"</dd>";
								transfer_lists += "<dt>Full Weight:</dt>";
								transfer_lists += "<dd>"+v['full_weight']+"</dd>";
								transfer_lists += "<dt>Ave Weight:</dt>";
								transfer_lists += "<dd>"+v['ave_weight']+"</dd>";
								transfer_lists += "<dt>Driver:</dt>";
								transfer_lists += "<dd>"+driversName(v['driver_id'])+"</dd>";
								transfer_lists += "</dl>";
								transfer_lists += "</div>";
								transfer_lists += "<div class='col-md-6'>";
								transfer_lists += "<hr class='hr' style='margin-top: 0px;'>";
								transfer_lists += "<dl class='dl-horizontal dl-horizontal-transfer'>";
								transfer_lists += "<dt>Shipped:</dt>";
								transfer_lists += "<dd>"+v['shipped']+"</dd>";
								transfer_lists += "<dt>Received:</dt>";
								transfer_lists += "<dd>"+v['received']+"</dd>";
								transfer_lists += "<dt>Dead:</dt>";
								transfer_lists += "<dd>"+v['dead']+"</dd>";
								transfer_lists += "<dt>Poor:</dt>";
								transfer_lists += "<dd>"+v['poor']+"</dd>";
								transfer_lists += "<dt>Farm Count:</dt>";
								transfer_lists += "<dd>"+v['farm_count']+"</dd>";
								transfer_lists += "<dt>Final Count:</dt>";
								transfer_lists += "<dd>"+v['final_count']+"</dd>";
								transfer_lists += "</dl>";
								transfer_lists += "<span class='btn btn-success pull-right btn-transfer' style='cursor:none;'>Finalized <span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span></span>";
								transfer_lists += "</div>";
								transfer_lists += "</div>";
								*/
						} else {

								transfer_lists += "<div class='row'>";
								transfer_lists += "<div class='alert-div-"+v['transfer_id']+" alert-transfer'></div>";
								transfer_lists += "<div class='col-md-6'>";
								transfer_lists += "<hr class='hr' style='margin-top: 0px;'>";
								transfer_lists += "<dl class='dl-horizontal dl-horizontal-transfer'>";
								transfer_lists += "<dt>Transfer #:</dt>";
								transfer_lists += "<dd>"+v['transfer_number']+"</dd>";
								transfer_lists += "<dt>Date:</dt>";
								transfer_lists += "<dd>"+v['date']+"</dd>";
								transfer_lists += "<dt>From:</dt>";
								transfer_lists += "<dd>"+v['group_from_farm']+"</dd>";
								transfer_lists += "<dt>To:</dt>";
								transfer_lists += "<dd>"+v['group_to_farm']+"</dd>";
								transfer_lists += "<dt>Empty Weight:</dt>";
								transfer_lists += "<dd>"+v['empty_weight']+"</dd>";
								transfer_lists += "<input type='hidden' name='Empty Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['empty_weight']+"'/>";
								transfer_lists += "<dt>Full Weight:</dt>";
								transfer_lists += "<dd>"+v['full_weight']+"</dd>";
								transfer_lists += "<input type='hidden' name='Full Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['full_weight']+"'/>";
								transfer_lists += "<dt>Ave Weight:</dt>";
								transfer_lists += "<dd>"+v['ave_weight']+"</dd>";
								transfer_lists += "<input type='hidden' name='Ave Weight' class='transfer-info-"+v['transfer_id']+"' value='"+v['ave_weight']+"'/>";
								transfer_lists += "<dt>Driver:</dt>";
								transfer_lists += "<dd>"+driversName(v['driver_id'])+"</dd>";
								transfer_lists += "</dl>";
								transfer_lists += "</div>";
								transfer_lists += "<div class='col-md-6'>";
								transfer_lists += "<hr class='hr' style='margin-top: 0px;'>";
								transfer_lists += "<button type='button' style='margin-left: 5px;' class='btn btn-xs btn-danger pull-right' data-toggle='modal' data-target='#delete-modal"+v['transfer_id']+"' aria-label='Left Align'>";
								transfer_lists += "<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
								transfer_lists += "</button>";
								transfer_lists += "<button type='button' class='btn btn-xs btn-warning pull-right' data-toggle='modal' data-target='#edit-modal"+v['transfer_id']+"' aria-label='Left Align'>";
								transfer_lists += "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
								transfer_lists += "</button>";
								transfer_lists += "<dl class='dl-horizontal dl-horizontal-transfer'>";
								transfer_lists += "<dt>Shipped:</dt>";
								transfer_lists += "<dd>"+v['shipped']+"</dd>";
								transfer_lists += "<input type='hidden' name='Shipped' class='transfer-info-"+v['transfer_id']+"' value='"+v['shipped']+"'/>";
								transfer_lists += "<dt>Received:</dt>";
								transfer_lists += "<dd>"+v['received']+"</dd>";
								transfer_lists += "<input type='hidden' name='Received' class='transfer-info-"+v['transfer_id']+"' value='"+v['received']+"'/>";
								transfer_lists += "<dt>Dead:</dt>";
								transfer_lists += "<dd>"+v['dead']+"</dd>";
								transfer_lists += "<input type='hidden' name='Dead' class='transfer-info-"+v['transfer_id']+"' value='"+v['dead']+"'/>";
								transfer_lists += "<dt>Poor:</dt>";
								transfer_lists += "<dd>"+v['poor']+"</dd>";
								transfer_lists += "<input type='hidden' name='Poor' class='transfer-info-"+v['transfer_id']+"' value='"+v['poor']+"'/>";
								transfer_lists += "<dt>Farm Count:</dt>";
								transfer_lists += "<dd>"+v['farm_count']+"</dd>";
								transfer_lists += "<input type='hidden' name='Farm Count' class='transfer-info-"+v['transfer_id']+"' value='"+v['farm_count']+"'/>";
								transfer_lists += "<dt>Final Count:</dt>";
								transfer_lists += "<dd>"+v['final_count']+"</dd>";
								transfer_lists += "<input type='hidden' name='Final Count' class='transfer-info-"+v['transfer_id']+"' value='"+v['final_count']+"'/>";
								transfer_lists += "</dl>";
								transfer_lists += "<button type='button' class='btn btn-success btn-xs pull-right btn-transfer btn-transfer-modal' ";
								transfer_lists += "element_ids='"+element_ids+"' ";
								transfer_lists += "id='btn-transfer-modal"+v['transfer_id']+"' ";
								transfer_lists += "transfer_id='"+v['transfer_id']+"' ";
								transfer_lists += "group_from='"+v['group_from']+"' ";
								transfer_lists += "group_to='"+v['group_to']+"' ";
								transfer_lists += "transfer_type='"+v['transfer_type']+"' ";
								transfer_lists += "farm_id_from='"+v['farm_id_from']+"' ";
								transfer_lists += "farm_id_to='"+v['farm_id_to']+"' ";
								transfer_lists += "data-toggle='modal' data-target='#finalize-modal"+v['transfer_id']+"' aria-label='Left Align'>Finalize Transfer <span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span></button>";
								transfer_lists += "</div>";
								transfer_lists += "</div>";

								transfer_lists += deleteTransferModal(element_ids,v,val);
								transfer_lists += finalizeTransferModal(element_ids,v,val);

								if(val['group_type'] == 'farrowing'){
										transfer_lists += editTransferModal(val,v,nursery_groups,element_ids);
								} else if(val['group_type'] == 'nursery') {
										transfer_lists += editTransferModal(val,v,finisher_groups,element_ids);
								} else {
										transfer_lists += editTransferModal(val,v,"",element_ids);
								}

						}

					});
			}

			transfer_lists += "</div>";
			transfer_lists += "</div>";
		  transfer_lists += "</div>";

	}
	if(val['group_type'] == 'farrowing'){
			group_lists += transferModal(val,nursery_groups);
	} else if(val['group_type'] == 'nursery') {
			group_lists += transferModal(val,finisher_groups);
	} else {
			group_lists += transferModal(val,"");
	}

});


$("#accordion").append(group_lists);
$("#accordions").append(transfer_lists);

// scheduling page date picker
$(".datepickerSchedTool").datepicker({
	controlType: 'select',
	oneLine: true,
	dateFormat: 'M d, yy',
	//comment the beforeShow handler if you want to see the ugly overlay
	beforeShow: function() {
		setTimeout(function(){
			$('.ui-datepicker').css('z-index', 99999999999999);
		}, 0);
	}
});

// trigger the change date
$(".datepickerSchedTool").change(function(){
	var date_selected = $(this).val();
});


</script>
