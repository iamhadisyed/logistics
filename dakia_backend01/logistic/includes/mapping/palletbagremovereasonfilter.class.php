<?php

// get settings
class PalletBagRemoveReasonFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of PalletBagRemoveReason items based on filter conditions
     *
     * @return array[PalletBagRemoveReason]
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

        $sql = "SELECT count(mpm.id) as total FROM  pallet_bag_remove_reason pbrr  $where $sort ";
        t($sql, __METHOD__);
        return PalletBagRemoveReason::getTotalNumberOfPalletBagRemoveReasonFromSql($sql);
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
        
       $sql = "SELECT " . $columns . "  FROM pallet_bag_remove_reason pbrr   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return PalletBagRemoveReason::getPalletBagRemoveReasonListFromSql($sql);
    }

    /**
     * Get list of PalletBagRemoveReason items based on filter conditions
     *
     * @return array[PalletBagRemoveReason]
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
                FROM pallet_bag_remove_reason pbrr
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return PalletBagRemoveReason::getPalletBagRemoveReasonListFromSql($sql);
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
				FROM pallet_bag_remove_reason pbrr
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return PalletBagRemoveReason::getPalletBagRemoveReasonListFromSql($sql);
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
				FROM pallet_bag_remove_reason pbrr
				$where
				$sort
				LIMIT 40000
				";
//      echo $sql;
        return PalletBagRemoveReason::getPalletBagRemoveReasonListFromSql($sql);
    }
    /**
     * Get count of PalletBagRemoveReason items based on filter conditions
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