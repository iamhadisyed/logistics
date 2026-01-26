<?php
/**
 * DHL Day Definite Delivery network.
 *
 */
class DeliveryNetworkDay extends DeliveryNetwork
{
	private $serviceHandlingCode = "";

	private $domestic = true;
	private $reamus = null;
	private $reamus_service = null;
	private $reamus_service_checked = false;

	/***
	 * Check if address if valid
	 */
	public function isAddressValid()
	{
		//

        $iso = $this->delivery_address->getCountryIsoCode();
        if ($iso == "")
        {
            $this->error_array[] = "Country " . $this->delivery_address->getCountry() . " not recognised.";
            return false;
        }
		$reamus = $this->getReamus();
		if ($reamus == null)
		{
			$this->error_array[] = "Unable to find service details.";
			return false;
		}

		if (!$reamus->isPostcodeValid())
		{
			$this->error_array = $reamus->getErrorList();
		}

		return (sizeof($this->error_array) == 0);
	}

        /***
     * Second version of isAddressValid which will take the serviceToValidate from the Add consignment page
     * and check for correct address.
     */
    public function isAddressValidDomestic($serviceToValidate)
    {
        //
		
		

        $iso = $this->delivery_address->getCountryIsoCode();
        if ($iso == "")
        {
            $this->error_array[] = "Country " . $this->delivery_address->getCountry() . " not recognised.";
            return false;
        }
        $reamus = $this->getReamusDomestic($serviceToValidate);
        if ($reamus == null)
        {
            $this->error_array[] = "Unable to find service details.";
            return false;
        }

        if (!$reamus->isPostcodeValid())
        {
            $this->error_array = $reamus->getErrorList();
        }

        return (sizeof($this->error_array) == 0);
    }


	/***
	 * Get the reamus service
	 */
	private function getReamusService()
	{
		if (!$this->reamus_service_checked)
		{
                    	$this->error_array = array();
                    //    if ($this->serviceHandlingCode == '3HS')
                     //  {
                    //        $this->serviceHandlingCode = '3H';
                    //   }
			// Is the handling code set up
			if ($this->serviceHandlingCode != "")
			{
                        //    echo 'jgjhgj'; die;
				$this->reamus_service = ReamusService::getServiceFromHandling($this->serviceHandlingCode);
			}
//echo $this->reamus_service; die;
			// If service not set then lookup
			if ($this->reamus_service == null)
			{
				//
				if ($this->delivery_address->getPostcode() == "")
				{
					$this->error_array[] = "Postcode cannot be blank.";
				}
				else
				{
					// Services to check for.
					$service_array = array ("2H", "2EN", "ISLE");

					// Check postcode
					$postcode_found = false;
					//
                                        //$service_handling = "1CSP";
					foreach ($service_array as $service_handling)
					{
						// get the service associated with this handling code
						$reamus_service = ReamusService::getServiceFromHandling($service_handling);
						if ($reamus_service == null) continue;
						// get reamus look up for this service.
						$reamus = new Reamus($this->delivery_address->getPostcode(), $reamus_service->getProductCode(), $reamus_service->getFeatureCode());

						// postcode found - set flag to indicate postcode is valid
						if (!$reamus->isPostcodeValid()) continue;
						$postcode_found = true;

						// Postcode is valid, but does it support this service?
						if ($reamus->getHub() != "")
						{
							t("Found service: " . $service_handling . " for " . $this->delivery_address->getPostcode(), __METHOD__);
							// service found
							$this->serviceHandlingCode = $service_handling;
							$this->reamus_service = $reamus_service;
							$this->reamus = $reamus;
							//
							break;
						}
						t("Service does not support postcode: " . $service_handling . " - " . $this->delivery_address->getPostcode(), __METHOD__);
					}

					// Check for errors
					if ($postcode_found)
					{
						// have we managed to find a service?
						if ($this->reamus_service == null)
						{
							$this->error_array[] = "Yodel does not provide service on " . $this->delivery_address->getPostcode() . ".";
						}
					}
					else
					{
						$this->error_array[] = "Yodel does not provide service on ".$this->delivery_address->getPostcode();//;"Unable to find postcode " . $this->delivery_address->getPostcode() . " in YODEL Gazette.";
					}
				}
			}
			// check only once
			$this->reamus_service_checked = true;
		}
		return $this->reamus_service;
	}


    private function getReamusServiceDomestic($serviceTobeChecked)
    {
        if (!$this->reamus_service_checked)
        {
            $this->error_array = array();

            // Is the handling code set up
            if ($this->serviceHandlingCode != "")
            {
                $this->reamus_service = ReamusService::getServiceFromHandling($this->serviceHandlingCode);
            }

            // If service not set then lookup
            if ($this->reamus_service == null)
            {
                //
                if ($this->delivery_address->getPostcode() == "")
                {
                    $this->error_array[] = "Postcode cannot be blank.";
                }
                else
                {
                    // Services to check for.
                    //Not used in this version of the method $service_array = array ("ECO", "HECO", "NIS", "ISLE");

                    // Check postcode
                    $postcode_found = false;
                    //
                        // get the service associated with this handling code
                        $reamus_service = ReamusService::getServiceFromHandling($serviceTobeChecked);
                        if ($reamus_service !=null) {
                        // get reamus look up for this service.
                        $reamus = new Reamus($this->delivery_address->getPostcode(), $reamus_service->getProductCode(), $reamus_service->getFeatureCode());

                        // postcode found - set flag to indicate postcode is valid
                        if ($reamus->isPostcodeValid())
                        {
                        $postcode_found = true;

                        // Postcode is valid, but does it support this service?
                        if ($reamus->getHub() != "")
                        {
                            //t("Found service: " . $service_handling . " for " . $this->delivery_address->getPostcode(), __METHOD__);
                            // service found
                            $this->serviceHandlingCode = $serviceTobeChecked;
                            $this->reamus_service = $reamus_service;
                            $this->reamus = $reamus;
                            //
                        }
                        }
                        }
                      // else t("Service does not support postcode: " . $service_handling . " - " . $this->delivery_address->getPostcode(), __METHOD__);


                    // Check for errors
                    if ($postcode_found)
                    {
                        // have we managed to find a service?
                        if ($this->reamus_service == null)
                        {
                            $this->error_array[] = "Yodel does not provide service on  " . $this->delivery_address->getPostcode() . ".";
                        }
                    }
                    else
                    {
                        $this->error_array[] = "Unable to find postcode " . $this->delivery_address->getPostcode() . " in YODEL Gazette.";
                    }
                }
            }
            // check only once
            $this->reamus_service_checked = true;
        }
        return $this->reamus_service;
    }


	/**
	 * Get the reamus object, if not already got
	 * @return unknown_type
	 */
	private function getReamus()
	{
		// Get reamus service
		$service = $this->getReamusService();

		if ($service == null) return null;

		// Has the reamus object been set
		if ($this->reamus == null)
		{
			$this->reamus = new Reamus($this->delivery_address->getPostcode(), $service->getProductCode(), $service->getFeatureCode());
		}
		return $this->reamus;
	}


   /**
   * Second version of the getReamus which takes the service from the add consignment page and checks to see
   * is there is service.
   *
   */

        private function getReamusDomestic($serviceToCheck)
    {
        // Get reamus service

        $service = $this->getReamusServiceDomestic($serviceToCheck); // pass in the service to getReamusService to check for service.

        if ($service == null) return null;

        // Has the reamus object been set
        if ($this->reamus == null)
        {
            $this->reamus = new Reamus($this->delivery_address->getPostcode(), $service->getProductCode(), $service->getFeatureCode());
        }
        return $this->reamus;
    }


	/***
	 * Routing barcode
	 */
	public function getRoutingBarcode()
	{
		$service = $this->getReamusService();
		//echo $service; die;
		if ($service == null) return "ERROR";

		// page 25 of LINK speck
		return "2L" . "GB" . $this->delivery_address->getPostcode() . "+" .
				$service->getProductCode() .
				$service->getDateCode() .
				$service->getTimeCode() .
				$service->getFeatureId()
				;
	}

	public function appendLabel(PdfBase $pdf, Consignment $consignment, $new_page_flag)
	{
		$label = new LabelPdf($pdf);
		// Create interface
		$epl = new EplInterface($consignment);
		$epl->setArchiveCopyFlag(false);

		// Get the handling code from the consignment
		$this->serviceHandlingCode = $consignment->getHandling();
		
		

		// Use epl to generate pdf label
		$label->addLabelFromEplText($epl->getEplText(), $new_page_flag, $consignment);
		//
		return true;
	}
	public function setServiceHandlingCode($handling)
	{
		$this->serviceHandlingCode = $handling;
                 $this->serviceHandlingCode;
		//
		//return $this->serviceHandlingCode;
	}

		
	/***
	 * Service code
	 */
	public function getHandlingCode()
	{
		$this->getReamusService();
		//
		return $this->serviceHandlingCode;
	}

	public function getServiceHub()
	{
		$reamus = $this->getReamus();
		if ($reamus == null) return "";

		return $reamus->getHub();
	}
	public function getServiceStation()
	{
		$reamus = $this->getReamus();
		if ($reamus == null) return "";
		return $reamus->getStation();
	}

	public function getDayCode ()
	{
		$service = $this->getReamusService();
		if ($service == null) return "";

		return $service->getDayText();
	}
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

	public function getShipmentNumber($deliveryNetwork)
	{
		return "";
	}

	/***
	 * All parcel are valid
	 */
	public function isParcelValid (Parcel $parcel)
	{
		return true;
	}
}