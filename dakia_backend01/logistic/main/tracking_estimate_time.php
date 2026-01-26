<?php
// get settings
require_once("../includes/settings/config.inc.php");

/* * *
 * Page for editing a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    protected function init() {

        Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
        // Tool bar
//		$toolbar = Toolbar::getItem();
//	
//        //$toolbar->showPrintOption($this->num_valid > 0);
//
//        $toolbar->showSearchOption();
//        $toolbar->showImportOption();
//        $toolbar->showLabelList();
//        $toolbar->showWarehousePage();
//        $toolbar->showAddConsignment();
//        $toolbar->showCSVOption();
//        $toolbar->showReleaseList();
//        $toolbar->showConsignmentList(true);
//        $toolbar->showSaveAddress();
//		$toolbar->showSearchOption();
//	    $toolbar->showAddUser();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
        t_on(); // turn on trace for this page
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new address detailsvalidate new address details - if OK, save and return to booking list
                case "save":
                    // save new address details
                    $services = new TrackingEstimatedTime(intval($this->form_vars["id"]));

                    $services->setHandelingCode($this->form_vars["handelingCode"]);
                    $services->setCountryIso($this->form_vars["countryIso"]);
                    $services->setEstimatedTime($this->form_vars["EstimatedTime"]);

                    $error_array = array();
                    $errorFlag = false;
                    if (trim($this->form_vars["handelingCode"]) == '') {
                        $error_array[] = 'Please select handeling code';
                        $errorFlag = true;
                    }
                    if (trim($this->form_vars["countryIso"]) == '') {
                        $error_array[] = 'Please select country';
                        $errorFlag = true;
                    }
                    if (trim($this->form_vars["EstimatedTime"]) == '') {
                        $error_array[] = 'Please enter message';
                        $errorFlag = true;
                    }
                    if (!$errorFlag) {
                        $services->save();
                        util_redirect("../main/tracking_estimeted_list.php");
                    }

                    // add list of errors to error list
                    $error_list = ErrorList::getItem();
                    $error_list->addErrorList($error_array);
                    break;

                case "delete":
                    $services = new TrackingEstimatedTime(intval($this->form_vars["id"]));
                    $services->delete();
                    util_redirect("../main/tracking_estimeted_list.php");
                    break;

                // CANCEL
                // - return to booking list
                case "cancel":
                default:
                    util_redirect("../main/tracking_estimeted_list.php");
                    break;
            }
        }

        // not post back - first time this form is shown
        else {
            // get consignment id passed
            $id = util_get_num("id");
            $this->form_vars["id"] = $id;

            // get address values
            $trackingEstimated = new TrackingEstimatedTime($id);
            //
            $this->form_vars["handelingCode"] = $trackingEstimated->getHandelingCode();
            $this->form_vars["countryIso"] = $trackingEstimated->getCountryIso();
            $this->form_vars["EstimatedTime"] = $trackingEstimated->getEstimatedTime();
        }

        // common initialisation for ths page
        $this->setTitle("Estimated Time");
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>
        <script>
	function numbersonly(e){
			var unicode=e.charCode? e.charCode : e.keyCode
			if (unicode!=8)
			{
				if(unicode==46)
				{}
				else if (unicode<48||unicode>57) //if not a number
				return false //disable key press
			}
		}
    </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/tracking_estimeted_list.php">Tracking List</a></li>
            <li><a href="../main/tracking_estimate_time.php?id=<?php echo util_get_num("id"); ?>"><?php if (util_get_num("id") > 0) echo 'Edit';
        else echo 'Add'; ?></a></li>
        </ul>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="clear" ></div>
        <div class="main_formpage">
            <h1 class="heading">

            </h1>
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-directions"></i>
        <?php
        if (isset($_GET['id']) && ($_GET['id'] == "" || $_GET['id'] < 0))
            echo "Add New Tracking  Estimated Data";
        else
            echo "Edit Tracking Estimated Data";
        ?>
                    </div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                        <?php errorList::getItem()->render(); ?>
                        </div>
                        <div class="col-md-4">
                            <label>Handeling Code:</label>
                            <select id="handelingCode" name="handelingCode" class="select_dropdown form-control">
                                <option value="" >Select Service code</option>
        <?php
        $userServices = new ServiceFilter();
        if ($userServices->getCount() > 0) {

            $userServicesList = $userServices->getList("code");
            foreach ($userServicesList as $servicesUser) {
                $selected = "";
                if ($handelingCode == $servicesUser->getCode())
                    $selected = "selected='selected'";

                echo '<option value="' . $servicesUser->getCode() . '" ' . $selected . ' >' . $servicesUser->getName() . '</option>';
            }
        }
        ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Country:</label>
                            <select id="countryIso" name="countryIso" class="select_dropdown form-control">
                                <option value="" >Select Country</option>
                                <?php
                                $userCountry = new CountryFilter();
                                if ($userCountry->getCount() > 0) {

                                    $userCountryList = $userCountry->getColumnList("iso, name");
                                    foreach ($userCountryList as $countryUser) {
                                        $selected = "";
                                        if ($countryIso == $countryUser->getIso())
                                            $selected = "selected='selected'";

                                        echo '<option value="' . $countryUser->getIso() . '" ' . $selected . ' >' . $countryUser->getName() . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">

                            <label>Message</label>
                            <input type="text" class="form_field_col1 form-control" name="EstimatedTime" id="EstimatedTime" value="<?php echo @$EstimatedTime; ?>" size="100" style="background-color:#FFC" />
                        </div> 


                    </div>
                    <div class="row" style="text-align:center;"> 
                        <br/><br/>
                        <a id="btnCancel"  href="#" class="btn btn-danger btn_cancel"><span></span>Cancel</a>
                        <a id="btnSave"    href="#" class="btn btn-primary btn_save"><span></span>Save</a>
                        <a id="btnDelete"  href="#"class="btn btn-primary btn_save"><span></span>Delete </a></div>
                    <br/><br/>
                </div>

            </div>
        </div>

        </div>
        <?php
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
