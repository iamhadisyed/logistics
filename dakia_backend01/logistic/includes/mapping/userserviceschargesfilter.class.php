<?php

// get settings
class UserServicesChargesFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
        private $join = "";
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT *
				FROM user_services_charges usc
				$where
				 $sort ";
		return UserServicesCharges::getUserServicesChargesListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields." FROM user_services_charges usc $where  $sort ";
		return UserServicesCharges::getUserServicesChargesListFromSql($sql);
	}
	public function getPagingCount()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
                
                $servicesJoin = "";
                        if ($this->join != "")
                            $servicesJoin = $this->join;
	
		$sql = "SELECT count(*) as total FROM user_services_charges usc $servicesJoin $where $sort ";
	
		t($sql, __METHOD__);
	
		return UserServicesCharges::getTotalNumberOfUsersFromSql($sql);
	
	}
	public function addUserAccountIdFilter($user_id) {
            if ($this->filter != "") {
		$this->filter .= "    AND ";
            }
            $this->filter .= "     usc.user_account_id = '" .DbAccess3::escape( $user_id) . "'";
	}
	public function getPagingList ()
	{
			// has filter been configured?
			$where = "";
			if ($this->filter != "")
			{
				$where = "WHERE " . substr($this->filter, 4);
			}
			$sort = "";
			if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;
                        
                        $servicesJoin = "";
                        if ($this->join != "")
                            $servicesJoin = $this->join;

                        $agentJoinColumn = "";
                        if (strpos($servicesJoin, 'JOIN `services`') !== false) {
                            $agentJoinColumn = " ,s.name as service_id";
                        }
	
			$sql = "SELECT usc.* ".$agentJoinColumn." FROM user_services_charges usc $servicesJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
			t($sql, __METHOD__);
	
			return UserServicesCharges::getUserServicesChargesListFromSql($sql);
	}
	
	
	public function setOffset($offset)
	{
		$this->pageOffset = $offset;
	}
	
	public function setRowsPerPage($rowsPP)
	{
		$this->rowsPerPage = $rowsPP;
	}
	/**
	 * Get count of user items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}

	
	/**
	 * Filter on given user/password combo
	 *
	 * @param string or array giving $service_type
	 */
	public function addUserIdFilter($id)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "usc.user_account_id ='" . DbAccess3::escape($id) . "'";
	}


	public function addServiceIdFilter($id)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "usc.service_id Like '" . DbAccess3::escape($id) . "'";
	}
	
	public function addFieldLikeFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
        }
        public function addServiceNameFilter($colName,$colVal) {
            if ($this->filter != "") $this->filter .= " AND ";
            
            $this->filter .= " s." . $colName . " LIKE '%" . DbAccess3::escape($colVal) . "%'";
        }
        public function addFieldFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
        }
        public function AddOrderBy($name, $ascending = true) {
            if (trim($name) != '')
                $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }
    public function addServicesJoin() {
        $this->join.= " JOIN `services` s  ON s.id = usc.service_id ";
    }
}  // class
?>