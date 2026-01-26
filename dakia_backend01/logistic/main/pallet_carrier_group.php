<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrier.class',
    'carrierfilter.class',
    'palletcariergroup.class',
    'palletcariergroupfilter.class',    
    'services.class',
    'servicefilter.class',
    'palletcarrierservice.class',
    'palletcarrierservicefilter.class',    
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Pallet Carrier Groups"
        );
        $user = SessionManager::getUser();
        /*
         * Get Ajax based serivce
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_service"){
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = trim($this->form_vars['service_id']);
            $serviceSelected = explode(',',$serviceId);
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addFieldFilter('carrier_id', $carrierId);
            $serviceFilter->addFieldFilter('user_account_id', $user->getUserAccountId());
            $serviceListObj = $serviceFilter->getInnerJoinList(['*', 'ser.id as id'], 'services ser', 'user_services_routing usr', 'ser.id = usr.service_id', 'GROUP BY ser.id');
            $serviceHtml = "";
//            $serviceSelected
            foreach ($serviceListObj as $serviceList) {
                $selected = "";
                if(in_array($serviceList->getId(), $serviceSelected))
                    $selected = "selected=selected";
                $serviceHtml .= '<option '.$selected.' value="'.$serviceList->getId().'">'.$serviceList->getName().' </option>';
            }
            echo $serviceHtml;
            die;
        }
        /*
        * Save Pallet carrier Service group
        */
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == "save") {
            $user = SessionManager::getUser();
            $carrierId = trim($this->form_vars['add_carrier_id']);
            $groupName = trim($this->form_vars['group_name']);
            $serviceList = $this->form_vars['services'];
            $palletcarrierGroupId = (int) trim($this->form_vars['id']);
            if(!$palletcarrierGroupId > 0)
                $palletcarrierGroupId = "";
            //Insert pallet group first
            $palletCarierGroup = new PalletCarierGroup($palletcarrierGroupId);
            $old_data['group_name'] = $palletCarierGroup->getGroupName();
            $palletCarierGroup->setGroupName($groupName);
            $palletCarierGroup->setCarrierId($carrierId);
            $palletCarierGroup->saveLog = false;
            $palletGroupObj = $palletCarierGroup->save();
            $palletGroupId = $palletCarierGroup->getId();
            $palletGroupServices = new PalletCarrierServiceFilter();
            $palletGroupServices->addFilter('carrier_group_id = ' . $palletcarrierGroupId);
            $palletGroupServices = $palletGroupServices->getList();
            $oldServicesName = [];
            if(!empty($palletGroupServices)) {
                foreach ($palletGroupServices as $palletGroupService) {
                    $serviceData = new Services($palletGroupService->getServiceId());
                    $oldServicesName[] = $serviceData->getName();
                }
            }
            if($palletGroupId > 0){
                //Delete already inserted record first
                PalletCarrierService::deleteByCarrierGroupId($palletGroupId);
                //Save pallet carreir group serivces
                $serviceNames = [];
                if(is_array($serviceList)){
                    foreach ($serviceList as $serviceId) {
                        $palletCarrierService = new PalletCarrierService();
                        $palletCarrierService->setCarrierGroupId($palletGroupId);
                        $palletCarrierService->setServiceId($serviceId);
                        $palletCarrierService->save();
                        $serviceData = new Services($serviceId);
                        $serviceNames[] = $serviceData->getName();

                    }
                    $old_data['services'] = $oldServicesName;
                    $new_data['services'] = $serviceNames;
                    $new_data['group_name'] = $groupName;
                    $palletCarierGroup->logMoreDataOld = $old_data;
                    $palletCarierGroup->logMoreDataNew = $new_data;
                    $palletCarierGroup->custom_message = $user->getFirstName() . ' ' . $user->getLastName() . " has updated pallet group " . $groupName;
                    $palletCarierGroup->saveAuditData();
/*                    $userAudit = new UserAudit();
                    $table_key = 0;
                    $userAudit->allowAdd = true;
                    $userAudit->insertAuditData('pallet_carrier_service', 'insert', $user->getFirstName() . ' ' . $user->getLastName(), $user->getId(), $new_data, $table_key, $old_data, $groupName . ' Carrier Services Updated By ' . $user->getFirstName() . ' ' . $user->getLastName());*/

                    $returnMsg['STATUS'] = "success";
                    echo json_encode($returnMsg);
                }
            }else{
                $returnMsg['STATUS'] = "error";
                $returnMsg['MESSAGE'] = "There is error occurred.";
                echo json_encode($returnMsg);
            }
            die;
        }
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "pallet_carrier_groups_ajax") {
            $user = SessionManager::getUser();
            $palletCarierGroupFilter = new PalletCarierGroupFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $carrierId = $this->form_vars['carrier_id'];
                if (!empty($carrierId))
                    $palletCarierGroupFilter->addFieldFilter('   pcg.carrier_id', $carrierId);

                $groupName = $this->form_vars['group_name'];
                if (!empty($groupName))
                    $palletCarierGroupFilter->addFieldLikeFilter('group_name', $groupName);
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
                $palletCarierGroupFilter->AddOrderBy(strtolower("rg." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $palletCarierGroupFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $palletCarierGroupFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $palletCarierGroupFilter->setOffset($iDisplayStart);
            $palletCarierGroupFilter->AddOrderBy("id");
            $palletCarierGroupObjs = $palletCarierGroupFilter->getPagingList();
            $setDataArr = array();
            foreach ($palletCarierGroupObjs as $palletCarierGroupObj) {
                $currentArr = array();
                $servicesHtml = "";
                $carrier = new Carrier($palletCarierGroupObj->getCarrierId());
                $palletCarrierServiceFilter = new PalletCarrierServiceFilter();
                $serviceNameObj = $palletCarrierServiceFilter->getServiceFromGroup($palletCarierGroupObj->getId());
                foreach ($serviceNameObj as $serviceName) {
                    $servicesHtml .= $serviceName->getServiceId().", ";
                }
                $currentArr['carrier_id'] = '<img src="../images/carrierlogo/thumbnail/owe_16_'.$carrier->getLogo().'" alt="" /> '.$carrier->getCarrier();
                $currentArr['group_name'] = $palletCarierGroupObj->getGroupName();
                $currentArr['service'] = rtrim($servicesHtml,', ');
                $currentArr['actions'] = "<a href='JavaScript:void(0);' data-group_id='" . $palletCarierGroupObj->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>".
                        "<a href='JavaScript:void(0);' data-group_id='" . $palletCarierGroupObj->getId() . "' class='btndelete btn btn-xs red btn-outline'><span class='fa fa-trash'></span> </a>" .
                "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $palletCarierGroupObj->getId() . "' 
                                            data-log_name='pallet_carier_group' data-toggle='modal' class='btn btn-xs blue btn-outline'> <i class='fa fa-list'></i>
                                            </a>";
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        /*
         * Manage Edit Functionality
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit") {
            $groupId = $this->form_vars['group_id'];
            if ($groupId > 0) {
                $palletCarierGroup = new PalletCarierGroup($groupId);
                $services = "";
                $returnMsg['STATUS'] = "success";
                $returnMsg['group_name'] = $palletCarierGroup->getGroupName();
                $returnMsg['carrier_id'] = $palletCarierGroup->getCarrierId();
                $returnMsg['group_id'] = $palletCarierGroup->getId();
                $palletCarrierServiceFilter = new PalletCarrierServiceFilter();
                $palletCarrierServiceFilter->addFieldFilter('    carrier_group_id', $palletCarierGroup->getId());
                $palletCarrierServiceobj = $palletCarrierServiceFilter->getList();
                foreach ($palletCarrierServiceobj as $palletCarrierService) {
                    $services .= $palletCarrierService->getServiceId().",";
                }
                $returnMsg['service'] = rtrim($services,',');
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
//        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
//             $user = SessionManager::getUser();
//            $carrierId = $this->form_vars['carrier_id_hidden'];
//            $csvObj = new RemoteareasGroups();
//            if (trim($carrierId) != '') {
//               $sql = "SELECT c.carrier AS carrier_id,rg.group_name FROM remoteareas_groups rg JOIN `carrier` c  ON rg.carrier_id = c.id WHERE  rg.carrier_id='" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted='N' ORDER BY rg.id ASC";
//                $res = $csvObj->getDataFromSql($sql);
//                $returnString = "Carrier Name, Group Name";
//                $fileName = "Remoteareas_group".time();
//                if (count($res) > 0) {
//                    foreach ($res as $resultData) {
//                        $returnString .= "\r\n";
//                        $returnString .= $resultData->getCarrierId() . ",";
//                        $returnString .= $resultData->getGroupName();
//                    }
//                }
//                    header("Content-type: text/csv");
//                    header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
//                    header("Pragma: no-cache");
//                    header("Expires: 0");
//                    echo $returnString;
//                    die;
//            }
//        }
//        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
//            $output = array();
//            $output['status'] = 'success';
//            $output['message'] = 'Uploaded successfully.';
//            $carrierId = $this->form_vars['carrier_id'];
//
//            @$csv_file = $_FILES['csv_file'];
//            if (!empty($csv_file['name'])) {
//                $file_name = $csv_file['name'];
//                $path_parts = pathinfo($file_name);
//                $ext = strtolower($path_parts['extension']);
//                $basename = $path_parts['basename'];
//                if ($ext == 'csv') {
//                    $user = SessionManager::getUser();
//                    $account = $user->getAccount();
//                    $new_file_name = $account . "_" . time() . "_" . $basename;
//                    $relPath = '../_assets/remoteareas_csv/'.$new_file_name;
//                    if (!file_exists("../_assets/remoteareas_csv/"))
//                    @mkdir("../_assets/remoteareas_csv/", 0775);
//                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
//                        $row = 1;
//                            if (($handle = fopen($relPath, "r")) !== FALSE) {
//                                $csvContent = '';
//                                $successRecords = 0;
//                                $errorRecords = 0;
//                                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//                                    if($row < 2){
//                                        $row++;
//                                        continue;
//                                    }
//                                    $carrierName = $data[0];
//                                    $groupName = $data[1];
//                                    $remoteareasGroupsCheck = new RemoteareasGroupsFilter();
//                                    $remoteareasGroupsCheck->addIsDeletedFilter();
//                                    $remoteareasGroupsCheck->addFilter("rg.carrier_id = '".DbAccess3::escape($carrierId)."' AND rg.group_name = '".DbAccess3::escape($groupName)."'");
//                                    $remoteareasGroupsCheckCount = $remoteareasGroupsCheck->getPagingCount();
//                                    if($remoteareasGroupsCheckCount == 0) {
//                                        $csvContent .= (!empty($csvContent) ? "\r\n" : "").$carrierId.",".$groupName;
//                                        $successRecords++;
//                                    }else{
//                                        $errorRecords++;
//                                    }
//                                    $row++;
//                                }
//                                fclose($handle);
//                                $CurFileName = "remotearea_group". time() .".csv";
//                                //Write File
//                                $CurFileContent = "../_assets/remoteareas_csv/".$CurFileName;
//                                if(!empty($csvContent)){
//                                    if(file_put_contents($CurFileContent,$csvContent)){
//                                        $output['file_name'] = $new_file_name;
//                                        $newDate = date('Y-m-d H:i:s');
//                                       $load_data_sql = "LOAD DATA LOCAL INFILE '../_assets/remoteareas_csv/".$CurFileName. "' INTO TABLE `remoteareas_groups` FIELDS ENCLOSED BY '\"' 
//                                            TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
//                                                              `carrier_id`,`group_name`					
//                                                            ) SET is_deleted = 'N' , added_by =  '" . $user->getId() . "' , added_date='".$newDate."'";
//                                        $output['sql'] = $load_data_sql;
//                                        $res = DbAccess3::runQueryWithError($load_data_sql);
//                                        if ($res === false) {
//                                            $error = DbAccess3::$dbError;
//                                            $output['message'] = $error[0];
//                                            $output['status'] = 'fail';
//                                        }else{
//                                            unlink("../_assets/remoteareas_csv/".$CurFileName);
//                                            $message = 'Uploaded successfully with';
//                                            $message .= '<br /> ' . $successRecords . ' Records imported successfully.';
//                                            $message .= '<br /> ' . $errorRecords . ' Records are invalid.';
//                                            $output['message'] = $message;
//                                        }
//                                    }
//                                }else{
//                                    $message = 'Uploaded successfully with';
//                                    $message .= '<br /> ' . $successRecords . ' Records imported successfully.';
//                                    $message .= '<br /> ' . $errorRecords . ' Records are invalid.';
//                                    $output['message'] = $message;
//                                }
//                            } 
//                    } else {
//                        $output['message'] = 'File upload fail.';
//                        $output['status'] = 'fail';
//                    }
//                } else {
//                    $output['message'] = 'Invalid CSV File.';
//                    $output['status'] = 'fail';
//                }
//            } else {
//                $output['message'] = 'No file found to import data.';
//                $output['status'] = 'fail';
//            }
//            echo json_encode($output);
//            exit;
//        }
        /*
         * manage Delete functrionality
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $user = SessionManager::getUser();
            $groupId = $this->form_vars['group_id'];
            if ($groupId > 0) {
                PalletCarrierService::deleteByCarrierGroupId($groupId);
                PalletCarierGroup::deleteById($groupId);
                $remoteareas = new RemoteareasGroups($remoteareasId);
                $oldRemoteareasGroupsData = serialize($remoteareas);
                $returnMsg['STATUS'] = "success";
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }
            die;
        }
//        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_remoteareas") {
//            $csvObj = new RemoteareasGroups();
//            $carrierId = 0;
//            if(!empty($this->form_vars['carrier_id']))
//                $carrierId = $this->form_vars['carrier_id'];
//            $sql = "SELECT count(c.carrier) AS carrier_id FROM remoteareas_groups rg JOIN `carrier` c  ON rg.carrier_id = c.id WHERE  rg.carrier_id='" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted='N' ORDER BY rg.id ASC";
//                $res = $csvObj->getDataFromSql($sql);
//                if($res[0]->getCarrierId() > 0){
//                    $output["status"] = "success";
//                    echo json_encode($output);
//                    die;
//                }else{
//                    $output["status"] = "error";
//                    echo json_encode($output);
//                    die;
//                }
//            die;   
//        }
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
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
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
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

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
                                "url": "pallet_carrier_group.php?action=pallet_carrier_groups_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "carrier_id"},
                                {"data": "group_name"},
                                {"data": "service"}
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
                $('.multiselect_drop_down').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function(ms){
                      var that = this,
                          $selectableSearch = that.$selectableUl.prev(),
                          $selectionSearch = that.$selectionUl.prev(),
                          selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                          selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
                      that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                      .on('keydown', function(e){
                        if (e.which === 40){
                          that.$selectableUl.focus();
                          return false;
                        }
                      });

                      that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                      .on('keydown', function(e){
                        if (e.which == 40){
                          that.$selectionUl.focus();
                          return false;
                        }
                      });
                    },
                    afterSelect: function(values){
                      this.qs1.cache();
                      this.qs2.cache();
//                      loadServiceRow(values);
                    },
                    afterDeselect: function(values){
                      this.qs1.cache();
                      this.qs2.cache();
//                      loadServiceRow(values);
                    }
                });
                DataTableFun.init();
//                $("#csv_download_btn").click(function () {
//                    $("#message_download_csv").hide();
//                    $('#csv_download').modal('show');
//                });
//                $("#download_csv").click(function () {
//                    var carrierId = $("#carrier_csv").val();
//                    $.ajax({
//                        type: "POST",
//                        url: "remoteareas_groups.php",
//                        data: {action: "check_download_remoteareas", carrier_id: carrierId},
//                        dataType: "json",
//                        success: function (data) {
//                            if (data.status == "success") {
//                                $("#carrier_id_hidden").val(carrierId);
//                                $("#hiddenForm").submit();
//                                $('#csv_download').modal('hide');    
//                            } else {
//                                $("#message_download_csv div.alert").html("There is no record found to download");
//                                $("#message_download_csv").show();
//                            }
//                        },
//                        error: function () {
//                            alert('error handing here');
//                        }
//                    });
//                });
//                $("#btnSubmitImport").click(function () {
//                    $("#file_in").val("");
//                    $("#csv_upload").modal('show');
//                });
//                $("#upload_csv").click(function () {
//                    $('#console_window').show();
//                    $('#console_window').html('');
//                    $('#console_window').html("Uploading CSV File....<br />");
//                    var file_data = $('#file_in').prop('files')[0];
//                    $('#csv_upload').modal('hide');
//                    var form_data = new FormData();
//                    var carrierId = "";
//                    carrierId = $("#carrier_csv_upload").val();
//                    form_data.append('csv_file', file_data);
//                    form_data.append('func', 'upload_csv_file');
//                    form_data.append('carrier_id', carrierId);
//                    $.ajax({
//                        url: "remoteareas_groups.php",
//                        dataType: 'json',
//                        cache: false,
//                        contentType: false,
//                        processData: false,
//                        data: form_data,
//                        type: 'post',
//                        success: function (response) {
//                            if (response.status == 'success') {
//                                $('#console_window').append(response.message);
//                                grid.getDataTable().ajax.reload();
//                            } else {
//                                $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
//                            }
//                        }
//                    });
//                    return false;
//                });
            });
            $('#btnSave').click(function () {
                if ($("#add_carrier_id").val() == "") {
                    swal("", "Please select Carrier name", "info");
                } else if ($.trim($("#group_name").val()) == "") {
                    swal("", "Please enter group Name", "info");
                } else if ($("#services option:selected").length == 0) {
                    swal("", "Please select service", "info");
                }else {
                    $("#form_action").val("save");
                    $.post("pallet_carrier_group.php", $("#adminForm").serialize(), function (response) {
                        $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                        if (response.STATUS == "success") {
                            $("#res_message div.alert").addClass('alert-success');
                            $("#res_message div.alert").html("Pallet carrier Group added successfully");
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                            $("#group_name").val("");
                            $("#add_carrier_id").val("").trigger('change');
                            $('#services').find('option').remove();
                            $('#services').multiSelect("refresh");
                        } else {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(response.MESSAGE);
                            $("#res_message").show();
                        }
                    }, "json");
                }
            });
            $(document).on('click', '.btndelete', function () {
                    var groupId = $(this).attr("data-group_id");
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
                        url: "pallet_carrier_group.php",
                        data: {action: "delete", group_id: groupId},
                        dataType: "json",
                        success: function (data) {
                            if (data.STATUS == "success") {
                                    $("#res_message div.alert").removeClass('alert-danger');
                                    $("#res_message div.alert").addClass('alert-success');
                                    $("#res_message div.alert").html("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
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
                    url: "pallet_carrier_group.php",
                    data: {action: "edit", group_id: groupId},
                    dataType: "json",
                    success: function (data) {
                        if (data.STATUS == "success") {
                            $("#service_id").val(data.service);
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
                        swal("","Some error occurred", "error");
                    }
                });
            });
            $('#add_carrier_id').change(function () {
                var carrierId = $(this).val();
                var serviceId = $("#service_id").val();
                $.ajax({
                    type: "POST",
                    url: "pallet_carrier_group.php",
                    data: {action: "get_service", carrier_id: carrierId,service_id:serviceId},
                    dataType: "html",
                    success: function (data) {
                        if (data) {
                            $("#services").html(data);
                            $('#services').multiSelect("refresh");
                        } else {
                            
                        }
                    },
                    error: function () {
                        swal("","Some error occurred", "error");
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
                        Add/Update Pallet Carrier Groups 
                    </div>
                    <div class="actions">
<!--                        <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                        <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i><?php echo Translation::GetCaption("IMPORT"); ?></a>-->
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="display-none" id="res_message">
                            <div class="col-md-12">
                                <div class="alert alert-success"></div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="row">
                        <div class="col-md-12">
                            <div  id="console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;">

                            </div>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <div class="first_form_col">
                                    <div class="form-group">
                                       
                                        <div class="has-float-label input-icon right"> 
                                            <div class="first_form_col">
                                            <?php echo Ddl::generateCarrierDDLWithImage('add_carrier_id', $search_Carrier_id, 'id', ' class="bs-select input-sm form-control form-filter" data-live-search="true"  data-show-subtext="true"'); ?> 
                                            <label>Carrier Name</label>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                   
                                     <div class="has-float-label input-icon right"> <i class="fa fa-shopping-cart"></i>
                                        <input type="text" name="group_name" id="group_name" required="required" title="Group Name" value="" class="form-control" placeholder='Group Name' />
                                         <label for="group_name">Group Name</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="col-md-12 accessibility-container form-group">
                                    <label>Services</label>

                                    <select name="services[]" id="services" multiple="multiple" class="multi-select multiselect_drop_down" title="Group" placeholder="Group" data-original-title="Group" >
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">  
                            <a id="btnSave"   href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>               
                            <a href="carrier.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="0" />
            <input type="hidden" name="form_action" id="form_action" value="" />
            <input type="hidden" name="service_id" id="service_id" value="" />
        </form>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Pallet Carrier Group List
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Carrier</th>
                            <th>Group Name</th>
                            <th>Service Name</th>
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
                            <td>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                 </div>

            </div>
        </div>
        <!--Model for CSV download-->
<!--        <div class="modal fade" id="csv_download" tabindex="-1" role="basic" aria-hidden="true">
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
                                        <?php //echo Ddl::generateCarrierDDLWithImage('carrier_csv', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>                                        
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
                 /.modal-content 
            </div>
             /.modal-dialog 
        </div>
        Model for CSV Upload
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select Carreir</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <?php //echo Ddl::generateCarrierDDLWithImage('carrier_csv_upload', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                    </div>
                </div>
                 /.modal-content 
            </div>
             /.modal-dialog 
        </div>-->
<!--        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="carrier_id_hidden" value="" id="carrier_id_hidden"/>
                <input type="hidden" name="action" value="download" />
            </form>
        </div>-->
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