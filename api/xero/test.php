<?php
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
	public function __toString()
	{
		return $this->saveXML();
	}
	public function query($query)
	{
		return $this->xpath->evaluate($query);
	}
}

$xml = new array2xml('Account');
$array = array(
	'Name'=>'Test',
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
	'IsSupplier'=>'false',
	'IsCustomer'=>'true',
	'Balances'=>array(
		'AccountsReceivable'=>array('Outstanding'=> 114.5,'Overdue'=>3324.2),
		'AccountsPayable'=>array('Outstanding'=>0,'Overdue'=>0),
	),
	'HasAttachments'=> 'false',
);
$xml->createNode($array);
echo $xml;