<?php

class ProductMarketPlaceMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */

    public static $marketplace_config_classes_array = [
        'amazon' => 'Amazon',
        'api2cart' => 'Api2cart'
    ];
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'product_id' => 'number',
            'marketplace_id' => 'number',
            'category_id' => 'number',
            'message' => 'string',
            'status' => 'string',
            'user_id' => 'number'
        );

        parent::__construct("product_marketplace_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of market_places objects, using sql given
     *
     * @param string $sql
     */
    public static function getProductMarketPlaceMappingListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of MarketPlaces objects, using sql given
     *
     * @param string $sql
     */
    public static function getProductMarketPlaceMappingCountFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
