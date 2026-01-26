<?php
// get settings
require_once("../includes/settings/config.inc.php");

ini_set('session.gc_maxlifetime', '-1');

class Page extends BasePage {

    //how many bookings to import/validate at once?
    const IMPORT_BATCH_MAX = 100;
    const IMPORT_FILE_DIR = "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/";
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

    /*     * *
     * Controller logic
     */

    protected function init() {
        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        $user = SessionManager::getUser();

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'customer.php' => Translation::GetCaption("CUSTOMER"),
            Translation::GetCaption("PRICING")
        );

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
            $csvObj = new PricingBulkData();
            $batch_number = $_REQUEST['batch'];
            if (trim($batch_number) != '') {
                $sql = "SELECT * FROM pricing_bulk_data WHERE  batch_number='" . $batch_number . "' ORDER BY id ASC";
                $res = $csvObj->getDataFromSql($sql);
                if (count($res) > 0) {
                    $totalWeight = 0;
                    $totalSupplierPrice = 0;
                    $totalPrice = 0;
                    $totalOweWeight = 0;
                    $totalTotalWeight = 0;

                    $returnString = "Account, Tracking Number, Hawb, basic charge, fuel surcharges, remote area, Total charge in GBP, status, Message, \r\n";
                    foreach ($res as $invoiceData) {
                        $returnString .= $this->removeChar($invoiceData->getCustomerAccount()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getTrackingNumber()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getHawb()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getBasicCharges()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getFuelCharges()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getRemoteAreaCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getTotalCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getStatus()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getMessage()) . ", ";
                        $returnString .= "\r\n";
                    }

                    header("Content-type: text/csv");
                    header("Content-Disposition: attachment; filename=" . $batch_number . ".csv");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    echo $returnString;
                } else
                    echo "No Data exist";
            } else
                echo "No Data exist";
            die;
        }

        if (isset($_POST['func']) && $_POST['func'] == 'upload_csv_file') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $batchNumber = $account . "_" . time();
                    if (move_uploaded_file($csv_file['tmp_name'], SETTING_DIR_ASSETS . "csv/pricing_bulk_report/" . $new_file_name)) {
                        $output['file_name'] = $new_file_name;
                        Consignment::runQuery("UPDATE pricing_bulk_data SET status = '0' WHERE user_account = '" . DbAccess3::escape($account) . "'");
                        $load_data_sql = "LOAD DATA LOCAL INFILE '" . SETTING_DIR_ASSETS . "csv/pricing_bulk_report/" . $new_file_name . "' INTO TABLE `pricing_bulk_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                `customer_account`, 
                                `tracking_number`,
                                `hawb`,
                                `basic_charges`,
                                `fuel_charges`,
                                `remote_area_charges`,
                                `ancillary_charges`,
                                `total_charges`
                                
                            ) 
                            SET user_account =  '" . $account . "', status='1' , is_complete='0', batch_number= '" . $batchNumber . "'";

                        $output['sql'] = $load_data_sql;
                        $output['batch_number'] = $batchNumber;
                        //Consignment::runQuery($load_data_sql);
                        $res = DbAccess3::runQueryWithError($load_data_sql);
                        if ($res === false) {
                            $error = DbAccess3::$dbError;
                            $output['message'] = $error[0];
                            $output['status'] = 'fail';
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
            $batch_number = $_POST['batch_number'];
            $user = SessionManager::getUser();
            $account = $user->getAccount();
            $csvObj = new PricingBulkData();
            $countSql = "SELECT id FROM pricing_bulk_data WHERE user_account = '" . $account . "' and batch_number='" . $batch_number . "' ";
            $TotalRes = $csvObj->getDataFromSql($countSql);
            $totalRec = count($TotalRes);


            $limit = Page::IMPORT_BATCH_MAX;
            $start = $_POST['start'];
            $output = array();

            $output['more'] = 1;
            $output['next'] = $start + $limit;
            $sql = "SELECT * FROM pricing_bulk_data WHERE user_account = '" . $account . "' and batch_number='" . $batch_number . "' ORDER BY id ASC LIMIT " . $start . "," . $limit;
            $output['QUERY'] = $sql;
            $res = $csvObj->getDataFromSql($sql);

            $account_number = $user->getUserAccount();

            $successCount = 0;
            $failureCount = 0;
            $output['error_message'] = array();
            foreach ($res as $csv) {
                $output['id'][] = $csv->getId();
                $result = $this->accountBookingData($csv, $account_number);
                if ($result['status']) {
                    Consignment::runQuery("
                        UPDATE 
                            pricing_bulk_data 
                        SET 
                            is_complete = 1, status = 0, message = 'success', customer_account = '" . $result['CUATOMER_ACCOUNT'] . "'
                        WHERE 
                            id = '" . $csv->getId() . "' and batch_number='" . $batch_number . "'");
                    $successCount++;
                } else {
                    Consignment::runQuery("UPDATE pricing_bulk_data SET is_complete = 1, status = 0, message = '" . $result['MESSAGE'] . "' , customer_account = '" . $result['CUATOMER_ACCOUNT'] . "' WHERE id = '" . $csv->getId() . "' and batch_number='" . $batch_number . "'");
                    $failureCount++;
                    $output['error_message'][] = $result['error'];
                }
            }

            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');
            if (($start + $limit) >= $totalRec) {
                $output['more'] = 0;
            }

            echo json_encode($output);
            exit;
        }

        // common initialisation for ths page
        $this->setTitle("Import Bookings");
    }

    /**
     * Force page refresh if importing
     */
    protected function renderHead() {
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script type="text/javascript">
            var urlPage = 'pricing_bulk.php';
            $(document).ready(function () {

                $("#btnSubmitImport").click(function () {
                    $('#console_window').html('');
                    $('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#file_in').prop('files')[0];
                    var form_data = new FormData();
                    var batch_number = '';
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: urlPage,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#console_window').append('Start Importing data<br />');
                                batch_number = response.batch_number
                                console.log(batch_number);
                                importCSV(0, batch_number);

                            } else {
                                $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                            }
                        }
                    });
                    return false;
                });
            });
            function importCSV(startfrom, batch_number) {

                var batch_limit = '<?php echo Page::IMPORT_BATCH_MAX; ?>'
                batch_limit = parseInt(batch_limit);
                $('#console_window').append('Importing records from ' + (startfrom + 1) + ' to ' + (startfrom + batch_limit) + '...');
                var form_data = new FormData();
                form_data.append('func', 'import_file');
                form_data.append('start', startfrom);
                form_data.append('batch_number', batch_number);
                $.ajax({
                    url: urlPage,
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
                            importCSV(response.next, batch_number);
                        } else {
                            $('#console_window').append(response.message + '<br /> <a href="' + urlPage + '?action=download&batch=' + batch_number + '" class="btn btn-primary btn_save margin-right-10">Click Here to Download Report</a>');
                            $('#console_window').append('<br /> After clicking the button above you will download report for data imported via csv file. ');
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
            <!--NEW Block Start-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-th-large"></i><? echo Translation::GetCaption("BULK_PRICING_UPLOAD"); ?></div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>
                <div class="portlet-body" >
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="col-md-2">
                                <label class="control-label"><? echo Translation::GetCaption("SELECT_FILE_TO_IMPORT"); ?></label>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input class="form-control" id="file_in" name="file_in" type="file" value="" />
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <a id="btnSubmitImport" href="javascript:{};" class="btn btn-primary btn_save margin-right-10"  onclick="return confirm('Are you sure you want to upload file?');" ><span></span><? echo Translation::GetCaption("IMPORT"); ?></a>
                                <a id="btnCancelImport" href="bookings.php" class="btn btn-danger btn_cancel"><span></span><? echo Translation::GetCaption("CANCEL"); ?></a>
                                <a id="btnCancel" href="../_assets/csv/pricing_bulk_report/pricing_bulk_template.csv" class="btn btn-danger btn-cancel" target="_blank" ><? echo Translation::GetCaption("TEMPLATE"); ?></a>

                            </div>
                        </div>                        
                    </div>   
                    <div class="row">
                        <div class="col-md-12">
                            <div id="console_window" style="clear:both;background-color: #000;color: #FFF; padding: 15px;">

                            </div>
                        </div>
                    </div>       
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
    private function accountBookingData(PricingBulkData $file_object, $account_number) {
        $result = array();
        $result['status'] = true;
        $result['error'] = '';
        $error_array = array();
        //$return['time1'] = $this->time_elapsed($now);
        $now = microtime(true);
        if (trim($file_object->getTrackingNumber()) != '' && trim($file_object->getHawb()) != '') {
            $remoteCharges = 'NO';
            $amount = 0.00;
            $consignmentAccount = '';
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->setFilter("and awb = '" . $file_object->getTrackingNumber() . "' and hawb = '" . $file_object->getHawb() . "' and  (InvoiceId <= 0 or InvoiceId is null or InvoiceId = '') and consignment_status not in ('recycled', 'invalid')");
            $consignmentData = $consignmentFilter->getColumnList(' id, hawb, account ');

            if (count($consignmentData) > 0) {
                foreach ($consignmentData as $keyData => $consignment) {
                    $invoiceSaveFunction = true;
                    $invoiceDetail = new InvoiceDetailFilter();
                    $invoiceDetail->addQueryFilter(" consignment_id = '" . $consignment->getId() . "'");
                    $invoiceDetail->orderBySort('id desc');
                    $invoiceDetailsFilter = $invoiceDetail->getList();
                    if (count($invoiceDetailsFilter) > 0) {
                        $invoiceDetailData = $invoiceDetailsFilter[0];
                        if (trim($invoiceDetailData->getInvoiceNo()) != '' && trim($invoiceDetailData->getInvoiceNo()) != '0')
                            $invoiceSaveFunction = false;
                    }
                    else {
                        $invoiceDetailData = new InvoiceDetail();
                    }
                    $invoiceDetailData->setConsignmentId($consignment->getId());
                    $invoiceDetailData->setHawb($consignment->getHawb());
                    $invoiceDetailData->setBasicCharges($file_object->getBasicCharges());
                    $invoiceDetailData->setFuelCharges($file_object->getFuelCharges());
                    $invoiceDetailData->setAncillaryCharges($file_object->getAncillaryCharges());
                    $invoiceDetailData->setRemoteAreaCharge($file_object->getRemoteAreaCharges());
                    $invoiceDetailData->setAdditionalCharges(0);
                    $invoiceDetailData->setDateCreated(date('Y-m-d h:i:s'));
                    $invoiceDetailData->setAddedBy($account_number);
                    $invoiceDetailData->setTariffName('UPLOADED FROM CSV');

                    $basicCharges = (float) $invoiceDetailData->getBasicCharges();
                    $fuelCharges = (float) $invoiceDetailData->getFuelCharges();
                    $additionalCharges = (float) $invoiceDetailData->getAdditionalCharges();
                    $remoteAreaCharge = (float) $invoiceDetailData->getRemoteAreaCharge();
                    $onFarwordCharges = (float) $invoiceDetailData->getOnFarwordCharges();
                    $ndx = (float) $invoiceDetailData->getNdx();
                    $ddp = (float) $invoiceDetailData->getDdp();
                    $extra = (float) $invoiceDetailData->getExtra();
                    $hv = (float) $invoiceDetailData->getHv();
                    $discount = (float) $invoiceDetailData->getDiscount();
                    $ancillaryCharges = (float) $invoiceDetailData->getAncillaryCharges();
                    $amountToChages = (($basicCharges + $fuelCharges +
                            $additionalCharges + $remoteAreaCharge + $onFarwordCharges + $ndx + $ddp + $extra + $hv + $ancillaryCharges ) - $discount );
                    $invoiceDetailData->setAmount($amountToChages);

                    if ($invoiceSaveFunction === true) {
                        $invoiceDetailData->save();
                        $result['status'] = true;
                        $result['CUATOMER_ACCOUNT'] = $consignment->getAccount();
                    } else {
                        $result['status'] = false;
                        $result['MESSAGE'] = 'Shipment already invoiced';
                    }
                }
            } else {
                $result['status'] = false;
                $result['MESSAGE'] = 'TRACKING NUMBER NOT FOUND IN SYSTEM';
            }
        } else {
            $result['status'] = false;
            $result['MESSAGE'] = 'NO TRACKING NUMBER';
        }
        //exit;
        return $result;
    }

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
        return $str;
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

    public function removeChar($text) {
        $textNew = $text;
        $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));

        $textNew = preg_replace('/[\n,]/', '', $textNew);

        return $textNew;
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
