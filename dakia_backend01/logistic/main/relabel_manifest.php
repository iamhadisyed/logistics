<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'country.class',
    'parcelfilter.class',
    'warehouse.class',
    'consignment.class',
    'parcel.class',
    'trackingdata.class',
    'owereturnlabel.class',
    'consignment.class',
    'consignmentfilter.class',
    'tracking.class'
]);
?>

<?php

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $error = array();
    public $message;
    private $status_array = array();
    private $track_point_array = array();
    private $hawbNotInOurSystem;
    private $duplicate_tracking_number;
    private $user = null;

    protected function init() {
        $sessionUser = SessionManager::getUser();
        $labelReturn = [];
        $labelReturnFileLink = "";
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Relabel Manifest'
        );
        $this->user = $sessionUser;
        $userAccount = $sessionUser->getUserAccount();
        t_on(); // turn on trace for this page
        if(isset($this->form_vars["action"]) && $this->form_vars["action"] == "relabel_manifest"){
            //New logic
            $serviceIdArr = [];
            $parcelArrId = [];
            $labelArr = [];
            $dateTime = date("Y-m-d H:i:s");
            $countryId = $this->user->getCountryId();
            $country = new Country($countryId);
            $trackpoint = '';
            $countryIso3 = '';
            $warehouseName = '';
            $carrierDesc = '';
            $msg = 'Either consignment already scanned as returned or tracking number invalid';
            if (count($country) > 0)
                $countryIso3 = $country->getIso3();

            $warehouseid = $this->user->getWarehouseId();
            if ($warehouseid > 0) {
                $warehouseObj = new Warehouse($warehouseid);
                $warehouseName = $warehouseObj->getWarehouseName();
            }
            $trackpoint = $warehouseName." - ".$countryIso3;
            if(!empty($warehouseName))
                $carrierDesc = 'Returned at Sort Facility '.$trackpoint;

            $duplicateTracking = array();
            $trackingNumberArray = $this->form_vars['tracking_number'];
            $count = 0;
            $dups = array();
            foreach(array_count_values($trackingNumberArray) as $val => $c){
                if($c > 1){
                    $this->duplicate_tracking_number .= "<tr><td>".$val."</td></tr>";
                    $duplicateTracking[] = $val;
                }
            }
            $bulkTrackingArr = array_unique($trackingNumberArray);
            Manifest::manifestCreate($bulkTrackingArr,"","client",0,$warehouseid,"","");





            // Get those parcels which are already scanned at other warehouse and now scannign at some other warehouse
            $allreadyScannedParcel = [];
            $allreadyScannedConId = [];
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addTrackingNumberFilterIn("p.tracking_number",$bulkTrackingArr);
            //$parcelFilter->addFieldNotEqualFilter("track.warehouse_id", $warehouseid);
            $parcelFilter->addFieldFilter("join_con.consignment_type", "return");
            $parcelFilter->addConsignmentTableJoin();
            $parcelFilter->addTrackingDataTableJoin();
            $scannedParcelList = $parcelFilter->getColumnList("p.id,p.consignment_id,p.tracking_number,join_con.consignment_type AS parcel_type,join_con.service_id AS routing_code,track.warehouse_id AS parcel_status_code,track.status_code_id AS commoditycode");
            if(count($scannedParcelList) > 0){
                $warehouseArr = [];
//                $trackingStatusArr = [];
                foreach ($scannedParcelList as $item) {
                    if($item->getCommoditycode() == '138') {
                        $warehouseArr[] = $item->getParcelStatusCode();
                    }
                }
                foreach ($scannedParcelList as $item) {
                    if (!in_array($warehouseid, $warehouseArr)) {
                        $allreadyScannedParcel[$item->getId()] = $item->getTrackingNumber();
                        $allreadyScannedConId[$item->getId()] = $item->getConsignmentId();
                        $serviceIdArr[] = $item->getRoutingCode();
                        $parcelArrId[] = $item->getId();
                    }
                }
            }
            // Now remove already scanned parcel form submiited parcel array for further actions
            $bulkTrackingArr = array_diff($bulkTrackingArr,$allreadyScannedParcel);
            $IsAlreadyScannedParcel = false;
            if(count($allreadyScannedParcel) > 0){
                $IsAlreadyScannedParcel = true;
                $msg = 'Consignment return successfully';
                foreach ($allreadyScannedParcel as $parcelId => $parcelScanned) {
                    $parcel = new Parcel($parcelId);
                    $consignment = new Consignment($parcel->getConsignmentId());
                    $labelArr[] = '<a href="'.$consignment->getLabelFile().'" target="_blank" >Label ['.$consignment->getAwb().']</a>';
                    //Save data into tracking_data table New parcel IDs @tahir
                    $trackingData = new TrackingData();
                    $trackingData->setEntityId($parcelId);
                    $trackingData->setEntityType('parcel');
                    $trackingData->setTrackingNumber($parcelScanned);
                    $trackingData->setUserId($this->user->getId());
                    $trackingData->setTrackPoint($trackpoint);
                    $trackingData->setDateCreated($dateTime);
                    $trackingData->setStatusCodeId(138);
                    $trackingData->setIpAddress(getClientIp());
                    $trackingData->setCarrierDesc($carrierDesc);
                    $trackingData->setWarehouseId($this->user->getWarehouseId());
                    $trackingData->save();
                }
            }
            $parcelList = [];
            if(!empty($bulkTrackingArr)){
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addTrackingNumberFilterIn("p.tracking_number",$bulkTrackingArr);
                $parcelFilter->addFieldNotEqualFilter("p.parcel_status_code", Consignment::STATUS_RETURNED);
                $parcelFilter->addFieldFilter("join_con.consignment_type", "outbound");
                $parcelFilter->addConsignmentTableJoin();
                $parcelList = $parcelFilter->getColumnList("p.consignment_id,p.tracking_number,join_con.consignment_type AS parcel_type");
            }
            if(count($parcelList) > 0){
                $conParcelArr = [];
                $parcelTrackingArr = [];
                $parcelTrackingConArr = [];
                foreach ($parcelList as $parcel) {
                    $this->track_point_array[] = $parcel->getTrackingNumber();
                    $parcelArrId[] = $parcel->getId();
                    $conParcelArr[$parcel->getConsignmentId()][] = $parcel->getId();
                    $conParcelArr[$parcel->getConsignmentId()]['tracking_number'][] = $parcel->getTrackingNumber();
                    $conObjParcel = new Consignment($parcel->getConsignmentId());
                    $serviceIdArr[] = $conObjParcel->getServiceId();
                }
//                if(isset($this->form_vars['manifest']) && $this->form_vars['manifest'] == "1") {
                $labelReturn = Manifest::saveReturnManifest($parcelArrId, $serviceIdArr, $warehouseid);
                if (!empty($labelReturn['FILE'])) {
                    $labelReturnFileLink = '<a href="' . _ASSETS_URL . "manifest/pdf/" . $labelReturn['FILE'] . '" target="_blank" >Return Manifest Label</a>';
                }
//                }
                $conParcelCount = [];
                if(count($conParcelArr) > 0){
                    foreach ($conParcelArr as $consignmentId => $parcelIds) {
                        $newConId = "";
                        $conParcelCount[$consignmentId] = count($parcelIds);
                        $oldConsignment = new Consignment($consignmentId);
                        if($oldConsignment->getShipmentStatus() != Consignment::STATUS_RETURNED){
                            $parcelFilterAllParcel = new ParcelFilter();
                            $parcelFilterAllParcel->addFieldFilter("p.consignment_id", $consignmentId);
                            $parcelFilterAllParcelList = $parcelFilterAllParcel->getColumnList("p.tracking_number");
                            $oldConFullParcelTracking = [];
                            if(count($parcelFilterAllParcelList) > 0){
                                foreach ($parcelFilterAllParcelList as $parcelFilterAllParcelObj) {
                                    $oldConFullParcelTracking[] = $parcelFilterAllParcelObj->getTrackingNumber();
                                }
                            }
                            $trackingNumbers = $conParcelArr[$consignmentId]['tracking_number'];
                            $parcelFilter = new ParcelFilter();
                            $parcelFilter->addTrackingNumberFilterIn("p.tracking_number",$oldConFullParcelTracking);
                            $parcelFilter->addFieldFilter("join_con.consignment_type", "return");
                            $parcelFilter->addConsignmentTableJoin();
                            $parcelList = $parcelFilter->getColumnList("p.consignment_id,p.tracking_number,join_con.consignment_type AS parcel_type");
                            if(count($parcelList) > 0){
                                $parcelIdArr = [];
                                foreach ($parcelList as $parcelNew) {
                                    $parcelIdArr[] = $parcelNew->getId();
                                }
                                $parcelArrDiff = array_diff($parcelIds,$parcelIdArr);
                                $newConId = $parcelList[0]->getConsignmentId();
                                if(count($parcelArrDiff) > 0){
                                    foreach ($parcelArrDiff as $parcelArrDiffId) {
                                        $newParcelId = Consignment::AddReturnShipmentParcel($parcelArrDiffId,$newConId);
                                        $parcelOldObj = new Parcel($parcelArrDiffId);
                                        $parcelOldObj->setParcelStatusCode(Consignment::STATUS_RETURNED);
                                        $parcelOldObj->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_RETURNED]);
                                        $parcelOldObj->save();
                                        //Save data into tracking_data table New parcel IDs @tahir
                                        $trackingData = new TrackingData();
                                        $trackingData->setEntityId($newParcelId); // $parcelOldObj->getId()
                                        $trackingData->setEntityType('parcel');
                                        $trackingData->setTrackingNumber($parcelOldObj->getTrackingNumber());
                                        $trackingData->setUserId($this->user->getId());
                                        $trackingData->setTrackPoint($trackpoint);
                                        $trackingData->setDateCreated($dateTime);
                                        $trackingData->setStatusCodeId(138);
                                        $trackingData->setIpAddress(getClientIp());
                                        $trackingData->setCarrierDesc($carrierDesc);
                                        $trackingData->setWarehouseId($this->user->getWarehouseId());
                                        $trackingData->save();
                                    }
//                                Parcel::updateParcelTrackingNumberByConId($newConId, "");
//                                Consignment::updateTrackingNumberByConId($newConId, "");
                                }
                            }else{
                                $newConId = Consignment::AddReturnShipment($oldConsignment->getId());
//                            Parcel::updateParcelTrackingNumberByConId($newConId, "");
//                            Consignment::updateTrackingNumberByConId($newConId, "");
                                if($newConId > 0){
                                    $consignmentNew = new Consignment($newConId);
                                    $consignmentNew->setConsignmentType('return');
                                    $consignmentNew->setDateCreated(date("Y-m-d H:i:s"));
                                    foreach ($parcelIds as $parcelId) {
                                        $parcelOldObj = new Parcel($parcelId);
                                        if($parcelOldObj->getParcelStatusCode() != Consignment::STATUS_RETURNED){
                                            $parcelOldObj->setParcelStatusCode(Consignment::STATUS_RETURNED);
                                            $parcelOldObj->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_RETURNED]);
                                            $parcelOldObj->save();
                                            //Add new parcel
                                            $newParcelId = Consignment::AddReturnShipmentParcel($parcelId,$newConId);
                                            //Save data into tracking_data table
                                            $trackingData = new TrackingData();
                                            $trackingData->setEntityId($newParcelId); //$parcelId
                                            $trackingData->setEntityType('parcel');
                                            $trackingData->setTrackingNumber($parcelOldObj->getTrackingNumber());
                                            $trackingData->setUserId($this->user->getId());
                                            $trackingData->setTrackPoint($trackpoint);
                                            $trackingData->setDateCreated($dateTime);
                                            $trackingData->setStatusCodeId(138);
                                            $trackingData->setIpAddress(getClientIp());
                                            $trackingData->setCarrierDesc($carrierDesc);
                                            $trackingData->setWarehouseId($this->user->getWarehouseId());
                                            $trackingData->save();
                                        }
                                    }
                                    $consignmentNew->save();
                                }
                            }
                            // Pricing
                            //$mysqli = new mysqli(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                            //if ($mysqli->connect_errno) {
                            // $message['status'] = 'error';
                            //  $message['message'] = "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                            //}
                            //$sql = "CALL tariff($newConId,'customer', '" . $this->user->getUserAccount() . "', '" . $parameter . "',@S_STATUS,@S_MESSAGE)";
                            //if (!($res = $mysqli->query($sql))) {
                            // $message['status'] = 'error';
                            //   $message['message'] = "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
                            // } else {
                            //$message['status'] = 'success';
                            //  $message['message'] = "tariff successfully updated";
                            //}
                            $labelCon = new Consignment($newConId);
                            $returnLabel = new OweReturnLabel();
                            $returnLabel = $returnLabel->AddConsignment($labelCon);
                            $msg = 'Consignment return successfully';
                            if(isset($returnLabel['status']) && $returnLabel['status'] =="SUCCESS"){
                                if ((isset($consignmentNew)) and ($consignmentNew instanceof Consignment))
                                    $consignmentNew = new Consignment($newConId);
                                $labelLink = explode("return",$returnLabel['label']);
                                $labelLink = "return".$labelLink['1'];
                                $consignmentNew->setLabelFile($labelLink);
                                $consignmentNew->setDateScanned(time());
                                $consignmentNew->setDateLabelCreated(time());
                                $consignmentNew->setDateDelivered("");
                                $consignmentNew->setBookedFileId("");
                                $consignmentNew->setSendCourierData("");
                                $consignmentNew->save();
                                $labelArr[] = '<a href="'.$returnLabel['label'].'" target="_blank" >Label ['.$oldConsignment->getAwb().']</a>';
                            }
                        }
                        $returnCount = 0;
                        if($newConId != ""){
                            $returnCount = ConsignmentFilter::getOutBoundParcelCount($newConId,'return');
                        }
                        $outboundCount = ConsignmentFilter::getOutBoundParcelCount($consignmentId);
                        if ((isset($consignmentNew)) and ($consignmentNew instanceof Consignment))
                            $consignmentNew = new Consignment($newConId);
                        if($returnCount == $outboundCount){
                            $consignmentNew->setShipmentStatus(Consignment::STATUS_RETURNED);//int
                            $consignmentNew->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RETURNED]);//str
                        }else{
                            $consignmentNew->setShipmentStatus(Consignment::STATUS_PARTIAL_RETURNED);//int
                            $consignmentNew->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RETURNED]);//str
                        }
                        $consignmentNew->save();
                    }
                }
            }
            //Check Tracking found in system or not
            $hawbNotInOurSystem = array_diff($bulkTrackingArr, $this->track_point_array);
            if(count($hawbNotInOurSystem) > 0){
                foreach ($hawbNotInOurSystem as $hawb) {
                    if(!in_array($hawb, $duplicateTracking)){
                        $this->hawbNotInOurSystem .= "<tr><td>".$hawb."</td></tr>";
                    }
                }
            }
            echo $this->duplicate_tracking_number."---".$this->hawbNotInOurSystem."---".$msg."---".implode("<br /> ",$labelArr)."---".$labelReturnFileLink;
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "rtn_list") {
            $manifestFilter = new ManifestFilter();
            $manifestFilter->addFilter(" DATE(m.date_created) BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() ");
            $manifestFilter->addJoin("`user` u"," u.`id` "," m.`user_id` "," JOIN");
            $manifestFilter->addFieldFilter("    u.`user_account_id`",$this->user->getUserAccountId());
            /*
             * Column filter
             * For search
             */
//            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
//
//                       $manifestNumber = $this->form_vars['manifest_number'];
//                if (!empty($manifestNumber)) {
//                    $manifestFilter->addFilter("m.id = '$manifestNumber'");
//                }
//
//                $date_created_from = $this->form_vars['date_created_from'];
//                $date_created_to = $this->form_vars['date_created_to'];
//                if (!empty($date_created_from) && !empty($date_created_to)){
//                    $manifestFilter->addDateFilter($date_created_from, $date_created_to,false);
//                }
//            }

            /*
             * Set columns orders for sorting
             */
//            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
//                $dataTableColumnId = $this->form_vars['order'][0]['column'];
//                $orderBy = $this->form_vars['order'][0]['dir'];
//                $orderFalse = 'ASC';
//                if ($orderBy == 'true') {
//                    $orderFalse = 'DESC';
//                }
//
//                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
//                if($dataTableColumnName == "Manifest_number")
//                    $dataTableColumnName = 'id';
//                if($dataTableColumnName == "Manifest_number")
//                    $dataTableColumnName = 'id';
//                $manifestFilter->AddOrderBy(strtolower("m." . $dataTableColumnName), strtoupper($orderBy));
//            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end;

            $manifestFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $manifestFilter->setOffset($iDisplayStart);
            $manifestList = $manifestFilter->getList();
            $iTotalRecords = $manifestFilter->getCount();
            $setDataArr = array();
            foreach ($manifestList as $manifestListObj) {
                $manifestParcelTotal = 0;
                $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                $manifestEntityMappingFilter->addFieldFilter("    manifest_id",$manifestListObj->getId());
                $manifestParcelTotal = $manifestEntityMappingFilter->getCount();
                $currentArr = array();
                $currentArr['date_created'] = date('d-m-Y', $manifestListObj->getDateCreated());
                $currentArr['manifest_number'] = $manifestListObj->getId();
                $currentArr['parcel_total'] = $manifestParcelTotal;
                $currentArr['file'] = '<a target="_blank" class="btn blue btn-outline btn-xs" href="'.$manifestListObj->getPdfFile().'">PDF</a>';
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
    }
    protected function renderHead() {
        ?>

        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <?php
    }
    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "bulk_return.php?action=rtn_list";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            $(".table-container .custom-alerts").hide();
                            if (response.recordsTotal > 0) {
                                $("#bluk_actions").show();
                                $(".button-download-records").show();
                            } else {
                                $("#bluk_actions").hide();
                                $(".button-download-records").hide();
                            }
                        },

                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {}
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "date_created", "bSortable": false},
                                {"data": "manifest_number", "bSortable": false},
                                {"data": "parcel_total", "bSortable": false},
                                {"data": "file", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {
                            }
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            $(document).ready(function (e) {
                $("#btnSave").click(function(){
                    var trackingNumber = $("#barcodelist").val().split("\n").filter(Boolean);
                    if(trackingNumber == ""){
                        swal("","please enter tracking number", "info");
                    }else{
                        $.blockUI();
                        $.ajax({
                            type: "POST",
                            url: "relable_manifest.php",
                            data: {action: "relabel_manifest", tracking_number: trackingNumber,manifest:manifest},
                            dataType: "html",
                            success: function (data) {
                                $.unblockUI();
                                if (data != "") {
                                    var returnData = data.split("---");
                                    $("#duplicate_tracking").html("");
                                    $("#duplicate_tracking").html('<tr><td>'+returnData[0]+'</td></tr>');
                                    $("#not_found_tracking").html("");
                                    $("#not_found_tracking").html('<tr><td>'+returnData[1]+'</td></tr>');
                                    $("#return_con_label").html("");
                                    $("#return_con_label").html('<tr><td>'+returnData[3]+'</td></tr>');
                                    $("#return_manifest_label").html("");
                                    $("#return_manifest_label").html('<tr><td>'+returnData[4]+'</td></tr>');
                                    swal("",returnData[2], "info");
                                } else {
                                }
                            },
                            error: function () {
                                $.unblockUI();
                                alert('error handing here');
                            }
                        });
                    }
                });
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                DataTableFun.init();
                // if ($('.date-picker').length > 0) {
                //     //init date pickers
                //     $('.date-picker').datepicker({
                //         autoclose: true,
                //         format: "yyyy-mm-dd"
                //     });
                // }
                // $('body').on('click', '.date-picker-driver', function () {
                //     $(this).datepicker({
                //         autoclose: true,
                //     });
                // });
            });
        </script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Relabel Manifest
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="has-float-label input-icon right">
                                <i class="fa fa-building"></i>
                                <textarea class="form-control" id="barcodelist" name="barcodelist" style=''  rows="10" cols="50" rel="tooltip" placeholder="Tracking Numbers" data-original-title="Tracking Numbers"></textarea>
                                <label class="barcodelist">Tracking Numbers</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="text-align:center;">
                    <a id="btnSave"   href="#" class="btn btn-primary btn_save margin-top-10"><span></span>Create Manifest</a>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Return Manifest List
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper"></div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Date</th>
                            <th>Manifest Number</th>
                            <th>Number of Parcel</th>
                            <th>File</th>
                        </tr>
                        <!--                        <tr role="row" class="filter">-->
                        <!--                            <td>-->
                        <!--                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">-->
                        <!--                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">-->
                        <!--                                    <span class="input-group-btn">-->
                        <!--                                        <button class="btn btn-sm default" type="button">-->
                        <!--                                            <i class="fa fa-calendar"></i>-->
                        <!--                                        </button>-->
                        <!--                                    </span>-->
                        <!--                                </div>-->
                        <!--                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">-->
                        <!--                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">-->
                        <!--                                    <span class="input-group-btn">-->
                        <!--                                        <button class="btn btn-sm default" type="button">-->
                        <!--                                            <i class="fa fa-calendar"></i>-->
                        <!--                                        </button>-->
                        <!--                                    </span>-->
                        <!--                                </div>-->
                        <!--                            </td>-->
                        <!--                            <td>-->
                        <!--                                <input type="text" class="form-control form-filter" name="manifest_number" id="manifest_number"/>-->
                        <!--                            </td>-->
                        <!--                            <td> </td>-->
                        <!--                            <td> </td>-->
                        <!--                        </tr>-->
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
