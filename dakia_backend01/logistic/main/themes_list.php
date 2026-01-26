<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'PHPExcel'
], '3rdparty/phpexcel');

require_once("../includes/library/vendor/autoload.php");

include_classes([

]);

class Page extends BasePage {
    /* * *
     * Controller logic
     */

    private $filerColumn = '*';
    private $params = "";
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Themes List"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "themes_ajax") {
            $themesFilter = new ThemesFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $whereLike = [];
                $themeName = $this->form_vars['name'];
                if (!empty($themeName))
                    $whereLike['t.name'] = $themeName;

                $slug = $this->form_vars['slug'];
                if (!empty($slug))
                    $whereLike['t.slug'] = $slug;

                $dashboardTemplate = $this->form_vars['dashboard_template'];
                if (!empty($dashboardTemplate))
                    $whereLike['t.dashboard_template'] = $dashboardTemplate;

                $themesFilter->whereLike($whereLike);

                $isActive = $this->form_vars['is_active'];
                if ($isActive == '1' || $isActive == '0') {
                    $themesFilter->where(['t.is_active' => $isActive]);
                }
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                $themesFilter->orderBy(strtolower("t." . $dataTableColumnName), $orderBy);
            } else {
                $themesFilter->orderBy(strtolower("t.id"),'desc');
            }
            /*
             * Pagination Logic Implemented
             *
             */

            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $themesFilter->setRowsPerPage($iDisplayLength);
            $themesFilter->setOffset($iDisplayStart);
            $themeFilterObj = $themesFilter->getList("t.*");
            $iTotalRecords = $themesFilter->getCount(true);
            $setDataArr = array();
            foreach ($themeFilterObj as $themeObj) {
                // set status of theme
                if ($themeObj->getIsActive() == 1) {
                    $statusOfTheme = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
                } else {
                    $statusOfTheme = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
                }
                $currentArr = array();
                $currentArr['name'] = $themeObj->getName();
                $currentArr['slug'] = $themeObj->getSlug();
                $currentArr['dashboard_template'] = $themeObj->getDashboardTemplate();
                $currentArr['is_active'] = $statusOfTheme;
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                    $currentArr['actions'] .= '<li>
                                                    <a title="Edit" href="themes_add.php?id=' . $themeObj->getId() . '">
                                                        <span class="glyphicon glyphicon-pencil"></span> Edit
                                                    </a>
                                                </li>';
                $currentArr['actions'] .= '</ul>
                                        </div>' ;
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $tariffId = $this->form_vars['tariff_id'];
            if ($tariffId > 0) {
                $remoteareas = new Tariffs($tariffId);
                $remoteareas->delete();
                $returnMsg['STATUS'] = "success";
                /*
                 * Add Remoteareas Log details
                 */
//                $remoteareasGroupsLog = new TariffsLog();
//                $newTariffsData = serialize($remoteareas);
//                $remoteareasGroupsLog->createlog($this->user->getId(),'',$tariffId,'REMOTEAREAS_GROUPS',$this->user->getUserName() . ' has deleted ' . $tariffId,$oldTariffsData, $newTariffsData);
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }

    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "themes_list.php?action=themes_ajax";
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
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "name"},
                                {"data": "slug"},
                                {"data": "dashboard_template"},
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
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            $(document).on('click', '.btndelete', function () {
                var tariffId = $(this).attr("data-tariff_id");
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
                            $.ajax({
                                type: "POST",
                                url: "tariffs_list.php",
                                data: {action: "delete", tariff_id: tariffId},
                                dataType: "json",
                                success: function (data) {
                                    if (data.STATUS == "success") {
                                        swal("Success!", "<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>", "success");
                                        $(".scroll-to-top").click();
                                        grid.getDataTable().ajax.reload();
                                    } else {
                                        swal("Sorry!", "something went wrong", "error");
                                    }
                                },
                                error: function () {
                                    swal("Sorry!", "something went wrong", "error");
                                }
                            });
                        }
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Themes List
                </div>
                <div class="actions">
                    <a href="themes_add.php" class="btn blue"  ><i class="fa fa-plus"></i> Add Theme</a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Actions</th>
                        <th>Theme Name</th>
                        <th>Slug</th>
                        <th>Dashboard Template</th>
                        <th>Is Active</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                            </div>

                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-sm " name="name" id ="name" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-sm " name="slug" id ="slug" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-sm " name="dashboard_template" id ="dashboard_template" />
                        </td>
                        <td>
                            <?php
                            $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
                            echo Ddl::generateArrayDDL('is_active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2"', "", 'is_active', 'Select Status', '');
                            ?>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>