<?php
class oweSouthAfricaPostcodeFilter
{
	private $filter_str = "";
	private $limit = 200;
	//
	public function getList($debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		
		$sql = "SELECT * FROM owe_southafrica_postcode o	$where $sort LIMIT 5000";

		if($debug)
                {
                    echo $sql;
                    die;
                }
		return oweSouthAfricaPostcode::getOweSouthAfricaPostcodeListFromSql($sql);
	}
        
        public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " AND ".$colm." = '" . DbAccess3::escape($value) . "'";
	}

	/***
	 * Filter by sent date.
	 */
	
}