<?php

namespace App\Http\Controllers;

use DB;
use Request;
use Input;
use App\Truck;
use App\Compartments;
use App\User;
use App\Http\Requests;
use App\Http\Requests\TruckRequest;
use App\Http\Controllers\Controller;
use Auth;



class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$trucks = DB::table('feeds_truck')
					->leftJoin('feeds_truck_compartment',function($join){
							$join->on('feeds_truck.truck_id', '=','feeds_truck_compartment.truck_id');
						})
					->select('feeds_truck.*',DB::raw('COUNT(feeds_truck_compartment.compartment_id) as compartment'),
							DB::raw('SUM(feeds_truck_compartment.capacity) as comcapacity'))
					->orderBy('feeds_truck.truck_id','DESC')
					->groupBy('feeds_truck.truck_id')
					->get();					
        return view("truck.index",compact("trucks"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
		$capacity = $this->capacity();
     	return view("truck.create", compact("capacity"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(TruckRequest $request)
    {
		$this->createTruck($request);
		
		flash()->overlay("Your truck has beed successfully created!", "H&H Farms");
		
		return redirect('truck');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Truck $truck)
    {        
		$capacity = $this->capacity();
        return view('truck.edit', compact('truck','capacity'));	
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Truck $truck, TruckRequest $request)
    {
		$capacity = $this->capacityConverter($request->capacity);
		DB::update('update feeds_truck set name = "'.$request->name.'", capacity = "'.$capacity.'"  where truck_id = '.$truck->truck_id.'');	
		
		return redirect('truck');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($truck)
    {
        Truck::findOrFail($truck)->delete();
		
		flash()->overlay("Your truck has been successfully deleted!", "H&H Farms");
		
		return redirect('truck');
    }
	
	/**
	* Save a new farm.
	*
	* @param FarmsRequest	$request
	*/
	private function createTruck(TruckRequest $request)
	{
		$truck = Auth::user()->truck()->create($request->all());
				
		return $truck;
	}
	
	/*
	*	Add Compartment
	*/
	public function addCompartment($id)
	{
		$truck_id = $id;
		return view('truck.addcom', compact('truck_id'));
	}
	
	/*
	*	Add Compartment Capacity
	*/
	public function addComCapacity()
	{		
		$truck_id = Request::get('truck_id');
		$trucks = Truck::findOrFail($truck_id);
		
		$totalTruckComCapacity = $this->compartmentTotalCap($truck_id);
		
		$capacity = ($totalTruckComCapacity == 0 ) ? $trucks->capacity : $totalTruckComCapacity;
		$capacity = $trucks->capacity - $totalTruckComCapacity;
					
		$capacity_list = $this->capacity();
		
		$compartments = Request::get('compartments');
		
		return view("truck.addcap",compact("compartments","capacity_list","capacity","truck_id"));
	}
	
	/*
	*	Compartment Query
	*/
	private function compartmentTotalCap($truck_id){
		$query = DB::raw("(
						SELECT capacity 
						FROM feeds_truck_compartment
						WHERE truck_id = ".$truck_id."
						) AS subquery");
						
		$compartments_capacity = DB::table($query)
								->select("SELECT capacity")
								->sum('capacity');
		return $compartments_capacity;						
	}
	
	/*
	*	Save Compartments
	*/
	public function storeCompartments() {
		if(Request::ajax()) {
			$compartmentTotal = Input::get('compartmentTotal');
			$truckId = Input::get('truck_id');
			$truck = Truck::findOrFail($truckId);
			$truck_capacity = floatval(Input::get('truck_capacity'));
			$userId = Auth::user()->id;
			$data = array();
			$comCapTotal = 0;	
			for($i = 1; $i <= $compartmentTotal; $i++){
				$compartmentNumber = "compartment_".$i;
				$data[] = array(
								'compartment_number'	=>	$i,
								'capacity'				=>	Input::get($compartmentNumber),
								'truck_id'				=>	$truckId,
								'user_id'				=>	$userId,
								'created_at'			=>	date('Y-m-d H:i:s'),
								'updated_at'			=>	date('Y-m-d H:i:s')
								);
				$comCapTotal = floatval($comCapTotal) + floatval($this->capacityConverter(Input::get($compartmentNumber)));
				$empty = $this->emptyChecker(Input::get($compartmentNumber));
			}
			
			$validate = $this->compartmentValidator($truck_capacity,$comCapTotal);
			if($validate == true) {
				if($empty == true){
					$output = array(
						'result'	=>	'Fail',
						'value'		=>	$comCapTotal,
						'message'	=>	"Please don't leave other compartments with a zero(0) tons"
					);
				} else {
					Compartments::insert($data);
					flash()->overlay("The truck compartments has been successfully added!", "H&H Farms");	
					$output = array(
						'result'	=>	'Pass',
						'url'		=>	"http://feeds.carrierinsite.com/truck"
					);
				}
				return $output;
			} else {
				$fail = array(
					'result' 	=> 'Fail',
					'value'		=>	$comCapTotal,
					'message'	=>	"The total compartments capacity should be equal to the truck capacity."
				);
				return $fail;	
			}
		}
	}
	
	/*
	 * Compartment empty checker
	 *
	 * @param  float  $truckCapacity,$compartmentCapacity 
     * @return Boolean	
	 */
	private function emptyChecker($compartment)
	{
		$compartment = $this->capacityConverter($compartment);
		if($compartment == 0){
			return true;
		}
	}
	
	/*
	 * Compartment Validator
	 *
	 * @param  float  $truckCapacity,$compartmentCapacity 
     * @return Boolean	
	 */
	private function compartmentValidator($truckCapacity,$compartmentCapacity)
	{
		if($compartmentCapacity == $truckCapacity) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	*	Amount of the truck capacity
	*/
	private function capacity()
	{
		$data = array();
		for($i=1;$i<=50;$i+=0.25){
			$amount = strval($i) . "Tons";
			if($i == 1){
				$data[$amount] = $i . " Ton";
			} else {
				$data[$amount] = $i . " Tons";
			}
		}
		return array($data);
	}
	
	/*
	*	Consumption Stripper
	*/
	private function capacityConverter($string)
	{
		return trim($string,"Tons");
	}
	
	/*
	*	Compartments
	*/
	public function viewCompartments($truck_id)
	{
		$trucks = Truck::findOrFail($truck_id);
		$compartments = Compartments::where('truck_id','=', $truck_id)->get();
		return view('truck.viewcompartments', compact("trucks","compartments"));
	}
	
	
	/*
	*	edit Compartment 
	*/
	public function editCompartment($id)
	{
		$compartment = Compartments::findOrFail($id);
		$truck = Truck::findOrFail($compartment->truck_id);
		$capacity = $this->capacity();
		
		$compartments_capacity = $this->comTotalCapEdit($truck->truck_id,$id);
		
		return view("truck.editcompartments", compact("capacity","truck","compartment","compartments_capacity"));
	}
	
	/*
	*	Compartment Query
	*/
	private function comTotalCapEdit($truck_id,$com_Id){
		$query = DB::raw("(
						SELECT capacity 
						FROM feeds_truck_compartment
						WHERE truck_id = ".$truck_id." AND compartment_id != ".$com_Id."
						) AS subquery");
						
		$compartments_capacity = DB::table($query)
								->select("SELECT capacity")
								->sum('capacity');
		return $compartments_capacity;						
	}
		
	public function updateCompartment()
	{
		$data = Input::all();
		$compartments = Compartments::find($data['compartment_id']);
		$compartments->capacity = $data['capacity'];
		$compartments->save();
		
		flash()->overlay("Truck compartment has been successfully updated!", "H&H Farms");
		
		return redirect('trucks/compartments/'.$data['truck_id']);
	}
	
	/**
     * Remove the specific compartment.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroyCompartment($compartment_id)
    {
        $compartments = Compartments::findOrFail($compartment_id);
		
		$truck_id = Truck::findOrFail($compartments->truck_id)->truck_id;
		
		$compartments->delete();
		
		flash()->overlay("The truck compartment has been successfully deleted!", "H&H Farms");
		
		return redirect('trucks/compartments/'.$truck_id);
    }
	
	/*
	 *	Remove all compartments ona  truck
	 *
	 *	@apram int $id
	 $ @return Response
	 */
	public function batchDelete($truck_id)
	{
		$deleteRows = Compartments::where('truck_id', $truck_id)->delete();
		
		flash()->overlay("The truck compartment has been successfully deleted!", "H&H Farms");
		
		return redirect('trucks/compartments/'.$truck_id);
	}
	
	
}