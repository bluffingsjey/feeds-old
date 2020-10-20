<?php

namespace App\Http\Controllers;

use DB;
use Input;
use Auth;
use App\Farms;
use App\Bins;
use App\User;
use App\Truck;
use App\Farmer;
use App\Deliveries;
use App\FarmSchedule;
use App\FeedTypes;
use App\FarmDelivery;
use App\Compartments;
use App\MobileNotification;
use App\CreateLoadLoadoutBins;
use App\CreateLoadCompartments;
use App\SchedTool;
use App\Http\Requests;
use Validator;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Session;
use Cache;
use Artisan;
class ScheduleController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth',['except' => ['schedToolStatusUpdate','scheduleCache']]);
	}

	/*
	*	Create Delivery
	*/
	public function createDelivery(){
		$truck = Truck::lists('name','truck_id');
		return view("scheduling.create", compact("truck"));
	}

	/*
	*	validator
	*/
	public function addFarm(){
		$rules = array(
			'delivery_date'		=> 'required',
			'delivery_time'		=> 'required',
			'time_of_the_day'	=> 'required',
			'delivery_truck'	=> 'required'
		);
		$validator = Validator::make(Input::all(), $rules);

		// check if the validator failed -----------------------
		if ($validator->fails()) {

			// get the error messages from the validator
			$messages = $validator->messages();

			// redirect our user back to the form with the errors from the validator
			return Redirect::to('scheduling')
				->withErrors($validator);

		} else {
			$data = Input:: all();
			$delivery_truck = $data['delivery_truck'];
			$delivery_datetime = date('m-d-Y h:i a',strtotime($data['delivery_date'] . $data['delivery_time'] . $data['time_of_the_day']));
			$date_string = strtotime($data['delivery_date'] . $data['delivery_time'] . $data['time_of_the_day']);
			$farms = DB::table('feeds_farms')->get();
			$ctrl = new ScheduleController;
			return view('scheduling.addfarms',compact("date_string","delivery_datetime","farms","ctrl","delivery_truck"));
		}
	}

	/*
	*	pigsSUm()
	* 	Get the sum of a farm.
	*/
	public function pigsSum($farm_id){
		$pigs = DB::table('feeds_bins')->where('farm_id','=',$farm_id)->sum('num_of_pigs');
		return $pigs;
	}

	/*
	*	binsCount()
	*	Get the total bins of a farm.
	*/
	public function binsCount($farm_id){
		$bins = DB::table('feeds_bins')->where('farm_id','=',$farm_id)->count('bin_id');
		return $bins;
	}

	/*
	*	finalizeSchedule()
	*	finalize schedule data
	*/
	public function finalizeSchedule(Request $request){
		$data = Input::all();
		$delivery_datetime = $data['delivery_datetime'];
		$delivery_truck = DB::table('feeds_truck')->select('feeds_truck.name')->where('truck_id','=',$data['delivery_truck'])->first();
		$delivery_truck = $delivery_truck->name;
		$date_string = $data['date_string'];
		$farm_id = array_slice($data,4);

		$farms = array();
		foreach($farm_id as $key=>$val){
			$farms[$key] = DB::table('feeds_farms')->select('name')->where('id','=',$val)->get();
		}

		$data = array_slice($data,1);
		if(null!==($request->session()->get('schedData',$data))){
			$request->session()->put('schedData',$data);
		}

		return view('scheduling.finalizesched',compact("delivery_datetime","delivery_truck","farms","date_string"));
	}

	/*
	*	saveSchedule()
	*	Save the scheduled data
	*/
	public function saveSchedule(Request $request){
		$scheduled_items = $request->session()->get('schedData');
		$schedItems = array_slice($scheduled_items,3);

		$farms = "";
		foreach($schedItems as $key => $val){
			$farms .= ",".$val;
		}

		$farms = preg_replace('/,/','',$farms,1);

		$batch = array(
						'date_of_delivery'	=>	date('Y-m-d H:i:s',$scheduled_items['date_string']),
						'truck_id'			=>	$scheduled_items['delivery_truck'],
						'farm_id'			=>	$farms
					);

		FarmSchedule::insert($batch);

		flash()->overlay("Farms Schedule successfully saved! You can now go to Create Load page to view scheduled list.", "H&H Farms");

		return Redirect::to('scheduling');
	}

	/*
	*	createLoad
	*/
	public function createload(){

		$uniqueId = Input::get('unique_id');

		$truckId = Input::get('truck_id');

		$truckData = DB::table('feeds_truck')
						->where('truck_id','=',$truckId)
						->first();

		$scheduleList = FarmSchedule::where('unique_id','=',$uniqueId)
							->orderBy('schedule_id','asc')
							->get()->toArray();

		$date_of_sched = $this->farmDeliveryDate($uniqueId);
		$date_of_sched = $scheduleList[0]['date_of_delivery'];

		$schedData = $this->schedData($scheduleList);

		$feedType = $this->feedTypesLists();

		$amount = $this->capacity();

		$drivers = $this->driversLists();

		$medication = $this->medicationsLists();

		$farmsLists = $this->farmsLists();

		$ticketsBinLoadOut = $this->ticketsListBinLoadOut($scheduleList);

		$ctrl = new ScheduleController;

		$colors = $this->loadoutBinColor();

		$totalTons = number_format($this->totalTonsToLoad($schedData));

		return view('loading.create',compact("farmsLists","feedType","totalTons","medication","amount","ticketsBinLoadOut","ctrl","truckData","schedData","date_of_sched","drivers","colors"));
	}

	/*
	*	createLoad version two
	*/
	public function createLoadVerTwo(){

		DB::table('feeds_sched_tool_unique_id')->delete();

		$unique_id = DB::table('feeds_sched_tool_unique_id')->first();
		if($unique_id != NULL) {
			$uniqueId = $unique_id->unique_id;
			$truckId = $unique_id->truck_id;
		}else {
			$uniqueId = Input::get('unique_id');
			$truckId = Input::get('truck_id');
		}

		DB::table('feeds_sched_tool_unique_id')->insert(['truck_id'=>$truckId,'unique_id'=>$uniqueId]);

		// update the time for create load
		$this->updateFarmSchedule($uniqueId);

		$truckData = DB::table('feeds_truck')
						->where('truck_id','=',$truckId)
						->first();

		$scheduleList = FarmSchedule::where('unique_id','=',$uniqueId)
							->orderBy('schedule_id','asc')
							->get()->toArray();

		$date_of_sched = $this->farmDeliveryDate($uniqueId);
		$date_of_sched = $scheduleList[0]['date_of_delivery'];

		$schedData = $this->schedData($scheduleList);

		$feedType = $this->feedTypesLists();

		$amount = $this->capacity();

		$drivers = $this->driversLists();

		$medication = $this->medicationsLists();

		$farmsLists = $this->farmsLists();

		$ticketsBinLoadOut = $this->ticketsListBinLoadOut($scheduleList);

		$ctrl = new ScheduleController;

		$colors = $this->loadoutBinColor();

		$totalTons = number_format($this->totalTonsToLoad($schedData));

		$loadoutBins = $this->loadOutBins();

		$truck_compts = $this->getTruckCompts($truckId);

		return view('loading.createnewversion',compact("farmsLists","feedType","totalTons","medication","amount","ticketsBinLoadOut","ctrl","truckData","schedData","date_of_sched","drivers","colors","loadoutBins","truck_compts"));
	}

	/*
	*	saveChangeDateSchedEdited
	*/
	public function saveChangeDateSchedAPI($user,$unique_id,$selected_date)
	{
		// fetch the selected_date and time from the feeds_farm_schedule
		$farm_sched_data = FarmSchedule::select('date_of_delivery','delivery_unique_id','bin_id')->where('unique_id',$unique_id)->get()->toArray();

		$date = date("Y-m-d",strtotime($selected_date));
		$time = date("H:i:s",strtotime($farm_sched_data[0]['date_of_delivery']));
		$datetime = date("Y-m-d H:i:s",strtotime($date.$time));

		$updated_date_of_delivery = array('date_of_delivery' => $datetime, 'user_id' => $user);
		$updated_sched_tool_date_of_delivery = array('delivery_date'=>$datetime);
		$updated_date_of_delivery_deliveries_table = array('delivery_date'=>$datetime, 'user_id' => $user);

		FarmSchedule::where('unique_id',$unique_id)->update($updated_date_of_delivery);
		SchedTool::where('farm_sched_unique_id',$unique_id)->update($updated_sched_tool_date_of_delivery);

		if($farm_sched_data[0]['delivery_unique_id'] != NULL){
			$this->updateFarmScheduledDeliveries($farm_sched_data[0]['delivery_unique_id'],$updated_date_of_delivery_deliveries_table);
			$this->updateCreatedLoadAPI($farm_sched_data[0]['delivery_unique_id'],$user);
			for($i=0; $i<count($farm_sched_data); $i++){
				Cache::forget('bins-'.$farm_sched_data[$i]['bin_id']);
			}
		}



		return "success";

	}

	/*
	*	saveChangeDateSched
	*/
	public function saveChangeDateSched()
	{
		$date = date("Y-m-d",strtotime(Input::get('selected_data')));
		$time = date("H:i:s",strtotime(Input::get('delivery_time')));
		$datetime = date("Y-m-d H:i:s",strtotime($date.$time));

		$updated_date_of_delivery = array('date_of_delivery' => $datetime);
		$updated_sched_tool_date_of_delivery = array('delivery_date'=>$datetime);

		$unique_id = Input::get('unique_id');

		FarmSchedule::where('unique_id','=',$unique_id)->update($updated_date_of_delivery);
		SchedTool::where('farm_sched_unique_id',$unique_id)->update($updated_sched_tool_date_of_delivery);

		$sched_data = FarmSchedule::where('unique_id',$unique_id)->get()->toArray();

		return $sched_data;
		if($sched_data != NULL){
			foreach($sched_data as $k => $v){
				Cache::forget('bins-'.$v['bin_id']);
			}
		}


	}

	/*
	*	saveChangeDateSchedEdited
	*/
	public function saveChangeDateSchedEdited()
	{
		$date = date("Y-m-d",strtotime(Input::get('selected_data')));
		$time = date("H:i:s",strtotime(Input::get('delivery_time')));
		$datetime = date("Y-m-d H:i:s",strtotime($date.$time));

		$updated_date_of_delivery = array('date_of_delivery' => $datetime);
		$updated_sched_tool_date_of_delivery = array('delivery_date'=>$datetime);

		$unique_id = Input::get('unique_id');

		FarmSchedule::where('delivery_unique_id',$unique_id)->update($updated_date_of_delivery);
		SchedTool::where('delivery_unique_id',$unique_id)->update($updated_sched_tool_date_of_delivery);

		$sched_data = FarmSchedule::where('delivery_unique_id',$unique_id)->get()->toArray();
		if($sched_data != NULL){
			$this->updateFarmScheduledDeliveries($unique_id,$updated_sched_tool_date_of_delivery);
			foreach($sched_data as $k => $v){
				Cache::forget('bins-'.$v['bin_id']);
			}
		}


	}

	/*
	*	update the farm schedule time
	*/
	private function updateFarmScheduledDeliveries($unique_id,$date_of_delivery){

		Deliveries::where('unique_id',$unique_id)->update($date_of_delivery);

	}



	/*
	*	update the farm schedule time
	*/
	private function updateFarmSchedule($unique_id){
		$sched_tool = SchedTool::where('farm_sched_unique_id','=',$unique_id)->get()->toArray();
		$farm_sched = FarmSchedule::where('unique_id','=',$unique_id)->get()->toArray();

		$start_time = $sched_tool[0]['start_time'];
		$date_of_delivery = date("Y-m-d",strtotime($farm_sched[0]['date_of_delivery']));

		$updated_date_of_delivery = array('date_of_delivery' => date("Y-m-d H:i:s",strtotime($date_of_delivery." ".$start_time)));

		FarmSchedule::where('unique_id','=',$unique_id)->update($updated_date_of_delivery);
	}

	/*
	*	loadout bins select menu counter
	*/
	public function loadoutBinsCounter($amount){

		$output = ceil($amount/11.5);

		return $output;

	}

	/*
	*	loadout bins selected menus
	*/
	public function selectedLoadoutBins($sched_id){

		$loadutBins = DB::table("feeds_create_load_loadout")->where('sched_id','=',$sched_id)->get();
		return $loadutBins;

	}

	/*
	*	compartment counter
	*/
	public function compartmentCounter($amount){

		$output = ceil($amount/3);

		return $output;
	}

	/*
	*	Loadout Bins
	*/
	private function loadOutBins(){

		$data = array(
			''		=>	'-',
			'1'		=>	'1',
			'2'		=>	'2',
			'3'		=>	'3',
			'4'		=>	'4',
			'5'		=>	'5',
			'6'		=>	'6',
			'7'		=>	'7',
			'8'		=>	'8',
			'9'		=>	'9',
			'10'	=>	'10',
			'11'	=>	'11',
			'12'	=>	'12'
		);

		return $data;

	}

	/*
	*	Get truck compartments without chunks
	*/
	public function getTruckCompts($truck_id){

		$compartments = Compartments::where('truck_id','=',$truck_id)->lists('compartment_number','compartment_number')->toArray();

		$compartments = array(''=>'-')+$compartments;

		return $compartments;

	}

	/*
	*	date of delivery
	*/
	private function farmDeliveryDate($uniqueId){
		$output = FarmSchedule::select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%b %d, %p") as date_of_delivery'))
			->where('unique_id','=',$uniqueId)
			->get();
		return $output;
	}

	/*
	*	Scheduled Data List
	*/
	private function schedData($data){
		$schedData = array();
		$counter = count($data) - 1;

		for($i=0; $i<=$counter; $i++){
			$schedData[] = array(
				'schedule_id'		=>	$data[$i]['schedule_id'],
				'date_of_delivery'	=>	$data[$i]['date_of_delivery'],
				'truck_id'			=>	$data[$i]['truck_id'],
				'bin_id'			=>	$data[$i]['bin_id'],
				'driver_id'			=>	$data[$i]['driver_id'],
				'medication_id'		=>	$data[$i]['medication_id'],
				'amount'			=>	$data[$i]['amount'],
				'feeds_type_id'		=>	$data[$i]['feeds_type_id'],
				'unique_id'			=>	$data[$i]['unique_id'],
				'farm_id'			=>	$data[$i]['farm_id'],
				'ticket'			=>	$data[$i]['ticket'],
				'farm_name'			=>	$this->getFarmNames($data[$i]['farm_id']),
				'truck_name'		=>	$this->truckName($data[$i]['truck_id']),
				'bin_name'			=>	$this->getSpecificBin($data[$i]['bin_id'])->alias,
				'bin_number'		=>	$this->getSpecificBin($data[$i]['bin_id'])->bin_number,
				'driver_name'		=>	$this->getDriver($data[$i]['driver_id']),
				'medication_name'	=>	$this->medicationName($data[$i]['medication_id']),
				'feed_name'			=>	$this->feedsNameDisplay($data[$i]['feeds_type_id'])
			);
		}

		return $schedData;
	}

	/*
	*	Total Tons to be loaded
	*/
	private function totalTonsToLoad($data){
		$total = 0;
		$counter = count($data) - 1;

		for($i=0; $i<=$counter; $i++){
			$total += $data[$i]['amount'];
		}
		return $total;
	}

	/*
	*	tickets lists for bin loadout
	*/
	private function ticketsListBinLoadOut($data){
		$tickets = array();
		$counter = count($data) - 1;

		for($i=0; $i<=$counter; $i++){
			$tickets[$data[$i]['ticket']] = $data[$i]['ticket'];
		}
		return $tickets;
	}


	/*
	*	Get truck compartments
	*/
	public function getTruckCompartments($truck_id){
		$compartments = Compartments::where('truck_id','=',$truck_id)->get()->toArray();
		return array_chunk($compartments,6);

	}

	/*
	*	Farms
	*/
	private function getFarmNames($farm_id){

		$farm = Farms::where('id','=',$farm_id)
					->select('name')
					->first();
		return !empty($farm->name) ? $farm->name : "-";

	}

	/*
	*	Farms Lists
	*/
	private function farmsLists(){

		$farmsLists = Farms::orderBy('name')->lists('name','id')->toArray();

		$farmsLists = array(''=>'Please Select')+$farmsLists;

		return $farmsLists;

	}

	/*
	*	Feed type lists
	*/
	public function feedTypesLists(){

		$feedTypes = DB::table('feeds_feed_types')
						->where('name','!=','None')
						->orderBy('name')
						->lists('name','type_id');

		$feedTypes = array(''=>'Please Select')+$feedTypes;

		return $feedTypes;
	}

	/*
	*	Medications lists
	*/
	private function medicationsLists(){

		$medication = DB::table('feeds_medication')
						->orderBy('med_name')
						->where('med_id','!=',0)
						->lists('med_name','med_id');

		$medication = array(''=>'Please Select')+$medication;

		return $medication;
	}

	/*
	*	get Drivers lists
	*/
	private function driversLists(){
		$drivers = DB::table('feeds_user_accounts')
					->select('*')
					->where('type_id','=',2)
					->orderBy('username')
					->lists('username','id');
		return $drivers;
	}

	/*
	*	Get the specific Bin Number
	*/
	private function getSpecificBin($id){
		$bins = Bins::where('bin_id','=',$id)
				->first();

		if(!empty($bins->alias)){

			return $bins;

		}else{

			return (object)array(
				'alias'	=>	'-',
				'bin_number' => '-'
			);

		}
	}


	/*
	*	Get the truck name
	*/
	private function truckName($id){

		$truck = Truck::where("truck_id","=",$id)
				->first();
		return $truck->name;
	}

	/*
	*	Batch Code Generator
	*/
	public function batchCodeGen($word){
		$words = explode(" ",$word);
		$acronym = "";

		foreach($words as $w){
			$acronym .= $w[0];
		}

		return $acronym."001";
	}

	/*
	*	Bins
	*/
	public function binsNumber($farmId=NULL){
		$farmId = (isset($farmId)) ? $farmId : Input::get('id');
		$bins = DB::table('feeds_bins')
					->where('farm_id','=',$farmId)
					->lists('alias','bin_id');
		return $bins;
	}

	/*
	*	Bins
	*/
	public function binsNumberAjax(){
		$farmId = Input::get('id');
		$bins_id = Input::get('bins_id');
		$bins = DB::table('feeds_bins')
					->select('bin_number','bin_id')
					->where('farm_id','=',$farmId)
					//->whereNotIn('bin_id',$bins_id)
					->get();
		return $bins;
	}

	/*
	*	Amount of the truck capacity
	*/
	private function capacity()
	{
		/*$data = array();
		for($i=1;$i<=20;$i+=1){
			$amount = strval($i) . " Tons";
			if($i == 1){
				$amount = strval($i) . " Ton";
				$data[$amount] = $i . " Ton";
			} else {
				$data[$amount] = $i . " Tons";
			}
		}*/
		$data = array(
			'0.5'	=>	'0.5 Ton',
			'1.0'	=>	'1.0 Ton',
			'1.5' => 	'1.5 Tons',
			'2.0' =>	'2.0 Tons',
			'2.5'	=>	'2.5 Tons',
			'3.0'	=>	'3.0 Tons'
		);
		return $data;
	}


	/*
	*	loadinglist()
	*/
	public function loadingList(){

		//$farm_sched_list = Cache::get('scheduling_data_1st_load');
		Cache::forget('scheduling_data_1st_load_ajax');
		//$this->scheduleCache();
		$drivers = Cache::store('file')->get('drivers');
		$data = Cache::get('scheduling_data_1st_load_ajax');


		$farm_sched_list = DB::table('feeds_farm_schedule')
							->select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%Y-%m-%d %h:%i:%s %p") as date_of_delivery'),
									'schedule_id','feeds_type_id','medication_id','unique_id','delivery_unique_id',
									DB::raw('GROUP_CONCAT(farm_id) AS farm_id'),
									DB::raw('GROUP_CONCAT(amount) AS amount'),
									DB::raw('GROUP_CONCAT(bin_id) AS bin_id'),
									'feeds_truck.name as truck_name',
									'feeds_truck.truck_id as truck_id',
									'feeds_farm_schedule.driver_id as driver_id')
							->leftJoin('feeds_truck','feeds_truck.truck_id','=','feeds_farm_schedule.truck_id')
							->where('status','=',0)
							->where('feeds_farm_schedule.date_of_delivery',date("Y-m-d")."%")
							->orderBy('date_of_delivery','desc')
							->groupBy('feeds_farm_schedule.unique_id')
							->get();

		$drivers = User::where('type_id','=',2)->orderBy('username')->lists("username","id");

		$data = array();
		for($i = 0; $i < count($farm_sched_list); $i++){
			$data[] = (object)array(
				'schedule_id'		=>	$farm_sched_list[$i]->schedule_id,
				'delivery_date'		=>	$this->dateFormat($farm_sched_list[$i]->date_of_delivery),
				'farm_name'			=>	$this->farmNames($farm_sched_list[$i]->farm_id,$farm_sched_list[$i]->date_of_delivery,$farm_sched_list[$i]->unique_id),
				'truck_name'		=>	$farm_sched_list[$i]->truck_name,
				'truck_id'			=>	$farm_sched_list[$i]->truck_id,
				'driver'			=>	$this->getDriver($farm_sched_list[$i]->driver_id),
				'unique_id'			=>	$farm_sched_list[$i]->unique_id,
			);
		}


		return view('loading.index',compact("data","drivers"));

	}

	/**
   * Convert object to array
   *
   * @return Response
   */
  private function toArray($data)
  {
		$resultArray = json_decode(json_encode($data), true);

		return $resultArray;
	}

	/*
	*	Scheduling tool status
	*/
	public function scheduleCache()
	{

		Cache::forget('scheduling_data_1st_load_ajax');

		$farm_sched_list = DB::table('feeds_farm_schedule')
							->select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%Y-%m-%d %h:%i:%s %p") as date_of_delivery'),
									'schedule_id','feeds_type_id','medication_id','unique_id','delivery_unique_id',
									DB::raw('GROUP_CONCAT(farm_id) AS farm_id'),
									DB::raw('GROUP_CONCAT(amount) AS amount'),
									DB::raw('GROUP_CONCAT(bin_id) AS bin_id'),
									'feeds_truck.name as truck_name',
									'feeds_truck.truck_id as truck_id',
									'feeds_farm_schedule.driver_id as driver_id')
							->leftJoin('feeds_truck','feeds_truck.truck_id','=','feeds_farm_schedule.truck_id')
							->where('status','=',0)
							->where('feeds_farm_schedule.date_of_delivery',date("Y-m-d")."%")
							->orderBy('date_of_delivery','desc')
							->groupBy('feeds_farm_schedule.unique_id')
							->get();

		$data = array();
		for($i = 0; $i < count($farm_sched_list); $i++){
			$data[] = (object)array(
				'schedule_id'		=>	$farm_sched_list[$i]->schedule_id,
				'delivery_date'		=>	$this->dateFormat($farm_sched_list[$i]->date_of_delivery),
				'farm_name'			=>	$this->farmNames($farm_sched_list[$i]->farm_id,$farm_sched_list[$i]->date_of_delivery,$farm_sched_list[$i]->unique_id),
				'truck_name'		=>	$farm_sched_list[$i]->truck_name,
				'truck_id'			=>	$farm_sched_list[$i]->truck_id,
				'driver'			=>	$this->getDriver($farm_sched_list[$i]->driver_id),
				'unique_id'			=>	$farm_sched_list[$i]->unique_id,
			);
		}

		$data = $this->toArray($data);
		// cache data via sort type a-z farms
		usort($data, function($a,$b){
			return strcasecmp($a["farm_name"], $b["farm_name"]);
		});

		Cache::forever('scheduling_data_1st_load_ajax',$data);


		return "done";

	}

	/*
	*	Scheduling tool status
	*/
	private function schedToolStatus($unique_id){
		$data = SchedTool::select('status')->where('delivery_unique_id',$unique_id)->first();
		if($data == NULL){
			return "none";
		}
		return $data->status;
	}

	/*
	*	get the delivery time of the farm
	*/
	private function farmDeliveryTimes($farms){

		$data = "";
		$farm = array_unique(explode(",",(string)$farms));

		/*foreach($farm as $k => $v){
			$farm_data = Farms::where('id','=',$v)->get()->toArray();
			$farm_name = !empty($farm_data[0]['name']) ? $farm_data[0]['name'] : "";
			$delivery_time = !empty($farm_data[0]['delivery_time']) ? $farm_data[0]['delivery_time'] : 0;
			$data .= $farm_name . " <strong class='ton_vw_sched_kb'> (".$delivery_time." Hour/s)</strong><br/>";
		}*/

		$output = Farms::select('delivery_time')->whereIn('id',$farm)->max('delivery_time');

		$counter = count($farm);
		$return = 0;
		if($counter == 1){
			$return = number_format((float)$output, 2, '.', '');
		} else{
			$added_minutes =  0.50 * ($counter - 1);
			$final = $output + $added_minutes;
			$return = number_format((float)$final, 2, '.', '');
		}

		$output = "<strong class='ton_vw_sched_kb'> (". $return ." Hour/s)</strong><br/>";

		return $output;

	}

	/*
	*	get specific bin info
	*/
	private function binInformation($bin_id){
		$data = Bins::select('bin_number')->where('bin_id','=',$bin_id)->first();
		$output = !empty($data->bin_number) ? $data->bin_number : "";

		return $output;
	}

	/*
	* 	get driver
	*/
	private function getDriver($driver){

		if($driver != 0){
			$driver = DB::table('feeds_user_accounts')
					->select('username','id')
					->where('id','=',$driver)
					->first();
			$output = !empty($driver->username) ? array($driver->id,$driver->username) : array("-","-");
		} else {
			$output = array("-","-");
		}

		return $output;
	}

	/*
	*	compartmentLoading
	*/
	public function compartmentLoading(){

		$data = array_slice(Input::all(),1);
		$truck_driver = Input::get('truck_driver');
		$truck_id = Input::get('truck_id');
		$compartments = DB::table('feeds_truck_compartment')
								->where('truck_id','=',Input::get('truck_id'))
								->orderBy('compartment_number')
								->get();
		$schedData = array();

		$cnt = (count(Input::get('farmId')) - 1);

		for($i=0; $i <= $cnt; $i++){
			$schedData[] = (object)array(
				'farmId'		=>	$data['farmId'][$i],
				'farmName'		=>	$data['farmName'][$i],
				'batch'			=>	$data['batch'][$i],
				'feedType'		=>	$data['feedType'][$i],
				'medId'			=>	$data['medication'][$i],
				'feedName'		=>	$this->feedsNameDisplay($data['feedType'][$i]),
				'medName'		=>	$this->medicationName($data['medication'][$i]),
				'amount'		=>	$data['amount'][$i],
				'bins'			=>	$data['bins'][$i],
				'bins_number'	=>	$this->binsNumberDisplay($data['bins'][$i]),
				'alias'			=>	$this->binsAliasDisplay($data['bins'][$i])
			);
		}

		$schedule_id = Input::get('schedule_id');
		dd($schedData);
		$ctrl = new ScheduleController;
		return view('loading.compartment',compact("ctrl","schedule_id","truck_driver","schedData","compartments","truck_id"));
	}

	/*
	*	feedsName
	*/
	private function feedsNameDisplay($feed_id){
		$bins = DB::table('feeds_feed_types')
					->select("name")
					->where('type_id','=',$feed_id)
					->first();
		$output = !empty($bins->name) ? $bins->name : "-";
		return $output;
	}

	/*
	*	Medicaiton Name
	*/
	private function medicationName($med_id) {

		$output = "";

		if($med_id != 0 || $med_id != NULL){
			$med = DB::table('feeds_medication')
					->select('med_name')
					->where('med_id','=',$med_id)
					->first();
			$output = $med->med_name;
		} else{
			$output = "-";
		}

		return $output;
	}

	/*
	*	binsNumber
	*/
	private function binsNumberDisplay($bin_id){
		$bins = DB::table('feeds_bins')
					->select("bin_number")
					->where('bin_id','=',$bin_id)
					->first();
		return $bins->bin_number;
	}

	/*
	*	binsAlias
	*/
	private function binsAliasDisplay($bin_id){
		$bins = DB::table('feeds_bins')
					->select("alias")
					->where('bin_id','=',$bin_id)
					->first();
		return $bins->aslias;
	}

	/*
	*	binsColor
	*/
	public function getBinsColor($bin_id){
		$bins = DB::table('feeds_bins')
					->select("hex_color")
					->where('bin_id','=',$bin_id)
					->first();
		return $bins->hex_color;
	}

	/*
	*	get farm names
	*/
	private function farmNames($farms,$delivery_date,$unique_id){
		$data = "";
		$farm = array_unique(explode(",",(string)$farms));

		foreach($farm as $k => $v){
			$farm_name = $this->farmNamesQuery($farm[$k]);
			$amount = $this->totalTonsFarmSched($farm[$k],date("Y-m-d H:i:s",strtotime($delivery_date)),$unique_id);
			$bins = $this->getScheduledBins($unique_id,$farm[$k]);
			$data .= $farm_name . "<br/> <strong> ".$bins."</strong> <br/><strong class='ton_vw_sched_kb'></strong>";
		}

		return 	$data;
	}

	/*
	*	getScheduledBins()
	*	get the bins based on scheduled items
	*
	*/
	private function getScheduledBins($unique_id,$farm_id)
	{
		$data = "";
		$scheduled_items = FarmSchedule::select(DB::raw('DISTINCT(bin_id) AS bin_id'))->where('unique_id',$unique_id)->where('farm_id',$farm_id)->get()->toArray();

		foreach($scheduled_items as $k => $v){
			$alias = Bins::select('alias')->where('bin_id',$v['bin_id'])->get()->toArray();
			$data .= !empty($alias[0]['alias']) ? $alias[0]['alias']."<strong class='ton_vw_sched_kb'> (".$this->getScheduledBinsSumAmount($unique_id,$farm_id,$v['bin_id'])." Tons)</strong><br/>" : "";
		}

		return substr($data, 0, -1);
	}

	/*
	*	getScheduledBinsSumAmount()
	*	get the bins sum amount based on scheduled items
	*
	*/
	private function getScheduledBinsSumAmount($unique_id,$farm_id,$bin_id)
	{
		$sum = FarmSchedule::where('unique_id',$unique_id)
																		->where('farm_id',$farm_id)
																		->where('bin_id',$bin_id)
																		->sum('amount');
		return $sum;
	}

	/*
	*	Farm Names Query
	*/
	private function farmNamesQuery($farm_id){
		$query = DB::table('feeds_farms')
					->select('name')
					->where('id',$farm_id)
					->first();
		return !empty($query->name) ? $query->name : "-";
	}

	/*
	*	total tons in farm schedule
	*/
	private function totalTonsFarmSched($farm_id,$delivery_date,$unique_id){

		$amount = FarmSchedule::where('farm_id',$farm_id)
								->where('date_of_delivery',"LIKE",$delivery_date."%")
								->where('unique_id',$unique_id)
								->sum('amount');

		return $amount;

	}


	/*
	*	save compartment selection
	*/
	public function saveCompartmentSelection(){
		$data = Input::all();

		$notification = new CloudMessaging;

		$truck_driver = Input::get('truck_driver');

		$sched_id = $data['schedule_id'];
		$sched_date = DB::table('feeds_farm_schedule')
				->select('feeds_farm_schedule.date_of_delivery')
				->where('schedule_id','=',$sched_id)
				->first();

		// update the status of scheduled item
		/*DB::table('feeds_farm_schedule')
			->where('schedule_id','=',$sched_id)
			->update(['status'=>1]);
		*/

		// Detect the compartments that did'nt have any attached bins
		$sched_date = $sched_date->date_of_delivery;

		$data = array_slice($data,3);
		$data = array_chunk($data,8);

		/*"compartment_1" => "1"
		  "batch_8" => "PM001"
		  "truck_id_8" => "40"
		  "farmId_8" => "11"
		  "feedType_8" => "1"
		  "amount_8" => "1 Ton"
		  "bins_id_8" => "153"*/
		  $output = array();
		foreach($data as $key => $val){
			//$key = preg_replace('/[0-9]+/','',$key);
			//$key = preg_replace('/[_]+/','',$key);

			$output[] = array(
				'delivery_date' 		=> 	$sched_date,
				'truck_id'				=>	$data[$key][2],
				'farm_id'				=>	$data[$key][3],
				'feeds_type_id'			=>	$data[$key][4],
				'medication_id'			=>	$data[$key][5],
				'user_id'				=>	Auth::id(),
				'driver_id'				=>	$truck_driver,
				'batch_code'			=>	$data[$key][1],
				'amount'				=>	$data[$key][6],
				'bin_id'				=>	$data[$key][7],
				'compartment_number'	=>	$data[$key][0],
				//'status'				=>	0,
				'created_at'			=>	date('Y-m-d H:i:s'),
				'updated_at'			=>	date('Y-m-d H:i:s')
			);



		}


		//$output = array_chunk($output,7);
		//$output = count($output[]['compartment']);
		if(Deliveries::insert($output)){

			$driver_data = array(
				'driver_id'	=>	$truck_driver,
				'truck_id'	=>	$output[0]['truck_id'],
				'date_of_delivery'	=>	$output[0]['delivery_date']
			);

			$notification->driverMessaging($driver_data);


			foreach($output as $k => $v){
				$farmer_data = array('farm_id'=>$v['farm_id'],'date_of_delivery'=>$v['delivery_date']);
				$notification->farmerLoadedTruckMessaging($farmer_data);
			}

			unset($notification);
		}

		//flash()->overlay("Truck loading completed! You can now view the status of the delivery list.", "H&H Farms");

		return Redirect::to('deliveries');

	}

	/*
	*	date format
	*/
	public function dateFormat($date){
		return date('M d',strtotime($date));
	}

	/*
	*	Load Delivery

	public function loadDelivery($id){
		return view("loading.load",compact("id"));
	}
	*/

	public function ajax(){

		if(Request::ajax()) {

			/*$data = Input::all();

			session()->forget('dateS');

			session()->put('dateS', $data['datepicker']);

			return view('scheduling/step2');*/
			dd(Input::all());

		}

	}

	public function upload(){

		//return $_FILES['file']['tmp_name'];

		if (is_uploaded_file($_FILES['file']['tmp_name'])) {
			$uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/mobile/';
			$tmp_name = $_FILES['file']['tmp_name'];
			$pic_name = $_FILES['file']['name'];
			move_uploaded_file($tmp_name, $uploads_dir.$pic_name);
			return "Uploaded successfully.";
			//return $_FILES['file']['tmp_name'];
		 } else {
			return "File not uploaded.";
		 }
		 // getting all of the post data
		/*$file = array('image' => Input::file('image'));
		// setting up rules
		$rules = array('image' => 'required',); //mimes:jpeg,bmp,png and for max size max:10000
		// doing the validation, passing post data, rules and the messages
		$validator = Validator::make($file, $rules);
		if ($validator->fails()) {
			// send back to the page with the input data and errors
			return Redirect::to('binstype')->withInput()->withErrors($validator);
		} else {
			// checking file is valid.
			if (Input::file('image')->isValid()) {
				$destinationPath = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/mobile/'; // upload path
				$extension = Input::file('image')->getClientOriginalExtension(); // getting image extension
				$fileName = rand(11111,99999).'.'.$extension; // renameing image
				Input::file('image')->move($destinationPath, $fileName); // uploading file to given path
				// sending back with message
				//Session::flash('success', 'Upload successfully');
				return 'Upload successfully';
			}
			else {
				// sending back with error message.
				//Session::flash('error', 'uploaded file is not valid');
				return 'uploaded file is not valid';
			}
		}*/
	}

	public function selectBins(){

		if(Request::ajax()) {

			$data = Input::all();
			$bins = $this->getBins($data['farm_id']);

			return $bins;
		}

	}

	private function getBins($farm_id) {

		if($farm_id == "default"){
			$bins = array('binsTotal' => 'default');
		} else {
			$bins = DB::table('feeds_bins')
					->select(DB::raw('COUNT(*) AS binsTotal'))
					->where('farm_id','=',$farm_id)
					->get();
		}

		return $bins;

	}


	/**
     * Schedule a date
     *
     * @return Response
     */
    public function schedule()
    {
       	return view("schedule.schedule",compact("farms"));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function selectFarm()
    {
		session()->forget('dateS');
		session()->put('dateS',Input::get('datepicker'));
		$schedDate = session()->get('dateS');

		$farms = Farms::all();

		return view("schedule.farm",compact("farms","schedDate"));
    }

	/**
     * Used to select a specific farm.
     *
     * @return Response
     */
    public function selectDriver()
    {
		dd(Input::all());
		$drivers = DB::table('feeds_user_accounts')
					->select('feeds_user_accounts.username','feeds_user_accounts.id')
					->where('type_id','=',2)
					->lists('username','id');

		return view("schedule.driver", compact("drivers"));
    }

	/**
     * Used to select a specific farm.
     *
     * @return Response
     */
    public function assignTruck()
    {
		$compartments = Truck::lists("name","truck_id");
		return view("schedule.truck", compact("compartments"));
    }

	/**
     * Used to select a specific farm.
     *
     * @return Response
     */
    public function assignCompartment()
    {
		$trucks = Truck::lists("name","truck_id");
		return view("schedule.assign", compact("trucks"));
    }

	public function addBins($farm_id)
	{
		$farm = Farms::findOrFail($farm_id);
		return view("schedule.addbins", compact("farm"));
	}

	public function addBinsAjax()
	{
		if(Request::ajax()) {

			$data = Input::all();

			$bins = new Bins;

			$bins->farm_id = $data['farm_id'];
			$bins->num_of_pigs = $data['number_of_pigs'];
			$bins->consumption = $data['consumption'];
			$bins->variance = $data['variance'];
			$bins->hex_color = $data['color'];
			$bins->user_id = Auth::id();

			$bins->save();

			$output = "success";

		} else {

			$output = "fail";

		}

		return $output;
	}


	public function trucks()
	{
		$trucks = Truck::all();
		return $trucks;
	}

	public function feedTypes()
	{
		$feedTypes = FeedTypes::all();
		return $feedTypes;
	}

	public function farms(){
		$farms = Farms::all();
		return $farms;
	}

	/*
	*	Amount of the truck capacity
	*/
	public function amounts()
	{
		$data = array();
		for($i=1;$i<=50;$i+=0.25){
			$amount = strval($i) . "Tons";
			$data[$amount] = $i . " Tons";
		}
		return $data;
	}

	/*
	*	Consumption Stripper
	*/
	private function amountTrimmer($string)
	{
		return trim($string," Tons");
	}

	/*
	*	Consumption Stripper
	*/
	private function whiteSpaceTrimmer($string)
	{
		return trim($string," ");
	}

	/*
	*	Bins of a specific farm
	*/
	public function farmBins($farm_id)
	{
		$bins = Bins::where('farm_id','=',$farm_id)->get();
		return $bins;
	}

	/*
	*	Unique ID generator
	*/
	private function generator(){

		$unique = uniqid(rand());
		$dateToday = date('ymdhms');

		$unique_id = Deliveries::where('unique_id','=',$unique)->exists();

		$output = ($unique_id == true ? $unique.$dateToday : $unique );

		return $output;

	}

	/*
	*	Add the batch scheduled data
	*/
	public function addBatch()
	{
		$data = Input::all();

		$batch = array();

		$counter = count($data);

		$truck_id = "";
		foreach($data as $k=>$v){
			if(!empty($v['truck_id'])){
				$truck_id = $v['truck_id'];
			}
		}


		$unique_id = $this->generator();

		for($i=0; $i<$counter; $i++){
			$batch[] = array(
				'delivery_date'			=>	date('Y-m-d H:i:s',strtotime(!empty($data[$i]['date_of_del']) ? $data[$i]['date_of_del'] : 0)),
				'truck_id'				=>	$truck_id,
				'driver_id'				=>	!empty($data[$i]['driver_id']) ? $data[$i]['driver_id']:0,
				'farm_id'				=>	!empty($data[$i]['farm_id']) ? $data[$i]['farm_id']:0,
				'feeds_type_id'			=>	!empty($data[$i]['feeds_type_id']) ? $data[$i]['feeds_type_id']:0,
				'user_id'				=>	Auth::id(),
				'batch_code'			=>	!empty($data[$i]['ticket']) ? $data[$i]['ticket']:0,
				'amount'				=>	$this->amountTrimmer(!empty($data[$i]['amount']) ? $data[$i]['amount']:0),
				'bin_id'				=>	!empty($data[$i]['bin_id']) ? $data[$i]['bin_id']:0,
				'medication_id'			=>	!empty($data[$i]['medication_id']) ? $data[$i]['medication_id']:0,
				'compartment_number' 	=>	!empty($data[$i]['compartment_number']) ? $data[$i]['compartment_number'] :0,
				'unique_id'				=>	$unique_id,
				'created_at'			=>	date('Y-m-d H:i:s'),
				'updated_at'			=>	date('Y-m-d H:i:s')
			);
			//update the schedule list status
			if(!empty($data[$i]['sched_id'])){
				$farmSched = FarmSchedule::find($data[$i]['sched_id']);
				$farmSched->status = 1;
				$farmSched->save();
			}
		}


		$notification = new CloudMessaging;

		$farmsIDs = array_count_values(array_column($batch, 'farm_id'));
		if(Deliveries::insert($batch)){
			$driver_data = array(
				'driver_id'			=>	$batch[1]['driver_id'],
				'truck_id'			=>	$batch[1]['truck_id'],
				'date_of_delivery'	=>	$batch[1]['delivery_date'],
				'unique_id'			=>	$unique_id
			);

			/*$farmer_data = array(
				'farm_id'	=>	$batch[1]['farm_id'],
				'date_of_delivery'=>	$v['delivery_date']
			);*/

			$notification->driverMessaging($driver_data);

			$mobileNotiData = array(
				'farm_id'			=>	NULL,
				'driver_id'			=>	$batch[1]['driver_id'],
				'user_id'			=>	$batch[1]['driver_id'],
				'date_of_delivery'	=> 	$batch[1]['delivery_date'],
				'is_readred'		=>	'false',
				'unique_id'			=>	$unique_id
			);
			$this->mobileNotiStore($mobileNotiData);

			foreach($farmsIDs as $key => $val){
				if($key != 0){
					$farmer_data = array('farm_id'=>$key,'date_of_delivery'=>$batch[1]['delivery_date'],'unique_id'=>$unique_id);
					$notification->farmerLoadedTruckMessaging($farmer_data);

					if($this->farmer($key) != NULL){
						foreach($this->farmer($key) as $k => $v){

							// send mobile notification for none gcm users
							$mobileNotiData = array(
								'farm_id'			=>	$key,
								'driver_id'			=>	1,
								'user_id'			=>	$v->user_id,
								'date_of_delivery'	=> 	$batch[1]['delivery_date'],
								'is_readred'		=>	'false',
								'unique_id'			=>	$unique_id
							);
							$this->mobileNotiStore($mobileNotiData);

						}
					}

				}
			}

			unset($notification);
		}

		//flash()->overlay("Truck loading completed! You can now view the status of the delivery list.", "H&H Farms");

		//return Redirect::to('deliveries');


	}

	/*
	*	get the farmer id
	*/
	private function farmer($farmID){
		$farmer = Farmer::where('farm_id','=',$farmID)
					->select('user_id')
					->get();

		$output = !empty($farmer) ? $farmer : NULL;

		return $output;
	}

	/*
	*	Store mobile notification
	*/
	private function mobileNotiStore($data){
		DB::table('feeds_mobile_notification')->insert($data);
	}

	/*
	*	store the scheduled data
	*/
	private function storeBatch($data){
		Deliveries::insert($data);
	}

	/*
	*	read the stored deliveries
	*/
	public function readBatch()
	{
		//Date/Time
        //Truck
        //Farm(s)
        //Delivery Time
		$batch = DB::table('feeds_deliveries')
					->join('feeds_farms', 'feeds_deliveries.farm_id', '=', 'feeds_farms.id')
					->leftJoin('feeds_truck', 'feeds_deliveries.truck_id', '=', 'feeds_truck.truck_id')
					->select('feeds_deliveries.*',
							DB::raw('GROUP_CONCAT(DISTINCT feeds_farms.name SEPARATOR ",") AS farm_name'),
							'feeds_truck.*')
					->orderBy('feeds_deliveries.delivery_date','DESC')
					->groupBy('feeds_deliveries.delivery_date')
					->get();
		return $batch;
	}

	/*
	*	loading scheduled items
	*/
    public function loadingSched($delivery_id)
	{
		$delivery = DB::table('feeds_deliveries')
					->join('feeds_farms', 'feeds_deliveries.farm_id', '=', 'feeds_farms.id')
					->leftJoin('feeds_truck', 'feeds_deliveries.truck_id', '=', 'feeds_truck.truck_id')
					->select('feeds_deliveries.*',
							DB::raw('GROUP_CONCAT(DISTINCT feeds_farms.name SEPARATOR ",") AS farm_name'),
							'feeds_truck.*')
					->where('delivery_id','=',$delivery_id)
					->orderBy('feeds_deliveries.delivery_date','DESC')
					->groupBy('feeds_deliveries.delivery_date')
					->get();

		$batch_where = array('feeds_deliveries.truck_id' => $delivery[0]->truck_id, 'delivery_date' => $delivery[0]->delivery_date);

		$batchs = DB::table('feeds_deliveries')
					->leftjoin('feeds_farms', 'feeds_deliveries.farm_id', '=', 'feeds_farms.id')
					->leftJoin('feeds_truck', 'feeds_deliveries.truck_id', '=', 'feeds_truck.truck_id')
					->leftJoin('feeds_bin_types', 'feeds_deliveries.feeds_type_id', '=', 'feeds_bin_types.type_id')
					->select('feeds_deliveries.*','feeds_farms.name','feeds_farms.id AS farm_id','feeds_truck.capacity','feeds_bin_types.name as feedsname')
					->where($batch_where)
					->get();

		$compartments = DB::table('feeds_truck_compartment')
						->leftJoin('feeds_truck', 'feeds_truck_compartment.truck_id','=','feeds_truck.truck_id')
						->select('feeds_truck_compartment.*')
						->where('feeds_truck_compartment.truck_id', '=', $delivery[0]->truck_id)
						->orderBy('feeds_truck_compartment.compartment_number','asc')
						->get();

		$deliveryLoad = array(
			'truck'			=>	$delivery,
			'farms'			=>	$batchs,
			'compartments'	=>	$compartments
		);

		return $deliveryLoad;
	}

	public function batchDelivery(){

	}

	public function binsColor(){

		$farmID = Input::get("farm_id");
		$bin_id = Input::get("bin_id");
		$where = array('feeds_bins.farm_id' => $farmID, 'feeds_bins.bin_id' => $bin_id);

		$bins = DB::table('feeds_bins')
					->select('feeds_bins.hex_color','feeds_bins.bin_number')
					->where($where)
					->get();

		return $bins;
	}

	public function binColor(){

		$farmID = Input::get("farm_id");
		$bin_number = Input::get("bin_number");
		$where = array('feeds_bins.farm_id' => $farmID, 'feeds_bins.bin_number' => $bin_number);

		$bins = DB::table('feeds_bins')
					->select('feeds_bins.hex_color','feeds_bins.bin_number')
					->where($where)
					->get();

		return $bins;
	}

	public function drivers() {

		$drivers = User::where('type_id','=',2)->get();

		return $drivers;

	}

	public function storeTruckLoads(){
		$truckLoads = Input::all();
		$truckLoad = array();
		$counter = count($truckLoads);
		for($i=0; $i<$counter; $i++){
			$truckLoad[] = array(
				'batch_code'			=>	$truckLoads[$i]['batch'],
				'driver_id'				=>	$truckLoads[$i]['truck_id'],
				'truck_id'				=>	$truckLoads[$i]['driver_id'],
				'farm_id'				=>	$truckLoads[$i]['farm_id'],
				'compartment_number'	=>	$truckLoads[$i]['compartment_number'],
				'compartment_amount'	=>	$truckLoads[$i]['capacity'],
				'bin_one_color'			=>	$truckLoads[$i]['bin_one_color'],
				'bin_two_color'			=>	$truckLoads[$i]['bin_two_color'],
				'bin_one_number'		=>	$truckLoads[$i]['bin_one_number'],
				'bin_two_number'		=>	$truckLoads[$i]['bin_two_number'],
				'bins_amount'			=>	$truckLoads[$i]['bins_amount'],
				'user_id'				=>	Auth::id(),
				'date_of_delivery'		=>	$truckLoads[$i]['date_of_delivery'],
				'created_at'			=>	date('Y-m-d H:i:s'),
				'updated_at'			=>	date('Y-m-d H:i:s')
			);
		}

		$this->saveTruckLoad($truckLoad);

		return $truckLoad;
	}


	/*
	*	store the scheduled data
	*/
	private function saveTruckLoad($data){
		FarmDelivery::insert($data);
	}


	/*
	*	Image Generator
	*/
	public function imageGenerator(){
		$im = imagecreate(30, 30);

		$R = rand(0,255);
		$G = rand(0,255);
		$B = rand(0,255);

		$background_color = imagecolorallocate($im, $R, $G, $B);

		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	}

	/*
	*	Farm Sched Data
	*/
	public function requestSchedData(){
		$sched_id = Input::get('sched_id');
		$schedule = FarmSchedule::where('schedule_id','=',$sched_id)->get()->toArray();

		$output = $this->schedData($schedule);

		return $output[0];
	}

	/*
	*	 update ticket from farm schedule
	*/
	public function updateBatch(){
		$sched_id =  Input::get('sched_id');
		$batchData['farm_id'] = Input::get('farm');
		$batchData['bin_id'] = Input::get('bins');
		$batchData['amount'] = Input::get('amount');
		$batchData['medication_id'] = Input::get('medication');
		$batchData['feeds_type_id'] = Input::get('feed_type');
		$batchData['ticket'] = Input::get('ticket');
		$batchData['unique_id'] = Input::get('unique_id');

		$truck_id = FarmSchedule::where('unique_id','=',$batchData['unique_id'])->first()->truck_id;

		$truck_capacity = Truck::where('truck_id','=',$truck_id)->first()->capacity;

		$total_amount = FarmSchedule::where('unique_id','=',$batchData['unique_id'])->where('schedule_id','!=',$sched_id)->sum('amount') + $batchData['amount'];

		if($total_amount > $truck_capacity){

			if($batchData['farm_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Farm</div>';
			} elseif($batchData['bin_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Bin Number</div>';
			} elseif($batchData['amount'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Amount</div>';
			} elseif($batchData['feeds_type_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Feed Type</div>';
			} elseif($batchData['ticket'] == NULL) {
				$message = '<div class="alert alert-danger" role="alert">Please add Ticket</div>';
			} else{
				$message = '<div class="alert alert-danger" role="alert">Cannot save batch, the total amount of batch is greater than the capacity of truck</div>';
			}

			$output = array(
				'status'	=>	'false',
				'message'	=>	$message
			);

		} else {
			if($batchData['farm_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Farm</div>';
			} elseif($batchData['bin_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Bin Number</div>';
			} elseif($batchData['amount'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Amount</div>';
			} elseif($batchData['feeds_type_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Feed Type</div>';
			} elseif($batchData['ticket'] == NULL) {
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please add Ticket</div>';
			} else{
				$update = FarmSchedule::where('schedule_id',$sched_id)->update($batchData);
				$status = "true";
				$message = "";
			}
			$output = array(
				'status'	=>	$status,
				'message'	=>	$message
			);

		}

		return $output;
	}


	/*
	*	add new batch
	*/
	public function addBatchLoad(){

		// get the data
		$batch_data = Input::all();

		$truck_capacity = Truck::where('truck_id','=',$batch_data['truck_id'])->first()->capacity;

		$total_amount = FarmSchedule::where('unique_id','=',$batch_data['unique_id'])->sum('amount') + $batch_data['amount'];

		if($total_amount > $truck_capacity){

			if($batch_data['farm_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Farm</div>';
			} elseif($batch_data['bin_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Bin Number</div>';
			} elseif($batch_data['amount'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Amount</div>';
			} elseif($batch_data['feeds_type_id'] == NULL){
				$message = '<div class="alert alert-danger" role="alert">Please select Feed Type</div>';
			} elseif($batch_data['ticket'] == NULL) {
				$message = '<div class="alert alert-danger" role="alert">Please add Ticket</div>';
			} else{
				$message = '<div class="alert alert-danger" role="alert">Cannot add batch, the total amount of batch is greater than the capacity of truck</div>';
			}

			$output = array(
				'status'	=>	'false',
				'message'	=>	$message
			);

		} else {
			if($batch_data['farm_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Farm</div>';
			} elseif($batch_data['feeds_type_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Feed Type</div>';
			} elseif($batch_data['ticket'] == NULL) {
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please add Ticket</div>';
			} elseif($batch_data['bin_id'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Bin Number</div>';
			} elseif($batch_data['amount'] == NULL){
				$status = "false";
				$message = '<div class="alert alert-danger" role="alert">Please select Amount</div>';
			}  else{
				$status = (FarmSchedule::insert($batch_data)) ? "true" : "false";
				$status = "true";
				$message = "";
			}
			$output = array(
				'status'	=>	$status,
				'message'	=>	$message
			);

		}

		return $output;
	}

	/*
	*	added batch view
	*/
	public function addedBatch(){

		$uniqueId = Input::get('unique_id');

		$scheduleList = FarmSchedule::where('unique_id','=',$uniqueId)
							->orderBy('schedule_id','asc')
							->get()->toArray();

		$date_of_sched = $scheduleList[0]['date_of_delivery'];

		$schedData = $this->schedData($scheduleList);

		$feedType = $this->feedTypesLists();

		$amount = $this->capacity();

		$drivers = $this->driversLists();

		$medication = $this->medicationsLists();

		$farmsLists = $this->farmsLists();

		$ctrl = new ScheduleController;

		return view("loading.ajax.batch", compact("schedData","feedType","amount","drivers","medication","farmsLists","ctrl"));
	}

	/*
	*	delete batch
	*/
	public function deleteBatch(){

		$sched_id = Input::get('schedule_id');
		$unique_id = Input::get('unique_id');

		$farmSched = FarmSchedule::find($sched_id);

		$farmSched->delete();

		$this->addedBatch($unique_id);

		$uniqueId = Input::get('unique_id');

		$scheduleList = FarmSchedule::where('unique_id','=',$uniqueId)
							->orderBy('schedule_id','asc')
							->get()->toArray();

		$date_of_sched = $scheduleList[0]['date_of_delivery'];

		$schedData = $this->schedData($scheduleList);

		$feedType = $this->feedTypesLists();

		$amount = $this->capacity();

		$drivers = $this->driversLists();

		$medication = $this->medicationsLists();

		$farmsLists = $this->farmsLists();

		$ctrl = new ScheduleController;

		return view("loading.ajax.batch", compact("schedData","feedType","amount","drivers","medication","farmsLists","ctrl"));

	}

	/*
	*	Summary Renderer
	*
	*	Bin loadout summary view renderer
	*/
	public function summaryRenderer(){

		$uniqueId = Input::get('unique_id');

		$scheduleList = FarmSchedule::where('unique_id','=',$uniqueId)
							->orderBy('schedule_id','asc')
							->get()->toArray();

		$schedData = $this->schedData($scheduleList);

		$totalTons = number_format($this->totalTonsToLoad($schedData));

		return view("loading.ajax.summary",compact("schedData","totalTons"));

	}

	/*
	*	scheduling lists for ajax
	*/
	public function schedIndexLists(){

		$uniqueID = Input::get('unique_id');

		//delete the delivery Sched
		$this->deleteSchedDel($uniqueID);

	}

	/*
	*	Edit the scheduled list
	*/
	public function schedEditLists($unique_id){

		$scheduleList = FarmSchedule::select('delivery_unique_id')
							->where('unique_id','=',$unique_id)
							->orderBy('schedule_id','asc')
							->first();
		$created_delivery = Deliveries::where('unique_id','=',$scheduleList->delivery_unique_id)->get()->toArray();

		$created_delivery = $this->editSchedDataBuilder($created_delivery);

		$truck_compartments = $created_delivery[0]['truck_compartments'];

		$feedType = $this->feedTypesLists();

		$amount = $this->capacity();

		$drivers = $this->driversLists();

		$medication = $this->medicationsLists();

		$farmsLists = $this->farmsLists();

		$ctrl = new ScheduleController;

		return view("loading.edit",compact("feedType",
																			 "ctrl",
																			 "amount",
																			 "drivers",
																			 "medication",
																			 "farmsLists",
																			 "created_delivery",
																			 "selected_driver",
																			 "selected_farm",
																		   "truck_compartments"));

		// get driver(users table), truck, farm lists, bins lists, feeds types lists, medication lists, amount lists, compartment lists
	}

	/*
	*	edit created delivery data builder
	*/
	private function editSchedDataBuilder($delivery){

		$data = array();
		foreach($delivery as $k => $v){
			$data[] = array(
				"delivery_id"						=> 	$v['delivery_id'],
				"delivery_date"					=> 	$v['delivery_date'],
				"truck_id"							=> 	$v['truck_id'],
				"farm_id"								=> 	$v['farm_id'],
				"feeds_type_id"					=> 	$v['feeds_type_id'],
				"medication_id"					=> 	$v['medication_id'],
				"user_id"								=> 	$v['user_id'],
				"driver_id"							=> 	$v['driver_id'],
				"amount"								=> 	$v['amount'],
				"bin_id"								=> 	$v['bin_id'],
				"compartment_number"		=> 	$v['compartment_number'],
				"status"								=> 	$v['status'],
				"unique_id"							=> 	$v['unique_id'],
				"farm_name"							=> 	$this->farmName($v['farm_id']),
				"bin_name"							=> 	$this->binName($v['bin_id']),
				"feed_name"							=> 	$this->feedName($v['feeds_type_id']),
				"driver_name"						=> 	$this->driverName($v['driver_id']),
				"truck_name"						=>	$this->trckName($v['truck_id']),
				"medication_name"				=> 	$this->medName($v['medication_id']),
				'truck_compartments'		=>  $this->trckCompartments($v['truck_id'])
			);
		}

		return $data;

	}

	/*
	*	get the farm name
	*/
	private function farmName($id){

		$selected_farm = Farms::select('name')
										 ->where('id',$id)
										 ->first();

		return $selected_farm->name;

	}

	/*
	*	get the bin name
	*/
	private function binName($id){

		$selected_bin = Bins::select('alias')
										->where('bin_id',$id)
										->first();
		return $selected_bin->alias;

	}

	/*
	*	get the feed name
	*/
	private function feedName($id){

		$output = DB::table('feeds_feed_types')->select('name')
										->where('type_id',$id)
										->first();
		return $output->name;

	}

	/*
	*	get the driver name
	*/
	private function driverName($id){

		$selected_driver = DB::table('feeds_user_accounts')->select('username')
											 ->where('id',$id)
											 ->first();
		return $selected_driver->username;

	}

	/*
	*	get the truck name
	*/
	private function trckName($id){

		$selected_driver = DB::table('feeds_truck')->select('name')
											 ->where('truck_id',$id)
											 ->first();
		return $selected_driver->name;

	}

	/*
	*	get the medication name
	*/
	private function medName($id){

		$selected_bin = DB::table('feeds_medication')->select('med_name')
										->where('med_id',$id)
										->first();
		return $selected_bin->med_name;

	}

	/*
	*	get the truck compartments
	*/
	private function trckCompartments($id){

		$selected_driver = DB::table('feeds_truck_compartment')->select('compartment_number')
											 ->where('truck_id',$id)
											 ->lists('compartment_number','compartment_number');
		return $selected_driver;

	}

	/*
	*	delete sched deliveries
	*/
	public function deleteSchedDel($uniqueID){


		SchedTool::where('farm_sched_unique_id','=',$uniqueID)->delete();

		$farmSched = FarmSchedule::where('unique_id','=',$uniqueID)->get()->toArray();

		for($i=0; $i<count($farmSched);$i++){
			Cache::forget('bins-'.$farmSched[$i]['bin_id']);
			Cache::forget('farm_holder-'.$farmSched[$i]['farm_id']);
			Cache::forget('farm_holder_bins_data-'.$farmSched[$i]['bin_id']);
		}

		$delivery_unique_id = !empty($farmSched[0]['delivery_unique_id']) ? $farmSched[0]['delivery_unique_id'] : NULL;

		$deliveries = Deliveries::where('unique_id',$delivery_unique_id)->get()->toArray();

		if($deliveries != NULL){

			$notification = new CloudMessaging;

			$notification_data_driver = array(
				'unique_id'		=> 	$deliveries[0]['unique_id'],
				'driver_id'		=> 	$deliveries[0]['driver_id']
				);

			$notification->deleteDeliveryNotifier($notification_data_driver);

				for($i=0; $i<count($deliveries); $i++){
					Cache::forget('bins-'.$deliveries[$i]['bin_id']);
					Cache::forget('farm_holder-'.$deliveries[$i]['farm_id']);
					Cache::forget('farm_holder_bins_data-'.$deliveries[$i]['bin_id']);


					$notification_data_farmer = array(
						'farm_id'		=> 	$deliveries[$i]['farm_id'],
						'unique_id'		=> 	$deliveries[$i]['unique_id']
						);

					$this->deleteDriverStats($deliveries[$i]['unique_id']);

					$notification->deleteDeliveryNotifier($notification_data_farmer);
					DB::table('feeds_mobile_notification')->where('unique_id',$deliveries[$i]['unique_id'])->delete();
				}
				/*
				foreach($deliveries as $k => $v){
					Cache::forget('bins-'.$v['bin_id']);

					$notification_data_farmer = array(
						'farm_id'		=> 	$v['farm_id'],
						'unique_id'		=> 	$v['unique_id']
						);

					$this->deleteDriverStats($v['unique_id']);

					$notification->deleteDeliveryNotifier($notification_data_farmer);
					DB::table('feeds_mobile_notification')->where('unique_id',$v['unique_id'])->delete();
				}
				*/
			Deliveries::where('unique_id',$delivery_unique_id)->update(['delivery_label'=>'deleted']);
			DB::table('feeds_mobile_notification')->where('unique_id',$delivery_unique_id)->delete();
			unset($notification);
		}

		FarmSchedule::where('unique_id','=',$uniqueID)->delete();
		DB::table('feeds_mobile_notification')->where('unique_id',$uniqueID)->delete();

		Artisan::call("forecastingdatacache");

	}

	/*
	*	Delete delivered items for the driver stats
	*/
	private function deleteDriverStats($unique_id)
	{
		DB::table('feeds_driver_stats')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_delivery_time')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_drive_time_google_est_mill')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_drive_time')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_drive_time_google_est')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_time_at_farm')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_time_at_mill')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_drive_time_interval')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_drive_time_interval_mill')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_driver_stats_total_miles')->where('deliveries_unique_id',$unique_id)->delete();
		DB::table('feeds_mobile_notification')->where('unique_id',$unique_id)->delete();
	}

	/*
	*	12 color for loadout bin
	*/
	public function loadoutBinColor(){

		$color = array(
			'0'		=>	'#fca600',
			'1'		=>	'#C3C50A',
			'2'		=>	'#85fa00',
			'3'		=>	'#00fd69',
			'4'		=>	'#00ffc3',
			'5'		=>	'#02c5f5',
			'6'		=>	'#007fff',
			'7'		=>	'#5538ff',
			'8'		=>	'#bd3fff',
			'9'		=>	'#fe42d5',
			'10'	=>	'#ff2b7d',
			'11'	=>	'#fb2700'
		);

		return $color;

	}

	/*
	*	loudoutSaveBatch
	*/
	public function loudoutSaveBatch(){

		$data = Input::all();

		foreach($data as $k => $v){

			//update the sched batch tickets
			$update = FarmSchedule::where('schedule_id','=',$v['sched-id'])
						->update(['ticket' => $v['tickets']]);

		}

		if($update == 0){
			$output = "fail";
		}else{
			$output = "success";
		}

		return $output;
	}

	/*
	*/
	public function saveBatchSelection(){

		$tickets = Input::get('tickets');
		$loadoutbins = Input::get('loadoutbins');
		$compartments = Input::get('compartments');

		$batch = array();

		foreach($tickets as $k => $v){
			$batch[] = array(
				'sched_id'	=>	$v['sched-id'],
				'ticket'	=>	$v['tickets'],
				'loadoutbins'	=>	$this->loadOutBinsExtractor($loadoutbins,$v['sched-id']),
				'compartments'	=>	$this->compartmentsExtract($compartments,$v['sched-id'])
			);
		}

		return $batch;
	}

	/*
	*	loadout bins extractor
	*/
	private function loadOutBinsExtractor($load,$id){
		$output = array();

		foreach($load as $k => $v){
			if($id == $v['sched-id']){
				$output[] = $v['loadoutbin'];
			}
		}

		return $output;
	}

	/*
	*	compartments extractor
	*/
	private function compartmentsExtract($compartments,$id){
		$output = array();

		foreach($compartments as $k => $v){
			if($id == $v['sched-id']){
				$output[] = array(
					'sched-id'	=>	$v['sched-id'],
					'amount'	=>	3,
					'number'	=>	$v['compartment']
				);
			}
		}

		$batch_amount = $this->batchAmount($id);
		$last = count($output) - 1;

		if($batch_amount > 3){
			$output[$last]['amount'] = $batch_amount % 3;
			$output[$last]['amount'] = ($output[$last]['amount'] == 0) ? 3 : $output[$last]['amount'];
		}
		//dd(floor($batch_amount));
		return $output;
	}

	/*
	*	batch amounts
	*/
	private function  batchAmount($id){

		$data = FarmSchedule::select('amount')->where('schedule_id','=',$id)->get()->toArray();

		$output = !empty($data[0]['amount']) ? $data[0]['amount'] : 0;

		return $output;

	}

	/*
	*	batch amounts
	*/
	private function  batchAmountTwo($id){

		$data = FarmSchedule::select('amount')->where('schedule_id','=',$id)->get()->toArray();

		$output = !empty($data[0]['amount']) ? $data[0]['amount'] : 0;

		return $output;

	}

	/*
	*	batch amounts
	*/
	private function scheduleInfo($id){

		$data = FarmSchedule::where('schedule_id','=',$id)->get()->toArray();

		$output = !empty($data[0]) ? $data[0] : 0;

		return $output;

	}

	/*
	*	compartments extractor
	*/
	private function compartmentsExtractUpdate($compartments,$batch){
		$output = array();
		foreach($compartments as $k => $v){
			if($batch['sched-id'] == $v['sched-id']){
				$output[] = array(
					'amount'							=>	$batch['amount'] < 3.0 ? $batch['amount'] : 3.0,
					'compartment_number'	=>	$v['compartment'],
					'delivery_date'				=>	$batch['delivery_date'],
					'truck_id'						=>	$batch['truck_id'],
					'farm_id'							=>	$batch['farm_id'],
					'feeds_type_id'				=>	$batch['feeds_type_id'],
					'medication_id'				=>	$batch['medication_id'],
					'user_id'							=>	$batch['user_id'],
					'driver_id'						=>	$batch['driver_id'],
					'batch_code'					=>	$batch['batch_code'],
					'bin_id'							=>	$batch['bin_id'],
					'load_out_bin'				=>	$batch['loadoutbin'],
					'status'							=>	$batch['status'],
					'unique_id'						=>	$batch['unique_id'],
					'delivered'						=>	$batch['delivered'],
					'created_at'					=>	$batch['created_at'],
					'updated_at'					=>	$batch['updated_at']
				);
			}
		}


		$batch_amount = $this->batchAmountTwo($batch['sched-id']);
		// last index of the array
		$last = count($output) - 1;

		for($i = 0; $i < $last; $i++){

			$batch_amount = $batch_amount - 3.0;

		}

		$output[$last]['amount'] = $batch_amount;

		// calculate the last amount
		/*if($batch_amount > 3.0){
			$output[$last]['amount'] = $batch_amount - 3.0;
			$output[$last]['amount'] = $output[$last]['amount'] == 0 ? 3.0 : $output[$last]['amount'];

		}*/

		return $output;

	}

	/*
	*	Load to truck used by the APIController
	*/
	public function loadToTruckAPI($data,$user){

		$data_to_delivery = array();
		$farm_schedule_data = array();
		$unique_id_for_delivery = $this->generator();

		// fetch the data from the batch table
		$batch = DB::table('feeds_batch')
								//->where('driver_id',NULL)
								->where('status','created')
								->where('unique_id',$data['unique_id'])
								->get();

		SchedTool::where('farm_sched_unique_id',$data['unique_id'])->update(['status'=>'created','delivery_unique_id'=>$unique_id_for_delivery,'driver_id'=>$data['driver_id']]);
		$farm_sched_data = FarmSchedule::select('date_of_delivery')->where('unique_id',$data['unique_id'])->first()->toArray();

		// build the data format to insert to deliveries table
		foreach($batch as $k => $v){

			//$this->updateSchedTool($data['unique_id'],$data['driver_id']);

			$medication = $v->medication == 8 ? 0 : $v->medication;

			$data_to_delivery[] = array(
				'delivery_date'				=>	date("Y-m-d H:i:s",strtotime($farm_sched_data['date_of_delivery'])),
				'truck_id'						=>	$v->truck,
				'farm_id'							=>	$v->farm_id,
				'feeds_type_id'				=>	$v->feed_type,
				'medication_id'				=>	$medication,
				'user_id'							=>	$user,//Auth::id(),
				'driver_id'						=>	$data['driver_id'],
				'amount'							=>	$v->amount,
				'bin_id'							=>	$v->bin_id,
				'compartment_number'	=>	$v->compartment,
				'status'							=>	0,
				'unique_id'						=>	$unique_id_for_delivery,
				'created_at'					=>	date('Y-m-d h:i:s'),
				'updated_at'					=>	date('Y-m-d h:i:s'),
				'delivered'						=>	0,
			);

			$farm_schedule_data[] = array(
				'date_of_delivery'		=>	date("Y-m-d H:i:s",strtotime($farm_sched_data['date_of_delivery'])),
				'truck_id'						=>	$v->truck,
				'farm_id'							=>	$v->farm_id,
				'bin_id'							=>	$v->bin_id,
				'driver_id'						=>	$data['driver_id'],
				'medication_id'				=>	$medication,
				'amount'							=>	$v->amount,
				'feeds_type_id'				=>	$v->feed_type,
				'unique_id'						=>	$data['unique_id'],
				'ticket'							=>	"-",
				'delivery_unique_id'	=>	$unique_id_for_delivery,
				'status'							=>	1,
				'user_id'							=>	$user//Auth::id()
			);

			Cache::forget('bins-'.$v->bin_id);
		}

		// update the feeds_batch (put the driver_id)
		//DB::table('feeds_batch')->where('driver_id',NULL)->where('unique_id',$data['unique_id'])->update(['driver_id'=>$data['driver_id'],'status'=>'loaded']);


		//delete the previous feeds farm data
		FarmSchedule::where('unique_id',$data['unique_id'])->delete();
		// save the data to feeds_farm_schedule
		FarmSchedule::insert($farm_schedule_data);
		// save the data to feeds_sched_tool(with delivery number)

		// sve the data to feeds_deliveries
		Deliveries::insert($data_to_delivery);

		// notify the driver and farmer
		$this->loadTruckDriverNotification($data_to_delivery,$unique_id_for_delivery);

		$deliveries = Deliveries::where('unique_id','=',$unique_id_for_delivery)->get()->toArray();

		$this->loadTruckFarmerNotification($deliveries);

		return $data_to_delivery;

	}

	/*
	*	Load to truck used by the APIController
	*/
	public function loadToTruckUpdateAPI($delivery_unique_id,$farm_sched_unique_id,$user_id){

		$data_to_delivery = array();
		$farm_schedule_data = array();


		// fetch the data from the batch table
		$batch = DB::table('feeds_batch')
								->where('status','created')
								->where('unique_id',$farm_sched_unique_id)
								->get();

		SchedTool::where('farm_sched_unique_id',$farm_sched_unique_id)->update(['status'=>'created','delivery_unique_id'=>$delivery_unique_id,'driver_id'=>$batch[0]->driver_id]);
		$farm_sched_data = FarmSchedule::select('date_of_delivery')->where('delivery_unique_id',$delivery_unique_id)->first()->toArray();

		// build the data format to insert to deliveries table
		foreach($batch as $k => $v){

			//$this->updateSchedTool($data['unique_id'],$data['driver_id']);

			$medication = $v->medication == 8 ? 0 : $v->medication;

			$data_to_delivery[] = array(
				'delivery_date'				=>	date("Y-m-d H:i:s",strtotime($farm_sched_data['date_of_delivery'])),
				'truck_id'						=>	$v->truck,
				'farm_id'							=>	$v->farm_id,
				'feeds_type_id'				=>	$v->feed_type,
				'medication_id'				=>	$medication,
				'user_id'							=>	$user_id,
				'driver_id'						=>	$v->driver_id,
				'amount'							=>	$v->amount,
				'bin_id'							=>	$v->bin_id,
				'compartment_number'	=>	$v->compartment,
				'status'							=>	0,
				'unique_id'						=>	$delivery_unique_id,
				'created_at'					=>	date('Y-m-d h:i:s'),
				'updated_at'					=>	date('Y-m-d h:i:s'),
				'delivered'						=>	0,
			);

			$farm_schedule_data[] = array(
				'date_of_delivery'		=>	date("Y-m-d H:i:s",strtotime($farm_sched_data['date_of_delivery'])),
				'truck_id'						=>	$v->truck,
				'farm_id'							=>	$v->farm_id,
				'bin_id'							=>	$v->bin_id,
				'driver_id'						=>	$v->driver_id,
				'medication_id'				=>	$medication,
				'amount'							=>	$v->amount,
				'feeds_type_id'				=>	$v->feed_type,
				'unique_id'						=>	$farm_sched_unique_id,
				'ticket'							=>	"-",
				'delivery_unique_id'	=>	$delivery_unique_id,
				'status'							=>	1,
				'user_id'							=>	$user_id
			);

			Cache::forget('bins-'.$v->bin_id);
		}

		// update the feeds_batch (put the driver_id)
		//DB::table('feeds_batch')->where('driver_id',NULL)->where('unique_id',$data['unique_id'])->update(['driver_id'=>$data['driver_id'],'status'=>'loaded']);


		//delete the previous feeds farm data
		FarmSchedule::where('unique_id',$farm_sched_unique_id)->delete();
		//delete the inserted delivery
		Deliveries::where('unique_id',$delivery_unique_id)->delete();
		// save the data to feeds_farm_schedule
		FarmSchedule::insert($farm_schedule_data);
		// sve the data to feeds_deliveries
		Deliveries::insert($data_to_delivery);

		// notify the driver and farmer
		$this->loadTruckDriverNotification($data_to_delivery,$delivery_unique_id);

		$deliveries = Deliveries::where('unique_id','=',$delivery_unique_id)->get()->toArray();

		$this->loadTruckFarmerNotification($deliveries);

		return $data_to_delivery;

	}

	/*
	*	Batch builder
	*/
	public function loadToTruck(){

		$partial_batch = Input::get('tickets');
		$compartments = Input::get('compartments');
		$farms = Input::get('farms');

		$feed_type = Input::get('feed_type');
		$medication = Input::get('medication');
		$amount = Input::get('amounts');
		$bin = Input::get('selected_bin');
		$driver = Input::get('driver');
		$loadout_bins = NULL;//$this->loadoutGrouping(Input::get('loadoutbins'));
		$unique_id = $this->generator();


		$data = $this->compartmentDataBuilder($compartments,$farms,$bin,$feed_type,$medication,$amount,$unique_id,$driver);

		return $data;

	}

	/*
	*	Build the data for compartments
	*/
	private function compartmentDataBuilder($compartments,$farms,$bin,$feed_type,$medication,$amount,$unique_id,$driver)
	{
		$data = array();

		$unique_compartments = $this->getUniqueCompartments($compartments,$amount,$farms,$bin,$feed_type,$medication);

		if($unique_compartments != NULL){
			return $unique_compartments;
		}


		$sched_ids = array();
		foreach($compartments as $k => $v){

			//update the farm schedule status
			//$this->farmSchedUpdate($v['sched-id'],$driver,$amount[$k]);
			$sched_ids[] = $v['sched-id'];
			// remove the data from the sched tool
			$this->removeSchedToolData($v['sched-id'],$unique_id);

			// get the schedule information
			$sched_info = $this->scheduleInfo($v['sched-id']);

			$this->updateSchedTool($sched_info['unique_id'],$driver);

			$data[] = array(
				'delivery_date'				=>	$sched_info['date_of_delivery'],
				'truck_id'						=>	$sched_info['truck_id'],
				'farm_id'							=>	$farms[$k],
				'feeds_type_id'				=>	$feed_type[$k],
				'medication_id'				=>	$medication[$k],
				'user_id'							=>	Auth::id(),
				'driver_id'						=>	$driver,
				'amount'							=>	$amount[$k],
				'bin_id'							=>	$bin[$k],
				'compartment_number'	=>	$v['compartment'],
				'status'							=>	0,
				'unique_id'						=>	$unique_id,
				'created_at'					=>	date('Y-m-d h:i:s'),
				'updated_at'					=>	date('Y-m-d h:i:s'),
				'delivered'						=>	0,
			);

			$farm_schedule_data[] = array(
				'date_of_delivery'		=>	$sched_info['date_of_delivery'],
				'truck_id'						=>	$sched_info['truck_id'],
				'farm_id'							=>	$farms[$k],
				'bin_id'							=>	$bin[$k],
				'driver_id'						=>	$driver,
				'medication_id'				=>	$medication[$k],
				'amount'							=>	$amount[$k],
				'feeds_type_id'				=>	$feed_type[$k],
				'unique_id'						=>	$sched_info['unique_id'],
				'ticket'							=>	"-",
				'delivery_unique_id'	=>	$unique_id,
				'status'							=>	1,
				'user_id'							=>	Auth::id()
			);



			Cache::forget('bins-'.$bin[$k]);

		}

		FarmSchedule::whereIn('schedule_id',$sched_ids)->delete();
		FarmSchedule::insert($farm_schedule_data);

		$output = 0;
		if(Deliveries::insert($data)){
			$output = 1;
		}

		$this->loadTruckDriverNotification($data,$unique_id);

		$deliveries = Deliveries::where('unique_id','=',$unique_id)->get()->toArray();

		$this->loadTruckFarmerNotification($deliveries);

		return $output;

	}

	/*
	*	get the unique data of the compartments
	* catch the validation for more than 3 tons per compartment
	*/
	private function getUniqueCompartments($compartments,$amount,$farms,$bin,$feed_type,$medication)
	{

		$data = array();
		foreach($compartments as $k => $v){
			$data[$k] = $v['compartment'];
		}
		$original_compartments = $data;
		$data = array_unique($data);

		$total_amount_per_compartment = array();
		$total_amount = 0;

		foreach($data as $k => $v){

			foreach($compartments as $key => $val){

				if($val['compartment'] == $v){

					$total_amount = $total_amount + $amount[$key];

					$total_amount_per_compartment[] = array(
						'compartment'		=>	$v,
						'farm'					=>	$farms[$key],
						'bin'						=>	$bin[$key],
						'feed_type'			=>	$feed_type[$key],
						'medication'		=>	$medication[$key],
						'total_amount'	=>	$total_amount
					);

				} else {
					$total_amount = 0;
				}

			}

		}


		$total_amount_per_compartments = array();
		$total_amounts = 0;

		foreach($data as $k => $v){

			foreach($total_amount_per_compartment as $key => $val){

				// if same compartment make the data
				if($val['compartment'] == $v){

					$total_amounts = $total_amounts + $amount[$key];

					$total_amount_per_compartments[] = array(
						'compartment'		=>	$v,
						'farm'					=>	$val['farm'],
						'bin'						=>	$val['bin'],
						'feed_type'			=>	$val['feed_type'],
						'medication'		=>	$val['medication'],
						'total_amount'	=>	$total_amounts
					);

					$total_amounts = 0;

				} else {

					$total_amounts = 0;

				}
			}

		}


		// for odd interval compartment validation $total_amount_per_compartment {111}
		// for same compartment validation $total_amount_per_compartments {121}

		$validator = array();
		foreach(array_count_values($original_compartments) as $k => $v){

			// unique compartments --> $k
			// get the key value of a bigger counter
			// concat the data
			if($v == 2){
				foreach($total_amount_per_compartments as $key => $val){
					if($val['compartment'] == $k){
						$validator[$k][] = array(
							'compartment_data'		=>	$val['compartment']."_".$val['farm']."_".$val['feed_type']."_".$val['medication']
						);
					}
				}

				$default_amount = 0;
				foreach($total_amount_per_compartment as $key => $val){
					if($val['compartment'] === $k){
						$default_amount = $default_amount + $total_amount_per_compartment[$key]['total_amount'];
						$total_amount_per_compartment[$key]['total_amount'] = $default_amount;
					} else {
						$default_amount = 0;
					}
				}

			} else {

				foreach($total_amount_per_compartments as $key => $val){
					if($val['compartment'] == $k){
						$validator[$k][] = array(
							'compartment_data'		=>	$val['compartment']."_".$val['farm']."_".$val['feed_type']."_".$val['medication']
						);
					}
				}

				$default_amount = 0;
				foreach($total_amount_per_compartment as $key => $val){
					if($val['compartment'] === $k){
						$default_amount = $default_amount + $total_amount_per_compartment[$key]['total_amount'];
						$total_amount_per_compartment[$key]['total_amount'] = $default_amount;
					} else {
						$default_amount = 0;
					}
				}

			}

		}

		// count the compartment
		// count the duplicates
		foreach($validator as $k => $v){
			$base = $validator[$k][0]['compartment_data'];
			foreach($v as $key => $val){
				if($base != $val['compartment_data']){
					return "compartment_data_error";
				}
			}
		}

		foreach($total_amount_per_compartment as $k => $v){
			//echo $v['total_amount'] . "<br/>";
			if($v['total_amount'] > 3){
				return "compartment_error";
			}
		}

		return NULL;
	}

	/*
	*	Loadto truck the edited compartments
	*/
	public function loadToTruckEdited(){

		$delivery_id = Input::get('delivery_id');
		$unique_id = Input::get('unique_id');
		$farms = Input::get('farms');
		$bin = Input::get('selected_bin');
		$feed_type = Input::get('feed_type');
		$medication = Input::get('medication');
		$amount = Input::get('amounts');
		$driver = Input::get('driver_id');
		$compartments = Input::get('compartments');
		$truck_id = Input::get('truck_id');
		$compartment_validation =  $this->getUniqueCompartmentsEdit($compartments,$amount,$farms,$bin,$feed_type,$medication);

		if($compartment_validation != NULL){
			return $compartment_validation;
		}

		$farm_sched_data = FarmSchedule::select('unique_id','date_of_delivery')->where('delivery_unique_id',$unique_id[0])->first();
		$farm_sched_unique_id = $farm_sched_data->unique_id;
		$deliveries_data = Deliveries::where('unique_id','=',$unique_id[0])->first();
		$farm_sched_date_of_delivery = $deliveries_data->delivery_date;

		$data_previous_driver = array(array(
			'driver_id'					=>	$deliveries_data->driver_id,
			'truck_id'					=>	$deliveries_data->truck_id,
			'delivery_date'			=>	$deliveries_data->delivery_date,
		));

		$this->loadTruckDriverNotification($data_previous_driver,$deliveries_data->unique_id);
		DB::table('feeds_mobile_notification')->where('unique_id',$deliveries_data->unique_id)->delete();

		$farm_schedule_data = array();
		foreach($compartments as $k => $v){

			$delivery_data = array(
				'truck_id'						=>	$truck_id[$k],
				'farm_id'							=>	$farms[$k],
				'feeds_type_id'				=>	$feed_type[$k],
				'user_id'							=>	Auth::id(),
				'medication_id'				=>	$medication[$k],
				'driver_id'						=>	$driver,
				'amount'							=>	$amount[$k],
				'bin_id'							=>	$bin[$k],
				'compartment_number'	=>	$v,
				'status'							=>	0,
				'unique_id'						=>	$unique_id[$k],
				'created_at'					=>	date('Y-m-d h:i:s'),
				'updated_at'					=>	date('Y-m-d h:i:s'),
				'delivered'						=>	0,
			);

			Deliveries::where('delivery_id',$delivery_id[$k])->update($delivery_data);

			$farm_schedule_data[] = array(
				'date_of_delivery'		=>	$farm_sched_date_of_delivery,
				'truck_id'						=>	$truck_id[$k],
				'farm_id'							=>	$farms[$k],
				'bin_id'							=>	$bin[$k],
				'driver_id'						=>	$driver,
				'medication_id'				=>	$medication[$k],
				'amount'							=>	$amount[$k],
				'feeds_type_id'				=>	$feed_type[$k],
				'unique_id'						=>	$farm_sched_unique_id,
				'ticket'							=>	"-",
				'delivery_unique_id'	=>	$unique_id[$k],
				'status'							=>	1,
				'user_id'							=>	Auth::id()
			);

			Cache::forget('bins-'.$bin[$k]);

		}

		FarmSchedule::where('unique_id',$farm_sched_unique_id)->delete();
		FarmSchedule::insert($farm_schedule_data);

		$unique_id = $unique_id[0];

		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("delivery_unique_id","=",$unique_id)
							->get()->toArray();

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$farm_names = Farms::select(DB::raw("GROUP_CONCAT(name) AS name"))->whereIn('id',$farm)->get()->toArray();


		// update the data for the sched tool
		// update the date of delivery
		// change the farm names
		// based on the delivery_unique_id
		SchedTool::where('delivery_unique_id',$unique_id)->update(array('farm_title'=>$farm_names[0]['name'],'driver_id'=>$driver));

		$deliveries = Deliveries::where('unique_id','=',$unique_id)->get()->toArray();

		$data = array(array(
			'driver_id'					=>	$deliveries[0]['driver_id'],
			'truck_id'					=>	$deliveries[0]['truck_id'],
			'delivery_date'			=>	$deliveries[0]['delivery_date'],
		));

		$this->loadTruckDriverNotification($data,$unique_id);

		$this->loadTruckFarmerNotification($deliveries);

		return 1;

	}

	/*
	*	get the unique data of the compartments
	* catch the validation for more than 3 tons per compartment
	*/
	private function getUniqueCompartmentsEdit($compartments,$amount,$farms,$bin,$feed_type,$medication)
	{
		$original_compartments = $compartments;
		$data = array_unique($compartments);

		$total_amount_per_compartment = array();
		$total_amount = 0;

		foreach($data as $k => $v){

			foreach($compartments as $key => $val){

				if($val == $v){

					$total_amount = $total_amount + $amount[$key];

					$total_amount_per_compartment[] = array(
						'compartment'		=>	$v,
						'farm'					=>	$farms[$key],
						'bin'						=>	$bin[$key],
						'feed_type'			=>	$feed_type[$key],
						'medication'		=>	$medication[$key],
						'total_amount'	=>	$total_amount
					);

				} else{
					$total_amount = 0;
				}
			}

		}

		$total_amount_per_compartments = array();
		$total_amounts = 0;
		foreach($data as $k => $v){

			foreach($total_amount_per_compartment as $key => $val){

				if($val['compartment'] == $v){

					$total_amounts = $total_amounts + $amount[$key];

					$total_amount_per_compartments[] = array(
						'compartment'		=>	$v,
						'farm'					=>	$val['farm'],
						'bin'						=>	$val['bin'],
						'feed_type'			=>	$val['feed_type'],
						'medication'		=>	$val['medication'],
						'total_amount'	=>	$total_amounts
					);

					$total_amounts = 0;

				} else {

					$total_amounts = 0;

				}

			}

		}

		// for odd interval compartment validation $total_amount_per_compartment {111}
		// for same compartment validation $total_amount_per_compartments {121}

		$validator = array();

		foreach(array_count_values($original_compartments) as $k => $v){
			// unique compartments --> $k
			// get the key value of a bigger counter
			// concat the data
			if($v == 2){
				foreach($total_amount_per_compartments as $key => $val){
					if($val['compartment'] == $k){
						$validator[$k][] = array(
							'compartment_data'		=>	$val['compartment']."_".$val['farm']."_".$val['feed_type']."_".$val['medication']
						);
					}
				}

				$default_amount = 0;
				foreach($total_amount_per_compartment as $key => $val){
					if($val['compartment'] === $k){
						$default_amount = $default_amount + $total_amount_per_compartment[$key]['total_amount'];
						$total_amount_per_compartment[$key]['total_amount'] = $default_amount;
					} else {
						$default_amount = 0;
					}
				}

			} else {

				foreach($total_amount_per_compartments as $key => $val){
					if($val['compartment'] == $k){
						$validator[$k][] = array(
							'compartment_data'		=>	$val['compartment']."_".$val['farm']."_".$val['feed_type']."_".$val['medication']
						);
					}
				}

				$default_amount = 0;
				foreach($total_amount_per_compartment as $key => $val){
					if($val['compartment'] === $k){
						$default_amount = $default_amount + $total_amount_per_compartment[$key]['total_amount'];
						$total_amount_per_compartment[$key]['total_amount'] = $default_amount;
					} else {
						$default_amount = 0;
					}
				}

			}


		}

		// count the compartment
		// count the duplicates
		foreach($validator as $k => $v){
			$base = $validator[$k][0]['compartment_data'];
			foreach($v as $key => $val){
				if($base != $val['compartment_data']){
					return "compartment_data_error";
				}
			}
		}

		foreach($total_amount_per_compartment as $k => $v){
			if($v['total_amount'] > 3){
				return "compartment_error";
			}
		}

		return NULL;
	}

	/*
	*	Specific Loadout Bin Grouping
	*/
	private function updateSchedTool($farm_sched_unique_id,$driver_id)
	{
		SchedTool::where('farm_sched_unique_id',$farm_sched_unique_id)->update(['driver_id'=>$driver_id]);
	}

	/*
	*	Specific Loadout Bin Grouping
	*/
	private function loBinGrouping($loadout_bins)
	{
		$output = "";
		foreach($loadout_bins as $k => $v){
			$output .= $v .",";
		}
		return $output;
	}

	/*
	*	Loadout Bin Grouping
	*/
	private function loadoutGrouping($loadout_bins)
	{

		$tmp = array();

		foreach($loadout_bins as $arg)
		{
			$tmp[$arg['sched-id']][] = $arg['loadoutbin'];
		}

		$output = array();

		foreach($tmp as $type => $labels)
		{
			$output[] = array(
				'sched-id' => $type,
				'loadoutbin' => $labels
			);
		}

		return $output;
	}

	/*
	*	remove sched tool data
	*/
	private function removeSchedToolData($sched_id,$delivery_unique_id){
		$sched_data = FarmSchedule::where('schedule_id','=',$sched_id)->get()->toArray();
		$unique_id = $sched_data[0]['unique_id'];
		// update the unique id of the sched tool and farm schedule tables,
		// the unique id will be the same as on the deliveries page item
		// so when the delivery item will be delivered, the sched tool will be updated at the same time
		SchedTool::where('farm_sched_unique_id',$unique_id)->update(['status'=>'created','delivery_unique_id'=>$delivery_unique_id]);
		FarmSchedule::where('schedule_id','=',$sched_id)->update(['delivery_unique_id'=>$delivery_unique_id]);
	}

	/*
	* load truck driver notification
	*/
	private function loadTruckDriverNotification($batch,$unique_id){
		$notification = new CloudMessaging;

		$driver_data = array(
			'driver_id'			=>	$batch[0]['driver_id'],
			'truck_id'			=>	$batch[0]['truck_id'],
			'date_of_delivery'	=>	$batch[0]['delivery_date'],
			'unique_id'			=>	$unique_id
		);

		$notification->driverMessaging($driver_data);

		$mobileNotiData = array(
			'farm_id'			=>	0,
			'driver_id'			=>	$batch[0]['driver_id'],
			'user_id'			=>	$batch[0]['driver_id'],
			'date_of_delivery'	=> 	$batch[0]['delivery_date'],
			'is_readred'		=>	'false',
			'unique_id'			=>	$unique_id,
			'created_by'	=>	1//Auth::id()
		);

		$this->mobileNotiStore($mobileNotiData);

		unset($notification);

	}

	/*
	* load truck farmer notification
	*/
	private function loadTruckFarmerNotification($batch){
		$notification = new CloudMessaging;

		$farmsIDs = array_count_values(array_column($batch, 'farm_id'));

		foreach($farmsIDs as $key => $val){
			if($key != 0){
				$farmer_data = array('farm_id'=>$key,'date_of_delivery'=>$batch[0]['delivery_date'],'unique_id'=>$batch[0]['unique_id']);
				$notification->farmerLoadedTruckMessaging($farmer_data);

				if($this->farmer($key) != NULL){
					foreach($this->farmer($key) as $k => $v){

						// send mobile notification for none gcm users
						$mobileNotiData = array(
							'farm_id'			=>	$key,
							'driver_id'			=>	1,
							'user_id'			=>	$v->user_id,
							'date_of_delivery'	=> 	$batch[0]['delivery_date'],
							'is_readred'		=>	'false',
							'unique_id'			=>	$batch[0]['unique_id']
						);
						$this->mobileNotiStore($mobileNotiData);

					}
				}

			}
		}

		unset($notification);
	}


	/*
	*	update the farm schedule
	*/
	private function farmSchedUpdate($id,$driver_id,$amount){
		$update = FarmSchedule::where('schedule_id','=',$id)->update(['status'=>1,'driver_id'=>$driver_id,'amount'=>$amount]);
		return $update;
	}

	/*
	*	loadout bins counter
	*/
	public function loadoutBinsLoadCounter(){

		$sched_id = Input::get('sched_id');
		$value = Input::get('value');
		$element_id = Input::get('element_id');
		$unique_id = Input::get('unique_id');
		$selected_index = Input::get('selected_index');

		if(CreateLoadLoadoutBins::where('sched_id','=',$sched_id)->where('value','=',$value)->where('element_id','=',$element_id)->where('unique_id','=',$unique_id)->exists()){
			//$output = "No Changes";
			$output = CreateLoadLoadoutBins::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);
		} elseif(CreateLoadLoadoutBins::where('sched_id','=',$sched_id)->where('element_id','=',$element_id)->where('unique_id','=',$unique_id)->exists()){

			CreateLoadLoadoutBins::where('sched_id','=',$sched_id)->where('element_id','=',$element_id)->where('unique_id','=',$unique_id)->delete();

			$selected = CreateLoadLoadoutBins::select('selected_index')
							->where('sched_id','=',$sched_id)
							->where('element_id','=',$element_id)
							->where('unique_id','=',$unique_id)
							->get()->toArray();

			CreateLoadLoadoutBins::insert(Input::all());
			$output = CreateLoadLoadoutBins::where('sched_id','=',$sched_id)->get()->toArray();

			/*if(CreateLoadLoadoutBins::where('value','=',$value)->where('unique_id','=',$unique_id)->exists()){
				$output = array(
					'message' 	=> 	"Loadout Already Selected Update",
					'selected'	=>	!empty($selected[0]['selected_index']) ? $selected[0]['selected_index'] : ""
					);
				$output = CreateLoadLoadoutBins::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);
			} else {

				if(CreateLoadLoadoutBins::where('value','=',$value)->where('unique_id','=',$unique_id)->exists()){
					//$output = array('message'=>"Loadout Bin Already Selected");
					$output = CreateLoadLoadoutBins::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);
				}

				//update the existing record
				//$output = CreateLoadLoadoutBins::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);
			}*/
		/*} elseif(CreateLoadLoadoutBins::where('value','=',$value)->where('unique_id','=',$unique_id)->exists()){
			//$output = array('message'=>"Loadout Bin Already Selected");
			$output = CreateLoadLoadoutBins::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);*/
		} else {
			CreateLoadLoadoutBins::insert(Input::all());
			$output = CreateLoadLoadoutBins::where('sched_id','=',$sched_id)->get()->toArray();
		}


		return  $output;

	}

	/*
	*	loadout bins compartments
	*/
	public function loadoutBinsCompartments(){

		$sched_id = Input::get('sched_id');
		$value = Input::get('value');
		$element_id = Input::get('element_id');
		$unique_id = Input::get('unique_id');
		$selected_index = Input::get('selected_index');

		if(CreateLoadCompartments::where('sched_id','=',$sched_id)->where('value','=',$value)->where('element_id','=',$element_id)->where('unique_id','=',$unique_id)->exists()){
			$output = "No Changes";
		} elseif(CreateLoadCompartments::where('sched_id','=',$sched_id)->where('element_id','=',$element_id)->where('unique_id','=',$unique_id)->exists()){

			$selected = CreateLoadCompartments::select('selected_index')
							->where('sched_id','=',$sched_id)


							->where('element_id','=',$element_id)
							->where('unique_id','=',$unique_id)
							->get()->toArray();

			if(CreateLoadCompartments::where('value','=',$value)->where('unique_id','=',$unique_id)->exists()){
				$output = array(
					'message' 	=> 	"Compartment Already Selected Update",
					'selected'	=>	!empty($selected[0]['selected_index']) ? $selected[0]['selected_index'] : ""
					);
			} else {
				//update the existing record
				$output = CreateLoadCompartments::where('element_id',$element_id)->update(['value' => $value,'selected_index'=>$selected_index]);
			}
		} elseif(CreateLoadCompartments::where('value','=',$value)->where('unique_id','=',$unique_id)->exists()){
			$output = array('message'=>"Compartment Already Selected");
		} else {
			CreateLoadCompartments::insert(Input::all());
			$output = CreateLoadCompartments::where('sched_id','=',$sched_id)->get()->toArray();
		}

		return  $output;

	}

	/*
	*	loadout bins compartments on create default selection load
	*/
	public function loadoutBinsCompartmentsDefault(){
		var_dump(Input::all());
	}

	/*
	*	Selected Loadout Bins
	*/
	public function loadoutBinsLoaded(){

		$unique_id = Input::get('unique_id');
		$data = CreateLoadLoadoutBins::where('unique_id','=',$unique_id)->get()->toArray();
		if($data){
			return $data;
		}

	}

	/*
	*	Selected Compartments
	*/
	public function compartmentsLoaded(){

		$unique_id = Input::get('unique_id');
		$data = CreateLoadCompartments::where('unique_id','=',$unique_id)->get()->toArray();
		if($data){
			return $data;
		}

	}

	/*
	*	Update the ticket
	*/
	public function updateTicket(){

		$ticket = Input::get('ticket');
		$sched_id = Input::get('sched_id');

		FarmSchedule::where('schedule_id','=',$sched_id)->update(['ticket'=>$ticket]);

	}

	/*
	*	Deleted Selected
	*/
	public function deleteSelected(){
		$unique_id = Input::get('unique_id');
		CreateLoadLoadoutBins::where('unique_id','=',$unique_id)->delete();
		CreateLoadCompartments::where('unique_id','=',$unique_id)->delete();
	}

	/*
	*	Initialize the scheduled data
	*/
	public function scheduledDataAPI($selected_date){

		$farm_sched_list = DB::table('feeds_farm_schedule')
							->select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%Y-%m-%d %h:%i:%s %p") as date_of_delivery'),
									'schedule_id','feeds_type_id','medication_id','unique_id','status','delivery_unique_id',
									DB::raw('GROUP_CONCAT(farm_id) AS farm_id'),
									DB::raw('GROUP_CONCAT(amount) AS amount'),
									DB::raw('GROUP_CONCAT(bin_id) AS bin_id'),
									'feeds_truck.name as truck_name',
									'feeds_truck.truck_id as truck_id',
									'feeds_farm_schedule.driver_id as driver_id')
							->leftJoin('feeds_truck','feeds_truck.truck_id','=','feeds_farm_schedule.truck_id')
							//->where('status','=',0)
							->where('date_of_delivery','LIKE',$selected_date.'%')
							->orderBy('date_of_delivery','desc')
							->groupBy('feeds_farm_schedule.unique_id')
							->get();

		$data = array();
		for($i = 0; $i < count($farm_sched_list); $i++){
			$data[] = (object)array(
				'schedule_id'		=>	$farm_sched_list[$i]->schedule_id,
				'delivery_date'		=>	$this->dateFormat($farm_sched_list[$i]->date_of_delivery),
				'delivery_time'		=>	$this->farmDeliveryTimes($farm_sched_list[$i]->farm_id),
				'farm_name'			=>	$this->farmNames($farm_sched_list[$i]->farm_id,$farm_sched_list[$i]->date_of_delivery,$farm_sched_list[$i]->unique_id),
				'truck_name'		=>	$farm_sched_list[$i]->truck_name,
				'status'			=>	$farm_sched_list[$i]->status,
				'truck_id'			=>	$farm_sched_list[$i]->truck_id,
				'driver'			=>	$this->getDriver($farm_sched_list[$i]->driver_id),
				'selected_driver'	=>	$this->schedSelecteDriver($farm_sched_list[$i]->unique_id,$farm_sched_list[$i]->driver_id),
				'selected_delivery'	=>	$this->schedSelectedDelivery($farm_sched_list[$i]->unique_id),
				'unique_id'			=>	$farm_sched_list[$i]->unique_id,
				'sched_tool_status'				=>	$this->schedToolStatus($farm_sched_list[$i]->delivery_unique_id)
			);
		}

		$drivers = User::where('type_id','=',2)->orderBy('username','asc')->lists("username","id")->toArray();


		$delivery_count = array();
		for($i = 0; $i <= 7; $i++){
			$delivery_count[] = $i;
		}


		return $data;

	}

	/*
	*	Initialize the scheduled data
	*/
	public function scheduledData(){

		$selected_data = Input::get('selected_data');
		$selected_data = date("Y-m-d",strtotime($selected_data));

		$farm_sched_list = DB::table('feeds_farm_schedule')
							->select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%Y-%m-%d %h:%i:%s %p") as date_of_delivery'),
									'schedule_id','feeds_type_id','medication_id','unique_id','status','delivery_unique_id',
									DB::raw('GROUP_CONCAT(farm_id) AS farm_id'),
									DB::raw('GROUP_CONCAT(amount) AS amount'),
									DB::raw('GROUP_CONCAT(bin_id) AS bin_id'),
									'feeds_truck.name as truck_name',
									'feeds_truck.truck_id as truck_id',
									'feeds_farm_schedule.driver_id as driver_id')
							->leftJoin('feeds_truck','feeds_truck.truck_id','=','feeds_farm_schedule.truck_id')
							//->where('status','=',0)
							->where('date_of_delivery','LIKE',$selected_data.'%')
							->orderBy('date_of_delivery','desc')
							->groupBy('feeds_farm_schedule.unique_id')
							->get();

		$data = array();
		for($i = 0; $i < count($farm_sched_list); $i++){
			$data[] = (object)array(
				'schedule_id'		=>	$farm_sched_list[$i]->schedule_id,
				'delivery_date'		=>	$this->dateFormat($farm_sched_list[$i]->date_of_delivery),
				'delivery_time'		=>	$this->farmDeliveryTimes($farm_sched_list[$i]->farm_id),
				'farm_name'			=>	$this->farmNames($farm_sched_list[$i]->farm_id,$farm_sched_list[$i]->date_of_delivery,$farm_sched_list[$i]->unique_id),
				'truck_name'		=>	$farm_sched_list[$i]->truck_name,
				'status'			=>	$farm_sched_list[$i]->status,
				'truck_id'			=>	$farm_sched_list[$i]->truck_id,
				'driver'			=>	$this->getDriver($farm_sched_list[$i]->driver_id),
				'selected_driver'	=>	$this->schedSelecteDriver($farm_sched_list[$i]->unique_id,$farm_sched_list[$i]->driver_id),
				'selected_delivery'	=>	$this->schedSelectedDelivery($farm_sched_list[$i]->unique_id),
				'unique_id'			=>	$farm_sched_list[$i]->unique_id,
				'sched_tool_status'				=>	$this->schedToolStatus($farm_sched_list[$i]->delivery_unique_id)
			);
		}

		$drivers = User::where('type_id','=',2)->orderBy('username','asc')->lists("username","id")->toArray();


		$delivery_count = array();
		for($i = 0; $i <= 7; $i++){
			$delivery_count[] = $i;
		}


		return view( "loading.ajax.index",compact("data","drivers","delivery_count"));

	}

	/*
	*	selected driver on scheduled items
	*/
	private function schedSelecteDriver($unique_id,$driver_id){

		$schedToolData = SchedTool::select('driver_id')->where('farm_sched_unique_id','=',$unique_id)->get()->toArray();
		$driver = !empty($schedToolData[0]['driver_id']) ? $schedToolData[0]['driver_id'] : $driver_id;
		$drivers = User::where('type_id','=',2)->where('id','=',$driver)->select('id')->get()->toArray();

		$output = !empty($drivers[0]['id']) ? $drivers[0]['id'] : NULL;

		return $output;

	}

	/*
	*	selected delivery number
	*/
	private function schedSelectedDelivery($unique_id){

		$schedToolData = SchedTool::select('delivery_number')->where('farm_sched_unique_id','=',$unique_id)->get()->toArray();

		if(!empty($schedToolData[0]['delivery_number'])){
			$output = array(0 => $schedToolData[0]['delivery_number']);
		} else {
			$output = NULL;
		}

		return $output;

	}

	/*
	*	Initialize the scheduled data
	*/
	public function scheduledDataBar(){

		$selected_data = Input::get('selected_data');
		$selected_data = date("Y-m-d",strtotime($selected_data));

		$farm_sched_list = FarmSchedule::select(DB::raw('DATE_FORMAT(feeds_farm_schedule.date_of_delivery, "%Y-%m-%d %h:%i %p") as date_of_delivery'),
									'schedule_id','feeds_type_id','medication_id','unique_id',
									DB::raw('GROUP_CONCAT(farm_id) AS farm_id'),
									DB::raw('GROUP_CONCAT(amount) AS amount'),
									DB::raw('GROUP_CONCAT(bin_id) AS bin_id'),
									'feeds_truck.name as truck_name',
									'feeds_truck.truck_id as truck_id',
									'feeds_farm_schedule.driver_id as driver_id')
							->leftJoin('feeds_truck','feeds_truck.truck_id','=','feeds_farm_schedule.truck_id')
							->where('status','=',0)
							->where('date_of_delivery','LIKE',$selected_data.'%')
							->orderBy('date_of_delivery','desc')
							->groupBy('feeds_farm_schedule.unique_id')
							->get()->toArray();


		return count($farm_sched_list);

	}

	/*
	*	Scheduled items for delivery number dropdown
	*/
	public function scheduledItemDeliveryNumberAPI($data){

		$selected_date = $data['selected_date'];
		$selected_index = $data['selected_index'];
		$unique_id = $data['unique_id'];
		$driver_id = $data['driver_id'];
		$delivery_number = $data['delivery_number'];

		$delivery_data = SchedTool::where('farm_sched_unique_id','=',$unique_id)->get()->toArray();
		$delivery_unique_id = !empty($delivery_data[0]['delivery_unique_id']) ? $delivery_data[0]['delivery_unique_id'] : 0;

		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("unique_id","=",$unique_id)
							->get()->toArray();

		$delivery_time = $this->deliveryTimes($data[0]['farm_id']);
		list($hours, $wrongMinutes) = explode('.', $delivery_time);
		$minutes = ($wrongMinutes < 100 ? $wrongMinutes * 100 : $wrongMinutes) * 0.6 / 100;
		$calculated_hour = $hours . 'hours ' . ceil($minutes) . 'minutes';

		if($delivery_number == 1){
			$start_time = "06:00:00";
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		} elseif ($delivery_number == 0){
			//delete the data
			$schedTool = SchedTool::where('farm_sched_unique_id','=',$unique_id)->delete();
			$start_time = NULL;
			$end_time = NULL;
		}else {
			// get the max delivery number add 10 minutes interval then add the start time and end time
			$items = SchedTool::where('delivery_date','=',$selected_date)
								->where('driver_id','=',$driver_id)
								->where('farm_sched_unique_id','!=',$unique_id)
								->orderBy('delivery_number','desc')
								->get()->toArray();
			$start_time = !empty($items[0]['end_time']) ? date("H:i:s",strtotime($items[0]['end_time']."+ 10 minutes")) : "06:00:00";


			if(date("H",strtotime($start_time)) > 16){
				$start_time = "06:00:00";
			}
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
			//$start_time = "06:00:00";
			//$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		}

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$farm_names = Farms::select(DB::raw("GROUP_CONCAT(name) AS name"))->whereIn('id',$farm)->get()->toArray();

		$data_to_save = array(
			'driver_id'				=>	$driver_id,
			'farm_sched_unique_id'	=>	$unique_id,
			'farm_title'			=>	$farm_names[0]['name'],
			'delivery_number'		=>	$delivery_number,
			'delivery_date'			=>	$selected_date,
			'start_time'			=>	$start_time,
			'end_time'				=>	$end_time,
			'selected_index'		=>	$selected_index
		);

		// delete existing same record
		SchedTool::where('delivery_date',$selected_date)->where('farm_sched_unique_id',$unique_id)->delete();
		FarmSchedule::where('unique_id',$unique_id)->update(['date_of_delivery'=>$selected_date." ".$start_time]);

		if($delivery_number != 0){
			// save record
			SchedTool::insert($data_to_save);
		}

		if($driver_id ==0){
			SchedTool::where('farm_sched_unique_id',$unique_id)->delete();
		}

		// check if the delivery is already created
		if($delivery_unique_id != 0){
			//update the delivery
			$this->updateCreatedLoadAPI($delivery_unique_id);
		}

		$output = $this->schedToolOutput($selected_date);


		return $output;
	}

	/*
	*	Scheduled items for delivery number dropdown
	*/
	public function scheduledItemDeliveryNumber(){

		$selected_date = Input::get('selected_date');
		$selected_date = date("Y-m-d",strtotime($selected_date));
		$selected_index = Input::get('selected_index');
		$unique_id = Input::get('unique_id');
		$driver_id = Input::get('driver_id');
		$delivery_number = Input::get('delivery_number');

		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("unique_id","=",$unique_id)
							->get()->toArray();

		$delivery_time = $this->deliveryTimes($data[0]['farm_id']);
		list($hours, $wrongMinutes) = explode('.', $delivery_time);
		$minutes = ($wrongMinutes < 100 ? $wrongMinutes * 100 : $wrongMinutes) * 0.6 / 100;
		$calculated_hour = $hours . 'hours ' . ceil($minutes) . 'minutes';

		if($delivery_number == 1){
			$start_time = "06:00:00";
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		} elseif ($delivery_number == 0){
			//delete the data
			$schedTool = SchedTool::where('farm_sched_unique_id','=',$unique_id)->delete();
			$start_time = NULL;
			$end_time = NULL;
		}else {
			// get the max delivery number add 10 minutes interval then add the start time and end time
			$items = SchedTool::where('delivery_date','=',$selected_date)
								->where('driver_id','=',$driver_id)
								->where('farm_sched_unique_id','!=',$unique_id)
								->orderBy('delivery_number','desc')
								->get()->toArray();
			$start_time = !empty($items[0]['end_time']) ? date("H:i:s",strtotime($items[0]['end_time']."+ 10 minutes")) : "06:00:00";


			if(date("H",strtotime($start_time)) > 16){
				$start_time = "06:00:00";
			}
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
			//$start_time = "06:00:00";
			//$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		}

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$farm_names = Farms::select(DB::raw("GROUP_CONCAT(name) AS name"))->whereIn('id',$farm)->get()->toArray();

		$data_to_save = array(
			'driver_id'				=>	$driver_id,
			'farm_sched_unique_id'	=>	$unique_id,
			'farm_title'			=>	$farm_names[0]['name'],
			'delivery_number'		=>	$delivery_number,
			'delivery_date'			=>	$selected_date,
			'start_time'			=>	$start_time,
			'end_time'				=>	$end_time,
			'selected_index'		=>	$selected_index
		);

		// delete existing same record
		SchedTool::where('delivery_date',$selected_date)->where('farm_sched_unique_id',$unique_id)->delete();

		if($delivery_number != 0){
			// save record
			SchedTool::insert($data_to_save);
		}

		if($driver_id ==0){
			SchedTool::where('farm_sched_unique_id',$unique_id)->delete();
		}

		$output = $this->schedToolOutput($selected_date);


		return $output;
	}

	/*
	*	Scheduled items for driver dropdown
	*/
	public function scheduledItemDriverAPI($data,$request){

		$selected_date = $data['selected_date'];
		$unique_id = $data['unique_id'];
		$driver_id = $data['driver_id'];
		$user_id = $data['user_id'];
		$delivery_data = SchedTool::where('farm_sched_unique_id','=',$unique_id)->get()->toArray();
		$delivery_number = !empty($delivery_data[0]['delivery_number']) ? $delivery_data[0]['delivery_number'] : 0;
		$delivery_unique_id = !empty($delivery_data[0]['delivery_unique_id']) ? $delivery_data[0]['delivery_unique_id'] : 0;
		$selected_index = $delivery_number;

		// if driver_id = 0 delete the entry on SchedTool
		//if($driver_id == 0){
		//	SchedTool::where('farm_sched_unique_id','=',$unique_id)->delete();
		//}

		// count the deliveries of the driver
		$delivery_counter = SchedTool::select('farm_sched_unique_id')
																			->where('delivery_date',$selected_date)
																			->where('driver_id',$driver_id)
																			->count();
		if($delivery_counter > 7){
			return "More than 7 deliveries";
		}

		if($request == "movetoschedtool"){
		$delivery_number = $this->selectedIndexPosition($delivery_number,$selected_index=NULL,$driver_id,$selected_date);
		}
		// get the id's of farm
		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("unique_id","=",$unique_id)
							->get()->toArray();

		$delivery_time = $this->deliveryTimes($data[0]['farm_id']);
		list($hours, $wrongMinutes) = explode('.', $delivery_time);
		$minutes = ($wrongMinutes < 100 ? $wrongMinutes * 100 : $wrongMinutes) * 0.6 / 100;
		$calculated_hour = $hours . 'hours ' . ceil($minutes) . 'minutes';

		if($delivery_number == 1){
			$start_time = "06:00:00";
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		} else {
			// get the max delivery number add 10 minutes interval then add the start time and end time
			$items = SchedTool::where('delivery_date','=',$selected_date)
								->where('driver_id','=',$driver_id)
								->where('farm_sched_unique_id','!=',$unique_id)
								->orderBy('delivery_number','desc')
								->get()->toArray();
			$start_time = !empty($items[0]['end_time']) ? date("H:i:s",strtotime($items[0]['end_time']."+ 10 minutes")) : "06:00:00";

			//if(date("H",strtotime($start_time)) > 16){
				//$start_time = "06:00:00";
			//}

			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		}

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$farm_names = Farms::select(DB::raw("GROUP_CONCAT(name) AS name"))->whereIn('id',$farm)->get()->toArray();

		$data_to_save = array(
			'driver_id'							=>	$driver_id,
			'farm_sched_unique_id'	=>	$unique_id,
			'farm_title'						=>	$farm_names[0]['name'],
			'delivery_number'				=>	$delivery_number,
			'delivery_date'					=>	$selected_date,
			'start_time'						=>	$start_time,
			'end_time'							=>	$end_time,
			'selected_index'				=>	$selected_index
		);

		// delete existing same record
		SchedTool::where('delivery_date',$selected_date)->where('farm_sched_unique_id',$unique_id)->delete();
		FarmSchedule::where('unique_id',$unique_id)->update(['date_of_delivery'=>$selected_date." ".$start_time,'user_id'=>$user_id]);

		if($delivery_number != 0 || $driver_id !=0){
			// save record
			SchedTool::insert($data_to_save);
		}

		if($driver_id ==0){
			SchedTool::where('farm_sched_unique_id',$unique_id)->delete();
		}

		// check if the delivery is already created
		if($delivery_unique_id != 0){
			//update the delivery
			$this->updateCreatedLoadAPI($delivery_unique_id,$user_id);
		}

		$this->updateScheduledDriver($driver_id,$unique_id);

		$output = $this->schedToolOutput($selected_date);

		return $output;
	}

	/*
	* update the created load and remove the previous notification for mobile app
	*/
	private function updateCreatedLoadAPI($delivery_unique_id,$user_id){
		$farm_sched_data = FarmSchedule::select('unique_id','date_of_delivery')->where('delivery_unique_id',$delivery_unique_id)->first();
		$farm_sched_unique_id = $farm_sched_data->unique_id;
		$deliveries_data = Deliveries::where('unique_id','=',$delivery_unique_id)->first();
		$farm_sched_date_of_delivery = $deliveries_data->delivery_date;

		$data_previous_driver = array(array(
			'driver_id'					=>	$deliveries_data->driver_id,
			'truck_id'					=>	$deliveries_data->truck_id,
			'delivery_date'			=>	$deliveries_data->delivery_date,
		));

		$this->loadTruckDriverNotification($data_previous_driver,$deliveries_data->unique_id);
		DB::table('feeds_mobile_notification')->where('unique_id',$deliveries_data->unique_id)->delete();

		$this->loadToTruckUpdateAPI($delivery_unique_id,$farm_sched_unique_id,$user_id);

	}

	/*
	*	Scheduled items for driver dropdown
	*/
	public function scheduledItemDriver(){

		$selected_date = Input::get('selected_date');
		$selected_date = date("Y-m-d",strtotime($selected_date));
		$unique_id = Input::get('unique_id');
		$driver_id = Input::get('driver_id');
		$delivery_data = SchedTool::where('farm_sched_unique_id','=',$unique_id)
									->get()->toArray();
		$delivery_number = !empty($delivery_data[0]['delivery_number']) ? $delivery_data[0]['delivery_number'] : 0;
		$delivery_number = $this->selectedIndexPosition($delivery_number,$selected_index=NULL,$driver_id,$selected_date);

		// get the id's of farm
		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("unique_id","=",$unique_id)
							->get()->toArray();

		$delivery_time = $this->deliveryTimes($data[0]['farm_id']);
		list($hours, $wrongMinutes) = explode('.', $delivery_time);
		$minutes = ($wrongMinutes < 100 ? $wrongMinutes * 100 : $wrongMinutes) * 0.6 / 100;
		$calculated_hour = $hours . 'hours ' . ceil($minutes) . 'minutes';

		if($delivery_number == 1){
			$start_time = "06:00:00";
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		} else {
			// get the max delivery number add 10 minutes interval then add the start time and end time
			$items = SchedTool::where('delivery_date','=',$selected_date)
								->where('driver_id','=',$driver_id)
								->where('farm_sched_unique_id','!=',$unique_id)
								->orderBy('delivery_number','desc')
								->get()->toArray();
			$start_time = !empty($items[0]['end_time']) ? date("H:i:s",strtotime($items[0]['end_time']."+ 10 minutes")) : "06:00:00";

			if(date("H",strtotime($start_time)) > 16){
				$start_time = "06:00:00";
			}
			$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
			//$start_time = "06:00:00";
			//$end_time = date("H:i:s",strtotime($start_time."+".$calculated_hour));
		}

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$farm_names = Farms::select(DB::raw("GROUP_CONCAT(name) AS name"))->whereIn('id',$farm)->get()->toArray();

		$data_to_save = array(
			'driver_id'				=>	$driver_id,
			'farm_sched_unique_id'	=>	$unique_id,
			'farm_title'			=>	$farm_names[0]['name'],
			'delivery_number'		=>	$delivery_number,
			'delivery_date'			=>	$selected_date,
			'start_time'			=>	$start_time,
			'end_time'				=>	$end_time,
			'selected_index'		=>	$selected_index
		);

		// delete existing same record
		SchedTool::where('delivery_date',$selected_date)->where('farm_sched_unique_id',$unique_id)->delete();

		if($delivery_number != 0 || $driver_id !=0){
			// save record
			SchedTool::insert($data_to_save);
		}

		if($driver_id ==0){
			SchedTool::where('farm_sched_unique_id',$unique_id)->delete();
		}

		$this->updateScheduledDriver($driver_id,$unique_id);

		$output = $this->schedToolOutput($selected_date);

		return $output;
	}

	/*
	*
	*/
	private function updateScheduledDriver($driver_id,$unique_id){
		$driver = array('driver_id'=>$driver_id);
		FarmSchedule::where('unique_id',$unique_id)->update($driver);
	}


	/*
	*	Selected index positioner
	*/
	private function selectedIndexPosition($delivery_number,$selected_index,$driver_id,$delivery_date){

		$sched_data = SchedTool::where('driver_id','=',$driver_id)
					->where('delivery_date','=',$delivery_date)
					->orderBy('delivery_number','desc')
					->get()->toArray();

		//if(!empty($selected_index) || $selected_index == 0){
			//$output = $delivery_number;
		//} else {
			$output = !empty($sched_data[0]['delivery_number']) ? $sched_data[0]['delivery_number'] + 1 : 1;
		//}

		return $output;
	}

	/*
	*	Sched tool data checker
	*/
	private function schedToolDataChecker($selected_date,$delivery_number,$driver_id){
		$output = SchedTool::where('delivery_date','=',$selected_date)
						->where('delivery_number','=',$delivery_number)
						->where('driver_id','=',$driver_id)
						->exists();
		return $output;
	}

	/*
	*	Data output for sched tool bar
	*/
	public function schedToolOutputAPI($delivery_date=NULL){

		$delivery_date = !empty($delivery_date) ? $delivery_date : date("Y-m-d",strtotime(Input::get('selected_data')));

		$stDrivers = SchedTool::select(DB::raw('DISTINCT(driver_id) as driver_id,
								(SELECT username FROM feeds_user_accounts WHERE id = driver_id) as driver_name'))
								->where('delivery_date','=',$delivery_date)
								->get()->toArray();
		$data = array();
		for($i = 0; $i < count($stDrivers); $i++){

			$data[] = array(
					'driver_id'	=> $stDrivers[$i]['driver_id'],
					'title'			=> $stDrivers[$i]['driver_name'],
					'eta'				=> $this->deliveriesETAPerDriver($stDrivers[$i]['driver_id'],$delivery_date),
					'schedule'	=> $this->schedToolLevelTwoAPI($stDrivers[$i]['driver_id'],$delivery_date),
					'dar'				=> $this->driverActivityReport($delivery_date)
				);
		}


		return $data;

	}

	/*
	*	Data output for sched tool bar
	*/
	public function schedToolOutput($delivery_date=NULL){

		$delivery_date = !empty($delivery_date) ? $delivery_date : date("Y-m-d",strtotime(Input::get('selected_data')));

		$stDrivers = SchedTool::select(DB::raw('DISTINCT(driver_id) as driver_id,
								(SELECT username FROM feeds_user_accounts WHERE id = driver_id) as driver_name'))
								->where('delivery_date','=',$delivery_date)
								->get()->toArray();
		$data = array();
		for($i = 0; $i < count($stDrivers); $i++){

			$data[] = array(
					'driver_id'	=> $stDrivers[$i]['driver_id'],
					'title'			=> $stDrivers[$i]['driver_name'],
					'eta'				=> $this->deliveriesETAPerDriver($stDrivers[$i]['driver_id'],$delivery_date),
					'schedule'	=> $this->schedToolLevelTwo($stDrivers[$i]['driver_id'],$delivery_date),
					'dar'				=> $this->driverActivityReport($delivery_date)
				);
		}


		return $data;

	}

	/*
	*	scheduled data
	*/
	private function schedToolLevelTwoAPI($driver_id,$delivery_date){

		$schedData = SchedTool::select('driver_id','delivery_number','status','farm_sched_unique_id','delivery_unique_id','start_time','end_time',DB::raw('farm_title as text'))
								->where('driver_id','=',$driver_id)
								->where('delivery_date','=',$delivery_date)
								->get()->toArray();

		for($i = 0; $i < count($schedData); $i++){

			$output[] = array(
				'start'				=>	date("H:i",strtotime($schedData[$i]['start_time'])),
				'end'					=>	date("H:i",strtotime($schedData[$i]['end_time'])),
				'text'				=>	$schedData[$i]['text'],
				'data'				=> 	array(
											'delivery_number'	=>	$schedData[$i]['delivery_number'],
											'unique_id'			=>	$schedData[$i]['farm_sched_unique_id'],
											'driver_id'			=>	$schedData[$i]['driver_id'],
											'status'			=>	$this->statusSchedToolAPI($schedData[$i]['status'],$schedData[$i]['delivery_unique_id'])
											)
			);

		}

		return $output;

	}

	private function statusSchedToolAPI($status,$delivery_unique_id){

		if($status == 'scheduled') {

			$status = "created";

		} else if($status == 'ongoing'){

			$status = "ongoing_green";

		} else if($status == 'unloaded'){

			$status = "ongoing_red";

		} else if($status == 'pending'){

			$status = "ongoing_green";

		} else if($status == 'delivered'){

			$status = "completed";

		} else {

			$status = $status;

		}

		return $status;
	}

	/*
	*	scheduled data
	*/
	private function schedToolLevelTwo($driver_id,$delivery_date){

		$schedData = SchedTool::select('driver_id','delivery_number','status','farm_sched_unique_id','delivery_unique_id','start_time','end_time',DB::raw('farm_title as text'))
								->where('driver_id','=',$driver_id)
								->where('delivery_date','=',$delivery_date)
								->get()->toArray();

		for($i = 0; $i < count($schedData); $i++){

			$status = !empty($schedData[$i]['delivery_unique_id']) ? $this->deliveriesStatus($schedData[$i]['delivery_unique_id']) : $schedData[$i]['status'];

			$output[] = array(
				'start'				=>	date("H:i",strtotime($schedData[$i]['start_time'])),
				'end'					=>	date("H:i",strtotime($schedData[$i]['end_time'])),
				'text'				=>	$schedData[$i]['text'],
				'data'				=> 	array(
											'delivery_number'	=>	$schedData[$i]['delivery_number'],
											'unique_id'			=>	$schedData[$i]['farm_sched_unique_id'],
											'driver_id'			=>	$schedData[$i]['driver_id'],
											'status'			=>	$schedData[$i]['status']//$status
											)
			);

		}

		return $output;

	}

	/*
	*	ETA Detector per driver
	*/
	private function deliveriesETAPerDriver($driver_id,$delivery_date)
	{
		$schedData = SchedTool::select('driver_id','delivery_number','status','farm_sched_unique_id','delivery_unique_id','start_time','end_time',DB::raw('farm_title as text'))
								->where('driver_id','=',$driver_id)
								->where('delivery_date','=',$delivery_date)
								->whereNotIn('status',['scheduled','delivered','created'])
								->orderBy('start_time','asc')
								->get()->toArray();

		$output = array();
		for($i = 0; $i < count($schedData); $i++){

			$status = $schedData[$i]['status']; //!empty($schedData[$i]['delivery_unique_id']) ? $this->deliveriesStatus($schedData[$i]['delivery_unique_id']) : $schedData[$i]['status'];
			$scheduled_start_time = date("H:i",strtotime($schedData[0]['start_time']));
			$scheduled_end_time = date("H:i",strtotime($schedData[$i]['end_time']));
			$farms_delivery_hours = $this->farmsDeliveryHours($schedData[$i]['farm_sched_unique_id']);
			$delivery_ETA = $this->deliveriesETA($status,$schedData[$i]['delivery_unique_id'],$scheduled_end_time,$farms_delivery_hours);

			$output[] = array(
				'status'	=>	$status,
				'start_time'	=>	$scheduled_start_time,
				'end_time'		=>	$scheduled_end_time,
				'farms_hours'	=> $farms_delivery_hours,
				'delivery_eta'	=>	$delivery_ETA,
				'delivery_eta_combined_farm'	=>	date("h:i a",strtotime($delivery_ETA))//date("h:i a",strtotime($delivery_ETA."+ ".$farms_delivery_hours))
			);

		}

		//return json_encode($output);
		if($output != NULL){
			if($output[count($schedData) - 1]['status'] == 'unloaded'){
				return date("h:i a",strtotime($output[count($schedData) - 1]['delivery_eta']));
			}
		}


		return $output != NULL ? $output[count($schedData) - 1]['delivery_eta_combined_farm'] : "--:--";
	}

	/*
	*	Driver Activity Report
	*/
	private function driverActivityReport($delivery_date)
	{

		// always get the scheduled and created just get the last 2 data
		$schedData = SchedTool::select('id','driver_id','delivery_number','status','farm_sched_unique_id','delivery_unique_id','start_time','end_time',DB::raw('farm_title as text'))
								//->where('driver_id','=',$driver_id)
								->where('delivery_date','=',$delivery_date)
								//->whereIn('status',['scheduled','delivered','created'])
								->orderBy('start_time','asc')
								->get()->toArray();

		$data = array();
		$exclude = array();

		if($schedData != NULL){
			for($i=0; $i < count($schedData); $i++){

				$next_delivery_start_time = "--:--";
				$farms_delivery_hours = $this->farmsDeliveryHours($schedData[$i]['farm_sched_unique_id']);
				$farms_delivery_hours = str_replace("hours","h",$farms_delivery_hours);
				$farms_delivery_hours = str_replace(" h","h",$farms_delivery_hours);
				$farms_delivery_hours = str_replace("minutes","m",$farms_delivery_hours);
				$farms_delivery_hours = str_replace(" m","m",$farms_delivery_hours);

				$exclude[] = array($schedData[$i]['id']);
				// next delivery for driver
				$next_delivery_start_time_query = SchedTool::where('driver_id',$schedData[$i]['driver_id'])
																							->whereNotIn('id',$exclude)
																							->where('delivery_date','=',$delivery_date)
																							->orderBy('start_time','asc')
																							->value('start_time');

				if($next_delivery_start_time_query != NULL){
					$next_delivery_start_time = date("g:i a",strtotime($next_delivery_start_time_query));
				}

				$actual_time_back = "--:--"; // get the end time on feeds_driver_stats_delivery_time
				$end_time_driver_stats = DB::table('feeds_driver_stats_delivery_time')->select('end_time')->where('deliveries_unique_id',$schedData[$i]['delivery_unique_id'])->orderBy('id','desc')->first();
				if($end_time_driver_stats != NULL){
					//$actual_time_back = $end_time_driver_stats->end_time;
					if($end_time_driver_stats->end_time != "0000-00-00 00:00:00"){
						$actual_time_back = date("g:i a",strtotime($end_time_driver_stats->end_time));
					}
				}

				$data[] = array(
					'driver_name'				=> 	User::where('id',$schedData[$i]['driver_id'])->value('username'),
					'start_time'				=>	date("g:i a",strtotime($schedData[$i]['start_time'])),
					'farm'							=>	$schedData[$i]['text'],
					'run_time'					=>	$farms_delivery_hours, //get the feeds_farm_schedule farm id's and get the sum of farms delivery time
					'return_time'				=>	$next_delivery_start_time, // next delivery
					'actual_time_back'	=>	$actual_time_back //end time
				);
			}
		}

		return $data;

		// start time = start time
		// truck = driver name
		// farm delivery = text
		// run time = farm delivery delivery time
		// return time = next delivery
		// actual time back = arive time at mill

	}

	/*
	*	Driver Activity Report
	*/
	public function driverActivityReportAPI($delivery_date)
	{

		// always get the scheduled and created just get the last 2 data
		$schedData = SchedTool::select('id','driver_id','delivery_number','status','farm_sched_unique_id','delivery_unique_id','start_time','end_time',DB::raw('farm_title as text'))
								//->where('driver_id','=',$driver_id)
								->where('delivery_date','=',$delivery_date)
								//->whereIn('status',['scheduled','delivered','created'])
								->orderBy('start_time','asc')
								->get()->toArray();

		$data = array();
		$exclude = array();

		if($schedData != NULL){
			for($i=0; $i < count($schedData); $i++){

				$next_delivery_start_time = "--:--";
				$farms_delivery_hours = $this->farmsDeliveryHours($schedData[$i]['farm_sched_unique_id']);
				$farms_delivery_hours = str_replace("hours","h",$farms_delivery_hours);
				$farms_delivery_hours = str_replace(" h","h",$farms_delivery_hours);
				$farms_delivery_hours = str_replace("minutes","m",$farms_delivery_hours);
				$farms_delivery_hours = str_replace(" m","m",$farms_delivery_hours);

				$exclude[] = array($schedData[$i]['id']);
				// next delivery for driver
				$next_delivery_start_time_query = SchedTool::where('driver_id',$schedData[$i]['driver_id'])
																							->whereNotIn('id',$exclude)
																							->where('delivery_date','=',$delivery_date)
																							->orderBy('start_time','asc')
																							->value('start_time');

				if($next_delivery_start_time_query != NULL){
					$next_delivery_start_time = date("g:i a",strtotime($next_delivery_start_time_query));
				}

				$actual_time_back = "--:--"; // get the end time on feeds_driver_stats_delivery_time
				$end_time_driver_stats = DB::table('feeds_driver_stats_delivery_time')->select('end_time')->where('deliveries_unique_id',$schedData[$i]['delivery_unique_id'])->orderBy('id','desc')->first();
				if($end_time_driver_stats != NULL){
					//$actual_time_back = $end_time_driver_stats->end_time;
					if($end_time_driver_stats->end_time != "0000-00-00 00:00:00"){
						$actual_time_back = date("g:i a",strtotime($end_time_driver_stats->end_time));
					}
				}

				$data[] = array(
					'driver_name'				=> 	User::where('id',$schedData[$i]['driver_id'])->value('username'),
					'start_time'				=>	date("g:i a",strtotime($schedData[$i]['start_time'])),
					'farm'							=>	$schedData[$i]['text'],
					'run_time'					=>	$farms_delivery_hours, //get the feeds_farm_schedule farm id's and get the sum of farms delivery time
					'return_time'				=>	$next_delivery_start_time, // next delivery
					'actual_time_back'	=>	$actual_time_back //end time
				);
			}
		}
		dd($data);
		return $data;

		// start time = start time
		// truck = driver name
		// farm delivery = text
		// run time = farm delivery delivery time
		// return time = next delivery
		// actual time back = arive time at mill

	}

	/*
	*	farms delivery timnes
	*/
	private function farmsDeliveryHours($unique_id)
	{
		$data = FarmSchedule::select(DB::raw("GROUP_CONCAT(farm_id) AS farm_id"))
							->where("unique_id","=",$unique_id)
							->get()->toArray();

		$farm = array_unique(explode(",",(string)$data[0]['farm_id']));
		$output = Farms::select('delivery_time')->whereIn('id',$farm)->sum('delivery_time');
		$output = number_format((float)$output, 2, '.', '');

		$delivery_time = $output;
		list($hours, $wrongMinutes) = explode('.', $delivery_time);
		$minutes = ($wrongMinutes < 100 ? $wrongMinutes * 100 : $wrongMinutes) * 0.6 / 100;
		$calculated_hour = $hours . ' hours ' . ceil($minutes) . ' minutes';

		return $calculated_hour;
	}

	/*
	*	ETA Detector for API
	* $status
	* $delivery_unique_id
	* $scheduled_end_time
	* $farms_delivery_hours
	*/
	private function deliveriesETAAPI($status,$deliveries_unique_id,$scheduled_end_time,$farms_delivery_hours)
	{
		$end_time = $scheduled_end_time;

		if($status == "pending"){
			$end_time = $this->deliveryETAAcceptLoadAndTenMinutesAfter($deliveries_unique_id);

		} else if($status == "ongoing"){
			$end_time = $this->deliveryETAAcceptLoadAndTenMinutesAfter($deliveries_unique_id);

		} else if($status == "unloaded"){
			$end_time = $this->deliveryETAUnloadLastAndTenMinutesAfter($deliveries_unique_id);

		}else{
			$end_time = $end_time;
		}

		return $end_time;
	}

	/*
	*	ETA Detector
	*/
	private function deliveriesETA($status,$deliveries_unique_id,$scheduled_end_time,$farms_delivery_hours)
	{
		$end_time = $scheduled_end_time;

		if($status == "pending"){
			//$end_time = $this->deliveriesAcceptedLoadETA($deliveries_unique_id) == NULL ? $end_time : $this->deliveriesAcceptedLoadETA($deliveries_unique_id);
			//$end_time = date("h:i a",strtotime($end_time."+ ".$farms_delivery_hours));
			//return $end_time;
			//When the driver accepted the load and 10 minutes after the driver accepted the load (10 mins interval)
			$end_time = $this->deliveryETAAcceptLoadAndTenMinutesAfter($deliveries_unique_id);

		} else if($status == "ongoing"){
			/*
			//10 minutes after the truck leaves the Mill
			$ten_minutes_interval_mill = $this->deliveryETATenMinutesLeavesMill($deliveries_unique_id);
			if($ten_minutes_interval_mill != "00:00"){
				$end_time = $ten_minutes_interval_mill;
			} else {
				$end_time = $this->deliveriesLeaveMillETA($deliveries_unique_id) == NULL ? $end_time : $this->deliveriesLeaveMillETA($deliveries_unique_id);
			}*/
			$end_time = $this->deliveryETAAcceptLoadAndTenMinutesAfter($deliveries_unique_id);

		} else if($status == "unloaded"){
			/*
			// 10 minutes after the driver emptied the last truck compartment
			$ten_minutes_interval_unload_last_compartment = $this->deliveryETATenMinutesUnloadLastCompartment($deliveries_unique_id);
			if($ten_minutes_interval_unload_last_compartment != "00:00"){
				$end_time = $ten_minutes_interval_unload_last_compartment;
			} else {
				$end_time = $this->deliveriesUnloadLastCompartmentETA($deliveries_unique_id) == NULL ? $end_time : $this->deliveriesUnloadLastCompartmentETA($deliveries_unique_id);
			} */
			$end_time = $this->deliveryETAUnloadLastAndTenMinutesAfter($deliveries_unique_id);

		}else{
			$end_time = $end_time;
		}

		return $end_time;
	}

	/*
	*	ETA every 10 minutes after the truck leaves the Mill (10 mins interval)
	*/
	private function deliveryETAAcceptLoadAndTenMinutesAfter($deliveries_unique_id)
	{
		$ten_mins_interval = DB::table('feeds_driver_stats_drive_time_interval')->where('deliveries_unique_id',$deliveries_unique_id)->orderBy('id','desc')->get();

		if($ten_mins_interval != NULL){
			return date("H:i",strtotime($ten_mins_interval[0]->eta));
		}

		return NULL;
	}

	/*
	*	ETA every 10 minutes after the truck leaves the Mill (10 mins interval)
	*/
	private function deliveryETAUnloadLastAndTenMinutesAfter($deliveries_unique_id)
	{
		$ten_mins_interval = DB::table('feeds_driver_stats_drive_time_interval_mill')->where('deliveries_unique_id',$deliveries_unique_id)->orderBy('id','desc')->get();

		if($ten_mins_interval != NULL){
			return date("H:i",strtotime($ten_mins_interval[0]->eta));
		}

		return NULL;
	}

	/*
	*	ETA every 10 minutes after the truck leaves the Mill (10 mins interval)
	*/
	private function deliveryETATenMinutesLeavesMill($deliveries_unique_id)
	{
		$ten_mins_interval = DB::table('feeds_driver_stats_drive_time_interval')->where('deliveries_unique_id',$deliveries_unique_id)->orderBy('id','desc')->get();

		if($ten_mins_interval != NULL){
			return date("H:i",strtotime($ten_mins_interval[0]->eta));
		}

		return NULL;
	}

	/*
	*	ETA every 10 minutes after the driver emptied the last truck compartment (10 mins interval)
	*/
	private function deliveryETATenMinutesUnloadLastCompartment($deliveries_unique_id)
	{
		$ten_mins_interval = DB::table('feeds_driver_stats_drive_time_interval_mill')->where('deliveries_unique_id',$deliveries_unique_id)->orderBy('id','desc')->get();

		if($ten_mins_interval != NULL){
			return date("H:i",strtotime($ten_mins_interval[0]->eta));
		}

		return NULL;
	}

	/*
	*	Deliveries ETA for accepted Load
	*/
	private function deliveriesAcceptedLoadETA($deliveries_unique_id)
	{
		$time = DB::table('feeds_deliveries_accepted_load')
									->select('created')
									->where('deliveries_unique_id',$deliveries_unique_id)
									->first();

		if($time == NULL){
			return NULL;
		}

		return date("H:i",strtotime($time->created));
	}

	/*
	*	Deliveries ETA when the driver leaves the mill
	*/
	private function deliveriesLeaveMillETA($deliveries_unique_id)
	{
		$time = DB::table('feeds_driver_stats_delivery_time')
									->select('start_time')
									->where('deliveries_unique_id',$deliveries_unique_id)
									->first();

		if($time == NULL){
			return NULL;
		}

		return date("H:i",strtotime($time->start_time));
	}


	/*
	*	Deliveries ETA when the driver unload the last compartment
	*/
	private function deliveriesUnloadLastCompartmentETA($deliveries_unique_id)
	{
		$start_time = DB::table('feeds_deliveries')
									->select('unload_at')
									->where('unique_id',$deliveries_unique_id)
									->orderBy('unload_at','desc')
									->first();

		$time_est = DB::table('feeds_driver_stats_drive_time_google_est_mill')
					->select('hours')
					->where('deliveries_unique_id',$deliveries_unique_id)
					->first();

		if($time_est != NULL){
			$unload_at = date("H:i",strtotime($start_time->unload_at . $this->timeExploder($time_est->hours)));
			return $unload_at;
		}

		return NULL;
	}

	/*
	* time exploder
	*/
	private function timeExploder($time)
	{
		$time = explode(":",$time);
		$time = '+'.$time[0].' hour +'.$time[1].' minutes +'.$time[2].' seconds';
		return $time;
	}


	/*
	*	deliveries status counter
	*/
	public function deliveriesStatus($unique_id){

		$status = "";

		$loads  = Deliveries::where('unique_id','=',$unique_id)->count();
		$delivered = Deliveries::where('unique_id','=',$unique_id)->where('status','=',3)->count();

		if($delivered == $loads){
			$status = "delivered";
		}else{
			$status = "pending";
		}

		return $status;
	}

	/*
	*	get the delivery time of the farm
	*/
	private function deliveryTimes($ids){

		$farm = array_unique(explode(",",(string)$ids));
		$output = Farms::select('delivery_time')->whereIn('id',$farm)->max('delivery_time');

		$counter = count($farm);
		$return = 0;
		if($counter == 1){
			$return = number_format((float)$output, 2, '.', '');
		} else{
			$added_minutes = ($counter-1) * 0.50;
			$final = $output + $added_minutes;
			$return = number_format((float)$final, 2, '.', '');
		}

		return $return;

	}

	/*
	*	update the sched time tool
	*/
	public function updateScheduledItemAPI($data){

		$delivery_number = $data['delivery_number'];
		$driver_id = $data['driver_id'];
		$unique_id = $data['unique_id'];
		$start_time = $data['start_time'];
		$end_time = $data['end_time'];

		if($end_time == "00:00"){
			$end_time = "23:50:00";
		}


		$delivery_date = SchedTool::where('farm_sched_unique_id',$unique_id)->get()->toArray();
		$delivery_date = $delivery_date[0]['delivery_date'];

		$sched_time = array(
						'start_time'			=>	$start_time,
						'end_time'				=>	$end_time
					);

		$update = SchedTool::where('farm_sched_unique_id',$unique_id)->update($sched_time);

		$driver_data = SchedTool::where('delivery_date',$delivery_date)
								->where('driver_id',$driver_id)
								->orderBy('start_time')
								->get()->toArray();

		for($i = 0; $i < count($driver_data); $i++){
			$data = array('delivery_number' => $i+1);
			SchedTool::where('farm_sched_unique_id',$driver_data[$i]['farm_sched_unique_id'])->update($data);
		}

		$output = array(
			'status'		=> 	$update,
			'delivery_date'	=>	$delivery_date
		);
		$this->updateFarmSchedDelivery($unique_id,$delivery_date." ".$start_time);
		//$this->updateFarmDeliveryTime($unique_id,$sched_time);
		return $output;

	}


	/*
	*	update the sched time tool
	*/
	public function updateScheduledItem(){
		$data = Input::get('data');

		$delivery_number = $data['delivery_number'];
		$driver_id = $data['driver_id'];
		$unique_id = $data['unique_id'];
		$start_time = Input::get('start');
		$end_time = Input::get('end');

		if($end_time == "00:00"){
			$end_time = "23:50:00";
		}


		$delivery_date = SchedTool::where('farm_sched_unique_id',$unique_id)->get()->toArray();
		$delivery_date = $delivery_date[0]['delivery_date'];

		$sched_time = array(
						'start_time'			=>	$start_time,
						'end_time'				=>	$end_time
					);

		$update = SchedTool::where('farm_sched_unique_id',$unique_id)->update($sched_time);

		$driver_data = SchedTool::where('delivery_date',$delivery_date)
								->where('driver_id',$driver_id)
								->orderBy('start_time')
								->get()->toArray();

		for($i = 0; $i < count($driver_data); $i++){
			$data = array('delivery_number' => $i+1);
			SchedTool::where('farm_sched_unique_id',$driver_data[$i]['farm_sched_unique_id'])->update($data);
		}

		$output = array(
			'status'		=> 	$update,
			'delivery_date'	=>	$delivery_date
		);
		$this->updateFarmSchedDelivery($unique_id,$delivery_date." ".$start_time);
		//$this->updateFarmDeliveryTime($unique_id,$sched_time);
		return $output;

	}

	/*
	*	Update the farm schedule and the delivery time
	*/
	private function updateFarmSchedDelivery($farm_sched_unique_id,$delivery_date){

		$farm_sched_data = FarmSchedule::where('unique_id',$farm_sched_unique_id)->get()->toArray();

		if($farm_sched_data != NULL){
			$delivery_unique_id = $farm_sched_data[0]['delivery_unique_id'];
			FarmSchedule::where('unique_id',$farm_sched_unique_id)->update(['date_of_delivery'=>$delivery_date]);
			Deliveries::where('unique_id',$delivery_unique_id)->update(['delivery_date'=>$delivery_date]);
		}

	}

	/*
	*	update farm delivery time
	*/
	private function updateFarmDeliveryTime($unique_id,$sched_time){
		$sched_data = FarmSchedule::where('unique_id',$unique_id)->get()->toArray();

		$StartTime= $sched_time['start_time'];
		$EndTime = $sched_time['end_time'];
		$sst = strtotime($StartTime);
		$eet=  strtotime($EndTime);
		$diff= $eet-$sst;
		$timeElapsed= gmdate("H:i",$diff);
		$hms = explode(":", $timeElapsed);
		$hour = $hms[0];
		$minutes = $hms[1]*60/3600;
     	$new_time = $hour + $minutes;

		for($i=0; $i < count($sched_data); $i++){
			$delivery_time = array('delivery_time' => number_format((float)$new_time, 1, '.', ''));
			Farms::where('id',$sched_data[$i]['farm_id'])->update($delivery_time);
		}

	}

	/*
	*	Sched Tool Delivery Number Validator
	*/
	public function deliveryNumberValidateAPI($data){

		$selected_date = $data['selected_date'];
		$delivery_number = $data['delivery_number'];
		$driver_id = $data['driver_id'];
		$unique_id = $data['unique_id'];

		$selected_index = SchedTool::select('delivery_number')
						->where('delivery_date','=',$selected_date)
						->where('driver_id','=',$driver_id)
						//->orderBy('delivery_number','desc')
						->first()->toArray();

		$data_exists = $this->schedToolDataChecker($selected_date,$delivery_number,$driver_id);

		if($data_exists == true){
			$output = array(
				'output'			=>	1,
				'selected_index'	=>	!empty($selected_index) ? $selected_index['delivery_number'] : 0
			);
		} else{
			$output = array('output'=>0,'selected_index'=>0);
		}

		if($output['selected_index'] == 0){
			$data = array(
				'delivery_number'	=>	0,
				'selected_index'	=>	0,
				'farm_title'		=>	NULL,
				'start_time'		=>	NULL,
				'end_time'			=>	NULL
			);

			SchedTool::where('farm_sched_unique_id',$unique_id)
						->update($data);
		}

		return $output;
	}

	/*
	*	Sched Tool Delivery Number Validator
	*/
	public function deliveryNumberValidate(){

		$selected_date = Input::get('selected_date');
		$selected_date = date("Y-m-d",strtotime($selected_date));
		$delivery_number = Input::get('delivery_number');
		$driver_id = Input::get('driver_id');
		$unique_id =Input::get('unique_id');

		$selected_index = SchedTool::select('selected_index')
						->where('farm_sched_unique_id','=',$unique_id)
						->where('driver_id','=',$driver_id)
						->get()->toArray();

		$data_exists = $this->schedToolDataChecker($selected_date,$delivery_number,$driver_id);

		if($data_exists == true){
			$output = array(
				'output'			=>	1,
				'selected_index'	=>	!empty($selected_index[0]['selected_index']) ? $selected_index[0]['selected_index'] : 0
			);
		} else{
			$output = array('output'=>0,'selected_index'=>0);
		}

		if($output['selected_index'] == 0){
			$data = array(
				'delivery_number'	=>	0,
				'selected_index'	=>	0,
				'farm_title'		=>	NULL,
				'start_time'		=>	NULL,
				'end_time'			=>	NULL
			);

			SchedTool::where('farm_sched_unique_id',$unique_id)
						->update($data);
		}

		return $output;
	}

	/*
	*	Total Tons Initializer
	*/
	public function totalTons(){

		$delivery_date = Input::get('delivery_date');
		$delivery_date = date("Y-m-d",strtotime($delivery_date));

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->get()->toArray();

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');

		return $total_tons;

	}

	/*
	*	Total Tons scheduled
	*/
	public function totalTonsScheduled(){

		$delivery_date = Input::get('delivery_date');
		$delivery_date = date("Y-m-d",strtotime($delivery_date));

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->whereIn('status',array('created','scheduled','pending','ongoing'))
											->get()->toArray();

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');


		$deliveries_unique_id = SchedTool::select('delivery_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->where('status','ongoing')
											->get()->toArray();

		$deliveries_total_tons = Deliveries::whereIn('unique_id',$deliveries_unique_id)->where('status',3)->sum('amount');

		$total_tons = $total_tons - $deliveries_total_tons;

		return $total_tons <= 0 ? 0 : $total_tons;

	}

	/*
	*	Total Tons delivered
	*/
	public function totalTonsDelivered(){

		$delivery_date = Input::get('delivery_date');
		$delivery_date = date("Y-m-d",strtotime($delivery_date));

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->where('status','delivered')
											->get()->toArray();

		$deliveries_unique_id = SchedTool::select('delivery_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->whereIn('status',['ongoing','pending','unloaded'])
											->get()->toArray();

		$deliveries_total_tons = Deliveries::whereIn('unique_id',$deliveries_unique_id)->whereIn('status',[2,3])->sum('amount');

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');

		return $total_tons+$deliveries_total_tons;

	}


	/*
	*	Total Tons Initializer
	*/
	public function totalTonsAPI($delivery_date){

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->get()->toArray();

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');

		return $total_tons;

	}

	/*
	*	Total Tons scheduled
	*/
	public function totalTonsScheduledAPI($delivery_date){

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->whereIn('status',array('created','scheduled','pending','ongoing'))
											->get()->toArray();

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');


		$deliveries_unique_id = SchedTool::select('delivery_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->where('status','ongoing')
											->get()->toArray();

		$deliveries_total_tons = Deliveries::whereIn('unique_id',$deliveries_unique_id)->where('status',3)->sum('amount');

		$total_tons = $total_tons - $deliveries_total_tons;

		return $total_tons <= 0 ? 0 : $total_tons;

	}

	/*
	*	Total Tons delivered
	*/
	public function totalTonsDeliveredAPI($delivery_date){

		// fetch the data to sched tool data and get the farm_sched_unique_id
		$farm_sched_unique_id = SchedTool::select('farm_sched_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->where('status','delivered')
											->get()->toArray();

		$deliveries_unique_id = SchedTool::select('delivery_unique_id')
											->where('delivery_date','LIKE',$delivery_date."%")
											->whereIn('status',['ongoing','pending','unloaded'])
											->get()->toArray();

		$deliveries_total_tons = Deliveries::whereIn('unique_id',$deliveries_unique_id)->whereIn('status',[2,3])->sum('amount');

		// fetch the farm sched data via unique_id
		$total_tons = FarmSchedule::whereIn('unique_id',$farm_sched_unique_id)->sum('amount');

		return $total_tons+$deliveries_total_tons;

	}

	/*
	*	Get the load of scheduling tool
	*/
	public function getLoads()
	{
		$delivery_date = date('Y-m-d',strtotime(Input::get('date')));
		$type = Input::get('status');

		$stDrivers = SchedTool::select(DB::raw('DISTINCT(driver_id) as driver_id,
								(SELECT username FROM feeds_user_accounts WHERE id = driver_id) as driver_name'))
								->where('delivery_date','LIKE',$delivery_date."%")
								->where('status',$type)
								->get()->toArray();
		$data = array();
		for($i = 0; $i < count($stDrivers); $i++){
			$data[] = array(
					'title'		=> $stDrivers[$i]['driver_name'],
					'eta'		=>	'Drivers ETA',
					'schedule'	=> $this->schedToolLevelTwo($stDrivers[$i]['driver_id'],$delivery_date)
				);
		}

		return $data;
	}

	/*
	*	Get the load of scheduling tool
	*/
	public function schedToolStatusUpdate()
	{

		$status = Input::get('status');
		$unique_id = Input::get('unique_id');

		SchedTool::where('delivery_unique_id',$unique_id)->update(['status'=>$status]);

	}

	/*
	*	Feed type lists
	*/
	public function binFeedTypesLists(){

		$bin_id = Input::get("bin_id");

		$bin_feed_type = DB::table("feeds_bin_history")->select("feed_type")->where("bin_id",$bin_id)->orderBy("history_id","desc")->first();

		$feedTypes = DB::table('feeds_feed_types')
						->select('name','type_id')
						->where('name','!=','None')
						->orderBy('name')
						->get();

		$feedTypes = $this->feedTypesListsSelected($feedTypes,$bin_feed_type->feed_type);

		return $feedTypes;
	}

	/*
	*	Feed type lists
	*/
	private function feedTypesListsSelected($feedsLists,$bin_feed_type){

		$option_lists = "";

		foreach($feedsLists as $k => $v){
			if($v->type_id == $bin_feed_type){
				$option_lists .= "<option value='".$v->type_id."' selected>".$v->name."</option>";
			} else {
				$option_lists .= "<option value='".$v->type_id."'>".$v->name."</option>";
			}
		}

		return $option_lists;

	}


}
