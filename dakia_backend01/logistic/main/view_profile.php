<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'ivisualcomponent', 'ddl.inc'
], 'library');
include_classes([
    'errorlist.class'
], 'visualcomponents');

include_classes([
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'currency.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'country.class',
    'countryfilter.class',
    'warehouse.class',
    'services.class',
    'usershoppingplatforms.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class',
    'marketplaces.class',
    'marketplacesfilter.class',
    'marketplacesauthenticatefieldfilter.class',
    'marketplacesauthenticatefield.class',
    'api2cart.class',
    'marketplaceslog.class',
    'marketplacesauthenticatefield.class',
    'marketplacesauthenticatefieldfilter.class',
]);


class Page extends BasePage
{

    private $error_list = array();
    private $login_msg;
    private $old_password;
    private $counting_error;
    private $user = null;
    private $userAccount = null;
    private $userObj = null;
    private $userTotalBalance = 0;

    /*     * *
     * Controller logic
     */

    protected function init()
    {
        $this->user = Sessionmanager::getUser();
        $this->userObj = new User($this->user->getId());
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->userTotalBalance = getBalance($this->user->getUserAccountId());

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "save_paypal") {
//            data: {action: 'save_paypal',paypal_client_id:paypal_client_id,paypal_client_secret:paypal_client_secret,paypal_currency:paypal_currency},
            $paypal_client_id = $this->form_vars['paypal_client_id'];
            $paypal_client_secret = $this->form_vars['paypal_client_secret'];
            $paypal_currency = $this->form_vars['paypal_currency'];
            $userAccount = new CustomerAccount($this->user->getUserAccountId());
            $userAccount->setPaypalClientId($paypal_client_id);
            $userAccount->setPaypalClientSecret($paypal_client_secret);
            $userAccount->setPaypalCurrency($paypal_currency);
            $userAccount->save();
            echo "User paypal updated successsully";
            die;
        }
        // adds/updates subscriber request
        if(isset($this->form_vars['action']) && $this->form_vars['action'] == "subscribe"){
            /*
             * subscriber Request
             */
            $parentId = ($this->userAccount->getParentId() > 0 ? $this->userAccount->getParentId() : 0);

            $userMarketPlacesMapping = (!empty($this->form_vars['rowid']) ? $this->form_vars['rowid'] : "");
            $userMarketPlacesId = (!empty($this->form_vars['mpid']) ? $this->form_vars['mpid'] : "");
            $marketPlacesId = (!empty($this->form_vars['mpid']) ? $this->form_vars['mpid'] : "");
            $lastStatus = 'rq';
            if($userMarketPlacesMapping > 0){
                if($parentId == 0){
                    $userMarketPlacesMapping = new UserMarketPlacesMapping($userMarketPlacesMapping);
                    $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                    $userMarketPlacesMapping->setUserParentId($parentId);
                    $userMarketPlacesMapping->setStatus("approved");
                    $userMarketPlacesMapping->setActive("1");
                    $userMarketPlacesMapping->save();
                }else{
                    $userMarketPlacesMapping = new UserMarketPlacesMapping($userMarketPlacesMapping);
                    $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                    $userMarketPlacesMapping->setUserParentId($parentId);
                    $userMarketPlacesMapping->setStatus("pending");
                    $userMarketPlacesMapping->setActive("1");
                    $userMarketPlacesMapping->save();
                }
            }else{
                // Get all fields from market_places_authenticate_field with empty jason into mapping table
                $marketPlacesAuthenticateField = New MarketPlacesAuthenticateFieldFilter();
                $marketPlacesAuthenticateField->addMarketPlacesIdFilter($userMarketPlacesId);
                $marketPlacesAuthenticateFieldData = $marketPlacesAuthenticateField->getColumnList();
                $fieldsArr = [];
                $fieldsJsonArr = [];
                if(count($marketPlacesAuthenticateFieldData)){
                    foreach ($marketPlacesAuthenticateFieldData as $marketPlacesAuthenticateFieldDatum) {
                        $fieldsArr[$marketPlacesAuthenticateFieldDatum->getFieldValue()] = "";
                    }
                }
                $fieldsJsonArr = json_encode($fieldsArr);
                // Add New Entry to UserMarketPlacesMapping table for this user account
                $userMarketPlacesMapping = new UserMarketPlacesMapping();
                $lastStatus = 'rq';
                $userMarketPlacesMapping->setMarketPlacesId($userMarketPlacesId);
                $userMarketPlacesMapping->setUserAccountId($this->user->getUserAccountId());
                $userMarketPlacesMapping->setActive('1');
                $userMarketPlacesMapping->setAuthData($fieldsJsonArr);
                $userMarketPlacesMapping->setDateCreated(time());
                $userMarketPlacesMapping->setUserParentId($parentId);
                if($parentId == 0){
                    $userMarketPlacesMapping->setStatus("approved");
                }else{
                    $userMarketPlacesMapping->setStatus("pending");
                }
                $userMarketPlacesMapping->save();
            }
            $statusArr = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','expired'=>'expired','rq'=>'requested'];
            /*
             * save log
             */
            $marketPlacesLog = New MarketPlacesLog();
            $marketPlacesLog->setMarketPlaceId($marketPlacesId);
            $marketPlacesLog->setUserAccountId($this->user->getUserAccountId());
            $marketPlacesLog->setLastStatus($statusArr[$lastStatus]);
            if($parentId == 0){
                $marketPlacesLog->setCurrentStatus("approved");
            }else{
                $marketPlacesLog->setCurrentStatus("pending");
            }
            $marketPlacesLog->setDateRequest(time());
            $marketPlacesLog->save();
            die;
        }

        if(isset($this->form_vars['rowid'], $this->form_vars['umps_id'])){
            /*
             * un subscribe
             */
            $getRow = new UserMarketPlacesMappingFilter();
            $umps_id = $_POST['umps_id'];
            $rowid = $_POST['rowid'];
            $getRow->unSubscribe($rowid, $umps_id);
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_market_platform_keys' && $this->form_vars['action'] != 'save_market_platform_subscriber') {
            // Set User Shopping Platforms
            $warning = [];
            if (!empty($this->form_vars['shopping_plateform'])) {

                foreach ($this->form_vars['shopping_plateform'] as $platform_id => $plateformData) {
                    if (!empty($this->user->getUserAccountId()) && $this->user->getUserAccountId() > 0) {
                        UserMarketPlacesMapping::deleteSpecificUserMarketPlacesMappingList($this->user->getUserAccountId(), md5 ($platform_id));
                    }
                    $storeKey = '';
                    if (!empty($plateformData)) {
                        $marketPlaceObj = new MarketPlaces($platform_id);
                        if ($marketPlaceObj->getIsApi2cart() == 1) {
                            $api2CartObj = new Api2cart();
                            $apiData = $plateformData;
                            $apiData['cart_id'] = $marketPlaceObj->getPluginKey();
                            $response = $api2CartObj->addCartInApi2Cart($apiData);
                            if ($response['status'] == true) {
                                $storeKey = $response['store_key'];
                            } else {
                                $warning[] = $marketPlaceObj->getTitle() . ' credentials are invalid. <br>Error: ' . $response['result'] . '<br>';
                            }
                        }
                    }

                    $userPlatform = new UserMarketPlacesMapping();
                    $userPlatform->setMarketPlacesId($platform_id);
                    $userPlatform->setUserAccountId($this->user->getUserAccountId());
                    $userPlatform->setAuthData(json_encode($plateformData));
                    $userPlatform->setStoreKey($storeKey);
                    $userPlatform->setActive(1);
                    $userPlatform->save();


                }
                $output = ['status' => 'success', 'message' => 'Information saved successfully. <br> ' . implode('<br>', $warning)];
            } else {
                $output = ['status' => 'error', 'message' => 'No platform available.'];
            }
            echo json_encode($output);
            exit;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "save_market_place") {
            // Set User Shopping Platforms
            UserMarketPlacesMapping::deleteUserMarketPlacesMappingList($this->user->getUserAccountId());
            foreach ($this->form_vars['shopping_plateform'] as $platformId => $platformData) {
                $userPlatform = new UserMarketPlacesMapping();
                $userPlatform->setMarketPlacesId($platformId);
                $userPlatform->setUserAccountId($this->user->getUserAccountId());
                $userPlatform->setAuthData(json_encode($platformData));
                $userPlatform->save();
            }
            echo 'Data saved successfully';
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == "update_personal_info") {
            $output = [];
            $user = new User(intval($this->user->getId()));
            $_address = DbAccess3::escape($this->form_vars["address"]);
            $_address2 = DbAccess3::escape($this->form_vars["address2"]);
            $_address3 = DbAccess3::escape($this->form_vars["address3"]);
            $city = DbAccess3::escape($this->form_vars["city"]);
            $postcode = DbAccess3::escape($this->form_vars["postcode"]);
            if (trim($_address) == "" && trim($postcode) == "") {
                $_address = "One world express";
                $_address2 = "One world house, pump lane";
                $_address3 = "hayes";
                $city = "London";
                $postcode = "UB3 3NB";
            }
            $user->setFirstName(DbAccess3::escape($this->form_vars["first_name"]));
            $user->setLastName(DbAccess3::escape($this->form_vars["last_name"]));
            $user->setEmail(DbAccess3::escape($this->form_vars["email"]));
            $user->setPhone(DbAccess3::escape($this->form_vars["phone"]));
            $user->setAddress($_address);
            $user->setAddress2($_address2);
            $user->setAddress3($_address3);
            $user->setCity($city);
            $user->setPostcode($postcode);
            $user->setState(DbAccess3::escape($this->form_vars["state"]));
            $user->setCountryId(DbAccess3::escape($this->form_vars["country"]));
            $user->save();
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $email = $_POST["email"];
            } else {
                $email = '';
            }
            $output = ['status' => 'success', 'message' => 'Information saved successfully.', 'first_name' => strip_tags(DbAccess3::escape($this->form_vars["first_name"])), 'last_name' => strip_tags($this->form_vars["last_name"]), 'email' => strip_tags(DbAccess3::escape($email)), 'phone' => preg_match('/^[0-9 +\d]+$/', $this->form_vars["phone"]) ? $this->form_vars["phone"] : ''];
            /* over ride session */
            $new_list = new User($this->user->getId());
            $_SESSION['session_user'] = $new_list;
            echo json_encode($output);
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == "update_profile_image") {
            $error_array = [];
            $output = [];
            $profile_image = "";
            if (isset($_FILES["profile_image"]) && trim($_FILES["profile_image"]["name"]) != '') {
                $newPath = "../_assets/profile_images/";
                if (!file_exists($newPath))
                    @mkdir($newPath, 0775);
                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $temp = explode(".", $_FILES["profile_image"]["name"]);
                $extension = end($temp);
                if ((($_FILES["profile_image"]["type"] == "image/gif") || ($_FILES["profile_image"]["type"] == "image/jpeg") || ($_FILES["profile_image"]["type"] == "image/jpg") || ($_FILES["profile_image"]["type"] == "image/pjpeg") || ($_FILES["profile_image"]["type"] == "image/x-png") || ($_FILES["profile_image"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                    if ($_FILES["profile_image"]["error"] > 0) {
                        $error_array[] = "Return Code: " . $_FILES["profile_image"]["error"];
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
                    $error_array[] = "Invalid file, only gif, jpeg, jpg and png files are allowed.";
                }
            } else {
                $error_array[] = "Please select file to upload.";
            }
            if (empty($error_array) && $profile_image != '') {
                $old_profile_image = $this->user->getProfileImage();
                @unlink('../_assets/profile_images/' . $old_profile_image);
                $this->user->setProfileImage($profile_image);
                $user = new User(intval($this->user->getId()));
                $user->setProfileImage($profile_image);
                $user->save();
                $output = ['status' => 'success', 'message' => 'Avatar changed successfully.', 'image' => "/_assets/profile_images/" . $profile_image];
            } else {
                $output = ['status' => 'error', 'message' => implode("<br />", $error_array), 'image' => ""];
            }
            echo json_encode($output);
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == "update_password") {
            $error_array = [];
            $output = [];
            $user = new User(intval($this->user->getId()));
            $old_pass = $user->getPassword();
            $current_password = $_POST["current_password"];
            if (!password_verify(DbAccess3::escape($current_password), $old_pass)) {
                $error_array[] = "Invalid current password.";
            } else if (!preg_match("/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/", $_POST["new_password"]) && !empty($_POST["new_password"])) {
                $error_array[] = " The Pasword must be
                                        at least one upper case english letter,
                                        at least one lower case english letter,
                                        at least one digit,
                                        at least one special character and
                                        minimum 8 in length";
            }
            if (empty($error_array)) {
                $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
                $user->setPassword($new_password);
                $user->save();
                $output = ['status' => 'success', 'message' => 'Password updated successfully.'];
            } else {
                $output = ['status' => 'error', 'message' => implode("<br />", $error_array)];
            }
            echo json_encode($output);
            exit;
        }


        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("PROFILE")
        );
    }

    private function languageDropdown($lang)
    {
        $languageFilter = new LanguageFilter();
        $languageFilter->AddOrderByLanguage();
        $list = $languageFilter->getColumnList("id, language");
        echo "<select id='language' name='language' class='form-control select2'  data-live-search='true'>";
        if ($lang == "")
            $lang = "en-GB";
        $selected = "";
        foreach ($list as $language) {
            if ($language->getLanguage() == $lang)
                $selected = " selected ";
            else
                $selected = "";
            echo "<option value='" . $language->getLanguage() . "' $selected>" . $language->getLanguage() . "</option>";
        }
        echo "</select>";
    }

    /*     * *
     * Content
     */

    protected function renderHead()
    {

    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <style>
            .progress-bar {
                width: 100% !important
            }

            .password-strength .password-verdict {
                display: inline-block !important;
                margin-top: 0px !important;
                margin-left: 0px !important;
            }

            .profile label {
                margin-top: 0px !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>

        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript">
        </script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript">
        </script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
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
                $('.show_tooltip').tooltip();
                $(".ajax_message").hide();
                handlePasswordStrengthChecker();
                var zindex = 10;

//                Subscribe and Unsubscribe request button
                $('.btn-subscribe').click(function(e) {
                    e.preventDefault();
                    var rowid = $(this).attr("id");
                    var toggleinfo_id = $("#toggleinfo_"+rowid);
                    var ths = $(this);
                    var umps_id = $(this).attr("umps_id");
                    var mpid = $(this).attr("mpid");
                    var val = $(this).text();
                    if(val == "Subscribe" || val == "Resubscribe" || val == "Click to Subscribe"){
                        title = "<?php echo Translation::GetCaption("Do you want to subscribe ?") ?>";
                        swal({
                                title: title,
                                text: "",
                                type: "info",
                                showCancelButton: true,
                                confirmButtonClass: "btn-info",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $.ajax({
                                        type: "POST",
                                        data: {action:"subscribe",mpid:mpid, rowid:rowid},
                                        success: function (data) {
                                            $('#ajax_message_market_places').html("<?php echo Translation::GetCaption("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                            $(".scroll-to-top").click();
                                            $("#ajax_message_market_places").addClass("alert alert-success");
                                            $('#ajax_message_market_places').show();
                                            $(ths).html("Request Pending");
                                        },
                                        error: function () {
                                            alert('error handing here');
                                        }
                                    });
                                }
                            })
                    }
                    if(val == "Unsubscribe"){
                        title = "<?php echo Translation::GetCaption("Do you want to unsubscribe ?") ?>";
                        swal({
                                title: title,
                                text: "",
                                type: "info",
                                showCancelButton: true,
                                confirmButtonClass: "btn-info",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $.ajax({
                                        type: "POST",
                                        data: {umps_id:umps_id, rowid:rowid},
                                        success: function (data) {
                                            $('#ajax_message_market_places').html("<?php echo Translation::GetCaption("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                            $(".scroll-to-top").click();
                                            $("#ajax_message_market_places").addClass("alert alert-success");
                                            $('#ajax_message_market_places').show();
                                            $(ths).html("Resubscribe");
                                            $(toggleinfo_id).off('click');
                                        },
                                        error: function () {
                                            alert('error handing here');
                                        }
                                    });
                                }
                            })
                    }
                    if(val == "Request Pending"){
                        return false;
                    }

                });

                $(".toggle-info").click(function (e) {
                    e.preventDefault();
                    var active_status = $(this).attr("active_status");
                    if(active_status == 0){
                        return false;
                    }else{
                        var isShowing = false;
                        if ($(this).closest('.card').hasClass("show")) {
                            isShowing = true
                        }

                        if ($("div.cards").hasClass("showing")) {
                            // a card is already in view
                            $("div.card.show")
                                .removeClass("show");

                            if (isShowing) {
                                // this card was showing - reset the grid
                                $("div.cards")
                                    .removeClass("showing");
                            } else {
                                // this card isn't showing - get in with it
                                $(this).closest('.card')
                                    .css({
                                        zIndex: zindex
                                    })
                                    .addClass("show");

                            }

                            zindex++;

                        } else {
                            // no cards in view
                            $("div.cards")
                                .addClass("showing");
                            $(this).closest('.card')
                                .css({
                                    zIndex: zindex
                                })
                                .addClass("show");

                            zindex++;
                        }
                    }
                });

                $("#search_marketplace").on('keyup keypress', function (e) {
                    $('.search_carrier_custom').each(function (e) {
                        var current = $.trim($(this).data('name')).toLowerCase();
                        var search_str = $.trim($("#search_marketplace").val()).toLowerCase();
                        if (current.indexOf(search_str) >= 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });

                //form submit start here
                $("#btn_personal_info").click(function () {
                    var msgError = '';
                    if ($('#first_name').val() == '')
                        msgError += "Please enter first name.<br>";
                    if ($('#last_name').val() == '')
                        msgError += "Please enter last name.<br>";
                    if ($('#email').val() == '') {
                        msgError += "Please enter email address.<br>";
                    } else if (validateEmail($('#email').val()) == false) {
                        msgError += "Please enter valid email address.<br>";
                    }
                    if ($('#address').val() == '')
                        msgError += "Please enter address.<br>";
                    if (msgError != '') {
                        $("#msg_personal_info div.alert").html(msgError);
                        $("#msg_personal_info div.alert").removeClass('alert-success').removeClass('alert-info')
                            .addClass('alert-danger');
                    } else {
                        $("#btn_personal_info").attr('disabled', 'disabled');
                        $("#msg_personal_info div.alert").html('Please wait...');
                        $("#msg_personal_info div.alert").removeClass('alert-success').removeClass('alert-danger')
                            .addClass('alert-info');
                        $.post('view_profile.php', $("#frm_personal_info").serialize(), function (response) {
                            $("#msg_personal_info div.alert").html(response.message);
                            if (response.status == 'success') {
                                $("#msg_personal_info div.alert").removeClass('alert-danger').removeClass(
                                    'alert-info').addClass('alert-success');
                                $("#personal_info_full_name").html(response.first_name + " " + response
                                    .last_name);
                                $("#personal_info_email").html(response.email);
                                $("#personal_info_phone").html(response.phone);
                            }
                            $("#btn_personal_info").removeAttr('disabled');
                        }, "json");
                    }
                    $("#msg_personal_info").show();
                });
                $("#btn_change_pic").click(function () {
                    $("#btn_change_pic").attr('disabled', 'disabled');
                    $("#msg_change_pic div.alert").html('Please wait...');
                    $("#msg_change_pic div.alert").removeClass('alert-success').removeClass('alert-danger')
                        .addClass('alert-info');
                    $("#msg_change_pic").show();
                    var formData = new FormData();
                    formData.append('func', 'update_profile_image');
                    formData.append('profile_image', $('#profile_image')[0].files[0]);
                    $.ajax({
                        url: 'view_profile.php',
                        data: formData,
                        type: 'POST',
                        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                        processData: false, // NEEDED, DON'T OMIT THIS
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            $("#msg_change_pic div.alert").html(response.message);
                            if (response.status == 'success') {
                                $("#msg_change_pic div.alert").removeClass('alert-danger').removeClass(
                                    'alert-info').addClass('alert-success');
                                $("#profile_avatar").attr("src", response.image);
                            } else {
                                $("#msg_change_pic div.alert").removeClass('alert-success').removeClass(
                                    'alert-info').addClass('alert-danger');
                            }
                            $("#btn_change_pic").removeAttr('disabled');
                        }
                    });
                });
                $("#btn_update_pass").click(function () {
                    var msgError = '';
                    if ($('#current_password').val() == '')
                        msgError += "Please enter current password.<br>";
                    if ($('#new_password').val() == '')
                        msgError += "Please enter new password.<br>";
                    if ($('#re_password').val() != $('#new_password').val())
                        msgError += "Your password and re-password does not match.<br>";
                    if (msgError != '') {
                        $("#msg_update_pass div.alert").html(msgError);
                        $("#msg_update_pass div.alert").removeClass('alert-success').removeClass('alert-info')
                            .addClass('alert-danger');
                    } else {
                        $("#btn_update_pass").attr('disabled', 'disabled');
                        $("#msg_update_pass div.alert").html('Please wait...');
                        $("#msg_update_pass div.alert").removeClass('alert-success').removeClass('alert-danger')
                            .addClass('alert-info');
                        $.post('view_profile.php', $("#frm_update_pass").serialize(), function (response) {
                            $("#msg_update_pass div.alert").html(response.message);
                            if (response.status == 'success') {
                                $("#msg_update_pass div.alert").removeClass('alert-danger').removeClass(
                                    'alert-info').addClass('alert-success');
                            } else {
                                $("#msg_update_pass div.alert").removeClass('alert-success').removeClass(
                                    'alert-info').addClass('alert-danger');
                            }
                            $("#btn_update_pass").removeAttr('disabled');
                        }, "json");
                    }
                    $("#msg_update_pass").show();
                });
                // btnSave click
                $('#btnSave').click(function () {
                    var frmMarketPlace = '';
                    frmMarketPlace = $("#frm_matket_place").serialize();
                    $.ajax({
                        type: "POST",
                        url: "view_profile.php",
                        data: frmMarketPlace,
                        dataType: "html",
                        success: function (data) {
                            swal("", data, "info");
                            //var obj = jQuery.parseJSON(data);
                            //if the dataType is not specified as json uncomment this
                            // do what ever you want with the server response
                        },
                        error: function () {
                            alert('error handling here');
                        }
                    });
                });

            });
            //PASSWORD STREANGH CHECKTER
            var handlePasswordStrengthChecker = function () {
                var initialized = false;
                var input = $("#new_password");
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
                            return word.match(
                                /^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/
                            ) && score;
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
                else if (sEmail != '') {
                    //swal("", "Please enter valid email address", "info");
                    //   $("#email").focus();
                    return false;
                }
            }

            $("#btn_update_paypal").click(function () {
                var paypal_client_id = $("#paypal_client_id").val();
                var paypal_client_secret = $("#paypal_client_secret").val();
                var paypal_currency = $("#paypal_currency").val();
                $.ajax({
                    type: "POST",
                    url: "view_profile.php",
                    data: {
                        action: 'save_paypal',
                        paypal_client_id: paypal_client_id,
                        paypal_client_secret: paypal_client_secret,
                        paypal_currency: paypal_currency
                    },
                    dataType: "html",
                    success: function (data) {

                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });



            $(".btn_update_market_places_keys").click(function () {
                $.ajax({
                    type: "POST",
                    url: "view_profile.php",
                    data: $(this).closest('.market_platform_setting_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            $('#ajax_message_market_places').show().find('.alert').addClass('alert-success')
                                .html(data.message);
                        } else {
                            $('#ajax_message_market_places').show().find('.alert').addClass('alert-danger')
                                .html(data.message);
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
        </script>
        <?php
    }

    protected function renderFooter()
    {

    }

    /*     * *
     * Content
     */

    protected function renderBody()
    {

        ?>
        <div class="<?php echo (!empty($this->counting_error)) ? 'alert alert-danger' : ''; ?>">
            <?php errorList::getItem()->render(); ?></div>
        <?php
        if (isset($_GET['save']) && $_GET['save'] == 'ok' && empty($this->counting_error)) {
            ?>
            <div class="note note-success">
                <p><?php echo Translation::GetCaption("CHANGES_UPDATE_SUCCESSFULLY"); ?></p>
            </div>
        <?php } ?>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-user"></i>

                        <?php echo Translation::GetCaption("PROFILE_OF"); ?>

                    </div>
                    <!-- Accounts Balance Section currently disabled due to Sage API integration pending  <div class="actions">
                        <?php
                    /* $bClass = "green";
                     $accountBalance = "0.00 GBP";
                     if(count($this->userTotalBalance) > 0) {
                         $accountBalance = $this->userTotalBalance['balance'];
                         if($accountBalance <= 0) {
                             $bClass = "red";
                         }
                     }*/
                    ?>
                        <span><label class="btn <?php// echo $bClass; ?> btn-outline btn-sm active"><strong>Account Balance <?php //echo $accountBalance; ?></strong></label></span>
                    </div>-->
                </div>
                <div class="portlet-body">
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="profile" style="padding: 0">
                        <div class="tabbable-line tabbable-full-width">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab_1_1" data-toggle="tab"> Overview </a>
                                </li>
                                <li>
                                    <a href="#tab_1_2" data-toggle="tab"> Settings </a>
                                </li>
                                <!-- <li>
                                             <a href="#tab_1_6" data-toggle="tab"> Help </a>
                                         </li> -->
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1_1">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <ul class="ver-inline-menu tabbable margin-bottom-10">
                                                <!--<ul class="list-unstyled profile-nav">-->
                                                <li>
                                                    <?php
                                                    $profile_avatar = $this->user->getProfileImage();
                                                    $profile_avatar_image = "/images/No-image-found.jpg";
                                                    if (!empty($profile_avatar) && file_exists("../_assets/profile_images/" . $profile_avatar))
                                                        $profile_avatar_image = "../_assets/profile_images/" . $profile_avatar;
                                                    ?>
                                                    <img id="profile_avatar" src="<?php echo $profile_avatar_image; ?>"
                                                         class="img-responsive pic-bordered" alt=""/>
                                                </li>
                                                <li class="active">
                                                    <a href="#tab_1_11" data-toggle="tab" aria-expanded="true"><i
                                                            class="fa fa-plug"></i> Services </a>
                                                </li>
                                                <li>
                                                    <a href="#tab_1_12" data-toggle="tab" aria-expanded="true"><i
                                                            class="fa fa-key"></i> API Keys </a>
                                                </li>
                                                <li>
                                                    <a href="#tab_1_13" data-toggle="tab" aria-expanded="true"><i
                                                            class="fa fa-key"></i> Market Places </a>
                                                </li>
                                                <li>
                                                    <a href="#tab_1_14" data-toggle="tab" aria-expanded="true"><i
                                                            class="fa fa-history"></i> Recent Activity </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-12 profile-info">
                                                    <h1 class="font-green sbold uppercase"><span
                                                            id="personal_info_full_name"><?php echo $this->user->getFirstName() . " " . $this->user->getLastName(); ?></span>
                                                    </h1>
                                                    <!--<p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt laoreet dolore magna aliquam tincidunt erat volutpat laoreet dolore magna aliquam tincidunt erat volutpat.</p>-->
                                                    <ul class="list-inline">
                                                        <li class="show_tooltip" title="Company"><i
                                                                class="fa fa-sitemap"></i>
                                                            <?php echo $this->userAccount->getCompany(); ?>
                                                        </li>
                                                        <li class="show_tooltip" title="Account Number"><i
                                                                class="fa fa-users"></i>
                                                            <?php echo $this->userAccount->getUserAccount(); ?>
                                                        </li>
                                                        <li class="show_tooltip" title="User Name"><i
                                                                class="fa fa-user"></i>
                                                            <?php echo $this->user->getUserName(); ?>
                                                        </li>
                                                        <li class="show_tooltip" title="Account Type">
                                                            <?php
                                                            $accountType = "Postpaid";
                                                            if ($this->userAccount->getIsPrepaid() == 1) {
                                                                $accountType = "Prepaid";
                                                            }
                                                            ?>
                                                            <i class="fa fa-money"></i> <?php echo $accountType; ?>
                                                        </li>
                                                        <li class="show_tooltip" title="Email"><i
                                                                class="fa fa-envelope"></i><span
                                                                id="personal_info_email"><?php echo $this->user->getEmail(); ?></span>
                                                        </li>
                                                        <li class="show_tooltip" title="Phone"><i
                                                                class="fa fa-phone"></i><span
                                                                id="personal_info_phone"><?php echo $this->user->getPhone(); ?></span>
                                                        </li>
                                                        <li class="show_tooltip" title="Country">
                                                            <?php
                                                            $countryList = new Country($this->user->getCountryId());
                                                            $userCountry = $countryList->getName();
                                                            ?>
                                                            <i class="fa fa-map-marker"></i> <?php echo $userCountry; ?>
                                                        </li>
                                                        <li class="show_tooltip" title="Warehouse">
                                                            <?php
                                                            $warehouseList = new Warehouse($this->user->getWarehouseId());
                                                            $userWareHouse = $warehouseList->getWarehouseName();
                                                            ?>
                                                            <i class="fa fa-building"></i> <?php echo $userWareHouse; ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="tabbable-line tabbable-custom-profile">
                                                    <!--                                                    <ul class="nav nav-tabs">
                                                                                                            <li class="">
                                                                                                                <a href="#tab_1_11" data-toggle="tab" aria-expanded="true">Services
                                                                                                                </a>
                                                                                                            </li>
                                                                                                            <li class="">
                                                                                                                <a href="#tab_1_12" data-toggle="tab" aria-expanded="true">
                                                                                                                    API Keys </a>
                                                                                                            </li>
                                                                                                            <li class="">
                                                                                                                <a href="#tab_1_13" data-toggle="tab" aria-expanded="true">
                                                                                                                    Market Places </a>
                                                                                                            </li>
                                                                                                            <li class="">
                                                                                                                <a href="#tab_1_14" data-toggle="tab" aria-expanded="true">
                                                                                                                    Recent Activity </a>
                                                                                                            </li>
                                                                                                        </ul>-->
                                                    <div class="tab-content padding-top-0">
                                                        <div class="tab-pane active" id="tab_1_11">
                                                            <div class="portlet-body">
                                                                <table class="table table-bordered table-advance table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th><?php echo Translation::GetCaption("CARRIERS"); ?>
                                                                        </th>
                                                                        <th><?php echo Translation::GetCaption("SERVICE_NAME"); ?>
                                                                            [Service Code]
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $servicesLists = Services::getServiceMappingWithCarrier($this->user->getUserAccountId());
                                                                    $userServices = [];
                                                                    $carrierLogo = [];
                                                                    foreach ($servicesLists as $servicesList) {
                                                                        $carrierLogo[$servicesList->getCarrierId()] = $servicesList->getDescription();
                                                                        if (isset($userServices[$servicesList->getCarrierId()])) {
                                                                            $services = $userServices[$servicesList->getCarrierId()];
                                                                            array_push($services, $servicesList->getName() . " [ <strong>" . $servicesList->getCode() . "</strong> ]");
                                                                            $userServices[$servicesList->getCarrierId()] = $services;
                                                                        } else {
                                                                            $services = [];
                                                                            array_push($services, $servicesList->getName() . " [ <strong>" . $servicesList->getCode() . "</strong> ]");
                                                                            $userServices[$servicesList->getCarrierId()] = $services;
                                                                        }
                                                                    }
                                                                    foreach ($userServices as $carrierName => $carrierServices) {
                                                                        $_carrierLogo = '<img src="/images/No-image-found.jpg" title="" alt="" height="16" />';
                                                                        if (!empty($carrierLogo[$carrierName]) && file_exists("../images/carrierlogo/thumbnail/owe_16_" . $carrierLogo[$carrierName]))
                                                                            $_carrierLogo = '<img src="/images/carrierlogo/thumbnail/owe_16_' . $carrierLogo[$carrierName] . '" title="" alt="" />';
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $_carrierLogo; ?>
                                                                                &nbsp;&nbsp;<?php echo $carrierName; ?></td>
                                                                            <td>
                                                                                <ul class="margin-bottom-0">
                                                                                    <li><?php echo implode("</li><li>", $carrierServices) ?>
                                                                                    </li>
                                                                                </ul>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                    ?>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tab_1_12">
                                                            <div class="portlet-body">
                                                                <?php
                                                                $dfApiKey = "";
                                                                $dfApiSec = "";
                                                                $defaultShopPlatForm = UserShoppingPlatforms::getShopingPlatformByPluginKey('smarttrack');
                                                                if ($defaultShopPlatForm) {
                                                                    $defaultShopPlatFormId = $defaultShopPlatForm->getId();
                                                                    $defUserApiKey = UserShoppingPlatforms::getUserApiKeyByShopingPlatFormId($defaultShopPlatFormId, $this->user->getId());
                                                                    if ($defUserApiKey) {
                                                                        $dfApiKey = $defUserApiKey->getApikey();
                                                                        $dfApiSec = $defUserApiKey->getApiSecrete();
                                                                        if (empty($dfApiKey) || empty($dfApiSec)) {
                                                                            if ($defaultShopPlatForm) {
                                                                                $defaultShopPlatFormId = $defaultShopPlatForm->getId();
                                                                                $userShoppingPlatforms = new UserShoppingPlatforms();
                                                                                $userShoppingPlatforms->setShoppingPlatformId($defaultShopPlatFormId);
                                                                                $userShoppingPlatforms->setReference('DefaultSmartTrackCred');
                                                                                $userShoppingPlatforms->setSiteUrl('');
                                                                                $userShoppingPlatforms->setUserId($user->getId());
                                                                                $dfApiKey = md5($_SESSION['admin']['id'] . $_SESSION['admin']['firstname'] . $_SESSION['admin']['surname'] . date('H:i:s'));
                                                                                $dfApiSec = md5($_SESSION['user_name'] . $_SESSION['user_type'] . $_SESSION['user_account'] . date('H:i:s'));
                                                                                $userShoppingPlatforms->setApiKey($dfApiKey);
                                                                                $userShoppingPlatforms->setApiSecrete($dfApiSec);
                                                                                $userShoppingPlatforms->setStatus(1);
                                                                                $userShoppingPlatforms->setDateCreated(time());
                                                                                $userShoppingPlatforms->save();
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <table class="table table-bordered table-advance table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>API Key</th>
                                                                        <th>API Secret</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr>
                                                                        <td><?php echo $dfApiKey; ?></td>
                                                                        <td><?php echo $dfApiSec; ?></td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tab_1_13">
                                                            <div class="portlet-body">
                                                                <div class="row">
                                                                    <input type="hidden" name="action"
                                                                           value="save_market_place"/>
                                                                    <div class="col-md-12">
                                                                        <?php
                                                                        //GET user platforms
                                                                        $UserMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
                                                                        $userAccount = $this->user->getUserAccountId();
                                                                        $UserMarketPlacesMappingFilter->addUserIdFilter($userAccount);
                                                                        //$UserMarketPlacesMappingFilter->addParentIdFilter($userAccount);
                                                                        $UserMarketPlacesMappingFilter->addJoin(' market_places mp', 'mp.id = ump.market_places_id ');
                                                                        $UserMarketPlacesMappingFilter->isActive();
                                                                        $UserData = $UserMarketPlacesMappingFilter->getList();
                                                                        $marketPlaces = new MarketPlacesAuthenticateFieldFilter();
                                                                        $marketPlaces = $marketPlaces->getList();
                                                                        $marketPlacesFieldValue = [];
                                                                        foreach ($marketPlaces as $marketPlace) {
                                                                            $marketPlacesFieldValue[$marketPlace->getFieldValue()] = $marketPlace->getFieldName();
                                                                        }
                                                                        if (count($UserData) > 0) {
                                                                            foreach ($UserData as $U) {
                                                                                $selectedShoppingPlatform[$U->getMarketPlacesId()] = [
                                                                                    'auth_data' => $U->getAuthData(),
                                                                                    'logo' => $U->getIntegrationLogo(),
                                                                                    'title' => $U->getTitle(),
                                                                                    'help_doc' => $U->getHelpDoc(),
                                                                                    'plugin_key' => $U->getPluginKey(),
                                                                                    'user_account_id' => $U->getUserAccountid(),
                                                                                    'market_places_id' => $U->getMarketPlacesId(),
                                                                                    'id' => $U->getId(),
                                                                                    'active' => $U->getActive(),
                                                                                ];
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <table
                                                                                    class="table table-striped table-bordered table-hover table-condensed">
                                                                                    <tbody>
                                                                                    <?php
                                                                                    if (count($selectedShoppingPlatform) > 0) {
                                                                                        $i = 1;
                                                                                        foreach ($selectedShoppingPlatform as $shoppingPlatfrom) { ?>
                                                                                            <tr>
                                                                                                <th colspan="2">
                                                                                                    <img width="50px"
                                                                                                         src="../images/thirdparty/<?php echo $shoppingPlatfrom['logo']; ?>" class="pull-right"><?php echo $shoppingPlatfrom['title']; ?>
                                                                                                </th>
                                                                                            </tr>
                                                                                            <?php $keys = json_decode($shoppingPlatfrom['auth_data']);
                                                                                            foreach ($keys as $key => $value):?>
                                                                                                <tr>
                                                                                                    <td width="35%">
                                                                                                        <?php echo $marketPlacesFieldValue[$key]; ?>
                                                                                                    </td>
                                                                                                    <td><?php echo $value; ?></td>
                                                                                                </tr>
                                                                                            <?php endforeach; ?>

                                                                                            <?php
                                                                                            $i++;
                                                                                        }
                                                                                    } else {
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td colspan="4">Market
                                                                                                Places not added.
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php } ?>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        <div class="tab-pane" id="tab_1_14">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <table
                                                                        class="table table-striped table-bordered table-hover table-condensed">
                                                                        <thead>
                                                                        <tr>
                                                                            <th> #</th>
                                                                            <th> Activity</th>
                                                                            <th> Date</th>
                                                                            <th> View Details</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php
                                                                        $userAuditFilter = new UserAuditFilter();
                                                                        $userAuditFilter->addFilter("ua.added_by = '" . $this->user->getId() . "' ");
                                                                        $userAuditFilter->orderBy('ua.id', 'DESC');
                                                                        $userAuditFilter->setRowsPerPage(15);
                                                                        $userAuditFilter->setOffset(0);
                                                                        $userAuditList = $userAuditFilter->getList('ua.*');
                                                                        if (count($userAuditList) > 0) {
                                                                            $i = 1;
                                                                            foreach ($userAuditList as $userAuditObj) { ?>
                                                                                <tr>
                                                                                    <td><?php echo $i; ?></td>
                                                                                    <td><?php echo $userAuditObj->getMessage(); ?>
                                                                                    </td>
                                                                                    <td><?php echo formatDateTime(date('Y-m-d H:i:s', $userAuditObj->getCreatedAt())); ?>
                                                                                    </td>
                                                                                    <td><a href=''
                                                                                           class='btn btn-xs blue btn-outline'
                                                                                           id='user_audit_single_view_btn'
                                                                                           data-target='#user_audit_single_view'
                                                                                           data-audit_id="<?php echo $userAuditObj->getId(); ?>"
                                                                                           data-toggle='modal'><span
                                                                                                class='fa fa-eye'></span>
                                                                                        </a></td>
                                                                                </tr>

                                                                                <?php
                                                                                $i++;
                                                                            }
                                                                        } else {
                                                                            ?>
                                                                            <tr>
                                                                                <td colspan="4">No Recent Activity
                                                                                    Records Found
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end row-->
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_1_2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <ul class="ver-inline-menu tabbable margin-bottom-10">
                                                <li class="active">
                                                    <a data-toggle="tab" href="#tab_1_21" aria-expanded="true"><i
                                                            class="fa fa-cog"></i> Personal info </a>
                                                </li>
                                                <li>
                                                    <a data-toggle="tab" href="#tab_1_22" aria-expanded="true"><i
                                                            class="fa fa-picture-o"></i> Change Avatar </a>
                                                </li>
                                                <li>
                                                    <a data-toggle="tab" href="#tab_1_23" aria-expanded="true"><i
                                                            class="fa fa-lock"></i> Change Password </a>
                                                </li>
                                                <?php if (Permissions::checkFilePermission('profile_paypal_setting')) { ?>
                                                    <li>
                                                        <a data-toggle="tab" href="#tab_1_24" aria-expanded="true"><i
                                                                class="fa fa-paypal"></i> Paypal Settings </a>
                                                    </li>
                                                <?php } ?>
                                                <li>
                                                    <a data-toggle="tab" href="#tab_1_25" aria-expanded="true"><i
                                                            class="fa fa-lock"></i> Market Places </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="tab-content padding-top-0">
                                                <div class="tab-pane active" id="tab_1_21">
                                                    <div class="portlet-body">
                                                        <div class="row ajax_message" id="msg_personal_info">
                                                            <div class="col-md-12">
                                                                <div class="alert"></div>
                                                            </div>
                                                        </div>
                                                        <form method="post" name="frm_personal_info"
                                                              id="frm_personal_info"
                                                              action="">
                                                            <input type="hidden" name="func"
                                                                   value="update_personal_info"/>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>First Name</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-user"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="First Name is mandatory">*</i>
                                                                                <input id="first_name" name="first_name"
                                                                                       value="<?php echo $this->userObj->getFirstName(); ?>"
                                                                                       type="text"
                                                                                       placeholder="First Name"
                                                                                       required=""
                                                                                       class="form-control tooltipbutton"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="top"
                                                                                       title="First Name"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Last Name</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-user"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Last Name is mandatory">*</i>
                                                                                <input id="last_name" name="last_name"
                                                                                       value="<?php echo $this->userObj->getLastName(); ?>"
                                                                                       type="text"
                                                                                       placeholder="Last Name"
                                                                                       required=""
                                                                                       class="form-control tooltipbutton"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="top"
                                                                                       title="Last Name"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Email Address</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-envelope"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Email is mandatory">*</i>
                                                                                <input id="email" name="email"
                                                                                       value="<?php echo $this->userObj->getEmail(); ?>"
                                                                                       type="email" placeholder="Email"
                                                                                       required=""
                                                                                       class="form-control tooltipbutton"
                                                                                       maxlength="40"
                                                                                       onblur="validateEmail(this.value);"
                                                                                       rel="tooltip"
                                                                                       data-original-title="This field will be used for update customer PODs, weight discrepancy"
                                                                                       data-placement="top"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="top"
                                                                                       title="Contact Email Address"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Phone Number</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-phone"></i> </span>
                                                                            <input id="phone" name="phone" type="text"
                                                                                   value="<?php echo $this->userObj->getPhone(); ?>"
                                                                                   placeholder="Telephone"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="Phone Number"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <?php
                                                                $address = $this->userObj->getAddress();
                                                                $address2 = $this->userObj->getAddress2();
                                                                $address3 = $this->userObj->getAddress3();
                                                                $city = $this->userObj->getCity();
                                                                $postcode = $this->userObj->getPostcode();
                                                                if (empty($address) && empty($postcode)) {
                                                                    $address = "One World Express Inc";
                                                                    $address2 = "One World House, Pump Lane";
                                                                    $address3 = "Hayes";
                                                                    $city = "London";
                                                                    $postcode = "UB3 3NB";
                                                                }
                                                                ?>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Address Line 1</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="address" name="address"
                                                                                   type="text"
                                                                                   value="<?php echo $address; ?>"
                                                                                   placeholder="Address"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="Address"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Address Line 2</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="address" name="address2"
                                                                                   type="text"
                                                                                   value="<?php echo $address2; ?>"
                                                                                   placeholder="Address"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="Address"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Address Line 3</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="address" name="address3"
                                                                                   type="text"
                                                                                   value="<?php echo $address3; ?>"
                                                                                   placeholder="Address"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="Address"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>City</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="city" name="city" type="text"
                                                                                   value="<?php echo $city; ?>"
                                                                                   placeholder="City"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="City"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Postcode</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="city" name="postcode" type="text"
                                                                                   value="<?php echo $postcode; ?>"
                                                                                   placeholder="Postcode"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="Postcode"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>State</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"><i
                                                                                    class="fa fa-map"></i> </span>
                                                                            <input id="city" name="state" type="text"
                                                                                   value="<?php echo $this->userObj->getState(); ?>"
                                                                                   placeholder="State"
                                                                                   class="form-control tooltipbutton"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top"
                                                                                   title="State"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Country</label>
                                                                        <?php echo Ddl::generateCountryDDL('country', $this->userObj->getCountryId()); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="button" id="btn_personal_info"
                                                                            class="btn btn-primary">Save Changes
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tab_1_22">
                                                    <div class="portlet-body">
                                                        <div class="row ajax_message" id="msg_change_pic">
                                                            <div class="col-md-12">
                                                                <div class="alert"></div>
                                                            </div>
                                                        </div>
                                                        <form id="frm_change_pic" name="frm_change_pic" method="post"
                                                              enctype="multipart/form-data">
                                                            <input type="hidden" name="func"
                                                                   value="update_profile_image"/>
                                                            <div class="row">
                                                                <div class="col-md-8 col-md-offset-1">
                                                                    <label for="profile_image"> Upload Profile
                                                                        Picture</label>
                                                                    <br clear="all">
                                                                    <div class="fileinput fileinput-new"
                                                                         data-provides="fileinput">
                                                                        <div class="fileinput-preview thumbnail"
                                                                             data-trigger="fileinput"
                                                                             style="width: 200px; height: 150px;">
                                                                            <?php
                                                                            if (trim($this->user->getProfileImage()) != '' && file_exists('../_assets/profile_images/' . $this->user->getProfileImage())) {
                                                                                echo '<img id="profile_img" src="/_assets/profile_images/' . $this->user->getProfileImage() . '" title="" >';
                                                                            } else {
                                                                                echo '<img id="profile_img" src = "/images/No-image-found.jpg">';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                        <div>
                                                                    <span class="btn default btn-file">
                                                                        <span class="fileinput-new"> Select image
                                                                        </span>
                                                                        <span class="fileinput-exists"> Change </span>
                                                                        <input id="profile_image" type="file"
                                                                               name="profile_image"/>
                                                                    </span>
                                                                            <a href="javascript:;"
                                                                               class="btn red fileinput-exists"
                                                                               data-dismiss="fileinput"> Remove </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="clearfix margin-top-10"><span
                                                                            class="label label-primary"><small>NOTE!</span>
                                                                        Recommended profile picture dimensions (225 x
                                                                        225) </small></div>
                                                                    <div class="form-group">
                                                                        <div class="input-group"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button id="btn_change_pic" type="button"
                                                                            class="btn btn-primary">Change Avatar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tab_1_23">
                                                    <div class="portlet-body">
                                                        <div class="row ajax_message" id="msg_update_pass">
                                                            <div class="col-md-12">
                                                                <div class="alert"></div>
                                                            </div>
                                                        </div>
                                                        <form method="post" name="frm_update_pass" id="frm_update_pass"
                                                              action="">
                                                            <input type="hidden" name="func" value="update_password"/>
                                                            <div class="row">
                                                                <div class="col-md-6 col-md-offset-1">
                                                                    <div class="form-group">
                                                                        <label>Current Password</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-key"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Password is mandatory">*</i>
                                                                                <input id="current_password"
                                                                                       name="current_password"
                                                                                       type="password"
                                                                                       value=""
                                                                                       placeholder="Current Password"
                                                                                       class="form-control" title=""/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 col-md-offset-1 password-strength"
                                                                     id="pwd-container">
                                                                    <div class="form-group">
                                                                        <label>New Password</label>
                                                                        <div class="input-group">
                                                                    <span class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Password is mandatory">*</i>
                                                                                <input id="new_password"
                                                                                       name="new_password"
                                                                                       type="password" value=""
                                                                                       placeholder="New Password"
                                                                                       class="form-control tooltipbutton"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="top"
                                                                                       title="The Pasword must be at least one upper case english letter,at least one lower case english letter,at least one special character and minimum 8 in length."/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="pwstrength_viewport_progress"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 col-md-offset-1">
                                                                    <div class="form-group">
                                                                        <label>Re-Password</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-key"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Password is mandatory">*</i>
                                                                                <input id="re_password"
                                                                                       name="re_password"
                                                                                       type="password" value=""
                                                                                       placeholder="Re Enter Password"
                                                                                       class="form-control" title=""/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button id="btn_update_pass" type="button"
                                                                            class="btn btn-primary">Change Password
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php if (Permissions::checkFilePermission('profile_paypal_setting')) { ?>
                                                    <div class="tab-pane" id="tab_1_24">
                                                        <div class="portlet-body">
                                                            <div class="row" id="ajax_message_paypal">
                                                                <div class="col-md-12">
                                                                    <div class="alert"></div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 col-md-offset-1">
                                                                    <div class="form-group">
                                                                        <label>Paypal Client Id</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-key"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Paypal Client Id is mandatory">*</i>
                                                                                <input id="paypal_client_id"
                                                                                       name="paypal_client_id"
                                                                                       type="text"
                                                                                       value="<?php echo(!empty($this->userAccount->getPaypalClientId()) ? $this->userAccount->getPaypalClientId() : ""); ?>"
                                                                                       placeholder="Paypal Client Id"
                                                                                       class="form-control" title=""/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 col-md-offset-1">
                                                                    <div class="form-group">
                                                                        <label>Paypal Client Secret</label>
                                                                        <div class="input-group"><span
                                                                                class="input-group-addon"> <i
                                                                                    class="fa fa-key"></i> </span>
                                                                            <div class="input-icon right"><i
                                                                                    class="fa tooltips font-red"
                                                                                    data-original-title="Paypal Client Secret is mandatory">*</i>
                                                                                <input id="paypal_client_secret"
                                                                                       name="paypal_client_secret"
                                                                                       type="text"
                                                                                       value="<?php echo(!empty($this->userAccount->getPaypalClientSecret()) ? $this->userAccount->getPaypalClientSecret() : ""); ?>"
                                                                                       placeholder="Paypal Client Secret"
                                                                                       class="form-control" title=""/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--                                                            <div class="col-md-6 col-md-offset-1">
                                                                <div class="form-group">
                                                                    <label>Paypal Currency</label>
                                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                                        <div class="input-icon right"><i class="fa tooltips font-red" data-original-title="Paypal Currency is mandatory">*</i>
                                                                            <?php
                                                                $paypalCurrency = 'gbp';
                                                                if (!empty($this->userAccount->getPaypalCurrency())) {
                                                                    $paypalCurrency = $this->userAccount->getPaypalCurrency();
                                                                }
                                                                $currencyArr = array('gbp' => 'GBP', 'usd' => 'USD');
                                                                echo Ddl::generateArrayDDL('paypal_currency', $currencyArr, $paypalCurrency, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Paypal Currency" placeholder="Paypal Currency"');
                                                                ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>-->
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button id="btn_update_paypal" type="button"
                                                                            class="btn btn-primary">Change Payapl
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="tab-pane" id="tab_1_25">
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="form-group" style="float:right;">
                                                                <div class="col-md-12">
                                                                    <div class="portlet-input input-inline input-small">
                                                                        <div class="input-icon right">
                                                                            <i class="icon-magnifier"></i>
                                                                            <input type="text" class="form-control input-circle" placeholder="search..."
                                                                                   name="search_marketplace" id="search_marketplace"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--<div class="row">
                                                            <div class="form-group" style="float:right;">
                                                                <div class="col-md-12">
                                                                    <label for="user_search">Search:</label>
                                                                    <input type="text" class="form-control" id="user_search">
                                                                </div>
                                                            </div>
                                                        </div>--!>
                                                        <?php
                                                        $UserMarketPlacesMappingFilter1 = new UserMarketPlacesMappingFilter();
                                                        $UserMarketPlacesMappingFilter1->addJoin(' market_places mp', 'mp.id =                                                       ump.market_places_id ');
                                                        $UserData = $UserMarketPlacesMappingFilter1->getAllList();
                                                        if (count($UserData) > 0) {
                                                            $userAccountMarketpalces = [];
                                                            foreach ($UserData as $U) {
                                                                if($U->getUserAccountId() == $this->user->getUserAccountId()) {
                                                                    $userAccountMarketpalces[$U->getMarketPlacesId()] = [
                                                                        'auth_data' => $U->getAuthData(),
                                                                        'logo' => $U->getIntegrationLogo(),
                                                                        'title' => $U->getTitle(),
                                                                        'help_doc' => $U->getHelpDoc(),
                                                                        'plugin_key' => $U->getPluginKey(),
                                                                        'user_account_id' => $U->getUserAccountid(),
                                                                        'market_places_id' => $U->getMarketPlacesId(),
                                                                        'id' => $U->getId(),
                                                                        'active' => $U->getActive(),
                                                                        'status' => $U->getStatus(),
                                                                        'expiry_date' => $U->getExpiryDate(),
                                                                        'active_date' => $U->getActiveDate(),
                                                                        'reason' => $U->getReason(),
                                                                    ];
                                                                }
                                                                $selectedShoppingPlatform[$U->getMarketPlacesId()] = [
                                                                    'auth_data' => $U->getAuthData(),
                                                                    'logo' => $U->getIntegrationLogo(),
                                                                    'title' => $U->getTitle(),
                                                                    'help_doc' => $U->getHelpDoc(),
                                                                    'plugin_key' => $U->getPluginKey(),
                                                                    'user_account_id' => $U->getUserAccountid(),
                                                                    'market_places_id' => $U->getMarketPlacesId(),
                                                                    'id' => $U->getId(),
                                                                    'active' => $U->getActive(),
                                                                    'status' => $U->getStatus(),
                                                                    'expiry_date' => $U->getExpiryDate(),
                                                                    'active_date' => $U->getActiveDate(),
                                                                    'reason' => $U->getReason(),
                                                                ];
                                                            }
                                                        }
                                                        if(count($selectedShoppingPlatform) > 0){
                                                            foreach ($selectedShoppingPlatform as $marketplaceId => $details) {
                                                                if(isset($userAccountMarketpalces[$marketplaceId])){
                                                                    $selectedShoppingPlatform[$marketplaceId] = $userAccountMarketpalces[$marketplaceId];

                                                                }

                                                            }
                                                        }
                                                        if (!empty($selectedShoppingPlatform)) { ?>
                                                            <div class="alert" id="ajax_message_market_places" style="display:none">
                                                            </div>
                                                            <div class="row cards">
                                                                <?php if (!empty($selectedShoppingPlatform)) {
                                                                    $subscriber = new UserMarketPlacesMappingFilter();
                                                                    foreach ($selectedShoppingPlatform as $marketplaceId => $shoppingArray) {
                                                                        if (empty($shoppingArray['logo']) || !file_exists("../images/thirdparty/" . $shoppingArray['logo'])) {
                                                                            $shoppingArray['logo'] = 'No-image-found.png';
                                                                        }
                                                                        ?>
                                                                        <div class="col-md-4 search_carrier_custom"
                                                                             data-name="<?php echo $shoppingArray['title']; ?>">
                                                                            <form method="post"
                                                                                  class="market_platform_setting_form">
                                                                                <input type="hidden" name="action"
                                                                                       value="save_market_platform_keys">
                                                                                <div class="card">
                                                                                    <div class="card-inner-box">
                                                                                        <center><img
                                                                                                src="../images/thirdparty/<?php echo $shoppingArray['logo'] ?>"
                                                                                                class="img-responsive"
                                                                                                alt="Amazon">
                                                                                        </center>
                                                                                    </div>
                        <?php
                        $uaid = $this->userAccount->getId();
                        $mpid = $shoppingArray['market_places_id'];
                        $mpmid = $shoppingArray['id'];
                        $todayDate = time();
                        $activeDate = $shoppingArray['active_date'];
                        $expiryDate = $shoppingArray['expiry_date'];
                        if (($todayDate >= $activeDate) && ($todayDate <= $expiryDate)){
                            //is between
                        }else{
                            // not go
                            $subscriber->autoInactive($mpmid,$uaid);
                        }
                        if(isset($userAccountMarketpalces[$marketplaceId])){
                            $active_status = $userAccountMarketpalces[$marketplaceId]['active'];
                            $id = $userAccountMarketpalces[$marketplaceId]['id'];
                        }
                        $currentStatus = (!empty($shoppingArray['status']) ? $shoppingArray['status'] : "h");
                        $statusArr = ['pending'=>'Request Pending','approved'=>'Unsubscribe','rejected'=>'Rejected','expired'=>'Resubscribe','h'=>'Subscribe'];
                        $status = $statusArr[$currentStatus];
                        $btnColor = "btn-primary";
                        if($currentStatus == "rejected"){
                            $btnColor = "btn-danger";
                        }
                        ?>
                                                                                    <div class="card-title">
                                                                        <?php if($currentStatus != "rejected"){ ?>
                                                                                        <a href="#"
                                                                                           id="toggleinfo_<?= $id; ?>"
                                                                                           active_status="<?= $active_status; ?>"
                                                                                           class="toggle-info btn">
                                                                                            <span class="left"></span>
                                                                                            <span class="right"></span>
                                                                                        </a>
                                                                            <?php } ?>
                                                                                        <a href="marketplace_help_doc.php?mp=<?php echo $shoppingArray['plugin_key']; ?>"
                                                                                           class="toggle-info2 btn grey"
                                                                                           data-original-title=""
                                                                                           title="">
                                                                                            <i class="fa fa-info"></i>
                                                                                        </a>
                                                                                        <?php if($currentStatus == "rejected"){ ?>
                                                                                            <div class="btn-group btn-group-solid pull-right">
                                                                                                <button type="button" class="show_tooltip btn btn-circle red" title="<?php echo $shoppingArray['reason']; ?>"><i class="fa fa-book"></i></button>
                                                                                            </div>
                                                                                        <?php } ?>
                                                                                        <h3 style="vertical-align: middle; line-height: 30px;">
                                                                                            <?php echo $shoppingArray['title'] ?>
                                                                                            <small></small>
                                                                                        </h3>
                                                                                        <!--                                    <label class="btn btn-default">-->
                                                                                        <?php
                                                       $sql = "SELECT id from user_market_places_mapping where market_places_id = '$mpid'
                                                                and user_account_id = '$uaid'";
                                                                $result = DbAccess3::runQuery($sql);
                                                                $row = mysqli_fetch_assoc($result);
                                                                $id = $row["id"];

                                                                                        ?>
            <div class="col text-center">
                <button  mpid="<?= $shoppingArray['market_places_id']; ?>"  class="btn <?php echo $btnColor; ?> btn-sm btn-subscribe"
                                  id="<?php echo $id; ?>" umps_id = "<?= $val['2']; ?>"><?= $status ?></button>
            </div>
                                                                                    </div>
                                                                                    <div class="card-flap flap1">
                                                                                        <div class="card-description">
                                                                                            <?php
                                                                                            $authDataArray = array();
                                                                                            if (isset($shoppingArray['auth_data'])) {
                                                                                                $auth_data = $shoppingArray['auth_data'];
                                                                                                $authDataArray = (array)json_decode($auth_data);
                                                                                            }
                                                                                            foreach ($authDataArray as $key => $value) {
                                                                                                if (!empty($key)) {
                                                                                                    $tmpVal = $value['AuthenticateValue'];
                                                                                                    $name_index = "shopping_plateform[" . $value['PlatformId'] . "][" . $tmpVal . "]";
                                                                                                    $auth_value = isset($authDataArray[$tmpVal]) ? $authDataArray[$tmpVal] : '';
                                                                                                    ?>
                                                                                                    <div class="form-group ">
                                                                                                        <div
                                                                                                            class="has-float-label input-icon right">

                                                                                                            <input
                                                                                                                name="shopping_plateform[<?php echo $shoppingArray['id']; ?>][<?php echo $key; ?>]"
                                                                                                                type="text"
                                                                                                                value="<?php echo $value; ?>"
                                                                                                                placeholder="<?php echo $marketPlacesFieldValue[$key]; ?>"
                                                                                                                class="form-control tooltipbutton shopping_plateform_input shopping_plateform_<?php echo $shoppingArray['id']; ?>"
                                                                                                                data-original-title=""
                                                                                                                data-toggle="tooltip"
                                                                                                                data-placement="top"
                                                                                                                title="">
                                                                                                            <label for="from_weight"
                                                                                                                   class="label-account"><?php echo $marketPlacesFieldValue[$key]; ?></label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <?php
                                                                                                }
                                                                                            } ?>
                                                                                            <div class="form-group text-right">
                                                                                                <button name="update_btn" id=""
                                                                                                        type="button"
                                                                                                        class="btn btn-primary btn_update_market_places_keys">
                                                                                                    Update
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <div>
                                                                        No Market places found.
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>

                                                        <?php } else {
                                                            ?>
                                                            <div>
                                                                No Market places found.
                                                            </div>

                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--tab_1_2-->
                            <!--end tab-pane-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE BASE CONTENT -->
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
