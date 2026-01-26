<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([
    'flightinfo.class',
    'flightinfofilter.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    
    ]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;
    private $noRecord='';

    protected function init() {
        $this->user = SessionManager::getUser();
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Pre Alert Advice Report'
        );
        
        if (isset($this->form_vars['download_file']) && $this->form_vars['download_file'] == "download_excel") {
            
             $flightFilterObj=  new FlighInfoFilter();
            if(!empty($this->form_vars['search_mawb'])){
                $flightFilterObj->addFilter("   fm.mawb LIKE   '%".$this->form_vars['search_mawb']."%'");
            }

            if(!empty($this->form_vars['search_flight'])){
                $flightFilterObj->addFilter("   fi.flight_number LIKE   '%".DbAccess3::escape($this->form_vars['search_flight'])."%'");
            }
            if(!empty($this->form_vars['search_to_date_etd']) && !empty($this->form_vars['search_from_date_etd'])){
                $flightFilterObj->addFilter("   DATE(fi.etd) >= '".date('Y-m-d',  strtotime($this->form_vars['search_from_date_etd']))."'");
                $flightFilterObj->addFilter("   DATE(fi.etd) <= '".date('Y-m-d',  strtotime($this->form_vars['search_to_date_etd']))."'");
            }
            if(!empty($this->form_vars['search_to_date_eta']) && !empty($this->form_vars['search_from_date_eta'])){
                $flightFilterObj->addFilter("   DATE(fi.eta) >= '".date('Y-m-d',  strtotime($this->form_vars['search_from_date_eta']))."'");
                $flightFilterObj->addFilter("   DATE(fi.eta) <= '".date('Y-m-d',  strtotime($this->form_vars['search_to_date_eta']))."'");
            }
//                if(!empty($this->form_vars['search_user_account_id'])){
//                    $flightFilterObj->addFilter("   fi.account_id = '".$this->form_vars['search_user_account_id']."'");
//                }
            
            if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                $flightFilterObj->addFilter("   fi.account_id = '".$this->user->getUserAccountId()."'");
            }
            
            $dataGettingFlightInfoAndMapping = $flightFilterObj->getList(TRUE);
            
            
            $objPHPExcel = new PHPExcel();
            $objDataSheet = $objPHPExcel->getActiveSheet();
            $objDataSheet->setTitle('Pre Alert Advice Report');

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
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
            $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleForReport);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Pre Alert Advice");
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Mawb#");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Flight#");
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Etd");
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Eta");
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Pieces");
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', "Weight (kg)");
            $objPHPExcel->getActiveSheet()->SetCellValue('G2', "Status");
            $count = '3';
            $totalShipment = [];
            $totalWeight = [];
            $totalAvgWeight = [];
            if(!empty($dataGettingFlightInfoAndMapping)){
                foreach($dataGettingFlightInfoAndMapping as $key => $data){
                    $status = (!empty($data->getStatus())? ucwords(str_replace("_"," ",$data->getStatus())) :'N/A');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $data->getMawb());
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, $data->getFlightNumber());
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $data->getEtd());
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $data->getEta());
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $data->getPieces());
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $data->getWeight());
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $count, $status);
                    $count++;
                }
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=pre-alert-advice-report-' . time() . '.xls'); // file name of excel
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
                $this->noRecord = 'No Record Found';
            }
        }
        
        if($_GET['action'] && $_GET['action'] == 'prealert_datatable'){
            $flightFilterObj=  new FlighInfoFilter();
           
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                if(!empty($this->form_vars['search_mawb'])){
                    $flightFilterObj->addFilter("   fm.mawb LIKE   '%".$this->form_vars['search_mawb']."%'");
                }
                
                if(!empty($this->form_vars['search_flight'])){
                    $flightFilterObj->addFilter("   fi.flight_number LIKE   '%".DbAccess3::escape($this->form_vars['search_flight'])."%'");
                }
                if(!empty($this->form_vars['search_to_date_etd']) && !empty($this->form_vars['search_from_date_etd'])){
                    $flightFilterObj->addFilter("   DATE(fi.etd) >= '".date('Y-m-d',  strtotime($this->form_vars['search_from_date_etd']))."'");
                    $flightFilterObj->addFilter("   DATE(fi.etd) <= '".date('Y-m-d',  strtotime($this->form_vars['search_to_date_etd']))."'");
                }
                if(!empty($this->form_vars['search_to_date_eta']) && !empty($this->form_vars['search_from_date_eta'])){
                    $flightFilterObj->addFilter("   DATE(fi.eta) >= '".date('Y-m-d',  strtotime($this->form_vars['search_from_date_eta']))."'");
                    $flightFilterObj->addFilter("   DATE(fi.eta) <= '".date('Y-m-d',  strtotime($this->form_vars['search_to_date_eta']))."'");
                }
//                if(!empty($this->form_vars['search_user_account_id'])){
//                    $flightFilterObj->addFilter("   fi.account_id = '".$this->form_vars['search_user_account_id']."'");
//                }
            }
            if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                $flightFilterObj->addFilter("   fi.account_id = '".$this->user->getUserAccountId()."'");
            }
            $flightFilterObj->addFilter("   fi.is_delete = '0' ");
            $flightFilterCount = $flightFilterObj->getCount();
            $iTotalRecords = $flightFilterCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $flightFilterObj->setRowsPerPage($iDisplayLength);
            $flightFilterObj->setOffset($iDisplayStart);
            $dataGettingFlightInfoAndMapping = $flightFilterObj->getList(TRUE);
            if(!empty($dataGettingFlightInfoAndMapping)){
                foreach($dataGettingFlightInfoAndMapping as $key => $preData){
                    $currentArr = array();
                    $owe_status = $preData->getStatus();
                    $status_owe = '';
                    if($owe_status == 'in_warehouse'){
                        $status_owe = 'IN Warehouse';
                    }else if($owe_status == 'assigned'){
                        $status_owe = 'ASSIGNED';
                    }else if($owe_status == 'not_assigned'){
                        $status_owe = 'NOT ASSIGNED';
                    }
                    //$status = (!empty($preData->getStatus())? ucwords(str_replace("_"," ",$preData->getStatus())) :'<label class="label label-warning">N/A</label>');
                    $currentArr['edit'] = "<a href='pre-alert.php?action=edit&id=".$preData->getId()."' class='btn btn-xs btn-default blue btn-outline center prealert_edit' rel='tooltip' data-toggle='tooltip' placeholder='Edit' title='Edit' data-id='".$preData->getId()."'> <span class='fa fa-pencil'></span> </a>";
                    $currentArr['mawb'] = $preData->getMawb();
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $currentArr['status'] = $status_owe;
                    $currentArr['eta'] = $preData->getEta();
                    $currentArr['etd'] = $preData->getEtd();
                    $currentArr['weight'] = $preData->getWeight();
                    $currentArr['pieces'] = $preData->getPieces();
                    if ($this->user->getUserType() != User::USER_TYPE_CLIENT) {
                        $currentArr['account'] = $preData->getUserAccount();
                    }
                    $file = '<label class="label label-warning">N/A</label>';
                    if(!empty($preData->getFiles())){
                        $fileLink = SETTING_MAIN_ASSETS . "preadvice/";
                        $file = '<a class="ebayButton" href="' . $fileLink . $preData->getFiles() . '" target="_blank"><i class="fa fa-download"></i></a>';
                    }
                    $currentArr['file'] = $file;
                    
                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }
        
        
    }

    protected function addPagelavelCss() {
        ?>
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
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>  

        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
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
                                "url": "pre_alert_search_report.php?action=prealert_datatable", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false},
                                {"data": "mawb", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "etd","bSortable": false},
                                {"data": "eta","bSortable": false},
                                {"data": "pieces","bSortable": false},
                                {"data": "weight","bSortable": false},
                                {"data": "status", "bSortable": false},
                                {"data": "file", "bSortable": false},
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
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                
                $('.download_file').click(function () {
                    $('#download_file').val('download_excel');
                    $('#search_from_date_download').val($('#from').val());
                    $('#search_to_date_download').val($('#to').val());
                    $('#search_mawb_download').val($('#search_mawb').val());
                    $('#search_flight_download').val($('#search_flight').val());
                    $('#form_download').submit();
                });
            });
            

        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-users"></i>
                    Pre Alert Advice Report
                </div>
                <div class="actions">
                    <a href="javascript:{};" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</a>
                    <form action="" id="form_download" method="post">
                        <input type="hidden" name="search_mawb" id="search_mawb_download" value="">
                        <input type="hidden" name="search_flight" id="search_flight_download" value="">
                        <input type="hidden" name="search_from_date" id="search_from_date_download" value="">
                        <input type="hidden" name="search_to_date" id="search_to_date_download" value="">
                        <input type="hidden" name="download_file" id="download_file" value="download_file"> 
                    </form>
                </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Edit</th>
                                <th style="width: 180px;">MAWB#</th>
                                <th style="width: 143px;">Flight#</th>
                                <th style="width: 253px;">ETD</th>
                                <th style="width: 253px;">ETA</th>
                                <th>Pieces</th>
                                <th>Weight</th>
                                <th>Status</th>
                                <th>File</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_mawb" id="search_mawb">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_flight" id="search_flight">
                                </td>
                                <td>
                                    <div class="input-group date-picker input-daterange" data-date="2018-01-20-" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter  input-sm" name="search_from_date_etd" id="search_from_date_etd" value="" data-original-title="" title="">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control form-filter  input-sm" name="search_to_date_etd" id="search_to_date_etd" value="" data-original-title="" title=""> 
                                    </div>
                                </td>
                               <td>
                                    <div class="input-group date-picker input-daterange" data-date="2018-01-20-" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter  input-sm" name="search_from_date_eta" id="search_from_date_eta" value="" data-original-title="" title="">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control form-filter  input-sm" name="search_to_date_eta" id="search_to_date_eta" value="" data-original-title="" title=""> 
                                    </div>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
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
