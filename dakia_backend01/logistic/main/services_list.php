<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'iaddress.class',
    'carrier.class',
    'carrierfilter.class',
    'consignment.class',
    'consignmentfilter.class',
    'services.class',
    'servicefilter.class',
    'servicecountrytime.class',
    'servicecountrytimefilter.class',
    'country.class',
    'countryfilter.class'
]);

class Page extends BasePage {

    // status colours
    private $colours_map = array("active" => "state_valid",
        "inactive" => "state_held"
    );
    private $service_filter;
	private $searcfield;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // user must be CLIENT
        if(!Permissions::checkFilePermission('services_list.php'))
                    util_redirect ("index.php");
        $this->breadCrumb['data']   =   array(
                                    'index.php'=>Translation::GetCaption("HOME"),
                                    "Service List"
                                );
     	$this->userSesstion =   $user = SessionManager::getUser();
//	if (!in_array($user->getUserType(), array(User::USER_TYPE_ADMIN, User::USER_TYPE_FINANCE, User::USER_TYPE_ACCOUNT))) 
//        {
//            util_redirect("index.php");
//        }
//        
        $service_list = "";
         if(isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_services_excel') {

            if (PHP_SAPI == 'cli')
                die('This should only be run from a Web Browser');

            /** Include PHPExcel */
            require_once '../includes/library/PHPExcel-1.8/PHPExcel.php';
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("[One World Express] Mruga Patel")
                ->setTitle("Services")
                ->setSubject("Smart Track System Services")
                ->setDescription("Smart Track System Services")
                ->setKeywords("Shipping,Services")
                ->setCategory("Shipping");

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:O1');

            $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
            $activeSheet->mergeCells('A1:O1');
            $activeSheet->setCellValue('A1', "Smart Track Services");
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('size' => 16, 'bold' => true),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);


            $activeSheet->setCellValue('A2', "Logo");
            $activeSheet->setCellValue('B2', "Service Name");
            $activeSheet->setCellValue('C2', "Origin Country");
            $activeSheet->setCellValue('D2', "Service Code");
            $activeSheet->setCellValue('E2', "Service Type");
            $activeSheet->setCellValue('F2', "Weight");
            $activeSheet->setCellValue('G2', "Volumetric Weight");
            $activeSheet->setCellValue('H2', "Length");
            $activeSheet->setCellValue('I2', "Width");
            $activeSheet->setCellValue('J2', "Height");
            $activeSheet->setCellValue('K2', "Girth / Cubic Meter");
            $activeSheet->setCellValue('L2', "Girth / Cubic Meter Formula");
            $activeSheet->setCellValue('M2', "Mail / Courier");
            $activeSheet->setCellValue('N2', "Parcel / Packet");
            $activeSheet->setCellValue('O2', "Type");
            $activeSheet->setCellValue('P2', "Active");
            $activeSheet->setCellValue('Q2', "Mail Type");
            $activeSheet->setCellValue('R2', "Mail Option");

            $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->applyFromArray(array('font' => array('bold' => true),'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);

            $serviceFilterObj = new ServiceFilter();
            $serviceFilterObj->addFilter("AND ser.active != '0'");
            $serviceData = $serviceFilterObj->getServiceViewList("ser.mail_type, ser.mail_option,ser.name,ser.code,ser.from_weight,ser.to_weight,ser.is_untrack,ser.active,ser.max_length,ser.max_width, ser.validation_type, ser.service_type, ser.max_height,ser.max_volumetric_weight,ser.is_untrack,ser.girth, ser.girth_formula, ser.wieght_type, c.name as service_country, ca.logo as carrier_logo, ca.carrier AS carrier_name");

            if (count($serviceData) > 0) {
                $rowNum = 3;
                foreach($serviceData as $serviceObj){


                    $gdImage = imagecreatefrompng('../images/carrierlogo/thumbnail/owe_50_'.$serviceObj->getCarrierLogo());
                    // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setName('Sample image');
                    $objDrawing->setDescription('Sample image');
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A'.$rowNum);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                    $activeSheet->setCellValue('B'.$rowNum, $serviceObj->getCarrierName() . " ".$serviceObj->getName());
                    $activeSheet->setCellValue('C'.$rowNum, $serviceObj->getServiceCountry());
                    $activeSheet->setCellValue('D'.$rowNum, $serviceObj->getCode());
                    $activeSheet->setCellValue('E'.$rowNum, ($serviceObj->getServiceType() == "C" ? "Collection" : "Dispatch"));
                    $activeSheet->setCellValue('F'.$rowNum, $serviceObj->getFromWeight() . " - " . $serviceObj->getToWeight());
                    $activeSheet->setCellValue('G'.$rowNum, $serviceObj->getMaxVolumetricWeight());
                    $activeSheet->setCellValue('H'.$rowNum, $serviceObj->getMaxLength());
                    $activeSheet->setCellValue('I'.$rowNum, $serviceObj->getMaxWidth());
                    $activeSheet->setCellValue('J'.$rowNum, $serviceObj->getMaxHeight());
                    $activeSheet->setCellValue('K'.$rowNum, $serviceObj->getGirth());
                    $activeSheet->setCellValue('L'.$rowNum, $serviceObj->getGirthFormula());
                    $activeSheet->setCellValue('M'.$rowNum, $serviceObj->getValidationType());
                    $activeSheet->setCellValue('N'.$rowNum, ($serviceObj->getWieghtType() == 1 ? 'Parcel' : 'Shipment'));
                    $activeSheet->setCellValue('O'.$rowNum, ($serviceObj->getIsUntrack() == 1 ? 'Untracked' : 'Tracked'));
                    $activeSheet->setCellValue('P'.$rowNum, ($serviceObj->getActive() == 1 ? 'Yes' : 'No'));
                    $activeSheet->setCellValue('Q'.$rowNum, ($serviceObj->getMailType()));
                    $activeSheet->setCellValue('R'.$rowNum, ($serviceObj->getMailOption()));
                    
                    $activeSheet->getRowDimension($rowNum)->setRowHeight(50);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowNum.':R'.$rowNum)->applyFromArray(array('alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
                    $rowNum++;
                }
            }

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Smart Track Carriers');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            foreach(range('B','P') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $fileName = "services_".time().".xlsx";

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }


         if(isset($this->form_vars['func']) && $this->form_vars['func'] == "GET_SERVICE_COUNTRY"){
             $serviceid = $this->form_vars['serviceid'];
             if($serviceid > 0)
             {
                $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
                $serviceCountryTimeFilter->addServiceTableJoin();
                $serviceCountryTimeFilter->addFilter(' `service_country_ttime`.id_service ='.DbAccess3::escape($serviceid));
                $serviceCountryTimeFilter->setRowsPerPage(300);
                $serviceCountry = $serviceCountryTimeFilter->getList();
                $output = "";
                  $output .= '<table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Countries List</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                    <tr>';
                                    $i = 0;

                    foreach ($serviceCountry as $sercoun) {
                        $serCountryIso = $sercoun->getIdCountry();
                        $countryFilter = new CountryFilter();
                        $countryFilter->addFilter(" id = ".$serCountryIso);
                        $countryList = $countryFilter->getList();
                        if(count($countryList) > 0)
                        {
                            $countryName = $countryList[0]->getName();
                        }
//                        else {                 
//                            $countryName = $agentObj->getCountryIsoCode();
//                        }
                       if($i % 4 == 0)
                       {
                        $output .= '</tr>';
                        $output .= '<tr>';
                       }
                            $filename = str_replace("|","-",$serviceCountry[0]->getCode());
                            $filelink = "../_assets/service_sample_label/" . $filename . ".pdf" ;
                            if(file_exists($filelink))
                            {
                                    $link = '<a href="'.$filelink.'" target="_blank"><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>';
                                    $output .= '<td>' . $link . '</td>';
                                    $i++;
                            }
                            else
                            {
                                    $link=  '<a href="#" onclick="fileNotFound();" ><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>' ;
                                    $output .= '<td>' . $link . '</td>';
                                    $i++;
                            }

                    }
                    echo $output;
             }
             exit;
         }
        if(isset($_GET['serviceAjax']) && $_GET['serviceAjax'] == "service_ajax"){

            $user = Sessionmanager::getUser();
            $service_filter = new ServiceFilter();
            $service_filter->addcoulmnFilter(" ser.deletedq","0");
            $service_filter->addCodeNotEmptyFilter();
            
            /*
            * Column filter
            * For search
            */
           if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                if(!empty($this->form_vars['search_Name']) || !empty($this->form_vars['search_Code']) || !empty($this->form_vars['search_Country']) || !empty($this->form_vars['search_Type']) || !empty($this->form_vars['search_WieghtType'])){
                    unset($service_filter);
                    $service_filter = new ServiceFilter();
                    $service_filter->addcoulmnFilter(" ser.deletedq","0");
                    $service_filter->addCodeNotEmptyFilter();
                }

               $searchName = $this->form_vars['search_Name'];
               if (!empty($searchName))
                   $service_filter->addFieldLikeFilter('ser.name', $searchName);

               $searchCarrierId = $this->form_vars['search_Carrier_id'];
               if (!empty($searchCarrierId))
                   $service_filter->addFieldFilter('ser.carrier_id', $searchCarrierId);


               $searchCode = $this->form_vars['search_Code'];
               if (!empty($searchCode))
                   $service_filter->addFieldFilter('ser.id', $searchCode);

               $searchType = $this->form_vars['search_Type'];
               if (!empty($searchType))
                   $service_filter->addFieldLikeFilter('ser.type', $searchType);

                $searchtracked = $this->form_vars['search_tracked'];
                if (($searchtracked) != '') {
                    $service_filter->addFieldFilter('ser.is_untrack', $searchtracked);
                }

                $searchstatus = $this->form_vars['search_Active'];
                if (($searchstatus) != '') {
                    $service_filter->addFieldFilter('ser.active', $searchstatus);
                }

                $searchiscustomized = $this->form_vars['search_is_customized'];
                if (($searchiscustomized) != '') {
                    $service_filter->addFieldFilter('ser.is_customized', $searchiscustomized);
                }

                $searchservicetype = $this->form_vars['search_service_type'];
                if (($searchservicetype) != '') {
                    $service_filter->addFieldFilter('ser.service_type', $searchservicetype);
                }
                
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
                if ($dataTableColumnName == "Logo") {
                    $dataTableColumnName = "ser.name";
                }
                else if($dataTableColumnName == "Tracked")
                {
                    $dataTableColumnName = "ser.is_untrack";
                }
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $service_filter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                $service_filter->AddOrderBy("carrier.carrier", true);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            
            $service_filter->addJoin('carrier', " carrier.id = ser.carrier_id ", "INNER");
            $service_filter->addJoin('country', " country.id = ser.origin_country ", "INNER");
            $iTotalRecords = $service_filter->getPagingCount();
            
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $service_filter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $service_filter->setOffset($iDisplayStart);
            $new_consignment_label = "(select label_file from consignment where service_id = ser.id and label_file <> '' and number_pieces= '1' order by id desc limit 1 ) as 'shipment_label'";
            $serviceObjs = $service_filter->getPagingList("ser.*, carrier.carrier 'carrier_name',carrier.logo 'carrier_logo',country.iso 'carrier_country_iso',country.name 'carrier_country_name', ".$new_consignment_label);
            
            
            $serviceDataArr = array();

            foreach ($serviceObjs as $serviceObj) {
                $serviceArr = array();
                $cnimag = '<img src="../assets/global/img/flags/'.  strtolower($serviceObj->getCarrierCountryIso()).'.png" title="' . $serviceObj->getCarrierCountryName() . '" alt="' . $serviceObj->getCarrierCountryName() . '">';
                $imag =        '<img src="https://logistics.funsocio.com/images/carrierlogo/thumbnail/owe_16_' . $serviceObj->getCarrierLogo() . '" title="' . $serviceObj->getCarrierName() . '" alt="' . $serviceObj->getCarrierName() . '">';
                $serviceArr['logo'] =   $imag ." ". $serviceObj->getCarrierName();

                    $serviceArr['is_customized'] = ($serviceObj->getIsCustomized() == 1 ? '<center><span class="label label-sm label-success">Product</span></center>' : '<center><span class="label label-sm label-danger">Service</span></center>');
                    $serviceType = '';
                    if($serviceObj->getServiceType() == "D") {
                        $serviceType = '<center><span class="label label-sm label-success">Dispatch</span></center>';
                    } else if($serviceObj->getServiceType() == "C") {
                        $serviceType = '<center><span class="label label-sm label-danger">Collection</span></center>';
                    } else if($serviceObj->getServiceType() == "DO") {
                        $serviceType = '<center><span class="label label-sm label-info">Drop off</span></center>';
                    }
                    $serviceArr['service_type'] = $serviceType;
                    $serviceArr['name'] = $cnimag.' '.strtoupper($serviceObj->getName()).' ['.$serviceObj->getCode().']';
                    $serviceArr['wieght_type']      =   number_format($serviceObj->getFromWeight(),2) . " - " . number_format($serviceObj->getToWeight(),2);
                    $serviceArr['tracked']          =   ($serviceObj->getIsUntrack() == 0 ? '<center><span class="label label-sm label-success">Tracked</span></center>' : '<center><span class="label label-sm label-danger">Untracked</span></center>');
                    $serviceArr['active']           =   ($serviceObj->getActive() == 1 ? '<center><span class="label label-sm label-success">Yes</span></center>' : '<center><span class="label label-sm label-danger">No</span></center>');

                    $labelFileName = '../_assets/service_sample_label/'.$serviceObj->getCode().'.pdf';

                    
                if(!file_exists($labelFileName) && trim($serviceObj->getShipmentLabel())!= ''){
                    $labelConsignmentLink  = "../_assets/pdf/".$serviceObj->getShipmentLabel();
                    if(file_exists($labelConsignmentLink))
                    {
                        file_put_contents('../_assets/service_sample_label/'.$serviceObj->getCode().'.pdf', file_get_contents($labelConsignmentLink) );
                    }
                }

                $labelFileSample    = file_exists($labelFileName)?($labelFileName):'404.php';
                $serviceArr['actions'] = '<div class="dropdown">
                                                <a href="#" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </a>
                                             <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">';

                    if(Permissions::checkFilePermission('service_edit')) {
                    $serviceArr['actions'] .= '<li>
                                                        <a class="dropdown-item" href="services_detail.php?id='. $serviceObj->getId() .'" >
                                                            Edit
                                                        </a>
                                                    </li>';
                    }
                    if(Permissions::checkFilePermission('services_view')) {
                        $serviceArr['actions'] .= '<li>
                                                        <a class="dropdown-item" href="services_view.php?id='. $serviceObj->getId() .'" title="Edit Service">
                                                            View
                                                        </a>
                                                    </li>';
                    }

                    $serviceArr['actions'] .='</ul> </div>';
                    $serviceDataArr []      = $serviceArr;
            }
            $serviceDataArrJson['data'] = $serviceDataArr;
            $serviceDataArrJson['draw'] = $sEcho;
            $serviceDataArrJson['recordsTotal'] = $iTotalRecords;
            $serviceDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($serviceDataArrJson);
            die;

        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <script type="text/javascript">


            function fileNotFound()
            {
                $('#view-country-popup').modal('hide');
                swal("","Label is under developement Proceses. Will be available Soon.", "info");
                return false;
            }

        </script>
        <script type="text/javascript">
             var DataTableFun = function () {
                var handleDataTable = function () {
                    var grid = new Datatable();
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
                                "url": "services_list.php?serviceAjax=service_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "logo"},
                                {"data": "name"},
                                {"data": "wieght_type", "bSortable": false},
                                {"data": "is_customized"},
                                {"data": "service_type"},
                                {"data": "tracked"},
                                {"data": "active"}
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
                $(document).on('click', '.viewcountry', function () {
                        var e = $(this);
                        var servicename    =   e.data('servicename');
                        var serviceid         =   e.data('serviceid');
                        var url             =   e.data('carrierload');
                        var action          =   e.data('action');

                        $("#service_name").html(servicename);
                        $.post(url, {func: action , servicename: servicename,serviceid:serviceid}, function (d) {
                            $("#carrier-logs-display").html(d);
                        });
                    });

                // Handle services download excel
                $("#export_services_excel").click(function(){
                   $("#frm_export_services_excel").submit();
                });
            });
        </script>
        <?php
    }

     protected function addPagelavelCss() {
            ?>
            <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
            <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
            <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
         <link href="<?=SETTING_MAIN_ASSETS;?>css/common.css" rel="stylesheet" type="text/css" />
            <?php
        }

        public function addPagelavelJs() {
            ?>
            <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
            <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
            <script src="../js/bootstrap-select.min.js"></script>
            <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
            <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
            <?php
        }

    /*     * *
     * Content View
     */

    protected function renderBody() {
      foreach ($this->form_vars as $key=>$val)
		{
			$$key = $val;
		}
		 ?>
        <form method="post" action="services_list.php" id="frm_export_services_excel">
            <input type="hidden" name="func" value="download_services_excel" />
        </form>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Services List</h4>

                        <div class="flex-shrink-0">
                            <?php if(Permissions::checkFilePermission('services_detail.php')) {   ?>
                             <a href="services_detail.php" class="btn btn-info"  > Add New Service</a>
                            <?php } ?>
                            <!--                            <div class="form-check form-switch form-switch-right form-switch-md">-->
                            <!--                                <label for="card-tables-showcode" class="form-label text-muted">Show Code</label>-->
                            <!--                                <input class="form-check-input code-switcher" type="checkbox" id="card-tables-showcode">-->
                            <!--                            </div>-->
                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <p class="text-muted mb-4"><code><?php $this->flashMsg->display(); ?></code> </p>

                        <div class="live-preview">
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap table-striped-columns mb-0" id="manage-data-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">Actions</th>
                                        <th scope="col">Carrier Logo</th>
                                        <th scope="col">Service Name/ Code</th>
                                        <th scope="col">Weight</th>
                                        <th scope="col">Service/Product</th>
                                        <th scope="col">Service Type</th>
                                        <th scope="col">Tracked/Untracked</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="modal fade" tabindex="-1" role="dialog" id="view-country-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="service_name"></span> Countries List</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" >
                            <div class="col-md-12" id="carrier-logs-display">

                            </div>
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

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?> 