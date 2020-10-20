<script type="text/javascript">
console.log("testing");
function drawChart() {


		var data{{$bin_id}} = google.visualization.arrayToDataTable([
		["Date", "Actual (Tons)", "Budgeted (Tons)"],
		@forelse($graph_data as $graph)
		['{{date('M j',strtotime($graph['update_date']))}}',{{$graph['actual']}},{{$graph['budgeted_amount']}}],
		@empty
		['-', 0,0],
		['-', 0,0],
		['-', 0,0],
		['-', 0,0],
		['-', 0,0],
		['-', 0,0],
		@endforelse
		]);

	var options = {
	  title: 'Consumptions History (Last 6 Updates)',
	  //chartArea:{left:30,top:50,width:"100%",height:"70%"},
	  chartArea:{left:30,top:50},
	  width: 590,
	  height: 280,
	  min:0,
	  curveType: 'function',
	  legend: { position: 'right' }
	};



	var chart{{$bin_id}} = new google.visualization.ColumnChart(document.getElementById('curve_chart{{$bin_id}}'));
	chart{{$bin_id}}.draw(data{{$bin_id}}, options);

}


</script>
