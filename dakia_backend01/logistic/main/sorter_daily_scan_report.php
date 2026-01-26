<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'optimussorter.class'
    
    ]);

class Page extends BasePage {

    private $licencePlate;
    private $id = NULL;
    private $breadcrumb = '';
    private $user = NULL;
    private $isCountry = 0;
    private $record_data;
    private $reportType;

    /*     * *
     * Controller logic
     */

    protected function init() {

//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        $this->user = $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'sorter_scan_report.php' => 'Sorter Scan Report'
        );
        // common initialisation for ths page
        $this->setTitle("Sorter Report");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "Export") {
            $dateFrom = $this->form_vars['date_from'];
            $dateTo = $this->form_vars['date_to'];
            $mawbNo = $this->form_vars['mawb_no'];
            $reportType = $this->form_vars['report_type'];
            $csv = "";
            $cr = "\r\n";
            if ($reportType == "holdshipment") {
                $parcelfilter = new ParcelFilter();
                $scannerNumberList = $parcelfilter->getSorterHoldShipments(date("Y-m-d",strtotime($dateFrom)), date("Y-m-d",strtotime($dateTo)), $mawbNo);
                if (count($scannerNumberList) > 0) {
                    $csvHeader = "Date Scanned, Tracking Number, Reason, Dimension";
                    foreach ($scannerNumberList as $scanlist) {
                        $csv .= date("Y-m-d H:i:s", strtotime($scanlist->getDateAdded())) . ",";
                        $csv .= $scanlist->getTrackingNumber() . ",";
                        $csv .= $scanlist->getMessage() . ",";
                        $csv .= $scanlist->getWidth() . " X " . $scanlist->getHeight() . " X " . $scanlist->getLength() . ",";
                        $csv .= $cr;
                    }
                }
            } else if ($reportType == "servicewise") {
                $trackingDataFilter = new TrackingDataFilter();
                $scannerNumberList = $trackingDataFilter->getTotalSorterScannedToday(date("Y-m-d",strtotime($dateFrom)), date("Y-m-d",strtotime($dateTo)), $mawbNo);
                if (count($scannerNumberList) > 0) {
                    $csvHeader = "Date Scanned, Service Name, Total Shipments";
                    foreach ($scannerNumberList as $scanlist) {
                        $csv .= date("Y-m-d H:i:s", strtotime($scanlist->getDateCreated())) . ",";
                        $csv .= $scanlist->getServiceName() . ",";
                        $csv .= $scanlist->getTotal() . ",";
                        $csv .= $cr;
                    }
                }
            } else {
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addFilter("      date_format(c.date_scanned, '%Y-%m-%d') >= '" . date("Y-m-d",strtotime($dateFrom )). "' and date_format(c.date_scanned, '%Y-%m-%d') <= '" . date("Y-m-d",strtotime($dateTo)) . "' ", "consignmentfilter");
                if ($mawbNo > 0) {
                    $consignmentFilter->addFilter("      mawb = '" . $mawbNo . "'", "consignmentfilter");
                }
                $consignmentFilter->addFilter("      pc.id in (select entity_id from tracking_data where track_point = 'Birmingham Sorting Centre - GBR')", "parcelfilter");
                $scannerNumberList = $consignmentFilter->getColumnList("c.awb, c.sorter_image, c.weight, pc.width, pc.length, pc.height, pc.chute_sorted, s.name as service_name, c.date_scanned, ua.user_account as user_account");
                if (count($scannerNumberList) > 0) {
                    $csvHeader = "Account, Date Scanned, Tracking Number, Service Name, Weight, Dimension, Chute Sorted";
                    foreach ($scannerNumberList as $scanlist) {
                        $csv .= $scanlist->getUserAccount() . ",";
                        $csv .= date("Y-m-d H:i:s", $scanlist->getDateScanned()) . ",";
                        $csv .= $scanlist->getAwb() . ",";
                        $csv .= $scanlist->getServiceName() . ",";
                        $csv .= $scanlist->getWeight() . ",";
                        $csv .= $scanlist->getWidth() . " X " . $scanlist->getHeight() . " X " . $scanlist->getLength() . ",";
                        $csv .= $scanlist->getChuteSorted() . ",";
                        //$csv .= "http://164.39.218.212/Optimus/image/".$scanlist->getSorterImage() . ",";
                        $csv .= $cr;
                    }
                }
            }
            if ($csv != "") {
                $folder_path = "../_assets/optimus_sorter/Export";

                if (!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }

                $uniqueFileName = uniqid();

                $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                $file_path = fopen($file_path, 'w');

                fwrite($file_path, $csvHeader . $cr . $csv);

                // close file
                fclose($file_path);

                $data = $csvHeader . $cr . $csv;

                header("Content-Type: application/csv");

                header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
                header("Content-Type: application/vnd.ms-excel");
                echo $data;
                exit;
            }
        }


        if (isset($_GET['action']) && $_GET['action'] == "ajaxList") {
            $dateFrom = $this->form_vars['date_from'];
            $dateTo = $this->form_vars['date_to'];
            $mawbNo = $this->form_vars['mawb_no'];
            $reportType = $this->form_vars['report_type'];
            $this->reportType = $reportType;

            if ($dateFrom == '' && $dateTo == '') {
                $dateFrom = date("Y-m-d");
                $dateTo = date("Y-m-d");
            }

            if ($reportType == "holdshipment") {
                $parcelfilter = new ParcelFilter();
                $iTotalRecords = $parcelfilter->getSorterHoldShipmentsCount(date("Y-m-d",strtotime($dateFrom)), date("Y-m-d",strtotime($dateTo)), $mawbNo);
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $parcelfilter->setRowsPerPage($iDisplayLength);
                // the offset of the list, based on current page
                $parcelfilter->setOffset($iDisplayStart);
                $scannerNumberList = $parcelfilter->getSorterHoldShipments(date("Y-m-d",strtotime($dateFrom)), date("Y-m-d",strtotime($dateTo)), $mawbNo);
            } else if ($reportType == "servicewise") {
                $trackingDataFilter = new TrackingDataFilter();
                $scannerNumberList = $trackingDataFilter->getTotalSorterScannedToday(date("Y-m-d",strtotime($dateFrom)), date("Y-m-d",strtotime($dateTo)), $mawbNo);
                $iTotalRecords = count($scannerNumberList);
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            } else {
                $consignmentFilter = new ConsignmentFilter();
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $consignmentFilter->setRowsPerPage($iDisplayLength);
                // the offset of the list, based on current page
                $consignmentFilter->setOffset($iDisplayStart);
                $consignmentFilter->addFilter("      date_format(c.date_scanned, '%Y-%m-%d') >= '" . date("Y-m-d",strtotime($dateFrom)) . "' and date_format(c.date_scanned, '%Y-%m-%d') <= '" . date("Y-m-d",strtotime($dateTo)) . "' AND awb in (select tracking_number from tracking_data t where t.tracking_number = c.awb and t.track_point = 'Birmingham Sorting Centre - GBR' )", "consignmentfilter");
                if ($mawbNo > 0) {
                    $consignmentFilter->addFilter("      mawb = '" . $mawbNo . "'", "consignmentfilter");
                }
                $consignmentFilter->addFilter("      pc.id in (select entity_id from tracking_data where track_point = 'Birmingham Sorting Centre - GBR')", "parcelfilter");
                $scannerNumberList = $consignmentFilter->getShipmentPagingListOpt(false, true);
                $iTotalRecords = $consignmentFilter->getShipmentPagingListOpt(false, true, false, true);
                ///$iTotalRecords = $consignmentFilter->getShipmentPagingCountNew();
                
            }

            $this->record_data = $scannerNumberList;

//            $iTotalRecords = count($scannerNumberList);
//            $iDisplayLength = intval($_REQUEST['length']);
//            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
//            $iDisplayStart = intval($_REQUEST['start']);
//            $sEcho = intval($_REQUEST['draw']);
//            $end = $iDisplayStart + $iDisplayLength;
//            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            //$this->licencePlate->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            //$this->licencePlate->setOffset($iDisplayStart);

            $rangeDataArrs = [];
            $rangeDataArr = [];
            foreach ($scannerNumberList as $scanlist) {
               
                if ($reportType == "holdshipment") {
                    $rangeArr['datescanned'] = date("Y-m-d H:i:s", strtotime($scanlist->getDateAdded()));
                    $rangeArr['trackingnumber'] = $scanlist->getTrackingNumber();
                    $rangeArr['reason'] = $scanlist->getMessage();
                    $rangeArr['dimension'] = $scanlist->getWidth() . " X " . $scanlist->getHeight() . " X " . $scanlist->getLength();
                    $rangeArr['servicename'] = "";
                    $rangeArr['weight'] = "";
                    $rangeArr['chute'] = "";
                    $rangeArr['sorterimage'] = "";
                    $rangeArr['totalshipments'] = "";
                } else if ($reportType == "servicewise") {
                    $rangeArr['datescanned'] = date("Y-m-d H:i:s", strtotime($scanlist->getDateCreated()));
                    $rangeArr['servicename'] = $scanlist->getServiceName();
                    $rangeArr['totalshipments'] = $scanlist->getTotal();
                    $rangeArr['trackingnumber'] = "";
                    $rangeArr['reason'] = "";
                    $rangeArr['weight'] = "";
                    $rangeArr['dimension'] = "";
                    $rangeArr['chute'] = "";
                    $rangeArr['sorterimage'] = "";
                } else {
                    $rangeArr['datescanned'] = date("Y-m-d H:i:s", $scanlist->getDateScanned());
                    $rangeArr['trackingnumber'] = $scanlist->getAwb();
                    $rangeArr['servicename'] = $scanlist->getServiceName();
                    $rangeArr['weight'] = $scanlist->getWeight();
                    $rangeArr['dimension'] = $scanlist->getWidth() . " X " . $scanlist->getHeight() . " X " . $scanlist->getLength();
                    $rangeArr['chute'] = $scanlist->getChuteSorted();
                    $rangeArr['sorterimage'] = ' <a '
                        . 'class="btn btn-xs btn-default blue btn-outline margin-right-5 show_sorter_image" '
                        . 'data-sorterImage="' . $scanlist->getSorterImage() . '" '
                        . 'data-trackingNumber="' . $scanlist->getAwb() . '" '
                        . 'rel="tooltip" title="Sorter Image">
                            <span class="fa fa-picture-o"></span> 
                                            </a>';//"<a href='http://164.39.218.212/Optimus/image/".$scanlist->getSorterImage() . "' target='_blank'>".$scanlist->getSorterImage()."</a>";
                    $rangeArr['reason'] = "";
                    $rangeArr['totalshipments'] = "";
                }
                $rangeDataArr[] = $rangeArr;
            }

            $rangeDataArrs['data'] = $rangeDataArr;
            $rangeDataArrs['draw'] = $sEcho;
            $rangeDataArrs['recordsTotal'] = $iTotalRecords;
            $rangeDataArrs['recordsFiltered'] = $iTotalRecords;
            $rangeDataArrs['reportType'] = $reportType;
            echo json_encode($rangeDataArrs);
            die;
        }
        
        /*
         * Get Sorter Image
         */
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_sorter_image') {
            $sorterImage = $this->form_vars['sorterImage'];
            $imagePath = SETTING_DIR_ASSETS.'optimus_sorter/Image/'.$sorterImage;
            $baseimagePath = SETTING_URL_ASSETS.'optimus_sorter/Image/'.$sorterImage;
            if(!file_exists($imagePath)){
                $optimusSorter = new OptimusSorter();
                $downloadImage = $optimusSorter->getSorterImage($sorterImage);
                echo $baseimagePath ;
            }
            else
            {
                echo $baseimagePath;
            }
            die;
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        


        <?php
    }

    public function addPagelavelJs() {
        ?>
       <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>  
        <script src="../assets/pages/scripts/flipclock.min.js"></script>
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>


        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            $('#countryrange').on('switchChange.bootstrapSwitch', function (event, state) {
                if (state) {
                    $("#show_country_div").show();
                } else {
                    $('#show_country_div').hide();
                }
            });
        <?php if ($this->isCountry == '0') { ?>
                $("#show_country_div").hide();
        <?php } ?>
            var gridScan = "";
            var DataTableScan = function () {
                var handleDataTable = function () {
                    gridScan = new Datatable();
                    gridScan.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (gridScan,response) {
                            // execute some code after table records loaded
                            var type = response.reportType;
                            setTimeout(function(){
                                $('.col_show').hide();
                                if(type == "scanshipment") {
                                    $('.scanshipment').show();
                                } else if(type == "holdshipment") {
                                    $('.holdshipment').show();
                                } else if(type == "servicewise") {
                                    $('.servicewise').show();
                                }
                            }, 200);

                        },
                        onError: function (gridScan) {
                            // execute some code on network or other general error  
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options 
                            "lengthMenu": [
                                [10, 20, 50, 100],
                                [10, 20, 50, 100] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "sorter_daily_scan_report.php?action=ajaxList", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "datescanned", "class": "col_show scanshipment holdshipment servicewise"},
                                {"data": "trackingnumber", "class": "col_show scanshipment holdshipment"},
                                {"data": "servicename", "class": "col_show scanshipment holdshipment servicewise"},
                                {"data": "weight", "class": "col_show scanshipment"},
                                {"data": "dimension", "class": "col_show scanshipment holdshipment"},
                                {"data": "chute", "class": "col_show scanshipment"},
                                {"data": "sorterimage", "class": "col_show scanshipment"},
                                {"data": "reason", "class": "col_show holdshipment"},
                                {"data": "totalshipments", "class": "col_show servicewise"},
                            ],
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
            
            $(document).on('click', '.show_sorter_image', function () {
                var sorterImage = $(this).attr('data-sorterImage');
                var trackingNumber = $(this).attr('data-trackingNumber');
                
                $.ajax({
                    url: "sorter_daily_scan_report.php?tracking_number="+trackingNumber,
                    data: {func: "get_sorter_image", sorterImage: sorterImage},
                    type: 'post',
                    async: false,
                    success: function (response) {
                        if(response != ''){
                             $('#show-sorter-image').modal('show');
                             $('#tracking_image_container').html("<div><img src='"+response+"' alt='"+trackingNumber+"' height='500' width = '800'></div>");
                        }
                    }
                });
            });
            
     
            $('#btn_Save').click(function () {
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    gridScan.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    gridScan.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    gridScan.setAjaxParam($(this).attr("name"), $(this).val());
                });
                gridScan.submitFilter();
             
            });



            $(document).ready(function () {
                DataTableScan.init();
            });
          
            $("#btn_Export").click(function () {
                $("#form_action").val("Export");
                $("#rangeForm").submit();
            });


        </script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div class="main_formpage">
        <?php
        //if(Permissions::checkFilePermission('range_add'))
        {
            ?>
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-plus"></i>
                            
                                Sorter Scan Report
                        </div>
                        <div class="actions">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <form name="rangeForm" id="rangeForm" action="" method="POST">           
                            <div class="row display-none" id="res_message">
                                <div class="col-md-12">
                                    <div class="alert alert-success"></div>
                                </div>
                            </div>
                            <div class="row"> 

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Date</label>
                                         <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                            <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                            <input type="text" class="form-control form-filter" name="date_from" id="date_from" value="" rel="tooltip" data-original-title="From Date">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control form-filter" name="date_to" id="date_to" value="" rel="tooltip" data-original-title="To Date"> 
                                        </div>
                                        
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Mawb No</label>
                                        <div class="input-group input-group-sm" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="mawb_no" id="mawb_no" value="" size="50" class="form-control form-filter" title="Mawb No" maxlength="35" placeholder="Mawb No" rel="tooltip" data-original-title="Mawb No" type="text" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Report Type</label>
                                        <div class="input-group input-group-sm" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <select id="report_type" class="table-group-action-input form-control form-filter input-inline input-small input-sm" name="report_type">
                                                <option value="scanshipment">Scan Shipment</option>
                                                <option value="holdshipment">Consignment Hold</option>
                                                <option value="servicewise">Service Wise</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="clear:both"></div> 
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action"  />
                            <br />
                            <div class="row " style="text-align:centre;" align="center">
                                <div class="col-md-12">
                                    <input id="btn_Save" type="button"  class="btn btn-primary" value="Search"/>
                                    <button type="button" name="btn_Export" id="btn_Export" class="btn btn-default btn-blcok">Export</button>
                                    <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                                </div>
                            </div>   
                        </form>
                    </div>
                </div>
        <?php } ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>                        
                            Sorter Scan List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading datatablehide" id="scanshipment">
                                    <th>Date Scanned</th>
                                    <th>Tracking Numbers</th>
                                    <th>Service Name</th>
                                    <th>Weight</th>
                                    <th>Dimension</th>
                                    <th>Chute</th>
                                    <th>Image</th>
                                    <th>Reason</th>
                                    <th>Total Shipments</th>
                                </tr>
                               
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                </div>
            </div>
        </div>
         <div class="modal fade" tabindex="-1" role="dialog" id="show-sorter-image" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><span></span> Sorter Scan Image</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 display-none" id="display_modal_message"></div>
                                        <div id="tracking_image_container"></div>
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
