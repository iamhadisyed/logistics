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
            'Manifest Return List'
        );
        $this->user = $sessionUser;
        $userAccount = $sessionUser->getUserAccount();
        t_on(); // turn on trace for this page
        if (isset($_GET['action']) && $_GET['action'] == "rtn_list") {
            $manifestFilter = new ManifestFilter();
            $manifestFilter->addJoin("`user` u"," u.`id` "," m.`user_id` "," JOIN");
            $manifestFilter->addFieldFilter("    u.`user_account_id`",$this->user->getUserAccountId());
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $manifestNumber = $this->form_vars['manifest_number'];
                if (!empty($manifestNumber)) {
                    $manifestFilter->addFilter("m.id = $manifestNumber");
                }

                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to)){
                    $manifestFilter->addDateFilter($date_created_from, $date_created_to,false);
                }
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = 'ASC';
                if ($orderBy == 'true') {
                    $orderFalse = 'DESC';
                }

                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if($dataTableColumnName == "Manifest_number")
                    $dataTableColumnName = 'id';
                if($dataTableColumnName == "Manifest_number")
                    $dataTableColumnName = 'id';
                $manifestFilter->AddOrderBy(strtolower("m." . $dataTableColumnName), strtoupper($orderBy));
            }
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
            $manifestFilter->setRowsPerPage("");
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
                $currentArr['actions'] = "";
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
                    var datatableurl = "return_manifest_list.php?action=rtn_list";
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
                                {"data": "actions", "bSortable": false},
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
                    var manifest = 0;
                    if ($("#create_manifest").is(":checked")) {
                        manifest = 1;
                    }
                    if(trackingNumber == ""){
                        swal("","please enter tracking number", "info");
                    }else{
                        $.blockUI();
                        $.ajax({
                            type: "POST",
                            url: "bulk_return.php",
                            data: {action: "return", tracking_number: trackingNumber,manifest:manifest},
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
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        format: "yyyy-mm-dd"
                    });
                }
                $('body').on('click', '.date-picker-driver', function () {
                    $(this).datepicker({
                        autoclose: true,
                    });
                });
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
                            <th>Action</th>
                            <th>Date</th>
                            <th>Manifest Number</th>
                            <th>Number of Parcel</th>
                            <th>File</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td  class="user_acccount_correct_button">
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="manifest_number" id="manifest_number"/>
                            </td>
                            <td> </td>
                            <td> </td>
                        </tr>
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
