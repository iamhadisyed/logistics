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


        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
            $csvObj = new AccountData();
            $batch_number = $_REQUEST['batch'];
            if (trim($batch_number) != '') {
                $sql = "SELECT * FROM account_data WHERE  batch_number='" . $batch_number . "' ORDER BY id ASC";
                $res = $csvObj->getDataFromSql($sql);
                if (count($res) > 0) {
                    $totalWeight = 0;
                    $totalSupplierPrice = 0;
                    $totalPrice = 0;
                    $totalOweWeight = 0;
                    $totalTotalWeight = 0;

                    $totalBacicCharges = 0;
                    $totalFuelCharges = 0;
                    $totalAdditionalCharges = 0;
                    $totalRemoteAreaCharges = 0;
                    $totalExtra = 0;
                    $totalAncillaryCharges = 0;



                    $returnString = "Account, Tracking Number, Supplier Code, Service Code, Customer Reference Number, Collection Date, Delivery Name, Delivery Postcode, Supplier Weight, OWE Weight, Weight Difference,  Customer Account, Remote Area, Consignment Status, Basic Charges, Fuel Charges, Additional Charges, Remotearea Charges, Extra, Ancillary Charges, Supplier Price, Price, Price Margin, Message \r\n";
                    foreach ($res as $invoiceData) {
                        $returnString .= $this->removeChar($invoiceData->getSupplierAccount()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getTrackingNumber()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getSupplierCode()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getServiceCode()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getCustomerOrderNumber()) . ",";
                        $returnString .= $this->removeChar($invoiceData->getCollectionDate()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getDeliveryName()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getDeliveryPostcode()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getWeight()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getOweWeight()) . ", ";
                        $totalWeightDiff = $this->removeChar(number_format(((float) $invoiceData->getWeight() - (float) $invoiceData->getOweWeight()), 3));
                        $returnString .= $this->removeChar($totalWeightDiff) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getCustomerAccount()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getRemoteArea()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getConsignmentStatus()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getBasicCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getFuelCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getAdditionalCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getRemoteAreaCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getExtra()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getAncillaryCharges()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getSupplierPrice()) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getPrice()) . ", ";
                        $returnString .= $this->removeChar(number_format(((float) $invoiceData->getPrice() - (float) $invoiceData->getSupplierPrice()), 2)) . ", ";
                        $returnString .= $this->removeChar($invoiceData->getMessage()) . ", ";

                        $returnString .= "\r\n";
                        $totalWeight += (float) $invoiceData->getWeight();
                        $totalOweWeight += (float) $invoiceData->getOweWeight();
                        $totalTotalWeight += (float) $totalWeightDiff;
                        $totalBacicCharges += (float) $invoiceData->getBasicCharges();
                        $totalFuelCharges += (float) $invoiceData->getFuelCharges();
                        $totalAdditionalCharges += (float) $invoiceData->getAdditionalCharges();
                        $totalRemoteAreaCharges += (float) $invoiceData->getRemoteAreaCharges();
                        $totalExtra += (float) $invoiceData->getExtra();
                        $totalAncillaryCharges += (float) $invoiceData->getAncillaryCharges();
                        $totalSupplierPrice += (float) $invoiceData->getSupplierPrice();
                        $totalPrice += (float) $invoiceData->getPrice();
                    }
                    $returnString .= "\r\n";
                    $returnString .= ",,,,,,TOTAL,, " . str_replace(',', '', number_format($totalWeight, 3)) . ", " . str_replace(',', '', number_format($totalOweWeight, 3))
                            . ", " . str_replace(',', '', number_format($totalTotalWeight, 3)) . ",,,,"
                            . str_replace(',', '', number_format($totalBacicCharges, 2)) . ","
                            . str_replace(',', '', number_format($totalFuelCharges, 2)) . ","
                            . str_replace(',', '', number_format($totalAdditionalCharges, 2)) . ","
                            . str_replace(',', '', number_format($totalRemoteAreaCharges, 2)) . ","
                            . str_replace(',', '', number_format($totalExtra, 2)) . ","
                            . str_replace(',', '', number_format($totalAncillaryCharges, 2)) . ","
                            . str_replace(',', '', number_format($totalSupplierPrice, 2)) . ","
                            . str_replace(',', '', number_format($totalPrice, 2)) . ", " . str_replace(',', '', (number_format(($totalPrice - $totalSupplierPrice), 2))) . ", ," .
                            "\r\n";
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
                    if (move_uploaded_file($csv_file['tmp_name'], SETTING_DIR_ASSETS . "csv/account_gp/" . $new_file_name)) {
                        $output['file_name'] = $new_file_name;
                        Consignment::runQuery("UPDATE account_data SET status = '0' WHERE user_account = '" . DbAccess3::escape($account) . "'");
                        $load_data_sql = "LOAD DATA LOCAL INFILE '" . SETTING_DIR_ASSETS . "csv/account_gp/" . $new_file_name . "' INTO TABLE `account_data` FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                              `supplier_account`, 
                                              `tracking_number`,
                                              `supplier_code`,
											  `service_code`,
                                              `customer_order_number`,
                                              `collection_date`,
                                              `delivery_name`,
                                              `delivery_postcode`,
                                              `weight`,
                                              `supplier_price`											
                                            ) SET user_account =  '" . $account . "' , status='1' , is_complete='0', batch_number= '" . $batchNumber . "', customer_reference_number =  customer_order_number";

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
            $csvObj = new AccountData();
            $countSql = "SELECT id FROM account_data WHERE user_account = '" . $account . "' and batch_number='" . $batch_number . "' ";
            $TotalRes = $csvObj->getDataFromSql($countSql);
            $totalRec = count($TotalRes);


            $limit = Page::IMPORT_BATCH_MAX;
            $start = $_POST['start'];
            $output = array();

            $output['more'] = 1;
            $output['next'] = $start + $limit;
            $sql = "SELECT * FROM account_data WHERE user_account = '" . $account . "' and batch_number='" . $batch_number . "' ORDER BY id ASC LIMIT " . $start . "," . $limit;
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

                    /*

                     */
                    Consignment::runQuery("UPDATE 
											account_data 
										SET 
											customer_account = '" . ((trim($result['CUATOMER_ACCOUNT']) != '') ? $result['CUATOMER_ACCOUNT'] : $result['ACCOUNT']) . "',
											is_complete = 1, status = 0, message = 'success', 
											price = '" . $result['AMOUNT'] . "', 
											remote_area = '" . $result['REMOTE'] . "', 
											owe_weight = '" . $result['owe_weight'] . "',
											basic_charges = '" . $result['BASIC_CHARGE'] . "',
											fuel_charges = '" . $result['FUEL_CHARGE'] . "',
											additional_charges = '" . $result['ADDITIONAL_CHARGE'] . "',
											remote_area_charges = '" . $result['REMOTE_CHARGE'] . "',
											extra = '" . $result['EXTRA_CHARGE'] . "',
											ancillary_charges = '" . $result['ANCILLARY_CHARGE'] . "',
											consignment_status = '" . $result['CONSIGNMENT_STATUS'] . "'
										WHERE id = '" . $csv->getId() . "' and batch_number='" . $batch_number . "'");
                    $successCount++;
                } else {
                    Consignment::runQuery("UPDATE account_data SET is_complete = 1, status = 0, message = '" . $result['MESSAGE'] . "' , customer_account = '" . $result['CUATOMER_ACCOUNT'] . "'WHERE id = '" . $csv->getId() . "' and batch_number='" . $batch_number . "'");
                    $failureCount++;
                    $output['error_message'][] = $result['error'];
                }
            }
            //$output['sql'] = $sql;
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
            var urlPage = 'gp_report.php';
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
        <ul class="breadcrumb">
            <li><a href="../main/index.php"><?php echo Translation::GetCaption("HOME"); ?></a></li>
            <li><a href="../main/client_list.php"><?php echo Translation::GetCaption("CONSIGNMENT"); ?></a></li>
            <li><a href="#"><?php echo Translation::GetCaption("IMPORT"); ?></a></li>
        </ul>
        <div class="main_formpage">
            <!--NEW Block Start-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-th-large"></i><? echo Translation::GetCaption("GP_REPORT"); ?></div>
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
                                <a id="btnSubmitImport" href="javascript:{};" class="btn btn-primary btn_save margin-right-10"><span></span><? echo Translation::GetCaption("IMPORT"); ?></a>
                                <a id="btnCancelImport" href="javascript:{};" class="btn btn-danger btn_cancel"><span></span><? echo Translation::GetCaption("CANCEL"); ?> </a>
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
    private function accountBookingData(AccountData $file_object, $account_number) {
        $return = array();
        $return['status'] = true;
        $return['error'] = '';
        $error_array = array();
        //$return['time1'] = $this->time_elapsed($now);
        $now = microtime(true);
        if (trim($file_object->getTrackingNumber()) != '') {
            $remoteCharges = 'NO';
            $amount = 0.00;
            $consignmentAccount = '';
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->setFilter(" c.awb = '" . $file_object->getTrackingNumber() . "'");
            //$consignmentFilter->addHawbFilter($file_object->getCustomerOrderNumber());
            $consignmentFilter->setRowsPerPage(5);
            $consignmentFilter->setOffset(0);
            $consignmentData = $consignmentFilter->getPagingList();

            if (count($consignmentData) > 0) {
                foreach ($consignmentData as $keyData => $consignment) {
                    $consignmentStatus = $consignment->getConsignmentStatus();
                    $OweWeight = ($consignment->getWeight() > $consignment->getVolWeight()) ? $consignment->getWeight() : $consignment->getVolWeight();

                    $remoteCharges = $consignment->getRemoteCharges();
                    $basicCharges = number_format($consignment->getBasicCharges(), 2);
                    $fuelCharges = number_format($consignment->getFuelCharges(), 2);
                    $AdditionalCharges = number_format($consignment->getAdditionalCharges(), 2);
                    $RemoteAreaCharge = number_format($consignment->getRemoteAreaCharge(), 2);
                    $Extra = number_format($consignment->getExtra(), 2);
                    $AncillaryCharges = number_format($consignment->getAncillaryCharges(), 2);
                    $amount = ( (float) $basicCharges + (float) $fuelCharges + (float) $AdditionalCharges + (float) $RemoteAreaCharge + (float) $Extra + (float) $AncillaryCharges );
                    $consignmentAccount = $consignment->getAccount();
                }

                $return['status'] = true;
                $return['owe_weight'] = $OweWeight;
                $return['REMOTE'] = $remoteCharges;
                $return['CONSIGNMENT_STATUS'] = $consignmentStatus;
                $return['BASIC_CHARGE'] = $basicCharges;
                $return['FUEL_CHARGE'] = $fuelCharges;
                $return['ADDITIONAL_CHARGE'] = $AdditionalCharges;
                $return['REMOTE_CHARGE'] = $RemoteAreaCharge;
                $return['EXTRA_CHARGE'] = $Extra;
                $return['ANCILLARY_CHARGE'] = $AncillaryCharges;
                $return['AMOUNT'] = $amount;
                $return['ACCOUNT'] = $consignmentAccount;
                $return['CUATOMER_ACCOUNT'] = $consignmentAccount;
            } else {
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->setFilter(" c.hawb = '" . $file_object->getCustomerOrderNumber() . "'");
                $consignmentFilter->setRowsPerPage(5);
                $consignmentFilter->setOffset(0);
                $consignmentData = $consignmentFilter->getPagingList();
                if (count($consignmentData) > 0) {
                    foreach ($consignmentData as $keyData => $consignment) {
                        $consignmentStatus = $consignment->getConsignmentStatus();
                        $OweWeight = ($consignment->getWeight() > $consignment->getVolWeight()) ? $consignment->getWeight() : $consignment->getVolWeight();
                        $remoteCharges = $consignment->getRemoteCharges();
                        $basicCharges = number_format($consignment->getBasicCharges(), 2);
                        $fuelCharges = number_format($consignment->getFuelCharges(), 2);
                        $AdditionalCharges = number_format($consignment->getAdditionalCharges(), 2);
                        $RemoteAreaCharge = number_format($consignment->getRemoteAreaCharge(), 2);
                        $Extra = number_format($consignment->getExtra(), 2);
                        $AncillaryCharges = number_format($consignment->getAncillaryCharges(), 2);
                        $amount = ( (float) $basicCharges + (float) $fuelCharges + (float) $AdditionalCharges + (float) $RemoteAreaCharge + (float) $Extra + (float) $AncillaryCharges );
                        $consignmentAccount = $consignment->getAccount();
                    }
                    $return['status'] = true;
                    $return['owe_weight'] = $OweWeight;
                    $return['REMOTE'] = $remoteCharges;
                    $return['CONSIGNMENT_STATUS'] = $consignmentStatus;
                    $return['BASIC_CHARGE'] = $basicCharges;
                    $return['FUEL_CHARGE'] = $fuelCharges;
                    $return['ADDITIONAL_CHARGE'] = $AdditionalCharges;
                    $return['REMOTE_CHARGE'] = $RemoteAreaCharge;
                    $return['EXTRA_CHARGE'] = $Extra;
                    $return['ANCILLARY_CHARGE'] = $AncillaryCharges;
                    $return['AMOUNT'] = $amount;
                    $return['ACCOUNT'] = $consignmentAccount;
                    $return['CUATOMER_ACCOUNT'] = $consignmentAccount;
                } else {
                    $return['status'] = false;
                    $return['MESSAGE'] = 'TRACKING NUMBER NOT FOUND IN SYSTEM';
                }
            }
        } else {
            $return['status'] = false;
            $return['MESSAGE'] = 'NO TRACKING NUMBER';
        }
        //exit;
        return $return;
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
