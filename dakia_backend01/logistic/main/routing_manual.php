<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'carrier.class',
    'carrierfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'customizedservicesroutinglog.class',
    'customizedservicesroutinglogfilter.class',
    'services.class',
    'servicefilter.class',    
    'products.class',
    'productfilter.class',
]);
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $setTypeOption = '';
    private $setServiceType = '';
    private $ServiceRegion = 'INT';

    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'products.php' => 'Routing',
            'Manual'
        );
        $user = SessionManager::getUser();
        if ($user->getUserType() != "admin" && $user->getUserType() != "finance") {
            util_redirect("index.php");
        }
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'getServices') {
            $carrier = $this->form_vars['carrier'];
            if ($carrier > 0) {
                $output .= Services::getServicesList($service_name, $carrier);
            }
            echo $output;
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'save_routine') {
            $productId = $this->form_vars['routing_name'];
            $country = $this->form_vars['country'];
            $fromweight = $this->form_vars['fromweight'];
            $toweightlimit = $this->form_vars['toweight'];
            $serviceId = $this->form_vars['service_name'];
            $output = array();

            if (trim($productId) > 0 && trim($country) != '' && $fromweight >= 0 && $toweight <= 30 && $serviceId > 0) {
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addIdFilter($service_id);
                $serviceList = $serviceFilter->getColumnList("name, to_weight");

                if (count($serviceList) > 0) {
                    $serviceName = $serviceList[0]->getName();
                    $maxAllowedweight = $serviceList[0]->getToWeight();
                }

                if ($toweight <= $maxAllowedweight) {

                    $csv = "";
                    $cr = "\r\n";
                    while ($fromweight < $toweightlimit) {
                        if ($fromweight < 2)
                            $toweight = $fromweight + 0.25;
                        else
                            $toweight = $fromweight + 0.5;

                        $psr = new PartnerServicesRoutingFilter();
                        $psr->addWeightRangeFilter($fromweight, $toweight);
                        $psr->addCountryFilter($country);
                        $psr->addFieldFilter("product_id", $productId);
                        $psrList = $psr->getList();

                        if (count($psrList) > 0) {

                            foreach ($psrList as $psr) {
                                $csv .= $psr->getCountryIso() . ",";
                                $csv .= $psr->getFromWeight() . ",";
                                $csv .= $psr->getToWeight() . ",";
                                $csv .= $psr->getStatus() . ",";
                                $csv .= $psr->getProductId() . ",";
                                $csv .= $psr->getServiceId() . ",";
                                $csv .= $cr;
                            }

                            $psr = PartnerServicesRouting::updatePartnerService($country, $fromweight, $toweight, $productId, $serviceId);
                        } else {
                            $psr = PartnerServicesRouting::insertPartnerService($country, $fromweight, $toweight, $productId, $serviceId);
                        }

                        if ($fromweight < 2)
                            $fromweight += 0.25;
                        else
                            $fromweight += 0.5;
                    }
                    $folder_path = "../_assets/routing_file/routing_log";

                    if (!file_exists($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }

                    $uniqueFileName = $routing_name . "_" . date("YmdHis") . "_" . $user->getUserAccount() . "_MANUAL";

                    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                    $file_path = fopen($file_path, 'w');
                    $csvHeader = "country_iso, from_weight, to_weight, status, product_id, service_id";
                    fwrite($file_path, $csvHeader . $cr . $csv);

                    // close file
                    fclose($file_path);
                    $output['status'] = 'success';
                    $output['message'] = '<div class="alert alert-success">Rotuine Added Successfully.</div>';
                } else {
                    $output['status'] = 'fail';
                    $output['message'] = '<div class="alert alert-danger">maximum allowed weight for selected service is ' . $maxAllowedweight . '</div>';
                }
            } else {
                $output['status'] = 'fail';
                $output['message'] = '<div class="alert alert-danger">Please provide all required value.</div>';
            }
            echo json_encode($output);
            exit;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>

        <?php
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#carrier').change(function () {
                    var carrier = $('#carrier').val();
                    $.ajax({
                        url: "routing_manual.php",
                        data: {action: 'getServices', carrier: carrier},
                        type: 'post',
                        success: function (response) {
                            $('#service_name').empty();
                            $('#service_name').append(response);
                            $('#service_name').selectpicker('refresh');
                        }
                    });

                });
                $('#saveRoutine').click(function () {
                    var routing_name = $('#routing_name').val();
                    var country = $('#country').val();
                    var fromweight = $('#fromweight').val();
                    var toweight = $('#toweight').val();
                    var service_name = $('#service_name').val();
                    var form_data = new FormData();
                    form_data.append('action', 'save_routine');
                    form_data.append('routing_name', routing_name);
                    form_data.append('country', country);
                    form_data.append('fromweight', fromweight);
                    form_data.append('toweight', toweight);
                    form_data.append('service_name', service_name);
                    $.ajax({
                        url: 'routing_manual.php',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            $("#successmsg").show();
                            $("#statusReponse").html(response.message);
                        }
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
        <?php
        $sessionUser = SessionManager::getUser();
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-dropbox"></i>
                            Add New Routing
                    </div>
                    <div class="actions">
                        <a href="routing_bulk.php" class="btn blue" id="routing_bulk" name="routing_bulk">
                            <i class="fa fa-upload"></i> Upload CSV</a>
                        <a href="products.php" class="btn blue" id="view" name="view">
                            <i class="fa fa-list"></i> List </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12" id="successmsg">
                            <div id="statusReponse"> </div>
                        </div>
                    </div>

                    <?php errorList::getItem()->render();
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <div class="input-group input-group-sm" >
                                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                                    <?php
                                    echo Ddl::generateDDL('routing_name', 'ProductFilter', ' status = "1"', 'product_name', 'id', $routing_name, ' class="form-control select2" required="" data-show-subtext="false" data-toggle="tooltip"  title="Select Product" data-original-title="Product"', 'Select Product', '', 'routing_name', 'product');
                                    ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country </label>
                                <div class="input-group input-group-sm" ><span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                                    <?php
                                    echo Ddl::generateCountryDDL('country', $country, 'id');
                                    ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Weight</label>
                                <div class="input-group input-group-sm" ><span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                                    <!-- Not able to use DDL as value of option is coming in point and cannot pass those as key in array. When passing with single quotes its giving wrong value. -->
                                    <select name="fromweight" id="fromweight" class="form-control selectpicker select2" data-live-search="true" title="From Weight">
                                        <?php
                                        $weightCount = 0;
                                        while ($weightCount < 30) {
                                            if ($weightCount == $fromweight)
                                                echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
                                            else
                                                echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
                                            if ($weightCount < 2)
                                                $weightCount += 0.25;
                                            //elseif($weightCount<10)$weightCount+=0.5;
                                            else
                                                $weightCount += 0.5;
                                        }
                                        ?>	                        
                                    </select><span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Weight</label>
                                <div class="input-group input-group-sm" ><span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                                    <select name="toweight" id="toweight" class="form-control selectpicker select2" data-live-search="true" title="To Weight">
                                        <?php
                                        $weightCount = 0.25;
                                        while ($weightCount <= 30) {
                                            if ($weightCount == $toweight)
                                                echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
                                            else
                                                echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
                                            //echo "<option value=".$weightCount.">".$weightCount."</option>";
                                            if ($weightCount < 2)
                                                $weightCount += 0.25;
                                            //elseif($weightCount<10)$weightCount+=0.5;
                                            else
                                                $weightCount += 0.5;
                                        }
                                        ?>                     
                                    </select><span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Carrier </label>
                                <div class="input-group input-group-sm" >
                                    <span class="input-group-addon"> 
                                        <i class="fa fa-bars"></i></span>
                                    <?php
                                    // Ask for DDL
                                    echo Ddl::generateCarrierDDLWithImage('carrier', $carrier, 'id');
                                    ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Service</label>
                                <div class="input-group input-group-sm" ><span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                                    <?php
                                    echo Ddl::generateArrayDDL('service_name', array(), $service_name, 'Select Service', ' class="form-control select2" rel="tooltip"', '', $service_name, "Select Services");
                                    ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <a href="#" class="btn btn-primary saveRoutine" id="saveRoutine" ><span></span>Save</a>
                                <a href="../main/products.php" id="btnCancel" class="btn btn-default btn_cancel"><span></span>Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>  
                <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
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
