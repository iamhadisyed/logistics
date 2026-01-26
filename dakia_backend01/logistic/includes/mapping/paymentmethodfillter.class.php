<?php

// get settings
class PaymentMethodFilter {

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
                    FROM paymentmethod
                    $where
                    Order by id ASC";
        return PaymentMethod::getPaymentMethodListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        
        if(!empty($this->filter))
            $where = "WHERE " . implode (" AND ", $this->filter);
        
        $sql = "SELECT id, " . $fields . " FROM paymentmethod  $where Order by id ASC";
        return PaymentMethod::getPaymentMethodListFromSql($sql);
    }

    public function addFilter($filter) {
        if ($filter != "")
            $this->filter[] = $filter;
    }
    public function addTitleFilter($title) {
        if ($filter != "")
            $this->filter[] = "isactive LIKE '%".DbAccess3::escape($title)."%'";
    }
    public function addIsActive($isactive) {
        if ($filter != "")
            $this->filter[] = "isactive = '".DbAccess3::escape($isactive)."'";
    }

    public function addFieldFilter($colm, $value) {
        
        $this->filter []= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
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