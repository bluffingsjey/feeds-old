<div class="row">
    <div class="col-md-6">
    
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Trucking</strong></div>
            <div class="col-md-4">{{$settlements_data->trucking_company}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Dead</strong></div>
            <div class="col-md-4">{{$settlements_data->dead_on_truck}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Destroyed</strong></div>
            <div class="col-md-4">{{$settlements_data->destroyed}}</div>
            <div class="col-md-4">-</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Total Head</strong></div>
            <div class="col-md-4">{{$settlements_data->total_head}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Live WT</strong></div>
            <div class="col-md-4">{{$settlements_data->live_avg_weight}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Carcass</strong></div>
            <div class="col-md-4">{{$settlements_data->carcass_avg_weight}}</div>
            <div class="col-md-4">-</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Fat</strong></div>
            <div class="col-md-4">{{$settlements_data->fat_depth}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Loin</strong></div>
            <div class="col-md-4">{{$settlements_data->loin_depth}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Lean</strong></div>
            <div class="col-md-4">{{$settlements_data->lean_percentage}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Yield</strong></div>
            <div class="col-md-4">{{$settlements_data->yield}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Lean Adj</strong></div>
            <div class="col-md-4">{{$settlements_data->lean_adj}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Sort Adj</strong></div>
            <div class="col-md-4">({{$settlements_data->sort_adj}})</div>
            <div class="col-md-4">-</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Price</strong></div>
            <div class="col-md-4">{{$settlements_data->price}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Paid</strong></div>
            <div class="col-md-4">{{$settlements_data->paid}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Sort Opp</strong></div>
            <div class="col-md-4">${{$settlements_data->sort_opportunity}}</div>
            <div class="col-md-4">-</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Net Payment</strong></div>
            <div class="col-md-4">${{number_format($settlements_data->net_amount,2)}}</div>
            <div class="col-md-4">-</div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="score">Reserved Area</div>
    </div>
</div>
<div class="row">
    <div class="col-md-11">
        <div class="clearfix"></div>
        
        <div id="carcass_graph" style="width: 1000px; height: 450px;"></div>
        
        <div id="lean_graph" style="width: 1000px; height: 450px;"></div>
    </div>
</div>


<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {
	var data = google.visualization.arrayToDataTable([
	 	['Weight', '', ''],
		@forelse($carcass_data as $carcass)
		['{{$carcass->weight}}',{{$carcass->total}},0],
		@empty
		['0/150.5', 3, 2],
		['151/160.5', 10, 9],
		['161/165.5', 30, 21],
		['166/170.5', 40, 30],
		['171/180.5', 26, 51],
		['181/190.5', 36, 47],
		['191/200.5', 27, 28],
		['201/210.5', 59, 47],
		['211/220.5', 43, 45],
		['221/230.5', 22, 22],
		['231/235.5', 50, 46],
		['236/240.5', 30, 20],
		['241/245.5', 20, 13],
		['246/250.5', 10, 10],
		['251/255.5', 8, 8],
		['256/999', 7, 5]
		@endforelse	
	]);
	
	var view = new google.visualization.DataView(data);
	  view.setColumns([0, 1,
					   { calc: "stringify",
						 sourceColumn: 1,
						 type: "string",
						 role: "annotation" },
					   2]);
	
	var options = {
		title: 'Carcass Weight Analysis',
		colors: ['#337ab7', '#33ac71'],
		chart: {
			title: 'Company Performance',
			subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  },
		hAxis: {title: "" , direction:1, slantedText:true, slantedTextAngle:60, fontSize: 12 },
		legend: { position: "none" }
	};
	
	var chart = new google.visualization.ColumnChart(document.getElementById('carcass_graph'));
	
	chart.draw(data, options);
}
</script>

<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawChartTwo);
	function drawChartTwo() {
		var data = google.visualization.arrayToDataTable([
		  	['Weight', '', ''],
			@forelse($lean_data as $lean)
			['{{$lean->weight}}',{{$lean->total}},0],
			@empty
			['0/44.99', 10, 40],
			['45/47.99', 10, 40],
			['48/48.99', 11, 46],
			['49/49.99', 50, 20],
			['50/50.99', 30, 50],
			['51/51.99', 10, 40],
			['52/52.5', 11, 46],
			['52.51/52.99', 50, 20],
			['53/53.5', 30, 40],
			['53.51/53.99', 10, 40],
			['54/54.99', 10, 40],
			['55/55.5', 50, 10],
			['55.51/56.5', 10, 50],
			['56.51/56.99', 10, 40],
			['57/57.99', 10, 50],
			['58/100', 50, 10]
			@endforelse
		]);
		
		var view = new google.visualization.DataView(data);
		  view.setColumns([0, 1,
						   { calc: "stringify",
							 sourceColumn: 1,
							 type: "string",
							 role: "annotation" },
						   2]);
		
		var options = {
			title: 'Lean Analysis',
        	colors: ['#337ab7', '#bdc100'],
			chart: {
				title: 'Company Performance',
				subtitle: 'Sales, Expenses, and Profit: 2014-2017',
			  },
			hAxis: {title: "" , direction:1, slantedText:true, slantedTextAngle:60, fontSize: 12 },
			legend: { position: "none" }
		};
		
		var chart = new google.visualization.ColumnChart(document.getElementById('lean_graph'));
		
		chart.draw(data, options);
	}
</script>