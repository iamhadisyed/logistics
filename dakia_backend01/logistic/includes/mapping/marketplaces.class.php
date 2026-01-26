<?php

class MarketPlaces extends DbAccess3 {

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
            'title' => 'string',
            'description' => 'string',
            'is_active' => 'number',
            'page_link' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'is_featured' => 'bit',
            'is_api2cart' => 'bit',
            'updated_date' => 'datetime',
            'help_doc' => 'string',
            'documentation_cover_image' => 'string',
            'documentation_title' => 'string',
            'class_name' => 'string',
			'translation_key' => 'string',
            'is_delete' => 'number',
			'integration_logo' => 'string',
			'manual_link'	=> 'string',
			'plugin_key' => 'string',
			'parent_id'	=> 'number',
			'integration_type' => 'number',
			'channel_type'	=> 'number',
			'country_id'	=> 'number',
			'last_sync' => 'datetime',
			'auth_data' => 'undefined',
        );
        //
        parent::__construct("market_places", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of market_places objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlacesPlatformListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of MarketPlaces objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlacesPlatformCountFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
