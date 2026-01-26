<?php
/*
 * Page to create bag label for shipments which are sort on sorter by depot wise.
 */
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger',
    'baglabel.class'
    ], 'labels');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'ivisualcomponent', 'ddl.inc'
        ], 'library');
include_classes(['errorlist.class'
        ], 'visualcomponents');
include_classes([
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'bagging.class',
    'baggingfilter.class',
    'parcelbaggingmappingfilter.class',
    'parcelbaggingmapping.class',
    'warehouse.class',
    'warehousefilter.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
]);

class Page extends BasePage {

    private $id = NULL;
    private $user = NULL;
    private $sorterBaggingList = NULL;

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->user = $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'sorter_bagging.php' => 'Sorter Bagging'
        );
        $countryRange = 0;
        // common initialisation for ths page
        $this->setTitle("Sorter Bagging List");


        if (isset($_GET['action']) && $_GET['action'] == "sorterlist_ajax") {

            $this->sorterBaggingList = new ConsignmentFilter();
            $this->sorterBaggingList->addJoin("parcel p", "c.id = p.consignment_id");
            $this->sorterBaggingList->addJoin("services s", "s.id = c.service_id");
            $scanDate = date('Y-m-d',strtotime("-7 days"));
            $this->sorterBaggingList->addFilterNew(" p.chute_sorted > 0 and p.id not in  (select parcel_id from parcel_bagging_mapping) and c.date_scanned >= '".$scanDate."' and sort_type = 'PCZ' ");
            $this->sorterBaggingList->addGroupBy("p.chute_sorted, s.carrier_id");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
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
                $this->sorterBaggingList->AddOrderBy(strtolower("p.chute_sorted"), $orderFalse);
            }

            
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            
            $this->sorterBaggingList->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->sorterBaggingList->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->sorterBaggingList->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->sorterBaggingList->AddOrderBy('c.id', false);
            }
            $range_list = $this->sorterBaggingList->getListNew("count(c.id) as total_parcel, p.chute_sorted, s.name as service_name, s.id, c.service_id ", false, false);
            $iTotalRecords = count($range_list);
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $rangeDataArr = array();
            foreach ($range_list as $range) {
                $rangeArr['actionss'] = '';
                $rangeArr['actionss'] .= "<a data-id ='" . $range->getServiceId() . "' data-chute = '" . $range->getChuteSorted() . "' class='btnedit btn-xs blue btn ' title='Edit'>Bag Label </a>";

                $rangeArr['chute_sorted'] = $range->getChuteSorted();
                $rangeArr['total_shipments'] = $range->getTotalParcel();
                $rangeArr['service_name'] = $range->getServiceName();
                $rangeDataArr[] = $rangeArr;
            }
            $rangeDataArr['data'] = $rangeDataArr;
            $rangeDataArr['draw'] = $sEcho;
            $rangeDataArr['recordsTotal'] = $iTotalRecords;
            $rangeDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rangeDataArr);
            die;
        }

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "createBagLabel") {
            $sessionUser = SessionManager::getUser();
            $serviceId = (int) $_POST['serviceId'];
            $chuteNumber = (int) $_POST['chuteNumber'];
            $sourceCountryId = (isset($_POST['source_country_id']) ? $_POST['source_country_id'] : 0);
            $sourceWarehouseId = (isset($_POST['source_warehouse_id']) ? $_POST['source_warehouse_id'] : NULL);
            $destinationCountryId = (isset($_POST['destination_country_id']) ? $_POST['destination_country_id'] : 0);
            $destinationWarehouseId = (isset($_POST['destination_warehouse_id']) ? $_POST['destination_warehouse_id'] : NULL);
            $output = array();
            if ($serviceId > 0 && $chuteNumber > 0) {
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addJoin("parcel p", "c.id = p.consignment_id");
                $consignmentFilter->addJoin("services s", "s.id = c.service_id");
                $scanDate = date('Y-m-d',strtotime("-7 days"));
                $consignmentFilter->addFilterNew(" p.chute_sorted = '" . $chuteNumber . "' and c.service_id = '" . $serviceId . "' and p.id not in  (select parcel_id from parcel_bagging_mapping) and c.date_scanned >= '".$scanDate."' and sort_type = 'PCZ'");
                #$consignmentFilter->addGroupBy("p.chute_sorted, s.carrier_id");
                $consignmentList = $consignmentFilter->getListNew("p.tracking_number, p.id as parcel_id,  p.chute_sorted, s.name as service_name,  c.service_id, c.weight ", false, false);
                if (count($consignmentList) > 0) {
                    $trackingNumber = array();
                    $parcelId = array();
                    $serviceId = array();
                    $totalWeight = 0;
                    // Create Bag
                    $bagging = new Bagging();
                    $bagging->setDateCreated(time());
                    $bagging->setAccount($sessionUser->getUserAccount());
                    $bagging->setUserId($sessionUser->getId());
                    $bagging->setBagSourceWarehouseId($sourceWarehouseId);
                    $bagging->setBagSourceCountryId($sourceCountryId);
                    $bagging->setBagDestinationCountryId($destinationCountryId);
                    $bagging->setBagDestinationWarehouseId($destinationWarehouseId);
                    

                    //This is Not defined in this function
                    $bagging->setService("");
                    $bagging->setCountry("");
                    $bagging->setBagType("");
                    $bagging->setBagStatus("");
                    $bagging->save();
                    $bagId = $bagging->getId();

                    foreach ($consignmentList as $consignment) {
                        $trackingNumber[] = $consignment->getTrackingNumber();
                        $parcelId[] = $consignment->getParcelId();
                        $serviceId[] = $consignment->getServiceId();
                        $totalWeight += $consignment->getWeight();
                        //Save Data to parcel bagging mapping
                        $parcelBaggingMapping = new ParcelBaggingMapping();
                        $parcelBaggingMapping->setParcelId($consignment->getParcelId());
                        $parcelBaggingMapping->setBagId($bagId);
                        $parcelBaggingMapping->setAddedBy($sessionUser->getId());
                        $parcelBaggingMapping->setAddedDate(time());
                        $parcelBaggingMapping->save();
                    }
                    $bagging->setWeight($totalWeight);
                    $warehouseid = $sessionUser->getWarehouseId();
                    $warehouse = new Warehouse($warehouseid);
                    $serviceTypeArray = array_unique($serviceId);
                    if (count($serviceTypeArray) > 1) {
                        $bagging->setBagType("MIX");
                        $bagging->setBagNumber(substr($warehouse->getDescription(), 0, 4) . $bagId . 'MIX');
                    } else {
                        $bagging->setBagType("NORMAL");
                        $bagging->setBagNumber(substr($warehouse->getDescription(), 0, 4) . $bagId . substr(strtoupper($serviceTypeArray[0]), 0, 3));
                    }
                    $bagging->setBagStatus(1);
                    $bagging->setService(implode(",", $serviceTypeArray));
                    $bagging->setDateUpdated(time());
                    $bagging->save();
                
                    if ($bagId > 0 && count($serviceTypeArray) == 1) {
                        $serviceObj = new Services($serviceId[0]);
                        $className = trim($serviceObj->getLabelClassName());
                        $classFound = false;
                        if (trim($className) != '') {
                            $file = "../includes/labels/" . strtolower($className) . ".class.php";
                            if (is_file($file)) {
                                require_once($file);
                                
                                if(method_exists($className, "bagLabel"))
                                    $classFound = true;
                            }
                        }
                        if ($classFound) {
                            $classObject = new $className();
                            $bagLabelResponse = $classObject->bagLabel($trackingNumber);
                            if ($bagLabelResponse["STATUS"] == "SUCCESS") {
                                $bagging->setBagLabel($bagLabelResponse["LABEL"]);
                                $bagging->setBagNumber($bagLabelResponse["BAG_NUMBER"]);
                                $bagging->save();
                            }
                            $output = $bagLabelResponse;
                        } else {
                           $bagLabel = new BagLabel();
                           $output = $bagLabel->generateBagLabel($bagId);
                        }
                    }

                } else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "UNABLE TO FIND SHIPMENTS";
                }
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "UNABLE TO FIND SHIPMENTS";
            }
            echo json_encode($output);
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

            $(document).ready(function () {


                $(document).on('click', '.btnedit', function () {
                    $("#res_message div.alert").removeClass('alert-success');
                    $("#res_message div.alert").removeClass('alert-danger');
                    var e = $(this);
                    var serviceId = e.data('id');
                    var chuteNumber = e.data('chute');

                    $("#save_mawb_modal").modal("show");
                    $("#btn_save_pallet").click(function (e) {
                        if ($("#save_mawb_source_country_id").val() == "" || $("#save_mawb_destination_country_id").val() == "") {
                            $("#show_general_msg_master").css("display", "block");
                            $("#show_general_msg_master div.alert").addClass('alert-danger');
                            $("#show_general_msg_master div.alert").html("Please select source and destination country.");
                        } else {
                            var source_country_id = $("#save_mawb_source_country_id").val();
                            var source_warehouse_id = $("#save_mawb_source_warehouse_id").val();
                            var destination_country_id = $("#save_mawb_destination_country_id").val();
                            var destination_warehouse_id = $("#save_mawb_destination_warehouse_id").val();

                            $.ajax({
                                method: "POST",
                                url: "sorter_bagging.php",
                                data: {serviceId: serviceId, chuteNumber: chuteNumber, source_country_id: source_country_id, source_warehouse_id: source_warehouse_id, destination_country_id: destination_country_id, destination_warehouse_id: destination_warehouse_id, func: "createBagLabel"}
                            }).done(function (data) {
                                var result = JSON.parse(data);
                                if (result.STATUS == "SUCCESS") {
                                $("#save_mawb_destination_country_id").val("").selectpicker('refresh');
                                get_save_mawb_destination_warehouse();
                                $("#save_mawb_modal").modal("hide");
                                    var labelLink = "<?php echo SETTING_MAIN_ASSETS; ?>/"+result.LABEL;
                                    newwindow = window.open(labelLink, 'Label', 'height=400,width=400');
                                    if (window.focus) {
                                        newwindow.focus();
                                    }
                                } else
                                {
                                  $("#show_general_msg_master").css("display", "block");
                                  $("#show_general_msg_master div.alert").addClass('alert-danger');
                                  $("#show_general_msg_master div.alert").html(result.MESSAGE);
                                }
                                grid.getDataTable().ajax.reload();
                                
                            });
                        }
                    });


                });

            });


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
                                "url": "sorter_bagging.php?action=sorterlist_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "chute_sorted"},
                                {"data": "total_shipments"},
                                {"data": "service_name"},
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
                get_save_mawb_source_warehouse();
            });
            function get_save_mawb_source_warehouse() {
        //  save_mawb_source_warehouse_id
                var source_country_id = $("#save_mawb_source_country_id").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: source_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#save_mawb_source_warehouse_id").html("");
                            $("#save_mawb_source_warehouse_id").html(data);
                            $('#save_mawb_source_warehouse_id').selectpicker("refresh");
                        } else {
                            $("#save_mawb_source_warehouse_id").html("");
                            $('#save_mawb_source_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });
            }
            function get_save_mawb_destination_warehouse() {
        //  save_mawb_destination_warehouse_id
                var destination_country_id = $("#save_mawb_destination_country_id").val();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php",
                    data: {action: "get_country_warehouse_id", country_id: destination_country_id},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#save_mawb_destination_warehouse_id").html("");
                            $("#save_mawb_destination_warehouse_id").html(data);
                            $('#save_mawb_destination_warehouse_id').selectpicker("refresh");
                        } else {
                            $("#save_mawb_destination_warehouse_id").html("");
                            $('#save_mawb_destination_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
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
                    <div class="caption"><i class="icon-list"></i>
                        Sorter Bagging List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>Chute Number</th>
                                    <th>Total Shipments</th>
                                    <th>Service Name</th>

                                </tr>
                                <tr role="row" class="filter">
                                    <td width = "6%">

                                    </td>
                                    <td class="user_acccount_correct_button">

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
        <!-- Save Mawb -->
        <div id="save_mawb_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Save Master <span id="mawb_number_span"></span> </h4>
                    </div>
                    <form method="post" action="" enctype="multipart/form-data" id="save_mawb_frm" name="save_mawb_frm">
                        <div class="modal-body" id="osx-modal-data">
                            <div class="row">
                                <div class="row" id="show_general_msg_master" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">Please select pallet carrier group</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Source Country</label>
                                        <div class="input-group source_country_select">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            $sourcecountryId = $this->user->getCountryId();
                                            echo Ddl::generateCountryDDL('save_mawb_source_country_id', $sourcecountryId, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_save_mawb_source_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Source Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group source_warehouse_select">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_mawb_source_warehouse_id" id="save_mawb_source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Destination Country</label>
                                        <div class="input-group destination_country_select">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('save_mawb_destination_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_save_mawb_destination_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Destination Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group  destination_warehouse_select">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_mawb_destination_warehouse_id" id="save_mawb_destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btn_save_pallet" name="btn_save_pallet" value="Save" />
                        </div>
                        <input type="hidden" name="mawb_number_txt" id="mawb_number_txt" />
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Save Mawb -->    
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
