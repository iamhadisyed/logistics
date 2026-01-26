<?php

class TariffDetail extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'tariff_name' => 'string',
            'status' => 'number',
            'tariff_type' => 'string',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'date_created' => 'datetime',
            'added_by' => 'number'
            
        );
        //
        parent::__construct("tariff_details", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getTariffDetailsListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}

// class