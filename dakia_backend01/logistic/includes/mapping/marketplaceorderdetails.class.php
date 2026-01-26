<?php

class MarketPlaceOrderDetails extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'marketplace_order_id' => 'number',
            'marketplace_item_id' => 'string',
            'sku' => 'string',
            'title' => 'string',
            'quantity_purchased' => 'string',
            'asin' => 'string',
            'item_price' => 'string', 
            'currency' => 'string'
        );
        parent::__construct("marketplace_order_details", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of marketplace_order objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlaceOrderDetailsPlatformListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class