<?php

class ShoppingPlatform extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'title' => 'string',
            'page_key' => 'string',
            'description' => 'string',
            'translation_key' => 'string',
				'plugin_key' => 'string',
				'integration_logo' => 'string',
				'display_option' => 'string',
				'connect_url'	=> 'string',
				'active' => 'number'
			
            );
        //
        parent::__construct("shopping_platform", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of ShoppingPlatform objects, using sql given
     *
     * @param string $sql
     */
    public static function getShoppingPlatformListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

    /**
     * Get count of ShoppingPlatform objects, using sql given
     *
     * @param string $sql
     */
    public static function getShoppingPlatformCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

}

// class