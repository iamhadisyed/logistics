<?php

// get settings
class carrierServiceCustomizeRulesFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of carrierServiceCustomizeRules items based on filter conditions
     *
     * @return array[carrierServiceCustomizeRules]
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

        $sql = "SELECT count(*) as total FROM carrier_service_customize_rules cscr $where $sort ";
        t($sql, __METHOD__);
        return carrierServiceCustomizeRules::getTotalNumberOfcarrierServiceCustomizeRulesFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM carrier_service_customize_rules cscr  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return carrierServiceCustomizeRules::getcarrierServiceCustomizeRulesListFromSql($sql);
    }

    /**
     * Get list of carrierServiceCustomizeRules items based on filter conditions
     *
     * @return array[carrierServiceCustomizeRules]
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

         $sql = "SELECT $columnName 
                FROM carrier_service_customize_rules cscr
                $userJoin 
                $where
                $sort
                ";
        if($debug)
            echo $sql;
        return carrierServiceCustomizeRules::getcarrierServiceCustomizeRulesListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000) {

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
				FROM carrier_service_customize_rules cscr
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return carrierServiceCustomizeRules::getcarrierServiceCustomizeRulesListFromSql($sql);
    }

       
    /**
     * Get count of carrierServiceCustomizeRules items based on filter conditions
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

}

// class
?>