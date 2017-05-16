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

	$result=array();

	if((!empty($_GET['filter']))||($_GET['filter']!='')){

		$status=$_GET['filter'];

	}else{

		$status='OPEN';

	}

	$apiObj = new apiclass($settings);

	$apiObj->mongoSetDB($settings['database']);

	$apiObj->mongoSetCollection("issues");

	if((!empty($_SESSION['api']['user']['department']))&&((strtoupper($_SESSION['api']['user']['permissionLevel'])=='ADMINISTRATOR')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='MANAGER')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='INSUREHC') )){

		if(strtoupper($_SESSION['api']['user']['permissionLevel'])=='ADMINISTRATOR'){

			$query=array('status'=>$status);

		}else{

			$query=array('status'=>$status,'department'=>strtoupper($_SESSION['api']['user']['department']));

		}

	}else{

		$query=array('_parentId'=>$_SESSION['api']['user']['_id'],'status'=>$status);

	}

	$cursor=$apiObj->mongoFind($query);

	$cursor->sort(array('_timestampCreated'=>-1));

	foreach($cursor as $issue){

		$apiObj->mongoSetCollection("user");

		$user=$apiObj->mongoFindOne(array('_id'=>$issue['_parentId']));

		$issue['_parentId']= $user['firstname'].' '.$user['lastname'];

		if($issue['read']=='UNREAD'){

			$issue['read']='<i class="fa fa-eye-slash"></i>';

			$issue['status']='NEW';

		}

		if($issue['read']=='READ'){

			$issue['read']='<i class="fa fa-eye"></i>';

		}

		array_push($result,$issue);

	}

	$app->render('tracker.php', array('result' => $result,'apiObj' => $apiObj));

})->via('GET','POST');

$app->map('/view/:issueId', function ($issueId) use ($app, $settings)  {

	$result=array();

	$comments=array();

	$apiObj = new apiclass($settings);

	$apiObj->mongoSetDB($settings['database']);

	$apiObj->mongoSetCollection("issues");

	$result=$apiObj->mongoFindOne(array('_id'=>$issueId));

	$apiObj->mongoSetDB($settings['database']);

	$apiObj->mongoSetCollection("user");

	$user=$apiObj->mongoFindOne(array('_id'=>$result['_parentId']));

	$result['_parentId']= $user['firstname'].' '.$user['lastname'];

	$read=$result['read'];

	if(!empty($result['comments'])){

		foreach($result['comments'] as $comment){

			$apiObj->mongoSetCollection("user");

			$userCursor = $apiObj->mongoFindOne(array('_id'=>$comment['_createdBy']));

			$comment['name']= $userCursor['firstname'].'  '.$userCursor['lastname'];

			array_push($comments,$comment);

		}

	}

	if(($_SESSION['api']['user']['permissionLevel']=='administrator')||($_SESSION['api']['user']['permissionLevel']=='ADMINISTRATOR')&&($read!='READ')){

		$data['user_0_id']=$result['_createdBy'];

		$data['user_0_issues_0_id']=$result['_id'];

		$data['user_0_createThing']='Y';

		$data['user_0_issues_0_createThing']='Y';

		$data['user_0_issues_0_read']='READ';

		$data['user_0_issues_0_status']='OPEN';

		$data['user_0_issues_0_label']='primary';

		$apiObj->save_things($data);

	}

	$app->render('viewissue.php', array('result' => $result,'apiObj' => $apiObj,'comments' => $comments));

})->via('GET','POST');

$app->map('/create', function () use ($app, $settings)  {

	$app->render('createissue.php', array('result' => $result,'apiObj' => $apiObj));

})->via('GET','POST');

$app->run();