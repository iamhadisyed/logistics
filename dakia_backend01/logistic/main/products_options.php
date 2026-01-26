<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'productsoptions.class',
    'productsoptionsfilter.class'
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
            'Products Options'
        );

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "save") {
            $validate = true;
            $name = $this->form_vars["name"];
            if ($name == "") {
                $output['status'] = "error";
                $output['message'] = "Please enter product option name";
                $validate = false;
            }
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
                $productsOption = new ProductsOptions();
                if ($id != "" && $id > 0) {
                    $productsOption = new ProductsOptions($id);
                }
                $productsOption->setOptionName($name);
                $productsOption->setIsActive($isActive);
                $productsOption->setDateAdded($dateAdded);
                $productsOption->setAddedBy($addedBy);
                $productsOption->setDateUpdated($dateUpdate);
                $productsOption->setUpdatedBy($updateBy);
                $productsOption->save();
                $output['status'] = "success";
                $output['message'] = "Product Options saved successfully";
            }
            echo json_encode($output);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "products_options_list") {
            $productsOptionsFilter = new ProductsOptionsFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $name = $this->form_vars['name'];
                if (!empty($name)) {
                    $productsOptionsFilter->whereLike(['option_name' => $name]);
                }
                $isActive = $this->form_vars['is_active'];
                if ($isActive != "" || $isActive === 0) {
                    $productsOptionsFilter->where(['is_active' => $isActive]);
                }
                $searchDateFrom = $this->form_vars['search_date_from'];
                $searchDateTo = $this->form_vars['search_date_to'];
                if (!empty($searchDateFrom) && !empty($searchDateTo)) {
                    $productsOptionsFilter->whereBetween('date_added', $searchDateFrom, $searchDateTo);
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
                    $productsOptionsFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $productsOptionsFilter->orderBy(strtolower("po.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $productsOptionsFilter->setRowsPerPage($iDisplayLength);
            $productsOptionsFilter->setOffset($iDisplayStart);
            $productsOptionsFilterObjs = $productsOptionsFilter->getList("po.*");
            $iTotalRecords = $productsOptionsFilter->getCount();
            $setDataArr = array();
            foreach ($productsOptionsFilterObjs as $productsOptionsFilterObj) {
                $currentArr['name'] = $productsOptionsFilterObj->getOptionName();
                $isActive = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
                if ($productsOptionsFilterObj->getIsActive() == 1) {
                    $isActive = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
                }
                $currentArr['is_active'] = $isActive;
                $action = '<div class="btn-group" data-container="body" >
                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" >';
                $action .= '<li>
                                    <a title="Edit" href="javascript:;" data-id="' . $productsOptionsFilterObj->getId() . '" class="edit">
                                        <span class="glyphicon glyphicon-eye-open"></span> Edit
                                    </a>
                                </li>';
                $action .= '<li>
                                    <a title="Delete" href="javascript:;" data-id="' . $productsOptionsFilterObj->getId() . '" class="delete">
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
            $productsOptionsObj = new ProductsOptions($id);
            $dt = [
                'id' => $productsOptionsObj->getId(),
                'name' => $productsOptionsObj->getOptionName(),
                'is_active' => $productsOptionsObj->getIsActive()
            ];
            $return = [
                'status' => 'success',
                'productsOptions' => $dt
            ];
            echo json_encode($return);
            die;
        }

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "delete") {
            $id = $this->form_vars['id'];
            $productsOptionsFilter = new ProductsOptionsFilter();
            $productsOptionsFilter->where(['id' => $id]);
            $productsOptionsFilter->delete();
            $return = [
                'status' => 'success',
                'message' => 'Product option delete successfully'
            ];
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
                    var datatableurl = "products_options.php?action=products_options_list";
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
                    var form = $('#products_options_form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);
                    formData.append('action', 'save');
                    $.blockUI();
                    $.ajax({
                        url: 'products_options.php',
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
                        url: "products_options.php",
                        data: {action: "edit", id: id},
                        dataType: "json",
                        success: function (data) {
                            $.unblockUI();
                            if (data.status == "success") {
                                var id = data.productsOptions.id;
                                var name = data.productsOptions.name;
                                var is_active = data.productsOptions.is_active;
                                $('#id').val(id);
                                $('#name').val(name);
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
                                url: "attributes.php",
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
                    Product Options
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form action="" method="post" enctype="multipart/form-data" id="products_options_form" name="products_options_form">
                    <input type="hidden" name="id" id="id"/>
                    <div class="row display-none" id="res_message">
                        <div class="col-md-12">
                            <div class="alert alert-success success_msg"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="label-account">Product Option Name</label>
                            <input type="text" class="form-control" name="name" id="name">
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
            </div>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="icon-bar-chart"></i>
                    Product Options List
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
                            <th>Product Option</th>
                            <th>Active</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <button class="btn btn-xs blue filter-submit btn-outline"><i class="fa fa-search"></i>
                                </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline">
                                    <div class="margin-bottom-5"><i class="fa fa-times"></i></div>
                                </button>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="name"/>
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
