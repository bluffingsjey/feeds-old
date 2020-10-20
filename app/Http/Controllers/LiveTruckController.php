<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Deliveries;
use App\User;
use DB;

class LiveTruckController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
    public function __construct()
    {
        $this->middleware('auth',['except' => ['']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveries_of_the_day = Deliveries::select('driver_id')
                                ->where(DB::raw('LEFT(delivery_date,10)'),date("Y-m-d"))
                                ->whereIn('status',[1,2])
                                ->where('delivery_label','active')
                                ->distinct()
                                ->get()
                                ->toArray();

        $drivers = array();

        foreach($deliveries_of_the_day as $k => $v){
          $driver_name = User::select('username')
                            ->where('id',$v['driver_id'])
                            ->first();
          $drivers[] = array(
            'driver_id'   =>  $v['driver_id'],
            'driver_name' =>  $driver_name->username
          );
        }

        return view('livetruck.index',compact("drivers"));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liveTrucksAPI()
    {
        $deliveries_of_the_day = Deliveries::select('driver_id')
                                ->where(DB::raw('LEFT(delivery_date,10)'),date("Y-m-d"))
                                ->whereIn('status',[1,2])
                                ->where('delivery_label','active')
                                ->distinct()
                                ->get()
                                ->toArray();

        $drivers = array();

        foreach($deliveries_of_the_day as $k => $v){
          $driver_name = User::select('username')
                            ->where('id',$v['driver_id'])
                            ->first();
          $drivers[] = array(
            'driver_id'   =>  $v['driver_id'],
            'driver_name' =>  $driver_name->username
          );
        }

        return $drivers;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
