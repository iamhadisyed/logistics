<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'userservicesrouting.class',
                    'userservicesroutingfilter.class',
                    'services.class' ,
                    'servicefilter.class',
    ]);
// set up local page class
class Page extends BasePage {
    /*     * *
     * Set the page header
     * @return void
     */

    private $service_performance_report_data = "";
    private $service_performance_graph_data = "";
    private $account_name = "";

    public function getTitle() {
        return "Admin - Service Performence Report";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        $this->user = SessionManager::getUser();
        ?>
        <!-- BEGIN PAGE BREADCRUMB -->
        <div class="row">
            <div class="col-lg-12 col-xs-12 col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span id="account_name"></span>Report Filters
                            <!--<span class="caption-helper">distance stats...</span>-->
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data" >
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="control-label">Carrier Name</label><br /><br />
                                    <?php
                                    $user_type = $this->user->getUserType();
                                    $carrier_id = $this->form_vars['carrier_id'];
                                    if ($user_type == User::USER_TYPE_CORPORATE || $user_type == User::USER_TYPE_CLIENT) {
                                        //Get user's assigned service
                                        $userServicesRoutingFilter = new UserServicesRoutingFilter();
                                        $userServicesRoutingFilter->addFieldFilter("user_account_id", $this->user->getUserAccountId());
                                        $userServicesRoutingFilter->setGroup('service_id');
                                        $userAccoutServices = $userServicesRoutingFilter->getList();
                                        $userAccountCarrierList = array();
                                        $userAccountCarrierListStr = '';
                                        foreach ($userAccoutServices as $userServiceList) {
                                            $service = new Services($userServiceList->getServiceId());
                                            $userAccountCarrierList[] = $service->getCarrierId();
                                            $userAccountCarrierListStr .= "'" . $service->getCarrierId() . "',";
                                        }
                                        $carrierIds = rtrim($userAccountCarrierListStr, ",");
                                        echo Ddl::generateCarrierDDLWithImage('carrier_id', DbAccess3::escape($carrier_id), 'id', ' class="bs-select form-control"  data-show-subtext="true"', '', '', " carrier_id = '0' AND country_id != '0' AND id IN (" . $carrierIds . ")");
                                    } else {
                                        echo Ddl::generateCarrierDDLWithImage('carrier_id', DbAccess3::escape($carrier_id), 'id', ' class="bs-select form-control"  data-show-subtext="true"', '', '', " carrier_id = '0' AND country_id != '0'");
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">Date Ranges </label><br /><br />
                                    <div class="input-group">
                                        <div class="input-group input-large date-picker input-daterange" data-date="20-01-2018" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control" name="from_date" id="from" value="<?php echo (isset($this->form_vars['from_date']) && !empty($this->form_vars['from_date']) ? $this->form_vars['from_date'] : ""); ?>">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="to_date" id="to" value="<?php echo (!empty($this->form_vars['to_date']) && is_integer((int)$this->form_vars['to_date'])) ? (int)$this->form_vars['to_date'] : ''; ?><?php echo (!empty($this->form_vars['to_date']) && strtotime($this->form_vars['to_date'] == true)) ? $this->form_vars['to_date'] : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">&nbsp;</label><br /><br />
                                    <button class="btn btn-default" type="submit">Filter Report</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>                
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo $this->account_name; ?> Service Performance
                            <!--<span class="caption-helper">distance stats...</span>-->
                        </div>
                        <div class="actions">
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="service_performance_chart" class="CSSAnimationChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function renderFooter() {
        ?>
        <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>

        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>



        <!-- END PAGE LEVEL SCRIPTS -->


        <script type="text/javascript">
            $(document).ready(function () {
                initShipmentStatsChart();
            });
            var initShipmentStatsChart = function () {
                if (typeof (AmCharts) === 'undefined' || $('#service_performance_chart').size() === 0) {
                    return;
                }
                var chart = AmCharts.makeChart("service_performance_chart", {
                    "type": "serial",
                    "addClassNames": true,
                    "theme": "light",
                    "autoMargins": true,
                    "balloon": {
                        "adjustBorderColor": false,
                        "horizontalPadding": 5,
                        "verticalPadding": 2,
                        "color": "#ffffff"
                    },
                    "legend": {
                        "horizontalGap": 10,
                        "maxColumns": 1,
                        "position": "right",
                        "useGraphSettings": true,
                        "markerSize": 10
                    },
                    "dataProvider": [<?php echo $this->service_performance_report_data; ?>],
                    "valueAxes": [{
                            "axisAlpha": 0,
                            "position": "left"
                        }],
                    "startDuration": 1,
                    "graphs": [{
                            "alphaField": "alpha",
                            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                            "fillAlphas": 1,
                            "title": "Total Shipments",
                            "type": "column",
                            "valueField": "total",
                            "dashLengthField": "dashLengthColumn"
                        }, <?php echo $this->service_performance_graph_data; ?>],
                    "categoryField": "service",
                    "categoryAxis": {
                        "gridPosition": "start",
                        "axisAlpha": 0,
                        "tickLength": 0,
                        "labelRotation": 45
                    },
                    "export": {
                        "enabled": true
                    }
                });
            }
        </script>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
             'Service Performance Report'
        );
        $this->userSession = $userSection = SessionManager::getUser();
        $this->sessionUsersType = str_replace(' ', '_', $userSection->getUserType());
        $consignmentFilteter = new ConsignmentFilter();
        $accountId = $this->userSession->getUserAccountId();
        $fromDate = '';
        $fromDateRaw = '';
        $toDate = '';
        $toDateRaw = '';
        $userAccount = new CustomerAccount($userSection->getUserAccountId());
        $this->account_name = $userAccount->getUserAccount();
        if (isset($this->form_vars['carrier_id'])) {
            $carrier = new Carrier($this->form_vars['carrier_id']);
            if (!empty($carrier->getCarrier())) {
                $this->account_name = $carrier->getCarrier();
            }
        }
        if (isset($this->form_vars['from_date']) && !empty($this->form_vars['from_date'])) {
            $fromDateRaw = $this->form_vars['from_date'];
            $consignmentFilteter->addConsignmentFilter("    c.date_label_created>='" . strtotime($fromDateRaw) . "'");
        }
        if (isset($this->form_vars['to_date']) && !empty($this->form_vars['to_date'])) {
            $toDateRaw = $this->form_vars['to_date'];
            $dt = date('Y-m-d',strtotime($toDateRaw));
            $consignmentFilteter->addConsignmentFilter("   c.date_label_created<='" . strtotime($dt." 11:59:59") . "'");
        }
        $user_type = $this->userSession->getUserType();
        if ($user_type == User::USER_TYPE_CORPORATE) {
            if (isset($this->form_vars['carrier_id']) && !empty($this->form_vars['carrier_id'])) {
                $consignmentFilteter->addConsignmentFilter("    s.carrier_id = '" . DbAccess3::escape($this->form_vars['carrier_id']) . "'");
            } else {
                //Get user's assigned service
                $userServicesRoutingFilter = new UserServicesRoutingFilter();
                $userServicesRoutingFilter->addFieldFilter("    user_account_id", $this->userSession->getUserAccountId());
                $userServicesRoutingFilter->setGroup('service_id');
                $userAccoutServices = $userServicesRoutingFilter->getList();
                $userAccountCarrierList = array();
                $userAccountCarrierListStr = '';
                foreach ($userAccoutServices as $userServiceList) {
                    $service = new Services($userServiceList->getServiceId());
                    $userAccountCarrierList[] = $service->getCarrierId();
                    $userAccountCarrierListStr .= "'" . $service->getCarrierId() . "',";
                }
                $carrierIds = rtrim($userAccountCarrierListStr, ",");
                $consignmentFilteter->addConsignmentFilter("     s.carrier_id IN( " . $carrierIds . ")");
            }
        } else {
            if (isset($this->form_vars['carrier_id']) && !empty($this->form_vars['carrier_id'])) {
                $consignmentFilteter->addConsignmentFilter("     s.carrier_id = '" . DbAccess3::escape($this->form_vars['carrier_id']) . "'");
            }
        }
        if ($user_type == User::USER_TYPE_CLIENT) {
            $consignmentFilteter->addConsignmentFilter("     user_id = '" . $this->userSession->getId() . "'");
        } else if ($user_type == User::USER_TYPE_CORPORATE) {
            $consignmentFilteter->addConsignmentFilter("    (user_id IN (select id from user where user_account_id = '" . DbAccess3::escape($this->userSession->getUserAccountId()) . "'))");
        }

        $status_not_include = array(Consignment::STATUS_INVALID, Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_RECYCLED);
        $consignmentFilteter->addStatusFilterNotIn($status_not_include);
        $status_chart_data = "";
        // Top Countries
        $dataServices = $consignmentFilteter->getServicePerformanceReport();
        $totalShipments = 0;
        $service = "";
        $status = "";
        $data = array();
        $shipmentStatusArr = [];
        foreach ($dataServices as $consignments) {
            $shipmentStatus = $consignments->getShipmentStatus();
            $shipmentStatusArr[] = $shipmentStatus;
            $serviceData = [];
            if (isset($data[$consignments->getServiceId()])) {
                $serviceData = $data[$consignments->getServiceId()];
            }
            $serviceData[$shipmentStatus] = $consignments->getId();
            $data[$consignments->getServiceId()] = $serviceData;
        }
        $shipmentStatusArr = array_unique($shipmentStatusArr);
        $status_array = Consignment::$status_array;
        $servicedata = [];
        foreach ($data as $serviceName => $statusValues) {
            $tmp = '{
                        "service": "' . $serviceName . '",
                        "total": "' . array_sum($statusValues) . '",';
            foreach ($shipmentStatusArr as $statusId) {
                $statusName = ucwords(strtolower($status_array[$statusId]));
                $shipments = isset($statusValues[$statusId]) ? $statusValues[$statusId] : 0;
                $tmp .= '"' . $statusName . '": "' . $shipments . '",';
            }
            $tmp .= '}';
            $servicedata[] = $tmp;
        }
        $this->service_performance_report_data = implode(",", $servicedata);
        $count = 1;
        foreach ($shipmentStatusArr as $statusId) {
            $statusName = ucwords(strtolower($status_array[$statusId]));
            $this->service_performance_graph_data .= ',{
                                                            "id": "graph' . ++$count . '",
                                                            "balloonText": "<span style=\'font-size:12px;\'>[[title]] in [[category]]:<br><span style=\'font-size:20px;\'>[[value]]</span> [[additional]]</span>",
                                                            "bullet": "round",
                                                            "lineThickness": 3,
                                                            "bulletSize": 7,
                                                            "bulletBorderAlpha": 1,
                                                            "bulletColor": "#FFFFFF",
                                                            "useLineColorForBulletBorder": true,
                                                            "bulletBorderThickness": 3,
                                                            "fillAlphas": 0,
                                                            "lineAlpha": 1,
                                                            "title": "' . $statusName . '",
                                                            "valueField": "' . $statusName . '",
                                                            "dashLengthField": "dashLengthLine"
                                                        }';
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
