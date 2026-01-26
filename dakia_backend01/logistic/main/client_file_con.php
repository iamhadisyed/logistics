<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
	'pdfmerger'
], 'labels');
include_classes([
    'ups.class'
    ], 'labels');
include_classes([
	'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'include_list',
    ], 'reamus');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'csvimporttemplate.class',
    'csvimporttemplatefilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'mawb.class',
    'mawbfilter.class',
    'baggingfilter.class',
    'bagging.class',
    'currency.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class'
    
    
    ]);

class Page extends BasePage {

    //how many bookings to import/validate at once?
    const IMPORT_BATCH_MAX = 10;
    // page states
    const STATUS_INITIAL = 0;

    private $user = null;
    private $csvColumn = [];
    private $csvTemplate = [];

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'client_list.php' => Translation::GetCaption("SHIPMENTS"),
            Translation::GetCaption("IMPORT")
        );
        $this->user = SessionManager::getUser();
        $this->csvColumn = array(
            'date_added'=> ['required' => true ,'desc' => 'date added <small>[Shipment create date]</small> '],
            'shipper_country_iso'=> ['required' => true ,'desc' => 'shipper country iso <small>[Shipper country iso code (2 character)]</small>'],
            'receiver_country_iso'=> ['required' => true ,'desc' => 'receiver country iso <small>[Receiver country iso code (2 character)]</small>'],
            'service_code'=> ['required' => true ,'desc' => 'service code <small>[This is code of the Service that we are using]</small>'],
            'order_reference'=> ['required' => true ,'desc' => 'order reference <small>[This is order reference number]</small>'],
            'shipper_company'=> ['required' => false ,'desc' => 'shipper company <small>[Company Name of the Shipper]</small>'],
            'shipper_contact'=> ['required' => false ,'desc' => 'shipper contact <small>[Name of the Shipper]</small>'],
            'shipper_email'=> ['required' => false ,'desc' => 'shipper email <small>[Email of the Shipper]</small>'],
            'shipper_telephone'=> ['required' => false ,'desc' => 'shipper telephone <small>[Phone Number of the shipper]</small>'],
            'shipper_address_line_1'=> ['required' => false ,'desc' => 'shipper address line 1 <small>[Address 1 of the shipper]</small>'],
            'shipper_address_line_2'=> ['required' => false ,'desc' => 'shipper address line 2 <small>[Address 2 of the shipper]</small>'],
            'shipper_address_line_3'=> ['required' => false ,'desc' => 'shipper address line 3 <small>[Address 3 of the shipper]</small>'],
            'shipper_city'=> ['required' => false ,'desc' => 'shipper city <small>[City of the shipper]</small>'],
            'shipper_state'=> ['required' => false ,'desc' => 'shipper state <small>[State of the shipper]</small>'],
            'shipper_postcode'=> ['required' => false ,'desc' => 'shipper postcode <small>[Postcode of the city of sender]</small>'],
            'receiver_company'=> ['required' => false ,'desc' => 'receiver company <small>[Company Name of the receiver]</small>'],
            'receiver_contact'=> ['required' => true ,'desc' => 'receiver contact <small>[Name of the receiver]</small>'],
            'receiver_email'=> ['required' => false ,'desc' => 'receiver email <small>[Email of the receiver]</small>'],
            'receiver_telephone'=> ['required' => false ,'desc' => 'receiver telephone <small>[Phone Number of the receiver]</small>'],
            'receiver_address_line_1'=> ['required' => true ,'desc' => 'receiver address_line 1 <small>[Address 1 of the receiver]</small>'],
            'receiver_address_line_2'=> ['required' => false ,'desc' => 'receiver address_line 2 <small>[Address 2 of the receiver]</small>'],
            'receiver_address_line_3'=> ['required' => false ,'desc' => 'receiver address_line 3 <small>[Address 3 of the receiver]</small>'],
            'receiver_city'=> ['required' => true ,'desc' => 'receiver city <small>[City of the receiver]</small>'],
            'receiver_state'=> ['required' => false ,'desc' => 'receiver state <small>[State of the receiver]</small>'],
            'receiver_postcode'=> ['required' => true ,'desc' => 'receiver postcode <small>[Postcode of the city of receiver]</small>'],
            'reference'=> ['required' => false ,'desc' => 'reference <small>[Enter reference]</small>'],
            'items_value'=> ['required' => false ,'desc' => 'items value <small>[Value of shipped goods]</small>'],
            'items_currency'=> ['required' => false ,'desc' => 'items currency <small>[Currency iso code]</small>'],
            'item_type'=> ['required' => false ,'desc' => 'item type <small>[Type of Consignment]</small>'],
            'note'=> ['required' => false ,'desc' => 'note <small>[Any notes that sender adds for the Consignment]</small>'],
            'description'=> ['required' => true ,'desc' => 'description <small>[Any description that sender adds for the Consignment]</small>'],
            'weight'=> ['required' => true ,'desc' => 'weight <small>[Enter Parcel(Weight|Weight) ]</small>'],
            'itemvalue'=> ['required' => false ,'desc' => 'itemvalue <small>[Enter Parcel(Item value|Item value) ]</small>'],
            'length'=> ['required' => true ,'desc' => 'length <small>[Enter Parcel(Length|Length) ]</small>'],
            'height'=> ['required' => true ,'desc' => 'height <small>[Enter Parcel(Height|Height) ]</small>'],
            'width'=> ['required' => true ,'desc' => 'width <small>[Enter Parcel(Width|Width) ]</small>'],
            'parcel_item_desc'=> ['required' => false ,'desc' => 'item desc <small>[Enter Parcel(p1d1|p1d2#p2d1|p2d2) ]</small>'],
            'parcel_item_sku'=> ['required' => false ,'desc' => 'item sku <small>[Enter Parcel(p1sku1|p1sku2#p2sku1|p2sku2) ]</small>'],
            'parcel_item_url'=> ['required' => false ,'desc' => 'item url <small>[Enter Parcel(p1url1|p1url2#p2url1|p2url2) ]</small>'],
            'parcel_item_quantity'=> ['required' => false ,'desc' => 'item quantity <small>[Enter Parcel(p1quantity1|p1quantity2#p2quantity1|p2quantity2) ]</small>'],
            'parcel_item_value'=> ['required' => false ,'desc' => 'item value <small>[Enter Parcel(p1value1|p1value2#p2value1|p2value2) ]</small>'],
            'parcel_item_weight'=> ['required' => false ,'desc' => 'item weight <small>[Enter Parcel(p1weight1|p1weight2#p2weight1|p2weight2) ]</small>'],
            'parcel_item_hs_code'=> ['required' => false ,'desc' => 'item hs code <small>[Enter Parcel(p1hs_code1|p1hs_code2#p2hs_code1|p2hs_code2) ]</small>'],
            'parcel_item_manufacture_country'=> ['required' => false ,'desc' => 'item manufacture country ISO <small>[Enter Parcel (p1man_ctry_iso1|p1man_ctry_iso2#p2man_ctry_iso1|p2man_ctry_iso2) ]</small>'],
            'bag_number'=> ['required' => false ,'desc' => 'bag number <small>[Set bag number]</small>'],
            'tracking_number'=> ['required' => false ,'desc' => 'tracking number <small>[Enter tracking_number|tracking_number]</small>'],
            'mawb_number'=> ['required' => false ,'desc' => 'mawb number <small>[Enter MAWB number]</small>'],
            'flight_number'=> ['required' => false ,'desc' => 'flight number <small>[Enter Flight number</small>]'],
            'eori_number'=> ['required' => false ,'desc' => 'EORI number required for shipment/good import or export for EU.</small>]'],
            'vat_number'=> ['required' => false ,'desc' => 'VAT number required for shipment/good import or export for EU.</small>]'],
            'ioss_number'=> ['required' => false ,'desc' => 'IOSS number required for shipment/good import or export for EU.</small>]']
        );
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'delete_temp') {
            $tempId = $this->form_vars['temp_id'];
            if($tempId > 0){
                $csvTemp = new CsvImportTemplate();
                $csvTemp->deleteById($tempId);
                echo "Template deleted successfully";
            }
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'view_user_template') {
            $tempId = $this->form_vars['temp_id'];
            $download = $this->form_vars['download'];
            if($tempId > 0){
                $csvTemp = new CsvImportTemplate($tempId);
                $csvColumn = (array)json_decode($csvTemp->getTemplate());
                $templateName = $csvTemp->getTemplateName();
            }else{
                $csvColumn = array_keys($this->csvColumn);
                $templateName = "Default";
            }
            $html = "";
            if(count($csvColumn) > 0){
                $count = 1;
                foreach ($csvColumn as $csvColumnName) {
                    if($download == "download"){
                        $html .= ucwords(str_replace('_', ' ', $csvColumnName)).",";
                    }else{
                        $html .= '<tr>';
                        $html .= '<td class="text-center">'.$count;
                        $html .= '</td>';
                        $html .= '<td>';
                        $html .= $this->csvColumn[$csvColumnName]['desc'];//ucwords(str_replace('_', ' ', $csvColumnName));
                        $html .= '</td>';
                        $html .= '</tr>';
                    }
                   $count++;
               }
            }
            if($download == "download"){
                $html = rtrim($html,',');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="'.$templateName.'.csv"');
            }
            echo $html;
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'save_csv_template') {
            $serilizedData = $this->form_vars['serilized_data'];
            $userId = $this->user->getId();
            $userAccountId = $this->user->getUserAccountId();
            $csvImportTemplate = new CsvImportTemplate();
            $csvImportTemplate->setUserId($userId);
            $csvImportTemplate->setUserAccountId($userAccountId);
            $csvImportTemplate->setTemplateName($this->form_vars['template_name']);
            $csvImportTemplate->setTemplate(json_encode($this->form_vars['csv']));
            $csvImportTemplate->setAddedBy($userId);
            $csvImportTemplate->setAddedDate(time());
            $csvImportTemplate->save();
            if($csvImportTemplate->getId() > 0){
                echo 'Template save successfully!';
            }else{
                echo 'Template can not save!';
            }
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $userId = $this->form_vars['user_id'];
            $userTemplateId = $this->form_vars['user_template_id'];
            $shipperMissingCol = [];
            $shipperTemTblColStr = "";
            $shipperUserTblColStr = "";
            $userTblCol = [
                'shipper_country_iso' => 'getCountryId',
                'shipper_contact' => 'getFirstName',
                'shipper_address_line_1' => 'getAddress',
                'shipper_city' => 'getCity',
                'shipper_postcode' => 'getPostcode'
            ];
            $shipperReqColumn = array_keys($userTblCol);
            if($userTemplateId > 0){
                $csvImportTemplate = new CsvImportTemplate($userTemplateId);
                $csvImportTemplateJson = (array)json_decode($csvImportTemplate->getTemplate());
                $shipperMissingCol = array_diff($shipperReqColumn, $csvImportTemplateJson);
            }else{
                $csvImportTemplateJson = array_keys($this->csvColumn);
                $shipperMissingCol = array_diff($shipperReqColumn, $csvImportTemplateJson);
            }
            $csvColumn = implode(',', $csvImportTemplateJson);
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
                    $account = $this->user->getUserName();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $output['batch_number'] = $batchNumber = $account . "_" . time();
                    if (move_uploaded_file($csv_file['tmp_name'], "../_assets/csv/" . $new_file_name)) {
                        $output['file_name'] = $new_file_name;
                        Consignment::runQuery("DELETE FROM import_csv_consignment_temp WHERE user_id = '" . DbAccess3::escape($userId) . "'");
                        $load_data_sql = "LOAD DATA LOCAL INFILE '" . "../_assets/csv/" . $new_file_name . "' INTO TABLE `import_csv_consignment_temp` CHARACTER SET latin1 FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES ( ".$csvColumn."  ) SET status='1' , is_complete='0', batch_number = '" . $batchNumber . "' , user_id = '".DbAccess3::escape($userId)."'";
                        $output['sql'] = '';//$load_data_sql;
                       
						$res = DbAccess3::runQueryWithError($load_data_sql);
                        $countSql = "SELECT COUNT(id) as id FROM import_csv_consignment_temp WHERE batch_number = '" . $batchNumber . "'";
                        $resultSet = DbAccess3::runQuery($countSql);
                        $TotalRecordImported = mysqli_fetch_array($resultSet);
                        // Get user column
                        if(count($shipperMissingCol) > 0){
                            $userFilter = new UserFilter();
                            $userFilter->addFieldFilter('u.id', $userId);
                            $userFilter->addCountryJoin();
                            $userObj = $userFilter->getColumnList('c.iso AS country_id,CONCAT(u.first_name," ",u.last_name) AS first_name,u.address,u.city,u.postcode');
                            $userObj = $userObj[0];
                            $updateStr = "";
                            foreach($shipperMissingCol as $missingCol){
                                $tmp = $userTblCol[$missingCol];                                
                                $updateStr .= ($updateStr != "" ? "," : "").$missingCol."= '".$userObj->$tmp()."'";
                            }
                            $tempConSql = "UPDATE import_csv_consignment_temp SET $updateStr WHERE batch_number = '" . $batchNumber . "'";
                            DbAccess3::runQuery($tempConSql);
                        }
                        
                        $output['sql'] = '';//$countSql;
                        $output['total_recode_imported'] = $TotalRecordImported;

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
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'import_file') {
//            if (isset($_GET['uaccount'])) {
//                $user_id = base64_decode($_GET['uaccount']);
//                $user_name = new User($user_id);
//                $account = $user_name->getUserName();
//                $UserObject = $user_name;
//            } else
//            {
//                $account = $this->user->getUserName();
//                $UserObject = $this->user;
//            }
            $batchNumber = $this->form_vars['batch_number'];
            
            $countSql = "SELECT id FROM import_csv_consignment_temp WHERE batch_number = '" . $batchNumber . "' ";
            $resultSet = DbAccess3::runQuery($countSql);
            $totalRec = mysqli_num_rows($resultSet);
            $limit = Page::IMPORT_BATCH_MAX;
            $start = $_POST['start'];
            $output = array();

            $output['more'] = 1;
            $output['next'] = $start + $limit;
            $sql = "SELECT * FROM import_csv_consignment_temp WHERE batch_number = '" . $batchNumber . "'  and is_complete = '0' ORDER BY id ASC LIMIT " . $start.",".$limit;
            $resultData = DbAccess3::runQuery($sql);
            $output['sql'] = '';////$sql;
            $successCount = 0;
            $failureCount = 0;
            $output['error_message'] = array();
            if (mysqli_num_rows($resultData) > 0) {
                while ($row = mysqli_fetch_array($resultData)) {
                    
//                    $result = $this->importBooking($row, $UserObject);
                    $result = $this->importBooking($row);
                    $output['time1'][] = $result['time1'];
                    $output['time2'][] = $result['time2'];
                    $output['time3'][] = $result['time3'];
                    $output['time4'][] = $result['time4'];

                    $output['id'][] = $row['id'];
                    if ($result['status']) {
                        Consignment::runQuery("UPDATE import_csv_consignment_temp SET is_complete = 1, status = 1, message = 'success' WHERE id = '" . $row['id'] . "'");
                        $successCount++;
                    } else {
                        Consignment::runQuery("UPDATE import_csv_consignment_temp SET is_complete = 1, status = 0, message = '" . $result['error'] . "' WHERE id = '" . $row['id'] . "'");
                        $failureCount++;
                        $output['error_message'][] = "<br>". $result['error'];
                    }
                }
            } else {
                $return['error'] = "<br />" . formatMessages(ERROR_FILE_REJECTED);
                $output['error_message'][] = $return['error'];
            }
            $output['sql'] = '';//$sql;
            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');
            if (($start + $limit) >= $totalRec) {
                $output['more'] = 0;
            }

            echo json_encode($output);
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_account_user') {
            $output = [];
            $userAccountId = $this->form_vars['user_account_id'];
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("       user_account_id", $userAccountId);
            $userObjs = $userFilter->getList();
            $options = "<option>Select User</option>";
            if(count($userObjs) > 0) {
                $selected = "";
                foreach($userObjs as $userObj) {
                    $selected = "";
                    if($userObj->getId() == $this->user->getId()) {
                        $selected = "selected='selected'";
                    }
                    $options .= "<option value='" . $userObj->getId() . "' " . $selected . " > " . $userObj->getUserName() . " </option>";
                }
            }
            $output['status'] = "success";
            $output['options'] = $options;
            echo json_encode($output);
            exit();
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_user_template') {
            $output = [];
            $userAccountId = $this->user->getUserAccountId();
            $csvImportTemplateFilter = new CsvImportTemplateFilter();
            $csvImportTemplateFilter->addFieldFilter("    cit.user_account_id", $userAccountId);
            $this->csvTemplate = $csvImportTemplateFilter->getColumnList('*');
            $options = '<option value="0">Default Template</option>';
            if(count($this->csvTemplate) > 0) {
                foreach($this->csvTemplate as $csvTemplate) {
                    $selected = "";
                    $options .= "<option value='" . $csvTemplate->getId() . "' " . $selected . " > " . $csvTemplate->getTemplateName(). " </option>";
                }
            }
            $output['status'] = "success";
            $output['options'] = $options;
            echo json_encode($output);
            exit();
        }

        // common initialisation for ths page
        $this->setTitle("Import Bookings");
    }

    protected function renderHead() {

    }

    protected function addPagelavelCss() {
        ?>

        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .help-block-error{
                display: none !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>

        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-nestable/jquery.nestable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var UINestable = function () {
            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
//                    output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };


            return {
                //main function to initiate the module
                init: function () {

                    // activate Nestable for list 1
                    $('#nestable_list_1').nestable({ group: 1,maxDepth:1 }).on('change', updateOutput);

                    // output initial serialised data
                    updateOutput($('#nestable_list_1').data('output', $('#nestable_list_1_output')));

                    $('#nestable_list_menu').on('click', function (e) {
                        var target = $(e.target),
                            action = target.data('action');
                        if (action === 'expand-all') {
                            $('.dd').nestable('expandAll');
                        }
                        if (action === 'collapse-all') {
                            $('.dd').nestable('collapseAll');
                        }
                    });
                }
            };

        }();
        $("#csv_temp_modal").click(function(){
            UINestable.init();
            $("#template_name").val("");
            $("#csv_template").modal("show");
        });
        $(document).on("click", "#save_csv_temp", function(event) {
            // Put your code here.
            var frm_serialize = $("#frm_csv_temp").serialize();
//            var serilized_data = $('#nestable_list_1').nestable('serialize');
            var template_name = $('#template_name').val();
            if(template_name == ""){
                swal("","Please enter template name");
            }else{
                $.ajax({
                    type: "POST",
                    url: "client_file_con.php", // your php file name
                    data: frm_serialize,
                    success: function (data) {
                        $("#csv_template").modal('hide');
                        swal("",data,"info");
                        getUserTempalte();
                    },
                    error: function (errorString) {
                    }
                });
            }
        });
        </script>
        <?php
    }

    protected function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#btnSubmitImport").click(function () {
                    var file_data = $('#file_in').prop('files')[0];
                    var user_account_id = $('#user_account_id').val();
                    var user_id = $('#user_id').val();
                    var user_template_id = $('#user_template_select').val();
                    if(user_template_id == "" || user_template_id == null){
                        swal("","Please select csv template first!");
                    }else{
                        if(user_id > 0) {
                            $("#btnSubmitImport").hide();
                            $('#console_window').show();
                            $('#console_window').html('');
                            $('#console_window').html("Uploading CSV File....<br />");
                            var form_data = new FormData();
                            form_data.append('csv_file', file_data);
                            form_data.append('func', 'upload_csv_file');
                            form_data.append('user_account_id', user_account_id);
                            form_data.append('user_id', user_id);
                            form_data.append('user_template_id', user_template_id);
                            $.ajax({
                                url: 'client_file_con.php',
                                dataType: 'json',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                success: function (response) {
                                    if (response.status == 'success') {
                                        var batch_number = response.batch_number;
                                        var total_record = response.total_recode_imported;
                                        $('#console_window').append('Start Importing data ' + batch_number + '<br />');
                                        importCSV(0, total_record, batch_number);
                                    } else {
                                        $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                                    }
                                }
                            });
                        } else {
                            $("#btnSubmitImport").show();
                            swal("Sorry!", "Please select the user first", "error");
                        }
                    }
                    return false;
                });
                /* Get user by account id */
                $("#user_account_id").change(function () {
                    var userAccountId = $(this).val();
                    if(userAccountId > 0) {
                        var form_data = new FormData();
                        form_data.append('func', 'get_account_user');
                        form_data.append('user_account_id', userAccountId);
                        $.ajax({
                            url: 'client_file_con.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                if(response.status == "success") {
                                    $('#user_id').html(response.options);
                                    $('#user_id').select2();
                                }
                            }
                        });
                    }
                });
                $("#user_account_id").trigger('change');
                getUserTempalte();
                $("#btn_view_csv").click(function () {
                    var user_temp = $("#user_template_select").val();
                    var user_temp_name = $("#user_template_select option:selected").text();
                    $.ajax({
                        url: 'client_file_con.php',
                        data: {func:'view_user_template',temp_id:user_temp},
                        type: 'post',
                        success: function (response) {
                            $("#temp_id_download").val(user_temp);
                            $("#template_name_view").html("");
                            $("#table_csv_data").html("");
                            $("#template_name_view").html(user_temp_name);
                            $("#table_csv_data").append(response);
                            $("#csv_template_view").modal('show');
                        }
                    });
                });
                $("#btn_delete_csv").click(function () {
                    swal({
                            title: "Are you sure you want to delete?",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                    },
                    function(isConfirm) {
                    if (isConfirm) {
                        var user_temp = $("#user_template_select").val();
                        $.ajax({
                           url: 'client_file_con.php',
                           data: {func:'delete_temp',temp_id:user_temp},
                           type: 'post',
                           success: function (response) {
                               swal("",response,'info');
                           }
                       });
                    }
                    });
                });
                $('#file_in').click(function(){
                   $("#btnSubmitImport").show(); 
                });
            });
            /* Get user CSV Template by account id */
            function getUserTempalte(){
                $.ajax({
                    type: "POST",
                    url: "client_file_con.php", // your php file name
                    dataType: 'json',
                    data: {func:'get_user_template'},
                    success: function (data) {
                        if(data.status == "success") {
                            $('#user_template_select').html(data.options);
                            $('#user_template_select').select2();
                        }
                    },
                    error: function (errorString) {
                    }
                });
            }
            function importCSV(startfrom, total_record = 0, batchNumber = '') {
                var batch_limit = '<?php echo Page::IMPORT_BATCH_MAX; ?>'
                batch_limit = parseInt(batch_limit);

                var currentBatchLimit = startfrom + batch_limit;
                if (total_record < currentBatchLimit)
                    currentBatchLimit = total_record;
                $('#console_window').append('Importing batch number ' + batchNumber + ' records from ' + (startfrom + 1) + ' to ' + (currentBatchLimit) + '...');
                var form_data = new FormData();
                form_data.append('func', 'import_file');
                form_data.append('start', startfrom);
                form_data.append('batch_number', batchNumber);
                var uaccount = '';
                uaccount = "<?php echo util_get("uaccount"); ?>";
                var filename = "client_file_con.php";
                if (uaccount != '')
                {
                    filename = 'client_file_con.php?uaccount=' + uaccount;
                }
                $.ajax({
                    url: filename,
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
                            importCSV(response.next, total_record, batchNumber);
                            $("#btnSubmitImport").show();
                        } else {
                            if(response.error_message != undefined && response.error_message != '') {
                                $('#console_window').append(response.message + '<br /> <a href="client_list.php?show=valid" class="btn btn-primary btn_save margin-right-10">Click Here to View Shipment</a>');
                            } else {
                                $('#console_window').append(response.message + '<br />All Records imported successfully. <br /> <a href="client_list.php?show=valid" class="btn btn-primary btn_save margin-right-10">Click Here to View Shipment</a>');
                            }
                            $('#console_window').append('<br /> After clicking the buttom above you will see a list of all shipments you have imported via .csv file.<br /> The list will show which shipments have been successfully imported ("Ready To Print") or which can´t be processed because of wrong or invalid data in the file ("Invalid").');
                            $("#btnSubmitImport").show();
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
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-upload"></i>
                      <?php echo Translation::GetCaption("IMPORT_SHIPMENT"); ?>
                    </div>
                    <div class="actions"> 
                        <a href="client_list.php<?php echo (isset($_GET['uaccount']) ? "?uaccount=" . $_GET['uaccount'] : '') ?>" class="btn blue"><i class="fa fa-list"></i> List </a>
                        <a href="consignment_add.php<?php echo (isset($_GET['uaccount']) ? "?uaccount=" . $_GET['uaccount'] : '') ?>" class="btn blue"><i class="fa fa-plus"></i> <?= Translation::GetCaption("ADD_NEW_SHIPMENT") ?></a>
                        <a href="show_address.php" class="btn blue"><i class="fa fa-building"></i> Manage Address </a>
                        <a href="#" id="csv_temp_modal" class="btn blue"><i class="icon-wrench"></i>&nbsp;Create Template</a>

                        <a href="javascript:;" class="collapse btn btn-circle btn-icon-only btn-default hidden" data-original-title="" title=""> </a>
                        <a href="" class="btn btn-circle btn-icon-only btn-default fullscreen hidden" data-original-title="" title=""> </a> 
                        <a href="#portlet-config" data-toggle="modal" class="btn btn-circle btn-icon-only btn-default hidden"><i class="icon-wrench"></i></a>
                    </div>
                </div>

                <div class="portlet-body" >
                    <div class="row">
                        <div class="col-md-6">
                            <label class="label-account">Select Account</label>
                            <div class="form-group">
                                <div id="user_account_content">
                                    <?php
                                    $accountParentId = 0;
                                    $includeParent = true;
                                    if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                        $accountParentId = $this->user->getUserAccountId();
                                    }
                                    $selectedAccount = $this->user->getUserAccountId();
                                    $allowedLevel = 0;
                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                        $allowedLevel = 1;
                                    }
                                    ?>
                                    <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true" onclick="get_account_users()" ', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="label-account">Select User</label>
                            <div class="form-group">
                                <div id="user_content">
                                    <select class="form-filter select2 form-control" name="user_id" id="user_id">
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Select CSV Template</label>
                            <div class="form-group">
                                <div id="user_content">
                                    <select class="form-filter select2 form-control" name="user_template_select" id="user_template_select">
                                        <option value="0">Default Template</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 margin-top-20">
                            <button class="btn blue" id="btn_view_csv" name="btn_view_csv">View</button>
                            <button class="btn red" id="btn_delete_csv" name="btn_delete_csv">Delete</button>
                        </div>
                        <div class="col-md-6">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> <?php echo Translation::GetCaption("SELECT_FILE_TO_IMPORT"); ?></label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                                                <span class="fileinput-new"> Select file </span>
                                                                <span class="fileinput-exists"> Change </span>
                                                                <input type="file" name="file_in" id="file_in"> </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                        <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImport" data-original-title="" title=""><?php echo Translation::GetCaption("IMPORT"); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both;">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="console_window" style="clear:both; border-style: solid; border-width:1px;  width: 100%; padding: 15px; display: none;">

                            </div>
                        </div>
                    </div>       
                    <div class="row">
                        <?php include_once "csv_disclaimers.php" ?>   	
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- /.modal -->
        <div class="modal fade" id="csv_template" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">CSV Template</h4>
                    </div>
                    <div class="modal-body">
                        <form name="frm_csv_temp" id="frm_csv_temp" action="" method="POST">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Template Name</label>
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                            <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Template Name is mandatory">*</i>
                                                <input id="template_name" name="template_name" type="text" value="" required="required" placeholder="Template Name" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" autocomplete="off" title="Template Name" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="portlet light" style="padding:0px;margin:0px;">
                                        <div class="portlet-body">
                                            <div class="dd" id="nestable_list_1">
                                                <ol class="dd-list">
                                                    <?php foreach ($this->csvColumn as $key => $csvColumn) {  ?>
                                                    <li style="display: flex;" class="dd-item " data-id="<?php echo $key; ?>">
                                                        <input style="margin-top:16px;" id="csv_chk_<?php echo $key; ?>" name="csv[<?php echo $key; ?>]" type="checkbox" <?php if($csvColumn['required']){ echo 'checked="checked" onclick="return false;" readonly'; } ?> class="form-filter" value="<?php echo $key; ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <div style="width:100%;" class="dd-handle">
                                                                <?php echo $csvColumn['desc']; ?>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ol>
                                            </div>
                                            <input type="hidden" name="func" value="save_csv_template" />
                                    </div>
                                </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="save_csv_temp" class="btn green">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- /.modal -->
        <div class="modal fade" id="csv_template_view" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">CSV Template [<span id="template_name_view"></span>]</h4>
                    </div>
                    <div class="modal-body">
                        <form name="frm_csv_temp" id="frm_csv_temp" action="" method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="50">Sr.</th>
                                                <th>Column Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_csv_data">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <form name="download_temp" id="" action="" target="_blank" method="POST">
                            <input type="hidden" name="func" value="view_user_template" />
                            <input type="hidden" id="temp_id_download" name="temp_id" value="" />
                            <input type="hidden" name="download" value="download" />
                            <input type="submit" name="btn_download" value="Download Template" class="btn btn-success" />
                            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <?php
    }

    /**
     * Import one consignment
     * - set status to INVALID (ignore user feedback) or VALID
     *
     * @param $row (with file row already extracted)
     */
    private function importBooking($row) {
        $row = array_map('trim',$row);
        if(!empty($row['receiver_country_iso']) && !empty(trim($row['service_code'])) && !empty($row['receiver_contact']) && !empty($row['receiver_address_line_1']) && !empty($row['receiver_city']) && !empty($row['receiver_postcode']) && !empty($row['description']) && !empty($row['weight']) && !empty($row['length'])  && !empty($row['height'])   && !empty($row['width']) ) {
//            $userAccount = new CustomerAccount($this->user->getId());
            $return['time1'] = $this->time_elapsed($now);
            $now = microtime(true);

            $consignmentArray   =   array();
            $consignmentArray['order_reference'] = ((trim($row['order_reference']) == '') ? (strtoupper("ST". substr($this->user->getUserAccountId(), 0,3).generateRandomString(3).time())) : $row['order_reference']);
            if(isset($row['tracking_number']) && trim($row['tracking_number'])!= '')
                $consignmentArray['awb'] = ((trim($row['tracking_number']) == '') ? str_replace( '.', '',microtime(true)) : $row['tracking_number']);
            $serflr = new ServiceFilter();
            $serflr->addSCodeFilter(trim($row['service_code']));
            $result = $serflr->getList();
            if (sizeof($result) > 0) {
                foreach ($result as $res) {
                    $service_id = $res->getId();
                    $consignmentArray['service'] = $service_id;
                    if($res->getIsCustomized())
                        $consignmentArray['is_product'] = 1;
                    else
                        $consignmentArray['is_product'] = 0;
                }
            }else {
                $return['error'] = "<br />Order Reference (" . $this->replaceSpecial($row['order_reference']) . ") rejected. Invalid service code [".$row['service_code']."], please enter valid Service code";
                $return['status'] = false;
                return $return;
            }

            $countryflr = new CountryFilter();
            $countryflr->addCountryCodeFilter($this->replaceSpecial(substr((strtoupper($row['shipper_country_iso'])), 0, 20)));
            $con = $countryflr->getList();
            if (count($con) > 0) {
                foreach ($con as $con_iso) {
                    $consignmentArray['sender_country'] = $con_iso->getId();
                }
            } else {
                $return['error'] = "<br />" . $this->replaceSpecial($row['order_reference']) . " rejected. Please enter shipper country ISO code in column name shipper country iso";
                $return['status'] = false;
                return $return;
            }

            $countryflr = new CountryFilter();
            $countryflr->addCountryCodeFilter($this->replaceSpecial(substr((strtoupper($row['receiver_country_iso'])), 0, 20)));
            $con = $countryflr->getList();
            if (count($con) > 0) {
                foreach ($con as $con_iso) {
                    $consignmentArray['receiver_country'] = $con_iso->getId();
                }
            } else {
                $return['error'] = "<br />" . $this->replaceSpecial($row['order_reference']) . " rejected. Please enter receiver country ISO code in column name receiver country iso";
                $return['status'] = false;
                return $return;
            }           
            // Set shipper data
            $consignmentArray['sender_company'] = substr(($row['shipper_company']), 0, 100);
            $consignmentArray['sender_contact'] = substr(($row['shipper_contact']), 0, 100);
            $consignmentArray['sender_email']  = $this->replaceSpecial($row['shipper_email']);
            $consignmentArray['sender_telephone']  = (substr(str_replace(' ', '', $row['shipper_telephone']), 0, 20));
            $userObj = null;
            if(empty($row['shipper_address_line_1'])){
                if(empty($userObj))
                    $userObj = new User($row['user_id']);
                $row['shipper_address_line_1'] = $userObj->getAddress();
            }
            if(empty($row['shipper_city'])){
                if(empty($userObj))
                    $userObj = new User($row['user_id']);
                $row['shipper_city'] = $userObj->getCity();
            }
            if(empty($row['shipper_postcode'])){
                if(empty($userObj))
                    $userObj = new User($row['user_id']);
                $row['shipper_postcode'] = $userObj->getPostcode();
            }
            if(empty($row['shipper_contact'])){
                if(empty($userObj))
                    $userObj = new User($row['user_id']);
                $consignmentArray['sender_contact'] = $userObj->getFirstName()." ".$userObj->getLastName();
            }
            $consignmentArray['sender_address_line_1'] = $this->replaceSpecial(substr($row['shipper_address_line_1'], 0, 50));
            $consignmentArray['sender_address_line_2'] = $this->replaceSpecial(substr(($row['shipper_address_line_2']), 0, 50));
            $consignmentArray['sender_address_line_3'] = $this->replaceSpecial(substr(($row['shipper_address_line_3']), 0, 50));
            $consignmentArray['sender_city'] = $this->replaceSpecial(substr((strtoupper($row['shipper_city'])), 0, 50));
            $consignmentArray['sender_state'] = $this->replaceSpecial(substr((strtoupper($row['shipper_state'])), 0, 50));
            $consignmentArray['sender_postcode'] = (substr($row['shipper_postcode'], 0, 11));

            // Set receiver data
            $consignmentArray['receiver_company'] = substr(($row['receiver_company']), 0, 100);
            $consignmentArray['receiver_contact'] = substr(($row['receiver_contact']), 0, 100);
            $consignmentArray['receiver_email']  = $this->replaceSpecial($row['receiver_email']);
            $consignmentArray['receiver_telephone']  = (substr(str_replace(' ', '', $row['receiver_telephone']), 0, 20));
            $consignmentArray['receiver_address_line_1'] = $this->replaceSpecial(substr($row['receiver_address_line_1'], 0, 50));
            $consignmentArray['receiver_address_line_2'] = $this->replaceSpecial(substr(($row['receiver_address_line_2']), 0, 50));
            $consignmentArray['receiver_address_line_3'] = $this->replaceSpecial(substr(($row['receiver_address_line_3']), 0, 50));
            $consignmentArray['receiver_city'] = $this->replaceSpecial(substr((strtoupper($row['receiver_city'])), 0, 50));
            $consignmentArray['receiver_state'] = $this->replaceSpecial(substr((strtoupper($row['receiver_state'])), 0, 50));
            $consignmentArray['receiver_postcode'] = (substr($row['receiver_postcode'], 0, 11));
            // Set remaning fields
            $consignmentArray['reference'] = (substr((strtolower($row['reference'])), 0, 20));
            $consignmentArray['item_value'] = $this->replaceSpecial($row['items_value']);
            $consignmentArray['item_currency'] = $this->replaceSpecial($row['items_currency']);
            $consignmentArray['item_type'] = $this->replaceSpecial($row['item_type']);
            $consignmentArray['notes'] = (substr($this->replaceSpecial($row['note']), 0, 100));
            $consignmentArray['description'] = (substr($this->replaceSpecial($row['description']), 0, 100));
			
			$consignmentArray['eori_number'] = $this->replaceSpecial($row['eori_number']);
			$consignmentArray['vat_number'] = $this->replaceSpecial($row['vat_number']);
			$consignmentArray['ioss_number'] = $this->replaceSpecial($row['ioss_number']);
			

			/// IRSHAD CODE

			// Make parcel weight array
			$parcelWeightArr = explode("|", $row['weight']);
			// Make parcel length array
			$parcelLengthArr = explode("|", $row['length']);
			// Make parcel height array
			$parcelHeightArr = explode("|", $row['height']);
			// Make parcel width array
			$parcelWidthArr = explode("|", $row['width']);
			// Make parcel width array
			$parcelItemValueArr = explode("|", $row['itemvalue']);


			// Set parcel items values
			$parcelItemdescArr = explode('#',$row['parcel_item_desc']);
			$parcelItemUrlArr = explode('#',$row['parcel_item_url']);
			$parcelItemWeightArr = explode('#',$row['parcel_item_weight']);
			$parcelItemSkuArr = explode('#',$row['parcel_item_sku']);
			$parcelItemQuantityArr = explode('#',$row['parcel_item_quantity']);
			$parcelItemsValueArr = explode('#',$row['parcel_item_value']);
			$parcelItemHsArr = explode('#',$row['parcel_item_hs_code']);
			$parcelItemMfCountryArr = explode('#',$row['parcel_item_manufacture_country']);



			if(count($parcelWeightArr) > 0){
				$weightCount = 0;
				foreach ($parcelWeightArr as $pi => $parcelWeight) {
					$consignmentArray['parcel'][$weightCount]['weight'] = trim($parcelWeight);
					//parcel length
					if(isset($parcelLengthArr[$pi])){
						$consignmentArray['parcel'][$weightCount]['length'] = trim($parcelLengthArr[$pi]);
					}else{
						$consignmentArray['parcel'][$weightCount]['length'] = 0;
					}
					//parcel height
					if(isset($parcelHeightArr[$pi])){
						$consignmentArray['parcel'][$weightCount]['height'] = trim($parcelHeightArr[$pi]);
					}else{
						$consignmentArray['parcel'][$weightCount]['height'] = 0;
					}
					//parcel width
					if(isset($parcelWidthArr[$pi])){
						$consignmentArray['parcel'][$weightCount]['width'] = trim($parcelWidthArr[$pi]);
					}else{
						$consignmentArray['parcel'][$weightCount]['width'] = 0;
					}
					//parcel itemvalue
					if(isset($parcelItemValueArr[$pi])){
						$consignmentArray['parcel'][$weightCount]['itemvalue'] = trim($parcelItemValueArr[$pi]);
					}else{
						$consignmentArray['parcel'][$weightCount]['itemvalue'] = 0;
					}

					$parcelItem = [];
					//parcel item description
					if(count($parcelItemdescArr) > 0){
						if(isset($parcelItemdescArr[$weightCount])){
							$_parcelItemdescArr = explode("|",$parcelItemdescArr[$weightCount]);
							if(count($_parcelItemdescArr) > 0){
								foreach($_parcelItemdescArr as $pii => $parcelItemdes){
									$parcelItem[$pii]["item_description"] = $parcelItemdes;
								}
							}
						}
					}
					//parcel item url
					if(count($parcelItemUrlArr) > 0){
						if(isset($parcelItemUrlArr[$weightCount])){
							$_parcelItemUrlArr = explode("|",$parcelItemUrlArr[$weightCount]);
							if(count($_parcelItemUrlArr) > 0){
								foreach($_parcelItemUrlArr as $pii => $parcelItemUrl){
									$parcelItem[$pii]["item_url"] = $parcelItemUrl;
								}
							}
						}
					}
					//parcel item weight
					if(count($parcelItemWeightArr) > 0){
						if(isset($parcelItemWeightArr[$weightCount])){
							$_parcelItemWeightArr = explode("|",$parcelItemWeightArr[$weightCount]);
							if(count($_parcelItemWeightArr) > 0){
								foreach($_parcelItemWeightArr as $pii => $parcelItemWeight){
									$parcelItem[$pii]["weight"] = $parcelItemWeight;
								}
							}
						}
					}
					//parcel item sku
					if(count($parcelItemSkuArr) > 0){
						if(isset($parcelItemSkuArr[$weightCount])){
							$_parcelItemSkuArr = explode("|",$parcelItemSkuArr[$weightCount]);
							if(count($_parcelItemSkuArr) > 0){
								foreach($_parcelItemSkuArr as $pii => $parcelItemSku){
									$parcelItem[$pii]["item_sku"] = $parcelItemSku;
								}
							}
						}
					}
					//parcel item quantity
					if(count($parcelItemQuantityArr) > 0){
						if(isset($parcelItemQuantityArr[$weightCount])){
							$_parcelItemQuantityArr = explode("|",$parcelItemQuantityArr[$weightCount]);
							if(count($_parcelItemQuantityArr) > 0){
								foreach($_parcelItemQuantityArr as $pii => $parcelItemQuantity){
									$parcelItem[$pii]["no_of_items"] = $parcelItemQuantity;
								}
							}
						}
					}
					//parcel item value
					if(count($parcelItemsValueArr) > 0){
						if(isset($parcelItemsValueArr[$weightCount])){
							$_parcelItemsValueArr = explode("|",$parcelItemsValueArr[$weightCount]);
							if(count($_parcelItemsValueArr) > 0){
								foreach($_parcelItemsValueArr as $pii => $parcelItemsValue){
									$parcelItem[$pii]["item_value"] = $parcelItemsValue;
								}
							}
						}
					}
					//parcel hs code
					if(count($parcelItemHsArr) > 0){
						if(isset($parcelItemHsArr[$weightCount])){
							$_parcelItemHsArr = explode("|",$parcelItemHsArr[$weightCount]);
							if(count($_parcelItemHsArr) > 0){
								foreach($_parcelItemHsArr as $pii => $parcelItemHs){
									$parcelItem[$pii]["hscode"] = $parcelItemHs;
								}
							}
						}
					}
					//parcel manufacture country
					if(count($parcelItemMfCountryArr) > 0){
						if(isset($parcelItemMfCountryArr[$weightCount])){
							$_parcelItemMfCountryArr = explode("|",$parcelItemMfCountryArr[$weightCount]);
							if(count($_parcelItemMfCountryArr) > 0){
								foreach($_parcelItemMfCountryArr as $pii => $parcelItemMfCountry){
									$parcelItem[$pii]["manufacture_country_iso"] = $parcelItemMfCountry;
								}
							}
						}
					}
					$consignmentArray['parcel'][$weightCount]['items'] = $parcelItem;
					$weightCount++;
				}
			}
			///////////////////////////////////////////////////////////////////////////////////////
			/*
            // Set parcel items values
            $parcelItemDescFinalArr = [];
            $parcelItemdescArr = explode('#',$row['parcel_item_desc']);
            foreach ($parcelItemdescArr as $index => $parcelItemdesc) {
                $parcelItemDescFinalArr[$index] = explode('|',$parcelItemdesc);
            }
            $parcelItemUrlFinalArr = [];
            $parcelItemUrlArr = explode('#',$row['parcel_item_url']);
            foreach ($parcelItemUrlArr as $index => $parcelItemUrl) {
                $parcelItemUrlFinalArr[$index] = explode('|',$parcelItemUrl);
            }
            $parcelItemWeightFinalArr = [];
            $parcelItemWeightArr = explode('#',$row['parcel_item_weight']);
            foreach ($parcelItemWeightArr as $index => $parcelItemWeight) {
                $parcelItemWeightFinalArr[$index] = explode('|',$parcelItemWeight);
            }
            $parcelItemSkuFinalArr = [];
            $parcelItemSkuArr = explode('#',$row['parcel_item_sku']);
            foreach ($parcelItemSkuArr as $index => $parcelItemSku) {
                $parcelItemSkuFinalArr[$index] = explode('|',$parcelItemSku);
            }
            $parcelItemQuantityFinalArr = [];
            $parcelItemQuantityArr = explode('#',$row['parcel_item_quantity']);
            foreach ($parcelItemQuantityArr as $index => $parcelItemQuantity) {
                $parcelItemQuantityFinalArr[$index] = explode('|',$parcelItemQuantity);
            }
            $parcelItemValueFinalArr = [];
            $parcelItemValueArr = explode('#',$row['parcel_item_value']);
            foreach ($parcelItemValueArr as $index => $parcelItemValue) {
                $parcelItemValueFinalArr[$index] = explode('|',$parcelItemValue);
            }
            $parcelItemHsFinalArr = [];
            $parcelItemHsArr = explode('#',$row['parcel_item_hs_code']);
            foreach ($parcelItemHsArr as $index => $parcelItemHs) {
                $parcelItemHsFinalArr[$index] = explode('|',$parcelItemHs);
            }
            $parcelItemMfCountryFinalArr = [];
            $parcelItemMfCountryArr = explode('#',$row['parcel_item_manufacture_country']);
            foreach ($parcelItemMfCountryArr as $index => $parcelItemMfCountry) {
                $parcelItemMfCountryFinalArr[$index] = explode('|',$parcelItemMfCountry);
            }
            $consignmentArray['save_invalid'] = false;
            // Make parcel weight array
            $consignmentArray['parcel'] = [];
            $parcelItemArrData = [];
            foreach ($parcelItemDescFinalArr as $parcelKey => $parcelItemDescFinalDataArr) {
                // This is for parcel
                if(count($parcelItemDescFinalDataArr) > 0){
                    // This is for items
                    foreach ($parcelItemDescFinalDataArr as $itemKey => $parcelItemDescFinalData) {
                        $parcelItemArrData[] = array("item_description"=>$parcelItemDescFinalData,"item_url"=>$parcelItemUrlFinalArr[$parcelKey][$itemKey],"item_sku"=>$parcelItemSkuArr[$parcelKey][$itemKey],"no_of_items"=>$parcelItemQuantityFinalArr[$parcelKey][$itemKey],"item_value"=>$parcelItemValueFinalArr[$parcelKey][$itemKey],"weight"=>$parcelItemWeightFinalArr[$parcelKey][$itemKey],"hscode"=>$parcelItemHsFinalArr[$parcelKey][$itemKey],"manufacture_country_iso"=>$parcelItemMfCountryFinalArr[$parcelKey][$itemKey]);
                    }
                }
            }
            $consignmentArray['parcel']['items'] = $parcelItemArrData;

            $parcelWeightArr = explode("|", $row['weight']);
            if(count($parcelWeightArr) > 0){
                $weightCount = 0;
                foreach ($parcelWeightArr as $parcelWeight) {
                    $consignmentArray['parcel'][$weightCount]['weight'] = trim($parcelWeight);
                    $weightCount++;
                }
            }
            // Make parcel length array
            $parcelLengthArr = explode("|", $row['length']);
//            if(count($parcelLengthArr) > 0){
                for ($l = 0 ; $l < $weightCount ; $l++) {
                    if(isset($parcelLengthArr[$l]))
                        $consignmentArray['parcel'][$l]['length'] = trim($parcelLengthArr[$l]);
                    else
                        $consignmentArray['parcel'][$l]['length'] = 0;
                }
//            }
            // Make parcel height array
            $parcelHeightArr = explode("|", $row['height']);
//            if(count($parcelHeightArr) > 0){
                for ($h = 0 ; $h < $weightCount ; $h++) {
                    if(isset($parcelHeightArr[$h]))
                        $consignmentArray['parcel'][$h]['height'] = trim($parcelHeightArr[$h]);
                    else
                        $consignmentArray['parcel'][$h]['height'] = 0;
                }
//            }
            // Make parcel width array
            $parcelWidthArr = explode("|", $row['width']);
//            if(count($parcelWidthArr) > 0){
                for ($w = 0 ; $w < $weightCount ; $w++) {
                    if(isset($parcelWidthArr[$w]))
                        $consignmentArray['parcel'][$w]['width'] = trim($parcelWidthArr[$w]);
                    else
                        $consignmentArray['parcel'][$w]['width'] = 0;
                }
//            }
            // Make parcel width array
            $parcelItemValueArr = explode("|", $row['itemvalue']);
//            if(count($parcelItemValueArr) > 0){
                for ($v = 0 ; $v < $weightCount ; $v++) {
                    if(isset($parcelItemValueArr[$v]))
                        $consignmentArray['parcel'][$v]['itemvalue'] = trim($parcelItemValueArr[$v]);
                    else
                        $consignmentArray['parcel'][$v]['itemvalue'] = 0;
                }
//            }
			*/
            if(isset($consignmentArray['awb'])){
                $parcelTrackingValueArr = explode("|", $row['tracking_number']);
                $consignmentArray['awb'] = @$parcelTrackingValueArr[0];
                for ($t = 0 ; $t < $weightCount ; $t++) {
                    if(isset($parcelTrackingValueArr[$t]))
                        $consignmentArray['parcel'][$t]['tracking_number'] = trim($parcelTrackingValueArr[$t]);
                    else
                        $consignmentArray['parcel'][$t]['tracking_number'] = '';
                }   
            }
                
            $return = array();
            $return['status'] = true;
            $return['error'] = '';
            $error_array = array();
            $now = microtime(true);
            $account_from_file = $row['user_id'];
    //        if (trim($UserObject->getUserName()) != "") { // no account number passed, import for all accounts
    //            if (trim($UserObject->getUserName()) != $account_from_file) { // account information does match
    //                $return['error'] = "<br />" . $file_object->getHawb() . " rejected. Your username: " . trim($UserObject->getUserName()) . " File account number: " . $account_from_file;
    //                $return['status'] = false;
    //                return $return;
    //            }
    //        }
            $return['time2'] = $this->time_elapsed($now);
            $now = microtime(true);

    //         $output = Consignment::createShipment($consignmentArray, $UserObject->getId());
             $output = Consignment::saveShipment($consignmentArray, $row['user_id'], false, '', 'csv');
            
             if(trim($output['STATUS']) == 'ERROR')
             {
                 $return['error'] .= '<br />'.$consignmentArray['order_reference'] . " - " .$output['MESSAGE'];
                 $return['status'] = false;
             } else {
                $consignmentId = $output['CONSIGNMENT_ID'];
                if($consignmentId > 0) {
                    $parcelFilter = new ParcelFilter();
                    $parcelFilter->addFieldFilter("     consignment_id", $consignmentId);
                    $parcelFilterObj = $parcelFilter->getList();
                    if(count($parcelFilterObj) > 0) {
                        foreach ($parcelFilterObj as $parcelObj) {
                            $parcelId = $parcelObj->getId();
                            $mawbFilter = new MawbFilter();
                            $mawbFilter->addFieldFilter("       mawb_number", $row['mawb_number']);
                            $mawbFilterObj = $mawbFilter->getList();
                            if(count($mawbFilterObj) > 0) {
                                $mawbId = $mawbFilterObj[0]->getId();
                                $date_added = time();
                                $added_by = $this->user->getId();
                                $date_update = time();
                                $update_by = $this->user->getId();

                                $mawbParcelMapping = new MawbParcelMapping();
                                $mawbParcelMapping->setMawbId($mawbId);
                                $mawbParcelMapping->setParcelId($parcelId);
                                $mawbParcelMapping->setWharehouseId(0);
                                $mawbParcelMapping->setBagId(0);
                                $mawbParcelMapping->setDateAdded($date_added);
                                $mawbParcelMapping->setAddedBy($added_by);
                                $mawbParcelMapping->setDateUpdated($date_update);
                                $mawbParcelMapping->setUpdatedBy($update_by);
                                $mawbParcelMapping->save();
                            }

                            $baggingFilter = new BaggingFilter();
                            $baggingFilter->addFieldFilter("        bagnumber", $row['bag_number']);
                            $baggingFilterObj = $baggingFilter->getList();
                            if(count($baggingFilterObj) > 0) {
                                $bagId = $baggingFilterObj[0]->getId();
                                $parcelBaggingMapping = new ParcelBaggingMapping();
                                $parcelBaggingMapping->setParcelId($parcelId);
                                $parcelBaggingMapping->setBagId($bagId);
                                $parcelBaggingMapping->save();
                            }
                        }
                    }
                }
             }

            $return['time3'] = $this->time_elapsed($now);
            $now = microtime(true);

            $this->import_num_accepted++;


            ++$this->import_num_checked;

            $return['time4'] = $this->time_elapsed($now);
            //exit;
            return $return;
        } else {
            $validatorArray = ['receiver_country_iso',
                'service_code',
                'receiver_contact',
                'receiver_address_line_1',
                'receiver_city',
                'receiver_postcode',
                'description',
                'weight',
                'length',
                'height',
                'width'
                ];
            foreach($validatorArray as $keyField => $validateFields)
                if(!empty($row[$validateFields]))
                    unset($validatorArray[$keyField]);
            
            return ['error' => "Import Data unsuccessful, Please check data in ". implode(", ", $validatorArray).(count($validatorArray)>1 ? ", all":", " )." required field"];
        }
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
