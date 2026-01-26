<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'ivisualcomponent', 'ddl.inc'
        ], 'library');
include_classes(['errorlist.class'
        ], 'visualcomponents');
include_classes([
    'skuorder.class',
    'skuorderfilter.class',
    'sku.class',
    'skufilter.class',
    'skuordermapping.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'licenceplate.class',
    'licenceplatefilter.class']);
include_classes([
    'tcpdf',
        ], '3rdparty/tcpdf');

include_classes([
    'pdfmerger'], 'labels');

class Page extends BasePage {

    private $licencePlate;
    private $skuorder;
    private $id = NULL;
    private $breadcrumb = '';
    private $user = NULL;
    private $isCountry = 0;

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'wms_sku.php' => 'Add SKUs'
        );
        $this->setTitle("SKU Order List");

        if ($this->form_vars["form_action"] == 'printSKU') {
            
            $this->pdf = new PdfBase(PDF_PAGE_ORIENTATION, 'mm', 'A4', true, 'UTF-8', false);
            $this->pdf->SetPrintFooter(false);
            $this->pdf->setAutoPageBreak(True, 0);
            
             $style = array(
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 11,
            );
            $path = "../_assets/pdf/" . date('Y_m_d');
            if (!file_exists($path)) {
                mkdir($path);
            }

            $WMSLicencePlate = LicencePlate::getLicencePlateNumber(201);
            if (trim($WMSLicencePlate['STATUS']) == 'ERROR')
                return $WMSLicencePlate;
            else {
                $trackingNumber = $WMSLicencePlate["RANGE"];
                $WMSbarcode = $WMSLicencePlate["PREFIX"] . $trackingNumber . $WMSLicencePlate["SUFIX"];
                $WMSLicencePlate = $WMSbarcode;
            }
            $warehouseId = $this->form_vars["select_warehouse"];
            $skuOrder = new SkuOrder();
            $skuOrder->setShipmentReference($WMSLicencePlate);
            $skuOrder->setDateCreated(strtotime(date('Y-m-d H:i:s')));
            $skuOrder->setWarehouseId($warehouseId);
            $skuOrder->setUserId($this->user->getId());
            $skuOrder->save();
            
            $skuOrderId = $skuOrder->getId();
            
            $count = count($this->form_vars["select_sku"]);
            $totalSKU = 0;
            for($j= 0; $j<$count; $j++)
            {
                $skuId = $this->form_vars["select_sku"][$j]; 
                 
                $noOfShipments = $this->form_vars["number_of_shipments"][$j]; 
                $totalSKU += $noOfShipments;
                $skuObj = new Sku($skuId);

                $this->pdf->AddPage();

                $counter = 1;
                $i = '0';
                $x = 20;
                $y = 35;

                for (; $i < $noOfShipments; $i++) {
                    if ($i > 0 && ($i % 24) == 0) {
                        $this->pdf->AddPage();
                        $x = 20;
                        $y = 35;
                    }
                    $this->pdf->write1DBarcode($skuObj->getSku(), 'C128', $x, $y, 70, 18, 0.25, $style, 'L');
                    $x = $x + 65;
                    $y = $y;

                    if ($counter == 3) {
                        $y = $y + 30;
                        $x = 20;
                        $counter = 1;
                    } else {
                        $counter++;
                    }
                }
                
                $skuOrderMapping = new SkuOrderMapping();
                $skuOrderMapping->setSkuId($skuId);
                $skuOrderMapping->setSkuOrderId($skuOrderId);
                $skuOrderMapping->setShippedQuantity($noOfShipments);
                //$skuOrderMapping->setWarehouseId($warehouseId);
                $skuOrderMapping->save();
            }
            $skuOrder->setTotalSkuQuantity($totalSKU);
            $skuOrder->save();
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $WMSLicencePlate . ".pdf", "F");

            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = '_assets/pdf/' . date('Y_m_d') . '/' . $WMSLicencePlate . ".pdf";
            echo json_encode($output);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "skuorderlist_ajax") {
            // Get current user
            $this->skuorder = new SkuOrderFilter();
            $this->skuorder->join("sku_order_mapping sop", "so.id = sop.sku_order_id", "INNER");
            $this->skuorder->join("sku s", " sop.sku_id = s.id", "INNER");
            $this->skuorder->join("warehouse w", " so.warehouse_id = w.id", "INNER");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $sku_id = $this->form_vars['search_sku'];
                if (!empty($sku_id)) {
                    $this->skuorder->addFilter(" sop.sku_id = '" . $sku_id . "' ");
                }
                $shipment_reference = $this->form_vars['search_shipment_reference'];
                if (!empty($shipment_reference)) {
                    $this->skuorder->addFilter(' so.shipment_reference = "' . $shipment_reference . '"');
                }
                $warehouse = $this->form_vars['search_warehouse'];

                if (!empty($warehouse)) {
                    $this->skuorder->addFilter('so.warehouse_id = "' . $warehouse . '"');
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
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $this->skuorder->orderBy(strtolower("so." . $dataTableColumnName), $orderFalse);
            }

            $iTotalRecords = $this->skuorder->getCount(false, false);

            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->skuorder->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->skuorder->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->skuorder->orderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->skuorder->orderBy('so.id', desc);
            }
            
            $this->skuorder->groupBy('so.shipment_reference');
            
            $skyOrderList = $this->skuorder->getList("so.id, so.consignment_id, so.shipment_reference, so.received_quantity, total_sku_quantity ,  GROUP_CONCAT(s.sku) as sku 
            ,w.warehouse_name as warehouse, so.warehouse_id");

            $rangeDataArr = array();
            foreach ($skyOrderList as $skulist) {
                $orderId = base64_encode($skulist->getId());
                if ($skulist->getConsignmentId() > 0) {
                    $queryString = "&type=skuedit&id=" . $skulist->getConsignmentId();
                } else {
                    $queryString = "&type=sku&id=0";
                }
                $rangeArr['actionss'] = ' <a class=" createLabel btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip"                                                     
                                                    href="consignment_add.php?skuid=' . $orderId . $queryString . '">'
                                .($skulist->getConsignmentId() > 0  ?  '<span class=" fa fa-edit"></span>' :  '<span class=" fa fa-plus"></span> ') .
                                                    '</a>';

                $rangeArr['sku_number'] = $skulist->getSku();
                $rangeArr['shipment_reference'] = '<a href="tracking.php?tracking_number='.$skulist->getShipmentReference().'" target="_blank" >'.$skulist->getShipmentReference().'</a>';
                $rangeArr['shipped'] = $skulist->getTotalSkuQuantity();
                $rangeArr['received'] = $skulist->getReceivedQuantity();
                $rangeArr['warehouse'] = $skulist->getWarehouse();
                $rangeDataArr[] = $rangeArr;
            }
            $rangeDataArr['data'] = $rangeDataArr;
            $rangeDataArr['draw'] = $sEcho;
            $rangeDataArr['recordsTotal'] = $iTotalRecords;
            $rangeDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rangeDataArr);
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

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />

        <style type="text/css">
            .ms-container {
                width: 100%;
            }
        </style>

        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>


        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            var grid = "";
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
                                [10, 20, 50, 100],
                                [10, 20, 50, 100] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "add_sku_order.php?action=skuorderlist_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "sku_number"},
                                {"data": "shipment_reference"},
                                {"data": "warehouse"},
                                {"data": "shipped"},
                                {"data": "received"},
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

            function popupwindow(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }

            $(document).ready(function () {
                DataTableFun.init();

                $("#btn_Save").click(function () {
                    var form_data = $("#skuAddOrder").serializeArray();
                    form_data.push({name: 'form_action', value: 'printSKU'});
                    $.ajax({
                            type: "POST",
                            url: "add_sku_order.php",
                            data: form_data,
                            dataType: "json",
                            success: function (response) {
                                    if (response.STATUS == 'SUCCESS')
                                    {
                                        grid.getDataTable().ajax.reload();
                                        popupwindow(response.LABEL, 'SKU Label', 450, 550);
                                    }
                            },
                            error: function () {
                                    //alert('error handing here');
                            }
                    });
                });

                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                    $('#elindex-hardcode').val(1);
                }
                $(document).on('click', '.add_more_sku_order_keys', function () {
                    elindex++;
                    $('#elindex-hardcode').val(elindex +1);
                    var clone = $(this).parent().parent().parent().clone();
                    var select_sku = $(clone).find('.select_sku .bs-select').attr('name');
                    var select_sku_id = $(clone).find('.select_sku .bs-select').attr('id');
                    var number_of_shipments = $(clone).find('.number_of_shipments').attr('name');
                    var number_of_shipments_id = $(clone).find('.number_of_shipments').attr('id');
                    $(this).remove();
                    $(clone).find('.select_sku .bs-select').attr('name', select_sku.replace(/\d+/, elindex));
                    $(clone).find('.select_sku .bs-select').attr('id', select_sku_id.replace(/\d+/, elindex));
                    $(clone).find('.number_of_shipments').attr('name', number_of_shipments.replace(/\d+/, elindex));
                    $(clone).find('.number_of_shipments').attr('id', number_of_shipments_id.replace(/\d+/, elindex));
                    $(clone).find('.bootstrap-select.select_sku').replaceWith(function () {
                        return $('#select_sku_' + elindex, this);
                    });
                    $(clone).find('#select_sku_' + elindex).selectpicker('refresh');
                    $(clone).find('button.remove_sku_order_key').show();
                    $(clone).find('button.remove_sku_order_key').removeClass('initial-button');;
                    $(clone).appendTo($('.sku_container'));
                });
                $(document).on('click', '.remove_sku_order_key', function () {
                    var el = $(this);
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    if ($('.sku_container .remove_sku_order_key').length > 1) {
                                        $(el).parent().parent().remove();
                                        $('#elindex-hardcode').val(elindex);                                        
                                        if ($('.add_more_sku_order_keys').length == 0) {
                                            var addMore = $(el).parent().find('.add_more_sku_order_keys').clone();
                                            $('.sku_container .remove_sku_order_key').last().parent().prepend(addMore);
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                });
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
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-plus"></i>
                        Create SKU Order
                    </div>
                    <div class="actions">
                        <a href="add_sku.php" class="btn blue"><span></span><i class="fa fa-plus"></i>&nbsp;Add SKU</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <form name="skuAddOrder" id="skuAddOrder" action="" method="POST">           
                        <div class="row display-none" id="res_message">
                            <div class="col-md-12">
                                <div class="alert alert-success"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group"> 
                                    <div class="has-float-label input-icon right">
                                        <?php
                                        echo Ddl::generateDDL('select_warehouse', 'WarehouseFilter', '  is_active = 1 ', 'warehouse name', 'id', $select_warehouse, 'class="form-control select2" required data-toggle="tooltip" data-placement="top" title="Select Hub" data-original-title="Select Hub"', 'Please select', '', 'select_warehouse', 'Select Hub');
                                        ?>
                                        <label>Warehouse <span class="red-18">*</span> </label>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-3">
                                <label>SKU</label>
                            </div>
                            <div class="col-md-3">
                                  <label>Number of Items</label>
                            </div>
                            <div class="col-md-3">
                                &nbsp;
                            </div>
                        </div>
                        <div class="sku_container" id="sku_container">
                            <div class="form-group">
                              
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="has-float-label input-icon right">
                                            <?php
                                            echo Ddl::generateDDL('select_sku[0]', "skuFilter", ' active = "1"', "sku", "id", $select_sku, ' class="bs-select input-sm form-control form-filter select_sku" data-container="body" data-live-search="true"  data-show-subtext="true"', 'Select SKU', '', 'select_sku_0');
                                            ?>                                          
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="has-float-label input-icon right">                                    
                                            <i class="fa fa-map-marker"></i> 
                                            <input name="number_of_shipments[0]" id="number_of_shipments_0" value="" size="50" class="form-control number_of_shipments" title="Number of Shipments" maxlength="35" placeholder="Number of Shipments" rel="tooltip" data-original-title="Number of Shipments" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-success add_more_sku_order_keys"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove_sku_order_key initial-button"><i class="fa fa-minus"></i></button>                                                
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        
                      
                        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />

                        
                        <br />
                        <div class="row " style="text-align:centre;" align="right">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("Confirm"); ?>"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>  
                        </form>
                </div>

                <div class="modal fade" tabindex="-1" role="dialog" id="add-sku-popup">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="modal-title text-primary"><strong>Add SKU: <span id="AddSKU"></span> </strong> </h4>                                
                                    </div>                            
                                </div>
                            </div>
                            <div class="modal-body">
                                <form name="AddSKUForm" id="AddSKUForm" action="wms_sku.php" method="POST" enctype="multipart/form-data">  
                                    <input type="hidden" name="form_action" id="form_action" value="add_SKU" />
                                    <div class="col-md-12" id="errorMessage">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="label-account">SKU</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku" name="sku" type="text" placeholder="SKU" value="" rel="tooltip" data-original-title="SKU">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Customer ID</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="customer_id" name="customer_id" type="text" placeholder="Customer ID" value="<?php echo $this->user->getUserName() ?>" rel="tooltip" data-original-title="Customer ID" readonly="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">SKU Name</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_name" name="sku_name" type="text" placeholder="SKU NAME" value="" rel="tooltip" data-original-title="SKU Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="label-account">Description</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_description" name="sku_description" type="text" placeholder="Description" value="" rel="tooltip" data-original-title="Description">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Notes</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_notes" name="sku_notes" type="text" placeholder="Notes" value="" rel="tooltip" data-original-title="Notes">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">HS Code</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_hscode" name="sku_hscode" type="text" placeholder="HS Code" value="" rel="tooltip" data-original-title="HS Code">
                                                </div>
                                            </div>
                                        </div>                            
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="label-account">Gross Weight</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_gross_weight" name="sku_gross_weight" type="text" placeholder="SKU Gross Weight" value="" rel="tooltip" data-original-title="SKU Gross Weight">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Net Weight</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_net_weight" name="sku_net_weight" type="text" placeholder="SKU Net Weight" value="" rel="tooltip" data-original-title="SKU Net Weight">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Price</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_price" name="sku_price" type="text" placeholder="SKU Price" value="" rel="tooltip" data-original-title="SKU Price">
                                                </div>
                                            </div>
                                        </div>                           
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="label-account">Length</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_length" name="sku_length" type="text" placeholder="Length" value="" rel="tooltip" data-original-title="Length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Width</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_width" name="sku_width" type="text" placeholder="WIDTH" value="" rel="tooltip" data-original-title="Width">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-account">Height</label>
                                            <div class="form-group">
                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                                    <input class="form-control form-filter" id="sku_height" name="sku_height" type="text" placeholder="Height" value="" rel="tooltip" data-original-title="Height">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Active</label><br />
                                                <input id="active_flag" <?php echo ($active_flag == '1' ? 'checked="checked"' : ''); ?> name="active_flag" type="checkbox" class="make-switch"  data-on-text="Yes"  data-off-text="No"  data-on-color="primary" data-off-color="danger">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <input id="btn_AddSKU" type="button" class="btn btn-primary" value="Save"/>
                                <input id="btn_Cancel" type="button" class="btn btn-primary" value="Cancel"/>                       
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

            </div>

            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                        SKU Order List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>SKU</th>
                                    <th>Shipment Reference</th>
                                    <th>Destination</th>
                                    <th>Shipped</th>
                                    <th>Received</th>


                                </tr>
                                <tr role="row" class="filter">
                                    <td width = "8%">
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td class="user_acccount_correct_button">
        <?php
        echo Ddl::generateDDL('search_sku', "skuFilter", ' active = "1"', "sku", "id", $search_sku, ' class="bs-select input-sm form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"', 'Select SKU', '', 'search_sku');
        ?>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="search_shipment_reference" />
                                        </div>
                                    </td>
                                    <td>
        <?php
        echo Ddl::generateDDL('search_warehouse', 'WarehouseFilter', '  is_active = 1 ', 'warehouse name', 'id', $search_warehouse, 'class="form-control select2" required data-toggle="tooltip" data-placement="top" title="Select Hub" data-original-title="Select Hub"', 'Please select', '', 'search_warehouse', 'Select Hub');
        ?>
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
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
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

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
