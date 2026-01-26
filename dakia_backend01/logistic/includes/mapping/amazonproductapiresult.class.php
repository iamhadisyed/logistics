<?php

class AmazonProductApiResult extends DbAccess3 {

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
            'feed_type' => 'string',
            'feed_id' => 'number',
            'feed_message' => 'string',
            'feed_status' => 'string',
            'user_id' => 'number',
            'added_at' => 'string'
        );
        //
        parent::__construct("amazon_product_api_result", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of market_places objects, using sql given
     *
     * @param string $sql
     */
    public static function getAmazonProductApiResultListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of MarketPlaces objects, using sql given
     *
     * @param string $sql
     */
    public static function getAmazonProductApiResultCountFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
