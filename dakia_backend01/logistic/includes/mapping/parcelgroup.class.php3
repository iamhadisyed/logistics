<?php

////////////////////////////////////////////////////
//
// Class for dealing with parcel groups
//
////////////////////////////////////////////////////

/**
 * Parcelgroup - Parcel group class
 * @package Courier
 */
class Parcelgroup extends DbAccess
{
	const BOOKING_STATUS_READY = 1;
	const BOOKING_STATUS_COMPLETE = 2;
	const BOOKING_STATUS_FAILED = 3;
	const BOOKING_STATUS_FAILED_DEALT_WITH = 4;

	// Implement as a Bit mask
	const HEART_BEAT_STATUS_NONE = 0;
	const HEART_BEAT_STATUS_WEIGHT_MISMATCH_DEALT_WITH = 1;

	//
	const ERROR_STATUS_NONE = 0;
	const ERROR_STATUS_ALERT = 1;
	const ERROR_STATUS_COMPLETE = 2;

	const VAT_STATUS_STANDARD = "S";
	const VAT_STATUS_NONE = "N";
	const VAT_STATUS_NOT_SET = "U";

	// access via the getCourierService() method
	private $courier_service; // gives the courier service for this parcel groups.
	private $parcelArray = array();

	//***
	protected $collection_address_id;
	protected $collection_country_id;
	protected $collection_postcode;
	protected $destination_address_id;
	protected $destination_country_id;
	protected $destination_subzone;
	protected $email_address;
	protected $name;
	protected $collection_date;
	protected $earliest_collection_time;
	protected $latest_collection_time;
	protected $courier_service_id;
	protected $product_id;
	protected $total_weight;
	protected $unit;
	protected $total_parcels;
	protected $courier_price;
	protected $final_price;
	protected $vat;
	protected $discount;
	protected $insurance_cover;
	protected $insurance_premium;
	protected $booking_status = self::BOOKING_STATUS_READY;
	protected $contents;
	protected $contents_value;
	protected $delivered_flag;
	protected $export_reason;
	protected $vat_status;
	protected $vat_number;
	protected $error_status;
	protected $heartbeat_status;
	protected $courier_booking_ref;

	protected $orderq;
	protected $active;
	protected $deletedq;
	protected $added_on;
	protected $added_by;
	protected $changed_on;
	protected $changed_by;

	// collection and destination addresses
	private $collection_address = null;
	private $destination_address = null;
	private $collection_summary = null;
	private $destination_summary = null;

	// indicates if the list of parcels have been retrieved from database.
	private $get_parcels_from_db = false;

	/**
	* Constructor. Returns the object.
	* @param int $id
	* @return obj
	*/
	public function __construct($id = 0)
	{
		$this->tablename = 'parcel_groups';
		$this->pkey = 'id';
		$this->fields =
			array(  'collection_address_id'    => 'collection_address_id',
					'collection_country_id'    => 'collection_country_id',
					'collection_postcode'      => 'collection_postcode',
					'destination_address_id'   => 'destination_address_id',
					'destination_country_id'   => 'destination_country_id',
					'destination_subzone'      => 'destination_subzone',
					'contents'                 => 'contents',
					'contents_value'           => 'contents_value',
					'collection_date'          => 'collection_date',
					'earliest_collection_time' => 'earliest_collection_time',
					'latest_collection_time'   => 'latest_collection_time',
					'courier_service_id'       => 'courier_service_id',
					'product_id'               => 'product_id',
					'total_weight'             => 'total_weight',
					'unit'                     => 'unit',
					'total_parcels'            => 'total_parcels',
					'courier_price'            => 'courier_price',
					'final_price'              => 'final_price',
					'vat'                      => 'vat',
					'discount'                 => 'discount',
					'insurance_cover'          => 'insurance_cover',
					'insurance_premium'        => 'insurance_premium',
					'booking_status'           => 'booking_status',
					'heartbeat_status'         => 'heartbeat_status',
					'delivered_flag'           => 'delivered_flag',
					'export_reason'            => 'export_reason',
					'vat_status'               => 'vat_status',
					'vat_number'               => 'vat_number',
					'error_status'             => 'error_status',
					'courier_booking_ref'      => 'courier_booking_ref',

					'orderq'                   => 'orderq',

					'active'                   => 'active',
					'deletedq'                 => 'deletedq',

					'added_on'                 => 'added_on',
					'added_by'                 => 'added_by',
					'changed_on'               => 'changed_on',
					'changed_by'               => 'changed_by'
					);

		$this->_construct($id);

		// If this object is being filled from database, then need to ensure
		// that we also get the parcel information (if required) from database.
		if ($this->id > 0) $this->get_parcels_from_db = true;

		// NOTE: As the class is offering unserialize we can't create any additional objects in the
		// constructor.

		return $this;
	}

	/**
	 * Override the fill from database.
	 * If this items is filled from database, then need to ensure children are
	 * also retrieved from database (first time they're accessed.
	 *
	 * @param int $id
	 */
	public function fillFromDatabase($id)
	{
		$this->get_parcels_from_db = true;
		return parent::fillFromDatabase($id);
	}
	public function populate($row)
	{
		$this->get_parcels_from_db = true;
		return parent::populate($row);
	}
	/**
     * Gets the courier service for this parcel group.
     *
     * @return CourierService
     */
	public function getCourierService()
	{
		//echo $this->getCourierServiceId(); exit;
		if ($this->courier_service == null)
		{
			$this->courier_service = new Services(@$this->getCourierServiceId());
		}
		return $this->courier_service;
	}

	////////////////////////////////////////////////////
	// Access and update methods
	////////////////////////////////////////////////////

	/**
	* getAnyParcelgroup. Returns the object array of Parcel groups.
	* @param string $where
	* @param string $activeOnly
	* @param string $orderBy
	* @return string
	*/
	public function getAnyParcelgroup($where = "", $activeOnly = true, $orderBy = "orderq")
	{
		$ret = array();
		$ids = $this->getAnyParcelgroupIds($where, $activeOnly, $orderBy);

		if (count($ids)>0)
		{
			foreach ($ids as $i)
			{
				$ret[] = new Parcelgroup($i);
			}

			return $ret;
		}
		else return $ret;
	}

	/**
	* getAnyParcelgroupIds. Returns the object array of Parcel group ids.
	* @param string $where
	* @param string $activeOnly
	* @param string $orderBy
	* @return string
	*/
	public function getAnyParcelgroupIds($where = "", $activeOnly = true, $orderBy = "orderq")
	{

		$ret = array();
		$whereSql  = "WHERE deletedq <> 'Y' ";

		if ($where)	$whereSql .= " and $where";
		if ($activeOnly) $whereSql .= " and active=1";
		$sql = "select {$this->pkey} from {$this->tablename} $whereSql order by $orderBy";

		//echo $sql;

		$result = Db::query($sql);
		while (list($pId) = mysqli_fetch_row($result))
		{
			$ret[] = $pId;
		}

		return $ret;
	}

	/**
	 * Gets the quote number for this parcel group
	 *
	 */
	public function getQuoteNumber()
	{
		return self::formatAsQuoteNumber($this->getId());
	}
	public static function formatAsQuoteNumber($theNumber)
	{
		return str_pad($theNumber, 6, "0", STR_PAD_LEFT);
	}


	public static function getParcelGroupListFromBasketId($basketId)
	{
		$parel_group_array = array();
		//
		$sql = "SELECT pg.*
				FROM parcel_groups pg
				LEFT JOIN basket_items bi on pg.id=bi.parcel_group_id
				WHERE bi.basket_id=" . $basketId;

		$result = Db::query($sql);
		while ($row = mysqli_fetch_assoc($result))
		{
			$parcelGroup = new ParcelGroup();
			$parcelGroup->populate($row);

			// set private field to indicate need to get parcels from database.
			$parcelGroup->get_parcels_from_db = true;
			//
			$parel_group_array[] = $parcelGroup;
		}


		return $parel_group_array;
	}


	/**
	 * Get list of parcel groups to get tracking information for
	 *
	 * @return unknown
	 */
	public static function getTrackingCheckListBookings ()
	{
		// start looking at 9pm on collection day (collection_date + 21 hours)
		// give up looking at the end of day after collection day (collection_date + 48 hours)
		// only check if the DHL weight hasn't already been got (weight 0 or null)
		// delay recheck by given interval (check time is null or more than 97 minutes ago)
		$parcelGroupList = array();
		$sql = "SELECT p.*
				FROM parcel_groups p
				LEFT JOIN dhl_bookings d on p.id=d.parcel_group_id
				LEFT JOIN basket_items bi on p.id=bi.parcel_group_id
				LEFT JOIN baskets b on bi.basket_id=b.id
				WHERE d.id is not null
					AND (d.weight = 0 or d.weight is null)
					AND DATE_ADD(p.collection_date, INTERVAL 21 HOUR) < Now()
					AND DATE_ADD(p.collection_date, INTERVAL 48 HOUR) > Now()
					AND (d.tracking_check_time is null or d.tracking_check_time=0 or Date_Add(d.tracking_check_time, INTERVAL 97 MINUTE) < Now())
					AND b.order_id > 0
				";
		//
		//echo "<p>$sql</p>";
		//
		$result = Db::query($sql);
		while ($row = mysqli_fetch_assoc($result))
		{
			$parcelGroup = new ParcelGroup();
			$parcelGroup->populate($row);
			//
			$parcelGroupList[] = $parcelGroup;
		}
		return $parcelGroupList;
	}
	/**
	 * Results array of failed parcel group bookings
	 *
	 */
	public static function getFailedBookings ()
	{
		$parcelGroupList = array();
		$sql = "SELECT p.*,
					c.postcode as collection_postcode, cc.name as collection_country, cc.iso as collection_iso,
					d.postcode as destination_postcode, dc.name as destination_country, dc.iso as destination_iso
				FROM parcel_groups p
				LEFT JOIN parcel_group_addresses c on p.collection_address_id=c.id
				LEFT JOIN parcel_group_addresses d on p.destination_address_id=d.id
				LEFT JOIN countries cc on c.country_id = cc.id
				LEFT JOIN countries dc on d.country_id = dc.id
				LEFT JOIN basket_items bi on p.id=bi.parcel_group_id
				LEFT JOIN baskets b on bi.basket_id=b.id
				WHERE b.order_id > 0
					AND p.booking_status=" . ParcelGroup::BOOKING_STATUS_FAILED . "
				ORDER BY
					p.added_on DESC
				";
		//
		t_ml($sql, __METHOD__);
		//
		$result = Db::query($sql);
		while ($row = mysqli_fetch_assoc($result))
		{
			$parcelGroup = new ParcelGroup();
			$parcelGroup->populate($row);
			//
			$parcelGroup->collection_summary = new LocationSummary();
			$parcelGroup->collection_summary->setPostcode ($row["collection_postcode"]);
			$parcelGroup->collection_summary->setCountry  ($row["collection_country"]);
			$parcelGroup->collection_summary->setCountryIso  ($row["collection_iso"]);
			//
			$parcelGroup->destination_summary = new LocationSummary();
			$parcelGroup->destination_summary->setPostcode ($row["destination_postcode"]);
			$parcelGroup->destination_summary->setCountry ($row["destination_country"]);
			$parcelGroup->destination_summary->setCountryIso ($row["destination_iso"]);
			//
			$parcelGroupList[] = $parcelGroup;
		}
		return $parcelGroupList;
	}

	/**
	 * Results array of failed parcel group bookings
	 *
	 */
	public static function getWeightMismatches ()
	{
		$parcelGroupList = array();
		$sql = "SELECT p.*,
					c.postcode as collection_postcode, cc.name as collection_country, cc.iso as collection_iso,
					d.postcode as destination_postcode, dc.name as destination_country, dc.iso as destination_iso
				FROM parcel_groups p
				LEFT JOIN dhl_bookings dh on p.id=dh.parcel_group_id
				LEFT JOIN parcel_group_addresses c on p.collection_address_id=c.id
				LEFT JOIN parcel_group_addresses d on p.destination_address_id=d.id
				LEFT JOIN countries cc on c.country_id = cc.id
				LEFT JOIN countries dc on d.country_id = dc.id
				LEFT JOIN basket_items bi on p.id=bi.parcel_group_id
				LEFT JOIN baskets b on bi.basket_id=b.id
				WHERE b.order_id > 0
					AND (p.heartbeat_status & " . ParcelGroup::HEART_BEAT_STATUS_WEIGHT_MISMATCH_DEALT_WITH . "= 0)
					AND dh.weight is not null
					AND dh.weight > 0
					AND dh.weight <> p.total_weight
				ORDER BY
					p.added_on DESC
				";
		//
		//echo "<p>$sql</p>";
		//
		$result = Db::query($sql);
		while ($row = mysqli_fetch_assoc($result))
		{
			$parcelGroup = new ParcelGroup();
			$parcelGroup->populate($row);
			//
			$parcelGroup->collection_summary = new LocationSummary();
			$parcelGroup->collection_summary->setPostcode ($row["collection_postcode"]);
			$parcelGroup->collection_summary->setCountry  ($row["collection_country"]);
			$parcelGroup->collection_summary->setCountryIso  ($row["collection_iso"]);
			//
			$parcelGroup->destination_summary = new LocationSummary();
			$parcelGroup->destination_summary->setPostcode ($row["destination_postcode"]);
			$parcelGroup->destination_summary->setCountry ($row["destination_country"]);
			$parcelGroup->destination_summary->setCountryIso ($row["destination_iso"]);
			//
			$parcelGroupList[] = $parcelGroup;
		}
		return $parcelGroupList;
	}


	/**
	* save. Updates table.
	* @return void
	*/
	public function save()
	{
		$this->changed_on = date("Y-m-d H:i:s");
		$this->changed_by = (isset($_SESSION['admin']['firstname'])	? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');

		if ($this->id < 1)
		{
			$this->added_on = date("Y-m-d H:i:s");
			$this->added_by = (isset($_SESSION['admin']['firstname'])	? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');
		}
		// save
		parent::save();
		//die;
	}

	/**
	* delete. Updates row as deleted
	* @return void
	*/
	public function delete()
	{
		$sql = "Delete from Parcel_Groups WHERE {$this->pkey} = '{$this->id}'";
		t($sql, __METHOD__);
		//
		$result = Db::query($sql);
	}

	/**
	* expunge. Real delete
	* @return void
	*/
	public function expunge()
	{

		$sql = "DELETE FROM {$this->tablename}
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}


	/**
	* updateTotals. Updates row with totals
	* @return void
	*/
	public function updateTotals()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['total_weight']}         = '{$this->total_weight}',
					{$this->fields['unit']}                 = '{$this->unit}',
					{$this->fields['total_parcels']}         = '{$this->total_parcels}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updateCountries. Updates row country details
	* @return void
	*/
	public function updateCountries()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['collection_country_id']} = '{$this->collection_country_id}',
					{$this->fields['collection_postcode']}     = '{$this->collection_postcode}',
					{$this->fields['destination_country_id']} = '{$this->destination_country_id}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updateTimesDetails. Updates row with times and details of bookings
	* @return void
	*/
	public function updateTimesDetails()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['email_address']}         = '{$this->email_address}',
					{$this->fields['name']}                     = '{$this->name}',
					{$this->fields['collection_date']}         = '{$this->collection_date}',
					{$this->fields['earliest_collection_time']} = '{$this->earliest_collection_time}',
					{$this->fields['latest_collection_time']}   = '{$this->latest_collection_time}',
					{$this->fields['insurance_cover']}       = '{$this->insurance_cover}'
					{$this->fields['insurance_premium']}     = '{$this->insurance_premium}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updateCollectionAddressId. Updates row with collection address id
	* @return void
	*/
	public function updateCollectionAddressId()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['collection_address_id']}     = '{$this->collection_address_id}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updateDestinationAddressId. Updates row with destination address id
	* @return void
	*/
	public function updateDestinationAddressId()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['destination_address_id']}     = '{$this->destination_address_id}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updatePrices. Updates row with courier and final prices
	* @return void
	*/
	public function updatePrices()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['courier_price']}     = '{$this->courier_price}',
					{$this->fields['final_price']}     = '{$this->final_price}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;

	}

	/**
	* updateService. Updates row with courier service
	* @return void
	*/
	public function updateService()
	{

		$sql = "UPDATE {$this->tablename}
				SET {$this->fields['courier_service_id']}   = '{$this->courier_service_id}'
				WHERE {$this->pkey} = '{$this->id}'
				";

		$result = Db::query($sql);
		return;
	}

	/**
     * Indicates if this parcel group contains purely documents.
     *
     * @return unknown
     */
	public function isDocumentsFlag()
	{
		$isDocs = true;
		foreach ($this->getParcels() as $parcel)
		{
			// Check if not documents.
			if (!$parcel->getIsDocuments()) $isDocs = false;
		}
		return $isDocs;
	}


	/**
	 * The volumetric weight is dependent up the courier
	 * service to be used for parcel group.
	 *
	 * @param Courier Service Object $theCourierService
	 */

	public function calculateVolumetricWeight ($theCourierService)
	{
		// Volumetic weight calculations can be courier dependent.
		// The volumetric weight calculation method is in the courier class
		// (hence can be overwritten for a particular courier if requird).
		// Get the appropriate courier class for this service.
		$courier = CourierBase::getCourierClass($theCourierService->getClassCode());
		//
		$vm_weight = 0;
		//
		foreach ($this->getParcels() as $parcel)
		{
			$vm_weight += $courier->getVolumetricWeight($parcel, $theCourierService->getVolumetricDenominator());
		}
		//
		return $vm_weight;
	}

	/**
	 * The volumetric weight (hence chargeable weight) is dependent up the courier
	 * service to be used for parcel group.
	 * This calculates the chargeable weight for this
	 * parcel group, based on the courier service passed.
	 *
	 * @param Courier Service Object $theCourierService
	 */
	public function calculateChargeableWeight($theCourierService)
	{
		// Volumetic weight calculations can be courier dependent.
		// The volumetric weight calculation method is in the courier class
		// (hence can be overwritten for a particular courier if requird).
		// Get the appropriate courier class for this service.
		$courier = CourierBase::getCourierClass($theCourierService->getClassCode());
		//
		$chargeWeight = 0;
		//
		foreach ($this->getParcels() as $parcel)
		{
			$vm_weight = $courier->getVolumetricWeight($parcel, $theCourierService->getVolumetricDenominator());
			$physical_weight = $parcel->getWeight();
			//
			$chargeWeight += ($physical_weight > $vm_weight) ? $physical_weight : $vm_weight;
		}
		//
		return $chargeWeight;
	}

	/**
     * Get Chargeable weight for CURRENTLY SELECTED Courier Service
     *
     * @return float weight
     */
	public function getChargeableWeight()
	{
		// use the courier service selected for this parcel group to
		// calculate the chargeable weight.
		return $this->calculateChargeableWeight($this->getCourierService());
	}

    /**
     * Unserialize the object and return instance of object.
     *
     * @param the serialized string
     */
    public static function unserialize ($serialStr)
    {
  	return parent::unserializeDbObject(__CLASS__, $serialStr);
    }

    public function getCollectionDate()
    {
    	return strtotime($this->collection_date);
    }
    public function setCollectionDate ($collection_date)
    {
    	$this->collection_date = date("Y-m-d H:i:s", $collection_date);
    }

    /**
     * Reference for this parcel group shown to the customer
     *
     */
    public function getParcelGroupRef()
    {
  	return str_pad($this->getId(), 6, "0", STR_PAD_LEFT);
    }

    /**
     * Reason customer selected for export
     *
     */
    public function getExportReason() { return $this->export_reason; }
    public function setExportReason($reason) { $this->export_reason = $reason; }

    /**
     * VAT Status - selected
     *
     * @return string - see constants
     */
    public function isVatCharged()
    {
    	if ($this->vat_status == self::VAT_STATUS_STANDARD) return true;
    	if ($this->vat_status == self::VAT_STATUS_NONE) return false;


    	// If vat status not known, then lookup.
    	// Calculate based on destination and collection countries
    	$collectionCountry = new Country($this->getCollectionCountryId());
    	$destinationCountry = new Country($this->getDestinationCountryId());

    	$this->vat_status = self::VAT_STATUS_NONE;
    	if (($collectionCountry->getIsoCode() == Country::ISO_UK)  && ($destinationCountry->getVatCharged()))
    	{
    		$this->vat_status = self::VAT_STATUS_STANDARD;
    	}
    	else if (($destinationCountry->getIsoCode() == Country::ISO_UK)  && ($collectionCountry->getVatCharged()))
    	{
    		$this->vat_status = self::VAT_STATUS_STANDARD;
    	}
    	//
    	return ($this->vat_status == self::VAT_STATUS_STANDARD);
    }

    /**
     * If customer selects a vat status of export, then
     * they need to enter the vat number.
     *
     * @return string
     */
	public function getVatNumber() { return ($this->vat_number); }
	public function setVatNumber($number) { $this->vat_number = $number; }

	/**
     * Flag to indicate if this parcel group has been delivered.
     *
     * @return bool
     */
	public function getDeliveredFlag() { return ($this->delivered_flag == 1); }
	public function setDeliveredFlag($delivered_flag) { $this->delivered_flag = ($delivered_flag ? 1 : 0); }

	/**
	 * DEPRECATED ***
	 * Gets earliest collection time as string.
	 * - time types should be passed as times and not strings.
	 * - use getEarlientCollTimeAsTime
	 */
	public function getEarliestCollTime()	{	return $this->earliest_collection_time; }
	public function getEarliestCollTimeAsTime()
	{
		return strToTime($this->earliest_collection_time);
	}
	/**
	 * DEPRECATED ***
	 * Gets latest collection time as string.
	 * - time types should be passed as times and not strings.
	 * - use getLatestCollTimeAsTime
	 */
	public function getLatestCollTime()			{	return $this->latest_collection_time; }
	public function getLatestCollTimeAsTime()
	{
		return strToTime($this->latest_collection_time);
	}

	/**
	 * Get units
	 *
	 * @return unknown
	 */
	public function getUnit()
	{
		return $this->unit;
	}


	/**
	 * Gives time that the booking was created
	 * - time types should be passed as times and not strings.
	 * - use getEarlientCollTimeAsTime
	 *
	 */
	public function getCreationTime()
	{
		return strToTime($this->added_on);
	}

	/**
	 * Check status, returns boolean indicate
	 * if the status passed has been set.
	 *
	 * @param Heat beat status constant. $status
	 * @return boolean
	 */
	public function checkHeartbeatStatus($status)
	{
		// check if status bit is set.
		return ($this->heartbeat_status & $status);
	}
	public function setHeartbeatStatus($status)
	{
		$this->heartbeat_status = intval($this->heartbeat_status);
		// bitwise or to update status
		$this->heartbeat_status |= $status;
	}

	/**
	 * Collection Address Postcode
	 *
	 * @return string
	 */
	public function getCollectionPostcode()
	{
		$postcode = "";

		// Collection postcode was originally stored in parcel group
		// - because postcode was specified separately from address.
		// But this is no longer then case, so collection postcode is got from collection address.
		$address = $this->getCollectionAddress();

		if ($address != null)
		{
			$postcode = $address->getPostcode();
		}

		return $postcode;
	}

	////////////////////////////////////////////////////
	// Getters
	////////////////////////////////////////////////////

	// customer parcel specific getters
	public function getId()						{	return $this->id; }
	public function getCollectionAddressId()	{	return (int)$this->collection_address_id; }
	public function getCollectionCountryId()	{	return (int)$this->collection_country_id; }
	public function getDestinationAddressId()	{	return $this->destination_address_id; }
	public function getDestinationCountryId()	{	return $this->destination_country_id; }
	public function getDestinationSubzone()		{	return $this->destination_subzone; }
	public function getCourierServiceId()		{	return $this->courier_service_id; }
	public function getProductId()				{	return $this->product_id; }
	public function getTotalParcels()			{	return $this->total_parcels; }
	public function getCourierPrice($formatted = false)
	{
		return ($formatted ? util_money($this->courier_price) : $this->courier_price);
	}
	public function getPackageDeliveryPrice($formatted = false)
	{
		return ($formatted ? util_money($this->final_price) : $this->final_price);
	}
	public function getVAT($formatted = false)
	{
		return ($formatted ? util_money($this->vat) : $this->vat);
	}
	public function getPackageDeliveryPricePlusVAT($formatted = false)
	{
		$val = $this->getPackageDeliveryPrice() + $this->getVAT();
		return ($formatted ? util_money($val) : $val);
	}
	public function getTotalPrice($formatted = false)
	{
		$val = $this->getPackageDeliveryPrice() + $this->getInsurancePremium();
		return ($formatted ? util_money($val) : $val);
	}
	public function getTotalPricePlusVat($formatted = false)
	{
		$val = $this->getTotalPrice() + $this->getVAT();
		return ($formatted ? util_money($val) : $val);
	}

	public function getDiscount()				{	return $this->discount; }
	public function getInsurancePremium($formatted = false)
	{
		return ($formatted ? util_money($this->insurance_premium) : $this->insurance_premium);
	}
	public function getInsuranceCover($formatted = false)
	{
		return ($formatted ? util_money($this->insurance_cover) : $this->insurance_cover);
	}
	public function getBookingStatus()			{	return $this->booking_status; }
	public function getContents()			{	return $this->contents; }
	public function getContentsValue()			{	return $this->contents_value; }

	// general and audit getters
	public function getRowOrder()			{	return $this->orderq; }
	public function getActive()				{	return $this->active; }
	public function getDeleted()			{	return $this->deletedq; }
	public function getAddedOn()			{	return $this->added_on; }
	public function getAddedBy()			{	return $this->added_by; }
	public function getChangedOn()			{	return $this->changed_on; }
	public function getChangedBy()			{	return $this->changed_by; }

	/**
     * Get array of parcel objects
     *
     * @return Array of Parcels
     */
	public function getParcels()
	{
		if ($this->get_parcels_from_db)
		{
			if ($this->getId() > 0)
			{
				$ParObj  = new Parcel;
				$where   = " parcel_group_id = " . $this->getId();

				$this->parcelArray = $ParObj->getAnyParcel($where);
			}
			$this->get_parcels_from_db = false;
		}
		return $this->parcelArray;
	}
	public function clearParcels ()
	{
		$this->parcelArray = array();
		// If we are clearning parcels, need to make sure we don't
		// try and fill from the database!
		$this->get_parcels_from_db = false;
		//
		$this->calculateTotalWeight();
	}
	public function addParcel ($parcel)
	{
		if ($this->parcelArray == null)
		{
			$this->parcelArray = array();
		}
		$this->parcelArray[] = $parcel;
		$this->calculateTotalWeight();
	}

	/**
	 * Sum all parcels to determine total parcel weight
	 *
	 */
	private function calculateTotalWeight()
	{
		$this->total_weight = 0;
		//
		foreach ($this->getParcels() as $parcel)
		{
			$this->total_weight += $parcel->getWeight();
		}
	}

	/**
	 * Get the total weight of all parcels
	 *
	 * @return unknown
	 */
	public function getTotalWeight()
	{
		// If the weight is set to 0, force calculation of weights just in case!
		if ($this->total_weight == 0) $this->calculateTotalWeight();
		return $this->total_weight;
	}

	public function getCourierServiceText()
	{
		return $this->getCourierService()->getCourier()->getName() . " - " . $this->getCourierService()->getName();
	}

	/**
	 * Get the basket id
	 *
	 */
	public function getBasketItemId($id = -1)
	{
		if ($id == -1) $id = $this->getId();

		$BimObj   = new Basketitem;
		$where    = " parcel_group_id = " . $id;
		$items    = $BimObj->getAnyBasketitem($where);

		if (sizeof ($items) > 0) return $items[0]->getId();
		return 0;
	}

	public function getAddressPDF($id)
	{
		$PgaObj   = new Parcelgroupaddress($id);

		return $PgaObj->getVerticalAddressPDF();
	}

	/**
     * Gets the collection address object for this parcel group
     *
     */
	public function getCollectionAddress()
	{
		//echo $this->getCollectionAddressId(); exit;
		if ($this->collection_address == null)
		{
			$this->collection_address = new Parcelgroupaddress($this->getCollectionAddressId());
			print_r($this->collection_address);
		}
		return $this->collection_address;
	}

	/**
     * Get the destination address object for this parcel group
     *
     */
	public function getDestinationAddress()
	{
		if ($this->destination_address == null)
		{
			$this->destination_address = new Parcelgroupaddress($this->getDestinationAddressId());
		}
		return $this->destination_address;
	}

	public function getCollectionSummary()
	{
		if ($this->collection_summary == null)
		{
			$this->collection_summary = new LocationSummary();
		}
		return $this->collection_summary;
	}
	public function getDestinationSummary()
	{
		if ($this->destination_summary == null)
		{
			$this->destination_summary = new LocationSummary();
		}
		return $this->destination_summary;
	}

	/**
     * The package number is the parcel group id shown to the customer.
     *
     */
	public function getPackageNumber ()
	{
		return str_pad($this->id, 6, "0", STR_PAD_LEFT);
	}

	/**
	 * Error Status indicating if there were any problems making bookings.
	 *
	 * @return unknown
	 */
	public function getErrorStatus()
	{
		return $this->error_status;
	}
	public function setErrorStatus($status)
	{
		$this->error_status = $status;
	}

	/***
	 * Set courier booking reference.
	 */
	public function getCourierBookingRef()
	{
		return $this->courier_booking_ref;
	}
	public function setCourierBookingRef($val)
	{
		$this->courier_booking_ref = $val;
	}

	/**
	 * Override clone
	 *
	 */
	public function __clone()
	{
		// set the id to 0 if being cloned (
		$this->id = 0;
	}

	////////////////////////////////////////////////////
	// Setters
	////////////////////////////////////////////////////

	// customer parcel specific setters
	public function setCollectionAddressId($collection_address_id)		{	$this->collection_address_id = $collection_address_id; }
	public function setCollectionCountryId($collection_country_id)
	{
		// If the collection country is changed, then update the vat status.
		if ($this->collection_country_id != $collection_country_id) $this->vat_status = self::VAT_STATUS_NOT_SET;
		$this->collection_country_id = $collection_country_id;
	}
	public function setDestinationAddressId($destination_address_id)	{	$this->destination_address_id = $destination_address_id; }
	public function setDestinationCountryId($destination_country_id)
	{
		// If the country is changed, clear the VAT status flag
		if ($this->destination_country_id != $destination_country_id) $this->vat_status = self::VAT_STATUS_NOT_SET;
		$this->destination_country_id = $destination_country_id;
	}
	public function setDestinationSubzone($destination_subzone)		{	$this->destination_subzone = $destination_subzone; }
	public function setEarliestCollTime($earliest_collection_time)	{	$this->earliest_collection_time = $earliest_collection_time; }
	public function setLatestCollTime($latest_collection_time)		{	$this->latest_collection_time = $latest_collection_time; }
	public function setCourierServiceId($courier_service_id)		{	$this->courier_service_id = $courier_service_id; }
	public function setProductId($product_id)						{	$this->product_id = $product_id; }
	public function setTotalWeight($total_weight)					{	$this->total_weight = $total_weight; }
	public function setUnit($unit)									{	$this->unit = $unit; }
	public function setTotalParcels($total_parcels)					{	$this->total_parcels = $total_parcels; }
	public function setCourierPrice($courier_price)					{	$this->courier_price = $courier_price; }
	public function setPackageDeliveryPrice($final_price)			{	$this->final_price = $final_price; }
	public function setVAT($vat)               					{	$this->vat = $vat; }
	public function setDiscount($discount)							{	$this->discount = $discount; }
	public function setInsurancePremium($insurance)					{	$this->insurance_premium = $insurance; }
	public function setInsuranceCover($insurance)					{	$this->insurance_cover = $insurance; }
	public function setBookingStatus($status)						{	$this->booking_status = $status; }
	public function setContents($contents)						{	$this->contents = $contents; }
	public function setContentsValue($value)						{$this->contents_value = floatval($value); }

	// general and audit setters
	public function setRowOrder($order)						{	$this->orderq = $order; }
	public function setActive($active)						{	$this->active = $active; }
	public function setDeleted($deleted)					{	$this->deletedq = $deleted; }
	public function setAddedOn($added_on)					{	$this->added_on = added_on; }
	public function setAddedBy($added_by)					{	$this->added_by = added_by; }
	public function setChangedOn($changed_on)				{	$this->changed_on = changed_on; }
	public function setChangedBy($changed_by)				{	$this->changed_by = changed_by; }

}
