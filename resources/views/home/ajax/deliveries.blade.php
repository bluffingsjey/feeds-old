@foreach($deliveries as $data)
<tr class="items-{{$data->unique_id}}">
    <td class="col-md-2">{{date('M d Y a',strtotime($data->delivery_date))}}</td>
    <td class="col-md-5">{!!$ctrl->getDeliveriesBinInfo($data->unique_id)!!}</td>
    <td class="col-md-2">{{$data->truck_name}}</td>
    <td class="col-md-1">{{$data->driver}}</td>
    <td class="col-md-1">#{{substr($data->unique_id,0,7)}}</td>
    <td class="col-md-1">
        <img class="img-{{$data->unique_id}}" src="{{ asset("images/".$ctrl->deliveriesStatus($data->unique_id)."")}}.png"/>
    </td>
    <td class="col-md-1">
        @if($data->status == 0 || $data->status == 1 || $ctrl->deliveriesStatus($data->unique_id) == "delivery_ongoing_red" || $ctrl->deliveriesStatus($data->unique_id) == "delivery_ongoing_green")
        <button type="button" class="btn btn-success btn-xs btn-block btn-mark-{{$data->unique_id}}" unique_id="{{$data->unique_id}}">Mark as Completed</button>
        @endif
        <button type="button" class="btn btn-danger btn-xs btn-block btn-delete-{{$data->unique_id}}" unique_id="{{$data->unique_id}}">Delete</button>
        @include('home.form.deliveryinfo')
    </td>
</tr>
@endforeach


<script type="text/javascript">
$(document).ready(function(e) {
     @foreach($deliveries as $data)

		$(".container").delegate(".btn-mark-{{$data->unique_id}}","click",function(e) {
            var unique_id = $(this).attr("unique_id");
		    $(".img-"+unique_id).attr("src",app_url+"/css/images/loader-stick.gif");
		    $(".btn-mark-"+unique_id).hide();
		    markDelivered(unique_id);
        });

		$(".container").delegate(".btn-delete-{{$data->unique_id}}","click",function(e) {
        	var unique_id = $(this).attr("unique_id");
			deleteDelivered(unique_id)
			$(".items-"+unique_id).hide();
        });
	 @endforeach
});
</script>
