<!-- Update Number of Pigs Modal -->
<div class="modal fade" id="delivery-modal{{$data->unique_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body form-horizontal">
        <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">

                <div>

                    <h1 class="panel-title title-top-kb">Load Information</h1>

                </div>
                <div class="col-md-12 col-lg-12 load-header-kb">

                    <div class="col-md-3">Date</div>
                    <div class="col-md-3">Truck</div>
                    <div class="col-md-3">Delivery Time</div>
                    <div class="col-md-3">Driver</div>

                </div>

                <div class="col-md-12 col-lg-12 mine-kb-load">
                	<div class="col-md-3">{{date('M d, A',strtotime($ctrl->getDeliveries($data->unique_id)[0]['delivery_date']))}}</div>
                    <div class="col-md-3">{{$ctrl->getDeliveriesTruck($ctrl->getDeliveries($data->unique_id)[0]['truck_id'])}}</div>
                    <div class="col-md-3">{{date('H:i A',strtotime($ctrl->getDeliveries($data->unique_id)[0]['delivery_date']))}}</div>
                    <div class="col-md-3">{{$ctrl->getDeliveriesDriver($ctrl->getDeliveries($data->unique_id)[0]['driver_id'])}}</div>
                </div>

                <div>
                    <h1 class="panel-title title-top-kb">Load Breakdown </h1>
                </div>

                <div class="col-md-12 col-lg-12 load-header-kb">
                    <div class="col-md-2"><strong>Farm</strong></div>
                    <div class="col-md-2"><strong>Feed Type</strong></div>
                    <div class="col-md-2"><strong>Medication</strong></div>
                    <div class="col-md-1"><strong>Amount</strong></div>
                    <div class="col-md-2"><strong>Bins</strong></div>
                    <div class="col-md-2"><strong>L-Out Bin</strong></div>
                    <div class="col-md-1"><strong>Compt #</strong></div>
                </div>

                <div class="fd_clear"></div>

                <div class="table-load-kb-view col-md-12 col-lg-12">
                	@foreach ($ctrl->getDeliveries($data->unique_id) as $k => $v)
                	<div class="col-md-2">{{$ctrl->getDeliveriesFarmName($v['farm_id'])}}</div>
                    <div class="col-md-2">{{$ctrl->getDeliveriesFeedType($v['feeds_type_id'])}}</div>
                    <div class="col-md-2">{{$ctrl->getDeliveriesMedication($v['medication_id'])}}</div>
                    <div class="col-md-1">{{$v['amount']}} Ton/s</div>
                    <div class="col-md-2">{{$ctrl->getDeliveriesSpecificBinName($v['bin_id'])}}</div>
                    <div class="col-md-2">{{rtrim($v['load_out_bin'],",")}}</div>
                    <div class="col-md-1">{{$v['compartment_number']}}</div>
                    @endforeach
                </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
