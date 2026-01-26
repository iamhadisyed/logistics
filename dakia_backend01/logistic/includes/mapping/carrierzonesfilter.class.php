<?php

// get settings
class CarrierZonesFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of CarrierZones items based on filter conditions
     *
     * @return array[CarrierZones]
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
        $sql = "SELECT count(cz.id) as total FROM carrier_zones cz $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return CarrierZones::getTotalNumberOfCarrierZonesFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*', $limit = true) {
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

        $sqlLimit = "";
        if($limit) {
            $sqlLimit = " LIMIT ". $this->pageOffset . "," . $this->rowsPerPage;
        }

        $sql = "SELECT " . $columns . " FROM carrier_zones cz $checkJoin $where $sort $sqlLimit";
//        echo $sql; die;
        t($sql, __METHOD__);
        return CarrierZones::getCarrierZonesListFromSql($sql);
    }

    /**
     * Get list of CarrierZones items based on filter conditions
     *
     * @return array[CarrierZones]
     */
    public function getList($columnName = "*",$debug = false) {
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

        $sql = "SELECT $columnName
                FROM carrier_zones cz
                $checkJoin
                $where
                $sort
                ";
        t($sql, __METHOD__);
        if($debug) {
            echo $sql; die;
        }
        return CarrierZones::getCarrierZonesListFromSql($sql);
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

        if ($recordLimit == '')
            $recordLimit = 5000;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . ", cz.id 
				FROM carrier_zones cz
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug) {
            echo $sql;
            die;
        }
        return CarrierZones::getCarrierZonesListFromSql($sql);
    }

    /**
     * Get count of CarrierZones items based on filter conditions
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
        $this->order_by .= $columnName . ($ascending ? " ASC" : " DESC");
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
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addCarrierJoin() {
        $this->join.= " JOIN `carrier` c  ON cz.carrier_id = c.id ";
    }
    public function addJoin($table,$on, $type="") {
        $this->join.= $type." JOIN ".$table."  ON  ".$on;
    }
    public function addIsDeletedFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    cz.status != '2'";
    }
    public function addFilterIn($field, $values) {
        if (is_array($values)) {
            $this->filter .= " AND " . $field ." IN (" . implode(",", $values) . ")";
        } else {
            $this->filter .= " AND " . $field . " IN ('" . $values . "')";
        }
    }
}

// class
?>