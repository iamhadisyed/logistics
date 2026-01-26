<?php
// get settings
require_once("../includes/settings/config.inc.php");


include_classes([
    'userdepartment.class',
    'userdepartmentfilter.class',
    'userhasgroups.class',
    'userhasgroupsfilter.class',
    'country.class',
    'countryfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'department.class',
    'departmentfilter.class',
    'groups.class',
    'groupsfilter.class',
    'usershoppingplatforms.class',
    'usershoppingplatformsfilter.class',
    'userlog.class',
    'userlogfilter.class',
    
]);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $user = null;
    private $user_filter;
    private $counting_error;
    private $user_group;
    private $selected_dep;
    private $user_id;
    private $user_id_encoded;
    private $account_id;
    private $account_id_encoded;

    protected function init() {
        $this->user_id = base64_decode(util_get("id"));
        $this->user_id_encoded = util_get("id");
        $this->account_id = base64_decode(util_get("account"));
        $this->account_id_encoded = util_get("account");

        
        $this->user = SessionManager::getUser();
        $accountFilter="";
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("Dashboard"),
            'list_users.php' => Translation::GetCaption("MEMBERS"),
            "Manage Member"
        );
        $sessionUser = SessionManager::getUser();
        if (isset($this->account_id) && $this->account_id > 0) {
            $this->account = $this->account_id;
        }
        if (isset($this->form_vars["form_action"])) {
            switch ($this->form_vars["form_action"]) {
                // SAVE
                case "save":
                    $this->form_vars["id"] = intval($this->form_vars["id"]);
                    $userLogSystem = new User($this->form_vars["id"]);
                    $sessionUser = SessionManager::getUser();
                    $error_array = "";
                    $profile_image = '';
                    $this->form_vars['permission_group'][0] = 25;
                    $this->form_vars['warehouse_id'][0] = 10;
                    if (isset($_FILES["profile_image"]) && trim($_FILES["profile_image"]["name"]) != '') {
                        $newPath = "../_assets/profile_images/";
                        if (!file_exists($newPath))
                            @mkdir($newPath, 0775);
                        $allowedExts = array("gif", "jpeg", "jpg", "png");
                        $temp = explode(".", $_FILES["profile_image"]["name"]);
                        $extension = end($temp);
                        if ((($_FILES["profile_image"]["type"] == "image/gif") || ($_FILES["profile_image"]["type"] == "image/jpeg") || ($_FILES["profile_image"]["type"] == "image/jpg") || ($_FILES["profile_image"]["type"] == "image/pjpeg") || ($_FILES["profile_image"]["type"] == "image/x-png") || ($_FILES["profile_image"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                            if ($_FILES["profile_image"]["error"] > 0) {
                                $error_array[] = "Return Code: " . $_FILES["profile_image"]["error"] . "<br>";
                            } else {
                                $profile_image = str_replace(' ', '_', time() . $_FILES["profile_image"]["name"]);
                                move_uploaded_file($_FILES["profile_image"]["tmp_name"], "../_assets/profile_images/" . $profile_image);
                                $logMessage[] = 'Changed Profile';

                                $thumb = new easyphpthumbnail;
                                $thumb->Thumblocation = '../_assets/profile_images/';
                                $thumb->Thumbprefix = 'owe_';
                                $thumb->Thumbsaveas = $extension;
                                $thumb->Thumbfilename = $profile_image;
                                $thumb->Clipcorner = array(2, 15, 0, 0, 1, 1, 0);

                                $thumb->Thumbsize = 16;
                                $thumb->Thumbprefix = 'owe_16_';
                                $thumb->Createthumb("../_assets/profile_images/" . $profile_image, 'file');
                            }
                        } else {
                            $error_array[] = formatMessages(ERROR_INVALID_FILE);
                        }
                    }
                    $old_profile_image = $sessionUser->getProfileImage();
                    if (trim(@$profile_image) != '') {
                        @unlink('../_assets/profile_images/' . $old_profile_image);
                    }

                    $user = new User(intval($this->form_vars["id"]));
                    $userAccountId = $sessionUser->getUserAccountId();
                    if (isset($this->form_vars["user_account_id"]) && $this->form_vars["user_account_id"] > 0)
                        $userAccountId = $this->form_vars["user_account_id"];
                    $user->setUserAccountId($userAccountId);
                    $user->setUserType($this->form_vars["user_type"]);
                    $user->setUserName(trim($this->form_vars["user_name"]));
                    $user->setEmail($this->form_vars["email"]);
                    $user->setFirstName($this->form_vars["first_name"]);
                    $user->setLastName($this->form_vars["last_name"]);
                    $user->setAddress($this->form_vars["address"]);
                    $user->setAddress2($this->form_vars["address_2"]);
                    $user->setAddress3($this->form_vars["address_3"]);
                    $user->setPhone($this->form_vars["phone"]);
                    $user->setCountryId($this->form_vars["country_name"]);
                    $user->setWarehouseId($this->form_vars["warehouse_id"]);
                    $user->setDashboard($this->form_vars["dashboard"]);
                    $user->setPostcode($this->form_vars["postcode"]);
                    $user->setState($this->form_vars["state"]);
                    $user->setCity($this->form_vars["city"]);
                    if(!empty($this->form_vars["commission_break_event_amount"])){
                        $commisionBreakEventAmount = $this->form_vars["commission_break_event_amount"];
                        if($this->form_vars["user_type"] != "sales_agent"){
                            $commisionBreakEventAmount = 0.00;
                        }
                        $user->setCommissionBreakEventAmount($commisionBreakEventAmount);
                    }
                    $receiveEmail = 'n';
                    if (isset($this->form_vars["receive_email"])){
                        $receiveEmail = 'y';
                        $this->form_vars["receive_email"] = 'y';
                    }
                    $user->setReceiveEmail($receiveEmail);
                    $user->setAddedBy($sessionUser->getId());
                    if($user->getAddedDate() <= '0' || trim($user->getAddedDate()) == '' || trim($user->getAddedDate()) == '1970-01-01 00:00:00')
                        $user->setAddedDate(time());
            		$user->setIsTcAgreed('Y');
                    if (!empty($profile_image))
                        $user->setProfileImage($profile_image);
//                    $user->setApiDate(time());
//                    if ($this->form_vars["key_text"] == '' && $this->form_vars["secert_text"] == '') {
//                        $api_key = md5($_SESSION['admin']['id'] . $_SESSION['admin']['firstname'] . $_SESSION['admin']['surname'] . date('H:i:s'));
//                        $api_secert = md5($_SESSION['user_name'] . $_SESSION['user_type'] . $_SESSION['user_account'] . date('H:i:s'));
//                        $user->setApiKey($api_key);
//                        $user->setApiSecert($api_secert);
//                    }
                    $activeFlag = 0;
                    if (isset($this->form_vars["active_flag"])){
                        $activeFlag = 1;
                        $this->form_vars["active_flag"] = 1;
                    }
                    $user->setActiveFlag($activeFlag);
                    
                    $is_employee = 0;
                    if (isset($this->form_vars["is_employee"])){
                        $is_employee = 1;
                        $this->form_vars["is_employee"] = 1;
                    }
                    $user->setIsEmployee($is_employee);
                    $this->selected_dep = $this->form_vars['deparment'];
                    if (!empty($this->form_vars["password"]))
                        $user->setPassword(password_hash($this->form_vars["password"], PASSWORD_DEFAULT));
                    if (!preg_match("/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/", $this->form_vars['password']) && !empty($this->form_vars['password'])) {
                        $error_array .= formatMessages(ERROR_PASSWORD_VERIFY) . "<br>";
                    }
                    if (empty($this->form_vars['country_name'])) {
                        $error_array .= formatMessages(ERROR_COUNTRY_EMPTY) . "<br>";
                    }
                    if(count($this->form_vars['permission_group']) == 0){
                        $error_array .= 'Please select accessibility.';
                    }
                    if ($error_array == "") {
                        $isValid = $user->isValid($error_array);
                        if ($isValid == "") {
                            if (!empty($this->form_vars["password"]) && $this->form_vars["id"] > 0) {
                                $to = $this->form_vars["email"];
                                //define the subject of the email
                                $subject = "Successfully Change of Password" . date('d-m-Y');
                                //define the headers we want passed. Note that they are separated with \r\n
                                $headers = "From: itsupport@logistic.com\r\nReply-To: itsupport@logistic.com";
                                $headers .= "Reply-To: itsupport@logistic.com \r\n";
                                $headers .= "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                $message = 'Hello ' . $this->form_vars["contact"] . ',';             //add boundary string and mime type specification
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Just to Inform you that your password has been successfully changed. Below is the detail of your new password. ';
                                $message .= "\r\n";
                                $message .= 'Your new password is ' . $this->form_vars["password"];
                                $message .= "\r\n";
                                $message .= 'If you have not made this change please email to itsupport@logistic.com to keep your account safe.';
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Thanks';
                                $message .= "\r\n";
                                $message .= "Logistic Support Team";
                                //send the email
                                $mail_sent = @mail($to, $subject, $message, $headers);
                            } else if ($this->form_vars["id"] <= 0) {

                                $to = $this->form_vars["email"];
                                //define the subject of the email
                                $subject = "Successfully Created User" . date('d-m-Y');
                                //define the headers we want passed. Note that they are separated with \r\n
                                $headers = "From: itsupport@logistic.com\r\nReply-To: itsupport@logistic.com";
                                $headers .= "Reply-To: itsupport@logistic.com \r\n";
                                $headers .= "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                $message = 'Hello ' . $this->form_vars["contact"] . ',';             //add boundary string and mime type specification
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Just to Inform you that your user has been created successfully. Below is your login details. ';
                                $message .= "\r\n";
                                $message .= 'Username:  ' . $this->form_vars["user_name"];
                                $message .= "\r\n";
                                $message .= 'Password:  ' . $this->form_vars["password"];
                                $message .= "\r\n";
                                $message .= 'If you have not created this account please email to itsupport@logistic.com.';
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Thanks';
                                $message .= "\r\n";
                                $message .= "Logistic Support Team";
                                $mail_sent = @mail($to, $subject, $message, $headers);
                            }
                            $user->saveLog = false;
                            $user->save();
                            //Do things after save data into user table
                            //Save user departments
                            $UserDepartment = new UserDepartment();
                            $UserDepartment->deleteByUser($user->getId());
                            if ($is_employee == 1) {
                                foreach ($this->form_vars['deparment'] as $valueDeparment) {
                                    $UserDepartmentSave = new UserDepartment();
                                    $UserDepartmentSave->setUserId($user->getId());
                                    $UserDepartmentSave->setDepartmentId($valueDeparment);
                                    $UserDepartmentSave->save();
                                }
                            }

                            $oldUserGroups = [];
                            $newUserGroups = [];
                            $userHasGroups = new UserHasGroups();
                            $userOldGroups = new UserHasGroupsFilter();
                            $userOldGroups->addFilter('     admin_id = ' . $user->getId());
                            $userOldGroups->addJoin('groups', 'groups.group_id', 'uhg.group_id', 'INNER JOIN');
                            $userOldGroups = $userOldGroups->getList();
                            foreach ($userOldGroups as $userOldGroup) {
                                $oldUserGroups[] = $userOldGroup->getGroupName();
                            }
                            $userHasGroups->deleteByAdminId($user->getId());
                            foreach ($this->form_vars['permission_group'] as $valueGroup) {
                                $userNewGroup = new Groups($valueGroup);
                                $newUserGroups[] = $userNewGroup->getGroupName();
                                $userHasGroups = new UserHasGroups();
                                $userHasGroups->setAdminId($user->getId());
                                $userHasGroups->setGroupId($valueGroup);
                                $userHasGroups->save();
                            }
                            $oldDataG['groups'] = $oldUserGroups;
                            $newDataG['groups'] = $newUserGroups;
                            $user->logMoreDataNew = $newDataG;
                            $user->logMoreDataOld = $oldDataG;
//                            $old_data = json_encode($oldDataG);
//                            $new_data = json_encode($newDataG);
//                            $userAudit = new UserAudit();
//                            $table_key = 0;
//                            $userAudit->allowAdd = true;
//                            $userAudit->insertAuditData('userhasgroups', 'update', $this->user->getFirstName() . ' ' . $this->user->getLastName(), $this->user->getId(), $new_data, $table_key, $old_data, $this->user->getFirstName() . ' ' . $this->user->getLastName() . ' has updated group permission for ' . $this->form_vars["first_name"] . ' ' . $this->form_vars["last_name"]);
                            $user->saveAuditData();
                            //End user departments
	                        //save default pluginkey for user
	                        $defaultShopPlatForm = UserShoppingPlatforms::getShopingPlatformByPluginKey('smarttrack');
	                        if($defaultShopPlatForm){
		                        $defaultShopPlatFormId =  $defaultShopPlatForm->getId();
		                        $userShoppingPlatforms = new UserShoppingPlatforms();
		                        $userShoppingPlatforms->setShoppingPlatformId($defaultShopPlatFormId);
		                        $userShoppingPlatforms->setReference('DefaultSmartTrackCred');


		                        $userShoppingPlatforms->setSiteUrl('');
		                        $userShoppingPlatforms->setUserId($user->getId());
		                        $api_key = md5($_SESSION['admin']['id'] . $_SESSION['admin']['firstname'] . $_SESSION['admin']['surname'] . date('H:i:s'));
		                        $api_secert = md5($_SESSION['user_name'] . $_SESSION['user_type'] . $_SESSION['user_account'] . date('H:i:s'));
		                        $userShoppingPlatforms->setApiKey($api_key);
		                        $userShoppingPlatforms->setApiSecrete($api_secert);
		                        $userShoppingPlatforms->setStatus(1);
		                        $userShoppingPlatforms->setDateCreated(time());
		                        $userShoppingPlatforms->save();
	                        }
                            $userLog = new UserLog();
                            if (empty($error_array)) {
                                if($this->account > 0 )
                                     $accountFilter = "&account=". $this->account_id_encoded;
                                
                                if ($this->form_vars["id"] > 0) {
                                    $oldUserData = serialize($userLogSystem);
                                    $newUserData = serialize($user);
                                    $userLog->createlog($sessionUser->getUserName() . ' has updated ' . $user->getUserName(), $user->getId(), 'USER',  $user->getId(), $oldUserData, $newUserData);
                                    $this->flashMsg->success(formatMessages(SUCCESS_USER_UPDATED), "../main/manage_user.php?id=" . base64_encode($user->getId()).$accountFilter);
                                } else {
                                            
                                            
                                    $userLog->createlog('Created new account', $user->getId(), 'USER',  $user->getId());
                                    $this->flashMsg->success(formatMessages(SUCCESS_USER_CREATED), "../main/manage_user.php?id=" . base64_encode($user->getId()).$accountFilter);
                                }
                            }else {
                                foreach ($error_array as $error) {
                                    $this->flashMsg->error($error);
                                }
                            }
                            //$sucess = 2;
                            //util_redirect("../main/manage_user.php?id=" . $user->getId() . "&&success=" . $sucess);
                        } else {
                            $this->flashMsg->error($isValid);
                        }
                    } else {
                        $this->flashMsg->error($error_array);
                    }
                    break;
            }
        }
        else {
            $this->form_vars["id"] = $this->user_id;
            $user = new User($this->user_id);
            /*if ($this->user->getuserType() == User::USER_TYPE_CORPORATE) {
                if ($user->getUserAccountId() != $this->user->getUserAccountId() ) {
                    util_redirect("403.php");
                    exit;
                }
            }*/
            $this->form_vars["user_type"] = $user->getUserType();
            $this->form_vars['user_name'] = $user->getUserName();
            $this->form_vars['active_flag'] = $user->getActiveFlag();
//            echo '<pre>';
//            print_r($this->form_vars['active_flag']);
//            echo '</pre>';
//            die;
            $this->form_vars['first_name'] = $user->getFirstName();
            $this->form_vars['last_name'] = $user->getLastName();
            $this->form_vars['address'] = $user->getAddress();
            $this->form_vars['email'] = $user->getEmail();
            $this->form_vars['phone'] = $user->getPhone();
            $this->form_vars['country_id'] = $user->getCountryId();
            $this->form_vars['api_key'] = $user->getApiKey();
            $this->form_vars['api_secert'] = $user->getApiSecert();
            $this->form_vars['profile_image'] = $user->getProfileImage();
            $this->form_vars['is_employee'] = $user->getIsEmployee();
            $this->form_vars['user_account_id'] = $user->getUserAccountId();
            $this->form_vars['warehouse_id'] = $user->getWarehouseId();
            $this->form_vars['dashboard'] = $user->getDashboard();
            $this->form_vars['receive_email'] = $user->getReceiveEmail();
            $this->form_vars["address_2"] = $user->getAddress2();
            $this->form_vars["address_3"] = $user->getAddress3();
            $this->form_vars["postcode"] = $user->getPostcode();
            $this->form_vars["state"] = $user->getState();
            $this->form_vars["city"] = $user->getCity();
            $this->form_vars["commission_break_event_amount"] = $user->getCommissionBreakEventAmount();

            $UserDepartment = new UserDepartmentFilter();
            $UserDepartment->addByUserId($this->user_id);
            $UserDepartmentObj = $UserDepartment->getList();
            $department_array = array();
            foreach ($UserDepartmentObj as $value) {
                $department_array[] = $value->getDepartmentId();
            }
            if (count($department_array) > 0)
                $this->selected_dep = $department_array;
            $userHasGroupsFilter = new UserHasGroupsFilter();
            $userHasGroupsFilter->addFilter("admin_id = " . $this->user_id);
            $userHasGroupsLists = $userHasGroupsFilter->getList();
            $userHasGroupsListArr = array();
            foreach ($userHasGroupsLists as $userHasGroupsList) {
                $userHasGroupsListArr[] = $userHasGroupsList->getGroupId();
            }
            $this->user_group = $userHasGroupsListArr;
            
//            if (isset($this->form_vars["action"]) && $this->form_vars['action'] == "get_accessibility_details") {
//                $output = [];
//                $group_user_type = $this->form_vars['group_user_type'];
//                $groupsStrIn = "";
//                if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
//                    $userHasGroupsFilter = new UserHasGroupsFilter();
//                    $userHasGroupsFilter->addJoin("groups g", "g.group_id", "uhg.group_id", "INNER JOIN");
//                    $userHasGroupsFilter->addFilter("       uhg.admin_id = " . $this->user->getId());
////                    $userHasGroupsFilter->addFilter("       g.group_type = '" . $group_user_type . "'");
//                    $groups = $userHasGroupsFilter->getColumnList("uhg.group_id");
//                    $groupsStr = "";
//                    foreach ($groups as $group) {
//                        $groupsStr .= "'" . $group->getGroupId() . "',";
//                    }
//                    if (!empty($groupsStr)) {
//                        $groupsStrIn = " AND group_id IN (" . rtrim($groupsStr, ',') . ") ";
//                    }
//                }
//                if (sizeof($this->user_group) > 0) {
//                    $selectedgroup = $this->user_group;
//                } else {
//                    $selectedgroup = $permission_group;
//                }
//                $output['data'] = Ddl::generateDDL('permission_group[]', 'GroupsFilter', 'is_deleted = 0 AND is_active = 1 ' . $groupsStrIn, 'group_name', 'group_id', $selectedgroup, ' multiple="multiple" name="favorite_fruits"', '', '', 'multiselect-basic', 'Group');
//                echo json_encode($output);
//                die;
//            }
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets_/libs/multi.js/multi.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password") {
                  input.attr("type", "text");
                } else {
                  input.attr("type", "password");
                }
              });       
                handlePasswordStrengthChecker();
                $('#is_employee').on('switchChange.bootstrapSwitch', function (event, state) {
                    if (state) {
                        $("#show_department").show();
                    } else {
                        $("#show_department").hide();
                        $("#deparment").val("");
                    }
                });
                $('#user_type').trigger('change');
            });
            //form submit start here
            $("#btnSave").click(function () {
                $("#overlay").show();
                $('#adminForm').validator().on('submit', function (e) {
                    var userNameReg = '^[a-zA-Z0-9_.-]*$';
                    var userNameVal = $('#user_name').val();
                    var msgError = '';
                    if(!userNameVal.match(userNameReg)){
                        msgError += "Please enter user name with letter and number only.<br>";
                    }

                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...

                        if ($('#user_name').val() == '')
                            msgError += "Please enter user name.<br>";
                        if ($('#first_name').val() == '')
                            msgError += "Please enter first name.<br>";
                        if ($('#last_name').val() == '')
                            msgError += "Please enter last name.<br>";
                        if ($('#email').val() == '')
                            msgError += "Please enter email address.<br>";
                        if ($('#country_name').val() == '')
                            msgError += "Please select country.<br>";
                        if ($('#warehouse_id').val() == '')
                            msgError += "Please select warehouse.<br>";
                        // if ($("#permission_group").val() != '' || $("#permission_group").val() == null){
                        //     msgError += "Please select accessibility.<br>";
                        // }
                        
        //                            var phone_number = $("#phone").val();
        //                            if(phone_number != ""){
        //                                var filter = /^[0-9-+]+$/;
        //                                if (filter.test(phone_number)) {
        //                                    return true;
        //                                }
        //                                else {
        //                                    msgError    += "Please enter valid phone number.<br>";
        //                                }
        //                            }
                        if ($('#address').val() == '')
                            msgError += "Please enter address.<br>";
                        if (parseInt($('#id').val) > 0)
                        {
                        } else
                        {
                            if ($('#password').val() == "") {
                                $('#password').attr("required");
                                msgError += "Please enter password.<br>";
                            }
                        }
                        // alert(msgError);
                        if (msgError == "")
                        {
                            $('#res_message').hide();
                            $('#res_message_div').html("");
                        } else
                        {
                            $("#res_message_div").addClass('alert-danger').removeClass('alert-success');
                            $('#res_message_div').html(msgError);
                            $('#res_message').show();
                        }
                        $("#overlay").hide();
                    }
                });
                $("#form_action").val("save");
                $("#adminForm").submit();
            });

            //PASSWORD STREANGH CHECKTER
            var handlePasswordStrengthChecker = function () {
                var initialized = false;
                var input = $("#password");
                input.keydown(function () {
                    if (initialized === false) {
                        // set base options
                        var options = {};
                        options.ui = {
                            container: "#pwd-container",
                            showVerdictsInsideProgressBar: true,
                            viewports: {
                                progress: ".pwstrength_viewport_progress"
                            }
                        };
                        options.common = {
                            raisePower: 1.4,
                            minChar: 8,
                            verdicts: ["Weak", "Normal", "Medium", "Strong", "Very Strong"],
                            scores: [17, 26, 40, 50, 60]
                        }
                        input.pwstrength(options);
                        // add your own rule to calculate the password strength
                        input.pwstrength("addRule", "demoRule", function (options, word, score) {
                            return word.match(/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/) && score;
                        }, 10, true);
                        // set as initialized
                        initialized = true;
                    }
                });
            }
            function validateEmail(sEmail) {
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test(sEmail))
                    return true;
                else if (sEmail != '')
                {
                    swal("", "Please enter valid email address", "info");
                    //   $("#email").focus();
                    return false;
                }
            }
            function emptyinputFeilds() {
                //                var select2Parentid = $("#parent_id").select2();
                //                    select2Parentid.val("").trigger('change');
                $(':input').val('');
                $('#address').val('One World Express IncOne World House, Pump Ln, Hayes, Greater London UB3 3NB');
                $('#country_name').val(0);
                $('#country_name').selectpicker('refresh');
            }
            function get_accessibility(obj) {
                console.log('this Obj',obj)
                //var user_type = obj.value;
                //var form_data = new FormData();
                //form_data.append('group_user_type', user_type);
                //form_data.append('action', 'get_accessibility_details');
                //$.ajax({
                //        url: 'manage_user.php?id=<?php //echo $this->user_id_encoded; ?>//',
                //        cache: false,
                //        contentType: false,
                //        processData: false,
                //        data: form_data,
                //        type: 'post',
                //        dataType: 'json',
                //        success: function (result) {
                //            $('#accessibility_ddl').html(result.data)
                //            $('.multiselect_drop_down').multiSelect();
                //        },
                //        error: function () {
                //                //alert('error handing here');
                //        }
                //});
            }
            $(document).on("change", "#user_type", function(event) {
                var userType = '';
                userType = $(this).val();
                if(userType == "sales_agent"){
                    $("#comission_break_div").show();
                }else{
                    $("#comission_break_div").hide();
                    $("#commission_break_event_amount").val('0.00');
                }
            });
            $('#country_id_temp').on('change', function () {
                var selectedCountry = $(this).find("option:selected").val();
                $("#country_id").val(selectedCountry);
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        $this->user = SessionManager::getUser();
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Add/Update Member</h4>
                        <div class="flex-shrink-0">
                            <a href="users-listing" class="btn btn-primary">List Member</a>
                            <?php
                            if (Permissions::checkFilePermission('list_users.php')) {
                                $accountFilter = '';
                                if($this->account > 0 )
                                    $accountFilter = "?account=". $this->account_id_encoded;
                                ?>
                                <a href="list_users.php<?=$accountFilter?>" class="btn blue"><span></span><i class="fa fa-list"></i>&nbsp;List Member</a>
                                <?php
                            }
                            if (Permissions::checkFilePermission('manage_user_audit')) {
                                if (isset($this->user_id) && ($this->user_id > 0)){
                                    ?>
                                    <a data-title="Accounts" data-table="user" data-container="audit_content" data-ajax_url="" data-id="<?php echo $this->user_id; ?>" id="btnAudit" href="javascript:;" class="btn btn-primary show_audit" title="audit" data-target="#audit-log" data-toggle="modal"> Audit </a>
                                    <?php
                                }
                            }
                            ?>
                            <div style="display: none;" class="col-md-12"  id="res_message" >
                                <div class="alert alert-success" id="res_message_div"></div>
                            </div>
                            <!--<div class="col-md-12 alert alert-success display-none"  id="res_message" ></div>-->
                            <div class="<?php echo(!empty($this->counting_error)) ? 'alert alert-danger' : ''; ?>">
                                <?php errorList::getItem()->render(); ?>
                            </div>
                            <?php
                            $this->flashMsg->display();
                            ?>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
							<form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off"  >
                            <div class="row gy-4">
								<div class="col-xxl-6 col-md-6">
                                    <div>
                                        <label for="user_name" class="form-label">Member Name</label>
                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="user_name"
                                                id="user_name" value="<?php echo @$user_name; ?>" required="required">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-6 col-md-6 password-strength" id="pwd-container">
                                    <label for="password" class="form-label">Password</label>
                                    <input maxlength="25" id="password" name="password" type="password" value="" class="form-control rounded-pill" autocomplete="new-password"  />
                                    <div class="pwstrength_viewport_progress"></div>
                                    <p class="help-block"><small>Leave password blank if don't want to change</small></p>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-6 col-md-6">
                                        <label for="user_type" class="form-label">Member Type</label>
                                            <?php
                                            $IsPeArr = User::USER_ROLES;//array('client' => 'General User', 'corporate' => 'Company', 'admin' => 'Super Admin');
                                            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                                $IsPeArr = array(
                                                    'corporate' => 'Company',
                                                    'client' => 'Client',
    //                                                'driver' => 'Driver',
    //                                                'sales_agent' => 'Sales Agent'
                                                    );
                                            echo Ddl::generateArrayDDL('user_type', $IsPeArr, $user_type, '', ' class="form-select mb-3 rounded-pill" onchange="get_accessibility(this)" placeholder="User Type"');
                                            ?>
                                </div>
                                <!--end col-->
									<?php if ($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
										<div  class="col-xxl-6 col-md-6" >
                                            <label for="user_account" class="form-label">Customer Account</label>
                                            <?php
                                                $accountParentId = 0;
                                                $includeParent = true;


                                                if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                                    $accountParentId = $this->user->getUserAccountId();
                                                if($this->account > 0 )
                                                   $accountParentId = $this->account;
                                                $allowedLevel = 0;
                                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                                    $allowedLevel = 1;
                                                }
                                                echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $user_account_id, "", 'class="form-select mb-3 rounded-pill" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel);
                                            ?>
										</div>
									<?php } ?>
									<div class="col-xxl-6 col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input id="first_name" name="first_name" value="<?php echo @$first_name; ?>" type="text" placeholder="First Name" required="" class="form-control rounded-pill" />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="first_name" class="form-label">Last Name</label>
                                        <input id="last_name" name="last_name" value="<?php echo @$last_name; ?>" type="text" placeholder="Last Name" required="" class="form-control rounded-pill"    />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input id="email" name="email" value="<?php echo @$email; ?>" type="email" placeholder="Email" required="" class="form-control rounded-pill" maxlength="40" onblur="validateEmail(this.value);" />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input id="phone" name="phone" type="text" value="<?php echo @$phone; ?>" class="form-control rounded-pill"  />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="address" class="form-label">Address 1</label>
                                        <input type="text" name="address"  value="<?php echo (!empty($address) ? $address : 'Logistic Lahore'); ?>" id="address" class="form-control rounded-pill"   />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="address_2" class="form-label">Address 2</label>
                                        <input type="text" name="address_2"  value="<?php echo (!empty($address_2) ? $address_2 : 'Logistic Lahore'); ?>" id="address_2" class="form-control rounded-pill" />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="address_3" class="form-label">Address 3</label>
                                        <input type="text" name="address_3"  value="<?php echo (!empty($address_3) ? $address_3 : 'Lahore'); ?>" id="address_3" class="form-control rounded-pill" />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="address_3" class="form-label">Postcode</label>
                                        <input type="text" name="postcode"  value="<?php echo (!empty($postcode) ? $postcode : '54000'); ?>" id="postcode" class="form-control rounded-pill" />
									</div>
									<div class="col-xxl-6 col-md-6">
											<label for="country_name" class="form-label">Country</label>
												<?php
												if (empty($country_id))
													$country_id = '225';
												echo Ddl::generateCountryDDL('country_name', $country_id, 'id', '  class="form-select mb-3 rounded-pill" required="" data-live-search="true" data-container="body" data-size="8"', '', '', false);
												?>
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" name="state"  value="<?php echo @$state ?>" id="state" class="form-control rounded-pill" />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" name="city"  value="<?php echo (!empty($city) ? DbAccess3::escape($city) : 'London'); ?>" id="city" class="form-control rounded-pill" placeholder="City"/>
									</div>
<!--									<div class="col-xxl-6 col-md-6">-->
<!--                                        <label for="warehouse_id" class="form-label">Warehouse</label>-->
<!--                                        --><?php
//                                        echo Ddl::generateDDL('warehouse_id', 'WarehouseFilter', '  is_active = 1 ', 'warehouse name', 'id', $warehouse_id, 'class="form-select mb-3 rounded-pill" required   ', 'Please select', '', 'warehouse_id', 'Select Hub');
//                                        ?>
<!--									</div>-->
									<div class="col-xxl-6 col-md-6">
                                        <label for="dashboard" class="form-label">Dashboard</label>
                                            <?php
                                            $IsPeArr = User::USER_DASHBOARD_ROLES;
                                            echo Ddl::generateArrayDDL('dashboard', $IsPeArr, $dashboard, '', ' class="form-control rounded-pill" ');
                                            ?>
									</div>
									<div class="col-xxl-6 col-md-6" style="display: none;" id="comission_break_div">
                                        <label for="commission_break_event_amount" class="form-label">Commission Break Event Amount</label>
                                        <input type="text" name="commission_break_event_amount"  value="<?php echo $commission_break_event_amount; ?>" id="commission_break_event_amount" class="form-control rounded-pill"  placeholder="Commission Break Event Amount" title="Commission Break Event Amount"/>
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="key_text" class="form-label">API Key</label>
                                        <input type="text" name="key_text"  value="<?php echo @$api_key; ?>" id="key_text" class="form-control rounded-pill"  readonly   />
									</div>
									<div class="col-xxl-6 col-md-6">
                                        <label for="secert_text" class="form-label">API Secret</label>
                                        <input type="text" name="secert_text"  value="<?php echo @$api_secert; ?>" id="secert_text" class="form-control rounded-pill" readonly />
									</div>
									<div style="display: none;" class="col-xxl-6 col-md-6">
                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                            <input checked="checked id="active_flag" type="checkbox" class="form-check-input" name="active_flag" <?php echo ($active_flag == '1' ? 'checked="checked"' : ''); ?>>
                                            <label class="form-check-label" for="active_flag">Active</label>
                                        </div>
									</div>

									<?php
									$receiveEmail = (($receive_email == "y") ? 'checked="checked"' : "");
									?>
                                    <div style="display: none;"  class="col-xxl-6 col-md-6">
                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                            <input <?php echo ($receiveEmail); ?> name="receive_email" id="receive_email" type="checkbox" class="form-check-input">
                                            <label class="form-check-label" for="receive_email">Manifest Email</label>
                                        </div>
                                    </div>
									<?php
									$isShowDepList = (($is_employee == "1") ? 'checked="checked"' : "");
									?>
                                    <div style="display: none;"  class="col-xxl-6 col-md-6">
                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                            <input <?php echo ($isShowDepList); ?>  name="is_employee" id="is_employee" type="checkbox" class="form-check-input">
                                            <label class="form-check-label" for="is_employee">Employee</label>
                                        </div>
                                    </div>
									<div id="show_department" class="col-xxl-6 col-md-6" <?php if (!$isShowDepList) { ?> style="display: none;" <?php } ?> >
											<label for="deparment" class="form-label">Department</label>
											<?php
											echo Ddl::generateDDL('deparment[]', 'DepartmentFilter', ' isdeleted = 0 AND isactive = 1 ', 'title', 'id', $this->selected_dep, ' class="form-select mb-3" multiple="multiple"   title="Department" data-original-title="Department"', '', '', 'deparment', 'Deparment');
											?>
									</div>
<!--									<div class="col-xxl-6 col-md-6">-->
<!--											<label for="secert_text" class="form-label">Accessibility</label>-->
<!--											<div id="accessibility_ddl">-->
<!--												-->
<!--											</div>-->
<!--									</div>-->

                                    <div class="col-xxl-6 col-md-6">
                                        <label for="profile_image" class="form-label"> Upload Profile Picture</label>
                                        <br clear="all">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                <?php
                                                if (trim($profile_image) != '' && file_exists('../_assets/profile_images/' . $profile_image)) {
                                                    echo '<img id="profile_img" src="../_assets/profile_images/' . $profile_image . '" title="" >';
                                                } else {
                                                    echo '<img id="profile_img" src = "../images/No-image-found.jpg">';
                                                }
                                                ?>

                                            </div>
                                            <div> <span class="btn default btn-file"> <span class="fileinput-new"> Select image </span> <span class="fileinput-exists"> Change </span>
                                                    <input id="profile_image"  type="file" name="profile_image" >
                                                </span> <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                                        </div>
                                        <div class="clearfix margin-top-10"> <span class="label label-primary"><small>NOTE!</span> Recommended profile picture dimensions (225 x 225) </small></div>
                                        <div class="form-group">
                                            <div class="input-group"> </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <a id="btnSave"   href="javascript:;" class="btn btn-success btn_save"><span></span>Save</a>
                                        <a href="list_users.php" id="btnCancel" class="btn_cancel btn btn btn-primary"><span></span>Cancel</a>
                                    </div>



                            </div>
							<div class="col-xxl-12 col-md-12">
								<input class="btn btn-primary" type="hidden" name="id" id="id" value="<?php echo @$this->user_id; ?>" />
								<input class="btn btn-primary" type="hidden" name="form_action" id="form_action" value="" />
							</div>
							</form>
                            <!--end row-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
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