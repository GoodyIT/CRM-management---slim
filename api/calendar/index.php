<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
require '../app.php';
$app->config(array(
	'templates.path' => './',
));
$apiObj = new apiclass($settings);
$mongo = new MongoClient();
$app->map('/search', function () use ($app, $settings, $apiObj){
	$result=array();
	$userIds = $apiObj->getUserIds();
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("person");
	$nameparts = explode(" ", $_GET['term']);
	if(count($nameparts) > 1){
		$collectionQuery['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		$collectionQuery['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
	} else {
		if(!empty($nameparts[0])){
			$collectionQuery['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
			$collectionQuery['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		}
	}
	$personCursor = $apiObj->mongoFind($collectionQuery);
	$personCursor->sort(array('firstName'=>1));
	foreach($personCursor as $person){
		//if(in_array($person['assignedTo'], $userIds)){
		$apiObj->mongoSetCollection('addresses');
		$addressQuery = array('_parentId'=>$person['_id']);
		$addressCursor = $apiObj->mongoFindOne($addressQuery);
		$a['label']=$person['firstName'].' '.$person['lastName'].((!empty($addressCursor['state']))?' of '.$addressCursor['state']:'');
		$a['value']=$person['_id'];
		array_push($result, $a);
		//}
	}
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($result,JSON_PRETTY_PRINT));
})->via('GET','POST');
$app->map('/remove/:thingType/:field/:parentId/:childId', function ($thingType,$field,$parentId,$childId) use ($app, $settings, $mongo){
	$mongo->$settings['database']->$thingType->update(
		array('_id'=>$parentId),
		array('$pull'=>
			  array($field=>array('_id'=>$childId))
			 )
	);
})->via('GET','POST');
$app->map('/push/:thingType/:field/:parentId', function ($thingType,$field,$parentId) use ($app, $settings, $apiObj, $mongo){
	$data = array(
		'_timestampCreated' => date("YmdHis"),
		'_timestampModified' => date("YmdHis"),
		'_createdBy' =>  $_POST['person_0_appointment_0_invitesAccepted_0_agentId'],
		'_modifiedBy' =>  $_POST['person_0_appointment_0_invitesAccepted_0_agentId'],
		'_id' => date("YmdHis"). "-".$apiObj->getRandomString(8),
		'_parentId' => $parentId,
		'_parentThing' => 'appointment',
		'agentId' => $_POST['person_0_appointment_0_invitesAccepted_0_agentId'],
	);
	$mongo->$settings['database']->$thingType->update(
		array('_id'=>$parentId),
		array('$push'=>
			  array($field=>$data)
			 )
	);
})->via('GET','POST');
$app->map('/decline/:thingType/:field/:parentId', function ($thingType,$field,$parentId) use ($app, $settings, $apiObj, $mongo){
	$data = array(
		'_timestampCreated' => date("YmdHis"),
		'_timestampModified' => date("YmdHis"),
		'_createdBy' =>  $_POST['person_0_appointment_0_invitesDeclined_0_agentId'],
		'_modifiedBy' =>  $_POST['person_0_appointment_0_invitesDeclined_0_agentId'],
		'_id' => date("YmdHis"). "-".$apiObj->getRandomString(8),
		'_parentId' => $parentId,
		'_parentThing' => 'appointment',
		'agentId' => $_POST['person_0_appointment_0_invitesDeclined_0_agentId'],
	);
	$mongo->$settings['database']->$thingType->update(
		array('_id'=>$parentId),
		array('$push'=>
			  array($field=>$data)
			 )
	);
})->via('GET','POST');
$app->map('/', function () use ($app, $settings, $apiObj){
	$result = array();
	$apiObj->mongoSetDB($settings['database']);
	$userIds = $apiObj->getUserIds();
	$collectionQuery['invitesAccepted.agentId']['$in'] = $userIds;
	$apiObj->mongoSetCollection("appointment");
	//$query= array('invitesAccepted.agentId'=>$_SESSION['api']['user']['_id']);
	$cursor = $apiObj->mongoFind($collectionQuery);
	foreach($cursor as $appointment){
		$date = $appointment['day'].' '.$appointment['time'];
		$a['title'] = $appointment['title'];
		$a['start'] = date(DATE_ISO8601, strtotime($date));
		$a['end'] = date(DATE_ISO8601, strtotime($date)+3600);
		$a['day'] = $appointment['day'];
		$a['time'] = $appointment['time'];
		$a['_parentId'] = $appointment['_parentId'];
		$a['createdBy'] = $appointment['_createdBy'];
		$a['id'] = $appointment['_id'];
		$a['notes'] = $appointment['notes'];
		if($a['title'] == 'CALLBACK'){
			if(!strpos(serialize($appointment), $_SESSION['api']['user']['_id'])){
				$a['color'] = 'rgba( 0, 116, 192, 0.25 )';
			}else{
				$a['color'] = 'rgba( 0, 116, 192, 0.961 )';
			}
		}
		elseif($a['title'] == 'FOLLOWUP'){
			if(!strpos(serialize($appointment), $_SESSION['api']['user']['_id'])){
				$a['color'] = 'rgba( 2, 140, 94, 0.25 )';
			}else{
				$a['color'] = 'rgba( 2, 140, 94, 0.961 )';
			}
		}
		elseif($a['title'] == 'SUBMIT'){
			if(!strpos(serialize($appointment), $_SESSION['api']['user']['_id'])){
				$a['color'] = 'rgba( 20, 152, 42, 0.25 )';
			}else{
				$a['color'] = 'rgba( 20, 152, 42, 0.911 )';
			}
		}
		elseif($a['title'] == 'PERSONAL'){
			if(!strpos(serialize($appointment), $_SESSION['api']['user']['_id'])){
				$a['color'] = 'rgba( 107, 48, 162, 0.25 )';
			}else{
				$a['color'] = 'rgba( 107, 48, 162, 0.911 )';
			}
		}
		$a['allDay'] = false;
		array_push($result, $a);
	}
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($result,JSON_PRETTY_PRINT));
})->via('GET','POST');
$app->map('/render', function () use ($app, $settings, $apiObj){
	$app->render('calendar.php', array('result' => $result, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/today', function () use ($app, $settings, $apiObj){
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode(date('YmdHis')-9600));
})->via('GET','POST');
$app->map('/createModal', function () use ($app, $settings, $apiObj){
	$_GET = $_GET;
	$apiObj->mongoSetDB($settings['database']);
	$userIds = $apiObj->getUserIds();
	$apiObj->mongoSetCollection("person");
	$collectionQuery['assignedTo']['$in'] = $userIds;
	$personCursor = $apiObj->mongoFindOne(array('_id'=>$_GET['_parentId']));
	//$personCursor->sort(array('firstName'=>1));
	// $personCursor->limit(100);
	$app->render('appointment.php', array('personCursor' => $personCursor,'$_GET' => $_GET, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/updateModal', function () use ($app, $settings, $apiObj){
	$_POST = $_POST;
	$apiObj->mongoSetDB($settings['database']);
	$userIds = $apiObj->getUserIds();
	$apiObj->mongoSetCollection("person");
	$collectionQuery = array('_id'=>$_POST['_parentId']);
	$personCursor = $apiObj->mongoFindOne($collectionQuery);
	//$personCursor->sort(array('firstName'=>1));
	//$personCursor->limit(100);
	$userCursor = array();
	$attachedUsers = array();
	$acceptedUsers = array();
	$apiObj->mongoSetCollection('user');
	$userFind = $apiObj->mongoFind();
	foreach($userFind as $userFound){
		if($userFound['_id']==$_POST['createdBy']){
			$createdName = ucwords(strtolower($userFound['firstname'])).' '.ucwords(strtolower($userFound['lastname']));
		}
		array_push($userCursor, $userFound);
	}
	$apiObj->mongoSetCollection('appointment');
	$attachedQuery = array('_id'=>$_POST['_id']);
	$attachedCursor = $apiObj->mongoFindOne($attachedQuery);
	if((!empty($attachedCursor['invitesAccepted']))&&(is_array($attachedCursor['invitesAccepted']))){
		foreach($attachedCursor['invitesAccepted'] as $accepted){
			$a = $accepted['agentId'];
			array_push($attachedUsers, $a);
			array_push($acceptedUsers, $a);
		}
	}
	if((!empty($attachedCursor['invitesPending']))&&(is_array($attachedCursor['invitesPending']))){
		foreach($attachedCursor['invitesPending'] as $pending){
			$a = $pending['agentId'];
			array_push($attachedUsers, $a);
		}
	}
	$app->render('appointmentUpdate.php', array('attachedCursor' => $attachedCursor,'attachedUsers' => $attachedUsers,'acceptedUsers' => $acceptedUsers,'userCursor' => $userCursor,'personCursor' => $personCursor,'createdName' => $createdName, '$_POST' => $_POST, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/notificationController', function () use ($app, $settings, $apiObj){
	$result = 'User not Logged in';
	if($apiObj->userLoggedIn()){
		$result = array();
		$invites = array();
		$acceptedUsers = array();
		$collectionQuery['$or'][]['invitesAccepted.agentId']['$regex'] = new MongoRegex("/".$_SESSION['api']['user']['_id'].".*/i");
		$collectionQuery['$or'][]['invitesPending.agentId']['$regex'] = new MongoRegex("/".$_SESSION['api']['user']['_id'].".*/i");
		$inviteAcceptedQuery = array(
			'invitesAccepted.agentId'=>$_SESSION['api']['user']['_id']
		);
		$apiObj->mongoSetDB($settings['database']);
		$apiObj->mongoSetCollection("appointment");
		$appointmentCursor = $apiObj->mongoFind($collectionQuery);
		$inviteCursor = $apiObj->mongoFind($inviteAcceptedQuery);
		foreach($inviteCursor as $inviteapps){
			foreach($inviteapps['invitesAccepted'] as $accptd){
				if($inviteapps['day']==date('m/d/Y')){
					$a=$accptd;
					array_push($acceptedUsers, $a);
				}
			}
		}
		foreach($appointmentCursor as $appointment){
			$personQuery = array(
				'_id'=>$appointment['_parentId']
			);
			$adressQuery = array(
				'_parentId'=>$appointment['_parentId']
			);
			$apiObj->mongoSetCollection("person");
			$personCursor= $apiObj->mongoFindOne($personQuery);
			$apiObj->mongoSetCollection("addresses");
			$addressCursor= $apiObj->mongoFindOne($adressQuery);
			$date = $appointment['day'].' '.$appointment['time'];
			$a['title'] = $appointment['title'];
			$a['start'] = date(DATE_ISO8601, strtotime($date));
			$a['end'] = date(DATE_ISO8601, strtotime($date)+3600);
			$a['day'] = $appointment['day'];
			$a['time'] = $appointment['time'];
			$a['parentId'] = $appointment['_parentId'];
			$a['person'] = ucwords(strtolower($personCursor['firstName'])).' '.ucwords(strtolower($personCursor['lastName'])). ' of '.$addressCursor['state'];
			$a['createdBy'] = $appointment['_createdBy'];
			$a['id'] = $appointment['_id'];
			$a['notes'] = $appointment['notes'];
			if((!empty($appointment['invitesPending']))&&($appointment['invitesPending'] != 'undefined')&&(!strpos(serialize($appointment['invitesAccepted']),$appointment['_createdBy']))){
				foreach($appointment['invitesPending'] as $pendingInvites){
					if(($pendingInvites['agentId'] == $_SESSION['api']['user']['_id'])&&($appointment['day']>=date('m/d/Y'))){
						$userQuery= array('_id'=>$pendingInvites['_createdBy']);
						$apiObj->mongoSetCollection("user");
						$userCusor= $apiObj->mongoFindOne($userQuery);
						if(!empty($userCusor['firstName'])){
							$a['host']= ucwords(strtolower($userCusor['firstName'])).' '.ucwords(strtolower($userCusor['lastName']));
						}else{
							$a['host']= ucwords(strtolower($userCusor['firstname'])).' '.ucwords(strtolower($userCusor['lastname']));
						}
						$a['inviteId']=$pendingInvites['_id'];
						array_push($invites, $a);
					}
				}
			}
			if(!empty($appointment['invitesAccepted'])){
				foreach($appointment['invitesAccepted'] as $acceptedInvites){
					if(($appointment['day']==date('m/d/Y'))){
						$a['inviteId']=$acceptedInvites['_id'];
						array_push($result, $a);
					}
				}
			}
		}
	}
	$app->render('calendarNotifications.php', array('result' => $result, 'acceptedUsers' => $acceptedUsers,'invites' => $invites, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->run();