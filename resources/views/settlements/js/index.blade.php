<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {

  loadFarms();

  function loadFarms()
  {

    var data = {
      'testing':'testing'
    }

    //var drop_down_one = "<option value='none'>Please Select</option>";
    //var drop_down_two = "<option value='none'>Please Select</option>";
    var drop_down = "<option value='none'>Please Select</option>";
    /*
    $.ajax({
      'url'     : app_url+"/loadfinishers",
      'type'    : "GET",
      'data'    : data,
      'success' : function(r){
        //console.log(r);
        $.each(r.farm_list_one, function(k,v){
          //console.log(v.name);
            drop_down_one += "<option value='"+v.id+"'>"+v.name+"</option>";
        });
        $('#farm_view_report_1').html("");
        $('#farm_view_report_1').html(drop_down_one);

        $.each(r.farm_list_two, function(k,v){
          //console.log(v.name);
            drop_down_two += "<option value='"+v.id+"'>"+v.name+"</option>";
        });
        $('#farm_view_report_2').html("");
        $('#farm_view_report_2').html(drop_down_two);

      }
    });
    */
    $.ajax({
      'url'     : app_url+"/loadfinisherfarms",
      'type'    : "GET",
      'success' : function(r){
        //console.log(r);
        $.each(r, function(k,v){
          //console.log(v.name);
            drop_down += "<option value='"+v.id+"'>"+v.name+"</option>";
        });
        $('#farm_view_report_1').html("");
        $('#farm_view_report_1').html(drop_down);
        $('#farm_view_report_2').html("");
        $('#farm_view_report_2').html(drop_down);
        $('.farm_process').html("");
        $('.farm_process').html(drop_down);
      }
    });

  }

  function loadGroups(farm_id,element)
  {
    $('#'+element).html("");
    $('#'+element).html("<option>Loading groups...<option>");
    $('#'+element).attr("disabled","true");
    data = {'farm_id':farm_id}
    var drop_down_groups = "";
    if(element != "group_process"){
      drop_down_groups = "<option value='none'>Please select</option>";
    }else{
      drop_down_groups = drop_down_groups;
    }
    $.ajax({
      'url'     : app_url+"/loadfinishergroups",
      'type'    : "GET",
      'data'    : data,
      'success' : function(r){
        $.each(r,function(k,v){
          drop_down_groups += "<option value='"+v.group_name+"'>"+v.group_name+"</option>";
        });
        $('#'+element).html("");
        $('#'+element).attr("disabled",false);
        $('#'+element).html(drop_down_groups);
      }
    });
  }

  $('#farm_view_report_1').change(function(){
    if($(this).val() == "none"){
      $("#group_view_report_1").html("");
    } else {
      loadGroups($(this).val(),'group_view_report_1');
    }
  });

  $('#farm_view_report_2').change(function(){
    if($(this).val() == "none"){
      $("#group_view_report_2").html("");
    } else {
      loadGroups($(this).val(),'group_view_report_2');
    }
  });

  $('#group_view_report_1').change(function(){
    if($(this).val() == $("#group_view_report_2").val()){
      alert("Group already selected")
      $(this).prop("selectedIndex",0);
    }
  });

  $('#group_view_report_2').change(function(){
    if($(this).val() == $("#group_view_report_1").val()){
      alert("Group already selected")
      $(this).prop("selectedIndex",0);
    }
  });

  $('.farm_process').change(function(){
    if($(this).val() == "none"){
      $("#group_process").html("");
    } else {
      loadGroups($(this).val(),'group_process');
    }
  });

	var file;

	$("#settlement_file").change(function(e) {

		file = e.target.files;

    });


	$(".btn-process-settlement").click(function(e){


		e.stopPropagation(); // Stop stuff happening
		e.preventDefault(); // Totally stop stuff happening

		if($("#settlement_file").val() == ""){

			$('.modalMessage').html("");
			$('.modalMessage').html('Please click "Choose File" and select the settlement file.');
			$("#alertModal").modal();

			return false;

		//} else if($("#group_process").val() == ""){

			//$('.modalMessage').html("");
			//$('.modalMessage').html('Please enter group number.');
			//$("#alertModal").modal();

			//return false;

		} else {

			// hide the form
			$(".holder-settlement-form").fadeOut(function(){

				// START A LOADING SPINNER HERE
				$(".settlement-content").html("");
				$(".settlement-content").html("<div class='loading-stick-circle'><img src='/css/images/loader-stick.gif' /></div>");

			});

		}


		// Create a formdata object and add the files
		var data = new FormData();
		$.each(file, function(key, value)
		{
			data.append(key, value);
			console.log(data)
		});

		data.append(1, $(".group_process").val());

		$.ajax({
			url	:	app_url+'/settlementsupload',
			data: 	data,
			type:	"POST",
			processData: false, // Don't process the files
      contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(r){
				if(r.output > 0){
					$('.modalMessage').html("");
					$('.modalMessage').html(r.message);
					$("#alertModal").modal();
					$(".settlement-content").html("");
					$(".holder-settlement-form").show();
				} else {
					$(".settlement-content").html("");
					$(".settlement-content").html(r);
					$(".holder-settlement-form").show();
					$("#settlement_file").val("");
					$("#group_process").val("")
				}
			}
		});

	});


	// begin date 1 date picker
	$("#begin_date_1").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'mm-dd-yy',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// begin date 1 date picker
	$("#begin_date_1").change(function(){

		var date_selected = $(this).val();

		//initData(date_selected);

	});



	// begin date 2 date picker
	$("#begin_date_2").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'mm-dd-yy',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// begin date 2 date picker
	$("#begin_date_2").change(function(){

		var date_selected = $(this).val();

		//initData(date_selected);

	});



	// end date 1 date picker
	$("#end_date_1").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'mm-dd-yy',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// end date 1 date picker
	$("#end_date_1").change(function(){

		var date_selected = $(this).val();

		//initData(date_selected);

	});



	// end date 2 date picker
	$("#end_date_2").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'mm-dd-yy',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	// end date 2 date picker
	$("#end_date_2").change(function(){

		var date_selected = $(this).val();

		//initData(date_selected);

	});


	// btn-view-report
	$(".btn-view-report").click(function(){

		var data = {
				'farm_vr_1'		   : 	$('#farm_view_report_1').val(),
				'farm_vr_2'		   :	$('#farm_view_report_2').val(),
				'group_vr_1'	   :	$('#group_view_report_1').val(),
				'group_vr_2'	   :	$('#group_view_report_2').val(),
				'begin_date_1'	 :	$('#begin_date_1').val(),
				'begin_date_2'   :	$('#begin_date_2').val(),
				'end_date_1'	   :	$('#end_date_1').val(),
				'end_date_2'	   :	$('#end_date_2').val()
			}

		$.ajax({
			url	:	app_url+'/settlementsearch',
			data: 	data,
			type:	"POST",
			success: function(r){

			}
		});

	})


});

</script>
