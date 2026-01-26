<?php
/*
 * Consignment Filter
 *
 */
class ConsignmentBaggingMappingFilter
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
				FROM consignment_bagging_mapping b
				$where
				$sort limit 5000
				";
				
		//mail("mruga@oneworldexpress.com", "sql", $sql);		

		t($sql, __METHOD__);
		

		

		return ConsignmentBaggingMapping::getConsignmentBaggingListFromSql($sql);
	}
	

	
	public function addFilter ($type)
    {		
		$this->filter .= $type;		
    }
	
	 public function addFieldEqualFilter($columnName, $operator, $columnValue) {
        $this->filter .= " AND ";
        $this->filter .= "b." . $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }
	


	
	

	
	
}