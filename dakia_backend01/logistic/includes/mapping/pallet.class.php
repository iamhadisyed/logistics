<?php

// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class Pallet extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'palletno' => 'string',
            'date_created' => 'datetime',
            'userid' => 'number',
            'close' => 'number',
            'pallet_carrier_id' => 'string',
            'date_dispatch' => 'datetime',
            'dispatch_userid' => 'number',
            'type' => 'string',
            'manifestid' => 'number',
            'hub' => 'string',
            'is_active' => 'number',
            'type' => 'string',
            'comments' => 'string',
            'label' => 'string',
            'pallet_source_country_id' => 'number',
            'pallet_source_warehouse_id' => 'number',
            'pallet_destination_country_id' => 'number',
            'pallet_destination_warehouse_id' => 'number'
        );
        //
        parent::__construct("pallet", 'id', $fieldList, $mixedCreator);
    }

    public static function getPalletListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function bulkDataUpdate($palletNo) {
        $palletNoArr = "'" . implode("','", $palletNo) . "'";
        $sql = "update pallet set date_dispatch = '" . date("Y-m-d H:i:s") . "', dispatch_userid = 189, close='Y'  where palletno IN(" . $palletNoArr . ") ";

        //echo $sql;
        //die;


        return DbAccess3::runQuery($sql);
    }

  
    public static function getTotalNumberOfPalletFromSql($sql)
    {

        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}

// class
