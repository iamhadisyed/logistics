<?php

// get settings
//require_once("includes/settings/common.inc.php");

class CustomerAccount extends DbAccess3
{

    // user_account types (if change types, update type list function).
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

    public function __construct($mixedCreator = null)
    {
        $fieldList = array
        (
            'id' => 'number',
            'user_account' => 'string',
            'company' => 'string',
            'full_name' => 'string',
            'active_flag' => 'bit',
            'return_address' => 'string',
            'email' => 'string',
            'sms_dpd' => 'bit',
            'user_service_type' => ['enum'=>['CHOICE','ROUTING','BOTH'],'default'=>'BOTH'],
            'parentid' => 'number',
            'phone' => 'string',

            'logo' => 'string',
            'instant_label' => 'bit',
            'country' => 'string',
            'tracking_api_access' => 'bit',
            'import_data_csv' => 'bit',
            'proforma' => 'bit',
            'add_tracking' => 'bit',
            'collection' => 'bit',
            'default_description' => 'string',
            'default_notes' => 'string',
            'default_weight' => 'number',
            'payment_term' => 'string',
            'query_term' => 'string',
            'vat_number' => 'string',
            'billing_currency' => 'string',
            'vat_chargable' => 'bit',
            'vat_value' => 'number',
            'allow_remote_area' => 'number',
            'telephone' => 'string',
            'alternative_email' => 'string',
            'billing_address' => 'string',

            'date_dispatch' => 'number',
            'is_product' => 'number',
            'send_courier_data' => 'bit',
            'archive_server' => 'bit',
            'credit_check' => 'bit',
            'tariff_agreed' => 'bit',
            'sales_person' => 'string',
            'scan_document' => 'string',
            'data_entry' => 'bit',
            'bank_account_title' => 'string',
            'bank_sortcode' => 'string',
            'bank_account_number' => 'string',
            'bank_branch_address' => 'string',
            'trade_name_i' => 'string',
            'trade_address_i' => 'string',
            'trade_name_ii' => 'string',
            'trade_address_ii' => 'string',
            'trade_email_i' => 'string',
            'trade_email_ii' => 'string',
            'trade_phone_i' => 'string',
            'trade_phone_ii' => 'string',

            'reg_number' => 'string',
            'reg_address' => 'string',
            'reg_postcode' => 'string',
            'reg_country' => 'number',
            'sale_agent' => 'string',
            'sale_date' => 'date',
            'fuel_charges' => 'number',
            'label_price' => 'number',
            'discount' => 'number',
            'warehouse_id' => 'number',
            'user_signature' => 'string',
            'billing_email' => 'string',
            'billing_contact' => 'string',
            'is_fuelcharges_include' => 'bit',
            'is_prepaid' => 'bit',
            'ftp_shipment_upload' => 'bit',
            'return_label' => 'bit',
            'finalmile_over_label' => 'bit',
            'request_manifest_collection' => 'bit',
            'create_pre_alert' => 'bit',
            'is_employee' => 'bit',
            'invoice_bank_details_id' => 'number',
            'check_list_account_form' => 'bit',
            'check_list_credit_check' => 'bit',
            'check_list_t_cs' => 'bit',
            'check_list_tariff_agreed' => 'bit',
            'check_list_sales_pot' => 'bit',
            'sales_pot_time_period' => 'number',
            'sales_pot_percentage' => 'number',
            'last_login_date' => 'datetime',
            'invalid_login_count' => 'number',
            'token' => 'string',
            'token_updated' => 'datetime',

            'opearation_manifest' => 'bit',
            'allow_return_email' => 'bit',

            'own_tariff' => 'bit',
            'user_warehouse' => ['enum'=>['NON','BIRMINGHAM','HAYES'],'default'=>'NON'],
            'api_key' => 'string',
            'api_secert' => 'string',
            'api_date' => 'datetime',
            'sales_rate' => 'number',
            'allow_oversize' => 'number',
            'allow_overweight' => 'number',
            'balance_alert_percentage' => 'number',
            'commission_break_event_account_amount' => 'number',
            'tracking_order_prefix' => 'string',
            'return_shipment_allow' => 'bit',
            'tariff_values' => 'undefined',
            'active_users' => 'undefined',
            'product_names' => 'undefined',
            'collection_add_line_1' => 'string',
            'collection_add_line_2' => 'string',
            'collection_add_line_3' => 'string',
            'collection_city' => 'string',
            'collection_country' => 'string',
            'collection_postcode' => 'string',
            'theme_id' => 'number',
            'bagging' => 'bit',
            'show_price' => 'bit',
            'retail_customer' => 'bit',
            'default_lang' => 'string',

            'user_code' => 'number',
            'website_link' => 'string',
            'credit_limit' => 'number',
            'balance_alert_percentage' => 'number',
            'invoice_period' => ['enum'=>['daily','weekly','bi-monthly','monthly'],'default'=>'daily'],
            'country_id' => 'number',
            'account_code' => 'string',
            
            'paypal_email' => 'string',
            'paypal_currency' => 'string',
            'paypal_client_id' => 'string',
            'paypal_client_secret' => 'string',
            'date_created' => 'datetime',
            'invoice_template_id' => 'number',

			'account_balance' => 'number'


        );
        //  
        parent::__construct("customer_account", 'id', $fieldList, $mixedCreator);
    }

    public static function getTotalNumberOfUserAccountFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }


    /*     * *
     * Indicates if user_account has given privilege
     */

    public static function bankAccountDetails()
    {
        $types = array(
            'NONE' => array(),
            'BIRMINGHAM' => array(
                'TITLE' => 'One World Express Inc Ltd',
                'SORT CODE' => '16-13-18',
                'ACCOUNT NUMBER' => '11437074',
                'IBAN' => '',
                'BANK' => 'Royal Bank of Scotland'
            ),
            'LONDON' => array(
                'TITLE' => 'One World Express Inc. ltd',
                'SORT CODE' => '20-38-83',
                'ACCOUNT NUMBER' => '40530638',
                'IBAN' => '',
                'BANK' => 'Barclays Bank plc'
            ),
            'OTHER' => array(
                'TITLE' => '',
                'SORT CODE' => '',
                'ACCOUNT NUMBER' => '',
                'IBAN' => '',
                'BANK' => 'OTHER'
            ),
        );
        return $types;
    }

    /*     * *
     * Gives array of user_account types
     */

    /**
     * Get list of user_account objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserAccountListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function randomPassword()
    {

        $alphabet_small = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet_caps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet_integer = '0123456789';
        $alphabet_special = '!$%@#()';

        $pass = array(); //remember to declare $pass as an array

        $alphaLength_small = strlen($alphabet_small) - 1; //put the length -1 in cache
        for ($i = 0; $i < 2; $i++) {
            $n = rand(0, $alphaLength_small);
            $pass[] = $alphabet_small[$n];
        }
        $alphaLength_caps = strlen($alphabet_caps) - 1; //put the length -1 in cache
        for ($i = 0; $i < 2; $i++) {
            $n = rand(0, $alphaLength_caps);
            $pass[] = $alphabet_caps[$n];
        }
        $alphaLength_int = strlen($alphabet_integer) - 1; //put the length -1 in cache
        for ($i = 0; $i < 2; $i++) {
            $n = rand(0, $alphaLength_int);
            $pass[] = $alphabet_integer[$n];
        }
        $alphaLength_special = strlen($alphabet_special) - 1; //put the length -1 in cache
        for ($i = 0; $i < 2; $i++) {
            $n = rand(0, $alphaLength_special);
            $pass[] = $alphabet_special[$n];
        }
        shuffle($pass);
        return implode($pass); //turn the array into a string

    }

    /**
     * Search for user/password combo
     * @param $username , $password
     * @return bool
     */
    public static function getUserRaw($username, $password, $apiUser = 'NO')
    {
        $user_array = array();
        $loginStatus = 0;
        $userId = '0';
        $filter = new UserAccountFilter();
        $filter->addFilter(" user_name = '" . DbAccess3::escape($username) . "'");

        $user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, user_pass');

        if (sizeof($user_array) <= 0 && $apiUser == 'YES') {
            $filter = new UserAccountFilter();
            $filter->addFilter(" api_key = '" . DbAccess3::escape($username) . "'");
            $filter->addFilter(" api_secert = '" . DbAccess3::escape($password) . "'");
            $user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, user_pass');

        }
        if (sizeof($user_array) > 0) {

            if (!$user_array[0]->getActive()) {
                $loginStatus = 0;
            } else {


                $userId = $user_array[0]->getId();
                $_SESSION["user_id"] = $user_array[0]->getId();   // displayed on user_account pages
                $_SESSION["user_name"] = $user_array[0]->getUserName();   // displayed on user_account pages
                $_SESSION["user_type"] = $user_array[0]->getUserType();   // used to guard access to user_account pages
                $_SESSION["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
                $_SESSION["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
                //if(trim($user_array[0]->getUserType()) == User::USER_TYPE_ADMIN || trim($user_array[0]->getUserType()) == User::USER_TYPE_ACCOUNT)
                {
                    $_SESSION['admin']['id'] = $user_array[0]->getId();
                    $_SESSION['admin']['firstname'] = $user_array[0]->getFullName();
                    $_SESSION['admin']['surname'] = $user_array[0]->getUserName();
                    $_SESSION['admin']["user_name"] = $user_array[0]->getUserName();   // displayed on user_account pages
                    $_SESSION['admin']["user_type"] = $user_array[0]->getUserType();   // used to guard access to user_account pages
                    $_SESSION['admin']["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
                    $_SESSION['admin']["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
                }
                $loginStatus = 1;


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
        $filter = new UserAccountFilter();
        $filter->addFilter(" user_name = '" . DbAccess3::escape($username) . "'");
        $user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, invalid_login_count, user_pass');
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
                        $filter = new UserAccountFilter();
                        $filter->addIdFilter($userId);
                        $user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, invalid_login_count, user_pass');
                    } else {
                        return false;
                    }
                } else {


                    return false;
                }
            } else {
                $filter = new UserAccountFilter();
                $filter->addFilter(" api_key = '" . DbAccess3::escape($username) . "' AND api_secert = '" . DbAccess3::escape($password) . "'");
                $user_array = $filter->getColumnList('id, user_name, full_name, user_type, active_flag, user_account, archive_server, parentid, invalid_login_count, user_pass');
                if (count($user_array) <= 0) {
                    return false;
                }
            }
            $useApiKey = true;

        }

        if (sizeof($user_array) > 0) {
            if (!$user_array[0]->getActive()) {
                $loginStatus = 0;
            } else {


                if ($useApiKey === false && !password_verify(DbAccess3::escape($password), $user_array[0]->getUserPass())) {
                    //$invalidCount	=	(int)$user_array[0]->getInvalidLoginCount()+1;
                    //$user_array[0]->setInvalidLoginCount($invalidCount);
                    //$user_array[0]->save();
                    $loginStatus = 0;
                } else {


                    $userId = $user_array[0]->getId();
                    $_SESSION["user_id"] = $user_array[0]->getId();   // displayed on user_account pages
                    $_SESSION["user_name"] = $user_array[0]->getUserName();   // displayed on user_account pages
                    $_SESSION["user_type"] = $user_array[0]->getUserType();   // used to guard access to user_account pages
                    $_SESSION["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
                    $_SESSION["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
                    //if(trim($user_array[0]->getUserType()) == User::USER_TYPE_ADMIN || trim($user_array[0]->getUserType()) == User::USER_TYPE_ACCOUNT)
                    {
                        $_SESSION['admin']['id'] = $user_array[0]->getId();
                        $_SESSION['admin']['firstname'] = $user_array[0]->getFullName();
                        $_SESSION['admin']['surname'] = $user_array[0]->getUserName();
                        $_SESSION['admin']["user_name"] = $user_array[0]->getUserName();   // displayed on user_account pages
                        $_SESSION['admin']["user_type"] = $user_array[0]->getUserType();   // used to guard access to user_account pages
                        $_SESSION['admin']["user_account"] = $user_array[0]->getUserAccount(); // applied to imported consignments
                        $_SESSION['admin']["archive_user"] = $user_array[0]->getArchiveServer(); // applied to imported consignments
                    }

                    $user_array[0]->setInvalidLoginCount(0);
                    $user_array[0]->setLastLoginDate(time());
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

    /**
     * Logout
     */
    public static function logout()
    {
        $UserSessionManager = SessionManager::getUser();
        $UserSessionManager->getUserAccountId();

        $useraccountData =   new CustomerAccount($UserSessionManager->getUserAccountId());
        switch($useraccountData->getThemeId())
        {
            case DEUTSHCEPOST:
                $loginpage  =   "../main/login-deutchepost.php";
                break;
            case UKMAILTHEME:
                $loginpage  =   "../main/login-ukmail.php";
                break;
            case VIVATHEME:
                $loginpage  =   "../main/login-viva.php";
                break;
            case SPARTHEME:
                $loginpage  =   "../main/login-spar.php";
                break;
            
            
            default:
                $loginpage  =   "../main/login.php";
                break;

        }



        self::logUserOut();
        util_redirect($loginpage);
    }

// Get user_account routing

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

    /*     * *
     * Looks up the user_account type name
     */

    public static function getAccountList()
    {
        $sql = "SELECT DISTINCT user_account FROM user";

        $account_list = array();

        $rs = self::runQuery($sql);

        //
        while ($valArray = mysqli_fetch_assoc($rs)) {
            if ($valArray["user_account"] == "")
                continue;
            $account_list[] = $valArray["user_account"];
        }

        return $account_list;
    }

    public static function getUserAccountList($userAccount = "", $parentidArray = array(), $account_typeArray = array(), $valueUserCode = true)
    {

        $where = "active_flag = 1";
        if (sizeof($parentidArray) > 0 && $parentidArray != '')
            $where .= " AND parentid in ('" . implode("','", $parentidArray) . "')";
        if (sizeof($account_typeArray) > 0 && $account_typeArray != '')
            $where .= " AND user_type in ('" . implode("','", $account_typeArray) . "')";


        $sql = "SELECT user_account, user_name, user_code, id FROM user_account where " . $where . " order by user_account asc ";

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

    /*     * **
     * Get/Set the user_account password
     *
     * @param string
     */

    public static function getThemeDropdown($themeselected)
    {

        $theme_array = array();
        $theme_array[1] = "handlerbund";

        foreach ($theme_array as $key => $valArray) {
            if ($valArray == "")
                continue;
            $selected = ($themeselected == $key) ? " selected" : "";
            $option .= "<option " . $selected . " value='" . $key . "'>" . $valArray . "</option>";
        }

        return $option;
    }

    public static function getImageExists($logoImage, $profileImage)
    {
        if (!empty($logoImage) && file_exists($logoImage)) {
            return $logoImage;
        } elseif (!empty($profileImage) && file_exists($profileImage)) {
            return $profileImage;
        } else {
            return "../_assets/images/profile/default_profile.png";
        }
    }

    public static function accountSubAccount($accountId = 0, $userId = 0, $includeSelf = false, $recursive = false)
    {
        $userAccountDataArray = [];
        if ($accountId == 0 && $userId == 0) {
            return false;
        } else if ($userId > 0) {
            $userIdData = new User($userId);
            if ($userIdData->getId() > 0) {
                $accountId = $userIdData->getUserAccountId();
            } else
                return false;
        }
        /*if($includeSelf)
            $userAccountDataArray[] =   $accountId;
        */
        if (!$recursive && $includeSelf)
            $userAccountDataArray = [$accountId];

        $accountData = new UserAccountFilter();
        $accountData->addFilter(" parentid = '" . $accountId . "' and active_flag = 1");
        $userAccountDataFilter = $accountData->getColumnList("id, user_account");
        if (count($userAccountDataFilter) > 0) {
            foreach ($userAccountDataFilter as $accountParentData) {
                //$userAccountDataArray[] = $accountParentData->getId();
                if ($recursive)
                    $userAccountDataArray[$accountParentData->getId()] = self::accountSubAccount($accountParentData->getId(), 0, false, $recursive);
                else
                    $userAccountDataArray = array_merge($userAccountDataArray, self::accountSubAccount($accountParentData->getId(), 0, true, $recursive));
            }
        } else if (!$recursive) {
            return $userAccountDataArray;
        }
        if ($includeSelf && $recursive)
            return [$accountId => $userAccountDataArray];
        else
            return $userAccountDataArray;
    }
    function getImmediateSubaccount($accountId,$accountNumber=false) {
        $accountFilter = new UserAccountFilter();
        $accountFilter->addFilter(' parentid = ' . $accountId);
        $subaccountFilter = $accountFilter->getColumnList("id,user_account");
        $subaccountArr = [];
        if (count($subaccountFilter) > 0) {
            foreach ($subaccountFilter as $accountObj){
                if($accountNumber)
                    $subaccountArr[] = $accountObj->getUserAccount();
                else
                    $subaccountArr[] = $accountObj->getId();
            }
        }
        return $subaccountArr;
    }
    public static function accountParentAccount($accountId, $getAccoutnID = false) {
        $sql = "SELECT 
                    user2.id,
                     user2.`parentid` 
                    FROM
                    (SELECT 
                      @r AS _id,
                      (SELECT 
                        @r := parentid 
                      FROM
                        customer_account 
                      WHERE id = _id) AS parentid,
                         @l := @l + 1 AS lvl 
                    FROM
                      (SELECT 
                        @r := ".$accountId.",
                        @l := 0) vars,
                      customer_account h 
                    WHERE @r <> 0) user1 
                    JOIN customer_account user2 
                      ON user1._id = user2.id ";
        if($getAccoutnID)
        {
            $accountArray   =   array();

            $resultset     = DbAccess3::getListFromSql(__CLASS__, $sql);
            if(count($resultset)>0)
            {
                foreach($resultset as $dataAccount){
                    $accountArray[] = $dataAccount->getId();
                }
            }
            return $accountArray   ;
        }
        else{
            return DbAccess3::getListFromSql(__CLASS__, $sql);
        }

    }
    
    public static function accountImmediateChild($accountId) {
        $sql = "SELECT 
                  *
                FROM
                  customer_account ua  
                WHERE  ua.`parentid` = " . $accountId;
        $accountArray = [];
        $resultset = DbAccess3::getListFromSql(__CLASS__, $sql);
        if(count($resultset)>0)
        {
            foreach($resultset as $dataAccount)
                $accountArray[] = $dataAccount->getId();
        }
        return $accountArray;
    }
    
    public static function accountImmediateParent($accountId) {
        $sql = "SELECT 
                user2.id,
                user2.user_account
              FROM
                (SELECT 
                  @r AS _id,
                  (SELECT 
                    @r := parentid 
                  FROM
                    user_account 
                  WHERE id = _id) AS parentid,
                  @l := @l + 1 AS lvl 
                FROM
                  (SELECT 
                    @r := '".$accountId."',
                    @l := 0) vars,
                  user_account h 
                WHERE @r <> 0) user1 
                JOIN user_account user2 
                  ON user1._id = user2.id 
              ORDER BY user1.lvl ASC LIMIT 1,1";
        $parentId = 0;
        $resultset = DbAccess3::getListFromSql(__CLASS__, $sql);
        if(count($resultset) > 0){
            $parentId = $resultset[0]->getId();
        }
        return $parentId;
    }

    /*     * *
     * Set user_account type - ensures a valid value is set.
     */

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
     * Gets textual description of current user_account type
     */

    public function getTariffValues()
    {

        $arrayTariff = array();

        if ($this->valArray["tariff_values"] == '') {
            // Get user_account tariff
            $tfilter = new TariffsAccountMappingFilter();
            $tfilter->addUserAccountFilter($this->getId());
            $tlist = $tfilter->getTariffNameDistinctList('t.tariff_name');
            if (count($tlist) > 0) {
                foreach ($tlist as $t) {
                    $arrayTariff[] = $t->getTariffName();
                }
            }
            $this->valArray["tariff_values"] = json_encode($arrayTariff);
        }
        return $this->valArray["tariff_values"];
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId()
    {
        return $this->valArray["id"];
    }

    public function getProductNames()
    {

        $arrayTariff = array();

        if ($this->valArray["product_names"] == '') {
            // Get user_account tariff
            $Rfilter = new CustomizedUserServicesRoutingFilter();
            $Rfilter->addUserAccountFilter($this->getId());
            $Rlist = $Rfilter->getRoutinNameDistinctList('product_name as routing_name');
            //print_r($tlist);exit;
            if (count($Rlist) > 0) {
                foreach ($Rlist as $R) {
                    $arrayTariff[] = $R->getRoutingName();
                }
            }
            $this->valArray["product_names"] = json_encode($arrayTariff);
        }
        return $this->valArray["product_names"];
    }



    public function getUserAccount()
    {
        return $this->valArray["user_account"];
    }


    /*     * *
     * Check if data held by curernt object is valid
     */

    public function getAllFields()
    {
        return $this->valArray;
    }

    public function getPassword()
    {
        return $this->valArray["user_pass"];
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
        $val = self::USER_TYPE_CLIENT;
        //
        $types = self::listOfUserTypes();
        foreach ($types as $key => $desc) {
            if ($type == $key) { // valid type found
                $val = $type;
                break;
            }
        }
        $this->valArray["user_type"] = $val;
        $this->modifyArray["user_type"] = 'user_type';
    }

    public static function listOfUserTypes()
    {
        $types = array(
            self::USER_TYPE_CLIENT => "Customer",
            self::USER_TYPE_ADMIN => "Administrator",
            self::USER_TYPE_CUSTOMER_SERVICE => "Customer Service",
            self::USER_TYPE_CORPORATE_CLIENT => "Corporate Customer",
            //self::USER_TYPE_MANAGER 			=> "End of Day",
            self::USER_TYPE_FINANCE => "Finance",
            self::USER_TYPE_WAREHOUSE => "Operation",
            self::USER_TYPE_WAREHOUSE_ALPHA => "Warehouse Scan",
            self::USER_TYPE_MANAGER => "Manager",
            self::USER_TYPE_SALES => "Sales",

        );
        return $types;
    }

    public function getUserTypeDescription()
    {
        return self::userTypeName($this->getUserType());
    }

    /*     * *
     * Get list of possible accounts
     */

    public static function userTypeName($userType)
    {
        $types = self::listOfUserTypes();
        if (isset($types[$userType]))
            return $types[$userType];
        return "Unknown";
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

        if ($this->getUserAccount() == "")
            $error_list_array[] = "Please enter account number.";
        if ($this->getEmail() == "" || !filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL))
            $error_list_array[] = "Please enter valid email.";
        if ($this->getFullName() == "")
            $error_list_array[] = "Please enter full name.";
        if ($this->getCompany() == "")
            $error_list_array[] = "Please enter company name.";
        t("Error list is " . sizeof($error_list_array), __METHOD__);

        return (sizeof($error_list_array) == 0);
    }

    public function getFullName()
    {
        return $this->valArray["full_name"];
    }

    public function getUserUniqueornot($user)
    {
        $filter = new UserAccountFilter();
        $filter->addFilter(" user_name = '" . DbAccess3::escape($user) . "'");
        $user_array = $filter->getColumnList('user_name');

        if (sizeof($user_array) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getLastID()
    {
        $sql = "SELECT id FROM user_account ORDER BY id DESC LIMIT 1;";
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['id'];

    }

    public function getNewUser()
    {
        $sql = "SELECT id,user_name FROM user_account where new_registred=1";
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_all($rs, MYSQLI_ASSOC);
        return $data;
    }
	//this function is used by api (create account)
	public static function createUserAccountApi($accountData,$apiUserData){
		$response = [];
		$sessionUser = $apiUserData;
		$sessionUserAccount = new CustomerAccount($sessionUser->getUserAccountId());
		$apiUserPermissions = Permissions::getAllPermissions($sessionUser->getId());
		$error_array = array();
		$oldLogo = '';
		// save new address details
		intval($sessionUser->getId());
		$userAccount = new CustomerAccount(intval($sessionUser->getId()));
		$userLogSystem = new CustomerAccount(intval($sessionUser->getId()));
		$userLogSystem->getTariffValues();
//		echo "<pre>"; print_r($userLogSystem->getTariffValues()); echo "</pre>"; die();
		/* Accounts Tabs */
		$default_weight = $userAccount->getDefaultWeight() == '' ? 0.00 : $userAccount->getDefaultWeight();
		$default_weight = (isset($accountData["weight"]) && !empty($accountData["weight"])) ? $accountData["weight"] : $default_weight;
		if ((!empty($apiUserPermissions['customer_detail_account']) && $apiUserPermissions['customer_detail_account'] > 0)) {
			$userAccountNumber  =   trim($accountData["account_number"]);
			$userAccount->setUserAccount($userAccountNumber);
			if ( (int)$userAccount->getId() <= 0) {
				$accountCheck   =   new UserAccountFilter();
				$accountCheck->addFilter("    user_account = '".$userAccountNumber. "'");
				$accountCheck->addFilter("    id != '".$sessionUser->getId(). "'");
				$accountCheckList = $accountCheck->getColumnList("user_account, id");
				if(count($accountCheckList)>0){
					$errors[] = 'Account Number already exist, Please choose different account number';
                    $response['STATUS'] = "ERROR";
                    $response['ERROR'] = $errors;
                    $response['MESSAGE'] = "Please fix below error(s)";
                    return $response;
                }
			}
			if (strpos($userAccountNumber, ' ') !== false)
			{
                $errors[] = 'Account Number contain space, which is not allowed, Please remove the spaces';
                $response['STATUS'] = "ERROR";
                $response['ERROR'] = $errors;
                $response['MESSAGE'] = "Please fix below error(s)";
			}
			$userAccount->setParentid($accountData["parentAccountId"]);
			$chkActive = 0;
			if (isset($accountData["isActive"]))
				$chkActive = 1;
			$userAccount->setActive($chkActive);
			$userAccount->setCompany(isset($accountData['company_name']) ? $accountData['company_name'] : "");
			$userAccount->setFullName(isset($accountData['contact_name']) ? $accountData['contact_name'] : "");
			$userAccount->setTelephone(isset($accountData['contact_number']) ? $accountData['contact_number'] : "");
			$userAccount->setCountryId($accountData["countryId"]);
			$userAccount->setEmail(isset($accountData['account_email']) ? $accountData['account_email'] : "");
			$userAccount->setReturnAddress(isset($accountData['return_address']) ? $accountData['return_address'] : "");
			$userAccount->setDefaultWeight($default_weight);
//			$userAccount->setDefaultNotes($this->form_vars["notes"]);
//			$userAccount->setDefaultDescription($this->form_vars["description"]);
//			if ($sessionUser->getUserType() == User::USER_TYPE_ADMIN && isset($this->form_vars["theme"])) {
//				$userAccount->setThemeId($this->form_vars["theme"]);
//			} else {
				$userAccount->setThemeId($sessionUserAccount->getThemeId());
//			}
		}
		/* End Accounts Tabs */
		/* Company Details */
		if ((!empty($apiUserPermissions['customer_detail_company']) && $apiUserPermissions['customer_detail_company'] > 0) && $sessionUser->getId() > 0) {
			$userAccount->setBankAccountTitle(isset($accountData['bank_account_title']) ? $accountData['bank_account_title'] : "");
			$userAccount->setBankSortcode(isset($accountData['bank_sort_code']) ? $accountData['bank_sort_code'] : "");
			$userAccount->setBankAccountNumber(isset($accountData['bank_account_number']) ? $accountData['bank_account_number'] : "");
			$userAccount->setBankBranchAddress(isset($accountData['bank_branch_address']) ? $accountData['bank_branch_address'] : "");
			$userAccount->setTradeNameI(isset($accountData['trade_ref_name']) ? $accountData['trade_ref_name'] : "");
			$userAccount->setTradeAddressI(isset($accountData['trade_ref_address']) ? $accountData['trade_ref_address'] : "");
			$userAccount->setTradeEmailI(isset($accountData['trade_ref_email']) ? $accountData['trade_ref_email'] : "");
			$userAccount->setTradePhoneI(isset($accountData['trade_ref_phone_no']) ? $accountData['trade_ref_phone_no'] : "");
			$userAccount->setRegNumber(isset($accountData['company_reg_number']) ? $accountData['company_reg_number'] : "");
			$userAccount->setRegAddress(isset($accountData['company_reg_address']) ? $accountData['company_reg_address'] : "");
			$userAccount->setRegPostcode(isset($accountData['company_reg_postcode']) ? $accountData['company_reg_postcode'] : "");
			$userAccount->setRegCountry(isset($accountData['companyRegCountryId']) ? $accountData['companyRegCountryId'] : "");
			$userAccount->setUserSignature(isset($accountData['email_signature']) ? $accountData['email_signature'] : "");
			/* End Company Details */
			/* Billing Detail */
		}

		$accountDisplayInvoices = $userAccount->getInvoiceBankDetailsId() == '' ? "0" : $userAccount->getInvoiceBankDetailsId();
		$userAccount->setInvoiceBankDetailsId($accountDisplayInvoices);
		$chkVatChargable = $userAccount->getVatChargable() == 0 ? 0 : $userAccount->getVatChargable();
		$userAccount->setVatChargable($chkVatChargable);
		$vatNumber = $userAccount->getVatNumber() == '' ? '' : $userAccount->getVatNumber();
		$userAccount->setVatNumber($vatNumber);
		$queryTerm = $userAccount->getQueryTerm() == '' ? '' : $userAccount->getQueryTerm();
		$userAccount->setQueryTerm($queryTerm);
		$paymentTerm = $userAccount->getPaymentTerm() == '' ? '' : $userAccount->getPaymentTerm();
		$userAccount->setPaymentTerm($paymentTerm);
		$vatValue = $userAccount->getVatValue() == '' ? 0.00 : $userAccount->getVatValue();
		$userAccount->setVatValue($vatValue);
		$chkAccountType = $userAccount->getIsPrepaid() == 0 ? 0 : $userAccount->getIsPrepaid();
		$userAccount->setIsPrepaid($chkAccountType);
		$credit_check = $userAccount->getCreditCheck() == 0 ? 0 : $userAccount->getCreditCheck();
		$userAccount->setCreditCheck($credit_check);
		$tariff_agreed = $userAccount->getTariffAgreed() == 0 ? 0 : $userAccount->getTariffAgreed();
		$userAccount->setTariffAgreed($tariff_agreed);
		$is_fulecharges_include = $userAccount->getIsFuelchargesInclude() == 0 ? 0 : $userAccount->getIsFuelchargesInclude();
		$userAccount->setIsFuelchargesInclude($is_fulecharges_include);
		$own_tariff = $userAccount->getOwnTariff() == 0 ? 0 : $userAccount->getOwnTariff();
		$userAccount->setOwnTariff($own_tariff);
		$fuelCharges = $userAccount->getFuelCharges() == '' ? 0.00 : $userAccount->getFuelCharges();
		$userAccount->setFuelCharges($fuelCharges);
		$labelCharges = $userAccount->getLabelPrice() == '' ? 0.00 : $userAccount->getLabelPrice();
		$userAccount->setLabelPrice($labelCharges);
		$discountCharges = $userAccount->getDiscount() == '' ? 0.00 : $userAccount->getDiscount();
		$userAccount->setDiscount($discountCharges);
		$credit_limit = $userAccount->getCreditLimit() == '' ? 0.00 : $userAccount->getCreditLimit();
		$userAccount->setCreditLimit($credit_limit);
		$invoicePeriod = $userAccount->getInvoicePeriod() == '' ? 'daily' : $userAccount->getInvoicePeriod();
		$userAccount->setInvoicePeriod($invoicePeriod);

		if ((!empty($apiUserPermissions['customer_detail_billing']) && $apiUserPermissions['customer_detail_billing'] > 0)  && $sessionUser->getId() > 0) {
			$userAccount->setBillingCurrency(isset($accountData['billing_currency']) ? $accountData['billing_currency'] : "");
			$invoicePeriod = "daily";
			if (isset($accountData["invoice_period"]))
				$invoicePeriod = $accountData["invoice_period"];
			$userAccount->setInvoicePeriod($invoicePeriod);

//			if (!empty($this->form_vars['invoice_bank_details_id']))
//				$accountDisplayInvoices = $this->form_vars["invoice_bank_details_id"];
//			$userAccount->setInvoiceBankDetailsId($accountDisplayInvoices);
//			$userAccount->setBillingAddress($this->form_vars["billing_address"]);
//			$userAccount->setBillingEmail($this->form_vars["billing_email"]);

			if (isset($accountData["vat_number"]) && !empty($accountData["vat_number"])) {
				$vatNumber = $accountData["vat_number"];
				$userAccount->setVatNumber($vatNumber);
			}

//			if (isset($this->form_vars["query_term"]) && !empty($this->form_vars["query_term"])) {
//				$queryTerm = $this->form_vars["query_term"];
//				$userAccount->setQueryTerm($queryTerm);
//			}

//			if (isset($this->form_vars["payment_term"]) && !empty($this->form_vars["payment_term"])) {
//				$paymentTerm = $this->form_vars["payment_term"];
//				$userAccount->setPaymentTerm($paymentTerm);
//			}

			if (isset($accountData["chkVatChargable"])) {
				$chkVatChargable = 1;
			} else {
				$chkVatChargable = 0;
			}
			$userAccount->setVatChargable($chkVatChargable);

//			if (isset($this->form_vars["vat_value"]) && !empty($this->form_vars["vat_value"]))
//				$vatValue = $this->form_vars["vat_value"];
//			$userAccount->setVatValue($vatValue);

			if (isset($accountData["chkAccountType"])) {
				$chkAccountType = 1;
			} else {
				$chkAccountType = 0;
			}
			$userAccount->setIsPrepaid($chkAccountType); //is_prepaid
			$credit_limit = trim($accountData["credit_limit"]) == '' ? 0 : $accountData["credit_limit"];
			$userAccount->setCreditLimit($credit_limit);

			if (isset($accountData["credit_check"])) {
				$credit_check = 1;
			} else {
				$credit_check = 0;
			}
			$userAccount->setCreditCheck($credit_check);

			if (isset($accountData["tariff_agreed"])) {
				$tariff_agreed = 1;
			} else {
				$tariff_agreed = 0;
			}
			$userAccount->setTariffAgreed($tariff_agreed);

			if (isset($accountData["is_fulecharges_include"])) {
				$is_fulecharges_include = 1;
			} else {
				$is_fulecharges_include = 0;
			}
			$userAccount->setIsFuelchargesInclude($is_fulecharges_include);

			if (isset($accountData["own_tariff"])) {
				$own_tariff = 1;
			} else {
				$own_tariff = 0;
			}
			$userAccount->setOwnTariff($own_tariff);

			/* End Billing Detail */
		}


		$check_list_sales_pot = $userAccount->getCheckListSalesPot() == '' ? 0 : $userAccount->getCheckListSalesPot();
		$salesPotTimePeriod = $userAccount->getSalesPotTimePeriod() == '' ? 0 : $userAccount->getSalesPotTimePeriod();
		$salesPotPercentage = $userAccount->getSalesPotPercentage() == '' ? 0.00 : $userAccount->getSalesPotPercentage();

		$userAccount->setSalesPotPercentage($salesPotPercentage);
		$userAccount->setSalesPotTimePeriod($salesPotTimePeriod);
		$userAccount->setCheckListSalesPot($check_list_sales_pot);



		$check_list_account_form = $userAccount->getCheckListAccountForm() == '' ? 0 : $userAccount->getCheckListAccountForm();
		$check_list_credit_check = $userAccount->getCheckListCreditCheck() == '' ? 0 : $userAccount->getCheckListCreditCheck();
		$check_list_t_cs = $userAccount->getCheckListTCs() == '' ? 0 : $userAccount->getCheckListTCs();
		$check_list_tariff_agreed = $userAccount->getCheckListTariffAgreed() == '' ? 0 : $userAccount->getCheckListTariffAgreed();

		if ((!empty($apiUserPermissions['customer_detail_checklist']) && $apiUserPermissions['customer_detail_checklist'] > 0) && $sessionUser->getId() > 0) {
			//Check List Post

			if (isset($accountData["check_list_account_form"])) {
				$check_list_account_form = 1;
			} else {
				$check_list_account_form = 0;
			}
			$userAccount->setCheckListAccountForm($check_list_account_form);

			if (isset($accountData["check_list_credit_check"])){
				$check_list_credit_check = 1;
			} else {
				$check_list_credit_check = 0;
			}
			$userAccount->setCheckListCreditCheck($check_list_credit_check);

			if (isset($accountData["check_list_t_cs"])) {
				$check_list_t_cs = 1;
			} else {
				$check_list_t_cs = 0;
			}
			$userAccount->setCheckListTCs($check_list_t_cs);

			if (isset($accountData["check_list_tariff_agreed"])) {
				$check_list_tariff_agreed = 1;
			} else {
				$check_list_tariff_agreed = 0;
			}
			$userAccount->setCheckListTariffAgreed($check_list_tariff_agreed);
		}

		/* End Check List */
//                    }/* End Permissions Check */
		/* Unknown Set Default */
		$userAccount->setUserServiceType("CHOICE");
		$userAccount->setUserWarehouse("NON"); //which warehouse user belongs to?
		$userAccount->setApiDate(date('d-m-Y'));


		/* End Unknown Set Default */
//		$this->selectedTariff = $this->form_vars["tariff_name"];


		if (empty($accountData['parentAccountId']) && $sessionUser->getUserType() != User::USER_TYPE_ADMIN) {
			if ($sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
				$userAccount->setParentId($sessionUser->getUserAccountId());
			} else {
                $errors[] = 'Parent account not found';
                $response['STATUS'] = "ERROR";
                $response['ERROR'] = $errors;
                $response['MESSAGE'] = "Please fix below error(s)";
                return $response;
			}
		}

		if (count($error_array) <= 0) {
			if ($userAccount->isValid($error_array) || $sessionUser->getId() > 0) {

				$userAccount->save();
				$response['STATUS'] = "SUCCESS";
				$response['MESSAGE'] = "Account successfully created.";

//				$latestId = $userAccount->getId();
//				$ConsignmentLog = new ConsignmentLog();
//				$ConsignmentLog->createlog("USER CREATED/ EIDTED " . $accountData["accountNumber"], $userAccount->getId());
//
			}
		}else{
			$response['ERRORS'] = $error_array;
		}
		return $response;
	}

    public function getAccountUsers($user_account_id) {
        $usersData = new \UserFilter();
        $usersData->addFilter('user_account_id = ' . $user_account_id);
        $usersData = $usersData->getColumnList('id');
        $usersIds = '';
        foreach ($usersData as $usersDatum) {
            $usersIds .= $usersDatum->getId() . ',';
        }
        return $usersIds = rtrim($usersIds, ',');
    }

    public function showAccount($consignmentAccountId,$accountArrays,$searchAccountId = 0,$returnAccountId=false) {
        $loginSubAccountArray = $accountArrays['loginSubAccountArray'];
        $searchSubAccountArray = $accountArrays['searchSubAccountArray'];
        $userAccount = new CustomerAccount($consignmentAccountId);
        $consignmentAccount = $userAccount->getUserAccount();
        if($returnAccountId) {
            $consignmentAccount = $userAccount->getId();
        }
        $tmpAccountWithChild = $accountArrays['accountSubAccount'];
        if (!in_array($searchAccountId, $loginSubAccountArray)) {
            if (!in_array($consignmentAccountId, $searchSubAccountArray) && $searchAccountId > 0) {
                foreach ($tmpAccountWithChild as $accountId => $tmpAccounrArr) {
                    if (in_array($consignmentAccountId, $tmpAccounrArr)) {
                        $userAccount = new CustomerAccount($accountId);
                        $consignmentAccount = $userAccount->getUserAccount();
                        if($returnAccountId) {
                            $consignmentAccount = $userAccount->getId();
                        }
                    }
                }
            } else {
                foreach ($tmpAccountWithChild as $accountId => $tmpAccounrArr) {
                    if (in_array($consignmentAccountId, $tmpAccounrArr)) {
                        $userAccount = new CustomerAccount($accountId);
                        $consignmentAccount = $userAccount->getUserAccount();
                        if($returnAccountId) {
                            $consignmentAccount = $userAccount->getId();
                        }
                    }
                }
            }
        } else {
            $userAccount = new CustomerAccount($searchAccountId);
            $consignmentAccount = $userAccount->getUserAccount();
            if($returnAccountId) {
                $consignmentAccount = $userAccount->getId();
            }
        }
        return $consignmentAccount;
    }

    public function getSubAccountsArrayShowConsignmentAccount($loginUserAccountId,$searchAccountId) {
        $loginSubAccountArray = $this->getImmediateSubaccount($loginUserAccountId);
        $searchSubAccountArray = $this->getImmediateSubaccount($searchAccountId);
        $tmpAccountArrays = [];
        $tmpAccountArrays['loginSubAccountArray'] = $loginSubAccountArray;
        $tmpAccountArrays['searchSubAccountArray'] = $searchSubAccountArray;
        if (!empty($searchSubAccountArray)) {
            foreach ($searchSubAccountArray as $acc) {
                $tmpAccountArrays['accountSubAccount'][$acc] = CustomerAccount::accountSubAccount($acc);
            }
        } else {
            foreach ($loginSubAccountArray as $acc) {
                $tmpAccountArrays['accountSubAccount'][$acc] = CustomerAccount::accountSubAccount($acc);
            }
        }
        return $tmpAccountArrays;
    }
    public static function updateBalance($accountId){
    	if(!empty($accountId) && $accountId > 0) {
			$accountBalanceInfo = getBalance($accountId);
			$newAccountBalance = $accountBalanceInfo['_balance'];
			$userAccountUpdateBalance = new CustomerAccount($accountId);
			$userAccountUpdateBalance->setAccountBalance($newAccountBalance);
			$userAccountUpdateBalance->save();
		}
	}

}

// class
