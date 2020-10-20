@if(Auth::user()->id == $pendingData[0]['user_id'])
<div class="feed_bl_hd">Pending Deliveries</div>
<div class="feed_tm_hd">{{$delivery_date}} - <span id="totalBatchAmount">{{$totalAmount}}</span> / <span id="totalTruckCapacity">{{$truck_capacity}}</span> Tons</div>

<div id="pend_list_kb">
    @forelse($pendingData as $pending)
	
    <div class="pend_individual"><!-- /.start batch -->
        
        <div class="info_top_kb col-md-12 col-lg-12">
            
            <div class="col-md-6 col-lg-10">
            
                <p class="farm_pend_nm">{{$pending['farm_name']}}</p>
                <p class="farm_pend_bn">{{$pending['bin_name']}}</p>
                
            </div>
            <div class="col-md-6 col-lg-2 pend_ton_disp">
            
                <p class="pend_hw">{{$pending['amount']}}</p>
                <p>tons</p>
                
            </div>
            
        </div>
        
        <div class="fd_clear"></div>
        
        <div class="info_btm_kb" id="pend_showmore{{$pending['delivery_id']}}">
            <p><strong>Feed:</strong> {{$pending['feed_name']}}</p>
            <p><strong>Medication:</strong> {{$pending['medication_name']}}</p>
        </div>
        
        <div class="fd_clear"></div>
        
    </div>
    
    <div class="col-lg-12 col-md-12 hide_pend" id="pend_hidebtn{{$pending['delivery_id']}}" unique="{{$pending['delivery_id']}}"></div>
    
    <div class="pend_btns col-md-12 col-lg-12" id="pendbtns{{$pending['delivery_id']}}">
        
        <div class="pend_btns_in col-md-4 col-lg-4 pend_show_info" unique="{{$pending['delivery_id']}}" id="pend_infobtn{{$pending['delivery_id']}}">Info</div>
        <div class="pend_edit pend_btns_in col-md-4 col-lg-4" delId="{{$pending['delivery_id']}}">Edit</div>
        <div class="pend_del pend_btns_in col-md-4 col-lg-4" delId="{{$pending['delivery_id']}}">Delete</div>
        
    </div>
    @empty
    
    @endforelse
    <div class="fd_clear"></div><!-- /.end batch -->

    <div id="pend_btn_sched" date-of-sched="{{$delivery_date}}" truck-id="{{$truck_id}}" driver-id="{{$driver_id}}">Schedule Delivery</div>

</div>
	<script>
	
	function appendPending(){
	@forelse($pendingData as $key => $pending)
	var pending_label = "<span class='has-pending pending-{{$pending['farm_id']}}'>PENDING</span>";
		
		if($(".pending-{{$pending['farm_id']}}").length == 1){
			$(".pending-{{$pending['farm_id']}}").html("");
			$(".pending-{{$pending['farm_id']}}").text("PENDING");
		} else {
			$("#farm-holder-{{$pending['farm_id']}}").append(pending_label);
		}
		
	@empty
    @endforelse
	}
	</script>
@endif