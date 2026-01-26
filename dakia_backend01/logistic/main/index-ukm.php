<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings


// set up local page class
class Page extends BasePage {
    /*     * *
     * Set the page header
     * @return void
     */

    private $total_consignment_chart = array();
    private $daily_consignment_chart = array();
    private $top_countries_chart = array();
    private $top_countries_bar = array();
    private $top_services_chart = array();
    private $top_services_bar = array();
    private $total_hold_parcels = 0;
    private $total_consignment = 0;
    private $total_shipped_parcels = 0;
    private $total_delivered_parcels = 0;
    private $total_weight = 0;
    private $max_weight = 0;
    private $response_array;
    
    //Operation Dashboard
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
    private $sessionUser;
    //End Operation Dashboard
    // CS DASHBOARD
    // Delivered Shipment, IN Time Delivered Shipment And Out Time Delivered Shipment
    private $pieChartDeliveredShipment;
    private $pieChartTopFiveGoodMonth = [];
    private $pieChartTopFiveGoodWeek = [];
    private $pieChartTopFiveGoodDay = [];
    private $pieChartTopFiveWorseDay = [];
    private $pieChartTopFiveWorseMonth = [];
    private $pieChartTopFiveWorseWeek = [];
    private $pieChartDeliveredShipmentToday = [];
    private $pieChartDeliveredShipmentWeek = [];
    private $pieChartDeliveredShipmentMonth = [];


    public function getTitle() {
        return "Admin - Index";
    }
	/*     * *
     * This page's content
     * @return void
     */
    public function renderMenuUpItem(){
                        $Sessionuser = SessionManager::getUser();
			if(isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
				$displayOption 	=	@$_SESSION['menu-option'];	
			else
				$displayOption 	=		$Sessionuser->getUserType() ;	

			switch ($displayOption) {
				case User::USER_TYPE_CUSTOMER_SERVICE : ?>
				<li  class="dropdown dropdown-registeruser" >
				    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><i class="fa fa-user"></i> Users
                    <span class="label label-danger" id="total_user"></span>
				        <i class="fa fa-angle-down"></i>
				    </a>
				    <ul class="dropdown-menu dropdown-menu-default"  id="user-new-data">
			        </ul>
				</li>
				<?php 
				break;
				case User::USER_TYPE_FINANCE : ?>
				<li  class="dropdown dropdown-registeruser" >
				    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><i class="fa fa-user"></i>Users
				       <span class="label label-danger" id="total_user"></span><i class="fa fa-angle-down"></i>
				    </a>
				     <ul class="dropdown-menu dropdown-menu-default " id="user-new-data">
				    </ul>
				</li>
				<?php 
				break;
					
			}

    }
    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>

        <div class="portlet light">
            <div class="portlet-body">

<div class="row1">
    <?php
        $Sessionuser = SessionManager::getUser();
        if($this->userSession->getUserType() == "client" || ($Sessionuser->getDashboard() != "operation")){
    ?>
    <div class="col-md-3">
            <h1> APPLICATION<br/> DASHBOARD </h1> <br/><br/>
		<h4>Smart Track</h4>
        <?php if (Permissions::checkFilePermission('shipment_edit.php') || Permissions::checkFilePermission('client_list.php') ||Permissions::checkFilePermission('coclient_user_endofday.php') || Permissions::checkFilePermission('multitracking.php')): ?>
		</br></br>
		<h4>Workflow</h4>
		<ul class="nav nav-pills nav-stacked" style="max-width: 260px;">
        <?php if (Permissions::checkFilePermission('shipment_edit.php')): ?>
            <li class="active">
                <a href="shipment_edit.php?option=new">Add new shipment</a>
            </li>
        <?php endif;?>
        <?php if (Permissions::checkFilePermission('client_list.php')): ?>
            <li class="active">
                <a href="client_list.php?show=show_valid">Create labels </a>
            </li>
        <?php endif;?>
        <?php if (Permissions::checkFilePermission('coclient_user_endofday.php')): ?>
            <li class="active">
                <a href="client_list.php?create_manifest=true">Create manifest </a>
            </li>
        <?php endif;?>
        <?php if (Permissions::checkFilePermission('multitracking.php')): ?>
            <li class="active">
                <a href="multitracking.php">Tracking </a>
            </li><?php endif;?>
        </ul>
        <?php endif;?>
    </div>
    <?php } ?>
    <div class="col-md-<?php if($this->userSession->getUserType() == "client" || ($Sessionuser->getDashboard() != "operation")){ echo "9";}else{ echo "12";}?>">
        <?php
            $Sessionuser = SessionManager::getUser();
            if($Sessionuser->getDashboard() == "operation" && $Sessionuser->getUserType()!= "client"){
                include_once 'dashboard/operation.php';
            }else if($Sessionuser->getDashboard() == "customer_service" && $Sessionuser->getUserType()!= "client"){
                include_once 'dashboard/customer_serivces.php';
            }else if($Sessionuser->getDashboard() == "account" && $Sessionuser->getUserType()!= "client"){
                include_once 'dashboard/account_dashboard.php'; 
            }else{
                include("dashboard/".   $this->sessionUsersType .".php");
            }
        ?>
    </div>
</div>
<?php if( $this->userSession->getUserType() == "client" || ($Sessionuser->getDashboard() != "operation") ){ ?>
<div class="row1">
    <?php    
        require_once("auto_dashboard_panel.php");
    ?>
</div>
</div>
</div>
    <?php } 
    
//    } ?>
            <!-- END PAGE CONTENT-->
    <?php
        }
	public function renderHead() { }
	public function addPagelavelCss() {
            ?>
             <style type="text/css">
                .page-breadcrumb { display: none }
            </style>
            <?php
            //Operation Dashboard
            if($this->userSession->getDashboard() == "operation" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_css.php';
            }else if($this->userSession->getDashboard() == "customer_service" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_css.php';
            
            }else if($this->userSession->getDashboard() == "account" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_css.php';
            }
            //End Operation Dashboard
        }
	public function addPagelavelJs() { ?> 
            <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
            <?php //Operation Dashboard
            if($this->userSession->getDashboard() != "operation" && $this->userSession->getUserType() != "client"){ ?>
            <script src="../assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
            <!--<script src="../assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>-->
            <?php //Operation Dashboard 
                }?>
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
            <script type="text/javascript">
                $(document).ready(function() {
                    //Working
                    initChartTopCountries();
                    //Working
                    initShipmentStatsChart();
                    //Working
                    getTotalShipments();
                });
                //Working
                var initShipmentStatsChart = function() {
                    if (typeof(AmCharts) === 'undefined' || $('#dashboard_shipment_stats_chart').size() === 0) {
                        return;
                    }                    
                    var chartData = <?php echo $this->consignment_stats_chart_data;?>;
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
                //Working
                var initChartTopCountries = function() {
                    if (typeof(AmCharts) === 'undefined' || $('#dashboard_top_countries_chart').size() === 0) {
                        return;
                    }
                    var chartData = <?php echo $this->top_countries_chart; ?>;
                    var chart = AmCharts.makeChart( "dashboard_top_countries_chart", {
                        "type": "serial",
                        "theme": "light",
                        "dataProvider": chartData,
                        "valueAxes": [ {
                            title: "Shipments",
                            "gridColor": "#FFFFFF",
                            "gridAlpha": 0.2,
                            "dashLength": 0
                        } ], 
                        "gridAboveGraphs": true,
                        "startDuration": 1,
                        "graphs": [ {
                            "balloonText": "[[category]]: <b>[[value]]</b>",
                            "fillAlphas": 0.8,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "value"
                        } ],
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
                //TOTAL SHIPMENTS //Working
                var getTotalShipments = function() {

                    $('#ajax-total-shipment').show();
                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    
                    $.ajax({
                        url: "index.php?menu-option=" + user_type,
                        data: {
                           func: "GET_TOTAL_SHIPMENTS", user_type: user_type
                        },
                        type: "POST",
                        dataType : "json",
                        async: false,
                    })
                      // Code to run if the request succeeds (is done);
                      // The response is passed to the function
                      .done(function( json ) {
                          
                        var totalShipment   =   parseInt(json.TOTAL);
                        var totalReceived   =   parseInt(json[<?=Consignment::STATUS_LABEL_CREATED ?>]);
                        var totalShipped    =   parseInt(json[<?=Consignment::STATUS_DISPATCHED ?>]);
                        var totalDelivered  =   parseInt(json[<?=Consignment::STATUS_DELIVERED ?>]);
                        
                        var received    = (totalReceived * 100)/ totalShipment;
                        var shipment    = (totalShipped * 100)/ totalShipment;
                        var delivered   = (totalDelivered * 100)/ totalShipment;
                        
                        
                        $("#total_shipment").html(totalShipment );
                        $("#total_hold_shipment").html(totalReceived);
                        $("#total_shipped_shipment").html(totalShipped);
                        $("#total_delivered_shipment").html(totalDelivered);
                        
                        
                        $('.ajax-count-loder').hide();
                        
                        $(".total_hold_shipment_bar").attr('style','width:'+received+'%');
                        $(".total_shipped_shipment_bar").attr('style','width:'+shipment+'%');
                        $(".total_delivered_shipment_bar").attr('style','width:'+delivered+'%');
                        
                        
                        
                    })
                      // Code to run if the request fails; the raw request and
                      // status codes are passed to the function
                      .fail(function( xhr, status, errorThrown ) {
                        alert( "Sorry, there was a problem!" );
                        console.log( "Error: " + errorThrown );
                        console.log( "Status: " + status );
                        console.dir( xhr );
                    })
                      // Code to run regardless of success or failure;
                      .always(function( xhr, status ) {
                       $('#ajax-total-shipment').hide();
                   });
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
        //Operation Dashboard
            if($this->userSession->getDashboard() == "operation" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_js.php';
            }else if($this->userSession->getDashboard() == "customer_service" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_js.php';
            
            }else if($this->userSession->getDashboard() == "account" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/account_dashboard_js.php';
            }
            //End Operation Dashboard
        }
        
       public function renderFooter() {
            ?>
            <style type="text/css">
                .page-breadcrumb { display: none }
            </style>
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

        /*         * *
         * Controller logic goes here
         */

            public function init() {
            $this->breadCrumb['data'] = array(
                                    'index.php'=>Translation::GetCaption("HOME"),
                                );
            $this->userSession = $userSection = SessionManager::getUser();
            $this->sessionUsersType   =   str_replace(' ', '_', $userSection->getUserType());
            //Operation Dashboard
            if($this->userSession->getDashboard() == "operation" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/operation_init.php';
            }else if($this->userSession->getDashboard() == "customer_service" && $this->userSession->getUserType() != "client"){
                include_once 'dashboard/customer_services_init.php';
            }
            //End Operation Dashboard
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
                $data = util_get("data");
                if (trim($data) && $data != '') {
                    util_redirect("/main/login.php?data=" . $data);
                }else {
                    util_redirect("/main/login.php");
                }
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
                }
                die;
            }
            // Total Consignment
            if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_TOTAL_SHIPMENTS') {
                $total_consignment = 0;
                $user_type = $this->userSession->getUserType();
                $cFilter = new ConsignmentFilter();
                if ($user_type == User::USER_TYPE_CLIENT)
                    $cFilter->addFilter("     user_id = '" . $userSection->getId() . "'");
                else if($user_type == User::USER_TYPE_CORPORATE)
                {
                    $cFilter->addFilter("     ( user_id = '" . $userSection->getId() . "'  OR c.user_id IN (SELECT id FROM user WHERE user_account_id = '".$userSection->getUserAccountId()."' ))");
                }
                $status_not_include = array(
                        Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_RECEIVED,
                        Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED,
                        Consignment::STATUS_PARTIAL_DISPATCHED, Consignment::STATUS_INTRANSIT,
                        Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED,
                        Consignment::STATUS_CLOSE);
                $cFilter->addStatusFilterIn($status_not_include);
                $dataTotalConsignment = $cFilter->getDashboardTotalConsignmentInfo();
                $outputArray    =   array();
                $outputArray ['TOTAL']  =   0;
                $outputArray [Consignment::STATUS_DISPATCHED]  =   0;
                $outputArray [Consignment::STATUS_LABEL_CREATED]  =   0;
                $outputArray [Consignment::STATUS_DELIVERED]  =   0;
                if (count($dataTotalConsignment) > 0) {
                    foreach($dataTotalConsignment as $shipmentStats)
                    {
                        if(in_array($shipmentStats->getShipmentStatus(),array(Consignment::STATUS_RECEIVED,
                            Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED,
                            Consignment::STATUS_PARTIAL_DISPATCHED, Consignment::STATUS_INTRANSIT)))
                            $outputArray[Consignment::STATUS_DISPATCHED] = $outputArray[Consignment::STATUS_DISPATCHED] + $shipmentStats->getId();
                        else
                            if(in_array($shipmentStats->getShipmentStatus(),array(Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED, Consignment::STATUS_CLOSE)))
                                $outputArray[Consignment::STATUS_DELIVERED] =  $outputArray[Consignment::STATUS_DELIVERED] + $shipmentStats->getId();
                        else
                            $outputArray[$shipmentStats->getShipmentStatus()] = $outputArray[$shipmentStats->getShipmentStatus()] + $shipmentStats->getId();
                        $outputArray ['TOTAL'] = $outputArray ['TOTAL']+$shipmentStats->getId();
                    }
                }
                echo json_encode($outputArray);
                die;
            }
            $consignmentFilteter = new ConsignmentFilter();
           $user_type = $this->userSession->getUserType();
            if ($user_type == User::USER_TYPE_CLIENT)
                    $consignmentFilteter->addFilter("     user_id = '" . $userSection->getId() . "'");
            else if($user_type == User::USER_TYPE_CORPORATE)
                    $consignmentFilteter->addFilter("     ( user_id = '" . $userSection->getId() . "'  OR user_id IN (SELECT id FROM user WHERE user_account_id = '".$userSection->getUserAccountId()."'))");
			$status_not_include = array(Consignment::STATUS_INVALID,Consignment::STATUS_NEW, Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_RECYCLED);
            $consignmentFilteter->addStatusFilterNotIn($status_not_include);            
            $statsValues = $consignmentFilteter->getShipmentStatusReport(15);
            $status_chart_data = array();
            if (count($statsValues) > 0) {
                foreach ($statsValues as $statsShipment) {
                    $date_booked = ($statsShipment->getDateLabelCreated() == "" || $statsShipment->getDateLabelCreated() == 0 ? date("d-m-Y"): $statsShipment->getDateLabelCreated());
                    $total_shipment = $statsShipment->getId();
                    $total_weight = $statsShipment->getWeight();
                    $total_services = $statsShipment->getServiceId();
                    $service_name = $statsShipment->getService();
                    $status_chart_data[] = '{
                                                "date": "'.$date_booked.'",
                                                "shipments": '.$total_shipment.',
                                                "serviceName": "'.$service_name.'",
                                                "serviceName2": "",
                                                "totalServices": '.$total_services.',
                                                "services": '.$total_services.',
                                                "weight": '.$total_weight.'
                                           }';
                    
                }
            }
            $this->consignment_stats_chart_data = '['.implode(",", $status_chart_data).']';
            // Top Countries
            $dataCountryValues = $consignmentFilteter->getDashboardTopCoutry(5);
            if (count($dataCountryValues) > 0) {
                $top_countries_chart = array();
                foreach ($dataCountryValues as $dataCountryList) {
                    if (trim($dataCountryList->getCountryIsoCode()) != '') {
                        $consignmentCountry = $dataCountryList->getCountryIsoCode();
                        $top_countries_chart[] = '{"country" : "' . $consignmentCountry . '", "value" : ' . $dataCountryList->getId() . '}';
                    }
                }
            }
            $this->top_countries_chart = '['.implode(",", $top_countries_chart).']';
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
