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
                'index.php' => Translation::GetCaption("HOME"),
                'list_users.php' => Translation::GetCaption("USERS"),
                "Manage User"
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
                                $user->save();
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
            } else {
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
        protected function renderFooter() {
            ?>
            <script type="text/javascript">
      /*!
     * jQuery FancyZoom Plugin
     * version: 1.0.1 (20-APR-2014)
     * @requires jQuery v1.6.2 or later
     *
     * Examples and documentation at: http://github.com/keegnotrub/jquery.fancyzoom/
     * Licensed under the MIT license:
     *   http://www.opensource.org/licenses/mit-license.php
     */
    !function(a){a.extend(jQuery.easing,{easeInOutCubic:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b+c:d/2*((b-=2)*b*b+2)+c}}),a.fn.fancyZoom=function(b){function e(b){b.childNodes.length>0&&(b=b.childNodes[0]);var c=a(b),d=c.offset().left,e=c.offset().top,f=c.width()||50,g=c.height()||12;return{left:d,top:e,width:f,height:g}}function f(){var b=a(window),c=b.width(),d=b.height(),e=b.scrollLeft(),f=b.scrollTop();return{width:c,height:d,scrollX:e,scrollY:f}}function g(b){function r(a){g?(p.css("backgroundPosition","0px "+-50*j+"px"),j=(j+1)%12):(clearInterval(i),i=0,j=0,p.hide(),t(a))}function s(a){p.css({left:l.width/2+l.scrollX+"px",top:l.height/2+l.scrollY+"px",backgroundPosition:"0px 0px",display:"block"}),j=0,i=setInterval(function(){r(a)},100)}function t(b){if(d)return!1;d=!0,n.attr("src",b.getAttribute("href"));var e=h.width,f=h.height,g=e/f;e>l.width-c.minBorder&&(e=l.width-c.minBorder,f=e/g),f>l.height-c.minBorder&&(f=l.height-c.minBorder,e=f*g);var i=l.height/2-f/2+l.scrollY,j=l.width/2-e/2+l.scrollX;o.hide(),m.hide().css({left:k.left+"px",top:k.top+"px",width:k.width+"px",height:k.height+"px",opacity:"hide"}),m.animate({left:j+"px",top:i+"px",width:e+"px",height:f+"px",opacity:"show"},200,"easeInOutCubic",function(){o.fadeIn(),o.click(u),m.click(u),a(document).keyup(v),d=!1})}function u(){return d?!1:(d=!0,o.hide(),m.animate({left:k.left+"px",top:k.top+"px",opacity:"hide",width:k.width+"px",height:k.height+"px"},200,"easeInOutCubic",function(){d=!1}),m.unbind("click",u),o.unbind("click",u),a(document).unbind("keyup",v),void 0)}function v(a){27==a.keyCode&&u()}var k,l,m,n,o,p,c=b,d=!1,g=!1,h=new Image,i=0,j=0,q=this;m=a("#zoom"),0===m.length&&(m=a(document.createElement("div")),m.attr("id","zoom"),a("body").append(m)),n=a("#zoom_img"),0===n.length&&(n=a(document.createElement("img")),n.attr("id","zoom_img"),m.append(n)),o=a("#zoom_close"),0===o.length&&(o=a(document.createElement("div")),o.attr("id","zoom_close"),m.append(o)),p=a("#zoom_spin"),0===p.length&&(p=a(document.createElement("div")),p.attr("id","zoom_spin"),a("body").append(p)),this.preload=function(){var c=this.getAttribute("href");h.src!==c&&(g=!0,h=new Image,a(h).load(function(){g=!1}),h.src=c)},this.show=function(a){l=f(),k=e(this),q.preload.call(this,a),g?0===i&&s(this):t(this),a.preventDefault()}}var c=a.extend({minBorder:90},b),d=new g(c);this.each(function(){var c=a(this);c.mouseover(d.preload),c.click(d.show)})}}(jQuery);
    $(document).ready(function() {
    $("a").fancyZoom();
    }); 
    </script>
          <style>
                    .page-breadcrumb {
                        display: none
                    }
                    @media (min-width: 992px) {
                        .page-content-wrapper .page-content {
                            padding: 0px 0 0 20px;
                        }
                    }
                    </style>
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
    <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="portlet light">
            <div class="portlet-title"  style="margin-bottom: 0">
                <div class="caption"> <i class="fa fa-question-circle"></i>
                    Help
                </div>
                <div class="actions">
                    <a href="" id="backLink" class="btn blue"><span></span><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back</a>
    <!--                <a href="javascript:;" class="btn btn-primary show_audit" title="audit"> WooCommerce</a>-->
                </div>
            </div>
                 <div class="row mb-4" style="margin-left: -20px; margin-right: -20px">
                    <img src="../images/bg-woo-help.png" class="img-responsive" alt="WooCommerce" style="width:100%;" >

                    <div class="help-sub-heading">
                        <h3>
                       <center>Step by step guide to connect your woocommerce store to smarttrack</center>
                   </h3>
</div>

               </div>
            <div class="portlet-body">
                    <div class="container1">
    <!-- 
     -->
                        <div class="row1">
                            <div class="col-md-4">
                                <!-- begin panel group -->
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <!-- panel 1 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab1" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingOne" data-toggle="collapse"
                                                data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                <h4 class="panel-title">Step 1 - Download Plugin</h4>
                                            </div>
                                        </span>
                                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                            aria-labelledby="headingOne">
                                            <div class="panel-body">
												Download The Woo Commerce Blank Plugin by clicking the link <a class="" target="_blank" href="../plugins/blank_WooCommerce_plugin.zip"><u>Download Plugin</u></a><br />.
											Install in WordPress Plugins, Activate and click on Settings
                                            </div>
                                        </div>
                                    </div>
                                    <!-- / panel 1 -->
                                    <!-- panel 2 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab2" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingTwo" data-toggle="collapse"
                                                data-parent="#accordion" href="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                                <h4 class="panel-title collapsed">Step 2 - Connect Plugin</h4>
                                            </div>
                                        </span>
                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingTwo">
                                            <div class="panel-body">
                                                <!-- Tab content goes here -->
                                                Click on connect.
                                            </div>
                                        </div>
                                    </div>
                                    <!-- / panel 2 -->
                                    <!--  panel 3 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab3" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingThree" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                <h4 class="panel-title">Step 3 - Configer in Smarttrack </h4>
                                            </div>
                                        </span>
                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingThree">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                1. Go to smarttrack and login.<br />
                                                2. Go to My Profile and click on tab settings<br />
                                                3. Click on marketplaces.<br />
                                            </div>
                                        </div>
                                    </div>
                                    <!--  panel 4 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab4" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingFour" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseFour"
                                                aria-expanded="false" aria-controls="collapseFour">
                                                <h4 class="panel-title">Step 4 - Open Settings
                                                </h4>
                                            </div>
                                        </span>
                                        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingFour">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                1. Click on Settings <br />
                                                2. Click on Market Places<br />
                                            </div>
                                        </div>
                                    </div>
                                    <!--  panel 5 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab5" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingFive" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseFive"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                <h4 class="panel-title">Step 5 - Fetch Orders
                                                </h4>
                                            </div>
                                        </span>
                                        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingFive">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                Click on marketplaces and select your marketplace which order you want
                                                to fetch and click on fetch order.
                                            </div>
                                        </div>
                                    </div>
                                    <!--  panel 6 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab6" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingSix" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseSix"
                                                aria-expanded="false" aria-controls="collapseSix">
                                                <h4 class="panel-title">Step 6 - Fetch Order
                                                </h4>
                                            </div>
                                        </span>
                                        <div id="collapseSix" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingSix">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                After click on Fetch Order Click on Generate label
                                            </div>
                                        </div>
                                    </div>
                                    <!--  panel 7 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab7" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingSeven" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseSeven"
                                                aria-expanded="false" aria-controls="collapseSeven">
                                                <h4 class="panel-title">Step 7 - Generate Label
                                                </h4>
                                            </div>
                                        </span>
                                        <div id="collapseSeven" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingSeven">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                1. Select Services <br />
                                                2. Chose Weight and Number of Pieces <br />
                                                3. Click on Generate Lable<br />
                                            </div>
                                        </div>
                                    </div>
                                    <!--  panel 8 -->
                                    <div class="panel panel-default">
                                        <!--wrap panel heading in span to trigger image change as well as collapse -->
                                        <span class="side-tab" data-target="#tab8" data-toggle="tab" role="tab"
                                            aria-expanded="false">
                                            <div class="panel-heading" role="tab" id="headingEight" class="collapsed"
                                                data-toggle="collapse" data-parent="#accordion" href="#collapseEight"
                                                aria-expanded="false" aria-controls="collapseEight">
                                                <h4 class="panel-title">Step 8 - Find Tracking Number
                                                </h4>
                                            </div>
                                        </span>
                                        <div id="collapseEight" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingEight">
                                            <div class="panel-body">
                                                <!-- tab content goes here -->
                                                Label will be generated, and you will find the tracking number.
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- / panel-group -->
                            </div> <!-- /col-md-4 -->
                            <div class="col-md-8">
                                <!-- begin macbook pro mockup -->
                                <div class="md-macbook-pro md-glare">
                                    <div class="md-lid">
                                        <div class="md-camera"></div>
                                        <div class="md-screen">
                                            <!-- content goes here -->
                                            <div class="tab-featured-image">
                                                <div class="tab-content">
                                                    <div class="tab-pane  in active" id="tab1">
                                          <a href="images/setp-1.png"><img src="images/setp-1.png" /></a>
                                                    </div>
                                                    <div class="tab-pane " id="tab2">
                                               <a href="images/setp-2.png"><img src="images/setp-2.png" /></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab3">
                                             <a href="images/setp-3.png"><img src="images/setp-3.png" ></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab4">
                                                <a href="images/setp-4.png"><img src="images/setp-4.png" alt="tab1"></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab5">
                                             <a href="images/setp-5.png"><img src="images/setp-5.png" alt="tab1"></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab6">
                                                      <a href="images/setp-6.png"><img src="images/setp-6.png" alt="tab1"></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab7">
                                                      <a href="images/setp-7.png"><img src="images/setp-7.png" alt="tab1"></a>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab8">
                                                       <a href="images/setp-8.png"><img src="images/setp-8.png" alt="tab1"></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="md-base"></div>
                                </div> <!-- end macbook pro mockup -->
                            </div> <!-- / .col-md-8 -->
                        </div>
                        <!--/ .row -->
                    </div> <!-- end sidetab container -->
    <br clear="all">
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
