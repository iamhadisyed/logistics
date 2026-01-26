<?php
// get settings
require_once("../includes/settings/config.inc.php");
  include_classes([  
                    'country.class',
                    'countryfilter.class',
                    'bagging.class',
                    'baggingfilter.class',
                    'warehouse.class',
                    'warehousefilter.class',
                        ]);
class Page extends BasePage {

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // Session check
        $this->user = SessionManager::getUser();
//        if ($this->user->getUserType() != User::USER_TYPE_CORPORATE && $this->user->getUserType() != User::USER_TYPE_ADMIN ) {
//            util_redirect("index.php");
//        }
        //BreadCrum
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'add_bag.php' => "Add Bag"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "bag_ajax") {
            $baggingFilter = new BaggingFilter();
            
            $userIdArr = [];
            $userAccountId = $this->user->getUserAccountId();
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("user_account_id", $userAccountId);
            $userObj = $userFilter->getColumnList("id");
            if(count($userObj) > 0){
                foreach ($userObj as $user) {
                    $userIdArr[] = $user->getId();
                }
            }
            $baggingFilter->addFilterIn("    user_id", $userIdArr);
            $baggingFilter->addFilter(" and isdeleted  = 0");
            
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $bagNumber = $this->form_vars['bag_number'];
                if (!empty($bagNumber))
                    $baggingFilter->addFieldFilter('   b.bagnumber', $bagNumber);
                
                $sourceCountry = $this->form_vars['source_country'];
                if (!empty($sourceCountry))
                    $baggingFilter->addFieldFilter('   b.bag_source_country_id', $sourceCountry);
                
                $sourceWarehouse = $this->form_vars['source_warehouse'];
                if (!empty($sourceWarehouse))
                    $baggingFilter->addFieldFilter('   b.bag_source_warehouse_id', $sourceWarehouse);
                
                $destinationCountry = $this->form_vars['destination_country'];
                if (!empty($destinationCountry))
                    $baggingFilter->addFieldFilter('   b.bag_destination_country_id', $destinationCountry);
                
                $destinationWarehouse = $this->form_vars['destination_warehouse'];
                if (!empty($destinationWarehouse))
                    $baggingFilter->addFieldFilter('   b.bag_destination_warehouse_id', $destinationWarehouse);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $baggingFilter->addDateCreatedFilter($date_created_from, $date_created_to);
            }

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
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                $baggingFilter->AddOrderBy(strtolower("b." . $dataTableColumnName), $orderFalse);
            }
            else
            {
                $baggingFilter->AddOrderBy('date_created', false);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $baggingFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $baggingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $baggingFilter->setOffset($iDisplayStart);
            $baggingObjs = $baggingFilter->getPagingList();
            $setDataArr = array();
            $count = 1;
            foreach ($baggingObjs as $baggingObj) {
                $currentArr = array();
                $dateCreated = formatDate(date("d-m-Y",$baggingObj->getDateCreated()));
                $currentArr['date_created'] = $dateCreated != '01-01-1970' ? $dateCreated : '';
                $currentArr['bagnumber'] = $baggingObj->getBagnumber();
                $sCountry = new Country($baggingObj->getBagSourceCountryId());
                $dCountry = new Country($baggingObj->getBagDestinationCountryId());
                $sWarehouse = new Warehouse($baggingObj->getBagSourceWarehouseId());
                $dWarehouse = new Warehouse($baggingObj->getBagDestinationWarehouseId());
                $currentArr['bag_source_country_id'] = $sCountry->getName();
                $currentArr['bag_source_warehouse_id'] = $sWarehouse->getWarehouseName();
                $currentArr['bag_destination_country_id'] = $dCountry->getName();
                $currentArr['bag_destination_warehouse_id'] = $dWarehouse->getWarehouseName();
                $currentArr['actions'] = $count;
                $count++;
//                $currentArr['actions'] = "<a href='JavaScript:Void(0);' data-bag_id='" . $baggingObj->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";

                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
         <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
         <style type="text/css">
             .notes {
                background-attachment: local;
                background-image:
                    linear-gradient(to right, white 10px, transparent 10px),
                    linear-gradient(to left, white 10px, transparent 10px),
                    repeating-linear-gradient(white, white 30px, #ccc 30px, #ccc 31px, white 31px);
                    line-height: 31px;
                    padding: 8px 10px;
            }
         </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
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
                                "url": "add_bag.php?action=bag_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "date_created"},
                                {"data": "bagnumber"},
                                {"data": "bag_source_country_id"},
                                {"data": "bag_source_warehouse_id"},
                                {"data": "bag_destination_country_id"},
                                {"data": "bag_destination_warehouse_id"}
                            ]
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
                DataTableFun.init();
                $("#btn_create_bag").click(function () {
                    if ($("#source_country_id").val() == "") {
                        show_res_msg("error", "Please select source country");
                    } else if ($("#destination_country_id").val() == "") {
                        show_res_msg("error", "Please select destination country");
                    } else {
                        var source_country_id = $("#source_country_id").val();
                        var source_warehouse_id = $("#source_warehouse_id").val();
                        var destination_country_id = $("#destination_country_id").val();
                        var destination_warehouse_id = $("#destination_warehouse_id").val();
                        var barcodelist = $("#barcodelist").val().split("\n").filter(Boolean);
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php",
                            data: {action: "create_bag", source_country_id: source_country_id, source_warehouse_id: source_warehouse_id, destination_country_id: destination_country_id, destination_warehouse_id: destination_warehouse_id,barcodelist:barcodelist},
                            success: function (data) {
                                var obj = jQuery.parseJSON(data);
                                if (obj.result == "success") {
                                    show_res_msg('success', obj.message);
                                    grid.getDataTable().ajax.reload();
                                } else {
                                    show_res_msg('error', obj.message);
                                }
                            },
                            error: function () {
                                alert('error occur');
                            }
                        });
                    }
                });
                $('.filter-cancel').click(function(){
                    $("#source_country").val('').change();
                    $("#source_warehouse").val('').change();
                    $("#destination_country").val('').change();
                    $("#destination_warehouse").val('').change();
                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            function get_source_warehouse() {
                var source_country_id = $("#source_country_id").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: source_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#source_warehouse_id").html("");
                            $("#source_warehouse_id").html(data);
                            $('#source_warehouse_id').selectpicker("refresh");
                        } else {
                            $("#source_warehouse_id").html("");
                            $('#source_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });
            }
            function get_destination_warehouse() {
                var destination_country_id = $("#destination_country_id").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: destination_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#destination_warehouse_id").html("");
                            $("#destination_warehouse_id").html(data);
                            $('#destination_warehouse_id').selectpicker("refresh");
                        } else {
                            $("#destination_warehouse_id").html("");
                            $('#destination_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });

            }
            function show_res_msg(type, msg) {
                $("html, body").animate({scrollTop: 0}, "slow");
                $("#show_general_msg div.alert").html(" ");
                if (type == "success") {
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                } else {
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                }
                $("#show_general_msg div.alert").html(msg);
                $("#show_general_msg").show();
                setTimeout(function ()
                {
                    $("#show_general_msg").hide();
                }, 7000);
            }
            function get_destination_warehouse_filter() {
                var destination_country_id = $("#destination_country").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: destination_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#destination_warehouse").html("");
                            $("#destination_warehouse").html(data);
                            $('#destination_warehouse').selectpicker("refresh");
                        } else {
                            $("#destination_warehouse").html("");
                            $('#destination_warehouse').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });

            }
            function get_source_warehouse_filter() {
                var source_country_id = $("#source_country").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: source_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#source_warehouse").html("");
                            $("#source_warehouse").html(data);
                            $('#source_warehouse').selectpicker("refresh");
                        } else {
                            $("#source_warehouse").html("");
                            $('#source_warehouse').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });
            }
        </script>
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
//        $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
//        foreach ($this->form_vars as $key => $val) {
//            $$key = $val;
//        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Add Bag
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                           
                              <div class="has-float-label"> 
                                  <div class="first_form_col">
                                <?php
                                echo Ddl::generateCountryDDL('source_country_id', $source_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_source_warehouse();');
                                ?> <label>Source Country  <span class="red-18">*</span></label>
                               
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           
                             <div class="has-float-label input-icon right"> 
                            <div class="first_form_col">
                                
                                    <!-- User Document -->
                                    <select name="source_warehouse_id" id="source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                    </select> <label>Source Warehouse</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                           
                            <div class="has-float-label input-icon right"> 
                                <div class="first_form_col">
                                <?php
                                echo Ddl::generateCountryDDL('destination_country_id', $destination_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_destination_warehouse();');
                                ?> <label>Destination Country  <span class="red-18">*</span></label>
                              
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            
                            <div class="has-float-label input-icon right"> 
                            <div class="first_form_col">
                         
                                    <!-- User Document -->
                                    <select name="destination_warehouse_id" id="destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                    </select><label>Destination Warehouse</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="barcodelist_form_div">

                            <div class="has-float-label input-icon right">  <i class="fa fa-table "></i> 
                                <textarea class="form-control notes" type="text" placeholder="Enter Parcel Number" rows="5" name="barcodelist" id="barcodelist"></textarea>
                                <label for="barcodelist">Enter Parcel Number</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row" align="center">
                        <div class="col-md-12">
                            <a href="javascript:;" id="btn_create_bag" class="btn btn-primary margin-bottom-5" data-original-title="" title="">Create Bag</a>
                        </div>
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Bag List
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Date Cerated</th>
                            <th>Bag Number</th>
                            <th>Source Country</th>
                            <th>Source Warehouse</th>
                            <th>Destination Country</th>
                            <th>Destination Warehouse</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
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
                                <input type="text" class="form-control form-filter input-xs" name="bag_number" id ="bag_number" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                echo Ddl::generateCountryDDL('source_country', $source_country, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_source_warehouse_filter();');
                                ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <select name="source_warehouse" id="source_warehouse" class="bs-select form-control form-filter" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                </select>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                echo Ddl::generateCountryDDL('destination_country', $destination_country, 'id', ' class="form-filter bs-select form-control" required="required" data-live-search="true" data-size="8" onChange=get_destination_warehouse_filter();');
                                ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <select name="destination_warehouse" id="destination_warehouse" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                </select>                                
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    public function renderFooter() {

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
