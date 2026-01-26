<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $error_msg = "";
    private $id = NULL;
    private $transit_time;
    private $warehousefrom;
    private $warehouseidfrom = NULL;
    private $warehouseto;
    private $warehouseidto = NULL;

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
        $warehousefrom = new WarehouseNewFilter;
        $this->warehousefrom = $warehousefrom->getWarehouseList();
        $warehouseto = new WarehouseNewFilter;
        $this->warehouseto = $warehouseto->getWarehouseList();

        if (isset($this->form_vars["form_action"])) {
            $error_array = array();
            $headerMessage = '';
            $delete = 0;

            $id = util_get_num("warehousetowarehouse_id");
            // take appropriate action 
            switch ($this->form_vars["form_action"]) {

                // SAVE
                case "save":
                    // save new details
//                    $warehousefrom = $this->form_vars["warehousefrom"];
//                    $warehouseto = $this->form_vars["warehouseto"];
//                    $transit_time = $this->form_vars["transit_time"];
//                    if (trim($warehousefrom) == '') {
//                        $error_array[] = "Please Select Warehouse from.";
//                        $headerMessage = 'Error';
//                    }
//                    if (trim($warehouseto) == '') {
//                        $error_array[] = "Please Select Warehouse to.";
//                        $headerMessage = 'Error';
//                    }
//                    if (trim($transit_time) == '') {
//                        $error_array[] = "Please Enter Transit Time.";
//                        $headerMessage = 'Error';
//                    }
                    if (count($error_array) <= 0) {
                        $warehousetowarehouse = new WarehouseToWarehouse();
                        if ($id >= 0) {
                            $warehousetowarehouse->SetId($this->form_vars["id"]);
                        }
                        $warehousetowarehouse->SetFromWarehouseId($this->form_vars["from_warehouse_id"]);
                        $warehousetowarehouse->SetToWarehouseId($this->form_vars["to_warehouse_id"]);
                        $warehousetowarehouse->SetTransitTime($this->form_vars["transit_time"]);
                        $warehousetowarehouse->save();
                        util_redirect("warehouse_to_warehouse.php");
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
                    util_redirect("warehouse_to_warehouse.php");
                    break;
            }
        } else {
            $id = util_get_num("warehousetowarehouse_id");
            $this->id = $id;
            $warehousetowarehouse = new WarehouseToWarehouse($id);
            $this->warehouseidfrom = $warehousetowarehouse->getFromWarehouseId();
            $this->warehouseidto = $warehousetowarehouse->getToWarehouseId();
            $this->transit_time = $warehousetowarehouse->getTransitTime();
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
            <li><a href=""><? echo Translation::GetCaption("WAREHOUSE_TRANSIT_TIME_DETAILS") ?></a></li>
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
                <div class="caption"> <i class="glyphicon glyphicon-search"></i><? echo Translation::GetCaption("WAREHOUSE_TRANSIT_TIME_DETAILS") ?></div>
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
                                if (count($this->warehousefrom) > 0) {
                                    ?>
                                    <select name="from_warehouse_id" id="from_warehouse_id" class="form-control">
                                        <option value=""><?php echo Translation::GetCaption("SELECT_WAREHOUSE"); ?></option>
                                        <?php
                                        foreach ($this->warehousefrom as $warehousefrom) {
                                            ?>
                                            <option value="<?php echo $warehousefrom->getId(); ?>"<?php echo ($warehousefrom->getId() == $this->warehouseidfrom ? ' selected="selected"' : ''); ?>><?php echo $warehousefrom->getWarehouseName(); ?></option>
                                            <?php
                                        }
                                        ?>    
                                    </select>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("WAREHOUSE"); ?>
                                </label>
                                <?php
                                if (count($this->warehouseto) > 0) {
                                    ?>
                                    <select name="to_warehouse_id" id="to_warehouse_id" class="form-control">
                                        <option value=""><?php echo Translation::GetCaption("SELECT_WAREHOUSE"); ?></option>
                                        <?php
                                        foreach ($this->warehouseto as $warehouseto) {
                                            ?>
                                            <option value="<?php echo $warehouseto->getId(); ?>"<?php echo ($warehouseto->getId() == $this->warehouseidto ? ' selected="selected"' : ''); ?>><?php echo $warehouseto->getWarehouseName(); ?></option>
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
