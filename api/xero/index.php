<?php
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Remote\Request;
use XeroPHP\Remote\URL;
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
require '../app.php';
$app->config(array(
	'templates.path' => './',
));
class array2xml extends DomDocument
{
	public $nodeName;
	private $xpath;
	private $root;
	private $node_name;
	public function __construct($root='root', $node_name='node')
	{
		parent::__construct();
		/*** set the encoding ***/
		$this->encoding = "ISO-8859-1";
		/*** format the output ***/
		$this->formatOutput = true;
		/*** set the node names ***/
		$this->node_name = $node_name;
		/*** create the root element ***/
		$this->root = $this->appendChild($this->createElement( $root ));
		$this->xpath = new DomXPath($this);
	}
	public function createNode( $arr, $node = null, $parentNode=null)
	{
		if (is_null($node))
		{
			$node = $this->root;
		}
		foreach($arr as $element => $value) 
		{
			$element = is_numeric( $element ) ? ($this->node_name=self::depluralize($parentNode)) : $element;
			$child = $this->createElement($element, (is_array($value) ? null : $value));
			$node->appendChild($child);
			if (is_array($value))
			{
				self::createNode($value, $child, $element);
			}
		}
	}
	function depluralize($word){
		$rules = array( 
			'ss' => false,
			'sses'=> 'ss',
			'os' => 'o', 
			'ies' => 'y', 
			'xes' => 'x', 
			'oes' => 'o', 
			'ies' => 'y', 
			'ves' => 'f', 
			's' => '');
		foreach(array_keys($rules) as $key){
			if(substr($word, (strlen($key) * -1)) != $key) 
				continue;
			if($key === false) 
				return $word;
			return substr($word, 0, strlen($word) - strlen($key)) . $rules[$key]; 
		}
		return $word;
	}
	public function __toString()
	{
		return $this->saveXML();
	}
	public function query($query)
	{
		return $this->xpath->evaluate($query);
	}
}
$apiObj = new apiclass($settings);
$app->map('/', function () use ($app, $settings, $mongo, $apiObj){
	require '../../vendor/xero/xerooauth-php/lib/XeroOAuth.php';
	define ( 'BASE_PATH', dirname(__FILE__) );
	define ( "XRO_APP_TYPE", "Private" );
	define ( "OAUTH_CALLBACK", "oob" );
	define ( "URL", "https://api.xero.com/api.xro/2.0/" );
	$useragent = "XeroOAuth-PHP Private App Test";
	$signatures = array (
		'consumer_key' => 'GYUVVMF0SO8HQS1ENLAJSM43PQMQGH',
		'shared_secret' => 'HFD3RYB6O3QKIULYOXSZIRGJJKXJPM',
		// API versions
		'core_version' => '2.0',
		'payroll_version' => '1.0',
		'file_version' => '1.0' 
	);
	if (XRO_APP_TYPE == "Private" || XRO_APP_TYPE == "Partner") {
		$signatures ['rsa_private_key'] = BASE_PATH . '/certs/privatekey.pem';
		$signatures ['rsa_public_key'] = BASE_PATH . '/certs/publickey.cer';
	}
	$XeroOAuth = new XeroOAuth ( array_merge ( array (
		'application_type' => XRO_APP_TYPE,
		'oauth_callback' => OAUTH_CALLBACK,
		'user_agent' => $useragent 
	), $signatures ) );
	include '../../vendor/xero/xerooauth-php/tests/testRunner.php';
	$initialCheck = $XeroOAuth->diagnostics ();
	$checkErrors = count ( $initialCheck );
	if ($checkErrors > 0) {
		// you could handle any config errors here, or keep on truckin if you like to live dangerously
		foreach ( $initialCheck as $check ) {
			echo 'Error: ' . $check . PHP_EOL;
		}
	} else {
		$session = persistSession ( array (
			'oauth_token' => $XeroOAuth->config ['consumer_key'],
			'oauth_token_secret' => $XeroOAuth->config ['shared_secret'],
			'oauth_session_handle' => '' 
		) );
		$oauthSession = retrieveSession ();
		if (isset ( $oauthSession ['oauth_token'] )) {
			$XeroOAuth->config ['access_token'] = $oauthSession ['oauth_token'];
			$XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];
		}
	}
	$inv= array(
		'Contact' => array(
			//'ContactID' => 'c49fd658-9936-49ab-9cb0-e1f84820afd4',
			'Name'=>'Test 4',
			'ContactStatus'=>'ACTIVE',
			'FirstName' => 'Martin',
			'LastName' => 'Dale',
			'EmailAddress' => 'martyd@citylim.co',
			'Addresses'=>array(
				array(
					'AddressType' => 'STREET',
					'AddressLine1' => '101 Green St, Fl 5',
					'City' => 'San Francisco',
					'Region' => 'CA',
					'PostalCode' => '94111',
					'Country' => 'USA',
					'AttentionTo'=>'Accounts Dept',
				),
				array(
					'AddressType' => 'STREET',
					'AddressLine1' => '101 BLue St, Fl 5',
					'City' => 'Los Angeles',
					'Region' => 'CA',
					'PostalCode' => '94111',
					'Country' => 'USA',
				),
			),
			'Phones'=>array(
				array(
					'PhoneType'=>'DEFAULT',
					'PhoneNumber'=>'9999',
					'PhoneAreaCode'=>'909',
				),
				array(
					'PhoneType'=>'Cell',
					'PhoneNumber'=>'923462346999',
					'PhoneAreaCode'=>'923',
				),
				array(
					'PhoneType'=>'Work',
					'PhoneNumber'=>'43636',
					'PhoneAreaCode'=>'344',
				),
			),
			'HasAttachments'=> 'false',
		),
		'Date' => '2015-10-10T00:00:00',
		'DueDate' => '2015-10-20T00:00:00',
		'Status' => 'AUTHORISED',
		'LineItems' => array(
			'LineItem'=>array(
				'Description'=>'Allstate, Are you in good hands?',
				'UnitAmount'=>'1000',
				'AccountCode'=>'400',
				'Quantity'=>'500',
			),
		),
		'LineAmountTypes'=>'Inclusive',
		'Type' => 'ACCREC',
		'HasAttachments' => 'false',
	);
	$pay=array(
		'Payment'=>array(
			'Invoice'=>array(
				'InvoiceID'=>'36d848f1-8653-45bb-a67f-1121490ba1fd',
			),
			'Account'=>array(
				'Code'=>'400',
			),
			'Date'=>date('2015-06-22'),
			'Amount'=>'150000',
		),
	);
	$xml= new array2xml('Payments');
	$xml->createNode($pay);
	$contacts = $XeroOAuth->request('GET',URL.'Contacts');//,'',$xml);
	$response = $app->response();
	$response['Content-Type'] = 'application/json';
	$response->status(200);
	//$response->body($contacts['response']);
	$xml=simplexml_load_string($contacts['response']);
	$json=print_r($xml);
	echo '<pre>';echo $json;
})->via('GET','POST');
$app->map('/callback', function () use ($app, $settings, $mongo, $apiObj){
	echo 'called back yo';
})->via('GET','POST');
$app->run();