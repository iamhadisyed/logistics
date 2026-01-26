<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    public $countrySearchValue = '';
    public $countrySearchVal = '';
    public $setServiceType = '';

    /*     * *
     * Controller logic
     */

    protected function init() {
        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin" && $user->getUserType() != "corporateclient") {
            util_redirect("index.php");
        }

        t_on(); // turn on trace for this page
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $srtServicOp = array();
        if (isset($_POST) && trim($_POST['UPDATE_ROUTING']) == 'UPDATE_ROUTING') {
            $routing_name = $_POST['routing_name'];
            $status = $_POST['status'];
            $country = $_POST['country'];
            $service_type = $_POST['service_type'];
            $handling = $_POST['handling'];
            $from_weight = $_POST['from_weight'];
            $to_weight = $_POST['to_weight'];


            $psrdelete = new PartnerServicesRoutingFilter();
            $psrdelete->addCountryFilter($country);
            $psrdelete->addRoutingNameFilter($routing_name);
            $psrdelete->addORPRServicesFilter();
            $psrdelete->deleteList();

            if (isset($_POST['bulk_upload'])) {
                $handlingBulk = $_POST['handlingBulk'];
                foreach ($handling as $indVal => $handlingCode) {
                    $psrSave = new PartnerServicesRouting();
                    $psrSave->setStatus($status);
                    $psrSave->setCountry($country);
                    $psrSave->setFromPostcode('XXXX');
                    $psrSave->setToPostcode('XXXX');
                    $psrSave->setServiceName($handlingBulk);
                    $psrSave->setFromWeight($from_weight[$indVal]);
                    $psrSave->setToWeight($to_weight[$indVal]);
                    $psrSave->setServiceType('PR');
                    $psrSave->setRoutingName($routing_name);
                    $psrSave->save();
                }
            } else {
                foreach ($handling as $indVal => $handlingCode) {
                    if (trim($handlingCode) != '') {
                        $psrSave = new PartnerServicesRouting();
                        $psrSave->setStatus($status);
                        $psrSave->setCountry($country);
                        $psrSave->setFromPostcode('XXXX');
                        $psrSave->setToPostcode('XXXX');
                        $psrSave->setServiceName($handlingCode);
                        $psrSave->setFromWeight($from_weight[$indVal]);
                        $psrSave->setToWeight($to_weight[$indVal]);
                        $psrSave->setServiceType('PR');
                        $psrSave->setRoutingName($routing_name);
                        $psrSave->save();
                    }
                }
            }
        }

        // common initialisation for ths page
        $this->setTitle("User Edit");

        // Tool bar
        //	$toolbar = Toolbar::getItem();
        //$toolbar->showPrintOption($this->num_valid > 0);

        /* $toolbar->showSearchOption();
          $toolbar->showImportOption();
          $toolbar->showLabelList();
          $toolbar->showWarehousePage();
          $toolbar->showAddConsignment();
          $toolbar->showCSVOption();
          $toolbar->showReleaseList();
          $toolbar->showConsignmentList(true);
          $toolbar->showSaveAddress();
          $toolbar->showSearchOption();
          $toolbar->showAddUser();
          $toolbar->showExportOption();
          $toolbar->showEndOfDayOption();
          $toolbar->showManifestList();
          $toolbar->showLabelCreation();
          $toolbar->showUserList(); */
    }

    protected function renderHead() {
        ?>
        <script>



            function getRoutingCountry(routingName) {


                $.get("processrouting.php?routing_name=" + routingName + "&action=ROUTING_COUNTRIES", function (data) {
                    $('#advanceservice').show();
                    $('#advanceservice').html(data);

                });

            }


            function getCountryServiceWeight(countryName, routingName)
            {
                $.get("processrouting.php?country_name=" + countryName + "&routing_name=" + routingName + "&action=COUNTRIES_DETAIL", function (data) {
                    $('#advanceWeightDetail').show();
                    $('#advanceWeightDetail').html(data);

                });

            }

            function getCountryServiceWeightEdit(countryName, routingName)
            {
                $.get("processrouting.php?country_name=" + countryName + "&routing_name=" + routingName + "&action=COUNTRIES_DETAIL_EDIT", function (data) {
                    $('#advanceWeightDetail').show();
                    $('#advanceWeightDetail').html(data);

                });

            }
            function getCountryServiceWeightDelete(countryName, routingName)
            {

                if (confirm("Are you sure, you want to delete " + routingName + " for country " + countryName))
                {
                    $.get("processrouting.php?country_name=" + countryName + "&routing_name=" + routingName + "&action=COUNTRIES_DETAIL_DELETE", function (data) {
                        $('#advanceWeightDetail').show();
                        $('#advanceWeightDetail').html(data);

                    });
                    getRoutingCountry(routingName);
                }

            }



            function submitWeight() {
                $("#form_action").val("saveWeight");
                $("#bookingForm").submit();
            }

            $(window).scroll(function () {
                if ($(this).scrollTop() > 380) {
                    if ($('#advance-service').height() < 350)
                        $('#advance-service').attr('style', 'position:fixed; top:30px; width:20%');
                    else
                        $('#advance-service').attr('style', '');

                } else {

                    $('#advance-service').attr('style', '');
                }
            });

        <?php
        if (isset($_POST['routing_name']) && isset($_POST['country'])) {
            echo '$(document).ready(function () { ';
            echo 'getRoutingCountry("' . $_POST['routing_name'] . '");';
            echo 'getCountryServiceWeight("' . $_POST['country'] . '","' . $_POST['routing_name'] . '")';
            echo '	});';
        }
        ?>


        </script>        
        <?

        }


        /***
        * Content View
        */
        protected function renderBody()
        {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="#">Routing</a></li>
        </ul>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <style>
            .row{
                margin-bottom:15px;
            }
        </style>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                    Available Routing
                </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
            </div>
            <div class="portlet-body">
                <a class="btn btn-primary" id="btnRoutingM" href="../main/routing_manual.php" class="btn_routing"><span></span>Manual</a>
                <a class="btn btn-primary" id="btnRoutingP" href="../main/routing_bulk.php" class="btn_routing"><span></span>Personalized</a>
                <a class="btn btn-primary" id="btnRoutingV" href="../main/routing.php" class="btn_routing"><span></span>View</a>
                <div class="row"> 
        <?php errorList::getItem()->render(); ?>
                    <div class="col-md-4">
                        <h3>One World Product/Routing</h3>
        <?php
        $psrSelectName = new PartnerServicesRoutingFilter();

        $psrSelectRoutingName = $psrSelectName->getAvailableRoutingNameList();


        /*         * ******************************** */
        /* 				DATA FIELD */
        /*         * ******************************** */
        if (count($psrSelectRoutingName) > 0) {
            $display_stringDemo = "<table class='table table-striped table-bordered table-advance table-hover'>";
            $display_stringDemo .= "<thead><tr>";
            $display_stringDemo .= "<th>Routing Name</th>";
            $display_stringDemo .= "<th>Advance</th>";
            $display_stringDemo .= "</tr></thead><tbody>";

            $countRoutingDetails = 0;
            foreach ($psrSelectRoutingName as $rowRoutingName) {
                if ($countRoutingDetails % 2 == 0)
                    $display_stringDemo .= "<tr>";
                else
                    $display_stringDemo .= "<tr>";

                $display_stringDemo .= '<td>' . $rowRoutingName->getRoutingName() . '</td>';
                $display_stringDemo .= "<td>
                                    <span class='btn btn-primary' style='cursor:pointer' onclick='return getRoutingCountry(\"" . $rowRoutingName->getRoutingName() . "\");'>Detail</span>
                                            </td>";
                $display_stringDemo .= "</tr>";
                $countRoutingDetails++;
            }
            $display_stringDemo .= "</tbody></table>";
            echo $display_stringDemo;
        }
        ?>
                        <input type="hidden" name="deleteserviceids" id="deleteserviceids" value=""  />
                    </div>
                    <div class="col-md-4">
                        <h3>Routing Country</h3>
                        <div class="main_grid main_grid2" id="advance-service">
                            <div id="advanceservice" style=" display:none;"> 

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">	
                        <h3>Routing Detail </h3>
                        <div class="main_grid main_grid2" id="advance-service">
                            <div id="advanceWeightDetail" style=" display:none;"> 

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
