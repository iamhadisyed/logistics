<?php

// get settings
//require_once("includes/settings/common.inc.php");

class UserAccountAdditionalContacts extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $this->tablename = 'user_account_additional_contacts';
        $this->pkey = 'id';
        $fieldList = array
            (
            'id' => 'number',
            'user_account_id' => 'number',
            'title' => 'string',
            'name' => 'string',
            'email' => 'string',
            'contact_type' => 'string',
            'phone' => 'string',
            'user_name' => 'undefined',
            'user_account' => 'undefined',
            'date_created' => 'undefined',
            'added_by' => 'number'
        );
        //
        parent::__construct("user_account_additional_contacts", 'id', $fieldList, $mixedCreator);
    }

    public static function getUserAccountAdditionalContactsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfUserAccountAdditionalContactsFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

}

// class
