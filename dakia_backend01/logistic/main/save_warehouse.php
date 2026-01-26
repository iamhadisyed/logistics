<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $error_msg = "";
    private $id = NULL;
    private $warehouse_name = NULL;
    private $addressline1 = NULL;
    private $addressline2 = NULL;
    private $stateregion = NULL;
    private $citytown = NULL;
    private $postzipcode = NULL;
    private $countryid = NULL;
    private $phone = NULL;
    private $description = NULL;
    private $is_active = 1;
    private $countries;
    private $hub;
    private $email;

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
        $Country = new CountryFilter;
        $this->countries = $Country->getList();

        if (isset($this->form_vars["form_action"])) {
            $error_array = array();
            $delete = 0;
            $id = util_get_num("warehouse_id");
            // take appropriate action 
            switch ($this->form_vars["form_action"]) {
                // SAVE
                case "save":
                    // save new details
                    $warehouse_name = $this->form_vars["warehouse_name"];
                    $hub = $this->form_vars["hub"];
                    if (trim($warehouse_name) == '') {
                        $error_array[] = "Please Enter Warehouse Name.";
                        $headerMessage = 'Error';
                    }
                    if (trim($hub) == '') {
                        $error_array[] = "Please Enter Hub.";
                        $headerMessage = 'Error';
                    }
                    if (count($error_array) <= 0) {
                        $Warehouse = new WarehouseNew();
                        if ($id >= 0) {
                            $Warehouse->SetId($this->form_vars["id"]);
                        }
                        $Warehouse->SetWarehouseName($this->form_vars["warehouse_name"]);
                        $Warehouse->Setaddressline1($this->form_vars["addressline1"]);
                        $Warehouse->Setaddressline2($this->form_vars["addressline2"]);
                        $Warehouse->SetState($this->form_vars["stateregion"]);
                        $Warehouse->SetCity($this->form_vars["citytown"]);
                        $Warehouse->SetPostCode($this->form_vars["postzipcode"]);
                        $Warehouse->SetCountry($this->form_vars["countryid"]);
                        $Warehouse->SetPhone($this->form_vars["phone"]);
                        $Warehouse->SetDescription($this->form_vars["description"]);
                        $Warehouse->SetActive($this->form_vars["is_active"]);
                        $Warehouse->SetDelete($this->form_vars["is_delete"]);
                        $Warehouse->setAdded_date(date("Y-m-d H:i:s"));
                        $Warehouse->setAddedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                        $Warehouse->setUpdated_date(date("Y-m-d H:i:s"));
                        $Warehouse->setUpdated_by(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                        $Warehouse->setEmail($this->form_vars["email"]);
                        $Warehouse->setHub($this->form_vars["hub"]);
                        $Warehouse->save();
                        util_redirect("warehouse_list.php");
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
                    util_redirect("warehouse_list.php");
                    break;
            }
        } else {
            $id = util_get_num("warehouse_id");
            $this->id = $id;
            $Warehouse = new WarehouseNew($id);
            $this->warehouse_name = $Warehouse->getWarehouseName();
            $this->addressline1 = $Warehouse->getAddressLine1();
            $this->addressline2 = $Warehouse->getAddressLine2();
            $this->stateregion = $Warehouse->getState();
            $this->citytown = $Warehouse->getCity();
            $this->postzipcode = $Warehouse->getPostCode();
            $this->phone = $Warehouse->getPhone();
            $this->description = $Warehouse->getDescription();
            $this->is_active = $Warehouse->getActive();
            $this->countryid = $Warehouse->getCountry();
            $this->hub = $Warehouse->getHub();
            $this->email = $Warehouse->getEmail();
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
            <li><a href=""><? echo Translation::GetCaption("WAREHOUSE_DETAILS") ?></a></li>
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
                <div class="caption"> <i class="glyphicon glyphicon-search"></i><? echo Translation::GetCaption("WAREHOUSE_DETAILS") ?></div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:300px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div>
                        <div class="row">
                            <div class="col-md-12 red-18" style="text-align:right; font-size:12px !important;"> <? echo Translation::GetCaption("FIELDS_WITH_AN_ASTERISK_(*)_ARE_MANDATORY") ?> </div>
                            <div class="col-sm-3">
                                <label><? echo Translation::GetCaption("WAREHOUSE_NAME"); ?><span style="color:#F00;"> * </span></label>
                                <!--<input type='text' name='warehouse_name' id='warehouse_name' value='<?php // echo htmlspecialchars($warehouse_name);                     ?>' <?php // echo $this->form_vars["warehouse_name"] ;                    ?> size='20' class="form-control" />-->
                                <input type='text' name='warehouse_name' id='warehouse_name' value='<?php echo (isset($warehouse_name) ? htmlspecialchars($warehouse_name) : $this->warehouse_name ); ?>' size='20' class="form-control" />
                            </div>
                            <div class="col-sm-3">
                                <label><? echo Translation::GetCaption("ADDRESS_LINE_1"); ?></label>
                                <input type='text' name='addressline1' id='addressline1' value='<?php echo (isset($address1) ? $address1 : $this->addressline1 ); ?>' size='20' class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("ADDRESS_LINE_2"); ?></label>
                                <input type='text' name='addressline2' id='addressline2' value='<?php echo (isset($address2) ? $address2 : $this->addressline2 ); ?>' size='20' class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("STATE_REGION"); ?></label>
                                <input type='text' name='postzipcode' id='postzipcode' value='<?php echo (isset($stateregion) ? $stateregion : $this->postzipcode ); ?>' size='20' class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("CITY/TOWN"); ?></label>
                                <input type='text' name='citytown' id='citytown' value='<?php echo (isset($citytown) ? $citytown : $this->citytown ); ?>' size='20' class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("POST/ZIP_CODE"); ?></label>
                                <input type='text' name='postzipcode' id='postzipcode' value='<?php echo (isset($postzipcode) ? $postzipcode : $this->postzipcode ); ?>' size='20' class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("COUNTRY"); ?> 
                                    <!--<span style="color:#F00;"> * </span>-->
                                </label>
                                <?php
                                if (count($this->countries) > 0) {
                                    ?>
                                    <select name="countryid" id="countryid" class="form-control">
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
                                <label><? echo Translation::GetCaption("PHONE"); ?></label>
                                <input type='text' name='phone' id='phone' value='<?php echo (isset($phone) ? $phone : $this->phone ); ?>' size='20' class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label><? echo Translation::GetCaption("DESCRIPTION"); ?></label>
                                <textarea class="form-control" rows="5" cols="25" name="description" id="description"><?php echo (isset($description) ? $description : $this->description ); ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("HUB"); ?><span style="color:#F00;"> * </span></label>
                                <input type='text' name='hub' id='hub' value='<?php echo (isset($hub) ? $hub : $this->hub ); ?>' size='20' class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label><? echo Translation::GetCaption("EMAIL"); ?></label>
                                <input type='text' name='email' id='email' value='<?php echo (isset($email) ? $email : $this->email ); ?>' size='20' class="form-control"/>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label"><input type="checkbox" name="is_active" id="is_active" value="1"<?php echo (@$is_active == 1 || $this->is_active == 1 ? ' checked="checked"' : ''); ?> /> Active</label>
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
                    <input type="hidden" name="is_delete" id="is_delete" value="0" />
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
