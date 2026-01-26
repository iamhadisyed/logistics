<div class="portlet light">
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="<?= BASE_URL.'view_scaned_parcel_report.php';?>">
            <div class="visual">
                <i class="fa fa-truck"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php echo TrackingData::getTotalParcelsCountByAccountId($this->user->getUserAccountId(), $this->user->getWarehouseId()); ?></span>
                </div>
                <div class="desc">Total Parcel Scanned </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red theme-bg-secondary" href="<?= BASE_URL.'view_scaned_parcel_report.php?id='.$this->user->getId();?>">
            <div class="visual">
                <i class="fa fa-user"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php echo TrackingData::getTotalParcelsCountByAccountId($this->user->getUserAccountId(), $this->user->getWarehouseId(), $this->user->getId()); ?></span>
                </div>
                <div class="desc"> Total Current User Scanned </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="op_manifest_list.php?dispatch=yes">
            <div class="visual">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php echo TrackingData::getTotalScannedManifestCountByAccountId($this->user->getUserAccountId()); ?></span>
                </div>
                <div class="desc"> Total Dispatched Manifests </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple theme-bg-secondary" href="op_manifest_list.php?manifest=upcomming">
            <div class="visual">
                <i class="fa fa-hourglass-half"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php $manifestObj = new ManifestFilter();?>
                    <span data-counter="counterup"><?php echo count($manifestObj->getUpCommingPagingCount($this->user->getUserAccountId())); ?></span>
                </div>
                <div class="desc"> Upcoming Client's Manifest </div>
            </div>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Service Scanned Report</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_comments_1" data-toggle="tab"> Today </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_2" data-toggle="tab"> Week </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_3" data-toggle="tab"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_comments_1">
                        <div id="chart_1" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_2">
                        <div id="chart_2" class="chart" style="height: 525px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_3">
                        <div id="chart_3" class="chart" style="height: 525px;"> </div>
                        <!--                                        <div id="legenddiv" style="border: 2px dotted #3f3; margin: 5px 0 20px 0;position: relative;"></div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">User Scanned Report</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_comments_4" data-toggle="tab"> Today </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_5" data-toggle="tab"> Week </a>
                    </li>
                    <li>
                        <a href="#portlet_comments_6" data-toggle="tab"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_comments_4">
                        <div id="chart_4" class="chart" style="height: 500px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_5">
                        <div id="chart_5" class="chart" style="height: 500px;"> </div>
                    </div>
                    <div class="tab-pane" id="portlet_comments_6">
                        <div id="chart_6" class="chart" style="height: 500px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Upcoming Manifest Report</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#upcomming_manifest_today_tab" data-toggle="tab"> Today </a>
                    </li>
                    <li>
                        <a href="#upcomming_manifest_week_tab" data-toggle="tab"> Week </a>
                    </li>
                    <li>
                        <a href="#upcomming_manifest_month_tab" data-toggle="tab"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="upcomming_manifest_today_tab">
                        <div id="upcomming_manifest" class="chart"> </div>
                    </div>
                    <div class="tab-pane" id="upcomming_manifest_week_tab">
                        <div id="upcomming_manifest_weekly" class="chart"> </div>
                    </div>
                    <div class="tab-pane" id="upcomming_manifest_month_tab">
                            <div id="upcomming_manifest_monthly" class="chart"> </div>
                    </div>

                </div>
            </div>
        </div>
    </div>   
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Dispatched Manifests Report</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#scanned_manifest_today_tab" data-toggle="tab"> Today </a>
                    </li>
                    <li>
                        <a href="#scanned_manifest_week_tab" data-toggle="tab"> Week </a>
                    </li>
                    <li>
                        <a href="#scanned_manifest_month_tab" data-toggle="tab"> Month </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="scanned_manifest_today_tab">
                        <div id="scanned_manifest" class="chart" style="height: 400px;"> </div>
                    </div>
                    <div class="tab-pane" id="scanned_manifest_week_tab">
                        <div id="scanned_manifest_weekly" class="chart" style="height: 400px;"> </div>
                    </div>
                    <div class="tab-pane" id="scanned_manifest_month_tab">
                        <div id="scanned_manifest_monthly" class="chart" style="height: 400px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>     
</div>