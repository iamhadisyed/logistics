<?php

// get settings
class TariffsPricingRulesFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of TariffsPricingRules items based on filter conditions
     *
     * @return array[TariffsPricingRules]
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
        $sql = "SELECT count(tpr.id) as total FROM tariffs_pricing_rules tpr $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return TariffsPricingRules::getTotalNumberOfTariffsPricingRulesFromSql($sql);
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

        $sql = "SELECT " . $columns . " FROM tariffs_pricing_rules tpr $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return TariffsPricingRules::getTariffsPricingRulesListFromSql($sql);
    }

    /**
     * Get list of TariffsPricingRules items based on filter conditions
     *
     * @return array[TariffsPricingRules]
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

        $sql = "SELECT $columnName
                FROM tariffs_pricing_rules tpr
                $checkJoin
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return TariffsPricingRules::getTariffsPricingRulesListFromSql($sql);
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
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . ", id 
				FROM tariffs_pricing_rules tpr
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return TariffsPricingRules::getTariffsPricingRulesListFromSql($sql);
    }

    
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

    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    
}

// class
?>