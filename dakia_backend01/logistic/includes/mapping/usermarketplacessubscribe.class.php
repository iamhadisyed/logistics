<?php

class UserMarketPlacesSubscribe extends DbAccess3
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
            'market_places_mapping_id' => 'number',
            'user_account_id' => 'number',
            'parent_id' => 'number',
            'date_time' => 'time',
            'active_date' => 'time',
            'expiry_date' => 'time',
            'status' => 'number',
            'is_delete' => 'number',
            'is_reject' => 'number',
            'pending_req' => 'number',
            'title' => 'text',
            'user_account' => 'text',
        );
        //
        parent::__construct("user_market_places_subscribe", 'id', $fieldList, $mixedCreator);
    }


    /**
     * Get list of user_market_places_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserMarketPlacesSubscribeListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    
    public static function getTotalNumberOfUserMarketPlacesSubscribeFromSql($sql)
    {

        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    /**
     * Delete user_market_places_mapping, using user id
     *
     * @param int $user_id
     */
    
}

// class