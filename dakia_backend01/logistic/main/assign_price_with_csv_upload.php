<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
     'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'currency.class',
    'currencyfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class' ,
    'consignmentcharges.class' ,
    'consignmentchargesfilter.class' ,
    'consignmentchargeslog.class' ,
    'consignmentchargeslogfilter.class' ,
    'services.class' ,
    'servicefilter.class' ,
    'paymentshistory.class' ,
    'paymentshistoryfilter.class'
    
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    const IMPORT_BATCH_MAX = 5;
    const IMPORT_FILE_DIR  = "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/";
    
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Assign price with csv upload"
        );

        $this->user = SessionManager::getUser();

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            $output['currency'] = $this->form_vars['currency'];
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $account = $this->user->getUserAccountId();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $batchNumber = $account . "_" . time();
                    $type = $this->form_vars['price_type'];
                    $heading = ConsignmentCharges::getPricingColoumn($type);
                    $priceColoum = array();
                    $priceColoumExportData = [];
                    foreach($heading as $key => $h) {
                        if($key > 2) {
                            $col = str_replace([' ', '/'],'_',strtolower($h));
                            $priceColoumCreateTable[] = "`".$col."` DECIMAL(10,2) DEFAULT NULL";
                            $priceColoumInsertData[] = "`".$col."`";
                            $priceColoumExportData[] = $col;
                        }
                    }
                    $output['column_export']  = $priceColoumExportData;
                            
                    $table =  "pricing_bulk_data_". time();
                    $sql = "CREATE TABLE `$table` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `tracking_number` VARCHAR(45) DEFAULT NULL,
                            `hawb` VARCHAR(45) DEFAULT NULL,
                            `charges_reference` VARCHAR(45) DEFAULT NULL,
                            " . implode(",", $priceColoumCreateTable) . ",
                            `user_account` VARCHAR(45) DEFAULT NULL,
                            `status` TINYINT(1) DEFAULT NULL,
                            `is_complete` TINYINT(1) DEFAULT NULL,
                            `message` VARCHAR(255) DEFAULT NULL,
                            `batch_number` VARCHAR(45) DEFAULT NULL,
                            `currency` VARCHAR(3) DEFAULT NULL,
                            `data_result` TEXT DEFAULT NULL,
                            `data_result_count` TINYINT(2) DEFAULT NULL,
                            `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
                          ) ENGINE=INNODB DEFAULT CHARSET=latin1";
                    $res = DbAccess3::runQueryWithError($sql);
                    if ($res === false) {
                        $error = DbAccess3::$dbError;
                        $output['message'] = $error[0];
                        $output['status'] = 'fail';
                    } else {
                        if (move_uploaded_file($csv_file['tmp_name'], SETTING_DIR_ASSETS . "csv/pricing_bulk_report/" . $new_file_name)) {
                            $output['file_name'] = $new_file_name;
                            Consignment::runQuery("UPDATE $table SET status = '0' WHERE user_account = '" . DbAccess3::escape($account) . "'");
                            $load_data_sql = "LOAD DATA LOCAL INFILE '" . SETTING_DIR_ASSETS . "csv/pricing_bulk_report/" . $new_file_name . "' INTO TABLE `$table`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                `hawb`,
                                `tracking_number`,
                                `charges_reference`,
                                " . implode(",", $priceColoumInsertData) . "
                            ) 
                            SET user_account =  '" . $account . "', currency= '".$output['currency']."',  status='1' , is_complete='0', batch_number= '" . $batchNumber . "'";
                            $output['sql'] = $load_data_sql;

                            $output['batch_number'] = $batchNumber;
                            $output['table_name'] = $table;
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

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'import_file') {
            $output = array();
            $batch_number = $this->form_vars['batch_number'];
            $price_type = $this->form_vars['price_type'];
            $table_name = $this->form_vars['table_name'];
            $column_export = $this->form_vars['column_export'];
            $account = $this->user->getUserAccountId();

            $countSql = "SELECT id FROM $table_name WHERE batch_number='" . $batch_number . "' ";
            $result = DbAccess3::runQuery($countSql);
            $totalRec = mysqli_num_rows($result);
            $limit = Page::IMPORT_BATCH_MAX;
            $start = $this->form_vars['start'];

            $output['more'] = 1;
            $output['next'] = $start + $limit;
            $sql = "SELECT * FROM $table_name WHERE batch_number='" . $batch_number . "' ORDER BY id ASC LIMIT " . $start . "," . $limit;
            $output['QUERY'] = $sql;
            $resultSql = DbAccess3::runQuery($sql);
            $res = array();
            while ($obj = mysqli_fetch_object($resultSql)) {
                $res[] = $obj;
            }
            $successCount = 0;
            $failureCount = 0;
            $output['error_message'] = array();
            $output['column_export'] = $column_export;

            foreach ($res as $csv) {
                $output['id'][] = $csv->id;
//                echo "<pre>";
//                echo "<br>";
//                echo $price_type;
//                echo "<br>";
                $result = ConsignmentCharges::csvUpdateChages($csv, $price_type);
//                print_r($result);
//               die; 
                $purchaseInvoiceString = "";
                if (trim($price_type) == 'purchase_invoice' && !empty($result['pricing_purchase_status']) && count($result['pricing_purchase_status']) > 0)
                    $purchaseInvoiceString = " , data_result = '" . json_encode($result['pricing_purchase_status']) . "', data_result_count = '" . count($result['pricing_purchase_status']) . "'";
                //pricing_purchase_status
                if ($result['status']) {
                
                    $successSql = "
                        UPDATE 
                            $table_name
                        SET 
                            is_complete = 1, status = 0, message = '".$result['MESSAGE']."', user_account = '" . $result['CUATOMER_ACCOUNT'] . "' " . $purchaseInvoiceString . "
                        WHERE 
                            id = '" . $csv->id . "' and batch_number='" . $batch_number . "'";
                    Consignment::runQuery($successSql);
                    $successCount++;
                } else {
                    Consignment::runQuery("UPDATE $table_name SET is_complete = 1, status = 0, message = '" . $result['MESSAGE'] . "' , user_account = '" . $result['CUATOMER_ACCOUNT'] . "' WHERE id = '" . $csv->id . "' and batch_number='" . $batch_number . "'");
                    $failureCount++;
                    $output['error_message'][] = $result['error'] . $result['MESSAGE'];
                }
                //die;
            }
            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');
            if (($start + $limit) >= $totalRec) {
                $output['more'] = 0;
                $outputFile = "csv/pricing_bulk_report/" . $batch_number . ".csv";
                $sqlExportRecords = "SELECT * FROM $table_name WHERE batch_number='" . $batch_number . "' ORDER BY id ASC";
                $resultExportSql = DbAccess3::runQuery($sqlExportRecords);
                $resExportBody = array();
                $column_export_data = explode(',', $column_export);
                $resExportHeader = ["Hawb", "Tracking Number", "Charges Reference"];
                $resExportExtra = ["status", "batch_number"];
                $resExportSystem = [];
                foreach ($column_export_data as $columnname){
                    $resExportSystem[] = $columnname;
                    $resExportSystem[] = "system_" . $columnname;
                }
                $resExportHeader = array_merge($resExportHeader, $resExportSystem, $resExportExtra);
                $resExport = [];
                $resExport[] = implode(",", $resExportHeader);
                //column_export
                while ($objExport = mysqli_fetch_assoc($resultExportSql)) {
                    $resExportBody = [];
                    $resExportBody[] = '="' . $objExport['hawb'] . '"';
                    $resExportBody[] = '="' . $objExport['tracking_number'] . '"';
                    $resExportBody[] = $objExport['charges_reference'];
                    if (trim($objExport['data_result']) != '') {
                        $systemPriceArray = [];
                        foreach (json_decode($objExport['data_result']) as $dataresultKey => $dataresultValue) {
                            $systemPriceArray[str_replace([' ', '/'], '_', strtolower($dataresultKey))] = $dataresultValue;
                        }
                    }
                    foreach ($column_export_data as $columnname) {
                        $resExportBody[] = $objExport[$columnname];
                        $resExportBody[] = implode("|", $systemPriceArray[$columnname]);
                    }
                    $resExportBody[] = $objExport['message'];
                    $resExportBody[] = $objExport['batch_number'];
                    $resExport[] = implode(",", $resExportBody);
                }
                $csvDataFile = implode("\r\n", $resExport);
                file_put_contents(SETTING_DIR_ASSETS . $outputFile, $csvDataFile);
                $removeTableSql = "DROP TABLE IF EXISTS $table_name";
                Consignment::runQueryWithError($removeTableSql);
                $output['command'] = $output;
                $output['link'] = SETTING_URL_ASSETS . $outputFile;
            }
            echo json_encode($output);
            exit;
        }
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_template') {
            $type = $this->form_vars['price_type'];
            $heading = ConsignmentCharges::getPricingColoumn($type);
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= $h . ",";
            } 
            $fileName = "bluk_pricing_". $type ."_template.csv";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">

        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/flipclock.min.js"></script>

        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var urlPage = 'assign_price_with_csv_upload.php';
            $(document).ready(function () {
                $("#btnSubmitImport").click(function () {
                    var customer_account_id = $('#customer_account').val();
                    var currency = $('#currency').val();
//                    if(customer_account_id > 0) {
                        $('#console_window').html('');
                        $('#console_window').html("Uploading CSV File....<br />");
                        var file_data = $('#csv_for_pricing').prop('files')[0];
                        var priceType = $("#price_type").val();
                        var form_data = new FormData();
                        var batch_number = '';
                        var table_name = '';
                        form_data.append('csv_file', file_data);
                        form_data.append('price_type', priceType);
                        form_data.append('customer_account_id', customer_account_id);
                        form_data.append('currency', currency);

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
                                    batch_number = response.batch_number;
                                    table_name = response.table_name;
                                    column_export = response.column_export;
                                    importCSV(0, batch_number, priceType, table_name,column_export);
                                    
                                } else {
                                    $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                                }
                            }
                        });
//                    }
                    return false;
                });
            });
            function importCSV(startfrom, batch_number, priceType, table_name,column_export) {
                var batch_limit = '<?php echo Page::IMPORT_BATCH_MAX; ?>';
                batch_limit = parseInt(batch_limit);
                $('#console_window').append('Importing records from ' + (startfrom + 1) + ' to ' + (startfrom + batch_limit) + '...');
                var form_data = new FormData();
                form_data.append('func', 'import_file');
                form_data.append('start', startfrom);
                form_data.append('price_type', priceType);
                form_data.append('batch_number', batch_number);
                form_data.append('table_name', table_name);
                form_data.append('column_export', column_export);
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
                            importCSV(response.next, batch_number, priceType, table_name,column_export);
                        } else {
//                            $('#console_window').append(response.message + '<br /> <a href="' + urlPage + '?action=download&batch=' + batch_number + '" class="btn btn-primary btn_save margin-right-10">Click Here to Download Report</a>');
//                            $('#console_window').append('<br /> After clicking the button above you will download report for data imported via csv file. ');
                            $('#console_window').append('<br /> You have successfully updated pricing. ');
                            $('#console_window').append('<br /> <a href="'+response.link+'" class="btn btn-primary btn-xs">Click Here</a> Click here to download file ');
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
                    CSV Upload Panel
                </div>
                <div class="actions">
                    <a href="javascript:;" class="btn btn-success" id="btnDownloadTemplate" onclick="document.getElementById('assign_price_with_csv_upload').submit()">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    
                      <?php
                                    $typeArray = [];
                                    
                                    if (Permissions::checkFilePermission('assign_price_with_csv_upload_customer')) { 
                                        $typeArray['customer'] = 'Customer';
                                    }
                                    if (Permissions::checkFilePermission('assign_price_with_csv_upload_agent')) { 
                                        
                                         $typeArray['agent'] = 'Agent';
                                    } 
                                    if (Permissions::checkFilePermission('assign_price_with_csv_upload_purchase')) { 
                                        $typeArray['purchase_invoice'] = 'Purchase Invoice';
                                    } 
                                ?>
                    
                    <?php if(count($typeArray)>0){ ?>
                    <form method="post" action="assign_price_with_csv_upload.php" id="assign_price_with_csv_upload" >
                        <input type="hidden" name="func" value="download_template" >
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> Select Type </label>
                              
                                <?php echo Ddl::generateArrayDDL('price_type', $typeArray, '', '', 'class="form-filter select2 form-control" required="required"', "", 'price_type'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> Upload Price Currency</label>
                                <?php 
                                $currecnyFilterWhere = "      isactive = 1";
                                echo Ddl::generateDDL('currency', 'CurrencyFilter',$currecnyFilterWhere  , 'rightsymbol', 'rightsymbol', 'GBP', ' class="form-control select2" required data-toggle="tooltip" data-placement="top" title="Currency" data-original-title="Currency"', 'Please Select', '', 'currency', 'Currency'); ?>
                                
                            </div>
                        </div>
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
                                            <input type="file" name="csv_for_priceing" id="csv_for_pricing"> 
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                        <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImport" >Upload</a>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php }else{?>
                    <div class="">You dont have permission for this section.</div>
                    <?php }?>
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