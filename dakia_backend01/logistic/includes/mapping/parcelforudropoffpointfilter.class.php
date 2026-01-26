<?php
class parcelforuDropoffPointFilter
{
	private $filter_str = "";
	private $limit = 200;
	//
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
		
		$sql = "SELECT * FROM parcelforu_dropoff_point p $where $sort LIMIT 5000";

		
		return parcelforuDropoffPoint::getparcelForUDropoffPointRoutineListFromSql($sql);
	}
        
        public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " AND ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
        public function insertData($query) {
        if (trim($query) != '') {
          
            return parcelforuDropoffPoint::runQuery($query);
        }
        }
        
        public function deletep4uRouting()
        {
                    $sql = "TRUNCATE TABLE parcelforu_dropoff_point"; 
                    return DbAccess3::runQuery($sql);		
        }
        
        public function getLatLng($lat, $lng)
        {
             $sql = "   SELECT 
                    *,
                    (3956 * 2 * ASIN(SQRT(POWER(SIN((".$lat." - latitude) * PI() / 180 / 2),
                                            2) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * POWER(SIN((".$lng." - longitude) * PI() / 180 / 2),
                                            2)))) AS distance
                FROM
                    parcelforu_dropoff_point
                HAVING distance <= 10
                ORDER BY distance";
            return parcelforuDropoffPoint::getparcelForUDropoffPointRoutineListFromSql($sql);
        }

	/***
	 * Filter by sent date.
	 */
	
}