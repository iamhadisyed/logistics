<?php

/**
 * MarketPlacesLog Object
 *
 */
class MarketPlacesLog extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'market_place_id' => 'number',
            'user_account_id' => 'number',
            'last_status' => 'string',
            'current_status' => 'string',
            'date_request' => 'datetime'
        );
        parent::__construct("market_places_log", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of market_places_log objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlacesLogListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfMarketPlacesLogFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM market_places_log WHERE id = '".DbAccess3::escape($id)."'");
    }
    public function deleteBySql($sql) {
        self::runQuery($sql);
    }
    public static function deleteByBagId($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM market_places_log WHERE bag_id = '".DbAccess3::escape($id)."'");
    }
}
