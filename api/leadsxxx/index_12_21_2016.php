<?php
require '../app.php';
$app->config(array(
	'templates.path' => './',
));



$app->get('/', function () use ($app,$settings) {
	$result['leads'] = array();
	$apiObj = new apiclass($settings);
	$docIds = array();
	$result['page_label'] = "Leads";
	/*
        $apiObj->mongoSetDB($settings['database']);
        $post['userGroups_0_createThing'] = "Y";
        $post['userGroups_0_id'] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
        $post['userGroups_0_label'] = "Sales";
        $post['userGroups_0_users_0_userId'] = "20151008135931-V2t9vDNy-JMLru8mr";
        $post['userGroups_0_users_0_level'] = "user";
        $apiObj->save_things($post);
    */

	if($apiObj->userLoggedIn()){
		$apiObj->mongoSetDB($settings['database']);
		$userIds = $apiObj->getUserIds();
		$apiObj->mongoSetCollection("person");
		$collectionQuery = false;
		if(trim($settings['leads']['search']) == ""){
			$collectionQuery['assignedTo']['$in'] = $userIds;
			$collectionQuery['$and'][]['disposition']['$ne'] = "SOLD";
			$collectionQuery['$and'][]['disposition']['$ne'] = "ONHOLD";
		}
		// $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
		// $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
		if(trim($settings['leads']['search']) <> ""){
			$searchType = "NAME";
			$settings['leads']['search'] = trim($settings['leads']['search']);
			$isPhone = trim(preg_replace("/[^0-9]/", "", $settings['leads']['search']));
			if( ( (strlen($isPhone) == "4") || (strlen($isPhone) == "7") || (strlen($isPhone) == "10") ) && (is_numeric($isPhone))){
				$searchType = "PHONE";
				$phoneParents = array();
				$apiObj->mongoSetCollection("phones");
				$phone = $apiObj->displayPhoneNumber($settings['leads']['search'], TRUE);
				//$collectionQueryPhone['phoneNumber']['$eq'] = $phone;
				$collectionQueryPhone['phoneNumber']['$regex'] = new MongoRegex("/".$isPhone.".*/i");
				$cursorPhone = $apiObj->mongoFind($collectionQueryPhone);
				if(!empty($cursorPhone)){
					foreach (iterator_to_array($cursorPhone) as $doc) {

						$phoneParents[] = $doc['_parentId'];
					}
				}
				$apiObj->mongoSetCollection("person");
				$collectionQuery['_id']['$in'] = $phoneParents;
			}

			if (!filter_var($settings['leads']['search'], FILTER_VALIDATE_EMAIL) === false) {
				$searchType = "EMAIL";
				$emailParents = array();
				$apiObj->mongoSetCollection("emails");
				$collectionQueryEmail['email']['$eq'] = trim($settings['leads']['search']);
				$cursorEmail = $apiObj->mongoFind($collectionQueryEmail);
				if(!empty($cursorEmail)){
					foreach (iterator_to_array($cursorEmail) as $doc) {
						$emailParents[] = $doc['_parentId'];
					}
				}
				$apiObj->mongoSetCollection("person");
				$collectionQuery['$or'][]['_id']['$in'] = $emailParents;
			}

			$nameparts = explode(" ", $settings['leads']['search']);
			if(count($nameparts) > 1){
				foreach($nameparts as $npKey=>$npVal){
					if((strlen($npVal) == "5") && (is_numeric($npVal))){
						$emailParents = array();
						$apiObj->mongoSetCollection("addresses");
						$collectionQueryState['zipCode']['$regex'] = new MongoRegex("/".$npVal.".*/i");
						$cursorState = $apiObj->mongoFind($collectionQueryState);
						if(!empty($cursorState)){
							unset($nameparts[$npKey]);
							foreach (iterator_to_array($cursorState) as $doc) {
								$stateParents[] = $doc['_parentId'];
							}
						}
						$apiObj->mongoSetCollection("person");
						$collectionQuery['_id']['$in'] = $stateParents;

					}

					if(strlen($npVal) == "2"){
						$emailParents = array();
						$apiObj->mongoSetCollection("addresses");
						$collectionQueryState['state']['$regex'] = new MongoRegex("/".$npVal.".*/i");
						$cursorState = $apiObj->mongoFind($collectionQueryState);
						if(!empty($cursorState)){
							unset($nameparts[$npKey]);
							foreach (iterator_to_array($cursorState) as $doc) {
								$stateParents[] = $doc['_parentId'];
							}
						}
						$apiObj->mongoSetCollection("person");
						$collectionQuery['_id']['$in'] = $stateParents;

					}
				}
			}
			//debug($collectionQuery);
			if($searchType == "NAME"){
				if(count($nameparts) > 1){
					$collectionQuery['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					$collectionQuery['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
				} else {
					if(!empty($nameparts[0])){
						$collectionQuery['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
						$collectionQuery['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					}
				}
			}
		}

		$cursor = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor)){
			$cursor->sort(array('_timestampCreated' => -1));
			$x = 0;
			if($cursor->count() == 0){
				$result['total'] = 0;
			} else {
				$result['total'] = $cursor->count();
				if((!empty($settings['leads']['page'])) && ($settings['leads']['page'] > 1)){
					$cursor->skip($settings['leads']['per_page'] * ($settings['leads']['page']-1));
				}
				//echo $cursor->count();
				$cursor->limit($settings['leads']['per_page']);
				foreach (iterator_to_array($cursor) as $doc) {
					$docIds[] = $doc['_id'];
					$result['leads'][] = $apiObj->get_thing_display($doc);
					//$result['leads'][] = $doc;
					$x++;
					if($x == $settings['leads']['per_page']){
						break;
					}
				}
			}
		} else {
			$result['leads'] = array();
		}
	} else {
		echo "User Not Logged In";
		exit();
	}
	//echo "<PRE>";
	//print_r(count($result['leads']));
	//echo "</PRE>";
	// Get Addresses
	$apiObj->mongoSetCollection("addresses");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['addresses'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['addresses'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Phones
	$apiObj->mongoSetCollection("phones");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['phones'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['phones'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Emails
	$apiObj->mongoSetCollection("emails");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['emails'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['emails'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Policies
	$apiObj->mongoSetCollection("policy");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['policies'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['policies'][] = $apiObj->get_thing_display($doc2);
		}
	}
	$app->render('leadlist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
	peakmemory();
	// header("Content-Type: application/json");
	//echo json_encode($result);
});


$app->get('/clients', function () use ($app,$settings) {
	$result['leads'] = array();
	$apiObj = new apiclass($settings);
	$docIds = array();
	$result['page_label'] = "Clients";
	/*
        $apiObj->mongoSetDB($settings['database']);
        $post['userGroups_0_createThing'] = "Y";
        $post['userGroups_0_id'] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
        $post['userGroups_0_label'] = "Sales";
        $post['userGroups_0_users_0_userId'] = "20151008135931-V2t9vDNy-JMLru8mr";
        $post['userGroups_0_users_0_level'] = "user";
        $apiObj->save_things($post);
    */
	if($apiObj->userLoggedIn()){
		$apiObj->mongoSetDB($settings['database']);
		$userIds = $apiObj->getUserIds();
		$apiObj->mongoSetCollection("person");
		$collectionQuery = false;

		// $collectionQuery['disposition']['$eq'] = "SOLD";
		if(trim($settings['clients']['search']) == ""){
			$collectionQuery['assignedTo']['$in'] = $userIds;
			$collectionQuery['$and'][0]['$or'][]['disposition']['$eq'] = "SOLD";
			$collectionQuery['$and'][0]['$or'][]['disposition']['$eq'] = "ONHOLD";
		}

		// $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
		// $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
		if(trim($settings['clients']['search']) <> ""){
			$settings['clients']['search'] = trim($settings['clients']['search']);
			$searchType = "NAME";
			$isPhone = trim(preg_replace("/[^0-9]/", "", $settings['clients']['search']));
			if( ( (strlen($isPhone) == "4") || (strlen($isPhone) == "7") || (strlen($isPhone) == "10") ) && (is_numeric($isPhone))){
				$searchType = "PHONE";
				$phoneParents = array();
				$apiObj->mongoSetCollection("phones");
				$phone = $apiObj->displayPhoneNumber($settings['leads']['search'], TRUE);
				//$collectionQueryPhone['phoneNumber']['$eq'] = $phone;
				$collectionQueryPhone['phoneNumber']['$regex'] = new MongoRegex("/".$isPhone.".*/i");
				$cursorPhone = $apiObj->mongoFind($collectionQueryPhone);
				if(!empty($cursorPhone)){
					foreach (iterator_to_array($cursorPhone) as $doc) {

						$phoneParents[] = $doc['_parentId'];
					}
				}
				$apiObj->mongoSetCollection("person");
				$collectionQuery['_id']['$in'] = $phoneParents;
			}

			if (!filter_var($settings['clients']['search'], FILTER_VALIDATE_EMAIL) === false) {
				$searchType = "EMAIL";
				$emailParents = array();
				$apiObj->mongoSetCollection("emails");
				$collectionQueryEmail['email']['$eq'] = trim($settings['clients']['search']);
				$cursorEmail = $apiObj->mongoFind($collectionQueryEmail);
				if(!empty($cursorEmail)){
					foreach (iterator_to_array($cursorEmail) as $doc) {
						$emailParents[] = $doc['_parentId'];
					}
				}
				$apiObj->mongoSetCollection("person");
				$collectionQuery['$or'][]['_id']['$in'] = $emailParents;
			}

			$nameparts = explode(" ", $settings['clients']['search']);
			if(count($nameparts) > 1){
				foreach($nameparts as $npKey=>$npVal){
					if((strlen($npVal) == "5") && (is_numeric($npVal))){
						$emailParents = array();
						$apiObj->mongoSetCollection("addresses");
						$collectionQueryState['zipCode']['$regex'] = new MongoRegex("/".$npVal.".*/i");
						$cursorState = $apiObj->mongoFind($collectionQueryState);
						if(!empty($cursorState)){
							unset($nameparts[$npKey]);
							foreach (iterator_to_array($cursorState) as $doc) {
								$stateParents[] = $doc['_parentId'];
							}
						}
						$apiObj->mongoSetCollection("person");
						$collectionQuery['_id']['$in'] = $stateParents;

					}

					if(strlen($npVal) == "2"){
						$emailParents = array();
						$apiObj->mongoSetCollection("addresses");
						$collectionQueryState['state']['$regex'] = new MongoRegex("/".$npVal.".*/i");
						$cursorState = $apiObj->mongoFind($collectionQueryState);
						if(!empty($cursorState)){
							unset($nameparts[$npKey]);
							foreach (iterator_to_array($cursorState) as $doc) {
								$stateParents[] = $doc['_parentId'];
							}
						}
						$apiObj->mongoSetCollection("person");
						$collectionQuery['_id']['$in'] = $stateParents;

					}
				}
			}

			if($searchType == "NAME"){

				$userparts = array();
				$userQuery = array();
				$apiObj->mongoSetCollection("user");
				if(count($nameparts) > 1){
					$userQuery['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					$userQuery['lastname']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
				} else {
					if(!empty($nameparts[0])){
						$userQuery['$or'][]['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
						$userQuery['$or'][]['lastname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");

					}
				}

				$assignedUsers = array();
				$cursorUser = $apiObj->mongoFind($userQuery);
				if(!empty($cursorUser)){
					foreach (iterator_to_array($cursorUser) as $doc) {
						$assignedUsers[] = $doc['_id'];

					}

				}

				$apiObj->mongoSetCollection("person");



				if(count($nameparts) > 1){
					$collectionQuery['$and'][0]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					$collectionQuery['$and'][0]['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
				} else {
					if(!empty($nameparts[0])){
						$collectionQuery['$and'][0]['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
						$collectionQuery['$and'][0]['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
						$collectionQuery['$and'][0]['$or'][]['assignedTo']['$in'] = $assignedUsers;
						if(strtoupper($nameparts[0]) == "UNKNOWN"){
							$collectionQuery['$and'][0]['$or'][]['leadSource']['$exists'] = FALSE;
						} else {
							$collectionQuery['$and'][0]['$or'][]['leadSource']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
						}

					}
				}
			}

		}

		// debug($collectionQuery);
		//debug($collectionQuery);
		//exit();
			$cursor = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor)){
			$cursor->sort(array('_timestampCreated' => -1));
			$x = 0;
			if($cursor->count() == 0){
				$result['total'] = 0;
			} else {
				$result['total'] = $cursor->count();
				if((!empty($settings['clients']['page'])) && ($settings['clients']['page'] > 1)){
            		$cursor->skip($settings['clients']['per_page'] * ($settings['clients']['page']-1));
				}
				//echo $cursor->count();
				$cursor->limit($settings['clients']['per_page']);
				foreach (iterator_to_array($cursor) as $doc) {
					$docIds[] = $doc['_id'];
					$result['leads'][] = $apiObj->get_thing_display($doc);
					//$result['leads'][] = $doc;
					$x++;
					if($x == $settings['clients']['per_page']){
						break;
					}
				}
			}
		} else {
			$result['leads'] = array();
		}
	} else {
		echo "User Not Logged In";
		exit();
	}
	//echo "<PRE>";
	//print_r(count($result['leads']));
	//echo "</PRE>";
	// Get Addresses
	$apiObj->mongoSetCollection("addresses");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['addresses'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['addresses'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Phones
	$apiObj->mongoSetCollection("phones");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['phones'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['phones'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Emails
	$apiObj->mongoSetCollection("emails");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['emails'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['emails'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// Get Policies
	$apiObj->mongoSetCollection("policy");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['policies'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['policies'][] = $apiObj->get_thing_display($doc2);
		}
	}
	$app->render('leadlist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
	peakmemory();
	// header("Content-Type: application/json");
	//echo json_encode($result);
});

$app->get('/groups/test/', function() use ($app) {
    $test = $app->request()->get('fields');
    echo "This is a GET route with $test";
});

$app->get('/policies', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
       if(!$apiObj->userLoggedIn()){
           echo "";
           exit();
       }
	$result['reportsStatus'] ="ANY";
    $result['reportsStartDate'] = date("Ymd000000");
    $result['reportsEndDate'] = date("Ymd235959");
    $result['reportsCarrier'] = "ANY";
    $result['reportsCarrierPlan'] = "ANY";
    $result['reportsLeadSource'] = "ANY";
    $result['reportsFronter'] = "ANY";
    $result['reportsCloser'] = "ANY";
    $result['reportsState'] = "ANY";
    $result['reportsLeadSource'] = "ANY";
    // debug($_GET);
    if(!empty($_GET["reportsStatus"])){ $result['reportsStatus'] = $_GET["reportsStatus"];}
    if(!empty($_GET["reportsStartDate"])){ $result['reportsStartDate'] = $apiObj->validateDate($_GET["reportsStartDate"], "m/d/Y", "Ymd000000"); }
    if(!empty($_GET["reportsEndDate"])){ $result['reportsEndDate'] = $apiObj->validateDate($_GET["reportsEndDate"], "m/d/Y", "Ymd235959"); }
    if(!empty($_GET['reportsCloser'])){ $result['reportsCloser'] =$_GET['reportsCloser']; }
    if(!empty($_GET['reportsFronter'])){ $result['reportsFronter'] = $_GET['reportsFronter']; }
    if(!empty($_GET['reportsCarrier'])){ $result['reportsCarrier'] = $_GET['reportsCarrier']; }
    if(!empty($_GET['reportsCarrierPlan'])){ $result['reportsCarrierPlan'] = $_GET['reportsCarrierPlan']; }
    if(!empty($_GET['reportsState'])){ $result['reportsState'] = $_GET['reportsState']; }
    if(!empty($_GET['reportsLeadSource'])){ $result['reportsLeadSource'] = $_GET['reportsLeadSource']; }
    

	$result['policies'] = array();
	$apiObj = new apiclass($settings);
	$docIds = array();
	if($apiObj->userLoggedIn()){
		$userIds = $apiObj->getUserIds();
		$apiObj->mongoSetDB($settings['database']);
		$collectionQuery = false;
		if((!empty($_SESSION['api']['user']['permissionLevel'])) && (strtoupper($_SESSION['api']['user']['permissionLevel']) <> "ADMINISTRATOR") && (strtoupper($_SESSION['api']['user']['permissionLevel']) <> "INSUREHC")){
			$collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
			$collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
		}




		if($settings['search_fronter'] = $apiObj->getValue($_REQUEST,"search_fronter")){

		}
		if(!empty($settings['search_fronter'])){
			if(trim($settings['search_fronter']) <> ""){
				$collectionQuery['$and'][]['soldBy']['$eq'] = $settings['search_fronter'];
			}
		}

		if($settings['search_closer'] = $apiObj->getValue($_REQUEST,"search_closer")){

		}
		if(!empty($settings['search_closer'])){
			if(trim($settings['search_closer']) <> ""){
				$collectionQuery['$and'][]['closedBy']['$eq'] = $settings['search_closer'];
			}
		}

		$collectionQuery['$and'][]['carrier']['$exists'] = TRUE;
		$collectionQuery['$and'][]['carrier']['$ne'] = "";

		if($settings['search_carrier'] = $apiObj->getValue($_REQUEST,"search_carrier")){

		}
		if(!empty($settings['search_carrier'])){
			if(trim($settings['search_carrier']) <> ""){
				$collectionQuery['$and'][]['carrier']['$eq'] = $settings['search_carrier'];
			}
		}


		if($settings['search_policy'] = $apiObj->getValue($_REQUEST,"search_policy")){

		}
		if(!empty($settings['search_policy'])){
			if(trim($settings['search_policy']) <> ""){
				$collectionQuery['$and'][]['coverageType']['$eq'] = $_REQUEST['search_policy'];
			}
		}

		if($settings['search_status'] = $apiObj->getValue($_REQUEST,"search_status")){

		}
		if(!empty($settings['search_status'])){
			if(trim($settings['search_status']) <> ""){
				$collectionQuery['$and'][]['status']['$eq'] = strtoupper($_REQUEST['search_status']);
			}
		}

		if(!empty($_REQUEST['carrier_search'])){
			if(trim($_REQUEST['carrier_search']) <> ""){
				$collectionQuery['$and'][]['carrier']['$eq'] = $_REQUEST['carrier_search'];
			}
		}

		if(!empty($_REQUEST['search_submitToday'])){
			if(trim($_REQUEST['search_submitToday']) == "Y"){
			    $submittedStatus = array("SUBMIT","SUBMITPAYMENT","ERRORS","CANCELLED","DECLINED","DUPLICATE");
                $collectionQuery['policySubmitted']['$nin'] = $submittedStatus;
				$collectionQuery['submissionDate']['$gte'] = date("Ymd000000");
				$collectionQuery['submissionDate']['$lte'] = date("Ymd235959");
			}
		} else {
			$_REQUEST['search_submitToday'] = "";
		}
		if(!empty($_REQUEST['search_pastDue'])){
			if(trim($_REQUEST['search_pastDue']) == "Y"){
                     if(trim($_REQUEST['search_majorMed']) != "Y"){
                         $pastDueStatus = array("SOLD","CANCELLED","PAYMENTISSUE");
                     } else {
                         $pastDueStatus = array("CANCELLED","PAYMENTISSUE");
                     }
                    if(trim($_REQUEST['search_followup']) != "Y"){
						// $pastDueStatus.push('FOLLOWUP');
						array_push($pastDueStatus,"FOLLOWUP");
					}
                    $submittedStatus = array("SUBMIT","SUBMITPAYMENT","ERRORS","CANCELLED","DECLINED","DUPLICATE");
					$collectionQuery['status']['$nin'] = $pastDueStatus;
                    $collectionQuery['policySubmitted']['$nin'] = $submittedStatus;
    	        	$collectionQuery['submissionDate']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
                    if(trim($_REQUEST['search_majorMed']) != "Y"){
	    			    $collectionQuery['dateToPay']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
                    }
			}
		} else {
			$_REQUEST['search_pastDue'] = "";
		}

		if(!empty($_REQUEST['search_followup'])){
			if(trim($_REQUEST['search_followup']) == "Y"){
				$collectionQuery['$and'][]['status']['$eq'] = 'FOLLOWUP';
			}
     //                 if(trim($_REQUEST['search_majorMed']) != "Y"){
     //                     $pastDueStatus = array("SOLD","CANCELLED","PAYMENTISSUE");
     //                 } else {
     //                     $pastDueStatus = array("CANCELLED","PAYMENTISSUE");
     //                 }
     //                $submittedStatus = array("SUBMIT","SUBMITPAYMENT","ERRORS","CANCELLED","DECLINED","DUPLICATE");
					// $collectionQuery['status']['$nin'] = $pastDueStatus;
     //                $collectionQuery['policySubmitted']['$nin'] = $submittedStatus;
    	//         	$collectionQuery['submissionDate']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
     //                if(trim($_REQUEST['search_majorMed']) != "Y"){
	    // 			    $collectionQuery['dateToPay']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
     //                }
			// }
		} else {
			$_REQUEST['search_followup'] = "";
		}



		if(!empty($_REQUEST['search_noPolicyNumber'])){
			if(trim($_REQUEST['search_noPolicyNumber']) == "Y"){
				$collectionQuery['$and'][0]['$or'][]['policyNumber']['$exists'] = FALSE;
				$collectionQuery['$and'][0]['$or'][]['policyNumber']['$eq'] = "";
				$collectionQuery['$and'][0]['$or'][]['policyNumber']['$regex'] = new MongoRegex("/TBD.*/i");

			}
		} else {
			$_REQUEST['search_noPolicyNumber'] = "";
		}

		if(!empty($_REQUEST['search_submissionErrors'])){
			if(trim($_REQUEST['search_submissionErrors']) == "Y"){
				$collectionQuery['$and'][0]['$or'][]['policySubmitted']['$eq'] = "ERRORS";
				$collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "PAYMENTISSUE";
			}
		} else {
			$_REQUEST['search_submissionErrors'] = "";
		}

		if(!empty($_REQUEST['search_notPaid'])){
			if(trim($_REQUEST['search_notPaid']) == "Y"){
				$collectionQuery['$and'][]['policySubmitted']['$ne'] = "SUBMITPAYMENT";
				if(!empty($_REQUEST['search_majorMed'])){
					if(trim($_REQUEST['search_majorMed']) == "Y"){
						$collectionQuery['$and'][]['policySubmitted']['$eq'] = "SUBMIT";
					}
				}
				$collectionQuery['dateToPay']['$lte'] = date("Ymd999999", time());
			}
		} else {
			$_REQUEST['search_notPaid'] = "";
		}
		if(!empty($_REQUEST['search_majorMed'])){
			if(trim($_REQUEST['search_majorMed']) == "Y"){
				$majorMeds = array();
				$majorMeds[] = "NNFLei-Mkjie83-Opejr93f";
				$majorMeds[] = "On97lakN-V0gVHNyP-LrpUEAOZ";
				$majorMeds[] = "f9tc2bTZ-H0P7mYrI-pMP0fMNW";
				$majorMeds[] = "YxNyBSDf-J8gM4Dou-Gf4vmJta";
				$majorMeds[] = "PrLtFKmF-872b5Q0c-tMBQunll";
				$collectionQuery['coverageType']['$in'] = $majorMeds;
				//$collectionQuery['submissionDate']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
			}
		} else {
			$_REQUEST['search_majorMed'] = "";
		}

		if(trim($settings['policies']['search']) <> ""){
			// Get Person
			$personIds = array();
			$result['persons'] = array();

			$nameparts = explode(" ", $settings['policies']['search']);

			$userparts = array();
			$userQuery = array();
			$apiObj->mongoSetCollection("user");
			if(count($nameparts) > 1){
				$userQuery['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
				$userQuery['lastname']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
			} else {
				if(!empty($nameparts[0])){
					$userQuery['$or'][]['firstname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					$userQuery['$or'][]['lastname']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
				}
			}

			$assignedUsers = array();
			$cursorUser = $apiObj->mongoFind($userQuery);
			if(!empty($cursorUser)){
				foreach (iterator_to_array($cursorUser) as $doc) {
					$assignedUsers[] = $doc['_id'];

				}

			}


			$apiObj->mongoSetCollection("person");

			if(count($nameparts) > 1){
				$collectionQuery2['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
				$collectionQuery2['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
			} else {
				if(!empty($nameparts[0])){
					$collectionQuery2['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					$collectionQuery2['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
					if(!empty(assignedUsers)){
						$collectionQuery2['$or'][]['assignedTo']['$in'] = $assignedUsers;
					}

				}
			}

			$personIds = array();
			$cursor2 = $apiObj->mongoFind($collectionQuery2);
			if(!empty($cursor2)){
				if($cursor2->count() == 0){
				} else {
					foreach (iterator_to_array($cursor2) as $doc2) {
						$personIds[] = $doc2['_id'];
					}
					$collectionQuery['$and'][]['_parentId']['$in'] = $personIds;
				}
			}
         
			$collectionQuery2 = array();
            $apiObj->mongoSetCollection("policy");

			if(count($nameparts) > 1){
				$collectionQuery2['policyNumber']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
				
			} else {
				if(!empty($nameparts[0])){
					$collectionQuery2['$or'][]['policyNumber']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
				}
			}
   
			$cursor2 = $apiObj->mongoFind($collectionQuery2);
			if(!empty($cursor2)){
				if($cursor2->count() == 0){
				} else {
					foreach (iterator_to_array($cursor2) as $doc2) {
						$policyIds[] = $doc2['_id'];
					}
					$collectionQuery['$and'][]['_id']['$in'] = $policyIds;
				}
			}
		}

        // $collectionQuery['_timestampCreated']['$gte'] = date("201512010000000");

		if(!empty($_GET["reportsStatus"])){
			// $collectionQuery = array();
			if($result['reportsStatus'] == "ANY"){
		        $collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		        $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		    } else {
		        $collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		        $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		        $collectionQuery['status']['$eq'] = $result['reportsStatus'];
		    }
		    if($result['reportsCloser'] != "ANY"){
		        $collectionQuery['closedBy']['$eq'] = $result['reportsCloser'];
		    }
		    if($result['reportsFronter'] != "ANY"){
		        $collectionQuery['soldBy']['$eq'] = $result['reportsFronter'];
		    }
		    if($result['reportsCarrier'] != "ANY"){
		        $collectionQuery['carrier']['$eq'] = $result['reportsCarrier'];
		    }
		    if($result['reportsCarrierPlan'] != "ANY"){
		        $collectionQuery['coverageType']['$eq'] = $result['reportsCarrierPlan'];
		    }
		    // if($result['reportsLeadSource'] != "ANY"){
		    //     $collectionQuery['leadSource']['$eq'] = $result['reportsLeadSource'];
		    // }
		    $policCollectionQuery = $collectionQuery;


		    $policCollectionQuery['$and'][]['carrier']['$exists'] = TRUE;
		    $policCollectionQuery['$and'][]['carrier']['$ne'] = "";

		    if((!empty($_SESSION['api']['user']['permissionLevel'])) && (strtoupper($_SESSION['api']['user']['permissionLevel']) <> "ADMINISTRATOR") && (strtoupper($_SESSION['api']['user']['permissionLevel']) <> "INSUREHC")){
		        $policCollectionQuery['$or'][]['soldBy']['$in'] = $userIds;
		        $policCollectionQuery['$or'][]['closedBy']['$eq'] = $_SESSION['api']['user']['_id'];
		    }
		    // var_dump($policCollectionQuery);
		    $apiObj->mongoSetCollection("policy");
		    $take = 500;
		    $personIds = array();
		    $countPolices = $apiObj->mongoCount($policCollectionQuery);
		    
		    $skip = intval($countPolices/$take);
		    if(is_float($countPolices/$take)){
		        $skip = intval($skip) + 1;
		    }
		    if($skip == 0)
		        $result['policies'][] = array();
		    for ($i=0; $i < $skip; $i++) { 
		        $cursor2 = $apiObj->mongoFind2($policCollectionQuery, $i, $take);
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            $personIds[] = $doc2['_parentId'];
		            $result['policies'][] = $doc2;

		        }
		    }

		    // echo count($result['policies']);
		    
		    $collectionQuery3 = false;
		    $apiObj->mongoSetCollection("person");
		    $collectionQuery3['_id']['$in'] = $personIds;

		    $countLeadResouces = $apiObj->mongoCount($collectionQuery3);
		    
		    $skip = intval($countLeadResouces/$take);
		    if(is_float($countLeadResouces/$take)){
		        $skip = intval($skip) + 1;
		    }
		    if($skip == 0)
		        $result['person'][] = array();

		    for ($i=0; $i < $skip; $i++) { 
		        $cursor2 = $apiObj->mongoFind2($collectionQuery3, $i, $take);
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            if(!empty($doc2['leadSource'])){
		                $result['leadSources'][$doc2['_id']] = $doc2['leadSource'];
		            }
		        }
		    }

		    $collectionQuery3 = false;
		    $apiObj->mongoSetCollection("addresses");
		    $collectionQuery3['_parentId']['$in'] = $personIds;
		    $cursor2 = $apiObj->mongoFind($collectionQuery3);
		    $result['addressesState'] = array();
		    if($cursor2->count() == 0){
		        $result['addresses'][] = array();
		    } else {
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            if(!empty($doc2['state'])){
		                $result['addressesState'][$doc2['_parentId']] = $doc2['state'];
		            }
		        }
		    }
		    // var_dump($result['addressesState']);
		    
		    
		    $apiObj->mongoSetCollection("carrier");
		    $cursor2 = $apiObj->mongoFind(false);
		    if($cursor2->count() == 0){
		        $result['carrier'][] = array();
		    } else {
		        $cursor2->sort(array('sort' => 1));
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            $result['carrier'][$doc2['_id']] = $doc2;
		        }
		    }
		    
		    $collectionQuery3 = false;
		    $apiObj->mongoSetCollection("carrierPlan");
		    $collectionQuery3['status']['$eq'] = "ACTIVE";
		    $cursor2 = $apiObj->mongoFind($collectionQuery3);
		    if($cursor2->count() == 0){
		        $result['carrierPlan'][] = array();
		    } else {
		        $cursor2->sort(array('sort' => 1));
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            $result['carrierPlan'][$doc2['_id']] = $doc2;
		        }
		    }	
		    // var_dump($result['carrierPlan']);
		    
		    $apiObj->mongoSetCollection("user");
		    $cursor2 = $apiObj->mongoFind(false);

		    if($cursor2->count() == 0){
		        $result['user'][] = array();
		    } else {
		        $cursor2->sort(array('status' => 1, 'firstname' => 1));

		        foreach (iterator_to_array($cursor2) as $doc2) {
		        	$result['user'][$doc2['_id']] = $doc2;
		        }
		    }
		    
		    $apiObj->mongoSetCollection("systemForm");
		    $cursor2 = $apiObj->mongoFind(false);
		    if($cursor2->count() == 0){
		        $result['systemForm'][] = array();
		    } else {
		        foreach (iterator_to_array($cursor2) as $doc2) {
		            $result['systemForm'][$doc2['name']] = $doc2;
		        }
		    }
		    unset($result['policies']);
		}

		$apiObj->mongoSetCollection("policy");
	    // echo var_dump($collectionQuery);
	    $cursor = $apiObj->mongoFind($collectionQuery);
	    // echo $cursor->count();

        $sort_by = "_timestampCreated";
        if(!empty($_REQUEST['search_sortFilter'])){
		    if(trim($_REQUEST['search_sortFilter']) == "dateToPay"){
             $sort_by = "dateToPay";

             }
             if(trim($_REQUEST['search_sortFilter']) == "submissionDate"){
             $sort_by = "submissionDate";

             }
        }
        if(($result['reportsState'] == "ANY" || empty($result['reportsState'])) && ($result['reportsLeadSource'] == "ANY" || empty($result['reportsLeadSource']))){
        	$cursor->sort(array($sort_by => -1));
			$x = 0;
			if($cursor->count() == 0){
				$result['total'] = 0;
			} else {
				$result['total'] = $cursor->count();
				if((!empty($settings['policies']['page'])) && ($settings['policies']['page'] > 1)){
					$cursor->skip($settings['policies']['per_page'] * ($settings['policies']['page']-1));
				}
				//echo $cursor->count();
				$cursor->limit($settings['policies']['per_page']);
				foreach (iterator_to_array($cursor) as $doc) {
					$docIds[] = $doc['_parentId'];
					$result['policies'][] = $apiObj->get_thing_display($doc);
					//$result['leads'][] = $doc;
					$x++;
					if($x == $settings['policies']['per_page']){
						break;
					}
				}
			}
        }else{
        	$cursor->sort(array($sort_by => -1));
			$x = 0;
			if($cursor->count() == 0){
				$result['total'] = 0;
			} else {
				
				if($result['reportsState'] == "ANY" && $result['reportsLeadSource'] == 'ANY'){
					$cursor->limit($settings['policies']['per_page']);
				}
				foreach (iterator_to_array($cursor) as $doc) {
					$leadsourceId = 'UNKNOWN';
					$leadsource = "UNKNOWN";
					$isItem = false;
					$docIds[] = $doc['_parentId'];
					
					// STATE SET
			        if(!empty($result['addressesState'][$doc['_parentId']])){
			            $state =  $result['addressesState'][$doc['_parentId']];
			        }
			          // LEAD SOURCE SET
			        if(!empty($result['leadSources'][$doc['_parentId']])){
			            $leadsource =  $result['leadSources'][$doc['_parentId']];
			        }

			        // LEAD SOURCE
			        if(!empty($result['systemForm']['leadSource']['options'])){
			            foreach($result['systemForm']['leadSource']['options'] as $sKey=>$sVal){
			                if($sVal['value'] == strtoupper($leadsource)){
			                    $leadsource =   strtoupper($sVal['label']);
			                    $leadsourceId = $sVal['_id'];
			                }
			            }
			        }
			        // echo $state .'</br>';
			        if($result['reportsLeadSource'] != 'ANY' && $result['reportsState'] != 'ANY'){
			        	if(($result['reportsState'] == strtoupper($state)) ){ 
				        	// var_dump($state);
				        	// $isItem = true;
				        	if(!empty($result['systemForm']['state']['options'])){
					            foreach($result['systemForm']['state']['options'] as $sKey=>$sVal){
					                if($sVal['value'] == strtoupper($state)){
					                    $state =   strtoupper($sVal['label']);
					                    $stateValue = $sVal['value'];
					                    // var_dump($sVal);die();
					                }
					            }
					        }
					        if ($result['reportsLeadSource'] == $leadsourceId) 
			        			$isItem = true;
				        }
			        }
			        else if($result['reportsState'] != 'ANY'){
			        	if(($result['reportsState'] == strtoupper($state)) ){ 
				        	// var_dump($state);
				        	// $isItem = true;
				        	if(!empty($result['systemForm']['state']['options'])){
					            foreach($result['systemForm']['state']['options'] as $sKey=>$sVal){
					                if($sVal['value'] == strtoupper($state)){
					                    $state =   strtoupper($sVal['label']);
					                    $stateValue = $sVal['value'];
					                    // var_dump($sVal);die();
					                }
					            }
					        }
					        $isItem = true;
				        }
				    }else if(($result['reportsLeadSource'] != "ANY" ) ){
				    	if ($result['reportsLeadSource'] == $leadsourceId) 
			        		$isItem = true;
			        }
				    
					if($isItem){
						$result['policies'][] = $apiObj->get_thing_display($doc);
						$x++;
						// if($x == $settings['policies']['per_page']){
						// 	break;
						// }
					}
				}
				$result['total'] = $x;
				
			}
        }
		
	} else {
		echo "User Not Logged In";
		exit();
	}

	// Get Person
	$result['persons'] = array();
	$apiObj->mongoSetCollection("person");
	$collectionQuery = array('_id' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['persons'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['persons'][] = $apiObj->get_thing_display($doc2);
			}
		}
	}
	// Get Carriers
	$result['carriers'] = array();
	$apiObj->mongoSetCollection("carrier");
	$collectionQuery = array();
	$collectionQuery['status']['$eq'] = "ACTIVE";
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['carriers'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['carriers'][] = $apiObj->get_thing_display($doc2);
			}
		}
	}
	// Get Carrier Plans
	$result['carrierPlans'] = array();
	$apiObj->mongoSetCollection("carrierPlan");
	$collectionQuery = array();
	$collectionQuery['status']['$eq'] = "ACTIVE";
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['carrierPlans'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['carrierPlans'][] = $apiObj->get_thing_display($doc2);
			}
		}
	}
	// Get Users
	$result['users'] = array();
	$apiObj->mongoSetCollection("user");
	$collectionQuery = array();
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['users'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['users'][] = $apiObj->get_thing_display($doc2);
			}
		}
	}


	$app->render('policylist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
	peakmemory();

});





$app->get('/edit/:personId', function ($personId) use ($app,$settings) {
	header('X-Frame-Options: SAMEORIGIN');	
	$result['leads'] = array();
	$apiObj = new apiclass($settings);
	if($apiObj->userLoggedIn()){
		$apiObj->mongoSetDB($settings['database']);

		$apiObj->mongoSetCollection("person");
		$collectionQuery = array('_id' => $personId );
		$cursor = $apiObj->mongoFind($collectionQuery);
		$x = 0;
		if($cursor->count() == 0){
		} else {

			$cursor->limit(100);
			foreach (iterator_to_array($cursor) as $doc) {
				if(!empty($doc['_timestampCreated'])){
					$doc['date_created'] = date("m/d/Y",$doc['_timestampCreated']);
				} else {
					$doc['date_created'] = "New";
				}
				if(!empty($doc['_timestampModified'])){
					$current_lock_time = $doc['_timestampModified'];
				} else {
					$current_lock_time = $date("YmdHis");
				}

				$result['leads'][] = $apiObj->get_thing_display($doc);
				//$result['leads'][] = $doc;
				$x++;
				if($x == 100){
					break;
				}
			}
		}
		// Get Addresses
		$apiObj->mongoSetCollection("addresses");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['addresses'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['addresses'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Phones
		$apiObj->mongoSetCollection("phones");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['phones'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['phones'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Emails
		$apiObj->mongoSetCollection("emails");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['emails'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['emails'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Notes
		$apiObj->mongoSetCollection("notes");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['notes'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['notes'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Policies
		$apiObj->mongoSetCollection("policy");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['policy'][] = array();
		} else {
			$i = 0;
			foreach (iterator_to_array($cursor2) as $doc2) {

				// Get AdminTab
				$apiObj->mongoSetCollection("adminTab");
				$collectionQuery = array('_parentId' => $doc2['_id']);
				$cursor3 = $apiObj->mongoFind($collectionQuery);
				$adminTab = array();
				if($cursor3->count() == 0){
					$adminTab[] = array();
				} else {
					foreach (iterator_to_array($cursor3) as $doc3) {
						$adminTab[] = $apiObj->get_thing_display($doc3);
					}
				}
				$result['policy'][$i] = $apiObj->get_thing_display($doc2);
				$result['policy'][$i]['adminTab'] = $adminTab;
				$i++;


			}
		}
		// Get Appointments
		$apiObj->mongoSetCollection("appointment");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['appointment'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['appointment'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Employer
		$apiObj->mongoSetCollection("employer");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['employer'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['employer'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get History
		$apiObj->mongoSetCollection("history");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['history'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['history'][] = $apiObj->get_thing_display($doc2);
			}
		}


		$result['carriers'] = array();
		$apiObj->mongoSetCollection("carrier");
		$collectionQuery = array();
		$collectionQuery['status']['$eq'] = "ACTIVE";
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				$result['carriers'][] = array();
			} else {
				foreach (iterator_to_array($cursor2) as $doc2) {
					$result['carriers'][] = $apiObj->get_thing_display($doc2);
				}
			}
		}
		// Get Carrier Plans
		$result['carrierPlans'] = array();
		$apiObj->mongoSetCollection("carrierPlan");
		$collectionQuery = array();
		$collectionQuery['status']['$eq'] = "ACTIVE";
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				$result['carrierPlans'][] = array();
			} else {
				foreach (iterator_to_array($cursor2) as $doc2) {
					$result['carrierPlans'][] = $apiObj->get_thing_display($doc2);
				}
			}
		}

		$result['users'] = array();
		$apiObj->mongoSetCollection("user");
		$collectionQuery = array();
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				$result['users'][] = array();
			} else {
				foreach (iterator_to_array($cursor2) as $doc2) {
					$result['users'][] = $apiObj->get_thing_display($doc2);
				}
			}
		}


	} else {
		echo "User Not Logged In";
		exit();
	}

	/*
    try {


        if((!empty($result['leads'][0]['_timestampModified'])) && (!empty($result['leads'][0]['_modifiedBy']))){
            $current_time =  date("YmdHis");
            $lock_time_passed = $current_time - $current_lock_time ;
            if($result['leads'][0]['_modifiedBy'] <> $_SESSION['api']['user']['_id']){
                if($lock_time_passed < 1800){
                    $time_left = (1800 - $lock_time_passed) / 60;
                    echo "This Lead/Client is currently locked and being modified by another user. Current Lock time is:  ". round($time_left). " Minutes";
                    exit();
                }
            }

        }

    } catch (Exception $e) {
    }
*/

	if(!empty($_SESSION['api']['user']['_id'])){
		$post['person_0_createThing'] = "Y";
		$post['person_0_id'] = $personId;
		$post['person_0_history_0_createThing'] = "Y";
		$post['person_0_history_0_note'] = "User Viewed this lead";
		$post['person_0_history_0_userId'] = $_SESSION['api']['user']['_id'];
		$post['person_0_history_0_userName'] = $_SESSION['api']['user']['firstname'] . " " . $_SESSION['api']['user']['lastname'];
		$apiObj->save_things($post);
	}





	$seller = FALSE;
	if(!empty($result['policy'])){
		foreach($result['policy'] as $pkey=>$pval){
			if($pval['closedBy'] == $_SESSION['api']['user']['_id']){
				$seller = true;
			}
			if($pval['soldBy'] == $_SESSION['api']['user']['_id']){
				$seller = true;
			}
		}
	}

	$podmanager = false;
	if($seller === false){
		$userIds = $apiObj->getUserIds();
		if (in_array($result['leads'][0]['assignedTo'], $userIds)) {
			$podmanager = TRUE;
		}
	}


	if (
		((!empty($result['leads'][0]['assignedTo'])) && ($result['leads'][0]['assignedTo'] == $_SESSION['api']['user']['_id']))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "ADMINISTRATOR"))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "MANAGER"))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "INSUREHC"))
		|| ($seller === TRUE)
		|| ($podmanager === TRUE)
		|| (($result['leads'][0]['leadSource'] == "PRECISELEADS") &&  ($result['leads'][0]['assignedTo'] == "20151005154138-k7N1dHZi-4I7ZoB2J") )
	) {
		
		$size_people = 1;
		$item_people = createItemPeople($apiObj->getValue( $result['leads'][0], "dateOfBirth"), $apiObj->getValue( $result['leads'][0], "smokerTabacco"), 'a');
		
		// debug($result['leads'][0]);
		// Spouses
		if (isset($result['leads'][0]['spouse']) && is_array($result['leads'][0]['spouse'])) {
			$size_people += count($result['leads'][0]['spouse']);
			foreach ($result['leads'][0]['spouse'] as $spouse) {
				$item_people .= ',' . createItemPeople($spouse['spouseDateOfBirth'], $spouse['spouseSmoker'], 'b');
			}
		}

		// Dependents
		if (isset($result['leads'][0]['dependents']) && is_array($result['leads'][0]['dependents'])) {
			$size_people += count($result['leads'][0]['dependents']);
			foreach ($result['leads'][0]['dependents'] as $spouse) {
				$item_people .= ',' . createItemPeople($spouse['dependentsDateOfBirth'], '', 'd');
			}
		}
		$zip_code = '';
		// debug($result);
		if (isset($result['addresses']) && is_array($result['addresses'])) {
			if(count($result['addresses']) > 0)
				$zip_code = $result['addresses'][0]['zipCode'];
		}

		$income = '';
		// debug($result['leads'][0]['taxes']);
		if (isset($result['leads'][0]['taxes']) && is_array($result['leads'][0]['taxes'])) {
			if(count($result['leads'][0]['taxes']) > 0)
				$income = $result['leads'][0]['taxes'][0]['estimatedYearlyIncome'];
		}

		$iframe_url = "https://www.healthsherpa.com/find-plans/plans?zip_code=$zip_code&people=[$item_people]
&income=$income&size=$size&cs=premium&year=2017&page=1&_agent_id=lisa-jackson-lerW0g";
		// echo $iframe_url;
		$app->render('leadform.php', array('iframe_url' => $iframe_url, 'result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	} else {
       //	$app->render('leadform.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	   	$app->render('leadview.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	}
	peakmemory();
});

$app->post('/updateLead', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	if(!empty($_SESSION['api']['user']['_id'])){
		if(!empty($_POST['person_0_id'])){
			$post['person_0_createThing'] = "Y";
			$post['person_0_id'] = $_POST['person_0_id'];
			$post['person_0_history_0_createThing'] = "Y";
			$post['person_0_history_0_note'] = "User Saved/Edited this lead";
			$post['person_0_history_0_userId'] = $_SESSION['api']['user']['_id'];
			$post['person_0_history_0_userName'] = $_SESSION['api']['user']['firstname'] . " " . $_SESSION['api']['user']['lastname'];
			//$apiObj->save_things($post);
		}
	}
	if(!empty($_POST)){
		$apiObj->saveAll($_POST,"lead");
		if($apiObj->save_things($_POST)){
			$result['message'] = "Things Saved";    
		} else {
			$result['message'] = "There was an error saving your Things.";   
		}
	}
});
$app->get('/template/:type/:index/:crtThng', function ($type, $index, $crtThng) use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$app->render('form_partials.php', array('index'=>$index, 'type'=>$type, 'crtThng'=>$crtThng,  'settings'=>$settings, "apiObj"=>$apiObj));
});
$app->get('/create', function () use ($app,$settings) {
	$result = array();
	$apiObj = new apiclass($settings);
	$app->render('leadform.php', array('result' => $result,  'settings'=>$settings, "apiObj"=>$apiObj));
});
$app->get('/delete/:personId', function ($personId) use ($app,$settings) {
	$result['person'] = array();
	$result['personId'] = $personId;
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);

	if(!empty($_SESSION['api']['user']['_id'])){
		if(!empty($personId)){
			$post['person_0_createThing'] = "Y";
			$post['person_0_id'] = $personId;
			$post['person_0_history_0_createThing'] = "Y";
			$post['person_0_history_0_note'] = "User DELETED this lead";
			$post['person_0_history_0_userId'] = $_SESSION['api']['user']['_id'];
			$post['person_0_history_0_userName'] = $_SESSION['api']['user']['firstname'] . " " . $_SESSION['api']['user']['lastname'];
			$apiObj->save_things($post);
			$post=array();
		}
	}

	if($apiObj->userLoggedIn()){
		$apiObj->mongoSetCollection("person");
		$collectionQuery = array('_id' => $personId );
		$cursor = $apiObj->mongoFind($collectionQuery);
		$x = 0;
		if(empty($cursor)){
			echo "Can Not Find";
			exit();
		}
		if($cursor->count() == 0){
			echo "Can Not Find";
			exit();
		} else {
			$cursor->limit(100);
			foreach (iterator_to_array($cursor) as $doc) {
				if(!empty($doc['_timestampCreated'])){
					$doc['date_created'] = date("m/d/Y",$doc['_timestampCreated']);
				} else {
					$doc['date_created'] = "New";   
				}
				$result['person'][] = $apiObj->get_thing_display($doc);
				//$result['leads'][] = $doc;
				$x++;
				if($x == 2){
					break;   
				}
				$collectionQuery = array("_id"=>$doc["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Addresses
		$apiObj->mongoSetCollection("addresses");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['addresses'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['addresses'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Phones
		$apiObj->mongoSetCollection("phones");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['phones'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['phones'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Emails
		$apiObj->mongoSetCollection("emails");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['emails'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['emails'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Notes
		$apiObj->mongoSetCollection("notes");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['notes'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['notes'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Policies
		$apiObj->mongoSetCollection("policy");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['policy'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['policy'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Appointments
		$apiObj->mongoSetCollection("appointment");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['appointment'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['appointment'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get Employer
		$apiObj->mongoSetCollection("employer");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['employer'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['employer'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
		// Get History
		$apiObj->mongoSetCollection("history");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['history'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['history'][] = $apiObj->get_thing_display($doc2);
				$collectionQuery = array("_id"=>$doc2["_id"]);
				$apiObj->mongoRemove($collectionQuery);
			}
		}
	} else {
		echo "User Not Logged In";
		exit();
	}
	$idcounter = array();
	foreach($result['person'] as $counter=>$person){
		foreach($person as $key=>$value){
			$post['person_'.$counter.'_createThing'] = "Y";
			$idcounter[$person['_id']] = $counter;
			if(!is_array($value)){
				$pos = strpos($key, "_");
				if ($pos === false) {
					$post["person_".$counter."_".$key] = $value;
				} else {
					$post["person_".$counter."".$key] = $value;
				}
			} else {
				foreach($value as $key2=>$value2){
					$post["person_0_".$key."_".$key2."_createThing"] = "N";
					$post["person_0_".$key."_".$key2."_id"] = $value2["_id"];
					foreach($value2 as $key3=>$value3){
						$pos = strpos($key3, "_");
						if ($pos === false) {
							$post["person_".$counter."_".$key."_".$key2."_".$key3] = $value3;
						} else {
							$post["person_".$counter."_".$key."_".$key2."".$key3] = $value3;
						}
					}
				}
			}
			$moreitems = array("addresses","phones", "emails", "notes", "policy", "history");
			foreach($moreitems as $mik=>$miv){
				if(!empty($result[$miv] )){
					foreach($result[$miv] as $key1=>$value1){
						if($value1['_parentId'] == $person['_id']){
							foreach($value1 as $key3=>$value3){
								$post["person_".$counter."_".$miv."_".$key1."_createThing"] ="Y";
								$post["person_".$counter."_".$miv."_".$key1."_id"] = $value1["_id"];
								$pos = strpos($key3, "_");
								if ($pos === false) {
									$post["person_".$counter."_".$miv."_".$key1."_".$key3] = $value3;
								} else {
									$post["person_".$counter."_".$miv."_".$key1."".$key3] = $value3;
								}
							}
						}
					}
				}
			}
		}
	}

	$m = new MongoClient();
	$db = $m->selectDB($settings['database']);
	$collection = 'deletedItems';
	$post['_id'] = $apiObj->getRandomId();
	$post['_timestampCreated'] = date("YmdHis");
	$post['_createdBy'] = $_SESSION['api']['user']['id'];
	$db->$collection->insert($post);

	$app->render('deleted.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj));

});
$app->get('/addrecording', function () use ($app,$settings) {
	$result = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB("trentTest");
	$post["recordings_0_createThing"] =  "Y";
	$post["recordings_0_temp"] =  "Y";
	$allGetVars = $app->request->get();
	$allPostVars = $app->request->post();
	$allPutVars = $app->request->put();
	if(!empty($allPostVars)){
		foreach($allPostVars as $key=>$var){
			$post["recordings_0_post".preg_replace('/[^a-zA-Z0-9]/', '', $key)] =$var;
		}
	}
	if(!empty($allGetVars)){
		foreach($allGetVars as $key=>$var){
			$post["recordings_0_get".preg_replace('/[^a-zA-Z0-9]/', '', $key)] =$var;
		}
	}
	$apiObj->save_things($post);
	echo "Thank You";
	exit();
});

$app->map('/recordingsview/:personId', function ($personId) use ($app, $settings)  {
	$result=array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("phones");
	$phones= $apiObj->mongoFind(array('_parentId'=>$personId));
	foreach($phones as $phone){
		array_push($result,$phone);
	}
	$app->render('recordings.php', array('result' => $result,'settings'=>$settings));
})->via('GET','POST');
$app->map('/recordingsnumber/:number', function ($number) use ($app, $settings)  {
	$result=array();
	$a['phoneNumber']=$number;
	array_push($result,$a);
	$app->render('recordings.php', array('result' => $result,'settings'=>$settings));
})->via('GET','POST');

$app->get('/export', function () use ($app,$settings) {
	//exit();
	//ini_set('max_execution_time', 300);
	//$settings['database'] = "ehealthbrokers";
	$result['policies'] = array();
	$apiObj = new apiclass($settings);
	$docIds = array();
	if($apiObj->userLoggedIn()){
		$userIds = $apiObj->getUserIds();
		$apiObj->mongoSetDB($settings['database']);
		$collectionQuery = false;
		$collectionQuery['_timestampCreated']['$gte'] = "20160830144430";
		$collectionQuery['_timestampCreated']['$lt'] = "20160906125803";
		$apiObj->mongoSetCollection("policy");
		$cursor = $apiObj->mongoFind($collectionQuery);
		$cursor->sort(array('_timestampCreated' => 1));
		$x = 0;
		if($cursor->count() == 0){
			$result['total'] = 0;
		} else {
			$result['total'] = $cursor->count();
			$cursor->limit(10000);
			foreach (iterator_to_array($cursor) as $doc) {
				$docIds[] = $doc['_parentId'];
				//$result['policies'][] = $apiObj->get_thing_display($doc);
				$result['policies'][] = $doc;
				//$result['leads'][] = $doc;

			}
		}
	} else {
		exit();
	}


	// Get Person
	$result['persons'] = array();
	$apiObj->mongoSetCollection("person");
	$collectionQuery = array('_id' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['persons'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				//$result['persons'][] = $apiObj->get_thing_display($doc2);
				$result['persons'][] = $doc2;
			}
		}
	}
	//  debug($result);
	//exit();
	// Get Carriers
	$result['carriers'] = array();
	$apiObj->mongoSetCollection("carrier");
	$collectionQuery = array();
	$collectionQuery['status']['$eq'] = "ACTIVE";
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['carriers'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				//$result['carriers'][] = $apiObj->get_thing_display($doc2);
				$result['carriers'][] = $doc2;
			}
		}
	}
	// Get Carrier Plans
	$result['carrierPlans'] = array();
	$apiObj->mongoSetCollection("carrierPlan");
	$collectionQuery = array();
	$collectionQuery['status']['$eq'] = "ACTIVE";
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['carrierPlans'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				//$result['carrierPlans'][] = $apiObj->get_thing_display($doc2);
				$result['carrierPlans'][] =$doc2;
			}
		}
	}
	// Get Users
	$result['users'] = array();
	$apiObj->mongoSetCollection("user");
	$collectionQuery = array();
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor2)){
		if($cursor2->count() == 0){
			$result['users'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				//$result['users'][] = $apiObj->get_thing_display($doc2);
				$result['users'][] = $doc2;
			}
		}
	}




	// Get Addresses
	$apiObj->mongoSetCollection("addresses");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['addresses'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			//$result['addresses'][] = $apiObj->get_thing_display($doc2);
			$result['addresses'][] = $doc2;
		}
	}
	// Get Phones
	$apiObj->mongoSetCollection("phones");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['phones'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			//$result['phones'][] = $apiObj->get_thing_display($doc2);
			$result['phones'][] = $doc2;
		}
	}
	// Get Emails
	$apiObj->mongoSetCollection("emails");
	$collectionQuery = array('_parentId' => array('$in' => $docIds));
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['emails'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			//$result['emails'][] = $apiObj->get_thing_display($doc2);
			$result['emails'][] = $doc2;
		}
	}
	

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$settings['database'].'_export_'.date("YmdHis").'.csv');
	$out = fopen('php://output', 'w');
	// output the column headings
	fputcsv($out, array('ID', 'First Name', 'Middle Name', 'Last Name', 'Gender','SSN','Date Created', 'Lead Status', 'Email', 'Phone 1', 'Phone 2', 'Address', 'AptNo', 'City', 'State', ' Zip', 'DOB', 'Policy Entered Date', 'Policy Number', 'Carrier', 'Coverage Type', 'Status', 'Premium', 'Setup Fee', 'Subsidy', 'Pay Schedule', 'Effecitve Date', 'Submission Date', 'Policy Submitted', 'Renewal Date', 'Term Date', 'Banking Info', 'Credit Card Info', 'Fronter', 'Closer'));



	if(!empty($result['policies'])){
		foreach($result['policies'] as $key=>$var){
			$person = "";
			$personId = "";
			if(!empty($result['persons'])){
				foreach($result['persons'] as $key2=>$var2){
					
					if($var['_parentId'] == $var2['_id']){
						$personId = $var2['_id'];
						$person['firstname']  =  $apiObj->getValues($var2, "firstName");
						$person['middlename']  =  $apiObj->getValues($var2, "middleName");
						$person['lastname']  =  $apiObj->getValues($var2, "lastName");
						$person['lastname']  =  $apiObj->getValues($var2, "lastName");
						$person['gender']  =  $apiObj->getValues($var2, "gender");
						$person['ssn']  =  $apiObj->getDecrypt($apiObj->getValues($var2, "socialSecurityNumber"));
						$person['dateOfBirth']  = date("m/d/Y",strtotime($apiObj->getValues($var2, "dateOfBirth")));
						$person['smokerTabacco']  =  $apiObj->getValues($var2, "smokerTabacco");
						$person['disposition'] =  $apiObj->getValues($var2, "disposition");
						$person['hasBankInfo'] = "N";
						if( ( !empty($var2['banking'][0]['paymentBankRoutingNumber']) ) && ( !empty($var2['banking'][0]['paymentBankAccountNumber']) ) ) {
							$person['hasBankInfo'] = "Y";
						}
						$person['hasCreditCard'] = "N";
						if( ( !empty($var2['creditcard'][0]['paymentCardNumber']) ) && ( !empty($var2['creditcard'][0]['paymentCreditCardMonth']) )  && ( !empty($var2['creditcard'][0]['paymentCreditCardYear']) ) ) {
							$person['hasCreditCard'] = "Y";
						}
						break;
					}
				}
			}

			$phones = array();
			if(!empty($result['phones'])){
				foreach($result['phones'] as $key2=>$var2){
					if($var['_parentId'] == $var2['_parentId']){
						$phones[] = $apiObj->getValues($var2, "phoneNumber");
						break;
					}
				}
			}

			$address = array();
			if(!empty($result['addresses'])){
				foreach($result['addresses'] as $key2=>$var2){
					if($var['_parentId'] == $var2['_parentId']){


						$address[] = array(
							"street1" => $var2['street1'],
							"street2" => $var2['street2'],
							"city" => $var2['[city'],
							"state" => $var2['state'],
							"zipCode" => $var2['zipCode'],
							"county" => $var2['county']
						);
					}
				}
			}
			$carrier = "";
			if(!empty($result['carriers'])){
				foreach($result['carriers'] as $key2=>$var2){
					if($var['carrier'] == $var2['_id']){
						$carrier = $apiObj->getValues($var2, "name");
						break;
					}
				}
			}
			$carrierPlan = "";
			if(!empty($result['carrierPlans'])){
				foreach($result['carrierPlans'] as $key2=>$var2){
					if($var['coverageType'] == $var2['_id']){
						$carrierPlan = $apiObj->getValues($var2, "name");
						break;
					}
				}
			}
			$fronter = "";
			if(!empty($result['users'])){
				foreach($result['users'] as $key2=>$var2){
					if($var['soldBy'] == $var2['_id']){
						$fronter = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname");
						break;
					}
				}
			}
			$closer = "";
			if(!empty($result['users'])){
				foreach($result['users'] as $key2=>$var2){
					if($var['closedBy'] == $var2['_id']){
						$closer = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname");
						break;
					}
				}
			}
			if(empty($var['policySubmitted'])){
				$var['policySubmitted'] = "";
			}
			$row = array();
			$row['id'] = $var['_id'];
			$row['firstname'] = $person['firstname'];
			$row['middlename'] = $person['middlename'];
			$row['lastname'] = $person['lastname'];
			$row['gender'] = $person['gender'];
			$row['ssn'] = $person['ssn'];
			$row['datecreated'] = date("m/d/Y",strtotime($var['_timestampCreated']));
			$row['leadstatus'] = $person['disposition'] ;
			$row['Email'] = $carrier;
			$row['phone1'] = $phones[0];
			if(empty($phones[1])){
				$phones[1] = "";
			}
			$row['phone2'] = $phones[1];
			if(empty($address[0])){
				$address[0]['street1']  = "";
				$address[0]['street2']  = "";
				$address[0]['city']  = "";
				$address[0]['state']  = "";
				$address[0]['zipCode']  = "";
				$person['dateOfBirth']   = "";
			}
			$row['address'] = $address[0]['street1'];
			$row['aptno'] = $address[0]['street2'];
			$row['city'] = $address[0]['city'];
			$row['state'] = $address[0]['state'];
			$row['zip'] = $address[0]['zipCode'];
			$row['dob'] = date("m/d/Y",strtotime($person['dateOfBirth']));
			$row['policyentereddate'] = date("m/d/Y",strtotime($var['_timestampCreated']));
			$row['policynumber'] = $var['policyNumber'];
			$row['Carrier'] = $carrier;
			$row['Coverage Type'] = $carrierPlan;
			$row['Status'] = ucwords(strtolower($var['status']));
			$row['Premium'] = $var['premiumMoney'] ;
			$row['SetupFee'] = $var['SetupFee'] ;
			$row['Subsidy'] =  $var['subsidyMoney'] ;
			$row['Pay Schedule'] = $carrier;
			$row['Effective Date'] = date("m/d/Y",strtotime($var['effectiveDate']));
			$row['Submission Date'] = date("m/d/Y",strtotime($var['effectiveDate']));
			$row['Policy Submitted'] = $var['policySubmitted'];
			$row['Renewal Date'] = date("m/d/Y",strtotime($var['renewalDate']));
			$row['Term Date'] = date("m/d/Y",strtotime($var['termDate']));
			$row['Has Bank Info'] = $person['hasBankInfo'];
			$row['Has Credit Card'] =  $person['hasCreditCard'];
			$row['Fronter'] = $fronter;
			$row['Closer'] = $closer;

			fputcsv($out, $row);


		}

	}


	fclose($out);
	exit ();


});




/*
    $apiObj->mongoSetDB($settings['database']);
    $post['userGroups_0_createThing'] = "Y";
    $post['userGroups_0_id'] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
    $post['userGroups_0_label'] = "Sales";
    $post['userGroups_0_users_0_userId'] = "20151005143440-ZpL1bu2Q-0rKCOjTg";
    $post['userGroups_0_users_0_level'] = "manager";
    $apiObj->mongoSetDB($settings['database']);
    $post['userGroups_0_createThing'] = "Y";
    $post['userGroups_0_id'] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
    $post['userGroups_0_label'] = "Sales";
  //  $post['userGroups_0_users_0_id'] =  'sj2R9KF4-Esn18eXl-PCrdFghV';
    $post['userGroups_0_users_0_userId'] = "20151005094323-eNsOqMnP-cfqe1KQL";
    $post['userGroups_0_users_0_level'] = "admin";
  //  $post['userGroups_0_users_1_id'] =  'F9dHAL54-RsAsQj3m-9U1kKG8U';
    $post['userGroups_0_users_1_userId'] = "20151005133707-sqSkuwWt-q2tLHqXU";
    $post['userGroups_0_users_1_level'] = "user";
    $post['userGroups_0_users_2_userId'] = "20151005133721-yxysRHVa-dRHl82gP";
    $post['userGroups_0_users_2_level'] = "user";
    $post['userGroups_0_users_3_userId'] = "20151005133852-nkhEQwXS-dt8BHSd5";
    $post['userGroups_0_users_3_level'] = "user";
    $post['userGroups_0_users_4_userId'] = "20151005134133-XnakJJkb-G9KC7MU8";
    $post['userGroups_0_users_4_level'] = "user";
      $post['userGroups_0_users_5_userId'] = "20151005134247-QtJ59VVa-8YmNLKzT";
    $post['userGroups_0_users_5_level'] = "user";
  //  $post['userGroups_0_users_2_userId'] = "2d20b1dc-9cf6-2f66-981e-54b5ad871df4";
  //  $post['userGroups_0_users_2_level'] = "user";
//    $post['userGroups_0_users_3_userId'] = "dwFfagta-xmeUr4K4-S7coafmJ";
//    $post['userGroups_0_users_3_level'] = "manager";
  //  $post['userGroups_0_users_4_userId'] = "906cd740-04d7-1986-431c-550c41acd635";
   // $post['userGroups_0_users_4_level'] = "user";
    //$post['userGroups_0_permission_0_admin'] = "viewLevel,viewBelow,groupReports,individualReports,assignUers,export";
    //$post['userGroups_0_permission_1_manager'] = "viewLevel,viewBelow,groupReports,individualReports,assignUsers";
    //$post['userGroups_0_permission_2_user'] = "viewLevel,viewBelow,individualReports";
       $apiObj->save_things($post);
*/
/*
$app->get('/phoneremove/:phone_id', function ($phone_id) use ($app,$settings) {
    if((!empty($phone_id))){
        $apiObj = new apiclass($settings);
        if($apiObj->userLoggedIn()){
            $apiObj->mongoSetDB($settings['database']);
            $apiObj->mongoSetCollection("phones");
            $collectionQuery = array('_id' => $phone_id);
            $apiObj->mongoRemove($collectionQuery);
        } else {
            echo "User Not Logged In";
            exit();
        }
    }
});
*/


$app->map('/admininfo/:policyid', function ($policyid) use ($app,$settings) {
	$result = array();
	// etc.

	$apiObj = new apiclass($settings);
	if($apiObj->userLoggedIn()){
		$apiObj->mongoSetDB($settings['database']);

		$apiObj->mongoSetCollection("policy");
		$collectionQuery = array('_id' => $policyid );
		$cursor = $apiObj->mongoFind($collectionQuery);
		$x = 0;
		if($cursor->count() == 0){
		} else {
			foreach (iterator_to_array($cursor) as $doc) {
				if(!empty($doc['_timestampCreated'])){
					$doc['date_created'] = date("m/d/Y",$doc['_timestampCreated']);
				} else {
					$doc['date_created'] = "New";   
				}
				$result['policy'][] = $apiObj->get_thing_display($doc);
				//$result['leads'][] = $doc;

				$parent_id = $doc['_parentId'];

			}
		}
		// Get Addresses
		$apiObj->mongoSetCollection("addresses");
		$collectionQuery = array('_parentId' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['addresses'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['addresses'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Phones
		$apiObj->mongoSetCollection("phones");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['phones'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['phones'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Emails
		$apiObj->mongoSetCollection("emails");
		$collectionQuery = array('_parentId' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['emails'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['emails'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Notes
		$apiObj->mongoSetCollection("notes");
		$collectionQuery = array('_parentId' =>$parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['notes'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['notes'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Policies
		$apiObj->mongoSetCollection("person");
		$collectionQuery = array('_id' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['person'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['person'][] = $apiObj->get_thing_display($doc2);
			}
		}


		if(!empty($_SESSION['api']['user']['_id'])){
			$post['person_0_createThing'] = "Y";
			$post['person_0_id'] = $parent_id;
			$post['person_0_history_0_createThing'] = "Y";
			$post['person_0_history_0_note'] = "Admin Viewed this lead";
			$post['person_0_history_0_userId'] = $_SESSION['api']['user']['_id'];
			$post['person_0_history_0_userName'] = $_SESSION['api']['user']['firstname'] . " " . $_SESSION['api']['user']['lastname'];
			$apiObj->save_things($post);
		}

		// Get Appointments
		$apiObj->mongoSetCollection("appointment");
		$collectionQuery = array('_parentId' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['appointment'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['appointment'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get Employer
		$apiObj->mongoSetCollection("employer");
		$collectionQuery = array('_parentId' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['employer'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['employer'][] = $apiObj->get_thing_display($doc2);
			}
		}
		// Get History
		$apiObj->mongoSetCollection("history");
		$collectionQuery = array('_parentId' => $parent_id);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['history'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['history'][] = $apiObj->get_thing_display($doc2);
			}
		}
	} else {
		echo "User Not Logged In";
		exit();
	}

	$app->render('admintab.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj));
})->via('GET','POST');
$app->map('/attSend', function () use ($app, $settings)  {

	if(!empty($_FILES)){
		if(!empty($_POST['attachmentCount'])){
			$i = $_POST['attachmentCount'];
		}else{
			$i=0;
		}
		$_POST['person_0_createThing'] = 'Y';
		foreach($_FILES['file']['name'] as $key=>$attachment){
			$info = pathinfo($attachment);
			$ext = $info['extension']; // get the extension of the file
			$newname = str_replace('/tmp/','',$_FILES['file']['tmp_name'][$key]).'.'.$ext;
			$target = '../../files/'.$newname;
			move_uploaded_file( $_FILES['file']['tmp_name'][$key], $target);
			$_POST['person_0_attachments_'.$i.'_createThing'] = 'Y';
			$_POST['person_0_attachments_'.$i.'_tmpName'] = $_FILES['file']['tmp_name'][$key];
			$_POST['person_0_attachments_'.$i.'_size'] = $_FILES['file']['size'][$key];
			$_POST['person_0_attachments_'.$i.'_type'] = $_FILES['file']['type'][$key];
			$_POST['person_0_attachments_'.$i.'_error'] = $_FILES['file']['error'][$key];
			$_POST['person_0_attachments_'.$i.'_name'] = $attachment;
			$i++;
		}
		$_POST['person_0_attachmentCount'] = $i;
	}
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->save_things($_POST);
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	$response->body(json_encode($_POST));
})->via('GET','POST');


$app->run();