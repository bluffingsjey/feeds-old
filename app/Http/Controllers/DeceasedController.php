<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Farms;
use App\Bins;
use Input;
use Auth;

class DeceasedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $animal_groups = $this->animalGroups();
        return view('deceased.index', compact("animal_groups"));
    }

    /**
     * Get all the deceased data
     *
     * @return Array
     */
    public function deceasedData()
    {
      $deceased = DB::table('feeds_deceased')->orderBy('id','desc')->get();
      $deceased = $this->toArray($deceased);

      return $this->deceasedDataBuilder($deceased);
    }

    /**
     * Deceased data builder
     *
     * @param array $data
     * @return Array
     */
    public function deceasedDataBuilder($data)
    {
      $output = array();
      foreach($data as $k => $v){
        $output[] = array(
          'id'          =>  $v['id'],
          'group_name'	=>	$this->groupName($v['group_id'],$v['group_type']),
          'farm_name'		=>	$this->loadFarms($v['farm_id']),
          'bin_label'		=>	$this->binLabel($v['bin_id']),
          'created_at'	=>	date("M d, Y",strtotime($v['created_at'])),
          'pigs'				=>	$v['pigs'],
          'cause'				=>	$v['cause'],
          'notes'       =>	$v['notes'],
          'user_id'     =>  $v['user_id']
        );
      }
      return $output;
    }

    /**
		 * Get the animal group name
		 *
     * @param  int  $group_id
     * @param  string  $type
		 * @return Array
		 */
		public function groupName($group_id,$type)
		{
      $farrowing_table = "feeds_movement_farrowing_group";
      $nursery_table = "feeds_movement_nursery_group";
      $finisher_table = "feeds_movement_finisher_group";
      $group_name = array();
      if($type == "farrowing"){
        $group_name = DB::table($farrowing_table)->where('group_id',$group_id)->select('group_name')->first();
      } else if($type == "nursery"){
        $group_name = DB::table($nursery_table)->where('group_id',$group_id)->select('group_name')->first();
      } else {
        $group_name = DB::table($finisher_table)->where('group_id',$group_id)->select('group_name')->first();
      }

      return $group_name->group_name;
    }

    /**
     * Get all the active animal groups
     *
     * @return Array
     */
    public function animalGroups()
    {
        $farrowing = DB::table('feeds_movement_farrowing_group')
                      ->select('group_id','group_name','unique_id','farm_id',DB::raw("IF(group_id, 'feeds_movement_farrowing_bins', 'feeds_movement_farrowing_bins') as type"))
                      ->where('status','!=','removed')->get();
        $farrowing = $this->toArray($farrowing);

        $nursery = DB::table('feeds_movement_nursery_group')
                  ->select('group_id','group_name','unique_id','farm_id',DB::raw("IF(group_id, 'feeds_movement_nursery_bins', 'feeds_movement_nursery_bins') as type"))
                  ->where('status','!=','removed')->get();
        $nursery = $this->toArray($nursery);

        $finisher = DB::table('feeds_movement_finisher_group')
                  ->select('group_id','group_name','unique_id','farm_id',DB::raw("IF(group_id, 'feeds_movement_finisher_bins', 'feeds_movement_finisher_bins') as type"))
                  ->where('status','!=','removed')->get();
        $finisher = $this->toArray($finisher);

        $group = array_merge($farrowing, $nursery, $finisher);

        usort($group, function($a,$b){
          return strcmp($a['group_name'],$b['group_name']);
        });

        return $group;
    }

    /**
		 * Convert object to array
		 *
     * @param  array  $data
		 * @return Array
		 */
		public function toArray($data)
		{
				$resultArray = json_decode(json_encode($data), true);

				return $resultArray;
		}

    /**
     * load the group farms
     *
     * @return Array
     */
    public function loadFarms($farm_id=NULL)
    {
        if($farm_id == NULL){
          $farm_id = Input::get('farm_id');
        } else {
          $farm_id = $farm_id;
        }
        $farm = Farms::where('id',$farm_id)->select('name')->first();

        return $farm->name;
    }

    /**
     * load the group bins
     *
     * @return Array
     */
    public function loadBins()
    {
        $unique_id = Input::get('unique_id');
        $table = Input::get('table');
        $bins = DB::table($table)->where('unique_id',$unique_id)->get();
        $bins = $this->toArray($bins);
        $bins = $this->binsBuilder($bins);

        return $bins;
    }



    /**
     * Build the animal groups bins data to have a bin label
     *
     * @param  array  $bins
     * @return Array
     */
    private function binsBuilder($bins)
    {
        $data = array();
        foreach($bins as $k => $v){
          $data[] = array(
            'id'  =>  $v['id'],
            'bin_id'  =>  $v['bin_id'],
            'number_of_pigs'  =>  $v['number_of_pigs'],
            'unique_id' =>  $v['unique_id'],
            'bin_label' =>  $this->binLabel($v['bin_id'])
          );
        }

        return $data;
    }

    /**
     * get the bin label
     *
     * @param  int  $bin_id
     * @return string
     */
    public function binLabel($bin_id)
    {
       $bin = Bins::where('bin_id',$bin_id)->select('alias')->first();
       return $bin==NULL ? "No Bins" : $bin->alias;
    }

    /**
     * save the deceased data
     *
     * @return boolean
     */
    public function saveDeceased()
    {
      $group_bin_table = Input::get('group_type');
      $unique_id = Input::get('unique_id');
      $bin_id = Input::get('bin_id');
      $deceased_pigs = Input::get('pigs');

      $pigs = DB::table($group_bin_table)->where('unique_id',$unique_id)->where('bin_id',$bin_id)->select('number_of_pigs')->first();
      $this->insertDeceasedUpdates($group_bin_table,$unique_id,$bin_id,$deceased_pigs);

    }

    /**
     * insert the deceased data and update the animal groups number of pigs and bin history
     *
     * @param  string $group_bin_table
     * @param  string $unique_id
     * @param  int $bin_id
     * @param  int $deceased_pigs
     * @return int
     */
    private function insertDeceasedUpdates($group_bin_table,$unique_id,$bin_id,$deceased_pigs)
    {
      // process the animal groups pigs update and the bin history update
      $number_of_pigs = $this->updateAnimalGroupBinsPigs($group_bin_table,$unique_id,$bin_id,$deceased_pigs);
      $movement_crtl = new MovementController;
      $movement_crtl->updateBinsHistoryNumberOfPigs($bin_id,$number_of_pigs,"Update Deceased");
      unset($movement_crtl);

      $group_type = str_replace("feeds_movement_","",$group_bin_table);
      $group_type = str_replace("_bins","",$group_type);
      $data = array(
            'group_id'		=>	Input::get('group_id'),
            'group_type'	=>	$group_type,
            'farm_id'			=>	Input::get('farm_id'),
            'bin_id'			=>	$bin_id,
            'created_at'	=>	date("Y-m-d",strtotime(Input::get('created_at'))),
            'pigs'				=>	$deceased_pigs,
            'cause'				=>	Input::get('cause'),
            'notes'       =>	Input::get('notes'),
            'user_id'     =>  Auth::id()
            );

      DB::table('feeds_deceased')->insert($data);
    }

    /**
     * update the animal group bins number of pigs and return the value of updated number of pigs
     *
     * @param  string $group_bin_table
     * @param  string $unique_id
     * @param  int $deceased_pigs
     * @return int
     */
    private function updateAnimalGroupBinsPigs($group_bin_table,$unique_id,$bin_id,$deceased_pigs)
    {
      $pigs = DB::table($group_bin_table)->where('unique_id',$unique_id)->where('bin_id',$bin_id)->select('number_of_pigs')->first();
      $pigs_to_update = $pigs->number_of_pigs - $deceased_pigs;
      DB::table($group_bin_table)->where('unique_id',$unique_id)->where('bin_id',$bin_id)->update(['number_of_pigs'=>$pigs_to_update]);

      return $pigs_to_update;
    }

    /**
     * Delete the deceased data
     *
     * @return boolean
     */
    public function removeDeceased()
    {
      $id = Input::get('id');
      DB::table('feeds_deceased')->where('id',$id)->delete();
    }
}
