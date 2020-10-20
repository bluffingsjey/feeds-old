@extends('app')

@section('content')

<style type="text/css">
.sorting {
	cursor: pointer;
	color: #DDD;
}
.sorting:hover {
	color: #8100FF;
}
.sorting-active {
	color: #8100FF;
}
.hr-driver{
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #24b300;
}

#date_from {
	background-color: #FFF;
}
#date_to {
	background-color: #FFF;
}
.hr-tons-delivered {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #00f3ff;
}

.hr-delivery-time {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #b30000;
}

.hr-drive-time {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #ffbc00;
}

.hr-time-at-farm {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #001eb3;
}

.hr-time-at-mill {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #f553c3;
}

.hr-total-miles {
	margin-top: 0px;
    margin-bottom: 5px;
    border: 0;
    border-top: 3px solid #00ff0d;
}

#drivers_list th{
	cursor: pointer;
}
</style>

<div class="col-md-10">

	<nav class="navbar navbar-default" style="background-color:#0084c7; color:#FFF; margin-bottom:5px;">
      <form class="navbar-form navbar-left" role="search">
          Date Range:
          <div class="form-group">
            <input type="text" class="form-control" id="date_from" readonly>
          </div>
          To:
          <div class="form-group">
            <input type="text" class="form-control" id="date_to" readonly>
          </div>
          <button type="button" class="btn btn-info btn-default btn-search" style="font-weight:bold;">Search</button>
      </form>
    </nav>

    <!--Loading-->
    <div class="loading-stick-circle">

        <img src="/css/images/loader-stick.gif" />
        Please wait, Rendering...

    </div>

	<!---->
    <div class="table_holder">

    </div>

</div>

@include('reports.js.driver-js')
<script src="{{ asset('js/jquery.sortElements.js') }}"></script>

@stop
