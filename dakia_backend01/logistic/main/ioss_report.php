<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'userservicesrouting.class',
                    'userservicesroutingfilter.class',
                    'country.class',
                    'countryfilter.class']);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $carrierId = '';
    private $serviceId = '';
    private $serAccountId = '';
    private $noRecordFound = '';
    private $recordFound = '';

    protected function init() {

         $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'ioss_report.php' => "IOSS VAT Report"
        );
        $this->user = SessionManager::getUser();

        /*         * DataTable handlings
         */
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "download_excel_report") {
            $countryId = $this->form_vars['country']; 
            $dateType = $this->form_vars['date_type'];
            $fromDate = (!empty($this->form_vars['from_date']) ? date('Y-m-d', strtotime($this->form_vars['from_date'])) : date('Y-m-d', strtotime('-30 days')));
            $toDate = (!empty($this->form_vars['to_date']) ? date('Y-m-d', strtotime($this->form_vars['to_date'])) : date('Y-m-d'));
            $dateFrom = date_create($fromDate);
            $dateTo = date_create($toDate);
            $diffLabelReport = date_diff($dateFrom, $dateTo);
            
               $allowedAccounts = [];
                $selectedAccountName ="";
                $user_account_id = (empty($this->form_vars['user_account_id']) ? $this->user->getUserAccountId() : $this->form_vars['user_account_id']);
                
                $selectAccountSearchType = $this->form_vars['selectAccountSearchType'];
                if ($this->user->getUserType() == USER::USER_TYPE_CLIENT) {
                    $selectAccountSearchType = "own";
                } else {
                    $selectAccountSearchType = $this->form_vars['selectAccountSearchType'];
                }
                if (!empty($user_account_id)) {
                    if ($selectAccountSearchType == 'all') {
                        $allowedAccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                    } else if ($selectAccountSearchType == 'own') {
                        $allowedAccounts[] = $user_account_id;
                    } else if ($selectAccountSearchType == 'subaccount') {
                        $allowedAccounts = CustomerAccount::accountSubAccount($user_account_id, 0, false);
                    }
                    $userAccountObj         = new CustomerAccount($user_account_id);
                    $selectedAccountName    = $userAccountObj->getUserAccount();
                }
                
                $consignmentFilterObj = new ConsignmentFilter();
                $consignmentFilterObj->addJoin('parcel p','p.consignment_id = c.id', "INNER");
                $consignmentFilterObj->addJoin('user u','u.id = c.user_id', "INNER");
                $consignmentFilterObj->addJoin('customer_account ua','ua.id = u.user_account_id', "INNER");
                $consignmentFilterObj->addJoin('country coun','coun.id = c.country_id', "INNER");
                $consignmentFilterObj->addJoin('services s','s.id = c.service_id', "INNER");
                
                if(!empty($countryId))
                {
                   $where .= "  AND c.country_id ='".$countryId."'";    
                }
                
                
                if(count($allowedAccounts) > 0)
                {
                    $where .=" AND u.`user_account_id` IN ('".implode("','", $allowedAccounts)."')";
                }
                
                if(!empty($dateType) && $dateType == 'dateLabelCreated')
                {
                    $where .= "  AND
                    c.`date_label_created` != '' AND c.`date_label_created` > 0 AND c.`label_file` != ''
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_label_created),
                      '%Y-%m-%d'
                    ) >= '".$fromDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_label_created),
                      '%Y-%m-%d') <= '".$toDate."' ";
                }
                
                else if(!empty($dateType) && $dateType == 'dateDelivered') 
                {
                    $where .= "  AND
                    c.`date_delivered` != '' AND c.`date_delivered` > 0
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_delivered),
                      '%Y-%m-%d'
                    ) >= '".$fromDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_delivered),
                      '%Y-%m-%d'
                    ) <= '".$toDate."' ";
                }
                
                else if(!empty($dateType) && $dateType == 'dateBooked') 
                {
                    $where .= "  AND
                    c.`date_booked` != '' AND c.`date_booked` > 0
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_booked),
                      '%Y-%m-%d'
                    ) >= '".$fromDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_booked),
                      '%Y-%m-%d'
                    ) <= '".$toDate."' ";
                }
                
                else if(!empty($dateType) && $dateType == 'dateScanned') 
                {
                    $where .= "  AND
                    DATE(c.date_scanned)  >= '".$fromDate."'  AND DATE(c.date_scanned) <= '".$toDate."'"; 
                }
                
                $where .= "  AND shipment_status not in ('22','11') AND ioss_number !=''";
                $consignmentFilterObj->addFilterNew($where);
                $iossResult = $consignmentFilterObj->getListNew("ua.user_account,c.hawb,c.awb,s.code as service_code,s.name as service_name,
                          DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),'%Y-%m-%d') as date_label_created,
                          DATE_FORMAT(FROM_UNIXTIME(c.date_booked),'%Y-%m-%d') as date_booked,
                          DATE_FORMAT(FROM_UNIXTIME(c.date_delivered),'%Y-%m-%d') as date_delivered,
                          c.sender_name,c.company,c.contact,c.address_line_1,c.city,
                          coun.name as country_name,c.postcode,c.telephone,c.description,c.number_pieces,c.charge_weight,c.weight,c.vol_weight,c.value,c.currency,
                          p.length,p.width,p.height,c.ioss_number,coun.vat_rate as vat_rate",'','');
            
            if ($diffLabelReport->days <= '30') 
            {
                $IOSSDataCsv  = '';
                
                $exportHeader = [
                        'Account',
                        "Order Reference",
                        "Tracking Number",
                        "Service Code",
                        "Service Name",
                        "Product Service",
                        "Date Label Created",
                        "Date Dispatched",
                        "Date Delivered",
                        "True Shipper",
                        "Company",
                        "Contact Name",
                        "Address",
                        "City",
                        "Country",
                        "Postcode",
                        "Telephone",
                        "Description",
                        "Number Pieces",
                        "Chargeable Weight",
                        "Weight",
                        "Vol Weight",
                        "Value",
                        "Currency",
                        "Length",
                        "Width",
                        "Height",
                        "IOSS Number",
                        "Value EUR",
                        "IOSS VAT Rate",
                        "VAT Payable EUR"
                ];
                
                if(count($iossResult)>0)
                {
                    $IOSSDataCsv  = implode(',',$exportHeader)."\r\n";
                    foreach ($iossResult as $iossObj) {
                        if(strtolower($iossObj->getCurrency()) == 'eur'){
                            $conValue = $iossObj->getValue();
                        }else{
                            $conValue = Currency::convertCurrency($iossObj->getCurrency(),"EUR",$iossObj->getValue());
                        }
                        if($conValue <= 150){
                        $consignmentArr = [
                            cleanCsvCall($iossObj->getUserAccount()),
                            cleanCsvCall($iossObj->getHawb(),'int'),
                            cleanCsvCall($iossObj->getAwb(), 'int'),
                            cleanCsvCall($iossObj->getServiceCode()),
                            cleanCsvCall($iossObj->getServiceName()),
                            cleanCsvCall(''),
                            cleanCsvCall($iossObj->getDateLabelCreated()),
                            cleanCsvCall($iossObj->getDateBooked()),
                            cleanCsvCall($iossObj->getDateDelivered()),
                            cleanCsvCall($iossObj->getSenderName()),
                            cleanCsvCall($iossObj->getCompany()),
                            cleanCsvCall($iossObj->getContact()),
                            cleanCsvCall($iossObj->getAddressLine1()." ".$iossObj->getAddressLine2()." ".$iossObj->getAddressLine3()),
                            cleanCsvCall($iossObj->getCity()),
                            cleanCsvCall($iossObj->getCountryName()),
                            cleanCsvCall($iossObj->getPostcode()),
                            cleanCsvCall($iossObj->getTelephone()),
                            cleanCsvCall($iossObj->getDescription()),
                            cleanCsvCall($iossObj->getNumberPieces()),
                            cleanCsvCall($iossObj->getchargeWeight()),
                            cleanCsvCall($iossObj->getWeight()),
                            cleanCsvCall($iossObj->getVolWeight()),
                            cleanCsvCall($iossObj->getValue()),
                            cleanCsvCall($iossObj->getCurrency()),
                            cleanCsvCall($iossObj->getLength()),
                            cleanCsvCall($iossObj->getWidth()),
                            cleanCsvCall($iossObj->getHeight()),
                            cleanCsvCall($iossObj->getIOSSNumber()),
                            cleanCsvCall($conValue),
                            cleanCsvCall($iossObj->getVatRate() * 100) . "%",
                            number_format(($conValue * $iossObj->getVatRate()), 2)
                            ];
                        $IOSSDataCsv  .= implode(',',$consignmentArr)."\r\n";
                        }
                    }                
                }
                else {
                    $this->noRecordFound = 'No record found to download';
                }
                if(trim($IOSSDataCsv) != ''){
                    $DOWNLOADABLE_FILE_NAME = "../_assets/csv/ioss-vat-report-" . time() . ".csv";
                    file_put_contents($DOWNLOADABLE_FILE_NAME, $IOSSDataCsv);
                    $this->recordFound = 'Please <a href="'.$DOWNLOADABLE_FILE_NAME.'" target="_blank" class="btn btn-xs btn-primary">click here</a> to download report.';                    
                }
            }
            else {
                $this->noRecordFound = 'Date ranges filter must be less than and euqal to 30 days. ';
            }
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

        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>


        <script type="text/javascript">
            $(document).ready(function () {
                //DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        endDate: 'today',
                        maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });

                }

                setTimeout(function () {
                    $('.msg').remove();
                }, 20000);
                
                $('.download_file_btn').click(function () {                   
                    $('#ioss_report').submit();
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    IOSS VAT Report
                </div>
            </div>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <?php if (!empty($this->noRecordFound)) { ?>
                    <div class="col-md-12 alert alert-danger msg">
                        <?php echo $this->noRecordFound; ?>
                    </div>
                <?php } 
                else
                    if(!empty($this->recordFound)) {?>
                    <div class="col-md-12 alert alert-success msg">
                        <?php echo $this->recordFound; ?>
                    </div>
                <?php } 
                ?>
                <div class="portlet-body">
                    <form action="ioss_report.php" id="ioss_report" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="download_excel_report" >
                        <div class="row">
                            <?php
                            $colSpan = '6';
                            ?>
                                <div class="col-md-<?php echo $colSpan; ?>">
                                    
                                    <div class="form-group ">
                                        <div class="has-float-label input-icon right">   
                                        <?php
                                        $showShipments = array(
                                            'all' => "Selected Account & Sub Accounts",
                                            'own' => "Selected Account",
                                            'subaccount' => "Sub Accounts",
                                        );
                                        echo Ddl::generateArrayDDL('selectAccountSearchType', $showShipments, '', '', 'class="form-filter select2 form-control" ', "", 'selectAccountSearchType');
                                        ?>
                                        <label class="label-account">Show Shipment </label>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-<?php echo $colSpan; ?>">
                                    
                                    <div class="form-group ">
                                        <div class="has-float-label input-icon right">
                                        
                                        <div id="user_content">
                                            <?php
                                            $accountParentId = 0;
                                            $includeParent = true;
                                            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                                $accountParentId = $this->user->getUserAccountId();
                                                $includeParent = false;
                                            }
                                            $selectedAccount = $this->user->getUserAccountId();
                                            $allowedLevel = 0;
                                            if (Permissions::checkFilePermission('hide_subaccount')) {
                                                $allowedLevel = 1;
                                            }
                                            ?>
                                            <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', true, $allowedLevel); ?><label class="label-account">Select Account</label>
                                        </div>
                                         </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-<?php echo $colSpan; ?>">
                              
                                <div class="form-group ">
                                    <div class="has-float-label">
                                    <div class="input-group margin-bottom-5"><?php echo Ddl::generateDDLFromSql('select * from country where region = "R1"','country', 'name', 'id', '', 'class="form-filter select2 form-control"','Please Select Country','','','',''); ?></div>
                                    <label class="label-account">Select Country</label>
                                </div>
                                </div>
                            </div>                                            
                        </div>
                        <div class="row">
                            
                            <div class="col-md-<?php echo $colSpan; ?>">
                              
                                <div class="form-group ">
                                    <div class="has-float-label">
                                    
                                    <select id="date_type" name="date_type" class="form-filter bs-select form-control" title="" placeholder="" tabindex="-1" aria-hidden="true" data-original-title="">
                                        <option value="dateCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateCreated' ? 'selected="selected"' : '') ?>>Date Created</option>
                                        <option value="dateLabelCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateLabelCreated' ? 'selected="selected"' : '') ?>>Date label Created</option>
                                        <option value="dateBooked" <?= (!empty($this->dateType) && $this->dateType == 'dateBooked' ? 'selected="selected"' : '') ?>>Date Dispatched</option>
                                        <option value="dateDelivered" <?= (!empty($this->dateType) && $this->dateType == 'dateDelivered' ? 'selected="selected"' : '') ?>>Date Delivered</option>
                                        <option value="dateScanned" <?= (!empty($this->dateType) && $this->dateType == 'dateScanned' ? 'selected="selected"' : '') ?>>Date Scanned</option>
                                    </select>
                                      <label class="label-account">Date Type </label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <div class="form-group ">
                                     <div class="has-float-label input-icon right">

                              
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?php echo (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" >
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?php echo (!empty($_POST['to_date']) && is_integer((int)$_POST['to_date'])) ? (int)$_POST['to_date'] : ''; ?><?php echo (!empty($_POST['to_date']) && strtotime($_POST['to_date'] == true)) ? $_POST['to_date'] : ''; ?>">
                                </div>   <label class="control-label" data-toggle="tooltip">Date Ranges </label>
                            </div>
                             </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label">&nbsp;</label><br>
                                <button type="submit" class="btn blue download_file_btn"><i class="fa fa-download"></i> Download Excel File</button>
                            </div>
                        </div>
                    </form>
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
        <style>
            #select2-service_id-results .select2-results__option[aria-disabled=true] {
                display: none;
            }
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