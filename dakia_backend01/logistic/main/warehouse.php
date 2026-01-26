<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country list page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'country.class',
                    'countryfilter.class',
                    'warehouse.class',
                    'warehousefilter.class',
                ]);
// set up local page class
class Page extends BasePage {

    var $warehouses;
    var $countries;
    var $countryid;
    var $name;

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }


        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Manage Warehouse'
        );
        //Handle warehouse Ajax
        if (isset($_GET['getWarehouseAjax']) && $_GET['getWarehouseAjax'] == 'warehouse_ajax') {
            $WObj = new WarehouseFilter();
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if ($dataTableColumnName == "search_Name")
                    $dataTableColumnName = "warehouse_name";

                if ($dataTableColumnName == "search_Phone")
                    $dataTableColumnName = "phone";

                if ($dataTableColumnName == "search_Active")
                    $dataTableColumnName = "is_active";

                $WObj->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchName = $this->form_vars['search_Name'];
                if (!empty($searchName))
                    $WObj->addFieldLikeFilter('warehouse_name', $searchName);

                $countryId = $this->form_vars['countryid'];
                if (!empty($countryId))
                    $WObj->addFieldFilter('countryid', $countryId);

                $searchPhone = $this->form_vars['search_Phone'];
                if (!empty($searchPhone))
                    $WObj->addFieldLikeFilter('phone', $searchPhone);

                $searchActive = $this->form_vars['search_Active'];
                if (!empty($searchActive))
                    $WObj->addFieldFilter('is_active', $searchActive);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $WObj->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $WObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $WObj->setOffset($iDisplayStart);
            $warehouseObjs = $WObj->getPagingList();
            $warehouseDataArr = array();
            foreach ($warehouseObjs as $warehouseObj) {
                $warehouseArr = array();
                $warehouseArr['actions'] = '';
                $warehouseArr['actions'] = '<div class="btn-group">
                                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <a title="Edit" href="warehouse_add.php?warehouse_id=' . $warehouseObj->getId() . '" >
                                                            <i class="glyphicon glyphicon-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    
                                                    <li>
                                                        <a href="racks.php?warehouse_id=' . $warehouseObj->getId() . '" title="Racks">
                                                            <i class="fa fa-eye"></i> Racks
                                                        </a>
                                                    </li>
                                                    <li>
                                                    <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $warehouseObj->getId() . '" 
                                                    data-log_name="warehouse" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                                                    </a>
                                                    </li>
                                                </ul>
                                            </div>';
                $warehouseArr['search_Name'] = $warehouseObj->getWarehouseName();
                $warehouseArr['countryid'] = Country::nameCountry($warehouseObj->getCountryId());
                $warehouseArr['search_Phone'] = $warehouseObj->getPhone();
                $warehouseArr['search_Active'] = '<div class="text-center">' . ($warehouseObj->getIsActive() == 1 ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
                $warehouseDataArr[] = $warehouseArr;
            }
            $warehouseDataArrJson['data'] = $warehouseDataArr;
            $warehouseDataArrJson['draw'] = $sEcho;
            $warehouseDataArrJson['recordsTotal'] = $iTotalRecords;
            $warehouseDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($warehouseDataArrJson);
            die;
        }
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderHead() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
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
                                "url": "warehouse.php?getWarehouseAjax=warehouse_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "search_Name"},
                                {"data": "countryid"},
                                {"data": "search_Phone"},
                                {"data": "search_Active"}
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

    public function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>Warehouse List
                </div>
                <div class="actions">
                    <a href="warehouse_add.php" class="btn blue" data-original-title="" title=""><span></span><i class="fa fa-list"></i>&nbsp;Add Warehouse</a>
                </div>
                <div class="tools">
                    <!--<a href="javascript:;" class="collapse"></a>-->
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger display-none"  id="res_message" ></div>
                    </div>
                </div>
                <div class=""  data-rail-color="blue" data-handle-color="blue">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="7%">Actions</th>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Phone</th>
                                    <th>Active</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i class="fa fa-search"></i> </button>
                                            <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i class="fa fa-times"></i></button>
                                        </div>

                                    </td>
                                    <td  class="user_acccount_correct_button"><input type="text" id="search_Name" class="form-control form-filter input-sm" name="search_Name"></td>
                                    <td  class="user_acccount_correct_button"> 
                                        <?php
                                        echo Ddl::generateCountryDDL('countryid', $countryid, 'id', ' class="form-filter bs-select form-control  input-sm" data-live-search="true" data-size="8" data-container="body"');
                                        ?>
                                    </td>
                                    <td  class="user_acccount_correct_button"><input type="text" id="search_Phone" class="form-control form-filter input-sm" name="search_Phone"></td>
                                    <td class="user_acccount_correct_button">
                                        <?php
                                        $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
                                        echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2  input-sm"', "", 'search_Active', 'Select Status', '');
                                        ?>
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
