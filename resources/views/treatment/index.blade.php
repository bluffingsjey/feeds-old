@extends('app')


@section('content')

<style>
.space {
	margin-bottom: 5px;
}
#notes {
	height: 150px;
}
</style>

<div class="col-md-10">
<div class="panel panel-info">
	<div class="panel-heading">
    <h1 class="panel-title">Treatment <span><button class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#treatmentModal">Add Treatment</button></span></h1>
    </div>
	<div class="panel-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped ">

            <thead>
                <tr>
                    <th>Date</th>
										<th>Animal Group</th>
										<th>Farm</th>
										<th>Bins</th>
                    <th>Amount</th>
                    <th>Illness</th>
										<th>Drug Used</th>
                    <th>Notes</th>
										<th>Actions</th>
                </tr>
            </thead>

            <tbody class="treatment-data">

            </tbody>

        </table>
    </div>
</div>
</div>


@include('treatment.modals.add')
@include('treatment.js.index')
@stop
