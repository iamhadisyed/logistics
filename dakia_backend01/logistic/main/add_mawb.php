<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'mawb.class',
    'mawbfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class'
]);
class Page extends BasePage {

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'add_mawb.php' => "Add Master"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "mawb_ajax") {
            $mawbObj = new MawbFilter();

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
            $mawbObj->addFilterIn("    m.added_by", $userIdArr);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $master_number = $this->form_vars['master_number'];
                if (!empty($master_number))
                    $mawbObj->addFieldFilter('   m.mawb_number', $master_number);

                $source_country = $this->form_vars['source_country'];
                if (!empty($source_country))
                    $mawbObj->addFieldFilter('   m.`mawb_source_country_id`', $source_country);
                $destination_country = $this->form_vars['destination_country'];
                if (!empty($destination_country))
                    $mawbObj->addFieldFilter('   m.`mawb_destination_country_id`', $destination_country);
                $source_warehouse = $this->form_vars['source_warehouse'];
                if (!empty($source_warehouse))
                    $mawbObj->addFieldFilter('   m.`mawb_source_warehouse_id`', $source_warehouse);
                $destination_warehouse = $this->form_vars['destination_warehouse'];
                if (!empty($destination_warehouse))
                    $mawbObj->addFieldFilter('   m.`mawb_destination_warehouse_id`', $destination_warehouse);

                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $mawbObj->addDateCreatedFilter($date_created_from, $date_created_to);

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
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                if($dataTableColumnName == "mawb"){
                    $dataTableColumnName = "mawb_number";
                }
                if($dataTableColumnName == "source_country"){
                    $dataTableColumnName = "mawb_source_country_id";
                }
                if($dataTableColumnName == "source_warehouse"){
                    $dataTableColumnName = "mawb_source_warehouse_id";
                }
                if($dataTableColumnName == "destination_country"){
                    $dataTableColumnName = "mawb_destination_country_id";
                }
                if($dataTableColumnName == "destination_warehouse"){
                    $dataTableColumnName = "mawb_destination_warehouse_id";
                }
                $mawbObj->AddOrderBy(strtolower("m." . $dataTableColumnName), $orderFalse);
            }

            /*
             * Pagination Logic Implemented
             * 
             */
            $countTotal = $mawbObj->getMawbTotalCount();
            $iTotalRecords = $countTotal[0]->getId();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $mawbObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $mawbObj->setOffset($iDisplayStart);
            $mawbData = $mawbObj->getListMawb();
            $setDataArr = array();

            foreach ($mawbData as $data) {
                $currentArr = array();
                $currentArr['added_date'] = formatDate(date("Y-m-d", strtotime($data->getAddedDate())));
                $currentArr['mawb'] = $data->getMawbNumber();
                $currentArr['source_country'] =  $data->getSourceCountry();
                $currentArr['source_warehouse'] =  $data->getSourceWarehouse();
                $currentArr['destination_country'] =  $data->getDestinationCountry();
                $currentArr['destination_warehouse'] =  $data->getDestinationWarehouse();
                //$currentArr['actions'] = "<a href='JavaScript:Void(0);' data-group_id='" . $data->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                                <ul class="dropdown-menu" >
                                                    <li>
                                                        <a href="javascript:;" title="Parcel Details" onclick="get_parcel_detail('. $data->getId() .')" >
                                                            <span class="glyphicon glyphicon-eye-open"></span> View Parcel Details
                                                        </a>
                                                        <a href="javascript:;" title="Edit Parcel" onclick="edit_parcel_detail('. $data->getId() .')" >
                                                            <span class="glyphicon glyphicon-pencil"></span> Edit Parcel
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_parcel_detail") {
            $mawbId = $this->form_vars['mawb_id'];
            $mawbParcelMappingFilter = New MawbParcelMappingFilter();
            $mawbParcelMappingObj = $mawbParcelMappingFilter->getMawbParcel($mawbId);
            $html = '<table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th> MAWB Number </th>
                                    <th> Tracking Number </th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody>';
            if(count($mawbParcelMappingObj) > 0){
                foreach ($mawbParcelMappingObj as $mawbParcelMappingItem) {
                    $html .= '<tr>
                            <td> '.$mawbParcelMappingItem->getMawbNumber().' </td>
                            <td> '.$mawbParcelMappingItem->getTrackingNumber().' </td>
                            <td> <span id="'.$mawbParcelMappingItem->getId().'" style="cursor: pointer;" class="label label-sm label-danger label-theme remove_parcel_cls">Remove</span> </td>
                        </tr>';
                }
            }
            $html .= '</tbody>
                        </table>';
            echo $html;
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "remove_parcel") {
            $mawbid = $this->form_vars['mawb_id'];
            $mawbParcelMapping = New MawbParcelMapping();
            $mawbParcelMapping->deleteById($mawbid);
            echo 'deleted';
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit_parcel_detail") {
            $mawbId = $this->form_vars['mawb_id'];
            $return['status'] = 'error';
            if($mawbId > 0){
                $mawb = New Mawb($mawbId);
                $return['status'] = 'success';
                $return['mawb_number'] = $mawb->getMawbNumber();
                $return['source_country_id'] = $mawb->getMawbSourceCountryId();
                $return['source_warehouse_id'] = $mawb->getMawbSourceWarehouseId();
                $return['destination_country_id'] = $mawb->getMawbDestinationCountryId();
                $return['destination_warehouse_id'] = $mawb->getmawbDestinationWarehouseId();
            }
            echo json_encode($return);
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
            $(document).ready(function (e) {
                $('.filter-cancel').click(function(){
                    $("#source_country").val('').change();
                    $("#source_warehouse").val('').change();
                    $("#destination_country").val('').change();
                    $("#destination_warehouse").val('').change();
                });
                DataTableFun.init();
                $(".source_country_select").change(function(){
                    $(".source_country_select").removeAttr('style');
                });
                $(".destination_country_select").change(function(){
                    $(".destination_country_select").removeAttr('style');
                });
                $("#btn_create_mawb").click(function(){

                    var source_country_id = $("#source_country_id option:selected").val();
                    var source_warehouse_id = $("#source_warehouse_id option:selected").val();
                    var destination_country_id = $("#destination_country_id option:selected").val();
                    var destination_warehouse_id = $("#destination_warehouse_id option:selected").val();
                    var parcel_number = $("#parcel_number").val().split("\n").filter(Boolean);
                    var mawb_number = $("#mawb_number").val();
                    if(!source_country_id) {
                        //$(".source_country_select").attr('style','border: 1px solid red; ');
                        show_res_msg("error","Please select source country.");
                        return false;
                    }else  if(!destination_country_id){
                        show_res_msg("error","Please select destination country.");
                        // $(".destination_country_select").attr('style','border: 1px solid red;  ');
                        return false;
                    }else{
//                        $(".destination_country_select").removeAttr('style');
//                        $(".source_country_select").removeAttr('style');
                    }
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php",
                        data: {action: "create_mawb", source_country_id: source_country_id,source_warehouse_id:source_warehouse_id,destination_country_id:destination_country_id,destination_warehouse_id:destination_warehouse_id,parcel_number:parcel_number,mawb_number:mawb_number},
                        dataType: "json",
                        success: function (data) {
                            if (data.response) {
                                $.unblockUI();
                                $('.msg').html('');
                                $('.msg').html(data.response);
                                $('.msg').html(data.msg);
                                $('.msg').removeClass('alert alert-danger');
                                $('.msg').addClass(data.class);
                                grid.getDataTable().ajax.reload();
                            } else {
                                $.unblockUI();
                                $('.msg').html('');
                                $('.msg').html(data.response);
                                $('.msg').html(data.msg);
                                $('.msg').removeClass('alert alert-success');
                                $('.msg').addClass(data.class);
                            }
                            //setTimeout(function(){ $('.msg').hide(); }, 4000);
                        },
                        error: function () {
                            alert('error occur');
                        }
                    });
                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
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
                                "url": "add_mawb.php?action=mawb_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "added_date"},
                                {"data": "mawb"},
                                {"data": "source_country"},
                                {"data": "source_warehouse"},
                                {"data": "destination_country"},
                                {"data": "destination_warehouse"}
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
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });

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
            function get_parcel_detail(mawb_id){
                $.ajax({
                    type: "POST",
                    url: "add_mawb.php",
                    data: {action: "get_parcel_detail", mawb_id: mawb_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#parcel_detail_div").html("");
                            $("#parcel_detail_div").html(data);
                            $('#parcel_detail_modal').modal('show');
                            // $('#source_warehouse_id').selectpicker("refresh");
                        } else {
                            // $("#source_warehouse_id").html("");
                            // $('#source_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });
            }
            $(document).on('click', '.remove_parcel_cls', function(){
                var this_new = $(this);
                var mawb_id = $(this).attr('id');
                swal({
                        title: "Are your sure you want to remove parcel from MAWB",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "add_mawb.php",
                                data: {action: "remove_parcel", mawb_id: mawb_id},
                                dataType: "html",
                                success: function (data) {
                                    if (data) {
                                        this_new.closest('tr').remove();
                                    } else {

                                    }
                                },
                                error: function () {
                                    alert('error occur');
                                }
                            });
                        }
                    });
            });
            function edit_parcel_detail(mawb_id){
                $.ajax({
                    type: "POST",
                    url: "add_mawb.php",
                    data: {action: "edit_parcel_detail", mawb_id: mawb_id},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                            $("#mawb_number").val(data.mawb_number);
                            $("#source_country_id").val(data.source_country_id);
                            $("#source_country_id").change();
                            $("#source_country_id").selectpicker('refresh');
                            $("#source_warehouse_id").val(data.source_warehouse_id);
                            $("#source_warehouse_id").selectpicker('refresh');
                            $("#destination_country_id").val(data.destination_country_id);
                            $("#destination_country_id").change();
                            $("#destination_country_id").selectpicker('refresh');
                            $("#destination_warehouse_id").val(data.destination_warehouse_id);
                            $("#destination_warehouse_id").selectpicker('refresh');
                            $(".scroll-to-top").click();
                        } else {
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
                    Add Master
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
                    <div class="col-md-12">
                        <div class="msg"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="has-float-label">
                                <input type="text" name="mawb_number" id="mawb_number" class="form-control" placeholder="Enter MAWB" value="" />
                                <label>MAWB Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="has-float-label">
                                <div class="source_country_select">
                                    <?php
                                    echo Ddl::generateCountryDDL('source_country_id', $source_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_source_warehouse();');
                                    ?><label>Source Country <span class="red-18">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group ">
                            <div class="has-float-label input-icon right">
                                <div class="first_form_col">
                                    <div class="source_warehouse_select">

                                        <!-- User Document -->
                                        <select name="source_warehouse_id" id="source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                        </select>
                                        <label>Source Warehouse</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">

                            <div class="has-float-label input-icon right">

                                <div class="destination_country_select">

                                    <?php
                                    echo Ddl::generateCountryDDL('destination_country_id', $destination_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_destination_warehouse();');
                                    ?><label>Destination Country <span class="red-18">*</span></label>

                                </div> </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">

                            <div class="has-float-label input-icon right">
                                <div class="first_form_col">
                                    <div class="destination_warehouse_select">

                                        <!-- User Document -->
                                        <select name="destination_warehouse_id" id="destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                        </select>
                                        <label>Destination Warehouse</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="barcodelist_form_div">

                            <div class="has-float-label input-icon right">  <i class="fa fa-table "></i>
                                <textarea class="form-control notes" type="text" placeholder="Enter Parcel Number" rows="5" name="parcel_number" id="parcel_number"></textarea>
                                <label for="parcel_number">Enter Parcel Number</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row" align="center">
                        <div class="col-md-12">
                            <a href="javascript:;" id="btn_create_mawb" class="btn btn-primary margin-bottom-5" data-original-title="" title="">Create MAWB</a>
                        </div>
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Master List
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
                        <th>Master Number</th>
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
                            <input type="text" class="form-control form-filter input-xs" name="master_number" id ="master_number" />
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
        <div class="modal fade" id="parcel_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Parcel List</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-body">
                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <div id="parcel_detail_div"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="close_btn" type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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
