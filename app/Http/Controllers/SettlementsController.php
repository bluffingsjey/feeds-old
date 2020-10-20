<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Smalot\PdfParser\Parser;
use DB;
use Input;

class SettlementsController extends Controller
{
		/**
		 * Create a new controller instance.
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->middleware('auth');
		}

	  /**
	   * Display a listing of the resource.
	   *
	   * @return Response
	   */
	  public function index()
	  {
	      return view('settlements.index');
	  }

		/**
	   * settlements search.
	   *
	   * @param  int  $id
	   * @return Response
	   */
	  public function settlementSearch()
	  {
				$farm_option_1 = Input::get('farm_vr_1');
				$farm_option_2 = Input::get('farm_vr_2');
				$group_option_1	= Input::get('group_vr_1');
				$group_option_2 = Input::get('group_vr_2');
				$begin_date_1 = Input::get('begin_date_1');
				$begin_date_2 = Input::get('begin_date_2');
				$end_date_1 = Input::get('end_date_1');
				$end_date_2 = Input::get('end_date_2');
		}

		/**
	   * settlements process.
	   *
	   * @param  int  $id
	   * @return Response
	   */
	  public function processSettlements()
	  {

			$data = array();
			$error = false;
			$files = array();


			$uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/settlements/';

			if(move_uploaded_file($_FILES[0]['tmp_name'], $uploaddir . basename($_FILES[0]['name']) )) {

				$files[] = $uploaddir .$_FILES[0]['name'];

				$parser = new Parser;

				$pdf = $parser->parseFile($uploaddir .$_FILES[0]['name']);

				$text = $pdf->getText();

				$group_number = Input::get(1);

				$unique_id = "set-" . uniqid(rand()) . date('ymdhms');

				$data = $this->settlementsMain($text,$unique_id,$group_number,$files);

				$settlement_exists = DB::table('feeds_settlement_main')->where('settlement_number','=',$data['settlement_number'])->count();

				if($settlement_exists > 0){
						// alert the user
						$output = array('output' => $settlement_exists,
										'message' => "The settlement file already uploaded, please upload other file.");

						return $output;
				}

				// insert the settlements main data
				DB::table('feeds_settlement_main')->insert($data);
				// extract and insert the settlement data
				$this->settlementsAnalysis($text,$data['unique_id']);

				$settlements_data_counter = DB::table('feeds_settlement_main')->count();

				if($settlements_data_counter > 1){

					// selttlemets main data
					$settlements_data = DB::table('feeds_settlement_main')
										->orderBy('main_id','desc')
										->limit(2)
										->get();

					// make array data for unique_id
					foreach($settlements_data as $v){
						$settlements_unique_id[] = $v->unique_id;
					}

					// carcass data
					$carcass_data = DB::table('feeds_settlement_carcass_weight')
									->whereIn('unique_id',$settlements_unique_id)
									->get();
					$carcass_data = array_chunk($carcass_data,16,true);

					for($i = 0; $i < count($carcass_data[1]); $i++){

						$carcass_part_one[] = (array)$carcass_data[0][$i] + array('total_two' => $carcass_data[1][$i + 16]->total);

					}

					$carcass_data = $carcass_part_one;



					// lean data
					$lean_data = DB::table('feeds_settlement_lean')
									->whereIn('unique_id',$settlements_unique_id)
									->get();

					$lean_data = array_chunk($lean_data,16,true);

					for($i = 0; $i < count($lean_data[1]); $i++){

						$lean_part_one[] = (array)$lean_data[0][$i] + array('total_two' => $lean_data[1][$i + 16]->total);

					}

					$lean_data = $lean_part_one;


					return view('settlements.ajax.indexdual', compact("settlements_data","carcass_data","lean_data"));

				} else {

					$settlements_data = DB::table('feeds_settlement_main')
										->orderBy('main_id','desc')
										->first();

					$settlements_unique_id = $settlements_data->unique_id;

					$carcass_data = DB::table('feeds_settlement_carcass_weight')
									->where('unique_id','=',$settlements_unique_id)
									->get();

					$lean_data = DB::table('feeds_settlement_lean')
									->where('unique_id','=',$settlements_unique_id)
									->get();


					return view('settlements.ajax.index', compact("settlements_data","carcass_data","lean_data"));

				}


			}	else {
				$error = true;
			}

			$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);

			return $data;

    }


	/**
		* Settlements Main Process.
		*
		* @param  string  $text
		* @param  string  $unique_id
		* @param  array  $unique_id
		* @return Response
		*/
		private function settlementsMain($text,$unique_id,$group_number,$files = array())
		{

			$data = array();

			// group_number
			$data['group_number'] = $group_number;

			// settlement_number
			$data['settlement_number'] = strstr($text,"SETTLEMENT#:");
			$data['settlement_number'] = substr($data['settlement_number'], 0, 19);
			$data['settlement_number'] = substr($data['settlement_number'],12);

			// market_date
			$data['market_date'] = strstr($text,"MARKET DATE:");
			$data['market_date'] = substr($data['market_date'], 0, 24);
			$data['market_date'] = substr($data['market_date'],14);
			$data['market_date'] = date('Y-m-d', strtotime($data['market_date']));

			// farm
			$data['farm'] = strstr($text,"FARM LOCATION");
			$data['farm'] = strstr($data['farm'],"#:  ",true);
			$data['farm'] = substr($data['farm'],21);

			// trucking_company
			$data['trucking_company'] = strstr($text,"TRUCKING COMPANY:");
			$data['trucking_company'] = strstr($data['trucking_company'],"\n", true); // get the data starting from right
			$data['trucking_company'] = substr($data['trucking_company'], 17);

			// dead_on_truck, if empty the output will be false
			$data['dead_on_truck'] = strstr($text,"DEAD ON TRUCK:");
			$data['dead_on_truck'] = strstr($data['dead_on_truck'],"INSURANCE CLAIM:",true);
			$data['dead_on_truck'] = substr($data['dead_on_truck'], 14);

			// destroyed
			$data['destroyed'] = strstr($text,"DESTROYED:");
			$data['destroyed'] = strstr($data['destroyed'],"INSURANCE PREMIUM:",true);
			$data['destroyed'] = substr($data['destroyed'],10);

			// total_head
			$data['total_head'] = strstr($text,"TOTALHEAD");
			$data['total_head'] = strstr($data['total_head'],"\nTOTALWEIGHT",true);
			$data['total_head'] = substr($data['total_head'],10);

			// live_avg_weight
			$data['live_avg_weight'] = strstr($text,"AVGWEIGHT");
			$data['live_avg_weight'] = strstr($data['live_avg_weight'],"\nFATDEPTH",true);
			$data['live_avg_weight'] = substr($data['live_avg_weight'],16);

			// carcas_avg_weight
			$data['carcass_avg_weight'] = strstr($text,"AVGWEIGHT");
			$data['carcass_avg_weight'] = strstr($data['carcass_avg_weight'],"\nFATDEPTH",true);
			$data['carcass_avg_weight'] = substr($data['carcass_avg_weight'],10);
			$data['carcass_avg_weight'] = substr($data['carcass_avg_weight'],0,6);

			// fat_depth
			$data['fat_depth'] = strstr($text,"FATDEPTH");
			$data['fat_depth'] = strstr($data['fat_depth'],"\nLOINDEPTH",true);
			$data['fat_depth'] = substr($data['fat_depth'],9);

			// loin_depth
			$data['loin_depth'] = strstr($text,"LOINDEPTH");
			$data['loin_depth'] = strstr($data['loin_depth'],"\nLEAN%",true);
			$data['loin_depth'] = substr($data['loin_depth'],10);

			// lean_percentage
			$data['lean_percentage'] = strstr($text,"LEAN%");
			$data['lean_percentage'] = strstr($data['lean_percentage'],"\nPRICE",true);
			$data['lean_percentage'] = substr($data['lean_percentage'],6);

			// yield
			$data['yield'] = strstr($text,"YIELD");
			$data['yield'] = strstr($data['yield'],"NATIONAL PORK BOARD:",true);
			$data['yield'] = substr($data['yield'],6);

			// lean_adj
			$data['lean_adj'] = strstr($text,"LEANADJ.");
			$data['lean_adj'] = strstr($data['lean_adj'],"\nPAID",true);
			$data['lean_adj'] = substr($data['lean_adj'],9);

			// sort_adj
			$data['sort_adj'] = strstr($text,"SORTADJ.");
			$data['sort_adj'] = strstr($data['sort_adj'],")\nLEANADJ.",true);
			$data['sort_adj'] = substr($data['sort_adj'],10);

			// price
			$data['price'] = strstr($text,"PRICE");
			$data['price'] = strstr($data['price'],"MARKET DATE:",true);
			$data['price'] = trim(substr($data['price'],6));

			// paid
			$data['paid'] = strstr($text,"PAID");
			$data['paid'] = strstr($data['paid'],"\nYIELD",true);
			$data['paid'] = trim($data['paid'],"PAID\n");

			// sort_opportunity
			$data['sort_opportunity'] = strstr($text,"SORT OPPORTUNITY");
			$data['sort_opportunity'] = trim($data['sort_opportunity'],"SORT OPPORTUNITY\n");
			$data['sort_opportunity'] = trim(substr($data['sort_opportunity'],8));
			$data['sort_opportunity'] = substr($data['sort_opportunity'],-6);

			// net_amount
			$data['net_amount'] = strstr($text,"NET AMOUNT:");
			$data['net_amount'] = strstr($data['net_amount'],"\nLOT ANALYSIS:",true);
			$data['net_amount'] = trim($data['net_amount'],"NET AMOUNT:$");
			$data['net_amount'] = str_replace(",","",$data['net_amount']);

			// unique_id
			$data['unique_id'] = $unique_id;

			// upload_date
			$data['upload_date'] = date("Y-m-d H:i:s");

			// file_location
			$data['file_location'] = $files[0];

			return $data;

		}

	/**
		* Settlements Analysis Process.
		*
		* @param  string  $text
		* @param  string  $unique_id
		* @return Response
		*/
		private function settlementsAnalysis($text,$unique_id)
		{
				$text = $this->textSpacer($text);

				$forward_contracts = $this->forwardContract($text,$unique_id);
				$forward_contracts_batch = array();
				for($i=1; $i<=count($forward_contracts); $i++){
					array_push($forward_contracts_batch,$forward_contracts['contract_'.$i]);
				}
				// insert the forwards contract data
				DB::table('feeds_settlement_forward_contracts')->insert($forward_contracts_batch);


				$carcass = $this->carcass($text);
				$carcass_batch = array();
				for($i=1; $i<=count($carcass); $i++){
					$carcass['carcass_'.$i] = $carcass['carcass_'.$i] + array('unique_id' => $unique_id);
					array_push($carcass_batch,$carcass['carcass_'.$i]);
				}
				// insert the carcass data
				DB::table('feeds_settlement_carcass_weight')->insert($carcass_batch);


				$lean = $this->lean($text);
				$lean_batch = array();
				for($i=1; $i<=count($lean); $i++){
					$lean['lean_'.$i] = $lean['lean_'.$i] + array('unique_id' => $unique_id);
					array_push($lean_batch,$lean['lean_'.$i]);
				}
				// insert the lean data
				DB::table('feeds_settlement_lean')->insert($lean_batch);

		}

		/**
		* Forward Contracts Extractor.
		*
		* @param  string  $text
		* @param  string  $unique_id
		* @return Response
		*/
		private function forwardContract($text,$unique_id)
		{
				$forward_contract = array();

				// contract 1
				$forward_contract['contract_1'] = strstr($text,"0/44.99:");
				$forward_contract['contract_1'] = strstr($forward_contract['contract_1']," ,151/160.5: ",true);
				$forward_contract['contract_1'] = substr($forward_contract['contract_1'],-5);
				$forward_contract['contract_1'] = array('contract_number' => $forward_contract['contract_1'], 'unique_id' => $unique_id);

				// contract 2
				$forward_contract['contract_2'] = strstr($text,"45/47.99:");
				$forward_contract['contract_2'] = strstr($forward_contract['contract_2']," ,161/165.5: ",true);
				$forward_contract['contract_2'] = substr($forward_contract['contract_2'],-5);
				$forward_contract['contract_2'] = array('contract_number' => $forward_contract['contract_2'], 'unique_id' => $unique_id);

				return $forward_contract;
		}

		/**
		 * Text Spacer for static lean and carcass type
		 *
		 * @param  string  $text
		 * @return Response
		 */
		private function textSpacer($text)
		{

			// Carcass
			$text = str_replace("0/150.5"," ,0/150.5: ", $text);
			$text = str_replace("151/160.5"," ,151/160.5: ", $text);
			$text = str_replace("161/165.5"," ,161/165.5: ", $text);
			$text = str_replace("166/170.5"," ,166/170.5: ", $text);
			$text = str_replace("171/180.5"," ,171/180.5: ", $text);
			$text = str_replace("181/190.5"," ,181/190.5: ", $text);
			$text = str_replace("191/200.5"," ,191/200.5: ", $text);
			$text = str_replace("201/210.5"," ,201/210.5: ", $text);
			$text = str_replace("211/220.5"," ,211/220.5: ", $text);
			$text = str_replace("221/230.5"," ,221/230.5: ", $text);
			$text = str_replace("231/235.5"," ,231/235.5: ", $text);
			$text = str_replace("236/240.5"," ,236/240.5: ", $text);
			$text = str_replace("241/245.5"," ,241/245.5: ", $text);
			$text = str_replace("246/250.5"," ,246/250.5: ", $text);
			$text = str_replace("251/255.5"," ,251/255.5: ", $text);
			$text = str_replace("256/999"," ,256/999: ", $text);

			// Lean
			$text = str_replace("0/44.99"," ,0/44.99: ", $text);
			$text = str_replace("45/47.99"," ,45/47.99: ", $text);
			$text = str_replace("48/48.99"," ,48/48.99: ", $text);
			$text = str_replace("49/49.99"," ,49/49.99: ", $text);
			$text = str_replace("50/50.99"," ,50/50.99: ", $text);
			$text = str_replace("51/51.99"," ,51/51.99: ", $text);
			$text = str_replace("52/52.5"," ,52/52.5: ", $text);
			$text = str_replace("52.51/52.99"," ,52.51/52.99: ", $text);
			$text = str_replace("53/53.5"," ,53/53.5: ", $text);
			$text = str_replace("53.51/53.99"," ,53.51/53.99: ", $text);
			$text = str_replace("54/54.99"," ,54/54.99: ", $text);
			$text = str_replace("55/55.5"," ,55/55.5: ", $text);
			$text = str_replace("55.51/56.5"," ,55.51/56.5: ", $text);
			$text = str_replace("56.51/56.99"," ,56.51/56.99: ", $text);
			$text = str_replace("57/57.99"," ,57/57.99: ", $text);
			$text = str_replace("58/100"," ,58/100: ", $text);

			return $text;

		}

		/**
	   * Carcass type extractor
	   *
	   * @param  string  $text
	   * @return Response
	   */
		private function carcass($text)
		{
			$carcass = array();

			// 0/150.5
			$carcass['carcass_1'] = strstr($text,"0/150.5:");
			$carcass['carcass_1'] = strstr($carcass['carcass_1']," ,0/44.99: ",true);
			$carcass['carcass_1'] = substr($carcass['carcass_1'], 9);
			$carcass['carcass_1'] = array('weight' => '0/150.5', 'total' => $carcass['carcass_1']);

			// 151/160.5
			$carcass['carcass_2'] = strstr($text,"151/160.5:");
			$carcass['carcass_2'] = strstr($carcass['carcass_2']," ,45/47.99: ", true);
			$carcass['carcass_2'] = substr($carcass['carcass_2'],11);
			$carcass['carcass_2'] = array('weight' => '151/160.5', 'total' => $carcass['carcass_2']);

			// 161/165.5
			$carcass['carcass_3'] = strstr($text,"161/165.5:");
			$carcass['carcass_3'] = strstr($carcass['carcass_3']," ,48/48.99: ",true);
			$carcass['carcass_3'] = substr($carcass['carcass_3'],11);
			$carcass['carcass_3'] = array('weight' => '161/165.5', 'total' => $carcass['carcass_3']);

			// 166/170.5
			$carcass['carcass_4'] = strstr($text,"166/170.5:");
			$carcass['carcass_4'] = strstr($carcass['carcass_4']," ,49/49.99: ",true);
			$carcass['carcass_4'] = substr($carcass['carcass_4'],11);
			$carcass['carcass_4'] = array('weight' => '166/170.5', 'total' => $carcass['carcass_4']);

			// 171/180.5
			$carcass['carcass_5'] = strstr($text,"171/180.5:");
			$carcass['carcass_5'] = strstr($carcass['carcass_5']," ,50/50.99: ",true);
			$carcass['carcass_5'] = substr($carcass['carcass_5'],11);
			$carcass['carcass_5'] = array('weight' => '171/180.5', 'total' => $carcass['carcass_5']);

			// 181/190.5
			$carcass['carcass_6'] = strstr($text,"181/190.5");
			$carcass['carcass_6'] = strstr($carcass['carcass_6']," ,51/51.99: ", true);
			$carcass['carcass_6'] = substr($carcass['carcass_6'],11);
			$carcass['carcass_6'] = array('weight' => '181/190.5', 'total' => $carcass['carcass_6']);

			// 191/200.5
			$carcass['carcass_7'] = strstr($text,"191/200.5:");
			$carcass['carcass_7'] = strstr($carcass['carcass_7']," ,52/52.5: ",true);
			$carcass['carcass_7'] = substr($carcass['carcass_7'],11);
			$carcass['carcass_7'] = array('weight' => '191/200.5', 'total' => $carcass['carcass_7']);

			// 201/210.5
			$carcass['carcass_8'] = strstr($text,"201/210.5:");
			$carcass['carcass_8'] = strstr($carcass['carcass_8']," ,52.51/52.99: ",true);
			$carcass['carcass_8'] = substr($carcass['carcass_8'],11);
			$carcass['carcass_8'] = array('weight' => '201/210.5', 'total' => $carcass['carcass_8']);

			// 211/220.5
			$carcass['carcass_9'] = strstr($text,"211/220.5:");
			$carcass['carcass_9'] = strstr($carcass['carcass_9']," ,53/53.5: ",true);
			$carcass['carcass_9'] = substr($carcass['carcass_9'],11);
			$carcass['carcass_9'] = array('weight' => '211/220.5', 'total' => $carcass['carcass_9']);

			// 221/230.5
			$carcass['carcass_10'] = strstr($text,"221/230.5:");
			$carcass['carcass_10'] = strstr($carcass['carcass_10']," ,53.51/53.99: ",true);
			$carcass['carcass_10'] = substr($carcass['carcass_10'],11);
			$carcass['carcass_10'] = array('weight' => '221/230.5', 'total' => $carcass['carcass_10']);

			// 231/235.5
			$carcass['carcass_11'] = strstr($text,"231/235.5:");
			$carcass['carcass_11'] = strstr($carcass['carcass_11']," ,54/54.99: ",true);
			$carcass['carcass_11'] = substr($carcass['carcass_11'],11);
			$carcass['carcass_11'] = array('weight' => '231/235.5', 'total' => $carcass['carcass_11']);

			// 236/240.5
			$carcass['carcass_12'] = strstr($text,"236/240.5:");
			$carcass['carcass_12'] = strstr($carcass['carcass_12']," ,55/55.5: ",true);
			$carcass['carcass_12'] = substr($carcass['carcass_12'],11);
			$carcass['carcass_12'] = array('weight' => '236/240.5', 'total' => $carcass['carcass_12']);

			// 241/245.5
			$carcass['carcass_13'] = strstr($text,"241/245.5:");
			$carcass['carcass_13'] = strstr($carcass['carcass_13']," ,55.51/56.5: ",true);
			$carcass['carcass_13'] = substr($carcass['carcass_13'],11);
			$carcass['carcass_13'] = array('weight' => '241/245.5', 'total' => $carcass['carcass_13']);

			// 246/250.5
			$carcass['carcass_14'] = strstr($text,"246/250.5:");
			$carcass['carcass_14'] = strstr($carcass['carcass_14']," ,56.51/56.99: ",true);
			$carcass['carcass_14'] = substr($carcass['carcass_14'],11);
			$carcass['carcass_14'] = array('weight' => '246/250.5', 'total' => $carcass['carcass_14']);

			// 251/255.5
			$carcass['carcass_15'] = strstr($text,"251/255.5:");
			$carcass['carcass_15'] = strstr($carcass['carcass_15']," ,57/57.99: ",true);
			$carcass['carcass_15'] = substr($carcass['carcass_15'],11);
			$carcass['carcass_15'] = array('weight' => '251/255.5', 'total' => $carcass['carcass_15']);

			// 256/999
			$carcass['carcass_16'] = strstr($text,"256/999:");
			$carcass['carcass_16'] = strstr($carcass['carcass_16']," ,58/100: ",true);
			$carcass['carcass_16'] = substr($carcass['carcass_16'],9);
			$carcass['carcass_16'] = array('weight' => '256/999', 'total' => $carcass['carcass_16']);

			return $carcass;
		}

		/**
		 * Lean type extractor
		 *
		 * @param  string  $text
		 * @return Response
		 */
		private function lean($text)
		{
			$carcass = array();

			// 0/44.99
			$lean['lean_1'] = strstr($text,"0/44.99:");
			$lean['lean_1'] = strstr($lean['lean_1']," ,151/160.5: ",true);
			$lean['lean_1'] = substr($lean['lean_1'],9,-5);
			$lean['lean_1'] = array('weight' => '0/44.99', 'total' => $lean['lean_1']);

			// 45/47.99
			$lean['lean_2'] = strstr($text,"45/47.99:");
			$lean['lean_2'] = strstr($lean['lean_2']," ,161/165.5: ",true);
			$lean['lean_2'] = substr($lean['lean_2'],10,-5);
			$lean['lean_2'] = array('weight' => '45/47.99', 'total' => $lean['lean_2']);

			// 48/48.99
			$lean['lean_3'] = strstr($text,"48/48.99:");
			$lean['lean_3'] = strstr($lean['lean_3']," ,166/170.5: ",true);
			$lean['lean_3'] = substr($lean['lean_3'],10);
			$lean['lean_3'] = array('weight' => '48/48.99', 'total' => $lean['lean_3']);

			// 49/49.99
			$lean['lean_4'] = strstr($text,"49/49.99:");
			$lean['lean_4'] = strstr($lean['lean_4']," ,171/180.5: ",true);
			$lean['lean_4'] = substr($lean['lean_4'],10);
			$lean['lean_4'] = array('weight' => '49/49.99', 'total' => $lean['lean_4']);

			// 50/50.99
			$lean['lean_5'] = strstr($text,"50/50.99:");
			$lean['lean_5'] = strstr($lean['lean_5'],"\n\n ,181/190.5: ",true);
			$lean['lean_5'] = substr($lean['lean_5'],10);
			$lean['lean_5'] = array('weight' => '50/50.99', 'total' => $lean['lean_5']);

			// 51/51.99
			$lean['lean_6'] = strstr($text,"51/51.99:");
			$lean['lean_6'] = strstr($lean['lean_6'],"\n\n ,191/200.5: ",true);
			$lean['lean_6'] = substr($lean['lean_6'],10);
			$lean['lean_6'] = array('weight' => '51/51.99', 'total' => $lean['lean_6']);

			// 52/52.5
			$lean['lean_7'] = strstr($text,"52/52.5:");
			$lean['lean_7'] = strstr($lean['lean_7'],"\n\n ,201/210.5: ",true);
			$lean['lean_7'] = substr($lean['lean_7'],9);
			$lean['lean_7'] = array('weight' => '52/52.5', 'total' => $lean['lean_7']);

			// 52.51/52.99
			$lean['lean_8'] = strstr($text,"52.51/52.99:");
			$lean['lean_8'] = strstr($lean['lean_8'],"\n\n ,211/220.5: ",true);
			$lean['lean_8'] = substr($lean['lean_8'],13);
			$lean['lean_8'] = array('weight' => '52.51/52.99', 'total' => $lean['lean_8']);

			// 53/53.5
			$lean['lean_9'] = strstr($text,"53/53.5:");
			$lean['lean_9'] = strstr($lean['lean_9'],"\n\n ,221/230.5: ",true);
			$lean['lean_9'] = substr($lean['lean_9'],9);
			$lean['lean_9'] = array('weight' => '53/53.5', 'total' => $lean['lean_9']);

			// 53.51/53.99
			$lean['lean_10'] = strstr($text,"53.51/53.99:");
			$lean['lean_10'] = strstr($lean['lean_10'],"\n\n ,231/235.5: ",true);
			$lean['lean_10'] = substr($lean['lean_10'],13);
			$lean['lean_10'] = array('weight' => '53.51/53.99', 'total' => $lean['lean_10']);

			// 54/54.99
			$lean['lean_11'] = strstr($text,"54/54.99:");
			$lean['lean_11'] = strstr($lean['lean_11']," ,236/240.5: ",true);
			$lean['lean_11'] = substr($lean['lean_11'],10);
			$lean['lean_11'] = array('weight' => '54/54.99', 'total' => $lean['lean_11']);

			// 55/55.5
			$lean['lean_12'] = strstr($text,"55/55.5:");
			$lean['lean_12'] = strstr($lean['lean_12']," ,241/245.5: ",true);
			$lean['lean_12'] = substr($lean['lean_12'],9);
			$lean['lean_12'] = array('weight' => '55/55.5', 'total' => $lean['lean_12']);

			// 55.51/56.5
			$lean['lean_13'] = strstr($text,"55.51/56.5:");
			$lean['lean_13'] = strstr($lean['lean_13']," ,246/250.5: ",true);
			$lean['lean_13'] = substr($lean['lean_13'],12);
			$lean['lean_13'] = array('weight' => '55.51/56.5', 'total' => $lean['lean_13']);

			// 56.51/56.99
			$lean['lean_14'] = strstr($text,"56.51/56.99:");
			$lean['lean_14'] = strstr($lean['lean_14']," ,251/255.5: ",true);
			$lean['lean_14'] = substr($lean['lean_14'],13);
			$lean['lean_14'] = array('weight' => '56.51/56.99', 'total' => $lean['lean_14']);

			// 57/57.99
			$lean['lean_15'] = strstr($text,"57/57.99:");
			$lean['lean_15'] = strstr($lean['lean_15']," ,256/999: ",true);
			$lean['lean_15'] = substr($lean['lean_15'],10);
			$lean['lean_15'] = array('weight' => '57/57.99', 'total' => $lean['lean_15']);

			// 58/100
			$lean['lean_16'] = strstr($text,"58/100:");
			$lean['lean_16'] = strstr($lean['lean_16'],"\nCARCASSLIVE",true);
			$lean['lean_16'] = substr($lean['lean_16'],8);
			$lean['lean_16'] = array('weight' => '58/100', 'total' => $lean['lean_16']);

			return $lean;
		}


		/*
		*	load Finisher farms
		*/
		public function loadFinishers()
		{
			 $input = DB::table('feeds_farms')->select('name','id')->where('farm_type','finisher')->orderBy('name')->get();

			 $len = count($input);

			 $firsthalf = array_slice($input, 0, $len / 2);
			 $secondhalf = array_slice($input, $len / 2);

			 $output = array(
				 'farm_list_one'	=>	$firsthalf,
				 'farm_list_two'	=>	$secondhalf
			 );

			 return $output;

		}

		/*
		*	Load the finisher groups
		*/
		public function loadFinisherGroups()
		{
			$farm_id = Input::get('farm_id');
			$groups = DB::table('feeds_movement_finisher_group')->select('group_name')
									->where('farm_id',$farm_id)
									->where('status','!=','removed')->get();

			if($groups == NULL){
				return array(array('group_name'=>'none'));
			}

			return $groups;
		}

		/*
		*	load Finisher farms
		*/
		public function loadFinisherFarms()
		{
			 $output = DB::table('feeds_farms')->select('name','id')->where('farm_type','finisher')->orderBy('name')->get();
			 return $output;
		}

}
