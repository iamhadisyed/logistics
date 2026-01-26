<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([  
                    'vehiclefilter.class',
                    'vehicle.class',
                    'vehicledriver.class',
                    'vehicledriverfilter.class'
                        ]);
class Page extends BasePage {

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init() {
        // Session check
        $this->user = SessionManager::getUser();
        //BreadCrum
        if (isset($_GET['action']) && trim($_GET['action']) == "vehicle_drivers_html") {
            $driversDataHtml = new VehicleDriverFilter();
            $driversDataHtml->addFilter('vehicle_id = ' . $_POST['vehicle_id']);
            $driversDataHtml->addFilter('is_active = 1');
            $driversDataHtml->addFilter('is_deleted = 0');
            $driversDataHtml = $driversDataHtml->getList();
            $dataHtml = "";
            $loopCount = 0;
            foreach ($driversDataHtml as $driverDatum) {
                $start_date = explode(':', $driverDatum->getDriverStartTime());
                $end_date = explode(':', $driverDatum->getDriverEndTime());
                $join_date = $driverDatum->getJoiningDate();
                $start_date = $start_date[0] . ':' . $start_date[1];
                $end_date = $end_date[0] . ':' . $end_date[1];
                $join_date = formatDate($join_date);
                $accountParentId = $this->user->getUserAccountId();
                $dataHtml .= "<div class='form-group'>
                                <div class='row'>
                                    <div class='col-md-3'>";
                $accountParentId = $this->user->getUserAccountId();
                if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                    $dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver"', 'first_name', 'id', $driverDatum->getDriverId(), ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                } else {
                    $dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $driverDatum->getDriverId(), ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                }
                //$dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $driverDatum->getDriverId(), ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                $dataHtml .= "      </div>
                                    <div class='col-md-2'>
                                        <div class='input-group'>
                                            <span class='input-group-addon'>
                                                <i class='fa fa-clock-o'></i>
                                            </span>
                                            <input type='text' name='driver_data[" . $loopCount . "][driver_start_time]' value='" . $start_date . "'
                                                   class='driver_start_time form-control timepicker timepicker-24'>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class='input-group'>
                                            <span class='input-group-addon'>
                                                <i class='fa fa-clock-o'></i>
                                            </span>
                                            <input type='text' name='driver_data[" . $loopCount . "][driver_end_time]' value='" . $end_date . "'
                                                   class='driver_end_time form-control timepicker timepicker-24'>
                                        </div>
                                    </div>
                                    <div class='col-sm-2'>
                                        <div class='form-group'>
                                            <div class='input-group date date-picker-driver margin-bottom-5'
                                                 data-date-format='dd-mm-yyyy'>
                                            <span class='input-group-btn'>
                                                <button class='btn btn-sm' type='button'><i class='fa fa-calendar'></i></button>
                                            </span>
                                                <input type='text' class='form-control driver-joining_date form-filter input-sm' readonly='' value='" . $join_date . "'
                                                       name='driver_data[" . $loopCount . "][joining_date]' placeholder='Join Date' id='model_year'
                                                       data-date-format='dd-mm-yyyy' data-original-title='' title='Model Year'>
                                            </div>
                                        </div>
                                    </div>
                                    <input type='hidden' name='driver_data[" . $loopCount . "][id]' value='" . $driverDatum->getId() . "'>
                                    <div class='col-md-3'>
                                        <button type='button'
                                                class='btn btn-danger remove-parcel-key initial-button'>
                                            <i class='fa fa-minus'></i></button>

                                    </div>
                                </div>
                            </div>";
                $loopCount++;
            }
            $accountParentId = $this->user->getUserAccountId();
            $selected = '';
            $dataHtml .= "<div class='form-group'>
                            <div class='row'>
                                <div class='col-md-3'>";
            if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                $dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver"', 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
            } else {
                $dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
            }
            //$dataHtml .=             Ddl::generateDDL('driver_data[' . $loopCount . '][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
            $dataHtml .= "      </div>
                                <div class='col-md-2'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'>
                                            <i class='fa fa-clock-o'></i>
                                        </span>
                                        <input type='text' name='driver_data[" . $loopCount . "][driver_start_time]'
                                               class='driver_start_time form-control timepicker timepicker-24'>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'>
                                            <i class='fa fa-clock-o'></i>
                                        </span>
                                        <input type='text' name='driver_data[" . $loopCount . "][driver_end_time]'
                                               class='driver_end_time form-control timepicker timepicker-24'>
                                    </div>
                                </div>
                                <div class='col-sm-2'>
                                    <div class='form-group'>
                                        <div class='input-group date date-picker-driver margin-bottom-5'
                                             data-date-format='dd-mm-yyyy'>
                                        <span class='input-group-btn'>
                                            <button class='btn btn-sm' type='button'><i class='fa fa-calendar'></i></button>
                                        </span>
                                            <input type='text' class='form-control driver-joining_date form-filter input-sm' readonly=''
                                                   name='driver_data[" . $loopCount . "][joining_date]' placeholder='Join Date' id='model_year'
                                                   data-date-format='dd-mm-yyyy' data-original-title='' title='Model Year'>
                                        </div>
                                    </div>
                                </div>
                                <input type='hidden' name='driver_data[" . $loopCount . "][id]'>
                                <div class='col-md-3'>
                                    <button type='button' class='btn btn-success add-more-parcel-keys'>
                                        <i class='fa fa-plus'></i></button>
                                    <button type='button'
                                            class='btn btn-danger remove-parcel-key initial-button'>
                                        <i class='fa fa-minus'></i></button>
                                </div>
                            </div>
                        </div>";
            echo $dataHtml;
            die;
        }
        if (isset($_GET['action']) && trim($_GET['action']) == "add_vehicle") {
            $vehicle_capacity = $_POST["vehicle_capacity"];
            $vehicle_number = $_POST["vehicle_number"];
            if (isset($vehicle_number) && $vehicle_number != "" && isset($vehicle_capacity) && $vehicle_capacity != "") {
                $vehiclesList = new Vehicle();
                $vehiclesList->setId($_POST['vehicle_id']);
                $vehiclesList->setVehicleModel($_POST['vehicle_model']);
                $vehiclesList->setModelYear($_POST['model_year']);
                $vehiclesList->setRegistrationNumber($_POST['registration_number']);
                $vehiclesList->setVehicleColor($_POST['vehicle_color']);
                $vehiclesList->setVehicleMake($_POST['vehicle_make']);
                $vehiclesList->setVehicleType($_POST['vehicle_type']);
                $vehiclesList->setVehicleNumber($_POST['vehicle_number']);
                $vehiclesList->setVehicleCapacity($_POST['vehicle_capacity']);
                $vehiclesList->setAddedBy($this->user->getId());
                $vehiclesList->saveLog = false;
                $vehiclesList->save();
                $vehicle_id = $vehiclesList->getId();
//                $driversDetails = new VehicleDriver();
                $auditLogDataNew = [];
                $auditLogDataOld = [];
                if (!empty($_POST['driver_data'])) {
                    $driverFilter = new VehicleDriverFilter();
                    $driverFilter->where('vehicle_id = ' . $vehicle_id);
                    $driversList = $driverFilter->getList();
                    foreach ($driversList as $driverDatum) {
                        $driverObj = new User($driverDatum->getDriverId());
                        $auditLogDataOld['drivers_assigned_to_' . $vehicle_number][] = [
                            'driver_name' => $driverObj->getFirstName() . ' ' . $driverObj->getLastName(),
                            'driver_start_time' => $driverDatum->getDriverStartTime(),
                            'driver_end_time' => $driverDatum->getDriverEndTime(),
                            'driver_join_date' => $driverDatum->getJoiningDate()
                        ];
                    }
                    $driverFilter->update(['is_deleted' => 1, 'deleted_by' => $this->user->getId()]);
                    foreach ($_POST['driver_data'] as $driver_datum) {
                        $driversDetails = new VehicleDriver();
                        if (!empty($driver_datum['id'])) {
                            $driversDetails->setId($driver_datum['id']);
                        }
                        $driverObj = new User($driver_datum['user_id_cls']);
                        $auditLogDataNew['drivers_assigned_to_' . $vehicle_number][] = [
                            'driver_name' => $driverObj->getFirstName() . ' ' . $driverObj->getLastName(),
                            'driver_start_time' => $driver_datum['driver_start_time'],
                            'driver_end_time' => $driver_datum['driver_end_time'],
                            'driver_join_date' => formatDate(date('Y-m-d', strtotime($driver_datum['joining_date'])))
                        ];
                        $driversDetails->setDriverEndTime($driver_datum['driver_end_time']);
                        $driversDetails->setDriverStartTime($driver_datum['driver_start_time']);
                        $driversDetails->setDriverId($driver_datum['user_id_cls']);
                        $driversDetails->setVehicleId($vehicle_id);
                        $driversDetails->setAddedBy($this->user->getId());
                        $driversDetails->setJoiningDate(date('Y-m-d', strtotime($driver_datum['joining_date'])));
                        $driversDetails->setIsActive(1);
                        $driversDetails->setIsDeleted(0);
                        $driversDetails->save();
                    }
                }
                $vehiclesList->logMoreDataNew = $auditLogDataNew;
                $vehiclesList->logMoreDataOld = $auditLogDataOld;
                $vehiclesList->saveAuditData();
                $msg = "Data saved";
                $arr = array('result' => 'success', 'message' => $msg);
            } else {
                $msg = "Something went wrong";
                $arr = array('result' => 'error', 'message' => $msg);
            }
            echo json_encode($arr);
            die;
        }

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'list_vehicles.php' => "Manage Vehicles"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "vehicles_list_ajax") {
            $vehiclesFilter = new VehicleFilter();
            //$userIdArr = [];
//            $userAccountId = $this->user->getUserAccountId();
//            $userFilter = new UserFilter();
//            $vehiclesFilter->addFilter("user_account_id", $userAccountId);
//            $userObj = $vehiclesFilter->getList("id");
//            if (count($userObj) > 0) {
//                foreach ($userObj as $user) {
//                    $userIdArr[] = $user->getId();
//                }
//            }
//            $vehiclesFilter->addFilterIn("userid", $userIdArr);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $vehicleType = $this->form_vars['vehicle_type'];
                if (!empty($vehicleType)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_type = '{$vehicleType}'");
                }

                $vehicleMake = $this->form_vars['vehicle_make'];
                if (!empty($vehicleMake)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_make = '{$vehicleMake}'");
                }

                $modelYear = $this->form_vars['model_year'];
                if (!empty($modelYear)) {
                    $vehiclesFilter->addFilter("vhl.model_year = '{$modelYear}'");
                }

                $vehicleModel = $this->form_vars['vehicle_model'];
                if (!empty($vehicleModel)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_model LIKE '%{$vehicleModel}%'");
                }

                $registrationNumber = $this->form_vars['registration_number'];
                if (!empty($registrationNumber)) {
                    $vehiclesFilter->addFilter("vhl.registration_number = '{$registrationNumber}'");
                }

                $vehicleColor = $this->form_vars['vehicle_color'];
                if (!empty($vehicleColor)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_color = '{$vehicleColor}'");
                }
                $vehicleCapacity = $this->form_vars['vehicle_capacity'];
                if (!empty($vehicleCapacity)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_capacity = '{$vehicleCapacity}'");
                }
                $vehicleNumber = $this->form_vars['vehicle_number'];
                if (!empty($vehicleNumber)) {
                    $vehiclesFilter->addFilter("vhl.vehicle_number = '{$vehicleNumber}'");
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
                $vehiclesFilter->OrderBy(strtolower("vhl." . $dataTableColumnName), strtoupper($orderBy));
            }
            /*
             * Pagination Logic Implemented
             *
             */
            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                $userIds = CustomerAccount::getAccountUsers($this->user->getUserAccountId());
                $vehiclesFilter->whereIn('added_by', $userIds);
            }
            $iTotalRecords = $vehiclesFilter->getCount(false);
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $vehiclesFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $vehiclesFilter->setOffset($iDisplayStart);
            $vehiclesFilter->addFilter('is_deleted = 0');
            $vehiclesList = $vehiclesFilter->getList();
            $setDataArr = array();
            foreach ($vehiclesList as $vehiclesListObj) {
                $currentArr = array();
//                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                $currentArr['vehicle_type'] = $vehiclesListObj->getVehicleType();
                $currentArr['model_year'] = $vehiclesListObj->getModelYear();
                $currentArr['vehicle_make'] = $vehiclesListObj->getVehicleMake();
                $currentArr['vehicle_color'] = $vehiclesListObj->getVehicleColor();
                $currentArr['vehicle_model'] = $vehiclesListObj->getVehicleModel();
                $currentArr['vehicle_capacity'] = $vehiclesListObj->getVehicleCapacity();
                $currentArr['vehicle_number'] = $vehiclesListObj->getvehicleNumber();
                $currentArr['registration_number'] = $vehiclesListObj->getregistrationNumber();
                $currentArr['registration_number'] = $vehiclesListObj->getregistrationNumber();
                $currentArr['first_name'] = $vehiclesListObj->getFirstName();
                $currentArr['last_name'] = $vehiclesListObj->getLastName();
                $driversDetails = new VehicleDriverFilter();
                $driversDetails->addFilter('vehicle_id = ' . $vehiclesListObj->getId());
                $driversDetails->where('dds.is_deleted = 0');
                $driversDetails->innerJoin('user', 'user.id = dds.driver_id');
                $driversDetails = $driversDetails->getList();
                $driverData = [];
                foreach ($driversDetails as $driverDatum) {
                    if ($driverDatum->getIsDeleted() == 0) {
                        $driverData[] = [
                            'driver_id' => $driverDatum->getDriverId(),
                            'vehicle_id' => $driverDatum->getVehicleId(),
                            'start_time' => $driverDatum->getDriverStartTime(),
                            'end_time' => $driverDatum->getDriverEndTime(),
                            'first_name' => $driverDatum->getFirstName(),
                            'last_name' => $driverDatum->getLastName(),
                            'joining_date' => formatDate($driverDatum->getJoiningDate())
                        ];
                    }
                }
//                $currentArr['driver_data'] = $driverData;
                $currentArr['actions'] = "<a href='javaScript:Void(0);' data-vehicle_json='" . json_encode($currentArr) . "' data-vehicle_id='" . $vehiclesListObj->getId() . "' data-driver_data='" . json_encode($currentArr['driver_data']) . "' class='btn-edit-vehicle btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";
                $currentArr['actions'] .= "<a href='' class='btn btn-xs blue btn-outline' id='driver-detail-btn' data-target='#vehicle-drivers' data-driver_data='" . json_encode($driverData) . "' data-toggle='modal'><span class='fa fa-eye'></span> </a> <a href='' class='btn btn-xs blue btn-outline' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $vehiclesListObj->getId() . "' 
                                            data-log_name='vehicle' data-toggle='modal'> <i class='fa fa-list'></i>
                                            </a>";
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
              <?php
          }

          public function addPagelavelJs() {
              ?>

        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
        type="text/javascript"></script>
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
                                "url": "list_vehicles.php?action=vehicles_list_ajax", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "vehicle_type"},
                                {"data": "vehicle_make"},
                                {"data": "model_year"},
                                {"data": "vehicle_model"},
                                {"data": "registration_number"},
                                {"data": "vehicle_color"},
                                {"data": "vehicle_capacity"},
                                {"data": "vehicle_number"},
                            ]
                        }
                    });
                };
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                $(".initial-button").hide();
                DataTableFun.init();
                $("#btn_save_vehicle").click(function () {
                    if ($("#vehicle_type").val() == "") {
                        show_res_msg("error", "Please enter Vehicle type");
                    } else if ($("#vehicle_make").val() == "") {
                        show_res_msg("error", "Please enter Vehicle make");
                    } else if ($("#vehicle_model").val() == "") {
                        show_res_msg("error", "Please enter Vehicle model");
                    } else if ($("#vehicle_color").val() == "") {
                        show_res_msg("error", "Please enter Vehicle color");
                    } else if ($("#model_year").val() == "") {
                        show_res_msg("error", "Please select modal year");
                    } else if ($("#registration_number").val() == "") {
                        show_res_msg("error", "Please enter registration number");
                    } else if ($("#vehicle_capacity").val() == "") {
                        show_res_msg("error", "Please enter Vehicle capacity");
                    } else if ($("#vehicle_number").val() == "") {
                        show_res_msg("error", "Please enter Vehicle number");
                    } else {
                        var vehicle_type = $("#vehicle_type").val();
                        var vehicle_number = $("#vehicle_number").val();
                        var vehicle_capacity = $("#vehicle_capacity").val();
                        var vehicle_make = $("#vehicle_make").val();
                        var vehicle_model = $("#vehicle_model").val();
                        var vehicle_color = $("#vehicle_color").val();
                        var model_year = $("#model_year").val();
                        var registration_number = $("#registration_number").val();
                        var vehicle_id = $("#vehicle_id").val();
                        /*var drivers_array = [];
                         for (i = 0; i < elindex; i++) {
                         drivers_array[i]['driver_end_time'] = $("input[name=driver_data[i]]").val();
                         }*/
                        var frmData = $("#vehicle_save").serialize();
                        $.ajax({
                            type: "POST",
                            url: "list_vehicles.php?action=add_vehicle",
                            data: frmData,

                            success: function (data) {
                                var obj = jQuery.parseJSON(data);
                                if (obj.result == "success") {
                                    show_res_msg('success', obj.message);
                                    $('input').val('');
                                    grid.getDataTable().ajax.reload();
                                } else {
                                    show_res_msg('error', obj.message);
                                }
                            },
                            error: function () {
                                alert('error occur');
                            }
                        });
                    }
                });
                $('body').on('click', '.btn-edit-vehicle', function () {
                    var vehicle_data = $(this).data("vehicle_json");
                    var vehicle_id = $(this).data("vehicle_id");
                    var driver_data = $(this).data("driver_data");
                    $("#vehicle_capacity").val(vehicle_data.vehicle_capacity);
                    $("#vehicle_model").val(vehicle_data.vehicle_model);
                    $("#vehicle_type").val(vehicle_data.vehicle_type);
                    $("#vehicle_color").val(vehicle_data.vehicle_color);
                    $("#vehicle_make").val(vehicle_data.vehicle_make);
                    $("#vehicle_number").val(vehicle_data.vehicle_number);
                    $("#model_year").val(vehicle_data.model_year);
                    $("#registration_number").val(vehicle_data.registration_number);
                    $("#vehicle_id").val(vehicle_id);
                    $.ajax({
                        type: "POST",
                        url: "list_vehicles.php?action=vehicle_drivers_html",
                        data: {vehicle_id: vehicle_id},
                        success: function (data) {
                            $(".driver-details-html").html(data);
                        },
                        error: function () {
                            alert('error occur');
                        }
                    });
                });
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
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years"
                    });
                }
                $('body').on('click', '.date-picker-driver', function () {
                    $(this).datepicker({
                        autoclose: true,
                    });
                });
                $('body').on('click', '#driver-detail-btn', function () {
                    var that = $(this);
                    var html = '';
                    var driver_data = that.data('driver_data');
                    $.each(driver_data, function (index, value) {
                        html += '<tr>' +
                                '<td>' + value.first_name + ' ' + value.last_name + '</td>' +
                                '<td>' + value.start_time + '</td>' +
                                '<td>' + value.end_time + '</td>' +
                                '<td>' + value.joining_date + '</td>' +
                                '</tr>';
                    });
                    $('#driver_details_list').html(html);
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
        </script>
        <?php
    }

    protected function renderHead() {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="modal fade" tabindex="-1" role="dialog" id="vehicle-drivers">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Vehicle Drivers</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Driver Name</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Joining Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="driver_details_list">
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="icon-bar-chart"></i>
                    Add Vehicle
                </div>
                <div class="tools"></div>
            </div>
            <form name="vehicle_save" id="vehicle_save" method="post">
                <div class="portlet-body">
                    <div class="row" id="show_general_msg" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-danger"></div>
                        </div>
                    </div>
                    <div class="vehicle-add-update-form">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                    <div class="has-float-label input-icon right">

                                       <i class="fa fa-user"></i>
                                    
                                            <input id="vehicle_type" name="vehicle_type"
                                                   value="<?php if (!empty($vehicle_type)) echo $vehicle_type; ?>"
                                                   type="text" placeholder="Vehicle Type" required=""
                                                   class="form-control tooltipbutton" data-toggle="tooltip"
                                                   data-placement="top" title="Vehicle Type"/>

                                                   <label>Vehicle Type  <i class="fa tooltips font-red"                                                                       data-original-title="Vehicle is mandatory">*</i></label>
                                      
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  
                                    <div class="has-float-label input-icon right">
                                     <i
                                                class="fa fa-user"></i>
                                  
                                            <input id="vehicle_make" name="vehicle_make"
                                                   value="<?php if (!empty($vehicle_make)) echo $vehicle_make; ?>"
                                                   type="text" placeholder="Vehicle Make" required=""
                                                   class="form-control tooltipbutton" data-toggle="tooltip"
                                                   data-placement="top" title="Vehicle Make"/>

                                                     <label>Vehicle Make <i class="fa tooltips font-red"
                                                                         data-original-title="Vehicle Make is mandatory">*</i></label>
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                     <div class="has-float-label input-icon right"><i
                                                class="fa fa-envelope"></i> 
                                        
                                            <input id="vehicle_model" name="vehicle_model"
                                                   value="<?php if (!empty($vehicle_model)) echo $vehicle_model; ?>"
                                                   type="text"
                                                   placeholder="Vehicle Model" required=""
                                                   class="form-control tooltipbutton"
                                                   maxlength="40" rel="tooltip"
                                                   data-original-title="Vehicle Model"
                                                   data-placement="top" data-toggle="tooltip" data-placement="top"
                                                   title="Vehicle Model"/>
                                                   <label>Vehicle Model <i class="fa tooltips font-red"
                                                                         data-original-title="Vehicle Model is mandatory">*</i></label>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                    <div class="input-group date date-picker margin-bottom-5"
                                         data-date-format="yyyy-mm-dd:">
                                       
                                        <input type="text" class="form-control form-filter input-sm" readonly=""
                                               name="model_year" placeholder="Date Created" id="model_year"
                                               data-date-format="yyyy-mm-dd" data-original-title="" title="Model Year">
                                               <label>Model Year</label>
                                                <span class="input-group-btn">
                                            <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                      </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                    <div class="has-float-label input-icon right">
                                       <i class="fa fa-user"></i> 
                                      
                                           
                                            <input id="registration_number" name="registration_number"
                                                   value="<?php if (!empty($registration_number)) echo $registration_number; ?>"
                                                   type="text" placeholder="Registration Number" required=""
                                                   class="form-control tooltipbutton" data-toggle="tooltip"
                                                   data-placement="top" title="Registration Number"/>
                                                   <label>Registration Number  <i class="fa tooltips font-red"
                                               data-original-title="Vehicle Registration Number is mandatory">*</i></label>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                   
                                    <div class="has-float-label input-icon right">
                                        <i
                                                class="fa fa-user"></i>
                                       
                                            <input id="vehicle_color" name="vehicle_color"
                                                   value="<?php if (!empty($vehicle_color)) echo $vehicle_color; ?>"
                                                   type="text" placeholder="Vehicle Color"
                                                   class="form-control tooltipbutton" data-toggle="tooltip"
                                                   data-placement="top" title="Vehicle Color"/>
                                                    <label>Vehicle Color <i class="fa tooltips font-red"></i></label>
                                      
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                   <div class="has-float-label input-icon right">
                                    <i class="fa fa-envelope"></i> 
                                   


                                  
                                            <input id="vehicle_capacity" name="vehicle_capacity"
                                                   value="<?php if (!empty($vehicle_capacity)) echo $vehicle_capacity; ?>"
                                                   type="text"
                                                   placeholder="Vehicle capacity" required=""
                                                   class="form-control tooltipbutton"
                                                   maxlength="40" rel="tooltip"
                                                   data-original-title="Vehicle Capacity"
                                                   data-toggle="tooltip" data-placement="top"
                                                   title="Vehicle Capacity"/>
                                                   <label>Vehicle Capacity  <i class="fa tooltips font-red"
                                                                         data-original-title="Vehicle Capacity is mandatory">*</i></label>
                                      
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    
                                   <div class="has-float-label input-icon right">
                                  
                                        <input id="vehicle_number" name="vehicle_number" type="text"
                                               value="<?php if (!empty($vehicle_number)) echo $vehicle_number; ?>"
                                               placeholder="Vehicle Number" class="form-control tooltipbutton"
                                               data-toggle="tooltip" data-placement="top" title="Vehicle Number"/>
                                               <label>Vehicle Number</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Driver</label>
                            </div>
                            <div class="col-md-2">
                                <label>Driver Start Time</label>
                            </div>
                            <div class="col-md-2">
                                <label>Driver End Time</label>                                  
                            </div>
                            <div class="col-md-2">
                                <label>Driver Join Date</label>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                            </div> 
                        </div>
                        <div class="driver-details-html">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php
                                        //for selected value
                                        $selected = '';
                                        $accountParentId = $this->user->getUserAccountId();
                                        if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                                            echo Ddl::generateDDL('driver_data[0][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver"', 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                                        } else {
                                            echo Ddl::generateDDL('driver_data[0][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                                        }
                                        //echo Ddl::generateDDL('driver_data[0][user_id_cls]', 'UserFilter', ' is_tc_agreed = "y" AND is_deleted = 0 AND active_flag = 1 AND user_type = "driver" AND user_account_id = ' . $accountParentId, 'first_name', 'id', $selected, ' class="user_id_cls form-control select2" data-toggle="tooltip" data-placement="top" title="Drivers" data-original-title="Drivers"', 'Please Select Driver', '', 'driver_id', 'Drivers');
                                        ?>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </span>
                                            <input type="text" name="driver_data[0][driver_start_time]" class="driver_start_time form-control timepicker timepicker-24">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </span>
                                            <input type="text" name="driver_data[0][driver_end_time]" class="driver_end_time form-control timepicker timepicker-24">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group date date-picker-driver margin-bottom-5" data-date-format="dd-mm-yyyy">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                            <input type="text" class="form-control driver-joining_date form-filter input-sm" readonly="" name="driver_data[0][joining_date]" placeholder="Join Date" id="model_year" data-date-format="dd-mm-yyyy" data-original-title="" title="Model Year">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-success add-more-parcel-keys"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove-parcel-key initial-button"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" class="parcel_id" name="parcel[0][id]" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="0" name="vehicle_id" id="vehicle_id">
                    <div class="row">
                        <div class="row" align="center">
                            <div class="col-md-12">
                                <a href="javascript:void(0);" id="btn_save_vehicle" class="btn btn-primary btn_save margin-bottom-5" data-original-title="" title="">Add/Update
                                    Vehicle
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!--portlet-body-->
            </form>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    Vehicles List
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
                            <th style="width: 75px;">Actions</th>
                            <th>Vehicle Type</th>
                            <th>Vehicle Make</th>
                            <th>Model Year</th>
                            <th>Vehicle Model</th>
                            <th>Reg. No</th>
                            <th>Vehicle Color</th>
                            <th>Vehicle Capacity</th>
                            <th>Vehicle Number</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5">
                                        <i class="fa fa-search"></i></button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline">
                                        <i class="fa fa-times"></i></button>
                                </div>

                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_type"
                                       id="vehicle_type1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_make"
                                       id="vehicle_make1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="model_year"
                                       id="model_year1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_model"
                                       id="vehicle_model1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="registration_number"
                                       id="registration_number1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_color"
                                       id="vehicle_color1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_capacity"
                                       id="vehicle_capacity1"/>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="vehicle_number"
                                       id="vehicle_number1"/>
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

    public function renderFooter() {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
