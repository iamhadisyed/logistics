<?php

////////////////////////////////////////////////////
//
// Class for dealing with Parcel group addresses
//
////////////////////////////////////////////////////

/**
 * Parcelgroupaddress - Parcel group address class
 * @package Address
 */
class Parcelgroupaddress extends DbAccess
{
    const TYPE_COLLECTION = "C";
    const TYPE_DESTINATION = "D";
    const TYPE_BILLING = "B";

    protected $type;
    protected $fullname;
    protected $company;
    protected $address_line_1;
    protected $address_line_2;
    protected $address_line_3;
    protected $town;
    protected $region;
    protected $postcode;
    protected $country_id;
    protected $telephone;
    protected $usa_state_code;
    protected $original_parcel_group_address_id;
    protected $routing_code;
    protected $collection_point;

    protected $orderq;
    protected $active;
    protected $deletedq;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    // Address country
    private $country = null;
    private $usaState = null;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($id = 0)
    {
        $this->tablename = 'parcel_group_addresses';
        $this->pkey      = 'id';
        $this->fields    =
            array(  'type'                    => 'type',
                    'fullname'                => 'fullname',
                    'company'                 => 'company',
                    'address_line_1'          => 'address_line_1',
                    'address_line_2'          => 'address_line_2',
                    'address_line_3'          => 'address_line_3',
                    'town'                    => 'town',
                    'region'                  => 'region',
                    'postcode'                => 'postcode',
            		'usa_state_code'          => 'usa_state_code',
                    'country_id'              => 'country_id',
                    'telephone'               => 'telephone',
            		'original_parcel_group_address_id' => 'original_parcel_group_address_id',
            		'routing_code'            => 'routing_code',
            		'collection_point'        => 'collection_point',
                    'orderq'                  => 'orderq',

                    'active'                  => 'active',
                    'deletedq'                => 'deletedq',

                    'added_by'                => 'added_by',
                    'added_on'                => 'added_on',
                    'changed_by'              => 'changed_by',
                    'changed_on'              => 'changed_on'
                    );

        $this->_construct($id);

        return $this;
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * Copy a parcel group address details to this address
     *
     * @param unknown_type $parcelAddressToCopy
     */
    public function Copy ($parcelAddressToCopy)
    {
		$this->type = $parcelAddressToCopy->type;
		$this->fullname = $parcelAddressToCopy->fullname;
		$this->company = $parcelAddressToCopy->company;
		$this->address_line_1 = $parcelAddressToCopy->address_line_1;
		$this->address_line_2 = $parcelAddressToCopy->address_line_2;
		$this->address_line_3 = $parcelAddressToCopy->address_line_3;
		$this->town = $parcelAddressToCopy->town;
		$this->region = $parcelAddressToCopy->region;
		$this->postcode = $parcelAddressToCopy->postcode;
		$this->country_id = $parcelAddressToCopy->country_id;
		$this->telephone = $parcelAddressToCopy->telephone;
		$this->usa_state_code = $parcelAddressToCopy->usa_state_code;
		$this->original_parcel_group_address_id = $parcelAddressToCopy->original_parcel_group_address_id;
		$this->routing_code = $parcelAddressToCopy->routing_code;
		$this->collection_point = $parcelAddressToCopy->collection_point;

		$this->orderq = $parcelAddressToCopy->orderq;
		$this->active = $parcelAddressToCopy->active;
		$this->deletedq = $parcelAddressToCopy->deletedq;
		//$this->added_on = $parcelAddressToCopy->added_on;
		//$this->added_by = $parcelAddressToCopy->added_by;
		//$this->changed_on = $parcelAddressToCopy->changed_on;
		//$this->changed_by = $parcelAddressToCopy->changed_by;
    }

    /**
     * getAnyAddress. Returns the object array of addresses.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyAddress($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret = array();
        $ids = $this->getAnyAddressIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Parcelgroupaddress($i);
            }

            return $ret;
        }
        else return $ret;
    }

    /**
     * getAnyAddressIds. Returns the object array of Address ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyAddressIds($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " and $where";
        if ($activeOnly) $whereSql .= " and active=1";
           $sql = "SELECT {$this->pkey}
                  FROM {$this->tablename}
                  $whereSql
              ORDER BY $orderBy";

        $this->sql = $sql;

        $result = Db::query($sql);
        while (list($pId) = mysql_fetch_row($result))
        {
            $ret[] = $pId;
        }

        return $ret;
    }

    /**
     * save. Updates table.
     * @return void
     */
    public function save()
    {
        $this->changed_on = date("Y-m-d H:i:s");
        $this->changed_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');

        if ($this->id < 1)
        {
            $this->added_on = date("Y-m-d H:i:s");
            $this->added_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');
        }

        // save
        parent::save(false);
    }

    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['deletedq']} = 'Y'
                 WHERE {$this->pkey} = '{$this->id}'
                 ";

        $result = Db::query($sql);
        return;

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
     * updateAddress. Updates row with address details
     * @return void
     */
    public function updateAddress()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['type']}               = '{$this->type}',
                       {$this->fields['fullname']}           = '{$this->fullname}',
                       {$this->fields['address_line_1']}     = '{$this->address_line_1}',
                       {$this->fields['address_line_2']}     = '{$this->address_line_2}',
                       {$this->fields['address_line_3']}     = '{$this->address_line_3}',
                       {$this->fields['town']}               = '{$this->town}',
                       {$this->fields['region']}             = '{$this->region}',
                       {$this->fields['postcode']}           = '{$this->postcode}',
                       {$this->fields['country_id']}         = '{$this->country_id}',
                       {$this->fields['telephone']}          = '{$this->telephone}'
                 WHERE {$this->pkey} = '{$this->id}'
                 ";
#echo $sql; die;
        $result = Db::query($sql);
        return;

    }


    /**
     * Routing Code.
     *
     * @return unknown
     */
    public function getRoutingCode()
    {
    	return $this->routing_code;
    }
    public function setRoutingCode($val)
    {
    	$this->routing_code = $val;
    }

    /**
     * Gets the country id associated with this address
     *
     */
    public function getCountry()
    {
    	if ($this->country == null)
    	{
    		$this->country = new Country();
    		// Is there a valid country id
    		if ($this->getCountryId() > 0)
    		{
    			$this->country->fillFromDatabase($this->getCountryId());
    		}
    	}
    	return $this->country;
    }



    /**
     * Get name functions.
     * Full name is set, but provide readonly methods to
     * get first and surname.
     *
     */
    public function getFullname()
    {
    	return $this->fullname;
    }
    /**
     * Get name functions.
     * Full name is set, but provide readonly methods to
     * get first and surname.
     *
     */
    public function getFirstName()
    {
    	$pos = strrpos(" ", $this->getFullname());
    	//
    	if ($pos > 0)
    	{
    		return trim(substr($this->getFullName(), 0, $pos));
    	}
    	return "";
    }
    /**
     * Get name functions.
     * Full name is set, but provide readonly methods to
     * get first and surname.
     *
     */
    public function getLastName()
    {
    	$pos = strrpos(" ", $this->getFullname());
    	//
    	if ($pos > 0)
    	{
    		return trim(substr($this->getFullName(), $pos));
    	}
    	return $this->getFullname();
    }

    /**
     * Company name
     *
     */
    public function getCompany()
    {
    	return $this->company;
    }
    public function setCompany ($company)
    {
    	$this->company = $company;
    }

    /**
     * Collection Point
     *
     */
    public function getCollectionPoint()
    {
    	return $this->collection_point;
    }
    public function setCollectionPoint ($value)
    {
    	$this->collection_point = substr($value, 0, 200);
    }

    /**
     * Places address in a continuous array,
     * removing blank lines.  Always pads address
     * array out to 5 lines.
     *
     * @return array
     */
    function getAddressInArray()
    {
    	$addrArray = array();

    	if ($this->getAddress1() != "") $addrArray[] = $this->getAddress1();
    	if ($this->getAddress2() != "") $addrArray[] = $this->getAddress2();
    	if ($this->getAddress3() != "") $addrArray[] = $this->getAddress3();
    	if ($this->getTown() != "") $addrArray[] = $this->getTown();
    	if ($this->getRegion() != "") $addrArray[] = $this->getRegion();

    	// Check for USA State
    	$country = $this->getCountry();
    	if ($country->getIsoCode() == Country::ISO_USA)
    	{
    		if ($this->getUsaStateCode() != "")
    		{
    			$usaState = $this->getUsaState();
    			if ($usaState != null) $addrArray[] = $usaState->getState();
    		}
    	}

    	if ($this->getPostcode() != "") $addrArray[] = $this->getPostcode();
    	if ($country->getName() != "") $addrArray[] = $country->getName();

    	return $addrArray;
    }


    /**
     * Get the address Type
     *
     * @return Addrss type constant
     */
    public function getType()
    {
    	// Ensure a valid type is returned
    	if ($this->type == self::TYPE_DESTINATION) return $this->type;
    	if ($this->type == self::TYPE_BILLING) return $this->type;
    	return self::TYPE_COLLECTION;
    }
    public function setType($type)
    {
    	$this->type = $type;
    }

    /**
     * Address postcode.  The compact version givens post code with all spaces stripped out.
     *
     * @return string
     */
    public function getPostcode()
    {
    	return $this->postcode;
    }
    public function getCompactPostcode()
    {
    	return str_replace(" ", "", $this->postcode);
    }

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                     {     return $this->id; }
    public function getAddress1()               {     return $this->address_line_1; }
    public function getAddress2()               {     return $this->address_line_2; }
    public function getAddress3()               {     return $this->address_line_3; }
    public function getTown()                   {     return $this->town; }
    public function getRegion()                 {     return $this->region; }
    public function getCountryId()              {     return $this->country_id; }
    public function getPhoneNumber()              {     return $this->telephone; }

    // general and audit getters
    public function getRowOrder()               {     return $this->orderq; }
    public function getActive()                 {     return $this->active; }
    public function getDeleted()                {     return $this->deletedq; }
    public function getAddedOn()                {     return $this->added_on; }
    public function getAddedBy()                {     return $this->added_by; }
    public function getChangedOn()              {     return $this->changed_on; }
    public function getChangedBy()              {     return $this->changed_by; }

	// special get methods
	public function getVerticalAddressPDF()
	{
		return $this->getAddressStr(",\n");
	}

	public function getAddressStr($separator = ", ")
	{

		$str_address = $this->getFullname();
		//
		foreach ($this->getAddressInArray() as $line)
		{
			if ($str_address != "") $str_address .= $separator;
			//
			$str_address .= $line;
		}
		//
		return $str_address;
	}

	/**
	 * Gets the 2 diget iso code for this country.
	 *
	 * @return string
	 */
	public function getCountryCode ()
	{
		$country = $this->getCountry();
		if ($country != null)
		{
			return $country->getIsoCode();
		}
		return "";
	}
	// Readonly, country name
	public function getCountryName ()
	{
		$country = $this->getCountry();
		if ($country != null)
		{
			return $country->getName();
		}
		return "";
	}

	/**
	 * USA State Code
	 *
	 */
	public function getUsaStateCode() { return $this->usa_state_code; }
	public function setUsaStateCode($state_code)
	{
		if ($this->usa_state_code != $state_code)
		{
			$this->usaState = null;
			$this->setRoutingCode("");
		}
		$this->usa_state_code = $state_code;
	}
	public function getUsaState()
	{
		if ($this->usaState == null)
		{
			if ($this->getUsaStateCode() != "")
			{
				$this->usaState = UsaState::getStateFromCode($this->getUsaStateCode());
			}
		}
		return $this->usaState;
	}

	/**
	 * A parcel group address may be updated.  This provides
	 * a link to the orginal unalter address.
	 *
	 * @param int
	 */
	public function getOriginalParcelGroupId()
	{
		return (int) $this->original_parcel_group_address_id;
	}
	public function setOriginalParcelGroupId($id)
	{
		$this->original_parcel_group_address_id = $id;
	}

    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setFullname($fullname)              {     $this->fullname = $fullname; }
    public function setAddress1($address_line_1)
    {
    	if ($this->address_line_1 != $address_line_1) $this->setRoutingCode("");
    	$this->address_line_1 = $address_line_1;
    }
    public function setAddress2($address_line_2)        {     $this->address_line_2 = $address_line_2; }
    public function setAddress3($address_line_3)        {     $this->address_line_3 = $address_line_3; }
    public function setTown($town)
    {
    	if ($this->town != $town) $this->setRoutingCode("");
    	$this->town = $town;
    }
    public function setRegion($region)
    {
    	if ($this->region != $region) $this->setRoutingCode("");
    	$this->region = $region;
    }
    public function setPostcode($postcode)
    {
    	if ($this->postcode != $postcode) $this->setRoutingCode("");
    	$this->postcode = strToUpper($postcode);
    }

    public function setCountryId($country_id)
    {
    	if ($this->country_id != $country_id) $this->setRoutingCode("");
    	//
    	$this->country = null;  // ensure the country is cleared.
    	$this->country_id = $country_id;
    }

    public function setPhoneNumber($telephone)            {     $this->telephone = $telephone; }

    // general and audit setters
    public function setRowOrder($order)                 {     $this->orderq = $order; }
    public function setActive($active)                  {     $this->active = $active; }
    public function setDeleted($deleted)                {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)               {     $this->added_on = added_on; }
    public function setAddedBy($added_by)               {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)           {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)           {     $this->changed_by = changed_by; }


    public static function CreateCopy ($addressObject)
    {
    	$newAddress = clone($addressObject);
    	$newAddress->id = 0;
    	return $newAddress;
    }
}
