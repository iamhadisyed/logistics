<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");


include_classes([
    'carrier.class',
    'carrierfilter.class',
    'services.class' ,
    'servicefilter.class',
    'country.class',
    'countryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'quotationdetails.class',
    'quotationdetailsfilter.class']);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $quotationDetailsFilter = array();
    private $user = '';
    private $quotationId = 0;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Quotation List"
        );
        $this->user = SessionManager::getUser();

        if ($this->user->getUserType() == USER::USER_TYPE_CLIENT) {
            util_redirect("index.php");
        }
        if (isset($_GET['id']) && ($_GET['id'] > 0)) {
            $this->quotationId = $_GET['id'];
        }

        if (isset($_GET['action']) && $_GET['action'] == "get_quotation_list_ajax") {
            /*
             * Column filter
             * For search
             */
            $this->quotationDetailsFilter = new QuotationDetailsFilter();
            $this->quotationDetailsFilter->join("customer_account ua", ['qd.added_by' => 'ua.id']);
            $this->quotationDetailsFilter->where(['qd.added_by' => $this->user->getUserAccountId()]);

            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->applyFilter($this->form_vars);
            }
            if (isset($_GET['id']) && ($_GET['id'] > 0)) {
                $id = $_GET['id'];
                $this->quotationDetailsFilter->where(['qd.id' => $id]);
            }
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = "DESC";
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if ($dataTableColumnName == "date_created") {
                    $dataTableColumnName = "qd.date_created";
                }
                if ($dataTableColumnName == "account") {
                    $dataTableColumnName = "qd.account_id";
                }
                if ($dataTableColumnName == "carrier_id") {
                    $dataTableColumnName = "qd.carrier_id";
                }
                if ($dataTableColumnName == "service_id_filter") {
                    $dataTableColumnName = "qd.service_id";
                }
                if ($dataTableColumnName == "weight_filter") {
                    $dataTableColumnName = "qd.weight";
                }
                if ($dataTableColumnName == "pieces_filter") {
                    $dataTableColumnName = "qd.pieces";
                }
                if ($dataTableColumnName == "from_country") {
                    $dataTableColumnName = "qd.shipping_from";
                }
                if ($dataTableColumnName == "to_country") {
                    $dataTableColumnName = "qd.shipping_to";
                }
                if ($dataTableColumnName == "quotation_date") {
                    $dataTableColumnName = "qd.date_created";
                }
                if ($dataTableColumnName == "total_charge") {
                    $dataTableColumnName = "qd.total_charge";
                }
                $this->quotationDetailsFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                $this->quotationDetailsFilter->orderBy(strtolower('qd.id'), "DESC");
            }
            /*
             * Pagination Logic Implemented
             * 
             */

            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->quotationDetailsFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->quotationDetailsFilter->setOffset($iDisplayStart);
            $quotationDetailsFilterObjs = $this->quotationDetailsFilter->getList("qd.*");
            $iTotalRecords = $this->quotationDetailsFilter->getCount();
            $setDataArr = array();
            foreach ($quotationDetailsFilterObjs as $quotationDetailsFilterObj) {
                $currentArr = array();
                if (!empty($quotationDetailsFilterObj->getDateCreated())) {
                    $date_created = date("d-m-Y", $quotationDetailsFilterObj->getDateCreated());
                } else {
                    $date_created = "";
                }
                $userAccountObj = new CustomerAccount($quotationDetailsFilterObj->getAccountId());
                $currentArr['account'] = $userAccountObj->getUserAccount();
                $carrierObj = new Carrier($quotationDetailsFilterObj->getCarrierId());
                $currentArr['carrier_id'] = $carrierObj->getCarrier();
                $serviceObj = new Services($quotationDetailsFilterObj->getServiceId());
                $currentArr['service_id'] = $serviceObj->getName();
                $currentArr['weight'] = $quotationDetailsFilterObj->getWeight();
                $currentArr['pieces'] = $quotationDetailsFilterObj->getPieces();
                $fromCountryObj = new Country($quotationDetailsFilterObj->getShippingFrom());
                $currentArr['from_country'] = $fromCountryObj->getName();
                $toCountryObj = new Country($quotationDetailsFilterObj->getShippingTo());
                $currentArr['to_country'] = $toCountryObj->getName();
                $currentArr['quotation_date'] = $date_created;
                $currentArr['total_charge'] = $quotationDetailsFilterObj->getTotalCharge();
                $pdfUrl = "";
                if (!empty($quotationDetailsFilterObj->getPdf())) {
                    $pdfUrl = SETTING_URL . '_assets/quotation_files/' . $quotationDetailsFilterObj->getPdf();
                }

                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                                <ul class="dropdown-menu" >';
                if (!empty($pdfUrl)) {
                    $currentArr['actions'] .= '<li>
                                                        <a href="' . $pdfUrl . '" title="Download Pdf" target="_blank" >
                                                            <i class="fa fa-download"></i> Download Pdf
                                                        </a>
                                                    </li>';
                }
                $currentArr['actions'] .= '<li>
                                                <a href="javascript:;" title="Send Email" onclick="sendEmail('. $quotationDetailsFilterObj->getId() .')" >
                                                    <i class="fa fa-envelope"></i> Send Email
                                                </a>
                                            </li>';

                $currentArr['actions'] .= '<li>
                                 <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $quotationDetailsFilterObj->getId() . '" 
                                 data-log_name="quotation_details" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                                 </a>
                                 </li>';
                $currentArr['actions'] .= '</ul>
                                            </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'send_email') {
            $output = [];
            $quotationid = $this->form_vars['id'];
            $quotatonDetail = new QuotationDetails($quotationid);
            $currencyObj = new Currency($quotatonDetail->getCurrencyId());
            $calc_currency = $currencyObj->getLeftsymbol();
            $fromCountryObj = new Country($quotatonDetail->getShippingFrom());
            $calc_shipping_from = $fromCountryObj->getName();
            $fromCity = $quotatonDetail->getFromCity();
            $fromPostcode = $quotatonDetail->getFromPostcode();
            $toCountryObj = new Country($quotatonDetail->getShippingTo());
            $calc_shipping_to = $toCountryObj->getName();
            $toCity = $quotatonDetail->getToCity();
            $toPostcode = $quotatonDetail->getToPostcode();
            $carrierObj = new Carrier($quotatonDetail->getCarrierId());
            $service_type = $carrierObj->getCarrier();
            $userAccountObj = new CustomerAccount($quotatonDetail->getAccountId());
            $account = $userAccountObj->getUserAccount();
            $pieces = $quotatonDetail->getPieces();
            $calc_remark = $quotatonDetail->getRemark();
            $calc_weight = $quotatonDetail->getWeight();

            $calculate = unserialize($quotatonDetail->getDimensions());
            $pieces = count($calculate);
            $calc_vol = $quotatonDetail->getVolumnWeight();
            $conversion = $quotatonDetail->getConversionrate();
            $calc_length = 0;
            $calc_width = 0;
            $calc_height = 0;
            $piecesDetails = "length x width x height / Convesrion = Vol Weight \n";
            if(count($calculate) > 0) {
                foreach($calculate as $value) {
                    if(!empty($value['length'])) {
                        $calc_length += $value['length'];
                    }
                    if(!empty($value['width'])) {
                        $calc_width += $value['width'];
                    }
                    if(!empty($value['height'])) {
                        $calc_height += $value['height'];
                    }
                    $piecesDetails .= $value['length'] . ' x ' . $value['width'] . ' x ' . $value['height'] . ' / ' . $conversion . ' = ' . $value['vol_weight'] . " \n";
                }
            }

            $userEmail = $quotatonDetail->getUserEmail();
            $basicChage = $quotatonDetail->getBasicCharge();
            $vatCharge = $quotatonDetail->getVatCharge();
            $extraCharge = $quotatonDetail->getExtraCharge();
            $totalCharge = $quotatonDetail->getTotalCharge();


            $countriesList = new CountryFilter();
            $countriesList->addCountryCodeArrayFilter($calc_shipping_from . "','" . $calc_shipping_to);
            $countryListData = $countriesList->getColumnList('iso, name, iso3');
            $countrArray = array();
            if (count($countryListData) > 0) {
                foreach ($countryListData as $countryData) {
                    $countrArray[$countryData->getIso()] = $countryData->getName();
                }
            }
            if (trim($userEmail) == '') {
                $ufilter = new UserAccountFilter();
                $ufilter->addUserAccountFilter($account);
                $uList = $ufilter->getColumnList("user_account,email,alternative_email");
                if (count($uList) > 0) {
                    if ($uList[0]->getEmail() != "") {
                        $userEmail = $uList[0]->getEmail();
                    } else if ($uList[0]->getAlternativeEmail() != "") {
                        $userEmail = $uList[0]->getAlternativeEmail();
                    } else {
                        $output['status'] = "error";
                        $output['message'] = "No email address found to send alert.";
                    }
                } else {
                    $output['status'] = "error";
                    $output['message'] = "No email address found to send alert.";
                }
            }
            if(!isset($output['status']) && $output['status'] != "error") {;
                $htmlMessage = "Please find the details of the quotation as below:<br><br>";
                $htmlMessage .= "<table width='100%' border='0'>";
                $htmlMessage .= "<tr>";
                $f = '';
                if($fromCity != "" ) {
                    $f .= $fromCity;
                }
                if($calc_shipping_from != "" && $f != "") {
                    $f .= ', '.$calc_shipping_from;
                } else if($calc_shipping_from != ""){
                    $f .= $calc_shipping_from;
                }
                if($fromPostcode != "" && $f != "") {
                    $f .= ', '.$fromPostcode;
                } else if($fromPostcode != ""){
                    $f .= $fromPostcode;
                }
                $htmlMessage .= "<td><b>From : </b>".$f."</td>";
                $t = '';
                if($toCity != "") {
                    $t .= $toCity;
                }
                if($calc_shipping_to != "" && $t != "") {
                    $t .= ', '.$calc_shipping_to;
                } else if($calc_shipping_to != ""){
                    $t .= $calc_shipping_to;
                }
                if($toPostcode != "" && $t != "") {
                    $t .= ', '.$toPostcode;
                } else if($toPostcode != "") {
                    $t .= $toPostcode;
                }
                $htmlMessage .= "<td><b>To : </b>".$t."</td>";
                $htmlMessage .= "</tr>";
                $htmlMessage .= "<tr>";
                $htmlMessage .= "<td colspan='2'><b>Weight : </b>".$calc_weight."</td>";
                $htmlMessage .= "</tr>";
                $htmlMessage .= "<tr>";
                $htmlMessage .= "<td colspan='2'><b>Number of Pieces : </b>".$pieces."</td>";
                $htmlMessage .= "</tr>";
                $htmlMessage .= "<tr>";
                $htmlMessage .= "<td colspan='2'><b>Service : </b>".$service_type."</td>";
                $htmlMessage .= "</tr>";
                $htmlMessage .= "</table>";

                $htmlMessage .= "<br />";

                $htmlMessage .= "<table width='100%' border='0'>";
                $htmlMessage .= "<tr>";
                $htmlMessage .= "<td><b>Total Price Quoted : </b>".$totalCharge." ".$currencyObj->getRightsymbol()."</td>";
                $htmlMessage .= "</tr>";
                $htmlMessage .= "</table>";


                $emailPart = explode('@', $userEmail);
                $to = $userEmail;
                $quoteNo = 'OWE'.str_pad($quotationid, 6, '0', STR_PAD_LEFT);
                $subject = 'Quotaton of ' . $quoteNo;

                $htmlMessage .= "<br>";
                $key = md5($quotationid);
                $link = BASE_URL."quotation_pay.php?key=".$key;
                $htmlMessage .= "Please click the button below, to pay.  <br /><br />";
                $htmlMessage .= "<a href='".$link."' style='color:#fff;background-color:#36c6d3;border-color:#2bb8c4;text-decoration: none;padding: 10px;' >Pay Now</a> <br /><br />";
                $team = (($userAccountObj->getCompany() != '') ? $userAccountObj->getCompany() : 'One World Express.');
//                $htmlMessage .= "<br><br><strong>Thanks For using our services.<br>Team<br> ".$team." </strong>.";

                $imageLogo = "";
                $logoImage = $userAccountObj->getLogo();
                if (trim($logoImage) != '' && trim($logoImage) != 'inner-logo.png') {
                    $imageLogo = BASE_URL.'images/userlogo/' . $logoImage;
                    if(!file_exists($imageLogo)) {
                        $imageLogo = BASE_URL.'images/inner-logo.png';
                    }
                } else {
                    $imageLogo = BASE_URL.'images/inner-logo.png';
                }
                $company = $userAccountObj->getCompany();
                $dt = [
                    'logo' => $imageLogo,
                    'name' => 'Customer,',
                    'message' => $htmlMessage,
                    'company' => $company
                ];
                $emailBody = file_get_contents ('quotation_new_shipment_email_template.php');// read in the template file from above
                foreach ($dt as $key => $value){
                    $emailBody = str_replace ("[$key]", $value, $emailBody);
                }
                $headers = 'From: Smart Track <smart@smarttrack.co>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1';
                if (mail($to, $subject, $emailBody, $headers)) {
                    $output['status'] = "success";
                    $output['message'] = "You have successfully sent an email.";
                } else {
                    $output['status'] = "error";
                    $output['message'] = "There is some technicall issue. Please contact to itsupport@oneworldexpress.com.";
                }
            }
            echo json_encode($output);
            die;
        }

    }

    protected function applyFilter($data) {

        $this->form_vars = $data;

        $date_created_from = $this->form_vars['date_created_from'];
        $date_created_to = $this->form_vars['date_created_to'];
        if (!empty($date_created_from) && !empty($date_created_to)) {
            $this->quotationDetailsFilter->whereBetween('qd.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));
        }
        $filterArray = [];
        $accountId = $this->form_vars['user_account_id_filter'];
        if (!empty($accountId))
            $filterArray['qd.account_id'] = $accountId;

        $carrierId = $this->form_vars['carrier_id'];
        if (!empty($carrierId))
            $filterArray['qd.carrier_id'] = $carrierId;

        $serviceId = $this->form_vars['service_id_filter'];
        if (!empty($serviceId))
            $filterArray['qd.service_id'] = $serviceId;

        $weight = $this->form_vars['weight_filter'];
        if (!empty($weight)) {
            $this->quotationDetailsFilter->whereBetween('qd.weight', $weight, $weight);
        }

        $pieces = $this->form_vars['pieces_filter'];
        if (!empty($pieces))
            $filterArray['qd.pieces'] = $pieces;

        $fromCountry = $this->form_vars['from_country_filter'];
        if (!empty($fromCountry))
            $filterArray['qd.shipping_from'] = $fromCountry;

        $toCountry = $this->form_vars['to_country_filter'];
        if (!empty($toCountry))
            $filterArray['qd.shipping_to'] = $toCountry;

        $totalCharge = $this->form_vars['total_charge'];
        if (!empty($totalCharge))
            $filterArray['qd.total_charge'] = $totalCharge;

        $this->quotationDetailsFilter->where($filterArray);
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            $(".table-container .custom-alerts").hide();
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error  
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options 
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here 
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "quotation_list.php?action=get_quotation_list_ajax<?php echo "&id=" . $this->quotationId ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "account"},
                                {"data": "carrier_id"},
                                {"data": "service_id"},
                                {"data": "weight"},
                                {"data": "pieces"},
                                {"data": "from_country"},
                                {"data": "to_country"},
                                {"data": "total_charge"},
                                {"data": "quotation_date"}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            function sendEmail(id) {
                $.ajax({
                    type: "POST",
                    url: "quotation_list.php",
                    data: {'action': "send_email",'id': id},
                    dataType: "json",
                    success: function (response) {
                        if(response.status == "success") {
                            swal("Success!", response.message, "success");
                        } else {
                            swal("Sorry!", response.message, "error");
                        }
                    },
                    error: function () {
                        //alert('error handing here');
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
                <div class="caption"> <i class="fa fa-list"></i>
                    Quotation List
                </div>
                <div class="actions">
                    <a href="quotation_add.php" class="btn btn-sm btn-success" id="add_quotation_btn" data-original-title="Add Quotation" title="Add Quotation"><span></span><i class="fa fa-plus"></i> Add Quotation</a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                    </div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Account</th>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>Weight</th>
                            <th>Pieces</th>
                            <th>From Country</th>
                            <th>To Country</th>
                            <th>Total Price</th>
                            <th>Quotation Date</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                $accountParentId = 0;
                                $includeParent = true;
                                if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                    $accountParentId = $this->user->getUserAccountId();
                                    $includeParent = false;
                                }
                                $selectedAccount = "";
                                $allowedLevel = 0;
                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                    $allowedLevel = 1;
                                }
                                ?>
                                <?php echo Ddl::showTreeDropdown('user_account_id_filter', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                $slectedCarrierId = '';
                                ?>
                                <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $slectedCarrierId, 'id', ' class="bs-select form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                $slectedServiceId = '';
                                ?>
                                <?php echo Ddl::generateServiceDDLWithImage('service_id_filter', $slectedServiceId, 'id', ' class="bs-select form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="weight_filter" id ="weight_filter" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="pieces_filter" id ="pieces_filter" />
                            </td>
                            <td  class="user_acccount_correct_button">
                                <?php echo Ddl::generateCountryDDL('from_country_filter', '', 'id'); ?>
                            </td>
                            <td  class="user_acccount_correct_button">
                                <?php echo Ddl::generateCountryDDL('to_country_filter', '', 'id'); ?>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="total_charge" id ="total_charge" />
                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="tracking_number_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Tracking Numbers</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <tr>
                                    <th> Master Number </th>
                                    <td> <span id="master_number_show"></span> </td>
                                    <th> Bag Number </th>
                                    <td> <span id="bag_number_show"></span> </td>
                                </tr>
                            </table>
                        </div>
                        <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th> Sr. </th>
                                    <th> Tracking Number </th>
                                    <th> Sender </th>
                                    <th> Status </th>
                                </tr>
                                </thead>
                                <tbody id="tracking_number_detail">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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
            #manage-data-table tbody tr td:nth-child(8),#manage-data-table tbody tr td:nth-child(9),#manage-data-table tbody tr td:nth-child(10){
                text-align: center;
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
