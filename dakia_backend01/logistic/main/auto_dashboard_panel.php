<?php
//require_once("../includes/settings/config.inc.php");
$Sessionuser = SessionManager::getUser();
//$col = "3";
//if($Sessionuser->getThemeId() == "1")
$col = "3";
?>
<div class="row">
    <div class="col-lg-<?= $col; ?> col-md-<?= $col; ?> col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-sharp">
                        <span id="total_shipment">
                            <img id="ajax-total-shipment" width="25" class="ajax-count-loder" src="loading.gif" style="left: 0px; position: relative;">
                        </span>
                        <!--<small class="font-green-sharp">$</small>-->
                    </h3>
                    <small><?php echo Translation::GetCaption("TOTAL_SHIPMENTS"); ?></small>
                </div>
                <div class="icon">
                    <i class="icon-pie-chart"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success blue-sharp">
                        <span class="sr-only">100% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"></div>
                    <div class="status-number"><a href="client_list.php?show=show_lsd"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-<?= $col; ?> col-md-<?= $col; ?> col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-sharp">
                        <span id="total_hold_shipment">
                            <img id="ajax-total-hold-shipment" class="ajax-count-loder" width="25" src="loading.gif" style="left: 0px; position: relative;">
                        </span>
                        <!--<small class="font-green-sharp">$</small>-->
                    </h3>
                    <small><?php echo Translation::GetCaption("LABEL_CREATED"); ?></small>
                </div>
                <div class="icon">
                    <i class="icon-pie-chart"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success red-haze total_hold_shipment_bar">
                        <span class="sr-only">100% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"></div>
                    <div class="status-number"><a href="client_list.php?show=label_created"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a></div>
                </div>
            </div>            
        </div>
    </div>

    <div class="col-lg-<?= $col; ?> col-md-<?= $col; ?> col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-haze">
                        <span id="total_shipped_shipment">
                            <img id="ajax-total-shipped-shipment" width="25" class="ajax-count-loder" src="loading.gif" style="left: 0px; position: relative;">
                        </span>
                        <!--<small class="font-green-sharp">$</small>-->
                    </h3>
                    <small><?php echo Translation::GetCaption("INTRANSIT"); ?></small>
                </div>
                <div class="icon">
                    <i class="icon-pie-chart"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success green-haze total_shipped_shipment_bar">
                        <span class="sr-only">100% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"></div>
                    <div class="status-number"><a href="client_list.php?show=shipped"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a></div>
                </div>
            </div>            
        </div>
    </div>
    <div class="col-lg-<?= $col; ?> col-md-<?= $col; ?> col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-plum">
                        <span id="total_delivered_shipment">
                            <img id="ajax-total-delivered-shipment" width="25" class="ajax-count-loder" src="loading.gif" style="left: 0px; position: relative;">
                        </span>
                        <!--<small class="font-green-sharp">$</small>-->
                    </h3>
                    <small><?php echo Translation::GetCaption("DELIVERED"); ?></small>
                </div>
                <div class="icon">
                    <i class="icon-pie-chart"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success purple-plum total_delivered_shipment_bar">
                        <span class="sr-only">100% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"></div>
                    <div class="status-number"><a href="client_list.php?show=delivered"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>