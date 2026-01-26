<?php
/*
 * Provides mechanism to send and receive messages to a URL address.
 *
 */
class HttpCommunication
{
	private $ssl_verify = false;
	private $url = "";
	private $port = 0;
	private $response = "";
	private $error = "No message posted.";


	/**
	 * Create communcation class.
	 *
	 * @param string $url - location to communicate with.
	 */
	public function __construct ($url, $port=0)
	{
		$this->url = $url;
		$this->port = $port;
	}

	/**
	 * Turn SSL verification on/off
	 *
	 * @param bool $status
	 */
	public function setSSLVerify ($status)
	{
		$this->ssl_verify = $status;
	}

	/**
	 * Performs an HTTP post of given message to the configured URL
	 *
	 * @param string xml msg
	 * @return bool indicate if got a success response.
	 *
	 */
	public function post($msg)
	{
		return $this->send($msg, true);
	}

	/**
	 * Performs an HTTP get of given message to the configured URL
	 *
	 * @param string xml msg
	 * @return bool indicate if got a success response.
	 *
	 */
	public function get($msg)
	{
		return $this->send($msg, false);
	}


	/**
	 * Sends message to url.
	 * Returns boolean to indicate if successful.
	 * Use getResponse method to read return message.
	 *
	 * @param string $msg - message to send
	 * @param bool $postFlag - indicates if should do post or get.
	 * @return bool - indicates success
	 */
	private function send($msg, $postFlag)
	{
		if ($this->url == "") return false;
		$this->error = "";
		//t($this->url);
		//t($msg);

		// create a new cURL resource
		$ch = curl_init();
		try
		{
			// set URL and other appropriate options
			if ($postFlag)
			{
				curl_setopt($ch, CURLOPT_URL, $this->url);
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
			}
			else
			{
				$url = $this->url . "?" . $msg;
				curl_setopt($ch, CURLOPT_URL, $url);
			}
			//
			//curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, $postFlag);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 180);
			//
			if ($this->port > 0) curl_setopt($ch, CURLOPT_PORT, $this->port);
			if ($this->ssl_verify)
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
			}
			// grab URL and pass it to the browser
			$this->response = curl_exec($ch);
			//t($this->response);
		}
		catch (Exception $e)
		{
			$this->error = $e;
			t($e, __METHOD__);
		}
		// close cURL resource, and free up system resources
		curl_close($ch);
		//
		return ($this->error == "");
	}


	/**
	 * Get response message from communcation.
	 *
	 * @return string
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * If error occurs during communcation this method
	 * gives error information.
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}


	/**
	 * Request a given page from this site without waiting for response.
	 * Allows scripts to be ran without pausing curent scripts.
	 *
	 * @param string $relative page to script to run
	 * @return bool - able to post to given page.
	 */
	public static function asynchronousPageCall($scriptRelativePath)
	{
		// determine the full url of script
		$url = dirname( $_SERVER["SCRIPT_NAME"]) . "/" . $scriptRelativePath;
		//
		$host = $_SERVER["HTTP_HOST"];
		//
		self::sendRequestIgnoreResponse($host, $url, "dummy_content");
	}

	/**
	 * Request a given page.  Returns immediately
	 * without waiting for response.
	 * Allows scripts to be ran without pausing curent
	 * scripts.
	 *
	 * @param string $host
	 * @param $uri (path without host name).
	 * @param string $content
	 * @return bool - able to post to given page.
	 */
	public static function sendRequestIgnoreResponse($host=null,$uri=null,$content=null) {
	    if (empty($host))       { return false; }
	    if (empty($uri))        { return false; }
	    if (empty($content))    { return false; }
	    //
	    $port=80;
	    // generate headers in array.
	    $t   = array();
	    $t[] = 'POST ' . $uri . ' HTTP/1.1';
	    $t[] = 'Content-Type: text/html';
	    $t[] = 'Host: ' . $host . ':' . $port;
	    $t[] = 'Content-Length: ' . strlen($content);
	    $t[] = 'Connection: close';
	    $t   = implode("\r\n",$t) . "\r\n\r\n" . $content;
	    //
	    // Open socket, provide error report vars and timeout of 10
	    // seconds.
	    //
	    $fp  = @fsockopen($host,$port,$errno,$errstr,10);
	    // If we don't have a stream resource, abort.
	    if (!(get_resource_type($fp) == 'stream')) {return false; }
	    //
	    // Send headers and content.
	    //
	    if (!fwrite($fp,$t)) {
	        fclose($fp);
	        return false;
	   	}
	    fclose($fp);

	    return true;
	}
}
