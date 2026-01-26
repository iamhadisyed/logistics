<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'services.class',
    'user.class',
    'mawb.class',
    'flightinfo.class',
    'country.class',
    'parcel.class',
    'bagging.class',
    'warehouse.class',
    'agentdata.class',
    'useraccount.class',
    'consignment.class',
    'vehicle.class',
]);

class Page extends BasePage
{
    /*
     * Controller logic
     */

    private $user = "";
    private $consignment_filter = "";
    private $subAccountArray = [];

    protected function init()
    {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "User audit list"
        );
        $this->user = SessionManager::getUser();

        $this->subAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);

        /*
         * DataTable handlings
         */
        if (isset($_POST['action']) && trim($_POST['action']) == "get_user_audit_data") {
            $userAudit = new UserAuditFilter();
            $userAuditObj = new UserAudit();
            $userAudit->addFilter(['ua.id' => $this->form_vars['user_audit_id']], '=');
            $userAudit = $userAudit->getList();
            $userAudit = $userAudit[0];
            $oldDataLog = [];
            $newDataLog = [];
            $oldDataN = json_decode($userAudit->getOldData(), true);
            $newDataN = json_decode($userAudit->getNewData(), true);
            if (!empty($oldDataN) && !empty($newDataN)) {
                foreach ($oldDataN as $old_datum_key => $old_datum_value) {
                    foreach ($newDataN as $new_datum_key => $new_datum_value) {
                        if ($new_datum_key == $old_datum_key) {
                            if (is_array($new_datum_value) && is_array($old_datum_value)) {
                                if (!empty($new_datum_value) || !empty($old_datum_value)) {
                                    $oldDataLog[$old_datum_key] = $old_datum_value;
                                    $newDataLog[$new_datum_key] = $new_datum_value;
                                }
                            } else {
                                if ($new_datum_value != $old_datum_value) {
                                    if ((!empty($old_datum_value) && $old_datum_value != 'NULL') || (!empty($new_datum_value) && $new_datum_value != 'NULL')) {
                                        $oldDataLog[$old_datum_key] = $old_datum_value;
                                        $newDataLog[$new_datum_key] = $new_datum_value;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $oldDataLog = $oldDataN;
                $newDataLog = $newDataN;
            }

            $oldDataHtml = '';
            $oldData = [];
            $newData = [];
            if (!empty($oldDataLog)) {
                foreach ($oldDataLog as $key => $datum) {
                    if ($key != 'user_pass') {
                        if ($key == 'permissions') {
                            $permissions_name = [];
                            foreach ($datum as $permission) {
                                $permissions_name[] = Translation::GetCaption($permission);
                            }
                            $oldData['permissions'] = implode(',', $permissions_name);
                        } elseif (in_array($key, $userAuditObj->servicesColumns)) {
                            $serviceObj = new Services($datum);
                            $oldData['service_name'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->servicesidColumns)) {
                            $serviceObj = new Services($datum);
                            $oldData['name_of_service'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->customizedServicesColumns)) {
                            $serviceObj = new Services($datum);
                            $oldData['customized_service_name'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->accountColumns)) {
                            $userAccountObj = new CustomerAccount($datum);
                            $oldData['user_account'] = $userAccountObj->getUserAccount();
                        } elseif (in_array($key, $userAuditObj->flightColumns)) {
                            $flightObj = new FlightInfo($datum);
                            $oldData['flight_no'] = $flightObj->getFlightNumber();
                        } elseif (in_array($key, $userAuditObj->countryColumns)) {
                            $countryObj = new Country($datum);
                            $oldData['source_country'] = $countryObj->getName();
                        } elseif (in_array($key, $userAuditObj->destinationCountryColumns)) {
                            $countryObj = new Country($datum);
                            $oldData['destination_country'] = $countryObj->getName();
                        } elseif (in_array($key, $userAuditObj->shippmentStatusColumn)) {
                            $oldData['shipment_status'] = Translation::GetCaption(Consignment::$status_array[$datum]);
                        } elseif (in_array($key, $userAuditObj->parcelColumns)) {
                            $parcelObj = new Parcel($datum);
                            $oldData['parcel_tacking_number'] = $parcelObj->getTrackingNumber();
                        } elseif (in_array($key, $userAuditObj->vehicleColumns)) {
                            $vehicleObj = new Vehicle($datum);
                            $oldData['vehicle'] = $vehicleObj->getVehicleType() . '<br>' . $vehicleObj->getVehicleModel() . '<br>' . $vehicleObj->getVehicleNumber();
                        } elseif (in_array($key, $userAuditObj->bagColumns)) {
                            $bagObj = new Bagging($datum);
                            $newData['bag_number'] = $bagObj->getBagnumber();
                        } elseif (in_array($key, $userAuditObj->mawbColumns)) {
                            $mawbObj = new Mawb($datum);
                            $oldData['mawb_number'] = $mawbObj->getMawbNumber();
                        } elseif (in_array($key, $userAuditObj->warehouseColumns)) {
                            $warehouseObj = new Warehouse($datum);
                            $oldData['source_warehosue'] = $warehouseObj->getwarehouseName();
                        } elseif (in_array($key, $userAuditObj->destinationWarehouseColumns)) {
                            $warehouseObj = new Warehouse($datum);
                            $oldData['destination_warehosue'] = $warehouseObj->getwarehouseName();
                        } elseif (in_array($key, $userAuditObj->agentColumns)) {
                            $agentObj = new AgentData($datum);
                            $oldData['agent_name'] = $agentObj->getAgentName();
                        } elseif (in_array($key, $userAuditObj->userColumns)) {
                            $userObj = new User($datum);
                            $oldData['user_name'] = $userObj->getUserName();
                        } elseif (in_array($key, $userAuditObj->userCommonColumn)) {
                            $userObj = new User($datum);
                            $oldData[$key] = $userObj->getUserName();
                        } else {
                            $oldData[$key] = $datum;
                        }
                    }
                }
            }

            if (!empty($newDataLog)) {
                foreach ($newDataLog as $key => $datum) {
                    if ($key != 'user_pass') {
                        if ($key == 'permissions') {
                            $permissions_name = [];
                            foreach ($datum as $permission) {
                                $permissions_name[] = Translation::GetCaption($permission);
                            }
                            $newData['permissions'] = implode(',', $permissions_name);
                        } elseif (in_array($key, $userAuditObj->servicesColumns)) {
                            $serviceObj = new Services($datum);
                            $newData['service_name'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->customizedServicesColumns)) {
                            $serviceObj = new Services($datum);
                            $newData['customized_service_name'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->flightColumns)) {
                            $flightObj = new FlightInfo($datum);
                            $newData['flight_no'] = $flightObj->getFlightNumber();
                        } elseif (in_array($key, $userAuditObj->shippmentStatusColumn)) {
                            $newData['shipment_status'] = Translation::GetCaption(Consignment::$status_array[$datum]);
                        } elseif (in_array($key, $userAuditObj->accountColumns)) {
                            $userAccountObj = new CustomerAccount($datum);
                            $newData['user_account'] = $userAccountObj->getUserAccount();
                        } elseif (in_array($key, $userAuditObj->mawbColumns)) {
                            $mawbObj = new Mawb($datum);
                            $newData['mawb_number'] = $mawbObj->getMawbNumber();
                        } elseif (in_array($key, $userAuditObj->vehicleColumns)) {
                            $vehicleObj = new Vehicle($datum);
                            $newData['vehicle'] = $vehicleObj->getVehicleType() . '<br>' . $vehicleObj->getVehicleModel() . '<br>' . $vehicleObj->getVehicleNumber();
                        } elseif (in_array($key, $userAuditObj->countryColumns)) {
                            $countryObj = new Country($datum);
                            $newData['source_country'] = $countryObj->getName();
                        } elseif (in_array($key, $userAuditObj->bagColumns)) {
                            $bagObj = new Bagging($datum);
                            $newData['bag_number'] = $bagObj->getBagnumber();
                        } elseif (in_array($key, $userAuditObj->parcelColumns)) {
                            $parcelObj = new Parcel($datum);
                            $newData['parcel_tacking_number'] = $parcelObj->getTrackingNumber();
                        } elseif (in_array($key, $userAuditObj->destinationCountryColumns)) {
                            $countryObj = new Country($datum);
                            $newData['destination_country'] = $countryObj->getName();
                        } elseif (in_array($key, $userAuditObj->agentColumns)) {
                            $agentObj = new AgentData($datum);
                            $newData['agent_name'] = $agentObj->getAgentName();
                        } elseif (in_array($key, $userAuditObj->warehouseColumns)) {
                            $warehouseObj = new Warehouse($datum);
                            $newData['source_warehosue'] = $warehouseObj->getwarehouseName();
                        } elseif (in_array($key, $userAuditObj->destinationWarehouseColumns)) {
                            $warehouseObj = new Warehouse($datum);
                            $newData['destination_warehosue'] = $warehouseObj->getwarehouseName();
                        } elseif (in_array($key, $userAuditObj->userCommonColumn)) {
                            $userObj = new User($datum);
                            $newData[$key] = $userObj->getUserName();
                        } elseif (in_array($key, $userAuditObj->servicesidColumns)) {
                            $serviceObj = new Services($datum);
                            $newData['name_of_service'] = $serviceObj->getName();
                        } elseif (in_array($key, $userAuditObj->userColumns)) {
                            $userObj = new User($datum);
                            $newData['user_name'] = $userObj->getUserName();
                        } else {
                            $newData[$key] = $datum;
                        }
                    }
                }
            }
            $dataFound = false;
            if (!empty($oldData)) {
                $dataFound = true;
                foreach ($oldData as $key => $datum) {
                    $oldDataHtml .= '<tr>';
                    $oldDataHtml .= '<td>' . ucwords(strtolower(str_replace('_', ' ', $key))) . '</td><td>';
                    if (is_array($datum)) {
                        foreach ($datum as $data) {
                            if (is_array($data) || is_object($data)) {
                                foreach ($data as $key => $value) {
                                    $oldDataHtml .= $key . ': ' . $value . '<br/>';
                                }
                                $oldDataHtml .= '<br/><br/>';
                            } else {
                                $oldDataHtml .= $data . '<br/>';
                            }
                        }
                    } else {
                        $oldDataHtml .= $datum;
                    }
                    $oldDataHtml .= '</td>';
                    $oldDataHtml .= '</tr>';
                }
            }

            $newDataHtml = '';
            if (!empty($newData)) {
                $dataFound = true;
                foreach ($newData as $key => $datum) {
                    $newDataHtml .= '<tr>';
                    $newDataHtml .= '<td>' . ucwords(strtolower(str_replace('_', ' ', $key))) . '</td><td>';
                    if (is_array($datum)) {
                        foreach ($datum as $data) {
                            if (is_array($data) || is_object($data)) {
                                foreach ($data as $key => $value) {
                                    $newDataHtml .= $key . ': ' . $value . '<br/>';
                                }
                                $newDataHtml .= '<br/><br/>';
                            } else {
                                $newDataHtml .= $data . '<br/>';
                            }
                        }
                    } else {
                        $newDataHtml .= $datum;
                    }
                    $newDataHtml .= '</td>';
                    $newDataHtml .= '</tr>';
                }
            }
            if ($dataFound == true) {
                $msg = ['old_html' => $oldDataHtml, 'new_html' => $newDataHtml];
            } else {
                $msg = array('result' => 'error', 'message' => 'Nothing updated and user submitted same data.');
            }
//            $arr = array('result' => 'success', 'message' => $msg);

            echo json_encode($msg);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "user_audit_list_ajax") {
            $userAuditFilter = new UserAuditFilter();
            /*
             * Column filter
             * For search
             */
            $userAuditObjNew = new UserAudit();
            $audit_table_key = 0;
            $userAuditFilter->innerJoin('user u', 'u.id = ua.added_by');
            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                $userAccountObj = new CustomerAccount();
                $userIds = $userAccountObj->getAccountUsers($this->user->getUserAccountId());
                $userAuditFilter->whereIn('ua.added_by', $userIds);
            }
            if (!empty($this->form_vars['search_table']) && $this->form_vars['search_table'] != 'select_search') {
                $tableName = $this->form_vars['search_table'];

                $query = "SELECT " . $userAuditObjNew->userSearchTable[$tableName]['primary_key'] . " FROM " . $userAuditObjNew->userSearchTable[$tableName]['table_name'];
                if (!empty($this->form_vars['search_name'])) {
                    $query .= " WHERE " . $userAuditObjNew->userSearchTable[$tableName]['search_column'] . " LIKE '%" . $this->form_vars['search_name'] . "%'";
                }
                $queryData = mysqli_fetch_assoc(DbAccess3::runQuery($query));
                if (!empty($this->form_vars['search_name'])) {
                    $audit_table_key = $queryData[$userAuditObjNew->userSearchTable[$tableName]['primary_key']];
                    if (!empty($audit_table_key)) {
                        $userAuditFilter->addFilter(" table_key = '$audit_table_key'");
                    } else {
                        $userAuditFilter->addFilter(" table_key = '999999999999999999'");
                    }
                }
                $userAuditFilter->addFilter(" table_name = '" . $userAuditObjNew->userSearchTable[$tableName]['table_name'] . "'");
            }
            if (!empty($this->form_vars['added_by'])) {
                $userObj = new UserFilter();
                $userObj->addFieldLikeFilter(" CONCAT(u.first_name, ' ', u.last_name)", $this->form_vars['added_by']);
                $userObj = $userObj->getList();
                if (!empty($userObj[0])) {
                    $userAuditFilter->addFilter(" ua.added_by = " . $userObj[0]->getId());
                } else {
                    $userAuditFilter->addFilter(" ua.added_by = " . $this->form_vars['added_by']);
                }
            }
            if (!empty($this->form_vars['message'])) {
                $userAuditFilter->addFilter(" ua.message LIKE '%" . $this->form_vars['message'] . "%'");
            }
            if (!empty($this->form_vars['date_to']) && !empty($this->form_vars['date_from'])) {
                $dateFrom = date('Y-m-d 00:00:00', strtotime($this->form_vars['date_from']));
                $dateTo = date('Y-m-d 23:59:59', strtotime($this->form_vars['date_to']));
                $userAuditFilter->addFilter(" created_at >= '$dateFrom'");
                $userAuditFilter->addFilter(" created_at <= '$dateTo'");
            }
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $userName = $this->form_vars['user_name'];
                if (!empty($userName)) {
                    $userAuditFilter->addFilter(" CONCAT(u.first_name, ' ', u.last_name) LIKE '%$userName%'");
                }

                $actionDate = $this->form_vars['action_date'];
                if (!empty($actionDate)) {
                    $actionDate = date('Y-m-d', strtotime(trim($actionDate)));
                    $userAuditFilter->addFilter("ua.created_at LIKE '%{$actionDate}%'");
                }

                $tableName = $this->form_vars['table_name'];
                if (!empty($tableName)) {
                    $userAuditFilter->addFilter("ua.table_name LIKE '%" . $userAuditObjNew->userSearchTable[$tableName]['table_name'] . "%'");
                }
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = 'ASC';
                if ($orderBy == 'true') {
                    $orderFalse = 'DESC';
                }

                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $userAuditFilter->OrderBy(strtolower("vhl." . $dataTableColumnName), strtoupper($orderBy));
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $userAuditFilter->orderBy('ua.id', 'DESC');

            $iDisplayLength = intval($_REQUEST['length']);
//            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
//            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $userAuditFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $userAuditFilter->setOffset($iDisplayStart);
            $userAuditList = $userAuditFilter->getList('ua.*, u.first_name, u.last_name');

            $iTotalRecords = $userAuditFilter->getCount();
            $setDataArr = array();
            foreach ($userAuditList as $userAuditObj) {
                $currentArr = array();
                if (empty($userAuditObj->getFirstName()) && empty($userAuditObj->getLastName())) {
                    $userName = 'System';
                } else {
                    $userName = $userAuditObj->getFirstName() . " " . $userAuditObj->getLastName();
                }
//                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                $currentArr['action_date'] = formatDateTime(date('Y-m-d H:i:s', $userAuditObj->getCreatedAt()));
                $currentArr['user_name'] = $userName;
                $currentArr['message'] = $userAuditObj->getMessage();
                $currentArr['ip_address'] = $userAuditObj->getIpAddress();
                $currentArr['actions'] .= "<a href='' class='btn btn-xs blue btn-outline' id='driver-detail-btn' data-target='#vehicle-drivers' data-audit_id='" . $userAuditObj->getId() . "' data-toggle='modal'><span class='fa fa-eye'></span> </a>";
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

        if (isset($_GET['action']) && trim($_GET['action']) == "get_user_audit_data_dynamic") {
            if ($this->form_vars['onload'] == 1) {
                $userAudit = new UserAuditFilter();
                $userAudit->addFilter(['ua.table_name' => $this->form_vars['table_name'], 'ua.table_key' => $this->form_vars['table_key']], '=');
                $userAudit->orderBy('ua.created_at', 'DESC');
//            $userAudit->orderBy('ua.id', 'DESC');
                $iTotalRecords = $userAudit->getCount();
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $end = $end > $iTotalRecords ? $iTotalRecords : $end;

                $userAudit->setRowsPerPage($iDisplayLength);
                // the offset of the list, based on current page
                $userAudit->setOffset($iDisplayStart);
                $userAuditList = $userAudit->getList();
                $setDataArr = array();
                foreach ($userAuditList as $userAuditObj) {
                    $currentArr = array();
                    if (empty($userAuditObj->getFirstName()) && empty($userAuditObj->getLastName())) {
                        $userName = 'System';
                    } else {
                        $userName = $userAuditObj->getFirstName() . " " . $userAuditObj->getLastName();
                    }
//                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                    $currentArr['action_date'] = formatDateTime(date('Y-m-d H:i:s', $userAuditObj->getCreatedAt()));
                    $currentArr['user_name'] = $userName;
                    $currentArr['message'] = $userAuditObj->getMessage();
//                $currentArr['ip_address'] = $userAuditObj->getIpAddress();
                    $currentArr['actions'] .= "<a href='' class='btn btn-xs blue btn-outline' id='user_audit_single_view_btn' data-target='#user_audit_single_view' data-audit_id='" . $userAuditObj->getId() . "' data-toggle='modal'><span class='fa fa-eye'></span> </a>";
                    $setDataArr[] = $currentArr;
                }
                $setDataArrJson['data'] = $setDataArr;
                $setDataArrJson['draw'] = $sEcho;
                $setDataArrJson['recordsTotal'] = $iTotalRecords;
                $setDataArrJson['recordsFiltered'] = $iTotalRecords;
                echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
                die;
            } else {
                $setDataArrJson['data'] = [];
                $setDataArrJson['draw'] = 1;
                $setDataArrJson['recordsTotal'] = 0;
                $setDataArrJson['recordsFiltered'] = 0;
                echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
                die;
            }
        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"
              rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">
        <style>
            .label-account {
                font-size: 12px;
                font-weight: bold;
            }

            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/flipclock.min.js"></script>
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "user_audit_list_new.php?action=user_audit_list_ajax";
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
                                {"data": "action_date", "bSortable": false},
                                {"data": "user_name", "bSortable": false},
                                {"data": "ip_address", "bSortable": false},
                                {"data": "message", "bSortable": false},
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
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
            $(document).ready(function () {
                DataTableFun.init();
                /* Custom filtering function which will search data in column four between two values */
                $('#btn_go').click(function () {
                    $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all checkboxes
                    $('input.form-filter[type="checkbox"]:checked').each(function () {
                        grid.addAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all radio buttons
                    $('input.form-filter[type="radio"]:checked').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    grid.submitFilter();
                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        dateFormate: "yyyy-mm-dd"
                    });
                }
                $('#user_account_id').change(function () {
                    var userAccountId = $(this).val();
                    get_carriers(userAccountId);
                });
                $('#carriers').change(function () {
                    $("#service").val("");
                    $("#service").select2();
                    var carrierId = $(this).val();
                    change_carriers(carrierId);
                });
                // get_carriers(<?php echo $this->user->getUserAccountId(); ?>);
                $(document).on("click", "#assign_vehicle_all_btn", function () {
                    $('#assign_driver').val('all');
                    $('#vehicle_id').trigger('change');
                    $('#assignVehicleModal').modal('show');
                });
                $(document).on("click", "#assign_vehicle_selected_btn", function () {
                    var ids = [];
                    $('.parcelIds:checked').map(function () {
                        ids.push(this.value);
                    }).get();
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        $('#assign_driver').val('selected');
                        $('#vehicle_id').trigger('change');
                        $('#assignVehicleModal').modal('show');
                    } else {
                        swal("Sorry!", "Please check the checkbox for action", "error");
                    }
                });
                $(document).on("change", "#vehicle_id", function () {
                    var form_data = new FormData();
                    var vehicle_id = $(this).val();
                    form_data.append('vehicle_id', vehicle_id);
                    form_data.append('action', 'get_deriver_option');
                    $.ajax({
                        url: 'assign_vehicle.php',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            $('#driver_id').html(data.options);
                            $('#driver_id').select2();
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });
                $(document).ready(function () {
                    $(".initial-button").hide();
                    $('.filter-cancel').click(function () {
                        $("#vehicle_capacity1").val('').change();
                        $("#vehicle_model1").val('').change();
                        $("#vehicle_type1").val('').change();
                        $("#vehicle_color1").val('').change();
                        $("#vehicle_make1").val('').change();
                        $("#vehicle_number1").val('').change();
                        $("#model_year1").val('').change();
                    });
                    if ($('.date-picker').length > 0) {
                        //init date pickers
                        $('.date-picker').datepicker({
                            autoclose: true,
                        });
                    }
                    $('body').on('click', '.date-picker-driver', function () {
                        $(this).datepicker({
                            autoclose: true,
                        });
                    });
                    $('body').on('click', '#driver-detail-btn', function () {
                        var that = $(this);
                        var user_audit_id = that.data('audit_id');
                        $.ajax({
                            type: "POST",
                            url: "user_audit_list.php",
                            data: {action: 'get_user_audit_data', user_audit_id: user_audit_id},
                            success: function (response) {
                                response = JSON.parse(response);
                                if (response.result == 'error') {
                                    $('.no-data-found').text(response.message);
                                    $('.no-data-found').show();
                                    $('.user_audit_data_view').hide();
                                } else {
                                    $('.user_audit_data_view').show();
                                    $('.no-data-found').hide();
                                    $('#old_data').html(response.old_html);
                                    $('#new_data').html(response.new_html);
                                }
                            },
                            error: function () {
                                //alert('error handing here');
                            }
                        });
                    });
                    $('body').on('click', '.timepicker-24', function () {
                        $('.timepicker-24').timepicker({
                            autoclose: true,
                            minuteStep: 5,
                            showSeconds: false,
                            showMeridian: false
                        });
                    });
                    var elindex = 0;
                    $(document).on('click', '.add-more-parcel-keys', function () {
                        elindex++;
                        var clone = $(this).parent().parent().parent().clone();

                        var driver_id = $(clone).find('.user_id_cls').attr('name');
                        var driver_start_time = $(clone).find('.driver_start_time').attr('name');
                        var driver_end_time = $(clone).find('.driver_end_time').attr('name');
                        var driver_joining_date = $(clone).find('.driver-joining_date').attr('name');

                        var id = $(clone).find('.parcel_id').attr('name');
                        $(clone).find('.select2-container').remove();
                        $(this).remove();
                        $(clone).find('input').val('');
                        $(clone).find('.user_id_cls').attr('name', driver_id.replace(/\d+/, elindex));
                        $(clone).find('.driver_start_time').attr('name', driver_start_time.replace(/\d+/, elindex));
                        $(clone).find('.driver_end_time').attr('name', driver_end_time.replace(/\d+/, elindex));
                        $(clone).find('.driver-joining_date').attr('name', driver_joining_date.replace(/\d+/, elindex));
                        $(clone).find('button.remove-parcel-key').show();
                        $(clone).find('button.remove-parcel-key').removeClass('initial-button');
                        $(clone).appendTo($('.driver-details-html'));
                        $(clone).find('.user_id_cls').select2();
                        $('.driver-joining_date').trigger('click');
                        $('body').trigger('click');
                    });
                    $(document).on('click', '.remove-parcel-key', function () {
                        var el = $(this);
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
                                    if ($('.driver-details-html .remove-parcel-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        if ($('.add-more-parcel-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-parcel-keys').clone();
                                            $('.driver-details-html .remove-parcel-key').last().parent().prepend(addMore);
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                    });
                    $('.driver-joining_date').trigger('click');
                    $('body').trigger('click');
                });

                function show_res_msg(type, msg) {
                    $("html, body").animate({scrollTop: 0}, "slow");
                    $("#show_general_msg div.alert").html(" ");
                    if (type == "success") {
                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                    } else {
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    }
                    $("#show_general_msg div.alert").html(msg);
                    $("#show_general_msg").show();
                    setTimeout(function () {
                        $("#show_general_msg").hide();
                    }, 7000);
                }

                $(document).on("click", "#save_assign_vehicle_btn", function () {
                    var assignDriverType = $('#assign_driver').val();
                    var ids = [];
                    var checkAllOrSelected = 1;
                    if (assignDriverType == 'selected') {
                        $('.parcelIds:checked').map(function () {
                            ids.push(this.value);
                        }).get();
                        if (typeof ids === 'undefined' && ids.length == 0) {
                            checkAllOrSelected = 0;
                        }
                    }
                    if (checkAllOrSelected == 1) {
                        var form_data = $("#parcel_details_search_form").serializeArray();
                        form_data.push({name: "ids", value: ids});
                        form_data.push({name: "action", value: "check_parcel_status"});
                        form_data.push({name: "action_type", value: assignDriverType});
                        $.ajax({
                            type: "POST",
                            url: "assign_vehicle.php",
                            data: form_data,
                            dataType: "json",
                            success: function (data) {
                                if (data.status == "success") {
                                    if (checkAllOrSelected == 1) {
                                        var form_data = $("#parcel_details_search_form").serializeArray();
                                        form_data.push({name: "ids", value: ids});
                                        form_data.push({name: "action", value: "save_assign_vehicle"});
                                        form_data.push({name: "action_type", value: assignDriverType});
                                        $.ajax({
                                            type: "POST",
                                            url: "assign_vehicle.php",
                                            data: form_data,
                                            dataType: "json",
                                            success: function (data) {
                                                if (data.status == "success") {
                                                    swal("Success!", data.message, "success");
                                                    $('#assignVehicleModal').modal('hide');
                                                } else if (data.status == "error") {
                                                    swal("Alert!", data.message, "warning");
                                                } else {
                                                    swal("Sorry!", data.message, "error");
                                                }
                                            },
                                            error: function (p1, p2, p3) {
                                                //alert('error handing here');
                                            }
                                        });
                                    } else {
                                        swal("Sorry!", "Please check the checkbox for action", "error");
                                    }
                                } else if (data.status == "error") {
                                    swal({
                                            title: "Are You Sure?",
                                            text: data.message_html,
                                            content: 'html',
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
                                                if (checkAllOrSelected == 1) {
                                                    var form_data = $("#parcel_details_search_form").serializeArray();
                                                    form_data.push({name: "ids", value: ids});
                                                    form_data.push({name: "action", value: "save_assign_vehicle"});
                                                    form_data.push({name: "action_type", value: assignDriverType});
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "assign_vehicle.php",
                                                        data: form_data,
                                                        dataType: "json",
                                                        success: function (data) {
                                                            if (data.status == "success") {
                                                                swal("Success!", data.message, "success");
                                                                $('#assignVehicleModal').modal('hide');
                                                            } else if (data.status == "error") {
                                                                swal("Alert!", data.message, "warning");
                                                            } else {
                                                                swal("Sorry!", data.message, "error");
                                                            }
                                                        },
                                                        error: function (p1, p2, p3) {
                                                            //alert('error handing here');
                                                        }
                                                    });
                                                } else {
                                                    swal("Sorry!", "Please check the checkbox for action", "error");
                                                }
                                            }
                                        });


                                } else if (data.status == 'error2') {
                                    swal("Alert", data.message, "warning");
                                } else {
                                    swal("Sorry!", data.message, "error");
                                }
                            },
                            error: function (p1, p2, p3) {
                                //alert('error handing here');
                            }
                        });
                    } else {
                        swal("Sorry!", "Please check the checkbox for action", "error");
                    }
                });
            });

            function get_carriers(userAccountId) {
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId},
                    dataType: "json",
                    success: function (data) {
                        $('#carriers').html(data.carrier_option);
                        $('#service').html(data.services_option);
                        $('#carriers').select2();
                        $('#service').select2();
                        $('#carriers').trigger('change');
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function change_carriers(carrierId) {
                $('.serviceOption').attr("disabled", "disabled");
                $('.carrier_' + carrierId).removeAttr("disabled");
                $('#service').select2();
            }

            function getTodayDate() {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }
                today = yyyy + '-' + mm + '-' + dd;
                return today;
            }

            function resetForm() {
                document.getElementById("parcel_details_search_form").reset();
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="modal fade" tabindex="-1" role="dialog" id="vehicle-drivers">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="no-data-found"></div>
                        <div class="row user_audit_data_view">
                            <div class="col-md-6">
                                <label>Old Data</label>
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>
                                    <tbody id="old_data">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <label>New Data</label>
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>
                                    <tbody id="new_data">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <form id="parcel_details_search_form" name="parcel_details_search_form" method="post">
            <input type="hidden" name="csv_action" id="csv_action" value="">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-search"></i>
                        Search Panel
                    </div>
                    <div class="tools">
                        <a href="" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div data-rail-color="blue" data-handle-color="blue" class="filter">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Select Date Range</label>
                                <div class="form-group">
                                    <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                        <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input type="text" class="form-control form-filter" name="date_from"
                                               id="date_from" value="" rel="tooltip" data-original-title="From Date">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control form-filter" name="date_to" id="date_to"
                                               value="" rel="tooltip" data-original-title="To Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Updated by</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="added_by" name="added_by"
                                               placeholder="Added By" type="text" value="" rel="tooltip"
                                               data-original-title="Added By">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Search Key</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="search_name" name="search_name"
                                               placeholder="Search name" type="text" value="" rel="tooltip"
                                               data-original-title="Search name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Search in </label>
                                <div class="form-group">
                                    <?php
                                    $tableNames = new UserAudit();
                                    echo Ddl::generateArrayDDL('search_table', $tableNames->searchTableNames, '', '', 'class="form-filter select2 form-control" ', "", 'search_table');
                                    ?>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Message</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="message" name="message"
                                               placeholder="Message" type="text" value="" rel="tooltip"
                                               data-original-title="Message">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="text-align:center;">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary filter-submit" name="btn_go"
                                            id="btn_go" value="Search"> Search
                                    </button>&nbsp;
                                    <button type="button" class="btn btn-default filter-cancel" name="btn_rest"
                                            value="Reset" onclick="resetForm()"> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-list"></i>
                        User audit list
                    </div>
                    <div class="actions" id="bluk_actions" style="display:none;">

                    </div>
                    <div class="invoice_action margin-bottom-10"></div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="table-actions-wrapper"></div>
                        <table class="table table-striped table-bordered table-hover table-condensed"
                               id="manage-data-table">
                            <thead>
                            <tr role="row" class="heading">
                                <th style="width: 75px;">Actions</th>
                                <th>Action Date</th>
                                <th>User Name</th>
                                <th>Ip Address</th>
                                <th>Message</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div id="assignVehicleModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">User audit list</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="assign_driver" id="assign_driver" value=""/>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Vehicle</label>
                                    <div class="form-group">
                                        <?php
                                        $userIds = CustomerAccount::getAccountUsers($this->user->getUserAccountId());
                                        $sql = "SELECT
                                                        *
                                                      FROM
                                                        `vehicle` WHERE added_by IN ($userIds)";
                                        $name = ['vehicle_type', 'vehicle_make', 'registration_number'];
                                        echo Ddl::generateDDLFromSql($sql, "vehicle_id", $name, 'id', "", "class='select2'", '', '', "vehicle_id", "Select Vehicle", '');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Driver</label>
                                    <div class="form-group">
                                        <select class="select2" name="driver_id" id="driver_id">
                                            <option value="">Select Driver</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <div class="input-group date date-picker margin-bottom-5"
                                             data-date-format="dd-mm-yyyy">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i
                                                            class="fa fa-calendar"></i></button>
                                            </span>
                                            <input type="text" class="form-control input-sm validate_check" readonly
                                                   name="pickup_date" id="pickup_date" placeholder="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="save_assign_vehicle_btn">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead()
    {
        ?>
        <style type="text/css">
            #tracking_number_for_status_box {
                display: none;
            }

            .table > thead > tr > th {
                vertical-align: top;
            }

            #select2-service-results .select2-results__option[aria-disabled=true] {
                display: none;
            }

            .left-check {
                float: left;
                padding-top: 5px;
            }

            .border-top-none {
                border-top: none;
            }

            .tracking_type_field .select2-container--bootstrap .select2-selection {
                border-bottom-right-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
                border-top-left-radius: 0px !important;
            }

            .tracking_type_field textarea {
                border-top-right-radius: 0px !important;
                border-top-left-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
            }

            table.dataTable td.sorting_1, table.dataTable td.sorting_2, table.dataTable td.sorting_3, table.dataTable th.sorting_1, table.dataTable th.sorting_2, table.dataTable th.sorting_3 {
                background: none !important;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td {
                color: #000;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td a {
                color: #000;
            }

            .line-height-2 {
                line-height: 2;
            }

            .input-daterange input {
                text-align: left;
            }

            @media only screen and (min-width: 983px) {
                .margin-top-md-30 {
                    margin-top: 30px;
                }
            }

        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>