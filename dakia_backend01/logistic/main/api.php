<?php
// get settings
require_once("../includes/settings/config.inc.php");

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    public $displayname = '';
    public $account = '';

    /*
     * Controller logic
     */

    protected function init() {
        
    }

    protected function renderHead() {
        ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <link rel="stylesheet" href="/resources/demos/style.css">
         <script type="text/javascript">
            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
            });
        </script> 
        <?php
    }

    private function getCellData($countryId, $lowerLimit, $upperLimit) {
        
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    private function showMessage() {
        
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="main_formpage"> 
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i> API Testing </div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>RESULT</label>  
                            <?php
                            if (isset($_POST['defined_api'])) {
                                $client = new SoapClient(null, array(
                                    'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                                    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));
                                $results = $client->__soapCall($_POST["defined_api"], array('consignmentinformation' => $_POST["api_string"]));
                                print_r($results);
                            }
                            ?>
                        </div>
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group col-md-6">
                                    <div class="form-group col-md-12">
                                        <div class="input-group">
                                            <textarea rows="4" cols="60" value="<?php echo @$_POST["api_string"]; ?>" name="api_string" id="api_string" class="form-control" rel="tooltip" placeholder="API STRING" data-original-title="API STRING"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                            <select name="defined_api" class="form-control" rel="tooltip" placeholder="API DEFINED" data-original-title="API DEFINED">
                                                <option value="getLabels">$_GetLabels</option>
                                                <option value="getValidLabel">$_getValidLabel</option>
                                                <option value="getTracking">$_getTracking</option>
                                                <option value="importConsignmentData">$_importConsignmentData</option>
                                                <option value="importValidConsignmentData">$_importValidConsignmentData</option>
                                                <option value="importBoxData">$_importBoxData</option>
                                                <option value="addTrackingByBoxNumber">$_addTrackingByBoxNumber</option>
                                                <option value="updateBoxMawb">$_updateBoxMawb</option>
                                                <option value="updateBoxManifest">$_updateBoxManifest</option>
                                                <option value="updateConsignmentMawb">$_updateConsignmentMawb</option>
                                                <option value="EnabledServices">$_EnabledServices</option>
                                                <option value="GetRestoreLabel">$_GetRestoreLabel</option>
                                                <option value="removeInvalidLabel">$_removeInvalidLabel</option>
                                                <option value="getBulkLabels">$_GetBulkLabels</option>
                                                <option value="getMultiTracking">$_GetMultiTracking</option>
                                                <option value="GetTrackingNumberByHawb">$_GetTrackingNumberByHawb</option>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="row" style="text-align:center;">    
                        <br/>
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary btn_save" />
                        <br/>
                    </div>
                    </form>
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
