<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    // source page for consignment edit
    private $source;
    private $id = NULL;
    private $readonly = array();
    private $countries;
    private $countryid = NULL;
    private $service;
    private $serviceid = NULL;
    private $transit_time;
    private $carrier;
    private $carrierid = NULL;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // save source page info
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        // Get current user
        $sessionUser = SessionManager::getUser();
        $Country = new CountryFilter;
        $this->countries = $Country->getList();
        $Service = new ServiceFilter;
        $this->service = $Service->getList();
        $Carrier = new CarrierFilter;
        $Carrier->addFilter(" (carrier_id = '0' OR carrier_id is null) ");
        $Carrier->addFilter(" status = '1' ");
        $this->carrier = $Carrier->getList();
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "getServiceList") {
            $carier_id = $this->form_vars["carrier"];
            $service_id = '';
            $service = new Services();
            $serviceList = $service->getServicesList($service_id, $carier_id);
            echo $serviceList;
            die;
        }
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            $error_array = array();
            $headerMessage = '';
            $id = util_get_num("countrytime_id");
            
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - validate consignment details - if OK, save and return to Service Country Time list
                case "save":
//                    $countryid = $this->form_vars["countryid"];
//                    $serviceid = $this->form_vars["serviceid"];
//                    $transit_time = $this->form_vars["transit_time"];
//                    if (trim($countryid) == '') {
//                        $error_array[] = "Please Select Country.";
//                        $headerMessage = 'Error';
//                    }
//                    if (trim($serviceid) == '') {
//                        $error_array[] = "Please Select Service.";
//                        $headerMessage = 'Error';
//                    }
//                    if (trim($transit_time) == '') {
//                        $error_array[] = "Please Enter Transit Time.";
//                        $headerMessage = 'Error';
//                    }
                    if (count($error_array) <= 0) {
                        $serviceCountry = new ServiceCountryTime();
                        if ($id >= 0) {
                            $serviceCountry->SetId($this->form_vars["id"]);
                        }
                        
                        $serviceCountry->SetCountryId($this->form_vars["countryid"]);
                        $serviceCountry->SetServiceId($this->form_vars["serviceid"]);
                        $serviceCountry->SetTransitTime($this->form_vars["transit_time"]);
                        $serviceCountry->save();
                        util_redirect("service_country_time.php");
                    } else {
                        // add list of errors to error list
                        $error_list = ErrorList::getItem();
                        $error_list->setPrelistMessage($headerMessage);
                        $error_list->addErrorList($error_array);
                        break;
                    }
                // CANCEL
                // - return to source page
                case "cancel":
                default:
                    util_redirect("service_country_time.php");
                    break;
            }
        } else {
            $id = util_get_num("countrytime_id");
            $this->id = $id;
            $serviceCountry = new ServiceCountryTime($id);
            $this->countryid = $serviceCountry->getCountryId();
            $this->serviceid = $serviceCountry->getServiceId();
            $this->transit_time = $serviceCountry->getTransitTime();
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                // Service changes
                $("#carrierid").change(function () {
                    var carrier_value = $('#carrierid').val();
                    $.ajax({
                        method: "POST",
                        url: "save_service_country_time.php",
                        data: {carrier: carrier_value, func: "getServiceList"}
                    })
                            .done(function (data) {
                                $('#serviceid').html(" ");
                                $('#serviceid').html(data);
                            });
                });
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php"><? echo Translation::GetCaption("HOME") ?></a></li>
            <li><a href=""><? echo Translation::GetCaption("SERVICES_TIME_DETAILS") ?></a></li>
        </ul>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i><? echo Translation::GetCaption("SERVICES_TIME_DETAILS") ?></div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:300px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div>
                        <div class="row">
                            <!--<div class="col-md-12 red-18" style="text-align:right; font-size:12px !important;"> <? echo Translation::GetCaption("FIELDS_WITH_AN_ASTERISK_(*)_ARE_MANDATORY") ?> </div>-->
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("CARRIERS"); ?> 
                                    <!--<span style="color:#F00;"> * </span>-->
                                </label>
                                <?php
                                if (count($this->carrier) > 0) {
                                    ?>
                                    <select name="carrierid" id="carrierid" class="form-control">
                                        <option value=""><?php echo Translation::GetCaption("SELECT_CARRIER"); ?></option>
                                        <?php
                                        foreach ($this->carrier as $carrier) {
                                            ?>
                                            <option value="<?php echo $carrier->getId(); ?>"<?php echo ($carrier->getId() == $this->carrierid ? ' selected="selected"' : ''); ?>><?php echo $carrier->getCarrier(); ?></option>
                                            <?php
                                        }
                                        ?>    
                                    </select>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("SERVICES"); ?> 
                                    <!--<span style="color:#F00;"> * </span>-->
                                </label>
                                <select name="serviceid" id="serviceid" class="form-control">
                                    <option value=""><?php echo Translation::GetCaption("SELECT_SERVICE"); ?></option>
                                      <?php
                                        foreach ($this->service as $service) {
                                            ?>
                                            <option value="<?php echo $service->getId(); ?>"<?php echo ($service->getId() == $this->serviceid ? ' selected="selected"' : ''); ?>><?php echo $service->getName(); ?></option>
                                            <?php
                                        }
                                        ?>    
                                </select>

                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("COUNTRY"); ?> 
                                    <!--<span style="color:#F00;"> * </span>-->
                                </label>
                                <?php
                                if (count($this->countries) > 0) {
                                    ?>
                                    <select name="countryid" id="countryid" class="form-control">
                                        <option value=""><?php echo Translation::GetCaption("SELECT_COUNTRY"); ?></option>
                                        <?php
                                        foreach ($this->countries as $country) {
                                            ?>
                                            <option value="<?php echo $country->getId(); ?>"<?php echo ($country->getId() == $this->countryid ? ' selected="selected"' : ''); ?>><?php echo $country->getName(); ?></option>
                                            <?php
                                        }
                                        ?>    
                                    </select>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("TRANSIT_TIME"); ?></label>
                                <!--<span style="color:#F00;"> * </span>-->
                                <input type='text' name='transit_time' id='transit_time' value='<?php echo (isset($transit_time) ? $transit_time : $this->transit_time ); ?>' size='20' class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div style="clear:both"></div>
                    <br /><br />
                    <div class="row " style="text-align:centre;" align="center">
                        <div class="col-md-12">
                            <a id="btnCancel" href="#" class="btn btn-danger"><span></span><? echo Translation::GetCaption("CANCEL"); ?></a>
                            <a id="btnSave" href="#" class="btn btn-primary"><span></span><? echo ($this->id <= 0 ? Translation::GetCaption("SAVE") : Translation::GetCaption("UPDATE")); ?></a>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>
                    <input type="hidden" name="form_action" id="form_action" value="" />

                </div>
            </div>    
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param none
     */

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
