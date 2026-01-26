<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'categories.class',
    'categoriesfilter.class'
]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    public $error = array();
    public $message;
    private $user = null;
    private $categories = [];

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Category'
        );

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "save") {
            $validate = true;
            $name = $this->form_vars["name"];
            if ($name == "") {
                $output['status'] = "error";
                $output['message'] = "Please enter category";
                $validate = false;
            }
            $parentId = $this->form_vars["parent_id"];
            $isActive = 0;
            if (isset($this->form_vars['is_active']) && ($this->form_vars['is_active'] == 'on')) {
                $isActive = 1;
            }
            $dateAdded = time();
            $addedBy = $this->user->getId();
            $dateUpdate = time();
            $updateBy = $this->user->getId();
            $id = $this->form_vars['id'];
            if ($validate) {
                $category = new Categories();
                if ($id != "" && $id > 0) {
                    $category = new Categories($id);
                }
                $category->setName($name);
                $category->setParentId($parentId);
                $category->setIsActive($isActive);
                $category->setDateAdded($dateAdded);
                $category->setAddedBy($addedBy);
                $category->setDateUpdated($dateUpdate);
                $category->setUpdatedBy($updateBy);
                $category->save();
                $output['status'] = "success";
                $output['message'] = "Category saved successfully";
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "upload_csv") {
            $csvfile = '../_assets/csv/uk.eu_browse_tree_mappings._TTH_.csv';
            if(!file_exists($csvfile)) {
                echo "File not found. Make sure you specified the correct path.\n";
                exit;
            }
            $file = fopen($csvfile,"r");
            if(!$file) {
                echo "Error opening data file.\n";
                exit;
            }
            $size = filesize($csvfile);
            if(!$size) {
                echo "File is empty.\n";
                exit;
            }

            //$csvcontent = fread($file,$size);
            $liness = 0;
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                $liness++;
                $parent_id = 0;
                if ($liness == 1)
                    continue;
                $dateAdded = time();
                $addedBy = $this->user->getId();
                $dateUpdate = time();
                $updateBy = $this->user->getId();

                $node_id = $column[1];
                $node_id = trim($node_id," \t");
                $node_id = str_replace("\r","",$node_id);
                $node_id = str_replace("'","\'",$node_id);

                $cats_tree = $column[2];
                $cats_tree = trim($cats_tree," \t");
                $cats_tree = str_replace("\r","",$cats_tree);
                $cats_tree = str_replace("'","\'",$cats_tree);
                $cats_tree = explode("/",$cats_tree);
                foreach($cats_tree as $cat){
                    $cat = trim($cat," \t");
                    $cat = str_replace("\r","",$cat);
                    $cat = str_replace("'","\'",$cat);
                    $cat = strtolower($cat);
                    $CategoriesFilterObj = new CategoriesFilter();
                    $CategoriesFilterObj->where(['name' => $cat]);
                    $catDataObj = $CategoriesFilterObj->getList("id, name, parent_id");
                    if(count($catDataObj)) {
                        foreach($catDataObj as $catObj) {
                            $parent_id = $catObj->getId();
                        }
                        continue;
                    }

                    $category = new Categories();
                    $category->setName($cat);
                    $category->setParentId($parent_id);
                    $category->setNodeId($node_id);
                    $category->setMarketPlaceId(1);
                    $category->setIsActive(1);
                    $category->setDateAdded($dateAdded);
                    $category->setAddedBy($addedBy);
                    $category->setDateUpdated($dateUpdate);
                    $category->setUpdatedBy($updateBy);
                    $category->save();
                    $output['status'] = "success";
                    $output['message'] = "Categories saved successfully";
                }
            }

            echo json_encode($output);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "category_list") {
            $categoriesFilter = new CategoriesFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $name = $this->form_vars['name'];
                if (!empty($name)) {
                    $categoriesFilter->whereLike(['name' => $name]);
                }
                $parentId = $this->form_vars['parent_id'];
                if (!empty($parentId)) {
                    $categoriesFilter->where(['parent_id' => $parentId]);
                }
                $isActive = $this->form_vars['is_active'];
                if (!empty($isActive) || $isActive == 0) {
                    $categoriesFilter->where(['is_active' => $isActive]);
                }
                $searchDateFrom = $this->form_vars['search_date_from'];
                $searchDateTo = $this->form_vars['search_date_to'];
                if (!empty($searchDateFrom) && !empty($searchDateTo)) {
                    $categoriesFilter->whereBetween('date_created', $searchDateFrom, $searchDateTo);
                }
            }
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if ($dataTableColumnName != "action") {
                    $categoriesFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $categoriesFilter->orderBy(strtolower("c.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $categoriesFilter->setRowsPerPage($iDisplayLength);
            $categoriesFilter->setOffset($iDisplayStart);
            $categoriesFilterObjs = $categoriesFilter->getList("c.*");
            $iTotalRecords = $categoriesFilter->getCount();
            $setDataArr = array();
            foreach ($categoriesFilterObjs as $categoriesFilterObj) {
                $parentCategory = new Categories($categoriesFilterObj->getParentId());
                $parentCategoryName = "";
                if (!empty($parentCategory)) {
                    $parentCategoryName = $parentCategory->getName();
                }
                $currentArr['name'] = $categoriesFilterObj->getName();
                $currentArr['parent_id'] = $parentCategoryName;
                $isActive = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
                if ($categoriesFilterObj->getIsActive() == 1) {
                    $isActive = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
                }
                $currentArr['is_active'] = $isActive;
                $action = '<div class="btn-group" data-container="body" >
                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" >';
                $action .= '<li>
                                        <a title="Edit" href="javascript:;" data-id="' . $categoriesFilterObj->getId() . '" class="edit">
                                            <span class="glyphicon glyphicon-eye-open"></span> Edit
                                        </a>
                                    </li>';
                $action .= '<li>
                                        <a title="Delete" href="javascript:;" data-id="' . $categoriesFilterObj->getId() . '" class="delete">
                                            <span class="glyphicon glyphicon-trash"></span> Delete
                                        </a>
                                    </li>';
                $action .= '</ul>';
                $action .= '</div>';
                $currentArr['actions'] = $action;
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "edit") {
            $id = $this->form_vars['id'];
            $categoryObj = new Categories($id);
            $dt = [
                'id' => $categoryObj->getId(),
                'name' => $categoryObj->getName(),
                'parent_id' => $categoryObj->getParentId(),
                'is_active' => $categoryObj->getIsActive()
            ];
            $return = [
                'status' => 'success',
                'category' => $dt
            ];
            echo json_encode($return);
            die;
        }

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "delete") {
            $id = $this->form_vars['id'];
            $categoryFilterCheck = new CategoriesFilter();
            $categoryFilterCheck->where(['parent_id' => $id]);
            $count = $categoryFilterCheck->getCount(false);
            $return = [
                'status' => 'error',
                'message' => 'Please delete first child category'
            ];
            if ($count == 0) {
                $categoryFilter = new CategoriesFilter();
                $categoryFilter->where(['id' => $id]);
                $categoryFilter->delete();
                $return = [
                    'status' => 'success',
                    'message' => 'Category delete successfully'
                ];
            }
            echo json_encode($return);
            die;
        }
    }

    protected function renderHead()
    {
        ?>

        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "categories.php?action=category_list";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            $(".table-container .custom-alerts").hide();
                            if (response.recordsTotal > 0) {
                                $("#bluk_actions").show();
                                $(".button-download-records").show();
                            } else {
                                $("#bluk_actions").hide();
                                $(".button-download-records").hide();
                            }
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
                                headers: {}
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "name", "bSortable": false},
                                {"data": "parent_id", "bSortable": false},
                                {"data": "is_active", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {
                            }
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
            $(document).ready(function (e) {
                $(document).on('click', '#btnSave', function () {
                    var form = $('#category_form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);
                    formData.append('action', 'save');
                    $.blockUI();
                    $.ajax({
                        url: 'categories.php',
                        data: formData,
                        type: 'POST',
                        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                        processData: false, // NEEDED, DON'T OMIT THIS
                        dataType: "json",
                        success: function (response) {
                            if (response.status == "success") {
                                $('#id').val('');
                                $('#name').val('');
                                $('#parent_id').val('').trigger('change');
                                $('#is_active').attr('checked', true);
                                $('#is_active').bootstrapSwitch('state', true);
                                swal("Success!", response.message, "success");
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                            $.unblockUI();
                            grid.getDataTable().ajax.reload();
                        },
                        error: function () {
                            $.unblockUI();
                        }
                    });
                });
                DataTableFun.init();

                $(document).on('click', '.edit', function () {
                    var id = $(this).data('id');
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "categories.php",
                        data: {action: "edit", id: id},
                        dataType: "json",
                        success: function (data) {
                            $.unblockUI();
                            if (data.status == "success") {
                                var id = data.category.id;
                                var name = data.category.name;
                                var parent_id = data.category.parent_id;
                                var is_active = data.category.is_active;
                                $('#id').val(id);
                                $('#name').val(name);
                                $('#parent_id').val(parent_id).trigger('change');
                                if (is_active == 1) {
                                    $('#is_active').attr('checked', true);
                                    $('#is_active').bootstrapSwitch('state', true);
                                } else {
                                    $('#is_active').attr('checked', false);
                                    $('#is_active').bootstrapSwitch('state', false);
                                }
                                $("html, body").animate({scrollTop: 0}, "slow");
                            }
                        },
                        error: function () {
                            $.unblockUI();
                            alert('error handing here');
                        }
                    });
                });

                $(document).on('click', '.delete', function () {
                    var id = $(this).data('id');
                    swal({
                        title: "Are You Sure?",
                        text: "you want to delete category!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $.blockUI();
                            $.ajax({
                                type: "POST",
                                url: "categories.php",
                                data: {action: "delete", id: id},
                                dataType: "json",
                                success: function (data) {
                                    $.unblockUI();
                                    if (data.status == "success") {
                                        swal("Success!", data.message, "success");
                                        grid.getDataTable().ajax.reload();
                                    } else {
                                        swal("Sorry!", data.message, "error");
                                    }
                                },
                                error: function () {
                                    $.unblockUI();
                                    alert('error handing here');
                                }
                            });
                        }
                    });
                });

            });
            function importCsv() {
                var form_data = new FormData();
                var file_data = $("#csvfile").prop('files')[0];
                form_data.append('csv_file', file_data);
                form_data.append("action", "upload_csv");
                    $.ajax({
                        url: 'categories.php',
                        type: 'post',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                            return;
                            if (response.status == "success") {
                                swal({
                                    type: 'success',
                                    html: true,
                                    title: "Success!",
                                    text: response.message
                                });
                            } else {
                                swal("Sorry!", "Some thing went wrong please contact to support", "error");
                            }
                        }
                    });

            }
        </script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-users"></i>
                    Category
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form action="" method="post" enctype="multipart/form-data" id="category_form" name="category_form">
                    <input type="hidden" name="id" id="id"/>
                    <div class="row display-none" id="res_message">
                        <div class="col-md-12">
                            <div class="alert alert-success success_msg"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="label-account">Category</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Parent Category</label>
                            <div class="form-group">
                                <?php
                                    $categoryParentId = '';
                                    $selectedCategory = '';
                                    $includeParent = true;
                                    $allowedLevel = 0;
                                    echo Ddl::showTreeDropdown('parent_id', 'categories', 'name', 'id', $categoryParentId, array("is_active = '1'"), $selectedCategory, "Please Select Parent", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", '', '', 'owe_16_', $includeParent, $allowedLevel, 'parent_id');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Active</label><br/>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php $chkActive = 1; ?>
                                        <input <?php echo($chkActive == '1' ? 'checked="checked"' : ''); ?> name="is_active" id="is_active" type="checkbox" class="make-switch" data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn_save" name="btnSave" id="btnSave" type="button">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Import CSV</button>
            </div>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="icon-bar-chart"></i>
                    Categories List
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper"></div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Category</th>
                                <th>Parient Category</th>
                                <th>Active</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-xs blue filter-submit btn-outline"><i class="fa fa-search"></i>
                                    </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline">
                                        <div class="margin-bottom-5"><i class="fa fa-times"></i>
                                    </button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="name"/>
                                </td>
                                <td>
                                    <div>
                                        <?php
                                            $categoryParentId = '';
                                            $selectedCategory = '';
                                            $includeParent = true;
                                            $allowedLevel = 0;
                                            echo Ddl::showTreeDropdown('parent_id', 'categories', 'name', 'id', $categoryParentId, array("is_active = '1'"), $selectedCategory, "Please Select Parent", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", '', '', 'owe_16_', $includeParent, $allowedLevel, 'parent_id');
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
                                    echo Ddl::generateArrayDDL('is_active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2"', "", 'is_active_search', 'Select Status', '');
                                    ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Header</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form class="form-horizontal" action="categories.php" onSubmit="importCsv(); return false;">
                                    <div class="form-group">
                                        <input type="hidden" name="action" id="action" value="upload_csv">
                                        <label class="col-md-4 control-label" for="filebutton">Select File</label>
                                        <div class="col-md-4">
                                            <input type="file" name="csvfile" id="csvfile" class="input-large">
                                        </div>
                                    </div>
                                    <!-- Button -->
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="singlebutton">Import data</label>
                                        <div class="col-md-4">
                                            <input type="submit" id="submit" name="Import" value="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">
                                        </div>
                                    </div>
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
