@foreach($notifications as $k => $v)

<li {{$v['status'] == 1 ? "style=background:#DDD" : ""}} @if($msgcrtl->msgNotiLoader($v['unique_id'])['user_id'] == "") style="display:none" @endif >
    <a href="/msg/{{$msgcrtl->msgNotiLoader($v['unique_id'])['user_id']}}" unique_id="{{$v['unique_id']}}" class="msg-noti-{{$v['unique_id']}}">
        <div>
            <strong>{{$msgcrtl->msgNotiLoader($v['unique_id'])['name'] }}</strong>
            <span class="pull-right text-muted">
                <em>{{$msgcrtl->msgNotiLoader($v['unique_id'])['time'] }}</em>
            </span>
        </div>
        <div>{{$msgcrtl->msgNotiLoader($v['unique_id'])['msg'] }}</div>
    </a>
</li>
<li class="divider"  @if($msgcrtl->msgNotiLoader($v['unique_id'])['user_id'] == "") style="display:none" @else style="margin: 3px 0;" @endif ></li>

@endforeach

<li model="{{$user = \App\User::select('id')->where('id','!=',Auth::user()->id)->orderBy('username')->take(1)->get()}}">
    <a class="read-all" href="/msg/{{$user[0]['id']}}">
        <strong>Read All Messages</strong>
        <i class="fa fa-angle-right"></i>
    </a>
</li>

<script type="text/javascript">


$(document).ready(function(e) {
    @foreach($notifications as $k => $v)

	$(".msg-noti-{{$v['unique_id']}}").click(function(){
		// update the status of notification
		/*unique_id = $(this).attr("unique_id");
		updateNotificationStatus(unique_id);
		var href = $(this).attr('href');
		setTimeout(function() {window.location = href}, 500);
        return false;*/
	})

	@endforeach

	$(".read-all").click(function(){
		// update the status of notification
		/*updateNotificationStatusAll();
		var href = $(this).attr('href');
		setTimeout(function() {window.location = href}, 500);
        return false;*/
	});

});
</script>
