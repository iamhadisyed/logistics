<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

include_classes([
    'country.class',
    'countryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'servicefilter.class',
    'services.class',
    'carrier.class',
    'carrierfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'carrierzonescountries.class',
    'carrierzonescountriesFilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'servicecountrytime.class',
    'servicecountrytimefilter.class',
    'countryzonesmapping.class',
    'countryzonesmappingfilter.class',
    'linehaul.class',
    'linehaulfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class'
]);
class Page extends BasePage
{
    private $user;
    private $tariffs;
    private $weightLimits;
    private $toCountryLimit = 100;
    /*     * *
     * Controller logic
     */
    protected function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
    }

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'get_agent_pricing.php' => "Get Agent Pricing"
        );
        /*
         * DataTable handlings
        */

        if(isset($this->form_vars['func']) && $this->form_vars['func'] == "get_tariff") {
            $serviceId = $this->form_vars['service_id'];
            $agentIds = $this->form_vars['agent_id'];

            $tariffFilter = new TariffsFilter();
            $tariffFilter->addTariffsDetailsJoin();
            $tariffFilter->addCustomJoin('        JOIN `carrier_zones` cz  ON cz.id = td.to_zone_id ');
            $tariffFilter->addCustomJoin('        JOIN `services` s  ON s.id = t.service_id ');
            $tariffFilter->addCustomJoin('        JOIN `agent_data` ad  ON ad.id = t.agent_id ');
            $tariffFilter->addCustomJoin('        JOIN `currency` c  ON c.id = t.currency_id ');
            $tariffFilter->addFieldFilter('      t.tariff_type','agent');
            $tariffFilter->addFieldFilter('      t.service_id',$serviceId);
            $tariffFilter->addFilterIn('      t.agent_id',$agentIds);
            $tariffFilterObjs = $tariffFilter->getList('td.*,ad.id as agent_id,c.id as currency_id,t.carrier_id,t.service_id,t.name,ad.agent_name,s.name as service_name,s.code as service_code,cz.name as carrier_zone,c.rightsymbol as currency,c.id as currency_id');
            $tariffFromWeightArr = [];
            $tariffToWeightArr = [];
            $tariffSteps = [];
            $weightLimits = [];
            $zonesArr = [];
            $tariffDetails = [];
            $jsonArray = [];
            foreach($tariffFilterObjs as $tariffFilterObj) {
                $fromZoneId = $tariffFilterObj->getFromZoneId();
                $toZoneId = $tariffFilterObj->getToZoneId();
                $carrierId = $tariffFilterObj->getCarrierId();
                $serviceId = $tariffFilterObj->getServiceId();
                $weightCost = $tariffFilterObj->getWeightCost();
                $pieceCost = $tariffFilterObj->getPieceCost();
                $tariffName = $tariffFilterObj->getName();
                $weightFrom = $tariffFilterObj->getWeightFrom();
                $weightTo = $tariffFilterObj->getWeightTo();
                $serviceCode = $tariffFilterObj->getServiceCode();
                $carrierZone = $tariffFilterObj->getCarrierZone();
                $formula = $tariffFilterObj->getFormula();
                $agentName = $tariffFilterObj->getAgentName();
                $currenyId = $tariffFilterObj->getCurrencyId();
                $currency = $tariffFilterObj->getCurrency();
                $agentId = $tariffFilterObj->getAgentId();
                $tariffDetails[$toZoneId][$serviceId][] = [
                    'aid' => $agentId,
                    'agn' => $agentName,
                    'wc' => $weightCost,
                    'pc' => $pieceCost,
                    'tn' => $tariffName,
                    'fw' => $weightFrom,
                    'tw' => $weightTo,
                    'sid' => $serviceId,
                    'sc' => $serviceCode,
                    'zn' => $carrierZone,
                    'fm' => $formula,
                    'cur' => $currency
                ];
                $tariffDataArr = [
                    'carrier_id' => $carrierId,
                    'service_id' => $serviceId,
                    'currency_id' => $currenyId,
                    'from_zone_id' => $fromZoneId,
                    'formula' => $formula
                ];
                $tariffFromWeightArr[] = $weightFrom;
                $tariffToWeightArr[] = $weightTo;
                $zonesArr[$toZoneId] = $carrierZone;
                $step = $weightTo - $weightFrom;
                $tariffSteps[] = $step;
            }
            $minWeigt = min($tariffFromWeightArr);
            $maxWeight = max($tariffToWeightArr);
            $step = min($tariffSteps);
            if($step > $maxWeight){
                $step = $maxWeight;
            }
            if(count($tariffSteps) > 0 && !empty($step)) {
                for ($weight = $minWeigt; $weight <= $maxWeight;) {
                    if($this->numberFormat(($weight + $step)) <= $maxWeight)
                        $weightLimits[] = ['start' => $this->numberFormat($weight), 'end' => $this->numberFormat($weight + $step)];
                    $weight = $weight + $step;
                }
            }
            $jsonArray['tariff_details'] = json_encode($tariffDataArr);
            $jsonArray['zones'] = $zonesArr;
            $jsonArray['weight_limits'] = $weightLimits;
             if(count($tariffDetails)) {
                 foreach($tariffDetails as $toZoneId => $servicesArr) {
                     foreach ($weightLimits as $index => $limits) {
                         foreach ($servicesArr as $serviceId => $details) {
                             foreach ($details as $detail) {
                                 $found = false;
                                 if ($limits['start'] >= $detail['fw'] && $limits['end'] <= $detail['tw']) {
                                     $found = true;
                                 }
                                 if ($found) {
                                     $tariffFromWeigth[] = $limits['start'];
                                     $tariffToWeigth[] = $limits['end'];
                                     $totalCost = '';
                                     if(!empty($detail['fm'])){
                                         $findArry = ['Q','W','ITMCHR','CHRG', 'FRMW'];
                                         $replaceArry = ['1',$limits['end'],$detail['pc'],$detail['wc'],$detail['fw']];
                                         $formulaStr = str_replace($findArry,$replaceArry,$detail['fm']);
                                         eval("\$totalCost = $formulaStr;");
                                     } else {
                                         $totalCost = $detail['wc'];
                                     }
                                     $detail['tc'] = $this->numberFormat($totalCost);
                                     $jsonArray['tariff_data'][$toZoneId][$limits['start'] . '-' . $limits['end']][] = $detail;
                                     $tc = array_column($jsonArray['tariff_data'][$toZoneId][$limits['start'] . '-' . $limits['end']], 'tc');
                                     array_multisort($tc, SORT_ASC, $jsonArray['tariff_data'][$toZoneId][$limits['start'] . '-' . $limits['end']]);
                                 }
                             }
                         }
                     }
                 }
             }
//            echo "<pre>";
//            print_r($jsonArray);
//            echo "</pre>";
//            die;
             echo json_encode($jsonArray);
             die;
        }

        if(isset($this->form_vars['func']) && $this->form_vars['func'] == "get_service_tariff_agent") {
            $serviceId = $this->form_vars['service_id'];
            $tariffFilter = new TariffsFilter();
            $tariffFilter->addCustomJoin('        JOIN `agent_data` ad  ON ad.id = t.agent_id ');
            $tariffFilter->addFieldFilter('      t.tariff_type','agent');
            $tariffFilter->addFieldFilter('      t.service_id',$serviceId);
            $tariffFilterObjs = $tariffFilter->getList('ad.agent_name,t.agent_id');
            $options = '';
            if(count($tariffFilterObjs)) {
                foreach($tariffFilterObjs as $tariffFilterObj) {
                    $options .= '<option value="'.$tariffFilterObj->getAgentId().'">'.$tariffFilterObj->getAgentName().'</option>';
                }
            }
            $result = [
                'status' => 'success',
                'options' => $options
            ];
            echo json_encode($result);
            die;
        }

        if(isset($this->form_vars['func']) && $this->form_vars['func'] == "save_tariff") {
            $result = [
                'status' => 'success',
                'message' => 'Tariff save successfully'
            ];
            $user_account_id = $this->user->getUserAccountId();
            $tariff_detail = str_replace("'",'"',$this->form_vars['tariff_detail']);
            $tariffDetail = json_decode($tariff_detail);
            $selectedAgentTariffs = $this->form_vars['selected_agent_tariff'];
            $tariffName = $this->form_vars['tariff_name'];
            $startDate = $this->form_vars['start_date'];
            $endDate = $this->form_vars['end_date'];
            if(count($selectedAgentTariffs)) {
                $description = "Save Supplier Agent Type Auto Tariff";
                $date_added = time();
                $added_by = $this->user->getId();
                $date_update = time();
                $update_by = $this->user->getId();
                $carrierId = $tariffDetail->carrier_id;
                $serviceId = $tariffDetail->service_id;
                $currencyId = $tariffDetail->currency_id;
                $status = 1;
                $type = 'supplier';
                $checkActiveTariff = Tariffs::checkSupplierTariffActive($user_account_id,$serviceId,$startDate,$endDate);
                if($checkActiveTariff['tariff_found'] > 0) {
                    $status = 0;
                }
                $tariffsObj = new Tariffs();
                $tariffsObj->setUserAccountId($user_account_id);
                $tariffsObj->setCarrierId($carrierId);
                $tariffsObj->setServiceId($serviceId);
                $tariffsObj->setName($tariffName);
                $tariffsObj->setStatus($status);
                $tariffsObj->setCurrencyId($currencyId);
                $tariffsObj->setTariffType($type);
                $tariffsObj->setStartDate($startDate);
                $tariffsObj->setEndDate($endDate);
                $tariffsObj->setDescription($description);
                $tariffsObj->setTariffTemplate('default');
                $tariffsObj->setDateAdded($date_added);
                $tariffsObj->setAddedBy($added_by);
                $tariffsObj->setDateUpdated($date_update);
                $tariffsObj->setUpdatedBy($update_by);
                $tariffsObj->save();
                $tariff_id = $tariffsObj->getId();
                /* save agent routing */
                if($status == 1) {
                    // carrierServiceCustomizeRules::deleteByUserAccountIdAndServiceId($user_account_id,$serviceId);
                    carrierServiceDefaultRules::deleteByServiceId($serviceId);
                }
                $result['tariff_id'] = $tariff_id;
                $from_zone_id = $tariffDetail->from_zone_id;
                $formula = $tariffDetail->formula;
                $tariffDetailCSVContent = '';
                foreach($selectedAgentTariffs as $to_zone_id => $selectedAgentTariff) {
                    foreach($selectedAgentTariff as $weightStr => $details) {
                        $weightArr = explode('-',$weightStr);
                        $weight_from = isset($weightArr[0]) ? $weightArr[0] : '';
                        $weight_to = isset($weightArr[1]) ? $weightArr[1] : '';
                        $arr = explode('-',$details);
                        $agentId = isset($arr[0]) ? $arr[0] : '';
                        $costStr = isset($arr[1]) ? $arr[1] : '';
                        $costArr = explode('/',$costStr);
                        $calculatedCost = isset($costArr[0]) ? $costArr[0] : '';
                        $weightCost = isset($costArr[1]) ? $costArr[1] : '';
                        $pieceCost = isset($costArr[2]) ? $costArr[2] : '';
                        $csvWeightCost = $weightCost;
                        if($calculatedCost != "") {
                            $csvWeightCost = $calculatedCost;
                        }
                        $csvPeiceCost = $pieceCost;
                        $tariffDetailCSVContent .= (!empty($tariffDetailCSVContent) ? "\n" : '').$tariff_id.','.$from_zone_id.','.$to_zone_id.','.$agentId.','.$weight_from.','.$weight_to.','.$csvWeightCost.','.$csvPeiceCost.','.$formula;
                        if($status == 1) {
                                $carrierServiceDefaultRules = new carrierServiceDefaultRules();
                                $carrierServiceDefaultRules->setServiceid($serviceId);
                                $carrierServiceDefaultRules->setAgentid($agentId);
                                $carrierServiceDefaultRules->setFromWeight($weight_from);
                                $carrierServiceDefaultRules->setToWeight($weight_to);
                                $carrierServiceDefaultRules->setIsDefault(1);
                                $carrierServiceDefaultRules->setAgentType("outbound");
                                $carrierServiceDefaultRules->save();
//                              Wrong table entry and delete by najam, We have to enter in carrierServiceDefaultRules table discussed with all
//                            $carrierServiceCustomizeRules = new carrierServiceCustomizeRules();
//                            $carrierServiceCustomizeRules->setServiceid($serviceId);
//                            $carrierServiceCustomizeRules->setAgentid($agentId);
//                            $carrierServiceCustomizeRules->setUserAccountId($user_account_id);
//                            $carrierServiceCustomizeRules->setFromWeight($weight_from);
//                            $carrierServiceCustomizeRules->setToWeight($weight_to);
//                            $carrierServiceCustomizeRules->setStatus(1);
//                            $carrierServiceCustomizeRules->save();
                        }
                    }
                }
                if(!empty($tariffDetailCSVContent)) {
                    $tariffDetailCSVFile = '../_assets/csv/tariff_detail_agent_csv_' . time() . ".csv";
                    if (file_put_contents($tariffDetailCSVFile, $tariffDetailCSVContent)) {
                        $tariffDetailSql = "LOAD DATA LOCAL INFILE '" . $tariffDetailCSVFile . "' INTO TABLE `tariffs_details` CHARACTER SET 'utf8' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (
                                              `tariffs_id`,
                                              `from_zone_id`,
                                              `to_zone_id`,
                                              `agent_id`,
                                              `weight_from`,
                                              `weight_to`,
                                              `weight_cost`,
                                              `piece_cost`,
                                              `formula`
                                            );";
                        DbAccess3::runQueryWithError($tariffDetailSql);
                        $dbError = DbAccess3::$dbError;
                        if (count($dbError) > 0) {
                            $result = [
                                'status' => 'error',
                                'message' => $dbError
                            ];
                        }
                        @unlink($tariffDetailCSVFile);
                    }
                }
            }
            echo json_encode($result);
            die;
        }

        if(isset($this->form_vars['func']) && $this->form_vars['func'] == "get_calculated_pricing_string") {
            $selectedAgentTariffs = $this->form_vars['selected_agent_tariff'];
            $csvStr = "";
            $csvHeaderStr = "Country/Weight,";
            if(count($selectedAgentTariffs)) {
                $count = 0;
                foreach ($selectedAgentTariffs as $toZoneId => $weightStr) {
                    $zone = new CarrierZones($toZoneId);
                    $csvStr .= $zone->getName().",";
                    foreach ($weightStr as $weight => $details) {
                        $csvCost = '';
                        $csvCostHeading = '';
                        $arr = explode('-',$details);
                        $agentId = isset($arr[0]) ? $arr[0] : '';
                        $costStr = isset($arr[1]) ? $arr[1] : '';
                        $costArr = explode('/',$costStr);
                        $calculatedCost = isset($costArr[0]) ? $costArr[0] : '';
                        $weightCost = isset($costArr[1]) ? $costArr[1] : '';
                        $pieceCost = isset($costArr[2]) ? $costArr[2] : '';
                        $csvCost .= $pieceCost.',';
                        $csvCost .= $weightCost.',';
                        $csvCost .= $calculatedCost.',';
                        $csvCostHeading .= 'Piece Cost,';
                        $csvCostHeading .= 'Weight Cost,';
                        $csvCostHeading .= 'Cost,';
                        $agent = new AgentData($agentId);
                        $csvStr .= $agent->getAgentName().','.$csvCost;
                        /* header string */
                        if($count == 0) {
                            $csvHeaderStr .= $weight . ',' . $csvCostHeading;
                        }
                    }
                    $csvStr = rtrim($csvStr, ",");
                    $csvStr .= "\r\n";
                    if($count == 0) {
                        $csvHeaderStr = rtrim($csvHeaderStr, ",");
                        $csvHeaderStr .= "\r\n";
                    }
                    $count++;
                }
            }
            $returnString = $csvHeaderStr . $csvStr;
            $return = [
                'status' => 'success',
                'csv_sting' => $returnString
            ];
            echo json_encode($return);
            die;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_calculated_pricing') {
            $returnString = $this->form_vars['csv_string'];
            $fileName = "agent_tariff_pricing_csv_" . time(). ".csv";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            exit;
        }


    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .table td, .table th {
                font-size: 10px !important;
            }
            .table-borderless > tbody > tr > td,
            .table-borderless > tbody > tr > th,
            .table-borderless > tfoot > tr > td,
            .table-borderless > tfoot > tr > th,
            .table-borderless > thead > tr > td,
            .table-borderless > thead > tr > th {
                border: none;
            }
            #advance_search_fields {
                display: none;
            }
            .btn-secelct-option {
                width: 100%;
                margin: 5px auto;
            }
            .ms-container {
                width: 100%;
            }
        </style>
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
        <script src="../assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"
                type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-bootstrap-multiselect.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/serializeForm.js" type="text/javascript"></script>
        <script type="text/javascript">
            var countriesTariffs = [];
            var tariffCountry = {};
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click','#search_tariff', function() {
                    var form_data = $("#frm_price_search").serializeArray();
                    form_data.push({name: 'func', value: 'get_tariff'});
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "get_agent_pricing.php",
                        data: form_data,
                        dataType: "json",
                        success: function (response) {
                            $.unblockUI();
                            var weightLimits = response.weight_limits;
                            var zones = response.zones;
                            var toZonesIds = response.tariff_data;
                            var html = '<table class="table table-bordered table-condensed"><thead><tr>';
                            var str = response.tariff_details;
                            var tf_detail = str.replaceAll('"',"'");
                            html += '<input type="hidden" name="tariff_detail" value="' + tf_detail + '" >';
                            html += '<th style=width:65px;">Country / Weight Limits</th>';
                            $.each(weightLimits,function(weightLimitkey, weightArr){
                                html += '<th style="border-right: 0px;min-width: 300px;">';
                                html += '<table class="table table-bordered table-condensed flip-content price_table" style="margin-bottom: 3px;min-width:215px;"><tbody><tr>';
                                html += '<th style="width: 55%;text-align:center;">'+weightArr['start']+'-'+weightArr['end']+'</th>';
                                html += '<th style="width: 15%;text-align:center;">I</th>';
                                html += '<th style="width: 15%;text-align:center;">W</th>';
                                html += '<th style="width: 15%;text-align:center;">T</th>';
                                html += '</tr></tbody></table>';
                                html += '</th>';
                            });
                            html += '</tr></thead>';
                            html += '<tbody>';
                            $.each(toZonesIds,function(toZonesId, weightArr){
                                var zoneName = zones[toZonesId];
                                html += '<tr>';
                                html += '<th style="border-right: 0px;text-align: center;">'+zoneName+'</th>';
                                $.each(weightArr,function(weightLimit, details){
                                    html += '<td>';
                                    html += '<table class="table table-bordered table-condensed flip-content price_table" style="margin-bottom: 3px;min-width:215px;"><tbody>';
                                    $.each(details,function(key, detail) {
                                        var popOverHtml = "<table class='table table-bordered table-striped table-condensed'>";
                                        popOverHtml += '<tr><td><strong>Tariff</strong></td><td>'+detail.tn+'</td></tr>';
                                        popOverHtml += '<tr><td><strong>Agent</strong></td><td>'+detail.agn+'</td></tr>';
                                        popOverHtml += '<tr><td><strong>Weight Limit</strong></td><td>'+weightLimit+'</td></tr>';
                                        popOverHtml += '<tr><td><strong>Zone</strong></td><td>'+detail.zn+'</td></tr>';
                                        popOverHtml += '<tr><td><strong>Code</strong></td><td>'+detail.sc+'</td></tr>';
                                        popOverHtml += '<tr><td><strong>Item Cost</strong></td><td>'+detail.pc +' ' + detail.cur + '</td></tr>';
                                        popOverHtml += '<tr><td><strong>Weight Cost</strong></td><td>'+detail.wc +' ' + detail.cur + '</td></tr>';
                                        popOverHtml += '<tr><td><strong>Total Cost</strong></td><td>'+detail.tc +' ' + detail.cur + '</td></tr>';
                                        popOverHtml += '</table>';
                                        var checked = "";
                                        if(key == 0) {
                                            checked = 'checked="checked"';
                                        }
                                        var costStr = detail.tc;
                                        if(detail.wc) {
                                            costStr += '/'+detail.wc;
                                        }
                                        if(detail.pc) {
                                            costStr += '/'+detail.pc;
                                        }
                                        html += '<tr>';
                                        html += '<td style="width: 55%;"><label><input class="country_tariff_check" type="radio" name="selected_agent_tariff[' + toZonesId + '][' + weightLimit + ']" value="' + detail.aid + '-' + costStr + '" '+checked+'  /> &nbsp;&nbsp;<a href="javascript:;" class="popovers" data-html="true" data-container="body" data-trigger="hover" data-content="'+popOverHtml+'" data-original-title="'+detail.tn+'" >'+detail.tn+'</a></label></td>';
                                        html += '<td style="width: 15%;">' + detail.pc + ' ' + detail.cur + '</td>';
                                        html += '<td style="width: 15%;">' + detail.wc + ' ' + detail.cur + '</td>';
                                        html += '<td style="width: 15%;">' + detail.tc + ' ' + detail.cur + '</td>';
                                        html += '</tr>';
                                    });
                                    html += '</tbody></table>';
                                    html += '</td>';
                                });
                                html += '</tr>';
                            });
                            html += '</tbody>';
                            html += '</table>';
                            $('#compare_price_table').html(html);
                            $("table.price_table .popovers").popover();
                        },
                        error: function () {
                            $.unblockUI();
                            //alert('error handing here');
                        }
                    });
                });

                $(document).on('click', '#save_tariff_modal_btn', function() {
                    $('#saveTariffModal').modal('show');
                });

                $(document).on('click', '#btn_tariff_save', function() {
                    $.blockUI();
                    var form_data = $("#frm_price_data").serializeArray();
                    form_data.push({name: 'func', value: 'save_tariff'});
                    var tariff_name = $('#tariff_name').val();
                    form_data.push({name: 'tariff_name', value: tariff_name});
                    var start_date = $('#start_date').val();
                    form_data.push({name: 'start_date', value: start_date});
                    var end_date = $('#end_date').val();
                    form_data.push({name: 'end_date', value: end_date});
                    $.ajax({
                        type: "POST",
                        url: "get_agent_pricing.php",
                        data: form_data,
                        dataType: "json",
                        success: function (response) {
                            $.unblockUI();
                            if(response.status == "success") {
                                swal({
                                    title:"Success!",
                                    text:response.message,
                                    type: "success",
                                    html:true
                                });
                                $("#tariffId").val(response.tariff_id);
                                $('#saveTariffModal').modal('hide');
                            } else if(response.status == "error") {
                                swal({
                                    title:"Sorry!",
                                    text:response.message,
                                    type: "error",
                                    html:true
                                });
                            }
                        },
                        error: function () {
                            $.unblockUI();
                        }
                    });
                });

                $("#download_tariff_csv").click(function(){
                    $("#download_tariff_csv_frm").submit();
                });

                $(document).on('click', '#download_calculated_pricing', function() {
                    $.blockUI();
                    var form_data = $("#frm_price_data").serializeArray();
                    form_data.push({name: 'func', value: 'get_calculated_pricing_string'});
                    $.ajax({
                        type: "POST",
                        url: "get_agent_pricing.php",
                        data: form_data,
                        dataType: "json",
                        success: function (response) {
                            $.unblockUI();
                            if(response.status == "success") {
                                $('#csv_string').val(response.csv_sting);
                                $('#download_csv_pricing_form').submit();
                            }
                        },
                        error: function () {
                            $.unblockUI();
                        }
                    });
                });

            });

            function getAgent() {
                var service_id = $('#service_id').val();
                var form_data = new FormData();
                form_data.append('service_id', service_id);
                form_data.append('func', 'get_service_tariff_agent');
                $.blockUI();
                $.ajax({
                    type: "POST",
                    url: "get_agent_pricing.php",
                    data: form_data,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $.unblockUI();
                        if(response.status == 'success') {
                            $('#agent_id').html(response.options);
                            $('#agent_id').selectpicker('refresh');
                        }
                    },
                    error: function () {
                        $.unblockUI();
                        //alert('error handing here');
                    }
                });
            }

        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>
        <style>

        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light all_portlet" id="search_portlet">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-search"></i> Search Panel</div>
                <div class="actions">

                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form name="frm_price_search" id="frm_price_search" method="post" action="">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="label-account">Service</label>
                            <div class="form-group">
                                <?php
                                $slectedServiceId = '';
                                if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                                    $slectedServiceId = $_GET['service_id'];
                                }
                                ?>
                                <?php echo Ddl::generateServiceDDLWithImage('service_id', $slectedServiceId, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body" onchange="getAgent()" ', '','', 'name'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="label-account">Agent</label>
                            <select class="bs-select form-control" name="agent_id[]" id="agent_id"  multiple="multiple" required="" data-live-search="true" data-actions-box="true"  data-max-options="'.$this->toCountryLimit.'" data-container="body" data-size="8">
                                <option value="">Select Agent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br />
                            <button type="button" class="btn btn-md btn-primary" id="search_tariff" >Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="portlet light all_portlet" id="result_portlet">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-search"></i> Compare Pricing</div>
                <div class="actions">
                    <button type="button" class="btn btn-sm btn-primary" id="download_tariff_csv" >Download Tariff</button>
                    <button type="button" class="btn btn-sm btn-primary" id="download_calculated_pricing">Download Pricing</button>
                    <button type="button" class="btn btn-sm btn-default" id="save_tariff_modal_btn" >Save Tariff</button>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form name="frm_price_data" id="frm_price_data" method="post" action="">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive" id="compare_price_table"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form name="download_csv_pricing_form" id="download_csv_pricing_form" method="post" action="">
            <input type="hidden" name="func" value="download_calculated_pricing" />
            <input type="hidden" name="csv_string" id="csv_string" value="" />
        </form>

        <form name="download_tariff_csv_frm" id="download_tariff_csv_frm" target="_blank" method="post" action="tariffs_list.php">
            <input type="hidden" name="func" value="download_tariff_detail_csv" />
            <input type="hidden" name="tariffId" id="tariffId" value="" />
        </form>

        <div id="saveTariffModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Save Tariff</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div id="new_tariff">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tariff Name</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <input type="text" name="tariff_name" id="tariff_name" class="form-control validate_check" value="oweat_<?php echo time(); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-btn">
                                            <button class="btn default btn-calender" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        <input type="text" class="form-control validate_check" readonly name="start_date" id="start_date" placeholder="" value="" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-btn">
                                            <button class="btn default btn-calender" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        <input type="text" class="form-control validate_check" readonly name="end_date" id="end_date" placeholder="" value="" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn_tariff_save">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
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
