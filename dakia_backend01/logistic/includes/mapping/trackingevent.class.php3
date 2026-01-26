<?php
/**
 * Tracking information for parcel groups.
 *
 */
class TrackingEvent extends DbAccess21
{
	/*
	 * Create and define class
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList =
			array(  'parcel_group_id'    => 'string',
					'courier'            => 'string',
					'event_date_time'    => 'string',
					'event_code'         => 'string',
					'description'        => 'string',
					'signatory'          => 'string',
					'service_area_code'  => 'string',
					'service_area'       => 'string',
					);
		//
		parent::__construct("tracking_events", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get Tracking event list
	 *
	 * @param unknown_type $filterArray
	 * @return unknown
	 */
	public static function getList($filterArray = null, $orderBy = "event_date_time desc")
	{
		return parent::getObjectList(__CLASS__, $filterArray, $orderBy);
	}

	/**
	 * Tracking Event
	 */
	public function setEvent($event) { $this->valArray["description"] = $event; }
	public function getEvent() { return $this->valArray["description"]; }

	/**
	 * Location
	 */
	public function setLocation($location) { $this->valArray["service_area"] = $location; }
	public function getLocation() { return $this->valArray["service_area"]; }

	/**
	 * Parcel group id
	 */
	public function setParcelGroupId($parcel_group_id) { $this->valArray["parcel_group_id"] = $parcel_group_id; }
	public function getParcelGroupId() { return $this->valArray["parcel_group_id"]; }

	/**
	 * Event date
	 */
	public function setEventDateTime($dateTime)
	{
		$this->valArray["event_date_time"] = date("Y-m-d H:i:s", $dateTime);
	}
	public function getEventDateTime()
	{
		return strtotime($this->valArray["event_date_time"]);
	}

	/**
	 * Carrier
	 */
	public function setCourier($courier) { $this->valArray["courier"] = $courier; }
	public function getCourier() { return $this->valArray["courier"]; }

	/**
	 * Gets DHLs description of event code
	 *
	 */
	public function getEventCodeDescription()
	{
		$desc = "";
		switch ($this->getEventCode())
		{
			case 'BA': $desc = 'Bad Address'; break;
			case 'CA': $desc = 'Closed on Arrival'; break;
			case 'CD': $desc = 'Clearance Delay'; break;
			case 'CM': $desc = 'Consignee Moved'; break;
			case 'HP': $desc = 'Held for Payment'; break;
			case 'MC': $desc = 'Miscode'; break;
			case 'MD': $desc = 'Missed Delivery Cycle'; break;
			case 'MS': $desc = 'Missort'; break;
			case 'ND': $desc = 'Not Delivered'; break;
			case 'NH': $desc = 'Not Home'; break;
			case 'OH': $desc = 'On Hold'; break;
			case 'RD': $desc = 'Refused Delivery'; break;
			case 'SC': $desc = 'Service Changed'; break;
			case 'TD': $desc = 'Transport Delay'; break;
			case 'UD': $desc = 'Uncontrollable Clearance Delay'; break;
			case 'BR': $desc = 'Cleared and Delivered by Broker'; break;
			case 'CS': $desc = 'Closed Shipments'; break;
			case 'DD': $desc = 'Delivered Damaged'; break;
			case 'DM': $desc = 'Damaged'; break;
			case 'DS': $desc = 'Destroyed/Disposal'; break;
			case 'OK': $desc = 'Delivery'; break;
			case 'RT': $desc = 'Retuned to Consignor'; break;
			case 'SS': $desc = 'Shipment Stopped'; break;
			case 'TP': $desc = 'Forwarded to Third party'; break;
			case 'AF': $desc = 'Arrived Facility'; break;
			case 'AR': $desc = 'Arrival at delivery facility'; break;
			case 'BN': $desc = 'Broker Notified'; break;
			case 'CC': $desc = 'Awaiting Consignee Collection'; break;
			case 'CR': $desc = 'Clearance Release'; break;
			case 'DF': $desc = 'Depart Facility'; break;
			case 'FD': $desc = 'Forwarded to Third Party Delivery Agent'; break;
			case 'IC': $desc = 'In Clearance Processing'; break;
			case 'PD': $desc = 'Partial Delivery'; break;
			case 'PL': $desc = 'Processed at Location'; break;
			case 'PO': $desc = 'Processed at Origin '; break;
			case 'PU': $desc = 'Shipment Pickup'; break;
			case 'SA': $desc = 'Shipment Acknowledged'; break;
			case 'SD': $desc = 'Shipment Detail'; break;
			case 'SI': $desc = 'Security Inspection'; break;
			case 'SM': $desc = 'Scheduled for Movement'; break;
			case 'TR': $desc = 'Record of Transit'; break;
			case 'WC': $desc = 'With Delivering Courier'; break;
			case 'YY': $desc = 'Passed Warehouse without scan'; break;
		}
		return $desc;
	}

}