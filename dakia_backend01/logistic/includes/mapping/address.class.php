<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class Address extends DbAccess3
{
    // consignment service type


    // consignment status
    // - if add a new status, remember to update setStatus()


    //
    private $parcel_list = null;
    private $iso_look_up_flag = true;
    //
    private $debug_flag = false;
    private $delivery_network = null;
    private $error_list = array();

    /**
     * Construct
     *
     * @param id/array
     */
     public function __construct($mixedCreator = null)
    {
        $fieldList = array(
                    'phone_number' => 'string',
                    'company' => 'string',
                    'contact' => 'string',
                    'address_line_1' => 'string',
                    'address_line_2' => 'string',
                    'address_line_3' => 'string',
                    'city' => 'string',
                    'country' => 'string',
                    'postcode' => 'string',
                    'user_id' => 'number',
                    'state' => 'string',
                    'email' => 'string',
                    'country_name'=>'external'
                    );
        //
        parent::__construct("address", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getPhoneNumber()
    {
        return $this->valArray["phone_number"];
    }
    
    public function getId()
    {
        return $this->valArray["id"];
    }
    
    public function getContact()
    {
        return $this->valArray["contact"];
    }
    
    public function getCompany()
    {
        return $this->valArray["company"];
    }
    
    public function getAddressLine1()
    {
        return $this->valArray["address_line_1"];
    }
    
    public function getAddressLine2()
    {
        return $this->valArray["address_line_2"];
    }
   
   
    public function getAddressLine3()
    {
        return $this->valArray["address_line_3"];
    }
    
     public function getReference()
    {
        return $this->valArray["reference"];
    }
    
    public function getCity()
    {
        return $this->valArray["city"];
    }
    
    public function getCountry()
    {
        return $this->valArray["country"];
    }
    
    public function getPostCode()
    {
        return $this->valArray["postcode"];
    }  
    
    public function getUserId()
    {
        return $this->valArray["user_id"];
    }  

  
    /**
     * Get list of consignment objects, using sql given
     *
     * @param string $sql
     */
    public static function getAddressListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
     public static function getTotalAddressListFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
   
    public function setPhoneNumber($val)
    {
        $this->valArray["phone_number"]=$val;
		$this->modifyArray["phone_number"]=$val;
    }
    
    
    public function setCompany($val)
    {
        $this->valArray["company"]=$val;
		$this->modifyArray["company"]=$val;
    }
    
    public function setContact($val)
    {
        $this->valArray["contact"]=$val;
		$this->modifyArray["contact"]=$val;
    }
    
    public function setAddress_line_1($val)
    {
        $this->valArray["address_line_1"]=$val;
		$this->modifyArray["address_line_1"]=$val;
    }
    
    public function setAddress_line_2($val)
    {
        $this->valArray["address_line_2"]=$val;
		$this->modifyArray["address_line_2"]=$val;
    }
    
    public function setAddress_line_3($val)
    {
        $this->valArray["address_line_3"]=$val;
		$this->modifyArray["address_line_3"]=$val;
    }
    
    public function setCity($val)
    {
        $this->valArray["city"]=$val;
		$this->modifyArray["city"]=$val;
    }
    
    public function setCountry($val)
    {
        $this->valArray["country"]=$val;
		$this->modifyArray["country"]=$val;
    }
    
    public function setPostCode($val)
    {
        $this->valArray["postcode"]=$val;
		$this->modifyArray["postcode"]=$val;
    }
    
    public function setUserId($val)
    {
        $this->valArray["user_id"]=$val;
        $this->modifyArray["user_id"]=$val;
		
    }
      public static function getTotalNumberOfAddressFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}
    

}  // class