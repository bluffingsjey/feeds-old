<script type="text/javascript">

$(".datePicker").datepicker({
	controlType: 'select',
	oneLine: true,
	dateFormat: 'M dd, yy',
	//maxDate: new Date(),
	//comment the beforeShow handler if you want to see the ugly overlay
	beforeShow: function() {
		setTimeout(function(){
			$('.ui-datepicker').css('z-index', 99999999999999);
		}, 0);
	}
});

var animal_groups = {!! json_encode($animal_groups) !!};
var animal_groups_option = "<option value='none'>Please Select</option>";
$.each(animal_groups, function(k,v){
	animal_groups_option += "<option value='"+v['group_id']+"' farm_id='"+v['farm_id']+"' type='"+v['type']+"' unique_id='"+v['unique_id']+"' >"+v['group_name']+"</option>"
});


$("#groups").html("");
$("#groups").append(animal_groups_option);
loadTreamentData();

$("#groups").change(function(){

	farm_id = $("#groups option:selected").attr("farm_id");
	unique_id = $("#groups option:selected").attr("unique_id");
	type_table = $("#groups option:selected").attr("type");

	loadFarms(farm_id);
	loadBins(unique_id,type_table);

});

/*
* Load the farms
*/
function loadFarms(farm_id){
	farms_option = "";
	$("#farms").attr("disabled",true);
	$("#farms").html("<option>Loading...</option>");
	$.ajax({
		url	:	app_url + '/loadGroupFarms',
		data	:	{'farm_id':farm_id},
		type	:	'get',
		success: function(data){
			farms_option += "<option value='"+farm_id+"'>"+data+"</option>"
			$("#farms").html("");
			$("#farms").html(farms_option);
			$("#farms").attr("disabled",false);
		}
	});
}

/*
* Load the bins
*/
function loadBins(unique_id,type_table){
	bins_option = "";
	$(".btn-save").attr("disabled",true);
	$("#bins").attr("disabled",true);
	$("#bins").html("<option>Loading...</option>");
	$.ajax({
		url	:	app_url + '/loadGroupBins',
		data	:	{'unique_id':unique_id,'table':type_table},
		type	:	'get',
		success: function(data){
			$.each(data, function(k,v){
					bins_option += "<option value='"+v.bin_id+"' bin_pigs='"+v.number_of_pigs+"'>"+v.bin_label+"</option>"
			});
			$("#bins").html("");
			$("#bins").html(bins_option);
			$("#bins").attr("disabled",false);

			if($("#bins option:selected").text() == "No Bins"){
				$(".btn-save").attr("disabled",true);
			}else{
				$(".btn-save").attr("disabled",false);
			}
		}
	});
}


$(".btn-save").click(function(){
	$(this).attr("disabled",true);
	var treatment = $("#amount").val();
	var bin_pigs = $("#bins option:selected").attr("bin_pigs");
	if(parseInt(bin_pigs) == 0 || parseInt(bin_pigs) < 0){
		alert("Empty bin.")
		$(this).attr("disabled",false);
	}else if(parseInt(treatment) > parseInt(bin_pigs)){
		alert("Treatment pigs are greater than the amount pigs on the bin.")
		$(this).attr("disabled",false);
	}else if(parseInt(bin_pigs) < parseInt(treatment)){
		alert("Pigs of bin has less than the amount of treatment pigs")
		$(this).attr("disabled",false);
	} else {
		saveTreatment();
	}
})

/*
* Load the bins
*/
function saveTreatment(){
	$('#treatmentModal').modal('hide');

	$.ajax({
		url	:	app_url + '/savetreatment',
		data	:	{
							'group_id'		:	$("#groups").val(),
							'group_type'	:	$("#groups option:selected").attr("type"),
							'unique_id'		:	$("#groups option:selected").attr("unique_id"),
							'farm_id'			:	$("#farms").val(),
							'bin_id'			:	$("#bins").val(),
							'created_at'	:	$("#date").val(),
							'pigs'				:	$("#amount").val(),
							'illness'			:	$("#illness").val(),
							'drug_used'		:	$("#drug_used").val(),
							'notes'				:	$("#notes").val()
						},
		type	:	'post',
		success: function(data){

				window.location.reload();
				loadTreatmentData();

		}
	});
}

$(document).ready(function(e) {
	$(".container").delegate('.btn-delete','click', function(){
		removeTreatment($(this).attr("id"));
	});
});

/*
* Load the bins
*/
function loadTreamentData(){
	$(".treatment-data").html("");
	var treatment_data = "";
	$.ajax({
		url	:	app_url + '/treatmentdata',
		type	:	'get',
		success: function(data){
			$.each(data, function(k,v){
				treatment_data += "<tr class='data-"+v.id+"'>";
				treatment_data += "<td>"+v.created_at+"</td>";
				treatment_data += "<td>"+v.group_name+"</td>";
				treatment_data += "<td>"+v.farm_name+"</td>";
				treatment_data += "<td>"+v.bin_label+"</td>";
				treatment_data += "<td>"+v.pigs+" Pig/s</td>";
				treatment_data += "<td>"+v.illness+"</td>";
				treatment_data += "<td>"+v.drug_used+"</td>";
				treatment_data += "<td class='col-md-2'>"+v.notes+"</td>";
				treatment_data += "<td><button class='btn btn-xs btn-danger btn-delete' id='"+v.id+"'>Delete</button></td>";
				treatment_data += "<tr>";
			});
			$(".treatment-data").html(treatment_data);
		}
	});
}

/*
* remove the deceased
*/
function removeTreatment(id){
	$.ajax({
		url		:	app_url + '/removetreatment',
		data	:	{'id':id},
		type	:	'post',
		success: function(data){
			$(".data-"+id).hide();
		}
	});
}

</script>
