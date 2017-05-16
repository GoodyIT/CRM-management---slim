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
        $collectionQuery['assignedTo']['$in'] = $userIds;
        $collectionQuery['$and'][]['disposition']['$ne'] = "SOLD";
        $collectionQuery['$and'][]['disposition']['$ne'] = "ONHOLD";
        // $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
        // $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
        if(trim($settings['leads']['search']) <> ""){
            $searchType = "NAME";
            $settings['leads']['search'] = trim($settings['leads']['search']);
            if($isPhone = $apiObj->validatePhoneNumber($settings['leads']['search'])){
                $searchType = "PHONE";
                $phoneParents = array();
                $phone = $apiObj->displayPhoneNumber($settings['leads']['search'], TRUE);
                $apiObj->mongoSetCollection("phones");
                $collectionQueryPhone['phoneNumber']['$eq'] = $phone;
                $cursorPhone = $apiObj->mongoFind($collectionQueryPhone);
                if(!empty($cursorPhone)){
                    foreach (iterator_to_array($cursorPhone) as $doc) {
                        $phoneParents[] = $doc['_parentId'];
                    }
                }
                $apiObj->mongoSetCollection("person");
                $collectionQuery['$or'][]['_id']['$in'] = $phoneParents;
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
            if($searchType = "NAME"){
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
        $collectionQuery['assignedTo']['$in'] = $userIds;
        // $collectionQuery['disposition']['$eq'] = "SOLD";
        $collectionQuery['$and'][0]['$or'][]['disposition']['$eq'] = "SOLD";
        $collectionQuery['$and'][0]['$or'][]['disposition']['$eq'] = "ONHOLD";
        // $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
        // $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
        if(trim($settings['clients']['search']) <> ""){
            $settings['clients']['search'] = trim($settings['clients']['search']);
            $searchType = "NAME";
            if($isPhone = $apiObj->validatePhoneNumber($settings['clients']['search'])){
                $searchType = "PHONE";
                $phoneParents = array();
                $phone = $apiObj->displayPhoneNumber($settings['clients']['search'], TRUE);
                $apiObj->mongoSetCollection("phones");
                $collectionQueryPhone['phoneNumber']['$eq'] = $phone;
                $cursorPhone = $apiObj->mongoFind($collectionQueryPhone);
                if(!empty($cursorPhone)){
                    foreach (iterator_to_array($cursorPhone) as $doc) {
                        $phoneParents[] = $doc['_parentId'];
                    }
                }
                $apiObj->mongoSetCollection("person");
                $collectionQuery['$or'][]['_id']['$in'] = $phoneParents;
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
            //debug($collectionQuery);
           if($searchType = "NAME"){
                if(count($nameparts) > 1){
                    $collectionQuery['$and'][1]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
                    $collectionQuery['$and'][1]['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
                } else {
                    if(!empty($nameparts[0])){
                        $collectionQuery['$and'][1]['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
                        $collectionQuery['$and'][1]['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
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
$app->get('/policies', function () use ($app,$settings) {
    $result['policies'] = array();
    $apiObj = new apiclass($settings);
    $docIds = array();
    if($apiObj->userLoggedIn()){
        $userIds = $apiObj->getUserIds();
        $apiObj->mongoSetDB($settings['database']);
        $collectionQuery = false;
        $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
        $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;



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
                //$collectionQuery['$and'][]['status']['$eq'] = "HOLD";
                $collectionQuery['submissionDate']['$gte'] = date("Ymd000000");
                $collectionQuery['submissionDate']['$lte'] = date("Ymd235959");
            }
        } else {
            $_REQUEST['search_submitToday'] = "";
        }
        if(!empty($_REQUEST['search_pastDue'])){
            if(trim($_REQUEST['search_pastDue']) == "Y"){
                $collectionQuery['$and'][]['status']['$eq'] = "HOLD";
                $collectionQuery['submissionDate']['$lte'] = date("Ymd000000");
            }
        } else {
            $_REQUEST['search_pastDue'] = "";
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
                $collectionQuery['submissionDate']['$lte'] = date("Ymd000000", time() - 60 * 60 * 24);
            }
        } else {
            $_REQUEST['search_majorMed'] = "";
        }


        if(trim($settings['policies']['search']) <> ""){
            // Get Person
            $personIds = array();
            $result['persons'] = array();
            $apiObj->mongoSetCollection("person");

            $nameparts = explode(" ", $settings['policies']['search']);
            if(count($nameparts) > 1){
                $collectionQuery2['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
                $collectionQuery2['lastName']['$regex'] = new MongoRegex("/".$nameparts[1].".*/i");
            } else {
                if(!empty($nameparts[0])){
                    $collectionQuery2['$or'][]['firstName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
                    $collectionQuery2['$or'][]['lastName']['$regex'] = new MongoRegex("/".$nameparts[0].".*/i");
                }
            }


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
        }
        $apiObj->mongoSetCollection("policy");
        $cursor = $apiObj->mongoFind($collectionQuery);
        $cursor->sort(array('_timestampCreated' => -1));
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
    if($apiObj->userLoggedIn()){
        $apiObj->mongoSetDB($settings['database']);
        if(!empty($_SESSION['api']['user']['_id'])){
            $post['person_0_createThing'] = "Y";
            $post['person_0_id'] = $personId;
            $post['person_0_history_0_createThing'] = "Y";
            $post['person_0_history_0_note'] = "User Viewed this lead";
            $post['person_0_history_0_userId'] = $_SESSION['api']['user']['_id'];
            $post['person_0_history_0_userName'] = $_SESSION['api']['user']['firstname'] . " " . $_SESSION['api']['user']['lastname'];
            $apiObj->save_things($post);
        }
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
            foreach (iterator_to_array($cursor2) as $doc2) {
                $result['policy'][] = $apiObj->get_thing_display($doc2);
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
    } else {
        echo "User Not Logged In";
        exit();
    }

    $app->render('leadform.php', array('result' => $result, 'settings'=>$settings, "apiObj"=>$apiObj));
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


$app->get('/export', function () use ($app,$settings) {


    //$settings['database'] = "ehealthbrokers";
    $result['policies'] = array();
    $apiObj = new apiclass($settings);
    $docIds = array();
    if($apiObj->userLoggedIn()){
        if( (empty($_SESSION['api']['user']['canExport'])) || ($_SESSION['api']['user']['canExport'] <> "Y")){
            echo "Nothing";
            exit();
        }
        $userIds = $apiObj->getUserIds();
        $apiObj->mongoSetDB($settings['database']);
        $collectionQuery = false;

        $apiObj->mongoSetCollection("policy");
        $cursor = $apiObj->mongoFind($collectionQuery);
        $cursor->sort(array('_timestampCreated' => -1));
        $x = 0;
        if($cursor->count() == 0){
            $result['total'] = 0;
        } else {
            $result['total'] = $cursor->count();
            $cursor->limit(10000);
            foreach (iterator_to_array($cursor) as $doc) {
                $docIds[] = $doc['_parentId'];
                $result['policies'][] = $apiObj->get_thing_display($doc);
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
                $result['persons'][] = $apiObj->get_thing_display($doc2);
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

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$settings['database'].'_export_'.date("YmdHis").'.csv');
    $out = fopen('php://output', 'w');
    // output the column headings
    fputcsv($out, array('ID', 'First Name', 'Middle Name', 'Last Name', 'Date Created', 'Lead Status', 'Email', 'Phone 1', 'Phone 2', 'Address', 'AptNo', 'City', 'State', ' Zip', 'DOB', 'Policy Entered Date', 'Policy Number', 'Carrier', 'Coverage Type', 'Status', 'Premium', 'Setup Fee', 'Subsidy', 'Pay Schedule', 'Effecitve Date', 'Submission Date', 'Policy Submitted', 'Renewal Date', 'Term Date', 'Banking Info', 'Credit Card Info', 'Fronter', 'Closer'));



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
                        $person['dateOfBirth']  =  $apiObj->getValues($var2, "dateOfBirth");
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
            $row['dob'] = $person['dateOfBirth'];
            $row['policyentereddate'] = date("m/d/Y",strtotime($var['_timestampCreated']));
            $row['policynumber'] = $var['policyNumber'];
            $row['Carrier'] = $carrier;
            $row['Coverage Type'] = $carrierPlan;
            $row['Status'] = ucwords(strtolower($var['status']));
            $row['Premium'] = $var['premiumMoney'] ;
            $row['SetupFee'] = $var['SetupFee'] ;
            $row['Subsidy'] =  $var['subsidyMoney'] ;
            $row['Pay Schedule'] = $carrier;
            $row['Effective Date'] = $var['effectiveDate'];
            $row['Submission Date'] = $var['submissionDate'];
            $row['Policy Submitted'] = $var['policySubmitted'];
            $row['Renewal Date'] = $var['renewalDate'];
            $row['Term Date'] = $var['termDate'];
            $row['Has Bank Info'] = $person['hasBankInfo'] ;
            $row['Has Credit Card'] =  $person['hasCreditCard'] ;
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
$app->run();