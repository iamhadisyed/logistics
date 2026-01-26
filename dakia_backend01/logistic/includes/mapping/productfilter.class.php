<?php

// get settings
class ProductFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    const PAGE_SIZE = 20;

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {


        // has filter been configured?
        $where = "WHERE p.id <> '' ";
        $where .= $this->filter;
        if (trim($this->order_by) != '')
            $orderBy = 'Order by ' . $this->order_by;

       $sql = "SELECT * FROM products p $where " . $orderBy . "";
        return Products::getProductsListFromSql($sql);
    }

    
    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getProductList( $filterColumn =   'p.*') {


        // has filter been configured?
        $where = "WHERE p.id <> '' ";
        $where .= $this->filter;
        if (trim($this->order_by) != '')
            $groupBy = 'Order by ' . $this->order_by;

       $sql = "SELECT ".$filterColumn." FROM products p LEFT JOIN country c on p.country_id = c.id  $where " . $groupBy . "";
        return Products::getProductsListFromSql($sql);
    }
    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getColumnList($fields) {


        // has filter been configured?
        $where = "WHERE cl.id <> '' ";
        $where .= $this->filter;
        $groupBy = $this->order_by;

        $sql = "SELECT " . $fields . " FROM products p $where " . $groupBy . "";
        return Carrier::getProductsListFromSql($sql);
    }

    /**
     * Get count of consignment items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function addDateFilter($date_value1, $date_value2, $filterDate) {
        if ($filterDate == "submitted") {
            $this->filter .= " AND ";
            $this->filter .= "(p.added_date>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";

            $this->filter .= " AND ";
            $this->filter .= "p.added_date<='" . date('Y-m-d 23:59:59', strtotime($date_value2)) . "')";
        }
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM products p $where $sort ";

        t($sql, __METHOD__);

        return Products::getTotalProductsListFromSql($sql);
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
        
        if ($this->group_by != "")
            $groupby = "GROUP BY " . $this->group_by;

        echo $sql = "SELECT * FROM products p $where $groupby $sort LIMIT $this->pageOffset , $this->rowsPerPage";

        t($sql, __METHOD__);

        return Products::getProductsListFromSql($sql);
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
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    /*     * *
     * Order by HAWB
     */

    public function AddOrderBy($columnNamem, $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnNamem . " " . ($ascending ? "" : " DESC");
    }
    
    public function AddGroupBy($columnName) {
        if ($this->group_by != "")
            $this->group_by .= ", ";
        //
        $this->group_by .= $columnName;
    }

    public function addFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " " . $code . " ";
    }
    public function getProductFilter($desServiceType = "") {
        $sql = 'SELECT DISTINCT p.* FROM `products` p JOIN `customizedservicesrouting` ps ON p.`id` = ps.`product_id` ';
         
        if(!empty($desServiceType) && $desServiceType != "all")
            $sql .= '  JOIN `country` ON ps.`country_id` = `country`.id AND `country`.`region` = "'.DbAccess3::escape($desServiceType).'"  ';
        return Products::getProductsListFromSql($sql);
    }
}

// class
?>