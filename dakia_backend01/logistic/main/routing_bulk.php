<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes(['inputfile1.class'],
        'autoload');
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
class Page extends BasePage {

    //how many bookings to import/validate at once?
    const IMPORT_BATCH_MAX = 5;
    const IMPORT_FILE_DIR = '../_assets/routing_file/';
    // page states
    const STATUS_INITIAL = 0;
    const STATUS_GOT_FILE = 1;
    const STATUS_IMPORTING = 2;
    const STATUS_IMPORTED = 3;

    private $file_name = "";
    private $file_position = 0;
    private $import_status = Page::STATUS_INITIAL;
    private $import_num_accepted;
    private $import_num_checked;
    private $import_num_rejected;
    private $import_msg;
    private $breadcrumb = '';
    private $csv = "";
    private $productName = "";
    private $productId = "";

    /*     * *
     * Controller logic
     */

    protected function init() {
        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        $user = SessionManager::getUser();
        if ($user->getUserType() != "admin" && $user->getUserType() != "finance") {
            util_redirect("index.php");
        }
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'products.php' => 'Routing',
            'Import Routing'
        );
        if (isset($_POST['func']) && $_POST['func'] == 'upload_csv_file') {

            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name1 = $csv_file['name'];
                $path_parts = pathinfo($file_name1);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];

                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $file_name = Page::IMPORT_FILE_DIR . $new_file_name;
                    if (move_uploaded_file($csv_file['tmp_name'], $file_name)) {
                        $output['file_name'] = $new_file_name;
                        $file_object = new InputFile1($file_name);
                        if (!$file_object->exists()) {
                            $output['message'] = "Enable to open file" . $new_file_name;
                            $output['status'] = 'fail';
                            //exit;
                        }
                    } else {
                        $output['message'] = 'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = 'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == 'import_file') {

            $limit = Page::IMPORT_BATCH_MAX;
            $start = $_POST['start'];
            $start_real = $_POST['start_real'];
            $file_name = $_POST['filename'];
            $output = array();

            $output['more'] = 1;
            $output['next_real'] = $start_real + $limit;

            //Page::IMPORT_FILE_DIR .$file_name;
            $file_object = new InputFile1(Page::IMPORT_FILE_DIR . $file_name);

            $file_object->setPosition($start);

            $output['error_message'] = array();
            if ($file_object == null) {
                $output['error_message'][] = "Cannot open File" . $file_name;
            } else {
                $more_rows = true;
                $successCount = 0;
                $failureCount = 0;
                $file_object->setPosition($start);
                $output['position'] = $start . ' -- ' . print_r($file_object->setPosition($start), true);
                for ($row_idx = 0; $more_rows && ($row_idx < $limit); $row_idx++) {
                    $output['position_next'] = $file_object->getPosition() . ' -- ';

                    if ($file_object->nextRow()) {

                        
                        $result = $this->importBooking($file_object);

                        if ($result['status']) {
                            $successCount++;
                        } else {
                            $failureCount++;
                            $output['error_message'][] = $result['error'];
                        }
                        $output['next'] = $file_object->getPosition();
                    } else {
                        $more_rows = false;
                        $output['more'] = 0;
                    }
                }
                if ($this->csv != '') {
                    $folder_path = "../_assets/routing_file/routing_log";
                    if (!file_exists($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }
                    $uniqueFileName = $this->productName . "_" . date("YmdHis") . "_" . $user->getUserAccount();
                    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";
                    $file_path = fopen($file_path, 'w');
                    $csvHeader = "country_iso, from_weight, to_weight, status, product_id, service_id";

                    fwrite($file_path, $csvHeader . "\r\n" . $this->csv);

                    // close file
                    fclose($file_path);
                    $productRoutinelog = new CustomizedServicesRoutingLog();
                    $productRoutinelog->createlog($user->getId(), "", $this->productId, $this->productName . " has been update by " . $user->getUserAccount() . ". Please click here to download csv file. <a href='" . $folder_path . "/" . $uniqueFileName . ".csv'  class='btn blue' >Download</a>");
                }
            }


            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');


            echo json_encode($output);
            exit;
        }

        // common initialisation for ths page
        $this->setTitle("Import Bookings");
    }

    protected function renderHead() {
        
    }

    /**
     * Force page refresh if importing
     */
    protected function renderFooter() {
        ?>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#btnSubmitImport").click(function () {
                    if (confirm("Please make sure that you are importing routine for new product only. Otherwise it will duplicate data."))
                    {
                        $('#console_window').html('');
                        $('#console_window').html("Uploading CSV File....<br />");
                        var file_data = $('#file_in').prop('files')[0];

                        var form_data = new FormData();
                        form_data.append('csv_file', file_data);
                        form_data.append('func', 'upload_csv_file');
                        $.ajax({
                            url: 'routing_bulk.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                if (response.status == 'success') {
                                    $('#console_window').append('Start Importing data<br />');
                                    // alert(response.file_name);
                                    importCSV(0, response.file_name, 0);
                                } else {
                                    $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                                }
                            }
                        });
                    }
                    return false;
                });
                
                $('#export_routine').click(function () {
                    $('#export_routine_frm').submit();
                });
                
            });
            function importCSV(startfrom, filename, next_actual) {

                $('#console_window').show();
                var batch_limit = '<?php echo Page::IMPORT_BATCH_MAX; ?>'
                batch_limit = parseInt(batch_limit);
                $('#console_window').append('Importing records from ' + (next_actual + 1) + ' to ' + (next_actual + batch_limit) + '...');
                var form_data = new FormData();
                form_data.append('func', 'import_file');
                form_data.append('start', startfrom);
                form_data.append('start_real', next_actual);
                form_data.append('filename', filename);
                $.ajax({
                    url: 'routing_bulk.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        //console.log(response);

                        if (response.more == 1) {
                            $('#console_window').append(response.message + '<br />');

                            importCSV(response.next, filename, response.next_real);
                        } else {
                            $('#console_window').append(response.message + '<br />All Records imported successfully. <br /> <a href="services_list.php" class="btn btn-primary btn_save margin-right-10">Click Here to View Service</a>');
                            $('#console_window').append('<br /> After clicking the buttom above you will see a list of all products.<br /> ');
                        }
                    }
                });

            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-upload"></i>
                        
                            Import Routing Files
                    </div>
                    <div class="actions">
                        <a href="routing_manual.php" class="btn blue" id="manual_add_product" name="manual_add_product">
                            <i class="fa fa-plus"></i> Add Routing </a>
                        <a href="products.php" class="btn blue" id="view" name="view">
                            <i class="fa fa-list"></i> List </a>
                    </div>
                </div>
                <div class="portlet-body ">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label"><?php echo Translation::GetCaption("SELECT_FILE_TO_IMPORT"); ?></label>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="..." id="file_in" name="file_in" value=""> </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" align="center">
                        <div class="col-md-6">
                            <?php  
                            if(util_get_num("customize_service_id") > 0){
                            ?>
                                <a href="javascript:;" class="btn btn-primary btn_save margin-right-10" id="export_routine" name="export_routine"> <i class="fa fa-download"></i> Download Template </a>
                            <?php
                            }else{
                            ?>
                            <a id="btntemplate" href="../csv/product_template.csv" class="btn btn-primary btn_save margin-right-10"> <i class="fa fa-download"></i>Download Template</a>
                            <?php } ?>
                            <a id="btnSubmitImport" href="javascript:{};" class="btn btn-primary btn_save margin-right-10"><span></span><?php echo Translation::GetCaption("IMPORT"); ?></a>
                            <a id="btnCancelImport" href="client_file_con.php?id=-1" class="btn btn-default btn_cancel"><span></span><?php echo Translation::GetCaption("CANCEL"); ?> </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="console_window" style="clear:both; border-style: solid; border-width:1px;  width: 100%; padding: 15px; display: none;">

                            </div>
                        </div>
                    </div> 
                    <form id="export_routine_frm" action="get_pricing.php" method="post">
                        <input type="hidden" name="func" value="download_routing_excel" />
                        <input type="hidden" name="cutomized_service_id_for_routing" value="<?php echo util_get_num("customize_service_id"); ?>" />
                        <input type="hidden" name="import_csv_format" value="1" />
                    </form>

                </div>  
            </div>
        </div>
        <br>
        <?php
    }

    /**
     * Import one consignment
     * - set status to INVALID (ignore user feedback) or VALID
     *
     * @param $file_object (with file row already extracted)
     */
    private function importBooking(InputFile1 $file_object) {
        
        $getError = false;
        $error = '';
        $return = array();
        $return['status'] = true;
        $return['error'] = '';
        $error_array = array();
        $weight = $file_object->weightlimits;
        $product_name = $file_object->getField("productname");
        $this->productName = $product_name;
        $productFilter = new ServiceFilter();
        $productFilter->addFilter("   name = '" . $product_name . "'");
        $productList = $productFilter->getList();
        $country_iso = $file_object->getField("countryiso");
        $countryObject = Country::getCountryByIso(strtolower(trim($country_iso)));
       
        $countryObject;
        if (count($countryObject) > 0) {
            $countryId = $countryObject[0]->getId();
        } else {
            $countryId = '';
        }
     
        if (trim($product_name) != "" && $countryId > 0) {
            $test = 0;
            foreach ($weight as $keyInd => $weightArr) {
                if (count($productList) > 0) {
                    $product_id = $productList[0]->getId();
                    $this->productId = $product_id;
                    $service_name = $file_object->getField('weight' . ($keyInd + 1));
                    if (trim($service_name) != '') {

                        $serviceFilter = new ServiceFilter();
                        $serviceFilter->addCodeExactFilter($service_name);
                        $serviceFilter->addFilter(" ser.from_weight <= '" . $weightArr[0] . "' and ser.to_weight >= '" . $weightArr[1] . "' and sct.id_country = '" . $countryId . "'");
                        $serviceList = $serviceFilter->getServiceAndCountryList("ser.id, ser.code");
                        if (count($serviceList) > 0) {
                            $service_id = $serviceList[0]->getId();

                            $partnerservicesrouting = new CustomizedServicesRouting();
                            $partnerservicesrouting->setCountryId($countryId);
                            $partnerservicesrouting->setFromWeight($weightArr[0]);
                            $partnerservicesrouting->setToWeight($weightArr[1]);
                            $partnerservicesrouting->setstatus("1");
                            $partnerservicesrouting->setCustomizeServiceId($product_id);
                            $partnerservicesrouting->setServiceId($service_id);

                            $psrFilter = new CustomizedServicesRoutingFilter();
                            $psrFilter->addFieldFilter("country_id", $countryId);
                            $psrFilter->addFieldFilter("customize_service_id", $product_id);
                            $psrFilter->addFieldFilter("from_weight", $weightArr[0]);
                            $psrFilter->addFieldFilter("to_weight", $weightArr[1]);
                            $psrFilter->addFieldFilter("status", 1);

                            $psrresult = $psrFilter->getList();
                            
                            if (count($psrresult) > 0) {
                                $cr = "\r\n";
                                $psrid = array();
                                foreach ($psrresult as $psr) {
                                    $psrid[] = $psr->getId();
                                    $this->csv .= $psr->getCountryIso() . ",";
                                    $this->csv .= $psr->getFromWeight() . ",";
                                    $this->csv .= $psr->getToWeight() . ",";
                                    $this->csv .= $psr->getStatus() . ",";
                                    $this->csv .= $psr->getCustomizeServiceId() . ",";
                                    $this->csv .= $psr->getServiceId() . ",";
                                    $this->csv .= $cr;
                                }
                                CustomizedServicesRouting::deleteRoutine(" id in ('" . implode("','", $psrid) . "')");
                                $partnerservicesrouting->save();
                            } else {
                                $partnerservicesrouting->save();
                            }
                        } else {
                            $getError = true;
                            $error .= "<br>Service " . $service_name . " is not availabel against " . $country_iso . " and " . $weightArr[0] . " to " . $weightArr[1] . " is incorrect";
                        }
                    }
                } else {
                    $getError = true;
                    $error .= "<br>Product Name " . $product_name . " against " . $country_iso . " and " . $weightArr[0] . " to " . $weightArr[1] . " is incorrect";
                }
            }

           
        } else {
            $getError = true;
            $error .= "<br>Product Name " . $product_name . " against country " . $country_iso . " and " . $weightArr[0] . " to " . $weightArr[1] . " is incorrect";
        } //end for
            if ($getError) {
                $return['error'] = $error;
                $return['status'] = false;
            }
        return $return;
    }

// end function
// end function

    public function replaceSpecial($str) {
        $chunked = str_split($str, 1);
        $str = "";
        foreach ($chunked as $chunk) {
            $num = ord($chunk);
            // Remove non-ascii & non html characters
            if ($num >= 32 && $num <= 123) {
                $str .= $chunk;
            }
        }
        return mb_convert_encoding($str, 'UTF-8');
    }

    function time_elapsed($last = null) {
        //static $last = null;
        $now = microtime(true);
        if ($last != null) {
            //echo '<!-- ' . ($now - $last) . ' -->';
            return ($now - $last);
        }
        //$last = $now;
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
