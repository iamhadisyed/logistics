<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'warehouse.class',
                    'warehousefilter.class',
                    'rack.class',
                    'rackfilter.class',
                    'rackshelf.class',
                    'rack.class',
                    'rackfilter.class'
                ]);
class Page extends BasePage {

    public $user;
    public $parcel_filter;
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Consignment Return"
        );
        $this->user = SessionManager::getUser();
        
        if (isset($_GET['action']) && $_GET['action'] == 'get_parcel') {
            $this->parcel_filter = new ParcelFilter();
            /*
             * Column filter
             * For search
             */
            $parcelDataArr = [];
            $parcelArr = [];
            $iTotalRecords = 0;
            $sEcho = $this->form_vars['draw'];
            /*
             * Column filter
             * For search
             */
            $consignmentId = 0;
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $tracking_number = $this->form_vars['tracking_number'];
                if (!empty($tracking_number)) {
                    $consignmentFilter = new ConsignmentFilter();
                    $consignmentFilter->addFilter("        c.awb = '" . $tracking_number . "' AND shipment_status != '" . Consignment::STATUS_RETURNED . "' AND consignment_type  = 'outbound'", "filter");
                    $consignmentObj =  $consignmentFilter->getListNew();
                    if(count($consignmentObj) > 0){
                        $consignmentId = $consignmentObj[0]->getId();
                    } else {
                        $parcelFilter = new ParcelFilter();
                        $parcelFilter->addTrackingNumberFilter($tracking_number);
                        $parcelFilter->addFieldNotFilter("p.parcel_status_code", Consignment::STATUS_RETURNED);
                        $parcelObj = $parcelFilter->getColumnList('p.consignment_id');
                        if(count($parcelObj) > 0){
                            $consignmentId = $parcelObj[0]->getConsignmentId();
                        }
                    }
                }
                $this->parcel_filter->addFieldFilter("      p.consignment_id", $consignmentId);
                $this->parcel_filter->addFieldNotFilter("      p.parcel_status_code", Consignment::STATUS_RETURNED);
                
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
                    if($dataTableColumnName === "Parcel_tracking_number_filter") {
                        $dataTableColumnName = "p.tracking_number";
                    }
                    $this->parcel_filter->AddOrderBy(strtolower($dataTableColumnName), "order_by", $orderFalse);
                }
            
                /*
                 * Set pagination & Encode data into Json form to return to DataTable
                 */

                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;

                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                //$end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $this->parcel_filter->setRowsPerPage($iDisplayLength);
                $this->parcel_filter->setOffset($iDisplayStart);
                $parcelObjs = $this->parcel_filter->getList(true);
                $iTotalRecords = $this->parcel_filter->getCount();
                foreach ($parcelObjs as $key => $parcelObj) {
                    $serialNumber = $key + 1;
                    $action = '<div class="action_check_box">';
                    $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select_parcel[]" data-consignment_id="' . $parcelObj->getConsignmentId() . '"  data-parcel_id="' . $parcelObj->getId() . '" id="multi_select_' . $serialNumber . '"  value="' . $parcelObj->getId() . '"  class="group-checkable parcelIds parcel-check" onclick="change_check_box(' . $serialNumber . ')" /><span></span></label>';
                    $action .= '</div>';
                    $parcelArr['option'] = $action;
                    
                    $parcelArr['parcel_tracking_number_filter'] = "<div class='big-field'><input type='text' name='old_tracking_numbers[]' class='form-control' placeholder='Old Tracking' value='" . $parcelObj->getTrackingNumber() . "'  readonly='readonly' ></div>";
                    $parcelArr['parcel_new_tracking_number_filter'] = "<div class='big-field'><input type='text' name='new_tracking_numbers[]' id='new_tracking_" . $serialNumber . "' class='form-control new_tracking_numbers' placeholder='New Tracking' readonly='readonly' ></div>";
                    $parcelDataArr[] = $parcelArr;
                }
            }
            $consignmentDataarr['data'] = $parcelDataArr;
            $consignmentDataarr['draw'] = $sEcho;
            $consignmentDataarr['recordsTotal'] = $iTotalRecords;
            $consignmentDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($consignmentDataarr,JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_rack_shelf') {
            $output = [];
            $rack_id = $this->form_vars['rack_id'];
            $rackShelfObj = new RackShelfFilter();
            $rackShelfObj->addFilter("is_filled = 0 and rs.rack_id = '" . $rack_id . "'");
            $rackshelfs = $rackShelfObj->getList();
            $rack = new Rack($rack_id);
            $title = $rack->getShortTitle();
            $html = '';
            $html .= '<select id="rackShelf" name="rackShelf" class="select2 form-filter" >';
            foreach ($rackshelfs as $rackshelf) {
                $shelf_id = $rackshelf->getId();
                $shelf_no = $rackshelf->getShelfNo();
                $html .= "<option value=" . $shelf_id . ">" . $title . " " . ($shelf_no + 1) . "</option>";
            }
            $html .= '</select>';
            $output['status'] = "success";
            $output['html'] = $html;
            echo json_encode($output);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveParcelAndConsignment') {
            $output = [];
            $parcelIds = $this->form_vars['multi_select_parcel'];
            if(count($parcelIds) > 0) {
                $consignmentIds = explode(",", $this->form_vars['consignmentIds']);
                $consignmentId = $consignmentIds[0];
                /* duplicate consignment */
                $consignmentObj = new Consignment($consignmentId);
                $consignmentObj->setId(NULL);
                $consignmentObj->setAwb(NULL);
                $consignmentObj->setShipmentStatus(Consignment::STATUS_NEW);//int
                $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_NEW]);//str
                $consignmentObj->setConsignmentType('return');
                $consignmentObj->save();
                /* end duplicate consignment */
                foreach($parcelIds as $key => $parcelId) { 
                    /* change old parcel status */
                    $oldParcelObj = new Parcel($parcelId);
                    $oldParcelObj->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_RETURNED]);
                    $oldParcelObj->setParcelStatusCode(Consignment::STATUS_RETURNED);
                    $oldParcelObj->save();
                    /* end change old parcel status */
                    /* duplicate parcel */
                    $parcelObj = new Parcel($parcelId);
                    $parcelObj->setId(NULL);
                    $parcelObj->setConsignmentId($consignmentObj->getId());
                    $parcelObj->setTrackingNumber($this->form_vars['new_tracking_numbers'][$key]);
                    $parcelObj->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_NEW]);
                    $parcelObj->setParcelStatusCode(Consignment::STATUS_NEW);
                    $parcelObj->save();
                    /* end duplicate parcel */
                }
                /* update old consignment */
                $oldConsignmentObj = new Consignment($consignmentId);
                $newStatus = Consignment::getConsignmentReturnStatus($consignmentId);
                $oldConsignmentObj->setShipmentStatus($newStatus);
                $oldConsignmentObj->setConsignmentStatus(Consignment::$database_status_array[$newStatus]);
                $oldConsignmentObj->save();
                /* end update old consignment */
                Consignment::getInstantLabel($consignmentObj);
                $location = $this->form_vars['location'];
                $rackShelf = $this->form_vars['rackShelf'];
                if(!empty($location)) {
                    $date_added = date('Y-m-d H:i:s',time());
                    $added_by = $this->user->getId();
                    $date_update = date('Y-m-d H:i:s',time());
                    $update_by = $this->user->getId();
                    if(!empty($rackShelf)) {
                        $rackShelfItem = new RackShelfItem();
                        $rackShelfItem->setTrackingNumber($consignmentObj->getAwb());
                        $rackShelfItem->setDescription($consignmentObj->getDescription());
                        $rackShelfItem->setAddedDate($date_added);
                        $rackShelfItem->setAddedBy($added_by);
                        $rackShelfItem->setUpdatedDate($date_update);
                        $rackShelfItem->setUpdatedBy($update_by);
                        $rackShelfItem->save();
                        
                        $consignmentUser = new User($consignmentObj->getUserId());
                        $logRackShelf = new LogRackShelf();
                        $logRackShelf->setRackShelfId($rackShelf);
                        $logRackShelf->setRackShelfItemId($rackShelfItem->getId());
                        $logRackShelf->setCustomerId($consignmentUser->getUserAccountId());
                        $logRackShelf->setInDate($date_added);
                        $logRackShelf->setInBy($this->user->getId());
                        $logRackShelf->save();
                    }
                }
            }
            $output['status'] = "success";
            $output['message'] = "Return Shipnment is save successfully";
            echo json_encode($output);
            die;
        }
        
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "return_consignment.php?action=get_parcel";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            // execute some code after table records loaded 
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
                            //                            "oLanguage": {
                            //                                "sEmptyTable": "<?php //echo ((isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') ? 'No records found' : 'Please search to view data');       ?>"
                            //                            },
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {

                                }
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "parcel_tracking_number_filter", "bSortable": false},
                                {"data": "parcel_new_tracking_number_filter", "bSortable": false}
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
            $(document).ready(function () {
                DataTableFun.init();
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
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            function getRackShelfs(obj)
            {
                var rack_id = obj.value;
                $.ajax({
                    type: "POST",
                    url: "return_consignment.php",
                    data: {action: 'get_rack_shelf', rack_id: rack_id},
                    dataType: "json",
                    success: function (data)
                    {
                        if (data.status == 'success')
                        {
                            $('#rackShelfDiv').html(data.html);
                            $('#rackShelf').select2();
                            $('#rackShelfMainDiv').show();
                        }
                    }
                });
            }
            function getParcel(obj) {
                var tracking_number = obj.value;
                $.ajax({
                    type: "POST",
                    url: "return_consignment.php",
                    data: {action: 'get_parcel', tracking_number: tracking_number},
                    dataType: "json",
                    success: function (data)
                    {
                        if (data.status == 'success')
                        {
                            $('#rackShelfDiv').html(data.html);
                            $('#rackShelf').select2();
                            $('#rackShelfMainDiv').show();
                        }
                    }
                });
            }
            function change_check_box(id) {
                if($('#multi_select_' + id).is(":checked")) {
                    $('#new_tracking_'+id).removeAttr("readonly");
                } else {
                    $('#new_tracking_'+id).attr("readonly","readonly");
                }
            }
            function select_all_checkbox(obj) {
                if(obj.checked) {
                    $('.new_tracking_numbers').removeAttr("readonly");
                } else {
                    $('.new_tracking_numbers').attr("readonly","readonly");
                }
            }
            function change_selected_action() {
                var parcelIds = [];
                var consignmentIds = [];
                $('.parcelIds:checked').map(function () {
                    parcelIds.push(this.value);
                    consignmentIds.push($(this).data('consignment_id'));
                }).get();
                if (typeof parcelIds !== 'undefined' && parcelIds.length > 0) {
                    var form_data = $("#returnConsignmentForm").serializeArray();
                    form_data.push({name: "action", value: 'saveParcelAndConsignment'});
                    form_data.push({name: "consignmentIds", value: consignmentIds});
                    $.ajax({
                        type: "POST",
                        url: "return_consignment.php",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            grid.getDataTable().ajax.reload();
                            swal("Success!", data.message, "success");
                        },
                        error: function (p1, p2, p3) {
                            swal("Sorry!", "Some thing went wrong please content to admin", "error");
                        }
                    });
                } else {
                    swal("Sorry!", "Please check the checkbox for action", "error");
                }
            }
            function resetForm() {
                document.getElementById("returnConsignmentForm").reset();
            }
        </script>
        <?php
    }

    protected function renderBody() {
        ?>
        <form name="returnConsignmentForm" id="returnConsignmentForm" action="" method="post">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-money"></i>
                        Consignment Return
                    </div>
                    <div class="actions">

                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                    <div class="row display-none" id="res_message">
                        <div class="col-md-12">
                            <div class="alert alert-success"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group big-field">
                                <label>Tracking Number</label>
                                <input type="text" class="form-control form-filter" name="tracking_number" id="tracking_number" placeholder="Tracking Number" value="" >
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 55px;">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-big filter-submit" name="btn_go" id="btn_go" value="Search"> Search </button>&nbsp;
                                <button type="button" class="btn btn-default btn-big filter-cancel" name="btn_rest" value="Reset" onclick="resetForm()"> Reset </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-money"></i>
                        Consignment Return
                    </div>
                    <div class="actions">

                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group big-field">
                                <label>Location</label>
                                <?php echo $this->GetSectionList() ?>
                            </div>
                        </div>
                        <div class="col-md-6" id="rackShelfMainDiv">
                            <div class="form-group big-field">
                                <label>Rack</label>
                                <div id="rackShelfDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span> </span>
                                    <button class="btn btn-sm btn-default btn-big table-group-action-submit" type="button" onclick="change_selected_action()">
                                        <i class="fa fa-check"></i> Submit</button>
                                </div>
                                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>
                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                    <input type='checkbox' name='checkall' class="group-checkable" onclick="select_all_checkbox(this)" />
                                                    <span></span>
                                                </label>
                                            </th>
                                            <th>Old Tracking Number</th>
                                            <th>New Tracking Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }

    public function GetSectionList() {
        $sessionUser = SessionManager::getUser();
        $rackFilter = new RackFilter();
        $rackFilter->addFieldFilter("       r.warehouse_id", $sessionUser->getWarehouseId());
        $list = $rackFilter->getColumnList("id, title");

        $html .= '<select name="location" onchange="getRackShelfs(this)" id="location" class="select2 form-filter" >';
        $html .= "<option value=0>-- Select Section --</option>";
        foreach ($list as $rackShelf) {
            $html .= "<option value=" . $rackShelf->getId() . ">" . $rackShelf->getTitle() . "</option>";
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead() {
        ?>
        <style type="text/css">
            .big-field label{
                font-size:35px !important;
            }
            .btn-big{
                padding: 10px 16px;
                font-size: 40px;
                line-height: 1.33333;
                border-radius: 6px;
            }
            .big-field input{
                height:78px !important;
                font-size:50px !important;
            }
            .select2-container--bootstrap .select2-selection--single{
                height:85px !important;
                font-size:50px !important; 
            }
            #rackShelfMainDiv{
                display: none;
            }
            .mt-checkbox>span, .mt-radio>span{
                height: 50px;
                width: 50px;
            }
            .mt-checkbox>span:after{
                left: 16px;
                top: 0px;
                width: 15px;
                height: 40px;
                border-width: 0 6px 6px 0;
            }
            #manage-data-table>thead>tr>th { 
                line-height: 100px;
                font-size: 35px;
                min-width: 70px;
            }
            .dataTables_extended_wrapper div.dataTables_info, .dataTables_extended_wrapper div.dataTables_length, .dataTables_extended_wrapper div.dataTables_paginate{
                font-size: 20px;
            }
            .dataTables_extended_wrapper div.dataTables_length label{
                font-size: 20px;
            }
            .dataTables_extended_wrapper .table-group-actions>span{
                font-size: 20px;
            }
            #manage-data-table_wrapper div.col-md-8{
                padding: 20px 15px 20px 15px;
            }
            td>.mt-checkbox.mt-checkbox-single, td>.mt-radio.mt-radio-single, th>.mt-checkbox.mt-checkbox-single, th>.mt-radio.mt-radio-single{
                top: -20px;
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