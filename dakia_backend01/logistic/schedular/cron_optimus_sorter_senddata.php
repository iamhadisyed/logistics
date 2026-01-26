<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'invoicetemplate.class'
],'invoices');
include_classes([
    'tcpdf'
],'3rdparty/tcpdf');

include_classes([
    'SFTP'
],'3rdparty/Net');
include_classes([
    'ftpimplicitssl'
],'3rdparty');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'notfoundrecord.class',
    'notfoundrecordfilter.class',
    'optimussorter.class',
    'optimussorterfilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
    'warehouse.class',
    'warehousefilter.class'
	
]);



error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(-1);


$consignmentFilter = new ConsignmentFilter();
$consignmentFilter->addFilter("     u.user_account_id in (2317, 2309,2310,4699,4700,4701,4702,4703,4704,4705,4706, 4778,4800, 4870, 4889, 2311)", "userfilter");
//$consignmentFilter->addFilter("     c.awb in ('JD0002210164116706','JD0002210164116708')", "consignmentfilter");
$consignmentFilter->addFilter("     c.optimus_sorter = '0' and c.shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "') ", "consignmentfilter");
$consignmentList = $consignmentFilter->getColumnList("c.id, c.awb, c.hawb, ua.user_account, s.code as service_code,s.name as service_name, s.carrier_id as carrier_id,c.other_routing_code, c.number_pieces, con.iso as country_iso_code, con.name as country_name, c.postcode, c.weight, c.routing_code_eur, c.optimus_sorter, c.destination_warehouse_id");
//id,awb,account,service_type,handling,number_pieces,country, country_iso_code,postcode, other_routing_code, weight
echo count($consignmentList);
if(count($consignmentList) >0)
{
        $optimus = new OptimusSorter();
        if($optimus->addConsignment($consignmentList))
        {
                $optimus->sendBookings();			
        }
}

?>