@foreach($messages as $k => $v)
	<div class="media msg-media" id="{{$v['unique_id']}}" style="padding:2px">
    	<div class="media-body" style="padding-left: 20px;">
        	<strong class="media-heading">{{$v['username']}}</strong><br/>
            <span class="pull-left msg-span">{{$v['message']}}</span>
						@if(date("Y-m-d", strtotime($v['posted'])) == date("Y-m-d"))
            <span class="pull-right msg-time">{{date("g:i A", strtotime($v['posted']))}}</span>
						@else
						<span class="pull-right msg-time">{{$v['posted']}}</span>
						@endif
        </div>
    </div>
@endforeach
