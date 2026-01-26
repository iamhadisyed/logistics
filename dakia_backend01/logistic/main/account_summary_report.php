<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'country.class',
                    'countryfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class']);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $userAccountId = '';
    private $getUserAccountId = '';
    private $userAllowedAccount = array();
    private $accountName = '';
    private $subAccountName = '';
    private $ownShipment = 0;
    private $ownAccountShipment = 0;
    private $totalShipment = 0;
    private $subDataAccount = 0;
    private $noReocrdFound = 0;

    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Account Summary Report"
        );
        $this->user = SessionManager::getUser();
        if(!empty($_POST['user_account_id'])){
            $user_account_id = $_POST['user_account_id'];
        }else{
            $user_account_id = $this->user->getUserAccountId();
        }
        
        $userFilterSubObj = new UserAccountFilter();
        $userFilterSubObj->addFieldFilter('parentid', $user_account_id);
        $dataSubAccount = $userFilterSubObj->getList();
        
         if(!empty($_POST['user_account_id'])){
            $this->subDataAccount = $dataSubAccount;
        }else{
            $userFilterSubObj = new UserAccountFilter();
            $userFilterSubObj->addFieldFilter('id', $this->user->getUserAccountId());
            $dataParentAccount = $userFilterSubObj->getList();
            $this->subDataAccount = (object) array_merge((array)$dataParentAccount, (array)  $dataSubAccount);
        }
        $subAccountArr = [];
        if(!empty($dataSubAccount)){
            foreach($dataSubAccount as $subAccountData){
                $subAccountArr[] = $subAccountData->getId();
            }
        }
       
        if(!empty($_POST['func']) && $_POST['func'] == 'func'){
            $subAccount = (!empty($_POST["subAccount"])?$_POST['subAccount']:'0');
            $userFilterSubObj = new UserAccountFilter();
            $userFilterSubObj->addFieldFilter('parentid', $subAccount);
            $dataSubAccount = $userFilterSubObj->getList();
            $this->subDataAccount = $dataSubAccount;
            $toDate = ((!empty($_POST['toDate']))?$_POST['toDate']:'');
            $fromDate = ((!empty($_POST['fromDate']))?$_POST['fromDate']:'');
            $dateFilter = '';
            if(!empty($_POST['toDate']) && !empty($_POST['fromDate'])){
                $dateFilter = "AND  DATE(c.`date_created`) >= '".date("Y-m-d",strtotime($fromDate))."' AND DATE(c.`date_created`) <=  '".date("Y-m-d",strtotime($toDate))."'";
            }
            $subAccountArr = [];
            $html ="";
            $totalCount =0;
            if(!empty($dataSubAccount)){
                $html .= "<thead>
                            <tr>
                                <th>Account Name </th>
                                <th>Shipment </th>
                            </tr>
                        </thead>
                        <tbody>";
                foreach($dataSubAccount as $subAccountData){
                    $subAccountShipment = ConsignmentFilter::getConsignmentSummaryReport($subAccountData->getId(), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                    $html .="<tr>"
                            ."<td>".$subAccountData->getUserAccount()."</td>"
                            ."<td>".$subAccountShipment[0]->getId()."</td>"
                            . "</tr>";
                    $totalCount += $subAccountShipment[0]->getId();
                }
                $html .="<tr>"
                            ."<th>Total Shipments</th>"
                            ."<td>".$totalCount."</td>"
                            . "</tr>";
                $html .="</tbody>";
                
            }
            if(!empty($html)){
                echo $html;
                exit;
            }else{
                $html .="<tr>"
                            ."<td colspan='2'>No Record Found.</td>"
                            . "</tr>";
                echo $html;
                exit;
            }
        }

        if(!empty($_POST['func']) && $_POST['func'] == 'func_country'){
            $own_shipment = [];
            $own_shipment[] = (!empty($_POST["own_shipment"])?$_POST['own_shipment']:'0');
            $html ="";
            $toDate = ((!empty($_POST['toDate']))?date("Y-m-d",  strtotime($_POST['toDate'])):'');
            $fromDate = ((!empty($_POST['fromDate']))?date("Y-m-d",  strtotime($_POST['fromDate'])):'');
            $own_shipment  = implode(",",$own_shipment);
            $dateFilter = '';
            if(!empty($toDate) && !empty($fromDate)){
                $dateFilter = "AND  DATE(c.`date_created`) >= '".date("Y-m-d",strtotime($fromDate))."' AND DATE(c.`date_created`) <=  '".date("Y-m-d",strtotime($toDate))."'";
            }
            $countryData = CountryFilter::getAccountSummaryCountryReport($own_shipment,$dateFilter);
            if(!empty($countryData)){
                $html .= "<thead>
                            <tr>
                                <th>Country Name </th>
                                <th>Shipments </th>
                            </tr>
                        </thead>
                        <tbody>";
                $totalCount = 0;
                foreach($countryData as $dataCountry){
                    $html .="<tr>"
                            ."<td>".$dataCountry->getName()."</td>"
                            ."<td>".$dataCountry->getId()."</td>"
                            . "</tr>";
                    $totalCount +=$dataCountry->getId();
                }
                $html .="<tr>"
                            ."<th>Total Shipments</th>"
                            ."<td>".$totalCount."</td>"
                            . "</tr>";
                $html .="</tbody>";
                
            }
            if(!empty($html)){
                echo $html;
                exit;
            }else{
                $html .="<tr>"
                            ."<td colspan='2'>No Record Found.</td>"
                            . "</tr>";
                echo $html;
                exit;
            }
        }
        
        
        if(!empty($_POST['func']) && $_POST['func'] == 'func_service'){
            $own_shipment = [];
            $own_shipment[] = (!empty($_POST["own_shipment"])?$_POST['own_shipment']:'0');
            $html ="";
            $own_shipment  = implode(",",$own_shipment);
            $toDate = ((!empty($_POST['toDate']))?date("Y-m-d",  strtotime($_POST['toDate'])):'');
            $fromDate = ((!empty($_POST['fromDate']))?date("Y-m-d",  strtotime($_POST['fromDate'])):'');
            $dateFilter = '';
            if(!empty($toDate) && !empty($fromDate)){
                $dateFilter = "AND  DATE(c.`date_created`) >= '".date("Y-m-d",strtotime($fromDate))."' AND DATE(c.`date_created`) <=  '".date("Y-m-d",strtotime($toDate))."'";
            }
            $serviceData = CountryFilter::getAccountSummarySerivceReport($own_shipment,$dateFilter);
            if(!empty($serviceData)){
                $html .= "<thead>
                            <tr>
                                <th>Service Name </th>
                                <th>Shipments </th>
                            </tr>
                        </thead>
                        <tbody>";
                $totalCount = 0;
                foreach($serviceData as $dataCountry){
                    $html .="<tr>"
                            ."<td>".$dataCountry->getName()."</td>"
                            ."<td>".$dataCountry->getId()."</td>"
                            . "</tr>";
                    $totalCount +=$dataCountry->getId();
                }
                $html .="<tr>"
                            ."<th>Total Shipments</th>"
                            ."<td>".$totalCount."</td>"
                            . "</tr>";
                $html .="</tbody>";
                
            }
            if(!empty($html)){
                echo $html;
                exit;
            }else{
                $html .="<tr>"
                            ."<td colspan='2'>No Record Found.</td>"
                            . "</tr>";
                echo $html;
                exit;
            }
        }
        
        if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];            
                if(!empty($this->subDataAccount)){
                    $objPHPExcel = new PHPExcel();
                    $objDataSheet = $objPHPExcel->getActiveSheet();
                    $objDataSheet->setTitle('Account Summary Report');

                    $headingArray = array(
                        'font' => array(
                            'bold' => true,
                            'size' => 20,
                            'name' => 'Calibri',
                        )
                    );                
                     $styleForReport = array(
                        'font' => array(
                            'bold' => true,
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                        'borders' => array(
                            'top' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ),
                            'bottom' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
                        )
                     ); 
                     $centCss = array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        )
                     ); 
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($styleForReport);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Account Summary Report");
                    $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Account");
                    $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Sub Account");
                    $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Own Shipment");
                    $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Sub Account Shipment");
                    $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Total Shipment");
                    
                    $dateFilter = '';
                    $from  = '';
                    $to = '';
                    if(!empty($_POST['from_date']) && !empty($_POST['to_date'])){
                        $from = $_POST['from_date'];
                        $to = $_POST['to_date'];
                        $dateFilter = "AND  DATE(c.`date_created`) >= '".date("Y-m-d",strtotime($from))."' AND DATE(c.`date_created`) <=  '".date("Y-m-d",strtotime($to))."'";
                    }
                    if(!empty($this->subDataAccount)){
                        $ownShipmentCounting = 0;
                        $subAccountShipmentCounting = 0;
                        $totalShipmentCounting = 0;
                        $totalOwn = 0;
                        $totalSubAcct = 0;
                        $totalShip = 0;
                        $count = 3;
                        $accountIds = [];
                        foreach($this->subDataAccount as $dataAccount){
                            $objPHPExcel->getActiveSheet()->getStyle('B'.$count.':E'.$count)->applyFromArray($centCss);
                            $userFilterSubObj = new UserAccountFilter();
                            $userFilterSubObj->addFieldFilter('parentid',$dataAccount->getId());
                            $dataSubAccount = $userFilterSubObj->getList();
                            $accountIds[$dataAccount->getId()]= $dataAccount->getUserAccount();
                            $ownShipment = ConsignmentFilter::getConsignmentSummaryReport($dataAccount->getId(), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                            $dataSubAccountShipment = CustomerAccount::accountSubAccount($dataAccount->getId(), 0, false);
                            $subAccountShipment = ConsignmentFilter::getConsignmentSummaryReport(implode(',',$dataSubAccountShipment), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                            $totalShipment = CustomerAccount::accountSubAccount($dataAccount->getId(), 0, true);
                            $accountTotalShipment = ConsignmentFilter::getConsignmentSummaryReport(implode(',',$totalShipment), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $dataAccount->getUserAccount());
                            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, count($dataSubAccount));
                            $ownShipmentExce = (!empty($ownShipment)?$ownShipment[0]->getId():'0');
                            $subAccountShipmentExcel = (!empty($subAccountShipment)?$subAccountShipment[0]->getId():'0');
                            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $ownShipmentExce);
                            $totalOwn += $ownShipmentExce;
                            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $subAccountShipmentExcel);
                            $totalSubAcct += $subAccountShipmentExcel;
                            $accountTotalExcel =(!empty($accountTotalShipment)?$accountTotalShipment[0]->getId():'0');
                            $totalShip += $accountTotalExcel;
                            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $accountTotalExcel);
                            $count++;
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$count.':E'.$count)->applyFromArray($centCss);
                        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, 'Grand Total');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count,number_format($totalOwn,2,'.',''));
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, number_format($totalSubAcct,2,'.',''));
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count,  number_format($totalShip,2,'.',''));
                    }
                    
                    if(!empty($accountIds)){
                        $index = 0;
                        $activeSheet = 0;
                        foreach($accountIds as $key => $data){
                            $count = 3;
                            $objWorkSheet = $objPHPExcel->createSheet($index); //Setting index when creating
                              //Write cells
                            $objWorkSheet->SetCellValue('A1', "Account Summary Report");
                            $objWorkSheet->SetCellValue('A2', 'Account');
                            $objWorkSheet->SetCellValue('B2', 'Service');
                            $objWorkSheet->SetCellValue('C2', 'Total Shipment');
                            $objWorkSheet->getStyle('A1')->applyFromArray($headingArray);
                            //$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
                            $objWorkSheet->getStyle('B2:C2')->applyFromArray($centCss);
                            $objWorkSheet->setTitle($data);
                            $serviceData = CountryFilter::getAccountSummarySerivceExcel($key,$dateFilter);
                            if(!empty($serviceData)){
                                $TotalSub = 0;
                                foreach($serviceData as $data){
                                    $objWorkSheet->getColumnDimension('A')->setWidth(22);
                                    $objWorkSheet->getColumnDimension('B')->setWidth(28);
                                    $objWorkSheet->getColumnDimension('C')->setWidth(22);
                                    //$objWorkSheet->getStyle('A'.$count.':C'.$count)->applyFromArray($centCss);
                                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
                                    $objWorkSheet->setCellValue('A'.$count, $data->getIso())
                                         ->setCellValue('B'.$count, $data->getName())
                                         ->setCellValue('C'.$count, $data->getId());
                                    $count++;
                                    $TotalSub += $data->getId();
                                }
                                $objWorkSheet->setCellValue('B'.$count,"Total");
                                $objWorkSheet->setCellValue('C'.$count,$TotalSub);
                            }

                            $index++;
                        }
                    }
                    $objPHPExcel->setActiveSheetIndex($index);
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=account-summary-report-' . time() . '.xls'); // file name of excel
                    header('Cache-Control: max-age=0');
                    header('Cache-Control: max-age=1');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Cache-Control: cache, must-revalidate');
                    header('Pragma: public'); // HTTP/1.0
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->setIncludeCharts(TRUE);
                    $objWriter->save('php://output');
                    exit;
                }else{
                    $this->noReocrdFound = 1;
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

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

        <script type="text/javascript">
            var grid = null;
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                
                // $('#manage-data-table button.filter-submit').click();
                $('.total_sub_account').click(function () {
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    var subAccount = $(this).data("sub_account");
                    var name  =$(this).data("name_account");
                    $.post("account_summary_report.php",{func:'func',subAccount:subAccount,toDate:toDate,fromDate:fromDate},function(data){
                        $('#subAccountDetail').html(data);
                        $('#modal_title_account').html(name+" Sub Account List");
                        $("#account_name_modal").modal('show');
                    });
                });
                $('.report_hide').hide();
                $('.report_hide_sub_account').hide();
                $('.report_hide_total_account').hide();
                $(document).on('click','.own_ship',function () {
                   $( ".report_hide" ).toggle();
                   $( ".report_hide_total_account" ).hide();
                   $( ".report_hide_sub_account" ).hide();
                });
                $(document).on('click','.total_account_ship',function () {
                   $( ".report_hide_total_account" ).toggle();
                   $( ".report_hide_sub_account" ).hide();
                   $( ".report_hide" ).hide();
                });
                $(document).on('click','.sub_account_ship',function () {
                   $( ".report_hide_sub_account" ).toggle();
                   $( ".report_hide_total_account" ).hide();
                   $( ".report_hide" ).hide();
                });
                $('.account_name').click(function () {
                    var name  =$(this).data("name_account");
                    var subAccount = $(this).data("sub_account");
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    var own_shipment = $(this).data("own_shipment");
                    $.post("account_summary_report.php",{func:'func',subAccount:subAccount,toDate:toDate,fromDate:fromDate},function(data){
                        $('#subAccountDetail').html(data);
                        $('#modal_title_account').html(name+" Sub Account List");
                        $("#account_name_modal").modal('show');
                    });
                });
                $('.country_own_shipment').click(function () {
                    var name  =$(this).data("name_account");
                    var own_shipment = $(this).data("own_shipment");
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    $('#country_table').html();
                    var own_shipment = $(this).data("own_shipment");
                    $.post("account_summary_report.php",{func:'func_country',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#country_table').html(data);
                        $('#modal_title_country').html(name+"Own Country Shipment");
                        $("#country_modal").modal('show');
                    });
                });
                $('.own_service').click(function () {
                    var name  =$(this).data("name_account");
                    var own_shipment = $(this).data("own_shipment");
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    $('#service_table').html();
                    $.post("account_summary_report.php",{func:'func_service',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#service_table').html(data);
                        $('#modal_title_service').html(name+" Own Service Shipment");
                        $("#service_modal").modal('show');
                    });
                });
                $('.country_sub_shipment').click(function () {
                    var name  =$(this).data("name_account");
                    var own_shipment = $(this).data("own_shipment");
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    $('#country_table').html();
                    var own_shipment = $(this).data("own_shipment");
                    $.post("account_summary_report.php",{func:'func_country',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#country_table').html(data);
                        $('#modal_title_country').html(name+" Sub Account Country Shipment");
                        $("#country_modal").modal('show');
                    });
                });
                $('.country_sub_service').click(function () {
                    
                     var name  =$(this).data("name_account");
                    var own_shipment = $(this).data("own_shipment");
                    $('#service_table').html();
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    var own_shipment = $(this).data("own_shipment");
                    $.post("account_summary_report.php",{func:'func_service',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#service_table').html(data);
                        $('#modal_title_service').html(name+" Sub Account Service Shipment");
                        $("#service_modal").modal('show');
                    });
                });
                
                $('.country_total_shipment').click(function () {
                    $( ".report_hide_total_account" ).toggle();
                    var name  =$(this).data("name_account");

                    var own_shipment = $(this).data("own_shipment");
                    var own_shipment = $(this).data("own_shipment");
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    $('#country_table').html();
                    $.post("account_summary_report.php",{func:'func_country',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#country_table').html(data);
                        $('#modal_title_country').html(name+" Total Country Shipment");
                        $("#country_modal").modal('show');
                    });
                });
                $('.country_total_service').click(function () {
                    $( ".report_hide_total_account" ).toggle();
                     var name  =$(this).data("name_account");
                    var own_shipment = $(this).data("own_shipment");
                    $('#service_table').html();
                    var fromDate = $("#from").val();
                    var toDate = $("#to").val();
                    $.post("account_summary_report.php",{func:'func_service',own_shipment:own_shipment,toDate:toDate,fromDate:fromDate},function(data){
                        $('#service_table').html(data);
                        $('#modal_title_service').html(name+" Total Service Shipment");
                        $("#service_modal").modal('show');
                    });
                });
                $('.download_file').click(function () {
                    $('#download_file').val('download_excel');
                    $('#admin_form').submit();
                    setTimeout(function () {
                        $('#download_file').val('');
                    }, 3000);
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
                    Account Summary Report
                </div>
                <div class="actions">
                    <a href="javascript:{};" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</a>
                </div>
            </div>
            <?php if(!empty($this->noReocrdFound)){?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">No Record Found.</div>
                    </div>
                </div>
                <?php }?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                       <span id="account_name"></span>Report Filters
                    </div>
                </div>
                
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                
                                <div class="form-group">
                                         <div class="has-float-label input-icon right">

                                <div class="input-group date-picker input-daterange" data-date="dd-mm-yyyy" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) && (is_integer((int) $_POST['to_date']) || strtotime($_POST['to_date'] != false))) ? formatDate($_POST['to_date']) : ''; ?>" data-original-title="" title="">
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 
                                    <label class="control-label">Date Ranges </label>

                                </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                               
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                            $includeParent = false;
                                        }
                                        $selectedAccount = (!empty($_POST['user_account_id']) ? $_POST['user_account_id'] : '');
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                         <label class="label-account">Select Account</label>

                                    </div>
                                     </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                
                                <button class="btn btn-default" type="submit">Filter Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th align="center" style="text-align: center;">Total Sub Account</th>
                                <th align="center" style="text-align: center;">Own Shipment</th>
                                <th align="center" style="text-align: center;">Sub Account Shipment</th>
                                <th align="center" style="text-align: center;">Total Shipment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $dateFilter = '';
                            $from  = '';
                            $to = '';
                            if(!empty($_POST['from_date']) && !empty($_POST['to_date'])){
                                $from = $_POST['from_date'];
                                $to = $_POST['to_date'];
                                $dateFilter = "AND  DATE(c.`date_created`) >= '".date("Y-m-d",strtotime($from))."' AND DATE(c.`date_created`) <=  '".date("Y-m-d",strtotime($to))."'";
                            }
                            $totalSubAccnt = 0;
                            if(!empty($this->subDataAccount)){
                                $ownShipmentCounting = 0;
                                $subAccountShipmentCounting = 0;
                                $totalShipmentCounting = 0;
                                foreach($this->subDataAccount as $dataAccount){
                                 
                                $userFilterSubObj = new UserAccountFilter();
                                $userFilterSubObj->addFieldFilter('parentid',$dataAccount->getId());
                                $dataSubAccount = $userFilterSubObj->getList();
                                $totalSubAccnt += count($dataSubAccount);
                                $ownShipment = ConsignmentFilter::getConsignmentSummaryReport($dataAccount->getId(), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                                $dataSubAccountShipment = CustomerAccount::accountSubAccount($dataAccount->getId(), 0, false);
                                $subAccountShipment = ConsignmentFilter::getConsignmentSummaryReport(implode(',',$dataSubAccountShipment), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                                $totalShipment = CustomerAccount::accountSubAccount($dataAccount->getId(), 0, true);
                                $accountTotalShipment = ConsignmentFilter::getConsignmentSummaryReport(implode(',',$totalShipment), " WHERE c.`shipment_status` NOT IN ('11', '12', '22', '23') $dateFilter");
                                
                                ?>
                            <tr>
                                <td style='cursor:pointer;' class="account_name" data-sub_account="<?= $dataAccount->getId();?>" data-name_account="<?= $dataAccount->getUserAccount();?>">
                                    <?= $dataAccount->getUserAccount();?>
                                </td>
                                
                                <td style='cursor:pointer;' align="center" class="total_sub_account" data-sub_account="<?= $dataAccount->getId();?>" data-name_account="<?= $dataAccount->getUserAccount();?>">
                                    <?= count($dataSubAccount);?>
                                </td>
                                <td class="own_ship" style='cursor:pointer;' align="center">
                                    <?= (!empty($ownShipment)?$ownShipment[0]->getId():'0');?>
                                    <?php $ownShipmentCounting += $ownShipment[0]->getId();?>
                                    <br>
                                    <small class="report_hide"><a href="javascript:{};" class="badge badge-success country_own_shipment" data-name_account="<?= $dataAccount->getUserAccount();?>" data-own_shipment="<?= $dataAccount->getId();?>">country</a> <a href="javascript:{};" class="badge badge-info own_service" data-own_shipment="<?= $dataAccount->getId();?>" data-name_account="<?= $dataAccount->getUserAccount();?>">Service</a></small>
                                </td>
                                <td class="sub_account_ship" style='cursor:pointer;' align="center">
                                    <?= (!empty($subAccountShipment)?$subAccountShipment[0]->getId():'0');?>
                                    <?php
                                    $sub_account = (!empty($subAccountShipment)?$subAccountShipment[0]->getId():'0');
                                    $subAccountShipmentCounting += $sub_account;
                                    ?>
                                    <br>
                                    <small class="report_hide_sub_account"><a href="javascript:{};" class="badge badge-success country_sub_shipment" data-name_account="<?= $dataAccount->getUserAccount();?>" data-own_shipment="<?= implode(",",$dataSubAccountShipment);?>">country</a>  <a href="javascript:{};" class="badge badge-info country_sub_service" data-own_shipment="<?= implode(",",$dataSubAccountShipment);?>" data-name_account="<?= $dataAccount->getUserAccount();?>">Service</a></small>
                                </td>
                                <td class="total_account_ship" style='cursor:pointer;' align="center">
                                    <?= (!empty($accountTotalShipment)?$accountTotalShipment[0]->getId():'0'); ?>
                                    <?php $totalShipmentCounting += $accountTotalShipment[0]->getId();?>
                                    
                                    <br>
                                    <small class="report_hide_total_account"><a href="javascript:{};" class="badge badge-success country_total_shipment" data-name_account="<?= $dataAccount->getUserAccount();?>" data-own_shipment="<?= implode(",",$totalShipment);?>">country</a>  <a href="javascript:{};" class="badge badge-info country_total_service" data-own_shipment="<?= implode(",",$totalShipment);?>" data-name_account="<?= $dataAccount->getUserAccount();?>">Service</a></small>
                                </td>
                            </tr>
                            <?php 
                            
                                }
                                
                            ?>
                            <tr>
                                <th>Total</th>
                                <td align="center" class="text-center"><?= $totalSubAccnt; ?></td>
                                <td align="center"><?= $ownShipmentCounting;?></td>
                                <td align="center"><?= $subAccountShipmentCounting;?></td>
                                <td align="center"><?= $totalShipmentCounting;?></td>
                            </tr>
                            <?php
                            }else{
                            ?>
                            <tr>
                                <td colspan="5">No Record Found.</td>
                                
                            </tr>
                            <?php   
                            }
                                ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="account_name_modal" class="modal fade" tabindex="-1" aria-hidden="true" style="z-index: 555555;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 id="modal_title_account" class="modal-title"></h4>
                    </div>
                    <div class="modal-body clearfix clear_both">  
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover" id="subAccountDetail">
                                </table>
                            </div>                    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn default">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="country_modal" class="modal fade" tabindex="-1" aria-hidden="true" style="z-index: 555555;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 id="modal_title_country" class="modal-title"></h4>
                    </div>
                    <div class="modal-body clearfix clear_both">  
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover" id="country_table">
                                </table>
                            </div>                    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn default">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="service_modal" class="modal fade" tabindex="-1" aria-hidden="true" style="z-index: 555555;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 id="modal_title_service" class="modal-title"></h4>
                    </div>
                    <div class="modal-body clearfix clear_both">  
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover" id="service_table">
                                </table>
                            </div>                    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn default">Close</button>
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

        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>