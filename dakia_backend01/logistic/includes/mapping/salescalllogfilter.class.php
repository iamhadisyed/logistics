<?php

class SalesCallLogFilter
{
	
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
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
				FROM sales_call_log s
				$where
				$sort 
				order by date_call desc
				";
				
		//echo $sql;		

		t($sql, __METHOD__);
		

		

		return SalesCallLog::getSalesCallLogListFromSql($sql);
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

		$sql = "SELECT id, ".$fields." FROM sales_call_log $where $sort order by date_call desc";

		t($sql, __METHOD__);

		return SalesCallLog::getSalesCallLogListFromSql($sql);

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

		$sql = "SELECT count(*) as total FROM sales_call_log $where $sort ";

		t($sql, __METHOD__);

		return SalesCallLog::getSalesCallLogListFromSql($sql);


	}

	public function getPagingList()
	{
			// has filter been configured?
			$where = "";
			if ($this->filter != "")
			{
				$where = "WHERE " . substr($this->filter, 4);
			}
			$sort = "";
			$sort = "ORDER BY date_call desc";


			$sql = "SELECT * FROM sales_call_log $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
			

			t($sql, __METHOD__);

			return SalesCallLog::getSalesCallLogListFromSql($sql);
	}


	public function setOffset($offset)
	{
		$this->pageOffset = $offset;
	}

	public function setRowsPerPage($rowsPP)
	{
		$this->rowsPerPage = $rowsPP;
	}
	
	public function addFieldFilter($colm, $value)
	{
	
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
	
	public function addFieldLikeFilter($colm, $value)
	{
		
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." like '%" . DbAccess3::escape($value) . "%'";
	}
	public function addFilter($account_number) {
		//echo $account_number;
		if ($this->filter != "")
			$this->filter .= " AND ";
		$this->filter .= $account_number;
		//echo $this->filter;
		//exit;
	}
	
	public function addgGroupBy($account_number) {
		$this->group_by .= $account_number;
	}
			
	
	
			
}