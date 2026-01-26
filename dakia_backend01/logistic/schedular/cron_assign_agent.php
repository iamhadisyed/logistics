<?php

//error_reporting(E_ALL & ~(E_NOTICE|E_WARNING));
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class'
]);
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$handling = array(
    '11EUR',
    '11STDDOM',
    '12',
    '19DEDR',
    '19EURDE',
    '19EURDPD',
    '19EURDPDDE',
    '19EURPL',
    '1H',
    '1HS',
    '2',
    '24',
    '2H',
    '2S',
    '3H',
    '3HPA',
    '3HS',
    'A|A',
    'A|H',
    'AIRBORNE',
    'AMXEUR',
    'AMXEURDOX',
    'AMXINT',
    'AMXINTDDP',
    'AMXINTDOX',
    'ASE',
    'ASEDE',
    'B|K',
    'BPO',
    'CORREOS',
    'CTT',
    'CURA',
    'DAC',
    'DHLDE',
    'DOM',
    'DOX',
    'ECONOMY',
    'ECX',
    'ESU',
    'EURECON',
    'EURPRIOR',
    'GLSNLSTD',
    'GLSPLSTD',
    'INPOST',
    'ITUKR',
    'KB',
    'KBUTR',
    'NXTCOR',
    'OCS',
    'PILOT',
    'PRIORITY',
    'REGHUNUTREUR',
    'REGHUNUTRINT',
    'REGPOST',
    'REGPOSTHUNEUR',
    'REGPOSTHUNINT',
    'REGPOSTINT',
    'REGPOSTIREEUR',
    'RM|L',
    'RM1',
    'RM2',
    'RMEURS',
    'RMEURTS',
    'RMINTRS',
    'RMINTS',
    'SPR',
    'TINA',
    'TP2',
    'TP2S',
    'TRANSNG',
    'WPX',
    'WPXDDP',
    'YSWS',
    'YSWSPALLET');

foreach ($handling as $servicecode) {
    $c_filter = new ConsignmentFilter();
    $total_record = $c_filter->getShipmentWithoutAgent($servicecode);
    if (count($total_record) > 0) {
        if ($total_record[0]->getId() > 0) {
            echo $servicecode;
            $agentId = ConsignmentFilter::getAgentId(trim($servicecode));
            if (count($agentId) > 0) {
                echo $agent_id = $agentId[0]->getAgentId();
                $c_filter = new ConsignmentFilter();
                $clist = $c_filter->updateShipmentWithoutAgent($servicecode, $agent_id);
                break;
            }
        } else {
            echo $servicecode . "continue<br>";
            continue;
        }
    }
}
?>





