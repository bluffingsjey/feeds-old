/*
*	Main Service of the app
*/
app.factory('MainServices', function($http,$location){
	
	var mainURL = "http://feeds.carrierinsite.com/";
	var $promise = "";
	
	
	return {

		// read all trucks
		readTrucks: function(scope){
			$promise = $http.post(mainURL+"scheduling/trucks");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.trucks = response.data;
				} else {
					console.log(response.data);
				}
			});
		},
		// read all trucks
		readDrivers: function(scope){
			$promise = $http.post(mainURL+"scheduling/drivers");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.drivers = response.data;
					console.log(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// read all feed types
		readFeedTypes: function(scope){
			$promise = $http.post(mainURL+"scheduling/feedtypes");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.feeds = response.data;
				} else {
					console.log(response.data);
				}
			});
		},
		// Read All Farms
		readFarms: function(scope){
			$promise = $http.post(mainURL+"scheduling/farms");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.farms = response.data;
				} else {
					console.log(response.data);
				}
			});
		},
		// Read Amounts
		readAmounts: function(scope){
			$promise = $http.post(mainURL+"scheduling/amounts");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.amounts = Object(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// Read Bins
		readBins: function(scope, farm_id){
			$promise = $http.post(mainURL+"scheduling/bins/"+farm_id);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.bin = Object(response.data);
					scope.bin_one = Object(response.data);
					scope.bin_two = Object(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// Store Batch Deliveries
		storeBatchDeliveries: function(scope, data){
			$promise = $http.post(mainURL+"scheduling/addbatch",data);			
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.batchPending = Object(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// Read Bins
		readBatch: function(scope){
			$promise = $http.post(mainURL+"schedule/batch");
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.BatchLists = Object(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// Read specific farm delivery
		readFarmDelivery: function(scope,id){
			$promise = $http.post(mainURL+"schedule/loading/"+id);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.DeliveryTruck = Object(response.data.truck);
					console.log(scope.DeliveryTruck);
					scope.DeliveryFarms = Object(response.data.farms);
					console.log(scope.DeliveryFarms);
					scope.DeliveryCompartments = Object(response.data.compartments);
					console.log(scope.DeliveryCompartments);
					scope.DeliveryTruckCompartments = Object(response.data.compartments);
					console.log(scope.DeliveryTruckCompartments);
					
				} else {
					console.log(response.data);
				}
			});
		},
		// Read specific bin
		readBinsData: function(scope,farm_id,bin_number){
			$promise = $http.post(mainURL+"schedule/bins?farm_id="+farm_id+"&bin_number="+bin_number);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.BinsData = Object(response.data[0]);
					console.log(scope.BinsData);
				} else {
					console.log(response.data);
				}
			});
		},
		// Read specific bin
		getBinsColorOne: function(scope,farm_id,bin_number){
			$promise = $http.post(mainURL+"schedule/bin?farm_id="+farm_id+"&bin_number="+bin_number);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.BinsColorDataOne = Object(response.data[0]);
					//console.log(scope.BinsColorDataOne.hex_color);
					console.log(response.data[0]);
				} else {
					console.log(response.BinsColorData);
				}
			});
		},
		// Read specific bin
		getBinsColorTwo: function(scope,farm_id,bin_number){
			$promise = $http.post(mainURL+"schedule/bins?farm_id="+farm_id+"&bin_number="+bin_number);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					scope.BinsColorDataTwo = Object(response.data[0]);
					console.log(scope.BinsColorDataTwo.hex_color);
				} else {
					console.log(response.BinsColorData);
				}
			});
		},
		// Read specific bin
		storeTruckLoads: function(scope,truckLoads){
			$promise = $http.post(mainURL+"scheduling/storetruckloads",truckLoads);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					console.log(response.data);
				} else {
					console.log(response.BinsColorData);
				}
			});
		},
		// Read specific bin
		sendDriverNotification: function(scope,batch){
			$promise = $http.post(mainURL+"scheduling/drivermessaging",batch);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					console.log(response.data);
				} else {
					console.log(response.data);
				}
			});
		},
		// Read specific bin
		sendFarmNotification: function(scope,batch){
			$promise = $http.post(mainURL+"scheduling/farmmessaging",batch);
			$promise.then(function(response){
				if(typeof response.data === 'object'){
					console.log(response.data);
				} else {
					console.log(response.data);
				}
			});
		}
		 
	}

})