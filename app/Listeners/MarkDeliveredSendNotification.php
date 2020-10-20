<?php

namespace App\Listeners;

use App\Deliveries;
use App\Bins;
use App\User;
use DB;
use Cache;
use App\Events\MarkDelivered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarkDeliveredSendNotification implements ShouldQueue
{
    private $google_api_key = "AIzaSyBenv1FNusdELHbFk9gfedn2qlnsRbPDwI";

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MarkDelivered  $event
     * @return void
     */
    public function handle(MarkDelivered $event)
    {

      $mobile_data = Cache::store('file')->get('mobile_data');

  		$this->markasDelivered($mobile_data);

      $deliveries = Deliveries::where('unique_id',$mobile_data['unique_id'])->get()->toArray();

      foreach($deliveries as $k => $v){

        $bins = Bins::where('bin_id',$v['bin_id'])->first()->toArray();
        Cache::forget('bins-'.$v['bin_id']);
        $data = array(
          'farm_id'			=>	$v['farm_id'],
          'bin_no'			=>	$bins['bin_number'],
          'compartment'		=>	$v['compartment_number'],
          'bin_id'			=>	$v['bin_id'],
          'unique_id'			=>	$v['unique_id'],
          'date_of_delivery'	=>	$v['delivery_date'],
          'delivery_status'	=>	'unload'
        );

        $this->markasDeliveredFarmer($data);

      }

    }

    /*
  	*	Mark as delivered notification
  	*/
  	public function markasDelivered($data){

  		$driverGCM = User::select('gcm_regid')->where('id',$data['driver_id'])->first()->toArray();

  		$message = array(
  					"delivery_status" 	=>	'delivered',
  					'unique_id'			=>	$data['unique_id']
  				);

      if($driverGCM != NULL){
    		$registration_ids = array($driverGCM['gcm_regid']);
    		$this->notification($registration_ids,$message);
      }

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
        if($v->gcm_regid != NULL){
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


  	}

    /*
    * Send mobile notification
    * notification
    */
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
