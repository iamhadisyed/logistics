<?php

// get settings
class WarehouseFilter {

    private $order_by = "";
    private $pageOffset = "";
    private $rowsPerPage = "";
    private $filter = "w.is_deleted = 0";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?		
        $where = "WHERE " . $this->filter;

        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT w.*
                    FROM warehouse w                    
                    $where $sort ";
        return Warehouse::getWarehouseListFromSql($sql);
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

        $sql = "SELECT count(*) as total FROM warehouse w $where $sort ";

        t($sql, __METHOD__);

        return Warehouse::getTotalNumberOfWarehouseFromSql($sql);
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


        $sql = "SELECT * FROM warehouse w $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        return Warehouse::getWarehouseListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM warehouse w $where Order by w.warehouse_name";
        return Warehouse::getWarehouseListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function AddOrderByWarehouse($ascending = true) {
        //if ($this->order_by != "") $this->order_by = "";
        //
		$this->order_by = " warehouse_name " . ($ascending ? "" : " DESC");
    }

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
    public static function checkColExsit($columnName,$columnVal,$curId) {
        $sql = "SELECT id FROM `warehouse` w WHERE `w`.".$columnName."  = '".DbAccess3::escape($columnVal)."' AND id != '".DbAccess3::escape($curId)."' ";
        return Warehouse::getWarehouseListFromSql($sql);
    }
    public static function getWarehouseFromUserAccount($accountArr) {
        $whereIn = "IN (" . implode(",", $accountArr) . ")";
        $sql = "SELECT 
                    DISTINCT w.`warehouse_name` ,  
                      w.id
                    FROM
                      `user` u 
                      JOIN warehouse w 
                        ON w.`id` = u.`warehouse_id` 
                    WHERE u.`user_account_id`".$whereIn." 
                    AND w.is_active = 1 
                    AND w.is_deleted = 0 
                    AND u.`active_flag` = 1 
                    AND u.`is_deleted` = 0 ";
        return Warehouse::getWarehouseListFromSql($sql);
    }
}

// class
?>