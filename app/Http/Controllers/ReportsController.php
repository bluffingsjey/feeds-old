<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Compartments;
use App\User;
use App\Deliveries;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use DB;

class ReportsController extends Controller
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
				$drivers_list = User::where('type_id',2)->orderBy('username','asc')->get()->toArray();

        return view("reports.driver.reports");
    }

	/**
     * driversTracking()
	 * Page for displaying aanlytics data about drivers deluiveries
     *
     * @return Response
     */
    public function driversTracking()
    {
		$days = 30;
		$reverse = array();
		$drivers = User::where('type_id',2)->get()->toArray();
        return view("reports.driver.driver",compact("days","reverse","drivers"));
    }

	/**
     * livestockTracking()
	 * Page for displaying anlytics data about drivers deluiveries
     *
     * @return Response
     */
    public function livestockTracking()
    {
        return view("reports.livestock.livestock");
    }

	/**
     * sort()
	 * Page for displaying aanlytics data about drivers deluiveries
     *
     * @return Response
     */
	public function sorting()
	{
		/*
		Types:
			driver
			tons-delivered
			delivery-time
			drive-time
			time-at-farm
			time-at-mill

		Sorting:
			asc
			desc
		*/

		$data = array(
			'date_from' => Input::get('date_from'),
			'date_to' => Input::get('date_to'),
			'type' => Input::get('type'),
			'sort' => Input::get('sorting')
		);

		return $this->search($data);

	}

	 /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function search($data = NULL)
    {

        $date_from  = $data['date_from'] == NULL ? date("Y-m-d",strtotime(Input::get('date_from'))) : date("Y-m-d",strtotime($data['date_from']));
				$date_to = $data['date_to'] == NULL ? date("Y-m-d",strtotime(Input::get('date_to'))) : date("Y-m-d",strtotime($data['date_to']));

				$sorting = $data['type'] == 'driver' ? $data['sort'] : 'asc';

				// fetch drivers
				$drivers = User::where('type_id',2)
								->orderBy('username')
								->get()->toArray();

				foreach($drivers as $k => $v){

					$required_args = array(
						'driver_id'		=>	$v['id'],
						'date_from'		=>	$date_from,
						'date_to'		=>	$date_to
					);

					$drivers_list[] = array(
						'driver'					=>	strtolower($v['username']),
						'tons_delivered'	=>	$this->tonsDelivered($required_args),
						'delivery_time'		=>	$this->deliveryTime($required_args),
						'drive_time'			=>	$this->driveTime($required_args),
						'time_at_farm'		=>	$this->timeAtFarm($required_args),
						'time_at_mill'		=>	$this->timeAtMill($required_args),
						'total_miles'			=>	$this->totalMiles($required_args)
					);

			}

		return view('reports.ajax.driver',compact("drivers_list"));

    }

		/**
      * Show the form for creating a new resource.
      *
      * @return Response
      */
     public function searchAPI($data)
     {

				$date_from  = $data['date_from'] == NULL ? date("Y-m-d") : date("Y-m-d",strtotime($data['date_from']));
				$date_to = $data['date_to'] == NULL ? date("Y-m-d") : date("Y-m-d",strtotime($data['date_to']));

				// fetch drivers
				$drivers = User::where('type_id',2)
							->orderBy('username')
							->get()->toArray();

				foreach($drivers as $k => $v){

					$required_args = array(
						'driver_id'		=>	$v['id'],
						'date_from'		=>	$date_from,
						'date_to'		=>	$date_to
					);

					$drivers_list[] = array(
						'driver'					=>	strtolower($v['username']),
						'tons_delivered'	=>	$this->tonsDelivered($required_args),
						'delivery_time'		=>	$this->deliveryTime($required_args),
						'drive_time'			=>	$this->driveTime($required_args),
						'time_at_farm'		=>	$this->timeAtFarm($required_args),
						'time_at_mill'		=>	$this->timeAtMill($required_args),
						'total_miles'			=>	$this->totalMiles($required_args)
					);

				}

				return $drivers_list;

     }

	/**
     * Data sorter.
     *
     * @return Response
     */
    public function dataSorter($data)
    {

	}

	/**
     * Fetch all the delivery unique id's.
     *
     * @return Response
     */
    public function deliveryUniqueID($data)
    {
     	$unique_id = "";

	    $query = DB::table('feeds_driver_stats')
					->select('deliveries_unique_id')
					->where('driver_id',$data['driver_id'])
					->whereBetween('date',array($data['date_from'],$data['date_to']))
					->get();
		$unique_ids = json_decode(json_encode($query), true);

		foreach($unique_ids as $k => $v){
			$unique_id .=	$v['deliveries_unique_id'] . ",";
		}

		return explode(",",substr($unique_id,0,-1));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function tonsDelivered($data)
    {
				$delivery_unique_id = $this->deliveryUniqueID($data);

				// disregard no end time deliveries
				//$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_time_at_farm');

		        //$query = DB::table('feeds_driver_stats')
						//	->where('driver_id',$data['driver_id'])
							//->whereBetween('date',array($data['date_from'],$data['date_to']))
						//	->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
						//	->sum('tons_delivered');


				$done_deliveries_unloaded_by_admin_n_driver = Deliveries::where('driver_id',$data['driver_id'])
																												->where('status',3)
																												->whereIn('unload_by',array('admin','driver'))
																												->where('delivery_label','active')
																												->whereBetween(DB::raw('LEFT(delivery_date,10)'),array($data['date_from'],$data['date_to']))
																												->sum('amount');

				if($done_deliveries_unloaded_by_admin_n_driver == NULL){
					return 0;
				}
				return $done_deliveries_unloaded_by_admin_n_driver;
    }

	/**
     * Delivery Time
	 * Calculate each time difference from start time and end time
     *
     * @return Response
     */
    public function deliveryTime($data)
    {

		$delivery_unique_id = $this->deliveryUniqueID($data);

		// disregard no end time deliveries
		$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_delivery_time');

		// actual time
		$geofence = DB::table('feeds_driver_stats_delivery_time')
					->select(DB::raw('TIMEDIFF(end_time,start_time) as hours'))
					->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
					->get();

		$geofence_drivetime = $this->sum_time($geofence);

		// estimated time
		// get the farm id's and estimated time from the farms profile
		$estimated_time_from_farm = $this->deliveryFarmIDs($delivery_unique_id);

		$drive_time = DB::table('feeds_driver_stats')
					->select(DB::raw('TIMEDIFF("'.$estimated_time_from_farm.'","'.$geofence_drivetime.'") as hours'))
					->limit(1)
					->get();

		$drive_time = $drive_time != NULL ? $drive_time[0]->hours : "00:00:00";


		$drive_time = str_replace("-","",$drive_time);

		$drive_time = DB::table('feeds_driver_stats')
						->select(DB::raw(''.$this->decimalHours($drive_time).'/'.count($done_deliveries_unique_id).' as testing'))
						->limit(1)
						->get();

		$drive_time = $drive_time == NULL ? 0 : $drive_time[0]->testing;

		$drive_time = round($drive_time,2);

		$type = $this->deliveryTimeType($estimated_time_from_farm, $geofence_drivetime);

		$final = array(
			'delivery_time'	=>	$this->convertTime($drive_time),
			'type'			=>	$type
		);

		return $final;

	}

	/**
     * get the deliveries that are done
	 *
     * @return Response
     */
    public function doneDeliveries($delivery_unique_id,$table)
    {
		$unique_id = "";

		$data = DB::table($table)
					->select('deliveries_unique_id')
					->whereIn('deliveries_unique_id',$delivery_unique_id)
					->where('end_time','!=','0000-00-00 00:00:00')
					->get();

		$unique_ids = json_decode(json_encode($data), true);

		foreach($unique_ids as $k => $v){
			$unique_id .=	$v['deliveries_unique_id'] . ",";
		}

		return explode(",",substr($unique_id,0,-1));
	}

	/**
     * get the deliveries that are done
	 *
     * @return Response
     */
    public function undoneDriveTime($delivery_unique_id)
    {
		$unique_id = "";

		$data = DB::table('feeds_driver_stats_drive_time')
					->select('deliveries_unique_id')
					->whereIn('deliveries_unique_id',$delivery_unique_id)
					->where('end_time','0000-00-00 00:00:00')
					->get();

		$unique_ids = json_decode(json_encode($data), true);

		foreach($unique_ids as $k => $v){
			$unique_id .=	$v['deliveries_unique_id'] . ",";
		}

		return explode(",",substr($unique_id,0,-1));
	}

	/**
     * get the farm id's and estimated time from the farms profile
	 *
     * @return Response
     */
    public function deliveryFarmIDs($delivery_unique_id)
    {
		$farm_ids = DB::table('feeds_driver_stats_time_at_farm')
						->select('farm_id')
						->whereIn('deliveries_unique_id',$delivery_unique_id)
						->where('end_time','!=','0000-00-00 00:00:00')
						->get();

		$farm_ids = json_decode(json_encode($farm_ids), true);

		$farm_del_time = 0;

		foreach($farm_ids as $k => $v){

			$estimated_time = DB::table('feeds_farms')
					->select('delivery_time')
					->where('id',$v['farm_id'])
					->take(1)
					->get();
			$estimated_time = json_decode(json_encode($estimated_time), true);

			//$farm_del_time = 0;

			if($estimated_time != NULL){
					$farm_del_time = $farm_del_time + $estimated_time[0]['delivery_time'];
			}

		}

		return $this->convertTime($farm_del_time);

	}

	/**
     * get the estimated time from the farms profile
	 *
     * @return Response
     */
    public function farmEstimatedTime($farm_id)
    {
		$estimated_time = DB::table('feeds_farms')
					->select('delivery_time')
					->whereIn('id',$farm_id)
					->get();

		$estimated_time = json_decode(json_encode($estimated_time), true);

		$farm_del_time = 0;
		foreach($estimated_time as $k => $v){
			$farm_del_time = $farm_del_time + $v['delivery_time'];
		}

		return $this->convertTime($farm_del_time);

	}

	/**
     * Types for delivery time
	 *
     * @return Response
     */
    public function deliveryTimeType($estimated,$actual)
    {
		if($estimated > $actual){
			$output = 'high';
		}elseif($estimated == $actual){
			$output = 'equal';
		}else{
			$output = 'low';
		}

		return $output;
	}

	/**
     * Drive Time
	 * Calculate each time difference from start time and end time
     *
     * @return Response
     */
    public function driveTime($data)
    {

        //$delivery_unique_id = $this->deliveryUniqueID($data);
				$delivery_unique_id = "";

				$query = DB::table('feeds_driver_stats')
							->select('deliveries_unique_id')
							->where('driver_id',$data['driver_id'])
							->whereBetween('date',array($data['date_from'],$data['date_to']))
							->get();
				$unique_ids = json_decode(json_encode($query), true);

				foreach($unique_ids as $k => $v){
					$delivery_time_data = DB::table('feeds_driver_stats_delivery_time')->where('deliveries_unique_id',$v['deliveries_unique_id'])->first();
					if($delivery_time_data != NULL){
						if($delivery_time_data->start_time != NULL && $delivery_time_data->end_time != "0000-00-00 00:00:00"){
							$delivery_unique_id .=	$v['deliveries_unique_id'] . ",";
						}
					}
				}
				//echo "deliveries unique_ids: ".$delivery_unique_id."<br/>";
				//echo "--------</br>";
				$delivery_unique_id = explode(",",substr($delivery_unique_id,0,-1));

				$total_loads = count($delivery_unique_id);


				// get undone drive time
				$undone_deliveries_unique_id = $this->undoneDriveTime($delivery_unique_id);

				$delivery_unique_id = str_replace($undone_deliveries_unique_id,"",$delivery_unique_id);

				// disregard no end time deliveries
				$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_drive_time');

				$actual_drive_time = DB::table('feeds_driver_stats_drive_time')
								->select(DB::raw('TIMEDIFF(start_time,end_time) as hours'))
								->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
								->get();

				$actual =  $this->sum_time($actual_drive_time);


				// estimated time
				$estimated =  DB::table('feeds_driver_stats_drive_time_google_est')
							->select(DB::raw('trip_time as hours'))
							->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
							->get();
				$estimated =  $this->sum_time($estimated);//$estimated != NULL ? $estimated[0]->hours : "00:00:00";


				// type of over or under the time estimated
				$type = $this->deliveryTimeType($estimated,$actual);

				// time difference just ignore the table
				$time_diff = DB::table('feeds_driver_stats')
							->select(DB::raw('TIMEDIFF("'.$estimated.'","'.$actual.'") as hours'))
							->limit(1)
							->get();

				$time_diff = $time_diff != NULL ? $time_diff[0]->hours : "00:00:00";

				$time_diff = str_replace("-","",$time_diff);
				if($time_diff != "00:00:00"){

					$time_diff = $this->decimalHours($time_diff);

					$testing_devision_mysql = DB::table('feeds_driver_stats')
								->select(DB::raw(''.$time_diff.'/'.$total_loads.' as testing'))
								->limit(1)
								->get();

					$final = round($testing_devision_mysql[0]->testing,2);
					$final = $this->convertTime($final);

				} else {
					$final = $time_diff;
				}

				if($actual == "00:00:00"){
						$final = "00:00";
				}

				$return = array(
					'drive_time' => date('H:i',strtotime($final)),//$this->convertTime($final),
					'type'		=>	$type
				);

				return $return;

    }

	public function decimalHours($time)
	{
		$hms = explode(":", $time);

		$m = !empty($hms[1]) ? $hms[1]/60 : 0;
		$h = !empty($hms[2]) ? $hms[2]/3600 : 0;
		return ($hms[0] + $m + $h);
	}


	/**
     * Time at Farm
	 * Calculate each time difference from start time and end time
     *
     * @return Response
     */
    public function timeAtFarm($data)
    {
        $delivery_unique_id = $this->deliveryUniqueID($data);

		// disregard no end time deliveries
		$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_time_at_farm');

		$output = DB::table('feeds_driver_stats_time_at_farm')
					->select(DB::raw('TIMEDIFF(end_time,start_time) as hours'))
					->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
					->get();

		$output = $this->sum_time($output);

		$total_loads = DB::table('feeds_driver_stats')
						->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
						->count();

		if($output != "00:00:00"){

			$output = $this->decimalHours($output);

			$testing_devision_mysql = DB::table('feeds_driver_stats')
						->select(DB::raw(''.$output.'/'.$total_loads.' as testing'))
						->limit(1)
						->get();

			$final = (float)$testing_devision_mysql[0]->testing;
			$final = sprintf('%02d:%02d', (int) $final, fmod($final, 1) * 60) . ":00";

		} else {
			$final = $output;
		}

		return $final;
    }

	/**
     * Time at Farm
	 * Calculate each time difference from start time and end time
     *
     * @return Response
     */
    public function timeAtMill($data)
    {
        $delivery_unique_id = $this->deliveryUniqueID($data);

		// disregard no end time deliveries
		$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_time_at_mill');

		// actual time
		/*$start_time = DB::table('feeds_driver_stats_time_at_mill')
						->select('start_time')
						->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
						->orderBy('id','asc')
						->take(1)
						->get();

		$end_time =  DB::table('feeds_driver_stats_time_at_mill')
						->select('end_time')
						->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
						->where('end_time','!=','0000-00-00 00:00:00')
						->orderBy('id','desc')
						->take(1)
						->get();

		if(!empty($end_time)){
			$output = DB::table('feeds_driver_stats_time_at_mill')
						->select(DB::raw('TIMEDIFF("'.$end_time[0]->end_time.'","'.$start_time[0]->start_time.'") as hours'))
						->take(1)
						->get();
			$output = $output != NULL ? $output[0]->hours : "00:00:00";

			$output = str_replace("-","",$output);

		} else {

			$output = "00:00:00";

		}*/

		$output = DB::table('feeds_driver_stats_time_at_mill')
						->select(DB::raw('TIMEDIFF(start_time,end_time) as hours'))
						->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
						->where('end_time','!=','0000-00-00 00:00:00')
						->get();
		$output = $this->sum_time($output);

		return $output;
    }


		/**
	     * Time at Farm
		 * Calculate each time difference from start time and end time
	     *
	     * @return Response
	     */
	    public function totalMiles($data)
	    {
	        $delivery_unique_id = $this->deliveryUniqueID($data);

					// disregard no end time deliveries
					$done_deliveries_unique_id = $this->doneDeliveries($delivery_unique_id,'feeds_driver_stats_drive_time');

					$output = DB::table('feeds_driver_stats_total_miles')
									->select('total_miles')
									->whereIn('deliveries_unique_id',$done_deliveries_unique_id)
									->sum('total_miles');

					return $output;
			}

	/**
     * get the sum of start and end time
     *
     * @return Response
     */
	 private function sum_time($output) {

		$result_to_array = (array)$output;
		$count = count($result_to_array);

		$total_hours = "";
		$today = strtotime("TODAY");

		foreach($result_to_array as $k => $v){
			$total_hours = $total_hours + (strtotime(str_replace("-","",$v->hours)) - $today);
		}

		$total_hours = $total_hours + $today;
		$sum_hours = date("H:i:s", $total_hours);

		if($sum_hours != "00:00:00"){
			$final = $sum_hours;
			return $final;
		}

		return $sum_hours;

	 }

	/**
     * Get the average time
     *
	 *
     * @return Response
     */
	public function average_time($total, $count, $rounding = 0)
	{
		$total = explode(":", strval($total));

		if (count($total) !== 3) return false;

		$sum = $total[0]*60*60 + $total[1]*60 + $total[2];
		$average = $sum / (float)$count;
		$hours = floor($average/3600);
		$minutes = floor(fmod($average,3600)/60);
		$seconds = number_format(fmod(fmod($average,3600),60),(int)$rounding);

		return $hours.":".$minutes.":".$seconds;
	}

	/*
	*	convert to time
	*/
	function convertTime($dec)
	{
		// start by converting to seconds
		$seconds = ($dec * 3600);
		// we're given hours, so let's get those the easy way
		$hours = floor($dec);
		// since we've "calculated" hours, let's remove them from the seconds variable
		$seconds -= $hours * 3600;
		// calculate minutes left
		$minutes = floor($seconds / 60);
		// remove those from seconds as well
		$seconds -= $minutes * 60;
		// return the time formatted HH:MM:SS
		return $this->lz($hours).":".$this->lz($minutes).":".$this->lz($seconds);
	}

	// lz = leading zero
	function lz($num)
	{
		return (strlen($num) < 2) ? "0{$num}" : $num;
	}





}
