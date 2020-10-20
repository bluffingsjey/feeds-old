<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Farms;
use App\Bins;
use App\BinsHistory;
use Input;
use DB;
use Cache;
use Auth;
use Storage;
use App\User;

class MovementController extends Controller
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

			$data = array(
				'date_from'	=> 	'2009-01-01',
				'date_to'		=>	date('Y-m-d'),
				'sort'			=>	"not_scheduled"
			);
			$output = $this->filterAll($data);
			Storage::put('animal_movement_data.txt',json_encode($output));
			$all = Storage::get('animal_movement_data.txt');

			$drivers = $this->filterAllDrivers();

      return view('movement.index',compact("all","drivers"));
    }

		/**
		 * Filter for animal movement page
		 *
		 * @return Response
		 */
		public function animalMovementFilter()
		{
		   	$type = Input::get('type');

				$data = array(
					'date_from'	=>	date("Y-m-d",strtotime(Input::get('date_from'))),
					'date_to'	=>	date("Y-m-d",strtotime(Input::get('date_to'))),
					'sort'		=>	Input::get('sort')
				);

				$nursery_groups = DB::table("feeds_movement_nursery_group")
														->where('status','!=','removed')
														->orderBy('group_name','asc')->get();
				$nursery_groups = $this->toArray($nursery_groups);
				Storage::put('nursery_groups_list.txt',json_encode($nursery_groups));
				$nursery_groups = Storage::get('nursery_groups_list.txt');

				$finisher_groups = DB::table("feeds_movement_finisher_group")
														->where('status','!=','removed')
														->orderBy('group_name','asc')->get();
				$finisher_groups = $this->toArray($finisher_groups);
				Storage::put('finisher_groups_list.txt',json_encode($finisher_groups));
				$finisher_groups = Storage::get('finisher_groups_list.txt');

				$output = array();

				switch($type){

					case 'farrowing_to_nursery':

						$group_table = 'feeds_movement_farrowing_group';
						$group_bins_table = 'feeds_movement_farrowing_bins';
						$file_name = 'farrowing_data.txt';
						$output = $this->animalGroupSorter($data,$group_table,$group_bins_table,$file_name);
						return view('movement.ajax.landing',compact("output","nursery_groups","finisher_groups"));

					case 'nursery_to_finisher':

						$group_table = 'feeds_movement_nursery_group';
						$group_bins_table = 'feeds_movement_nursery_bins';
						$file_name = 'nursery_data.txt';
						$output = $this->animalGroupSorter($data,$group_table,$group_bins_table,$file_name);
						return view('movement.ajax.landing',compact("output","nursery_groups","finisher_groups"));

					case 'finisher_to_market':

						$group_table = 'feeds_movement_finisher_group';
						$group_bins_table = 'feeds_movement_finisher_bins';
						$file_name = 'finisher_data.txt';
						$output = $this->animalGroupSorter($data,$group_table,$group_bins_table,$file_name);
						return view('movement.ajax.landing',compact("output","nursery_groups","finisher_groups"));

					default:

						$output = $this->filterAll($data);
						Storage::delete('animal_movement_data.txt');
						Storage::put('animal_movement_data.txt',json_encode($output));
						$output = Storage::get('animal_movement_data.txt');
						return view('movement.ajax.landing',compact("output","nursery_groups","finisher_groups"));

				}

		}

		/**
		 * sort the animal groups
		 *
		 * @return Response
		 */
		private function animalGroupSorter($data,$group_table,$group_bins_table,$file_name)
		{
				$output_one = $this->filterTransfer($data,$group_table,$group_bins_table);

				$checker = Storage::get($file_name);
				if($checker != NULL){
					Storage::delete($file_name);
				}

				if($data['sort'] == 'day_remaining'){

					usort($output_one, function($a,$b){
						if($a['date_to_transfer'] == $b['date_to_transfer'])
						return ($a['date_to_transfer'] < $b['date_to_transfer']);
						return ($a['date_to_transfer'] > $b['date_to_transfer'])?1:-1;
					});

					Storage::put($file_name,json_encode($output_one));
					$output = Storage::get($file_name);

					return $output;

				}

				$output_two = $this->filterTransferCreated($data,$group_table,$group_bins_table);

				$output = array_merge($output_one,$output_two);

				Storage::put($file_name,json_encode($output));
				$output = Storage::get($file_name);

				return $output;
		}

		/**
		 * Filter for all animal groups
		 *
		 * @return Response
		 */
		private function filterAllDrivers()
		{
			$drivers = User::select('id','username')->where('type_id',2)->get()->toArray();

			Storage::put('drivers_data.txt',json_encode($drivers));
			$output = Storage::get('drivers_data.txt');

			return $output;
		}

		/**
		 * Filter for all animal groups
		 *
		 * @return Response
		 */
		private function filterAll($data)
		{
				$farrowing_groups = $this->filterTransfer($data,'feeds_movement_farrowing_group','feeds_movement_farrowing_bins');
				$nursery_groups = $this->filterTransfer($data,'feeds_movement_nursery_group','feeds_movement_nursery_bins');
				$finisher_groups = $this->filterTransfer($data,'feeds_movement_finisher_group','feeds_movement_finisher_bins');

				$output_one = array_merge($farrowing_groups, $nursery_groups, $finisher_groups);

				if($data['sort'] == 'day_remaining'){
					usort($output_one, function($a,$b){

						return ($a['date_to_transfer'] - $b['date_to_transfer'])
									?: ($a['group_type_int'] - $b['group_type_int'])
									?: ($b['group_type_int'] - $a['group_type_int']);

					});

					return $output_one;
				}

				$output_two = $this->filterAdditional($data);

				$output = array_merge($output_one,$output_two);

				return $output;
		}

		/**
		 * Filter for all additional animal groups
		 *
		 * @return Response
		 */
		private function filterAdditional($data)
		{
				$farrowing_groups = $this->filterTransferCreated($data,'feeds_movement_farrowing_group','feeds_movement_farrowing_bins');
				$nursery_groups = $this->filterTransferCreated($data,'feeds_movement_nursery_group','feeds_movement_nursery_bins');
				$finisher_groups = $this->filterTransferCreated($data,'feeds_movement_finisher_group','feeds_movement_finisher_bins');

			$output = array_merge($farrowing_groups, $nursery_groups, $finisher_groups);

			return $output;
		}

		/**
		 * Type of groups detector
		 *
		 * @return Response
		 */
		private function groupType($table)
		{
				$type = "";

				if($table == 'feeds_movement_farrowing_bins'){
					$type = 'farrowing';
				}else if($table == 'feeds_movement_nursery_bins'){
					$type = 'nursery';
				}else{
					$type = 'finisher';
				}

				return $type;
		}

		/**
		 * Type of groups detector
		 *
		 * @return Response
		 */
		private function groupTypeInt($table)
		{
				$type = "";

				if($table == 'feeds_movement_farrowing_bins'){
					$type = 1;
				}else if($table == 'feeds_movement_nursery_bins'){
					$type = 2;
				}else{
					$type = 3;
				}

				return $type;
		}

		/**
		 * Filter for all farrowing to nursery groups
		 *
		 * @return Response
		 */
		private function filterTransfer($data,$group_table,$group_bins_table)
		{
		    if($data['sort'] == 'not_scheduled'){

					$groups = DB::table($group_table)
								->whereIn('status',['entered','pending'])
								->whereBetween('date_created',[$data['date_from'],$data['date_to']])
								->get();
					$groups = $this->toArray($groups);
					$groups = $this->filterTransferBins($groups,$group_table,$group_bins_table);

				} else {

					$groups = DB::table($group_table)
								->whereNotIn('status',['finalized','removed'])
								->whereBetween('date_created',[$data['date_from'],$data['date_to']])
								->orderBy('date_to_transfer','desc')
								->get();
					$groups = $this->toArray($groups);
					$groups = $this->filterTransferBins($groups,$group_table,$group_bins_table);
					usort($groups, function($a,$b){
						if($a['date_to_transfer'] == $b['date_to_transfer'])
						return ($a['date_to_transfer'] < $b['date_to_transfer']);
						return ($a['date_to_transfer'] > $b['date_to_transfer'])?1:-1;
					});
				}

				return $groups;

		}

		/**
		 * Filter for all farrowing to nursery groups
		 *
		 * @return Response
		 */
		private function filterTransferCreated($data,$group_table,$group_bins_table)
		{
					$groups = DB::table($group_table)
								->where('status','created')
								->whereBetween('date_created',[$data['date_from'],$data['date_to']])
								->orderBy('date_to_transfer','asc')
								->get();
					$groups = $this->toArray($groups);
					$groups = $this->filterTransferBins($groups,$group_table,$group_bins_table);

				return $groups;

		}


		/**
		 * Filter for all animal groups
		 *
		 * @return Response
		 */
		private function filterTransferBins($groups,$group_table,$group_bins_table)
		{
		    $data = array();

				foreach($groups as $k => $v){

					$date_to_transfer = (strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);

					$data[] = array(
						'group_id'			=>	$v['group_id'],
						'group_name'		=>	$v['group_name'],
						'unique_id'			=>	$v['unique_id'],
						'date_created'		=>	$v['date_created'],
						'date_transfered'	=>	$v['date_transfered'],
						'date_to_transfer'	=>	$this->daysRemaining($date_to_transfer,$group_table),
						'status'			=>	$v['status'],
						'group_type'		=>	$this->groupType($group_bins_table),
						'group_type_int'	=> $this->groupTypeInt($group_bins_table),
						'user_id'			=>	$v['user_id'],
						'farm_id'			=>	$v['farm_id'],
						'total_pigs'		=>	$this->totalPigsFilter($v['unique_id'],$group_bins_table),
						'farm_name'			=>	$this->farmData($v['farm_id']),
						'bin_data'			=>	$this->binsDataFilter($v['unique_id'],$group_bins_table,$v['farm_id']),
						'transfer_data'	=> 	$this->transferData($v['group_id']),
						'sched_pigs'		=>	$this->scheduledTransaferPigs($v['group_id'])
					);

				}

				return $data;

		}

		/**
		 * transferData()
		 * get the corresponding transfer data of a group
		 * @return Response
		 */
		private function transferData($group_id)
		{
			$transfer = DB::table('feeds_movement_transfer')->where('group_from',$group_id)->get();
			if($transfer == NULL){
				return NULL;
			}
			$transfer = $this->toArray($transfer);

			return $this->buildTransferData($transfer,NULL);
		}

		/**
		 * scheduledTransaferPigs()
		 * get the total scheduled for tansfer pigs
		 * @return Response
		 */
		private function scheduledTransaferPigs($group_id)
		{
			$total_shipped = DB::table('feeds_movement_transfer')
										->where('group_from',$group_id)
										->where('status','!=','finalized')
										->sum('shipped');
			return $total_shipped;
		}

		/**
		 * days remaining for all animal groups
		 *
		 * @return Response
		 */
		private function daysRemaining($date,$group_table)
		{
				$output = NULL;
				if($group_table == 'feeds_movement_farrowing_group') {
					if($date > 2) {
						$output = $date - 2 . "-" . $date;
					} else if ($date < 0) {
						$output = 0;
					} else {
						$output = $date;
					}
				} else if($group_table == 'feeds_movement_nursery_group') {
					if ($date < 0) {
						$output = 0;
					} else {
						$output = $date;
					}
				} else if($group_table == 'feeds_movement_finisher_group') {
					if($date > 10) {
						$output = $date - 10 . "-" . $date;
					} else if ($date < 0) {
						$output = 0;
					} else {
						$output = $date;

					}
				} else {
					$output = $output;
				}

				return round($output);
		}

		/**
		 * Get the total number of pigs for the specific animal groups
		 *
		 * @return Response
		 */
		private function totalPigsFilter($unique_id,$group_bins_table)
		{
				$total = DB::table($group_bins_table)->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $total;
		}

		/**
		 * Get the bins data for the farrowing page
		 *
		 * @return Response
		 */
		private function binsDataFilter($unique_id,$group_bins_table,$farm_id)
		{

				$bins = DB::table($group_bins_table)->where('unique_id',$unique_id)->get();

				$bins = $this->toArray($bins);
				$data = array();
				foreach($bins as $k => $v){
					$data[] = array(
									'id'			=>	$v['id'],
									'alias_label' 	=> $this->binLabel($v['bin_id']),
									'farm_name'	=> $this->farmName($farm_id),
									'bin_id'		=>	$v['bin_id'],
									'number_of_pigs'	=> $v['number_of_pigs']
									);
				}

				return $data;

		}

		/**
		 * Display the group of animals page
		 *
		 * @return Response
		 */
		private function farmName($farm_id)
		{
				$data = Farms::where('id',$farm_id)->first();
		    return $data != NULL ? $data->name : NULL;
		}


		/**
		 * Display the group of animals page
		 *
		 * @return Response
		 */
		public function groupPage()
		{
		    return view('movement.group.index');
		}

		/**
		 * Display the farrowing group maintenance page
		 *
		 * @return Response
		 */
		public function farrowingPage()
		{
				$farrow_data = DB::table('feeds_movement_farrowing_group')
								->select('feeds_movement_farrowing_group.group_id',
									'feeds_movement_farrowing_group.group_name',
									'feeds_movement_farrowing_group.farm_id',
									'feeds_movement_farrowing_group.date_created',
									'feeds_movement_farrowing_group.date_to_transfer',
									'feeds_movement_farrowing_group.unique_id',
									'feeds_movement_farrowing_group.start_weight',
									'feeds_movement_farrowing_group.end_weight',
									'feeds_movement_farrowing_group.crates',
									'feeds_farms.name')
								->leftJoin('feeds_farms','feeds_movement_farrowing_group.farm_id','=','feeds_farms.id')
								->where('feeds_movement_farrowing_group.status','!=','removed')
								->orderBy('group_id','desc')
								->take(8)
								->get();

				$farrow_count = DB::table('feeds_movement_farrowing_group')->count();

				$farms_data = Farms::select('id','name')->where('farm_type','farrowing')->orderBy('name','desc')->get()->toArray();
				$farrow_data = $this->buildData($farrow_data);

				return view('movement.group.farrowing.list',compact("farrow_data","farms_data","farrow_count"));
		}

		/**
		 * Display the farrowing group maintenance page
		 *
		 * @return Response
		 */
		public function farrowingPageLoadMore()
		{
				$skip = Input::get('items');

				$farrow_data = DB::table('feeds_movement_farrowing_group')
								->select('feeds_movement_farrowing_group.group_id',
									'feeds_movement_farrowing_group.group_name',
									'feeds_movement_farrowing_group.farm_id',
									'feeds_movement_farrowing_group.date_created',
									'feeds_movement_farrowing_group.date_to_transfer',
									'feeds_movement_farrowing_group.unique_id',
									'feeds_movement_farrowing_group.start_weight',
									'feeds_movement_farrowing_group.end_weight',
									'feeds_movement_farrowing_group.crates',
									'feeds_farms.name')
								->leftJoin('feeds_farms','feeds_movement_farrowing_group.farm_id','=','feeds_farms.id')
								->where('feeds_movement_farrowing_group.status','!=','removed')
								->orderBy('group_id','desc')
								->take(8)->skip($skip)
								->get();

				$farms_data = Farms::select('id','name')->where('farm_type','farrowing')->orderBy('name','desc')->get()->toArray();
				$farrow_data = $this->buildData($farrow_data);

		    return view('movement.group.farrowing.ajax.loadmore',compact("farrow_data","farms_data"));
		}

		/**
		 * remove pigs group
		 *
		 * @return Response
		 */
		public function removegroup()
		{
				$group_id = Input::get('group_id');

				// remove data on deceased and treatment
				DB::table('feeds_deceased')->where('group_id',$group_id)->delete();
				DB::table('feeds_treatment')->where('group_id',$group_id)->delete();

				$unique_id = DB::table('feeds_movement_farrowing_group')->where('group_id',$group_id)->first();
				$animal_bins = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id->unique_id)->get();

				if($animal_bins != NULL){
					foreach($animal_bins as $k => $v){
						DB::table('feeds_movement_farrowing_bins')
						->where('id',$v->id)
						->delete();
						DB::table('feeds_deceased')->where('group_id',$v->id)->delete();
						//DB::table('feeds_treatment')->where('group_id',$v->id)->delete();
						$this->removePigsHistory($v->bin_id);
					}
				}
				$this->removeTransferData($group_id);

				DB::table('feeds_movement_farrowing_group')->where('unique_id',$unique_id->unique_id)->delete();
				DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id->unique_id)->delete();

		}

		/**
		 * remove pigs on the bin history
		 *
		 * @return Response
		 */
		private function removeTransferData($group_id)
		{

			$transfer = DB::table('feeds_movement_transfer')->where('group_from',$group_id)->first();
			if($transfer != NULL){
				$transfer_bins = DB::table('feeds_movement_transfer_bins')->where('transfer_id',$transfer->transfer_id)->get();
				foreach($transfer_bins as $k => $v){
					DB::table('feeds_movement_transfer_bins')->where('transfer_id',$v->transfer_id)->delete();
				}
			}

			$transfer = DB::table('feeds_movement_transfer')->where('group_to',$group_id)->first();
			if($transfer != NULL){
				$transfer_bins = DB::table('feeds_movement_transfer_bins')->where('transfer_id',$transfer->transfer_id)->get();
				foreach($transfer_bins as $k => $v){
					DB::table('feeds_movement_transfer_bins')->where('transfer_id',$v->transfer_id)->delete();
				}
			}

			DB::table('feeds_movement_transfer')->where('group_from',$group_id)->delete();
			DB::table('feeds_movement_transfer')->where('group_to',$group_id)->delete();
		}

		/**
		 * remove pigs on the bin history
		 *
		 * @return Response
		 */
		public function removePigsHistory($bin_id)
		{
				$bin_history = BinsHistory::where('bin_id',$bin_id)->orderBy('history_id','desc')->first();

				if($bin_history != NULL){

					$total_pigs = DB::table('feeds_movement_farrowing_bins')
												->where('bin_id',$bin_id)
												->sum('number_of_pigs');
					BinsHistory::where('history_id',$bin_history->history_id)->update(['num_of_pigs'=>$total_pigs,'update_type'=>'Manual Update Number of Pigs, Remove Animal Groups Admin']);
					Cache::forget('bins-'.$bin_id);
					$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

					return true;

				}

				$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

				return false;
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

		/**
		 * build the data for the farrowing page
		 *
		 * @return Response
		 */
		private function buildData($data)
		{
				$data = $this->toArray($data);
				$farrowdata = array();

				foreach($data as $v){
					$farrowdata[] = array(
						'group_id'					=>	$v['group_id'],
						'group_name'				=>	$v['group_name'],
						'name'							=>	$v['name'],
						'farm_id'						=>	$v['farm_id'],
						'start_weight'			=>	$v['start_weight'],
						'end_weight'				=>	$v['end_weight'],
						'crates'						=>	$v['crates'],
						'date_created'			=>	$v['date_created'],
						'date_to_transfer'	=>	(strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24),
						'unique_id'					=>	$v['unique_id'],
						'bin_data'					=>	$this->binsData($v['unique_id']),
						'total_pigs'				=>	$this->totalPigs($v['unique_id']),
						'farrowing_farms'		=>	Farms::select('id','name')->where('farm_type','farrowing')->orderBy('name','desc')->get()->toArray()
					);
				}

				return $farrowdata;
		}


		/**
		 * Get the bins data for the farrowing page
		 *
		 * @return Response
		 */
		private function binsData($unique_id)
		{
					$bins = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id)->get();

				$bins = $this->toArray($bins);
				$data = array();
				foreach($bins as $k => $v){
					$data[] = array(
									'id'			=>	$v['id'],
									'alias_label' 	=> 	$this->binLabel($v['bin_id']),
									'bin_id'		=>	$v['bin_id'],
									'number_of_pigs'	=> $v['number_of_pigs']
									);
				}

				return $data;
		}

		/**
		 * Get the total number of pigs for the farrowing page
		 *
		 * @return Response
		 */
		private function totalPigs($unique_id)
		{
				$total = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $total; //== NULL ? 0 : $total[0]->number_of_pigs;
		}

		/**
		* Show the form for creating a new resource.
		*
		* @return Response
		*/
		public function createFarrowing()
		{
				$farrowing = Farms::select('id','name')->where('farm_type','farrowing')->orderBy('name','asc')->get()->toArray();
				$unique_id = $this->generateUniqueID();

			  return view('movement.group.farrowing.create',compact("farrowing","unique_id"));
		}

		/**
		 * farrowing bins for create farrowing group
		 *
		 * @return Response
		 */
		public function farrowingBins()
		{
				$farm_id = Input::get('farm_id');
				$bins = Bins::select('bin_id',DB::raw("CONCAT(bin_number, ' - ',alias) AS bin_number"))->orderBy('bin_id')->where('farm_id',$farm_id)->get()->toArray();

				return $bins;
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return Response
		 */
		public function farrowingFarms()
		{
				$farms_data = Farms::select('id','name')->where('farm_type','farrowing')->orderBy('name','desc')->get()->toArray();
				return $farms_data;
		}

    /**
     * Create the autopopulated group name
     *
     * @return Response
     */
    public function groupName()
    {
        $farm_id = Input::get('farm_id');
				$bin_id = Input::get('bin_id');

				$farm_name = Farms::select('name')->where('id',$farm_id)->take(1)->get()->toArray();
				$bin_number = Bins::select('bin_number')->where('bin_id',$bin_id)->take(1)->get();

				$bin_number = strlen((string)$bin_number[0]['bin_number']) == 1 ? "0".$bin_number[0]['bin_number'] : $bin_number[0]['bin_number'];

				$group_name = "H".substr(str_replace(" ","",$farm_name[0]['name']),0,2).date('mdy')."-".$bin_number;

				if(Input::get('bin_id_two') != NULL){

					$bin_number_two = Bins::select('bin_number')->where('bin_id',Input::get('bin_id_two'))->take(1)->get();
					$bin_number_two = strlen((string)$bin_number_two[0]['bin_number']) == 1 ? "0".$bin_number_two[0]['bin_number'] : $bin_number_two[0]['bin_number'];

					$group_name = "H".substr(str_replace(" ","",$farm_name[0]['name']),0,2).date('mdy')."-".$bin_number."-".$bin_number_two;

				}

				return strtoupper($group_name);

    }

    /**
     * Save farrowing
     *
     * @param  int  $id
     * @return Response
     */
    public function saveFarrowing()
    {
				$bins = Input::get('bins');
				$number_of_pigs = Input::get('num_of_pigs');
				$bins = $this->saveBins($bins,$number_of_pigs);
				$date_created = date("Y-m-d H:i:s",strtotime(Input::get('date_created')));

        $data_farrowing_group = array(
					'group_name'			=>	Input::get('group_name'),
					'farm_id'				=>	Input::get('farrowing'),
					'start_weight'	=>	Input::get('start_weight'),
					'end_weight'	=>	Input::get('end_weight'),
					'crates'			=>	Input::get('crates'),
					'date_created'			=>	$date_created,
					'date_to_transfer'	=> date('Y-m-d',strtotime($date_created . "+20 days")),
					'status'				=>	'entered',
					'user_id'				=>	Auth::id(),
					'unique_id'				=>	Input::get('unique_id')
				);

				foreach($bins as $k => $v){
					$data_farrowing_bins = array(
						'bin_id'			=>	$bins[$k]['bin_id'],
						'number_of_pigs'	=>	$bins[$k]['number_of_pigs'],
						'unique_id'			=>	Input::get('unique_id')
					);
					$this->saveGroupBins($data_farrowing_bins);
				}

				$save = DB::table('feeds_movement_farrowing_group')->insert($data_farrowing_group,Input::get('farrowing'));

				foreach($bins as $k => $v){
					$data_farrowing_bins = array(
						'bin_id'			=>	$bins[$k]['bin_id'],
						'number_of_pigs'	=>	$bins[$k]['number_of_pigs']
					);
					$this->updateBinsHistoryNumberOfPigs($data_farrowing_bins['bin_id'],$data_farrowing_bins['number_of_pigs'],"create");
				}

				if($save == 1){
								return redirect('farrowing');
				}

    }

		/**
		*	save bins
		*
		*/
		private function saveBins($bins,$number_of_pigs)
		{
				$data = array();
				foreach($bins as $k=>$v){
					$values = str_replace("-".$k,"",$v);
					if($values != "none"){
							$data[] = array('bin_id'=>$v,'number_of_pigs'=>$number_of_pigs[$k]);
					}
				}
				return $data;
		}

		/**
		* Save farrowing bins
		*
		* @return Response
		*/
		public function saveGroupBins($data)
		{
				DB::table('feeds_movement_farrowing_bins')->insert($data);
				// update the bin history and bin tables
				DB::table('feeds_bins')->where('bin_id',$data['bin_id'])->update(['num_of_pigs' => $data['number_of_pigs']]);
		}

		/**
		 * Save farrowing ntoes
		 *
		 * @return Response
		 */
		public function saveNotes($data,$notes,$type)
		{
				// fetch the group_id
				$farrowing_group = DB::table('feeds_movement_farrowing_group')->select('group_id')->where('unique_id',$data['unique_id'])->get();

				$notes = array(
					'group_id'		=>	$farrowing_group[0]->group_id,
					'message'		=>	$notes,
					'date_created'	=>	$data['date_created'],
					'type'			=>	$type,
					'unique_id'		=>	$data['unique_id']
				);

				DB::table('feeds_movement_farrowing_notes')->insert($notes);

		}


		/**
		 * Update farrowing
		 *
		 * @param  int  $id
		 * @return Response
		 */
		public function generateUniqueID()
		{
				return date('ymshis').uniqid(rand());
		}

		/**
		 * Update farrowing
		 *
		 * @return Response
		 */
		public function updateFarrowing()
		{

				$bins = Input::get('bins');
				$number_of_pigs = Input::get('number_of_pigs_group');
				$f_bins_id = Input::get('f_bins_id');
				$date_created = date("Y-m-d",strtotime(Input::get('date_created')));

				$data = "";
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);

				if($data > 1){
						return "duplicate bins";
				}


				$data_bin = array();
				foreach($bins as $k=>$v){
					$values = str_replace("-".$k,"",$v['value']);
					if($values != "none"){
							$data_bin[] = array('bin_id'=>$v['value'],'number_of_pigs'=>$number_of_pigs[$k]['value']);
					}
				}


				$data = array(
					'group_name'		=>	Input::get('group_name'),
					'date_created'		=>	$date_created,
					'farrowing_farm'	=> 	Input::get('farrowing_farm'),
					'unique_id'			=>	Input::get('unique_id'),
					'bin_one'			=>	Input::get('bin_one'),
					'bin_two'			=>	Input::get('bin_two'),
					'number_of_pigs'	=>	Input::get('number_of_pigs')
				);

				$group_data = array(
					'group_name'		=>	$data['group_name'],
					'farm_id'			=>	$data['farrowing_farm'],
					'date_created'		=>	$data['date_created'],
					'start_weight'		=>	Input::get('start_weight'),
					'end_weight'		=>	Input::get('end_weight'),
					'crates'				=>	Input::get('crates'),
					'date_to_transfer'	=> date('Y-m-d',strtotime($data['date_created'] . "+20 days")),
					'date_transfered'	=>	"0000-00-00 00:00:00",
					'status'			=>	'entered',
					'user_id'			=>	Auth::id()
				);

				/*
				if farm is the same as the farm on the farrowing group update else, delete bins and insert new selected bins
				*/
				$farm = $this->checkFarmExistsFarrowing($data['farrowing_farm'],$data['unique_id']);
				if($farm == 0){
					dd("Exist");
					$bins_to_delete = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$data['unique_id'])->get();
					if($bins_to_delete != NULL){
						foreach($bins_to_delete as $k => $v){
							Cache::forget('bins-'.$v->bin_id);
						}
					}
					// delete bins
					DB::table('feeds_movement_farrowing_bins')->where('unique_id',$data['unique_id'])->delete();
					//dd($bins[0]['value']);
					foreach($data_bin as $k => $v){
						$this->insertBinFarrowing($v['bin_id'],$data['unique_id'],$v['number_of_pigs']);
					}
				} else {
					// update bins
					foreach($data_bin as $k => $v){
						$this->updateBinFarrowing($v['bin_id'],$data['unique_id'],$v['number_of_pigs'],$f_bins_id[$k]['value']);
					}

				}

				// update farrowing group
				DB::table('feeds_movement_farrowing_group')->where('unique_id',$data['unique_id'])->update($group_data);

				return "success";
		}


		/**
		* Farrowing clear cache for previous selected bins
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function clearCachePreviousSelectedBinsFarrowing($unique_id)
		{
				$bins = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id)->get();
				$bins = $this->toArray($bins);

				foreach($bins as $k => $v){
					Cache::forget('bins-'.$v['bin_id']);
				}
		}

		/**
		* Farrowing farm data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkFarmExistsFarrowing($farm_id,$unique_id)
		{
				$counter  = DB::table('feeds_movement_farrowing_group')
					->where('unique_id',$unique_id)
					->where('farm_id',$farm_id)
					->count();

				return $counter;
		}

		/**
		* Farrowing bins data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkBinExistsFarrowing($bin_id,$unique_id)
		{
				$count  = DB::table('feeds_movement_farrowing_bins')
					->where('unique_id',$unique_id)
					->where('bin_id',$bin_id)
					->count();
				return $count;
		}

		/**
		* Update farrowing bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function updateBinFarrowing($bin_id,$unique_id,$pigs,$f_bin_id)
		{

				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs
				);

				DB::table('feeds_movement_farrowing_bins')
				->where('id',$f_bin_id)
				->where('unique_id',$unique_id)
				->update($data);

				// if the number of pigs = 0
				//if($pigs == 0){
				//	DB::table('feeds_movement_farrowing_bins')
				//	->where('id',$f_bin_id)
				//	->where('unique_id',$unique_id)
				//	->delete();
				//}

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"update");

		}

		/**
		* Insert farrowing bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function insertBinFarrowing($bin_id,$unique_id,$pigs)
		{

				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs,
				'unique_id'			=>	$unique_id
				);

				DB::table('feeds_movement_farrowing_bins')->insert($data);

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"create");

		}

		/**
		* Bin history updater
		*
		* @param  int  $farm_id
		* @param  int  $bin_id
		* @param  int  $number_of_pigs
		* @return Response
		*/
		private function updateLatestBinHistory($farm_id,$bin_id,$number_of_pigs)
		{
				$latest_bin_history = DB::table('feeds_bin_history')->select('history_id')->where('bin_id',$bin_id)->orderBy('history_id','desc')->take(1)->get();



				if($latest_bin_history != NULL){
				//update
				DB::table('feeds_bin_history')
				->where('history_id',$latest_bin_history[0]->history_id)
				->update(['num_of_pigs' => $number_of_pigs,'update_type'=>'Manual Update','update_date'=>date("Y-m-d H:i:s")]);

				} else {
				// save
				$this->insertBinHistory($farm_id,$bin_id,$number_of_pigs);
				}

				// clear cache
				Cache::forget('bins-'.$bin_id);
				// notify mobile app
				$this->updatePigsNotification($farm_id,$bin_id,$number_of_pigs);



				return true;
		}

		/**
		* Save the newly entered number of pigs to the bin history table
		*
		* @param  int  $farm_id
		* @param  int  $bin_id
		* @param  int  $number_of_pigs
		* @return Response
		*/
		private function insertBinHistory($farm_id,$bin_id,$number_of_pigs)
		{

		}


		/**
		* Notify the mobile app for the number of pigs update
		*
		* @param  int  $farm_id
		* @param  int  $bin_id
		* @param  int  $number_of_pigs
		* @return Response
		*/
		private function updatePigsNotification($farm_id,$bin_id,$number_of_pigs)
		{

				$notification = new CloudMessaging;
				$farmer_data = array(
				'farm_id'		=> 	$farm_id,
				'bin_id'		=> 	$bin_id,
				'num_of_pigs'	=> 	$number_of_pigs
				);
				$notification->updatePigsMessaging($farmer_data);
				unset($notification);

				return true;

		}

    /**
     * Check if the group name already exists in the table
     *
     * @param  int  $id
     * @return Response
     */
    public function checkExists()
    {
				$data = "";
				$bins = Input::get('bins');
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);
				if($data > 1){
					return "duplicate bins";
				}

        $exists = DB::table('feeds_movement_farrowing_group')
					->select('group_name')
					->where('group_name',Input::get('group_name'))
					->where('status','!=','removed')
					->take(1)
					->get();

				return $exists != NULL ? 0 : 1;

    }


		/**
		* Display the nursery group maintenance page
		*
		* @return Response
		*/
		public function nurseryPage()
		{
				$nursery_data = $this->nurseryData();
				$nursery_counter = DB::table('feeds_movement_nursery_group')->count();
			  return view('movement.group.nursery.list',compact("nursery_data","nursery_counter"));
		}

		/**
		* Get all the data of the nursery group
		*
		* @return Response
		*/
		private function nurseryData()
		{
				$nursery_data = DB::table('feeds_movement_nursery_group')
												->where('status','!=','removed')
												->orderBy('group_id','desc')
												->take(8)->get();
				$nursery_data = $this->toArray($nursery_data);

				$data = array();
				foreach($nursery_data as $k => $v){
					$data[] = array(
						'group_id'			=>	$v['group_id'],
						'group_name'		=>	$v['group_name'],
						'start_weight'	=>	$v['start_weight'],
						'end_weight'		=>	$v['end_weight'],
						'unique_id'			=>	$v['unique_id'],
						'date_created'		=>	$v['date_created'],
						'date_to_transfer'	=> (strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24),
						'date_transfered'	=>	$v['date_transfered'],
						'status'			=>	$v['status'],
						'user_id'			=>	$v['user_id'],
						'farm_id'			=>	$v['farm_id'],
						'total_pigs'		=>	$this->totalNurseryPigs($v['unique_id']),
						'farm_name'			=>	$this->farmData($v['farm_id']),
						'bin_data'			=>	$this->nurseryBinsData($v['unique_id'])
					);
				}

				return $data;

		}

		/**
		* Get all the data of the nursery group
		*
		* @return Response
		*/
		public function nurseryPageLoadMore()
		{
				$skip = Input::get('items');
				$nursery_data = DB::table('feeds_movement_nursery_group')
													->where('status','!=','removed')
													->orderBy('group_id','desc')
													->take(8)->skip($skip)->get();
				$nursery_data = $this->toArray($nursery_data);

				$data = array();
				foreach($nursery_data as $k => $v){
					$data[] = array(
						'group_id'			=>	$v['group_id'],
						'group_name'		=>	$v['group_name'],
						'start_weight'	=>	$v['start_weight'],
						'end_weight'	=>	$v['end_weight'],
						'unique_id'			=>	$v['unique_id'],
						'date_created'		=>	$v['date_created'],
						'date_to_transfer'	=> (strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24),
						'date_transfered'	=>	$v['date_transfered'],
						'status'			=>	$v['status'],
						'user_id'			=>	$v['user_id'],
						'farm_id'			=>	$v['farm_id'],
						'total_pigs'		=>	$this->totalNurseryPigs($v['unique_id']),
						'farm_name'			=>	$this->farmData($v['farm_id']),
						'bin_data'			=>	$this->nurseryBinsData($v['unique_id'])
					);
				}
				$nursery_data = $data;
				return view('movement.group.nursery.ajax.loadmore',compact("nursery_data"));

		}

		/**
		* Get all the data of the nursery group
		*
		* @return Response
		*/
		private function farmData($farm_id)
		{
				$farm = Farms::select('name')->where('id',$farm_id)->first();
				return $farm != NULL ? $farm->name : NULL;
		}

		/**
		* Get the bins data for the farrowing page
		*
		* @return Response
		*/
		private function nurseryBinsData($unique_id)
		{
				$bins = DB::table('feeds_movement_nursery_bins')->where('unique_id',$unique_id)->orderBy('bin_id','asc')->get();
				$bins = $this->toArray($bins);
				$data = array();
				foreach($bins as $k => $v){
				$data[] = array(
								'id'			=>	$v['id'],
								'alias_label' 	=> $this->binLabel($v['bin_id']),
								'bin_id'		=>	$v['bin_id'],
								'number_of_pigs'	=>	$v['number_of_pigs']
								);
				}

				return $data;
		}

		/**
		* Show the form for creating a new resource.
		*
		* @return Response
		*/
		public function nurseryFarms()
		{
				$farms_data = Farms::select('id','name')->where('farm_type','nursery')->orderBy('name','desc')->get()->toArray();
				return $farms_data;
		}

		/**
		* nursery bins for create nursery group
		*
		* @return Response
		*/
		public function nurseryBins()
		{
				$farm_id = Input::get('farm_id');
				$bins = Bins::select('bin_id',DB::raw("CONCAT(bin_number, ' - ',alias) AS bin_number"))->orderBy('bin_id')->where('farm_id',$farm_id)->get()->toArray();
				return $bins;
		}

		/**
		* Update nursery
		*
		* @return Response
		*/
		public function updateNursery()
		{
				$date_created = date("Y-m-d",strtotime(Input::get('date_created')));
				$bins = Input::get('bins');
				$number_of_pigs = Input::get('number_of_pigs_group');
				$f_bins_id = Input::get('f_bins_id');

				$data = "";
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);

				if($data > 1){
						return "duplicate bins";
				}

				$data_bin = array();
				foreach($bins as $k=>$v){
					$values = str_replace("-".$k,"",$v['value']);
					if($values != "none"){
							$data_bin[] = array('bin_id'=>$v['value'],'number_of_pigs'=>$number_of_pigs[$k]['value']);
					}
				}

				$data = array(
					'group_name'		=>	Input::get('group_name'),
					'date_created'		=>	date("Y-m-d",strtotime(Input::get('date_created'))),
					'nursery_farm'		=> 	Input::get('nursery_farm'),
					'unique_id'			=>	Input::get('unique_id'),
					'bin_one'			=>	Input::get('bin_one'),
					'bin_two'			=>	Input::get('bin_two'),
					'number_of_pigs'	=>	Input::get('number_of_pigs')
				);

				$group_data = array(
					'group_name'		=>	$data['group_name'],
					'farm_id'			=>	$data['nursery_farm'],
					'start_weight'	=>	Input::get('start_weight'),
					'end_weight'	=>	Input::get('end_weight'),
					'date_created'		=>	$data['date_created'],
					'date_to_transfer'	=> date('Y-m-d',strtotime($date_created . "+40 days")),
					'date_transfered'	=>	"0000-00-00 00:00:00",
					'status'			=>	'entered',
					'user_id'			=>	Auth::id()
				);


				/*
				if farm is the same as the farm on the farrowing group update else, delete bins and insert new selected bins
				*/
				$farm = $this->checkFarmExistsNursery($data['nursery_farm'],$data['unique_id']);
				if($farm == 0){
						$bins_to_delete = DB::table('feeds_movement_nursery_bins')->where('unique_id',$data['unique_id'])->get();
						if($bins_to_delete != NULL){
							foreach($bins_to_delete as $k => $v){
								Cache::forget('bins-'.$v->bin_id);
							}
						}
						// delete bins
						DB::table('feeds_movement_nursery_bins')->where('unique_id',$data['unique_id'])->delete();

						// insert bins
						foreach($data_bin as $k => $v){
							$this->insertBinNursery($v['bin_id'],$data['unique_id'],$v['number_of_pigs']);
						}
				}else{
						// update bins
						foreach($data_bin as $k => $v){
	 						$this->updateBinNursery($v['bin_id'],$data['unique_id'],$v['number_of_pigs'],$f_bins_id[$k]['value']);
	 					}
				}

				// update nursery group
				DB::table('feeds_movement_nursery_group')->where('unique_id',$data['unique_id'])->update($group_data);

				return "success";
		}

		/**
		* Nursery clear cache for previous selected bins
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function clearCachePreviousSelectedBinsNursery($unique_id)
		{
				$bins = DB::table('feeds_movement_nursery_bins')->where('unique_id',$unique_id)->get();
				$bins = $this->toArray($bins);

				foreach($bins as $k => $v){
					Cache::forget('bins-'.$v['bin_id']);
				}

		}

		/**
		* Nursery farm data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkFarmExistsNursery($farm_id,$unique_id)
		{
				$counter  = DB::table('feeds_movement_nursery_group')
					->where('unique_id',$unique_id)
					->where('farm_id',$farm_id)
					->where('status','!=','removed')
					->count();

				return $counter;
		}

		/**
		* Nursery bins data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkBinExistsNursery($bin_id,$unique_id)
		{
				$count  = DB::table('feeds_movement_nursery_bins')
					->where('unique_id',$unique_id)
					->where('bin_id',$bin_id)
					->count();
				return $count;
		}

		/**
		* Update nursery bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function updateBinNursery($bin_id,$unique_id,$pigs,$f_bin_id)
		{

				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs
				);

				DB::table('feeds_movement_nursery_bins')
				->where('id',$f_bin_id)
				->where('unique_id',$unique_id)
				->update($data);

				/* if the number of pigs = 0
				if($pigs == 0){
					DB::table('feeds_movement_nursery_bins')
					->where('id',$f_bin_id)
					->where('unique_id',$unique_id)
					->delete();
				}*/

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"update");

		}

		/**
		* Insert nursery bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function insertBinNursery($bin_id,$unique_id,$pigs)
		{
				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs,
				'unique_id'			=>	$unique_id
				);

				DB::table('feeds_movement_nursery_bins')->insert($data);

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"create");
		}


		/**
		* Get all the data of the combined farrowing group
		*
		* @return Response
		*/
		private function nurseryFarrowingData($unique_id)
		{
				$nursery_farrow_data = DB::table('feeds_movement_nursery_farrowing_group')
									->where('unique_id',$unique_id)
									->select(DB::raw('DISTINCT(farrowing_group_id) as farrowing_group_id'),'unique_id')
									->get();

				$nursery_farrow_data = $this->toArray($nursery_farrow_data);

				$nursery_farrow_data = $this->nurseryFarrowingDataBuild($nursery_farrow_data);

				return $nursery_farrow_data;
		}

		/**
		* build the nurseryFarrowingData
		*
		* @return Response
		*/
		private function nurseryFarrowingDataBuild($data)
		{
				$farrowing_groups = array();

				foreach($data as $k => $v){
						$farrowing_groups[] = array(
							//'id'					=>	$v['id'],
							//'farrowing_group_id'	=>	$v['farrowing_group_id'],
							'number_of_pigs'		=>	$this->numberOfPigs($v['farrowing_group_id']),
							'unique_id'				=>	$v['unique_id'],
							'group_name'			=>	$this->farrowingGroupName($v['farrowing_group_id']),
							'farm_name'				=>	$this->farrowingDataFarm($v['farrowing_group_id']),
							'bin'					=>	$this->nurseryDataBin($v['farrowing_group_id'], $v['unique_id'])
						);
				}

				return $farrowing_groups;
		}

		private function numberOfPigs($unique_id)
		{
				$data = DB::table('feeds_movement_nursery_farrowing_group')->select('number_of_pigs')->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $data;
		}

		/**
		* Get the farm related to the farrowing group
		*
		* @return Response
		*/
		private function farrowingGroupName($farrowing_group_id)
		{
				$data = DB::table('feeds_movement_farrowing_group')->select('group_name')->where('group_id',$farrowing_group_id)->first();

				return $data != NULL ? $data->group_name : NULL;
		}

		/**
		* Get the farm related to the farrowing group
		*
		* @return Response
		*/
		private function farrowingDataFarm($farrowing_group_id)
		{
				$data_farrow = DB::table('feeds_movement_farrowing_group')->select('farm_id')->where('group_id',$farrowing_group_id)->first();

				if($data_farrow == NULL){
					return NULL;
				}

				$data = DB::table('feeds_farms')->select('name')->where('id',$data_farrow->farm_id)->first();

				return $data->name;
		}

		/**
		* Get the bins related to the farrowing group
		*
		* @return Response
		*/
		private function farrowingDataBin($unique_id)
		{
				$data = DB::table('feeds_movement_farrowing_bins')->where('unique_id',$unique_id)->get();
				$data = $this->toArray($data);
				foreach($data as $k => $v){
						$bins[] = array(
							'unique_id'	=>	$v['unique_id'],
							'bin_id'	=>	$v['bin_id'],
							//'alias_label'	=>	$this->binLabel($v['bin_id'])
						);
				}

				return $bins;
		}

		/**
		* Get the bins related to the nursery created group
		*
		* @return Response
		*/
		private function nurseryDataBin($group_id,$unique_id)
		{
				$data = DB::table('feeds_movement_nursery_farrowing_group')
					->where('farrowing_group_id',$group_id)
					->where('unique_id',$unique_id)
					->get();

				$data = $this->toArray($data);

				foreach($data as $k => $v){
						$bins[] = array(
							'bin_id'	=>	$v['bin_id'],
							//'alias_label'	=>	$this->binLabel($v['bin_id'])
						);
				}

				return $bins;
		}

		/**
		* Get the bins alias and label
		*
		* @return Response
		*/
		private function binLabel($bin_id)
		{
				$data = DB::table('feeds_bins')->select('bin_number','alias')->where('bin_id',$bin_id)->first();

				if($data == NULL){
					return NULL;
				}

				$alias = $data->bin_number . " - ". $data->alias;

				return $alias;
		}

		/**
		* Get all the data of the combined farrowing group
		*
		* @return Response
		*/
		private function totalNurseryPigs($unique_id)
		{
				$pigs = DB::table('feeds_movement_nursery_bins')->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $pigs; //->number_of_pigs;
		}

		/**
		* Display the create nursery group page
		*
		* @return Response
		*/
		public function createNursery()
		{
				$unique_id = $this->generateUniqueID();

				$farrowing_groups = $this->loadFarrowingGroups();

				$nursery = $this->getNurseryFarms();

			  return view('movement.group.nursery.create',compact("unique_id","farrowing_groups","nursery"));
		}

		/**
		* get the list of nursery farms
		*
		* @return Response
		*/
		private function getNurseryFarms()
		{
				$nursery = DB::table('feeds_farms')->where('farm_type','nursery')->orderBy('name','asc')->lists('name','id');

				return $nursery;
		}

		/**
		* get the selected bins
		*
		* @return Response
		*/
		public function getSelectedBins()
		{
				if(Input::get('group_id') != NULL){
						$group_id  = Input::get('group_id');
						$selected = Cache::store('file')->get('selected_index-'.$group_id);
						return $selected;
				}

				$selected = Cache::store('file')->get('selected_index');
				return $selected;
		}

		/**
		* get the selected bins
		*
		* @return Response
		*/
		public function saveSelectedBins()
		{

				if(Input::get('group_id') != NULL){
						$group_id  = Input::get('group_id');
						Cache::forget('selected_index-'.$group_id);

						$selected_index = array(
							'bin_one'	=> 	Input::get('bin_one'),
							'bin_two'	=>	Input::get('bin_two')
						);

						Cache::forever('selected_index-'.$group_id,$selected_index);
						$selected = Cache::store('file')->get('selected_index-'.$group_id);
						return $selected;
				}

				Cache::forget('selected_index');

				$selected_index = array(
				'bin_one'	=> 	Input::get('bin_one'),
				'bin_two'	=>	Input::get('bin_two')
				);

				Cache::forever('selected_index',$selected_index);
				$selected = Cache::store('file')->get('selected_index');

				return $selected;
		}

		/**
		* get the selected bins
		*
		* @return Response
		*/
		public function clearSelectedBins()
		{
				Cache::forget('selected_index');
		}

		/**
		* get the selected bins on animal groups list
		*
		* @return Response
		*/
		public function clearSelectedBinsEdit()
		{
				if(Input::get('group_id') != NULL){
						$group_id  = Input::get('group_id');

						Cache::forget('selected_index-'.$group_id);
				}
		}


		/**
		* Count all farrowing groups
		*
		* @return Response
		*/
		public function countFarrowingGroups()
		{
				$counter = DB::table('feeds_movement_farrowing_group')->count();
				return $counter;
		}

		/**
		* load all farrowing groups
		*
		* @return Response
		*/
		public function loadFarrowingGroups()
		{
				$data = array();
				$group_id = Input::get('group_id');

				$farrow_groups = DB::table('feeds_movement_farrowing_group')
							->select('group_id','group_name','farm_id','unique_id')
							->get();

				if($group_id != ""){
					$farrow_groups = DB::table('feeds_movement_farrowing_group')
								->select('group_id','group_name','farm_id','unique_id')
								->where('group_id',$group_id)
								->get();
				}

				$farrow_data = $this->toArray($farrow_groups);

				foreach($farrow_data as $k => $v){
						$data[] = array(
							'group_id'		=>	$v['group_id'],
							'group_name'	=>	$v['group_name'],
							'farm_name'		=>	$this->farrowingDataFarm($v['group_id']),
							//'bins'			=>	$this->binsElementsBuilder($v['unique_id'])
						);
				}

				return $data;
		}

		/**
		* load all farrowing groups
		*
		* @return Response
		*/
		private function binsElementsBuilder($unique_id)
		{
				$data = $this->farrowingDataBin($unique_id);

				$bins = "";
				foreach($data as $k => $v){
						$bins .= '<div class="form-group">';
						$bins .= '<label for="" class="col-sm-4 control-label">Bin</label>';
						$bins .= '<div class="col-sm-5">';
						$bins .= '<input name="unique_id[]" type="hidden" class="bin_'.$v['unique_id'].'_'.$v['bin_id'].'" value="'.$v['unique_id'].'" />';
						$bins .= '<input name="bin[]" type="hidden" class="form-control readonly bins bin_'.$v['unique_id'].'_'.$v['bin_id'].'" value="'.$v['bin_id'].'" readonly>';
						$bins .= '<input name="" type="text" class="form-control readonly bin_'.$v['unique_id'].'_'.$v['bin_id'].'" value="'.$v['alias_label'].'" readonly>';
						$bins .= '</div>';
						$bins .= '<div class="col-sm-1">';
						$bins .= '<button type="button" class="btn btn-default glyphicon glyphicon-remove btn-danger" aria-label="Left Align" bin="bin_'.$v['unique_id'].'_'.$v['bin_id'].'" title="Remove this bin">';
						$bins .= '</button>';
						$bins .= '</div>';
						$bins .= '</div>';
				}

				return $bins;
		}

		/**
		* load farrowing group total number of pigs
		*
		* @return Response
		*/
		public function loadFarrowingGroupsPigs()
		{
				$group_id = Input::get('group_id');
				$group_number = Input::get('group_number');

				if($group_id == "none"){
						Cache::forget('selected_farrowing_group_'.$group_number);
						Cache::forget('selected_farrowing_group_pigs_'.$group_number);
						return 0;
				}

				$unique_id = DB::table('feeds_movement_farrowing_group')->where('group_id',$group_id)->select('unique_id')->first();
				$group_name = DB::table('feeds_movement_farrowing_group')->where('group_id',$group_id)->select('group_name')->first();
				$group_name = $this->groupNameNursery($group_name->group_name);

				$unique_id = $unique_id->unique_id;
				$number_of_pigs = DB::table('feeds_movement_farrowing_bins')->select('number_of_pigs')->where('unique_id',$unique_id)->sum('number_of_pigs');

				$this->saveSelectedFarrowingGroupPigs($group_number,$number_of_pigs);

				return array('number_of_pigs'	=>	$number_of_pigs, 'nursery_group_name' => $group_name);

		}


		/**
		* Create the auto-populated group name for nursery group
		*
		* @return Response
		*/
		private function groupNameNursery($group_name)
		{
				$group_name = substr($group_name,0,3).date('mdy');

				return strtoupper($group_name);
		}

		/**
		* Create the auto-populated group name for nursery group
		*
		* @return Response
		*/
		public function saveNursery()
		{
				$date_created = date("Y-m-d H:i:s",strtotime(Input::get('date_time')));
				$bins = Input::get('bins');
				$number_of_pigs = Input::get('num_of_pigs');
				$bins = $this->saveBins($bins,$number_of_pigs);
				$unique_id = Input::get("unique_id");

				$nursery_group = array(
					'group_name'		=>	Input::get('group_name'),
					'farm_id'			=>	Input::get('nursery'),
					'start_weight'	=>	Input::get('start_weight'),
					'end_weight'	=>	Input::get('end_weight'),
					'unique_id'			=>	$unique_id,
					'date_created'			=>	$date_created,
					'date_to_transfer'	=> date('Y-m-d',strtotime($date_created . "+40 days")),
					'date_transfered'	=>	NULL,
					'status'			=>	'pending',
					'user_id'			=>	Auth::id()
				);

				foreach($bins as $k => $v){
					$data_nursery_bins = array(
						'bin_id'			=>	$bins[$k]['bin_id'],
						'number_of_pigs'	=>	$bins[$k]['number_of_pigs'],
						'unique_id'			=>	$unique_id
					);
					$this->saveNurseryGroupBins($data_nursery_bins,Input::get('farrowing'));
				}

				$save = DB::table('feeds_movement_nursery_group')->insert($nursery_group);

				foreach($bins as $k => $v){
					$data_nursery_bins = array(
						'bin_id'			=>	$bins[$k]['bin_id'],
						'number_of_pigs'	=>	$bins[$k]['number_of_pigs']
					);
					$this->updateBinsHistoryNumberOfPigs($data_nursery_bins['bin_id'],$data_nursery_bins['number_of_pigs'],"create");
				}

				if($save == 1){
						return redirect('nursery');
				}

		}

		/**
		* Save nursery bins
		*
		* @return Response
		*/
		public function saveNurseryGroupBins($data,$farm_id)
		{
				DB::table('feeds_movement_nursery_bins')->insert($data);

				// update the bin history and bin tables
				DB::table('feeds_bins')->where('bin_id',$data['bin_id'])->update(['num_of_pigs' => $data['number_of_pigs']]);
		}

		/**
		* Get the fasrrowing group id
		*
		* @param  int  $id
		* @return Response
		*/
		private function getFarrowingId($unique_id)
		{
				$data = DB::table('feeds_movement_farrowing_group')
					->select('group_id')
					->where('unique_id',$unique_id)
					->first();

				return $data->group_id;
		}

		/**
		 * Check if the group name already exists in the table of nursery group
		 *
		 * @param  int  $id
		 * @return Response
		 */
		public function checkExistsNursery()
		{
				$data = "";
				$bins = Input::get('bins');
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);
				if($data > 1){
					return "duplicate bins";
				}

		    $exists = DB::table('feeds_movement_nursery_group')
					->select('group_name')
					->where('group_name',Input::get('group_name'))
					->take(1)
					->get();

				return $exists != NULL ? 1 : 0;

		}


		/**
		* remove pigs group nursery
		*
		* @return Response
		*/
		public function removeGroupNursery()
		{
				$nursery_id = Input::get('nursery_id');
				// remove data on deceased and treatment
				DB::table('feeds_deceased')->where('group_id',$nursery_id)->delete();
				DB::table('feeds_treatment')->where('group_id',$nursery_id)->delete();

				$unique_id = DB::table('feeds_movement_nursery_group')->select('unique_id')->where('group_id',$nursery_id)->first();

				$animal_bins = DB::table('feeds_movement_nursery_bins')->where('unique_id',$unique_id->unique_id)->get();

				if($animal_bins != NULL){
						foreach($animal_bins as $k => $v){
							DB::table('feeds_movement_nursery_bins')
							->where('id',$v->id)
							->delete();
							$this->removeNurseryPigsHistory($v->bin_id);
						}
				}
				$this->removeTransferData($nursery_id);

				DB::table('feeds_movement_nursery_group')->where('unique_id',$unique_id->unique_id)->delete();
				DB::table('feeds_movement_nursery_bins')->where('unique_id',$unique_id->unique_id)->delete();
		}

		/**
		* remove pigs on the bin history
		*
		* @return Response
		*/
		public function removeNurseryPigsHistory($bin_id)
		{
				$bin_history = BinsHistory::where('bin_id',$bin_id)->orderBy('history_id','desc')->first();

				if($bin_history != NULL){
						$total_pigs = DB::table('feeds_movement_nursery_bins')->where('bin_id',$bin_id)->sum('number_of_pigs');
						BinsHistory::where('history_id',$bin_history->history_id)->update(['num_of_pigs'=>$total_pigs]);
						Cache::forget('bins-'.$bin_id);
						$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

						return true;
				}

				$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

				return false;
		}


		/**
		* save pending selection
		*
		* @return Response
		*/
		public function savePendingSelection()
		{
				$data = array(
				'group_id'			=>	Input::get('group_id'),
				'number_of_pigs'	=>	Input::get('number_of_pigs'),
				'unique_id'			=>	Input::get('unique_id')
				);

				$exists = $this->checkExistsPendingRecord(Input::get('group_id'),Input::get('unique_id'));

				if($exists == 1){
						return "exists";
				}

				DB::table('feeds_movement_pending_selection')->insert($data);

				$pending_added_farrowing_groups = DB::table('feeds_movement_pending_selection')->where('unique_id',Input::get('unique_id'))->get();
				$pending_added_farrowing_groups = $this->toArray($pending_added_farrowing_groups);

				$pending_data = array();
				foreach($pending_added_farrowing_groups as $k => $v){
						$pending_data[] = array(
							'id'				=>	$v['id'],
							'group_id'			=>	$v['group_id'],
							'group_name'		=>	$this->getFarrowingGroupName($v['group_id']),
							'farm_name'			=>	$this->getFarrowingGroupFarms($v['group_id']),
							'number_of_pigs'	=>	$v['number_of_pigs']
						);
				}

				return view('movement.group.nursery.ajax.pendinglist',compact("pending_data"));

		}

		/**
		 * delete pending selection
		 *
		 * @return Response
		 */
		public function deletePendingSelection()
		{
				DB::table('feeds_movement_pending_selection')->where('id',Input::get('id'))->delete();
		}

		/**
		 * empty pending selection
		 *
		 * @return Response
		 */
		public function emptyPendingSelection()
		{
				DB::table('feeds_movement_pending_selection')->delete();
		}

		/**
		* check pending selection
		*
		* @return Response
		*/
		private function checkExistsPendingRecord($group_id,$unique_id)
		{
				$data = DB::table('feeds_movement_pending_selection')
					->where('group_id',$group_id)
					->where('unique_id',$unique_id)
					->get();

				$output = 1;

				if($data == NULL){
				$output = 0;
				}

				return $output;
		}

		/**
		 * Get the farrowing group names
		 *
		 * @return Response
		 */
		private function getFarrowingGroupName($group_id)
		{
				$data = DB::table('feeds_movement_farrowing_group')->select('group_name')->where('group_id',$group_id)->first();

				return $data->group_name;
		}

		/**
		* Get the farrowing group names
		*
		* @return Response
		*/
		private function getFarrowingGroupFarms($group_id)
		{
				$data = DB::table('feeds_movement_farrowing_group')->select('farm_id')->where('group_id',$group_id)->first();

				$farm = DB::table('feeds_farms')->select('name')->where('id',$data->farm_id)->first();

				return $farm->name;
		}


		/**
		* Display the finisher page
		*
		* @return Response
		*/
		public function finisherPage()
		{
				$finisher_data = DB::table('feeds_movement_finisher_group')
														->where('status','!=','removed')
														->orderBy('group_id','desc')
														->take(8)->get();
				$finisher_data = $this->toArray($finisher_data);
				$finisher_counter = DB::table('feeds_movement_finisher_group')->count();

				$data = array();
				foreach($finisher_data as $k => $v){

					if(!empty($this->finisherBinsData($v['unique_id']))){
						$data[] = array(
							'group_id'				=>	$v['group_id'],
							'group_name'			=>	$v['group_name'],
							'start_weight'		=>	$v['start_weight'],
							'end_weight'		=>	$v['end_weight'],
							'unique_id'				=>	$v['unique_id'],
							'date_created'		=>	$v['date_created'],
							'date_to_transfer'	=> (strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24),
							'date_transfered'	=>	$v['date_transfered'],
							'status'					=>	$v['status'],
							'user_id'					=>	$v['user_id'],
							'farm_id'					=>	$v['farm_id'],
							'total_pigs'			=>	$this->totalFinisherPigs($v['unique_id']),
							'farm_name'				=>	$this->farmData($v['farm_id']),
							'bin_data'				=>	$this->finisherBinsData($v['unique_id'])
						);
					}
				}

				$finisher_data = $data;
				return view('movement.group.finisher.list', compact("finisher_data","finisher_counter"));

		}

		/**
		* Display the finisher page
		*
		* @return Response
		*/
		public function finisherPageLoadMore()
		{
				$skip = Input::get('items');
				$finisher_data = DB::table('feeds_movement_finisher_group')
													->where('status','!=','removed')
													->orderBy('group_id','desc')
													->take(8)->skip($skip)->get();
				$finisher_data = $this->toArray($finisher_data);
				$finisher_counter = DB::table('feeds_movement_finisher_group')->count();

				$data = array();
				foreach($finisher_data as $k => $v){

					$data[] = array(
						'group_id'				=>	$v['group_id'],
						'group_name'			=>	$v['group_name'],
						'start_weight'		=>	$v['start_weight'],
						'end_weight'			=>	$v['end_weight'],
						'unique_id'				=>	$v['unique_id'],
						'date_created'		=>	$v['date_created'],
						'date_to_transfer'	=> (strtotime(date('Y-m-d',strtotime($v['date_to_transfer']))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24),
						'date_transfered'	=>	$v['date_transfered'],
						'status'					=>	$v['status'],
						'user_id'					=>	$v['user_id'],
						'farm_id'					=>	$v['farm_id'],
						'total_pigs'			=>	$this->totalFinisherPigs($v['unique_id']),
						'farm_name'				=>	$this->farmData($v['farm_id']),
						'bin_data'				=>	$this->finisherBinsData($v['unique_id'])
					);

				}

				$finisher_data = $data;
				//dd($finisher_data);
				return view('movement.group.finisher.ajax.loadmore', compact("finisher_data"));

		}



		/**
		* Get all the data of the combined farrowing group
		*
		* @return Response
		*/
		private function totalFinisherPigs($unique_id)
		{
				$pigs = DB::table('feeds_movement_finisher_bins')->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $pigs; // != NULL ? $pigs->number_of_pigs : 0;
		}

		/**
		* Get the bins data for the farrowing page
		*
		* @return Response
		*/
		private function finisherBinsData($unique_id)
		{
				$bins = DB::table('feeds_movement_finisher_bins')->where('unique_id',$unique_id)->orderBy('bin_id','asc')->get();
				$bins = $this->toArray($bins);

				if($bins == NULL){
					return NULL;
				}

				$data = array();
				foreach($bins as $k => $v){
					$data[] = array(
									'id'			=>	$v['id'],
									'alias_label' 	=> $this->binLabel($v['bin_id']),
									'bin_id'		=>	$v['bin_id'],
									'number_of_pigs'	=>	$v['number_of_pigs']
									);
				}

				return $data;
		}

		/**
		* Show the form for creating a new resource.
		*
		* @return Response
		*/
		public function finisherFarms()
		{
				$farms_data = Farms::select('id','name')->where('farm_type','finisher')->orderBy('name','desc')->get()->toArray();
				return $farms_data;
		}

		/**
		* nursery bins for create nursery group
		*
		* @return Response
		*/
		public function finisherBins()
		{
				$farm_id = Input::get('farm_id');
				$bins = Bins::select('bin_id',DB::raw("CONCAT(bin_number, ' - ',alias) AS bin_number"))->orderBy('bin_id')->where('farm_id',$farm_id)->get()->toArray();
				return $bins;
		}

		/**
		* Update nursery
		*
		* @return Response
		*/
		public function updateFinisher()
		{
				$date_created = date("Y-m-d",strtotime(Input::get('date_created')));
				$bins = Input::get('bins');
				$number_of_pigs = Input::get('number_of_pigs_group');
				$f_bins_id = Input::get('f_bins_id');

				$data = "";
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);

				if($data > 1){
						return "duplicate bins";
				}

				$data_bin = array();
				foreach($bins as $k=>$v){
					$values = str_replace("-".$k,"",$v['value']);
					if($values != "none"){
							$data_bin[] = array('bin_id'=>$v['value'],'number_of_pigs'=>$number_of_pigs[$k]['value']);
					}
				}

				$data = array(
				'group_name'		=>	Input::get('group_name'),
				'date_created'		=>	$date_created,
				'finisher_farm'		=> 	Input::get('finisher_farm'),
				'unique_id'			=>	Input::get('unique_id'),
				'bin_one'			=>	Input::get('bin_one'),
				'bin_two'			=>	Input::get('bin_two'),
				'number_of_pigs'	=>	Input::get('number_of_pigs')
				);

				$group_data = array(
				'group_name'		=>	$data['group_name'],
				'farm_id'			=>	$data['finisher_farm'],
				'start_weight'	=>	Input::get('start_weight'),
				'end_weight'	=>	Input::get('end_weight'),
				'date_created'		=>	$data['date_created'],
				'date_to_transfer'	=> date('Y-m-d',strtotime($date_created . "+40 days")),
				'date_transfered'	=>	"0000-00-00 00:00:00",
				'status'			=>	'entered',
				'user_id'			=>	Auth::id()
				);

				/*
				if farm is the same as the farm on the farrowing group update else, delete bins and insert new selected bins
				*/
				$farm = $this->checkFarmExistsFinisher($data['finisher_farm'],$data['unique_id']);
				if($farm == 0){
						$bins_to_delete = DB::table('feeds_movement_finisher_bins')->where('unique_id',$data['unique_id'])->get();
						if($bins_to_delete != NULL){
							foreach($bins_to_delete as $k => $v){
								Cache::forget('bins-'.$v->bin_id);
							}
						}
						// delete bins
						DB::table('feeds_movement_finisher_bins')->where('unique_id',$data['unique_id'])->delete();
						// insert bins
						foreach($data_bin as $k => $v){
							$this->insertBinFinisher($v['bin_id'],$data['unique_id'],$v['number_of_pigs']);
						}
				}else{
						// update bins
						foreach($data_bin as $k => $v){
	 						$this->updateBinFinisher($v['bin_id'],$data['unique_id'],$v['number_of_pigs'],$f_bins_id[$k]['value']);
	 					}
				}

				// update finisher group
				DB::table('feeds_movement_finisher_group')->where('unique_id',$data['unique_id'])->update($group_data);

				return "success";
		}

		/**
		* Fiinsher clear cache for previous selected bins
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function clearCachePreviousSelectedBinsFinisher($unique_id)
		{
				$bins = DB::table('feeds_movement_finisher_bins')->where('unique_id',$unique_id)->get();
				$bins = $this->toArray($bins);

				foreach($bins as $k => $v){
						Cache::forget('bins-'.$v['bin_id']);
				}
		}

		/**
		* Finisher farm data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkFarmExistsFinisher($farm_id,$unique_id)
		{
				$counter  = DB::table('feeds_movement_finisher_group')
					->where('unique_id',$unique_id)
					->where('farm_id',$farm_id)
					->where('status','!=','removed')
					->count();

				return $counter;
		}

		/**
		* Finisher bins data exists checker
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @return Response
		*/
		private function checkBinExistsFinisher($bin_id,$unique_id)
		{
				$count  = DB::table('feeds_movement_finisher_bins')
					->where('unique_id',$unique_id)
					->where('bin_id',$bin_id)
					->count();
				return $count;
		}

		/**
		* Update finisher bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function updateBinFinisher($bin_id,$unique_id,$pigs,$f_bin_id)
		{
				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs
				);

				DB::table('feeds_movement_finisher_bins')
				->where('id',$f_bin_id)
				->where('unique_id',$unique_id)
				->update($data);

				/* if the number of pigs = 0
				if($pigs == 0){
					DB::table('feeds_movement_finisher_bins')
					->where('id',$f_bin_id)
					->where('unique_id',$unique_id)
					->delete();
				}*/

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"update");
		}

		/**
		* Insert nursery bin
		*
		* @param  int  $bin_id
		* @param  string  $unique_id
		* @param  int  $pigs
		* @return Response
		*/
		private function insertBinFinisher($bin_id,$unique_id,$pigs)
		{
				$data = array(
				'bin_id'			=>	$bin_id,
				'number_of_pigs'	=>	$pigs,
				'unique_id'			=>	$unique_id
				);

				DB::table('feeds_movement_finisher_bins')->insert($data);

				$this->updateBinsHistoryNumberOfPigs($bin_id,$pigs,"create");
		}

		/**
		* Get all the data of the combined farrowing group
		*
		* @return Response
		*/
		private function finisherNurseryData($unique_id)
		{
				$nursery_farrow_data = DB::table('feeds_movement_finisher_nursery_group')
									->where('unique_id',$unique_id)
									->select(DB::raw('DISTINCT(nursery_group_id) as nursery_group_id'),'unique_id')
									->get();

				$nursery_farrow_data = $this->toArray($nursery_farrow_data);

				$nursery_farrow_data = $this->finisherNurseryDataBuild($nursery_farrow_data);

				return $nursery_farrow_data;
		}

		/**
		* build the nurseryFarrowingData
		*
		* @return Response
		*/
		private function finisherNurseryDataBuild($data)
		{
				$farrowing_groups = array();

				foreach($data as $k => $v){
						$farrowing_groups[] = array(
							'number_of_pigs'		=>	$this->numberOfPigsFinisher($v['nursery_group_id']),
							'unique_id'				=>	$v['unique_id'],
							'group_name'			=>	$this->nurseryGroupName($v['nursery_group_id']),
							'farm_name'				=>	$this->nurseryDataFarm($v['nursery_group_id'])
						);
				}

				return $farrowing_groups;
		}

		private function numberOfPigsFinisher($unique_id)
		{
				$data = DB::table('feeds_movement_finisher_nursery_group')->select('number_of_pigs')->where('unique_id',$unique_id)->sum('number_of_pigs');

				return $data;
		}

		/**
		* Get the farm related to the farrowing group
		*
		* @return Response
		*/
		private function nurseryGroupName($farrowing_group_id)
		{
				$data = DB::table('feeds_movement_nursery_group')->select('group_name')->where('nursery_id',$farrowing_group_id)->first();

				return $data != NULL ? $data->group_name : "";
		}

		/**
		* Get the farm related to the farrowing group
		*
		* @return Response
		*/
		private function nurseryDataFarm($farrowing_group_id)
		{
				$data_farrow = DB::table('feeds_movement_nursery_group')->select('farm_id')->where('nursery_id',$farrowing_group_id)->first();

				if($data_farrow == NULL){
					return NULL;
				}

				$data = DB::table('feeds_farms')->select('name')->where('id',$data_farrow->farm_id)->first();

				return $data->name;
		}


		/**
		* Display the finisher page
		*
		* @return Response
		*/
		public function createFinisher()
		{
				$unique_id = $this->generateUniqueID();
				$finisher = $this->getFinisherFarms();

				return view('movement.group.finisher.create',compact("unique_id","finisher"));
		}

		/**
		* load all finisher groups
		*
		* @return Response
		*/
		private function loadNurseryGroups()
		{
				$nursery_groups = DB::table('feeds_movement_nursery_group')->where('status','pending')->lists('nursery_id','group_name');
				return $nursery_groups;
		}

		/**
		* Get the farrowing group names
		*
		* @return Response
		*/
		private function getFinisherFarms()
		{
				$farm = DB::table('feeds_farms')->where('farm_type','finisher')->orderBy('name','asc')->lists('id','name');
				return $farm;
		}

		/**
		* save pending selection
		*
		* @return Response
		*/
		public function savePendingSelectionFinisher()
		{
				$data = array(
				'group_id'		=>	Input::get('group_id'),
				'number_of_pigs'	=>	Input::get('number_of_pigs'),
				'unique_id'			=>	Input::get('unique_id')
				);

				$exists = $this->checkExistsPendingRecord(Input::get('group_id'),Input::get('unique_id'));

				if($exists == 1){
						return "exists";
				}

				DB::table('feeds_movement_pending_selection')->insert($data);

				$pending_added_farrowing_groups = DB::table('feeds_movement_pending_selection')->where('unique_id',Input::get('unique_id'))->get();
				$pending_added_farrowing_groups = $this->toArray($pending_added_farrowing_groups);

				$pending_data = array();
				foreach($pending_added_farrowing_groups as $k => $v){
						$pending_data[] = array(
							'id'				=>	$v['id'],
							'group_id'			=>	$v['group_id'],
							'group_name'		=>	$this->getNurseryGroupName($v['group_id']),
							'farm_name'			=>	$this->getNurseryGroupFarms($v['group_id']),
							'number_of_pigs'	=>	$v['number_of_pigs']
						);
				}

				return view('movement.group.finisher.ajax.pendinglist',compact("pending_data"));

		}

		/**
		* remove pigs group nursery
		*
		* @return Response
		*/
		public function removeGroupFinisher()
		{
				$finisher_id = Input::get('finisher_id');

				// remove data on deceased and treatment
				DB::table('feeds_deceased')->where('group_id',$finisher_id)->delete();
				DB::table('feeds_treatment')->where('group_id',$finisher_id)->delete();

				$nursery_id = Input::get('nursery_id');

				$unique_id = DB::table('feeds_movement_finisher_group')->select('unique_id')->where('group_id',$finisher_id)->first();

				$animal_bins = DB::table('feeds_movement_finisher_bins')->where('unique_id',$unique_id->unique_id)->get();

				if($animal_bins != NULL){
						foreach($animal_bins as $k => $v){
							DB::table('feeds_movement_finisher_bins')
							->where('id',$v->id)
							->delete();
							$this->removeFinisherPigsHistory($v->bin_id);
						}
				}
				$this->removeTransferData($finisher_id);

				DB::table('feeds_movement_finisher_group')->where('unique_id',$unique_id->unique_id)->delete();
				DB::table('feeds_movement_finisher_bins')->where('unique_id',$unique_id->unique_id)->delete();

		}

		/**
		* remove pigs on the bin history for finisher
		*
		* @return Response
		*/
		public function removeFinisherPigsHistory($bin_id)
		{
				$bin_history = BinsHistory::where('bin_id',$bin_id)->orderBy('history_id','desc')->first();

				if($bin_history != NULL){
						$total_pigs = DB::table('feeds_movement_finisher_bins')->where('bin_id',$bin_id)->sum('number_of_pigs');
						BinsHistory::where('history_id',$bin_history->history_id)->update(['num_of_pigs'=>$total_pigs]);
						Cache::forget('bins-'.$bin_id);
						$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

						return true;
				}

				$this->updateBinsHistoryNumberOfPigs($bin_id,0,"remove");

				return false;
		}

		/**
		* Get the nursery group names
		*
		* @return Response
		*/
		private function getNurseryGroupName($group_id)
		{
				$data = DB::table('feeds_movement_nursery_group')->select('group_name')->where('nursery_id',$group_id)->first();
				return $data->group_name;
		}

		/**
		* Get the nursery group names
		*
		* @return Response
		*/
		private function getNurseryGroupFarms($group_id)
		{
				$data = DB::table('feeds_movement_nursery_group')->select('farm_id')->where('nursery_id',$group_id)->first();

				$farm = DB::table('feeds_farms')->select('name')->where('id',$data->farm_id)->first();

				return $farm->name;
		}

		/**
		 * Create the auto-populated group name for nursery group
		 *
		 * @return Response
		 */
		public function saveFinisher()
		{
					$date_created = date("Y-m-d H:i:s",strtotime(Input::get('date_time')));
					$bins = Input::get('bins');
					$number_of_pigs = Input::get('num_of_pigs');
					$bins = $this->saveBins($bins,$number_of_pigs);
					$unique_id = Input::get("unique_id");

					$finisher_group = array(
						'group_name'		=>	Input::get('group_name'),
						'start_weight'	=>	Input::get('start_weight'),
						'end_weight'	=>	Input::get('end_weight'),
						'farm_id'			=>	Input::get('nursery'),
						'unique_id'			=>	$unique_id,
						'date_created'		=>	$date_created,
						'date_to_transfer'	=> date('Y-m-d',strtotime($date_created . "+130 days")),
						'date_transfered'	=>	NULL,
						'status'			=>	'pending',
						'user_id'			=>	Auth::id()
					);

					foreach($bins as $k => $v){
						$data_finisher_bins = array(
							'bin_id'			=>	$bins[$k]['bin_id'],
							'number_of_pigs'	=>	$bins[$k]['number_of_pigs'],
							'unique_id'			=>	$unique_id
						);
						$this->saveFinisherGroupBins($data_finisher_bins,$finisher_group['farm_id']);
					}

					$save = DB::table('feeds_movement_finisher_group')->insert($finisher_group);

					foreach($bins as $k => $v){
						$data_finisher_bins = array(
							'bin_id'			=>	$bins[$k]['bin_id'],
							'number_of_pigs'	=>	$bins[$k]['number_of_pigs']
						);
						$this->updateBinsHistoryNumberOfPigs($data_finisher_bins['bin_id'],$data_finisher_bins['number_of_pigs'],"create");
					}

					if($save == 1){
									return redirect('finisher');
					}
		}

		/**
		* Save nursery bins
		*
		* @return Response
		*/
		public function saveFinisherGroupBins($data,$farm_id)
		{
				DB::table('feeds_movement_finisher_bins')->insert($data);
				// update the bin history and bin tables
				DB::table('feeds_bins')->where('bin_id',$data['bin_id'])->update(['num_of_pigs' => $data['number_of_pigs']]);
		}

		/**
		* Check if the group name already exists in the table of nursery group
		*
		* @param  int  $id
		* @return Response
		*/
		public function checkExistsFinisher()
		{
				$data = "";
				$bins = Input::get('bins');
				foreach($bins as $k => $v){
					$data .= $v['value'].",";
				}
				$data = substr(trim($data), 0, -1);
				$data = array_count_values(explode(",",$data));
				$data = max($data);
				if($data > 1){
					return "duplicate bins";
				}

				$exists = DB::table('feeds_movement_finisher_group')
						->select('group_name')
						->where('group_name',Input::get('group_name'))
						->take(1)
						->get();

				return $exists != NULL ? 1 : 0;
		}

		/*
		*	Update the bin history for update number of pigs
		*/
		public function updateBinsHistoryNumberOfPigs($bin_id,$number_of_pigs,$type)
		{
					$bininfo = $this->getBinDefaultInfo($bin_id);
					$lastupdate  = $this->getLastHistory($bininfo);

					// get the total number of pigs based on the animal group total number of pigs
					$total_number_of_pigs = $this->totalNumberOfPigsAnimalGroups($bin_id,$bininfo[0]->farm_id); //$number_of_pigs;

					if(!empty($lastupdate)){
								$update_date = date("Y-m-d",strtotime($lastupdate[0]->update_date));
								if($update_date == date("Y-m-d")){
											$variance = $lastupdate[0]->variance;
											$consumption = $lastupdate[0]->consumption;


											DB::table('feeds_bin_history')
											->where('bin_id', '=', $bin_id)
											->whereBetween('update_date', array(date("Y-m-d") . " 00:00:00", date("Y-m-d") . " 23:59:59"))
											->delete();
								}else{
											$variance = 0;
											$consumption = 0;
								}
					}

					$home_controller = new HomeController;
					$budgeted_amount = $home_controller->daysCounterbudgetedAmount($bininfo[0]->farm_id,$bin_id,$lastupdate[0]->feed_type,date("Y-m-d H:i:s"));
					unset($home_controller);

					$data = array(
							'update_date' => date("Y-m-d H:i:s"),
							'bin_id' => $bin_id,
							'farm_id' => $bininfo[0]->farm_id,
							'num_of_pigs' => $total_number_of_pigs,
							'user_id' => Auth::id(),
							'amount' => $lastupdate[0]->amount,
							'update_type' => 'Manual Update Number of Pigs, '.$type.' Animal Groups Admin',
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
							'budgeted_amount' => $budgeted_amount,//$lastupdate[0]->budgeted_amount,
							'budgeted_amount_tons' => $lastupdate[0]->budgeted_amount_tons,
							'actual_amount_tons' => $lastupdate[0]->actual_amount_tons,
							'remaining_amount' => $lastupdate[0]->remaining_amount,
							'sub_amount' => $lastupdate[0]->sub_amount,
							'variance' => $variance,
							'consumption' => $consumption,
							'admin' => 1,
							'medication' => !empty($lastupdate[0]->medication) ? $lastupdate[0]->medication : 0,
							'feed_type' => $lastupdate[0]->feed_type,
							'unique_id'	=> !empty($lastupdate[0]->unique_id) ? $lastupdate[0]->unique_id : "none"
						);

					BinsHistory::insert($data);

					$notification = new CloudMessaging;
					$farmer_data = array(
						'farm_id'		=> 	$bininfo[0]->farm_id,
						'bin_id'		=> 	$bin_id,
						'num_of_pigs'	=> 	$total_number_of_pigs
						);
					$notification->updatePigsMessaging($farmer_data);
					unset($notification);

					Cache::forget('bins-'.$bin_id);

		}

		private function totalNumberOfPigsAnimalGroups($bin_id,$farm_id){
				// check the farm type
				$type = $this->farmTypes($farm_id);
				$total_pigs = 0;

				if($type == 'farrowing'){

					$unique_id = $this->activeGroups('feeds_movement_farrowing_group');
					if($unique_id != NULL){
						$total_pigs = $this->farrowingTotalNumberOfPigs($bin_id,$unique_id);
					}

				} elseif ($type == 'nursery') {

					$unique_id = $this->activeGroups('feeds_movement_nursery_group');
					if($unique_id != NULL){
						$total_pigs = $this->nurseryTotalNumberOfPigs($bin_id,$unique_id);
					}

				} elseif ($type == 'finisher') {

					$unique_id = $this->activeGroups('feeds_movement_finisher_group');
					if($unique_id != NULL){
						$total_pigs = $this->finisherTotalNumberOfPigs($bin_id,$unique_id);
					}

				} else {
					return $total_pigs;
				}

				return $total_pigs != NULL ? $total_pigs : 0;
		}


		/**
		** Gets the active groups
		** string $group_table Primary key
		** return array
		**/
		private function activeGroups($group_table)
		{

			$active_groups = DB::table($group_table)
												->select('unique_id')
												->where('status','!=','removed')
												->get();
			$active_groups = $this->toArray($active_groups);

			if($active_groups != NULL){
				return $active_groups;
			}

			return $active_groups;
		}

		/**
		** Gets the Default Values of a certain Bin
		** int bin_id Primary key
		** return array Object 2-19-2016
		**/
		private function getBinDefaultInfo($bin_id)
		{
				$output = DB::table('feeds_bins')
							->select('bin_id','farm_id','num_of_pigs','amount', 'feed_type', 'bin_size')
							->where('bin_id','=',$bin_id)
							->get();
				return $output;
		}

		/**
		** Gets last values from Update History
		** bininfo array Object
		** return array Object 2-19-2016
		**/
		private  function getLastHistory($bininfo)
		{
				$output = DB::table('feeds_bin_history')
							->where('bin_id','=',$bininfo[0]->bin_id)
							->orderBy('update_date', 'DESC')
							->take(1)
							->get();

				if(count($output) == 0) {
					$output[0] =  (object)array(
						'num_of_pigs' => $bininfo[0]->num_of_pigs,
						'amount'=> 0,
						'budgeted_amount'=>$this->getBudgetedAmount($bininfo[0]->feed_type),
						'remaining_amount'=> 0,
						'sub_amount' => 0,
						'variance' => 0,
						'consumption' => 0
					);
				}

				return $output;
		}

		/**
		** Gets the budgeted amount of specific feed type
		** bininfo array Object
		** return array Object 2-19-2016
		**/
		private function getBudgetedAmount($feedtype) {
				$output = DB::table('feeds_feed_types')
							->select('budgeted_amount')
							->where('type_id','=',$feedtype)
							->get();

				return !empty($output[0]->budgeted_amount) ? $output[0]->budgeted_amount : 0;
		}

		/*
		*	farm types
		*/
		private function farmTypes($farm_id)
		{
				$type = Farms::where('id',$farm_id)->select('farm_type')->first();

				return $type != NULL ? $type->farm_type : NULL;
		}

		/*
		*	farrowingTotalNumberOfPigsAnimalGroup
		*	get the total number of pigs based on the animal groups bin
		*/
		private function farrowingTotalNumberOfPigs($bin_id,$unique_id)
		{
			$sum = DB::table('feeds_movement_farrowing_bins')
							->where('bin_id',$bin_id)
							->whereIn('unique_id',$unique_id)
							->sum('number_of_pigs');

			return $sum;
		}

		/*
		*	nurseryTotalNumberOfPigsAnimalGroup
		*	get the total number of pigs based on the animal groups bin
		*/
		private function nurseryTotalNumberOfPigs($bin_id,$unique_id)
		{
			$sum = DB::table('feeds_movement_nursery_bins')
							->where('bin_id',$bin_id)
							->whereIn('unique_id',$unique_id)
							->sum('number_of_pigs');

			return $sum;
		}

		/*
		*	finisherTotalNumberOfPigsAnimalGroup
		*	get the total number of pigs based on the animal groups bin
		*/
		private function finisherTotalNumberOfPigs($bin_id,$unique_id)
		{
			$sum = DB::table('feeds_movement_finisher_bins')
							->where('bin_id',$bin_id)
							->whereIn('unique_id',$unique_id)
							->sum('number_of_pigs');

			return $sum;
		}

		/*
		*	farrowingTransferDateUpdater
		*	update the date to transfer of the farrowing pigs
		*/
		public function farrowingTransferDateUpdater()
		{
				$groups = DB::table('feeds_movement_farrowing_group')->get();

				foreach($groups as $k => $v){
					$days = (strtotime(date('Y-m-d',strtotime($v->date_created . "+20 days"))) - strtotime($v->date_created)) / (60 * 60 * 24);
					$days_current = (strtotime(date('Y-m-d',strtotime($v->date_to_transfer))) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
					echo date('Y-m-d',strtotime($v->date_created)) . " - " . date('Y-m-d',strtotime($v->date_created . "+20 days")) . " = " . $days . " = " . $days_current . "<br/>";
					if($v->date_to_transfer == '0000-00-00'){
						DB::table('feeds_movement_farrowing_group')->where('group_id',$v->group_id)->update(['date_to_transfer' => date('Y-m-d',strtotime($v->date_created . "+20 days"))]);
					}
				}

		}

		/*
		*	nurseryTransferDateUpdater
		*	update the date to transfer of the nursery pigs
		*/
		public function nurseryTransferDateUpdater()
		{
				$groups = DB::table('feeds_movement_nursery_group')->get();

				foreach($groups as $k => $v){
					$days = (strtotime(date('Y-m-d',strtotime($v->date_created . "+40 days"))) - strtotime($v->date_created)) / (60 * 60 * 24);
					echo date('Y-m-d',strtotime($v->date_created)) . " - " . date('Y-m-d',strtotime($v->date_created . "+40 days")) . " = " . $days . "<br/>";
					if($v->date_to_transfer == '0000-00-00'){
						DB::table('feeds_movement_nursery_group')->where('group_id',$v->group_id)->update(['date_to_transfer' => date('Y-m-d',strtotime($v->date_created . "+40 days"))]);
					}
				}

		}

		/*
		*	finisherTransferDateUpdater
		*	update the date to transfer of the finisher pigs
		*/
		public function finisherTransferDateUpdater()
		{
				$groups = DB::table('feeds_movement_finisher_group')->get();

				foreach($groups as $k => $v){
					$days = (strtotime(date('Y-m-d',strtotime($v->date_created . "+130 days"))) - strtotime($v->date_created)) / (60 * 60 * 24);
					echo date('Y-m-d',strtotime($v->date_created)) . " - " . date('Y-m-d',strtotime($v->date_created . "+130 days")) . " = " . $days . "<br/>";
					if($v->date_to_transfer == '0000-00-00'){
						DB::table('feeds_movement_finisher_group')->where('group_id',$v->group_id)->update(['date_to_transfer' => date('Y-m-d',strtotime($v->date_created . "+130 days"))]);
					}
				}

		}

		/**
     * saveTransfer()
     *
     * @return Response
     */
    public function saveTransfer()
    {
				$type = $this->transferType(Input::get('group_type'));

        $data = array(
					'transfer_number'	=>	$this->transferIDGenerator(),
					'transfer_type'		=>	$type,
					'group_from'			=>	Input::get('group_from'),
					'group_to'				=>	Input::get('group_to'),
					'status'					=>	'created',
					'driver_id'				=>	Input::get('driver_id'),
					'date'						=> 	date("Y-m-d",strtotime(Input::get('date'))),
					'shipped'					=>	Input::get('pigs'),
					'initial_count'		=>	Input::get('pigs')
				);

					//$counter = $this->checkTransfer($data['transfer_type'],$data['group_from'],$data['group_to']);

				//if($counter == 0){
					DB::table('feeds_movement_transfer')->insert($data);
					$this->updateAnimalGroup($type,$data['group_from']);
					return 'success';
				//} else {
					//return 'transfer already created';
				//}

    }

		/**
     * trasnfer id generator()
     *
     * @return Response
     */
    private function transferIDGenerator()
    {
			$unique = 'trans-';//uniqid(rand())
			$dateToday = date('ymdhms');

			return $dateToday;
		}

		/**
     * updateTransfer()
     *
     * @return Response
     */
    public function updateTransfer()
    {
				$type = $this->transferType(Input::get('group_type'));
				$trasnfer_id	=	Input::get('transfer_id');
				$group_to_previous	=	Input::get('group_to_previous');
        $data = array(
					'transfer_type'			=>	$type,
					'group_from'				=>	Input::get('group_from'),
					'group_to'					=>	Input::get('group_to'),
					'status'						=>	'edited',
					'date'							=> 	date("Y-m-d",strtotime(Input::get('date'))),
					'shipped'						=>	Input::get('shipped'),
					'empty_weight'			=>	Input::get('empty_weight'),
					'ave_weight'				=>	Input::get('ave_weight'),
					'driver_id'					=>	Input::get('driver_id'),
					'full_weight'				=>	Input::get('full_weight'),
					'received'					=>	Input::get('received'),
					'dead'							=>	Input::get('dead'),
					'poor'							=>	Input::get('poor'),
					'farm_count'				=>	Input::get('farm_count'),
					'final_count'				=>	Input::get('final_count'),
					'notes'							=>	Input::get('notes')
				);

				$counter = $this->groupToChecker($data['group_from'],$data['group_to'],$group_to_previous,$data['transfer_type']);

				if($counter == 0){
					DB::table('feeds_movement_transfer')->where('transfer_id',$trasnfer_id)->update($data);
					$this->updateAnimalGroup($type,$data['group_from']);
					return 'success';
				} else {
					return 'transfer already created';
				}

    }

		/**
     * deleteTransfer()
     *
     * @return Response
     */
    public function deleteTransfer()
    {

			$data = Input::get('data');
			$group_type = $data['group_type'];
			$group_id = $data['group_from'];
			$table = "";
			if($group_type == 'farrowing'){
				$table = 'feeds_movement_farrowing_group';
				$transfer_type = 'farrowing_to_nursery';
			} else if($group_type == 'nursery'){
				$table = 'feeds_movement_nursery_group';
				$transfer_type = 'nursery_to_finisher';
			} else {
				$table = 'feeds_movement_finisher_group';
				$transfer_type = 'finisher_to_market';
			}

			$counter = DB::table('feeds_movement_transfer')->where('group_from',$group_id)->where('status','!=','finalized')->count();

			if($counter == 0){
				DB::table($table)->where('group_id',$group_id)->update(['status'=>'entered']);
			}

			DB::table('feeds_movement_transfer')->where('transfer_id',Input::get('transfer_id'))->delete();

		}

		/*
		*	groupToChecker()
		*
		* group to exists checker
		*/
		private function groupToChecker($group_from,$group_to,$group_to_previous,$type)
		{
			$output = 0;
			if($group_to != $group_to_previous){
				$output = DB::table('feeds_movement_transfer')->where('group_from',$group_from)->where('group_to',$group_to)->where('transfer_type',$type)->count();
			}
			return $output;
		}

		/**
     * updateAnimalGroup()
     *
     * @return Response
     */
    private function updateAnimalGroup($type,$group_id)
		{
			$table = "";
			if($type == 'farrowing_to_nursery'){
				$table = 'feeds_movement_farrowing_group';
			} else if($type == 'nursery_to_finisher'){
				$table = 'feeds_movement_nursery_group';
			} else if($type == 'finisher_to_market'){
				$table = 'feeds_movement_finisher_group';
			} else {
				return false;
			}
			DB::table($table)->where('group_id',$group_id)->update(['status'=>'created']);
		}

		/**
     * saveTransfer()
     *
     * @return Response
     */
    private function checkTransfer($transfer_type,$group_from,$group_to)
    {
			$counter = DB::table('feeds_movement_transfer')
									->where('transfer_type',$transfer_type)
									->where('group_from',$group_from)
									->where('group_to',$group_to)
									->count();
			return $counter;
		}

		/**
     * transferType()
     *
     * @return Response
     */
    private function transferType($type)
    {
			$final_type = "none";
			if($type == 'farrowing'){
				$final_type = 'farrowing_to_nursery';
			} else if($type == 'nursery'){
				$final_type = 'nursery_to_finisher';
			} else if($type == 'finisher'){
				$final_type = 'finisher_to_market';
			} else {
				$final_type = $final_type;
			}

			return $final_type;
		}

		/**
     * transferTypeChanger()
     *
     * @return Response
     */
    private function transferTypeChanger($type)
    {
			$final_type = "none";
			if($type == 'farrowing_to_nursery'){
				$final_type = 'farrowing';
			} else if($type == 'nursery_to_finisher'){
				$final_type = 'nursery';
			} else if($type == 'finisher_to_market'){
				$final_type = 'finisher';
			} else {
				$final_type = $final_type;
			}

			return $final_type;
		}


		/**
     * fetchTransfer()
     *
     * @return Response
     */
    public function fetchTransfer()
    {

			if(Input::get('transfer_data') != NULL){
				$data = Input::get('transfer_data');
				$group_from = $data['group_from'];
				$group_to = $data['group_to'];
				$type = $this->transferType($data['group_type']);
				$current_pigs = 0;
			} else {
				$group_from = Input::get('group_from');
				$group_to = Input::get('group_to');
				$type = $this->transferType(Input::get('group_type'));
				$current_pigs = Input::get('current_pigs');
			}
			$transfer_data = DB::table('feeds_movement_transfer')
												->where('transfer_type',$type)
												->where('group_from',$group_from)
												->where('status','!=','finalized')
												->get();

			// scheduled pigs for transfer
			$sched_pigs = $this->scheduledTransaferPigs($group_from);
			$sched_pigs = $sched_pigs == NULL ? 0 : $sched_pigs;
			// current pigs
			$current_pigs_and_bins = $this->currentPigsAndBinsAnimalGroup($group_from,$type);
			// available pigs for transfer
			$available_pigs = $current_pigs_and_bins['current_pigs'] - $sched_pigs;

			if($transfer_data == NULL){
				$this->resetGroupStatus($group_from,Input::get('group_type'));
				return $transfer_data = array(
									'sched_pig'	=>	$sched_pigs,
									'available_pigs'	=> $available_pigs,
									'current_pigs'	=>	$current_pigs_and_bins['current_pigs'],
									'transfer'	=>	'none',
									'group_from_bins_data'	=>	$current_pigs_and_bins['bins_data']
								);
			}

			$transfer_data = $this->toArray($transfer_data);
			$transfer_data = $this->buildTransferData($transfer_data,$type);

			$transfer_data = array(
				'sched_pig'	=>	$sched_pigs,
				'available_pigs'	=> $available_pigs,
				'current_pigs'	=>	$current_pigs_and_bins['current_pigs'],
				'transfer'	=>	$transfer_data,
				'group_from_bins_data'	=>	$current_pigs_and_bins['bins_data']
			);

			return $transfer_data;

		}




		/*
		*	currentPigsAnimalGroup($group_id,$type)
		*
		*	$group = int()
		*	$type = string()
		*	fetch the current pigs of the requested group
		*/
		private function currentPigsAndBinsAnimalGroup($group_id,$type)
		{
			$current_pigs_and_bins = array();
			if($type == 'farrowing_to_nursery'){
				// get the group unique_id
				$current_pigs_and_bins = $this->uniqueIDAnimalGroup($group_id,'feeds_movement_farrowing_group','feeds_movement_farrowing_bins');
			} else if($type == 'nursery_to_finisher'){
				$current_pigs_and_bins = $this->uniqueIDAnimalGroup($group_id,'feeds_movement_nursery_group','feeds_movement_nursery_bins');
			} else {
				$current_pigs_and_bins = $this->uniqueIDAnimalGroup($group_id,'feeds_movement_finisher_group','feeds_movement_finisher_bins');
			}

			return $current_pigs_and_bins;
		}

		/*
		*	uniqueIDAnimalGroup($group_id,$group_table,$group_bins_table)
		*
		*	$group_id = int()
		*	$group_bins = string()
		*	$group_table = string()
		*	fetch the current unique_id of the requested group and get the total pigs
		*/
		private function uniqueIDAnimalGroup($group_id,$group_table,$group_bins_table)
		{
			$data = DB::table($group_table)->select('unique_id')->where('group_id',$group_id)->first();

			$output = $this->pigsAndbinsAnimalGroup($data->unique_id,$group_bins_table);

			return $output;
		}

		/*
		*	pigsAnimalGroup($unique_id,$table)
		*
		*	$unique_id = string()
		*	$table = string()
		*	fetch the current pigs of the requested group
		*/
		private function pigsAndbinsAnimalGroup($unique_id,$table)
		{
			$output = array();

			$data = DB::table($table)->where('unique_id',$unique_id)->get();

			$bin_data = array();
			$current_pigs = 0;
			foreach($data as $v){
					$current_pigs = $current_pigs + $v->number_of_pigs;
					$bin_data[] = array(
						'name'	=>	$this->binsName($v->bin_id),
						'number_of_pigs'	=>	$v->number_of_pigs
					);
			}

			return array(
				'current_pigs'	=>	$current_pigs,
				'bins_data'			=>	$bin_data
			);
		}

		/*
		*	binsName($bin_id)
		*
		*	$bin_id = int()
		*	fetch the bins name from the bins table
		*/
		private function binsName($bin_id)
		{
			$bins_name = Bins::select('alias')->where('bin_id',$bin_id)->first();
			return $bins_name->alias;
		}

		/**
     * resetGroupStatus()
     *
     * @return Response
     */
    private function resetGroupStatus($group_id,$type)
    {
			$table = "";
			if($type == "farrowing"){
				$table = "feeds_movement_farrowing_group";
			} else if($type == "nursery") {
				$table = "feeds_movement_nursery_group";
			} else {
				$table = "feeds_movement_finisher_group";
			}
			DB::table($table)->where('group_id',$group_id)->update(['status'=>'entered']);
		}

		/**
     * buildTransferData()
     *
     * @return Response
     */
    public function buildTransferData($transfer_data,$type)
    {
			$data = array();

			foreach($transfer_data as $k => $v){
				$type = $type == NULL ? $v['transfer_type'] : $type;
				$farms = $this->animalGroupFarmName($v['group_from'],$v['group_to'],$type);

				$data[] = array(
					'transfer_id'	=>	$v['transfer_id'],
					'transfer_number'	=>	$v['transfer_number'],
					'transfer_type'	=>	$v['transfer_type'],
					'status'	=>	$v['status'],
					'date'	=>	date("M d, Y", strtotime($v['date'])),
					'group_id'	=>	$v['group_from'],
					'group_from'	=>	$v['group_from'],
					'group_to'	=> $v['group_to'],
					'group_from_farm'	=>	$farms['farm_from'],
					'group_to_farm'	=> $farms['farm_to'],
					'group_name_from'	=>	$farms['group_name_from'],
					'group_name_to'	=> $farms['group_name_to'] == NULL ? "-" : $farms['group_name_to'],
					'farm_id_from'	=>	$farms['farm_id_from'],
					'farm_id_to'	=> $farms['farm_id_to'],
					'empty_weight'	=>	$v['empty_weight'],
					'full_weight'	=>	$v['full_weight'],
					'ave_weight'	=> $v['ave_weight'],
					'shipped'	=>	$v['shipped'],
					'received'	=>	$v['received'],
					'dead'	=>	$v['dead'],
					'poor'	=>	$v['poor'],
					'initial_count'	=> $v['initial_count'],
					'farm_count'	=> $v['farm_count'],
					'final_count'	=> $v['final_count'],
					'notes'			=>	$v['notes'],
					'driver_id'		=>	$v['driver_id']
				);
			}

			return $data;
		}

		/**
     * animalGroupFarmName()
     *
     * @return Response
     */
    private function animalGroupFarmName($group_from,$group_to,$type)
    {
			$table_from = "";
			$table_to = "";
			if($type == 'farrowing_to_nursery' || $type == 'farrowing'){
				$table_from = 'feeds_movement_farrowing_group';
				$table_to = 'feeds_movement_nursery_group';
			} else if($type == 'nursery_to_finisher' || $type == 'nursery'){
				$table_from = 'feeds_movement_nursery_group';
				$table_to = 'feeds_movement_finisher_group';
			} else if($type == 'finisher_to_market' || $type == 'finisher'){
				$table_from = 'feeds_movement_finisher_group';
				$table_to = NULL;
				$farm_name_to = "market";
				$group_name_to = "";
				$farm_id_to = "";
			} else {
				return "none";
			}

			$group_from_data = DB::table($table_from)->where('group_id',$group_from)->first();
			$group_name_from = $group_from_data->group_name;
			$farm_id_from = $group_from_data->farm_id;
			$farm_name_from = Farms::where('id',$farm_id_from)->first();
			$farm_name_from = $farm_name_from->name;
			if($table_to != NULL){
				$group_to_data = DB::table($table_to)->where('group_id',$group_to)->first();
				$group_name_to = $group_to_data->group_name;
				$farm_id_to = $group_to_data->farm_id;
				$farm_name_to = Farms::where('id',$farm_id_to)->first();
				$farm_name_to = $farm_name_to->name;
			}

			return array(
				'farm_from'=>$farm_name_from,
				'farm_to'=>$farm_name_to,
				'group_name_from' => $group_name_from,
				'group_name_to'	=>	$group_name_to,
				'farm_id_from'	=>	$farm_id_from,
				'farm_id_to'		=>	$farm_id_to
			);
		}

		/**
     * animalGroupFarmName()
     *
     * @return Response
     */
		public function fetchFarmBinsTransfer()
		{

				$group_id_from = Input::get('group_id_from');
				$farm_id_from	= Input::get('farm_id_from');

				$group_id_to = Input::get('group_id_to');
				$farm_id_to	= Input::get('farm_id_to');

				$type = Input::get('transfer_type');

				$group_table_from = "";
				$bins_table_from = "";
				$group_table_to = "";
				$bins_table_to = "";

				if($type == 'farrowing_to_nursery'){

						$group_table_from = 'feeds_movement_farrowing_group';
						$bins_table_from = "feeds_movement_farrowing_bins";

						$group_table_to = 'feeds_movement_nursery_group';
						$bins_table_to = "feeds_movement_nursery_bins";

				} else if($type == 'nursery_to_finisher'){

						$group_table_from = 'feeds_movement_nursery_group';
						$bins_table_from = "feeds_movement_nursery_bins";

						$group_table_to = 'feeds_movement_finisher_group';
						$bins_table_to = "feeds_movement_finisher_bins";

				} else {
					$animal_group_from = DB::table('feeds_movement_finisher_group')->where('group_id',$group_id_from)->where('farm_id',$farm_id_from)->first();
					$bins_data_from = DB::table('feeds_movement_finisher_bins')->where('unique_id',$animal_group_from->unique_id)->get();
					$bins_data_from = $this->toArray($bins_data_from);
					$from = array();
					foreach($bins_data_from as $k=>$v){
						$bin = $this->getBinsTransfer($v['bin_id']);
						$from[] = array(
							'bin_id'		=>	$v['bin_id'],
							'unique_id'	=>	$v['unique_id'],
							'bin_label'	=>	$bin['alias'],
							'number_of_pigs'	=>	$v['number_of_pigs']
						);
					}
					return array(
						'from'	=>	$from,
						'to'		=>	NULL
					);
				}

				$animal_group_from = DB::table($group_table_from)->where('group_id',$group_id_from)->where('farm_id',$farm_id_from)->first();
				$animal_group_to = DB::table($group_table_to)->where('group_id',$group_id_to)->where('farm_id',$farm_id_to)->first();
				if($animal_group_from == NULL){
					return "none";
				}

				$bins_data_from = DB::table($bins_table_from)->where('unique_id',$animal_group_from->unique_id)->get();
				$bins_data_from = $this->toArray($bins_data_from);

				$bins_data_to = DB::table($bins_table_to)->where('unique_id',$animal_group_to->unique_id)->get();
				$bins_data_to = $this->toArray($bins_data_to);

				$from = array();
				foreach($bins_data_from as $k=>$v){
					$bin = $this->getBinsTransfer($v['bin_id']);
					$from[] = array(
						'bin_id'		=>	$v['bin_id'],
						'unique_id'	=>	$v['unique_id'],
						'bin_label'	=>	$bin['alias'],
						'number_of_pigs'	=>	$v['number_of_pigs']
					);
				}

				$to = array();
				foreach($bins_data_to as $k=>$v){
					$bin = $this->getBinsTransfer($v['bin_id']);
					$to[] = array(
						'bin_id'		=>	$v['bin_id'],
						'unique_id'	=>	$v['unique_id'],
						'bin_label'	=>	$bin['alias'],
						'number_of_pigs'	=>	$v['number_of_pigs']
					);
				}

				return array(
					'from'	=>	$from,
					'to'		=>	$to
				);

		}

		/*
		* fetch bins info
		*/
		private function getBinsTransfer($bin_id)
		{
			$bin = Bins::where('bin_id',$bin_id)->first()->toArray();
			return $bin;
		}

		/*
		*	finalizeTransfer()
		*
		* fetch bins info
		*/
		public function finalizeTransfer()
		{
			$transfer_data = Input::get('transfer_data');
			$transfer_id = $transfer_data['transfer_id'];
			$bins_from_pigs = Input::get('bins_from_pigs');
			$bins_from = Input::get('bins_from');
			$bins_to = Input::get('bins_to');
			$num_of_pigs = Input::get('num_of_pigs');
			$num_of_pigs_dead = Input::get('num_of_pigs_dead');
			$num_of_pigs_poor = Input::get('num_of_pigs_poor');

			$transfer = array(
				'transfer_type'		=>	$transfer_data['transfer_type'],
				'status'					=>	'finalized',
				'date'						=>	date('Y-m-d',strtotime($transfer_data['date'])),
				'group_from'			=>	$transfer_data['group_from'],
				'group_to'				=>	$transfer_data['group_to'],
				'empty_weight'		=>	$transfer_data['empty_weight'],
				'full_weight'			=>	$transfer_data['full_weight'],
				'ave_weight'			=>	$transfer_data['ave_weight'],
				'shipped'					=>	$transfer_data['shipped'],
				'received'				=>	$transfer_data['received'],
				'dead'						=>	$transfer_data['dead'],
				'poor'						=>	$transfer_data['poor'],
				'initial_count'		=>	$transfer_data['shipped'],
				'farm_count'			=>	$transfer_data['farm_count'],
				'final_count'			=>	$transfer_data['final_count'],
				'driver_id'				=>	$transfer_data['driver_id']
			);

			// update the 'feeds_movement_transfer'
			DB::table('feeds_movement_transfer')->where('transfer_id',$transfer_id)->update($transfer);

			$transfer_bins = array();
			foreach($bins_from as $k => $v){

				//if($bins_from_pigs[$k]['value'] != 0){
						$transfer_bins[] = array(
							'transfer_id'		=>	$transfer_id,
							'bin_id_from'		=>	$v['value'],
							'bin_id_to'			=>	$bins_to[$k]['value'],
							'number_of_pigs_transferred'	=>	$num_of_pigs[$k]['value'],
							'dead'					=>	$num_of_pigs_dead[$k]['value'],
							'poor'					=>	$num_of_pigs_poor[$k]['value'],
						);

						$transfer_bins_update = array(
							'transfer_id'		=>	$transfer_id,
							'bin_id_from'		=>	$v['value'],
							'bin_id_to'			=>	$bins_to[$k]['value'],
							'number_of_pigs_transferred'	=>	$num_of_pigs[$k]['value'],
							'dead'					=>	$num_of_pigs_dead[$k]['value'],
							'poor'					=>	$num_of_pigs_poor[$k]['value'],
						);

						$this->updateGroupsBinsPigs($transfer_bins_update,$v['name'],$transfer_data['transfer_type'],$transfer_data['group_from'],$transfer_data['group_to'],$num_of_pigs_poor[$k]['value']);
				//}

			}
			//dd($transfer_bins);
			// insert data on the 'feeds_movement_transfer_bins'
			if(DB::table('feeds_movement_transfer_bins')->insert($transfer_bins)){
				return redirect('animalmovement');
			}

			// notify the driver

		}

		/*
		*	updateGroupsBinsPigs()
		*
		* update the status of group and group bins number of pigs
		*/
		private function updateGroupsBinsPigs($transfer_bins,$unique_id,$transfer_type,$group_from_id,$group_to_id,$poor)
		{
			$group_from_unique_id = $unique_id;
			if($transfer_type == 'farrowing_to_nursery'){

				// get the number_of_pigs for the bins in group from
				$number_of_pigs_from = DB::table('feeds_movement_farrowing_bins')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->first();
				$decreased_pigs = $number_of_pigs_from->number_of_pigs - ($transfer_bins['number_of_pigs_transferred'] + $transfer_bins['dead'] + $poor); // + $transfer_bins['poor'];
				$decreased_pigs = $decreased_pigs < 0 ? 0 : $decreased_pigs;

				//update the feeds_movement_farrowing_bins for decreased transferred pigs
				DB::table('feeds_movement_farrowing_bins')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->update(['number_of_pigs'=>$decreased_pigs]);

				// remove empty pigs group
				$pigs_count = $this->groupPigsCounter('feeds_movement_farrowing_bins',$group_from_unique_id);

				$group_to = DB::table('feeds_movement_nursery_group')->select('unique_id')->where('group_id',$group_to_id)->first();
				$group_to_unique_id = $group_to->unique_id;

				// get the number_of_pigs for the bins in group to
				$number_of_pigs_to = DB::table('feeds_movement_nursery_bins')->where('bin_id',$transfer_bins['bin_id_to'])->where('unique_id',$group_to_unique_id)->first();
				$added_pigs = $number_of_pigs_to->number_of_pigs + $transfer_bins['number_of_pigs_transferred'];
				if($number_of_pigs_to->number_of_pigs == 0){
					$added_pigs = $transfer_bins['number_of_pigs_transferred'];
				}

				//update the feeds_movement_nursery_bins for added transferred pigs
				DB::table('feeds_movement_nursery_bins')->where('bin_id',$transfer_bins['bin_id_to'])->where('unique_id',$group_to_unique_id)->update(['number_of_pigs'=>$added_pigs]);

				$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_to'],$added_pigs,"update");

				// bins from status updater
				if($pigs_count == 0){
					$this->removeEmptyPigsGroups('feeds_movement_farrowing_group','feeds_movement_farrowing_bins',$group_from_unique_id);
					//$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_from'],$decreased_pigs,"remove");
				} else {
					$this->animalGroupStatusUpdateChecker($group_from_id,'feeds_movement_farrowing_group');
					//if($decreased_pigs != 0){
						$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_from'],$decreased_pigs,"update");
					//}
				}


			} else if($transfer_type == 'nursery_to_finisher'){

				// get the number_of_pigs for the bins in group from
				$number_of_pigs_from = DB::table('feeds_movement_nursery_bins')->select('number_of_pigs')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->first();
				$decreased_pigs = $number_of_pigs_from->number_of_pigs - ($transfer_bins['number_of_pigs_transferred'] + $transfer_bins['dead'] + $poor); // + $transfer_bins['poor'];
				$decreased_pigs = $decreased_pigs < 0 ? 0 : $decreased_pigs;

				//update the feeds_movement_farrowing_bins for decreased transferred pigs
				DB::table('feeds_movement_nursery_bins')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->update(['number_of_pigs'=>$decreased_pigs]);

				// remove empty pigs group
				$pigs_count = $this->groupPigsCounter('feeds_movement_nursery_bins',$group_from_unique_id);

				$group_to = DB::table('feeds_movement_finisher_group')->select('unique_id')->where('group_id',$group_to_id)->first();
				$group_to_unique_id = $group_to->unique_id;

				// get the number_of_pigs for the bins in group to
				$number_of_pigs_to = DB::table('feeds_movement_finisher_bins')->select('number_of_pigs')->where('bin_id',$transfer_bins['bin_id_to'])->where('unique_id',$group_to_unique_id)->orderBy('id','desc')->first();
				$added_pigs = $number_of_pigs_to->number_of_pigs + $transfer_bins['number_of_pigs_transferred'];
				if($number_of_pigs_to->number_of_pigs == 0){
					$added_pigs = $transfer_bins['number_of_pigs_transferred'];
				}

				//update the feeds_movement_finisher_bins for added transferred pigs
				DB::table('feeds_movement_finisher_bins')->where('bin_id',$transfer_bins['bin_id_to'])->where('unique_id',$group_to_unique_id)->update(['number_of_pigs'=>$added_pigs]);

				$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_to'],$added_pigs,"update");

				// bins from status updater
				if($pigs_count == 0){
					$this->removeEmptyPigsGroups('feeds_movement_nursery_group','feeds_movement_nursery_bins',$group_from_unique_id);
					//$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_from'],$decreased_pigs,"remove");
				} else {
					$this->animalGroupStatusUpdateChecker($group_from_id,'feeds_movement_nursery_group');
					//if($decreased_pigs != 0){
						$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_from'],$decreased_pigs,"update");
					//}
				}

			} else {

				// get the number_of_pigs for the bins in group from
				$number_of_pigs_from = DB::table('feeds_movement_finisher_bins')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->first();
				$decreased_pigs = $number_of_pigs_from->number_of_pigs - ($transfer_bins['number_of_pigs_transferred'] + $transfer_bins['dead']); // + $transfer_bins['poor'];
				//update the feeds_movement_finisher_bins for decreased transferred pigs
				DB::table('feeds_movement_finisher_bins')->where('bin_id',$transfer_bins['bin_id_from'])->where('unique_id',$group_from_unique_id)->update(['number_of_pigs'=>$decreased_pigs]);

				$this->updateBinsHistoryNumberOfPigs($transfer_bins['bin_id_from'],$decreased_pigs,"create");

				$this->animalGroupStatusUpdateChecker($group_from_id,'feeds_movement_finisher_group');

			}
		}

		/*
		* Pigs counter
		*	Get the sum of pigs from a sepeficif group
		*/
		private function groupPigsCounter($group_bins_table,$unique_id)
		{
			$pigs_count = DB::table($group_bins_table)
											->select('number_of_pigs')
											->where('unique_id',$unique_id)
											->sum('number_of_pigs');

			return $pigs_count;
		}

		/*
		*	Delete empty pigs
		* All empty animal group pigs will be deleted after transfer
		*/
		private function removeEmptyPigsGroups($group_table,$bins_table,$unique_id)
		{
			DB::table($group_table)->where('unique_id',$unique_id)->update(['status'=>'removed']);
			$bins = DB::table($bins_table)->where('unique_id',$unique_id)->get();
			foreach($bins as $k => $v){
				if($v != NULL){
					$this->updateBinsHistoryNumberOfPigs($v->bin_id,0,"remove");
				}
			}
		}

		/*
		*	animalGroupStatusUpdateChecker()
		*	check and update the animal group
		*/
		private function animalGroupStatusUpdateChecker($group_id,$table)
		{
			$counter = DB::table('feeds_movement_transfer')->where('group_from',$group_id)->where('status','!=','finalized')->count();
			if($counter == 0){
				DB::table($table)->where('group_id',$group_id)->update(['status'=>'entered']);
			}
		}

		/*
		*	updateGroupStatus()
		*
		* update the status of group
		*/
		private function updateGroupStatus($group_id,$status,$table)
		{
			DB::table($table)->where('group_id',$group_id)->update('status',$status);
		}

}
