<?php
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    var $warehouses;

    public function init() {

//        echo Ddl::generateDDL('search_Name', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2 form-filter input-sm"', 'Select Warehouse', '', 'search_Name', 'Warehouse');
//        die;
        // check admin user is authenticated
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        //
        $this->setTitle("Admin Warehouses");
        // get warehouses
        $this->warehouses = new WarehouseNewFilter();

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "deleterecord") {
            $delete_id = $_POST['warehouse_id'];
            $Warehouse = new WarehouseNew($delete_id);
            $Warehouse->delete();
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "cancelrecord") {
            $country = new Country();
            $countrylist = $country->getCountryDropDown('countryid', 'countryid', '', 'id');
            echo $countrylist;
            die;
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {
            $id = $_POST['warehouse_id'];
            $this->id = $id;
            $edit_array = array();
            $Warehouse = new WarehouseNew($id);
            $edit_array['id'] = $id;
            $edit_array['warehouse_name'] = $Warehouse->getWarehouseName();
            $edit_array['addressline1'] = $Warehouse->getAddressLine1();
            $edit_array['addressline2'] = $Warehouse->getAddressLine2();
            $edit_array['stateregion'] = $Warehouse->getState();
            $edit_array['citytown'] = $Warehouse->getCity();
            $edit_array['postzipcode'] = $Warehouse->getPostCode();
            $edit_array['phone'] = $Warehouse->getPhone();
            $edit_array['description'] = $Warehouse->getDescription();
            $edit_array['is_active'] = $Warehouse->getActive();
            $edit_array['countryid'] = $Warehouse->getCountry();
            $edit_array['hub'] = $Warehouse->getHub();
            $edit_array['email'] = $Warehouse->getEmail();
            echo json_encode($edit_array);
            die;
        }

        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            $Warehouse = new WarehouseNew();
            $id = $_POST["id"];

            if ($id > 0) {
                $Warehouse->SetId($_POST["id"]);
            }
            $Warehouse->SetWarehouseName($_POST["warehouse_name"]);
            $Warehouse->Setaddressline1($_POST["addressline1"]);
            $Warehouse->Setaddressline2($_POST["addressline2"]);
            $Warehouse->SetState($_POST["stateregion"]);
            $Warehouse->SetCity($_POST["citytown"]);
            $Warehouse->SetPostCode($_POST["postzipcode"]);
            $Warehouse->SetCountry($_POST["countryid"]);
            $Warehouse->SetPhone($_POST["phone"]);
            $Warehouse->SetDescription($_POST["description"]);
            $Warehouse->SetActive($_POST["is_active"]);
            $Warehouse->SetDelete($_POST["is_delete"]);
            $Warehouse->setAdded_date(date("Y-m-d H:i:s"));
            $Warehouse->setAddedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $Warehouse->setUpdated_date(date("Y-m-d H:i:s"));
            $Warehouse->setUpdated_by(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $Warehouse->setEmail($_POST["email"]);
            $Warehouse->setHub($_POST["hub"]);
            $Warehouse->save();
        }

        if (isset($_GET['action']) && $_GET['action'] == "warehouse_ajax") {
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
                $this->warehouses->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->warehouses = new WarehouseNewFilter();

                $searchName = $this->form_vars['search_Name'];
                if (!empty($searchName)) {
                    $this->warehouses->addFieldLikeFilter('warehouse_name', $searchName);
                }

                $searchCountry = $this->form_vars['search_Country'];
                if (!empty($searchCountry)) {
                    $this->warehouses->addFieldLikeFilter('countryid', $searchCountry);
                }
                $searchAccount = $this->form_vars['search_Hub'];
                if (!empty($searchAccount)) {
                    $this->warehouses->addFieldLikeFilter('hub', $searchAccount);
                }

                $searchAccount = $this->form_vars['search_Active'];
                if (!empty($searchAccount)) {
                    $this->warehouses->addFieldLikeFilter('is_active', $searchAccount);
                }
            }
            $iTotalRecords = $this->warehouses->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);

            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->warehouses->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->warehouses->setOffset($iDisplayStart);
            $warehouses_list = $this->warehouses->getPagingList();
            $warehouseDataArr = array();
            foreach ($warehouses_list as $warehouse) {
                $warehouseArr['actionss'] = '';
                $warehouseArr['actionss'] .= "<a data-id =" . $warehouse->getId() . " class='btnedit btn-sm blue btn mt-ladda-btn ladda-button btn-outline'><span class='fa fa-pencil'></span> </a>";
                $warehouseArr['actionss'] .= "<a href='#' data-id =" . $warehouse->getId() . " onclick='return confirm('Are you sure you want to delete?')' class='btn btn-sm btndelete red mt-ladda-btn ladda-button btn-outline'><span class='fa fa-times'></a>";
                $warehouseArr['warehouse_name'] = htmlspecialchars($warehouse->getWarehouseName());
                $warehouseArr['countryid'] = "<img src='../assets/global/img/flags/" . Country::getIsoFromId($warehouse->getCountry()) . ".png' /> " . Country::nameCountry($warehouse->getCountry());
                $warehouseArr['hub'] = $warehouse->getHub();
                $warehouseArr['is_active'] = ($warehouse->getIsActive() == 1 ? 'Yes' : 'No');
                $warehouseDataArr[] = $warehouseArr;
            }
            $warehouseDataarr['data'] = $warehouseDataArr;
            $warehouseDataarr['draw'] = $sEcho;
            $warehouseDataarr['recordsTotal'] = $iTotalRecords;
            $warehouseDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($warehouseDataarr);
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
                   <? echo Translation::GetCaption("WAREHOUSE_DETAILS") ?>
                </div>
                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="" style="min-height:400px; max-height:500px"  data-rail-color="blue" data-handle-color="blue">
                    <form method="post" action="javascript:;" enctype="multipart/form-data" id="warehouse_data" name="warehouse_data" role="form">
                        <div class="row">
                            <div class="col-md-12 hidden" id="successmsg">
                                <div class="alert alert-success" id="success_msg"> Record has been Added Successfully .</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <i class="fa tooltips font-red" data-original-title="Warehouse Name is mandatory">*</i>
                                            <input name="warehouse_name" id="warehouse_name" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("WAREHOUSE_NAME"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("WAREHOUSE_NAME"); ?>" type="text" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="addressline1" id="addressline1" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("ADDRESS_LINE_1"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("ADDRESS_LINE_1"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="addressline2" id="addressline2" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("ADDRESS_LINE_2"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("ADDRESS_LINE_2"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="stateregion" id="stateregion" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("STATE_REGION"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("STATE_REGION"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="citytown" id="citytown" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("CITY/TOWN"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("CITY/TOWN"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="postzipcode" id="postzipcode" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("POST/ZIP_CODE"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("POST/ZIP_CODE"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <?php // echo Country::getCountryDropDown('countryid','countryid','','id');  ?>
                                        <?php echo Ddl::generateDDL('countryid', 'CountryFilter', '', 'name', 'id', '', 'class="form-control select2"', 'Select Country', '', 'countryid', 'Country'); ?>
                                        <span class="input-group-addon red-18"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="phone" id="phone" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("PHONE"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("PHONE"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
        <!--                                <label><? echo Translation::GetCaption("DESCRIPTION"); ?></label>-->
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i class="fa fa-table "></i> </span>
                                        <textarea class="form-control" type="text" placeholder="Description" rows="7" cols="150" name="description" id="description"><?php echo (isset($description) ? $description : $this->description ); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <i class="fa tooltips font-red" data-original-title="Hub is mandatory">*</i>
                                            <input name="hub" id="hub" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("HUB"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("HUB"); ?>" type="text" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                        <div class="input-icon right">
                                            <input name="email" id="email" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<? echo Translation::GetCaption("EMAIL"); ?>" rel="tooltip" data-original-title="<? echo Translation::GetCaption("EMAIL"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="md-checkbox-inline" align="center">
                                    <div class="md-checkbox">
                                        <input class="md-check" type="checkbox" name="is_active" id="is_active" value="1"<?php echo (@$is_active == 1 || $this->is_active == 1 ? ' checked="checked"' : ''); ?> />
                                        <label for="is_active">
                                            <span></span> 
                                            <span class="check"></span>
                                            <span class="box"></span>Active</label>
                                    </div>
                                </div>
                            </div>                        
                        </div>

                        <div style="clear:both"></div>
                        <br /><br />
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="<? echo Translation::GetCaption("SAVE"); ?>"/>
                                       <input id="btn_Cancel" type="button"  class="btn btn-default" value="<? echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>
                          <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                        <input type="hidden" name="is_delete" id="is_delete" value="0" />
                        <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                    </form>
                </div>
            </div>    
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>
                   <? echo Translation::GetCaption("WAREHOUSE_LIST") ?>
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
                                <th>Name</th>
                                <th>Country</th>
                                <th>Hub</th>
                                <th>Active</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn-sm filter-submit margin-bottom btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                        <button class="btn-sm red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_Name', 'WarehouseNewFilter', '', 'warehouse_name', 'id', '', 'class="form-control select2 form-filter input-sm"', 'Select Warehouse', '', 'search_Name', 'Warehouse'); ?>
                                </td>
                                <td>
                                    <?php echo Ddl::generateDDL('search_Country', 'CountryFilter', '', 'name', 'id', '', 'class="form-control select2 form-filter input-sm"', 'Select Country', '', 'search_Country', 'Country'); ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_Hub">
                                </td>
                                <td>
                                    <select name="search_Active" id="search_Active" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
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
            $(document).ready(function () {
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "warehouse_list.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $('#countryid').html(" ");
                        $('#countryid').html(data);
                        $(this).closest('#warehouse_data').find("input[type=text], textarea").val("");
                    });
                });
                $(document).on('click', '#btn_Save', function () {
                    $('#warehouse_data').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            var id = $('#id').val();
                            $.ajax({
                                method: "POST",
                                url: "warehouse_list.php",
                                data: $('#warehouse_data').serialize()
                            }).done(function (data) {
                                $('#btn_Save').val("Save");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $('#btn_Cancel').click();
                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                                $('#manage-data-table').DataTable().ajax.reload();
                            });
                        }
                    });
                    $("#warehouse_data").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "warehouse_list.php",
                        data: {warehouse_id: recordid, func: "editrecord"}
                    }).done(function (data) {
                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#warehouse_name').val(t.warehouse_name);
                        $('#addressline1').val(t.addressline1);
                        $('#addressline2').val(t.addressline2);
                        $('#stateregion').val(t.stateregion);
                        $('#citytown').val(t.citytown);
                        $('#postzipcode').val(t.postzipcode);
                        $('#phone').val(t.phone);
                        $('#description').val(t.description);
                        $('#is_active').val(t.is_active);
                        $('#countryid').val(t.countryid);
                        $('#hub').val(t.hub);
                        $('#email').val(t.email);
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });
                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "warehouse_list.php",
                        data: {warehouse_id: recordid, func: "deleterecord"}
                    }).done(function (data) {
                        e.parents('tr').hide();
                        $('#manage-data-table').DataTable().ajax.reload();
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
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "warehouse_list.php?action=warehouse_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                //                                {"data": "option", "bSortable": false},
                                {"data": "actionss", "bSortable": false},
                                {"data": "warehouse_name"},
                                {"data": "countryid"},
                                {"data": "hub"},
                                {"data": "is_active"}
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
