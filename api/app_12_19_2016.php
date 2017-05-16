<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
session_cache_limiter(false);
require_once __DIR__ . '/../vendor/autoload.php';
// CHECK DOMAIN FOR CONFIG FILE
$this_domain = "";
if(!empty($_SERVER['HTTP_HOST'])){
	$this_domain =  $_SERVER['HTTP_HOST'];
} else {
	if(!empty($_SERVER['SERVER_NAME'])){
		$this_domain =  $_SERVER['SERVER_NAME'];
	}
}
//echo $this_domain;
//echo getHost();
// END DOMAIN CHECK
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../api/twilio/twilio.php';
function getHost() {
	$possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
	$sourceTransformations = array(
		"HTTP_X_FORWARDED_HOST" => function($value) {
			$elements = explode(',', $value);
			return trim(end($elements));
		}
	);
	$host = '';
	foreach ($possibleHostSources as $source)
	{
		if (!empty($host)) break;
		if (empty($_SERVER[$source])) continue;
		$host = $_SERVER[$source];
		if (array_key_exists($source, $sourceTransformations))
		{
			$host = $sourceTransformations[$source]($host);
		} 
	}
	// Remove port number from host
	$host = preg_replace('/:\d+$/', '', $host);
	return trim($host);
}
use UAParser\Parser;
$app = new \Slim\Slim(array(
	'cookies.lifetime' => '2 days',
	'cookies.encrypt' => true,
	'cookies.secret_key' => $settings['encrypted_key'],
	'cookies.cipher' => MCRYPT_RIJNDAEL_256,
	'cookies.cipher_mode' => MCRYPT_MODE_CBC
));
if(!empty( $_SESSION['api']['user']['agencyId'])){
	//echo "<P>Agency ID: ". $_SESSION['api']['user']['agencyId'];
}
/*
IP CHECK
*/
$ipaddress = '';
if (getenv('HTTP_CLIENT_IP'))
{
	$ipaddress = getenv('HTTP_CLIENT_IP');
}
else if(getenv('HTTP_X_FORWARDED_FOR'))
{
	$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
}
else if(getenv('HTTP_X_FORWARDED'))
{
	$ipaddress = getenv('HTTP_X_FORWARDED');
}
else if(getenv('HTTP_FORWARDED_FOR'))
{
	$ipaddress = getenv('HTTP_FORWARDED_FOR');
}
else if(getenv('HTTP_FORWARDED'))
{
	$ipaddress = getenv('HTTP_FORWARDED');
}
else if(getenv('REMOTE_ADDR')){
	$ipaddress = getenv('REMOTE_ADDR');
}
else{
	$ipaddress = 'UNKNOWN'; 
}
if(!empty($_REQUEST['texas'])){
	$_SESSION['texas'] = "ON";
}
if(!empty($_SESSION['texas'])){
	$settings['allow_all'] = TRUE;
}
try {
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if(!empty($settings['allowed_uris'])){
		if(in_array($actual_link,$settings['allowed_uris'])){
			$settings['allow_all'] = TRUE;
		}
	}
} catch (Exception $e) {
	$actual_link = "";
}
if($settings['allow_all'] !== TRUE){
	if(!empty($settings['allowed_ips'])){
		if(!in_array($ipaddress,$settings['allowed_ips'])){
			// echo "Coming Soon";
			// exit();
		}
	}
}
//$app->deleteCookie('visitorCookie');
$cookieValue = $app->getEncryptedCookie('visitorCookie'); //NULL if not set
if ( $cookieValue ) {
	// echo "<P>alreadyvisit!";
} else {
	// echo "<P>notvisit!";
	$ua = $_SERVER['HTTP_USER_AGENT'] ;
	$cookieValue = sha1(date("Ymdhis").$ua.rand());
	$app->setEncryptedCookie('visitorCookie', $cookieValue);
	$parser = Parser::create();
	$result = $parser->parse($ua);
	$m = new MongoClient();
	$collection = $m->selectCollection($settings['database'], 'userAgent');
	/*
    print "<P>".$result->ua->family;            // Safari
    print "<P>".$result->ua->major;             // 6
    print "<P>".$result->ua->minor;             // 0
    print "<P>".$result->ua->patch;             // 2
    print "<P>".$result->ua->toString();        // Safari 6.0.2
    print "<P>".$result->ua->toVersion();       // 6.0.2
    print "<P>".$result->os->family;            // Mac OS X
    print "<P>".$result->os->major;             // 10
    print "<P>".$result->os->minor;             // 7
    print "<P>".$result->os->patch;             // 5
    print "<P>".$result->os->patchMinor;        // [null]
    print "<P>".$result->os->toString();        // Mac OS X 10.7.5
    print "<P>".$result->os->toVersion();       // 10.7.5
    print "<P>".$result->device->family;        // Other
    print "<P>".$result->toString();            // Safari 6.0.2/Mac OS X 10.7.5
    print "<P>".$result->originalUserAgent;     // Mozilla/5.0 (Macintosh; Intel Ma...
    */
	$result = (array) $result;
	$result["_id"] = $cookieValue;
	$result["date"] = date("Ymdhis");
	$result["ipAddress"] = $ipaddress;
	$collection->insert($result);
}
/*
*
* FUNCTIONS 
*
*
*
*
*
*/
/*
Verify API KEYS 
*/
$verifyApiKeys  = function () use ($app, $settings)  {
	$apiKey = $app->getEncryptedCookie('apiKey'); //NULL if not set
	if ( $apiKey ) {
		// set to first domain used (hard coded now but should store on installl)
		if($apiKey == $settings['defaults']['apikey']){
			$result["code"] = "ERROR";
			$result["status"] = "API KEYS NOT CORRECT";
			$result["id"] = FALSE;
			header("Content-Type: application/json");
			echo json_encode($result);
			exit();
		}
		return true;
	} else {
		$result["code"] = "ERROR";
		$result["status"] = "API KEYS NOT SET";
		$result["id"] = FALSE;
		header("Content-Type: application/json");
		echo json_encode($result);
		exit();
	}
};
/*
Verify Authentice Role
*/
$authenticateForRole = function ( $role = 'member' ) {
	return function () use ( $role ) {
		$user = User::fetchFromDatabaseSomehow();
		if ( $user->belongsToRole($role) === false ) {
			$app = \Slim\Slim::getInstance();
			$app->flash('error', 'Login required');
			$app->redirect('/login');
		}
	};
};
/*
Verify Login
*/
$verifyLogin = function ()  use ($app, $settings)  {
	$cookieValue = $app->getEncryptedCookie('userCookie'); //NULL if not set
	$apiObj = new apiclass($settings);
	if ( $apiObj->userLoggedIn() ) {
		return true;
	} else {
		$app = \Slim\Slim::getInstance();
		$app->flash('error', 'Login required');
		$app->redirect($settings['base_uri'].'api/auth/login');
	}
};
/*
Debug Output
*/
function createItemPeople($birth_year, $tabacco, $people_type){
	$item_people= 'null';
	if(!empty($birth_year)){
		$age = getAge(date('Y', strtotime($birth_year)));
		
		$item_people = $age;
	}
	
	if(!empty($tabacco) && $tabacco == 'Y'){
		$item_people .= 's';
	}

	return $item_people . $people_type;

}
function getAge($birth_year){
	return (date("Y") - $birth_year);
}

function debug($arr, $label = "Debug Array"){
	echo "<PRE><hr>";
	echo "<h2>".$label."</h2>";
	print_r($arr);
	echo "<hr></pre>";
}
function convertmemory($size) {
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
function peakmemory()
{
	echo "<!-- Peak Memory: ".convertmemory(memory_get_peak_usage(true))."-->";
}
/*
*
* API CLASS
*
*
*
*
*
*
*/
class apiclass
{
	protected $settings;
	protected $mongo;
	public $things;
	function __construct($settings = false){
		$this->mongo['client'] = new MongoClient();
		if(!empty($settings)){
			foreach($settings as $key=>$value){
				$this->settings[$key] = $value;
			}
		}
	}
	/*
     *
     */
	function userLoggedIn(){
		// Check by Cookie
		$user_id = "";
		$reset_user = FALSE;
		if((empty($_SESSION['api']['user']['_id'])) || (trim($_SESSION['api']['user']['_id']) == "")) {
			$app = \Slim\Slim::getInstance();
			$cookieValue = $app->getEncryptedCookie("apiCookieUserId"); //NULL if not set
			if ( $cookieValue ) {
				$user_id = $cookieValue;
				$reset_user = TRUE;
			} else {
				return false; 
			}
		} else {
			$user_id = $_SESSION['api']['user']['_id'];
		}
		$this->mongoSetDB($this->settings['database']);
		$this->mongoSetCollection("user");
		$collectionQuery = array("_id"=>$user_id);
		$item = $this->mongofindOne($collectionQuery);
		if(empty($item)){
			return false;
		} else {
			if(!empty($item['status'])){
				if(strtolower($item['status']) <> "active"){
					echo "Sorry, your account is inactive. <p>Please contact your administrator";
					exit();
				}
			}
			if($reset_user === TRUE){
				unset($item['password']);
				$_SESSION['api']['user'] = $this->get_thing_display($item);
			}
			return true;
		}
	}
	function agencySelect($agencyId = FALSE){
		if(empty($_SESSION['api']['user']['agencyId'])){
			echo "No Agency!";
			exit();
		}
	}
	function userPermissionLevel($permissionLevel = FALSE){
		if ((!array($permissions)) || (empty($_SESSION['api']['user'])) || (!in_array(strtolower($_SESSION['api']['user']['permissionLevel']),$permissionLevel))){
			echo "Sorry, you do not have permissions";
			exit();
		}   
		return true;
	}
	function getSetting($key){
		if(!empty($this->settings[$key])){
			return $this->settings[$key];   
		} else {
			return false;    
		}
	}
	function getUserName($userid){
		$this->getUserList();
		if(!empty($this->users['list'][$userid])){
			return $this->users['list'][$userid]['firstname'] . " ". $this->users['list'][$userid]['lastname'];
		}
	}
	function getUserList(){
		if(empty($this->users['list'])){
			$this->mongoSetDB($this->settings['database']);
			$this->mongoSetCollection("user");
			$cursor = $this->mongoFind($collectionQuery);
			if(empty($cursor)){
				return false;   
			}
			$cursor->sort(array('firstname' => 1));
			$this->users['list'] = array();
			$row = array();
			foreach ($cursor as $doc) {
				$this->users['list'][$doc['_id']] = $this->get_thing_display($doc);   
			}
			return true;
		} else {
			return false;
		}
	}
	function getUserIds(){
		if(empty($_SESSION['api']['user']['_id'])){
			$userIds = array();
			return $userIds;
		}
		$userIds[] = $_SESSION['api']['user']['_id'];
		$this->mongoSetCollection("userGroups");
		$collectionQuery['users.userId']['$eq'] = $_SESSION['api']['user']['_id'];
		$collectionQuery = array();
		$cursor = $this->mongoFind($collectionQuery);
		if(!empty($cursor)){
			$cursor->sort(array('_timestampCreated' => -1));
			$x = 0;
			$groups = array();
			if($cursor->count() == 0){
			} else {
				foreach (iterator_to_array($cursor) as $doc) {
					if(empty($groups[$doc['_id']])){
						$groups[$doc['_id']]['label'] = $doc['label'];
					}
					if(!empty($doc['users'])){
						foreach($doc['users'] as $key=>$userInfo){
							if($userInfo['userId'] == $_SESSION['api']['user']['_id']){
								$groups[$doc['_id']]['level'] = $userInfo['level'];
							}
						}
					}
					if(strtoupper($groups[$doc['_id']]['level']) == "MANAGER"){
						if(!empty($doc['users'])){
							foreach($doc['users'] as $key=>$userInfo){
								if($userInfo['level'] == "USER"){
									$userIds[] = $userInfo['userId'];
								}
							}
						}
					}
					if(strtoupper($groups[$doc['_id']]['level']) == "ADMIN"){
						if(!empty($doc['users'])){
							foreach($doc['users'] as $key=>$userInfo){
								if(($userInfo['level'] == "USER") || ($userInfo['level'] == "MANAGER")){
									$userIds[] = $userInfo['userId'];
								}
							}
						}
					}
				}
			}
		}
		return $userIds;
	}
	function getUserIdsSiblings(){
		if(empty($_SESSION['api']['user']['_id'])){
			$userIds = array();
			return $userIds;
		}
		$userIds[] = $_SESSION['api']['user']['_id'];
		$this->mongoSetCollection("userGroups");
		$collectionQuery['users.userId']['$eq'] = $_SESSION['api']['user']['_id'];
		$cursor = $this->mongoFind($collectionQuery);
		$this->mongoSetCollection("user");
		$collectionQuery = array();
		$collectionQuery['$or'][]['status'] = 'ACTIVE';
		$collectionQuery['$or'][]['status'] = 'active';
		$collectionQuery['canSell']['$eq'] = 'Y';
		$cursor2 = $this->mongoFind($collectionQuery);
		$users = array();
		if(!empty($cursor2)){
			foreach (iterator_to_array($cursor2) as $doc) {
				array_push($users, $doc['_id']);
			}
		}
		if(!empty($cursor)){
			$cursor->sort(array('_timestampCreated' => -1));
			$x = 0;
			$groups = array();
			if($cursor->count() == 0){
			} else {
				foreach (iterator_to_array($cursor) as $doc) {
					if(empty($groups[$doc['_id']])){
						$groups[$doc['_id']]['label'] = $doc['label'];
					}
					foreach($doc['users'] as $key=>$userInfo){
						if(in_array($userInfo['userId'], $users)){
							array_push($userIds, $userInfo['userId']);
						}
					}
				}
			}
		}
		return $userIds;
	}
	/*
*/
	function mongoSetDB($dbname){
		$this->mongo['db'] = $dbname;   
	}
	/*
*/
	function mongoSetCollection($collectionName){
		$this->mongo['collection'] = $collectionName;   
	}
	/*
*/
	function mongoFind($collectionQuery = false)
	{
		try{
			if(empty($collectionQuery)){
				$cursor = $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->find();
			} else {
				$cursor = $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->find($collectionQuery);
			}
			return $cursor;
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoFindOne($collectionQuery = false)
	{
		try{
			$item = $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->findOne($collectionQuery);
			return $item;
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoInsert($collectionFields)
	{
		try{
			$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->insert($collectionFields);
		}
		catch (\Exception $e) {
			return false;
		}
	} 
	/*
*/
	function mongoRemove($collectionFields, $justOne = TRUE)
	{
		try{
			$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->remove($collectionFields, array("justOne" => $justOne));
		}
		catch (\Exception $e) {
			//echo $e;
			return false;
		}
	}
	/*
*/
	function mongoUpdate($mongoCriteria, $collectionUpdates, $createNew = FALSE, $multiple = FALSE)
	{
		try{
			if(($createNew === TRUE) || ($createNew === "Y")){
				$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->update($mongoCriteria, array('$set' => $collectionUpdates), array("upsert" => true, "multiple"=>$multiple));
			} else {
				$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->update($mongoCriteria, array('$set' => $collectionUpdates), array("multiple"=>$multiple));
			}
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoIndexAscending($field){
		try{
			$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->createIndex(array($field => 1));
			return true;
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoIndexDescending($field){
		try{
			$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->createIndex(array($field => -1));
			return true;
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoIndexUnique($field){
		try{
			$this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->createIndex(array($field => 1), array('unique' => true));
			return true;
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function mongoDoesExist($collectionQuery){
		try{
			$item = $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->findOne($collectionQuery);
			if(empty($item)){
				return false;  
			} else {
				return true;
			}
		}
		catch (\Exception $e) {
			return false;
		}
	}
	/*
*/
	function getClientIP() 
	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	/*
*/
	function getValue($arr,$key){
		if(!empty($arr[$key])){
			return $arr[$key];
		} else {
			return false; 
		}
	}
	function getValues($form_values, $field, $key = 0, $key_type = FALSE)
	{
		if ($key_type)
		{
			if (!empty ($form_values[$key_type][$key][$field]))
			{
				return $form_values[$key_type][$key][$field];
			}
			if (!empty ($form_values[$key_type][$field]))
			{
				return $form_values[$key_type][$field];
			}
		}
		if (isset ($form_values[$field]) && is_array($form_values[$field]))
		{
			if (!empty ($form_values[$field][$key]))
			{
				return $form_values[$field][$key];
			}
			else
			{
				return false;
			}
		}
		if (!empty ($form_values[$field]))
		{
			return $form_values[$field];
		}
		else
		{
			return false;
		}
	}
	/*
*/
	function displayPhoneNumber($number = false, $clean = FALSE)
	{
		if ($number == "")
		{
			return false;
		}
		if (!$clean)
		{
			return trim(preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number));
		}
		else
		{
			return trim(preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '$1$2$3', $number));
		}
	}
	/*
*/
	function validatePhoneNumber($number = false)
	{
		$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
		return preg_match( $regex, $number ) ? TRUE : FALSE ;
	}
	/*
*/
	function stateCheck($str, $return_false = FALSE)
	{
		$str = preg_replace("/[^a-zA-Z]/", "", $str);
		$us_state_abbrevs_names = array('AL' => 'ALABAMA', 'AK' => 'ALASKA', 'AS' => 'AMERICAN SAMOA', 'AZ' => 'ARIZONA', 'AR' => 'ARKANSAS', 'CA' => 'CALIFORNIA', 'CO' => 'COLORADO', 'CT' => 'CONNECTICUT', 'DE' => 'DELAWARE', 'DC' => 'DISTRICT OF COLUMBIA', 'FM' => 'FEDERATED STATES OF MICRONESIA', 'FL' => 'FLORIDA', 'GA' => 'GEORGIA', 'GU' => 'GUAM GU', 'HI' => 'HAWAII', 'ID' => 'IDAHO', 'IL' => 'ILLINOIS', 'IN' => 'INDIANA', 'IA' => 'IOWA', 'KS' => 'KANSAS', 'KY' => 'KENTUCKY', 'LA' => 'LOUISIANA', 'ME' => 'MAINE', 'MH' => 'MARSHALL ISLANDS', 'MD' => 'MARYLAND', 'MA' => 'MASSACHUSETTS', 'MI' => 'MICHIGAN', 'MN' => 'MINNESOTA', 'MS' => 'MISSISSIPPI', 'MO' => 'MISSOURI', 'MT' => 'MONTANA', 'NE' => 'NEBRASKA', 'NV' => 'NEVADA', 'NH' => 'NEW HAMPSHIRE', 'NJ' => 'NEW JERSEY', 'NM' => 'NEW MEXICO', 'NY' => 'NEW YORK', 'NC' => 'NORTH CAROLINA', 'ND' => 'NORTH DAKOTA', 'MP' => 'NORTHERN MARIANA ISLANDS', 'OH' => 'OHIO', 'OK' => 'OKLAHOMA', 'OR' => 'OREGON', 'PW' => 'PALAU', 'PA' => 'PENNSYLVANIA', 'PR' => 'PUERTO RICO', 'RI' => 'RHODE ISLAND', 'SC' => 'SOUTH CAROLINA', 'SD' => 'SOUTH DAKOTA', 'TN' => 'TENNESSEE', 'TX' => 'TEXAS', 'UT' => 'UTAH', 'VT' => 'VERMONT', 'VI' => 'VIRGIN ISLANDS', 'VA' => 'VIRGINIA', 'WA' => 'WASHINGTON', 'WV' => 'WEST VIRGINIA', 'WI' => 'WISCONSIN', 'WY' => 'WYOMING', 'AE' => 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST', 'AA' => 'ARMED FORCES AMERICA (EXCEPT CANADA)', 'AP' => 'ARMED FORCES PACIFIC');
		foreach ($us_state_abbrevs_names as $key => $value)
		{
			if ((strtoupper($str) == $key) || (strtoupper($str) == $value))
			{
				return $key;
			}
		}
		if ($return_false === FALSE)
		{
			return strtoupper($str);
		}
		else
		{
			return FALSE;
		}
	}
	function validateTimestamp($date,$returnFormat){
		$dateNow = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2).":".substr($date,12,2);
		$dateNow = $this->validateDate($dateNow,  'Y-m-d H:i:s', $returnFormat);
		return $dateNow;
	}
	/*
*/
	function validateDate($date, $format = 'Y-m-d H:i:s', $returnFormat = "YmdHis" )
	{
		try {
			/*
            if(strlen($date) == 8){
                //if(is_numeric($date)){
                 $date_mo = substr($date,0,2);
                 $date_day = substr($date,2,2);
                 $date_year = substr($date,4,4);
                 $date = $date_year."".$date_mo . "". $date_day . "000000";
                 return $date;
                //}
            }
            if(strlen($date) == 7){
                if(is_numeric($date)){
                 $date_mo = substr($date,0,1);
                 $date_day = substr($date,1,2);
                 $date_year = substr($date,3,4);
                 $date = $date_year."0".$date_mo . "". $date_day . "000000";
                 return $date;
                }
            }
            */
			$d = DateTime::createFromFormat($format, $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$date = str_replace("-", "/", $date);
			// Y/m/d
			$d = DateTime::createFromFormat('Y/m/d', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('Y/m/d H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('Y/m/d G:i', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('Y/m/d h:i a', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$firstMonthCheck = explode("/",$date);
			if($firstMonthCheck[0] < 13){
				// m/d/Y
				$d = DateTime::createFromFormat('m/d/Y H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
				if($d){return $d->format($returnFormat);} 
				$d = DateTime::createFromFormat('m/d/Y', $date, new DateTimeZone("America/Los_Angeles") );
				if($d){return $d->format($returnFormat);} 
				$d = DateTime::createFromFormat('m/d/Y h:i a', $date, new DateTimeZone("America/Los_Angeles") );
				if($d){return $d->format($returnFormat);} 
				$d = DateTime::createFromFormat('m/d/Y G:i', $date, new DateTimeZone("America/Los_Angeles") );
				if($d){return $d->format($returnFormat);} 
			}
			// d/m/Y
			$d = DateTime::createFromFormat('d/m/Y H:i:s', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('d/m/Y', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('d/m/Y h:i a', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('d/m/Y G:i', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			$d = DateTime::createFromFormat('mdY', $date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
			// Final Attempt to return 
			$d = new DateTime($date, new DateTimeZone("America/Los_Angeles") );
			if($d){return $d->format($returnFormat);} 
		} catch (Exception $e) {
			return false;
		}
	}
	function formatDateToSeconds($date){
		try{
			$date_string = new DateTime($date);
			return date_format($date_string, 'YmdHis');
		}
		catch (Exception $e)
		{
		}
		try{
			$date = date_create($date_string);
			return date_format($date, 'YmdHis');
		}
		catch (Exception $e)
		{
		}
		return FALSE;
	}
	function formatDate($date_string = false, $format = 'Y-m-d H:i:s', $create = FALSE)
	{
		if ($date_string == "")
		{
			if (!$create)
			{
				return false;
			}
			date_default_timezone_set('America/Los_Angeles');
			$date_string = date($format);
		}
		try
		{
			$date_string = new DateTime($date_string);
			return date_format($date_string, $format);
		}
		catch (Exception $e)
		{
			return $date_string;
		}
	}
	/*
*/
	function displayDate($date_string = false)
	{
		if ($date_string == "")
		{
			return false;
		}
		if ($this->validateDate($date_string))
		{
			$date = date_create($date_string);
			return date_format($date, "m/d/Y");
		}
		else
		{
			return $date_string;
		}
	}
	/*
*/
	function displayDateTime($date_string = false)
	{
		if ($date_string == "")
		{
			return false;
		}
		if ($this->validateDate($date_string))
		{
			$date = date_create($date_string);
			return date_format($date, "m/d/Y h:i a");
		}
		else
		{
			return $date_string;
		}
	}
	/*
*/
	function displayTime($date_string = false)
	{
		if ($date_string == "")
		{
			return false;
		}
		if ($this->validateDate($date_string))
		{
			$date = date_create($date_string);
			return date_format($date, "h:i a");
		}
		else
		{
			return $date_string;
		}
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	public function setEncrypt($pure_string = false, $encryption_key = false)
	{
		if (empty (trim($pure_string)))
		{
			return FALSE;
		}
		if ($encryption_key === FALSE)
		{
			$encryption_key = $this->settings['encrypted_key'];
		}
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $encryption_key);
		$secret_iv = $this->settings['encryptionIV'];
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		$output = openssl_encrypt($pure_string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		return $output;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	public function getDecrypt($encrypted_string = false, $encryption_key = false)
	{
		if (empty (trim($encrypted_string)))
		{
			return FALSE;
		}
		if (strlen($encrypted_string) < 12)
		{
			//return $encrypted_string;
		}
		if ($encryption_key === FALSE)
		{
			$encryption_key = $this->settings['encrypted_key'];
		}
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $encryption_key);
		$secret_iv = $this->settings['encryptionIV'];
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		try{
			$output = openssl_decrypt(base64_decode($encrypted_string), $encrypt_method, $key, 0, $iv);
		}
		catch (\Exception $e) {
			$output = $encrypted_string;
		}
		return $output;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	public function getDomain()
	{
		if (!empty ($_SERVER['HTTP_HOST']))
		{
			$server_name = $_SERVER['HTTP_HOST'];
		}
		else
		{
			$server_name = $_SERVER['SERVER_NAME'];
		}
		return strtolower(str_replace("www.", "", $server_name));
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getRandomId($parts=2, $part_size=8){
		$id = date("YmdHis"). "-".$this->getRandomString($part_size);
		if($parts > 1){
			for ($i = 2; $i <= $parts; $i++) {
				$id .= "-".$this->getRandomString($part_size);
			}  
		}
		return $id;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getRandomString($name_length = 8) {
		$alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		return substr(str_shuffle(str_repeat($alpha_numeric,12)), 0, $name_length);
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function setFormValues($formArray, $formFields){
		$_SESSION['api']['form']['errors'] = FALSE;
		if( (empty($formArray)) || (!is_array($formArray)) ) {
			return false;   
		}
		// Check Required Are Set
		foreach($formFields as $key=>$value){
			if( (!empty($value['required'])) && ($value['required'] === TRUE)){
				if(empty($formArray[$key])){
					$this->setFormError($key, $formFields[$key]['name'] . " is required");
					$_SESSION['api']['form']['errors'] = TRUE; 
				}
			}
		}
		foreach($formArray as $key=>$value){
			if(!empty($formFields[$key])){
				$this->setFormValue($key,$value);   
				if(!empty($formFields[$key]['type'])){
					if($formFields[$key]['type'] == "email"){
						if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$this->setFormError($key, $formFields[$key]['name'] . " is not a properly formatted email");
							$_SESSION['api']['form']['errors'] = TRUE;
						}
					}
					if($formFields[$key]['type'] == "phone"){
						if (!$this->validatePhoneNumber($value)) {
							$this->setFormError($key, $formFields[$key]['name'] . " is not properly formatted.");
							$_SESSION['api']['form']['errors'] = TRUE;
						}
					}
				}
				if((!empty($formFields[$key]['required'])) && ($formFields[$key]['required'] === TRUE)){
					if( (empty($formArray[$key])) || (trim($formArray[$key]) == "")  ){
						$this->setFormError($key, $formFields[$key]['name'] . " must be set");
						$_SESSION['api']['form']['errors'] = TRUE;
					}
				}
				if((!empty($formFields[$key]['unique'])) && ($formFields[$key]['unique'] === TRUE)){
					$key_array = explode("_",$key);
					if(count($key_array) == 3){
						$this->mongoSetCollection($key_array[0]);
						if(empty($formArray[$key_array[0]."_".$key_array[1]."_id"])){
							$collectionQuery = array(
								$key_array[2] => $value
							);
						} else {
							$collectionQuery = array(
								$key_array[2] =>$value,
								"_id" => array('$ne'=>$formArray[$key_array[0]."_".$key_array[1]."_id"])
							);
						}
					}
					if(count($key_array) == 5){
						$this->mongoSetCollection($key_array[2]);
						if(empty($formArray[$key_array[0]."_".$key_array[1]."_".$key_array[2]."_".$key_array[3]."_id"])){
							$collectionQuery = array(
								$key_array[4] => $value
							);
						} else {
							$collectionQuery = array(
								$key_array[4] =>$value,
								"_id" => array('$ne'=>$formArray[$key_array[0]."_".$key_array[1]."_".$key_array[2]."_".$key_array[3]."_id"])
							);
						}
					}
					if ($this->mongoDoesExist($collectionQuery))
					{
						$this->setFormError($key, $formFields[$key]['name'] . " must be Unique");
						$_SESSION['api']['form']['errors'] = TRUE;
					}
					if( (empty($formArray[$key])) || (trim($formArray[$key]) == "")  ){
						$this->setFormError($key, $formFields[$key]['name'] . " must be set");
						$_SESSION['api']['form']['errors'] = TRUE;
					}
				}
				if((!empty($formFields[$key]['hash'])) && ($formFields[$key]['hash'] === TRUE)){
					//echo "YES!";
				}
				if(!empty($formFields[$key]['match'])){
					$key_match =  $formFields[$key]['match'];
					if( (!empty($formArray[$key_match])) && (!empty($formArray[$key])) ){
						if($formArray[$key_match] != $formArray[$key]){
							$this->setFormError($key, $formFields[$key]['name'] . " does not match " . $formFields[$key_match]['name']);
							$_SESSION['api']['form']['errors'] = TRUE;
						}
					} else {
						$this->setFormError($key, $formFields[$key]['name'] . " and ". $formFields[$key_match]['name'] . " must match");
						$_SESSION['api']['form']['errors'] = TRUE;
					}
				}
			}
		}
		return true;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function formHasErrors(){
		if((!empty($_SESSION['api']['form']['errors'])) && ($_SESSION['api']['form']['errors'] === TRUE)){
			return TRUE; 
		}
		return FALSE;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function setMessage($field="success",$value){
		$_SESSION['api']['messages'][strtolower($field)][] = $value;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getMessage($field="success"){
		$messages = array();
		if(!empty( $_SESSION['api']['messages'][strtolower($field)])){
			if(is_array($_SESSION['api']['messages'][strtolower($field)])){
				foreach($_SESSION['api']['messages'][strtolower($field)] as $message){
					$messages[] = $message;
				}
			} else {
				$messages[] = $_SESSION['api']['messages'][strtolower($field)];
			}
			$_SESSION['api']['messages'][strtolower($field)] = array();
			unset($_SESSION['api']['messages'][strtolower($field)]);
		}
		return $messages;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function setFormValue($field,$value){
		$_SESSION['api']['form'][$field]['value'] = $value;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getFormValue($field){
		$value = FALSE;
		if(!empty($_SESSION['api']['form'][$field]['value'])){
			$value =  $_SESSION['api']['form'][$field]['value'];   
			unset($_SESSION['api']['form'][$field]['value']);
		}
		return $value;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getFormChecked($field,$value){
		$checked = FALSE;
		if(!empty($_SESSION['api']['form'][$field]['value'])){
			if($_SESSION['api']['form'][$field]['value'] == $value){
				$checked = "checked";
			}
			unset($_SESSION['api']['form'][$field]['value']);
		}
		return $checked;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function setFormError($field,$errorMessage){
		$_SESSION['api']['form'][$field]['error'] = $errorMessage;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function getFormError($field){
		$error = FALSE;
		if(!empty($_SESSION['api']['form'][$field]['error'])){
			$error =  $_SESSION['api']['form'][$field]['error'];   
			unset($_SESSION['api']['form'][$field]['error']);
		}
		return $error;
	}
	function get_thing_display($thing){
		try{
			$thing = (array) $thing;
			foreach($thing as $key=>$value)
			{
				if(is_array($value))
				{
					foreach($value as $key2=>$value2){
						if(is_array($value2)){
							foreach($value2 as $key3=>$value3){
								$thing[$key][$key2][$key3] = $this->display_thing_value($key3,$value3);
							}
						} else {
							$thing[$key][$key2] = $this->display_thing_value($key2,$value2);
						}
					}
				} else {
					$thing[$key] = $this->display_thing_value($key,$value);
				}
			}
		}
		catch (\Exception $e) {
			//echo "<br>".$e;
		}
		if(!empty($thing['_id'])){
			$thing['id'] = $thing['_id'];
		}
		return $thing;
	}
	/*
    *   Format Thing Values to save to database
    */
	function validateDateTime($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
	/*
    *   Format Thing Values to save to database
    */
	function format_thing_value($key,$value){
		// countrycode = 2 letter code
		// stateCodeUS = 2 letter state
		// stateNameUS = full state name
		// socialsecurity = encrypt
		if (strpos(strtolower($key), 'firstname') !== FALSE)
		{
			$value = strtoupper($value);
		}
		if (strpos(strtolower($key), 'lastname') !== FALSE)
		{
			$value = strtoupper($value);
		}
		if (strpos(strtolower($key), 'middlename') !== FALSE)
		{
			$value = strtoupper($value);
		}
		if(trim($value) <> ""){
			if (strpos(strtolower($key), 'socialsecurity') !== FALSE)
			{
				$value = $this->setEncrypt($value);
			}
			// socialsecurity = encrypt
			if (strpos(strtolower($key), 'ssn') !== FALSE)
			{
				$value = $this->setEncrypt($value);
			}
			// creditCard = encrypt
			if (strpos(strtolower($key), 'payment') !== FALSE)
			{
				$value = $this->setEncrypt($value);
			} else {
				// bank = encrypt
				if (strpos(strtolower($key), 'bank') !== FALSE)
				{
					// $value = $this->setEncrypt($value);
				}
				// creditCard = encrypt
				if (strpos(strtolower($key), 'creditcard') !== FALSE)
				{
					$value = $this->setEncrypt($value);
				}
				// creditCard = encrypt
				if (strpos(strtolower($key), 'cvv') !== FALSE)
				{
					$value = $this->setEncrypt($value);
				}
			}
		}
		// date == FORMAT DATE !!!
		if (strpos(strtolower($key), 'date') !== FALSE)
		{
			if(trim($value) != ""){
				if (is_a($value, 'MongoDate')) {
					$value = $value->sec;
				}
				if ($this->validateDate($value)) {
					$value = date('YmdHis', strtotime($value));
				} else {
					if ($this->validateDateTime($value)) {
						$value = date('YmdHis', strtotime($value));
					} else {
						$value = $this->formatDateToSeconds(strtotime($value));
					}
				}
			}
		}
		// date == FORMAT DATE !!!
		if (strpos(strtolower($key), 'timestamp') !== FALSE)
		{
			if(trim($value) != ""){
				if (is_a($value, 'MongoDate')) {
					$value = $value->sec;
				}
				if ($this->validateDate($value)) {
					$value = date('YmdHis', strtotime($value));
				} else {
					if ($this->validateDateTime($value)) {
						$value = date('YmdHis', strtotime($value));
					} else {
						$value = $this->formatDateToSeconds(strtotime($value));
					}
				}
			}
		}
		// phone = format
		if (strpos(strtolower($key), 'phone') !== FALSE)
		{
			$value = $this->displayPhoneNumber($value,TRUE);
		}
		// phone = format
		if (strpos(strtolower($key), 'notes') !== FALSE)
		{
			$value =trim(preg_replace('/[^a-zA-Z0-9_ ,$%\[().\]\\/-]/s', '', $value));
		}
		// fax = format
		if (strpos(strtolower($key), 'fax') !== FALSE)
		{
			$value = $this->displayPhoneNumber($value,TRUE);
		}
		// createThing = empty value so no save
		if (strpos(strtolower($key), 'creatething') !== FALSE)
		{
			$value = FALSE;
		}
		// Money
		if (strpos(strtolower($key), 'money') !== FALSE)
		{
			if(trim($value) <> ""){
				$value = preg_replace("/[^0-9.]/", "", $value);
				$value = str_replace("$", "", $value);
				$value = floatval($value);
			}
		}
		// password = hash
		if (strpos(strtolower($key), 'password') !== FALSE)
		{
			$options = [
				'cost' => 10,
				'salt' => $this->settings['password_salt']
			];
			$value =  password_hash($value, PASSWORD_BCRYPT, $options);
		}
		// passwordConfirm = empty value so no save
		if (strpos(strtolower($key), 'passwordconf') !== FALSE)
		{
			$value = FALSE;
		}
		if (strpos(strtolower($key), 'html') !== FALSE)
		{
			$value = $value;
		} else {
			$value =strip_tags($value);
		}
		return utf8_encode($value);
	}
	/*
    *   Format Thing Values for Display from database
    */
	function display_thing_value($key,$value){
		try{
			if (strpos(strtolower($key), 'firstname') !== FALSE)
			{
				$value = ucwords(strtolower($value));
			}
			if (strpos(strtolower($key), 'lastname') !== FALSE)
			{
				$value = ucwords(strtolower($value));
			}
			if (strpos(strtolower($key), 'middlename') !== FALSE)
			{
				$value = ucwords(strtolower($value));
			}
			if (strpos(strtolower($key), 'socialsecurity') !== FALSE)
			{
				$value = $this->getDecrypt($value);
			}
			if (strpos(strtolower($key), 'ssn') !== FALSE)
			{
				$value = $this->getDecrypt($value);
			}
			if (strpos(strtolower($key), 'payment') !== FALSE)
			{
				$value = $this->getDecrypt($value);
			} else {
				if (strpos(strtolower($key), 'bank') !== FALSE)
				{
					$value = $this->getDecrypt($value);
				}
				if (strpos(strtolower($key), 'creditcard') !== FALSE)
				{
					$value = $this->getDecrypt($value);
				}
				if (strpos(strtolower($key), 'cvv') !== FALSE)
				{
					$value = $this->getDecrypt($value);
				}
			}
			if (strpos(strtolower($key), 'date') !== FALSE)
			{
				if (is_a($value, 'MongoDate')) {
					$value = $value->sec;
				}
				if ($myDateTime = DateTime::createFromFormat('YmdHis', $value)) {
					$value = $myDateTime->format($this->settings['date_format']);
				}
				//$value =  date($this->settings['date_format'], $value);
			}
			if (strpos(strtolower($key), 'timestamp') !== FALSE)
			{
				if ($myDateTime = DateTime::createFromFormat('YmdHis', $value)) {
					$value = $myDateTime->format("Y-m-d H:i:s");
				}
				// $value =  date("Y-m-d H:i:s", $value);
			}
			if ((strpos(strtolower($key), 'time') !== FALSE) && ((strpos(strtolower($key), 'timestamp') === FALSE)))
			{
				if ($myDateTime = DateTime::createFromFormat('YmdHis', $value)) {
					$value = $myDateTime->format($this->settings['time_format']);
				}
				//$value =  date($this->settings['time_format'], $value);
			}
			if (strpos(strtolower($key), 'phone') !== FALSE)
			{
				$value = $this->displayPhoneNumber($value);
			}
			if (strpos(strtolower($key), 'fax') !== FALSE)
			{
				$value = $this->displayPhoneNumber($value);
			}
			if (strpos(strtolower($key), 'money') !== FALSE)
			{
				//setlocale(LC_MONETARY, 'en_US');
				//$value = money_format('%i', $value);
				$value = number_format($value,2,'.',',');
			}
			if (strpos(strtolower($key), 'password') !== FALSE)
			{
				$value = false;
			}
		}
		catch (\Exception $e) {
			//echo $e; exit();
			//return false;
		};
		return $value;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
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
					if(!empty($doc['timestampCreated'])){
						$_temp_thing['_timestampCreated'] = date("YmdHis", strtotime($doc['timestampCreated']));
					} else {
						$_temp_thing['_timestampCreated'] = date("YmdHis") ;
					}
					if(!empty($doc['timestampModified'])){
						$_temp_thing['_timestampModified'] = date("YmdHis", strtotime($doc['timestampModified']));
					} else {
						$_temp_thing['_timestampModified'] = date("YmdHis") ;
					}
					if(!empty($_SESSION['api']['user']['_id'])){
						$_temp_thing['_createdBy'] = $_SESSION['api']['user']['_id'] ;
						$_temp_thing['_modifiedBy'] = $_SESSION['api']['user']['_id'] ;
					}
					if(!empty($this->settings['conversion'])){
						if(!empty($doc['dateCreated'])){
							$_temp_thing['_timestampCreated'] = date("YmdHis", strtotime($doc['dateCreated']));
							unset($doc['dateCreated']);
						}
						if(!empty($doc['dateModified'])){
							$_temp_thing['_timestampModified'] = date("YmdHis", strtotime($doc['dateModified']));
							unset($doc['dateModified']);
						}
					}
					$_temp_thing['_id'] = $_id;
					$_temp_thing['_parentId'] = $parent_id;
					$_temp_thing['_parentThing'] = $parent_thing;
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
								if($temp_value){
									//$_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
									$_temp_thing[$key3] = $temp_value;
								}
							}
						}
					}
					$original_doc = array();
					$this->mongoSetCollection($key1);
					$collectionQuery = false;
					$collectionQuery['_id']['$eq'] = $_id;
					$cursor = $this->mongoFind($collectionQuery);
					if(!empty($cursor)){
						foreach (iterator_to_array($cursor) as $original_doc) {
							$new_thing = FALSE;
							break;
						}
					}
					//echo "<PRE>";
					$updated_thing = array();
					if($new_thing === TRUE){
						$keep_values = array("NOTSETITEM");
					} else {
						$keep_values = array("_timestampCreated", "_createdBy");
					}
					// Set Updated thing to Original Doci if Exists as base; 
					if(!empty($original_doc)){
						$updated_thing = $original_doc;
						//debug($original_doc, "ORIGINAL");
						if(is_array($_temp_thing)){
							//   debug($_temp_thing, "temp thing");
							foreach($_temp_thing as $key=>$value){
								if(is_array($value)){
									if(is_array($original_doc[$key])){
										$updated_thing[$key] = $this->compare_things($_temp_thing[$key], $original_doc[$key]);
									} else {
										$updated_thing[$key] = $value;
									}
								} else {
									if(!in_array($key,$keep_values)){
										$updated_thing[$key] = $value;
									}
								}
							} 
						}
					} else {
						$updated_thing = $_temp_thing;
					}
					// echo "<PRE>";
					// debug($doc , " DOC");
					//debug($original_doc, " ORIGINAL DOC");
					//debug($_temp_thing, " TEMP THING");
					// Remove Empty Data 
					foreach($updated_thing as $tempKey=>$tempVal){
						if(is_array($tempVal)){
							foreach($tempVal as $tempKey2=>$tempVal2){
								if(!empty($tempVal2['id'])){
									unset($tempVal2['id']);
								}
								if(!empty($tempVal2['createThing'])){
									unset($tempVal2['createThing']);
								}
								$datacount = 0;
								foreach($tempVal2 as $tempKey3=>$tempVal3){
									$pos = strpos($tempKey3, "_");
									if ($pos === false) {
										if(trim($tempVal3) != ""){
											$datacount++;
										}
									}  
								}
								//debug($tempVal2, "TEM VAL THING! Data Count". $datacount);  
								if($datacount == 0){
									unset($updated_thing[$tempKey][$tempKey2]);
								}
							}   
							if(empty($updated_thing[$tempKey])){
								unset(   $updated_thing[$tempKey] );
							}
						}
					}
					// debug($updated_thing, "UPDATED THING!");
					//exit();
					// echo "<PRE>";
					// echo "<h4>Update</h4>";
					// print_r($updated_thing);
					/*
                    echo "<h4>Original</h4>";
                    print_r($original_doc);
                    echo "<h4>New</h4>";
                    print_r($_temp_thing);
                    echo "</PRE>";
                    */
					// exit();
					if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE) || ($doc['createThing'] === "Y") || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
						$this->things[$key1][] = $updated_thing;
					}
					//if((isset($doc['deleteThing'])) && ( ($doc['deleteThing'] === TRUE) || ($doc['deleteThing'] === "Y") || ($doc['deleteThing'] === 1) || ($doc['creatdeleteThingeThing'] === "TRUE") ) ){
					//$this->things[$key1][] = $updated_thing;
					//}
				}
			}
		}
		return false;
	}
	function compare_things($new_thing,$old_thing){
		$keep_values = array("_timestampCreated", "_createdBy");
		$updated_thing = array();
		if(is_array($old_thing)){
			foreach($old_thing as $key=>$val){
				if(!empty($val['_id'])){
					$updated_thing[$val['_id']] = $val;
				}
			}
		}
		foreach($new_thing as $nKey=>$nValue){
			$itemFound = FALSE;
			if(is_array($old_thing)){
				foreach($old_thing as $oKey=>$oValue){
					if( ((!empty($oValue['_id'])) &&  (   $oValue['_id'] ==  $nValue['_id'])) || ((!empty($oValue['id'])) &&  (   $oValue['id'] ==  $nValue['_id'])) ){
						$itemFound = TRUE;
						if(!empty($oValue['_id'])){
							$this_id = $oValue['_id'];
						} else {
							$this_id = $oValue['_id'];
						}
						$updated_thing[$this_id] = $oValue;
						foreach($nValue as $fkey=>$fval){
							if(!is_array($fval)){
								if(!in_array($fkey,$keep_values)){
									$updated_thing[$this_id][$fkey] = $fval;
								}
							} else {
								if((!empty($oValue[$fkey])) && (!is_array( $oValue[$fkey]))){
									$updated_thing[$this_id][$fkey] = $fval;
								} else {
									$updated_thing[$this_id][$fkey] = $this->compare_things($fval, $oValue[$fkey]);
								}
							}
						}
					}
				}
			}
			if($itemFound === FALSE){
				$updated_thing[$nValue['_id']] = $nValue;
			}  
		}
		$updated_thing= array_values($updated_thing);
		if(!empty($updated_thing)){
			foreach($updated_thing as $key=>$val){
				if((empty($updated_thing[$key])) || (count($updated_thing[$key]) == 0)){
					unset($updated_thing[$key]);
				}
				// if(!empty($val['deleteThing'])){
				//  if((isset($val['deleteThing'])) && ( ($val['deleteThing'] === TRUE) || ($val['deleteThing'] === "Y") || ($val['deleteThing'] === 1) || ($val['creatdeleteThingeThing'] === "TRUE") ) ){
				//      unset($updated_thing[$key]);
				//     $updated_thing = array_values($updated_thing);
				//   } 
				//}
			}
		}
		/*
   */
		// debug($updated_thing, "UPDATED THING!!!");
		return $updated_thing;
	}
	/*
        Builds a properly formatted Child Thing Array
    */
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
					if(!empty($this->settings['conversion'])){
						$_id = $parent_id . "-". $key2 ."-".$child_thing; // ADD IN FOR CONVERSION
					}
					if(!empty($doc['timestampCreated'])){
						$_temp_thing['_timestampCreated'] = date("YmdHis", strtotime($doc['timestampCreated']));
					} else {
						$_temp_thing['_timestampCreated'] = date("YmdHis") ;
					}
					if(!empty($doc['timestampModified'])){
						$_temp_thing['_timestampModified'] = date("YmdHis", strtotime($doc['timestampModified']));
					} else {
						$_temp_thing['_timestampModified'] = date("YmdHis") ;
					}
					if(!empty($_SESSION['api']['user']['_id'])){
						$_temp_thing['_createdBy'] = $_SESSION['api']['user']['_id'] ;
						$_temp_thing['_modifiedBy'] = $_SESSION['api']['user']['_id'] ;
					}
					if(!empty($this->settings['conversion'])){
						if(!empty($doc['dateCreated'])){
							$_temp_thing['_timestampCreated'] = date("YmdHis", strtotime($doc['dateCreated']));
							unset($doc['dateCreated']);
						}
						if(!empty($doc['dateModified'])){
							$_temp_thing['_timestampModified'] = date("YmdHis", strtotime($doc['dateModified']));
							unset($doc['dateModified']);
						}
					}
					$_temp_thing['_id'] = $_id;
					$_temp_thing['_parentId'] = $parent_id;
					$_temp_thing['_parentThing'] = $parent_thing;
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
							//if($temp_value){
							if($key3 != "createThing"){
								//$_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
								$_temp_thing[$key3] = $temp_value;
							}
							// }
						}
					}
					$original_doc = false;
					$this->mongoSetCollection($child_thing);
					$collectionQuery = false;
					$collectionQuery['_id']['$eq'] = $_id;
					$cursor = $this->mongoFind($collectionQuery);
					if(!empty($cursor)){
						foreach (iterator_to_array($cursor) as $original_doc) {
							$new_thing = FALSE;
							break;
						}
					}
					$updated_thing = array();
					if($new_thing === TRUE){
						$keep_values = array("NOTSETITEM");
					} else {
						$keep_values = array("_timestampCreated", "_createdBy");
					}
					// Set Updated thing to Original Doci if Exists as base; 
					if(!empty($original_doc)){
						$updated_thing = $original_doc;
						//debug($original_doc, "ORIGINAL");
						if(is_array($_temp_thing)){
							//   debug($_temp_thing, "temp thing");
							foreach($_temp_thing as $key=>$value){
								if(is_array($value)){
									if(is_array($original_doc[$key])){
										$updated_thing[$key] = $this->compare_things($_temp_thing[$key], $original_doc[$key]);
									} else {
										$updated_thing[$key] = $value;
									}
								} else {
									if(!in_array($key,$keep_values)){
										$updated_thing[$key] = $value;
									}
								}
							} 
						}
					} else {
						$updated_thing = $_temp_thing;
					}
					// Remove Empty Data 
					foreach($updated_thing as $tempKey=>$tempVal){
						if(is_array($tempVal)){
							if(empty($updated_thing[$tempKey])){
								unset(   $updated_thing[$tempKey] );
							}
						}
					}
					//debug($_temp_thing, "temp thing");
					//debug($original_doc, "Original Doc");
					//debug($doc, "Doc");
					//debug($updated_thing, "Updated thing");
					if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE)  || ($doc['createThing'] === "Y") || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
						$this->things[$child_thing][] = $updated_thing;
					} else {
						// if((isset($doc['deleteThing'])) && ( ($doc['deleteThing'] === TRUE) || ($doc['deleteThing'] === "Y") || ($doc['deleteThing'] === 1)) ){
						//     $this->things[$child_thing][] = $updated_thing;
						//  } else {
						$return_children[] = $updated_thing;   
						//  }
					}
				}
			}
		}
		// debug($return_children, "return children");
		return $return_children;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function create_thing_check($post_array)
	{
		if(is_array($post_array))
		{
			foreach($post_array as $key=>$value)
			{
				if((!empty($value['createThing'])) && ($value['createThing'] === TRUE))
				{
					return TRUE;
				}
			}
		}   
		return FALSE; 
	}
	// Parse an array in the format ['collection_index_column'] gets saved as a thing. 
	function save_things($thing_array)
	{
		if(!empty($thing_array)){
			$thing_array = $this->parse_post($thing_array);
			$result['thingarray'] = $thing_array;
			$this->create_thing_array($thing_array);
			// debug($thing_array, "thing array!");
			try{
				// Loop Through Things and check if stop from creating new items that are blank
				foreach($this->things as $key=>$value){
					foreach($value as $key2=>$value2){
						$empty_item = TRUE;
						if(!empty($value2['_timestampCreated'])){ // check if new item _dateCreated should be set.
							foreach($value2 as $key3=>$value3){
								$field_id = substr($key3, 0,1);
								if($field_id != "_"){
									if(!empty($value3) && (trim($value3) <> "")){
										$empty_item = FALSE;
									}
								}
							}
							if($empty_item === TRUE){
								unset($this->things[$key][$key2]);                  
							}  
						}
					}
				}
			}
			catch (\Exception $e) {
				//echo $e; exit();
				//return false;
			};
			// debug($this->things, "THINGS!");
			foreach($this->things as $key=>$value){
				foreach($value as $key2=>$value2){
					$this->mongoSetCollection($key);
					$criteria = array("_id"=>$value2['_id']);
					foreach($value2 as $key3=>$value3){
						if(is_array($value3)){
							foreach($value3 as $key4=>$value4){
								// DELETE NESTED ITEM
								if((isset($value4['deleteThing'])) && ( ($value4['deleteThing'] === TRUE)  || ($value4['deleteThing'] === "Y") || ($value4['deleteThing'] === 1) || ($value4['deleteThing'] === "TRUE") ) ){
									unset($value2[$key3][$key4]);
								}
							}
							$value2[$key3] = array_values($value2[$key3]);
						}
						//debug($value2, $key3);
						if($key3 == "spouse"){
							if(count($value2["spouse"] > 1)){
								$spouse[0] = $value2[$key3][0];
								$value2["spouse"] = $spouse;
							}
						}
					}
					if((isset($value2['deleteThing'])) && ( ($value2['deleteThing'] === TRUE)  || ($value2['deleteThing'] === "Y") || ($value2['deleteThing'] === 1) || ($value2['deleteThing'] === "TRUE") ) ){
						// DELETE ITEM
						$this->mongoRemove($criteria); 
					} else {
						// INSERT/UPDATE ITEM
						$this->mongoUpdate($criteria, $value2, TRUE); 
					}
				}
			}
			//exit();
			return true;
		}
		return false;
	}
	/*
    *   Small function to parse Key String into Multidimensional Array
    */
	function parse_post_to_array(& $newarr, $keys, $value)
	{
		if (count($keys) > 1)
		{
			$key = array_shift($keys);
			if (!isset ($newarr[$key]) || !is_array($newarr[$key]))
			{
				$newarr[$key] = array();
			}
			$this->parse_post_to_array($newarr[$key], $keys, $value);
		}
		else
		{
			$newarr[array_shift($keys)] = $value;
		}
		return $newarr;
	}
	function parse_post($post_array){
		foreach ($post_array as $key => $value)
		{
			$keys = explode("_", $key);
			$thing_post = $this->parse_post_to_array($newarr, $keys, $value);
		}
		return $thing_post;
	}
	function saveAll($info = false, $type = false){
		$this->mongoSetCollection("saveAll");
		$saveAll['_id'] = $this->getRandomId();
		$saveAll['_dateCreated'] = date("YmdHis");
		$saveAll['_type'] = $type;
		if(!empty($_SESSION['api']['user']['_id'])){
			$saveAll['_userId'] = $_SESSION['api']['user']['_id'];
		} else {
			$saveAll['_userId'] = "UNKNOWN";
		}
		if(is_array($info)){
			$result = array_merge($saveAll, $info); 
		}
		$this->mongoInsert($result);
		return true;
	}
	function setThingFormVariables(){
		if(empty($this->forms['systemForms'])){
			$this->mongoSetDB($this->settings['database']);
			$this->mongoSetCollection("systemForm");
			$cursor = $this->mongoFind($collectionQuery);
			if(empty($cursor)){
				return false;   
			}
			$cursor->sort(array('sort' => 1));
			$row = array();
			foreach ($cursor as $doc) {
				$row[$doc['thing']][(int)$doc['row']][] = $doc;   
			}
			foreach($row as $thingKey=>$thingRow){
				ksort($row[$thingKey]);  
			}
			$this->forms['systemForms'] = $row;
		} else {
		}
	}
	function checkThingFormVariable($thing,$key, $val){
		$this->setThingFormVariables();
		if(empty($this->forms['systemForms'][$thing])){
			return $val;   
		}
		foreach($this->forms['systemForms'][$thing] as $rowId=>$rowItem){
			if(is_array($rowItem)){
				foreach($rowItem as $itemId=>$itemInfo){
					if((!empty($itemInfo['name'])) && ($itemInfo['name'] == $key)){
						if(!empty($itemInfo['options'])){
							foreach($itemInfo['options'] as $optionKey=>$optionValue){
								if(strtoupper($val) == strtoupper($optionValue['value'])){
									return $optionValue['label'];   
								}
							}
						}
					}
				}
			}
		}
	}
	function displayThingForm($thing, $thingData = FALSE,  $index,  $prefix=FALSE, $createThing = "Y"){
		$this->setThingFormVariables();
		if(empty($this->forms['systemForms'][$thing])){
			return false;   
		}
		$_temp_id = $this->getValue($thingData[$index],'_id');
		if(empty($_temp_id)){
			$_temp_id = $this->getValue($thingData[$index],'id');
		}
		if(empty($_temp_id)){
			$_temp_id = $this->getRandomId();   
		}
		if($thing == "notes"){
			$_temp_id = $this->getRandomId();   
		}
		echo '<input type="hidden" name="'.$prefix.''.$thing.'_'.$index.'_createThing" value="'.$createThing.'" />';
		echo '<input type="hidden" name="'.$prefix.''.$thing.'_'.$index.'_id" value="'.$_temp_id.'" />';
		$currentRow = 0;
		$fieldCount = 0;
		$endRow = false;
		foreach ($this->forms['systemForms'][$thing] as $rowKey=>$rowDocs) {
			if($rowKey != $currentRow){
				if($endRow === true){
					echo "</div>";   
					echo "</div>";  
				}
				$currentRow = $rowKey;
				echo "<div class='row'><!-- Dynamic Row ($rowKey) -->";   
				echo "<div class='col-sm-12'>";
				$endRow = true;
			}
			if(is_array($rowDocs)){
				foreach($rowDocs as $doc){
					$createFormItem = TRUE;
					if(strtoupper($doc['permissionLevel']) == "MANAGER"){
						if(empty($_SESSION['api']['user']['permissionLevel'])){
							$createFormItem = FALSE;
						} else {
							$createFormItem = FALSE;
							if(strtoupper($_SESSION['api']['user']['permissionLevel']) == "MANAGER"){
								$createFormItem = TRUE;
							}
							if(strtoupper($_SESSION['api']['user']['permissionLevel']) == "ADMINISTRATOR"){
								$createFormItem = TRUE;
							}
							if(strtoupper($_SESSION['api']['user']['permissionLevel']) == "INSUREHC"){
								$createFormItem = TRUE;
							}
							if(strtoupper($_SESSION['api']['user']['permissionLevel']) == "ACCOUNTING"){
								$createFormItem = TRUE;
							}
						}
					}
					if($createFormItem === TRUE){
						if(strtoupper($doc['type']) == "TEXT"){
							$this->formTextField($doc, $thingData[$index], $index, $prefix);
						}
						if(strtoupper($doc['type']) == "PASSWORD"){
							$this->formPasswordField($doc, $thingData[$index], $index, $prefix);
						}
						if(strtoupper($doc['type']) == "DATE"){
							$this->formDateField($doc, $thingData[$index], $index, $prefix);
						}
						if(strtoupper($doc['type']) == "TEXTAREA"){
							$this->formTextAreaField($doc, $thingData[$index], $index, $prefix);
						}
						if(strtoupper($doc['type']) == "NOTES"){
							if(!empty($thingData[$index])){
								$this->formNotesDisplay($doc, $thingData[$index], $index, $prefix);
							}
						}
						if(strtoupper($doc['type']) == "SELECT"){
							$this->formSelectField($doc, $thingData[$index], $index, $prefix);
						} 
						$fieldCount++;
					}
				}
			}
		}
		if($endRow === true){
			echo "</div>";   
			echo "</div>";  
		}
	}
	/*
    *  FORM FIELDS
    *
    *
    *
    *
    */
	function formPasswordField($field, $thingInfo, $index, $prefix=false){
		$field_type = "password"; 
		if(!empty($field['name'])){
			if (strpos(strtolower($field['name']), 'email') !== FALSE)
			{
				//$field_type = "email";
			}
		}
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			<?php echo $this->getValue($field,'label'); ?>
		</label>
		<input type="<?php echo $field_type; ?>" name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" value="<?php echo $this->getValue($thingInfo,$field['name']); ?>" class="form-control " <?php if($this->getValue($field,'required')){ echo "required=true"; } ?> placeholder="
            <?php echo $this->getValue($field,'placeholder')?>">
	</div>
</div>
<?php
	}
	function formTextField($field, $thingInfo, $index, $prefix=false){
		$field_type = "text"; 
		if(!empty($field['name'])){
			if (strpos(strtolower($field['name']), 'email') !== FALSE)
			{
				//$field_type = "email";
			}
		}
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			<?php echo $this->getValue($field,'label'); ?>
		</label>
		<input type="<?php echo $field_type; ?>" name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" value="<?php echo $this->getValue($thingInfo,$field['name']); ?>" class="form-control " <?php if($this->getValue($field,'required')){ echo "required=true"; } ?> placeholder="
            <?php echo $this->getValue($field,'placeholder')?>">
	</div>
</div>
<?php
	}
	/*
    *  FORM FIELDS
    *
    *
    *
    *
    */
	function formNotesField($field, $thingInfo, $index, $prefix=false){
		$field_type = "text"; 
		print_r($field);
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			<?php echo $this->getValue($field,'label'); ?>
		</label>
		<textarea name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" value="<?php echo $this->getValue($thingInfo,$field['name']); ?>" class="form-control" placeholder="<?php echo $this->getValue($field,'placeholder')?>">
			<?php echo $this->getValue($thingInfo,$field['name'])?>
		</textarea>
	</div>
</div>
<?php
	}
	function formNotesDisplay($field, $thingInfo, $index, $prefix=false){
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			Posted On:
			<?php echo date("m/d/Y H:i:s", strtotime($this->getValue($thingInfo,"_timestampCreated"))); ?> By: <?php echo $this->getUserName($this->getValue($thingInfo,"_createdBy"));?>
		</label>
		<div class="form-notes">
			<pre><?php echo trim($this->getValue($thingInfo,"information"), " \t\n\r\0\x0B"); ?></pre>
		</div>
	</div>
</div>
<?php
																		}
	/*
    *  FORM FIELDS
    *
    *
    *
    *
    */
	function formDateField($field, $thingInfo,  $index, $prefix=false){
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			<?php echo $this->getValue($field,'label'); ?>
		</label>
		<input type="text" name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" value="<?php echo $this->getValue($thingInfo,$field['name']); ?>" class="form-control  datepicker" <?php if($this->getValue($field,'required')){ echo "required=true"; } ?> placeholder="
                        <?php echo $this->getValue($field,'placeholder')?>">
	</div>
</div>
<?php
																	  }
	/*
    *  FORM FIELDS
    *
    *
    *
    *
    */
	function formTextAreaField($field, $thingInfo,  $index, $prefix=false){
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group">
		<label>
			<?php echo $this->getValue($field,'label'); ?>
		</label>
		<textarea name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" value="<?php echo $this->getValue($thingInfo,$field['name']); ?>" class="form-control" placeholder="<?php echo $this->getValue($field,'placeholder')?>"><?php echo $this->getValue($thingInfo,$field['name'])?></textarea>
	</div>
</div>
<?php
																		  }
	/*
    *  FORM FIELDS
    *
    *
    *
    *
    */
	function formSelectField($field, $thingInfo,  $index, $prefix=false){
?>
<div class=" col-sm-12 col-md-<?php echo $field['columns'];?>">
	<div class="form-group ">
		<label>
			<?php echo $this->getValue($field,'label'); ?> 
		</label>
		<div class="input-group col-xs-12">
			<?php
																		 $selected_value =  $this->getValue($thingInfo,$field['name']);   
																		 $disabled = "";
																		 $dataSourceOptions = "";                                                          
																		 if(!empty($field['dataSource'])){
																			 if($field['dataSource'] == "userList"){
																				 $options = $this->userList($field, $selected_value);   
																			 }
																			 if($field['dataSource'] == "carrierList"){
																				 $options = $this->carrierList();   
																			 }
																			 if($field['dataSource'] == "carrierPlanList"){
																				 $options = $this->carrierPlanList();   
																			 }
																			 if($field['dataSource'] == "userSellersList"){
																				 $options = $this->userSellersList($selected_value);   
																			 }
																			 if($field['dataSource'] == "userAgentsList"){
																				 $options = $this->userAgentsList($selected_value);   
																			 }
																			 if(!empty($options)){
																				 foreach($options as $oKey=>$oValue){
																					 $selected = "";
																					 if(empty($selected_value)){
																						 if(!empty($oValue['selected_value'])){
																							 $selected_value = $oValue['selected_value'];
																						 }
																					 }
																					 if(!empty($selected_value)){
																						 if($oValue['value'] == $selected_value){
																							 $selected = "selected";
																							 if((!empty($_SESSION['api']['user']['permissionLevel'])) && ($_SESSION['api']['user']['permissionLevel'] == "user")){
																								 //$disabled = "  disabled='disabled' ";
																							 }
																						 }
																					 }
																					 $dataSourceOptions .= "<option value='".$oValue['value']."' ".$selected." >".$oValue['label']."</option>";
																				 }
																			 }
																		 }                                                  
			?>
			<select name="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" id="<?php echo $prefix;?><?php echo $field['thing'];?>_<?php echo $index;?>_<?php echo $field['name'];?>" class=" form-control <?php echo  $field['attributes']['class']; ?>" <?php echo $disabled;?>
					>
				<?php
																		 if(!empty($field['options'])){
																			 //echo '<option value=""></option>';
																			 foreach($field['options'] as $okey=>$ovalue){
																				 $selected = "";
																				 if(strtoupper($selected_value) == strtoupper($ovalue['value'])){
																					 $selected = "selected";
																				 }
																				 if(isset($ovalue['value'])){
																					 if(empty($selected_value)){
																						 if((!empty($ovalue['default'])) && ($ovalue['default'] == "Y")){
																							 $selected = "selected";
																						 }
																					 }
																					 echo "<option value='".strtoupper($ovalue['value'])."' ".$selected.">".$ovalue['label']."</option>";
																				 } else {
																					 echo "<option value='".strtoupper($okey)."' ".$selected.">".$ovalue."</option>";
																				 }
																			 }
																		 } 
																		 if(!empty($field['dataSource'])){
																			 echo $dataSourceOptions;
																		 }
				?>
			</select>
		</div>
	</div>
</div>
<?php
																		}  
	function carrierList(){
		$carrierList = array();
		$this->mongoSetCollection("carrier");
		$collectionQuery = array('status' => 'ACTIVE');
		$cursor2 = $this->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				//
			} else {
				$cursor2->sort(array('name' => 1));
				$carrierList[] = array("value"=>"","label"=>"");
				foreach (iterator_to_array($cursor2) as $doc2) {
					$carrierInfo = $this->get_thing_display($doc2);
					$carrierList[$doc2['_id']]['value'] = $doc2['_id'];
					$carrierList[$doc2['_id']]['label'] = $carrierInfo['name'];
				}
			}
		}
		return $carrierList;
	}
	function carrierPlanList(){
		$carrierPlanList = array();
		$this->mongoSetCollection("carrierPlan");
		$collectionQuery = array('status' => 'ACTIVE');
		$cursor2 = $this->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				//
			} else {
				$cursor2->sort(array('name' => 1));
				$carrierPlanList[] = array("value"=>"","label"=>"");
				foreach (iterator_to_array($cursor2) as $doc2) {
					$carrierPlanInfo = $this->get_thing_display($doc2);
					$carrierPlanList[$doc2['_id']]['value'] = $doc2['_id'];
					$carrierPlanList[$doc2['_id']]['label'] = $carrierPlanInfo['name'];
				}
			}
		}
		return $carrierPlanList;
	}
	function userList($field = false, $selectedValue = FALSE){
		$userList = array();
		$userids = $this->getUserIdsSiblings();
		$this->mongoSetCollection("user");
		$collectionQuery['$or'][]['status'] = 'ACTIVE';
		$collectionQuery['$or'][]['status'] = 'active';
		$collectionQuery['$or'][]['_id'] = $selectedValue;
		$cursor2 = $this->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				//
			} else {
				$cursor2->sort(array('firstname' => 1));
				$userList[] = array("value"=>"","label"=>"");
				foreach (iterator_to_array($cursor2) as $doc2) {
					if((in_array($doc2['_id'], $userids)) || ($doc2['_id'] == $_SESSION['api']['user']['_id']) || ($doc2['_id'] == $selectedValue) ){
						$userInfo = $this->get_thing_display($doc2);
						if((!empty($field['dataDefault'])) && ($field['dataDefault'] = "userId")){
							$userList[$doc2['_id']]['selected_value'] = $_SESSION['api']['user']['_id']; 
						}
						$userList[$doc2['_id']]['value'] = $doc2['_id'];
						$userList[$doc2['_id']]['label'] = $userInfo['firstname'] . " " . $userInfo['lastname'];
					}
				}
			}
		}
		return $userList;
	}
	function userSellersList($previous_user_id="NONE"){
		$userList = array();
		$this->mongoSetCollection("user");
		$collectionQuery['$or'][0]['$or'][0]['status']['$eq'] = 'active';
		$collectionQuery['$or'][0]['$or'][1]['status']['$eq'] = 'ACTIVE';
		$collectionQuery['canSell']['$eq'] = 'Y';
		//$collectionQuery['licensed']['$eq'] = 'N';
		$collectionQuery['$or'][1]['_id']['$eq'] = $previous_user_id; 
		$cursor2 = $this->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				//
			} else {
				$cursor2->sort(array('firstname' => 1));
				$userList[] = array("value"=>"","label"=>"");
				foreach (iterator_to_array($cursor2) as $doc2) {
					$userInfo = $this->get_thing_display($doc2);
					$userList[$doc2['_id']]['value'] = $doc2['_id'];
					$userList[$doc2['_id']]['label'] = $userInfo['firstname'] . " " . $userInfo['lastname'];
				}
			}
		}
		return $userList;
	}
	function userAgentsList($previous_user_id="NONE"){
		$userList = array();
		$this->mongoSetCollection("user");
		$collectionQuery['$or'][0]['$or'][0]['status']['$eq'] = 'active';
		$collectionQuery['$or'][0]['$or'][1]['status']['$eq'] = 'ACTIVE';
		//$collectionQuery['canSell']['$eq'] = 'Y';
		$collectionQuery['licensed']['$eq'] = 'Y';
		$collectionQuery['$or'][1]['_id']['$eq'] = $previous_user_id; 
		$cursor2 = $this->mongoFind($collectionQuery);
		if(!empty($cursor2)){
			if($cursor2->count() == 0){
				//
			} else {
				$cursor2->sort(array('firstname' => 1));
				$userList[] = array("value"=>"","label"=>"");
				foreach (iterator_to_array($cursor2) as $doc2) {
					$userInfo = $this->get_thing_display($doc2);
					$userList[$doc2['_id']]['value'] = $doc2['_id'];
					$userList[$doc2['_id']]['label'] = $userInfo['firstname'] . " " . $userInfo['lastname'];
				}
			}
		}
		return $userList;
	}
}