<?php

/**
 * PalletCarierGroup Object
 *
 */
class PalletCarierGroup extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'group_name' => 'string',
            'carrier_id' => 'number'
        );
        parent::__construct("pallet_carier_group", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of pallet_carier_group objects, using sql given
     *
     * @param string $sql
     */
    public static function getPalletCarierGroupListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPalletCarierGroupFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public static function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM pallet_carier_group WHERE id = '".DbAccess3::escape($id)."'");
    }
}
