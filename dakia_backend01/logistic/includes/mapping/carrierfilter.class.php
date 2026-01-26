<?php

// get settings
class CarrierFilter 
{
	private $filter = "";
	private $order_by= "";
        private $groupBy = "";
	
	
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getList($debug=false)
	{

		
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		if(trim($this->order_by) != '')
		$groupBy	=	'Order by '.$this->order_by;
                else
                    $groupBy	=	'Order by carrier asc';

		$sql = "SELECT * FROM carrier cl $where ".$groupBy."";
          if($debug )
              echo $sql;
		return Carrier::getCarrierListFromSql($sql);
	}


        /**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getCarrierList($fields)
	{

		
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		if(trim($this->order_by) != '')
		$groupBy	=	'Order by '.$this->order_by;
                else
                    $groupBy	=	'Order by carrier asc';

		$sql = "SELECT ".$fields." FROM carrier cl INNER JOIN country cn ON cn.id = cl.country_id  $where ".$groupBy."";
                
		return Carrier::getCarrierListFromSql($sql);
	}
        
        public function getCarrierSetupList($fields, $debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
                
                $groupby = "";
                if($this->groupBy != "") $groupby =  $this->groupBy;
                
		if(trim($this->order_by) != '')
		$orderBy	=	'Order by '.$this->order_by;
                else
                    $orderBy	=	'Order by is_agreed asc';

		 $sql = "SELECT ".$fields." FROM (user_services_routing usr INNER JOIN services s ON usr.service_id = s.id )
                        INNER JOIN carrier cl on cl.id = s.carrier_id 
                        LEFT JOIN country cn on cl.country_id = cn.id $where  $groupby ".$orderBy."";
                if ($debug)
                    echo $sql;
		return Carrier::getCarrierListFromSql($sql);
	}
/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function  getColumnList($fields,$limit=5000,$debug = false)
	{
            // has filter been configured?
            $where = "WHERE cl.id <> '' ";
            $where .= $this->filter;
            $groupBy	=	$this->order_by;

            $sql = "SELECT ".$fields." FROM carrier cl $where ".$groupBy."";
            if($debug){
                echo $sql;
                die;
            }
                
            return Carrier::getCarrierListFromSql($sql);
	}
        
        public function  getColumnListIn($fields,$debug = false)
	{
            // has filter been configured?
            $where = "";
            if (trim($this->filter) != ""){
                $where = "WHERE " . substr($this->filter, 4);
            }        
            //$groupBy = $this->order_by;
            
             if (@$this->order_by != "")
                    $sort = "ORDER BY " . $this->order_by;
                else
                    $sort = "ORDER BY c1.carrier asc";
        
            $sql = "SELECT ".$fields." FROM carrier c1 $where ".$sort.""; 
            if($debug){
                echo $sql ;
                die;
            }
            return Carrier::getCarrierListFromSql($sql);
	}

	/**
	* Limit list to consigments with given Postcode value
	* @param $postcode_value
	*/
	public function addFilter($code)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$code." ";
	}
        
        
        
	/**
	* Limit list to consigments with given Postcode value
	* @param $postcode_value
	*/
	public function addCarrierFilter($code)
	{
		$this->filter .= " AND ";
		$this->filter .= " cl.carrier = '".DbAccess3::escape($code)."'";
	}
		/**
	 * Get count of consignment items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount($debug=false)
	{
		$result = $this->getList($debug);
		return sizeof($result);
	}

	/*     * *
     * Order by HAWB
     */

    public function AddOrderBy($columnNamem, $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnNamem." " . ($ascending ? " ASC" : " DESC");
    }
    
    public function addIdFilter($code){
        $this->filter .= " AND ";
        $this->filter .= " cl.id = '".DbAccess3::escape($code)."'";
    }
    public function setGroup($groupBy){
		$this->groupBy  = " group by cl.".$groupBy;
	}
    public function addFilterIn($field, $values) {
        if (is_array($values)) { 
            $this->filter .= " AND " . $field ." IN (" . implode(",", $values) . ")";
        } else {
            $this->filter .= " AND " . $field . " IN ('" . $values . "')";
        }
    }    
}  // class
?>