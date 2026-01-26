<?php
// get settings
require_once("../includes/settings/config.inc.php");
//require_once("../includes/mapping/glorderpdf8.class.php");
include_classes([
    'pdfmerger'
], 'labels');
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'PHPExcel'
], '3rdparty/phpexcel');
include_classes([
    'country.class',
    'flightinfo.class',
    'flightinfofilter.class',
    'mawb.class',
    'mawbfilter.class',
    'bagging.class',
    'baggingfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'services.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'currency.class'
]);


class Page extends BasePage
{

    /*     * *
     * Controller logic
     */

    protected function init()
    {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Manifest Export View"
        );
        $user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        /// Mariya Task start
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_flight_mawb_stats") {
            $flightId = trim($this->form_vars['flight_id']);
            if ($flightId <= 0) {
                $returnData['status'] = "error";
                $returnData['message'] = "Flight Number Empty";
            }
            if (empty($returnData)) {
                // Check if parcel exsist/ Get parcel service
                $dataArray = [];
                $flightInfo = new FlighInfoFilter();
                $flightInfo->addFieldFilter('       id', $flightId);
                $flightInfo = $flightInfo->getColumnList('*');
                $flightInfo = $flightInfo[0];
                $dataArray['flight_info']['flight_id'] = $flightId;
                $dataArray['flight_info']['flight_number'] = $flightInfo->getFlightNumber();
                $dataArray['flight_info']['arrival_date'] = !empty($flightInfo->getEta()) ? date('d-m-Y H:i:s', strtotime($flightInfo->getEta())) : '';
                $dataArray['flight_info']['departure_date'] = !empty($flightInfo->getEtd()) ? date('d-m-Y H:i:s', strtotime($flightInfo->getEtd())) : '';
                $dataArray['flight_info']['from_country'] = $flightInfo->getCountryId();
                $dataArray['flight_info']['to_country'] = $flightInfo->getDestinationCountryId();
                $dataArray['mawb_info'] = [];
                $flightMapping = new FlightMapping();
                $flightMawb = $flightMapping->getFlightMawbList($flightId);
                $flightMawbs = [];
                if (!empty($flightMawb)) {
                    foreach ($flightMawb as $mawb) {
                        $flightMawbs[] = $mawb->getMawbId();
                    }
                }
                if (!empty($flightMawbs)) {
                    $mawbs = new MawbFilter();
                    $mawbs->addFilter(' id IN (' . implode(',', $flightMawbs) . ')');
                    $mawbs->AddOrderBy('id', FALSE);
                    $mawbs = $mawbs->getList();
                    $i = 0;
                    foreach ($mawbs as $mawb) {
                        $dataArray['mawb_info'][$i] = [
                            'mawb_number' => $mawb->getMawbNumber(),
                            'mawb_id' => $mawb->getId(),
                            'mawb_status' => $mawb->getMawbStatus(),
                            'mawb_manifest_lv' => $mawb->getMawbLvManifest(),
                            'mawb_manifest_hv' => $mawb->getMawbHvManifest()
                        ];
                        $mawbBags = new MawbParcelMappingFilter();
                        $mawbBags->addFilter(' mawb_id = ' . $mawb->getId());
                        $mawbBags->addFilter(' bag_id > 0 ');
                        $mawbBags->addGroupBy('bag_id');
                        $mawbBags = $mawbBags->getList();
//                        $totalBag = count($mawb_info['bags_info']);
                        $openBags = 0;
                        if (!empty($mawbBags)) {
                            foreach ($mawbBags as $mawbBag) {
                                $bagging = new Bagging($mawbBag->getBagId());
                                if ($bagging->getIsClosed() == 0) {
                                    $openBags++;
                                }
                                $baggingObj = BaggingFilter::getParcelTotalFromBagId($mawbBag->getBagId());
                                $baggingParcelCount = 0;
                                if (count($baggingObj) > 0) {
                                    $baggingParcelCount = $baggingObj[0]->getId();
                                }
                                $dataArray['mawb_info'][$i]['bags_info'][] = [
                                    'bag_id' => $bagging->getId(),
                                    'bag_number' => $bagging->getBagnumber(),
                                    'total_parcels' => $baggingParcelCount,
                                    'service_id' => $bagging->getService(),
                                    'is_closed' => $bagging->getIsClosed(),
                                    'weight' => $bagging->getWeight(),
                                    'bag_manifest' => $bagging->getBagManifest(),
                                    'bag_label' => $bagging->getBagLabel(),
                                    'is_bag_open' => $isAnyBagOpen,
                                    'bag_value' => $bagging->getBagValue()
                                ];
                            }
                            $dataArray['mawb_info'][$i]['open_bags'] = $openBags;
                        }
                        $i++;
                    }
                }
//                echo "<pre>";
//                print_r($dataArray);
//                die;
                $toCountry = new Country($dataArray['flight_info']['to_country']);
                $toCountry = $toCountry->getName();
                $fromCountry = new Country($dataArray['flight_info']['from_country']);
                $fromCountry = $fromCountry->getName();
                $html = '';
                $htmlCloseFligthBtn = "";
                if ($flightInfo->getIsClosed() == 0) {
                    $htmlCloseFligthBtn = '';
                } else {
                    $SAManifestFileLv = $flightInfo->getFilesLv();
                    $SAManifestFileHv = $flightInfo->getFilesHv();
                    $lowValueExcel = $flightInfo->getLowValueManifest();
                    $highValueExcel = $flightInfo->getHighValueManifest();
                    $invoicesLink = $flightInfo->getInvoice();
                    $htmlCloseFligthBtn = "";
                    if (!empty($SAManifestFileLv))
                        $htmlCloseFligthBtn .= '<a href="' . SETTING_MAIN_ASSETS . $SAManifestFileLv . '" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download SA LV Manifest"><i class="fa fa-download"></i> SA LV Manifest</a>';
                    if (!empty($SAManifestFileHv))
                        $htmlCloseFligthBtn .= '<a href="' . SETTING_MAIN_ASSETS . $SAManifestFileHv . '" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download SA HV Manifest"><i class="fa fa-download"></i> SA HV Manifest</a>';

                    if (!empty($lowValueExcel))
                        $htmlCloseFligthBtn .= '<a href="' . SETTING_MAIN_ASSETS . $lowValueExcel . '" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download LV Manifest"><i class="fa fa-download"></i> LV Manifest</a>';

                    if (!empty($highValueExcel))
                        $htmlCloseFligthBtn .= '<a href="' . SETTING_MAIN_ASSETS . $highValueExcel . '" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download HV Manifest"><i class="fa fa-download"></i> HV Manifest</a>';

                    if (!empty($invoicesLink))
                        $htmlCloseFligthBtn .= '<a href="' . SETTING_MAIN_ASSETS . $invoicesLink . '" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" target="_blank" data-toggle="tooltip" title="Download Invoice"><i class="fa fa-download"></i> Invoice</a>';


                }
                $html .= '<div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                            <b>Departure From</b>
                                            <p>' . $fromCountry . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Arrival To</b>
                                            <p>' . $toCountry . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Arrival Date</b>
                                            <p>' . $dataArray['flight_info']['arrival_date'] . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Departure Date</b>
                                            <p>' . $dataArray['flight_info']['departure_date'] . '</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                            ' . $htmlCloseFligthBtn . '
                                    </div>
                                </div>
                            </div>
                            ';
                $html .= '<div class="clearfix"></div>';
                if (!empty($dataArray['mawb_info'])) {
                    foreach ($dataArray['mawb_info'] as $mawb_info) {
                        $i = 1;
                        $openBags = $mawb_info['open_bags'];
                        $htmlBtn = "";
                        if ($mawb_info['mawb_status'] == "o") {
                            $htmlBtn .= '<span class="label label-sm label-info pull-right">Open</span>';
                        } else {
                            if ($mawb_info['mawb_status'] == "d") {
                                $htmlBtn = '<span class="label label-sm pull-right label-warning">Dispatched</span>';
                            } else if ($mawb_info['mawb_status'] == "pd") {
                                $htmlBtn = '<span class="label label-sm pull-right label-warning">Partial Dispatched</span>';
                            }
                        }
                        if ($openBags > 0) {
                            $htmlBtn .= '';
                        } else {
                            if (!empty($mawb_info['mawb_manifest_lv'])) {
                                $htmlBtn .= '<a href="' . SETTING_MAIN_ASSETS . $mawb_info['mawb_manifest_lv'] . '" title="Download LV Manifest" data-toggle="tooltip" class="margin-right-10 pull-right"><i class="fa fa-file-excel-o"></i></a>';

                            }
                            if (!empty($mawb_info['mawb_manifest_hv'])) {
                                $htmlBtn .= '<a href="' . SETTING_MAIN_ASSETS . $mawb_info['mawb_manifest_hv'] . '" title="Download HV Manifest" data-toggle="tooltip" class="margin-right-10 pull-right"><i class="fa fa-file-excel-o"></i></a>';

                            }
                        }
                        $html .= '<div class="panel panel-default">
                                            <!-- Default panel contents -->
                                            <div class="panel-heading">
                                                <h3 class="panel-title">MAWB#:&nbsp;' . $mawb_info['mawb_number'] . $htmlBtn . '</h3>
                                            </div>
                                            <!-- Table -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center"> # </th>
                                                        <th> Bag No. </th>
                                                        <th width="20%" class="text-center"> Parcels </th>
                                                        <th width="20%" class="text-center"> Status </th>
                                                        <th width="20%" class="text-center"> Action </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                        foreach ($mawb_info['bags_info'] as $bag_info) {
                            $mawbNumberCls = "";
                            if ($bag_info['is_closed'] == 0) {
                                $labelClass = "label-success";
                                $label = "Open";
                                $button = "";
                            } else {
                                $closedBag++;
                                $mawbNumberCls = $mawb_info['mawb_number'];
                                $labelClass = "label-danger";
                                $label = "Closed";
                                $button = '';
                                $button .= '<a href="' . $bag_info['bag_label'] . '"  class="margin-right-10" data-toggle="tooltip" target="_blank" title="Download Label"><i class="fa fa-file-pdf-o"></i></a>';
                                $button .= '<a href="' . $bag_info['bag_manifest'] . '" class="margin-right-10" data-toggle="tooltip" title="Download Manifest"><i class="fa fa-file-excel-o "></i></a>';
                            }
                            $service = new Services($bag_info['service_id']);
                            $bagValueCls = 'label-success';
                            if ($bag_info['bag_value'] == "hv") {
                                $bagValueCls = 'label-info';
                            }
                            $html .= '
                                        <tr>
                                            <td class="text-center"> ' . $i . ' </td>
                                            <td>' . $bag_info['bag_number'] . ' <span class="label label-sm  ' . $bagValueCls . '">' . strtoupper($bag_info['bag_value']) . '</span> ' . '<br /><small></small><i>' . $service->getName() . '</i><small></td>
                                            <td style="cursor: pointer;" class="text-center" onclick="get_parcel_details(\'' . $bag_info['bag_id'] . '\')">' . $bag_info['weight'] . ' Kg <br /><small><i><strong>Parcels:</strong> ' . $bag_info['total_parcels'] . '</i></small></td>
                                            <td class="text-center"> <span class="label label-sm ' . $mawbNumberCls . ' ' . $labelClass . '"> ' . $label . ' </span> </td>
                                            <td class="text-center">' . $button . '</td>
                                        </tr>';
                            $i++;
                        }
                        $html .= '</tbody></table></div>';
                    }
                }
//                echo "<pre>";
                $returnData['status'] = "success";
                $returnData['html'] = $html;
                $returnData['flight_number'] = $flightInfo->getFlightNumber();
                echo json_encode($returnData);
                die;
            } else {
                echo json_encode($returnData);
                die;
            }
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "parcel_detail_ajax") {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addJoin("parcel p", "p.id", "pbm.parcel_id");
            $parcelBaggingMappingFilter->addJoin("bagging b", "b.id", "pbm.bag_id");

            $bagId = $this->form_vars['bag_filter'];
            if(!empty($bagId)) {
                $parcelBaggingMappingFilter->addFieldFilter('    pbm.bag_id', $bagId);
            }
            $parcelBaggingMappingFilter->addGroupBy("pbm.parcel_id");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $trackingNumber = $this->form_vars['tracking_number'];
                if (!empty($trackingNumber))
                    $parcelBaggingMappingFilter->addFieldFilter('    p.tracking_number', $trackingNumber);

                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    p.weight', $weight);

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

                if($dataTableColumnName == "tracking_number") {
                    $dataTableColumnName = "p.tracking_number";
                }
                $parcelBaggingMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $parcelBaggingMappingFilter->getParcelPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelBaggingMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $parcelBaggingMappingFilter->setOffset($iDisplayStart);
            $parcelBaggingMappingFilterobjs = $parcelBaggingMappingFilter->getPagingList("p.`id`,p.`tracking_number`,p.`length`,p.`width`,p.`height`,p.`weight`,p.`parcel_status_code`,b.id AS bag_id");
            $setDataArr = array();
            foreach ($parcelBaggingMappingFilterobjs as $key => $parcelBaggingMappingFilterobj) {
                $shipment_status = Consignment::getShipnmentStatus($parcelBaggingMappingFilterobj->getParcelStatusCode());
                $currentArr = array();
                $currentArr['tracking_number'] = $parcelBaggingMappingFilterobj->getTrackingNumber();
                $currentArr['dims'] = $parcelBaggingMappingFilterobj->getLength() . " x " . $parcelBaggingMappingFilterobj->getWidth() . " x " . $parcelBaggingMappingFilterobj->getHeight();
                $currentArr['weight'] = $parcelBaggingMappingFilterobj->getWeight();
                $currentArr['status'] = $shipment_status;
                $currentArr['actions'] = '<a class="btn btn-xs btn-default red btn-outline center types_delete" title="Delete Parcel" href="javascript:;" onclick="delete_parcel(' . "'" . $parcelBaggingMappingFilterobj->getId() . "'" . ',' . "'" . $parcelBaggingMappingFilterobj->getBagId()  . "'" . ')"><span class="fa fa-trash"></span></a>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        /// Mariya task end
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <style>
            .inputfield_custom_style {
                width: 100% !important;
                height: 100px !important;
                font-size: 32px !important;
                background-color: #f5f5f5;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                parcelDataTableFun.init();
                var flight_id = <?php echo (int) $_GET['flight_id'];?>;
                // alert(flight_id);
                get_flight_mawb_stats(flight_id);
                $(".reload").click(function () {
                    var flight_id = <?php echo (int) $_GET['flight_id'];?>;
                    get_flight_mawb_stats(flight_id);
                });
            });


            function get_flight_mawb_stats(flight_id) {
                // var flight_id = $("#flight").val();
                // var flight_id = flight_id;
                // alert(flight_id);
                if (flight_id != '') {
                    $.ajax({
                        url: "mawb_flight_view.php", // point to server-side PHP script
                        data: {
                            flight_id: flight_id,
                            action: 'get_flight_mawb_stats'
                        },
                        dataType: "json",
                        type: 'post',
                        success: function (php_script_response) {
                            if (php_script_response.status == "success") {
                                $("#flight_info_stats").html(php_script_response.html);
                                $("#flight_info_number").html(php_script_response.flight_number);
                            } else {

                            }
                            $('[data-toggle="tooltip"]').tooltip();
                            //                            check_mawb_bag();
                        }
                    });
                } else {
                    $("#flight_info_stats").html('No flight is selected.');
                }
            }

            var parcelGrid = null;
            var parcelDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bag_id = $('#bag_filter').val();
                    var bagdatatableurl = "mawb_flight_view.php?action=parcel_detail_ajax";
                    parcelGrid = new Datatable();
                    parcelGrid.init({
                        src: $("#parcel-Detail-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [10, 20, 50, 100, 150],
                                [10, 20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": bagdatatableurl, // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "tracking_number"},
                                {"data": "dims", "bSortable": false},
                                {"data": "weight"},
                                {"data": "status", "bSortable": false}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        BaghandleDataTable();
                    }
                };
            }();

            function get_parcel_details(bag_id) {
                $('#bag_filter').val(bag_id);
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    parcelGrid.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                parcelGrid.submitFilter();
                $('#parcel_detail_model').modal('show');
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
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Flight Info [ <span
                                        id="flight_info_number"></span> ]
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="" class="fullscreen" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" id="flight_info_stats"></div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div id="create_mawb_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create Mawb</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">MAWB Number.</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="mawb_number" id="mawb_number" class="form-control"
                                               placeholder="mawb number"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="create_mawb">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>-->
                </div>

            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_detail_model">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel Detail List</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="portlet light">
                                    <div class="portlet-body">
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover table-condensed"
                                                   id="parcel-Detail-table">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th>Actions</th>
                                                    <th>Tracking Number</th>
                                                    <th>Dim(L x W x H)</th>
                                                    <th>Weight(KG)</th>
                                                    <th>Status</th>
                                                </tr>
                                                <tr role="row" class="filter">
                                                    <td>
                                                        <div class="margin-bottom-5">
                                                            <button class="btn btn-xs blue filter-submit btn-outline"><i
                                                                        class="fa fa-search"></i></button>
                                                            <!--                                                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>-->
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="bag_filter" id="bag_filter"
                                                               class="form-filter">
                                                        <input type="text" name="tracking_number" id="tracking_number"
                                                               class="form-control form-filter">
                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <input type="text" name="weight" id="weight"
                                                               class="form-control form-filter">
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
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

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
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }
}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();

function createBagWithServiceId($serviceId, $sourceCountryId, $destinationCountryId, $bagLowValue)
{
    $returnData = [];
    $user = SessionManager::getUser();
    $bagnumber = "FSBG" . time();
    $bagging = new Bagging();
    $bagging->setBagnumber($bagnumber);
    $bagging->setDateCreated(time());
    $bagging->setUserId($user->getId());
    $bagging->setBagSourceCountryId($sourceCountryId);
    $bagging->setBagSourceWarehouseId("");
    $bagging->setBagDestinationCountryId($destinationCountryId);
    $bagging->setBagDestinationWarehouseId("");
    $bagging->setBagType("NORMAL");
    $bagging->setService($serviceId);
    $bagging->setBagValue($bagLowValue);
    $bagging->setBagStatus(1);
    $bagging->setIsClosed(0);
    $bagging->setDateUpdated(time());
    $bagging->save();
    if ($bagging->getId() > 0) {
        $returnData['bag_id'] = $bagging->getId();
        $returnData['bag_number'] = $bagnumber;
    }
    return json_encode($returnData);
    die;
}

function addDataIntoMappingTable($bagId, $mawbId, $parcelId, $flightId)
{
    $user = SessionManager::getUser();
    $returnData['status'] = "success";
    $returnData['message'] = "Bag is opened and parcel is added into it";
//    check if data is already into table
    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
    $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
    $mawbParcelMappingFilter->addFieldFilter("parcel_id", $parcelId);
    $mawbParcelMappingFilter->addFieldFilter("bag_id", $bagId);
    $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("id,mawb_id");
    if (count($mawbParcelMappingObj) > 0) {
// Already added
    } else {
        // Add data into mawb_parcel_mapping
        $mawbParcelMapping = new MawbParcelMapping();
        $mawbParcelMapping->setMawbId($mawbId);
        $mawbParcelMapping->setParcelId($parcelId);
        $mawbParcelMapping->setBagId($bagId);
        $mawbParcelMapping->setDateAdded(time());
        $mawbParcelMapping->setAddedBy($user->getId());
        $mawbParcelMapping->save();
    }
    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
    $parcelBaggingMappingFilter->addFieldFilter("    parcel_id", $parcelId);
    $parcelBaggingMappingFilter->addFieldFilter("bag_id", $bagId);
    $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id,bag_id");
    if (count($parcelBaggingMappingObj) > 0) {
        $returnData['status'] = "error";
        $returnData['message'] = "Parcel is already added into bag";
    } else {
        // Add data into parcel_bagging_mapping
        $parcelBaggingMapping = new ParcelBaggingMapping();
        $parcelBaggingMapping->setParcelId($parcelId);
        $parcelBaggingMapping->setBagId($bagId);
        $parcelBaggingMapping->setAddedBy($user->getId());
        $parcelBaggingMapping->setAddedDate(time());
        $parcelBaggingMapping->save();
    }
    // Add data into flightinfo_mapping
    // check if data already added into
    $flightinfoMappingFilter = new FlightMappingFilter();
    $flightinfoMappingFilter->addFieldFilter("    flight_info_id", $flightId);
    $flightinfoMappingFilter->addFieldFilter("    mawb_id", $mawbId);
    $flightinfoMappingObj = $flightinfoMappingFilter->getColumnList("id,mawb_id,is_delete");
    if (count($flightinfoMappingObj) > 0) {
        $flightinfoMappingObj[0]->getIsDelete();
        if ($flightinfoMappingObj[0]->getIsDelete() == 1) {
            $flightinfoMapping = new FlightMapping($flightinfoMappingObj[0]->getId());
            $flightinfoMapping->setIsDelete(0);
            $flightinfoMapping->save();
        }
    } else {
        $flightinfoMapping = new FlightMapping();
        $flightinfoMapping->setFlightInfoId($flightId);
        $flightinfoMapping->setMawbId($mawbId);
        $flightinfoMapping->setIsDelete(0);
        $flightinfoMapping->save();
    }

    $baggingFilter = new BaggingFilter();
    $baggingFilterObj = $baggingFilter->getBagParcelWeightByBagId($bagId);
    $bagTotalParcelWeight = 0;
    if (count($baggingFilterObj) > 0) {
        $bagTotalParcelWeight = $baggingFilterObj[0]->getActualWeight();
    }
    $bagParcel = BaggingFilter::getParcelTotalFromBagId($bagId);
    $bagging = new Bagging($bagId);
    $bagging->setWeight($bagTotalParcelWeight);
    $bagging->setPieces($bagParcel);
    $bagging->save();
    return $returnData;
}
function getParcelListFromBagNumber($bagId, $parcelArr = false)
{
    $list = array();
    $parcelListArr = array();
    if ($bagId > 0) {
        $parcelBaggingmapping = new ParcelBaggingMappingFilter();
        $parcelBaggingmapping->addFieldFilter("    bag_id", $bagId);
        $parcelBaggingMappingObj = $parcelBaggingmapping->getColumnList("parcel_id");
        if (count($parcelBaggingMappingObj) > 0) {
            foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                $list[] = new Parcel($parcelBaggingMappingArr->getParcelId());
                $parcelListArr[] = $parcelBaggingMappingArr->getParcelId();
            }
        }
    }
    if ($parcelArr) {
        return $parcelListArr;
    } else {
        return $list;
    }
}

function generateBagLabel($bagId)
{
    $user = SessionManager::getUser();
    $dataArr = [];
    if ($bagId > 0) {
        $bagging = new Bagging($bagId);
        $dataArr['bag_number'] = $bagging->getBagnumber();
        $dataArr['bag_weight'] = $bagging->getWeight();
        $serviceObj = new Services($bagging->getService());
        if (count($serviceObj) > 0) {
            $dataArr['bag_service'] = $serviceObj->getName();
        }
        $parcelbaggingCount =
        $mawbParcelMappingFilter = new MawbParcelMappingFilter();
        $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
        $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("mawb_id");
        if (count($mawbParcelMappingObj) > 0) {
            $mawbId = $mawbParcelMappingObj[0]->getMawbId();
            $mawbObj = new Mawb($mawbId);
            $dataArr['bag_mawb'] = $mawbObj->getMawbNumber();
            $flighMappingFilter = new FlightMappingFilter();
            $flighMappingFilter->addFieldFilter("    mawb_id", $mawbId);
            $flighMappingFilterObj = $flighMappingFilter->getColumnList("flight_info_id");
            if (count($flighMappingFilterObj) > 0) {
                $flightInfoId = $flighMappingFilterObj[0]->getFlightInfoId();
                $fligthInfo = new FlightInfo($flightInfoId);
                $countryObj = new Country($fligthInfo->getDestinationCountryId());
                if (count($countryObj) > 0) {
                    $dataArr['bag_country'] = $countryObj->getName();
                }
                $dataArr['bag_address_line_1'] = $fligthInfo->getAddressLine1();
                $dataArr['bag_address_line_2'] = $fligthInfo->getAddressLine2();
                $dataArr['bag_city'] = $fligthInfo->getCity();
                $dataArr['bag_postcode'] = $fligthInfo->getPostcode();
                $dataArr['bag_company'] = $fligthInfo->getCompany();
            }
        }
    }
    if (count($dataArr) > 0) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(false, 0);
        $page_size = array(150, 100);
        $pdf->AddPage("P", $page_size);
        $pdf->setFont("helvetica", "L", 7);
        $userImage = User::getUserCompanyImages(true, $user->getId());
        $userImage = explode('images/', $userImage);
        $userImage = "../images/" . $userImage[1];
        $pdf->Image($userImage, 5, 2, 40, 20);
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $pdf->SetFont('Times', 'B', 10);
        $addressline1 = $dataArr['bag_address_line_1'];
        $addressline2 = $dataArr['bag_address_line_2'];
//        $addressline3 = $dataArr['bag_address_line_3'];
        $addressline4 = "";
        $city = $dataArr['bag_city'];
        $postcode = $dataArr['bag_postcode'];
        $countryname = $dataArr['bag_country'];
        $company = $dataArr['bag_company'];
        $y = 25;
        $pdf->Text(12, $y, strtoupper("TO:"));
        if ($company != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($company));
        }
        if ($addressline1 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline1));
        }
        if ($addressline2 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline2));
        }
//        if ($addressline3 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline3));
//        }
//        if ($addressline4 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline4));
//        }
        if ($city != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($city));
        }
        if ($postcode != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($postcode));
        }
        if ($countryname != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($countryname));
        }
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Text(20, 68, "MAWB");
        $pdf->Text(20, 78, "Pieces");
        $pdf->Text(20, 88, "Tag No.");
        $pdf->Text(20, 98, "Weight");
//        $pdf->Text(20, 108, "Service");
        $pdf->line(50, 65, 50, 105);   //centerline
        $pdf->line(10, 65, 90, 65); // line 2
        $pdf->line(10, 75, 90, 75); // line 3
        $pdf->line(10, 85, 90, 85); // line 4
        $pdf->line(10, 95, 90, 95); // line 5
        $pdf->line(10, 105, 90, 105);
        $pdf->line(10, 115, 90, 115);
        $pdf->line(10, 25, 90, 25);
        $pdf->line(10, 25, 10, 115);
        $pdf->line(90, 25, 90, 115);
        $pdf->Text(55, 68, $dataArr['bag_mawb']);
        $parcelCount = getParcelListFromBagNumber($bagId, true);
        $pdf->Text(55, 78, count($parcelCount));
        $pdf->Text(55, 88, $dataArr['bag_number']);
        $pdf->Text(55, 98, $dataArr['bag_weight'] . " Kg.");
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Text(20, 108, $dataArr['bag_service']);
        $pdf->SetFont('Times', 'B', 12);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->write1DBarcode($dataArr['bag_number'], 'C128', 10, 120, 80, 18, 0.4, '', 'C');
        $folderPath = '../_assets/export_manifest/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $fileName = time() . "_" . $dataArr['bag_number'] . '.pdf';
        $outFile = $folderPath . $fileName;
        $filePathDb = SETTING_MAIN_ASSETS . "export_manifest/" . $fileName;
        $pdf->Output($outFile, 'F');
        $bagging->setBagLabel($filePathDb);
        $bagging->save();
        return $filePathDb;
    }
}

function generateInvoice($conArr)
{
    $pdf2 = new PDFMerger();
    $mergeFileName = "manifest-invoice-" . time() . ".pdf";
    $folderPath = '../_assets/export_manifest/';
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
    foreach ($conArr as $consignemtId) {
        $consignment = new Consignment($consignemtId); //	$consignmentData[0];
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetX(1.0);
        $glOrderPdf = new ProformaInvoice($pdf);
        $glOrderPdf->setInvoiceFolder("export_manifest");
        $glOrderPdf->setIsReturnUrl(false);
        $invoiceLink = $glOrderPdf->AddHTML($consignment, '', false, true);
        $pdf2->addPDF($invoiceLink, 'all');
        $labelLinl = "../_assets/pdf/" . $consignment->getLabelFile();
        if (file_exists($labelLinl)) {
            $pdf2->addPDF($labelLinl, 'all');
        }
    }
    $filePatch = $mergeFileName;
//        $newFileVarUrls	=	SETTING_MAIN_ASSETS."export_manifest/".$filePatch;
    $newFileVar = $folderPath . $filePatch;
    $pdf2->merge('file', $newFileVar);
    return "export_manifest/" . $filePatch;
}

function getExcelFile($flightList, $HLValue = "lv")
{

    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
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
            )
        )
    );
    $borderBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderThinBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderThinRight = array(
        'borders' => array(
            'right' => array(
                'style' => 'thin'
            )
        )
    );
    $borderThinRightBottom = array(
        'borders' => array(
            'right' => array(
                'style' => 'thin'
            ),
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderTop = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            )
        )
    );
    $topFiveBox = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $topText = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 22,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNine = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $columnTen = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $h12Mawb = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottom = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight2 = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => 'F2F2F2'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeft = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeftBold = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Times New Roman',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastHeaderYellow = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFF71C')
        ),
    );
    $alignCenterTopBottomRightBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightBoldExtraa = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnTenPlus = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNineH = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    // Add some data
    $objPHPExcel->getActiveSheet()->getColumnDimension("A1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("B1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D1")->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension("E1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("F1")->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension("G1")->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension("H1")->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension("I1")->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension("J1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("K1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("L1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("M1")->setWidth(16);
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'APPLICATION FOR RELEASE OF GOODS IN TERMS OF SECTION 38 (1) (a) OF THE')->mergeCells('A1:J1')
        ->setCellValue('A2', 'CUTOMS AND EXCISE ACT , ACT NUMBER 91 OF 1964')->mergeCells('A2:J2')
        ->setCellValue('A3', '')->mergeCells('A3:J3')
        ->setCellValue('A4', 'AANSOEK OM LOSSING VAN GOEDERE INGEVLOGE ARTIKEL 38 (1) (a) VAN DIE')->mergeCells('A4:J4')
        ->setCellValue('A5', 'DOEANE  EN AKSYNSWET, WET NOMMER 91 VAN 1964')->mergeCells('A5:J5')
        ->setCellValue('A6', '')->mergeCells('A6:M6')
        ->setCellValue('A7', 'Name and Address of Importer:')->mergeCells('A7:M7')
        ->setCellValue('A8', 'RT CLEARING AND FORWARDING')->mergeCells('A8:M8')
        ->setCellValue('A9', 'TRANSPORT DOCUMENT NUMBER AND DATE')->mergeCells('A9:G9')
        ->setCellValue('A10', 'VERVOERDOKUMENT NOMMER EN DATUM')->mergeCells('A10:G10')
        ->setCellValue('A12', $flightList['flight_number'])->mergeCells('A12:G12')
        ->setCellValue('H9', 'SHIP AND VOYAGE NO / FLIGHT NO AND DATE')->mergeCells('H9:M9')
        ->setCellValue('H10', 'SKIP EN VAARTNR /  VLUGNR EN DATUM')->mergeCells('H10:M10');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('K1', 'DA 306')->mergeCells('K1:M4')
        ->setCellValue('K5', '')->mergeCells('K5:M5');
    $objPHPExcel->getActiveSheet()->getStyle('A1:J5')->applyFromArray($topFiveBox);
    $objPHPExcel->getActiveSheet()->getStyle('K1:M5')->applyFromArray($topText);
    $objPHPExcel->getActiveSheet()->getStyle('A7:M7')->applyFromArray($columnNine);
    $objPHPExcel->getActiveSheet()->getStyle('A8:M8')->applyFromArray($columnNine);
    $objPHPExcel->getActiveSheet()->getStyle('A9:G10')->applyFromArray($columnTenPlus);
    $objPHPExcel->getActiveSheet()->getStyle('H9:M10')->applyFromArray($columnNineH);
    // Miscellaneous glyphs, UTF-8
    $mawblistHLV = 'mawb_list_' . $HLValue;
    if (count($flightList[$mawblistHLV]) > 0) {
        $countMawbDataLine = 12;
        foreach ($flightList[$mawblistHLV] as $flightData) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('H' . $countMawbDataLine, $flightData)->mergeCells('H' . $countMawbDataLine . ':J' . $countMawbDataLine);
            $countMawbDataLine++;
        }
    }
    $cssColumnCount = $countMawbDataLine - 1;
    $objPHPExcel->getActiveSheet()->getStyle('A12:G' . $cssColumnCount)->applyFromArray($columnTen);
    $objPHPExcel->getActiveSheet()->getStyle('H11:J' . $cssColumnCount)->applyFromArray($h12Mawb);
    $cssColumnCount = $countMawbDataLine + 1;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $countMawbDataLine . ':G' . $cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $countMawbDataLine . ':I' . $cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $countMawbDataLine . ':M' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $cssColumnCount . ':M' . $cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $cssColumnCount = $cssColumnCount + 1;
//        $cssColumnCountNext = $cssColumnCount+1;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $cssColumnCount . ':G' . $cssColumnCount)->applyFromArray($alignCenterTopBottomRight2);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $cssColumnCount . ':I' . $cssColumnCount)->applyFromArray($alignCenterTopBottomRightBold);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $cssColumnCount . ':M' . $cssColumnCount)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCount + 1;
    $cssColumnCountNext = $cssColumnCount + 3;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $cssColumnCount . ':G' . $cssColumnCountNext)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $cssColumnCount . ':M' . $cssColumnCountNext)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCountNext + 1;
    $cssColumnCountNext = $cssColumnCount + 2;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $cssColumnCount . ':G' . $cssColumnCountNext)->applyFromArray($alignCenterTopBottomRightBoldExtraa);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $cssColumnCount . ':M' . $cssColumnCountNext)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCountNext + 2;
    $objPHPExcel->getActiveSheet()->getStyle('N' . $cssColumnCount . ':O' . $cssColumnCount)->applyFromArray($alignCenterTopBottomLeftBold);
    $cssColumnCount = $cssColumnCount + 1;
    $objPHPExcel->getActiveSheet()->getStyle('N' . $cssColumnCount . ':O' . $cssColumnCount)->applyFromArray($alignCenterTopBottomLeftBold);
    $cssColumnCount = $cssColumnCount + 1;
    $objPHPExcel->getActiveSheet()->getStyle('A' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('C' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('D' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('F' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('G' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('I' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('K' . $cssColumnCount)->applyFromArray($lastHeaderYellow);
    $objPHPExcel->getActiveSheet()->getStyle('L' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('M' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('N' . $cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('O' . $cssColumnCount)->applyFromArray($lastHeader);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'TOTAL NUMBER OF PACKAGES')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'CUSTOMS VALUE')->mergeCells('H' . $countMawbDataLine . ':I' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $countMawbDataLine, 'IMPORT PERMIT NO AND AMOUNT')->mergeCells('J' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'TOTALE GETAL PAKKE')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'DOEANEWAARDE')->mergeCells('H' . $countMawbDataLine . ':I' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $countMawbDataLine, 'INVOERPERMITNR EN BEDRAG')->mergeCells('J' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $flightTotalParcelIndex = "flight_total_parcel_" . $HLValue;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, $flightList[$flightTotalParcelIndex])->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'N.C.V')->mergeCells('H' . $countMawbDataLine . ':I' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'MARKS, NUMBERS AND DESCRIPTION OF PACKAGES AND / OR')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, '')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'CONTAINER NUMBER(S)')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'DESCRIPTION OF GOODS')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'MERKE, NOMMERS EN BESKRYWING VAN PAKKE EN / OF')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'BESKRYWING VAN GOEDERE')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'HOUERNOMMER(S)')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, '')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, '')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $flightTotalParcelWeightIndex = "flight_total_parcel_weight_" . $HLValue;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, $flightList[$flightTotalParcelWeightIndex] . " KG")->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, 'DOCUMENTS / ASSORTED / DUITABLES')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, '')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':G' . $countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $countMawbDataLine, '')->mergeCells('H' . $countMawbDataLine . ':M' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $countMawbDataLine, 'SAR USE ONLY')->mergeCells('N' . $countMawbDataLine . ':O' . $countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $countMawbDataLine, '')->mergeCells('N' . $countMawbDataLine . ':O' . $countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'FLIGHT NUMBER')
        ->setCellValue('B' . $countMawbDataLine, 'MAWB')
        ->setCellValue('C' . $countMawbDataLine, 'HAWB')
        ->setCellValue('D' . $countMawbDataLine, 'Origin')
        ->setCellValue('E' . $countMawbDataLine, 'Shipper')
        ->setCellValue('F' . $countMawbDataLine, 'Dest')
        ->setCellValue('G' . $countMawbDataLine, 'Consignee')
        ->setCellValue('H' . $countMawbDataLine, 'Weight')
        ->setCellValue('I' . $countMawbDataLine, 'Description')
        ->setCellValue('J' . $countMawbDataLine, 'Pieces')
        ->setCellValue('K' . $countMawbDataLine, 'Foreign Value ($)')
        ->setCellValue('L' . $countMawbDataLine, ' Customs Value (R)')
        ->setCellValue('M' . $countMawbDataLine, '20% Duty')
        ->setCellValue('N' . $countMawbDataLine, 'OGA')
        ->setCellValue('O' . $countMawbDataLine, 'Detain');
    //Data from flight table
    $arrayIndex = "flight_" . $HLValue . "_data";
    foreach ($flightList[$arrayIndex] as $mawbData) {
        $countMawbDataLine++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, $flightList['flight_number'])
            ->setCellValue('B' . $countMawbDataLine, $mawbData['mawb'])
            ->setCellValue('C' . $countMawbDataLine, $mawbData['hawb'])
            ->setCellValue('D' . $countMawbDataLine, $mawbData['from_country'])
            ->setCellValue('E' . $countMawbDataLine, $mawbData['sender_name'])
            ->setCellValue('F' . $countMawbDataLine, $mawbData['to_country'])
            ->setCellValue('G' . $countMawbDataLine, $mawbData['receiver_name'])
            ->setCellValue('H' . $countMawbDataLine, $mawbData['weight'])
            ->setCellValue('I' . $countMawbDataLine, $mawbData['description'])
            ->setCellValue('J' . $countMawbDataLine, $mawbData['no_of_pieces'])
            ->setCellValue('K' . $countMawbDataLine, $mawbData['foreign_value'] . " " . $mawbData['currency'])
            ->setCellValue('L' . $countMawbDataLine, $mawbData['custom_value'])
            ->setCellValue('M' . $countMawbDataLine, $mawbData['duty'])
            ->setCellValue('N' . $countMawbDataLine, '')
            ->setCellValue('O' . $countMawbDataLine, '');
        $objPHPExcel->getActiveSheet()->getStyle('A' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('M' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('N' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('O' . $countMawbDataLine)->applyFromArray($lastTable);
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'Name and Surname')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'FOR THE IMPORTER HEAR BY APPLY FOR THE RELEASE OF THE ABOVE MENTIONED GOODS IN TERMS OF')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine);
    $countMawbDataLine++;
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'SECTION 38 (1)(A) AND DECLARE THAT THE PARTICULARS HEREIN ARE TRUE AND CORRECT  AND COMPLY')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'WITH THE  PROVISIONS OF THE CUSTOMS AND EXCISE ACT.')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine);
    $countMawbDataLine += 3;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'NAMENS INVOERDER DOEN HIERMEE AANSOEK OM LOSSING VAN DIE BOGENOEMDE GOEDERE INGEVOLGE')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'ARTIKEL 38 (1) (a) EN VERKLAAR DAT DIE BESONDERHEDE HIERIN WAAR EN KORREK IS EN AAN DIE')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'PEPALINGS VAN DIE DOEANE EN  AKSYNSWET VOLDOEN')->mergeCells('A' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'DATE');
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $countMawbDataLine)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $countMawbDataLine, 'Signature / Handtekening')->mergeCells('M' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $countMawbDataLine . ':O' . $countMawbDataLine)->applyFromArray($borderThinBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'INSTRUCTIONS BY THE CONTROLLER')->mergeCells('A' . $countMawbDataLine . ':F' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $countMawbDataLine, '')->mergeCells('G' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, '')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'OF CUSTOMS AND EXCISE')->mergeCells('A' . $countMawbDataLine . ':F' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $countMawbDataLine, 'ENDORSEMENTS')->mergeCells('G' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, 'PLACE OF ENTRY')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'OPDRAG DEUR DIE KONTROLEUR VAN')->mergeCells('A' . $countMawbDataLine . ':F' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $countMawbDataLine, 'ENDOSSEMENTE')->mergeCells('G' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('G' . $countMawbDataLine)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, 'KLARINGSPLEK')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('K' . $countMawbDataLine)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, 'DOEANE EN AKSYNS')->mergeCells('A' . $countMawbDataLine . ':F' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $countMawbDataLine, '')->mergeCells('G' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, '')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, 'J.S.A.')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('K' . $countMawbDataLine)->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, '')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, 'NUMBER AND DATE')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, '')->mergeCells('A' . $countMawbDataLine . ':J' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $countMawbDataLine, 'NOMMER EN DATEM ')->mergeCells('K' . $countMawbDataLine . ':O' . $countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $folder_path = "../_assets/";
    if (!file_exists($folder_path . "export_manifest")) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Export SA Manifest');
    $fileName = "export_manifest/export-SA-" . strtoupper($HLValue) . "-manifest-" . time() . rand('1', '10000') . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $fileName . '');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path . $fileName);
    return $fileName;
}

function getBagManifestExcel($bagId)
{
    $bagging = new Bagging($bagId);
    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
    $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
    $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getList();
    if (count($parcelBaggingMappingObj) > 0) {
        $baggingData = [];
        $shipper = "";
        $consignee = "";
        foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
            $parcelObj = new Parcel($parcelBaggingMappingArr->getParcelId());
            $consignmentObj = new Consignment($parcelObj->getConsignmentId());
            $senderCountry = new Country($consignmentObj->getSenderCountryId());
            $consigneeCountry = new Country($consignmentObj->getCountryId());
            $senderCountryName = $senderCountry->getName();
            $consigneeCountryName = $consigneeCountry->getName();

            $shipper = $consignmentObj->getSenderCompany() . " " . $consignmentObj->getSenderAddressLine1() . " " . $consignmentObj->getSenderAddressLine2() . " " . $consignmentObj->getSenderAddressLine3() . " " . $consignmentObj->getSenderCity() . " " . $consignmentObj->getSenderPostcode() . " " . $senderCountryName;

            $consignee = $consignmentObj->getCompany() . " " . $consignmentObj->getAddressLine1() . " " . $consignmentObj->getAddressLine2() . " " . $consignmentObj->getAddressLine3() . " " . $consignmentObj->getCity() . " " . $consignmentObj->getPostcode() . " " . $consigneeCountryName;
            $baggingData[$bagging->getBagnumber()][$parcelObj->getTrackingNumber()] = [$shipper, $consignee, $parcelObj->getWeight(), $parcelObj->getItemvalue() . " " . $consignmentObj->getCurrency(), $consignmentObj->getDescription()];
        }
    }

    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
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
            )
        )
    );
    $borderBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderThinBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderTop = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            )
        )
    );
    $topFiveBox = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $topText = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 22,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNine = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $columnTen = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $alignCenterTopBottom = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeft = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeftBold = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Times New Roman',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastHeaderYellow = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFF71C')
        ),
    );
    $alignCenterTopBottomRightBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightLeftBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightLeftThin = array(
        'font' => array(
//                'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottomRightLeftThinNew = array(
        'font' => array(
//                'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottomRightBoldExtra = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnTenPlus = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNineH = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(25);
    // Add some data
    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($alignCenterTopBottomRightBold);
    $objPHPExcel->getActiveSheet()->getStyle('G1:H1')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Tag: ' . $bagging->getBagnumber())->mergeCells('A1:B1')
        ->setCellValue('G1', 'Date: ' . DATE("Y-m-d"))->mergeCells('G1:H1')
        ->setCellValue('B3', 'Shipment Details')
        ->setCellValue('C3', 'Shipper')
        ->setCellValue('D3', 'Consignee')
        ->setCellValue('E3', 'Weight')
        ->setCellValue('F3', 'Value')
        ->setCellValue('G3', 'Description');
    // Miscellaneous glyphs, UTF-8
    if (count($baggingData) > 0) {
        $countMawbDataLine = 4;
        $totalParcel = 0;
        $totalWeight = 0;
        $totalValue = 0;
        foreach ($baggingData as $bagNumber => $conDataArr) {
            $totalParcel = count($conDataArr);
            foreach ($conDataArr as $parcelTracking => $parcelData) {
                $totalWeight += $parcelData['2'];
                $totalValue += $parcelData['3'];
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . $countMawbDataLine, 'Tracking : ' . $parcelTracking)
                    ->setCellValue('C' . $countMawbDataLine, $parcelData['0'])
                    ->setCellValue('D' . $countMawbDataLine, 'c/o ' . $parcelData['1'])
                    ->setCellValue('E' . $countMawbDataLine, $parcelData['2'])
                    ->setCellValue('F' . $countMawbDataLine, $parcelData['3'])
                    ->setCellValue('G' . $countMawbDataLine, $parcelData['4']);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThinNew);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle("C" . $countMawbDataLine)->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle("D" . $countMawbDataLine)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $countMawbDataLine++;
            }
        }
    }
    $countMawbDataLine = $countMawbDataLine + 1;
    $cssCount = $countMawbDataLine + 1;
    $objPHPExcel->getActiveSheet()->getStyle('B' . $countMawbDataLine . ':E' . $cssCount)->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $countMawbDataLine, 'Totals:');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $countMawbDataLine, 'Shipments');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $countMawbDataLine, 'Total Weight');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $countMawbDataLine, 'Value');
    $countMawbDataLine = $countMawbDataLine + 1;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $countMawbDataLine, '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $countMawbDataLine, $totalParcel);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $countMawbDataLine, $totalWeight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $countMawbDataLine, $totalValue);

    $folder_path = "../_assets/export_manifest/";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Bag Manifest');
    $fileName = "Bag-Manifest-" . strtoupper($bagging->getBagValue()) . "-" . time() . rand('1', '100000') . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $fileName . '');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path . $fileName);
    $bagging->setBagManifest(SETTING_MAIN_ASSETS . "export_manifest/" . $fileName);
    $bagging->save();
    return SETTING_MAIN_ASSETS . "export_manifest/" . $fileName;
}

function getExcelFileValueBased($flightList, $HLValue = "lv")
{
    $fileName = "Manifest_" . strtoupper($HLValue);
    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $excel_column = 1;
    // Design array here
    //
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        )
    );
    $boldAllignCenter = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        )
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    // Add some data
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('E1', 'Manifest')->mergeCells('E1:H1')
        ->setCellValue('A3', 'From:' . $flightList['flight_from_country'])->mergeCells('A3:B3')
        ->setCellValue('D3', 'To: ' . $flightList['flight_to_country'])->mergeCells('D3:E3')
        ->setCellValue('F3', 'MAWB:' . $flightList['flight_' . $HLValue . '_data'][0]['mawb'])->mergeCells('F3:G3')
        ->setCellValue('A4', 'Create Date:' . Date("Y-m-d"))->mergeCells('A4:B4')
        ->setCellValue('A5', 'Total Pieces:' . $flightList['flight_total_parcel_' . $HLValue])->mergeCells('A5:B5')
        ->setCellValue('E5', 'Total Weight:' . $flightList['flight_total_parcel_weight_' . $HLValue])->mergeCells('E5:F5');
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('D3:E3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('F3:G3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('A5:B5')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('E5:F5')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('K7')->applyFromArray($lastHeader);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'No.')
        ->setCellValue('B7', 'HAWB')
        ->setCellValue('C7', 'Tracking No.')
        ->setCellValue('D7', 'Country')
        ->setCellValue('E7', 'Shipper Details')
        ->setCellValue('F7', "Consignee's Details")
        ->setCellValue('G7', 'No. of Pieces')
        ->setCellValue('H7', 'Weight (kg)')
        ->setCellValue('I7', 'Description')
        ->setCellValue('J7', 'Value')
        ->setCellValue('K7', 'Currency');
    //Data from flight table
    $number = 1;
    $countMawbDataLine = 7;
    $flightDataArr = [];
    if ($fileName == "Manifest_LV") {
        $flightDataArr = $flightList['flight_lv_data'];
    } else {
        $flightDataArr = $flightList['flight_hv_data'];
    }
    foreach ($flightDataArr as $mawbData) {
        $countMawbDataLine++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $countMawbDataLine, $number)
            ->setCellValue('B' . $countMawbDataLine, "'" . $mawbData['hawb'] . "'")
            ->setCellValue('C' . $countMawbDataLine, $mawbData['tracking_no'])
            ->setCellValue('D' . $countMawbDataLine, $mawbData['to_country'])
            ->setCellValue('E' . $countMawbDataLine, $mawbData['sender_name'])
            ->setCellValue('F' . $countMawbDataLine, $mawbData['receiver_name'])
            ->setCellValue('G' . $countMawbDataLine, $mawbData['no_of_pieces'])
            ->setCellValue('H' . $countMawbDataLine, $mawbData['weight'])
            ->setCellValue('I' . $countMawbDataLine, $mawbData['description'])
            ->setCellValue('J' . $countMawbDataLine, $mawbData['foreign_value'])
            ->setCellValue('K' . $countMawbDataLine, $mawbData['currency']);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $countMawbDataLine)->applyFromArray($lastTable);
        $number++;
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $folder_path = "../_assets/";
    if (!file_exists($folder_path . "export_manifest")) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle($fileName);
    $fileName = "export_manifest/" . $fileName . "-" . time() . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $fileName . '');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path . $fileName);
    return $fileName;
}

function getBagIdWithValue($parcelValue, $consignmentCurrency, $fligthToCountryId)
{
    $return = [];
    if ($fligthToCountryId > 0) {
        $countryObj = new Country($fligthToCountryId);
        $bagLowValue = $countryObj->getBagLowValue();
        $currencyId = $countryObj->getCurrencyId();
        $currencyObj = new Currency($currencyId);
        $rightSymbolFromCurrency = $currencyObj->getRightsymbol();
        $countryLowValue = Currency::convertCurrency($consignmentCurrency, $rightSymbolFromCurrency, $parcelValue);
        $return['value'] = $countryLowValue;
        $return['bag_low_value'] = $bagLowValue;
    }
    return $return;
}

// Najam Code
function mawbManifestExcel($parcelDetails)
{
    $mawbNumber = $parcelDetails['mawb_number'];
    $fromAddress = $parcelDetails['flight_from_address'];
    $toAddress = $parcelDetails['flight_to_address'];
    $parcelDetails = $parcelDetails['parcel_data'];

    $styleMainHeading = array(
        'font' => array(
            'bold' => true,
            'size' => 16
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleHeading = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleHeadingMiddle = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleData = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);

    $objPHPExcel->getActiveSheet()->mergeCells('E1:H2');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleMainHeading);
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', "MANIFEST");
    $objPHPExcel->getActiveSheet()->SetCellValue('A3', "From:");
    $objPHPExcel->getActiveSheet()->SetCellValue('A4', $fromAddress)->mergeCells('A4:B6');
    $objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
    $objPHPExcel->getActiveSheet()->SetCellValue('D3', "To:");
    $objPHPExcel->getActiveSheet()->SetCellValue('D4', $toAddress)->mergeCells('D4:E6');
    $objPHPExcel->getActiveSheet()->getStyle('D4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->mergeCells('F4:G6');
    $objPHPExcel->getActiveSheet()->getStyle('F4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->getStyle('F4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('F4', "MAWB: " . $mawbNumber);
    $objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
    $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($styleHeadingMiddle);
    $objPHPExcel->getActiveSheet()->SetCellValue('A7', "Create Date:" . Date("Y-m-d"));
    $objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
    $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleHeadingMiddle);
    $objPHPExcel->getActiveSheet()->SetCellValue('A8', "Total Pieces: " . count($parcelDetails));
    $objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleHeadingMiddle);


    $heading = [
        "No.",
        "HAWB",
        "Tracking No.",
        "Country",
        "Shipper Details",
        "Consignee's Details",
        "Contact Number",
        "No. of Pieces",
        "Weight (kg)",
        "Description",
        "Value",
        "Currency",
        "Bag No",
        "Note",
    ];
    $rowNum = 10;
    $colNum = 'A';
    foreach ($heading as $value) {
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $value);
        $colNum++;
    }
    $rowNum++;
    $curCount = 1;
    $parcelWeight = 0;
    foreach ($parcelDetails as $key => $DataArr) {
        if ($DataArr[6] > 0)
            $parcelWeight += $DataArr[6];
        $colNum = 'A';
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $curCount);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[0]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[1]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[2]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[3]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[4]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[11]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[5]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[6]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[7]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[8]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[9]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[10]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $DataArr[12]);
        $rowNum++;
        $curCount++;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('E8', "Total Weight: " . $parcelWeight . " KG");
    $folder_path = "../_assets/export_manifest/";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('MAWB Manifest');
    $fileName = "MAWB-Manifest-" . time() . rand(1, 1000) . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $fileName . '');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path . $fileName);
    return "export_manifest/" . $fileName;
}

// End najam Code
function getLvHvDataMawb($mawbId, $flightId, $bagType)
{
    // Make Data
    $mawb = new Mawb($mawbId);
    $mawbNumber = $mawb->getMawbNumber();
    $flightData = new FlightInfo($flightId);
    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
    $mawbParcelMappingFilter->addJoin('bagging bag', "bag.id", "mpm.bag_id");
    $mawbParcelMappingFilter->addFieldFilter("    mpm.mawb_id", $mawbId);
    $mawbParcelMappingFilter->addFieldFilter("    bag.bag_value", $bagType);
    $mawbParcelMappingFilter->addGroupBy("mpm.bag_id");
    $mawbParcelMappingObj = $mawbParcelMappingFilter->getList();
    $mawbBagCount = 0;
    $bagParcelWeight = 0;
    $parcelDetails = [];
    $shipperCountry = "";
    $recieverCountry = "";
    $shipperCountryName = "";
    $recieverCountryName = "";

    $shipperCountry = new Country($flightData->getCountryId());
    $shipperCountryName = $shipperCountry->getName();

    $recieverCountry = new Country($flightData->getDestinationCountryId());
    $recieverCountryName = $recieverCountry->getName();

    $parcelDetails['mawb_number'] = $mawbNumber;
    $parcelDetails['flight_from_address'] = $flightData->getShipperCo() . ' ' . $flightData->getShippersAddressline1() . ' ' . $flightData->getShippersAddressline2() . ' ' . $shipperCountryName;

    $parcelDetails['flight_to_address'] = $flightData->getConsigneeCo() . ' ' . $flightData->getAddressLine1() . ' ' . $flightData->getAddressLine2() . ' ' . $recieverCountryName;
    if (count($mawbParcelMappingObj) > 0) {
        $mawbBagCount = count($mawbParcelMappingObj);
        foreach ($mawbParcelMappingObj as $mawbParcelMappingArr) {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $mawbParcelMappingArr->getBagId());
            $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getList();
            $bagging = new Bagging($mawbParcelMappingArr->getBagId());
            //if($bagging->getBagValue() == $bagType){
            if (count($parcelBaggingMappingObj) > 0) {
                $MawbBagParcelCount = count($parcelBaggingMappingObj);
                $shipper = "";
                $reciever = "";
                foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                    $parcel = new parcel($parcelBaggingMappingArr->getParcelId());
                    $bagParcelWeight = $bagParcelWeight + $parcel->getWeight();
                    $consignment = new Consignment($parcel->getConsignmentId());
                    $shipperCountryObj = new Country($consignment->getSenderCountryId());
                    $recCountryObj = new Country($consignment->getCountryId());
                    $recCountryName = $recCountryObj->getName();
                    $shipperCountryName = $shipperCountryObj->getName();


                    $shipper = $consignment->getSenderCompany() . " " . $consignment->getSenderAddressLine1() . " " . $consignment->getSenderAddressLine2() . " " . $consignment->getSenderAddressLine3() . " " . $consignment->getSenderCity() . " " . $consignment->getSenderPostcode() . " " . $shipperCountryName;

                    $reciever = $consignment->getCompany() . " " . $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3() . " " . $consignment->getCity() . " " . $consignment->getPostcode() . " " . $recCountryName;

                    $parcelDetails['parcel_data'][$parcel->getId()] = [
                        $consignment->getHawb(),
                        $parcel->getTrackingNumber(),
                        $recCountryName,
                        $shipper,
                        $reciever,
                        '1',
                        $parcel->getWeight(),
                        $consignment->getDescription(),
                        $parcel->getItemvalue(),
                        $consignment->getCurrency(),
                        $bagging->getBagnumber(),
                        $consignment->getTelephone(),
                        $consignment->getNotes()
                    ];
                }
            }
            //}
        }
    }
    return $parcelDetails;
}

?>