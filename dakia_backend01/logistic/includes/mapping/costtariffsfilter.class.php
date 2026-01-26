<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier cost_tariffs
//
////////////////////////////////////////////////////
/**
 * Tariff - Tariff class
 * @package Courier
 */
class CostTariffsFilter
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
				FROM cost_tariffs
				$where
				$sort
				";

		return CostTariffs::getTariffListFromSql($sql);
	}
	
	
	
	public function getTarifList()
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
				FROM cost_tariffs
				$where
				GROUP BY collection_rateband_id,
						 destination_rateband_id,
						collection_postcode_group_id,
						destination_postcode_group_id
				ORDER BY collection_rateband_id,
						destination_rateband_id,
						collection_postcode_group_id,
						destination_postcode_group_id
				";
		//echo $sql;
		return CostTariffs::getTariffListFromSql($sql);
	}


	
	public function getTarifDistinctList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
	
	
		$sql = "SELECT DISTINCT  ".$fields."
				FROM cost_tariffs
				$where
				ORDER BY collection_rateband_id,
						destination_rateband_id,
						collection_postcode_group_id,
						destination_postcode_group_id
				";
		return CostTariffs::getTariffListFromSql($sql);
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
				FROM cost_tariffs
				$where
				$sort
				";

		t($sql, __METHOD__);
		

		return CostTariffs::getTariffListFromSql($sql);

	}
	
	public function getCollectionZones($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT ".$fields."
				FROM cost_tariffs
				$where
				$sort
				";

		t($sql, __METHOD__);
		return CostTariffs::getTariffListFromSql($sql);

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
	
	
	public function addFieldLikeFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." LIKE '" . DbAccess3::escape($value) . "%'";
	}	
	
	
	public function addFieldOrderBy($sort)
	{

		$this->order_by = $sort;
	}

}

	

?>
