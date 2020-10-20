<div class="form-group">
    <label for="" class="col-sm-2 col-sm-offset-2 control-label">Delivery Date</label>
    <div class="col-sm-5">
      <input type="text" name="delivery_date" class="form-control" id="datepicker" title="Date of the deliveries"/>
    </div>
</div>
<div class="form-group">
    <label for="" class="col-sm-2 col-sm-offset-2 control-label">Delivery Time</label>
    <div class="col-sm-5">
      <input type="text" class="form-control" name="delivery_time" id="timepicker" title="Delivery time for the deliveries.">
    </div>
</div>
<div class="form-group">
    <label for="" class="col-sm-2 col-sm-offset-2 control-label">Time of the Day</label>
    <div class="col-sm-5">
      <select name="time_of_the_day" class="form-control" title="Time of the day for the deliveries.">
            <option value=""></option>
            <option value="am">AM</option>
            <option value="pm">PM</option>
      </select>
    </div>
</div>
<div class="form-group">
    <label for="" class="col-sm-2 col-sm-offset-2 control-label">Truck</label>
    <div class="col-sm-5">
      {!! Form::select('delivery_truck', (isset($truck) ? $truck : NULL), null, ['class' => 'form-control',"data-toggle"=>"tooltip", "data-placement"=>"right", "title"=>"Truck for the deliveries."]) !!}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-4 col-sm-5">
      <button type="submit" class="btn btn-success btn-block">Next</button>
    </div>
</div>