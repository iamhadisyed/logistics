<?php

/**
 * PalletCarrierService Object
 *
 */
class PalletCarrierService extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'carrier_group_id' => 'number',
            'service_id' => 'number'
        );
        parent::__construct("pallet_carrier_service", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of pallet_carrier_service objects, using sql given
     *
     * @param string $sql
     */
    public static function getPalletCarrierServiceListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPalletCarrierServiceFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM pallet_carrier_service WHERE id = '".DbAccess3::escape($id)."'");
    }
    
    public static function deleteByCarrierGroupId($carrierGroupId) {
        if($carrierGroupId != "" && $carrierGroupId > 0)
            self::runQuery("DELETE FROM pallet_carrier_service WHERE carrier_group_id = '".DbAccess3::escape($carrierGroupId)."'");
    }
}
