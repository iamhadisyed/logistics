<?php

// get settings
class MawbBagDetailFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $groupby = "";

    

    /**
     * Get list of MawbBagDetail items based on filter conditions
     *
     * @return array[MawbBagDetail]
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
                FROM mawb_bag_detail mpm
                $checkJoin
                $where
                $_groupby
                $sort
                ";
       if($debug) {
           die($sql);
       }
        t($sql, __METHOD__);
        return MawbBagDetail::getMawbBagDetailListFromSql($sql);
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
				FROM mawb_bag_detail mpm
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
        return MawbBagDetail::getMawbBagDetailListFromSql($sql);
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
				FROM mawb_bag_detail mpm
				$where
				$sort
				LIMIT 40000
				";
//      echo $sql;
        return MawbBagDetail::getMawbBagDetailListFromSql($sql);
    }
    
    function delete_parcel_from_mapping() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sql =  "DELETE 
                    FROM mawb_bag_detail
                    $where";
        return MawbBagDetail::deleteBySql($sql);
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
   
   
    public function addFieldFindInSet($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " FIND_IN_SET('" . DbAccess3::escape($value) . "'," . $colm . " )";
    }
    
  
    
}

// class
?>