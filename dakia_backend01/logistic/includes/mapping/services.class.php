<?php
// get settings
//require_once("includes/settings/common.inc.php");

class Services extends DbAccess3
{
	// user types (if change types, update type list function).
	const USER_TYPE_CLIENT    = "client";
	const USER_TYPE_WAREHOUSE = "warehouse";
	const USER_TYPE_MANAGER   = "manager";
	const USER_TYPE_ADMIN     = "admin";

	// Privileges
	const PRIVILEGE_CLIENT = 1;
	const PRIVILEGE_IMPORT = 2;
	const PRIVILEGE_WAREHOUSE_LIST = 4;
	const PRIVILEGE_EXPORT = 8;
	const PRIVILEGE_USER_LIST = 16;
	const PRIVILEGE_END_OF_DAY = 32;
	
	// packaget types for courier services
	const PACKAGE_TYPE_PARCEL = 1;
	const PACAKGE_TYPE_DOCUMENT = 2;
	const PACKAGE_TYPE_BOTH = 0;
	//
	//
	const PRICE_CALCULATION_TOTAL_WEIGHT = 0;
	const PRICE_CALCULATION_INDIVIDUAL_PARCEL = 1;

	public function __construct($mixedCreator = null, $debug = false)
	{
		$this->tablename = 'services';
                $this->pkey      = 'id';
		$fieldList = array
                    (
                    'name'      		        => 'string',
                    'code'      		        => 'string',
                    'carrier_id'   		        => 'number',
                    'account_number'   		    => 'string',
                    'type' 			            => 'string',
                    
                    'from_weight' 		        => 'number',
                    'to_weight' 		        => 'number',
                    'wieght_type' 		        => 'number',
                    'supplier' 			        => 'string',
                    'service_type'		        => ['enum' => ['D','C','B','DO'], 'default'=> 'D'],
                    'drop_off_service_id'		=> 'number',
                    'description'		        => 'string',
                    'fuel_surcharge_cost'      	=> 'number',
                    'fuel_surcharge'           	=> 'number',
                    'fuel_surcharge_type'      	=> 'string',
                    'max_length'               	=> 'number',
                    'max_width'                	=> 'number',
                    'max_height'               	=> 'number',
                    'max_volumetric_weight'    	=> 'number',
                    'volumetric_denominator'    => 'number',
                    'send_data_courier'		    => 'number',
                    'is_document'		        => 'number',
                    'friday_only_flag'         	=> 'number',
                    'saturday_only_flag'        => 'number',
                    'sunday_only_flag'          => 'number',
                    'active'                   	=> 'string',
                    'deletedq'                 	=> 'string',
                    'added_on'                 	=> 'string',
                    'added_by'                 	=> 'string',
                    'changed_on'               	=> 'string',
                    'changed_by'               	=> 'string',
                    'uploaded_currency'		    => 'string',
                    'uploaded_currency_value'	=> 'number',
                    'registration_fee'		    => 'number',
                    'weight_after'		        => 'number',
                    'aditional_charge'		    => 'number',
                    'origin_country'		    => 'number',
                    'is_untrack'		        => 'bit',
                    'account_owner'             => 'number',
                    'remotearea'		        => ['enum' => ['ON_WEIGHT','ON_PIECE'], 'default'=> ''],
                    'carrier_address_limit'	    => 'number',
                    'label_class_name'		    => 'string',
                    'transit_time'		        => 'number',
                    'required_email'            => 'number',
                    'required_telephone'        => 'number',
                    'shipment_type'             => ['enum' => ['LETTER','PARCEL'], 'default'=> ''],
                    'pre_sort'                  => ['enum' => ['YES','NO'], 'default'=> 'NO'],
                    'proforma_invoice'          => 'number',
                    'agent_dispatch'            => ['enum' => ['Y','N'], 'default'=> 'N'],
                    'brief_manifest'            => ['enum' => ['Y','N'], 'default'=> 'N'],
                    'insurance_available'       => 'number',
                    'delivery_mode'             => 'number',
                    'vol_wgt_formula'           => 'string',
                    'is_remotearea'             => 'string',
                    'is_customized'             => 'bit',
                    'pre_advise'                => 'string',
                    'pre_alert'                 => 'string',
                    'pre_alert_email'           => 'string',
                    'cut_off_time'              => 'string',
                    'label_charges'             => 'number',
                    'product_owner'             => 'number',
                    'allow_oversize' => 'number',
                    'allow_overweight' => 'number',
                    'maximum_allowed_dimension' => 'number',
                    'maximum_dim_formula' => 'string',
                    'validation_type' => ['enum' => ['mail','courier'],'default' => 'mail'],
                    'zone_type' => ['enum' => ['country','postcode'],'default' => 'country'],
                    'tariff_type' => ['enum' => ['single','multi'],'default' => 'single'],
                    'girth' => 'number',
                    'girth_formula' => 'string',
                    'mail_type'                 => ['enum' => ['letter','boxable','non-boxable'], 'default'=> ''],
                    'mail_option'               => ['enum' => ['commercial','freight_to_post'], 'default'=> ''],
                    'carrier_service_code'      => 'string',
                    'is_reschedulable'          => 'bit',
                    'is_eori_required'          => 'bit',
                    'delivery_type' => ['enum' => ['all', 'economy','priority']],
                    'is_commercials' => ['enum' => ['required', 'not required']],
                    'is_cn' => ['enum' => ['required', 'not required']],
                    
                    'service_country' 		    => 'undefined',
                    'carrier_logo'              => 'undefined',
                    'carrier_name'              => 'undefined',
                    'carrier_cut_off'           => 'undefined',
                    'carrier_currency_code'     => 'undefined',
                    'carrier_country_iso'       => 'undefined',
                    'carrier_country_name'      => 'undefined',
                    'user_service_status'       => 'undefined',
                    'shipment_label'       => 'undefined',
                    'is_dead_weight'       => 'undefined',
                    'is_over_size'       => 'undefined',
                    'user_account_id'       => 'undefined',
                    'service_account_id'       => 'undefined',
                    'user_services_routing_account_id'       => 'undefined',
                    'user_services_routing_from_weight'       => 'undefined',
                    'user_services_routing_to_weight'       => 'undefined',
                    'user_services_routing_is_remotearea'       => 'undefined',
                    'user_services_routing_is_over_size'       => 'undefined',
                    'user_services_routing_is_over_label'       => 'undefined',
                    'user_services_routing_is_dead_weight'       => 'undefined',
                    'user_services_routing_label_chagres'       => 'undefined',
                    'user_services_routing_is_over_label'       => 'undefined'
                    
                );

                
		parent::__construct("services", 'id', $fieldList, $mixedCreator, $debug);
	}

	

	/****
	 * Get/Set the user password
	 *
	 * @param string
	 */
    public  function getName()
    {
		
        return strtoupper($this->valArray["name"]);
    }
	
	public  function getVolumetricDenominator()
    {
        return $this->valArray["volumetric_denominator"];
    }
    public  function setVolumetricDenominator($val)
        {
            $this->valArray["volumetric_denominator"] = $val;
            $this->modifyArray["volumetric_denominator"] = $val;
        }
	
    public  function getCode()
    {
        return $this->valArray["code"];
    }
	
	public  function getCarrier()
    {
        return $this->valArray["carrier"];
    }
	public  function getPrivateName()
    {
        return $this->valArray["private_name"];
    }
	
	public function getReference()
	{
		return "S" . substr("00" . $this->valArray["id"], -3);
	}
	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	

	public function setName($val)
	{
		$this->valArray["name"] = $val;
		$this->modifyArray["name"] = $val;
	}
	public function setCode($val)
	{
		$this->valArray["code"] = $val;
		$this->modifyArray["code"] = $val;
	}
	public function setCarrier($val)
	{
		$this->valArray["carrier"] = $val;
		$this->modifyArray["carrier"] = $val;
	}

	public function setServiceCountry($val)
	{
		$this->valArray["service_country"] = $val;
		$this->modifyArray["service_country"] = $val;
	}

	

	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getServicesListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


	
	public static function getTotalNumberOfServiceFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}
	/***
	 * Check if data held by curernt object is valid
	 */
	public function isValid(&$error_list_array)
	{
		if (trim($this->getName()) == "") 
			$error_list_array[] = "Please enter service name.<br>";
		if (trim($this->getCode()) == "") 
			$error_list_array[] = "Please enter service code.<br>";
		if (trim($this->getCarrier()) == "") 
			$error_list_array[] = "Please enter service carrier.<br>";
			
		
		if (trim($this->getOriginCountry()) == "") 
			$error_list_array[] = "Please select sertvice origin country.<br>";
//		if ($this->getFromWeight() == "") 
//			$error_list_array[] = "Please enter from weight.<br>";
//		if ($this->getToweight() == "") 
//			$error_list_array[] = "Please enter to weight.<br>";
		if (trim($this->getWieghtType()) == "") 
			$error_list_array[] = "Please select Package Type.<br>";
		if (trim($this->getActive()) == "") 
			$error_list_array[] = "Please select Status.";
		if (trim($this->getIsUntrack()) == "") 
			$error_list_array[] = "Please select Un-Tracked Service option.<br>";
		
		t("Error list is " . sizeof($error_list_array), __METHOD__);

		return (sizeof($error_list_array) == 0);
	}


    public function getAnyCourierservice($where = "", $activeOnly = true, $orderBy = "id")
    {
        $ret = array();
        $ids = $this->getAnyCourierserviceIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Services($i);
            }
            return $ret;
        }
        else return $ret;
    }

/**
     * getAnyCourierserviceIds. Returns the object array of Courierservice ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyCourierserviceIds($where = "", $activeOnly = true, $orderBy = "id")
    {
        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " AND $where";
        if ($activeOnly) $whereSql .= " AND active=1";
           $sql = "SELECT {$this->pkey}
                  FROM {$this->tablename}
                         $whereSql
              ORDER BY $orderBy";
//echo $sql;
//        $result = DbAccess3::query($sql);
		$result = DbAccess3::getListFromSql(__CLASS__, $sql);

        foreach( $result as $pId)
        {
          $ret[] = $pId->getId();
        }

        return $ret;
    }
	

    // special getters
    public function getLogo($prefix = "")       {
													if (file_exists($this->getLogoPath($prefix) . $this->getId() . ".jpg"))
													{	$file =  $this->getLogoPath($prefix) . $this->getId() . '.jpg';
														return '<img src="' . $file . '" >';
													}

													return false;
												}

    public function getLogoPath($prefix = "")   { return $prefix . CONFIG_LOGO_PATH; }

   
	/**
	 * Service only available on Friday Flag
	 *
	 */
	public function getOnlyAvailableOnFridayFlag()
	{
		return ($this->friday_only_flag == 1);
	}
	public function setOnlyAvailableOnFridayFlag($flag)
	{
		$this->friday_only_flag = ($flag ? 1 : 0);
	}

/**
	 * Set the account number for this courier service
	 *
	 * @param string $accountNumber
	 */
	public function setAccountNumber ($accountNumber)
	{
            $this->valArray["account_number"] = trim($accountNumber);
            $this->modifyArray["account_number"] = trim($accountNumber);
	}
        public  function getAccountNumber()
        {
            return $this->valArray["account_number"];
        }

	public function getContentDescMandatory()
	{
		return ($this->content_mandatory_flag == 1);
	}
	public function setContentDescMandatory($flag)
	{
            $this->valArray["content_mandatory_flag"] = ($flag ? 1 : 0);
            $this->modifyArray["content_mandatory_flag"] = ($flag ? 1 : 0);
	}

	/**
	 * Minimum time between collection and delivery times
	 *
	 * @return int - hours
	 */
	public function getMinimumCollectionWindow()
	{
                return $this->valArray["minimum_collect_window"];
	}
	public function setMinimumCollectionWindow($hours)
	{
                $this->valArray["minimum_collect_window"] = $hours;
                $this->modifyArray["minimum_collect_window"] = $hours;
	}

	/**
	 * Provides delivery details for this service
	 *
	 * @return string
	 */
	public function getDeliveryDetails()
	{
		return $this->delivery_details;
	}
	public function setDeliveryDetails($details)
	{
		$this->delivery_details = substr($details, 0, 250);
	}

	/**
	 * Indicates if this service provides tracking
	 *
	 * @return bool
	 */
	public function getTrackingFlag()
	{
                return $this->valArray["tracking_flag"];
	}
	public function setTrackingFlag ($boolVal)
	{
            $this->valArray["tracking_flag"] = ($boolVal ? 1 : 0);
            $this->modifyArray["tracking_flag"] = ($boolVal ? 1 : 0);
	}

/**
	 * Indicates how the price should be calculated for this service
	 *
	 */
	public function getPriceCalculationType()
	{
		if ($this->price_calculation_flag == self::PRICE_CALCULATION_INDIVIDUAL_PARCEL)
		{
                        return $this->valArray["price_calculation_flag"];
		}
		// default is total weight
		return self::PRICE_CALCULATION_TOTAL_WEIGHT;
	}
	public function setPriceCalculationType ($type)
	{
                $this->valArray["price_calculation_flag"] = ($type == self::PRICE_CALCULATION_INDIVIDUAL_PARCEL)
						? self::PRICE_CALCULATION_INDIVIDUAL_PARCEL : self::PRICE_CALCULATION_TOTAL_WEIGHT;
            $this->modifyArray["price_calculation_flag"] = ($type == self::PRICE_CALCULATION_INDIVIDUAL_PARCEL)
						? self::PRICE_CALCULATION_INDIVIDUAL_PARCEL : self::PRICE_CALCULATION_TOTAL_WEIGHT;
	}


	public function getLatestBookingTime ()
	{
		if ($this->valArray["last_booking_time"] == "") return strtoTime("2000-1-1 7:00:00");
		return strtoTime($this->valArray["last_booking_time"]);
	}

	public function setLatestBookingTime ($time)
	{
            $this->valArray["last_booking_time"] = strtoTime($time);
            $this->modifyArray["last_booking_time"] = strtoTime($time);
	}
        public  function getCollectionTimeGroupId()
        {
            return $this->valArray["collection_time_group_id"];
        }
        public  function setCollectionTimeGroupId($val)
        {
            $this->valArray["collection_time_group_id"] = $val;
            $this->modifyArray["collection_time_group_id"] = $val;
        }
        public  function getUploadedCurrency()
        {
            return $this->valArray["uploaded_currency"];
        }
        public  function setUploadedCurrency($val)
        {
            $this->valArray["uploaded_currency"] = $val;
            $this->modifyArray["uploaded_currency"] = $val;
        }
        public  function setMaxLength($val)
        {
            $this->valArray["max_length"] = $val;
            $this->modifyArray["max_length"] = $val;
        }
        public  function getMaxLength()
        {
            return $this->valArray["max_length"];
        }
        public  function setMaxWidth($val)
        {
            $this->valArray["max_width"] = $val;
            $this->modifyArray["max_width"] = $val;
        }
        public  function getMaxWidth()
        {
            return $this->valArray["max_width"];
        }
        public  function setMaxHeight($val)
        {
            $this->valArray["max_height"] = $val;
            $this->modifyArray["max_height"] = $val;
        }
        public  function getMaxHeight()
        {
            return $this->valArray["max_height"];
        }
        public  function setMaxWeight($val)
        {
            $this->valArray["max_weight"] = $val;
            $this->modifyArray["max_weight"] = $val;
        }
        public  function getMaxWeight()
        {
            return $this->valArray["max_weight"];
        }
        public  function setMaxVolumetricWeight($val)
        {
            $this->valArray["max_volumetric_weight"] = $val;
            $this->modifyArray["max_volumetric_weight"] = $val;
        }
        public  function getMaxVolumetricWeight()
        {
            return $this->valArray["max_volumetric_weight"];
        }
        public  function setMinTotalWeight($val)
        {
            $this->valArray["min_total_weight"] = $val;
            $this->modifyArray["min_total_weight"] = $val;
        }
        public  function getMinTotalWeight()
        {
            return $this->valArray["min_total_weight"];
        }
        public  function setMaxTotalWeight($val)
        {
            $this->valArray["max_total_weight"] = $val;
            $this->modifyArray["max_total_weight"] = $val;
        }
        public  function getMaxTotalWeight()
        {
            return $this->valArray["max_total_weight"];
        }
        public  function setMaxTotalVolumetricWeight($val)
        {
            $this->valArray["max_total_volumetric_weight"] = $val;
            $this->modifyArray["max_total_volumetric_weight"] = $val;
        }
        public  function getMaxTotalVolumetricWeight()
        {
            return $this->valArray["max_total_volumetric_weight"];
        }
        public  function setFuelSurcharge($val)
        {
            $this->valArray["fuel_surcharge"] = $val;
            $this->modifyArray["fuel_surcharge"] = $val;
        }
        public  function getFuelSurcharge()
        {
            return $this->valArray["fuel_surcharge"];
        }
        public  function setProductServiceCode($val)
        {
            $this->valArray["product_service_code"] = $val;
            $this->modifyArray["product_service_code"] = $val;
        }
        public  function getProductServiceCode()
        {
            return $this->valArray["product_service_code"];
        }
        public  function setFridayOnlyFlag($val)
        {
            $this->valArray["friday_only_flag"] = $val;
            $this->modifyArray["friday_only_flag"] = $val;
        }
        public  function getFridayOnlyFlag()
        {
            return $this->valArray["friday_only_flag"];
        }
        public  function setClassCode($val)
        {
            $this->valArray["class_code"] = $val;
            $this->modifyArray["class_code"] = $val;
        }
        public  function getClassCode()
        {
            return $this->valArray["class_code"];
        }
        public  function setSupplier($val)
        {
            $this->valArray["supplier"] = $val;
            $this->modifyArray["supplier"] = $val;
        }
        public  function getSupplier()
        {
            return $this->valArray["supplier"];
        }
        public  function setServiceType($val)
        {
            $this->valArray["service_type"] = $val;
            $this->modifyArray["service_type"] = $val;
        }
        public  function getServiceType()
        {
            return $this->valArray["service_type"];
        }
        public  function setDescription($val)
        {
            $this->valArray["description"] = $val;
            $this->modifyArray["description"] = $val;
        }
        public  function getDescription()
        {
            return $this->valArray["description"];
        }
        public  function setCutOffTime($val)
        {
            $this->valArray["cut_off_time"] = $val;
            $this->modifyArray["cut_off_time"] = $val;
        }
        public  function getCutOffTime()
        {
            return $this->valArray["cut_off_time"];
        }
        public  function setLogoServices($val)
        {
            $this->valArray["logo"] = $val;
            $this->modifyArray["logo"] = $val;
        }
        public  function getLogoServices()
        {
            return $this->valArray["logo"];
        }
        public  function setAdditionalDetails($val)
        {
            $this->valArray["additional_details"] = $val;
            $this->modifyArray["additional_details"] = $val;
        }
        public  function getAdditionalDetails()
        {
            return $this->valArray["additional_details"];
        }
        public  function setUploadedCurrencyValue($val)
        {
            $this->valArray["uploaded_currency_value"] = $val;
            $this->modifyArray["uploaded_currency_value"] = $val;
        }
        public  function getUploadedCurrencyValue()
        {
            return $this->valArray["uploaded_currency_value"];
        }
        public  function setRegistrationFee($val)
        {
            $this->valArray["registration_fee"] = $val;
            $this->modifyArray["registration_fee"] = $val;
        }
        public  function getRegistrationFee()
        {
            return $this->valArray["registration_fee"];
        }
        public  function setAditionalCharge($val)
        {
            $this->valArray["aditional_charge"] = $val;
            $this->modifyArray["aditional_charge"] = $val;
        }
        public  function getAditionalCharge()
        {
            return $this->valArray["aditional_charge"];
        }
        public  function setGroupCharges($val)
        {
            $this->valArray["group_charges"] = $val;
            $this->modifyArray["group_charges"] = $val;
        }
        public  function getGroupCharges()
        {
            return $this->valArray["group_charges"];
        }
        public  function setAgentid($val)
        {
            $this->valArray["agentid"] = $val;
            $this->modifyArray["agentid"] = $val;
        }
        public  function getAgentid()
        {
            return $this->valArray["agentid"];
        }
        public  function setAddedOn($val)
        {
            $this->valArray["added_on"] = $val;
            $this->modifyArray["added_on"] = $val;
        }
        public  function getAddedOn()
        {
            return $this->valArray["added_on"];
        }
        public  function setAddedBy($val)
        {
            $this->valArray["added_by"] = $val;
            $this->modifyArray["added_by"] = $val;
        }
        public  function getAddedBy()
        {
            return $this->valArray["added_by"];
        }
        public  function setChangedOn($val)
        {
            $this->valArray["changed_on"] = $val;
            $this->modifyArray["changed_on"] = $val;
        }
        public  function getChangedOn()
        {
            return $this->valArray["changed_on"];
        }
        public  function setChangedBy($val)
        {
            $this->valArray["changed_by"] = $val;
            $this->modifyArray["changed_by"] = $val;
        }
        public  function getChangedBy()
        {
            return $this->valArray["changed_by"];
        }
        public  function setCarrierId($val)
        {
            $this->valArray["carrier_id"] = $val;
            $this->modifyArray["carrier_id"] = $val;
        }
        public  function getCarrierId()
        {
            return $this->valArray["carrier_id"];
        }
    
        public static function getServicesList($service_id="", $carrier="",$is_customized="") 
        { 
            $where = "active = 1";
            $join = "";
            if($carrier > 0)
            {
                $join = " left join carrier c on c.id = s.carrier_id" ;
                $where .= " AND s.carrier_id = '".$carrier."'";
            }
            if($is_customized != "")
            {
                $where .= " AND s.is_customized = '".$is_customized."'";
            }
            $sql = "SELECT s.id,c.logo, name FROM services s ".$join." where " . $where . " order by name asc ";
          
            $rs = self::runQuery($sql);
            $option = "";
            $option .= "<option value=''>Select Service</option>";
            while ($valArray = mysqli_fetch_assoc($rs)) {
            if ($valArray["name"] == "")
                continue;
                $selected = ($service_id == $valArray["id"]) ? " selected" : "";
                $logo = $valArray["logo"];
                $display_name = $valArray["name"];
                $option .= '<option ' . $selected . ' value="' . $valArray["id"]. '" data-content="<img src=\'../images/carrierlogo/thumbnail/owe_16_' . $logo . '\' /> ' . ucfirst($display_name) . ' ">' . $display_name . '</option>';
            }
            return $option;
        }
        
        public static function nameService($service)
	{

		$sql 		=	"SELECT id, name  FROM services WHERE id = '".DbAccess3::escape($service)."'";
		$result 	=	DbAccess3::getListFromSql(__CLASS__, $sql);
		$name	=	'';
		if(count($result)>0)
		{
					$name	=	$result[0]->getName();
		}
		return 	$name;
	}
        
        public static function getServiceDropDownList($service_id="",$userId='') {
            
            (int)$service_id    =   $service_id;
            (int)$userId        =   $userId;  
            if($userId>0)
                $join = " INNER JOIN user_services_routing usr ON usr.service_id = s.id where usr.user_id = '".$userId."' ";
            else
                $join = "";
             $sql = "SELECT s.id, name FROM services s ".$join."  group by s.id order by name asc ";
            $rs = self::runQuery($sql);
            $option = "";
            $option .= "<option value=''>Select Service</option>";
            while ($valArray = mysqli_fetch_assoc($rs)) {
            if ($valArray["name"] == "")
                continue;
                $selected = ($service_id == $valArray["id"]) ? " selected" : "";
                $option .= "<option ". $selected ." value='".$valArray["id"]."'>". $valArray["name"] ."</option>" ;
            }
            return $option;
        }
         public static function updateServices($setvalues,$where) {
              $sql = "UPDATE services SET " . $setvalues . " where " . $where;
              DbAccess3::runQuery($sql);
         }
        /**
        * expunge. Real delete
        * @return void
        */
        public static function DeleteByCountryId($countryIds,$serviceId)
        {
            if(is_array($countryIds)){
                $commaCountry = "";
                foreach ($countryIds as $countryId) {
                    $commaCountry .= "'".$countryId."',";
                }
                $commaCountry = rtrim($commaCountry,',');
               $sql = "DELETE FROM service_country_ttime WHERE id_country IN (".$commaCountry.") AND id_service = '".DbAccess3::escape($serviceId)."'";
                DbAccess3::runQuery($sql);
            }
        }
        public static function checkServiceExists($serviceId , $countryId) {
           $sql = "SELECT COUNT(id) AS id FROM `service_country_ttime` WHERE  id_service = " . DbAccess3::escape($serviceId)." AND id_country = ". DbAccess3::escape($countryId);
            $result = DbAccess3::getListFromSql(__CLASS__, $sql);
                return $result[0]->getId();
        }
        public static function getServiceMapping($userId) {
            $sql = "SELECT ser.id,ser.name,c.logo as carrier_logo FROM `services` ser JOIN `user_services_routing` us ON ser.id = us.`service_id` JOIN `carrier` c ON c.id = ser.`carrier_id` WHERE us.`user_account_id` =".DbAccess3::escape($userId)." GROUP BY us.service_id";
            $result = DbAccess3::getListFromSql(__CLASS__, $sql);
            return $result;
        }
    public static function getServiceMappingWithCarrier($userAccountId, $debug = false)
    {
        $sql = "SELECT
                  ser.`code`,
                  c.`logo` AS description,
                  c.`carrier_display_name` AS carrier_id,
                  ser.name
                FROM
                  `services` ser
                  JOIN `user_services_routing` us
                    ON ser.id = us.`service_id` AND us.status = 1 AND ser.active = 1
                  JOIN `carrier` c
                    ON c.`id` = ser.`carrier_id` AND c.status = 1
                WHERE us.`user_account_id` = '".DbAccess3::escape($userAccountId)."'
                GROUP BY us.service_id
                ORDER BY c.id ;";

        if($debug)
            echo $sql;
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getImageExists($image) {
        if(!empty($image) && file_exists($image)){
            return $image;
        }
        else{
            return "../images/No-image-found.jpg";
        }
    }
    public static function DeleteByColumnName($columnName,$columnValue) {
        if(!empty($columnName) && !empty($columnValue)){
            $sql = "DELETE FROM services WHERE ".$columnName." = '".DbAccess3::escape($columnValue)."'";
            DbAccess3::runQuery($sql);
        }
        if(is_array($countryIds)){
            $commaCountry = "";
            foreach ($countryIds as $countryId) {
                $commaCountry .= "'".$countryId."',";
            }
            $commaCountry = rtrim($commaCountry,',');
           $sql = "DELETE FROM service_country_ttime WHERE id_country IN (".$commaCountry.") AND id_service = '".DbAccess3::escape($serviceId)."'";
            DbAccess3::runQuery($sql);
        }
    }
    public  function getServiceByCode($serviceCode) {
        $serviceCode = trim($serviceCode);
        $sql = "SELECT * FROM `services` WHERE `code` ='".DbAccess3::escape($serviceCode)."' AND `active` = 1 AND deletedq = 0 Limit 1";
        $list = DbAccess3::getListFromSql(__CLASS__, $sql);
        if (sizeof($list) > 0) return $list[0];
        return null;
    }
}  // class
