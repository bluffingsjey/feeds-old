<?php

namespace App\Http\Controllers;

use App\BinsType;
use Illuminate\Http\Request;
use App\BinsCat;
use App\Http\Requests;
use App\Http\Requests\BintypeRequest;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class BinsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {	
		$feedTypes = DB::table('feeds_bin_types')
					->select('feeds_bin_types.*')
					->latest()
					->get();
		
        return view("feedtype.index", compact('feedTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
		$categories = Binscat::lists("name","id");
        return view("bintype.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(BintypeRequest $request)
    {
        $this->createBintype($request);
		
		flash()->overlay("Your bin type has been successfully created!", "H&H Farms");
		
		return redirect('binstype');
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
    public function edit(Binstype $binstype)
    {
		$categories = Binscat::lists('name', 'id');
        return view("bintype.edit", compact("binstype","categories"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Binstype $binstype, BintypeRequest $request)
    {
        $binstype->update($request->all());
		
		return redirect('binstype');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Binstype $binstype)
    {
        Binstype::findOrFail($binstype->type_id)->delete();
		
		flash()->overlay("Bins type has beed successfully deleted!", "H&H Farms");
		
		return redirect('binstype');
    }
	
	/**
	* Save a new farm.
	*
	* @param FarmsRequest	$request
	*/
	private function createBintype(BintypeRequest $request)
	{
		$binsType = Auth::user()->binstype()->create($request->all());
				
		return $binsType;
	}
	
}
