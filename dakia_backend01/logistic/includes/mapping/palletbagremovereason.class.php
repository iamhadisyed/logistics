<?php

/**
 * PalletBagRemoveReason Object
 *
 */
class PalletBagRemoveReason extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'pallet_id' => 'number',
            'bag_id' => 'number',
            'reason' => 'string'
        );
        parent::__construct("pallet_bag_remove_reason", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of pallet_bag_remove_reason objects, using sql given
     *
     * @param string $sql
     */
    public static function getPalletBagRemoveReasonListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPalletBagRemoveReasonFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM pallet_bag_remove_reason WHERE id = '".DbAccess3::escape($id)."'");
    }
}
