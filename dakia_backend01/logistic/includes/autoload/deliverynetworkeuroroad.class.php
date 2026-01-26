<?php
class DeliveryNetworkEuroRoad extends DeliveryNetworkDay
{
	private $serviceHandlingCode = "EPL";
	private $reamus_service = null;

	public function appendLabel(PdfBase $pdf, Consignment $consignment, $new_page_flag)
	{
			//$handling = $consignment->getHandling();
			//mail('pleasant.bright@gmail.com','',$handling);
			/*if($handling=='TP2test'|| $handling=='TP2Stest')
			{
				$label = new InternationalLabel_Test($pdf);
				return $label->AddConsignment ($consignment);
			}
			else
			{*/
				// append an international label
				$label = new InternationalLabel($pdf);
				return $label->AddConsignment ($consignment, $new_page_flag);
			//}
	}

	/***
	 * Service hub is blank for euro road.
	 */
	public function getServiceHub()
	{
		return "";
	}

	/***
	 * Barcode
	 */
	public function getRoutingBarcode()
	{
		$service = $this->getReamusService();
		if ($service == null) return "ERROR";

		// page 25 of LINK speck
		//return "2L" . "DE" . "53639" . "+" . "04" . "00" . "0" . "000";

		return "2L" .
		 		$this->delivery_address->getCountryIsoCode() .
				$this->delivery_address->getPostcode() . "+" .
				$service->getProductCode() .
				$service->getDateCode() .
				$service->getTimeCode() .
				"000"
				;
	}

	public function getDayCode ()
	{
		$service = $this->getReamusService();
		if ($service == null) return "";

		return $service->getDayText();
	}

	/***
	 * Time code
	 */
	public function getTimeCode ()
	{
		$service = $this->getReamusService();
		if ($service == null) return "";

		return $service->getTimeCode();
	}
	public function getTimeText ()
	{
		$service = $this->getReamusService();
		if ($service == null) return "";

		return $service->getTimeText();
	}

	public function getShipmentNumber($delivery_network)
	{
		$delivery_network="R1";
        return AwbRange::getAwb($delivery_network);
	}


	/***
	 * Check that the address is valid
	 */
	public function isAddressValid ()
	{
		$esd_array = array();

		// protected $delivery_address = null;
		// protected $error_array = array();

		$iso = $this->delivery_address->getCountryIsoCode();
		if ($iso == "")
		{
			$this->error_array[] = "Unrecognised country " . $this->delivery_address->getCountry()  . ".";
			return false;
		}
		$pc = trim($this->delivery_address->getPostcode());
		$city = $this->delivery_address->getCity();

		// ESD Filter
		$filter = new DhlEsdFilter();
		$filter->AddCountryIsoFilter($iso);

		// If a postcode is given check for the postcode
		if ($pc != "")
		{
            if($iso=="CA")
            $filter->AddPartialPostcodeFilter($pc);
            else
			$filter->AddFullPostcodeFilter($pc);
			$esd_array = $filter->getList();
		}

		// either nothing found, or no pc given.
		if (sizeof($esd_array) == 0)
		{
			$postcode_error = true;

			// Does this country have postcodes
			$filter = new DhlEsdFilter();
			$filter->AddCountryIsoFilter($iso);
			//
			$esd_array = $filter->getList();
			if (sizeof($esd_array) > 0)
			{
				//
				if ($esd_array[0]->getPostcodeFrom() == "")
				{
					$postcode_error = false; // this country doesn't have postcodes
					// Check again city
					$filter->AddCityFilter($city);
					$esd_array = $filter->getList();
					if (sizeof($esd_array) > 0)
					{
						$this->routing_code = $esd_array[0]->getRoutingCode();
					}
					else
					{
						$this->error_array[] = "Unable to find city " . $city . ".";
					}
				}
			}
			// was there a postcode error?
			if ($postcode_error)
			{
				if ($pc == "")
				{
					$this->error_array[] = "Postcode cannot be blank for " . $this->delivery_address->getCountry();
				}
				else
				{
					$this->error_array[] = "Unable to find postcode " . $pc  . ".";
				}
			}
		}
		else
		{
			$this->routing_code = $esd_array[0]->getRoutingCode();
		}
		return (sizeof($this->error_array) == 0);
	}

	/***
	 * Service code
	 */
	public function getHandlingCode()
	{
		return $this->serviceHandlingCode;
	}

	/***
	 * Get the reamus service
	 */
	private function getReamusService()
	{
		if ($this->reamus_service == null)
		{
			$this->reamus_service = ReamusService::getServiceFromHandling($this->serviceHandlingCode);
		}
		return $this->reamus_service;
	}
}