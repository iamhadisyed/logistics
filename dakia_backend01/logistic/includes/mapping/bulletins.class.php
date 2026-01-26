<?php

class Bulletins extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'heading' => 'string',
            'description' => 'string',
            'date_created' => 'datetime',
            'date_submitted' => 'datetime',
            'created_by' => 'string'
        );
        //
        parent::__construct("bulletins", 'id', $fieldList, $mixedCreator);
    }

    public static function getBulletinsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}
