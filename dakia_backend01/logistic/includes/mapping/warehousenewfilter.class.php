<?php

/*
 * ServiceCountryTime Filter
 *
 */

class WarehouseNewFilter {

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
        
        if (trim($this->filter) != "")
            $where = "WHERE " . $this->filter;
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

//        $manifestjoin = "";
//        if ($this->join != "")
//            $manifestjoin = $this->join;

	    $checkJoin = "";
	    if ($this->join != ""){
		    $checkJoin = $this->join;
	    }
	    $joinColumnName = "";
	    if (strpos($checkJoin, 'countryJoin') !== false) { //search via tableAlias
		    $joinColumnName = ",countryJoin.name AS country_name,countryJoin.iso AS country_iso";
	    }

        $sql = "Select w.* $joinColumnName  from warehouse w $checkJoin $where $sort";

        t($sql, __METHOD__);

        $Warehouse = new WarehouseNew();

        return WarehouseNew::getWarehouseListFromSql($sql);
    }

    public function getWarehouseList() {
        $sql = "SELECT * FROM warehouse";

        $Warehouse = new WarehouseNew();

        return WarehouseNew::getWarehouseListFromSql($sql);
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

        $sql = "SELECT id, " . $fields . "FROM warehouse $where $sort ";

        t($sql, __METHOD__);

        return WarehouseNew::getWarehouseListFromSql($sql);
    }

    public function addFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND $name";
        else
            $this->filter .= " $name ";
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

        $sql = "SELECT count(*) as total FROM warehouse $where $sort ";

        t($sql, __METHOD__);

        return WarehouseNew::getTotalNumberOfWarehouseFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        
        if (trim($this->filter) != "")
            $where = "WHERE " . $this->filter;
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM warehouse $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);

        return WarehouseNew::getWarehouseListFromSql($sql);
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

    public function AddOrderBy($field, $ascending = true) {
        $this->order_by = $field . "" . ($ascending ? "" : " DESC");
    }

    
    
	public function addCountryJoin() {
		$this->join.= " JOIN `country` countryJoin  ON w.countryid = countryJoin.id ";
	}
	//get last warehouse by warehouse_code. Used in api
	public function getWarehouseByCode($warehouseCode) {
		$sql = "SELECT * FROM warehouse where warehouse_code = '". DbAccess3::escape($warehouseCode)."' order by id asc LIMIT 1";
		t($sql, __METHOD__);
		$Warehouse = new WarehouseNew();
		return WarehouseNew::getWarehouseListFromSql($sql);
	}
}
