<?php

// get settings
class RackFilter {

    private $filter = "r.is_deleted = 0";
    private $order_by = null;

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?		
        $where = "WHERE " . $this->filter;

        $sql = "SELECT r.*
                    FROM rack r                    
                    $where
                    Order by r.id DESC";
        return Rack::getRackListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM rack r $where Order by id DESC";
        return Rack::getRackListFromSql($sql);
    }
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM rack r $where $sort ";

        t($sql, __METHOD__);

        return Rack::getTotalNumberOfRacksFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE  " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT * FROM rack r $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        return Rack::getRackListFromSql($sql);
    }
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    public function addRackIdFilter($filter) {
        if($filter != "")
            $this->filter .= " AND warehouse_id = " . $filter;
    }
    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    public function AddOrderBy($fieldName, $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";

        $this->order_by = " " . $fieldName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
    }

    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= $account_number;
        //echo $this->filter;
        //exit;
    }
}

// class
?>