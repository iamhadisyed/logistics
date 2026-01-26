<?php

/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class warenpostGazetteer extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'alort' => 'string',
            'schluessel' => 'string',
            'hnrvon' => 'string',
            'hnrbis' => 'string',
            'status' => 'string',
            'hnr_1000' => 'string',
            'stverz' => 'string',
            'name_sort' => 'string',
            'name_umlauts' => 'string',
            'street_abbreviation' => 'string',
            'house_number_type' => 'string',
            'house_number_type' => 'string',
            'postcode' => 'string',
            'street_code' => 'string',
            'town_code' => 'string',
            'date_update' => 'datetime'
        );
        //
        parent::__construct("warenpost_gazetteer", 'id', $fieldList, $mixedCreator);
    }

    public static function getWarenpostGazetteerListFromSql($sql) {
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
