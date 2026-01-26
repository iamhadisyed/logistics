<?php

// get settings
class carrierDocumentFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of carrierDocument items based on filter conditions
     *
     * @return array[carrierDocument]
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

        $sql = "SELECT count(*) as total FROM carrier_document ud $where $sort ";
        t($sql, __METHOD__);
        return carrierDocument::getTotalNumberOfcarrierDocumentFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM carrier_document ud  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return carrierDocument::getcarrierDocumentListFromSql($sql);
    }

    /**
     * Get list of carrierDocument items based on filter conditions
     *
     * @return array[carrierDocument]
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
                FROM carrier_document ud
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return carrierDocument::getcarrierDocumentListFromSql($sql);
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
				FROM carrier_document ud
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return carrierDocument::getcarrierDocumentListFromSql($sql);
    }

    /**
     * Get count of carrierDocument items based on filter conditions
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
//        $this->filter .= "    " . DbAccess3::escape($filterVal) . " ";
    }
    public function getCarrierDoc($CarrierId) {
        $sql = 'SELECT '
                . '`carrier_document`.*, `document_type`.`document_name` AS `document_id`'
                . 'FROM `carrier_document`  '
                . 'LEFT JOIN '
                . '`document_type` ON `carrier_document`.`document_id` = `document_type`.`id` '
                
                . 'WHERE  `carrier_document`.`carrier_id` ="'.DbAccess3::escape($CarrierId).'" ';
        return carrierDocument::getcarrierDocumentListFromSql($sql);
    }

}

// class
?>