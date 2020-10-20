<?php

namespace App\Http\Controllers;

use App\Farms;
use App\Tag;
use App\Bins;
use App\User;
use App\Farmer;
use App\FeedTypes;
use App\BinSize;
use App\BinsHistory;
use Request;
use Cache;
use DB;
use Input;
use App\Http\Requests;
use App\Http\Requests\FarmsRequest;
use App\Http\Requests\BinSizeRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Controllers\AuthController;
//use Illuminate\Http\Request;
use Auth;
use Storage;
use Artisan;

class FarmsController extends Controller
{
		/**
		* Create a new farms controller instance.
		*/
		public function __construct()
		{
			$this->middleware('auth',['except' => ['destroyLeftOvers']]);
		}

	  /**
	   * Display a listing of the resource.
	   *
	   * @return Response
	   */
	  public function index()
	  {
				$farms = DB::table('feeds_farms')
							->leftJoin('feeds_bins','feeds_farms.id', '=', 'feeds_bins.farm_id')
							->select('feeds_farms.*', (DB::raw('count(feeds_bins.bin_id) AS totalBins')))
							->orderBy('feeds_farms.name')
							->groupBy('feeds_farms.id')
							->get();


	      return view('farms.index', compact('farms'));

	  }



    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
				$tags = Tag::lists('name', 'id');
				$farmTypes = $this->farmTypes();
				$selectedFarmType = array('none' => 'none');
				$farmOwner = array("none"=>"none","H & H Farms"=>"H & H Farms");
				$selectedFarmOwner = array("none"=>"none");
        $update_notification = array("disable"=>"Disable","enable"=>"Enable");
        $update_notification_selected = array("disable"=>"Disable");

        return view('farms.create', compact('tags','farmTypes','selectedFarmType','farmOwner','selectedFarmOwner','update_notification','update_notification_selected'));
    }

		/*
		*	Bin Sizes
		*/
		private function farmTypes(){

			$data = array(
				'none' 		=> 	'None',
				'farrowing' => 	'Farrowing',
				'nursery'	=>	'Nursery',
				'finisher' 	=> 	'Finisher'
			);

			return $data;

		}

    /**
     * Store a newly created resource in storage.
     *
     * @param  FarmsRequestg  $request
     * @return Response
     */
    public function store(FarmsRequest $request)
    {
			$this->createFarms($request);
			Cache::forget('farms_lists');

			flash()->overlay("Your farm has been successfully created!", "Good Job");

			return redirect('farms');

    }

    /**
     * Display the specified resource.
     *
     * @param  Farms  $farm
     * @return Response
     */
    public function show(Farms $farm)
    {
        return view('farms.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Farms $farms)
    {

				Cache::forget('farms_lists');
				$tags = Tag::lists('name', 'id');
				$farmTypes = $this->farmTypes();
				$selectedFarmType = array('none' => $farms->farm_type);
				if($farms->owner == NULL || $farms->owner == "none" ){
					$farmOwner = array("none"=>"none", "H & H Farms"=>"H & H Farms");
				} else {
					$farmOwner = array("none"=>"none",$farms->owner => $farms->owner);
				}
				$selectedFarmOwner = array($farms->owner=>$farms->owner);
        $update_notification = array("disable"=>"Disable","enable"=>"Enable");
        $update_notification_selected = array($farms->update_notification => $farms->update_notification);

        return view('farms.edit', compact('farms', 'tags','farmTypes','selectedFarmType','farmOwner','selectedFarmOwner','update_notification','update_notification_selected'));
    }

		/*
		*	edit bins
		*/
		public function editBins(){

			$data = Input::all();

			$bin = Bins::findOrFail($data['bin_id']);

			$up_hist = $this->lastUpdate_numpigs($data['bin_id']);
			$bin->num_of_pigs = $up_hist[0]->num_of_pigs;

			$feed_name = $this->getFeedName($bin->feed_type);

			$bin_size_name = $this->getBinSizeName($bin->bin_size);

			$bin_size = BinSize::lists("name","size_id");

			$farm_id = $data['farm_id'];

			$amount = $this->amount();

			$feed_type = FeedTypes::lists("name","type_id");

			$bin_history = $this->recentFeedsHistory($data['bin_id']);
			$feed_type_history = FeedTypes::where('type_id','=',$bin_history[0]['feed_type'])->get()->toArray();
			$feed_type_history = array('type_id' => $feed_type_history[0]['type_id'], 'name' =>  $feed_type_history[0]['name']);

			Cache::forget('forecastingData_1');
			Cache::forget('forecastingData_2');
			Cache::forget('bins-'.$data['bin_id']);

			return view('farms.editbins',compact("bin","farm_id","feed_name","bin_size_name","amount","feed_type","bin_size","bin_history","feed_type_history"));

		}

		/*
		*	recent feeds history data
		*/
		public function recentFeedsHistory($bin_id){
			$bin_history = BinsHistory::where('bin_id','=',$bin_id)
										//->where('update_date','LIKE',date('Y-m-d').'%')
										->orderBy('update_date', 'DESC')
										->take(1)
										->get()
										->toArray();

			if(empty($bin_history)){
				$output = array(0=>array(
					'feed_type'		=>	51,
					'name'			=>	'None',
					'description'	=>	'None',
					'user_id'		=>	0,
					'num_of_pigs'	=>	0,
					'amount'		=>	0,
					'created_at'	=>	'0000-00-00 00:00:00',
					'updated_at'	=>	'0000-00-00 00:00:00'
				));
			} else {
				$output = $bin_history;
			}

			return $output;
		}

		/*
		*	bin size name
		*/
		private function getBinSizeName($id){
			$bin_size = BinSize::where('size_id','=',$id)->select('name')->first();
			$output = ($id != NULL ? $bin_size->name : "-");
			return $output;
		}

		/*
		*	feed name
		*/
		public function getFeedName($id){

			$feeds = FeedTypes::where('type_id','=',$id)->select('name')->first();
			$output = ($id != NULL ? $feeds->name : "-");
			return $output;
		}

		/*
		*	feed name
		*/
		public function getFeedDescription($id){

			$feeds = DB::table('feeds_feed_types')->where('type_id','=',$id)
												->select('description')
												->first();
			$output = $id != NULL ? $feeds->description : "-";
			return $output;
		}

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Farms  $farms
     * @return Response
     */
    public function update(Farms $farms, FarmsRequest $request)
    {
			Cache::forget('farms_lists');
			$farms->update($request->all());
			//$this->syncTags($farms, $request->input('tag_list'));
			Artisan::call("forecastingdatacache");

			return redirect('farms');
    }

		/*
		*	Update the Bins
		*/
		public function updateBin(BinSizeRequest $request){
			Cache::forget('farms_lists');
			$date_today = date("Y-m-d");
			$data = $request->all();

			$bins = Bins::find($data['bin_id']);
			$bins_history = BinsHistory::where('bin_id',$data['bin_id'])
																	->where('update_date','LIKE',$date_today.'%')
																	->orderBy('history_id','desc')->first();
			//$up_hist = $this->lastUpdate_numpigs($data['bin_id']);

			$feed_types_data = FeedTypes::where('type_id','=',$data['feed_type'])->get()->toArray();

			// update bin
			$bins->bin_number = $data['bin_number'];
			$bins->alias =  $data['alias'];
			//$bins->num_of_pigs =  $this->displayDefaultNumberOfPigs($data['num_of_pigs'], $up_hist[0]->num_of_pigs);
			$bins->num_of_pigs = $data['num_of_pigs'];
			//$bins->budgeted_amount = $feed_types_data[0]['budgeted_amount'];
			$bins->feed_type =  $data['feed_type'];
			$bins->bin_size =  $data['bin_size'];
			$bins->save();

			$home_controller = new HomeController;
			// for the update budgeted
			if($bins_history->feed_type != $data['feed_type']){
				// insert data to feeds_budgeted_amount_counter
				$budgeted_amount = $home_controller->budgetedAmountCounterUpdater($data['farm_id'],$data['bin_id'],$data['feed_type']);
			}else {
				// get the days counted for the auto update budgeted amount
				// feeds_feed_type_budgeted_amount_per_day
				// get the last date inserted on the feeds_budgeted_amount_counter and count it on today's date then get the day column for that budgeted amount
				// if the day column has 0 get the last day column where it has a value that is not equal to zero
				$budgeted_amount = $home_controller->daysCounterbudgetedAmount($data['farm_id'],$data['bin_id'],$data['feed_type'],date("Y-m-d H:i:s"));
			}
			unset($home_controller);

			// update the bin history
			if(!empty($bins_history)){
				if($date_today == date("Y-m-d")){
					$bins_history->budgeted_amount = $budgeted_amount; //$feed_types_data[0]['budgeted_amount'];
					$bins_history->update_date = date("Y-m-d H:i:s");
					$bins_history->created_at = date("Y-m-d H:i:s");
					$bins_history->user_id = Auth::id();
					//$bins_history->num_of_pigs = $data['num_of_pigs'];
					$bins_history->feed_type = $data['feed_type'];
					$bins_history->update_type = "Manual Update Edit Bin Admin";
					$bins_history->save();
				} else {
					//$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$feed_types_data[0]['budgeted_amount']);
					$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$budgeted_amount);
				}
			} else {
				//$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$feed_types_data[0]['budgeted_amount']);
				$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$budgeted_amount);
			}

			//call the cache builder
			$this->cacheBuilder();
			Artisan::call("forecastingdatacache");

			$url = 'farms/viewbins/'.$data['farm_id'];

			return redirect($url);

		}

		/*
		*	Update the Bins
		*/
		public function updateBinAPI($data){

			Cache::forget('farms_lists');
			$date_today = date("Y-m-d");

			$bins = Bins::find($data['bin_id']);
			$bins_history = BinsHistory::where('bin_id',$data['bin_id'])
																	->where('update_date','LIKE',$date_today.'%')
																	->orderBy('history_id','desc')->first();
			//$up_hist = $this->lastUpdate_numpigs($data['bin_id']);

			$feed_types_data = FeedTypes::where('type_id','=',$data['feed_type'])->get()->toArray();

			// update bin
			//$bins->bin_number = $data['bin_number'];
			$bins->alias =  $data['alias'];
			//$bins->num_of_pigs =  $this->displayDefaultNumberOfPigs($data['num_of_pigs'], $up_hist[0]->num_of_pigs);
			//$bins->num_of_pigs = $data['num_of_pigs'];
			//$bins->budgeted_amount = $feed_types_data[0]['budgeted_amount'];
			$bins->feed_type =  $data['feed_type'];
			$bins->bin_size =  $data['bin_size'];
			$bins->save();

			$home_controller = new HomeController;
			// for the update budgeted
			if($bins_history->feed_type != $data['feed_type']){
				// insert data to feeds_budgeted_amount_counter
				$budgeted_amount = $home_controller->budgetedAmountCounterUpdater($data['farm_id'],$data['bin_id'],$data['feed_type']);
			}else {
				// get the days counted for the auto update budgeted amount
				// feeds_feed_type_budgeted_amount_per_day
				// get the last date inserted on the feeds_budgeted_amount_counter and count it on today's date then get the day column for that budgeted amount
				// if the day column has 0 get the last day column where it has a value that is not equal to zero
				$budgeted_amount = $home_controller->daysCounterbudgetedAmount($data['farm_id'],$data['bin_id'],$data['feed_type'],date("Y-m-d H:i:s"));
			}
			unset($home_controller);

			// update the bin history
			if(!empty($bins_history)){
				if($date_today == date("Y-m-d")){
					$bins_history->budgeted_amount = $budgeted_amount; //$feed_types_data[0]['budgeted_amount'];
					$bins_history->update_date = date("Y-m-d H:i:s");
					$bins_history->created_at = date("Y-m-d H:i:s");
					$bins_history->user_id = $data['user_id'];
					//$bins_history->num_of_pigs = $data['num_of_pigs'];
					$bins_history->feed_type = $data['feed_type'];
					$bins_history->update_type = "Manual Update Edit Bin Admin";
					$bins_history->save();
				} else {
					//$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$feed_types_data[0]['budgeted_amount']);
					$this->insertHistoryPigs($data['farm_id'],$bins_history->num_of_pigs, $data['bin_id'],$data['feed_type'],$budgeted_amount);
				}
			} else {
				//$this->insertHistoryPigs($data['farm_id'],$data['num_of_pigs'], $data['bin_id'],$data['feed_type'],$feed_types_data[0]['budgeted_amount']);
				$this->insertHistoryPigs($data['farm_id'],$bins_history->num_of_pigs, $data['bin_id'],$data['feed_type'],$budgeted_amount);
			}

			//call the cache builder
			//$this->cacheBuilder();
			//Artisan::call("forecastingdatacache");

		}

		/*
		* forecasting cache builder
		*/
		private function cacheBuilder(){
			// create curl resource
	        $ch = curl_init();

	        // set url
	        curl_setopt($ch, CURLOPT_URL, url()."/cachebuilder");

	        //return the transfer as a string
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	        // $output contains the output string
	        $output = curl_exec($ch);

	        // close curl resource to free up system resources
	        curl_close($ch);
		}



		/** Private Method
		** @Int Value Default, @Int Value from History
		** Compare two
		** Return Highest Value
		**/
		private function displayDefaultNumberOfPigs($default, $history) {
			$a = $default;

			if($history != 0) {

				$a = $history;

			}

			return $a;

		}


		/** Static Function
		** Gets the Default Values of a certain Bin
		** int bin_id Primary key
		** return array Object 2-19-2016
		**/
		public function getBinDefaultInfo($bin_id) {

			$output = DB::table('feeds_bins')
						->select('bin_id','farm_id','num_of_pigs','amount', 'feed_type', 'bin_size')
						->where('bin_id','=',$bin_id)
						->get();

			return $output;

		}

		/** STATIC FUNCTION
		** Gets last values from Update History
		** bininfo array Object
		** return array Object 2-19-2016
		**/
		public function getLastHistory($bininfo) {

			$output = DB::table('feeds_bin_history')
						->select('num_of_pigs', 'amount', 'budgeted_amount', 'remaining_amount', 'sub_amount', 'variance', 'consumption')
						->where('bin_id','=',$bininfo[0]->bin_id)
						->orderBy('update_date', 'DESC')
						->take(1)
						->get();

			if(count($output) == 0) {

				$output[0] =  (object)array(

					'num_of_pigs' => $bininfo[0]->num_of_pigs,
					'amount'=> $bininfo[0]->amount,
					'budgeted_amount'=>$this->getBudgetedAmount($bininfo[0]->feed_type),
					'remaining_amount'=> 0,
					'sub_amount' => 0,
					'variance' => 0,
					'consumption' => 0

				);

			}

			return $output;

		}

		public function getBudgetedAmount($feedtype) {

			$output = DB::table('feeds_feed_types')
						->select('budgeted_amount')
						->where('type_id','=',$feedtype)
						->get();

			return $output[0]->budgeted_amount;

		}


		/** Public Method
		** @Int Value Number of Pigs @Int Value Bin ID
		** Insert Data on History
		**/
		public function insertHistoryPigs($farm_id, $numofpigs, $binid, $feed_type,$budgeted_amount) {

			if(is_numeric($numofpigs)) {


				$bininfo = $this->getBinDefaultInfo($binid);
				$lastupdate  = $this->getLastHistory($bininfo);

				DB::table('feeds_bin_history')
				->where('bin_id', '=', $binid)
				->whereBetween('update_date', array(date("Y-m-d") . " 00:00:00", date("Y-m-d") . " 23:59:59"))
				->delete();

				DB::table('feeds_bin_history')->insert(
					array(
						'update_date' => date("Y-m-d H:i:s"),
						'bin_id' => $binid,
						'farm_id' => $farm_id,
						'num_of_pigs' => $numofpigs,
						'user_id' => Auth::id(),
						'amount' => $lastupdate[0]->amount,
						'update_type' => 'Manual Update Edit Bin Admin',
						'created_at' => date("Y-m-d H:i:s"),
						'budgeted_amount' => $budgeted_amount,
						'remaining_amount' => 0,//$lastupdate[0]->remaining_amount,
						'sub_amount' => 0,//$lastupdate[0]->sub_amount,
						'variance' => 0,//$lastupdate[0]->variance,
						'consumption' => 0,//$lastupdate[0]->consumption,
						'admin' => 1,
						'feed_type'	=>	$feed_type
					)
				);

				$msg = "Bin was successfully Updated!";

			} else {

				$msg = "Only accepts number value";

			}

			return array(

				'msg' => $msg

			);

		}

		/**
	  ** Get Last Update from History
	  **
	  ** @param  Request  $bin_id (int) Primary Key
	  ** @return object
	  **/
		private function lastUpdate_numpigs($bin_id){

			$output = DB::table('feeds_bin_history')
						->select('num_of_pigs','amount')
						->where('bin_id','=',$bin_id)
						->orderBy('update_date','desc')
						->take(1)->get();

			if($output == NULL) {

				$output[0] = (object) array(
								'num_of_pigs' => '0',
								'amount' => '0'
							);

			}


			return $output;

		}

		/**
		 * Remove the specified resource from storage.
		 *
		 * @param  int  $id
		 * @return Response
		 */
		public function destroyLeftOvers($farmid)
		{
			Cache::forget('farms_lists');

			Farms::where('id',$farmid)->delete();
			// Delete related bins for the deleted farm
			DB::table('feeds_bins')->where('farm_id','=',$farmid)->delete();
			// Delete related farm users entries
			DB::table('feeds_farm_users')->where('farm_id',$farmid)->delete();

			// delete related  bin_accepted_load
			DB::table('feeds_bins_accepted_load')->where('farm_id',$farmid)->delete();
			// delete data on history table
			DB::table('feeds_bin_history')->where('farm_id',$farmid)->delete();

			// delete deliveries
			DB::table('feeds_deliveries')->where('farm_id',$farmid)->delete();
			// delete animal groups and transfers
			$this->removeRelatedData($farmid);
			// delete scheduled deliveries
			$this->removeFarmSchedule($farmid);

			//delete pending deliveries
			DB::table('feeds_deliveries_pending')->where('farm_id',$farmid)->delete();

			return "left overs deleted";

		}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($farmid)
    {
		Cache::forget('farms_lists');

    Farms::findOrFail($farmid)->delete();
		// Delete related bins for the deleted farm
		DB::table('feeds_bins')->where('farm_id','=',$farmid)->delete();
		// Delete related farm users entries
		DB::table('feeds_farm_users')->where('farm_id',$farmid)->delete();

		// delete related  bin_accepted_load
		DB::table('feeds_bins_accepted_load')->where('farm_id',$farmid)->delete();
		// delete data on history table
		DB::table('feeds_bin_history')->where('farm_id',$farmid)->delete();

		// delete deliveries
		DB::table('feeds_deliveries')->where('farm_id',$farmid)->delete();
		// delete animal groups and transfers
		$this->removeRelatedData($farmid);
		// delete scheduled deliveries
		$this->removeFarmSchedule($farmid);

		//delete pending deliveries
		DB::table('feeds_deliveries_pending')->where('farm_id',$farmid)->delete();

		flash()->overlay("The farm has been successfully deleted!", "H&H Farms");

		Cache::forget('farm_holder-'.$farmid);
		$home_controller = new HomeController;
		$home_controller->farmHolderBinClearCache($farmid);
		$home_controller->forecastingDataCache();
		unset($home_controller);

		return redirect('farms');

    }


		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function removeFarmData($farmid)
    {
			Cache::forget('farms_lists');

	    Farms::findOrFail($farmid)->delete();
			// Delete related bins for the deleted farm
			DB::table('feeds_bins')->where('farm_id','=',$farmid)->delete();
			// Delete related farm users entries
			DB::table('feeds_farm_users')->where('farm_id',$farmid)->delete();

			// delete related  bin_accepted_load
			DB::table('feeds_bins_accepted_load')->where('farm_id',$farmid)->delete();
			// delete data on history table
			DB::table('feeds_bin_history')->where('farm_id',$farmid)->delete();

			// delete deliveries
			DB::table('feeds_deliveries')->where('farm_id',$farmid)->delete();
			// delete animal groups and transfers
			$this->removeRelatedData($farmid);
			// delete scheduled deliveries
			$this->removeFarmSchedule($farmid);

			//delete pending deliveries
			DB::table('feeds_deliveries_pending')->where('farm_id',$farmid)->delete();

			Cache::forget('farm_holder-'.$farmid);
			$home_controller = new HomeController;
			$home_controller->farmHolderBinClearCache($farmid);
			$home_controller->forecastingDataCache();
			unset($home_controller);
		}


		/**
	   * remove bins
	   *
	   * @param  int  $id
	   * @return Response
	   */
		private function removeRelatedData($farm_id)
		{
			$farrowing_group = DB::table('feeds_movement_farrowing_group')->where('farm_id',$farm_id)->get();
			$nursery_group = DB::table('feeds_movement_nursery_group')->where('farm_id',$farm_id)->get();
			$finisher_group = DB::table('feeds_movement_finisher_group')->where('farm_id',$farm_id)->get();

			$this->removeGroups($farrowing_group,'feeds_movement_farrowing_bins');
			$this->removeGroups($nursery_group,'feeds_movement_nursery_bins');
			$this->removeGroups($finisher_group,'feeds_movement_finisher_bins');

			DB::table('feeds_movement_farrowing_group')->where('farm_id',$farm_id)->delete();
			DB::table('feeds_movement_nursery_group')->where('farm_id',$farm_id)->delete();
			DB::table('feeds_movement_finisher_group')->where('farm_id',$farm_id)->delete();
		}

		/*
		*	removeGroups
		* remove the groups from the animal group
		*/
		private function removeGroups($group,$bin_table)
		{
			if($group != NULL){
				foreach($group as $k => $v){
					$this->removeGroupBins($v->unique_id,$bin_table);
					$this->removeTransfer($v->group_id);
				}
			}
		}

		/*
		*	removeGroupBins
		*	remove the group bins for the animal groups
		*/
		private function removeGroupBins($unique_id,$bin_table)
		{
			DB::table($bin_table)->where('unique_id',$unique_id)->delete();
		}

		private function removeTransfer($group_id)
		{
			$transfer_from = DB::table('feeds_movement_transfer')->select('transfer_id')->where('group_from',$group_id)->get();
			$transfer_to = DB::table('feeds_movement_transfer')->select('transfer_id')->where('group_to',$group_id)->get();

			if($transfer_from != NULL){
				foreach($transfer_from as $k => $v){
					$this->removeTransferBins($v->transfer_id);
				}
			}

			if($transfer_to != NULL){
				foreach($transfer_to as $k => $v){
					$this->removeTransferBins($v->transfer_id);
				}
			}

			DB::table('feeds_movement_transfer')->where('group_from',$group_id)->delete();
			DB::table('feeds_movement_transfer')->where('group_to',$group_id)->delete();
		}

		/*
		*	removeTransferBins
		*	remove the trnasfer bins from the animal groups
		*/
		private function removeTransferBins($transfer_id)
		{
			DB::table('feeds_movement_transfer')->where('transfer_id',$transfer_id)->delete();
		}

		/*
		*	removeFarmSchedule
		*	remove the farm scchedule
		*/
		private function removeFarmSchedule($farm_id)
		{
			$schedule_deliveries = DB::table('feeds_farm_schedule')->where('farm_id',$farm_id)->get();
			if($schedule_deliveries != NULL){
				foreach($schedule_deliveries as $k => $v){
					$this->removeSchedTool($v->unique_id);
				}
			}
			DB::table('feeds_farm_schedule')->where('farm_id',$farm_id)->delete();
		}

		/*
		*	remove SchedTool
		* remove the sched tool data
		*/
		private function removeSchedTool($unique_id)
		{
			DB::table('feeds_sched_tool')->where('farm_sched_unique_id',$unique_id)->delete();
		}

		/**
     * destroy bins
     *
     * @param  int  $id
     * @return Response
     */
    public function destroyBin()
    {
     	$data = Request::all();

	    Bins::findOrFail($data['bin_id'])->delete();

			DB::table('feeds_bins_accepted_load')->where('bin_id',$data['bin_id'])->delete();

			// delete data on history table
			DB::table('feeds_bin_history')->where('bin_id',$data['bin_id'])->delete();

			flash()->overlay("The bin has been successfully deleted!", "H&H Farms");

			$url = 'farms/viewbins/'.$data['farm_id'];

			return redirect($url);

    }


		/**
		* Sync up the list of tags in the database.
		*
		* @param Farms	$farms
		* @param array	$tags
		*/
		private function syncTags(Farms $farms, array $tags)
		{
			$farms->tags()->sync($tags);
		}

		/**
		* Save a new farm.
		*
		* @param FarmsRequest	$request
		*/
		private function createFarms(FarmsRequest $request)
		{
			$farms = Auth::user()->farms()->create($request->all());

			//$this->syncTags($farms, $request->input('tag_list'));

			return $farms;
		}

		/*
		*	Add Bins begin
		*	The beginning functionality of the add bins
		*/
		public function addBinsBegin($farmid)
		{
			$farm =  Farms::findOrFail($farmid);

			return view("farms.addbinsbegin", compact("farm"));
		}

		/*
		*	Add Bins create
		*/
		public function addBinsCreate()
		{
			$bins_number_orig = Request::get('bins_number');

			$farm_id = Request::get('farm_id');

			$selected_farm_bin = Bins::where('farm_id',$farm_id)->count();
			$selected_farm_bins = $selected_farm_bin == 0 ? 1 : $selected_farm_bin;

			$bins_number = $selected_farm_bins + $bins_number_orig;


			$feed_types = FeedTypes::lists("name","type_id");
			$bin_sizes = BinSize::lists("name","size_id");
			$hex_color = $this->hexColorGenerator();

			$amount = $this->amount();

			if($selected_farm_bin == 0){
				return view("farms.addbinscreate1", compact("bins_number_orig","farm_id","feed_types","amount","bin_sizes"));
			} else {
				return view("farms.addbinscreate2", compact("bins_number","farm_id","feed_types","amount","bin_sizes","selected_farm_bins"));
			}
		}

		/*
		*	amount()
		* the amount for the consumptions of feeds
		*/
		private function amount()
		{
			$data = array();
			for($i=1;$i<=50;$i+=0.25){
				$amount = strval($i) . "Tons";
				$data[$amount] = $i . " Tons";
			}
			return array($data);
		}

		/*
		*	consumptionConverter
		* convert the consumption of feeds
		*/
		private function consumptionConverter($string)
		{
			return trim($string,"Tons");
		}

		/*
		*	Store Bins One
		*/
		public function storeBinsOne()
		{
			$bins_total = Request::get('bins_number');
			$farm_id = Request::get('farm_id');
			$selected_farm_bins = Request::get('selected_farm_bins')+1;
			$user_id = Auth::user()->id;
			$data = array();
			$unique_id = $this->generator();

			for($i = 1; $i <= $bins_total; $i++){
				$number_of_pigs = Request::get("number_of_pigs_".$i);
				//$amount = $this->consumptionConverter(Request::get("amount_".$i));
				$hex_color = Request::get("bins_color_".$i);
				$bin_name = Request::get("bin_name_".$i);
				$bin_size = Request::get("bin_size_".$i);
				$feed_type = Request::get("feed_type_".$i);
				$data[] = array(
								'farm_id'					=>	$farm_id,
								'bin_number'			=>	$i,
								'alias'						=>	$bin_name,
								'num_of_pigs'			=>	$number_of_pigs,
								'hex_color'				=>	$hex_color,
								'bin_size'				=>	$bin_size,
								'created_at'			=>	date('Y-m-d H:i:s'),
								'updated_at'			=>	date('Y-m-d H:i:s'),
								'user_id'					=>	$user_id,
								'unique_id'				=>	$unique_id
								);
			}
			Bins::insert($data);

			$bins_data = Bins::where('unique_id',$unique_id)->get()->toArray();

			$home_controller = new HomeController;
			foreach($bins_data as $k => $v){
				// add the counter for budgeted
				$budgeted_amount = $home_controller->budgetedAmountCounterUpdater($v['farm_id'],$v['bin_id'],$v['feed_type']);

				$binsData[] = array(
					'update_date'	=>	date('Y-m-d H:i:s'),
					'update_type'	=>	'Manual Update Create Bin Admin',
					'budgeted_amount' => $budgeted_amount,
					'bin_id'		=>	$v['bin_id'],
					'farm_id'		=>	$v['farm_id'],
					'feed_type'		=>	$v['feed_type'],
					'num_of_pigs'	=>	$v['num_of_pigs'],
					'user_id'		=>	Auth::id()
				);
			}
			unset($home_controller);

			BinsHistory::insert($binsData);
			Artisan::call("forecastingdatacache");

			return redirect('farms/viewbins/'.$farm_id);
		}

		/*
		*	Store Bins Two
		*/
		public function storeBinsTwo()
		{
			$bins_total = Request::get('bins_number');
			$farm_id = Request::get('farm_id');
			$selected_farm_bins = Request::get('selected_farm_bins')+1;
			$user_id = Auth::user()->id;
			$data = array();

			$unique_id = $this->generator();

			for($i = $selected_farm_bins; $i <= $bins_total; $i++){
				$number_of_pigs = Request::get("number_of_pigs_".$i);
				//$amount = $this->consumptionConverter(Request::get("amount_".$i));
				$hex_color = Request::get("bins_color_".$i);
				$bin_name = Request::get("bin_name_".$i);
				$bin_size = Request::get("bin_size_".($i-1));
				$feed_type = Request::get("feed_type_".$i);
				$data[] = array(
								'farm_id'				=>	$farm_id,
								'bin_number'			=>	$i,
								'alias'					=>	$bin_name,
								'num_of_pigs'			=>	$number_of_pigs,
								'hex_color'				=>	$hex_color,
								'bin_size'				=>	$bin_size,
								'created_at'			=>	date('Y-m-d H:i:s'),
								'updated_at'			=>	date('Y-m-d H:i:s'),
								'user_id'				=>	$user_id,
								'unique_id'				=>	$unique_id
								);
			}
			Bins::insert($data);

			$bins_data = Bins::where('unique_id',$unique_id)->get()->toArray();

			$home_controller = new HomeController;
			foreach($bins_data as $k => $v){

				// add the counter for budgeted
				$budgeted_amount = $home_controller->budgetedAmountCounterUpdater($v['farm_id'],$v['bin_id'],$v['feed_type']);

				$binsData[] = array(
					'update_date'		=>	date('Y-m-d H:i:s'),
					'update_type'	=>	'Manual Update Create Bin Admin',
					'bin_id'			=>	$v['bin_id'],
					'farm_id'			=>	$v['farm_id'],
					'feed_type'			=>	$v['feed_type'],
					'num_of_pigs'		=>	$v['num_of_pigs'],
					'user_id'			=>	Auth::id(),
					'budgeted_amount'	=>	$budgeted_amount,//$this->budgtedAmount($v['feed_type'])
				);
			}
			unset($home_controller);

			BinsHistory::insert($binsData);

			Artisan::call("forecastingdatacache");

			return redirect('farms/viewbins/'.$farm_id);
		}

		/*
		*	budgeted amount
		*/
		private function budgtedAmount($type_id)
		{
			$budgeted_amount = 	FeedTypes::where('type_id',$type_id)->get()->toArray();

			return !empty($budgeted_amount[0]['budgeted_amount']) ? $budgeted_amount[0]['budgeted_amount'] : 0;
		}

		/*
		*	Unique ID generator
		*/
		public function generator(){

			$unique = uniqid(rand());
			$dateToday = date('ymdhms');

			$output = $unique.$dateToday;

			return $output;

		}

		/*
		*	Bins View
		*/
		public function viewBins($farmid)
		{
			$farm = Farms::findOrFail($farmid);
			$bins = DB::table('feeds_bins')
	                     ->select('feeds_bins.*','feeds_bin_sizes.name AS bin_size_name', 'feeds_feed_types.name AS feed_type_name')
						 ->leftJoin('feeds_bin_sizes','feeds_bin_sizes.size_id', '=', 'feeds_bins.bin_size')
						 ->leftJoin('feeds_feed_types','feeds_feed_types.type_id', '=', 'feeds_bins.feed_type')
	                     ->where('farm_id', '=', $farmid)
						 ->orderBy('bin_number','ASC')
	                     ->get();

			$x = 0;
			while($x < count($bins)) {

				$up_hist = $this->lastUpdate_numpigs($bins[$x]->bin_id);
				//$bins[$x]->num_of_pigs = $this->displayDefaultNumberOfPigs($bins[$x]->num_of_pigs, $up_hist[0]->num_of_pigs);
				$bins[$x]->num_of_pigs = $this->animalGroupBinTotalPigs($bins[$x]->bin_id,$farmid);
				$x++;

			}

			$ctrl = new FarmsController;

			return view("farms.viewbins",compact("bins","farm","ctrl"));
		}

		/*
		*	Bins View
		*/
		public function viewBinsAPI($farmid)
		{
			$farm = Farms::findOrFail($farmid);
			$bins = DB::table('feeds_bins')
	                     ->select('feeds_bins.*','feeds_bin_sizes.name AS bin_size_name', 'feeds_feed_types.name AS feed_type_name')
						 ->leftJoin('feeds_bin_sizes','feeds_bin_sizes.size_id', '=', 'feeds_bins.bin_size')
						 ->leftJoin('feeds_feed_types','feeds_feed_types.type_id', '=', 'feeds_bins.feed_type')
	                     ->where('farm_id', '=', $farmid)
						 ->orderBy('bin_number','ASC')
	                     ->get();

			$x = 0;
			while($x < count($bins)) {

				$up_hist = $this->lastUpdate_numpigs($bins[$x]->bin_id);
				//$bins[$x]->num_of_pigs = $this->displayDefaultNumberOfPigs($bins[$x]->num_of_pigs, $up_hist[0]->num_of_pigs);
				$bins[$x]->feed_type = $this->recentFeedsHistory($bins[$x]->bin_id)[0]['feed_type'];
				$bins[$x]->feed_type_name = $this->getFeedDescription($this->recentFeedsHistory($bins[$x]->bin_id)[0]['feed_type']);
				$bins[$x]->num_of_pigs = $this->animalGroupBinTotalPigs($bins[$x]->bin_id,$farmid);
				$bins[$x]->amount = $this->recentFeedsHistory($bins[$x]->bin_id)[0]['amount'] . " Ton/s";
				$x++;

			}

			return $bins;
		}

		/*
		*	Random hex color generator
		*/
		public function hexColorGenerator()
		{
			return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
		}

		/*
		*	Farms Profile
		*/
		public function profile(){
			/**/
			$farms = Farms::orderBy('name')->get();

			$data = $this->profileDataBuilder($farms);

			Storage::put('farms_profile_data.txt',json_encode($data));
			$farms_profile = Storage::get('farms_profile_data.txt');

			return  view("farms.profile.index",compact("farms_profile"));
		}

		/*
		*	Farms Profile
		*/
		public function apiPFList(){
			/**/
			$farms = Farms::orderBy('name')->get();

			$farmsProfile = $this->profileDataBuilder($farms);

			Storage::put('farms_profile_data.txt',json_encode($farmsProfile));
			$farms_profile = json_decode(Storage::get('farms_profile_data.txt'));

			return  compact("farms_profile");
		}

		/*
		*	Farms Profile data builder
		*/
		private function profileDataBuilder($farms)
		{
			$data = array();
			$counter = count($farms) - 1;
			for($i=0; $i<=$counter; $i++){

				$data[] = array(
					'id'						=>	$farms[$i]->id,
					'name'					=>	$farms[$i]->name,
					'delivery_time'	=>	$farms[$i]->delivery_time,
					'bins'					=>	$this->profileDataBins($farms[$i]->id),
					'users'					=>	$this->profileDataFarmers($farms[$i]->id)
				);

			}

			return $data;
		}

		/*
		*	Farms Profile data for farmers
		*/
		private function profileDataFarmers($farm_id)
		{
			$farmers = DB::table('feeds_farm_users')->where('farm_id',$farm_id)->select('user_id')->get();
			$data = array();
			$counter = count($farmers) - 1;

			for($i=0; $i<=$counter; $i++){
				$data[] = $this->profileDataFarmerAccess($farmers[$i]->user_id);
			}

			return $data;
		}

		/*
		*	Farms Profile data for farmers access
		*/
		private function profileDataFarmerAccess($user_id)
		{
			$farmers = DB::table('feeds_user_accounts')->where('id',$user_id)->first();

			return array(
				 'user_id'	=>	$user_id,
				 'username'	=>	$farmers->username,
				 'no_hash'	=>	$farmers->no_hash
			);

		}


		/*
		*	Farms Profile data for history bins
		*/
		private function profileDataBins($farm_id)
		{

			$bins = Bins::select('bin_id','bin_number','alias','bin_size','farm_id')->where('farm_id',$farm_id)->get();
			$data = array();
			$counter = count($bins) - 1;

			for($i=0; $i<=$counter; $i++){
				$bin_history = $this->profileDataHistoryBins($bins[$i]->bin_id,$bins[$i]->farm_id);
				$total_number_of_pigs = $this->animalGroupBinTotalPigs($bins[$i]->bin_id,$bins[$i]->farm_id);
				$data[] = array(
					'bin_id'					=>	$bins[$i]->bin_id,
					'bin_number'			=>	$bins[$i]->bin_number,
					'alias'						=>	$bins[$i]->alias,
					'bin_size'				=>	$this->profileDataBinsSize($bins[$i]->bin_size),
					'number_of_pigs'	=>	$total_number_of_pigs,//$bin_history->num_of_pigs,
					'feed_type'				=>	$this->profileDataBinsFeedType($bin_history->feed_type)
				);
			}

			return $data;
		}

		/*
		*	Farms Profile data for history bins
		*/
		private function profileDataHistoryBins($bin_id,$farm_id)
		{
			$bin_history = BinsHistory::where('bin_id',$bin_id)->where('farm_id',$farm_id)->select('num_of_pigs','feed_type')->orderBy('history_id','desc')->first();
			return $bin_history;
		}

		/*
		*	Farms Profile data for bins sizes
		*/
		private function profileDataBinsSize($size_id)
		{
			$bin_size = BinSize::where('size_id',$size_id)->select('name')->first();
			return $bin_size->name;
		}

		/*
		*	Farms Profile data for bins sizes
		*/
		private function profileDataBinsFeedType($type_id)
		{
			$feed_type = FeedTypes::where('type_id',$type_id)->select('description')->first();
			return $feed_type != NULL ? $feed_type->description : "";
		}

		/*
		*	animalGroup()
		*/
		public function animalGroupBinTotalPigs($bin_id,$farm_id)
		{
			// check the farm type
			$type = $this->farmTypesForGroups($farm_id);

			if($type == 'farrowing'){

				$output = DB::table('feeds_movement_groups_bins')->where('bin_id',$bin_id)->sum('number_of_pigs');

			} elseif ($type == 'nursery') {

				$output = DB::table('feeds_movement_groups_bins')->where('bin_id',$bin_id)->sum('number_of_pigs');

			} elseif ($type == 'finisher') {

				$output = DB::table('feeds_movement_groups_bins')->where('bin_id',$bin_id)->sum('number_of_pigs');

			} else {

				$output = 0;

			}

			return $output != NULL ? $output : 0;
		}

		/*
		*	farm types()
		*/
		private function farmTypesForGroups($farm_id)
		{
			$type = Farms::where('id',$farm_id)->select('farm_type')->first();

			return $type != NULL ? $type->farm_type : NULL;
		}

		/*
		*	Bins
		*/
		public function getBins($farm_id)
	  {
			$bins = DB::table('feeds_bins')
	                   ->select('feeds_bins.*','feeds_bin_sizes.name AS bin_size_name', 'feeds_feed_types.name AS feed_type_name')
					 ->leftJoin('feeds_bin_sizes','feeds_bin_sizes.size_id', '=', 'feeds_bins.bin_size')
					 ->leftJoin('feeds_feed_types','feeds_feed_types.type_id', '=', 'feeds_bins.feed_type')
	                   ->where('farm_id', '=', $farm_id)
					 ->orderBy('bin_number')
	                   ->get();

	      return $bins;
	  }

		/*
		*	getBinsNumber()
		*/
		public function getBinNumber($bin_id)
		{
			$bins = BinsHistory::select('num_of_pigs')
					->where('bin_id',$bin_id)
					->orderBy('update_date','desc')
					->take(1)->get()->toArray();

			return !empty($bins[0]['num_of_pigs']) ? $bins[0]['num_of_pigs'] : 0;
		}

		/*
		*	Feed Type ID
		*/
		public function getFeedType($bin_id)
		{
			$bins = BinsHistory::select('feed_type')
					->where('bin_id',$bin_id)
					->orderBy('update_date','desc')
					->take(1)->get()->toArray();

			$feed_type = !empty($bins[0]['feed_type']) ? $this->getFeedTypeName($bins[0]['feed_type']) : "None";

			return $feed_type;
		}

		/*
		*	Feed Type Name
		*/
		private function getFeedTypeName($feed_type)
		{
			$feed_type = FeedTypes::select('name')
					->where('type_id',$feed_type)
					->take(1)->get()->toArray();

			return $feed_type != NULL ? $feed_type[0]['name'] : "";
		}

		/*
		*	getFarmUser
		* get the farm users
		* method for farms profile page
		*/
		public function getFarmUser($farm_id)
		{
			$users = DB::table('feeds_farm_users')
						->select('feeds_farm_users.farm_id','feeds_farm_users.user_id',
								'feeds_user_accounts.id',
								'feeds_user_accounts.username',
								'feeds_user_accounts.no_hash',
								'feeds_user_accounts.gcm_regid')
						->leftJoin('feeds_user_accounts',
								'feeds_user_accounts.id','=','feeds_farm_users.user_id')
						->where('feeds_farm_users.farm_id','=',$farm_id)
						->get();
			return $users;
		}

		/*
		* addFarmerUser
		* add the farm user
		* method for the farms profile page
		*/
		public function addFarmUser($id)
		{
			$farmers = DB::table('feeds_user_accounts')
						->select('feeds_user_accounts.*')
						->whereRaw("id NOT IN (SELECT GROUP_CONCAT(user_id) FROM feeds_farm_users WHERE farm_id = {$id} GROUP BY user_id ORDER BY user_id)")
						->where('type_id','=',1)
						->get();
			return view("farms.profile.adduser",compact("farmers","id"));
		}

		/*
		* availableFarmersAPI
		* get the available farmers
		* method for farms profile page
		*/
		public function availableFarmersAPI($farm_id) {
			$farmers = DB::table('feeds_user_accounts')
						->select('feeds_user_accounts.*')
						->whereRaw("id NOT IN (SELECT GROUP_CONCAT(user_id) FROM feeds_farm_users WHERE farm_id = {$farm_id} GROUP BY user_id ORDER BY user_id)")
						->where('type_id','=',1)
						->get();
			return 	$farmers;
		}

		/*
		* removeFarmer
		* remove the farmers from the farm profile page
		*/
		public function removeFarmer()
		{
			//dd(Input::all());
			$farm_id = Input::get('farm_id');
			$user_id = Input::get('user_id');

			$delete = Farmer::where('farm_id',$farm_id)->where('user_id',$user_id)->delete();

			return $delete;
		}

		/*
		*	removeFarmerAPi
		* remove the farmers form the farms profile page
		*/
		public function removeFarmerAPI($farm_id, $user_id)
		{
			$delete = Farmer::where('farm_id',$farm_id)->where('user_id',$user_id)->delete();

			return $delete;
		}

		/*
		* saveFarmer
		* Save the farmder data to add it of the farms profile page
		*/
		public function saveFarmer(){
			$farm_id = Input::get('farm_id');
			$farmer_id = Input::get('farmer_id');

			$data = array(
				'farm_id' => $farm_id,
				'user_id' => $farmer_id
			);

			$farmer = Farmer::where('farm_id',$farm_id)->where('user_id',$farmer_id)->get()->toArray();

			if(empty($farmer)){
				Farmer::insert($data);
			}

			flash()->overlay("The farmer has been added successfully!", "H&H Farms");

			return redirect('farmsprofile');
		}

		/*
		* saveFarmerAPI
		* Save the farmder data to add it of the farms profile page
		*/
		public function saveFarmerAPI($farm_id,$farmer_id){

			$output = "";

			$data = array(
				'farm_id' => $farm_id,
				'user_id' => $farmer_id
			);

			$farmer = Farmer::where('farm_id',$farm_id)->where('user_id',$farmer_id)->get()->toArray();

			if(empty($farmer)){
				Farmer::insert($data);
				$output = "Successfully added farmer";
			} else {
				$output = "No farmer get from that farmer id";
			}

			return $output;
		}


		/*
		*	Turn off farm
		*/
		public function turnOffFarm(){

			$reactivation_date = date('Y-m-d',strtotime(Input::get('reactivation_date')));
			$farm_id = Input::get("farm_id");

			$farm = Farms::find($farm_id);
			$farm->status = 0; //deactivate
			$farm->reactivation_date = $reactivation_date; // reactivation date
			$farm->save();

		}

		/*
		*	Trun on farms
		*/
		public function turnOnFarm(){

			$farm_id = Input::get('farm_id');

			$farm = Farms::find($farm_id);
			$farm->status = 1; //deactivate
			$farm->reactivation_date = NULL; // reactivation date
			$farm->save();

		}

		/**
		 * Display a listing of the resource for the API.
		 *
		 * @return Response
		 */
		public function listFarmAPI()
		{
				$farms = DB::table('feeds_farms')
							->leftJoin('feeds_bins','feeds_farms.id', '=', 'feeds_bins.farm_id')
							->select('feeds_farms.*', (DB::raw('count(feeds_bins.bin_id) AS totalBins')))
							->orderBy('feeds_farms.name')
							->groupBy('feeds_farms.id')
							->get();

				return $farms;
		}

		/**
		 * Save the farm.
		 *
		 * @return Response
		 */
		public function saveFarmAPI($data)
		{

			if(Farms::insert($data)){
				return "saved";
			}

			return "error";

		}

		/**
		 * Update the farm.
		 *
		 * @return Response
		 */
		public function updateFarmAPI($farm_id,$data)
		{

			if(Farms::where('id',$farm_id)->update($data)){
				return "updated";
			}

			return "error";

		}

		/**
		 * Delete the farm.
		 *
		 * @return Response
		 */
		public function deleteFarmAPI($farm_id)
		{

			$this->removeFarmData($farm_id);

			return "deleted";

		}

		/**
		 * Turn on farm.
		 *
		 * @return Response
		 */
		public function turnOnFarmAPI($farm_id)
		{

			$farm = Farms::find($farm_id);
			$farm->status = 1; //activate
			$farm->reactivation_date = NULL; // reactivation date
			if($farm->save()){
				return "turn on";
			}

			return "error";

		}

		/**
		 * Turn on farm.
		 *
		 * @return Response
		 */
		public function turnOffFarmAPI($reactivation_date,$farm_id)
		{

			$reactivation_date = date('Y-m-d',strtotime($reactivation_date));

			$farm = Farms::find($farm_id);
			$farm->status = 0; //deactivate
			$farm->reactivation_date = $reactivation_date; // reactivation date
			if($farm->save()){
				return "turn off";
			}

			return "error";

		}

		/**
		 * List the farm bin.
		 *
		 * @return Response
		 */
		public function listBinFarmAPI($farm_id)
		{
				$farm = DB::table('feeds_farms')->where('id', $farm_id)->exists();

				if($farm == 0){
					return NULL;
				}

				return $this->viewBinsAPI($farm_id);
		}

		/**
		 * Save the farm bin.
		 *
		 * @return Response
		 */
		public function saveBinFarmAPI($bin)
		{

			 $data = array(
							'farm_id'					=>	$bin['farm_id'],
							'bin_number'			=>	$bin['bin_number'],
							'alias'						=>	$bin['alias'],
							'num_of_pigs'			=>	0,
							'hex_color'				=>	'#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
							'bin_size'				=>	$bin['bin_size'],
							'created_at'			=>	date('Y-m-d H:i:s'),
							'updated_at'			=>	date('Y-m-d H:i:s'),
							'user_id'					=>	$bin['user_id'],
							'unique_id'				=>	$this->generator()
							);

				Bins::insert($data);

				$bins_data = Bins::where('unique_id',$data['unique_id'])->first()->toArray();

				$home_controller = new HomeController;

				// add the counter for budgeted
				$budgeted_amount = $home_controller->budgetedAmountCounterUpdater($bins_data['farm_id'],$bins_data['bin_id'],$bins_data['feed_type']);

				$binsData = array(
					'update_date'			=>	date('Y-m-d H:i:s'),
					'update_type'			=>	'Manual Update Create Bin Admin',
					'budgeted_amount' => 	$budgeted_amount,
					'bin_id'					=>	$bins_data['bin_id'],
					'farm_id'					=>	$bins_data['farm_id'],
					'feed_type'				=>	$bins_data['feed_type'],
					'num_of_pigs'			=>	$bins_data['num_of_pigs'],
					'user_id'					=>	$bin['user_id']
				);

				unset($home_controller);

				BinsHistory::insert($binsData);
				Artisan::call("forecastingdatacache");

		}

		/**
		 * update the farm bin.
		 *
		 * @return Response
		 */
		public function updateBinFarmAPI($bin)
		{

				$data = array(
							 'bin_id'						=>	$bin['bin_id'],
							 'farm_id'					=>	$bin['farm_id'],
							 'feed_type'				=>	$bin['feed_type'],
							 'alias'						=>	$bin['alias'],
							 'bin_size'					=>	$bin['bin_size'],
							 'user_id'					=>	$bin['user_id']
							 );

				$this->updateBinAPI($data);

				return $data;

		}

		/**
		 * Delete the farm.
		 *
		 * @return Response
		 */
		public function deleteBinFarmAPI($bin_id)
		{

			Bins::findOrFail($bin_id)->delete();
			DB::table('feeds_bins_accepted_load')->where('bin_id',$bin_id)->delete();
			// delete data on history table
			DB::table('feeds_bin_history')->where('bin_id',$bin_id)->delete();

		}

}
