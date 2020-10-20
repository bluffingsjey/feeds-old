<!-- Update Number of Pigs Modal -->
<div class="modal fade" id="bin-modal{{$bin['bin_id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Bin Level</h4>
      </div>
      <div class="modal-body form-horizontal">
        <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
                 {!! Form::select('binsAmount',$bin['bin_s'],array($bin['default_amount']),['class'=>'form-control ddslickme','id'=>'amountOfBins'.$bin['bin_id']]) !!}
            </div>
           
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary updateBin"  bin-number="{{$bin['bin_id']}}" pigs="{{$bin['num_of_pigs']}} data-dismiss="modal">Save changes</button>
      </div>
    </div>
  </div>
</div>
