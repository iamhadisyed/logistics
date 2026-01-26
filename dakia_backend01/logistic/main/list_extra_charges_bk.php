<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $extra_charge_id = 0;
    private $user_id;
    private $charges_list;
    private $service_id;
    private $sur_charge;
    private $sur_charge_type;
    private $extra_charge;
    private $extra_charge_type;
    private $discount;
    private $discount_type;
    private $displayname;

    protected function init() {
        $user = SessionManager::getUser();
        $this->user_id = 0;
        if (!empty($_GET['id']) || !empty($_POST)) {
            $this->user_id = trim($_GET['id']);
            $userAccount = new CustomerAccount($this->user_id);
            $this->displayname = $userAccount->getUserAccount();
        } else {
            util_redirect('customers.php');
        }
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'customers_details.php?id=' . $this->user_id  => Translation::GetCaption("ACCOUNTS"),
            Translation::GetCaption("SERVICE_CHARGES")
        );
        /*
         * Handle Data Table 
         */
        
        if (isset($_GET['action']) && $_GET['action'] == 'list_extra_charges_ajax') {
            $userServicesChargesFilter = new UserServicesChargesFilter();
            $userServicesChargesFilter->addServicesJoin();
            $userServicesChargesFilter->addUserAccountIdFilter($this->user_id);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchName = $this->form_vars['name'];
                if (!empty($searchName))
                    $userServicesChargesFilter->addServiceNameFilter('name', $searchName);

                $surCharge = $this->form_vars['sur_charge'];
                if (!empty($surCharge))
                    $userServicesChargesFilter->addFieldFilter('sur_charge', $surCharge);


                $extraCharge = $this->form_vars['extra_charge'];
                if (!empty($extraCharge))
                    $userServicesChargesFilter->addFieldFilter('extra_charge', $extraCharge);

                $overWeight = $this->form_vars['over_weight'];
                if (!empty($overWeight))
                    $userServicesChargesFilter->addFieldFilter('over_weight', $overWeight);

                $overSize = $this->form_vars['over_size'];
                if (!empty($overSize))
                    $userServicesChargesFilter->addFieldFilter('over_size', $overSize);

                $discount = $this->form_vars['discount'];
                if (($discount) != '') {
                    $userServicesChargesFilter->addFieldFilter('discount', $discount);
                }

                $additionalCharges = $this->form_vars['additional_charges'];
                if (($additionalCharges) != '') {
                    $userServicesChargesFilter->addFieldFilter('additional_charges', $additionalCharges);
                }
            }
            /*
             * Set columns orders 
             * For sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if($dataTableColumnName == "Name"){
                    $userServicesChargesFilter->AddOrderBy('s.name', $orderFalse);
                }else{
                    $userServicesChargesFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $userServicesChargesFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $userServicesChargesFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $userServicesChargesFilter->setOffset($iDisplayStart);
            $serviceObjs = $userServicesChargesFilter->getPagingList();
            $serviceDataArr = array();
            $serviceDataListArr = array();
            foreach ($serviceObjs as $chargesList) {
                $chargesListArr = array();
                $name = $chargesList->getServiceId();
                $chargesListArr['option'] = '<a href="javascript:void(0);" data-cid="' . $chargesList->getId() . '" data-uid="' . $this->user_id . '" class="btndelete btn btn-xs red btn-outline"><span class="fa fa-trash"></span></a>'
                        . '<a class="btnedit btn btn-xs blue btn-outline" href="javascript:void(0);" data-uid="' . $this->user_id . '" data-cid="' . $chargesList->getId() . '"><span class="glyphicon glyphicon-pencil"></span></a>'
                        . ' <a class="btn btn-xs blue btn-outline poload"  href="javascript:void(0);" data-uid="' . $this->user_id . '" data-cid="' . $chargesList->getId() . '" data-poload="list_extra_charges.php" title="Ancillary Charge" ><span class="glyphicon glyphicon-screenshot"></span></a>';
                $chargesListArr['name'] = $name;
                $chargesListArr['sur_charge'] = $chargesList->getSurCharge() . ($chargesList->getSurChargeType() == 'percentage' ? '%' : '');
                $chargesListArr['extra_charge'] = $chargesList->getExtraCharge() . ($chargesList->getExtraChargeType() == 'percentage' ? '%' : ($chargesList->getExtraChargeType() == 'fixed' ? '&pound;' : 'Per Kg'));
                $chargesListArr['over_weight'] = $chargesList->getOverWeight() . ($chargesList->getOverWeightType() == 'percentage' ? '%' : ($chargesList->getOverWeightType() == 'fixed' ? '&pound;' : 'Per Pcs'));
                $chargesListArr['over_size'] = $chargesList->getOverSize() . ($chargesList->getOverSizeType() == 'percentage' ? '%' : ($chargesList->getOverSizeType() == 'fixed' ? '&pound;' : 'Per Pcs'));
                $chargesListArr['discount'] = $chargesList->getDiscount() . ($chargesList->getDiscountType() == 'percentage' ? '%' : '');
                $chargesListArr['additional_charges'] = $chargesList->getAdditionalCharges() . ($chargesList->getAdditionalChargesType() == 'percentage' ? '%' : 'Per Kg');
                $serviceDataListArr [] = $chargesListArr;
            }
            $serviceDataArrJson['data'] = $serviceDataListArr;
            $serviceDataArrJson['draw'] = $sEcho;
            $serviceDataArrJson['recordsTotal'] = $iTotalRecords;
            $serviceDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($serviceDataArrJson);
            die;
        }
        if (isset($_POST['func']) && $_POST['func'] == 'GET_DATA_EXTRA_CHARGES') {
            $cid = $_POST['cid'];
            $userServicesChargesData = new UserServicesCharges($cid);
            $output = '<table id="booking-table" class="table table-striped table-bordered table-advance table-hover">  ';
            $output .= '<tbody>';
            if (!empty($userServicesChargesData)) {
                $jsonArrayData = $userServicesChargesData->getAdditionalChargesDetails();
                if (trim($jsonArrayData) != '') {
                    $jsonArrayData = (trim($jsonArrayData));
                    $dataArray = json_decode($jsonArrayData);
                } else {
                    $dataArray = array();
                }
                if (count($dataArray) > 0) {

                    foreach ($dataArray as $key => $value) {
                        $output .= '<tr> <td>' . ucwords(str_replace('_', ' ', $key)) . '</td><td>' . $value . '</td><td>Per-Kg</td></tr>';
                    }
                } else {

                    $output .= '<tr> <td>No charges found</td></tr>';
                }
            } else {

                $output .= '<tr> <td>No record found</td></tr>';
            }
            $output .= '</tbody>   
                        </table>';
            echo $output;
            exit;
        }
        t_on(); // turn on trace for this page
        // common initialisation for ths page
        $error_array = array();
        if (isset($this->form_vars["form_action"]) && !empty($this->form_vars["form_action"])) {
            if(!empty($this->form_vars["service_id"])){
                $this->service_id = $this->form_vars["service_id"];
                $userId = $this->form_vars["user_id"];
                $this->extra_charge_id = $this->form_vars["extra_charge_id"];
                $this->sur_charge = $this->form_vars["sur_charge"];
                $this->sur_charge_type = $this->form_vars["sur_charge_type"];
                $this->extra_charge = $this->form_vars["extra_charge"];
                $this->extra_charge_type = $this->form_vars["extra_charge_type"];
                $this->discount = $this->form_vars["discount"];
                $this->discount_type = $this->form_vars["discount_type"];
                $this->over_weight = $this->form_vars["over_weight"];
                $this->over_weight_type = "fixed";
                if (!empty($this->form_vars["over_weight_type"]))
                    $this->over_weight_type = $this->form_vars["over_weight_type"];

                $this->over_size = $this->form_vars["over_size"];
                $this->over_size_type = "fixed";
                if (!empty($this->form_vars["over_size_type"]))
                    $this->over_size_type = $this->form_vars["over_size_type"];

                $this->additional_charges = $this->form_vars["additional_charges"];
                $this->additional_charges_details = $this->form_vars["additional_charges_details"];
                if (trim($this->additional_charges_details) == '') {
                    $this->additional_charges_details == '{}';
                }
                $this->additional_charges_type = "fixed";
                if (!empty($this->form_vars["additional_charges_type"]))
                    $this->additional_charges_type = $this->form_vars["additional_charges_type"];


                $is_service_exists = false;
                $userServicesChargesFilter = new UserServicesChargesFilter();
                $userServicesChargesFilter->addUserAccountIdFilter($userId);
                $userServicesChargesFilter->addFieldFilter("service_id",$this->service_id);
                $service_list = $userServicesChargesFilter->getColumnList('service_id');
                if (count($service_list) > 0) {
                    $is_service_exists = true;
                }

                if ($this->extra_charge_id == 0) {
                    if ($is_service_exists) {
                        $error_array[] = formatMessages(ERROR_SERVICE_CHARGES);
                    } else {
                        $serviceData = new UserServicesCharges();
                        $serviceData->setUserAccountId($userId);
                        $serviceData->setServiceId(trim($this->service_id));
                        $serviceData->setSurCharge(trim($this->sur_charge));
                        $serviceData->setSurChargeType(trim($this->sur_charge_type));
                        $serviceData->setExtraCharge(trim($this->extra_charge));
                        $serviceData->setExtraChargeType(trim($this->extra_charge_type));
                        $serviceData->setDiscount(trim($this->discount));
                        $serviceData->setDiscountType(trim($this->discount_type));
                        $serviceData->setOverSize(trim($this->over_size));
                        $serviceData->setOverSizeType(trim($this->over_size_type));
                        $serviceData->setOverWeight(trim($this->over_weight));
                        $serviceData->setOverWeightType(trim($this->over_weight_type));

                        $serviceData->setAdditionalCharges(trim($this->additional_charges));
                        $serviceData->setAdditionalChargesDetails(trim($this->additional_charges_details));
                        $serviceData->setAdditionalChargesType(trim($this->additional_charges_type));

                        $serviceData->setLastUpdated(date('Y-m-d H:i:s'));
                        $serviceData->save();
                        $msg['status'] = "success";
                    }
                } else {
                    if ($is_service_exists && $service_list[0]->getId() == $this->extra_charge_id) {
                        $serviceData = new UserServicesCharges($this->extra_charge_id);
                        $serviceData->setServiceId(trim($this->service_id));
                        $serviceData->setSurCharge(trim($this->sur_charge));
                        $serviceData->setSurChargeType(trim($this->sur_charge_type));
                        $serviceData->setExtraCharge(trim($this->extra_charge));
                        $serviceData->setExtraChargeType(trim($this->extra_charge_type));
                        $serviceData->setDiscount(trim($this->discount));
                        $serviceData->setDiscountType(trim($this->discount_type));
                        $serviceData->setAdditionalCharges(trim($this->additional_charges));
                        $serviceData->setOverSize(trim($this->over_size));
                        $serviceData->setOverSizeType(trim($this->over_size_type));
                        $serviceData->setOverWeight(trim($this->over_weight));
                        $serviceData->setOverWeightType(trim($this->over_weight_type));

                        $serviceData->setAdditionalChargesDetails(trim($this->additional_charges_details));
                        $serviceData->setAdditionalChargesType(trim($this->additional_charges_type));

                        $serviceData->setLastUpdated(date('Y-m-d H:i:s'));
                        $serviceData->save();
                        $msg['status'] = "success";
                    } else {
                        $error_array[] = formatMessages(ERROR_SERVICE_CHARGES);
                    }
                }
                if (empty($error_array)) {
                    echo json_encode($msg);
                } else {
                    $errors = "";
                    foreach($error_array as $er) {
                        $errors .= $er . "<br >";
                    }
                    $msg['status'] = $errors;
                    echo json_encode($msg);
                }
            } else {
                $msg['status'] = "error";
                $msg['message'] = "Please select service";
                echo json_encode($msg);
            }
            die;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'extra_list_edit') {
            $charge_id = $_POST['c_id'];
            $returnJson = array();
            $chergeObj = new UserServicesCharges($charge_id);
            $returnJson['extra_charge_id'] = $chergeObj->getId();
            $returnJson['service_id'] = $chergeObj->getServiceId();
            $returnJson['sur_charge'] = $chergeObj->getSurCharge();
            $returnJson['sur_charge_type'] = $chergeObj->getSurChargeType();
            $returnJson['extra_charge'] = $chergeObj->getExtraCharge();
            $returnJson['extra_charge_type'] = $chergeObj->getExtraChargeType();
            $returnJson['discount'] = $chergeObj->getDiscount();
            $returnJson['discount_type'] = $chergeObj->getDiscountType();
            $returnJson['additional_charges'] = $chergeObj->getAdditionalCharges();
            $returnJson['additional_charges_details'] = $chergeObj->getAdditionalChargesDetails();
            $returnJson['additional_charges_type'] = $chergeObj->getAdditionalChargesType();
            $returnJson['over_size'] = $chergeObj->getOverSize();
            $returnJson['over_size_type'] = $chergeObj->getOverSizeType();
            $returnJson['over_weight'] = $chergeObj->getOverWeight();
            $returnJson['over_weight_type'] = $chergeObj->getOverWeightType();
            echo json_encode($returnJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $serviceId = $this->form_vars['service_id'];
            if ($serviceId > 0) {
                $serviceObj = new UserServicesCharges($serviceId);
                $serviceObj->delete();
                $returnMsg['STATUS'] = "success";
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    $("#bookingForm").serialize();
                    $.post('list_extra_charges.php', $("#bookingForm").serialize(), function (response) {
                        if (response.status == "success") {
                            $("#success_message").addClass("alert-success");
                            $("#success_message").removeClass("alert-danger");
                            $("#success_message").html("Data saved successfully");
                            $("#success_message").show();
                            // Reload data table command
                            grid.getDataTable().ajax.reload();
                        } else {
                            $("#success_message").removeClass("alert-success");
                            $("#success_message").addClass("alert-danger");
                            $("#success_message").html(response.status);
                            $("#success_message").show();
                        }
                    }, "json");
                });
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();

                $(document).on('click', '#updateAdditionalPricingData', function () {
                    var customer_total = 0.00;
                    var jsonData = [];
                    $(".customer_charges").each(function () {
                        var val = parseFloat($.trim($(this).val()));
                        val = (val == '' ? 0 : val);
                        var key = $(this).data('key');
                        customer_total += parseFloat(val);
                        jsonData.push('"' + key + '":' + val);
                    });
                    if (jsonData.length > 0) {
                        jsonData = "{" + jsonData.join(",") + "}";
                    }
                    customer_total = parseFloat(customer_total).toFixed(2);
                    $("#additional_charges_details").val(jsonData);
                    $("#additional_charges").val(customer_total);
                    swal("Success!", "Ancillary charges calculated successfully", "success");
                });
                $(document).on('click', '#btnMoreOption', function () {
                    var tmp = $("#additional_charges_details").val();
                    if ($.trim(tmp) != '') {
                        var obj = JSON.parse($("#additional_charges_details").val());
                        $(".customer_charges").each(function () {
                            var key = $(this).data('key');
                            if (jQuery.type(obj[key]) != 'undefined')
                            {
                                var fieldValue = parseFloat(obj[key]).toFixed(2);
                                $(this).val(fieldValue);
                            }
                        });
                    }
                });
                $(document).on('click', '.poload', function () {
                    var e = $(this);
                    var cid = e.data('cid');
                    var url = e.data('poload');
                    e.off('click');
                    $.post(url, {func: 'GET_DATA_EXTRA_CHARGES', cid: cid}, function (d) {
                        swal({html: true, title: '<i>Ancillary Charge</i>', text: d});
                    });
                });

            });
        </script>
        <!--Hadi Code-->
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
                                "url": "list_extra_charges.php?action=list_extra_charges_ajax&id=<?php echo trim($_GET['id']); ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "name"},
                                {"data": "sur_charge"},
                                {"data": "extra_charge"},
                                {"data": "over_weight"},
                                {"data": "over_size"},
                                {"data": "discount"},
                                {"data": "additional_charges"}
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
            });
            $(document).on('click', '.btndelete', function () {
                var serviceId = $(this).attr("data-cid");
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
                                    url: "list_extra_charges.php",
                                    data: {action: "delete", service_id: serviceId},
                                    dataType: "json",
                                    success: function (data) {
                                        if (data.STATUS == "success") {
                                            swal("Success!", "<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>", "success");
                                            $(".scroll-to-top").click();
                                            grid.getDataTable().ajax.reload();
                                        } else {
                                            swal("Sorry!", "something went wrong", "error");
                                        }
                                    },
                                    error: function () {
                                        swal("Sorry!", "something went wrong", "error");
                                    }
                                });
                            }
                        });
            });
            $(document).on('click', '.btnedit', function () {
                $(".scroll-to-top").click();
                var cId = $(this).attr('data-cid');
                var uId = $(this).attr('data-uid');
                $.ajax({
                    url: 'list_extra_charges.php',
                    type: 'POST',
                    data: {action: 'extra_list_edit', c_id: cId, u_id: uId},
                    headers: {
                    },
                    success: function (data) {
                        var obj = jQuery.parseJSON(data);
                        var select2Id = $("#service_id").select2();
                        select2Id.val(obj.service_id).trigger('change');
                        $("#surcharge").val(obj.sur_charge);
                        var select2Id = $("#sur_charge_type").select2();
                        select2Id.val(obj.sur_charge_type).trigger('change');
                        $("#extra_charge").val(obj.extra_charge);
                        var select2Id = $("#extra_charge_type").select2();
                        select2Id.val(obj.extra_charge_type).trigger('change');
                        $("#discount").val(obj.discount);
                        var select2Id = $("#discount_type").select2();
                        select2Id.val(obj.discount_type).trigger('change');
                        $("#over_size").val(obj.over_size);
                        var select2Id = $("#over_size_type").select2();
                        select2Id.val(obj.over_size_type).trigger('change');
                        $("#over_weight").val(obj.over_weight);
                        var select2Id = $("#over_weight_type").select2();
                        select2Id.val(obj.over_weight_type).trigger('change');
                        $("#additional_charges").val(obj.additional_charges);
                        $("#extra_charge_id").val(obj.extra_charge_id);
                    },
                    error: function (xhr, status, error) {

                    }
                });
            });
            $(document).on('click', '.btndelete', function () {
                $(".scroll-to-top").click();
                var cId = $(this).attr('data-cid');
            });
        </script>
        <!--End Hadi Code-->
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
        $userId = util_get_num("id");
        $sessionUser = SessionManager::getUser();
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        $errorMsg = ErrorList::getItem();
//        if (!empty($errorMsg) && $errorMsg != NULL) {
//            
        ?>
        <!--<div class="alert alert-danger">-->
        <?php //ErrorList::getItem()->render();  ?>
        <!--</div>-->
        <?php
//        }
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-docs"></i>Service Charges for <?php echo $this->displayname; ?></div>
            </div>
            <form id="bookingForm" name="bookingForm" action="list_extra_charges.php" method="POST" enctype="multipart/form-data">
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="col-md-12 alert alert-success" id="success_message" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Service</label>
                                <div class="input-group"> <div class="input-group-addon"> <i class="fa fa-rocket"></i> </div>
                                    <?php
                                        $userServicesRoutingFilter = new UserServicesRoutingFilter();
                                        $userServicesRoutingFilter->addFieldFilter("user_account_id", $sessionUser->getUserAccountId());
                                        $userServicesRoutingFilter->setGroup("service_id");
                                        $userServicesRoutingFilterList = $userServicesRoutingFilter->getColumnList(" psr.service_id");
                                        $accountServices = "";
                                        foreach ($userServicesRoutingFilterList as $userServicesRoutingArr) {
                                            $accountServices .= "'".$userServicesRoutingArr->getServiceId()."',";
                                        }
                                        $accountServices = rtrim($accountServices,",");
                                        echo Ddl::generateDDL('service_id', 'ServiceFilter', ' s.id IN ('.$accountServices.') ', 'name', 'id', $this->service_id, ' class="form-control select2 select" rel="tooltip" data-show-subtext="true" data-toggle="tooltip"  title="Service" data-original-title="Service"', 'Select service', '', 'service_id', 'Service', '', '', '');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Surcharge</label>
                            <div class="row">
                                <div class="form-group col-xs-9 padding-right-0">

                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                        <input id="surcharge" name="sur_charge" type="text" value="<?php echo @$this->sur_charge; ?>" placeholder="Sur Charge" class="form-control" maxlength="30" rel="tooltip"  data-original-title="Surcharge" />
                                    </div>
                                </div>
                                <div class="form-group col-xs-3 padding-left-0 ">

                                    <?php
                                    $IsPeArr = array('percentage' => '%', 'fixed' => '&pound;');
                                    $surChargeType = "";
                                    if (!empty($this->sur_charge_type))
                                        $surChargeType = $this->sur_charge_type;
                                    echo Ddl::generateArrayDDL('sur_charge_type', $IsPeArr, $surChargeType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Surcharge" placeholder="Surcharge"');
                                    ?>                                    

                                </div>

                            </div>

                        </div>
                        <div class="col-md-4">

                            <label>Extra Charge</label>

                            <div class="row"> 

                                <div class="form-group col-xs-9 padding-right-0">
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                        <input id="extra_charge" name="extra_charge" type="text" value="<?php echo @$this->extra_charge; ?>"  class="form-control" maxlength="30" rel="tooltip" placeholder="Extra Charge" data-original-title="Extra Charge"/>
                                    </div>
                                </div>

                                <div class="form-group col-xs-3 padding-left-0 ">
                                    <?php
                                    $IsPeArr = array('percentage' => '%', 'fixed' => '&pound;');
                                    $extraChargeType = "";
                                    if (!empty($this->extra_charge_type))
                                        $extraChargeType = $this->extra_charge_type;
                                    echo Ddl::generateArrayDDL('extra_charge_type', $IsPeArr, $extraChargeType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Extra Charge" placeholder="Extra Charge"');
                                    ?>
                                </div>

                            </div>  

                        </div>
                        <div class="col-md-4">
                            <label>Discount</label>



                            <div class="row">


                                <div class="form-group col-xs-9 padding-right-0">
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                        <input id="discount" name="discount" type="text" value="<?php echo @$this->discount; ?>" placeholder="Discount" class="form-control" maxlength="30" rel="tooltip"  data-original-title="Discount"/>
                                    </div>
                                </div>

                                <div class="form-group col-xs-3 padding-left-0 ">
                                    <?php
                                    $IsPeArr = array('percentage' => '%', 'fixed' => '&pound;');
                                    $discountType = "";
                                    if (!empty($this->discount_type))
                                        $discountType = $this->discount_type;
                                    echo Ddl::generateArrayDDL('discount_type', $IsPeArr, $discountType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Discount" placeholder="Discount"');
                                    ?>
                                </div>


                            </div>


                        </div>
                        <div class="col-md-4">

                            <label>Over Size Charges</label>



                            <div class="row">

                                <div class="form-group col-xs-9 padding-right-0">
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                        <input id="over_size" name="over_size" type="text" value="<?php echo @$this->over_size; ?>" placeholder="Over Size" class="form-control" maxlength="30" rel="tooltip"  data-original-title="Over Size Charges" />

                                    </div>
                                </div>


                                <div class="form-group col-xs-3 padding-left-0 ">
                                    <?php
                                    $IsPeArr = array('percentage' => '%', 'fixed' => '&pound;');
                                    $overSizeType = "";
                                    if (!empty($this->over_size_type))
                                        $overSizeType = $this->over_size_type;
                                    echo Ddl::generateArrayDDL('over_size_type', $IsPeArr, $overSizeType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Over Size Charges" placeholder="Over Size Charges"');
                                    ?>
                                </div>


                            </div>


                        </div>


                        <div class="col-md-4">

                            <label>Over Weight Charges</label>

                            <div class="row">

                                <div class="form-group col-xs-9 padding-right-0">


                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                        <input id="over_weight" name="over_weight" type="text" value="<?php echo @$this->over_weight; ?>" placeholder="Over Weight" class="form-control" maxlength="30" rel="tooltip"  data-original-title="Over Weight Charges" />

                                    </div>
                                </div>


                                <div class="form-group col-xs-3 padding-left-0 ">
                                    <?php
                                    $IsPeArr = array('percentage' => '%', 'fixed' => '&pound;');
                                    $overWeightType = "";
                                    if (!empty($this->over_weight_type))
                                        $overWeightType = $this->over_weight_type;
                                    echo Ddl::generateArrayDDL('over_weight_type', $IsPeArr, $overWeightType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Over Weight Charges" placeholder="Over Weight Charges"');
                                    ?>

                                </div>


                            </div>              
                        </div>



                        <div class="col-md-4">
                            <div class="row">

                                <div class="form-group col-xs-9 padding-right-0">
                                    <div class="input-group">
                                        <span class="input-group-addon"> <i class="fa fa-money"></i> </span>
                                        <input id="additional_charges_details" name="additional_charges_details" type="hidden" value='<?php echo @$this->additional_charges_details; ?>'  readonly="readonly"/>
                                        <input id="additional_charges" name="additional_charges" type="text" value="<?php echo @$this->additional_charges; ?>" placeholder="Additional Charges" class="form-control" maxlength="30" rel="tooltip"  data-original-title="Additional Charges" readonly="readonly"/>

                                    </div>
                                </div>



                                <div class="form-group col-xs-3 padding-left-0 ">
                                    <?php
                                    $IsPeArr = array('per_kg' => 'Per Kg');
                                    echo Ddl::generateArrayDDL('additional_charges_type', $IsPeArr, 'per_kg', '', ' class="form-control select2 select" rel="tooltip" style="padding:5px 12px;" readonly="readonly" disabled data-original-title="Surcharge" placeholder="Surcharge"');
                                    ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <input id="user_id" type="hidden" name="user_id" value="<?php echo @$this->user_id; ?>" class="form_field_col1" />
                                <input type="hidden" name="extra_charge_id" id="extra_charge_id" value="<?php echo @$this->extra_charge_id; ?>" />
                                <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
                                <a id="btnCncl" href="../main/customers_details.php?id=<?php echo util_get_num("id"); ?>" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>

                                <a id="btnSave" href="javascript:void(0);" class="btn btn-primary btn_save"><span></span>Save</a>

                                <a id="btnMoreOption" href="#" class="btn btn-primary" title="More Option" data-target="#more_option" data-toggle="modal"><span></span>Ancillary Charge</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    Service Charges for <?php echo $this->displayname; ?>
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="10%">Actions</th>
                                <th>Service Name</th>
                                <th>Sur Charges</th>
                                <th>Extra Charges</th>
                                <th>Over Weight</th>
                                <th>Over Size</th>
                                <th>Discount</th>
                                <th>Ancillary Charge</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control form-filter input-sm" name="name"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="sur_charge"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="extra_charge"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="over_weight"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="over_size"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="discount"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="additional_charges"></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="more_option" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Pricing Break Down</h4>
                    </div>
                    <div class="modal-body">
                        <form id="update_more_option_frm" name="update_more_option_frm" method="post">
                            <div class="alert alert-success hidden" id="update_remote_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_REMOTE" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <?php
                                        $userExtraColumn = UserServicesCharges::extraDetailsCharges();
                                        if (count($userExtraColumn) > 0) {
                                            $countForLoop = 0;
                                            ?><div class="row"><?php
                                            foreach ($userExtraColumn as $key => $value) {
                                                ?>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label font-green-soft"><?php echo $value ?>（GBP/KG):</label> 
                                                            <div class="input-group">
                                                                <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                                                <input type="text" name="extraPric[<?php echo $key; ?>]" id="<?php echo $key ?>" data-key="<?php echo $key; ?>" value="0.00" class="form-control input-sm customer_charges" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if ($countForLoop % 3 == 0)
                                                        echo '<div stype="clear:both;"></div>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>	
                                    </fieldset>
                                </div>

                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateAdditionalPricingData" name="updateAdditionalPricingData">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
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
