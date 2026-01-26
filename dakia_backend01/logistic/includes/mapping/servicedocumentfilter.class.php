<?php

// get settings
class serviceDocumentFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of serviceDocument items based on filter conditions
     *
     * @return array[serviceDocument]
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

        $sql = "SELECT count(*) as total FROM service_document ud $where $sort ";
        t($sql, __METHOD__);
        return serviceDocument::getTotalNumberOfserviceDocumentFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM service_document ud  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return serviceDocument::getserviceDocumentListFromSql($sql);
    }

    /**
     * Get list of serviceDocument items based on filter conditions
     *
     * @return array[serviceDocument]
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
                FROM service_document ud
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return serviceDocument::getserviceDocumentListFromSql($sql);
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
				FROM service_document ud
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return serviceDocument::getserviceDocumentListFromSql($sql);
    }

    /**
     * Get count of serviceDocument items based on filter conditions
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
    public function getServiceDoc($ServiceId) {
        $sql = 'SELECT '
                . '`service_document`.*, `document_type`.`document_name` AS `document_id`, `agent_data`.`agent_name` AS `agent_id` '
                . 'FROM `service_document`  '
                . 'LEFT JOIN '
                . '`document_type` ON `service_document`.`document_id` = `document_type`.`id` '
                . ' LEFT JOIN '
                . '`agent_data` ON `agent_data`.`id` = `service_document`.`agent_id`'
                . 'WHERE  `service_document`.`service_id` ="'.DbAccess3::escape($ServiceId).'" ';
        return serviceDocument::getserviceDocumentListFromSql($sql);
    }

}

// class
?>