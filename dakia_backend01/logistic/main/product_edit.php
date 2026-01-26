<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'customizedservicesroutinglog.class',
    'customizedservicesroutinglogfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class'
]);
class Page extends BasePage {

    public $productDetail;
    private $breadcrumb = '';
    private $fromWeightProduct = array();
    private $toWeightProduct = array();

    /*     * *
     * Controller logic
     */

    protected function init() {
        $user = SessionManager::getUser();
        //if ($user->getUserType() != "admin") {
        //    util_redirect("index.php");
        //}

        Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
        t_on(); // turn on trace for this page

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'services_list.php' =>  Translation::GetCaption("CUSTOMIZED_SERVICE_DETAILS")
        );
        $customize_service_id = util_get_num("customize_service_id");
        $patnerserviceroutine = new CustomizedServicesRoutingFilter();
        $patnerserviceroutine->addFilter("  service_id > 0 AND customize_service_id > 0");
        $patnerserviceroutine->addFilter("  customize_service_id = '" . $customize_service_id . "'");
        $productWeightList = $patnerserviceroutine->getWeigthList();
        $arrayWeightLimit = array();
        if (count($productWeightList) > 0) {
            foreach ($productWeightList as $weightArrayData) {
                $this->fromWeightProduct[$weightArrayData->getFromWeight()] = $weightArrayData->getFromWeight();
                $this->toWeightProduct[$weightArrayData->getToWeight()] = $weightArrayData->getToWeight();
            }
        }

        if (isset($_POST['func']) && $_POST['func'] == 'get_audit_log') {
            $output = "";
            $productid = $this->form_vars['productid'];

            $productRoutineFilter = new CustomizedServicesRoutingLogFilter();
            $productlog = $productRoutineFilter->getAuditLog($productid);

            if (count($productlog) > 0) {

                foreach ($productlog as $clog) {
                    $output .= '<tr>';
                    $output .= '<td>' . strtoupper($clog->getIpaddress()) . '</td>';
                    $output .= '<td>' . formatDateTime(date("Y-m-d H:i:s", $clog->getLogDate())) . '</td>';
                    $output .= '<td>' . $clog->getMessage() . '</td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="3">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'getServiceList') {
            if (isset($this->form_vars['service_id']))
                $serviceid = $this->form_vars['service_id'];
            else
                $serviceid = 0;

            $customize_service_id = $this->form_vars['customize_service_id'];
            $service_name = $this->form_vars['service_name'];
            echo Ddl::generateDDL('services', 'ServiceFilter', 'AND active = 1 ', 'name', 'id', $serviceid, ' class="form-control select2" rel="tooltip" onchange="save_service_change(this.value, ' . (int) $customize_service_id . ')"', "Please Select Service", '', 'services', '');

            exit;
        }
        else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'delete') {
            $psrid = str_replace('ser-', '', $this->form_vars['reid']);
            $psrStatus = new CustomizedServicesRouting(intval($psrid));
            $psrStatus->setStatus('0');
            $psrStatus->save();
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'active') {
            $psrid = str_replace('ser-', '', $this->form_vars['reid']);
            $psrStatus = new CustomizedServicesRouting(intval($psrid));
            $psrStatus->setStatus('1');
            $psrStatus->save();
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'updateServiceChanges') {
            $customize_service_id = $this->form_vars["customize_service_id"];
            $service_id = $this->form_vars["service_id"];
            $productName = $this->form_vars["productName"];
            if ($customize_service_id > 0 && $service_id > 0) {
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addIdFilter($service_id);
                $serviceList = $serviceFilter->getColumnList("name, to_weight");

                if (count($serviceList) > 0) {
                    $serviceName = $serviceList[0]->getName();
                    $maxAllowedWeight = $serviceList[0]->getToWeight();
                }


                $psrupdate = new CustomizedServicesRouting(intval($customize_service_id));
                $output = array();
                if ($psrupdate->getToWeight() <= $maxAllowedWeight) {
                    $csv = "";
                    $cr = "\r\n";
                    $csv .= $psrupdate->getCountryIso() . ",";
                    $csv .= $psrupdate->getFromWeight() . ",";
                    $csv .= $psrupdate->getToWeight() . ",";
                    $csv .= $psrupdate->getStatus() . ",";
                    $csv .= $psrupdate->getCustomizeServiceId() . ",";
                    $csv .= $psrupdate->getServiceId() . ",";
                    $csv .= $cr;
                    $folder_path = "../_assets/routing_file/routing_log";

                    if (!file_exists($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }

                    $uniqueFileName = $productName . "_" . date("YmdHis") . "_" . $user->getUserAccount();

                    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                    $file_path = fopen($file_path, 'w');
                    $csvHeader = "country_iso, from_weight, to_weight, status, customize_service_id, service_id";
                    fwrite($file_path, $csvHeader . $cr . $csv);
                    // close file
                    fclose($file_path);

                    $output['status'] = 'success';
                    $psrupdate->setServiceId($service_id);
                    $psrupdate->save();
                    $output['message'] = $serviceName;
                } else {
                    $output['status'] = 'fail';
                    $output['message'] = "maximum allowed weight for selected service is " . $maxAllowedWeight;
                }
                echo json_encode($output);
            }
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'bulkEdit') {
            $country_iso = $this->form_vars['country_iso'];
            $productid = $this->form_vars['productid'];
            $from_weight = $this->form_vars['from_weight'];
            $to_weight = $this->form_vars['to_weight'];
            $services = $this->form_vars['services'];
            $productName = $this->form_vars['productName'];
            $maxAllowedWeight = 0;

            if ($from_weight >= 0 && $to_weight > 0 && $services != '') {

                $serviceFilter = new ServiceFilter();
                //$serviceFilter->addIdFilter($services);
                $serviceFilter->addFilter(" ser.id = '" . $services . "' AND ser.from_weight >= '" . $from_weight . "' AND ser.to_weight <= '" . $to_weight . "' and ser.active= '1'");
                $serviceList = $serviceFilter->getServiceAndCountryList("ser,name, ser.to_weight, ser.from_weight");
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addIdFilter($services);
                $serviceList = $serviceFilter->getColumnList("name, to_weight");

                if (count($serviceList) > 0) {
                    $serviceName = $serviceList[0]->getName();
                    $maxAllowedWeight = $serviceList[0]->getToWeight();
                }
                if (count($serviceList) > 0) {
                    //$maxAllowedWeight = $serviceList[0]->getToWeight();
                    //}
                    //if ($to_weight <= $maxAllowedWeight) {
                    $psr = new CustomizedServicesRoutingFilter();
                    $psr->addWeightRangeFilter($from_weight, $to_weight);
                    $psr->addFieldFilter("country_id", $country_iso);
                    $psr->addFieldFilter("customize_service_id", $productid);

                    $psrList = $psr->getList();
                    if (count($psrList) > 0) {

                        $csv = "";
                        $cr = "\r\n";
                        foreach ($psrList as $psr) {
                            $csv .= $psr->getCountryId() . ",";
                            $csv .= $psr->getFromWeight() . ",";
                            $csv .= $psr->getToWeight() . ",";
                            $csv .= $psr->getStatus() . ",";
                            $csv .= $psr->getCustomizeServiceId() . ",";
                            $csv .= $psr->getServiceId() . ",";
                            $csv .= $cr;
                        }
                        $folder_path = "../_assets/routing_file/routing_log";

                        if (!file_exists($folder_path)) {
                            mkdir($folder_path, 0777, true);
                        }

                        $uniqueFileName = $productName . "_" . date("YmdHis") . "_" . $user->getUserAccount();

                        $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                        $file_path = fopen($file_path, 'w');
                        $csvHeader = "country_iso, from_weight, to_weight, status, customize_service_id, service_id";
                        fwrite($file_path, $csvHeader . $cr . $csv);

                        // close file
                        fclose($file_path);
                        CustomizedServicesRouting::updateCustomizedService($country_iso, $from_weight, $to_weight, $productid, $services);
                        $output = array();
                        $output['status'] = 'success';
                        $output['message'] = '<div class="alert alert-success">Uploaded successfully.</div>';
                    } else {
                        $output = array();
                        $output['status'] = 'success';
                        $output['message'] = '<div class="alert alert-danger">There is no services add for this country at this weight. Please add routine first for the product.</div>';
                    }
                } else {

                    $output = array();
                    $output['status'] = 'fail';
                    $output['message'] = '<div class="alert alert-danger">maximum allowed weight for selected service is ' . $maxAllowedWeight . '</div>';
                }
            } else {
                $output = array();
                $output['status'] = 'fail';
                $output['message'] = '<div class="alert alert-danger">Please provide all required value.</div>';
            }
            echo json_encode($output);
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'view') {
            $country = DbAccess3::escape($this->form_vars["country"]);
            $customize_service_id = $this->form_vars["customize_service_id"];
            $patnerserviceroutine = new CustomizedServicesRoutingFilter();
            $patnerserviceroutine->addFilter("  service_id > 0 AND customize_service_id > 0");
            $patnerserviceroutine->addFilter("  customize_service_id = '" . $customize_service_id . "'");
            $countryList = $patnerserviceroutine->getCountrytList($country);
            $ProductWeightList = $patnerserviceroutine->getWeigthList();
            $arrayWeightLimit = array();
            if (count($ProductWeightList) > 0) {
                foreach ($ProductWeightList as $weightArrayData) {
                    $arrayWeightLimit[] = array($weightArrayData->getFromWeight(), $weightArrayData->getToWeight());
                }
            }

            if (count($countryList) > 0) {
                $output = array();
                $resultCountry = array();
                foreach ($countryList as $country_iso) {
                    $resultCountry[$country_iso->getCarrierCountry()] = $country_iso->getCountryId();
                }

                $display_stringDemo = "<table id='table_container' class='consignment_list_tbl table table-striped table-bordered table-advance table-hover'>";
                $display_stringDemo .= "<tr class='grid_header' style='font-weight:bold;'>";
                $display_stringDemo .= "<th>Options</th>";
                $display_stringDemo .= "<th>Countries</th>";
                foreach ($arrayWeightLimit as $dataWeight) {
                    $display_stringDemo .= "<th style=text-align='center'>" . number_format($dataWeight[0], 2) . " - " . number_format($dataWeight[1], 2) . "</th>";
                }
                $display_stringDemo .= "</tr>";
                foreach ($resultCountry as $countryName => $countryId) {
                    $display_stringDemo .= "<tr>";
                    $display_stringDemo .= '<td> <a class="btn btn-sm blue btn-outline bulk-edit" title="Bulk Edit"
                                      data-toggle="modal" data-target="#bulk-edit-popup" role="dialog" tabindex="-1"  
                                       data-country="' . $countryName . '" data-iso="' . $countryId . '" data-product-id = "' . $customize_service_id . '" 
                                       href="javascript:;">
                                        <span class="fa fa-list"></span> 
                                  </a></td>';
                    $display_stringDemo .= "<td>" . strtoupper($countryName) . "</td>";
                    foreach ($arrayWeightLimit as $dataWeight) {
                        $display_stringDemo .= "<td style='max-width:100px; min-width:100px; text-align:center;'>" . $this->getCellData($countryId, $dataWeight[0], $dataWeight[1], $customize_service_id) . "</td>";
                    }
                    $display_stringDemo .= "</tr>";
                }
                $display_stringDemo .= "</table>";
                $output['status'] = 'success';
                $output['message'] = $display_stringDemo;
            } else {
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        } else if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'export') {
            $country = $this->form_vars["country"];
            $customize_service_id = $this->form_vars["customize_service_id"];
            $patnerserviceroutine = new CustomizedServicesRoutingFilter();
            $patnerserviceroutine->addFilter("  service_id > 0 AND customize_service_id > 0");
            $patnerserviceroutine->addFilter("  customize_service_id = '" . $customize_service_id . "'");
            $countryList = $patnerserviceroutine->getCountrytList($country);
            $weightList = $patnerserviceroutine->getWeigthList();
            $resultWeights = array();
            if (count($weightList) > 0) {
                foreach ($weightList as $weightsPsr) {
                    $resultWeights[] = array($weightsPsr->getFromWeight(), $weightsPsr->getToWeight());
                }
            }

            $output = array();
            if (count($countryList) > 0) {
                $resultCountry = array();
                foreach ($countryList as $country_iso) {
                    $resultCountry[$country_iso->getCarrierCountry()] = $country_iso->getCountryId();
                }

                $csvHeader = "Product Name, Country Iso, ";
                foreach ($resultWeights as $indexWeight => $arrayweight) {
                    $csvHeader .= number_format($arrayweight[0], 2) . '-' . number_format($arrayweight[1], 2) . ",";
                }

                $product = new Services($customize_service_id);
                $product_name = $product->getName();
                $csv = "";
                foreach ($resultCountry as $countryName => $countryId) {
                    $countryDetails = new Country($countryId);
                    $iso = $countryDetails->getIso();
                    $csv .= $product_name . ",";
                    $csv .= strtoupper($iso) . ",";


                    $weightCount = 0.25;
                    foreach ($resultWeights as $indexWeight => $arrayweight) {
                        $csv .= $this->getCellDataCsv($countryId, $arrayweight[0], $arrayweight[1], $customize_service_id) . ",";
                    }
                    while ($weightCount <= 30) {

                        if ($weightCount < 2)
                            $weightCount += 0.25;
                        //elseif($weightCount<10)$weightCount+=0.5;
                        else
                            $weightCount += 0.5;
                    }
                    $csv .= "\r\n";
                }

                $folder_path = "../_assets/csv";

                if (!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }

                $uniqueFileName = "productRoutineFile";

                $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                $file_path = fopen($file_path, 'w');
                fwrite($file_path, $csvHeader . "\r\n" . $csv);
                fclose($file_path);
                $output["status"] = "success";
                $output["message"] = "../_assets/csv/" . $uniqueFileName . ".csv";
            } else {
                $output["status"] = "fail";
                $output["message"] = "Unable to download file.";
            }

            echo json_encode($output);
            exit;
        } else {

            $customize_service_id = util_get_num('customize_service_id');

            if ($customize_service_id < 0) {
                util_redirect("products.php");
            }

            $this->productDetail = new Services($customize_service_id);
        }


        // common initialisation for ths page
        $this->setTitle("Edit Product");
    }

    private function getCellDataCsv($countryId, $lowerLimit, $upperLimit, $customize_service_id) {
        $colData = '';
        $psr = new CustomizedServicesRoutingFilter();
        $psr->addWeightRangeFilter($lowerLimit, $upperLimit);
        $psr->addFilter(" customize_service_id > 0 AND customize_service_id = '" . $customize_service_id . "'");
        $psr->addCountryFilter($countryId);
        $resultCountry = $psr->getProductCustomerServiceList("csr.id, status, s.code as service_name, s.id as service_id");

        if (count($resultCountry) > 0) {
            foreach ($resultCountry as $rowSer) {
                $colData .= $rowSer->getServiceName();
            }
            return $colData;
        } else {
            return '';
        }
    }

    private function getCellData($countryId, $lowerLimit, $upperLimit, $customize_service_id) {
        $colData = '';
        $psr = new CustomizedServicesRoutingFilter();
        $psr->addWeightRangeFilter($lowerLimit, $upperLimit);
        $psr->addFilter(" customize_service_id > 0 AND customize_service_id = '" . $customize_service_id . "' AND country_id = '" . $countryId . "'");
        $resultCountry = $psr->getProductCustomerServiceList("csr.id, status, s.name as service_name, s.id as service_id");
        if (count($resultCountry) > 0) {
            foreach ($resultCountry as $rowSer) {
                $colData .= '<div style=" min-width: 30px; text-align:center; cursor:pointer;" class="' . $rowSer->getStatus() . ' service_list" id="ser-' . $rowSer->getId() . '" ondblclick="addDropdown(\'' . $rowSer->getId() . '\',\'' . $rowSer->getServiceId() . '\',\'' . $rowSer->getServiceName() . '\');">' . $rowSer->getServiceName();
                $colData .= '<div style="clear:both;"></div>';
                /* --- Here status check  */
                $colData .= '
				</div>';
                if ($rowSer->getStatus() == '1') {
                    $active = 'display:block;';
                    $inactive = 'display:none;';
                } else {
                    $active = 'display:none;';
                    $inactive = 'display:block;';
                }
                $colData .= '<div title="Make Disable" id="act-' . $rowSer->getId() . '" style="cursor:pointer; ' . $active . ' " onclick="remove_service(\'ser-' . $rowSer->getId() . '\',\'inact-' . $rowSer->getId() . '\',\'act-' . $rowSer->getId() . '\');">  <span class="fa fa-times text-center"></span>  </div>';
                $colData .= '<div title="Make Enable"  id="inact-' . $rowSer->getId() . '" style="cursor:pointer;' . $inactive . '" onclick="add_service(\'ser-' . $rowSer->getId() . '\',\'act-' . $rowSer->getId() . '\',\'inact-' . $rowSer->getId() . '\');">  <span class="fa fa-check text-center"></span>  </div>';
                $colData .= '<div style="clear:both;"></div>
				';
            }
            return $colData;
        } else {
            return '';
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <?php
    }

    protected function renderFooter() {
        ?>


        <script>
            function remove_service(rid, act, inact)
            {
                if (confirm("Are you sure, you want to inactive this option"))
                {
                    $.post("product_edit.php", {action: 'delete', reid: rid}, function (d) {
                        $("#" + rid).attr("class", 'inactive');
                        $("#" + act).show();
                        $("#" + inact).hide();


                    });
                }
            }
            function add_service(rid, act, inact)
            {
                if (confirm("Are you sure, you want to active this option"))
                {
                    $.post("product_edit.php", {action: 'active', reid: rid}, function (d) {
                        $("#" + rid).attr("class", 'active');
                        $("#" + act).show();
                        $("#" + inact).hide();
                    });
                }
            }

            function addDropdown(customize_service_id, service_id, service_name)
            {
                $.post("product_edit.php", {action: 'getServiceList', customize_service_id: customize_service_id, service_name: service_name, service_id: service_id}, function (data) {
                    $("#ser-" + customize_service_id).html(data);

                });



            }

            function save_service_change(service_id, customize_service_id)
            {
                var product_name = $("#productName").val();

                var form_data = new FormData();
                form_data.append('productName', product_name);
                form_data.append('service_id', service_id);
                form_data.append('customize_service_id', customize_service_id);
                form_data.append('action', 'updateServiceChanges');
                $.ajax({
                    url: 'product_edit.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    
                }).done( function (response) {
                        if (response.status == 'success') {
                            $("#ser-" + customize_service_id).html(response.message);
                            $("#ser-" + customize_service_id).attr("onclick", 'addDropdown(' + customize_service_id + ',' + service_id + ',' + response.message + ')');
                        } else {
                            alert(response.message);
                            return false;
                        }
                    }).fail(function(xhr, status, errorThrown) {
                    swal("", "Please try again later", "info");
                    console.log("Error: " + errorThrown);
                    console.log("Status: " + status);
                    console.dir(xhr);
                    });
            }

            $(document).ready(function () {
        <?php
        $country = util_get("country");
        $pid = util_get_num("customize_service_id");
        if ($country != '') {
            ?>
                    loadDetails(<?= $pid; ?>, 'u');
            <?php
        }
        ?>

                $("#search_country").on('change paste', function (e) {
                    var search_val = $('#search_country').val();
                    if ($.trim(search_val) != '')
                        loadDetails(<?= $pid; ?>, $('#search_country').val());
                    else
                        loadDetails(<?= $pid; ?>, 'u');
                });
                $(document).on('click', '.bulk-edit', function () {

                    var e = $(this);

                    var country = e.data('country');
                    var productid = e.data('product-id');
                    var country_iso = e.data('iso');

                    $("#country_iso").val(country_iso);
                    $("#productid").val(productid);
                    $("#country_name_header").html(country);

                });

                $('#close_modal').click(function () {
                    loadDetails($('#productid').val(), $('#searchAlpha').val());
                });

                $('#export_routine').click(function () {
                    $('#export_routine_frm').submit();

                    /*
                    var e = $(this);
                    swal({
                        title: "Do you like to download whole product?",
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
                                    var country = "";
                                } else
                                {
                                    var country = $('#search_country').val();
                                    if ($.trim(country) == '')
                                        country = $('#searchAlpha').val();
                                }
                                $('#export-product-popup').modal('show');
                                $(".progress-bar-info").attr("aria-valuenow", "10");
                                $('.progress-bar-info').css('width', '10%');
                                $('#download-csv').hide();
                                var productid = $('#productid').val();
                                var form_data = new FormData();
                                form_data.append('action', 'export');
                                form_data.append('customize_service_id', productid);
                                form_data.append('country', country);
                                $.ajax({
                                    url: "product_edit.php",
                                    dataType: 'json',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    data: form_data,
                                    type: 'post',
                                    success: function (response) {

                                        if (response.status == "success")
                                        {
                                            //alert(response.message);
                                            $(".progress-bar-info").attr("aria-valuenow", "100");
                                            $('.progress-bar-info').css('width', '100%');
                                            $('#download-csv').show();
                                            $("#download-csv").attr("href", response.message);
                                            //  $('#showDetailProduct').html(response.message);
                                        } else {
                                            $('#export-product-popup').modal('hide');
                                            swal("Sorry!", "No record found for download", "error");
                                            // return false;
                                        }
                                    }
                                });
                            });
                    */
                });

                $('#save_bulk').click(function () {
                    var country_iso = $("#country_iso").val();
                    var productid = $("#productid").val();
                    var from_weight = $("#fromweight").val();
                    var to_weight = $("#toweight").val();
                    var services = $("#services").val();
                    var product_name = $("#productName").val();

                    var form_data = new FormData();
                    form_data.append('action', 'bulkEdit');
                    form_data.append('country_iso', country_iso);
                    form_data.append('productid', productid);
                    form_data.append('from_weight', from_weight);
                    form_data.append('to_weight', to_weight);
                    form_data.append('services', services);
                    form_data.append('productName', product_name);

                    $.ajax({
                        url: "product_edit.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == "success")
                            {
                                $('#display_error').html(response.message);
                                $('#fromweight').val(0);
                                $('#toweight').val(0.25);
                                $('#services').val("");
                                var searchAlpha = $('#searchAlpha').val();
                                if (searchAlpha == "")
                                    searchAlpha = 'u';
                                loadDetails(productid, searchAlpha);
                            } else
                            {
                                $('#display_error').html(response.message);
                                return false;
                            }
                        }
                    });

                });
            });

            function ShowAudit(productid)
            {
                $.post('product_edit.php', {func: 'get_audit_log', productid: productid}, function (data) {
                    $("#audit_content").html(data);
                });

            }

            function loadDetails(customize_service_id, alphabet)
            {
                $('#blockui_sample_2_2').click();
                $('#searchAlpha').val(alphabet);
                $('#productid').val(customize_service_id);
                var form_data = new FormData();
                form_data.append('action', 'view');
                form_data.append('customize_service_id', customize_service_id);
                form_data.append('country', alphabet);

                $.ajax({
                    url: "product_edit.php",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {

                        if (response.status == "success")
                        {
                            $('#showDetailProduct').html(response.message);
                        } else
                        {
                            return false;
                        }
                    }
                });
            }
        </script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    Customized Service Details
                    
                </div>
                <div class="actions">
                    <a href="services_list.php" class="btn blue" id="manual_add_product" name="manual_add_product">
                        <i class="fa fa-list"></i> List </a>
                    <!--                    <a href="routing_manual.php" class="btn blue" id="manual_add_product" name="manual_add_product">
                                            <i class="fa fa-plus"></i> Add Routine </a>-->
                    <a href="routing_bulk.php?customize_service_id=<?php echo util_get_num("customize_service_id"); ?>" class="btn blue" id="routing_bulk" name="routing_bulk">
                        <i class="fa fa-upload"></i> Upload CSV</a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="tableA" style ="width:100%; font-weight:bold;">
                            <tr style="text-align:center;">
                                <td> <h2  style="text-align:center;"><?php echo strtoupper($this->productDetail->getName()); ?> COUNTRIES </h2></td>
                            </tr>
                            <tr>
                                <?php
                                $alphabets = range("A", "Z");
                                ?>
                                <td align="center"> 
                                    <?php
                                    foreach ($alphabets as $alpha) {
                                        $small_alpha = strtolower($alpha);
                                        ?>   
                                    <!--                             <a class="btn btn-sm btn-default" href="product_edit.php?customize_service_id=<?php // echo $this->productDetail->getId();      ?>&country=<?php //echo $small_alpha;      ?>&action=view"><? $alpha; ?></a> -->
                                        <a class="btn btn-sm btn-default" href="javascript:;" onclick="loadDetails('<?php echo $this->productDetail->getId(); ?>', '<?php echo $small_alpha; ?>');"><?= $alpha; ?></a> 
                                    <?php } ?>
                                </td>
                            </tr>

                        </table> 
                    </div>		   
                </div>  
            </div>
        </div>

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    <?php echo $this->productDetail->getName(); ?> Detail
                </div>
                <div class="actions">
                    <a id="btnAudit" href="#" class="btn blue" title="audit" data-target="#audit-log" data-toggle="modal"  onClick="ShowAudit('<?php echo $this->productDetail->getId(); ?>');">Audit</a>
                    <a href="javascript:;" class="btn blue" id="export_routine" name="export_routine">
                        <i class="fa fa-download"></i> Export </a>
                    <div class="portlet-input input-inline input-small">
                        <div class="input-icon right">
                            <i class="icon-magnifier"></i>
                            <input type="text" class="form-control input-circle" placeholder="search..." name="search_country" id="search_country"> </div>
                    </div>
                </div>
            </div>
            <form id="export_routine_frm" action="get_pricing.php" method="post">
                <input type="hidden" name="func" value="download_routing_excel" />
                <input type="hidden" name="cutomized_service_id_for_routing" value="<?php echo $this->productDetail->getId(); ?>" />
            </form>
            <div class="portlet-body">

                <div class="row">
                    <div class="col-md-12">  
                        <a href="javascript:;" id="blockui_sample_2_2"></a>
                        <div class="table-scrollable" id="showDetailProduct">


                        </div>
                    </div>
                </div>


                <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                <input type="hidden" name="form_action" id="form_action" value="" /> 
                <input type="hidden" name="country_iso" id="country_iso" value="" />
                <input type="hidden" name="productid" id="productid" value="" />
                <input type="hidden" name="searchAlpha" id="searchAlpha" value="" />
                <input type="hidden" name="productName" id="productName" value="<?= $this->productDetail->getName() ?>" />
            </div>
        </div>

        <!-- BULK EDIT Modal  -->
        <div class="modal fade" tabindex="-1" role="dialog" id="bulk-edit-popup" data-backdrop="static" data-keyboard="false" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><span id="country_name_header"></span> Bulk Edit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="display_error">

                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-12" id="product-detail-display">

                                <div class="col-md-12">
                                    <div class="form-group"> 
                                        <label>From Weight: </label>
                                        <div class="input-group"> 
                                            <?php
                                            echo Ddl::generateArrayDDL('fromweight', $this->fromWeightProduct, "", "Select From Weight ", 'class="form-control select2 select" rel="tooltip" data-original-title="From Weight " placeholder="From Weight"', "", '', 'Select From Weight', true);
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group"> 
                                        <label>To Weight: </label>
                                        <div class="input-group"> 
                                            <?php
                                            echo Ddl::generateArrayDDL('toweight', $this->toWeightProduct, "", "Select To Weight ", 'class="form-control select2 select" rel="tooltip" data-original-title="To Weight" placeholder="To Weight"', "", '', 'Select To Weight', true);
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group"> 
                                        <label>Services: </label>
                                        <div class="input-group input-group-sm"> 
                                            <?php
                                            //echo Ddl::generateServiceDDLWithImage('services', $services, 'id', ' class="bs-select input-sm form-control form-filter " required="" data-show-subtext="true"', 'services', 'services', 'name', 'Select Services');
                                            echo Ddl::generateServiceDDLWithImage('services',$services,'id', ' class="bs-select input-sm form-control form-filter " required="" data-show-subtext="true"','services','Services','');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">


                        <a id="save_bulk"   href="javascript:;" class="btn btn-primary"><span></span>Save</a>
                        <a href="javascript:;" id="close_modal" class="btn btn-default" data-dismiss="modal">Close</a>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>


        <div class="modal fade  bs-modal-lg" id="export-product-popup" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalExport" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close modal-close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Export Product</h4>
                    </div>
                    <form class="form-horizontal" role="form" action="" method="POST" name="frm_layout">
                        <div class="modal-body">
                            <div class="row">
                                <div class="progress progress-striped active">
                                    <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only"> 100% Complete </span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <span><a  id="download-csv"  class="btn blue margin-left-5" >Download</a></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn default modal-close" data-dismiss="modal">Close</button>
                            <input type="hidden" name="save_layout" value="layout_editor"/>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Product Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Date Time</th>
                                            <th>Product</th>
                                        </tr>
                                    </thead>
                                    <tbody id="audit_content">
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
