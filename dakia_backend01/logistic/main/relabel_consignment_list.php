<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'services.class',
    'servicefilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'relabelconsignment.class',
    'relabelconsignmentfilter.class',
    
    
]);
class Page extends BasePage {
    /* * *
     * Controller logic
     */

    private $user = "";
    private $relabelConsignmentFilter = [];

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Relabel Consignment List"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "relabel_consignment_ajax") {
            $this->applyFilter($this->form_vars);

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = strtolower($this->form_vars['columns'][$dataTableColumnId]['data']);
                if($dataTableColumnName != "user_account_id" && $dataTableColumnName != "hawb") {
                    $this->relabelConsignmentFilter->orderBy("cr." . $dataTableColumnName, $orderFalse);
                }
            } else {
                $this->relabelConsignmentFilter->orderBy(strtolower("cr.id"), false);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $this->relabelConsignmentFilter->setRowsPerPage($iDisplayLength);
            $this->relabelConsignmentFilter->setOffset($iDisplayStart);
            $relabelConsignmentFilterObj = $this->relabelConsignmentFilter->getList("cr.*, ua.user_account, CONCAT(u.first_name,' ',u.last_name) AS user_name");
            $iTotalRecords = $this->relabelConsignmentFilter->getCount();
            $setDataArr = [];
            foreach ($relabelConsignmentFilterObj as $relabelConsignmentObj) {
                $oldConsignmentData = json_decode($relabelConsignmentObj->getOldConsignmentData());
                $oldLabel = SETTING_URL_LABEL.$oldConsignmentData->old_label;
                $currentArr = array();
                $consignment = new Consignment($relabelConsignmentObj->getConsignmentId());
                $newLabel = SETTING_URL_LABEL.$consignment->getLabelFile();
                $currentArr['hawb'] = $consignment->getHawb();
                if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                    $currentArr['user_account_id'] = $relabelConsignmentObj->getUserAccount();
                }
                $currentArr['old_tracking_no'] = '<a href="tracking.php?tracking_number=' . trim($relabelConsignmentObj->getOldTrackingNo()) . ' "target="_blank">'.$relabelConsignmentObj->getOldTrackingNo().'</a>';
                $currentArr['new_tracking_no'] = '<a href="tracking.php?tracking_number=' . trim($relabelConsignmentObj->getNewTrackingNo()) . ' "target="_blank">'.$relabelConsignmentObj->getNewTrackingNo().'</a>';
                $relabelUserObj = new User($relabelConsignmentObj->getUserid());
                $currentArr['userid'] = $relabelUserObj->getFirstName(). " " .$relabelUserObj->getLastName();
                $currentArr['date_created'] = date("d-m-Y g:i a", $relabelConsignmentObj->getDateCreated());
                $currentArr['actions'] = '<div class="btn-group" data-container="body">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                $currentArr['actions'] .= '<li>
                                                <a href="consignment_add.php?id=' . $relabelConsignmentObj->getConsignmentId() .'" title="View Consignment">
                                                    <i class="fa fa-eye"></i> Consignment View
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '<li>
                                                <a href="javascript:;" title="View Parcel Tracking" class="parcelTracingBtn" data-id="'.$relabelConsignmentObj->getId().'">
                                                    <i class="fa fa-eye"></i> Parcel Tracking View
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '<li>
                                                <a href="'.$oldLabel.'" title="View Old Label" target="_blank" >
                                                    <i class="fa fa-eye"></i> Old Label
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '<li>
                                                <a href="'.$newLabel.'" title="View New Label" target="_blank" >
                                                    <i class="fa fa-eye"></i> New Label
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
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_relabel_consignment_parcel_details") {
            $consignmentRelabelId = $this->form_vars['consignment_relabel_id'];
            $relabelConsignment = new RelabelConsignment($consignmentRelabelId);
            $oldNewTrackingParcelMapping = json_decode($relabelConsignment->getOldNewTrackingMapping());
            $output = [];
            $html = "";
            if(count($oldNewTrackingParcelMapping) > 0) {
                foreach($oldNewTrackingParcelMapping as $oldTracking => $newTracking) {
                    $html .= "<tr>";
                    $html .= "<td><a href='tracking.php?tracking_number=" . trim($oldTracking) . "' target='_blank' >" .$oldTracking."</a></td>";
                    $html .= "<td><a href='tracking.php?tracking_number=" . trim($newTracking) . "' target='_blank' >" .$newTracking."</a></td>";
                    $html .= "</tr>";
                }
                $output['html'] = $html;
            } else {
                $html .= "<tr>";
                $html .= "<td colspan='2'>No parcel tracking found</td>";
                $html .= "</tr>";
                $output['html'] = $html;
            }
            echo json_encode($output);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_account_user_options") {
            $userAccountId = $this->form_vars['user_account_id'];
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("     u.user_account_id", $userAccountId);
            $userFilterObjs = $userFilter->getList();
            $output = [];
            $html = "<option value=''>Select User</option>";
            if(count($userFilterObjs) > 0) {
                foreach($userFilterObjs as $userFilterObj) {
                    $html .= "<option value='".$userFilterObj->getId()."'> ".$userFilterObj->getFirstName()." ".$userFilterObj->getLastName()." </option>";
                }
            } 
            $output['html'] = $html;
            echo json_encode($output);
            die;
        }
        
    }
    
    protected function applyFilter($form_vars) {
        $this->relabelConsignmentFilter = new RelabelConsignmentFilter();
        $this->relabelConsignmentFilter->join("consignment c", ['c.id' => 'cr.consignment_id']);
        $this->relabelConsignmentFilter->join("user u", ['u.id' => 'cr.userid']);
        $this->relabelConsignmentFilter->join("customer_account ua", ['ua.id' => 'u.user_account_id']);
        
        $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
        /*
         * Column filter
         * For search
         */
        if (isset($form_vars['action']) && $form_vars['action'] == 'filter') {
            $filterArray = [];

            $hawb = $form_vars['hawb'];
            if (!empty($hawb))
                $filterArray['c.hawb'] = $hawb;
            
            $oldTrackingNo = $form_vars['old_tracking_no'];
            if (!empty($oldTrackingNo))
                $filterArray['cr.old_tracking_no'] = $oldTrackingNo;
            
            $newTrackingNo = $form_vars['new_tracking_no'];
            if (!empty($newTrackingNo))
                $filterArray['cr.new_tracking_no'] = $newTrackingNo;
            
            $userid = $form_vars['userid'];
            if (!empty($userid))
                $filterArray['cr.userid'] = $userid;

            $this->relabelConsignmentFilter->where($filterArray);
            
            if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                $userAccountId = $form_vars['user_account_id'];
                if (!empty($userAccountId)) {
                    $userAccountArry = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                    $this->relabelConsignmentFilter->whereIn('u.user_account_id', $userAccountArry);
                }
            } else {
                $this->relabelConsignmentFilter->whereIn('u.user_account_id', $userAccountArry);
            }
            
            $date_created_from = $form_vars['date_created'];
            $date_created_to = $form_vars['date_created'];
            if (!empty($date_created_from) && !empty($date_created_to))
                $this->relabelConsignmentFilter->whereBetween ('cr.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));

        } else {
            $this->relabelConsignmentFilter->whereIn('u.user_account_id', $userAccountArry);
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
                    var datatableurl = "relabel_consignment_list.php?action=relabel_consignment_ajax";
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
                                {"data": "hawb", "bSortable": false},
                                <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                                {"data": "user_account_id", "bSortable": false},
                                <?php } ?>        
                                {"data": "old_tracking_no"},
                                {"data": "new_tracking_no"},
                                {"data": "userid"},
                                {"data": "date_created"}
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
            $(document).on('click', '.parcelTracingBtn', function () {
                var form_data = new FormData();
                var consignment_relabel_id = $(this).data('id');
                form_data.append('consignment_relabel_id', consignment_relabel_id);
                form_data.append('action', 'get_relabel_consignment_parcel_details');
                $.ajax({
                        url: "relabel_consignment_list.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            $('#parcelRelabelTrackingBody').html(data.html);
                            $('#parcelRelabelTrackingModal').modal('show');
                        },
                        error: function () {
                                //alert('error handing here');
                        }
                });
            });
            $(document).on('change', '#user_account_id', function () {
                var form_data = new FormData();
                var user_account_id = $(this).val();
                form_data.append('user_account_id', user_account_id);
                form_data.append('action', 'get_account_user_options');
                $.ajax({
                        url: "relabel_consignment_list.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            $('#userid').html(data.html);
                            $('#userid').select2();
                        },
                        error: function () {
                                //alert('error handing here');
                        }
                });
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
                    Relabel Consignment List
                </div>
                <div class="actions">
                    <a href="relabel_shipment.php" class="btn blue"  > Relabel Consignment </a>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>HAWB</th>
                            <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                            <th>Account</th>
                            <?php } ?>
                            <th>Old Tracking Number</th>
                            <th>New Tracking Number</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="hawb" value=""  />
                            </td>
                            <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                            <td>
                                <?php
                                    $accountParentId = 0;
                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                        $accountParentId = $this->user->getUserAccountId();
                                    }
                                    $selectedAccount = "";
                                    $allowedLevel = 0;
                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                        $allowedLevel = 1;
                                    }
                                    echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                ?>
                            </td>
                            <?php } ?>
                            <td>
                                <input type="text" class="form-control form-filter" name="old_tracking_no" value=""  />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="new_tracking_no" value=""  />
                            </td>
                            <td>
                                <?php
                                    if($this->user->getUserType() == User::USER_TYPE_ADMIN) { 
                                        $sql =  "SELECT
                                                    *
                                                  FROM
                                                    user";
                                    } else {
                                        $sql =  "SELECT
                                                    *
                                                  FROM
                                                    user
                                                  WHERE user_account_id = ".$this->user->getUserAccountId();
                                    }
                                    $selectFieldName = ['first_name','last_name'];
                                    echo Ddl::generateDDLFromSql($sql, "userid", $selectFieldName, "id", "", "class='select2 form-filter'", "Select", "", "userid", "");
                                ?>
                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control input-sm form-filter" readonly name="date_created" id="date_created" placeholder="" >
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
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
        <!-- Modal -->
        <div id="parcelRelabelTrackingModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Parcel Tracking</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Old Tracking</th>
                                            <th>New Tracking</th>
                                        </tr>
                                    </thead>
                                    <tbody id="parcelRelabelTrackingBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
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