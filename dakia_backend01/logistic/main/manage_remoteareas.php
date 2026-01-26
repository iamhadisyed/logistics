<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'remoteareaslog.class',
    'remoteareaslogfilter.class',
    
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
    
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Manage Remoteareas"
        );
        $user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "remoteareas_ajax") {
            $remoteareasFilter = new RemoteareasFilter();
            $remoteareasFilter->addIsDeletedFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $remoteareasGroupsId = $this->form_vars['remoteareas_groups_id'];
                if (!empty($remoteareasGroupsId))
                    $remoteareasFilter->addFieldFilter('remoteareas_groups_id', $remoteareasGroupsId);

                $countryId = $this->form_vars['country_id'];
                if (!empty($countryId))
                    $remoteareasFilter->addFieldFilter('country_id', $countryId);

                $fromPostcode = $this->form_vars['from_postcode'];
                if (!empty($fromPostcode))
                    $remoteareasFilter->addFieldLikeFilter('from_postcode', $fromPostcode);

                $toPostcode = $this->form_vars['to_postcode'];
                if (!empty($toPostcode))
                    $remoteareasFilter->addFieldLikeFilter('to_postcode', $toPostcode);

                $city = $this->form_vars['city'];
                if (!empty($city))
                    $remoteareasFilter->addFieldLikeFilter('city', $city);
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
                $remoteareasFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $remoteareasFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $remoteareasFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $remoteareasFilter->setOffset($iDisplayStart);
            $remoteareasObjs = $remoteareasFilter->getPagingList();
            $setDataArr = array();

            foreach ($remoteareasObjs as $remoteareasObj) {
                $currentArr = array();
                $remoteareasGroups = new RemoteareasGroups($remoteareasObj->getRemoteareasGroupsId());
                $countryObj = new Country($remoteareasObj->getCountryId());
                $currentArr['remoteareas_groups_id'] = $remoteareasGroups->getGroupName();
                $currentArr['country_id'] = '<img src="../assets/global/img/flags/' . strtolower($countryObj->getIso()) . '.png" title="' . $countryObj->getName() . '" alt=""> ' . $countryObj->getName();
                $currentArr['from_postcode'] = $remoteareasObj->getFromPostcode();
                $currentArr['to_postcode'] = $remoteareasObj->getToPostcode();
                $currentArr['city'] = $remoteareasObj->getCity();
//                $currentArr['actions'] = "<a href='javaScript:void(0);' data-remoteareas_id='" . $remoteareasObj->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>" .
//                        "<a href='javaScript:void(0);' data-remoteareas_id='" . $remoteareasObj->getId() . "' class='btndelete btn btn-xs red btn-outline'><span class='fa fa-trash'></span> </a>" .
//                        '<a data-title="remoteareas" data-table="remoteareas" data-container="audit_content"  data-id="' . $remoteareasObj->getId() . '" id="btnAudit" href="javascript:;" class="btn btn-xs btn-default blue btn-outline show_audit"  title="audit" data-target="#audit-log" data-toggle="modal" >
//                        <span class="fa fa-list"></span>
//                        </a>';
                $currentArr['actions'] = '<div class="btn-group">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">';
              
                 
                if (Permissions::checkFilePermission('manage_remote_area_edit')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Edit" href="javaScript:void(0);" data-remoteareas_id="' . $remoteareasObj->getId() . '" class="btnedit" >
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                </li>';
                }
                if (Permissions::checkFilePermission('manage_remote_area_delete')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Delete" href="javaScript:void(0);" data-remoteareas_id="' . $remoteareasObj->getId() . '" class="btndelete" >
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </li>';
                }
                /*if (Permissions::checkFilePermission('manage_remote_area_audit')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Audit" data-title="remoteareas" data-table="remoteareas" data-container="audit_content"  data-id="' . $remoteareasObj->getId() . '" id="btnAudit" href="javascript:;" class="show_audit" data-target="#audit-log" data-toggle="modal" >
                                                        <i class="fa fa-list"></i> Audit
                                                    </a>
                                                </li>';
                }*/
                $currentArr['actions'] .= '<li>
                                         <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $remoteareasObj->getId() . '" 
                                         data-log_name="remoteareas" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
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
            $groupId = $this->form_vars['group_name'];
            $countryId = $this->form_vars['country_name'];
            $fromPostCode = $this->form_vars['from_post_code'];
            $toPostCode = $this->form_vars['to_post_code'];
            $cityName = $this->form_vars['city_name'];
            $remoteareas = new Remoteareas();
            if (!empty($this->form_vars['id']) && $this->form_vars['id'] > 0) {
                $remoteareas = new Remoteareas($this->form_vars['id']);
                $oldData = new Remoteareas($this->form_vars['id']);
                $oldRemoteareasData = serialize($oldData);
            }
            $remoteareas->setRemoteareasGroupsId($groupId);
            $remoteareas->setCountryId($countryId);
            $remoteareas->setFromPostcode($fromPostCode);
            $remoteareas->setToPostcode($toPostCode);
            $remoteareas->setCity($cityName);
            $remoteareas->setAddedBy($user->getId());
            $remoteareas->setAddedDate(time());
            $remoteareas->setIsDeleted('N');
            $remoteareas->save();
            $newData = $remoteareas;
            $remoteareasGroup = new RemoteareasGroups($groupId);
            /*
             * Add Remoteareas Log details
             */
            $remoteareasLog = new RemoteareasLog();
            $newRemoteareasData = serialize($newData);
            if ((int) $this->form_vars['id'] <= 0) {
                $remoteareasLog->createlog($user->getId(), '', $remoteareas->getId(), 'REMOTEAREAS', $user->getUserName() . ' has added new remoteareas ' . $remoteareasGroup->getGroupName(), '', $newRemoteareasData);
            } else {
                $remoteareasLog->createlog($user->getId(), '', $remoteareas->getId(), 'REMOTEAREAS', $user->getUserName() . ' has updated ' . $remoteareasGroup->getGroupName(), $oldRemoteareasData, $newRemoteareasData);
            }
            $returnMsg['STATUS'] = "success";
            echo json_encode($returnMsg);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit") {
            $remoteareasId = $this->form_vars['remoteareas_id'];
            if ($remoteareasId > 0) {
                $remoteareas = new Remoteareas($remoteareasId);
                $returnMsg['STATUS'] = "success";
                $returnMsg['remoteareas_groups_id'] = $remoteareas->getRemoteareasGroupsId();
                $returnMsg['country_id'] = $remoteareas->getCountryId();
                $returnMsg['from_postcode'] = $remoteareas->getFromPostcode();
                $returnMsg['to_postcode'] = $remoteareas->getToPostcode();
                $returnMsg['city'] = $remoteareas->getCity();
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $remoteareasId = $this->form_vars['remoteareas_id'];
            if ($remoteareasId > 0) {
                $remoteareas = new Remoteareas($remoteareasId);
                $oldRemoteareasData = serialize($remoteareas);
                $returnMsg['STATUS'] = "success";
                $remoteareas->setUpdatedBy($user->getId());
                $remoteareas->setUpdatedDate(time());
                $remoteareas->setIsDeleted("Y");
                $remoteareas->save();
                /*
                 * Add Remoteareas Log details
                 */
                $remoteareasLog = new RemoteareasLog();
                $newRemoteareasData = serialize($remoteareas);
                $remoteareasLog->createlog($user->getId(), '', $remoteareas->getId(), 'REMOTEAREAS', $user->getUserName() . ' has deleted ' . $remoteareasId, $oldRemoteareasData, $newRemoteareasData);
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
        //Handle Import csv
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = formatMessages(SUCCESS_UPLOADED);
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
                    $relPath = '../_assets/remoteareas_csv/' . $new_file_name;
                    if (!file_exists("../_assets/remoteareas_csv/"))
                        @mkdir("../_assets/remoteareas_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {

                        $row = 1;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 2) {
                                    $row++;
                                    continue;
                                }
                                $groupName = $data[0];
                                $country = $data[1];
                                $fromPostcode = $data[2];
                                $toPostcode = $data[3];
                                $cityName = $data[4];

                                $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                                $remoteareasGroupsFilter->addFilter("    group_name = '" . DbAccess3::escape($groupName) . "'");
                                $remoteareasGroupsResult = $remoteareasGroupsFilter->getColumnList("id");

                                if (count($remoteareasGroupsResult) > 0) {
                                    $countryFilter = new CountryFilter();
                                    $countryFilter->addFilter("     iso = '" . DbAccess3::escape($country) . "'");
                                    $countryFilterResult = $countryFilter->getColumnList("id");
                                    if (count($countryFilterResult) > 0) {
                                        $countryId = $countryFilterResult[0]->getId();

                                        $groupId = $remoteareasGroupsResult[0]->getId();

                                        $csvContent .= (!empty($csvContent) ? "\r\n" : "") . $groupId . "," . $countryId . "," . $fromPostcode . "," . $toPostcode . "," . $cityName;
                                    }
                                }
                                $row++;
                            }
                            fclose($handle);
                            $CurFileName = "remoteareas" . time() . ".csv";
                            //Write File
                            $CurFileContent = "../_assets/remoteareas_csv/" . $CurFileName;
                            if (file_put_contents($CurFileContent, $csvContent)) {
                                $output['file_name'] = $new_file_name;
                                $newDate = date('Y-m-d H:i:s');
                                $load_data_sql = "LOAD DATA LOCAL INFILE '../_assets/remoteareas_csv/" . $CurFileName . "' INTO TABLE `remoteareas` FIELDS ENCLOSED BY '\"' 
                                        TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
                                                          `remoteareas_groups_id`,`country_id`,`from_postcode`,`to_postcode`,`city`    					
                                                        ) SET is_deleted = 'N' , added_by =  '" . $user->getId() . "' , added_date='" . $newDate . "'";
                                $output['sql'] = $load_data_sql;
                                $res = DbAccess3::runQueryWithError($load_data_sql);
                                if ($res === false) {
                                    $error = DbAccess3::$dbError;
                                    $output['message'] = $error[0];
                                    $output['status'] = 'fail';
                                } else {
                                    unlink("../_assets/remoteareas_csv/" . $CurFileName);
                                }
                            }
                        }
                    } else {
                        $output['message'] = formatMessages(ERROR_FILE_UPLOADED); //'File upload fail.';
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
        //Handle Export CSV          
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_csv_frm') {
            $optionValue = $this->form_vars['option_value'];
            if (!empty($optionValue)) {
                $csvObj = new Remoteareas();
                if ($optionValue == "all") {
                    $sql = "SELECT  rg.group_name AS remoteareas_groups_id, c.iso AS country_id, r.from_postcode, r.to_postcode, r.city, r.id FROM `remoteareas` r  LEFT JOIN `remoteareas_groups` rg  ON r.remoteareas_groups_id = rg.id  LEFT JOIN `country` c  ON r.country_id = c.id WHERE rg.is_deleted = 'N'";
                } 
                else if ($optionValue == "download_template") {
                    $sql = "SELECT  rg.group_name AS remoteareas_groups_id, c.iso AS country_id, r.from_postcode, r.to_postcode, r.city, r.id FROM `remoteareas` r  LEFT JOIN `remoteareas_groups` rg  ON r.remoteareas_groups_id = rg.id  LEFT JOIN `country` c  ON r.country_id = c.id WHERE rg.is_deleted = 'N' GROUP BY rg.group_name";
                } 
                else {
                    $sql = "SELECT  rg.group_name AS remoteareas_groups_id, c.iso AS country_id, r.from_postcode, r.to_postcode, r.city, r.id FROM `remoteareas` r  LEFT JOIN `remoteareas_groups` rg  ON r.remoteareas_groups_id = rg.id  LEFT JOIN `country` c  ON r.country_id = c.id  WHERE rg.id = '" . DbAccess3::escape($optionValue) . "' AND rg.is_deleted = 'N'";
                }
                $res = $csvObj->getDataFromSql($sql);
                if (count($res) > 0) {
                    $returnString = "Group Name,Country ISO,From Postcode,To Postcode,City";
                    $fileName = "remotearea_" . time();
                    foreach ($res as $resultData) {
                        $returnString .= "\r\n";
                        $returnString .= cleanCsvCall($resultData->getRemoteareasGroupsId()) . ",";
                        
                         if ($optionValue == "download_template") {
                                $returnString .=  ",";
                                $returnString .=  ",";
                                $returnString .=   ",";
                                $returnString .= "";
                         }else{
                                $returnString .= cleanCsvCall($resultData->getCountryId()) . ",";
                                $returnString .= cleanCsvCall($resultData->getFromPostcode()) . ",";
                                $returnString .= cleanCsvCall($resultData->getToPostcode()) . ",";
                                $returnString .= cleanCsvCall($resultData->getCity());
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
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_remoteareas") {
            $csvObj = new Remoteareas();
            $group_id = "";
            if (!empty($this->form_vars['group_id']))
                $group_id = $this->form_vars['group_id'];
            if ($group_id == "all") {
                $sql = "SELECT  COUNT(rg.group_name) AS remoteareas_groups_id FROM `remoteareas` r  LEFT JOIN `remoteareas_groups` rg  ON r.remoteareas_groups_id = rg.id  LEFT JOIN `country` c  ON r.country_id = c.id WHERE rg.is_deleted='N'";
            } else {
                $sql = "SELECT  COUNT(rg.group_name) AS remoteareas_groups_id FROM `remoteareas` r  LEFT JOIN `remoteareas_groups` rg  ON r.remoteareas_groups_id = rg.id  LEFT JOIN `country` c  ON r.country_id = c.id  WHERE rg.id = '" . DbAccess3::escape($group_id) . "' AND rg.is_deleted = 'N'";
            }
            $res = $csvObj->getDataFromSql($sql);
            if ($res[0]->getRemoteareasGroupsId() > 0) {
                $output["status"] = "success";
                echo json_encode($output);
                die;
            } else {
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
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
                                "url": "manage_remoteareas.php?action=remoteareas_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "remoteareas_groups_id"},
                                {"data": "country_id"},
                                {"data": "from_postcode"},
                                {"data": "to_postcode"},
                                {"data": "city"}
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
                $(document).on('click', '#btnSubmitImport', function () {
                    $("#file_in").val("");
                    $("#csv_upload").modal('show');
                });
                $(document).on('click', '#upload_csv', function () {
                    $('#console_window').show();
                    $('#console_window').html('');
                    $('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#file_in').prop('files')[0];
                    $('#csv_upload').modal('hide');
                    var form_data = new FormData();
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: "manage_remoteareas.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#console_window').append('Data Import successfully<br />');
                                grid.getDataTable().ajax.reload();
                            } else {
                                $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                            }
                        }
                    });
                    return false;
                });
                $("#csv_download_btn").click(function () {
                    $("#message_download_csv").hide();
                    $('#csv_download').modal('show');
                });
                $("#download_csv").click(function () {
                    var groupId = $("#group_name_download").val();
                    $.ajax({
                        type: "POST",
                        url: "manage_remoteareas.php",
                        data: {action: "check_download_remoteareas", group_id: groupId},
                        dataType: "json",
                        success: function (data) {
                            if (data.status == "success") {
                                $("#option_value").val(groupId);
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
                     $("#option_value").val(groupId);
                     $("#hiddenForm").submit();
                });
                
            });
            //form submit start here
            //Form validation Start
        //                var form = $('#adminForm');
        //                var error = $('.alert-danger', form);
        //                var success = $('.alert-success', form);
        //                form.validate({
        //                    doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
        //                    errorElement: 'span', //default input error message container
        //                    errorClass: 'help-block help-block-error', // default input error message class
        //                    focusInvalid: false, // do not focus the last invalid input
        //                    rules: {
        //                        //account
        //                        group_name: {
        //                            required: true
        //                        },
        //                        country_name: {
        //                            required: true
        //                        },
        //                        from_post_code: {
        //                            required: true
        //                        },
        //                        //profile
        //                        to_post_code: {
        //                            required: true
        //                        },
        //                        city_name: {
        //                            required: true
        //                        }
        //                    },
        //
        //                    messages: { // custom messages for radio buttons and checkboxes
        //                        'group_name': {
        //                            required: "Please select group name"
        //                        },
        //                        'country_name': {
        //                            required: "Please select country name"
        //                        },
        //                        'from_post_code': {
        //                            required: "Please enter from post code"
        //                        },
        //                        'to_post_code': {
        //                            required: "Please enter to postcode"
        //                        },
        //                        'city_name': {
        //                            required: "Please enter city name"
        //                        }
        //                    },
        //
        //                    errorPlacement: function (error, element) { // render error placement for each input type
        ////                        if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
        ////                            error.insertAfter("#form_gender_error");
        ////                        } else if (element.attr("name") == "payment[]") { // for uniform checkboxes, insert the after the given container
        ////                            error.insertAfter("#form_payment_error");
        ////                        } else {
        ////                            error.insertAfter(element); // for other inputs, just perform default behavior
        ////                        }
        //                          $("#res_message").show();
        //                          error.insertAfter("#res_message");
        //                    },
        //
        //                    invalidHandler: function (event, validator) { //display error alert on form submit   
        //                        success.hide();
        //                        error.show();
        //                        App.scrollTo(error, -200);
        //                    },
        //
        //                    highlight: function (element) { // hightlight error inputs
        //                        $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
        //                    },
        //
        //                    unhighlight: function (element) { // revert the change done by hightlight
        //                        $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
        //                    },
        //
        //                    success: function (label) {
        //                        label
        //                            .addClass('valid') // mark the current input as valid and display OK icon
        //                            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
        ////                        if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
        ////                            label.closest('.form-group').removeClass('has-error').addClass('has-success');
        ////                            label.remove(); // remove error label here
        ////                        } else { // display success icon for other inputs
        ////                            label
        ////                                .addClass('valid') // mark the current input as valid and display OK icon
        ////                                .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
        ////                        }
        //                    },
        //
        //                    submitHandler: function (form) {
        //                        success.show();
        //                        error.hide();
        ////                        form[0].submit();
        //                        $("#form_action").val("save");
        //                        $("#form_action").val("save");
        //                        $.post("manage_remoteareas.php", $("#adminForm").serialize(), function (response) {
        //                            if (response.STATUS == "success") {
        //                                $("#res_message").html("Remoteareas added successfully");
        //                                $("#res_message").show();
        //                                $('#group_name').val("");
        //                                $('#group_name').selectpicker('refresh');
        //                                $('#country_name').val("");
        //                                $('#country_name').selectpicker('refresh');
        //                                $("#from_post_code").val("");
        //                                $("#to_post_code").val("");
        //                                $("#city_name").val("");
        //                                $("#add_carrier_id").val("");
        //                                $("#id").val("");
        //                                grid.getDataTable().ajax.reload();
        //                            } else {
        //            //                            $("#abc").html(response.message);
        //                            }
        //                        }, "json");
        //                        //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
        //                    }
        //
        //                });
        ////                Form Validation End
        //                $("#adminForm #btnSaveConsignment").click(function() {
        //                    $('#adminForm').valid();
        //                });
        //                $("#form_action").val("save");
        //                $.post("manage_remoteareas.php", $("#adminForm").serialize(), function (response) {
        //                    if (response.STATUS == "success") {
        //                        $("#res_message").html("Remoteareas added successfully");
        //                        $("#res_message").show();
        //                        $('#group_name').val("");
        //                        $('#group_name').selectpicker('refresh');
        //                        $('#country_name').val("");
        //                        $('#country_name').selectpicker('refresh');
        //                        $("#from_post_code").val("");
        //                        $("#to_post_code").val("");
        //                        $("#city_name").val("");
        //                        $("#add_carrier_id").val("");
        //                        $("#id").val("");
        //                        grid.getDataTable().ajax.reload();
        //                    } else {
        //                    }
        //                }, "json");
        //        });
            $('#btnSave').click(function () {
                if ($("#country_name").val() == "") {
                    swal("", "Please select Country name", "info");
                } else if ($("#from_post_code").val() == "") {
                    swal("", "Please enter from post code", "info");
                } else if ($("#to_post_code").val() == "") {
                    swal("", "Please enter to post code", "info");
                } else if ($("#city_name").val() == "") {
                    swal("", "Please enter city name", "info");
                } else {
                    $("#form_action").val("save");
                    $.post("manage_remoteareas.php", $("#adminForm").serialize(), function (response) {
                        if (response.STATUS == "success") {
                            $("#res_message").html("Remoteareas added successfully");
                            $("#res_message").show();
                            var select2Parentid = $("#group_name");
                            select2Parentid.val("").trigger('change');
                            var select2Parentid = $("#country_name");
                            select2Parentid.val("").trigger('change');
        //                            $('#group_name').val("");
        //                            $('#group_name').selectpicker('refresh');
        //                            $('#country_name').val("");
        //                            $('#country_name').selectpicker('refresh');
                            $("#from_post_code").val("");
                            $("#to_post_code").val("");
                            $("#city_name").val("");
                            $("#add_carrier_id").val("");
                            $("#id").val("");
                            grid.getDataTable().ajax.reload();
                        } else {
        //                            $("#abc").html(response.message);
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
                        function (isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "POST",
                                    url: "manage_remoteareas.php",
                                    data: {action: "delete", remoteareas_id: remoteareasId},
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
                var remoteareasId = $(this).attr("data-remoteareas_id");
                $.ajax({
                    type: "POST",
                    url: "manage_remoteareas.php",
                    data: {action: "edit", remoteareas_id: remoteareasId},
                    dataType: "json",
                    success: function (data) {
                        if (data.STATUS == "success") {
        //                                $('#group_name').val(data.remoteareas_groups_id);
        //                                $('#group_name').selectpicker('refresh');
        //                                $('#country_name').val(data.country_id);
        //                                $('#country_name').selectpicker('refresh');
                            var select2Parentid = $("#group_name");
                            select2Parentid.val(data.remoteareas_groups_id).trigger('change');
                            var select2Parentid = $("#country_name");
                            select2Parentid.val(data.country_id).trigger('change');
                            $("#from_post_code").val(data.from_postcode);
                            $("#to_post_code").val(data.to_postcode);
                            $("#city_name").val(data.city);
                            $("#add_carrier_id").val(data.carrier_id);
                            $("#id").val(remoteareasId);
                            $(".scroll-to-top").click();
                        } else {
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
            $('#country_id_temp').on('change', function () {
                var selectedCountry = $(this).find("option:selected").val();
                $("#country_id").val(selectedCountry);
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
                        Add/Update Remoteareas
                    </div>
                    <div class="actions">
                        <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                        <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;<?php echo Translation::GetCaption("IMPORT"); ?></a>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 alert alert-success display-none"  id="res_message" ></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div  id="console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                 
                                    <div class="has-float-label input-icon right"> 
                                        <i class="fa fa-shopping-cart"></i>
        <?php
        echo Ddl::generateDDL('group_name', 'RemoteareasGroupsFilter', "   is_deleted = 'N' ", 'group_name', 'id', '', ' class="select2 form-control" data-live-search="true"  data-show-subtext="true" data-toggle="tooltip"  title="Remoteareas Group Name" data-original-title="Remoteareas Group Name"', '', '', 'group_name', 'Remoteareas Group Name', '', '');
        ?>   <label>Remoteareas Group Name</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"> 
                              
                               <div class="has-float-label"> 
                                    
        <?php
        echo Ddl::generateCountryDDL('country_name', $country_id, 'id');
        ?>  <label>Country</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                 
                                    
                                     <div class="has-float-label input-icon right"> 
                                     <i class="fa fa-shopping-cart"></i>
                                        <input type="text" name="from_post_code" id="from_post_code" required="required" title="From Post Code" value="" class="form-control" placeholder='From Post Code' />  

                                         <label for="from_post_code">From Post Code</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                   
                                     <div class="has-float-label input-icon right"> 
                                        <i class="fa fa-shopping-cart"></i>
                                        <input type="text" name="to_post_code" id="to_post_code" required="required" title="To Post Code" value="" class="form-control" placeholder='To Post Code' /> 
                                        <label for="to_postcode">To Post Code</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                   
                               <div class="has-float-label input-icon right"> 
                                      <i class="fa fa-shopping-cart"></i>
                                        <input type="text" name="city_name" id="city_name" required="required" title="City Name" value="" class="form-control" placeholder='City Name' /> 
                                        <label for="city_name">City Name</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;">  </div>          
                        <div class="col-md-12 text-center">  
                            <a id="btnSave"   href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>               
                            <a href="" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
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
                    Remoteareas List
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
                            <th>Remoteareas Groups</th>
                            <th>Country</th>
                            <th>From Postcode</th>
                            <th>To Postcode</th>
                            <th>City</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
        <?php
        echo Ddl::generateDDL('remoteareas_groups_id', 'RemoteareasGroupsFilter', "   is_deleted = 'N' ", 'group_name', 'id', '', ' class="select2 form-control form-filter " data-show-subtext="true"', 'Please select', '', 'remoteareas_groups_id', 'Remoteareas Group Name', '', '');
        ?>
                            </td>
                            <td class="user_acccount_correct_button">
        <?php
        echo Ddl::generateCountryDDL('country_id', $country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8"');
        ?>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="from_postcode" id ="from_postcode" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="to_postcode" id ="to_postcode" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="city" id ="city" />
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="option_value" value="" id="option_value"/>
                <input type="hidden" name="action" value="download_csv_frm" />
            </form>
        </div>
        <!--Model for CSV Upload-->
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Import RemoteAreas</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <input class="form-control" id="file_in" name="file_in" type="file" value="" />                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                        <button type="button" id="download_csv_btn" class="btn green">  <i class="fa fa-download"></i> Download Template </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for CSV download-->
        <div class="modal fade" id="csv_download" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select Group</h4>
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

                                    <label>Group Name</label>
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
        <?php
        echo Ddl::generateDDL('group_name_download', 'RemoteareasGroupsFilter', "   is_deleted = 'N' ", 'group_name', 'id', '', ' class="select2 form-control" data-live-search="true"  data-show-subtext="true" data-toggle="tooltip"  title="Remoteareas Group Name" data-original-title="Remoteareas Group Name"', 'All', 'all', 'group_name_download', 'Remoteareas Group Name', '', '');
        ?>                                        
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