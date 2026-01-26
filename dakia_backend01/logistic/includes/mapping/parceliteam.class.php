<?php

/**
 * ParcelIteam Object
 *
 */
class ParcelIteam extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'parcel_id' => 'number',
            'iteam_name' => 'string',
            'iteam_weight' => 'string',
            'iteam_weight_unit' => 'string',
            'iteam_value' => 'number',
            'iteam_quantity' => 'number',
            'iteam_country_id' => 'number',
            'iteam_description' => 'number'
        );
        parent::__construct("parcel_iteam", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of parcel_iteam objects, using sql given
     *
     * @param string $sql
     */
    public static function getParcelIteamListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfParcelIteamFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM parcel_iteam WHERE id = '".DbAccess3::escape($id)."'");
    }
}
