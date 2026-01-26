<?php
/*
 * Consignment Filter
 *
 */
class ManifestConsignmentDataFilter
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
				FROM manifest_consignment_mapping m
				$where
				$sort limit 10000
				";
				
	//	echo $sql;		

		t($sql, __METHOD__);
		

		

		return ManifestConsignmentMapping::getManifestListFromSql($sql);
	}
	
	public function addManifestIDFilter ($manifestid)
    {		
		$this->filter .= " AND manifestid = '".DbAccess3::escape($manifestid)."'";		
    }
	
	public function addConsignmentIDFilter ($consignmentid)
    {		
		$this->filter .= " AND consignmentid = '".DbAccess3::escape($consignmentid)."'";		
    }

	
	public function getColumnList($fields)
	{
		
		
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT ".$fields."
				FROM manifest_consignment_mapping m
				$where
				$sort
				";
				
		//mail("mkazim4u@gmail.com", 'Manifest Consignment SQL', $sql);
		//t($sql, __METHOD__);
		
	  //	echo $sql;
		

		return ManifestConsignment::getManifestListFromSql($sql);
	}	
	
	

	
	
}