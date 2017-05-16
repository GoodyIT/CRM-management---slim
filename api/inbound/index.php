<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../app.php';
$app->config(array(
	'templates.path' => './',
));



$app->map('/', function () use ($app,$settings) {
echo "Please Provide Lead Source.";	
	
})->via('GET','POST');


$app->map('/preciseleads', function () use ($app,$settings) {
	
try {
		$vars[] = 'first_name';
		$vars[] = 'last_name';
		$vars[] = 'email';
		$vars[] = 'phone1';
		$vars[] = 'phone2';
		$vars[] = 'address1';
		$vars[] = 'zip';
		$vars[] = 'city';
		$vars[] = 'state';
		$vars[] = 'bestTime';
		$vars[] = 'ip';
		$vars[] = 'occupation';
		$vars[] = 'income';
		$vars[] = 'self';
		$vars[] = 'homeowner';
		$vars[] = 'gender';
		$vars[] = 'birth';
		$vars[] = 'marital';
		$vars[] = 'kids';
		$vars[] = 'physician';
		$vars[] = 'hospitalized';
		$vars[] = 'height1';
		$vars[] = 'height2';
		$vars[] = 'weight';
		$vars[] = 'smoke';
		$vars[] = 'insured';
		$vars[] = 'insComp';
		$vars[] = 'PreEx';
		$vars[] = 'pregnant';
		$vars[] = 'deniedInsurance';
		$vars[] = 'meds';
		$vars[] = 'medList';
		$vars[] = 'lifeEvent';
		$vars[] = 'sp_first_name';
		$vars[] = 'sp_last_name';
		$vars[] = 'sp_gender';
		$vars[] = 'sp_birth';
		$vars[] = 'sp_height1';
		$vars[] = 'sp_height2';
		$vars[] = 'sp_weight';
		$vars[] = 'sp_smoke';
		$vars[] = 'sp_physician';
		$vars[] = 'sp_hospitalized';
		$vars[] = 'sp_preEx';
		$vars[] = 'sp_meds';
		if((!empty($_REQUEST["kids"])) && ($_REQUEST["kids"] > 0)){
			for ($i = 1; $i <=$_REQUEST["kids"]; $i++) { 
			$vars[] = 'ch_'.$i.'_firstName';
			$vars[] = 'ch_'.$i.'_birth';
			$vars[] = 'ch_'.$i.'_gender';
			}
		}
		
		foreach($vars as $key=>$var){
			if(empty($_REQUEST[$var])){
				$_REQUEST[$var] = "";
			}
		}
		
		
	if($_REQUEST['smoke'] == "1"){
		$_REQUEST['smoke'] = "Y";
	} else {
		$_REQUEST['smoke']  = "N";
	}	
		
	$lead['person_0_createThing'] = "Y";
	$lead['person_0_title'] = "";
	$lead['person_0_firstName'] = $_REQUEST['first_name'];
	$lead['person_0_lastName'] = $_REQUEST['last_name'];
	$lead['person_0_suffix'] = "";
	$lead['person_0_gender'] = $_REQUEST['gender'];
	$lead['person_0_socialSecurityNumber'] = "";
	$lead['person_0_dateOfBirth'] =  $_REQUEST['birth'];
	$lead['person_0_smokerTabacco'] =  $_REQUEST['smoke'];
	$lead['person_0_assignedTo'] = "20151005154138-k7N1dHZi-4I7ZoB2J";
	$lead['person_0_leadSource'] = "PRECISELEADS";
	

	if(trim($_REQUEST['phone1']) <> ""){
	$lead['person_0_phones_0_createThing'] = "Y";
	$lead['person_0_phones_0_phoneNumber'] = $_REQUEST['phone1'];
	$lead['person_0_phones_0_phoneType'] = "PRIMARY";
	}
	if(trim($_REQUEST['phone2']) <> ""){
	$lead['person_0_phones_1_createThing'] = "Y";
	$lead['person_0_phones_1_phoneNumber'] = $_REQUEST['phone2'];
	$lead['person_0_phones_1_phoneType'] = "SECONDARY";
	}

	if(trim($_REQUEST['email']) <> ""){
	$lead['person_0_emails_0_createThing'] = "Y";
	$lead['person_0_emails_0_email'] = $_REQUEST['email'];
	$lead['person_0_emails_0_type'] = "PRIMARY";
	}

	if(trim($_REQUEST['email']) <> ""){
	$lead['person_0_emails_0_createThing'] = "Y";
	$lead['person_0_emails_0_email'] = $_REQUEST['email'];
	$lead['person_0_emails_0_type'] = "PRIMARY";
	}

	
	if($_REQUEST['sp_first_name'] <> ""){
		$lead['person_0_spouse_0_createThing'] = "N";
		$lead['person_0_spouse_0_spouseFirstName'] = $_REQUEST['sp_first_name'];
		$lead['person_0_spouse_0_spouseLastName'] = $_REQUEST['sp_last_name'];
		$lead['person_0_spouse_0_spouseGender'] = $_REQUEST['sp_gender'];
		$lead['person_0_spouse_0_spouseDateOfBirth'] = $_REQUEST['sp_birth'];
		$lead['person_0_spouse_0_spouseSmoker'] = $_REQUEST['sp_smoke'];
	}

	$lead['person_0_addresses_0_createThing'] = "Y";
	$lead['person_0_addresses_0_street1'] = $_REQUEST['address1'];
	$lead['person_0_addresses_0_city'] = $_REQUEST['city'];
	$lead['person_0_addresses_0_state'] = $_REQUEST['state'];
	$lead['person_0_addresses_0_zipCode'] =  $_REQUEST['zip'];



	$note_string = "";
	foreach($vars as $key=>$var){
			$note_string  .= "<br>".$var. " : ". $_REQUEST[$var] . "\n";	
	}
	$lead['person_0_notes_0_createThing'] = "Y";
	$lead['person_0_notes_0_information'] = $note_string;

	if(($_REQUEST['first_name'] <> "") && ($_REQUEST['last_name'] <> "")){
		$apiObj = new apiclass($settings);
		$apiObj->mongoSetDB($settings['database']);
		$apiObj->save_things($lead);
		

		$results['code'] = "10001"; 
		$results['result'] = "Success"; 
		$results['message'] = "Lead Created"; 
		$response = $app->response();
		$response['Content-Type'] = 'application/json';
		$response['X-Powered-By'] = 'Thing Engine';
		$response->status(200);
		$response->body(json_encode($results));
	} else {
		
		$results['code'] = "10002"; 
		$results['result'] = "Error"; 
		$results['message'] = "Lead Was Not Created"; 
		$response = $app->response();
		$response['Content-Type'] = 'application/json';
		$response['X-Powered-By'] = 'Thing Engine';
		$response->status(200);
		$response->body(json_encode($results));
	}
	
	} catch (Exception $e) {
		$results['code'] = "10003"; 
		$results['result'] = "Error"; 
		$results['message'] = "Lead Was Not Created"; 
		$response = $app->response();
		$response['Content-Type'] = 'application/json';
		$response['X-Powered-By'] = 'Thing Engine';
		$response->status(200);
		$response->body(json_encode($results));
}
	
	
	
})->via('GET','POST');

$app->run();