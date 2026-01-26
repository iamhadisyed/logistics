<?php

// get settings
//require_once("includes/settings/common.inc.php");

class CarrierHubs extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $this->tablename = 'carrier_hubs';
        $this->pkey = 'id';
        $fieldList = array
            (
            'id' => 'number',
            'carrier_id' => 'number',
            'hub' => 'string',
            'routing_code' => 'string',
            'company' => 'string',
            'contact' => 'string',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
            'address_line_3' => 'string',
            'city' => 'string',
            'postcode' => 'string',
            'country_iso_code' => 'string'
        );
        //
        parent::__construct("carrier_hubs", 'id', $fieldList, $mixedCreator);
    }

    public static function getCarrierHubsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
