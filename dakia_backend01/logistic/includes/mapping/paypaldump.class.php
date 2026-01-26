<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * Rack - rack class
 * @package Ecommerce
 */
class PaypalDump extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'payment_id' => 'number',
            'dump' => 'string',
            'added_date' => 'date'
        );
        parent::__construct('paypal_dump', 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getPaypalDumpListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}

?>