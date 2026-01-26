<?php
// get settings
require_once("../includes/settings/config.inc.php");

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $page_vars;

    /*     * *
     * Controller logic goes here
     */

    public function init() {

        SessionManager::checkUserAccess(User::PRIVILEGE_CLIENT);

        //Checked logged in
        //$adminUser = SessionManager::getAdminUser();
        //if ($adminUser->getId() <= 0) util_redirect ("../controller/login.php");

        $this->setTitle("Generate Manifest");
        //ControllerMenu::getItem()->setVisibility(true);
        // Tool bar
        /* $toolbar = Toolbar::getItem();
          //$toolbar->showPrintOption($this->num_valid > 0);
          $toolbar->showSearchOption();
          $toolbar->showImportOption();
          $toolbar->showLabelList();
          $toolbar->showWarehousePage();
          $toolbar->showAddConsignment();
          $toolbar->showCSVOption();
          $toolbar->showReleaseList();
          $toolbar->showConsignmentList();
          $toolbar->showSaveAddress();
          $toolbar->showSearchOption();
          $toolbar->showAddUser();
          $toolbar->showExportOption();
          $toolbar->showEndOfDayOption();
          $toolbar->showManifestList();
          $toolbar->showLabelCreation();
          $toolbar->showUserList(); */
        // Set up filter conditions and pass to list
        t_on();
        if (isset($_POST["form_action"])) {

            if ($_POST["form_action"] == "ok" || $_POST["form_action"] == "save") {

                $this->page_vars = util_getPostArray();
                $filterSet = false;
                $time = 0;

                $from_date = $this->getStrToTime($this->page_vars["from_date"]);
                $to_date = $this->getStrToTime($this->page_vars["to_date"]);
                $value = $this->page_vars["Value"];
                $servicetype = $this->page_vars["servicetype"];
                $carrier = $this->page_vars["carrier"];
                $service_type = $this->page_vars["service_type"];
                //	echo 'abc'.$value;
                //	exit;

                $_SESSION["fromdate"] = $from_date;
                $_SESSION["todate"] = $to_date;

                //
                if ($this->page_vars["from_date"] != "" && $this->page_vars["to_date"] != "") {
                    $fromdate = $this->page_vars["from_date"];
                    $todate = $this->page_vars["to_date"];

                    $fromtime = $fromdate[6] . $fromdate[7] . $fromdate[8] . $fromdate[9] . '/' . $fromdate[3] . $fromdate[4] . '/' . $fromdate[0] . $fromdate[1];
                    $totime = $todate[6] . $todate[7] . $todate[8] . $todate[9] . '/' . $todate[3] . $todate[4] . '/' . $todate[0] . $todate[1];
                    $link = "createmanifestservices.php?fromdate=" . $fromtime . "&todate=" . $totime . "&value=" . $value . "&type=" . $servicetype . "&carrier=" . $carrier . "&handling=" . $service_type;
                    util_redirect($link);
                }

                if ($this->page_vars["from_date"] != "" && $this->page_vars["to_date"] == "") {
                    $fromdate = $this->page_vars["from_date"];
                    //$todate = $this->page_vars["to_date"];

                    $fromtime = $fromdate[6] . $fromdate[7] . $fromdate[8] . $fromdate[9] . '/' . $fromdate[3] . $fromdate[4] . '/' . $fromdate[0] . $fromdate[1];
                    $totime = $fromtime;
                    $link = "createmanifestservices.php?fromdate=" . $fromtime . "&todate=" . $totime . "&value=" . $value . "&type=" . $servicetype . "&carrier=" . $carrier . "&handling=" . $service_type;
                    util_redirect($link);
                }


                // Is the filter set
                else {
                    util_redirect("client_list.php");
                }
            } else {
                util_redirect("client_list.php");
            }
        }

        //util_redirect("warehouse_list.php");
    }

    /*     * *
     * Converts uk format date to date time
     */

    function getStrToTime($date_str) {
        $date_str = trim($date_str);
        $date_str = str_replace("\\", "/", $date_str);
        $date_str = str_replace("-", "/", $date_str);
        $date_str = str_replace(" ", "/", $date_str);

        $day = Date("d");
        $month = Date("m");
        $year = Date("Y");

        $date_array = explode("/", $date_str);
        $idx = 0;
        foreach ($date_array as $date_part) {
            $date_val = intval($date_part);
            if ($date_val > 0) {
                switch ($idx) {
                    case 0:
                        $day = $date_val;
                        break;
                    case 1:
                        $month = $date_val;
                        if ($month < 0)
                            $month = 1;
                        if ($month > 12)
                            $month = 12;
                        break;
                    case 2:
                        $year = $date_val;
                        break;
                }
                ++$idx;
            }
        }
        //
        $new_date = $month . "/" . $day . "/" . $year;
        return strToTime($new_date);
    }

    public function renderHead() {
        ?>
        <script type="text/javascript">
        <!--
            $(document).ready(function () {

                //*** Search
                $("#search_button").click(function () {

                    $("#form_action").val("search");
                    $("#controllerform").submit();
                    // alert("test");  
                });

                $("#from_date").datepicker({dateFormat: 'dd/mm/yy', showOn: 'button', buttonImage: '../images/calendar.gif', buttonImageOnly: true});
                //
                $("#to_date").datepicker({dateFormat: 'dd/mm/yy', showOn: 'button', buttonImage: '../images/calendar.gif', buttonImageOnly: true});


            });

            function getServiceType()
            {
                va   r carr          ierName = $("#carrier").val();
                        $(".load          ing-service").show();

                // alert(         "Sorry, no matching items found");
                $.post("         ../main/ajaxlabel.php", {action: 'GETCARRIERSERVICETYPES', carrierName: carrierName}, function (data) {
                    $('#ser                                vice_type').html(data);
                    $(".loadi                ng-service").hide();
                });


            }
            //-->
                </script>
        <?php
    }

    /*     * *
     * Render the made body of page.
     */

    public function renderBody() {
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/release_list_services.php">Manifest</a></li>
            <li><a href="">Generate</a></li>
        </ul>
        <?php
        ?>
        <!-- <form action="report.php" method="post">-->
        <div class="clear" ></div>
        <div class="main_formpage">
            <h1 class="heading"></h1>
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i>
        <?php errorList::getItem()->render(); ?>
                        Generate Manifest				</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>From Date:</label>
                            <input class='form-control' name="from_date" align="left" id="from_date" type="text" value="<?php echo @$this->page_vars["from_date"]; ?>">
                        </div>
                        <div class="col-md-4">

                            <label>To Date:</label>
                            <input class='form-control' name="to_date" align="left" id="to_date" type="text" value="<?php echo @$this->page_vars["to_date"]; ?>"> 
                        </div> 
                        <div class="col-md-4">
                            <label>Advance Filter</label>
                            <select id="Value" name="Value" class="form-control" >
                                <option value="None">--- Please Select ---</option>
                                <option value="LV">Low Value</option>
                                <option value="MV">Medium Value</option>
                                <option value="HV">High Value</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Select Service Type:</label>
                            <select id="servicetype" name="servicetype" class="form-control" >
                                <option value="None">--- Please Select ---</option>
                                <option value="DBP">United Kingdom</option>
                                <option value="R1">Europe</option>
                                <option value="INT">Rest Of The World</option>
                            </select>
                        </div>      

                        <div class="col-md-4">
                            <label>Select Carrier:</label>
                            <select id="carrier" name="carrier" onchange="return getServiceType();" class="form-control" >
        <?php $this->carrierDropDown(@$carrier); ?>
                            </select>
                        </div>	

                        <div class="col-md-4">
                            <label>Select Service: <img src="loading.gif" class="loading-service" width="20" height="20" style=" display:none;" /></label>
                            <select id="service_type" name="service_type" class="form-control" >
                                <option value="">Select Service</option>
                            </select>
                        </div>	

                    </div>
                    <div class="row" style="text-align:center;">    
                        <br/>
                        <a id="btnCancel" href="#" class="btn btn-danger btn_cancel"><span></span>Cancel</a>
                        <a id="btnSaveNew" href="#" name="btnSave" class="btn btn-primary btn_save"><span></span>Generate</a>
                        <br/>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }

    private function getCarrierArray() {
        $filter = new ServiceFilter();
        $filter->getUniqueCarrierfilter();
        $carrierList = $filter->getList();
        return $carrierList;
    }

    /*     * **
     * List of flight numbers for current user
     */

    private function carrierDropDown($carrier) {
        echo '<option>--- Please Select ---</option>';
        $carriers = $this->getCarrierArray();

        foreach ($carriers as $carrierData) {
            $selected = ($carrier == $carrierData->getCarrier()) ? " selected" : "";
            echo '<option' . $selected . ' value="' . $carrierData->getCarrier() . '">' . $carrierData->getCarrier() . '</option>';
        }
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and show page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
