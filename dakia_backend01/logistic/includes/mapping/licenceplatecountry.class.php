<?php

// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class LicencePlateCountry extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'licence_plate_id' => 'number',
            'country_id' => 'number',
            'range_name' => 'string',
            'range_start' => 'number',
            'range_end' => 'number',
            'next_number' => 'number',
            'increment_date' => 'datetime',
            'prefix' => 'string',
            'sufix' => 'string',
            'date_created' => 'datetime',
            'date_updated' => 'datetime',
            'addedby' => 'number',
            'updatedby' => 'number',
            'countryname' => 'undefined'
            
        );
        //
        parent::__construct("licence_plate_country", 'id', $fieldList, $mixedCreator);
    }

    public static function getLicencePlateCountryListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    

}

// class
