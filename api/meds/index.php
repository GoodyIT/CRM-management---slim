<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));

$app->get('/getApplicants', function ()use ($app,$settings) {

    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);





    $apiObj->mongoSetCollection("carrier");
    $cursor = $apiObj->mongoFind();
    $carriers = array();

    if(!empty($cursor)){
        if($cursor->count() == 0){
        } else {
            foreach (iterator_to_array($cursor) as $doc) {
                $carriers[$doc['_id']] = $doc['name'];
            }
        }
    }

    $apiObj->mongoSetCollection("carrierPlan");
    $cursor = $apiObj->mongoFind();
    $plans = array();

    if(!empty($cursor)){
        if($cursor->count() == 0){
        } else {
            foreach (iterator_to_array($cursor) as $doc) {
                $plans[$doc['_id']] = $doc['name'];
            }
        }
    }

    $apiObj->mongoSetCollection("user");
    $cursor = $apiObj->mongoFind();
    $users = array();

    if(!empty($cursor)){
        if($cursor->count() == 0){
        } else {
            foreach (iterator_to_array($cursor) as $doc) {
                $users[$doc['_id']] = $doc['firstname'] . " " .  $doc['lastname'];
            }
        }
    }












    $apiObj->mongoSetCollection("policy");
    $majorMeds = array();
    $majorMeds[] = "NNFLei-Mkjie83-Opejr93f";
    $majorMeds[] = "On97lakN-V0gVHNyP-LrpUEAOZ";
    $majorMeds[] = "f9tc2bTZ-H0P7mYrI-pMP0fMNW";
    $majorMeds[] = "YxNyBSDf-J8gM4Dou-Gf4vmJta";
    $majorMeds[] = "PrLtFKmF-872b5Q0c-tMBQunll";

   // $collectionQuery['_id'] = $policyId; //20160113163732-NOaOiwI7-Lv1zeF7C

    $collectionQuery['coverageType']['$in'] = $majorMeds;
    $pastDueStatus = array("CANCELLED","PAYMENTISSUE");
    $submittedStatus = array("SUBMIT","SUBMITPAYMENT","ERRORS","CANCELLED","DECLINED","DUPLICATE");

    $collectionQuery['_timestampCreated']['$gte'] = "201512151000000";
    //$collectionQuery['submissionDate']['$gte'] = "20160101000000";
    $collectionQuery['status']['$nin'] = $pastDueStatus;
    $collectionQuery['policySubmitted']['$nin'] = $submittedStatus;
    $cursor = $apiObj->mongoFind($collectionQuery);

    $policyStatus = array();

    $total_count = 0;
    if(!empty($cursor)){
        if($cursor->count() == 0){
        } else {
             $cursor->sort(array('_timestampCreated' => 1));
            foreach (iterator_to_array($cursor) as $doc) {
               // debug($doc,"policy");
                // Get Person Information
                $apiObj->mongoSetCollection("person");
                $collectionQuery2 = array();
                $collectionQuery2['_id'] = $doc['_parentId']; // policy has a parent id from person
                $cursor2 = $apiObj->mongoFind($collectionQuery2);
                if(!empty($cursor2)){
                    if($cursor2->count() == 0){
                    } else {
                        foreach (iterator_to_array($cursor2) as $doc2) {
                            //debug($doc2,"person");
                            $address = false;
                            $email = "NOTSET";
                            // get the address
                            $apiObj->mongoSetCollection("addresses");
                            $collectionQuery3 = array();
                            $collectionQuery3['_parentId'] = $doc2['_id']; // email belongs to person
                            $cursor3 = $apiObj->mongoFind($collectionQuery3);
                            if(!empty($cursor3)){
                                if($cursor3->count() == 0){
                                    // echo "no address";
                                } else {
                                    foreach (iterator_to_array($cursor3) as $doc3) {
                                            //debug($doc3,"address");
                                            $address = $doc3;

                                            $email = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $doc2['firstName']).preg_replace("/[^a-zA-Z0-9]/", "", $doc2['lastName']).preg_replace("/[^a-zA-Z0-9]/", "", $doc3['zipCode'])."@24hourmail.net");
                                            break;
                                    }
                                }
                            }

                            // Check if Address was found, other wise can't create account
                            if($email <> "NOTSET"){

                                $createAccount = TRUE;
                                $applicant['link'] = "http://agents.ebrokercenter.com/#lead/edit/".$doc2['_id'];
                                $applicant['dateCreated'] = substr($doc['_timestampCreated'],4,2). "/".substr($doc['_timestampCreated'],6,2)."/".substr($doc['_timestampCreated'],0,4);
                                $applicant['submissionDate'] =  substr($doc['submissionDate'],4,2). "/".substr($doc['submissionDate'],6,2)."/".substr($doc['submissionDate'],0,4);
                                $applicant['paymentDate'] = substr($doc['paymentDate'],4,2). "/".substr($doc['paymentDate'],6,2)."/".substr($doc['paymentDate'],0,4);
                                $applicant['status'] = $doc['status'];

                                if(empty($policyStatus[$doc['status']])){
                                 $policyStatus[$doc['status']] = 0;
                                }
                                $policyStatus[$doc['status']] = $policyStatus[$doc['status']] + 1;

                                if(empty($doc2['firstName'])){
                                  $createAccount = FALSE;
                                } else {
                                   $applicant['firstName'] = $doc2['firstName'];
                                }

                                  if(empty($doc2['lastName'])){
                                  $createAccount = FALSE;
                                } else {
                                   $applicant['lastName'] = $doc2['lastName'];
                                }

                                $applicant['email'] = $email;

                                if(empty($address['state'])){
                                  $createAccount = FALSE;
                                } else {
                                   $applicant['state'] = $address['state'];
                                }




                                $applicant['password'] = $apiObj->getRandomString(10);

                                $applicant['carrier'] = $carriers[$doc['carrier']];
                                $applicant['coverageType'] = $plans[$doc['coverageType']];
                                $applicant['fronter'] = ucwords(strtolower($users[$doc['soldBy']]));
                                $applicant['closer'] = ucwords(strtolower($users[$doc['closedBy']]));

                                if(trim($doc['carrier']) == ""){
                                    $createAccount = FALSE;
                                }

                                if(trim($doc3['state']) == ""){
                                    $createAccount = FALSE;
                                }

                                if(trim($doc3['zipCode']) == ""){
                                    $createAccount = FALSE;
                                }

                                if($createAccount === TRUE){
                                    //echo "<br>Create account";
                                    // Get the email
                                    $apiObj->mongoSetCollection("emails");
                                    $collectionQuery3 = array();
                                    $collectionQuery3['_parentId'] = $doc2['_id']; // email belongs to person
                                    $collectionQuery3['email'] = $email; // email belongs to person
                                    $cursor3 = $apiObj->mongoFind($collectionQuery3);
                                    if(!empty($cursor3)){
                                        if($cursor3->count() == 0){
                                            $post = array();
                                            $post['person_0_createThing'] = "Y";
                                            $post['person_0_id'] = $doc2['_id'];
                                            $post['person_0_emails_0_createThing'] = "Y";
                                            $post['person_0_emails_0_email'] = $email;
                                            $post['person_0_emails_0_type'] = "OTHER";
                                           // debug($post);
                                            $apiObj->save_things($post);
                                        } else {
                                            foreach (iterator_to_array($cursor3) as $doc3) {
                                               // debug($doc3,"email");
                                            }
                                        }
                                    }

                                    /// DO THE STUF HERE AND  IF SUCCESSSFUL!!!!
                                    $success = FALSE;
                                    if($success === true){
                                            $post = array();
                                            $post['person_0_createThing'] = "Y";
                                            $post['person_0_id'] = $doc2['_id'];
                                            $post['person_0_notes_0_createThing'] = "Y";
                                            $post['person_0_notes_0_information'] = "Healthcare.gov account created for ".$email." with password ".$password;
                                            //$apiObj->save_things($post);
                                    }

                                }

                                // OUT PUT TO SYSTEM
                                //debug($applicant);
                                if($createAccount === TRUE){
                                $tableRows[] = $applicant;
                                }

                                $total_count++;


                            }
                            //break;
                        }
                    }
                }
               // break;

            }

        }
        echo "<table cellpadding='3' border='1' >";
        echo "<tr><td>Created</td><td>Submission</td><td>Payment</td><td>Status</td><td>First</td><td>Last</td><td>email</td><td>State</td><td>Carrier</td><td>Coverage</td><td>Fronter</td><td>Closer</td></tr>";
        foreach($tableRows as $key=>$row){
            echo "<tr>";
            echo "<td>".$row['dateCreated']."</td>";
             echo "<td>".$row['submissionDate']."</td>";
             echo "<td>".$row['paymentDate']."</td>";
             echo "<td>".$row['status']."</td>";
             echo "<td><a href='".$row['link']."' target='blank'>".$row['firstName']."</a></td>";
             echo "<td><a href='".$row['link']."' target='blank'>".$row['lastName']."</a></td>";
             echo "<td>".$row['email']."</td>";
             echo "<td>".$row['state']."</td>";
            // echo "<td>".$row['password']."</td>";
             echo "<td>".$row['carrier']."</td>";
             echo "<td>".$row['coverageType']."</td>";
             echo "<td>".$row['fronter']."</td>";
             echo "<td>".$row['closer']."</td>";


        }
        echo "</table>";
        echo $total_count;
        debug ($policyStatus);
    }
})->via('GET','POST');
$app->run();