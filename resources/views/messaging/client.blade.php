 @extends('app')
@section('content')
<style type="text/css">

.msg-panel {
	min-height: 705px;
	padding-top: 2px;
}
.msg-contact-list {
	padding: 0px;
	height: 713px;
	overflow-y: auto;
}

.msg-message-list {
	padding: 0px;
}

.msg_list {
	margin-bottom: 1px !important;	
	border-radius: 0px !important;
	cursor: pointer;
	background-color: #FFFFFF;
}
.msg_list:hover {
	background-color: #e3e3e3;	
}

.msg-name-header {
	height: 29px;
	background-color: #808080;
}

.msg-name-header strong {
	vertical-align: sub;
	color: #FFF;
}

.msg-search-contacts {
	border-radius: 0px !important;
}

.input-message {
	border-radius: 0px !important;
}

.messages-logs {
	border-radius: 0px !important;	
}

.msg-span {
	background: #DDD;
    padding: 5px;
    border-radius: 5px;
	max-width: 570px;
}

.msg-media {
	margin-top: 0px !important;
}

.msg-time {
	font-size: 11px;
	padding-right: 10px;
}
.msg-person-link{
	text-decoration: none;
}
.active-msg {
	background: #DDD;
}
</style>

<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h1 class="panel-title">Messaging</h1>
        </div>
        
        <div class="panel-body msg-panel" style="padding-bottom:0px;">
        	<div class="row ">
            	<div class="col-xs-3 msg-contact-list">
                	<!--<div class="">
                		<input type="text" class="form-control input-sm msg-search-contacts" value="" placeholder="Search"/>
                	</div>-->
                    @foreach ($users as $k => $v)
                    <a class="msg-person-link" href="/msg/{{$v['id']}}">
                        <div class="msg_list well well-sm msg-person-{{$v['id']}} {{($v['id'] == $person[0]['id']) ? 'active-msg' : ''}}" person="{{$v['id']}}">
                            <div>
                                <strong>{{$v['username']}}</strong>
                                <span class="pull-right text-muted msg-noti-contact-{{$v['id']}}"></span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="col-xs-9 msg-message-list">
                    <div class="msg-name-header text-center"><strong>{{$person[0]['username']}}</strong></div>
                    <div class="messages_log_holder msg-for-{{$login_id}}-{{$person[0]['id']}}" id="messages_log_holder_id" style="height:611px; overflow-y: auto; margin-bottom: 0px;"></div>
                    <textarea class="form-control input-message" rows="3" placeholder="Message…" person="{{$person[0]['id']}}"></textarea>
                </div>
            </div>
        </div>
        
        <div class="modal-holder"></div>
         
    </div> 
</div>

@include('messaging.js.jsmessage')

@stop