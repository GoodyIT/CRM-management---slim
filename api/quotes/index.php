<?php
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));
$app->post('/major', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
    echo "Major";
});
$app->map('/', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
    if(empty($_REQUEST['type'])){
        $_REQUEST['type'] = "Accidental";
    }
    
   // echo $_REQUEST['type'];
    
    // debug($_REQUEST);
    //soap_version'=>SOAP_1_1,
    try{
        //$options = array('soap_version'=>SOAP_1_1,'exceptions'=>true,'trace'=>1,'cache_wsdl'=>WSDL_CACHE_NONE);
        if((!empty($_REQUEST['test'])) && ($_REQUEST['test'] == 1)){
            $location = 'https://train2.imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }else{
            $location = 'https://imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }
        $options = array(
            'exceptions' => 1,
            'trace' => 1,
            "connection_timeout"=> 2000,
            'location'       => $location, // Mandatory
        );
        $client = new SoapClient($location.'?wsdl', $options);
        //$client = new SoapClient('https://imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx?wsdl',$options);
        // Note where 'Get' and 'request' tags are in the XML
        $Credentials = array("UserId"=>"IMQuotingTest","Password"=>"Test1234");
        date_default_timezone_set("America/Chicago");
       // if(empty($_REQUEST['person_0_policy_0_effectiveDate'])){
            $today = date("d") + 3;
            $month = date("m");
            $year = date("Y");
            if($today > 25){
                $month = $month + 1;
                $today = 3;
                if($month == 1){
                    $year = $year + 1;   
                }
            }
            $_REQUEST['effDate'] =$year . "-". $month. "-" . $today;
      //  } else {
       //     $_REQUEST['effDate'] = date("Y-m-d", strtotime($_REQUEST['person_0_policy_0_effectiveDate']));
     //   }
        if(empty($_REQUEST['person_0_gender'])){
            echo "<h3>Error</h3>Gender not Provided";
            //  echo "<P>Please provide in format <strong>&person_1_gender=Female</strong>";
            exit();
        }
        if(empty($_REQUEST['person_0_dateOfBirth'])){
            echo "<h3>Error</h3>Date Of Birth not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_smokerTabacco'])){
            echo "<h3>Error</h3>Tobacco Use not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_firstName'])){
            echo "<h3>Error</h3>First Name not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_initial'])){
            $_REQUEST['person_0_initial'] = "";
        }
        if(empty($_REQUEST['person_0_id'])){
            $_REQUEST['person_0_id'] = "";
        }
        if(empty($_REQUEST['person_0_lastName'])){
            echo "<h3>Error</h3>Last Name not Provided";
            exit();
        }
        
         if(empty($_REQUEST['person_0_addresses_0_zipCode'])){
            echo "<h3>Error</h3>Zip Code not Provided";
            exit();
        }
        if(empty($_REQUEST['writingAgentNumber'])){
            $_REQUEST['writingAgentNumber'] = "AA027560000704";
        }
        if(empty($_REQUEST['person_0_suffix'])){
            $_REQUEST['person_0_suffix'] = "";
        }
        if($_REQUEST['person_0_gender'] == "M"){
            $_REQUEST['person_0_gender'] = "Male";
        } else {
            $_REQUEST['person_0_gender'] = "Female";
        }
        
        
        
        $Applicants[] = array(
            "Gender" => $_REQUEST['person_0_gender'],
            "DOB" => date("Y-m-d", strtotime($_REQUEST['person_0_dateOfBirth'])) . '-00:00',
            "Smoker" =>  $_REQUEST['person_0_smokerTabacco'],
            "Relationship" => "Primary",
            "FirstName" => $_REQUEST['person_0_firstname'],
            "Initial" => substr($_REQUEST['person_0_middleName'],0,1),
            "LastName" => $_REQUEST['person_0_lastname'],
            "Suffix" => "",
            "ExternalApplicantID" => $_REQUEST['person_0_id']
        );
        if(!empty($_REQUEST['person_0_spouse_0_spouseFirstName'])){
            if($_REQUEST['person_0_spouse_0_spouseGender'] == "M"){
                $_REQUEST['person_0_spouse_0_spouseGender'] = "Male";
            } else {
                $_REQUEST['person_0_spouse_0_spouseGender'] = "Female";
            }
            $Applicants[] = array(
                "Gender" => $_REQUEST['person_0_spouse_0_spouseGender'],
                "DOB" => date("Y-m-d", strtotime($_REQUEST['person_0_spouse_0_spouseDateOfBirth'])) . '-00:00',
                "Smoker" => $_REQUEST['person_0_spouse_0_spouseSmoker'],
                "Relationship" => "Spouse",
                "FirstName" => $_REQUEST['person_0_spouse_0_spouseFirstName'],
                "Initial" => substr($_REQUEST['person_0_spouse_0_spouseMiddleName'],0,1),
                "LastName" => $_REQUEST['person_0_spouse_0_spouseLastName'],
                "Suffix" => "",
                "ExternalApplicantID" => $_REQUEST['person_0_id']
            );
        }
        $Demographics=array(
            'ZipCode' => $_REQUEST['person_0_addresses_0_zipCode'],
            'EffectiveDate' =>  date("Y-m-d", strtotime($_REQUEST['effDate'])) . '-00:00',
            'Applicants' => $Applicants,
            'Email' => $_REQUEST['person_0_emails_0_email'],
            'Address1' => $_REQUEST['person_0_addresses_0_street1'],
            'Address2' => $_REQUEST['person_0_addresses_0_street2'],
            'Phone' => "",
            'City' => $_REQUEST['person_0_addresses_0_city'],
            'State' =>  $_REQUEST['person_0_addresses_0_state'],
            'County' =>  $_REQUEST['person_0_addresses_0_county'],
            'IsQualifyingLifeEvent' => TRUE,
        );
        $PlanFilter = array(
            'WritingAgentNumber' => $_REQUEST['writingAgentNumber'], 
            'OfficeCopayBuyUp' => FALSE, 
            'TeladocDiscount' => FALSE, 
            'InitialRateGuaranteeBuyUp' => FALSE,
            'FacilityFeeBuyUp' => FALSE, 
            'RightStartOptionsBuyup' => FALSE, 
            'PrescriptionDrugBenefitBuyup' => FALSE, 
            'AccidentalDeathBenefitBuyUp' => FALSE,
            'WaiverOfPremiumBuyUp' => FALSE, 
            'DefaultPlansOnly' => FALSE
        );
        $params = array(
            'Credentials' => $Credentials,
            'ExternalReferenceID' => $_REQUEST['person_0_id'],
            'Demographics'=> $Demographics,
            'PlanFilter' => $PlanFilter
        );
        $quotes = $client->GetASCPlans($params);
        //  debug($quotes);
    } catch (Exception $e) {
        echo($client->__getLastResponse());
        echo PHP_EOL;
        echo($client->__getLastRequest());
    }
    // debug($quotes);
    // Accident 0  https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanAccident.pdf
    // Accident Medical Expense 1  https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/Exclusions-AccMedExpRiders-MT.pdf
    //  Cancer and Heart/Stroke 2   https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanCHS.pdf
    // Dental 3  https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanDental.pdf
    //  debug($quotes->ASCPlanBundles);
    if(strtolower($_REQUEST['type']) == "accidental"){
        if(!empty($quotes->ASCPlanBundles)){
            foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                if(is_array($bundle)){
                    //debug($bundle);
                    foreach($bundle as $key2=>$plans){
                        if($plans->Plan == "Accident"){
                            $bundleKey = $key2;
                        }
                    }
                }
            }
        }
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Carrier</th>
            <th>Coverage</th>
            <th>Plan Type</th>
            <th class="project-actions">Start Up Costs</th>
            <th class="project-actions">Monthly Premium</th>
            <th class="project-actions">More Details</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan)){
            foreach($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan as $qkey=>$qvalue){
                if($qvalue->MonthlyPremium > 0){
                    echo '<tr>';
                    echo '   <td class="project-title"> ';
                    echo '     Assurant ';
                    echo '    </td> ';
                    echo '    <td class="project-title"> ';
                    echo $qvalue->Plan;
                    if(!empty($qvalue->BasePolicy)){
                        echo " ".$qvalue->BasePolicy;
                    }
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->Level;
                    if(!empty($qvalue->LevelDescription)){
                        echo " ".$qvalue->LevelDescription;
                    } 
                    echo '   </td> ';
                    echo '   <td class="project-actions"> ';
                    //echo "<strong>$".number_format($qvalue->PrimaryPremium,2,'.',',')."</strong>";
                    echo '$0.00';
                    echo '  </td>  ';
                    echo '   <td class="project-actions"> ';
                    echo "<strong>$".number_format($qvalue->MonthlyPremium,2,'.',',')."</strong>";
                    echo '  </td>  ';
                    echo '  <td class="project-actions"> ';
                    echo '      <a href="'.$quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->Benefits->PlanInformationLinks->PlanInformationLink[0]->URL.'" target="_blank">Details</a> ';
                    echo '  </td> ';
                    echo ' </tr> ';
                }
            }
        }
        ?>
    </tbody>
</table>
<?php
    }
    if(strtolower($_REQUEST['type']) == "ame"){  
        if(!empty($quotes->ASCPlanBundles)){
            foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                if(is_array($bundle)){
                    foreach($bundle as $key2=>$plans){
                        if($plans->Plan == "Accident Medical Expense"){
                            $bundleKey = $key2;
                            // debug($plans->RatedPlans);
                        }
                    }
                }
            }
        }
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Carrier</th>
            <th>Coverage</th>
            <th>Plan Type</th>
            <th class="project-actions">Start Up Costs</th>
            <th class="project-actions">Monthly Premium</th>
            <th class="project-actions">More Details</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $planlevels = array('2000','3500','5000','7500');
        if(!empty($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan)){
            foreach($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan as $qkey=>$qvalue){
                if(($qvalue->MonthlyPremium > 0) && (in_array($qvalue->Level,$planlevels))){
                    echo '<tr>';
                    echo '   <td class="project-title"> ';
                    echo '     Assurant ';
                    echo '    </td> ';
                    echo '    <td class="project-title"> ';
                    echo $qvalue->Plan;
                    if(!empty($qvalue->BasePolicy)){
                        echo " ".$qvalue->BasePolicy;
                    }
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->Level;
                    if(!empty($qvalue->LevelDescription)){
                        echo " ".$qvalue->LevelDescription;
                    } 
                    echo '   </td> ';
                    echo '   <td class="project-actions"> ';
                    //echo "<strong>$".number_format($qvalue->PrimaryPremium,2,'.',',')."</strong>";
                    echo '$0.00';
                    echo '  </td>  ';
                    echo '   <td class="project-actions"> ';
                    echo "<strong>$".number_format($qvalue->MonthlyPremium,2,'.',',')."</strong>";
                    echo '  </td>  ';
                    echo '  <td class="project-actions"> ';
                    echo '      <a href="https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanAMERdr_MT.pdf" target="_blank">Details</a> ';
                    echo '  </td> ';
                    echo ' </tr> ';
                }
            }
        }
        ?>
    </tbody>
</table>
<?php
    }
    if(strtolower($_REQUEST['type']) == "cancerheart"){    
        if(!empty($quotes->ASCPlanBundles)){
            foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                if(is_array($bundle)){
                    foreach($bundle as $key2=>$plans){
                        if($plans->Plan == "Cancer and Heart/Stroke"){
                            $bundleKey = $key2;
                            //  debug($plans->RatedPlans);
                        }
                    }
                }
            }
        }
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Carrier</th>
            <th>Coverage</th>
            <th>Plan Type</th>
            <th class="project-actions">Start Up Costs</th>
            <th class="project-actions">Monthly Premium</th>
            <th class="project-actions">More Details</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $planlevels = array('20000','35000','50000','75000');
        if(!empty($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan)){
            foreach($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan as $qkey=>$qvalue){
                if(($qvalue->MonthlyPremium > 0) && ((in_array($qvalue->Level,$planlevels)) || (in_array($qvalue->LevelDescription,$planlevels)))){
                    echo '<tr>';
                    echo '   <td class="project-title"> ';
                    echo '     Assurant ';
                    echo '    </td> ';
                    echo '    <td class="project-title"> ';
                    echo $qvalue->Plan;
                    if(!empty($qvalue->BasePolicy)){
                        echo " ".$qvalue->BasePolicy;
                    }
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->Level;
                    if(!empty($qvalue->LevelDescription)){
                        echo " ".$qvalue->LevelDescription;
                    } 
                    echo '   </td> ';
                    echo '   <td class="project-actions"> ';
                    //echo "<strong>$".number_format($qvalue->PrimaryPremium,2,'.',',')."</strong>";
                    echo '$0.00';
                    echo '  </td>  ';
                    echo '   <td class="project-actions"> ';
                    echo "<strong>$".number_format($qvalue->MonthlyPremium,2,'.',',')."</strong>";
                    echo '  </td>  ';
                    echo '  <td class="project-actions"> ';
                    echo '      <a href="https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanAMERdr_MT.pdf" target="_blank">Details</a> ';
                    echo '  </td> ';
                    echo ' </tr> ';
                }
            }
        }
        ?>
    </tbody>
</table>
<?php
    }
    if(strtolower($_REQUEST['type']) == "dental"){
        if(!empty($quotes->ASCPlanBundles)){
            foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                if(is_array($bundle)){
                    foreach($bundle as $key2=>$plans){
                        if($plans->Plan == "Dental"){
                            $bundleKey = $key2;
                            // debug($plans->RatedPlans);
                        }
                    }
                }
            }
        }
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Carrier</th>
            <th>Coverage</th>
            <th>Plan Type</th>
            <th class="project-actions">Start Up Costs</th>
            <th class="project-actions">Monthly Premium</th>
            <th class="project-actions">More Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="project-title">
                Diversified Dental
            </td>
            <td class="project-title">
                Dental
            </td>
            <td class="project-title">
                Comprehensive Plan
            </td>
            <td class="project-actions">
                <strong>$0.00</strong>
            </td>
    </td>
    <td class="project-actions">
        <strong>$25.00</strong>
    </td>
    <td class="project-actions">
        <A href='http://agents.ebrokercenter.com/dental.php' target='_blank'>Details</A>
    </td>
    </tr>
<?php 
        if(!empty($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan)){
            foreach($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan as $qkey=>$qvalue){
                if($qvalue->MonthlyPremium > 0){
                    echo '<tr>';
                    echo '   <td class="project-title"> ';
                    echo '     Assurant ';
                    echo '    </td> ';
                    echo '    <td class="project-title"> ';
                    echo $qvalue->Plan;
                    if(!empty($qvalue->BasePolicy)){
                        echo " ".$qvalue->BasePolicy;
                    }
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->Level;
                    if(!empty($qvalue->LevelDescription)){
                        echo " ".$qvalue->LevelDescription;
                    } 
                    echo '   </td> ';
                    echo '   <td class="project-actions"> ';
                    //echo "<strong>$".number_format($qvalue->PrimaryPremium,2,'.',',')."</strong>";
                    echo '$0.00';
                    echo '  </td>  ';
                    echo '   <td class="project-actions"> ';
                    echo "<strong>$".number_format($qvalue->MonthlyPremium,2,'.',',')."</strong>";
                    echo '  </td>  ';
                    echo '  <td class="project-actions"> ';
                    echo '      <a href="https://train2.ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanAMERdr_MT.pdf" target="_blank">Details</a> ';
                    echo '  </td> ';
                    echo ' </tr> ';
                }
            }
        }
?>
</tbody>
</table>
<?php
    }
   if(strtolower($_REQUEST['type']) == "criticalillness"){    
        if(!empty($quotes->ASCPlanBundles)){
            foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                if(is_array($bundle)){
                    foreach($bundle as $key2=>$plans){
                        if($plans->Plan == "Critical Illness"){
                            $bundleKey = $key2;
                            //  debug($plans->RatedPlans);
                        }
                    }
                }
            }
        }
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Carrier</th>
            <th>Coverage</th>
            <th>Max Benefits</th>
            <th>Term Life</th>
            <th>Policy Term</th>
            <th class="project-actions">Monthly Premium</th>
            <th class="project-actions">More Details</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan)){
            foreach($quotes->ASCPlanBundles->ASCPlanBundle[$bundleKey]->RatedPlans->RatedASCPlan as $qkey=>$qvalue){
                if(($qvalue->MonthlyPremium > 0)){
                    echo '<tr>';
                    echo '   <td class="project-title"> ';
                    echo '     Assurant ';
                    echo '    </td> ';
                    echo '    <td class="project-title"> ';
                    echo $qvalue->Plan;
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->MaxBenefit;
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->TermLifeBenefit;
                    echo '   </td> ';
                    echo '   <td class="project-title"> ';
                    echo $qvalue->PolicyTerm;
                    echo '   </td> ';
                    echo '   <td class="project-actions"> ';
                    echo "<strong>$".number_format($qvalue->MonthlyPremium,2,'.',',')."</strong>";
                    echo '  </td>  ';
                    echo '  <td class="project-actions"> ';
                    echo '      <a href="https://ease.eassuranthealth.com/easeplaninformationpdfs/ASCViewPlanCI.pdf" target="_blank">Details</a> ';
                    echo '  </td> ';
                    echo ' </tr> ';
                }
            }
        }
        ?>
    </tbody>
</table>
<?php
    }
})->via('GET','POST');
$app->map('/gohealth', function () use ($app,$settings) {
    $result['plans'] = array();
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    $goHealth['subscriber_id'] = "118250";
    $goHealth['customer_number'] = "405735";
    $goHealth['lead_type'] = "Health";
    
    if(empty($_REQUEST["person_0_firstName"])){
        echo "Please Provide Name.";
        exit();
    }
    if(empty($_REQUEST["person_0_lastName"])){
        echo "Please Provide Name.";
        exit();
    }
   
    $goHealth['first_name'] = $_REQUEST["person_0_firstName"];
    $goHealth['last_name'] = $_REQUEST["person_0_lastName"];
    $goHealth['contact_time'] = "Morning";
    if(!empty($_REQUEST["person_0_emails_0_email"])){
        $goHealth['email'] = $_REQUEST["person_0_emails_0_email"];
    } else {
        if(!empty($_REQUEST["person_0_emails_1_email"])){
            $goHealth['email'] = $_REQUEST["person_0_emails_1_email"];
        }  
    }
    if(!empty($_REQUEST["person_0_phones_0_phoneNumber"])){
        $goHealth['phone'] = $_REQUEST["person_0_phones_0_phoneNumber"];
    } else {
        if(!empty($_REQUEST["person_0_phones_0_phoneNumber"])){
            $goHealth['phone'] = $_REQUEST["person_0_phones_0_phoneNumber"];
        }  
    }
    $goHealth['phone2'] = "";
    if(!empty($_REQUEST["person_0_addresses_0_zipCode"])){
        $goHealth['address_1_street1']  = $_REQUEST["person_0_addresses_0_street1"];
        $goHealth['address_1_street2'] = $_REQUEST["person_0_addresses_0_street2"];
        $goHealth['address_1_city'] = $_REQUEST["person_0_addresses_0_city"];
        $goHealth['address_1_state'] = $_REQUEST["person_0_addresses_0_state"];
        $goHealth['address_1_zip'] = $_REQUEST["person_0_addresses_0_zipCode"];
    } else { 
        if(!empty($_REQUEST["person_0_addresses_1_zipCode"])){
            $goHealth['address_1_street1']  = $_REQUEST["person_0_addresses_1_street1"];
            $goHealth['address_1_street2'] = $_REQUEST["person_0_addresses_1_street2"];
            $goHealth['address_1_city'] = $_REQUEST["person_0_addresses_1_city"];
            $goHealth['address_1_state'] = $_REQUEST["person_0_addresses_1_state"];
            $goHealth['address_1_zip'] = $_REQUEST["person_0_addresses_1_zipCode"];
        }
    }
    
    
    $goHealth['insured_1_first_name'] = $_REQUEST["person_0_firstName"];
    $goHealth['insured_1_last_name'] = $_REQUEST["person_0_lastName"];
    $goHealth['insured_1_currently_covered'] = "No";
    $goHealth['insured_1_current_medications_detail'] = "";
    $goHealth['insured_1_dobMM'] =  date("m", strtotime($_REQUEST['person_0_dateOfBirth']));
    $goHealth['insured_1_dobDD'] =  date("d", strtotime($_REQUEST['person_0_dateOfBirth']));
    $goHealth['insured_1_dobYYYY'] = date("Y", strtotime($_REQUEST['person_0_dateOfBirth']));
    $goHealth['insured_1_gender'] = $_REQUEST["person_0_gender"];
    $goHealth['insured_1_health_conditions_detail'] = "";
    $goHealth['insured_1_heightFT'] = "";
    $goHealth['insured_1_heightIN'] = "";
    $goHealth['insured_1_weight'] = "";
    $goHealth['insured_1_smoker'] = $_REQUEST["person_0_smokerTabacco"];
    //<!-- SPOUSE -->
    if(!empty($_REQUEST["person_0_spouse_0_spouseFirstName"])){
        $goHealth['insured_2_name'] = $_REQUEST["person_0_spouse_0_spouseFirstName"] . " " .  $_REQUEST["person_0_spouse_0_spouseLastName"];
        $goHealth['insured_2_dobMM'] = date("m", strtotime($_REQUEST['person_0_spouse_0_spouseDateOfBirth']));
        $goHealth['insured_2_dobDD'] = date("d", strtotime($_REQUEST['person_0_spouse_0_spouseDateOfBirth']));
        $goHealth['insured_2_dobYYYY'] = date("Y", strtotime($_REQUEST['person_0_spouse_0_spouseDateOfBirth']));
        $goHealth['insured_2_gender'] = $_REQUEST["person_0_spouse_0_spouseGender"];
        $goHealth['insured_2_heightFT'] = "";
        $goHealth['insured_2_heightIN'] = "";
        $goHealth['insured_2_weight'] = "";
        $goHealth['insured_2_smoker'] = $_REQUEST["person_0_spouse_0_spouseSmoker"];
    }
    //<!-- DEPENDENT -->
    for ($i = 0; $i <= 5; $i++) {
        if(!empty($_REQUEST["person_0_dependents_".$i."_dependentsFirstName"])){
            $x = $i + 3;
            $goHealth['insured_'.$x.'_name'] = $_REQUEST['person_0_dependents_'.$i.'_dependentsFirstName'] . " " .  $_REQUEST['person_0_dependents_'.$i.'_dependentsLastName'];
            $goHealth['insured_'.$x.'_dobMM'] = date("m", strtotime($_REQUEST['person_0_dependents_'.$i.'_dependentsDateOfBirth']));
            $goHealth['insured_'.$x.'_dobDD'] = date("d", strtotime($_REQUEST['person_0_dependents_'.$i.'_dependentsDateOfBirth']));
            $goHealth['insured_'.$x.'_dobYYYY'] = date("Y", strtotime($_REQUEST['person_0_dependents_'.$i.'_dependentsDateOfBirth']));
            $goHealth['insured_'.$x.'_gender'] = "";
            $goHealth['insured_'.$x.'_heightFT'] = "";
            $goHealth['insured_'.$x.'_heightIN'] = "";
            $goHealth['insured_'.$x.'_weight'] = "";
            $goHealth['insured_'.$x.'_smoker'] = "";
            $i++;
        }
    }
    //$goHealth['assigned_user'] = "119750";
    $goHealth['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $goHealth['affiliate_id'] = $settings['domain'];
    $goHealth['link_id'] = "";
    $goHealth['source'] = $_SESSION['api']['user']['lastname'].", ".substr($_SESSION['api']['user']['firstname'],0,1);
    // Check to see if lead was craeted already
    $apiObj->mongoSetCollection("brokeroffice");
    $collectionQuery = false;
    $collectionQuery['_parentId']['$eq'] = $_REQUEST['person_0_id'];
    $cursor = $apiObj->mongoFind($collectionQuery);
    if(!empty($cursor)){
        foreach (iterator_to_array($cursor) as $doc) {
            echo "<h2>This Lead has been generated.</h2>";
            echo "<a href='https://www.brokeroffice.com/leads/leads.jsp?subscriber_id=118250' target='_blank'>Go to your BrokerOffice leads admin and search name. Use Broker Office to get major medical quotes for your lead.</a>";
            echo '<p style="margin-top:20px"><a href="https://www.brokeroffice.com/leads/leads.jsp?subscriber_id=118250" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Go To Broker Office </a>';
            exit();
        }
    }
    $ch = curl_init("https://www.brokeroffice.com/leads/leadImport.do");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $goHealth);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    // Note our use of ===.  Simply == would not work as expected
    // because the position of 'a' was the 0th (first) character.
    $pos = strpos($result, "saved");
    if ($pos === false) {
        $pos = strpos($result, "Saved");
    }
    if ($pos === false) {
        echo "<h2>This lead could not be generated.</h2>";
        echo "Please verify that all information is properly set, Name, Date of Birth, Gender, Tobacco Use, Etc";
    } else {
        $post['person_0_createThing'] = "Y";
        $post['person_0_id'] = $_REQUEST['person_0_id'];
        $post['person_0_brokeroffice_0_createThing'] = "Y";
        $post['person_0_brokeroffice_0_result'] = $result;
        $apiObj->save_things($post);
        echo "<h2>This Lead has been generated.</h2>";
        echo "<a href='https://www.brokeroffice.com/leads/leads.jsp?subscriber_id=118250' target='_blank'>Go to your BrokerOffice leads admin and search name. Use Broker Office to get major medical quotes for your lead.</a>";
        echo '<p style="margin-top:20px"><a href="https://www.brokeroffice.com/leads/leads.jsp?subscriber_id=118250" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Go To Broker Office </a>';
        exit();
    }
})->via('GET','POST');
$app->run();