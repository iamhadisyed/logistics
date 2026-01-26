<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
/* * *
 * Page for editing a user
 */
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([  
                    'agentdata.class',
                    'agentdatafilter.class',
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'warehouse.class',
                    'warehousefilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'parcel.class',
                    'parcelfilter.class'
    ]);

class Page extends BasePage {

    private $user;
    private $selected_user;
    private $service_id;
    private $carrier_id;
    private $date_created_to;
    private $date_created_from;
    private $warehouse_id= [];
    private $warehouseIdDropDown= [];
    private $noRecordFound;
    private $selectedWarehouse;

    protected function init() {
        $this->user = SessionManager::getUser();
        
              $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),'Service Scan Report'
        );

        if (isset($_GET['action']) && $_GET['action'] == 'get_carrier_services') {
            $user_account_id = $this->form_vars['user_account_id'];
            $selected_carrier = $this->form_vars['selected_carrier'];
            $selected_service = (!empty($this->form_vars['selected_service'])?explode(',',$this->form_vars['selected_service']):'');

            $consignmentList = array();
            if (!empty($user_account_id)) {
                $return = array();
                $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                $ids = implode(",", $userAccountArry);
                $userFilter = new UserFilter();
                $userIds = $userFilter->getUserIdsFromAccountIds($ids);
                $consignmentFilterObj = new ConsignmentFilter();
                $consignmentFilterObj->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                $consignmentList = $consignmentFilterObj->getColumnList("DISTINCT(c.service_id)");
                $serviceIds = array();
                foreach ($consignmentList as $con) {
                    $serviceIds[] = $con->getServiceId();
                }
            }
            $serviceFilterObj = new ServiceFilter();
            if (!empty($serviceIds)) {
                $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
            }
            $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            $carrierIds = array();
            foreach ($services as $ser) {
                if (!in_array($ser->getCarrierId(), $carrierIds)) {
                    $carrierIds[] = $ser->getCarrierId();
                }
            }
            
            $carrierFilterObj = new CarrierFilter();
            $carrierFilterObj->addFilterIn("c1.id", $carrierIds);
            $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
            $service_option = "<option value=''>Select Service</option>";
            foreach ($services as $sr) {
                $selected = "";
                if(in_array($sr->getId(),$selected_service)) {
                    $selected = "selected='selected'";
                }
                $service_option .= "<option value='" . $sr->getId() . "' " . $selected . " class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                $selected = "";
                if($cr->getId() == $selected_carrier) {
                    $selected = "selected='selected'";
                }
                $carrier_option .= "<option value='" . $cr->getId() . "' " . $selected . " >" . $cr->getCarrierDisplayName() . "</option>";
            }

            $return['services_option'] = $service_option;
            $return['carrier_option'] = $carrier_option;
            echo json_encode($return);
            die;
        }
        
        
        
        
        if($_GET['action'] && $_GET['action'] == 'serviceScannedReport'){
            
            $parcelObj = new ParcelFilter();
            $user_account_id = $this->user->getUserAccountId();
            $userId = 0;
            $trackingNumber = 0;
            $consignmentId = 0;
           
            if(!empty($_GET['fromDate'])){
                $this->date_created_from = $_GET['fromDate'];
            }
            if(!empty($_GET['toDate'])){
                $this->date_created_to = $_GET['toDate'];
            }
            if(!empty($_GET['service_id'])){
                $this->service_id = $_GET['service_id'];
            }
            if(!empty($_GET['carrier_id'])){
                $this->carrier_id = $_GET['carrier_id'];
            }
            if(!empty($_GET['warehouse_id'])){
               
                $this->warehouse_id[] = $_GET['warehouse_id'];
            }else{
                $dataWareHouse = getLoggedInUserChildWarehouse();
                if(!empty($dataWareHouse)){
                    foreach($dataWareHouse as $data){
                        $this->warehouse_id[] = $data->getId();
                    }
                }
            }
          
            $user_account_id = $this->user->getUserAccountId();
            $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
            if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                $user_account_id = '';
                $allouedAcccounts = [];
            }
            if(empty($_GET['service_id']) && empty($_GET['carrier_id'])){
                $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
                $this->carrier_id = $data['carrierIds'];
                $this->service_id = $data['serviceIds'];
            }
           
            $viewScanedParcelObj = $parcelObj->serivceScanReport($this->date_created_from,$this->date_created_to,  $this->carrier_id,  $this->service_id,  $this->warehouse_id,$allouedAcccounts);
            
            if(!empty($viewScanedParcelObj)){
                $serviceName = [];
                $totalShipment = 0;
                foreach($viewScanedParcelObj as $key => $viewData){
                    $serviceName[$viewData->getTarrifNo()][$viewData->getCarrier()][] = [
                        'carrierName' => $viewData->getCarrier(),
                        'serviceName' => $viewData->getName(),
                        'totalShipment' => $viewData->getId(),
                        'totalWeight' => $viewData->getWeight(),
//                        'trackingNumber' => $viewData->getTrackingNumber(),
//                        'scanBy' => $viewData->getQty(),
//                        'scanDate' => $viewData->getNumberItem()
                    ];
                }
//                echo '<pre>';
//                print_r($serviceName);
//                echo '</pre>';
//                die();
                $carrierName = '';
                $count= 0;
                $modalData= '';
                $grandWeight = [];
                $grandAvgWeight = [];
                $grandShipment = [];
                foreach($serviceName as $ind => $dataReport){
                    foreach($dataReport as $keyOne => $value){
                        $shipmentTotal = 0;
                        $weightTotal = 0;
                        $avgWeightTotal = 0;
                        $modalData = '';
                        $grandModalData = '';
                        $modalAvgWeight= [] ;
                        foreach($value as $key => $data){
                            $shipmentTotal += $data['totalShipment'];
                            $weightTotal += $data['totalWeight'];
                            $avgWeightTotal = $data['totalWeight'] / $data['totalShipment'];
                            $avgWeight = 0;
                            $avgWeight = $data['totalWeight'] / $data['totalShipment'];
                            $grandWeight[] = $data['totalWeight'];
                            $grandShipment[] = $data['totalShipment'];
                            $modalData .= '<tr>              <td>'.$data['serviceName'].'</td>
                                                            <td>'.$data['totalShipment'].'</td>
                                                            <td>'.number_format($data['totalWeight'],2).'</td>
                                                            <td>'.number_format($avgWeight,2).'</td>
                                                        </tr>';
                            $modalAvgWeight[] = $avgWeight;
                        }
                        $grandModalData .='<tr>            
                            <td><strong>Grand Total</strong></td>
                            <td>'. number_format($shipmentTotal,2).'</td>
                            <td>'. number_format($weightTotal,2).'</td>
                            <td>'.number_format(array_sum($modalAvgWeight),2).'</td>
                                        </tr>';
                        $currentArr = array();
                        $carrierName = $keyOne;
                        $currentArr['action'] = '<i  class="fa fa-eye" data-toggle="modal" data-target="#serviceScanModal'.$count.'"></i><div class="modal fade" id="serviceScanModal'.$count.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Service Scan Report</h4>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr role="row" class="heading">
                                                            <th>Service Name</th>
                                                            <th>Total Shipment</th>
                                                            <th>Total Weight (Kg)</th>
                                                            <th>Total AvgWeight</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        '.$modalData.$grandModalData.'
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                            </div>
                        </div>';
                        $currentArr['warehouse'] = $ind;
                        $currentArr['carriername'] = $carrierName;
                        $currentArr['totalparcel'] = number_format($shipmentTotal,2);
                        $currentArr['totalweight'] = number_format($weightTotal,2);
                        $weight_avg = $weightTotal / $shipmentTotal;
                        $grandAvgWeight[] = $weight_avg;
                        $currentArr['totalavgweight'] = number_format($weight_avg,2);
                        $setDataArr[] = $currentArr;
                        $count++;
                    }
                }
                $weightGrand = array_sum($grandWeight);
                $shipmentGrand = array_sum($grandShipment);
                $currentArr['action'] = '';
                $currentArr['warehouse'] = '';
                $currentArr['carriername'] = '<strong>Grand Total</strong>';
                $currentArr['totalparcel'] = number_format($shipmentGrand,2);
                $currentArr['totalweight'] = number_format($weightGrand,2);
                $currentArr['totalavgweight'] = number_format(array_sum($grandAvgWeight),2);
                $setDataArr[] = $currentArr;
               
            }
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }
        
         if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
                $parcelObj = new ParcelFilter();
                if(!empty($_POST['from_date'])){
                    $this->date_created_from = $_POST['from_date'];
                }
                if(!empty($_POST['to_date'])){
                    $this->date_created_to = $_POST['to_date'];
                }
                if(!empty($_POST['service_id'])){
                    $this->service_id = $_POST['service_id'];
                }
                if(!empty($_POST['carrier_id'])){
                    $this->carrier_id = $_POST['carrier_id'];
                }
                if(!empty($_POST['warehouse_id'])){
                    $this->warehouse_id[] = $_POST['warehouse_id'];
                }
                $user_account_id = $this->user->getUserAccountId();
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                    $user_account_id = '';
                    $allouedAcccounts = [];
                }
                if(empty($_POST['service_id']) && empty($_POST['carrier_id'])){
                    $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
                    $this->carrier_id = $data['carrierIds'];
                    $this->service_id = $data['serviceIds'];
                }
                
                
                
                $serviceScanedParcelObj = $parcelObj->serivceScanReport($this->date_created_from,$this->date_created_to,  $this->carrier_id,  $this->service_id,  $this->warehouse_id,$allouedAcccounts);
                if(!empty($serviceScanedParcelObj)){
                    
                    $objPHPExcel = new PHPExcel();
                    $objDataSheet = $objPHPExcel->getActiveSheet();
                    $objDataSheet->setTitle('Service Scan Report');

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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleForReport);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Service Scan Report");
                    $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Warehouse Name");
                    $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Carrier Name");
                    $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Service Name");
                    $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Total Shipment");
                    $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Total Weight");
                    $objPHPExcel->getActiveSheet()->SetCellValue('F2', "Avg Weight");
                    $count = '3';
                    $totalShipment = [];
                    $totalWeight = [];
                    $totalAvgWeight = [];
                    foreach($serviceScanedParcelObj as $key => $data){
                        $avgWeight = 0;
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $data->getTarrifNo());
                        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, $data->getCarrier());
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $data->getName());
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $data->getId());
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $data->getWeight());
                        $avgWeight = $data->getWeight() / $data->getId();
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $avgWeight);
                        $totalShipment[] =  $data->getId();
                        $totalWeight[] =  $data->getWeight();
                        $totalAvgWeight[] = $avgWeight;
                        $count++;
                    }
                   
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, 'Grand Total');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count,number_format(array_sum($totalShipment),2,'.',''));
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, number_format(array_sum($totalWeight),2,'.',''));
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count,  number_format(array_sum($totalAvgWeight),2,'.',''));
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=service-scan-report-' . time() . '.xls'); // file name of excel
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
                    
                    
                    
                }else {
                    $this->noRecordFound = "No record Found.";
                }
                
                
                
        }
        $this->selectedWarehouse = (!empty($this->form_vars['warehouse_id'])?$this->form_vars['warehouse_id']:'');
        $dataWareHouse = getLoggedInUserChildWarehouse();
        if(!empty($dataWareHouse)){
            foreach($dataWareHouse as $data){
                $this->warehouseIdDropDown[$data->getId()] =$data->getWarehouseName();
            }
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />  
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var toDate = '<?= (!empty($_POST['to_date']) ? '&toDate=' . $_POST['to_date'] : ''); ?>';
                    var fromDate = '<?= (!empty($_POST['from_date']) ? '&fromDate=' . $_POST['from_date'] : ''); ?>';
                    var search_Carrier_id = '<?= (!empty($_POST['carrier_id']) ? '&carrier_id=' . $_POST['carrier_id'] : ''); ?>';
                    var service_id = '<?= (!empty($_POST['service_id']) ? '&service_id=' . $_POST['service_id'] : ''); ?>';
                    var warehouse_id = '<?= (!empty($_POST['warehouse_id'] && is_integer((int)$_POST['warehouse_id'])) ? '&warehouse_id=' . (int)$_POST['warehouse_id'] : ''); ?>';
                    var datatableurl = "service_scan_report.php?action=serviceScannedReport"+ toDate + fromDate + search_Carrier_id + service_id+ warehouse_id;
                    
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
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
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "bInfo": false, //Dont display info e.g. "Showing 1 to 4 of 4 entries"
                            "paging": false,//Dont want paging                
                            "bPaginate": false,//Dont want paging      
                            "columns": [
                                {"data": "action", "bSortable": false},
                                {"data": "warehouse", "bSortable": false},
                                {"data": "carriername", "bSortable": false},
                                {"data": "totalparcel", "bSortable": false},
                                {"data": "totalweight", "bSortable": false},
                                {"data": "totalavgweight", "bSortable": false},
                            ],
                            rowCallback: function (row, data, index) {
                                var cssClass = $('td input', row).val();
                                //$('td',row).removeClass("sorting_1").addClass(cssClass);
                            }
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
            
            function get_carriers(userAccountId) {
            var selected_carrier = '<?php echo (!empty($_POST['carrier_id'])?$_POST['carrier_id']:''); ?>';
            var selected_service = '<?php echo (!empty($_POST['service_id'])?$_POST['service_id']:''); ?>';
                $.ajax({
                    type: "POST",
                    url: "service_scan_report.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId,selected_carrier:selected_carrier,selected_service:selected_service },
                    dataType: "json",
                    success: function (data) {
                        $('#carriers').html(data.carrier_option);
                        $('#service_id').html(data.services_option);
                        $('#carriers').select2();
                        $('#service_id').select2();
                        $('#carriers').trigger('change');
                        
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            function change_carriers(carrierId) {
                $('.serviceOption').attr("disabled", "disabled");
                $('.carrier_' + carrierId).removeAttr("disabled");
                $('#service_id').select2();
                
            }
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $('.download_file').click(function () {
                    $('#download_file').val('download_excel');
                    $('#admin_form').submit();
                    setTimeout(function () {
                        $('#download_file').val('');
                    }, 3000);
                });
                setTimeout(function () {
                    $('.msg').hide();
                }, 3000);
                var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
                var selected = '<?php echo $this->carrier_id ?>';
                get_carriers(userAccountId, selected);
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    change_carriers(carrierId);
                   
                });
//                $('#date_created_from').focusout(function(){
//                    $('#date_created_to').val($(this).val());
//                });
            });
            
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <style>
            #select2-service_id-results .select2-results__option[aria-disabled=true] {
                display: none;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
        <div class="portlet light bordered">
                               
                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                    </div>
                    <div class="actions">
                        <a href="javascript:{};" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</a>
                    </div>
                </div>
                <?php if(!empty($this->noRecordFound)){?>
                <div class="col-md-12 alert alert-danger msg">
                    <?= $this->noRecordFound;?>
                </div>
                <?php }?> 
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                       
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    
                                    <div class="first_form_col">
                                        <div class="input-group">
                                           <div class="has-float-label input-icon right"> <i class="fa fa-shopping-cart"></i> 
                                            <?php
                                             echo Ddl::generateArrayDDL('carrier_id', array("" => "Select Carrier"), $this->carrier_id, '', 'class="form-filter select2 form-control" ', "", 'carriers'); 
                                            ?> 
                                            <label >Carrier</label>                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-3">
                               
                               <div class="form-group">
                                 <div class="has-float-label input-icon right">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id', array("" => "Select Service"), '', '', ' rel="tooltip"  title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service_id'); ?>
                                         <label class="label-account">Select Service</label>
                                    </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                   <div class="form-group">
                                 <div class="has-float-label input-icon right">
                                <div class="input-group date-picker input-daterange" data-date="20-01-2018" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']) : ''); ?>" data-original-title="" title="">

                                </div>
                                <label class="control-label">Date Ranges </label>
</div></div>
                                
                            </div>
                            <div class="col-md-3">
                                
                              <div class="form-group">
                                 <div class="has-float-label input-icon right">
                                     <div id="service_div">
                                    <?php 
                                        echo Ddl::generateArrayDDL('warehouse_id', $this->warehouseIdDropDown, $this->selectedWarehouse, 'Please Select Warehouse', ' rel="tooltip"  title="Warehouse" class="form-filter select2 form-control" ', "", 'warehouse_id'); 
                                    ?><label class="label-account">Select Warehouse</label>

                                     </div> </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                 <label class="control-label">&nbsp;</label><br>
                                 <input type="hidden" class="" name="download_file" id="download_file" value=""> 
                                <button class="btn btn-primary" type="submit">Filter Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-envelope"></i>
                    Service Scanned Report
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Detail</th>
                                <th>Warehouse Name</th>
                                <th>Carrier Name</th>
                                <th>Total Shipment</th>
                                <th>Total Weight (Kg)</th>
                                <th>Avg Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
