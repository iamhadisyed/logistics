<?php

////////////////////////////////////////////////////
//
// Class for dealing with Invoices
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class DropoffUserLocation extends DbAccess3 {


    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'string',
            'service_id' => 'number',
            'country' => 'string',
            'user_id' => 'number',
            'companyname' => 'string',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
            'address_line_3' => 'string',
            'city' => 'string',
            'postcode' => 'string',
            'telephone' => 'string',
            'mon' => 'string',
            'tue' => 'string',
            'wed' => 'string',
            'thu' => 'string',
            'fri' => 'string',
            'sat' => 'string',
            'sun' => 'string',
            'lat'   => 'string',
            'lng'   => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'date_created' => 'datetime'
        );
        parent::__construct("dropoff_user_location", 'id', $fieldList, $mixedCreator);
    }
    public static function getListSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}

?>
