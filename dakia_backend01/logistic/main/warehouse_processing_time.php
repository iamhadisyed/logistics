<?php
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $warehouseprocessingtime;
    private $id = NULL;
    private $ParcelProcessingTime = NULL;
    private $service;
    private $serviceid = NULL;
    private $warehouse;
    private $warehouseid = NULL;

    public function init() {
        // check admin user is authenticated
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        //
        $this->setTitle("Admin Warehouse Processing Time");
        // get warehouse processing time
        $this->warehouseprocessingtime = new WarehouseProcessingTimeFilter();
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "deleterecord") {
            $delete_id = $_POST['warehouseprocessingtime_id'];
            $Warehouse = new WarehouseProcessingTime($delete_id);
            $Warehouse->delete();
        }

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "cancelrecord") {

            $cancel_array = array();
            $Warehouse = new WarehouseNew();
            $service = new Services();
            $cancel_array['warehouseList'] = $Warehouse->getWarehouseDropDownList();
            $cancel_array['serviceList'] = $service->getServiceDropDownList();
            echo json_encode($cancel_array);
            die;
        }

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $id = $_POST['recordid'];
            $this->id = $id;
            $edit_array = array();
            $WarehouseProcessingTime = new WarehouseProcessingTime($id);
            $edit_array['id'] = $id;
            $edit_array['warehouseid'] = $WarehouseProcessingTime->getWarehouseId();
            $edit_array['serviceid'] = $WarehouseProcessingTime->getServiceId();
            $edit_array['ParcelProcessingTime'] = $WarehouseProcessingTime->getParcelProcessingTime();
            echo json_encode($edit_array);
            die;
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "saverecord") {
            $id = $this->form_vars["id"];
            $alreadyexist_array = array();
            $this->warehouseprocessingtime = new WarehouseProcessingTimeFilter();
            $warehouse = $this->form_vars["warehouseid"];
            $service = $this->form_vars["serviceid"];
            $this->warehouseprocessingtime->addFieldFilter('warehouse_id', $warehouse);
            $this->warehouseprocessingtime->addFieldFilter('service_id', $service);
            $warehouseprocessingtime_list = $this->warehouseprocessingtime->getList();
            if (empty($warehouseprocessingtime_list)) {
                $WarehouseProcessingTime = new WarehouseProcessingTime();
                if ($id > 0) {
                    $WarehouseProcessingTime->SetId($this->form_vars["id"]);
                }
                $WarehouseProcessingTime->SetWarehouseId($this->form_vars["warehouseid"]);
                $WarehouseProcessingTime->setServiceId($this->form_vars["serviceid"]);
                $WarehouseProcessingTime->SetParcelProcessingTime($this->form_vars["ParcelProcessingTime"]);
                $WarehouseProcessingTime->save();
                $alreadyexist_array['message'] = "Record Addess Successfully";
                $alreadyexist_array['success'] = "1";
                echo json_encode($alreadyexist_array);
                die;
            } else {
                $alreadyexist_array['message'] = "Record Already Exist";
                $alreadyexist_array['success'] = "0";
                echo json_encode($alreadyexist_array);
                die;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == "warehouseprocessingtime_ajax") {
            // Get current user
            $sessionUser = SessionManager::getUser();
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
                $this->warehouseprocessingtime->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->warehouseprocessingtime = new WarehouseProcessingTimeFilter();

                $searchWarehouse = $this->form_vars['search_warehouse'];
                if (!empty($searchWarehouse)) {
                    $this->warehouseprocessingtime->addFieldFilter('warehouse_id', $searchWarehouse);
                }

                $searchService = $this->form_vars['search_Service'];
                if (!empty($searchService)) {
                    $this->warehouseprocessingtime->addFieldFilter('service_id', $searchService);
                }
                $searchTime = $this->form_vars['search_parcel_processing_time'];
                if (!empty($searchTime)) {
                    $this->warehouseprocessingtime->addFieldLikeFilter('parcel_processing_time', $searchTime);
                }
            }
            $iTotalRecords = $this->warehouseprocessingtime->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);

            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->warehouseprocessingtime->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->warehouseprocessingtime->setOffset($iDisplayStart);
            $warehouseprocessingtime_list = $this->warehouseprocessingtime->getList();
            $warehouseprocessingtimeDataArr = array();
            foreach ($warehouseprocessingtime_list as $warehouseprocessingtime) {
                $warehouseprocessingtimeArr['actionss'] = '';
                $warehouseprocessingtimeArr['actionss'] .= "<a data-id =" . $warehouseprocessingtime->getId() . " class='btnedit btn-sm blue btn mt-ladda-btn ladda-button btn-outline'><span class='fa fa-pencil'></span> </a>";
                $warehouseprocessingtimeArr['actionss'] .= "<a href='#' data-id =" . $warehouseprocessingtime->getId() . " onclick='return confirm('Are you sure you want to delete?')' class='btn btn-sm btndelete red mt-ladda-btn ladda-button btn-outline'><span class='fa fa-times'></a>";
                $warehouseprocessingtimeArr['warehouse_id'] = WarehouseNew::nameWarehouse($warehouseprocessingtime->getWarehouseId());
                $warehouseprocessingtimeArr['service_id'] = Services::nameService($warehouseprocessingtime->getServiceId());
                $warehouseprocessingtimeArr['parcel_processing_time'] = $warehouseprocessingtime->getParcelProcessingTime() . " days";
                $warehouseprocessingtimeDataArr[] = $warehouseprocessingtimeArr;
            }
            $warehouseprocessingtimeDataarr['data'] = $warehouseprocessingtimeDataArr;
            $warehouseprocessingtimeDataarr['draw'] = $sEcho;
            $warehouseprocessingtimeDataarr['recordsTotal'] = $iTotalRecords;
            $warehouseprocessingtimeDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($warehouseprocessingtimeDataarr);
            die;
        }
    }

    public function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <?php
    }

    public function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>
                    <? echo Translation::GetCaption("WAREHOUSE_PROCESSING_TIME_DETAILS") ?>
                </div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <form method="post" action="javascript:;" enctype="multipart/form-data" id="warehouseprocessingtimeForm" name="warehouseprocessingtimeForm"  role="form">
                    <div class="row">
                        <div class="col-md-12 hidden" id="successmsg">
                            <div class="alert alert-success" id="success_msg"> Record has been Added Successfully .</div>
                        </div>
                        <div class="col-md-12 hidden" id="failuremsg">
                            <div class="alert alert-danger" id="failure_msg"> Record Already Exist .</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <?php echo Ddl::generateDDL('warehouseid', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2" required', 'Select Warehouse', '', 'warehouseid', 'Warehouse'); ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <?php echo Ddl::generateDDL('serviceid', 'ServiceFilter', '', 'name', 'id', '', 'class="form-control select2" required', 'Select Service', '', 'serviceid', 'Service'); ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red" data-original-title="Parcel Processing Time is mandatory">*</i>
                                        <input name="ParcelProcessingTime" id="ParcelProcessingTime" value="" size="50" class="form-control"  maxlength="35" 
                                               title="" placeholder="<? echo Translation::GetCaption("PARCEL_PROCESSING_TIME"); ?> in days"
                                               rel="tooltip" onkeypress='return numbersonly(event)' data-original-title="<? echo Translation::GetCaption("PARCEL_PROCESSING_TIME"); ?>" type="text" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="row " style="text-align:centre;" align="center">
                        <div class="col-md-12">
                            <input id="btn_Save" type="button"  class="btn btn-primary" value="<? echo Translation::GetCaption("SAVE"); ?>"/>
                                   <input id="btn_Cancel" type="button"  class="btn btn-default" value="<? echo Translation::GetCaption("CANCEL"); ?>"/>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                    <input type="hidden" name="form_action" id="form_action" value="" />
                </form>
            </div>
        </div>    

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>
                    <? echo Translation::GetCaption("WAREHOUSE_PROCESSING_TIME_LIST") ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Actions</th>
                                <th>Warehouse</th>
                                <th>Service</th>
                                <th>Parcel Processing Time</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn-sm filter-submit margin-bottom btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                        <button class="btn-sm red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_warehouse', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2 form-filter input-sm"', 'Select Warehouse', '', 'search_warehouse', 'Warehouse'); ?>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_Service', 'ServiceFilter', '', 'name', 'id', '', 'class="form-control select2 form-filter input-sm"', 'Select Service', '', 'search_Service', 'Service'); ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_parcel_processing_time" onkeypress='return numbersonly(event)'>
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

    public function renderfooter() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript">
                                        $(document).ready(function() {
                                            var xhr;
                                            var active=false;
                                            $('#btn_Cancel').click(function () {
                                                //                    $(".remove").removeClass("has-error has-danger");
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_processing_time.php",
                                                    data: {func: "cancelrecord"}
                                                }).done(function (data) {
                                                    //By using javasript json parse
                                                    var t = JSON.parse(data);
                                                    $('#warehouseid').html(" ");
                                                    $('#serviceid').html(" ");
                                                    $('#ParcelProcessingTime').val(" ");
                                                    $('#warehouseid').html(t.warehouseList);
                                                    $('#serviceid').html(t.serviceList);
                                                });
                                            });
                                            $('.btndelete').click(function () {
                                                var e = $(this);
                                                var recordid = e.data('id');
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_processing_time.php",
                                                    data: {warehouseprocessingtime_id: recordid, func: "deleterecord"}
                                                }).done(function (data) {
                                                    e.parents('tr').hide();
                                                    $('#manage-data-table').DataTable().ajax.reload();
                                                });
                                            });
                                            $('#btn_Save').on('click',function(){
//                                            $('#btn_Save#btn_Save').click(function () {
                                               $('#warehouseprocessingtimeForm').validator().on('submit', function (e) {
                                                    if (e.isDefaultPrevented())
                                                    {
                                                        return false;
                                                    } else {
//                                                        $('#btn_Save').prop('disabled', true);
                                                        $("#btn_Save").attr("disabled","disabled");
                                                        if(active) {xhr.abort();}
                                                        active=true;
                                                        var warehouseid = $('#warehouseid').val();
                                                        var serviceid = $('#serviceid').val();
                                                        var ParcelProcessingTime = $('#ParcelProcessingTime').val();
                                                        var id = $('#id').val();
                                                        xhr = $.ajax({
                                                            method: "POST",
                                                            url: "warehouse_processing_time.php",
                                                            data: {id: id, ParcelProcessingTime: ParcelProcessingTime, serviceid: serviceid, warehouseid: warehouseid, func: "saverecord"}
                                                        }).done(function (data) {
                                                            var t = $.parseJSON(data);
                                                            if (t.success == '1') {
                                                                $('#btn_Save').val("Save");
                                                                $('#success_msg').html(" ");
                                                                if (id == "") {
                                                                    $('#success_msg').html("Record has been Added Successfully");
                                                                } else {
                                                                    $('#success_msg').html("Record has been Updated Successfully");
                                                                }
                                                                $('#btn_Cancel').click();
                                                                $("div").removeClass("hidden");
                                                                $('#failuremsg').hide();
                                                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                                                $('#id').val("");
                                                                $('#manage-data-table').DataTable().ajax.reload();
                                                                active=false;
                                                            } else {
                                                                $('#failure_msg').html("");
                                                                $('#failure_msg').html(t.message);
                                                                $("div").removeClass("hidden");
                                                                $('#successmsg').hide();
                                                                $('#failuremsg').show();
                                                                active=false;
                                                            }
                                                        },'json');
                                                        $('#btn_Save').removeAttr('disabled');
                                                    }
                                                });
                                                $("#warehouseprocessingtimeForm").submit();
                                            });
                                            $('.btnedit').click(function () {
                                                var e = $(this);
                                                var recordid = e.data('id');
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_processing_time.php",
                                                    data: {recordid: recordid, func: "editrecord"}
                                                }).done(function (data) {
                                                    //By using javasript json parse
                                                    var t = JSON.parse(data);
                                                    $('#btn_Save').val("Update");
                                                    $('#ParcelProcessingTime').val(t.ParcelProcessingTime);
                                                    $('#serviceid').val(t.serviceid);
                                                    $('#warehouseid').val(t.warehouseid);
                                                    $('#id').val(t.id);
                                                    $('html, body').animate({scrollTop: '0px'}, 300);
                                                });
                                            });
                                        });
                                      
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
                                                            [10, 20, 50, 100],
                                                            [10, 20, 50, 100] // change per page values here 
                                                        ],
                                                        "pageLength": 10, // default record count per page
                                                        "ajax": {
                                                            "url": "warehouse_processing_time.php?action=warehouseprocessingtime_ajax", // ajax source
                                                            headers: {
                                                            },
                                                        },
                                                        "bStateSave": true,
                                                        "columns": [
                                                            //                                {"data": "option", "bSortable": false},
                                                            {"data": "actionss", "bSortable": false},
                                                            {"data": "warehouse_id"},
                                                            {"data": "service_id"},
                                                            {"data": "parcel_processing_time"}
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
                                        });
                                        function numbersonly(e)
                                        {
                                            var unicode = e.charCode ? e.charCode : e.keyCode
                                            if (unicode != 8)
                                            {
                                                if (unicode == 46)
                                                {
                                                } else if (unicode < 48 || unicode > 57) //if not a number
                                                    return false //disable key press
                                            }
                                        }
        </script>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COUNTRIES);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
