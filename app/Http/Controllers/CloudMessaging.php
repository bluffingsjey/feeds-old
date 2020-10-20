<?php

namespace App\Http\Controllers;

use Input;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Farms;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class CloudMessaging extends Controller
{

		private $google_api_key = "AIzaSyBenv1FNusdELHbFk9gfedn2qlnsRbPDwI";

	/**
     * Execute the list method
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => ['messagingNotification','farmerAcceptLoad']]);
    }

		/**
	   * livezilla cron job method
	   *
	   * @return Response
	   */
		public function livezillaCronJob()
		{
			$livezillaURL = "http://j2feeds.carrierinsite.com/livezilla/";
			$apiURL = $livezillaURL . "api/v2/api.php";

			// authentication parameters
			$postd["p_user"]='administrator';
			$postd["p_pass"]=md5('123456');

			// function parameter
			$postd["p_cronjob_execute"]=1;
			$postd["p_send_chat_transcripts"]=1;
			$postd["p_receive_emails"]=1;
			$postd["p_maintenance"]=1;
			$postd["p_social_media"]=1;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$apiURL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($postd));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);

			if($server_output === false)
			    exit(curl_error($ch));

			echo $server_output;
			echo "<hr>";
			curl_close($ch);

			$response = json_decode($server_output);

		}


	public function testingNoti(){
		$data = Input::all();
		$this->driverMessaging($data);
		$this->farmerAcceptLoadTest($data);
	}

	/**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function driverTestMessaging()
    {
		$driver_id = Input::get('driver_id');
		$truck_id = Input::get('truck_id');
		$date_of_delivery = Input::get('date_of_delivery');

		$driverGCM = User::find($driver_id);

		$message = array(
					"delivery"			=>	"driver",
					"fname"				=>	Auth::user()->username,
					"lname" 			=> 	"",
					"truck_id"			=>	$truck_id,
					"date_of_delivery" 	=>	$date_of_delivery
				);

		$registration_ids = array($driverGCM->gcm_regid);

		$this->notification($registration_ids,$message);

    }

	/*
	*	Delete delivery item notifier
	*/
	public function deleteDeliveryNotifier($data)
	{
		$data = array(
				'farm_id'		=> 	!empty($data['farm_id']) ? $data['farm_id'] : NULL,
				'unique_id'		=> 	$data['unique_id'],
				'driver_id'		=> 	!empty($data['driver_id']) ? $data['driver_id'] : NULL
				);

		if(empty($data['farm_id']) || $data['farm_id'] == NULL){
			$this->deleteDeliveryDriver($data);
		}

		$this->deleteDeliveryFarmers($data);
	}

	/*
	*	Delete delivery item for driver
	*/
	private function deleteDeliveryFarmers($farmerNoti)
	{

		// fetch the farmers in specific farm
		// get farmer
		$farmer = DB::table('feeds_farm_users')
					->select('feeds_farm_users.*',
							'feeds_user_accounts.username',
							'feeds_user_accounts.no_hash',
							'feeds_user_accounts.gcm_regid')
					->leftJoin('feeds_user_accounts',
							'feeds_user_accounts.id','=','feeds_farm_users.user_id')
					->where('feeds_farm_users.farm_id','=',$farmerNoti['farm_id'])
					->get();

		foreach($farmer as $k => $v) {

			$message = array(
						'user' 				=>	"farmer",
						'delivery_status'	=>	'delete',
						'unique_id'			=>	$farmerNoti['unique_id']
						);

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}

	}

	/*
	*	Delete delivery item for farmer
	*/
	private function deleteDeliveryDriver($driverNoti)
	{

		$driverGCM = User::find($driverNoti['driver_id']);

		$message = array(
					"user"				=>	"driver",
					"delivery_status"	=>	"delete",
					'unique_id'			=>	$driverNoti['unique_id']
				);
		if($driverGCM != NULL){
			$registration_ids = array($driverGCM->gcm_regid);
			$this->notification($registration_ids,$message);
		}
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function driverMessaging($driverNoti)
    {
		//$driverNoti = Input::all();

		$driverGCM = User::find($driverNoti['driver_id']);

		$message = array(
					"delivery"			=>	"driver",
					"fname"				=>	"admin", //Auth::user()->username,
					"lname" 			=> 	"",
					"truck_id"			=>	$driverNoti['truck_id'],
					"date_of_delivery" 	=>	$driverNoti['date_of_delivery'],
					'unique_id'			=>	$driverNoti['unique_id']
				);

		$registration_ids = array($driverGCM->gcm_regid);

		$this->notification($registration_ids,$message);

    }

	public function farmerAcceptLoad(){
		$data = Input::all();
		// get farmer
		$farmer = DB::table('feeds_farm_users')
					->select('feeds_farm_users.*',
							'feeds_user_accounts.username',
							'feeds_user_accounts.no_hash',
							'feeds_user_accounts.gcm_regid')
					->leftJoin('feeds_user_accounts',
							'feeds_user_accounts.id','=','feeds_farm_users.user_id')
					->where('feeds_farm_users.farm_id','=',$data['farm_id'])
					->get();

		$message = array(
					"delivery"			=>	"farmer",
					"user"				=>	$data['user'],
					"farm_id"			=>	$data['farm_id'],
					"date_of_delivery" 	=>	$data['date_of_delivery']
				);

		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}

	}

	/*
	*	 farmerLoadedData
	*/
	public function farmerLoadedData(){
		$data = Input::all();

		$driverGCM = User::find($driverNoti['driver_id']);

		$message = array(
			'farm_id'			=>	$data['farm_id'],
			'bin_no'			=>	$data['bin_no'],
			'compartment'		=>	$data['compartment'],
			'bin_id'			=>	$data['bin_id'],
			'unique_id'			=>	$data['unique_id'],
			'date_of_delivery'	=>	$data['date_of_delivery'],
			'delivery_status'	=>	$data['delivery_status'],
			'user'				=>	$data['user'],
		);


		$registration_ids = array($driverGCM->gcm_regid);
		$this->notification($registration_ids,$message);

	}

	/*
	*	Mark as delivered notification
	*/
	public function markasDelivered($data){

		$driverGCM = User::find($data['driver_id']);

		$message = array(
					"delivery_status" 	=>	'delivered',
					'unique_id'			=>	$data['unique_id']
				);

		$registration_ids = array($driverGCM->gcm_regid);

		$this->notification($registration_ids,$message);


	}

	/*
	*	Mark as delivered notification for farmer
	*/
	public function markasDeliveredFarmer($data){

		$farmer = DB::table('feeds_farm_users')
					->select('feeds_farm_users.*',
							'feeds_user_accounts.username',
							'feeds_user_accounts.no_hash',
							'feeds_user_accounts.gcm_regid')
					->leftJoin('feeds_user_accounts',
							'feeds_user_accounts.id','=','feeds_farm_users.user_id')
					->where('feeds_farm_users.farm_id','=',$data['farm_id'])
					->get();

		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);
			$message = array(
				'farm_id'			=>	$data['farm_id'],
				'bin_no'			=>	$data['bin_no'],
				'compartment'		=>	$data['compartment'],
				'bin_id'			=>	$data['bin_id'],
				'unique_id'			=>	$data['unique_id'],
				'date_of_delivery'	=>	$data['date_of_delivery'],
				'delivery_status'	=>	'unload',
				'user'				=>	$v->id
			);

			$this->notification($registration_ids,$message);

		}


	}

	/*
	*	Mark as delivered notification
	*/
	public function testMarkasDelivered(){

		$data = Input::all();

		$driverGCM = User::find($data['driver_id']);

		$message = array(
					"delivery_status" 	=>	'delivered',
					'unique_id'			=>	$data['unique_id']
				);

		$registration_ids = array($driverGCM->gcm_regid);

		dd($registration_ids);

		$this->notification($registration_ids,$message);


	}

	public function farmerAcceptLoadTest($data){
		// get farmer
		$farmer = DB::table('feeds_farm_users')
					->select('feeds_farm_users.*',
							'feeds_user_accounts.username',
							'feeds_user_accounts.no_hash',
							'feeds_user_accounts.gcm_regid')
					->leftJoin('feeds_user_accounts',
							'feeds_user_accounts.id','=','feeds_farm_users.user_id')
					->where('feeds_farm_users.farm_id','=',$data['farm_id'])
					->get();

		$message = array(
					"delivery"			=>	"farmer",
					"user"				=>	$data['user'],
					"farm_id"			=>	$data['farm_id'],
					"date_of_delivery" 	=>	$data['date_of_delivery'],
					'unique_id'			=>	$data['unique_id']
				);

		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}

	}

	public function updatePigsMessaging($data){

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


		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$message = array(
					"farm_id"			=>	$data['farm_id'],
					"user"				=>	$v->user_id,
					"bin_id"			=>	$data['bin_id'],
					"num_of_pigs" 		=>	$data['num_of_pigs']
				);

			$this->notification($registration_ids,$message);

		}
	}

	public function farmerLoadedTruckMessaging($data){

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
					"delivery"			=>	"farmer",
					"user"				=>	"admin",//Auth::user()->username,
					"farm_id"			=>	$data['farm_id'],
					"date_of_delivery" 	=>	$data['date_of_delivery'],
					"unique_id"			=>	$data['unique_id']
				);

		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}
	}

	public function autoUpdateMessaging($data,$history_id){

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
					'feed_type'			=>	$data['feed_type']
				);



		foreach($farmer as $k => $v) {

			$registration_ids = array($v->gcm_regid);

			$this->notification($registration_ids,$message);

		}
	}

	public function farmerMessaging()
    {
		$driverNoti = Input::all();
		$farmer = User::find(2);

		$message = array(
					"delivery" 			=> "farmer",
					"fname" 			=> $farmer->username,
					"lname" 			=> "farmerlastname",
					"farmname" 			=> Farms::find(45)->name,
					"date_of_delivery" 	=> $driverNoti[3]['date_of_delivery']
					);
		$reId = array($farmer->gcm_regid);


		$this->notification($reId,$message);

    }

		/*
 	 *	messaging notification
 	 */
 	 public function messagingNotification()
 	 {
		 $messaging = array(
			 'wakeup'		=> 	Input::get('wakeup'),
			 'userfrom'		=> 	Input::get('userid'),
			 'userto'		=> Input::get('to')
			 );

		 $user = User::find($messaging['userto']);

		 if($user != NULL){
			 $reId = array($user->gcm_regid);
			 return $this->notification($reId,$messaging);
		 }

 	 }


	private function notification($registration_ids,$message){

		// Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
						'priority' => 'high'
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
