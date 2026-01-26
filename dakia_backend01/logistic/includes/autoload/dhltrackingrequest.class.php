<?php
/*
 * DHL Request Class
 *
 * This class is for creating a DHL XML request and then posting the request to the server.
 *
 *
 ***********************************************************/
class DhlTrackingRequest
{
	const DATE_FORMAT = "Y-m-d";
	const TIME_FORMAT = "H:i";

	const DETAIL_LAST = "LAST_CHECK_POINT_ONLY";
	const DETAIL_ALL = "ALL_CHECK_POINTS";

	const PIECES_ENABLED = "S";

	private $dom;

	/**
	 * Create the DOM and add root element
	 *
	 */
	public function __construct()
	{
		$this->dom = new DomDocument('1.0', 'UTF-8');

		// Set up the root of Dom document
		$root = $this->dom->createElementNS("http://www.dhl.com","req:KnownTrackingRequest");
		$this->dom->appendChild($root);
	}

	/**
	 * Set Service header information
	 *
	 * @param string $msgReference
	 * @param string $siteId
	 * @param string $password
	 */
	public function setHeaderInfo ($msgReference, $siteId, $password)
	{
		$parentNode = $this->getNode("//req:KnownTrackingRequest/Request/ServiceHeader");

		// Message reference has to be between 28-30 characters.
		$msgReference = substr(str_repeat("0", 28) . $msgReference, -28);

		// Add message time
		$time = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
		$node = $this->dom->createElement("MessageTime", $time);
		$parentNode->appendChild($node);
		//
		$node = $this->dom->createElement("MessageReference", $msgReference);
		$parentNode->appendChild($node);
		//
		$node = $this->dom->createElement("SiteID", $siteId);
		$parentNode->appendChild($node);
		//
		$node = $this->dom->createElement("Password", $password);
		$parentNode->appendChild($node);

		// Automatically add language after service header
		$parentNode = $this->getNode("//req:KnownTrackingRequest");
		$node = $this->dom->createElement("LanguageCode", "en");
		$parentNode->appendChild($node);

	}

	/**
	 * Set the airways bill
	 *
	 * @param string $awb_number
	 */
	public function setAirwayBill ($awb_number)
	{
		$parentNode = $this->getNode("//req:KnownTrackingRequest");
        if(is_array($awb_number)){
            foreach($awb_number as $awbnumber){
                $node = $this->dom->createElement("AWBNumber", $awbnumber);
                $parentNode->appendChild($node);
            }
        }else if(is_string($awb_number)) {
            $node = $this->dom->createElement("AWBNumber", $awb_number);
            $parentNode->appendChild($node);
        }
	}

    /**
     * Set the LP Number
     *
     * @param string $awb_number
     */
    public function setLPNumber ($lp_number)
    {
        $parentNode = $this->getNode("//req:KnownTrackingRequest");
        if(is_array($lp_number)){
            foreach($lp_number as $lpnumber){
                $node = $this->dom->createElement("LPNumber", $lpnumber);
                $parentNode->appendChild($node);
            }
        }else if(is_string($lp_number)) {
            $node = $this->dom->createElement("LPNumber", $lp_number);
            $parentNode->appendChild($node);
        }
    }

	public function setDetailLevel($theLevel)
	{
		$level = self::DETAIL_ALL;
		if ($theLevel == self::DETAIL_LAST) $level = $theLevel;
		//
		$parentNode = $this->getNode("//req:KnownTrackingRequest");
		//
		$node = $this->dom->createElement("LevelOfDetails", $level);
		$parentNode->appendChild($node);
	}
    public function setPiecesEnabled($piecesEnabled)
    {
        $_piecesEnabled = self::PIECES_ENABLED;
        if ($piecesEnabled != self::PIECES_ENABLED) $_piecesEnabled = $piecesEnabled;
        //
        $parentNode = $this->getNode("//req:KnownTrackingRequest");
        //
        $node = $this->dom->createElement("PiecesEnabled", $_piecesEnabled);
        $parentNode->appendChild($node);
    }

	/**
	 * Gets node defined by the path value.  Creates
	 * the path if it does not already exist.
	 *
	 * @param Xpath to required node
	 * @param XPathObject
	 * @return Node
	 */
	private function getNode ($path, $xpath = null)
	{
		// Get the xpath object
		if ($xpath == null) $xpath = new DOMXPath($this->dom);
		//
		$nodeList = $xpath->query($path);
		//
		if ($nodeList->length > 0)
		{
			$node = $nodeList->item(0);
		}
		else
		{
			$node = null;
			$pos = strrpos($path, "/");
			if ($pos > 0)
			{
				$element = substr($path, $pos + 1);
				$parent = $this->getNode (substr($path,0,$pos), $xpath);
				if ($parent != null)
				{
					$elm = $this->dom->createElement($element);
					$node = $parent->appendChild($elm);
				}
			}
		}
		return $node;
	}



	/**
	 * Get the XML request message
	 *
	 */
	public function getXmlRequest()
	{
		return $this->dom->saveXml();
	}

}
?>