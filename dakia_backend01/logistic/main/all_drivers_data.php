<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage
{

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init()
    {
        $this->user = SessionManager::getUser();

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'list_vehicles.php' => "Driver Stats List"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "drivers_list_ajax") {
            $driverFilter = new UserFilter();
            $driverFilter->addFieldFilter('user_type', 'driver');
            /*
             * Pagination Logic Implemented
             *
             */
            $userAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
            $driverFilter->addFilterIn('user_account_id', $userAccountArray);
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $user_name = $this->form_vars['user_name'];
                if (!empty($user_name)) {
                    $driverFilter->addFieldLikeFilter(" CONCAT(first_name, ' ', last_name)", $user_name);
                }
            }
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end;

            $driverFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $driverFilter->setOffset($iDisplayStart);
            $driversList = $driverFilter->getList();
            $iTotalRecords = $driverFilter->getCount();
            $setDataArr = array();
            foreach ($driversList as $DriversListObj) {
                $currentArr = array();
//                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
//                $currentArr['vehicle_type'] = $vehiclesListObj->getVehicleType();
                $currentArr['driver_name'] = $DriversListObj->getFirstName() . " " . $DriversListObj->getLastName();
                $driverParcels = new AssignVehicleFilter();
                $driverParcels->addFilter(['driver_id' => $DriversListObj->getId()], '=');
                $totalParcels = $driverParcels->getCount(false);
                $driverParcels = new AssignVehicleFilter();
                $driverParcels->addFilter(['driver_id' => $DriversListObj->getId(), 'is_active' => 1], '=');
                $totalDelivereParcels = $driverParcels->getCount(false);
                $driverParcels = new AssignVehicleFilter();
                $driverParcels->addFilter(['driver_id' => $DriversListObj->getId(), 'is_active' => 0], '=');
                $totalUndeliveredParcels = $driverParcels->getCount(false);
                $currentArr['total_parcels'] = $totalParcels;
                $currentArr['total_delivered'] = $totalDelivereParcels;
                $currentArr['total_undelivered'] = $totalUndeliveredParcels;
                $currentArr['actions'] = '<a class="btn btn-default" href="list_assign_parcels_to_vehicle.php?driver_id=' . $DriversListObj->getId() . '"><i class="fa fa-eye"></i></a>';
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

    protected function addPagelavelCss()
    {
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

    public function addPagelavelJs()
    {
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
                            "ordering": false,
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "all_drivers_data.php?action=drivers_list_ajax&driver_id=<?php if (!empty($_GET['driver_id'])) echo $_GET['driver_id'];?>", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "driver_name"},
                                {"data": "total_parcels"},
                                {"data": "total_delivered"},
                                {"data": "total_undelivered"}
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
                        format: "yyyy-mm-dd"
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

                /*                if ($('.time-picker').length > 0) {
                 //init date pickers
                 $('.time-picker').datetimepicker({
                 datepicker:false,
                 format:'H:i'
                 });
                 }*/
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
                    $(clone).find('input').val('0');
                    $(clone).find('.user_id_cls').attr('name', driver_id.replace(/\d+/, elindex));
                    $(clone).find('.driver_start_time').attr('name', driver_start_time.replace(/\d+/, elindex));
                    $(clone).find('.driver_end_time').attr('name', driver_end_time.replace(/\d+/, elindex));
                    $(clone).find('.driver-joining_date').attr('name', driver_joining_date.replace(/\d+/, elindex));
                    $(clone).find('button.remove-parcel-key').show();
                    $(clone).find('button.remove-parcel-key').removeClass('initial-button');
                    $(clone).appendTo($('.vehicle-add-update-form'));
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
                                if ($('.vehicle-add-update-form .remove-parcel-key').length > 1) {
                                    $(el).parent().parent().remove();
                                    if ($('.add-more-parcel-keys').length == 0) {
                                        var addMore = $(el).parent().find('.add-more-parcel-keys').clone();
                                        $('.vehicle-add-update-form .remove-parcel-key').last().parent().prepend(addMore);
                                    }
                                    if ($(".driver_end_time").length == 1) {
                                        $(".vehicle-add-update-form .remove-parcel-key").hide();
                                    }
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                                // calculateTotalParcelWeight();
                            }
                        });
                });
                $('.driver-joining_date').trigger('click');
                $('body').trigger('click');

                $(document).on("click", ".delete_vehicle_assignment", function () {
                    var parcel_assignment_id = $(this).data('parcel_assignment_id');
                    swal({
                            title: "Are You Sure?",
                            text: "you want to change the status of selected assign parcel!",
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
                                    url: 'list_assign_parcels_to_vehicle.php?action=delete_parcel_consignment',
                                    data: {
                                        parcel_assignment_id: parcel_assignment_id
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (data) {
                                        grid.getDataTable().ajax.reload();
                                        $('.alert-success').show().html(data.message).delay(5000).slideUp(500);
                                        $(window).scrollTop(0);
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });

                });
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

    protected function renderHead()
    {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
//        $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
//        foreach ($this->form_vars as $key => $val) {
//            $$key = $val;
//        }
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
                <div class="caption"><i class="fa fa-list"></i>
                    All Drivers Stats
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-success" style="display: none">

                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Action</th>
                        <th>Driver Name</th>
                        <th>Total Parcels</th>
                        <th>Delivered Parcels</th>
                        <th>Delivered Parcels</th>
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
                            <input type="text" class="form-control form-filter" name="user_name"
                                   id="user_name"/>
                        </td>
                        <td>

                        </td>
                        <td>
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
        <?php
    }

    public function renderFooter()
    {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
