<?php

/**
 * MAWB CLASS
 *
 */
class ConsignmentChargesTypes extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id'          => 'number',
            'title' => 'string',
            'charges_key' => 'string',
            'charge_type' => ['enum' => ['both','customer','agent'],'default' => 'both'],
            'apply_per_kg' => 'bit',
            'is_replace_charges' => 'bit',
            'is_extra_charge' => 'bit',
            'is_vat' => 'bit',
            'status' => 'number',
            'is_expirable' => 'number',
            'is_delete' => 'bit',
            'has_account_default_value' => 'bit',
            'added_date' => 'datetime',
            'added_by' => 'number',
            'updated_date' => 'datetime',
            'apply_by' => ['enum' => ['fixed','country','postcode','state'],'default' => 'fixed'],
            'updated_by' => 'number',
            'cost' => 'undefined'
        );

        //
        parent::__construct("consignment_charges_types", 'id', $fieldList, $mixedCreator);
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
    public static function getConsignmentChargesTypesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfConsignmentChargesTypesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}
