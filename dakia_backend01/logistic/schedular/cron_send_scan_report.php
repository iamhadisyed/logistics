<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

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
]);
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$consignment = new ConsignmentFilter();
//$consignment->addStatusFilterNotIn(array('received'));
//		$consignment->addDateScannedRangeFilter(strtotime(date("Y-m-d", strtotime("-38 days"))), strtotime(date("Y-m-d", strtotime("-7 days"))));

$consignment->addDateScannedRangeFilter(strtotime("2017-05-01"), strtotime("2017-05-31"));
$consignment->addFilter(" account IN ('SED(UK)')");
$consignment->addFilter(" handling not like 'RTN%'");
$consignment->addFieldNotFilter("awb", "");
//$consignment->addFieldFilter("date_submitted",date("Y-m-d"));
//print_r($consignment);
//$consignment->addawbFilterList("'JD0002255030674953'");
$list = $consignment->getDistinctColumnList("id,hawb,awb,service_type,handling,number_pieces,country, country_iso_code,postcode, weight, date_scanned, remote_charges, vol_weight", 50000);
if (count($list) > 0) {
    $csv = "";

    $cr = "\r\n";
    foreach ($list as $consignment) {
        $csv .= "=\"" . removecommas($consignment->getHawb()) . "\"" . ',';
        $csv .= "=\"" . removecommas($consignment->getAwb()) . "\"" . ',';
        $csv .= removecommas($consignment->getPostCode()) . ',';
        $csv .= removecommas($consignment->getRemoteCharges()) . ',';
        $csv .= removecommas($consignment->getHandling()) . ',';
        $csv .= removecommas($consignment->getServiceType()) . ',';
        $csv .= removecommas($consignment->getWeight()) . ',';
        $csv .= removecommas($consignment->getVolWeight()) . ',';
        $csv .= removecommas($consignment->getNumberPieces()) . ',';
        $csv .= removecommas($consignment->getCountry()) . ',';
        $csv .= removecommas(date("Y-m-d H:i:s", strtotime($consignment->getDateScanned()))) . ',';
        $csv .= $cr;
    }
    $csvHeader = "Order Reference Number, Tracking Number, Postcode, Remote Charges, Serivce Code, Service Name, Weight, volumetric Weight, Number of Pieces,Country, Scan Date Time";
    $folder_path = SETTING_DIR_REMOTE . "_assets/selead_daily_scan_report";

    /* if (!file_exists($folder_path)) {
      mkdir($folder_path, 0777, true);
      } */

    $uniqueFileName = uniqid();

    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

    $file_path = fopen($file_path, 'w');

    fwrite($file_path, $csvHeader . $cr . $csv);

    // close file
    fclose($file_path);

    $to = 'mruga@oneworldexpress.com'; //international@posta.hu				
    //define the subject of the email
    $subject = ' RECEIVED SHIPMENT ON ' . date('d-m-Y');

    $headers .= "From: shabbir@oneworldexpress.com\r\nReply-To: shabbir@oneworldexpress.com";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\r\n";


    $message = 'Dear Sir/Madam,';             //add boundary string and mime type specification
    $message .= "<br><br>";
    $message .= 'Kindly find below todays Processed Shipments of SED(UK). If you have any query regarding any shipment please get back to us with the 2 working days otherwise it will go for billing.';
    $message .= "<br><br>";
    $message .= 'File Link : "https://www.oneworldexpress.co.uk/remote/_assets/selead_daily_scan_report/' . $uniqueFileName . '.csv"';
    $message .= "<br><br>";
    $message .= 'Kind Regards';
    $message .= "<br><br>";
    $message .= "ITSupport";
    //send the email
    $mail_sent = @mail($to, $subject, $message, $headers);
}

function removecommas($data) {
    return str_replace(",", " ", $data);
}
