<?php
class AccountData extends DbAccess3 { /**
 * Construct
 *
 * @param id/array
 */

    public function __construct($mixedCreator = null) {
        $fieldList = array(
					'id' 								=> 'number',
					'supplier_account' 			=> 'string',
					'tracking_number' 			=> 'string',
					'supplier_code' 				=> 'string',
					'service_code' 			   => 'string',			
					'customer_order_number' 	=> 'string',
					'customer_reference_number'=> 'string',
					'collection_date' 			=> 'string',
					'delivery_name' 				=> 'string',
					'delivery_postcode' 			=> 'string',
					'weight' 						=> 'string',
					'supplier_price' 				=> 'string',
					'status' 						=> 'string',
					'user_account' 				=> 'string',
					'is_complete' 					=> 'string',
					'message' 						=> 'string',
					'owe_weight'					=> 'string',
					'remote_area'					=> 'string',
					'price' 							=> 'string',
					'customer_account'			=> 'string',
					'number_pieces'				=> 'string',
					'length'							=> 'string',
					'height'							=> 'string',
					'width'							=> 'string',
					'invoiced'						=> 'string',
					'invoice_type'					=> 'string',
					'vol_weight'					=> 'string',
					'cal_weight'					=> 'string',
					'privious_weight'				=> 'string',
					'batch_number' 				=> 'string',
					'country' 						=> 'string',
					'basic_charges' 				=> 'string',
					'fuel_charges' 				=> 'string',
					'additional_charges' 		=> 'string',
					'remote_area_charges' 		=> 'string',
					'extra' 							=> 'string',
					'ancillary_charges' 			=> 'string',
					'consignment_status' 		=> 'string',
                                        'vat_value' => 'string',
                                        'is_fuelcharges_include' => 'number',
                                        'vat_chargable' => 'number'
			
        );
        //
        parent::__construct("account_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>