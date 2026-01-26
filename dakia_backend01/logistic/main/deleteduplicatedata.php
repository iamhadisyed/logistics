<?php

// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
        'iaddress.class'
        ]);



DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);


$duplicateList = TrackingData::GetDuplicateConList(1);

print_r($duplicateList);

//die;

$arrayCon = array();

foreach ($duplicateList as $dup) {

    if ($dup->getConsignmentId() > 0) {
        $arrayCon[] = $dup->getConsignmentId();
        TrackingData::DeleteDuplicateConList($dup->getConsignmentId());
    }
}

//mail("kazim@oneworldexpress.com, mruga@oneworldexpress.com", "delete consignment id list", implode(",", $arrayCon));


die;



