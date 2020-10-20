<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Farms;
use App\Bins;
use App\BinsHistory;
use App\Medication;
use App\FeedTypes;
use App\MobileBinsAcceptedLoad;
use Cache;
use Carbon\Carbon;

class ConsumptionAutoUpdater extends Command
{
		// google play private key
		private $google_api_key = "AIzaSyBenv1FNusdELHbFk9gfedn2qlnsRbPDwI";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the consumptions of the software';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->forecastingDataCacheBuilder();
    }

	/*
	*	Cache forecasting data builder
	*/
	public function forecastingDataCacheBuilder(){

		/*Cache::forget('forecastingData');

		$farms = Farms::where('status',1)->get()->toArray();
		$forecastingData = array();
		$farms_count = count($farms)-1;
		for($i=0; $i<=$farms_count; $i++){
			$forecastingData[] = array(
				'farm_id'	=>	$farms[$i]['id'],
				'name'		=>	$farms[$i]['name'],
				'address'	=>	$farms[$i]['address'],
				'low_bins'	=>	$this->lowBins($this->binsDataFirstLoad($farms[$i]['id'])),
				'bins'		=> 	$this->binsDataFirstLoad($farms[$i]['id'])
			);
		}

		usort($forecastingData, function($a,$b){
			if($a['bins'][0]['days_to_empty'] == $b['bins'][0]['days_to_empty']) return 0;
			return ($a['bins'][0]['days_to_empty'] < $b['bins'][0]['days_to_empty'])?-1:1;
		});

		if(Cache::forever('forecastingData',$forecastingData)){
			return "Cached";
		}*/

		// create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, 'http://'.env('APP_DOMAIN')."/testcron");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
				echo $output;
        // close curl resource to free up system resources
        curl_close($ch);
	}

	/*
	*	Bins forecating Data first load
	*/
	private function binsDataFirstLoad($farm_id) {

		$bins = DB::table('feeds_bins')
                     ->select('feeds_bins.*',
					 			'feeds_bin_sizes.name AS bin_size_name',
								'feeds_feed_types.name AS feed_type_name',
								'feeds_feed_types.budgeted_amount')
					 ->leftJoin('feeds_bin_sizes','feeds_bin_sizes.size_id', '=', 'feeds_bins.bin_size')
					 ->leftJoin('feeds_feed_types','feeds_feed_types.type_id', '=', 'feeds_bins.feed_type')
                     ->where('farm_id', '=', $farm_id)
					 ->orderBy('feeds_bins.bin_number','asc')
                     ->get();


		$bins = json_decode(json_encode($bins),true);

		$binsData = array();

		$binsCount = count($bins) - 1;
		for($i=0;$i<=$binsCount;$i++){

			$up_hist[$i] = json_decode(json_encode($this->lastUpdate_numpigs($bins[$i]['bin_id'])), true);
			$numofpigs_ = $this->displayDefaultNumberOfPigs($bins[$i]['num_of_pigs'], $up_hist[$i][0]['num_of_pigs']);
			$budgeted_ = $this->getmyBudgetedAmount($up_hist[$i][0]['budgeted_amount'], $bins[$i]['feed_type']);

			$binsData[] = array(
				'bin_id'					=>	$bins[$i]['bin_id'],
				'days_to_empty'				=>	$this->daysOfBins($this->currentBinCapacity($bins[$i]['bin_id']),$budgeted_,$numofpigs_),
				'empty_date'				=>	$this->emptyDate($this->daysOfBins($this->currentBinCapacity($bins[$i]['bin_id']),$budgeted_,$numofpigs_)),
			);



		}

		return $binsData;
	}

	private function lastUpdate_numpigs($bin_id){

		$output = DB::table('feeds_bin_history')
					->select('num_of_pigs','amount','budgeted_amount','medication', 'feed_type')
					->where('bin_id','=',$bin_id)
					->where('update_date','<=','NOW()')
					->orderBy('update_date','desc')
					->take(1)->get();

		if($output == NULL) {

			$output = array(
						array(
							'num_of_pigs' => 0,
							'amount' => 0,
							'budgeted_amount' => 0,
							'feed_type' => 51,
							'medication' => 7
						)
					  );

		}

		return $output;

	}

	/**
	** Private Method
	** @Int Value Default, @Int Value from History
	** Compare two
	** Return Highest Value
	**/
	private function displayDefaultNumberOfPigs($default, $history) {
		$a = $default;

		if($history !=0) {

			$a = $history;

		}

		return $a;

	}

	public function getmyBudgetedAmount($lastupd_budgeted, $feed_type) {

			$a = $lastupd_budgeted;

			if($lastupd_budgeted == 0) {

			  $a = $this->getBudgetedAmount($feed_type);

			}

			return $a;

	}

	public function getBudgetedAmount($feedtype) {

		$output = DB::table('feeds_feed_types')
					->select('budgeted_amount')
					->where('type_id','=',$feedtype)
					->get();

		return !empty($output[0]->budgeted_amount) ? $output[0]->budgeted_amount : 0;

	}

	/*
	*	Current Bin Capacity converted to pounds
	*/
	public function currentBinCapacity($bin_id){


		$data =  DB::table('feeds_bin_history')
				->select(DB::raw('round(feeds_bin_history.amount * 2000,0) AS amount'))
				->where('feeds_bin_history.update_date','<=', date('Y-m-d') . '23:59:59')
				->where('feeds_bin_history.bin_id','=',$bin_id)
				->orderBy('feeds_bin_history.update_date','desc')
				->take(1)->get();

		if($data == NULL){

			/*$data =  DB::table('feeds_bins')
					 ->select(DB::raw('round(feeds_bins.amount * 2000, 0) AS amount'))
					 ->where('bin_id','=',$bin_id)
					 ->first();*/
			$data = 0;
		} else {

			$data = json_decode(json_encode($data), true);

			foreach($data as $k => $v){
				$data = $v['amount'];
			}

		}

		return $data;
	}
	/*
	*	Empty date
	*/
	public function emptyDate($days){

		$emptyDate = Carbon::now()->addDays($days)->format('m-d-Y');
		$soon = Carbon::now()->addDays($days)->format('M d');

		if($emptyDate == Carbon::now()->format('m-d-Y')){
			$output = "Empty";
		} else {
			$output = $soon;
		}

		return $output;
	}

	/*
	*	daysOfBins()
	*	Current Bin Amount / budgeted amount
	*/
	public function daysOfBins($currentBinAmount,$budgetedAmount,$numOfPigs){

		$budgetedAmount = str_replace(' lbs pig per day','',$budgetedAmount);

		$currentBinAmount = (int)$currentBinAmount;
		if($currentBinAmount != NULL && $budgetedAmount != NULL){
			$result_one = (int)(((float)$budgetedAmount)*$numOfPigs);
			$daysOfBins = @($currentBinAmount/$result_one);
			$daysOfBins = (int)round($daysOfBins,0);
		} else {
			$daysOfBins = 0;
		}
		//dd($daysOfBins);
		return $daysOfBins;
	}

	/*
	*	low bins
	*/
	private function lowBins($bins){

		$output = array();
		foreach($bins as $k => $v){
			if($v['days_to_empty'] <= 2){
				$output[] = array(
					'lowBins'	=> $v['days_to_empty']
				);
			}
		}

		return count($output);
	}

	/*
	* 	FarmBinsScanner
	*/
	public function FarmBinsScanner(){

		$farms = Farms::all();

		$forecastingData = array();
		$farms_count = count($farms)-1;
		for($i=0; $i<=$farms_count; $i++){
			$forecastingData[] = array(
				'farm_id'	=>	$farms[$i]['id'],
				'name'		=>	$farms[$i]['name'],
				'address'	=>	$farms[$i]['address'],
				'bins'		=> 	$this->testBinsData($farms[$i]['id'])
			);

		}

		$farmBins = $forecastingData;

		foreach($farmBins as $k => $v){

			$counter = count($v['bins'])-1;
			for($i=0; $i<=$counter; $i++){
				$this->updateBinHistory($v['bins'][$i]['bin_id']);
			}

		}

		$this->forecastingDataCacheBuilder();

	}

	/*
	*	Get all the bins id via farm id
	*/
	private function testBinsData($farm_id){

		$bins = Bins::where('farm_id','=',$farm_id)
					->select('bin_id')
					->get()->toArray();

		return $bins;

	}

	/*
	*	History Updater
	*/
	private function updateBinHistory($bin_id){

		$date_today = date("Y-m-d");
		$date_yesterday = date("Y-m-d", time() - 60 * 60 * 24);
		$previous_auto_update_data = BinsHistory::where('update_date','LIKE',$date_today.'%')
										->where('bin_id','=',$bin_id)
										->where('update_type','=','Automatic Update')
										->orderBy('update_date','desc')
										->get()
										->toArray();

		// check if there's already automatic update
		if(empty($previous_auto_update_data)){

			// if update not exists, update data based on 0 consumption update
			$history = $this->historyFinder($date_today,$bin_id);
			if(!empty($history[0])){

				if($history[0]['consumption'] == 0.0){
					//$this->testUpdate($history);
					$this->UpdateNoConsumption($history);
				}
			// update based on yesterday's update
			} else {
				$history = $this->historyFinder($date_yesterday,$bin_id);
				if(!empty($history[0])){
					$this->UpdateHistory($history);
				}
			}

		} else {
			echo "Nothing to update<br/>";

		}
	}


	/*
	*	Update finder query
	*/
	private function historyFinder($date,$bin_id){

		$data = BinsHistory::where('update_date','LIKE',$date.'%')
					->where('bin_id','=',$bin_id)
					->orderBy('update_date','desc')
					->get()
					->toArray();
		return $data;
	}

	/*
	*	No Consumption Updater
	*/
	private function UpdateNoConsumption($data){
		$amount = $this->calculateBin($data[0]['num_of_pigs'],$data[0]['budgeted_amount'],$data[0]['amount']);
		$consumption = $this->calculateConPerPig($data[0]['num_of_pigs'],$data[0]['budgeted_amount'],$data[0]['amount']);

		$amount = $amount < 0 ? 0 : $amount;

		$update_data = array(
			'update_date'		=>	date('Y-m-d H:i:s'),
			'bin_id'			=>	$data[0]['bin_id'],
			'farm_id'			=>	$data[0]['farm_id'],
			'num_of_pigs'		=>	$data[0]['num_of_pigs'],
			'user_id'			=>	$data[0]['user_id'],
			'amount'			=>	$amount,
			'update_type'		=>	'Automatic Update',
			'created_at'		=>	date('Y-m-d H:i:s'),
			'updated_at'		=>	date('Y-m-d H:i:s'),
			'budgeted_amount'	=>	$data[0]['budgeted_amount'],
			'remaining_amount'	=>	$data[0]['remaining_amount'],
			'sub_amount'		=>	$data[0]['sub_amount'],
			'variance'			=>	0,
			'consumption'		=>	$consumption,
			'admin'				=>	1,
			'medication'		=>	!empty($data[0]['medication']) ? $data[0]['medication'] : 8,
			'feed_type'			=>	$data[0]['feed_type'],
			'unique_id'			=>	!empty($data[0]['unique_id']) ? $data[0]['unique_id'] : "none"
			);

		BinsHistory::where('history_id','=',$data[0]['history_id'])->update($update_data);

		$bin_size = Bins::where('bin_id','=',$data[0]['bin_id'])->first();
		$med_name = Medication::where('med_id','=',$data[0]['medication'])->first();
		$feed_name = FeedTypes::where('type_id','=',$data[0]['feed_type'])->first();

		// Mobile created date
		/*$time = date('H:i:s',strtotime($data[0]['update_date'])+60*60);
		$date = date('Y-m-d');
		$created_at = date('Y-m-d H:i:s', strtotime($date.$time));
		$user_created_at = date('Y-m-d H:i:s', strtotime($data[0]['update_date'])+60*60);*/

		$mobile_data = array(
			'bin_id'			=>	!empty($bin_size->bin_number) ? $bin_size->bin_number : 0,
			'farm_id'			=>	$data[0]['farm_id'],
			'user_id'			=>	$data[0]['user_id'],
			'current_amount'	=>	$amount,
			'created_at'		=>	date('Y-m-d H:i:s'),
			'budgeted_amount'	=>	$data[0]['budgeted_amount'],
			'actual_amount'		=>	$amount,
			'bin_size'			=>	!empty($bin_size->size_id) ? $bin_size->size_id : 0,
			'variance'			=>	0,
			'consumption'		=>	$consumption,
			'feed_type'			=>	$data[0]['feed_type'],
			'medication'		=>	!empty($data[0]['medication']) ? $data[0]['medication'] : 8,
			'med_name'			=>	!empty($med_name->med_name) ? $med_name->med_name : 0,
			'feed_name'			=>	!empty($feed_name->name) ? $feed_name->name : '-',
			'user_created_at'	=>	date('Y-m-d H:i:s'),
			'num_of_pigs'		=>	$data[0]['num_of_pigs'],
			'bin_no_id'			=>	$data[0]['bin_id'],
			'status'			=>	2,
			'unique_id'			=>	!empty($data[0]['unique_id']) ? $data[0]['unique_id'] : "none"
		);

		$this->mobileSaveAccepted($mobile_data);

		$this->autoUpdateMessaging($update_data,$data[0]['history_id']);


		echo "0 Consumption update<br/>";
	}

	/*
	*	build the update data and update the history
	*/
	private function UpdateHistory($data){

		$update_date = date('Y-m-d',strtotime($data[0]['update_date']));

			$amount = $this->calculateBin($data[0]['num_of_pigs'],$data[0]['budgeted_amount'],$data[0]['amount']);
			$consumption = $this->calculateConPerPig($data[0]['num_of_pigs'],$data[0]['budgeted_amount'],$data[0]['amount']);

			$amount = $amount < 0 ? 0 : $amount;

			$update_data = array(
				'update_date'		=>	date('Y-m-d H:i:s'),
				'bin_id'			=>	$data[0]['bin_id'],
				'farm_id'			=>	$data[0]['farm_id'],
				'num_of_pigs'		=>	$data[0]['num_of_pigs'],
				'user_id'			=>	$data[0]['user_id'],
				'amount'			=>	$amount,
				'update_type'		=>	'Automatic Update',
				'created_at'		=>	date('Y-m-d H:i:s'),
				'updated_at'		=>	date('Y-m-d H:i:s'),
				'budgeted_amount'	=>	$data[0]['budgeted_amount'],
				'remaining_amount'	=>	$data[0]['remaining_amount'],
				'sub_amount'		=>	$data[0]['sub_amount'],
				'variance'			=>	0,
				'consumption'		=>	$consumption,
				'admin'				=>	1,
				'medication'		=>	!empty($data[0]['medication']) ? $data[0]['medication'] : 8,
				'feed_type'			=>	$data[0]['feed_type'],
				'unique_id'			=>	!empty($data[0]['unique_id']) ? $data[0]['unique_id'] : "none"
				);

			$this->UpdateSave($update_data,$data[0]['history_id']);

			$bin_size = Bins::where('bin_id','=',$data[0]['bin_id'])->first();
			$med_name = Medication::where('med_id','=',$data[0]['medication'])->first();
			$feed_name = FeedTypes::where('type_id','=',$data[0]['feed_type'])->first();

			// Mobile created date
			/*$time = date('H:i:s',strtotime($data[0]['update_date'])+60*60);
			$date = date('Y-m-d');
			$created_at = date('Y-m-d H:i:s', strtotime($date.$time));
			$user_created_at = date('Y-m-d H:i:s', strtotime($data[0]['update_date'])+60*60);*/

			$mobile_data = array(
				'bin_id'			=>	!empty($bin_size->bin_number) ? $bin_size->bin_number : 0,
				'farm_id'			=>	$data[0]['farm_id'],
				'user_id'			=>	$data[0]['user_id'],
				'current_amount'	=>	$amount,
				'created_at'		=>	date('Y-m-d H:i:s'),
				'budgeted_amount'	=>	$data[0]['budgeted_amount'],
				'actual_amount'		=>	$amount,
				'bin_size'			=>	!empty($bin_size->size_id) ? $bin_size->size_id : 0,
				'variance'			=>	0,
				'consumption'		=>	$consumption,
				'feed_type'			=>	$data[0]['feed_type'],
				'medication'		=>	!empty($data[0]['medication']) ? $data[0]['medication'] : 8,
				'med_name'			=>	!empty($med_name->med_name) ? $med_name->med_name : 0,
				'feed_name'			=>	!empty($feed_name->name) ? $feed_name->name : '-',
				'user_created_at'	=>	date('Y-m-d H:i:s'),
				'num_of_pigs'		=>	$data[0]['num_of_pigs'],
				'bin_no_id'			=>	$data[0]['bin_id'],
				'status'			=>	2,
				'unique_id'			=>	!empty($data[0]['unique_id']) ? $data[0]['unique_id'] : "none"
			);

			$this->mobileSaveAccepted($mobile_data);

	}

	/*
	*	calculate the amount of the bin
	*/
	private function calculateBin($number_of_pigs,$budgeted_amount,$current_amount){
		$total_consumption = $number_of_pigs * $budgeted_amount;
		$current_bin_amount = round($current_amount - ($total_consumption/2000),2);
		return $current_bin_amount;
	}

	/*
	*	calculate consumption per pig
	*/
	private function calculateConPerPig($number_of_pigs,$budgeted_amount,$current_amount){

		if($number_of_pigs == 0){
			$consumption_per_pig = 0;
		} else{
			$total_consumption = $number_of_pigs * $budgeted_amount;
			$consumption_per_pig = ($total_consumption/$number_of_pigs);
		}

		return $consumption_per_pig;
	}

	/*
	*	save the automatic update data in the mobile bins accepted load
	*/
	private function mobileSaveAccepted($data){

		MobileBinsAcceptedLoad::insert($data);

	}

	/*
	*	save the automatic update data
	*/
	private function UpdateSave($data,$history_id){

		if(BinsHistory::insert($data)){

			$this->autoUpdateMessaging($data,$history_id);

			echo "Updated Successfully<br/>";
		} else {
			echo "Something went wrong<br/>";
		}

	}


	/*
	*	Coud messaging for auto update
	*/
	private function autoUpdateMessaging($data,$history_id){

		// get farmer
		$farmer = DB::table('feeds_farm_users')
					->select('feeds_farm_users.*',
							'feeds_user_accounts.username AS username',
							'feeds_user_accounts.no_hash',
							'feeds_user_accounts.gcm_regid')
					->leftJoin('feeds_user_accounts',
							'feeds_user_accounts.id','=','feeds_farm_users.user_id')
					->where('feeds_farm_users.farm_id','=',$data['farm_id'])
					->get();


		$message = array(
					'history_id'		=>	$history_id,
					'update_date'		=>	$data['update_date'],
					'bin_id'			=>	$data['bin_id'],
					'farm_id'			=>	$data['farm_id'],
					'num_of_pigs'		=>	$data['num_of_pigs'],
					'user_id'			=>	$data['user_id'],
					'amount'			=>	$data['amount'],
					'update_type'		=>	$data['update_type'],
					'created_at'		=>	$data['created_at'],
					'updated_at'		=>	$data['updated_at'],
					'budgeted_amount'	=>	$data['budgeted_amount'],
					'remaining_amount'	=>	$data['remaining_amount'],
					'sub_amount'		=>	$data['sub_amount'],
					'variance'			=>	$data['variance'],
					'consumption'		=>	$data['consumption'],
					'admin'				=>	$data['admin'],
					'medication'		=>	$data['medication'],
					'feed_type'			=>	$data['feed_type'],
					'unique_id'			=>	$data['unique_id']
				);



		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}
	}

	/*
	*	Notification processor
	*/
	private function notification($registration_ids,$message){

		// Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );

        $headers = array(
            'Authorization: key=' . $this->google_api_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        //echo $result;
	}
}
