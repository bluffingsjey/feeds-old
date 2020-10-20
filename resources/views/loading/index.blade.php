@extends('app')
@section('content')
<style type="text/css">
@page {
    size: landscape;
    size: 297mm 210mm;
    margin: 0%;
    scale: 90;
}
@media print {
  @page {
    size: 297mm 210mm; /* landscape */
    /* you can also specify margins here: */
    margin: 25mm;
    margin-right: 45mm; /* for compatibility with both A4 and Letter */
  }
}
.tab-title {
	padding: 10px;
	color: #FFFFFF;
}

.tab-button {
    border-radius: 2px;
    padding: 5px !important;
	color: #FFFFFF !important;
    //cursor: pointer;
}

.tab-button-active {
	//background-color: #00f3f3 !important;
	//font-weight: bold;
}

.sched-tabs {
	border-bottom: none !important;
}

.sched-tabs>li>a {
	color: #20517b !important;
	padding: 0px 8px !important;
	font-weight: 600 !important;
}

.tab-content {
	background: #dadada;
	padding-left: 10px;
	padding-right: 10px;
	padding-top: 2px;
}

.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
	background-color: #dadada !important;
}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
	padding: 2px !important;
}

.sched_tool {
    background: #337AB7;
    margin-bottom: 10px;
    margin-top: -27px;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    padding: 10px;
    z-index: 1;
    padding-left: 11px;
    box-shadow: 1px 1px 7px 1px #615F5F;
    width: 1020px;
}
</style>

<div class="col-md-10">
 	 <div class="panel" style="width: 1032px;">

        <div class="panel-body" style="padding:5px;">
        	<div class="sched_tool">
                <div class="date_picker_sched form-inline" style="margin-bottom:5px;">
                    <div class="form-group-sm">
                        <input type="text" class="form-control input-sm" id="datepickerSchedTool" value="{{date("M d")}}" placeholder="Select Date" style="width:160px; margin-bottom:-3px;">
                        <small class="tab-title tab-button scheduled-btn">Scheduled: <span class="total_tons_scheduled">0</span> Ton/s</small>
                        <small class="tab-title tab-button delivered-btn">Delivered: <span class="total_tons_delivered">0</span> Ton/s</small>
                        <small class="tab-title">Total Items: <span class="total_tons_sched_tool">0</span> Ton/s</small>

                        <button type="button" class="btn btn-success btn-xs pull-right btn-next" aria-label="Left Align">
                          <span class="glyphicon glyphicon-chevron-right" id="next_date" aria-hidden="true"></span>
                        </button>

                        <button type="button" class="btn btn-success btn-xs pull-right btn-previous" aria-label="Left Align">
                          <span class="glyphicon glyphicon-chevron-left" id="previous_date" aria-hidden="true"></span>
                        </button>

                        <small class="tab-title pull-right loading-text" style="display:none; padding-bottom:0px;">Loading Please Wait...</small>

                    </div>
                </div>
                <div id="schedule" ></div>
        	</div>

					<div class="tab-legend-eta">

						<!-- Nav tabs -->
						<ul class="nav nav-tabs sched-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#legend" aria-controls="legend" role="tab" data-toggle="tab" id="tab_legend">Legend</a></li>
							<li role="presentation"><a href="#eta" aria-controls="eta" role="tab" data-toggle="tab" id="tab_eta">Delivery ETA</a></li>
              <li role="presentation"><a href="#dar" aria-controls="dar" role="tab" data-toggle="tab" id="tab_dar">Driver Activity Report</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="legend">
								<div class="legend">
									<div>
										<span class="glyphicon glyphicon-stop" aria-hidden="true" style="color:blue;"></span> - Delivery created and scheduled
									</div>
									<div>
										<span class="glyphicon glyphicon-stop" aria-hidden="true" style="color:green;"></span> - Driver accepted the load and currently en route to destination farms
									</div>
									<div>
										<span class="glyphicon glyphicon-stop" aria-hidden="true" style="color:red;"></span> - Driver unloaded all feed compartments and en route back to Mill
									</div>
									<div>
										<span class="glyphicon glyphicon-stop" aria-hidden="true" style="color:black;"></span> - The truck return successfully in the Mill
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="eta">
								<table class="table table-hover">
									<tr class="info">
                    <th>Name</th>
										<th>ETA</th>
									</tr>
								  <tr class="active">
										<td>Chris</td>
										<td>9:30 pm</td>
									</tr>
									<tr class="active">
										<td>Chris</td>
										<td>9:30 pm</td>
									</tr>
									<tr class="active">
										<td>Chris</td>
										<td>9:30 pm</td>
									</tr>
									<tr class="active">
										<td>Chris</td>
										<td>9:30 pm</td>
									</tr>
								</table>
							</div>
              <div role="tabpanel" class="tab-pane" id="dar">
								<table class="table table-hover">
									<tr class="info">
                    <th class="col-md-2">Time</th>
										<th class="col-md-2">Truck</th>
                    <th class="col-md-2">Going To</th>
                    <th class="col-md-1">Run Time</th>
                    <th class="col-md-3 text-center">Return (New Load Time)</th>
                    <th class="col-md-2">Actual Time Back</th>
									</tr>
								  <tr class="active">
										<td class="col-md-2">9:30 am</td>
										<td class="col-md-2">Chris</td>
                    <td class="col-md-2">YB Farming</td>
                    <td class="col-md-1">1h 30m</td>
                    <td class="col-md-3 text-center">8:00 am</td>
                    <td class="col-md-2">7:42 am</td>
									</tr>
									<tr class="active">
                    <td class="col-md-2">9:30 am</td>
										<td class="col-md-2">Chris</td>
                    <td class="col-md-2">YB Farming</td>
                    <td class="col-md-1">1h 30m</td>
                    <td class="col-md-3 text-center">8:00 am</td>
                    <td class="col-md-2">7:42 am</td>
									</tr>
									<tr class="active">
                    <td class="col-md-2">9:30 am</td>
										<td class="col-md-2">Chris</td>
                    <td class="col-md-2">YB Farming</td>
                    <td class="col-md-1">1h 30m</td>
                    <td class="col-md-3 text-center">8:00 am</td>
                    <td class="col-md-2">7:42 am</td>
									</tr>
									<tr class="active">
                    <td class="col-md-2">9:30 am</td>
										<td class="col-md-2">Chris</td>
                    <td class="col-md-2">YB Farming</td>
                    <td class="col-md-1">1h 30m</td>
                    <td class="col-md-3 text-center">8:00 am</td>
                    <td class="col-md-2">7:42 am</td>
									</tr>
								</table>
							</div>
						</div>

					</div>

        	<div class="sched-header-label">
                <h1 class="panel-title title-top-kb">Scheduled Deliveries
									<button type="button" class="pull-right btn btn-xs btn-default btn-print-preview" aria-label="Left Align" data-toggle="modal" data-target="#mmyModal">
										<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print
									</button>
								</h1>
            </div>

        	<div class="table-responsive sched-items-holder">

              <div class="col-md-12 col-lg-12 load-header-kb">

                    <div class="col-md-1">Date</div>
                    <div class="col-md-3">Farm</div>
                    <div class="col-md-2">Delivery Time</div>
                    <div class="col-md-1">Truck</div>
                    <div class="col-md-2">Driver</div>
                    <div class="col-md-1" style="width: 120px !important;">Delivery #</div>
                    <div class="col-md-1"></div>

                </div>
                <div class="data-holder">

              	</div>
           </div>
        </div>
     </div>
 </div>

@include('loading.js.index')
<script type="text/javascript" src="{{ asset('/js/html2canvas.min.js') }}"></script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" style="width: 95% !important; height: 320px !important;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Screenshot</h4>
      </div>
      <div class="modal-body print-preview" style="overflow:scroll">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-print">Print</button>
      </div>
    </div>
  </div>
</div>

@stop
