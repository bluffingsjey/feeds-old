<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
</div>
<div class="modal-body form-horizontal">
	<div class="alert-holder"></div>
	<div class="form-group">
    {!! Form::label('feed_type', 'Feed Type',['class'=>'col-sm-3 control-label']) !!}
    <div class="col-sm-8">
      {!! Form::select('feed_type',$feeds,array($feed_id,$feeds_selected),['class'=>'form-control feed_type_'.$delivery_id,'form'=>'schedForm']) !!}
    </div>
    </div>
    <div class="form-group">
        {!! Form::label('medication', 'Medication',['class'=>'col-sm-3 control-label']) !!}
        <div class="col-sm-8">
         {!! Form::select('medication',$medication,array($medication_id,$medication_selected),['class'=>'medicationId form-control form-inline medication_'.$delivery_id,'form'=>'schedForm']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('amount', 'Amount',['class'=>'col-sm-3 control-label']) !!}
        <div class="col-sm-8">
          {!! Form::select('amount',$amount,array($amount_selected,$amount_selected),['class'=>'feedAmount form-control form-inline amount_'.$delivery_id,'form'=>'schedForm']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="button" class="btn-update-batch btn btn-primary" del-id="{{$delivery_id}}">Update Batch</button>
</div>

