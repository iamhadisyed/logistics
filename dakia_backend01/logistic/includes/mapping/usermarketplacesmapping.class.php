<?php

class UserMarketPlacesMapping extends DbAccess3
{

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'market_places_id' => 'number',
            'user_account_id' => 'number',
            'auth_data' => 'text',
            'active' => 'number',
            'store_key' => 'string',
            'title' => 'string',
            'page_link' => 'undefined',
            'integration_logo' => 'undefined',
            'field_name' => 'undefined',
            'help_doc' => 'undefined',
            'plugin_key' => 'undefined',
            'user_token' => 'text',
            'date_created' => 'datetime',
            'active_date' => 'datetime',
            'expiry_date' => 'datetime',
            'user_parent_id' => 'number',
            'status' => ['enum' => ['pending','approved','expired','rejected']],
            'reason' => 'text',
        );
        
        parent::__construct("user_market_places_mapping", 'id', $fieldList, $mixedCreator);
    }


    /**
     * Get list of user_market_places_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserMarketPlacesformMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfMarketPlacesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    /**
     * Delete user_market_places_mapping, using user id
     *
     * @param int $user_id
     */
    public static function deleteUserMarketPlacesMappingList($user_id)
    {
        $sql = "DELETE FROM user_market_places_mapping WHERE user_account_id = '" . DbAccess3::escape($user_id) . "'";
        if (!empty($user_id) && $user_id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    public static function deleteSpecificUserMarketPlacesMappingList($user_id, $platfor_id)
    {
        $sql = "DELETE FROM user_market_places_mapping WHERE user_account_id = '" . DbAccess3::escape($user_id) . "' AND md5(market_places_id) = '" . DbAccess3::escape($platfor_id) . "'";
        if (!empty($user_id) && $user_id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    /**
     * Get count of user_market_places_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserMarketPlacesMappingCountFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class