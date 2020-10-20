var loading_animal_groups = "<div class='loading-animal-groups text-center' style='width:300px; margin:0 auto;'>";
		loading_animal_groups += "Please wait, loading data...";
		loading_animal_groups += "</div>";

function show_stack_bottomleft(type,msg,user) {
	if(msg.length > 60){
		msg = msg.substring(0,60)+"...";
	}

	var opts = {
		title: "Over Here",
		text: "Check me out. I'm in a different stack.",
		addclass: "stack-bottomleft"
	};
	switch (type) {
	case 'error':
		opts.title = "Oh No";
		opts.text = "Watch out for that water tower!";
		opts.type = "error";
		break;
	case 'info':
		opts.title = "New Message";
		opts.text = user+": "+msg;
		opts.type = "info";
		break;
	case 'success':
		opts.title = "Good News Everyone";
		opts.text = "I've invented a device that bites shiny metal asses.";
		opts.type = "success";
		break;
	}
	new PNotify(opts);
}

$(".loading-stick-circle").fadeOut(100,function() {

	$(".forecasting-display").slideDown(100);
	$(".panel-kb").append(loading_style);

});


/*
*	Notication loader
*/
function notificationLoader(user_id){

	$.ajax({
		url    :	app_url+"/msgnotification",
		method :"GET",
		success: function(r){
			// list item
			$(".msg-notifications").html("");
			$(".msg-notifications").html(r);

		}
	})
	$.ajax({
		url    :	app_url+"/notificationtotal",
		method :"GET",
		success: function(r){
			$(".badge-header").remove();
			if(r > 0){
				$(".msg-noti-badge").prepend("<span class='badge badge-header' style='padding:2px 4px; background-color: #5CB85C;'>"+r+"</span>");
			}
		}
	})

}

// update notification status
function updateNotificationStatus(user_id){
	$.ajax({
		url	: app_url+"/updatenotification",
		data: {'user_id':user_id},
		method	: "post",
		success: function(r){
			console.log("Notification status changed!");
			$(".msg-noti-contact-"+user_id).html("");
			notificationLoader();
		}
	})

}

function updateNotificationStatusAll(){
	$.ajax({
		url	: app_url+"/updatenotification",
		data: {'unique_id':''},
		method	: "post",
		success: function(r){
			console.log("All notification status changed!");
		}
	})
}

// always scroll down the message logs
function autoScrollMessages(value){

	var timesRun = value;
	if(value == null || value == ""){
		timesRun = 0;
	}
	var interval = setInterval(function(){
		timesRun += 1;
		if(timesRun === 10){
			clearInterval(interval);
		}
		//do whatever here..
		var div = $(".messages_log_holder");
	  	div.scrollTop(div.prop('scrollHeight'));
	}, 500);

}

function graphReload(bin_id,num_of_pigs){

	$.ajax({
		url		:	app_url+"/graphreload",
		type	:	"POST",
		data	:	data={'bin_id':bin_id, 'num_of_pigs':num_of_pigs},
		success: function(r){
			$(".container").append(r);
			setTimeout(function(){
				drawChart();
			},500);
		}
	});

}

$(document).ready(function() {

	$(".container").on("keyup",".negative",function(){
		var sanitized = $(this).val().replace(/(.)-+/g, '$1');//replace(/[^0-9]/g, '');
		//sanitized = sanitized.replace(/(.)-+/g, '$1');
	  // Update value
	  $(this).val(sanitized);

	})

	var loggedin_user = null;
	$.ajax({
		url:	app_url+"/loginuser",
		method:"GET",
		success: function(r){
			loggedin_user = r;
		}
	})

	notificationLoader();

	function messageTemplate(messagesent){
		var message = '<div class="media msg-media" tabindex="-1" style="padding:2px"><div class="media-body" style="padding-left: 20px;"><strong class="media-heading">'+messagesent.username+'</strong><br/><span class="pull-left msg-span">'+ messagesent.msg +'</span><span class="pull-right msg-time">'+messagesent.time+'</span></div></div>';
		$(".msg-for-"+messagesent.user_to+"-"+messagesent.user_from).append(message);
		//$(".msg-for-"+messagesent.admin).append(message);
	}


	try {


		socket = new WebSocket(host);

		//Manages the open event within your client code
		socket.onopen = function () {
		   // print('Click a person to message...');
			//$(".input-message").focus();
			console.log("connection open");
			return;
		};
		//Manages the message event within your client code
		socket.onmessage = function (msg) {

			console.log(msg.data)
			var message = JSON.parse(msg.data);

			if(message.msg != ":seen:"){

				// restrict this message to specific user
				messageTemplate(message);

				if(loggedin_user == message.user_to){

					// the baloon notification
					show_stack_bottomleft('info',message.msg,message.username);

					// fire the notification updates
					notificationLoader();

				}
				autoScrollMessages(0);

				setTimeout(function(){
					$(".msg-sending").html("");
					$(".msg-sending").html(message['time']);
				},500)

			}

			return;
		};
		//Manages the close event within your client code
		socket.onclose = function () {
			console.log('Connection Closed');
			//return;
		};


	} catch (e) {
		console.log(e);
		//if(alert("Something went wrong on the messaging, please click ok to refresh the page...")){
			//window.location.reload();
		//}
	}


	try {
		home_socket = new WebSocket(home_host);

		//Manages the open event within your client code
		home_socket.onopen = function () {
			console.log("forecasting connection open");
		};

		//Manages the message event within your client code
		home_socket.onmessage = function (msg) {

		};

		//Manages the close event within your client code
		home_socket.onclose = function () {
			console.log('forecasting Connection Closed');
			return;
		};

	} catch (e) {
		console.log(e);
	}


	$('#flash-overlay-modal').modal();

	$(".container").delegate('.pend_show_info','click',function() {

		var u = $(this).attr("unique");

		$("#pendbtns"+u).slideUp();
		$("#pend_showmore"+u).slideDown();
		$("#pend_hidebtn"+u).show();

	});

	$(".container").delegate('.hide_pend','click',function() {

		var u = $(this).attr("unique");

		$("#pendbtns"+u).slideDown();
		$("#pend_showmore"+u).slideUp();
		$("#pend_hidebtn"+u).hide();

	});

	$('.undone').click(function(){
		alert("Under Construction.");
	});

	//$(".numeric").numeric(false, function() { alert("Numbers only"); this.value = ""; this.focus(); });

   $("#btn-step1").hide();

   $("#datepicker").datepicker({
		controlType: 'select',
		oneLine: true,
		minDate:0
	});

	// forecasting page dtae picker
	$("#datepickerHome").datepicker({
		controlType: 'select',
		oneLine: true,
		minDate:0,
		dateFormat: 'M d'
	});

	$("#btn-step1").click(function(){

		var data = {
				'dateSched':	$("#datepicker").val(),
				'_token': 		$("input[name=_token]").val()
			};

		$.ajax({
			url: "ajax",
			type: "post",
			data: data,
			success: function(data){
				console.log(data);
			}
		});

	});

	$(".farms-list").on("select2:select", function () {

		var data = {
				'farm_id' : $(this).val(),
				'_token'  : $("input[name=_token]").val()
			};

		$.ajax({
			url	: "selectbins",
			type: "post",
			data: data,
			success: function(r){



				$(".bins-holder").text("");


				if(r[0].binsTotal <= 0) {
					$(".bins-holder").append('No Bins found for this farm, <a href="addbins/'+data['farm_id']+'">Add Bins for this Farm?</a>');
				} else if(r[0].binsTotal == "default"){
					$(".bins-holder").text("");
				} else {
					$(".bins-holder").append("Total Bins for this farm: " + r[0].binsTotal);
				}
			}
		});

	});

	$(".btn-assignrole").click(function(){

		var data = {
				'role' 	 : 		$(".roles-list").val(),
				'user_id' :		$("input[name=userid]").val(),
				'_token' : 		$("input[name=_token]").val()
			};

		var redirect_url = app_url + "/users";

		$.ajax({
			url: app_url + "/users/addRoleUpdate",
			type: "post",
			data: data,
			success: function(data){
				if(data == "success") {
					window.location.href = redirect_url;
				} else {
					console.log("Error");
				}
			}
		});

	});

	/*
	$(".color-picker").spectrum({
		preferredFormat: "hex6",
		showPaletteOnly: true,
		showPalette:true,
		hideAfterPaletteSelect: true,
		color: 'blanchedalmond',
		palette: [
			["#0f0","#f00","#00f","#ff0"]
		]
	});


	$(".btn-hex").click(function(){
		alert($(".color-picker").spectrum('get').toHexString());
	})
	*/

	$(".btn-addbins").click(function(){
		var data = {
				'farm_id'			:	$("input[name=farm_id]").val(),
				'number_of_pigs'	:	$(".num_of_pigs").val(),
				'consumption'		:	$(".consumption").val(),
				'variance'			:	$(".variance").val(),
				'color'				:	$(".color-picker").spectrum('get').toHexString(),
				'_token'			:	$("input[name=_token]").val()
			};
		var redirect_url = app_url + "/scheduling/step2";
		$.ajax({
			url: app_url + "scheduling/addbinsajax",
			type: "POST",
			data: data,
			success: function(r){
				if(r == "success") {
					window.location.href = redirect_url;
				} else {
					console.log("Error");
				}
			}
		})

	});

	$(".compartments").keyup(function(e){
		var sum = 0;
		var capacity = Number($(".totalCapacity").text());
		var totalCapacity = 0;
		var totalCapacityAdded = 0;

			setTimeout(function(){

				$(".compartments").each(function(){
					sum += Number($(this).val());
				});

				$(".totalCapacityAdded").text("");
				$(".totalCapacityAdded").text(sum);

				totalCapacityAdded = $(".totalCapacityAdded").text();

			},2000);

	});



	$('#create_farm').keypress( function( e ) {
	  var code = e.keyCode || e.which;

	  if( code === 13 ) {
		e.preventDefault();
		return false;
	  }
	});


	$(".btn-addcom").click(function(){
		var data = {
				'truck_id'	 		: 		$("input[name=truckId]").val(),
				'compartmentTotal' 	:		$("input[name=compartmentTotal]").val(),
				'truck_capacity'	:		$("input[name=truck_capacity]").val(),
				'_token' 			: 		$("input[name=_token]").val()
			};

		var number = data['compartmentTotal'];
		for(var i = 1; i <= number; i++){
			data['compartment_'+i] = $('#com_'+i).val();
		}

		$.ajax({
			url: app_url + '/truck/storeCompartments',
			type: "POST",
			data: data,
			success: function(r){
				var result = r.result;
				if(result == 'Fail') {
					$(".comTotalCap").text("");
					$(".comTotalCap").append(r.value);
					$(".comMessage").text("");
					$(".comMessage").append(r.message);
					$("#validationModal").modal('show');
				} else {
					window.location.href = app_url+"/truck";
				}
			}
		})

	});


});
