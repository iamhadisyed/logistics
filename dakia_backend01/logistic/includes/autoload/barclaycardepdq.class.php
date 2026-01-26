<?php

////////////////////////////////////////////////////
//
// Class for dealing with Barclay Card EPDQ Payments
//
////////////////////////////////////////////////////

/**
 * Barclaycardepdq - Payment class for Barclay Card EPDQ Payments
 * @package Courier
 */
class Barclaycardepdq
{
	private 	$host			=	CONFIG_EDPQ_HOST;
	private 	$usepath		=	CONFIG_EDPQ_USEPATH;
	private 	$return_url		=	CONFIG_EDPQ_RETURN_URL;
	protected 	$oid			=	NULL;
	private 	$client			=	CONFIG_EDPQ_CLIENT;
	private 	$password		=	CONFIG_EDPQ_PASSWORD;
	private 	$charge_type	=	CONFIG_EDPQ_CHARGE_TYPE;
	private 	$currency_code	=	CONFIG_EDPQ_CURRENCY_CODE;

	protected 	$postdata		=	NULL;
	protected 	$total			=	NULL;

    /**
     * Constructor. Returns the object.
     * @return void
     */
    public function __construct()
    {
    }

    ////////////////////////////////////////////////////
    // Special methods
    ////////////////////////////////////////////////////

	/**
     * pullpage. Encrypt the transaction details, performs a HTTP Post and returns the whole response
     * @return void
     */
	public function pullpage()
	{
		// open socket to filehandle(epdq encryption cgi)
		$fp = fsockopen($this->host, 80, $errno, $errstr, 60 );

		// check that the socket has been opened successfully
		if(!$fp)
		{
			echo $errstr . "(" . $errno . ")" . "<br>\n";
			die("There was an error processing the payment");
		}

		// write the data to the encryption cgi
		fputs( $fp, "POST " . $this->usepath . " HTTP/1.0\n");
		$strlength = strlen($this->postdata);
		fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs( $fp, "Content-length: " . $strlength."\n\n" );
		fputs( $fp, $this->postdata . "\n\n" );

		// clear the response data
		$output = "";

		// read the response from the remote cgi
		while(!feof($fp))
		{
			$output .= fgets( $fp, 1024);
		}

		// close the socket connection
		fclose( $fp);

		// return the response
		return $output;

	}

	/**
     * getKey. Set up parameters and receive encrypted form element
     * @return void
     */
	public function getKey()
	{
		$key = NULL;

		// the following parameters have been obtained earlier in the merchant's webstore
		// clientid, passphrase, oid, currencycode, total
		$this->postdata  = 	"clientid=" . $this->getClient();
		$this->postdata .= 	"&password=" . $this->getPassword();
		$this->postdata .= 	"&oid=" . $this->getOid();
		$this->postdata .= 	"&chargetype=" . $this->getChargeType();
		$this->postdata .= 	"&currencycode=" . $this->getCurrencyCode();
		$this->postdata .= 	"&total=" . $this->getTotal();

		// perform the HTTP Post
		$response = $this->pullpage();

		// split the response into separate lines
		$response_lines = explode("\n",$response);

		// for each line in the response check for the presence of the string ‘epdqdata’
		// this line contains the encrypted string
		$response_line_count = count($response_lines);
		for ($i = 0; $i < $response_line_count; $i++)
		{
			if (preg_match('/epdqdata/',$response_lines[$i]))
			{
				$key = $response_lines[$i];
			}
		}

		return $key;

	}

	/**
     * response. Read transaction response
     * @return void
     */
	public function response()
	{
		if (!strcmp(getenv("REQUEST_METHOD"),"POST"))
		{
			$path		=""; #set your logfile directory path here
			$timestamp	=date("d-m-y--H-i-s");
			$FILE		=fopen($path . $timestamp. "-" . $oid . ".txt","a");
			fwrite($FILE,"OrderID - $oid\n");
			fwrite($FILE,"Transaction Status - $transactionstatus\n");
			fwrite($FILE,"Total - $total\n");
			fwrite($FILE,"ClientID - $clientid\n");
			fwrite($FILE,"Transaction Time Stamp - $datetime\n");
			fclose($FILE);
		}
	}

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getClient()                    {     return $this->client; }
    public function getPassword()                  {     return $this->password; }
    public function getOid()                	   {     return $this->oid; }
    public function getChargeType()                {     return $this->charge_type; }
    public function getCurrencyCode()              {     return $this->currency_code; }
    public function getTotal()                     {     return $this->total; }

    public function getPostdata()                  {     return $this->postdata; }
    public function getReturnUrl()                 {     return $this->return_url; }

    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setClient($client)         		{     $this->client = $client; }
    public function setPassword($password)         	{     $this->password = $password; }
    public function setChargeType($charge_type)     {     $this->charge_type = $charge_type; }
    public function setOid($oid) 					{     $this->oid = $oid; }
    public function setCurrencyCode($currency_code) {     $this->currency_code = $currency_code; }
    public function setTotal($total)         		{     $this->total = $total; }

    public function setPostdata($postdata)         	{     $this->postdata = $postdata; }
    public function setReturnUrl($return_url)       {     $this->return_url = $return_url; }

}
?>
