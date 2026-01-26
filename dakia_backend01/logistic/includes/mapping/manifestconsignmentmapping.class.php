<?php

// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class ManifestConsignmentMapping extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'manifestid' => 'number',
            'consignmentid' => 'number',
            'export_mawb' => 'number'
        );
        //
        parent::__construct("manifest_consignment_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getManifestListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function bulkDataInsert($rows, $debug= false) {
    
        $sql = array();

        foreach ($rows as $row) {
            $sql[] = "(" . DbAccess3::escape(@$row['manifestid']) . ",'"
                    . DbAccess3::escape(@$row['consignmentid']) . "','"
                    . DbAccess3::escape(@$row['export_mawb']) . "')";
        }
        
        $sqlQuery = 'INSERT INTO manifest_consignment_mapping (
						 manifestid, 
						 consignmentid,
						 export_mawb) 
						 VALUES ' . implode(',', @$sql);
        if($debug)
            echo $sqlQuery;
        return DbAccess3::runQuery($sqlQuery);
    }

}

// class
