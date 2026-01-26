<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    // status colours
    private $colours_map = array("active" => "state_valid",
        "inactive" => "state_held"
    );

    /*     * *
     * Controller logic
     */

    protected function init() {
        // user must be CLIENT
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_USER_LIST);

        $action = ((isset($this->form_vars["form_action"])) ? $this->form_vars["form_action"] : "show");

        // take appropriate action
        switch ($action) {
            case "show": // show for first time
                break;
            default:
                break;
        }
        t_on(); // turn on trace for this page
        // common initialisation for ths page
        $this->setTitle("Services List");

        // Tool bar
        // Number of valid consignments
        // Tool bar
//        $toolbar = Toolbar::getItem();
//        //$toolbar->showPrintOption($this->num_valid > 0);
//        $toolbar->showSearchOption();
//        $toolbar->showImportOption();
//        $toolbar->showLabelList();
//        $toolbar->showWarehousePage();
//        $toolbar->showAddConsignment();
//        $toolbar->showCSVOption();
//        $toolbar->showReleaseList();
//        $toolbar->showConsignmentList();
//        $toolbar->showSaveAddress();
//		$toolbar->showSearchOption();
//	    $toolbar->showAddUser();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
    }

    /**
     * Page-specific buttons
     */
    protected function renderHead() {
        ?>
        <script type="text/javascript">
		<!--
		$(document).ready(function(){

			// Export
			$("#btnExport").click(function() {
				location.href="../main/export_list.php";
			});
		});
		-->
		</script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/tracking_estimeted_list.php">Tracking Data</a></li>
            <li><a href="#">List</a></li>
        </ul>
        <?php
        // transfer form variables into local values (form variables come from parent)
        //	foreach ($this->form_vars as $key=>$val) { $$key = $val; }
        // Get clients
        $trackingEstimatedTimefilter = new TrackingEstimatedTimeFilter();
        $trackingEstimatedTimeList = $trackingEstimatedTimefilter->getColumnList("id, handeling_code, country_iso, estimated_time");
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-directions"></i>
        <?php errorList::getItem()->render(); ?> Tracking Estimated Time Listings</div>
                <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
            </div>

            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <label></label>
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th id="col1" class="red-back"></th>
                                        <th id="col1" class="red-back">Service Code</th>
                                        <th id="col2" class="red-back">Country</th>
                                        <th id="col3" class="red-back">Message</th>

                                    </tr>
                                </thead>
        <?php
        foreach ($trackingEstimatedTimeList as $trackingData) {
            ?>
                                    <tr class="<?php /* ?><?php echo $this->colours_map[($service->getActive() ? "active":"inactive")]; ?><?php */ ?>">

                                        <td><a href="tracking_estimate_time.php?id=<?php echo $trackingData->getId(); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                        </td>
                                        <td><a href="tracking_estimate_time.php?id=<?php echo $trackingData->getId(); ?>"><?php
                        $userService = new ServiceFilter();
                        $userService->addCodeFilter($trackingData->getHandelingCode());
                        $serviceList = $userService->getColumnList("name");
                        echo $serviceList[0]->getName();
            ?></a>
                                        </td>
                                        <td><?php
                                                $userCountry = new CountryFilter();
                                                $userCountry->addIsoFilter($trackingData->getCountryIso());
                                                $countryList = $userCountry->getColumnList("name");
                                                if (count($countryList) > 0)
                                                    echo $countryList[0]->getName();
                                                else
                                                    echo $trackingData->getCountryIso();
                                                ?></td>
                                        <td><?php echo $trackingData->getEstimatedTime(); ?></td>
                                    </tr>
                                            <?php
                                        }
                                        ?>
                            </table>	 
                        </div>
                    </div>
                </div>
                <div class="row" style="text-align:center;"> 
                    <strong class="cnls"><?php echo sizeof($trackingEstimatedTimeList) . " Service Listed" ?></strong>
        <?php
        // report any errors
        errorList::getItem()->render();
        ?>
                </div>
            </div>

        </div>
        </div>


        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>