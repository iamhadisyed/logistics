<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'country.class',
    'countryfilter.class',
    
]);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $user = null;
    private $user_filter;
    private $account_id;
    private $account_id_encoded;

    protected function init() {
        
//        if (!Permissions::checkFilePermission('list_users.php'))
//            util_redirect("index.php");
        $this->user = SessionManager::getUser();

        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        
        $this->account_id = base64_decode(util_get("account"));
        $this->account_id_encoded = util_get("account");
        
        if ($this->user->getuserType() == User::USER_TYPE_CORPORATE) {
            $allowedAccountArr = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
            if(isset($this->account_id) && !empty($this->account_id) && !in_array($this->account_id, $allowedAccountArr) ){
                util_redirect("index.php");
                exit;
            }
        }
        
        if (isset($this->account_id) && $this->account_id > 0) {
            $this->account = $this->account_id;
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("Dashboard"),
            Translation::GetCaption("Members")
        );
        /*
         * DataTable handlings
         */
        //Handle Customer Ajax
        if (isset($_GET['getCustomerAjax']) && $_GET['getCustomerAjax'] == 'customers_ajax') {
            /*
             * Set columns orders for sorting
             */
            $this->user = SessionManager::getUser();
            $this->user_filter = new UserFilter();
            $this->user_filter->addUserAccountJoin();
//            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE || $this->user->getuserType() == User::USER_TYPE_CORPORATE) {
            if ($this->user->getuserType() == User::USER_TYPE_CORPORATE) {
                if (isset($this->account_id) && $this->account_id > 0) {
                    $this->user_filter->addFieldFilter('user_account_id', $this->account_id);
                } else {
                    $this->user_filter->addFieldFilter('user_account_id', $this->user->getUserAccountId());
                }
            } else if (isset($this->account_id) && $this->account_id > 0) {
                $this->user_filter->addFieldFilter('u.user_account_id', $this->account_id);
            }
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                if ($dataTableColumnName == "country")
                    $dataTableColumnName = "c.name";

                if ($dataTableColumnName == "user_parent_account")
                    $dataTableColumnName = "parentid";
                $this->user_filter->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchUserCode = $this->form_vars['search_UserCode'];
                if (!empty($searchUserCode))
                    $this->user_filter->addFieldLikeFilter('user_code', $searchUserCode);

                $searchAccount = $this->form_vars['search_Account'];
                if (!empty($searchAccount))
                    $this->user_filter->addFieldLikeFilter('user_account', $searchAccount);

                $searchUserName = $this->form_vars['search_UserName'];
                if (!empty($searchUserName))
                    $this->user_filter->addFieldLikeFilter('user_name', $searchUserName);

                $searchFirstName = $this->form_vars['search_FirstName'];
                if (!empty($searchFirstName))
                    $this->user_filter->addFieldLikeFilter('first_name', $searchFirstName);

                $searchLastName = $this->form_vars['search_LastName'];
                if (!empty($searchLastName))
                    $this->user_filter->addFieldLikeFilter('last_name', $searchLastName);

                $searchParentAccount = $this->form_vars['search_ParentAccount'];

                if (!empty($searchParentAccount)) {
                    $this->user_filter->addFieldFilter('ua.id', $searchParentAccount);
                }


                $searchEmail = $this->form_vars['search_Email'];
                if (!empty($searchEmail))
                    $this->user_filter->addFieldLikeFilter('email', $searchEmail);

                $searchActive = $this->form_vars['search_Active'];
                if (trim($searchActive) != '') {
                    $this->user_filter->addFieldFilter('u.active_flag', $searchActive);
                }
                $searchUserType = $this->form_vars['search_UserType'];
                if (trim($searchUserType) != '') {
                    $this->user_filter->addFieldFilter('user_type', $searchUserType);
                }
                $searchCountry = $this->form_vars['search_Country'];
                if (!empty($searchCountry))
                    $this->user_filter->addFieldFilter('u.country_id', $searchCountry);

                $searchVATRate = $this->form_vars['search_VATRate'];
                if (!empty($searchVATRate))
                    $this->user_filter->addFieldLikeFilter('vat_value', $searchVATRate);

                $searchSurcharge = $this->form_vars['search_Surcharge'];
                if (!empty($searchSurcharge))
                    $this->user_filter->addFieldLikeFilter('fuel_charges', $searchSurcharge);
                $searchdaaedDate = $this->form_vars['search_added_date'];
                if (!empty($searchdaaedDate))
                    $this->user_filter->addFieldFilter(" DATE_FORMAT( u.added_date, '%Y-%m-%d') ", date('Y-m-d', strtotime($searchdaaedDate)));

            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $customerObjs = $this->user_filter->addCountryJoin();
            $iTotalRecords = $this->user_filter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->user_filter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->user_filter->setOffset($iDisplayStart);
            $customerObjs = $this->user_filter->getPagingList();
            $customerDataArr = array();
            foreach ($customerObjs as $customerObj) {
                $customerArr = array();
                $customerLogo = "";
                $customerProfileImage = "";
                // has this customer got an account
                $accountName = "";
                $accountId = "";
                if ($customerObj->getId() > 0) {
                    $account = $customerObj->getUserAccount();
                    $accountName = $customerObj->getCompany();
                    $accountId = $customerObj->getId();
                }
                
                $customerArr['actions'] = '<div class="dropdown">
                                                <a href="#" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </a>
                                             <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">';
                if(Permissions::checkFilePermission('edit_user_permission')) {
                     if($this->account > 0 )
                           $accountFilter = "&account=". $this->account_id_encoded;
                     
                    $customerArr['actions'] .= '<li>
                                                <a class="dropdown-item" title="Edit" href="manage_user.php?id=' . base64_encode($customerObj->getId()) .$accountFilter. '">
                                                    Edit
                                                </a>
                                            </li>';
                }
                if (Permissions::checkFilePermission('api_key_expiry_date_update')) {
                $customerArr['actions'] .= '<li>
                                                    <a class="dropdown-item" href="api_keys.php?user_id=' . $customerObj->getId() . '" title="Hierarchical View">
                                                        API Keys
                                                    </a>
                                                </li>';
                }

                $customerArr['actions'] .= '<li>
                                 <a href="" class="dropdown-item" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $customerObj->getId() . '" 
                                 data-log_name="user" data-toggle="modal"> View Audit
                                 </a>
                                 </li>';
                
                $customerArr['actions'] .= '</ul>
                                        </div>';

                if ($customerObj->getProfileImage() != '' && file_exists("../_assets/profile_images/owe_16_" . $customerObj->getProfileImage())) {
                    $customerProfileImage = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../_assets/profile_images/owe_16_' . $customerObj->getProfileImage() . '" alt="' . $customerObj->getProfileImage() . '" uib-popover="' . $customerObj->getProfileImage() . '" popover-trigger="mouseenter" >
                                        </div>';
                } else {
                    $customerProfileImage = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img class="customer_img" src="../images/No-image-found.jpg" alt="' . $customerObj->getProfileImage() . '" uib-popover="' . $customerObj->getProfileImage() . '" popover-trigger="mouseenter" data-center="' . $customerObj->getProfileImage() . '">
                                        </div>';
                }
                $customerArr['user_name'] = $customerProfileImage . '<div style="float: left;margin-right: 5px;">' . $customerObj->getUserName() . '</div>';
                $customerArr['user_type'] = User::USER_ROLES[$customerObj->getUserType()];
                $customerArr['first_name'] = $customerObj->getFirstname();
                $customerArr['last_name'] = $customerObj->getLastname();
                if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                    $userClassParentr = new CustomerAccount($customerObj->getUserAccountId());
                    if ($userClassParentr->getLogo() != '' && file_exists("../images/userlogo/thumbnail/owe_16_" . $userClassParentr->getLogo())) {
                        $customerParentLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/userlogo/thumbnail/owe_16_' . $userClassParentr->getLogo() . '" alt="' . $userClassParentr->getUserAccount() . '" uib-popover="' . $userClassParentr->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                    } else {
                        $customerParentLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/No-image-found.jpg" alt="' . $userClassParentr->getUserAccount() . '" uib-popover="' . $customerObj->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                    }
                    $customerArr['user_parent_account'] = $customerParentLogo . '<div style="float: left;margin-right: 5px;">' . $userClassParentr->getUserAccount() . '</div>';
                }
                $customerArr['active_flag'] = '<div class="text-center">' . ($customerObj->getActive() ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
                $country = $customerObj->getCountryId();
                if ($country != '') {
                    $countryFilter = new CountryFilter();
                    $countryFilter->addFilter("id = '" . $country . "'");
                    $countryList = $countryFilter->getList();
                    if (count($countryList) > 0) {
                        $countryName = '<img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryList[0]->getName();
                    } else {
                        $countryName = '';
                    }
                    $customerArr['country'] = $countryName;
                } else {
                    $customerArr['country'] = "";
                }
                $customerArr['userServiceType'] = "";
                $customerArr['added_date'] = formatDate(date("d-m-Y" , $customerObj->getAddedDate()));
                //if ($user_type == 1) {
                //    $customerArr['userServiceType'] = '<span class="label label-sm label-danger">Product</span>';
                //} else {
                //    $customerArr['userServiceType'] = '<span class="label label-sm label-success">Standard</span>';
                //}
                $customerDataArr [] = $customerArr;
            }
            $customerDataArrJson['data'] = $customerDataArr;
            $customerDataArrJson['draw'] = $sEcho;
            $customerDataArrJson['recordsTotal'] = $iTotalRecords;
            $customerDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($customerDataArrJson);
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
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet"  type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="<?=SETTING_MAIN_ASSETS;?>css/common.css" rel="stylesheet" type="text/css" />

        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
<!--        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>-->
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script type="text/javascript">
            var account = '';
        <?php if (trim($this->account_id_encoded)!='') { ?>
                account = '<?php echo $this->account_id_encoded; ?>';
        <?php } ?>
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
                            "url": "list_users.php?getCustomerAjax=customers_ajax&account=" + account, // ajax source
                                    headers: {

                                    },
                            },
                            "bStateSave": true,
                            "columns": [
                            {"data": "actions", "bSortable": false},
                                {"data": "added_date"},
        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                {"data": "user_parent_account"},
        <?php } ?>
                            {"data": "user_name"},
                            {"data": "user_type"},
                            {"data": "first_name"},
                            {"data": "last_name"},
                            {"data": "active_flag"},
                            {"data": "country"}
                            //,{"data": "userServiceType", "bSortable": false}
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
        <?php if ($this->account_id > 0) { ?>
                $(".filter-submit").click();
        <?php } ?>
            });
        //            $(document).on('click', '.btndelete', function () {
        //            var remoteareasId = $(this).attr("data-remoteareas_id");
        //            swal({
        //            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
        //                    text: "",
        //                    type: "warning",
        //                    showCancelButton: true,
        //                    confirmButtonClass: "btn-danger",
        //                    confirmButtonText: "Yes",
        //                    cancelButtonText: "No",
        //                    closeOnConfirm: true,
        //                    closeOnCancel: true
        //            },
        //                    function(isConfirm) {
        //                    if (isConfirm) {
        //                    $.ajax({
        //                    type: "POST",
        //                            url: "manage_remoteareas.php",
        //                            data: {action: "delete", remoteareas_id: remoteareasId},
        //                            dataType: "json",
        //                            success: function (data) {
        //                            if (data.STATUS == "success") {
        //                            $("#res_message").html("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
        //                            $("#res_message").show();
        //                            $(".scroll-to-top").click();
        //                            grid.getDataTable().ajax.reload();
        //                            } else {
        //                            }
        //                            },
        //                            error: function () {
        //                            alert('error handing here');
        //                            }
        //                    });
        //                    }
        //                    });
        //            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $this->user = SessionManager::getUser();
        ?>
        <div class="row" style="margin-bottom: 10px">
            <div class="col-xl-12">
                <div class="accordion" id="default-accordion-example">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Filter Here
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#default-accordion-example">
                            <div class="accordion-body">
                                <div class="row gy-4">
                                    <div class="col-xxl-4 col-md-4">
                                        <div>
                                            <label for="user_name" class="form-label">User Name</label>
                                            <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="user_name"
                                                    id="user_name" value="<?php echo @$user_name; ?>" required="required">
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-md-4">
                                        <div>
                                            <label for="user_name" class="form-label">First Name</label>
                                            <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="user_name"
                                                    id="user_name" value="<?php echo @$user_name; ?>" required="required">
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-md-4">
                                        <div>
                                            <label for="user_name" class="form-label">Last Name</label>
                                            <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="user_name"
                                                    id="user_name" value="<?php echo @$user_name; ?>" required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a id="btnSave"   href="javascript:;" class="pull-right btn btn-success btn_save"><span></span>Filter</a>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Member's List</h4>

                        <div class="flex-shrink-0">
                            <a href="manage_user.php" class="btn btn-primary">Add New Member</a>
                            <!--                            <div class="form-check form-switch form-switch-right form-switch-md">-->
                            <!--                                <label for="card-tables-showcode" class="form-label text-muted">Show Code</label>-->
                            <!--                                <input class="form-check-input code-switcher" type="checkbox" id="card-tables-showcode">-->
                            <!--                            </div>-->
                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
<!--                        <p class="text-muted mb-4"><code>Show Error message Here</code> </p>-->

                        <div class="live-preview">
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap table-striped-columns mb-0" id="manage-data-table">
                                    <thead class="table-light">
                                    <tr class="heading">
                                        <th scope="col" style="width: 46px;">
                                            Action
                                        </th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Account</th>
                                        <th scope="col">Member Name</th>
                                        <th scope="col">Member Type</th>
                                        <th scope="col">First Name</th>
                                        <th scope="col">Last Name</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Country</th>
                                    </tr>
                                    </thead>
                                    <tbody>
<!--                                    <tr>-->
<!--                                        <td>-->
<!--                                            <div class="dropdown">-->
<!--                                                <a href="#" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                                                    <i class="ri-more-2-fill"></i>-->
<!--                                                </a>-->
<!---->
<!--                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">-->
<!--                                                    <li><a class="dropdown-item" href="#">View</a></li>-->
<!--                                                    <li><a class="dropdown-item" href="#">Edit</a></li>-->
<!--                                                    <li><a class="dropdown-item" href="#">Delete</a></li>-->
<!--                                                </ul>-->
<!--                                            </div>-->
<!--                                        </td>-->
<!--                                        <td>07 Oct, 2021</td>-->
<!--                                        <td><a href="#" class="fw-medium">#VL2110</a></td>-->
<!--                                        <td>Supper Admin</td>-->
<!--                                        <td>William Elmore</td>-->
<!--                                        <td>William</td>-->
<!--                                        <td>Elmore</td>-->
<!--                                        <td><span class="badge bg-success">Paid</span></td>-->
<!--                                        <td> Pakistan </td>-->
<!--                                    </tr>-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="option_value" value="" id="option_value"/>
                <input type="hidden" name="action" value="download_csv_frm" />
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

    public function renderHead() {
        ?>
        <style type="text/css">
            .table-responsive {
                overflow-x: visible !important;
                overflow-y: visible !important;
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