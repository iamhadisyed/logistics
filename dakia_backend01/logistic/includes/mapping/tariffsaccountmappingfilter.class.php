<?php

// get settings
//require_once("includes/settings/common.inc.php");

class TariffsAccountMappingFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
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
				FROM tariffs_account_mapping tam
				$where
				 $sort ";

		return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields." FROM tariffs_account_mapping tam  $where  $sort "; 
		
		
		return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);
	}
	
	
    public function getTariffNameDistinctList($fields, $debug=false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT distinct ".$fields."  FROM tariffs_account_mapping tam inner join tariffs t on tam.tariff_id = t.id  $where  $sort ";
		
		if($debug)
                    echo  $sql;
		return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);
	}
    public function getUserAccountTariffDistinctList($fields, $debug=false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT "
                        . "     DISTINCT ".$fields."  "
                        . " FROM "
                        . "     tariffs_account_mapping tam "
                        . " INNER JOIN customer_account ua "
                        . " ON tam.user_account_id = ua.id "
                        . " $where "
                        . " $sort ";
		
		if($debug)
                    echo  $sql;
		return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);
	}
	
	
	public function getPagingCount()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;//substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

		$sql = "SELECT count(*) as total FROM tariffs_account_mapping tam $where $sort ";

		t($sql, __METHOD__);

		return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);

	}

	public function getPagingList ()
	{
			// has filter been configured?
			$where = "";
			if ($this->filter != "")
			{
				$where = "WHERE " . $this->filter;//substr($this->filter, 4);
			}
			$sort = "";
			if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;


		 $sql = "SELECT * FROM tariffs_account_mapping tam $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
			
			

			t($sql, __METHOD__);

			return TariffsAccountMapping::getTariffsAccountMappingListFromSql($sql);
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
	
	public function addUserAccountFilter($useraccount)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= " tam.user_account_id = '" . DbAccess3::escape($useraccount) . "'";
	}
	/**
	 * Filter on given user/password combo
	 *
	 * @param string or array giving $service_type
	 */
	public function addFieldFilter($field,$value)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= $field."='". DbAccess3::escape($value) . "'";
	}
	
	/**
	* expunge. Real delete
	* @return void
	*/
	public function expunge()
	{
		$where = "";
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;//substr($this->filter, 4);
		}
		//$sort = "";
		
		
	 	$sql = "DELETE `tam` FROM tariffs_account_mapping tam $where ";

		return DbAccess3::runQuery($sql);
	}



	
}