<?php
/*
 * Consignment Filter
 *
 */
class RatebandFilter
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
				FROM ratebands
				$where
				$sort
				";

		t($sql, __METHOD__);
		
		//echo $sql;
		return Rateband::getRatebandListFromSql($sql);
	}
	
	public function getServiceRatebandNameLIst($service_id, $from_rateband, $to_rateband)
	{
		$sql 	=	"SELECT s.name as changed_by,  r.name as name, r.id as id FROM services as s  inner join ratebands as r ON s.id = r.courier_service_id where s.id = ".DbAccess3::escape($service_id)." and r.id IN ( ".$from_rateband.",".$to_rateband.")";
		t($sql, __METHOD__);
		
		//echo $sql;
		return Rateband::getRatebandListFromSql($sql);
	}
	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "order by name";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT id, ".$fields."
				FROM ratebands
				$where
				$sort
				";

		t($sql, __METHOD__);
		

		return Rateband::getRatebandListFromSql($sql);

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
        
        public function addFilter($colm)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." ";
	}
	
	
	public function getCountryRatebandList($fields, $debug=false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "order by r.name";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
//
//c.name, r.name
//courier_service_id = '23' order by r.id
                

                $sql = "SELECT 
                    ".$fields."
                FROM
                    (country c
                    INNER JOIN countries_link_ratebands clr ON c.id = clr.country_id)
                        INNER JOIN
                    ratebands r ON r.id = clr.rateband_id
                    $where
                    $sort";
		
                if($debug)
                   echo  $sql;
                
                
		t($sql, __METHOD__);
		

		return Rateband::getRatebandListFromSql($sql);

	}


    public function AddOrderBy($columnName='r.id', $ascending = true) {
        //if ($this->order_by != "") $this->order_by .= ", ";
        //
		$this->order_by = $columnName . ($ascending ? "" : " DESC");
    }

	
}