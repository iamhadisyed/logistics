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
    /*     * *
     * Set the page header
     * @return void
     */

    private $consignment_stats_chart_data = "";
    private $total_consignment_chart = array();
    private $daily_consignment_chart = array();
    private $top_countries_chart = "";
    private $top_countries_bar = array();
    private $top_services_chart = array();
    private $top_services_bar = array();
    private $total_hold_parcels = 0;
    private $total_consignment = 0;
    private $total_shipped_parcels = 0;
    private $total_delivered_parcels = 0;
    private $total_weight = 0;
    private $max_weight = 0;

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>


        <!-- BEGIN PAGE HEAD-->
        <div class="page-head">
            <!-- BEGIN PAGE TITLE -->
            <div class="page-title">
                <h1>Dashboard
                    <small>statistics, charts, recent events and reports</small>
                </h1>
            </div>

            <!-- END PAGE TITLE -->
            <!-- BEGIN PAGE TOOLBAR -->
            <div class="page-toolbar">
                <!-- END THEME PANEL -->
            </div>
            <!-- END PAGE TOOLBAR -->
        </div>
        <!-- END PAGE HEAD-->
        <?php
        include("dashboard/" . $this->sessionUsersType . ".php");
    }

    public function renderHead() {
        
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
        <!--<script src="../assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/horizontal-timeline/horizontal-timeline.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script>-->
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!--<script src="../assets/pages/scripts/<?php echo $this->sessionUsersType; ?>-dashboard.js" type="text/javascript"></script>-->
        <!--<script src="../assets/pages/scripts/dashboard.js" type="text/javascript"></script>-->
        <!-- END PAGE LEVEL SCRIPTS -->


        <script type="text/javascript">
            $(document).ready(function () {
        //                    initCratTotalConsignment();
        //                    initCratDailyConsignment();
                initChartTopCountries();
        //                    initChartTopServices();
                initShipmentStatsChart();
                getTotalShipments();

                //MULTI TRACKING
                $(".start-tracking").click(function () {

                    if ($.trim($("#resnums").val()) == '') {
                        alert("<?= Translation::GetCaption("MULTITRACKING_ERROR"); ?>");
                    } else {
                        var totalNumberValue = $("#resnums").val().split("\n");
                        //alert(totalNumberValue.length);
                        if (totalNumberValue.length > 50)
                            alert("You are not allowed to track more than " + record_limit + " number at one go.");
                        else
                            $("#resTrackfrm").submit();
                    }
                });
                // GET QUOTATIOn
                $("#calculatePrice").click(function () {

                    var calc_shipping_from = $('#calc_shipping_from').val();
                    var calc_shipping_to = $('#calc_shipping_to').val();
                    var calc_weight = $('#calc_weight').val();
                    var calc_currency = "";
                    //var calc_currency       = $('#calc_currency').val();
                    var calc_length = $('#calc_length').val();
                    var calc_width = $('#calc_width').val();
                    var calc_height = $('#calc_height').val();

                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    $.post("index.php?menu-option=" + user_type, {func: "GET_TARRIF", calc_shipping_from: calc_shipping_from, calc_shipping_to: calc_shipping_to,
                        calc_weight: calc_weight, calc_currency: calc_currency, calc_length: calc_length, calc_width: calc_width, calc_height: calc_height, user_type: user_type}, function (response_data)
                    {
                        $('#screen_price').empty();
                        $('#screen_price').append(response_data);
                    });
                });

                // SHOW ALL
                $("#show_all").click(function () {
                    $("#form_action").val("show_all_index");
                    $("#frm_dashboard").submit();
                });

                // SHOW HOLD PARCELS
                $("#hold_parcel").click(function () {
                    $("#form_action").val("hold_parcels");
                    $("#frm_dashboard").submit();
                });

                // SHOW HOLD PARCELS
                $("#delivered_parcel").click(function () {
                    $("#form_action").val("delivered_parcel");
                    $("#frm_dashboard").submit();
                });

                // SHOW HOLD PARCELS
                $("#shipped_parcel").click(function () {
                    $("#form_action").val("shipped_parcel");
                    $("#frm_dashboard").submit();
                });

                // SHOW WEIGHT
                $("#weight").click(function () {
                    $("#form_action").val("weight");
                    $("#frm_dashboard").submit();
                });

                // SHOW Consignment Daily
                $("#Consignment_daily").click(function () {
                    $("#form_action").val("Consignment_daily");
                    $("#frm_dashboard").submit();
                });

                // SHOW top_countries
                $("#top_countries").click(function () {
                    $("#form_action").val("top_countries");
                    $("#frm_dashboard").attr("action", "top_countries_list.php");
                    $("#frm_dashboard").submit();
                });

                // SHOW top_services
                $("#top_services").click(function () {
                    $("#form_action").val("top_services");
                    $("#frm_dashboard").attr("action", "top_services_list.php");
                    $("#frm_dashboard").submit();
                });

            });


            var initShipmentStatsChart = function () {
                if (typeof (AmCharts) === 'undefined' || $('#dashboard_shipment_stats_chart').size() === 0) {
                    return;
                }
                var chartData = <?php echo $this->consignment_stats_chart_data; ?>;
                var chart = AmCharts.makeChart("dashboard_shipment_stats_chart", {
                    type: "serial",
                    fontSize: 12,
                    fontFamily: "Open Sans",
                    dataDateFormat: "YYYY-MM-DD",
                    dataProvider: chartData,

                    addClassNames: true,
                    startDuration: 1,
                    color: "#6c7b88",
                    marginLeft: 0,

                    categoryField: "date",
                    categoryAxis: {
                        parseDates: true,
                        minPeriod: "DD",
                        autoGridCount: false,
                        labelRotation: 45,
                        gridCount: 50,
                        gridAlpha: 0.1,
                        gridColor: "#FFFFFF",
                        axisColor: "#555555",
                        dateFormats: [{
                                period: 'DD',
                                format: 'DD-MM-YYYY'
                            }, {
                                period: 'WW',
                                format: 'MMM DD'
                            }, {
                                period: 'MM',
                                format: 'MMM'
                            }, {
                                period: 'YYYY',
                                format: 'YYYY'
                            }]
                    },

                    valueAxes: [{
                            id: "a1",
                            title: "Shipments",
                            gridAlpha: 0,
                            axisAlpha: 0
                        }, {
                            id: "a2",
                            position: "right",
                            gridAlpha: 0,
                            axisAlpha: 0,
                            labelsEnabled: false
                        }, {
                            id: "a3",
                            title: "Weight",
                            position: "right",
                            gridAlpha: 0,
                            axisAlpha: 0,
                            inside: true,
                            weight: "kg"
                        }],
                    graphs: [{
                            id: "g1",
                            valueField: "shipments",
                            title: "Shipments",
                            type: "column",
                            fillAlphas: 0.7,
                            valueAxis: "a1",
                            balloonText: "[[value]]",
                            legendValueText: "[[value]]",
                            legendPeriodValueText: "total: [[value.sum]]",
                            lineColor: "#08a3cc",
                            alphaField: "alpha",
                        }, {
                            id: "g2",
                            valueField: "services",
                            classNameField: "bulletClass",
                            title: "Services",
                            type: "line",
                            valueAxis: "a2",
                            lineColor: "#786c56",
                            lineThickness: 1,
                            legendValueText: "[[description]]",
                            descriptionField: "serviceName",
                            bullet: "round",
                            bulletSizeField: "totalServices",
                            bulletBorderColor: "#02617a",
                            bulletBorderAlpha: 1,
                            bulletBorderThickness: 2,
                            bulletColor: "#89c4f4",
                            labelText: "[[serviceName2]]",
                            labelPosition: "right",
                            balloonText: "services:[[value]]",
                            showBalloon: true,
                            animationPlayed: true,
                        }, {
                            id: "g3",
                            title: "Weight",
                            valueField: "weight",
                            type: "line",
                            valueAxis: "a3",
                            lineAlpha: 0.8,
                            lineColor: "#e26a6a",
                            balloonText: "[[value]] kg",
                            lineThickness: 1,
                            legendValueText: "[[value]] kg",
                            bullet: "square",
                            bulletBorderColor: "#e26a6a",
                            bulletBorderThickness: 1,
                            bulletBorderAlpha: 0.8,
                            dashLengthField: "dashLength",
                            animationPlayed: true
                        }],

                    chartCursor: {
                        zoomable: false,
                        categoryBalloonDateFormat: "DD",
                        cursorAlpha: 0,
                        categoryBalloonColor: "#e26a6a",
                        categoryBalloonAlpha: 0.8,
                        valueBalloonsEnabled: false
                    },
                    legend: {
                        bulletType: "round",
                        equalWidths: false,
                        valueWidth: 120,
                        useGraphSettings: true,
                        color: "#6c7b88"
                    }
                });
            }




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
            var initCratDailyConsignment = function () {
                if ($('#daily_consignment_statistics').size() != 0) {
                    $('#daily_consignment_statistics_loading').hide();
                    $('#daily_consignment_statistics_content').show();

                    var daily_consignment = [
        <?php echo implode(",", $this->daily_consignment_chart); ?>
                    ];
                    var daily_plot_statistics = $.plot($("#daily_consignment_statistics"),
                            [{
                                    data: daily_consignment,
                                    lines: {
                                        fill: 0.2,
                                        lineWidth: 0,
                                    },
                                    color: ['#BAD9F5']
                                }, {
                                    data: daily_consignment,
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
                                    data: daily_consignment,
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

                    var previousPoint = null;
                    $("#daily_consignment_statistics").bind("plothover", function (event, pos, item) {
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
            var initChartTopCountries = function () {
                if (typeof (AmCharts) === 'undefined' || $('#dashboard_top_countries_chart').size() === 0) {
                    return;
                }
                var chartData = <?php echo $this->top_countries_chart; ?>;
                var chart = AmCharts.makeChart("dashboard_top_countries_chart", {
                    "type": "serial",
                    "theme": "light",
                    "dataProvider": chartData,
                    "valueAxes": [{
                            "gridColor": "#FFFFFF",
                            "gridAlpha": 0.2,
                            "dashLength": 0
                        }],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [{
                            "balloonText": "[[category]]: <b>[[value]]</b>",
                            "fillAlphas": 0.8,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "value"
                        }],
                    "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                    },
                    "categoryField": "country",
                    "categoryAxis": {
                        "gridPosition": "start",
                        //"labelRotation": 45,
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20
                    },
                    "export": {
                        "enabled": true
                    }

                });
            }

            //TOTAL SHIPMENTS
            var getTotalShipments = function () {

                $('#ajax-total-shipment').show();
                var user_type = "<? echo $_SESSION['menu-option']; ?>";

                $.ajax({
                    url: "index.php?menu-option=" + user_type,
                    data: {
                        func: "GET_TOTAL_SHIPMENTS", user_type: user_type
                    },
                    type: "POST",
                    dataType: "json",
                    async: false,
                })
                        // Code to run if the request succeeds (is done);
                        // The response is passed to the function
                        .done(function (json) {

                            var totalShipment = parseInt(json.TOTAL);
                            var totalReceived = parseInt(json[<?= Consignment::STATUS_LABEL_CREATED ?>]);
                            var totalShipped = ( parseInt(json[<?= Consignment::STATUS_DISPATCHED ?>]) +  parseInt(json[<?= Consignment::STATUS_INTRANSIT ?>]));
                            var totalDelivered = parseInt(json[<?= Consignment::STATUS_DELIVERED ?>]);

                            var received = (totalReceived * 100) / totalShipment;
                            var shipment = (totalShipped * 100) / totalShipment;
                            var delivered = (totalDelivered * 100) / totalShipment;


                            $("#total_shipment").html(totalShipment);
                            $("#total_hold_shipment").html(totalReceived);
                            $("#total_shipped_shipment").html(totalShipped);
                            $("#total_delivered_shipment").html(totalDelivered);


                            $('.ajax-count-loder').hide();

                            $(".total_hold_shipment_bar").attr('style', 'width:' + received + '%');
                            $(".total_shipped_shipment_bar").attr('style', 'width:' + shipment + '%');
                            $(".total_delivered_shipment_bar").attr('style', 'width:' + delivered + '%');



                        })
                        // Code to run if the request fails; the raw request and
                        // status codes are passed to the function
                        .fail(function (xhr, status, errorThrown) {
                            alert("Sorry, there was a problem!");
                            console.log("Error: " + errorThrown);
                            console.log("Status: " + status);
                            console.dir(xhr);
                        })
                        // Code to run regardless of success or failure;
                        .always(function (xhr, status) {
                            $('#ajax-total-shipment').hide();
                        });
            }



            var initChartTopServices = function () {
                var chart = AmCharts.makeChart("top_services_chart", {
                    "type": "pie",
                    "theme": "light",
                    "fontFamily": 'Open Sans',
                    "color": '#888',
                    "dataProvider": [<?php echo implode(",", $this->top_services_chart); ?>],
                    "valueField": "value",
                    "titleField": "service",
                    "outlineAlpha": 0.4,
                    "depth3D": 15,
                    "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                    "angle": 30,
                    "exportConfig": {
                        menuItems: [{
                                icon: '/lib/3/images/export.png',
                                format: 'png'
                            }]
                    }
                });

                jQuery('.top_services_chart_input').off().on('input change', function () {
                    var property = jQuery(this).data('property');
                    var target = chart;
                    var value = Number(this.value);
                    chart.startDuration = 0;

                    if (property == 'innerRadius') {
                        value += "%";
                    }

                    target[property] = value;
                    chart.validateNow();
                });

                $('#top_services_chart').closest('.portlet').find('.fullscreen').click(function () {
                    chart.invalidateSize();
                });
            }
            function calculateChargeableWeight()
            {
                var weight = $('#calc_weight').val();
                var length = $('#calc_length').val();
                var width = $('#calc_width').val();
                var height = $('#calc_height').val();
                $('#_calc_width').val(width);
                $('#_calc_length').val(length);
                $('#_calc_height').val(height);
                var vol_weight = (length * width * height) / 5000;

                if (vol_weight > weight)
                {
                    $('#calc_chargeable_weight').val(vol_weight);
                } else
                {
                    $('#calc_chargeable_weight').val(weight);
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
        );
        $this->userSession = $userSection = SessionManager::getUser();
        $this->sessionUsersType = str_replace(' ', '_', $userSection->getUserType());
        if (!isset($_POST['func']) && $userSection->getDefaultLang() != 'en-GB' && $userSection->getDefaultLang() != '') {
            $lang_selected = util_get("lang");
            $lang_session = trim(@$_SESSION['lang']);
            $lang_default = trim($userSection->getDefaultLang());

            if (trim($lang_session) != '' && $lang_selected != '' && $lang_session != $lang_selected) {
                $_SESSION['lang'] = $lang_selected;
                util_redirect("../main/index.php?lang=" . trim($lang_selected));
            } else if (trim($lang_session) != '') {
                
            } else if (trim($lang_default) != '') {
                $_SESSION['lang'] = $lang_default;
                util_redirect("../main/index.php?lang=" . $lang_default);
            }
        }
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            $data = util_get("data"); //$_SESSION["user_account"];
            if (trim($data) && $data != '')
                util_redirect(SETTING_MAIN_URL . "main/login.php?data=" . $data);
            else
                util_redirect(SETTING_MAIN_URL . "main/login.php");
        }

        $redirectToDb = false;
        if ($userSection->getUserType() != USER::USER_TYPE_CLIENT && !isset($_SESSION['menu-option']))
            $redirectToDb = true;
        else
            $redirectToDb = false;

        if (util_get("menu-option") != '') {
            @$_SESSION['menu-option'] = util_get("menu-option");
        } else if (@$_SESSION['menu-option'] != util_get("menu-option"))
            @$_SESSION['menu-option'] = util_get("menu-option");
        else
            @$_SESSION['menu-option'] = $userSection->getUserType();

        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_TARRIF') {
            $Sessionuser = SessionManager::getUser();
            $calc_shipping_from = $_REQUEST["calc_shipping_from"];
            $calc_shipping_to = $_REQUEST["calc_shipping_to"];
            $calc_weight = $_REQUEST["calc_weight"];
            $calc_currency = $_REQUEST["calc_currency"];
            $calc_length = $_REQUEST["calc_length"];
            $calc_width = $_REQUEST["calc_width"];
            $calc_height = $_REQUEST["calc_height"];


            if ($calc_shipping_from != '' && $calc_shipping_to != '' && $calc_weight != '') {

                if ($calc_width > 0 && $calc_height > 0 && $calc_length > 0) {
                    $_calc_width = $calc_width;
                    $_calc_height = $calc_height;
                    $_calc_length = $calc_length;
                    $vol_weight = ($calc_width * $calc_height * $calc_length) / 5000;
                    if ($vol_weight > $calc_weight)
                        $chargeable_weight = $vol_weight;
                    else
                        $chargeable_weight = $calc_weight;
                }
                else {
                    $chargeable_weight = $calc_weight;
                }



                $consignmentinformation = array(
                    "apiKey" => $Sessionuser->getApiKey(),
                    "apiSecert" => $Sessionuser->getApiSecert(),
                    "fromCountryIso" => $calc_shipping_from,
                    "toCountryIso" => $calc_shipping_to,
                    "Weight" => $chargeable_weight,
                    "Peices" => '1',
                    "requestedCurrency" => $Sessionuser->getBillingCurrency(),
                    "platform" => '',
                    "shipment_type" => '',
                    "postcode" => '');



                $json = json_encode($consignmentinformation);

                $client = new SoapClient(null, array('location' => "https://oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl",
                    'uri' => "https://oneworldexpress.co.uk/remote/main/index.php"));

                $results = $client->__soapCall('GetTariffCodeByParams', array('consignmentinformation' => $json, 'dataType' => 'JSON'));

                $array = ConvertXMLToArray::XML2Array($results);


                $response_array = $array['Source']['Response'];
                if (sizeof($response_array) > 0) {
                    if ($response_array['Status'] == "Success") {
                        $tarrif_list = $response_array['Tariffs'];

                        $current_tarrif = array();
                        $i = 0;
                        if (isset($tarrif_list['Service'])) {
                            $current_tarrif[] = number_format(round($tarrif_list['Tarrif']), 2);
                        } else {
                            foreach ($tarrif_list as $tarrifs) {
                                $current_tarrif[] = number_format(round($tarrifs['Tarrif']), 2);
                            }
                        }
                        if (sizeof($current_tarrif) > 0) {
                            echo $tarrif = min($current_tarrif);
                        } else {
                            echo "No Tariff Found";
                        }
                    } else {
                        echo $response_array["Detail"];
                    }
                }


                /* 	$response = $response_array["Source"]["Response"];

                  if(sizeof($response) > 0)
                  {
                  $status = $response['Status'] ;
                  $response = $response["Tariffs"];
                  if($status == "Success")
                  {
                  echo $tarrif = $response["Tarrif"];
                  }
                  else
                  {
                  echo $tarrif = $response["Detail"];
                  }
                  } */
            }
            die;
        }

        // Total Consignment
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_TOTAL_SHIPMENTS') {

            $total_consignment = 0;
            $user_type = $this->userSession->getUserType();
            $cFilter = new ConsignmentFilter();
            //if ($user_type == User::USER_TYPE_CLIENT)
            $cFilter->addFilter("user_id = '" . $userSection->getId() . "'");
            $status_not_include = array(Consignment::STATUS_DISPATCHED, Consignment::STATUS_INTRANSIT, Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_DELIVERED);
            $cFilter->addStatusFilterIn($status_not_include);
            $dataTotalConsignment = $cFilter->getColumnList('id');
            $dataTotalConsignment = $cFilter->getDashboardTotalConsignmentInfo();

            $outputArray = array();
            $outputArray ['TOTAL'] = 0;
            $outputArray [Consignment::STATUS_DISPATCHED] = 0;
            $outputArray [Consignment::STATUS_LABEL_CREATED] = 0;
            $outputArray [Consignment::STATUS_INTRANSIT] = 0;
            $outputArray [Consignment::STATUS_DELIVERED] = 0;
            if (count($dataTotalConsignment) > 0) {

                foreach ($dataTotalConsignment as $shipmentStats) {
                    $outputArray[$shipmentStats->getShipmentStatus()] = $shipmentStats->getId();
                    $outputArray ['TOTAL'] = $outputArray ['TOTAL'] + $shipmentStats->getId();
                }
            }

            echo json_encode($outputArray);
            die;
            //   $this->total_weight = $dataConsignment->getWeight();
        }


        $consignmentFilteter = new ConsignmentFilter();

        //if($userSection->getUserType() != User::USER_TYPE_ADMIN)
        //$consignmentFilteter->addFilter("c.user_id = '" . $userSection->getid() . "'");
        //	$consignmentFilteter->addAccountFilter($userSection->getUserAccount());

        $status_not_include = array('11', '12', '23');
        $consignmentFilteter->addStatusFilterNotIn($status_not_include);
        $statsValues = $consignmentFilteter->getShipmentStatusReport();
        $status_chart_data = array();

        if (count($statsValues) > 0) {

            foreach ($statsValues as $statsShipment) {
                $date_booked = ($statsShipment->getDateLabelCreated() == "" || $statsShipment->getDateLabelCreated() == 0 ? date("d-m-Y"): $statsShipment->getDateLabelCreated());
                $total_shipment = $statsShipment->getId();
                $total_weight = $statsShipment->getWeight();
                $total_services = $statsShipment->getServiceId();
                $service_name = $statsShipment->getService();

                $status_chart_data[] = '{
                                                "date": "' . $date_booked . '",
                                                "shipments": ' . $total_shipment . ',
                                                "serviceName": "' . $service_name . '",
                                                "serviceName2": "",
                                                "totalServices": ' . $total_services . ',
                                                "services": ' . $total_services . ',
                                                "weight": ' . $total_weight . '
                                           }';
            }
        }

        $this->consignment_stats_chart_data = '[' . implode(",", $status_chart_data) . ']';
        // Top Countries
        $dataCountryValues = $consignmentFilteter->getDashboardTopCoutry(5);

        if (count($dataCountryValues) > 0) {
            $top_countries_chart = array();
            foreach ($dataCountryValues as $dataCountryList) {
                if (trim($dataCountryList->getCountryIsoCode()) != '') {
                    //$consignmentCountry = ucwords(strtolower($dataCountryList->getCompany()));
                    $consignmentCountry = $dataCountryList->getCountryIsoCode();
                    $top_countries_chart[] = '{"country" : "' . $consignmentCountry . '", "value" : ' . $dataCountryList->getId() . '}';
                }
            }
        }
        $this->top_countries_chart = '[' . implode(",", $top_countries_chart) . ']';
        // Top Services
        $dataServicesValues = $consignmentFilteter->getDashboardTopServices();
        if (count($dataServicesValues) > 0) {
            foreach ($dataServicesValues as $dataServiceList) {
                if (trim($dataServiceList->getServiceType()) != '') {
                    $consignmentService = $dataServiceList->getServiceType();
                    $this->top_services_bar[ucwords($consignmentService)] = $dataServiceList->getId();
                    $this->top_services_chart[] = '{"service" : "' . ucwords($consignmentService) . '", "value" : ' . $dataServiceList->getId() . '}';
                }
            }
        } else
            $this->top_services_chart[] = '{"service" : "No Country Found", "value" : 0}';
        // Max  Weight
        $dataHeighestConsignment = $consignmentFilteter->getDashboardHighestConsignmentWeight();
        if (count($dataHeighestConsignment) > 0) {
            $dataHeighWeightConsignment = $dataHeighestConsignment[0];
            $this->max_weight = $dataHeighWeightConsignment->getWeight();
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
