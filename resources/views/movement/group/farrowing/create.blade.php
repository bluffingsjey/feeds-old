@extends('app')


@section('content')

<style type="text/css">
</style>
<div class="col-md-10">

<div class="row">
    <div class="col-sm-6 col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>Create Farrowing Group</strong>
                <a href="{{url('/farrowing')}}" class="btn btn-warning btn-xs pull-right" role="button">Back</a>
            </div>
            <div class="panel-body holder">
            	<div class="col-md-offset-2 col-md-8">
                    <form class="form-horizontal" action="{{url('/savefarrowing')}}" method="post" id="farrowing_form">

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Group</label>
                        <div class="col-sm-5">
                          <!--<p class="form-control-static group_name"></p>-->
                          <input type="text" class="form-control" name="group_name" id="group_name">
                          <input type="hidden" class="form-control" name="unique_id" id="unique_id" value="{{$unique_id}}">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-5">
                          <!--<p class="form-control-static group_name"></p>-->
                          <input name="date_created" type="text" class="form-control datepickerSchedTool" value="{{date("M d, Y")}}">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Start Weight (lbs)</label>
                        <div class="col-sm-5">
                          <input name="start_weight" type="number" min="0" class="form-control negative" id="start_weight" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">End Weight (lbs)</label>
                        <div class="col-sm-5">
                          <input name="end_weight" type="number" min="0" class="form-control negative" id="end_weight" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-4 control-label">Crates</label>
                        <div class="col-sm-5">
                          <input name="crates" type="number" min="0" class="form-control negative" id="crates" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Farrowing Farm</label>
                        <div class="col-sm-5">
                          <select name="farrowing" class="form-control" id="nursery">
                          	@forelse($farrowing as $k => $v)
                              <option value="{{$v['id']}}">{{$v['name']}}</option>
                            @empty
                              <option>none</option>
                            @endforelse
                          </select>
                        </div>
                      </div>


                      <div class="bins_holder">
                        <!--
                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Bin One</label>
                        <div class="col-sm-5">
                          <select name="bin_one" class="form-control" id="bin">

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

                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Bin Three</label>
                        <div class="col-sm-5">
                          <select name="bin_two" class="form-control" id="bin_three">

                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Bin Four</label>
                        <div class="col-sm-5">
                          <select name="bin_two" class="form-control" id="bin_four">

                          </select>
                        </div>
                      </div>
                    -->
                    </div>
                      <!--
                      <div class="form-group">
                        <label for="inputPassword" class="col-sm-4 control-label">Pigs</label>
                        <div class="col-sm-5">
                          <input name="number_of_pigs_one" type="number" class="form-control" id="pigs">
                        </div>
                      </div>
                    -->
                      <hr/>

                      <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-10">
                          <button type="button" class="btn btn-success btn-create-group">Create</button>
                        </div>
                      </div>

                    </form>
                </div>
            </div>
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

  loadBins($("#nursery").val());

	clearSelectMenuSelection();

  $("#nursery").change(function(e) {
  	   id = $(this).val();
	     loadBins(id);
	     clearSelectMenuSelection();
	});

	$("#bin").change(function(e) {
    	bin_id = $(this).val();
		farm_id = $("#nursery").val();

		if(bin_id == $("#bin_two").val()) {
			alert("bin already selected");
			getSelectMenuSelection();
		} else {
			saveSelecMenuSelection()
			if($("#bin_two").val() == ""){
				//groupName(farm_id,bin_id);
			}else{
				//groupNameTwo(farm_id,bin_id,$("#bin_two").val())
			}
		}

		console.log();

	});

	$("#bin_two").change(function(e) {

		bin_id = $(this).val();
		farm_id = $("#nursery").val();

		/*if($(this).val() == ""){
			$("#pigs_two").val("");
		}*/

		if(bin_id == $("#bin").val()) {
			alert("bin already selected");
			getSelectMenuSelection();
		} else {
			saveSelecMenuSelection()
			//groupNameTwo(farm_id,$("#bin").val(),bin_id)
		}

	});

	var loading = "<div class='loading-stick-circle'>";
    	loading += "<img src='/css/images/loader-stick.gif' />";
      loading += "Please wait, Rendering...";
    	loading += "</div>";

	$(".btn-create-group").click(function(e) {

		if($("#bin_two").val() == ""){

			saveFarrowing();

		} else {

			if($("#pigs").val() == ""){
				alert("Please enter pigs")
			} else {
				// save
				saveFarrowing();
			}

		}

    });

	function saveFarrowing(){

    start_weight = parseFloat($("#start_weight").val());
    end_weight = parseFloat($("#end_weight").val());
    crates = parseInt($("#crates").val());
    console.log(start_weight);
    console.log(end_weight);

		if($("#pigs").val() == ""){
			alert("Please enter the pigs.");
		} else if($("#group_name").val() == ""){
			alert("Please enter the group name.");
		//} else if($("#start_weight").val() == "" || $("#start_weight").val() == 0){
		//	alert("Please enter the start weight.");
		//} else if($("#end_weight").val() == "" || $("#end_weight").val() == 0){
    //  alert("Please enter the end weight.");
    } else if(start_weight < 0){
			alert("The start weight should not be negative");
		} else if(end_weight < 0){
			alert("The end weight should not be negative");
		} else if(crates < 0){
			alert("The crates should not be negative");
		//} else if(crates == 0){
			//alert("The crates should not be zero");
		}
    else {
      var values = $(".bins_menu").serializeArray();
      var number_of_pigs = $(".num_of_pigs").serializeArray();

      // check exists
			$.ajax({
				url		:	app_url+"/checkexists",
				data	: 	{
                    'group_name'    : $("#group_name").val(),
                    'bins'          : values,
                    'num_of_pigs'   : number_of_pigs
                  },
				type	:	"GET",
				success	: 	function(r){
					if(r == 1){
						$("#farrowing_form").submit();
          } else if(r == "duplicate bins") {
            alert("Bins with same values are not allowed");
          } else {
						alert("Group already existed");
					}
				}
			});

		}

	}

	function loadBins(id){

		$.ajax({
			'url'	:	app_url+"/farrowingbins",
			data	: 	{'farm_id':id},
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
              bins += "<label for='inputPassword' class='col-sm-6 text-center'>Bin "+bin_number+":</label>";
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
				$("#bin").html("");
				$("#bin").append(select_options);

				var select_options_two = "<option value=''>none</option>";
				$.each(bin_data, function(k,v){
					select_options_two += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
				})
				$("#bin_two").html("");
				$("#bin_two").append(select_options_two);
        */
				// auto populated group name
				groupName(id,$("#bin-0").val());

			}
		});
	}

	function groupName(id,bin_id){
		$(".btn-create-group").attr('disabled',true);
		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':id,'bin_id':bin_id},
			type	:	"GET",
			success	: 	function(r){
				$(".group_name").html("");
				$(".group_name").html(r);
				$("#group_name").val(r);
				$(".btn-create-group").attr('disabled',false);
			}
		})

	}

	function groupNameTwo(farm_id,bin_id,bin_id_two){
		$(".btn-create-group").attr('disabled',true);
		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':farm_id,'bin_id':bin_id,'bin_id_two':bin_id_two},
			type	:	"GET",
			success	: 	function(r){
				$(".group_name").html("");
				$(".group_name").html(r);
				$("#group_name").val(r);
				$(".btn-create-group").attr('disabled',false);
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
		$(".btn-create-group").attr('disabled',true);
		setTimeout(function(){

			bin_one = $("#bin")[0].selectedIndex;
			bin_two = $("#bin_two")[0].selectedIndex;

			$.ajax({
				url		:	app_url+"/getSelectedBins",
				data	:	{'bin_one' : bin_one, 'bin_two' : bin_two},
				type 	:	"POST",
				success: function(r){
					console.log(r);
					$("#bin").prop('selectedIndex',r.bin_one);
					$("#bin_two").prop('selectedIndex',r.bin_two);
					$(".btn-create-group").attr('disabled',false);
				}
			});

		},1000);
	}

	function saveSelecMenuSelection(){

		setTimeout(function(){

			bin_one = $("#bin")[0].selectedIndex;
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

});
</script>

@stop
