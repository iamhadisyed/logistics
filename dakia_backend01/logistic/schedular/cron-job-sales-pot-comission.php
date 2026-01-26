<?php

//	Important
//	Please change email before putting it live
//	Please remove the break function from line number 130 for full file compliation 
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'trace.class',
    'invoicedetail.class',
    'salespotcomission.class',
    'salespotcomissionfilter.class']);

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$connection = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD)
        or die("Could not connect to database server.");
mysqli_select_db($connection, SETTING_DB_DATABASE)
        or die("Could not select database");

$salesPotComission = NULL;

$salesPotComissionFilter = new SalesPotComissionFilter();
$salesPotComissionFilter->addIsNotPaidFilter();
$salesPotComissionList = $salesPotComissionFilter->getList();

if(count($salesPotComissionList) > 0){
    $salesPotComission = $salesPotComissionList[0];
}  else {
    $salesPotComission = new SalesPotComission();
    $salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
    $salesPotComission->setNoOfShipments(0);
    $salesPotComission->setComission(0);
    $salesPotComission->setIsPaid(0);
    $salesPotComission->save();
}

$sales_post_comission_id = $salesPotComission->getId();
$sales_post_comission = $salesPotComission->getComission();
$sales_post_consignments = $salesPotComission->getNoOfShipments();

$totalComission = $sales_post_comission;
$totalConsignments = $sales_post_consignments;

$sql = "SELECT 
            c.id,
            c.account,
            u.`check_list_sales_pot` AS awb,
            u.`sales_pot_percentage` AS hawb,
            u.`sales_pot_time_period` AS service 
          FROM
            consignment c 
            JOIN `user` u 
              ON u.`user_account` = c.`account` 
              AND u.`check_list_sales_pot` = 'YES' 
              AND u.`sales_pot_percentage` > 0 
          WHERE c.`IsInvoiced` = 'Y' 
            AND c.`invoice_type` = 'AUTO' 
            AND c.account = 'ITTEAM'
            AND c.sales_pot_id IS NULL 
          ORDER BY c.`id` DESC 
          LIMIT 10";

$consignmentList = Consignment::getConsignmentListFromSql($sql);
if(count($consignmentList) > 0){
    foreach($consignmentList as $consignment){
        $consignment_id = $consignment->getId();
        $sales_pot_percentage = $consignment->getHawb();
        $sales_pot_time_period = $consignment->getService();        
        $invoiceDetailSql = "SELECT basic_charges FROM invoice_detail WHERE consignment_id = '".$consignment_id."' ORDER BY id DESC LIMIT 1";        
        $invoiceDetail = InvoiceDetail::getListSql($invoiceDetailSql);
        if(count($invoiceDetail) > 0){
            $invoiceDetail = $invoiceDetail[0];            
            //echo "<pre>"; print_r($invoiceDetail); echo "</pre>";
            $basic_charges = $invoiceDetail->getBasicCharges();
            $comission = $basic_charges * $sales_pot_percentage / 100;
            echo $basic_charges ." = ".$comission . "<br />"; 
            $totalComission += $comission;
            $totalConsignments++;
            echo "UPDATE consignment SET sales_pot_id = '".$sales_post_comission_id."' WHERE id = '".$consignment_id."'";
            echo "<br />";
            DbAccess3::runQuery("UPDATE consignment SET sales_pot_id = '".$sales_post_comission_id."' WHERE id = '".$consignment_id."'");
        }
    }
}

//DbAccess3::runQuery("UPDATE sales_pot_comission SET no_of_shipments = '".$totalConsignments."', comission = '".$totalComission."', date_added = NOW() WHERE id = '".$sales_post_comission_id."'");
$salesPotComission->setComission($totalComission);
$salesPotComission->setNoOfShipments($totalConsignments);
$salesPotComission->setNoOfShipments($totalConsignments);
$salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
$salesPotComission->save();
echo "<b>Sales Pot Id:</b> ".$sales_post_comission_id."<br />";
echo "<b>Total Consignments:</b> ".$totalConsignments."<br />";
echo "<b>Total Comission:</b> ".$totalComission;



?>