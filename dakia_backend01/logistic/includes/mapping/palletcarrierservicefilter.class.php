<?php

// get settings
class PalletCarrierServiceFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of PalletCarrierService items based on filter conditions
     *
     * @return array[PalletCarrierService]
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
        
        $sql = "SELECT count(pcs.id) as total FROM pallet_carrier_service $where $sort ";
        t($sql, __METHOD__);
        return PalletCarrierService::getTotalNumberOfPalletCarrierServiceFromSql($sql);
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
        
       $sql = "SELECT " . $columns . "  FROM pallet_carrier_service pcs   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return PalletCarrierService::getPalletCarrierServiceListFromSql($sql);
    }

    /**
     * Get list of PalletCarrierService items based on filter conditions
     *
     * @return array[PalletCarrierService]
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
        
        $sql = "SELECT $columnName
                FROM pallet_carrier_service pcs
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return PalletCarrierService::getPalletCarrierServiceListFromSql($sql);
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
				FROM pallet_carrier_service pcs
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return PalletCarrierService::getPalletCarrierServiceListFromSql($sql);
    }
    /**
     * Get count of PalletCarrierService items based on filter conditions
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
    public function getServiceFromGroup($groupId) {
        if($groupId > 0){
            $sql = "SELECT ser.`name` service_id  FROM `services` ser JOIN pallet_carrier_service pcs  ON pcs.`service_id` = ser.`id` WHERE pcs.`carrier_group_id`='".DbAccess3::escape($groupId) ."'";
            return PalletCarrierService::getPalletCarrierServiceListFromSql($sql);
        }
    }
}

// class
?>