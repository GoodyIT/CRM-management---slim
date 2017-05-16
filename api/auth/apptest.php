<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
session_cache_limiter(false);
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use UAParser\Parser;
$app = new \Slim\Slim(array(
    'cookies.lifetime' => '2 days',
    'cookies.encrypt' => true,
    'cookies.secret_key' => $settings['encrypted_key'],
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

/*
<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
session_cache_limiter(false);
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
use UAParser\Parser;
$app = new \Slim\Slim(array(
    'cookies.lifetime' => '2 days',
    'cookies.encrypt' => true,
    'cookies.secret_key' => $settings['encrypted_key'],
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

*/

/*
Set User Agent Data 
*/
$cookieValue = $app->getEncryptedCookie('userCookie'); //NULL if not set
if ( $cookieValue ) {
    //echo "<P>user!";
} else {
    //echo "<P>notUser!";
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
    $collection->insert($result);
}
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
API CLASS
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
        if((empty($_SESSION['api']['user']['_id'])) && (trim($_SESSION['api']['user']['_id']) == "")) {
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
        $collectionQuery = array("_id"=>$user_id, "status"=>"active");
        $item = $this->mongofindOne($collectionQuery);
        if(empty($item)){
            return false;
        } else {
            if($reset_user === TRUE){
                $_SESSION['api']['user'] = $this->get_thing_display($item);
            }
            return true;
        }
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
    function mongoUpdate($mongoCriteria, $collectionUpdates, $createNew = FALSE)
    {
        try{
            if($createNew === TRUE){
                $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->update($mongoCriteria, array('$set' => $collectionUpdates), array("upsert" => true));
            } else {
                $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->update($mongoCriteria, array('$set' => $collectionUpdates));
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
            $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->ensureIndex(array($field => 1));
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
            $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->ensureIndex(array($field => -1));
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
            $this->mongo['client']->{$this->mongo['db']}->{$this->mongo['collection']}->ensureIndex(array($field => 1), array('unique' => true));
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
    /*
*/
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    function formatDateToSeconds($date){
        try{
            $date_string = new DateTime($date);
            return date_format($date_string, 'U');
        }
        catch (Exception $e)
        {
        }
        try{
            $date = date_create($date_string);
            return date_format($date, 'U');
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
            return $encrypted_string;
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
        $output = openssl_decrypt(base64_decode($encrypted_string), $encrypt_method, $key, 0, $iv);
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
    function getRandomId($parts=3, $part_size=8){
        $id = $this->getRandomString($part_size);
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
                        $thing[$key][$key2] = $this->display_thing_value($key2,$value2);
                    }
                } else {
                    $thing[$key] = $this->display_thing_value($key,$value);
                }
            }
        }
        catch (\Exception $e) {
        }
        return $thing;
    }
    /*
    *   Format Thing Values to save to database
    */
    function format_thing_value($key,$value){
        // countrycode = 2 letter code
        // stateCodeUS = 2 letter state
        // stateNameUS = full state name
        // socialsecurity = encrypt
        if (strpos(strtolower($key), 'socialsecurity') !== FALSE)
        {
            $value = $this->setEncrypt($value);
        }
        // socialsecurity = encrypt
        if (strpos(strtolower($key), 'ssn') !== FALSE)
        {
            $value = $this->setEncrypt($value);
        }
        // bank = encrypt
        if (strpos(strtolower($key), 'bank') !== FALSE)
        {
            $value = $this->setEncrypt($value);
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
        // date == FORMAT DATE !!!
        if (strpos(strtolower($key), 'date') !== FALSE)
        {
            $value = $this->formatDateToSeconds($value);
        }
        // phone = format
        if (strpos(strtolower($key), 'phone') !== FALSE)
        {
            $value = $this->displayPhoneNumber($value,TRUE);
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
        return $value;
    }
    /*
    *   Format Thing Values for Display from database
    */
    function display_thing_value($key,$value){
        if (strpos(strtolower($key), 'socialsecurity') !== FALSE)
        {
            $value = $this->getDecrypt($value);
        }
        if (strpos(strtolower($key), 'ssn') !== FALSE)
        {
            $value = $this->getDecrypt($value);
        }
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
        if (strpos(strtolower($key), 'date') !== FALSE)
        {
            $value =  date($this->settings['date_format'], $value);
        }
        if (strpos(strtolower($key), 'time') !== FALSE)
        {
            $value =  date($this->settings['time_format'], $value);
        }
        if (strpos(strtolower($key), 'phone') !== FALSE)
        {
            $value = $this->displayPhoneNumber($value);
        }
        if (strpos(strtolower($key), 'fax') !== FALSE)
        {
            $value = $this->displayPhoneNumber($value);
        }
        return $value;
    }
    /*
    *   Small function to parse Key String into Multidimensional Array
    */
    function   create_thing_array($post_array, $parent_id = FALSE, $parent_thing = FALSE, $parent_key = FALSE ){
        foreach($post_array as $key1=>$value)
        {
            if(is_array($value))
            {
                foreach($value as $key2=>$doc)
                {
                    $_temp_thing = array();
                    $_id=FALSE;
                    if (!empty ($doc['id']))
                    {
                        // Set key with right _id format for Mongo
                        $_id= $doc['id'];
                    }
                    if (!empty ($doc['_id']))
                    {
                        $_id = $doc['_id'];
                    }
                    if(empty($_id))
                    {
                        $_id = $this->getRandomId();
                        $_temp_thing['_dateCreated'] = date("U");
                        $_temp_thing['_dateModified'] = date("U");
                    } else {
                        $_temp_thing['_dateModified'] = date("U");
                    }
                    unset($doc['id']);
                    $_temp_thing['_id'] = $_id;
                    $_temp_thing['_parentId'] = $parent_id;
                    $_temp_thing['_parentThing'] = $parent_thing;
                    //$_temp_thing['_parentKey'] = $parent_key;
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
                                $_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
                            }
                        }
                    }
                    if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE) || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
                        $this->things[$key1][] = $_temp_thing;
                    }
                }
            }
        }
        return false;
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
                    }
                    if (!empty ($doc['_id']))
                    {
                        $_id = $doc['_id'];
                    }
                    if(empty($_id))
                    {
                        $_id = $this->getRandomId();
                        $_temp_thing['_dateCreated'] = date("U");
                        $_temp_thing['_dateModified'] = date("U");
                    } else {
                        $_temp_thing['_dateModified'] = date("U");
                    }
                    unset($doc['id']);
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
                            if($temp_value){
                                $_temp_thing[$key3] = utf8_encode(strip_tags($temp_value));
                            }
                        }
                    }
                    if((isset($doc['createThing'])) && ( ($doc['createThing'] === TRUE) || ($doc['createThing'] === 1) || ($doc['createThing'] === "TRUE") ) ){
                        $this->things[$child_thing][] = $_temp_thing;
                    } else {
                        $return_children[] = $_temp_thing;   
                    }
                }
            }
        }
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
         foreach ($post_array AS $key => $value)
         {
            $keys = explode("_", $key);
            $thing_post = $this->parse_post_to_array($newarr, $keys, $value);
         }
         return $thing_post;
    }
}