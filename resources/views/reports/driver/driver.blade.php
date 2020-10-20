@extends('app')


@section('content')

<div class="col-md-9">
 
    <div>

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="/driverstracking" role="tab" style="color: #31708f; background-color: #d9edf7; font-weight:bolder;">Drivers Delivery Time Tracking</a></li>
        <li role="presentation"><a href="/livestocktracking" role="tab">Livestock Tracking</a></li>
      </ul>
    
      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active">
        	@foreach($drivers as $k => $v)
        	<h3 class="text-center">{{$v['username']}}</h3>
        	<div id="drivers_chart{{$v['id']}}" style="width: 850px; height: 300px; margin-top: 5px; border: 1px solid #DDD; padding: 10px;"></div>
            
            @endforeach
        </div>
      </div>
    
    </div>
 
</div>

<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>        
<script type="text/javascript">
// Load the Visualization API and the piechart package.
google.charts.load('current', {'packages':['corechart']});
</script>
<script type="text/javascript">
$(document).ready(function(e) {
    drawChart();
});

function drawChart() {

	@foreach($drivers as $k => $v)
	var data{{$v['id']}} = google.visualization.arrayToDataTable([
		["Date", "Time Spent(Hour)", "Down Time(Hour)"],
		@for($d = 15; $d > 0; $d--)
		["{{date('M j',strtotime('-'.$d.' days'))}}",1,2],
		@endfor	
		]);
	
	var options = {
	  title: '(Last 15 Days Updates)',
	  //chartArea:{left:30,top:50,width:"100%",height:"70%"},
	  chartArea:{left:30,top:50},
	  width: 825,
	  height: 280,
	  min:0,
	  curveType: 'function',
	  legend: { position: 'right' }
	};
	
	var chart{{$v['id']}} = new google.visualization.LineChart(document.getElementById('drivers_chart{{$v['id']}}'));
	chart{{$v['id']}}.draw(data{{$v['id']}}, options);
	
	@endforeach
	
}
</script>

@stop