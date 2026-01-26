<?php
/**
 * Sage Pay Message Communication Class
 * Sets up payment request message, sends request and gets response.
 *
 */
class SagePayComm
{
	const OPERATION_MODE_DUMMY = "dummy";
	const OPERATION_MODE_TEST = "test";
	const OPERATION_MODE_LIVE = "live";

	const VENDOR_NAME = "globallogistics";

	const DUMMY_SAGEPAY_TRANSACTION_ID = "{E61EC7BE-6195-0E7B-E5DB-BD195624FDB6}";

	private static $operation_mode = self::OPERATION_MODE_LIVE;
	private static $debug_emails = false;
	//
	private $error_array = array();
	private $field_array = array();
	private $sagepay_response = null;
	private $required_array = array(
								"VPSProtocol",
								"TxType",
								"Vendor",
								"VendorTxCode",
								"Amount",
								"Currency",
								"Description",
								"NotificationURL",
								"BillingSurname",
								"BillingFirstNames",
								"BillingAddress1",
								"BillingCity",
								"BillingPostcode",
								"BillingCountry",
								"DeliverySurname",
								"DeliveryFirstNames",
								"DeliveryAddress1",
								"DeliveryCity",
								"DeliveryPostcode",
								"DeliveryCountry"
								);

	/**
	 * Create sage pay comms object
	 *
	 */
	public function __construct ()
	{
		// set defaults
		$this->field_array["VPSProtocol"] = "2.23";
		$this->field_array["TxType"] = "PAYMENT";
		$this->field_array["Vendor"] = self::VENDOR_NAME;
		$this->field_array["Currency"] = "GBP";
	}


	/**
	 * Different modes of operation:
	 * 	1. Dummy mode, uses local pages to simulator payment
	 *  2. Test mode, uses the sage pay test server.
	 *  3. Live mode, use the saye pay liver server.
	 *
	 * @param Flag - operation mode
	 */
	public static function setOperationMode ($operation_mode)
	{
		switch ($operation_mode)
		{
			case self::OPERATION_MODE_LIVE:
			case self::OPERATION_MODE_TEST:
				self::$operation_mode = $operation_mode;
				break;
			default:
				self::$operation_mode = self::OPERATION_MODE_DUMMY;
				break;
		}
	}
	public static function getOperationMode()
	{
		return self::$operation_mode;
	}

	/***
	 * Turn debug emails on/off
	 */
	public static function setDebugEmail ($debug_flag)
	{
		self::$debug_emails = $debug_flag;
	}

	/***
	 * Page to post to
	 */
	public function getPostPage ()
	{
		switch (self::$operation_mode)
		{
			case self::OPERATION_MODE_LIVE:
				return "https://live.sagepay.com/gateway/service/vspserver-register.vsp";
				break;
			case self::OPERATION_MODE_TEST:
				return "https://test.sagepay.com/gateway/service/vspserver-register.vsp";
				break;
			default:
				return SETTING_MAIN_URL . "payment/sagepay_dummy_response.php";
				break;
		}
	}

	/**
	 * Set client transaction code
	 *
	 * @param string $value
	 */
	public function addTransactionCode($value)
	{
		$this->field_array["VendorTxCode"] = $value;
	}

	/**
	 * Trasaction amount
	 *
	 * @param numeric $value
	 */
	public function addAmount($value)
	{
		$this->field_array["Amount"] = $value;
	}

	/**
	 * Transaction description
	 *
	 * @param string $value
	 */
	public function addDescription ($value)
	{
		if (strlen($value) > 100) $value = substr($value, 0, 100);
		$this->field_array["Description"] = $value;
	}

	/**
	 * Notification URL
	 *
	 * @param string $value
	 */
	public function addNotificationURL($value)
	{
		$maxLen = 255;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["NotificationURL"] = $value;
	}

	/**
	 * Billing Surname
	 *
	 * @param string $value
	 */
	public function addBillingSurname($value)
	{
		$maxLen = 20;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingSurname"] = $value;
	}

	/**
	 * First name
	 *
	 * @param string $value
	 */
	public function addBillingFirstnames($value)
	{
		$maxLen = 20;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingFirstNames"] = $value;
	}

	/**
	 * First line of booking address
	 *
	 * @param string $value
	 */
	public function addBillingAddressLine1 ($value)
	{
		$maxLen = 100;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingAddress1"] = $value;
	}

	/**
	 * Second line of booking address
	 *
	 * @param string $value
	 */
	public function addBillingAddressLine2 ($value)
	{
		if (trim ($value) == "") return;
		$maxLen = 100;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingAddress2"] = $value;
	}


	/**
	 * Billing City
	 *
	 * @param string $value
	 */
	public function addBillingCity($value)
	{
		$maxLen = 40;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingCity"] = $value;
	}

	/**
	 * Billing Postcode
	 *
	 * @param string $value
	 */
	public function addBillingPostcode($value)
	{
		$maxLen = 10;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingPostcode"] = $value;
	}

	/**
	 * Billing Country
	 *
	 * @param string $value
	 */
	public function addBillingCountry($value)
	{
		$maxLen = 2;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["BillingCountry"] = $value;
	}

	/**
	 * Delivery Surname
	 *
	 * @param string $value
	 */
	public function addDeliverySurname($value)
	{
		$maxLen = 20;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliverySurname"] = $value;
	}

	/**
	 * First name
	 *
	 * @param string $value
	 */
	public function addDeliveryFirstnames($value)
	{
		$maxLen = 20;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryFirstNames"] = $value;
	}

	/**
	 * First line of booking address
	 *
	 * @param string $value
	 */
	public function addDeliveryAddressLine1 ($value)
	{
		$maxLen = 100;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryAddress1"] = $value;
	}
	/**
	 * Second line of booking address
	 *
	 * @param string $value
	 */
	public function addDeliveryAddressLine2 ($value)
	{
		if (trim($value) == "") return;
		$maxLen = 100;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryAddress2"] = $value;
	}

	/**
	 * Delivery City
	 *
	 * @param string $value
	 */
	public function addDeliveryCity($value)
	{
		$maxLen = 40;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryCity"] = $value;
	}

	/**
	 * Delivery Postcode
	 *
	 * @param string $value
	 */
	public function addDeliveryPostcode($value)
	{
		$maxLen = 10;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryPostcode"] = $value;
	}

	/**
	 * Delivery Country
	 *
	 * @param string $value
	 */
	public function addDeliveryCountry($value)
	{
		$maxLen = 2;
		if (strlen($value) > $maxLen) $value = substr($value, 0, $maxLen);
		$this->field_array["DeliveryCountry"] = $value;
	}

	/**
	 * Check all fields required have been configured.
	 *
	 * @return unknown
	 */
	public function validate()
	{
		$this->error_array = array();
		//
		foreach ($this->required_array as $field)
		{
			if (!isset($this->field_array[$field]))
			{
				$this->error_array[] = "The $field field has not be configured.";
			}
			// Check valuis is not empty
			else if ($this->field_array[$field] == "")
			{
				$this->error_array[] = "The $field field is empty.";
			}
		}
		//
		return (sizeof($this->error_array) == 0);
	}
	/**
	 * Sends payment request to Sage Pay server
	 *
	 * @return bool - indicate if SENT ok.
	 */
	public function send()
	{
		// Check we have the rquired fields
		if (!$this->validate()) return false;

		// Build the post request
		$post = "";
		foreach ($this->required_array as $key)
		{
			if ($post != "") $post .= "&";
			$post .= $key . "=" . $this->field_array[$key];
		}
		//
		$server = $this->getPostPage();
		//echo $server; die;

		// Post to server
		$httpComm = new HttpCommunication($server);
		$httpComm->setSSLVerify(true);
		$httpComm->post($post);
		$response = $httpComm->getResponse();

		if (self::$debug_emails)
		{
			EmailSend::EmailError($post . "\r\n\r\n" . $response, "SagepayComm Send");
		}
		$this->sagepay_response = new SagePayResponse($response);

		return true;
	}

	/**
	 * Gives the sage pay response object for a successful communication
	 *
	 */
	public function getSagePayResponse()
	{
		if ($this->sagepay_response == null)
		{
			$this->sagepay_response = new SagePayResponse("");
		}
		return $this->sagepay_response;
	}

	/**
	 * Returns an error of errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->error_array;
	}
}