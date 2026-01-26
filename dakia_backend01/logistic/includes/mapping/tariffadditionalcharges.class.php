<?php 
/**
 * UserAccountServiceCharges class
 * @package News Releases
 */
class TariffAdditionalCharges extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'tariff_id' => 'number',
            'consignment_charges_types_id' => 'number',
            'charge' => 'number',
            'charge_type' => ['enum' => ['fixed', 'percentage'],'fixed'],
			'zone_id' => 'number',
			'formula' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'user_account' => 'undefined',
            'title' => 'undefined',
            'name' => 'undefined'
        );

        parent::__construct("tariff_additional_charges", 'id', $fieldList, $mixedCreator);
    }

    public static function getTariffAdditionalChargesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfTariffAdditionalChargesFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteTariffAdditionalChargesFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
