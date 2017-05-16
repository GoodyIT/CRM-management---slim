<?php


header('Content-Disposition: attachment; filename=crm_export.csv');
$out = fopen('php://output', 'w');
// output the column headings
fputcsv($out, array('ID', 'First Name', 'Middle Name', 'Last Name', 'Date Created', 'Lead Status', 'Email', 'Phone 1', 'Phone 2', 'Address', 'AptNo', 'City', 'State', ' Zip', 'DOB', 'Policy Entered Date', 'Policy Number', 'Carrier', 'Coverage Type', 'Status', 'Premium', 'Subsidy', 'Pay Schedule', 'Effecitve Date', 'Submission Date', 'Renewal Date', 'Term Date', 'Fronter', 'Closer'));



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
        $row = array();
        $row['id'] = $person['firstname'];
        $row['firstname'] = $person['firstname'];
        $row['middlename'] = $person['middlename'];
        $row['lastname'] = $person['lastname'];
        $row['datecreated'] = date("m/d/Y",strtotime($var['_timestampCreated']));
        $row['leadstatus'] = "";
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
        $row['Renewal Date'] = $var['renewalDate'];
        $row['Term Date'] = $var['termDate'];
        $row['Fronter'] = $fronter;
        $row['Closer'] = $closer;

fputcsv($out, $row);

       
    }

}


fclose($out);
exit ();