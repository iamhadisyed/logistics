<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

error_reporting(E_ALL);
ini_set('display_errors', 1);
//44require_once(SETTING_DIR_REMOTE . "Classes/PHPExcel.php");

require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
        ], '3rdparty/tcpdf');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'country.class',
    'countryfilter.class',
    'tracking.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'userservicesrouting.class',
    'consignmentbillinghold.class',
    'consignmentbillingholdfilter.class',
    'consignmentbillingholdlog.class',
    'consignmentbillingholdlogfilter.class',
    'consignmentchargeslog.class',
    'consignmentchargeslogfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentstatuslog.class',
    'consignmentstatuslogfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'consignmentbaggingmapping.class',
    'consignmentbaggingmappingfilter.class',
    'currencyfilter.class',
    'currency.class',
    'invoicedetail.class',
    'invoicedetailfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'invoicebankdetails.class',
    'paymentshistoryfilter.class',
    'excelshipmentsreports.class',
    'warehouse.class',
    'warehousefilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'invoicetemplates.class',
    'invoicetemplatesfilter.class',
    'bagging.class',
    'baggingfilter.class',
    'csvtrackingtemplate.class',
    'csvtrackingtemplatefilter.class',
    

]);


// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $carrier_list;
    private $services_list;
    private $carrier;
    private $service;
    private $date_scanned_from;
    private $date_scanned_to;
    private $date_received_from;
    private $date_received_to;
    private $handlingcode;
    private $report_data;
	private $pallet_no;
	private $errorData ;
    
    private $where = "";
    private $join = "";
    /*     * *
     * Set the page header
     * @return void
     */

    
    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $this->user = $sessionUser = $user = SessionManager::getUser();
        
        $this->errorData = '';

        
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
            'bag_number'=> ['required' => false ,'desc' => 'bag number <small>[Set bag number]</small>'],
            'tracking_number'=> ['required' => false ,'desc' => 'tracking number <small>[Enter tracking number]</small>'],
            'mawb_number'=> ['required' => false ,'desc' => 'mawb number <small>[Enter MAWB number]</small>'],
            'flight_number'=> ['required' => false ,'desc' => 'flight number <small>[Enter Flight number</small>]']
        );
        
        if (@$this->form_vars["form_action"] == "get_carrier_tracking_data") {
            $service_id = $this->form_vars["service_id"];
            $output = ["status"=>false, "message"=>"There is some issue, please contact to support team at info@smarttrack.co"];
            $trackingObj = TrackingDataFilter::getTrackingPointsDescriptonByServiceId($service_id);
            //echo count($trackingObj);
            if(count($trackingObj)>0){
                $output["status"] = true;
                $output["data"] = [];    
                foreach($trackingObj as $trackingObjKey => $trackingObjVal){
                    $output["data"][] = $trackingObjVal->getCarrierDesc();    
                }
                unset($output["message"]);
            } else {
                $output = ["status"=>false, "message"=>"No track point found to generate grid, Please contact to support team at info@smarttrack.co"];
            }
            echo json_encode($output );
            die;
            
        }else if (@$this->form_vars["form_action"] == "Export") {
            $output = array();
            if (isset($this->form_vars['date_scanned_from'])) {
                $date_scanned_fromp = $this->form_vars['date_scanned_from'];
                $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'] = $date_scanned_fromp;
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_FROM'])) {
                $date_scanned_fromp = $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'];
            }
            $this->date_received_from = $date_scanned_fromp;
            if (isset($this->form_vars['date_scanned_to'])) {
                $date_scanned_top = $this->form_vars['date_scanned_to'];
                $_SESSION['USER_REPORTING']['DATE_SCAN_TO'] = $bag_numberp;
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_TO'])) {
                $date_scanned_top = $_SESSION['USER_REPORTING']['DATE_SCAN_TO'];
            }
            $this->date_received_to = $date_scanned_top;
            if (isset($this->form_vars['mawb'])) {
                $this->mawb = $this->form_vars['mawb'];
            }
            if (isset($this->form_vars['user_template_select'])) {
                $this->user_template_select = $this->form_vars['user_template_select'];
            }
            
            if (isset($this->form_vars['accountcode'])) {
                $this->accountcode = $this->form_vars['accountcode'];
            }
            if (isset($this->form_vars['service_id'])) {
                $this->handlingcode = $this->form_vars['service_id'];
            }
            if (($this->accountcode == '' || $this->mawb == '') && (trim($this->date_received_from) == '' || trim($this->date_received_to) == '' )) {
                $this->errorData = 'Please select filter to download report data';
            }
            
            if(trim($this->errorData)!= ''){
                $output['status'] = "error";
                $output['message'] = $this->errorData;
                
            }
            else
            {
//            echo "<pre>"    ;
//            print_r($this->form_vars);
//            die;
            /*if ($this->handlingcode == 'OWEWHIUTR') {
                ob_end_clean();

                $uniqueFileName = $this->mawb . "-" . $filename;
                header("Content-Type: application/csv");
                header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
                $dateFrom = date("Y-m-d");
                $dateTo = date("Y-m-d");

                echo Consignment::GetWhistlReport($this->date_received_from, $this->date_received_to, $this->mawb, $this->accountcode, $this->handlingcode, false);
                exit;
            } else {*/
                
                ob_end_clean();
                /*$filename = date("Y-m-d")."-".time();
                $uniqueFileName = $this->mawb . "-" . $filename;
                header("Content-Type: application/csv");
                header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");*/
                $dateFrom = date("Y-m-d");
                $dateTo = date("Y-m-d");
                
                
                //if (trim($this->handlingcode) == 'OWE48TRAC') {
                    $dataToExport = Consignment::getEbayMasterReport($this->date_received_from, $this->date_received_to, $this->mawb, $this->accountcode, $this->handlingcode,$this->user_template_select);
                    
                    if($dataToExport['status'])
                    {
                        $fileName = time().".csv";
                        $dirFilename = _ASSETS_PATH . "csv/".$fileName;
                        $urlFilename = _ASSETS_URL . "csv/".$fileName;
                       // file_put_contents($dirFilename, $dataToExport);
                        $fp = fopen($dirFilename, 'w'); 
                        // Loop through file pointer and a line 
                        foreach ($dataToExport["data"] as $fields) { 
                            fputcsv($fp, $fields); 
                        } 
                        fclose($fp);
                        $output['status'] = "success";
                        $output['message'] = 'Please <a href="'.$urlFilename.'" class="btn btn-xs btn-primary">click here</a> to download report..';
                        
                        
                    } else{
                        $output['status'] = "error";
                        $output['message'] = $dataToExport['message'];//"No data found to download.";
                    }
            }
                echo json_encode($output);
                die;
            exit;
        }
        /*         * *****************Whistle Report Button Sumbit Code Start Here ******************** */ 
        else if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_user_template') {
            $service_id  = $this->form_vars['service_id'];
            $output = [];
            $userAccountId = $this->user->getUserAccountId();
            $csvImportTemplateFilter = new CsvTrackingTemplateFilter();
            $csvImportTemplateFilter->addFieldFilter("    ctt.service_id", $service_id);
            $csvImportTemplateFilter->addFieldFilter("    ctt.user_account_id", $userAccountId);
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
        } else if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'save_csv_template') {
            
            $finalDataSave = [];
            if(count($this->form_vars['csv'])>0){
                foreach($this->form_vars['csv'] as $keyCsv=>$valCsv){
                    $finalDataSave[$valCsv] =  (trim($this->form_vars['cap'][$keyCsv])!= '' ? $this->form_vars['cap'][$keyCsv] : $valCsv   );
                }
                
                $userId = $this->user->getId();
                $userAccountId = $this->user->getUserAccountId();
                $csvImportTemplate = new CsvTrackingTemplate();
                $csvImportTemplate->setUserId($userId);
                $csvImportTemplate->setUserAccountId($userAccountId);
                $csvImportTemplate->setTemplateName($this->form_vars['template_name']);
                $csvImportTemplate->setServiceId($this->form_vars['selected_service_id']);
                $csvImportTemplate->setTemplate(json_encode($finalDataSave));
                $csvImportTemplate->setAddedBy($userId);
                $csvImportTemplate->setAddedDate(time());
                $csvImportTemplate->save();
                if($csvImportTemplate->getId() > 0){
                    echo 'Template save successfully!';
                }else{
                    echo 'Template can not save!';
                }
            } else {
                echo 'Template can not save!, Please check the trackpoint to add into report.';
                
            }
            die;
        } else if (@$this->form_vars["form_action"] == "ExportWhistle") {
                   
               $whistleSummary = false;
                if (isset($this->form_vars['getWhistleSummary'])) {
                    $whistleSummary = true;
                }
 
            if (isset($this->form_vars['date_scanned_from'])) {
                $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'] =  $this->date_received_from = $this->form_vars['date_scanned_from'];
            
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_FROM'])) {
                $this->date_received_from = $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'];
            }
 
            if (isset($this->form_vars['date_scanned_to'])) {
               $_SESSION['USER_REPORTING']['DATE_SCAN_TO'] =  $this->date_received_to = $this->form_vars['date_scanned_to'];
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_TO'])) {
                $this->date_received_to = $_SESSION['USER_REPORTING']['DATE_SCAN_TO'];
            }
 
            if (isset($this->form_vars['mawb'])) {
                $this->mawb = trim($this->form_vars['mawb']);
            }
            if (isset($this->form_vars['accountcode'])) {
                $this->accountcode = $this->form_vars['accountcode'];
            }
            if (isset($this->form_vars['handlingcode'])) {
                $this->handlingcode = $this->form_vars['handlingcode'];
                $filename = (trim($this->handlingcode) == 'OWE48TRAC' ? 'TP48' : 'RM24');
            }
 
            if ( (trim($this->date_received_from) == '' && trim($this->date_received_to) == '' ) && $this->mawb == '' ) {
                  $this->errorData = 'Please select date range filter to download report data <br> OR  <br>  Enter MAWB number'; 
            }else if( $this->mawb == '' && $this->accountcode == '' ){
                   $this->errorData = 'Please select user account.'; 
            }else {


                if ($this->date_received_from != '' && $this->date_received_from != '1970-01-01' && $this->date_received_to != '' && $this->date_received_to != '1970-01-01') {
                    $where .= "AND date_format(date_received,'%Y-%m-%d') >= '" . DbAccess3::escape($this->date_received_from) . "' and date_format(date_received,'%Y-%m-%d') <= '" . DbAccess3::escape($this->date_received_to) . "' ";
                }
                if ($this->mawb != '') {
                    $where .= "AND   mawb = '" . $this->mawb . "' ";
                }
                if ($this->accountcode != '') {
                    $where .= "AND   user_code = '" . $this->accountcode . "' ";
                }
                if ($this->handlingcode != '') {
                    $where .= "AND   handling = '" . $this->handlingcode . "' ";
                }

              //  $where .= " AND c.date_scanned <> '0000-00-00 00:00:00'";
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->join = " INNER JOIN parcel p ON c.id = p.consignment_id ";
                $consignmentFilter->setFilter($where);
                $weigthColumn = 'IF(c.weight >vol_weight ,c.weight, vol_weight)';
                
                $caseForPredict ="
                        CASE WHEN
                            (TRIM(reference) = '' OR reference IS NULL OR UPPER(TRIM(reference)) NOT IN ('PACKET','LARGE LETTER', 'PACKETS','LARGE LETTERS'))
                             AND (c.weight > .750 OR p.length > 2.5 OR p.width > 2.5  OR p.height > 2.5)
                        THEN
                            'PACKETS'
                        WHEN
                        (TRIM(reference) = ''  OR reference IS NULL OR UPPER(TRIM(reference)) NOT IN ('PACKET','LARGE LETTER', 'PACKETS','LARGE LETTERS')) 
                        AND (c.weight <= .750 AND  p.length <= 2.5 AND p.width <= 2.5  AND p.height <= 2.5)
                        THEN
                            'LARGE LETTERS'
                            
                           
                            ";

                $fields = " hawb, awb, mawb, c.bag_number, handling, service_type, `reference` ,notes, p.length as parcel_length , p.width as parcel_width, p.height as parcel_height ,
                        CONCAT(address_line_1,address_line_2,address_line_3) as company,city, country, postcode, number_pieces,
                        IF(c.weight>vol_weight ,c.weight, vol_weight) whistle_chargable_weight,  c.weight , 
                        CASE 
                        WHEN $weigthColumn between 0 and 0.100 then '0-100' 
                        WHEN $weigthColumn between 0.1 and 0.250 then '101-250' 
                        WHEN $weigthColumn between 0.251 and 0.3 then '251-300' 
                        WHEN $weigthColumn between 0.301 and .350 then '301-350' 
                        WHEN $weigthColumn between 0.351 and .4 then '351-400' 
                        WHEN $weigthColumn between 0.401 and .450 then '401-450' 
                        WHEN $weigthColumn between 0.451 and .500 then '451-500' 
                        WHEN $weigthColumn between 0.501 and .550 then '501-550' 
                        WHEN $weigthColumn between 0.551 and .6 then '551-600' 
                        WHEN $weigthColumn between 0.601 and .650 then '601-650' 
                        WHEN $weigthColumn between 0.651 and .700 then '651-700' 
                        WHEN $weigthColumn between 0.701 and .750 then '701-750'                       
                        WHEN $weigthColumn between 0.751 and .800 then '751-800' 
                        WHEN $weigthColumn between 0.801 and .850 then '801-850' 
                        WHEN $weigthColumn between 0.851 and .900 then '851-900' 
                        WHEN $weigthColumn between 0.901 and .950 then '901-950' 
                        WHEN $weigthColumn between 0.951 and .1000 then '951-1000' 
                        WHEN $weigthColumn between 0.1001 and .1250 then '1001-1250' 
                        WHEN $weigthColumn between 0.1251 and .1500 then '1251-1500' 
                        WHEN $weigthColumn between 0.1501 and .1750 then '1501-1750' 
                        WHEN $weigthColumn between 0.1751 and .2000 then '1751-2000'
                        ELSE '1751-2000' END  as whistle_weight_range ,
                        $caseForPredict  ELSE UPPER(TRIM(reference)) END   AS predicted_reference
                        ";
  
                
                $resultWhistleData = $consignmentFilter->getColumnList($fields, 40000 );
                $totalRecords = count($resultWhistleData);

                if (isset($totalRecords) && $totalRecords > 0) {

                     $letterPacketDataArray = array(
                            'large letter' => array(
                                '0-100' => array(
                                    'item' => '0.71',
                                    'kilo' => '0.60',
                                    'nop' => 'E16',
                                    'weight' => 'F16',
                                    'charges' => 'G16'
                                ),
                                '101-250' => array(
                                    'item' => '0.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E17',
                                    'weight' => 'F17',
                                    'charges' => 'G17'),
                                '251-300' => array(
                                    'item' => '0.93',
                                    'kilo' => '0.60',
                                    'nop' => 'E18',
                                    'weight' => 'F18',
                                    'charges' => 'G18'),
                                '301-350' => array(
                                    'item' => '1.06',
                                    'kilo' => '0.60',
                                    'nop' => 'E19',
                                    'weight' => 'F19',
                                    'charges' => 'G19'),
                                '351-400' => array(
                                    'item' => '1.06',
                                    'kilo' => '0.60',
                                    'nop' => 'E20',
                                    'weight' => 'F20',
                                    'charges' => 'G20'),
                                '401-450' => array(
                                    'item' => '1.19',
                                    'kilo' => '0.60',
                                    'nop' => 'E21',
                                    'weight' => 'F21',
                                    'charges' => 'G21'),
                                '451-500' => array(
                                    'item' => '1.19',
                                    'kilo' => '0.60',
                                    'nop' => 'E22',
                                    'weight' => 'F22',
                                    'charges' => 'G22'),
                                '501-550' => array(
                                    'item' => '1.32',
                                    'kilo' => '0.60',
                                    'nop' => 'E23',
                                    'weight' => 'F23',
                                    'charges' => 'G23'),
                                '551-600' => array(
                                    'item' => '1.32',
                                    'kilo' => '0.60',
                                    'nop' => 'E24',
                                    'weight' => 'F24',
                                    'charges' => 'G24'),
                                '601-650' => array(
                                    'item' => '1.46',
                                    'kilo' => '0.60',
                                    'nop' => 'E25',
                                    'weight' => 'F25',
                                    'charges' => 'G25'),
                                '651-700' => array(
                                    'item' => '1.46',
                                    'kilo' => '0.60',
                                    'nop' => 'E26',
                                    'weight' => 'F26',
                                    'charges' => 'G26'),
                                '701-750' => array(
                                    'item' => '1.52',
                                    'kilo' => '0.60',
                                    'nop' => 'E27',
                                    'weight' => 'F27',
                                    'charges' => 'G27')
                            ),
                            'packet' => array(
                                '0-100' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E28',
                                    'weight' => 'F28',
                                    'charges' => 'G28'),
                                '101-250' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E29',
                                    'weight' => 'F29',
                                    'charges' => 'G29'),
                                '251-300' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E30',
                                    'weight' => 'F30',
                                    'charges' => 'G30'),
                                '301-350' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E31',
                                    'weight' => 'F31',
                                    'charges' => 'G31'),
                                '351-400' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E32',
                                    'weight' => 'F32',
                                    'charges' => 'G32'),
                                '401-450' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E33',
                                    'weight' => 'F33',
                                    'charges' => 'G33'),
                                '451-500' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E34',
                                    'weight' => 'F34',
                                    'charges' => 'G34'),
                                '501-550' => array(
                                    'item' => '1.86',
                                    'kilo' => '0.60',
                                    'nop' => 'E35',
                                    'weight' => 'F35',
                                    'charges' => 'G35'),
                                '551-600' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E36',
                                    'weight' => 'F36',
                                    'charges' => 'G36'),
                                '601-650' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E37',
                                    'weight' => 'F37',
                                    'charges' => 'G37'),
                                '651-700' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E38',
                                    'weight' => 'F38',
                                    'charges' => 'G38'),
                                '701-750' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E39',
                                    'weight' => 'F39',
                                    'charges' => 'G39'),
                                '751-800' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E40',
                                    'weight' => 'F40',
                                    'charges' => 'G40'),
                                '801-850' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E41',
                                    'weight' => 'F41',
                                    'charges' => 'G41'),
                                '851-900' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E42',
                                    'weight' => 'F42',
                                    'charges' => 'G42'),
                                '901-950' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E43',
                                    'weight' => 'F43',
                                    'charges' => 'G43'),
                                '951-1000' => array(
                                    'item' => '1.91',
                                    'kilo' => '0.60',
                                    'nop' => 'E44',
                                    'weight' => 'F44',
                                    'charges' => 'G44'),
                                '1001-1250' => array(
                                    'item' => '2.29',
                                    'kilo' => '0.60',
                                    'nop' => 'E45',
                                    'weight' => 'F45',
                                    'charges' => 'G45'),
                                '1251-1500' => array(
                                    'item' => '2.39',
                                    'kilo' => '0.60',
                                    'nop' => 'E46',
                                    'weight' => 'F46',
                                    'charges' => 'G46'),
                                '1501-1750' => array(
                                    'item' => '2.39',
                                    'kilo' => '0.60',
                                    'nop' => 'E47',
                                    'weight' => 'F47',
                                    'charges' => 'G47'),
                                '1751-2000' => array(
                                    'item' => '2.39',
                                    'kilo' => '0.60',
                                    'nop' => 'E48',
                                    'weight' => 'F48',
                                    'charges' => 'G48')
                        ));

                      if ($whistleSummary) {
                        $consignmentFilter = new ConsignmentFilter();
                        $consignmentFilter->join = " INNER JOIN parcel p ON c.id = p.consignment_id ";
                        $consignmentFilter->setFilter($where);
                      
                        $fields = " SUM(number_pieces)  number_pieces,
                            SUM(IF(c.weight>vol_weight ,c.weight, vol_weight)) whistle_chargable_weight,
                            CASE WHEN $weigthColumn between 0 and 0.100 then '0-100' 
                            WHEN $weigthColumn between 0.1 and 0.250 then '101-250' 
                            WHEN $weigthColumn between 0.251 and 0.3 then '251-300' 
                            WHEN $weigthColumn between 0.301 and .350 then '301-350' 
                            WHEN $weigthColumn between 0.351 and .4 then '351-400' 
                            WHEN $weigthColumn between 0.401 and .450 then '401-450'
                             WHEN $weigthColumn between 0.451 and .500 then '451-500' 
                             WHEN $weigthColumn between 0.501 and .550 then '501-550' 
                             WHEN $weigthColumn between 0.551 and .6 then '551-600' 
                             WHEN $weigthColumn between 0.601 and .650 then '601-650'
                             WHEN $weigthColumn between 0.651 and .700 then '651-700' 
                             WHEN $weigthColumn between 0.701 and .750 then '701-750' 
                             WHEN $weigthColumn between 0.751 and .800 then '751-800' 
                             WHEN $weigthColumn between 0.801 and .850 then '801-850' 
                             WHEN $weigthColumn between 0.851 and .900 then '851-900' 
                             WHEN $weigthColumn between 0.901 and .950 then '901-950' 
                             WHEN $weigthColumn between 0.951 and .1000 then '951-1000' 
                             WHEN $weigthColumn between 0.1001 and .1250 then '1001-1250' 
                             WHEN $weigthColumn between 0.1251 and .1500 then '1251-1500' 
                             WHEN $weigthColumn between 0.1501 and .1750 then '1501-1750' 
                             WHEN $weigthColumn between 0.1751 and .2000 then '1751-2000' ELSE '1751-2000' 
                             END as whistle_weight_range,   
                                            $caseForPredict
                                            WHEN UPPER(TRIM(reference)) = 'LARGE LETTER' THEN 'LARGE LETTERS'
                                            WHEN UPPER(TRIM(reference)) = 'PACKET' THEN 'PACKETS'
                                            ELSE UPPER(TRIM(reference))
                                        END AS packagetype  ";
                        $consignmentFilter->addGroupByClause(" whistle_weight_range , 
                                        $caseForPredict 
                                        WHEN  UPPER(TRIM(reference)) = 'LARGE LETTER'   THEN   'LARGE LETTERS'
                                        WHEN UPPER(TRIM(reference)) = 'PACKET' THEN 'PACKETS'
                                        ELSE UPPER(TRIM(reference))
                                    END");
                        $resultWhistleDataSummary = $consignmentFilter->getColumnList($fields,10000 ); 
              
                    }
 
                    $linecount =7;
                    $rangeCounter = 0;
                    $weightRanges = array('0-100', '101-250', '251-300', '301-350', '351-400', '401-450', '451-500', '501-550', '551-600', '601-650', '651-700', '701-750','751-800','801-850','851-900','901-950','951-1000','1001-1250','1251-1500','1501-1750','1751-2000');
                    $objPHPExcel = new PHPExcel();

                    
                    if ($whistleSummary) { 
                            $file = '../assets/mawb-report-sample-files/mwab-whistle-summary-sample-new.xlsx';
                            $file_type = 'Excel2007';
                            // Open file for reading
                            $objReader = PHPExcel_IOFactory::createReader($file_type);
                           ob_end_clean();
                            // Load the file
                            $objPHPExcel = $objReader->load($file);
                            $objPHPExcel->setActiveSheetIndex(0);
                            
                            if (count($resultWhistleDataSummary) > 0) {
                                
                                foreach ($resultWhistleDataSummary as $whishlistSummaryData) {
 
                                    $reference = rtrim(strtolower($whishlistSummaryData->getPackageType()), "s");
                                    $referenceRange = $whishlistSummaryData->getWhistleWeightRange();
           
                                    if (isset($letterPacketDataArray[$reference][$referenceRange])) {
                                        $itemPrice = $letterPacketDataArray[$reference][$referenceRange]['item'];
                                        $kiloPrice = $letterPacketDataArray[$reference][$referenceRange]['kilo'];
                                         $numberOfPiecesColumn = $letterPacketDataArray[$reference][$referenceRange]['nop'];
                                         $weightColumn = $letterPacketDataArray[$reference][$referenceRange]['weight'];
                                         $chargesColumn = $letterPacketDataArray[$reference][$referenceRange]['charges'];
                                        $calculatedPrice = (($kiloPrice * $whishlistSummaryData->getWhistleChargableWeight()) + ($itemPrice * $whishlistSummaryData->getNumberPieces()));
                                    // echo   'Column Name    weight column     charges     calculated prcie   range<br>';
                                       // echo $numberOfPiecesColumn.' '.$weightColumn.' '.$chargesColumn.' '.$calculatedPrice. '  '.$referenceRange.'<br>' ;
                                         $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValueExplicit($numberOfPiecesColumn, $whishlistSummaryData->getNumberPieces(), PHPExcel_Cell_DataType::TYPE_NUMERIC)
                                            ->setCellValueExplicit($weightColumn, $whishlistSummaryData->getWhistleChargableWeight(), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                           //  ->setCellValueExplicit($chargesColumn,$calculatedPrice, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                    } else {;

                                        $itemPrice = $kiloPrice=$calculatedPrice=$weightColumn=$chargesColumn=$calculatedPrice="";
                                    }
                                    unset($reference);
                                    unset($referenceRange);
                                    unset($calculatedPrice);
                                    unset($weightColumn);
                                    unset($numberOfPiecesColumn);
                                }
                                                    if($this->mawb!=''){
                                                    $objPHPExcel->getActiveSheet(0)->setCellValue("C5",$this->mawb);
                                                    }
                            }
                        }
                        
                    // Set properties
                    $objPHPExcel->getProperties()->setCreator("One World Express ")
                            ->setLastModifiedBy("One World Express")
                            ->setTitle("Office 2007 XLSX Whistl Report")
                            ->setSubject("Office 2007 XLSX Whistl Report")
                            ->setDescription("This document is generated from the system, generated using PHP classes.")
                            ->setKeywords("office 2007 openxml ")
                            ->setCategory("One World Express");
                    
                    
                       if ($whistleSummary) {
                            $whistleSheetNumber = 1;
                        } else {
                            $whistleSheetNumber = 0;
                        }
                        
                    $objPHPExcel->setActiveSheetIndex($whistleSheetNumber)
                            ->setCellValue('A6', "Order Ref No.")
                            ->setCellValue('B6', "Tracking Number")
                            ->setCellValue('C6', "MAWB")
                            ->setCellValue('D6', "Bag No.")
                            ->setCellValue('E6', "Service Name")
                            ->setCellValue('F6', "Address")
                            ->setCellValue('G6', "City")
                            ->setCellValue('H6', "Country")
                            ->setCellValue('I6', "Postcode")
                            ->setCellValue('J6', "Number of Pieces")
                            ->setCellValue('K6', "Chargable Weight")
                            ->setCellValue('L6', "Weight")
                            ->setCellValue('M6', "Chargable Weight")
                            ->setCellValue('N6', "Reference")
                            ->setCellValue('O6', "Blank Reference")
                            ->setCellValue('P6', "Predicted Reference")
                            ->setCellValue('Q6', "Item")
                            ->setCellValue('R6', "Kilo")
                            ->setCellValue('S6', "Total Charges")
                            ->setCellValue('T6', "Length")
                            ->setCellValue('U6', "Width")
                            ->setCellValue('V6', "Height")
                            ->setCellValue('W6', "Discrepency")
                            ->setCellValue('X6', "Weight Ranges Band");
       
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                            )
                        )
                    );

                    $emptyReference = 0;
                    $predictedPacket = 0;
                    $predictedLargeLetter = 0;
                    foreach ($resultWhistleData as $consignmentData) {
                        $reference = rtrim(strtolower($consignmentData->getReference()), "s");
                        $preference = rtrim(strtolower($consignmentData->getPredictedReference()), "s");
                        $referenceRange = $consignmentData->getWhistleWeightRange();
                        $referenceBlank = 'No';
                        
                        
                          if (!isset($letterPacketDataArray[$reference])){

                          if (trim($reference) == '') {
                                $emptyReference++;
                                $referenceBlank = 'Yes';
                            } else if (!in_array(strtoupper($reference), array('PACKET', 'LARGE LETTER', 'PACKETS', 'LARGE LETTERS'))) {
                                $referenceBlank = 'No';
                            }

                            if ($consignmentData->getPredictedReference() == 'PACKETS') {
                                $predictedPacket++;
                            } else if ($consignmentData->getPredictedReference() == 'LARGE LETTERS') {
                                $predictedLargeLetter++;
                            }
                            
                            $itemPrice = $letterPacketDataArray[$preference][$referenceRange]['item'];
                            $kiloPrice = $letterPacketDataArray[$preference][$referenceRange]['kilo'];
                            $calculatedPrice = (($kiloPrice * $consignmentData->getWhistleChargableWeight()) + ($itemPrice * $consignmentData->getNumberPieces()));
                        }else{
                           $predictedReference  = "";
                            $itemPrice = $letterPacketDataArray[$reference][$referenceRange]['item'];
                            $kiloPrice = $letterPacketDataArray[$reference][$referenceRange]['kilo'];
                            $calculatedPrice = (($kiloPrice * $consignmentData->getWhistleChargableWeight()) + ($itemPrice * $consignmentData->getNumberPieces()));
                            
                        }
                        
                        
                        $objPHPExcel->setActiveSheetIndex($whistleSheetNumber)
                                ->setCellValueExplicit('A' . $linecount, $consignmentData->getHawb(), PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValueExplicit('B' . $linecount, $consignmentData->getAwb(), PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValueExplicit('C' . $linecount, $consignmentData->getMawb(), PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValue('D' . $linecount, $consignmentData->getBagNumber())
                                ->setCellValue('E' . $linecount, $consignmentData->getServiceType())
                                ->setCellValue('F' . $linecount, $consignmentData->getCompany())
                                ->setCellValue('G' . $linecount, $consignmentData->getCity())
                                ->setCellValue('H' . $linecount, $consignmentData->getCountry())
                                ->setCellValue('I' . $linecount, $consignmentData->getPostcode())
                                ->setCellValue('J' . $linecount, $consignmentData->getNumberPieces())
                                ->setCellValue('K' . $linecount, $consignmentData->getWhistleChargableWeight())
                                ->setCellValue('L' . $linecount, $consignmentData->getWeight())
                                ->setCellValue('M' . $linecount, $consignmentData->getWhistleWeightRange())
                                ->setCellValueExplicit('N' . $linecount, $consignmentData->getReference(), PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValue('O' . $linecount, $referenceBlank)
                                ->setCellValue('P' . $linecount,  $consignmentData->getPredictedReference())
                                ->setCellValue('Q' . $linecount, $itemPrice)
                                ->setCellValue('R' . $linecount, $kiloPrice)
                                ->setCellValue('S' . $linecount, $calculatedPrice)
                                ->setCellValue('T' . $linecount,  $consignmentData->getParcelLength())
                                ->setCellValue('U' . $linecount,  $consignmentData->getParcelWidth())
                                ->setCellValue('V' . $linecount,  $consignmentData->getParcelHeight())
                                 ->setCellValue('W' . $linecount, (trim($consignmentData->getNotes())=='discrepency')?'Discrepancy':'')
                                ->setCellValue('X' . $linecount, $weightRanges[$rangeCounter])
                                ;

                        if(trim($consignmentData->getNotes())=='discrepency'){
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$linecount.':W'.$linecount)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ffb0a8');
                        }
                        
                        $rangeCounter++;
                        $linecount++;
                        unset($reference);
                        unset($referenceRange);
                        unset($calculatedPrice);
                    }
 
                    // Set properties
                        $borderStyleBold = array(
                      'borders' => array(
                        'outline' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                );
                        
                        
                    $objPHPExcel->getActiveSheet()->mergeCells('B1:E4');
                    $objPHPExcel->getActiveSheet()->getStyle('B1:E4')
                                ->applyFromArray($borderStyleBold);
                    $dataDetail = "Total Records : ".$totalRecords;
                    $dataDetail .= "\n"; 
                    $referencePercentage =  round((($emptyReference/$totalRecords)*100),2);
                    $dataDetail .= "Empty Reference : ".$emptyReference. " ( $referencePercentage % )";
                    $dataDetail .= "\n";
                    $dataDetail .= "Total empty reference converted to Packtes: ".$predictedPacket;
                    $dataDetail .= "\n";
                    $dataDetail .= "Total empty reference converted to Large Letters: ".$predictedLargeLetter  ;
                    
                    

                    
                    $objPHPExcel->getActiveSheet()->setCellValue("B1",$dataDetail);
                    $objPHPExcel->getActiveSheet()->getStyle( "B1") ->applyFromArray(array(
                    'font' => array(
                                'bold' => true,
                                'color' => array('rgb' => 'FF0000'),
                                'size' => 10 ,
                                'name' => 'Verdana'
                        ),'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT) ));
                    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setWrapText(true);
                     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

                    $columRangeM = 'M7:M' . ($totalRecords + 6);
                    $columRangeX = "X7:X" . (count($weightRanges) + 6);
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($columRangeM)
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('A6A6A6');
                    $objPHPExcel->getActiveSheet()->getStyle($columRangeM)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($columRangeX)
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('A6A6A6');
                    $objPHPExcel->getActiveSheet()->getStyle($columRangeX)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->setAutoFilter('A6:W6');
                    foreach(array('B','C','D','E','N','O','P','S','T') as $columnID) {
                     $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                         ->setAutoSize(true);
                }

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    if($this->mawb!=''){
                        $file_name = "mwab-".$this->mawb."-whistl-report";
                    }else{
                        $file_name = "mwab-".$this->date_received_from."-".$this->date_received_to."-whistl-report";
                    }
                    header('Content-Disposition: attachment;filename="'.$file_name.'-' . time() . '.xlsx"');
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    ob_clean();
                    $objWriter->setPreCalculateFormulas(); 
                    $objWriter->save('php://output');
                    exit;
                } else {
                    $this->errorData = 'No data found for selected filter.';
                }
            }
        }
        /*******************Ebay Royal Mail Report Button Sumbit Code Start Here ******************** */ 
        else if (@$this->form_vars["form_action"] == "ExportEbayRoyal") {
                   
               $whistleSummary = false;
                if (isset($this->form_vars['getWhistleSummary'])) {
                    $whistleSummary = true;
                }
 
            if (isset($this->form_vars['date_scanned_from'])) {
                $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'] =  $this->date_received_from = $this->form_vars['date_scanned_from'];
            
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_FROM'])) {
                $this->date_received_from = $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'];
            }
 
            if (isset($this->form_vars['date_scanned_to'])) {
               $_SESSION['USER_REPORTING']['DATE_SCAN_TO'] =  $this->date_received_to = $this->form_vars['date_scanned_to'];
            } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_TO'])) {
                $this->date_received_to = $_SESSION['USER_REPORTING']['DATE_SCAN_TO'];
            }
 
            if (isset($this->form_vars['mawb'])) {
                $this->mawb = trim($this->form_vars['mawb']);
            }
            if (isset($this->form_vars['accountcode'])) {
                $this->accountcode = $this->form_vars['accountcode'];
            }
            if (isset($this->form_vars['handlingcode'])) {
                $this->handlingcode = $this->form_vars['handlingcode'];
                $filename = (trim($this->handlingcode) == 'OWE48TRAC' ? 'TP48' : 'RM24');
            }
 
             if( $this->mawb == ''){
                   $this->errorData = 'Please enter MWAB number for Ebay Royal'; 
            }else {


              
                if ($this->mawb != '') {
                    $where .= "AND   mawb = '" . $this->mawb . "' ";
                }
                if ($this->accountcode != '') {
                    $where .= "AND   user_code = '" . $this->accountcode . "' ";
                }
                if ($this->handlingcode != '') {
                    $where .= "AND   handling = '" . $this->handlingcode . "' ";
                }

                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->setFilter($where);

                $fields = " handling,
                            service_type,
                            SUM(number_pieces) number_pieces,
                            SUM(IF(weight > vol_weight,
                                weight,
                                vol_weight)) whistle_chargable_weight
                      ";
 
                 $consignmentFilter->addGroupByClause(" service_type ");
                  $consignmentFilter->AddOrderByHandling();
                $resultRoyalMailData = $consignmentFilter->getColumnList($fields,1000,true);
 
                $totalRecords = count($resultRoyalMailData);

                if (isset($totalRecords) && $totalRecords > 0) {
 
                    $totalItemPerManifest = 0;
                    $totalWeightPerManifest = 0;
                    $objPHPExcel = new PHPExcel();
 
                            $file = '../assets/mawb-report-sample-files/ebay-royal-mail-sample.xlsx';
                            $file_type = 'Excel2007';
                            // Open file for reading
                            $objReader = PHPExcel_IOFactory::createReader($file_type);
                            ob_end_clean();
                            // Load the file
                            $objPHPExcel = $objReader->load($file);
                            
                            
                             foreach ($resultRoyalMailData as $consignmentData) {
                                        if($consignmentData->getHandling()=='OWE24STAN'){
                                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D10'  , $consignmentData->getNumberPieces());
                                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E10' , $consignmentData->getWhistleChargableWeight());
                                                
                                                        
                                            $totalItemPerManifest  = $totalItemPerManifest + $consignmentData->getNumberPieces();
                                            $totalWeightPerManifest  = $totalWeightPerManifest + $consignmentData->getWhistleChargableWeight();
                                        }
                                        if($consignmentData->getHandling()=='OWE48TRAC'){
                                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13'  , $consignmentData->getNumberPieces());
                                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E13' , $consignmentData->getWhistleChargableWeight());
                                            $totalItemPerManifest  = $totalItemPerManifest + $consignmentData->getNumberPieces();
                                            $totalWeightPerManifest  = $totalWeightPerManifest + $consignmentData->getWhistleChargableWeight();
                                        }
                                        
                                    }
                                    
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', $this->mawb);
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', $totalItemPerManifest);
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', $totalWeightPerManifest);
                           
                      
                    // Set properties
                    $objPHPExcel->getProperties()->setCreator("One World Express ")
                            ->setLastModifiedBy("One World Express")
                            ->setTitle("Office 2007 XLSX Whistle Report")
                            ->setSubject("Office 2007 XLSX Whistle Report")
                            ->setDescription("This document is generated from the system, generated using PHP classes.")
                            ->setKeywords("office 2007 openxml ")
                            ->setCategory("One World Express");
        
                     

                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    if($this->mawb!=''){
                        $file_name = "mwab-".$this->mawb."-ebay-royalmail";
                    } 
                    header('Content-Disposition: attachment;filename="'.$file_name.'-' . time() . '.xlsx"');
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    ob_clean();
                    $objWriter->setPreCalculateFormulas(); 
                    $objWriter->save('php://output');
                    exit;
                } else {
                    $this->errorData = 'No data found for selected filter.';
                }
            }
        }
        /*******************Whistle Report Button Sumbit Code Ends Here *********************/
    }
    public function renderBody() {
        ?>
<!-- END STYLE CUSTOMIZER -->
<!-- BEGIN PAGE HEADER-->
    <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">
<div class="row">
<div class="portlet light">
<div class="portlet-title">
  <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
    <div class="actions"> 
        <a href="#" id="csv_temp_modal" class="btn blue" style="display: none;"><i class="icon-wrench"></i>&nbsp;Create Template</a>
    </div>
  
</div>
<div class="portlet-body">
    <div class="alert alert-danger hidden" id="result-data-msg"></div>
  <div class="scroller" data-rail-color="blue" data-handle-color="blue">
  <?php 
  	if(trim($this->errorData) != '')
		{
			echo '<div class="row">
			      	<div class="col-md-12">
						<div class="alert alert-danger">
						'.$this->errorData.'
						</div>
	  				</div>
				</div> ';
		}
  ?>
    <div class="row">
      <div class="col-md-3">
     <div class="form-group  date-date-pic">
         <input class="form-control" autocomplete="off"  name="date_scanned_from" data-date-format="yyyy-mm-dd" id="date_scanned_from" type="text" placeholder="<?php echo Translation::GetCaption("DATEFROM") ?>" value="<?php echo @$this->date_received_from; ?>" />
        
        </div>
      </div>
      <div class="col-md-3">
         <div class="form-group  date-date-pic">
             <input class="form-control" autocomplete="off" name="date_scanned_to" data-date-format="yyyy-mm-dd" id="date_scanned_to" type="text" placeholder="<?php echo Translation::GetCaption("DATETO") ?>" value="<?php echo @$this->date_received_to; ?>" />
         
        </div>
      </div>
       <div class="col-md-3">
         <div class="form-group">
            <input class="form-control" name="mawb"  id="mawb" type="text" placeholder="MAWB" value="<?php echo @$this->mawb; ?>" />
         
        </div>
      </div>
        <div class="col-md-3">
            
            <div class="form-group  has-float-label">
                
                <div id="user_content">
                    <?php
                    $accountParentId = "0";
                    $includeParent = true;
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $accountParentId = $this->user->getUserAccountId();
                    }
                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                        $accountParentId = $this->user->getUserAccountId();
                    }
                    $selectedAccount = "";

                    $allowedLevel = 0;
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $allowedLevel = 1;
                    }
                    echo Ddl::showTreeDropdown('accountcode', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent, $allowedLevel);
                    ?>
                </div>
                <label class="label-account">Select Account</label>
            </div>
        </div>
      <div class="col-md-3">
         <div class="form-group">
             <?php 
             echo Ddl::generateServiceDDLWithImage('service_id', "", 'id', $attr = ' class="bs-select form-control" required="" data-show-subtext="true" data-container="body"',$dd_id='service_id',$title='Select Service',$displayValue='name',$selectTitle='Select Services',0);
             ?>
        </div>
      </div>
        <div class="col-md-3">
<!--                            <label class="label-account">Select CSV Template</label>-->
                            <div class="form-group">
                                <div id="user_content">
                                    <select class="form-filter select2 form-control" name="user_template_select" id="user_template_select">
                                        <option value="0">Default Template</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
    </div>
      <div class="row">
   <div class="col-md-8">
                                <input type="hidden" name="form_action" id="form_action"  />
                                <button type="button" name="btnExport" id="btnExport" class="btn btn-primary">Download</button>
                                        <?php /*if ($user->getUserType() == User::USER_TYPE_FINANCE) 
                                        { ?>
                                            <button type="button" name="btnExportEbayRoyal" id="btnExportEbayRoyal" class="btn btn-info">Download Ebay Royal</button>
                                            <button type="button" name="btnExportWhistle" id="btnExportWhistle" class="btn btn-info">Download Ebay Whistl</button>
                                            <input type="checkbox" name="getWhistleSummary" id="getWhistleSummary"> Include Ebay Whistl Summary Report 
                                            <br><small>Whistl report will download max 20000 records (Note: Select max 1 week date range)</small>
<!--                                            <br><small><b>Shipment having blank reference will be Priced as Large Packet</b></small>-->
                                        <?php } */ ?>
                            </div>
      </div>
    </div>
  </div>
</div>
</form>
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
                                                <ol class="dd-list" id="check_columns_html">
                                                </ol>
                                            </div>
                                            <input type="hidden" name="func" value="save_csv_template" />
                                            <input id="selected_service_id" name="selected_service_id" type="hidden" />
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

<!-- END PAGE CONTENT-->
<?php
    }
    /**
     * Override to show the menu
     *
    */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }
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
        <link href="../assets/global/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">
        <style>
            .label-account{
                font-size: 12px;
                font-weight: bold;
            }
            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
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
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-nestable/jquery.nestable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
<script type="text/javascript">
		
		
			
    $(document).ready(function() 
    {
        $("#date_scanned_from").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
        $("#date_scanned_to").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
        $("#btnExport").click(function()
        {
           $("#form_action").val("Export");
           var frm_serialize = $("#adminForm").serialize();
           $.ajax({
                    type: "POST",
                    url: "mawb_account_report.php", // your php file name
                    dataType: "json",
                    data: frm_serialize,
                    success: function (response){
                        $("#result-data-msg").removeClass('hidden');
                        
                        if($("#result-data-msg").hasClass('alert-success'))
                           $("#result-data-msg").removeClass('alert-success');
                        if($("#result-data-msg").hasClass('alert-danger'))
                            $("#result-data-msg").removeClass('alert-danger');
                        if(response.status == 'error'){
                            $("#result-data-msg").addClass('alert-danger');
                        }else{
                           $("#result-data-msg").addClass('alert-success');
                        }
                        $("#result-data-msg").html(response.message);
                        $("#result-data-msg").show();
                    },
                    error: function (errorString){

                    }
                });
           
        
       }); 
//   $("#adminForm").submit();/*
       /*$("#btnExportWhistle").click(function()
        {
           $("#form_action").val("ExportWhistle");
           $("#adminForm").submit();
       }); 

        $("#btnExportEbayRoyal").click(function()
        {
           $("#form_action").val("ExportEbayRoyal");
           $("#adminForm").submit();
       }); 
         */    
         
             
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
            var serviceid = $("#service_id").val();
            $.ajax({
                    type: "POST",
                    url: "mawb_account_report.php", // your php file name
                    dataType: "json",
                    data: {'form_action': 'get_carrier_tracking_data',"service_id":serviceid},
                    success: function (response)
                    {
                        var html = "";
                        if(response.status){
                            html    +=  '<li style="display: flex;">';
                                html    +=  '   <b>Select</b>';
                                html    +=  '   <div class="col-sm-6">'
                                html    +=  '     <b>Track Point</b>';
                                html    +=  '   </div>';
                                html    +=  '   <div class="col-sm-6">'
                                html    +=  '       <b>Title</b>';
                                html    +=  '   </div>';
                                html    +=  '</li>';
                                
                            $.each(response.data, function(datakey,dataval){
                               // html    += dataval+"<br>";
                                /*html    +=  '<li style="display: flex;" class="dd-item " data-id="'+datakey+'">';
                                html    +=  '   <input style="margin-top:16px;" id="csv_chk_'+datakey+'" name="csv['+datakey+']" type="checkbox" class="form-filter" value="'+datakey+'"/>&nbsp;&nbsp;&nbsp;&nbsp';
                                html    +=  '   <div style="width:100%;" class="dd-handle">';
                                html    +=  '       '+dataval;
                                html    +=  '   </div>';
                                html    +=  '</li>';*/
                                
                                html    +=  '<li style="display: flex;" class="dd-item" data-id="'+datakey+'">';
                                html    +=  '   <input style="margin-top:10px;" id="csv_chk_'+datakey+'" name="csv['+datakey+']" type="checkbox" class="form-filter" value="'+dataval+'"/>&nbsp;&nbsp;&nbsp;&nbsp';
                                html    +=  '   <div class="col-sm-6 dd-handle">'
                                html    +=  '       <div style="width:100%; margin:0px !important;" >';
                                html    +=  '           '+dataval;
                                html    +=  '       </div>';
                                html    +=  '   </div>';
                                html    +=  '   <div class="col-sm-6" style"margin:5px 0 !important; padding:5px 10px !important;">'
                                html    +=  '       <div class="form-group">';
                                html    +=  '           <input id="csv_cap_'+datakey+'" name="cap['+datakey+']" type="text" class="form-control input-sm" value=""/>';                            
                                html    +=  '       </div>';
                                html    +=  '   </div>';
                                html    +=  '</li>';
                                 
                            });
                            $('#check_columns_html').html(html);
                            $('#make-switch-columns').attr('checked', true);
                             $('#make-switch-columns').bootstrapSwitch();
                             $('.csvCheckbox').iCheck({
                                checkboxClass: 'icheckbox_minimal-grey',
                            });
                            //alert(html);
                        }
                       /* $('#check_columns_html').html(data);
                        $('#make-switch-columns').attr('checked', true);
                         $('#make-switch-columns').bootstrapSwitch();
                         $('.csvCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-grey',
                        });*/
                        UINestable.init();
                        $("#template_name").val("");
                        $("#csv_template").modal("show");
                    },
                    error: function (errorString)
                    {

                    }
                });
            
        });
        
        $("#service_id").change(function(){
            if($(this).val() != ''){
                $("#csv_temp_modal").show();
                getUserTempalte();
            }else{
                $("#csv_temp_modal").hide();
            }
        })  ;  
        
        
    });

     $(document).on("click", "#save_csv_temp", function(event) {
            // Put your code here.
            $("#selected_service_id").val($("#service_id").val());
            
            var frm_serialize = $("#frm_csv_temp").serialize();
//            var serilized_data = $('#nestable_list_1').nestable('serialize');
            var template_name = $('#template_name').val();
            if(template_name == ""){
                swal("","Please enter template name");
            }else{
                $.ajax({
                    type: "POST",
                    url: "mawb_account_report.php", // your php file name
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
	function getUserTempalte(){
                var service_id = $('#service_id').val();
                $.ajax({
                    type: "POST",
                    url: "mawb_account_report.php", // your php file name
                    dataType: 'json',
                    data: {func:'get_user_template',service_id:service_id},
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
		</script>
<?php
		
    }

}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
  