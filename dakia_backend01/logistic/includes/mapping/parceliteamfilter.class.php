<?php

// get settings
class ParcelIteamFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of ParcelIteam items based on filter conditions
     *
     * @return array[ParcelIteam]
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

        $sql = "SELECT count(pi.id) as total FROM    $where $sort ";
        t($sql, __METHOD__);
        return ParcelIteam::getTotalNumberOfParcelIteamFromSql($sql);
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
        
       $sql = "SELECT " . $columns . "  FROM parcel_iteam pi   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ParcelIteam::getParcelIteamListFromSql($sql);
    }

    /**
     * Get list of ParcelIteam items based on filter conditions
     *
     * @return array[ParcelIteam]
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
        
       $sql = "SELECT $columnName $joinColumnName
                FROM parcel_iteam pi
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return ParcelIteam::getParcelIteamListFromSql($sql);
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
				FROM parcel_iteam pi
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return ParcelIteam::getParcelIteamListFromSql($sql);
    }

    
    /**
     * Get count of ParcelIteam items based on filter conditions
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
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    /***
    * Limit number of rows returned
    */
    public function setLimit ($lim)
    {
            $this->limit = $lim;
    }
}

// class
?>