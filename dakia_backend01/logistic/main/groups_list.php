<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */
include_classes([   
                    'groups.class',
                    'groupsfilter.class',
                    'groupslog.class',
                    'groupslogfilter.class'
                    ]);
class Page extends BasePage {
    private $groups_filter;
    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
                                    'add_groups.php' => Translation::GetCaption("GROUP"),
                                    Translation::GetCaption("GROUP_LIST")
        );
        $user = SessionManager::getUser();
        t_on(); // turn on trace for this page
/*
        *  END 
        * Ajax Handling for Consignment table value
        */
        if (isset($_GET['action']) && $_GET['action'] == "groups_ajax") {
            $this->groups_filter = new GroupsFilter();
            $this->groups_filter->addFilter('is_deleted = 0');            
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
                $this->groups_filter->AddOrderBy($dataTableColumnName, $orderFalse);

            }
             /*
            * Column filter
            * For search
            */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
               $this->groups_filter = new GroupsFilter();

               $groupName = $this->form_vars['group_name'];
               if (!empty($groupName))
                   $this->groups_filter->addFieldLikeFilter('group_name', trim($groupName));

               $groupDesc = $this->form_vars['group_desc'];
               if (!empty($groupDesc))
                   $this->groups_filter->addFieldLikeFilter('group_desc', trim($groupDesc));

               $isActive = $this->form_vars['is_active'];
               if (isset($isActive) && trim($isActive) == "0"){
                   $this->groups_filter->addFilter('is_active = 0');
               }else if (isset($isActive) && trim($isActive) == "1"){
                  $this->groups_filter->addFilter('is_active = 1'); 
               }
           }
           
           /*
            * Set pagination & Encode data into Json form to return to DataTable
            */
           $iTotalRecords = $this->groups_filter->getPagingCount();
           $iDisplayLength = intval($_REQUEST['length']);
           $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
           $iDisplayStart = intval($_REQUEST['start']);
           $sEcho = intval($_REQUEST['draw']);
           $end = $iDisplayStart + $iDisplayLength;
           $end = $end > $iTotalRecords ? $iTotalRecords : $end;
           $this->groups_filter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->groups_filter->setOffset($iDisplayStart);
            $groupsFilterList = $this->groups_filter->getPagingList();
            
            $groupsDataArr = array();
            $status = array();
            $status[0] = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
            $status[1] = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
            foreach ($groupsFilterList as $groupsList) {
                $addedBy = new User($groupsList->getAddedBy());
                $userName = "";
                $userName = $addedBy->getFirstName();
                $groupsArr['option'] = '<a href="add_groups.php?gid='.base64_encode($groupsList->getGroupId()).'" class="btn btn-xs btn-default blue btn-outline pull-left group_edit" rel="tooltip" data-toggle="tooltip" placeholder="Edit" title="Edit"> <span class="fa fa-pencil"></span> </a>
                                        <a href="javascript:void(0);" data-group-id = "'.$groupsList->getGroupId().'"  class="btn btn-xs btn-default red btn-outline pull-left group_delete" rel="tooltip" placeholder="Delete" title="Delete" ><span class="fa fa-trash"></span> </a>
                                        <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" rel="tooltip"  placeholder="View Audit" data-log_key="'.$groupsList->getGroupId().'" 
                                 data-log_name="groups" data-toggle="modal"> <span class="fa fa-list"></span> 
                                 </a>';
                $groupsArr['group_name'] = $groupsList->getGroupName();
                $groupsArr['group_desc'] = $groupsList->getGroupDesc();
                $groupsArr['is_active'] = $status[$groupsList->getIsActive()];
                $groupsArr['added_by'] = $userName;
                $groupsDataArr[] = $groupsArr;
            }
            $groupsDataarr['data'] = $groupsDataArr;
            $groupsDataarr['draw'] = $sEcho;
            $groupsDataarr['recordsTotal'] = $iTotalRecords;
            $groupsDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($groupsDataarr);
            die;
        }
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new Groups new Groups details - if OK, save and return to booking list
                case "delete":                    
                     
                    $groupsObjd = new GroupsFilter();
                   
                    $groupsObjd->addFilter(" group_id = '".$this->form_vars["id"]."'");
                    $groupsObjdata = $groupsObjd->getList('*');
                    $groupsObj = $groupsObjdata[0];
                    $groupName = $groupsObj->getGroupName();
                    $oldGroupData = $groupsObj;
                    $groupsObj->setIsDeleted('1');
                    $groupsObj->save();
                    /*
                     * Add Groups Log details
                     */
                    $groupsLog = new GroupsLog();
                    $newGroupsData = serialize($groupsObj);
                    $oldGroupsData = serialize($oldGroupData);
                            $groupsLog->createlog($user->getId(),'',$groupsObj->getGroupId(),'GROUPS',$user->getUserName() . ' has deleted ' . $groupName,$oldGroupsData, $newGroupsData);
                    $output["status"] = "success";
                    $output["message"] = Translation::GetCaption("GROUP_DELETE_MESSAGE");
                    echo json_encode($output);
                    die;
                    break;

                case "cancel":
                default:
                    util_redirect("add_groups.php");
                    break;
            }
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>

        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
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
                        <?php echo Translation::GetCaption("GROUP_LIST"); ?>
                    </div>
                    <div class="actions">
                        <a href="add_permission.php" class="btn blue"><span></span><i class="fa fa-lock"></i>&nbsp;<?=Translation::GetCaption("ADD_PERMISSIONS");?></a>
                        <a href="add_groups.php" class="btn blue"><span></span><i class="fa fa-plus"></i>&nbsp;<?=Translation::GetCaption("ADD_GROUPS")?></a>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th>
                                        Options
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Description
                                    </th>
                                    <th>
                                        Active
                                    </th>
                                    <th>
                                        Created By
                                    </th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <button class="btn btn-xs btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                        <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                    </td>
                                    <td>
                                            <input type="text" class="form-control form-filter input-sm" name="group_name">
                                    </td>
                                    <td>
                                            <input type="text" class="form-control form-filter input-sm" name="group_desc">
                                    </td>
                                    <td>
                                        <?php
                                        $arrayTypeValues    =   array('0'=>'No', '1'=>'Yes');
                                        echo Ddl::generateArrayDDL('is_active', $arrayTypeValues, "" , "Please Select", ' class="select2 form-control form-filter input-sm searchbox"', "", 'is_active' ,'Active','');?>
                                    </td>
                                    <td>
                                            <input type="text" class="form-control form-filter input-sm" name="added_by">
                                    </td>
                                        
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
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

    public function renderFooter() {
        ?>
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
                        "url": "groups_list.php?action=groups_ajax", // ajax source
                        headers: {
                            
                        },
                    },
                    "bStateSave": true,
                    "columns": [
                        {"data": "option", "bSortable": false},
                        {"data": "group_name"},
                        {"data": "group_desc"},
                        {"data": "is_active"},
                        {"data": "added_by"}
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
</script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('a').tooltip();
                DataTableFun.init();               
            });
            $(document).on('click', '.group_delete', function () {
                $("#id").val($(this).data("group-id"));
                swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD"); ?>",
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
                    $("#form_action").val("delete");
                    handleActionAjax('delete');
                }
                });
                
                    
            });
            function handleActionAjax(action) {
                var dataString = "";                
                dataString = $("#adminForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "groups_list.php",
                    data: dataString,
                    dataType: "json",
                    success: function (data) {
                        $('#res_message').removeClass('alert-danger');
                        $('#res_message').removeClass('alert-success');
                        $('#res_message').removeClass('alert-info');
                        if (data.status == 'success') {
                            $('#res_message').addClass('alert-success');
                            if (action == 'delete') {
                                grid.getDataTable().ajax.reload();
                            }
                        } else if (data.status == 'info') {
                            $('#res_message').addClass('alert-info');
                        } else {
                            $('#res_message').addClass('alert-danger');
                        }
                        $('#res_message').html(data.message);
                        $('#res_message').show();
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }
            var UITree = function () {
        var tree = function () {
            $("#tree_2").jstree({
                "core": {
                    "themes": {
                        "responsive": false,
                        "icons": false
                    },
                    //"keep_selected_style": false,
                    // so that create works
                    "check_callback": false,
                    'data': {
                        'url': function (node) {
                            return 'add_groups.php';
                        },
                        'data': function (node) {
                            return {'func': 'get_tree_data', 'parent': node.id <?php echo (isset($_GET["Update"]) && isset($_GET["COAID"]) ? ", 'COAID':" . $_GET["COAID"] : ""); ?>};
                        }
                    }
                },
                "checkbox": {
                    three_state: false,
                    cascade: 'down'
                },
                "plugins": ["checkbox", "ui"]
            })

        }

        return {
            //main function to initiate the module
            init: function () {
                tree();
            }
        };
        }();
        </script>
        <?php
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();