<?php

namespace App\Http\Controllers;

use App\Medication;
use Illuminate\Http\Request;
use App\Http\Requests\MedicationRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$medications = DB::table('feeds_medication')
					->select('feeds_medication.*')
					->get();
							
        return view('medication.index', compact("medications"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view("medication.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(MedicationRequest $request)
    {
        $this->createMedication($request);
		
		flash()->overlay("The Medication has been successfully created!", "H&H Farms");
		
		return redirect('medication');
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
    public function edit(Medication $medication)
    {
		return view("medication.edit", compact("medication"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Medication $medication, MedicationRequest $request)
    {
        $medication->update($request->all());
		
		return redirect('medication');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Medication $medication)
    {
        Medication::findOrFail($medication->med_id)->delete();
				
		flash()->overlay("Medication has beed successfully deleted!", "H&H Farms");
		
		return redirect('medication');
    }
	
	/**
	* Save a new medication.
	*
	* @param MedicationRequest	$request
	*/
	private function createMedication(MedicationRequest $request)
	{
		$medication = Auth::user()->medication()->create($request->all());
				
		return $medication;
	}
}
