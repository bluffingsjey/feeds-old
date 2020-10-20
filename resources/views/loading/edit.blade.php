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
            	<div class="col-md-3">{!! Form::text('schedDateTime',date('M d, A',strtotime($created_delivery[0]['delivery_date'])),['id'=>'schedDateTime','class'=>'form-control input-sm col-md-12 txt-kb','unique_id'=>$created_delivery[0]['unique_id']]) !!}</div>
            	<div class="col-md-3">{!! Form::text('truck_name',$created_delivery[0]['truck_name'],['class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::text('delivery_time',date('H:i A',strtotime($created_delivery[0]['delivery_date'])),['id'=>'delivery_time','class'=>'form-control input-sm col-md-12 txt-kb','disabled'=>'true']) !!}</div>
                <div class="col-md-3">{!! Form::select('truck_driver',$drivers,array($created_delivery[0]['driver_id'],$created_delivery[0]['driver_name']),['class'=>'form-control driver-kb input-sm col-md-12']) !!}</div>
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
              <div class="col-md-3"><strong>Compt #</strong></div>
            </div>

            <div class="fd_clear"></div>

            <!--Merge lists with the selected lists-->
            <div class="batch_holder">
          	@forelse($created_delivery as $sched)

            <div class="table-load-kb-view col-md-12 col-lg-12">
                <div class="col-md-2">
                {!! Form::select('farm_name',$farmsLists, array($sched['farm_id'],$sched['farm_name']), ["class"=>"farm-lists form-control col-md-12 input-sm form-inline", "id" => "farm-name-".$sched['delivery_id'], "delivery-id" => $sched['delivery_id'], "unique_id"=>$sched['unique_id'], "truck_id"=>$sched['truck_id'] ]) !!}
                </div>

                <div class="col-md-2">
                {!! Form::select('bins', $ctrl->binsNumber($sched['farm_id']), array($sched['bin_id'],$sched['bin_name']),["class"=>"selected_bins form-control col-md-12 input-sm form-inline", 'sched-id'=>$sched['delivery_id'], "id" => "bin-".$sched['delivery_id']]) !!}
                </div>

                <div class="col-md-2">
                {!! Form::select('feed_name', $feedType, array($sched['feeds_type_id'],$sched['feed_name']),["class"=>"feed-name-".$sched['delivery_id']." feed_name form-control col-md-12 input-sm form-inline", "id" => "feed-name-".$sched['delivery_id'] ]) !!}
                </div>

                <div class="col-md-2">
                {!! Form::select('medication', $medication, array($sched['medication_id'],$sched['medication_name']), ["class"=>"medication form-control col-md-12 input-sm form-inline", "id" => "medication-"])!!}
                </div>

                <div class="col-md-1">
                {!! Form::select('amount',$amount,$sched['amount'], ["class"=>"form-control col-md-12 input-sm form-inline amounts", "id"=>"amount-"]) !!}
                </div>

                <div class="col-md-3">
                {!! Form::select('truck_compts', $truck_compartments, array($sched['compartment_number']=>$sched['compartment_number']), ["id" => "truck_compts-".$sched['delivery_id'], 'sched-id'=>$sched['delivery_id'], 'unique-id'=>$sched['unique_id'], "class"=>"tc-".$sched['delivery_id']." truck_compts form-control col-md-12 input-sm form-inline"]) !!}
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

            </div>
            <!--End batch_holder Div-->

            <button type="button" class="btn-kb-succ col-md-12 col-lg-12 ">Save Load</button>

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

@include("loading.js.edit")

@stop
