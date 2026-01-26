<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'flightinfofilter.class',
    'flightinfo.class',
    'country.class',
    'countryfilter.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'mawb.class',
    'mawbfilter.class',
     'bagging.class',
    'baggingfilter.class',
]);
/* * *
 * Page for editing a user
 */

class Page extends BasePage
{

    private $uploadfilelist = "";
    private $user;

    protected function init()
    {
        $this->user = SessionManager::getUser();
//        if(!Permissions::checkFilePermission('flight_list.php')) {
//            header('Location: ' . BASE_URL);
//        }
        
          $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Manage Flights'
        );
        if ($_GET['action'] && $_GET['action'] == 'prealert_datatable') {
            $flightFilterObj = new FlighInfoFilter();
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                if ($dataTableColumnName == "search_etd")
                    $dataTableColumnName = "fi.etd";

                if ($dataTableColumnName == "search_eta")
                    $dataTableColumnName = "fi.eta";
                $flightFilterObj->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                if (!empty($this->form_vars['flight_number'])) {
                    $flightFilterObj->addFilter("   fi.flight_number LIKE   '%" . $this->form_vars['flight_number'] . "%'");
                }
                if (!empty($this->form_vars['search_status'])) {
                    $flightFilterObj->addFilter("   fi.status =  '" . $this->form_vars['search_status'] . "'");
                }
                if (!empty($this->form_vars['from_country'])) {
                    $flightFilterObj->addFilter("     fi.country_id =  '" . $this->form_vars['from_country'] . "'");
                }
                if (!empty($this->form_vars['to_country'])) {
                    $flightFilterObj->addFilter("     fi.destination_country_id =  '" . $this->form_vars['to_country'] . "'");
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
                $searchEtd = $this->form_vars['search_etd'];
                if (!empty($searchEtd))
                    $flightFilterObj->addFromFilter("    DATE_FORMAT( fi.etd, '%Y-%m-%d') ", date('Y-m-d', strtotime($searchEtd)));
                $searchEta = $this->form_vars['search_eta'];
                if (!empty($searchEta))
                    $flightFilterObj->addToFilter("    DATE_FORMAT( fi.eta, '%Y-%m-%d') ", date('Y-m-d', strtotime($searchEta)));
            }
            /*            if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                            $flightFilterObj->addFilter("   fi.account_id = '".$this->user->getUserAccountId()."'");
                        }*/

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
                  /*  $currentArr['edit'] = "<a href='add_flight.php?flight_id=" . $preData->getId() . "' class='btn btn-xs btn-default blue btn-outline center prealert_edit' rel='tooltip' data-toggle='tooltip' placeholder='Edit' title='Edit' data-id='" . $preData->getId() . "'> <span class='fa fa-pencil'></span> </a>
                            <a href='mawb_flight_view.php?flight_id=" . $preData->getId() . "' data-toggle='tooltip' title='View' class='btn btn-xs btn-default blue btn-outline center'><span class='fa fa-eye'></span></a>
                            <a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' rel='tooltip'  placeholder='View Audit' data-log_key='" . $preData->getId() . "' 
                                 data-log_name='flight_info' data-toggle='modal'> <span class='fa fa-list'></span> 
                                 </a>";*/
                    
                $currentArr['edit'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';

                    $currentArr['edit'] .= "<li>
                                                 <a href='add_flight.php?flight_id=" . $preData->getId() . "' class='prealert_edit'  title='Edit' data-id='" . $preData->getId() . "'>  <span class='glyphicon glyphicon-pencil'></span> Edit Flight </a>
                                            </li>";


                    $currentArr['edit'] .= "<li>
                                                 <a href='mawb_flight_view.php?flight_id=" . $preData->getId() . "' data-toggle='tooltip' title='View' class=''><span class='fa fa-eye'></span> View Flight</a>
                                            </li>";


                    $currentArr['edit'] .= "<li>  <a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' rel='tooltip'  placeholder='View Audit' data-log_key='" . $preData->getId() . "' 
                                 data-log_name='flight_info' data-toggle='modal'> <span class='fa fa-list'></span>  View Audit
                                 </a></li>";
                    
 
                    
                    if ($preData->getIsClosed() == 0) {
               
                    } else {
                        $currentArr['edit'] .=  "<li>  <a href='javascript:void(0)' class='reopen-flight' data-isclosed='".$preData->getIsClosed()."' data-flight='".$preData->getFlightNumber()."'   data-id='" . $preData->getId() . "'   > <span class='fa fa-retweet'></span> Reopen Flight
                                 </a></li>";
                    }
                    
                    $currentArr['edit'] .= '</ul>
                                        </div>';
 
                    
                    
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $currentArr['eta'] = !empty($preData->getEta()) ? formatDate(date("Y-m-d",strtotime($preData->getEta()))) : '';
                    $currentArr['etd'] = !empty($preData->getEtd()) ? formatDate(date("Y-m-d",strtotime($preData->getEtd()))) : '';
                    $currentArr['status'] = $status;
                    $toCountry = new Country($preData->getDestinationCountryId());
                    $fromCountry = new Country($preData->getCountryId());
                    $currentArr['to'] = $toCountry->getName();
                    $currentArr['from'] = $fromCountry->getName();
                    if ($preData->getIsClosed() == 0) {
                        $currentArr['closed'] = "<span class='label label-success label-sm'>Open</span>";
                    } else {
                        $currentArr['closed'] = "<span class='label label-danger label-sm'>Close</span>";
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
            $flightInfo = new FlightInfo();
            $flightInfo->setShippersName($this->form_vars['shipper_name']);
            $flightInfo->setShippersAddressline1($this->form_vars['shipper_address_line1']);
            $flightInfo->setShippersAddressline2($this->form_vars['shipper_address_line2']);
            $flightInfo->setAddressline1($this->form_vars['destination_addressline1']);
            $flightInfo->setAddressline2($this->form_vars['destination_addressline2']);
            $flightInfo->setDestinationCountryId($this->form_vars['shipped_to']);
            $flightInfo->setCountryId($this->form_vars['ship_from']);
            $flightInfo->setFlightNumber($this->form_vars['flight_number']);
            $flightInfo->setConnectingFlightNumber($this->form_vars['connecting_flight_number']);
            $flightInfo->setSignature($this->form_vars['signature_carrier']);
            $flightInfo->setAccountingReference($this->form_vars['accounting_reference']);
            $flightInfo->setReference($this->form_vars['reference']);
            $flightInfo->setCustomValue($this->form_vars['custom_value']);
            $flightInfo->setCarrier($this->form_vars['carriers']);
            $flightInfo->setCarriageValue($this->form_vars['carriage_value']);
            $flightInfo->setInsuranceAmount($this->form_vars['insurance_amount']);
            $flightInfo->setCurrancy($this->form_vars['currancy']);
            $flightInfo->setRateChange($this->form_vars['rate_change']);
            $flightInfo->setWeightType($this->form_vars['weight_format']);
            $flightInfo->setIataCode($this->form_vars['agent_iata_code']);
            $flightInfo->setFlightDate($this->form_vars['flight_date']);
            $flightInfo->setShipperCo($this->form_vars['shippers_co']);
            $flightInfo->setAccountNumber($this->form_vars['account_number']);
            $flightInfo->setAirwayBill($this->form_vars['airway_bill']);
            $flightInfo->setHscodes($this->form_vars['hs_codes']);
            $flightInfo->setArrivalAirport($this->form_vars['airport_of_depature']);
            $flightInfo->setCity($this->form_vars['city']);
            $flightInfo->setPostcode($this->form_vars['postcode']);
            $flightInfo->setPostcode($this->form_vars['postcode']);
            $flightInfo->setPhoneNumber($this->form_vars['phone_number']);
            $flightInfo->setCompany($this->form_vars['company']);
            $flightInfo->setConsigneeCo($this->form_vars['consignee_co']);
            $flightInfo->setIsDelete(0);
            $flightInfo->setIsClosed(0);
            $flightInfo->setCreatedBy($sessionUser->getId());
            $flightInfo->save();
            $outputArray['status'] = "success";
            $outputArray['message'] = "Flight Saved Successfully";
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
        
        if (isset($_POST['action']) && $_POST['action'] == 'update_flight_status') {
            $msg = [];
            $updatedStatus = "";
            $user = Sessionmanager::getUser();
            $id = !empty($this->form_vars["flightId"]) ? $this->form_vars["flightId"] : '';
            $mawbArr = [];
            if (!empty($id)) {
                $flightInfoObj = new FlightInfo($id);
                
                
                  
            if ($flightInfoObj->getIsClosed() == 0) {
                    //do nothing
                  $msg['status'] = "error";
                           $msg['message'] = "Flight already opened.";
            } else {
                
                /////////OPEN FLIGHT///////////
                $flightInfoObj->setIsClosed(0);
                $updatedStatus = "Reopened";
                $flightInfoObj->save();
                $flighInfoFilter = new FlightMappingFilter();
                $flighInfoFilter->addFieldFilter("    flight_info_id", $id);
                $flighInfoFilter->addGroupBy("mawb_id");
                $flighInfoFilterObj = $flighInfoFilter->getList();
                if (count($flighInfoFilterObj) > 0) {
                    foreach ($flighInfoFilterObj as $flighInfoMapping) {
                        $mawbArr[] = $flighInfoMapping->getMawbId();
                    }
                }
                
                /////////OPEN Mawb number///////////
                $mawbFilter = new MawbFilter();
                $mawbFilter->addFilterIn("id", $mawbArr);
                $mawbResObj = $mawbFilter->getList();
                if(count($mawbResObj)>0){
                    foreach($mawbResObj as $mwbs){
                        $mwbs->setMawbStatus('o');
                        $mwbs->save();  
                    }
                }
                
                        /////////OPEN BAGS ///////////
                        $mawbBags = new MawbParcelMappingFilter();
                        $mawbBags->addFilterIn('      mawb_id  ' , $mawbArr);
                        $mawbBags->addFilter(' bag_id > 0 ');
                        $mawbBags->addGroupBy('bag_id');
                        $mawbBags = $mawbBags->getList();
                    
                        $openBags = 0;
                        if (!empty($mawbBags)) {
                            foreach ($mawbBags as $mawbBag) {
                                $bagging = new Bagging($mawbBag->getBagId());
                                $bagging->setIsClosed(0);
                                $bagging->save();
                            }
                             
                        }
                            $msg['status'] = "success";
                           $msg['message'] = "Flight status $updatedStatus successfully";
                        }

            }else{
          
                    $msg['status'] = "error";
                    $msg['message'] = "Flight status not opened. There is some technical issue. Please contact to itsupport@oneworldexpress.com.";
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
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
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
                                "url": "flight_list.php?action=prealert_datatable", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "from"},
                                {"data": "to"},
                                {"data": "etd"},
                                {"data": "eta"},
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

            $(document).ready(function () {

                DataTableFun.init();
                $('#etd').datetimepicker({dateFormat: 'd M yy H:i', step: 10});
                $('#eta').datetimepicker({dateFormat: 'd M yy H:i', step: 10});

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

                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        dateFormate: "yyyy-mm-dd"
                    });
                }

                $(document).on('click', '#btn_add_flight_detail', function () {
                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                    form_data.push({name: "action", value: 'add_flight_information'});
                    $.ajax({
                        type: "POST",
                        url: "add_flight.php",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if(data.status == 'success') {
                                grid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                                resetForm();
                            } else if(data.status == 'error') {
                                swal("Alert!", data.message, "info");
                            }
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });


        $('#manage-data-table tbody').on('click', '.reopen-flight', function () {
           
         
                  var vdata = grid.getDataTable().row($(this).parents('tr')).data();
                console.log( vdata.flight );
              
                    var flightId = $(this).data('id');
                    var isclosed = $(this).data('isclosed');
                    var flightNumber = vdata.flight;
                    var flightArrival = vdata.eta;
                    var flightAction = 'Reopen';
                    if(isclosed=='1'){
                          flightAction = 'Reopen';
                    }else{
                          flightAction = 'Close';
                    }
                    
                    swal({
                            title: "Are you sure to "+flightAction+" flight "+flightNumber+" \n  Arrival "+flightArrival+" ?",
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
                                        url: "flight_list.php",
                                        data: {
                                            action: 'update_flight_status',
                                            flightId: flightId,
                                        },
                                        dataType: "json",
                                        success: function (rdata) {
                                             if(rdata.status == 'success') {
                                                grid.getDataTable().ajax.reload();
                                                swal("Success!", rdata.message, "success");
                                            } else if(rdata.status == 'error') {
                                                swal("Alert!", rdata.message, "info");
                                            }
                                        },
                                        error: function () {
                                            //alert('error handing here');
                                        }
                                    });
                            }
                        });
                });
                
            
    
                $(document).on('click', '.prealert_edit', function () {
                    <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                    var id = '<?= $_GET['id'];?>';
                    <?php }else {?>
                    var id = $(this).data('id');
                    <?php }?>

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
                <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                setTimeout(function () {
                    $('.prealert_edit').trigger('click');
                }, 2000);

                <?php }?>
            });
        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        $sessionUser = SessionManager::getUser();
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-plane" aria-hidden="true"></i>
                    All flight List
                </div>
                <div class="actions">
                    <a href="add_flight.php" class="btn blue" data-original-title="" title=""><span></span><i class="fa fa-plus"></i>&nbsp;Add Flight</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Edit</th>
                            <th>Flight#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure Date</th>
                            <th>Arrival Date</th>
                            <th>Closed</th>
                            <th>Status</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit">
                                    <i class="fa fa-search"></i></button>
                                <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom">
                                    <i class="fa fa-times"></i></button>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="flight_number"
                                       id="flight_number">
                            </td>
                            <td>
                                <div>
                                    <?php echo Ddl::generateCountryDDL('from_country', '', 'id'); ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?php echo Ddl::generateCountryDDL('to_country', '', 'id'); ?>
                                </div>
                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="search_etd" placeholder="Departure Date" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="search_eta" placeholder="Arrival Date" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                </div>
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
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
