<?php
/*
 * Consignment Filter
 *
 */
class PreSortCarrierFilter
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
				FROM presort_carrier
				$where
				$sort
				";
				
		//mail("mruga@oneworldexpress.com", "sql", $sql);		

		t($sql, __METHOD__);
		

		

		return PreSortCarrier::getPreSortCarrierListFromSql($sql);
	}
	
	
	
	
	
	
	
}