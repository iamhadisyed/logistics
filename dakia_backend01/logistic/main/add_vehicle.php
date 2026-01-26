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
    private $counting_error;
    private $user_group;
    private $selected_dep;

    protected function init()
    {
        $this->user = SessionManager::getUser();

        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'list_vehicles.php' => Translation::GetCaption("DRIVERS"),
            "Add Vehicle"
        );
        $sessionUser = SessionManager::getUser();
        if (isset($this->form_vars["form_action"])) {
            switch ($this->form_vars["form_action"]) {
                // SAVE
                case "save":
                    $this->form_vars["id"] = intval($this->form_vars["id"]);

                    $userLogSystem = new User($this->form_vars["id"]);
                    $sessionUser = SessionManager::getUser();
                    $error_array = "";

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
                    $user->setWarehouseId($this->form_vars["hub"]);
                    $user->setDashboard($this->form_vars["dashboard"]);
                    $user->setPostcode($this->form_vars["postcode"]);
                    $user->setState($this->form_vars["state"]);
                    $user->setCity($this->form_vars["city"]);
                    $receiveEmail = 'n';
                    if (isset($this->form_vars["receive_email"])) {
                        $receiveEmail = 'y';
                        $this->form_vars["receive_email"] = 'y';
                    }
                    $user->setReceiveEmail($receiveEmail);
                    $user->setAddedBy($sessionUser->getId());
                    if ($user->getAddedDate() <= '0' || trim($user->getAddedDate()) == '' || trim($user->getAddedDate()) == '1970-01-01 00:00:00')
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
                    if (isset($this->form_vars["active_flag"])) {
                        $activeFlag = 1;
                        $this->form_vars["active_flag"] = 1;
                    }
                    $user->setActiveFlag($activeFlag);

                    $is_employee = 0;
                    if (isset($this->form_vars["is_employee"])) {
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
                    if (count($this->form_vars['permission_group']) == 0) {
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
                                $headers = "From: itsupport@oneworldexpress.com\r\nReply-To: itsupport@oneworldexpress.com";
                                $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                                $headers .= "MIME-Version: 1.0\r\n";
                                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                $message = 'Hello ' . $this->form_vars["contact"] . ',';             //add boundary string and mime type specification
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Just to Inform you that your password has been successfully changed. Below is the detail of your new password. ';
                                $message .= "\r\n";
                                $message .= 'Your new password is ' . $this->form_vars["password"];
                                $message .= "\r\n";
                                $message .= 'If you have not made this change please email to itsupport@oneworldexpress.com to keep your account safe.';
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Thanks';
                                $message .= "\r\n";
                                $message .= "One World Express Support Team";
                                //send the email
                                $mail_sent = @mail($to, $subject, $message, $headers);
                            } else if ($this->form_vars["id"] <= 0) {

                                $to = $this->form_vars["email"];
                                //define the subject of the email
                                $subject = "Successfully Created User" . date('d-m-Y');
                                //define the headers we want passed. Note that they are separated with \r\n
                                $headers = "From: itsupport@oneworldexpress.com\r\nReply-To: itsupport@oneworldexpress.com";
                                $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
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
                                $message .= 'If you have not created this account please email to itsupport@oneworldexpress.com.';
                                $message .= "\r\n";
                                $message .= "\r\n";
                                $message .= 'Thanks';
                                $message .= "\r\n";
                                $message .= "One World Express Support Team";
                                $mail_sent = @mail($to, $subject, $message, $headers);
                            }
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
                            $userHasGroups = new UserHasGroups();
                            $userHasGroups->deleteByAdminId($user->getId());
                            foreach ($this->form_vars['permission_group'] as $valueGroup) {
                                $userHasGroups = new UserHasGroups();
                                $userHasGroups->setAdminId($user->getId());
                                $userHasGroups->setGroupId($valueGroup);
                                $userHasGroups->save();
                            }
                            //End user departments
                            //save default pluginkey for user
                            $defaultShopPlatForm = UserShoppingPlatforms::getShopingPlatformByPluginKey('smarttrack');
                            if ($defaultShopPlatForm) {
                                $defaultShopPlatFormId = $defaultShopPlatForm->getId();
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
                                if ($this->form_vars["id"] > 0) {
                                    $oldUserData = serialize($userLogSystem);
                                    $newUserData = serialize($user);
                                    $userLog->createlog($sessionUser->getUserName() . ' has updated ' . $user->getUserName(), $user->getId(), 'USER', $user->getId(), $oldUserData, $newUserData);
                                    $this->flashMsg->success(formatMessages(SUCCESS_USER_UPDATED), "../main/manage_user.php?id=" . $user->getId());
                                } else {
                                    $userLog->createlog('Created new account', $user->getId(), 'USER', $user->getId());
                                    $this->flashMsg->success(formatMessages(SUCCESS_USER_CREATED), "../main/manage_user.php?id=" . $user->getId());
                                }
                            } else {
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
        } else {
            $id = util_get_num("id");
            $this->form_vars["id"] = $id;
            $user = new User($id);
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

            $UserDepartment = new UserDepartmentFilter();
            $UserDepartment->addByUserId(util_get_num("id"));
            $UserDepartmentObj = $UserDepartment->getList();
            $department_array = array();
            foreach ($UserDepartmentObj as $value) {
                $department_array[] = $value->getDepartmentId();
            }
            if (count($department_array) > 0)
                $this->selected_dep = $department_array;
            $userHasGroupsFilter = new UserHasGroupsFilter();
            $userHasGroupsFilter->addFilter("admin_id = " . $id);
            $userHasGroupsLists = $userHasGroupsFilter->getList();
            $userHasGroupsListArr = array();
            foreach ($userHasGroupsLists as $userHasGroupsList) {
                $userHasGroupsListArr[] = $userHasGroupsList->getGroupId();
            }
            $this->user_group = $userHasGroupsListArr;

            if (isset($this->form_vars["action"]) && $this->form_vars['action'] == "get_accessibility_details") {
                $output = [];
                $group_user_type = $this->form_vars['group_user_type'];
                $groupsStrIn = "";
                if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                    $userHasGroupsFilter = new UserHasGroupsFilter();
                    $userHasGroupsFilter->addJoin("groups g", "g.group_id", "uhg.group_id", "INNER JOIN");
                    $userHasGroupsFilter->addFilter("       uhg.admin_id = " . $this->user->getId());
//                    $userHasGroupsFilter->addFilter("       g.group_type = '" . $group_user_type . "'");
                    $groups = $userHasGroupsFilter->getColumnList("uhg.group_id");
                    $groupsStr = "";
                    foreach ($groups as $group) {
                        $groupsStr .= "'" . $group->getGroupId() . "',";
                    }
                    if (!empty($groupsStr)) {
                        $groupsStrIn = " AND group_id IN (" . rtrim($groupsStr, ',') . ") ";
                    }
                }
                if (sizeof($this->user_group) > 0) {
                    $selectedgroup = $this->user_group;
                } else {
                    $selectedgroup = $permission_group;
                }
                $output['data'] = Ddl::generateDDL('permission_group[]', 'GroupsFilter', 'is_deleted = 0 AND is_active = 1 ' . $groupsStrIn, 'group_name', 'group_id', $selectedgroup, ' multiple="multiple" class="multi-select multiselect_drop_down"', '', '', 'permission_group', 'Group');
                echo json_encode($output);
                die;
            }
        }
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
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
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>

        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                handlePasswordStrengthChecker();
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

                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...

                        if ($('#vehicle_type').val() == '')
                            msgError += "Please enter Vehicale Type.<br>";
                        if ($('#vehicle_make').val() == '')
                            msgError += "Please enter Vehicle Make.<br>";
                        if ($('#vehicle_model').val() == '')
                            msgError += "Please enter Vehicle Model.<br>";
                        if ($('#model_year').val() == '')
                            msgError += "Please enter Vehicle Model Year.<br>";
                        if ($('#registration_number').val() == '')
                            msgError += "Please Enter Vehicle Registration Number.<br>";
                        if ($("#vehicle_color").val() != '') {
                            msgError += "Please Enter Vehicle Color.<br>";
                        }
                        if ($("#vehicle_capacity").val() != '') {
                            msgError += "Please Enter Vehicle Capacity.<br>";
                        }
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
                        // alert(msgError);
                        if (msgError == "") {
                            $('#res_message').hide();
                            $('#res_message_div').html("");
                        } else {
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

            //form submit start here
            //Form validation Start
            //                var form = $('#adminForm');
            //                var error = $('.alert-danger', form);
            //                var success = $('.alert-success', form);
            //                form.validate({
            //                    doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            //                    errorElement: 'span', //default input error message container
            //                    errorClass: 'help-block help-block-error', // default input error message class
            //                    focusInvalid: false, // do not focus the last invalid input
            //                    rules: {
            //                        //account
            //                        user_name: {
            //                            required: true
            //                        },
            //                        first_name: {
            //                            required: true
            //                        },
            //                        last_name: {
            //                            required: true
            //                        },
            //                        //profile
            //                        email: {
            //                            required: true
            //                        },
            //                        phone: {
            //                            required: true
            //                        },
            //                        country_name: {
            //                            required: true
            //                        }
            //                    },
            //
            //                    messages: { // custom messages for radio buttons and checkboxes
            //                        'group_name': {
            //                            required: "Please select group name"
            //                        },
            //                        'first_name': {
            //                            required: "Please select country name"
            //                        },
            //                        'from_post_code': {
            //                            required: "Please enter from post code"
            //                        },
            //                        'to_post_code': {
            //                            required: "Please enter to postcode"
            //                        },
            //                        'city_name': {
            //                            required: "Please enter city name"
            //                        }
            //                    },
            //
            //                    errorPlacement: function (error, element) { // render error placement for each input type
            ////                        if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
            ////                            error.insertAfter("#form_gender_error");
            ////                        } else if (element.attr("name") == "payment[]") { // for uniform checkboxes, insert the after the given container
            ////                            error.insertAfter("#form_payment_error");
            ////                        } else {
            ////                            error.insertAfter(element); // for other inputs, just perform default behavior
            ////                        }
            //                          $("#res_message").show();
            //                          error.insertAfter("#res_message");
            //                    },
            //
            //                    invalidHandler: function (event, validator) { //display error alert on form submit
            //                        success.hide();
            //                        error.show();
            //                        App.scrollTo(error, -200);
            //                    },
            //
            //                    highlight: function (element) { // hightlight error inputs
            //                        $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            //                    },
            //
            //                    unhighlight: function (element) { // revert the change done by hightlight
            //                        $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            //                    },
            //
            //                    success: function (label) {
            //                        label
            //                            .addClass('valid') // mark the current input as valid and display OK icon
            //                            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            ////                        if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
            ////                            label.closest('.form-group').removeClass('has-error').addClass('has-success');
            ////                            label.remove(); // remove error label here
            ////                        } else { // display success icon for other inputs
            ////                            label
            ////                                .addClass('valid') // mark the current input as valid and display OK icon
            ////                                .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            ////                        }
            //                    },
            //
            //                    submitHandler: function (form) {
            //                        success.show();
            //                        error.hide();
            ////                        form[0].submit();
            //                        $("#form_action").val("save");
            //                        $("#form_action").val("save");
            //                        $.post("manage_remoteareas.php", $("#adminForm").serialize(), function (response) {
            //                            if (response.STATUS == "success") {
            //                                $("#res_message").html("Remoteareas added successfully");
            //                                $("#res_message").show();
            //                                $('#group_name').val("");
            //                                $('#group_name').selectpicker('refresh');
            //                                $('#country_name').val("");
            //                                $('#country_name').selectpicker('refresh');
            //                                $("#from_post_code").val("");
            //                                $("#to_post_code").val("");
            //                                $("#city_name").val("");
            //                                $("#add_carrier_id").val("");
            //                                $("#id").val("");
            //                                grid.getDataTable().ajax.reload();
            //                            } else {
            //            //                            $("#abc").html(response.message);
            //                            }
            //                        }, "json");
            //                        //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
            //                    }
            //
            //                });
            //                Form Validation End
            //            //Handle flash message
            //            function setFlashMsg(msg){
            //
            //            }
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
            };

            function validateEmail(sEmail) {
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test(sEmail))
                    return true;
                else if (sEmail != '') {
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
                var user_type = obj.value;
                var form_data = new FormData();
                form_data.append('group_user_type', user_type);
                form_data.append('action', 'get_accessibility_details');
                $.ajax({
                    url: 'manage_user.php?id=<?php echo util_get_num("id") ?>',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    success: function (result) {
                        $('#accessibility_ddl').html(result.data)
                        $('.multiselect_drop_down').multiSelect();
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

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

    protected function renderBody()
    {
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        $this->user = SessionManager::getUser();
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-user"></i>
                        Add/Update Vehicles
                    </div>
                    <div class="actions">
                        <?php
                        if (Permissions::checkFilePermission('list_users.php')) {
                            ?>
                            <a href="list_vehicles.php" class="btn blue"><span></span><i class="fa fa-list"></i>&nbsp;List
                                Vehicles</a>
                            <?php
                        }
                        if (Permissions::checkFilePermission('manage_user_audit')) {
                            if (isset($id) && ($id > 0)) {
                                ?>
                                <a data-title="Accounts" data-table="user" data-container="audit_content"
                                   data-ajax_url="" data-id="<?php echo $id; ?>" id="btnAudit" href="javascript:;"
                                   class="btn btn-primary show_audit" title="audit" data-target="#audit-log"
                                   data-toggle="modal"> Audit </a>
                                <?php
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div style="display: none;" class="col-md-12" id="res_message">
                            <div class="alert alert-success" id="res_message_div"></div>
                        </div>
                        <!--<div class="col-md-12 alert alert-success display-none"  id="res_message" ></div>-->
                        <div class="<?php echo (!empty($this->counting_error)) ? 'alert alert-danger' : ''; ?>">
                            <?php errorList::getItem()->render(); ?>
                        </div>
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Type</label>
                                <div class="input-group"><span class="input-group-addon"> <i
                                                class="fa fa-user"></i> </span>
                                    <div class="input-icon right"><i class="fa tooltips font-red"
                                                                     data-original-title="Vehicle is mandatory">*</i>
                                        <input id="vehicle_type" name="vehicle_type"
                                               value="<?php if (!empty($vehicle_type)) echo $vehicle_type; ?>"
                                               type="text" placeholder="Vehicle Type" required=""
                                               class="form-control tooltipbutton" data-toggle="tooltip"
                                               data-placement="top" title="Vehicle Type"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Make</label>
                                <div class="input-group"><span class="input-group-addon"> <i
                                                class="fa fa-user"></i> </span>
                                    <div class="input-icon right"><i class="fa tooltips font-red"
                                                                     data-original-title="Vehicle Make is mandatory">*</i>
                                        <input id="vehicle_make" name="vehicle_make"
                                               value="<?php if (!empty($vehicle_make)) echo $vehicle_make; ?>"
                                               type="text" placeholder="Vehicle Make" required=""
                                               class="form-control tooltipbutton" data-toggle="tooltip"
                                               data-placement="top" title="Vehicle Make"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Model</label>
                                <div class="input-group"><span class="input-group-addon"> <i class="fa fa-envelope"></i> </span>
                                    <div class="input-icon right"><i class="fa tooltips font-red"
                                                                     data-original-title="Vehicle Model is mandatory">*</i>
                                        <input id="vehicle_model" name="vehicle_model"
                                               value="<?php if (!empty($vehicle_model)) echo $vehicle_model; ?>"
                                               type="text"
                                               placeholder="Vehicle Model" required=""
                                               class="form-control tooltipbutton"
                                               maxlength="40" rel="tooltip"
                                               data-original-title="Vehicle Model"
                                               data-placement="top" data-toggle="tooltip" data-placement="top"
                                               title="Vehicle Model"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Model Year</label>
                                <div class="input-group date date-picker margin-bottom-5"
                                     data-date-format="yyyy-mm-dd:">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" class="form-control form-filter input-sm" readonly=""
                                           name="model_year" placeholder="Date Created" id="model_year"
                                           data-date-format="yyyy-mm-dd" data-original-title="" title="Model Year">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Registration Number</label>
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red"
                                           data-original-title="Vehicle Registration Number is mandatory">*</i>
                                        <input id="registration_number" name="registration_number"
                                               value="<?php if (!empty($registration_number)) echo $registration_number; ?>"
                                               type="text" placeholder="Registration Number" required=""
                                               class="form-control tooltipbutton" data-toggle="tooltip"
                                               data-placement="top" title="Registration Number"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Color</label>
                                <div class="input-group"><span class="input-group-addon"> <i
                                                class="fa fa-user"></i> </span>
                                    <div class="input-icon right"><i class="fa tooltips font-red"></i>
                                        <input id="vehicle_color" name="vehicle_color"
                                               value="<?php if (!empty($vehicle_color)) echo $vehicle_color; ?>"
                                               type="text" placeholder="Vehicle Color"
                                               class="form-control tooltipbutton" data-toggle="tooltip"
                                               data-placement="top" title="Vehicle Color"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Capacity</label>
                                <div class="input-group"><span class="input-group-addon"> <i class="fa fa-envelope"></i> </span>
                                    <div class="input-icon right"><i class="fa tooltips font-red"
                                                                     data-original-title="Vehicle Capacity is mandatory">*</i>
                                        <input id="vehicle_capacity" name="vehicle_capacity"
                                               value="<?php if (!empty($vehicle_capacity)) echo $vehicle_capacity; ?>"
                                               type="text"
                                               placeholder="Vehicle capacity" required=""
                                               class="form-control tooltipbutton"
                                               maxlength="40" rel="tooltip"
                                               data-original-title="Vehicle Capacity"
                                               data-toggle="tooltip" data-placement="top"
                                               title="Vehicle Capacity"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Vehicle Number</label>
                                <div class="input-group"><span class="input-group-addon"><i
                                                class="fa fa-phone"></i> </span>
                                    <input id="vehicle_number" name="vehicle_number" type="text"
                                           value="<?php if (!empty($vehicle_number)) echo $vehicle_number; ?>"
                                           placeholder="Vehicle Number" class="form-control tooltipbutton"
                                           data-toggle="tooltip" data-placement="top" title="Vehicle Number"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a id="btnSave" href="javascript:void(0);" class="btn btn-primary btn_save"><span></span>Save</a>
                            <a href="list_vehicles.php" id="btnCancel"
                               class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?php if(!empty($vehicle_id)) echo $vehicle_id; ?>"/>
            <input type="hidden" name="form_action" id="form_action" value=""/>
        </form>
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

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
