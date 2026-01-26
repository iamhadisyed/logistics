<?php

// get settings
//require_once("includes/settings/common.inc.php");

class PalletCarrier extends DbAccess3 {

   

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'name' => 'string',
            'service_id' => 'string'
            );
        //  
        parent::__construct("pallet_carrier", 'id', $fieldList, $mixedCreator);
    }
    
    public static function getPalletCarrierListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

	
 
  
}

// class
