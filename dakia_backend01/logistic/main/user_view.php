<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'documenttype.class',
    'documenttypefilter.class',
    'userdocument.class',
    'userdocumentfilter.class',
    'tariffsaccountmapping.class',
    'tariffsaccountmappingfilter.class',
    'services.class',
    'servicesfilter.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class',
    'marketplaces.class',
    'marketplacesfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'invoicebankdetails.class',
    'invoicebankdetailsfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'tariffadditionalcharges.class',
    'tariffadditionalchargesfilter.class',
    'useraccountadditionalcontactsfilter.class',
    'useraccountadditionalcontacts.class'
]);
/* * *
 * Page for details a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $userDetails;
    private $userTotalBalance;
    private $tariffsServiceFilterObjs;
    private $account_id = 0 ;
    private $account_id_encode = '';

    protected function init() {
        $this->account_id = base64_decode(util_get("id"));
        $this->account_id_encode = util_get("id");
        
        if ($this->account_id <= 0) {
            util_redirect("customers.php");
        }
        $sessionUser = SessionManager::getUser();

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'customers.php' => 'Account',
            Translation::GetCaption("Account DETAIL")
        );
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $id = $this->account_id;
        $user = new UserAccountFilter();
        $user->addIdFilter($id);
//        $user->addFilter($account_number);
        $this->userDetails = $user->getList();
        /* Get user balance */
        if ($id > 0) {
            $this->userTotalBalance = getBalance($id);
        } else {
            $this->userTotalBalance = getBalance($sessionUser->getUserAccountId());
        }
        
        // Get All Tariff
        $accountId = $sessionUser->getUserAccountId();
        if($this->account_id > 0 ){
            $accountId = $this->account_id;
        }
        
        $tariffAccountFilter = new TariffsAccountMappingFilter();
        $tariffAccountFilter->addUserAccountFilter($accountId);
        $tariffAccountFilterData = $tariffAccountFilter->getTariffNameDistinctList("t.name as tariff_name,t.service_id,t.id");
        if(count($tariffAccountFilterData)) {
            foreach($tariffAccountFilterData as $tariffAccountFilterObj) {
                $this->tariffsServiceFilterObjs[$tariffAccountFilterObj->getServiceId()][] =  $tariffAccountFilterObj;
            }
        }
    }

    protected function renderHead() {
        
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>  
        <script src="../assets/pages/scripts/profile.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>

        <script type="text/javascript">
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    public function renderBody() {
        $sessionUser = SessionManager::getUser();
        $userDetails = $this->userDetails[0];
        ?>
        <!-- BEGIN PAGE BASE CONTENT -->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar">
                    <!-- PORTLET MAIN -->
                    <div class="portlet light profile-sidebar-portlet bordered">
                        <!-- SIDEBAR USERPIC -->
                        <div class="profile-userpic">
                            <?php
                            $logoImage = "../_assets/images/profile/default_profile.png";
                            if (!empty($userDetails->getLogo()))
                                $logoImage = '../images/userlogo/' . $userDetails->getLogo();
                            if (empty($logoImage) || !file_exists($logoImage)) {
                                $logoImage = "../_assets/images/profile/default_profile.png";
                            }
//                                if(!empty($userDetails->getProfileImage()))
//                                    $userProfile = '../_assets/profile_images/'.$userDetails->getProfileImage();
                            ?>
                            <img src="<?php echo $logoImage; ?>" class="img-responsive" alt=""> </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name"> <?php echo $userDetails->getFullName(); ?> </div>
                            <div class="profile-usertitle-job"> <?php echo $userDetails->getCompany(); ?> [ <?php echo $userDetails->getUserAccount(); ?> ]</div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- SIDEBAR MENU -->
                        <!-- END MENU -->
                    </div>
                    <!-- END PORTLET MAIN -->
                    <!-- PORTLET MAIN -->
                    <div class="portlet light bordered">
                        <!-- STAT -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;">
                                    <?php
                                    /*
                                     *  Shipment Data check
                                     */
                                    $conArrayStatus = array(
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
                                        Consignment::STATUS_PROBLEM,
                                        Consignment::STATUS_RELABLED,
                                        Consignment::STATUS_DISCREPANCY,
                                        Consignment::STATUS_AWATING_CLAIM);

                                    $myShipmentDataFilter = new ConsignmentFilter();
                                    $myShipmentDataFilter->addFilter("        ua.id = '" . $userDetails->getId() . "'", 'userAccountFilter');
                                    $myShipmentDataFilter->addFilter("        c.date_label_created > 0 and c.shipment_status IN  ( '" . implode("','", $conArrayStatus) . "') ", 'consignmentfilter');
                                    $myshipmentData = $myShipmentDataFilter->getColumnList("count(c.id) as shipment_count");
                                    echo $myShipmentCount = (count($myshipmentData) > 0 ? $myshipmentData[0]->getShipmentCount() : 0);
                                    ?> 
                                </div>
                                <div class="uppercase profile-stat-text"> Shipments </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;"> 
                                    <?php
                                    $accountBalance = "0.00 GBP";
                                    if (count($this->userTotalBalance) > 0) {
                                        $accountBalance = $this->userTotalBalance['balance'];
                                    }
                                    echo $accountBalance;
                                    ?>
                                </div>
                                <div class="uppercase profile-stat-text"> <?php echo ($userDetails->getIsPrepaid() == "1") ? "Prepaid" : "Postpaid"; ?> </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;">
                                    <?php
                                    $invoiceTotal = 0;
                                    $invoiceTotalTemp1 = 0;
                                    $invoiceTotalTemp2 = 0;
                                    $this->invoiceFilter = new InvoiceFilter();
                                    $this->invoiceFilter->join("user u", ['inv.added_by' => 'u.id']);
                                    $this->invoiceFilter->orWhere(['inv.invoice_by' => $userDetails->getId(), 'inv.user_account_id' => $userDetails->getId()]);
                                    echo $iTotalRecords = $this->invoiceFilter->getCount(false);
                                    ?>
                                </div>
                                <div class="uppercase profile-stat-text"> Invoices </div>
                            </div>
                        </div>
                        <!-- END STAT -->
                        <div>
                            <h4 class="profile-desc-title">Billing Address</h4>
                            <span class="profile-desc-text"> <?php echo $userDetails->getBillingAddress(); ?> </span>
                            <?php if (!empty($userDetails->getEmail())) { ?>
                                <div class="margin-top-20 profile-desc-link">
                                    <i class="fa fa-envelope"></i>
                                    <a href="mailto:<?php echo $userDetails->getEmail(); ?>"><?php echo $userDetails->getEmail(); ?></a>
                                </div>
                                <?php
                            }
                            if (!empty($userDetails->getAlternativeEmail())) {
                                ?>
                                <div class="margin-top-20 profile-desc-link">
                                    <i class="fa fa-envelope"></i>
                                    <a href="mailto:<?php echo $userDetails->getAlternativeEmail(); ?>"><?php echo $userDetails->getAlternativeEmail(); ?></a>
                                </div>
                                <?php
                            }
                            if (!empty($userDetails->getTelephone())) {
                                ?>
                                <div class="margin-top-20 profile-desc-link">
                                    <i class="fa fa-phone"></i>
                                    <a href="javascript:void(0)"><?php echo $userDetails->getTelephone(); ?> </a>
                                </div>
                                <?php
                            }
                            if (!empty($userDetails->getUserAccount())) {
                                ?>
                                <div class="margin-top-20 profile-desc-link">
                                    <i class="fa fa-desktop"></i>
                                    <a href="javascript:void(0)"><?php echo $userDetails->getUserAccount(); ?></a>
                                </div>
                                <?php
                            }
                            if (!empty($userDetails->getCountryId())) {
                                ?>
                                <div class="margin-top-20 profile-desc-link">
                                    <i class="fa fa-globe"></i>
                                    <a href="javascript:void(0)"><?php
                                        $country = new Country($userDetails->getCountryId());
                                        echo $country->getName();
                                        ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- END PORTLET MAIN -->
                </div>
                <!-- END BEGIN PROFILE SIDEBAR -->
                <!-- BEGIN PROFILE CONTENT -->
                <div class="profile-content smart-legend">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light bg-inverse">
                                <div class="portlet-title tabbable-line">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#account_tab" data-toggle="tab">Basic</a>
                                        </li>
                                        <li>
                                            <a href="#company_tab" data-toggle="tab">Billing</a>
                                        </li>
                                        <li>
                                            <a href="#services_tab" data-toggle="tab">Service</a>
                                        </li>
                                        <li>
                                            <a href="#other_tab" data-toggle="tab">Other</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="portlet-body">
                                    <div class="tab-content">
                                        <!-- GENERAL QUESTION TAB -->
                                        <div class="tab-pane active" id="account_tab">
                                            <div class="portlet light">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="icon-user"></i>
                                                        <span class="caption-subject bold uppercase">Accounts</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <fieldset class="margin-top-0">
                                                        <legend><i class="fa fa-user"></i> Account Information</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> User Code </td>
                                                                        <td> <?php echo $userDetails->getUserCode(); ?> </td>
                                                                        <td class="label_new"> Account Number </td>
                                                                        <td> <?php echo $userDetails->getUserAccount(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Parent Account </td>
                                                                        <td>
                                                                            <?php
                                                                            $userAccount = new CustomerAccount($userDetails->getParentid());
                                                                            echo $userAccount->getUserAccount();
                                                                            ?>
                                                                        </td>
                                                                        <td class="label_new">Active </td>
                                                                        <td> <?php echo ($userDetails->getActiveFlag() == "1") ? "Yes" : "NO"; ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-phone"></i> Contact Information</legend>

                                                        <div class="table-scrollable">

                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Company </td>
                                                                        <td> <?php echo $userDetails->getCompany(); ?> </td>
                                                                        <td class="label_new"> Full Name </td>
                                                                        <td> <?php echo $userDetails->getFullName(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Telephone Number </td>
                                                                        <td> <?php echo $userDetails->getTelephone(); ?> </td>
                                                                        <td class="label_new"> Owner Email Address </td>
                                                                        <td> <?php echo $userDetails->getEmail(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Email Address (CS, Operations) </td>
                                                                        <td> <?php echo $userDetails->getAlternativeEmail(); ?> </td>
                                                                        <td class="label_new"> Country </td>
                                                                        <td>
                                                                            <?php
                                                                            $country = new Country($userDetails->getCountryId());
                                                                            echo $country->getName();
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Website Link </td>
                                                                        <td> <?php echo $userDetails->getWebsiteLink(); ?> </td>
                                                                        <td class="label_new"> Retrun Address </td>
                                                                        <td> <?php echo $userDetails->getReturnAddress(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            
                                                        </div>

                                                    </fieldset>
                                                    <?php
                                                        $this->userAccountAdditionalContactsFilter = new UserAccountAdditionalContactsFilter();
                                                        $this->userAccountAdditionalContactsFilter->addFieldFilter('uaac.user_account_id', $this->account_id);
                                                        $userAccountAdditionalContactsList = $this->userAccountAdditionalContactsFilter->getList();
                                                       if(count($userAccountAdditionalContactsList)>0){
                                                           ?>
                                                    <fieldset>
                                                            <legend><i class="fa fa-phone"></i> Additional Contact Information</legend>

                                                        <div class="table-scrollable">
                                                            <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                                                <thead>
                                                                    <tr role="row" class="heading">
                                                                        <th>Name</th>
                                                                        <th>Department</th>
                                                                        
                                                                        <th>Email</th>
                                                                        <th>Phone</th>
                                                                    </tr>
                                                                </thead>
                                                                <?php 
                                                                foreach($userAccountAdditionalContactsList as $itemdata){
                                                                ?>
                                                                    <tr role="row" class="heading">
                                                                        <td><?php echo $itemdata->getTitle()." ".$itemdata->getName();?></td>
                                                                        <td><?php echo $itemdata->getContactType();?></td>
                                                                        <td><?php echo $itemdata->getEmail();?></td>
                                                                        <td><?php echo $itemdata->getPhone();?></td>
                                                                    </tr>
                                                                    <?php     
                                                                }?>
                                                            </table>
                                                        </div>

                                                    </fieldset>
                                                               <?php
                                                       }
                                                        ?>
                                                    
                                                    <?php
                                                    $theme = "";
                                                    if ($userDetails->getThemeId() == 1) {
                                                        $theme = "Handlerbund";
                                                    } else if ($userDetails->getThemeId() == 2) {
                                                        $theme = "Ukmail";
                                                    }
                                                    ?>
                                                    <fieldset>
                                                        <legend><i class="fa fa-cog"></i> Customization</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Weight </td>
                                                                        <td> <?php echo number_format((float) $userDetails->getDefaultWeight(), 3, '.', ''); ?> KG</td>
                                                                        <td class="label_new"> Notes </td>
                                                                        <td> <?php echo $userDetails->getDefaultNotes(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Description </td>
                                                                        <td> <?php echo $userDetails->getDefaultDescription(); ?> </td>
                                                                        <td class="label_new"> Theme </td>                               
                                                                        <td> <?php echo $theme; ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-building"></i>
                                                        <span class="caption-subject bold uppercase">Company Details</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <fieldset class="margin-top-0">
                                                        <legend><i class="fa fa-money"></i> Bank Account Details</legend>

                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new">Account Title</td>
                                                                        <td> <?php echo $userDetails->getBankAccountTitle(); ?> </td>
                                                                        <td class="label_new">Sort Code</td>
                                                                        <td> <?php echo $userDetails->getBankSortcode(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new">Account Number</td>
                                                                        <td> <?php echo $userDetails->getBankAccountNumber(); ?> </td>
                                                                        <td class="label_new">Branch Address</td>
                                                                        <td> <?php echo $userDetails->getBankBranchAddress(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-male"></i> Trade Reference 1</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Reference Name </td>
                                                                        <td> <?php echo $userDetails->getTradeNameI(); ?> </td>
                                                                        <td class="label_new"> Reference Address </td>
                                                                        <td> <?php echo $userDetails->getTradeAddressI(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Reference Email </td>
                                                                        <td> <?php echo $userDetails->getTradeEmailI(); ?> </td>
                                                                        <td class="label_new"> Reference Phone Number </td>
                                                                        <td> <?php echo $userDetails->getTradePhoneI(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-male"></i> Trade Reference 2</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Reference Name </td>
                                                                        <td> <?php echo $userDetails->getTradeNameIi(); ?> </td>
                                                                        <td class="label_new"> Reference Address </td>
                                                                        <td> <?php echo $userDetails->getTradeAddressIi(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Reference Email </td>
                                                                        <td> <?php echo $userDetails->getTradeEmailIi(); ?> </td>
                                                                        <td class="label_new"> Reference Phone Number </td>
                                                                        <td> <?php echo $userDetails->getTradePhoneIi(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-registered"></i> Company Registration Details</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Registration Number </td>
                                                                        <td> <?php echo $userDetails->getRegNumber(); ?> </td>
                                                                        <td class="label_new"> Registered Address </td>
                                                                        <td> <?php echo $userDetails->getRegAddress(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Registration Postcode </td>
                                                                        <td> <?php echo $userDetails->getRegPostcode(); ?> </td>
                                                                        <td class="label_new"> Registered Country </td>
                                                                        <td colspan="3"> <?php echo $userDetails->getRegCountry(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-envelope"></i> Email Signature</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Signature </td>
                                                                        <td colspan="3"> <?php echo $userDetails->getUserSignature(); ?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-file"></i> Company Documents</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="4">                                                                
                                                                            <div class="row">
                                                                                <?php
                                                                                if ($userDetails->getId() > 0) {
                                                                                    $userDocumentFilter = new userDocumentFilter();
                                                                                    $userDocumentLists = $userDocumentFilter->getUserDoc($userDetails->getId());
                                                                                    foreach ($userDocumentLists as $userDocumentList) {
                                                                                        $temp = explode(".", $userDocumentList->getDocumentName());
                                                                                        $extension = end($temp);
                                                                                        $fileFullPath = "../_assets/user_documents/" . $userDetails->getId() . "/" . $userDocumentList->getDocumentName();
                                                                                        if (!file_exists($fileFullPath)) {
                                                                                            $fileFullPath = "../images/No-image-found.jpg";
                                                                                        }
                                                                                        if ($extension == "pdf") {
                                                                                            $fileFullPath = "../images/pdf.png";
                                                                                        }
                                                                                        if (in_array($extension, array( "xls","xlsx"))) {
                                                                                            $fileFullPath = "../images/xls.png";
                                                                                        }
                                                                                        if (in_array($extension, array( "doc","docx"))) {
                                                                                            $fileFullPath = "../images/word.png";
                                                                                        }
                                                                                        ?>
                                                                                        <div class="col-md-3">
                                                                                            <div class="thumbnail">
                                                                                                <div class="document-thumb">
                                                                                                    <img src="<?php echo $fileFullPath ?>" alt="<?php echo $userDocumentList->getDocumentName(); ?>" data-src="<?php echo $fileFullPath ?>">
                                                                                                </div>
                                                                                                <div class="caption">
                                                                                                    <h3><?php echo $userDocumentList->getDocumentId(); ?></h3>
                                                                                                    <a target="_blank" href="../_assets/user_documents/<?php echo $userDetails->getId() . "/" . $userDocumentList->getDocumentName(); ?>" class="btn blue"> View </a>
                                                                                                    </p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div><!--End Account Tab -->
                                        <div class="tab-pane" id="company_tab">
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-file-text-o font-blue-madison"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Billing Detail </span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <fieldset class="margin-top-0">
                                                        <legend><i class="fa fa-file-text-o"></i> Invoice Details</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new">Currency</td>
                                                                        <td> <?php echo $userDetails->getBillingCurrency(); ?> </td>
                                                                        <td class="label_new">Invoice Period</td>
                                                                        <td> <?php echo ucfirst($userDetails->getInvoicePeriod()); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new">Bank Account to Display On Invoices</td>
                                                                        <td> <?php
                                                                            if ($userDetails->getInvoiceBankDetailsId() > 0) {
                                                                                $bankDetailsIncoi = new InvoiceBankDetails($userDetails->getInvoiceBankDetailsId());
                                                                                echo "Account Title: " . $bankDetailsIncoi->getAccountTitle() . "<br>";
                                                                                echo "Sort Code: " . $bankDetailsIncoi->getAccountSortcode() . "<br>";
                                                                                echo "Account No.: " . $bankDetailsIncoi->getAccountNumber() . "<br>";
                                                                                echo "Branch: " . $bankDetailsIncoi->getBankBranch() . "<br>";
                                                                                echo "Bank: " . $bankDetailsIncoi->getBankName();
                                                                            }
                                                                            ?> </td>
                                                                        <td class="label_new">Billing Address</td>
                                                                        <td> <?php echo $userDetails->getBillingAddress(); ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new">Billing Email</td>
                                                                        <td> <?php echo $userDetails->getBillingEmail(); ?> </td>
                                                                        <td class="label_new">VAT Chargable</td>
                                                                        <td> <?php echo ($userDetails->getVatChargable() == "0") ? "Yes" : "NO"; ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new">VAT Percentage</td>
                                                                        <td> <?php echo $userDetails->getVatValue(); ?> </td>
                                                                        <td class="label_new"> <?php echo ($userDetails->getIsPrepaid() == "1") ? "Prepaid" : "Post Paid"; ?> </td>
                                                                        <td> <?php echo ($userDetails->getIsPrepaid() == "1") ? "" : "Credit Limit ".$userDetails->getCreditLimit()." ".$userDetails->getBillingCurrency() ;?> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-truck"></i> Tarrif Details</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new">Included Fuel Charges</td>
                                                                        <td> <?php echo ($userDetails->getIsFuelchargesInclude() == "1") ? "Yes" : "NO"; ?> </td>
                                                                        <td class="label_new">Use Assigned Tariff</td>
                                                                        <td> <?php echo ($userDetails->getOwnTariff() == "1") ? "Yes" : "NO"; ?> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new">Fuel Charges</td>
                                                                        <td> <?php echo $userDetails->getFuelCharges(); ?> </td>
                                                                        <td class="label_new"><!-- Tariff Name --> </td>
                                                                        <td> 
                                                                            <?php
                                                                            // Get user tariff
//                                                                            $tfilter = new TariffsAccountMappingFilter();
//                                                                            $tfilter->addUserAccountFilter($userDetails->getId());
//                                                                            $tlist = $tfilter->getTariffNameDistinctList("t.name 'tariff_name'");
//                                                                            $tariffName = "";
//                                                                            if (count($tlist) > 0) {
//                                                                                foreach ($tlist as $t) {
//                                                                                    $tariffName .= $t->getTariffName() . " ,";
//                                                                                }
//                                                                            }
//                                                                            echo rtrim($tariffName, ' ,');
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php if(count($this->tariffsServiceFilterObjs)) { ?>
                                                                    <tr>
                                                                        <td colspan="4" class="label_new text-center">Assigned Tariffs</td>
                                                                    </tr>
                                                                    <?php foreach($this->tariffsServiceFilterObjs as $serviceId => $tariffsFilterObjs) { ?>
                                                                        <?php $serviceObj = new Services($serviceId) ?>
                                                                        <tr>
                                                                            <td colspan="4" class="label_new"><?php echo $serviceObj->getName() ?></td>
                                                                        </tr>
                                                                        <?php foreach($tariffsFilterObjs as $key => $tariffsFilterObj) { ?>
                                                                            <?php $record = $key+1; ?>
                                                                            <?php if($key == 0) { ?> <tr> <?php } ?>
                                                                                <td class="">
                                                                                    <a href="tariff_add.php?id=<?php echo $tariffsFilterObj->getId() ?>"><?php echo $tariffsFilterObj->getTariffName(); ?></a>
                                                                                </td>
                                                                            <?php if(($record%4 ==  0) && $key > 0) { ?> </tr> <tr> <?php } ?>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                </div>    
                                            </div>
                                        </div><!--End Company Tab -->
                                        <div class="tab-pane" id="services_tab">
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-files-o font-blue-madison"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Services</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <ul>
                                                                 <?php
                                                                    $servicesLists = Services::getServiceMapping($userDetails->getId());
                                                                    foreach ($servicesLists as $servicesList) {
                                                                        $image = '../images/carrierlogo/thumbnail/owe_16_'. $servicesList->getCarrierLogo();
                                                                        echo '<li class="col-md-4" style="list-style-image: url('.$image.')"> <a href="user_account_service_charges.php?service_id=' . $servicesList->getId() .  '&account_id=' . $this->account_id . '" style="display: block;padding: 8px 0px;" >' . $servicesList->getName() . '</a></li>';
                                                                    }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--End Service Tab -->
                                        <div class="tab-pane" id="other_tab">
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-files-o font-blue-madison"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Sales Details</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label_new"> Sales Person </td>
                                                                    <td> <?php echo $userDetails->getSalesPerson(); ?> </td>
                                                                    <td class="label_new"> Sale Date </td>
                                                                    <td>
                                                                        <?php
                                                                        if (empty($userDetails->getSaleDate()) || $userDetails->getSaleDate() == '0000-00-00 00:00:00') {
                                                                            echo '-';
                                                                        } else {
                                                                            echo formatDate($userDetails->getSaleDate());
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Sales POT </td>
                                                                    <td> <?php echo ($userDetails->getCheckListSalesPot() == "1") ? "Yes" : "NO"; ?> </td>
                                                                    <td class="label_new"> Pot Time Period </td>
                                                                    <td> <?php echo $userDetails->getSalesPotTimePeriod() . " Months"; ?> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Sales Pot Percentage </td>
                                                                    <td colspan="3"> <?php echo $userDetails->getSalesPotPercentage() . " %"; ?> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-plug font-blue-madison"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Third Party Details</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label_new"> Third Party Connected </td>
                                                                    <td colspan="3">
                                                                        <?php
                                                                        //GET user platforms
                                                                        $UserMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
                                                                        $UserMarketPlacesMappingFilter->addUserIdFilter(trim($this->account_id));
                                                                        $UserData = $UserMarketPlacesMappingFilter->getList();
                                                                        if (count($UserData) > 0) {
                                                                            foreach ($UserData as $U) {
                                                                                $this->selectedShoppingPlatfrom[$U->getMarketPlacesId()] = $U->getAuthData();
                                                                            }
                                                                        }
                                                                        // GET All Shopping Platform List
                                                                        $MarketPlacesFilter = new MarketPlacesFilter();
                                                                        $MarketPlaces = $MarketPlacesFilter->getShoppingAndAuthenticateData();
                                                                        $PlatformArray = array();
                                                                        foreach ($MarketPlaces as $Shopping) {
                                                                            $PlatformArray[$Shopping->getId()][] = array("PlatformId" => $Shopping->getId(), "PlatformTitle" => $Shopping->getTitle(), "AuthenticateId" => $Shopping->getDescription(), "AuthenticateTitle" => $Shopping->getIsActive(), "AuthenticateValue" => $Shopping->getAddedBy(), "AutoGenerate" => $Shopping->getTranslationKey());
                                                                        }
                                                                        $count = 1;
                                                                        $thirdParty = "";
                                                                        foreach ($PlatformArray as $ShoppingArray) {
                                                                            if (isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) {
                                                                                $thirdParty .= htmlentities($ShoppingArray[0]['PlatformTitle']) . ", ";
                                                                            }
                                                                        }
                                                                        echo rtrim($thirdParty, ' ,');
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-check-square-o font-blue-madison"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Check List</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="table-scrollable">
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label_new"> Account Form </td>
                                                                    <td> <?php echo ($userDetails->getCheckListAccountForm() == "1") ? "Yes" : "NO"; ?> </td>
                                                                    <td class="label_new"> Credit Check </td>
                                                                    <td> <?php echo ($userDetails->getCheckListCreditCheck() == "1") ? "Yes" : "NO"; ?> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Terms & Conditions </td>
                                                                    <td> <?php echo ($userDetails->getCheckListTCs() == "1") ? "Yes" : "NO"; ?> </td>
                                                                    <td class="label_new"> Tariff Agreed </td>
                                                                    <td> <?php echo ($userDetails->getCheckListTariffAgreed() == "1") ? "Yes" : "NO"; ?> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--End Service Tab -->
                                    </div>
                                </div>
                                <!-- END TERMS OF USE TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE BASE CONTENT -->
        <?php
    }

    protected function renderFooter() {
        
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
