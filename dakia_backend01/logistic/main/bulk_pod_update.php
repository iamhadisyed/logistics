<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes(['inputfilebulkpod.class'],
        'autoload');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    const IMPORT_FILE_DIR = '../_assets/csv/pod_bulk_import/';
    const IMPORT_BATCH_MAX = 5;
    private $user = "";

    protected function init() {
        //if(!Permissions::checkFilePermission('bulk_pod_update.php')) 
        //            util_redirect ("index.php");
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Bulk POD Update"
        );

        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */

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
                  
                    $account = $this->user->getUsername();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $file_name = Page::IMPORT_FILE_DIR . $new_file_name;
                    $path = SETTING_DIR_ASSETS . "_assets/csv/pod_bulk_import/";
                    if (!file_exists($path))
                        @mkdir($path, 0777, true);
                    if (move_uploaded_file($csv_file['tmp_name'], $file_name)) {
                        $output['file_name'] = $new_file_name;
                        $file_object = new InputFileBulkPod($file_name);
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
            $row_idx = 0;
            $output['more'] = 1;
            $output['next_real'] = $start_real + $limit;

            //Page::IMPORT_FILE_DIR .$file_name;
            $file_object = new InputFileBulkPod(Page::IMPORT_FILE_DIR . $file_name);

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
                       
                         if($file_object->getField("trackingnumber") == "trackingnumber")
                            continue;
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
             
            }


            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');


            echo json_encode($output);
            exit;
        }
    
    }
    
    /*
     * Import one pod at a time. Updating consignment status and parcel status and entry in tracking table
     */
    private function importBooking(InputFileBulkPod $file_object) {
        
        $getError = false;
        $error = '';
        $return = array();
        $return['status'] = true;
        $return['error'] = '';
        $error_array = array();
        $trackingNumber = trim($file_object->getField("trackingnumber"));
         $pod_date = str_replace("/","-",trim($file_object->getField("poddatetime")));
        if (trim($pod_date) != "") {
                $date_delivery = date("Y-m-d H:i:s", strtotime($pod_date));
        }
        $trackPoint = $file_object->getField("trackpoint");
        $carrierDesc = $file_object->getField("carrierdescription");
        $status = $file_object->getField("status");
        if(strtolower($status) == "client requested an appointment")
            $status = "In Transit";
        if($trackingNumber != ''){
            $trackingCodes = Tracking::$oneworld_status_code;
            $owetrackingCode = array_map("strtoupper", $trackingCodes);
            $pod_value = array_search(strtoupper(trim($status)), $owetrackingCode);          
            $consignmentCode = Tracking::$oneworld_consignment_code_mapping[$pod_value];
            $consignmentStatus = Consignment::$database_status_array[$consignmentCode];
            
            if($pod_value == "")
            {
                $return['error'] = "<br /> . Unable to map  - " . $pod_value . $trackingNumber   . "<br /> ";
                $return['status'] = false;
            }
            $trackingDataFilter = new TrackingDataFilter();
            $trackingDataFilter->addFilter(" t.status_code_id = '121' and t.tracking_number = '".$trackingNumber."'");
            $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type");
            if(count($trackingDataExistsObj) <= 0){
                $ParcelFilter = new ParcelFilter();
                $ParcelFilter->bulkupdateCourierStatus(array($trackingNumber), Tracking::$oneworld_consignment_code_mapping[$pod_value]);
                TrackingData::AddVirtualTrackingToScanParcels(array($trackingNumber), $this->user, $pod_value, $date_delivery, '', $trackPoint, '','','','', $carrierDesc);
                $updateDateDelivered = "";
                if($consignmentCode == "19"){
                    $updateDateDelivered = ",date_delivered = '".strtotime($date_delivery)."'";
                }
                $setParam = "consignment_status =  '" . $consignmentStatus . "', shipment_status = '" . $consignmentCode . "'" . $updateDateDelivered;
                $wherecaluse = "awb in ('" .   $trackingNumber . "') and awb <> ''";
                $c_filer = new ConsignmentFilter();
                $c_filer->updateFunction($setParam, $wherecaluse);
            }
            else
            {
                $return['error'] = "<br /> Tracking point for delivered is already added so cannot add it for ". $trackingNumber   . "<br /> ";
                $return['status'] = false;
            }
        }
        
        
        
        return $return;
    }
  

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
      
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        
        <script type="text/javascript">
            var urlPage = 'bulk_pod_update.php';
            $(document).ready(function () {
                $("#btnSubmitImport").click(function () {
                    
                        $('#console_window').html('');
                        $('#console_window').html("Uploading CSV File....<br />");
                        var file_data = $('#csv_for_pod').prop('files')[0];

                        var form_data = new FormData();
                        form_data.append('csv_file', file_data);
                        form_data.append('func', 'upload_csv_file');
                        $.ajax({
                            url: 'bulk_pod_update.php',
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
                    url: 'bulk_pod_update.php',
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
                            $('#console_window').append(response.message + '<br />All Records imported successfully. <br /> ');
                            //$('#console_window').append('<br /> After clicking the buttom above you will see a list of all products.<br /> ');
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-search"></i>
                    <a href="bulk_pod_update.php"></a>
                    CSV Upload Panel
                </div>
                <div class="actions">
                    <a href="../csv/template_import_pod.csv" class="btn btn-success" id="btnDownloadTemplate" >
                        <i class="fa fa-download"></i> Download Template
                    </a> 
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <form method="post" action="bulk_pod_update.php.php" id="bulk_pod_update" >
                        <input type="hidden" name="func" value="download_template" >
                      
                        <div class="col-sm-6">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> Shipment Details</label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="csv_for_pod" id="csv_for_pod"> 
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                        <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImport" >Upload</a>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-sm-12">
                        <div id="console_window">
                            
                        </div>
                    </div>
                </div>
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

    public function renderHead() {
        ?>
        <style type="text/css">

        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>