<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('transferpigs', 'MiscController@testing');
Route::get('/', 'HomeController@index');
Route::get('drivertestmessage','CloudMessaging@driverTestMessaging');
Route::get('testcron','HomeController@testCrons');
Route::get('binclearcache/{id}','HomeController@clearBinsCache');
Route::get('autoupdate/{id}/{date}','HomeController@updateBinHistory');
Route::get('markasdeliveredmobile','CloudMessaging@testMarkasDelivered');
Route::get('removefarmleftovers/{id}','FarmsController@destroyLeftOvers');
Route::get('servertime','MiscController@serverTimes');
Route::post('loadMessage','MessagingController@messageLoader');
Route::post('closewebsocket','MessagingController@closeWebsocket');
Route::post('apipost','MiscController@indexPost');
Route::get('testSupport','MiscController@testSupport');
Route::get('fbudgetupdater','HomeController@feedsBudgetedCounterUpdater');
Route::get('forecastingdatacache','HomeController@forecastingDataCache');

Route::get('sendmail','MessagingController@sendEmail');
Route::get('phpinfo',function(){
	$date_one = date("Y-m-d H:i a");
	$date_two = date("Y-m-d") . " 01:00 am";
	$date_three = date("Y-m-d") . " 11:59 pm";
	echo $date_one."<br/>";
	echo $date_two."<br/>";
	if(strtotime($date_one) > strtotime($date_two) && strtotime($date_one) < strtotime($date_three)){
		echo "true";
	}
});


Route::get('livetrucks','LiveTruckController@index');

Route::get('releasenotes','MiscController@releaseNotes');
Route::get('getreleasenotes','MiscController@getReleaseNotes');
Route::post('savereleasenotes','MiscController@saveReleaseNotes');
Route::post('updatereleasenotes','MiscController@updateReleaseNotes');

Route::get('driveract','DriverActController@index');
Route::get('driveractcontent','DriverActController@content');


Route::get('lzcronjob','CloudMessaging@livezillaCronJob');


Route::get('forgotpw','MiscController@forgotPassword');
Route::get('resetpw','MiscController@resetPasswordAdmin');

Route::get('deceased','DeceasedController@index');
Route::get('loadGroupFarms','DeceasedController@loadFarms');
Route::get('loadGroupBins','DeceasedController@loadBins');
Route::get('deceaseddata','DeceasedController@deceaseddata');
Route::post('savedeceased','DeceasedController@savedeceased');
Route::post('removedeceased','DeceasedController@removedeceased');

Route::get('treatment','TreatmentController@index');
Route::get('treatmentdata','TreatmentController@treatmentdata');
Route::post('savetreatment','TreatmentController@savetreatment');
Route::post('removetreatment','TreatmentController@removetreatment');

/*
*	delete user
*/
Route::get('removeuser/{id}','UsersController@destroy');


/*
*	messaging awaker
*/
Route::get('messagingnoti','CloudMessaging@messagingNotification');


/*
*	Settlements
*/
Route::get('settlements','SettlementsController@index');
Route::post('settlementsupload','SettlementsController@processSettlements');
Route::post('settlementsearch','SettlementsController@settlementsearch');
Route::get('loadfinishers','SettlementsController@loadFinishers');
Route::get('loadfinishergroups','SettlementsController@loadFinisherGroups');
Route::get('loadfinisherfarms','SettlementsController@loadFinisherFarms');

/*
*	Animal movement routes
*/
Route::get('animalmovement','MovementController@index');
Route::get('animalmovementlanding','MovementController@animalMovementFilter');
Route::get('animalmovementfilter','MovementController@animalMovementFilter');

Route::get('animalgroup','MovementController@groupPage');
// farrowing routes
Route::get('farrowing','MovementController@farrowingPage');
Route::get('createfarrowing','MovementController@createfarrowing');
Route::get('farrowingbins','MovementController@farrowingbins');
Route::get('farrowingfarms','MovementController@farrowingfarms');
Route::get('groupname','MovementController@groupname');
Route::post('savefarrowing','MovementController@savefarrowing');
Route::get('checkexists','MovementController@checkexists');
Route::post('updatefarrowing','MovementController@updatefarrowing');
Route::post('getSelectedBins','MovementController@getSelectedBins');
Route::post('saveSelectedBins','MovementController@saveSelectedBins');
Route::get('clearSelectedBins','MovementController@clearSelectedBins');
Route::post('clearSelectedBinsEdit','MovementController@clearSelectedBinsEdit');
Route::post('removegroup','MovementController@removegroup');
Route::get('farrowingloadmore','MovementController@farrowingPageLoadMore');
Route::get('farrowingtransferupdatater','MovementController@farrowingTransferDateUpdater');

// nursery routes
Route::get('nursery','MovementController@nurseryPage');
Route::get('createnursery','MovementController@createnursery');
Route::get('countFarrowingGroups','MovementController@countFarrowingGroups');
Route::post('loadFarrowingGroups','MovementController@loadFarrowingGroups');
Route::post('loadFarrowingGroupsPigs','MovementController@loadFarrowingGroupsPigs');
Route::post('saveSelectedFarrowingGroup','MovementController@saveSelectedFarrowingGroup');
Route::post('getSelectedFarrowingGroup','MovementController@getSelectedFarrowingGroup');
Route::post('getSelectedFarrowingGroupPigs','MovementController@getSelectedFarrowingGroupPigs');
Route::post('savenursery','MovementController@savenursery');
Route::post('checkExistsNursery','MovementController@checkExistsNursery');
Route::post('removeGroupNursery','MovementController@removeGroupNursery');
Route::get('nurseryfarms','MovementController@nurseryfarms');
Route::get('nurserybins','MovementController@nurserybins');
Route::post('updatenursery','MovementController@updatenursery');
Route::post('savePendingSelection','MovementController@savePendingSelection');
Route::post('deletePendingSelection','MovementController@deletePendingSelection');
Route::post('emptyPendingSelection','MovementController@emptyPendingSelection');
Route::get('nurseryloadmore','MovementController@nurseryPageLoadMore');
Route::get('nurserytransferupdatater','MovementController@nurseryTransferDateUpdater');

// finisher routes
Route::get('finisher','MovementController@finisherpage');
Route::get('createfinisher','MovementController@createfinisher');
Route::post('savePendingSelectionFinisher','MovementController@savePendingSelectionFinisher');
Route::post('saveFinisher','MovementController@saveFinisher');
Route::post('checkExistsFinisher','MovementController@checkExistsFinisher');
Route::post('removeGroupFinisher','MovementController@removeGroupFinisher');
Route::post('updatefinisher','MovementController@updateFinisher');
Route::get('finisherfarms','MovementController@finisherfarms');
Route::get('finisherbins','MovementController@finisherbins');
Route::get('finisherloadmore','MovementController@finisherPageLoadMore');

Route::get('finishertransferupdatater','MovementController@finisherTransferDateUpdater');

Route::post('saveTransfer','MovementController@saveTransfer');
Route::post('updateTransfer','MovementController@updateTransfer');
Route::post('deleteTransfer','MovementController@deleteTransfer');
Route::post('finalizeTransfer','MovementController@finalizeTransfer');
Route::get('fetchTransfer','MovementController@fetchTransfer');
Route::get('fetchfarmbinstransfer','MovementController@fetchfarmbinstransfer');

//login checker
Route::post('loginchecker','LoginController@checker');

// API
// forecasting data for new UI
//Route::post('api/farmlist','HomeController@forecastingDataOutput');
//Route::post('api/access',"LoginController@loginChecker");
Route::get('api',"APIController@index");
Route::post('api',"APIController@index");


// status update for sched tool
Route::get('statusupdate','ScheduleController@schedToolStatusUpdate');

/*    Reports Page Routes    */
Route::get('driverstracking','ReportsController@index');
Route::post('driversearch','ReportsController@search');
Route::post('sorting','ReportsController@sorting');

Route::get('livestocktracking','ReportsController@livestocktracking');
/*    End of Reports Page Routes    */

// bins cache builder
Route::get('binscachebuilder','HomeController@binsDataCacheBuilder');
Route::get('binscachebuilder/{id}','HomeController@binsDataCacheBuilder');
Route::get('curlbinscache','HomeController@curlBinsCache');

Route::get('edituserinfo/{id}','UsersInfoController@editInfo');
Route::post('saveinfo','UsersInfoController@save');

Route::post('removefarmer','FarmsController@removeFarmer');
Route::post('farmsloadmore','HomeController@farmsLoadMore');


Route::get('farmsprofile', 'FarmsController@profile');
Route::get('addfarmuser/{id}','FarmsController@addFarmUser');
Route::get('scheduling','ScheduleController@createDelivery');
Route::get('loading/createload','ScheduleController@createLoadVerTwo');
Route::post('loading/addbin','ScheduleController@binsNumberAjax');
Route::post('loading/compartment','ScheduleController@compartmentLoading');
Route::post('loading/saveCompartmentSelection','ScheduleController@saveCompartmentSelection');
Route::get('loading/{id}','ScheduleController@loadDelivery');
Route::get('loading','ScheduleController@loadinglist');

Route::get('schedulingcache','ScheduleController@scheduleCache');


Route::get('unique','HomeController@generator');

Route::get('farmandbins','HomeController@farmBinsGetter');
Route::get('binslistshome','HomeController@binsListsFiltered');
Route::get('feedslistshome','HomeController@feedsListsFiltered');
Route::post('amountslists','HomeController@amountsLists');

// forecasting cache builder
Route::get('cachebuilder','HomeController@forecastingDataCacheBuilder');



// Mark as delivered
Route::post('markdelivered','HomeController@markDelivered');

// delete delivered item
Route::post('deletedelivered','HomeController@deleteDelivered');

// load more deliveries
Route::post('deliveriesloadmore','HomeController@deliveriesLoadMore');

// delivery farms
Route::get('deliveriesfarms','HomeController@farmsDelivered');
// delivery drivers
Route::get('deliveriesdrivers','HomeController@driversDelivered');
// delivery numbers
Route::get('deliveriesnumbers','HomeController@deliveryNumberDelivered');
// delivery farm select event
Route::get('farmselectdeliveriesdrivers','HomeController@farmSelectDriverDelivered');
// delivery farm select event
Route::get('driverselectdeliveriesdrivers','HomeController@driverSelectDriverDelivered');

// forecasting bins
Route::get('forecastingbins','HomeController@forecastingBins');

// forecasting data sorter
Route::post('sorter','HomeController@sortForecast');

Route::post('updatebatch','ScheduleController@updateBatch');
Route::post('updatepending','HomeController@updatepending');
Route::post('updatesavepending','HomeController@updatesavepending');

Route::post('addbatch','ScheduleController@addBatchLoad');
Route::post('getbatch','ScheduleController@addedBatch');
Route::post('delbatch','ScheduleController@deleteBatch');

Route::post('savebatchselection','ScheduleController@saveBatchSelection');
Route::post("loadtotruck","ScheduleController@loadToTruck");
Route::post("loadtotruckedit","ScheduleController@loadToTruckEdited");

Route::post('loadoutsavebatch', 'ScheduleController@loudoutSaveBatch');

Route::post('summrender','ScheduleController@summaryRenderer');

Route::post('schedlistindex','ScheduleController@schedIndexLists');
Route::get('schededitlist/{id}','ScheduleController@schedEditLists');

Route::post('updateticket','ScheduleController@updateTicket');
Route::post('loadoutbins','ScheduleController@loadoutBinsLoaded');
Route::post('compselected','ScheduleController@compartmentsLoaded');
Route::post('loadoutbinsloadcounter','ScheduleController@loadoutBinsLoadCounter');
Route::post('loadoutbinscompartments','ScheduleController@loadoutBinsCompartments');
Route::post('defaultcompartments','ScheduleController@loadoutBinsCompartmentsDefault');
Route::post('deletetempselected','ScheduleController@deleteSelected');

Route::post('json/updatehistory','HomeController@insertHistoryPigs');
Route::post('json/updatebinhistory','HomeController@insertHistoryBin');

Route::post('graphreload','HomeController@graphReloader');


/*
*	Messaging
*/
Route::get('messaging','MessagingController@messaging');
Route::get('msg/{id}','MessagingController@messageLoaderSpecific');
Route::post('notiperson','MessagingController@notificationPerPerson');

Route::get('loginuser','MiscController@loginUser');

Route::get('msgnotification','MessagingController@messageNotification');
Route::post('msghistory','MessagingController@messageHistory');
Route::post('updatenotification',"MessagingController@updateNotification");
Route::get('notificationtotal','MessagingController@totalNotification');

// Post Requests
Route::post('scheduling/ajax', 'ScheduleController@selectFarm');
Route::post('scheduling/selectbins', 'ScheduleController@selectbins');
Route::post('scheduling/addbinsajax', 'ScheduleController@addBinsAjax');
Route::post('users/addRoleUpdate', 'UsersController@addRoleUpdate');
Route::post('truck/addcap', 'TruckController@addComCapacity');
Route::post('truck/storeCompartments', 'TruckController@storeCompartments');
Route::post('farms/addbinscreate', 'FarmsController@addBinsCreate');
Route::post('farms/storebinsone', 'FarmsController@storeBinsOne');
Route::post('farms/storebinstwo', 'FarmsController@storeBinsTwo');
Route::post('scheduling/trucks', 'ScheduleController@trucks');
Route::post('scheduling/drivers', 'ScheduleController@drivers');
Route::post('scheduling/feedtypes', 'ScheduleController@feedTypes');
Route::post('scheduling/farms', 'ScheduleController@farms');
Route::post('scheduling/amounts', 'ScheduleController@amounts');
Route::post('scheduling/bins/{id}', 'ScheduleController@farmBins');
Route::post('scheduling/addbatch', 'ScheduleController@addBatch');
Route::post('scheduling/storetruckloads', 'ScheduleController@storeTruckLoads');
Route::post('scheduling/drivermessaging', 'CloudMessaging@drivermessaging');
Route::post('scheduling/farmmessaging', 'CloudMessaging@farmermessaging');
Route::post('/schedule/batch', 'ScheduleController@readBatch');
Route::post('schedule/loading/{id}','ScheduleController@loadingSched');
Route::post('/schedule/bins', 'ScheduleController@binsColor');
Route::post('/schedule/bin', 'ScheduleController@binColor');
Route::post('compartment/update','TruckController@updateCompartment');
Route::post('binslists','ScheduleController@binsNumber');
Route::get('binslistselected','ScheduleController@binFeedTypesLists');
Route::get('feedstypelists','ScheduleController@feedTypesLists');
Route::post('savebudgedtedperday','FeedTypeController@saveBudgedtedPerDay');


Route::post('savechangedatesched','ScheduleController@saveChangeDateSched');
Route::post('savechangedateschededited','ScheduleController@saveChangeDateSchedEdited');
Route::post('requestsched','ScheduleController@requestSchedData');

// Scheduling Tool Routes
Route::post('initdata','ScheduleController@scheduledData');
// Scheduling Tool Routes for time data
Route::post('initdataBar','ScheduleController@schedToolOutput');
// scheduled items
Route::post('scheduleditemsdelivery','ScheduleController@scheduledItemDeliveryNumber');
// scheduled item for delivery selction
Route::post('scheduleditemsdriver','ScheduleController@scheduledItemDriver');
// update scheduled items time
Route::post('updatescheditems','ScheduleController@updateScheduledItem');
// scheduling tool delivery number validator
Route::post('deliveryNumberValidate','ScheduleController@deliveryNumberValidate');
//total tons
Route::post('totaltonsinit','ScheduleController@totalTons');
//total tons scheduled
Route::post('totaltonsscheduled','ScheduleController@totaltonsscheduled');
//total tons delivered
Route::post('totaltonsdelivered','ScheduleController@totaltonsdelivered');
//get loads
Route::post('getloads','ScheduleController@getLoads');


// turn off farm
Route::post('turnoff','FarmsController@turnOffFarm');
// turn on farm
Route::post('turnon','FarmsController@turnOnFarm');


// Get Requests
Route::get('scheduling/addbins/{id}', 'ScheduleController@addBins');
Route::get('scheduling/step1', 'ScheduleController@schedule');
Route::post('scheduling/step2', 'ScheduleController@selectfarm');
Route::post('scheduling/step3', 'ScheduleController@selectdriver');
Route::get('scheduling/step4', 'ScheduleController@assigntruck');
Route::get('scheduling/final', 'ScheduleController@assignCompartment');
Route::get('reports','ReportsController@index');
Route::get('users/assignrole/{id}', 'UsersController@addRole');
Route::get('truck/addcom/{id}', 'TruckController@addCompartment');
Route::get('truck/delete/{id}', 'TruckController@destroy');
Route::get('trucks/compartments/{id}', 'TruckController@viewCompartments');
Route::get('farms/addbins/{id}', 'ScheduleController@addBins');
Route::get('farms/viewbins/{id}', 'FarmsController@viewBins');
Route::get('farms/editbins', 'FarmsController@editBins');
Route::get('farms/addbinsbegin/{id}', 'FarmsController@addBinsBegin');
Route::get('farms/delete/{id}', 'FarmsController@destroy');
Route::get('/compartment/delete/{id}','TruckController@destroyCompartment');
Route::get('/compartment/edit/{id}','TruckController@editCompartment');
Route::get('/compartment/batchdelete/{id}', 'TruckController@batchDelete');
Route::post('/upload/', 'ScheduleController@upload');

Route::post('farms/updatebin','FarmsController@updateBin');
Route::get('farms/deletebin','FarmsController@destroyBin');

Route::post('saveFarmer','FarmsController@saveFarmer');
Route::post('addFarm', 'ScheduleController@addFarm');
Route::get('deliveries','HomeController@deliveriespage');
Route::post('finalizesched','ScheduleController@finalizeSchedule');
Route::post('saveSchedule','ScheduleController@saveSchedule');
Route::get('saveSchedHome','HomeController@saveSchedule');
Route::post('deletebatch','HomeController@deleteBatch');
Route::post('movetosched','HomeController@scheduleDelivery');

Route::get('pendingdeliveries','HomeController@pendingDeliveries');

Route::post('updatepdtruck','HomeController@updateTruckPending');


// for farmer notification
Route::get('farmernoti','CloudMessaging@farmerAcceptLoad');

// unloading request for mobile
Route::get('farmerloadeddata','CloudMessaging@farmerLoadedData');

// testing noti
Route::get('testingnoti','CloudMessaging@testingNoti');

// Image generator
Route::get('imggenerator','ScheduleController@imageGenerator');

// testing
Route::get('testing','HomeController@binAmount');
Route::get('testingtons','HomeController@tonsAmount');
Route::get('testamounts','HomeController@amountsBins');

//bin sizes update
Route::resource('binsize/update','BinSizeController@update');


// Resource
Route::resource('home','HomeController');
Route::resource('bins','BinsController');
Route::resource('binscat','BinCatController');
Route::resource('binsize','BinSizeController');
Route::resource('feedtype','FeedTypeController');
Route::resource('farms','FarmsController');
Route::resource('truck','TruckController');
Route::resource('users','UsersController');
Route::resource('usersinfo','UsersInfoController');
Route::resource('userstype','UsersTypeController');
Route::resource('medication','MedicationController');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


// for expired login session
Route::filter('csrf','MiscController@sessionLogin');
Route::get('api','MiscController@index');
