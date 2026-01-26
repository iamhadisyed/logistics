<?php

/**
 * User Object
 *
 */
class User extends DbAccess3
{

    /**
     * Construct
     *
     * @param id/array
     */
    // user types (if change types, update type list function).
    const USER_TYPE_CLIENT = "client";
    const USER_TYPE_CORPORATE_CLIENT = "corporateclient";
    const USER_TYPE_WAREHOUSE = "warehouse";
    const USER_TYPE_MANAGER = "manager";
    const USER_TYPE_ADMIN = "admin";
    const USER_TYPE_CORPORATECLIENT = "corporateclient";
    const USER_TYPE_CORPORATE = "corporate";
    const USER_TYPE_WAREHOUSE_ALPHA = "warehousealpha";
    const USER_SERVICE_ROUTING = "ROUTING";
    const USER_SERVICE_BOTH = "BOTH";
    const USER_SERVICE_CHOICE = "CHOICE";
    const USER_TYPE_ACCOUNT = "account";
    const USER_TYPE_CUSTOMER_SERVICE = "customerservice";
    const USER_TYPE_FINANCE = "finance";
    const USER_TYPE_SALES = "sales";
    // Privileges
    const PRIVILEGE_CLIENT = 1;
    const PRIVILEGE_IMPORT = 2;
    const PRIVILEGE_WAREHOUSE_LIST = 4;
    const PRIVILEGE_EXPORT = 8;
    const PRIVILEGE_USER_LIST = 16;
    const PRIVILEGE_END_OF_DAY = 32;
    const PRIVILEGE_ADDUSER = 64;
    const PRIVILEGE_SERVICE_LIST = 128;
    const PRIVILEGE_TRACKING_ENTRY = 256;

    const USER_ROLES = array('client' => 'General User', 'corporate' => 'Company', 'admin' => 'Super Admin', 'driver' => 'Driver', 'sales_agent' => 'Sales Agent');//;array('client' => 'General User', 'corporate' => 'Company', 'admin' => 'Super Admin');
    const USER_DASHBOARD_ROLES = array('corporate' => 'Company', 'operation' => 'Operation', 'customer_service' => 'Customer Service', 'account' => 'Accounts', 'driver' => 'Driver');
    const USER_ROLES_CORPORATE = array('client' => 'General User', 'corporate' => 'Company', 'sales_agent' => 'Sales Agent');
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'user_type' => 'string',
            'user_name' => 'string',
            'user_pass' => 'string',
            'active_flag' => 'bit',
            'first_name' => 'string',
            'last_name' => 'string',
            'address' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'country_id' => 'number',
            'api_key' => 'string',
            'api_secert' => 'string',
            'api_date' => 'datetime',
            'address_2' => 'string',
            'address_3' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postcode' => 'string',
            'profile_image' => 'string',
            'invalid_login_count' => 'number',
            'user_account_id' => 'number',
            'archive_server' => 'number',
            'last_login_date' => 'datetime',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'number',
            'is_employee' => 'bit',
            'warehouse_id' => 'number',
            'carrier_setup_agreement' => 'number',
            'receive_email' => 'string',
            'tc_agreed_date'	=> 'datetime',
            'is_tc_agreed'	=> 'string',
            'dashboard' => ['enum' => ['corporate','operation','customer_service','account', 'driver', 'sales_agent']],
            'commission_break_event_amount'	=> 'string',
            'is_sale_pot_eligible'	=> 'bit',

            'account_full_name' => 'undefined',
            'account_email' => 'undefined',
            'account_phone' => 'undefined',
            'account_country_id' => 'undefined',
            'user_account' => 'undefined',
            'company' => 'undefined',
            'return_address' => 'undefined',
            'sms_dpd' => 'undefined',
            'user_service_type' => 'undefined',
            'parentid' => 'undefined',
            'logo' => 'undefined',
            'instant_label' => 'undefined',
            'tracking_api_access' => 'undefined',
            'import_data_csv' => 'undefined',
            'proforma' => 'undefined',
            'add_tracking' => 'undefined',
            'collection' => 'undefined',
            'default_description' => 'undefined',
            'default_notes' => 'undefined',
            'default_weight' => 'undefined',
            'vat_undefined' => 'undefined',
            'billing_currency' => 'undefined',
            'vat_chargable' => 'undefined',
            'vat_value' => 'undefined',
            'allow_remote_area' => 'undefined',
            'telephone' => 'undefined',
            'alternative_email' => 'undefined',
            'billing_address' => 'undefined',
            'date_dispatch' => 'undefined',
            'is_product' => 'undefined',
            'send_courier_data' => 'undefined',
            'credit_check' => 'undefined',
            'tariff_agreed' => 'undefined',
            'sales_person' => 'undefined',
            'scan_document' => 'undefined',
            'data_entry' => 'undefined',
            'bank_account_title' => 'undefined',
            'bank_sortcode' => 'undefined',
            'bank_account_undefined' => 'undefined',
            'bank_branch_address' => 'undefined',
            'trade_name_i' => 'undefined',
            'trade_address_i' => 'undefined',
            'trade_name_ii' => 'undefined',
            'trade_address_ii' => 'undefined',
            'trade_email_i' => 'undefined',
            'trade_email_ii' => 'undefined',
            'trade_phone_i' => 'undefined',
            'trade_phone_ii' => 'undefined',
            'reg_undefined' => 'undefined',
            'reg_address' => 'undefined',
            'reg_postcode' => 'undefined',
            'reg_country' => 'undefined',
            'sale_agent' => 'undefined',
            'sale_date' => 'undefined',
            'fuel_charges' => 'undefined',
            'user_signature' => 'undefined',
            'billing_email' => 'undefined',
            'is_fuelcharges_include' => 'undefined',
            'is_prepaid' => 'undefined',
            'return_label' => 'undefined',
            'finalmile_over_label' => 'undefined',
            'request_manifest_collection' => 'undefined',
            'create_pre_alert' => 'undefined',
            'account_display_invoices' => 'undefined',
            'check_list_account_form' => 'undefined',
            'check_list_credit_check' => 'undefined',
            'check_list_t_cs' => 'undefined',
            'check_list_tariff_agreed' => 'undefined',
            'check_list_sales_pot' => 'undefined',
            'sales_pot_time_period' => 'undefined',
            'sales_pot_percentage' => 'undefined',
            'invalid_login_count' => 'undefined',
            'token' => 'undefined',
            'token_updated' => 'undefined',
            'opearation_manifest' => 'undefined',
            'own_tariff' => 'undefined',
            'user_warehouse' => 'undefined',
            'sales_rate' => 'undefined',
            'tariff_values' => 'undefined',
            'product_names' => 'undefined',
            'collection_add_line_1' => 'undefined',
            'collection_add_line_2' => 'undefined',
            'collection_add_line_3' => 'undefined',
            'collection_city' => 'undefined',
            'collection_country' => 'undefined',
            'collection_postcode' => 'undefined',
            'theme_id' => 'undefined',
            'bagging' => 'undefined',
            'show_price' => 'undefined',
            'retail_customer' => 'undefined',
            'default_lang' => 'undefined',
            'user_code' => 'undefined',
            'website_link' => 'undefined',
            'credit_limit' => 'undefined',
            'invoice_period' => 'undefined',
            'paypal_email' => 'undefined',
            'paypal_currency' => 'undefined',
            'user_count'	=> 'undefined',
            'expires'	=> 'undefined',
            'client_id'	=> 'undefined',
            'access_token'	=> 'undefined'

        );
        parent::__construct("user", 'id', $fieldList, $mixedCreator);
    }


    

    /*     * *
     * Gives array of user types
     */

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * Search for user/password combo
     * @param $username , $password
     * @return bool
     */
    public static function getUser($username, $password, $apiUser = 'NO', $platform = '')
    {
        $useApiKey = false;
        $user_array = array();
        $loginStatus = 0;
        $userId = '0';
        $filter = new UserFilter;
        $filter->addFilter(" u.user_name = '" . DbAccess3::escape($username) . "'");
        $filter->addFilter(" u.active_flag = 1");
        $filter->addFilter(" ua.active_flag = 1");
        $filter->addUserAccountJoin();
        $user_array = $filter->getColumnList('u.id, u.user_name,u.last_login_date, u.first_name, u.last_name, u.user_type, u.active_flag, u.user_account_id, u.archive_server, u.invalid_login_count, u.user_pass,ua.parentid');
//		$user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account_id, archive_server, parentid, invalid_login_count, user_pass');
        if (sizeof($user_array) <= 0 && $apiUser == 'YES') {
            if ($platform != "") {
                $shoppingPlatformFilter = new ShoppingPlatformFilter();
                $shoppingPlatformFilter->addPluginKeyFilter($platform);
                $shoppingPlatformArr = $shoppingPlatformFilter->getColumnList('id, title');
                if (count($shoppingPlatformArr) > 0) {
                    $shoppingPlatform = $shoppingPlatformArr[0];
                    $shoppingPlatformId = $shoppingPlatform->getId();
                    $userPlatformsFilter = new UserShoppingPlatformsFilter();
                    $userPlatformsFilter->addShoppingPlatformIdFilter($shoppingPlatformId);
                    $userPlatformsFilter->addApiKeyFilter($username);
                    $userPlatformsFilter->addApiSecreteFilter($password);
                    $userPlatformsFilter->addStatusFilter('1');
                    $userPlatformsArr = $userPlatformsFilter->getColumnList('id, user_id, site_url');
                    if (count($userPlatformsArr) > 0) {
                        $userPlatform = $userPlatformsArr[0];
                        $userId = $userPlatform->getUserId();
                        $filter = new UserFilter;
                        $filter->addIdFilter($userId);
                        $user_array = $filter->getColumnList('id, last_login_date,user_name, first_name, last_name,user_type, active_flag, user_account, archive_server, parentid, invalid_login_count, user_pass, last_login_date');
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $filter = new UserFilter;
                $filter->addUserAccountJoin();
                $filter->addFilter(" api_key = '" . DbAccess3::escape($username) . "' AND api_secert = '" . DbAccess3::escape($password) . "'");
                $user_array = $filter->getColumnList('u.id, u.last_login_date, u.user_name, u.last_name, u.user_type, u.user_type, u.active_flag, u.user_account, u.archive_server, u.invalid_login_count, u.user_pass,ua.user_account,ua.parentid');
//						$user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, invalid_login_count, user_pass');
                if (count($user_array) <= 0) {
                    return false;
                }
            }
            $useApiKey = true;
        }

        if (sizeof($user_array) > 0) {
            if ($useApiKey === false && !password_verify(DbAccess3::escape($password), $user_array[0]->getUserPass())) {
				//$invalidCount	=	(int)$user_array[0]->getInvalidLoginCount()+1;
				//$user_array[0]->setInvalidLoginCount($invalidCount);
				//$user_array[0]->save();
				$loginStatus = 0;
			} else {
				$parentAccount = CustomerAccount::accountParentAccount($user_array[0]->getUserAccountId());
				$checkOfActiveAccount = 0;
				if(count($parentAccount) > 1) {
					foreach($parentAccount as $pa) {
						if($pa->getId() != $user_array[0]->getUserAccountId()) {
							$checkAccount = new UserAccountFilter();
							$checkAccount->addFieldFilter("ua.active_flag", 0);
							$checkAccount->addFieldFilter("ua.id", $pa->getId());
							$checkAccountCount = $checkAccount->getCount();
							if($checkAccountCount > 0) {
								$checkOfActiveAccount = 1;
								break;
							}
						}
					}
				}
				if($checkOfActiveAccount == 1) {
					$loginStatus = 0;
				} else {
					//Check T&C check
					$userFilter = new UserFilter();
					$userList = $userFilter->getTcUser($user_array[0]->getId());
					if(count($userList) > 0) {
						$_SESSION["is_tc_agreed"] = "n";   // Set user t&c check
					}else{
						$_SESSION["is_tc_agreed"] = "y";   // Set user t&c check
					}
					$userId = $user_array[0]->getId();
					$_SESSION["user_id"] = $user_array[0]->getId();   // displayed on user pages
					$_SESSION["user_name"] = $user_array[0]->getUserName();   // displayed on user pages
					$_SESSION["user_type"] = $user_array[0]->getUserType();   // used to guard access to user pages
					$_SESSION["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
					$_SESSION["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
					//if(trim($user_array[0]->getUserType()) == User::USER_TYPE_ADMIN || trim($user_array[0]->getUserType()) == User::USER_TYPE_ACCOUNT)
//				{
					$_SESSION['admin']['id'] = $user_array[0]->getId();
					$_SESSION['admin']['firstname'] = $user_array[0]->getFirstName();
					$_SESSION['admin']['surname'] = $user_array[0]->getUserName();
					$_SESSION['admin']["user_name"] = $user_array[0]->getUserName();   // displayed on user pages
					$_SESSION['admin']["user_type"] = $user_array[0]->getUserType();   // used to guard access to user pages
					$_SESSION['admin']["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
					$_SESSION['admin']["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
//				}
					$user_array[0]->setInvalidLoginCount(0);
					$eventOldArray = date('Y-m-d H:i:s', $user_array[0]->getLastLoginDate());
					$user_array[0]->setLastLoginDate(time());
					$eventNewArray =  $user_array[0]->getLastLoginDate();

					$user_array[0]->eventKey = 'loggedIn';
					$user_array[0]->eventNewArray['last_login_date'] = date('Y-m-d H:i:s', $eventNewArray);
					$user_array[0]->save();
					$loginStatus = 1;

				}
			}
        }
        User::runQuery("	INSERT INTO `login_request`( `user_id`, `user_name`, `ip_address`, `user_agent`, `login_status`,session_id)
VALUES ( '" . $userId . "', '" . DbAccess3::escape($username) . "','" . User::get_client_ip() . "','" . $_SERVER['HTTP_USER_AGENT'] . "','" . $loginStatus . "','" . session_id() . "')");


        if ($loginStatus == '1') {

            return true;
        } else        // none found
        {
            return false;
        }
    }

// Get user routing

    /**
     * Logout
     */
    public static function logout()
    {
        $UserSessionManager = SessionManager::getUser();
        $UserSessionManager->getUserAccountId();

        $useraccountData =   new CustomerAccount($UserSessionManager->getUserAccountId());
        $loginpage  =   "/login.php";
        if($useraccountData->getThemeId() > 0) {
            $theme = new Themes($useraccountData->getThemeId());
            $loginpage  =   "/login-".$theme->getSlug().".php";
        }
//        switch($useraccountData->getThemeId())
//        {
//            case DEUTSHCEPOST:
//                $loginpage  =   "/login-deutchepost.php";
//                break;
//            case UKMAILTHEME:
//                $loginpage  =   "/login-ukmail.php";
//                break;
//            case VIVATHEME:
//                $loginpage  =   "/login-viva.php";
//                break;
//            case SPARTHEME:
//                $loginpage  =   "/login-spar.php";
//                break;
//            case RTCLEARTHEME:
//                $loginpage  =   "/login-rt.php";
//                break;
//
//            default:
//                $loginpage  =   "/login.php";
//                break;
//        }
        $userObj = new User($UserSessionManager->getId());
        $userObj->logMoreDataNew['logged_out'] = formatDateTime(date('Y-m-d h:i:s', time()));
        $userObj->logMoreDataOld['logged_out'] = '';
        $userObj->custom_message = $UserSessionManager->getFirstName() . ' ' . $UserSessionManager->getLastName() . ' has logged out.';
        $userObj->tableKey = $UserSessionManager->getId();
        $userObj->saveAuditData();
        self::logUserOut();
        util_redirect($loginpage);
    }

    public static function logUserOut()
    {
        //	User::runQuery(" UPDATE login_request SET logout_time = now() where user_id = '".$_SESSION["user_id"]."' and session_id = '".session_id()."' and logout_time is null ");

        $_SESSION["user_name"] = null;
        $_SESSION["user_type"] = null;
        $_SESSION["user_account"] = null;
        $_SESSION["detail"] = array();
        $_SESSION["orders"] = array();
        $_SESSION["filter"] = array();

        unset($_SESSION["detail"]);
        unset($_SESSION['orders']);
        unset($_SESSION['filter']);
        unset($_SESSION['admin']);
        // remove all session variables
        DbAccess3::closeConnection();
        session_unset();
    }

    

    public static function getUserAccountList($userAccount = "", $parentidArray = array(), $account_typeArray = array(), $valueUserCode = true)
    {

        $where = "active_flag = 1";
        if (sizeof($parentidArray) > 0 && $parentidArray != '')
            $where .= " AND parentid in ('" . implode("','", $parentidArray) . "')";
        if (sizeof($account_typeArray) > 0 && $account_typeArray != '')
            $where .= " AND user_type in ('" . implode("','", $account_typeArray) . "')";


        $sql = "SELECT user_account, user_name, user_code, id FROM user where " . $where . " order by user_account asc ";

        $account_list = array();

        $rs = self::runQuery($sql);


        //
        $option = "";
        while ($valArray = mysqli_fetch_assoc($rs)) {
            if ($valueUserCode == false)
                $userAccountCOde = $valArray["user_account"];
            else
                $userAccountCOde = $valArray["user_code"];

            if ($valArray["user_account"] == "")
                continue;
            $selected = ($userAccount == $userAccountCOde) ? " selected" : "";
            $option .= "<option " . $selected . " value='" . $userAccountCOde . "'>" . $valArray["user_account"] . " (" . $valArray["user_name"] . ") </option>";
        }

        return $option;
    }

    public static function getTotalNumberOfUsersFromSql($sql)
    {

        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

 
    public function hasPrivilege($privilege)
    {
        $access = 0;
        switch ($this->getUserType()) {
            case self::USER_TYPE_ADMIN:
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_EXPORT + self::PRIVILEGE_IMPORT + self::PRIVILEGE_WAREHOUSE_LIST + self::PRIVILEGE_SERVICE_LIST + self::PRIVILEGE_USER_LIST + self::PRIVILEGE_END_OF_DAY + self::PRIVILEGE_ADDUSER;
                break;
            case (self::USER_TYPE_ACCOUNT || self::USER_TYPE_CUSTOMER_SERVICE || self::USER_TYPE_FINANCE):
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_EXPORT + self::PRIVILEGE_IMPORT + self::PRIVILEGE_WAREHOUSE_LIST + self::PRIVILEGE_SERVICE_LIST + self::PRIVILEGE_USER_LIST + self::PRIVILEGE_END_OF_DAY + self::PRIVILEGE_ADDUSER;
                break;

            case self::USER_TYPE_MANAGER:
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_EXPORT + self::PRIVILEGE_IMPORT + self::PRIVILEGE_WAREHOUSE_LIST + self::PRIVILEGE_END_OF_DAY;
                break;
            case self::USER_TYPE_WAREHOUSE:
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_IMPORT + self::PRIVILEGE_WAREHOUSE_LIST + self::PRIVILEGE_ADDUSER;
                break;
            case self::USER_TYPE_CLIENT:
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_IMPORT;
                break;
            case self::USER_TYPE_CORPORATE_CLIENT:
                $access = self::PRIVILEGE_CLIENT + self::PRIVILEGE_IMPORT + self::PRIVILEGE_ADDUSER + self::PRIVILEGE_USER_LIST;
                break;
            case self::USER_TYPE_WAREHOUSE_ALPHA:
                $access = self::PRIVILEGE_TRACKING_ENTRY;
                break;
        }
        return (($access & $privilege) > 0);
    }

    /*     * *
     * Set user type - ensures a valid value is set.
     */

    public function getFullName()
    {
        return $this->valArray["full_name"];
    }

    
   
    public function getId()
    {
        return $this->valArray["id"];
    }

    public function getUserAccount()
    {
        return $this->valArray["user_account"];
    }


    public function setPassword($val)
    {
        $this->valArray["user_pass"] = $val;
        $this->modifyArray["user_pass"] = 'user_pass';
    }

    public function setFullName($val)
    {
        $this->valArray["full_name"] = $val;
        $this->modifyArray["full_name"] = 'user_pass';
    }

    public function setUserType($type)
    {
        $this->valArray["user_type"] = $type;
        $this->modifyArray["user_type"] = 'user_type';
    }

    /**
     * Indicates if this account is active.
     *
     * @return bool
     */
    public function getActive()
    {
        return ($this->valArray["active_flag"] > 0);
    }

    public function getAccount()
    {
        return ($this->valArray["user_account"]);
    }

    public function setActive($flag)
    {
        $this->valArray["active_flag"] = ($flag ? 1 : 0);
        $this->modifyArray["active_flag"] = 'active_flag';
    }

    public function isValid(&$error_list_array)
    {
        if ($this->getUserName() == "")
            $error_list_array .= " Please enter username.<br>";
        if ($this->getUserType() == "")
            $error_list_array .= "Please select user type.<br>";
        if ($this->getFirstName() == "")
            $error_list_array .= "Please enter first name.<br>";
        if ($this->getLastName() == "")
            $error_list_array .= "Please enter last name.<br>";
        if ($this->getEmail() == "")
            $error_list_array .= "Please enter email.<br>";
        if ($this->getPassword() == "") {
            $error_list_array .= "Please enter password.<br>";
        } else if (strlen($this->getPassword()) < 6) {
            $error_list_array .= "The password must be at least 6 characters long.<br>";
        }
        $userfilter = new userfilter();
        $userfilter->addUserNameFilter(trim($this->getUserName()));
        $userfilter->getList();
        $checkuser = $userfilter->getCount();


        if (($checkuser > 0) and ($this->getId() == "")) {
            $error_list_array .= "Duplicated Username, please use another name!<br>";
        }
        $duplicateEmail = $userfilter->checkFeildAlreayExsist('email',trim($this->getEmail()),$this->getId());
        if(count($duplicateEmail) > 0){
            $error_list_array .= "Duplicated E-mail address, please use another E-mail!<br>";
        }
        
        
        
        
//        t("Error list is " . sizeof($error_list_array), __METHOD__);

        return $error_list_array;
    }

    public function getPassword()
    {
        return $this->valArray["user_pass"];
    }
    
    
   
    public static function getUserCompanyImages($relativePath=false,$userId = null)
    {
		$UserSessionManager = new User($userId);
        $userAccountID	    =	$UserSessionManager->getUserAccountId();
        $userAccountDetail	=	new CustomerAccount($userAccountID);
        $imageLogo = '';
        $userImage = $userAccountDetail->getLogo();
        $img_path = SETTING_URL.'images/userlogo/';
        $img_path_directory = SETTING_DIR_REMOTE.'images/userlogo/';
        $images_path= SETTING_DIR_REMOTE.'images/';
        $imgPath = SETTING_URL.'images/';
        if (trim($userImage) != '' && (file_exists($img_path_directory .  $userImage) || file_get_contents($img_path.$userImage))) {
            $imageLogo =  ($relativePath === false ? $img_path : $img_path_directory) . "" . $userImage ;
        } else {
            $accountArray = CustomerAccount::accountParentAccount($userAccountDetail->getId());
            $parientArray = array();
            foreach($accountArray as $v) {
                if($v->getId() != $userAccountDetail->getId()) {
                    $parientArray[] = ['id' => $v->getId()];
                }
                rsort($parientArray);
            }

            if(count($parientArray) > 0) {
                $imageFound = 0;
                foreach($parientArray as $v) {
                    $userAccountObj = new CustomerAccount($v['id']);
                    $accountImage = $userAccountObj->getLogo();
                    if(trim($accountImage) != '' && file_exists($img_path_directory . $accountImage)) {
                        $imageLogo =  $img_path . "" . $accountImage ;
                        $imageLogo =  ($relativePath === false ? $img_path : $img_path_directory) . "" . $accountImage ;
                        $imageFound = 1;
                        break;
                    }
                }
                if($imageFound == 0) {
                    $imageLogo = ($relativePath === false ? $imgPath : $images_path). "inner-logo.png";
                }
            } else {
                $imageLogo = ($relativePath === false ? $imgPath : $images_path) . "inner-logo.png";
            }
        }
        return  $imageLogo;

    }
	public static function createUserApi($accountData,$apiUserData){
		$response = ['STATUS'=>"ERROR",'MESSAGE'=>'Invalid Request','ERRORS'=>[]];
//		echo "<pre>"; print_r($accountData); echo "</pre>"; die();
		$sessionUser = $apiUserData;
		$error_array = [];

		$user = new User();
		$userAccountId = $sessionUser->getUserAccountId();

		$user->setUserAccountId($userAccountId);
		$user->setUserType(isset($accountData['userType']) ? $accountData['userType'] : "client" );
		$user->setUserName(isset($accountData['userName']) ? trim($accountData['userName']) : "");
		$user->setEmail(isset($accountData['email']) ? $accountData['email'] : "");
		$user->setFirstName(isset($accountData['firstName']) ? $accountData['firstName'] : "");
		$user->setLastName(isset($accountData['lastName']) ? $accountData['lastName'] : "");
		$user->setAddress(isset($accountData['address']) ? $accountData['address'] : "");
		$user->setPhone(isset($accountData['phoneNumber']) ? $accountData['phoneNumber'] : "");
		$user->setCountryId(isset($accountData['countryId']) ? $accountData['countryId'] : 1);
		$user->setWarehouseId(isset($accountData['warehouseId']) ? $accountData['warehouseId'] : "");
		$user->setDashboard(isset($accountData['dashboard']) ? $accountData['dashboard'] : "corporate");
		$receiveEmail = 'n';
		if (isset($accountData["receiveEmail"]))
		    $receiveEmail = 'y';
		$user->setReceiveEmail($receiveEmail);
		$user->setAddedBy($sessionUser->getId());
		if($user->getAddedDate() <= '0' || trim($user->getAddedDate()) == '' || trim($user->getAddedDate()) == '1970-01-01 00:00:00')
		    $user->setAddedDate(time());
		$user->setIsTcAgreed('Y');

		//by default set active when added via api
		$user->setActive(1);
		$employee = 0;
		if (isset($accountData["isEmployee"]))
		    $employee = 1;
		$user->setIsEmployee($employee);
		if (!empty($accountData["password"]))
		    $user->setPassword(password_hash($accountData["password"], PASSWORD_DEFAULT));

		$user->save();
		$response['STATUS'] = "SUCCESS";
		$response['MESSAGE'] = "Account successfully created.";
		if (empty($error_array)) {
			  if(isset($accountData['email'])){
				  $to = $accountData["email"];
				  //define the subject of the email
				  $subject = "Successfully Created User" . date('d-m-Y');
				  //define the headers we want passed. Note that they are separated with \r\n
				  $headers = "From: itsupport@oneworldexpress.com\r\nReply-To: itsupport@oneworldexpress.com";
				  $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
				  $headers .= "MIME-Version: 1.0\r\n";
				  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				  $message = 'Hello ' . isset($accountData['firstName']) ? $accountData['firstName'] : "" . ',';             //add boundary string and mime type specification
				  $message .= "\r\n";
				  $message .= "\r\n";
				  $message .= 'Just to Inform you that your user has been created successfully. Below is your login details. ';
				  $message .= "\r\n";
				  $message .= 'Username:  ' . $accountData["userName"];
				  $message .= "\r\n";
				  $message .= 'Password:  ' . $accountData["password"];
				  $message .= "\r\n";
				  $message .= 'If you have not created this account please email to itsupport@oneworldexpress.com.';
				  $message .= "\r\n";
				  $message .= "\r\n";
				  $message .= 'Thanks';
				  $message .= "\r\n";
				  $message .= "One World Express Support Team";
				  $mail_sent = @mail($to, $subject, $message, $headers);
			  }
		       //Do things after save data into user table
				if(isset($accountData['permission_group']) && is_array($accountData['permission_group']) &&  count($accountData['permission_group']) > 0){
					$userHasGroups = new UserHasGroups();
					$userHasGroups->deleteByAdminId($user->getId());
					foreach ($accountData['permission_group'] as $valueGroup) {
						$userHasGroups = new UserHasGroups();
						$userHasGroups->setAdminId($user->getId());
						$userHasGroups->setGroupId($valueGroup);
						$userHasGroups->save();
					}
				}

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
		            $api_key = md5($sessionUser->getId() . $sessionUser->getFirstName().$user->getId(). $sessionUser->getLastName() . date('H:i:s'));
		            $api_secert = md5($sessionUser->getUserName(). $user->getId(). $sessionUser->getUserType() . $sessionUser->getUserAccountId() . date('H:i:s'));
		            $userShoppingPlatforms->setApiKey($api_key);
		            $userShoppingPlatforms->setApiSecrete($api_secert);
		            $userShoppingPlatforms->setStatus(1);
		            $userShoppingPlatforms->setDateCreated(time());
		            $userShoppingPlatforms->save();
		        }

		        $userLog = new UserLog();
		        if (empty($error_array)) {
		                $userLog->createlog('Created new account By Api', $user->getId(), 'USER');
//		                $this->flashMsg->success(formatMessages(SUCCESS_USER_CREATED), "../main/manage_user.php?id=" . $user->getId());
		        }
		} else {
			$response['ERRORS'] = $error_array;
		}
		return $response;
	}
}
