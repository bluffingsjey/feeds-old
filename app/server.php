<?php
/*Create a server variable with the link to the tcp IP and custom port you need to
specify the Homestead IP if you are using homestead or, for local environment using
WAMP, MAMP, ... use 127.0.0..1*/


require_once(__DIR__.'/../vendor/autoload.php');

use \Illuminate\Database\Capsule\Manager as Capsule;  
use Illuminate\Database\Eloquent\Model as Model;


$capsule = new Capsule;

$capsule->addConnection(array(
	'driver'    => 'mysql',
	'host'      => 'localhost',
	'database'  => 'j2feeds_db',
	'username'  => 'j2feeds_user',
	'password'  => 'harhar123',
	'port'      => '3306',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix'    => ''
));

$capsule->setAsGlobal();

$capsule->bootEloquent();


$server = new Hoa\Websocket\Server(
    new Hoa\Socket\Server('tcp://37.221.175.118:9540')
);

date_default_timezone_set('UTC');
//Manages the message event to get send data for each client using the broadcast method
$server->on('message', function ( Hoa\event\Bucket $bucket ) {
	
	$unique = uniqid(rand()) . date('ymdhms');	
	
    $data = $bucket->getData();
    //echo 'message: ', $data['message'], "\n";
	
	if(!empty($data['message']) && $data['message'] != NULL || $data['message'] != ""){
		
		$bucket->getSource()->broadcast($data['message']);
			  
		// output to be converted by the mobile app
		echo json_encode($data['message']);
		
		$msg = json_decode($data['message'],true);
	
		$notification = array(
			'type'		=>	1,
			'status'	=>	1,
			'admin'		=>	$msg['user_from'],
			'posted'	=>	date('Y-m-d H:i:s'),
			'unique_id'	=>	$unique
		);
		
		Capsule::table('feeds_notifications')->insert($notification);
		
		$msg = array(
			'user_from'		=>	$msg['user_from'],
			'user_to'		=>	$msg['user_to'],
			'message'		=>	$msg['msg'],
			'posted'		=>	date('Y-m-d H:i:s'),
			'username'		=>	$msg['username'],
			'unique_id'		=>	$unique,
			
		);
		Capsule::table('feeds_messages')->insert($msg);
	}
	
    return;

});

//Execute the server
$server->run();