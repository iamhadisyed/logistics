<?php
// get settings
require_once("../includes/settings/config.inc.php");
        
        include_classes([  
                    'palletcariergroup.class',
                    'palletcariergroupfilter.class',
                    'pallet.class',
                    'palletfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'carrierhubs.class',
                    'carrierhubstfilter.class',
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
            'add_pallet.php' => "Add Pallet"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "pallet_ajax") {
            $palletFilter = new PalletFilter();
            
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
            $palletFilter->addFilterIn("userid", $userIdArr);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $palletNumber = $this->form_vars['pallet_number'];
                if (!empty($palletNumber))
                    $palletFilter->addFieldFilter('   p.palletno', $palletNumber);

                $palletCarrierGroupFilter = $this->form_vars['pallet_carrier_group_filter'];
                if (!empty($palletCarrierGroupFilter))
                    $palletFilter->addFieldFilter('   p.pallet_carrier_id', $palletCarrierGroupFilter);

                $carrierHubFilter = $this->form_vars['carrier_hub_filter'];
                if (!empty($carrierHubFilter))
                    $palletFilter->addFieldFilter('   p.hub', $carrierHubFilter);

                $sourceCountry = $this->form_vars['source_country'];
                if (!empty($sourceCountry))
                    $palletFilter->addFieldFilter('   p.pallet_source_country_id', $sourceCountry);

                $sourceWarehouse = $this->form_vars['source_warehouse'];
                if (!empty($sourceWarehouse))
                    $palletFilter->addFieldFilter('   p.pallet_source_warehouse_id', $sourceWarehouse);

                $destinationCountry = $this->form_vars['destination_country'];
                if (!empty($destinationCountry))
                    $palletFilter->addFieldFilter('   p.pallet_destination_country_id', $destinationCountry);

                $destinationWarehouse = $this->form_vars['destination_warehouse'];
                if (!empty($destinationWarehouse))
                    $palletFilter->addFieldFilter('   p.pallet_destination_warehouse_id', $destinationWarehouse);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $palletFilter->addDateCreatedFilter($date_created_from, $date_created_to);

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
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $palletFilter->AddOrderBy(strtolower("p." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $palletFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $palletFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $palletFilter->setOffset($iDisplayStart);
            $palletFilterObjs = $palletFilter->getPagingList();
            $setDataArr = array();

            foreach ($palletFilterObjs as $palletFilterObj) {
                $currentArr = array();
                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                $carrierHub = new CarrierHubs($palletFilterObj->getHub());
                $sCountry = new Country($palletFilterObj->getPalletSourceCountryId());
                $sWarehouse = new Warehouse($palletFilterObj->getPalletSourceWarehouseId());
                $dCountry = new Country($palletFilterObj->getPalletDestinationCountryId());
                $dWarehouse = new Warehouse($palletFilterObj->getPalletDestinationWarehouseId());
                $currentArr['date_created'] = formatDate(date("d-m-Y",$palletFilterObj->getDateCreated()));
                $currentArr['palletno'] = $palletFilterObj->getPalletno();
                $currentArr['pallet_carrier_id'] = $palletCarrierGroup->getGroupName();
                $currentArr['hub'] = $carrierHub->getHub();
                $currentArr['pallet_source_country_id'] = $sCountry->getName();
                $currentArr['pallet_source_warehouse_id'] = $sWarehouse->getWarehouseName();
                $currentArr['pallet_destination_country_id'] = $dCountry->getName();
                $currentArr['pallet_destination_warehouse_id'] = $dWarehouse->getWarehouseName();
                $currentArr['actions'] = "";
//                $currentArr['actions'] = "<a href='JavaScript:Void(0);' data-group_id='" . $palletFilterObj->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";
//                        "<a href='services_view.php?id=" . $palletFilterObj->getId() . "' class='btn btn-xs blue btn-outline'><span class='fa fa-eye'></span> </a>"
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
         <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
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
                                "url": "add_pallet.php?action=pallet_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "date_created"},
                                {"data": "palletno"},
                                {"data": "pallet_carrier_id"},
                                {"data": "hub"},
                                {"data": "pallet_source_country_id"},
                                {"data": "pallet_source_warehouse_id"},
                                {"data": "pallet_destination_country_id"},
                                {"data": "pallet_destination_warehouse_id"}
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
                $("#btn_save_pallet").click(function(){
                    if ($("#pallet_carrier_group").val() == "") {
                        show_res_msg("error","Please select pallet carrier group");
                    }else if ($("#source_country_id").val() == "") {
                        show_res_msg("error","Please select source country");
                    }else if($("#destination_country_id").val() == "") {
                        show_res_msg("error","Please select destination country");
                    }else{
                        var pallet_carrier_group = $("#pallet_carrier_group").val();
                        var carrier_hubs = $("#carrier_hubs").val();
                        var source_country_id = $("#source_country_id").val();
                        var source_warehouse_id = $("#source_warehouse_id").val();
                        var destination_country_id = $("#destination_country_id").val();
                        var destination_warehouse_id = $("#destination_warehouse_id").val();
                        $.ajax({
                        type: "POST",
                        url: "box_ajax.php",
                        data: {action: "create_pallet", source_country_id: source_country_id,source_warehouse_id:source_warehouse_id,destination_country_id:destination_country_id,destination_warehouse_id:destination_warehouse_id,pallet_carrier_group:pallet_carrier_group,carrier_hubs:carrier_hubs},
                        success: function (data) {
                                var obj = jQuery.parseJSON(data);
                                if (obj.result == "success") {
                                     show_res_msg('success',obj.message);
                                     grid.getDataTable().ajax.reload();
                                } else {
                                    show_res_msg('error',obj.message);
                                }
                            },
                            error: function () {
                                alert('error occur');
                            }
                        });
                    }
                });
                $("#pallet_carrier_group_filter").change(function () {
                    var carrierGroup = $(this).val();
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php",
                        data: {action: "get_carrier_service", carrier_group: carrierGroup},
                        dataType: "html",
                        success: function (data) {
                            if (data) {
                                var returnData = data.split(':::');
                                $("#carrier_hub_filter").html(returnData[1]);
                                $('#carrier_hub_filter').selectpicker("refresh");
                            } else {

                            }
                        },
                        error: function () {

                        }
                    });
                });
                $("#pallet_carrier_group").change(function () {
                    var carrierGroup = $(this).val();
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php",
                        data: {action: "get_carrier_service", carrier_group: carrierGroup},
                        dataType: "html",
                        success: function (data) {
                            if (data) {
                                var returnData = data.split(':::');
                                $("#carrier_hubs").html(returnData[1]);
                                $('#carrier_hubs').selectpicker("refresh");
                            } else {

                            }
                        },
                        error: function () {

                        }
                    });
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
            function show_res_msg(type,msg){
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $("#show_general_msg div.alert").html(" ");
                if(type == "success"){
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                }else{
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
                    Add Pallet
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
                         
                            <div class="has-float-label input-icon right"> 
                            <div class="first_form_col">
                            
                                    <!-- User Document -->
                                    <?php
                                    echo Ddl::generateDDL('pallet_carrier_group', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="Pallet Carrier Group" data-live-search="true"  data-original-title="Pallet Carrier Group"', 'Please select', '', 'pallet_carrier_group', 'Pallet Carrier Group', '', '');
                                    ?>   <label>Pallet Carrier Group <span class="red-18">*</span></label>
                                    
                                </div>
                                </div>
                            </div>
                        </div>
                 
                    <div class="col-sm-4">
                        <div class="form-group">
                             <div class="has-float-label input-icon right"> 
                            <div class="first_form_col">
                            
                                    <!-- User Document -->
                                    <select name="carrier_hubs" id="carrier_hubs" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                    </select>  <label>Carrier Hubs</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                               <div class="has-float-label input-icon right"> 
                                   <div class="first_form_col">
                                <?php
                                echo Ddl::generateCountryDDL('source_country_id', $source_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_source_warehouse();');
                                ?>   <label>Source Country <span class=" red-18">*</span></label>
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
                </div>
                <div class="row">
                    <div class="row" align="center">
                        <div class="col-md-12">
                            <a href="javascript:;" id="btn_save_pallet" class="btn btn-primary btn_save margin-bottom-5" data-original-title="" title="">Create Pallet</a>
                        </div>
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Pallet List
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
                            <th>Date Created</th>
                            <th>Pallet Number</th>
                            <th>Pallet Carrier Group</th>
                            <th>Pallet Carrier Hub</th>
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
                                <input type="text" class="form-control form-filter input-xs" name="pallet_number" id ="master_number" />
                            </td>
                            <td class="user_acccount_correct_button">
                                 <?php
                                    echo Ddl::generateDDL('pallet_carrier_group_filter', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="Pallet Carrier Group" data-live-search="true"  data-original-title="Pallet Carrier Group"', 'Please select', '', 'pallet_carrier_group_filter', 'Pallet Carrier Group', '', '');
                                    ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <select name="carrier_hub_filter" id="carrier_hub_filter" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                </select>
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
        ?>
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
