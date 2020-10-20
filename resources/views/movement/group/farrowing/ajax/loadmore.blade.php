@forelse($farrow_data as $v)

    <div class="col-sm-6 col-md-6 group-{{$v['group_id']}} groups">
        <div class="panel panel-info" style="min-height: 230px;">
            <div class="panel-heading">
                <h3 class="panel-title text-left group-name-text-{{$v['group_id']}}"><strong>{{$v['group_name']}}</strong>

                  <button type="button" class="btn btn-danger btn-xs pull-right btn-delete" group-id="{{$v['group_id']}}">
    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
  </button>

  <button type="button" class="btn btn-warning btn-xs pull-right btn-edit" group-id="{{$v['group_id']}}" farm-id="{{$v['farm_id']}}" bin-id="" aria-label="Left Align" data-toggle="modal" data-target="#editFarrowing{{$v['group_id']}}" style="margin-right: 2px;">
    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
  </button>

                </h3>
                <!--<p class="text-right">
                <strong>Current Pigs:</strong> <small>127</small>
                <br/>
                <strong>Days Remaining:</strong> <small>9</small>
                </p>-->
            </div>
            <div class="panel-body" style="min-height:156px; overflow:auto;">
                <div class="col-md-12">
                    <hr class="hr">

                        <dl class="dl-horizontal">
                          <dt>Created:</dt>
                          <dd>{{date("M d",strtotime($v['date_created']))}}</dd>
                          <dt>Days Remaining:</dt>
                          @if($v['date_to_transfer'] > 2)
                          <dd>{{$v['date_to_transfer'] - 2}} - {{$v['date_to_transfer']}}</dd>
                          @elseif($v['date_to_transfer'] < 0)
                          <dd>0</dd>
                          @else
                          <dd>{{$v['date_to_transfer']}}</dd>
                          @endif
                          <dt>Total Pigs:</dt>
                          <dd class="num-pigs-{{$v['group_id']}}">{{$v['total_pigs']}}</dd>
                          <dt>Start Weight:</dt>
                          <dd class="start-weight-{{$v['group_id']}}">{{$v['start_weight']}} lbs</dd>
                          <dt>End Weight:</dt>
                          <dd class="end-weight-{{$v['group_id']}}">{{$v['end_weight']}} lbs</dd>
                          <dt>Crates:</dt>
                          <dd class="crates-{{$v['group_id']}}">{{$v['crates']}}</dd>
                          <dt>Farrowing:</dt>
                          <dd class="farm-name-{{$v['group_id']}}">{{$v['name']}}</dd>
                          @forelse($v['bin_data'] as $key => $val)
                          <dt>bin:</dt>
                          <dd class="farm-name-{{$v['group_id']}}">{{$val['alias_label']}} | pigs:{{$val['number_of_pigs']}}</dd>
                          @empty
                          <dt>No bin selected...</dt>
                          @endforelse
                        </dl>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('movement.group.farrowing.edit')



@empty

@endforelse
