<div class="col-md-9 col-lg-9 summleft_k" style="min-height: 148px;">
    <div class="summaryheader_kb">+ Summary</div>
    
    <div class="summ_batches_disp">
        
        @forelse($schedData as $sched)
        <div class="col-md-4 col-lg-4 summinfoin">
            
            <div class="batch_bin_color_summ col-md-3 col-lg-3"></div>
            <div class="batch_bin_info_summ col-md-9 col-lg-9">
                <p>{{$sched['farm_name']}}</p>
                <p><span class="summ_farm_amount" sched-id="{{$sched['schedule_id']}}" id="summ_farm_amount-{{$sched['schedule_id']}}">0</span> out of <span class="summ_farm_amount_base_{{$sched['schedule_id']}}">{{$sched['amount']}}</span> loaded</p>
            </div>
        
        </div>
        @empty
        
        @endforelse
        
    </div>
    
</div>
<div class="col-md-3 col-lg-3 summ_kb_ton">

<p class="avail_ton_summ">{{$totalTons}}</p>
<p>more tons needed to be loaded</p>
</div>