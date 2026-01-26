<?php
class PricingBulkData extends DbAccess3 { /**
 * Construct
 *
 * @param id/array
 */

    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id'                        => 'number',
            'customer_account' 		=> 'string',
            'tracking_number' 		=> 'string',
            'hawb'                      => 'string',
            'basic_charges' 		=> 'number',			
            'fuel_charges'              => 'number',
            'remote_area_charges'       => 'number',
            'ancillary_charges'         => 'number',
            'total_charges' 		=> 'number',
            'user_account' 		=> 'string',
            'status'                    => 'string',
            'is_complete' 		=> 'string',
            'message'                   => 'string',
            'date_created' 		=> 'string',
            'batch_number' 		=> 'string'
        );
        parent::__construct("pricing_bulk_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>