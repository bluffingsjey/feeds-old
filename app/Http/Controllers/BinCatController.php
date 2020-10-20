<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BinsCat;
use App\Http\Requests;
use App\Http\Requests\BincatRequest;
use App\Http\Controllers\Controller;
use Auth;

class BinCatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {	
		$binsCats = BinsCat::latest()->get();
		
        return view("bincat.index", compact('binsCats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view("bincat.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(BincatRequest $request)
    {
        $this->createBinCategory($request);
		
		flash()->overlay("Your bins category has beed successfully created!", "H&H Farms");
		
		return redirect('binscat');
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
    public function edit(Binscat $binscat)
    {
         return view("bincat.edit", compact("binscat"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Binscat $binscat, BincatRequest $request)
    {
        $binscat->update($request->all());
		
		return redirect('binscat');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Binscat $binscat)
    {
       	Binscat::findOrFail($binscat->id)->delete();
		
		flash()->overlay("Bins category has beed successfully deleted!", "H&H Farms");
		
		return redirect('binscat');
    }
	
	/**
	* Save a new farm.
	*
	* @param FarmsRequest	$request
	*/
	private function createBinCategory(BincatRequest $request)
	{
		$binscat = Auth::user()->binscat()->create($request->all());
				
		return $binscat;
	}
}
