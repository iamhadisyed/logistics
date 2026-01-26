<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'tcpdf'
        ], '3rdparty/tcpdf');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
	'parcel.class',
    'parcelfilter.class',
	]);

$startFileCount = file_get_contents(SETTING_URL_ASSETS . 'counters/export_consignment_id.txt');
$consignment_filter = new ConsignmentFilter();

$consignmentDataArr = array();
$TotalNumberPieces = 0;
$TotalWeight = 0;
$iTotalRecords = 0;
$getZeroPriced = 0;

$dateFrom = date('Y-m-d 00:00:00');

$dateTo = date('Y-m-d 23:59:59');
//$dateCreatedWhere = "       ((c.date_created >= '" . DbAccess3::escape($dateFrom) . "') AND (c.date_created <= '" . DbAccess3::escape($dateTo) . "')) AND   c.id > $startFileCount";
$dateCreatedWhere = "        c.id > $startFileCount and shipment_status not in ('22', '11')";
$consignment_filter->addFilter($dateCreatedWhere, 'filter');
$userAccountId = 2321;
$userAccountArray = CustomerAccount::accountSubAccount($userAccountId, 0, true);
$ids = implode(",", $userAccountArray);
$consignment_filter->addFilterIn("     u.user_account_id", $ids, "userfilter");


$consignment_filter->setRowsPerPage(1000);
$consignment_filter->setOffset(0);
$consignmentObjs = $consignment_filter->getShipmentPagingListOpt(false);
$output = [];
$outputData = [];
$consignmentId = 0;
if (count($consignmentObjs) > 0) {

    foreach ($consignmentObjs as $consignmentData) {
        if ($startFileCount < $consignmentId || $consignmentId <= 0)
        {
            $consignmentId = $consignmentData->getId();
        }
        $consignmentarray = [];
        //$consignmentarray['consignment_id']    =	$consignmentData->getId();
        $consignmentarray['order_reference'] = $consignmentData->getHawb();
        $consignmentarray['tracking_number'] = $consignmentData->getAwb();
        $consignmentarray['mawb'] = $consignmentData->getMawb();
        $consignmentarray['company'] = $consignmentData->getCompany();
        $consignmentarray['contact'] = $consignmentData->getContact();
        $consignmentarray['address1'] = $consignmentData->getAddressLine1();
        $consignmentarray['address2'] = $consignmentData->getAddressLine2();
        $consignmentarray['address3'] = $consignmentData->getAddressLine3();
        $consignmentarray['city'] = $consignmentData->getCity();
        $consignmentarray['countrycode'] = $consignmentData->getCountryIsoCode();
        $consignmentarray['postcode'] = $consignmentData->getPostcode();
        $consignmentarray['telephone'] = $consignmentData->getTelephone();
        $consignmentarray['numberpieces'] = $consignmentData->getNumberPieces();
        $consignmentarray['weight'] = $consignmentData->getWeight();
        $consignmentarray['description'] = $consignmentData->getDescription();
        $consignmentarray['value'] = (($consignmentData->getValue() <= 0)?1:$consignmentData->getValue());
        $consignmentarray['currency'] = $consignmentData->getCurrency();
        $consignmentarray['sendername'] = $consignmentData->getSenderCompany();
        $consignmentarray['reference'] = $consignmentData->getReference();
        $consignmentarray['handlingcode'] = $consignmentData->getServiceCode();
        $consignmentarray['account'] = $consignmentData->getUserAccount();
        $consignmentarray['username'] = '';
        $consignmentarray['password'] = '';
        $consignmentarray['documentType'] = ($consignmentData->getIsDoc() == 1) ? 'DOC' : 'NONDOC';
        $consignmentarray['notes'] = $consignmentData->getNotes();
        $parcelData = [];
        $consignmentParcelData = $consignmentData->getParcels();
        if (count($consignmentParcelData) > 0) {
            foreach ($consignmentParcelData as $consignmentParcel) {
                $parcelDataItems = [];
                $parcelDataItems["weight"] = $consignmentParcel->getWeight();
                $parcelDataItems["length"] = $consignmentParcel->getLength();
                $parcelDataItems["width"] = $consignmentParcel->getWidth();
                $parcelDataItems["height"] = $consignmentParcel->getHeight();
                $parcelDataItems["tracking_number"] = $consignmentParcel->getTrackingNumber();
                $parcelData[] = $parcelDataItems;
            }
        }
        $consignmentarray['dimension'] = $parcelData;
        $consignmentarray['email'] = $consignmentData->getEmail();
        $consignmentarray['label'] = SETTING_URL_LABEL . $consignmentData->getLabelFile();

        $outputData[] = $consignmentarray;
    }
    $output["status"] = "success";
    $output["shipments"] = $outputData;
    $output["message"] = "";
        
    if ($startFileCount < $consignmentId) {
    $myfile = fopen(SETTING_DIR_ASSETS . 'counters/export_consignment_id.txt', "w") or die("Unable to open file!");
    fwrite($myfile, $consignmentId);
    fclose($myfile);
    
    $outputJasonData = json_encode($output);
    chdir('..');
    chdir('_assets/json_data/');
    $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
    $path = $currentDirecotryPath . date("Y-m-d", time()) . "/";
    if (!file_exists($path))
        @mkdir($path, 0777);
    chdir('../../schedular');
    $jsonDataFilename = SETTING_DIR_ASSETS . "json_data/" . date("Y-m-d") . "/" . time() . ".json";
    $remote_file_path = "shipment-data-in/" . time() . ".json";
    file_put_contents($jsonDataFilename, $outputJasonData);
    $ftp_conn = ftp_connect('213.246.110.102') or die("Could not connect to $SETTING_FTP_SITE_YODEL");
    $login = ftp_login($ftp_conn, 'china_json_data', '5Gd,24$@@WJR;QKR');
    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
    // upload file
    $isYodelUpload = ftp_put($ftp_conn, $remote_file_path, $jsonDataFilename, FTP_ASCII);
    if ($isYodelUpload) {
        echo "Successfully uploaded file.";
    } else {
        $message = 'There is an error while uploading the file to Yodel FTP.';
    }
    ftp_close($ftp_conn);
    
    
}
} else {
    $output["status"] = "error";
    $output["message"] = "No data found";
}

die;
?>