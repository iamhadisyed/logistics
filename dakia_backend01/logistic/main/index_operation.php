<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $user;
    private $today;
    private $week;
    private $month;
    private $userToday;
    private $userWeek;
    private $userMonth;
    private $totalServicesScanned = 0;
    private $totalUpcommingMenifest = 0;
    private $graphUpcommingMenifestToday;
    private $graphUpcommingMenifestWeek;
    private $graphUpcommingMenifestMonth;
    private $graphDispatchedMenifestToday;
    private $graphDispatchedMenifestWeek;
    private $graphDispatchedMenifestMonth;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // Session check
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("index.php");
        }
        //BreadCrum
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'index_operation.php' => 'Operation Dashboard'
        );
        $week = "";
        $today = "";
        $month = "";
        $date = "";
        $tempWeekDate = "";
        $tempMonthDate = "";

        $date = date('Y-m-d');
        $today = $date;
        $date = strtotime($date);
        $tempDate = strtotime("-7 day", $date);
        $tempMonthDate = strtotime("-30 day", $date);
        $week = date('Y-m-d', $tempDate);
        $month = date('Y-m-d', $tempMonthDate);

        $todayData = "";
        $weekData = "";
        $monthData = "";

        $todayUserData = "";
        $weekUserData = "";
        $monthUserData = "";
        //Get data for service
        $trackingDataFilter = new TrackingDataFilter();
        $todayData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(), $today, "=");
        $weekData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(), $week, ">=");
        $monthData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(), $month, ">=");
        $this->totalServicesScanned = count($trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId()));

        foreach ($monthData as $monthArr) {
            $this->month .= '{
                                service: "' . $monthArr->getCarrierDesc() . '",
                                scanTotal: ' . $monthArr->getEntityId() . '
                            },';
        }
        $this->month = rtrim($this->month, ',');

        foreach ($weekData as $weekArr) {
            $this->week .= '{
                                service: "' . $weekArr->getCarrierDesc() . '",
                                scanTotal: ' . $weekArr->getEntityId() . '
                            },';
        }
        $this->week = rtrim($this->week, ',');

        foreach ($todayData as $todayArr) {
            $this->today .= '{
                                service: "' . $todayArr->getCarrierDesc() . '",
                                scanTotal: ' . $todayArr->getEntityId() . '
                            },';
        }
        $this->today = rtrim($this->today, ',');
        //End Get data for service
        //Get logged in user account then get that user accounts users then get their scanned parcels and then list down each user's total scanned parcels list
        $userFilter = new UserFilter();
        $todayUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $today, "=");

        foreach ($todayUserData as $todayUserArr) {
            $this->userToday .= '{
                                user: "' . $todayUserArr->getUserName() . '",
                                scannedTotal: ' . $todayUserArr->getId() . '
                            },';
        }
        $this->userToday = rtrim($this->userToday, ',');

        $weekUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $week, ">=");
        foreach ($weekUserData as $weekUserArr) {
            $this->userWeek .= '{
                                user: "' . $weekUserArr->getUserName() . '",
                                scannedTotal: ' . $weekUserArr->getId() . '
                            },';
        }
        $this->userWeek = rtrim($this->userWeek, ',');

        $monthUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $month, ">=");
        foreach ($monthUserData as $monthUserArr) {
            $this->userMonth .= '{
                                user: "' . $monthUserArr->getUserName() . '",
                                scannedTotal: ' . $monthUserArr->getId() . '
                            },';
        }
        $this->userMonth = rtrim($this->userMonth, ',');

        $trackingDatObj = new TrackingData();
        $scannedMenifestToday = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $today, "=");
        foreach ($scannedMenifestToday as $menifest) {
            $this->totalUpcommingMenifest++;
            $this->graphUpcommingMenifestToday .= "[" . $menifest->getrMenifestId() . "," . $menifest->getrMenifestWeight() . "],";
        }
        $this->graphUpcommingMenifestToday = rtrim($this->graphUpcommingMenifestToday, ',');

        $scannedMenifestWeek = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $week, ">=");
        foreach ($scannedMenifestWeek as $menifest) {
            $this->totalUpcommingMenifest++;
            $this->graphUpcommingMenifestWeek .= "[" . $menifest->getrMenifestId() . "," . $menifest->getrMenifestWeight() . "],";
        }
        $this->graphUpcommingMenifestWeek = rtrim($this->graphUpcommingMenifestWeek, ',');

        $scannedMenifestMonth = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $month, ">=");
        foreach ($scannedMenifestMonth as $menifest) {
            $this->totalUpcommingMenifest++;
            $this->graphUpcommingMenifestMonth .= "[" . $menifest->getrMenifestId() . "," . $menifest->getrMenifestWeight() . "],";
        }
        $this->graphUpcommingMenifestMonth = rtrim($this->graphUpcommingMenifestMonth, ',');
        
        $manifest = new Manifest();
        $scannedMenifestToday = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $today, "=");
        foreach ($scannedMenifestToday as $scannedMenifest) {
            $this->graphDispatchedMenifestToday .= '["'.date('d/m/Y',$scannedMenifest->getDateCreated()).'"' .',' . $scannedMenifest->getId() . '],';
        }
        $this->graphDispatchedMenifestToday = rtrim($this->graphDispatchedMenifestToday, ',');

        $scannedMenifestWeek = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $week, ">=");
        foreach ($scannedMenifestWeek as $scannedMenifestW) {
            $this->graphDispatchedMenifestWeek .= '["'.date('d/m/Y',$scannedMenifestW->getDateCreated()).'"' .',' . $scannedMenifestW->getId() . '],';
        }
        $this->graphDispatchedMenifestWeek = rtrim($this->graphDispatchedMenifestWeek, ',');

        $scannedMenifestMonth = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $month, ">=");
        foreach ($scannedMenifestMonth as $scannedMenifestM) {
            $this->graphDispatchedMenifestMonth .= '["'.date('d/m/Y',$scannedMenifestM->getDateCreated()).'"' .',' . $scannedMenifestM->getId() . '],';
        }
        $this->graphDispatchedMenifestMonth = rtrim($this->graphDispatchedMenifestMonth, ',');
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style>
            #upcomming_menifest_today, #upcomming_manifest_weekly,#upcomming_manifest_monthly
            {
                width: 100% !important;
                height: 228px !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <?php
    }

    protected function renderHead() {
        ?>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="#">
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
                <a class="dashboard-stat dashboard-stat-v2 red theme-bg-secondary" href="#">
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
                <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
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
                <a class="dashboard-stat dashboard-stat-v2 purple theme-bg-secondary" href="#">
                    <div class="visual">
                        <i class="fa fa-hourglass-half"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup"><?php echo TrackingData::upcommingManifestTotal($this->user->getUserAccountId()); ?></span>
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
                                <div id="upcomming_manifest_loading">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="upcomming_menifest_today" class="display-none">
                                    <div id="upcomming_manifest" style="height: 228px;"> </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="upcomming_manifest_week_tab">
                                <div id="upcomming_manifest_loading_week">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="upcomming_menifest_week" class="display-none">
                                    <div id="upcomming_manifest_weekly" style="height: 228px;"> </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="upcomming_manifest_month_tab">
                                <div id="upcomming_manifest_loading_month">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="upcomming_menifest_month" class="display-none">
                                    <div id="upcomming_manifest_monthly" style="height: 228px;"> </div>
                                </div>
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
                                <div id="scanned_manifest_loading">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="scanned_menifest_today" class="display-none">
                                    <div id="scanned_manifest" class="chart"> </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="scanned_manifest_week_tab">
                                <div id="scanned_manifest_loading_week">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="scanned_menifest_week" class="display-none">
                                    <div id="scanned_manifest_weekly" class="chart"> </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="scanned_manifest_month_tab">
                                <div id="scanned_manifest_loading_month">
                                    <img src="../assets/global/img/loading.gif" alt="loading" />
                                </div>
                                <div id="scanned_menifest_month" class="display-none">
                                    <div id="scanned_manifest_monthly" class="chart"> </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <?php
    }

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function (e) {
                AmCharts.makeChart("chart_1", {
                    type: "pie",
                    theme: "light",
                    fontFamily: "Open Sans",
                    color: "#888",
                    "labelRadius": -35,
                    "labelText": "[[percents]]%",
                    "depth3D": 30,
                    dataProvider: [<?php echo $this->today; ?>],
                    valueField: "scanTotal",
                    titleField: "service",
                    "angle": "30",
                    "radius": '200',
                    "balloonText": "[[service]]: [[percents]]% ([[value]])\n[[caption]]",
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    exportConfig: {
                        menuItems: [{
                                icon: App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                                format: "png"
                            }]
                    }
                });
                AmCharts.makeChart("chart_2", {
                    type: "pie",
                    theme: "light",
                    fontFamily: "Open Sans",
                    color: "#888",
                    "labelRadius": -35,
                    "labelText": "[[percents]]%",
                    "depth3D": 30,
                    dataProvider: [<?php echo $this->week; ?>],
                    valueField: "scanTotal",
                    titleField: "service",
                    "angle": "30",
                    "radius": '200',
                    "balloonText": "[[service]]: [[percents]]% ([[value]])\n[[caption]]",
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    exportConfig: {
                        menuItems: [{
                                icon: App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                                format: "png"
                            }]
                    }
                });
                AmCharts.makeChart("chart_3", {
                    type: "pie",
                    theme: "light",
                    fontFamily: "Open Sans",
                    color: "#888",
                    "labelRadius": -35,
                    "labelText": "[[percents]]%",
                    "depth3D": 30,
                    dataProvider: [<?php echo $this->month; ?>],
                    valueField: "scanTotal",
                    titleField: "service",
                    "angle": "30",
                    "radius": '200',
                    "balloonText": "[[service]]: [[percents]]% ([[value]])\n[[caption]]",
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    exportConfig: {
                        menuItems: [{
                                icon: App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                                format: "png"
                            }]
                    }
                });

                AmCharts.makeChart("chart_4", {
                    type: "serial",
                    theme: "light",
                    pathToImages: App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
                    autoMargins: !1,
                    marginLeft: 80,
                    marginRight: 8,
                    marginTop: 10,
                    marginBottom: 26,
                    fontFamily: "Open Sans",
                    color: "#888",
                    dataProvider: [<?php echo $this->userToday; ?>],
                    valueAxes: [{
                            axisAlpha: 0,
                            position: "left"
                        }],
                    startDuration: 1,
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    graphs: [{
                            alphaField: "alpha",
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            dashLengthField: "dashLengthColumn",
                            fillAlphas: 1,
                            title: "USER SCANNED PARCEL ",
                            type: "column",
                            valueField: "scannedTotal"
                        }, {
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            bullet: "round",
                            dashLengthField: "dashLengthLine",
                            lineThickness: 3,
                            bulletSize: 7,
                            bulletBorderAlpha: 1,
                            bulletColor: "#FFFFFF",
                            useLineColorForBulletBorder: !0,
                            bulletBorderThickness: 3,
                            fillAlphas: 0,
                            lineAlpha: 1,
                            title: "user",
                            valueField: "user"
                        }],
                    categoryField: "user",
                    categoryAxis: {
                        gridPosition: "start",
                        axisAlpha: 0,
                        tickLength: 0
                    }
                });

                AmCharts.makeChart("chart_5", {
                    type: "serial",
                    theme: "light",
                    pathToImages: App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
                    autoMargins: !1,
                    marginLeft: 80,
                    marginRight: 8,
                    marginTop: 10,
                    marginBottom: 26,
                    fontFamily: "Open Sans",
                    color: "#888",
                    dataProvider: [<?php echo $this->userWeek; ?>],
                    valueAxes: [{
                            axisAlpha: 0,
                            position: "left"
                        }],
                    startDuration: 1,
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    graphs: [{
                            alphaField: "alpha",
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            dashLengthField: "dashLengthColumn",
                            fillAlphas: 1,
                            title: "USER SCANNED PARCEL ",
                            type: "column",
                            valueField: "scannedTotal"
                        }, {
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            bullet: "round",
                            dashLengthField: "dashLengthLine",
                            lineThickness: 3,
                            bulletSize: 7,
                            bulletBorderAlpha: 1,
                            bulletColor: "#FFFFFF",
                            useLineColorForBulletBorder: !0,
                            bulletBorderThickness: 3,
                            fillAlphas: 0,
                            lineAlpha: 1,
                            title: "user",
                            valueField: "user"
                        }],
                    categoryField: "user",
                    categoryAxis: {
                        gridPosition: "start",
                        axisAlpha: 0,
                        tickLength: 0
                    }
                });


                AmCharts.makeChart("chart_6", {
                    type: "serial",
                    theme: "light",
                    pathToImages: App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
                    autoMargins: !1,
                    marginLeft: 80,
                    marginRight: 8,
                    marginTop: 10,
                    marginBottom: 26,
                    fontFamily: "Open Sans",
                    color: "#888",
                    dataProvider: [<?php echo $this->userMonth; ?>],
                    valueAxes: [{
                            axisAlpha: 0,
                            position: "left"
                        }],
                    startDuration: 1,
                    "legend": {
                        "position": "bottom",
                        "marginTop": 1,
                        "autoMargins": true
                    },
                    graphs: [{
                            alphaField: "alpha",
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            dashLengthField: "dashLengthColumn",
                            fillAlphas: 1,
                            title: "USER SCANNED PARCEL ",
                            type: "column",
                            valueField: "scannedTotal"
                        }, {
                            balloonText: "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                            bullet: "round",
                            dashLengthField: "dashLengthLine",
                            lineThickness: 3,
                            bulletSize: 7,
                            bulletBorderAlpha: 1,
                            bulletColor: "#FFFFFF",
                            useLineColorForBulletBorder: !0,
                            bulletBorderThickness: 3,
                            fillAlphas: 0,
                            lineAlpha: 1,
                            title: "user",
                            valueField: "user"
                        }],
                    categoryField: "user",
                    categoryAxis: {
                        gridPosition: "start",
                        axisAlpha: 0,
                        tickLength: 0
                    }
                });
                function showChartTooltip(x, y, xValue, yValue) {
                    $('<div id="tooltip" class="chart-tooltip">' + yValue + '<\/div>').css({
                        position: 'absolute',
                        display: 'none',
                        top: y - 40,
                        left: x - 40,
                        border: '0px solid #ccc',
                        padding: '2px 6px',
                        'background-color': '#fff'
                    }).appendTo("body").fadeIn(200);
                }
                if ($('#upcomming_manifest').size() != 0) {
                    var previousPoint2 = null;
                    $('#upcomming_manifest_loading').hide();
                    $('#upcomming_menifest_today').show();
                    var data1 = [<?php echo $this->graphUpcommingMenifestToday; ?>];

                    var plot_statistics = $.plot($("#upcomming_manifest"),
                            [{
                                    data: data1,
                                    lines: {
                                        fill: 0.2,
                                        lineWidth: 0,
                                    },
                                    color: ['#BAD9F5']
                                }, {
                                    data: data1,
                                    points: {
                                        show: true,
                                        fill: true,
                                        radius: 4,
                                        fillColor: "#9ACAE6",
                                        lineWidth: 2
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 1
                                }, {
                                    data: data1,
                                    lines: {
                                        show: true,
                                        fill: false,
                                        lineWidth: 3
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 0
                                }],
                            {

                                xaxis: {
                                    tickLength: 0,
                                    tickDecimals: 0,
                                    mode: "categories",
                                    min: 0,
                                    font: {
                                        lineHeight: 18,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                yaxis: {
                                    ticks: 5,
                                    tickDecimals: 0,
                                    tickColor: "#eee",
                                    font: {
                                        lineHeight: 14,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                grid: {
                                    hoverable: true,
                                    clickable: true,
                                    tickColor: "#eee",
                                    borderColor: "#eee",
                                    borderWidth: 1
                                }
                            });

                    $("#upcomming_manifest").bind("plothover", function (event, pos, item) {
                        $("#x").text(pos.x.toFixed(2));
                        $("#y").text(pos.y.toFixed(2));
                        if (item) {
                            if (previousPoint2 != item.dataIndex) {
                                previousPoint2 = item.dataIndex;
                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                        y = item.datapoint[1].toFixed(2);
                                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + 'kg');
                            }
                        }
                    });

                    $('#upcomming_manifest').bind("mouseleave", function () {
                        $("#tooltip").remove();
                    });
                }
                if ($('#upcomming_manifest_weekly').size() != 0) {
                    var previousPointWeek = null;
                    $('#upcomming_manifest_loading_week').hide();
                    $('#upcomming_menifest_week').show();
                    var dataWeek = [<?php echo $this->graphUpcommingMenifestWeek; ?>];

                    var plot_statisticss = $.plot($("#upcomming_manifest_weekly"),
                            [{
                                    data: dataWeek,
                                    lines: {
                                        fill: 0.2,
                                        lineWidth: 0,
                                    },
                                    color: ['#BAD9F5']
                                }, {
                                    data: dataWeek,
                                    points: {
                                        show: true,
                                        fill: true,
                                        radius: 4,
                                        fillColor: "#9ACAE6",
                                        lineWidth: 2
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 1
                                }, {
                                    data: dataWeek,
                                    lines: {
                                        show: true,
                                        fill: false,
                                        lineWidth: 3
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 0
                                }],
                            {

                                xaxis: {
                                    tickLength: 0,
                                    tickDecimals: 0,
                                    mode: "categories",
                                    min: 0,
                                    font: {
                                        lineHeight: 18,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                yaxis: {
                                    ticks: 5,
                                    tickDecimals: 0,
                                    tickColor: "#eee",
                                    font: {
                                        lineHeight: 14,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                grid: {
                                    hoverable: true,
                                    clickable: true,
                                    tickColor: "#eee",
                                    borderColor: "#eee",
                                    borderWidth: 1
                                }
                            });

                    $("#upcomming_manifest_weekly").bind("plothover", function (event, pos, item) {
                        $("#x").text(pos.x.toFixed(2));
                        $("#y").text(pos.y.toFixed(2));
                        if (item) {
                            if (previousPointWeek != item.dataIndex) {
                                previousPointWeek = item.dataIndex;
                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                        y = item.datapoint[1].toFixed(2);
                                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + 'kg');
                            }
                        }
                    });

                    $('#upcomming_manifest_weekly').bind("mouseleave", function () {
                        $("#tooltip").remove();
                    });
                }
                if ($('#upcomming_manifest_monthly').size() != 0) {
                    var previousPointmonth = null;
                    $('#upcomming_manifest_loading_month').hide();
                    $('#upcomming_menifest_month').show();
                    var dataMonth = [<?php echo $this->graphUpcommingMenifestMonth; ?>];

                    var plot_statisticsMonth = $.plot($("#upcomming_manifest_monthly"),
                            [{
                                    data: dataMonth,
                                    lines: {
                                        fill: 0.2,
                                        lineWidth: 0,
                                    },
                                    color: ['#BAD9F5']
                                }, {
                                    data: dataMonth,
                                    points: {
                                        show: true,
                                        fill: true,
                                        radius: 4,
                                        fillColor: "#9ACAE6",
                                        lineWidth: 2
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 1
                                }, {
                                    data: dataMonth,
                                    lines: {
                                        show: true,
                                        fill: false,
                                        lineWidth: 3
                                    },
                                    color: '#9ACAE6',
                                    shadowSize: 0
                                }],
                            {

                                xaxis: {
                                    tickLength: 0,
                                    tickDecimals: 0,
                                    mode: "categories",
                                    min: 0,
                                    font: {
                                        lineHeight: 18,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                yaxis: {
                                    ticks: 5,
                                    tickDecimals: 0,
                                    tickColor: "#eee",
                                    font: {
                                        lineHeight: 14,
                                        style: "normal",
                                        variant: "small-caps",
                                        color: "#6F7B8A"
                                    }
                                },
                                grid: {
                                    hoverable: true,
                                    clickable: true,
                                    tickColor: "#eee",
                                    borderColor: "#eee",
                                    borderWidth: 1
                                }
                            });

                    $("#upcomming_manifest_monthly").bind("plothover", function (event, pos, item) {
                        $("#x").text(pos.x.toFixed(2));
                        $("#y").text(pos.y.toFixed(2));
                        if (item) {
                            if (previousPointmonth != item.dataIndex) {
                                previousPointmonth = item.dataIndex;
                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                        y = item.datapoint[1].toFixed(2);
                                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + 'kg');
                            }
                        }
                    });

                    $('#upcomming_manifest_monthly').bind("mouseleave", function () {
                        $("#tooltip").remove();
                    });
                }
                if (0 != $("#scanned_manifest").size()) {
                    $("#scanned_manifest_loading").hide(), $("#scanned_menifest_today").show();
                    var dataDispatchToday = [<?php echo $this->graphDispatchedMenifestToday; ?>];
                    var a = ($.plot($("#scanned_manifest"), [{
                        data: dataDispatchToday,
                        lines: {
                            fill: .6,
                            lineWidth: 0
                        },
                        color: ["#f89f9f"]
                    }, {
                        data: dataDispatchToday,
                        points: {
                            show: !0,
                            fill: !0,
                            radius: 5,
                            fillColor: "#f89f9f",
                            lineWidth: 3
                        },
                        color: "#fff",
                        shadowSize: 0
                    }], {
                        xaxis: {
                            tickLength: 0,
                            tickDecimals: 0,
                            mode: "categories",
                            min: 0,
                            position: "top",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        yaxis: {
                            ticks: 5,
                            tickDecimals: 0,
                            tickColor: "#eee",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        grid: {
                            hoverable: !0,
                            clickable: !0,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        }
                    }), null);
                    $("#scanned_manifest").bind("plothover", function(dataDispatchToday, i, l) {
                        if ($("#x").text(i.x.toFixed(2)), $("#y").text(i.y.toFixed(2)), l) {
                            if (a != l.dataIndex) {
                                a = l.dataIndex, $("#tooltip").remove();
                                l.datapoint[0].toFixed(2), l.datapoint[1].toFixed(2);
                                showChartTooltip(l.pageX, l.pageY, l.datapoint[0], l.datapoint[1] + " Manifest")
                            }
                        } else $("#tooltip").remove(), a = null
                    })
                }
                if (0 != $("#scanned_manifest_weekly").size()) {
                    $("#scanned_manifest_loading_week").hide(), $("#scanned_menifest_week").show();
                    var dataDispatchWeek = [<?php echo $this->graphDispatchedMenifestWeek; ?>];
                    var a = ($.plot($("#scanned_manifest_weekly"), [{
                        data: dataDispatchWeek,
                        lines: {
                            fill: .6,
                            lineWidth: 0
                        },
                        color: ["#f89f9f"]
                    }, {
                        data: dataDispatchWeek,
                        points: {
                            show: !0,
                            fill: !0,
                            radius: 5,
                            fillColor: "#f89f9f",
                            lineWidth: 3
                        },
                        color: "#fff",
                        shadowSize: 0
                    }], {
                        xaxis: {
                            tickLength: 0,
                            tickDecimals: 0,
                            mode: "categories",
                            min: 0,
                            position: "top",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        yaxis: {
                            ticks: 5,
                            tickDecimals: 0,
                            tickColor: "#eee",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        grid: {
                            hoverable: !0,
                            clickable: !0,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        }
                    }), null);
                    $("#scanned_manifest_weekly").bind("plothover", function(dataDispatchWeek, i, l) {
                        if ($("#x").text(i.x.toFixed(2)), $("#y").text(i.y.toFixed(2)), l) {
                            if (a != l.dataIndex) {
                                a = l.dataIndex, $("#tooltip").remove();
                                l.datapoint[0].toFixed(2), l.datapoint[1].toFixed(2);
                                showChartTooltip(l.pageX, l.pageY, l.datapoint[0], l.datapoint[1] + "  Manifest")
                            }
                        } else $("#tooltip").remove(), a = null
                    })
                }
                if (0 != $("#scanned_manifest_monthly").size()) {
                    $("#scanned_manifest_loading_month").hide(), $("#scanned_menifest_month").show();
                    var dataDispatchMonth = [<?php echo $this->graphDispatchedMenifestMonth; ?>];
                    var bg = ($.plot($("#scanned_manifest_monthly"), [{
                        data: dataDispatchMonth,
                        lines: {
                            fill: .6,
                            lineWidth: 0
                        },
                        color: ["#f89f9f"]
                    }, {
                        data: dataDispatchMonth,
                        points: {
                            show: !0,
                            fill: !0,
                            radius: 5,
                            fillColor: "#f89f9f",
                            lineWidth: 3
                        },
                        color: "#fff",
                        shadowSize: 0
                    }], {
                        xaxis: {
                            tickLength: 0,
                            tickDecimals: 0,
                            mode: "categories",
                            min: 0,
                            position: "top",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        yaxis: {
                            ticks: 5,
                            tickDecimals: 0,
                            tickColor: "#eee",
                            font: {
                                lineHeight: 14,
                                style: "normal",
                                variant: "small-caps",
                                color: "#6F7B8A"
                            }
                        },
                        grid: {
                            hoverable: !0,
                            clickable: !0,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        }
                    }), null);
                    $("#scanned_manifest_monthly").bind("plothover", function(dataDispatchMonth, i, l) {
                        if ($("#x").text(i.x.toFixed(2)), $("#y").text(i.y.toFixed(2)), l) {
                            if (bg != l.dataIndex) {
                                bg = l.dataIndex, $("#tooltip").remove();
                                l.datapoint[0].toFixed(2), l.datapoint[1].toFixed(2);
                                showChartTooltip(l.pageX, l.pageY, l.datapoint[0], l.datapoint[1] + "  Manifest")
                            }
                        } else $("#tooltip").remove(), bg = null
                    });
                }
            });
        </script>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
