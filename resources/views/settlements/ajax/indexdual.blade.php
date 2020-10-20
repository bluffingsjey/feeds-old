<div class="row">
    <div class="col-md-6">
    
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Trucking</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->trucking_company}}</div>
            <div class="col-md-4">{{$settlements_data[1]->trucking_company}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Dead</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->dead_on_truck}}</div>
            <div class="col-md-4">{{$settlements_data[1]->dead_on_truck}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Destroyed</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->destroyed}}</div>
            <div class="col-md-4">{{$settlements_data[1]->destroyed}}</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Total Head</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->total_head}}</div>
            <div class="col-md-4">{{$settlements_data[1]->total_head}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Live WT</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->live_avg_weight}}</div>
            <div class="col-md-4">{{$settlements_data[1]->live_avg_weight}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Carcass</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->carcass_avg_weight}}</div>
            <div class="col-md-4">{{$settlements_data[0]->carcass_avg_weight}}</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Fat</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->fat_depth}}</div>
            <div class="col-md-4">{{$settlements_data[1]->fat_depth}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Loin</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->loin_depth}}</div>
            <div class="col-md-4">{{$settlements_data[1]->loin_depth}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Lean</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->lean_percentage}}</div>
            <div class="col-md-4">{{$settlements_data[1]->lean_percentage}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Yield</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->yield}}</div>
            <div class="col-md-4">{{$settlements_data[1]->yield}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Lean Adj</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->lean_adj}}</div>
            <div class="col-md-4">{{$settlements_data[1]->lean_adj}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Sort Adj</strong></div>
            <div class="col-md-4">({{$settlements_data[0]->sort_adj}})</div>
            <div class="col-md-4">({{$settlements_data[1]->sort_adj}})</div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-4 text-right"><strong>Price</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->price}}</div>
            <div class="col-md-4">{{$settlements_data[1]->price}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Paid</strong></div>
            <div class="col-md-4">{{$settlements_data[0]->paid}}</div>
            <div class="col-md-4">{{$settlements_data[1]->paid}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Sort Opp</strong></div>
            <div class="col-md-4">${{$settlements_data[0]->sort_opportunity}}</div>
            <div class="col-md-4">{{$settlements_data[1]->sort_opportunity}}</div>
        </div>
        <div class="row">
            <div class="col-md-4 text-right"><strong>Net Payment</strong></div>
            <div class="col-md-4">${{number_format($settlements_data[0]->net_amount,2)}}</div>
            <div class="col-md-4">${{number_format($settlements_data[1]->net_amount,2)}}</div>
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
		['{{$carcass['weight']}}',{{$carcass['total']}},{{$carcass['total_two']}}],
		@empty
		['0/150.5', 0, 0],
		['151/160.5', 0, 0],
		['161/165.5', 0, 0],
		['166/170.5', 0, 0],
		['171/180.5', 0, 0],
		['181/190.5', 0, 0],
		['191/200.5', 0, 0],
		['201/210.5', 0, 0],
		['211/220.5', 0, 0],
		['221/230.5', 0, 0],
		['231/235.5', 0, 0],
		['236/240.5', 0, 0],
		['241/245.5', 0, 0],
		['246/250.5', 0, 0],
		['251/255.5', 0, 0],
		['256/999', 0, 0]
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
		colors: ['#33ac71', '#337ab7'],
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
			['{{$lean['weight']}}',{{$lean['total']}},{{$lean['total_two']}}],
			@empty
			['0/44.99', 0, 0],
			['45/47.99', 0, 0],
			['48/48.99', 0, 0],
			['49/49.99', 0, 0],
			['50/50.99', 0, 0],
			['51/51.99', 0, 0],
			['52/52.5', 0, 0],
			['52.51/52.99', 0, 0],
			['53/53.5', 0, 0],
			['53.51/53.99', 0, 0],
			['54/54.99', 0, 0],
			['55/55.5', 0, 0],
			['55.51/56.5', 0, 0],
			['56.51/56.99', 0, 0],
			['57/57.99', 0, 0],
			['58/100', 0, 0]
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
        	colors: ['#bdc100', '#337ab7'],
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