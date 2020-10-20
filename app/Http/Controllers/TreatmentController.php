<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use DB;
use Auth;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $DeceasedCtrl = new DeceasedController;
      $animal_groups = $DeceasedCtrl->animalGroups();
      unset($DeceasedCtrl);
      return view('treatment.index', compact("animal_groups"));
    }

    /**
     * Get all the treatment data
     *
     * @return Array
     */
    public function treatmentData()
    {
      $deceased = DB::table('feeds_treatment')->orderBy('id','desc')->get();
      $DeceasedCtrl = new DeceasedController;
      $deceased = $DeceasedCtrl->toArray($deceased);
      unset($DeceasedCtrl);
      return $this->treatmentDataBuilder($deceased);
    }

    /**
     * Treatment data builder
     *
     * @param array $data
     * @return Array
     */
    public function treatmentDataBuilder($data)
    {
      $DeceasedCtrl = new DeceasedController;
      $output = array();
      foreach($data as $k => $v){
        $output[] = array(
          'id'          =>  $v['id'],
          'group_name'	=>	$DeceasedCtrl->groupName($v['group_id'],$v['group_type']),
          'farm_name'		=>	$DeceasedCtrl->loadFarms($v['farm_id']),
          'bin_label'		=>	$DeceasedCtrl->binLabel($v['bin_id']),
          'created_at'	=>	date("M d, Y",strtotime($v['created_at'])),
          'pigs'				=>	$v['pigs'],
          'illness'			=>	$v['illness'],
          'drug_used'		=>	$v['drug_used'],
          'notes'       =>	$v['notes'],
          'user_id'     =>  $v['user_id']
        );
      }
      unset($DeceasedCtrl);
      return $output;
    }

    /**
     * save the treatment data
     *
     * @return boolean
     */
    public function saveTreatment()
    {

      $group_bin_table = Input::get('group_type');
      $unique_id = Input::get('unique_id');
      $bin_id = Input::get('bin_id');
      $deceased_pigs = Input::get('pigs');

      $pigs = DB::table($group_bin_table)->where('unique_id',$unique_id)->where('bin_id',$bin_id)->select('number_of_pigs')->first();

      $group_type = str_replace("feeds_movement_","",$group_bin_table);
      $group_type = str_replace("_bins","",$group_type);
      $data = array(
            'group_id'		=>	Input::get('group_id'),
            'group_type'	=>	$group_type,
            'farm_id'			=>	Input::get('farm_id'),
            'bin_id'			=>	$bin_id,
            'created_at'	=>	date("Y-m-d",strtotime(Input::get('created_at'))),
            'pigs'				=>	$deceased_pigs,
            'illness'			=>	Input::get('illness'),
            'drug_used'		=>	Input::get('drug_used'),
            'notes'       =>	Input::get('notes'),
            'user_id'     =>  Auth::id()
            );

      DB::table('feeds_treatment')->insert($data);
    }

    /**
     * Delete the deceased data
     *
     * @return boolean
     */
    public function removeTreatment()
    {
      $id = Input::get('id');
      DB::table('feeds_treatment')->where('id',$id)->delete();
    }
}
