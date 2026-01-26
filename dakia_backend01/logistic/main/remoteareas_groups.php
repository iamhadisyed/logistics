<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrier.class',
    'carrierfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
    'remoteareasgroupslog.class',
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Remoteareas Groups"
        );
        $user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "remoteareas_groups_ajax") {
            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
            $remoteareasGroupsFilter->addIsDeletedFilter();
            $remoteareasGroupsFilter->addCarrierJoin();
             /*
             * Column filter
             * For search
             */
             if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $carrierId = $this->form_vars['carrier_id'];
                if (!empty($carrierId))
                    $remoteareasGroupsFilter->addFieldFilter('   rg.carrier_id', $carrierId);
                $groupName = $this->form_vars['group_name'];
                if (!empty($groupName))
                    $remoteareasGroupsFilter->addFieldLikeFilter('group_name', $groupName);
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
                $remoteareasGroupsFilter->AddOrderBy(strtolower("rg." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $remoteareasGroupsFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $remoteareasGroupsFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $remoteareasGroupsFilter->setOffset($iDisplayStart);
            $remoteareasGroupsFilter->AddOrderBy("rg.id");
            $remoteareasGroupsObjs = $remoteareasGroupsFilter->getPagingList('rg.id,rg.group_name');
            $setDataArr = array();
            foreach ($remoteareasGroupsObjs as $remoteareasGroupsObj) {
                $currentArr = array();
                $currentArr['carrier_id'] = '<img src="../images/carrierlogo/thumbnail/owe_16_'.$remoteareasGroupsObj->getCarrierLogo().'" alt="" /> '.$remoteareasGroupsObj->getCarrierId();
                $currentArr['group_name'] = $remoteareasGroupsObj->getGroupName();
                $currentArr['actions'] = '<div class="btn-group">
                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">';
                if (Permissions::checkFilePermission('remote_area_group_edit')) {
                    $currentArr['actions'] .= '<li>
                    <a title="Edit" href="JavaScript:void(0);" data-group_id="' . $remoteareasGroupsObj->getId() . '" class="btnedit" >
                    <i class="fa fa-pencil"></i> Edit
                    </a>
                    </li>';
                }
                if (Permissions::checkFilePermission('remote_area_group_delete')) {
                    $currentArr['actions'] .= '<li>
                    <a title="Delete" href="JavaScript:void(0);" data-remoteareas_id="' . $remoteareasGroupsObj->getId() . '" class="btndelete" >
                    <i class="fa fa-trash"></i> Delete
                    </a>
                    </li>';
                }
                if (Permissions::checkFilePermission('remote_area_group_list')) {
                    $currentArr['actions'] .= '<li>
                    <a data-title="Remoteareas Groups" data-table="remoteareas_groups" data-container="audit_content"  data-id="'.$remoteareasGroupsObj->getId().'" id="btnAudit" href="javascript:;" class="show_audit"  title="audit" data-target="#audit-log" data-toggle="modal" >
                    <i class="fa fa-list"></i> List
                    </a>
                    </li>';
                }
                $currentArr['actions'] .= '<li>
                <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $remoteareasGroupsObj->getId() . '" 
                data-log_name="remoteareas_groups" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
                </a>
                </li>';
                $currentArr['actions'] .= '</ul> </div>';
//                        "<a href='services_view.php?id=" . $remoteareasGroupsObj->getId() . "' class='btn btn-xs blue btn-outline'><span class='fa fa-eye'></span> </a>"
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
            $user = SessionManager::getUser();
            $carrierId = $this->form_vars['add_carrier_id'];
            $groupName = $this->form_vars['group_name'];
            $remoteareasGroupsCheck = new RemoteareasGroupsFilter();
            $remoteareasGroupsCheck->addIsDeletedFilter();
            $remoteareasGroupsCheck->addFilter("rg.carrier_id = '".DbAccess3::escape($carrierId)."' AND rg.group_name = '".DbAccess3::escape($groupName)."'");
            $remoteareasGroupsCheckCount = $remoteareasGroupsCheck->getPagingCount();
            if($remoteareasGroupsCheckCount == 0) {
                $remoteareasGroups = new RemoteareasGroups();
                if (!empty($this->form_vars['id']) && $this->form_vars['id'] > 0) {
                    $remoteareasGroups = new RemoteareasGroups($this->form_vars['id']);
                    $oldData = new RemoteareasGroups($this->form_vars['id']);
                    $oldRemoteareasGroupsData = serialize($oldData);
                }
                $remoteareasGroups->setCarrierId($carrierId);
                $remoteareasGroups->setGroupName($groupName);
                $remoteareasGroups->setAddedBy($user->getId());
                $remoteareasGroups->setAddedDate(time());
                $remoteareasGroups->setIsDeleted('N');
                $remoteareasGroups->save();
                $newData = $remoteareasGroups;
                /*
                * Add Remoteareas Groups Log details
                */
                $remoteareasGroupsLog = new RemoteareasGroupsLog();
                $newRemoteareasGroupsData = serialize($newData);
                if ((int)$this->form_vars['id'] <= 0) {
                    $remoteareasGroupsLog->createlog($user->getId(), '', $remoteareasGroups->getId(), 'REMOTEAREAS', $user->getUserName() . ' has added new remoteareas groups ' . $groupName, '', $newRemoteareasGroupsData);
                } else {
                    $remoteareasGroupsLog->createlog($user->getId(), '', $remoteareasGroups->getId(), 'REMOTEAREAS', $user->getUserName() . ' has updated ' . $groupName, $oldRemoteareasGroupsData, $newRemoteareasGroupsData);
                }
                $returnMsg['STATUS'] = "success";
                echo json_encode($returnMsg);
            }else{
                $returnMsg['STATUS'] = "error";
                $returnMsg['MESSAGE'] = formatMessages(ERROR_GROUP_ASSIGNED);//"This carrier already has this group.";
                echo json_encode($returnMsg);
            }
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit") {
            $groupId = $this->form_vars['group_id'];
            if ($groupId > 0) {
                $remoteareasGroups = new RemoteareasGroups($groupId);
                $returnMsg['STATUS'] = "success";
                $returnMsg['group_name'] = $remoteareasGroups->getGroupName();
                $returnMsg['carrier_id'] = $remoteareasGroups->getCarrierId();
                $returnMsg['group_id'] = $remoteareasGroups->getId();
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }
            die;
        }
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
           $user = SessionManager::getUser();
           $carrierId = $this->form_vars['carrier_id_hidden'];
           $csvObj = new RemoteareasGroups();
           if (trim($carrierId) != '') {
            if ($carrierId == 'download_template') {
                $sql = "SELECT c.carrier AS carrier_id FROM`carrier` c   ORDER BY carrier ASC";
                $res = $csvObj->getDataFromSql($sql);
            } else {
                $sql = "SELECT c.carrier AS carrier_id,rg.group_name FROM remoteareas_groups rg JOIN `carrier` c  ON rg.carrier_id = c.id WHERE  rg.carrier_id='" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted='N' ORDER BY rg.id ASC";
                $res = $csvObj->getDataFromSql($sql);
            }
            $returnString = "Carrier Name, Group Name";
            if ($carrierId == 'download_template') {
                $fileName = "Remoteareas_group_sample" . time();
            } else {
                $fileName = "Remoteareas_group" . time();
            }
            if (count($res) > 0) {
                foreach ($res as $resultData) {
                    $returnString .= "\r\n";
                    $returnString .= cleanCsvCall($resultData->getCarrierId()) . ",";
                    $returnString .= cleanCsvCall($resultData->getGroupName());
                }
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }
    }
    if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
        $output = array();
        $output['status'] = 'success';
        $output['message'] = formatMessages(SUCCESS_UPLOADED);
        $carrierId = $this->form_vars['carrier_id'];
        @$csv_file = $_FILES['csv_file'];
        if (!empty($csv_file['name'])) {
            $file_name = $csv_file['name'];
            $path_parts = pathinfo($file_name);
            $ext = strtolower($path_parts['extension']);
            $basename = $path_parts['basename'];
            if ($ext == 'csv') {
                $user = SessionManager::getUser();
                $account = $user->getAccount();
                $new_file_name = $account . "_" . time() . "_" . $basename;
                $relPath = '../_assets/remoteareas_csv/'.$new_file_name;
                if (!file_exists("../_assets/remoteareas_csv/"))
                    @mkdir("../_assets/remoteareas_csv/", 0775);
                if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                    $row = 1;
                    $oldcarrierName = '';
                    if (($handle = fopen($relPath, "r")) !== FALSE) {
                        $csvContent = '';
                        $successRecords = 0;
                        $errorRecords = 0;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if($row < 2){
                             if( count($data) !=2 )
                             {
                                $output['message'] = "Please check your CSV, it should only have 2 columns, Carrier name and Zone Name";
                                $output['status'] = 'fail';
                                break;
                            }
                            $row++;
                            continue;
                        }
                        $carrierName = $data[0];
                        $groupName = $data[1];
                        if(trim($carrierName)== '')
                        {
                            $output['message'] = " Carrier name column cannot be blank";
                            $output['status'] = 'fail';
                            break;
                        }
                        if(trim($groupName)== '')
                        {
                            $output['message'] = " Group name column cannot be blank";
                            $output['status'] = 'fail';
                            break;
                        }
                        if ($oldcarrierName != $carrierName) {
                                    /*
                                     * Get carrier id from name 
                                     */
                                    $carrierFilter = new CarrierFilter();
                                    $carrierFilter->addFilter(' status= 1 and UPPER(TRIM(carrier)) = UPPER(TRIM("' . trim(DbAccess3::escape($carrierName)) . '"))');
                                    $carrierFilterData = $carrierFilter->getColumnList("id");
                                    if (count($carrierFilterData) > 0) {
                                        $carrier_id = $carrierFilterData[0]->getId();
                                    } else {
                                        $output['message'] = $carrierName . " Carrier name is invalid, Please check the name and try again";
                                        $output['status'] = 'fail';
                                        break;
                                    }
                                }
                                if($carrier_id != $carrierId){
                                    $output['message'] = $carrierName . " Carrier name is invalid, Please check the name and try again";
                                    $output['status'] = 'fail';
                                    break;
                                }
                                $oldcarrierName = $carrierName;   
                                $remoteareasGroupsCheck = new RemoteareasGroupsFilter();
                                $remoteareasGroupsCheck->addIsDeletedFilter();
                                $remoteareasGroupsCheck->addFilter("rg.carrier_id = '".DbAccess3::escape($carrierId)."' AND rg.group_name = '".DbAccess3::escape($groupName)."'");
                                $remoteareasGroupsCheckCount = $remoteareasGroupsCheck->getPagingCount();
                                if($remoteareasGroupsCheckCount == 0) {
                                    $csvContent .= (!empty($csvContent) ? "\r\n" : "").$carrierId.",".$groupName;
                                    $successRecords++;
                                }else{
                                    $errorRecords++;
                                }
                                $row++;
                            }
                            fclose($handle);
                            $CurFileName = "remotearea_group". time() .".csv";
                                //Write File
                            $CurFileContent = "../_assets/remoteareas_csv/".$CurFileName;
                            if($output['status']!='fail')
                            {
                                if(!empty($csvContent)){
                                    if(file_put_contents($CurFileContent,$csvContent)){
                                        $output['file_name'] = $new_file_name;
                                        $newDate = date('Y-m-d H:i:s');
                                        $load_data_sql = "LOAD DATA LOCAL INFILE '"._ASSETS_PATH."remoteareas_csv/".$CurFileName. "' INTO TABLE `remoteareas_groups` FIELDS ENCLOSED BY '\"' 
                                        TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
                                        `carrier_id`,`group_name`                 
                                    ) SET is_deleted = 'N' , added_by =  '" . $user->getId() . "' , added_date='".$newDate."'";
                                    $output['sql'] = $load_data_sql;
                                    $res = DbAccess3::runQueryWithError($load_data_sql);
                                    if ($res === false) {
                                        $error = DbAccess3::$dbError;
                                        $output['message'] = $error[0];
                                        $output['status'] = 'fail';
                                    }else{
                                        unlink("../_assets/remoteareas_csv/".$CurFileName);
                                        $message = formatMessages(SUCCESS_UPLOADED).'with';
                                        $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD);
                                               // $message .= '<br /> ' . $errorRecords . formatMessages(ERROR_INVALID_RECORD);//'Records are invalid.';
                                        $output['message'] = $message;
                                    }
                                }
                            }else{
                                $message = formatMessages(SUCCESS_UPLOADED).'with';
                                $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD);
                                $output['message'] = $message;
                            }
                        }
                    } 
                } else {
                    $output['message'] = formatMessages(ERROR_FILE_UPLOADED);
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = formatMessages(ERROR_INVALID_FILE);
                $output['status'] = 'fail';
            }
        } else {
            $output['message'] = formatMessages(ERROR_FILE_EMPTY);
            $output['status'] = 'fail';
        }
        echo json_encode($output);
        exit;
    }
    if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
        $user = SessionManager::getUser();
        $remoteareasId = $this->form_vars['remoteareas_group_id'];
        if ($remoteareasId > 0) {
            $remoteareas = new RemoteareasGroups($remoteareasId);
            $oldRemoteareasGroupsData = serialize($remoteareas);
            $returnMsg['STATUS'] = "success";
            $remoteareas->setUpdatedBy($user->getId());
            $remoteareas->setUpdatedDate(time());
            $remoteareas->setIsDeleted("Y");
            $remoteareas->save();
                /*
                * Add Remoteareas Log details
                */
                $remoteareasGroupsLog = new RemoteareasGroupsLog();
                $newRemoteareasGroupsData = serialize($remoteareas);
                $remoteareasGroupsLog->createlog($user->getId(),'',$remoteareasId,'REMOTEAREAS_GROUPS',$user->getUserName() . ' has deleted ' . $remoteareasId,$oldRemoteareasGroupsData, $newRemoteareasGroupsData);
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_remoteareas") {
            $csvObj = new RemoteareasGroups();
            $carrierId = 0;
            if(!empty($this->form_vars['carrier_id']))
                $carrierId = $this->form_vars['carrier_id'];
            $sql = "SELECT count(c.carrier) AS carrier_id FROM remoteareas_groups rg JOIN `carrier` c  ON rg.carrier_id = c.id WHERE  rg.carrier_id='" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted='N' ORDER BY rg.id ASC";
            $res = $csvObj->getDataFromSql($sql);
            if($res[0]->getCarrierId() > 0){
                $output["status"] = "success";
                echo json_encode($output);
                die;
            }else{
                $output["status"] = "error";
                echo json_encode($output);
                die;
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
                                "url": "remoteareas_groups.php?action=remoteareas_groups_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                            {"data": "actions", "bSortable": false},
                            {"data": "carrier_id"},
                            {"data": "group_name"}
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
                        url: "remoteareas_groups.php",
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
                $("#download_csv_btn").click(function () {
                   var groupId = 'download_template';
                   $("#carrier_id_hidden").val('download_template');
                   $("#hiddenForm").submit();
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
                        url: "remoteareas_groups.php",
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
                    $.post("remoteareas_groups.php", $("#adminForm").serialize(), function (response) {
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
                            url: "remoteareas_groups.php",
                            data: {action: "delete", remoteareas_group_id: remoteareasId},
                            dataType: "json",
                            success: function (data) {
                                if (data.STATUS == "success") {
                                    $("#res_message").text("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
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
                var groupId = $(this).attr("data-group_id");
                $.ajax({
                    type: "POST",
                    url: "remoteareas_groups.php",
                    data: {action: "edit", group_id: groupId},
                    dataType: "json",
                    success: function (data) {
                        if (data.STATUS == "success") {
                            $("#add_carrier_id").val(data.carrier_id).trigger('change');
                            $("#group_name").val(data.group_name);
                            $("#id").val(data.group_id);
                            $(".scroll-to-top").click();
                        } else {
                        }
                        //var obj = jQuery.parseJSON(data); 
                        //if the dataType is not specified as json uncomment this
                        // do what ever you want with the server response
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
                        Add/Update Remote Areas Groups
                    </div>
                    <div class="actions">
                        <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                        <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i><?php echo Translation::GetCaption("IMPORT"); ?></a>
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
                            <div  id="console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="first_form_col">
                                <div class="form-group">
                                   <div class="has-float-label"> 
                                    <?php echo Ddl::generateCarrierDDLWithImage('add_carrier_id', $search_Carrier_id, 'id', ' class="bs-select input-sm form-control form-filter" data-live-search="true"  data-show-subtext="true"'); ?>   <label>Carrier Name</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> 
                         <div class="has-float-label input-icon right"> 
                             <i class="fa fa-shopping-cart"></i>
                             <input type="text" name="group_name" id="group_name" required="required" title="Group Name" value="" class="form-control" placeholder='Group Name' />
                             <label for="group_name">Group Name</label>
                         </div>
                     </div>
                 </div>
                 <div style="clear:both;">  </div>          
                 <div class="col-md-12 text-center">  
                    <a id="btnSave"   href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>               
                    <a href="remoteareas_groups.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" id="id" value="<?php echo !empty($this->form_vars["id"]) ? (int) $this->form_vars["id"] : 0; ?>" />
    <input type="hidden" name="form_action" id="form_action" value="" />
</form>
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption"> <i class="fa fa-list"></i>
            Services List
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
                    <th>Carrier</th>
                    <th>Group Name</th>
                </tr>
                <tr role="row" class="filter">
                    <td>
                        <div class="margin-bottom-5">
                            <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                            <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                        </div>
                    </td>
                    <td class="user_acccount_correct_button">
                        <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $search_Carrier_id, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                    </td>
                    <td>
                        <input type="text" class="form-control form-filter input-xs" name="group_name" id ="search_Name" />
                    </td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!--Model for CSV download-->
<div class="modal fade" id="csv_download" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Select Carrier</h4>
            </div>
            <div class="modal-body">
                <div class="row" id="message_download_csv" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                <?php echo Ddl::generateCarrierDDLWithImage('carrier_csv', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>                                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                <button type="button" id="download_csv" class="btn green">Download</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--Model for CSV Upload-->
<div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Import RemoteArea Groups</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                <?php echo Ddl::generateCarrierDDLWithImage('carrier_csv_upload', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="input-group input-sm">
                                    <div class="form-control uneditable-input input-fixed input-sm" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                        <span class="fileinput-filename"> </span>
                                    </div>
                                    <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Select file </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="file_in" id="file_in">
                                    </span>
                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
               <button type="button" id="download_csv_btn" class="btn btn-primary">  <i class="fa fa-download"></i> Download Template </button>
               <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
               <button type="button" id="upload_csv" class="btn green">Upload</button>
           </div>
       </div>
       <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<div id="hidden_frm" style="display: none;">
    <form name="hiddenForm" id="hiddenForm" action="" method="POST">
        <input type="hidden" name="carrier_id_hidden" value="" id="carrier_id_hidden"/>
        <input type="hidden" name="action" value="download" />
    </form>
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