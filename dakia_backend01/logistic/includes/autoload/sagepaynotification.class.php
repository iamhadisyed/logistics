<?php
/**
 * Response from Sage Pay following a transaction
 *
 */
class SagePayNotification
{
	const STATUS_OK = "OK";

	private $detail_array;
	private static $debug_email_flag = false;

	/**
	 * Create the notification object.
	 *
	 */
	public function __construct()
	{
		// Values are posted to page, so read from post array
		$this->detail_array = util_getPostArray();

		if (self::$debug_email_flag)
		{
			$msg_array = array();
			foreach ($this->detail_array as $key=>$val)
			{
				$msg_array[] = "$key = $val";
			}
			$msg = "Post values: " . util_formatArrayValues($msg_array, "\r\n");
			//
			EmailSend::EmailError($msg, "SagePageNotification Construct");
		}
	}

	/**
	 * Turn email debugging on/off.
	 *
	 * @param bool $flag
	 */
	public static function setDebugEmail($flag)
	{
		self::$debug_email_flag = $flag;
	}

	/**
	 * Transaction Id passed to sage pay
	 *
	 */
	public function getVendorTransactionCode ()
	{
		return @$this->detail_array["VendorTxCode"];
	}
	/**
	 * Sage Pays transaction Id
	 *
	 */
	public function getSagePayTransactionCode ()
	{
		return @$this->detail_array["VPSTxId"];
	}

	public function getStatus ()
	{
		return @$this->detail_array["Status"];
	}

	public function getStatusDetails ()
	{
		return @$this->detail_array["StatusDetails"];
	}


	public function getAuthorisationCode ()
	{
		return @$this->detail_array["TxAuthNo"];
	}

	public function getAvsCV2Check ()
	{
		return @$this->detail_array["AVSCV2"];
	}

	public function getAddressCheck()
	{
		return @$this->detail_array["AddressResult"];
	}

	public function getPostcodeCheck ()
	{
		return @$this->detail_array["PostCodeResult"];
	}

	public function getCV2Check ()
	{
		return @$this->detail_array["CV2Result"];
	}

	public function getGiftAid ()
	{
		return @$this->detail_array["GiftAid"];
	}

	public function getSecureStatus ()
	{
		return @$this->detail_array["3DSecureStatus"];
	}

	public function getEncodedSecureStatus()
	{
		return @$this->detail_array["CAVV"];
	}

	public function getAddressStatus()
	{
		return @$this->detail_array["AddressStatus"];
	}

	public function getPayerStatus()
	{
		return @$this->detail_array["PayerStatus"];
	}

	public function getCardType()
	{
		return @$this->detail_array["Cardtype"];
	}

	public function getLast4Digits()
	{
		return @$this->detail_array["Last4Digits"];
	}

	public function hasBeenTamperWith ($securityKey)
	{
		$key = $this->getSagePayTransactionCode() .
				$this->getVendorTransactionCode() .
				$this->getStatus() .
				$this->getAuthorisationCode() .
				SagePayComm::VENDOR_NAME .
				$this->getAvsCV2Check() .
				$securityKey .
				$this->getAddressCheck() .
				$this->getPostcodeCheck() .
				$this->getCV2Check() .
				$this->getGiftAid() .
				$this->getSecureStatus() .
				$this->getEncodedSecureStatus() .
				$this->getAddressStatus() .
				$this->getPayerStatus() .
				$this->getCardType() .
				$this->getLast4Digits();

		$sig = @$this->detail_array["VPSSignature"];
		//
		return ($sig == md5($key));
	}

}
