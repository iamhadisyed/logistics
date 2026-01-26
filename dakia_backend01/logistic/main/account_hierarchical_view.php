<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'invoicebankdetails.class',
    'invoicebankdetailsfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
]);
/* * *
 * Page for editing a user
 */
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $allowedAccounts = [];
    private $userData = null;
    private $account_id = 0;
    private $account_id_encoded = '';
    protected function init() {

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Account Hierarchical View'
        );
        
        $this->account_id = base64_decode(util_get("id"));
        $this->account_id_encoded = util_get("id");
        
        
        $this->userData = SessionManager::getUser();
        if($this->userData->getUserType() == User::USER_TYPE_CORPORATE)
        {
            $userAccountId = $this->userData->getUserAccountId();       
            $this->allowedAccounts = CustomerAccount::accountSubAccount($userAccountId,0,true);
        }
        t_on(); // turn on trace for this page
       
        //Handle user account details ajax
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == 'get_user_account_details') {
            $userAccountId = (int) $this->form_vars['account_id'];
            if($userAccountId > 0){
                $userAccountObj = new CustomerAccount($userAccountId);
                $userAccountParentObj = new CustomerAccount($userAccountObj->getParentid());
                $country = new Country($userAccountObj->getCountryId());
                // All Sub Accounts Cuunt
                $userAccountFilter = new UserAccountFilter();
                $userAccountFilter->addFilter("ua.parentid = '".$userAccountId."' AND active_flag = '1' ");
                $childAccountsTotal = $userAccountFilter->getCount();

                // Today's Sub Accounts Cuunt
                $userAccountTodayFilter = new UserAccountFilter();
                $userAccountTodayFilter->addFilter("ua.parentid = '".$userAccountId."' AND active_flag = '1' ");
                $userAccountTodayFilter->addFilter(" DATE_FORMAT(ua.date_created,'%Y-%m-%d') = '".date("Y-m-d")."'");
                $childTodayAccountsTotal = count($userAccountTodayFilter->getList());

                // Last week's Sub Accounts Cuunt
                $userAccountWeekFilter = new UserAccountFilter();
                $userAccountWeekFilter->addFilter("ua.parentid = '".$userAccountId."' AND active_flag = '1' ");
                $userAccountWeekFilter->addFilter(" YEARWEEK(ua.date_created, 1) = YEARWEEK(CURDATE(), 1)");
                $childWeeklyAccountsTotal = count($userAccountWeekFilter->getList());



                $userFilter = new UserFilter();
                $userFilter->addFilter("user_account_id = ".$userAccountId." AND active_flag = '1' ");
                $childUserTotal = $userFilter->getCount();
                $userFilter->addFilter("  YEARWEEK(ua.date_created, 1) = YEARWEEK(CURDATE(), 1)");
                $childWeeklyUserTotal = $userFilter->getCount();
                $userFilter->addFilter(" DATE_FORMAT(added_date,'%Y-%m-%d') = '".date("Y-m-d")."'");
                $childTodayUserTotal = $userFilter->getCount();
                
                


                $userLogo = "";
                if(!empty($userAccountObj->getLogo()))
                    $userLogo = '../images/userlogo/thumbnail/owe_100_'.$userAccountObj->getLogo();
                if(empty($userLogo) || !file_exists($userLogo)){
                    $userLogo = "../images/userlogo/noimagefound.jpg";
                }
                $activeFlag = ($userAccountObj->getActiveFlag() == "1") ? "Yes" : "NO";
                $vatChargable = ($userAccountObj->getVatChargable() == "0") ? "Yes" : "NO";
                $isPrepaid = ($userAccountObj->getIsPrepaid() == "1") ? "Yes" : "NO";
                $servicesLists = Services::getServiceMapping($userAccountObj->getId());


                /*
               *  User Data check
               */
                $myUserDataFilter   =   new UserFilter();
                $myUserDataFilter->addFilter(" user_account_id = '".$userAccountId."' AND active_flag = '1' " );
                $myUserData   =   $myUserDataFilter->getColumnList("count(id) as user_count");
                $myUserCount    =   (count($myUserData)>0 ? $myUserData[0]->getUserCount():0);
                
                $myUserDataFilter->addFilter("  YEARWEEK(added_date, 1) = YEARWEEK(CURDATE(), 1)");
                $myWeeklyUserData   =   $myUserDataFilter->getColumnList("count(id) as user_count");
                $myWeeklyUserCount  =   (count($myWeeklyUserData)>0 ? $myWeeklyUserData[0]->getUserCount():0);
                
                $myUserDataFilter->addFilter(" DATE_FORMAT(added_date,'%Y-%m-%d') = '".date("Y-m-d")."'");
                $myTodayUserData   =   $myUserDataFilter->getColumnList("count(id) as user_count");
                $myTodayUserCount  =   (count($myTodayUserData)>0 ? $myTodayUserData[0]->getUserCount():0);


                /*
               *  All User Data check
               */
                $mySubAccountUserDataFilter   =   new UserFilter();
                $mySubAccountUserDataFilter->addFilter(" user_account_id IN (SELECT id FROM customer_account WHERE parentid =  '".$userAccountId."') AND active_flag = '1' " );

                $mySubAccountUserData   =   $mySubAccountUserDataFilter->getColumnList("count(id) as user_count");
                $mySubAccountUserCount    =   (count($mySubAccountUserData)>0 ? $mySubAccountUserData[0]->getUserCount():0);

                $mySubAccountUserDataFilter->addFilter(" YEARWEEK(added_date, 1) = YEARWEEK(CURDATE(), 1)");
                $myWeeklySubAccountUserData   =   $mySubAccountUserDataFilter->getColumnList("count(id) as user_count");
                $myWeeklySubAccountUserCount    =   (count($myWeeklySubAccountUserData)>0 ? $myWeeklySubAccountUserData[0]->getUserCount():0);
                
                $mySubAccountUserDataFilter->addFilter(" DATE_FORMAT(added_date,'%Y-%m-%d') = '".date("Y-m-d")."'");
                $myTodaySubAccountUserData   =   $mySubAccountUserDataFilter->getColumnList("count(id) as user_count");
                $myTodaySubAccounUserCount    =   (count($myTodaySubAccountUserData)>0 ? $myTodaySubAccountUserData[0]->getUserCount():0);


                $myUserDataFilter->addFilter(" DATE_FORMAT(added_date,'%Y-%m-%d') = '".date("Y-m-d")."'");



                 /*
                *  Shipment Data check
                */
                $conArrayStatus =   array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_PARTIAL_DELIVERED,
                    Consignment::STATUS_CLOSE,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PROBLEM ,
                    Consignment::STATUS_RELABLED ,
                    Consignment::STATUS_DISCREPANCY ,
                    Consignment::STATUS_AWATING_CLAIM);
                
                $myShipmentDataFilter   =   new ConsignmentFilter();
                $myShipmentDataFilter->addJoin(" `user` u"," u.id = c.user_id AND u.`user_account_id` = '".$userAccountId."'");
//                $myShipmentDataFilter->addFilter("        ua.id = '".$userAccountId."'" , 'userAccountFilter');
                $myShipmentDataFilter->addFilter("        c.date_label_created > 0 and c.shipment_status IN  ( '".implode("','",$conArrayStatus)."') " , 'filter');
                $myshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myShipmentCount    =   (count($myshipmentData)>0 ? $myshipmentData[0]->getShipmentCount():0);

                $myShipmentDataFilter->addFilter("         YEARWEEK(c.date_label_created, 1) = YEARWEEK(CURDATE(), 1)" , 'filter');
                $myWeeklyshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myWeeklyShipmentCount    =   (count($myWeeklyshipmentData)>0 ? $myWeeklyshipmentData[0]->getShipmentCount():0);
                
                
                $myShipmentDataFilter->addFilter("        (c.date_label_created >= ".strtotime(date("Y-m-d 00:00:00"))." AND c.date_label_created <= ".strtotime(date("Y-m-d 23:59:59")).")  " , 'filter');
                $myTodayshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myTodayShipmentCount    =   (count($myTodayshipmentData)>0 ? $myTodayshipmentData[0]->getShipmentCount():0);
                /*
                *  Shipment Data check Weekly
                */

                $myShipmentDataFilter   =   new ConsignmentFilter();
                $myShipmentDataFilter->addJoin(" `user` u"," u.id = c.user_id AND u.`user_account_id` = '".$userAccountId."'");
//                $myShipmentDataFilter->addFilter("        ua.id = '".$userAccountId."'" , 'userAccountFilter');
                $myShipmentDataFilter->addFilter("        c.date_label_created > 0 and c.shipment_status IN  ( '".implode("','",$conArrayStatus)."') " , 'filter');
                $myshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myShipmentCount    =   (count($myshipmentData)>0 ? $myshipmentData[0]->getShipmentCount():0);

                $myShipmentDataFilter->addFilter("        YEARWEEK(FROM_UNIXTIME(c.date_label_created) , 1) = YEARWEEK(CURDATE(), 1)  " , 'filter');
                $myWeeklyshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myWeeklyShipmentCount    =   (count($myWeeklyshipmentData)>0 ? $myWeeklyshipmentData[0]->getShipmentCount():0);
                
                
                $myShipmentDataFilter->addFilter("        (c.date_label_created >= ".strtotime(date("Y-m-d 00:00:00"))." AND c.date_label_created <= ".strtotime(date("Y-m-d 23:59:59")).")  " , 'filter');
                $myTodayshipmentData   =   $myShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $myTodayShipmentCount    =   (count($myTodayshipmentData)>0 ? $myTodayshipmentData[0]->getShipmentCount():0);
                /*
                *  Shipment Data check
                */

                $userAccountArry    =   CustomerAccount::accountSubAccount($userAccountObj->getid());
                $subShipmentDataFilter   =   new ConsignmentFilter();
                $subShipmentDataFilter->addJoin(" `user` u"," u.id = c.user_id AND u.user_account_id in ('" . implode("','", $userAccountArry) . "')");
                //$subShipmentDataFilter->addFilter("       ua.id in ( '".implode("','",$userAccountArry)."' ) " , 'userAccountFilter');
                //$subShipmentDataFilter->addFilter("     u.user_account_id in ('" . implode("','", $userAccountArry) . "')", 'userfilter');

                $subShipmentDataFilter->addFilter("       c.date_label_created > 0 and c.shipment_status IN  (  '".implode("','",$conArrayStatus)."') " , 'filter');
                $SubShipmentData   =   $subShipmentDataFilter->getListNew("count(c.id) as shipment_count ");
                $subShipmentCount    =   (count($SubShipmentData)>0 ? $SubShipmentData[0]->getShipmentCount():0);

                $subShipmentDataFilter->addFilter("        YEARWEEK(FROM_UNIXTIME(c.date_label_created) , 1) = YEARWEEK(CURDATE(), 1) " , 'filter');
                $SubWeeklyShipmentData   =   $subShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $subWeeklyShipmentCount    =   (count($SubWeeklyShipmentData)>0 ? $SubWeeklyShipmentData[0]->getShipmentCount():0);
                
                $subShipmentDataFilter->addFilter("        (c.date_label_created >= ".strtotime(date("Y-m-d 00:00:00"))." AND c.date_label_created <= ".strtotime(date("Y-m-d 23:59:59")).")  " , 'filter');
                $SubTodayShipmentData   =   $subShipmentDataFilter->getListNew("count(c.id) as shipment_count");
                $subTodayShipmentCount    =   (count($SubTodayShipmentData)>0 ? $SubTodayShipmentData[0]->getShipmentCount():0);

                /*
                *   Shipment data end
                */
                $servicesHtml = "";
                $servicesHtml .= "<ul>";

                foreach ($servicesLists as $servicesList) {
                    $servicesHtml .= '<li class="col-md-4">'.$servicesList->getName().'</li>';
                }
                $servicesHtml .= "</ul>";


                $html = '';
                $bankAccountInvoice = "";
                if($userAccountObj->getInvoiceBankDetailsId()>0)
                {
                    $bankDetailsIncoi   =   new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
                    $bankAccountInvoice .= "Account Title: ". $bankDetailsIncoi->getAccountTitle()."<br>";
                    $bankAccountInvoice .= "Sort Code: ". $bankDetailsIncoi->getAccountSortcode()."<br>";
                    $bankAccountInvoice .= "Account No.: ". $bankDetailsIncoi->getAccountNumber()."<br>";
                    $bankAccountInvoice .= "Branch: ". $bankDetailsIncoi->getBankBranch()."<br>";
                    $bankAccountInvoice .= "Bank: ". $bankDetailsIncoi->getBankName();
                }
                $balance = 0.00;
                $outstanding = 0.00;
                $balanceArr = getBalance($userAccountObj->getId());
                $accountType = "";
                if($isPrepaid == "Yes") {
                    $accountType = "Prepaid";
                    if(isset($balanceArr['balance'])) {
                        $balance = $balanceArr['balance'];
                    }
                } else {
                    if(isset($balanceArr['balance'])) {
                        $balance = $balanceArr['balance'];
                    }
                    if(isset($balanceArr['outstanding'])) {
                        $outstanding = $balanceArr['outstanding'];
                    }
                    $accountType = "Postpaid";
                }
                $html .= '  <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption"> <i class="fa fa-cubes"></i>
                                        '.$userAccountObj->getUserAccount().' Account Details
                                    </div>
                                    <div class="actions">
                                        '.((Permissions::checkFilePermission('usersection_manage_shipment'))?'<a href="client_list.php?saccount='.base64_encode($userAccountId).'" class="btn blue"><span></span><i class="fa fa-eye"></i>&nbsp;Manage Shipments</a>':'').'
                                        <a href="user_view.php?id='.base64_encode($userAccountId).'" class="btn blue"><span></span><i class="fa fa-eye"></i>&nbsp;View Profile</a>
                                    </div>
                                    <div class="tools"></div>
                                </div>
                                <div class="portlet-body">
                                <div class="row">
<div class="col-md-12">
                                            <div class="row" style="margin-bottom:20px;margin-top:10px;">';
                                    if($isPrepaid == "Yes") {        
                                       $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="payment_history.php?id=' . base64_encode($userAccountId ). '">
                                                        <div class="visual">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="details">
                                                            <div class="number">
                                                                <span data-counter="counterup text-center">
                                                                '.$accountType.'
                                                                </span>
                                                            </div>
                                                            <div class="desc">Account Type </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <a class="dashboard-stat dashboard-stat-v2 red theme-bg-primary" href="payment_history.php?id=' . base64_encode($userAccountId) . '">
                                                        <div class="visual">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="details">
                                                            <div class="number">
                                                                <span data-counter="counterup text-center">
                                                                '.$balance.'
                                                                </span>
                                                            </div>
                                                            <div class="desc">Account Balance </div>
                                                        </div>
                                                    </a>
                                                </div>';
                                    } else {
                                       $html .= '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <a class="dashboard-stat dashboard-stat-v2 blue theme-bg-primary" href="payment_history.php?id=' . base64_encode($userAccountId) . '">
                                                        <div class="visual">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="details">
                                                            <div class="number">
                                                                <span data-counter="counterup text-center">
                                                                '.$accountType.'
                                                                </span>
                                                            </div>
                                                            <div class="desc">Account Type </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <a class="dashboard-stat dashboard-stat-v2 red theme-bg-primary" href="payment_history.php?id=' . base64_encode($userAccountId) . '">
                                                        <div class="visual">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="details">
                                                            <div class="number">
                                                                <span data-counter="counterup text-center">
                                                                '.$balance.'
                                                                </span>
                                                            </div>
                                                            <div class="desc">Credit Limit </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <a class="dashboard-stat dashboard-stat-v2 green theme-bg-primary" href="#">
                                                        <div class="visual">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="details">
                                                            <div class="number">
                                                                <span data-counter="counterup text-center">
                                                                '.$outstanding.'
                                                                </span>
                                                            </div>
                                                            <div class="desc">Outstanding </div>
                                                        </div>
                                                    </a>
                                                </div>';
                                    }
                                   $html .= '</div>
                                        </div>                                
</div>
                                <div class="tabbable-line tabbable-custom-profile">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#tab_1_11" data-toggle="tab" aria-expanded="true">Overview </a>
                                        </li>
                                        <li class="">
                                            <a href="#tab_1_12" data-toggle="tab" aria-expanded="true"> Today\'s Stats </a>
                                        </li>
                                        <li class="">
                                            <a href="#tab_1_13" data-toggle="tab" aria-expanded="true"> This Week\'s Stats </a>
                                        </li>
                                        
                                    </ul>
                                    <div class="tab-content padding-top-0">
                                        <div class="tab-pane active" id="tab_1_11">
                                            <div class="portlet-body">
                                                <div class="row widget-row">
                                                
                                        <div class="col-md-4">
                                            <div class="portlet light profile-sidebar-portlet bordered" style="min-height: 194px;">
                                                <!-- SIDEBAR USERPIC -->
                                                <div class="profile-userpic">
                                                    <img src="'.$userLogo.'" class="img-responsive" alt="" style="max-height: 165px;"> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered"  style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Sub Accounts</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-green icon-badge"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childAccountsTotal.'"><a target="_blank" href="customers.php?parent_id='.base64_encode($userAccountId).'" >'.$childAccountsTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered" style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Users</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-red icon-user"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childUserTotal.'"><a target="_blank" href="list_users.php?account='.base64_encode($userAccountId).'" >'.$childUserTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>';

                                        if (!Permissions::checkFilePermission('hide_subaccount')) {
                                            $html .= '<!-- PORTLET MAIN -->
                                            <div class="col-md-12">
                                                <h4><i class="fa fa-users"></i> USERS  </h4>
                                                <div class="portlet light bordered">
                                                    <!-- STAT -->
                                                    <div class="row list-separated">
                                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                                            <div class="uppercase profile-stat-title"> '.$myUserCount.' </div>
                                                            <div class="uppercase profile-stat-text"><a href="#"> '.$userAccountObj->getUserAccount().' Users </a></div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                                            <div class="uppercase profile-stat-title"> '.$mySubAccountUserCount.' </div>
                                                            <div class="uppercase profile-stat-text"><a href="#"> Sub-Account Users </a></div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                                            <div class="uppercase profile-stat-title"> '.( $myUserCount + $mySubAccountUserCount ).' </div>
                                                            <div class="uppercase profile-stat-text"><a href="#"> Total Users  </a></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- PORTLET MAIN -->';
                                        }

                                        $html .= '<div class="col-md-12">
                                            <h4><i class="fa fa-shopping-cart"></i> LABEL SHIPMENTS </h4>
                                            <div class="portlet light bordered">
                                                <!-- STAT -->
                                                <div class="row list-separated">
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('own').'"> '.$userAccountObj->getUserAccount().' Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$subShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('sub').'"> Sub-Account Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.( $myShipmentCount + $subShipmentCount ).' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('all').'"> Total Shipments  </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_1_12">
                                            <div class="portlet-body">
                                                
                                    <div class="row widget-row">
                                        <div class="col-md-4">
                                            <div class="portlet light profile-sidebar-portlet bordered" style="min-height: 194px;">
                                                <!-- SIDEBAR USERPIC -->
                                                <div class="profile-userpic">
                                                    <img src="'.$userLogo.'" class="img-responsive" alt="" style="max-height: 165px;"> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered"  style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Sub Accounts</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-green icon-badge"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childTodayAccountsTotal.'"><a target="_blank" href="customers.php?parent_id='.base64_encode($userAccountId).'" >'.$childTodayAccountsTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered" style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Users</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-red icon-user"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childTodayUserTotal.'"><a target="_blank" href="list_users.php?account='.base64_encode($userAccountId).'" >'.$childTodayUserTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>
                                        <!-- PORTLET MAIN -->
                                        <div class="col-md-12">
                                            <h4><i class="fa fa-users"></i> USERS  </h4>
                                            <div class="portlet light bordered">
                                                <!-- STAT -->
                                                <div class="row list-separated">
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myTodayUserCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> '.$userAccountObj->getUserAccount().' Users </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myTodaySubAccounUserCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> Sub-Account Users </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.( $myTodayUserCount + $myTodaySubAccounUserCount ).' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> Total Users  </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- PORTLET MAIN -->
                                        <div class="col-md-12">
                                            <h4><i class="fa fa-shopping-cart"></i> LABEL SHIPMENTS </h4>
                                            <div class="portlet light bordered">
                                                <!-- STAT -->
                                                <div class="row list-separated">
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myTodayShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('own').'&daterange='.base64_encode('today').'&record=today"> '.$userAccountObj->getUserAccount().' Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$subTodayShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('sub').'&daterange='.base64_encode('today').'&record=today"> Sub-Account Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.( $myTodayShipmentCount + $subTodayShipmentCount ).' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('all').'&daterange='.base64_encode('today').'&record=today"> Total Shipments  </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_1_13">
                                            <div class="portlet-body">
                                                <div class="row widget-row">
                                        <div class="col-md-4">
                                            <div class="portlet light profile-sidebar-portlet bordered" style="min-height: 194px;">
                                                <!-- SIDEBAR USERPIC -->
                                                <div class="profile-userpic">
                                                    <img src="'.$userLogo.'" class="img-responsive" alt="" style="max-height: 165px;"> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered"  style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Sub Accounts</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-green icon-badge"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childWeeklyAccountsTotal.'"><a target="_blank" href="customers.php?parent_id='.base64_encode($userAccountId).'" >'.$childWeeklyAccountsTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>
                                        <div class="col-md-4">
                                            <!-- BEGIN WIDGET THUMB -->
                                            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered" style="min-height: 194px;">
                                                <h4 class="widget-thumb-heading">Users</h4>
                                                <div class="widget-thumb-wrap">
                                                    <i class="widget-thumb-icon bg-red icon-user"></i>
                                                    <div class="widget-thumb-body">
                                                        <span class="widget-thumb-subtitle"></span>
                                                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="'.$childWeeklyUserTotal.'"><a target="_blank" href="list_users.php?account='.base64_encode($userAccountId).'" >'.$childWeeklyUserTotal.'</a></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END WIDGET THUMB -->
                                        </div>
                                        <!-- PORTLET MAIN -->
                                        <div class="col-md-12">
                                            <h4><i class="fa fa-users"></i> USERS  </h4>
                                            <div class="portlet light bordered">
                                                <!-- STAT -->
                                                <div class="row list-separated">
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myWeeklyUserCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> '.$userAccountObj->getUserAccount().' Users </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myWeeklySubAccountUserCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> Sub-Account Users </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.( $myWeeklyUserCount + $myWeeklySubAccountUserCount ).' </div>
                                                        <div class="uppercase profile-stat-text"><a href="#"> Total Users  </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- PORTLET MAIN -->
                                        <div class="col-md-12">
                                            <h4><i class="fa fa-shopping-cart"></i> LABEL SHIPMENTS </h4>
                                            <div class="portlet light bordered">
                                                <!-- STAT -->
                                                <div class="row list-separated">
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$myWeeklyShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('own').'&daterange='.base64_encode('weekly').'&record=week"> '.$userAccountObj->getUserAccount().' Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.$subWeeklyShipmentCount.' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('sub').'&daterange='.base64_encode('weekly').'&record=week"> Sub-Account Shipments </a></div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                                        <div class="uppercase profile-stat-title"> '.( $myWeeklyShipmentCount + $subWeeklyShipmentCount ).' </div>
                                                        <div class="uppercase profile-stat-text"><a href="client_list.php?saccount='.base64_encode($userAccountId).'&filtertype='.base64_encode('all').'&daterange='.base64_encode('weekly').'&record=week"> Total Shipments  </a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="row widget-row">
                                        
                                        <!-- END PORTLET MAIN -->
                                        <div class="col-md-12">
                                            <h4><i class="fa fa-user"></i> ACCOUNTS</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="active label_new" width="20%"> User Code </td>
                                                        <td width="30%"> '.$userAccountObj->getUserCode().' </td>
                                                        <td class="active label_new" width="20%"> Account Number </td>
                                                        <td width="30%"> '.$userAccountObj->getUserAccount().'</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="active label_new" > Parent Account </td>
                                                        <td> '.$userAccountParentObj->getUserAccount().' </td>
                                                        <td class="active label_new"> Active </td>
                                                        <td> '.$activeFlag.'</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h4><i class="fa fa-phone"></i> CONTACT INFORMATION</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="active label_new" width="20%"> Company </td>
                                                        <td width="30%"> '.$userAccountObj->getCompany().' </td>
                                                        <td class="active label_new" width="20%"> Contact Name </td>
                                                        <td width="30%"> '.$userAccountObj->getFullName().'</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="active label_new" > Telephone Number </td>
                                                        <td> '.$userAccountObj->getTelephone().' </td>
                                                        <td class="active label_new" > Email Address </td>
                                                        <td> '.$userAccountObj->getEmail().' </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="active label_new"> Alternative Email Address </td>
                                                        <td> '.$userAccountObj->getAlternativeEmail().'</td>
                                                        <td class="active label_new"> Country </td>
                                                        <td> '.$country->getName().'</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="active label_new"> Website Link </td>
                                                        <td> '.$userAccountObj->getWebsiteLink().'</td>
                                                        <td class="active label_new"> Retrun Address </td>
                                                        <td> '.$userAccountObj->getReturnAddress().'</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h4><i class="fa fa-money"></i> BANK ACCOUNT DETAILS</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="label_new active" width="20%">Account Title</td>
                                                        <td width="30%"> '.$userAccountObj->getBankAccountTitle().' </td>
                                                        <td class="label_new active" width="20%">Sort Code</td>
                                                        <td width="30%"> '.$userAccountObj->getBankSortcode().' </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="label_new active">Account Number</td>
                                                        <td> '.$userAccountObj->getBankAccountNumber().' </td>
                                                        <td class="label_new active">Branch Address</td>
                                                        <td> '.$userAccountObj->getBankBranchAddress().' </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                             <h4><i class="fa fa-registered"></i> COMPANY REGISTRATION DETAILS</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="label_new active" width="20%"> Registration Number </td>
                                                        <td width="30%"> '.$userAccountObj->getRegNumber().'  </td>
                                                        <td class="label_new active" width="20%"> Registered Address </td>
                                                        <td width="30%"> '.$userAccountObj->getRegAddress().'  </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="label_new active"> Registration Postcode </td>
                                                        <td> '.$userAccountObj->getRegPostcode().'  </td>
                                                        <td class="label_new active"> Registered Country </td>
                                                        <td colspan="3"> '.$userAccountObj->getRegCountry().'  </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h4><i class="fa fa-file-text-o"></i> INVOICE DETAILS</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="label_new active" width="20%">Currency</td>
                                                        <td width="30%"> '.$userAccountObj->getBillingCurrency().' </td>
                                                        <td class="label_new active" width="20%">Invoice Period</td>
                                                        <td width="30%"> '.ucfirst($userAccountObj->getInvoicePeriod()).' </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="label_new active">Bank Account to Display On Invoices</td>
                                                        <td> '.$bankAccountInvoice.' </td>
                                                        <td class="label_new active">Billing Address</td>
                                                        <td> '.$userAccountObj->getBillingAddress().' </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="label_new active">Billing Email</td>
                                                        <td> '.$userAccountObj->getBillingEmail().' </td>
                                                        <td class="label_new active">VAT Chargable</td>
                                                        <td> '.$vatChargable.' </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="label_new active">VAT Percentage</td>
                                                        <td> '.$userAccountObj->getVatValue().' </td>
                                                        <td class="label_new active">Prepaid</td>
                                                        <td> '.$isPrepaid.' </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h4><i class="fa fa-list"></i> SERVICES CONNECTED</h4>
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4">
                                                            '.$servicesHtml.'
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                echo $html;
            }
            die;
        }
        //Handle Tree ajax data
        if (isset($_GET["func"]) && $_GET["func"] == 'get_tree_data') {
            $user = SessionManager::getUser();
            $parent = 0;
            if(isset($_GET['force_parent']) && isset($_GET['force_parent']) > 0 && $_GET["parent"] == '#'){
                $parent = $_GET['force_parent'];
            }else{
                $parent = $_GET["parent"];
            }
            if($this->userData->getUserType() == User::USER_TYPE_CORPORATE && ($parent == 0 || $parent == NULL)){
                $parent = $this->userData->getUserAccountId();
            }
            $allowedCliendIn = "";
            if(count($this->allowedAccounts) > 0){
                $allowedCliendIn = " AND id IN (".implode(",", $this->allowedAccounts).")";
            }
            if (Permissions::checkFilePermission('hide_subaccount')) {
                $parent = $this->userData->getUserAccountId();
                $allowedCliendIn = "";
            }
            $parentWhere = "parentid = '".DbAccess3::escape($parent)."'";
            if($parent == 0 ){
                $parentWhere .= " OR parentid IS NULL";
            }
            $data = array();
            $userAccountFilter = new UserAccountFilter();
            $userAccountFilter->addFilter("(" . $parentWhere . ") AND active_flag = 1".$allowedCliendIn);
            $userAccountList = $userAccountFilter->getList();
            if (count($userAccountList) > 0) {
                foreach ($userAccountList as $key => $COAArr) {
                    $parent1 = $COAArr->getId();
                    $userAccountFilterNew = new UserAccountFilter();
                    $userAccountFilterNew->addFilter("parentid = '" . $parent1 . "' AND active_flag = 1".$allowedCliendIn);
                    $COARes1 = $userAccountFilterNew->getCount();
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $COARes1 = [];
                    }
                    $data1["id"] = $parent1;
                    $data1["text"] = $COAArr->getUserAccount();
                    $data1["children"] = (count($COARes1) > 0 ? true : false);
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $parent1 = 0;
                    }
                    if ($parent == 0) {
                        $data1["type"] = "root";
                    }
                    if (isset($_GET['COAID']) && $COAArr["COAID"] == $_GET['COAID']) {
                        $data1["state"] = "{opened:true, selected:true}";
                    } else if ($parent == 0) {
                        $data1["state"] = "{opened:true}";
                    }
                    $data[] = $data1;
                }
            }//exit;
//            echo '<pre>';
//            print_r($data);
//            echo '</pre>';
//            die;
            header('Content-type: text/json');
            header('Content-type: application/json');
            echo json_encode($data);
            exit;
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>

        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST">
            <div class="col-md-4">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"> <i class="fa fa-tree"></i>
                            List Account Hierarchical View
                        </div>
                        <div class="actions">
<!--                            <a href="add_permission.php" class="btn blue"><span></span><i class="fa fa-lock"></i>&nbsp;<?=Translation::GetCaption("ADD_PERMISSIONS");?></a>
                            <a href="groups_list.php" class="btn blue"><span></span><i class="fa fa-list"></i>&nbsp;<?php echo Translation::GetCaption("VIEW_ALL_GROUPS"); ?></a>-->
                            <input class="btn blue" type="button" value="Collapse All" onclick="$('#tree_2').jstree('close_all');">
                            <!--<input class="btn blue" type="button" value="Expand All" onclick="$('#tree_2').jstree('open_all');">-->
                        </div>
                        <div class="tools"> </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-none"  id="res_message" ></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php
                                                $account = (int) $this->account_id;
                                                $userAccount = new CustomerAccount($this->userData->getUserAccountId());
                                                if($account > 0){
                                                    $userCurAccount = new CustomerAccount($account);
                                                    echo '<span style="cursor: pointer;" class="jstree-anchor" id="'.$account.'_anchor">'.ucfirst($userCurAccount->getUserAccount()).'</span>';
                                                }
                                                else if($userAccount->getParentid() > 0){
                                                    echo '<span style="cursor: pointer;" class="jstree-anchor" id="'.$this->userData->getUserAccountId().'_anchor">'.ucfirst($userAccount->getUserAccount()).'</span>';
                                                } else{
                                                    echo "User Accounts";
                                                }
                                                
                                            ?>
                                    </label><br />
                                    <div id="tree_2" class="tree-demo"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div id="apend_user_details"></div>
            </div><!--end col-md-8> -->
            <input type="hidden" name="id" id="id" value="<?php echo (isset($_GET['gid']) && base64_decode(trim($_GET['gid'])) > 0 ? base64_decode(trim($_GET['gid'])) : "" ) ?>" />
            <input type="hidden" name="form_action" id="form_action" value="" />
            <input type="hidden" name="parent_id" id="parent_id" value="" />
        </form>
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

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                UITree.init();                
                $(document).on('click', '.jstree-anchor', function () {
                   var accountId = 0;
                   var accountStr = "";
                   accountStr = $(this).attr('id');
                   accountId =  accountStr.replace('_anchor','');
                   $.ajax({
                        type: "POST",
                        url: "account_hierarchical_view.php",
                        data: { 'func':'get_user_account_details','account_id':accountId },
                        dataType: "html",
                        success: function(data) {
                            $("#apend_user_details").html("");
                            $("#apend_user_details").html(data);
                        },
                        error: function() {
                            alert('error handing here');
                        }
                    });

                });
            });
            var UITree = function () {
                var tree = function () {
                    $("#tree_2").bind("loaded.jstree", function (event, data) {
                        $('.jstree-anchor:first').trigger('click');
                    }).jstree({
                        "core": {
                            "themes": {
                                "responsive": false,
                                "icons": false
                            },
                            //"keep_selected_style": false,
                            // so that create works
                            "check_callback": false,
                            'data': {
                                'url': function (node) {
                                return 'account_hierarchical_view.php';
                                },
                                'data': function (node) {
                                return {'func': 'get_tree_data', 'parent': node.id <?php echo (isset($_GET["Update"]) && isset($_GET["COAID"]) ? ", 'COAID':" . $_GET["COAID"] : ""); ?>,'force_parent':'<?php echo ((!isset($this->account_id) && !is_integer((int) $this->account_id)) ? 0: (int) $this->account_id); ?>'};
                                }

                            }
                        },
                        "checkbox": {
                            three_state: false,
                            cascade: 'down'
                        },
                        "plugins": ["ui"]
                    })
                }
                return {
                //main function to initiate the module
                    init: function () {
                        tree();
                    }
                };
            }();
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
