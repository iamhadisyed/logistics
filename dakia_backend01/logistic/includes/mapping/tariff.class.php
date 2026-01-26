<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier tariffs
//fv
////////////////////////////////////////////////////
/**
 * Tariff - Tariff class
 * @package Courier
 */
class Tariff extends DbAccess3
{
	//
	protected $courier_service_id;
	protected $collection_rateband_id;
	protected $destination_rateband_id;
	protected $collection_postcode_group_id;
	protected $destination_postcode_group_id;
	protected $weight_from;
	protected $weight_to;
	protected $tariff;
	protected $add_unit_cost;
	protected $unit_size;
	protected $extra_tariff;
	protected $extra_add_unit_cost;

	protected $orderq;
	protected $active;
	protected $deletedq;
	protected $added_on;
	protected $added_by;
	protected $changed_on;
	protected $changed_by;

	protected $total_weight;
    protected $customer_id;
	protected $formula;
	protected $accountGot=false;
	//
	private $service = null;
	
	
	public static function  formullaDetails(){
	return array('Q'=>'QUANTITY',
					'ITMCHR'=>'UNIT PRICE (Chargeable Unit Price)',
					'REG'=>'REGISTRATION CHARGES',
					'CHRG'=>'BASIC CHARGE (Chargeable Tariff Price)',
					'FRMW'=>'FROM WEIGHT',
					'<br>ceil'=>'Round a number to next possible value i.e 2.12 to 3 , 2.56 to 3 ',

					);
	
	}
	
	
	public static function  formulla(){
	return array(
						array('formulla'=>'', 'name'=>'- Select -'),
						array('formulla'=>'Q * ITMCHR', 'name'=>'Q * ITMCHR (Per Piece Price )'),
						array('formulla'=>'Q * CHRG', 'name'=>'Q * CHRG (Per Piece Price )'),
						array('formulla'=>'Q * ( ITMCHR + REG ) + W * CHRG', 'name'=>'Q * ( ITMCHR + REG ) + W * CHRG (REGISTER POST)'),
						array('formulla'=>'( Q * ITMCHR ) + ( W * CHRG ) ', 'name'=>'( Q * ITMCHR ) + ( W * CHRG ) (UNTRACK REGISTER POST)'),
						array('formulla'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) +CHRG', 'name'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) +CHRG (DHL ECO)'),
						array('formulla'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG', 'name'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG (DHL EXP)'),
						array('formulla'=>'(  ITMCHR * ceil (( W - FRMW ) / 0.45 )) +CHRG', 'name'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) +CHRG (DHL ECO SHIPMENT)'),
						array('formulla'=>'(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG', 'name'=>'Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG (DHL EXP SHIPMENT))'),
						array('formulla'=>'Q * (  ITMCHR * ceil ( W - FRMW )) + CHRG', 'name'=>'Q * (  ITMCHR * ceil ( W - FRMW ) ) + CHRG (STANDARD)'),
					);
	
	}
	

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
	public function __construct($mixedCreator = null)
    {
        $fieldList  =	array(  
                    'id'				=> 'number',
                    'courier_service_id'       		=> 'number',
                    'collection_rateband_id'  		=> 'number',
                    'destination_rateband_id' 		=> 'number',
                    'collection_postcode_group_id' 	=> 'number',
                    'destination_postcode_group_id'     => 'number',
                    'weight_from'             		=> 'string',
                    'weight_to'	              		=> 'string',
                    'tariff'		          	=> 'string',
                    'add_unit_cost'           		=> 'string',
                    'unit_size'	              		=> 'string',
                    'extra_tariff'            		=> 'string',
                    'extra_add_unit_cost'     		=> 'string',

                    'orderq'		         	=>	'string',
                    'active'		          	=>	'string',
                    'deletedq'	              		=>	'string',
                    'added_on'	              		=>	'string',
                    'added_by'	              		=>	'string',
                    'changed_on'	          	=>	'string',
                    'changed_by'	          	=>	'string',
                    'customer_id'             		=>	'string',
                    'formula'                 		=>	'string',
                    'max_width'                 	=>	'undefined',
                    'max_height'                 	=>	'undefined',
                    'max_length'                 	=>	'undefined'
            );

		parent::__construct("tariffs", 'id', $fieldList, $mixedCreator);
    }
	
	
	
	
/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getTariffListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	
	
	////////////////////////////////////////////////////
	// Access and update methods
	////////////////////////////////////////////////////

	public function getService()
	{
		if ($this->service == null)
		{
			$this->service = new Courierservice($this->getServiceId());
		}
		return $this->service;
	}


	/**
	* getAnyTariff. Returns the object array of Tariffs.
	* @param string $where
	* @param string $activeOnly
	* @param string $orderBy
	* @return string
	*/
	public function getAnyTariff($where = "", $activeOnly = true, $orderBy = "orderq",$from)
	{
		$ret    = array();
		$whereSql = "WHERE deletedq <> 'Y' ";

		if ($where)	$whereSql .= " and $where";
		if ($activeOnly) $whereSql .= " and active=1";
		$account= Sessionmanager::getCustomerId();
        if (isset($account)&&$from!="admin")
        $whereSql .= " and (customer_id= $account OR customer_id='ALL')";
        else if($from!="admin")
        $whereSql .= " and customer_id= 'ALL'";

        $sql = "SELECT *
				FROM	{$this->tablename}
				$whereSql
				ORDER BY	$orderBy";

		$result = DbAccess3::runQuery($sql);
        $row_value="";
        $firstTime=true;
        $firstRow;
        $count=0;

        while ($row1 = mysqli_fetch_assoc($result))
        {
            $count++;
        }

     $result = DbAccess3::runQuery($sql);

       while ($row = mysqli_fetch_assoc($result))
		{
            if($from!="admin")
            {
            $test=$row;
          if($count>1)
            {
                $tariff = new Tariff();
			    if($firstTime==true)
                {
            $firstRecord= $row;
            $firstRow=$row;
            $row_value=$firstRecord;
            $firstTime=false;

                 }

                else
                {
            $test=$row["tariff"];
            if($firstRecord["tariff"]<$test)
            {
            $tariff = new Tariff();
            $tariff->populate($firstRecord);
            $row_value=$firstRecord;
            $ret[] = $tariff;
                }

            else
            {
                $tariff = new Tariff();
                $tariff->populate($row);
                $ret[]=$tariff;
            }
                 }


            }

        if($count==1)
        {
         $tariff = new Tariff();
            $tariff->populate($row);
            $firstRecord=$row;
            $ret[] = $tariff;
        }
          else
            {
           $tariff = new Tariff();
            $tariff->populate($row);
            $row_value=$firstRecord;
            $ret[] = $tariff;
            }
		}
        else
        {
            $tariff = new Tariff();
            $tariff->populate($row);
            $ret[] = $tariff;
        }
    }
    $ret="";
    if(isset($account))
     $whereSql .= " and (customer_id= '".DbAccess3::escape($account)."' OR customer_id='".DbAccess3::escape('ALL')."')";
     else
     $whereSql .= " and (customer_id='".DbAccess3::escape('ALL')."')";
      $sql1= "SELECT *
                FROM    {$this->tablename}
                $whereSql
                ORDER BY    $orderBy";
     $result = DbAccess3::runQuery($sql1);
        $tariffFound=0;
        while ($row = mysqli_fetch_assoc($result))
        {
            $tariff = new Tariff();

            if($row["customer_id"]==Sessionmanager::getCustomerId())
            {
                $tariffFound=1;
                if($tariffFound==1)
                {
                $tariff->populate($row);
                //$tariff->setChargeableWeight($theParcelGroup->getTotalWeight());
                $ret[] = $tariff;
                }

            }
        }
        $sql2 = "SELECT *
                FROM    {$this->tablename}
                $whereSql
                ORDER BY    $orderBy";

         $result = DbAccess3::runQuery($sql);
         if($tariffFound==0)
         {
         while ($row = mysqli_fetch_assoc($result))
         {

            $tariff = new Tariff();
            $tariff->populate($row);
            //$tariff->setChargeableWeight($theParcelGroup->getTotalWeight());
            $ret[] = $tariff;

         }
         }


		return @$ret;
	}


	////////////////////////////////////////////////////
	// Table retreival
	////////////////////////////////////////////////////

	/**
	* getQuotes. Get quotes available for countries selected and weights of parcels
	* @return array
	*/
	public function getTariffsForParcelGroup($theParcelGroup, $dest_any_matching_postcode = true, $active_services_only = true)
	{
	$ret = array();
		// Documents or packages
		$notPackageType = ($theParcelGroup->isDocumentsFlag()) ? CourierService::PACKAGE_TYPE_PARCEL : CourierService::PACAKGE_TYPE_DOCUMENT;
        $customer_id1=SessionManager::getCustomerId();

		// Determine the destination postcode groups to check for
		$dest_subzone = "(pd.postcode='" . $theParcelGroup->getCollectionPostcode() . "' or pd.postcode is null)";
		if (!$dest_any_matching_postcode && ($theParcelGroup->getDestinationSubzone() != "")) // exact match required
		{
			$dest_subzone = "pd.postcode='" . $theParcelGroup->getDestinationSubzone() . "'";
		}
		//
		$active_filter = ($active_services_only ? "AND serv.active = 1" : "");

		//
		$weight = $theParcelGroup->getTotalWeight();
        $all="ALL";
        Sessionmanager::setDelCountryCode($theParcelGroup->getCollectionCountryId());

		//
	if((isset($customer_id1))&&($customer_id1!=0))
      {
            $sql = "SELECT t.* FROM tariffs t
            LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
            LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
            LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
            LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
            LEFT OUTER JOIN courier_services serv on t.courier_service_id=serv.id
            WHERE weight_from <" . DbAccess3::escape($weight) . "
                AND weight_to >= " . DbAccess3::escape($weight) . "
                AND col.country_id = " . $theParcelGroup->getCollectionCountryId() . "
                AND del.country_id = " . $theParcelGroup->getDestinationCountryId() . "
                AND (pc.postcode='" . $theParcelGroup->getCollectionPostcode() . "' or pc.postcode is null)
                AND $dest_subzone
                $active_filter
                AND serv.parcel_document_flag <> $notPackageType AND
                (customer_id= $customer_id1 OR customer_id='ALL')
            ORDER BY t.tariff";
       }

       else
        {
        $sql = "SELECT t.* FROM tariffs t
			LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
			LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
			LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
			LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
			LEFT OUTER JOIN courier_services serv on t.courier_service_id=serv.id
			WHERE weight_from <" . DbAccess3::escape($weight) . "
				AND weight_to >= " . DbAccess3::escape($weight) . "
				AND col.country_id = " . $theParcelGroup->getCollectionCountryId() . "
				AND del.country_id = " . $theParcelGroup->getDestinationCountryId() . "
				AND (pc.postcode='" . $theParcelGroup->getCollectionPostcode() . "' or pc.postcode is null)
				AND $dest_subzone
                AND customer_id='ALL'
				$active_filter
				AND serv.parcel_document_flag <> $notPackageType
			ORDER BY t.tariff
		";
        }
		//echo "<p>$sql</p>";

		$result = DbAccess3::runQuery($sql);
        $tariffFound=0;
		while ($row = mysqli_fetch_assoc($result))
		{
			$tariff = new Tariff();

            if($row["customer_id"]==Sessionmanager::getCustomerId())
            {
                $tariffFound=1;
                if($tariffFound==1)
                {
                $tariff->populate($row);
                $tariff->setChargeableWeight($theParcelGroup->getTotalWeight());
                $ret[] = $tariff;
                }

            }
		}
        $sql2=        $sql = "SELECT t.* FROM tariffs t
            LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
            LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
            LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
            LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
            LEFT OUTER JOIN courier_services serv on t.courier_service_id=serv.id
            WHERE weight_from <" . DbAccess3::escape($weight) . "
                AND weight_to >= " . DbAccess3::escape($weight) . "
                AND col.country_id = " . $theParcelGroup->getCollectionCountryId() . "
                AND del.country_id = " . $theParcelGroup->getDestinationCountryId() . "
                AND (pc.postcode='" . $theParcelGroup->getCollectionPostcode() . "' or pc.postcode is null)
                AND $dest_subzone
                AND customer_id='ALL'
                $active_filter
                AND serv.parcel_document_flag <> $notPackageType
            ORDER BY t.tariff
        ";

         $result = DbAccess3::runQuery($sql);
         if($tariffFound==0)
         {
         while ($row = mysqli_fetch_assoc($result))
         {

            $tariff = new Tariff();
            $tariff->populate($row);
            $tariff->setChargeableWeight($theParcelGroup->getTotalWeight());
            $ret[] = $tariff;

         }
         }
		return @$ret;
	}


	/**
	 * Factory method for creating tariff objects.
	 * The tariff object created is based on the
	 * details of the tariff passed, but for the new
	 * weight passed rather.
	 *
	 * @param Tariff $tariff
	 * @param double $weight
	 * @return tariff
	 */
	public static function createFromTariffAndWeight(Tariff $tariff, $weight)
	{
		// Selection criteria for the tariff
		$where = "weight_from <" . DbAccess3::escape($weight) . "
				AND weight_to >= " . DbAccess3::escape($weight) . "
				AND collection_rateband_id = " . $tariff->getCollectionRateBandId() . "
				AND destination_rateband_id = " . $tariff->getDestinationRateBandId() . "
				AND courier_service_id = " . $tariff->getServiceId();

		if ($tariff->getCollectionPostcodeGroupId() > 0)
		{
			$where .= " AND collection_postcode_group_id=" . $tariff->getCollectionPostcodeGroupId();
		}
		else
		{
			$where .= " AND (collection_postcode_group_id=0 OR collection_postcode_group_id is null)";
		}
		if ($tariff->getDestinationPostcodeGroupId() > 0)
		{
			$where .= " AND destination_postcode_group_id=" . $tariff->getDestinationPostcodeGroupId();
		}
		else
		{
			$where .= " AND (destination_postcode_group_id=0 OR destination_postcode_group_id is null)";
		}

		// Try and find new tariff
		$filter = new Tariff();
		$accountGot=true;
        $tariff_list = $filter->getAnyTariff($where, $activeOnly = true, $orderBy = "orderq",$from="notadmin");
		if (sizeof($tariff_list) > 0)
		{
			$tariff_list[0]->setChargeableWeight($weight);
			if (Trace::isActive())
			{
				$msg = "Found tariff " . $tariff_list[0]->getId();
				$msg .= " Base Price: " . $tariff_list[0]->getBasePrice();
				$msg .= " Final Price: " . $tariff_list[0]->getPrice();
				t($msg, __METHOD__);
			}

			return $tariff_list[0];

		}
		return null;
	}
//------------------------------------------------------
	/**
	* Get price for a quote
	* @return float
	*/
	public function getPrice()
	{
		$price = $this->getBasePrice();

		// Add fuel surcharge
		$price = ($price * (100 + $this->getService()->getFuelSurcharge())) / 100;

		return $price;
	}

	
	/**
	* Base price, is price before fuel surcharge is added.
	*
	*/
	public function getBasePrice()
	{
		// Calculate the additional price
		$price = $this->getExtraUnits() * $this->getAddUnitCost();
		//t ("Extra units: " . $this->getExtraUnits() . " Unit cost: " . $this->getAddUnitCost(), __METHOD__);
		//
		$price += $this->getTariff();
		//
		return $price;
	}

	/***
	 * Extra weight units.
	 */
	private function getExtraUnits()
	{
		$extraUnits = 0;
		// Unit size has to be set.
		if ($this->getUnitSize() > 0)
		{
			// The difference between "from" and "to" weights has to be bigger than unit weight.
			if ($this->getWeightTo() - $this->getWeightFrom() > $this->getUnitSize())
			{
				$extraUnits = $this->getChargeableWeight() - $this->getWeightFrom();
				$extraUnits = round ($extraUnits / $this->getUnitSize(), 0);
			}
		}
		return $extraUnits;
	}

	////////////////////////////////////////////////////
	// Table updates
	////////////////////////////////////////////////////

	

	/**
	* delete. Updates row as deleted
	* @return void
	*/
	public function delete($id=0)
	{

		$sql = "UPDATE tariffs
				SET deletedq = 'Y'
				WHERE id = '$id'
				";

		$result = DbAccess3::runQuery($sql);
		return;

	}

	/**
	* expunge. Real delete
	* @return void
	*/
	public function expunge()
	{

	if(trim($this->getCustomerId()) != '')
	{
		$sql = "DELETE FROM tariffs
				WHERE courier_service_id  = '" . $this->getCourierServiceId() . "'
				AND collection_rateband_id= '" . $this->getCollectionRatebandId() . "'
				AND destination_rateband_id = '" . $this->getDestinationRatebandId() . "'
				AND collection_postcode_group_id = '" . $this->getCollectionPostcodeGroupId() . "'
				AND destination_postcode_group_id = '" . $this->getDestinationPostcodeGroupId() . "'
				AND customer_id = '" . $this->getCustomerId() . "'
				";

		$result = DbAccess3::runQuery($sql);
	}
		return;
	}


	public function getCollectionRateband()			{
														$RbnObj = new Rateband($this->getCollectionRatebandId());
														return $RbnObj->getName();
													}

	public function getDestinationRateband()		{
														$RbnObj = new Rateband($this->getDestinationRatebandId());
														return $RbnObj->getName();
													}

	public function setChargeableWeight($total_weight)
	{
		// total weight for this tariff has to be a factor of unit size
		$unitSize = $this->getUnitSize();
		if ($unitSize <= 0) $unitSize = 0.5;
		//
		// The charge weight has be to a multiple of current unit size
		$unitCount = ceil($total_weight /	$unitSize);
		if ($unitCount <= 0) $unitCount = 1;
		//
		$this->total_weight = $unitCount * $unitSize;
		//
		//t("Weight in: " . $total_weight . " set to " . $this->total_weight, __METHOD__);
		//
		return $this->total_weight;
	}

	////////////////////////////////////////////////////
	// Business layer methods
	////////////////////////////////////////////////////

	/**
	* Indictes if the tariff object has all the required information required to save to database.
	*
	*/
	function isValid ()
	{
		if ($this->getTariff()		<= 0)
			return false;
		return true;
	}
	
	

	public function getTariffChargesInvoice($colcountry, $delcountry, $handling, $user,$weight='', $weightType='SHIPMENT', $print = false,$tariffNameArray)
	{
		$ret = NULL;
		$where	=	'';
		if(count($tariffNameArray)>0){
			$where	=	" AND tum.tariff_name in ('".implode("', '",$tariffNameArray)."')";
                }
                
                $partTariffName      =   "SELECT tariff_name as user_name  FROM tariff_user_mapping where user_account = '$user'";
                        $partnerTariffName   =   User::getUserListFromSql($partTariffName);
                        if(count($partnerTariffName)>0)
                        {
                            foreach($partnerTariffName as $tariffDataNames)   
                            {
                              $tariffNameArray[]    =   $tariffDataNames->getUserName();
                            }
                        }
                        else
                        {
                           return ;
                        }
                        
                        
		$sql = "
				SELECT tariff, add_unit_cost, unit_size, extra_tariff, extra_add_unit_cost, formula, customer_id, serv.registration_fee, if(serv.fuel_surcharge>0,serv.fuel_surcharge,0) added_by, tum.user_account , weight_from, weight_to, courier_service_id, destination_rateband_id, collection_rateband_id 
                                FROM (select * from tariffs  where customer_id IN ('".implode("','",$tariffNameArray)."') ) t
			   LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
			   LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
			  
			   LEFT OUTER JOIN services serv on t.courier_service_id=serv.id
			   LEFT OUTER JOIN tariff_user_mapping tum on tum.tariff_name = t.customer_id   
			   LEFT OUTER JOIN user u on u.user_account = tum.user_account 
				WHERE col.country_id = '".DbAccess3::escape($colcountry)."'
				AND u.user_account = '". DbAccess3::escape($user)."'
				AND del.country_id = '".DbAccess3::escape($delcountry)."'
				AND serv.id = '".DbAccess3::escape($handling)."'
				AND (t.weight_from < '".DbAccess3::escape($weight)."' AND t.weight_to >='".DbAccess3::escape($weight)."' )
				AND serv.active = 1
				".$where."
			  ORDER BY t.tariff desc
				";
				
				
				if( $print )
					echo $sql;
	/*
	
	AND (pc.postcode='' or pc.postcode is null)
				AND (pd.postcode='' or pd.postcode is null)
	 LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
			   LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
	*/
		$result = DbAccess3::getListFromSql(__CLASS__, $sql);
// && $weightType != 'PARCEL'
		if(count($result)<=0)
		{
		 $sql = "
				SELECT  tariff, add_unit_cost, unit_size, extra_tariff, extra_add_unit_cost, formula, customer_id, if(serv.fuel_surcharge>0,serv.fuel_surcharge,0)  added_by, serv.registration_fee, tum.user_account , weight_from, weight_to ,  courier_service_id, destination_rateband_id, collection_rateband_id  
                                FROM (select * from tariffs  where customer_id IN ('".implode("','",$tariffNameArray)."') ) t
			   LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
			   LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
			  
			   LEFT OUTER JOIN services serv on t.courier_service_id=serv.id
			   LEFT OUTER JOIN tariff_user_mapping tum on tum.tariff_name = t.customer_id   
			   LEFT OUTER JOIN user u on u.user_account = tum.user_account 
			   
				WHERE 	col.country_id = '".DbAccess3::escape($colcountry)."'
						AND u.user_account = '". DbAccess3::escape($user)."'
						AND del.country_id = '".DbAccess3::escape($delcountry)."'
						AND serv.id = '".DbAccess3::escape($handling)."'
				AND serv.active = 1
				".$where."
			  ORDER BY t.weight_from desc limit 1
				";
			/*
			 LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
			   LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
			   
			   AND (pc.postcode='' or pc.postcode is null)
						AND (pd.postcode='' or pd.postcode is null)
			*/
				$result = DbAccess3::getListFromSql(__CLASS__, $sql);
		}
		
		
		return $result;

	
	}
	public function getTariffChargesInvoiceBySearchParam($colcountry, $delcountry, $user,$weight='', $weightType='SHIPMENT', $print = false, $shipmentType = '')
	{
            $serviceWhereClause =   '';
            if(trim($shipmentType) == 'PARCEL')
                $serviceWhereClause             =   " AND serv.shipment_type = 'PARCEL' ";
            else if(trim($shipmentType) == 'LETTER')
                $serviceWhereClause             =   " AND serv.shipment_type = 'LETTER' ";
            else if(trim($shipmentType) == 'PARCEL_TRACK')
                $serviceWhereClause             =   " AND serv.shipment_type = 'PARCEL' AND serv.is_untrack = 'NO' ";
            else if(trim($shipmentType) == 'LETTER_TRACK')            
                $serviceWhereClause             =   " AND serv.shipment_type = 'LETTER' AND serv.is_untrack = 'NO' ";
            
            $ret = NULL;
            $userFilter = new UserFilter();
            $userFilter->addUserAccountFilter($user);
            $userList = $userFilter->getColumnList("is_product");
            
            
		if(count($userList) > 0)
		{
                    $productNameArray   =   array();
                    $tariffNameArray   =   array();
                    $userObj = $userList[0];
					
					if($userObj->getIsProduct() == 'YES')
						$whereExtraForProduct	=	' and psr.routing_name  = t.customer_id ';
					else
						$whereExtraForProduct	=	'';
					
                    if($userObj->getIsProduct() == 'YES')
                    {
                        
                        $partProductName    =   "SELECT routing_name as user_name  FROM routing_user_mapping where user_account = '$user'";
                        $partnerProducts    =   User::getUserListFromSql($partProductName);
                        if(count($partnerProducts)>0)
                        {
                            foreach($partnerProducts as $productDataNames)   
                            {
                              $productNameArray[]    =   $productDataNames->getUserName();
                            }
                        }
                        else
                        {
                           return ;
                        }
                        
                        
                        $partTariffName      =   "SELECT tariff_name as user_name  FROM tariff_user_mapping where user_account = '$user'";
                        $partnerTariffName   =   User::getUserListFromSql($partTariffName);
                        if(count($partnerTariffName)>0)
                        {
                            foreach($partnerTariffName as $tariffDataNames)   
                            {
                              $tariffNameArray[]    =   $tariffDataNames->getUserName();
                            }
                        }
                        else
                        {
                           return ;
                        }
                      
            
            
				$sql = "SELECT distinct tariff, add_unit_cost, unit_size, extra_tariff, extra_add_unit_cost, formula,                customer_id, psr.routing_name 'changed_by', serv.registration_fee, 
	            if(serv.fuel_surcharge>0,serv.fuel_surcharge,0) added_by, tum.user_account , weight_from,                     weight_to, courier_service_id, destination_rateband_id, 
	                collection_rateband_id FROM 
                        (select * from tariffs  where customer_id IN ('".implode("','",$tariffNameArray)."') ) t
				   INNER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
				   INNER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
				   INNER JOIN services serv on t.courier_service_id=serv.id
				   INNER JOIN tariff_user_mapping tum on tum.tariff_name = t.customer_id
				   INNER JOIN user u on u.user_account = tum.user_account				   
				   INNER JOIN routing_user_mapping rum on u.user_account = rum.user_account                          
				   INNER JOIN 
                                   (select * from customizedservicesrouting where routing_name IN ('".implode("','",$productNameArray)."') ) psr on psr.service_name = serv.code

				   WHERE 
				   col.country_id = '$colcountry' and 
				   rum.user_account = '$user' 
				   AND del.country_id = '$delcountry' and tum.user_account = '$user' AND 
				   (t.weight_from < '$weight' AND t.weight_to >= '$weight' )
                                   ".$serviceWhereClause."    
				    AND psr.routing_name in ('".implode("','",$productNameArray)."')
				   and
				   t.courier_service_id in (
					select distinct s.id from customizedservicesrouting psr, services s where psr.service_name = s.code	
					and psr.from_weight < '$weight' and psr.to_weight >= '$weight' and
					country = (select name from country where id = '$delcountry') and routing_name != ''
					and routing_name in ('".implode("','",$productNameArray)."')
                            
                    )" .$whereExtraForProduct;
					
					//SELECT routing_name FROM routing_user_mapping where user_account = '$user'
			 }
			 else
			 {
				 $sql = "SELECT distinct tariff, add_unit_cost, unit_size, extra_tariff, extra_add_unit_cost,                         formula,  customer_id, serv.name 'changed_by', serv.registration_fee,
                         if(serv.fuel_surcharge>0,serv.fuel_surcharge,0) added_by, tum.user_account , weight_from,                         weight_to, courier_service_id, destination_rateband_id,
                         collection_rateband_id FROM tariffs t
                         INNER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
					     INNER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
					     INNER JOIN services serv on t.courier_service_id=serv.id
					     INNER JOIN tariff_user_mapping tum on tum.tariff_name = t.customer_id
					     INNER JOIN user u on u.user_account = tum.user_account					     
					     INNER JOIN 
						 (
						 	select account_number,from_weight, to_weight , service_name from customizedservicesrouting 
							 where service_type = 'S' and user_id = '".$userObj->getId()."'
						 )  psr ON psr.service_name = serv.code

						 WHERE
						 t.tariff > 0 AND
						 col.country_id = '$colcountry' AND
						 psr.account_number = '$user' AND
						 del.country_id = '$delcountry'
                                                     ".$serviceWhereClause."    
				                     AND 
                                    		 tum.user_account = '$user' AND
						 (t.weight_from < '$weight' AND t.weight_to >= '$weight' ) AND
						 (psr.from_weight < '$weight' AND psr.to_weight >= '$weight' )".$whereExtraForProduct;
			 }
				//mail("tahir@oneworldexpress.com", "sql", $sql. $serviceWhereClause );
							
				$result = DbAccess3::getListFromSql(__CLASS__, $sql);
				
				return $result;
		}
		




	}
	
	
		public function getCustomerTariffUrl($tariff_name)
	{
		$ret = NULL;

		$sql = "
				SELECT serv.id as serviceId, serv.carrier as serviceCarrier FROM tariffs t
			   LEFT OUTER JOIN countries_link_ratebands del ON t.destination_rateband_id=del.rateband_id
			   LEFT OUTER JOIN countries_link_ratebands col on t.collection_rateband_id=col.rateband_id
			   LEFT OUTER JOIN services serv on t.courier_service_id=serv.id
			   LEFT OUTER JOIN tariff_user_mapping tum on tum.tariff_name = t.customer_id   
			   LEFT OUTER JOIN user u on u.user_account = tum.user_account 
			   WHERE 
				 t.customer_id = '".DbAccess3::escape($tariff_name)."'
				AND serv.active = 1 limit 1
				";
				/*
				LEFT OUTER JOIN postcodes pc on t.collection_postcode_group_id=pc.postcode_group_id
			   LEFT OUTER JOIN postcodes pd on t.destination_postcode_group_id=pd.postcode_group_id
			   */
//	echo $sql;
		$result = DbAccess3::runQuery($sql);
		if(mysqli_num_rows($result) > 0)
		{
			while ($pId = @mysqli_fetch_array($result))
			{
				return 'tariffs.php?service_id='.$pId['serviceId']	.'&courier_id='.$pId['serviceCarrier'].'&tariff_name='.$tariff_name;
			}
		}
		else
			return 'couriers.php';
	
	}
	
	
}


?>
