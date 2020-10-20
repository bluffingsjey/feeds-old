<!-- Update Number of Pigs Modal -->
<div class="modal fade" id="pigs-modal{{$bin['bin_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">UPDATE NUMBER OF PIGS</h4>
      </div>
      <div class="modal-body">
      	<div class="form-group">
      	@if($bin['default_val'] != NULL)
            <div class="input-group">
              <div class="col-sm-12"><h2 class="text-center text-primary">Bin #{{$bin['bin_number']}} - {{$bin['alias']}}</h2></div>
              <hr/>
              <label class="col-sm-6 control-label text-center" for="exampleInputAmount" >Group ID:</label>
              <label class="col-sm-6 control-label text-left" for="exampleInputAmount" >Number of Pigs:</label>
              <input type="hidden" value="{{$total_pigs = 0}}" />
              @foreach($bin['default_val'] as $k => $v)
                @if($v['group_name'] != NULL)
                  <input type="hidden" value="{{$total_pigs = $total_pigs + $v['number_of_pigs']}}" />
                  <div class="col-sm-offset-2 col-sm-4">
                  	<p class="text-primary">{{$v['group_name']}}</p>
                  </div>
                  <div class="col-sm-4">
                  	<input type="number" name="number_of_pigs[]" class="form-control input-sm numpigsupdate{{$bin['bin_id']}}" id="numberOfPigs{{$bin['bin_id']}}-{{$v['unique_id']}}" value="{{$v['number_of_pigs']}}" placeholder="Number of Pigs" animal-unique-id="{{$v['unique_id']}}">
                  </div>
                @endif
              @endforeach
           </div>
           <br/>
			<label class="col-sm-offset-4 col-sm-6 control-label text-right" for="exampleInputAmount" >Total Number of Pigs: <span class="total-pigs{{$bin['bin_id']}}">{{$total_pigs}}</span></label>

        @else
        	<div class="input-group">
              <div class="col-sm-12">
              	<h2 class="text-center text-primary">No Group yet...</h2>
              	<p class="text-info"><a href="{{ url('/animalgroup') }}">Create Group?</a></p>
              </div>
            </div>
        @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        @if($bin['default_val'] != NULL)
        <button type="button" class="btn btn-success updatePig" bin-number="{{$bin['bin_id']}}" farm-id="{{$bin['default_val'][0]['farm_id']}}" animal-unique-id="" data-dismiss="modal">Save changes</button>
        @endif
      </div>
    </div>
  </div>
</div>
