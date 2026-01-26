<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier tariffs
//
////////////////////////////////////////////////////
/**
 * Tariff - Tariff class
 * @package Courier
 */
class TariffFilter
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
				FROM tariffs
				$where
				$sort
				";


		//echo $sql;
		return Tariff::getTariffListFromSql($sql);
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
				FROM tariffs
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
		return Tariff::getTariffListFromSql($sql);
	}
	
	
	public function getTarifListDetail()
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
				FROM tariffs
				$where
				ORDER BY collection_rateband_id,
						destination_rateband_id,
						weight_from,
						collection_postcode_group_id,
						destination_postcode_group_id
				";
		//echo $sql;
		return Tariff::getTariffListFromSql($sql);
	}
	
	public function getTarifServicesList($tariffName)
	{
		$sql = "SELECT 
					ser.id as id, code 'added_by', name 'changed_by', carrier 'formula'
				FROM
					tariffs ts
						INNER JOIN
					services ser ON ts.courier_service_id = ser.id
				WHERE
					ts.customer_id = '".$tariffName."'
				 
				 group by ser.code
				 order by ser.name
				";
		//echo $sql;
		return Tariff::getTariffListFromSql($sql);
	}
	
	public function getAllTariffs()
	{
		
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "  " . $this->filter;
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		
		
		
		$sql = "SELECT 
					customer_id, uploaded_currency 'weight_from', t.added_by, t.changed_on, t.courier_service_id, s.name 'tariff',s.carrier  'unit_size'
				 FROM 
					rumba19.tariffs AS t 
					INNER JOIN services AS s ON t.courier_service_id = s.id 
				WHERE 
						t.customer_id <> ''
						$where 
					AND 
						t.active = '1' 
					GROUP by 
						customer_id";
		//echo $sql;
		return Tariff::getTariffListFromSql($sql);
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
				FROM tariffs
				$where
				ORDER BY collection_rateband_id,
						destination_rateband_id,
						collection_postcode_group_id,
						destination_postcode_group_id
				";
		return Tariff::getTariffListFromSql($sql);
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
                $groupby = "";
		if ($this->group_by != "") $groupby = "GROUP BY " . $this->group_by;


		$sql = "SELECT id, ".$fields."
				FROM tariffs
				$where
                                $groupby
				$sort
				";

		t($sql, __METHOD__);
		

		return Tariff::getTariffListFromSql($sql);

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
				FROM tariffs
				$where
				$sort
				";

		t($sql, __METHOD__);
		return Tariff::getTariffListFromSql($sql);

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
	public function addFieldGroupBy($sort)
	{
                if(trim($this->group_by) != '')
                    $this->group_by .= ",".$sort;    
                else
                    $this->group_by = $sort;
	}
		
	
        public function getCountryNameByRateband($rateband){
            $sql = "SELECT  (SELECT con.name FROM country con WHERE con.id = c.country_id) AS collection_rateband_id FROM ratebands r INNER JOIN `countries_link_ratebands` c ON r.id = c.rateband_id WHERE c.rateband_id = '" . DbAccess3::escape($rateband) . "'";    
             t($sql, __METHOD__);
            return Tariff::getTariffListFromSql($sql);
        }
     
        
}

	

?>
