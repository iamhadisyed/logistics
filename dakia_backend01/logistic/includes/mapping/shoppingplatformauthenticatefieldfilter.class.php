<?php

/*
 * Sample Filter
 *
 */

class ShoppingPlatformAuthenticateFieldFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of ShoppingPlatformAuthenticateField items based on filter conditions
     *
     * @return array[Sample]
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

        $sql = "SELECT * FROM shopping_platform_authenticate_field $where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return ShoppingPlatformAuthenticateField::getShoppingPlatformAuthenticateFieldListFromSql($sql);
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

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", id 
				FROM shopping_platform_authenticate_field
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return ShoppingPlatformAuthenticateField::getShoppingPlatformAuthenticateFieldListFromSql($sql);
    }

    /**
     * Get count of ShoppingPlatformAuthenticateField items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    
  
    public function addShoppingPlatformIdMd5Filter($data_value) {
        $this->filter .= " AND MD5(shopping_platform_id) = '" . DbAccess3::escape($data_value) . "'";
    }

    public function addIsNotDeletedFilter() {
        $this->filter .= " AND is_delete = '0'";
    }

    public function addIsDeletedFilter() {
        $this->filter .= " AND is_delete = '1'";
    }

}
