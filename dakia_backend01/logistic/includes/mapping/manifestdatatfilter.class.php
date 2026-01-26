<?php
/*
 * Consignment Filter
 *
 */
class ManifestDataFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
	private $rowsPerPage = 0;
    private $pageOffset = 0;

	
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT *
				FROM manifest m
				$where
				$sort limit 100
				";
				

		t($sql, __METHOD__);
		

		

		return Manifest::getManifestListFromSql($sql);
	}
        
        public function getPagingList($debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT * FROM manifest m $where	$sort LIMIT $this->pageOffset , $this->rowsPerPage";
				
        if($debug)
		    echo $sql;
		

		return Manifest::getManifestListFromSql($sql);
	}
        
        
        
        public function getPagingCount($debug=false)
	{
		// has filter been configured?
                $sort = "";
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	
                
                
        $sql = "SELECT count(*) as id FROM manifest m $where $sort ";
              if($debug)
                   echo  $sql;
        return Manifest::getManifestListFromSql($sql);
	}
	
	public function setRowsPerPage($rowsPP) 
	{
        $this->rowsPerPage = $rowsPP;
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
	
	public function addIdFilterIn ($id)
    {		
		$this->filter .= " AND id IN(".$id.") ";		
    }	
	
	public function addIdFilter ($id)
    {		
		$this->filter .= " AND id = '".DbAccess3::escape($id)."' ";		
    }	
	
	public function addAccountFilter ($account)
    {		
		$this->filter .= " AND account = '".DbAccess3::escape($account)."' ";		
    }

	
	public function addAgentFilter ($agent)
    {		
		$this->filter .= " AND agent = '".DbAccess3::escape($agent)."' ";		
    }
	
	public function addTypeFilter ($type)
    {		
		$this->filter .= " AND type = '".DbAccess3::escape($type)."' ";		
    }
	
	public function addFilter ($type)
    {		
		$this->filter .= $type;		
    }
	
	public function addOrderById ($desc = 'desc')
    {		
		$this->order_by =  "id $desc";		
    }
	
	public function addDateRangeFilter ($date1, $date2)
    {		
		$this->filter .= " AND date_format(date_created,'%Y-%m-%d') >= '$date1' AND date_format(date_created,'%Y-%m-%d') <= '$date2'";		
    }
	
	public function addDateFilter ($date)
    {		
		$this->filter .= " AND date_created >= '$date' AND date_created <= ' " . date("Y-m-d 23:59:59") . "'";		
    }
    
     public function addFromAndToDateFilter($date_value1, $date_value2) 
    {
        $date_value1  = trim($date_value1);
        $date_value2  = trim($date_value2);
        if($date_value1 != '')
        {
            $this->filter .= " AND ";
            $this->filter .= "(m.date_created>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "')";
        }

        if($date_value2 != '')
        {
            $this->filter .= " AND ";
            $this->filter .= "(m.date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
        }

        
    }
	
    
    
	
	
	public function addPickupIdFilter ($pickup_id)
    {		
		$this->filter .= " AND pickup_id = '$pickup_id'";		
    }
	
	public function addFieldFilter($field, $value)
    {		
		$this->filter .= " AND $field = '$value'";		
    }
	
	
	
	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT id, ".$fields."
				FROM manifest m
				$where
				$sort
				";
		return Manifest::getManifestListFromSql($sql);
	}	
	

	public function GetTrackingNumberByManifestId ($manifestId){
            
            $sql = "SELECT m.*,mcm.*,c.* FROM consignment c
                    INNER JOIN manifest_consignment_mapping mcm
                    ON mcm.consignmentid = c.id
                    INNER JOIN manifest m
                    ON m.id= mcm.manifestid
                    WHERE m.id =".$manifestId." AND m.is_deleted = 'N'";
//            $sql = "SELECT * FROM consignment LIMIT 3";
            return Consignment::getConsignmentListFromSql($sql);
        }
	public function addNewFieldFilter($colm, $value) {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
        }
        public function addNewNotEqFieldFilter($colm, $value) {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " " . $colm . " != '" . DbAccess3::escape($value) . "'";
        }
        public function addUserAccountFilter($accoutId) {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " user_id IN (SELECT id FROM `user` WHERE user_account_id = '".DbAccess3::escape($accoutId)."' AND `active_flag` = 1 AND `is_deleted` = 0)";
        }
	
	
}