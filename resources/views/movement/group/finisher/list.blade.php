@extends('app')


@section('content')

<style type="text/css">
.hr {
	margin-top: 0px !important;
	margin-bottom: 3px !important;
}
.dl-horizontal dt {
	width: 130px !important;
}
.dl-horizontal dd {
	margin-left: 170px !important;
}
.edit {
	margin-right: 5px;
}
</style>

<div class="col-md-10">

<div class="row">
    <div class="col-sm-6 col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>Finisher Groups</strong>
                <a href="{{url('/createfinisher')}}" class="btn btn-warning btn-xs pull-right" role="button">Create Group</a>
            </div>
            <div class="panel-body finisher-list">

                <div class="row">


                	@forelse($finisher_data as $k => $v)
                	<div class="col-sm-6 col-md-6 group-{{$v['group_id']}}">
                        <div class="panel panel-info" style="height: 250px;">
                            <div class="panel-heading">

                                <h3 class="panel-title text-left"><strong>{{$v['group_name']}}</strong>

                                	<button type="button" class="btn btn-danger btn-xs pull-right btn-delete" finisher-id="{{$v['group_id']}}"  aria-label="Left Align"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>

                                	<button type="button" class="btn btn-warning btn-xs pull-right btn-edit" group-id="{{$v['group_id']}}" farm-id="{{$v['farm_id']}}" bin-id="" aria-label="Left Align" data-toggle="modal" data-target="#editFinisher{{$v['group_id']}}" style="margin-right: 2px;">
										<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
									</button>

                                </h3>

                            </div>
                            <div class="panel-body" style="max-height:208px; overflow:auto;">
                                <div class="col-md-12">
                                	<hr class="hr">
                                    <dl class="dl-horizontal">
										<dt>Created:</dt>
										<dd>{{date("M d",strtotime($v['date_created']))}}</dd>
										<dt>Days Remaining:</dt>
										@if($v['date_to_transfer'] > 10)
										<dd>{{round($v['date_to_transfer']) - 10}} - {{round($v['date_to_transfer'])}}</dd>
										@elseif($v['date_to_transfer'] < 0)
										<dd>0</dd>
										@else
										<dd>{{$v['date_to_transfer']}}</dd>
										@endif
										<dt>Total Pigs:</dt>
										<dd>{{$v['total_pigs']}}</dd>
										<dt>Start Weight:</dt>
										<dd class="start-weight-{{$v['group_id']}}">{{$v['start_weight']}} lbs</dd>
										<dt>End Weight:</dt>
										<dd class="end-weight-{{$v['group_id']}}">{{$v['end_weight']}} lbs</dd>
										<dt>Farm:</dt>
										<dd>{{$v['farm_name']}}</dd>
										@forelse($v['bin_data'] as $key => $val)
										<dt>bin:</dt>
										<dd>{{$val['alias_label']}} | pigs:{{$val['number_of_pigs']}}</dd>
										@empty
										<dt>No bin selected...</dt>
										@endforelse
									</dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('movement.group.finisher.edit')

                	@empty

               		<div class="col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title text-left"><strong>No Group Yet...</strong></h3>
                            </div>
                    </div>
                    @endforelse

										<div class="load-more"></div>

										<div class="col-sm-12 col-md-12 loadmore-button-holder">
												<div class="panel panel-info">
														<div class="panel-heading loading-holder">
																<button type="button" class="btn btn-default btn-block btn-loadmore" items="{{count($finisher_data)}}" counter="{{$finisher_counter}}"><strong>Load More...</strong></h3>
														</div>
												</div>
										</div>


                </div>

            </div>
        </div>
    </div>
</div>



</div>

<script type="text/javascript">
$(document).ready(function(e) {

	$(".container").delegate(".btn-loadmore","click",function(){
		var items = $(this).attr("items");
		var counter = Number(items) + 8;
		var total_groups = $(this).attr("counter");
		$(this).attr("items",counter);
		$(this).hide(function(){
			$(".loading-holder").append(loading_animal_groups);
		});
		$.ajax({
			'url'		:		app_url+"/finisherloadmore",
			data		: 	{'items':items},
			type		:		"GET",
			success	: 	function(r){
				if(r == "") {
					$(".loadmore-button-holder").hide();
				} else {
					$(".load-more").append(r);
					if(total_groups <= counter){
						$(".loadmore-button-holder").hide();
					} else {
						$(".loading-animal-groups").hide(function(){
								$(".btn-loadmore").show();
						});
					}

				}
			}
		});

	});

	$(".container").delegate(".btn-delete","click",function(e) {

		finisher_id = $(this).attr("finisher-id");

		$.ajax({
			'url'	:	app_url+"/removeGroupFinisher",
			data	: 	{'finisher_id':finisher_id},
			type	:	"POST",
			success	: 	function(r){
				window.location.reload();
				$(".group-"+finisher_id).hide();
			}
		})

    });

});
</script>

@stop
