<?php
class twilioPlugin
{
	public $token;
	public $clientName;
	public $authToken;
	public $accountSid;
	public $outKey;
	public $number;
	public $servies_twilio;
	public $callerId;
	public $apiUri;
	function __construct($settings = false)
	{
		$this->accountSid = $settings['twilio']['accountSid'];
		$this->authToken = $settings['twilio']['authToken'];
		$this->clientName =  $settings['twilio']['clientName'];
		$this->appId =  $settings['twilio']['appId'];
		$this->callerId = $settings['twilio']['callerId'];
		$this->number = $settings['twilio']['number'];
		$this->apiUri = 'https://api.twilio.com/2010-04-01/Accounts/'. $this->accountSid;
		$this->_soft_phone();
	}
	function _soft_phone()
	{
		$this->token = new Services_Twilio_Capability($this->accountSid, $this->authToken);
		$this->token->allowClientOutgoing($this->appId);
		$this->token->allowClientIncoming($this->clientName);
	}
	function _randm_string($valid_chars, $length)
	{
		$random_string = "";
		$num_valid_chars = strlen($valid_chars);
		for ($i = 0; $i < $length; $i++)
		{
			$random_pick = mt_rand(1, $num_valid_chars);
			$random_char = $valid_chars[$random_pick-1];
			$random_string .= $random_char;
		}
		return $random_string;
	}
}
