<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Bins;
use App\Farms;
use App\Http\Requests;
use App\Http\Requests\BinsRequest;
use App\Http\Controllers\Controller;
use Auth;
use Cache;

class BinsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$bins = DB::table('feeds_bins')
					->leftJoin('feeds_farms', 'feeds_bins.farm_id', '=','feeds_farms.id')
					->select('feeds_bins.*','feeds_farms.name')
					->orderBy('feeds_bins.bin_id','desc')
					->groupBy('feeds_bins.Bin_id')
					->get();	
				
        return view('bins.index', compact('bins'));
    }

    /**
     * Show the form for creating a new resource.
     *3
     * @return Response
     */
    public function create()
    {
		$farms = Farms::lists('name', 'id');
		
        return view("bins.create", compact("farms"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(BinsRequest $request)
    {
        $this->createBins($request);
		Cache::forget('bins_lists');
		
		flash()->overlay("Your bin has beed successfully created!", "H&H Farms");
		
		return redirect('farms');
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
    public function edit(Bins $bins)
    {	
		$farms = Farms::lists('name', 'id');
		
        return view("bins.edit", compact("bins","farms"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Bins $bins, BinsRequest $request)
    {
		Cache::forget('bins_lists');
		$bins->update($request->all());
		
		flash()->overlay("Your bin has beed successfully updated!", "H&H Farms");
		
		return redirect('bins');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Bins $bins)
    {
		Cache::forget('bins_lists');
        Bins::findOrFail($bins->bin_id)->delete();
		
		flash()->overlay("Your bin has beed successfully deleted!", "H&H Farms");
		
		return redirect('farms');
    }
	
	/** Save a new bin.
	*
	* @param FarmsRequest	$request
	*/
	private function createBins(BinsRequest $request)
	{
		$bins = Auth::user()->bins()->create($request->all());
		
		return $bins;
	}
}
