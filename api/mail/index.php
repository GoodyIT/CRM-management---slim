<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
$settings['allow_all'] = TRUE;
require '../app.php';
use Mailgun\Mailgun;
$app->config(array(
	'templates.path' => './',
));
$app->map('/', function () use ($app, $settings)  {
	if(empty($_GET['folder'])){
		$_GET['folder'] = 'INBOX';
	}
	$result = array();
	$unread = array();
	$draft = array();
	$important = array();
	if(!empty($_GET['mail_page'])){
		$settings['mail']['page'] = $_GET['mail_page'];
	}else{
		$settings['mail']['page'] = 0;
	}
	$settings['mail']['mail_per_page'] = 15;
	$settings['mail']['search'] = "";
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	if(!empty($_GET['term'])){
		$query['$and'][0]['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
		$query['$and'][0]['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
		$query['$and'][0]['$or'][]['from']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
		$query['$and'][0]['$or'][]['_createdBy']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['id'].")\b/i");
		$query['$and'][1]['$or'][]['to']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['from']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['recipient']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['subject']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['bodyHtml']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['bodyPlain']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
		$query['$and'][1]['$or'][]['strippedSignature']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
	}else{
		if($_GET['folder'] != 'TRASH'){
			if(($_GET['folder'] == 'INBOX')||($_GET['folder'] == 'IMPORTANT')){
				$query['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
				$query['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			}
			if(($_GET['folder'] == 'IMPORTANT')){
				$query['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
				$query['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
				$query['$or'][]['from']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			}
			if($_GET['folder'] == 'SENT'){
				$query['$or'][]['from']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			}
			if($_GET['folder'] == 'DRAFT'){
				$query['_createdBy'] =$_SESSION['api']['user']['id'];
			}
			$query['folder'] = $_GET['folder'];
			$query['trash'] = 'N';
		}else{
			$query['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			$query['$or'][]['from']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			$query['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
			$query['trash'] = 'Y';
		}
	}
	$countQuery['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
	$countQuery['$or'][]['from']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
	$countQuery['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
	$countQuery['trash'] = 'N';
	$emailCursor = $apiObj->mongoFind($query);
	$countCursor = $apiObj->mongoFind($countQuery);
	$emailCursor->sort(array('_timestampCreated'=>-1));
	$emailCursor->limit($settings['mail']['mail_per_page']);
	$emailCursor->skip($settings['mail']['mail_per_page'] * $_GET['mail_page']);
	$settings['mail']['total'] = $emailCursor->count();
	foreach($emailCursor as $email){
		array_push($result, $email);
	}
	foreach($countCursor as $count){
		if(($count['folder']=='INBOX')&&($count['state']=='UNREAD')){
			array_push($unread, $count);
		}
		if(($count['folder']=='IMPORTANT')&&($count['state']=='UNREAD')){
			array_push($important, $count);
		}
		if(($count['folder']=='DRAFT')){
			array_push($draft, $count);
		}
	}
	$app->render('mailbox.php', array('result' => $result, 'unread' => $unread, 'important' => $important, 'settings'=>$settings, 'GET'=>$_GET, 'draft' => $draft, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/notificationController', function () use ($app, $settings)  {
	$read = array();
	$unread = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	$countQuery['$or'][]['to']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
	$countQuery['$or'][]['recipient']['$regex'] = new MongoRegex("/\b(".$_SESSION['api']['user']['email'].")\b/i");
	$countCursor = $apiObj->mongoFind($countQuery);
	$countCursor->sort(array('_timestampCreated'=>-1));
	foreach($countCursor as $count){
		if(($count['folder']=='INBOX')&&($count['trash']!='Y')){
			array_push($read, $count);
		}
		if(($count['folder']=='INBOX')&&($count['trash']!='Y')&&($count['state']=='UNREAD')){
			array_push($unread, $count);
		}
	}
	$app->render('mailnotifications.php', array('unread' => $unread, 'read' => $read, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/function/:func/:action', function ($func, $action) use ($app, $settings)  {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	foreach($_POST as $id){
		$data['mail_0_id']= $id;
		$data['mail_0_createThing']='Y';
		$data['mail_0_'.$func]=$action;
		$apiObj->save_things($data);
	}
	print_r($_POST);
})->via('GET','POST');
$app->map('/attSend', function () use ($app, $settings)  {
	if(!empty($_FILES)){
		if(!empty($_POST['attachmentCount'])){
			$i = $_POST['attachmentCount'];
		}else{
			$i=0;
		}
		$_POST['mail_0_mailAttachments_0_createThing'] = 'N';
		foreach($_FILES['file']['name'] as $key=>$attachment){
			$info = pathinfo($attachment);
			$ext = $info['extension']; // get the extension of the file
			$newname = str_replace('/tmp/','',$_FILES['file']['tmp_name'][$key]).'.'.$ext;
			$target = __DIR__.'/files/'.$newname;
			move_uploaded_file( $_FILES['file']['tmp_name'][$key], $target);
			$_POST['mail_0_mailAttachments_'.$i.'_tmpName'] = $_FILES['file']['tmp_name'][$key];
			$_POST['mail_0_mailAttachments_'.$i.'_size'] = $_FILES['file']['size'][$key];
			$_POST['mail_0_mailAttachments_'.$i.'_type'] = $_FILES['file']['type'][$key];
			$_POST['mail_0_mailAttachments_'.$i.'_error'] = $_FILES['file']['error'][$key];
			$_POST['mail_0_mailAttachments_'.$i.'_name'] = $attachment;
			$i++;
		}
		$_POST['mail_0_attachmentCount'] = $i;
	}
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($_POST));
})->via('GET','POST');
$app->map('/zip/:emailId', function ($emailId) use ($app, $settings)  {
	chdir(__DIR__.'/files/');
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	$email = $apiObj->mongoFindOne(array('_id'=>$emailId));
	$zipname = 'Attachments'.$apiObj->getRandomString(8).'.zip';
	$zip = new ZipArchive();
	$zip->open($zipname, ZipArchive::CREATE);
	foreach($email['mailAttachments'] as $attachment){
		$info = pathinfo($attachment['name']);
		$ext = $info['extension'];
		$oldname = str_replace('/tmp/','', $attachment['tmpName']).'.'.$ext;
		$zip->addFile($oldname, $attachment['name']);
	}
	$zip->close();
	header('Content-Type: application/zip');
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zipname);
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);
	echo "<script>window.close();</script>";
})->via('GET','POST');
$app->map('/view/:emailId', function ($emailId) use ($app, $settings)  {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	$email = $apiObj->mongoFindOne(array('_id'=>$emailId));
	if($email['state']!='READ'){
		$result['mail_0_createThing'] = 'Y';
		$result['mail_0_id']=$emailId;
		$result['mail_0_state']='READ';
		$apiObj->save_things($result);
	}
	$app->render('mailview.php', array("apiObj"=>$apiObj, 'email'=>$email));
})->via('GET','POST');
$app->map('/compose', function () use ($app, $settings)  {
	$result = array();
	if(!empty($_GET['emailId'])){
		$apiObj = new apiclass($settings);
		$apiObj->mongoSetDB($settings['database']);
		$apiObj->mongoSetCollection("mail");
		$email = $apiObj->mongoFindOne(array('_id'=>$_GET['emailId']));
	}
	$app->render('mailcompose.php', array('result' => $result, 'email' => $email, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/contact/search', function () use ($app, $settings)  {
	$result = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("person");
	$nameparts = explode(' ', $_GET['term']);
	if(count($nameparts) > 1){
		$collectionQuery['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		$collectionQuery['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
	} else {
		if(!empty($nameparts[0])){
			$collectionQuery['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
			$collectionQuery['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		}
	}
	//$collectionQuery['$or'][]['firstName']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
	//$collectionQuery['$or'][]['lastName']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
	$personCursor = $apiObj->mongoFind($collectionQuery);
	$personCursor->sort(array('firstName'=>1));
	foreach($personCursor as $person){
		//if(in_array($person['assignedTo'], $userIds)){
		$apiObj->mongoSetCollection('emails');
		$emailQuery = array('_parentId'=>$person['_id']);
		$emailCursor = $apiObj->mongoFindOne($emailQuery);
		$a['label']=$person['firstName'].' '.$person['lastName'];
		$a['value']=$emailCursor['email'];
		$a['desc']=$emailCursor['email'];
		if(!empty($emailCursor['email'])){
			array_push($result, $a);
		}
		//}
	}
	$apiObj->mongoSetCollection("emails");
	$collectionQuery['$or'][]['email']['$regex'] = new MongoRegex("/".$_GET['term'].".*/i");
	$mailCursor = $apiObj->mongoFind($collectionQuery);
	$mailCursor->sort(array('email'=>1));
	foreach($mailCursor as $mail){
		//if(in_array($person['assignedTo'], $userIds)){
		$apiObj->mongoSetCollection('person');
		$personQuery = array('_id'=>$mail['_parentId']);
		$personCursor = $apiObj->mongoFindOne($personQuery);
		$b['label']=$personCursor['firstName'].' '.$personCursor['lastName'];
		$b['value']=$mail['email'];
		$b['desc']=$mail['email'];
		array_push($result, $b);
		//}
	}
	$apiObj->mongoSetCollection("user");
	$nameparts = explode(' ', $_GET['term']);
    $collectionQuery2['status']['$eq'] = "ACTIVE";
	if(count($nameparts) > 1){
		$collectionQuery2['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		$collectionQuery2['lastname']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
	} else {
		if(!empty($nameparts[0])){
			$collectionQuery2['$or'][]['email']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
			$collectionQuery2['$or'][]['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
			$collectionQuery2['$or'][]['lastname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
		}
	}
	$userCursor = $apiObj->mongoFind($collectionQuery2);
	$userCursor->sort(array('firstname'=>1));
	foreach($userCursor as $user){
		//if(in_array($person['assignedTo'], $userIds)){
		$b['label']=$user['firstname'].' '.$user['lastname'];
		$b['value']=$user['email'];
		$b['desc']=$user['email'];
		array_push($result, $b);
		//}
	}
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($result,JSON_PRETTY_PRINT));
})->via('GET','POST');
// Receive emails from Mailgun
$app->map('/incomingBROKKEN', function () use ($app, $settings)  {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	$mongo = new MongoClient();
	$mongo->$settings['database']->mailtest->save($_REQUEST);
	if(!empty($_REQUEST)){
		foreach($_REQUEST as $key=>$value){
			if($key == 'id'){$key = 'mailGunId';}
			$key = str_replace("-", " ", $key);
			$key = ucwords(strtolower($key));
			$_REQUEST['mail_0_'.lcfirst(preg_replace("/[^A-Za-z0-9]/", '', $key))] = $value;
		}
		//$mailAddresses=explode(',', str_replace(' ', '', $_REQUEST['recipient']));
		//if(!empty($mailAddresses)){
		//	foreach($mailAddresses as $mailAddress){
		//		if(!empty(trim($mailAddress))){
		//			$_REQUEST['mail_0_to']=$mailAddress;
		//			$apiObj->save_things($_REQUEST);
		//		}
		//	}
		//}
		$_REQUEST['mail_0_createThing'] = 'Y';
		$_REQUEST['mail_0_folder'] = 'INBOX';
		$_REQUEST['mail_0_state'] = 'UNREAD';
		$_REQUEST['mail_0_trash'] = 'N';
		if(!empty($_FILES)){
			try{
				$i = 0;
				foreach($_FILES as $attachment){
					$info = pathinfo($attachment['name']);
					$ext = $info['extension']; // get the extension of the file
					$newname = str_replace('/tmp/','',$attachment['tmp_name']).'.'.$ext;
					$target = __DIR__.'/files/'.$newname;
					move_uploaded_file( $attachment['tmp_name'], $target);
					$i++;
					$_REQUEST['mail_0_mailAttachments_0_createThing'] = 'N';
					foreach($attachment as $attkey=>$attvalue){
						$attkey = str_replace("_", " ", $attkey);
						$attkey = ucwords(strtolower($attkey));
						$_REQUEST['mail_0_mailAttachments_'.$i.'_'.lcfirst(preg_replace("/[^A-Za-z0-9]/", '', $attkey))] = $attvalue;
					}
				}
			} catch (Exception $e) {
			}
		}
		$mongo->$settings['database']->mailtest->save($_REQUEST);
		$apiObj->save_things($_REQUEST);
		echo "Saving";
	}
})->via('GET','POST');
$app->map('/incoming', function () use ($app, $settings)  {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	if(!empty($_REQUEST)){
		foreach($_REQUEST as $key=>$value){
			if($key == 'id'){$key = 'mailGunId';}
			$key = str_replace("-", " ", $key);
			$key = ucwords(strtolower($key));
			$_REQUEST['mail_0_'.lcfirst(preg_replace("/[^A-Za-z0-9]/", '', $key))] = $value;
		}
		$_REQUEST['mail_0_createThing'] = 'Y';
		$_REQUEST['mail_0_folder'] = 'INBOX';
		$_REQUEST['mail_0_state'] = 'UNREAD';
		$_REQUEST['mail_0_trash'] = 'N';
		if(!empty($_FILES)){
			try{
				$i = 0;
				foreach($_FILES as $attachment){
					$info = pathinfo($attachment['name']);
					$ext = $info['extension']; // get the extension of the file
					$newname = str_replace('/tmp/','',$attachment['tmp_name']).'.'.$ext;
					$target = __DIR__.'/files/'.$newname;
					move_uploaded_file( $attachment['tmp_name'], $target);
					$i++;
					$_REQUEST['mail_0_mailAttachments_0_createThing'] = 'N';
					foreach($attachment as $attkey=>$attvalue){
						$attkey = str_replace("_", " ", $attkey);
						$attkey = ucwords(strtolower($attkey));
						$_REQUEST['mail_0_mailAttachments_'.$i.'_'.lcfirst(preg_replace("/[^A-Za-z0-9]/", '', $attkey))] = $attvalue;
					}
				}
			} catch (Exception $e) {
			}
		}
		$apiObj->save_things($_REQUEST);
		echo "Saving";
	}
})->via('GET','POST');
// Send emails via Mailgun
$app->map('/send', function () use ($app, $settings)  {
	$domain_parts = explode('@',$_POST['mail_0_to']);
	if(!empty($domain_parts[1])){
		$domto=$domain_parts[1];
	} else {
		$domto=$domain_parts[0];
	}
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$_POST['mail_0_timestamp'] = date('YmdHis');
	$_POST['mail_0_date'] = date('YmdHis');
	$_POST['mail_0_direction'] = 'OUT';
	$_POST['mail_0_trash'] = 'N';
	$_POST['mail_0_sendCode'] = date('YmdHis').'-'.$apiObj->getRandomString(8);
	//print_r($_POST); exit();
	//$apiObj->saveAll($_POST,"email");
	$apiObj->save_things($_POST);
	if(in_array(str_replace(', ','',$domto),$settings['mailgun']['domains'])){
		$_POST['mail_0_timestamp'] = date('YmdHis');
		$_POST['mail_0_date'] = date('YmdHis');
		$_POST['mail_0_direction'] = 'IN';
		$_POST['mail_0_sender'] = $_POST['from'];
		$_POST['mail_0_folder'] = 'INBOX';
		//$apiObj->saveAll($_POST,"email");
		$apiObj->save_things($_POST);
	}
	if(!in_array(str_replace(', ','',$domto),$settings['mailgun']['domains'])){
		$mgClient = new Mailgun($settings['mailgun']['apikey']);
		//$domain = substr($_SESSION['api']['user']['email'], strpos($_SESSION['api']['user']['email'], "@") + 1);
		$email_parts = explode('@',$_SESSION['api']['user']['email']);
		if(!empty($email_parts[1])){
			$domain=$email_parts[1];
		} else {
			$domain=$email_parts[0];
		}
		$data = array(
			'from'    => $_SESSION['api']['user']['firstname'].' '.$_SESSION['api']['user']['lastname'].' <'.$_SESSION['api']['user']['email'].'>',
			'to'      => $_POST['mail_0_to'],
			'subject' => $_POST['mail_0_subject'],
			'text'    => html_entity_decode($_POST['mail_0_bodyHtml']),
			'html'    => $_POST['mail_0_bodyHtml']
		);
		$recipients = explode(',', str_replace(' ', '', $_POST['mail_0_to']));
		if($recipients > 1){
			foreach($recipients as $recipient){
				if(!empty($recipient)){
					$a[$recipient]['uniqueId'] = $apiObj->getRandomString(8);
				}
			}
			$data['recipient-variables'] = json_encode($a);
		}
		if(!empty($_POST['mail_0_mailAttachments_0_createThing'])){
			$attachments = array();
			$apiObj->mongoSetDB($settings['database']);
			$apiObj->mongoSetCollection("mail");
			$att=$apiObj->mongoFindOne(array('sendCode'=>$_POST['mail_0_sendCode']));
			foreach($att['mailAttachments'] as $file){
				$info = pathinfo($file['name']);
				$ext = $info['extension'];
				$newname = str_replace('/tmp/','',$file['tmpName']).'.'.$ext;
				$target = __DIR__.'/files/'.$newname;
				array_push($attachments, $target);
			}
			$attach= array('attachment'=>$attachments);
			$result = $mgClient->sendMessage($domain, $data, $attach);
		}else{
			$result = $mgClient->sendMessage($domain, $data);
		}
	}
})->via('GET','POST');
$app->map('/customer/:emailId', function ($emailId) use ($app, $settings)  {
	$result = array();
	$unread = array();
	$draft = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("mail");
	$query['$or'][0]['to']['$regex'] = new MongoRegex("/".$emailId.".*/i");
	$query['$or'][1]['from']['$regex'] = new MongoRegex("/".$emailId.".*/i");
	$query['$or'][2]['recipient']['$regex'] = new MongoRegex("/".$emailId.".*/i");
	$emailCursor = $apiObj->mongoFind($query);
	$emailCursor->sort(array('timestamp'=>-1));
	$emailCursor->limit(50);
	foreach($emailCursor as $email){
		if(!empty($_GET['folder'])){
			if(($_GET['folder']=='TRASH') && ($email['trash'] == 'Y')){
				array_push($result, $email);
			}else{
				if($email['folder']==$_GET['folder']){
					array_push($result, $email);
				}
				if($email['state']=='UNREAD'){
					array_push($unread, $email);
				}
				if($email['folder']=='DRAFT'){
					array_push($draft, $email);
				}
			}
		}else{
			if($email['trash'] != 'Y'){
				if($email['state']=='UNREAD'){
					array_push($unread, $email);
				}
				if($email['folder']=='INBOX'){
					array_push($result, $email);
				}
				if($email['folder']=='DRAFT'){
					array_push($draft, $email);
				}
			}
		}
	}
	if(empty($_GET['folder'])){
		$_GET['folder'] = 'INBOX';
	}
	$app->render('customermail.php', array('result' => $result, 'unread' => $unread, 'GET'=>$_GET, 'draft' => $draft, "apiObj"=>$apiObj));
})->via('GET','POST');
// create lead in Vici - BrokerOffice List
// http://97.93.171.189/vicidial/non_agent_api.php?source=test&user=1099&pass=463221&function=add_lead&phone_number=7609022211&phone_code=1&list_id=996&dnc_check=N&first_name=Trent&last_name=Ramseyer
/*
vendor_lead_code -	1-20 characters
source_id  -		1-50 characters
gmt_offset_now -	overridden by auto-lookup of phone_code and area_code portion of phone number if applicable
title -			1-4 characters
first_name -		1-30 characters
middle_initial -	1 character
last_name -		1-30 characters
address1 -		1-100 characters
address2 -		1-100 characters
address3 -		1-100 characters
city -			1-50 characters
state -			2 characters
province -		1-50 characters
postal_code -		1-10 characters
country_code -		3 characters
gender -		U, M, F (Undefined, Male, Female) - defaults to 'U'
date_of_birth -		YYYY-MM-DD
alt_phone -		1-12 characters
email -			1-70 characters
*/
// Run App
$app->run();