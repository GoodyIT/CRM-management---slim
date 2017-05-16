<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));




$app->map('/getagentstatus/:agent', function ($agent) use ($app,$settings) {
return true;
})->via('GET','POST');

// Allan Dev Here
$app->map('/setagentstatus/:agent/:status', function ($agent,$status) use ($app,$settings) {
	//var_dump($settings['vici']['pass']);
	if(!ctype_digit($agent)){ die('Invalid agent...'); }
	if(strtoupper($status) != "PAUSE" && strtoupper($status) != "RESUME"){ die("Invalid Status..."); } else { $status = strtoupper($status); }
	//$url = $settings['vici']['serverapi']."agent_api.php?function=version";
	$url = "{$settings['vici']['serverapi']}agent_api.php?source=thingCRM&user={$settings['vici']['user']}&pass={$settings['vici']['pass']}&agent_user={$agent}&function=external_pause&value={$status}";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$data = curl_exec($ch);
	echo($data);
	exit();

})->via('GET','POST');

// Last 100 check
$app->map('/last100', function () use ($app,$settings) {
    echo "Last 100";
    $link = mysqli_connect(
        $settings['vici']['dbipaddress'] ,  
        $settings['vici']['dbuser'] ,
        $settings['vici']['dbpass'],
        $settings['vici']['dbdatabase'],
        $settings['vici']['dbdport']
    );
    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    } 
    $stmt="	SELECT 
				vig.group_id,
				vig.group_name, 
				vid.did_description,
				vdl.did_id,
				vdl.did_route,
				vid.call_handle_method,
				vid.agent_search_method,
				vid.list_id,
				vid.campaign_id,
				vdl.uniqueid,
				vdl.channel,
				vdl.server_ip,
				vdl.caller_id_number,
				vdl.caller_id_name,
				vdl.extension,
				vdl.call_date,
				vdl.caller_id_number,
				vlist.user,
				vlist.title,
				vlist.first_name,
				vlist.middle_initial,
				vlist.last_name,
				vlist.address1,
				vlist.address2,
				vlist.address3,
				vlist.city,
				vlist.state,
				vlist.province,
				vlist.postal_code,
				vlist.country_code,
				vlist.gender,
				vlist.date_of_birth,
				vlist.alt_phone,
				vlist.email
			FROM 
				vicidial_did_log vdl
			LEFT JOIN
				vicidial_inbound_dids vid on vid.did_id = vdl.did_id
			LEFT JOIN
				vicidial_inbound_groups vig on vig.group_id = vid.group_id  
			LEFT JOIN
				vicidial_log vlog on vlog.uniqueid = vdl.uniqueid
			LEFT JOIN
				vicidial_list vlist on vlist.lead_id = vlog.lead_id
			WHERE 
				vdl.did_route = 'IN_GROUP'
            AND
				vdl.call_date > CAST('2015-12-28' AS DATETIME) and vdl.call_date < CAST('2015-12-29' AS DATETIME)
			ORDER BY
				vdl.call_date DESC
			LIMIT 
				100";
    $result = mysqli_query($link,$stmt);
    $output = array();
    while($row = $result->fetch_assoc())
    {
        echo "<PRE>";
        print_r($row);
        //foreach($row as $key=>$value){
        //	$a[$key] = $value;
        //}
        //array_push($output, $a);
    }
})->via('GET','POST');
//
//
// General Users
//
//


$app->map('/insureHcList', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("person");
    $dateString = date("Ymd");
    $collectionQuery['_timestampCreated']['$gt'] = ($dateString )."000000";
    $collectionQuery['_timestampCreated']['$lt'] = ($dateString )."245959";
    $cursor = $apiObj->mongoFind($collectionQuery);
    $cursor->sort(array('_timestampCreated' => -1));
    //$cursor->limit($limit);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {

            $apiObj->mongoSetCollection("policy");
            $collectionQuery['_parentId']['$eq'] = $doc['_id'];
            $cursor2 = $apiObj->mongoFind($collectionQuery);
            if(!empty($cursor2)){
                //debug($doc, "PERSON");
                foreach (iterator_to_array($cursor2) as $doc2) {
                   // debug($doc2, "POLICY");
                    if($doc2['status'] == "SOLD"){
                        $apiObj->mongoSetCollection("phones");
                        $collectionQuery['_parentId']['$eq'] = $doc['_id'];
                        $cursor3 = $apiObj->mongoFind($collectionQuery);
                        if(!empty($cursor3)){
                            foreach (iterator_to_array($cursor3) as $doc3) {
                                try {

                                    $url = $settings['vici']['serverapi']."non_agent_api.php?source=thingCRM&user=".$settings['vici']['user']."&pass=".$settings['vici']['pass']."&function=add_lead&list_id=992&phone_number=".$doc3['phoneNumber']."&first_name=".$doc2['firstName']."&last_name=".$doc2['lastName'];
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                    $data = curl_exec($ch);

                                } catch (Exception $e) {
                                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                                }
                                //debug($doc3, "PHONE");
                                break;
                            }
                        }
                        break;
                    }
                }
            }

        }
    }
})->via('GET','POST');


$app->map('/', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    if($apiObj->userLoggedIn()){
        if($settings['vici']['active'] === TRUE){
            try {
                $url = $settings['vici']['serverapi']."non_agent_api.php?source=test&user=".$settings['vici']['user'] ."&pass=".$settings['vici']['pass']."&function=user_group_status&user_groups=AGENTS&header=YES";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $data = curl_exec($ch);
                $call_data = explode("\n", $data);
                $call_headers = explode ("|",$call_data[0]);
                $call_numbers = explode ("|",$call_data[1]);
                $vici['usergroups'] = $call_numbers[0];
                $vici['calls_waiting'] = $call_numbers[1];
                $vici['agents_logged_in'] = $call_numbers[2];
                $vici['agents_in_calls'] = $call_numbers[3];
                $vici['agents_waiting'] = $call_numbers[4];
                $vici['agents_paused'] = $call_numbers[5];
                $vici['agents_in_dead_calls'] = $call_numbers[6];
                $vici['agents_in_dispo'] = $call_numbers[7];
            } catch (Exception $e) {
                $vici['usergroups'] = 0;
                $vici['calls_waiting'] = 0;
                $vici['agents_logged_in'] = 0;
                $vici['agents_in_calls'] = 0;
                $vici['agents_waiting'] = 0;
                $vici['agents_paused'] = 0;
                $vici['agents_in_dead_calls'] = 0;
                $vici['agents_in_dispo'] = 0;
            }
            try {
                //$url = "http://97.93.171.189/vicidial/non_agent_api.php?source=test&function=agent_stats_export&time_format=M&stage=pipe&user=1099&pass=463221&datetime_start=".date("Y", time() - 7 * 86400)."-".date("m",  time() - 7 * 86400)."-".date("d",  time() - 7 * 86400)."+00:00:00&datetime_end=".date("Y")."-".date("m")."-".date("d")."+23:59:59";
                $url = $settings['vici']['serverapi']."non_agent_api.php?source=test&function=agent_stats_export&time_format=M&stage=pipe&user=".$settings['vici']['user'] ."&pass=".$settings['vici']['pass'] ."&datetime_start=".date("Y")."-".date("m")."-".date("d")."+00:00:00&datetime_end=".date("Y")."-".date("m")."-".date("d")."+23:59:59";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $data = curl_exec($ch);
                $call_data = explode("\n", $data);
                foreach($call_data as $key=>$val){
                    $user_data = explode ("|",$val);
                    $viciusers[$key]['user'] = $user_data[0];
                    $viciusers[$key]['full_name'] = $user_data[1];
                    $viciusers[$key]['user_group'] = $user_data[2];
                    $viciusers[$key]['calls'] = $user_data[3];
                    $viciusers[$key]['login_time'] = $user_data[4];
                    $viciusers[$key]['total_talk_time'] = $user_data[5];
                    $viciusers[$key]['avg_talk_time'] = $user_data[6];
                    $viciusers[$key]['avg_wait_time'] = $user_data[7];
                    $viciusers[$key]['pct_of_queue'] = $user_data[8];
                    $viciusers[$key]['pause_time'] = $user_data[9];
                    $viciusers[$key]['sessions'] = $user_data[10];
                    $viciusers[$key]['avg_session'] = $user_data[11];
                    $viciusers[$key]['pauses'] = $user_data[12];
                    $viciusers[$key]['avg_pause_time'] = $user_data[13];
                    $viciusers[$key]['pause_pct'] = $user_data[14];
                    $viciusers[$key]['pauses_per_session'] = $user_data[15];
                }
                //print_r($viciusers);
            } catch (Exception $e) {
                $viciusers = false;
            }
            if($settings['vici']['active'] === TRUE){
                if( (!empty($_SESSION['api']['user']['permissionLevel'])) && ( (trim(strtoupper($_SESSION['api']['user']['permissionLevel'])) == "ADMINISTRATOR") || (trim(strtoupper($_SESSION['api']['user']['permissionLevel'])) ==  "MANAGER")) ){
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>VICI SERVER</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-danger m-r-sm">
                                        <?php echo $vici['calls_waiting']; ?>
                                    </button>
                                    Calls Waiting
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary m-r-sm">
                                        <?php echo $vici['agents_logged_in']; ?>
                                    </button>
                                    Agents Logged In
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info m-r-sm">
                                        <?php echo $vici['agents_in_calls'];?>
                                    </button>
                                    Agents In Calls
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-info m-r-sm">
                                        <?php echo $vici['agents_paused'];?>
                                    </button>
                                    Agents Paused
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success m-r-sm">
                                        <?php echo $vici['agents_in_dead_calls'];?>
                                    </button>
                                    Agents in Dead Calls
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger m-r-sm">
                                        <?php echo $vici['agents_in_dispo'];?>
                                    </button>
                                    Agents In Disposition
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>VICI AGENTS</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <table class="table">
                        <tbody>
                            <thead>
                                <th>
                                    Name
                                </th>
                                <th class='text-center'>
                                    Calls
                                </th>
                                <th class='text-center'>
                                    Login Time
                                </th>
                                <th class='text-center'>
                                    Total Talk Time
                                </th>
                                <th class='text-center'>
                                    Avg Talk Time
                                </th>
                            </thead>
                            <?php
                if(empty($viciusers)){
                    echo "<tr><td colspan='5'>No Calls Today</td></tr>";
                } else {
                    foreach($viciusers as $vKey=>$vVal){ ?>
                            <tr>
                                <td class='text-left'>
                                    <?php echo $vVal['full_name'];?>
                                </td>
                                <td class='text-center'>
                                    <?php echo $vVal['calls'];?>
                                </td>
                                <td class='text-center'>
                                    <?php echo $vVal['login_time'];?>
                                </td>
                                <td class='text-center'>
                                    <?php echo $vVal['total_talk_time'];?>
                                </td>
                                <td class='text-center'>
                                    <?php echo $vVal['avg_talk_time'];?>
                                </td>
                            </tr>
                            <?php } }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
            }
            }
        }
    }
})->via('GET','POST');





// Last 100 check
//$app->map('/leadsByDate/:date/:limit', function ($date,$limit) use ($app,$settings) {
$app->map('/leadsByDate', function () use ($app,$settings) {
    echo "Last 100";
    $link = mysqli_connect(
        $settings['vici']['dbipaddress'] ,
        $settings['vici']['dbuser'] ,
        $settings['vici']['dbpass'],
        $settings['vici']['dbdatabase'],
        $settings['vici']['dbdport']
    );
    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $apiObj->mongoSetCollection("phones");

   // $dateString = preg_replace("/[^0-9]/", "", $date);
    $dateString = date("Ymd");

    $collectionQuery['_timestampCreated']['$gt'] = ($dateString)."000000";
    $collectionQuery['_timestampCreated']['$lt'] = ($dateString)."245959";




    debug($collectionQuery);
    $cursor = $apiObj->mongoFind($collectionQuery);
    $cursor->sort(array('_timestampCreated' => -1));

    foreach (iterator_to_array($cursor) as $doc) {
        $phonenumbers[ltrim($doc['phoneNumber'],"1")] = $doc['_id'];
        $persons[$doc['_id']] = $doc['_parentId'];
    }
    //debug($phonenumbers);
    //debug($persons);

    /*
    BROKEROFFICE
    DATALOT
    REFERRAL
    BACKOFFICE
    STRATICS
    PRISIDIO
    ELEADGEN
    TOGETHERHEALTH
    DIALERWEBSITE
    IDAMARKETING
    HEALTHPOCKET
    RENEWAL
    ALLWEBLEADS


    $leadIds['10000'] = "ALLWEBLEADS";
    $leadIds['10000'] = "PRISIDIO";
    $leadIds['10002'] = "STRATICS";
    $leadIds['10003'] = "ELEADGEN";
    $leadIds['10004'] = "DATALOT";
    $leadIds['10005'] = "TOGETHERHEALTH";
    $leadIds['10006'] = "PITCHPERFECT";
    $leadIds['10007'] = "DIALERWEBSITE";
    $leadIds['10008'] = "HEALTHPOCKET";
    $leadIds['10009'] = "IDAMARKETING";
    $leadIds['10010'] = "BROKEROFFICE";
    $leadIds['10011'] = "BROKEROFFICELIVE";



    */

    $leadIds['10000'] = "ALLWEBLEADS";
    $leadIds['10001'] = "PRISIDIO";
    $leadIds['10002'] = "STRATICS";
    $leadIds['10003'] = "ELEADGEN";
    $leadIds['10004'] = "DATALOT";
    $leadIds['10005'] = "TOGETHERHEALTH";
    $leadIds['10006'] = "PITCHPERFECT";
    $leadIds['10007'] = "DIALERWEBSITE";
    $leadIds['10008'] = "NEXTGEN";
    $leadIds['10009'] = "IDAMARKETING";
    $leadIds['10010'] = "BROKEROFFICE";
    $leadIds['10011'] = "BROKEROFFICELIVE";
    $leadIds['10017'] = "VOICEMAILTRANSFER";
    $leadIds['10018'] = "ALLWEBLEADSCALL";
    $leadIds['10019'] = "DATALOT";
    $leadIds['10020'] = "ALLWEBLEADS";
    $leadIds['10022'] = "PRECISELEADS";

    $stmt="	SELECT
				vig.group_id,
				vig.group_name,
                vid.did_description,
				vdl.did_id,
				vdl.did_route,
				vid.call_handle_method,
				vid.agent_search_method,
				vid.list_id,
				vid.campaign_id,
				vdl.uniqueid,
				vdl.channel,
				vdl.server_ip,
				vdl.caller_id_number,
				vdl.caller_id_name,
				vdl.extension,
				vdl.call_date,
				vdl.caller_id_number

			FROM
				vicidial_did_log vdl
			LEFT JOIN
				vicidial_inbound_dids vid on vid.did_id = vdl.did_id
			LEFT JOIN
				vicidial_inbound_groups vig on vig.group_id = vid.group_id
			WHERE
				vdl.did_route = 'IN_GROUP'
            AND
				vdl.call_date > CAST('".date("Y-m-d")." 00:00:00' AS DATETIME) and vdl.call_date < CAST('".date("Y-m-d")." 23:59:59' AS DATETIME)
			ORDER BY
				vdl.call_date DESC
			";
    $result = mysqli_query($link,$stmt);
    $output = array();

    $total_entered = 0;
    $total_missing = 0;

    while($row = $result->fetch_assoc())
    {
        echo "<PRE>";
        $phone_to_check = ltrim($row['caller_id_number'],"1");


        if(!empty($phonenumbers[$phone_to_check])){

            echo "<P> Yes: Set ID: ".   $persons[$phonenumbers[$phone_to_check]] . " to THIS: ". $leadIds[$row['group_id']] . " <P>";
            $total_entered++;

            $m = new MOngoClient();
            $db = $m->selectDB($settings['database']);
            $collection = 'person';
            $db->$collection->update(
                array("_id" => $persons[$phonenumbers[$phone_to_check]] ),
                array('$set' => array('leadSource' =>$leadIds[$row['group_id']] )),
                array("multiple" => false)
            );


            // print_r($row);
        } else {
            $total_missing++;
            echo "<P>No -- <p>". $row['caller_id_number'];

            //print_r($row);
        }



    }

    echo "<P>Matched: ". $total_entered;
    echo "<P>Missing: ". $total_missing;


})->via('GET','POST');



// Last 100 check
$app->map('/phoneLogs/:phonenumber', function ($phonenumber) use ($app,$settings) {
   echo "<PRE>";
    
     
    $link = mysqli_connect(
        $settings['vici']['dbipaddress'] ,  
        $settings['vici']['dbuser'] ,
        $settings['vici']['dbpass'],
        $settings['vici']['dbdatabase'],
        $settings['vici']['dbdport']
    );
    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    } 
    
    /*
    $apiObj = new apiclass($settings);
	$docIds = array();
    if($apiObj->userLoggedIn()){
		$apiObj->mongoSetDB($settings['database']);
		$apiObj->mongoSetCollection("phones");
        $collectionQuery['leadCheck']['$exists'] = false;
        $cursor = $apiObj->mongoFind($collectionQuery);
        $cursor->sort(array('_timestampCreated' => -1));
        $cursor->limit(20);
        foreach (iterator_to_array($cursor) as $doc) {
             $phonenumbers[] = ltrim($doc['phoneNumber'],"1");
             $phonenumbers[] = "1".$doc['phoneNumber'];
        }
    } else {
        echo "Not";   
    }
    */
        
        
        
   
        
        
            $phonenumber = ltrim($phonenumber,"1");
            $phonenumbers[] = $phonenumber;
             $phonenumbers[] = "1".$phonenumber;
        
        
   debug($phonenumbers);
   
   //$phonenumber = ltrim($phonenumber,"1");
   $stmt="	SELECT
				vig.group_id,
				vig.group_name,
				vdl.did_id,
				vdl.did_route,
				vid.call_handle_method,
				vid.agent_search_method,
				vid.list_id,
				vid.campaign_id,
				vdl.uniqueid,
				vdl.channel,
				vdl.server_ip,
				vdl.caller_id_number,
				vdl.caller_id_name,
				vdl.extension,
				vdl.call_date,
				vdl.caller_id_number
			FROM 
				vicidial_did_log vdl
			LEFT JOIN
				vicidial_inbound_dids vid on vid.did_id = vdl.did_id
			LEFT JOIN
				vicidial_inbound_groups vig on vig.group_id = vid.group_id
			WHERE 
				caller_id_number in (".implode(",", $phonenumbers).") 
			ORDER BY
				vdl.call_date ASC";
    $result = mysqli_query($link,$stmt);
    $output = array();
    while($row = $result->fetch_assoc())
    {
        echo "<PRE>";
        print_r($row);
        //foreach($row as $key=>$value){
        //	$a[$key] = $value;
        //}
        //array_push($output, $a);
    }
})->via('GET','POST');



$app->run();