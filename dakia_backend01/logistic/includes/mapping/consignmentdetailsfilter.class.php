<?php
/*
 * Consignment Filter
 *
 */
class ConsignmentDetailsFilter
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
				FROM consignment_details c
				$where
				$sort 
				";
				
	//	echo $sql;		

		t($sql, __METHOD__);
		

		

		return ConsignmentDetails::getConsignmentDetailsListFromSql($sql);
	}
	
	
	
	public function addconsignmentFilter ($id)
    {		
		$this->filter .= " AND consignment_id = '$id'";		
    }
	
	
	
	
	

	
	
}