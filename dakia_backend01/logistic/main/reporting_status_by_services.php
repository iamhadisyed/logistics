<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $services_list;
    private $service;
    private $date_from;
    private $date_to;
    private $serach_by;
    private $report_data;
    private $total_consignment_chart;
    private $total_consignment_tbl;
    private $isProduct;
    private $serviceOrProduct;

    /*     * *
     * Set the page header
     * @return void
     */

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-8"> 
                <h3 class="page-title">Service Status Report</h3>
            </div>
            <div class="col-md-4"> 
                <!--<a href="reporting_user_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a>-->
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="row">                        
                    <div class="col-md-2">
                        <label class="control-label">Date From</label>
                        <div class="form-group  date-date-pic">
                            <input class="form-control" name="date_from" id="date_from" type="text" placeholder="Date Form" value="<?php echo @$this->date_from; ?>" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Date To</label>
                        <div class="form-group  date-date-pic">
                            <input class="form-control" name="date_to" id="date_to" type="text" placeholder="Date To" value="<?php echo @$this->date_to; ?>"  />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Service/Product</label>
                            <select name="service" id="service" class="form-control">
                                <?php
//                                if (count($this->services_list) > 0) {
//                                    foreach ($this->services_list as $serviceObj) {
//                                        $serviceCode = trim($serviceObj->getCode());
//                                        $serviceName = trim($serviceObj->getName());
//                                        $carrier = trim($serviceObj->getCarrier());
//                                        echo '<option value="' . $serviceCode . '" class="' . $carrier . '"' . ($serviceCode == $this->service ? ' selected="selected"' : '') . '>' . $serviceName . '</option>';
//                                    }
//                                }
                                ?>
                                <?php
                                foreach ($this->serviceOrProduct as $data) {
                                    $DDL_Value = '';
                                    $DDL_Text = '';
                                    if ($this->isProduct == 'YES') {
                                        $DDL_Value = $data->getRoutingName();
                                        $DDL_Text = $data->getRoutingName();
                                    } else if ($this->isProduct == 'NO') {
                                        $DDL_Value = $data->getCarrier();
                                        $DDL_Text = $data->getServiceName();
                                    }
                                    ?>
                                    <option value="<?php echo $DDL_Value; ?>" <?php echo ($DDL_Value == $this->service ? ' selected="selected"' : '');?>><?php echo $DDL_Text; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Search Shipments</label><br />
                        <label class="control-label"><input type="radio" name="serach_by" value="printed" class="form-control"<?php echo ($this->serach_by == 'printed' ? ' checked="checked"' : ''); ?>> Printed</label>
                        <label class="control-label"><input type="radio" name="serach_by" value="shipped" class="form-control"<?php echo ($this->serach_by == 'shipped' ? ' checked="checked"' : ''); ?>> Shipped</label>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">&nbsp;</label><br />
                        <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart"></i>Result
                </div>
                <?php
                if ($this->report_data > 0) {
                    ?>
                    <div class="actions">
                        <form name="exportfrm" id="exportfrm" method="post" action="">
                            <button class="btn btn-default" name="exportcsv" type="submit"><i class="fa fa-save margin-right-10"></i>Export CSV</button>
                        </form>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <!-- BEGIN PORTLET-->
                        <div class="portlet light ">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-bar-chart font-green-sharp hide"></i>
                                    <span class="caption-subject font-green-sharp bold uppercase">Shipment</span>
                                    <span class="caption-helper">Total stats...</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="consignment_statistics_loading">
                                    <img src="../_assets/admin/layout/img/loading.gif" alt="loading"/>
                                </div>
                                <div id="consignment_statistics_content" class="display-none">
                                    <div id="consignment_statistics" class="chart">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PORTLET-->
                    </div>
                    <hr />
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>Status</th>
                                <th>total</th>
                            </tr>
        <?php echo $this->total_consignment_tbl; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
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

    public function renderHead() {
        ?>        
        <script type="text/javascript">
            $(document).ready(function () {
                $("#date_from").datepicker();
                $("#date_to").datepicker();
                initCratTotalConsignment();
            });
            var initCratTotalConsignment = function () {
                if ($('#consignment_statistics').size() != 0) {
                    $('#consignment_statistics_loading').hide();
                    $('#consignment_statistics_content').show();

                    var consignment = [
        <?php echo implode(",", $this->total_consignment_chart); ?>
                    ];

                    var plot_statistics = $.plot($("#consignment_statistics"),
                            [{
                                    data: consignment,
                                    lines: {
                                        fill: 0.6,
                                        lineWidth: 0
                                    },
                                    color: ['#f89f9f']
                                }, {
                                    data: consignment,
                                    points: {
                                        show: true,
                                        fill: true,
                                        radius: 5,
                                        fillColor: "#f89f9f",
                                        lineWidth: 3
                                    },
                                    color: '#fff',
                                    shadowSize: 0
                                }],
                            {
                                xaxis: {
                                    tickLength: 0,
                                    tickDecimals: 0,
                                    mode: "categories",
                                    min: 0,
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
                                    hoverable: true,
                                    clickable: true,
                                    tickColor: "#eee",
                                    borderColor: "#eee",
                                    borderWidth: 1
                                }
                            });

                    var previousPoint = null;
                    $("#consignment_statistics").bind("plothover", function (event, pos, item) {
                        $("#x").text(pos.x.toFixed(2));
                        $("#y").text(pos.y.toFixed(2));
                        if (item) {
                            if (previousPoint != item.dataIndex) {
                                previousPoint = item.dataIndex;
                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                        y = item.datapoint[1].toFixed(2);
                                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' Consignments');
                            }
                        } else {
                            $("#tooltip").remove();
                            previousPoint = null;
                        }
                    });
                }
            }
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
        </script>
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $servicep = '';
        $date_fromp = '';
        $date_top = '';
        $serach_byp = '';

        if (isset($_POST['exportcsv'])) {
            $report_data = $_SESSION['SERVICE_STATUS_REPORTING']['REPOET_DATA'];
            $serv = $_SESSION['SERVICE_STATUS_REPORTING']['SERVICE'];
            $d_f = $_SESSION['SERVICE_STATUS_REPORTING']['DATE_FROM'];
            $d_t = $_SESSION['SERVICE_STATUS_REPORTING']['DATE_TO'];

            $csv = '';
            $csv .= 'Status Report Of ' . $serv . ' from ' . $d_f . ' to ' . $d_t . "\r\n\r\n";

            $csv .= 'Status, Shipment' . "\r\n";
            if (count($report_data) > 0) {
                foreach ($report_data as $dataList) {
                    $consignmentStatus = $dataList->getConsignmentStatus();
                    if (trim($consignmentStatus) != '') {
                        $status = ucwords(Consignment::$status_array[$consignmentStatus]);
                        $csv .= $status . ',' . $dataList->getId() . "\r\n";
                    }
                }
            }
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="status_report_' . $serv . '_' . $d_f . '-' . $d_t . '.csv"');
            echo $csv;
            exit;
        }

        $servicesFilterObj1 = new ServiceFilter();
        $this->services_list = $servicesFilterObj1->getColumnList('DISTINCT `code`, `name`, `carrier`');



        if (isset($_POST['service'])) {
            $servicep = $_POST['service'];
            $_SESSION['SERVICE_STATUS_REPORTING']['SERVICE'] = $servicep;
        } else if (isset($_SESSION['SERVICE_STATUS_REPORTING']['SERVICE'])) {
            $servicep = $_SESSION['SERVICE_STATUS_REPORTING']['SERVICE'];
        }
        $this->service = $servicep;

        if (isset($_POST['date_from'])) {
            $date_fromp = $_POST['date_from'];
            $_SESSION['SERVICE_STATUS_REPORTING']['DATE_FROM'] = $date_fromp;
        } else if (isset($_SESSION['SERVICE_STATUS_REPORTING']['DATE_FROM'])) {
            $date_fromp = $_SESSION['SERVICE_STATUS_REPORTING']['DATE_FROM'];
        }
        $this->date_from = $date_fromp;

        if (isset($_POST['date_to'])) {
            $date_top = $_POST['date_to'];
            $_SESSION['SERVICE_STATUS_REPORTING']['DATE_TO'] = $date_top;
        } else if (isset($_SESSION['SERVICE_STATUS_REPORTING']['DATE_TO'])) {
            $date_top = $_SESSION['SERVICE_STATUS_REPORTING']['DATE_TO'];
        }
        $this->date_to = $date_top;

        if (isset($_POST['serach_by'])) {
            $serach_byp = $_POST['serach_by'];
            $_SESSION['SERVICE_STATUS_REPORTING']['SEARCH_BY'] = $serach_byp;
        } else if (isset($_SESSION['SERVICE_STATUS_REPORTING']['SEARCH_BY'])) {
            $serach_byp = $_SESSION['SERVICE_STATUS_REPORTING']['SEARCH_BY'];
        }
        $this->serach_by = $serach_byp;

        if ($this->serach_by == "")
            $this->serach_by = 'printed';

        if (empty($this->date_from))
            $this->date_from = date("d-m-Y");
        if (empty($this->date_to))
            $this->date_to = date("d-m-Y");


        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }

        $this->setTitle("Admin - Service Status Report");
        /* ------------------------------------------------------------------------------ */
        // get report
        $dt_from = new DateTime($this->date_from);
        $db_date_from = $dt_from->format('Y-m-d');

        $dt_to = new DateTime($this->date_to);
        $db_date_to = $dt_to->format('Y-m-d');

        $userSection = SessionManager::getUser();
        $this->report_filter = new ConsignmentFilter();
        $this->report_filter->addAccountFilter($userSection->getUserAccount());
        $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
        $this->report_filter->addStatusFilterNotIn($status_not_include);
        $this->report_filter->addDateFilter($db_date_from, $db_date_to, $this->serach_by);
        $this->report_filter->addFieldFilter('handling', $this->service);
        $this->report_data = $this->report_filter->getDashboardReport_A();

        $_SESSION['SERVICE_STATUS_REPORTING']['REPOET_DATA'] = $this->report_data;

        if (count($this->report_data) > 0) {
            foreach ($this->report_data as $dataList) {
                $consignmentStatus = $dataList->getConsignmentStatus();
                if (trim($consignmentStatus) != '') {
                    $status = ucwords(Consignment::$status_array[$consignmentStatus]);
                    $this->total_consignment_tbl .= '<tr><td>' . $status . '</td><td>' . $dataList->getId() . '</td></tr>';
                    $this->total_consignment_chart[] = "['" . $status . "',   " . $dataList->getId() . "]";
                }
            }
        } else {
            $this->total_consignment_tbl .= '<tr><td colspan="2">No Shipment Found</td></tr>';
            $this->total_consignment_chart[] = "['No Shipment Found',  0]";
        }

        $sessionManager = Sessionmanager::getUser();

        $user_id = $sessionManager->getId();
        $user_name = $sessionManager->getUserName();
        $user_pass = $sessionManager->getUserPass();
        $user_account = $sessionManager->getUseraccount();
        //echo '<pre>';print_r($sessionManager);echo '</pre>';
        $this->isProduct = $sessionManager->getIsProduct();
        if ($sessionManager->getIsProduct() == 'YES') {
            $RoutingUserMappingFilter = new CustomizedUserServicesRoutingFilter();
            $RoutingUserMappingFilter->addUserAccountFilter($user_account);
            $this->serviceOrProduct = $RoutingUserMappingFilter->getColumnList('routing_name');
        } else if ($sessionManager->getIsProduct() == 'NO') {
            $PartnerServicesRoutingFilter = new PartnerServicesRoutingFilter();
            $PartnerServicesRoutingFilter->addUserIdFilter($user_id);
            $this->serviceOrProduct = $PartnerServicesRoutingFilter->getUserAllowServiceList();
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>