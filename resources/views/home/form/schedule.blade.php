<!-- on save schedule save the data to the create load -->
<div class="col-md-12 schedule-form-stick">

	<div class="col-md-12" id="search_bar" style="padding: 5px 12px;">
		<form class="horizontal-form" role="search" onsubmit="return false;">
		<div class="input-group input-group-sm col-md-12">
			<input type="text" class="form-control" autocomplete="off" placeholder="Search Farms" id="search_farm" name="q" style="border-bottom-left-radius: 0px;">
			<div class="input-group-btn">
				<button class="btn btn-success btn-search-farm" type="button" style="border-bottom-right-radius: 0px;"><i class="glyphicon glyphicon-search"></i></button>
			</div>
		</div>
		</form>
	</div>
	
{!! Form::open(['id'=>'schedForm']) !!}
<div class="panel panel-body panel-stick">
    <div class="row sched-form-top-fix-stick" style="z-index:9; top:0;">
    	<div class="col-md-12 bg-primary schedule-form-top">
        	<div class="col-md-2 text-center schedule-form-header-top">Farm</div>
            <div class="col-md-2 text-center schedule-form-header-top">Bin</div>
            <div class="col-md-2 text-center schedule-form-header-top">Feed Type</div>
            <div class="col-md-2 text-center schedule-form-header-top">Medication</div>
            <div class="col-md-1 text-center schedule-form-header-top">Amount</div>
            <div class="col-md-1 text-center schedule-form-header-top">Date</div>
            <div class="col-md-1 text-center schedule-form-header-top">Truck</div>
            <div class="col-md-1 text-center schedule-form-header-top">Driver</div>
        </div>
        <div class="col-md-12 schedule-form-top bg-info">
        	<div class="col-md-2 schedule-form-top">
            	{!! Form::hidden('farmId',NULL,['class'=>'farmId','form'=>'schedForm']) !!}
                {!! Form::hidden('binId',NULL,['class'=>'binId','form'=>'schedForm']) !!}
            	{!! Form::select('farmName',$farms_list,NULL,['class'=>'farmName form-control form-inline input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-2 schedule-form-top">
            	{!! Form::select('binNumber',$bins_list,NULL,['class'=>'binNumber form-control form-inline input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-2 schedule-form-top">
            	{!! Form::select('feed_type',$feeds,NULL,['class'=>'feedTypeId form-control form-inline input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-2 schedule-form-top">
            	{!! Form::select('medication',$medication,NULL,['class'=>'medicationId form-control form-inline input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-1 schedule-form-top">
            	{!! Form::select('amount',array_merge(['' => '0 Ton'], $amount[0]),NULL,['class'=>'feedAmount form-control form-inline input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-1 schedule-form-top">
            	<div class="col-md-12 schedule-form-top">
                	{!! Form::text('date_sched',date('M d'),['class'=>'dateSched form-control form-inline input-sm','id'=>'datepickerHome','form'=>'schedForm']) !!}
                </div>
                <div class="col-md-6 schedule-form-top" style="display:none">
                	 <select name="time_of_the_day" class="time_of_the_day form-control form-inline input-sm" title="Time of the day for the deliveries.">
                            <option value="">-</option>
                            <option value="AM">AM</option>
                            <option value="PM" selected>PM</option>
                      </select>
                </div>
            </div>
            <div class="col-md-1 schedule-form-top">
            	{!! Form::select('trucks',$trucks,NULL,['class'=>'truckId form-control input-sm','form'=>'schedForm']) !!}
            </div>
            <div class="col-md-1 schedule-form-top">
            	{!! Form::select('driver',$driver,NULL,['class'=>'driverId form-control input-sm','form'=>'schedForm']) !!}
            </div>
        </div>
        {!! Form::button('Add Batch',['id'=>'btn-save-sched', 'class'=>'btn-xs btn-info pull-right','style'=>'border-top-left-radius: 0px; border-top-right-radius: 0px;']) !!}
    </div>
</div>  
{!! Form::close() !!}  
</div>

<!-- Save Sschedule Modal -->
<div class="modal-margin modal fade" id="schedModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <div class="input-group modalMessage">
            	  
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- update pending batch Modal -->
<div class="modal-margin modal fade" id="editPendingBatchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content pending-update-holder">
    </div>
  </div>
</div>

<!-- delete pending batch Modal -->
<div class="modal-margin modal fade" id="delPendingBatchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <div class="input-group">
            	Are you sure you want to delete this batch?	  
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn-delete-batch btn btn-danger" data-dismiss="modal">Delete Batch</button>
      </div>
    </div>
  </div>
</div>


<!-- Save batch Modal -->
<div class="modal-margin modal fade" id="scheduleDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <div class="input-group">
            	Batch saved to scheduled deliveries, do you want to view scheduled delivery list now?	  
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-save-batch-no btn btn-default" data-dismiss="modal">No</button>
        <button type="button" class="btn-save-batch btn btn-primary" data-dismiss="modal">Yes</button>
      </div>
    </div>
  </div>
</div>