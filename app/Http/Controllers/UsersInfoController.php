<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInfo;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Cache;
use Validator;

class UsersInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($userid)
    {
        return view("usersinfo.create", compact('userid'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
    public function editInfo($id)
    {
        $user_id = $id;
		
		$user_info = UserInfo::where('user_id',$id)->get()->toArray();
		
		$info = array(
			'first_name'		=>	!empty($user_info[0]['first_name']) ? $user_info[0]['first_name'] : NULL,
			'middle_name'		=>	!empty($user_info[0]['middle_name']) ? $user_info[0]['middle_name'] : NULL,
			'last_name'			=>	!empty($user_info[0]['last_name']) ? $user_info[0]['last_name'] : NULL,
			'contact_number'	=>	!empty($user_info[0]['contact_number']) ? $user_info[0]['contact_number'] : NULL,
		);
		
		return view('users.info', compact("info","user_id"));
    }
	
	/**
     * Save a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function save(Request $request)
    {
		Cache::forget('drivers');
		
		$validator = Validator::make($request->all(), [
			'first_name' 		=>  'required',
			'middle_name'		=>	'',
			'last_name'			=>	'required',
			'contact_number'	=>	''
		]);
		
		$user_id = Input::get('user_id');
		
		if ($validator->fails()) {
            return redirect('edituserinfo/'.$user_id)
                        ->withErrors($validator)
                        ->withInput();
        }
		
        $info = array(
			'first_name'		=>	Input::get('first_name'),
			'middle_name'		=>	Input::get('middle_name'),
			'last_name'			=>	Input::get('last_name'),
			'contact_number'	=>	Input::get('contact_number'),
			'user_id'			=>	$user_id
		);
		
		$user_info = UserInfo::where('user_id',$user_id)->get()->toArray();
		
		if(empty($user_info)){
			// insert	
			UserInfo::create($info);
			
			return redirect('users');
		}
		
		// update
		UserInfo::where('user_id',$user_id)
				->update($info);
		
		return redirect('users');
		
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
