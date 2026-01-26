<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
        'countryfilter.class',
        'country.class',
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
            "Assign Vehicle"
        );
        $this->user = SessionManager::getUser();

        $this->subAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);

        /*
         * DataTable handlings
         */

        if (isset($_GET['action']) && $_GET['action'] == "consignment_list") {
            $this->consignment_filter = new ConsignmentFilter();
            /*
             * Column filter
             * For search
             */
            $consignmentDataArr = array();
            $TotalNumberPieces = 0;
            $TotalWeight = 0;
            $iTotalRecords = 0;
            $sEcho = $this->form_vars['draw'];
            $getZeroPriced = 0;
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->filter_form($this->form_vars);

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

                    if (trim($dataTableColumnName) == 'account')
                        $dataTableColumnName = 'user_account';
                    if (trim($dataTableColumnName) == 'service_type')
                        $dataTableColumnName = 'service_name';
                    if (trim($dataTableColumnName) == 'shipment_status')
                        $dataTableColumnName = 'consignment_status';
                    if (trim($dataTableColumnName) == 'country_iso_code')
                        $dataTableColumnName = 'country_name';
                }
                /*
                 * Set pagination & Encode data into Json form to return to DataTable
                 */

                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;

                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $this->consignment_filter->setRowsPerPage($iDisplayLength);
                $this->consignment_filter->setOffset($iDisplayStart);
                $consignmentObjs = $this->consignment_filter->getListNew('c.`hawb`,c.`service_id`,c.`address_line_1`,c.`address_line_2`,c.`address_line_3`,c.`city`,c.`state`,c.`postcode`,c.`country_id`,p.tracking_number,p.id as parcel_id,p.parcel_status_code as shipment_status ,u.user_account_id', true, false);
                $iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
                
                foreach ($consignmentObjs as $consignmentObj) {
                    $action = '';
//                    $action .= '<div class="action_check_box">';
//                    $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select[]"  data-parcel_id="' . $consignmentObj->getParcelId() . '" id="multi_select_' . $consignmentObj->getParcelId() . '"  value="' . $consignmentObj->getParcelId() . '"  class="group-checkable parcelIds shipment-check" /><span></span></label>';
//                    $action .= '</div>';
//                    $consignmentArr['option'] = $action;

                    $trackingNumber = "";
                    $trackingNumber .= '<a href="tracking.php?tracking_number=' . $consignmentObj->getTrackingNumber() . '" target="_blank">';
                    $trackingNumber .= $consignmentObj->getTrackingNumber();
                    $trackingNumber .= '</a>';
                    /* get address */
                    $country = new Country($consignmentObj->getCountryId());
                    $address = $consignmentObj->getAddressLine1() . " " . $consignmentObj->getAddressLine2() . " " . $consignmentObj->getAddressLine3() . " " . $consignmentObj->getCity() . " " . $consignmentObj->getState() . ", " . $consignmentObj->getPostcode() . " " . $country->getName();
                    $shipment_status = Consignment::getShipnmentStatus($consignmentObj->getShipmentStatus());
                    /* get service */
                    $service = new Services($consignmentObj->getServiceId());
                    $carrier = new Carrier($service->getCarrierId());
                    /* get user account */
                    $userAccount = new CustomerAccount($consignmentObj->getUserAccountId());
                    $consignmentArr['account'] = $userAccount->getUserAccount();
                    $consignmentArr['hawb'] = $consignmentObj->getHawb();
                    $consignmentArr['tracking_number'] = $trackingNumber;
                    $consignmentArr['carrier'] = $carrier->getCarrierDisplayName();
                    $consignmentArr['service'] = $service->getName();
                    $consignmentArr['status'] = $shipment_status;
                    $assignVehicle = new AssignVehicleFilter();
                    $assignVehicle->addFilter(['parcel_id' => $consignmentObj->getParcelId(), 'is_active' => 1], '=');
                    $assignVehicle = $assignVehicle->getList();
                    if(empty($assignVehicle)) {
                        $consignmentArr['assigned_status'] = "Assigned";
                    } else {
                        $consignmentArr['assigned_status'] = "Not Assigned";
                    }

                    $consignmentArr['address'] = $address;

                    $consignmentDataArr[] = $consignmentArr;
                }
            }
            $consignmentDataarr['data'] = $consignmentDataArr;
            $consignmentDataarr['draw'] = $sEcho;
            $consignmentDataarr['recordsTotal'] = $iTotalRecords;
            $consignmentDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($consignmentDataarr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $return = [];
            if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                $carrierFilterObj = new CarrierFilter();
                $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
                $carrier_option = "<option value=''>Select Carrier</option>";
                foreach ($carriers as $cr) {
                    $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
                }

                $serviceFilterObj = new ServiceFilter();
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
                $service_option = "<option value=''>Select Service</option>";
                foreach ($services as $sr) {
                    $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
                }
                $return['services_option'] = $service_option;
                $return['carrier_option'] = $carrier_option;
            } else {
                $user_account_id = $this->form_vars['user_account_id'];
                $consignmentList = array();
                if (!empty($user_account_id)) {
                    $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                    $ids = implode(",", $userAccountArry);
                    $userFilter = new UserFilter();
                    $userIds = $userFilter->getUserIdsFromAccountIds($ids);
                    $consignmentFilterObj = new ConsignmentFilter();
                    $consignmentFilterObj->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                    $consignmentList = $consignmentFilterObj->getColumnList("DISTINCT(c.service_id)");
                    $serviceIds = array();
                    foreach ($consignmentList as $con) {
                        $serviceIds[] = $con->getServiceId();
                    }
                }
                $serviceFilterObj = new ServiceFilter();
                if (!empty($serviceIds)) {
                    $serviceFilterObj->addFilterIn("    ser.id", $serviceIds);
                }
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
                $carrierIds = array();
                foreach ($services as $ser) {
                    if (!in_array($ser->getCarrierId(), $carrierIds)) {
                        $carrierIds[] = $ser->getCarrierId();
                    }
                }
                $carrierFilterObj = new CarrierFilter();
                $carrierFilterObj->addFilterIn("    c1.id", $carrierIds);
                $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
                $service_option = "<option value=''>Select Service</option>";
                foreach ($services as $sr) {
                    $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
                }
                $carrier_option = "<option value=''>Select Carrier</option>";
                foreach ($carriers as $cr) {
                    $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
                }
                $consignmentFilterForAgentIds = new ConsignmentFilter();
                $consignmentFilterForAgentIds->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                $consignmentAgentList = $consignmentFilterForAgentIds->getColumnList("DISTINCT(c.agent_id)");
                $agentIds = array();
                foreach ($consignmentAgentList as $obj) {
                    $agentIds[] = $obj->getAgentId();
                }
                $agentFilter = new AgentDataFilter();
                $agentFilter->addAgentIdInFilter($agentIds);
                $agentFilterObj = $agentFilter->getColumnList('   a.agent_name,a.agent_code');
                $agent_option = "<option value=''>Select Agent</option>";
                foreach ($agentFilterObj as $obj) {
                    $agent_option .= "<option value='" . $obj->getId() . "'>" . $obj->getAgentName() . "</option>";
                }
                $return['services_option'] = $service_option;
                $return['carrier_option'] = $carrier_option;
                $return['agent_option'] = $agent_option;
            }
            echo json_encode($return);
            die;
        }
    }

    protected function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    protected function filter_form($data)
    {
        $this->form_vars = $data;

        $this->consignment_filter->addJoin("   parcel p ", "   p.consignment_id = c.id ");
        $this->consignment_filter->addJoin("   user u ", "   u.id = c.user_id ");

        $countryId = $this->form_vars['country_id'];
        $state = $this->form_vars['state'];
        $city = $this->form_vars['city'];
        $postcode = $this->form_vars['postcode'];

        $showShipment = $this->form_vars['show_shipments'];
        $userAccountId = $this->form_vars['user_account_id'];
        $carriers = $this->form_vars['carriers'];
        $service = $this->form_vars['service'];

        $dateFrom = $this->form_vars['date_from'];
        $dateTo = $this->form_vars['date_to'];

        $address = $this->form_vars['address'];

        $trackingNumber = $this->form_vars['tracking_number'];
        $hawb = $this->form_vars['hawb'];

        $serviceIdsArr = [];

        if (!empty($countryId)) {
            $this->consignment_filter->addFieldFilter('     c.country_id', $countryId);
        }
        if (!empty($state)) {
            $this->consignment_filter->addFieldLikeFilter('     c.state', $state, 'filter');
        }
        if (!empty($city)) {
            $this->consignment_filter->addFieldLikeFilter('     c.city', $city, 'filter');
        }
        if (!empty($postcode)) {
            $this->consignment_filter->addFieldFilter('     c.postcode', $postcode);
        }
        if (!empty($userAccountId)) {
            if ($showShipment == 'all') {
                $userAccountArray = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                $ids = implode(",", $userAccountArray);
                $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "filter");
            } else if ($showShipment == 'own') {
                $this->consignment_filter->addFilterIn("     u.user_account_id", $userAccountId, "filter");
            } else if ($showShipment == 'subaccount') {
                $subAccountArray = CustomerAccount::accountSubAccount($userAccountId, 0, false);
                $ids = implode(",", $subAccountArray);
                $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "filter");
            }
        } else {
            $includeParent = true;
            $userAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, $includeParent);
            $ids = implode(",", $userAccountArray);
            $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "filter");
        }

        if (isset($carriers) && trim($carriers) != '') {
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addCarrierFilter($carriers);
            $serviceList = $serviceFilter->getColumnList("id");
            if (count($serviceList) > 0) {
                foreach ($serviceList as $serviceObj) {
                    $serviceIdsArr[] = $serviceObj->getId();
                }
            }
        }
        if (isset($service) && $service != "") {
            $serviceIdsArr = [];
            $serviceIdsArr[] = $service;
        }
        if (count($serviceIdsArr) > 0) {
            $this->consignment_filter->addFilterIn('    c.service_id', $serviceIdsArr, 'filter');
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
            $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));
            $dateLabelCreatedWhere = "       ((c.date_label_created >= '" . strtotime(DbAccess3::escape($dateFrom)) . "') AND (c.date_label_created <= '" . strtotime(DbAccess3::escape($dateTo)) . "'))";
            $this->consignment_filter->addFilter($dateLabelCreatedWhere, 'filter');
        }

        if (!empty($address)) {
            $this->consignment_filter->addFilter('(c.address_line_1 like ' . $address . ' OR c.address_line_2 like ' . $address . ' OR c.address_line_3 like ' . $address . ')', 'filter');
        }

        if (trim($hawb) != '') {
            $hawb = nl2br($hawb);
            $HawbArray = explode('<br />', $hawb);
            foreach ($HawbArray as $k => $value) {
                $HawbArray[$k] = "'" . trim($value) . "'";
            }
            $this->consignment_filter->addFilterIn('    c.hawb', $HawbArray, 'filter');
        }

        if (trim($trackingNumber) != '') {
            $awb = nl2br($trackingNumber);
            $AwbArray = explode('<br />', $awb);
            foreach ($AwbArray as $key => $value) {
                $AwbArray[$key] = "'" . trim(ParseTrackingNumber::Parse($value)) . "'";
            }
            $this->consignment_filter->addFilterIn('    c.awb', $AwbArray, 'filter');
        }

        $statusIn = [
            Consignment::STATUS_LABEL_CREATED,
            Consignment::STATUS_DISPATCHED,
            Consignment::STATUS_PARTIAL_DISPATCHED,
            Consignment::STATUS_RECEIVED,
            Consignment::STATUS_INTRANSIT
        ];
        $this->consignment_filter->addFilterIn("      p.parcel_status_code", $statusIn, "filter");

        $this->consignment_filter->addGroupBy('p.id');
    }

    public function removeChar($text)
    {
        $textNew = $text;
        $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("'", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace('"', '', preg_replace('/[\$,]/', '', $textNew));

        $textNew = preg_replace('/[\n,]/', '', $textNew);

        return $textNew;
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
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
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
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
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
                    var datatableurl = "list_all_parcels.php?action=consignment_list";
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
                                // {"data": "option", "bSortable": false},
                                {"data": "account", "bSortable": false},
                                {"data": "hawb", "bSortable": false},
                                {"data": "tracking_number", "bSortable": false},
                                {"data": "carrier", "bSortable": false},
                                {"data": "service", "bSortable": false},
                                {"data": "assigned_status", "bSortable": false},
                                {"data": "status", "bSortable": false},
                                {"data": "address", "bSortable": false}
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
                grid.submitFilter();
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
                get_carriers(<?php echo $this->user->getUserAccountId(); ?>);
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
                        url: 'list_all_parcels.php',
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
                            url: "list_all_parcels.php",
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
                                            url: "list_all_parcels.php",
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
                                                        url: "list_all_parcels.php",
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


                                } else if(data.status == 'error2') {
                                    swal("Alert", data.message, "warning");
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
                                <label class="label-account">Country</label>
                                <div class="form-group">
                                    <?php echo Ddl::generateCountryDDL('country_id', '', 'iso'); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">State</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="state" name="state"
                                               placeholder="State" type="text" value="" rel="tooltip"
                                               data-original-title="State">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">City</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                        <input class="form-control form-filter" id="city" name="city" placeholder="City"
                                               type="text" value="" rel="tooltip" data-original-title="City">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Post Code</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-map-marker"></i> </span>
                                        <input class="form-control form-filter" id="postcode" name="postcode"
                                               placeholder="Postcode" type="text" value="" rel="tooltip"
                                               data-original-title="Postcode">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <label class="label-account">Address</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-table "></i> </span>
                                        <input name="address" id="address" type="text" placeholder="Address"
                                               rel="tooltip" data-original-title="Address"
                                               class="form-control form-filter"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Date </label>
                                <div class="input-group date-picker input-daterange" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter" name="date_from" id="date_from"
                                           value="" rel="tooltip" data-original-title="From Date">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control form-filter" name="date_to" id="date_to"
                                           value="" rel="tooltip" data-original-title="To Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Show Shipment </label>
                                <div class="form-group">
                                    <?php
                                    $showShipments = array(
                                        'all' => "Selected Account & Sub Accounts",
                                        'own' => "Selected Account",
                                        'subaccount' => "Sub Accounts",
                                    );
                                    echo Ddl::generateArrayDDL('show_shipments', $showShipments, '', '', 'class="form-filter select2 form-control" ', "", 'show_shipments');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                            $accountParentId = $this->user->getUserAccountId();
                                        }
                                        $selectedAccount = "";
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Carrier</label>
                                <div class="form-group">
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('carriers', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'carriers'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Service</label>
                                <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-account">Tracking</label>
                                <div class="form-group tracking_type_field">
                                    <div class="input-group"><span class="input-group-addon"> <i class="fa fa-road"></i> </span>
                                        <textarea id="tracking_number" name="tracking_number"
                                                  placeholder="Tracking Number" rel="tooltip"
                                                  data-original-title="Tracking" class="form-control form-filter"
                                                  style="resize: none;height: 143px;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">HAWB</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-table "></i> </span>
                                        <textarea name="hawb" id="hawb" type="text" placeholder="HAWB" rel="tooltip"
                                                  data-original-title="HAWB" class="form-control form-filter"
                                                  style="resize: none; height: 143px;"></textarea>
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
                        Parcel List
                    </div>
                    <div class="actions" id="bluk_actions" style="display:none;">
<!--                        <a href="javascript:;" class="btn btn-default" id="assign_vehicle_all_btn">
                            Assign Vehicle all
                        </a>
                        <a href="javascript:;" class="btn btn-default" id="assign_vehicle_selected_btn">
                            Assign Vehicle Selected
                        </a>-->
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
                               <!-- <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type='checkbox' name='checkall' class="group-checkable"/>
                                        <span></span>
                                    </label>
                                </th>-->
                                <th>Account</th>
                                <th>HAWB</th>
                                <th>Tracking Number</th>
                                <th>Carrier</th>
                                <th>Service</th>
                                <th>Assigned Status</th>
                                <th>Status</th>
                                <th>Address</th>
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
                            <h4 class="modal-title">Assign Vehicle</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="assign_driver" id="assign_driver" value=""/>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Vehicle</label>
                                    <div class="form-group">
                                        <?php
                                        $sql = "SELECT
                                                        *
                                                      FROM
                                                        `vehicle`";
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
                                             data-date-format="yyyy-mm-dd">
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