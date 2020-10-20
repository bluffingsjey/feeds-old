@extends('app')
@section('content')
@include('settlements.js.index')
<style type="text/css">

.labels {
	width: 85px;
}

.labels-right {
	width: 70px;
}

.or-border {
	width: 170px;
	text-align: center;
}

.view-report {
	margin-top: 5px;
	padding-left: 0px;
    padding-right: 0px;
}

.settlement-upload {
	width: 170px;
    margin-top: 28px;
}

.margin-textbox {
	margin-top: 5px;
}

.holder-settlement-form {
	background: #337AB7;
    margin-top: -21px;
    padding-top: 15px;
    padding-bottom: 5px;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
	margin-left: 40px;
}

.labels-color {
	color: #FFF;
}

.clearfix {
	margin-top: 20px;
}
.score {
	margin-top: 20px;
    text-align: center;
    height: 370px;
    padding: 10px;
    border: 3px solid black;
    border-radius: 5px;
}
#farm_view_report_1 {
	width: 168px;
}
#farm_view_report_2 {
	width: 168px;
}
#group_view_report_1 {
	width: 168px;
}
#group_view_report_2 {
	width: 168px;
}
#farm_process {
	width: 168px;
}
#group_process {
	width: 168px;
}
</style>

<!-- Alert Modal -->
<div class="modal-margin modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">H & H Farms</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <div class="input-group modalMessage">

            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="col-md-9 holder-settlement-form">

    <div class="upload_tool">

		<div class="left-form col-md-8">

        	<!-- Farm and group -->
        	<form class="form-inline">
              <div class="form-group labels labels-color">
                Farm
              </div>
              <div class="form-group">
                <select class="form-control input-sm" id="farm_view_report_1" placeholder=""></select>
              </div>
              <div class="form-group">
                <select class="form-control input-sm" id="farm_view_report_2" placeholder=""></select>
              </div>
            </form>
            <form class="form-inline">
              <div class="form-group labels labels-color">
                Group
              </div>
              <div class="form-group margin-textbox">
                <select class="form-control input-sm" id="group_view_report_1" placeholder=""></select>
              </div>
              <div class="form-group margin-textbox">
                <select class="form-control input-sm" id="group_view_report_2" placeholder=""></select>
              </div>
            </form>

            <!-- or border -->
            <form class="form-inline">
              <div class="form-group labels">

              </div>
              <div class="form-group or-border labels-color">
                or
              </div>
              <div class="form-group or-border labels-color">
                or
              </div>
            </form>

            <!-- begin Date and end date -->
            <form class="form-inline">
              <div class="form-group labels labels-color">
                Begin Date
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" id="begin_date_1" placeholder="">
              </div>
              <div class="form-group">
                <input type="text" class="form-control input-sm" id="begin_date_2" placeholder="">
              </div>
            </form>
            <form class="form-inline">
              <div class="form-group labels labels-color">
                End Date
              </div>
              <div class="form-group margin-textbox">
                <input type="text" class="form-control input-sm" id="end_date_1" placeholder="">
              </div>
              <div class="form-group margin-textbox">
                <input type="text" class="form-control input-sm" id="end_date_2" placeholder="">
              </div>
            </form>

            <!-- view report button -->
            <form class="form-inline">
              <div class="form-group col-md-8 col-md-offset-2 view-report">
                <button class="btn btn-xs btn-block btn-warning btn-view-report" type="button">View Report</button>
              </div>
            </form>

        </div>

        <div class="right-form col-md-4">
        	<!-- begin Date and end date -->
            <form class="form-inline">
              <div class="form-group labels-right labels-color">
                Farm
              </div>
              <div class="form-group">
                <select class="form-control input-sm farm_process" id="farm_process" placeholder=""></select>
              </div>
            </form>
            <form class="form-inline">
              <div class="form-group labels-right labels-color">
                Group
              </div>
              <div class="form-group margin-textbox">
                <select class="form-control input-sm" id="group_process" placeholder=""></select>
              </div>
            </form>
            <form class="form-inline" id="settlement_file_form">
              <div class="form-group settlement-upload">
                <input type="file" id="settlement_file" class=" labels-color">
                <p class="help-block labels-color">Upload the settlement file</p>
              </div>
            </form>

            <!-- process settlement statement button -->
            <form class="form-inline">
              <div class="form-group col-md-12 view-report">
                <button class="btn btn-xs btn-block btn-info btn-process-settlement" type="button">Process Settlement Statement</button>
              </div>
            </form>
        </div>

    </div>

</div>

<div class="col-md-9">
	<div class="settlement-content"></div>
</div>


@stop
