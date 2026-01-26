<?php

// get settings
class PaypalDumpFilter {

    private $filter = array();

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?		
        if(!empty($this->filter))
            $where = "WHERE " . implode (" AND ", $this->filter);

        $sql = "SELECT *
                    FROM paypal_dump
                    $where
                    Order by id DESC";
        return PaypalDump::getPaypalDumpListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        
        if(!empty($this->filter))
            $where = "WHERE " . implode (" AND ", $this->filter);
        
        $sql = "SELECT id, " . $fields . " FROM paypal_dump  $where Order by id DESC";
        return PaypalDump::getPaypalDumpListFromSql($sql);
    }

    public function addFilter($filter) {
        if ($filter != "")
            $this->filter[] = $filter;
    }
    public function addPaymentIdFilter($payment_id) {
        if ($filter != "")
            $this->filter[] = "payment_id = '".DbAccess3::escape($payment_id)."'";
    }
    public function addDateAdded($date_added) {
        if ($filter != "")
            $this->filter[] = "DATE(date_added) = '".DbAccess3::escape($date_added)."'";
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

}

// class
?>