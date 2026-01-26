<?php

// get settings
class RemoteareasFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of Remoteareas items based on filter conditions
     *
     * @return array[Remoteareas]
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

       $sql = "SELECT count(*) as total FROM remoteareas ra $where $sort ";
        t($sql, __METHOD__);
        return Remoteareas::getTotalNumberOfRemoteareasFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM remoteareas ra  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return Remoteareas::getRemoteareasListFromSql($sql);
    }

    /**
     * Get list of Remoteareas items based on filter conditions
     *
     * @return array[Remoteareas]
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
                FROM remoteareas ra
                $userJoin 
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return Remoteareas::getRemoteareasListFromSql($sql);
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
				FROM remoteareas ra
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return Remoteareas::getRemoteareasListFromSql($sql);
    }

    /**
     * Get count of Remoteareas items based on filter conditions
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
    public function addIsDeletedFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    is_deleted = 'N'";
    }
    public function addFilterIn($field, $values) {
        if (trim($this->filter) != "") {
                $this->filter .= " AND ";
        }
        if (is_array($values)) { 
            $this->filter .= " " .$field ." IN (" . implode(",", $values) . ")";
        } else {
            $this->filter .= " " .$field . " IN (" . $values . ")";
        }
    }
}

// class
?>