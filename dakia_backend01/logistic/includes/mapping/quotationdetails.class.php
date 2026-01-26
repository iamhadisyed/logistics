<?php

////////////////////////////////////////////////////
//
// Class for dealing with QuotationDetails
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class QuotationDetails extends DbAccess3
{
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array( 
            'id'      			=> 'number',
            'shipping_from'             => 'number',
            'shipping_to'       	=> 'number',
            'carrier_id'   		=> 'number',
            'service_id'   		=> 'number',
            'account_id' 		=> 'number',
            'price_type' 		=> 'string',
            'pieces' 			=> 'number',
            'currency_id' 		=> 'string',
            'weight'      		=> 'string',
            'dimensions'      		=> 'string',
            'volumn_weight'             => 'string',
            'basic_charge'              => 'string',
            'vat_charge' 		=> 'string',
            'extra_charge' 		=> 'string',
            'sub_total' 		=> 'string',
            'discount'                  => 'string',
            'discount_type' 		=> 'string',
            'total_charge' 		=> 'string',
            'user_email' 		=> 'string',
            'remark' 			=> 'string',
            'from_city' 			=> 'string',
            'to_city' 			=> 'string',
            'from_postcode' 			=> 'string',
            'to_postcode' 			=> 'string',
            'conversionrate'            => 'string',
            'status' 			=> 'string',
            'pdf' 			=> 'string',
            'quotation_data' => 'string',
            'date_created' 		=> 'datetime',
            'added_by'                  => 'number',
            'date_updated'              => 'datetime',
            'updated_by'                => 'number'
            );

        parent::__construct("quotation_details", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getQuotationDetailsListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfQuotationDetailsFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteQuotationDetailsFromSql($sql) {
        self::runQuery($sql);
    }
	 
}
?>
