<?php

class UserShoppingPlatformMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'shopping_platform_id' => 'number',
            'user_id' => 'number',
            'auth_data' => 'text'
            );
        //
        parent::__construct("user_shopping_platform_mapping", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of UserShoppingPlatformMapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserShoppingPlatformMappingListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    /**
     * Delete User Shopping Platform, using user id
     *
     * @param int $user_id
     */
    public static function deleteUserShoppingPlatformMappingList($user_id) {
        $sql = "DELETE FROM user_shopping_platform_mapping WHERE user_id = '".DbAccess3::escape($user_id)."'";
        if(!empty($user_id) && $user_id > 0){
            return DbAccess3::runQuery($sql);
        }
    }    
}

// class