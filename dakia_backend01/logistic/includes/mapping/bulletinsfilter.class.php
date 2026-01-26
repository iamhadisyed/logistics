<?php

class BulletinsFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";

    public function getList() {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->group_by))
            $sort = "GROUP BY " . $this->group_by;
        
        if (!empty($this->order_by))
            $sort .= "ORDER BY " . $this->order_by;

        
        $sql = "SELECT *
                    FROM bulletins b
                    $where
                    $sort 
                    order by date_created DESC LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return Bulletins::getBulletinsListFromSql($sql);
    }

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->order_by))
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT id, " . $fields . "FROM bulletins $where $sort ";

        t($sql, __METHOD__);

        return Bulletins::getBulletinsListFromSql($sql);
    }

    /**
     * Get count of consignment items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->group_by))
            $sort = "GROUP BY " . $this->group_by;
        
        if (!empty($this->order_by))
            $sort .= "ORDER BY " . $this->order_by;

        
        $sql = "SELECT *
                    FROM bulletins b
                    $where
                    $sort 
                    order by date_created DESC";
        
        
         t($sql, __METHOD__);
        $result = Bulletins::getBulletinsListFromSql($sql);
        //$result = $this->getList();
        return sizeof($result);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM bulletins $where $sort ";

        t($sql, __METHOD__);

        return Bulletins::getBulletinsListFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->order_by))
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM bulletins $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return Bulletins::getBulletinsListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    
    public function addLikeFilter($fieldName, $fieldValue) {
        $this->filter .= " AND $fieldName LIKE '%" . DbAccess3::escape($fieldValue) . "%'";
    }
}
