@extends('app')
@section('content')
 <div class="col-md-10">
 	 <div class="panel">
        <div class="panel-body" style="padding:5px; margin-bottom: 200px;">
        	
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
            	<div class="col-md-3">{!! Form::text('schedDateTime',date('M d, A',strtotime($date_of_sched)),['class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
            	<div class="col-md-3">{!! Form::text('truck_name',$truckData->name,['class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::text('truck_capacity'," - ",['class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::select('truck_driver',$drivers,array($schedData[0]['driver_id'],$schedData[0]['driver_name']),['class'=>'form-control driver-kb input-sm col-md-12']) !!}</div>
            </div>
            
            <div>
                <h1 class="panel-title title-top-kb">Load Breakdown </h1> 
               	<div class="alert alert-danger" role="alert" style="padding:5px; margin-bottom:5px; border-radius:0px;">
                  To be able to continue, fill up all <strong>ticket numbers</strong> and press save ticket
                </div>
            </div>
            <div class="col-md-12 col-lg-12 load-header-kb">
            	<div class="col-md-2"><strong>Farm</strong></div>
                <div class="col-md-1"><strong>Feed Type</strong></div>
                <div class="col-md-1"><strong>Medication</strong></div>
                <div class="col-md-1"><strong>Amount</strong></div>
                <div class="col-md-1"><strong>Bins</strong></div>
                <div class="col-md-2"><strong>Ticket #</strong></div>
                <div class="col-md-1"><strong>Load Out Bin</strong></div>
                <div class="col-md-1"><strong>Compartment #</strong></div>
                <div class="col-md-2"></div>
            </div>
            
            <div class="fd_clear"></div>
          	
            <!--Merge lists with the selected lists-->
            <div class="batch_holder">
          	@forelse($schedData as $sched)
            <div class="table-load-kb-view col-md-12 col-lg-12">
                <div class="col-md-2">
                {!! Form::select('farm_name',$farmsLists, array($sched['farm_id'],$sched['farm_name']), ["placeholder"=>"#","class"=>"farm-lists form-control col-md-12 input-sm form-inline", "disabled" => "true", "id" => "farm-name-".$sched['schedule_id'], "sched-id" => $sched['schedule_id']]) !!}
                </div>
                <div class="col-md-1">
                {!! Form::select('feed_name',$feedType, array($sched['feeds_type_id'],$sched['feed_name']),["placeholder"=>"#","class"=>"feed_name form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "feed-name-".$sched['schedule_id']]) !!}
                </div>
                <div class="col-md-1">
                {!! Form::select('medication',$medication, array($sched['medication_id'],$sched['medication_name']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "medication-".$sched['schedule_id']])!!}
                </div>
                <div class="col-md-1">
                {!! Form::select('amount',$amount, array($sched['amount'],$sched['amount']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "amount-".$sched['schedule_id']]) !!}
                </div>
                <div class="col-md-1">
                {!! Form::select('bins',$ctrl->binsNumber($sched['farm_id']),array($sched['bin_id'],$sched['bin_number']),["placeholder"=>"#","class"=>"form-control col-md-12 input-sm form-inline","disabled"=>"true", "id" => "bin-".$sched['schedule_id']]) !!}
                </div>
                <div class="col-md-2">
                {!! Form::text('ticket',$sched['ticket'], ["placeholder"=>"#","class"=>"tickets form-control col-md-12 input-sm form-inline", "disabled" => "true", "autocomplete" => "off", "id" => "sched-ticket-".$sched['schedule_id'], 'sched-id'=>$sched['schedule_id'],'bin-number'=>'']) !!}
                </div>
                <div class="col-md-1">
                {!! Form::text('ticket',$sched['ticket'], ["placeholder"=>"#","class"=>"tickets form-control col-md-12 input-sm form-inline", "disabled" => "true", "autocomplete" => "off", "id" => "sched-ticket-".$sched['schedule_id'], 'sched-id'=>$sched['schedule_id'],'bin-number'=>'']) !!}
                </div>
                <div class="col-md-1">
                {!! Form::text('ticket',$sched['ticket'], ["placeholder"=>"#","class"=>"tickets form-control col-md-12 input-sm form-inline", "disabled" => "true", "autocomplete" => "off", "id" => "sched-ticket-".$sched['schedule_id'], 'sched-id'=>$sched['schedule_id'],'bin-number'=>'']) !!}
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
            <div class="col-md-12 col-lg-12 save-ticket-holder" style="padding-left:0px; padding-right:0px;">
                <button type="button" class="col-md-12 btn btn-md btn-info btn-save-tickets" style="margin-top:5px;">Load Truck</button>
            </div>
            
            <!--Add Batch Row-->
            		{!! Form::hidden('date_of_delivery',!empty($schedData[0]['date_of_delivery']) ? $schedData[0]['date_of_delivery'] : 0,['class'=>'ab-date-of-delivery']) !!}
                    {!! Form::hidden('driver_id',!empty($schedData[0]['driver_id']) ? $schedData[0]['driver_id'] : 0,['class'=>'ab-driver-id']) !!}
                    {!! Form::hidden('unique_id',!empty($schedData[0]['unique_id']) ? $schedData[0]['unique_id'] : 0,['class'=>'ab-unique-id']) !!}
                    {!! Form::hidden('truck_id',!empty($schedData[0]['truck_id']) ? $schedData[0]['truck_id'] : 0, ['class'=>'ab-truck']) !!}
            <div class="table-load-kb-view col-md-12 col-lg-12 add-batch-view" style="display:none;">
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
					{!! Form::select('amount[]', array(''=>'-')+$amount[0], NULL,["class"=>"ab-amount form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-1">
                	{!! Form::select('bins',array(''=>'-')+$ctrl->binsNumber($schedData[0]['farm_id']),NULL,["placeholder"=>"#","class"=>"ab-bins form-control col-md-12 input-sm form-inline kb_sel_not-disable"]) !!}
                </div>
                <div class="col-md-2">
                    <input placeholder="#" class="ab-ticket form-control col-md-12 input-sm form-inline" name="" type="text" value="">
                </div>
                <div class="col-md-1">
                    <input placeholder="#" class="ab-ticket form-control col-md-12 input-sm form-inline" name="" type="text" value="">
                </div>
                <div class="col-md-1">
                    <input placeholder="#" class="ab-ticket form-control col-md-12 input-sm form-inline" name="" type="text" value="">
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
            
            <!-- Start Truck Display -->
            
            <div class="fd_clear"></div>
            
            <div>
                <h1 class="panel-title title-top-kb bin_loadout_holder">Bin Loadout</h1>
            </div>
            
            
            <div class="alert alert-danger loadout-message" role="alert" style="display:none; padding:5px; margin-bottom:5px; border-radius:0px;">
              To be able to continue, select the <strong>ticket numbers</strong> on the <strong>loadout Bin</strong>
            </div>
            
            <!-- Start Summary -->
            
            <div class="col-md-12 col-lg-12 kb_rmvpadd summdiv_b bin_loadout_holder">
            
            	<div class="col-md-9 col-lg-9 summleft_k" style="min-height: 148px;">
            		<div class="summaryheader_kb">+ Summary</div>
                    
                    <div class="summ_batches_disp">
                    	
                    	@forelse($schedData as $sched)
                        <div class="col-md-4 col-lg-4 summinfoin">
                        	
                            <div class="batch_bin_color_summ col-md-3 col-lg-3"></div>
                            <div class="batch_bin_info_summ col-md-9 col-lg-9">
                        		<p>{{$sched['ticket']}} - {{$sched['amount']}} Tons</p>
                            	<p style="display:none;"><span class="summ_farm_amount" sched-id="{{$sched['schedule_id']}}" id="summ_farm_amount-{{$sched['schedule_id']}}">0</span> out of <span class="summ_farm_amount_base_{{$sched['schedule_id']}}">{{$sched['amount']}}</span> loaded</p>
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
                <p>more tons needed to be loaded</p>
                </div>
            
            </div>
            
            <!-- End Summary -->
            
            <div class="loading-stick-circle-bin-loadout">
    	
                <img src="/css/images/loader-stick.gif" />
                <small>Loading bin loadout selection...</small>
            
            </div>
            
            <div class="fd_clear kb_spacer bin_loadout_holder"></div>
            
            <!-- Start Loadout 1-6 -->
            <div class="col-md-2 col-lg-2 loadoutheader bin_loadout_holder">Loadout Bin 1-6</div>
            
            <div class="truckloadout col-md-10 col-lg-10 pull-right bin_loadout_holder">
            	
                @for($i = 0; $i <= 5; $i++)
                <div class="binloadout col-lg-2 col-md-2" style="border-top: 39px solid {{$colors[$i]}};">
                    <select name="binloadout" id="binLoadout_{{$i}}" color="$colors[$i]">
                        <option data-description="">select</option>
                        @forelse($schedData as $sched)
                            @if(!empty($sched['ticket']))
                            <option sched-id="{{$sched['schedule_id']}}" loudout-bin-number="{{$i}}">{{$sched['ticket']}}</option>
                            @endif
                        @empty
                        @endforelse
                    </select>
                </div>
                @endfor
            
            </div>
            <div class="invi_div_loudoutbin"></div>
            <!-- END Loadout 1-6 -->
            
            
            <div class="fd_clear kb_spacer bin_loadout_holder"></div>
            
            <!-- Start Loadout 7-12 -->
            <div class="col-md-2 col-lg-2 loadoutheader bin_loadout_holder">Loadout Bin 7-12</div>
            
            <div class="truckloadout col-md-10 col-lg-10 pull-right bin_loadout_holder">
            	
                @for($i = 6; $i <= 11; $i++)
                <div class="binloadout col-lg-2 col-md-2" style="border-top: 39px solid {{$colors[$i]}};">
                    <select name="binloadout" id="binLoadout_{{$i}}" color="$colors[$i]">
                        <option data-description="">select</option>
                        @forelse($schedData as $sched)
                            @if(!empty($sched['ticket']))
                            <option sched-id="{{$sched['schedule_id']}}" loudout-bin-number="{{$i}}">{{$sched['ticket']}}</option>
                            @endif
                        @empty
                        @endforelse
                    </select>
                </div>
                @endfor
            
            </div>
            <div class="invi_div_loudoutbin"></div>
            <!-- END Loadout 7-12 -->
            
            <div class="fd_clear kb_spacer truck_holder"></div>
            <div class="kb_spacer truck_holder"></div>
            
            <div class="kb_truckdesign col-md-12 col-lg-12 truck_holder">
            
            	<div class="boxes_div kb_rmvpadd">
            		
                    <div class="firstbatch_kb truckboxes col-md-12 col-lg-12 kb_rmvpadd">
                    	
                        <div class="col-md-11 col-lg-11 kb_rmvpadd">
                          
                          	@forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[0] as $comp)
                            <div class="boxes col-md-2 col-lg-2">
                            <div class="col-md-12 col-lg-12 invi_div_compartments" id="comp_disabler_{{$comp['compartment_number']}}">
                            	<button type="button" class="btn btn-warning btn-xs change_comp_selection" style="display:none">change selection</button>
                            </div>
                            	<p>Comp#{{$comp['compartment_number']}}<strong> <span class="comp_caps_{{$comp['compartment_number']}}">0</span>/3 tons</strong></p>
                                <select name="pukingina" class="box-comp"  id="box-comp-{{$comp['compartment_number']}}" comp-number="{{$comp['compartment_number']}}">
                                <option data-description="">Please Select</option>
                                @forelse($schedData as $sched)
                                	@if(!empty($sched['ticket']))
                                	<option data-description="{{$sched['farm_name']}}" amount="{{$sched['amount']}}" sched-id="{{$sched['schedule_id']}}" value="{{$sched['ticket']}}"
                                    date-of-del="{{$sched['date_of_delivery']}}"
                                    truck-id="{{$sched['truck_id']}}"
                                    farm-id="{{$sched['farm_id']}}"
                                    feeds-type-id="{{$sched['feeds_type_id']}}"
                                    medication-id="{{$sched['medication_id']}}"
                                    driver-id="{{$sched['driver_id']}}"
                                    ticket="{{$sched['ticket']}}"
                                    amount="3"
                                    bin-id="{{$sched['bin_id']}}"
                                    compartment-number="{{$comp['compartment_number']}}"
                                    >{{$sched['ticket']}}</option>
                                    @endif
                                @empty
                                @endforelse
                                </select>
                            </div>
                            
                            @empty
                			@endforelse
                        </div>
                        <div class="col-md-1 col-lg-1 arrow-truck truck1page"></div>
                        
                    </div>
                    
                    <div class="secondbatch_kb truckboxes col-md-12 col-lg-12 kb_rmvpadd">
                    	
                        <div class="col-md-1 col-lg-1 arrow-truck truck2page"></div>
                        <div class="col-md-11 col-lg-11 kb_rmvpadd">
                            @forelse($ctrl->getTruckCompartments($schedData[0]['truck_id'])[1] as $comp)
                            <div class="boxes col-md-2 col-lg-2">
                            <div class="col-md-12 col-lg-12 invi_div_compartments" id="comp_disabler_{{$comp['compartment_number']}}">
                            	<button type="button" class="btn btn-warning btn-xs change_comp_selection" style="display:none">change selection</button>
                            </div>
                            	<p>Comp#{{$comp['compartment_number']}}<strong> <span class="comp_caps_{{$comp['compartment_number']}}">0</span>/3 tons</strong></p>
                                <select name="pukingina" class="box-comp" id="box-comp-{{$comp['compartment_number']}}" comp-number="{{$comp['compartment_number']}}">
                                <option data-description="">Please Select</option>
                                @forelse($schedData as $sched)
                                	@if(!empty($sched['ticket']))
                                	<option data-description="{{$sched['farm_name']}}" data-farm="testing" amount="{{$sched['amount']}}" sched-id="{{$sched['schedule_id']}}" value="{{$sched['ticket']}}"
                                    date-of-del="{{$sched['date_of_delivery']}}"
                                    truck-id="{{$sched['truck_id']}}"
                                    farm-id="{{$sched['farm_id']}}"
                                    feeds-type-id="{{$sched['feeds_type_id']}}"
                                    medication-id="{{$sched['medication_id']}}"
                                    driver-id="{{$sched['driver_id']}}"
                                    ticket="{{$sched['ticket']}}"
                                    amount="3"
                                    bin-id="{{$sched['bin_id']}}"
                                    compartment-number="{{$comp['compartment_number']}}"
                                    >{{$sched['ticket']}}</option>
                                    @endif
                                @empty
                                @endforelse
                                </select>
                            </div>
                            
                            @empty
                			@endforelse
                        </div>
                    
                    </div>
                
                </div>
            
            </div>
            
            <div class="fd_clear truck_holder"></div>
            
            <!-- End Truck Display -->
            
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

@include('loading.js.create')

@stop