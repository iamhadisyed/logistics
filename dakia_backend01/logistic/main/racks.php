<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country list page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

        include_classes([   
                    'warehouse.class',
                    'warehousefilter.class',
                    'rack.class',
                    'rackfilter.class',
                    'rackshelf.class',
                    'rack.class',
                    'rackfilter.class'
                ]);
// set up local page class
class Page extends BasePage {

    private $warehouses = NULL;
    private $racks = NULL;
    private $warehouse_id = NULL;
    private $title = NULL;

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Manage Rack'
        );

        /* ------------------------------------------------------------------------------ */
        // get RACKS
        //Handle RACKS Ajax
        if (isset($_GET['getRacksAjax']) && $_GET['getRacksAjax'] == 'racks_ajax') {
            $RackObj = new RackFilter();
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if ($dataTableColumnName == "search_title")
                    $dataTableColumnName = "title";

                if ($dataTableColumnName == "search_level")
                    $dataTableColumnName = "rack_rows";

                if ($dataTableColumnName == "search_dimension")
                    $dataTableColumnName = "shelf_dimension";

                if ($dataTableColumnName == "search_max_weight")
                    $dataTableColumnName = "shelf_max_weight";

                if ($dataTableColumnName == "search_york")
                    $dataTableColumnName = "is_york";

                if ($dataTableColumnName == "search_active")
                    $dataTableColumnName = "is_active";

                $RackObj->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchTitle = $this->form_vars['search_Title'];
                if (!empty($searchTitle))
                    $RackObj->addFieldLikeFilter('title', $searchTitle);

                if (isset($this->form_vars['search_York']) && $this->form_vars['search_York'] != "") {
                    $RackObj->addFieldFilter('is_york', $this->form_vars['search_York']);
                }
                $searchWarehouse = $this->form_vars['search_Warehouse'];
                if (!empty($searchWarehouse))
                    $RackObj->addFieldFilter('warehouse_id', $searchWarehouse);

                if (isset($this->form_vars['search_Active']) && $this->form_vars['search_Active'] != "") {
                    $RackObj->addFieldFilter('is_active', $this->form_vars['search_Active']);
                }
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $RackObj->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $RackObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $RackObj->setOffset($iDisplayStart);
            $rackObjs = $RackObj->getPagingList();
            $rackDataArr = array();
            foreach ($rackObjs as $rackObj) {
                $RackObj = new Warehouse($rackObj->getWarehouseId());
                $RSObj = RackShelf::getRackShelfListFromSql("SELECT COUNT(id) AS id FROM rack_shelf WHERE rack_id='" . $rackObj->getId() . "' AND is_filled = 0");
                $rackArr = array();
                $rackArr['actions'] = '';
                $rackArr['actions'] = '<div class="btn-group">
                                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <a title="Edit" href="rack_add.php?rack_id=' . $rackObj->getId() . '" >
                                                            <i class="glyphicon glyphicon-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    
                                                    <li>
                                                    <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $rackObj->getId() . '" 
                                                    data-log_name="rack" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                                                    </a>
                                                    </li>
                                                </ul>
                                            </div>';
                $rackArr['search_title'] = htmlspecialchars($rackObj->getTitle()) . "<br />" . htmlspecialchars($RackObj->getWarehouseName());
                $rackArr['search_level'] = htmlspecialchars($rackObj->getRackRows()) . "X" . htmlspecialchars($rackObj->getRackCols()) . "=" . htmlspecialchars($rackObj->getRackRows()) * htmlspecialchars($rackObj->getRackCols());
                $rackArr['search_available'] = $RSObj[0]->getId();
                $rackArr['search_dimension'] = htmlspecialchars($rackObj->getShelfDimension());
                $rackArr['search_max_weight'] = htmlspecialchars($rackObj->getShelfMaxWeight());
                $rackArr['search_york'] = '<div class="text-center">' . ($rackObj->getIsYork() == 1 ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
                $rackArr['search_active'] = '<div class="text-center">' . ($rackObj->getIsActive() == 1 ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
                $rackDataArr[] = $rackArr;
            }
            $rackDataArrJson['data'] = $rackDataArr;
            $rackDataArrJson['draw'] = $sEcho;
            $rackDataArrJson['recordsTotal'] = $iTotalRecords;
            $rackDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rackDataArrJson);
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
                                "url": "racks.php?getRacksAjax=racks_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "search_title"},
                                {"data": "search_level"},
                                {"data": "search_available", "bSortable": false},
                                {"data": "search_dimension"},
                                {"data": "search_max_weight"},
                                {"data": "search_york"},
                                {"data": "search_active"}
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
        <?php if (!empty($_GET['warehouse_id']) && (int) trim($_GET['warehouse_id']) > 0) { ?>
                    $(".filter-submit").click();
        <?php } ?>
            });
        </script>
        <?php
    }

    public function renderBody() {
        $warehouseId = (isset($_GET['warehouse_id']) && $_GET['warehouse_id'] > 0 ? $_GET['warehouse_id'] : 0);
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>Rack List
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div data-rail-color="blue" data-handle-color="blue">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="6%">Actions</th>
                                    <th>Title</th>
                                    <th>LevelsXShelves=Total</th>
                                    <th>Available</th>
                                    <th>Dimension</th>
                                    <th>Max Weight</th>
                                    <th>York</th>
                                    <th>Active</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i class="fa fa-search"></i> </button>
                                            <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i class="fa fa-times"></i></button>
                                        </div>

                                    </td>
                                    <td style="width:225px;">
                                        <div class="input-group">
                                            <input type="text" id="search_Title" class="form-control form-filter input-xs" name="search_Title">
                                            <span class="input-group-btn select2-bootstrap-append">
        										<?php echo Ddl::generateDDL('search_Warehouse', 'WarehouseFilter', '      is_deleted = 0 ', 'warehouse_name', 'id', $warehouseId, ' class="form-filter bs-select form-control"  data-show-subtext="true" data-toggle="tooltip"  title="Warehouse" data-live-search="true" data-original-title="Warehouse"', 'Select  Warehouse', '', 'search_Warehouse', '', '', '', ''); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td>
										<?php
										$arrayTypeValues = array('0' => 'No', '1' => 'Yes');
										echo Ddl::generateArrayDDL('search_York', $arrayTypeValues, "", "Select York", ' class="form-control form-filter bs-select"', "", 'search_York', 'Select York', '');
										?>
																	</td>
																	<td>
										<?php
										$arrayTypeValues = array('0' => 'No', '1' => 'Yes');
										echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter bs-select"', "", 'search_Active', 'Select Status', '');
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
