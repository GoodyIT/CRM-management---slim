<?php
date_default_timezone_set('America/Los_Angeles');
setlocale(LC_MONETARY, 'en_US.UTF-8');


// Set default settings
$settings = array();
$settings['site_name'] = "CRM 1.1";
$settings['domain'] = "162.243.147.238";
// $settings['database'] = "insurtainty";
// $settings['database'] = "insurtainty";
$settings['database'] = "dump";

$settings['base_uri'] = "/insurtainty/";
$settings['login_page'] = "api/auth/login";
$settings['registration_page'] = "api/auth/signup";
$settings['date_format'] = "m/d/Y";
$settings['time_format'] = "h:i a";
$settings['date_time_format'] = "m/d/Y h:i a";
$settings['timestamp_format'] = "m/d/Y h:i:s";
$settings['encrypted_key'] = "JKNFSDFLJASFHJhkjHJKHZNKJNkjnjkn123FPMdfP";
$settings['encryptionIV'] = "adslkjKLJKLJMFLKmksmf23#E#@Mm!_2311030";
$settings['password_salt'] = "ANEODOICMWIOCmi3234i2m34mMDM";

// Block Ips
$settings['allowed_ips'] = array();
$settings['allowed_ips'][] = "97.93.171.178";
$settings['allowed_ips'][] = "76.174.70.156";
$settings['allowed_ips'][] = "23.243.43.184";
$settings['allowed_ips'][] = "198.199.103.42";
$settings['allowed_ips'][] = "98.112.94.102";
$settings['allowed_ips'][] = "172.249.13.164";


$settings['allowed_uris'] = array();
$settings['allowed_uris'][] = "http://agents.ebrokercenter.com/api/mail/incoming";
$settings['allowed_uris'][] = "http://agents.ebrokercenter.com/api/inbound/preciseleads";
$settings['allowed_uris'][] = "https://agents.ebrokercenter.com/api/mail/incoming";
$settings['allowed_uris'][] = "https://agents.ebrokercenter.com/api/inbound/preciseleads";
$settings['allowed_uris'][] = "http://104.131.135.180/thingcrm/api/inbound/preciseleads";
$settings['allowed_uris'][] = "http://104.131.135.180/thingcrm/api/mail/incoming";



$settings['assurant_writing_numbers'] = array();
$settings['assurant_writing_numbers'][] = "AA051665000301";
$settings['assurant_writing_numbers'][] = "AA027560000704";
$settings['assurant_writing_numbers'][] = "AA027560000704";




// MAILGUN
$settings['mailgun']['apikey'] = 'key-da685ed267e502ade4f796461e72744f';
$settings['mailgun']['domains'] = array('allinsurancecenter.com','exchangeadvisers.com','insurington.com');

// VICI
$settings['vici']['active'] = FALSE;
$settings['vici']['serverapi'] = "http://97.93.171.189/vicidial/";
$settings['vici']['serverapi'] = "http://97.93.171.182/vicidial/";
$settings['vici']['user'] = "1099";
$settings['vici']['pass'] =  "678923451";
$settings['vici']['apiKey'] =  "AK9129312993939";

$settings['vici']['dbipaddress'] = "97.93.171.181";
$settings['vici']['dbipaddress'] = "127.0.0.1";
$settings['vici']['dbdatabase'] = "asterisk";
$settings['vici']['dbuser'] = "crm";
$settings['vici']['dbpass'] =  "8huno2m903423";
$settings['vici']['dbdport'] = "3307";


/*
2025456529 - All Web Leads
2076203289 - Prisidio Interactive
2076203290 - Stratix
2076203291 - E Lead Gen
2076203292 - Data Lot
2076203293 - Together Health
2076203294 - Pitch Perfect
2076203295 - Dialer Website
2076203296 - Health Pocket
2076203297 - IDA Marketing
*/

//TWILIO
$settings['twilio']['accountSid'] = 'ACa498d8cbded7a64419203bc9671127a2';
$settings['twilio']['authToken'] = '6a254154462485ba06b18a277ff258c9';
if(!empty($_SESSION['api']['user']['_id'])){
$settings['twilio']['clientName'] = strtoupper(str_replace('-', '', preg_replace('/[^A-Za-z0-9\-]/', '', $_SESSION['api']['user']['_id'])));
} else {
  $settings['twilio']['clientName'] = strtoupper(str_replace('-', '', preg_replace('/[^A-Za-z0-9\-]/', '', $settings['domain'])));
}
$settings['twilio']['appId'] = 'AP7664b26eda957fa03a2b42339db12f07';
$settings['twilio']['callerId'] = '+19092120430';
$settings['twilio']['number'] = "HEALTHEXCHANGEADVISERS";

//Mandatory Items
foreach ($settings as $key=>$value){
    $_SESSION['settings'][$key] = $settings[$key];  
}

// Dynamic Global Variables
$settings['leads']['page'] = 1;
$settings['leads']['per_page'] = 100;
$settings['leads']['search'] = "";
$settings['clients']['page'] = 1;
$settings['clients']['per_page'] = 100;
$settings['clients']['search'] = "";
$settings['policies']['page'] = 1;
$settings['policies']['per_page'] = 100;
$settings['policies']['search'] = "";
$settings['news']['page'] = 1;
$settings['news']['news_per_page'] = 5;
$settings['news']['search'] = "";

// Undo empty command to reset sessing variales. Use Reset user sessions to force logins -- CHECK FOR WORKING
if(empty($_SESSION['settings'])){
  $_SESSION['settings'] = $settings;   
}

// LEADS
if(!empty($_REQUEST['leads_page'])){
    $settings['leads']['page'] = $_REQUEST['leads_page'];
    $_SESSION['settings']['leads']['page'] = $_REQUEST['leads_page'];
}
if((empty($settings['leads']['page'])) ||  (!is_numeric($settings['leads']['page'])) ){
    $settings['leads']['page'] = 1;
    $_SESSION['settings']['leads']['page'] =  1;
}

if(isset($_REQUEST['leads_search'])){
    $settings['leads']['search'] = $_REQUEST['leads_search'];
    $_SESSION['settings']['leads']['search'] = $_REQUEST['leads_search'];
} 

if(!empty($_REQUEST['leads_per_page'])){
    $settings['leads']['per_page'] = $_REQUEST['leads_per_page'];
    $_SESSION['settings']['leads']['per_page'] = $_REQUEST['leads_per_page'];
}
if((empty($settings['leads']['per_page'])) ||  (!is_numeric($settings['leads']['per_page']))  ||  ($settings['leads']['per_page'] < 2) ){
    $settings['leads']['per_page'] = 100;
    $_SESSION['settings']['leads']['per_page'] =  100;
}


// CLIENTS
if(!empty($_REQUEST['clients_page'])){
    $settings['clients']['page'] = $_REQUEST['clients_page'];
    $_SESSION['settings']['clients']['page'] = $_REQUEST['clients_page'];
}
if((empty($settings['clients']['page'])) ||  (!is_numeric($settings['clients']['page'])) ){
    $settings['clients']['page'] = 1;
    $_SESSION['settings']['clients']['page'] =  1;
}

if(isset($_REQUEST['clients_search'])){
    $settings['clients']['search'] = $_REQUEST['clients_search'];
    $_SESSION['settings']['clients']['search'] = $_REQUEST['clients_search'];
} 

if(!empty($_REQUEST['clients_per_page'])){
    $settings['clients']['per_page'] = $_REQUEST['clients_per_page'];
    $_SESSION['settings']['clients']['per_page'] = $_REQUEST['clients_per_page'];
}
if((empty($settings['clients']['per_page'])) ||  (!is_numeric($settings['clients']['per_page']))  ||  ($settings['clients']['per_page'] < 2) ){
    $settings['clients']['per_page'] = 100;
    $_SESSION['settings']['clients']['per_page'] =  100;
}

// POLICIES
if(!empty($_REQUEST['policies_page'])){
    $settings['policies']['page'] = $_REQUEST['policies_page'];
    $_SESSION['settings']['policies']['page'] = $_REQUEST['policies_page'];
}
if((empty($settings['policies']['page'])) ||  (!is_numeric($settings['policies']['page'])) ){
    $settings['policies']['page'] = 1;
    $_SESSION['settings']['policies']['page'] =  1;
}

if(isset($_REQUEST['policies_search'])){
    $settings['policies']['search'] = $_REQUEST['policies_search'];
    $_SESSION['settings']['policies']['search'] = $_REQUEST['policies_search'];
} 

if(!empty($_REQUEST['policies_per_page'])){
    $settings['policies']['per_page'] = $_REQUEST['policies_per_page'];
    $_SESSION['settings']['policies']['per_page'] = $_REQUEST['policies_per_page'];
}
if((empty($settings['policies']['per_page'])) ||  (!is_numeric($settings['policies']['per_page']))  ||  ($settings['policies']['per_page'] < 2) ){
    $settings['policies']['per_page'] = 100;
    $_SESSION['settings']['policies']['per_page'] =  100;
}



//news
if(!empty($_REQUEST['news_page'])){
    $settings['news']['page'] = $_REQUEST['news_page'];
    $_SESSION['settings']['news']['page'] = $_REQUEST['news_page'];
}
if(!empty($_REQUEST['calendar_search'])){
    $settings['news']['news_search'] = $_REQUEST['news_search'];
    $_SESSION['settings']['news']['search'] = $_REQUEST['news_search'];
}
if(!empty($_REQUEST['calendar_per_page'])){
    $settings['news']['news_per_page'] = $_REQUEST['news_per_page'];
    $_SESSION['settings']['news']['per_page'] = $_REQUEST['news_per_page'];
}
    
//$settings = $_SESSION['settings'];

