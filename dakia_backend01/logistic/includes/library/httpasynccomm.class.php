<?php
class HttpAsyncComm
{
	private static $httpAsncnComm = null;
	private $handle_array = array();
	private $multi_handle = null;
	private $ssl_verify = false;
	private $error_array = array();
	private $port = 80;

	/***
	 * Only tet instance from factory method.
	 */
	private function _construct()
	{
	}

	/***
	 * Singleton factory method
	 *
	 * @return HttpAsyncCommFactory
	 */
	public static function HttpAsyncCommFactory ()
	{
		if (self::$httpAsncnComm == null)
		{
			self::$httpAsncnComm = new HttpAsyncComm();
		}
		return self::$httpAsncnComm;
	}

	/***
	 * Add a get command to asynchronous calls.
	 */
	public function get ($url)
	{
		$this->send($url, false);
	}

	/***
	 * Add a post command to asynchronous calls.
	 */
	public function post ($url, $msg)
	{
		$this->send($url, true, $msg);
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
	private function send($url, $postFlag, $msg="")
	{
		if ($url == "") return false;
		$this->error = array();
		t($url . " msg: " . $msg, __METHOD__);

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

			//create the multiple cURL handle
			if ($this->multi_handle == null)
			{
				$this->multi_handle = curl_multi_init();
			}
			//add the two handles
			curl_multi_add_handle($this->multi_handle, $ch);

			$active = 1;
			 do {
			     $mrc = curl_multi_exec($this->multi_handle, $active);
			 } while ($mrc == CURLM_CALL_MULTI_PERFORM);

		}
		catch (Exception $e)
		{
			$this->error[] = $e;
			t($e, __METHOD__);
		}
		// close cURL resource, and free up system resources
		$this->handle_array[] = $ch;
		//
		return (sizeof($this->error) == 0);
	}


	public function finish ()
	{
		if ($this->multi_handle == null) return;


		$active = true;
		$mrc = curl_multi_exec($this->multi_handle, $active);

		 while ($active && $mrc == CURLM_OK) {

		     if (curl_multi_select($this->multi_handle) != -1) {
		         do {
		             $mrc = curl_multi_exec($this->multi_handle, $active);
		         } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		     }
		 }

		$this->close();
		t_die();
	}

	public function close()
	{
		if ($this->multi_handle != null)
		{
			// remove all handles
			foreach ($this->handle_array as $ch)
			{
				curl_multi_remove_handle($this->multi_handle, $ch);
			}
			curl_multi_close($this->multi_handle);
		}
	}

	public function getErrorArray()
	{
		return $this->error_array;
	}
}