<?php

////////////////////////////////////////////////////
//
// Class for dealing with Presort Destination warehouse
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class presortDestinationWarehouse extends DbAccess3 {


    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'string',
            'postcode' => 'string',
            'countryid' => 'number',
            'state' => 'string',
            'inboud_gateway' => 'string',
            'presort_warehouse' => 'string',
            'warehouse_id' => 'string',
            'bag_tag' => 'string',
            'date_created' => 'datetime'
        );
        parent::__construct("presort_destination_warehouse", 'id', $fieldList, $mixedCreator);
    }
    public static function getListSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}

?>
