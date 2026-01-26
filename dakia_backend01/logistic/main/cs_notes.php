<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'ddl.inc'
        ], 'library');
include_classes([
    'csnotes.class',
    'csnotesfilter.class'
]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $user;
    private $csnotesFilter = '';

    protected function init() {
        
         $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Notes"
        );

        $this->user = SessionManager::getUser();
        
        if ($_GET['action'] && $_GET['action'] == 'view_cs_notes') {
            $this->csnotesFilter = new CSNotesFilter();
            $this->csnotesFilter->join("customer_account ua", ['csn.created_by' => 'ua.id']);
            if ($this->user->getUserType() != USER::USER_TYPE_ADMIN) {
                $this->csnotesFilter->where(['csn.created_by' => $this->user->getUserAccountId()]);
            }
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->applyFilter($this->form_vars);
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
                if($dataTableColumnName == "Created_by") {
                    $this->csnotesFilter->orderBy(strtolower("csn.created_by"), $orderFalse);
                }
                if($dataTableColumnName == "Created_date") {
                    $this->csnotesFilter->orderBy(strtolower("csn.date_created"), $orderFalse);
                }
            } else {
                $this->csnotesFilter->orderBy(strtolower("csn.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $this->csnotesFilter->setRowsPerPage($iDisplayLength);
            $this->csnotesFilter->setOffset($iDisplayStart);
            $csnotesFilterObj = $this->csnotesFilter->getList("csn.*,ua.user_account");
            $iTotalRecords = $this->csnotesFilter->getCount();
            $setDataArr = array();
            foreach ($csnotesFilterObj as $csnotesFilterobj) {
                if (!empty($csnotesFilterobj->getDateCreated())) {
                    $date_created = date("d-m-Y", $csnotesFilterobj->getDateCreated());
                } else {
                    $date_created = "";
                }
                $currentArr = array();
                $currentArr['notes'] = "<div class='note_box'>".$csnotesFilterobj->getNotes()."</div>";
                if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                    $currentArr['created_by'] = $csnotesFilterobj->getUserAccount();
                }
                $currentArr['created_date'] = $date_created;
                $currentArr['actions'] = '';
                $currentArr['actions'] .= '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        <ul class="dropdown-menu" >';
                        $currentArr['actions'] .= '<li>
                                                        <a title="Edit" href="javascript:;" onclick="editNote(' . $csnotesFilterobj->getId() . ')" >
                                                            <span class="fa fa-pencil"></span> Edit
                                                        </a>
                                                    </li>';
                        $currentArr['actions'] .= '<li>
                                                   <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $csnotesFilterobj->getId() . '" 
                                                    data-log_name="cs_notes" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                                                    </a>
                                                    </li>';
                $currentArr['actions'] .= '</ul>
                                            </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == "save_note") {
            $output = [];
            $id = $this->form_vars["id"];
            $note = $this->form_vars["note"];
            $created_by = $this->user->getUserAccountId();
            $date_created = time();
            if (!empty($id)) {
                $notes = new CSNotes($id);
            } else {
                $notes = new CSNotes();
            }
            $notes->setNotes($note);
            $notes->setCreatedBy($created_by);
            $notes->setDateCreated($date_created);
            $notes->save();
            $output['status'] = "success";
            $output['message'] = "Notes is created successfully";
            echo json_encode($output);
            die;
        }
        
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == "edit_note") {
            $note = new CSNotes($this->form_vars["id"]);
            $responceArray = [];
            if (!empty($note)) {
                $responceArray['id'] = $note->getId();
                $responceArray['note'] = $note->getNotes();
            }
            echo json_encode($responceArray);
            exit;
        }
    }
    
    protected function applyFilter($form_vars) {
        $this->form_vars = $form_vars;
        /*
         * Column filter
         * For search
         */
        if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
            $filterArray = [];
            $account = $this->form_vars['user_account_id_filter'];
            if (!empty($account)) {
                $filterArray['csn.created_by'] = $account;
            }
            $this->csnotesFilter->where($filterArray);
        }

        $note = $this->form_vars['notes'];
        if (!empty($note))
            $this->csnotesFilter->where("(csn.notes LIKE '%" . DbAccess3::escape($note) . "%')");

        $date_created_from = $this->form_vars['date_created_from'];
        $date_created_to = $this->form_vars['date_created_to'];
        if (!empty($date_created_from) && !empty($date_created_to))
            $this->csnotesFilter->whereBetween ('csn.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));

    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />   
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/max_length_jquery/src/css/show-hide-text.min.css" rel="stylesheet" type="text/css" />
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
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="./../assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/max_length_jquery/src/js/show-hide-text.min.js" type="text/javascript"></script>
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
                                "url": "cs_notes.php?action=view_cs_notes", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "notes", "bSortable": false},
                                <?php if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) { ?>
                                {"data": "created_by"},
                                <?php } ?>        
                                {"data": "created_date"},
                            ],
                            rowCallback: function (row, data, index) {
                                
                            },
                            "initComplete": function( settings, json ) {
                                new showHideText('.note_box', {
                                    charQty     : 50,
                                    ellipseText : "...",
                                    moreText    : "Read more",
                                    lessText    : "Read less"
                                });
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

            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click', '#add_note', function () {
                    var form_data = new FormData();
                    var id = $("#note_id").val();
                    var note = $("#notes_description").val();
                    form_data.append('id', id);
                    form_data.append('note', note);
                    form_data.append('action', 'save_note');
                    $.ajax({
                            url: "cs_notes.php",
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            dataType: 'json',
                            success: function (data) {
                                $("#notes_description").val('');
                                $('#addNotesModal').modal('hide');
                                grid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                            },
                            error: function () {
                                swal("Sorry!", "something went wrong please content to admin", "error");
                            }
                    });
                });         
            });
            function addNote() {
                $("#notes_description").val('');
                $("#note_id").val('');
                $('#note_model_title').html("Add Note");
                $('#addNotesModal').modal('show');
            }
            function editNote(id) {
                $.post(
                        "cs_notes.php",
                        {action: 'edit_note', id: id},
                        function (data)
                        {
                            $("#note_id").val(id);
                            $("#notes_description").val(data.note);
                            $('#note_model_title').html("Edit Note");
                            $('#addNotesModal').modal('show');
                        }
                , "json");
            }
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .morelink {
                margin-top: 0px;
                color: #3598DC;
            }

        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-users"></i>
                    Notes
                </div>
                <div class="actions">
                    <input type="button" class="btn btn-primary pull-right" value="Add Notes" title="Add Notes" name='btnNotes' onclick="addNote()" >
                </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Notes</th>
                                <?php if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) { ?>
                                <th>Created By</th>
                                <?php } ?>
                                <th>Created Date</th>
                            </tr>
                            <tr role="row" class="heading">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                    </div>
                                </td>   
                                <td>
                                    <input type="text" name="notes" class="form-control form-filter" >
                                </td>
                                <?php if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) { ?>
                                <td>
                                    <div class="form-group">
                                    <?php
                                    $accountParentId = 0;
                                    $includeParent = true;
                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                        $accountParentId = $this->user->getUserAccountId();
                                        $includeParent = false;
                                    }
                                    $selectedAccount = "";
                                    $allowedLevel = 0;
                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                        $allowedLevel = 1;
                                    }
                                    ?>
                                    <?php echo Ddl::showTreeDropdown('user_account_id_filter', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                </div>

                                </td>
                                <?php } ?>
                                <td>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>	
        <div class="modal fade" id="addNotesModal" tabindex="-1" role="dialog" aria-labelledby="addNotesModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title note_model_title">Add Notes</h4>
                    </div>

                    <form action="javascript:;" name="notes_form" id="notes_form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Notes</label>
                                        <textarea data-provide="markdown" class="form-control"  rows="7" cols="150" name="notes_description" placeholder="Notes" id="notes_description" rel="tooltip" data-original-title="Notes" required="required"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="note_id" id="note_id" value="">
                            <button type="submit" id = "add_note" class="btn btn-info btn_save">Save</button>
                            <button type="button"  class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
