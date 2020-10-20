<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use DB;
use Validator;
use Input;
use Session;
use App\BinsHistory;
use App\FarmSchedule;
use App\Deliveries;
use Auth;
use App\User;
use Cache;

class MiscController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('auth',['except' => ['indexPost','index','testing','testSupport','serverTimes','forgotPassword','resetPasswordAdmin']]);
    }
    public function testing()
    {
      $data = FarmSchedule::where('farm_id', 97)
  							->where('bin_id',621)
  							->where('date_of_delivery','>',date('Y-m-d'))
  							->where('status',0)
  							->orderBy('date_of_delivery','desc')
  							->sum('amount');
      return $data;
      //$bins = BinsHistory::select('bin_id','num_of_pigs')->where('update_date','LIKE','2017-03-15%')->get()->toArray();
      //return $bins;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        if (isset($_GET['q'])){

    			$table = $_GET['t'];
    			$query = $_GET['q'];
    			$statement = array('select','insert','update','delete','describe','START');
          /*
    			foreach($statement as $key => $val){
    				$q = strpos($query,$val);
    				if($q !== false){
    					$statement = $val;
    				}
    			}*/

          $query_strip = substr($query, 0, 8);

          for($i=0;$i<count($statement);$i++){
            $q = strpos($query_strip,$statement[$i]);
            if($q !== false){
    					$statement = $statement[$i];
    				}
          }

          // detect if the statement is select or insert or update or delete
    			switch($statement){

    				case 'select':
    					// Select
    					$output = DB::select($query);
    				break;

    				case 'insert':
    					// insert
    					$output = DB::insert($query);
    				break;

    				case 'update':
    					// update
    					$output = DB::update($query);
    				break;

    				case 'delete':
    					// delete
    					$output = DB::delete($query);
    				break;

    				case 'describe';
    					//describe
    					$output = DB::statement($query);

            case 'START';
                //describe
                $query = str_replace("START TRANSACTION; ", "", $query);
                $query = str_replace(" commit;", "", $query);
                $query = explode(";",$query);
                //dd($query);
                foreach($query as $k => $v){
                  if(strstr($v,"update")){
                    echo "update<br/>";
                    DB::update($v.";");
                  } else if( strstr($v,"delete") ){
                    echo "delete<br/>";
                    DB::delete($v.";");
                  } else if( strstr($v,"insert") ){
                    echo "insert<br/>";
                    DB::insert($v.";");
                  } else {
                    echo "none";
                  }
                }
                $output = "success";
    				break;

    			}

  			$output = array(
  				$table => $output
  			);

  			return $output;

		  }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function loginUser()
    {
        return Auth::user()->id;
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
    public function sessionLogin()
    {
        if ( Session::getToken() != Input::get('_token')) {
			return Redirect::to('/auth/login')->with('warning', 'Your session has expired. Please try logging in again.');
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
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
    public function indexPost()
    {

      if (isset($_POST['q'])){

          $table = $_POST['t'];
          $query = $_POST['q'];
          $statement = array('select','insert','update','delete','describe','START');

          foreach($statement as $key => $val){
            $q = strpos($query,$val);
            if($q !== false){
              $statement =  $val;
              //echo $val;
            }
          }

          // detect if the statement is select or insert or update or delete
          switch($statement){

            case 'select':
              // Select
              $output = DB::select($query);
            break;

            case 'insert':
              // insert
              $output = DB::insert($query);
            break;

            case 'update':
              // update
              $output = DB::update($query);
            break;

            case 'delete':
              // delete
              $output = DB::delete($query);
            break;

            case 'describe';
              //describe
              $output = DB::statement($query);

            case 'START';
                //transaction
                DB::beginTransaction();

                $output = DB::commit();

            break;

          }

        $output = array(
          $table => $output
        );

        return $output;

      }
    }

    public function testSupport()
    {
      dd(Input::all());
    }

    public function serverTimes()
    {
      return date("Y-m-d H:i:s");
    }

    public function forgotPassword()
    {
      return view('users.forgot');
    }

    /**
     * Forgot password
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function resetPasswordAdmin()
    {

      $data = array(
				'username' => Input::get('username'),
				'password'	=> Input::get('password'),
        'password_confirmation' =>  Input::get('password_confirmation')
			);

			$validation = Validator::make($data, [
          'username' => 'required|max:255|exists:feeds_user_accounts,username,type_id,0',
          'password' => 'required|confirmed|min:6',
      ]);

			if($validation->fails()){
				return redirect('forgotpw')
								->withErrors($validation)
								->withInput();
			}

			Cache::forget('drivers');

      User::where('username',$data['username'])->update([
				'password'	=>	bcrypt($data['password']),
				'no_hash'	=>	$data['password']
			]);

				return redirect('auth/login');

    }


    /**
     * Release Notes Maintenance Page
     *
     */
    public function releaseNotes()
    {
      return view( "partials.notes");
    }

    /**
     * Release Notes Maintenance Page
     *
     */
    public function getReleaseNotes()
    {
      $release_notes = DB::table('feeds_release_notes')->orderBy('id','desc')->first();

      if($release_notes != NULL){
        $user = DB::table('feeds_release_notes_entries')
                ->where('release_notes_id',$release_notes->id)
                ->where('user_id',Auth::id())
                ->first();
        if($user == NULL){
          $data = array(
            'id'  => $release_notes->id,
            'description' => $release_notes->description
          );
          return $data;
        } else {
          return NULL;
        }

      }

      return NULL;

    }

    /**
     * Save Release Notes
     *
     */
    public function saveReleaseNotes()
    {
      $date = date("Y-m-d H:i:s");
      $data = array(
        'created_date'  =>  $date,
        'description'   =>  Input::get('description')
      );

      if(DB::table('feeds_release_notes')->insert($data)){
        return "success";
      } else {
        return "failed";
      }
    }

    /**
     * Update Release Notes
     *
     */
    public function updateReleaseNotes()
    {
      $data = array(
        'release_notes_id'  =>  Input::get('release_notes_id'),
        'user_id'           =>  Auth::id()
      );
      DB::table('feeds_release_notes_entries')->insert($data);
    }

    /**
     * Get latest release notes for api
     *
     */
    public function apiGetReleaseNotes()
    {
      $release_notes = DB::table('feeds_release_notes')->orderBy('id','desc')->first();

      if($release_notes != NULL){
        $user = DB::table('feeds_release_notes_entries')
                ->where('release_notes_id',$release_notes->id)
                ->where('user_id',1)
                ->first();
        if($user == NULL){
          $data = array(
            'id'  => $release_notes->id,
            'description' => $release_notes->description
          );
          return $data;
        } else {
          return NULL;
        }

      }

      return NULL;

    }

    /**
     * API for Save Release Notes
     *
     */
    public function apiSaveReleaseNotes($description)
    {
      $date = date("Y-m-d H:i:s");
      $data = array(
        'created_date'  =>  $date,
        'description'   =>  Input::get('description')
      );

      if(DB::table('feeds_release_notes')->insert($data)){
        return "success";
      } else {
        return "failed";
      }
    }

    /**
     * API for Update Release Notes
     *
     */
    public function apiUpdateReleaseNotes($rn_id,$user_id)
    {
      $data = array(
        'release_notes_id'  =>  $rn_id,
        'user_id'           =>  $user_id
      );
      DB::table('feeds_release_notes_entries')->insert($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function apiLMDriver()
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
          $live_truck = $this->getLivetruckData($v['driver_id']);
          $drivers[] = array(
            'driver_id'   =>  $v['driver_id'],
            'driver_name' =>  $driver_name->username,
            'lat'         =>  $live_truck['lat'],
            'long'        =>  $live_truck['long']
          );
        }

        return $drivers;
    }

    private function getLivetruckData($driver_id)
    {
      $output = array(
        'lat'         =>  42.113332,
        'long'        =>  -85.567900
      );
      $live_truck = DB::table('feeds_live_truck')->where('driver_id',$driver_id)
                                                ->select('lat','lng')
                                                ->first();
      if($live_truck != NULL){
        $output = array(
          'lat'         =>  $live_truck->lat,
          'long'        =>  $live_truck->lng
        );
      }

      return $output;
    }

}
