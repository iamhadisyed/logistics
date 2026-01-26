<?php
/***
 * DHL Time Definitie Delivery Network
 */
class DeliveryNetworkTime extends DeliveryNetwork
{
	private $routing_code = "";
    private $handling="";


	/***
	 * Append label for international booking.
	 */
	public function appendLabel(PdfBase $pdf, Consignment $consignment, $new_page_flag)
	{
		// append an international label
		$label = new InternationalLabel1($pdf);
		//
		return $label->AddConsignment ($consignment, $new_page_flag);
	}


	/***
	 * Time definition shipment number is the AWB.
	 */
	public function getShipmentNumber($deliveryNetwork)
	{
		return AwbRange::getAwb($deliveryNetwork);
	}


	/***
	 * Indicates if address is valid
	 *
	 * @return bool
	 */
	public function isAddressValid()
	{
		$iso = $this->delivery_address->getCountryIsoCode();
		if ($iso == "")
		{
			$this->error_array[] = "Country " . $this->delivery_address->getCountry() . " not recognised.";
			return false;
		}
		$pc = $this->delivery_address->getPostcode();
		$city = $this->delivery_address->getCity();

		// ESD Filter
		$filter = new DhlEsdFilter();
		$filter->AddCountryIsoFilter($iso);

		// If a postcode is given check for the postcode
		if ($pc != "")
		{
			if($iso=="CA" || $iso=="GB")
            $filter->AddPartialPostcodeFilter($pc);
            else
            $filter->AddFullPostcodeFilter($pc);
		}
		$esd_array = $filter->getList();

        //if(($iso=="CA" || $iso=="GB")&&strlen($pc)<6)
        //   {
        //  $this->error_array[] = "Please Enter Full Zipcode ";
        //   }


        if (sizeof($esd_array) == 0 )
		{

			$this->error_array[] = "Unable to find postcode " . $pc  . ".";
		}


		else
		{
			$this->routing_code = $esd_array[0]->getRoutingCode();
		}
		return (sizeof($this->error_array) == 0);
	}

	/***
	 * Checks whether have required information for a parcel
	 *
	 */
	public function isParcelValid(Parcel $parcel)
	{
		// dimensions are required for international items
		$Ok = true;
		//
		//if ($parcel->getLength() <= 0)
	//	{
//			$this->error_array[] = "A package length is missing";
//			$Ok = false;
//		}
//		if ($parcel->getWidth() <= 0)
//		{
//			$this->error_array[] = "A package width is missing";
//			$Ok = false;
	//	}
	//	if ($parcel->getHeight() <= 0)
	//	{
	//		$this->error_array[] = "A package height is missing";
	//		$Ok = false;
	//	}
	//	if ($parcel->getWeight() < 0.5)
	//	{
	//		$this->error_array[] = "A package weight is too small";
	//		$Ok = false;
	//	}
		return $Ok;
	}

	/***
	 *
	 */
	public function getRoutingBarcode()
	{



		$handlingCode = substr("00" . $this->getHandlingCode(true), - 2);

		//
		return "2L" .
		 		$this->delivery_address->getCountryIsoCode() .
				$this->delivery_address->getPostcode() . "+" .
				$handlingCode .
				"000000";
	}

	/***
	 * This is the 3 letter abbreviation used by DHL to identify services.
	 * (termed handling code in DHL documenation)
	 */
	public function getHandlingCode($return_numeric_code = false )
	{
/*

Product code table from DHL below.
Currently only using the WPX service.
------------------------
Three Letter Product Code	Timed Delivery	Dutiable
Y/N	Product Code to appear in Reverse Video on label	Two digit DPWN Routing Code	Product Name	Product Name (to appear on label)	Product Description
DOM	 	N	N	46	DOMESTIC EXPRESS (UK)	DOMESTIC EXPRESS	UK to UK Shipments
ECX	 	N	N	51	EXPRESS WORLDWIDE (EU)	EXPRESS WORLDWIDE	UK to EC countries
DOX	 	N	N	42	EXPRESS WORLDWIDE (DOC)	EXPRESS WORLDWIDE	Non Dutiable Shipments - UK to Non EC countries
WPX	 	Y	Y	48	EXPRESS WORLDWIDE (NON DOC)	EXPRESS WORLDWIDE	Dutiable Shipments - UK to Non EC countries
TDK	0900	N	N	39	EXPRESS 09:00 (EU / DOC / UK)	EXPRESS 09:00	Non Dutiable Shipments - UK to specific postcode / areas worldwide
TDE	0900	Y	Y	38	EXPRESS 09:00 (NON DOC)	EXPRESS 09:00	Dutiable Shipments - UK to specific postcode / areas outside EU
TDT	1200	N	N	38	EXPRESS 12:00 (EU / DOC / UK)	EXPRESS 12:00	Non Dutiable Shipments - UK to specific postcode / areas worldwide
TDY	1200	Y	Y	50	EXPRESS 12:00 (NON DOC)	EXPRESS 12:00	Dutiable Shipments - UK to specific postcode / areas outside EU
*/





		if ($return_numeric_code)
		{
			if ($this->delivery_address->getHandling() == "WPX")
			return 48;
			else if ($this->delivery_address->getHandling() == "DOX")
			return 42;
			else if ($this->delivery_address->getHandling() == "ECX")
			return 51;
		}
		//
		return $this->handling;
	}

    public function setHandlingCode($val)
    {
        $this->handling=$val;
    }
	public function getDayCode() {return ""; }
	public function getTimeText() {return ""; }


	public function getRoutingCode()
	{
		return $this->routing_code;
	}

	/**
	 * Check consignement
	 *
	 * @param Consignment $consignment
	 * @return bool
	 */
	public function isConsignmentValid(Consignment $consignment)
	{
		if ($consignment->getWeight() < 0.5)
		{
			$this->error_array[] = "Total weight must be greater than 0.5 Kg";
		}

		return (sizeof($this->error_array) == 0);
	}

}