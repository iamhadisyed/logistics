<?php

class ShoppingPlatformAuthenticateField extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'field_name' => 'string',
            'field_value' => 'string',
            'shopping_platform_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'is_delete' => 'number'
            );
        //
        parent::__construct("shopping_platform_authenticate_field", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of ShoppingPlatformAuthenticateField objects, using sql given
     *
     * @param string $sql
     */
    public static function getShoppingPlatformAuthenticateFieldListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

    /**
     * Get count of ShoppingPlatformAuthenticateField objects, using sql given
     *
     * @param string $sql
     */
    public static function getShoppingPlatformAuthenticateFieldCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

}

// class