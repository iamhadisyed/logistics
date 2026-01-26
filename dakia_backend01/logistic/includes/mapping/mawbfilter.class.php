<?php
/*
 * MAWB Filter
 *
 */
class MawbFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";

	
	public function getList($column="*",$debug=false)
	{
		
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;
		}
		
		$sort = "";
//		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort  = "ORDER BY " . $this->order_by;	

		$sql = "SELECT $column
				FROM mawb m
				$where
				$sort
				";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
//		echo $sql;die;		
				
		t($sql, __METHOD__);		
		return Mawb::getConsignmentListFromSql($sql);
	}
	
	
	public function getCount() {
		$result = $this->getList();
		return sizeof($result);
	}
	public function getPagingCount() {
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
                
                $sql = "SELECT count(id) as total FROM mawb $where $sort ";
		t($sql, __METHOD__);
		return Mawb::getTotalNumberOfRecordsFromSql($sql);
	}
	
		public function getPagingList ()
		{
				
				$where = "";
				if ($this->filter != "")
				{
					$where = "WHERE " . substr($this->filter, 4);
				}
				$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
				 $sql = "SELECT id, consignment_id, tracking_number, mawb_number, carton_number, manifest_number, date_created from mawb m " .
				 		 $where . " " . $sort . " LIMIT  " . $this->pageOffset . "," .  $this->rowsPerPage;
                                          echo '<pre>';
                                          print_r($sql);
                                          echo '</pre>';
                                          die;
		
			/*if( $_SERVER['REMOTE_ADDR'] == '188.66.86.88')*/
				//echo $sql;
			t($sql, __METHOD__);

				return Mawb::getConsignmentListFromSql($sql);
			}


			public function setOffset($offset)
			{
				$this->pageOffset = $offset;
			}

			public function setRowsPerPage($rowsPP)
			{
				$this->rowsPerPage = $rowsPP;
			}
	
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFieldNotEqualFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '" . DbAccess3::escape($value) . "'";
    }
    public function addOrFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " OR ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
	
    public function getListMawb(){
        $where = "";
        if (!empty($this->filter)){
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") 
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY m.id DESC";
        $limit = '';
        if(!empty($this->rowsPerPage)){
            $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }
         $sql = "SELECT 
                m.id,
                m.added_date,
                m.`mawb_number`,
                c.`name` AS destination_country,
                coun.`name` AS source_country,
                w.`warehouse_name` AS destination_warehouse,
                wa.`warehouse_name` AS source_warehouse 
              FROM
                mawb m 
                JOIN country c 
                  ON c.`id` = m.`mawb_destination_country_id` 
                JOIN country coun 
                  ON coun.id = m.`mawb_source_country_id` 
                LEFT JOIN `warehouse` w 
                  ON w.`id` = m.`mawb_destination_warehouse_id` 
                LEFT JOIN `warehouse` wa
                  ON wa.`id` = m.`mawb_source_warehouse_id` $where  $sort $limit";
        t($sql, __METHOD__);
        return Mawb::getMawbListFromSql($sql);
    }
        
    public function getMawbTotalCount(){
        $where = "";
        if (!empty($this->filter)){
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sql = "SELECT 
                COUNT(id) AS id
              FROM
                (SELECT 
                m.id 
              FROM
                mawb m 
                JOIN country c 
                  ON c.`id` = m.`mawb_destination_country_id` 
                JOIN country coun 
                  ON coun.id = m.`mawb_source_country_id` 
                JOIN `warehouse` w 
                  ON w.`id` = m.`mawb_destination_warehouse_id` 
                  JOIN `warehouse` wa
                  ON wa.`id` = m.`mawb_source_warehouse_id` $where ) AS asd ";
        t($sql, __METHOD__);
        return Mawb::getMawbListFromSql($sql);
    }
	/*public function addGroupByMawb()
	{
		$this->group_by = "mawb";
	}*/
    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }
    public function addDateCreatedFilter($date_value1, $date_value2){
        if($date_value1 != ''){
            if(!empty($this->filter))
                $this->filter .= " AND ";
            $this->filter .= "    DATE(m.added_date)>='" . date('Y-m-d', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= "DATE(m.added_date)<='" . date('Y-m-d',strtotime($date_value2)) . "'";
        }
    }
    public function addFilterIn($field, $values) {
        if (is_array($values)) { 
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field ." IN (" . implode(",", $values) . ")";
        } else {
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field . " IN ('" . $values . "')";
        }
    }
    public function addFilter($filterVal) {
        if (!empty($this->filter))
            $this->filter .= " AND ";

        $this->filter .= "    " . $filterVal . " ";
    }
    public function getMawbList($fromCountryId,$toCountryId) {
        if($fromCountryId > 0 && $toCountryId > 0){
            $sql  = "SELECT
                            *
                          FROM
                            mawb m
                          WHERE mawb_source_country_id = '" . DbAccess3::escape($fromCountryId) . "'
                            AND mawb_destination_country_id = '" . DbAccess3::escape($toCountryId) . "'
                            AND (
                              mawb_status = 'o'
                              OR mawb_status = 'pd'
                            )";
            t($sql, __METHOD__);
            return Mawb::getMawbListFromSql($sql);
        }
    }
    public function getFlightMawbList($flightId,$fromCountryId,$toCountryId) {
        if(!empty($flightId) && $flightId > 0 && $fromCountryId > 0 && $toCountryId > 0) {
            $sql = "SELECT
                      mawb.*
                    FROM
                      `flight_mapping` fm
                      JOIN mawb
                        ON mawb.`id` = fm.`mawb_id`
                    WHERE fm.`flight_info_id` = '" . DbAccess3::escape($flightId) . "'
                      AND mawb.`is_active` = 'y'
                      AND mawb.`mawb_status` != 'd'
                      AND mawb.`mawb_source_country_id` = '" . DbAccess3::escape($fromCountryId) . "'
                      AND mawb.`mawb_destination_country_id` = '" . DbAccess3::escape($toCountryId) . "'";
            t($sql, __METHOD__);
            return Mawb::getMawbListFromSql($sql);
        }
    }
}