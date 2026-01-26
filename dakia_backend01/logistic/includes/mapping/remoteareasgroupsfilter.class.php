<?php

// get settings
class RemoteareasGroupsFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
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
        
        $sql = "SELECT count(rg.id) as total FROM remoteareas_groups rg $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return RemoteareasGroups::getTotalNumberOfRemoteareasGroupsFromSql($sql);
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
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id, c.logo AS carrier_logo";
        }
        
        
       $sql = "SELECT " . $columns . " $joinColumnName FROM remoteareas_groups rg  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return RemoteareasGroups::getRemoteareasGroupsListFromSql($sql);
    }

    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
     */
    public function getList($columnName = "*") {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4)." AND is_deleted = 'N'";
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id, c.logo AS carrier_logo";
        }
        //8779133
        $sql = "SELECT $columnName $joinColumnName
                FROM remoteareas_groups rg
                $checkJoin 
                $where
                $sort";
        t($sql, __METHOD__);
        return RemoteareasGroups::getRemoteareasGroupsListFromSql($sql);
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

        $sql = "SELECT " . $fields . ", id
				FROM remoteareas_groups rg 
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return RemoteareasGroups::getRemoteareasGroupsListFromSql($sql);
    }

    /**
     * Get count of RemoteareasGroups items based on filter conditions
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

        $this->filter .=  " ".$filterVal." ";
    }
    public function addCarrierJoin() {
        $this->join.= " JOIN `carrier` c  ON rg.carrier_id = c.id ";
    }
   
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addIsDeletedFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    rg.is_deleted = 'N'";
    }
}

// class
?>