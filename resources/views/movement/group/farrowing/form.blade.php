<!-- Modal -->
<div class="modal fade" id="editFarrowing{{$v['group_id']}}" tabindex="-1" role="dialog" aria-labelledby="editFarrowing">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Farrowing Group</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" action="{{url('/savefarrowing')}}" method="post" id="farrowing_form">
          <div class="form-group">
            <label class="col-sm-4 control-label">Group</label>
            <div class="col-sm-5">
              <input type="text" class="form-control group_name_{{$v['group_id']}}" name="group_name" id="group_name" group-id="{{$v['group_id']}}" value="{{$v['group_name']}}">
              <input type="hidden" class="form-control group_name_previous_{{$v['group_id']}}" name="group_name_previous" id="group_name_previous" group-id="{{$v['group_id']}}" value="{{$v['group_name']}}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword" class="col-sm-4 control-label">Farrowing Farm</label>
            <div class="col-sm-5">
              <select name="farrowing" class="form-control nursery_{{$v['group_id']}}" id="nursery" group-id="{{$v['group_id']}}" bin-id="">
              	<option value="{{$v['farm_id']}}">{{$v['name']}}</option>
              </select>
            </div>
          </div>
          @forelse($v['bin_data'] as $bin)
          <div class="form-group">
            <label for="inputPassword" class="col-sm-4 control-label">Bin</label>
            <div class="col-sm-5">
              <select name="bin" class="form-control bin-{{$bin['id']}}" id="bin" group-id="{{$v['group_id']}}" farm-id="{{$v['farm_id']}}">
                <option value=""></option>  
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword" class="col-sm-4 control-label">Pigs</label>
            <div class="col-sm-5">
              <input name="number_of_pigs" type="number" class="form-control number_of_pigs_{{$bin['id']}}" id="pigs" value="{{$bin['number_of_pigs']}}">
            </div>
          </div>
          @empty
          @endforelse
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-save-edited" group-id="{{$v['group_id']}}">Save changes</button>
      </div>
    </div>
  </div>
</div>