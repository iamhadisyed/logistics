<?php

class ForgetPasswordRequest extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'user_name' => 'string',
            'ip_address' => 'string',
            'user_agent' => 'string',
            'token' => 'string',
            'date_created' => 'string',
            'date_expire' => 'string',
            'is_expire' => 'number'
            );
        //
        parent::__construct("forget_password_request", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of ForgetPasswordRequest objects, using sql given
     *
     * @param string $sql
     */
    public static function getForgetPasswordRequestListFromSql($sql, $debug = false) {


        return DbAccess3::getListFromSql(__CLASS__, $sql, $debug);
    }
    

    /**
     * Get count of ForgetPasswordRequest objects, using sql given
     *
     * @param string $sql
     */
    public static function getForgetPasswordRequestCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
}

// class