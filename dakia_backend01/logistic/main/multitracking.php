<?php
// get settings
require_once("../includes/settings/config.inc.php");

        include_classes([   
                    'carrierservice.class'
                ],'general');
        
        include_classes([   
                  'yodeltrackingstatus.class'
                ],'labels');
        include_classes([   
                    'carrier.class',
                    'carrierfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'iaddress.class',
                    'tracking.class',
                    'consignmentrelabelfilter.class', 
                    'consignmentrelabel.class',
                    'trackingdata.class', 
                    'trackingdatafilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'services.class',
                    'parcel.class', 
                    'serviceagentmapping.class',
                    'serviceagentmappingfilter.class',
                    'parcelfilter.class',
                    'servicecountrytime.class',
                    'servicecountrytimefilter.class',
                    'serviceconstantvalue.class',
                    'serviceconstantvaluefilter.class',
                    'warehouse.class',  
                ]);
// set up local page class
class Page extends BasePage {
    /*     * *
     * Set the page header
     * @return void
     */
    var $totalNums = 0;
    public function getTitle() {
        return "Admin - Index";
    }

    public function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'multitracking.php' => 'Multitracking'
        );
        // check admin user is authenticated
        /*if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            $data = util_get("data"); //$_SESSION["user_account"];
            if (trim($data) && $data != '')
                util_redirect("../main/login.php?data=" . $data);
            else
                util_redirect("login.php");
        }*/
        if(isset($_POST['func']) && $_POST['func'] == 'get_tracking'){
            $count = $_POST['count'];
            $trackingNumber = $_POST['number'];
            $trackingNumber = cleanTrackingNo($trackingNumber);
            $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
            $trackingObj = new Tracking(); //  Calling the constructor
            $trackingResults = $trackingObj->GetTracking($trackingNumber);
            $trackingData = [];
            if($trackingResults['status'] == 'success'){
                $shipmentDetail = $trackingResults['tracking']['shipment_detail'];
                $trackingEvents = $trackingResults['tracking']['tracking_events'];
                if(count($trackingEvents) > 0){
                    $rtnArr = [];
                    foreach ($trackingEvents as $date => $trackingEvent) {
                        foreach($trackingEvent as $key => $row)
                        {
                            $time[$key]  = $row['time_12_hr'];
                        }
                        array_multisort($time, SORT_DESC, $trackingEvent);
                        $rtnArr[$date] = $trackingEvent;
                    }
                }
                $trackingEvents = $rtnArr;
                $trackingStatus = '';
                $trackingTrackPoint = '';
                $trackingStatusDesc = '';
                $trackingStatusDateTime = '';
                $trackingStatusTime12hr = '';
                $trackingSignatory = '';
                foreach($trackingEvents as $date => $eventDataArr){
                    foreach($eventDataArr as $eventData){
                        $trackingStatus = $eventData['event_content'];
                        $trackingTrackPoint = $eventData['track_point'];
                        $trackingStatusDesc = $eventData['carrier_desc'];
                        $trackingStatusDateTime = $eventData['date_time'];
                        $trackingStatusTime12hr = $eventData['time_12_hr'];
                        $trackingSignatory = ($eventData['signatory'] != '' ? ' ' . $eventData['signatory'] : '');
                        break;
                    }
                    break;
                }
                //print_r($trackingResults);
                $trackingData = [
                    'count' => $count,
                    'Tracking_Number' => preg_match('/^[a-zA-Z0-9 \d]+$/', $shipmentDetail['tracking_number']) ? $shipmentDetail['tracking_number'] : '',
                    'Hawb' => $shipmentDetail['hawb'],
                    'Company' => $shipmentDetail['company'],
                    'Service' => $shipmentDetail['service'],
                    'Origin_Country' => $shipmentDetail['origin_country'],
                    'Destination_Country' => $shipmentDetail['destination_country'],
                    'Transit_Time' => $shipmentDetail['transit_time'],
                    'Status' => $shipmentDetail['status'],
                    'trackingStatus' => $trackingStatus,
                    'trackingSignatory' => $trackingSignatory,
                    'trackingTrackPoint' => $trackingTrackPoint,
                    'trackingStatusDesc' => $trackingStatusDesc,
                    'trackingStatusDateTime' => $trackingStatusDateTime,
                    'trackingStatusTime12hr' => $trackingStatusTime12hr,
                    'trackEvents' => $trackingEvents
                ];
                $_SESSION["TRACKING_DATA"][$trackingNumber] = $trackingData;
            }else{
                $trackingData = [
                    'count' => $count,
                    'Tracking_Number' => preg_match('/^[a-zA-Z0-9 \d]+$/', $trackingNumber) ? $trackingNumber : '',
                    'Hawb' => '',
                    'Company' => '',
                    'Service' => '',
                    'Origin_Country' => '',
                    'Destination_Country' => '',
                    'Transit_Time' => '',
                    'Status' => 'Not Found',
                    'trackingStatus' => 'Not Found',
                    'trackingSignatory' => '',
                    'trackingTrackPoint' => '',
                    'trackingStatusDesc' => '',
                    'trackingStatusDateTime' => '',
                    'trackingStatusTime12hr' => '',
                    'trackEvents' => []
                ];
            }
            //print_r($trackingData);
            echo json_encode($trackingData);
            exit;
        }
        $this->rawNums = isset($_POST['nums']) ? $_POST['nums'] : '';
        $this->numsTmp = nl2br($this->rawNums);
        $this->numsArr = explode('<br />', $this->numsTmp);
        $this->nums = array();
        foreach ($this->numsArr as $num) {
            if (trim($num) != '')
                array_push($this->nums, trim($num));
        }
        $this->totalNums = count($this->nums);
        //echo $_SESSION['menu-option'] =   util_get("menu-option");
        //  echo    SessionManager::getUser()->getUserType();
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <script type="text/javascript">
            function iframeLoaded() {
                $('#idIframe').height($('.page-content-wrapper').height() + "px");
            }
        </script>
        <div class="container1">
            <!-- Page Heading/Breadcrumbs -->
            <div class="row">
                <div class="flex-box">
                    <div class="col-lg-4 col-md-12 col-xs-12">
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered text-area-multitrack" style="min-height:300px;" >
                            <h4 class="widget-thumb-heading"><?php echo Translation::GetCaption("MUTLTITRACKING_ENTER"); ?> </h4>
                            <div class="widget-thumb-wrap">
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-body-stat" data-counter="counterup">
                                        <form name="resTrackfrm" id="resTrackfrm" novalidate="" method="post" action="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea style="width: 100%;min-height: 180px;" class="full-width" placeholder="Example: 23518523742352" id="resnums" name="nums" required data-validation-required-message="Please enter a Tracking Number."><?php echo @strip_tags($this->rawNums); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-danger btn-block" id="resbtnDelete">
                                                        <i class="fa fa-trash-o"></i> <?php echo Translation::GetCaption("CLEAR"); ?>
                                                    </button>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-primary btn-block start-tracking">
                                                        <i class="fa fa-search"></i> <?php echo Translation::GetCaption("TRACK"); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12 col-xs-12" id="boxes-issues">
                        <div class="row widget-row">
                            <div class="col-md-6">
                                <!-- BEGIN WIDGET THUMB -->
                                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                    <h4 class="widget-thumb-heading"> <?php echo Translation::GetCaption("ALL"); ?> </h4>
                                    <div class="widget-thumb-wrap">
                                        <i class="widget-thumb-icon fa fa-globe" style="background-color: #234093;"></i>
                                        <div class="widget-thumb-body">
                                            <span class="widget-thumb-subtitle">Shipments</span>
                                            <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo @$this->totalNums; ?>">
                                                <?php echo @$this->totalNums; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- END WIDGET THUMB -->
                            </div>
                            <div class="col-md-6">
                                <!-- BEGIN WIDGET THUMB -->
                                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                    <h4 class="widget-thumb-heading"><?php echo Translation::GetCaption("NOT DELIVERED"); ?> </h4>
                                    <div class="widget-thumb-wrap">
                                        <i class="widget-thumb-icon fa fa-plane" style="background-color: #00b2b2;"></i>
                                        <div class="widget-thumb-body">
                                            <span class="widget-thumb-subtitle">Shipments</span>
                                            <span class="widget-thumb-body-stat" data-counter="counterup" id="knob_transit">0</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- END WIDGET THUMB -->
                            </div>
                            <div class="col-md-6">
                                <!-- BEGIN WIDGET THUMB -->
                                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                    <h4 class="widget-thumb-heading"> <?php echo Translation::GetCaption("DELIVERED"); ?> </h4>
                                    <div class="widget-thumb-wrap">
                                        <i class="widget-thumb-icon fa fa-check" style="background-color: #0B0;"></i>
                                        <div class="widget-thumb-body">
                                            <span class="widget-thumb-subtitle">Shipments</span>
                                            <span class="widget-thumb-body-stat" data-counter="counterup" id="knob_deliverd">0</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- END WIDGET THUMB -->
                            </div>
                            <div class="col-md-6">
                                <!-- BEGIN WIDGET THUMB -->
                                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                    <h4 class="widget-thumb-heading"> <?php echo Translation::GetCaption("NOT_FOUND"); ?> </h4>
                                    <div class="widget-thumb-wrap">
                                        <i class="widget-thumb-icon fa fa-times" style="background-color: #999;"></i>
                                        <div class="widget-thumb-body">
                                            <span class="widget-thumb-subtitle">Shipments</span>
                                            <span class="widget-thumb-body-stat" data-counter="counterup" id="knob_notfound">0</div>
                                    </div>
                                </div>
                                <!-- END WIDGET THUMB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <br clear="all">
        <div class="row">
            <div class="col-md-12">
                <a href="javascript:{};" title="Click to export result" class="btn btn-primary btn-right btn-export margin-bottom-10 display-none">
                    <i class="fa fa-external-link"></i>   EXPORT
                </a>
                <a href="javascript:{};" title="Click to export only last tracking status" class="btn btn-primary btn-right btn-export-last margin-bottom-10 display-none" >
                    <i class="fa fa-external-link"></i>  Export Last Status 
                </a>
                <br clear="all">
                <div class="table-responsive">
                    <table id="heading-row" class="table table-striped table-bordered table-advance table-hover" style="margin-bottom: 0 !important" >
                        <thead>
                        <tr>
                            <th class="border-right sort-all"><a href="javascript:{};" data-status="all" class="sort-result on"><i class="fa fa-globe text-default"></i> <b><? echo Translation::GetCaption("ALL"); ?></b> <span id="sp_all">(<?php echo $this->totalNums; ?>)</span></a></th>
                            <th class="border-right notfound"><a href="javascript:{};" data-status="Not Found" class="sort-result"><i class="fa fa-times text-default"></i> <b><? echo Translation::GetCaption("NOT_FOUND"); ?></b> <span id="sp_notfound">(0)</span></a></th>
                            <th class="border-right transit"><a href="javascript:{};" data-status="Transit" class="sort-result"><i class="fa fa-plane text-default"></i> <b><? echo Translation::GetCaption("TRANSIT"); ?></b> <span id="sp_transit">(0)</span></a></th>
                            <th class="border-right pickup"><a href="javascript:{};" data-status="Pick Up" class="sort-result"><i class="fa fa-flag text-default"></i> <b><? echo Translation::GetCaption("PICK_UP"); ?></b> <span id="sp_pickup">(0)</span></a></th>
                            <th class="border-right alerts"><a href="javascript:{};" data-status="Alert" class="sort-result"><i class="fa fa-exclamation-triangle text-default"></i> <b><? echo Translation::GetCaption("ALERT"); ?></b> <span id="sp_alert">(0)</span></a></th>
                            <th class="deliver"><a href="javascript:{};" data-status="Delivered" class="sort-result"><i class="fa fa-check text-default"></i> <b><? echo Translation::GetCaption("DELIVERED"); ?></b> <span id="sp_delivered">(0)</span></a></th>
                        </tr>
                        </thead>
                    </table>
                    <table id="sortTable" class="table table-striped table-bordered table-advance table-hover" style="margin-bottom: 0 !important">
                        <tr class="success">
                            <th class="col-md-1" data-sort="int">&nbsp;&nbsp;</th>
                            <th class="col-md-2" data-sort="string"><?php echo Translation::GetCaption("Tracking Number"); ?></th>
                            <th class="col-md-1 text-center" data-sort="string"><?php echo Translation::GetCaption("Origin"); ?></th>
                            <th class="col-md-1 text-center" data-sort="string"><?php echo Translation::GetCaption("DESTINATION"); ?></th>
                            <th class="col-md-5" data-sort="string"><?php echo Translation::GetCaption("DESCRIPTION"); ?></th>
                            <th class="col-md-2 text-center" data-sort="string"><?php echo Translation::GetCaption("STATUS"); ?></th>
                        </tr>
                    </table>
                </div>
                <div id="tracking_result_conatiner">
                    <?php
                    if (count($this->nums) > 0) {
                        $count = 1;
                        foreach ($this->nums as $tr_num) {
                            $tr_num = ParseTrackingNumber::Parse($tr_num);
                            ?>
                            <div class="panel panel-default track-result-panel" data-sortorder="" data-count="<?php echo $count; ?>" data-number="<?php echo strip_tags($tr_num); ?>" id="panel_<?php echo $count; ?>">
                                <div class="panel-heading">
                                    <div class="table-responsive">
                                        <table id="ptable_<?php echo $count; ?>">
                                            <tbody data-sortval="0">
                                            <tr>
                                                <td class="col-md-1 text-center" style="padding-top:6px;"><i class="fa fa-2x fa-lock"></i></td>
                                                <td class="col-md-2"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#accordion_<?php echo $count; ?>"><?php echo strip_tags($tr_num); ?></a></h4></td>
                                                <td class="col-md-1 text-center">&nbsp;</td>
                                                <td class="col-md-1 text-center">&nbsp;</td>
                                                <td class="col-md-5">&nbsp;</td>
                                                <td class="col-md-2 text-center"><i class="fa fa-spinner fa-pulse fa-2x"></i></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="accordion_<?php echo $count; ?>" class="panel-collapse collapse"> </div>
                            </div>
                            <?php
                            $count++;
                        }
                    } else {
                        ?>
                        <div class="panel panel-default track-result-panel" data-count="0">
                            <div class="panel-heading">
                                <div class="table-responsive">
                                    <table>
                                        <tbody data-sortval="0">
                                        <tr>
                                            <td class="col-md-12"><? echo Translation::GetCaption("NO_RECORD_FOUND"); ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <form name="exportexcelfrm" id="exportexcelfrm" action="export_result.php" method="post">
                    <input type="hidden" name="tracking_nums" value="<?php echo is_array($this->nums) ? strip_tags(implode("|", $this->nums)) : ''; ?>" />
                    <input type="hidden" name="export_type" id="export_type" value="detail" />
                    <input type="hidden" class="" name="download_file" id="download_file" value="">
                </form>
                <br clear="all"/>
            </div>
        </div>



    <?php
    }

    public function renderHead() {

    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/layouts/layout4/css/multitrack.css" rel="stylesheet">
    <?php
    }

    protected function addPagelavelJs() {
        ?>
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script type="text/javascript">
        var _status = [];
        _status['In Transit'] = 'Transit';
        _status['Partial Delivered'] = 'Transit';
        _status['Discrepancy'] = 'Transit';
        _status['Relabel'] = 'Transit';
        _status['Awaiting'] = 'Transit';
        _status['Delayed'] = 'Transit';
        _status['Undelivered'] = 'Transit';
        _status['Delivery Check'] = 'Transit';
        _status['Customs clearance'] = 'Transit';
        _status['Label Created'] = 'Transit';
        _status['Data Received'] = 'Transit';
        _status['Received'] = 'Transit';
        _status['Partial Received'] = 'Transit';
        _status['Arrived'] = 'Transit';

        _status['Delivered'] = 'Delivered';

        _status['Not Found'] = 'Not Found';

        _status['Collected'] = 'Pick Up';
        _status['Collection'] = 'Pick Up';
        _status['Partially Collected'] = 'Pick Up';
        _status['Picked Up'] = 'Pick Up';
        _status['Dispatched'] = 'Pick Up';
        _status['Partial Dispatched'] = 'Pick Up';

        _status['Problem'] = 'Alert';
        _status['Out for delivery'] = 'Alert';
        _status['Address Problem'] = 'Alert';
        _status['Consignee Unavailable'] = 'Alert';
        _status['Damaged Parcel'] = 'Alert';
        _status['Damaged'] = 'Alert';
        _status['Delayed'] = 'Alert';
        _status['Pending'] = 'Alert';
        _status['Not Picked Up'] = 'Alert';
        _status['Returned'] = 'Alert';
        _status['Held'] = 'Alert';
        _status['Hold'] = 'Alert';
        _status['Failure'] = 'Alert';
        _status['Label Problem'] = 'Alert';


        var statusIcon = [];
        statusIcon["Transit"] = "fa-plane";
        statusIcon["Not Found"] = "fa-times";
        statusIcon["Pick Up"] = "fa-flag";
        statusIcon["Delivered"] = "fa-check";
        statusIcon["Alert"] = "fa-warning";

        var statusColor = [];
        statusColor["Intransportation"] = "primary";
        statusColor["Transit"] = "primary";
        statusColor["Not Found"] = "danger";
        statusColor["Pick Up"] = "info";
        statusColor["Delivered"] = "success";
        statusColor["Alert"] = "warning";

        var transit = 0;
        var knob_transit = 0;
        var deliverd = 0;
        var notfound = 0;
        var pickup = 0;
        var _alert = 0;
        var record_limit = parseInt('<?= (isset($_SESSION['menu-option']) && ($_SESSION['menu-option'] == User::USER_TYPE_CUSTOMER_SERVICE)) ? 500 : 500 ?>');
        var totalTracking = $(".track-result-panel").length;
        var trackedRecords = 0;

        function getTrackingResult(num, count) {
            $.post('multitracking.php', {func: 'get_tracking', number: num, count: count}, function (data) {
                trackedRecords++;
                if (trackedRecords == totalTracking) {
                    $(".btn-export").show();
                    $(".btn-export-last").show();
                }
                var Tracking_Number = data.Tracking_Number;
                var Hawb = data.Hawb;
                var Status = null;
                var containerPanelId = "";
                containerPanelId = data.count;
                Status = (_status[data.trackingStatus] != "" ? _status[data.trackingStatus] : 'Transit');      
                if(typeof Status === "undefined") {
                    Status = "Transit";
                }
                var trackingImageCss = 'pending';
                var TrackingImageTransitText = 'Transit';
                //console.log(data.trackingStatus);
                //console.log(Status);                
                switch (Status) {
                    case 'Delivered':
                        deliverd++;
                        $("#knob_deliverd").html(deliverd);;
                        $("#sp_delivered").html('(' + deliverd + ')');
                        trackingImageCss = 'delivered';
                        TrackingImageTransitText = 'Due for Delivery';
                        break;
                    case 'Not Found':
                        notfound++;
                        $("#knob_notfound").html(notfound);
                        $("#sp_notfound").html('(' + notfound + ')');
                        trackingImageCss = 'pending';
                        break;
                    case 'Transit':
                        transit++;
                        $("#knob_transit").html(++knob_transit);
                        $("#sp_transit").html('(' + transit + ')');
                        TrackingImageTransitText = 'Due for Delivery';
                        trackingImageCss = 'transit';
                        break;
                    case 'Pick Up':
                        pickup++;
                        $("#knob_transit").html(++knob_transit);
                        $("#sp_pickup").html('(' + pickup + ')');
                        TrackingImageTransitText = 'Due for Delivery';
                        trackingImageCss = 'pickup';
                        break;
                    case 'Alert':
                        _alert++;
                        $("#knob_transit").html(++knob_transit);
                        $("#sp_alert").html('(' + _alert + ')');
                        trackingImageCss = 'sendback';
                        break;
                }
                if(data.trackingStatus == "Data Received") {
                    trackingImageCss = 'pending';
                }
                var panel_css = statusColor[Status];
                var icon_css = statusIcon[Status];

                $("#panel_" + containerPanelId).removeClass('panel-default');
                $("#panel_" + containerPanelId).addClass("panel-" + panel_css);
                if (Status)
                    var sortClass = 'sort-' + Status.replace(/ /g, '-');

                $("#panel_" + containerPanelId).addClass(sortClass);

                //console.log(historyLast);
                var eventDetail = '';
                var deliverInDays = '';
                if (data.Status == 'Delivered') {
                    deliverInDays = '<div class="text-center">( ' + data.Transit_Time + ')</div>';
                }

                $("#ptable_" + containerPanelId + " td").filter(":first").children('i').removeClass('fa-lock');
                $("#ptable_" + containerPanelId + " td").filter(":first").children('i').addClass(icon_css);

                if (Status != 'Not Found') {
                    $("#ptable_" + containerPanelId + " td").eq(2).html(data.Origin_Country);
                    $("#ptable_" + containerPanelId + " td").eq(3).html(data.Destination_Country);
                    $("#ptable_" + containerPanelId + " td").eq(4).html(data.trackingStatusDesc);
                }

                $("#ptable_" + containerPanelId + " td").eq(5).html(data.trackingStatus + deliverInDays);

                if (Status != 'Not Found') {
                    var detail = '<div class="panel-body">';
                    detail = '<div class="panel-body">';
                    detail += '<h4 class="text-danger"> <i class="fa fa-paper-plane fa-lg"></i> Tracking Number: ' + Tracking_Number;
                    detail += '<section class="schedule">';
                    detail += ' <ul id="status" class="on-' + trackingImageCss + '">';
                    detail += '     <li class="schedule-pending">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>Data Received</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += '     <li class="schedule-transit">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>Transit</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += '     <li class="schedule-pickup">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>' + TrackingImageTransitText + '</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += '     <li class="schedule-sendback">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>Alert</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += '     <li class="schedule-delivered">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>Delivered</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += '     <li class="schedule-expired">';
                    detail += '         <div>';
                    detail += '             <b><i></i></b><span><em>Expired</em></span>';
                    detail += '         </div>';
                    detail += '     </li>';
                    detail += ' </ul>';
                    detail += '</section>';
                    detail += '</h4>';
                    detail += '<table class="table-bordered">';
                    detail += '     <tr>';
                    detail += '         <td>Company: ' + data.Company + '</td>';
                    detail += '         <td>Service: ' + data.Service + '</td>';
                    detail += '         <td>Origin Country: ' + data.Origin_Country + '</td>';
                    detail += '         <td>Destination Country: ' + data.Destination_Country + '</td>';
                    //detail += '         <td>Other Data: </td>';
                    detail += '         <td>Status: ' + data.trackingStatus + '</td>';
                    detail += '         <td>Transit Time: ' + data.Transit_Time + '</td>';
                    detail += '     </tr>';
                    detail += ' </table>';
                    detail += ' <div class="timeline-centered">';

                    $.each(data.trackEvents, function (index, eventData) {
                        //var bgColor = statusColor[historyOther] != undefined ? statusColor[historyOther] : 'success';
                        var bgColor = 'success';
                        detail += '     <article class="timeline-entry">';
                        detail += '         <div class="timeline-entry-inner">';
                        detail += '             <div class="timeline-icon bg-' + bgColor + '"> <i class="entypo-feather"></i> </div>';
                        detail += '             <div class="timeline-label">\n\
                                   <table class="table margin-bottom-0">\n\
                                       <tr><th colspan="2">' + index + '</th><th>Description</th><th>Status</th></tr>';
                        $.each(eventData, function (index, eventDetail) {
                            //var eventDateTimeArr = eventDetail.date_time.split(" ");
                            var signatory = eventDetail.signatory;
                            signatory = (signatory != "" ? " to <i>"+signatory+"</i>" : "");
                            detail += '     <tr>\n\
                                                <td width="5%">' + eventDetail.time_12_hr + '</td>\n\
                                                <td width="30%">' + eventDetail.track_point + '</td>\n\
                                                <td width="55%">' + eventDetail.carrier_desc + signatory +'</td>\n\
                                                <td width="10%">' + eventDetail.event_content + '</td>\n\
                                           </tr>';
                        });
                        detail += '</table>\n\
                               </div>';
                        detail += '         </div>';
                        detail += '     </article>';
                    });
                    detail += ' </div>';
                    detail += '</div>';
                    $("#accordion_" + containerPanelId).html(detail);
                }
            }, "json");
        }

        $(document).ready(function () {
            $(".start-tracking").click(function () {
                if ($.trim($("#resnums").val()) == '') {
                    alert("<?= Translation::GetCaption("MULTITRACKING_ERROR"); ?>");
                } else {
                    var totalNumberValue = $("#resnums").val().split("\n");
                    //alert(totalNumberValue.length);
                    if (totalNumberValue.length > record_limit)
                        alert("You are not allowed to track more than " + record_limit + " number at one go.");
                    else
                        $("#resTrackfrm").submit();
                }
            });
            $(".btn-export").click(function () {
                $("#exportexcelfrm input#export_type").val("detail");
                //$("#exportexcelfrm").attr("action","shipment_status_report.php");
                $("#download_file").val('download_excel');
                $("#exportexcelfrm").submit();
            });
            $(".btn-export-last").click(function () {
                $("#exportexcelfrm input#export_type").val("last");
                $("#exportexcelfrm").submit();
            });

            $(".track-result-panel").each(function () {
                var number = $(this).data('number');
                var count = $(this).data('count');
                if (count > 0)
                    getTrackingResult(number, count);
            });

            $(".sort-result").on('click', function () {
                $(".sort-result").removeClass('on');
                $(this).addClass('on');
                $(".track-result-panel").data('sortorder', '10000');
                var status = $(this).data('status');
                status = status.replace(/ /g, '-');
                $(".sort-" + status).data('sortorder', '1');
                $(".track-result-panel").hide();
                $(".sort-" + status).show();
                if (status == 'Transit') {
                    $(".sort-Intransportation").data('sortorder', '1');
                    $(".sort-Intransportation").show();
                } else if (status == 'all') {
                    $(".track-result-panel").show();
                }
                var $result_conatiner = $('#tracking_result_conatiner');
                var $result_conatinerpanel = $result_conatiner.children('div.track-result-panel');
                $result_conatinerpanel.sort(function (a, b) {
                    var an = $(a).data("sortorder"),
                        bn = $(b).data("sortorder");
                    if (an > bn) {
                        return 1;
                    }
                    if (an < bn) {
                        return -1;
                    }
                    return 0;
                });
                $result_conatinerpanel.detach().appendTo($result_conatiner);
            });

            $('[data-toggle="tooltip"]').tooltip();

            $("#btnDelete").on('click', function (event) {
                $('#nums').val("");
            });

            $("#resbtnDelete").on('click', function (event) {
                $('#resnums').val("");
                return false;
            });

            $("#restrackNums").on('click', function (event) {
                $('#resTrackfrm').submit();
            });
        });
        </script>

    <?php
    }

    /*     * *
     * Controller logic goes here
     */
}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>