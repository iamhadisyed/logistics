<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([  
                    'invoices.class',
                    'invoicesfilter.class',
                    'pallet.class',
                    'palletfilter.class',
                    'palletcariergroup.class',
                    'palletcariergroupfilter.class', 
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    
    private $palletFilter = array();
    private $user = '';

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Pallet Dispatch Report"
        );
        $userObj = "";
        $userId = "";
        $userIdArr = [];
        $this->user = SessionManager::getUser();
        if($this->user->getUserType() == USER::USER_TYPE_CLIENT){
            util_redirect("index.php");
        }
        
        /*
         * DataTable handlings
         */
//        if($this->user->getUserType() == USER::USER_TYPE_ADMIN){
//            // Add Filter that account and sub account's users pallet shown
//            $childAccount = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), $userId = 0, $includeSelf = false, $recursive = false);
//        }
//        if($this->user->getUserType() == USER::USER_TYPE_CORPORATE){
//            $childAccount = $this->user->getUserAccountId();
//        }
        
        
         $childAccount = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), $userId = 0, $includeSelf = true, $recursive = false);
        $userFilter = new UserFilter();
        $userFilter->addFilterIn("user_account_id", $childAccount);
        $userIdObj = $userFilter->getColumnDistinctList("id");
        foreach ($userIdObj as $userId) {
            $userIdArr[] = $userId->getId();
        }
        $this->palletFilter = new PalletFilter();
        $this->palletFilter->addFilterIn(" p.userid",$userIdArr);
        $this->palletFilter->addDateDispatchIsNotNull();
        $this->palletFilter->addBagJoin();
            
        if (isset($_GET['action']) && $_GET['action'] == "pallet_ajax") {
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $this->palletFilter->addDateFilter($date_created_from, $date_created_to);
                
                $palletNo = $this->form_vars['pallet_no'];
                if (!empty($palletNo)) {
                    $this->palletFilter->addFieldFilter('   p.palletno', $palletNo);
                }

                $bagNo = $this->form_vars['bag_no'];
                if (!empty($bagNo)){
                    $this->palletFilter->addFieldFindInSet('   b.bagnumber', $bagNo);
                }
                
                $carrier = $this->form_vars['carrier'];
                if (!empty($carrier))
                    $this->palletFilter->addFieldFilter('   p.pallet_carrier_id', $carrier);
                            
                $isClose = $this->form_vars['is_close'];
                if ($isClose != '' && ($isClose == 1 || $isClose == 0)){
                    $this->palletFilter->addFieldFilter('   p.close', $isClose);
                }

                $date_created_to = $this->form_vars['date_created_to'];
                $date_created_from = $this->form_vars['date_created_from'];

                if ($date_created_to != '' && $date_created_from == 1){
                    $this->palletFilter->addDateRangeFilter($date_created_to,$date_created_from);
                }

                $isClose = $this->form_vars['is_close'];
                if ($isClose != '' && ($isClose == 1 || $isClose == 0)){
                    $this->palletFilter->addFieldFilter('   p.close', $isClose);
                }
            }

            /*
             * Set columns orders for sorting
             */
            $this->palletFilter->AddOrderBy(' date_dispatch',false);
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
               
                //$this->palletFilter->AddOrderBy(strtolower("rg.".$dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $this->palletFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->palletFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->palletFilter->setOffset($iDisplayStart);
            $palletObjs = $this->palletFilter->getPagingList();
            $setDataArr = array();
            foreach ($palletObjs as $palletObj) {
                $currentArr = array();
                if (!empty($palletObj->getDateDispatch())) {
                    $date_created = formatDate(date("d-m-Y", $palletObj->getDateDispatch()));
                } else {
                    $date_created = "";
                }
                $currentArr['date_created'] = $date_created;
                $currentArr['pallet_no'] = $palletObj->getPalletno();
                $currentArr['bag_no'] = $palletObj->getComments();
                
                $palletCarierGroup = new PalletCarierGroup($palletObj->getPalletCarrierId());
                $currentArr['carrier'] = $palletCarierGroup->getGroupName();
                
                if($palletObj->getClose() == '0'){
                    $currentArr['is_close'] = '<span class="label label-sm label-danger"> <strong>No</strong> </span>';
                }
                else{
                    $currentArr['is_close'] = '<span class="label label-sm label-success"> <strong>Yes</strong> </span>';
                }
                $palletLabelLink = "../_assets/".$palletObj->getLabel();
                if(file_exists($palletLabelLink)){
                    $currentArr['label'] = '<a target="_blank" style="cursor: pointer; cursor: hand;" href="'.$palletLabelLink.'"><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Label"> </i></a>';
                }else{
                    $currentArr['label'] = "";
                }
                $currentArr['actions'] = "";
//                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
//                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
//                                                <i class="fa fa-angle-down"></i>
//                                            </button>
//                                                <ul class="dropdown-menu" >
//                                                    <li>
//                                                        <a data-manifest_id="'.sprintf('%010d', $palletObj->getId()).'" data-service_name="'.$service->getName().'" class="view_detail" href="JavaScript:Void(0);" title="View Details">
//                                                            <i class="fa fa-eye"></i> View
//                                                        </a>
//                                                    </li>
//                                                    <li>
//                                                        <a href="../_assets/manifest/csv/'.$palletObj->getFileName().'" title="View CSV" target="_blank" >
//                                                            <i class="fa fa-download"></i> Download CSV
//                                                        </a>
//                                                    </li>
//                                                    <li>
//                                                        <a href="../_assets/manifest/pdf/'.$palletObj->getPdfFile().'" title="View PDF" target="_blank" >
//                                                            <i class="fa fa-download"></i> Download PDF
//                                                        </a>
//                                                    </li>
//                                                </ul>
//                                            </div>
//                                            ';
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
    
//    protected function applyFilter($data) {
//        
//        $this->form_vars = $data;
//        
//        $date_created_from = $this->form_vars['date_created_from'];
//        $date_created_to = $this->form_vars['date_created_to'];
//        if (!empty($date_created_from) && !empty($date_created_to))
//            $this->palletFilter->addDateFilter($date_created_from, $date_created_to);
//        
//        $manifestId = $this->form_vars['manifest_id'];
//        if (!empty($manifestId))
//            $this->palletFilter->addFieldFilter('   m.id', $manifestId);
//
//        $serviceId = $this->form_vars['service_id'];
//        if (!empty($serviceId))
//            $this->palletFilter->addFieldFilter('    service_id', $serviceId);
//
//        $manifestWeight = $this->form_vars['manifest_weight'];
//        if (!empty($manifestWeight))
//            $this->palletFilter->addFieldFilter('    weight', $manifestWeight);
//
//        $idDispacthed = $this->form_vars['id_dispacthed'];
//        if (!empty($idDispacthed))
//            $this->palletFilter->addFieldFilter('    is_dispatched', $idDispacthed);
//
//        $idEmail = $this->form_vars['id_email'];
//        if (!empty($idEmail))
//            $this->palletFilter->addFieldFilter('    is_send_email', $idEmail);
//    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
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
                                "url": "pallet_dispatch_report.php?action=pallet_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                        {"data": "actions", "bSortable": false},
                                        {"data": "date_created"},
                                        {"data": "pallet_no"},
                                        {"data": "bag_no"},
                                        {"data": "carrier"},
                                        {"data": "is_close"},
                                        {"data": "label"}
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
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
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
                    Pallet Dispatch Report
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <form method="post" action="op_manifest_list.php" id="operation_manifest_list_form">
                    <input type="hidden" name="action" value="download_operation_manifest_list_csv" />
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Date Dispatched</th>
                            <th>Pallet Number</th>
                            <th>Bag Number</th>
                            <th>Carrier</th>
                            <th>Is Closed</th>
                            <th>Label</th>
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
                                <input type="text" class="form-control form-filter input-xs" name="pallet_no" id ="pallet_no" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="bag_no" id ="bag_no" />
                            </td>
                            <td>
                                <?php
                                $dsaf= new PalletCarierGroupFilter();
                                $search_Carrier_id = "";
                                echo Ddl::generateDDL('carrier', 'PalletCarierGroupFilter', ' carrier_id > 0', 'group_name', 'id', '', ' class="form-control select2 form-control input-sm form-filter select2 select2-hidden-accessible" data-toggle="tooltip" data-placement="top" title="Pallet Carreir" data-original-title="Pallet Carreir"', 'Please Select', '', 'carrier_group', 'Carrier Group');
                                ?>
                            </td>
                            <td>
                                <?php
                                    $IsClosedArr = array('' => 'Please Select','1' => 'Yes', '0' => 'No');
                                    $default = "";
                                    echo Ddl::generateArrayDDL('is_close', $IsClosedArr, $default, '', ' class="form-control input-sm form-filter select2 select2-hidden-accessible form-filter form-control select2 select" rel="tooltip" data-original-title="Is Close" placeholder="Is Close"');
                                ?>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </form>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="mawb_detail_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Manifest Detail [ <span id="modal_mawb_number"></span> - <span id="modal_mawb_service"></span> ]</h4>
                    </div>
                    <div class="modal-body">
                         <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Tracking Number </th>
                                        <th> Dims </th>
                                        <th> Weight </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody id="manifest_list_data"> 
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
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