<?php
require '../app.php';
$app->config(array(
	'templates.path' => './',
));

$app->get('/export-clients', function () use ($app,$settings) {
	if(strtoupper($_SESSION['api']['user']['permissionLevel']) != "ADMINISTRATOR"){
		echo 'Permission define';
		return;
	}
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=clients.csv');

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// // output the column headings
	// // fputcsv($output, array('ID', 'Lastname', 'Firstname', 'Date of Birth', 'Phone', 'Email', 'Street Address 1', 'Street Address 2', 'State', 'City', 'Zip Code', 'County'));
	fputcsv($output, array(	'Primary-First-Name', 'Primary-Last-Name', 'Primary-Cell-Phone', 'Primary-Email-Primary', 
							'Primary-Gender', 'Primary-Age', 'Primary-Tobacco',	'Spouse-Age', 
							'Spouse-Tobacco-Use', 'Lead-Date', 'Lead-Time', 'Lead-Source', 
							'Lead-Source-Description', 'Estimated-Yearly-Income', 'Estimated-Monthly-Budget', 
							'Primary-Address',	'Primary-State', 'Primary-City',	'Primary-Zip', 
							'Total-Dependents', 
							'Lead-Note1', 'Lead-Note2', 'Lead-Note3', 'Lead-Note4', 'Lead-Note5', 
							'Lead-Note6', 'Lead-Note7', 'Lead-Note8', 'Lead-Note9', 'Lead-Note10', 
							'Lead-Attachment1', 'Lead-Attachment2', 'Lead-Attachment3', 'Lead-Attachment4', 'Lead-Attachment5', 
							'Lead-Attachment6', 'Lead-Attachment7', 'Lead-Attachment8', 'Lead-Attachment9', 'Lead-Attachment10', 'Family-Member-Type1', 
							'Family-Member-Title1', 'Family-Member-First-Name1', 'Family-Member-Last-Name1', 'Family-Member-Suffix1', 
							'Family-Member-Gender1', 'Family-Member-Birth-Date1', 'Family-Member-Social-Security-No1', 
							'Family-Member-Tobacco1', 'Family-Member-Height1', 'Family-Member-Weight1', 'Family-Member-Type2', 
							'Family-Member-Title2', 'Family-Member-First-Name2', 'Family-Member-Last-Name2', 'Family-Member-Suffix2', 
							'Family-Member-Gender2', 'Family-Member-Birth-Date2', 'Family-Member-Social-Security-No2', 
							'Family-Member-Tobacco2', 'Family-Member-Height2', 'Family-Member-Weight2', 'Family-Member-Type3', 
							'Family-Member-Title3', 'Family-Member-First-Name3', 'Family-Member-Last-Name3', 'Family-Member-Suffix3', 
							'Family-Member-Gender3', 'Family-Member-Birth-Date3', 'Family-Member-Social-Security-No3', 
							'Family-Member-Tobacco3', 'Family-Member-Height3', 'Family-Member-Weight3', 'Family-Member-Type4', 
							'Family-Member-Title4', 'Family-Member-First-Name4', 'Family-Member-Last-Name4', 'Family-Member-Suffix4', 
							'Family-Member-Gender4', 'Family-Member-Birth-Date4', 'Family-Member-Social-Security-No4', 
							'Family-Member-Tobacco4', 'Family-Member-Height4', 'Family-Member-Weight4', 'Family-Member-Type5', 
							'Family-Member-Title5', 'Family-Member-First-Name5', 'Family-Member-Last-Name5', 'Family-Member-Suffix5', 
							'Family-Member-Gender5', 'Family-Member-Birth-Date5', 'Family-Member-Social-Security-No5', 
							'Family-Member-Tobacco5', 'Family-Member-Height5', 'Family-Member-Weight5', 'Lead-Policy-Carrier1', 
							'Lead-Policy-Policy-Number1', 'Lead-Policy-Coverage-Type1', 'Lead-Policy-Submission-Date1', 
							'Lead-Policy-Renewal-Date1', 'Lead-Policy-Effective-Date1', 'Lead-Policy-Payment-Date1', 
							'Lead-Policy-Disposition1', 'Lead-Policy-Setup-Fee1', 'Lead-Policy-Monthly-Premium1', 
							'Lead-Policy-Subsidy-Amount1', 'Lead-Policy-Sold-By1', 'Lead-Policy-Closed-By1', 'Lead-Policy-Manager1', 
							'Lead-Policy-Notes1', 'Lead-Policy-Updated-By1', 'Lead-Policy-Carrier2', 'Lead-Policy-Policy-Number2', 
							'Lead-Policy-Coverage-Type2', 'Lead-Policy-Submission-Date2', 'Lead-Policy-Renewal-Date2', 
							'Lead-Policy-Effective-Date2', 'Lead-Policy-Payment-Date2', 'Lead-Policy-Disposition2', 
							'Lead-Policy-Setup-Fee2', 'Lead-Policy-Monthly-Premium2', 'Lead-Policy-Subsidy-Amount2', 
							'Lead-Policy-Sold-By2', 'Lead-Policy-Closed-By2', 'Lead-Policy-Manager2', 'Lead-Policy-Notes2', 
							'Lead-Policy-Updated-By2',	'Lead-Policy-Carrier3', 'Lead-Policy-Policy-Number3', 
							'Lead-Policy-Coverage-Type3', 'Lead-Policy-Submission-Date3', 'Lead-Policy-Renewal-Date3', 
							'Lead-Policy-Effective-Date3', 'Lead-Policy-Payment-Date3', 'Lead-Policy-Disposition3', 
							'Lead-Policy-Setup-Fee3', 'Lead-Policy-Monthly-Premium3', 'Lead-Policy-Subsidy-Amount3', 
							'Lead-Policy-Sold-By3', 'Lead-Policy-Closed-By3', 'Lead-Policy-Manager3', 'Lead-Policy-Notes3', 
							'Lead-Policy-Updated-By3', 'Lead-Policy-Carrier4', 'Lead-Policy-Policy-Number4', 
							'Lead-Policy-Coverage-Type4', 'Lead-Policy-Submission-Date4', 'Lead-Policy-Renewal-Date4', 
							'Lead-Policy-Effective-Date4', 'Lead-Policy-Payment-Date4', 'Lead-Policy-Disposition4', 
							'Lead-Policy-Setup-Fee4', 'Lead-Policy-Monthly-Premium4', 'Lead-Policy-Subsidy-Amount4', 
							'Lead-Policy-Sold-By4', 'Lead-Policy-Closed-By4', 'Lead-Policy-Manager4', 'Lead-Policy-Notes4', 
							'Lead-Policy-Updated-By4', 'Lead-Policy-Carrier5', 'Lead-Policy-Policy-Number5', 
							'Lead-Policy-Coverage-Type5', 'Lead-Policy-Submission-Date5', 'Lead-Policy-Renewal-Date5', 
							'Lead-Policy-Effective-Date5', 'Lead-Policy-Payment-Date5', 'Lead-Policy-Disposition5', 
							'Lead-Policy-Setup-Fee5', 'Lead-Policy-Monthly-Premium5', 'Lead-Policy-Subsidy-Amount5', 
							'Lead-Policy-Sold-By5', 'Lead-Policy-Closed-By5', 'Lead-Policy-Manager5', 'Lead-Policy-Notes5', 
							'Lead-Policy-Updated-By5', 'Lead-Employment-Status1', 'Lead-Tax-Year1', 'Lead-Estimated-Yearly-Income1', 
							'Lead-Following-Year-Income1', 'Lead-File-Taxes1', 'Lead-File-Jointly1', 'Lead-Are-Dependent1', 
							'Lead-Employment-Status2', 'Lead-Tax-Year2', 'Lead-Estimated-Yearly-Income2', 
							'Lead-Following-Year-Income2', 'Lead-File-Taxes2', 'Lead-File-Jointly2', 'Lead-Are-Dependent2', 
							'Lead-Employment-Status3', 'Lead-Tax-Year3', 'Lead-Estimated-Yearly-Income3', 
							'Lead-Following-Year-Income3', 'Lead-File-Taxes3',	'Lead-File-Jointly3', 'Lead-Are-Dependent3', 
							'Lead-Employment-Status4', 'Lead-Tax-Year4', 'Lead-Estimated-Yearly-Income4', 'Lead-Following-Year-Income4', 
							'Lead-File-Taxes4', 'Lead-File-Jointly4', 'Lead-Are-Dependent4', 'Lead-Employment-Status5', 
							'Lead-Tax-Year5', 'Lead-Estimated-Yearly-Income5', 'Lead-Following-Year-Income5', 'Lead-File-Taxes5', 
							'Lead-File-Jointly5', 'Lead-Are-Dependent5', 'Lead-Other-Income-Person1', 'Lead-Other-Income-Type1', 
							'Lead-Other-Income-Amount1', 'Lead-Other-Income-Frequency1', 'Lead-Other-Income-Note1', 
							'Lead-Other-Income-Person2', 'Lead-Other-Income-Type2', 'Lead-Other-Income-Amount2', 
							'Lead-Other-Income-Frequency2', 'Lead-Other-Income-Note2', 'Lead-Other-Income-Person3', 
							'Lead-Other-Income-Type3', 'Lead-Other-Income-Amount3', 'Lead-Other-Income-Frequency3', 'Lead-Other-Income-Note3', 'Lead-Other-Income-Person4', 'Lead-Other-Income-Type4', 'Lead-Other-Income-Amount4', 'Lead-Other-Income-Frequency4', 'Lead-Other-Income-Note4', 'Lead-Other-Income-Person5', 'Lead-Other-Income-Type5', 'Lead-Other-Income-Amount5', 'Lead-Other-Income-Frequency5', 'Lead-Other-Income-Note5', 'Lead-Employer-Name1', 'Lead-Employer-Person-Employed1', 'Lead-Employer-Phone1', 'Lead-Employer-Address1', 'Lead-Employer-State1', 'Lead-Employer-City1', 'Lead-Employer-ZipCode1', 'Lead-Employer-Wages1', 'Lead-Employer-Pay-Frequency1', 'Lead-Employer-Hours-Per-Week1', 'Lead-Employer-Name2', 'Lead-Employer-Person-Employed2', 'Lead-Employer-Phone2', 'Lead-Employer-Address2', 'Lead-Employer-State2', 'Lead-Employer-City12', 'Lead-Employer-ZipCode2', 'Lead-Employer-Wages2', 'Lead-Employer-Pay-Frequency2', 'Lead-Employer-Hours-Per-Week2', 'Lead-Employer-Name3', 'Lead-Employer-Person-Employed3', 'Lead-Employer-Phone3', 'Lead-Employer-Address3', 'Lead-Employer-State3', 'Lead-Employer-City3', 'Lead-Employer-ZipCode3', 'Lead-Employer-Wages3', 'Lead-Employer-Pay-Frequency3', 'Lead-Employer-Hours-Per-Week3', 'Lead-Employer-Name4', 'Lead-Employer-Person-Employed4', 'Lead-Employer-Phone4', 'Lead-Employer-Address4', 'Lead-Employer-State4', 'Lead-Employer-City4', 'Lead-Employer-ZipCode4', 'Lead-Employer-Wages4', 'Lead-Employer-Pay-Frequency4', 'Lead-Employer-Hours-Per-Week4', 'Lead-Employer-Name5', 'Lead-Employer-Person-Employed5', 'Lead-Employer-Phone5', 'Lead-Employer-Address5', 'Lead-Employer-State5', 'Lead-Employer-City5', 'Lead-Employer-ZipCode5', 'Lead-Employer-Wages5', 'Lead-Employer-Pay-Frequency5', 'Lead-Employer-Hours-Per-Week5'));


	$apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $collectionQuery = array();

    $apiObj->mongoSetCollection("systemForm");
    $cursor2 = $apiObj->mongoFind(false);
    if($cursor2->count() == 0){
        $result['systemForm'][] = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $result['systemForm'][$doc2['name']] = $doc2;
        }
    }

    // Get Carrier
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

    $apiObj->mongoSetCollection("person");
    $countLeadResouces = $apiObj->mongoCount($collectionQuery);
    // echo $countLeadResouces . '</br>';die(); 
    $take = 1000;
    $skip = intval($countLeadResouces/$take);
    if(is_float($countLeadResouces/$take)){
        $skip = intval($skip) + 1;
    }
    if($skip == 0)
        $result['person'][] = array();
    $j = 0;
    // echo $skip; die();
    for ($i=0; $i < $skip; $i++) { 
    // for ($i=0; $i < 1; $i++) { 	
    	// $cursor2 = $apiObj->mongoFind($collectionQuery);
    	$apiObj->mongoSetCollection("person");
        $cursor2 = $apiObj->mongoFind2($collectionQuery, $i, $take);
        $cursor2->sort(array('_id' => -1));
        foreach (iterator_to_array($cursor2) as $doc2) {
      //   	echo '<pre>';
    		// var_dump($doc2);
    		// echo '</pre>';
    		// die();
        	$j++;
        	 // x. Get email 
            $apiObj->mongoSetCollection("emails");
            $itemEmail = $apiObj->mongofindOne(array('_parentId'=> $doc2['_id']));
            if(empty($itemEmail)){
            	$doc2['email'] = '';
	        } else{
	        	$doc2['email'] = $itemEmail['email'];
	        }
	        
	        // x. Get phone 
	        $apiObj->mongoSetCollection("phones");
            $itemPhone = $apiObj->mongofindOne(array('_parentId'=> $doc2['_id']));
            if(empty($itemPhone)){
            	$doc2['phoneNumber'] = '';
	        } else{
		        $doc2['phoneNumber'] = $itemPhone['phoneNumber'];
	        }

	        // x. Get address 
	        $apiObj->mongoSetCollection("addresses");
            $itemAddress = $apiObj->mongofindOne(array('_parentId'=> $doc2['_id']));
            if(empty($itemAddress)){
	        } else{
		        $doc2['street1'] = $itemAddress['street1'];
		        $doc2['street2'] = $itemAddress['street2'];
		        $doc2['city'] = $itemAddress['city'];
		        $doc2['state'] = $itemAddress['state'];
		        $doc2['zipCode'] = $itemAddress['zipCode'];
		        $doc2['country'] = $itemAddress['country'];

		        if(!empty($result['systemForm']['state']['options']) && !empty($doc2['state'])){
		            foreach($result['systemForm']['state']['options'] as $sKey=>$sVal){
		                if($sVal['value'] == strtoupper($doc2['state'])){
		                    $state = strtoupper($sVal['label']);
		                    // $stateValue = $sVal['value'];
		                    // var_dump($sVal);die();
		                    $doc2['state2'] = $state;
		                    break;
		                }
		            }
		        }
	        }

	        $doc2['socialSecurityNumber2'] = $apiObj->getDecrypt($doc2['socialSecurityNumber']);
	        
	        if(!empty($doc2['dateOfBirth'])){
	        	$doc2['dateOfBirth2'] = date("m/d/Y",strtotime($doc2['dateOfBirth']));
	        	$today = date("Y-m-d");
	        	$diff = date_diff(date_create($doc2['dateOfBirth2']), date_create($today));
				$age_person = $diff->format('%y');
	        }else{
	        	$age_person = '';
	        }

	        $today = date("Y-m-d");
	        	
	        if(!empty($doc2['spouse'][0]['spouseDateOfBirth'])){
	        	$doc2['spouse'][0]['spouseDateOfBirth2'] = date("m/d/Y",strtotime($doc2['spouse'][0]['spouseDateOfBirth']));
	        	$diff = date_diff(date_create($doc2['spouse'][0]['spouseDateOfBirth2']), date_create($today));
				$age_spouse = $diff->format('%y');

	        }else{
	        	$age_spouse = '';
	        }

	        // Tax 
	        if(count($doc2['taxes']) > 0){
	        	$estimatedYearlyIncome = $doc2['taxes'][0]['estimatedYearlyIncome'];
	        }else{
	        	$estimatedYearlyIncome = 0;
	        }

	        // var_dump($paymentCreditCardType); die();
	       	$row = array();
	       	$row = array(	$doc2['firstName'], $doc2['lastName'],
	       					$doc2['phoneNumber'], $doc2['email'], 
	       					$doc2['gender'],
	       					$age_person, $doc2['smokerTabacco'], $age_spouse, $doc2['spouse'][0]['spouseSmoker'],
	       					date("m/d/Y",strtotime($doc2['_timestampCreated'])), date("h:i:s A",strtotime($doc2['_timestampCreated'])), $doc2['leadSource'],'',
	       					$estimatedYearlyIncome, '', $doc2['street1'], $doc2['state'], $doc2['city'], $doc2['zipCode'],
	       					count($doc2['dependents']),
	       				);

	       	// Notes
	       	$apiObj->mongoSetCollection("notes");
			$collectionQuery2 = array('_parentId' => $doc2['_id']);
			$cursor3 = $apiObj->mongoFind($collectionQuery2);
			$note_count = $cursor3->count();
			foreach (iterator_to_array($cursor3) as $var) {
				array_push($row, $var['information']);
			}
			for ($k=0; $k < (10 - intval($note_count)); $k++) { 
				array_push($row, '');
			}

			// Attachments
	       	$apiObj->mongoSetCollection("attachments");
			$collectionQuery2 = array('_parentId' => $doc2['_id']);
			$cursor3 = $apiObj->mongoFind($collectionQuery2);
			$attachment_count = $cursor3->count();
			foreach (iterator_to_array($cursor3) as $var) {
				array_push($row, $var['name']);
			}
			for ($k=0; $k < (10 - intval($attachment_count)); $k++) { 
				array_push($row, '');
			}

			// Family
			$family_count = 0;
			if(isset($doc2['dependents'])){
				$family_count = count($doc2['dependents']);
				
				foreach ($doc2['dependents'] as $var) {
					array_push($row, '');
					array_push($row, '');
					array_push($row, $var['dependentsFirstName']);
					array_push($row, $var['dependentsLastName']);
					array_push($row, '');
					array_push($row, $var['gender']);
					array_push($row, date("m/d/Y",strtotime($var['dependentsDateOfBirth'])));
					array_push($row, $apiObj->getDecrypt($var['dependentsSocialSecurityNumber']));
					array_push($row, '');
					array_push($row, '');
					array_push($row, '');
				}
			}

			for ($k=0; $k < (5 - intval($family_count)); $k++) { 
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
			}

	        // var_dump($age_spouse);
	        // die();
	        // fputcsv($output, array($doc2['firstName'], $doc2['lastName'], $doc2['dateOfBirth2'], $doc2['phoneNumber'], $doc2['email'], $doc2['street1'], $doc2['street2'], $doc2['state2'], $doc2['city'], $doc2['zipCode'], $doc2['country']));
	    	
	    	// Get Policies
			$apiObj->mongoSetCollection("policy");
			$collectionQuery2 = array('_parentId' => $doc2['_id']);
			$cursor3 = $apiObj->mongoFind($collectionQuery2);
			$policy_count = $cursor3->count();
			
			$cursor3->sort(array('sort' => -1));
			
			foreach (iterator_to_array($cursor3) as $var) {
				
				$carrier = "";
		        if(!empty($result['carriers'])){
		        	foreach($result['carriers'] as $key2=>$var2){
		                if($var['carrier'] == $var2['_id']){
		                    $carrier = $apiObj->getValues($var2, "name");
		                    break;
		                }
		            }
		        }
		        array_push($row, $carrier);
		        array_push($row, $var['policyNumber']);
		       	// var_dump($carrier);
	       		$carrierPlan = "";
		        if(!empty($result['carrierPlans'])){
		            foreach($result['carrierPlans'] as $key2=>$var2){
		                if($var['coverageType'] == $var2['_id']){
		                    $carrierPlan = $apiObj->getValues($var2, "name");
		                    break;
		                }
		            }
		        }
		        array_push($row, $carrierPlan);
		        array_push($row, date("m/d/Y",strtotime($var['submissionDate'])));
		        array_push($row, date("m/d/Y",strtotime($var['renewalDate'])));
		        array_push($row, date("m/d/Y",strtotime($var['effectiveDate'])));
		        array_push($row, date("m/d/Y",strtotime($var['dateToPay'])));
		        array_push($row, $var['status']);
		        array_push($row, $var['setupFeeMoney']);
		        array_push($row, $var['premiumMoney']);
		        array_push($row, $var['subsidyMoney']);

		        // var_dump($carrierPlan);
		        $fronter = "";
		        if(!empty($result['users'])){
		            foreach($result['users'] as $key2=>$var2){
		                if($var['soldBy'] == $var2['_id']){
		                  $ActiveUser = "";
		                   if(substr($apiObj->getValues($var2, "status"),0,1) == "I"){
		                        $ActiveUser = "(Inactive)";
		                   }
		                    $fronter = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname") . " ".$ActiveUser;
		                    break;
		                }
		            }
		        }

		        array_push($row, $fronter);
		        // var_dump($fronter);
		        $closer = "";
		        if(!empty($result['users'])){
		            foreach($result['users'] as $key2=>$var2){
		                if($var['closedBy'] == $var2['_id']){
		                    $closer = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname") ;
		                    break;
		                }
		            }
		        }

		        array_push($row, $var['closer']);
		        array_push($row, $var['submitMainPerson']);
		        array_push($row, $var['notes']);
		        array_push($row, date("m/d/Y",strtotime($var['_timestampModified'])));
		        // var_dump($closer);
	       		// fputcsv($output, array('', date("m/d/Y",strtotime($var['_timestampCreated'])), date("m/d/Y",strtotime($var['submissionDate'])), date("m/d/Y",strtotime($var['dateToPay'])), ucwords(strtolower($var['status'])), $var['policyNumber'], $carrier, $carrierPlan, $fronter, $closer, $var['premiumMoney']));
		
	       	
			}

			for ($k=0; $k < (5 - intval($policy_count)); $k++) { 
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
			}

			// Taxes
			$tax_count = 0;

			if(isset($doc2['taxes'])){
				$tax_count = count($doc2['taxes']);
				foreach ($doc2['taxes'] as $var) {
					array_push($row, $var['employmentStatus']);
					array_push($row, $var['incomeYear']);
					array_push($row, $var['estimatedYearlyIncome']);
					array_push($row, $var['estimatedFollowingIncom']);
					array_push($row, $var['planToFileTaxes']);
					array_push($row, $var['fileTaxesJointly']);
					array_push($row, $var['taxesAreYourADependent']);
					
				}
			}
			for ($k=0; $k < (5 - intval($tax_count)); $k++) { 
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
			}


			// Income Sources
			$income_count = 0;
			if(isset($doc2['incomeSources'])){
				$income_count = count($doc2['incomeSources']);
				foreach ($doc2['incomeSources'] as $var) {
					array_push($row, $var['personOfIncome']);
					array_push($row, $var['incomeType']);
					array_push($row, $var['incomeMoney']);
					array_push($row, $var['incomeFrequency']);
					array_push($row, $var['']);
				}
			}

			for ($k=0; $k < (5 - intval($income_count)); $k++) { 
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
			}

			// Employer
			$employer_count = 0;
			if(isset($doc2['employers'])){
				$income_count = count($doc2['employers']);
				foreach ($doc2['employers'] as $var) {
					array_push($row, $var['name']);
					array_push($row, $var['personEmployed']);
					array_push($row, $var['phone']);
					array_push($row, $var['address']);
					array_push($row, $var['state']);
					array_push($row, $var['city']);
					array_push($row, $var['zipcode']);
					array_push($row, $var['wages']);
					array_push($row, $var['payFrequency']);
					array_push($row, $var['hoursWeekly']);
				}
			}

			for ($k=0; $k < (5 - intval($employer_count)); $k++) { 
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
				array_push($row, '');
			}



			fputcsv($output, $row);

			
	    }
        // return;
    }// End for
});

$app->get('/update-ui', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);

    // Underwriting Status
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20170222142720-kV1R33Fv-zeXtzMKJ';
    $form['systemForm_0_label'] = 'Underwriting Status';
    $form['systemForm_0_thing'] = 'policy';
    $form['systemForm_0_type'] = 'SELECT';
    $form['systemForm_0_name'] = 'underwritingStatus';
    $form['systemForm_0_row'] = '1';
    $form['systemForm_0_sort'] = '4';
    $form['systemForm_0_columns'] = '3';

    $form['systemForm_0_options_0_id'] = '20170222013957-hrUDutoC-bSghi89a';
    $form['systemForm_0_options_0_default'] = 'Y';
    $form['systemForm_0_options_0_label'] = '';
    $form['systemForm_0_options_0_value'] = '';

    $form['systemForm_0_options_1_id'] = '20170222013957-CpZ2SlQf-m7G1LtYz';
    $form['systemForm_0_options_1_default'] = 'N';
    $form['systemForm_0_options_1_label'] = 'Active';
    $form['systemForm_0_options_1_value'] = 'ACTIVE';

    $form['systemForm_0_options_2_id'] = '20170222013957-wDcJNfLr-81jogx1g';
    $form['systemForm_0_options_2_default'] = 'N';
    $form['systemForm_0_options_2_label'] = 'Late Payment';
    $form['systemForm_0_options_2_value'] = 'LATEPAYMENT';

    $form['systemForm_0_options_3_id'] = '20170222013957-T5ZUIXrz-fyItfV8e';
    $form['systemForm_0_options_3_default'] = 'N';
    $form['systemForm_0_options_3_label'] = 'Cancelled';
    $form['systemForm_0_options_3_value'] = 'CANCELLED';

    $form['systemForm_0_options_4_id'] = '20170222013957-YAirGkR7-IbjyanO4';
    $form['systemForm_0_options_4_default'] = 'N';
    $form['systemForm_0_options_4_label'] = 'Pending UW';
    $form['systemForm_0_options_4_value'] = 'PENDINGUW';

    $form['systemForm_0_options_5_id'] = '20170222013957-PteagsQ9-iBuAVLYj';
    $form['systemForm_0_options_5_default'] = 'N';
    $form['systemForm_0_options_5_label'] = 'Pending Initial Payment';
    $form['systemForm_0_options_5_value'] = 'PENDINGINITIALPAYMENT';

    $form['systemForm_0_options_6_id'] = '20170222013957-7QrnROaN-bY4OH2CA';
    $form['systemForm_0_options_6_default'] = 'N';
    $form['systemForm_0_options_6_label'] = 'Payment Declined';
    $form['systemForm_0_options_6_value'] = 'PAYMENTDECLINED';

    $apiObj->save_things($form);

    // Commission Status
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20170222142720-aS9R22Wv-zeXtzDLJ';
    $form['systemForm_0_label'] = 'Commission Status';
    $form['systemForm_0_thing'] = 'policy';
    $form['systemForm_0_type'] = 'SELECT';
    $form['systemForm_0_name'] = 'commissionStatus';
    $form['systemForm_0_row'] = '1';
    $form['systemForm_0_sort'] = '4';
    $form['systemForm_0_columns'] = '3';

    $form['systemForm_0_options_0_id'] = '20170222013652-oK4zd6MK-ZX2fbszc';
    $form['systemForm_0_options_0_default'] = 'Y';
    $form['systemForm_0_options_0_label'] = '';
    $form['systemForm_0_options_0_value'] = '';

    $form['systemForm_0_options_1_id'] = '20170222013652-nJHLc0lZ-tjl9AxKu';
    $form['systemForm_0_options_1_default'] = 'N';
    $form['systemForm_0_options_1_label'] = 'Pending';
    $form['systemForm_0_options_1_value'] = 'PENDING';

    $form['systemForm_0_options_2_id'] = '20170222013652-TJtOIhWE-cxkKksoH';
    $form['systemForm_0_options_2_default'] = 'N';
    $form['systemForm_0_options_2_label'] = 'Advance Received';
    $form['systemForm_0_options_2_value'] = 'ADVANCERECEIVED';

    $form['systemForm_0_options_3_id'] = '20170222013652-9JDSu8zk-Uvu4z04E';
    $form['systemForm_0_options_3_default'] = 'N';
    $form['systemForm_0_options_3_label'] = 'Monthly Payment Received';
    $form['systemForm_0_options_3_value'] = 'MONTHLYPAYMENTRECEIVED';

    $form['systemForm_0_options_4_id'] = '20170222013652-HEn84rwP-TRce2oJA';
    $form['systemForm_0_options_4_default'] = 'N';
    $form['systemForm_0_options_4_label'] = 'Chargeback Received';
    $form['systemForm_0_options_4_value'] = 'CHARGEBACKRECEIVED';

    $apiObj->save_things($form);

    // Policy Status
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'Ar51WmbX-mZI39NTX-7iNnklDG';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Submission Status
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20151112142720-kF1F33Fv-zeXtzMKJ';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Date Submitted Confirmed
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20151112142720-PmDOvk8q-WJe8XeVx';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Policy Number
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'lthF0ITT-cmeWDjz3-x6hGJmRC';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Carrier Name
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'sQKew6IT-wDPKJBzu-IKTLXUzB';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Coverage Type
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'NozFuCG2-sCY8j3pv-yI3vcgTO';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // One Time Setup Fee
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '3LdYy39H-ta2uTNr5-LbxcM6S2';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '2';
    $form['systemForm_0_sort'] = '4';

    $apiObj->save_things($form);

    // Monthly Premium
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'zzAQhrmi-UqoJyBkb-q7geG0h6';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Subsidy Amount
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'aaen6Fph-QyEBSmCg-q2tqUbWC';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Submission Date
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'PHBZrYz3-NIpbUPu2-KT4DnEoW';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Renewal Date
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '7pQNgGkJ-X2B2KO47-E2u0ELzp';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Effective Date
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'SzRHe34R-eCNltXph-8owJkuac';
    $form['systemForm_0_columns'] = '3';

    $apiObj->save_things($form);

    // Payment Date
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'f6uRiEZ2-oKYmSUHV-DP4ilbr6';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '3';

    $apiObj->save_things($form);

    // Sold By
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'jPh01Sv4-Za60ARTP-kG10MI3i';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '4';

    $apiObj->save_things($form);

    // Closed By
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = 'jhszlQo0-BqMqTnjd-ny2j72bN';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '3';

    $apiObj->save_things($form);

    // Submit Main Person with Policy
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20151112141129-FgxMDoqz-yj1mW0iP';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '5';

    $apiObj->save_things($form);

    // Submit Spouse with Policy
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20151112141129-rUhkz6V7-aPE9ZJPs';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '5';

    $apiObj->save_things($form);

    // Submit Dependents with Policy
    $form = false;
    $form['systemForm_0_createThing'] = "Y";
    $form['systemForm_0_id'] = '20151112141129-D9ywtm7e-58dOOkC9';
    $form['systemForm_0_columns'] = '3';
    $form['systemForm_0_row'] = '5';

    $apiObj->save_things($form);

    echo 'update ok';
});

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
		if(!empty($_GET["start_date"]) && !empty($_GET["end_date"])){

	        $result['StartDate'] = $apiObj->validateDate($_GET["start_date"], "m/d/Y", "Ymd000000"); 
	        $result['EndDate'] = $apiObj->validateDate($_GET["end_date"], "m/d/Y", "Ymd235959"); 
		    
			$collectionQuery = array(
                "_timestampCreated" => array(
                    '$gte' =>  $result['StartDate'] ,
                    '$lte' =>  $result['EndDate']
                )
            );
		}
		if(trim($settings['leads']['search']) == ""){
			$collectionQuery['assignedTo']['$in'] = $userIds;
			if(empty($_GET["start_date"]) && empty($_GET["end_date"])){
				$collectionQuery['$and'][]['disposition']['$ne'] = "SOLD";
				$collectionQuery['$and'][]['disposition']['$ne'] = "ONHOLD";
			}
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
		// debug($collectionQuery);
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
    $result['reportsSubmissonStatus'] = "ANY";
    $result['reportsUnderwritingStatus'] = "ANY";
    $result['reportsCommisionStatus'] = "ANY";

    // debug($_GET);
    if(!empty($_GET["reportsSubmissonStatus"])){ $result['reportsSubmissonStatus'] = $_GET["reportsSubmissonStatus"];}
    if(!empty($_GET["reportsUnderwritingStatus"])){ $result['reportsUnderwritingStatus'] = $_GET["reportsUnderwritingStatus"];}
    if(!empty($_GET["reportsCommisionStatus"])){ $result['reportsCommisionStatus'] = $_GET["reportsCommisionStatus"];}

    if(!empty($_GET["reportsStatus"])){ $result['reportsStatus'] = $_GET["reportsStatus"];}
    if(!empty($_GET["reportsStartDate"])){ $result['reportsStartDate'] = $apiObj->validateDate($_GET["reportsStartDate"], "m/d/Y", "Ymd000000"); }
    if(!empty($_GET["reportsEndDate"])){ $result['reportsEndDate'] = $apiObj->validateDate($_GET["reportsEndDate"], "m/d/Y", "Ymd235959"); }
    if(!empty($_GET['reportsCloser'])){ $result['reportsCloser'] =$_GET['reportsCloser']; }
    if(!empty($_GET['reportsFronter'])){ $result['reportsFronter'] = $_GET['reportsFronter']; }
    if(!empty($_GET['reportsCarrier'])){ $result['reportsCarrier'] = $_GET['reportsCarrier']; }
    if(!empty($_GET['reportsCarrierPlan'])){ $result['reportsCarrierPlan'] = $_GET['reportsCarrierPlan']; }
    if(!empty($_GET['reportsState'])){ $result['reportsState'] = $_GET['reportsState']; }
    if(!empty($_GET['reportsLeadSource'])){ $result['reportsLeadSource'] = $_GET['reportsLeadSource']; }

    if(!empty($_GET['reportsUserGroup'])){ 
    	$result['reportsUserGroup'] = $_GET['reportsUserGroup']; 
    	$apiObj->mongoSetCollection("userGroups");
	    $cursor1 = $apiObj->mongoFind();
	    if($cursor1->count() == 0){
	        $userGroupList = array();
	    } else {
	        foreach (iterator_to_array($cursor1) as $doc2) {
	                $userGroupList[$doc2['_id']] = $doc2;
	        }
	    }
	   // debug($userGroupList);
	    $apiObj->mongoSetCollection("user");
	    $cursor1 = $apiObj->mongoFind();
	    if($cursor1->count() == 0){
	        $userList = array();
	    } else {
	        foreach (iterator_to_array($cursor1) as $doc2) {
	            $userList[$doc2['_id']] = $doc2['firstname'] . " " . $doc2['lastname'];
	        }
	    }
	    $userGroups = array();
	    foreach($userList as $key=>$var){
	        foreach($userGroupList as $key2=>$var2){
	            if(!empty($var2['users'])){
	                foreach($var2['users'] as $key3=>$var3){
	                    if ($key == $var3['userId']) {
	                        // if(strtoupper($var3['level']) == "USER" || strtoupper($var3['level']) == "ADMINISTRATOR" || strtoupper($var3['level']) == "MANAGER"){
	                        if(strtoupper($var3['level'])!='NONE'){
	                        	$userGroups[$var3['userId']] = $key2;
	                        }
	                    }
	                }
	            }
	        }
	    }

    }

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

		if(empty($_REQUEST['dashboardStatus'])){
			$collectionQuery['$and'][]['carrier']['$exists'] = TRUE;
			$collectionQuery['$and'][]['carrier']['$ne'] = "";
		}

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
        if($_GET["widget"] == 'clients'){
			$collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		    $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		    $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
            $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "SOLD";
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "HOLD";
		}else if($_GET["widget"] == 'Policies_Sold'){
			$collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		    $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		    $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
            $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
            $collectionQuery['$and'][]['status']['$eq'] = "SOLD";
            
		}else if($_GET["widget"] == 'Policies_Hold'){
			$collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		    $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		    $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
            $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "SOLD";
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "HOLD";
            
		}else if($_GET["dashboardStatus"] == 'Y'){
			$collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		    $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];

		    $statuses = array("HOLD","SOLD");
		    $collectionQuery['status']['$in'] = $statuses;
		    $collectionQuery['soldBy']['$ne'] = "";
		    $collectionQuery['closedBy']['$ne'] = "";
		    
		    if($result['reportsFronter'] != "ANY"){
		        $collectionQuery['soldBy']['$eq'] = $result['reportsFronter'];
		    }
		    if($result['reportsCloser'] != "ANY"){
		        $collectionQuery['closedBy']['$eq'] = $result['reportsCloser'];
		    }
		    // var_dump($collectionQuery);
		}else if(!empty($_GET["reportsStatus"])){
			// $collectionQuery = array();
			if($result['reportsStatus'] == "ANY"){
		        $collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		        $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		    } else {
		        $collectionQuery['_timestampCreated']['$lte'] = $result['reportsEndDate'];
		        $collectionQuery['_timestampCreated']['$gte'] = $result['reportsStartDate'];
		        if($result['reportsStatus'] == 'COMMISSION_PAYMENT_RECEIVED')
		        	$collectionQuery['isReceived']['$eq'] = 'Y';
        		else
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

		    if($result['reportsSubmissonStatus'] != "ANY"){
		        $collectionQuery['policySubmitted']['$eq'] = $result['reportsSubmissonStatus'];
		    }

		    if($result['reportsUnderwritingStatus'] != "ANY"){
		        $collectionQuery['underwritingStatus']['$eq'] = $result['reportsUnderwritingStatus'];
		    }

		    if($result['reportsCommisionStatus'] != "ANY"){
		        $collectionQuery['commissionStatus']['$eq'] = $result['reportsCommisionStatus'];
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
	    // debug($collectionQuery);
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
        if( $_GET['widget'] != 'clients' && empty($result['reportsUserGroup']) && ($result['reportsState'] == "ANY" || empty($result['reportsState'])) && ($result['reportsLeadSource'] == "ANY" || empty($result['reportsLeadSource']))){
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
				
				if($result['reportsUserGroup'] == "ANY" && $result['reportsState'] == "ANY" && $result['reportsLeadSource'] == 'ANY'){
					$cursor->limit($settings['policies']['per_page']);
				}
				$client_array = array();
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
			        // var_dump($doc['_id']);
			  //       if(!empty($result['reportsUserGroup'])){
				 //        	// var_dump($result['reportsUserGroup']);
					//         foreach($userGroupList as $key2=>$var2){
					//         	if($var2['_id'] == $result['reportsUserGroup']){
					//         		if(!empty($var2['users'])){
					// 	                foreach($var2['users'] as $key3=>$var3){
					// 	                	if (strtoupper($var3['level'])!='NONE' && $userGroups[$doc['soldBy']] <> "" && $doc['soldBy'] == $var3['userId']) {
					// 	                       $isItem = true;
					// 	                    }
					// 	                }
					// 	            }
					// 	        }
					//         }
					    
					// }
					if(!empty($result['reportsUserGroup'])){
				        if($userGroups[$doc['soldBy']] <> "" && $userGroupList[$userGroups[$doc['soldBy']]]['_id'] == $result['reportsUserGroup']){
	                		$isItem = true;
				        }
				    }
			        else if($_GET['widget'] == 'clients' && $client_array[$doc['_parentId']] != 1){
			        	$isItem = true;
			        	$client_array[$doc['_parentId']] = 1;
			        	
			        }else if($result['reportsLeadSource'] != 'ANY' && $result['reportsState'] != 'ANY'){
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
				}// End foreach
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
	
	$result['leads'] = array();
	$apiObj = new apiclass($settings);
	$result = getLeadById($personId, $settings);
	// var_dump($result['leads'][0]);
	if (
		((!empty($result['leads'][0]['assignedTo'])) && ($result['leads'][0]['assignedTo'] == $_SESSION['api']['user']['_id']))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "ADMINISTRATOR"))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "MANAGER"))
		|| ((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "INSUREHC"))
		|| ($seller === TRUE)
		|| ($podmanager === TRUE)
		|| (($result['leads'][0]['leadSource'] == "PRECISELEADS") &&  ($result['leads'][0]['assignedTo'] == "20151005154138-k7N1dHZi-4I7ZoB2J") )
	) {
		// echo 'sdfsdf';
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

		if(!empty($income)){
			$income = "&income=$income";
		}
		if(!empty($item_people)){
			$item_people = "&people=[$item_people]";
		}

		$iframe_url = "https://www.healthsherpa.com/find-plans/plans?zip_code=$zip_code". $income . $item_people ."&size=$size&cs=premium&year=2017&page=1&_agent_id=lisa-jackson-lerW0g";
		
		// echo $iframe_url;
		$app->render('leadform.php', array('iframe_url' => $iframe_url, 'result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	} else {
       //	$app->render('leadform.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	   	$app->render('leadview.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj, 'personId'=>$personId));
	}
	peakmemory();
});

function getLeadById($personId, $settings){
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

		// Get Comission
		$result['comissions'] = array();
		$apiObj->mongoSetCollection("commission");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['comissions'][] = array();
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['comissions'][] = $apiObj->get_thing_display($doc2);
			}
		}

		// var_dump($result['comissions']);

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

		// Get Notes
		$apiObj->mongoSetCollection("sms");
		$collectionQuery = array('_parentId' => $doc['_id']);
		$cursor2 = $apiObj->mongoFind($collectionQuery);
		if($cursor2->count() == 0){
			$result['sms'] = null;
		} else {
			foreach (iterator_to_array($cursor2) as $doc2) {
				$result['sms'][] = $apiObj->get_thing_display($doc2);
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

	return $result;
}

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
	if(!empty($_POST)){
		$apiObj->saveAll($_POST,"lead");
		if($apiObj->save_things($_POST)){
			$result['message'] = "Things Saved";    
		} else {
			$result['message'] = "There was an error saving your Things.";   
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
		$result = getLeadById($_POST['person_0_id'], $settings);
		$size_people = 1;
		$item_people = createItemPeople($apiObj->getValue( $result['leads'][0], "dateOfBirth"), $apiObj->getValue( $result['leads'][0], "smokerTabacco"), 'a');
		
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

		if(!empty($income)){
			$income = "&income=$income";
		}
		if(!empty($item_people)){
			$item_people = "&people=[$item_people]";
		}

		$iframe_url = "https://www.healthsherpa.com/find-plans/plans?zip_code=$zip_code". $income . $item_people ."&size=$size&cs=premium&year=2017&page=1&_agent_id=lisa-jackson-lerW0g";
		
		echo $iframe_url;
	}else {

	}
}
});
$app->get('/template/:type/:index/:crtThng', function ($type, $index, $crtThng) use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$app->render('form_partials.php', array('index'=>$index, 'type'=>$type, 'crtThng'=>$crtThng,  'settings'=>$settings, "apiObj"=>$apiObj));
});

$app->get('/commision/:leadId/:index', function ($leadId, $index) use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$result = array();

	// x. Get person
	$apiObj->mongoSetCollection("person");
    $itemPerson = $apiObj->mongofindOne(array('_id'=> $leadId));
    if(empty($itemPerson)){
    } else{
    	$result['person'] = $itemPerson;
    }

    // x. Get carriers
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

	$result['policies'] = array();
	$apiObj->mongoSetCollection("policy");
	$collectionQuery = false;
	$collectionQuery = array('_parentId' => $leadId);
	$collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "SOLD";
    $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "INFORCE";
	$collectionQuery['$and'][]['submissionDate']['$ne'] = "";
            
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['policy'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['policies'][] = $apiObj->get_thing_display($doc2);
		}
	}

	$result['carrierPlans'] = array();
	$collectionQuery = false;
    $apiObj->mongoSetCollection("carrierPlan");
    $collectionQuery['status']['$eq'] = "ACTIVE";
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $result['carrierPlan'][] = array();
    } else {
        $cursor2->sort(array('sort' => 1));
        foreach (iterator_to_array($cursor2) as $doc2) {
        	$result['carrierPlans'][] = $doc2;
        }
    }	
	
	$app->render('form_commission.php', array('leadId' => $leadId, 'index' => $index, 'result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj));
});

$app->get('/load_commision/:leadId/', function ($leadId) use ($app,$settings) {

	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$result = array();

	// x. Get comission
	$result['comissions'] = array();
	$apiObj->mongoSetCollection("commission");
	$collectionQuery = array('_parentId' => $leadId);
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() > 0){
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['comissions'][] = $apiObj->get_thing_display($doc2);
		}
	}
	// debug($result['comissions']);
	// x. Get person
	$apiObj->mongoSetCollection("person");
    $itemPerson = $apiObj->mongofindOne(array('_id'=> $leadId));
    if(empty($itemPerson)){
    } else{
    	$result['person'] = $itemPerson;
    }

    // x. Get carriers
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

	$result['policies'] = array();
	$apiObj->mongoSetCollection("policy");
	$collectionQuery = false;
	$collectionQuery = array('_parentId' => $leadId);
	$collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "SOLD";
    $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "INFORCE";
	$collectionQuery['$and'][]['submissionDate']['$ne'] = "";
	$cursor2 = $apiObj->mongoFind($collectionQuery);
	if($cursor2->count() == 0){
		$result['policy'][] = array();
	} else {
		foreach (iterator_to_array($cursor2) as $doc2) {
			$result['policies'][] = $apiObj->get_thing_display($doc2);
		}
	}

	$result['carrierPlans'] = array();
	$collectionQuery = false;
    $apiObj->mongoSetCollection("carrierPlan");
    $collectionQuery['status']['$eq'] = "ACTIVE";
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $result['carrierPlan'][] = array();
    } else {
        $cursor2->sort(array('sort' => 1));
        foreach (iterator_to_array($cursor2) as $doc2) {
        	$result['carrierPlans'][] = $doc2;
        }
    }	
	
	$app->render('form_edit_commission.php', array('leadId' => $leadId, 'result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj));
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
	echo 'sdfsdf';
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