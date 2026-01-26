<?php
// get settings
require_once("../includes/settings/config.inc.php");

/* * *
 * Page for editing a user
 */
include_classes([
    'country.class',
    'countryfilter.class',
    'carrier.class',
    'carrierfilter.class',
    'flightinfo.class',
    'flightinfofilter.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'flight.class',
    'flightfilter.class',
    'sea.class',
    'seafilter.class',
    'truck.class',
    'truckfilter.class',
]);

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;

    protected function init() {
//        if(!Permissions::checkFilePermission('add_flight.php')) {
//            header('Location: ' . BASE_URL);
//        }

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Add/Update Dispatch'
        );
        $this->user = SessionManager::getUser();
        if ($_GET['action'] && $_GET['action'] == 'prealert_datatable') {
            $flightFilterObj = new FlighInfoFilter();

            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                if (!empty($this->form_vars['flight_number'])) {
                    $flightFilterObj->addFilter("   fi.flight_number LIKE   '%" . $this->form_vars['flight_number'] . "%'");
                }
                if (!empty($this->form_vars['search_status'])) {
                    $flightFilterObj->addFilter("   fi.status =  '" . $this->form_vars['search_status'] . "'");
                }
                if (!empty($this->form_vars['search_flight'])) {
                    $flightFilterObj->addFilter("   fi.flight_number LIKE   '%" . $this->form_vars['search_flight'] . "%'");
                }
                if (!empty($this->form_vars['search_weight'])) {
                    $flightFilterObj->addFilter("   fi.weight = '" . $this->form_vars['search_weight'] . "'");
                }
                if (!empty($this->form_vars['search_pieces'])) {
                    $flightFilterObj->addFilter("   fi.pieces = '" . $this->form_vars['search_pieces'] . "'");
                }
                if (!empty($this->form_vars['search_user_account_id'])) {
                    $flightFilterObj->addFilter("   fi.account_id = '" . $this->form_vars['search_user_account_id'] . "'");
                }
            }
            /*            if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
              $flightFilterObj->addFilter("   fi.account_id = '".$this->user->getUserAccountId()."'");
              } */

            $flightFilterObj->addFilter("   fi.is_delete = '0' ");
            $flightFilterCount = $flightFilterObj->getCount(FALSE);
            $iTotalRecords = $flightFilterCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $flightFilterObj->setRowsPerPage($iDisplayLength);
            $flightFilterObj->setOffset($iDisplayStart);
            $dataGettingFlightInfoAndMapping = $flightFilterObj->getList(FALSE);
            if (!empty($dataGettingFlightInfoAndMapping)) {
                foreach ($dataGettingFlightInfoAndMapping as $key => $preData) {
                    $currentArr = array();
                    $status = (!empty($preData->getStatus()) ? ucwords(str_replace("_", " ", $preData->getStatus())) : '<label class="label label-warning">N/A</label>');
                    $currentArr['edit'] = "<a href='javascript:void(0);' class='btn btn-xs btn-default blue btn-outline center prealert_edit' rel='tooltip' data-toggle='tooltip' placeholder='Edit' title='Edit' data-id='" . $preData->getId() . "'> <span class='fa fa-pencil'></span> </a>";
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $currentArr['status'] = $status;
                    $toCountry = new Country($preData->getCountryId());
                    $fromCountry = new Country($preData->getDestinationCountryId());
                    $currentArr['to'] = $toCountry->getName();
                    $currentArr['from'] = $fromCountry->getName();
                    if ($preData->getIsClosed() == 0) {
                        $currentArr['closed'] = "Open";
                    } else {
                        $currentArr['closed'] = "Close";
                    }

                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr) ? $setDataArr : 0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_flight_information") {
            $outputArray = [];
//            $this->consignment_filter = new ConsignmentFilter();
            $sessionUser = SessionManager::getUser();

            if (empty($this->form_vars['flight_number']) || empty($this->form_vars['shipped_to']) || empty($this->form_vars['ship_from'])) {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Required fields is empty";
                echo json_encode($outputArray);
                die;
            }
            if (!empty($this->form_vars['flight_id'])) {
                $flightInfo = new FlightInfo($this->form_vars['flight_id']);
            } else {
                $flightInfo = new FlightInfo();
            }
            /*

              Array
              (
              [shipper_name] => sadasd
              [shipper_address_line1] => asdasd
              [shipper_address_line2] => asdasd
              [account_number] => asdsad
              [ship_from] => 2
              [flight_number] => asdasd
              [connecting_flight_number] => asdasd
              [signature_carrier] => asadsd
              [accounting_reference] => asdasd
              [reference] => asdasd
              [custom_value] => 3
              [carriers] => 195
              [carriage_value] => 3
              [insurance_amount] => 3
              [currancy] => 545
              [rate_change] => asdasd
              [weight_format] => kg
              [agent_iata_code] => asdsad
              [flight_date] => 2020-02-13 01:25
              [airport_of_depature] => asdasd
              [airway_bill] => asdasd
              [hs_codes] => asdasd
              [shippers_co] => asdsad
              [city] => asdasd
              [postcode] => ub3 3nb
              [shipped_to] => 225
              [destination_phone_number] => asdsad
              [destination_company] => asdsad
              [arrived_date] => 1899-12-06 06:30
              [destination_addressline1] => sadasd
              [destination_addressline2] => asdsad
              [consignee_co] => asdsad
              [arrival_airport] => sadsad
              [comments] => sadsad
              [action] => add_flight_information
              )

             */
            $flightInfo->setShippersName($this->form_vars['shipper_name']);
            $flightInfo->setCompany($this->form_vars['company']);
            $flightInfo->setShippersAddressline1($this->form_vars['shipper_address_line1']);
            $flightInfo->setShippersAddressline2($this->form_vars['shipper_address_line2']);
            $flightInfo->setAddressline1($this->form_vars['destination_addressline1']);
            $flightInfo->setAddressline2($this->form_vars['destination_addressline2']);
            $flightInfo->setDestinationCountryId($this->form_vars['shipped_to']);
            $flightInfo->setCountryId($this->form_vars['ship_from']);
//            $flightInfo->setFlightNumber($this->form_vars['flight_number']);
//            $flightInfo->setConnectingFlightNumber($this->form_vars['connecting_flight_number']);
            $flightInfo->setSignature($this->form_vars['signature_carrier']);
            $flightInfo->setAccountingReference($this->form_vars['accounting_reference']);
            $flightInfo->setReference($this->form_vars['reference']);
            $flightInfo->setCustomValue($this->form_vars['custom_value']);
            $flightInfo->setCarrier($this->form_vars['carriers']);
            $flightInfo->setCarriageValue($this->form_vars['carriage_value']);
            $flightInfo->setInsuranceAmount($this->form_vars['insurance_amount']);
            $flightInfo->setCurrency($this->form_vars['currancy']);
            $flightInfo->setComments($this->form_vars['comments']);
            $flightInfo->setRateChange($this->form_vars['rate_change']);
            $flightInfo->setWeightType($this->form_vars['weight_format']);
            $flightInfo->setIataCode($this->form_vars['agent_iata_code']);
            $flightInfo->setEtd(date('Y-m-d H:i:s', strtotime($this->form_vars['flight_date'])));
            $flightInfo->setEta(date('Y-m-d H:i:s', strtotime($this->form_vars['arrived_date'])));
            $flightInfo->setShipperCo($this->form_vars['shippers_co']);
            $flightInfo->setAccountNumber($this->form_vars['account_number']);
            $flightInfo->setAirwayBill($this->form_vars['airway_bill']);
            $flightInfo->setHscodes($this->form_vars['hs_codes']);
            $flightInfo->setDepartureAirport($this->form_vars['airport_of_depature']);
            $flightInfo->setArrivalAirport($this->form_vars['arrival_airport']);
            $flightInfo->setCity($this->form_vars['city']);
            $flightInfo->setPostcode($this->form_vars['postcode']);
            $flightInfo->setPhoneNumber($this->form_vars['phone_number']);
            $flightInfo->setDestinationPhoneNumber($this->form_vars['destination_phone_number']);
            $flightInfo->setCompany($this->form_vars['company']);
            $flightInfo->setConsigneeCo($this->form_vars['consignee_co']);
            $flightInfo->setDestinationCompany($this->form_vars['destination_company']);
            $flightInfo->setIsDelete(0);
            $flightInfo->setIsClosed(0);
            $flightInfo->setCreatedBy($sessionUser->getId());
            $flightInfo->setDateCreated(time());
            $flightInfo->setAccountId($sessionUser->getUserAccountId());
            $flightInfo->setTransportType($this->form_vars['flight_type']);
            $flightInfo->save();
            if ($flightInfo->getId() > 0) {
                if ($this->form_vars['flight_type'] == "flight") {
                    $flightObj = new Flight();
                    $flightObj->setFlightNumber($this->form_vars['flight_number']);
                    $flightObj->setConnectingFlightNumber($this->form_vars['connecting_flight_number']);
                    $flightObj->save();
                    $flightInfo->setTransportId($flightObj->getId());
                    $flightInfo->save();
                } else if ($this->form_vars['flight_type'] == "sea") {
                    $seaObj = new Sea();
                    $seaObj->setShipNumber($this->form_vars['flight_number']);
                    $seaObj->save();
                    $flightInfo->setTransportId($seaObj->getId());
                    $flightInfo->save();
                } else {
                    $truckObj = new Truck();
                    $truckObj->setTruckNumber($this->form_vars['flight_number']);
                    $truckObj->save();
                    $flightInfo->setTransportId($truckObj->getId());
                    $flightInfo->save();
                }

                $outputArray['status'] = "success";
                $outputArray['message'] = "Flight Saved Successfully";
            } else {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Oops! something went wrong. Please try it again later";
            }
            echo json_encode($outputArray);
            die;
        }


        if (isset($_POST['form_action']) && $_POST['form_action'] == 'save') {

            $user = Sessionmanager::getUser();
            $id = !empty($this->form_vars["id"]) ? $this->form_vars["id"] : '';
            $flightInfoObj = new FlightInfo();
            if (!empty($id)) {
                $flightInfoObj = new FlightInfo($id);
            }
            $flightInfoObj->setFlightNumber($this->form_vars["flight"]);
            $flightInfoObj->setPieces($this->form_vars["pieces"]);
            $flightInfoObj->setCarriageValue(0.0);
            $flightInfoObj->setCustomValue(0.0);
            $flightInfoObj->setInsuranceAmount(0.0);
            $flightInfoObj->setWeight($this->form_vars["weight"]);
            if (empty($id))
                $flightInfoObj->setDateCreated(date("Y-m-d H:i:s"));
            if (!empty($this->form_vars['user_account_id'])) {
                $flightInfoObj->setAccountId($this->form_vars["user_account_id"]);
            } else {
                $flightInfoObj->setAccountId($this->user->getUserAccountId());
            }
            $flightInfoObj->setEtd($this->form_vars["etd"]);
            $flightInfoObj->setEta($this->form_vars["eta"]);
            $flightInfoObj->setStatus("in_transit");

            $flightInfoObj->setDateCreated(time());
            $flightInfoObj->setCleared('NO');
            $flightInfoObj->setIsDelete(0);
            $flightInfoObj->setStatus('not_assigned');
            if (isset($_FILES['upload_file']) && $_FILES['upload_file']['name']) {
                if (!file_exists('path/to/directory')) {
                    mkdir('../_assets/preadvice', 0777, true);
                }
                $errors = array();
                $file_name = $_FILES['upload_file']['name'];
                $file_size = $_FILES['upload_file']['size'];
                $file_tmp = $_FILES['upload_file']['tmp_name'];
                $file_type = $_FILES['upload_file']['type'];
                $file_ext = strtolower(end(explode('.', $_FILES['upload_file']['name'])));
                $file_name = time() . '-' . $file_name;
                if (empty($errors)) {
                    move_uploaded_file($file_tmp, "../_assets/preadvice/" . $file_name);
                }
                if (!empty($_POST['file_update'])) {
                    unlink("../_assets/preadvice/" . $_POST['file_update']);
                }

                $flightInfoObj->setFiles($file_name);
            }
            $flightInfoObj->setCreatedBy($user->getId());
            $userAccount = (!empty($this->form_vars["user_account_id"]) ? $this->form_vars["user_account_id"] : '');

            $flightInfoObj->save();
            $msg = [];
            $flightMapppingObj = new FlightMapping();
            $flightMappDelObj = new FlightMappingFilter();
            $flightMappDelObj->deleteOnFlightId($flightInfoObj->getId());
            $flightMapppingObj->setFlightInfoId($flightInfoObj->getId());
            $flightMapppingObj->setFlightNumber($this->form_vars["flight"]);
            $flightMapppingObj->setMawb($this->form_vars["mawb"]);
            $flightMapppingObj->setIsDelete(0);
            $flightMapppingObj->Save();
            if (!empty($flightInfoObj->getId())) {
                $msg['sucess'] = "<div class='col-md-12 alert alert-success'>Pre Alert Advice data has been saved.</div>";
            } else {
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>Pre Alert Advice data has not been save.</div>";
            }
            echo json_encode($msg);
            exit;
        }


        if (isset($_POST['func']) && $_POST['func'] == 'edit') {
            $id = $_POST["id"];
            $prealerfilter = new FlighInfoFilter();
            $preList = $prealerfilter->addIdFilter($id);
            $preAlerttData = [];
            $preAlerttData['mawb'] = $preList[0]->getMawb();
            $preAlerttData['flight'] = $preList[0]->getFlightNumber();
            $preAlerttData['pieces'] = $preList[0]->getPieces();
            $preAlerttData['weight'] = $preList[0]->getWeight();
            $preAlerttData['eta'] = $preList[0]->getEta();
            $preAlerttData['etd'] = $preList[0]->getEtd();
            $preAlerttData['file'] = $preList[0]->getFiles();
            $preAlerttData['comments'] = $preList[0]->getComments();
            $preAlerttData['shed'] = $preList[0]->getShed();
            $preAlerttData['status'] = $preList[0]->getStatus();
            $preAlerttData['currenct_status'] = $preList[0]->getCurrentStatus();
            $preAlerttData['arrived_date'] = date('m/d/Y', strtotime($preList[0]->getArrivedDate()));
            $preAlerttData['id'] = $preList[0]->getId();
            $preAlerttData['account'] = $preList[0]->getAccountId();
            echo json_encode($preAlerttData);
            exit;
        }

        /*
         * Fetch Flight Data 
         */
        if ($_POST['action'] && $_POST['action'] == 'fetchFlightData') {
            $html = "";
            $flightNumber = $this->form_vars["keyword"];
            if (trim($flightNumber) != '') {
            $flightFilter = new FlighInfoFilter();
            $flightFilter->addJoin("flight fl", "fl.id", "f.transport_id", "INNER JOIN");
            
            $flightFilter->addFilter("fl.flight_number like '" . $flightNumber . "%' and fl.flight_number != ''");
            
            $flightFilter->addGroupBy("fl.flight_number");
            $flightList = $flightFilter->getColumnList("distinct fl.flight_number, departure_airport, arrival_airport, iata_code, address_line_1, address_line_2, city, postcode, shippers_name, shippers_addressline1, shippers_addressline2, country_id, destination_country_id", 100);
            if (count($flightList) > 0) {
                $html = "";
                $html .= '<ul id="country-list">';
                foreach ($flightList as $flight) {
                    $fnumber = "'" . $flight->getFlightNumber() . "'";
                    $dAirport = "'" . $flight->getDepartureAirport() . "'";
                    $aAirport = "'" . $flight->getArrivalAirport() . "'";
                    $iataCode = "'" . $flight->getIataCode() . "'";
                    $destinationAddressLine1 = "'" . $flight->getAddressLine1() . "'";
                    $destinationAddressLine2 = "'" . $flight->getAddressLine2() . "'";
                    $destinationCity = "'" . $flight->getCity() . "'";
                    $destinationPostcode = "'" . $flight->getPostcode() . "'";
                    $shipperName = "'" . $flight->getShippersName() . "'";
                    $shipperAddressline1 = "'" . $flight->getShippersAddressLine1() . "'";
                    $shipperAddressLine2 = "'" . $flight->getShippersAddressLine2() . "'";
                    $shipperCountry = "'" . $flight->getCountryId() . "'";
                    $destinationCountry = "'" . $flight->getDestinationCountryId() . "'";
                    $html .= '<li onClick="selectFlight(' . $fnumber . ',' . $dAirport . ',' . $aAirport . ',' . $iataCode . ','. $destinationAddressLine1 .',' . $destinationAddressLine2 .','.$destinationCity.','.$destinationPostcode.','.$shipperName.','.$shipperAddressline1.','.$shipperAddressLine2.','.$shipperCountry.','.$destinationCountry.');">' . $flight->getFlightNumber() . '</li>';
                }
                $html .= '</ul>';
            }
           
            }
             echo $html;
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
        type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
        type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
        type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>

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
                                "url": "add_flight.php?action=prealert_datatable", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "from"},
                                {"data": "to"},
                                {"data": "closed"},
                                {"data": "status", "bSortable": false}
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

            function resetForm() {
                document.getElementById("add_assign_bag_to_flight").reset();
            }
            function selectFlight(flightNumber, departureAirport, arrivalAirport, IataCode, destinationAdd1, destinationAdd2, destinationCity, destinationPostcode, shipperName, shipperAdd1, shipperAdd2, shipperCountry, destinationCountry) {
                $("#flight_number").val(flightNumber);
                $("#airport_of_depature").val(departureAirport);
                $("#arrival_airport").val(arrivalAirport);
                $("#agent_iata_code").val(IataCode);
                $("#shipper_name").val(shipperName);
                $("#shipper_address_line1").val(shipperAdd1);
                $("#shipper_address_line2").val(shipperAdd2);
                $("#destination_addressline1").val(destinationAdd1);
                $("#destination_addressline2").val(destinationAdd2);
                $("#city").val(destinationCity);
                $("#postcode").val(destinationPostcode);
                $("#ship_from").val(shipperCountry).selectpicker('refresh');
                $("#shipped_to").val(destinationCountry).selectpicker('refresh');
                $("#suggesstion-box").hide();
            }
            $(document).ready(function () {

                DataTableFun.init();
                //  $('#etd').datetimepicker({dateFormat: 'd M yy H:i', step: 10});
                //    $('#eta').datetimepicker({dateFormat: 'd M yy H:i', step: 10});

                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();

                });


                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('#btnSaveNew').click(function () {
                    if ($('#mawb').val() != '' && $('#weight').val() != '' && $('#pieces').val() != '' && $('#eta').val() != '' && $('#etn').val() && $('#flight').val()) {
                        $('#pre_alert_form').trigger('submit');
                    }
                });

                $('#etd').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: false, autoclose: true});
                $('#eat').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: false, autoclose: true});

                $(document).on('click', '#btn_add_flight_detail', function () {
                    var check = 0;
                    if ($('#shipper_name').val() == '') {
                        $('.shipper_name_div').addClass('has-error');
                        $('#shipper_name').focus();

                        check = 1;
                    } else {
                        $('.shipper_name_div').removeClass('has-error');
                    }
                    if ($('#flight_number').val() == '') {
                        check = 1;
                        $('#flight_number').focus();
                        $('.flight_number_div').addClass('has-error');
                    } else {
                        $('.flight_number_div').removeClass('has-error');
                    }
                    if ($('#ship_from').val() == '') {
                        check = 1;
                        $('#ship_from').focus();
                        $('.ship_from_div').addClass('has-error');
                    } else {
                        $('.ship_from_div').removeClass('has-error');
                    }
                    if ($('#shipped_to').val() == '') {
                        check = 1;
                        $('#shipped_to').focus();
                        $('.shipped_to_div').addClass('has-error');
                    } else {
                        $('.shipped_to_div').removeClass('has-error');
                    }
                    if ($('#shipper_address_line1').val() == '') {
                        $('#shipper_address_line1').focus();
                        $('.shipper_address_line1_div').addClass('has-error');
                    } else {
                        $('.shipper_address_line1_div').removeClass('has-error');
                    }
                    if ($('#shipper_address_line2').val() == '') {
                        check = 1;
                        $('#shipper_address_line2').focus();
                        $('.shipper_address_line2_div').addClass('has-error');
                    } else {
                        $('.shipper_address_line2_div').removeClass('has-error');
                    }
                    if ($('#eat').val() == '') {
                        check = 1;
                        $('#eat').focus();
                        $('.eat_div').addClass('has-error');

                    } else {
                        $('.eat_div').removeClass('has-error');
                    }
                    if (check == 1) {
                        $('.alert-danger').show().text('Required Fields are empty.').delay(10000).fadeOut();
                        return false;
                    }
                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                    form_data.push({name: "action", value: 'add_flight_information'});
                    $.ajax({
                        type: "POST",
                        url: "add_flight.php",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 'success') {
                                grid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                                resetForm();
                                $("#ship_from").val("").selectpicker('refresh');
                                $("#shipped_to").val("").selectpicker('refresh');
                                
                            } else if (data.status == 'error') {
                                swal("Alert!", data.message, "info");
                            }
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });

                $(document).on('click', '.prealert_edit', function () {
        <?php if (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])) { ?>
                        var id = '<?= $_GET['id']; ?>';
        <?php } else { ?>
                        var id = $(this).data('id');
        <?php } ?>

                    $.post('pre-alert.php', {func: 'edit', id: id}, function (data) {
                        var obj = JSON.parse(data);
                        if (obj.id) {
                            $('#weight').val(obj.weight);
                            $('#pieces').val(obj.pieces);
                            $('#eta').val(obj.eta);
                            $('#etd').val(obj.etd);
                            $('#mawb').val(obj.mawb);
                            $('#flight').val(obj.flight);
                            $('#id').val(obj.id);
                            $("#user_account_id").val(obj.account).change();
                        }
                    });
                });
        <?php if (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])) { ?>
                    setTimeout(function () {
                        $('.prealert_edit').trigger('click');
                    }, 2000);

        <?php } ?>

                $("#flight_number").keyup(function () {
                    var flightNumber = $(this).val();
                    $.ajax({
                        type: "POST",
                        url: "add_flight.php",
                        data: {action: "fetchFlightData", keyword: flightNumber},
                        //data:'keyword='+$(this).val(),
                        beforeSend: function () {
                            $("#flight_number").css("background", "#FFF url(../images/LoaderIcon.gif) no-repeat 165px");
                        },
                        success: function (data) {
                            $("#suggesstion-box").show();
                            $("#suggesstion-box").html(data);
                            $("#flight_number").css("background", "#FFF");
                        }
                    });
                });
            });
            $("#flight_type").on('change', function () {
                var flightType = $(this).val();
                if (flightType == "sea") {
                    flightType = "Bill of Lading";
                }
                if (flightType != "flight") {
                    $("#connecting_flight_number").val("");
                    $("#connecting_flight_number_div").hide();
                    $("#agent_iata_code").val("");
                    $("#agent_iata_code_div").hide();
                    $("#airport_of_depature").val("");
                    $("#airport_of_depature_div").hide();
                    $("#arrival_airport").val("");
                    $("#arrival_airport_div").hide();
                } else {
                    $("#connecting_flight_number").val("");
                    $("#connecting_flight_number_div").show();
                    $("#agent_iata_code").val("");
                    $("#agent_iata_code_div").show();
                    $("#airport_of_depature").val("");
                    $("#airport_of_depature_div").show();
                    $("#arrival_airport").val("");
                    $("#arrival_airport_div").show();
                }
                $("#flight_type_div").html("");
                $("#flight_type_div").html(flightType.substr(0, 1).toUpperCase() + flightType.substr(1) + " Number");
            });


        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
//        $flightInfo = [];
        if (!empty($_GET['flight_id'])) {
            $flightInfo = new FlightInfo($_GET['flight_id']);
        }
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-plane" aria-hidden="true"></i>
                    Add/Update Dispatch
                </div>
                <div class="actions">
                    <a href="flight_list.php" class="btn blue" data-original-title="" title=""><span></span><i
                            class="fa fa-list"></i>&nbsp;List Dispatch</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form id="add_assign_bag_to_flight" name="add_assign_bag_to_flight" method="post">
                    <div class="alert alert-danger" style="display: none"></div>
                    <div class="portlet-body">
                        <div data-rail-color="blue" data-handle-color="blue" class="filter">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2>Shipper Details</h2>
                                </div>
                                <div class="col-md-4">
                                    <h2>Destination Details</h2>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <label class="label-account" ><span id="flight_type_div">Flight Number</span><span class="required"
                                                                                                                       style="color: #ff4238;"
                                                                                                                       aria-required="true"> * </span></label>
                                    <div class="form-group">
                                        <div class="input-group flight_number_div  frmSearch">
                                            <span class="input-group-addon"> <i class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="flight_number"
                                                   name="flight_number" placeholder="Flight Number" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getFlightNumber(); ?>"
                                                   rel="tooltip" data-original-title="flight_number"  tabindex="9"
                                                   required="required">
                                            <div id="suggesstion-box"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="airport_of_depature_div">
                                    <label class="label-account">Airport of Departure</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="airport_of_depature"
                                                   name="airport_of_depature"
                                                   placeholder="Airport of Depature" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getDepartureAirport(); ?>"
                                                   rel="tooltip" tabindex="21"
                                                   data-original-title="Airport of Depature">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="arrival_airport_div">
                                    <label class="label-account">Arrival Airport</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="arrival_airport"
                                                   name="arrival_airport"
                                                   placeholder="Arrival Airport" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getArrivalAirport(); ?>"
                                                   rel="tooltip" tabindex="33"
                                                   data-original-title="Arrival Airport">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4" id="connecting_flight_number_div">
                                    <label class="label-account">Connecting Flight Number</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="connecting_flight_number"
                                                   name="connecting_flight_number"
                                                   placeholder="Connecting Flight Number" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getConnectingFlightNumber(); ?>"
                                                   rel="tooltip" tabindex="10" data-original-title="connecting_flight_number">
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="col-md-4">
                                    <label class="label-account">Departure Date/Time</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-calendar"></i> </span>
                                            <input class="form-control" readonly name="flight_date" id="etd" type="text"
                                                   placeholder="Estimated Departure Time" rel="tooltip" tabindex="20"
                                                   value="<?php if (!empty($flightInfo)) echo date('Y-m-d H:i:s', strtotime($flightInfo->getEtd())); ?>"
                                                   data-original-title="ETD" required/>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <label class="label-account">Arrival Time <span class="required"
                                                                                    style="color: #ff4238;"
                                                                                    aria-required="true"> * </span></label>
                                    <div class="input-group eat_div"><span class="input-group-addon"> <i
                                                class="fa fa-calendar"></i> </span>
                                        <input class="form-control" readonly name="arrived_date" id="eat" type="text"
                                               placeholder="Estimated Arrival Time"
                                               value="<?php if (!empty($flightInfo)) echo date('Y-m-d h:i:s', strtotime($flightInfo->getEta())); ?>"
                                               rel="tooltip" tabindex="32" placeholder="ETD" data-original-title="ETD" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Dispatch Type
                                        <span class="required" style="color: #ff4238;" aria-required="true"> * </span>
                                    </label>
                                    <div class="form-group">
                                        <?php
                                        $themeArray = array('flight' => 'International', 'sea' => 'Sea', 'truck' => 'Domestic');
                                        echo Ddl::generateArrayDDL('flight_type', $themeArray, "", '', 'required class="form-control bs-select" tabindex="8" data-toggle="tooltip" data-placement="top" title="Select flight type" data-original-title="flight type" data-live-search="true" ');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4" id="agent_iata_code_div">
                                    <label class="label-account">Agent IATA Code</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="agent_iata_code"
                                                   name="agent_iata_code"
                                                   placeholder="Agent IATA Code" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getIataCode(); ?>"
                                                   rel="tooltip" tabindex="19"
                                                   data-original-title="Agent IATA Code">
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="col-md-4">
                                    <label class="label-account">Shipper's Co</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="shippers_co"
                                                   name="shippers_co"
                                                   placeholder="Shipper's Co" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getShipperCo(); ?>"
                                                   rel="tooltip" tabindex="1"
                                                   data-original-title="Shipper's Co">
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-md-4">
                                    <label class="label-account">Select Carrier</label>
                                    <div class="form-group">
                                        <div id="carriers_div">
                                        <?php
                                        $carrier_id = '';
                                        if (!empty($flightInfo)) {
                                            $carrier_id = $flightInfo->getCarrier();
                                        }
                                        ?>
                                        <?php echo Ddl::generateCarrierDDLWithImage('carriers', $carrier_id, 'id', ' class="bs-select form-control" tabindex="13" data-live-search="true"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Shipper's Name <span class="required"
                                                                                      style="color: #ff4238;"
                                                                                      aria-required="true"> * </span></label>
                                    <div class="input-group shipper_name_div"><span class="input-group-addon"> <i
                                                class="fa fa-ticket "></i> </span>
                                        <input class="form-control form-filter" id="shipper_name"
                                               name="shipper_name" placeholder="Shipper's Name" type="text"
                                               value="<?php if (!empty($flightInfo)) echo $flightInfo->getShippersName(); ?>"
                                               rel="tooltip" tabindex="2" data-original-title="shipper_name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Carriage Value</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-random"></i> </span>
                                            <input class="form-control form-filter" id="carriage_value"
                                                   name="carriage_value"
                                                   placeholder="Carriage Value" type="number"
                                                   value="<?php echo!empty($flightInfo) ? $flightInfo->getCarriageValue() : "0.00"; ?>"
                                                   rel="tooltip" tabindex="14"
                                                   data-original-title="Carriage Value">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Destination Company</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-thumb-tack"></i> </span>
                                            <input class="form-control form-filter" id="destination_company"
                                                   name="destination_company" placeholder="Company" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getCompany(); ?>"
                                                   rel="tooltip" tabindex="25" data-original-title="Company">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Shipper's Company <span class="required"
                                                                                         style="color: #ff4238;"
                                                                                         aria-required="true"> * </span></label>
                                    <div class="input-group shipper_name_div"><span class="input-group-addon"> <i
                                                class="fa fa-ticket "></i> </span>
                                        <input class="form-control form-filter" id="company"
                                               name="company" placeholder="Shipper's company" type="text"
                                               value="<?php if (!empty($flightInfo)) echo $flightInfo->getCompany(); ?>"
                                               rel="tooltip" tabindex="3" data-original-title="company">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Insurance Amount</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-thumb-tack"></i> </span>
                                            <input class="form-control form-filter" id="insurance_amount"
                                                   name="insurance_amount"
                                                   placeholder="Insurance Amount" type="number"
                                                   value="<?php echo!empty($flightInfo) ? $flightInfo->getInsuranceAmount() : "0.00"; ?>"
                                                   rel="tooltip" tabindex="15"
                                                   data-original-title="Insurance Amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account"> Destination Consignee C/O</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="consignee_co"
                                                   name="consignee_co"
                                                   placeholder="Consignee C/O" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getConsigneeCo(); ?>"
                                                   rel="tooltip" tabindex="26"
                                                   data-original-title="Consignee C/O">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Shipper's Addressline 1 <span class="required"
                                                                                               style="color: #ff4238;"
                                                                                               aria-required="true"> * </span></label>
                                    <div class="input-group shipper_address_line1_div"><span class="input-group-addon"> <i
                                                class="fa fa-ticket "></i> </span>
                                        <input class="form-control form-filter" id="shipper_address_line1"
                                               name="shipper_address_line1" placeholder="Shipper's Address Line 1"
                                               type="text"
                                               value="<?php if (!empty($flightInfo)) echo $flightInfo->getShippersAddressline1(); ?>"
                                               rel="tooltip" tabindex="4"
                                               data-original-title="shipper_address_line1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Currency</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-thumb-tack"></i> </span>
                                            <input class="form-control form-filter" id="currancy" name="currancy"
                                                   placeholder="Currency" type="number"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getCurrency(); ?>"
                                                   rel="tooltip" tabindex="16" data-original-title="Currency">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Destination Address Line 1</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-random"></i> </span>
                                            <input class="form-control form-filter" id="destination_addressline1"
                                                   name="destination_addressline1" placeholder="Address Line 1"
                                                   type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getAddressLine1(); ?>"
                                                   rel="tooltip"tabindex="27" data-original-title="Address Line 1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                 <div class="col-md-4">
                                    <label class="label-account">Shipper's Addressline 2 <span class="required"
                                                                                               style="color: #ff4238;"
                                                                                               aria-required="true"> * </span></label>
                                    <div class="input-group shipper_address_line2_div"><span class="input-group-addon"> <i
                                                class="fa fa-ticket "></i> </span>
                                        <input class="form-control form-filter" id="shipper_address_line2"
                                               name="shipper_address_line2" placeholder="Shipper's Address Line 2"
                                               type="text"
                                               value="<?php if (!empty($flightInfo)) echo $flightInfo->getShippersAddressline2(); ?>"
                                               rel="tooltip" tabindex="5"
                                               data-original-title="shipper_address_line2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Rate Change</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="rate_change"
                                                   name="rate_change"
                                                   placeholder="Rate Change" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getRateChange(); ?>"
                                                   rel="tooltip" tabindex="17"
                                                   data-original-title="Rate Change">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Address Line 2</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-random"></i> </span>
                                            <input class="form-control form-filter" id="destination_addressline2"
                                                   name="destination_addressline2"
                                                   placeholder="Address Line 2" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getAddressLine2(); ?>"
                                                   rel="tooltip" tabindex="28"
                                                   data-original-title="Address Line 2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="label-account">Ship From <span class="required"
                                                                                 style="color: #ff4238;"
                                                                                 aria-required="true"> * </span></label>
                                    <div class="form-group ship_from_div">
                                        <?php
                                        $country_id = '';
                                        if (!empty($flightInfo)) {
                                            $country_id = $flightInfo->getCountryId();
                                        }
                                        ?>
                                        <?php echo Ddl::generateCountryDDL('ship_from', $country_id, 'id', 'class="form-control bs-select" tabindex="7"  data-live-search="true"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Custom Value</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-map-marker"></i> </span>
                                            <input class="form-control form-filter" id="custom_value"
                                                   name="custom_value"
                                                   placeholder="Custom Value" type="number"
                                                   value="<?php echo!empty($flightInfo) ? $flightInfo->getCustomValue() : "0.00"; ?>"
                                                   rel="tooltip" tabindex="24"
                                                   data-original-title="Custom Value">
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="col-md-4">
                                    <label class="label-account">Account Number</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <input class="form-control form-filter" id="account_number"
                                                   name="account_number"
                                                   placeholder="Account Number" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getAccountNumber(); ?>"
                                                   rel="tooltip" tabindex="6"
                                                   data-original-title="Account Number">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Weight Format</label>
                                    <div class="form-group">
                                        <select id="weight_format" name="weight_format"
                                                class="form-filter bs-select form-control"
                                                title="" placeholder="" tabindex="18" aria-hidden="true"
                                                data-original-title="">
                                            <option value="kg">KG</option>
                                            <option value="gm">GM</option>
                                        </select>
                                    </div>
                                </div>-->
                                <div class="col-md-4">
                                    <label class="label-account">City</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                            <input class="form-control form-filter" id="city" name="city"
                                                   placeholder="City" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getCity(); ?>"
                                                   rel="tooltip" tabindex="29"
                                                   data-original-title="City">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="label-account">Reference</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                            <input class="form-control form-filter" id="reference" name="reference"
                                                   placeholder="Reference" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getReference(); ?>"
                                                   rel="tooltip" tabindex="22"
                                                   data-original-title="reference">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Post Code</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-map-marker"></i> </span>
                                            <input class="form-control form-filter" id="postcode" name="postcode"
                                                   placeholder="Postcode" type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getPostCode(); ?>"
                                                   rel="tooltip" tabindex="30"
                                                   data-original-title="Postcode">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="label-account">Airway Bill</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                            <textarea class="form-control form-filter" id="airway_bill"
                                                      name="airway_bill" placeholder="Airway Bill" rel="tooltip" tabindex="23"
                                                      data-original-title="Airway Bill"><?php if (!empty($flightInfo)) echo $flightInfo->getAirwayBill(); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Country <span class="required" style="color: #ff4238;"
                                                                               aria-required="true"> * </span></label>
                                    <div class="form-group shipped_to_div">
                                    <?php
                                    $destination_country_id = '';
                                    if (!empty($flightInfo)) {
                                        $destination_country_id = $flightInfo->getDestinationCountryId();
                                    }
                                    ?>
                                    <?php echo Ddl::generateCountryDDL('shipped_to', $destination_country_id, 'id', 'class="form-control bs-select" tabindex="31" data-live-search="true"'); ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="label-account">Comments</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-credit-card"></i> </span>
                                           <textarea class="form-control form-filter" id="comments"
                                                      name="comments" placeholder="Comments" rel="tooltip" tabindex="23"
                                                      data-original-title="Comments"><?php if (!empty($flightInfo)) echo $flightInfo->getComments(); ?></textarea>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="label-account">Phone Number</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa  fa-thumb-tack"></i> </span>
                                            <input class="form-control form-filter" id="destination_phone_number"
                                                   name="destination_phone_number" placeholder="Phone Number"
                                                   type="text" tabindex="32"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getDestinationPhoneNumber(); ?>"
                                                   rel="tooltip" data-original-title="Phone Number">
                                        </div>
                                    </div>
                                </div>
                                
                               
                            </div>
<!--                            <div class="row">
                                
                                
                                
                                <div class="col-md-4">
                                    <label class="label-account">Signature of issuing Carrier</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="signature_carrier"
                                                   name="signature_carrier"
                                                   placeholder="Signature of issuing Carrier"
                                                   type="text" tabindex="11"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getSignature(); ?>"
                                                   rel="tooltip"
                                                   data-original-title="signature_carrier">
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                            
                                <div class="col-md-4">
                                    <label class="label-account">Accounting Reference</label>
                                    <div class="form-group">
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-globe"></i> </span>
                                            <input class="form-control form-filter" id="accounting_reference"
                                                   name="accounting_reference" placeholder="Accounting Reference"
                                                   type="text"
                                                   value="<?php if (!empty($flightInfo)) echo $flightInfo->getAccountingReference(); ?>"
                                                   rel="tooltip" tabindex="12"
                                                   data-original-title="accounting_reference">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>-->
                        </div>
                    </div>
                    
                    <div class="portlet-body">
                        <div data-rail-color="blue" data-handle-color="blue" class="filter">
                          
                        <?php if (!empty($flightInfo)) { ?>
                            <input type="hidden" value="<?php echo $flightInfo->getId(); ?>" name="flight_id">
                        <?php }        ?>
                            <div class="row">
                                <div class="col-md-12" style="text-align:center;">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary filter-submit" name="btn_go"
                                                id="btn_add_flight_detail" tabindex="35" value="Submit"> Submit
                                        </button>&nbsp;
                                        <button type="button" class="btn btn-default filter-cancel" name="btn_rest"
                                                value="Reset" onclick="resetForm()"> Reset
                                        </button>
                                        <!-- <button type="button" class="btn btn-danger" id="btn_more_option" name="btn_more_option" onclick="$('#more-option').toggle();"> More Option </button>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
