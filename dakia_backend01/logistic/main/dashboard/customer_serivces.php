<?php $this->user = SessionManager::getUser();
$allouedAcccounts = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
?>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="client_list.php?show=cs_order_shipment">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalOrderSubmitted= 0;
                        $totalOrderSubmitted = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.shipment_status IN ('10','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30')  GROUP BY c.id	ORDER BY c.date_created DESC"); 
                        echo count($totalOrderSubmitted);
                    ?>
                    </span>
                </div>
                <div class="desc"><small>Total Order Submitted <br/>(Excluding Invalid) </small></div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="client_list.php?show=cs_ready_print_shipment">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalOrderSubmitted= 0;
                        $totalOrderSubmitted = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.shipment_status IN ('12')  GROUP BY c.id	ORDER BY c.date_created DESC"); 
                        echo count($totalOrderSubmitted);
                    ?>
                    </span>
                </div>
                <div class="desc">Total Ready To Print </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="client_list.php?show=cs_total_shipment">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup">
                    <?php 
                        $totalShipment= 0;
                        $totalShipment = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.shipment_status IN ('13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29')  GROUP BY c.id	ORDER BY c.date_created DESC"); 
                        echo count($totalShipment);
                    ?>
                    </span>
                </div>
                <div class="desc"><small>Total Label Created <br/>(including deleted) </small></div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="client_list.php?show=cs_label_shipment">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php 
                     $totalLabelCreated= 0;
                        $totalLabelCreated = TrackingData::getCSDashboardTotalLabelCreated($allouedAcccounts, 0,0," AND c.shipment_status IN ('13') GROUP BY c.id	ORDER BY c.date_created DESC"); 
                        echo count($totalLabelCreated);
                    ?>
                    </span>
                </div>
                <div class="desc"><small> Current Label Created </small></div>
            </div>
        </a>
    </div>
   
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-secondary" href="client_list.php?show=cs_delivered_shipment">
            <div class="visual">
                <i class="fa fa-hourglass-half"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php $delivered = 0; 
                    $delivered = TrackingData::getCSDashboardTotalShipment($allouedAcccounts, $this->user->getWarehouseId(), $this->user->getId(),"   AND c.`shipment_status` IN ('19','20')   GROUP BY c.id	ORDER BY c.date_created DESC"); 
                    echo count($delivered);
                    ?></span>
                </div>
                <div class="desc"> Total Delivered Shipments</div>
            </div>
        </a>
    </div>
     <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-primary" href="client_list.php?show=cs_intransit_shipment">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php $notDelivered = 0;
                        $notDelivered = TrackingData::getCSDashboardTotalShipment($allouedAcccounts, 0, 0,"   AND c.`shipment_status` IN ('18','17','16','14','15')  GROUP BY c.id	ORDER BY c.date_created DESC "); 
                        echo count($notDelivered);
                    ?></span>
                </div>
                <div class="desc"><small style="font-size:11px;"> Total Not Delivered Shipments <br/>(in Transit, Dispatched, partial Dispatched)</small></div>
            </div>
        </a>
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-secondary" href="client_list.php?show=cs_deleted_shipment">
            <div class="visual">
                <i class="fa fa-hourglass-half"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php $delivered = 0; 
                    $delivered = TrackingData::getCSDashboardTotalShipment($allouedAcccounts, $this->user->getWarehouseId(), $this->user->getId(),"   AND c.`shipment_status` = '22'  GROUP BY c.id	ORDER BY c.date_created DESC"); 
                    echo count($delivered);
                    ?></span>
                </div>
                <div class="desc"> Total Deleted Shipments</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-secondary" href="client_list.php?show=cs_hold_problem_shipment">
            <div class="visual">
                <i class="fa fa-user"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php $hold = 0; 
                    $hold = TrackingData::getCSDashboardTotalShipment($allouedAcccounts, 0, 0,"  AND c.`shipment_status` IN ('24','25') GROUP BY c.id	ORDER BY c.date_created DESC"); 
                    echo count($hold);
                    ?>
                    </span>
                </div>
                <div class="desc"> <small>Total Hold / Problem Shipment</small> </div>
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
                    <span class="caption-subject font-dark bold uppercase">Shipment Top 20 Countries</span>
                </div>
            </div>
            <div class="portlet-body">
                <div id="top_countries_serial" class="" style="height: 525px;"> </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-xs-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Top Good Services </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_goods_services_day" onclick="setTopGoodServiceDataSet('customer_service_ajax.php?serialchart=serial&param=today','serialchart_today');" data-toggle="tab" class="today_top"> Today </a>
                    </li>
                    <li>
                        <a href="#portlet_goods_services_week" onclick="setTopGoodServiceDataSet('customer_service_ajax.php?serialchart=serial&param=week','serialchart_week');" data-toggle="tab"> Week </a>
                    </li>
                    <li>
                        <a href="#portlet_goods_services_month" onclick="setTopGoodServiceDataSet('customer_service_ajax.php?serialchart=serial&param=month','serialchart_month');" data-toggle="tab"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_goods_services_day">
                        <div id="serialchart_today" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_goods_services_week">
                        <div id="serialchart_week" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_goods_services_month">
                        <div id="serialchart_month" class="chart" style="height: 525px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--</div>
<div class="row">-->
    <div class="col-lg-6 col-xs-6 col-sm-6">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Top  Worse Services </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_worse_services_day" data-toggle="tab" onclick="setTopWorseServiceDataSet('customer_service_ajax.php?serialchart=serial-worse&param=today','chart_worse_services_day');" class="today_worse"> Today </a>
                    </li>
                    <li>
                        <a href="#portlet_worse_services_week" data-toggle="tab" onclick="setTopWorseServiceDataSet('customer_service_ajax.php?serialchart=serial-worse&param=week','chart_worse_services_week');"> Week </a>
                    </li>
                    <li>
                        <a href="#portlet_worse_services_month" data-toggle="tab" onclick="setTopWorseServiceDataSet('customer_service_ajax.php?serialchart=serial-worse&param=month','chart_worse_services_month');"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_worse_services_day">
                        <div id="chart_worse_services_day" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_worse_services_week">
                        <div id="chart_worse_services_week" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_worse_services_month">
                        <div id="chart_worse_services_month" class="chart" style="height: 525px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Carrier Performance</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_comments_1" data-toggle="tab"  onclick="setDataSet('customer_service_ajax.php?piechart=pie&param=today','chart_today');" data-page="portlet_comments_1" class="today_delivery"> Today </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_2" data-toggle="tab" class="raio" onclick="setDataSet('customer_service_ajax.php?piechart=pie&param=week','chart_week');" data-page="portlet_comments_2"> Week </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_3" data-toggle="tab" class="raio" onclick="setDataSet('customer_service_ajax.php?piechart=pie&param=month','chart_month');" data-page="portlet_comments_3"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_comments_1">
                        <div id="chart_today" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_2">
                        <div id="chart_week" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_3">
                        <div id="chart_month" class="chart" style="height: 525px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

