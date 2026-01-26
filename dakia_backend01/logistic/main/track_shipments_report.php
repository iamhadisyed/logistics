<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'countryfilter.class',
    'country.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'warehousefilter.class',
    'warehouse.class',
]);

class Page extends BasePage
{
    /*
     * Controller logic
     */

    private $user = "";
    private $tracking_data_filter = "";

    protected function init()
    {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Track Shipments Report"
        );
        $this->user = SessionManager::getUser();
        
//        $this->subAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);

        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "tracking_data_export"){
            $userAccountData = new CustomerAccount($this->user->getUserAccountId() );
            if($userAccountData->getParentId()>0){
                $parentUserAccountData =  new CustomerAccount($userAccountData->getParentId());
                $parentAccount = $parentUserAccountData->getUserAccount();
                
            } else {
                $parentAccount = $userAccountData->getUserAccount();
            }
            
            $TrackingDataCsv  = '';
            $outputArray = [
                'status'=>'error',
                'message'=>'<div class="alert alert-danger">There is no data available for export.</div>',
            ];
            $exportHeader = [
                        'Account',
                        'Tracking Number',
                        "Order Reference",
                        "Reference",
                        "Contact Name",
                        "Address",
                        "City",
                        "Postcode",
                        "Country",
                        "Service",
                        "Description",
                        "Number Pieces",
                        "Weight",
                        "Vol Weight",
                        'Hub', 
                        'Agent Name',
                        'Scanned By', 
                        'Date Scanned',
                        'Outbound Date Label Created',
                        'Outbound Date Dispatched'
                ];
            $wharehouseId = $this->form_vars['warehouse_id'];
            if(empty($wharehouseId)){
                $outputArray = [
                    'status'=>'error',
                    'message'=>'<div class="alert alert-danger">Please selet warehouse.</div>',
                ];    
                echo json_encode($outputArray, JSON_PARTIAL_OUTPUT_ON_ERROR);
                die;
            }
            
            $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
            $userImidiateAccountArry = CustomerAccount::getImmediateSubaccount($this->user->getUserAccountId());
            $accountObj = new CustomerAccount();
            $showAcountDataArraya = $accountObj->getSubAccountsArrayShowConsignmentAccount($this->user->getUserAccountId(),$this->form_vars['user_account_id']);
           /* echo "<pre>";
            print_r($userImidiateAccountArry);
            die;
            *///if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') 
            {
                $this->tracking_data_filter = new TrackingDataFilter();
                $this->tracking_data_filter->setRowsPerPage(5000);
                $this->tracking_data_filter->setOffset(0);
                $this->filter_form($this->form_vars);
                $trackingDataObj = $this->tracking_data_filter->getColumnList('t.*, '
                        . ' c.hawb,c.awb, c.reference, c.description, c.contact,c.company,'
                        . ' (select date_label_created from consignment where awb = c.awb and consignment_type = "outbound" limit 1) as date_label_created, '
                        . ' (select date_booked from consignment where awb = c.awb and consignment_type = "outbound" limit 1) as date_booked, '
                        . ' c.address_line_1,c.address_line_2,c.address_line_3,c.city,c.postcode,'
                        . ' con.name as countryname, s.name as service_name, s.code as service_code,'
                        . ' c.weight, c.vol_weight,c.number_pieces, ua.user_account,'
                        . ' uac.user_account as shipment_user_account,uac.id as shipment_user_account_id, '
                        . ' u.first_name, u.last_name, ad.agent_name', '', 50000);
                if(count($trackingDataObj)>0){
                $TrackingDataCsv  = implode(',',$exportHeader)."\r\n";
                    foreach ($trackingDataObj as $trackingObj) {
                        $trackPoint = '';
                        if (!empty($trackingObj->getTrackPoint())) {
                            $dataArray = explode('-', $trackingObj->getTrackPoint());
                            if (!empty($dataArray) && is_array($dataArray)) {
                                $trackPoint = $dataArray[0];
                            }
                        }
                        
                        if(in_array($trackingObj->getShipmentUserAccountId(), $userAccountArry)){
                            $shipmentAccountShow = $trackingObj->getShipmentUserAccount();
                            if (Permissions::checkFilePermission('hide_subaccount')) {
                               $accountObj = new CustomerAccount();
                                $shipmentAccountShow = $accountObj->showAccount($trackingObj->getShipmentUserAccountId(),$showAcountDataArraya,$this->form_vars['user_account_id']);
                            }
                        } else {
                            $shipmentAccountShow = $parentAccount;
                        }
                        
                        
        
        
                        $consignmentArr = [
                            cleanCsvCall($shipmentAccountShow),
                            cleanCsvCall($trackingObj->getTrackingNumber(),'int'),
                            cleanCsvCall($trackingObj->getHawb(),'int'),
                            cleanCsvCall($trackingObj->getReference()),
                            cleanCsvCall($trackingObj->getContact()),
                            cleanCsvCall($trackingObj->getAddressLine1()." ".$trackingObj->getAddressLine2()." ".$trackingObj->getAddressLine3()),
                            cleanCsvCall($trackingObj->getCity()),
                            cleanCsvCall($trackingObj->getPostcode()),
                            cleanCsvCall($trackingObj->getCountryName()),
                            cleanCsvCall($trackingObj->getServiceName()."(".$trackingObj->getServiceCode().")"),
                            cleanCsvCall($trackingObj->getDescription()),
                            cleanCsvCall($trackingObj->getNumberPieces()),
                            cleanCsvCall($trackingObj->getWeight()),
                            cleanCsvCall($trackingObj->getVolWeight()),
                            cleanCsvCall(    $trackPoint),
                            cleanCsvCall(    $trackingObj->getAgentName()),
                            cleanCsvCall(    $trackingObj->getUserAccount()),
                            cleanCsvCall(    $trackingObj->getDateAdded()),
                            cleanCsvCall(    date("d/m/Y",$trackingObj->getDateLabelCreated())),
                            (trim($trackingObj->getDateBooked()) !='' && $trackingObj->getDateBooked() > 0 ? cleanCsvCall(    date("d/m/Y",$trackingObj->getDateBooked())):'' )
                            
                            ];
                        $TrackingDataCsv  .= implode(',',$consignmentArr)."\r\n";
                    }                
                }
                if(trim($TrackingDataCsv) != ''){
                    $DOWNLOADABLE_FILE_NAME = "../_assets/csv/hub-scann-report-" . time() . ".csv";
                    file_put_contents($DOWNLOADABLE_FILE_NAME, $TrackingDataCsv);
                    $outputArray = [
                        'status'=>'success',
                        'message'=>'Please <a href="'.$DOWNLOADABLE_FILE_NAME.'" target="_blank" class="btn btn-xs btn-primary">click here</a> to download report.',
                    ];
                }
            }
            echo json_encode($outputArray, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        } else if (isset($_GET['action']) && $_GET['action'] == "tracking_data_list") {
            $this->tracking_data_filter = new TrackingDataFilter();
            /*
             * Column filter
             * For search
             */
            $trackingDataArray = array();
            $TotalNumberPieces = 0;
            $TotalWeight = 0;
            $iTotalRecords = 0;
            $sEcho = $this->form_vars['draw'];
            $getZeroPriced = 0;
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->filter_form($this->form_vars);

                /*
                 * Set columns orders for sorting
                 */
                if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                    $dataTableColumnId = $this->form_vars['order'][0]['column'];
                    $orderBy = $this->form_vars['order'][0]['dir'];
                    $orderFalse = TRUE;
                    if ($orderBy == 'desc') {
                        $orderFalse = FALSE;
                    }
                    $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                    if (trim($dataTableColumnName) == 'account')
                        $dataTableColumnName = 'user_account';
                    if (trim($dataTableColumnName) == 'service_type')
                        $dataTableColumnName = 'service_name';
                    if (trim($dataTableColumnName) == 'shipment_status')
                        $dataTableColumnName = 'consignment_status';
                    if (trim($dataTableColumnName) == 'country_iso_code')
                        $dataTableColumnName = 'country_name';
                }
                /*
                 * Set pagination & Encode data into Json form to return to DataTable
                 */

                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;

                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $this->tracking_data_filter->setRowsPerPage($iDisplayLength);
                $this->tracking_data_filter->setOffset($iDisplayStart);
                $trackingDataObj = $this->tracking_data_filter->getColumnList('t.*, ua.user_account, u.first_name, u.last_name, ad.agent_name', '', $iDisplayLength);
                $iTotalRecords = $this->tracking_data_filter->getColumnListCount();
                foreach ($trackingDataObj as $trackingObj) {
                    $trackPoint = '';
                    $consignmentArr['scanned_by'] = $trackingObj->getUserAccount();
                    $consignmentArr['agent_name'] = $trackingObj->getAgentName();
                            //. "<small>" . $trackingObj->getFirstName() . " " . $trackingObj->getLastName() . "</small>";
                    $consignmentArr['scanned_date'] = $trackingObj->getDateAdded();
                    if (!empty($trackingObj->getTrackPoint())) {
                        $dataArray = explode('-', $trackingObj->getTrackPoint());
                        if (!empty($dataArray) && is_array($dataArray)) {
                            $trackPoint = $dataArray[0];
                        }
                    }
                    $consignmentArr['hub'] = $trackPoint;
                    $consignmentArr['tracking_number'] = $trackingObj->getTrackingNumber();

                    $trackingDataArray[] = $consignmentArr;
                }
            }
            $trackingDataArr['data'] = $trackingDataArray;
            $trackingDataArr['draw'] = $sEcho;
            $trackingDataArr['recordsTotal'] = $iTotalRecords;
            $trackingDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($trackingDataArr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }
    }

    protected function filter_form($data)
    {
        $this->form_vars = $data;
        $this->tracking_data_filter->addJoin("   user u ", "   u.id = t.user_id ");
        $this->tracking_data_filter->addJoin("   customer_account ua ", "   ua.id = u.user_account_id ");
        $this->tracking_data_filter->addJoin("   parcel p ", "   p.id = t.entity_id ");
        $this->tracking_data_filter->addJoin("   consignment c ", "   c.id = p.consignment_id ");
        $this->tracking_data_filter->addJoin("   services s ", "   c.service_id = s.id ");
        $this->tracking_data_filter->addJoin("   country con ", "   c.country_id = con.id ");
        $this->tracking_data_filter->addJoin("   agent_data ad ", "   c.agent_id = ad.id ");
        $this->tracking_data_filter->addJoin("   user uc ", "   uc.id = c.user_id ");
        $this->tracking_data_filter->addJoin("   user_account uac ", "   uac.id = uc.user_account_id ");
        $this->tracking_data_filter->addFieldFilter('     ua.id', $this->user->getUserAccountId());
        $wharehouseId = $this->form_vars['warehouse_id'];
        $scannedBy = $this->form_vars['user_account_id'];
        $trackingNumber = $this->form_vars['tracking_number'];
        $dateFrom = $this->form_vars['date_from'];
        $dateTo = $this->form_vars['date_to'];
        $status = $this->form_vars['status'];
        $manifestId = $this->form_vars['manifest_id'];
        $userIds = [];
        if (!empty($scannedBy)) {
            $userFilter = new UserFilter();
            $userIds = $userFilter->getUserIdsFromAccountIds($scannedBy);
        }
        if (!empty($wharehouseId)) {
            $this->tracking_data_filter->addFieldFilter('     t.warehouse_id', $wharehouseId);
        }
        
        
        if (!empty($status)) {
            if ($status == 'outbound') {
                $this->tracking_data_filter->addFieldFilter('     status_code_id', 146);
            } else if ($status == 'return') {
                
                $this->tracking_data_filter->addFieldFilter('     c.consignment_type', 'return');
            }
//            $this->tracking_data_filter->addFieldFilter('     warehouse_id', $status);
        }
        if (!empty($manifestId)) {
            $this->tracking_data_filter->addJoin("   manifest_entity_mapping mem ", "   mem.entity_id = t.entity_id ");
            $this->tracking_data_filter->addFieldFilter('     mem.id', $manifestId);
//            $this->tracking_data_filter->addFieldFilter('     warehouse_id', $status);
        }
        if (trim($trackingNumber) != '') {
            $awb = nl2br($trackingNumber);
            $AwbArray = explode('<br />', $awb);
            foreach ($AwbArray as $key => $value) {
                $AwbArray[$key] = trim(ParseTrackingNumber::Parse($value));
            }
            $this->tracking_data_filter->addFilterIn('    t.tracking_number', $AwbArray);
        }
        if (!empty($userIds) || !empty($scannedBy)) {
            $this->tracking_data_filter->addFilterIn('    t.user_id', $userIds);
        }
        if (!empty($dateFrom) && !empty($dateTo)) {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
            $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));
            $dateLabelCreatedWhere = "       ((t.date_added >= '" . DbAccess3::escape($dateFrom) . "') AND (t.date_added <= '" . DbAccess3::escape($dateTo) . "'))";
            $this->tracking_data_filter->addFilterString($dateLabelCreatedWhere, 'filter');
        }

//        $statusIn = [
//            Consignment::STATUS_LABEL_CREATED,
//            Consignment::STATUS_DISPATCHED,
//            Consignment::STATUS_PARTIAL_DISPATCHED,
//            Consignment::STATUS_RECEIVED,
//            Consignment::STATUS_INTRANSIT
//        ];
//        $this->consignment_filter->addFilterIn("      p.parcel_status_code", $statusIn, "filter");

//        $this->consignment_filter->addGroupBy('p.id');
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">
        <style>
            .label-account {
                font-size: 12px;
                font-weight: bold;
            }

            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/flipclock.min.js"></script>
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "track_shipments_report.php?action=tracking_data_list";
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
                                // {"data": "option", "bSortable": false},
                                {"data": "tracking_number", "bSortable": false},
                                {"data": "hub", "bSortable": false},
                                {"data": "scanned_by", "bSortable": false},
                                {"data": "agent_name", "bSortable": false},
                                {"data": "scanned_date", "bSortable": false},
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
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
            $(document).ready(function () {
                $('#tracking_data_export_btn').click(function () {
                    var frmData = $("#parcel_details_search_form").serialize();
                    $("#export-result-modal").modal('show');
                   $.ajax({
                        type: "POST",
                        url: "track_shipments_report.php?action=tracking_data_export", // your php file name
                        dataType: "json",
                        data: frmData,
                        success: function (data)
                        {
                            $("#export-result-content").html(data.message);
                        },
                        error: function (errorString)
                        {
                           
                        }
                    });
                   
                   
                });

                DataTableFun.init();
                // grid.submitFilter();
                /* Custom filtering function which will search data in column four between two values */
                $('#btn_go').click(function () {
                    $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all checkboxes
                    $('input.form-filter[type="checkbox"]:checked').each(function () {
                        grid.addAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all radio buttons
                    $('input.form-filter[type="radio"]:checked').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    grid.submitFilter();
                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        dateFormate: "yyyy-mm-dd"
                    });
                }
            });

            function getTodayDate() {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }
                today = yyyy + '-' + mm + '-' + dd;
                return today;
            }

            function resetForm() {
                document.getElementById("parcel_details_search_form").reset();
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <form id="parcel_details_search_form" name="parcel_details_search_form" method="post">
            <input type="hidden" name="csv_action" id="csv_action" value="">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-search"></i>
                        Search Panel
                    </div>
                    <div class="tools">
                        <a href="" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div data-rail-color="blue" data-handle-color="blue" class="filter">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                            $accountParentId = $this->user->getUserAccountId();
                                        }
                                        $selectedAccount = "";
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent, $allowedLevel); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Hub</label>
                                <div class="form-group">
                                    <!--                                    --><?php //echo Ddl::generateCountryDDL('country_id', '', 'id');
                                    ?>
                                    <?php echo Ddl::generateWarehouseDDLWithImage('warehouse_id', 'warehouse_id', '', 'class="form-filter form-control input-sm select2 searchbox"'); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Status</label>
                                <div class="form-group">
                                    <?php
                                    $dataArray = [
                                        'outbound' => 'Outbound',
                                        'return' => 'Return',
                                    ];
                                    ?>
                                    <!--                                    --><?php //echo Ddl::generateCountryDDL('country_id', '', 'id');
                                    ?>
                                    <?php echo Ddl::generateArrayDDL('status', $dataArray, '', '', 'class="form-filter form-control input-sm select2 searchbox"'); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Date </label>
                                <div class="input-group date-picker input-daterange" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter" name="date_from" id="date_from"
                                           value="" rel="tooltip" data-original-title="From Date">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control form-filter" name="date_to" id="date_to"
                                           value="" rel="tooltip" data-original-title="To Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Manifest Id</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="manifest_id" name="manifest_id"
                                               placeholder="Manifest Id" type="text" value="" rel="tooltip"
                                               data-original-title="Manifest Id">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="label-account">Tracking</label>
                                <div class="form-group tracking_type_field">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-road"></i> </span>
                                        <textarea id="tracking_number" name="tracking_number"
                                                  placeholder="Tracking Number" rel="tooltip"
                                                  data-original-title="Tracking" class="form-control form-filter"
                                                  style="resize: none;height: 143px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" style="text-align:center;">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary filter-submit" name="btn_go"
                                            id="btn_go" value="Search"> Search
                                    </button>&nbsp;
                                    <button type="button" class="btn btn-primary filter-submit" name="tracking_data_export_btn"
                                            id="tracking_data_export_btn" value="Export"> Export
                                    </button>&nbsp;
                                    
                                    <button type="button" class="btn btn-default filter-cancel" name="btn_rest"
                                            value="Reset" onclick="resetForm()"> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-list"></i>
                        Tracking Data List
                    </div>
                    <div class="actions" id="bluk_actions" style="display:none;">
                        <!--                        <a href="javascript:;" class="btn btn-default" id="assign_vehicle_all_btn">
                                                    Assign Vehicle all
                                                </a>
                                                <a href="javascript:;" class="btn btn-default" id="assign_vehicle_selected_btn">
                                                    Assign Vehicle Selected
                                                </a>-->
                    </div>
                    <div class="invoice_action margin-bottom-10"></div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="table-actions-wrapper"></div>
                        <table class="table table-striped table-bordered table-hover table-condensed"
                               id="manage-data-table">
                            <thead>
                            <tr role="row" class="heading">
                                <!-- <th>
                                     <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                         <input type='checkbox' name='checkall' class="group-checkable"/>
                                         <span></span>
                                     </label>
                                 </th>-->
                                <th>Tracking Number</th>
                                <th>HUB</th>
                                <th>Scanned By</th>
                                <th>Agent Name</th>
                                <th>Scanned Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div id="assignVehicleModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Assign Vehicle</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="assign_driver" id="assign_driver" value=""/>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Vehicle</label>
                                    <div class="form-group">
                                        <?php
                                        $sql = "SELECT
                                                        *
                                                      FROM
                                                        `vehicle`";
                                        $name = ['vehicle_type', 'vehicle_make', 'registration_number'];
                                        echo Ddl::generateDDLFromSql($sql, "vehicle_id", $name, 'id', "", "class='select2'", '', '', "vehicle_id", "Select Vehicle", '');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Driver</label>
                                    <div class="form-group">
                                        <select class="select2" name="driver_id" id="driver_id">
                                            <option value="">Select Driver</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <div class="input-group date date-picker margin-bottom-5"
                                             data-date-format="yyyy-mm-dd">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i
                                                            class="fa fa-calendar"></i></button>
                                            </span>
                                            <input type="text" class="form-control input-sm validate_check" readonly
                                                   name="pickup_date" id="pickup_date" placeholder="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="save_assign_vehicle_btn">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Modal -->
            <div id="export-result-modal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Export Data</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="assign_driver" id="assign_driver" value=""/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="export-result-content"></div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </form>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead()
    {
        ?>
        <style type="text/css">
            #tracking_number_for_status_box {
                display: none;
            }

            .table > thead > tr > th {
                vertical-align: top;
            }

            #select2-service-results .select2-results__option[aria-disabled=true] {
                display: none;
            }

            .left-check {
                float: left;
                padding-top: 5px;
            }

            .border-top-none {
                border-top: none;
            }

            .tracking_type_field .select2-container--bootstrap .select2-selection {
                border-bottom-right-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
                border-top-left-radius: 0px !important;
            }

            .tracking_type_field textarea {
                border-top-right-radius: 0px !important;
                border-top-left-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
            }

            table.dataTable td.sorting_1, table.dataTable td.sorting_2, table.dataTable td.sorting_3, table.dataTable th.sorting_1, table.dataTable th.sorting_2, table.dataTable th.sorting_3 {
                background: none !important;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td {
                color: #000;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td a {
                color: #000;
            }

            .line-height-2 {
                line-height: 2;
            }

            .input-daterange input {
                text-align: left;
            }

            @media only screen and (min-width: 983px) {
                .margin-top-md-30 {
                    margin-top: 30px;
                }
            }

        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>