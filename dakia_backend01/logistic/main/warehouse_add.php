<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Warehouse Add/Update page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
 
                    'country.class',
                    'countryfilter.class',
                    'warehouse.class',
                    'warehousefilter.class']);
// set up local page class
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
    
    
    
    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <?php
    }
    
    /*     * *
     * This page's content
     * @return void
     */
    
    public function renderBody() { ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-gift"></i>Warehouse Detail
                </div>
                <div class="actions">
                    <a href="warehouse.php" class="btn blue"><i class="fa fa-plus"></i> Warehouse List</a>
                </div>
                <div class="tools">
                    <!--<a href="javascript:;" class="collapse"></a>-->
                </div>
            </div>
            <div class="portlet-body">
                <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <?php
                        if ($this->error_msg != "") {
                            ?>
                            <div class="note note-success"><?php echo $this->error_msg ?></div>
                            <?php
                        }
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warehouse Name</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Warehouse is mandatory">*</i>
                                        <input  id="warehouse_name" name="warehouse_name" type="text" value="<?php echo htmlspecialchars($this->warehouse_name); ?>" required="" placeholder="Warehouse Name" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="Warehouse Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Address Line 1</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Address Line 1 is mandatory">*</i>
                                        <input id="addressline1" name="addressline1" type="text" value="<?php echo htmlspecialchars($this->addressline1); ?>" required="" placeholder="Address Line 1" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="Address Line 1" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>State/Region</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="State/Region is mandatory">*</i>
                                        <input id="stateregion" name="stateregion" type="text" value="<?php echo htmlspecialchars($this->stateregion); ?>" required="" placeholder="State/Region" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="State/Region" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>City/Town</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="City/Town is mandatory">*</i>
                                        <input id="citytown" name="citytown" type="text" value="<?php echo htmlspecialchars($this->citytown); ?>" required="" placeholder="City/Town" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="City/Town" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Post/Zip Code</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Post/Zip Code is mandatory">*</i>
                                        <input id="postzipcode" name="postzipcode" type="text" value="<?php echo htmlspecialchars($this->postzipcode); ?>" required="" placeholder="Post/Zip Code" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="Post/Zip Code" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <label>Country</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <div class="first_form_col">
                                    <?php
                                    echo Ddl::generateCountryDDL('countryid', $this->countryid, 'id');
                                    ?>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Phone</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Phone is mandatory">*</i>
                                        <input id="phone" name="phone" type="text" value="<?php echo htmlspecialchars($this->phone); ?>" required="" placeholder="Phone" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="Phone" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warehouse Code</label>
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Warehouse Code is mandatory">*</i>
                                        <input id="warehouse_code" name="warehouse_code" type="text" value="<?php echo htmlspecialchars($this->warehouse_code); ?>" required="" placeholder="3 Digit Warehouse Code" class="form-control tooltipbutton" maxlength="3" data-toggle="tooltip" data-placement="top" title="Warehouse Code" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 ">
                            <div class="form-group">
                                <label>Active</label><br />
                                <input id="is_active" <?php echo ($this->is_active == '1' ? 'checked="checked"' : ''); ?> name="is_active" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No" checked data-on-color="primary" data-off-color="danger">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" rows="5" cols="25" name="description" id="description"><?php echo htmlspecialchars($this->description); ?></textarea>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">Save</button>
                                <a href="warehouse_add.php" id="btnCancel" class="btn_cancel btn btn btn-default" data-original-title="" title=""><span></span>Cancel</a>
                        </div>
                    </div>
                </form>    
            </div>
        </div>

        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }


        $CouObj = new CountryFilter;
        $this->countries = $CouObj->getList();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'Add warehouse'
        );

        /* ------------------------------------------------------------------------------ */
        // get vars
        $this->id = (isset($_REQUEST['warehouse_id']) ? strip_tags($_REQUEST['warehouse_id']) : '0');

        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_save'])) {
//            print_r($_POST);
            $this->warehouse_name = $_POST['warehouse_name'];
            $this->addressline1 = $_POST['addressline1'];
            $this->addressline2 = $_POST['addressline2'];
            $this->stateregion = $_POST['stateregion'];
            $this->citytown = $_POST['citytown'];
            $this->postzipcode = $_POST['postzipcode'];
            $this->countryid = $_POST['countryid'];
            $this->phone = $_POST['phone'];
            $this->warehouse_code = $_POST['warehouse_code'];
            $this->description = $_POST['description'];
            $this->is_active = isset($_POST['is_active']) ? '1' : '0';
            if ($this->validate_form()) {
                $fieldsVal = array('id' => $this->id,
                    'warehouse_name' => $this->warehouse_name,
                    'addressline1' => $this->addressline1,
                    'addressline2' => $this->addressline2,
                    'stateregion' => $this->stateregion,
                    'citytown' => $this->citytown,
                    'postzipcode' => $this->postzipcode,
                    'countryid' => $this->countryid,
                    'phone' => $this->phone,
                    'warehouse_code' => $this->warehouse_code,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                    'is_deleted' => 0,
                    'added_date' => date("Y-m-d H:i:s"),
                    'added_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0'),
                    'updated_date' => date("Y-m-d H:i:s"),
                    'updated_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                );

                $WSavObj = new Warehouse($fieldsVal);
                $WSavObj->save(true);
                // go to warehouse list
                util_redirect("warehouse.php");
            }
        }else if ($this->id > 0){
            /* ------------------------------------------------------------------------------ */
            // get warehouse
            $WObj = new Warehouse($this->id);
            $this->warehouse_name = $WObj->getWarehouseName();
            $this->addressline1 = $WObj->getAddressLine1();
            $this->addressline2 = $WObj->getAddressLine2();
            $this->stateregion = $WObj->getStateRegion();
            $this->citytown = $WObj->getCityTown();
            $this->postzipcode = $WObj->getPostZipcode();
            $this->countryid = $WObj->getCountryId();
            $this->phone = $WObj->getPhone();
            $this->description = $WObj->getDescription();
            $this->warehouse_code = $WObj->getWarehouseCode();
            $this->is_active = $WObj->getIsActive();
        }

        /* ------------------------------------------------------------------------------ */
        // process delete
        if ($this->action == "confirmed_delete") {
            // save times and details
            $WDelObj = new Warehouse($this->id);
            $WDelObj->delete();
            //$WDelObj->setDeletedq('Y');
            //$WDelObj->save(true);
            // go to courier list
            util_redirect("warehouse.php");
        }
        /* ------------------------------------------------------------------------------ */
        $this->setTitle("Admin - Warehouse Details");
    }

    /**
     * Returns boolean to indicate if the form is valid
     *
     */
    private function validate_form() {
        $chekWarehouseName = WarehouseFilter::checkColExsit("warehouse_name", $this->warehouse_name, $this->id);
        // Check that the warehouse name already exist
        if (count($chekWarehouseName) > 0) {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Warehouse name already exist.";
        }
        $chekWarehouseCode = WarehouseFilter::checkColExsit("warehouse_code", $this->warehouse_code,$this->id);
        // Check that the warehouse name already exist
        if (count($chekWarehouseCode) > 0) {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Warehouse code already exist.";
        }
        // Check that the warehouse name is not blank
        if (trim($this->warehouse_name) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Warehouse name cannot be blank.";
        }
        // Check the address line 1
        if (trim($this->addressline1) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Address Line 1 must be given.";
        }
        // Check the stateregion
        if (trim($this->stateregion) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "State/region must be given.";
        }
        if (trim($this->countryid) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Country must be given.";
        }

        return ($this->error_msg == "");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?> 
