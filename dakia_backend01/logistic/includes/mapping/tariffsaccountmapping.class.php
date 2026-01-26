<?php

// get settings
//require_once("includes/settings/common.inc.php");

class TariffsAccountMapping extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'tariff_id' => 'number',
            'user_account_id' => 'number',
            'added_date' => 'datetime',
            'added_by' => 'number',
            'tariff_name' => 'undefined',
            'user_account' => 'undefined',
            'service_id' => 'undefined'
        );
        //
        parent::__construct("tariffs_account_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getTariffsAccountMappingListFromSql($sql) {
//		echo $sql;
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
