<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
 
class CustomizedServicesRoutingFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $groupBy = '';
        private $rowsPerPage = 0;
        private $pageOffset = 0;
        private $join = "";

	/**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
	public function getList($debug=false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;
                $groupby = "";
                if($this->groupBy != "") $groupby =  $this->groupBy;
                
                $checkJoin = "";
                if ($this->join != "")
                    $checkJoin = $this->join;
		$sql = "SELECT csr.*
				FROM customized_services_routing csr
                                $checkJoin
				$where
				$sort
                                $groupby
				";

		t($sql, __METHOD__);

                if($debug){
                    echo $sql;
                    die;
                }
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
        public function getPagingCount() {
        // has filter been configured?
        $where = "WHERE ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        $sort = "";
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        $sql = "SELECT count(id) as total FROM customized_services_routing csr $checkJoin $where " . $groupBy . " Order by id asc";
        t($sql, __METHOD__);
        return CustomizedServicesRouting::getTotalNumberOfCustomizedServiceFromSql($sql);
    }
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
     public function getPagingList() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $groupBy = $this->groupBy;
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        $sql = "SELECT csr.* FROM  customized_services_routing csr $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
//        echo $sql;die;
        t($sql, __METHOD__);
        return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
    }
	
		/**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
	public function getColumnList($fields, $debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;
                
                $groupby = "";
                if($this->groupBy != "") $groupby =  $this->groupBy;

                $checkJoin = "";
                if ($this->join != "")
                    $checkJoin = $this->join;

		$sql = "SELECT csr.id, ".$fields."
				FROM customized_services_routing csr
                                $checkJoin
				$where
				$sort
                                $groupby
				";
		t($sql, __METHOD__);
                if($debug){
                    echo $sql;
                    exit;
                }

		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}

        
        public function getProductCustomerServiceList($selectedcolumn)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;


		$sql = "SELECT 
                        ".$selectedcolumn."
                    FROM
                        customized_services_routing csr 
                        inner Join services s
                        on csr.service_id = s.id
                        $where";
//                echo $sql;die;
		t($sql, __METHOD__);


		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
        
        
        public function getProductCustomerSerList($selectedcolumn,$userId,$service_id='',$countryId='',$Weight='',$productId='',$debug=false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;
                
                $weightWhereClause  =   '';
                if($Weight!= '')
                    $weightWhereClause      =   "AND csr.from_weight <= '".$Weight."' AND csr.to_weight >= '".$Weight."'";
                
                $productWhereClause  =   '';
                if($productId!= '' )
                    $productWhereClause      =   "AND p.id = '".$productId."'";
                
                
               $sql = "SELECT 
                        ".$selectedcolumn."
                FROM
                    ( 
                        user_services_routing rum 
                        inner join services p on p.id = rum.service_id and user_account_id IN (select user_account_id from user u where u.id = '".$userId."') and p.active = 1 ".$productWhereClause."
                    )
                    inner join customized_services_routing csr on p.id = csr.customize_service_id ".$weightWhereClause."
                    inner join services sr on csr.service_id = sr.id AND csr.country_id = '".(trim($countryId))."'
                    inner join    
                        service_country_ttime sctt on sctt.id_service = sr.id
		group by p.id
		order by p.name asc ";
           if($debug)
           {
               echo $sql;
           }
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
        
        
	
	
	/**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
	public function getServiceColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;


		$sql = "SELECT ".$fields."
				FROM customized_services_routing csr INNER JOIN services ser ON csr.service_name = ser.code
				$where
				$sort
				";

		t($sql, __METHOD__);


		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
        

	
		/**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
	public function deleteList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		
	  	$sql = "DELETE FROM customized_services_routing $where";
	
		$sql = str_replace('csr.','',$sql);

		t($sql, __METHOD__);


		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
	
	/****
	 * Get distinct list of flight numbers, using current filter conditions.
	 */
	public function getCountrytList($country_name, $debug = false)
	{
		// has filter been configured?
		$where = "WHERE country_id > 0 ";
                if($country_name != '')
                    $where .= "AND c.name like '".$country_name."%'";
                    
                if ($this->filter != "")
                {
                      $where .= $this->filter;
                }
		//
		$sql = "SELECT c.id 'country_id', c.name as carrier_country FROM customized_services_routing csr 
                        LEFT JOIN country c
                        on c.id = csr.country_id"
                        . " $where ORDER BY c.name";

		//if ($this->limit > 0) $sql .= " Limit " . $this->limit;
		if($debug)
			echo $sql;
		t($sql, __METHOD__);
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql, "country");
	}
        
        
        /****
	 * Get distinct list of flight numbers, using current filter conditions.
	 */
	public function getWeigthList( $debug = false)
	{
		// has filter been configured?
		$where = "WHERE country_id > 0 ";
                if ($this->filter != "")
                {
                      $where .= $this->filter;
                }
		//
		$sql = "SELECT from_weight , to_weight FROM customized_services_routing csr "
                        . " $where GROUP BY from_weight order by from_weight asc";
		//if ($this->limit > 0) $sql .= " Limit " . $this->limit;
		if($debug)
			echo $sql;
		t($sql, __METHOD__);
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql, "country");
	}
	
	/****
	 * Get distinct list of flight numbers, using current filter conditions.
	 */
	public function getCountrytAllList()
	{
		// has filter been configured?
		$where = "WHERE country <> '' ";
		$where .= $this->filter;
		//
	 	$sql = "SELECT DISTINCT(csr.country) FROM (customized_services_routing csr JOIN services ser on csr.service_name = ser.code ) INNER JOIN country cty on csr.country = cty.name $where ORDER BY country";

		//if ($this->limit > 0) $sql .= " Limit " . $this->limit;

		t($sql, __METHOD__);
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql, "country");
	}

	public function addNotSpecialServiceTypeFilter()
	{
		
		$this->filter .= " AND ";
		$this->filter .= " csr.service_type <> 'S'";
	}
	
	public function addFieldFilter($fieldName, $fieldValue,$tableprefix	= 'csr.' )
	{
		if (trim($this->filter) != '')
                    $this->filter .= " AND ".$tableprefix.$fieldName." = '".DbAccess3::escape($fieldValue)."'";
                else
                    $this->filter .= $tableprefix.$fieldName." = '".DbAccess3::escape($fieldValue)."'";
	}
	
	
	public function addOrderBy($orderBy)
	{
		$this->order_by = $orderBy;
	}

	
		/****
	 * Get distinct list of flight numbers, using current filter conditions.
	 */
	public function getUserAllowServiceList()
	{
		// has filter been configured?
		$where = "WHERE country <> '' ";
		$where .= $this->filter;
		//
//		$sql = "SELECT DISTINCT(ser.name) as service_name, type FROM customized_services_routing csr INNER JOIN services as ser ON ser.code = csr.service_name $where group by ser.code order by ser.name";
		$sql = "SELECT DISTINCT(ser.name) as service_name, ser.code AS carrier FROM customized_services_routing csr INNER JOIN services as ser ON ser.code = csr.service_name $where group by ser.code order by ser.name";

		//if ($this->limit > 0) $sql .= " Limit " . $this->limit;

		t($sql, __METHOD__);
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
	

	/****
	 * Get distinct list of routing_name
	*/
	public function getAvailableRoutingNameList()
	{
		
		$sql = "SELECT DISTINCT(routing_name) FROM customized_services_routing csr WHERE routing_name <> '' AND service_type in ('OR','PR') group by routing_name ";
		t($sql, __METHOD__);
		return CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
	}
	
	/**
	 * Get count of consignment items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}

	/**
	* Limit list to consigments with given Postcode value
	* @param $postcode_value
	*/
	public function addUserIdFilter($user_id)
	{
		$this->filter .= " AND ";
		$this->filter .= " csr.user_id = '" .DbAccess3::escape( $user_id) . "'";
	}
	
	
	
	public function addFilter($name)
	{
		$this->filter .= " AND ";
		$this->filter .= " " . $name . "";
	}
	
	
	
	
	/**
	* Limit list to consigments with given Postcode value
	* @param $postcode_value
	*/



    public function addCustomizeServiceIdFilter($customizeServiceIdValue)
    {
        $this->filter .= " AND ";
        $this->filter .= " csr.customize_service_id = '" . DbAccess3::escape($customizeServiceIdValue) . "'";
    }

	public function addFromWeightFilter($fromweight_value)
	{
		$this->filter .= " AND ";
		$this->filter .= " csr.from_weight = '" . DbAccess3::escape($fromweight_value) . "'";
	}

	/**
	* Limit list to consigments with given Postcode value
	* @param $postcode_value
	*/
	public function addCountryFilter($country)
	{
		$this->filter .= " AND ";
		$this->filter .= " csr.country_id = '" . DbAccess3::escape($country ). "'";
	}
	
	
	
	public function addSerServiceNameFilter($service_name)
	{
		$this->filter .= " AND ";
		$this->filter .= " ser.name = '" .DbAccess3::escape( $service_name) . "'";
	}
	
	
	
	public function addStatusFilter($status)
	{
		$this->filter .= " AND ";
		$this->filter .= " csr.status = '" . DbAccess3::escape($status) . "'";
	}
	

		public function addSpecialServicesFilter()
	{
		$this->filter .= " AND ";
		$this->filter .= " csr.service_type = 'S'";
	}
	
	/***
	 * Limit number of rows returned
	 */
	public function setLimit ($lim)
	{
		$this->limit = $lim;
	}


	/*
	*
	*/
	public function setGroup($groupBy){
		$this->groupBy  = " group by csr.".$groupBy;
	}
        public function addFieldLikeFilter($colm, $value) {
            $colm = trim($colm);
            $value = trim($value);
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
        }
        public function addJoin($table,$whare) {
            if(!empty($table) && !empty($whare)){
                $this->join.= " JOIN $table  ON ".$whare;
            }
        }
        public function addWeightRangeFilter($fromweight_value, $toweight_value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ( csr.from_weight >= " . DbAccess3::escape($fromweight_value ). " AND csr.to_weight <= " . DbAccess3::escape($toweight_value) . " )";
	}
}
