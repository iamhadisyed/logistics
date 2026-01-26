<?php
/*
 * Consignment Filter
 *
 */
class BaggingFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
	private $join= "";

        
    /* Najam Code */    
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT COUNT(id) AS total FROM  `bagging` b $where $groupBy $sort ";
        t($sql, __METHOD__);
        return Bagging::getTotalNumberOfBaggingFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*') {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY b.id DESC";
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
       $sql = "SELECT " . $columns . "  FROM `bagging` b $where $groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
       t($sql, __METHOD__);
        return Bagging::getBaggingListFromSql($sql);
    }
    
    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    
    public function addGroupBy($colm) {
        if ($this->groupby != "")
            $this->groupby .= " , ";
        $this->groupby .= "  " . $colm . " ";
    }
    /* Najam Code */    
	
	public function getList( $debug = false)
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
				FROM bagging b
				$where
				$sort limit 100
				";
                if($debug){
                    echo $sql;
                    die;    
                }
		return Bagging::getBaggingListFromSql($sql);
	}
    	
	public function addFilter ($type)
    {		
		$this->filter .= $type;		
    }
	
	 public function addFieldEqualFilter($columnName, $operator, $columnValue) {
        $this->filter .= " AND ";
        $this->filter .= "b." . $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }
	

	
	public function addDateFilter ($date)
    {		
		$this->filter .= " AND date_created >= '$date' AND date_created <= ' " . date("Y-m-d 23:59:59") . "'";		
    }
    public function addOrderByField ($field, $desc = 'desc')
    {  
  		$this->order_by =  "$field $desc";  
    }
	

	
	public function getColumnList($fields)
	{
	    $selectColumn = 'id,'.$fields;
	    if(trim($fields) == "*"){
            $selectColumn = trim($fields);
        }
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT ".$selectColumn."
				FROM bagging b
				$where
				$sort
				";
		return Bagging::getBaggingListFromSql($sql);
	}	
	

    public function addDateCreatedFilter($date_value1, $date_value2){
        if($date_value1 != ''){
            if(!empty($this->filter))
                $this->filter .= " AND ";
            $this->filter .= "DATE(b.date_created)>='" . date('Y-m-d', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= "DATE(b.date_created)<='" . date('Y-m-d',strtotime($date_value2)) . "'";
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
    public function getBagParcelWeightByBagId($bagId) {
        $return = "";
        if($bagId > 0){
            $sql = "SELECT
                        SUM(p.`weight`) AS actual_weight,
                        pbm.bag_id
                      FROM
                        `parcel_bagging_mapping` pbm
                        JOIN parcel p
                          ON p.`id` = pbm.`parcel_id`
                      WHERE pbm.`bag_id` = '".DbAccess3::escape($bagId)."'";
            $return = Bagging::getBaggingListFromSql($sql);
        }
        return $return;
    }
    public static function getParcelTotalFromBagId($bagId) {
        $return = 0;
        if($bagId){
            $sql = "SELECT
                    COUNT(pbm.id) AS id
                  FROM
                    parcel_bagging_mapping pbm
                  WHERE pbm.`bag_id` ='".DbAccess3::escape($bagId)."'";
            $return = Bagging::getBaggingListFromSql($sql);
        }
        return $return;
    }
    public function getServiceBag($serviceId,$mawbId,$lowValue) {
        $return = "";
        if($serviceId > 0){
            $bagValueChk = ($lowValue == 0 ? "b.bag_value is NULL" : "b.bag_value = '".DbAccess3::escape($lowValue)."'");
            $sql = "SELECT
                        b.*    
                      FROM
                        bagging b
                        JOIN `mawb_parcel_mapping` mpm
                        ON mpm.`bag_id` = b.`id`
                      WHERE b.service = '".DbAccess3::escape($serviceId)."'
                            AND mpm.mawb_id = '".DbAccess3::escape($mawbId)."'
                            AND ". $bagValueChk .
                            " AND b.is_closed = '0' AND b.is_country_bagging = 'n'
                            GROUP BY b.`id`
                      LIMIT 100";
            $return = Bagging::getBaggingListFromSql($sql);
        }
        return $return;
    }
    public function getBagData($sourceCountryId,$destinationCountryId,$sourceWarehouseId,$userAccountId){
	    $return = "";
	    if($sourceCountryId > 0 && $destinationCountryId > 0 && $sourceWarehouseId > 0 && $userAccountId > 0){
            $sql = "SELECT
                      b.*
                    FROM
                      bagging b
                      JOIN parcel_bagging_mapping pbm
                        ON b.`id` = pbm.`bag_id`
                      LEFT JOIN mawb_parcel_mapping mpm
                        ON pbm.`parcel_id` = mpm.`parcel_id`
                      JOIN `user` u
                        on u.`id` = b.`user_id`
                    WHERE mpm.id IS NULL AND b.`bag_destination_country_id` = '".DbAccess3::escape($destinationCountryId)."'
                      AND b.`bag_source_country_id` = '".DbAccess3::escape($sourceCountryId)."'
                      AND b.`bag_source_warehouse_id` = '".DbAccess3::escape($sourceWarehouseId)."'
                      AND b.`bag_status` = '1' AND b.`is_closed` = '0' AND u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' and b.is_country_bagging = 'y' ORDER BY b.`id` DESC
                      ";
//            echo $sql;die;
            $return = Bagging::getBaggingListFromSql($sql);
        }
	    return $return;
    }
    public function getOpenBagData($bagSourceWarehouseId,$userId,$bagType="n"){
        $return = "";
	    if($bagSourceWarehouseId > 0 && $userId > 0){
	        $sql = "
	                SELECT
                          b.*,
                          c.`name` AS country_name
                        FROM
                          bagging AS b
                          JOIN parcel_bagging_mapping pbm
                            ON b.`id` = pbm.`bag_id`
                          LEFT JOIN mawb_parcel_mapping mpm
                            ON pbm.`parcel_id` = mpm.`parcel_id`
                          JOIN country c
                            ON c.`id` = b.`bag_destination_country_id`
                        WHERE mpm.id IS NULL
                          AND b.`bag_source_warehouse_id` = '".DbAccess3::escape($bagSourceWarehouseId)."'
                          AND b.`user_id` = '".DbAccess3::escape($userId)."'
                          AND b.`is_fixed` = '".DbAccess3::escape($bagType)."'
                          AND b.`is_country_bagging` = 'y'
                        GROUP BY b.`id` ORDER BY b.`id` DESC
	                ";
                
            $return = Bagging::getBaggingListFromSql($sql);
        }
        return $return;
    }
}