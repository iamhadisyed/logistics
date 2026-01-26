<?php

class MarketPlacesAuthenticateField extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'field_name' => 'string',
            'field_value' => 'string',
            'market_places_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'is_delete' => 'number',
			'auto_generate_value' => 'number'
            );
        //
        parent::__construct("market_places_authenticate_field", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of MarketPlacesAuthenticateField objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlacesAuthenticateFieldListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

    /**
     * Get count of MarketPlacesAuthenticateField objects, using sql given
     *
     * @param string $sql
     */
    public static function getMarketPlacesAuthenticateFieldCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

}

// class