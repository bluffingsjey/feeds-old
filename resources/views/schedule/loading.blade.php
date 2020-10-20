@extends('app')


@section('content')
<div class="row" style="border-top: #C2BCBC 2px solid;">
    <div class="col-lg-4">
        <h4 class="text-left"><a href="#" class="undone" style="font-weight: normal; text-decoration: none;">Scheduling    <span style="font-weight:bolder;">|    Loading</span></a></h4>
    </div>
    <div class="col-lg-4 col-lg-offset-4">
        <h4 class="text-right"><a href="#" class="undone" style="font-weight: bolder; text-decoration: none;">Create Load   <span class="glyphicon glyphicon-fullscreen" aria-hidden="true" style="color:#7E7E7E"></span></a></h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-6">
        	<div class="row" style="padding-right:10px;">
                <div class="table-responsive">
                    <table class="table table-strip">
                        <thead>
                            <tr>
                                <th class="col-lg-1"></th>
                                <th class="col-lg-2 bold-text">Date/Time</th>
                                <th class="col-lg-2 bold-text">Truck</th>
                                <th class="col-lg-4 bold-text">Farm(s)</th>
                                <th class="col-lg-2 bold-text">Delivery Time</th>
                                <th class="col-lg-1 bold-text">Load</th>
                            </tr>
                        </thead>
                        <tbody>
                        	@foreach ($deliveries as $delivery)
                            <tr>
                                <td class="col-lg-1">
                                <button type="button" class="btn btn-xs center-block">
                                <span class="glyphicon glyphicon-plus undone" style="color:#337ab7; cursor: pointer;"></span>
                                </button>
                                </td>
                                <td class="col-lg-2">{{ date('m-d-Y',strtotime($delivery->delivery_date)) }}</td>
                                <td class="col-lg-2">{{ $delivery->capacity }}</td>
                                <td class="col-lg-4">{{ $delivery->farm_name }}</td>
                                <td class="col-lg-2">{{ date('h:m a',strtotime($delivery->delivery_date)) }}</td>
                                <td class="col-lg-1">
                                    <a class="btn btn-default btn-xs undone center-block" href="schedule/loading"  aria-label="Left Align">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6" style="padding: 20px; border: 1px solid #DDD;">
        	<div class="row">
            	<div class="table-responsive">
                    <table class="table table-striped" style="margin-bottom: 0px;">
                        <thead>
                            <tr>
                                <th class="col-lg-3 bold-text home-table-list-font"><p>Delivery Date: <br/>{{date('m-d-Y',strtotime($batchs[0]->delivery_date))}}</p>
                                </th>
                                <th class="col-lg-3 bold-text home-table-list-font"><p>Delivery Time: <br/>{{date('h:m',strtotime($batchs[0]->delivery_date))}}</p>
                                </th>
                                <th class="col-lg-3 bold-text home-table-list-font"><p>Time of the Day: <br/>{{date('a',strtotime($batchs[0]->delivery_date))}}</p>
                                </th>
                                <th class="col-lg-3 bold-text home-table-list-font"><p>Truck: <br/>{{$batchs[0]->capacity}} Tons</p>
                                </th>
                            </tr>
                        </thead>
                     </table>
                </div>
            </div>
        	<div class="row" style="padding-right:10px;">
                <div class="table-responsive">
                    <table class="table table-strip">
                        <thead>
                            <tr>
                                <th class="col-lg-2 bold-text">Farm</th>
                                <th class="col-lg-2 bold-text">Batch</th>
                                <th class="col-lg-2 bold-text">Type</th>
                                <th class="col-lg-2 bold-text">Amount</th>
                                <th class="col-lg-2 bold-text">Bin(s)</th>
                                <th class="col-lg-1 bold-text">Load</th>
                                <th class="col-lg-1 bold-text">Comp</th>
                            </tr>
                        </thead>
                        <tbody>
                        	@foreach ($batchs as $batch)
                            <tr ng-repeat="BatchList in BatchLists">
                                <td class="col-lg-2">{{$batch->name}}</td>
                                <td class="col-lg-2">{{$batch->batch_code}}</td>
                                <td class="col-lg-2">{{$batch->feedsname}}</td>
                                <td class="col-lg-2">{{$batch->amount}} Tons</td>
                                <td class="col-lg-2">{{$batch->bins}}</td>
                                <td class="col-lg-1">
                                	<input type="checkbox" />
                                </td>
                                <td class="col-lg-1">
                                	<input type="checkbox" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
    </div>
</div>
    
@stop