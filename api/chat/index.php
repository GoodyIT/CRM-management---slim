<?php

ini_set('display_errors', 'On');

error_reporting(E_ALL ^ E_NOTICE);

error_reporting(-1);

require '../app.php';

use Mailgun\Mailgun;

use Firebase\Token\TokenException;

use Firebase\Token\TokenGenerator;

$app->config(array(

	'templates.path' => './',

));

$app->map('/', function () use ($app, $settings)  {

	$settings['firebaseUrl']='https://sweltering-heat-2490.firebaseio.com/insurtainty';

	$settings['firebaseSecret']='yRvxZFM5vtLlN5uRb6ZA60XkieFrkLzzfsza0nd8';

	$usergroups=array();

	$manager=false;

	$podmanager=0;

	$apiObj = new apiclass($settings);

	$apiObj->mongoSetDB($settings['database']);

	$apiObj->mongoSetCollection("userGroups");

	$query['users.userId']=$_SESSION['api']['user']['_id'];

	$userRoom= $apiObj->mongoFind($query);

	$gouprsCursor = $apiObj->mongoFind();

	$roomLabel=false;

	$roomId=false;

	foreach($userRoom as $room){

		foreach($room['users'] as $user){

			if(($user['userId']==$_SESSION['api']['user']['_id'])&&($user['level']!='none')&&($user['level']!='NONE')){

				$roomLabel=$room['label'];

				 if(empty($roomId)){
				    $roomId=$room['_id'];
                }

			}

			if(($user['userId']==$_SESSION['api']['user']['_id'])&&(strtoupper($user['level'])=='MANAGER')){

				$podmanager=true;

			}

		}

	}

	if(!empty($_GET['roomId'])){

		$roomId=$_GET['roomId'];

	}

	foreach($gouprsCursor as $group){

		array_push($usergroups,$group);

	}

	if((strtoupper($_SESSION['api']['user']['permissionLevel'])=='MANAGER')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='INSUREHC')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='ADMINISTRATOR')){

		$manager= true;

		$podmanager= true;

	}else{

		$manager= 0;

	}

	try {

		$generator = new TokenGenerator($settings['firebaseSecret']);

		$token = $generator

			->setData(array('uid' => $_SESSION['api']['user']['_id']))

			->setOption('admin', true)

			->create();

	} catch (TokenException $e) {

		$token = "Error: ".$e->getMessage();

	}

	$name=$_SESSION['api']['user']['firstname']." ".$_SESSION['api']['user']['lastname'];

	$app->render('chatpage.php', array('settings'=>$settings,'name' => $name,'roomId'=>$roomId,'roomLabel'=>$roomLabel,'usergroups'=>$usergroups,'manager'=>$manager,'podmanager'=>$podmanager,'token'=>$token));

})->via('GET','POST');

$app->run();