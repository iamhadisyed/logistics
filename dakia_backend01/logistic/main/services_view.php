<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'servicecountrytimefilter.class',
                    'servicecountrytime.class',
                    'country.class',
                    'countryfilter.class',
                    'carrierservicedefaultrules.class',
                    'carrierservicedefaultrulesfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'servicedocument.class',
                    'servicedocumentfilter.class',

                ]);
/* * *
 * Page for details a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $servicesDetails;
    private $carrierDetails;

    protected function init() {
        if (util_get_num("id") <= 0) {
            util_redirect("services_list.php");
        }
        $sessionUser = SessionManager::getUser();

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("SERVICES_DETAILS")
        );
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $id = util_get_num("id");


        $services = new ServiceFilter($id);
        $services->addIdFilter($id);

        $servicelist = $services->getServiceViewList("ser.*, c.iso 'carrier_country_iso', c.name 'carrier_country_name',ca.logo 'carrier_logo', carrier 'carrier_name', cut_off_time 'carrier_cut_off',  currency_code 'carrier_currency_code'", false);
        $this->servicesDetails = $servicelist[0];
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
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/profile.min.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    public function renderBody() {
        $sessionUser = SessionManager::getUser();
        $this->servicesDetails = new Services($_GET['id']);
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
                            $carrierLogo = $this->servicesDetails->getCarrierLogo();
                            if (!empty($carrierLogo))
                                $carrierLogo = '../images/carrierlogo/thumbnail/owe_100_' . $carrierLogo;
                            else
                                $carrierLogo = '../images/carrierlogo/thumbnail/owe_100_' . $carrierLogo;
                            ?>
                            <img src="<?php echo Services::getImageExists($carrierLogo); ?>" class="img-responsive" alt=""> </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name"> <?php echo $this->servicesDetails->getCarrierName(); ?> </div>
                            <div class="profile-usertitle-job"> <?php echo $this->servicesDetails->getName(); ?> [ <?php echo $this->servicesDetails->getCode(); ?> ]</div>
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
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;" title="Mininum weight in KG">
                                    <?php echo $this->servicesDetails->getFromWeight(); ?>
                                </div>
                                <div class="uppercase profile-stat-text"> Min Wgt </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;" title="Maximum weight in KG"> 
                                    <?php echo $this->servicesDetails->getToWeight(); ?>
                                </div>
                                <div class="uppercase profile-stat-text"> Max Wgt </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class="uppercase profile-stat-title" style="font-size: 18px !important;" title="Exchange Rate For <?php echo $this->servicesDetails->getUploadedCurrency(); ?>"> 
                                    <?php echo $this->servicesDetails->getUploadedCurrencyValue(); ?>
                                </div>
                                <div class="uppercase profile-stat-text"> <?php echo $this->servicesDetails->getUploadedCurrency(); ?> </div>
                            </div>
                        </div>
                        <!-- END STAT -->
                        <div>
                            <div class="margin-top-20 profile-desc-link">
                                <i class="fa"><?php echo '<img src=\'../assets/global/img/flags/' . strtolower($this->servicesDetails->getCarrierCountryIso()) . '.png\' /> '; ?></i>
                                <a href="javascript:;"><?php echo $this->servicesDetails->getCarrierCountryName(); ?></a>
                            </div>

                            <div class="margin-top-20 profile-desc-link">
                                <i class="fa fa-deviantart"></i>
                                <a href="javascript:void(0)"><?php
                                    if ($this->servicesDetails->getServiceType() == 'D')
                                        echo 'Dispatch';
                                    else if ($this->servicesDetails->getServiceType() == 'C')
                                        echo 'Collection';
                                    else if ($this->servicesDetails->getServiceType() == 'B')
                                        echo 'Collection & Dispatch';
                                    ?></a>
                            </div>

                            <div class="margin-top-20 profile-desc-link">
                                <i class="fa fa-flask"></i>
                                <a href="javascript:void(0)">
                                    <?php
                                    if ($this->servicesDetails->getDeliveryMode() == '1')
                                        echo 'Door To Door';
                                    else if ($this->servicesDetails->getDeliveryMode() == '2')
                                        echo 'Parcel Shops';
                                    else if ($this->servicesDetails->getDeliveryMode() == '3')
                                        echo 'Door To Door (POD)';
                                    else if ($this->servicesDetails->getDeliveryMode() == '4')
                                        echo 'Drop Off';
                                    else if ($this->servicesDetails->getDeliveryMode() == '5')
                                        echo 'Collection From address';
                                    ?>
                                </a>
                            </div>
                            <div class="margin-top-20 profile-desc-link">
                                <?php echo (trim($this->servicesDetails->getActive()) == '1') ? '<i class="fa fa-check"></i><span class="label label-sm label-success">Active</span>' : '<i class="fa fa-times"></i><span class="label label-sm label-danger">Inactive</span>' ?>

                            </div>
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
                                <div class="portlet-body">
                                    <div class="tab-content">
                                        <!-- GENERAL QUESTION TAB -->
                                        <div class="tab-pane active" id="account_tab">
                                            <div class="portlet light">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="icon-user"></i>
                                                        <span class="caption-subject bold uppercase">Service Details</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <fieldset>
                                                        <legend><i class="fa fa-globe"></i> Service Allowed Country(s)</legend>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <?php
                                                                $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
                                                                $serviceCountryTimeFilter->addServiceTableJoin();
                                                                $serviceCountryTimeFilter->addFilter(' `service_country_ttime`.id_service =' . $this->servicesDetails->getId());
                                                                $serviceCountry = $serviceCountryTimeFilter->getList();
                                                                $output = "";
                                                                $i = 0;

                                                                foreach ($serviceCountry as $sercoun) {
                                                                    $serCountryIso = $sercoun->getIdCountry();
                                                                    $countryFilter = new CountryFilter();
                                                                    $countryFilter->addFilter(" id = " . $serCountryIso);
                                                                    $countryList = $countryFilter->getList();
                                                                    if (count($countryList) > 0) {
                                                                        $countryName = $countryList[0]->getName();
                                                                    }
                                                                    $filename = str_replace("|", "-", $serviceCountry[0]->getCode());
                                                                    $filelink = "../_assets/service_sample_label/" . $filename . ".pdf";
                                                                    if (file_exists($filelink)) {
                                                                        $link = '<a href="' . $filelink . '" target="_blank"><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>';
                                                                        $output .= '<div class="col-sm-3">' . $link . '</div>';
                                                                    } else {
                                                                        $link = '<a href="#" onclick="fileNotFound();" ><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>';
                                                                        $output .= '<div class="col-sm-3">' . $link . '</div>';
                                                                    }
                                                                }
                                                                echo $output;
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-key"></i> Charges Details</legend>
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>

                                                                <tr>
                                                                    <td class="label_new"> Additional Charges</td>
                                                                    <td ><?php echo $this->servicesDetails->getAditionalCharge(); ?></td>
                                                                    <td class="label_new"> Registration Charges </td>
                                                                    <td ><?php echo $this->servicesDetails->getRegistrationFee(); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Fuel Charges</td>
                                                                    <td ><?php echo $this->servicesDetails->getFuelSurcharge(); ?></td>
                                                                    <td class="label_new"> Fuel Charges Cost</td>
                                                                    <td ><?php echo $this->servicesDetails->getFuelSurchargeCost(); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Carrier Address limit</td>
                                                                    <td ><?php echo $this->servicesDetails->getCarrierAddressLimit(); ?></td>
                                                                    <td class="label_new"> Package Type</td>
                                                                    <td ><?php echo $this->servicesDetails->getShipmentType(); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Remotearea Charges</td>
                                                                    <td ><?php echo $this->servicesDetails->getRemotearea(); ?></td>
                                                                    <td class="label_new"> Un-Tracked Service</td>
                                                                    <td ><?php echo $this->servicesDetails->getIsUntrack(); ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-exchange"></i> Limitation Details</legend>
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label_new"> Max Vol Weight</td>
                                                                    <td ><?php echo $this->servicesDetails->getMaxVolumetricWeight(); ?></td>
                                                                    <td class="label_new"> Vol Denominator</td>
                                                                    <td ><?php echo $this->servicesDetails->getVolumetricDenominator(); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Vol Formula</td>
                                                                    <td ><?php echo $this->servicesDetails->getVolWgtFormula(); ?></td>
                                                                    <td class="label_new"> </td>
                                                                    <td ></td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="label_new"> Max Length</td>
                                                                    <td ><?php echo $this->servicesDetails->getMaxLength(); ?></td>
                                                                    <td class="label_new"> Max Width</td>
                                                                    <td ><?php echo $this->servicesDetails->getMaxWidth(); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Max Height</td>
                                                                    <td ><?php echo $this->servicesDetails->getMaxHeight(); ?></td>
                                                                    <td class="label_new"> Email Required </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getRequiredEmail()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Phone Required</td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getRequiredTelephone()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                    <td class="label_new"> Proforma Invoices Required </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getProformaInvoice()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Only Available On Fridays </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getFridayOnlyFlag()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                    <td class="label_new"> Only Available On Saturday  </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getSaturdayOnlyFlag()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Only Available On Sunday </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getSundayOnlyFlag()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                    <td class="label_new"> Insurance Available </td>
                                                                    <td ><?php echo ((trim($this->servicesDetails->getInsuranceAvailable()) == '1') ? 'YES' : 'NO'); ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend><i class="fa fa-cog"></i> Extra Details</legend>
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label_new">  Additional Details </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> <?php echo $this->servicesDetails->getAdditionalDetails() ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label_new"> Description </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> <?php echo ($this->servicesDetails->getDescription()) ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </fieldset>
        <?php if ($this->servicesDetails->getIsCustomized() != 1) { ?>
                                                        <!--If service is carrier based then show this block-->
                                                        <fieldset>
                                                            <legend><i class="fa fa-cog"></i> Service Agents Details</legend>
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Agent Name </td>
                                                                        <td class="label_new"> From Weight (KG)  </td>
                                                                        <td class="label_new"> To Weight (KG)</td>
                                                                    </tr>
            <?php
            $carrierServiceDefaultRules = new carrierServiceDefaultRulesFilter();
            $carrierServiceDefaultRules->addAgentTableJoin();
            $carrierServiceDefaultRules->addFilter(" csdr.serviceid = '" . DbAccess3::escape($this->servicesDetails->getId()) . "'");
            $carrierServiceDefaultRules->AddOrderBy('csdr.from_weight', 'asc');
            $carrierServiceDefaultRulesData = $carrierServiceDefaultRules->getList("csdr.*", " , a.agent_name 'agent_name'");
            if (count($carrierServiceDefaultRulesData) > 0) {
                foreach ($carrierServiceDefaultRulesData as $carrierServiceDefaultRulesItems) {
                    echo '<tr>
                                                                                <td> ' . $carrierServiceDefaultRulesItems->getAgentName() . ' </td>
                                                                                <td> ' . $carrierServiceDefaultRulesItems->getFromWeight() . ' </td>
                                                                                <td> ' . $carrierServiceDefaultRulesItems->getToWeight() . ' </td>
                                                                            </tr>';
                }
            } else {
                echo '<tr>
                                                                                <td colspan="3"> <div class="alert alert-danger">No agent found</div></td>
                                                                            </tr>';
            }
            e
            ?>

                                                                </tbody>
                                                            </table>
                                                        </fieldset>
                                                        <!--End If service is carrier based then show this block-->
                                                                <?php } ?>
                                                </div>
                                            </div>
                                                                <?php if ($this->servicesDetails->getIsCustomized() != 1) { ?>
                                                <!--If service is carrier based then show this block-->
                                                <div class="portlet light bordered">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-file-o"></i>
                                                            <span class="caption-subject bold uppercase">Agent's Service Documents</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">


                                                        <fieldset>
                                                            <table class="table table-bordered table-advance table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="4">                                                                
                                                                            <div class="row">
            <?php
            if ($this->servicesDetails->getId() > 0) {
                $serviceDocumentFilter = new serviceDocumentFilter();
                $serviceDocumentLists = $serviceDocumentFilter->getServiceDoc($this->servicesDetails->getId());
                foreach ($serviceDocumentLists as $serviceDocumentList) {
                    $temp = explode(".", $serviceDocumentList->getDocumentName());
                    $extension = end($temp);
                    $fileFullPath = "../_assets/service_documents/" . $this->servicesDetails->getId() . "/" . $serviceDocumentList->getDocumentName();
                    if (!file_exists($fileFullPath) || $extension == "pdf") {
                        $fileFullPath = "../images/No-image-found.jpg";
                    }
                    if ($extension == "pdf") {
                        $fileFullPath = "../images/pdf.png";
                    }
                    ?>
                                                                                        <div class="col-md-3">
                                                                                            <div class="thumbnail">
                                                                                                <div class="document-thumb">
                                                                                                    <img src="<?php echo $fileFullPath ?>" alt="<?php echo $serviceDocumentList->getDocumentName(); ?>" data-src="<?php echo $fileFullPath ?>">
                                                                                                </div>
                                                                                                <div class="caption">
                                                                                                    <h4><?php echo $serviceDocumentList->getAgentId(); ?><br><?php echo $serviceDocumentList->getDocumentId(); ?></h4>
                                                                                                    <a target="_blank" href="../_assets/service_documents/<?php echo $this->servicesDetails->getId() . "/" . $serviceDocumentList->getDocumentName(); ?>" class="btn blue"> View </a>
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
                                                        </fieldset>
                                                        </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--End If service is carrier based then show this block-->
        <?php } ?>
                                            <div class="portlet light bordered">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-list-ul"></i>
                                                        <span class="caption-subject bold uppercase">Audit Logs</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">


                                                    <fieldset>
                                                        <table class="table table-bordered table-advance table-hover">
                                                            <tbody>
                                                                <tr>

                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </fieldset>
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div><!--End Account Tab -->

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
