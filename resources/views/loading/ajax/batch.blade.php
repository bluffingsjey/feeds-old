@forelse($schedData as $sched)
<div class="table-load-kb-view col-md-12 col-lg-12">
    <div class="col-md-2">
    {!! Form::select('farm_name',$farmsLists, array($sched['farm_id'],$sched['farm_name']), ["placeholder"=>"#","class"=>"farm-lists form-control col-md-12 input-sm form-inline", "disabled" => "true", "id" => "farm-name-".$sched['schedule_id'], "sched-id" => $sched['schedule_id']]) !!}
    </div>
    <div class="col-md-2">
    {!! Form::text('ticket',$sched['ticket'], ["placeholder"=>"#","class"=>"tickets form-control col-md-12 input-sm form-inline", "disabled" => "true", "id" => "sched-ticket-".$sched['schedule_id'], 'sched-id'=>$sched['schedule_id'],'bin-number'=>'']) !!}
    </div>
    <div class="col-md-2">
    {!! Form::select('feed_name',$feedType, array($sched['feeds_type_id'],$sched['feed_name']),["placeholder"=>"#","class"=>"feed_name form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "feed-name-".$sched['schedule_id']]) !!}
    </div>
    <div class="col-md-2">
    {!! Form::select('medication',$medication, array($sched['medication_id'],$sched['medication_name']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "medication-".$sched['schedule_id']])!!}
    </div>
    <div class="col-md-1">
    {!! Form::select('amount',$amount, array($sched['amount'],$sched['amount']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "amount-".$sched['schedule_id']]) !!}
    </div>
    <div class="col-md-1">
    {!! Form::select('bins',$ctrl->binsNumber($sched['farm_id']),array($sched['bin_id'],$sched['bin_number']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "bin-".$sched['schedule_id']]) !!}
    </div>
    <div class="col-md-2 kb-load-btns">
        <div class="col-md-12 col-lg-12 savebtn" sched-id="{{$sched['schedule_id']}}" unique-id="{{$sched['unique_id']}}" id="savebtn-{{$sched['schedule_id']}}" style="display:none">
            Save
        </div>
        <div class="col-md-6 col-lg-6 editbtnkb" sched-id="{{$sched['schedule_id']}}" id="editbtnkb-{{$sched['schedule_id']}}">
            Edit
        </div>
        <div class="col-md-6 col-lg-6 delbtnkb" sched-id="{{$sched['schedule_id']}}" unique-id="{{$sched['unique_id']}}" id="delbtnkb-{{$sched['schedule_id']}}">
            Delete
        </div>
    </div>
</div>
@empty
<div class="row load-table">
    <div class="col-md-4">No schedule yet</div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
</div>
@endforelse

<!--Add Batch Row-->
{!! Form::hidden('date_of_delivery',!empty($schedData[0]['date_of_delivery']) ? $schedData[0]['date_of_delivery'] : 0,['class'=>'ab-date-of-delivery']) !!}
{!! Form::hidden('driver_id',!empty($schedData[0]['driver_id']) ? $schedData[0]['driver_id'] : 0,['class'=>'ab-driver-id']) !!}
{!! Form::hidden('unique_id',!empty($schedData[0]['unique_id']) ? $schedData[0]['unique_id'] : 0,['class'=>'ab-unique-id']) !!}
{!! Form::hidden('truck_id',!empty($schedData[0]['truck_id']) ? $schedData[0]['truck_id'] : 0, ['class'=>'ab-truck']) !!}
<div class="table-load-kb-view col-md-12 col-lg-12 add-batch-view">
    <div class="col-md-2 my_add_batch_kb">        
        {!! Form::select('farms[]',$farmsLists,NULL,['class'=>'ab-farm-lists kb_sel_not-disable']) !!}    
    </div>
    <div class="col-md-2">        
        <input placeholder="#" class="ab-ticket form-control col-md-12 input-sm form-inline" name="" type="text" value="">    
    </div>
    <div class="col-md-2">    
        {!! Form::select('feed_types[]',$feedType,NULL,["class"=>"ab-feed-types form-inline kb_sel_not-disable"]) !!}    
    </div>
    <div class="col-md-2">    
        {!! Form::select('medications[]',$medication,NULL,["class"=>"ab-medications form-inline kb_sel_not-disable"]) !!}    
    </div>
    <div class="col-md-1">    
        {!! Form::select('amount[]', array(''=>'-')+$amount[0], NULL,["class"=>"ab-amount form-inline kb_sel_not-disable"]) !!}    
    </div>
    <div class="col-md-1">    
        {!! Form::select('bins',array(''=>'-')+$ctrl->binsNumber($schedData[0]['farm_id']),NULL,["placeholder"=>"#","class"=>"ab-bins form-control col-md-12 input-sm form-inline kb_sel_not-disable"]) !!}        
    </div>
    <div class="col-md-2 kb-load-btns">        
        <div class="col-md-12 col-lg-12 addbatchbtnkb">
            Add Batch +
        </div>    
    </div>
</div>
<!--End Add Batch Row-->