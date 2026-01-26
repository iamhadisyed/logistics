<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([   
                    'mawbparcelmapping.class',
                    'mawbparcelmappingfilter.class',
                    'invoices.class',
                    'invoicesfilter.class']);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $headerStyle;
    private $styleForReport;
    private $carrierId = '';
    private $serviceId = '';
    private $countryId = '';
    private $userAccountId = '';
    private $allUserCarrierId = array();
    private $allUserServicesId = array();
    private $totalbags = [];
    private $noRecord = '';
    private $pallet_id = '';
    private $startDate = '';
    private $endDate = '';
    private $mawb = '';
    private $message = '';
    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),'Mawb Service Report'
        );
        $this->user = SessionManager::getUser();

        if(isset($_POST) && !empty($_POST) && !empty($_POST['mawb_no'])){
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];            
            $this->mawb = (!empty($_POST['mawb_no']) ? $_POST['mawb_no'] : '');
            $this->mawb = array_map('trim',explode("\n", str_replace("\r", "", $this->mawb)));            
            if(count($this->mawb) <= 50){
                $user_account_id = $this->user->getUserAccountId();
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                    $user_account_id = '';
                    $allouedAcccounts = [];
                }

                $mawbParcelObj  = new MawbParcelMappingFilter();
                $mawbServiceData = $mawbParcelObj->getMawbService($this->mawb);
                $servicesArr = [];
                $carrierArr = [];
                $mawbArr = [];
                if(!empty($mawbServiceData)){
                    $serviceMawb = [];
                    foreach($mawbServiceData as $mawbData){
                        $servicesArr[] = $mawbData->getWharehouseId();
                        $carrierArr[] = $mawbData->getWidth();
                        $mawbArr[] = $mawbData->getMawbNumber();
                        $totalBags = 0;
                        if(!empty($mawbData->getBagId()))
                            $totalBags = count(explode(",",$mawbData->getBagId()));
                        $avgWeight = $mawbData->getActualWeight()*$mawbData->getParcelId()/100;
                        $serviceMawb[$mawbData->getWharehouseId()][$mawbData->getMawbNumber()] = array(
                            'totalParcel' => $mawbData->getParcelId(),
                            'avgWeight' => $avgWeight,
                            'weight' => $mawbData->getActualWeight(),
                            'carrierName' => $mawbData->getWidth(),
                            'totalBagNumber' => $totalBags,
                            'mawb' => $mawbData->getMawbNumber(),

                        );
                    }
                    $servicesArr = array_unique($servicesArr);
                    //$carrierArr = array_unique($carrierArr);
                    $mawbArr = array_unique($mawbArr);
                    $objPHPExcel = new PHPExcel();
                    $objDataSheet = $objPHPExcel->getActiveSheet();
                    $objDataSheet->setTitle('Mawb Service Report');

                    $headingArray = array(
                        'font' => array(
                            'bold' => true,
                            'size' => 20,
                            'name' => 'Calibri',
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    );                
                    $styleForReport = array(
                       'alignment' => array(
                           'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                       )
                    ); 
                    $styleForBoldReport = array(
                       'font' => array(
                           'bold' => true,
                       ),
                       'alignment' => array(
                           'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                       )
                    ); 
                    $styleForGrandTotal = array(
                       'font' => array(
                           'bold' => true,
                       ),
                       'alignment' => array(
                           'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                       )
                    ); 

                    $lastColIndex = count($mawbArr) * 4 + 1; 
                    
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cols[$lastColIndex].'1');
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($headingArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$cols[$lastColIndex].'2')->applyFromArray($styleForReport);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Mawb Service Report");
                    $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Carrier");
                    $objPHPExcel->getActiveSheet()->SetCellValue('B3', "Services");
                    $count = '4';
                    $countCarrier = '4';
                    $countData = '4';
                    $grandTotalWeight = 0;
                    $grandTotalShipment = 0;
                    $grandTotalBag = 0;
                    $grandTotalAvgWeight = 0;
                    $palletNo = [];
                    $countServce =  3;
                    $startColIndex = 1;

                    $mergeCellIndex=2;
                    $isFalse = true;
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    foreach($servicesArr as $key =>$service){
                        $objPHPExcel->getActiveSheet()->SetCellValue('A'. $count,$carrierArr[$key]);
                        $objPHPExcel->getActiveSheet()->SetCellValue('B'. $count,$service);   
                        $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($styleForReport);
                        $objPHPExcel->getActiveSheet()->getStyle('B'. $count)->applyFromArray($styleForReport);
                        $count++;                    
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'. $count,' Total'); 
                    $objPHPExcel->getActiveSheet()->getStyle('B'. $count)->applyFromArray($styleForGrandTotal);
                    $eachMawbCol = 1;
                    foreach($mawbArr as $mawb){
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[$mergeCellIndex].'2', $mawb);                    
                        $objPHPExcel->getActiveSheet()->mergeCells($cols[$mergeCellIndex].'2:'.$cols[$mergeCellIndex + 3].'2');
                        $mergeCellIndex += 4; 
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol]."3", "Total Bags");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $startColIndex++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol]."3", "Total Parcels");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $startColIndex++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol]."3", "Total Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $startColIndex++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol]."3", "Avg Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        
                    }
                    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$cols[$eachMawbCol].'2')->applyFromArray($styleForBoldReport);
                    $objPHPExcel->getActiveSheet()->getStyle('A3:'.$cols[$eachMawbCol].'3')->applyFromArray($styleForBoldReport);
                    $dataStartRow = 4;
                    $totalBagNumberArr = [];
                    $totalParcelArr = [];
                    $totalWeightArr = [];
                    foreach($servicesArr as $service1){
                        $dataStartCol = 1;
                        foreach($mawbArr as $mawb1){
                            $totalParcel = 0;
                            $avgWeight = 0;                        
                            $totalBagNumber = 0;
                            $weight = 0;
                            if(isset($serviceMawb[$service1][$mawb1])){
                                $totalParcel = $serviceMawb[$service1][$mawb1]['totalParcel'];
                                $avgWeight = $serviceMawb[$service1][$mawb1]['avgWeight'];
                                $totalBagNumber = $serviceMawb[$service1][$mawb1]['totalBagNumber'];
                                $weight = $serviceMawb[$service1][$mawb1]['weight'];
                            }
                            $totalBagNumberArr[$mawb1][] = $totalBagNumber;
                            $totalParcelArr[$mawb1][] = $totalParcel;
                            $totalWeightArr[$mawb1][] = $weight;
                            $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$dataStartCol].$dataStartRow, $totalBagNumber);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol].$dataStartRow)->applyFromArray($styleForReport);
                            $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$dataStartCol].$dataStartRow, $totalParcel);                        
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol].$dataStartRow)->applyFromArray($styleForReport);
                            $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$dataStartCol].$dataStartRow, $weight);                        
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol].$dataStartRow)->applyFromArray($styleForReport);
                            $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$dataStartCol].$dataStartRow, $avgWeight);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol].$dataStartRow)->applyFromArray($styleForReport);
                        }
                        $dataStartRow++;                
                    }
                    $eachMawbCol = 1;
                    foreach($mawbArr as $mawb){
                        $avg = 0;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol].$count,  array_sum($totalBagNumberArr[$mawb]));
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$eachMawbCol].$count)->applyFromArray($styleForReport);
                        $startColIndex++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol].$count, array_sum($totalParcelArr[$mawb]));
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$eachMawbCol].$count)->applyFromArray($styleForReport);
                        $startColIndex++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol].$count, array_sum($totalWeightArr[$mawb]));
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$eachMawbCol].$count)->applyFromArray($styleForReport);
                        $startColIndex++;
                        $avg = array_sum($totalWeightArr[$mawb]) * array_sum($totalParcelArr[$mawb]) / 100;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cols[++$eachMawbCol].$count, $avg);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$eachMawbCol].$count)->applyFromArray($styleForReport);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$eachMawbCol])->setAutoSize(true);
                    }
                      
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=mawb_service' . time() . '.xls'); // file name of excel
                    header('Cache-Control: max-age=0');
                    header('Cache-Control: max-age=1');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Cache-Control: cache, must-revalidate');
                    header('Pragma: public'); // HTTP/1.0
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save('php://output');
                    exit;
                }else{
                    $this->message = "No record found";
                }
            }else{
                $this->message = "You can not get more than 50 mawb report.";
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

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>


        <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/charts-amcharts.min.js" type="text/javascript"></script>


        <script type="text/javascript">

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
                    Mawb Service Report
                </div>
            </div>
            <div class="portlet light bordered">

                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <?php if(!empty($this->message) && !empty($_POST)){?>
                    <div class="row msg">
                        <div class="col-md-12 alert alert-danger"><?php echo $this->message; ?></div>
                    </div>
                <?php }?>
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="form-group">
                                    <div class="has-float-label input-icon right"> <i class="fa fa-credit-card"></i> 
                                        <textarea class="form-control notes" type="text" placeholder="Enter Mawb No" rows="22" name="mawb_no" id="mawb_no" style="min-height: 100px;" data-original-title="" title=""  required="required"></textarea>
                                         <label for="mawb_no">Mawb</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" > 
                            <div class="col-md-3">
                                <label class="control-label">&nbsp;</label><br>
                                <button type="submit" id="btnexportmawb" class="btn btn-primary btn_save" >Download Excel</button>
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
        
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>