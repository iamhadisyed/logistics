<?php
/**
 * Reponse details from SagePay
 *
 */
class SagePayResponse
{
	const STATUS_OK = "OK";
	const STATUS_INVALID = "INVALID";
	const STATUS_ERROR = "ERROR";
	const STATUS_MALFORMED = "MALFORMED";
	//
	private $detail_array = array();

	/**
	 * Create with the response string from Sage pay
	 *
	 * @param string $response
	 */
	public function __construct($response)
	{
		// decode message
		$line_array = split("\n",$response);
		// split lines
		foreach ($line_array as $line)
		{
			$pos = strPos($line, "=");
			// check something either side of = sign!
			if (($pos > 0) && (strLen($line) > $pos + 1))
			{
				$key = trim(substr($line,0, $pos));
				$this->detail_array[$key] = trim(substr($line, $pos+1));
			}
		}
	}

	/**
	 * Status of Sage Pay Response
	 *
	 */
	public function getStatus()
	{
		$status = self::STATUS_ERROR;
		// check status returned
		if (isset($this->detail_array["Status"]))
		{
			switch ($this->detail_array["Status"])
			{
				case self::STATUS_OK:
				case self::STATUS_INVALID:
				case self::STATUS_MALFORMED:
					$status = $this->detail_array["Status"];
					break;
			}
		}
		return $status;
	}

	/**
	 * Sage Pays transaction id
	 *
	 * @return string
	 */
	public function getTransactionId()
	{
		return @$this->detail_array["VPSTxId"];
	}

	/**
     * Serialise this object
     *
     * @return string
     */
    public function serialize ()
    {
    	return serialize($this->detail_array);
    }
    
}

?>