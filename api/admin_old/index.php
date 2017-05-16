<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
	'templates.path' => './',
));
$app->get('/', function () use ($app,$settings) {
	echo "";
});
$app->get('/settings', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	if($apiObj->userLoggedIn()){
		$permissions = array("administrator","manager");
		$apiObj->userPermissionLevel($permissions);
		// $apiObj->agencySelect();
		/*
$post['agency_0_createThing'] = "Y";
$post['agency_0_id'] = "20151015105805-jc0i6h8l-k54x8HCg";
$post['agency_0_agencyName'] = "EBROKERCENTER";
$post['agency_0_agencyOwner'] = "auSpFsFC-Hs4WQenx-qQJSbMud";
$post['agency_0_agencyContact'] = "auSpFsFC-Hs4WQenx-qQJSbMud";
$post['agency_0_phoneNumber'] = "8882909060";
$post['agency_0_addressStreet1'] = "9518 9th St Suite 201";
$post['agency_0_addressCity'] = "Rancho Cucamonga!!";
$post['agency_0_addressState'] = "CA";
$post['agency_0_addressPostalCode'] = "91730";
$post['agency_0_addressCountry'] = "USA";
$post['agency_0_domains_0_createThing'] = "N";
$post['agency_0_domains_0_url'] = "104.131.135.180!!";
$post['agency_0_domains_1_createThing'] = "N";
$post['agency_0_domains_1_url'] = "agents.ebrokercenter.com";
       */
		$result = array();
		$app->render('settingsmenu.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
	}
});
$app->get('/user/create', function () use ($app,$settings) {
	$result = array();
	$result['usergroups'] = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection('userGroups');
	$usergroupCursor = $apiObj->mongoFind();
	foreach($usergroupCursor as $ugroup){
		array_push($result['usergroups'], $ugroup);
	}
	$app->render('userform.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
});
$app->post('/user/createuser', function () use ($app,$settings) {
	$result = array();
	$result['user'][0] = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$error = FALSE;
	if((empty($_POST['previousEmail'])) || ($_POST['previousEmail'] <> $_POST['user_0_email'])){
		$apiObj->mongoSetCollection('user');
		$collectionQuery = array('email' => array('$eq' => $_POST['user_0_email']));
		if ($apiObj->mongoDoesExist($collectionQuery))
		{
			$error = TRUE;
			$result['result'] = "ERROR";
			$result['message'] = "Email is already in use.";
			$result['field'] = "user_0_email";
		}
	}
	if (!filter_var($_POST['user_0_email'], FILTER_VALIDATE_EMAIL)) {
		$error = TRUE;
		$result['result'] = "ERROR";
		$result['message'] = "Email is not valid.";
		$result['field'] = "user_0_email";
	}
	if(!empty($_POST['user_0_phone'])){
		if (!$apiObj->validatePhoneNumber($_POST['user_0_phone'])) {
			$error = TRUE;
			$result['result'] = "ERROR";
			$result['message'] = "Phone Number is not formatted correctlly.";
			$result['field'] = "user_0_phone";
		}
	}
	if((isset($_POST['user_0_passwordNew'])) && (strlen(trim($_POST['user_0_passwordConfirm'])) > 4)){
		if($_POST['user_0_passwordNew']<>$_POST['user_0_passwordConfirm']){
			$error = TRUE;
			$result['result'] = "ERROR";
			$result['message'] = "The passwords do not match.";
			$result['field'] = "user_0_password";
		}else{
			$_POST['user_0_password']=$_POST['user_0_passwordNew'];
		}
	}
	$apiObj->mongoSetCollection("userGroups");
	$usergroup=$apiObj->mongoFind();
	$i=0;
	$k=0;
	foreach($usergroup as $group){
		foreach($group['users'] as $user){
			if($user['userId']==$_POST['user_0_id']){
				$data['userGroups_'.$i.'_createThing']='Y';
				$data['userGroups_'.$i.'_id']=$group['_id'];
				$data['userGroups_'.$i.'_users_0_createThing']='N';
				$data['userGroups_'.$i.'_users_0_id']=$user['_id'];
				$data['userGroups_'.$i.'_users_0_userId']=$_POST['user_0_id'];
				$data['userGroups_'.$i.'_users_0_level']=$_POST['user_0_'.lcfirst(str_replace(' ', '',$group['label']))];
			}else{
				$data['userGroups_'.$i.'_createThing']='Y';
				$data['userGroups_'.$i.'_id']=$group['_id'];
				$data['userGroups_'.$i.'_users_0_createThing']='N';
				$data['userGroups_'.$i.'_users_0_userId']=$_POST['user_0_id'];
				$data['userGroups_'.$i.'_users_0_level']=$_POST['user_0_'.lcfirst(str_replace(' ', '',$group['label']))];
			}
		}
		$i++;
		$k++;
		$_POST['user_0_'.lcfirst(str_replace(' ', '',$group['label']))]='';
	}
	//$_POST['user_0_id']=$apiObj->getRandomId(); for multiple user creation
	if($error === false){
		if(($apiObj->save_things($_POST))&&($apiObj->save_things($data))){
			$result['result'] = "SUCCESS";
			$result['message'] = "User Saved";
		} else {
			$result['result'] = "ERROR";
			$result['message'] = "There was an error saving your User.";
		}
	}
	header("Content-Type: application/json");
	echo json_encode($result);
});
$app->get('/user/list', function () use ($app,$settings) {
	$result['users'] = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$query['$or'][]=array('status'=>'ACTIVE');
	$query['$or'][]=array('status'=>'active');
	$query2['$or'][]=array('status'=>'INACTIVE');
	$query2['$or'][]=array('status'=>'inactive');
	if($_GET['state']=='INACTIVE'){
		$cursor = $apiObj->mongoFind($query2);
	}else{
		$cursor = $apiObj->mongoFind($query);
	}
	if(!empty($cursor)){
		$cursor->sort(array('firstname' => 1));
		if($cursor->count() == 0){
		} else {
			foreach (iterator_to_array($cursor) as $doc) {
				$result['users'][] =  $apiObj->get_thing_display($doc);
			}
		}
	}
	$app->render('userlist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
});
$app->get('/user/edit/:userId', function ($userId) use ($app,$settings) {
	$result['user'] = array();
	$result['usergroups'] = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$collectionQuery['_id']['$eq'] = $userId;
	$cursor = $apiObj->mongoFind($collectionQuery);
	if(!empty($cursor)){
		if($cursor->count() == 0){
		} else {
			foreach (iterator_to_array($cursor) as $doc) {
				$result['user'][] =  $apiObj->get_thing_display($doc);
			}
		}
	}
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection('userGroups');
	$usergroupCursor = $apiObj->mongoFind();
	foreach($usergroupCursor as $ugroup){
		array_push($result['usergroups'], $ugroup);
	}
	$app->render('userform.php', array('result' => $result, "userId"=>$userId, "apiObj"=>$apiObj, "settings"=> $settings));
});
$app->get('/user/systemform', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB('ehealthbrokers');
	$apiObj->mongoSetCollection("userGroups");
	$usergroup=$apiObj->mongoFind();
	//$mongo = new MongoClient();
	//foreach($usergroup as $group){
	//	if($group['_timestampCreated']=='20151123132508'){
	//		$mongo->$settings['database']->systemForm->remove(array('_id'=>$group['_id']));
	//	}
	//}
	$i=0;
	foreach($usergroup as $group){
		$data['systemForm_'.$i.'_createThing']='Y';
		$data['systemForm_'.$i.'_id']=$group['_id'];
		$data['systemForm_'.$i.'_thing']='user';
		$data['systemForm_'.$i.'_name']=lcfirst(str_replace(' ', '',$group['label']));
		$data['systemForm_'.$i.'_label']=$group['label']. ' User Group';
		$data['systemForm_'.$i.'_type']='SELECT';
		$data['systemForm_'.$i.'_row']=10;
		$data['systemForm_'.$i.'_sort']=2;
		$data['systemForm_'.$i.'_columns']=3;
		$data['systemForm_'.$i.'_required']=1;
		$data['systemForm_'.$i.'_options_0_createThings']='N';
		$data['systemForm_'.$i.'_options_0_value']='USER';
		$data['systemForm_'.$i.'_options_0_label']='User';
		$data['systemForm_'.$i.'_options_0_default']='N';
		$data['systemForm_'.$i.'_options_1_value']='MANAGER';
		$data['systemForm_'.$i.'_options_1_label']='Manager';
		$data['systemForm_'.$i.'_options_1_default']='N';
		$data['systemForm_'.$i.'_options_2_value']='ADMINISTRATOR';
		$data['systemForm_'.$i.'_options_2_label']='Administrator';
		$data['systemForm_'.$i.'_options_2_default']='N';
		$data['systemForm_'.$i.'_options_3_value']='NONE';
		$data['systemForm_'.$i.'_options_3_label']='None';
		$data['systemForm_'.$i.'_options_3_default']='Y';
		$i++;
	}
	//$apiObj->save_things($data);
});
$app->get('/carriers', function () use ($app,$settings) {
	$result=array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("carrier");
	if($_GET['status']!='INACTIVE'){
		$query=array('status'=>'ACTIVE');
	}else{
		$query=array('status'=>'INACTIVE');
	}
	$cursor = $apiObj->mongoFind($query);
	if(!empty($cursor)){
		foreach($cursor as $carrier){
			array_push($result, $carrier);
		}
	}
	$app->render('carrierlist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
});
$app->get('/plans', function () use ($app,$settings) {
	$result=array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("carrierPlan");
	if($_GET['status']!='INACTIVE'){
		$query=array('status'=>'ACTIVE');
	}else{
		$query=array('status'=>'INACTIVE');
	}
	$cursor = $apiObj->mongoFind($query);
	if(!empty($cursor)){
		foreach($cursor as $carrier){
			array_push($result, $carrier);
		}
	}
	$app->render('carrierplan.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
});
$app->map('/createCarrier', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("carrier");
	if(!empty($_POST)){
		$_POST['carrier_0_option']=strtoupper(preg_replace("[^A-Za-z0-9]", "", $_POST['carrier_0_name']));
		$_POST['carrier_0_selectable']='Y';
		$_POST['carrier_0_status']='ACTIVE';
		$_POST['carrier_0_id']=$apiObj->getRandomId();
		if($apiObj->save_things($_POST)){
			$response='Things Saved';
		}else{
			$response='Error Saving';
		}
		print_r($_POST);
	}
})->via('GET','POST');
$app->map('/createCarrierPlan', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("carrierPlan");
	if(!empty($_POST)){
		$_POST['carrierPlan_0_option']=strtoupper(preg_replace("[^A-Za-z0-9]", "", $_POST['carrierPlan_0_name']));
		$_POST['carrierPlan_0_selectable']='Y';
		$_POST['carrierPlan_0_status']='ACTIVE';
		$_POST['carrierPlan_0_id']=$apiObj->getRandomId();
		if($apiObj->save_things($_POST)){
			$response='Things Saved';
		}else{
			$response='Error Saving';
		}
		print_r($_POST);
	}
})->via('GET','POST');
$app->map('/leadsources', function () use ($app,$settings) {
	if($_SESSION['api']['user']['permissionLevel'] <> "ADMINISTRATOR"){
		echo 'Sorry, you do not have permissions';
		return;
	}
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);

	$collectionQuery = false;
    $apiObj->mongoSetCollection("systemForm");
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $result['systemForm'][] = array();
    } else {
    	foreach (iterator_to_array($cursor2) as $key=>$doc2) {
        	
            if($doc2['name'] == 'leadSource'){
            	$result['systemForm'][$doc2['name']] = $doc2;
            }
        }
    }

	$app->render('leadsourcelist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
})->via('GET', 'POST');

$app->map('/deleteLeadSource', function () use ($app,$settings) {
	if($_SESSION['api']['user']['permissionLevel'] <> "ADMINISTRATOR"){
		echo 'Sorry, you do not have permissions';
		return;
	}
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);

	// $userForm['systemForm_0_createThing'] = "Y";
    // $userForm['systemForm_0_id'] = 'H3Sah2-feED23-dw23423fv';
    // $userForm['systemForm_0_options_0_createThing'] = "N";
    // $userForm['systemForm_0_options_0_id'] = '20161223001226-GmfLj22X-5LCeEKY8';
    // $userForm['systemForm_0_options_0_deleteThing'] = 'Y';
    
    // $apiObj->save_things($userForm);  

	$_POST['systemForm_0_createThing'] = "Y";
    $_POST['systemForm_0_options_0_createThing'] = "N";
    $_POST['systemForm_0_options_0_deleteThing'] = 'Y';

    $apiObj->save_things($_POST);  
})->via('POST');	
$app->map('/createLeadSource', function () use ($app,$settings) {
	if($_SESSION['api']['user']['permissionLevel'] <> "ADMINISTRATOR"){
		echo 'Sorry, you do not have permissions';
		return;
	}
	// $userForm['systemForm_0_createThing'] = "Y";
    // $userForm['systemForm_0_id'] = 'H3Sah2-feED23-dw23423fv';
    // $userForm['systemForm_0_options_0_createThing'] = "N";
    // $userForm['systemForm_0_options_0_value'] = "activxxxx2x3";
    // $userForm['systemForm_0_options_0_label'] = "Activexxx2x3";
    // $userForm['systemForm_0_options_0_default'] = "Y";
    // $apiObj->save_things($userForm);  

	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("systemForm");
	if(!empty($_POST)){
		$value_temp = strtoupper($_POST["systemForm_0_options_0_label"]);
	    $value_temp = str_replace(" ","", $value_temp);
	    $_POST['systemForm_0_options_0_createThing'] = "N";
	    $_POST['systemForm_0_options_0_value'] = $value_temp;
	    $_POST['systemForm_0_options_0_default'] = "Y";
	    
	    if($apiObj->save_things($_POST)){
			$response='Things Saved';
		}else{
			$response='Error Saving';
		}
	}
})->via('GET','POST');

$app->map('/usergroups', function () use ($app,$settings) {
	$result=array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("userGroups");
	$cursor = $apiObj->mongoFind();
	if(!empty($cursor)){
		foreach($cursor as $group){
			array_push($result, $group);
		}
	}
	$app->render('grouplist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
})->via('GET','POST');
$app->map('/usergroups/create', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$userCursor=$apiObj->mongoFind();
	$app->render('groupform.php', array('userCursor' => $userCursor, "apiObj"=>$apiObj, "settings"=> $settings));
})->via('GET','POST');
$app->map('/usergroups/createUserGroup', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("userGroups");
	if(!empty($_POST['userGroups_0_id'])){
		$cursor = $apiObj->mongoFindOne(array('_id'=>$_POST['userGroups_0_id']));
	}
	$i=0;
	$_POST['userGroups_0_createThing']='Y';
	if(!empty($_POST['userGroups_0_managers'])){
		foreach($_POST['userGroups_0_managers'] as $managers){
			if(!empty($cursor['users'])){
				foreach($cursor['users'] as $user){
					if($user['userId']==$managers){
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_id']=$user['_id'];
						$_POST['userGroups_0_users_'.$i.'_level']='manager';
					}else{
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_userId']=$managers;
						$_POST['userGroups_0_users_'.$i.'_level']='manager';
					}
				}
			}else{
				$_POST['userGroups_0_users_'.$i.'_createThing']='N';
				$_POST['userGroups_0_users_'.$i.'_userId']=$managers;
				$_POST['userGroups_0_users_'.$i.'_level']='manager';
			}
			$i++;
		}
	}
	if(!empty($_POST['userGroups_0_attachusers'])){
		foreach($_POST['userGroups_0_attachusers'] as $users){
			if(!empty($cursor['users'])){
				foreach($cursor['users'] as $user){
					if($user['userId']==$users){
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_id']=$user['_id'];
						$_POST['userGroups_0_users_'.$i.'_level']='user';
					}else{
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_userId']=$users;
						$_POST['userGroups_0_users_'.$i.'_level']='user';
					}
				}
			}else{
				$_POST['userGroups_0_users_'.$i.'_createThing']='N';
				$_POST['userGroups_0_users_'.$i.'_userId']=$users;
				$_POST['userGroups_0_users_'.$i.'_level']='user';
			}
			$i++;
		}
	}
	if(!empty($_POST['userGroups_0_admins'])){
		foreach($_POST['userGroups_0_admins'] as $admins){
			if(!empty($cursor['users'])){
				foreach($cursor['users'] as $user){
					if($user['userId']==$admins){
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_id']=$user['_id'];
						$_POST['userGroups_0_users_'.$i.'_level']='administrator';
					}else{
						$_POST['userGroups_0_users_'.$i.'_createThing']='N';
						$_POST['userGroups_0_users_'.$i.'_userId']=$admins;
						$_POST['userGroups_0_users_'.$i.'_level']='administrator';
					}
				}
			}else{
				$_POST['userGroups_0_users_'.$i.'_createThing']='N';
				$_POST['userGroups_0_users_'.$i.'_userId']=$admins;
				$_POST['userGroups_0_users_'.$i.'_level']='administrator';
			}
			$i++;
		}
	}
	$_POST['userGroups_0_managers']=null;
	$_POST['userGroups_0_attachusers']=null;
	$_POST['userGroups_0_admins']=null;
	if($apiObj->save_things($_POST)){
		echo'<pre>';print_r($_POST);
	}else{
		echo 'no';
	}
})->via('GET','POST');
$app->get('/usergroups/edit/:userGroupId', function ($userGroupId) use ($app,$settings) {
	$users=array();
	$managers=array();
	$admins=array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("userGroups");
	$collectionQuery['_id']['$eq'] = $userGroupId;
	$result = $apiObj->mongoFindOne($collectionQuery);
	if(!empty($result['users'])){
		foreach($result['users'] as $usr){
			if(($usr['level']=='USER')||($usr['level']=='user')){
				array_push($users,$usr['userId']);
			}
			if(($usr['level']=='MANAGER')||($usr['level']=='manager')){
				array_push($managers,$usr['userId']);
			}
			if(($usr['level']=='ADMINISTRATOR')||($usr['level']=='administrator')){
				array_push($admins,$usr['userId']);
			}
		}
	}
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$userCursor=$apiObj->mongoFind();
	$app->render('groupform.php', array('result' => $result, "apiObj"=>$apiObj, "users"=>$users, "userCursor"=>$userCursor, "managers"=>$managers, "admins"=>$admins, "settings"=> $settings));
})->via('GET','POST');
$app->get('/agencies', function () use ($app,$settings) {
	$result = array();
	$result['agencies'] = array();
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	if($apiObj->userLoggedIn()){
		$permissions = array("administrator","manager");
		$apiObj->userPermissionLevel($permissions);
		$apiObj->mongoSetCollection("agency");
		$cursor = $apiObj->mongoFind($collectionQuery);
		if(!empty($cursor)){
			if($cursor->count() == 0){
			} else {
				foreach (iterator_to_array($cursor) as $doc) {
					$result['agencies'][] =  $apiObj->get_thing_display($doc);
				}
			}
		}
		$app->render('agencylist.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
	}
});
$app->map('/agencies/edit/:agencyId', function ($agencyId) use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("agency");
	$result = $apiObj->mongoFindOne(array('_id'=>$agencyId));
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$userCursor=$apiObj->mongoFind();
	$app->render('agencyform.php', array('result' => $result, "apiObj"=>$apiObj, "userCursor"=>$userCursor, "settings"=> $settings));
})->via('GET','POST');
$app->map('/agencies/createAgency', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("agency");
	if(!empty($_POST)){
		if(!empty($_POST['agency_0_id'])){
			$agencyId=$_POST['agency_0_id'];
		}else{
			$_POST['agency_0_id']=$apiObj->getRandomId();
			$agencyId=$_POST['agency_0_id'];
		}
		if(!empty($_POST['agency_0_attachusers'])){
			$i=0;
			foreach($_POST['agency_0_attachusers'] as $user){
				$_POST['user_'.$i.'_id']=$user;
				$_POST['user_'.$i.'_createThing']='Y';
				$_POST['user_'.$i.'_agencyId']=$agencyId;
				$i++;
			}
			$_POST['agency_0_attachusers']=null;
		}
		$_POST['agency_0_createThing']='Y';
		$apiObj->save_things($_POST);
	}
})->via('GET','POST');
$app->map('/agencies/create', function () use ($app,$settings) {
	$apiObj = new apiclass($settings);
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("user");
	$userCursor=$apiObj->mongoFind();
	$app->render('agencyform.php', array('result' => $result, "apiObj"=>$apiObj, "userCursor"=>$userCursor, "settings"=> $settings));
})->via('GET','POST');
$app->run();          