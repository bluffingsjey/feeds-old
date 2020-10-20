@extends('app')


@section('content')

<style type="text/css">
.readonly{
	background: #FFF !important;
}
.disabled{
	background: #FF0004 !important;
}
</style>
<div class="col-md-10">

<div class="row">
    <div class="col-sm-6 col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>Create Nursery Group</strong>
                <a href="{{url('/nursery')}}" class="btn btn-warning btn-xs pull-right" role="button">Back</a>
            </div>
            <div class="panel-body holder">
            	<div class="col-md-offset-2 col-md-8">
                    <form class="form-horizontal" action="{{url('/savenursery')}}" method="post" id="nursery_form">
                    	<input id="unique_id" name="unique_id" type="hidden" value="{{$unique_id}}"/>
                      <div class="form-group">
                        <label class="col-sm-4 control-label">Nursery Group</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control" name="group_name" id="group_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-5">
                          <input type="text" class="form-control datepickerSchedTool" name="date_time" id="date_time" value="{{date("M d, Y")}}">
                        </div>
                      </div>

											<div class="form-group">
                        <label class="col-sm-4 control-label">Start Weight (lbs)</label>
                        <div class="col-sm-5">
                          <!--<p class="form-control-static group_name"></p>-->
                          <input name="start_weight" type="number" min="0" class="form-control negative" id="start_weight" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">End Weight (lbs)</label>
                        <div class="col-sm-5">
                          <!--<p class="form-control-static group_name"></p>-->
                          <input name="end_weight" type="number" min="0" class="form-control negative" id="end_weight" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Nursery Farm</label>
                        <div class="col-sm-5">
                          <select name="nursery" class="form-control" id="nursery">
                          	@forelse($nursery as $k => $v)
                              <option value="{{$k}}">{{$v}}</option>
                            @empty
                              <option>none</option>
                            @endforelse
                          </select>
                        </div>
                      </div>

											<div class="bins_holder"></div>
											<!--
                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Bin One</label>
                        <div class="col-sm-5">
                          <select name="bin_one" class="form-control" id="bin_one">

                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Bin Two</label>
                        <div class="col-sm-5">
                          <select name="bin_two" class="form-control" id="bin_two">

                          </select>
                        </div>
                      </div>



                      <div class="form-group" style="display: none;">
                        <div class="col-sm-offset-4 col-sm-5">
                          <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#farrowingModal">Add Farrowing Group</button>
                        </div>
                      </div>

                      <div class="AddedFarrowGroups">

                      </div>


											<div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Pigs</label>
                        <div class="col-sm-5">
                          <input name="number_of_pigs" type="number" class="form-control" value="0" id="pigs">
                        </div>
                      </div>
											-->

                      <hr/>


                      <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-5">
                          <button type="button" class="btn btn-block btn-success btn-create-group">Create</button>
                        </div>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



</div>


<!-- Modal -->
<div class="modal fade" id="farrowingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Farrowing Groups</h4>
      </div>
      <div class="modal-body">
      	<form class="form-horizontal">
            <div class="form-group">
                <label for="inputPassword" class="col-sm-4 control-label">Groups</label>
                <div class="col-sm-5">
                 <select id="farrowingGroups" class="form-control">
                  @forelse($farrowing_groups as $k => $v)
                      <option value="{{$v['group_id']}}">{{$v['group_name']}}</option>
                  @empty
                      <option>none</option>
                  @endforelse
                 </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword" class="col-sm-4 control-label">Number of Pigs</label>
                <div class="col-sm-5">
                 <input type="number" id="numberOfPigs" class="form-control input-sm" value="0" id="number_of_pigs" />
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-add-farowing-group">Add</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(e) {
/*$(window).on('beforeunload', function () {
    if ($('#nursery').length) {
		emptyPending();
		clearSelectMenuSelection();
        return 'All changes will not be saved.';
    }
});*/

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


function emptyPending(){
	$.ajax({
		url		:	app_url+'/emptyPendingSelection',
		type	:	"POST",
		success	: function(r){

		}
	});
}

	$(".btn-add-farowing-group").click(function(e) {

        data = {
			'group_id':$("#farrowingGroups").val(),
			'number_of_pigs':$("#numberOfPigs").val(),
			'unique_id': $("#unique_id").val()
		}
		$.ajax({
			url		:	app_url+'/savePendingSelection',
			data	:	data,
			type	:	"POST",
			success	: function(r){
				if(r == "exists"){
					alert("Group already added");
				} else {
					// close modal dialog box
					$('#farrowingModal').modal('hide');

					$(".AddedFarrowGroups").html("");
					$(".AddedFarrowGroups").html(r)
				}
			}
		});

    });

	$(".container").delegate(".glyphicon-remove","click",function(e) {
		disableCreate()
		id = $(this).attr("id");

		$.ajax({
			url		:	app_url+'/deletePendingSelection',
			data	:	{'id':id},
			type	:	"POST",
			success	: function(){
				$(".farrow-"+id).remove();
				enableCreate()
			}
		});

    });


	nurseryBins($("#nursery").val());

	// nursery groups generator
	function nurseryGroup(nursery_id){
		disableCreate()
		//setTimeout(function(){
			// load bin for specific farm
			$.ajax({
				url		:	app_url+'/groupname',
				data	:	{'farm_id':nursery_id,'bin_id':$("#bin-0").val()},
				type	:	"GET",
				success	: function(r){
					$("#group_name").html("");
					$("#group_name").val(r);
					enableCreate()
				}
			});
		//},2000)

	}

	$("#nursery").change(function(e) {
    	id = $(this).val();
		nurseryBins(id);
		//clearSelectMenuSelection();
	});

	$("#bin_one").change(function(e) {

    	bin_id = $(this).val();
		farm_id = $("#nursery").val();

		if(bin_id == $("#bin_two").val()) {
			alert("bin already selected");
			//getSelectMenuSelection();
		} else {
			//saveSelecMenuSelection()
			if($("#bin_two").val() == ""){
				//nurseryGroup(farm_id,bin_id);
			}else{
				//groupNameTwo(farm_id,bin_id,$("#bin_two").val())
			}
		}

	});

	$("#bin_two").change(function(e) {

		bin_id = $(this).val();
		farm_id = $("#nursery").val();

		if(bin_id == $("#bin_one").val()) {
			alert("bin already selected");
			//getSelectMenuSelection();
		} else {
			//saveSelecMenuSelection()
			//groupNameTwo(farm_id,$("#bin_one").val(),bin_id)
		}

	});

	function nurseryBins(farm_id){
		disableCreate()
		$.ajax({
			'url'	:	app_url+"/farrowingbins",
			data	: 	{'farm_id':farm_id},
			type	:	"GET",
			success	: 	function(r){
				var bin_data = r;

        var select_options = "";
				$.each(bin_data, function(k,v){
					select_options += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
				})

        var bins = "";
        $.each(bin_data, function(k,v){
              bin_number = k+1;
              bins += "<div class='form-group'>";
              bins += "<label for='inputPassword' class='col-sm-6 text-center'>Bin "+bin_number+"</label>";
							bins += "<label for='inputPassword' class='col-sm-6 text-center'>Bin "+bin_number+": Number of Pigs</label>";
              bins += "<div class='col-sm-6'>";
              bins += "<select name='bins[]' class='form-control bins_menu' id='bin-"+k+"'>";
              if(k != 0){
              bins += "<option value='none-"+k+"'>none</option>";
              }
              bins += select_options
              bins += "</select>";
              bins += "</div>";
							bins += "<div class='col-sm-6'>";
              bins += "<input name='num_of_pigs[]' type='number' min='0' class='form-control num_of_pigs negative' value='0'/>";
              bins += "</div>";
              bins += "</div>";
				})
        $(".bins_holder").html("");
				$(".bins_holder").append(bins);

				/*
				var select_options = "";
				$.each(bin_data, function(k,v){
					select_options += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
				})
				$("#bin_one").html("");
				$("#bin_one").append(select_options);

				var select_options_two = "<option value=''>none</option>";
				$.each(bin_data, function(k,v){
					select_options_two += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
				})
				$("#bin_two").html("");
				$("#bin_two").append(select_options_two);
				*/
				// auto populated group name
				nurseryGroup(farm_id,$("#bin_one").val());

				enableCreate()
				//saveSelecMenuSelection();
			}
		});

	}

	function groupNameTwo(farm_id,bin_id,bin_id_two){
		disableCreate()
		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':farm_id,'bin_id':bin_id,'bin_id_two':bin_id_two},
			type	:	"GET",
			success	: 	function(r){
				$("#group_name").html("");
				$("#group_name").val(r);
				enableCreate()
			}
		})

	}


	function clearSelectMenuSelection(){

		setTimeout(function(){
			$.ajax({
				url		:	app_url+"/clearSelectedBins",
				type 	:	"GET",
				success: function(r){
					saveSelecMenuSelection();
				}
			});
		},1000);

	}

	function getSelectMenuSelection(){
		//$(".btn-create-group").attr('disabled',true);
		setTimeout(function(){
			bin_one = $("#bin_one")[0].selectedIndex;
			bin_two = $("#bin_two")[0].selectedIndex;

			$.ajax({
				url		:	app_url+"/getSelectedBins",
				data	:	{'bin_one' : bin_one, 'bin_two' : bin_two},
				type 	:	"POST",
				success: function(r){
					console.log(r);
					$("#bin_one").prop('selectedIndex',r.bin_one);
					$("#bin_two").prop('selectedIndex',r.bin_two);
					$(".btn-create-group").attr('disabled',false);
				}
			});
		},1000);
	}

	function saveSelecMenuSelection(){

		setTimeout(function(){
			bin_one = $("#bin_one")[0].selectedIndex;
			bin_two = $("#bin_two")[0].selectedIndex;

			$.ajax({
				url		:	app_url+"/saveSelectedBins",
				data	:	{'bin_one' : bin_one, 'bin_two' : bin_two},
				type 	:	"POST",
				success: function(r){

				}
			});
		},500);

	}



	/*
	*	Disable the create nursery and add farrowing group buttons
	*/
	function disableCreate(){
		$(".btn-add-farrowing").attr("disabled",true);
		$(".btn-create-group").attr("disabled",true);
	}

	/*
	*	Enable the create nursery and add farrowing group buttons
	*/
	function enableCreate(){
		$(".btn-add-farrowing").attr("disabled",false);
		$(".btn-create-group").attr("disabled",false);
	}



	var counter_catcher = [];
	/*
	*	Create the nursery group
	*/
	$(".container").delegate(".btn-create-group","click",function(e) {

		counter_catcher = [];

		farrow_groups = $(".farrow-groups").length;

		  var values = $(".bins_menu").serializeArray();

			var start_weight = parseFloat($("#start_weight").val());
			var end_weight = parseFloat($("#end_weight").val());
		/*if(farrow_groups == 0){
			alert("Please add atleast one farrowing group");
			counter_catcher.push("fail");
		} else{
			counter_catcher = [];
		}*/
		//if($("#start_weight").val() == "" || start_weight == 0){
		//	alert("Please enter start weight");
		//} else if($("#end_weight").val() == "" || end_weight == 0){
		//	alert("Please enter end weight");
		//}  else
		if(start_weight < 0){
			alert("The start weight should not be negative");
		} else if(end_weight < 0){
			alert("The end weight should not be negative");
		//} else if(start_weight > end_weight){
			//alert("Start weight should not be greater than the end weight.");
		} else {

			// check exists
			$.ajax({
				url		:	app_url+"/checkExistsNursery",
				data	: 	{'group_name':$("#group_name").val(),'bins':values},
				type	:	"POST",
				success	: 	function(r){
					if(r == 1){
						alert("Nursery group name existed");
						counter_catcher.push("fail");
					}  else if(r == "duplicate bins") {
						alert("Bins with same values are not allowed");
						counter_catcher.push("fail");
					} else {
						$("#nursery_form").submit();
					}
				}
			});

		}

		setTimeout(function(){
			if(counter_catcher == ""){
				//success

				emptyPending();
				clearSelectMenuSelection();
			}
		},1500);
    });



});
</script>

@stop
