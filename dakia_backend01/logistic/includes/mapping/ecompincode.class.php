<?php

/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class EcomPinCode extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'city' => 'string',
            'state' => 'string',
            'city_type' => 'string',
            'active' => 'string',
            'route' => 'string',
            'date_of_discontinuance' => 'string',
            'state_code' => 'string',
            'pincode' => 'string',
            'city_code' => 'string',
            'dccode' => 'string',
        );
        //
        parent::__construct("ecom_pincodes", 'id', $fieldList, $mixedCreator);
    }

    public static function getEcomPinCodesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getListFromSql($sql, $val_field, $key_field = "") {
        $list = array();
        $rs = DbAccess3::runQuery($sql);
        //
        while ($row = mysqli_fetch_assoc($rs)) {
            if ($key_field == "") {
                $list[] = $row[$val_field];
            } else {
                $list[$row[$key_field]] = $row[$val_field];
            }
        }
        return $list;
    }

}
