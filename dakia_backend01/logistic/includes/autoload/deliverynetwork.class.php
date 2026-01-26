<?php
/**
 * Courier services are only available to particular addresses
 * depending on he delivery network.
 * This is the base class for deliveyr networks and
 * has be overridden to create a concrete class.
 * Use to validate and retrieve information on delivery networks.
 *
 * Useage:
 * 	1. Create
 * 	2. Set the delivery adress
 * 	3. Use the "IsValid" method to check if service is available for given address.
 * 	4. Get service information using various methods (getRoutingCode, etc).
 *
 */
abstract class DeliveryNetwork
{
	protected $delivery_address = null;
	protected $error_array = array();
	/***
	 * Create the delivery network object.
	 */
	public function __construct(iAddress $theDeliveryAddress)
	{
		$this->delivery_address = $theDeliveryAddress;
	}

	/***
	 * Gets delivery network used for a given consignment.
	 *
	 * @return DeliveryNetwork
	 */
	public static function deliveryNetworkFactory(Consignment $consignment)
	{
		$delivery_network = null;
		//echo "bb" . $consignment->getService();
		//die;

		// service must be one of allowed values
		// - set default handling based on service type
		
		
		
		
		
		switch($consignment->getService())
		{
			case Consignment::SERVICE_DOMESTIC:
				$delivery_network = new DeliveryNetworkDay($consignment);
				break;
			case Consignment::SERVICE_INTERNATIONAL:
				$delivery_network = new DeliveryNetworkTime($consignment);
				break;
		//	case Consignment::SERVICE_EUROPE_ROAD:
		//		$delivery_network = new DeliveryNetworkEuroRoad($consignment);
		//		break;
			case Consignment::SERVICE_ROYALMAIL_24:
				$delivery_network = new DeliveryNetworkEuroRoad($consignment);
				break;
			case Consignment::SERVICE_ROYALMAIL_24_SIGN:
				$delivery_network = new DeliveryNetworkEuroRoad($consignment);
				break;
			case Consignment::SERVICE_ROYALMAIL_48:
				$delivery_network = new DeliveryNetworkEuroRoad($consignment);
				break;
			case Consignment::SERVICE_ROYALMAIL_48_SIGN:
				$delivery_network = new DeliveryNetworkEuroRoad($consignment);
				break;








		}
		
		
		return $delivery_network;
	}

	/**
	 * Routing Barcode and shipping number of delivery network.
	 *
	 * @return string
	 */
	abstract public function isAddressValid();
	abstract public function isParcelValid(Parcel $parcel);
	public function isConsignmentValid(Consignment $consignment) { return true; }
	//
	abstract public function getShipmentNumber($deliveryNetwork);
	abstract public function appendLabel(PdfBase $pdf, Consignment $consignment, $new_page_flag);

	/***
	 * Code used by the courier to identify a service.
	 */
	public function getHandlingCode()
	{
		return "";
	}

	public function getServiceHub()
	{
		return "";
	}
	public function getServiceStation()
	{
		return "";
	}
	public function getRoutingCode()
	{
		return "";
	}

	public function getErrorList()
	{
		return $this->error_array;
	}
}