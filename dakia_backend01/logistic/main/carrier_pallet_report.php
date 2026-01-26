<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

include_classes([
    'ivisualcomponent', 'ddl.inc'
], 'library');

include_classes([
    'invoices.class',
    'invoicesfilter.class',
    'parcel.class',
    'parcelfilter.class'
]);

class Page extends BasePage
{
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

    protected function init()
    {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),'Carrier Pallet Report'
        );
        $this->user = SessionManager::getUser();

        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $user_account_id = $this->form_vars['user_account_id'];
            $consignmentList = array();
            if (!empty($user_account_id)) {
                $return = array();
                $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                $userServicesRoutingFilter = new UserServicesRoutingFilter();
                $userServicesRoutingFilter->addFilterIn("    user_account_id", $userAccountArry);
                $userServiceObj = $userServicesRoutingFilter->getColumnList(" service_id", false, true);
                $serviceIds = array();
                if (count($userServiceObj) > 0) {
                    foreach ($userServiceObj as $service) {
                        $serviceIds[] = $service->getServiceId();
                    }
                }
            }
            $serviceFilterObj = new ServiceFilter();
            if (!empty($serviceIds)) {
                $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            }
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
                $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
            }

            $return['services_option'] = $service_option;
            $return['carrier_option'] = $carrier_option;
            echo json_encode($return);
            die;
        }

        if (isset($_POST) && !empty($_POST)) {

            $this->pallet_id = (!empty($_POST['pallet_id']) ? $_POST['pallet_id'] : '');
            $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : '');
            $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : '');
            $this->startDate = (!empty($_POST['from_date']) ? $_POST['from_date'] : '');
            $this->endDate = (!empty($_POST['to_date']) ? $_POST['to_date'] : '');
            $user_account_id = $this->user->getUserAccountId();
            $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
            if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                $user_account_id = '';
                $allouedAcccounts = [];
            }
            $parcelReportObj = new ParcelFilter();
            $carrierPalletObj = $parcelReportObj->getCarrierPalletReport($this->startDate, $this->endDate, $this->pallet_id, $this->carrierId, $this->serviceId, $allouedAcccounts);
            if (!empty($carrierPalletObj)) {
                $objPHPExcel = new PHPExcel();
                $objDataSheet = $objPHPExcel->getActiveSheet();
                $objDataSheet->setTitle('Carrier Pallet Report');

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
                $styleForGrandTotal = array(
                    'font' => array(
                        'bold' => true,
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
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);

                $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($headingArray);
                $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleForReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Carrier Pallet Report");
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Country Name");
                $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Carrier Name");
                $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Pallet No");
                $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Total Bag");
                $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Total Shipment");
                $objPHPExcel->getActiveSheet()->SetCellValue('F2', "Weight (Kg)");
                $objPHPExcel->getActiveSheet()->SetCellValue('G2', "Avg.Weight");
                $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
                $count = '3';
                $grandTotalWeight = 0;
                $grandTotalShipment = 0;
                $grandTotalBag = 0;
                $grandTotalAvgWeight = 0;
                $palletNo = [];
                foreach ($carrierPalletObj as $data) {
                    $avgWeight = '';
                    $palletNo[] = $data->getQty();
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $data->getName());
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, $data->getCarrier());
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $data->getQty());
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $data->getPweight());
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $data->getId());
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $data->getWeight());
                    $avgWeight = $data->getWeight() / $data->getId();
                    $grandTotalWeight = $grandTotalWeight + $data->getWeight();
                    $grandTotalShipment = $grandTotalShipment + $data->getId();
                    $grandTotalBag = $grandTotalBag + $data->getPweight();
                    $grandTotalAvgWeight = $grandTotalAvgWeight + $avgWeight;
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $count, number_format($avgWeight, 2));
                    $count++;
                }
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, 'Grand Total');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $grandTotalBag);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $grandTotalShipment);
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $grandTotalWeight);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $count, number_format($grandTotalAvgWeight, 2));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $count . ':G' . $count)->applyFromArray($styleForGrandTotal);
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=carrier_pallet_report' . date('d-m-Y') . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;
            } else {
                $this->noRecord = true;
                $_POST['to_date'] = '';
                $_POST['from_date'] = '';
                $this->serviceId = '';
                $this->form_vars["pallet_id"] = '';
            }

        }

    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>

        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>


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
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
                var selected = '<?php echo (is_array($this->carrierId))  ? $this->carrierId : '';?><?php echo is_integer((int)$this->carrierId) ? (int)$this->carrierId : '';?>';
                get_carriers(userAccountId, selected);

                setTimeout(function () {
                    $('.msg').remove();
                }, 3000);
//                $('.download_file').click(function () {
//                    $('#download_file').val('download_excel');
//                    $('#admin_form').submit();
//                    setTimeout(function () {
//                        $('#download_file').val('');
//                    }, 3000);
//                });

                $('#user_account_id').change(function () {
                    var userAccountId = $(this).val();
                    get_carriers(userAccountId);
                });
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    change_carriers(carrierId);

                });


            });

            function get_carriers(userAccountId) {
                $.ajax({
                    type: "POST",
                    url: "label_generation_report.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId},
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
                $('#service_id').val([]);
                $('#service_id').select2();

            }

        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    Carrier Pallet Report
                </div>
            </div>
            <div class="portlet light bordered">

                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <?php if (!empty($this->noRecord) && !empty($_POST)) { ?>
                    <div class="row msg">
                        <div class="col-md-12 alert alert-danger">No Record Found.</div>
                    </div>
                <?php } ?>
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form"
                          enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                 
<div class="has-float-label input-icon right">

                                
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018"
                                     data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from"
                                           value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : ''); ?>"
                                           data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to"
                                           value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']): ''); ?>"
                                           data-original-title="" title="">
                                    <input type="hidden" class="" name="download_file" id="download_file" value="">
                                </div><label>Date Ranges </label>

                                </div></div></div>
                           
                            <div class="col-md-3">
                              
                                <div class="form-group">

                                    <div class="has-float-label input-icon right"> <i
                                                    class="fa fa-credit-card"></i>
                                        <input type='text' name='pallet_id' id='pallet_id' placeholder="Pallet No"
                                               class="form-control" maxlength="30" style=""
                                               value="<?php echo @$this->form_vars["pallet_id"] ?>"/>
                                               <label for="pallet_id">Pallet No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                <div class="form-group">
                                    <div class="has-float-label"> 
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", 'carriers'); ?> <label class="label-account">Select Carrier</label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                    <div class="has-float-label"> 
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control"', "", $dd_id = 'service_id'); ?>
                                        <label class="label-account">Select Service</label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label class="control-label">&nbsp;</label><br>
                                <!--                                <button type="submit" id="btnSearch" class="btn btn-primary btn_save" > Export PDF</button>
                                                                <button type="submit"id="btnexport"  style="" class="btn yellow btn_save">Export Mawb/Sacnned Excel</button>-->
                                <button type="submit" id="btnexportmawb" class="btn btn-primary btn_save">Download
                                    Excel
                                </button>
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
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead()
    {
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