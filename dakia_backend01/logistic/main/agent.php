<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
]);
// set up local page class
class Page extends BasePage {
     private $user = NULL;
    public function init() {
        if (!Permissions::checkFilePermission('agent.php'))
            util_redirect("index.php");

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("AGENT")
        );
        // check admin user is authenticated
        $this->user = SessionManager::getUser();


        if (isset($_GET['action']) && $_GET['action'] == "agent_ajax") {

            $agent_filter = new AgentDataFilter();
            $agent_filter->addFieldFilter('is_deleted', 0);
            /*
             * Set columns orders for sorting
             */
            $agent_filter->addFilter(" user_id in (select id from user where user_account_id ='".$this->user->getUserAccountId()."')" );
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = strtolower($this->form_vars['columns'][$dataTableColumnId]['data']);
            }


            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $search_agentType = $this->form_vars['search_agentType'];
                if (!empty($search_agentType))
                    $agent_filter->addFieldFilter('agent_type', $search_agentType);

                $agent_code = $this->form_vars['search_agentcode'];
                if (!empty($agent_code))
                    $agent_filter->addFieldLikeFilter('agent_code', $agent_code);

                $search_contact = $this->form_vars['search_contact'];
                if (!empty($search_contact))
                    $agent_filter->addFieldLikeFilter('agent_name', $search_contact);


                $search_telephone = $this->form_vars['search_telephone'];
                if (!empty($search_telephone))
                    $agent_filter->addFieldLikeFilter('telephone', $search_telephone);

                $search_Country = $this->form_vars['search_Country'];
                if (!empty($search_Country))
                    $agent_filter->addFieldFilter('country_id', $search_Country);

                $search_status = $this->form_vars['search_Active'];
                if ($search_status != '')
                    $agent_filter->addFieldFilter('active', $search_status);
            }

            $iTotalRecords = $agent_filter->getPagingCount();
            //Paginatiopn code start here 
            $agentDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $agent_filter->setRowsPerPage($iDisplayLength);
            $agent_filter->setOffset($iDisplayStart);
            if ($dataTableColumnName != '')
                $agent_filter->AddOrderBy($dataTableColumnName, $orderFalse);
            $agentObjs = $agent_filter->getPagingList(" agent_type, agent_code, agent_name, telephone, email, country_id, active, logo", false);
            foreach ($agentObjs as $agentObj) {
                $countryFilter = new CountryFilter();
                $countryFilter->addFilter(" id = '" . $agentObj->getCountryId() . "'");
                $countryList = $countryFilter->getList();

                if (count($countryList) > 0) {
                    $countryName = $countryList[0]->getName();
                    $countryIso = $countryList[0]->getIso();
                }

                $agentArr = array();
                $agentArr['option'] .= '<div class="btn-group">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">';
                if (Permissions::checkFilePermission('agent_view')) {
                    $agentArr['option'] .= '<li>
                                                        <a title="View" href="agent_view.php?id=' . $agentObj->getId() . '" class="btnedit" >
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </li>';
                }
                if (Permissions::checkFilePermission('agent_edit')) {
                    $agentArr['option'] .= '<li>
                                                        <a title="Edit" href="agent_details.php?id=' . $agentObj->getId() . '" class="btnedit" >
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </a>
                                                    </li>';
                }
                if (Permissions::checkFilePermission('agent_delete')) {
                    $agentArr['option'] .= '<li>
                                                        <a title="Delete" data-id ="' . $agentObj->getId() . '" class="btndelete" >
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    </li>';
                }
                                 $agentArr['option'] .= "<li>"
                                            . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $agentObj->getId() . "' data-log_name='agent_data' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                            . "</li>";
                
                $agentArr['option'] .= '</ul> </div>';
                $agentType = ucfirst($agentObj->getAgentType());
                if ($agentType == "Dispatch") {
                    $agentArr['agent_type'] = '<center><span class="label label-sm label-success">' . $agentType . '</span></center>';
                } else {
                    $agentArr['agent_type'] = '<center><span class="label label-sm label-danger label-theme">' . $agentType . '</span></center>';
                }
                $logo = $agentObj->getLogo();
                if ($logo != '') {
                    $image = ' <img src=\'../images/agentlogo/thumbnail/owe_16_' . $logo . '\' /> ' . strtoupper($agentObj->getAgentCode());
                } else {
                    $image = ucfirst(strtolower($agentObj->getAgentCode()));
                }
                $agentArr['agent_code'] = $image;
                $agentArr['agent_name'] = ucfirst(strtolower($agentObj->getAgentName()));
                $agentArr['telephone'] = $agentObj->getTelephone();
                $agentArr['country_iso_code'] = '<img src=\'../assets/global/img/flags/' . strtolower($countryIso) . '.png\' /> ' . ucfirst($countryName);
                $agentArr['active'] = ($agentObj->getActive() == 1 ? '<center><span class="label label-sm label-success">Yes</span></center>' : '<center><span class="label label-sm label-danger">No</span></center>');

                $agentDataArr [] = $agentArr;
            }
            $agentDataArrJson['data'] = $agentDataArr;
            $agentDataArrJson['draw'] = $sEcho;
            $agentDataArrJson['recordsTotal'] = $iTotalRecords;
            $agentDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($agentDataArrJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "DELETEAGENT") {

            $output = array();
            $agentid = $this->form_vars["agentid"];
            if ($agentid > 0) {
                $agentObj = new AgentData($agentid);
                $agentObj->setIsDeleted(1);
                $agentObj->save();
                $output["status"] = "success";
                $output["message"] = formatMessages(SUCCESS_RECORD_DELETED); //"Record deleted successfully.";
            } else {
                $output["status"] = "fail";
                $output["message"] = formatMessages(ERROR_DELETE_AGENT); //"Unable to delete agent.";
            }
            echo json_encode($output);
            die;
        } else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "export_agent") {

            $output = array();
            $agent_filter = new AgentDataFilter();
            $agent_filter->addFieldFilter('is_deleted', 0);
            $agentList = $agent_filter->getList();
            if (count($agentList > 0)) {
                $csv = "";
                $cr = "\r\n";
                foreach ($agentList as $agent) {
                    $countryFilter = new CountryFilter();
                    $countryFilter->addFilter(" id = '" . $agent->getCountryId() . "'");
                    $countryList = $countryFilter->getList();

                    if (count($countryList) > 0) {
                        $countryName = $countryList[0]->getName();
                        $countryIso = $countryList[0]->getIso();
                    }
                    $csv .= cleanCsvCall($agent->getAgentType()) . ",";
                    $csv .= strtoupper(cleanCsvCall($agent->getAgentCode())) . ",";
                    $csv .= cleanCsvCall($agent->getAgentName()) . ",";
                    $csv .= cleanCsvCall($agent->getContactName()) . ",";
                    $csv .= cleanCsvCall($agent->getAddressLine1()) . ",";
                    $csv .= cleanCsvCall($agent->getCity()) . ",";
                    $csv .= cleanCsvCall($countryName) . ",";
                    $csv .= cleanCsvCall($agent->getPostcode()) . ",";
                    $csv .= cleanCsvCall($agent->getTelephone()) . ",";
                    $csv .= $cr;
                }
                $csvHeader = "Agent Type, Agent Code, Agent Name, Contact Name, Address Line 1, City, Country, Postcode, Telephone";
                $folder_path = SETTING_DIR_ASSETS . "/csv/";

                if (!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }

                $uniqueFileName = "agentExportData";

                $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                $file_path = fopen($file_path, 'w');
                fwrite($file_path, $csvHeader . "\r\n" . $csv);
                fclose($file_path);
                $output["status"] = "success";
                $output["message"] = "../_assets/csv/" . $uniqueFileName . ".csv";
            } else {
                $output["status"] = "fail";
                $output["message"] = formatMessages(ERROR_FILE_DOWNLOAD); //"Unable to download file.";
            }
            echo json_encode($output);
            die;
        }
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
//		 Adminmenu::CURRENCY
        $menu = new Adminmenu();
        $menu->render();
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>

        <?php
    }

    public function renderHead() {
        
    }

    public function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                     Agent List
                </div>
                <div class="actions">
                    <?  if(Permissions::checkFilePermission('agent_details.php')) { ?>
                    <a href="agent_details.php" class="btn blue"  ><i class="fa fa-plus"></i> Add New Agent</a>
                    <? } ?>

                    <a href="javascript:;" class="btn blue" id="btnExportData"> <i class="fa fa-download"></i> <?php echo Translation::GetCaption("EXPORT"); ?></a>    
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-md-12 display-none"  id="display_message" ></div>
                <div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading ">
                                <th>Action</th>
                                <th>Agent Type</th>
                                <th>Agent Code</th>
                                <th>Agent Name</th>
                                <th>Telephone</th>
                                <th>Country</th>
                                <th>Active</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs blue filter-submit btn-outline margin-bottom-5"><i class="fa fa-search"></i></button>
                                        <button  class="btn btn-xs red filter-cancel btn-outline  margin-bottom-5"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td class="user_acccount_correct_button">
        <?php
        $agentTypeArray = array("carrier" => "Carrier", "dispatch" => "Dispatch", "both" => "Both");
        echo ddl::generateArrayDDL("search_agentType", $agentTypeArray, $search_agentType, "Select Agent Type", ' class="form-control form-filter select2"', '', "agentType", "Select Agent Type")
        ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="search_agentcode">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter " name="search_contact">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="search_telephone">
                                </td>
                                <td class="user_acccount_correct_button">
        <?php echo Ddl::generateCountryDDL('search_Country', '', 'id', ' class="bs-select form-control form-filter" required="" data-live-search="true" data-show-subtext="true"'); ?>
                                </td>
                                <td class="user_acccount_correct_button">
        <?php
        $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
        echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2"', "", 'search_Active', 'Select Status', '');
        ?>

                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="modal fade  bs-modal-lg" id="export-product-popup" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalExport" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close modal-close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Export Agent</h4>
                            </div>
                            <form class="form-horizontal" role="form" action="" method="POST" name="frm_layout">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                <span class="sr-only"> 100% Complete </span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <span><a  id="download-csv"  class="btn blue margin-left-5" >Download</a></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn default modal-close" data-dismiss="modal">Close</button>
                                    <input type="hidden" name="save_layout" value="layout_editor"/>
                                </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </div>
        </div>
        <?php
    }

    public function renderFooter() {
        ?>
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
                                "url": "agent.php?action=agent_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option"},
                                {"data": "agent_type"},
                                {"data": "agent_code"},
                                {"data": "agent_name"},
                                {"data": "telephone"},
                                {"data": "country_iso_code"},
                                {"data": "active"}
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

                $(document).on('click', '#btnExportData', function () {
                    $('#export-product-popup').modal('show');
                    $(".progress-bar-info").attr("aria-valuenow", "10");
                    $('.progress-bar-info').css('width', '10%');
                    $('#download-csv').hide();
                    var form_data = new FormData();
                    form_data.append('action', 'export_agent');
                    $.ajax({
                        url: "agent.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == "success")
                            {
                                //alert(response.message);
                                $(".progress-bar-info").attr("aria-valuenow", "100");
                                $('.progress-bar-info').css('width', '100%');
                                $('#download-csv').show();
                                $("#download-csv").attr("href", response.message);
                            }
                        }
                    });

                });
                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    var agentid = e.data('id');
                    swal({
                        title: "Are you sure you want to delete?",
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
                                        method: "POST",
                                        url: "agent.php",
                                        data: {agentid: agentid, action: "DELETEAGENT"}
                                    }).done(function (data) {
                                        var t = JSON.parse(data);
                                        if (t.status == "success")
                                        {
                                            e.parents('tr').hide();
                                            $('#display_message').show();
                                            $('#display_message').addClass('alert alert-success');
                                            $('#display_message').html(t.message);
                                            $('.filter-cancel').click();
                                        }
                                    });
                                } else
                                {
                                    return false;
                                }
                            });
                });

                DataTableFun.init();
            });
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
