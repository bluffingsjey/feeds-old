@extends('app')


@section('content')

<style type="text/css">
.fix_width {
	width: 110px !important;
	height: 25px !important;
	line-height: 1 !important;
}
.date_group {
	margin-left: 90px;
}
#date_from{
	background-color: #FFF;
}
#date_to{
	background-color: #FFF;
}
.sort_group {
	margin-left: 80px;
}
.btn-search {
	font-weight: bold;
	height: 25px;
	line-height: 1 !important;
}
.badge-farrowing {
	background-color: #00e7ff !important;
}
.badge-nursery {
	background-color: #0095ff !important;
}
.badge-finisher {
	background-color: #002bff !important;
}
</style>
<div class="col-md-10">

	<nav class="navbar navbar-default" style="background-color:#0084c7; color:#FFF; margin-bottom:5px; min-height:40px;">
      <form class="navbar-form " role="search">

          <div class="form-group">
          	TYPE:
            <select id="farm_type" class="form-control input-sm fix_width">
            	<option value="all">All</option>
            	<option value="farrowing_to_nursery">Farrowing to Nursery</option>
                <option value="nursery_to_finisher">Nursery to Finisher</option>
                <option value="finisher_to_market">Finisher to Market</option>
            </select>
          </div>


          <div class="form-group date_group">
          	DATE RANGE:
            <input type="text" value="{{date('Y-01-01')}}" class="form-control input-sm fix_width" id="date_from" placeholder="From" readonly>
            <input type="text" value="{{date('Y-m-t')}}"  class="form-control input-sm fix_width" id="date_to" placeholder="To" readonly>
          </div>


          <div class="form-group sort_group">
          	SORT BY:
            <select id="sort_by" class="form-control input-sm fix_width">
            	<option value="not_scheduled">Not Scheduled</option>
            	<option value="day_remaining">Day Remaining</option>
            </select>
          </div>

          <div class="form-group pull-right">
          	<button type="button" class="btn btn-warning btn-default btn-search btn-sm" style="font-weight:bold;">View</button>
      	  </div>
      </form>
    </nav>

    <!--Loading-->


	<!---->
    <div class="table_holder row">
    	<div class="loading-stick-circle loading">

			<img src="/css/images/loader-stick.gif" />
			Please wait, Rendering...

		</div>
    </div>

</div>

@include('movement.js.landing')

@stop
