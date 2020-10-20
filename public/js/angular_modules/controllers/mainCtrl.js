/*
*	Main controller for the handapp
*/

app.controller('MainController',function($scope,$http,$timeout,MainServices){
	
	// variables for object "loadingList"
	var farm_id = "";
	var batch = "";
	var feeds_type = "";
	var amount = "";
	var bin_one = "";
	var bin_two = "";
	
	// variables for object "loadingElements"
	var farm_label = "";
	var batch_label = "";
	var feeds_type_label = "";
	var amount_label = "";
	var bin_one_label = "";
	var bin_two_label = "";
	
	// Objects	
	var loadingList = [];
	var loadingElements = [];
	
	$scope.toggle = true;
	
	/*
	*	toggleMe()
	*	Toggle the desired element for fadeIn and fadeOut effect
	*/
	$scope.toggleMe = function() {
		$scope.toggle=!$scope.toggle;
	}
	
	/*
	*	sliderMe()
	*	Toggle the desired element for fadeIn and fadeOut effect
	*/
	$scope.sliderMe = function() {
		this.slider=!this.slider;
	}
	
	// Call the initializer method
	init();
	
	/*
	*	init()
	*	Initializer
	*/
    function init(){
		MainServices.readTrucks($scope);
		MainServices.readDrivers($scope);
		MainServices.readFeedTypes($scope);
		MainServices.readFarms($scope);
		MainServices.readAmounts($scope);
		MainServices.readBatch($scope);
		$scope.loadingElements;
		$scope.batch = {};
    };
	
	/*
	*	clear()
	*	Clear the forms input
	*/
	function clear(){
		MainServices.readFeedTypes($scope);
		MainServices.readFarms($scope);
		MainServices.readAmounts($scope);
		$scope.loadingElements;
		$scope.batch = {};
		$scope.farms = {};
		$scope.feeds = {};
		$scope.bin_one = {};
		if(typeof $scope.bin_two != 'undefined'){
			$scope.bin_two = {};
		}
	}
	
	/*
	*	update()
	*	Update the bins based from the selected farm
	*/
	$scope.update = function() {
		farm_id = $scope.farms.selected.id;
		MainServices.readBins($scope,farm_id);
	};
	
	/*
	*	addLoading()
	*	Add the loading farm for scheduling
	*/
	$scope.addLoading = function(batch,amounts) {
		
		// Variables for "loadingList"
		farm_id = $scope.farms.selected.id;
		feeds_type = $scope.feeds.selected.type_id;
		amount = amounts.selected;
		bin_id = $scope.bin_one.selected_one.bin_id;
		
		// Variables for loadingElements
		farm_label = $scope.farms.selected.name;
		batch_label = batch.text;
		feeds_type_label = $scope.feeds.selected.name;
		amount_label = amount;
		bin_one_label = $scope.bin_one.selected_one.bin_number;
		// object to be saved in database
		loadingList.push({
				'farm_id' 		:	farm_id,
				'feeds_type_id'	:	feeds_type,
				'batch'			:	batch.text,
				'amount' 		:	amount,
				'bin_id'		:	bin_id
		});
		
		// elements - add elements to the tables
		loadingElements.push({
			'farm'		:	farm_label,
			'batch'		:	batch_label,
			'type'		:	feeds_type_label,
			'amount'	:	amount_label,
			'bin_one'	:	bin_one_label	
		})
		
		// initialize the loading list
		$scope.loadingElements = loadingElements;
		$scope.loadingList = loadingList;
		
		// clear input form
		clear();
		
	}
	
	
	/*
	*	removeLoading()
	*	Remove the recently added farms delivery from input form
	*/
	$scope.removeLoading = function(id){
		$scope.loadingElements.splice(id,1);
	}
	
	/*
	*	deleteBatch()
	*	Delete the batch farms delivery from the batch lists
	*/
	$scope.deleteBatch = function(){
		$scope.loadingElements = {};
		$scope.loadingList = {};
	}
	
	/*
	*	saveBatch()
	*	Save the batch farms deliveries
	*	param: deldate,deltime,deltimeofday,truck_id,drive_id
	*/
	$scope.saveBatch = function(deldate,deltime,deltimeofday,truck_id,driver_id){
		//validate
		if(deldate == null){
			alert("Please pick delivery date");
		}else if(deltime == null){
			alert("Please pick delivery time");
		}else if(deltimeofday == null){
			alert("Please pick delivery time of the day");
		}else if(truck_id == undefined){
			alert("Please pick delivery truck");
		}else if($scope.loadingList == null){
			alert("Please add batch delivery");
		} else {
			var delivery_date = deldate+" "+deltime+" "+deltimeofday
			buildData($scope.loadingList,delivery_date,truck_id,driver_id);	
		}
		
		init();
		$scope.deleteBatch();
		
	}
	
	/*
	*	buildData()
	*	Build the array data for the farms batch deliveries
	*	param: batch,deliveryDate,truckId
	*/
	function buildData(batch,deliveryDate,truckId,driver_id){
		var batchData = [];
		
		angular.forEach(batch, function(value,key){
			batchData.push({
				'deliveryDate'	:	deliveryDate,
				'truck_id'		:	truckId,
				'driver_id'		:	driver_id,
				'farm_id' 		:	value.farm_id,
				'feeds_type_id'	:	value.feeds_type_id,
				'batch'			:	value.batch,
				'amount' 		:	value.amount,
				'bin_id'		:	value.bin_id	
			});
		});
		
		console.log(batchData);
		MainServices.storeBatchDeliveries($scope,batchData);		
	}
	
	
	/*
	*	readDelivery()
	*	read the requested delivery id
	*	param: id
	*/
	$scope.readDelivery = function(id){
		MainServices.readFarmDelivery($scope,id);
	}
	
	/*
	*	binColorInit()
	*	Bins Color Initializer
	*/
	$scope.binColorInit = function(DeliveryFarm){
		// Get the bins color
		
		MainServices.getBinsColorOne($scope,DeliveryFarm.farm_id,DeliveryFarm.binonecolor.bin_number);
		//MainServices.getBinsColorTwo($scope,DeliveryFarm.farm_id,DeliveryFarm.bin_two);
	}
	
	/*
	*	updateCompartments
	*	Update the compartments from the data coming form the bins
	*/
	$scope.updateCompartments = function(DeliveryFarm){
		// if id is the same as the build data array
		
		MainServices.getBinsColorOne($scope,DeliveryFarm.farm_id,DeliveryFarm.binonecolor.bin_number);
		//MainServices.getBinsColorTwo($scope,DeliveryFarm.farm_id,DeliveryFarm.bin_two);
			
		if(DeliveryFarm.amount > this.comnumber.capacity){
			alert("The amount of the bin is greater than the compartment # "+ this.comnumber.compartment_number +" capacity");
		} else {
			MainServices.getBinsColorOne($scope,DeliveryFarm.farm_id,DeliveryFarm.binonecolor.bin_number);
			//MainServices.getBinsColorTwo($scope,DeliveryFarm.farm_id,DeliveryFarm.bin_two);
			rebuildCompartments(this.comnumber.compartment_number,$scope.BinsColorDataOne,DeliveryFarm);
		}
	}
	
	/*
	*	rebuildCompartments()
	*	rebuild the compartments by adding the bin color
	*	
	*/
	function rebuildCompartments(com_number,bin_one_color,DeliveryFarm){
		console.log($scope.DeliveryTruckCompartments[0].bin_one_color);
		// if no bin color is added, add the bin color
		if(typeof $scope.DeliveryTruckCompartments[0].bin_one_color === 'undefined' || $scope.DeliveryTruckCompartments[0].bin_one_color === null) {
			var rebuildCompartments = [];
			angular.forEach($scope.DeliveryTruckCompartments, function(value,key){
				
				var bins_color_one = value.compartment_number == com_number ? bin_one_color.hex_color : "none";
				//var bins_color_two = value.compartment_number == com_number ? bin_two_color.hex_color : "none";
				
				rebuildCompartments.push({
					'capacity'				:	value.capacity,
					'compartment_id'		:	value.compartment_id,
					'compartment_number' 	:	value.compartment_number,
					'created_at'			:	value.created_at,
					'truck_id'				:	value.truck_id,
					'driver_id'				:	DeliveryFarm.driver_id,
					'batch'					:	"none",
					'updated_at' 			:	value.updated_at,
					'user_id'				:	value.user_id,
					'bin_one_color'			:	bins_color_one,
					'bin_one_number'		:	0, //Number(DeliveryFarm.bin_one),
					'bin_two_number'		:	0, //DeliveryFarm.bin_two,
					'bins_amount'			:	0, //DeliveryFarm.amount,
					'farm_id'				:	0, //DeliveryFarm.farm_id,
					'date_of_delivery'		:	DeliveryFarm.delivery_date
				});
			});		
			$scope.DeliveryTruckCompartments = rebuildCompartments;	
		} else {
		// if there is added bin color, update the data
			
			// get the array key of the selected dropdown
			var array_key =  Number(com_number-1);
			
			// remove previous bin color
			angular.forEach($scope.DeliveryTruckCompartments, function(value,key){
				
				/*if($scope.DeliveryTruckCompartments[key].bin_one_color == bin_one_color.hex_color ||
				   $scope.DeliveryTruckCompartments[key].bin_two_color == bin_two_color.hex_color){*/
				if($scope.DeliveryTruckCompartments[key].bin_one_color == bin_one_color.hex_color){		
					$scope.DeliveryTruckCompartments[key].bin_one_color = "none";
					//$scope.DeliveryTruckCompartments[key].bin_two_color = "none";
					$scope.DeliveryTruckCompartments[key].bin_one_number = 0;
					$scope.DeliveryTruckCompartments[key].bins_amount = 0;
					$scope.DeliveryTruckCompartments[key].capacity = 0;
					$scope.DeliveryTruckCompartments[key].compartment_id = 0;
					$scope.DeliveryTruckCompartments[key].farm_id = 0;
					
				}
				
			});
			
			// find the previous hex color
			/*if($scope.DeliveryTruckCompartments[array_key].bin_one_color == bin_one_color.hex_color ||
			   $scope.DeliveryTruckCompartments[array_key].bin_two_color == bin_two_color.hex_color){*/
			if($scope.DeliveryTruckCompartments[array_key].bin_one_color == bin_one_color.hex_color){	
				// add the bin color
				//$scope.DeliveryTruckCompartments[array_key].bin_one_color = bin_one_color.hex_color;
				//$scope.DeliveryTruckCompartments[array_key].bin_two_color = bin_two_color.hex_color;
				$scope.DeliveryTruckCompartments[array_key].bin_one_color = "none";
				$scope.DeliveryTruckCompartments[array_key].bin_one_number = 0;
				//$scope.DeliveryTruckCompartments[array_key].bin_two_number = 0;
				$scope.DeliveryTruckCompartments[array_key].bins_amount = 0;
				$scope.DeliveryTruckCompartments[array_key].capacity = 0;
				$scope.DeliveryTruckCompartments[array_key].compartment_id = 0;
				$scope.DeliveryTruckCompartments[array_key].farm_id = 0;
			} else {
				// add the bin color
				$scope.DeliveryTruckCompartments[array_key].bin_one_color = bin_one_color.hex_color;
				$scope.DeliveryTruckCompartments[array_key].farm_id = DeliveryFarm.farm_id;
				$scope.DeliveryTruckCompartments[array_key].bin_one_number = Number(DeliveryFarm.bin_one);
				//$scope.DeliveryTruckCompartments[array_key].bin_two_number = DeliveryFarm.bin_two;
				$scope.DeliveryTruckCompartments[array_key].bins_amount = DeliveryFarm.amount;
				$scope.DeliveryTruckCompartments[array_key].batch = DeliveryFarm.batch_code;
			}
			
			
			$scope.DeliveryTruckCompartments;
		}
		
		
		console.log($scope.DeliveryTruckCompartments);
		
	}
	
	
	/*
	*	initializeBins()
	*	Initialize the bins data
	*	param: farm_id,bin_number
	*/
	$scope.initializeBins = function(DeliveryFarm){
		//console.log(BinsData.bin_one);
		DeliveryFarm.binonecolor = {};
		$http({
			method:"post",
			url: "http://feeds.carrierinsite.com/schedule/bins?farm_id="+DeliveryFarm.farm_id+"&bin_id="+DeliveryFarm.bin_id
		}).success(function (r){
			DeliveryFarm.binonecolor = r[0];
			console.log(DeliveryFarm);
		});
		
	}
	
	/*
	*	createLoad()
	*	Make the load creation
	*/
	$scope.createLoad = function(){
		//console.log($scope.DeliveryTruckCompartments);
		//MainServices.storeTruckLoads($scope,$scope.DeliveryTruckCompartments);
		
		var DriverNoti = {
				'truck_id' 			:	$scope.DeliveryTruckCompartments[0].truck_id,
				'driver_id'			:	$scope.DeliveryTruckCompartments[0].driver_id,
				'date_of_delivery'	:	$scope.DeliveryTruckCompartments[0].date_of_delivery
			}
		
		//console.log($scope.DeliveryTruckCompartments[0].truck_id,$scope.DeliveryTruckCompartments[0].date_of_delivery);
		MainServices.sendDriverNotification($scope,DriverNoti);
		MainServices.sendFarmNotification($scope,$scope.DeliveryTruckCompartments);
		console.log($scope.DeliveryTruckCompartments);
	}
	
	/*
	*	cancelLoad()
	*
	*/
	$scope.cancelLoad = function(){
		
		angular.forEach($scope.DeliveryTruckCompartments, function(value,key){
			
				$scope.DeliveryTruckCompartments[key].bin_one_color = "none";
				$scope.DeliveryTruckCompartments[key].bin_two_color = "none";
				$scope.DeliveryTruckCompartments[key].bin_one_number = 0;
				$scope.DeliveryTruckCompartments[key].bin_two_number = 0;
				$scope.DeliveryTruckCompartments[key].bins_amount = 0;
				$scope.DeliveryTruckCompartments[key].capacity = 0;
				$scope.DeliveryTruckCompartments[key].compartment_id = 0;
				$scope.DeliveryTruckCompartments[key].farm_id = 0;
			
		});
		
	}
	
	
	/*
	*	cancelLoad()
	*/
	$scope.loadingInit = function(id){
		MainServices.readFarmDelivery($scope,id);
	}
	 
});