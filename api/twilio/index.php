<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
require '../app.php';
$app->config(array(
	'templates.path' => './',
));
$app->map('/smsModal', function () use ($app, $settings, $mongo){
	$parentId = $_GET['parentId'];
	if(!empty($_GET['selectedNumber'])){
		$selectedNumber = $_GET['selectedNumber'];
	}else{
		$selectedNumber=null;
	}
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("phones");
	$phones=array();
	$phoneCursor = $apiObj->mongoFind(array('_parentId'=>$parentId));
	foreach($phoneCursor as $phone){
		$a = $phone;
		array_push($phones, $a);
	}
	$templates=array();
	$apiObj->mongoSetCollection("smsTemplate");
	$templateCursor=$apiObj->mongoFind();
	foreach($templateCursor as $temp){
		$b = $temp;
		array_push($templates, $b);
	}
	$app->render('message.php', array("apiObj"=>$apiObj, 'phones'=>$phones, 'settings'=>$settings,'templates'=>$templates, 'parentId'=>$parentId, 'selectedNumber'=>$selectedNumber));
})->via('GET','POST');
/*
****************************************************************************************
 --------------------------------------------------------------------------------------
 Send a predefined SMS to inputed Number
 --------------------------------------------------------------------------------------
****************************************************************************************
*/
$app->map('/sms', function() use ($twilioObj, $client, $app, $settings){
	$twilioObj = new twilioPlugin($settings);
	$number = $_REQUEST['person_0_sms_0_toNumber'];
	$message = $_REQUEST['person_0_sms_0_message'];
	$twilioObj->servies_twilio = new Services_Twilio($twilioObj->accountSid, $twilioObj->authToken);
	$twilioObj->servies_twilio->account->messages->sendMessage(
		$twilioObj->callerId, $number, $message
	);
})->via('GET', 'POST');
$app->map('/messageManager', function() use ($twilioObj, $client, $app, $settings){
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("smsTemplate");
	$templates=array();
	$templateCursor=$apiObj->mongoFind();
	foreach($templateCursor as $temp){
		$a = $temp;
		array_push($templates, $a);
	}
	$app->render('templates.php', array("apiObj"=>$apiObj, 'templates'=>$templates, 'settings'=>$settings));
})->via('GET', 'POST');
$app->map('/templateModal', function() use ($twilioObj, $client, $app, $settings){
	if(!empty($_POST['templateId'])){
		$templateId = $_POST['templateId'];
		$apiObj = new apiclass($settings);
		$apiObj->mongoSetDB($settings['database']);
		$apiObj->mongoSetCollection("smsTemplate");
		$template=$apiObj->mongoFindOne(array('_id'=>$templateId));
	}
	$apiObj = new apiclass($settings);
	$app->render('tmplModal.php', array("apiObj"=>$apiObj, 'settings'=>$settings, 'templateId'=>$templateId, 'template'=>$template));
})->via('GET', 'POST');
$app->map('/receivesms', function() use ($twilioObj, $client, $app, $settings){
	
})->via('GET', 'POST');
$app->map('/dialer', function() use ($twilioObj, $client, $app, $settings){
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("smsTemplate");
	$app->render('dialerInterface.php', array("apiObj"=>$apiObj, 'settings'=>$settings));
})->via('GET', 'POST');
$app->run();