<?php

// get settings
class PaymentFilter {

    private $filter = array();
    private $order_by = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $groupBy = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?		
        if (!empty($this->filter))
            $where = "WHERE " . implode(" AND ", $this->filter);
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT *
                    FROM payment $where $sort";
        return Payment::getPaymentListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";

        if (!empty($this->filter))
            $where = "WHERE " . implode(" AND ", $this->filter);

        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT id, " . $fields . " FROM payment  $where $sort";
        return Payment::getPaymentListFromSql($sql);
    }

    public function addFilter($filter) {
        $this->filter[] = $filter;
    }

    public function addIsCompletedFilter($iscompleted) {
        $this->filter[] = "iscompleted = '" . DbAccess3::escape($iscompleted) . "'";
    }

    public function addCustomerIdFilter($customer_id) {
        $this->filter[] = "customer_id = '" . DbAccess3::escape($customer_id) . "'";
    }

    public function addAddedBy($added_by) {
        $this->filter[] = "added_by = '" . DbAccess3::escape($added_by) . "'";
    }

    public function addPaymentDate($paymentdate) {
        $this->filter[] = "paymentdate = '" . DbAccess3::escape($paymentdate) . "'";
    }

    public function addFieldFilter($colm, $value) {
        $this->filter [] = " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

    /*     * *
     * Oder by ID
     */

    public function AddOrderById($ascending = true) {
        $this->order_by = "id" . ($ascending ? "" : " DESC");
    }

    /*     * *
     * Oder by Customer ID
     */

    public function AddOrderByCustomerId($ascending = true) {
        $this->order_by = "customer_id" . ($ascending ? "" : " DESC");
    }

    /*     * *
     * Oder by Payment Date
     */

    public function AddOrderByPaymentDate($ascending = true) {
        $this->order_by = "paymentdate" . ($ascending ? "" : " DESC");
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

    public function getPagingList() {
        $where = "";
        if (!empty($this->filter))
            $where = "WHERE " . implode(" AND ", $this->filter);

        $groupBy = $this->groupBy;
        $sort = "";

        $sql = "SELECT * FROM  payment   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";

        t($sql, __METHOD__);

        return Payment::getPaymentListFromSql($sql);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter))
            $where = "WHERE " . implode(" AND ", $this->filter);

        $groupBy = $this->groupBy;
        $sort = "";
        $sql = "SELECT count(id) as total FROM payment  $where " . $groupBy . " Order by id asc";
        t($sql, __METHOD__);
        return Payment::getTotalNumberOfPaymentsFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

}

// class
?>