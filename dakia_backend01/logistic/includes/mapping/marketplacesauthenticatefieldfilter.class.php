<?php

/*
 * MarketPlacesAuthenticateFieldFilter
 *
 */

class MarketPlacesAuthenticateFieldFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of MarketPlacesAuthenticateFieldFilter items based on filter conditions
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

        $sql = "SELECT * FROM market_places_authenticate_field $where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return MarketPlacesAuthenticateField::getMarketPlacesAuthenticateFieldListFromSql($sql);
    }

    public function getColumnList($fields='*', $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        if($fields != "*"){
            $column = $fields . ", id ";
        }else{
            $column = '*';
        }
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

        $sql = "SELECT " . $column . " 
				FROM market_places_authenticate_field
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return MarketPlacesAuthenticateField::getMarketPlacesAuthenticateFieldListFromSql($sql);
    }

   
    /**
     * Get count of MarketPlacesAuthenticateFieldFilter items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    
    public function DeleteRecordByMarketPlacesformId($id) {
        $sql = "DELETE FROM `market_places_authenticate_field` WHERE market_places_id = '" . DbAccess3::escape($id) . "'";
      return MarketPlacesAuthenticateField::getMarketPlacesAuthenticateFieldListFromSql($sql);  
    }

    public function addMarketPlacesIdMd5Filter($data_value) {
        $this->filter .= " AND MD5(market_places_id) = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addMarketPlacesIdFilter($marketPlacesId) {
        if(!empty($marketPlacesId))
            $this->filter .= " AND market_places_id = '" . DbAccess3::escape($marketPlacesId) . "'";
    }
    public function addIsNotDeletedFilter() {
        $this->filter .= " AND is_delete = '0'";
    }

    public function addIsDeletedFilter() {
        $this->filter .= " AND is_delete = '1'";
    }

}
