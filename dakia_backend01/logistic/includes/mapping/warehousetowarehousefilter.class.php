<?php

/*
 * ServiceCountryTime Filter
 *
 */

class WarehouseToWarehouseFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 10;
    private $pageOffset = 0;

    const PAGE_SIZE = 20;

    /**
     * Get list of Service Country Time items based on filter conditions
     *
     */
    public function getList() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $manifestjoin = "";
        if ($this->join != "")
            $manifestjoin = $this->join;
        $sql = "Select * from warehouse_warehouse_ttime $manifestjoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
//        echo $sql;
//        die;
        t($sql, __METHOD__);
        $WarehouseToWarehouse = new WarehouseToWarehouse();

        return WarehouseToWarehouse::getWarehouseToWarehouseListFromSql($sql);
    }

    public function getOrderByList() {
        $sql = "SELECT * FROM warehouse_warehouse_ttime ORDER BY id DESC";

        $WarehouseToWarehouse = new WarehouseToWarehouse();

        return WarehouseToWarehouse::getWarehouseToWarehouseListFromSql($sql);
    }

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT id, " . $fields . "FROM warehouse_warehouse_ttime $where $sort ";

        t($sql, __METHOD__);

        return WarehouseToWarehouse::getWarehouseToWarehouseListFromSql($sql);
    }

    /**
     * Get count of items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
//        if ($this->order_by != "")
//            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM warehouse_warehouse_ttime $where $sort ";

        t($sql, __METHOD__);

        return WarehouseToWarehouse::getTotalWarehouseToWarehouseListFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        echo $sql = "SELECT * FROM warehouse_warehouse_ttime $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";

        t($sql, __METHOD__);

        return WarehouseToWarehouse::getWarehouseToWarehouseListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function addFieldLikeFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    public function addFieldFilter($colm, $value) {
        $this->filter .= " AND ";
         $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) ."'";
    }
    public function AddOrderBy($field, $ascending = true) {
        $this->order_by = $field . "" . ($ascending ? "" : " DESC");
    }

    public function addFilter($filter) {
        if (!empty($filter))
            $this->filter .= " AND " . DbAccess3::escape($filter);
    }
    
}
