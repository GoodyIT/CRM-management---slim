<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));
$app->get('/', function () use ($app,$settings) {
    echo "Dev";
?>
<div class="ibox-content m-b-sm border-bottom">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="order_id">Order ID</label>
                <input type="text" id="order_id" name="order_id" value="" placeholder="Order ID" class="form-control">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="status">Order status</label>
                <input type="text" id="status" name="status" value=""  placeholder="Status" class="form-control">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="customer">Customer</label>
                <input type="text" id="customer" name="customer" value="" placeholder="Customer" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="date_added">Date added</label>
                <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_added" type="text" class="form-control" value="03/04/2014">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="date_modified">Date modified</label>
                <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_modified" type="text" class="form-control" value="03/06/2014">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="amount">Amount</label>
                <input type="text" id="amount" name="amount" value="" placeholder="Amount" class="form-control">
            </div>
        </div>
    </div>
</div>
<?php
});
$app->map('/indextables', function () use ($app,$settings) {
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokerTest2');
    // $db = $m->selectDB('ehealthbrokers');
    /*
    $tables = array("policy","addresses","notes","person","phones","emails","history","phones");
    foreach($tables as $key=>$table){
            $collection = $table;
            // create an index on 'x' ascending
            $db->$collection->createIndex(array('_parentId' => 1));
            $db->$collection->createIndex(array('_parentThing' => 1));
            $db->$collection->createIndex(array('_timestampCreated' => 1));
    }
    */
    $collection = "person";
    $db->$collection->createIndex(array('assignedTo' => 1));
    $collection = "policy";
    $db->$collection->createIndex(array('closedBy' => 1));
    $db->$collection->createIndex(array('soldBy' => 1));
    $db->$collection->createIndex(array('status' => 1));
    /**/
})->via('GET','POST');

$app->map('/updatemajors', function () use ($app,$settings) {
$apiObj = new apiclass($settings);

$settings['database'] = "ehealthbrokers";

$apiObj->mongoSetDB($settings['database']);
$apiObj->mongoSetCollection("policy");
$collectionQuery['coverageType']['$eq'] = "NNFLei-Mkjie83-Opejr93f";
 //$collectionQuery['coverageType']['$eq'] = "On97lakN-V0gVHNyP-LrpUEAOZ";   // Gold
// $collectionQuery['coverageType']['$eq'] = "PrLtFKmF-872b5Q0c-tMBQunll";   // Bronze
//$collectionQuery['coverageType']['$eq'] = "YxNyBSDf-J8gM4Dou-Gf4vmJta";   // Platinum
//  $collectionQuery['coverageType']['$eq'] = "f9tc2bTZ-H0P7mYrI-pMP0fMNW";   // Silver

  
 $m = new MongoClient();
$db = $m->selectDB($settings['database']);
$collection = 'policy';
$db->$collection->update(
    array("coverageType" => "On97lakN-V0gVHNyP-LrpUEAOZ"),
    array('$set' => array('coverageType' => "NNFLei-Mkjie83-Opejr93f")),
    array("multiple" => true)
);

$cursor1 = $apiObj->mongoFind($collectionQuery);
echo $cursor1->count();
})->via('GET','POST');


$app->map('/info/:table', function ($table) use ($app,$settings) {
    $result = array();
    $result['headers'] = array(
        0=>"Fronter",
        1=>"Policies",
        2=>"Average",
        3=>"Premium"
    );
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    if(!$apiObj->userLoggedIn()){
        echo "";
        exit();
    }
    $apiObj->mongoSetCollection("user");
    $cursor1 = $apiObj->mongoFind();
    if($cursor1->count() == 0){
        $userList = array();
    } else {
        foreach (iterator_to_array($cursor1) as $doc2) {
            $userList[$doc2['_id']] = $doc2['firstname'] . " " . $doc2['lastname'];
        }
    } 
    $userIds = $apiObj->getUserIds();
    $apiObj->mongoSetCollection("policy");
    $collectionQuery['_timestampCreated']['$lte'] = date("YmdHis");   
    $collectionQuery['_timestampCreated']['$gte'] = date("Ymd000000"); 
    $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
    $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
    $personIds = array();
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $policies = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $personIds[] = $doc2['_parentId'];
            $policies['closed'][$doc2['closedBy']]['policies'][$doc2['_id']]['personId'] =  $doc2['_parentId'];
            $policies['closed'][$doc2['closedBy']]['count'] = count($policies['closed'][$doc2['closedBy']]['policies']);
            $policies['closed'][$doc2['closedBy']]['premium'] = count($policies['closed'][$doc2['closedBy']]['policies']);
            $policies['fronter'][$doc2['soldBy']]['policies'][$doc2['_id']]['personId'] =  $doc2['_parentId'];
            $policies['fronter'][$doc2['soldBy']]['count'] = count($policies['fronter'][$doc2['soldBy']]['policies']);
            $policies['fronter'][$doc2['soldBy']]['premium'] = count($policies['fronter'][$doc2['soldBy']]['policies']);
        }
    }
    foreach ( $policies['closed'] as $key => $row) {
        $volume[$key]  = $row['count'];
        $premium[$key]  = $row['premium'];
    }
    array_multisort($volume, SORT_DESC, $premium, SORT_DESC, $policies['closed']);
    foreach ( $policies['fronter'] as $key => $row) {
        $volume2[$key]  = $row['count'];
        $premium2[$key]  = $row['premium'];
    }
    array_multisort($volume2, SORT_DESC, $premium2, SORT_DESC, $policies['fronter']);
    if($table == "closerTable"){
        foreach($policies['closed'] as $key=>$info){
            $result['rows'][] = array(
                $userList[$key],
                $policies['closed'][$key]['count'],
                1,
                1
            );
        }
    } else {
        foreach($policies['fronter'] as $key=>$info){
            $result['rows'][] = array(
                $userList[$key],
                $policies['fronter'][$key]['count'],
                1,
                1
            );
        }
    }
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response['X-Powered-By'] = 'EBC';
    $response->status(200);
    // etc.
    $response->body(json_encode($result));
})->via('GET','POST');
$app->get('/addusertogroup', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ehealthbrokers");
    $apiObj->mongoSetCollection("user");
    $cursor = $apiObj->mongoFind();
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $original_doc) {
            $users[$original_doc['_id']] = $original_doc['_id'];
        }
    }
    $apiObj->mongoSetCollection("userGroups");
    $cursor = $apiObj->mongoFind();
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $original_doc) {
            //$users[] = $original_doc['_id'];
            $userGroup = $original_doc;
        }
    }
    //debug($userGroup);
    foreach($userGroup['users'] as $key=>$value){
        $usergroupies[$value['userId']] = $value['userId'];
    }
    foreach($users as $key=>$val){
        if(empty($usergroupies[$key])){
            $post["userGroups_0_createThing"] = "Y";
            $post["userGroups_0_id"] = "20151101085244-mP7tq48N-rvAQWP3G";
            $post["userGroups_0_users_0_userId"] = $key;
            $post["userGroups_0_users_0_createThing"] = "N";
            $post["userGroups_0_users_0_level"] = "user";
            debug($post);
        } else {
            //echo "<br>Found: ".   $key;
        }
    }
    echo "here";
    exit();
    /*
    $agents[1098] = "Larry Tran";
    $agents[1097] = "Ibeakalam Nduka";
    $agents[1096] = "Charles Holmes";
    $agents[1095] = "Lamar Turner";
    $agents[1094] = "Daisy Navarro";
    $agents[1093] = "Shanell Pierce";
    $agents[1092] = "Caryne Aguilar";
    $agents[1091] = "Debbie Butler";
    $agents[1090] = "Sharon Jackson";
    $agents[1089] = "Ruben Hurtado";
    $agents[1088] = "William Dickson";
    $agents[1087] = "Darrell Robinson";
    $agents[1086] = "Anneth Gaul";
    $agents[1085] = "Ruth Socoy";
    $agents[1084] = "Kasey Harris";
    $agents[1083] = "Zachary Kabir";
    $agents[1082] = "Darnell Smith";
    $agents[1056] = "Patrick Burns";
    foreach($agents as $key=>$val){
    $formFields['user_0_createThing'] = "Y";
    $formFields['user_0_id'] = $apiObj->getRandomId();
        $name = explode(" ", strtolower($val));
    $formFields['user_0_firstname'] = strtoupper($name[0]); 
    $formFields['user_0_lastname'] = strtoupper($name[1]);
    $formFields['user_0_email'] = substr($name[0],0,1). "". $name[1]. "@exchangeadvisers.com";
    $formFields['user_0_phone'] = "5555555555";
    $formFields['user_0_password'] = "EBC2015!";
    $formFields['user_0_licensed'] = "N";
    $formFields['user_0_canSell'] = "Y";
     $formFields['user_0_status'] = "ACTIVE";
    $post["userGroups_0_createThing"] = "Y";
    $post["userGroups_0_id"] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
    $post["userGroups_0_users_0_userId"] = $formFields['user_0_id'];
    $post["userGroups_0_users_0_createThing"] = "N";
    $post["userGroups_0_users_0_level"] = "user";
   // debug($formFields);
    //    $apiObj->save_things($formFields);
    //    debug($post);
    //    $apiObj->save_things($post);
    }
     */  
    /*
    $collectionQuery['_timestampCreated']['$gt'] = "20151029000000";
    $cursor = $apiObj->mongoFind($collectionQuery);
    $post["userGroups_0_createThing"] = "Y";
    $post["userGroups_0_id"] = "SF6sRxUq-USlDmYfU-MrXSBWgx";
    $i = 0;
    foreach (iterator_to_array($cursor) as $key=>$doc) {
        $post["userGroups_0_users_".$i."_userId"] = $doc['_id'];
        $post["userGroups_0_users_".$i."_createThing"] = "N";
        $post["userGroups_0_users_".$i."_level"] = "user";
        $i++;
        //debug($doc);
    }
    debug($post);
    $apiObj->save_things($post);
    */
});
$app->get('/password/:password', function ($password) use ($app,$settings) 
          {
              $apiObj = new apiclass($settings);
              $options = [
                  'cost' => 10,
                  'salt' => $settings['password_salt']
              ];
              $value =  password_hash($password, PASSWORD_BCRYPT, $options);
              echo $value;
          });
$app->get('/emaillist', function () use ($app,$settings) 
          {
              $apiObj = new apiclass($settings);
              $apiObj->mongoSetDB("ebrokerTest2");
              $apiObj->mongoSetCollection("user");
              $cursor = $apiObj->mongoFind($collectionQuery);
              foreach (iterator_to_array($cursor) as $doc) {
                  if(strtolower($doc['status']) == "active"){
                      $pos = strpos($doc['email'], "exchangeadvisers");
                      if ($pos === false) {
                      } else {
                          echo $doc['email']."; ";
                      }
                  }
              }
          });
$app->get('/adduserlist', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    //Agent Name ,Phone ,Email ,EBC,IMF,Pay Rate
    /*
    $users = array();
    $users[] = "Brian White,,,X,,,";
    $users[] = "Cameron Bryant,949-412-3534,cameron.frsolutions@gmail.com,X,,,";
    $users[] = "Carla Ruiz ,,,,X,,";
    $users[] = "Jackie Dambrocia,909-319-4783,jmdgtidldy@hotmail.com,,X,,";
    $users[] = "John Beckos ,909-286-4071,jmbeckos@yahoo.com,,X,,";
    $users[] = "MD Sadi,909-304-1927,Sadi.Shibli.md@gmail.com,,X,,";
    $users[] = "Nicole Randolph,,,,X,,7,Yolanda Chaney ,,,X,";
    $users[] = "Patricia Ponce,951-536-9617 ,pattytex2001@yahoo.com,X,,,8,,,,,";
    $users[] = "Patrick Kelly ,310-598-0669,pkkinsurance@yahoo.com,,X,,9,,,,,";
    $users[] = "Saidah Anderson,909-258-0615,anderson6298@adelphia.net,,X,,10,,,,,";
    $users[] = "Sarah Beltran,714-612-5430,sarahgualco@yahoo.com,X,,,11,,,,,";
    $users[] = "Tom Sponcil,951 -552-0570,ktsponcil@gmail.com,,X,,12,,,,,";
    $users[] = "Angela Galvin ,,,,,,13,,,,,";
    $users[] = "Nohemi Devora , ,,,X,,14,,,,,";
    $users[] = "Jennifer Spence ,,,,X,,15,,,,,";
    $users[] = "Michael Gregg,,,,X,,16,,,,,";
    $users[] = "Suzanne Bradley,,,,X,,17,,,,,";
    $users[] = "Mina Thomas ,,,X,,,18,,,,,";
    $users[] = "Shirlee Fequiere,909-336-8577,Sfequiere.Nucleus@gmail.com,X,";
    //$users[] = "Sean McCloskey,,,X,";
    $users[] = "Matthew Bosa ,,,X,";
    //$users[] = "Keith Johnson ,909-728-2276,kjworldwide@yahoo.com,X,";
    //$users[] = "Jordon Bryan,,,,X";
   // $users[] = "Chris Esser,,,X,";
    */
    //  Name,Department ,Position ,EBC ,IMF ,Phone ,Email,Pay Rate
    /*
    $users = array();
    $users[] = "Britteny Broomfield ,Insure HC ,CSR,,X,,,";
    $users[] = "Lashelle Knight,Insure HC ,CSR,X,,323-635-7044,lashellejnight17@gmail.com,11.00/hr";
    $users[] = "Ebony Cain ,Insure HC ,CSR,X,,,,";
    //$users[] = "Jennifer Fields ,Insure HC ,Temp/Manager ,X,,909-419-2930,fieldsjennifer5@gmail.com,12.00/hr";
    $users[] = "Marisela Sanchez ,Insure HC ,CSR,X,,,,";
    $users[] = "Mellisa Cordova ,Compliance ,Hall Monitor ,X,,,,";
    $users[] = "Krystle Flores,Compliance ,Manager ,X,,,,";
    $users[] = "Belquis Gulzarzada ,Reception ,Admin Assist ,X,,909-297-9032,Bella.Gulzarzada@gmail.com,10.00/hr";
    $users[] = "Krystal Wylder ,Reception ,Admin Assist ,x,,951-202-2593,krystalwilder90@gmail.com,10.00/hr";
    */
    /*
    $users = array();
   // $users[] = "Persia Jones ,909-559-7830,persiajones@hotmail.com,X,,11.00/hr";
    //$users[] = "Tiani Flanagan ,909-642-8198,tiani.flanagan13@gmail.com,X,,11.00/hr";
    $users[] = "Mathew Brooks ,619-325-7132,,X,,11.00hr";
    $users[] = "Jenna Adamson ,909-531-8113,jennabean87@aim.com,X,,12.00/hr";
    $users[] = "Tierra Gant ,909-904-3011,ganttierra@yahoo. Com,X,,10.00/hr";
    $users[] = "Elizabeth Ruiz ,909-260-6716,elizabeth91764@gmail.com,X,,11.00/hr";
    $users[] = "Treyvon Ralph ,909-489-9829,ralph25@me.com,X,,13.00/hr";
    $users[] = "Marisela Chavez ,909-689-6446,marislea12chavez@gmail.com,X,,10.00/hr";
    $users[] = "Tina Medrano ,909-731-0135,tlmedrano88@yahoo.com,X,,10.00/hr";
    $users[] = "Chester Stewart,909-336-8577,navacane4x10@yahoo.com,X,,10.00/hr";
    $users[] = "Yvette Richel,909-927-9700,y.rishel@yahoo.com,X,,13.00/hr";
    $users[] = "Charles Mendoza,909-728-9883,cm.founder@outlook.com,X,,11.00/hr";
    $users[] = "Kory Wilson ,562-469-9011,porkys07@hotmail.com,X,,12.00/hr";
    $users[] = "Jailen Esteen,323-384-7880,jailenesteen@gmail.com,X,,10.00/hr";
    $users[] = "Katt Luna ,415-629-4784,luna.kaleo@gmail.com ,X,,10.00/hr";
    $users[] = "Patrick Young ,801-634-6029,pyguyi@yahoo.com,X ,,10.00/hr";
    $users[] = "Norma Williams ,442-243-0271,nuwilliams13@gmail.com,X,,13.00/hr";
    $users[] = "Angelina Garcia ,909-767-1205,garciatigers07@gmail.com,X,,10.00/jr";
    $users[] = "Kamileh Smith ,626-679-0938,no email,X,,13.00/hr";
    $users[] = "Maria Rosado ,909-708-3179,mariarosado34@yahoo.com,X,,13.00/hr";
    $users[] = "Taneisha Harris ,909-454-2015,tharplaya@yahoo.com,X,,10.00/hr";
    $users[] = "Norman Jones ,909-294-4685,no email,X,,13.00/hr";
    $users[] = "Scott Nwarueze,909-990-8331,snwarveze1@gmail.com,X,,11.00/hr";
    $users[] = "Blake Pelmon ,909-714-8728,no email,X,,10.00/hr";
    $users[] = "Naimah Manning ,562-674-4269,manningnai023@gmail.com,X,,10.00/hr";
    $users[] = "Regina Gutierrez ,760-605-5198,reggutavi1025@gmail.com,X,,10.00/hr";
    $users[] = "Steve Hilborn,,,,X,12.00/hr";
    $users[] = "Gabrielle Turrubiartes,,,,X,";
    $users[] = "Shwenti Sheats,,,,X,";
    $users[] = "Cynthia Ricci ,,,,X,";
    $users[] = "Tiara Williams ,,,,X,";
    $users[] = "Cheryl Eustaquio,,,,X,";
    $users[] = "Miriam Graciano ,,,,X,";
    $users[] = "Erin Williams ,,,,X,";
    $users[] = "Joy Faamaligi,,,,X,";
    $users[] = "Danielle Hardison ,,,,X,";
    $users[] = "Lupe Espinoza ,,,,X,";
    $users[] = "Lisa Bennett,,,,X,";
    $users[] = "Andy Madrigal,,,,X,";
    $users[] = "Emily White ,,,,X,";
    $users[] = "Alma Andrade,,,,X,";
    $users[] = "Ashley Cooley ,,,,X,";
    $users[] = "Amunease Allen ,,,,X,";
    $users[] = "Cindy Diaz ,,,,X,";
    $users[] = "Sofine Bingham,760-780-0034,ambitioussofine@gmail.com,,X,10.00/hr";
    $users[] = "Robert Zalaha,,,,X,";
    $users[] = "Mary Sayeah,,,,X,";
    $users[] = "Kristina Spence,,,,X,";
    $users[] = "Quiana Wright,,,,X,";
    $users[] = "Debra Davis,,,,X,";
    $users[] = "Brianna Araiza,,,,X,";
    $users[] = "Brandon Munoz ,,,,X,";
    $users[] = "Evan Sumpter,,,,X,";
    $users[] = "Jose Fierro ,,,,X,";
    $users[] = "Jody Rishel ,,,,X,";
    $users[] = "Crystal Johnson ,,,,X,";
    $users[] = "Roxanne Hagin ,,,,X,";
    */
    echo "<PRE>";
    foreach($users as $key=>$value){
        $userinfo = explode(",",$value);
        if(strtoupper(trim($userinfo[4])) == "X"){ 
            $usnername = explode(" ",trim($userinfo[0]));
            $usernameFull = $usnername[0];
            $usernameFull = strtolower(substr($usernameFull, 0, 1)."".$usnername[1]);
            /*
            $user[$key] = array();
            $user[$key]['createThing'] = "Y";
            $user[$key]['firstname'] = strtoupper($usnername[0]);
            $user[$key]['lastname'] = strtoupper($usnername[1]);
            $user[$key]['phone'] = $userinfo[1];
             if(strtoupper(trim($userinfo[4])) == "X"){
                 $user[$key]['email'] = $usernameFull."@ehealthbrokers.com";
                 $user[$key]['password'] = "health123!";
             } else {
                 $user[$key]['email'] = $usernameFull."@exchangeadvisers.com";
                 $user[$key]['password'] = "EBC2015!";
             }
            $user[$key]['agreeToTerms'] = "Y";
            $user[$key]['status'] = "ACTIVE";
            $user[$key]['licensed'] = "Y";
            $user[$key]['canSell'] = "Y";
            $user[$key]['permissionLevel'] = "user";
            $user[$key]['agencyId'] = '20151015105805-askejij-ruIuej31';
               */
            //  Name,Department ,Position ,EBC ,IMF ,Phone ,Email,Pay Rate
            /*
            $user[$key] = array();
            $user[$key]['createThing'] = "Y";
            $user[$key]['firstname'] = strtoupper($usnername[0]);
            $user[$key]['lastname'] = strtoupper($usnername[1]);
            $user[$key]['phone'] = $userinfo[5];
           if(strtoupper(trim($userinfo[4])) == "X"){
                 $user[$key]['email'] = $usernameFull."@ehealthbrokers.com";
                 $user[$key]['password'] = "health123!";
             } else {
                 $user[$key]['email'] = $usernameFull."@exchangeadvisers.com";
                 $user[$key]['password'] = "EBC2015!";
             }
            $user[$key]['agreeToTerms'] = "Y";
            $user[$key]['status'] = "ACTIVE";
            $user[$key]['licensed'] = "Y";
            $user[$key]['canSell'] = "Y";
            $user[$key]['permissionLevel'] = "user";
            $user[$key]['agencyId'] =  '20151015105805-askejij-ruIuej31';
            $user[$key]['department'] = strtoupper($userinfo[1]);
            $user[$key]['position'] = strtoupper($userinfo[2]);
            */
            //echo "<h1>".strtolower($usernameFull)."</h1>";
            //print_r($usnername);
            //print_r($userinfo);
        }
    }
    foreach($user as $key=>$value){
        foreach ($value as $ukey=>$uvalue){
            $post['user_'.$key.'_'.$ukey] = $uvalue;   
        }
    }
    print_r($post);
    //$apiObj->save_things($post);
    print_r($user);
    // print_r($users);
});
//
//
//
//
//
// Credit user187291 Stack Overflow
//http://stackoverflow.com/questions/6088115/transform-flat-array-into-a-hierarchical-multi-dimensional-array
function ins(&$ary, $keys, $val) {
    $keys ? ins($ary[array_shift($keys)], $keys, $val) :
    $ary = $val;
}
function updateItems($arr,$apiObj){
    foreach($arr as $k=>$v){
        if(!is_array($v)){
            $arr[$k] = $apiObj->format_thing_value($k, $v);
        } else {
            $arr[$k] = updateItems($v,$apiObj);
        }
    }
    return $arr;
}
/*
function processNew($new,$apiObj,  $thingKeeper = false, $parent = false, $parent_id= false){
    $thing = array();
    $_id = $apiObj->getRandomId();
    $_createThing = "Y";
    $_deleteThing = "N";
    foreach($new as $k1=>$v1){
        if(is_array($v1)){
            debug($v1 , " Parent: ". $parent. " Type: ". $k1 . " Parent Id ". $parent_id);
            foreach($v1 as $k=>$v){
                if(empty($parent)){
                    $parent = $k1;   
                    $parent_id = $v['id'];
                }
                if(!is_array($v)){
                   // echo "<P>".$k;
                    if((strtolower($k) != "id") && (strtolower($k) != "creatething") && (strtolower($k) != "deletething")){
                        $thing[$k] = $v;
                    } 
                    if(strtolower($k) == "id"){
                        $_id = $v;
                    }
                    if((strtolower($k) == "deletething") && (strtolower($v) == "y")){
                        $_deleteThing = "Y";
                    }
                } else {
                    //  debug($v);
                    processNew($v,$apiObj, $thingKeeper, $parent, $parent_id);
                }
            }
             debug ($thing);
        }
    }
    if(!empty($thing)){
        $value_count = 0;
        foreach($thing as $key=>$value){
            if(trim($value) <> ""){
                $value_count++;   
            }
        }
        $thing['_id'] = $_id;
        if($value_count == 0){
            $thingKeeper->things["delete"][] = $thing;
            debug($thing, "DELETE ".$k . " - ". $_id . "  CREATE ". $_createThing . " DELETE ". $_deleteThing . " Parent: ". $parent_id);
        }  else {
            $thingKeeper->things["save"][] = $thing;
            debug($thing, " SAVE ".$k . " - ". $_id . "  CREATE ". $_createThing . " DELETE ". $_deleteThing . " Parent: ". $parent_id);
        }
    }
      return $thing;
}
*/
class thingKeeper {
    var $things = array();   
    function processNew($arr,$apiObj, $thing = FALSE){
        $parent = $thing;
        if(is_array($arr)){
            foreach($arr as $key=>$val){
                $thing = $key;
                if(is_array($val)){
                    foreach($val as $key2=>$val2){
                        if(is_array($val2)){
                            $parent_key = $key;
                            echo "<P>PARENT: ". $parent. "  Parent Key : ". $parent_key . " THING: ".$thing . " Key: " . $key2;
                            $this->processNew($val2,$apiObj, $thing);
                        }
                    }
                } else {
                    echo  "<P>HERE: ". $key. " - ".$val;
                }
                /*
                debug($val, "Key " .$key);   
                if(!is_array($val)){
                    echo "here";   
                } else {
                    foreach($val as $key2=>$val2){
                        debug($val2, "Key " .$key2);   
                    }
                }
				*/
            }
        }
    }
    function   create_thing_array($post_array, $parent_id = FALSE, $parent_thing = FALSE, $parent_key = FALSE ){
        $this->things = array();
        foreach($post_array as $key1=>$value)
        {
            if(is_array($value))
            {
                foreach($value as $key2=>$doc)
                {
                    $_temp_thing = array();
                    $_id=FALSE;
                    $new_thing = TRUE;
                    if ( (!empty ($doc['id'])) && ($doc['id'] == "") )
                    {
                        unset($doc['id']);
                    }
                    if ( (!empty ($doc['_id'])) && ($doc['_id'] == "") )
                    {
                        unset($doc['id']);
                    }
                    if (!empty ($doc['id']))
                    {
                        // Set key with right _id format for Mongo
                        $_id= $doc['id'];
                        unset($doc['id']);
                    }
                    if (!empty ($doc['_id']))
                    {
                        $_id = $doc['_id'];
                        unset($doc['_id']);
                    }
                    if(empty($_id)){
                        $_id = $this->getRandomId();
                    }
                    /*
                    $_temp_thing['id'] = $_id;
                    $_temp_thing['parentId'] = $parent_id;
                    $_temp_thing['parentThing'] = $parent_thing;
                    if(!empty($doc['timestampCreated'])){
                        $_temp_thing['timestampCreated'] = date("YmdHis", strtotime($doc['timestampCreated']));
                    } else {
                        $_temp_thing['timestampCreated'] = date("YmdHis") ;
                    }
                    if(!empty($doc['timestampModified'])){
                        $_temp_thing['timestampModified'] = date("YmdHis", strtotime($doc['timestampModified']));
                    } else {
                        $_temp_thing['timestampModified'] = date("YmdHis") ;
                    }
                    if(!empty($_SESSION['api']['user']['_id'])){
                        $_temp_thing['createdBy'] = $_SESSION['api']['user']['_id'] ;
                        $_temp_thing['modifiedBy'] = $_SESSION['api']['user']['_id'] ;
                    }
                    if(!empty($this->settings['conversion'])){
                        if(!empty($doc['dateCreated'])){
                            $_temp_thing['timestampCreated'] = date("YmdHis", strtotime($doc['dateCreated']));
                            unset($doc['dateCreated']);
                        }
                        if(!empty($doc['dateModified'])){
                            $_temp_thing['timestampModified'] = date("YmdHis", strtotime($doc['dateModified']));
                            unset($doc['dateModified']);
                        }
                    }
                    */
                    //$_temp_thing['_parentKey'] = $parent_key;
                    if(is_array($doc)){
                        foreach($doc as $key3=>$value3)
                        {
                            if (is_array($value3))
                            {   
                                $return_value = $this->create_thing_array_child($value3, $key3, $_id, $key1, $key2 );
                                if(!empty($return_value)){
                                    $_temp_thing[$key3] = $return_value;  
                                }
                            } else {
                                $temp_value = $this->format_thing_value($key3,$value3);
                                if(!empty($temp_value)){
                                    //$_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
                                    $_temp_thing[$key3] = $temp_value;
                                }
                            }
                        }
                    }
                    //if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE) || ($doc['createThing'] === "Y") || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
                    $this->things[$key1][] = $_temp_thing;
                    // }
                }
            }
        }
        return false;
    }
    function getRandomId(){
        return "hererhehre0";
    }
    function format_thing_value($key,$val){
        return $val;
    }
    function create_thing_array_child($child_array, $child_thing = false, $parent_id = false, $parent_thing= false, $parent_key= false){
        $return_children = false;
        if(is_array($child_array))
        {
            foreach($child_array as $key2=>$doc)
            {
                if(is_array($doc)){
                    $_temp_thing = array();
                    $_id = FALSE;
                    if (!empty ($doc['id']))
                    {
                        $_id= $doc['id'];
                        unset($doc['id']);
                    }
                    if (!empty ($doc['_id']))
                    {
                        $_id = $doc['_id'];
                        unset($doc['_id']);
                    }
                    if(empty($_id)){
                        $_id = $this->getRandomId();
                    }
                    /*
                    $_temp_thing['id'] = $_id;
                    $_temp_thing['parentId'] = $parent_id;
                    $_temp_thing['parentThing'] = $parent_thing;
                    if(!empty($doc['timestampCreated'])){
                        $_temp_thing['timestampCreated'] = date("YmdHis", strtotime($doc['timestampCreated']));
                    } else {
                        $_temp_thing['timestampCreated'] = date("YmdHis") ;
                    }
                    if(!empty($doc['timestampModified'])){
                        $_temp_thing['timestampModified'] = date("YmdHis", strtotime($doc['timestampModified']));
                    } else {
                        $_temp_thing['timestampModified'] = date("YmdHis") ;
                    }
                    if(!empty($_SESSION['api']['user']['_id'])){
                        $_temp_thing['createdBy'] = $_SESSION['api']['user']['_id'] ;
                        $_temp_thing['modifiedBy'] = $_SESSION['api']['user']['_id'] ;
                    }
                   */
                    //$_temp_thing['_parentKey'] = $parent_key;
                    foreach($doc as $key3=>$value3)
                    {
                        if (is_array($value3))
                        {
                            $return_value = $this->create_thing_array_child($value3, $key3, $_id, $child_thing, $key2 );
                            if(!empty($return_value)){
                                $_temp_thing[$key3] = $return_value;  
                            }
                        } else {
                            $temp_value = $this->format_thing_value($key3,$value3);
                            if(!empty($temp_value)){
                                if($key3 != "createThing"){
                                    //$_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
                                    $_temp_thing[$key3] = $temp_value;
                                }
                            }
                        }
                    }
                    //if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE)  || ($doc['createThing'] === "Y") || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
                    $this->things[$child_thing][] = $_temp_thing;
                    //} else {
                    //    $return_children[] = $_temp_thing;   
                    //}
                }
            }
        }
        // debug($return_children, "return children");
        //return $return_children;
    }
}
$app->get('/testit', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("trentTest2");
    if($apiObj->userLoggedIn()){
        //$apiObj->mongoSetCollection("saveAll");
        //$apiObj->mongoInsert($_REQUEST);
        $old = array (
            'person_0_createThing' => 'Y',
            'person_0_id' => '20150928172828-wzlwbfGE-9spllJVg',
            'person_0_dateCreated' => '20151103010101',
            'person_0_dateModified' => '20151103010101',
            'person_0_title' => '',
            'person_0_firstName' => 'Test',
            'person_0_middleName' => '',
            'person_0_lastName' => 'Test',
            'person_0_suffix' => 'III',
            'person_0_gender' => 'M',
            'person_0_socialSecurityNumber' => '333-444-3333',
            'person_0_dateOfBirth' => '09/03/2015',
            'person_0_smokerTabacco' => 'Y',
            'person_0_assignedTo' => 'auSpFsFC-Hs4WQenx-qQJSbMud',
            'person_0_disposition' => 'GOODLEAD',
            'person_0_phones_0_createThing' => 'Y',
            'person_0_phones_0_id' => '20150928173046-eoELBeHD-xU5lXoNV',
            'person_0_phones_0_phoneNumber' => '(760) 902-2341',
            'person_0_phones_0_phoneType' => 'adas',
            'person_0_phones_1_createThing' => 'Y',
            'person_0_phones_1_id' => '20150928173112-qqn2269J-a72h5zvw',
            'person_0_phones_1_phoneNumber' => '(222) 333-4444',
            'person_0_phones_1_phoneType' => '',
            'person_0_phones_1_phoneSms' => 'NO',
            'person_0_emails_0_createThing' => 'Y',
            'person_0_emails_0_id' => '20151007115921-Fx0gXww0-r04zsWlI',
            'person_0_emails_0_email' => '',
            'person_0_emails_0_type' => '',
            'person_0_addresses_0_createThing' => 'Y',
            'person_0_addresses_0_id' => '20150928173233-lV06sN64-aDPy3DeF',
            'person_0_addresses_0_street1' => '123 Main St!',
            'person_0_addresses_0_street2' => '',
            'person_0_addresses_0_city' => 'Brea',
            'person_0_addresses_0_CODE1' => 'Brea',
            'person_0_addresses_0_CODE2' => 'Brea',
            'person_0_addresses_0_CODE3' => 'Brea',
            'person_0_addresses_0_CODE4' => 'Brea',
            'person_0_addresses_0_state' => 'CA',
            'person_0_addresses_0_zipCode' => '92821',
            'person_0_addresses_0_county' => 'USA',
            'person_0_taxes_0_createThing' => 'N',
            'person_0_taxes_0_id' => '20150928172828-aokb3Tzw-lxJBJNgo',
            'person_0_taxes_0_employmentStatus' => '',
            'person_0_taxes_0_incomeYear' => '',
            'person_0_taxes_0_estimatedFollowingIncome' => '',
            'person_0_taxes_0_estimatedYearlyIncome' => '',
            'person_0_taxes_0_planToFileTaxes' => '',
            'person_0_taxes_0_fileTaxesJointly' => '',
            'person_0_taxes_0_taxesClaimDependents' => '',
            'person_0_taxes_0_taxesAreYourADependent' => '',
            'person_0_incomeSources_0_createThing' => 'N',
            'person_0_incomeSources_0_deleteThing' => 'Y',
            'person_0_incomeSources_0_id' => '20150928172828-RU3E702Z-Ai0w2Ea9',
            'person_0_incomeSources_0_incomeMoney' => '0.00',
            'person_0_incomeSources_0_incomeType' => '',
            'person_0_incomeSources_0_incomeFrequency' => '',
            'person_0_employers_0_createThing' => 'N',
            'person_0_employers_0_id' => '20150928172828-1bziUDKr-koYahuS8',
            'person_0_employers_0_name' => '',
            'person_0_employers_0_phone' => '',
            'person_0_employers_0_address' => '',
            'person_0_employers_0_city' => '',
            'person_0_employers_0_state' => '',
            'person_0_employers_0_zipcode' => '',
            'person_0_employers_0_wages' => '',
            'person_0_employers_0_payFrequency' => '',
            'person_0_employers_0_hoursWeekly' => '',
            'person_0_spouse_0_createThing' => 'N',
            'person_0_spouse_0_id' => '20150928172828-SqecqfJd-PNaUy60l',
            'person_0_spouse_0_spouseTitle' => 'MR',
            'person_0_spouse_0_spouseFirstName' => 'GENE',
            'person_0_spouse_0_spouseMiddleName' => 'ROSS',
            'person_0_spouse_0_spouseLastName' => 'BANYARD',
            'person_0_spouse_0_spouseSuffix' => '',
            'person_0_spouse_0_spouseGender' => '',
            'person_0_spouse_0_spouseSocialSecurityNumber' => '',
            'person_0_spouse_0_spouseDateOfBirth' => '',
            'person_0_spouse_0_spouseSmoker' => '',
            'person_0_dependents_0_createThing' => 'N',
            'person_0_dependents_0_id' => '20150928172828-UgvNu3q4-wmLNetuV',
            'person_0_dependents_0_dependentsFirstName' => '',
            'person_0_dependents_0_dependentsLastName' => '',
            'person_0_dependents_0_dependentsSocialSecurityNumber' => '',
            'person_0_dependents_0_dependentsDateOfBirth' => '',
            'person_0_policy_0_createThing' => 'Y',
            'person_0_policy_0_id' => '20150928175124-9md2q3Ru-l5nCOhSD',
            'person_0_policy_0_status' => 'SOLD',
            'person_0_policy_0_policyNumber' => 'FFWWW',
            'person_0_policy_0_carrier' => 'nTn3nGz5-670fFb0m-4R1GN2mD',
            'person_0_policy_0_coverageType' => 'Mcv1iVm5-LLixKVnc-iYkc9EmI',
            'person_0_policy_0_setupFeeMoney' => '0.00',
            'person_0_policy_0_premiumMoney' => '4.00',
            'person_0_policy_0_subsidyMoney' => '0.00',
            'person_0_policy_0_submissionDate' => '',
            'person_0_policy_0_renewalDate' => '',
            'person_0_policy_0_effectiveDate' => '',
            'person_0_policy_0_termDate' => '',
            'person_0_policy_0_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_0_closedBy' => 'dwFfagta-xmeUr4K4-S7coafmJ',
            'person_0_policy_0_notes' => '        ',
            'person_0_policy_1_createThing' => 'Y',
            'person_0_policy_1_id' => '20151007115930-rC0JwsLo-BuSuBZ5A',
            'person_0_policy_1_status' => 'SOLD',
            'person_0_policy_1_policyNumber' => 'ASDD',
            'person_0_policy_1_carrier' => 'FGNjkEft-n1r3SpZe-WA5tDLYi',
            'person_0_policy_1_coverageType' => 'uvxXbO2Q-9Tz7FV8R-pbWJngfC',
            'person_0_policy_1_setupFeeMoney' => '0.00',
            'person_0_policy_1_premiumMoney' => '12.00',
            'person_0_policy_1_subsidyMoney' => '0.00',
            'person_0_policy_1_submissionDate' => '',
            'person_0_policy_1_renewalDate' => '',
            'person_0_policy_1_effectiveDate' => '',
            'person_0_policy_1_termDate' => '',
            'person_0_policy_1_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_1_closedBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
            'person_0_policy_1_notes' => '',
            'person_0_policy_2_createThing' => 'Y',
            'person_0_policy_2_id' => '20151007120714-XjWkFuPU-aanIlyXN',
            'person_0_policy_2_status' => 'SOLD',
            'person_0_policy_2_policyNumber' => '23423432',
            'person_0_policy_2_carrier' => 'heFzY3j4-Qfjp9LjW-GeQ2SiT0',
            'person_0_policy_2_coverageType' => 'pCWCcwIC-TmQnUcNt-0oEZIVJE',
            'person_0_policy_2_setupFeeMoney' => '0.00',
            'person_0_policy_2_premiumMoney' => '34.00',
            'person_0_policy_2_subsidyMoney' => '0.00',
            'person_0_policy_2_submissionDate' => '',
            'person_0_policy_2_renewalDate' => '',
            'person_0_policy_2_effectiveDate' => '',
            'person_0_policy_2_termDate' => '',
            'person_0_policy_2_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_2_closedBy' => '',
            'person_0_policy_2_notes' => '',
            'person_0_notes_0_createThing' => 'Y',
            'person_0_notes_0_information' => '',
            'person_0_notes_0_id' => '20151007121119-K1c8mabB-ixBa38lZ',
            'person_0_notes_1_createThing' => 'Y',
            'person_0_notes_1_id' => '20151007121119-rMOR5oDr-dqPsVoCz',
            'person_0_notes_2_createThing' => 'Y',
            'person_0_notes_2_id' => '20151007121119-pBM0d4gD-TZZq3ls3',
            'person_0_notes_3_createThing' => 'Y',
            'person_0_notes_3_id' => '20151007121119-9MBDVZDX-zZPCxnow',
            'person_0_notes_4_createThing' => 'Y',
            'person_0_notes_4_id' => '20151007121119-uyzau1VA-b1fFbTca',
            'person_0_notes_5_createThing' => 'Y',
            'person_0_notes_5_id' => '20151007121119-pij4sXGh-dBofebU5',
            'person_0_notes_6_createThing' => 'Y',
            'person_0_notes_6_id' => '20151007121119-7zUBe6GB-cXUOh8WR',
            'person_0_notes_7_createThing' => 'Y',
            'person_0_notes_7_id' => '20151007121119-9p0N9xcv-BC4IDmxG',
            'person_0_notes_8_createThing' => 'Y',
            'person_0_notes_8_id' => '20151007121119-0ABNYpGx-Q3FZnm1H',
            'person_0_notes_9_createThing' => 'Y',
            'person_0_notes_9_id' => '20151007121119-n4lQpb8o-H6gIcXld',
            'person_0_notes_10_createThing' => 'Y',
            'person_0_notes_10_id' => '20151007121119-hLMhs8TG-dNwcIlIL',
            'person_0_notes_11_createThing' => 'Y',
            'person_0_notes_11_id' => '20151007121119-4GltDViW-lPswcBxl',
            'person_0_notes_12_createThing' => 'Y',
            'person_0_notes_12_id' => '20151007121119-5JwPXtCz-zhMpV9gg',
            'person_0_notes_13_createThing' => 'Y',
            'person_0_notes_13_id' => '20151007121119-IT27cJCA-QvVQ5sHs',
            'person_0_notes_14_createThing' => 'Y',
            'person_0_notes_14_id' => '20151007121119-xSxsYFFT-bxdoKUei',
            'person_0_notes_15_createThing' => 'Y',
            'person_0_notes_15_id' => '20151007121119-kO7SJr4J-ugELAk2X',
            'person_0_banking_0_createThing' => 'N',
            'person_0_banking_0_id' => '20150928172828-Irw5EiCe-A4VtpszK',
            'person_0_banking_0_paymentBankName' => 'test',
            'person_0_banking_0_paymentBankAccountType' => 'CHECKING',
            'person_0_banking_0_paymentBankRoutingNumber' => '111222333444',
            'person_0_banking_0_paymentBankAccountNumber' => '111222333',
            'person_0_creditcard_0_createThing' => 'N',
            'person_0_creditcard_0_id' => '20150928172828-l3u40VTh-YbINZgzS',
            'person_0_creditcard_0_paymentCreditCardType' => '',
            'person_0_creditcard_0_paymentNameOnCard' => '',
            'person_0_creditcard_0_paymentCardNumber' => '',
            'person_0_creditcard_0_paymentCCV' => '',
            'person_0_creditcard_0_paymentCreditCardMonth' => '',
            'person_0_creditcard_0_paymentCreditCardYear' => '',
        );
        $post = array (
            'person_0_createThing' => 'Y',
            'person_0_id' => '20150928172828-wzlwbfGE-9spllJVg',
            'person_0_title' => '',
            'person_0_firstName' => 'Test',
            'person_0_middleName' => '',
            'person_0_lastName' => 'Test',
            'person_0_suffix' => 'III',
            'person_0_gender' => 'M',
            'person_0_socialSecurityNumber' => '333-444-3333',
            'person_0_dateOfBirth' => '09/03/2015',
            'person_0_smokerTabacco' => 'Y',
            'person_0_assignedTo' => 'auSpFsFC-Hs4WQenx-qQJSbMud',
            'person_0_disposition' => 'GOODLEAD',
            'person_0_phones_0_createThing' => 'Y',
            'person_0_phones_0_id' => '20150928173046-eoELBeHD-xU5lXoNV',
            'person_0_phones_0_phoneNumber' => '(760) 902-2341',
            'person_0_phones_0_phoneType' => 'adas',
            'person_0_phones_1_createThing' => 'Y',
            'person_0_phones_1_id' => '20150928173112-qqn2269J-a72h5zvw',
            'person_0_phones_1_phoneNumber' => '(222) 333-4444',
            'person_0_phones_1_phoneType' => '',
            'person_0_phones_1_phoneSms' => 'NO',
            'person_0_emails_0_createThing' => 'Y',
            'person_0_emails_0_id' => '20151007115921-Fx0gXww0-r04zsWlI',
            'person_0_emails_0_email' => '',
            'person_0_emails_0_type' => '',
            'person_0_addresses_0_createThing' => 'Y',
            'person_0_addresses_0_id' => '20150928173233-lV06sN64-aDPy3DeF',
            'person_0_addresses_0_street1' => '123 Main St!',
            'person_0_addresses_0_street2' => '',
            'person_0_addresses_0_city' => 'Brea',
            'person_0_addresses_0_CODE1' => '',
            'person_0_addresses_0_CODE9' => 'NACBBC',
            'person_0_addresses_0_state' => 'CA',
            'person_0_addresses_0_zipCode' => '92821',
            'person_0_addresses_0_county' => 'USA',
            'person_0_taxes_0_createThing' => 'N',
            'person_0_taxes_0_id' => '20150928172828-aokb3Tzw-lxJBJNgo',
            'person_0_taxes_0_employmentStatus' => '',
            'person_0_taxes_0_incomeYear' => '',
            'person_0_taxes_0_estimatedFollowingIncome' => '',
            'person_0_taxes_0_estimatedYearlyIncome' => '',
            'person_0_taxes_0_planToFileTaxes' => '',
            'person_0_taxes_0_fileTaxesJointly' => '',
            'person_0_taxes_0_taxesClaimDependents' => '',
            'person_0_taxes_0_taxesAreYourADependent' => '',
            'person_0_incomeSources_0_createThing' => 'N',
            'person_0_incomeSources_0_id' => '20150928172828-RU3E702Z-Ai0w2Ea9',
            'person_0_incomeSources_0_incomeMoney' => '0.00',
            'person_0_incomeSources_0_incomeType' => '',
            'person_0_incomeSources_0_incomeFrequency' => '',
            'person_0_employers_0_createThing' => 'N',
            'person_0_employers_0_id' => '20150928172828-1bziUDKr-koYahuS8',
            'person_0_employers_0_name' => '',
            'person_0_employers_0_phone' => '',
            'person_0_employers_0_address' => '',
            'person_0_employers_0_city' => '',
            'person_0_employers_0_state' => '',
            'person_0_employers_0_zipcode' => '',
            'person_0_employers_0_wages' => '',
            'person_0_employers_0_payFrequency' => '',
            'person_0_employers_0_hoursWeekly' => '',
            'person_0_spouse_0_createThing' => 'N',
            'person_0_spouse_0_id' => '20150928172828-SqecqfJd-PNaUy60l',
            'person_0_spouse_0_spouseTitle' => '',
            'person_0_spouse_0_spouseFirstName' => '',
            'person_0_spouse_0_spouseMiddleName' => '',
            'person_0_spouse_0_spouseLastName' => 'NEWNAME',
            'person_0_spouse_0_CODE3' => 'ANAHEIM',
            'person_0_spouse_0_CODE9' => 'NACBBC',
            'person_0_spouse_0_spouseSuffix' => '',
            'person_0_spouse_0_spouseGender' => '',
            'person_0_spouse_0_spouseSocialSecurityNumber' => '',
            'person_0_spouse_0_spouseDateOfBirth' => '',
            'person_0_spouse_0_spouseSmoker' => '',
            'person_0_spouse_0_spousechild_createThing' => 'N',
            'person_0_spouse_0_spousechild_id' => '20150928172828-SqecqfJd-PNaUy60l',
            'person_0_spouse_0_spousechild_spouseTitle' => '',
            'person_0_spouse_0_spousechild_spouseFirstName' => '',
            'person_0_spouse_0_spousechild_spouseMiddleName' => '',
            'person_0_spouse_0_spousechild_spouseLastName' => 'NEWNAME',
            'person_0_spouse_0_spousechild_CODE3' => 'ANAHEIM',
            'person_0_spouse_0_spousechild_CODE9' => 'NACBBC',
            'person_0_spouse_0_spousechild_spouseSuffix' => '',
            'person_0_spouse_0_spousechild_spouseGender' => '',
            'person_0_spouse_0_spousechild_spouseSocialSecurityNumber' => '',
            'person_0_spouse_0_spousechild_spouseDateOfBirth' => '',
            'person_0_spouse_0_spousechild_spouseSmoker' => '',
            'person_0_dependents_0_createThing' => 'N',
            'person_0_dependents_0_id' => '20150928172828-UgvNu3q4-wmLNetuV',
            'person_0_dependents_0_dependentsFirstName' => '',
            'person_0_dependents_0_dependentsLastName' => '',
            'person_0_dependents_0_dependentsSocialSecurityNumber' => '',
            'person_0_dependents_0_dependentsDateOfBirth' => '',
            'person_0_policy_0_createThing' => 'Y',
            'person_0_policy_0_id' => '20150928175124-9md2q3Ru-l5nCOhSD',
            'person_0_policy_0_status' => 'SOLD',
            'person_0_policy_0_policyNumber' => 'FFWWW',
            'person_0_policy_0_carrier' => 'nTn3nGz5-670fFb0m-4R1GN2mD',
            'person_0_policy_0_coverageType' => 'Mcv1iVm5-LLixKVnc-iYkc9EmI',
            'person_0_policy_0_setupFeeMoney' => '0.00',
            'person_0_policy_0_premiumMoney' => '4.00',
            'person_0_policy_0_subsidyMoney' => '0.00',
            'person_0_policy_0_submissionDate' => '',
            'person_0_policy_0_renewalDate' => '',
            'person_0_policy_0_effectiveDate' => '',
            'person_0_policy_0_termDate' => '',
            'person_0_policy_0_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_0_closedBy' => 'dwFfagta-xmeUr4K4-S7coafmJ',
            'person_0_policy_0_notes' => '        ',
            'person_0_policy_1_createThing' => 'Y',
            'person_0_policy_1_id' => '20151007115930-rC0JwsLo-BuSuBZ5A',
            'person_0_policy_1_status' => 'SOLD',
            'person_0_policy_1_policyNumber' => 'ASDD',
            'person_0_policy_1_carrier' => 'FGNjkEft-n1r3SpZe-WA5tDLYi',
            'person_0_policy_1_coverageType' => 'uvxXbO2Q-9Tz7FV8R-pbWJngfC',
            'person_0_policy_1_setupFeeMoney' => '0.00',
            'person_0_policy_1_premiumMoney' => '12.00',
            'person_0_policy_1_subsidyMoney' => '0.00',
            'person_0_policy_1_submissionDate' => '',
            'person_0_policy_1_renewalDate' => '',
            'person_0_policy_1_effectiveDate' => '',
            'person_0_policy_1_termDate' => '',
            'person_0_policy_1_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_1_closedBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
            'person_0_policy_1_notes' => '',
            'person_0_policy_2_createThing' => 'Y',
            'person_0_policy_2_id' => '20151007120714-XjWkFuPU-aanIlyXN',
            'person_0_policy_2_status' => 'SOLD',
            'person_0_policy_2_policyNumber' => '23423432',
            'person_0_policy_2_carrier' => 'heFzY3j4-Qfjp9LjW-GeQ2SiT0',
            'person_0_policy_2_coverageType' => 'pCWCcwIC-TmQnUcNt-0oEZIVJE',
            'person_0_policy_2_setupFeeMoney' => '0.00',
            'person_0_policy_2_premiumMoney' => '34.12320',
            'person_0_policy_2_subsidyMoney' => '0.00',
            'person_0_policy_2_submissionDate' => '',
            'person_0_policy_2_renewalDate' => '',
            'person_0_policy_2_effectiveDate' => '',
            'person_0_policy_2_termDate' => '',
            'person_0_policy_2_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_0_policy_2_closedBy' => '',
            'person_0_policy_2_notes' => '',
            'person_0_notes_0_createThing' => 'Y',
            'person_0_notes_0_information' => '',
            'person_0_notes_0_id' => '20151007121119-K1c8mabB-ixBa38lZ',
            'person_0_notes_1_createThing' => 'Y',
            'person_0_notes_1_id' => '20151007121119-rMOR5oDr-dqPsVoCz',
            'person_0_notes_2_createThing' => 'Y',
            'person_0_notes_2_id' => '20151007121119-pBM0d4gD-TZZq3ls3',
            'person_0_notes_3_createThing' => 'Y',
            'person_0_notes_3_id' => '20151007121119-9MBDVZDX-zZPCxnow',
            'person_0_notes_4_createThing' => 'Y',
            'person_0_notes_4_id' => '20151007121119-uyzau1VA-b1fFbTca',
            'person_0_notes_5_createThing' => 'Y',
            'person_0_notes_5_id' => '20151007121119-pij4sXGh-dBofebU5',
            'person_0_notes_6_createThing' => 'Y',
            'person_0_notes_6_id' => '20151007121119-7zUBe6GB-cXUOh8WR',
            'person_0_notes_7_createThing' => 'Y',
            'person_0_notes_7_id' => '20151007121119-9p0N9xcv-BC4IDmxG',
            'person_0_notes_8_createThing' => 'Y',
            'person_0_notes_8_id' => '20151007121119-0ABNYpGx-Q3FZnm1H',
            'person_0_notes_9_createThing' => 'Y',
            'person_0_notes_9_id' => '20151007121119-n4lQpb8o-H6gIcXld',
            'person_0_notes_10_createThing' => 'Y',
            'person_0_notes_10_id' => '20151007121119-hLMhs8TG-dNwcIlIL',
            'person_0_notes_11_createThing' => 'Y',
            'person_0_notes_11_id' => '20151007121119-4GltDViW-lPswcBxl',
            'person_0_notes_12_createThing' => 'Y',
            'person_0_notes_12_id' => '20151007121119-5JwPXtCz-zhMpV9gg',
            'person_0_notes_12_note' => 'alkfjkdja sklfjkla fjfj fjk asklfjasfkljfklsjd fklajs fklasjdf klj',
            'person_0_notes_13_createThing' => 'Y',
            'person_0_notes_13_id' => '20151007121119-IT27cJCA-QvVQ5sHs',
            'person_0_notes_14_createThing' => 'Y',
            'person_0_notes_14_id' => '20151007121119-xSxsYFFT-bxdoKUei',
            'person_0_notes_15_createThing' => 'Y',
            'person_0_notes_15_id' => '20151007121119-kO7SJr4J-ugELAk2X',
            'person_0_banking_0_createThing' => 'N',
            'person_0_banking_0_id' => '20150928172828-Irw5EiCe-A4VtpszK',
            'person_0_banking_0_paymentBankName' => 'test',
            'person_0_banking_0_paymentBankAccountType' => 'CHECKING',
            'person_0_banking_0_paymentBankRoutingNumber' => '111222333444',
            'person_0_banking_0_paymentBankAccountNumber' => '111222333',
            'person_0_creditcard_0_createThing' => 'N',
            'person_0_creditcard_0_id' => '20150928172828-l3u40VTh-YbINZgzS',
            'person_0_creditcard_0_paymentCreditCardType' => '',
            'person_0_creditcard_0_paymentNameOnCard' => '',
            'person_0_creditcard_0_paymentCardNumber' => '',
            'person_0_creditcard_0_paymentCCV' => '',
            'person_0_creditcard_0_paymentCreditCardMonth' => '',
            'person_0_creditcard_0_paymentCreditCardYear' => '',
            'person_1_createThing' => 'Y',
            'person_1_id' => '43634643643-wzlwbfGE-9spllJVg',
            'person_1_title' => '',
            'person_1_firstName' => 'Test',
            'person_1_middleName' => '',
            'person_1_lastName' => 'Test',
            'person_1_suffix' => 'III',
            'person_1_gender' => 'M',
            'person_1_socialSecurityNumber' => '333-444-3333',
            'person_1_dateOfBirth' => '09/03/2015',
            'person_1_smokerTabacco' => 'Y',
            'person_1_assignedTo' => 'auSpFsFC-Hs4WQenx-qQJSbMud',
            'person_1_disposition' => 'GOODLEAD',
            'person_1_phones_0_createThing' => 'Y',
            'person_1_phones_0_id' => '34634646-eoELBeHD-xU5lXoNV',
            'person_1_phones_0_phoneNumber' => '(760) 902-2341',
            'person_1_phones_0_phoneType' => 'adas',
            'person_1_phones_1_createThing' => 'Y',
            'person_1_phones_1_id' => '436436436-qqn2269J-a72h5zvw',
            'person_1_phones_1_phoneNumber' => '(222) 333-4444',
            'person_1_phones_1_phoneType' => '',
            'person_1_phones_1_phoneSms' => 'NO',
            'person_1_emails_0_createThing' => 'Y',
            'person_1_emails_0_id' => '34634643643-Fx0gXww0-r04zsWlI',
            'person_1_emails_0_email' => '',
            'person_1_emails_0_type' => '',
            'person_1_addresses_0_createThing' => 'Y',
            'person_1_addresses_0_id' => '34664364-lV06sN64-aDPy3DeF',
            'person_1_addresses_0_street1' => '123 Main St!',
            'person_1_addresses_0_street2' => '',
            'person_1_addresses_0_city' => 'Brea',
            'person_1_addresses_0_CODE1' => '',
            'person_1_addresses_0_CODE9' => 'NACBBC',
            'person_1_addresses_0_state' => 'CA',
            'person_1_addresses_0_zipCode' => '92821',
            'person_1_addresses_0_county' => 'USA',
            'person_1_taxes_0_createThing' => 'N',
            'person_1_taxes_0_id' => '346346436-aokb3Tzw-lxJBJNgo',
            'person_1_taxes_0_employmentStatus' => '',
            'person_1_taxes_0_incomeYear' => '',
            'person_1_taxes_0_estimatedFollowingIncome' => '',
            'person_1_taxes_0_estimatedYearlyIncome' => '',
            'person_1_taxes_0_planToFileTaxes' => '',
            'person_1_taxes_0_fileTaxesJointly' => '',
            'person_1_taxes_0_taxesClaimDependents' => '',
            'person_1_taxes_0_taxesAreYourADependent' => '',
            'person_1_incomeSources_0_createThing' => 'N',
            'person_1_incomeSources_0_id' => '346346436-RU3E702Z-Ai0w2Ea9',
            'person_1_incomeSources_0_incomeMoney' => '0.00',
            'person_1_incomeSources_0_incomeType' => '',
            'person_1_incomeSources_0_incomeFrequency' => '',
            'person_1_employers_0_createThing' => 'N',
            'person_1_employers_0_id' => '6436436-1bziUDKr-koYahuS8',
            'person_1_employers_0_name' => '',
            'person_1_employers_0_phone' => '',
            'person_1_employers_0_address' => '',
            'person_1_employers_0_city' => '',
            'person_1_employers_0_state' => '',
            'person_1_employers_0_zipcode' => '',
            'person_1_employers_0_wages' => '',
            'person_1_employers_0_payFrequency' => '',
            'person_1_employers_0_hoursWeekly' => '',
            'person_1_spouse_0_createThing' => 'N',
            'person_1_spouse_0_id' => '436436-SqecqfJd-PNaUy60l',
            'person_1_spouse_0_spouseTitle' => '',
            'person_1_spouse_0_spouseFirstName' => '',
            'person_1_spouse_0_spouseMiddleName' => '',
            'person_1_spouse_0_spouseLastName' => 'NEWNAME',
            'person_1_spouse_0_CODE3' => 'ANAHEIM',
            'person_1_spouse_0_CODE9' => 'NACBBC',
            'person_1_spouse_0_spouseSuffix' => '',
            'person_1_spouse_0_spouseGender' => '',
            'person_1_spouse_0_spouseSocialSecurityNumber' => '',
            'person_1_spouse_0_spouseDateOfBirth' => '',
            'person_1_spouse_0_spouseSmoker' => '',
            'person_1_spouse_0_spousechild_createThing' => 'N',
            'person_1_spouse_0_spousechild_id' => '346346-SqecqfJd-PNaUy60l',
            'person_1_spouse_0_spousechild_spouseTitle' => '',
            'person_1_spouse_0_spousechild_spouseFirstName' => '',
            'person_1_spouse_0_spousechild_spouseMiddleName' => '',
            'person_1_spouse_0_spousechild_spouseLastName' => 'NEWNAME',
            'person_1_spouse_0_spousechild_CODE3' => 'ANAHEIM',
            'person_1_spouse_0_spousechild_CODE9' => 'NACBBC',
            'person_1_spouse_0_spousechild_spouseSuffix' => '',
            'person_1_spouse_0_spousechild_spouseGender' => '',
            'person_1_spouse_0_spousechild_spouseSocialSecurityNumber' => '',
            'person_1_spouse_0_spousechild_spouseDateOfBirth' => '',
            'person_1_spouse_0_spousechild_spouseSmoker' => '',
            'person_1_dependents_0_createThing' => 'N',
            'person_1_dependents_0_id' => '4363464-UgvNu3q4-wmLNetuV',
            'person_1_dependents_0_dependentsFirstName' => '',
            'person_1_dependents_0_dependentsLastName' => '',
            'person_1_dependents_0_dependentsSocialSecurityNumber' => '',
            'person_1_dependents_0_dependentsDateOfBirth' => '',
            'person_1_policy_0_createThing' => 'Y',
            'person_1_policy_0_id' => '6436346-9md2q3Ru-l5nCOhSD',
            'person_1_policy_0_status' => 'SOLD',
            'person_1_policy_0_policyNumber' => 'FFWWW',
            'person_1_policy_0_carrier' => 'nTn3nGz5-670fFb0m-4R1GN2mD',
            'person_1_policy_0_coverageType' => 'Mcv1iVm5-LLixKVnc-iYkc9EmI',
            'person_1_policy_0_setupFeeMoney' => '0.00',
            'person_1_policy_0_premiumMoney' => '4.00',
            'person_1_policy_0_subsidyMoney' => '0.00',
            'person_1_policy_0_submissionDate' => '',
            'person_1_policy_0_renewalDate' => '',
            'person_1_policy_0_effectiveDate' => '',
            'person_1_policy_0_termDate' => '',
            'person_1_policy_0_soldBy' => '20151005133721-yxysRHVa-dRHl82gP',
            'person_1_policy_0_closedBy' => 'dwFfagta-xmeUr4K4-S7coafmJ',
            'person_1_policy_0_notes' => '        ',
            'person_1_policy_1_createThing' => 'Y',
            'person_1_policy_1_id' => '236236326-rC0JwsLo-BuSuBZ5A',
            'person_1_policy_1_status' => 'SOLD',
            'person_1_policy_1_policyNumber' => 'ASDD',
            'person_1_policy_1_carrier' => 'FGNjkEft-n1r3SpZe-WA5tDLYi',
            'person_1_policy_1_coverageType' => 'uvxXbO2Q-9Tz7FV8R-pbWJngfC',
            'person_1_policy_1_setupFeeMoney' => '0.00',
            'person_1_policy_1_premiumMoney' => '12.00',
            'person_1_policy_1_subsidyMoney' => '0.00',
            'person_1_policy_1_submissionDate' => '',
            'person_1_policy_1_renewalDate' => '',
            'person_1_policy_1_effectiveDate' => '',
            'person_1_policy_1_termDate' => '',
            'person_1_policy_1_soldBy' => '236326-yxysRHVa-dRHl82gP',
            'person_1_policy_1_closedBy' => '236236-k7N1dHZi-4I7ZoB2J',
            'person_1_policy_1_notes' => '',
            'person_1_policy_2_createThing' => 'Y',
            'person_1_policy_2_id' => '326236623-XjWkFuPU-aanIlyXN',
            'person_1_policy_2_status' => 'SOLD',
            'person_1_policy_2_policyNumber' => '23423432',
            'person_1_policy_2_carrier' => 'heFzY3j4-Qfjp9LjW-GeQ2SiT0',
            'person_1_policy_2_coverageType' => 'pCWCcwIC-TmQnUcNt-0oEZIVJE',
            'person_1_policy_2_setupFeeMoney' => '0.00',
            'person_1_policy_2_premiumMoney' => '34.12320',
            'person_1_policy_2_subsidyMoney' => '0.00',
            'person_1_policy_2_submissionDate' => '',
            'person_1_policy_2_renewalDate' => '',
            'person_1_policy_2_effectiveDate' => '',
            'person_1_policy_2_termDate' => '',
            'person_1_policy_2_soldBy' => '35235235-yxysRHVa-dRHl82gP',
            'person_1_policy_2_closedBy' => '',
            'person_1_policy_2_notes' => '',
            'person_1_notes_0_createThing' => 'Y',
            'person_1_notes_0_information' => '',
            'person_1_notes_0_id' => '234234-K1c8mabB-ixBa38lZ',
            'person_1_notes_1_createThing' => 'Y',
            'person_1_notes_1_id' => '234324-rMOR5oDr-dqPsVoCz',
            'person_1_notes_2_createThing' => 'Y',
            'person_1_notes_2_id' => '2343243-pBM0d4gD-TZZq3ls3',
            'person_1_notes_3_createThing' => 'Y',
            'person_1_notes_3_id' => '23432423-9MBDVZDX-zZPCxnow',
            'person_1_notes_4_createThing' => 'Y',
            'person_1_notes_4_id' => '324234324-uyzau1VA-b1fFbTca',
            'person_1_notes_5_createThing' => 'Y',
            'person_1_notes_5_id' => '234324-pij4sXGh-dBofebU5',
            'person_1_notes_6_createThing' => 'Y',
            'person_1_notes_6_id' => '242324-7zUBe6GB-cXUOh8WR',
            'person_1_notes_7_createThing' => 'Y',
            'person_1_notes_7_id' => '235235325-9p0N9xcv-BC4IDmxG',
            'person_1_notes_8_createThing' => 'Y',
            'person_1_notes_8_id' => '23523-0ABNYpGx-Q3FZnm1H',
            'person_1_notes_9_createThing' => 'Y',
            'person_1_notes_9_id' => '235235235253-n4lQpb8o-H6gIcXld',
            'person_1_notes_10_createThing' => 'Y',
            'person_1_notes_10_id' => '235235235-hLMhs8TG-dNwcIlIL',
            'person_1_notes_11_createThing' => 'Y',
            'person_1_notes_11_id' => '235235235-4GltDViW-lPswcBxl',
            'person_1_notes_12_createThing' => 'Y',
            'person_1_notes_12_id' => '234235-5JwPXtCz-zhMpV9gg',
            'person_1_notes_12_note' => 'alkfjkdja sklfjkla fjfj fjk asklfjasfkljfklsjd fklajs fklasjdf klj',
            'person_1_notes_13_createThing' => 'Y',
            'person_1_notes_13_id' => '23423432-IT27cJCA-QvVQ5sHs',
            'person_1_notes_14_createThing' => 'Y',
            'person_1_notes_14_id' => '234234-xSxsYFFT-bxdoKUei',
            'person_1_notes_15_createThing' => 'Y',
            'person_1_notes_15_id' => '234324-kO7SJr4J-ugELAk2X',
            'person_1_banking_0_createThing' => 'N',
            'person_1_banking_0_id' => '234-Irw5EiCe-A4VtpszK',
            'person_1_banking_0_paymentBankName' => 'test',
            'person_1_banking_0_paymentBankAccountType' => 'CHECKING',
            'person_1_banking_0_paymentBankRoutingNumber' => '111222333444',
            'person_1_banking_0_paymentBankAccountNumber' => '111222333',
            'person_1_creditcard_0_createThing' => 'N',
            'person_1_creditcard_0_id' => '23423-l3u40VTh-YbINZgzS',
            'person_1_creditcard_0_paymentCreditCardType' => '',
            'person_1_creditcard_0_paymentNameOnCard' => '',
            'person_1_creditcard_0_paymentCardNumber' => '',
            'person_1_creditcard_0_paymentCCV' => '',
            'person_1_creditcard_0_paymentCreditCardMonth' => '',
            'person_1_creditcard_0_paymentCreditCardYear' => '',
        );
        $update = array_merge($old,$post);
        debug($update);
        // Convert to Array from Strings
        $new = array();
        foreach ($update as $k=>$v){
            ins($new, explode('_', $k), $v);
        }
        //$a = array_filter_recursive( $a );
        //  debug($a);
        // Format to Saved Variables Date, Encrypt, Phones etc
        // $new = updateItems($new, $apiObj);
        // $a = removeBlanks($a);
        debug($new);
        if(empty($thingKeeper)){
            $thingKeeper = new thingKeeper();   
        }
        $thingKeeper->create_thing_array($new);
        debug($thingKeeper->things);
        exit();
        /*
        echo "<PRE>";
       // print_r($post);
        $thing_array = $apiObj->parse_post($post);
       // print_r($thing_array);
         $apiObj->create_thing_array($thing_array);
        print_r($apiObj->things);
        exit();
        */
        if($apiObj->save_things($post)){
            $result['message'] = "Things Saved";    
        } else {
            $result['message'] = "There was an error saving your Things.";   
        }
        echo $result['message'];
        // header("Content-Type: application/json");
        // echo json_encode($result);
    } else {
        echo "Not Logged In";   
    }
});
function _decrypt($encrypted_string = false, $encryption_key = false)
{
    //MIG
    $cryptSalt 	 = 'SDKFJASDFK323#$@#*($@)3ASKFJASFJ';
    $encryptionKey 	 = 'DASDFKDSHFLKJ23L!@#!312LKJKL';
    $encryptionIV 	 = 'ASKDJASLKqweKJ23432424JD@#@!!M';
    //EBROKER
    $cryptSalt 	 ='LKASJL0#)9212312940J!)@#J0j-Kasf';
    $encryptionKey 	 = 'KAJDOm9320m!Om1P@MPME!K!MPOMP';
    $encryptionIV 	 ='ASDNQKLMNQLDM131231omo!M';
    if (empty (trim($encrypted_string)))
    {
        return FALSE;
    }
    if(strlen($encrypted_string) < 15){
        return $encrypted_string;   
    }
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $encryptionKey);
    $secret_iv = $encryptionIV;
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    $output = openssl_decrypt(base64_decode($encrypted_string), $encrypt_method, $key, 0, $iv);
    return $output;
}
$app->get('/fixoldCarrier', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokercentermain");
    $apiObj->mongoSetCollection("carrier");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokercenterCRM');
    $collection = 'policy';
    foreach (iterator_to_array($cursor) as $doc) {
        echo "<P>".$doc['option'];   
        $db->$collection->update(
            array("carrierName" => new MongoRegex("/".trim($doc['option'])."/i") ),
            array('$set' => array('carrier' =>$doc['_id'])),
            array("multiple" => true)
        );
    }
});
$app->get('/assurant', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->mongoSetCollection("policy");
    $collectionQuery['carrier']['$eq'] =  'Qiq3PkVh-alqqsuz0-Il4lL80o';
    $collectionQuery['_timestampCreated']['$gte'] = "20151001000000"; 
    // $collectionQuery['policyNumber']['$eq'] =  new MongoRegex("/625/");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $totalpolicies = $cursor->count();
    $policynumbers = 0;
    $futurepolicies= 0;
    $nodates = 0;
    $pastdue = 0;
    foreach (iterator_to_array($cursor) as $doc) {
        $pos = strpos($doc['policyNumber'], '00625');
        if ($pos === false) {
            if($doc['submissionDate'] > date("Ymd000000"))
            {
                $futurepolicies++;
            } else {
                $pastdue++;   
            }
            if (trim($doc['submissionDate'])  == ""){
                $nodates++;
            }
        } else {
            $policynumbers++;
        }
    }
    echo "<P>Total Policies: ".$totalpolicies;
    echo "<P>Policies with Policy Number: ".$policynumbers;
    echo "<P>Future Policies: ".$futurepolicies;
    echo "<P>No Date: ".$nodates;
    echo "<P>Past Due: ".$pastdue;
});
$app->get('/vici', function () use ($app,$settings) {
    //   http://97.93.171.182/vicidial/non_agent_api.php?source=test&function=did_log_export&stage=pipe&user=1099&pass=463221&phone_number=2076203290&date=2015-10-23
});
$app->get('/userGroupManagers', function () use ($app,$settings) {
    exit(); 
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $post['userGroups_0_id'] = "Mjeko3k2-gwkOp1p2K-nfmJKEKkw";
    $post['userGroups_0_createThing'] = "Y";
    $post['userGroups_0_users_0_createThing'] = "N";
    $post['userGroups_0_users_0_userId'] = "20151019094003-Gh3l9cUv-uBPsYGg7";
    $post['userGroups_0_users_0_level'] = "manager";
    $post['userGroups_0_users_1_createThing'] = "N";
    $post['userGroups_0_users_1_userId'] = "20151005154138-k7N1dHZi-4I7ZoB2J";
    $post['userGroups_0_users_1_level'] = "manager";
    $post['userGroups_0_users_2_createThing'] = "N";
    $post['userGroups_0_users_2_userId'] = "20151005153950-AnfiHs14-FgS0mrvJ";
    $post['userGroups_0_users_2_level'] = "manager";
    $post['userGroups_0_users_3_createThing'] = "N";
    $post['userGroups_0_users_3_userId'] = "20151005153859-W2mwrWhB-oQ9GB4nM";
    $post['userGroups_0_users_3_level'] = "manager";
    $post['userGroups_0_users_4_createThing'] = "N";
    $post['userGroups_0_users_4_userId'] = "20151005134133-XnakJJkb-G9KC7MU8";
    $post['userGroups_0_users_4_level'] = "manager";
    $apiObj->save_things($post);
});
$app->get('/oldChars', function () use ($app,$settings) {
    exit();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->mongoSetCollection("user");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokerTest2');
    $collection = 'user';
    /*  $db->$collection->update(
            array("coverageTypeName" => new MongoRegex("/MAJOR_MEDICAL/i")  ),
            array('$set' => array('coverageType' =>'NNFLei-Mkjie83-Opejr93f')),
            array("multiple" => true)
        );
        */
    foreach (iterator_to_array($cursor) as $doc) {
        $pos = strpos($doc['email'], "allinsurancecenter.com");
        if ($pos === false) {
            echo "NO";
        } else {
            // debug($doc);
            $db->$collection->update(
                array("_id" => $doc['_id'] ),
                array('$set' => array('lastname' =>$doc['lastname'] . " - AF")),
                array("multiple" => false)
            );
        }
        /*
        $db->$collection->update(
            array("coverageTypeName" => new MongoRegex("/".trim($doc['option'])."/i") ),
            array('$set' => array('coverageType' =>$doc['_id'])),
            array("multiple" => true)
        );
        */
    }
});
$app->get('/fixoldCoverage', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokercentermain");
    $apiObj->mongoSetCollection("carrierPlan");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokercenterCRM');
    $collection = 'policy';
    $db->$collection->update(
        array("coverageTypeName" => new MongoRegex("/MAJOR_MEDICAL/i")  ),
        array('$set' => array('coverageType' =>'NNFLei-Mkjie83-Opejr93f')),
        array("multiple" => true)
    );
    foreach (iterator_to_array($cursor) as $doc) {
        echo "<P>".$doc['option'];   
        $db->$collection->update(
            array("coverageTypeName" => new MongoRegex("/".trim($doc['option'])."/i") ),
            array('$set' => array('coverageType' =>$doc['_id'])),
            array("multiple" => true)
        );
    }
});
$app->get('/fixoldCreatedBy', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->mongoSetCollection("notes");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokerTest2');
    $collection = 'notes';
    foreach (iterator_to_array($cursor) as $doc) {
        if(!empty($doc['createdBy'])){
            $db->$collection->update(
                array("_id" => $doc['_id'] ),
                array('$set' => array('_createdBy' => $doc['createdBy']))
            );
        }
    }
});
$app->get('/fixoldStates', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->mongoSetCollection("addresses");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokerTest2');
    $collection = 'addresses';
    $cursor->skip(0);
    $cursor->limit(10000);
    foreach (iterator_to_array($cursor) as $doc) {
        if(strlen($doc['state']) > 2){
            debug($doc);
            if(strtoupper($doc['state']) == "FLORIDA"){
                $db->$collection->update(
                    array("_id" => $doc['_id'] ),
                    array('$set' => array('state' => "FL"))
                );
            }
        }
    }
});
$app->get('/fixoldStatus', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokercenterCRM");
    $apiObj->mongoSetCollection("policy");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokercenterCRM');
    $collection = 'policy';
    $cursor->skip(25000);
    $cursor->limit(5000);
    foreach (iterator_to_array($cursor) as $doc) {
        echo "<P>".$doc['option'];   
        $db->$collection->update(
            array("_id" => $doc['_id'] ),
            array('$set' => array('underwritingStatus' => $doc['status']))
        );
    }
});
$app->get('/fixoldStatusSet', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokercenterCRM");
    $apiObj->mongoSetCollection("policy");
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokercenterCRM');
    $collection = 'policy';
    $db->$collection->update(
        array("status" => 'Hold' ),
        array('$set' => array('status' =>'HOLD')),
        array("multiple" => true)
    );
    $db->$collection->update(
        array("status" => 'hold' ),
        array('$set' => array('status' =>'HOLD')),
        array("multiple" => true)
    );
    $db->$collection->update(
        array("status" => array('$ne'=> "HOLD") ),
        array('$set' => array('status' =>'SOLD')),
        array("multiple" => true)
    );
});
$app->get('/fixold', function () use ($app,$settings) {
    echo "Old";
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ephone");
    exit();
    $apiObj->mongoSetCollection("leads");
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        $cursor->sort(array('date_created' => -1));
        $cursor->skip(15186);
        $cursor->limit(1000);
        foreach (iterator_to_array($cursor) as $doc) {
            $docIds[] = $doc['_id'];
            $doc2 = array();
            $doc2['person_0_createThing'] = "Y";
            if(!empty($doc['_id'])){
                $doc2['person_0_id'] = $doc['_id'];
            }
            if(!empty($doc['date_created'])){
                $doc2['person_0_timestampCreated'] = date("YmdHis", strtotime($doc['date_created']));  
            }
            if(!empty($doc['date_modified'])){
                $doc2['person_0_timestampModified'] = date("YmdHis", strtotime($doc['date_modified'])); 
            }
            if(!empty($doc['created_by'])){
                $doc2['person_0_createdBy'] = $doc['created_by'];
            }
            if(!empty($doc['modified_by'])){
                $doc2['person_0_modifiedBy'] = $doc['modified_by'];
            }
            if(!empty($doc['assigned_to'])){
                $doc2['person_0_assignedTo'] = $doc['assigned_to'];
            }
            if(!empty($doc['lead_source'])){
                $doc2['person_0_leadSource'] = str_replace(" ", "", $doc['lead_source']); 
            }
            if(!empty($doc['lead_disposition'])){
                $doc2['person_0_disposition'] = $doc['lead_disposition'];
            }
            if(!empty($doc['person_1_first_name'])){
                $doc2['person_0_firstName'] = $doc['person_1_first_name'];
            }
            if(!empty($doc['person_1_middle_name'])){
                $doc2['person_0_middleName'] = $doc['person_1_middle_name'];
            }
            if(!empty($doc['person_1_last_name'])){
                $doc2['person_0_lastName'] = $doc['person_1_last_name'];
            }
            if(!empty($doc['person_1_title'])){
                $doc2['person_0_title'] = $doc['person_1_title'];
            }
            if(!empty($doc['person_1_suffix'])){
                $doc2['person_0_suffix'] = $doc['person_1_suffix'];
            }
            if(!empty($doc['person_1_gender'])){
                $doc2['person_0_gender'] =  $doc['person_1_gender'];
            }
            if(!empty($doc['person_1_date_of_birth'])){
                $doc2['person_0_dateofBirth'] = date("YmdHis", strtotime($doc['person_1_date_of_birth'])); 
            }
            if(!empty($doc['person_1_smoker'])){
                $doc2['person_0_smokerTabaccor'] = $doc['person_1_smoker'];
            }
            if(!empty($doc['person_1_social_security_number'])){
                $doc2['person_0_socialSecurityNumber'] = _decrypt($doc['person_1_social_security_number']);
                //$doc2['person_0_socialSecurityNumber'] = "XXX-XX-XXXX";
            }
            if(!empty($doc['person_1_social_security_number_last4'])){
                $doc2['person_0_socialSecurityNumberLast4'] = _decrypt($doc['person_1_social_security_number_last4']);
            }
            $spouse = array();
            if(!empty($doc['person_2_first_name'])){
                $spouse['spouseFirstName'] = $doc['person_2_first_name'];
            }
            if(!empty($doc['person_2_middle_name'])){
                $spouse['spouseMiddleName'] = $doc['person_2_middle_name'];
            }
            if(!empty($doc['person_2_last_name'])){
                $spouse['spouseLastName'] = $doc['person_2_last_name'];
            }
            if(!empty($doc['person_2_title'])){
                $spouse['spouseTitle'] = $doc['person_2_title'];
            }
            if(!empty($doc['person_2_suffix'])){
                $spouse['spouseSuffix'] = $doc['person_2_suffix'];
            }
            if(!empty($doc['person_2_gender'])){
                $spouse['spouseGender'] = $doc['person_2_gender'];
            }
            if(!empty($doc['person_2_date_of_birth'])){
                $spouse['spouseDateOfBirth'] = date("YmdHis", strtotime($doc['person_2_date_of_birth'])); 
            }
            if(!empty($doc['person_2_smoker'])){
                $spouse['spouseSmoker'] = $doc['person_2_smoker'];
            }
            if(!empty($doc['person_2_social_security_number'])){
                //$spouse['spouseSocialSecurityNumber'] = _decrypt($doc['person_2_social_security_number']);
                $spouse['spouseSocialSecurityNumber'] = XXX-XX-XXXX;
            }
            if(!empty($doc['person_2_social_security_number_last4'])){
                $spouse['spouseSocialSecurityNumberLast4'] = _decrypt($doc['person_2_social_security_number_last4']);
            }
            if(!empty($spouse)){
                $doc2['person_0_spouse_0_createThing'] = "N";
                foreach($spouse as $sk=>$sv){
                    $doc2['person_0_spouse_0_'.$sk] = $sv;   
                }
            }
            if(!empty($doc['do_not_call'])){
                $doc2['doNotCall'] = $doc['do_not_call'];
            }
            $doc2['person_0_addresses_0_createThing'] = "Y";
            if(!empty($doc['person_1_address_street'])){
                $doc2['person_0_addresses_0_street1'] = $doc['person_1_address_street'];
            }
            if(!empty($doc['person_1_address_apt'])){
                $doc2['person_0_addresses_0_apt'] = $doc['person_1_address_apt'];
            }
            if(!empty($doc['person_1_address_city'])){
                $doc2['person_0_addresses_0_city'] = $doc['person_1_address_city'];
            }
            if(!empty($doc['person_1_address_state'])){
                $doc2['person_0_addresses_0_state'] = $doc['person_1_address_state'];
            }
            if(!empty($doc['person_1_address_county'])){
                $doc2['person_0_addresses_0_county'] = $doc['person_1_address_county'];
            }
            if(!empty($doc['person_1_address_zipcode'])){
                $doc2['person_0_addresses_0_zipcode'] = $doc['person_1_address_zipcode'];
            }
            if(!empty($doc['person_1_address_country'])){
                $doc2['person_0_addresses_0_country'] = $doc['person_1_address_country'];
            }
            if(!empty($doc['emails'])){
                if(is_array($doc['emails'])){
                    foreach($doc['emails'] as $k=>$v){
                        if(!empty($v['person_1_email'])){
                            $doc2['person_0_emails_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_emails_'.$k.'_email'] = $v['person_1_email'];
                            $doc2['person_0_emails_'.$k.'_type'] = "";
                        }
                    }
                }
            }
            if(!empty($doc['phones'])){
                if(is_array($doc['phones'])){
                    foreach($doc['phones'] as $k=>$v){
                        $doc2['person_0_phones_'.$k.'_createThing'] = "Y";
                        if(!empty($v['person_1_phone_number'])){
                            $doc2['person_0_phones_'.$k.'_phoneNumber'] = $v['person_1_phone_number'];
                        }
                        if(!empty($v['person_1_phone_type'])){
                            $doc2['person_0_phones_'.$k.'_phoneType'] = $v['person_1_phone_type'];
                        }
                        if(!empty($v['person_1_phone_when_to_call'])){
                            $doc2['person_0_phones_'.$k.'_whenToCall'] = $v['person_1_phone_when_to_call'];
                        }
                    }
                }
            }
            if(!empty($doc['contact'])){
                if(is_array($doc['contact'])){
                    foreach($doc['contact'] as $k=>$v){
                        $information = "";
                        if(!empty($v['contact_information'])){
                            $information .= $v['contact_information'];
                        }
                        if(!empty($v['contact_disposition'])){
                            $information .= "\n <P>Disposition: ". $v['contact_disposition'];
                        }
                        if(!empty($v['follow_up_time'])){
                            $information .= "\n <P>Follow Up: ".$v['follow_up_time'];
                        }
                        if(!empty($v['follow_up_date'])){
                            $information .= "\n <P>Date ".$v['follow_up_date'];
                        }
                        if(trim($information) <> ""){
                            $doc2['person_0_notes_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_notes_'.$k.'_information']  = $information;
                            if(!empty($v['date_created'])){
                                $doc2['person_0_notes_'.$k.'_timestampCreated'] =date("YmdHis", strtotime($v['date_created'])); 
                            }
                            if(!empty($v['created_by'])){
                                $doc2['person_0_notes_'.$k.'_createdBy'] = $v['created_by'];
                            }
                        }
                    }
                }
            }
            if(!empty($doc['employers'])){
                if(is_array($doc['employers'])){
                    foreach($doc['employers'] as $k=>$v){
                        if(!empty($v['person_1_employer_name'])){
                            $doc2['person_0_employers_'.$k.'_createThing'] = "N";
                            $doc2['person_0_employers_'.$k.'_name'] = $v['person_1_employer_name'];
                        }
                        if(!empty($v['person_1_employer_phone'])){
                            $doc2['person_0_employers_'.$k.'_phone'] = $v['person_1_employer_phone'];
                        }
                        if(!empty($v['person_1_employer_address'])){
                            $doc2['person_0_employers_'.$k.'_address'] = $v['person_1_employer_address'];
                        }
                        if(!empty($v['person_1_employer_city'])){
                            $doc2['person_0_employers_'.$k.'_city'] = $v['person_1_employer_city'];
                        }
                        if(!empty($v['person_1_employer_state'])){
                            $doc2['person_0_employers_'.$k.'_state'] = $v['person_1_employer_state'];
                        }
                        if(!empty($v['person_1_employer_zipcode'])){
                            $doc2['person_0_employers_'.$k.'_zipcode'] = $v['person_1_employer_zipcode'];
                        }
                        if(!empty($v['person_1_employer_wages'])){
                            $doc2['person_0_employers_'.$k.'_wages'] = $v['person_1_employer_wages'];
                        } 
                        if(!empty($v['person_1_employer_pay_frequency'])){
                            $doc2['person_0_employers_'.$k.'_payFrequency'] = $v['person_1_employer_pay_frequency'];
                        }
                        if(!empty($v['person_1_employer_hours_weekly'])){
                            $doc2['person_0_employers_'.$k.'_hoursWeekly'] = $v['person_1_employer_hours_weekly'];
                        }
                    }
                }
            }
            if(!empty($doc['income'])){
                if(is_array($doc['income'])){
                    foreach($doc['income'] as $k=>$v){
                        if(!empty($v['income_amount'])){
                            if ($v['income_amount'] > 0){
                                $doc2['person_0_incomeSources_'.$k.'_createThing'] = "N";
                                $doc2['person_0_incomeSources_'.$k.'_incomeMoney'] = $v['income_amount'];
                            }
                        }
                        if(!empty($v['income_type'])){
                            $doc2['person_0_incomeSources_'.$k.'_incomeType'] = $v['income_type'];
                        }
                        if(!empty($v['income_frequency'])){
                            $doc2['person_0_incomeSources_'.$k.'_incomeFrequency'] = $v['income_frequency'];
                        }
                    }
                }
            }
            if(!empty($doc['deductions'])){
                if(is_array($doc['deductions'])){
                    foreach($doc['deductions'] as $k=>$v){
                        if(!empty($v['deductions_amount'])){
                            $doc2['person_0_incomeDeductions_'.$k.'_createThing'] = "N";
                            $doc2['person_0_incomeDeductions_'.$k.'_deductionMoney'] = $v['deductions_amount'];
                        }
                        if(!empty($v['deductions_type'])){
                            $doc2['person_0_incomeDeductions_'.$k.'_deductionType'] = $v['deductions_type'];
                        }
                        if(!empty($v['deductions_frequency'])){
                            $doc2['person_0_incomeDeductions_'.$k.'_deductionFrequency'] = $v['deductions_frequency'];
                        }
                    }
                }
            }
            if(!empty($doc['pets'])){
                if(is_array($doc['pets'])){
                    foreach($doc['pets'] as $k=>$v){
                        if(!empty($v['pet_name'])){
                            $doc2['person_0_pets_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_pets_'.$k.'_name'] = $v['pet_name'];
                        }
                        if(!empty($v['pets_type'])){
                            $doc2['person_0_pets_'.$k.'_type'] = $v['pets_type'];
                        }
                    }
                }
            }
            if(!empty($doc['dependents'])){
                if(is_array($doc['dependents'])){
                    foreach($doc['dependents'] as $k=>$v){
                        if(!empty($v['dependent_first_name'])){
                            $doc2['person_0_dependent_'.$k.'_createThing'] = "N";
                            $doc2['person_0_dependent_'.$k.'_dependentsFirstName'] = $v['dependent_first_name'];
                        }
                        if(!empty($v['dependent_middle_name'])){
                            $doc2['person_0_dependent_'.$k.'_dependentsMiddleName'] = $v['dependent_middle_name'];
                        }
                        if(!empty($v['dependent_last_name'])){
                            $doc2['person_0_dependent_'.$k.'_dependentsLastName'] = $v['dependent_last_name'];
                        }
                        if(!empty($v['dependent_date_of_birth'])){
                            $doc2['person_0_dependent_'.$k.'_dependentsDateOfBirth'] = $v['dependent_date_of_birth'];
                        }
                        if(!empty($v['dependent_social_security_number'])){
                            $doc2['person_0_dependent_'.$k.'_dependentsSocialSecurityNumber'] = _decrypt($v['dependent_social_security_number']);
                        }
                        if(!empty($v['dependent_smoker'])){
                            $doc2['person_0_dependent_'.$k.'_dependentsSmoker'] = $v['dependent_smoker'];
                        }
                    }
                }
            }
            $taxes = FALSE;
            if(!empty($doc['income_year'])){
                $doc2['person_0_taxes_0_incomeYear'] = $doc['income_year'];
                $taxes = TRUE;
            }
            if(!empty($doc['yearly_income'])){
                $doc2['person_0_taxes_0_estimatedYearlyIncomee'] = $doc['yearly_income'];
                $taxes = TRUE;
            }
            if(!empty($doc['following_year_income'])){
                $doc2['person_0_taxes_0_estimatedFollowingIncome'] = $doc['following_year_income'];
                $taxes = TRUE;
            }
            if(!empty($doc['plan_to_file_taxes_next_year'])){
                $doc2['person_0_taxes_0_planToFileTaxes'] = $doc['plan_to_file_taxes_next_year'];
                $taxes = TRUE;
            }
            if(!empty($doc['plan_to_file_taxes_next_year'])){
                $doc2['person_0_taxes_0_planToFileTaxes'] = $doc['plan_to_file_taxes_next_year'];
                $taxes = TRUE;
            }
            if(!empty($doc['taxes_file_jointly'])){
                $doc2['person_0_taxes_0_fileTaxesJointly'] = $doc['taxes_file_jointly'];
                $taxes = TRUE;
            }
            if(!empty($doc['joint_name_of_spouse'])){
                $doc2['person_0_taxes_0_jointSpouseName'] = $doc['joint_name_of_spouse'];
                $taxes = TRUE;
            }
            if(!empty($doc['claim_dependents'])){
                $doc2['person_0_taxes_0_taxesClaimDependents'] = $doc['claim_dependents'];
                $taxes = TRUE;
            }
            if(!empty($doc['are_you_a_dependent'])){
                $doc2['person_0_taxes_0_taxesAreYourADependent'] = $doc['are_you_a_dependent'];
                $taxes = TRUE;
            }
            if($taxes !== FALSE){
                $doc2['person_0_taxes_0_createThing'] = "N";
            }
            // BANKING
            if(!empty($doc['payment_account_type'])){
                $doc2['person_0_banking_0_paymentBankAccountType'] = _decrypt($doc['payment_account_type']);
            }
            if(!empty($doc['payment_routing_number'])){
                $doc2['person_0_banking_0_paymentBankRoutingNumber'] = _decrypt($doc['payment_routing_number']);
            }
            if(!empty($doc['payment_bank_account_number'])){
                $doc2['person_0_banking_0_createThing'] = "N";
                $doc2['person_0_banking_0_paymentBankAccountNumber'] = _decrypt($doc['payment_bank_account_number']);
            }
            // CC
            if( (!empty($doc['payment_credit_card_number'])) && (trim($doc['payment_credit_card_number']) <> "")) {
                if(!empty($doc['payment_credit_card_type'])){
                    $doc2['person_0_creditcard_0_paymentCreditCardType'] = _decrypt($doc['payment_credit_card_type']);
                }
                if(!empty($doc['payment_credit_card_name'])){
                    $doc2['person_0_creditcard_0_paymentNameOnCard'] = _decrypt($doc['payment_credit_card_name']);
                }
                if(!empty($doc['payment_credit_card_number'])){
                    $doc2['person_0_creditcard_0_createThing'] = "N"; 
                    $doc2['person_0_creditcard_0_paymentCardNumber'] = _decrypt($doc['payment_credit_card_number']);
                }
                if(!empty($doc['payment_credit_card_ccv'])){
                    $doc2['person_0_creditcard_0_paymentCCV'] = _decrypt($doc['payment_credit_card_ccv']);
                }
                if(!empty($doc['payment_credit_card_exp_month'])){
                    $doc2['person_0_creditcard_0_paymentCreditCardMonth'] = _decrypt($doc['payment_credit_card_exp_month']);
                }
                if(!empty($doc['payment_credit_card_exp_year'])){
                    $doc2['paymentCreditCardYear'] = _decrypt($doc['payment_credit_card_exp_year']);
                }
            }
            if(!empty($doc['policies'])){
                if(is_array($doc['policies'])){
                    foreach($doc['policies'] as $k=>$v){
                        if(!empty($v['policy_date_entered'])){
                            $doc2['person_0_policy_'.$k.'_timestampCreated'] = $v['policy_date_entered'];
                        }
                        if(!empty($v['policy_date_modified'])){
                            $doc2['person_0_policy_'.$k.'_timestampModified'] = $v['policy_date_modified'];
                        }
                        if(!empty($v['policy_created_by'])){
                            $doc2['person_0_policy_'.$k.'_createdBy'] = $v['policy_created_by'];
                        }
                        if(!empty($v['policy_modified_by'])){
                            $doc2['person_0_policy_'.$k.'_modifiedBy'] = $v['policy_modified_by'];
                        }
                        if(!empty($v['policy_carrier'])){
                            $doc2['person_0_policy_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_policy_'.$k.'_carrier'] = $v['policy_carrier'];
                            $doc2['person_0_policy_'.$k.'_carrierName'] = $v['policy_carrier'];
                        }
                        if(!empty($v['policy_coverage_type'])){
                            $doc2['person_0_policy_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_policy_'.$k.'_coverageType'] = $v['policy_coverage_type'];
                            $doc2['person_0_policy_'.$k.'_coverageTypeName'] = $v['policy_coverage_type'];
                        }
                        if(!empty($v['policy_premium'])){
                            $doc2['person_0_policy_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_policy_'.$k.'_premiumMoney'] = $v['policy_premium'];
                        }
                        if(!empty($v['policy_number'])){
                            $doc2['person_0_policy_'.$k.'_createThing'] = "Y";
                            $doc2['person_0_policy_'.$k.'_policyNumber'] = $v['policy_number'];
                        }
                        if(!empty($v['policy_setup_fee'])){
                            $doc2['person_0_policy_'.$k.'_setupFeeMoney'] = $v['policy_setup_fee'];
                        }
                        if(!empty($v['policy_status'])){
                            $doc2['person_0_policy_'.$k.'_status'] = $v['policy_status'];
                        }
                        if(!empty($v['policy_subsidy_amount'])){
                            $doc2['person_0_policy_'.$k.'_subsidyMoney'] = $v['policy_subsidy_amount'];
                        }
                        if(!empty($v['policy_pay_schedule'])){
                            $doc2['person_0_policy_'.$k.'_paySchedule'] = $v['policy_pay_schedule'];
                        }
                        if(!empty($v['policy_effective_date'])){
                            $doc2['person_0_policy_'.$k.'_effectiveDate'] = $v['policy_effective_date'];
                        }
                        if(!empty($v['policy_submission_date'])){
                            $doc2['person_0_policy_'.$k.'_submissionDate'] = $v['policy_submission_date'];
                        }
                        if(!empty($v['policy_renewal_date'])){
                            $doc2['person_0_policy_'.$k.'_renewalDate'] = $v['policy_renewal_date'];
                        }
                        if(!empty($v['policy_term_date'])){
                            $doc2['person_0_policy_'.$k.'_termDate'] = $v['policy_term_date'];
                        }
                        if(!empty($v['policy_notes'])){
                            $doc2['person_0_policy_'.$k.'_notes'] = $v['policy_notes'];
                        }
                        if(!empty($v['policy_notes'])){
                            $doc2['person_0_policy_'.$k.'_notes'] = $v['policy_notes'];
                        }
                        if(!empty($v['policy_sold_by'])){
                            $doc2['person_0_policy_'.$k.'_soldBy'] = $v['policy_sold_by'];
                        }
                        if(!empty($v['policy_closed_by'])){
                            $doc2['person_0_policy_'.$k.'_closedBy'] = $v['policy_closed_by'];
                        }
                    }
                }
            }
            /*
            [policies] => Array
        (
            [0] => Array
                (
                    [policy_date_entered] => 2015-10-14 10:46:58
                    [policy_created_by] => bsdfbsdsf-wsdsdfgf-qwsdfdfgdgf1
                    [policy_date_modified] => 2015-10-14 10:46:58
                    [policy_modified_by] => bsdfbsdsf-wsdsdfgf-qwsdfdfgdgf1
                    [policy_number] => 
                    [policy_carrier] => HUMANA
                    [policy_setup_fee] => 
                    [policy_coverage_type] => MAJORMEDICALSILVER
                    [policy_status] => ACTIVE
                    [policy_premium] => 285.29
                    [policy_subsidy_amount] => 9.29
                    [policy_pay_schedule] => MONTHLY
                    [policy_effective_date] => 2015-11-01 00:00:00
                    [policy_submission_date] => 
                    [policy_renewal_date] => 
                    [policy_term_date] => 
                    [policy_sold_by] => BSDFBSDSF-WSDSDFGF-QWSDFDFGDGF1
                    [policy_closed_by] => ASDFSDF-WQWEFWEF-QWFEWQFWF1
                    [policy_notes] => 
                )
        )
        [status] => HOLD
[policyNumber] => tbd
[carrier] => k2wAEgVK-3bCgluZG-GACE5DkI
[coverageType] => q6IpwCcd-G72STAKN-0LH8J5J5
[setupFeeMoney] => 20
[premiumMoney] => 167.02
[subsidyMoney] => 0
[submissionDate] => 20151023000000
[renewalDate] => 
[effectiveDate] => 20151024000000
[termDate] => 
[soldBy] => 20151005134247-QtJ59VVa-8YmNLKzT
[closedBy] => 20151005143440-ZpL1bu2Q-0rKCOjTg
[notes] => 
        */
            $result['person'][] = $doc2;
            //debug($doc2 , "DOC2");
            //debug($doc, "DOC");
            $apiObj->mongoSetDB("ebrokercenterCRM");
            $apiObj->save_things($doc2);
        }
    } else {
        $result['person'] = array();   
    }
    exit();
});
$app->get('/userupdate', function () use ($app,$settings) {
    exit();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ephone");
    $apiObj->mongoSetCollection("users");
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    $usedEmails = array();
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $post = array();
            $post['user_0_createThing'] = "Y";
            $post['user_0_id'] = $doc['_id'];
            $post['user_0_firstname'] = $doc['first_name'];
            $post['user_0_lastname'] = $doc['last_name'];
            $email = strtolower($doc['first_name'].".".$doc['last_name']."@allinsurancecenter.com");
            if(!in_array($email, $usedEmails)){
                $usedEmails[] = $email;
            } else {
                $email = strtolower($doc['first_name'].".".$doc['last_name'].".".$apiObj->getRandomString(3)."@allinsurancecenter.com");
                $usedEmails[] = $email;
            }
            $post['user_0_email'] = $email;
            $post['user_0_phone'] = $doc['phone_home'];
            $post['user_0_password'] = 'EBROKER2015';
            $post['user_0_agreeToTerms'] = 'Y';
            $post['user_0_status'] = 'INACTIVE';
            if((!empty($doc['agency_level'])) && ($doc['agency_level'] =='MANAGER')){
                $post['user_0_licensed'] = 'Y';
            } else {
                $post['user_0_licensed'] = 'N';
            }
            $post['user_0_canSell'] = 'Y';
            $post['user_0_permissionLevel'] = 'user';
            $post['user_0_agencyId']= "20151015105805-jc0i6h8l-k54x8HCg";
            $groups = array();
            $groups['agency_0_createThing'] = "Y";
            $groups['agency_0_id'] = "20151015105805-jc0i6h8l-k54x8HCg";
            $groups['agency_0_userGroups_0_createThing'] = "Y";
            $groups['agency_0_userGroups_0_id'] = "Mjeko3k2-gwkOp1p2K-nfmJKEKkw";
            $groups['agency_0_userGroups_0_label'] = "Rancho Cucamonga 2015";
            $groups['agency_0_userGroups_0_users_0_createThing'] = "N";
            $groups['agency_0_userGroups_0_users_0_userId'] = $doc['_id'];
            if((!empty($doc['agency_level'])) && ($doc['agency_level'] =='MANAGER')){
                $groups['agency_0_userGroups_0_users_0_level'] = 'MANAGER';
            } else {
                $groups['agency_0_userGroups_0_users_0_level'] = 'USER';
            }
            $apiObj->mongoSetDB("ebrokerTest2");
            $apiObj->save_things($post);
            $apiObj->save_things($groups);
            debug($post);
            debug($groups);
            //$m = new MOngoClient();
            //$db = $m->selectDB('ebrokerTest2');
            //$collection = $dbname;
            //$db->$collection->insert($doc);
        }
    }
    /*
[_id] => 20151005094323-eNsOqMnP-cfqe1KQL
[_timestampCreated] => 20151005094407
[_timestampModified] => 20151005094824
[_createdBy] => auSpFsFC-Hs4WQenx-qQJSbMud
[_modifiedBy] => auSpFsFC-Hs4WQenx-qQJSbMud
[_parentId] => 
[_parentThing] => 
[firstname] => DAVID
[lastname] => JACKSON
[email] => djackson@ebrokercenter.com
[phone] => 3238061314
[password] => $2y$10$ANEODOICMWIOCmi3234i2eToMDQ8h1dtMG8V7CT2YEfquGkR6p5We
[agreeToTerms] => Y
[status] => active
[licensed] => Y
[canSell] => Y
[permissionLevel] => administrator
*/
    /*
[_id] => 94eb0f37-29fc-c78d-71b4-54acdbe12401
[is_admin] => 0
[first_name] => KAITLYN
[middle_name] => 
[last_name] => GONZALES
[nickname] => 
[username] => KAITLYN.GONZALES
[department] => 
[title] => 
[status] => INACTIVE
[social_security_number] => 
[date_of_birth] => 
[email] => 
[email_alternative] => 
[notifications] => 1
[contact_instructions] => 
[phone_work] => 
[phone_mobile] => 
[phone_home] => 
[phone_other] => 
[phone_fax] => 
[address_street_1] => 
[address_street_2] => 
[address_city] => 
[address_state] => 
[address_postalcode] => 
[address_country] => 
[address_mailing_street_1] => 
[address_mailing_street_2] => 
[address_mailing_city] => 
[address_mailing_state] => 
[address_mailing_postalcode] => 
[address_mailing_country] => 
[national_producer_number] => 
[agency_level] => FRONTER
[agency] => 551d72774ec3be7f078552e2
[ga] => 
[mga] => 
[commission_level] => 
[password] => asdasdasdasdasddasasvasvVECpjqoArKXpEpmY2ym
*/
});
$app->get('/merge', function () use ($app,$settings) {
    exit();
    $apiObj = new apiclass($settings);
    $copyTables = array("history","agency","appointment","carrier", "carrierPlan", "formOptions","news","saveAll","sms","smsTemplate","systemForm","user","userGroups");
    $apiObj->mongoSetDB("ebrokercentermain");
    foreach($copyTables as $key=>$val){
        $dbname = $val;
        $apiObj->mongoSetCollection($dbname);
        $collectionQuery = false;
        $cursor = $apiObj->mongoFind($collectionQuery);
        if(!empty($cursor)){
            foreach (iterator_to_array($cursor) as $doc) {
                $m = new MOngoClient();
                $db = $m->selectDB('ebrokerTest2');
                $collection = $dbname;
                $db->$collection->insert($doc);
            }
        }
    }
    exit();
    $apiObj->mongoSetDB("ebrokercenterCRM");
    $dbname = "addresses";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
    $dbname = "emails";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
    $dbname = "history";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
    $dbname = "notes";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
    $dbname = "policy";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
    $dbname = "person";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ebrokerTest2');
            $collection = $dbname;
            $db->$collection->insert($doc);
        }
    }
});
$app->get('/fix', function () use ($app,$settings) {
    //   echo "here";
    exit();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($database['settings']);
    $apiObj->mongoSetDB($settings['database']);
    $userIds = $apiObj->getUserIds();
    $apiObj->mongoSetCollection("person");
    $collectionQuery = false;
    $collectionQuery['_timestampCreated']['$gt'] = '20150928155000';
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        $cursor->sort(array('_timestampCreated' => -1));
        $cursor->skip(300);
        $cursor->limit(100);
        foreach (iterator_to_array($cursor) as $doc) {
            $docIds[] = $doc['_id'];
            $result['person'][] = $apiObj->get_thing_display($doc);
        }
    } else {
        $result['person'] = array();   
    }
    //echo "<PRE>";
    //print_r(count($result['leads']));
    //echo "</PRE>";
    // Get Addresses
    $apiObj->mongoSetCollection("addresses");
    $collectionQuery = false;
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
        $result['policy'][] = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $result['policy'][] = $apiObj->get_thing_display($doc2);
        }
    }
    $apiObj->mongoSetCollection("notes");
    $collectionQuery = array('_parentId' => array('$in' => $docIds));
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $result['notes'][] = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $result['notes'][] = $apiObj->get_thing_display($doc2);
        }
    }
    //debug($result);
    $idcounter = array();
    foreach($result['person'] as $counter=>$person){
        foreach($person as $key=>$value){
            $post['person_'.$counter.'_createThing'] = "Y";
            $post['person_'.$counter.'_id'] = $person['_id'];
            $post['person_'.$counter."_timestampCreated"] = $person["_timestampCreated"];
            $post['person_'.$counter."_timestampModified"] = $person["_timestampModified"];
            $post['person_'.$counter."_created"] = $person["_timestampCreated"];
            $idcounter[$person['_id']] = $counter;
            if(!is_array($value)){
                $pos = strpos($key, "_");
                if ($pos === false) {
                    $post["person_".$counter."_".$key] = $value;
                } 
            } else {
                foreach($value as $key2=>$value2){
                    $post["person_0_".$key."_".$key2."_createThing"] = "N";
                    $post["person_0_".$key."_".$key2."_id"] = $value2["_id"];
                    $post["person_0_".$key."_".$key2."_timestampCreated"] = $value2["_timestampCreated"];
                    $post["person_0_".$key."_".$key2."_timestampModified"] = $value2["_timestampModified"];
                    foreach($value2 as $key3=>$value3){
                        $pos = strpos($key3, "_");
                        if ($pos === false) {
                            $post["person_".$counter."_".$key."_".$key2."_".$key3] = $value3;
                        }  
                    }
                }
            }
            $moreitems = array("addresses","phones", "emails", "notes", "policy");
            foreach($moreitems as $mik=>$miv){
                if(!empty($result[$miv] )){
                    foreach($result[$miv] as $key1=>$value1){
                        if($value1['_parentId'] == $person['_id']){
                            foreach($value1 as $key3=>$value3){
                                $post["person_".$counter."_".$miv."_".$key1."_createThing"] ="Y";
                                $post["person_".$counter."_".$miv."_".$key1."_id"] = $value1["_id"];
                                $post["person_".$counter."_".$miv."_".$key1."_timestampCreated"] = $value1["_timestampCreated"];
                                $post["person_".$counter."_".$miv."_".$key1."_timestampModified"] = $value1["_timestampModified"];
                                //$post["person_".$counter."_".$miv."_".$key1."_parentId"] = $value1['_parentId'];
                                $pos = strpos($key3, "_");
                                if ($pos === false) {
                                    $post["person_".$counter."_".$miv."_".$key1."_".$key3] = $value3;
                                }  
                            }
                        }
                    }
                }
            }
        }
    }
    debug($post);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->save_things($post);
    exit();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("person");
    $cursor = $apiObj->mongoFind();
    if(!empty($cursor)){
        $cursor->sort(array('_timestampCreated' => -1));
        $cursor->limit(20);
        foreach (iterator_to_array($cursor) as $doc) {
            debug($doc , "Original ". $doc['_id']);
            foreach($doc as $key=>$val){
                if(is_array($val)){
                    foreach($val as $key2=>$val2){
                        foreach($val2 as $key3=>$val3){
                            $pos = strpos($key3, "_");
                            if ($pos === false) {
                                if(trim($val3) != ""){
                                    $new_doc[$key][$key2][$key3] = $val3;
                                }
                            }  
                        }
                    }
                } else {
                    $pos = strpos($key, "_");
                    if ($pos === false) {
                        if(trim($val) != ""){
                            $new_doc[$key] = $val;
                        }
                    } 
                }
            }
            debug($new_doc, "Copy ". $doc['_id']);
        }
    }
    exit();
});
$app->get('/cansell', function () use ($app,$settings) {
    exit();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB('ehealthbrokers');
    /*
    $options = [
                'cost' => 10,
                'salt' => $settings['password_salt']
            ];
            $value =  password_hash("manager123", PASSWORD_BCRYPT, $options);
    echo $value;
    */
    $dbname = "user";
    $apiObj->mongoSetCollection($dbname);
    $collectionQuery = false;
    $cursor = $apiObj->mongoFind($collectionQuery);
    $post['userGroups_0_label'] = "Sales";
    $post['userGroups_0_createThing'] = "Y";
    $i = 0;
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            $m = new MOngoClient();
            $db = $m->selectDB('ehealthbrokers');
            $collection = "userGroups";
            $post['userGroups_0_users_'.$i.'_createThing'] = 'N';
            $post['userGroups_0_users_'.$i.'_userId'] = $doc['_id'];
            $post['userGroups_0_users_'.$i.'_level'] = 'USER';
            $i++;
            /*
            $newdata = array('$set' => array("canSell" => "Y"));
            $db->$collection->update(array("_id" => $doc['_id']), $newdata);
            echo $doc['_id'];
            */
        }
    }
    $apiObj->save_things($post);
    echo "<PRE>";
    print_r($post);
});
$app->get('/saveallpost', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($database['settings']);
    $post = array (
        'person_0_createThing' => 'Y',
        'person_0_id' => '20151103132758-wFa2xnao-1ZHJN0b1',
        'person_0_title' => '',
        'person_0_firstName' => 'Testing ',
        'person_0_middleName' => '',
        'person_0_lastName' => 'Test',
        'person_0_suffix' => '',
        'person_0_gender' => 'F',
        'person_0_socialSecurityNumber' => '',
        'person_0_dateOfBirth' => '11/25/1954',
        'person_0_smokerTabacco' => 'N',
        'person_0_assignedTo' => '20151030143327-MTcvgYel-XDuPq3k9',
        'person_0_disposition' => 'GOODLEAD',
        'person_0_leadSource' => '',
        'person_0_phones_0_createThing' => 'Y',
        'person_0_phones_0_id' => '20151103163129-63J6RrIM-zhIZtpx2',
        'person_0_phones_0_phoneNumber' => '(760) 902-2211',
        'person_0_phones_0_phoneType' => 'MOBILE',
        'person_0_emails_0_createThing' => 'Y',
        'person_0_emails_0_id' => '20151103163129-nci8bqt3-YDkJQjRH',
        'person_0_emails_0_email' => 'test@ehealthbrokers.com',
        'person_0_emails_0_type' => '',
        'person_0_addresses_0_createThing' => 'Y',
        'person_0_addresses_0_id' => '20151112115108-cXzSjJhy-K6hsVrUU',
        'person_0_addresses_0_street1' => '',
        'person_0_addresses_0_street2' => '',
        'person_0_addresses_0_city' => '',
        'person_0_addresses_0_state' => '',
        'person_0_addresses_0_zipCode' => '',
        'person_0_addresses_0_county' => '',
        'person_0_taxes_0_createThing' => 'N',
        'person_0_taxes_0_id' => '20151103132758-Nt9sWXFx-ZQ6SdBec',
        'person_0_taxes_0_employmentStatus' => 'EMPLOYED',
        'person_0_taxes_0_incomeYear' => '2016',
        'person_0_taxes_0_estimatedFollowingIncome' => '19000',
        'person_0_taxes_0_estimatedYearlyIncome' => '19000',
        'person_0_taxes_0_planToFileTaxes' => 'Y',
        'person_0_taxes_0_fileTaxesJointly' => 'N',
        'person_0_taxes_0_taxesClaimDependents' => 'N',
        'person_0_taxes_0_taxesAreYourADependent' => '',
        'person_0_incomeSources_0_createThing' => 'N',
        'person_0_incomeSources_0_id' => '20151112115108-mkLU16Ui-Dz4lp0g7',
        'person_0_incomeSources_0_incomeMoney' => '',
        'person_0_incomeSources_0_incomeType' => '',
        'person_0_incomeSources_0_incomeFrequency' => '',
        'person_0_employers_0_createThing' => 'N',
        'person_0_employers_0_id' => '20151112115108-7lb8coB7-2FyLc1DP',
        'person_0_employers_0_name' => '',
        'person_0_employers_0_phone' => '',
        'person_0_employers_0_address' => '',
        'person_0_employers_0_city' => '',
        'person_0_employers_0_state' => '',
        'person_0_employers_0_zipcode' => '',
        'person_0_employers_0_wages' => '',
        'person_0_employers_0_payFrequency' => '',
        'person_0_employers_0_hoursWeekly' => '',
        'person_0_spouse_0_createThing' => 'N',
        'person_0_spouse_0_id' => '20151112115108-p06gNo9N-4zzFqw7Q',
        'person_0_spouse_0_spouseTitle' => '',
        'person_0_spouse_0_spouseFirstName' => '',
        'person_0_spouse_0_spouseMiddleName' => '',
        'person_0_spouse_0_spouseLastName' => '',
        'person_0_spouse_0_spouseSuffix' => '',
        'person_0_spouse_0_spouseGender' => '',
        'person_0_spouse_0_spouseSocialSecurityNumber' => '',
        'person_0_spouse_0_spouseDateOfBirth' => '',
        'person_0_spouse_0_spouseSmoker' => '',
        'person_0_dependents_0_createThing' => 'N',
        'person_0_dependents_0_id' => '20151112115108-FpEAKulI-uvZPR4it',
        'person_0_dependents_0_dependentsFirstName' => '',
        'person_0_dependents_0_dependentsLastName' => '',
        'person_0_dependents_0_dependentsSocialSecurityNumber' => '',
        'person_0_dependents_0_dependentsDateOfBirth' => '',
        'person_0_policy_0_createThing' => 'Y',
        'person_0_policy_0_id' => '20151105074956-aIiYUowA-mE9M7fuG',
        'person_0_policy_0_status' => 'HOLD',
        'person_0_policy_0_policyNumber' => '12345',
        'person_0_policy_0_carrier' => 'ArcmrtyW-dtzzIWnM-iPIQZHTn',
        'person_0_policy_0_coverageType' => '5iq2ur1A-76qNlGxC-lYSs3Czt',
        'person_0_policy_0_setupFeeMoney' => '123.00',
        'person_0_policy_0_premiumMoney' => '123.00',
        'person_0_policy_0_subsidyMoney' => '123.00',
        'person_0_policy_0_submissionDate' => '11/05/2015',
        'person_0_policy_0_renewalDate' => '11/05/2015',
        'person_0_policy_0_effectiveDate' => '11/05/2015',
        'person_0_policy_0_termDate' => '11/05/2015',
        'person_0_policy_0_soldBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
        'person_0_policy_0_closedBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
        'person_0_policy_0_notes' => '',
        'person_0_policy_1_createThing' => 'Y',
        'person_0_policy_1_id' => '20151105075010-tMoKmeuV-dUZv1zB0',
        'person_0_policy_1_status' => 'SOLD',
        'person_0_policy_1_policyNumber' => '55443322',
        'person_0_policy_1_carrier' => 'k2wAEgVK-3bCgluZG-GACE5DkI',
        'person_0_policy_1_coverageType' => 'udeDvT6l-oRONXA5Q-y79efOcH',
        'person_0_policy_1_setupFeeMoney' => '',
        'person_0_policy_1_premiumMoney' => '22.33',
        'person_0_policy_1_subsidyMoney' => '12.22',
        'person_0_policy_1_submissionDate' => '11/05/2015',
        'person_0_policy_1_renewalDate' => '11/05/2015',
        'person_0_policy_1_effectiveDate' => '11/05/2015',
        'person_0_policy_1_termDate' => '11/05/2015',
        'person_0_policy_1_soldBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
        'person_0_policy_1_closedBy' => '20151005154138-k7N1dHZi-4I7ZoB2J',
        'person_0_policy_1_notes' => '',
        'person_0_policy_2_id' => '20151112114853-uL4y7YBw-lgfNPo8r',
        'person_0_policy_2_deleteThing' => 'Y',
        'person_0_policy_2_createThing' => 'Y',
        'person_0_policy_3_createThing' => 'Y',
        'person_0_policy_3_id' => '20151112115117-mdMIwG5T-bKwmSkD0',
        'person_0_policy_3_status' => 'HOLD',
        'person_0_policy_3_policyNumber' => '123',
        'person_0_policy_3_carrier' => 'URR6sxdK-iMtjVGL9-Lw9Lx240',
        'person_0_policy_3_coverageType' => 'Mcv1iVm5-LLixKVnc-iYkc9EmI',
        'person_0_policy_3_setupFeeMoney' => '',
        'person_0_policy_3_premiumMoney' => '',
        'person_0_policy_3_subsidyMoney' => '',
        'person_0_policy_3_submissionDate' => '',
        'person_0_policy_3_renewalDate' => '11/12/2015',
        'person_0_policy_3_effectiveDate' => '11/11/2015',
        'person_0_policy_3_termDate' => '',
        'person_0_policy_3_soldBy' => '',
        'person_0_policy_3_closedBy' => '',
        'person_0_policy_3_notes' => 'TEST',
        'person_0_notes_0_createThing' => 'Y',
        'person_0_notes_0_information' => '',
        'person_0_notes_0_id' => '20151112115108-1L9R5aOB-67VbAkt4',
        'person_0_banking_0_createThing' => 'N',
        'person_0_banking_0_id' => '20151112115108-Sxta19Yo-l6c56sr1',
        'person_0_banking_0_paymentBankName' => '',
        'person_0_banking_0_paymentBankAccountType' => '',
        'person_0_banking_0_paymentBankRoutingNumber' => '',
        'person_0_banking_0_paymentBankAccountNumber' => '',
        'person_0_creditcard_0_createThing' => 'N',
        'person_0_creditcard_0_id' => '20151112115108-VqDzXMiR-JMdpsbrq',
        'person_0_creditcard_0_paymentCreditCardType' => '',
        'person_0_creditcard_0_paymentNameOnCard' => '',
        'person_0_creditcard_0_paymentCardNumber' => '',
        'person_0_creditcard_0_paymentCCV' => '',
        'person_0_creditcard_0_paymentCreditCardMonth' => '',
        'person_0_creditcard_0_paymentCreditCardYear' => '',
        'person_0_policy_adminTab_0_createThing' => 'N',
        'person_0_policy_adminTab_0_id' => '20151112115108-5igjzXcY-4loV15E0',
        'person_0_policy_adminTab_0_submissionVerified' => '',
        'person_0_policy_adminTab_0_carrierBackOffice' => '',
        'person_0_policy_adminTab_0_underwritingDisposition' => '',
        'person_0_policy_0_submissionVerified' => '',
        'person_0_policy_0_inCarrierBackOffice' => '',
        'person_0_policy_0_adminDisposition' => '',
        'person_0_policy_0_factoredAmountMoney' => '',
        'person_0_policy_0_commissionRecieved' => '',
        'person_0_policy_0_commissionAmountMoney' => '',
        'person_0_policy_adminTab_1_createThing' => 'N',
        'person_0_policy_adminTab_1_id' => '20151112115108-FTsj3pSv-Q1nzJAXU',
        'person_0_policy_adminTab_1_submissionVerified' => '',
        'person_0_policy_adminTab_1_carrierBackOffice' => '',
        'person_0_policy_adminTab_1_underwritingDisposition' => '',
        'person_0_policy_1_submissionVerified' => '',
        'person_0_policy_1_inCarrierBackOffice' => '',
        'person_0_policy_1_adminDisposition' => '',
        'person_0_policy_1_factoredAmountMoney' => '',
        'person_0_policy_1_commissionRecieved' => '',
        'person_0_policy_1_commissionAmountMoney' => '',
        'person_0_policy_adminTab_2_createThing' => 'N',
        'person_0_policy_adminTab_2_id' => '20151112115108-ghB7G1zc-3Sz4YZfp',
        'person_0_policy_adminTab_2_submissionVerified' => '',
        'person_0_policy_adminTab_2_carrierBackOffice' => '',
        'person_0_policy_adminTab_2_underwritingDisposition' => '',
        'person_0_policy_2_submissionVerified' => '',
        'person_0_policy_2_inCarrierBackOffice' => '',
        'person_0_policy_2_adminDisposition' => '',
        'person_0_policy_2_factoredAmountMoney' => '',
        'person_0_policy_2_commissionRecieved' => '',
        'person_0_policy_2_commissionAmountMoney' => '',
    );
    debug($post);
    $thing_array =  $apiObj->parse_post($post);
    debug($thing_array);
    $apiObj->create_thing_array($thing_array);
    debug($apiObj->things);
    //$apiObj->save_things($post);
    exit();
    exit();
});
$app->get('/emptynote', function () use ($app,$settings) {
   echo "here";
        $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("notes");
   // $collectionQuery['information']['$exists'] = FALSE;
     $collectionQuery['information']['$eq'] = "";
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
    } else {
        echo $cursor2->count();
        foreach (iterator_to_array($cursor2) as $doc2) {
            debug($doc2);
               // $collectionQuery = array("_id"=>$doc2["_id"]);
				//$apiObj->mongoRemove($collectionQuery);
        }
    }
    exit();
});
$app->get('/leadget', function () use ($app,$settings) {
    $xml = file_get_contents('leadpost.xml');
    try {
        $leadinfo = new SimpleXMLElement($xml);
    } catch (Exception $e) {
    }
      $url =  "http://97.93.171.182/vicidial/non_agent_api.php?source=AllWebLead&user=1099&pass=463221&function=add_lead&phone_code=1&list_id=995&dnc_check=N";
    if(!empty($leadinfo->ZipCode)){
        echo $leadinfo->ZipCode;
    }
    if(!empty($leadinfo->ContactInfo->FirstName)){
        echo $leadinfo->ContactInfo->FirstName;
        $url .= "&first_name=".$leadinfo->ContactInfo->FirstName;
    }
    if(!empty($leadinfo->ContactInfo->LastName)){
        echo $leadinfo->ContactInfo->LastName;
        $url .= "&last_name=".$leadinfo->ContactInfo->LastName;
    }
    if(!empty($leadinfo->ContactInfo->Address)){
        echo $leadinfo->ContactInfo->Address;
         $url .= "&address1=".urlencode($leadinfo->ContactInfo->Address);
    }
    if(!empty($leadinfo->ContactInfo->ZipCode)){
        echo $leadinfo->ContactInfo->ZipCode;
         $url .= "&postal_code=".$leadinfo->ContactInfo->ZipCode;
    }
    if(!empty($leadinfo->ContactInfo->City)){
        echo $leadinfo->ContactInfo->City;
         $url .= "&city=".$leadinfo->ContactInfo->City;
    }
    if(!empty($leadinfo->ContactInfo->County)){
        echo $leadinfo->ContactInfo->County;
    }
    if(!empty($leadinfo->ContactInfo->State)){
        echo $leadinfo->ContactInfo->State;
        $url .= "&state=".$leadinfo->ContactInfo->State;
    }
    if(!empty($leadinfo->ContactInfo->PhoneDay)){
        echo $leadinfo->ContactInfo->PhoneDay;
        $url .= "&phone_number=".$leadinfo->ContactInfo->PhoneDay;
    }
    if(!empty($leadinfo->ContactInfo->PhoneEve)){
        echo $leadinfo->ContactInfo->PhoneEve;
        if(empty($leadinfo->ContactInfo->PhoneDay)){
            $url .= "&phone_number=".$leadinfo->ContactInfo->PhoneEve;
        } else {
            $url .= "&alt_phone=".$leadinfo->ContactInfo->PhoneEve;
        } 
    }
    if(!empty($leadinfo->ContactInfo->PhoneCell)){
        echo $leadinfo->ContactInfo->PhoneCell;
        if(empty($leadinfo->ContactInfo->PhoneDay)){
            $url .= "&alt_phone=".$leadinfo->ContactInfo->PhoneCell;
        } 
    }
    if(!empty($leadinfo->ContactInfo->Email)){
        echo $leadinfo->ContactInfo->Email;
         $url .= "&email=".urlencode($leadinfo->ContactInfo->Email);
    }
    if(!empty($leadinfo->ContactInfo->Comment)){
        echo $leadinfo->ContactInfo->Comment;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->DOB)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->DOB;
        $url .= "&date_of_birth=".$leadinfo->HealthInsurance->ApplicantInfo->DOB;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Gender)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Gender;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Height_FT)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Height_FT;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Height_IN)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Height_IN;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Weight)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Weight;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Tobacco)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Tobacco;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Occupation)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->Occupation;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->USResidence)){
        echo $leadinfo->HealthInsurance->ApplicantInfo->USResidence;
    }
    if(!empty($leadinfo->HealthInsurance->SelfEmployed)){
        echo $leadinfo->HealthInsurance->SelfEmployed;
    }
    if(!empty($leadinfo->HealthInsurance->DUI)){
        echo $leadinfo->HealthInsurance->DUI;
    }
    if(!empty($leadinfo->HealthInsurance->ExpectantMother)){
        echo $leadinfo->HealthInsurance->ExpectantMother;
    }
   if(!empty($leadinfo->HealthInsurance->Dependents->Dependent)){
        foreach ($leadinfo->HealthInsurance->Dependents->Dependent as $key=>$var){
                       echo "<P>Dependent " . $key;             
             if(!empty($var->DOB)){
                 echo $key. " - " .$var->DOB;
             }
              if(!empty($var->Gender)){
                 echo $key. " - " . $var->Gender;
             }
              if(!empty($var->Height_FT)){
                 echo $key. " - " .$var->Height_FT;
             }
              if(!empty($var->Height_IN)){
                 echo $key. " - " . $var->Height_IN;
             }
              if(!empty($var->Weight)){
                 echo $key. " - " . $var->Weight;
             }
              if(!empty($var->Tobacco)){
                 echo $key. " - " . $var->Tobacco;
             }
              if(!empty($var->DependentType)){
                 echo $key. " - " . $var->DependentType;
             }
              if(!empty($var->Student)){
                 echo $key. " - " . $var->Student;
             }
        }
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Relative_Heart)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Relative_Heart;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Relative_Cancer)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Relative_Cancer;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Medication)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Medication;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Medical_Treatment)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Medical_Treatment;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Hospital)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Hospital;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Comments)){
        echo $leadinfo->HealthInsurance->MedicalHistory->Comments;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->AIDS_HIV)){
        echo $leadinfo->HealthInsurance->MajorMedical->AIDS_HIV;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Alcohol_Drug_Abuse)){
        echo $leadinfo->HealthInsurance->MajorMedical->Alcohol_Drug_Abuse;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Alzheimers_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Alzheimers_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Asthma)){
        echo $leadinfo->HealthInsurance->MajorMedical->Asthma;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Cancer)){
        echo $leadinfo->HealthInsurance->MajorMedical->Cancer;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Cholesterol)){
        echo $leadinfo->HealthInsurance->MajorMedical->Cholesterol;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Depression)){
        echo $leadinfo->HealthInsurance->MajorMedical->Depression;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Diabetes)){
        echo $leadinfo->HealthInsurance->MajorMedical->Diabetes;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Heart_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Heart_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->High_Blood_Pressure)){
        echo $leadinfo->HealthInsurance->MajorMedical->High_Blood_Pressure;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Kidney_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Kidney_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Liver_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Liver_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Mental_Illness)){
        echo $leadinfo->HealthInsurance->MajorMedical->Mental_Illness;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Pulmonary_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Pulmonary_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Stroke)){
        echo $leadinfo->HealthInsurance->MajorMedical->Stroke;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Ulcer)){
        echo $leadinfo->HealthInsurance->MajorMedical->Ulcer;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Vascular_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Vascular_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Other_Major_Disease)){
        echo $leadinfo->HealthInsurance->MajorMedical->Other_Major_Disease;
    }
     if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentlyInsured)){
        echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentlyInsured;
    }
      if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Carrier)){
        echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Carrier;
    }
    if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Expiration)){
        echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Expiration;
    }
    if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->InsuredSince)){
        echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->InsuredSince;
    }
 if(!empty($leadinfo->HealthInsurance->RequestedCoverage)){
        echo $leadinfo->HealthInsurance->RequestedCoverage;
    }
    echo $url;
        //    $url =  "http://97.93.171.182/vicidial/non_agent_api.php?source=AllWebLead&user=1099&pass=463221&function=add_lead&phone_number=5555551010&phone_code=1&list_id=995&dnc_check=N&first_name=Bob&last_name=Wilson&address1=1234+Main+St.&city=Chicago+Heights&state=IL&postal_code=60606";
/*
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HEADER, TRUE); 
            curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
            $head = curl_exec($ch); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
*/
    debug($leadinfo);
});
$app->get('/laedcheck', function () use ($app,$settings) {
    $row = 1;
    $phones = array();
    if (($handle = fopen("leadstodate.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            $phones[] = preg_replace("/[^0-9,.]/", "", $data[7]); ;
            // for ($c=0; $c < $num; $c++) {
            //     echo $data[$c] . "<br />\n";
            // }
            $row++;
        }
        fclose($handle);
    }
    $persons = array();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("phones");
    $collectionQuery['phoneNumber']['$in'] = $phones;
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $persons[] = $doc2['_parentId'];
        }
    }
    $totalpolicies = 0;
    $totalpremium = 0;
    // debug($persons);
    $apiObj->mongoSetCollection("policy");
    $collectionQuery = array();
    $collectionQuery['_parentId']['$in'] = $persons;
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        echo "none";
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $totalpolicies++;
            echo "<P>";
            echo "Status: ". $doc2['status'];
            echo "<BR>Policy Number: ". $doc2['policyNumber'];
            echo "<BR>Premium Money: ". $doc2['premiumMoney'];
            echo "<BR>Subsidy Money: ". $doc2['subsidyMoney'];
            $totalpremium = $totalpremium + $doc2['premiumMoney'];
            //debug($doc2);
        }
    }
    echo "<P>Total Policies: ". $totalpolicies;
    echo "<P>Total Premium: ". $totalpremium;
});
$app->get('/checkupdate', function () use ($app,$settings) {



//$post['_id'] = "20151105161240-hq2o21vW-PHDTt6Zr";
$post['_timestampCreated'] = "20151105172941";
$post['_timestampModified'] = "20151208091924";
$post['_createdBy'] = "20151029145201-AgSZW5ZX-C7CHKY0d";
$post['_modifiedBy'] = "20151029145201-AgSZW5ZX-C7CHKY0d";
$post['_parentId'] = "20151105161240-e9A5WLr5-Ebu3eb8b";
$post['_parentThing'] = "person";
$post['status'] = "SOLD";
$post['policyNumber'] = "12/15";
$post['carrier'] = "g0416lwZ-S6PBlreM-48IS6BRO";
$post['coverageType'] = "f9tc2bTZ-H0P7mYrI-pMP0fMNW";
$post['setupFeeMoney'] = "";
$post['premiumMoney'] = "126.25";
$post['subsidyMoney'] = "288";
$post['submissionDate'] = "20151106000000";
$post['renewalDate'] = "";
$post['effectiveDate'] = "20160101000000";
$post['termDate'] = "";
$post['soldBy'] = "20151029145201-AgSZW5ZX-C7CHKY0d";
$post['closedBy'] = "20151029150130-6AH3pN72-YNBQmIjk";
$post['notes'] = "Hold payment until- 12/15/2015
Benefit / Amount
Premium
Dental
Plan Details | Plan Exclusions
$26.50
Accident Fixed-Benefit
Plan Details | Plan Exclusions
$15.60
Accident Medical Expense
Plan Details | Plan Exclusions
$23.80
Cancer and Heart/Stroke
Plan Exclusions | Plan Exclusions
$61.88
Shopping Cart
Cancer and Heart/Stroke
$25,000 	$61.88 	Remove
Dental
Intermediate Plan 	$26.50 	Remove
Total Monthly Premium 	$88.38 	   	
  Blue Cross and Blue Shield of Texas health insurance	
Blue Cross and Blue Shield of Texas
Blue Advantage Silver
Plan ID: 33602TX0460304 | 94 % Actuarial Value
Plan Type	Deductible	PCP Visit Copay	Max Out-Of-Pocket	Plan Details
HMO	$0	$10	$700	View
Compare
silver level plan silver / Reduced Cost
Original Monthly Premium $414.25
Your Monthly Premium $126.25";
$post['submitMainPerson'] = "SUBMIT";
$post['submitSpouse'] = "";
$post['submitDependents'] = "";
$post['dateToPay'] = "";

$m = new MongoClient();
$db = $m->selectDB('ehealthbrokers');
$collection = 'policy';

$db->$collection->update(array("_id" => "20151105161240-hq2o21vW-PHDTt6Zr"), $post);
 
exit();
    $post = array (
     
 'person_0_createThing' => 'Y',
  'person_0_id' => '20151217095636-qcUEPYbv-uuKroXOw',
  'person_0_title' => '',
  'person_0_firstName' => 'Samantha',
  'person_0_middleName' => '',
  'person_0_lastName' => 'Forrest',
  'person_0_suffix' => '',
  'person_0_gender' => 'F',
  'person_0_socialSecurityNumber' => '592512717',
  'person_0_dateOfBirth' => '04/03/1982',
  'person_0_smokerTabacco' => 'N',
  'person_0_assignedTo' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_disposition' => 'GOODLEAD',
  'person_0_leadSource' => 'REFERRAL',
  'person_0_phones_0_createThing' => 'Y',
  'person_0_phones_0_id' => '20151217095636-vfOOsdxm-xVRUNFFx',
  'person_0_phones_0_phoneNumber' => '(386) 218-6401',
  'person_0_phones_0_phoneType' => 'HOME',
  'person_0_emails_0_createThing' => 'Y',
  'person_0_emails_0_id' => '20151217095636-QsaIHo3E-Zy1sUg1V',
  'person_0_emails_0_email' => 'schamscham8211@yahoo.com',
  'person_0_emails_0_type' => 'PRIMARY',
  'person_0_emails_1_createThing' => 'Y',
  'person_0_emails_1_id' => '20151217102954-sBmZZyvx-6ide6zmG',
  'person_0_emails_1_email' => 'dlblackmon44@hotmail.com',
  'person_0_emails_1_type' => 'SECONDARY',
  'person_0_addresses_0_createThing' => 'Y',
  'person_0_addresses_0_id' => '20151217095636-w9tkOmny-sSgSbArr',
  'person_0_addresses_0_street1' => '2437 Alamanda Ave.',
  'person_0_addresses_0_street2' => '',
  'person_0_addresses_0_city' => 'Deltona',
  'person_0_addresses_0_state' => 'FL',
  'person_0_addresses_0_zipCode' => '32738',
  'person_0_addresses_0_county' => 'Volusia',
  'person_0_taxes_0_createThing' => 'N',
  'person_0_taxes_0_id' => '20151217095636-mO8kZG6z-LxxOWeha',
  'person_0_taxes_0_employmentStatus' => 'SELFEMPLOYED',
  'person_0_taxes_0_incomeYear' => '',
  'person_0_taxes_0_estimatedFollowingIncome' => '',
  'person_0_taxes_0_estimatedYearlyIncome' => '',
  'person_0_taxes_0_planToFileTaxes' => '',
  'person_0_taxes_0_fileTaxesJointly' => '',
  'person_0_taxes_0_taxesClaimDependents' => '',
  'person_0_taxes_0_taxesAreYourADependent' => '',
  'person_0_incomeSources_0_createThing' => 'N',
  'person_0_incomeSources_0_id' => '20151217110848-OseQjCdX-ZA97qe5i',
  'person_0_incomeSources_0_personOfIncome' => '',
  'person_0_incomeSources_0_incomeMoney' => '',
  'person_0_incomeSources_0_incomeType' => '',
  'person_0_incomeSources_0_incomeFrequency' => '',
  'person_0_employers_0_createThing' => 'N',
  'person_0_employers_0_id' => '20151217095636-GrH5uCOP-DOHLbxNX',
  'person_0_employers_0_personEmployed' => 'MAINPERSON',
  'person_0_employers_0_name' => '',
  'person_0_employers_0_phone' => '',
  'person_0_employers_0_address' => '',
  'person_0_employers_0_city' => '',
  'person_0_employers_0_state' => '',
  'person_0_employers_0_zipcode' => '',
  'person_0_employers_0_wages' => '25000',
  'person_0_employers_0_payFrequency' => '',
  'person_0_employers_0_hoursWeekly' => '',
  'person_0_spouse_0_createThing' => 'N',
  'person_0_spouse_0_id' => '20151217095636-yHm7hguj-i7s9oEj4',
  'person_0_spouse_0_spouseTitle' => 'MR',
  'person_0_spouse_0_spouseFirstName' => 'Daimond  ',
  'person_0_spouse_0_spouseMiddleName' => '',
  'person_0_spouse_0_spouseLastName' => 'Blackmon',
  'person_0_spouse_0_spouseSuffix' => '',
  'person_0_spouse_0_spouseGender' => 'M',
  'person_0_spouse_0_spouseSocialSecurityNumber' => '265772091',
  'person_0_spouse_0_spouseDateOfBirth' => '01/14/1977',
  'person_0_spouse_0_spouseSmoker' => 'N',
  'person_0_dependents_0_createThing' => 'N',
  'person_0_dependents_0_id' => '20151217095636-saxas0a2-5kNax7gd',
  'person_0_dependents_0_dependentsFirstName' => 'David',
  'person_0_dependents_0_dependentsLastName' => 'Blackmon',
  'person_0_dependents_0_dependentsSocialSecurityNumber' => '671307474',
  'person_0_dependents_0_dependentsDateOfBirth' => '07/14/2006',
  'person_0_dependents_0_gender' => 'M',
  'person_0_dependents_1_createThing' => 'N',
  'person_0_dependents_1_id' => '20151217100210-TH64aJ8n-VvhzsWm2',
  'person_0_dependents_1_dependentsFirstName' => 'Amanda',
  'person_0_dependents_1_dependentsLastName' => 'Blackmon',
  'person_0_dependents_1_dependentsSocialSecurityNumber' => '767025115',
  'person_0_dependents_1_dependentsDateOfBirth' => '03/31/2011',
  'person_0_dependents_1_gender' => 'F',
  'person_0_policy_0_createThing' => 'Y',
  'person_0_policy_0_id' => '20151217102733-tvzSx5bS-E6U8FzGi',
  'person_0_policy_0_status' => 'SOLD',
  'person_0_policy_0_policyNumber' => '',
  'person_0_policy_0_carrier' => '',
  'person_0_policy_0_coverageType' => '',
  'person_0_policy_0_setupFeeMoney' => '',
  'person_0_policy_0_premiumMoney' => '',
  'person_0_policy_0_subsidyMoney' => '',
  'person_0_policy_0_submissionDate' => '',
  'person_0_policy_0_renewalDate' => '',
  'person_0_policy_0_effectiveDate' => '',
  'person_0_policy_0_dateToPay' => '',
  'person_0_policy_0_soldBy' => '',
  'person_0_policy_0_closedBy' => '',
  'person_0_policy_0_submitMainPerson' => '',
  'person_0_policy_0_submitSpouse' => '',
  'person_0_policy_0_submitDependents' => '',
  'person_0_policy_0_notes' => 'Dental
Intermediate Plan 	$41.94 	Remove
Accident Medical Expense
$2,000 	$11.60 	Remove
Accident
Level 2 - 24 Hour Accident 	$15.75 	Remove
Cancer and Heart/Stroke
$25,000 	$28.70 	Remove
Total Monthly Premium 	$97.99',
  'person_0_policy_1_createThing' => 'Y',
  'person_0_policy_1_id' => '20151217095636-JPSuUL8Z-n4ndo7wD',
  'person_0_policy_1_status' => 'SOLD',
  'person_0_policy_1_policyNumber' => 'TBD',
  'person_0_policy_1_carrier' => 'upiJfOac-GiuhR8BC-Kvxve2ET',
  'person_0_policy_1_coverageType' => 'f9tc2bTZ-H0P7mYrI-pMP0fMNW',
  'person_0_policy_1_setupFeeMoney' => '',
  'person_0_policy_1_premiumMoney' => '562.00',
  'person_0_policy_1_subsidyMoney' => '526.20',
  'person_0_policy_1_submissionDate' => '12/17/2015',
  'person_0_policy_1_renewalDate' => '',
  'person_0_policy_1_effectiveDate' => '01/01/2016',
  'person_0_policy_1_dateToPay' => '12/17/2015',
  'person_0_policy_1_soldBy' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_policy_1_closedBy' => '20151111093233-PJ87HWoK-NjobuTyD',
  'person_0_policy_1_submitMainPerson' => 'SUBMIT',
  'person_0_policy_1_submitSpouse' => 'SUBMIT',
  'person_0_policy_1_submitDependents' => 'EXCLUDE',
  'person_0_policy_1_notes' => 'Humana Â· Humana Silver 3800/Volusia HUMx (HMOx)
Silver HMO | Plan ID: 35783FL1160032
Estimated monthly premium
$36
Premium before tax credit: $562
Deductible
$1,000 Estimated Family Total
Out-of-pocket maximum
$1,500 Estimated Family Total
Estimated total yearly costs
Your doctors, medical facilities, and prescription drugs

Beta
Copayments / Coinsurance

    Emergency room care: $150 Copay before deductible/20% Coinsurance after deductible
    Generic drugs: $6
    Primary doctor: $5
    Specialist doctor: $15',
  'person_0_policy_2_createThing' => 'Y',
  'person_0_policy_2_id' => '20151217102734-ktsEiT7r-bYKUFLDR',
  'person_0_policy_2_status' => 'SOLD',
  'person_0_policy_2_policyNumber' => 'TBD',
  'person_0_policy_2_carrier' => 'Qiq3PkVh-alqqsuz0-Il4lL80o',
  'person_0_policy_2_coverageType' => '8xtTZM6S-jnt7wUNu-B7MnKB96',
  'person_0_policy_2_setupFeeMoney' => '',
  'person_0_policy_2_premiumMoney' => '41.94',
  'person_0_policy_2_subsidyMoney' => '',
  'person_0_policy_2_submissionDate' => '12/17/2015',
  'person_0_policy_2_renewalDate' => '',
  'person_0_policy_2_effectiveDate' => '12/18/2015',
  'person_0_policy_2_dateToPay' => '12/17/2015',
  'person_0_policy_2_soldBy' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_policy_2_closedBy' => '20151111093233-PJ87HWoK-NjobuTyD',
  'person_0_policy_2_submitMainPerson' => 'SUBMIT',
  'person_0_policy_2_submitSpouse' => 'SUBMIT',
  'person_0_policy_2_submitDependents' => 'EXCLUDE',
  'person_0_policy_2_notes' => 'Dental
Intermediate Plan 	$41.94 	Remove
Accident Medical Expense
$2,000 	$11.60 	Remove
Accident
Level 2 - 24 Hour Accident 	$15.75 	Remove
Cancer and Heart/Stroke
$25,000 	$28.70 	Remove
Total Monthly Premium 	$97.99',
  'person_0_policy_3_createThing' => 'Y',
  'person_0_policy_3_id' => '20151217102734-YlsOvpgg-sWZLOAvy',
  'person_0_policy_3_status' => 'SOLD',
  'person_0_policy_3_policyNumber' => 'TBD',
  'person_0_policy_3_carrier' => 'Qiq3PkVh-alqqsuz0-Il4lL80o',
  'person_0_policy_3_coverageType' => 'i6dS31jr-eMtMQUuW-26eIYjXy',
  'person_0_policy_3_setupFeeMoney' => '',
  'person_0_policy_3_premiumMoney' => '11.60',
  'person_0_policy_3_subsidyMoney' => '',
  'person_0_policy_3_submissionDate' => '12/17/2015',
  'person_0_policy_3_renewalDate' => '',
  'person_0_policy_3_effectiveDate' => '12/18/2015',
  'person_0_policy_3_dateToPay' => '12/17/2015',
  'person_0_policy_3_soldBy' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_policy_3_closedBy' => '20151111093233-PJ87HWoK-NjobuTyD',
  'person_0_policy_3_submitMainPerson' => 'SUBMIT',
  'person_0_policy_3_submitSpouse' => 'SUBMIT',
  'person_0_policy_3_submitDependents' => 'EXCLUDE',
  'person_0_policy_3_notes' => 'Dental
Intermediate Plan 	$41.94 	Remove
Accident Medical Expense
$2,000 	$11.60 	Remove
Accident
Level 2 - 24 Hour Accident 	$15.75 	Remove
Cancer and Heart/Stroke
$25,000 	$28.70 	Remove
Total Monthly Premium 	$97.99',
  'person_0_policy_4_createThing' => 'Y',
  'person_0_policy_4_id' => '20151217102735-fWVPQu8E-16NqIqtO',
  'person_0_policy_4_status' => 'SOLD',
  'person_0_policy_4_policyNumber' => 'TBD',
  'person_0_policy_4_carrier' => 'Qiq3PkVh-alqqsuz0-Il4lL80o',
  'person_0_policy_4_coverageType' => 'uvxXbO2Q-9Tz7FV8R-pbWJngfC',
  'person_0_policy_4_setupFeeMoney' => '',
  'person_0_policy_4_premiumMoney' => '15.75',
  'person_0_policy_4_subsidyMoney' => '',
  'person_0_policy_4_submissionDate' => '12/17/2015',
  'person_0_policy_4_renewalDate' => '',
  'person_0_policy_4_effectiveDate' => '12/18/2015',
  'person_0_policy_4_dateToPay' => '12/17/2015',
  'person_0_policy_4_soldBy' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_policy_4_closedBy' => '20151111093233-PJ87HWoK-NjobuTyD',
  'person_0_policy_4_submitMainPerson' => 'SUBMIT',
  'person_0_policy_4_submitSpouse' => 'SUBMIT',
  'person_0_policy_4_submitDependents' => 'EXCLUDE',
  'person_0_policy_4_notes' => 'Dental
Intermediate Plan 	$41.94 	Remove
Accident Medical Expense
$2,000 	$11.60 	Remove
Accident
Level 2 - 24 Hour Accident 	$15.75 	Remove
Cancer and Heart/Stroke
$25,000 	$28.70 	Remove
Total Monthly Premium 	$97.99',
  'person_0_policy_5_createThing' => 'Y',
  'person_0_policy_5_id' => '20151217111338-Upr2uim2-M8Ri60YX',
  'person_0_policy_5_status' => 'SOLD',
  'person_0_policy_5_policyNumber' => '',
  'person_0_policy_5_carrier' => 'yBOwCg96-cG94JRf6-4ffCVYDZ',
  'person_0_policy_5_coverageType' => '72p0NTzV-XFI3Jolx-pTID4wsw',
  'person_0_policy_5_setupFeeMoney' => '15.00',
  'person_0_policy_5_premiumMoney' => '9.99',
  'person_0_policy_5_subsidyMoney' => '',
  'person_0_policy_5_submissionDate' => '12/17/2015',
  'person_0_policy_5_renewalDate' => '',
  'person_0_policy_5_effectiveDate' => '12/18/2015',
  'person_0_policy_5_dateToPay' => '12/17/2015',
  'person_0_policy_5_soldBy' => '20151030142516-8NkBbHap-TXo3oKLm',
  'person_0_policy_5_closedBy' => '20151111093233-PJ87HWoK-NjobuTyD',
  'person_0_policy_5_submitMainPerson' => 'SUBMIT',
  'person_0_policy_5_submitSpouse' => 'SUBMIT',
  'person_0_policy_5_submitDependents' => 'EXCLUDE',
  'person_0_policy_5_notes' => '',
  'person_0_notes_0_createThing' => 'Y',
  'person_0_notes_0_information' => '',
  'person_0_notes_0_id' => '20151217110848-JWUAVInL-37EmLBvf',
  'person_0_banking_0_createThing' => 'N',
  'person_0_banking_0_id' => '20151217110848-n0SbCcru-qKUnRY6a',
  'person_0_banking_0_paymentBankName' => '',
  'person_0_banking_0_paymentBankAccountType' => '',
  'person_0_banking_0_paymentBankRoutingNumber' => '',
  'person_0_banking_0_paymentBankAccountNumber' => '',
  'person_0_creditcard_0_createThing' => 'N',
  'person_0_creditcard_0_id' => '20151217095636-AIsSuum3-LfZwdEk3',
  'person_0_creditcard_0_paymentCreditCardType' => 'VISA',
  'person_0_creditcard_0_paymentNameOnCard' => 'Daimond L Blackmon',
  'person_0_creditcard_0_paymentCardNumber' => '4494355155266326',
  'person_0_creditcard_0_paymentCCV' => '782',
  'person_0_creditcard_0_paymentCreditCardMonth' => '11',
  'person_0_creditcard_0_paymentCreditCardYear' => '2017',
    );
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->save_things($post);
    debug($post);
    exit();
});
/*
    $userForm = array();
    $userForm['systemForm_0_createThing'] = "Y";
    $userForm['systemForm_0_thing'] = 'user';
    $userForm['systemForm_0_name'] = 'firstname';
    $userForm['systemForm_0_label'] = 'First Name';
    $userForm['systemForm_0_type'] = 'TEXT';
    $userForm['systemForm_0_row'] = '1';
    $userForm['systemForm_0_sort'] = '1';
    $userForm['systemForm_0_columns'] = '3';
    $userForm['systemForm_0_required'] = true;
    $userForm['systemForm_1_createThing'] = "Y";
    $userForm['systemForm_1_thing'] = 'user';
    $userForm['systemForm_1_name'] = 'lastname';
    $userForm['systemForm_1_label'] = 'Last Name';
    $userForm['systemForm_1_type'] = 'TEXT';
    $userForm['systemForm_1_row'] = '2';
    $userForm['systemForm_1_sort'] = '1';
    $userForm['systemForm_1_columns'] = '3';
    $userForm['systemForm_1_required'] = true;
    $userForm['systemForm_2_createThing'] = "Y";
    $userForm['systemForm_2_thing'] = 'user';
    $userForm['systemForm_2_name'] = 'email';
    $userForm['systemForm_2_label'] = 'Email';
    $userForm['systemForm_2_type'] = 'TEXT';
    $userForm['systemForm_2_row'] = '3';
    $userForm['systemForm_2_sort'] = '1';
    $userForm['systemForm_2_columns'] = '3';
    $userForm['systemForm_2_required'] = true;
    $userForm['systemForm_3_createThing'] = "Y";
    $userForm['systemForm_3_thing'] = 'user';
    $userForm['systemForm_3_name'] = 'phone';
    $userForm['systemForm_3_label'] = 'Phone NUmber';
    $userForm['systemForm_3_type'] = 'TEXT';
    $userForm['systemForm_3_row'] = '4';
    $userForm['systemForm_3_sort'] = '1';
    $userForm['systemForm_3_columns'] = '3';
    $userForm['systemForm_3_required'] = false;
    $userForm['systemForm_4_createThing'] = "Y";
    $userForm['systemForm_4_thing'] = 'user';
    $userForm['systemForm_4_name'] = 'password';
    $userForm['systemForm_4_label'] = 'Password';
    $userForm['systemForm_4_type'] = 'TEXT';
    $userForm['systemForm_4_row'] = '5';
    $userForm['systemForm_4_sort'] = '1';
    $userForm['systemForm_4_columns'] = '3';
    $userForm['systemForm_4_required'] = true;
    $userForm['systemForm_5_createThing'] = "Y";
    $userForm['systemForm_5_thing'] = 'user';
    $userForm['systemForm_5_name'] = 'agreeToTerms';
    $userForm['systemForm_5_label'] = 'Agree To Terms';
    $userForm['systemForm_5_type'] = 'SELECT';
    $userForm['systemForm_5_row'] = '6';
    $userForm['systemForm_5_sort'] = '1';
    $userForm['systemForm_5_columns'] = '3';
    $userForm['systemForm_5_required'] = true;
    $userForm['systemForm_5_options_0_createThing'] = "N";
    $userForm['systemForm_5_options_0_value'] = "Y";
    $userForm['systemForm_5_options_0_label'] = "Yes";
    $userForm['systemForm_5_options_0_default'] = "Y";
    $userForm['systemForm_5_options_1_createThing'] = "N";
    $userForm['systemForm_5_options_1_value'] = "N";
    $userForm['systemForm_5_options_1_label'] = "No";
    $userForm['systemForm_5_options_1_default'] = "N";
    $userForm['systemForm_6_createThing'] = "Y";
    $userForm['systemForm_6_thing'] = 'user';
    $userForm['systemForm_6_name'] = 'status';
    $userForm['systemForm_6_label'] = 'Status';
    $userForm['systemForm_6_type'] = 'SELECT';
    $userForm['systemForm_6_row'] = '7';
    $userForm['systemForm_6_sort'] = '1';
    $userForm['systemForm_6_columns'] = '3';
    $userForm['systemForm_6_required'] = true;
    $userForm['systemForm_6_options_0_createThing'] = "N";
    $userForm['systemForm_6_options_0_value'] = "active";
    $userForm['systemForm_6_options_0_label'] = "Active";
    $userForm['systemForm_6_options_0_default'] = "Y";
    $userForm['systemForm_6_options_1_createThing'] = "N";
    $userForm['systemForm_6_options_1_value'] = "inactive";
    $userForm['systemForm_6_options_1_label'] = "Inactive";
    $userForm['systemForm_6_options_1_default'] = "N";
    $apiObj->save_things($userForm);   
       */    
/*
    $userForm = array();
    $userForm['systemForm_5_createThing'] = "Y";
    $userForm['systemForm_5_id'] =  'z1kGQJQ8-eAW4OB8p-ILN55opI';
    $options =  array (
        '' => '',
       '2015' => '2015',
        '2016' => '2016',
        '2017' => '2017',
        '2018' => '2018',
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
        '2022' => '2022',
         '2023' => '2022',
         '2024' => '2022',
      );
    $idx = 0;
    foreach($options as $key=>$value){
        $userForm['systemForm_5_options_'.$idx.'_createThing'] = "N";
        $userForm['systemForm_5_options_'.$idx.'_value'] = $key;
        $userForm['systemForm_5_options_'.$idx.'_label'] = $value;
        $userForm['systemForm_5_options_'.$idx.'_active'] = "Y";
        if($idx == 0){
             $userForm['systemForm_5_options_'.$idx.'_default'] = "Y";
        } else {
            $userForm['systemForm_5_options_'.$idx.'_default'] = "N";   
        }
        $idx++;
    }
    $apiObj->save_things($userForm);     
    */
$app->get('/', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($database['settings']);
});
$app->get('/addfields', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("trentTest");
    exit();
    //echo $_SESSION['api']['user']['permissionLevel'];
    $post['systemForm_0_createThing'] = "Y";
    $post['systemForm_0_name'] = "personOfIncome";
    $post['systemForm_0_thing'] = "incomeSources";
    $post['systemForm_0_label'] = "Person of Income";
    $post['systemForm_0_type'] = 'SELECT';
    $post['systemForm_0_row'] = '1';
    $post['systemForm_0_sort'] = '1';
    $post['systemForm_0_columns'] = '3';
    $post['systemForm_0_required'] = false;
    $post['systemForm_0_formOptions'] = 'Y';
    $post['systemForm_0_options_0_createThing'] = 'N';
    $post['systemForm_0_options_0_value'] = '';
    $post['systemForm_0_options_0_label'] = '';
    $post['systemForm_0_options_0_default'] = 'Y';
    $post['systemForm_0_options_1_createThing'] = 'N';
    $post['systemForm_0_options_1_value'] = 'MAINPERSON';
    $post['systemForm_0_options_1_label'] = 'Main Person';
    $post['systemForm_0_options_1_default'] = 'N';
    $post['systemForm_0_options_2_createThing'] = 'N';
    $post['systemForm_0_options_2_value'] = 'SPOUSE';
    $post['systemForm_0_options_2_label'] = 'Spouse';
    $post['systemForm_0_options_2_default'] = 'N';
    $post['systemForm_0_options_3_createThing'] = 'N';
    $post['systemForm_0_options_3_value'] = 'DEPENDENT';
    $post['systemForm_0_options_3_label'] = 'Dependent';
    $post['systemForm_0_options_3_default'] = 'N';
    /*
    $post['systemForm_1_createThing'] = "Y";
    $post['systemForm_1_name'] = "submitDateConfirmed";
    $post['systemForm_1_thing'] = "policy";
    $post['systemForm_1_label'] = "Date Submitted Confirmed";
    $post['systemForm_1_type'] = 'DATE';
    $post['systemForm_1_row'] = '1';
    $post['systemForm_1_sort'] = '3';
    $post['systemForm_1_columns'] = '4';
    $post['systemForm_1_required'] = false;
    */
    /* 
    $post['systemForm_0_createThing'] = "Y";
    $post['systemForm_0_name'] = "submitMainPerson";
    $post['systemForm_0_thing'] = "policy";
    $post['systemForm_0_label'] = "Submit Main Person with Policy";
    $post['systemForm_0_type'] = 'SELECT';
    $post['systemForm_0_row'] = '6';
    $post['systemForm_0_sort'] = '1';
    $post['systemForm_0_columns'] = '4';
    $post['systemForm_0_required'] = false;
    $post['systemForm_0_formOptions'] = 'Y';
    $post['systemForm_0_options_0_createThing'] = 'N';
    $post['systemForm_0_options_0_value'] = '';
    $post['systemForm_0_options_0_label'] = '';
    $post['systemForm_0_options_0_default'] = 'Y';
    $post['systemForm_0_options_1_createThing'] = 'N';
    $post['systemForm_0_options_1_value'] = 'SUBMIT';
    $post['systemForm_0_options_1_label'] = 'Submit with Policy';
    $post['systemForm_0_options_1_default'] = 'N';
    $post['systemForm_0_options_2_createThing'] = 'N';
    $post['systemForm_0_options_2_value'] = 'EXCLUDE';
    $post['systemForm_0_options_2_label'] = 'Exclude From Policy';
    $post['systemForm_0_options_2_default'] = 'N';
    $post['systemForm_1_createThing'] = "Y";
    $post['systemForm_1_name'] = "submitSpouse";
    $post['systemForm_1_thing'] = "policy";
    $post['systemForm_1_label'] = "Submit Spouse with Policy";
    $post['systemForm_1_type'] = 'SELECT';
    $post['systemForm_1_row'] = '6';
    $post['systemForm_1_sort'] = '2';
    $post['systemForm_1_columns'] = '4';
    $post['systemForm_1_required'] = false;
    $post['systemForm_1_formOptions'] = 'Y';
    $post['systemForm_1_options_0_createThing'] = 'N';
    $post['systemForm_1_options_0_value'] = '';
    $post['systemForm_1_options_0_label'] = '';
    $post['systemForm_1_options_0_default'] = 'Y';
    $post['systemForm_1_options_1_createThing'] = 'N';
    $post['systemForm_1_options_1_value'] = 'SUBMIT';
    $post['systemForm_1_options_1_label'] = 'Submit with Policy';
    $post['systemForm_1_options_1_default'] = 'N';
    $post['systemForm_1_options_2_createThing'] = 'N';
    $post['systemForm_1_options_2_value'] = 'EXCLUDE';
    $post['systemForm_1_options_2_label'] = 'Exclude From Policy';
    $post['systemForm_1_options_2_default'] = 'N';
    $post['systemForm_2_createThing'] = "Y";
    $post['systemForm_2_name'] = "submitDependents";
    $post['systemForm_2_thing'] = "policy";
    $post['systemForm_2_label'] = "Submit Dependents with Policy";
    $post['systemForm_2_type'] = 'SELECT';
    $post['systemForm_2_row'] = '6';
    $post['systemForm_2_sort'] = '3';
    $post['systemForm_2_columns'] = '4';
    $post['systemForm_2_required'] = false;
    $post['systemForm_2_formOptions'] = 'Y';
    $post['systemForm_2_options_0_createThing'] = 'N';
    $post['systemForm_2_options_0_value'] = '';
    $post['systemForm_2_options_0_label'] = '';
    $post['systemForm_2_options_0_default'] = 'Y';
    $post['systemForm_2_options_1_createThing'] = 'N';
    $post['systemForm_2_options_1_value'] = 'SUBMIT';
    $post['systemForm_2_options_1_label'] = 'Submit with Policy';
    $post['systemForm_2_options_1_default'] = 'N';
    $post['systemForm_2_options_2_createThing'] = 'N';
    $post['systemForm_2_options_2_value'] = 'EXCLUDE';
    $post['systemForm_2_options_2_label'] = 'Exclude From Policy';
    $post['systemForm_2_options_2_default'] = 'N';
    */
    debug($post);
    $apiObj->save_things($post);
});
$app->get('/assignment', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("ebrokerTest2");
    $apiObj->mongoSetCollection("person");
    $collectionQuery['assignedTo']['$eq'] = "30dfc29a-d957-75ea-7b2e-54b92cfce3bd";
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('ebrokerTest2');
    $collection = 'person';
    foreach (iterator_to_array($cursor) as $doc) {
        if(!empty($doc['assignedTo'])){
            debug($doc);
            exit();
            $db->$collection->update(
                array("_id" => $doc['_id'] ),
                array('$set' => array('assignedTo' =>'20151030142517-RNHVwu6o-1Fm2KZoM'))
            );
        }
    }
});
$app->get('/mig', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB("MIGcrmFinal");
    $apiObj->mongoSetCollection("user");
    $collectionQuery = array();
    //  $collectionQuery['assignedTo']['$eq'] = "caskdjaskd-asddasas-qsdfsdfgdgf1";
    $cursor = $apiObj->mongoFind($collectionQuery);
    foreach (iterator_to_array($cursor) as $doc) {
        $users[strtoupper($doc['_id'])] = $doc['_id'];
    }
    debug($users);
    $apiObj->mongoSetDB("MIGcrmFinal");
    $apiObj->mongoSetCollection("policy");
    $collectionQuery = array();
    //  $collectionQuery['assignedTo']['$eq'] = "caskdjaskd-asddasas-qsdfsdfgdgf1";
    $cursor = $apiObj->mongoFind($collectionQuery);
    $m = new MOngoClient();
    $db = $m->selectDB('MIGcrmFinal');
    $collection = 'policy';
    foreach (iterator_to_array($cursor) as $doc) {
        // if(!empty($doc['assignedTo'])){
        debug($doc);
        echo "<P>".$users[$doc['soldBy']];
        echo "<P>".$users[$doc['closedBy']];
        //  exit();
        /*
            $db->$collection->update(
                array("_id" => $doc['_id'] ),
                array('$set' => array(
                               'soldBy' => $users[$doc['soldBy']],
                                'closedBy' => $users[$doc['closedBy']]
                ))
            );
          */
        //  }
    }
});
$app->map('/gohealth', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
?>    
<form method="post" action="https://www.brokeroffice.com/leads/leadImport.do">
    <input type="hidden" name="subscriber_id" value="118250">
    <input type="hidden" name="customer_number" value="405735">
    <input type="hidden" name="lead_type" value="Health">
    <!--<input type="hidden" name="lead_type" value=" Uninsurable Health">-->
    <?php 
    debug($_REQUEST);
    exit();
    /*
$goHealth['first_name'] = "";
$goHealth['last_name'] = "";
$goHealth['contact_time'] = "";
$goHealth['email'] = "";
$goHealth['phone'] = "";
$goHealth['phone2'] = "";
$goHealth['address']['1']['street1']  = "";
$goHealth['address']['1']['street2'] = "";
$goHealth['address']['1']['city'] = "";
$goHealth['address']['1']['state'] = "";
$goHealth['address']['1']['zip'] = "";
$goHealth['insured']['1']['currently_covered'] = "";
$goHealth['insured']['1']['current_medications_detail'] = "";
$goHealth['insured']['1']['dobMM'] = "";
$goHealth['insured']['1']['dobDD'] = "";
$goHealth['insured']['1']['dobYYYY'] = "";
$goHealth['insured']['1']['gender'] = "";
$goHealth['insured']['1']['health_conditions_detail'] = "";
$goHealth['insured']['1']['heightFT'] = "";
$goHealth['insured']['1']['heightIN'] = "";
$goHealth['insured']['1']['weight'] = "";
$goHealth['insured']['1']['smoker'] = "";
//<!-- SPOUSE -->
$goHealth['insured']['2']['dobMM'] = "";
$goHealth['insured']['2']['dobDD'] = "";
$goHealth['insured']['2']['dobYYYY'] = "";
$goHealth['insured']['2']['gender'] = "";
$goHealth['insured']['2']['heightFT'] = "";
$goHealth['insured']['2']['heightIN'] = "";
$goHealth['insured']['2']['weight'] = "";
$goHealth['insured']['2']['smoker'] = "";
//<!-- DEPENDENT -->
$goHealth['insured']['3']['dobMM'] = "";
$goHealth['insured']['3']['dobDD'] = "";
$goHealth['insured']['3']['dobYYYY'] = "";
$goHealth['insured']['3']['gender'] = "";
$goHealth['insured']['3']['heightFT'] = "";
$goHealth['insured']['3']['heightIN'] = "";
$goHealth['insured']['3']['weight'] = "";
$goHealth['insured']['3']['smoker'] = "";
$goHealth['ip_address'] = "";
$goHealth['affiliate_id'] = "";
$goHealth['link_id'] = "";
$goHealth['source'] = "";
    */
    /*
$goHealth['first_name'] = $_REQUEST['person_0_firstName'];
$goHealth['last_name'] = $_REQUEST['person_0_lastName'];
$goHealth['contact_time'] = "Morning";
$goHealth['email'] = $_REQUEST['person_0_emails_0_email'];
$goHealth['phone'] = $_REQUEST['person_0_emails_0_email'];
$goHealth['phone2'] = "";
$goHealth['address']['1']['street1']  = "123 Main St";
$goHealth['address']['1']['street2'] = "";
$goHealth['address']['1']['city'] = "Brea";
$goHealth['address']['1']['state'] = "CA";
$goHealth['address']['1']['zip'] = "92821";
$goHealth['insured']['1']['currently_covered'] = "No";
$goHealth['insured']['1']['current_medications_detail'] = "";
$goHealth['insured']['1']['dobMM'] = "3";
$goHealth['insured']['1']['dobDD'] = "2";
$goHealth['insured']['1']['dobYYYY'] = "1965";
$goHealth['insured']['1']['gender'] = "M";
$goHealth['insured']['1']['health_conditions_detail'] = "";
$goHealth['insured']['1']['heightFT'] = "6";
$goHealth['insured']['1']['heightIN'] = "2";
$goHealth['insured']['1']['weight'] = "215";
$goHealth['insured']['1']['smoker'] = "No";
//<!-- SPOUSE -->
$goHealth['insured']['2']['dobMM'] = "3";
$goHealth['insured']['2']['dobDD'] = "22";
$goHealth['insured']['2']['dobYYYY'] = "1974";
$goHealth['insured']['2']['gender'] = "F";
$goHealth['insured']['2']['heightFT'] = "5";
$goHealth['insured']['2']['heightIN'] = "10";
$goHealth['insured']['2']['weight'] = "165";
$goHealth['insured']['2']['smoker'] = "No";
//<!-- DEPENDENT -->
$goHealth['insured']['3']['dobMM'] = "4";
$goHealth['insured']['3']['dobDD'] = "3";
$goHealth['insured']['3']['dobYYYY'] = "2011";
$goHealth['insured']['3']['gender'] = "M";
$goHealth['insured']['3']['heightFT'] = "4";
$goHealth['insured']['3']['heightIN'] = "2";
$goHealth['insured']['3']['weight'] = "98";
$goHealth['insured']['3']['smoker'] = "No";
$goHealth['ip_address'] = "22.22.22.22";
$goHealth['affiliate_id'] = "1234";
$goHealth['link_id'] = "4321";
$goHealth['source'] = "Here";
    */
    foreach($goHealth as $key=>$val){
        if(!is_array($val)){
            echo "<P>".$key ." <input type='text' name='".$key."' value='".$val."'>";
        } else {
            foreach($val as $key2=>$val2){
                if(!is_array($val2)){
                    echo "<P>".$key."_".$key2 ." <input type='text' name='".$key."_".$key2."' value='".$val2."'>";
                } else {
                    foreach($val2 as $key3=>$val3){
                        if(!is_array($val3)){
                            echo "<P>".$key."_".$key2 ."_".$key3 ." <input type='text' name='".$key."_".$key2."_".$key3."' value='".$val3."'>";
                        } else {
                        }
                    }
                }
            }
        }
    }
    ?>
    <P></P> 
    <input type="submit" value="submit">
</form>
<?php
})->via('GET','POST');
$app->run();    