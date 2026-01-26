<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");


include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class',
], 'general');
include_classes([
    'owesouthafrica.class',
], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'country.class',
    'countryfilter.class',
    'licenceplate.class',
    'services.class',
    'servicefilter.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'parcel.class',
    'parcelfilter.class',
]);
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$consignmentFilter = new ConsignmentFilter();
//$consignmentFilter->addFilterNew(" awb in ('OW50001606SA')");
$consignmentFilter->addFilterNew(" service_id = '315' and date_label_created >= '".strtotime(date("Y-m-d"))."'");
$consignmentList = $consignmentFilter->getListNew("*");
if(count($consignmentList) > 0){
    foreach($consignmentList as $consignment){
        $labellink = $consignment->getLabelFile();
        if(trim($labellink) != ""){
            
            if(strpos(strtolower($labellink), "zpl")){
               $pdffilename = SETTING_DIR_ASSETS."pdf/".str_replace("zpl", "pdf", $labellink);
                if(!file_exists($pdffilename)){
                    $owesouthafrica = new oweSouthAfrica();
                    $owesouthafrica->label($consignment);
                    echo "label created. <br />";
                }
            }
            
        }
    }
}
exit;
?>





