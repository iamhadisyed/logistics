<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'iaddress.class',
    'carrier.class',
    'carrierfilter.class',
    'consignment.class',
    'consignmentfilter.class',
    'services.class',
    'servicefilter.class',
    'department.class',
    'departmentfilter.class',
    'country.class',
    'countryfilter.class'
]);


class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $table_msg;
    private $department_data;

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $this->sessionUser = $sessionUser = $user = SessionManager::getUser();
        if ($user->getUserType() != "admin") {
            util_redirect("index.php");
        }
        if (isset($_GET['action']) && $_GET['action'] == "department_ajax") {
            $departmentFilter = new DepartmentFilter();
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
                $functionName = 'AddOrderBy' . $dataTableColumnName;
                $departmentFilter->$functionName($orderFalse);
            }
            /*
             * Column filter
             * For search
             */

            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchTitle = $this->form_vars['search_title'];
                if (!empty($searchTitle))
                    $departmentFilter->addFieldLikeFilter('title', $searchTitle);

                $searchDescription = $this->form_vars['search_description'];
                if (!empty($searchDescription))
                    $departmentFilter->addFieldLikeFilter('description', $searchDescription);

                $searchDepartmentHead = $this->form_vars['search_department_head'];
                if (!empty($searchDepartmentHead))
                    $departmentFilter->addFieldLikeFilter('u.user_name', $searchDepartmentHead);
            }
            $departmentFilter->addFieldFilter('a.isdeleted', '0');
            $iTotalRecords = $departmentFilter->getPagingCount();
            //Paginatiopn code start here 
            $addressDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $departmentFilter->setRowsPerPage($iDisplayLength);
            $departmentFilter->setOffset($iDisplayStart);
            $departmentObjs = $departmentFilter->getPagingList(" a.title, a.id, a.description, u.user_name 'department_head'");
            $departmentDataArr = [];
            if(count($departmentObjs)>0){
            foreach ($departmentObjs as $departmentObj) {
                $departmentArr = array();
                $option = '';
                $option .= "<a data-id =" . $departmentObj->getId() . " class='btnedit btn btn-sm yellow btn-outline'><span class='fa fa-pencil'></span> </a>";
                $option .= "<a href='#' data-id =" . $departmentObj->getId() . "  class='btndelete btn btn-sm red btn-outline'><span class='fa fa-times'></a>";
                $departmentArr['option'] = $option;
                $departmentArr['title'] = $departmentObj->getTitle();
                $departmentArr['description'] = $departmentObj->getDescription();
                $departmentArr['department_head'] = $departmentObj->getDepartmentHead();

                $departmentCurrArr = base64_encode(json_encode($departmentArr));
                $departmentDataArr [] = $departmentArr;
            }}
            $departmentDataArrJson['data'] = $departmentDataArr;
            $departmentDataArrJson['draw'] = $sEcho;
            $departmentDataArrJson['recordsTotal'] = $iTotalRecords;
            $departmentDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($departmentDataArrJson);
            die;
        } else if (isset($this->form_vars["action"])) {
            $output = array();
            $output['STATUS'] = 'SUCCESS';
//            echo "<pre>";print_r($_POST);echo "</pre>"; die;
            switch ($this->form_vars["action"]) {
                case "DEPARTMENT_ADD":
                    $Department = new Department(intval($this->form_vars["id"]));
                    $Department->setTitle($this->form_vars["dep_title"]);
                    $Department->setDescription($this->form_vars["dep_description"]);
                    $Department->setDepartmentHead($this->form_vars["department_head"]);
                    $Department->setAddedDate(time());
                    $Department->setAddedBy($sessionUser->getId());
                    $Department->setIsActive('1');
                    $Department->setIsDeleted('0');
                    $Department->save();
                    $output['STATUS'] = 'SUCCESS';
                    if (intval($this->form_vars["id"]) > 0)
                        $output['MESSAGE'] = 'Department head successfully updated';
                    else
                        $output['MESSAGE'] = 'Department head successfully added';
                    break;
                case "editrecord":
                    $Department = new Department(intval($this->form_vars["recordid"]));
                    if ($Department->getId() <= 0) {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = 'Department record not found';
                    } else {
                        $output['STATUS'] = 'SUCCESS';
                        $output['department_head'] = $Department->getDepartmentHead();
                        $output['dep_description'] = $Department->getDescription();
                        $output['dep_title'] = $Department->getTitle();
                        $output['id'] = $Department->getId();
                    }

                    break;
                case "deleterecord":
                    $delete_id = $_POST['recordid'];
                    $department = new Department($delete_id);
                    if ($department->getId() <= 0) {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = 'Department record not found';
                    } else {
                        $department->setIsDeleted(1);
                        $department->setIsActive(0);
                        $department->save();
                        $output['STATUS'] = 'SUCCESS';
                        $output['MESSAGE'] = 'Department deleted successfully';
                    }
                    break;




                default:
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = 'No action has been selected';
                    break;
            }
            echo json_encode($output);
            die;
        }
        $AurObj = new UserAccountFilter();
        $AurObj->AddUserTypeFilter('admin');
        $this->User = $AurObj->getColumnList(" full_name, user_name");

        if (isset($_REQUEST['dep_id']) && $_REQUEST['dep_id'] != '' && isset($_REQUEST['action']) && $_REQUEST['action'] = "confirmed_delete") {
            $Department = new Department($_REQUEST['dep_id']);
            $Department->setIsDeleted('1');
            $Department->save();
            util_redirect("department_list.php?del=1");
        }

        $this->table_msg = "Invalid Command";
        $this->setTitle("Department List");
        $this->breadCrumb['data'] = array('index.php' => 'home', '' => 'Department List');
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderFooter() {
        ?>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script type="text/javascript">
            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('a').tooltip();

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
                                    "url": "department_list.php?action=department_ajax",// ajax source
                                    headers: {

                                    },
                                },
                                "bStateSave": true,
                                "columns": [
                                    {"data": "option", "bSortable": false},
                                    {"data": "title"},
                                    {"data": "description"},
                                    {"data": "department_head"},
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

                DataTableFun.init();

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "department_list.php",

                        data: {recordid: recordid, action: "editrecord"}
                    }).done(function (data) {
                        //By using javasript json parse
                        var t = JSON.parse(data);

                        $("#btnDepartmentSave").html("Update");
                        $('#dep_title').val(t.dep_title);
                        $('#dep_description').val(t.dep_description);
                        //$('#department_head').val(t.department_head);
                        $('#department_head').val(t.department_head).selectpicker('refresh');
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                        $('.filter-submit').click();
                    });
                });
                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    if (confirm("Are you sure you want to delete?"))
                    {
                        $.ajax({
                            method: "POST",
                            url: "department_list.php",
                            data: {recordid: recordid, action: "deleterecord"}
                        }).done(function (data) {
                            e.parents('tr').hide();
                            $('#error_message').hide();
                            $('#error_message').show();
                            $('#error_message').removeClass('alert-success').addClass('alert-danger');
                            $('#error_message').html("Record deleted successfully.");
                            $('.filter-cancel').click();
                        });
                    } else
                    {
                        return false;
                    }
                });


                $("#btnDepartmentSave").click(function () {
                    $('#adminForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            var id = $("#id").val();
                            var dep_title = $("#dep_title").val();
                            var dep_description = $("#dep_description").val();
                            var department_head = $("#department_head").val();

                            var form_data = new FormData();
                            form_data.append('id', id);
                            form_data.append('dep_title', dep_title);
                            form_data.append('dep_description', dep_description);
                            form_data.append('department_head', department_head);

                            form_data.append('action', 'DEPARTMENT_ADD');
                            $.ajax({
                                url: 'department_list.php',
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                data: form_data,
                                type: 'post',
                            })
                                    // Code to run if the request succeeds (is done);
                                    // The response is passed to the function
                                    .done(function (response) {
                                        if (response.STATUS == 'SUCCESS') {
                                            $("#adminForm").find("input[type=text], textarea, select").val("");
                                            $("#adminForm").find("select").change();
                                            $('#error_message').removeClass('alert-danger').addClass('alert-success');
                                            $('#error_message').show();
                                            $('#error_message').html(response.MESSAGE);
                                            $('.filter-cancel').click();
                                        } else {
                                            $('#error_message').show();
                                            $('#error_message').removeClass('alert-success').addClass('alert-danger');
                                            $('#error_message').html(response.MESSAGE);
                                        }
                                    })
                                    // Code to run if the request fails; the raw request and
                                    // status codes are passed to the function
                                    .fail(function (xhr, status, errorThrown) {
                                        $('#error_message').show();
                                        $('#error_message').removeClass('alert-success').addClass('alert-danger');
                                        $('#error_message').html("Sorry, there was a problem!");
                                    })
                                    // Code to run regardless of success or failure;
                                    .always(function (xhr, status) {
                                        // alert( "The request is complete!" );
                                    });
                            return false;
                        }
                    });
                    $("#adminForm").submit();
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
        <div class="alert alert-danger display-none"  id="error_message" ></div>
        <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">

            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-plus"></i>
        <?= Translation::GetCaption("DEPARTMENT_ADD"); ?>
                    </div>                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                    <input type="text" name="dep_title" id="dep_title" value="<?= @$title; ?>" style=""  class="form-control"  rel="tooltip" placeholder="Title" title="Title" required="required"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input required="required" type="text" name="dep_description" id="dep_description" value="<?= @$description ?>" placeholder="Description"  class="form-control" rel="tooltip"  title="Description">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                    <?php 
                                   $userAccountId = $this->sessionUser->getUserAccountId();
                                    echo Ddl::generateDDL('department_head', 'UserFilter', " active_flag = '1' AND user_account_id = '". $userAccountId ."'", 'user_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="User" data-original-title="User"', '', 'Select document type', 'document_name', 'Select', '', '');
                                    
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="hidden" name="id" id="id" value="0" placeholder="id"  class="form-control" rel="tooltip"  title="id">
                            <a id="btnDepartmentSave"   href="#" class="btn btn-primary btn_save"><span></span> Save  </a>
                            <a id="btnCancel" class="btn_cancel btn btn btn-default" href="#"><span></span> Cancel </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
        <?= Translation::GetCaption("DEPARTMENT_LIST"); ?>
                </div>                    
            </div>
            <div class="portlet-body">

                <!-- describe table filter -->
                <!-- CONSIGNMENT TABLE -->
                <div class="col-md-12 alert alert-danger display-none"  id="display_message" ></div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Department Name</th>
                                <th>Department Head Name</th>
                                <th>Department Description</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-sm yellow filter-submit btn-outline margin-bottom-5"><i class="fa fa-search"></i></button>
                                        <button  class="btn btn-sm red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_title">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_description">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_department_head">
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div> 


            </div>
            <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        </div>  <!-- table_container -->
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
