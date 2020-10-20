@extends('app')


@section('content')

<style type="text/css">
.hr {
	margin-top: 0px !important;
	margin-bottom: 3px !important;
}
.dl-horizontal dt {
	width: 120px !important;
	margin-right: 10px;
}
.dl-horizontal dd {
	margin-left: 100px !important;
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
                <strong>Farrowing Groups</strong>
								<a href="{{url('/createfarrowing')}}" class="btn btn-success btn-xs pull-right" role="button">Create Group</a>

								<select class="form-control input-sm pull-right farrowing-sort" style="display:none; height: 22px; line-height: 1; width: 50px; margin-right: 10px;">
                    <option value="1" selected>A-Z</option>
                    <option value="2">Z-A</option>
                </select>
								<span class="pull-right" style="display:none;">sort: </span>

            </div>
            <div class="panel-body farrowing-list">

                <div class="row">


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
                            <div class="panel-body" style="max-height:156px; overflow:auto;">
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
                    <div class="col-sm-6 col-md-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title text-left"><strong>No farrowing groups yet</strong></h3>
                            </div>
                        </div>
                    </div>
              @endforelse

							<div class="load-more"></div>

							<div class="col-sm-12 col-md-12 loadmore-button-holder">
									<div class="panel panel-info">
											<div class="panel-heading loading-holder">
													<button type="button" class="btn btn-default btn-block btn-loadmore" items="{{count($farrow_data)}}" counter="{{$farrow_count}}"><strong>Load More...</strong></h3>
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
			'url'		:		app_url+"/farrowingloadmore",
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

	})

	$(".container").delegate(".btn-delete","click",function(e) {

		group_id = $(this).attr("group-id");

		$.ajax({
			'url'	:	app_url+"/removegroup",
			data	: 	{'group_id':group_id},
			type	:	"POST",
			success	: 	function(r){
				window.location.reload();
				$(".group-"+group_id).hide();
			}
		})

    });

	$(".container").delegate(".btn-edit","click",function(e) {

		  var group_id = $(this).attr("group-id");
		  var farm_id = $(this).attr("farm-id");
		  var bin_id = $(this).attr("bin-id");

		  setTimeout(function(){
			loadFarrowing(farm_id,group_id)
		  	loadBins(farm_id,bin_id,"none",group_id)
		  },1000)

	});

	$(".container").delegate("#nursery","change",function(e) {

		group_id = $(this).attr("group-id");
    	farm_id = $(this).val();
		bin_id = $(this).attr("bin-id");
		loadBins(farm_id,bin_id,"yes",group_id);

		setTimeout(function(){
			bin_id = $(".bin-"+group_id).val();
			groupName(farm_id,bin_id,group_id);
		},1000)

	});

	$(".container").delegate("#bin","change",function(e) {

		group_id = $(this).attr("group-id");
    	bin_id = $(this).val();
		farm_id = $(this).attr("farm-id");

		groupName($(".nursery_"+group_id).val(),bin_id,group_id)

	});

	function loadFarrowing(id,group_id){
		$.ajax({
			'url'	:	app_url+"/farrowingfarms",
			data	: 	{'farm_id':id},
			type	:	"GET",
			success	: 	function(r){
				var bin_data = r;

				var select_options = "";

				$.each(bin_data, function(k,v){
					if(id != v.id){
						select_options += "<option value='"+v.id+"'>"+v.name+"</option>";
					} else {
						select_options += "<option value='"+v.id+"' selected>"+v.name+"</option>";
					}
				})
				$(".nursery_"+group_id).html("");
				$(".nursery_"+group_id).append(select_options);
			}
		});
	}

	function loadBins(farm_id,bin_id,status,group_id){

		$.ajax({
			'url'	:	app_url+"/farrowingbins",
			data	: 	{'farm_id':farm_id},
			type	:	"GET",
			success	: 	function(r){
				var bin_data = r;

				var select_options = "";

				$.each(bin_data, function(k,v){
					if(bin_id != v.bin_id){
						select_options += "<option value='"+v.bin_id+"'>"+v.bin_number+"</option>";
					}else{
						select_options += "<option value='"+v.bin_id+"' selected>"+v.bin_number+"</option>";
					}
				})
				$(".bin-"+group_id).html("");
				$(".bin-"+group_id).append(select_options);

				if(status != "none"){
					// auto populated group name
					groupName($(".nursery_"+group_id).val(),$(".bin-"+group_id).val(),group_id);
				}

			}
		});
	}

	function groupName(farm_id,bin_id,group_id){

		$.ajax({
			'url'	:	app_url+"/groupname",
			data	: 	{'farm_id':farm_id,'bin_id':bin_id},
			type	:	"GET",
			success	: 	function(r){
				$(".group_name_text-"+group_id).html("");
				$(".group_name_text-"+group_id).html(r);
				$(".group_name_"+group_id).val(r);
			}
		})

	}

	function updateRecord(data){

		$.ajax({
			'url'	:	app_url+"/updatefarrowing",
			data	: 	data,
			type	:	"POST",
			success	: 	function(r){
				console.log(r)
			}
		})

	}

	$(".container").delegate(".btn-save-edited","click",function(e) {
        var group_id = $(this).attr("group-id");
		var previous_group_name = $(".group_name_previous_"+group_id).val();

		var data = {
			'group_id'			:	group_id,
			'group_name' 		:	$(".group_name_"+group_id).val(),
			'farm_id'			:	$(".nursery_"+group_id).val(),
			'bin_id'			:	$(".bin-"+group_id).val(),
			'number_of_pigs' 	:	$(".number_of_pigs_"+group_id).val()
		};

		if(group_name == previous_group_name){
			// nothing to update
			alert("Nothing to update.")
		} else if($("#group_name").val() == ""){
			alert("Please enter the group name.");
		} else {

			exists = checkExists(data['group_name'])

			setTimeout(function(){
				if(exists == 0){
					alert("Group name already exists...");
				} else {

					// update record
					updateRecord(data);

					// append
					$(".group-name-text-"+group_id).html("");
					$(".group-name-text-"+group_id).html(data['group_name']);

					$(".num-pigs-"+group_id).html("");
					$(".num-pigs-"+group_id).html(data['number_of_pigs']);

					$(".farm-name-"+group_id).html("");
					$(".farm-name-"+group_id).html($(".nursery_"+group_id+" option:selected").text());

					setTimeout(function(){
						$("#editFarrowing"+group_id).modal('hide');
					},500)

				}
			},1000)


		}
    });

	function checkExists(group_name){
		var result = 0;
		$.ajax({
			'url'	:	app_url+"/checkexists",
			data	: 	{'group_name':group_name},
			type	:	"GET",
			success	: 	function(r){
				result = r;
				return result;
			}
		})
	}

});
</script>

@stop
