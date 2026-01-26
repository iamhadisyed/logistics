<?php

/*
 * Sample Filter
 *
 */

class UserShoppingPlatformMappingFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of Sample items based on filter conditions
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

      $sql = "SELECT * FROM user_shopping_platform_mapping	$where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return UserShoppingPlatformMapping::getUserShoppingPlatformMappingListFromSql($sql);
    }
   /**
     * Get count of Sample items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by  id
     */

    
    
    
    
    
    
    
    
    
    
    public function addUserIdFilter($data_value) {
        $this->filter .= " AND user_id = '" . DbAccess3::escape($data_value) . "'";
    }

    
    
    
    
    public function addShoppingPlatformMappingIdMd5Filter($data_value) {
        $this->filter .= " AND MD5(shopping_platform_id) = '" . DbAccess3::escape($data_value) . "'";
    }
   
}
