<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));


$app->post('/create', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    if($apiObj->userLoggedIn()){
        //$apiObj->mongoSetCollection("saveAll");
        //$apiObj->mongoInsert($_REQUEST);


        if($apiObj->save_things($_POST)){
            $result['message'] = "Things Saved";    
        } else {
            $result['message'] = "There was an error saving your Things.";   
        }
        header("Content-Type: application/json");
        echo json_encode($result);
    }
});

$app->get('/remove/:thingType/:thingId', function ($thingType,$thingId) use ($app,$settings) 
          {
               $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
              if($apiObj->userLoggedIn()){
                  $apiObj = new apiclass($settings);
                  $result['message'] = "Thing Saved";
                  $apiObj->mongoSetDB($settings['database']);
                  $apiObj->mongoSetCollection($thingType);
                  $apiObj->mongoRemove(array('_id' => $thingId));      
                  $result['message'] = "Thing Type ".$thingType." removed (Id: ".$thingId.") ";
                  header("Content-Type: application/json");
                  echo json_encode($result);
              }
          });

$app->get('/indextables', function () use ($app,$settings) 
          {
               $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
              if($apiObj->userLoggedIn()){
                  $m = new MongoClient();
                  $db = $m->selectDB($settings['database']);
                  $collections = $db->listCollections();

                  foreach ($collections as $collection) {

                      $collection->ensureIndex(array('_parentId' => 1));
                      $collection->ensureIndex(array('_parentThing' => 1));
                      $collection->ensureIndex(array('_timestampCreated' => -1));
                      echo "<P>amount of documents in $collection: ";
                      echo "<P>".$collection->count(), "\n";
                  }
              }

          });





$app->get('/testit', function () use ($app,$settings) {
    $apiObj = new apiclass($settings);
    $apiObj->mongoSetDB($settings['database']);
    if($apiObj->userLoggedIn()){
        //$apiObj->mongoSetCollection("saveAll");
        //$apiObj->mongoInsert($_REQUEST);


        
     
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
  'person_0_spouse_0_spouseLastName' => '',
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
        
        echo "<PRE>";
       // print_r($post);
        $thing_array = $apiObj->parse_post($post);
       // print_r($thing_array);
         $apiObj->create_thing_array($thing_array);
        print_r($apiObj->things);
        exit();

        if($apiObj->save_things($post)){
            $result['message'] = "Things Saved";    
        } else {
            $result['message'] = "There was an error saving your Things.";   
        }
        header("Content-Type: application/json");
        echo json_encode($result);
    }
});


 $app->run();          