<?php
/*
 * Cron Job to create invoices on daily basis on VIVA SFTP 
 * Created By: Mruga
 */
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'tcpdf',
        ], '3rdparty/tcpdf');
include_classes([
    'SFTP'
        ], '3rdparty/Net');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
   
]);
$date = date("Y-m-d");

$sql = "SELECT 
  *
FROM
    consignment c
    WHERE
        c.user_id IN (SELECT 
            id
        FROM
            user
        WHERE
            user_account_id IN( select id from user_account where parentid = '2319'))
        AND c.date_created >= '".$date."'";
$consignmentList = Consignment::getConsignmentListFromSql($sql);

$invoicePath = "../_assets/proforma_invoice/viva_invoices/";
$invoicePathProcessed = "../_assets/proforma_invoice/viva_invoices/processed/";
if (!file_exists($invoicePath))
    @mkdir($invoicePath, 0777, true);
if (!file_exists($invoicePathProcessed))
    @mkdir($invoicePathProcessed, 0777, true);


/*
 * Create PDF Files
 */
if (count($consignmentList) > 0) {
    $pdfFileName = "";
    foreach ($consignmentList as $consignResult) {
        $country = new Country($consignResult->getCountryId());
        if($country->getIso() != "GB"){
            $pdfFileName = $consignResult->getAwb() . ".pdf";
            if(!file_exists($invoicePathProcessed.$pdfFileName)){
            if(!file_exists($invoicePath.$pdfFileName)){
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetX(1.0);
                $glOrderPdf = new ProformaInvoice($pdf);
                $glOrderPdf->setInvoiceFolder("proforma_invoice/viva_invoices");
                $glOrderPdf->setInvoiceName($consignResult->getAwb());
                $glOrderPdf->setIsReturnUrl(false);
                $result = $glOrderPdf->AddHTML($consignResult,'',true, false, true);
            }
        }
        }
    }
}
$files = scandir($invoicePath);
if(count($files) > 0){
    $url = "orbitran.wqxs.com";
    $username = "oneworld";
    $password = "0ne29493*";
    $ftpconnection = new Net_SFTP($url);
    $loginStatus =  $ftpconnection->login($username, $password);
    if($loginStatus){
        foreach($files as $invoice){
            if($invoice == "." || $invoice == ".." || $invoice == "processed")
                continue;
            $remoteFilePath = "/invoice/".$invoice;
            $isUpload = $ftpconnection->put($remoteFilePath, $invoicePath.$invoice, NET_SFTP_LOCAL_FILE);
            if($isUpload)
                rename($invoicePath.$invoice, $invoicePathProcessed.$invoice);
        }
    }
    else
    {
        mail("mruga@oneworldexpress.com","VIVA INVOICE LOGIN FAILED", "VIVA INVOICE LOGIN FAILED");
    }
}        

die;

?>