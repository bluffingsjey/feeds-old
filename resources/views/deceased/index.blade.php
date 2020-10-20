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
    <h1 class="panel-title">Deceased <span><button class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#decreasedModal">Add Decreased Pigs</button></span></h1>
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
                    <th>Cause</th>
                    <th>Notes</th>
										<th>Actions</th>
                </tr>
            </thead>

            <tbody class="deceased-data">

            </tbody>

        </table>
    </div>
</div>
</div>


@include('deceased.modals.add')
@include('deceased.js.index')
@stop
