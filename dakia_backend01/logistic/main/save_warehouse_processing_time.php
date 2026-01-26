<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $error_msg = "";
    private $id = NULL;
    private $ParcelProcessingTime = NULL;
    private $service;
    private $serviceid = NULL;
    private $warehouse;
    private $warehouseid = NULL;

    /*     * *
     * Controller logic
     */

    protected function init() {

        // save source page info
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        // Get current user
        $sessionUser = SessionManager::getUser();
        // is this form being posted back? 
        $Service = new ServiceFilter;
        $this->service = $Service->getList();
        $Warehouse = new WarehouseNewFilter;
        $this->warehouse = $Warehouse->getWarehouseList();

        if (isset($this->form_vars["form_action"])) {
            $error_array = array();
            $headerMessage = '';
            $delete = 0;

            $id = util_get_num("warehouseprocessingtime_id");
            // take appropriate action 
            switch ($this->form_vars["form_action"]) {

                // SAVE
                case "save":
                    // save new details
//                    $warehouse_name = $this->form_vars["warehouse_name"];
//                    $hub = $this->form_vars["hub"];
//                    if (trim($warehouse_name) == '') {
//                        $error_array[] = "Please Enter Warehouse Name.";
//                        $headerMessage = 'Error';
//                    }
//                    if (trim($hub) == '') {
//                        $error_array[] = "Please Enter Hub.";
//                        $headerMessage = 'Error';
//                    }
                    if (count($error_array) <= 0) {
                        $WarehouseProcessingTime = new WarehouseProcessingTime();
                        if ($id >= 0) {
                            $WarehouseProcessingTime->SetId($this->form_vars["id"]);
                        }
                        $WarehouseProcessingTime->SetWarehouseId($this->form_vars["warehouseid"]);
                        $WarehouseProcessingTime->setServiceId($this->form_vars["serviceid"]);
                        $WarehouseProcessingTime->SetParcelProcessingTime($this->form_vars["ParcelProcessingTime"]);
                        $WarehouseProcessingTime->save();
                        util_redirect("warehouse_processing_time.php");
                    } else {
                        // add list of errors to error list
                        $error_list = ErrorList::getItem();
                        $error_list->setPrelistMessage($headerMessage);
                        $error_list->addErrorList($error_array);
                        break;
                    }

                //die;
                // CANCEL
                // - return to source page
                case "cancel":
                default:
                    util_redirect("warehouse_processing_time.php");
                    break;
            }
        } else {
            $id = util_get_num("warehouseprocessingtime_id");
            $this->id = $id;
            $WarehouseProcessingTime = new WarehouseProcessingTime($id);
            $this->warehouseid = $WarehouseProcessingTime->getWarehouseId();
            $this->serviceid = $WarehouseProcessingTime->getServiceId();
            $this->ParcelProcessingTime = $WarehouseProcessingTime->getParcelProcessingTime();
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php"><? echo Translation::GetCaption("HOME") ?></a></li>
            <li><a href=""><? echo Translation::GetCaption("WAREHOUSE_PROCESSING_TIME_DETAILS") ?></a></li>
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
                <div class="caption"> <i class="glyphicon glyphicon-search"></i><? echo Translation::GetCaption("WAREHOUSE_PROCESSING_TIME_DETAILS") ?></div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:300px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div>
                        <div class="row">
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("WAREHOUSE"); ?>
                                </label>
        <?php
        if (count($this->warehouse) > 0) {
            ?>
                                    <select name="warehouseid" id="warehouseid" class="form-control">
                                        <option value=""><?php echo Translation::GetCaption("SELECT_WAREHOUSE"); ?></option>
            <?php
            foreach ($this->warehouse as $warehouse) {
                ?>
                                            <option value="<?php echo $warehouse->getId(); ?>"<?php echo ($warehouse->getId() == $this->warehouseid ? ' selected="selected"' : ''); ?>><?php echo $warehouse->getWarehouseName(); ?></option>
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
        <?php
        if (count($this->service) > 0) {
            ?>
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
                                        <?php
                                    }
                                    ?>
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("PARCEL_PROCESSING_TIME"); ?></label>
                                <input type='text' name='ParcelProcessingTime' id='ParcelProcessingTime' value='<?php echo (isset($ParcelProcessingTime) ? $ParcelProcessingTime : $this->ParcelProcessingTime ); ?>' size='20' class="form-control" />
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
                      <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
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
