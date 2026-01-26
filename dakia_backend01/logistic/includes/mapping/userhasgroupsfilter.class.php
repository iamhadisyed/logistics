<?php

// get settings
class UserHasGroupsFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = '';

    /**
     * Get list of UserHasGroups items based on filter conditions
     *
     * @return array[UserHasGroups]
     */
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if (trim($this->filter) != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM userhasgroups uhg $where $sort ";
        t($sql, __METHOD__);
        return UserHasGroups::getTotalNumberOfUserHasGroupsFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM userhasgroups uhg  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return UserHasGroups::getUserHasGroupsListFromSql($sql);
    }

    /**
     * Get list of UserHasGroups items based on filter conditions
     *
     * @return array[UserHasGroups]
     */
    public function getList() {
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

        $sql = "SELECT * 
                FROM userhasgroups uhg 
                $checkJoin
                $where
                $sort
                ";
//        echo $sql; die;
        t($sql, __METHOD__);
        return UserHasGroups::getUserHasGroupsListFromSql($sql);
    }
    
     public function getColumnList($column = "*") {
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

        $sql = "SELECT  $column 
                FROM userhasgroups uhg 
                $checkJoin
                $where
                $sort
                ";
//        echo $sql; die;
        t($sql, __METHOD__);
        return UserHasGroups::getUserHasGroupsListFromSql($sql);
    }

    /**
     * Get count of UserHasGroups items based on filter conditions
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
    
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
	$this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    
}

// class
?>