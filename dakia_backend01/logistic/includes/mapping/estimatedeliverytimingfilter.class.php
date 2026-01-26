<?php

// get settings
//require_once("includes/settings/common.inc.php");

class EstimateDeliveryTimingFilter
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

		echo $sql = "SELECT * FROM estimate_delivery_timing adt $where ".$groupBy." Order by service_id asc"; 

		return EstimateDeliveryTiming::getEstimateDeliveryTimingListFromSql($sql);
	}
	
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getColumnList($fields)
	{

		
		// has filter been configured?
		$where = "WHERE adt.id <> '' ";
		$where .= $this->filter;
		$groupBy	=	$this->groupBy;

		$sql = "SELECT ".$fields.", adt.id FROM estimate_delivery_timing adt $where ".$groupBy." Order by adt.id asc"; 


		return EstimateDeliveryTiming::getEstimateDeliveryTimingListFromSql($sql);
	}
	
	public function addFieldFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
}  // class
?>