<?php
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    var $warehousetowarehouse;

    public function init() {
        // check admin user is authenticated
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        //
        $this->setTitle("Admin Warehouse To Warehouse Time");
        // get warehouse processing time
        $this->warehousetowarehouse = new WarehouseToWarehouseFilter();

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "deleterecord") {
            $delete_id = $_POST['warehousetowarehouse_id'];
            $Warehouse = new WarehouseToWarehouse($delete_id);
            $Warehouse->delete();
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "cancelrecord") {
            $warehouse = new WarehouseNew();
            $warehouseList = $warehouse->getWarehouseDropDownList();
            echo $warehouseList;
            die;
        }

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {
            $id = $_POST['warehousetowarehouse_id'];
            $this->id = $id;
            $edit_array = array();
            $Warehouse = new WarehouseToWarehouse($id);
            $edit_array['id'] = $id;
            $edit_array['from_warehouse_id'] = $Warehouse->getFromWarehouseId();
            $edit_array['to_warehouse_id'] = $Warehouse->getToWarehouseId();
            $edit_array['transit_time'] = $Warehouse->getTransitTime();
            echo json_encode($edit_array);
            die;
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "saverecord") {
            $sessionUser = SessionManager::getUser();
            $id = $this->form_vars["id"];
            $alreadyexist_array = array();
            $this->warehousetowarehouse = new WarehouseToWarehouseFilter();
            $warehousetowarehouse = new WarehouseToWarehouse();
            $warehouseto = $this->form_vars['warehouseto'];
            $warehousefrom = $this->form_vars['warehousefrom'];

            $this->warehousetowarehouse->addFieldFilter('from_warehouse_id', $warehousefrom);
            $this->warehousetowarehouse->addFieldFilter('to_warehouse_id', $warehouseto);
            $warehousetowarehouse_list = $this->warehousetowarehouse->getList();
            if (empty($warehousetowarehouse_list)) {
                $warehousetowarehouse = new WarehouseToWarehouse();

                if ($id > 0) {
                    $warehousetowarehouse->SetId($this->form_vars["id"]);
                }
                $warehousetowarehouse->SetFromWarehouseId($this->form_vars["warehousefrom"]);
                $warehousetowarehouse->SetToWarehouseId($this->form_vars["warehouseto"]);
                $warehousetowarehouse->SetTransitTime($this->form_vars["transit"]);
                $warehousetowarehouse->save();
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
        if (isset($_GET['action']) && $_GET['action'] == "warehousetowarehouse_ajax") {
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
                $this->warehousetowarehouse->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->warehousetowarehouse = new WarehouseToWarehouseFilter();

                $searchwarehousefrom = $this->form_vars['search_warehouse_from'];
                if (!empty($searchwarehousefrom)) {
                    $this->warehousetowarehouse->addFieldFilter('from_warehouse_id', $searchwarehousefrom);
                }

                $searchwarehouseto = $this->form_vars['search_warehouse_to'];
                if (!empty($searchwarehouseto)) {
                    $this->warehousetowarehouse->addFieldFilter('to_warehouse_id', $searchwarehouseto);
                }
                $searchTransittime = $this->form_vars['search_transit_time'];
                if (!empty($searchTransittime)) {
                    $this->warehousetowarehouse->addFieldLikeFilter('transit_time', $searchTransittime);
                }
            }

            $iTotalRecords = $this->warehousetowarehouse->getPagingCount();

            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $this->warehousetowarehouse->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->warehousetowarehouse->setOffset($iDisplayStart);
            $warehousetowarehouse_list = $this->warehousetowarehouse->getList();

            $warehousetowarehouseDataArr = array();
            foreach ($warehousetowarehouse_list as $warehousetowarehouse) {
                $warehousetowarehouseArr['actionss'] = '';
//                $warehouseArr['option'] = '<input type=checkbox id= "delete55" name="deletewarehouse[]" value="' . $warehouse->getId() . '" />';
                $warehousetowarehouseArr['actionss'] .= "<a data-id =" . $warehousetowarehouse->getId() . " class='btnedit btn-sm blue btn mt-ladda-btn ladda-button btn-outline'><span class='fa fa-pencil'></span> </a>";
                $warehousetowarehouseArr['actionss'] .= "<a href='#' data-id =" . $warehousetowarehouse->getId() . " onclick='return confirm('Are you sure you want to delete?')' class='btn btn-sm btndelete red mt-ladda-btn ladda-button btn-outline'><span class='fa fa-times'></a>";
                $warehousetowarehouseArr['from_warehouse_id'] = WarehouseNew::nameWarehouse($warehousetowarehouse->getFromWarehouseId());
                $warehousetowarehouseArr['to_warehouse_id'] = WarehouseNew::nameWarehouse($warehousetowarehouse->getToWarehouseId());
                $warehousetowarehouseArr['transit_time'] = $warehousetowarehouse->getTransitTime() . " days";
                $warehousetowarehouseDataArr[] = $warehousetowarehouseArr;
            }
            $warehousetowarehouseDataArr['data'] = $warehousetowarehouseDataArr;
            $warehousetowarehouseDataArr['draw'] = $sEcho;
            $warehousetowarehouseDataArr['recordsTotal'] = $iTotalRecords;
            $warehousetowarehouseDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($warehousetowarehouseDataArr);
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
                <div class="caption">
                    <i class="glyphicon glyphicon-search"></i>
                   <? echo Translation::GetCaption("WAREHOUSE_TRANSIT_TIME_DETAILS") ?>
                </div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <form method="post" action="javascript:;" enctype="multipart/form-data" id="warehousetowarehouseForm" name="warehousetowarehouseForm"  role="form">
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
                                    <?php echo Ddl::generateDDL('from_warehouse_id', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2" required', 'Select Warehouse', '', 'from_warehouse_id', 'Warehouse'); ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <?php echo Ddl::generateDDL('to_warehouse_id', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2" required', 'Select Warehouse', '', 'to_warehouse_id', 'Warehouse'); ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red" data-original-title="Transit Time is mandatory">*</i>
                                        <input name="transit_time" id="transit_time" value="" size="50" class="form-control"  maxlength="35" 
                                               title="" placeholder="<? echo Translation::GetCaption("TRANSIT_TIME"); ?> in days" rel="tooltip" 
                                               onkeypress='return numbersonly(event)' data-original-title="<? echo Translation::GetCaption("TRANSIT_TIME"); ?>" type="text" required>
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
                    <? echo Translation::GetCaption("WAREHOUSE_TRANSIT_TIME_LIST") ?>
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
                                <!--<th class="red-back"width="1%"><input type='checkbox' name='checkall'></th>-->
                                <th>Actions</th>
                                <th>Warehouse From</th>
                                <th>Warehouse To</th>
                                <th>Transit Time</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn-sm filter-submit margin-bottom btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                        <button class="btn-sm red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_warehouse_from', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2 form-filter input-sm gtx-select" required', 'Select Warehouse', '', 'search_warehouse_from', 'Warehouse'); ?>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_warehouse_to', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2 form-filter input-sm gtx-select" required', 'Select Warehouse', '', 'search_warehouse_to', 'Warehouse'); ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_transit_time" onkeypress='return numbersonly(event)'>
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

    public function renderFooter() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script> 
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script src="../js/bootstrap-select.min.js"></script>
        <script type="text/javascript">
                                        $(document).ready(function () {
                                            var xhr;
                                            var active=false;
                                            $(document).on('click', '#btn_Cancel', function () {
                                                //                    $(".remove").removeClass("has-error has-danger");
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_to_warehouse.php",
                                                    data: {func: "cancelrecord"}
                                                }).done(function (data) {
                                                    $('#from_warehouse_id').html(" ");
                                                    $('#to_warehouse_id').html(" ");
                                                    $('#transit_time').val(" ");
                                                    $('#from_warehouse_id').html(data);
                                                    $('#to_warehouse_id').html(data);
                                                });
                                                return false;
                                            });
                                            $(document).on('click', '#btn_Save', function () {
                                                $('#warehousetowarehouseForm').validator().on('submit', function (e) {
                                                    if (e.isDefaultPrevented())
                                                    {
                                                        return false;
                                                    } else
                                                    {
                                                        $('#btn_Save').prop('disabled', true);
                                                        var transit_value = $('#transit_time').val();
                                                        var warehousefrom = $('#from_warehouse_id').val();
                                                        var warehouseto = $('#to_warehouse_id').val();
                                                        var id = $('#id').val();
                                                        if(active) {xhr.abort();}
                                                        active=true;
                                                        xhr = $.ajax({
                                                            method: "POST",
                                                            url: "warehouse_to_warehouse.php",
                                                            data: {id: id, transit: transit_value, warehousefrom: warehousefrom, warehouseto: warehouseto, func: "saverecord"}
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
                                                                $('#id').val("");
                                                                $("div").removeClass("hidden");
                                                                $('#failuremsg').hide();
                                                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
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
                                                        }, 'json');
                                                        $('#btn_Save').removeAttr('disabled');
                                                    }
                                                });
                                                $("#warehousetowarehouseForm").submit();
                                            });
                                            $(document).on('click', '.btndelete', function () {
                                                var e = $(this);
                                                var recordid = e.data('id');
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_to_warehouse.php",
                                                    data: {warehousetowarehouse_id: recordid, func: "deleterecord"}
                                                }).done(function (data) {
                                                    e.parents('tr').hide();
                                                    $('#manage-data-table').DataTable().ajax.reload();
                                                });
                                            });
                                            $(document).on('click', '.btnedit', function () {
                                                var e = $(this);
                                                var recordid = e.data('id');
                                                $.ajax({
                                                    method: "POST",
                                                    url: "warehouse_to_warehouse.php",
                                                    data: {warehousetowarehouse_id: recordid, func: "editrecord"}
                                                }).done(function (data) {
                                                    //By using javasript json parse
                                                    var t = JSON.parse(data);
                                                    $('#btn_Save').val("Update");
                                                    $('#transit_time').val(t.transit_time);
                                                    $('#from_warehouse_id').val(t.from_warehouse_id);
                                                    $('#to_warehouse_id').val(t.to_warehouse_id);
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
                                                            "url": "warehouse_to_warehouse.php?action=warehousetowarehouse_ajax", // ajax source
                                                            headers: {
                                                            },
                                                        },
                                                        "bStateSave": true,
                                                        "columns": [
                                                            {"data": "actionss", "bSortable": false},
                                                            {"data": "from_warehouse_id"},
                                                            {"data": "to_warehouse_id"},
                                                            {"data": "transit_time"}
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
