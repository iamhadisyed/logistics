<?php 
/**
 * MAWB CLASS
 *
 */
class PricingBulkData extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'customer_account' => 'string',
            'tracking_number' => 'string',
            'hawb' => 'string',
            'basic_charges' => 'string',
            'fuel_charges' => 'string',
            'remote_area_charges' => 'string',
            'total_charges' => 'string',
            'user_account' => 'string',
            'status' => 'number',
            'is_complete' => 'number',
            'message' => 'string',
            'date_created' => 'datetime',
            'batch_number' => 'string',
            'ancillary_charges' => 'string',
            'reference' => 'string'
        );

        parent::__construct("pricing_bulk_data", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getPricingBulkDataListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPricingBulkDataFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}
