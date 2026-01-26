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
    'optimussorter.class',
    'optimussorterfilter.class',
    'services.class',
    'servicefilter.class'
]);

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$consignment = new ConsignmentFilter();
$consignment->addFilter(" handling in (select code from services where account_owner = '387')");
$consignment->addFilter(" date_received = '" . date("Y-m-d") . "'");
$consignment->addFilter(" handling not like 'RTN%'");
$consignment->addFilter(" account not in (select user_account from user where parentid = '387' or id = '387')");
$consignment->addFieldNotFilter("awb", "");
//$consignment->addFieldFilter("date_submitted",date("Y-m-d"));
//$consignment->addawbFilterList("'JD0002255030674953'");
$list = $consignment->getColumnList("handling, account, hawb, reference, awb, company, contact, address_line_1, address_line_2, address_line_3, city, postcode, country, telephone, weight, number_pieces, description, date_submitted, date_received, date_booked, service_type", "5000");

if (count($list) > 0) {
    $csv = "";
    $cr = "\r\n";
    $code = "";

    foreach ($list as $consignment) {

        $code = $consignment->getHandling();

        $serv = new ServiceFilter();
        $serv->addSCodeFilter($code);
        $servList = $serv->getColumnList("supplier, name");
        if (count($servList) > 0) {
            $supplier = $servList[0]->getSupplier();
            $servicename = $servList[0]->getName();
        } else
            $servicename = $consignment->getServiceType();

        $userfilterClass = new UserAccountFilter();
        $userfilterClass->addUserAccountFilter($consignment->getAccount());

        $ulist = $userfilterClass->getColumnList("parentid");

        if (count($ulist) > 0) {
            $userfilterClass = new UserAccountFilter();
            $userfilterClass->addIdFilter($ulist[0]->getParentId());
            $user_list = $userfilterClass->getColumnList("user_account");

            if (count($user_list) > 0)
                $owner = $user_list[0]->getUserAccount();
        }

        $csv .= $consignment->getAccount() . ',';
        $csv .= date("Y-m-d", ($consignment->getDateReceived())) . ',';
        $csv .= removecommas($consignment->getHawb()) . ',';
        $csv .= removecommas($consignment->getReference()) . ',';
        $csv .= "=\"" . $consignment->getAwb() . "\"" . ",";
        $csv .= removecommas($consignment->getCompany()) . ',';
        $csv .= removecommas($consignment->getContact()) . ',';
        $csv .= removecommas($consignment->getAddressLine1()) . ',';
        $csv .= removecommas($consignment->getAddressLine2()) . ',';
        //;
        $csv .= removecommas($consignment->getAddressLine3()) . ',';
        $csv .= removecommas($consignment->getCity()) . ',';
        $csv .= removecommas($consignment->getPostCode()) . ',';
        $csv .= removecommas($consignment->getCountry()) . ',';
        $csv .= removecommas($consignment->getTelephone()) . ',';
        $csv .= removecommas($consignment->getWeight()) . ',';
        $csv .= removecommas($consignment->getNumberPieces()) . ',';
        $csv .= $consignment->getHandling() . ',';
        $csv .= $servicename . ',';
        $csv .= $owner . ',';
        $csv .= $cr;
    }

    $csvHeader = "Account, Date, HawbNo, Reference, TrackingNumber, Company, Contact, Address1, Address2, Address3, City, Postcode, Country, Telephone, Weight, NumberOfPieces, ServiceCode, ServiceName, OWNER";
    //$linkFileBulgaria = $this->getLinkFile($consignment_booking_type);


    $folder_path = SETTING_DIR_REMOTE . "_assets/supplier_data/service_vise_shipments";

    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0755, true);
    }

    $uniqueFileName = uniqid();

    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";
    $file_path1 = SETTING_DIR_REMOTE . "_assets/supplier_kaz/service_vise_shipments/" . $uniqueFileName . ".csv";

    $file_path = fopen($file_path, 'w');
    $file_path1 = fopen($file_path1, 'w');

    fwrite($file_path, $csvHeader . $cr . $csv);

    fwrite($file_path1, $csvHeader . $cr . $csv);

    fclose($file_path);
    fclose($file_path1);
}

function removecommas($data) {
    return str_replace(",", " ", $data);
}
