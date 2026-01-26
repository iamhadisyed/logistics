<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $user = null;
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Custom Clearance Agent"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "custom_clearance_agent_ajax") {
            $customClearanceAgentFilter = new CustomClearanceAgentFilter();
             /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $agentName = $this->form_vars['agent_name'];
                if (!empty($agentName))
                    $customClearanceAgentFilter->addFieldLikeFilter('agent_name', $agentName);
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
                $customClearanceAgentFilter->AddOrderBy(strtolower("cca." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $customClearanceAgentFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $customClearanceAgentFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $customClearanceAgentFilter->setOffset($iDisplayStart);
            $customClearanceAgentFilter->AddOrderBy("cca.id");
            $customClearanceAgentObjs = $customClearanceAgentFilter->getPagingList();
            $setDataArr = array();

            foreach ($customClearanceAgentObjs as $customClearanceAgentObj) {
                $currentArr = array();
                $currentArr['agent_name'] = $customClearanceAgentObj->getAgentName();
                $currentArr['actions'] = '<div class="btn-group">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">';
                $currentArr['actions'] .= '<li>
                                                <a title="Edit" href="JavaScript:void(0);" data-agent_id="' . $customClearanceAgentObj->getId() . '" class="btnedit" >
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '<li>
                                                <a title="Delete" href="JavaScript:void(0);" data-remoteareas_id="' . $customClearanceAgentObj->getId() . '" class="btndelete" >
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '<li>
                                                <a title="Oath Field" href="manage_custom_clearance_agent.php?agent_id='.$customClearanceAgentObj->getId().'"  class="" >
                                                    <i class="fa fa-key"></i> Oath Field
                                                </a>
                                            </li>';
                $currentArr['actions'] .= '</ul> </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == "save") {
            $id = "";
            if(isset($this->form_vars['id']) && $this->form_vars['id'] > 0)
                $id = $this->form_vars['id'];
            if(!empty(trim($this->form_vars['agent_name']))){
                $customClearanceAgent = new CustomClearanceAgent($id);
                $customClearanceAgent->setAgentName($this->form_vars['agent_name']);
                $customClearanceAgent->setAddedBy($this->user->getId());
                $customClearanceAgent->setAddedDate(time());
                $customClearanceAgent->save();
                if($customClearanceAgent->getId() > 0){
                    $returnMsg['STATUS'] = "success";
                    $returnMsg['MESSAGE'] = "Agent Added successfully";
                    echo json_encode($returnMsg);
                }else{
                    $returnMsg['STATUS'] = "error";
                    $returnMsg['MESSAGE'] = "Agent Added issue";
                    echo json_encode($returnMsg);
                }
            }else{
                $returnMsg['STATUS'] = "error";
                $returnMsg['MESSAGE'] = "Agent Added issue";
                echo json_encode($returnMsg);
            }
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit") {
            $agentId = $this->form_vars['agent_id'];
            if ($agentId > 0) {
                $remoteareasGroups = new CustomClearanceAgent($agentId);
                $returnMsg['STATUS'] = "success";
                $returnMsg['agent_name'] = $remoteareasGroups->getAgentName();
                $returnMsg['agent_id'] = $agentId;
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $user = SessionManager::getUser();
            $remoteareasId = $this->form_vars['remoteareas_group_id'];
            if ($remoteareasId > 0) {
                $remoteareas = new CustomClearanceAgent($remoteareasId);
                $oldCustomClearanceAgentData = serialize($remoteareas);
                $returnMsg['STATUS'] = "success";
                $remoteareas->setUpdatedBy($user->getId());
                $remoteareas->setUpdatedDate(time());
                $remoteareas->setIsDeleted("Y");
                $remoteareas->save();
                /*
                * Add Remoteareas Log details
                */
                $remoteareasGroupsLog = new CustomClearanceAgentLog();
                $newCustomClearanceAgentData = serialize($remoteareas);
                $remoteareasGroupsLog->createlog($user->getId(),'',$remoteareasId,'REMOTEAREAS_GROUPS',$user->getUserName() . ' has deleted ' . $remoteareasId,$oldCustomClearanceAgentData, $newCustomClearanceAgentData);
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


        <script type="text/javascript">
            var grid = null;
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
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here 
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "custom_clearance_agent.php?action=custom_clearance_agent_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "agent_name"}
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
                $("#csv_download_btn").click(function () {
                    $("#message_download_csv").hide();
                    $('#csv_download').modal('show');
                });
                $("#download_csv").click(function () {
                    var carrierId = $("#carrier_csv").val();
                    $.ajax({
                        type: "POST",
                        url: "custom_clearance_agent.php",
                        data: {action: "check_download_remoteareas", carrier_id: carrierId},
                        dataType: "json",
                        success: function (data) {
                            if (data.status == "success") {
                                $("#carrier_id_hidden").val(carrierId);
                                $("#hiddenForm").submit();
                                $('#csv_download').modal('hide');    
                            } else {
                                $("#message_download_csv div.alert").html("There is no record found to download");
                                $("#message_download_csv").show();
                            }
                        },
                        error: function () {
                            alert('error handing here');
                        }
                    });
                });
                $("#btnSubmitImport").click(function () {
                    $("#file_in").val("");
                    $("#csv_upload").modal('show');
                });
                $("#upload_csv").click(function () {
                    $('#console_window').show();
                    $('#console_window').html('');
                    $('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#file_in').prop('files')[0];
                    $('#csv_upload').modal('hide');
                    var form_data = new FormData();
                    var carrierId = "";
                    carrierId = $("#carrier_csv_upload").val();
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    form_data.append('carrier_id', carrierId);
                    $.ajax({
                        url: "custom_clearance_agent.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#console_window').append(response.message);
                                grid.getDataTable().ajax.reload();
                            } else {
                                $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                            }
                        }
                    });
                    return false;
                });
            });
            $('#btnSave').click(function () {
                if ($("#add_carrier_id").val() == "") {
                    swal("", "Please select Carrier name", "info");
                } else if ($("#group_name").val() == "") {
                    swal("", "Please enter group Name", "info");
                } else {
                    $("#form_action").val("save");
                    $.post("custom_clearance_agent.php", $("#adminForm").serialize(), function (response) {
                        $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                        if (response.STATUS == "success") {
                            $("#res_message div.alert").addClass('alert-success');
                            $("#res_message div.alert").html("Remoteareas Group added successfully");
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                            $("#group_name").val("");
                            $('#add_carrier_id').val("");
                        } else {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(response.MESSAGE);
                            $("#res_message").show();
                        }
                    }, "json");
                }
            });
            $(document).on('click', '.btndelete', function () {
                    var remoteareasId = $(this).attr("data-remoteareas_id");
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
                function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "custom_clearance_agent.php",
                        data: {action: "delete", remoteareas_group_id: remoteareasId},
                        dataType: "json",
                        success: function (data) {
                            if (data.STATUS == "success") {
                                    $("#res_message").html("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
                                    $("#res_message").show();
                                    $(".scroll-to-top").click();
                                    grid.getDataTable().ajax.reload();
                            } else {
                            }
                        },
                        error: function () {
                            alert('error handing here');
                        }
                    });
                }
                });
                
            });
            $(document).on('click', '.btnedit', function () {
                var agentId = $(this).attr("data-agent_id");
                $.ajax({
                    type: "POST",
                    url: "custom_clearance_agent.php",
                    data: {action: "edit", agent_id: agentId},
                    dataType: "json",
                    success: function (data) {
                        if (data.STATUS == "success") {
                            $("#agent_name").val(data.agent_name);
                            $("#id").val(data.agent_id);
                            $(".scroll-to-top").click();
                        } else {
                        }
                    },
                    error: function () {
                        alert('error handing here');
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
        <form name="adminForm" id="adminForm" action="" method="POST">        
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-dropbox"></i>
                        Add/Update Custom Clearance Agent
                    </div>
                    <div class="actions">
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row display-none">
                        <div class="col-md-12">
                            <div class="alert alert-success" id="res_message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="first_form_col">
                                <div class="form-group">
                                    <label>Agent Name</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <input type="text" name="agent_name" id="agent_name" required="required" title="Agent Name" value="" class="form-control" placeholder='Agent Name' />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;">  </div>          
                        <div class="col-md-12 text-center">  
                            <a id="btnSave"   href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>               
                            <a href="custom_clearance_agent.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo @$this->form_vars["id"]; ?>" />
            <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Custom Clearance Agent List
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Agent Name</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="agent_name" id ="agent_name" />
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