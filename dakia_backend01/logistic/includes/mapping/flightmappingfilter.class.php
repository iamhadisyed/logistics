<?php

// get settings
class FlightMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $groupBy = "";

    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
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
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT count(f.id) as total FROM flight_mapping f $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return FlightInfo::getFlightInfoListFromSql($sql);
    }

    public function join($joinTable, $where, $type = "")
    {
        $_where = [];
        $operand = '=';
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $_where[] = $col . " " . $operand . " " . DbAccess3::escape($val);
            }
            $_where = implode(" AND ", $_where);
        } else if (is_string($where)) {
            $_where = $where;
        }
        $this->join .= $type . " JOIN " . $joinTable . " ON " . $_where;
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*',$join='') {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->order_by))
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = '';
        if (!empty($join))
            $checkJoin = $join;
        
        
        
       $sql = "SELECT " . $columns . " $joinColumnName FROM flight_mapping m  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return FlightInfo::getFlightInfoListFromSql($sql);
    }

    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
     */
    public function getList($columnName = "*") {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
          //  $joinColumnName = ",c.carrier AS carrier_id";
        }
        
        
       $sql = "SELECT $columnName 
                FROM flight_mapping m
                $checkJoin 
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return FlightMapping::getFlightMappingListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr(stripslashes($this->filter), 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . " 
				FROM flight_mapping m
				$where
                                $groupBy
				$sort
				LIMIT " . $recordLimit . "
				";
//        echo $sql;exit;
        return FlightMapping::getFlightMappingListFromSql($sql);
    }

    /**
     * Get count of RemoteareasGroups items based on filter conditions
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

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . DbAccess3::escape($filterVal) . " ";
    }
//    public function addCarrierJoin() {
//        $this->join.= " JOIN `carrier` c  ON rg.carrier_id = c.id ";
//    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFromFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " >= '" .$value. "'";
    }
    public function addToFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " <= '" .$value. "'";
    }
    public function addInFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " IN( " .$value. ")";
    }
    
    public function deleteOnFlightId($id) {
        
        $sql = "DELETE FROM flight_mapping
                WHERE flight_info_id = '".$id."' ";
        t($sql, __METHOD__);
        return FlightMapping::getFlightMappingListFromSql($sql);
    }
    public function addGroupBy($field) {
        $this->groupBy .= " GROUP BY $field";
    }
}
?>