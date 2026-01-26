<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([  
                    'warehouse.class',
                    'warehousefilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'mawbparcelmapping.class',
                    'mawbparcelmappingfilter.class',
    'iaddress.class',
    'carrier.class',
    'carrierfilter.class',
    'consignment.class',
    'consignmentfilter.class',
    'services.class',
    'servicefilter.class',
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    
    private $mawbParcelMappingFilter = array();
    private $user = '';

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Batch Scanning Report"
        );
        $this->user = SessionManager::getUser();
        if($this->user->getUserType() == USER::USER_TYPE_CLIENT){
            util_redirect("index.php");
        }
            
        if (isset($_GET['action']) && $_GET['action'] == "scan_ajax") {
            /*
             * Column filter
             * For search
             */
            $this->mawbParcelMappingFilter = new MawbParcelMappingFilter();
            
            $this->mawbParcelMappingFilter->addFilterBagNotEmpty();
            if($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
                $this->mawbParcelMappingFilter->addFilterIn('       u.user_account_id', $userAccountArry);
            }
        
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->applyFilter($this->form_vars);
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                
                if($dataTableColumnName == "consignment_id") {
                    $dataTableColumnName = "p.consignment_id";
                }
                if($dataTableColumnName == "master_number") {
                    $dataTableColumnName = "mawb.mawb_number";
                }
                if($dataTableColumnName == "bag_number") {
                    $dataTableColumnName = "b.bagnumber";
                }
                if($dataTableColumnName == "tracking_number") {
                    $dataTableColumnName = "td.tracking_number";
                }
                if($dataTableColumnName == "scanned_by") {
                    $dataTableColumnName = "td.user_id";
                }
                $this->mawbParcelMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                $this->mawbParcelMappingFilter->AddOrderBy(strtolower("mpm.id"), false);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $this->mawbParcelMappingFilter->getBatchScanningReportCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->mawbParcelMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->mawbParcelMappingFilter->setOffset($iDisplayStart);
            $mawbParcelMappingFilterObjs = $this->mawbParcelMappingFilter->getBatchScanningReport();
            $setDataArr = array();
            foreach ($mawbParcelMappingFilterObjs as $mawbParcelMappingFilterObj) {
                $currentArr = array();
                $currentArr['warehouse_name'] = $mawbParcelMappingFilterObj->getWarehouseName();
                $currentArr['master_number'] = $mawbParcelMappingFilterObj->getMawbNumber();
                $currentArr['bag_number'] = $mawbParcelMappingFilterObj->getBagNumber();
                $currentArr['tracking_number'] = $mawbParcelMappingFilterObj->getTotalTracking();
                $currentArr['scanned_by'] = $mawbParcelMappingFilterObj->getScannedBy();
                $currentArr['scan'] = $mawbParcelMappingFilterObj->getTotalScan();
                $currentArr['not_scan'] = $mawbParcelMappingFilterObj->getTotalNotScan();
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                                <ul class="dropdown-menu" >
                                                    <li>
                                                        <a href="JavaScript:;" title="View Details" onclick="get_tracking_numbers('."'".$mawbParcelMappingFilterObj->getMawbNumber()."'".','.$mawbParcelMappingFilterObj->getBagId().','."'".$mawbParcelMappingFilterObj->getWharehouseId()."'".','."'".$mawbParcelMappingFilterObj->getBagNumber()."'".','."'".$mawbParcelMappingFilterObj->getMawbId()."'".')" >
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="JavaScript:Void(0);" title="Download Excel" onclick="download_single_excel('."'".$mawbParcelMappingFilterObj->getMawbId()."'".','.$mawbParcelMappingFilterObj->getBagId().','."'".$mawbParcelMappingFilterObj->getWharehouseId()."'".','."'".$mawbParcelMappingFilterObj->getBagNumber()."'".')" >
                                                            <i class="fa fa-download"></i> Download Excel
                                                        </a>
                                                    </li>
                                                </ul>
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
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download') {
            $this->mawbParcelMappingFilter = new MawbParcelMappingFilter(); 
            if($this->form_vars['download_type'] == "single") {
                $masterNumber = $this->form_vars['master_number_download'];
                $bagId = $this->form_vars['bag_id_download'];
                $bagNumber = $this->form_vars['bag_number_download'];
                $warehouseId = $this->form_vars['warehouse_download'];
                $output = [];
                $mawbParcelMappingFilterObjs = MawbParcelMappingFilter::getBagParcelTrackingNo($masterNumber, $bagId,$warehouseId);
                $heading = ["MAWB", "Bag Number", "Tracking Number", "Warehouse", "Status"];
                $objPHPExcel = new PHPExcel();
                $rowNum = 1;
                $colNum = 'A';
                foreach ($heading as $h) {
                    $cell_name = $colNum.$rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                } 
                $rowNum=2;
                $record = 1;
                foreach($mawbParcelMappingFilterObjs as $mawbParcelMappingFilterObj) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $mawbParcelMappingFilterObj->getMawbId());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $bagNumber);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $mawbParcelMappingFilterObj->getParcelId());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $mawbParcelMappingFilterObj->getWharehouseId());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, Consignment::$database_status_array[$mawbParcelMappingFilterObj->getParcelStatusCode()]);
                    $colNum++;
                    $rowNum++;
                    $record++;
                }
                $fileName = "MPOW_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            } else {
                $fileName = "MPOW_" . time(). ".csv";
                $this->mawbParcelMappingFilter->addFilterBagNotEmpty();
                if($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                    $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
                    $this->mawbParcelMappingFilter->addFilterIn('       u.user_account_id', $userAccountArry);
                }
                $this->applyFilter($this->form_vars);
                $mawbParcelMappingFilterObjs = $this->mawbParcelMappingFilter->getBatchScanningReport(false);

                $heading = ["Warehouse Name", "Master Number", "Bag Number", "Scanned By", "Tracking", "Scan", "Not Scan"];
                if(count($mawbParcelMappingFilterObjs) > 0) {
                    $returnString = "";
                    foreach ($heading as $h) {
                        $returnString .= $h . ",";
                    }   
                    foreach($mawbParcelMappingFilterObjs as $mawbParcelMappingFilterObj) {
                        $returnString .=  "\r\n";
                        $returnString .=  $mawbParcelMappingFilterObj->getWarehouseName() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getMawbId() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getBagNumber() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getScannedBy() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getTotalTracking() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getTotalScan() . ",";
                        $returnString .=  $mawbParcelMappingFilterObj->getTotalNotScan() . ",";
                    }
                    header("Content-type: text/csv");
                    header("Content-Disposition: attachment; filename=" . $fileName);
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    echo $returnString;
                }
            }
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_tracking_detail') {
            $masterNumber = $this->form_vars['master_number'];
            $bagId = $this->form_vars['bag_id'];
            $warehouseId = $this->form_vars['warehouse_id'];
            $output = [];
            $results = MawbParcelMappingFilter::getBagParcelTrackingNo($masterNumber, $bagId,$warehouseId);
            $html = "";
            if(count($results) > 0) {
                foreach($results as $key => $data) {
                    $html .= '<tr>';
                    $html .= '<td>'.($key+1).'</td>';
                    $html .= '<td>'.$data->getParcelId().'</td>';
                    $html .= '<td>'.$data->getWharehouseId().'</td>';
                    $html .= '<td><span class="label label-sm label-success">'. ucfirst(Consignment::$database_status_array[$data->getParcelStatusCode()]).'</span></td>';
                    $html .= '</tr>';
                }
            }
            $output["status"] = "success";
            $output["html"] = $html;
            echo json_encode($output);
            die;
        }
        
    }
    
    protected function applyFilter($data) {
        
        $this->form_vars = $data;

        $warehouse = $this->form_vars['warehouse'];
        if (!empty($warehouse))
            $this->mawbParcelMappingFilter->addFieldFilter('      td.warehouse_id', $warehouse);
        
        $masterNumber = $this->form_vars['master_number'];
        if (!empty($masterNumber))
            $this->mawbParcelMappingFilter->addFieldFilter('      mawb.mawb_number', $masterNumber);

        $bagNumber = $this->form_vars['bag_number'];
        if (!empty($bagNumber))
            $this->mawbParcelMappingFilter->addFieldFindInSet('      b.bagnumber', $bagNumber);


        $scannedBy = $this->form_vars['user_id'];
        if (!empty($scannedBy))
            $this->mawbParcelMappingFilter->addFieldFilter('     td.user_id', $scannedBy);
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
                        onSuccess: function (grid,response) {
                            $(".table-container .custom-alerts").hide();
                            if (response.recordsTotal == 0) {
                                $("#csv_download_btn").hide();
                            }else{
                                $("#csv_download_btn").show();
                            }
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
                                "url": "batch_scanning_report.php?action=scan_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                        {"data": "actions", "bSortable": false},
                                        {"data": "warehouse_name"},
                                        {"data": "master_number"},
                                        {"data": "bag_number"},
                                        {"data": "scanned_by"},
                                        {"data": "tracking_number"},
                                        {"data": "scan", "bSortable": false},
                                        {"data": "not_scan", "bSortable": false}
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
            function download_single_excel(master_number,bag_id,warehouse_id,bag_number) {
                $('#download_type').val("single");
                $('#master_number_download').val(master_number);
                $('#warehouse_download').val(warehouse_id);
                $('#bag_id_download').val(bag_id);
                $('#bag_number_download').val(bag_number);
                $('#batch_scanning_report_form').submit();
            }
            function download_csv() {
                $('#download_type').val("multiple");
                $('#consignment_id_download').val(0);
                $('#batch_scanning_report_form').submit();
            }
            function get_tracking_numbers(master_number,bag_id,warehouse_id,bag_number,mawb_id) {
                var form_data = new FormData();
                form_data.append('master_number', mawb_id);
                form_data.append('bag_id', bag_id);
                form_data.append('warehouse_id', warehouse_id);
                form_data.append('action', 'get_tracking_detail');
                $.ajax({
                    url: "batch_scanning_report.php",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        var status = response.status;
                        if (status == 'success') {
                            $('#tracking_number_detail').html(response.html);
                            $('#master_number_show').html(master_number);
                            $('#bag_number_show').html(bag_number);
                            $('#tracking_number_modal').modal("show");
                        } else {
                            swal("Sorry!", response.message, "error");
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
                <div class="caption"> <i class="fa fa-list"></i>
                    Batch Scanning Report
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <form method="post" action="batch_scanning_report.php" id="batch_scanning_report_form">
                    <input type="hidden" name="action" value="download" />
                    <input type="hidden" name="download_type" id="download_type" value="" />
                    <input type="hidden" name="master_number_download" id="master_number_download" value="0" />
                    <input type="hidden" name="warehouse_download" id="warehouse_download" value="0" />
                    <input type="hidden" name="bag_id_download" id="bag_id_download" value="0" />
                    <input type="hidden" name="bag_number_download" id="bag_number_download" value="0" />
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_btn" onclick="download_csv()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                        </div>
                        <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th>Actions</th>
                                    <th>Warehouse Name</th>
                                    <th>Master Number</th>
                                    <th>Bag Number</th>
                                    <th>Scanned By</th>
                                    <th>Tracking No</th>
                                    <th>Scan</th>
                                    <th>Not Scan</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                            <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                        </div>

                                    </td>                            
                                    <td>
                                        <?php
                                        $warehouseArr = "";
                                        $logddedInWareouse = getLoggedInUserChildWarehouse();
                                        foreach ($logddedInWareouse as $logddedWareouse) {
                                            $warehouseArr.= '<option value="'.$logddedWareouse->getId().'">'.$logddedWareouse->getWarehouseName().'</option>';
                                        }
                                        ?>
                                        <select id="warehouse" name="warehouse" class="form-control form-filter select2 select2-hidden-accessible" title="" placeholder="Select Warehouse" tabindex="-1" aria-hidden="true" data-original-title="Select Warehouse">
                                            <option value="">Please Select</option>
                                            <?php echo $warehouseArr; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-xs" name="master_number" id ="master_number" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-xs" name="bag_number" id ="bag_number" />
                                    </td>
                                    <td>
                                        <?php
                                        if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                                            echo Ddl::generateDDL('user_id', 'UserFilter', ' user_type IN ("corporate", "admin") ', 'user_name', 'id', "", ' class="form-filter select2 form-control" data-toggle="tooltip" data-placement="top" title="Scan by" data-original-title="Scan by"', 'Please Select', '', 'user_id', 'Scan by');
                                        }
                                        ?>
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
                </form>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="tracking_number_modal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Bag Details</h4>
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
                                        <th> Warehouse </th>
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