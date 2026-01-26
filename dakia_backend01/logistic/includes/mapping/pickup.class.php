<?php

// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class PickupSmart extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'pickup_number' => 'string',
			'date_created' => 'datetime',
            'pickup_date' => 'datetime',
            'delivery_note' => 'string',
            'pick_up_pdf' => 'string',
            'collection_pdf' => 'string',
			'collection_address' => 'string',
			'address_line_1' => 'string',
			'address_line_2' => 'string',
			'address_line_3' => 'string',
			'city' => 'string',
			'country' => 'string',
			'postcode' => 'string',
			'active' => 'string'
			
        );
        //
        parent::__construct("pickup", 'id', $fieldList, $mixedCreator);
    }

    public static function getPickupListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class
