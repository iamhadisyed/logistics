<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'Bagging.class']);
class Page extends BasePage {
    /* * *
     * Controller logic
     */
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Bagging Parcel List"
        );
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "parcel_bagging_mapping_ajax") {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addJoin("parcel p", "p.id", "pbm.parcel_id");
            $parcelBaggingMappingFilter->addJoin("bagging b", "b.id", "pbm.bag_id");
            $parcelBaggingMappingFilter->addJoin("user u", "u.id", "b.user_id");
            $parcelBaggingMappingFilter->addGroupBy("pbm.bag_id");
            $parcelBaggingMappingFilter->orderBy("pbm.added_date", 'DESC');

            
           
            $userAccountId = $this->user->getUserAccountId();
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("user_account_id", $userAccountId);
            $userObj = $userFilter->getColumnList("id");
            if(count($userObj) > 0){
                foreach ($userObj as $user) {
                    $userIdArr[] = $user->getId();
                }
            }
            $parcelBaggingMappingFilter->addFilterIn("    u.id", $userIdArr);
            
//            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
//                $userAccounts = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
//                $mawbParcelMappingFilter->addFilterIn('    u.user_account_id', $userAccounts);
//            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $bagNumber = $this->form_vars['bag_number'];
                if (!empty($bagNumber))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    b.bagnumber', $bagNumber);

                $isClosed = $this->form_vars['is_closed'];
                if (!empty($isClosed))
                    $parcelBaggingMappingFilter->addFilter('    b.is_closed', $isClosed);

                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    b.`actual_weight`', $weight);

                $dateCreated = $this->form_vars['date_created'];
                if (!empty($dateCreated))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    b.`date_created`', date('Y-m-d', strtotime($dateCreated)));
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
                if($dataTableColumnName === "bag_number") {
                    $dataTableColumnName = "        b.bagnumber";
                }
                $parcelBaggingMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $parcelBaggingMappingFilter->getParcelPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelBaggingMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $parcelBaggingMappingFilter->setOffset($iDisplayStart);
            $parcelBaggingMappingFilterObjs = $parcelBaggingMappingFilter->getPagingList("pbm.id,pbm.parcel_id,pbm.bag_id,COUNT(DISTINCT (pbm.parcel_id)) AS total_parcel,b.`bagnumber` as bag_number,b.`id`,b.`user_id`,b.`actual_weight` AS weight,u.user_name AS username,b.`date_created`,b.`is_closed` AS is_closed");
            $setDataArr = array();
            foreach ($parcelBaggingMappingFilterObjs as $parcelBaggingMappingFilterObj) {
                if(!empty($parcelBaggingMappingFilterObj->getDateCreated())) {
                    $dateAdded = date("d-m-Y", strtotime($parcelBaggingMappingFilterObj->getDateCreated()));
                } else {
                    $dateAdded = "";
                }
                $bagClosed = '<span class="label label-sm label-success">Open</span>';
                //$parcelBaggingMappingFilterObj->getIsClosed()
                if($parcelBaggingMappingFilterObj->getIsClosed())
                    $bagClosed = '<span class="label label-sm label-warning">Closed</span>';
                $currentArr = array();
                $currentArr['bag_number'] = $parcelBaggingMappingFilterObj->getBagNumber();
                $currentArr['user'] = $parcelBaggingMappingFilterObj->getUsername();
                $currentArr['weight'] = $parcelBaggingMappingFilterObj->getWeight();
                $currentArr['date_created'] = !empty($dateAdded) ? formatDate($dateAdded) : '';
                $currentArr['is_closed'] = $bagClosed;
                $currentArr['total_parcel'] = $parcelBaggingMappingFilterObj->getTotalParcel();
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                    $currentArr['actions'] .=   '<li>
                                                    <a title="View Parcel Detail" href="javascript:;" onclick="get_parcel_details('. "'" . $parcelBaggingMappingFilterObj->getBagId() . "'" .')">
                                                        <span class="glyphicon glyphicon-eye-open"></span> View Parcel Detail
                                                    </a>
                                                    <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $parcelBaggingMappingFilterObj->getBagId() . '" 
                                            data-log_name="bagging" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                                            </a>
                                                </li>';
                $currentArr['actions'] .= '</ul>
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
        
        if (isset($_GET['action']) && $_GET['action'] == "parcel_detail_ajax") {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addJoin("parcel p", "p.id", "pbm.parcel_id");
            $parcelBaggingMappingFilter->addJoin("bagging b", "b.id", "pbm.bag_id");
            
            $bagId = $this->form_vars['bag_filter'];
            if(!empty($bagId)) {
                $parcelBaggingMappingFilter->addFieldFilter('    pbm.bag_id', $bagId);
            }
            $parcelBaggingMappingFilter->addGroupBy("pbm.parcel_id");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $trackingNumber = $this->form_vars['tracking_number'];
                if (!empty($trackingNumber))
                    $parcelBaggingMappingFilter->addFieldFilter('    p.tracking_number', $trackingNumber);
                
                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    p.weight', $weight);

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

                if($dataTableColumnName == "tracking_number") {
                    $dataTableColumnName = "p.tracking_number";
                }
                $parcelBaggingMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $parcelBaggingMappingFilter->getParcelPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelBaggingMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $parcelBaggingMappingFilter->setOffset($iDisplayStart);
            $parcelBaggingMappingFilterobjs = $parcelBaggingMappingFilter->getPagingList("p.`id`,p.`tracking_number`,p.`length`,p.`width`,p.`height`,p.`weight`,p.`parcel_status_code`,b.id AS bag_id");
            $setDataArr = array();
            foreach ($parcelBaggingMappingFilterobjs as $key => $parcelBaggingMappingFilterobj) {
                $shipment_status = Consignment::getShipnmentStatus($parcelBaggingMappingFilterobj->getParcelStatusCode());
                $currentArr = array();
                $currentArr['tracking_number'] = $parcelBaggingMappingFilterobj->getTrackingNumber();
                $currentArr['dims'] = $parcelBaggingMappingFilterobj->getLength() . " x " . $parcelBaggingMappingFilterobj->getWidth() . " x " . $parcelBaggingMappingFilterobj->getHeight();
                $currentArr['weight'] = $parcelBaggingMappingFilterobj->getWeight();
                $currentArr['status'] = $shipment_status;
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                        $currentArr['actions'] .=   '<li>
                                                        <a title="Delete Parcel" href="javascript:;" onclick="delete_parcel(' . "'" . $parcelBaggingMappingFilterobj->getId() . "'" . ',' . "'" . $parcelBaggingMappingFilterobj->getBagId()  . "'" . ')">
                                                            <span class="fa fa-trash"></span> Delete parcel
                                                        </a>
                                                    </li>';
                $currentArr['actions'] .= '</ul>
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
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete_parcel") {
            $output = [];
            $bagId = $this->form_vars['bag_id'];
            $parcelId = $this->form_vars['parcel_id'];
            if($bagId > 0 && $parcelId > 0){
                // Check if bag is closed
                $bagging = new Bagging($bagId);
                if($bagging->getIsClosed() == 1){
                    $output['status'] = "error";
                    $output['message'] = "Bag is closed. Cannot remove parcel";
                }else{
                    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                    $parcelBaggingMappingFilter->addFieldFilter("      parcel_id", $parcelId);
                    $parcelBaggingMappingFilter->addFieldFilter("      bag_id", $bagId);
                    $parcelBaggingMappingFilter->delete_parcel_from_mapping();
                    $output['status'] = "success";
                    $output['message'] = "Your parcel is deleted successfully";
                }
            }else{
                $output['status'] = "error";
                $output['message'] = "parcel or bag not found";
            }
            echo json_encode($output);
            die;
        }
        
    }
    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
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
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "bagging_parcel_list.php?action=parcel_bagging_mapping_ajax";
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
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "bag_number"},
                                {"data": "user"},
                                {"data": "weight"},
                                {"data": "date_created"},
                                {"data": "is_closed"},
                                {"data": "total_parcel", "bSortable": false}
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
            var parcelGrid = null;
            var parcelDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bagdatatableurl = "bagging_parcel_list.php?action=parcel_detail_ajax";
                    parcelGrid = new Datatable();
                    parcelGrid.init({
                        src: $("#parcel-Detail-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error  
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options 
                            "lengthMenu": [
                                [10, 20, 50, 100, 150],
                                [10, 20, 50, 100, 150] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": bagdatatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "tracking_number"},
                                {"data": "dims", "bSortable": false},
                                {"data": "weight"},
                                {"data": "status", "bSortable": false}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        BaghandleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                DataTableFun.init();
                parcelDataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                <?php if(!empty($_GET['bag_number'])){ ?>
                    $(".filter-submit").trigger( "click" );
                <?php } ?>
            });
            function get_parcel_details(bag_id) {
                $('#bag_filter').val(bag_id);
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    parcelGrid.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                parcelGrid.submitFilter();
                $('#parcel_detail_model').modal('show');
            }
            function delete_parcel(parcel_id,bag_id) {
                var form_data = new FormData();
                form_data.append('parcel_id', parcel_id);
                form_data.append('bag_id', bag_id);
                form_data.append('action', 'delete_parcel');
                
                swal({
                    title: "Are you sure you want to remove parcel form bag",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                                url: 'bagging_parcel_list.php',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == "success") {
                                        grid.getDataTable().ajax.reload();
                                        parcelGrid.getDataTable().ajax.reload();
                                        swal("Success!", data.message, "success");   
                                    }
                                },
                                error: function () {
                                    swal("Sorry!", "something went wrong please content to admin", "error");
                                }
                        });
                    }
                });
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Bagging Parcel List
                </div>
                <div class="actions">
                    
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Bag Number</th>
                            <th>User</th>
                            <th>Weight</th>
                            <th>Date Created</th>
                            <th>Bag Status</th>
                            <th>Total Parcel</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                            </td>
                            <td>
                                <input value="<?php if(!empty($_GET['bag_number'])){ echo $_GET['bag_number'];} ?>" type="text" name="bag_number" id="bag_number" class="form-control form-filter" >
                            </td>
                            <td>
                                <input type="text" name="user" id="user" class="form-control form-filter" >
                            </td>
                            <td>
                                <input type="text" name="weight" id="weight" class="form-control form-filter" >
                            </td>
                            <td>
                                <input value="" class="form-control form-filter date-picker" name="date_created" id="date_created" type="text" placeholder="Date Created"/>
<!--                                <input type="text" name="date_created" id="date_created" class="form-control form-filter" >-->
                            </td>
                            <td>
                                <?php $IsOpen = array(''=>'Select','1' => 'Closed', '0' => 'Open');
                                echo Ddl::generateArrayDDL('is_closed', $IsOpen, "", '', ' class="form-control form-filter select2 select" rel="tooltip" data-original-title="Bag Status" placeholder="Bag Status"'); ?>
                            </td>
                            <td>
                              
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_detail_model" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel Detail List</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="portlet light">
                                    <div class="portlet-body">   
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover table-condensed" id="parcel-Detail-table">                     
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th>Actions</th>
                                                        <th>Tracking Number</th>
                                                        <th>Dim(L x W x H)</th>
                                                        <th>Weight(KG)</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    <tr role="row" class="filter">
                                                        <td>
                                                            <div class="margin-bottom-5">
                                                                <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="bag_filter" id="bag_filter" class="form-filter" >
                                                            <input type="text" name="tracking_number" id="tracking_number" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                            
                                                        </td>
                                                        <td>
                                                             <input type="text" name="weight" id="weight" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                            
                                                        </td>
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
                    </div>
                    <div class="modal-footer">
                        
                    </div>
                </div>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">

            </form>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>