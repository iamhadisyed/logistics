<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes(
    ['currency.class',
    'currencyfilter.class',
    'tariffspricingrules.class',
    'tariffspricingrulesfilter.class',
    'tariffspricingrulesdetails.class',
    'tariffspricingrulesdetailsfilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'carrier.class',
    'carrierfilter.class',
    'services.class',
    'servicesfilter.class',
    'tariffs.class',
    'tariffsfilter.class'
    ]);

set_time_limit(300);

class Page extends BasePage {

    public $user;
    private $tariffPricingObj;
    private $tariffId = 0;
    private $ruleId = 0;
    private $msg;
    private $params = "";
    private $tariffPricingRulesObj = array();
    private $tariffPricingRulesDetailsObj = array();

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Tariff Pricing"
        );
        
        if((isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) && (isset($_GET['service_id']) && !empty($_GET['service_id']))) {
            $this->params = "carrier_id=" . $_GET['carrier_id'] . "&service_id=" . $_GET['service_id'];
        } else {
            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                $this->params = "carrier_id=" . $_GET['carrier_id'];
            }
            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                $this->params = "service_id=" . $_GET['service_id'];
            }
        }

        $this->user = SessionManager::getUser();
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_calculated_tariff') {
            $user_account_id = $this->user->getUserAccountId();
            $carrier_id = $this->form_vars['carrier_id'];
            $service_id = $this->form_vars['service_id'];
            $name = $this->form_vars['tariff_name'];
            $type = 'Customer';
            if (isset($this->form_vars['chkActive']) && ($this->form_vars['chkActive'] == 'on')) {
                $status = 1;
            } else {
                $status = 0;
            }
            $currency_id = $this->form_vars['currency_id'];
            $start_date = date('Y-m-d', strtotime($this->form_vars['start_date']));
            $end_date = date('Y-m-d', strtotime($this->form_vars['end_date']));
            $description = $this->form_vars['description'];

            $date_added = time();
            $added_by = $this->user->getId();
            $date_update = time();
            $update_by = $this->user->getId();
            
            /* Trarif Rules */            
            if(!empty($this->form_vars['tariff_rules_name'])) {
                $tariffPricingRuleName = $this->form_vars['tariff_rules_name'];
            } else {
                $tariffPricingRuleName = $this->form_vars['tariff_name'] . " rules";
            }
            
            $tariffsPricingRulesObj = new TariffsPricingRules();
            $tariffsPricingRulesObj->setTariffId($this->form_vars['tariff_selected_id']);
            $tariffsPricingRulesObj->setName($tariffPricingRuleName);
            $tariffsPricingRulesObj->setDateAdded($date_added);
            $tariffsPricingRulesObj->setAddedBy($added_by);
            $tariffsPricingRulesObj->setDateUpdated($date_update);
            $tariffsPricingRulesObj->setUpdatedBy($update_by);
            $tariffsPricingRulesObj->save();
            
            $tariff_pricing_rule_id = $tariffsPricingRulesObj->getId();
            
            if(is_numeric($this->form_vars['whole_tariff_margin']) || is_numeric($this->form_vars['whole_tariff_line_haul'])) {
                $margin = $this->form_vars['whole_tariff_margin'];
                $margin_type = $this->form_vars['whole_tariff_margin_type'];
                $margin_weight_cost = $this->form_vars['whole_tariff_margin_weight_cost'];
                $margin_piece_cost = $this->form_vars['whole_tariff_margin_piece_cost'];
                $linehaul = $this->form_vars['whole_tariff_line_haul'];
                $linehaul_type = $this->form_vars['whole_tariff_line_haul_type'];
                
                $tariffsPricingRulesDetailsObj = new TariffsPricingRulesDetails();
                $tariffsPricingRulesDetailsObj->setTariffPricingRuleId($tariff_pricing_rule_id);
                if(is_numeric($this->form_vars['whole_tariff_margin'])) {
                    $tariffsPricingRulesDetailsObj->setMargin($margin);
                    $tariffsPricingRulesDetailsObj->setMarginType($margin_type);
                    $tariffsPricingRulesDetailsObj->setMarginWeightCost($margin_weight_cost);
                    $tariffsPricingRulesDetailsObj->setMarginPieceCost($margin_piece_cost);
                }
                if(is_numeric($this->form_vars['whole_tariff_line_haul'])) {
                    $tariffsPricingRulesDetailsObj->setLinehaul($linehaul);
                    $tariffsPricingRulesDetailsObj->setLinehaulType($linehaul_type);
                }
                $tariffsPricingRulesDetailsObj->setTariffPricingType("whole");
                $tariffsPricingRulesDetailsObj->setDateAdded($date_added);
                $tariffsPricingRulesDetailsObj->setAddedBy($added_by);
                $tariffsPricingRulesDetailsObj->setDateUpdated($date_update);
                $tariffsPricingRulesDetailsObj->setUpdatedBy($update_by);
                $tariffsPricingRulesDetailsObj->save();
            }
            
            foreach($this->form_vars['apply'] as $apply) {
                if(is_numeric($apply['to_zone_id']) || (is_numeric($apply['weight_from']) && is_numeric($apply['weight_to']))) {                     
                    $to_zone_id = $apply['to_zone_id'];
                    $weight_from = $apply['weight_from'];
                    $weight_to = $apply['weight_to'];
                    $margin = $apply['margin'];
                    $margin_type = $apply['margin_type'];
                    $margin_weight_cost = $apply['margin_weight_cost'];
                    $margin_piece_cost = $apply['margin_piece_cost'];
                    $linehaul = $apply['line_haul'];
                    $linehaul_type = $apply['line_haul_type'];

                    $tariffsPricingRulesDetailsObj = new TariffsPricingRulesDetails();
                    $tariffsPricingRulesDetailsObj->setTariffPricingRuleId($tariff_pricing_rule_id);
                    $tariffsPricingRulesDetailsObj->setToZoneId($to_zone_id);
                    $tariffsPricingRulesDetailsObj->setWeightFrom($weight_from);
                    $tariffsPricingRulesDetailsObj->setWeightTo($weight_to);
                    $tariffsPricingRulesDetailsObj->setMargin($margin);
                    $tariffsPricingRulesDetailsObj->setMarginType($margin_type);
                    $tariffsPricingRulesDetailsObj->setMarginWeightCost($margin_weight_cost);
                    $tariffsPricingRulesDetailsObj->setMarginPieceCost($margin_piece_cost);
                    $tariffsPricingRulesDetailsObj->setLinehaul($linehaul);
                    $tariffsPricingRulesDetailsObj->setLinehaulType($linehaul_type);
                    $tariffsPricingRulesDetailsObj->setTariffPricingType("multiple");
                    $tariffsPricingRulesDetailsObj->setDateAdded($date_added);
                    $tariffsPricingRulesDetailsObj->setAddedBy($added_by);
                    $tariffsPricingRulesDetailsObj->setDateUpdated($date_update);
                    $tariffsPricingRulesDetailsObj->setUpdatedBy($update_by);
                    $tariffsPricingRulesDetailsObj->save();
                }
            }
            
            /* Trarif */
            $tariffsObj = new Tariffs();
            $tariffsObj->setUserAccountId($user_account_id);
            $tariffsObj->setCarrierId($carrier_id);
            $tariffsObj->setServiceId($service_id);
            $tariffsObj->setName($name);
            $tariffsObj->setStatus($status);
            $tariffsObj->setCurrencyId($currency_id);
            $tariffsObj->setTariffType($type);
            $tariffsObj->setStartDate($start_date);
            $tariffsObj->setEndDate($end_date);
            $tariffsObj->setDescription($description);
            $tariffsObj->setTariffTemplate('default');
            $tariffsObj->setTariffsPricingRuleId($tariff_pricing_rule_id);
            $tariffsObj->setDateAdded($date_added);
            $tariffsObj->setAddedBy($added_by);
            $tariffsObj->setDateUpdated($date_update);
            $tariffsObj->setUpdatedBy($update_by);
            $tariffsObj->save();
            $tarif_id = $tariffsObj->getId();
            /* Trarif Details */
            $sql = "SELECT 
                       from_zone_id
                    FROM
                       tariffs_details
                    WHERE tariffs_id=" . $this->form_vars['tariff_selected_id'];
            $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
            $fromZoneId = 0;
            if(count($tariffDetails) > 0) {
                $fromZoneId = $tariffDetails[0]->getFromZoneId();
            }
            TariffsDetails::deleteTarifsDetailByTarifId($tarif_id);
            foreach($this->form_vars['zone_new'] as $weight => $zones) {
                $weights = explode("/", $weight);
                $weight_from = $weights[0];
                $weight_to = $weights[1];
                foreach($zones as $toZoneId => $value) {
                    $from_zone_id = $fromZoneId;
                    $to_zone_id = $toZoneId;
                    $weight_from = $weight_from;
                    $weight_to = $weight_to;
                    $values = explode("/", $value);
                    $user_weight_cost = $values[0];
                    $user_peice_cost = $values[1];
                    $tariffsDetailObj = new TariffsDetails();
                    $tariffsDetailObj->setTariffsId($tarif_id);
                    $tariffsDetailObj->setFromZoneId($from_zone_id);
                    $tariffsDetailObj->setToZoneId($to_zone_id);
                    $tariffsDetailObj->setWeightFrom($weight_from);
                    $tariffsDetailObj->setWeightTo($weight_to);
                    $tariffsDetailObj->setWeightCost($user_weight_cost);
                    $tariffsDetailObj->setPieceCost($user_peice_cost);
                    $tariffsDetailObj->save();
                }
            }           
            
            $this->msg = 'Tariff has been added successfully.';
            $this->flashMsg->success($this->msg);
            /* End Trarif Details Pricing */
        }

        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            $this->tariffId = $_GET['id'];            
        }
        
        if (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id'])) {
            $this->ruleId = $_GET['rule_id'];
            $this->tariffPricingRulesObj = new TariffsPricingRules($this->ruleId);
            if(!empty($this->tariffPricingRulesObj) && count($this->tariffPricingRulesObj) > 0) {
                $this->tariffId = $this->tariffPricingRulesObj->getTariffId();
            }            
            $tariffPricingRulesDetails = new TariffsPricingRulesDetailsFilter();
            $tariffPricingRulesDetails->addFieldFilter("   tprd.tariff_pricing_rule_id", $this->tariffPricingRulesObj->getId());
            $this->tariffPricingRulesDetailsObj = $tariffPricingRulesDetails->getList(" tprd.id,tprd.to_zone_id,tprd.weight_from,tprd.weight_to,tprd.margin,tprd.margin_type,tprd.margin_weight_cost,tprd.margin_piece_cost,tprd.linehaul,tprd.linehaul_type,tprd.tariff_pricing_type");
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'load_tariff_details') {
            $tariffId = $this->form_vars['tariff_id'];
            $sql = "SELECT td.id,td.to_zone_id,cz.name as zone_name FROM tariffs_details td JOIN carrier_zones cz ON td.to_zone_id = cz.id WHERE td.tariffs_id = " . $tariffId . " GROUP BY(cz.id);";
            $zones = TariffsDetails::getTariffsDetailsListFromSql($sql);
            $option = '<option value="">Select Zone</option>';
            if (count($zones) > 0) {
                foreach ($zones as $z) {
                    $option .= '<option value="' . $z->getToZoneId() . '">' . $z->getZoneName() . '</option>';
                }
            }
            $output['zones'] =  $option;
            
            $sql = "select
                        min(td.`weight_from`) as weight_from,
                        max(td.`weight_to`) as weight_to,
                        min(td.`weight_to` - td.`weight_from`) as step 
                    from
                        `tariffs_details` td
                    where td.`tariffs_id` = ".$tariffId;
            $tariffDetailWeight = TariffsDetails::getTariffsDetailsListFromSql($sql);
            $minWeight = "";
            $maxWeight = "";
            $step = "";
            if(count($tariffDetailWeight)) {
                $minWeight = $tariffDetailWeight[0]->getWeightFrom();
                $maxWeight = $tariffDetailWeight[0]->getWeightTo();
                $step = $tariffDetailWeight[0]->getStep();
            }

            $sql = "SELECT 
                        td.id,
                        td.tariffs_id,
                        td.from_zone_id,
                        td.to_zone_id,
                        td.weight_from,
                        td.weight_to,
                        td.weight_cost,
                        td.piece_cost,
                        td.formula,
                        cz.name AS zone_name 
                      FROM
                        tariffs_details td 
                        JOIN carrier_zones cz 
                          ON td.to_zone_id = cz.id
                      WHERE td.tariffs_id = " . $tariffId . " ORDER BY td.weight_from ASC";
            $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
            $tariff_details = [];
            $zones = [];
            if (!empty($tariffDetails)) {
                foreach ($tariffDetails as $tariffsDetailsObj) {
                    $weight_from = $tariffsDetailsObj->getWeightFrom();
                    $weight_to = $tariffsDetailsObj->getWeightTo();
                    $weight = $weight_from . "/" . $weight_to;
                    $zone_id = $tariffsDetailsObj->getToZoneId();
                    $id = $tariffsDetailsObj->getId();
                    $tariff_details[$zone_id][$weight] = $tariffsDetailsObj->getWeightCost() . "/" . $tariffsDetailsObj->getPieceCost();
                    if (!in_array($zone_id, $zones)) {
                        $zones[] = $zone_id;
                    }
                }
                sort($zones);
//                foreach($tariff_details as $k => $td) {
//                    foreach($zones as $zk => $z) {
//                        if(!isset($tariff_details[$k][$z])) {
//                            $tariff_details[$k][$z] = '';
//                        }
//                        ksort($tariff_details[$k]);
//                    }
//                }
            }
            $tariffDetailData = [];
            foreach($zones as $zone) {
                $zoneWeights = $tariff_details[$zone];
//                for ($weight = $minWeight; $weight <= $maxWeight;) {
//                    if($this->numberFormat(($weight + $step)) <= $maxWeight){
                        $startLimit = $this->numberFormat($weight);
                        $endLimit = $this->numberFormat($weight + $step);
                        $tariffFound = 0;
                        foreach($zoneWeights as $weights => $cost){
//                            $limits = explode("/",$weights);
                            $tariffDetailData[$weights][$zone] = $cost;

//                            if($limits[0] <= $startLimit && $endLimit <= $limits[1]) {
//                                $tariffDetailData[$startLimit."/".$endLimit][$zone] = $cost;
//                                $tariffFound = 1;
//                                break;
//                            }
                        }
//                        if($tariffFound == 0) {
//                            $tariffDetailData[$startLimit."/".$endLimit][$zone] = "";
//                        }
//                    }
//                    $weight += $step;
//                }
            }
            $tdhtml = '';
            $tdhtml .= '<div class="table-responsive">';
                $tdhtml .= '<table class="table table-bordered fixed custom_table" id="tarif_csv_data">';
                        $tdhtml .= '<thead>';
                            $tdhtml .= '<tr>';                                
                                $tdhtml .= '<th class="bg-blue"><b>Weight/Zone</b></th>';
                                foreach ($zones as $k => $zone) {                                                   
                                    $toZone = new CarrierZones($zone);
                                    $tdhtml .= '<th class="bg-blue"><b>' . $toZone->getName() . '</b><input type="hidden" name="zonename[]" value="' . $toZone->getName() . '"></th>';
                                }
                            $tdhtml .= '</tr>';
                        $tdhtml .= '</thead>';
                        $tdhtml .= '<tbody>';
                            $key = 0;
                            foreach ($tariffDetailData as $weight => $detail) {
                                if($key == 0) {
                                    $tdhtml .= '<tr>';
                                        $tdhtml .= '<td class="bg-info">';
                                            $tdhtml .= '<div>From Weight / To Weight</div>';
                                        $tdhtml .= '</td>';  
                                    foreach ($detail as $k => $dt) {
                                        $tdhtml .= '<td>';
                                            $tdhtml .= '<div class="col-md-6">';
                                                $tdhtml .= '<div>KG Cost / Item Cost</div>';
                                            $tdhtml .= '</div>';
                                            $tdhtml .= '<div class="col-md-6">';
                                                $tdhtml .= '<div>KG Cost / Item Cost</div>';
                                            $tdhtml .= '</div>';
                                        $tdhtml .= '</td>'; 
                                    }        
                                    $tdhtml .= '</tr>';
                                }
                                $tdhtml .= '<tr>';                           
                                    $tdhtml .= '<td class="bg-info">';
                                        $tdhtml .= '<input type="text" name="weight[]" id="" class="form-control zone_weight" value="' . $weight . '" readonly="readonly">';
                                    $tdhtml .= '</td>';
                                    foreach ($detail as $k => $dt) {
                                        $tdhtml .= '<td>';
                                            $idValue = str_replace(".","_",$weight);
                                            $tdhtml .= '<div class="row">';
                                                $tdhtml .= '<div class="col-md-6">';                                                            
                                                    $tdhtml .= '<input type="text" name="zones[' . $weight . '][' . $k . ']" id="" class="form-control zone_price_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" value="' . $dt . '" readonly="readonly">';
                                                $tdhtml .= '</div>';
                                                $tdhtml .= '<div class="col-md-6">';
                                                    $tdhtml .= '<input type="hidden" name="zone_new[' . $weight . '][' . $k . ']" id="zone_new_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="form-control" value="" readonly="readonly">';
                                                    $tdhtml .= '<div class="form-control" id="zone_new_' . str_replace("/","_",$idValue) . '_' . $k . '" readonly="readonly"></div>';
                                                $tdhtml .= '</div>';
                                            $tdhtml .= '</div>';
                                        $tdhtml .= '</td>';   
                                    } 
                                    $key++;
                                $tdhtml .= '</tr>';
                            }
                        $tdhtml .= '</tbody>';
                $tdhtml .= '</table>';
            $tdhtml .= '</div>';
            $output['tariff_detail'] =  $tdhtml;
            
            $sql = "SELECT 
                        t.id,
                        t.user_account_id,
                        t.carrier_id,
                        t.service_id,
                        t.name,
                        t.status,
                        t.currency_id,
                        t.start_date,
                        t.end_date,
                        t.description
                    FROM
                        tariffs t 
                    WHERE t.id = " . $tariffId;
            $tariff = Tariffs::getTariffsListFromSql($sql);
            $output['carrierId'] =  $tariff[0]->getCarrierId();
            $output['serviceId'] =  $tariff[0]->getServiceId();
            $output['name'] =  $tariff[0]->getName();
            $output['currencyId'] =  $tariff[0]->getCurrencyId();
            $output['startDate'] =  formatDate(date('Y-m-d', strtotime($tariff[0]->getStartDate())));
            $output['endDate'] =  formatDate(date('Y-m-d', strtotime($tariff[0]->getEndDate())));
            $output['status'] =  $tariff[0]->getStatus();
            $output['description'] =  $tariff[0]->getDescription();
            
            $carrierObj = new Carrier($tariff[0]->getCarrierId());
            $output['carrierName'] =  $carrierObj->getCarrier();
            $serviceObj = new Services($tariff[0]->getServiceId());
            $output['serviceName'] =  $serviceObj->getname();
            
            echo json_encode($output);
            exit;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'calculate_multiple_price') {
            $formData = '';
            parse_str($this->form_vars['pricing_form'], $formData);
            $result = array();
            $color = array();
            foreach($formData['apply'] as $apply) {
                foreach($formData['zones'] as $weight => $zone) {    
                    if(is_numeric($apply['to_zone_id'])) {
                        if((is_numeric($apply['weight_from'])) && (is_numeric($apply['weight_to']))) {
                            $w_from = number_format($apply['weight_from'], 2, '.', '');
                            $w_to = number_format($apply['weight_to'], 2, '.', '');
                            $checkWeight = explode("/", $weight);
                            if(($checkWeight[0] >= $w_from) && ($checkWeight[1] <= $w_to)) {
                                foreach($zone as $zoneKey => $val) {
                                    if(($zoneKey == $apply['to_zone_id']) && (!empty($val))) {
                                        $cost = explode("/", $val);
                                        $resultPrice = $val;
                                        if(!empty($apply['margin']) && is_numeric($apply['margin'])) {                                                
                                            if($apply['margin_type'] == "percentage") {
                                                if(isset($apply['margin_weight_cost'])) {
                                                    $weightMargin = (($apply['margin']/100) * $cost[0]);
                                                } else {
                                                    $weightMargin = 0;
                                                }
                                                if(isset($apply['margin_piece_cost'])) {
                                                    $pieceMargin = (($apply['margin']/100) * $cost[1]);
                                                } else {
                                                    $pieceMargin = 0;
                                                }
                                            } else {
                                                if(isset($apply['margin_weight_cost'])) {
                                                    $weightMargin = $apply['margin'];                                                   
                                                } else {
                                                    $weightMargin = 0;
                                                }
                                                if(isset($apply['margin_piece_cost'])) {                                                   
                                                    $pieceMargin = $apply['margin'];
                                                } else {
                                                    $pieceMargin = 0;
                                                }
                                            }
                                            if(is_numeric($apply['line_haul'])) {
                                                if($apply['line_haul_type'] == "kilogram") {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                } else {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $apply['line_haul'];
                                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                }
                                            } else {
                                                $resultPrice = ($cost[0] + $weightMargin) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul = 0 ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }
                                        } else {
                                            if(is_numeric($apply['line_haul'])) {
                                                if($apply['line_haul_type'] == "kilogram") {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                } else {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $apply['line_haul'];
                                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                }                                              
                                            }
                                        }                                            
                                    }
                                }
                            }
                        } else {
                            foreach($zone as $zoneKey => $val) {
                                if(($zoneKey == $apply['to_zone_id']) && (!empty($val))) {
                                    $cost = explode("/", $val);
                                    $resultPrice = $val;
                                    if(!empty($apply['margin']) && is_numeric($apply['margin'])) {
                                        if($apply['margin_type'] == "percentage") {
                                            if(isset($apply['margin_weight_cost'])) {
                                                $weightMargin = (($apply['margin']/100) * $cost[0]);
                                            } else {
                                                $weightMargin = 0;
                                            }
                                            if(isset($apply['margin_piece_cost'])) {
                                                $pieceMargin = (($apply['margin']/100) * $cost[1]);
                                            } else {
                                                $pieceMargin = 0;
                                            }
                                        } else {
                                            if(isset($apply['margin_weight_cost'])) {
                                                $weightMargin = $apply['margin'];                                                   
                                            } else {
                                                $weightMargin = 0;
                                            }
                                            if(isset($apply['margin_piece_cost'])) {                                                   
                                                $pieceMargin = $apply['margin'];
                                            } else {
                                                $pieceMargin = 0;
                                            }
                                        }
                                        if(is_numeric($apply['line_haul'])) {
                                            if($apply['line_haul_type'] == "kilogram") {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            } else {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $apply['line_haul'];
                                                $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }
                                        } else {
                                            $resultPrice = ($cost[0] + $weightMargin) . "/" . ($cost[1] + $pieceMargin);
                                            $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                            $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul =  0 ,$cost[0] ,$cost[1]);               
                                            $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                        }
                                    } else {
                                        if(is_numeric($apply['line_haul'])) {
                                            if($apply['line_haul_type'] == "kilogram") {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            } else {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $apply['line_haul'];
                                                $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }   
                                        }
                                    }                                            
                                }
                            }
                        }
                    } else {
                        if((is_numeric($apply['weight_from'])) && (is_numeric($apply['weight_to']))) {
                            $w_from = number_format($apply['weight_from'], 2, '.', '');
                            $w_to = number_format($apply['weight_to'], 2, '.', '');
                            $checkWeight = explode("/", $weight);
                            if(($checkWeight[0] >= $w_from) && ($checkWeight[1] <= $w_to)) {
                                foreach($zone as $zoneKey => $val) {
                                    if((!empty($val))) {
                                        $cost = explode("/", $val);
                                        $resultPrice = $val;
                                        if(!empty($apply['margin']) && is_numeric($apply['margin'])) {                                                
                                            if($apply['margin_type'] == "percentage") {
                                                if(isset($apply['margin_weight_cost'])) {
                                                    $weightMargin = (($apply['margin']/100) * $cost[0]);
                                                } else {
                                                    $weightMargin = 0;
                                                }
                                                if(isset($apply['margin_piece_cost'])) {
                                                    $pieceMargin = (($apply['margin']/100) * $cost[1]);
                                                } else {
                                                    $pieceMargin = 0;
                                                }
                                            } else {
                                                if(isset($apply['margin_weight_cost'])) {
                                                    $weightMargin = $apply['margin'];                                                   
                                                } else {
                                                    $weightMargin = 0;
                                                }
                                                if(isset($apply['margin_piece_cost'])) {                                                   
                                                    $pieceMargin = $apply['margin'];
                                                } else {
                                                    $pieceMargin = 0;
                                                }
                                            }
                                            if(is_numeric($apply['line_haul'])) {
                                                if($apply['line_haul_type'] == "kilogram") {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                } else {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $apply['line_haul'];
                                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                }
                                            } else {
                                                $resultPrice = ($cost[0] + $weightMargin) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul = 0 ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }
                                        } else {
                                            if(is_numeric($apply['line_haul'])) {
                                                if($apply['line_haul_type'] == "kilogram") {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                } else {
                                                    $toWeight = explode("/", $weight);
                                                    $lineHaul = $apply['line_haul'];
                                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                                }
                                            }
                                        }                                            
                                    }
                                }
                            }
                        } else {
                            foreach($zone as $zoneKey => $val) {
                                if(!empty($val)) {
                                    $cost = explode("/", $val);
                                    $resultPrice = $val;
                                    if(!empty($apply['margin']) && is_numeric($apply['margin'])) {                                                
                                        if($apply['margin_type'] == "percentage") {
                                            if(isset($apply['margin_weight_cost'])) {
                                                $weightMargin = (($apply['margin']/100) * $cost[0]);
                                            } else {
                                                $weightMargin = 0;
                                            }
                                            if(isset($apply['margin_piece_cost'])) {
                                                $pieceMargin = (($apply['margin']/100) * $cost[1]);
                                            } else {
                                                $pieceMargin = 0;
                                            }
                                        } else {
                                            if(isset($apply['margin_weight_cost'])) {
                                                $weightMargin = $apply['margin'];                                                   
                                            } else {
                                                $weightMargin = 0;
                                            }
                                            if(isset($apply['margin_piece_cost'])) {                                                   
                                                $pieceMargin = $apply['margin'];
                                            } else {
                                                $pieceMargin = 0;
                                            }
                                        }
                                        if(is_numeric($apply['line_haul'])) {
                                            if($apply['line_haul_type'] == "kilogram") {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            } else {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $apply['line_haul'];
                                                $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }
                                        } else {
                                            $resultPrice = ($cost[0] + $weightMargin) . "/" . ($cost[1] + $pieceMargin);
                                            $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                            $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul = 0 ,$cost[0] ,$cost[1]);               
                                            $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                        }
                                    } else {
                                        if(is_numeric($apply['line_haul'])) {
                                            if($apply['line_haul_type'] == "kilogram") {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $toWeight[1] * $apply['line_haul'];
                                                $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            } else {
                                                $toWeight = explode("/", $weight);
                                                $lineHaul = $apply['line_haul'];
                                                $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                            }   
                                        }
                                    }                                            
                                }
                            }
                        }
                    }
                }
            }
            echo json_encode(array('colors' => $color, 'fields' => $result));
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'calculate_whole_tariff_price') {
            $formData = '';
            parse_str($this->form_vars['pricing_form'], $formData);
            $result = array();
            foreach($formData['zones'] as $weight => $zone) {    
                foreach($zone as $zoneKey => $val) {
                    if(!empty($val)) {
                    $cost = explode("/", $val);
                    $resultPrice = $val;
                        if(is_numeric($formData['whole_tariff_margin'])) {                            
                            if($formData['whole_tariff_margin_type'] == "percentage") {
                                if(isset($formData['whole_tariff_margin_weight_cost'])) {
                                    $weightMargin = (($formData['whole_tariff_margin']/100) * $cost[0]);
                                } else {
                                    $weightMargin = 0;
                                }
                                if(isset($formData['whole_tariff_margin_piece_cost'])) {
                                    $pieceMargin = (($formData['whole_tariff_margin']/100) * $cost[1]);
                                } else {
                                    $pieceMargin = 0;
                                }
                            } else {
                                if(isset($formData['whole_tariff_margin_weight_cost'])) {
                                    $weightMargin = $formData['whole_tariff_margin'];                                                   
                                } else {
                                    $weightMargin = 0;
                                }
                                if(isset($formData['whole_tariff_margin_piece_cost'])) {                                                   
                                    $pieceMargin = $formData['whole_tariff_margin'];
                                } else {
                                    $pieceMargin = 0;
                                }
                            }
                            if(is_numeric($formData['whole_tariff_line_haul'])) {
                                if($formData['whole_tariff_line_haul_type'] == "kilogram") {                                
                                    $toWeight = explode("/", $weight);
                                    $lineHaul = $toWeight[1] * $formData['whole_tariff_line_haul'];
                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                } else {
                                    $toWeight = explode("/", $weight);
                                    $lineHaul = $formData['whole_tariff_line_haul'];
                                    $resultPrice = (($cost[0] + $weightMargin) + $lineHaul) . "/" . ($cost[1] + $pieceMargin);
                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul ,$cost[0] ,$cost[1]);               
                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                }
                            } else {
                                $resultPrice = ($cost[0] + $weightMargin) . "/" . ($cost[1] + $pieceMargin);
                                $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin ,$pieceMargin ,$lineHaul = 0 ,$cost[0] ,$cost[1]);               
                                $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                            }
                        } else {
                            if(is_numeric($formData['whole_tariff_line_haul'])) {
                                if($formData['whole_tariff_line_haul_type'] == "kilogram") {
                                    $toWeight = explode("/", $weight);
                                    $lineHaul = $toWeight[1] * $formData['whole_tariff_line_haul'];
                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                } else {
                                    $toWeight = explode("/", $weight);
                                    $lineHaul = $formData['whole_tariff_line_haul'];
                                    $resultPrice = ($cost[0] + $lineHaul) . "/" . $cost[1];
                                    $result['zone_new['.$weight.']['.$zoneKey.']'] = $resultPrice;
                                    $colors = $this->assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul ,$cost[0] ,$cost[1]);               
                                    $color['zone_new['.$weight.']['.$zoneKey.']'] = $colors;
                                }
                            }
                        }
                    }
                }
            }
            echo json_encode(array('colors' => $color, 'fields' => $result));
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_rules') {
            $formData = '';
            parse_str($this->form_vars['pricing_form'], $formData);
            $output = array();
            $date_added = time();
            $added_by = $this->user->getId();
            $date_update = time();
            $update_by = $this->user->getId();
            $tariffPricingRuleName = $formData['tariff_rules_name_save'];
            $tariffSelectedId = (isset($formData['tariff_selected_id']) ? $formData['tariff_selected_id'] : 0);

            $tariffsPricingRulesObj = new TariffsPricingRules();
            $tariffsPricingRulesObj->setTariffId($tariffSelectedId);
            $tariffsPricingRulesObj->setName($tariffPricingRuleName);
            $tariffsPricingRulesObj->setDateAdded($date_added);
            $tariffsPricingRulesObj->setAddedBy($added_by);
            $tariffsPricingRulesObj->setDateUpdated($date_update);
            $tariffsPricingRulesObj->setUpdatedBy($update_by);
            $tariffsPricingRulesObj->save();
            
            $tariff_pricing_rule_id = $tariffsPricingRulesObj->getId();
            
            if(is_numeric($formData['whole_tariff_margin']) || is_numeric($formData['whole_tariff_line_haul'])) {
                $margin = $formData['whole_tariff_margin'];
                $margin_type = $formData['whole_tariff_margin_type'];
                $margin_weight_cost = $formData['whole_tariff_margin_weight_cost'];
                $margin_piece_cost = $formData['whole_tariff_margin_piece_cost'];
                $linehaul = $formData['whole_tariff_line_haul'];
                $linehaul_type = $formData['whole_tariff_line_haul_type'];
                
                $tariffsPricingRulesDetailsObj = new TariffsPricingRulesDetails();
                $tariffsPricingRulesDetailsObj->setTariffPricingRuleId($tariff_pricing_rule_id);
                if(is_numeric($formData['whole_tariff_margin'])) {
                    $tariffsPricingRulesDetailsObj->setMargin($margin);
                    $tariffsPricingRulesDetailsObj->setMarginType($margin_type);
                    $tariffsPricingRulesDetailsObj->setMarginWeightCost($margin_weight_cost);
                    $tariffsPricingRulesDetailsObj->setMarginPieceCost($margin_piece_cost);
                }
                if(is_numeric($formData['whole_tariff_line_haul'])) {
                    $tariffsPricingRulesDetailsObj->setLinehaul($linehaul);
                    $tariffsPricingRulesDetailsObj->setLinehaulType($linehaul_type);
                }
                $tariffsPricingRulesDetailsObj->setTariffPricingType("whole");
                $tariffsPricingRulesDetailsObj->setDateAdded($date_added);
                $tariffsPricingRulesDetailsObj->setAddedBy($added_by);
                $tariffsPricingRulesDetailsObj->setDateUpdated($date_update);
                $tariffsPricingRulesDetailsObj->setUpdatedBy($update_by);
                $tariffsPricingRulesDetailsObj->save();
            }
            
            foreach($formData['apply'] as $apply) {
                if(is_numeric($apply['to_zone_id']) || (is_numeric($apply['weight_from']) && is_numeric($apply['weight_to']))) {                     
                    $to_zone_id = $apply['to_zone_id'];
                    $weight_from = $apply['weight_from'];
                    $weight_to = $apply['weight_to'];
                    $margin = $apply['margin'];
                    $margin_type = $apply['margin_type'];
                    $margin_weight_cost = $apply['margin_weight_cost'];
                    $margin_piece_cost = $apply['margin_piece_cost'];
                    $linehaul = $apply['line_haul'];
                    $linehaul_type = $apply['line_haul_type'];

                    $tariffsPricingRulesDetailsObj = new TariffsPricingRulesDetails();
                    $tariffsPricingRulesDetailsObj->setTariffPricingRuleId($tariff_pricing_rule_id);
                    $tariffsPricingRulesDetailsObj->setToZoneId($to_zone_id);
                    $tariffsPricingRulesDetailsObj->setWeightFrom($weight_from);
                    $tariffsPricingRulesDetailsObj->setWeightTo($weight_to);
                    $tariffsPricingRulesDetailsObj->setMargin($margin);
                    $tariffsPricingRulesDetailsObj->setMarginType($margin_type);
                    $tariffsPricingRulesDetailsObj->setMarginWeightCost($margin_weight_cost);
                    $tariffsPricingRulesDetailsObj->setMarginPieceCost($margin_piece_cost);
                    $tariffsPricingRulesDetailsObj->setLinehaul($linehaul);
                    $tariffsPricingRulesDetailsObj->setLinehaulType($linehaul_type);
                    $tariffsPricingRulesDetailsObj->setTariffPricingType("multiple");
                    $tariffsPricingRulesDetailsObj->setDateAdded($date_added);
                    $tariffsPricingRulesDetailsObj->setAddedBy($added_by);
                    $tariffsPricingRulesDetailsObj->setDateUpdated($date_update);
                    $tariffsPricingRulesDetailsObj->setUpdatedBy($update_by);
                    $tariffsPricingRulesDetailsObj->save();
                }
            }
            $output['success'] = "Rules is added successfully";
            echo json_encode($output);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_tariff_pricing') {
            $csvString = "Weight/zone,";
            foreach($this->form_vars['zone_new'] as $zones) {
                 foreach($zones as $zoneId => $price) {
                    $toZone = new CarrierZones($zoneId);
                    $csvString .= $toZone->getName() . ",";
                }
                break;
            }        
            $csvString .= "formula,";
            $fileName = "tariff_details_pricing_".$this->form_vars['tariff_selected_id']."_".time();
            foreach($this->form_vars['zone_new'] as $weight => $zones) {
                $csvString .= "\r\n";
                $csvString .= $weight . ",";
                foreach($zones as $price) {                    
                    $csvString .= $price . ",";                    
                }
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $csvString;
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'load_pricing_rules') {
            $tariffId = $this->form_vars['tariffId'];
            $tariffPricingRulesObj = new TariffsPricingRules($this->form_vars['rule_id']);           
            $tariffPricingRulesDetails = new TariffsPricingRulesDetailsFilter();
            $tariffPricingRulesDetails->addFieldFilter("   tprd.tariff_pricing_rule_id", $tariffPricingRulesObj->getId());
            $tariffPricingRulesDetailsObj = $tariffPricingRulesDetails->getList(" tprd.id,tprd.to_zone_id,tprd.weight_from,tprd.weight_to,tprd.margin,tprd.margin_type,tprd.margin_weight_cost,tprd.margin_piece_cost,tprd.linehaul,tprd.linehaul_type,tprd.tariff_pricing_type");
            $sql = "SELECT td.id,td.to_zone_id,cz.name as zone_name FROM tariffs_details td JOIN carrier_zones cz ON td.to_zone_id = cz.id WHERE td.tariffs_id = " . $tariffId . " GROUP BY(cz.id);";
            $zones = TariffsDetails::getTariffsDetailsListFromSql($sql);                      
            $whtml = '';
            $html = '';
            if($this->form_vars['rule_id'] > 0) {                                
                if(!empty($tariffPricingRulesDetailsObj) && count($tariffPricingRulesDetailsObj) > 0) {
                    foreach($tariffPricingRulesDetailsObj as $k => $tprdo) {
                        if($tprdo->getTariffPricingType() == "whole") {
                        $whtml .= '<div class="form-group">';
                            $whtml .= '<div class="col-md-12">';
                                $whtml .= '<div class="caption block">';
                                    $whtml .= '<span class="caption-subject bold uppercase">Whole Tariff Price</span>';
                                $whtml .= '</div>';
                            $whtml .= '</div>';
                            $whtml .= '<div class="col-md-4">';
                                $whtml .= '<label>Add Margin</label>';
                                $whtml .= '<div class="input-group">';
                                    $whtml .= '<input type="text" name="whole_tariff_margin" id="whole_tariff_margin" class="form-control" value="' . $tprdo->getMargin() . '">';
                                    $whtml .= '<div class="input-group-btn">';
                                        $whtml .= '<select class="selectpicker form-control" name="whole_tariff_margin_type" id="whole_tariff_margin_type" >';
                                            if($tprdo->getMarginType() == "percentage") {
                                                $whtml .= '<option selected="selected" value="percentage">%</option>';
                                            } else {
                                                $whtml .= '<option value="percentage">%</option>';                                                       
                                            }
                                            if($tprdo->getMarginType() == "price") {
                                                $whtml .= '<option selected="selected" value="price">Fixed</option>';
                                            } else {
                                                $whtml .= '<option value="price">Fixed</option>';                                                        
                                            }
                                        $whtml .= '</select>';
                                    $whtml .= '</div>';
                                $whtml .= '</div>';
                            $whtml .= '</div>';
                            $whtml .= '<div class="col-md-3">';
                                $whtml .= '<label>Margin on</label>';
                                $whtml .= '<div class="mt-checkbox-inline padding-top-0 padding-bottom-0">';
                                    $whtml .= '<label class="mt-checkbox margin-bottom-0"> KG Cost';
                                        if($tprdo->getMarginWeightCost() == "weight") {
                                            $whtml .= '<input type="checkbox" class="whole-tariff-margin-weight-cost" id="whole_tariff_margin_weight_cost" value="weight" name="whole_tariff_margin_weight_cost" checked="checked" />';
                                        } else {
                                            $whtml .= '<input type="checkbox" class="whole-tariff-margin-weight-cost" id="whole_tariff_margin_weight_cost" value="weight" name="whole_tariff_margin_weight_cost" />';                                              
                                        }
                                        $whtml .= '<span></span>';
                                    $whtml .= '</label>';
                                    $whtml .= '<label class="mt-checkbox margin-bottom-0"> Item Cost';
                                        if($tprdo->getMarginPieceCost() == "piece") {
                                            $whtml .= '<input type="checkbox" class="whole-tariff-margin-piece-cost" id="whole_tariff_margin_piece_cost" value="piece" name="whole_tariff_margin_piece_cost" checked="checked" />';
                                        } else {
                                            $whtml .= '<input type="checkbox" class="whole-tariff-margin-piece-cost" id="whole_tariff_margin_piece_cost" value="piece" name="whole_tariff_margin_piece_cost" />';                                                  
                                        }
                                        $whtml .= '<span></span>';
                                    $whtml .= '</label>';
                                $whtml .= '</div>';
                            $whtml .= '</div>';
                            $whtml .= '<div class="col-md-3">';
                                $whtml .= '<label>Add Linehaul</label>';
                                $whtml .= '<div class="input-group">';
                                    $whtml .= '<input type="text" name="whole_tariff_line_haul" id="whole_tariff_line_haul" class="form-control whole-tariff-line-haul" value="' . $tprdo->getLinehaul() . '">';
                                    $whtml .= '<div class="input-group-btn">';
                                        $whtml .= '<select class="selectpicker form-control" name="whole_tariff_line_haul_type" id="whole_tariff_line_haul_type" >';
                                            if($tprdo->getLinehaulType() == "kilogram") {
                                                $whtml .= '<option selected="selected" value="kilogram">KG</option>';
                                            } else {
                                                $whtml .= '<option value="kilogram">KG</option>';                                                        
                                            }
                                            if($tprdo->getLinehaulType() == "flat") {
                                                $whtml .= '<option selected="selected" value="flat">Flat</option>';
                                            } else {
                                                $whtml .= '<option value="flat">Flat</option>';                                                      
                                            }
                                        $whtml .= '</select>';
                                    $whtml .= '</div>';
                                $whtml .= '</div>';
                            $whtml .= '</div>';
                            $whtml .= '<div class="col-md-2">';
                                $whtml .= '<button class="btn btn-success btn-sm margin-top-20" id="calculate_whole_tariff_price_btn" type="button" >Apply</button>';
                            $whtml .= '</div>';
                        $whtml .= '</div>';
                        }
                    }
                }                
                if(!empty($tariffPricingRulesDetailsObj) && count($tariffPricingRulesDetailsObj) > 0) {
                    foreach($tariffPricingRulesDetailsObj as $k => $tprdo) {
                        if($tprdo->getTariffPricingType() == "multiple") {
                            $html .= '<div class="form-group">';
                                $html .= '<div class="row">';
                                    $html .= '<div class="col-md-2">';
                                        $html .= '<div class="input-group">';
                                            $html .= '<span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>';
                                            $html .= '<span id="zone_to_span">';
                                                $html .= '<select class="form-control select2 to_zone_id zone-to-id" name="apply[' .$k . '][to_zone_id]" id="to_zone_id_' .$k . '">';
                                                    $html .= '<option value="">Select Zone</option>';  
                                                    if (count($zones) > 0) {
                                                        foreach ($zones as $z) {
                                                            if($z->getToZoneId() == $tprdo->getToZoneId()) {
                                                                $html .= '<option selected="selected" value="' . $z->getToZoneId() . '">' . $z->getZoneName() . '</option>';                                                               
                                                            } else {                                                                
                                                                $html .= '<option value="' . $z->getToZoneId() . '">' . $z->getZoneName() . '</option>';
                                                            }
                                                        }
                                                    }
                                                $html .= '</select>';
                                            $html .= '</span>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-2">';
                                        $html .= '<div class="input-group">';
                                            $html .= '<input type="text" class="form-control weight-from" min="0" name="apply[' .$k . '][weight_from]" value="' . $tprdo->getWeightFrom() . '" >';
                                            $html .= '<span class="input-group-addon"> to </span>';
                                            $html .= '<input type="text" class="form-control weight-to" min="0" name="apply[' .$k . '][weight_to]" value="' . $tprdo->getWeightTo() . '" > ';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-2">';
                                        $html .= '<div class="input-group">';
                                            $html .= '<input type="text" class="form-control tariff-margin" name="apply[' .$k . '][margin]" value="' . $tprdo->getMargin() . '">';
                                            $html .= '<div class="input-group-btn">';
                                                $html .= '<select class="selectpicker form-control tariff-margin-type" name="apply[' .$k . '][margin_type]" id="margin_type_' .$k . '">';
                                                    if($tprdo->getMarginType() == "percentage") {
                                                        $html .= '<option selected="selected" value="percentage">%</option>';
                                                    } else {
                                                        $html .= '<option value="percentage">%</option>';                                                
                                                    }
                                                    if($tprdo->getMarginType() == "price") {
                                                        $html .= '<option selected="selected" value="price">Fixed</option>';
                                                    } else {  
                                                        $html .= '<option value="price">Fixed</option>';
                                                    }
                                                $html .= '</select>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-2">';
                                        $html .= '<div class="padding-top-0 padding-bottom-0">';
                                            $html .= '<label class="mt-checkbox margin-bottom-0 margin-right-10"> KG Cost';
                                                if($tprdo->getMarginWeightCost() == "weight") {
                                                    $html .= '<input type="checkbox" class="margin-weight-cost" value="weight" name="apply[' . $k . '][margin_weight_cost]" checked="checked" />';
                                                } else {                                            
                                                    $html .= '<input type="checkbox" class="margin-weight-cost" value="weight" name="apply[' . $k . '][margin_weight_cost]" />';
                                                }
                                                $html .= '<span></span>';
                                            $html .= '</label>';
                                            $html .= '<label class="mt-checkbox margin-bottom-0 margin-right-10"> Item Cost';
                                                if($tprdo->getMarginPieceCost() == "piece") {
                                                    $html .= '<input type="checkbox" class="margin-piece-cost" value="piece" name="apply[' .$k . '][margin_piece_cost]" checked="checked" />';
                                                } else {
                                                    $html .= '<input type="checkbox" class="margin-piece-cost" value="piece" name="apply[' .$k . '][margin_piece_cost]" />';
                                                }
                                                $html .= '<span></span>';
                                            $html .= '</label>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-2">';
                                        $html .= '<div class="input-group">';
                                            $html .= '<input type="text" name="apply[' .$k . '][line_haul]" class="form-control tariff-line-haul" value="' . $tprdo->getLinehaul() . '">';
                                            $html .= '<div class="input-group-btn">';
                                                $html .= '<select class="selectpicker form-control tariff-line-haul-type" name="apply[' .$k . '][line_haul_type]" id="line_haul_type_' .$k . '" >';
                                                    if($tprdo->getLinehaulType() == "kilogram") {
                                                        $html .= '<option selected="selected" value="kilogram">KG</option>';
                                                    } else {                                               
                                                        $html .= '<option value="kilogram">KG</option>';
                                                    }
                                                    if($tprdo->getLinehaulType() == "flat") {
                                                        $html .= '<option selected="selected" value="flat">Flat</option>';
                                                    } else {
                                                        $html .= '<option value="flat">Flat</option>';
                                                    }
                                                $html .= '</select>';
                                            $html .= '</div>';
                                        $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="col-md-2">';
                                        if (count($tariffPricingRulesDetailsObj) == ($k + 1)) {
                                            $html .= '<button type="button" class="btn btn-success add-more-tariff-pricing-keys"><i class="fa fa-plus"></i></button>';
                                            $html .= '<button type="button" class="btn btn-danger remove-tariff-pricing-key"><i class="fa fa-minus"></i></button>';
                                        } else {
                                            $html .= '<button type="button" class="btn btn-danger remove-tariff-pricing-key"><i class="fa fa-minus"></i></button>';
                                        }
                                    $html .= '</div>';
                                $html .= '</div>';                               
                            $html .= '</div>';
                        } 
                    } 
                }                
            }
            
            $output = array('whole' => $whtml, 'multiple' => $html, 'totalRules' => count($tariffPricingRulesDetailsObj));
            echo json_encode($output);
            die;
        }

    }
    
    protected function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
    }
    
    protected function assign_color_to_calculated_new_zone_prices ($weightMargin = 0 ,$pieceMargin = 0 ,$lineHaul = 0 ,$weightCost = 0,$pieceCost = 0) {
        if((($weightCost + $weightMargin) + $lineHaul) < $weightCost) {
            $color_weight = "danger-color";
        } else if((($weightCost + $weightMargin) + $lineHaul) > $weightCost) {
            $color_weight = "success-color";
        } else {
            $color_weight = "warning-color";
        }
        if(($pieceCost + $pieceMargin) < $pieceCost) {
            $color_cost = "danger-color";
        } else if(($pieceCost + $pieceMargin) > $pieceCost) {
            $color_cost = "success-color";
        } else {
            $color_cost = "warning-color";
        }
        if(((($weightCost + $weightMargin) + $lineHaul) <= $weightCost) || (($pieceCost + $pieceMargin) <= $pieceCost)) {
            $border_color = "warning-border";
        } else {
            $border_color = "success-border";
        }
        $color = $border_color."/".$color_weight."/".$color_cost;
        return $color;
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add-more-tariff-pricing-keys', function () {
                    elindex++;
                    var clone = $(this).parent().parent().parent().clone();
                    var zoneTo = $(clone).find('.zone-to-id').attr('name');
                    var zoneToId = $(clone).find('.zone-to-id').attr('id');
                    var weightFrom = $(clone).find('.weight-from').attr('name');
                    var weightFromId = $(clone).find('.weight-from').attr('id');
                    var weightTo = $(clone).find('.weight-to').attr('name');
                    var weightToId = $(clone).find('.weight-to').attr('id');
                    var tariffMargin = $(clone).find('.tariff-margin').attr('name');
                    var tariffMarginId = $(clone).find('.tariff-margin').attr('id');
                    var tariffMarginType = $(clone).find('.tariff-margin-type .selectpicker').attr('name');
                    var tariffMarginTypeId = $(clone).find('.tariff-margin-type .selectpicker').attr('id');
                    var tariffLineHaul = $(clone).find('.tariff-line-haul').attr('name');
                    var tariffLineHaulId = $(clone).find('.tariff-line-haul').attr('id');
                    var tariffLineHaulType = $(clone).find('.tariff-line-haul-type .selectpicker').attr('name');
                    var tariffLineHaulTypeId = $(clone).find('.tariff-line-haul-type .selectpicker').attr('id');
                    var marginWeightCost = $(clone).find('.margin-weight-cost').attr('name');
                    var marginWeightCostId = $(clone).find('.margin-weight-cost').attr('id');
                    var marginPieceCost = $(clone).find('.margin-piece-cost').attr('name');
                    var marginPieceCostId = $(clone).find('.margin-piece-cost').attr('id');

                    $(this).remove();
                    $(clone).find('.select2-container').remove();

                    $(clone).find('input').val('');
                    $(clone).find('.zone-to-id').attr('name', zoneTo.replace(/\d+/, elindex));
                    $(clone).find('.zone-to-id').attr('id', zoneToId.replace(/\d+/, elindex));
                    $(clone).find('.weight-from').attr('name', weightFrom.replace(/\d+/, elindex));
                    $(clone).find('.weight-from').attr('id', weightFromId.replace(/\d+/, elindex));
                    $(clone).find('.weight-to').attr('name', weightTo.replace(/\d+/, elindex));
                    $(clone).find('.weight-to').attr('id', weightToId.replace(/\d+/, elindex));
                    $(clone).find('.tariff-margin').attr('name', tariffMargin.replace(/\d+/, elindex));
                    $(clone).find('.tariff-margin').attr('id', tariffMarginId.replace(/\d+/, elindex));
                    $(clone).find('.tariff-margin-type .selectpicker').attr('name', tariffMarginType.replace(/\d+/, elindex));
                    $(clone).find('.tariff-margin-type .selectpicker').attr('id', tariffMarginTypeId.replace(/\d+/, elindex));
                    $(clone).find('.tariff-line-haul').attr('name', tariffLineHaul.replace(/\d+/, elindex));
                    $(clone).find('.tariff-line-haul').attr('id', tariffLineHaulId.replace(/\d+/, elindex));
                    $(clone).find('.tariff-line-haul-type .selectpicker').attr('name', tariffLineHaulType.replace(/\d+/, elindex));
                    $(clone).find('.tariff-line-haul-type .selectpicker').attr('id', tariffLineHaulTypeId.replace(/\d+/, elindex));
                    $(clone).find('.margin-weight-cost').attr('name', marginWeightCost.replace(/\d+/, elindex));
                    $(clone).find('.margin-weight-cost').attr('id', marginWeightCostId.replace(/\d+/, elindex));
                    $(clone).find('.margin-piece-cost').attr('name', marginPieceCost.replace(/\d+/, elindex));
                    $(clone).find('.margin-piece-cost').attr('id', marginPieceCostId.replace(/\d+/, elindex));
                    $(clone).find('.margin-weight-cost').attr('value', "weight");
                    $(clone).find('.margin-piece-cost').attr('value', "piece");
                    $(clone).find('.bootstrap-select.tariff-margin-type').replaceWith(function () {
                        return $('#margin_type_' + elindex, this);
                    });
                    $(clone).find('#margin_type_' + elindex).selectpicker('refresh');
                    $(clone).find('.bootstrap-select.tariff-line-haul-type').replaceWith(function () {
                        return $('#line_haul_type_' + elindex, this);
                    });
                    $(clone).find('#line_haul_type_' + elindex).selectpicker('refresh');
                    $(clone).find('button.remove-tariff-pricing-key').show();
                    $(clone).find('button.remove-tariff-pricing-key').removeClass('initial-button');
                    $(clone).find('.zone-to-id').select2();
                    $(clone).appendTo($('.tariffs-pricing-container'));
                });
                $(document).on('click', '.remove-tariff-pricing-key', function () {
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
                                    if ($('.tariffs-pricing-container .remove-tariff-pricing-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        if ($('.add-more-tariff-pricing-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-tariff-pricing-keys').clone();
                                            $('.tariffs-pricing-container .remove-tariff-pricing-key').last().parent().prepend(addMore);
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                });
                $('#tariff_rule_id').change(function () {
                    var ruleId = $(this).val();
                    var tariff_id =  $('#tariff_selected_id').val();
                    var form_data = new FormData();
                    form_data.append('rule_id', ruleId);
                    form_data.append('tariffId', tariff_id);
                    form_data.append('action', 'load_pricing_rules');
                    $.ajax({
                        url: "tariffs_pricing.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            var whole = response.whole;
                            var multiple = response.multiple;
                            $('#elindex-hardcode').val(response.totalRules);
                            elindex = response.totalRules;
                            if(whole.length > 0){
                                $('#tariff_single_pricing').html(whole);
                            }
                            if(multiple.length > 0){
                                $('#tariff_detail_pricing').html(multiple);
                            }
                            $('.zone-to-id').select2();
                            $('.selectpicker').selectpicker('refresh');
                            <?php if ((!empty($_GET['id']) && is_numeric($_GET['id'])) || (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id']))) { ?>
                            $('#tariff_selected_id').trigger('change');
                            <?php } ?>
                        }
                    });
                });
                $('#tariff_selected_id').change(function () {
                    var tariffId = $(this).val();
                    var ruleId = $('#tariff_rule_id').val();
                    var form_data = new FormData();
                    form_data.append('tariff_id', tariffId);
                    form_data.append('action', 'load_tariff_details');
                    $.ajax({
                        url: "tariffs_pricing.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            if(!ruleId.length){
                                $('.zone-to-id').html(response.zones);                             
                                $('.zone-to-id').select2(); 
                            }
                            $('#tariff_detals').html(response.tariff_detail);
                            $('#carrier_id').val(response.carrierId);
                            $('#service_id').val(response.serviceId);
                            $('#tariff_name').val(response.name);
                            $('#currency_id_show').val(response.currencyId);
                            $('#currency_id_show').select2();
                            $('#currency_id').val(response.currencyId);
                            $('#currency_id').select2();
                            $('#start_date').val(response.startDate);
                            $('#end_date').val(response.endDate);
                            if(response.status == 1) {
                                $('#tariff_status').attr('checked', true);
                                $('#tariff_status').bootstrapSwitch('state', true);
                            }
                            $('#description').val(response.description);     
                            <?php if (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id'])) { ?>
                            $('#calculate_whole_tariff_price_btn').trigger('click');
                            <?php } ?>
                        }
                    });                    
                });
                $(document).on('click', '#calculate_price', function () {
                    $('#calculate_whole_tariff_price_btn').trigger('click');
                });
                $(document).on('click', '#calculate_whole_tariff_price_btn', function () {
                    $('.zone_weight').each(function(){
                        var weight = $(this).val();
                        var weightArr = weight.split('/');
                        var fromWeight = weightArr[0];
                        var toWeight = weightArr[1];
                        var weightStr = weight.replace(/[\/.]/g, "_");
                        $('.zone_price_'+weightStr).each(function(){
                            var zoneId = $(this).data('zone_id');
                            var value = $(this).val().split('/');
                            var kgCost = value[0];
                            var itemCost = value[1];
                            calculateNewCost(kgCost,itemCost,fromWeight,toWeight,zoneId,weightStr);
                        });
                    });
                });
//                $(document).on('click', '#calculate_whole_tariff_price_btn', function () {
//                    var form_data = new FormData();
//                    form_data.append('pricing_form', $("#tarif_pricing_form").serialize());
//                    form_data.append('action', 'calculate_whole_tariff_price');
//                    $.ajax({
//                        url: "tariffs_pricing.php",
//                        cache: false,
//                        contentType: false,
//                        processData: false,
//                        data: form_data,
//                        type: 'post',
//                        dataType: 'json',
//                        success: function (response) {                           
//                            jQuery.each(response.fields, function(i, value) {
//                                var check = $("input[name='" + i + "']").val();
////                                if(check.length == 0) {
//                                    $("input[name='" + i + "']").val(value);
//                                    var new_zone_id = i.replace(/[\[\]\/\.]/g, '_');
//                                    new_zone_id = new_zone_id.replace(/\_+/g, '_');
//                                    new_zone_id = new_zone_id.replace(/\_$/, "");
//                                    var a = value.split('/');
//                                    var html = "<span>" + parseFloat(Math.round(a[0] * 100) / 100).toFixed(2) + "</span>";
//                                    if(a[1].length != 0) {
//                                        html += "<span>/</span>";
//                                        html += "<span>" + parseFloat(Math.round(a[1] * 100) / 100).toFixed(2) + "</span>";
//                                    }                                
//                                    $("#"+new_zone_id).html(html);
//                                    var colors = response.colors[i].split('/');
//                                    var border_color = colors[0];
//                                    var weight_cost_color = colors[1];
//                                    var piece_cost_color = colors[2];
//                                    $("#"+new_zone_id).attr("class","");
//                                    $("#"+new_zone_id).addClass("form-control new-zone-font  "+border_color);
//                                    $("#"+new_zone_id+" span").each(function(i) {
//                                        if ( i === 0) {
//                                            $(this).attr("class","");
//                                            $(this).addClass(weight_cost_color);
//                                        }
//                                        if ( i === 2) {
//                                            $(this).attr("class","");
//                                            $(this).addClass(piece_cost_color);
//                                        }
//                                    });
////                                }                               
//                            });
//                            $('#calculate_price').trigger('click'); 
//                            <?php //if (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id'])) { ?>
//                            $('#calculate_price').trigger('click');                            
//                            <?php //} ?>
//                        }
//                    });
//                });
//                $(document).on('click', '#calculate_price', function () {
//                    var form_data = new FormData();
//                    form_data.append('pricing_form', $("#tarif_pricing_form").serialize());
//                    form_data.append('action', 'calculate_multiple_price');
//                    $.ajax({
//                        url: "tariffs_pricing.php",
//                        cache: false,
//                        contentType: false,
//                        processData: false,
//                        data: form_data,
//                        type: 'post',
//                        dataType: 'json',
//                        success: function (response) {
//                            jQuery.each(response.fields, function(i, value) {                                
//                                $("input[name='" + i + "']").val(value);
//                                var new_zone_id = i.replace(/[\[\]\/\.]/g, '_');
//                                new_zone_id = new_zone_id.replace(/\_+/g, '_');
//                                new_zone_id = new_zone_id.replace(/\_$/, "");
//                                var a = value.split('/');
//                                var html = "<span>" + parseFloat(Math.round(a[0] * 100) / 100).toFixed(2) + "</span>";
//                                if(a[1].length != 0) {
//                                    html += "<span>/</span>";
//                                    html += "<span>" + parseFloat(Math.round(a[1] * 100) / 100).toFixed(2) + "</span>";
//                                }                                
//                                $("#"+new_zone_id).html(html);
//                                var colors = response.colors[i].split('/');
//                                var border_color = colors[0];
//                                var weight_cost_color = colors[1];
//                                var piece_cost_color = colors[2];
//                                $("#"+new_zone_id).attr("class","");
//                                $("#"+new_zone_id).addClass("form-control new-zone-font  "+border_color);
//                                $("#"+new_zone_id+" span").each(function(i) {
//                                    if ( i === 0) {
//                                        $(this).attr("class","");
//                                        $(this).addClass(weight_cost_color);
//                                    }
//                                    if ( i === 2) {
//                                        $(this).attr("class","");
//                                        $(this).addClass(piece_cost_color);
//                                    }
//                                });
//                            });
//                        }
//                    });
//                });                                
                <?php if ((!empty($_GET['id']) && is_numeric($_GET['id'])) || (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id']))) { ?>
                $('#tariff_rule_id').trigger('change');
                <?php } ?>
            });
            $(document).on('click', '#save_rules', function () {
                $('#pricing_form_action').val("save_rules");
                $('#rules_modal').modal("show");
            });
            $('#btnRulesSave').click(function () {
                var validation = 1;
                $('.validate_check_rule_2').filter(function () {
                    if (!$(this).val()) {
                        $(this).parents(".input-group").css('border', '1px solid red');
                        validation = 0;
                    } else {
                        $(this).parents(".input-group").css('border', '0px');
                    }
                    return !$(this).val();
                });
                if (validation > 0) {
                    var form_data = new FormData();
                    form_data.append('pricing_form', $("#tarif_pricing_form").serialize());
                    form_data.append('action', 'save_rules');
                    $.ajax({
                        url: "tariffs_pricing.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {                           
                            if(response.success) {
                                swal("Success!", response.success, "success");
                            } else {
                                swal("Sorry!", "Some thing went wrong", "error");
                            }
                        }
                    });
                } else {
                    swal("Sorry!", "Error is high lighted with red border", "error");
                }               
            });
            $(document).on('click', '#btnSave', function () {
                var validation = 1;
                $('.validate_check').filter(function () {
                    if (!$(this).val()) {
                        $(this).parents(".input-group").css('border', '1px solid red');
                        validation = 0;
                    } else {
                        $(this).parents(".input-group").css('border', '0px');
                    }
                    return !$(this).val();
                });
                if (validation > 0) {
                    $('#pricing_form_action').val("save_calculated_tariff");
                    $('#tariff_modal').modal("show");
                } else {
                    swal("Sorry!", "Error is high lighted with red border", "error");
                }
            });
            $(document).on('click', '#tariff_save_btn', function () {
                var validation = 1;
                $('.validate_check_tariff').filter(function () {
                    if (!$(this).val()) {
                        $(this).parents(".input-group").css('border', '1px solid red');
                        validation = 0;
                    } else {
                        $(this).parents(".input-group").css('border', '0px');
                    }
                    return !$(this).val();
                });
                if (validation > 0) {
                    $('#pricing_form_action').val("save_calculated_tariff");
                    $('#tariff_modal').modal("hide");
                    $('#tarif_pricing_form').submit();
                } else {
                    swal("Sorry!", "Error is high lighted with red border", "error");
                }
            });
            $(document).on('click', '#csv_download_btn', function () {
                $('#pricing_form_action').val("download_tariff_pricing");
                $('#tarif_pricing_form').submit();
            });

            function calculateNewCost(kgCost,itemCost,fromWeight,toWeight,zoneId,weightStr) {
                var wholeMargin = $('#whole_tariff_margin').val();
                var wholeMarginType = $('#whole_tariff_margin_type').val();
                var wholeMarginKgCost = 0;
                if($('#whole_tariff_margin_weight_cost').is(":checked")) {
                    wholeMarginKgCost = 1;
                }
                var wholeMarginPieceCost = 0;
                if($('#whole_tariff_margin_piece_cost').is(":checked")) {
                    wholeMarginPieceCost = 1;
                }
                var wholeLineHaul = $('#whole_tariff_line_haul').val();
                var wholeLineHaulType = $('#whole_tariff_line_haul_type').val();
                var kgCostNew = parseFloat(kgCost);
                var pieceCostNew = parseFloat(itemCost);
                if(wholeMargin != "") {
                    if(wholeMarginType == "percentage") {
                        if(wholeMarginKgCost) {
                            var applyValue = (parseFloat(wholeMargin)/100)*parseFloat(kgCost);
                            kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
                        }
                        if(wholeMarginPieceCost) {
                            var applyValue = (parseFloat(wholeMargin)/100)*parseFloat(itemCost);
                            pieceCostNew = (parseFloat(pieceCostNew) + parseFloat(applyValue));
                        }
                    } else {
                        var applyValue = wholeMargin;
                        if(wholeMarginKgCost) {
                            kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
                        }
                        if(wholeMarginPieceCost) {
                            pieceCostNew = (parseFloat(pieceCostNew) + parseFloat(applyValue));
                        }
                    }
                }
                if(wholeLineHaul != "") {
                    if(wholeLineHaulType == "kilogram") {
                        var calLineHaul = parseFloat(wholeLineHaul) * parseFloat(toWeight);
                        kgCostNew = parseFloat(kgCostNew) + parseFloat(calLineHaul);
                    } else {
                        kgCostNew = parseFloat(kgCostNew) + parseFloat(wholeLineHaul);
                    }
                }
                $('.to_zone_id').each(function(key,obj) {
                    var toZoneId = obj.value;
                    if(zoneId == toZoneId) {
                        var fromWeightEnter = parseFloat($("#weight_from_"+key).val());
                        var toWeightEnter = parseFloat($("#weight_to_"+key).val());
                        var margin = $.trim($("#margin_"+key).val());
                        if(margin != "") {
                            margin = parseFloat(margin);
                        }
                        var margin_type = $("#margin_type_"+key).val();
                        var marginWeightCost = 0;
                        if($("#margin_weight_cost_"+key).is(":checked")) {
                            marginWeightCost = 1;
                        }
                        var marginPieceCost = 0;
                        if($("#margin_piece_cost_"+key).is(":checked")) {
                            marginPieceCost = 1;
                        }
                        var lineHaul = $.trim($("#line_haul_"+key).val());
                        if(lineHaul != "") {

                            lineHaul = parseFloat(lineHaul);
                        }
                        var lineHaulType = $("#line_haul_type_"+key).val();
                        
                        fromWeightEnter = fromWeightEnter.toFixed(2);
                        toWeightEnter = toWeightEnter.toFixed(2);

                        if((parseFloat(fromWeight) >= parseFloat(fromWeightEnter)) && (parseFloat(toWeight) <= parseFloat(toWeightEnter))) {
                            kgCostNew = parseFloat(kgCost);
                            pieceCostNew = parseFloat(itemCost);
                            if(margin != "") {
                                if(margin_type == "percentage") {
                                    if(marginWeightCost) {
                                        var applyValue = parseFloat((parseFloat(margin)/100)*parseFloat(kgCost));
                                        kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
                                    }
                                    if(marginPieceCost) {
                                        var applyValue = parseFloat((parseFloat(margin)/100)*parseFloat(itemCost));
                                        pieceCostNew = (parseFloat(pieceCostNew) + parseFloat(applyValue));
                                    }
                                } else {
                                    var applyValue = parseFloat(margin);
                                    if(marginWeightCost) {
                                        kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
                                    }
                                    if(marginPieceCost) {
                                        pieceCostNew = (parseFloat(pieceCostNew) + parseFloat(applyValue));
                                    }
                                }
                            }

                            if(lineHaul != "") {
                                if(lineHaulType == "kilogram") {
                                    var calLineHaul = parseFloat(lineHaul) * parseFloat(toWeight);
                                    kgCostNew = parseFloat(kgCostNew) + parseFloat(calLineHaul);
                                } else {
                                    kgCostNew = parseFloat(kgCostNew) + parseFloat(lineHaul);
                                }
                            }
                        }
                    }
                });

                kgCostNew = kgCostNew.toFixed(2);
                pieceCostNew = pieceCostNew.toFixed(2);

                var kgColor = "success-color";
                var pieceColor = "success-color";
                var boder_color = "success-border";
                if(parseFloat(kgCostNew) < parseFloat(kgCost)) {
                    kgColor = "danger-color";
                    boder_color = "warning-border";
                } else if(parseFloat(kgCostNew) == parseFloat(kgCost)) {
                    kgColor = "warning-color";
                    boder_color = "warning-border";
                }
                if(parseFloat(pieceCostNew) < parseFloat(itemCost)) {
                    pieceColor = "danger-color";
                    boder_color = "warning-border";
                } else if(parseFloat(pieceCostNew) == parseFloat(itemCost)) {
                    pieceColor = "warning-color";
                    boder_color = "warning-border";
                }
                var input_value = kgCostNew + "/" + pieceCostNew;
                var html = '<span class="'+kgColor+'">'+kgCostNew+'</span><span>/</span><span class="'+pieceColor+'">'+pieceCostNew+'</span>';
                $('#zone_new_input_'+weightStr+'_'+zoneId).val(input_value);
                $('#zone_new_'+weightStr+'_'+zoneId).html(html);
                $('#zone_new_'+weightStr+'_'+zoneId).removeAttr('class');
                $('#zone_new_'+weightStr+'_'+zoneId).addClass("form-control " + boder_color);
            }
        </script>
        <?php
    }

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Bulk Tracking Upload
                </div>                
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row display-none" id="res_message">
                    <div class="col-md-12">
                        <div class="alert alert-success"></div>
                    </div>
                </div>
                <form name="bulk_tracking_form" id="bulk_tracking_form" action="" method="post">
                    <div class="row margin-bottom-10">
                        <div class="col-md-12">
                            <div class="caption margin-bottom-10 block">
                                <span class="caption-subject bold uppercase">Bulk Tracking Upload</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Master Number</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Bag Number</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Tracking Number</label>                                  
                                </div>
                                <div class="col-md-2">
                                    <label>Status</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Description</label>
                                </div> 
                                <div class="col-md-1">
                                    <label>Date</label>
                                </div>
                            </div>
                            <div class="tariffs-pricing-container" id="bulk_tracking">
                                <div class="form-group">
                                    <div class="row">                                        
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                                                <input type="text" class="form-control" min="0" name="bulk[0][mawb]" id="mawb_0" value="" >                                                                                    
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                                                <textarea class="form-control tariff-margin" name="bulk[0][bagNumbers]" id="bagNumbers_0" ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                                                <textarea class="form-control tariff-margin" name="bulk[0][trackingNumbers]" id="trackingNumbers_0" ></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <span id="zone_to_span">
                                                    <select class="form-control select2" name="bulk[0][status]" id="status_0">
                                                        <option value="">Status</option>
                                                        <option value=""></option>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                                                <input type="text" class="form-control" min="0" name="bulk[0][description]" id="description_0" value="" >                                                                                    
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group date-picker input-daterange">
                                                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                                                <input type="text" class="form-control" min="0" name="bulk[0][date]" id="date_0" value="" >                                                                                    
                                            </div>
                                        </div>
                                        <div class="col-md-1">                                            
                                            <button type="button" class="btn btn-success add-more-tariff-pricing-keys"><i class="fa fa-plus"></i></button>
                                            <button type="button" class="btn btn-danger remove-tariff-pricing-key initial-button"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>     
                    
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save">Save</button>
                            <a href="ops_bulk_tracking.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>                    
                </form>
                <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="<?php echo count($this->tariffPricingRulesDetailsObj); ?>" />          
            </div>
        </div>
        <?php
    }

    protected function renderHead() {
        ?>
        <style>
            .input-group-btn:last-child>.btn, .input-group-btn:last-child>.btn-group {
                z-index: auto;
            }
            #tariff_detals .col-md-6{
                padding: 5px;
            }
            #tariff_detals td{
                padding: 0px 15px;
            }
            .success-color { 
                color: #5cb85c;
            }
            .danger-color { 
                color: #d9534f !important;
            }
            .warning-color { 
                color: #F89406 !important; 
            }
            .success-border { 
                border-color: #5cb85c;
            }
            .warning-border {
                border-color: #F89406;
            }
            .new-zone-font {
                font-size: 16px;
                font-weight: bold;
            }
            select[readonly].select2 + .select2-container {
                pointer-events: none;
                touch-action: none;

                .select2-selection {
                  background-color: #eef1f5;
                  box-shadow: none;
                }

                .select2-selection__arrow,
                .select2-selection__clear {
                  display: none;
                }
             }
            table.fixed { table-layout:fixed; }
            table.fixed td { overflow: hidden; }
            .custom_table thead th { width: 210px;}
        </style>
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