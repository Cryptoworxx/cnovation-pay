<?php

class CoinworxxClient
{
	var $url = "https://coinworxx.com/api";
	var $token = false;
    var $response = false;
    var $success = false;
    var $result = false;
    var $error = false;
	
	public function __construct($token=false)
	{
		$this->token = $token;
	}
	
	protected function callApi($path,$arguments=array())
	{
        $this->response = $this->success = $this->result = $this->error = false;
        
		$url = "{$this->url}/{$this->token}/{$path}";
        
        $arguments = array_filter($arguments,function($a){ return !is_null($a); });
		if( count($arguments)>0 )
			$url .= "?".http_build_query($arguments);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		$this->response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		if( !$this->response )
			throw new CoinworxxException("CURL: {$error}");
		
		$json = json_decode($this->response,true);
		if( !$json )
			throw new CoinworxxException("INVALID JSON: {$this->response}");
            
        $this->success = $json['success'];
        
        if( !$this->success )
        {
            if( !isset($json['code']) || !$json['code'] )
                $json['code'] = 'ERR_UNKNOWN';
            $this->error = ['code'=>$json['code']];
            if( isset($json['message']) )
                $this->error['message'] = $json['message'];
            if( isset($json['info']) )
                $this->error['info'] = $json['info'];
            return false;
        }
        $this->result = $json['result'];
		return $this->result;
	}
	
	public function info() { return $this->callApi(""); }
	
	public function currencies() { return $this->callApi("currencies"); }
	public function currency($code) { return $this->callApi("currencies/{$code}"); }

	public function wallets() { return $this->callApi("wallets"); }
	public function wallet($uid) { return $this->callApi("wallets/{$uid}"); }
	
	public function payments() { return $this->callApi("payments"); }
	public function payment($uid) { return $this->callApi("payments/{$uid}"); }
	public function createPayment($currency, $price, $price_currency, $reference, $deadline=null, $callback=null) 
    {
        return $this->callApi(
            "payments/create",
            compact('currency','price','price_currency','reference','deadline','callback')
        );
    }
	public function cancelPayment($uid) { return $this->callApi("payments/cancel/{$uid}"); }
}

class CoinworxxException extends \Exception { }