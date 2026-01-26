<?php
/*
 * Consignment Filter
 *
 */
class CollectionTimeGroupFilter
{
	private $filter = "";
	private $order_by = "";

	
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT *
				FROM collection_time_groups
				$where
				$sort
				";

		t($sql, __METHOD__);
		
		//echo $sql;
		return CollectionTimeGroup::getCollectionTimeGroupList($sql);
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
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT id, ".$fields."
				FROM collection_time_groups
				$where
				$sort
				";

		t($sql, __METHOD__);
		
		//echo $sql;
		return CollectionTimeGroup::getCollectionTimeGroupList($sql);

	}
	
	
	
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}	
	
	public function addFieldFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
	
	
	




	
}