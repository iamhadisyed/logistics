<?php

// get settings
class agentDocumentFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of userDocument items based on filter conditions
     *
     * @return array[userDocument]
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

        $sql = "SELECT count(*) as total FROM agent_document ud $where $sort ";
        t($sql, __METHOD__);
        return agentDocument::getTotalNumberOfagentDocumentFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM agent_document ud  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return agentDocument::getagentDocumentListFromSql($sql);
    }

    /**
     * Get list of userDocument items based on filter conditions
     *
     * @return array[userDocument]
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
                FROM agent_document ud
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return agentDocument::getagentDocumentListFromSql($sql);
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
				FROM agent_document ud
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return agentDocument::getagentDocumentListFromSql($sql);
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
				FROM agent_document
				$where
				$sort
				LIMIT 40000
				";
//      echo $sql;
        return agentDocument::getagentDocumentListFromSql($sql);
    }
    /**
     * Get count of userDocument items based on filter conditions
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
    public function getAgentDoc($agentId) {
        $sql = 'SELECT `agent_document`.*, `document_type`.`document_name` AS `document_id` FROM `agent_document`  LEFT JOIN `document_type` ON `agent_document`.`document_id` = `document_type`.`id` WHERE  `agent_document`.`agent_id` ="'.DbAccess3::escape($agentId).'" ';
        return agentDocument::getagentDocumentListFromSql($sql);
    }

}

// class
?>