<?php

// get settings
class MawbParcelMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $groupby = "";

    /**
     * Get list of MawbParcelMapping items based on filter conditions
     *
     * @return array[MawbParcelMapping]
     */
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

        $sql = "SELECT count(mpm.id) as total FROM  mawb_parcel_mapping mpm $checkJoin $where $groupBy $sort ";
        t($sql, __METHOD__);
        return MawbParcelMapping::getTotalNumberOfMawbParcelMappingFromSql($sql);
    }
    
    public function getMawbBaggingPagingCount() {
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

        $sql = "SELECT COUNT(id) AS total FROM (SELECT mpm.id FROM  mawb_parcel_mapping mpm $checkJoin $where $groupBy $sort ) AS sdf ";
        t($sql, __METHOD__);
        return MawbParcelMapping::getTotalNumberOfMawbParcelMappingFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*',$debug=false) {
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
        
       $sql = "SELECT " . $columns . "  FROM mawb_parcel_mapping mpm  $checkJoin $where $groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug) {
            echo $sql;
            die;
        }
       t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }

    /**
     * Get list of MawbParcelMapping items based on filter conditions
     *
     * @return array[MawbParcelMapping]
     */
    public function getList($columnName = "*", $debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $_groupby = "";
        if ($this->groupby != "")
            $_groupby = "GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
       $sql = "SELECT $columnName 
                FROM mawb_parcel_mapping mpm
                $checkJoin
                $where
                $_groupby
                $sort
                ";
       if($debug) {
           die($sql);
       }
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000, $debug = false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $_groupby = "";
        if ($this->groupby != "")
            $_groupby = "GROUP BY " . $this->groupby;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", mpm.id 
				FROM mawb_parcel_mapping mpm
                                $checkJoin
				$where
				$_groupby
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug){
            echo $sql;
            die;
        }
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }

    public function getColumnListLimit($fields) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT id, " . $fields . "
				FROM mawb_parcel_mapping mpm
				$where
				$sort
				LIMIT 40000
				";
//      echo $sql;
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    
    function delete_parcel_from_mapping() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sql =  "DELETE 
                    FROM mawb_parcel_mapping
                    $where";
        return MawbParcelMapping::deleteBySql($sql);
    }
    /**
     * Get count of MawbParcelMapping items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by Column Name
     */

    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addFieldNotEqualFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . DbAccess3::escape($filterVal) . " ";
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFilterIn($field, $values) {
	if (trim($this->filter) != "") {
			$this->filter .= " AND ";
	}
	if (is_array($values)) { 
		$this->filter .= $field ." IN (" . implode(",", $values) . ")";
	} else {
		$this->filter .= $field . " IN (" . $values . ")";
	}
    }
    public function addExtraChargesFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $colm . " > 0";
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    public function addGroupBy($colm) {
        if ($this->groupby != "")
            $this->groupby .= " , ";
        $this->groupby .= "  " . $colm . " ";
    }
    /***
    * Limit number of rows returned
    */
    public function setLimit ($lim)
    {
            $this->limit = $lim;
    }
    public function addDateFilter($date_value1, $date_value2, $date = "date_created"){
        if($date_value1 != ''){
            $this->filter .= " AND ";
            $this->filter .= "(" . $date . ">='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= $date . "<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
        }
    }
    public function getBatchScanningReport($limit = true,$debug = false) {
        
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
         $setLimit = "";
        if($limit) {
            $setLimit = "
                    LIMIT 
                    $this->pageOffset , 
                    $this->rowsPerPage";
        }

        $sql=  "SELECT 
                    w.`warehouse_name` AS warehouse_name,
                    w.`id` AS wharehouse_id,
                    mpm.`mawb_id`,
                    mawb.`mawb_number`,
                    mpm.`bag_id`,
                    b.`bagnumber` AS bag_number,
                    COUNT(mpm.`parcel_id`) AS total_tracking,
                    SUM(
                      CASE
                        WHEN td.`tracking_number` IS NULL 
                        THEN 0 
                        ELSE 1 
                      END
                    ) AS total_scan,
                    SUM(
                      CASE
                        WHEN td.`tracking_number` IS NULL 
                        THEN 1 
                        ELSE 0 
                      END
                    ) AS total_not_scan,
                    GROUP_CONCAT(
                      DISTINCT u.`user_name` SEPARATOR '<br />'
                    ) AS scanned_by 
                  FROM
                    `mawb_parcel_mapping` mpm 
                    JOIN `bagging` b 
                      ON b.`id` = mpm.`bag_id` 
                    LEFT JOIN tracking_data td 
                      ON td.`entity_id` = mpm.`parcel_id` 
                      AND td.`status_code_id` = '146' 
                      AND td.`entity_type` = 'parcel' 
                    LEFT JOIN `user` u 
                      ON u.`id` = td.`user_id` 
                    LEFT JOIN `warehouse` w 
                      ON w.`id` = td.`warehouse_id`    
                      LEFT JOIN `mawb` mawb 
                      ON mawb.`id` = mpm.`mawb_id`  
                    $where
                    GROUP BY mpm.`bag_id`,td.`warehouse_id`      
                    $sort  
                    $setLimit";
        if($debug) {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    
    public function getBatchScanningReportCount($debug = false) {
        
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $sql =  "SELECT 
                COUNT(warehouse_name) AS total 
                FROM (
                    SELECT 
                    w.`warehouse_name` AS warehouse_name,
                    mpm.`mawb_id`,
                    mawb.`mawb_number`,
                    b.`bagnumber` AS bag_number,
                    COUNT(mpm.`parcel_id`) AS total_tracking,
                    SUM(
                      CASE
                        WHEN td.`tracking_number` IS NULL 
                        THEN 0 
                        ELSE 1 
                      END
                    ) AS total_scan,
                    SUM(
                      CASE
                        WHEN td.`tracking_number` IS NULL 
                        THEN 1 
                        ELSE 0 
                      END
                    ) AS total_not_scan,
                    GROUP_CONCAT(
                      DISTINCT u.`user_name` SEPARATOR '<br />'
                    ) AS scanned_by 
                  FROM
                    `mawb_parcel_mapping` mpm 
                    JOIN `bagging` b 
                      ON b.`id` = mpm.`bag_id` 
                    LEFT JOIN tracking_data td 
                      ON td.`entity_id` = mpm.`parcel_id` 
                      AND td.`status_code_id` = '146' 
                      AND td.`entity_type` = 'parcel' 
                    LEFT JOIN `user` u 
                      ON u.`id` = td.`user_id` 
                    LEFT JOIN `warehouse` w 
                      ON w.`id` = td.`warehouse_id` 
                    LEFT JOIN `mawb` mawb 
                    ON mawb.`id` = mpm.`mawb_id` 
                        $where
                      GROUP BY  mpm.`bag_id`,td.`warehouse_id`
                        $sort  
                    ) AS sdf";
        if($debug) {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return MawbParcelMapping::getTotalNumberOfMawbParcelMappingFromSql($sql);
    }
  
   
    public static function getBagParcelTrackingNo($master_number, $bag_id,$warehouseId) {
        $sql = "SELECT 
                    b.`bagnumber` AS bag_id,
                    mpm.`mawb_id`,
                    p.`tracking_number` AS parcel_id,
                    p.`parcel_status_code` AS parcel_status_code,
                     w.`warehouse_name` AS wharehouse_id 
                  FROM
                    `mawb_parcel_mapping` mpm 
                    JOIN bagging b
                    ON b.`id` = mpm.`bag_id`
                    JOIN parcel p
                    ON p.`id` = mpm.`parcel_id`
                    LEFT JOIN warehouse w
                    ON w.`id`= mpm.`wharehouse_id`  
                WHERE mpm.`mawb_id` = '" . DbAccess3::escape($master_number) . "' 
                        AND mpm.`bag_id` = '" . DbAccess3::escape($bag_id) . "' 
                        AND mpm.`wharehouse_id` = '" . DbAccess3::escape($warehouseId) . "' ";
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    public function addFieldFindInSet($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " FIND_IN_SET('" . DbAccess3::escape($value) . "'," . $colm . " )";
    }
    public function addFilterBagNotEmpty() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "     mpm.`bag_id` != ''  ";
    }
  
    
    public static function getTodayMawbNo($curDate='',$wareHouseId = 0 , $userId = 0) {
        $sql = "SELECT 
                mpm.* 
              FROM
                mawb_parcel_mapping mpm 
              WHERE mpm.`wharehouse_id` IN (".implode(',', $wareHouseId).")
                AND DATE(mpm.`date_added`) = '".$curDate."' GROUP BY mpm.mawb_id";
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    
    public static function getAllDataOfMawbParcel($mawbNumber,$warehouseId) {
        $sql = "SELECT 
                    mpm.* 
                  FROM
                    mawb_parcel_mapping mpm 
                  WHERE mpm.`mawb_id` = '" . DbAccess3::escape($mawbNumber) . "' AND mpm.`wharehouse_id` = '" . DbAccess3::escape($warehouseId) . "'";
        
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    
    public static function getMawbService($mawbNumber='') {
        
        if(is_array($mawbNumber))
            $mawbNumbers = "'".implode("','",$mawbNumber)."'";
        else
            $mawbNumbers = "'".$mawbNumber."'";
        $sql = "SELECT 
                    mpm.`mawb_id`,
                    m.`mawb_number`,
                    GROUP_CONCAT(DISTINCT b.`bagnumber`) AS bag_id,
                    s.`name` AS wharehouse_id,
                    COUNT(mpm.`id`) AS parcel_id,
                    SUM(p.`weight`) AS actual_weight,
                    ca.`carrier` AS width
                  FROM
                    `mawb_parcel_mapping` mpm 
                    JOIN parcel p 
                      ON p.`id` = mpm.`parcel_id` 
                    JOIN mawb m
                       ON mpm.`mawb_id` = m.`id`
                    LEFT JOIN bagging b 
                      ON b.`id` = mpm.`bag_id` 
                    JOIN consignment c 
                      ON c.`id` = p.`consignment_id` 
                    JOIN services s 
                      ON s.`id` = c.`service_id` 
                    JOIN `carrier` ca 
                      ON ca.`id` = s.`carrier_id`                       
                  WHERE m.`mawb_number` IN (".$mawbNumbers.")
                  GROUP BY mpm.`mawb_id`,
                    s.`id` ";
//        echo $sql;exit;
        t($sql, __METHOD__);
        return MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
    }
    public function getMawbParcel($mawbId){
        $return = "";
        if($mawbId > 0){
            $sql = "SELECT
                          m.`mawb_number`,
                          p.`tracking_number`,
                          mpm.`parcel_id`, 
                          mpm.`id` 
                        FROM
                          `mawb_parcel_mapping` mpm
                          JOIN mawb m
                            ON m.`id` = mpm.`mawb_id`
                          JOIN parcel p
                            ON p.`id` = mpm.`parcel_id`
                        WHERE mpm.`mawb_id` = '" . DbAccess3::escape($mawbId) . "' ";
            t($sql, __METHOD__);
            $return =  MawbParcelMapping::getMawbParcelMappingListFromSql($sql);
        }
        return $return;
    }
}

// class
?>