<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Input;
use App\BinSize;
use App\Http\Requests\BinSizeRequest;

class BinSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $feedSizes = DB::table('feeds_bin_sizes')
					->select('feeds_bin_sizes.*')
					->get();
        $ctrl = new BinSizeController;

        return view("binsize.index", compact('feedSizes','ctrl'));
    }

    /**
     * tons amount extractor.
     *
     * @return Response
     */
    public function tonsAmountExtractor($size_id)
    {
      $home_controller = New HomeController;

      $sizes = $home_controller->getmyBinSize($size_id);
      end($sizes);
      $output = key($sizes);
      foreach($sizes as $k => $v){

      }
      return $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view("binsize.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(BinSizeRequest $binsize)
    {
        $this->createBinSize($binsize);

		flash()->overlay("Your bin size has been successfully created!", "H&H Farms");

		return redirect('binsize');
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
    public function edit(BinSize $binsizes)
    {
        return view('binsize.edit', compact('binsizes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(BinSize $binsize, BinSizeRequest $request)
    {
        //$binsize->update($request->all());
		$data = array(
			'name'	=>	$request->name,
			'ring'	=>	$request->ring
		);
		$bin_size = BinSize::where('size_id',$request->size_id)->update($data);

		flash()->overlay("Bins type has been successfully updated!", "H&H Farms");

		return redirect('binsize');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(BinSize $binsize)
    {
        BinSize::findOrFail($binsize->size_id)->delete();

		flash()->overlay("Bins type has beed successfully deleted!", "H&H Farms");

		return redirect('binsize');
    }

	/**
	* Save a new farm.
	*
	* @param FarmsRequest	$request
	*/
	public function createBinSize(BinSizeRequest $request)
	{

		$binsType = BinSize::create($request->all());

		return $binsType;
	}
}
