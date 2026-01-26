<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
        'parcelfilter.class',
        'parcel.class',
]);
/* * *
 * Page for editing a user
 * Not needed this file as per shabbir sir we can do this ob find way bill screen 05-11-2019
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;
    private $selected_user;

    protected function init() {
        $this->user = SessionManager::getUser();
        if($_GET['action'] && $_GET['action'] == 'scannedParcelReport'){
            
            $parcelObj = new ParcelFilter();
            $user_account_id = $this->user->getUserAccountId();
            $userId = 0;
            $trackingNumber = 0;
            $mawb = 0;
            $searchDateCreated = "";
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                if(!empty($this->form_vars['search_user_account_id'])){
                    $user_account_id = $this->form_vars['search_user_account_id'];
                }
                if(!empty($this->form_vars['search_trackingnumber'])){
                    $trackingNumber = $this->form_vars['search_trackingnumber'];
                }
                if(!empty($this->form_vars['search_consignmentid'])){
                    $mawb = $this->form_vars['search_consignmentid'];
                }
                if(!empty($this->form_vars['user_id'])){
                    $userId = $this->form_vars['user_id'];
                }
                $searchDateCreated = $this->form_vars['search_date_created'];
            }
            
            $allouedAcccounts[] = $user_account_id;
            if(empty($this->form_vars['search_user_account_id'])){
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                    $user_account_id = '';
                    $allouedAcccounts = [];
                }
            }
//            echo '<pre>';
//            print_r($allouedAcccounts);
//            echo '</pre>';
//            die();
            $wareHouseId = $this->user->getWarehouseId();
            
            $parcelCount = $parcelObj->getViewScannedParcelReportCount($allouedAcccounts,$wareHouseId,$userId,$trackingNumber,$mawb,$searchDateCreated);
            
            $iTotalRecords = (!empty($parcelCount)?$parcelCount[0]->getId():'0');
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelObj->setRowsPerPage($iDisplayLength);
            $parcelObj->setOffset($iDisplayStart);
            $viewScanedParcelObj = $parcelObj->scannedParcelReport($allouedAcccounts,$wareHouseId,$userId,$trackingNumber,$mawb,$searchDateCreated);
            if(!empty($viewScanedParcelObj)){
                foreach($viewScanedParcelObj as $key => $viewData){
                    $currentArr = array();
                    $currentArr['date'] = date("d-m-Y",strtotime($viewData->getDescription()));
                    $currentArr['account'] = $viewData->getUserAccount();
                    $currentArr['trackingNumber'] = $viewData->getTrackingNumber();
                    $currentArr['consignmentId'] = '<a href="'.BASE_URL.'shipment_edit.php?from=client&id='.$viewData->getConsignmentId().'" target="_blank">'.$viewData->getQty().'</a>';
                    $currentArr['dims'] = $viewData->getDims();
                    $currentArr['scanby'] = $viewData->getGrossWeight();
                    $currentArr['edit'] = '';
                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }
        
        

    }

    protected function addPagelavelCss() {
        ?>
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
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

<!--        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>-->
<!--        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>-->
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
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
                                "url": "view_scaned_parcel_report.php?action=scannedParcelReport", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false},
                                {"data": "date"},
                                {"data": "consignmentId", "bSortable": false},
                                {"data": "trackingNumber", "bSortable": false},
                                {"data": "account", "bSortable": false},
                                {"data": "dims", "bSortable": false},
                                {"data": "scanby", "bSortable": false},
                            ],
                            rowCallback: function (row, data, index) {
                                var cssClass = $('td input', row).val();
                                //$('td',row).removeClass("sorting_1").addClass(cssClass);
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
               <?php if(!empty($_GET['id'])){?>
                       $('.filter-submit').trigger('click');
               <?php }?>
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose:true,
                        endDate: "today",
                        maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });


                    $('.date-picker').keyup(function () {
                        if (this.value.match(/[^0-9]/g)) {
                            this.value = this.value.replace(/[^0-9^-]/g, '');
                        }
                    });
                }
            });
            
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .in-transit{
                color: #000 !important;
                background-color: #FF3535 !important;
            }
            .in-warehouse{
                color: #000 !important;
                background-color: #A3F46C !important;
            }
            .default-color{
                color: #000 !important;
                background-color: #FFB164 !important;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
<!--        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/prealert.php">Pre Alert</a></li>			
        </ul>-->

        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-envelope"></i>
                    Parcel Scanned Report
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Scan Date</th>
                                <th>HAWB</th>
                                <th>Tracking Number</th>
                                <th>Account</th>
                                <th>Dims (L*W*H)</th>
                                <th>Scan By</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                </td>
                                <td>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="search_date_created" placeholder="Date Created" data-date-format="dd-mm-yyyy">
                                        <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_consignmentid" id="search_consignmentid">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_trackingnumber">
                                </td>
                                <td>
                                <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                            $includeParent = false;
                                        }
                                        $selectedAccount = (!empty($this->userAccountId) ? $this->userAccountId : '');
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('search_user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>

                                </td>
                                <td>
                                </td>
                                <td>
                                    <?php
                                        $this->selected_user = (!empty($_GET['id']))?$_GET['id']:'';
                                        echo Ddl::generateDDL('user_id', 'UserFilter', ' user_account_id = "'.$this->user->getUserAccountId().'" ', 'user_name', 'id', $this->selected_user, ' class="form-filter bs-select form-control" data-toggle="tooltip" data-placement="top" data-live-search="true" title="Scan by" data-original-title="Scan by"', 'Please Select', '', 'user_id', 'Scan by');
                                    ?>    
                                </td>
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
     * Override to show the menu
     *
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
