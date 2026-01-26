<?php

// get settings
//require_once("includes/settings/common.inc.php");

class DomesticDPDFilter
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

		$sql = "SELECT * FROM domestic d $where ".$groupBy." Order by id asc"; 

		return DomesticDPD::getDomesticDPDListFromSql($sql);
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

		$sql = "SELECT ".$fields.", id FROM domestic d $where ".$groupBy." Order by id asc"; 


		return DomesticDPD::getDomesticDPDListFromSql($sql);
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
	
	
	
	
}  // class
?>