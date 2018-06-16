<?php
class eapi
{
	public $url;
	public $clientCode;
	public $username;
	public $password;
	public $sslCACertPath;

public function __construct($url = null, $clientCode = null, $username = null, $password = null, 
	$sslCACertPath = null)
{

	$this-> url = $url;
	$this-> clientCode = $clientCode;
	$this-> username = $username;
	$this-> password = $password;
	$this-> sslCACertPath = $sslCACertPath;
}

public function getSessionKey()
{
	if (!isset($_SESSION)) throw new Exception('PHP session not started', self::PHP_SESSION_NOT_STARTED);
	{
		if (!isset($_SESSION['EAPISessionKey'][$this->clientCode][$this->username])||
	        $_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username] < time())
		{
			$result = $this->sendRequest("verifyUser",array("username" => $this->username, "password" => $this->password));
			$results = json_decode($result,true);


			$_SESSION['EAPISessionKey'][$this->clientCode][$this->username] = $results['records'][0]['sessionKey'];
			$_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username] = $results['records'][0]['sessionLength'] - 30;
		}
	}
	return $_SESSION['EAPISessionKey'][$this->clientCode][$this->username];
}
public function sendRequest($request, $parameters=array())
{
	$parameters['request'] = $request;
	$parameters['clientCode'] =$this->clientCode;
	$parameters['version'] = '1.0';
	$parameters['username'] = $this->username;
	$parameters['password'] = $this->password;

	if($request != "verifyUser")
	{
		$parameters["sessionKey"] = $this -> getSessionKey();
	}

	$handle = curl_init($this->url);

	//set the payload
	curl_setopt($handle, CURLOPT_POST, true);
	curl_setopt($handle, CURLOPT_POSTFIELDS, $parameters);
	
	//return body only
	curl_setopt($handle, CURLOPT_HEADER, 0);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
	
	//create errors on timeout and on response code >= 300
	curl_setopt($handle, CURLOPT_TIMEOUT, 45);
	
	//set up host and cert verification
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	if($this->sslCACertPath) {
		curl_setopt($handle, CURLOPT_CAINFO, $this->sslCACertPath);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
	}
	//run
	$response = curl_exec($handle);
	$error = curl_error($handle);
	$errorNumber = curl_errno($handle);
	curl_close($handle);
	if($error) throw new Exception('CURL error: '.$response.':'.$error.': '.$errorNumber, self::CURL_ERROR);
	return $response;


}

}