<?php
include_classes([
        'paymentshistory.class',
        'paymentshistoryfilter.class',
        'consignmentcharges.class',
]);
$accounts = []; 
$this->user = SessionManager::getUser();
$allouedAcccounts = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
$accounts[] = $this->user->getUserAccountId();
$userFilterSubObjs = new UserAccountFilter();
$userFilterSubObjs->addFieldFilter('id', $this->user->getUserAccountId());
$dataSubAccountOnly = $userFilterSubObjs->getList();
$userFilterSubObj = new UserAccountFilter();
$userFilterSubObj->addFieldFilter('parentid', $this->user->getUserAccountId());
$dataSubAccount = $userFilterSubObj->getList();
$ImediateAccount = [];
if(!empty($dataSubAccount)){
    foreach($dataSubAccount as $data){
        $ImediateAccount[] = $data->getId();
    }
}
// Postpaid Customer 
if(!empty($dataSubAccountOnly) && $dataSubAccountOnly[0]->getIsPrepaid() == '0'){
?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup text-center">
                    <?php 
                        $totalShipment= 0;
                        $totalOfALLShipment = 0;
                        $totalShipment = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.`shipment_status` NOT IN ('11','12','22','23')"); 
                        if(!empty($totalShipment[0]->getTotal())){
                            echo $totalShipment[0]->getTotal();
                            $totalOfALLShipment = $totalShipment[0]->getTotal();
                        }else{
                            echo 0;
                        }
                        
                    ?>
                    </span>
                </div>
                <div class="desc">Total Shipments </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup text-center">
                    <?php 
                    $totalShipmentAmount= 0;
                    $totalShiAmnt = 0;
                        $totalShipmentAmount = TrackingData::getAccountShipmentAmount($this->user->getUserAccountId()); 
                        if(!empty($totalShipmentAmount[0]->getId())){
                            echo number_format($totalShipmentAmount[0]->getId(),2).' GBP';
                            $totalShiAmnt = $totalShipmentAmount[0]->getId();
                        }else{
                            echo 0;
                        }
                    ?>
                    </span>
                </div>
                <div class="desc">Total Shipments Amount </div>
            </div>
        </a>
    </div>
    
</div>
<?php 
$totalShipment= 0;
$totalForBilledShipment = 0;
$billableShip = 0;
//$totalShipment = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.shipment_status IN ('13','14','15','16','17','18','19','20','21','24','25','26','27','28','29','30') AND c.`date_label_created` > 0"); 
$totalShipment = TrackingData::getAccountBillableShipment($this->user->getUserAccountId());
if(!empty($totalShipment[0]->getId())){
    $billableShip = $totalShipment[0]->getId();
}
$totalForBilledShipment = $totalOfALLShipment - $billableShip;

$totalunBillShipment= 0;
$totalPaidShip = 0;
$totalRecordUnPaidShip = 0;
$totalunBillShipment = ConsignmentFilter::getPaidDashboard($this->user->getUserAccountId()); 
if(!empty($totalunBillShipment))
    $totalPaidShip = $totalunBillShipment[0]->getId();

$totalRecordUnPaidShip = $billableShip - $totalPaidShip;
?>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?= $totalForBilledShipment;?>
                    </span>
                </div>
                <div class="desc">Total Billable Shipment </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                    echo $billableShip;
                    ?>
                    </span>
                </div>
                <div class="desc">Total Billed Shipment </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        echo $totalRecordUnPaidShip;
                    ?>
                    </span>
                </div>
                <div class="desc">Total Un Paid Shipment </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        echo $totalPaidShip;
                    ?>
                    </span>
                </div>
                <div class="desc">Total Paid Shipment </div>
            </div>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalAmount= 0;
                        //$totalAmount = TrackingData::getAccountDashboardBalnce($this->user->getUserAccountId()); 
                        $totalAmount = checkBalance($this->user->getUserAccountId(),'',1); 
                        if(!empty($totalAmount))
                            echo number_format($totalAmount[0],2).' GBP';
                        else 
                            echo "0.00 GBP";
                    ?>
                    </span>
                </div>
                <div class="desc"> Available Balance</div>
            </div>
        </a>
    </div>
    <?php 
    $totalBillingAmount= 0;
    $totalBillableAmount = 0;
    $totalBillAmnt = 0;
    $totalBillingAmount = TrackingData::getAccountDashboardBillableAmount($this->user->getUserAccountId()); 
    if(!empty($totalBillingAmount[0]->getId()))
        $totalBillableAmount = $totalBillingAmount[0]->getId();

    
    $totalBillAmnt = $totalShiAmnt - $totalBillableAmount;
    ?>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?= number_format($totalBillableAmount,2).' GBP';?>
                    </span>
                </div>
                <div class="desc"> Total Billable Amount</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?= number_format($totalBillAmnt,2).' GBP';  ?>
                    </span>
                </div>
                <div class="desc"> Total Billed Amount</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalunBillingAmount= 0;
                        $totalunBillingAmount = TrackingData::getAccountDashboardTotalBilledAmount($this->user->getUserAccountId()); 
                        if(!empty($totalunBillingAmount[0]->getId()))
                            echo number_format($totalunBillingAmount[0]->getId(),2).' GBP';
                        else
                            echo "0.00 GBP";
                    ?>
                    </span>
                </div>
                <div class="desc"> Total Paid Amount</div>
            </div>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Top 20 Countries Billed Amount</span>
                </div>
            </div>
            <div class="portlet-body">
                <div id="top_countries_serial" style="height: 525px;"></div>
            </div>
        </div>
    </div>
</div>
<?php
}else if(!empty($dataSubAccountOnly) && $dataSubAccountOnly[0]->getIsPrepaid() == '1'){
?>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalAmount= 0;
                        //$totalAmount = TrackingData::getAccountDashboardBalnce($this->user->getUserAccountId()); 
                        $totalAmount = checkBalance($this->user->getUserAccountId(),'',1);  
                        if(!empty($totalAmount))
                            echo number_format($totalAmount[0],2).' GBP';
                        else 
                            echo "0.00 GBP";
                    ?>
                    </span>
                </div>
                <div class="desc"> Current Balance</div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalSpend = 0;
                        $totalSpend = PaymentsHistoryFilter::customerTotalSpend($this->user->getUserAccountId()); 
                        if(!empty($totalSpend))
                            echo number_format($totalSpend[0]->getId(),2).' GBP';
                        else 
                            echo "0.00 GBP";
                    ?>
                    </span>
                </div>
                <div class="desc"> Total Spend</div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow theme-bg-primary" href="#">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup text-center">
                    <?php 
                        $totalShipment= 0;
                        $totalOfALLShipment = 0;
                        $totalShipment = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.`shipment_status` NOT IN ('11','12','22','23')"); 
                        if(!empty($totalShipment[0]->getTotal())){
                            echo $totalShipment[0]->getTotal();
                        }else{
                            echo 0;
                        }
                        
                    ?>
                    </span>
                </div>
                <div class="desc">Total Shipments </div>
            </div>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Shipment Amount Of Current Month</span>
                </div>
            </div>
            <div class="portlet-body">
                <div id="top_shipment_serial" style="height: 525px;"></div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>


