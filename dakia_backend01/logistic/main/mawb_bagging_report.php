<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'mawbparcelmappingfilter.class',
    'mawbparcelmapping.class',
    'mawbfilter.class',
    'mawb.class',
    
    ]);
class Page extends BasePage {
    /* * *
     * Controller logic
     */
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "MAWB Bagging List"
        );
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "mawb_bagging_ajax") {
            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
            $mawbParcelMappingFilter->addJoin("parcel p", "p.id", "mpm.parcel_id");
            $mawbParcelMappingFilter->addJoin("bagging b", "b.id", "mpm.bag_id");
            $mawbParcelMappingFilter->addJoin("mawb m", "m.id", "mpm.mawb_id");
            $mawbParcelMappingFilter->addJoin("warehouse w", "w.id", "mpm.wharehouse_id");
            $mawbParcelMappingFilter->addJoin("user u", "u.id", "mpm.added_by");
            $mawbParcelMappingFilter->addGroupBy("mpm.mawb_id");
            
            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                $userAccounts = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
                $mawbParcelMappingFilter->addFilterIn('    u.user_account_id', $userAccounts);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $mawb = $this->form_vars['mawb'];
                if (!empty($mawb))
                    $mawbParcelMappingFilter->addFieldLikeFilter('   m.mawb_number', $mawb);
                
                $warehouse = $this->form_vars['warehouse'];
                if (!empty($warehouse))
                    $mawbParcelMappingFilter->addFieldFilter('   mpm.wharehouse_id', $warehouse);
                
                $user_account_id = $this->form_vars['user_account_id'];
                if (!empty($user_account_id))
                    $mawbParcelMappingFilter->addFieldFilter('   u.user_account_id', $user_account_id);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $mawbParcelMappingFilter->addDateFilter($date_created_from, $date_created_to, "mpm.added_by");

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
                if($dataTableColumnName == "mawb") {
                    $dataTableColumnName = "mpm.mawb_number";
                }
                if($dataTableColumnName == "warehouse") {
                    $dataTableColumnName = "mpm.wharehouse_id";
                }
                if($dataTableColumnName == "user_account_id") {
                    $dataTableColumnName = "u.user_account_id";
                }
                if($dataTableColumnName == "added_date") {
                    $dataTableColumnName = "mpm.date_added";
                }
                $mawbParcelMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $mawbParcelMappingFilter->getMawbBaggingPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $mawbParcelMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $mawbParcelMappingFilter->setOffset($iDisplayStart);
            $mawbParcelMappingFilterObjs = $mawbParcelMappingFilter->getPagingList("mpm.id,mpm.wharehouse_id,mpm.parcel_id,mpm.mawb_id,mpm.bag_id,mpm.date_added,mpm.added_by,w.warehouse_name,COUNT(DISTINCT (mpm.bag_id)) AS total_scan,b.bagnumber AS bag_number,u.user_account_id AS user_id ");
            $setDataArr = array();
            foreach ($mawbParcelMappingFilterObjs as $key => $mawbParcelMappingFilterObj) {
                /* count Total Parcel */
                $mawbParcelMappingFilterParcelCount = new MawbParcelMappingFilter();
                $mawb = new Mawb($mawbParcelMappingFilterObj->getMawbId());
                $mawbNumber = $mawb->getMawbNumber();
//                $bagId = $mawbParcelMappingFilterObj->getBagId();
//                if(!empty($bagId)) {
//                    $mawbParcelMappingFilterParcelCount->addFieldFilter('    mpm.bag_id', $bagId);
//                } else {
//                    $mawbParcelMappingFilterParcelCount->addFilter('    (mpm.bag_id IS NULL OR mpm.bag_id = 0)');
//                }
                $mawbParcelFilter = $mawbParcelMappingFilterObj->getMawbId();
                if(!empty($mawbParcelFilter)) {
                    $mawbParcelMappingFilterParcelCount->addFieldFilter('    mpm.mawb_number', $mawbParcelFilter);
                }
                
                $mawbParcelMappingFilterParcelCount->addGroupBy("   mpm.parcel_id");
                $totalParcels = $mawbParcelMappingFilterParcelCount->getMawbBaggingPagingCount();

                /* end count Total Parcel */
                if(count($totalParcels) > 0) {
                    $totaParcel = '<a href="javascript:;" onclick="get_parcel_details(' . "'". $mawbParcelMappingFilterObj->getMawbId() . "'" .')" >'.$totalParcels.'</a>';
                } else {
                    $totaParcel = "N/A";
                }
                if($mawbParcelMappingFilterObj->getTotalScan() > 0) {
                    $totalBags = '<a title="View Bag Detail" href="javascript:;" onclick="get_bag_details('. "'" . $mawbParcelMappingFilterObj->getMawbId() . "','" . $mawbNumber . "'" .')"> '. $mawbParcelMappingFilterObj->getTotalScan() .' </a>';
                } else {
                    $totalBags = "N/A";
                }
                
                if(!empty($mawbParcelMappingFilterObj->getDateAdded())) {
                    $dateAdded = date("d-m-Y", $mawbParcelMappingFilterObj->getDateAdded());
                } else {
                    $dateAdded = "";
                }
                $userAccountObj = new CustomerAccount($mawbParcelMappingFilterObj->getUserId());
                $currentArr = array();
                $currentArr['mawb'] = $mawbNumber;
                $currentArr['warehouse'] = $mawbParcelMappingFilterObj->getWarehouseName();
                $currentArr['total_bags'] = $totalBags;
                $currentArr['total_parcel'] = $totaParcel;
                $currentArr['added_by'] = $userAccountObj->getUserAccount();
                $currentArr['added_date'] = $dateAdded;
                $currentArr['actions'] = ($key+1);
//                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
//                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
//                                                <i class="fa fa-angle-down"></i>
//                                            </button>
//                                            <ul class="dropdown-menu" >';
//                    $currentArr['actions'] .=   '<li>
//                                                    <a title="View Bag Detail" href="javascript:;" onclick="get_bag_details('. "'" . $mawbParcelMappingFilterObj->getMawbNumber() . "'" .')">
//                                                        <span class="glyphicon glyphicon-eye-open"></span> View Bag Detail
//                                                    </a>
//                                                </li>';
//                $currentArr['actions'] .= '</ul>
//                                        </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == "bag_Detail_ajax") {
            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
            $mawbParcelMappingFilter->addJoin("bagging b", "b.id", "mpm.bag_id");
            $mawbParcelMappingFilter->addJoin("user u", "u.id", "b.user_id");
            $mawb = $this->form_vars['mawb_filter'];
            if(!empty($mawb)) {
                $mawbParcelMappingFilter->addFieldFilter('   mpm.mawb_id', $mawb);
            }
            $mawbParcelMappingFilter->addGroupBy("mpm.bag_id");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $bagNumber = $this->form_vars['bag_number'];
                if (!empty($bagNumber))
                    $mawbParcelMappingFilter->addFieldFilter('    b.bagnumber', $bagNumber);
                
                $user = $this->form_vars['user'];
                if (!empty($user))
                    $mawbParcelMappingFilter->addFieldLikeFilter('    u.user_name', $user);
                
                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $mawbParcelMappingFilter->addFieldLikeFilter('    b.actual_weight', $weight);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $mawbParcelMappingFilter->addDateFilter($date_created_from, $date_created_to, "b.date_created");

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
                if($dataTableColumnName == "mawb") {
                    $dataTableColumnName = "mpm.mawb_number";
                }
                if($dataTableColumnName == "warehouse") {
                    $dataTableColumnName = "mpm.wharehouse_id";
                }
                if($dataTableColumnName == "user_account_id") {
                    $dataTableColumnName = "u.user_account_id";
                }
                if($dataTableColumnName == "added_date") {
                    $dataTableColumnName = "mpm.date_added";
                }
                $mawbParcelMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $mawbParcelMappingFilter->getMawbBaggingPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $mawbParcelMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $mawbParcelMappingFilter->setOffset($iDisplayStart);
            $mawbParcelMappingFilterObjs = $mawbParcelMappingFilter->getPagingList("b.`id`,b.`bagnumber` AS bag_number,b.`date_created` AS date_added,b.`pdf`,b.`bag_label`,b.`user_id`,b.`actual_weight`,u.user_name AS user_id,mpm.mawb_id ");
            $setDataArr = array();
            foreach ($mawbParcelMappingFilterObjs as $key => $mawbParcelMappingFilterObj) {
                if(!empty($mawbParcelMappingFilterObj->getDateAdded())) {
                    $dateAdded = date("d F Y", $mawbParcelMappingFilterObj->getDateAdded());
                } else {
                    $dateAdded = "";
                }
                $currentArr = array();
                $currentArr['bag_number'] = $mawbParcelMappingFilterObj->getBagNumber();
                $currentArr['user'] = $mawbParcelMappingFilterObj->getUserId();
                $currentArr['weight'] = $mawbParcelMappingFilterObj->getActualWeight();
                $currentArr['added_bag_date'] = $dateAdded;
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                    if(!empty($mawbParcelMappingFilterObj->getPdf())) {
                        $currentArr['actions'] .=   '<li>
                                                        <a title="Download PDF" href="' . $mawbParcelMappingFilterObj->getPdf() . '" target="_blank" >
                                                            <span class="fa fa-download"></span> Download PDF
                                                        </a>
                                                    </li>';
                    }
                    if(!empty($mawbParcelMappingFilterObj->getBagLabel())) {
                        $currentArr['actions'] .=   '<li>
                                                        <a title="Download Label" href="' . $mawbParcelMappingFilterObj->getBagLabel() . '" target="_blank" >
                                                            <span class="fa fa-download"></span> Download Label
                                                        </a>
                                                    </li>';
                    }
                        $currentArr['actions'] .=   '<li>
                                                        <a title="Delete" href="javascript:;" onclick="delete_bag(' . $mawbParcelMappingFilterObj->getId() . ',' . "'" . $mawbParcelMappingFilterObj->getMawbId()  . "'" . ')">
                                                            <span class="fa fa-trash"></span> Delete Bag
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
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete_bag") {
            $output = [];
            $bagId = $this->form_vars['bag_id'];
            $mawb = $this->form_vars['mawb'];
            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
            $mawbParcelMappingFilter->addFieldFilter("      mawb_number", $mawb);
            $mawbParcelMappingFilter->addFieldFilter("      bag_id", $bagId);
            $mawbParcelMappingFilter->delete_parcel_from_mapping();
            $output['status'] = "success";
            $output['message'] = "Your bag is deleted successfully";
            echo json_encode($output);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == "parcel_detail_ajax") {
            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
            $mawbParcelMappingFilter->addJoin("parcel p", "p.id", "mpm.parcel_id");
            $mawbParcelMappingFilter->addJoin("bagging b", "b.id", "mpm.bag_id");
            $mawbParcelFilter = $this->form_vars['mawb_parcel_filter'];
            if(!empty($mawbParcelFilter)) {
                $mawbParcelMappingFilter->addFieldFilter('    mpm.mawb_number', $mawbParcelFilter);
            }
            $mawbParcelMappingFilter->addGroupBy("mpm.parcel_id");
            
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $trackingNumber = $this->form_vars['tracking_number'];
                if (!empty($trackingNumber))
                    $mawbParcelMappingFilter->addFieldFilter('    p.tracking_number', $trackingNumber);
                
                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $mawbParcelMappingFilter->addFieldFilter('    p.weight', $weight);
                
                $parcelBagNumber = $this->form_vars['parcel_bag_number'];
                if (!empty($parcelBagNumber))
                    $mawbParcelMappingFilter->addFieldFilter('    b.bagnumber', $parcelBagNumber);

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
            $iTotalRecords = $mawbParcelMappingFilter->getMawbBaggingPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $mawbParcelMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $mawbParcelMappingFilter->setOffset($iDisplayStart);
            $parcelBaggingMappingFilterobjs = $mawbParcelMappingFilter->getPagingList("p.`id`,p.`tracking_number`,p.`length`,p.`width`,p.`height`,p.`weight`,p.`parcel_status_code`,mpm.bag_id,b.bagnumber AS bag_number ");
            $setDataArr = array();
            foreach ($parcelBaggingMappingFilterobjs as $key => $parcelBaggingMappingFilterobj) {
                $shipment_status = Consignment::getShipnmentStatus($parcelBaggingMappingFilterobj->getParcelStatusCode());
                $currentArr = array();
                $currentArr['parcel_bag_number'] = $parcelBaggingMappingFilterobj->getBagNumber();
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
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addFieldFilter("      parcel_id", $parcelId);
            $parcelBaggingMappingFilter->addFieldFilter("      bag_id", $bagId);
            $parcelBaggingMappingFilter->delete_parcel_from_mapping();
            $output['status'] = "success";
            $output['message'] = "Your parcel is deleted successfully";
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
                    var datatableurl = "mawb_bagging_report.php?action=mawb_bagging_ajax";
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
                                {"data": "mawb"},
                                {"data": "warehouse"},
                                {"data": "total_bags", "bSortable": false},
                                {"data": "total_parcel", "bSortable": false},
                                {"data": "added_by"},
                                {"data": "added_date"}
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
            var bagGrid = null;
            var BagDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bagdatatableurl = "mawb_bagging_report.php?action=bag_Detail_ajax";
                    bagGrid = new Datatable();
                    bagGrid.init({
                        src: $("#bag-Detail-table"),
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
                                {"data": "bag_number"},
                                {"data": "user", "bSortable": false},
                                {"data": "weight"},
                                {"data": "added_bag_date"}
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
            var parcelGrid = null;
            var parcelDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bagdatatableurl = "mawb_bagging_report.php?action=parcel_detail_ajax";
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
                                {"data": "parcel_bag_number"},
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
                BagDataTableFun.init();
                parcelDataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            function get_bag_details(mawb,mawbNumber) {
                $('#mawb_filter').val(mawb);
                $('#bag_mawb').html(mawbNumber);
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    bagGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    bagGrid.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    bagGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                bagGrid.submitFilter();
                $('#bag_detail_model').modal('show');
            }
            function delete_bag(bag_id,mawb) {
                var form_data = new FormData();
                form_data.append('bag_id', bag_id);
                form_data.append('mawb', mawb);
                form_data.append('action', 'delete_bag');
                $.ajax({
                        url: 'mawb_bagging_report.php',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            if(data.status == "success") {
                                grid.getDataTable().ajax.reload();
                                bagGrid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");   
                            }
                        },
                        error: function () {
                            swal("Sorry!", "something went wrong please content to admin", "error");
                        }
                });
            }
            function get_parcel_details(mawb) {
                $('#mawb_parcel_filter').val(mawb);
                $('#parcel_mawb').html(mawb);
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
                $.ajax({
                        url: 'mawb_bagging_report.php',
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
                    Mawb Bagging List
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
                            <th>Mawb Number</th>
                            <th>Warehouse</th>
                            <th>Total Bags</th>
                            <th>Total parcels</th>
                            <th>Added By</th>
                            <th>Date Added</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td >
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                            </td>
                            <td  class="user_acccount_correct_button"> 
                                <input type="text" name="mawb" id="mawb" class="form-control form-filter" >
                            </td>
                            <td  class="user_acccount_correct_button">
                               <?php
                                    $warehouseArr = "";
                                    $logddedInWareouse = getLoggedInUserChildWarehouse();
                                    foreach ($logddedInWareouse as $logddedWareouse) {
                                        $warehouseArr.= '<option value="'.$logddedWareouse->getId().'">'.$logddedWareouse->getWarehouseName().'</option>';
                                    }
                                ?>
                                <select id="warehouse" name="warehouse" class="form-control form-filter select2 select2-hidden-accessible" title="" placeholder="Select Warehouse" tabindex="-1" aria-hidden="true" data-original-title="Select Warehouse">
                                    <option value="">Please Select</option>
                                    <?php echo $warehouseArr; ?>
                                </select>
                            </td>
                            <td  class="user_acccount_correct_button">
                               
                            </td>
                            <td  class="user_acccount_correct_button">
                               
                            </td>
                            <td  class="user_acccount_correct_button">
                                 <?php
                                    $accountParentId = 0;
                                    $includeParent = true;
                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                        $accountParentId = $this->user->getUserAccountId();
                                        $includeParent = false;
                                    }
                                    $selectedAccount = "";
                                     $allowedLevel = 0;
                                     if (Permissions::checkFilePermission('hide_subaccount')) {
                                         $allowedLevel = 1;
                                     }
                                ?>
                                <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent, $allowedLevel); ?>
                            </td>
                            <td class="user_acccount_correct_button">
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
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="bag_detail_model" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Bag Detail List [<span id="bag_mawb"></span>]</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="portlet light">
                                    <div class="portlet-body">   
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover table-condensed" id="bag-Detail-table">                     
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th>Actions</th>
                                                        <th>Bag Number</th>
                                                        <th>User</th>
                                                        <th>Weight</th>
                                                        <th>Date Added</th>
                                                    </tr>
                                                    <tr role="row" class="filter">
                                                        <td>
                                                            <div class="margin-bottom-5">
                                                                <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="mawb_filter" id="mawb_filter" class="form-filter" >
                                                           <input type="text" name="bag_number" id="bag_number" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="user" id="user" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                             <input type="text" name="weight" id="weight" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                                <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                            <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                                <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
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
        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_detail_model" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel Detail List [<span id="parcel_mawb"></span>]</h4>
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
                                                        <th>Bag Number</th>
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
                                                            <input type="text" name="parcel_bag_number" id="parcel_bag_number" class="form-control form-filter" >
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="mawb_parcel_filter" id="mawb_parcel_filter" class="form-filter" >
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