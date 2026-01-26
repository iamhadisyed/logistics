<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

include_classes([
    'country.class',
    'countryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'servicefilter.class',
    'services.class',
    'carrier.class',
    'carrierfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'carrierzonescountries.class',
    'carrierzonescountriesFilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'servicecountrytime.class',
    'servicecountrytimefilter.class',
    'countryzonesmapping.class',
    'countryzonesmappingfilter.class',
    'linehaul.class',
    'linehaulfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class'
]);
class Page extends BasePage
{
    private $user;
    private $tariffs;
    private $weightLimits;
    private $toCountryLimit = 100;
    private $userAccountIds = [];
    /*     * *
     * Controller logic
     */
    protected function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
    }

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'tariff_customer_report.php' => "Customer Tariff Report"
        );
        /*
         * DataTable handlings
        */
        $userAccountId = $this->user->getUserAccountId();
        $this->userAccountIds = CustomerAccount::accountSubAccount($userAccountId, 0, true);
        if (isset($_GET['action']) && $_GET['action'] == "tariff_report_ajax") {
            $tariffsFilter = new TariffsFilter();
            $tariffsFilter->addCustomJoin('        JOIN `tariffs_account_mapping` tam  ON tam.tariff_id = t.id ');
            $tariffsFilter->addCustomJoin('        JOIN `customer_account` ua  ON ua.id = tam.user_account_id ');
            $tariffsFilter->addCustomJoin('        JOIN `carrier` c  ON c.id = t.carrier_id ');
            $tariffsFilter->addCustomJoin('        JOIN `services` s  ON s.id = t.service_id ');
            $tariffsFilter->addFieldFilter('      t.status', 1);
            $tariffsFilter->addFilter('      t.end_date >= CURRENT_DATE');
            $tariffsFilter->addFieldFilter('      ua.active_flag',1);
            $tariffsFilter->addFilterIn('      tam.user_account_id',$this->userAccountIds);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $userAccountId = $this->form_vars['user_account_id'];
                if (!empty($userAccountId))
                    $tariffsFilter->addFieldFilter('       ua.id', $userAccountId);

                $carrierId = $this->form_vars['carrier_id'];
                if (!empty($carrierId))
                    $tariffsFilter->addFieldFilter('      c.id', $carrierId);

                $serviceId = $this->form_vars['service_id'];
                if (!empty($serviceId))
                    $tariffsFilter->addFieldFilter('      s.id', $serviceId);

                $tariffName = $this->form_vars['name'];
                if (!empty($tariffName))
                    $tariffsFilter->addFieldLikeFilter('      t.name', $tariffName);

                $startDateFrom = $this->form_vars['date_start_from'];
                $startDateTo = $this->form_vars['date_start_to'];
                if (!empty($startDateFrom) && !empty($startDateTo)) {
                    $tariffsFilter->addDateFilter('       t.start_date',$startDateFrom, '>=');
                    $tariffsFilter->addDateFilter('       t.start_date',$startDateTo, '<=');
                }

                $endDateFrom = $this->form_vars['date_end_from'];
                $endDateTo = $this->form_vars['date_end_to'];
                if (!empty($endDateFrom) && !empty($endDateTo)) {
                    $tariffsFilter->addDateFilter('       t.end_date',$endDateFrom, '>=');
                    $tariffsFilter->addDateFilter('       t.end_date',$endDateTo, '<=');
                }

                $assignDateFrom = $this->form_vars['date_assign_from'];
                $assignDateTo = $this->form_vars['date_assign_to'];
                if (!empty($assignDateFrom) && !empty($assignDateTo)) {
                    $tariffsFilter->addDateFilter('       tam.added_date',$assignDateFrom, '>=');
                    $tariffsFilter->addDateFilter('       tam.added_date',$assignDateTo, '<=');
                }

                $endDate = $this->form_vars['end_date'];
                if (!empty($endDate))
                    $tariffsFilter->addEndDateFilter($endDate);

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
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                $tariffsFilter->AddOrderBy(strtolower("t." . $dataTableColumnName), $orderFalse);
            } else {
                $tariffsFilter->AddOrderBy('       ua.user_account');
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $tariffsFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $tariffsFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $tariffsFilter->setOffset($iDisplayStart);
            $tariffsObjs = $tariffsFilter->getPagingList('t.name,t.start_date,t.end_date,ua.user_account,c.carrier,s.name as service,tam.added_date as assign_date');
            $setDataArr = array();
            foreach ($tariffsObjs as $tariffsObj) {
                $startDate = formatDate(date("d-m-Y", strtotime($tariffsObj->getStartDate())));
                $endDate = formatDate(date("d-m-Y", strtotime($tariffsObj->getEndDate())));
                $assignDate = formatDate(date("d-m-Y", strtotime($tariffsObj->getAssignDate())));
                $currentArr = array();
                $currentArr['user_account'] = $tariffsObj->getUserAccount();
                $currentArr['carrier'] = $tariffsObj->getCarrier();
                $currentArr['service'] = $tariffsObj->getService();
                $currentArr['tariff'] = $tariffsObj->getName();
                $currentArr['start_date'] = $startDate;
                $currentArr['end_date'] = $endDate;
                $currentArr['assign_date'] = $assignDate;
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_tariff_excel') {
            $tariffsFilter = new TariffsFilter();
            $tariffsFilter->addCustomJoin('        JOIN `tariffs_account_mapping` tam  ON tam.tariff_id = t.id ');
            $tariffsFilter->addCustomJoin('        JOIN `customer_account` ua  ON ua.id = tam.user_account_id ');
            $tariffsFilter->addCustomJoin('        JOIN `carrier` c  ON c.id = t.carrier_id ');
            $tariffsFilter->addCustomJoin('        JOIN `services` s  ON s.id = t.service_id ');
            $tariffsFilter->addFieldFilter('      t.status', 1);
            $tariffsFilter->addFilter('      t.end_date >= CURRENT_DATE');
            $tariffsFilter->addFieldFilter('      ua.active_flag',1);
            $tariffsFilter->AddOrderBy('       ua.user_account');
            $tariffsFilter->addFilterIn('      tam.user_account_id',$this->userAccountIds);
            $tariffsFilterObjs = $tariffsFilter->getlist('t.name,t.start_date,t.end_date,ua.user_account,c.carrier,s.name as service,tam.added_date as assign_date');
            $styleHeadingColoumn = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                    'color' => array('rgb' => 'FFFFFF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000')
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3598dc')
                ),
            );
            $styleColoumn = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000')
                    )
                )
            );
            if (!empty($tariffsFilterObjs)) {
                $objPHPExcel = new PHPExcel();
                $heading = ['Account Name','Carrier','Service','Tariff Name','Tariff Start date','Tariff End Date','Tariff Assign Date'];
                $rowNum = 1;
                $colNum = 'A';
                foreach ($heading as $h) {
                    $cell_name = $colNum.$rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleHeadingColoumn);
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowNum)->setRowHeight(20);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth(35);
                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                }
                $rowNum++;
                foreach($tariffsFilterObjs as $tariffsFilterObj) {
                    $startDate = formatDate(date("d-m-Y", strtotime($tariffsFilterObj->getStartDate())));
                    $endDate = formatDate(date("d-m-Y", strtotime($tariffsFilterObj->getEndDate())));
                    $assignDate = formatDate(date("d-m-Y", strtotime($tariffsFilterObj->getAssignDate())));
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $tariffsFilterObj->getUserAccount());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $tariffsFilterObj->getCarrier());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $tariffsFilterObj->getService());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $tariffsFilterObj->getName());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $startDate);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $endDate);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleColoumn);
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $assignDate);
                    $rowNum++;
                }
                $fileName = "tariff_customer_report_excel_" . time();
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
            }
            die;
        }

    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style type="text/css">

        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"
                type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-bootstrap-multiselect.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/serializeForm.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "tariff_customer_report.php?action=tariff_report_ajax";
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
                            "columns": [
                                {"data": "user_account", "bSortable": false},
                                {"data": "carrier", "bSortable": false},
                                {"data": "service", "bSortable": false},
                                {"data": "tariff", "bSortable": false},
                                {"data": "start_date", "bSortable": false},
                                {"data": "end_date", "bSortable": false},
                                {"data": "assign_date", "bSortable": false}
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
                        autoclose: true,
                        formate: 'yyyy-mm-dd'
                    });
                }

                $(document).on('change','.date_filter,.select_filter', function() {
                    $('#manage-data-table button.filter-submit').click();
                });

                $(document).on('blur','.input_filter', function() {
                    $('#manage-data-table button.filter-submit').click();
                });

            });
        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>
        <style>
            .btn_hide {
                display: none;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light all_portlet" id="search_portlet">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-search"></i> Tariff Customer Excel</div>
                <div class="actions">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="download_tariff_excel" />
                        <button type="submit" class="btn btn-sm blue"><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_EXCEL"); ?></button>
                    </form>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Account Name</th>
                        <th>Carrier</th>
                        <th>Service</th>
                        <th>Tariff Name</th>
                        <th>Tariff Start Date</th>
                        <th>Tariff End Date</th>
                        <th>Tariff Assign Date</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs blue filter-submit btn-outline btn_hide" ><i class="fa fa-search"></i> </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline btn_hide"><i class="fa fa-times"></i> </button>
                            </div>
                            <div>
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
//                                    $allowedLevel = 1;
                                }
                                ?>
                                <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control select_filter" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                            </div>
                        </td>
                        <td class="user_acccount_correct_button">
                            <?php
                            $slectedCarrierId = '';
                            ?>
                            <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $slectedCarrierId, 'id', ' class="bs-select input-sm form-control form-filter select_filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                        </td>
                        <td class="user_acccount_correct_button">
                            <?php
                                $slectedServiceId = '';
                            ?>
                            <?php echo Ddl::generateServiceDDLWithImage('service_id', $slectedServiceId, 'id', ' class="bs-select input-sm form-control form-filter select_filter " data-live-search="true"  data-show-subtext="true" data-container="body"', '','', 'name'); ?>
                        </td>
                        <td class="user_acccount_correct_button">
                            <input type="text" class="form-control form-filter input-sm input_filter " name="name" id ="name" />
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_start_from" placeholder="From">
                                <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                            </div>
                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_start_to" placeholder="To">
                                <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_end_from" placeholder="From">
                                <span class="input-group-btn">
                                    <button class="btn btn-sm default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_end_to" placeholder="To">
                                <span class="input-group-btn">
                                    <button class="btn btn-sm default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_assign_from" placeholder="From">
                                <span class="input-group-btn">
                                    <button class="btn btn-sm default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date_filter" readonly name="date_assign_to" placeholder="To">
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
        <?php
    }

    public function renderFooter()
    {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
