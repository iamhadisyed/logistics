<?php

class UserShoppingPlatforms extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' 					=> 'number',
            'shopping_platform_id' 	=> 'string',
            'reference' 			=> 'string',
            'site_url' 				=> 'string',
            'user_id' 				=> 'string',
			'api_key' 				=> 'string',
			'api_secrete' 			=> 'string',
			'status' 				=> 'string',
			'date_created'			=> 'datetime'
            );
        //
        parent::__construct("user_shopping_platforms", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of ShoppingPlatform objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserShoppingPlatformsListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

	public static function getUserApiKeyByShopingPlatFormId($shopPlatformId,$userId) {
		$sql = "SELECT * FROM user_shopping_platforms WHERE shopping_platform_id = '".DbAccess3::escape($shopPlatformId)."' AND user_id = '".DbAccess3::escape($userId)."' LIMIT 1";
		DbAccess3::runQuery($sql);
		$userShopPlatFormData = DbAccess3::getListFromSql(__CLASS__, $sql);
		if (sizeof($userShopPlatFormData) > 0) {
			return $userShopPlatFormData[0];
		} else {
			return null;
		}
	}
	public static function getShopingPlatformByPluginKey($pluginKey) {
		$sql = "SELECT * FROM shopping_platform WHERE plugin_key = '".DbAccess3::escape($pluginKey)."' LIMIT 1";
		DbAccess3::runQuery($sql);
		$shopPlatFormData = DbAccess3::getListFromSql(__CLASS__, $sql);
		if (sizeof($shopPlatFormData) > 0) {
			return $shopPlatFormData[0];
		} else {
			return null;
		}
	}
	 

}

// class