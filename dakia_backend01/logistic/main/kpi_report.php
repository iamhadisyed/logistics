<?php
////////////////////////////////////////////////////
// Controller for Admin - Courier list page
////////////////////////////////////////////////////

require_once("../includes/settings/config.inc.php");

//ini_set('upload_max_filesize', '1024M');
//ini_set('post_max_size', '265M');
//ini_set('max_execution_time', '60000');
//ini_set('request_terminate_timeout', '60000');
// set up local page class
class Page extends BasePage {
    /*     * *
     * This page's content
     * @return void
     */

    private $account;
    private $scandate = "";
    private $mawbno = "";
    private $order_date_from = "";
    private $order_date_to = "";
    //private $account = "";
    private $country = "";
    private $courier = "";
    private $service = "";
    private $errormsg = "";
    private $mawb_report_list = "";

    protected function init() {

        $user = SessionManager::getUser();

        $this->consigfilter = new ConsignmentFilter();

        //AJAX USER DROPDOWN REQUEST
        if (isset($_POST['action']) && trim($_POST['action']) == 'GET_SERVICE_LIST') {

            echo $this->serviceCodeDropdown($_POST["carrier"]);
            exit;
        }

        if (isset($_POST['FUNCTION']) && trim($_POST['FUNCTION']) == 'EXPORT') {
            $this->order_date_from = $_POST['order_date_from'];
            $this->order_date_to = $_POST['order_date_to'];
            $this->courier = $_POST['courier'];
            $this->service = $_POST['service'];

            $this->exportAjaxCSV();
            die;
        }

        if (isset($_POST['FUNCTION']) && trim($_POST['FUNCTION']) == 'DATA_TO_GENERATE') {
            $this->DATACONSIGNMENT = $_POST['data'];
            $this->order_date_from = $_POST['order_date_from'];
            $this->order_date_to = $_POST['order_date_to'];
            $this->next_data = $_POST['next_data'];
            $this->index = $_POST['index'];
            $this->totalLength = $_POST['totalLength'];
            $this->exportDataCSV($this->index, $this->totalLength, $this->DATACONSIGNMENT);

            die;
        }




        if (isset($_POST['FUNCTION']) && trim($_POST['FUNCTION']) == 'GENERATE_FILE') {
            $this->DATA_TO_PRINT = $_POST['DATA_TO_PRINT'];
            $fileName = "../_assets/" . date('Ymd') . "-" . time() . "-masterReport.csv";
            $myfile = fopen($fileName, "w") or die("Unable to open file!");
            fwrite($myfile, $this->DATA_TO_PRINT);
            fclose($myfile);
            echo "You have successfully downloaded the CSV. Please <a href='" . $fileName . "'>Click Here </a> to download";
            die;
        }


        if (isset($_POST['FUNCTION']) && trim($_POST['FUNCTION']) == 'EXPORT_EXL') {
            $this->mawbno = $_POST['mawb_search'];
//			$this->account_num 			=  $_POST['account_num'];
            $this->order_date_from = $_POST['order_date_from'];
            $this->order_date_to = $_POST['order_date_to'];
            $this->account = $_POST['account'];
            $this->country = $_POST['country'];
            $this->courier = $_POST['courier'];
            $this->service = $_POST['service'];


            $this->exportAjaxEXL("", $this->mawbno, "");
            die;
        }

        if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] = 'search') {
            $this->mawbno = $_POST['mawb_search'];
            $PreAlertFilter = new PreAlertFilter();
            $this->mawb_report_list = $PreAlertFilter->getMawbReport($this->mawbno);
        }
    }

    private function getHeader() {
        $record = "Mawb, Total Bags";
        return $record;
    }

    private function getCountryHeader() {
        $record = "\r\n";
        $record .= ", ,Service, Country, Total Shipment, Weight";
        $record = "\r\n";
        return $record;
    }

    private function exportAjaxCSV() {


        $orderDateFrom = $this->order_date_from;
        $orderDateTo = $this->order_date_to;


        if (trim($this->courier) != '' && sizeof($this->service) > 0) {


            $servicesCarrierFilter = new ServiceFilter();
            $servicesCarrierFilter->addFilter("code in ('" . implode("','", $this->service) . "')");
            $serviceCarrierDataFilter = $servicesCarrierFilter->getColumnList(' transit_time, code ');

            $transit_time_array = array();
            if (count($serviceCarrierDataFilter) > 0) {
                foreach ($serviceCarrierDataFilter as $serviceCarrierHandlingCode) {
                    $transit_time_array[$serviceCarrierHandlingCode->getCode()] = $serviceCarrierHandlingCode->getTransitTime();
                }
            }

            if (count($transit_time_array) > 0) {
                $DOT_report_array = array();
                $DNOT_report_array = array();
                $return_report_array = array();
                foreach ($transit_time_array as $key => $transittime) {

                    $servicesCarrierFilter = new ServiceFilter();
                    $servicesCarrierFilter->addcoulmnFilter('code', trim($key));
                    $serviceList = $servicesCarrierFilter->getColumnList(' name ');
                    if (count($serviceList) > 0) {
                        $servicename = $serviceList[0]->getName();
                    }

                    //GET LIST OF DELIVERY ON TIME
                    $consignmentFilter = new ConsignmentFilter();
                    if (trim($orderDateFrom) != '' && trim($orderDateTo) != '')
                        $consignmentFilter->addDateBookedRangeFilter(strtotime($orderDateFrom), strtotime($orderDateTo));

                    $consignmentFilter->addFieldFilter('handling', $key);
                    $consignmentFilter->addFilter("( DATEDIFF(date_delivered, date_booked) <= " . $transittime . ") ");
                    $consignmentFilter->addFilter(" date_delivered != '' ");
                    $consignmentFilter->addGroupByClause("date_booked");

                    $consignmentDOTList = $consignmentFilter->getColumnList("count(id) as awb, date_booked");


                    if (count($consignmentDOTList) > 0) {
                        foreach ($consignmentDOTList as $consignment) {
                            $DOT_report_array[$servicename][$consignment->getDateBooked()] = array($consignment->getAwb());
                        }
                    }


                    //GET LIST OF DELIVERY NOT ON TIME
                    $consignmentFilter = new ConsignmentFilter();
                    if (trim($orderDateFrom) != '' && trim($orderDateTo) != '')
                        $consignmentFilter->addDateBookedRangeFilter(strtotime($orderDateFrom), strtotime($orderDateTo));

                    $consignmentFilter->addFieldFilter('handling', $key);
                    $consignmentFilter->addFilter("( DATEDIFF(date_delivered, date_booked) > " . $transittime . ") ");
                    $consignmentFilter->addFilter(" date_delivered != '' ");
                    $consignmentFilter->addGroupByClause("date_booked");
                    $consignmentDeliveryPlusOneDayList = $consignmentFilter->getColumnList("count(id) as awb, date_booked");

                    if (count($consignmentDeliveryPlusOneDayList) > 0) {
                        foreach ($consignmentDeliveryPlusOneDayList as $consignment) {
                            $DNOT_report_array[$servicename][$consignment->getDateBooked()] = array($consignment->getAwb());
                        }
                    }


                    //GET LIST OF RETURN PARCELS
                    $consignmentFilter = new ConsignmentFilter();
                    if (trim($orderDateFrom) != '' && trim($orderDateTo) != '')
                        $consignmentFilter->addDateBookedRangeFilter(strtotime($orderDateFrom), strtotime($orderDateTo));

                    $consignmentFilter->addFieldFilter('handling', "RTN" . $key);

                    $consignmentFilter->addGroupByClause("date_booked");
                    $consignmentReturnShipments = $consignmentFilter->getColumnList("count(id) as awb, date_booked");

                    if (count($consignmentReturnShipments) > 0) {
                        foreach ($consignmentReturnShipments as $consignment) {
                            $return_report_array[$servicename][$consignment->getDateBooked()] = array($consignment->getAwb());
                        }
                    }
                }
                if (sizeof($DOT_report_array) > 0 || sizeof($DNOT_report_array) > 0 || sizeof($return_report_array) > 0) {
                    require_once(SETTING_DIR_REMOTE . "Classes/PHPExcel.php");
                    $objPHPExcel = new PHPExcel();

                    // Set properties
                    $objPHPExcel->getProperties()->setCreator("One World Express ")
                            ->setLastModifiedBy("KPI REPORT")
                            ->setTitle("Office 2007 XLSX KPI REPORT")
                            ->setSubject("Office 2007 XLSX KPI REPORT")
                            ->setDescription("This document is generated from the system, generated using PHP classes.")
                            ->setKeywords("office 2007 openxml php")
                            ->setCategory("KPI REPORT");

                    $workSheet = 0;
                    foreach ($DOT_report_array as $keypor => $array0) {

                        $objWorkSheet = $objPHPExcel->createSheet($workSheet);
                        $objPHPExcel->setActiveSheetIndex($workSheet);
                        $objPHPExcel->getActiveSheet()->setTitle('Worksheet' . $workSheet);
                        $array1 = $DNOT_report_array[$keypor];
                        $array2 = $return_report_array[$keypor];
                        $this->exportAjaxEXL($objWorkSheet, $workSheet, $keypor, $array0, $array1, $array2);
                        $workSheet++;
                    }

                    function saveExcelToLocalFile($objWriter) {
                        // make sure you have permission to write to directory
                        $file_name = "";
                        $file_name = '../_assets/kpi_report/kpi_report' . time() . '.xlsx';
                        $filePath = $file_name;
                        $fileUrl = SETTING_MAIN_URL . str_replace("../", "", $file_name);

                        $objWriter->save($filePath);
                        return $fileUrl;
                    }

                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                    $objWriter->setIncludeCharts(TRUE);
                    $response = array(
                        'success' => true,
                        'url' => saveExcelToLocalFile($objWriter)
                    );
                    echo json_encode($response);
                    exit;
                }
            }
        }
    }

    private function exportAjaxEXL($objWorkSheet, $workSheet, $handling, $array0, $array1, $array2) {

        $title = $objWorkSheet->getTitle();

        $orderDateFrom = $this->order_date_from;
        $orderDateTo = $this->order_date_to;

        $startmonth = date("m", strtotime($orderDateFrom));
        $startday = date("d", strtotime($orderDateFrom));
        $startyear = date("Y", strtotime($orderDateFrom));

        $endmonth = date("m", strtotime($orderDateTo));
        $endday = date("d", strtotime($orderDateTo));
        $endyear = date("Y", strtotime($orderDateTo));

        $totalmonth = (int) abs((strtotime($orderDateFrom) - strtotime($orderDateTo)) / (60 * 60 * 24 * 30));

        $excel_column = 1;
        $FontBoldArray = array(
            'font' => array(
                'bold' => true,
                'size' => 10,
                'name' => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => 'thick'
                ),
                'bottom' => array(
                    'style' => 'thick'
                ),
                'left' => array(
                    'style' => 'thick'
                ),
                'right' => array(
                    'style' => 'thick'
                )
            )
        );
        $thin_border = array(
            'borders' => array(
                'top' => array(
                    'style' => 'thin'
                ),
                'bottom' => array(
                    'style' => 'thin'
                ),
                'left' => array(
                    'style' => 'thin'
                ),
                'right' => array(
                    'style' => 'thin'
                )
            )
        );
        $center_text_merge = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );



        $columnname = 'A';
        $chartTableRow = 43;
        $chartDataArray = array();
        $chartDataArray[] = array('', 'DOT', 'DNOT', 'Return');
        $newyear = false;
        for ($i = 0; $i <= $totalmonth; $i++) {
            $totalDOTcount = 0;
            $totalDNOTcount = 0;
            $totalReturncount = 0;
            $startcolumn = $columnname;
            $objWorkSheet->setCellValue($columnname . '5', date("F", strtotime("$orderDateFrom + $i months")));
            $objWorkSheet->setCellValue($columnname . '6', "Date Dispatch");
            $objWorkSheet->getStyle($columnname . '6')->applyFromArray($thin_border);
            ++$columnname;
            $objWorkSheet->setCellValue($columnname . '6', "Service");
            $objWorkSheet->getStyle($columnname . '6')->applyFromArray($thin_border);
            ++$columnname;
            $objWorkSheet->setCellValue($columnname . '6', "DOT");
            $objWorkSheet->getStyle($columnname . '6')->applyFromArray($thin_border);
            ++$columnname;
            $objWorkSheet->setCellValue($columnname . '6', "Delivere + 1 Day");
            $objWorkSheet->getStyle($columnname . '6')->applyFromArray($thin_border);
            ++$columnname;
            $objWorkSheet->setCellValue($columnname . '6', "Return");
            $objWorkSheet->getStyle($columnname . '6')->applyFromArray($thin_border);

            $objWorkSheet->mergeCells($startcolumn . '5:' . $columnname . '5');
            $objWorkSheet->getStyle($startcolumn . '5:' . $columnname . '5')->applyFromArray($center_text_merge);
            $objWorkSheet->getStyle($startcolumn . '5:' . $columnname . '5')->applyFromArray($border);
            ++$columnname;

            $nextmonth_date = date('Ymd', mktime(0, 0, 0, $startmonth + $i, 1, $startyear));

            $month_year = date("Y", strtotime($nextmonth_date));
            $month_start = date("m", strtotime($nextmonth_date));
            $monthdays = date("t", strtotime($nextmonth_date));

            /* $currentmonth = $startmonth + $i;

              if($currentmonth >= 12 && $i != 0)
              {
              if($month_start == 12)
              {
              $startyear++;
              $month_start = $i;
              }
              else
              {
              $month_start = $startmonth + $i;
              }
              }
              else
              {
              $month_start = $startmonth + $i;
              }
             */

            $data_row_number = 7;
            for ($j = 1; $j <= $monthdays; $j++) {
                $other_column = $startcolumn;



                // add the date to the column
                $currentDate = $month_year . "-" . str_pad($month_start, 2, '0', STR_PAD_LEFT) . "-" . str_pad($j, 2, '0', STR_PAD_LEFT);



                $objWorkSheet->getStyle($other_column . $data_row_number)->applyFromArray($thin_border);
                $objWorkSheet->getColumnDimension($other_column)->setWidth(15);
                $objWorkSheet->setCellValue($other_column++ . $data_row_number, $month_year . "-" . str_pad($month_start, 2, '0', STR_PAD_LEFT) . "-" . str_pad($j, 2, '0', STR_PAD_LEFT));
                $objWorkSheet->getStyle($other_column . $data_row_number)->applyFromArray($thin_border);
                $objWorkSheet->getColumnDimension($other_column)->setWidth(35);
                $objWorkSheet->setCellValue($other_column++ . $data_row_number, $handling);
                $objWorkSheet->getStyle($other_column . $data_row_number)->applyFromArray($thin_border);
                $objWorkSheet->setCellValue($other_column++ . $data_row_number, $array0[$currentDate][0]);
                $objWorkSheet->getStyle($other_column . $data_row_number)->applyFromArray($thin_border);
                $objWorkSheet->setCellValue($other_column++ . $data_row_number, $array1[$currentDate][0]);
                $objWorkSheet->getStyle($other_column . $data_row_number)->applyFromArray($thin_border);
                $objWorkSheet->setCellValue($other_column++ . $data_row_number, $array2[$currentDate][0]);

                $totalDOTcount += $array0[$currentDate][0];
                $totalDNOTcount += $array1[$currentDate][0];
                $totalReturncount += $array2[$currentDate][0];

                $data_row_number++;
            }


            //CHART START HERE
            $chartTableColumn = "B";

            $chartDataArray[] = array(date("F", strtotime("$orderDateFrom + $i months")), $totalDOTcount, $totalDNOTcount, $totalReturncount);

            $objWorkSheet->getStyle("C42")->applyFromArray($thin_border);

            $objWorkSheet->getStyle("D42")->applyFromArray($thin_border);

            $objWorkSheet->getStyle("E42")->applyFromArray($thin_border);
            $objWorkSheet->getStyle($chartTableColumn . $chartTableRow)->applyFromArray($thin_border);
            $chartTableColumn++;
            $objWorkSheet->getStyle($chartTableColumn . $chartTableRow)->applyFromArray($thin_border);
            $chartTableColumn++;
            $objWorkSheet->getStyle($chartTableColumn . $chartTableRow)->applyFromArray($thin_border);
            $chartTableColumn++;
            $objWorkSheet->getStyle($chartTableColumn . $chartTableRow)->applyFromArray($thin_border);
            $chartTableColumn++;

            $chartTableRow++;
        }
        $totalrow = $chartTableRow - 43;
        $chartTableRow = $chartTableRow - 1;
        $objWorkSheet->fromArray($chartDataArray, NULL, 'B42');


        //  Set the Labels for each data series we want to plot
        $dataseriesLabels = array(
            new PHPExcel_Chart_DataSeriesValues('String', $title . '!$C$42', NULL, 1), //	DOT
            new PHPExcel_Chart_DataSeriesValues('String', $title . '!$D$42', NULL, 1), //	DNOT
            new PHPExcel_Chart_DataSeriesValues('String', $title . '!$E$42', NULL, 1), //	RETURN
        );
        //
        //
			//  Set the X-Axis Labels
        $xAxislastRow = $title . '!$B$43:$B$' . $chartTableRow;

        $xAxisTickValues = array(
            new PHPExcel_Chart_DataSeriesValues('String', $xAxislastRow, NULL, $totalrow), //  Jan to Dec
        );

        //  Set the Data values for each data series we want to plot
        $yDotLastColumn = $title . '!$C$43:$C$' . $chartTableRow;
        $yDNotLastColumn = $title . '!$D$43:$D$' . $chartTableRow;
        $yReturnLastColumn = $title . '!$E$43:$E$' . $chartTableRow;
        $dataSeriesValues = array(
            new PHPExcel_Chart_DataSeriesValues('Number', $yDotLastColumn, NULL, $totalrow),
            new PHPExcel_Chart_DataSeriesValues('Number', $yDNotLastColumn, NULL, $totalrow),
            new PHPExcel_Chart_DataSeriesValues('Number', $yReturnLastColumn, NULL, $totalrow),
        );



        //  Build the dataseries
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
                PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
                range(0, count($dataSeriesValues) - 1), // plotOrder
                $dataseriesLabels, // plotLabel
                $xAxisTickValues, // plotCategory
                $dataSeriesValues        // plotValues
        );

        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
        //  Set the series in the plot area
        $plotarea = new PHPExcel_Chart_PlotArea(null, array($series));
        //	Set the chart legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        $title = new PHPExcel_Chart_Title('KPI REPORT');


        $chart = new PHPExcel_Chart(
                'chart1', // name
                $title, // title
                $legend, // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                null, // xAxisLabel
                $yAxisLabel  // yAxisLabel
        );
        //	Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('H43');
        $chart->setBottomRightPosition('Q56');
        //	Add the chart to the worksheet
        $objWorkSheet->addChart($chart);
    }

    protected function renderHead() {
        ?>
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <script src="../js/bootstrap-select.min.js"></script>
        <script>
            $(document).ready(function () {


                $("#btnexport").click(function () {
                    $("#form_action").val("export");
                    $("#bookingForm").submit();
                });
                $("#btn_search").click(function () {
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });


                $("#order_date_from").datepicker({dateFormat: "d M yy"});
                $("#order_date_to").datepicker({dateFormat: "d M yy"});
            });

            function getServices(serviceValue)
            {
                $('#service_loader').show();
                var carrier = $('#courier').val();
                $.ajax({method: "POST",
                    url: "kpi_report.php",
                    data: {action: 'GET_SERVICE_LIST', carrier: carrier}})
                        .done(function (reponseData) {
                            $('#service_loader').hide();
                            $('#service_content').show();
                            $('#service_content').html(reponseData);
                        })
                        .fail(function (data) {
                            alert("There is some error, Please reload the page.");
                        });
                return false;
            }

            function noSpeciatCharacter(e)
            {
                var unicode = e.charCode ? e.charCode : e.keyCode
                //alert(unicode);
                if (unicode != 8)
                {
                    if ((unicode == 31) || (unicode >= 33 && unicode <= 35) || (unicode >= 39 && unicode <= 43) || (unicode >= 36 && unicode <= 38) || unicode == 163 || unicode == 94 || unicode == 64 || unicode == 126) //if not a number
                        return false //disable key press
                }
            }

            function ajaxBaseReport()
            {

                var order_date_from = $('#order_date_from').val();//order_date_from
                var order_date_to = $('#order_date_to').val();//order_date_to
                var courier = $('#courier').val();//courier
                var service = $('#service').val();//service
                var func = 'EXPORT'//service

                //var form_data = $("#adminForm").serialize();
                //	form_data.append('FUNCTION', 'EXPORT');
                $("#btn_save_new").attr("disabled", "disabled");

                if ($("#basic-ajax-div-msg").hasClass("alert-success"))
                    $("#basic-ajax-div-msg").removeClass("alert-success");

                $("#basic-ajax-div-msg").addClass("alert-info");
                $("#basic-ajax-div-msg").html("<strong>Please wait while we are dealing your request.</strong>");
                $("#basic-ajax-div-msg").show();


                var CSVDATA = '';
                $.ajax({
                    url: "kpi_report.php",
                    type: "POST",
                    async: false,
                    dataType: "json",
                    data: {
                        FUNCTION: func,
                        order_date_from: order_date_from,
                        order_date_to: order_date_to,
                        courier: courier,
                        service: service
                    },
                    success: function (data) {

                        $("#weight_discrepancy").removeAttr("disabled");
                        $("#basic-ajax-div-msg").removeClass("alert-info");
                        $("#basic-ajax-div-msg").addClass("alert-success");
                        $("#basic-ajax-div-msg").html('Please click on this link to <a target="_blank" class="btn btn-primary" href="' + data.url + '" > ' + ' Download </a>');
                        $("#basic-ajax-div-msg").show();
                    }
                });

                $("#btn_save_new").removeAttr("disabled");

            }

            function change_msg() {
                $("#basic-ajax-div-msg").html("");
                $("#weight_discrepancy").attr("disabled", "disabled");
                if ($("#basic-ajax-div-msg").hasClass("alert-success"))
                {
                    $("#basic-ajax-div-msg").removeClass("alert-success");
                }
                $("#basic-ajax-div-msg").addClass("alert-info");
                $("#basic-ajax-div-msg").html("<strong>Please wait while we are dealing your request.</strong>");
                $("#basic-ajax-div-msg").css("display", "block");


            }
        </script>
        <?php
    }

    public function renderBody() {
        ?>

        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-save"></i>KPI Report </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div style="min-height:200px; "  data-rail-color="blue" data-handle-color="blue">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                    <input class="form-control" name="order_date_from" id="order_date_from" type="text" value="<?php echo @$this->order_date_from; ?>" placeholder="Choose Date From">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                    <input class="form-control" name="order_date_to" id="order_date_to" type="text" value="<?php echo @$this->order_date_to; ?>" placeholder="Choose Date To">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-paper-plane-o "></i> </span>
                                    <?
                                    $this->couriersDropdown();
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div id="service_loader" style="text-align:center"; class="display-none">
                                    Services Loading <img src="../_assets/admin/layout/img/loading.gif" alt="loading"/>
                                </div>
                                <div id="service_content" class="display-none">
                                    <div class="input-group">
        <?php // $this->serviceCodeDropdown();  ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="text-align:center;">
                            <div class="form-group">
                                <button  type="button" class="btn btn-primary" id="btn_save_new" name="btn_save_new" value="Export CSV" onclick="ajaxBaseReport();"> Export CSV </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="alert alert-info col-md-12" style="text-align:center; display:none; font-size:18px;" id="basiajaxdiv">
                            <div class="col-md-4"></div>
                            <div class="col-md-1" id="countrecords" style="text-align:right;"></div>
                            <div class="col-md-1" >out of </div>
                            <div class="col-md-3" id="totalrecords"  style="text-align:left;"></div>
                            <div class="col-md-5"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="alert col-md-12" style="text-align:center; display:none;" id="basic-ajax-div-msg"></div>
                    </div>
                    <div style="clear:both;"></div>

                    <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
                </div>
            </div>
        </div>

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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

    private function serviceCodeDropdown($carrier) {


        $filter = new ServiceFilter();
        $filter->addCarrierFilter(@$carrier);
        $serviceList = $filter->getColumnList("code, name");
        echo '<select id="service" name="service[]" multiple="multiple" style="width:400px;"  class="form-control" rel="tooltip" title="Select Service">';
        echo '<option value="">Select Service</option>';
        foreach ($serviceList as $serviceData) {
            $selected = ($service == $serviceData->getCode()) ? " selected" : "";
            echo '<option' . $selected . ' value="' . $serviceData->getCode() . '">' . $serviceData->getName() . '</option>';
        }

        echo '</select>';
    }

    private function couriersDropdown() {
        if (isset($_POST["courier"])) {
            $courier = @$_POST["courier"];
            $_SESSION['BOOKING']['COURIER'] = $courier;
        } elseif (isset($_SESSION['BOOKING']['COURIER'])) {
            $courier = $_SESSION['BOOKING']['COURIER'];
        }
        $filter = new ServiceFilter();
        $filter->getUniqueCarrierfilter();
        $serviceList = $filter->getColumnList(" carrier ");
        echo '<select id="courier" name="courier"  class="selectpicker" onchange="getServices(this);"  rel="tooltip" data-live-search-style="startsWith" data-live-search="true" data-original-title="Select Carrier">';
        echo '<option value="">Please Select Courier</option>';
        foreach ($serviceList as $serviceData) {
            $selected = ($courier == $serviceData->getCarrier()) ? " selected" : "";
            echo '<option' . $selected . ' value="' . $serviceData->getCarrier() . '">' . $serviceData->getCarrier() . '</option>';
        }

        echo '</select>';
    }

    /*     * *
     * Controller logic goes here
     */
}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
