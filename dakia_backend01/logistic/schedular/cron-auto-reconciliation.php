<?php
// get settings
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'currency.class',
    'currencyfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class' ,
    'consignmentcharges.class' ,
    'consignmentrelabelfilter.class' ,
    'consignmentrelabel.class' ,
    'consignmentchargesfilter.class' ,
    'consignmentchargeslog.class' ,
    'consignmentchargeslogfilter.class' ,
    'services.class' ,
    'servicefilter.class' ,
    'paymentshistory.class' ,
    'paymentshistoryfilter.class' ,
    'csvassignpricetemplate.class' ,
    'csvassignpricetemplatefilter.class' ,
    'carrier.class' ,
    'carrierfilter.class' ,
    'parcel.class' ,
    'parcelfilter.class' ,
    'supplierinvoices.class',
    'supplierinvoicesfilter.class',
    'reconciliationdata.class',
    'reconciliationdatafilter.class',
    'tariffs.class',
    'tariffsfilter.class'

]);
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
error_reporting(E_ALL);
ini_set('display_errors', '1');


$supplierInvoiceObj = new SupplierInvoicesFilter();
$supplierInvoiceObj->addFilter(['status' => 'pending'], '=');
$supplierInvoiceObj = $supplierInvoiceObj->getList();
if($supplierInvoiceObj) {
    
    foreach ($supplierInvoiceObj as $supplierInvoiceObjSingle) {
        $passData = [];
        $output = [];
        $id = $supplierInvoiceObjSingle->getId();
        $output = SupplierInvoices::reprocessFile($id);

        $emailObj = new SendEmail();
        $userObj = new User($supplierInvoiceObjSingle->getAddedBy());
        $to = $userObj->getEmail();
        $carrierObj = new Carrier($supplierInvoiceObjSingle->getCarrierId());
        $carrierNameLink = str_replace(" ","_",$carrierObj->getCarrier());
        $carrierName = $carrierObj->getCarrier();
        $dateFolder = $supplierInvoiceObjSingle->getDate();
        $invoiceCheckFile = RECONCILIATION_URL.$carrierNameLink.'/'.$dateFolder.'/'.$supplierInvoiceObjSingle->getInvoiceCheckFile();
        $reconciliationFile = RECONCILIATION_URL.$carrierNameLink.'/'.$dateFolder.'/'.$supplierInvoiceObjSingle->getReconciliationFile();
        $subject = "Reconciliation File Processed";
        $message = "The file you have uploaded for Reconciliation with following details has been processed:
        Invoice number: " . $supplierInvoiceObjSingle->getInvoiceNumber() . "
        Carrier: " . $carrierObj->getCarrier() . "
        Reconciliation File: <a href='" . $reconciliationFile . "'>View</a>
        Invoice Check File: <a href='" . $invoiceCheckFile . "'>View</a>
        ";
        $emailObj->supplierReconciliationEmail($to, $subject, $message);
        echo json_encode($output);
    }

}