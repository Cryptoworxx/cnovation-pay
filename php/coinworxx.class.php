<?php

class Coinworxx
{
	var $url = "https://coinworxx.com/api";
	var $token = false;
	
	public function __construct($token=false)
	{
		$this->token = $token;
	}
	
	protected function callApi($path,$arguments=array())
	{
		$url = "{$this->url}/{$this->token}/{$path}";
		if( count($arguments)>0 )
			$rul .= "?".http_build_query($arguments);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		if( !$response )
			throw new CoinWorxxException("CURL: {$error}");
		
		$result = json_decode($response,true);
		if( !$result )
			throw new CoinWorxxException("INVALID JSON: {$response}");
		return $result;
	}
	
	public function info() { return $this->callApi(""); }
	
	public function currencies() { return $this->callApi("currencies"); }
	public function currency($code) { return $this->callApi("currencies/{$code}"); }

	public function wallets() { return $this->callApi("wallets"); }
	public function wallet($uid) { return $this->callApi("wallets/{$uid}"); }
	
	public function payments() { return $this->callApi("payments"); }
	public function payment($uid) { return $this->callApi("payments/{$uid}"); }
}

class CoinworxxException extends \Exception { }