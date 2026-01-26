<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    public $user = null;
    private $user_filter;

    protected function init()
    {

        if (!Permissions::checkFilePermission('list_users.php'))
            util_redirect("index.php");
        $this->user = SessionManager::getUser();

        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        if ($this->user->getuserType() == User::USER_TYPE_CORPORATE) {
            $account = trim($_GET['account']);
            $allowedAccountArr = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
            if (isset($_GET['account']) && !empty($_GET['account']) && !in_array($account, $allowedAccountArr)) {
                util_redirect("index.php");
                exit;
            }
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("API KEYS")
        );
        /*
         * DataTable handlings
         */
        if (isset($_POST['action']) && $_POST['action'] == 'save_api_expiry_date') {
            if (empty($_POST['expiery_date']) && empty($_POST['access_token'])) {
                $arr = array('result' => 'error', 'message' => "Data missing");
                echo json_encode($arr);
                die;
            } else {
                $apiKeyUpdate = new UserFilter();
                $apiKeyUpdate->updateUserApiKey($_POST['access_token'], $_POST['expiery_date']);
                $arr = array('result' => 'success', 'message' => "Data Saved Successfully");
                echo json_encode($arr);
                die;
            }
        }
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
                if (isset($_GET['account']) && $_GET['account'] > 0) {
                    $this->user_filter->addFieldFilter('user_account_id', $_GET['account']);
                } else {
                    $this->user_filter->addFieldFilter('user_account_id', $this->user->getUserAccountId());
                }
            } else if (isset($_GET['account_id']) && $_GET['account_id'] > 0) {
                $this->user_filter->addFieldFilter('u.user_account_id', $_GET['account_id']);
            }
            if (isset($_GET['account_id']) && $_GET['account_id'] > 0) {
                $this->user_filter->addFieldFilter('u.user_account_id', $_GET['account_id']);
            }
            if (isset($_GET['user_id']) && $_GET['user_id'] > 0) {
                $this->user_filter->addFieldFilter('u.id', $_GET['user_id']);
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

                $accessToken = $this->form_vars['access_token'];
                if (!empty($accessToken))
                    $this->user_filter->addFieldLikeFilter(' at.access_token', $accessToken);


                $clientId = $this->form_vars['client_id'];
                if (!empty($clientId))
                    $this->user_filter->addFieldLikeFilter(' at.client_id', $clientId);


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
                    $this->user_filter->addFieldFilter(" DATE_FORMAT( u.added_date, '%Y-%m-%d') ", $searchdaaedDate);

            }

            // join on table keys for keys data
            $this->user_filter->addApiKeysJoin();
            /*
             * Pagination Logic Implemented
             *
             */
//            $customerObjs = $this->user_filter->addCountryJoin();
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
            $customerObjs = $this->user_filter->getPagingColumnList(" u.*, at.* ");
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

                $customerArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                if (Permissions::checkFilePermission('edit_user_permission')) {
                    $customerArr['actions'] .= '<li>
                                                <a title="Edit" data-expire_date="' . date("d.m.Y", strtotime($customerObj->getExpires())) . '" data-api_key="' . $customerObj->getAccessToken() . '" data-api_secret="' . $customerObj->getClientId() . '" class="edit-expire" href="javascript:void(0);">
                                                    <span class="glyphicon glyphicon-pencil"></span> Edit Expire Date
                                                </a>
                                            </li>';
                }
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
                $customerArr['api_key'] = $customerObj->getAccessToken();
                $customerArr['api_secret'] = $customerObj->getClientId();
                $customerArr['expires'] = date("d.m.Y", strtotime($customerObj->getExpires()));
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
//        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
//            $remoteareasId = $this->form_vars['remoteareas_id'];
//            if ($remoteareasId > 0) {
//                $remoteareas = new Remoteareas($remoteareasId);
//                $oldRemoteareasData = serialize($remoteareas);
//                $returnMsg['STATUS'] = "success";
//                $remoteareas->setUpdatedBy($user->getId());
//                $remoteareas->setUpdatedDate(time());
//                $remoteareas->setIsDeleted("Y");
//                $remoteareas->save();
//                /*
//                 * Add Remoteareas Log details
//                 */
//                $remoteareasLog = new RemoteareasLog();
//                $newRemoteareasData = serialize($remoteareas);
//                $remoteareasLog->createlog($user->getId(), '', $remoteareas->getId(), 'REMOTEAREAS', $user->getUserName() . ' has deleted ' . $remoteareasId, $oldRemoteareasData, $newRemoteareasData);
//                echo json_encode($returnMsg);
//            } else {
//                $returnMsg['STATUS'] = "error";
//                echo json_encode($returnMsg);
//            }
//
//            die;
//        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>

        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            var account = 0;
            var user_id = 0;
            <?php if (isset($_GET['account_id'])) { ?>
            account = <?php echo $_GET['account_id']; ?>;
            <?php } ?>
            <?php if (isset($_GET['user_id'])) { ?>
            user_id = <?php echo $_GET['user_id']; ?>;
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
                                "url": "api_keys.php?getCustomerAjax=customers_ajax&account_id=" + account + "&user_id=" + user_id, // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "expires"},
                                <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                {"data": "user_parent_account"},
                                <?php } ?>
                                {"data": "first_name"},
                                {"data": "last_name"},
                                {"data": "api_key"},
                                {"data": "api_secret"}
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
                        format: 'yyyy-mm-dd',
                        autoclose: true,
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
                $(document).on('click', '.save-expiery-date', function () {
                    var expiery_date = $(".update_expiry_date").val();
                    var access_token = $(this).data('access_token');
                    if (expiery_date == undefined || expiery_date == '') {
                        swal('Expiry date can not be empty.');
                        return false;
                    }
                    $.ajax({
                        type: "POST",
                        url: "api_keys.php",
                        data: {action: 'save_api_expiry_date', access_token: access_token, expiery_date: expiery_date},
                        success: function () {
                            $('#mawb_detail_modal').modal('hide');
                            $(".res_message1").text("<?php echo Translation::GetCaption("RECORD_UPDATED_SUCCESSFULLY") ?>");
                            $("#res_message").show();
                            $(".scroll-to-top").click();
                            grid.getDataTable().ajax.reload();
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });
                //functin to handle view details
                $(document).on('click', '.edit-expire', function () {
                    var api_key = "";
                    var api_secret = "";
                    api_key = $(this).data("api_key");
                    api_secret = $(this).data("api_secret");
                    var html = '<tr><td>1</td><td>' + api_key + '</td><td>' + api_secret + '</td><td><div class="input-group date date-picker-expiry margin-bottom-5" data-date-format="yyyy-mm-dd:">' +
                        '<input type="text" class="form-control form-filter input-sm update_expiry_date" name="update_expiry_date" placeholder="Date Created" data-date-format="yyyy-mm-dd">' +
                        '<span class="input-group-btn">' +
                        '<button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>' +
                        '</span>' +
                        '</div></td><td><a href="javascript:void(0);" class="btn blue save-expiery-date margin-bottom-5" data-access_token="' + api_key + '" title="">' +
                        'Save </a></td><tr>';
                    $('#manifest_list_data').html(html);
                    $('#mawb_detail_modal').modal('show');
                    $('.date-picker-expiry').trigger('click');
                    $('body').trigger('click');
                });
                $(document).on('click', '.date-picker-expiry', function () {
                    $(this).datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
                });
                <?php if (!empty($_GET['account'])) { ?>
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

    protected function renderBody()
    {
        $this->user = SessionManager::getUser();
        ?>
        <div class="modal fade" tabindex="-1" role="dialog" id="mawb_detail_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update Api Key Expiry Date</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th> #</th>
                                    <th> API Key</th>
                                    <th> API Secret</th>
                                    <th> Expiry</th>
                                    <th> Action</th>
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    <?php
                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE && isset($_GET['account']) && $_GET['account'] > 0) {
                        $userAccount = new CustomerAccount($_GET['account']);
                        echo $userAccount->getUserAccount();
                    }
                    if(!empty($_GET['account_id'])) {
                        $userAccount = new CustomerAccount($_GET['account_id']);
                        $userAccountName = $userAccount->getUserAccount();
                    }
                    ?>
                    User Api Keys <?php if(!empty($userAccountName)) echo $userAccountName; ?>
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row display-none" id="res_message">
                    <div class="col-md-12">
                        <div class="alert res_message1 alert-success"></div>
                    </div>
                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th width="6%">Actions</th>
                        <th>Expires</th>
                        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                            <th>Account</th>
                        <?php } ?>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Api key</th>
                        <th>Api Secret</th>

                        <!--<th>Type</th>-->
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i
                                            class="fa fa-search"></i></button>
                                <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i
                                            class="fa fa-times"></i></button>
                            </div>

                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd:">
                                <input type="text" class="form-control form-filter input-sm" readonly
                                       name="search_added_date" placeholder="Date Created"
                                       data-date-format="yyyy-mm-dd">
                                <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                            </div>
                        </td>
                        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                            <td>
                                <?php
                                //                                    $userAdminCheck = " id <> 0";
                                //                                    echo Ddl::generateDDL('search_ParentAccount', 'userAccountFilter', ' ' . $userAdminCheck . ' AND active_flag = 1 ', 'user_account', 'id', $_GET['account'], ' class="form-filter bs-select form-control" required="" data-show-subtext="true" data-toggle="tooltip"  title="Account" data-live-search="true" data-original-title="Account"', 'Select  Account', '', 'search_ParentAccount', 'Account', 'Logo', '../images/userlogo/thumbnail/', 'owe_16_');
                                $accountParentId = 0;
                                if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                    $accountParentId = $this->user->getUserAccountId();
                                $selectedAccount = "";
                                if (!empty($_GET['account']) && (int)trim($_GET['account']) > 0)
                                    $selectedAccount = (int)trim($_GET['account']);
                                $allowedLevel = 0;
                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                    $allowedLevel = 1;
                                }
                                //echo Ddl::showTreeDropdown('search_ParentAccount', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                ?>
                            </td>
                        <?php } ?>
                        <td><input type="text" id="search_FirstName" class="form-control form-filter input-sm"
                                   name="search_FirstName"></td>
                        <td><input type="text" id="search_LastName" class="form-control form-filter input-sm"
                                   name="search_LastName"></td>

                        <td><input type="text" id="access_token" class="form-control form-filter input-sm"
                                   name="access_token"></td>
                        <td><input type="text" id="client_id" class="form-control form-filter input-sm"
                                   name="client_id"></td>

                        <!--<td></td>-->
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="option_value" value="" id="option_value"/>
                <input type="hidden" name="action" value="download_csv_frm"/>
            </form>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead()
    {
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