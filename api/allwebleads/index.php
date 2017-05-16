<?php
require '../app.php';
$app->config(array(
	'templates.path' => './',
));



$app->map('/', function () use ($app,$settings) {
   // $xml = file_get_contents('leadpost.xml');

    if(empty($_POST['xml'])){
        echo 'No XML found please post as post variable "xml"';
        exit();
    }
    $xml =$_POST['xml'];
    try {
        $leadinfo = new SimpleXMLElement($xml);
    } catch (Exception $e) {
        echo 'Lead data was received, but there was problem in parsing the data';
        exit();
    }

      $url =  "http://97.93.171.182/vicidial/non_agent_api.php?source=AllWebLead&user=1099&pass=463221&function=add_lead&add_to_hopper=Y&phone_code=1&list_id=995&dnc_check=N";

    if(!empty($leadinfo->ZipCode)){
        //echo $leadinfo->ZipCode;

    }
    if(!empty($leadinfo->ContactInfo->FirstName)){
        //echo $leadinfo->ContactInfo->FirstName;
        $url .= "&first_name=".$leadinfo->ContactInfo->FirstName;
    }
    if(!empty($leadinfo->ContactInfo->LastName)){
        //echo $leadinfo->ContactInfo->LastName;
        $url .= "&last_name=".$leadinfo->ContactInfo->LastName;
    }
    if(!empty($leadinfo->ContactInfo->Address)){
        //echo $leadinfo->ContactInfo->Address;
         $url .= "&address1=".urlencode($leadinfo->ContactInfo->Address);
    }

    if(!empty($leadinfo->ContactInfo->ZipCode)){
        //echo $leadinfo->ContactInfo->ZipCode;
         $url .= "&postal_code=".$leadinfo->ContactInfo->ZipCode;
    }
    if(!empty($leadinfo->ContactInfo->City)){
        //echo $leadinfo->ContactInfo->City;
         $url .= "&city=".$leadinfo->ContactInfo->City;
    }
    if(!empty($leadinfo->ContactInfo->County)){
        //echo $leadinfo->ContactInfo->County;
    }
    if(!empty($leadinfo->ContactInfo->State)){
        //echo $leadinfo->ContactInfo->State;
        $url .= "&state=".$leadinfo->ContactInfo->State;
    }
    if(!empty($leadinfo->ContactInfo->PhoneDay)){
        //echo $leadinfo->ContactInfo->PhoneDay;
        $url .= "&phone_number=".$leadinfo->ContactInfo->PhoneDay;
    }
    if(!empty($leadinfo->ContactInfo->PhoneEve)){
        //echo $leadinfo->ContactInfo->PhoneEve;
        if(empty($leadinfo->ContactInfo->PhoneDay)){
            $url .= "&phone_number=".$leadinfo->ContactInfo->PhoneEve;
        } else {
            $url .= "&alt_phone=".$leadinfo->ContactInfo->PhoneEve;
        }
    }
    if(!empty($leadinfo->ContactInfo->PhoneCell)){
        //echo $leadinfo->ContactInfo->PhoneCell;
        if(empty($leadinfo->ContactInfo->PhoneDay)){
            $url .= "&alt_phone=".$leadinfo->ContactInfo->PhoneCell;
        }
    }
    if(!empty($leadinfo->ContactInfo->Email)){
        //echo $leadinfo->ContactInfo->Email;
         $url .= "&email=".urlencode($leadinfo->ContactInfo->Email);
    }
    if(!empty($leadinfo->ContactInfo->Comment)){
        //echo $leadinfo->ContactInfo->Comment;
    }


    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->DOB)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->DOB;
        $url .= "&date_of_birth=".$leadinfo->HealthInsurance->ApplicantInfo->DOB;

    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Gender)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Gender;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Height_FT)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Height_FT;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Height_IN)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Height_IN;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Weight)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Weight;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Tobacco)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Tobacco;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->Occupation)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->Occupation;
    }
    if(!empty($leadinfo->HealthInsurance->ApplicantInfo->USResidence)){
        //echo $leadinfo->HealthInsurance->ApplicantInfo->USResidence;
    }




    if(!empty($leadinfo->HealthInsurance->SelfEmployed)){
        //echo $leadinfo->HealthInsurance->SelfEmployed;
    }
    if(!empty($leadinfo->HealthInsurance->DUI)){
        //echo $leadinfo->HealthInsurance->DUI;
    }
    if(!empty($leadinfo->HealthInsurance->ExpectantMother)){
        //echo $leadinfo->HealthInsurance->ExpectantMother;
    }


   if(!empty($leadinfo->HealthInsurance->Dependents->Dependent)){
        foreach ($leadinfo->HealthInsurance->Dependents->Dependent as $key=>$var){
                       //echo "<P>Dependent " . $key;
             if(!empty($var->DOB)){
                 //echo $key. " - " .$var->DOB;
             }
              if(!empty($var->Gender)){
                 //echo $key. " - " . $var->Gender;
             }
              if(!empty($var->Height_FT)){
                 //echo $key. " - " .$var->Height_FT;
             }
              if(!empty($var->Height_IN)){
                 //echo $key. " - " . $var->Height_IN;
             }
              if(!empty($var->Weight)){
                 //echo $key. " - " . $var->Weight;
             }
              if(!empty($var->Tobacco)){
                 //echo $key. " - " . $var->Tobacco;
             }
              if(!empty($var->DependentType)){
                 //echo $key. " - " . $var->DependentType;
             }
              if(!empty($var->Student)){
                 //echo $key. " - " . $var->Student;
             }

        }
    }



    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Relative_Heart)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Relative_Heart;
    }

    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Relative_Cancer)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Relative_Cancer;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Medication)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Medication;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Medical_Treatment)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Medical_Treatment;
    }
    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Hospital)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Hospital;
    }

    if(!empty($leadinfo->HealthInsurance->MedicalHistory->Comments)){
        //echo $leadinfo->HealthInsurance->MedicalHistory->Comments;
    }


    if(!empty($leadinfo->HealthInsurance->MajorMedical->AIDS_HIV)){
        //echo $leadinfo->HealthInsurance->MajorMedical->AIDS_HIV;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Alcohol_Drug_Abuse)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Alcohol_Drug_Abuse;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Alzheimers_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Alzheimers_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Asthma)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Asthma;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Cancer)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Cancer;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Cholesterol)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Cholesterol;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Depression)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Depression;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Diabetes)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Diabetes;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Heart_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Heart_Disease;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->High_Blood_Pressure)){
        //echo $leadinfo->HealthInsurance->MajorMedical->High_Blood_Pressure;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Kidney_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Kidney_Disease;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Liver_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Liver_Disease;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Mental_Illness)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Mental_Illness;
    }

    if(!empty($leadinfo->HealthInsurance->MajorMedical->Pulmonary_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Pulmonary_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Stroke)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Stroke;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Ulcer)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Ulcer;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Vascular_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Vascular_Disease;
    }
    if(!empty($leadinfo->HealthInsurance->MajorMedical->Other_Major_Disease)){
        //echo $leadinfo->HealthInsurance->MajorMedical->Other_Major_Disease;
    }



     if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentlyInsured)){
        //echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentlyInsured;
    }
      if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Carrier)){
        //echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Carrier;
    }
    if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Expiration)){
        //echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->Expiration;
    }
    if(!empty($leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->InsuredSince)){
        //echo $leadinfo->HealthInsurance->CurrentInsurance->CurrentPolicy->InsuredSince;
    }



 if(!empty($leadinfo->HealthInsurance->RequestedCoverage)){
        //echo $leadinfo->HealthInsurance->RequestedCoverage;
    }


    ////echo $url;

        //    $url =  "http://97.93.171.182/vicidial/non_agent_api.php?source=AllWebLead&user=1099&pass=463221&function=add_lead&phone_number=5555551010&phone_code=1&list_id=995&dnc_check=N&first_name=Bob&last_name=Wilson&address1=1234+Main+St.&city=Chicago+Heights&state=IL&postal_code=60606";

 try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $head = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    } catch (Exception $e) {
        echo 'Lead was successfully received and parsed, but there was a generic or internally specific problem with the content of the data';
        exit();
    }


    //debug($leadinfo);
})->via('GET','POST');


$app->run();