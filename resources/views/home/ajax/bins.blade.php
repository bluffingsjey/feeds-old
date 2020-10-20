<div class="panel-group" id="accordion{{$farm_data[0]['id']}}"></div>

<script type="text/javascript">

var farm_id = {{$farm_id}}; // farm id
var farm_name = "{{$farm_data[0]['name']}}"; // farm name
var bins_data = {!!$bins_data!!}; //all the bins data
var total_pigs = 0; // default total number of pigs
var bins_data_html = ""; // holder for bins data
var farms_list = {!!json_encode($farms_list)!!};
var farm_list = [];
var empty_date = "";
$.each(farms_list, function(farm_id,farm_name){
  farm_list.push({'farm_id':farm_id,'farm_name':farm_name});
})
farm_list.sort(function(a, b){
  var aName = a.farm_name.toLowerCase();
  var bName = b.farm_name.toLowerCase();
  return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
});

$.each( bins_data, function( key, value ) {

  $('#collapseinline'+value['bin_id']).on('hidden.bs.collapse.collapse'+farm_id, function () {
		$('.farm-header-two-'+farm_id).show();
		$('.farm-header-one-'+farm_id).hide();
		return false;
	})

	// collapse bin div
	$(".container").delegate(".bin-collapse"+value['bin_id'],'click',function(){
		var data = {
				'farmId'	:	$(this).attr("farm-id"),
				'farmName'	:	$(this).attr("farm-name"),
				'binId'		:	$(this).attr("bin-id"),
				'binNumber'	:	$(this).attr("bin-number")
			}
    var selected_farm_id = 	$(this).attr("farm-id");
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
			type: "get",
			success: function(r){

				$('.feedTypeId').empty();
				$('.farmName').empty();
				$('.binNumber').empty();

        var selected_option = "";
        $.each(farm_list,function(key,val){
          if(selected_farm_id == val['farm_id']){
            selected_option = "selected";
          } else {
            selected_option = "";
          }
          if(val['farm_name'] != "Please Select") {
            $('.farmName').append($("<option "+selected_option+">").text(val['farm_name']).attr('value',val['farm_id']));
          }
        })

				$.each(r.bins, function(i,v){
					selected = (i == data['binId'] ? 'selected' : '');
					$('.binNumber').append($('<option '+selected+'>').text(v).attr('value',i));
				})

				loadFeeds();
			}
		})

	 });



    bins_data_html += "<div class='panel panel-info'>";
    bins_data_html += "<div class='panel-heading'>";
    bins_data_html += "<div class='bin-collapse"+value['bin_id']+"' data-toggle='collapse' farm-name='"+farm_name+"' farm-id='{{$farm_data[0]['id']}}' bin-number='"+value['bin_number']+"' bin-id='"+value['bin_id']+"' data-parent='#accordion{{$farm_data[0]['id']}}' data-target='#collapseinline"+value['bin_id']+"' style='cursor: pointer'>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='row' data-toggle='collapse' data-parent='#accordion"+farm_id+"' data-target='#collapseinline"+value['bin_id']+"' style='cursor: pointer'>";
    bins_data_html += "<div class='col-md-12 bin-collapse"+value['bin_id']+"' data-toggle='collapse' farm-name='"+farm_name+"' farm-id='"+farm_id+"' bin-number='"+value['bin_number']+"' bin-id='"+value['bin_id']+"' style='z-index:0;'>";
    bins_data_html += "<div class='col-md-2'>";
    bins_data_html += "<a class='bin-collapse"+value['bin_id']+"' id='a-tag-bin-"+value['bin_id']+"' data-toggle='collapse' farm-name='"+farm_name+"' farm-id='"+farm_id+"' bin-number='"+value['bin_number']+"' bin-id='"+value['bin_id']+"' data-parent='#accordion"+farm_id+"' href='#collapseinline"+value['bin_id']+"' style='text-decoration:none'>Bin #"+value['bin_number']+" - "+value['alias']+"</a>";

    //bins_data_html += "<br/><span class='no-manual-update no-update-1' rel='tooltip' data-toggle='tooltip' data-placement='bottom' style='font-size: 10px;'>NO MANUAL UPDATE</span>";

    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<div class='progress' style='margin-bottom:0px;'>";
    if (value['days_to_empty'] == 0){
    bins_data_html += "<div class='progress-bar progress-bar-danger binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 1%;'>"+value['days_to_empty']+" Days</div>";
    } else if (value['days_to_empty'] == 1){
    bins_data_html += "<div class='progress-bar progress-bar-danger binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 20%;'>"+value['days_to_empty']+" Day</div>";
    } else if (value['days_to_empty'] == 2){
    bins_data_html += "<div class='progress-bar progress-bar-danger binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 40%;'>"+value['days_to_empty']+" Days</div>";
    } else if (value['days_to_empty'] == 3){
    bins_data_html += "<div class='progress-bar progress-bar-warning binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 60%;'>"+value['days_to_empty']+" Days</div>";
    } else if (value['days_to_empty'] == 4){
    bins_data_html += "<div class='progress-bar progress-bar-success binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 80%;'>"+value['days_to_empty']+" Days</div>";
    } else if (value['days_to_empty'] == 5){
    bins_data_html += "<div class='progress-bar progress-bar-success binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 100%;'>"+value['days_to_empty']+" Days</div>";
    } else {
    bins_data_html += "<div class='progress-bar progress-bar-success binprog myprog"+value['bin_id']+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: 100%;'>"+value['days_to_empty']+" Days</div>";
    }

    if(value['empty_date'] == "Empty"){
      empty_date = "-";
    } else {
      empty_date = value['empty_date'];
    }

    if(value['default_val'] != null){
      $.each( value['default_val'], function( k, v ) {
          total_pigs = v['number_of_pigs']; // total_pigs + v['number_of_pigs'];
      });
    }else{
      total_pigs = 0;
      empty_date = "-";
    }


    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-1 mytons"+value['bin_id']+"'>";
    bins_data_html += "<small>"+value['current_bin_amount_tons']+" T</small>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-1 text-center myempty"+value['bin_id']+"'>";
    bins_data_html += "<small>"+empty_date+"</small>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-1 text-center'>";
    bins_data_html += "<small>"+value['delivery_amount']+"</small>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-1 text-center'>";
    bins_data_html += "<small>"+value['next_deliverydd']+"</small>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-1 text-center'>";
    if (value['last_update'] != null){
    bins_data_html += "<small class='lstupd"+value['bin_id']+" lastupdate' rel='_tooltip' data-toggle='_tooltip' data-placement='bottom' title='Last updated "+lastUpdateBy(value['last_update'][0]['update_date'])+" by "+value['username']+"'>"+lastUpdate(value['last_update'][0]['update_date'])+"</small>";
    } else {
    bins_data_html += "<small>-</small>";
    }
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-2'>";
    bins_data_html += "<small><button class='btn btn-xs btn-block btn-info btn-update-bin btn_bin"+value['bin_id']+"' data-toggle='modal' data-target='#bin-modal"+value['bin_id']+"' style='margin-bottom:5px; z-index:9;'>Update Bin</button></small>";
    bins_data_html += "<small><button class='btn btn-xs btn-block btn-warning btn-update-bin btn_pigs"+value['bin_id']+"' data-toggle='modal' data-target='#pigs-modal"+value['bin_id']+"' default-amount='"+value['num_of_pigs']+"'>Update # of Pigs</button></small>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "<div id='collapseinline"+value['bin_id']+"' class='collapseinline"+value['bin_id']+" panel-collapse collapse panel-body'>";
    bins_data_html += "<div class='col-md-12'>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Current Medication:</dt>";
    bins_data_html += "<dd>"+value['medication']+"</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Next Delivery:</dt>";
    bins_data_html += "<dd>"+value['next_delivery']+"</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Current Feed:</dt>";
    bins_data_html += "<dd class='curfeedt"+value['bin_id']+"'>"+value['feed_type_name']+"</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<dl class=''>";


    bins_data_html += "<dt>Number of Pigs:</dt>";
    bins_data_html += "<dd class='pigvalue"+value['bin_id']+"'>"+total_pigs+"</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<dl class='amount-"+value['bin_id']+"'>";
    bins_data_html += "<dt>Ring Amount:</dt>";
    bins_data_html += "<dd class='amount-expanded-"+value['bin_id']+"' style='display:none'></dd>";
    bins_data_html += "</dl>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='row'>";
    bins_data_html += "<div class='col-md-12'>";
    bins_data_html += "<div class='col-md-9'>";
    bins_data_html += "<div id='curve_chart"+value['bin_id']+"' style='width: 610px; height: 300px;'></div>";
    bins_data_html += "</div>";
    bins_data_html += "<div class='col-md-3'>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Variance:</dt>";
    bins_data_html += "<dd class='avg_variance"+value['bin_id']+"'>"+avgVariance(value['average_actual'],value['num_of_update'],value['budgeted_amount'])+" lbs</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Actual:</dt>";
    bins_data_html += "<dd class='avg_actual"+value['bin_id']+"'>"+avgActual(value['average_actual'],value['num_of_update'])+" lbs</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "<dl class=''>";
    bins_data_html += "<dt>Budgeted:</dt>";
    bins_data_html += "<dd>"+value['budgeted_amount']+" lbs</dd>";
    bins_data_html += "</dl>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";
    bins_data_html += "</div>";

    pigs_modal = pigsModal(bins_data[key],farm_id);
    bins_modal = binsModal(bins_data[key]);

    bins_data_html += pigs_modal;
    bins_data_html += bins_modal;

});


function pigsModal(bins_data,farm_id){

  var total_pigs = 0;
  var pigs_modal = "<div class='modal fade' id='pigs-modal"+bins_data['bin_id']+"' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>";
      pigs_modal += "<div class='modal-dialog' role='document'>";
      pigs_modal += "<div class='modal-content'>";
      pigs_modal += "<div class='modal-header'>";
      pigs_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
      pigs_modal += "<h4 class='modal-title' id='myModalLabel'>UPDATE NUMBER OF PIGS</h4>";
      pigs_modal += "</div>";
      pigs_modal += "<div class='modal-body'>";
      pigs_modal += "<div class='form-group'>";

      if(bins_data['default_val'] != null) {

        pigs_modal += "<div class='input-group'>";
        pigs_modal += "<div class='col-sm-12'><h2 class='text-center text-primary'>Bin #"+bins_data['bin_number']+" - "+bins_data['alias']+"</h2></div>";
        pigs_modal += "<hr/>";
        pigs_modal += "<label class='col-sm-6 control-label text-center' for='exampleInputAmount' >Group ID:</label>";
        pigs_modal += "<label class='col-sm-6 control-label text-left' for='exampleInputAmount' >Number of Pigs:</label>";

        $.each(bins_data['default_val'], function(k,v){
          if(bins_data['bin_id'] == v['bin_id']){
            total_pigs = total_pigs + v['number_of_pigs'];
            pigs_modal += "<div class='col-sm-offset-2 col-sm-4'>";
            pigs_modal += "<p class='text-primary'>"+v['group_name']+"</p>";
            pigs_modal += "</div>";
            pigs_modal += "<div class='col-sm-4'>";
            pigs_modal += "<input type='number' name='number_of_pigs[]' class='form-control input-sm numpigsupdate"+bins_data['bin_id']+"' id='numberOfPigs"+bins_data['bin_id']+"-"+v['unique_id']+"' value='"+v['pigs_per_group']+"' placeholder='Number of Pigs' animal-unique-id='"+v['unique_id']+"'>";
            pigs_modal += "</div>";
          }
        });

        pigs_modal += "</div>";
        pigs_modal += "<br/>";
    		//pigs_modal += "<label class='col-sm-offset-4 col-sm-6 control-label text-right' for='exampleInputAmount' >Total Number of Pigs: <span class='total-pigs"+bins_data['bin_id']+"'>"+total_pigs+"</span></label>";

      } else {

        pigs_modal += "<div class='input-group'>";
        pigs_modal += "<div class='col-sm-12'>";
        pigs_modal += "<h2 class='text-center text-primary'>No Group yet...</h2>";
        pigs_modal += "<p class='text-info'><a href='{{ url('/animalgroup') }}'>Create Group?</a></p>";
        pigs_modal += "</div>";
        pigs_modal += "</div>";

      }

      pigs_modal += "</div>";
      pigs_modal += "</div>";
      pigs_modal += "<div class='modal-footer'>";
      pigs_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";

      if(bins_data['default_val'] != null){

        pigs_modal += "<button type='button' class='btn btn-success updatePig' bin-number='"+bins_data['bin_id']+"' farm-id='"+farm_id+"' animal-unique-id='' data-dismiss='modal'>Save changes</button>";

      }

      pigs_modal += "</div>";
      pigs_modal += "</div>";
      pigs_modal += "</div>";
      pigs_modal += "</div>";

      return pigs_modal;

}

function binsModal(bins_data){
  var selected = "";
  var bins_modal = "<div class='modal fade' id='bin-modal"+bins_data['bin_id']+"' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>";
      bins_modal += "<div class='modal-dialog' role='document'>";
      bins_modal += "<div class='modal-content'>";
      bins_modal += "<div class='modal-header'>";
      bins_modal += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
      bins_modal += "<h4 class='modal-title' id='myModalLabel'>Update Bin Level</h4>";
      bins_modal += "</div>";
      bins_modal += "<div class='modal-body form-horizontal'>";
      bins_modal += "<div class='form-group'>";
      bins_modal += "<div class='col-sm-offset-1 col-sm-10'>";
      bins_modal += "<select class='form-control ddslickme' id='amountOfBins"+bins_data['bin_id']+"' name='binsAmount'>";
      $.each(bins_data['bin_s'], function(k,v){
        if(bins_data['default_amount'] == k){
          selected = "selected='selected'";
        } else {
          selected = "";
        }
      bins_modal += "<option value='"+k+"' "+selected+">"+v+"</option>";
      });
      bins_modal += "</select>";
      bins_modal += "</div>";
      bins_modal += "</div>";
      bins_modal += "</div>";
      bins_modal += "<div class='modal-footer'>";
      bins_modal += "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
      bins_modal += "<button type='button' class='btn btn-primary updateBin'  bin-number='"+bins_data['bin_id']+"' pigs='"+bins_data['num_of_pigs']+"' data-dismiss='modal'>Save changes</button>";
      bins_modal += "</div>";
      bins_modal += "</div>";
      bins_modal += "</div>";
      bins_modal += "</div>";

      return bins_modal;
}

function avgVariance(average_actual,num_of_update,budgeted_amount){
  var result = 0;
  result = average_actual / num_of_update;
  result = result - budgeted_amount;
  return numberFormat(result);
}

function avgActual(average_actual,num_of_update){
  var result = 0;
  result = average_actual / num_of_update;
  return numberFormat(result);
}

function lastUpdate(date_data){
  result = $.format.date(date_data, "MMM dd");
  return result;
}

function lastUpdateBy(date_data){
  result = $.format.date(date_data, "MMM dd, yyyy - hh:mm a");
  return result;
}

function numberFormat(number){
  result = Number(number).toFixed(2);
  return result;
}

$("#accordion"+farm_id).html("");
$("#accordion"+farm_id).html(bins_data_html);

function drawChart() {


  var chart_data = [];
  var budgeted_amount = 0;
  var chart_data_graph = [];
  var chart = [];

  $.each( bins_data, function( key, value ) {

      if(value['graph_data'] == null){
        chart_data[key] = google.visualization.arrayToDataTable([
          ["Date", "Actual (Tons)", "Budgeted (Tons)"],
          ['-', 0, 0],
          ['-', 0, 0],
          ['-', 0, 0],
          ['-', 0, 0],
          ['-', 0, 0],
          ['-', 0, 0]
        ]);
      } else {
        chart_data_graph = [];
        chart_data_graph.push(["Date", "Actual (Tons)", "Budgeted (Tons)"]);
        $.each(value['graph_data'], function( k,v ){
          if(v['budgeted_amount'] != 0){budgeted_amount = v['budgeted_amount']}
          chart_data_graph.push([lastUpdate(v['update_date']),v['actual'],budgeted_amount]);
        });
        chart_data[key] = google.visualization.arrayToDataTable(chart_data_graph);
      }

      if(bins_data[key]['bin_id'] == 779){
          console.log(value['graph_data']);
      }

      var arr = value['bin_s'];
    	var num = value['current_bin_amount_tons'];

    	var arr_val=[];
    	var test_val = [];
    	$.each(arr, function(i,v){
    		arr_val.push(i);
    	});

    	var curr = arr_val[0];
      var diff = Math.abs (curr - num);
    	for (var val = 0; val < arr_val.length; val++) {
    		var newdiff = Math.abs (num - arr_val[val]);
    		if (diff > newdiff) {
    			diff = newdiff;
    			curr = arr_val[val];
    			if(curr < num){
    				curr = arr_val[val+1];
    			}
    		}
    	}

    	$(".amount-expanded-"+bins_data[key]['bin_id']).text("");
    	$(".amount-expanded-"+bins_data[key]['bin_id']).text(num+" Tons");
    	$(".amount-"+bins_data[key]['bin_id']).append("<dd>"+arr[curr]+"</dd>");

    	chart[key] = new google.visualization.ColumnChart(document.getElementById('curve_chart'+bins_data[key]['bin_id']));
    	chart[key].draw(chart_data[key], options);




  });

}


</script>
