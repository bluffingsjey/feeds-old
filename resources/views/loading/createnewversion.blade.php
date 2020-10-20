@extends('app')
@section('content')
 <div class="col-md-10">
 	 <div class="panel loading-panel">
        <div class="panel-body load_to_truck_panel" style="padding:5px; margin-bottom: 200px;">

            <div>
                <h1 class="panel-title title-top-kb">Load Information</h1>
            </div>
        	<div class="col-md-12 col-lg-12 load-header-kb">

                <div class="col-md-3">Date</div>
                <div class="col-md-3">Truck</div>
                <div class="col-md-3">Delivery Time</div>
                <div class="col-md-3">Driver</div>

            </div>


        	{!!	Form::open(['action'=>'ScheduleController@compartmentLoading']) !!}
        	<div class="col-md-12 col-lg-12 mine-kb-load">
            	<div class="col-md-3">{!! Form::text('schedDateTime',date('M d, A',strtotime($date_of_sched)),['id'=>'schedDateTime','class'=>'form-control input-sm col-md-12 txt-kb','unique_id'=>$schedData[0]['unique_id']]) !!}</div>
            	<div class="col-md-3">{!! Form::text('truck_name',$truckData->name,['class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::text('delivery_time',date('H:i A',strtotime($date_of_sched)),['id'=>'delivery_time','class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::select('truck_driver',$drivers,array($schedData[0]['driver_id'],$schedData[0]['driver_name']),['class'=>'form-control driver-kb input-sm col-md-12']) !!}</div>
            </div>

            <div>
                <h1 class="panel-title title-top-kb">Load Breakdown </h1>
               	<div class="alert alert-danger" role="alert" style="display:none; padding:5px; margin-bottom:5px; border-radius:0px;">
                  To be able to continue, fill up all <strong>ticket numbers</strong> and press save ticket
                </div>
            </div>
            <div class="col-md-12 col-lg-12 load-header-kb">
            	<div class="col-md-2"><strong>Farm</strong></div>
              <div class="col-md-2"><strong>Bins</strong></div>
              <div class="col-md-2"><strong>Feed Type</strong></div>
              <div class="col-md-2"><strong>Medication</strong></div>
              <div class="col-md-1"><strong>Amount</strong></div>
              <div class="col-md-2" style="display:none"><strong>L-Out Bin</strong></div>
              <div class="col-md-3"><strong>Compt #</strong></div>
              <div class="col-md-2" style="display:none"><strong>Ticket #</strong></div>
              <div class="col-md-2" style="display:none"></div>
            </div>

            <div class="fd_clear"></div>

            <!--Merge lists with the selected lists-->
            <div class="batch_holder">
          	@forelse($schedData as $sched)

            <div class="table-load-kb-view col-md-12 col-lg-12">
                <div class="col-md-2">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
                {!! Form::select('farm_name',$farmsLists, array($sched['farm_id'],$sched['farm_name']), ["placeholder"=>"#","class"=>"farm-lists form-control col-md-12 input-sm form-inline", "id" => "farm-name-".$sched['schedule_id'], "counter"=>$i, "sched-id" => $sched['schedule_id']]) !!}
                @endfor
                </div>

                <div class="col-md-2">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
                {!! Form::select('bins',$ctrl->binsNumber($sched['farm_id']),array($sched['bin_id'],$sched['bin_name']),["placeholder"=>"#","class"=>"selected_bins selected_bins$i form-control col-md-12 input-sm form-inline", 'sched-id'=>$sched['schedule_id'], "counter"=>$i, "id" => "bin-".$sched['schedule_id'].$i]) !!}
                <!--<button class="btn btn-block btn-xs btn-info btn-add-compartment-data" sched-id="{{$sched['schedule_id']}}" unique-id="{{$sched['unique_id']}}" type="button">Add</button>-->
                @endfor
                </div>

                <div class="col-md-2">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
                {!! Form::select('feed_name',$feedType, array($sched['feeds_type_id'],$sched['feed_name']),["placeholder"=>"#","class"=>"feed-name-".$sched['schedule_id']." feed_name form-control col-md-12 input-sm form-inline", "id" => "feed-name-".$sched['schedule_id'].$i]) !!}
                @endfor
                </div>

                <div class="col-md-2">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
                {!! Form::select('medication',$medication, array($sched['medication_id'],$sched['medication_name']),["placeholder"=>"#","class"=>"medication form-control col-md-12 input-sm form-inline", "id" => "medication-".$sched['schedule_id']])!!}
                @endfor
                </div>

                <div class="col-md-1">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
                <div style="display:none"
                {{$last_amount = (($ctrl->compartmentCounter($sched['amount']) * 3) - $sched['amount']) - 3}}<br/>
                {{$last_amount = str_replace("-","",$last_amount)}}
                ></div>
                @if($i == $ctrl->compartmentCounter($sched['amount']))
                {!! Form::select('amount',$amount,$last_amount,["class"=>"form-control col-md-12 input-sm form-inline amounts", "id"=>"amount-".$sched['schedule_id']]) !!}
                @else
                {!! Form::select('amount',$amount,3.0,["class"=>"form-control col-md-12 input-sm form-inline amounts", "id"=>"amount-".$sched['schedule_id']]) !!}
                @endif
                @endfor
                </div>

                <div class="col-md-2" id="div-{{$sched['schedule_id']}}" style="display:none">

                @if(count($ctrl->selectedLoadoutBins($sched['schedule_id'])) > 1)
                	@for($i = 1; $i <= count($ctrl->selectedLoadoutBins($sched['schedule_id'])); $i++)
                	 {!! Form::select('loadoutBins',$loadoutBins,NULL,["placeholder"=>"#","id" => "loadoutBins-".$sched['schedule_id']."-".$i,'sched-id'=>$sched['schedule_id'], 'count'=>$i, 'unique-id'=>$sched['unique_id'], "class"=>"lob-".$sched['schedule_id']." loadoutBins form-control col-md-12 input-sm form-inline"]) !!}
                	@endfor
                    <!--<button class="btn btn-block btn-xs btn-info btn-addloadout" sched-id="{{$sched['schedule_id']}}" unique-id="{{$sched['unique_id']}}" type="button">Add</button>-->
                @else
                    @for($i=1; $i <= $ctrl->loadoutBinsCounter($sched['amount']); $i++)
                         {!! Form::select('loadoutBins',$loadoutBins,NULL,["placeholder"=>"#","id" => "loadoutBins-".$sched['schedule_id']."-".$i,'sched-id'=>$sched['schedule_id'], 'count'=>$i, 'unique-id'=>$sched['unique_id'], "class"=>"lob-".$sched['schedule_id']." loadoutBins form-control col-md-12 input-sm form-inline"]) !!}
                    @endfor
                    <button class="btn btn-block btn-xs btn-info btn-addloadout" sched-id="{{$sched['schedule_id']}}" unique-id="{{$sched['unique_id']}}" type="button">Add</button>
                @endif
                </div>
                <div class="col-md-3">
                @for($i=1; $i <= $ctrl->compartmentCounter($sched['amount']); $i++)
               		 {!! Form::select('truck_compts',$truck_compts,NULL,["placeholder"=>"#","id" => "truck_compts-".$sched['schedule_id']."-".$i,'sched-id'=>$sched['schedule_id'],'unique-id'=>$sched['unique_id'],"class"=>"tc-".$sched['schedule_id']." truck_compts form-control col-md-12 input-sm form-inline"]) !!}
                 @endfor
                </div>
                <div class="col-md-2" style="display:none">
                <input placeholder="#" class="tickets form-control col-md-12 input-sm form-inline" id="truck_compts-{{$sched['schedule_id']."-".$i}}" sched-id="{{$sched['schedule_id']}}" name="ticket" type="hidden" value="-">
                </div>
                <div class="col-md-2 kb-load-btns" style="display:none">
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
                <div class="col-md-1">
                	{!! Form::select('feed_types[]',$feedType,NULL,["class"=>"ab-feed-types form-inline kb_sel_not-disable"]) !!}
				</div>
                <div class="col-md-1">
                	{!! Form::select('medications[]',$medication,NULL,["class"=>"ab-medications form-inline kb_sel_not-disable"]) !!}
				</div>
                <div class="col-md-1">
					{!! Form::select('amount', $amount, NULL,["class"=>"ab-amount form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-1">
                	{!! Form::select('bins',array(''=>'-')+$ctrl->binsNumber($schedData[0]['farm_id']),NULL,["placeholder"=>"#","class"=>"ab-bins form-control col-md-12 input-sm form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-2">
                    <input placeholder="#" class="ab-ticket form-control col-md-12 input-sm form-inline" name="" type="text" value="">
                </div>
                <div class="col-md-1">
                   {!! Form::select('loadoutBins',$loadoutBins,NULL,["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-1">
                   {!! Form::select('truck_compts',$truck_compts,NULL,["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-2 kb-load-btns">
                    <div class="col-md-12 col-lg-12 addbatchbtnkb">
                    	Add Batch +
                    </div>
                </div>
            </div>
            <!--End Add Batch Row-->

            </div>
            <!--End batch_holder Div-->

            <div class="col-md-12 col-lg-12 save-ticket-holder" style="padding-left:0px; padding-right:0px;">
                <button type="button" class="col-md-12 btn btn-md btn-info btn-save-tickets" style="margin-top:5px; display:none;">Save Selection</button>
            </div>

            <!-- Start Summary -->

            <div class="col-md-12 col-lg-12 kb_rmvpadd summdiv_b" style="display:none">

            	<div class="col-md-9 col-lg-9 summleft_k" style="min-height: 148px;">
            		<div class="summaryheader_kb">+ Summary</div>

                    <div class="summ_batches_disp">

                    	@forelse($schedData as $sched)
                        <div class="col-md-4 col-lg-4 summinfoin">

                            <div class="batch_bin_color_summ col-md-3 col-lg-3"></div>
                            <div class="batch_bin_info_summ col-md-9 col-lg-9">
                        		<p><span id="ticket-holder-{{$sched['schedule_id']}}"></span></p>
                                <p>{{$sched['amount']}} Tons ({{$sched['farm_name']}})</p>
                                <p id="summ-loadoutbin-{{$sched['schedule_id']}}"></p>
                                <p id="summ-compartment-{{$sched['schedule_id']}}"></p>
                            </div>

                        </div>
                        @empty

                        @endforelse

                    </div>

                </div>
                <div class="col-md-3 col-lg-3 summ_kb_ton">

                <p class="avail_ton_summ">{{$totalTons}}</p>
                <p>tons to be loaded</p>
                </div>

            </div>

            <!-- End Summary -->

            <button type="button" class="btn-kb-succ col-md-12 col-lg-12 ">Load Truck</button>



        </div>

     </div>
 </div>

 <!-- batch Modal -->
<div class="modal-margin modal fade" id="BatchDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
      </div>
      <div class="modal-body batchMessageHolder">
      </div>
    </div>
  </div>
</div>

@include('loading.js.createnewversion')

@stop
