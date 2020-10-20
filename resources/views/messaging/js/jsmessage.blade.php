

<script type="text/javascript">



function messageLoader(person){

	$.ajax({
		url	:	app_url+'/loadMessage',
		type: "POST",
		data: {'person':person},
		success: function(r){
			//console.log(r);
			$(".msg-message-list").html("");
			$(".msg-message-list").html(r);
		}
	});

}

// load the message history
function loadMessageHistory(user){

	$.ajax({
		url    	:	app_url+"/msghistory",
		data	:	{'user':user},
		method 	:	"POST",
		success	: function(r){
			$(".msg-for-{{$login_id}}-"+user).html("");
			$(".msg-for-{{$login_id}}-"+user).html(r);
		}
	})

}

// get the notification of messages per contact
function mobileAwaker(user_from,user_to){

	$.ajax({
		url		:	app_url+"/messagingnoti",
		data	:	{'wakeup':'true','from':user_from,'to':user_to},
		method	:	"GET",
		success: function(r){
			console.log(r);
		}
	})

}

// get the notification of messages per contact
function loadNotiPerContact(user_id){

	$.ajax({
		url		:	app_url+"/notiperson",
		data	:	{'user_id':user_id},
		method	:	"POST",
		success: function(r){
			if(r != 0){
				var badge = '<span class="badge badge-contact-"'+user_id+'>'+r+'</span>';
				$(".msg-noti-contact-"+user_id).html("");
				$(".msg-noti-contact-"+user_id).html(badge);
			}
		}
	})

}


$(document).ready(function(e) {

	autoScrollMessages(0);

	$(".messages_log_holder").scroll(function(){
		window.clearInterval(0);
	})

	// click event for all contacts
	@foreach ($users as $k => $v)

		loadNotiPerContact({{$v['id']}});

	@endforeach


	$(".input-message").click(function(){

		var user_id = $(this).attr("person");
		updateNotificationStatus(user_id);

	});

	// load the messages
	loadMessageHistory({{$person[0]['id']}});

	//var host = 'ws://37.221.175.118:9540';
	//var socket = null;


	function messageTemplate(messagesent){
		var message = '<div class="media msg-media" tabindex="-1" style="padding:2px"><div class="media-body" style="padding-left: 20px;"><strong class="media-heading">'+messagesent.username+'</strong><br/><span class="pull-left msg-span">'+ messagesent.msg +'</span><span class="pull-right msg-time">'+messagesent.time+'</span></div></div>';
		//$(".msg-for-"+messagesent.user).append(message);
		$(".msg-for-"+messagesent.user_from+"-"+messagesent.user_to).append(message);
	}

	function messageTemplateSending(messagesent){
		var message = '<div class="media msg-media" tabindex="-1" style="padding:2px"><div class="media-body" style="padding-left: 20px;"><strong class="media-heading">'+messagesent.username+'</strong><br/><span class="pull-left msg-span">'+ messagesent.msg +'</span><span class="pull-right msg-time msg-sending">Sending...</span></div></div>';
		//$(".msg-for-"+messagesent.user).append(message);
		$(".msg-for-"+messagesent.user_from+"-"+messagesent.user_to).append(message);
	}


    //Manges the keyup event
    $(".container").delegate('.input-message','keyup',function(evt){
		if (13 === evt.keyCode) {


            var msg = $(this).val();
            if (msg != "" || msg != null){

				message = {
						'msg'		:	msg,
						'user_from'	: 	{{$login_id}},
						'user_to'	:	{{$person[0]['id']}},
						'username'	: 	"{{$login_username}}",
						'time'		:	"{{date('g:i A')}}"
					};

				mobileAwaker({{$login_id}},{{$person[0]['id']}});

				messageTemplateSending(message);

				socket.send(JSON.stringify(message))



				$(this).val("");
			}
		}

	});

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

			// restrict this message to specific user
			console.log(message)
			if(message.msg != ":seen:"){
				if(message.user_to == {{$login_id}}){
					// the baloon notification
					show_stack_bottomleft('info',message.msg,message.username);
					// fire the notification updates
					notificationLoader(message.user_from);
					// notification per contact
					loadNotiPerContact(message.user_from);
					var div = $(".messages_log_holder");
					div.scrollTop(div.prop('scrollHeight'));

					setTimeout(function(){
						$(".msg-sending").html("");
						$(".msg-sending").html(message['time']);
					},500)
				}
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
		//alert("Chat feature not supported by this browser, please use Google Chrome...");
	}
});

</script>
