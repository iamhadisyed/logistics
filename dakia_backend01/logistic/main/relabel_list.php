<?php
// get settings
require_once("../includes/settings/config.inc.php");

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
            'relabel_shipment.php' => 'Relabel Shipment',
            'relabel_list.php' => "Relabel List"
        );
        if (isset($_GET['action']) && $_GET['action'] == "data_table_ajax") {
            $user = SessionManager::getUser();
            $conRelabelJson = [];
            $sessionUser = Sessionmanager::getUser();
            $consignmentRelabelFilter = new ConsignmentRelabelFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchFromDate = $this->form_vars['search_from_date'];
                if (!empty($searchFromDate) || $searchFromDate != '1970-01-01'){
                    $searchFromDate = date('Y-m-d', strtotime($searchFromDate));
                    if ($searchFromDate == '1970-01-01'){
                        $searchFromDate = date('Y-m-d');
                    }else{
                        $searchFromDate = $searchFromDate;
                    }
                }
                $consignmentRelabelFilter->addFromFilter('    DATE(date_created)', $searchFromDate);
                
                $searchToDate = $this->form_vars['search_to_date'];
                if (!empty($searchToDate) || $searchToDate != '1970-01-01'){
                    $searchToDate = date('Y-m-d', strtotime($searchToDate));
                    if ($searchToDate == '1970-01-01'){
                        $searchToDate = date('Y-m-d');
                    }else{
                        $searchToDate = $searchToDate;
                    }
                }
                $consignmentRelabelFilter->addToFilter('    DATE(date_created)', $searchToDate);
                $searchUserAccount = $this->form_vars['search_user_account'];
                if (!empty($searchUserAccount)){
                    $userIdArr = "";
                    $userFilter = new UserFilter();
                    $userFilter->addFieldFilter("    user_account_id", $searchUserAccount);
                    $userLisObj = $userFilter->getColumnList("id");
                   if(count($userLisObj) > 0){
                       foreach ($userLisObj as $userLis) {
                            $userIdArr .= "'".$userLis->getId()."',";
                       }
                   }
                    $userIdArr = rtrim($userIdArr,",");
                    $consignmentRelabelFilter->addInFilter('userid', $userIdArr);
                }
                $searchOldTracking = $this->form_vars['search_old_tracking'];
                if (!empty($searchOldTracking)){
                    $consignmentRelabelFilter->addFieldFilter('    old_tracking_no', $searchOldTracking);
                }
                $searchNewTracking = $this->form_vars['search_new_tracking'];
                if (!empty($searchNewTracking)){
                    $consignmentRelabelFilter->addFieldFilter('    new_tracking_no', $searchNewTracking);
                }
            }else{
                if ($this->user->getUserType() != User::USER_TYPE_CLIENT){
                    $consignmentRelabelFilter->addInFilter('    userid', $user->getId());
                }
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
                $consignmentRelabelFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $consignmentRelabelFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $consignmentRelabelFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $consignmentRelabelFilter->setOffset($iDisplayStart);
            $conRelabel = $consignmentRelabelFilter->getPagingList();
            $conRelabelDataArr = array();
            foreach ($conRelabel as $conRelabelArrs) {
                $conRelabelArr = [];
                $conRelabelArr['date_created'] = date("Y-m-d", $conRelabelArrs->getDateCreated());
                $conRelabelArr['old_tracking_no'] = $conRelabelArrs->getOldTrackingNo();
                $conRelabelArr['new_tracking_no'] = $conRelabelArrs->getNewTrackingNo();
                $user = new User($conRelabelArrs->getUserid());
                $conRelabelArr['account'] = ucfirst($user->getUserName());
                $conRelabelArr['option'] = "";
                $conRelabelDataArr [] = $conRelabelArr;
            }
            $conRelabelJson['data'] = $conRelabelDataArr;
            $conRelabelJson['draw'] = $sEcho;
            $conRelabelJson['recordsTotal'] = $iTotalRecords;
            $conRelabelJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($conRelabelJson);
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <!--        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
                <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />-->
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <!--DateRangePicker-->
        <link href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <!--DateRangePicker-->
        <?php
    }

    public function addPagelavelJs() {
        ?>

                                <!--<script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>-->
                                <!--<script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>-->
                                <!--<script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>-->
                                <!--<script src="../js/validator.min.js" type="text/javascript"></script>-->
        <!--DateRangePicker-->
        <script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <!--DateRangePicker-->
        <!--DataTable--> 
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <!--Hadi Code-->
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
                                "url": "relabel_list.php?action=data_table_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "date_created"},
                                {"data": "account", "bSortable": false},
                                {"data": "old_tracking_no"},
                                {"data": "new_tracking_no"}
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
                //Sreach fucntionality
                $("#btn_search").click(function(){
                    var from_date = '';
                    var to_date = '';
                    var user_account = '';
                    from_date = $("#from_date").val();
                    to_date = $("#to_date").val();
                    <?php if ($this->user->getUserType() != User::USER_TYPE_CLIENT){ ?>
                        user_account = $("#user_account_id").val();
                    <?php } ?>
                        if(from_date == ""){
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("Please select from date");
                            $("#show_general_msg").show(); 
                        }
                        else if(to_date == ""){
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("Please select to date");
                            $("#show_general_msg").show(); 
                        }else{
                            $('#search_from_date').val(from_date);
                            $('#search_to_date').val(to_date);
                            $('#search_user_account').val(user_account);
                            $(".filter-submit").click();
                        }
                });
                $("#btn_search").click();
            });
            // Reload data table command
            //                grid.getDataTable().ajax.reload();
        </script>
        <!--End Hadi Code-->
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
                    Relabel Filter
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label>Date Range</label>
                                <div class="input-group input-medium date-picker input-daterange" data-date="10/11/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" id="from_date" name="from" value="<?php echo date("d/m/Y"); ?>">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" id="to_date" name="to" value="<?php echo date("d/m/Y"); ?>"> 
                                </div>
                                <!-- /input-group -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Select Account</label>
                        <div class="form-group">
                            <div id="user_content">
                                <?php
                                    $accountParentId = 0;
                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                            $accountParentId = $this->user->getUserAccountId();

                                    $selectedAccount = $this->user->getUserAccountId();
                                    $allowedLevel = 0;
                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                        $allowedLevel = 1;
                                    }
                                     echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                ?>                                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <input type="button" class=" margin-top-20 btn btn-primary" id="btn_search" name="btn_search" value="Search" data-original-title=Search"" title="Search">
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Relabel List
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Options</th>
                                <th>Date</th>
                                <th>Account</th>
                                <th>Old Tracking</th>
                                <th>New Tracking</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                    <input type="hidden" class="form-filter"  id="search_from_date" name="search_from_date" value="">
                                    <input type="hidden" class="form-filter" id="search_to_date" name="search_to_date" value="">
                                    <input type="hidden" class="form-filter" id="search_user_account" name="search_user_account" value="">
                                </td>
                                <td>
<!--                                    <input type="text" class="form-control form-filter input-sm" name="search_date">-->
                                </td>
                                <td>
<!--                                    <input type="text" class="form-control form-filter input-sm" name="search_account">-->
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_old_tracking">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_new_tracking">
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div><!--portlet-body-->
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
