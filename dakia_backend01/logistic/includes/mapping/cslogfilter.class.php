<?php

// get settings
//require_once("includes/settings/common.inc.php");

class CsLogFilter
{
	private $filter = "";
	private $groupBy= "";
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
		$where = "WHERE ";
		$where .= $this->filter;
		$groupBy	=	$this->groupBy;

		$sql = "SELECT * FROM cs_log c $where ".$groupBy." Order by date_created asc"; 

		return CsLog::getCsLogListFromSql($sql);
	}
	
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getColumnList($fields)
	{

		
		// has filter been configured?
		$where = "WHERE ";
		$where .= $this->filter;
		$groupBy	=	$this->groupBy;

		$sql = "SELECT ".$fields.", id FROM cs_log c $where ".$groupBy." Order by date_created asc"; 


		return CsLog::getCsLogListFromSql($sql);
	}
	
	
	public function getPagingCount()
	{
		// has filter been configured?
		$where = "WHERE ";
		$where .= $this->filter;
		$groupBy	=	$this->groupBy;
		$sort = "";
		$sql = "SELECT count(id) as total FROM cs_log c $where ".$groupBy." Order by date_created asc"; 
		t($sql, __METHOD__);
		return CsLog::getCsLogListFromSql($sql);
	}

	public function getPagingList ()
	{
			$where = "WHERE ser.code <> '' ";
			$where .= $this->filter;
			$groupBy	=	$this->groupBy;
			$sort = "";

//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
			 $sql = "SELECT * FROM  cs_log c  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
	//	else
	//	$sql = "SELECT * FROM consignment c $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
		/*if( $_SERVER['REMOTE_ADDR'] == '188.66.86.88')
			echo $sql;*/
		t($sql, __METHOD__);

			return CsLog::getCsLogListFromSql($sql);
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
		if($this->filter != "")
		$this->filter .= " AND ";
		else
		$this->filter .= "  ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
	
	public function addFilter($account_number) {
		//echo $account_number;
		if ($this->filter != "")
			$this->filter .= " AND ";
		$this->filter .= $account_number;
		//echo $this->filter;
		//exit;
	}
	
	public function getAccountCountry($userid) {
		 $sql = "SELECT cs.id, cs.consignment_id, c.account as internal_message, c.country as customer_message FROM cs_log cs, consignment c 
		 where cs.userid = '".$userid."' and c.id = cs.consignment_id and cs.reminder_expiry != '0000-00-00 00:00:00' and cs.reminder_expiry <= '".date("Y-m-d H:i:s")."'";
		 return CsLog::getCsLogListFromSql($sql);
	}
	
	
}  // class
?>