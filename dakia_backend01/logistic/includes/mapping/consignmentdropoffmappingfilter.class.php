<?php

// get settings
class ConsignmentDropoffMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $groupBy = "";

    /**
     * Get list of ConsignmentDropoffMapping items based on filter conditions
     *
     * @return array[ConsignmentDropoffMapping]
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
        
        $sql = "SELECT count(cdm.id) as total FROM consignment_dropoff_mapping cdm $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return ConsignmentDropoffMapping::getTotalNumberOfConsignmentDropoffMappingFromSql($sql);
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
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id";
        }
        
        
       $sql = "SELECT " . $columns . " $joinColumnName FROM consignment_dropoff_mapping cdm  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ConsignmentDropoffMapping::getConsignmentDropoffMappingListFromSql($sql);
    }

    /**
     * Get list of ConsignmentDropoffMapping items based on filter conditions
     *
     * @return array[ConsignmentDropoffMapping]
     */
    public function getList($columnName = "*",$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id";
        }
        
        
       $sql = "SELECT $columnName $joinColumnName
                FROM consignment_dropoff_mapping cdm
                $checkJoin 
                $where
                $groupBy
                $sort
                ";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        t($sql, __METHOD__);
        return ConsignmentDropoffMapping::getConsignmentDropoffMappingListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000,$debug=false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", id 
				FROM consignment_dropoff_mapping cdm
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        return ConsignmentDropoffMapping::getConsignmentDropoffMappingListFromSql($sql);
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
				FROM consignment_dropoff_mapping cdm
				$where
				$sort
				LIMIT 40000
				";
//      echo $sql;
        return ConsignmentDropoffMapping::getConsignmentDropoffMappingListFromSql($sql);
    }
    /**
     * Get count of ConsignmentDropoffMapping items based on filter conditions
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

        $this->filter .= "    " . $filterVal . " ";
    }
    public function addCarrierJoin() {
        $this->join.= " JOIN `carrier` c  ON cdm.carrier_id = c.id ";
    }
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
    public function addGroupBy($field) {
        $this->groupBy .= " GROUP BY $field";
    }
    public function addNotEqualEmptyFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '' ";
    }
}

// class
?>