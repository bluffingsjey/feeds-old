<!-- Modal -->
<div class="modal fade" id="editFarrowing{{$v['group_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Animal Group</h4>
      </div>
      <div class="modal-body">

        	<form class="form-horizontal" method="post" id="farrowing_form">

			  <div class="form-group">
				<label class="col-sm-4 control-label">Group</label>
				<div class="col-sm-5">
				  <input type="text" class="form-control group_name_{{$v['group_id']}}" name="group_name" id="group_name_{{$v['group_id']}}" value="{{$v['group_name']}}">
				  <input type="hidden" class="form-control" name="unique_id" id="unique_id_{{$v['group_id']}}" value="{{$v['unique_id']}}">
				</div>
			  </div>

			  <div class="form-group">
				<label for="inputPassword" class="col-sm-4 control-label">Date</label>
				<div class="col-sm-5">
				  <input name="date_created" type="text" class="form-control datepickerSchedTool date_created_{{$v['group_id']}}" value="{{date('M d, Y',strtotime($v['date_created']))}}">
				</div>
			  </div>

        <div class="form-group">
				<label for="inputPassword" class="col-sm-4 control-label">Start Weight (lbs)</label>
				<div class="col-sm-5">
				  <input name="start_weight" type="number" min="0" class="form-control start_weight_{{$v['group_id']}} negative" value="{{$v['start_weight']}}">
				</div>
			  </div>

        <div class="form-group">
				<label for="inputPassword" class="col-sm-4 control-label">End Weight (lbs)</label>
				<div class="col-sm-5">
				  <input name="end_weight" type="number" min="0" class="form-control end_weight_{{$v['group_id']}} negative" value="{{$v['end_weight']}}">
				</div>
			  </div>

        <div class="form-group">
          <label class="col-sm-4 control-label">Crates</label>
          <div class="col-sm-5">
            <input name="crates" type="number" min="0" class="form-control negative" id="crates_{{$v['group_id']}}" value="{{$v['crates']}}">
          </div>
        </div>

			  <div class="form-group">
				<label for="inputPassword" class="col-sm-4 control-label">Farrowing Farms</label>
				<div class="col-sm-5">
				  <select name="farrowing" class="form-control" id="farrowing{{$v['group_id']}}">

				  </select>
				</div>
			  </div>


			  @forelse($v['bin_data'] as $key => $val)
				 <div class="bins_holder_{{$val['id']}} bins{{$v['group_id']}}"></div>
			  @empty

			  @endforelse

			  <div class="bins_holder_edit_{{$v['group_id']}}"></div>

			</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-save-changes-{{$v['group_id']}}">Save changes</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript">
$(document).ready(function(e) {

	// scheduling page date picker
	$(".datepickerSchedTool").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat:  'M d, yy',
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

	farrowingFarms({{$v['farm_id']}})

	// append the farrowing farms
	function farrowingFarms(farm_id){

		$.ajax({
			url		:	app_url+"/farrowingfarms",
			data	: 	"",
			type	:	"GET",
			success	: 	function(r){

				var select_options = "";

				$.each(r, function(k,v){
					if(v.id == farm_id){
						select_options += "<option selected value='"+v.id+"'>"+v.name+"</option>";
					} else {
						select_options += "<option value='"+v.id+"'>"+v.name+"</option>";
					}
				})
				$("#farrowing"+{{$v['group_id']}}).html("");
				$("#farrowing"+{{$v['group_id']}}).append(select_options);
			}
		});

	}

	$(".bins_holder_"+{{$v['farm_id']}}).html("");

	@forelse($v['bin_data'] as $key => $val)
		loadBinsEdit({{$key}},{{$val['id']}},{{$v['farm_id']}},{{$val['bin_id']}},{{$val['number_of_pigs']}});
  @empty
	@endforelse

  $("#farrowing{{$v['group_id']}}").change(function(e) {
    id = $(this).val();
		//hide bins
    $(".bins{{$v['group_id']}}").hide();
    $(".bins{{$v['group_id']}}").remove();

		loadBins(id);
	});


	var loading = "<div style='width:250px; height:50px; margin-left:200px; padding 40px;'>";
    	loading += "<img src='/css/images/loader-stick.gif' />";
      loading += "Please wait, getting bins...";
    	loading += "</div>";

  var loading_list = "<div style='width:500px; margin-left:200px; margin:0 auto; padding-top: 40px; padding-bottom: 40px;'>";
    	loading_list += "<img src='/css/images/loader-stick.gif' />";
      loading_list += "Please wait, refreshing groups...";
    	loading_list += "</div>";

	$(".btn-save-changes-{{$v['group_id']}}").click(function(e) {

		// group name
		var group_name = $("#group_name_{{$v['group_id']}}").val();
		// date
		var date_created = $(".date_created_{{$v['group_id']}}").val();
    // start_weight
    var start_weight = parseFloat($(".start_weight_{{$v['group_id']}}").val());
    // end weight
    var end_weight = parseFloat($(".end_weight_{{$v['group_id']}}").val());
    //crates
    var crates = parseInt($("#crates_{{$v['group_id']}}").val());
		// farm
		var farrowing_farm = $("#farrowing{{$v['group_id']}}").val();
		// unique_id
		var unique_id = $("#unique_id_{{$v['group_id']}}").val();

    var bins = $(".bins_{{$v['group_id']}}").serializeArray();
    var number_of_pigs_group = $(".num_of_pigs_{{$v['group_id']}}").serializeArray();

    var f_bins_id = $('.bins_{{$v['group_id']}}').map(function() {
      return {
        value: $(this).attr('f_bins_id')
      }
    }).get();

		if($("#bin_one{{$v['group_id']}}").length == 0){
			// bin 1 with first load of page
			var bin_one = $(".bin_0_{{$v['group_id']}}").val();
			var f_bin_id_one = $(".bin_0_{{$v['group_id']}}").attr("f_bins_id");

			// bin 2 with first load of page
			if($(".bin_1_{{$v['group_id']}}").length){
				var bin_two = $(".bin_1_{{$v['group_id']}}").val();
				var f_bin_id_two = $(".bin_1_{{$v['group_id']}}").attr("f_bins_id");
			}else{
				var bin_two = "";
				var f_bin_id_two = "";
			}

		}else{
			// bin 1 with changed farm population
			var bin_one = $("#bin_one{{$v['group_id']}}").val();
			// bin 2 with changed farm population
			var bin_two = $("#bin_two{{$v['group_id']}}").val();
		}

		// pigs
		var number_of_pigs = $("#pigs_{{$v['group_id']}}").val();

		var animal_group = {
			'group_name'		:	group_name,
			'date_created'		:	date_created,
      'start_weight'    : start_weight,
      'end_weight'      : end_weight,
      'crates'          : crates,
			'farrowing_farm'	: 	farrowing_farm,
			'unique_id'			  :	unique_id,
			'bin_one'			    :	bin_one,
			'f_bin_id_one'		:	f_bin_id_one,
			'bin_two'			    :	bin_two,
			'f_bin_id_two'		:	f_bin_id_two,
			'number_of_pigs'	:	number_of_pigs,
      'bins'            : bins,
      'number_of_pigs_group'  : number_of_pigs_group,
      'f_bins_id' : f_bins_id
		}

    if(start_weight < 0){
			alert("The start weight should not be negative");
		} else if(end_weight < 0){
			alert("The end weight should not be negative");
		} else if(crates < 0){
			alert("The crates should not be negative");
		//} else if(crates == 0){
			//alert("The crates should not be zero");
		} else {
        updateFarrowing(animal_group);
    }

  });

	function updateFarrowing(animal_group){

		if($("#pigs").val() == ""){
			alert("Please enter the pigs.");
		} else if($("#group_name").val() == ""){
			alert("Please enter the group name.");
		} else {
			// check exists
			$.ajax({
				url		:	app_url+"/updatefarrowing",
				data	: 	animal_group,
				type	:	"POST",
				success	: 	function(r){
					if(r == 'success'){
						$('#editFarrowing{{$v['group_id']}}').modal('hide');

            setTimeout(function(){
              $(".farrowing-list").html("");
              $(".farrowing-list").append(loading_list);
            },500);

						window.location.reload();
					} else if(r == "duplicate bins") {
            alert("Bins with same values are not allowed");
          } else {

          }
				}
			});

		}

	}

	function loadBinsEdit(key,id,farm_id,bin_id,number_of_pigs){

		$.ajax({
			'url'	:	app_url+"/farrowingbins",
			data	: 	{'farm_id':farm_id},
			type	:	"GET",
			success	: 	function(r){
				var bin_data = r;

				var select_options = "<div class='form-group'>";
					select_options += "<label for='inputPassword' class='col-sm-6 text-center'>Bin </label>";
          select_options += "<label for='inputPassword' class='col-sm-6 text-center'> Number of Pigs</label>";
					select_options += "<div class='col-sm-6'>";
					select_options += "<select name='bins' class='form-control bin_"+key+"_{{$v['group_id']}} bins_{{$v['group_id']}}' id='bin_"+id+"' f_bins_id='"+id+"'>";

					$.each(bin_data, function(k,v){
						if(v.bin_id == bin_id){
							select_options += "<option selected value='"+v.bin_id+"'>"+v.bin_number+"</option>";
						} else {
							select_options += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
						}
					});

					select_options += "</select>";
					select_options += "</div>";
          select_options += "<div class='col-sm-6'>";
          select_options += "<input name='num_of_pigs[]' type='number' min='0' class='form-control num_of_pigs_{{$v['group_id']}} num_of_pigs_"+id+" negative' value='"+number_of_pigs+"'/>";
          select_options += "</div>";
					select_options += "</div>";


				$(".bins_holder_"+id).append(select_options);

			}
		});
	}

	function loadBins(id){
    $(".bins_holder_edit_{{$v['group_id']}}").html("");
    $(".bins_holder_edit_{{$v['group_id']}}").append(loading);

		$.ajax({
			url	  :	app_url+"/farrowingbins",
			data	: {'farm_id':id},
			type	:	"GET",
			success	: 	function(r){

				var bin_data = r;

        var bins_select_element = "";
        $.each(bin_data, function(key,val){
          var bin_counter = key + 1;
              bins_select_element += "<div class='form-group'>";
              bins_select_element += "<label for='inputPassword' class='col-sm-6 text-center'>Bin "+bin_counter+"</label>";
              bins_select_element += "<label for='inputPassword' class='col-sm-6 text-center'> Number of Pigs</label>";
              bins_select_element += "<div class='col-sm-6'>";
              bins_select_element += "<select name='bin_one' class='form-control bins bins_{{$v['group_id']}}'>";
              bins_select_element += "<option value='none-"+key+"' selected>none</option>";
              $.each(bin_data, function(k,v){
                bins_select_element += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
              })
              bins_select_element += "</select>";
              bins_select_element += "</div>";
              bins_select_element += "<div class='col-sm-6'>";
              bins_select_element += "<input name='num_of_pigs[]' type='number' min='0' class='form-control num_of_pigs_{{$v['group_id']}} num_of_pigs_"+id+" negative' value='0'/>";
              bins_select_element += "</div>";
              bins_select_element += "</div>";
        })


				var select_menu = bins_select_element//select_one + select_two;
					$(".bins_holder_edit_{{$v['group_id']}}").html("");
					$(".bins_holder_edit_{{$v['group_id']}}").append(select_menu);

				// auto populated group name
				groupName(id,$("#bin_one{{$v['group_id']}}").val());

			}
		});

	}

	function groupName(farm_id,bin_id){
		//$(".btn-create-group").attr('disabled',true);
		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':farm_id,'bin_id':bin_id},
			type	:	"GET",
			success	: 	function(r){
				$(".group_name_{{$v['group_id']}}").html("");
				$(".group_name_{{$v['group_id']}}").html(r);
				$("#group_name_{{$v['group_id']}}").val(r);
				//$(".btn-create-group").attr('disabled',false);
			}
		})

	}

	function groupNameTwo(farm_id,bin_id,bin_id_two){
		//$(".btn-create-group").attr('disabled',true);
		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':farm_id,'bin_id':bin_id,'bin_id_two':bin_id_two},
			type	:	"GET",
			success	: 	function(r){
				$(".group_name_{{$v['group_id']}}").html("");
				$(".group_name_{{$v['group_id']}}").html(r);
				$("#group_name_{{$v['group_id']}}").val(r);
				//$(".btn-create-group").attr('disabled',false);
			}
		})

	}

	function checkExists(group_name){
		var result = 0;
		$.ajax({
			'url'	:	app_url+"/checkexists",
			data	: 	{'group_name':group_name},
			type	:	"GET",
			success	: 	function(r){
				result = r;
				return result;
			}
		})
	}


});
</script>
