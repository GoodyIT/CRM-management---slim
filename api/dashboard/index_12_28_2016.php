<?php
require '../app.php';
$app->config(array(
    'templates.path' => './',
));
$app->get('/', function () use ($app,$settings) {
    $result = array();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    if($apiObj->userLoggedIn()){
        try {
            $userIds = $apiObj->getUserIds();
            $result['widgets']['premiums']['label'] = "<a href='#policies'>Premium</a>";
            $result['widgets']['premiums']['time'] = "Earned";
            $result['widgets']['premiums']['amount'] = 0;
            $result['widgets']['premiums']['compare'] = 0;
            $result['widgets']['premiums']['percent'] = "0%";
            $result['widgets']['premiums']['note'] = "Total Premium As Sold";
            $result['widgets']['policies']['label'] = "<a href='#policies'>Policies Sold / Hold</a>";
            $result['widgets']['policies']['time'] = "Created";
            $result['widgets']['policies']['amount'] = "0";
            $result['widgets']['policies']['percent'] = "0%";
            $result['widgets']['policies']['note'] = "Total Policies Sold";
            $result['widgets']['leads']['label'] = "<a href='#lead'>Leads Created</a>";
            $result['widgets']['leads']['time'] = "Created";
            $result['widgets']['leads']['amount'] = "0";
            $result['widgets']['leads']['percent'] = "0%";
            $result['widgets']['leads']['note'] = "Total Leads Conversion";
            $result['widgets']['clients']['label'] = "<a href='#clients'>Clients</a>";
            $result['widgets']['clients']['time'] = "Calculated";
            $result['widgets']['clients']['amount'] = "0";
            $result['widgets']['clients']['percent'] = "0";
            $result['widgets']['clients']['note'] = "Average Policies Per Client";
            $start_var = -7;
            $end_var = 1;
            $result['timeIncrement'] = "1";
            if(!empty($_SESSION['timeIncrement'])){
                $result['timeIncrement'] = $_SESSION['timeIncrement'];
            }
            if(!empty($_GET['timeIncrement'])){
                $result['timeIncrement'] = $_GET['timeIncrement'];
            }
            $_SESSION['timeIncrement'] = $result['timeIncrement'];
            $start_var = (-$result['timeIncrement']) + 1;
            $start_date  = date('YmdHis', mktime(0, 0, 0, date("m") , date("d") + $start_var, date("Y")));
            $end_date =  date('YmdHis');

            $reportsStartDate = date('m/d/Y', mktime(0, 0, 0, date("m") , date("d") + $start_var, date("Y")));
            $reportsEndDate = date('m/d/Y');

            $collectionQuery = array(
                "_timestampCreated" => array(
                    '$gte' =>  $start_date ,
                    '$lte' =>  $end_date
                )
            );
            if(($result['timeIncrement'] == "week") || ($result['timeIncrement'] == "prevweek")){
                $day = date('w');
                if($result['timeIncrement'] == "prevweek"){
                    $start_date = date('Ymd000000', strtotime('-'.($day + 7).' days'));
                    $end_date = date('Ymd235959', strtotime('+'.(6-($day+7)).' days'));
                    $reportsStartDate = date('m/d/Y', strtotime('-'.($day + 7).' days'));
                    $reportsEndDate = date('m/d/Y', strtotime('+'.(6-($day+7)).' days'));
                    $start_var = -$day - 7;
                    $end_var = $end_var -7;
                } else {
                    $start_date = date('Ymd000000', strtotime('-'.$day.' days'));
                    $end_date = date('Ymd235959', strtotime('+'.(6-$day).' days'));
                    $reportsStartDate = date('m/d/Y', strtotime('-'.$day.' days'));
                    $reportsEndDate = date('m/d/Y', strtotime('+'.(6-$day).' days'));
                    $start_var = -$day;
                }
                $collectionQuery = array(
                    "_timestampCreated" => array(
                        '$gte' =>  $start_date ,
                        '$lte' =>  $end_date
                    )
                );
            }
            $collectionQuery = array();
            $collectionQuery['_timestampCreated']['$gte'] = $start_date;
            $collectionQuery['_timestampCreated']['$lte'] = $end_date;
            $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
            $collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "SOLD";
            $collectionQuery['$and'][0]['$or'][]['status']['$eq'] = "HOLD";
            //$collectionQuery['$or'][]['_createdBy']['$in'] = $userIds;
            // debug($collectionQuery);
            $apiObj->mongoSetCollection("policy");
            $cursor = $apiObj->mongoFind($collectionQuery);
            // echo $cursor->count();
            $cursor->sort(array('_timestampCreated' => 1));
            $total_new_policies = $cursor->count();
            $total_sold_policies = 0;
            $total_hold_policies = 0;
            $record_totals['policies'] = array();
            $record_totals['policies_sold'] = array();
            $start_var = $start_var - 1;
            $client_array = array();
            for($i= $start_var; $i< $end_var; $i++){
                $record_totals['policies'][date('Ymd', time() + (60 * 60 * 24 * $i))] = 0;
                $record_totals['policies_sold'][date('Ymd', time() + (60 * 60 * 24 * $i))] = 0;
                $record_totals['policies_hold'][date('Ymd', time() + (60 * 60 * 24 * $i))] = 0;
            }
            if($cursor->count() == 0){
            } else {
                foreach (iterator_to_array($cursor) as $doc) {
                    $datekey= substr($doc['_timestampCreated'],0,8);
                    if(empty($record_totals['policies'][$datekey])){
                        $record_totals['policies'][$datekey] = 1;
                    } else {
                        $record_totals['policies'][$datekey] =  $record_totals['policies'][$datekey] + 1;
                    }
                    $result['widgets']['policies']['compare'] = $result['widgets']['policies']['compare'] + 1;
                    $result['widgets']['premiums']['compare'] = $result['widgets']['premiums']['compare'] + $doc['premiumMoney'];
                    if((!empty($doc['status'])) && (strtoupper($doc['status']) == "SOLD")){
                        $converted_leads[$doc['_parentId']] = 1;
                        if(empty($record_totals['policies_sold'][$datekey])){
                            $record_totals['policies_sold'][$datekey] = 1;
                        } else {
                            $record_totals['policies_sold'][$datekey] = $record_totals['policies_sold'][$datekey] + 1;
                        }
                        $total_sold_policies++;
                        $result['widgets']['premiums']['amount'] = $result['widgets']['premiums']['amount'] + $doc['premiumMoney'];
                        $result['widgets']['premiums']['percent'] = $result['widgets']['premiums']['percent'] + $doc['premiumMoney'];
                    }
                     if((!empty($doc['status'])) && (strtoupper($doc['status']) == "HOLD")){
                        $converted_leads[$doc['_parentId']] = 1;
                        if(empty($record_totals['policies_hold'][$datekey])){
                            $record_totals['policies_hold'][$datekey] = 1;
                        } else {
                            $record_totals['policies_hold'][$datekey] = $record_totals['policies_hold'][$datekey] + 1;
                        }
                        $total_hold_policies++;
                        $result['widgets']['premiums']['amount'] = $result['widgets']['premiums']['amount'] + $doc['premiumMoney'];
                    }
                    $client_array[$doc['_parentId']] = 1;
                }
            }
            $collectionQuery = array();
            $collectionQuery['assignedTo']['$in'] = $userIds;
            $collectionQuery['_timestampCreated']['$gte'] = $start_date;
            $collectionQuery['_timestampCreated']['$lte'] = $end_date;
            $apiObj->mongoSetCollection("person");
            $cursor = $apiObj->mongoFind($collectionQuery);
            $total_new_leads = $cursor->count();
            $total_new_customers = count($client_array);
            if($result['widgets']['premiums']['compare'] < 1){ $result['widgets']['premiums']['compare'] = 1; }
            if($result['widgets']['policies']['compare'] < 1){ $result['widgets']['policies']['compare'] = 1; }
            $result['widgets']['clients']['amount'] = '<a target="_blank" href="#policies/widget=clients&dashboardStatus=Y&reportsStatus=ANY&reportsStartDate='.urlencode($reportsStartDate).'&reportsEndDate='.urlencode($reportsEndDate).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter=ANY&reportsCloser=ANY&reportsState=ANY">'.$total_new_customers.'</a>';
            if($total_new_customers < 1){
                $result['widgets']['clients']['percent'] =  0;
            } else {
                $result['widgets']['clients']['percent'] =  number_format( $total_new_policies / $total_new_customers , 2, '.', ',') ;
            }
            $result['widgets']['leads']['amount'] = '<a target="_blank" href="#lead/start_date='.urlencode($reportsStartDate).'&end_date='.urlencode($reportsEndDate).'">'.$total_new_leads.'</a>';
            if($total_new_leads < 1){
                $result['widgets']['leads']['percent'] =  0;
            } else {
                $result['widgets']['leads']['percent'] =  number_format(($total_new_customers/$total_new_leads) * 100)."%";
            }
           // $result['widgets']['premiums']['percent'] = number_format(($result['widgets']['premiums']['amount'] / $result['widgets']['premiums']['compare']) * 100) . "%" ;
            $result['widgets']['policies']['amount'] =  '<a target="_blank" href="#policies/widget=Policies_Sold&dashboardStatus=Y&reportsStatus=ANY&reportsStartDate='.urlencode($reportsStartDate).'&reportsEndDate='.urlencode($reportsEndDate).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter=ANY&reportsCloser=ANY&reportsState=ANY">'.$total_sold_policies.'</a>' . " / ". '<a target="_blank" href="#policies/widget=Policies_Hold&dashboardStatus=Y&reportsStatus=ANY&reportsStartDate='.urlencode($reportsStartDate).'&reportsEndDate='.urlencode($reportsEndDate).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter=ANY&reportsCloser=ANY&reportsState=ANY">'.$total_new_policies.'</a>';
            $result['widgets']['policies']['percent'] = number_format(($result['widgets']['policies']['amount'] / $result['widgets']['policies']['compare']) * 100) . "%" ;
            $result['widgets']['premiums']['amount'] =  "$".number_format(  $result['widgets']['premiums']['amount'] , 2, '.', ',') ;
            $result['widgets']['premiums']['percent'] = "$".number_format(  $result['widgets']['premiums']['percent'] , 2, '.', ',') ;
            $result['chart']['highestDay'] = 5;
            //Data2 has to be higher
            foreach($record_totals['policies'] as $key=>$value){
                if($value > $result['chart']['highestDay']){
                    $result['chart']['highestDay'] = $value;
                }
                $result['chart']['data2'][] = array(     substr($key,0,4). ", ". substr($key,4,2). ", ". substr($key,6,2)  , $value);
            }
            foreach($record_totals['policies_sold'] as $key=>$value){
                $result['chart']['data1'][] = array(    substr($key,0,4). ", ". substr($key,4,2). ", ". substr($key,6,2) , $value);
            }
            $total_new_leads_div = $total_new_leads;
            if ( $total_new_leads_div < 1){
                $total_new_leads_div = 1;
            }
            $total_new_policies_div = $total_new_policies;
            if ( $total_new_policies_div < 1){
                $total_new_policies_div = 1;
            }
            $result['chart']['info'][1]= array(
                "value"=>number_format($total_new_leads),
                "description"=>"Converted to <strong>".$total_new_customers."</strong> Clients",
                "percentage"=>number_format(($total_new_customers/$total_new_leads_div) * 100)
            );
            $result['chart']['info'][2]= array(
                "value"=>number_format($total_new_policies),
                "description"=>"Total of <strong>".$total_sold_policies."</strong> Policies Sold",
                "percentage"=>number_format(($total_sold_policies/$total_new_policies_div) * 100)
            );
            $result['chart']['info'][3]= array(
                "value"=>$total_new_customers,
                "description"=>"Lead to Customer Conversion Rate.",
                "percentage"=>number_format(($total_new_customers/$total_new_leads_div) * 100)
            );
            $app->render('dashboard1.php', array('result' => $result, "apiObj"=>$apiObj, "settings"=> $settings));
            peakmemory();
        } catch (Exception $e) {
            // debug($e);
            echo 'Please create your first leads and policies';
        }
    } else {
        echo "Please Log In";
    }
});
$app->map('/info/:table', function ($table) use ($app,$settings) {
    $result = array();
    $result['headers'] = array(
        0=>"Name",
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
    //$userIds = $apiObj->getUserIds();
    $apiObj->mongoSetCollection("policy");
    $collectionQuery['_timestampCreated']['$lte'] = date("Ymd235959");
    $collectionQuery['_timestampCreated']['$gte'] = date("Ymd000000");

    $statuses = array("HOLD","SOLD");
    $collectionQuery['status']['$in'] = $statuses;
    $collectionQuery['soldBy']['$ne'] = "";
    $collectionQuery['closedBy']['$ne'] = "";
    // var_dump($collectionQuery);die();
   // $collectionQuery['$or'][]['soldBy']['$in'] = $userIds;
    //$collectionQuery['$or'][]['closedBy']['$in'] = $userIds;
    $personIds = array();
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    if($cursor2->count() == 0){
        $policies = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            $personIds[] = $doc2['_parentId'];
            // var_dump($doc2);
            if(empty( $policies['closed'][$doc2['closedBy']]['premium'])){
                $policies['closed'][$doc2['closedBy']]['premium'] = 0;
            }
            if(empty( $policies['fronter'][$doc2['soldBy']]['premium'] )){
                $policies['fronter'][$doc2['soldBy']]['premium'] = 0;
            }
            $policies['closed'][$doc2['closedBy']]['policies'][$doc2['_id']]['personId'] =  $doc2['_parentId'];
            $policies['closed'][$doc2['closedBy']]['count'] = count($policies['closed'][$doc2['closedBy']]['policies']);
            $policies['closed'][$doc2['closedBy']]['premium'] = $policies['closed'][$doc2['closedBy']]['premium']  +  $doc2['premiumMoney'];
            $policies['closed'][$doc2['closedBy']]['closedBy'] = $doc2['closedBy'];
            $policies['fronter'][$doc2['soldBy']]['policies'][$doc2['_id']]['personId'] =  $doc2['_parentId'];
            $policies['fronter'][$doc2['soldBy']]['count'] = count($policies['fronter'][$doc2['soldBy']]['policies']);
            $policies['fronter'][$doc2['soldBy']]['premium'] = $policies['fronter'][$doc2['soldBy']]['premium'] +  $doc2['premiumMoney'];
            $policies['fronter'][$doc2['soldBy']]['soldBy'] = $doc2['soldBy'];
            
        }
    }
try {
    foreach ( $policies['closed'] as $key => $row) {
        $volume[$key]  = $row['count'];
        $premium[$key]  = $row['premium'];
    }

    array_multisort($volume, SORT_DESC, $premium, SORT_DESC, $policies['closed']);
    foreach ( $policies['fronter'] as $key => $row) {
        $volume2[$key]  = $row['count'];
        $premium2[$key]  = $row['premium'];
    }
    // var_dump($volume2);die();
    array_multisort($volume2, SORT_DESC, $premium2, SORT_DESC, $policies['fronter']);
    if($table == "closerTable"){
        foreach($policies['closed'] as $key=>$info){
            $result['rows'][] = array(
                // ucwords(strtolower($userList[$key])),
                '<a target="_blank" href="#policies/dashboardStatus=Y&reportsStatus=ANY&reportsStartDate='.urlencode(date("m/d/Y")).'&reportsEndDate='.urlencode(date("m/d/Y")).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter=ANY&reportsCloser='.$info['closedBy'].'&reportsState=ANY">'.ucwords(strtolower($userList[$key])).'</a>',
                $policies['closed'][$key]['count'],
                "$" . number_format(($premium[$key]/$volume[$key]),2,'.',','),
                "$" . number_format($premium[$key],2,'.',','),
            );
        }
    } else {
        foreach($policies['fronter'] as $key=>$info){
            $result['rows'][] = array(
                '<a target="_blank" href="#policies/dashboardStatus=Y&reportsStatus=ANY&reportsStartDate='.urlencode(date("m/d/Y")).'&reportsEndDate='.urlencode(date("m/d/Y")).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter='.$info['soldBy'].'&reportsCloser=ANY&reportsState=ANY">'.ucwords(strtolower($userList[$key])).'</a>',
                $policies['fronter'][$key]['count'],
                "$" . number_format(($premium2[$key]/$volume2[$key]),2,'.',','),
                "$" .  number_format($premium2[$key],2,'.',','),
            );
        }
    }
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response['X-Powered-By'] = 'EBC';
    $response->status(200);
    // etc.
    $response->body(json_encode($result));
} catch (Exception $e) {
    $result['rows'][] = array(
                 "None",
                0,
                "$0.00",
                "$0.00",
            );
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response['X-Powered-By'] = 'EBC';
    $response->status(200);
    // etc.
    $response->body(json_encode($result));
}
})->via('GET','POST');
function record_sort($records, $field, $reverse=false)
{
    $hash = array();
    $item = 1.00000000001;
    foreach($records as $key => $record)
    {
        $item = $item + 0.00000000001;
        $hash[$record[$field].$item] = $record;
    }
    ($reverse)? krsort($hash) : ksort($hash);
    $records = array();
    foreach($hash as $record)
    {
        $records []= $record;
    }
    return $records;
}
$app->map('/grouptables', function () use ($app,$settings) {
    $result = array();
    $result['headers'] = array(
        0=>"Name",
        1=>"Total Policies",
        2=>"Sold",
        3=>"Hold",
        4=>"Follow Up",
        5=>"Cancelled",
        6=>"Payment Issue",
        7=>"Unknown",
        8=>"Premium"
    );
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    if(!$apiObj->userLoggedIn()){
        echo "";
        exit();
    }
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
                        if(strtoupper($var3['level']) == "USER"){
                        $userGroups[$var3['userId']] = $key2;
                        }
                    }
                }
            }
        }
    }
    // var_dump($userGroups);die();
    $apiObj->mongoSetCollection("policy");
            $start_var = -7;
            $end_var = 1;
            $result['timeIncrement'] = "1";
            if(!empty($_SESSION['timeIncrement'])){
                $result['timeIncrement'] = $_SESSION['timeIncrement'];
            }
            if(!empty($_GET['timeIncrement'])){
                $result['timeIncrement'] = $_GET['timeIncrement'];
            }
            $start_var = (-$result['timeIncrement']) + 1;
            $start_date  = date('Ymd000000', mktime(0, 0, 0, date("m") , date("d") + $start_var, date("Y")));
            $end_date =  date('Ymd235959');

            $reportsStartDate = date('m/d/Y', mktime(0, 0, 0, date("m") , date("d") + $start_var, date("Y")));
            $reportsEndDate = date('m/d/Y');
            $collectionQuery = array(
                "_timestampCreated" => array(
                    '$gte' =>  $start_date ,
                    '$lte' =>  $end_date
                )
            );
            if(($result['timeIncrement'] == "week") || ($result['timeIncrement'] == "prevweek")){
                $day = date('w');
                if($result['timeIncrement'] == "prevweek"){
                    $start_date = date('Ymd000000', strtotime('-'.($day + 7).' days'));
                    $end_date = date('Ymd235959', strtotime('+'.(6-($day+7)).' days'));
                    $reportsStartDate = date('m/d/Y', strtotime('-'.($day + 7).' days'));
                    $reportsEndDate = date('m/d/Y', strtotime('+'.(6-($day+7)).' days'));
                    $start_var = -$day - 7;
                    $end_var = $end_var -7;
                } else {
                    $start_date = date('Ymd000000', strtotime('-'.$day.' days'));
                    $end_date = date('Ymd235959', strtotime('+'.(6-$day).' days'));
                    $start_var = -$day;
                    $reportsStartDate = date('m/d/Y', strtotime('-'.$day.' days'));
                    $reportsEndDate = date('m/d/Y', strtotime('+'.(6-$day).' days'));
                }
                $collectionQuery = array(
                    "_timestampCreated" => array(
                        '$gte' =>  $start_date ,
                        '$lte' =>  $end_date
                    )
                );
            }

    $personIds = array();
    $cursor2 = $apiObj->mongoFind($collectionQuery);
    
    if($cursor2->count() == 0){
        $policies = array();
    } else {
        foreach (iterator_to_array($cursor2) as $doc2) {
            if($userGroups[$doc2['soldBy']] <> ""){
                if(empty( $policies[$userGroups[$doc2['soldBy']]]['name'] )){
                    $policies[$userGroups[$doc2['soldBy']]]['name'] = $userGroupList[$userGroups[$doc2['soldBy']]]['label'];
                    $policies[$userGroups[$doc2['soldBy']]]['userGroup'] = $userGroups[$doc2['soldBy']];
                }
                if(empty( $policies[$userGroups[$doc2['soldBy']]]['TOTAL'] )){
                 $policies[$userGroups[$doc2['soldBy']]]['TOTAL'] = 0;
                }
            // echo $policies[$userGroups[$doc2['soldBy']]]['TOTAL'];
                $policies[$userGroups[$doc2['soldBy']]]['TOTAL']++;
                if(empty( $policies[$userGroups[$doc2['soldBy']]]['premium'] )){
                    $policies[$userGroups[$doc2['soldBy']]]['premium'] = 0;
                }
                $policies[$userGroups[$doc2['soldBy']]]['premium'] = $policies[$userGroups[$doc2['soldBy']]]['premium'] +  $doc2['premiumMoney'];
                if($doc2['status'] == ""){
                        $doc2['status'] = "UNKNOWN";
                }
                if(empty( $policies[$userGroups[$doc2['soldBy']]][strtoupper($doc2['status'])] )){
                    $policies[$userGroups[$doc2['soldBy']]][strtoupper($doc2['status'])] = 0;
                }
                $policies[$userGroups[$doc2['soldBy']]][strtoupper($doc2['status'])] = $policies[$userGroups[$doc2['soldBy']]][strtoupper($doc2['status'])] +1;
            }
        } // End foreach
        // die();
    }
try {
    $policies = record_sort($policies, "TOTAL", TRUE);
    foreach($policies as $key=>$var){
         if($var['TOTAL'] == ""){
                $var['TOTAL'] = 0;
            }
            if($var['SOLD'] == ""){
                $var['SOLD'] = 0;
            }
          if($var['HOLD'] == ""){
                $var['HOLD'] = 0;
            }
          if($var['FOLLOWUP'] == ""){
                $var['FOLLOWUP'] = 0;
            }
          if($var['CANCELLED'] == ""){
                $var['CANCELLED'] = 0;
            }
        if($var['PAYMENTISSUE'] == ""){
                $var['PAYMENTISSUE'] = 0;
            }

          if($var['UNKNOWN'] == ""){
                $var['UNKNOWN'] = 0;
            }
            $result['rows'][] = array(
                // $var['name'],
                '<a target="_blank" href="#policies/reportsStatus=ANY&reportsStartDate='.urlencode($reportsStartDate).'&reportsEndDate='.urlencode($reportsEndDate).'&reportsCarrier=ANY&reportsCarrierPlan=ANY&reportsLeadSource=ANY&reportsFronter=ANY&reportsCloser=ANY&reportsState=ANY&reportsUserGroup='.$var['userGroup'].'">'. $var['name'] .'</a>',
                $var['TOTAL'],
                $var['SOLD'],
                $var['HOLD'],
                $var['FOLLOWUP'],
                $var['CANCELLED'],
                $var['PAYMENTISSUE'],
                $var['UNKNOWN'],
                "$" .  number_format( $var['premium'],2,'.',','),
            );
    }
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response['X-Powered-By'] = 'EBC';
    $response->status(200);
    // etc.
    $response->body(json_encode($result));
} catch (Exception $e) {
    $result['rows'][] = array(
                 "None",
                0,
                "$0.00",
                "$0.00",
            );
    $response = $app->response();
    $response['Content-Type'] = 'application/json';
    $response['X-Powered-By'] = 'EBC';
    $response->status(200);
    // etc.
    $response->body(json_encode($result));
}
})->via('GET','POST');
$app->run();