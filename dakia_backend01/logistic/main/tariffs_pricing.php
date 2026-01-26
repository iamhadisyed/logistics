<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'PHPExcel'
], '3rdparty/phpexcel');

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
    'tariffsfilter.class',
    'tariffspricingdrafts.class',
    'tariffspricingdraftsfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class'
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
    private $tariffPricingDraftDetailsObj = array();

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
            $tariffs_formula = $this->form_vars['tariffs_formula'];
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

           $tariff_id = $this->form_vars['tariff_selected_id'];
            
            $tariffsPricingRulesObj = new TariffsPricingRules();
            $tariffsPricingRulesObj->setTariffId($tariff_id);
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
            $userId = $this->user->getId();
            TariffsPricingDrafts::deleteTariffsPricingDraftsByTariffId($tariff_id,$userId);
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
                    $tariffsDetailObj->setFormula($tariffs_formula);
                    $tariffsDetailObj->save();
                }
            }           
            
            $this->msg = 'Tariff has been added successfully.';
            $this->flashMsg->success($this->msg);
            /* End Trarif Details Pricing */
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_darft_tariff') {
            $userId = $this->user->getId();
            $date_added = time();
            $added_by = $userId;
            $date_update = time();
            $update_by = $userId;
            $zonesValues = $this->form_vars['zones_value'];
            $zonesMargins = $this->form_vars['zones_margin'];
            $tariffId = $this->form_vars['tariff_id'];
            $wholeTariffMarginType = $this->form_vars['whole_tariff_margin_type'];
            if(count($zonesValues)) {
                TariffsPricingDrafts::deleteTariffsPricingDraftsByTariffId($tariffId,$userId);
                foreach($zonesValues as $weight => $arr) {
                    foreach($arr as $zoneId => $value) {
                        $currentZoneValue = "";
                        $currentZoneMargin = "";

                        $currentZoneValue = $value;
                        $currentZoneMargin = ($zonesMargins[$weight][$zoneId] != "") ? $zonesMargins[$weight][$zoneId] : "";
                        if($currentZoneMargin != "" || $currentZoneValue != "") {
                            $weightArr = explode('/', $weight);
                            $weightFrom = $weightArr[0];
                            $weightTo = $weightArr[1];
                            $tariffsPricingDrafts = new TariffsPricingDrafts();
                            $tariffsPricingDrafts->setUserId($userId);
                            $tariffsPricingDrafts->setTariffId($tariffId);
                            $tariffsPricingDrafts->setZoneId($zoneId);
                            $tariffsPricingDrafts->setWeightFrom($weightFrom);
                            $tariffsPricingDrafts->setWeightTo($weightTo);
                            $tariffsPricingDrafts->setValue($currentZoneValue);
                            $tariffsPricingDrafts->setMargin($currentZoneMargin);
                            $tariffsPricingDrafts->setMarginType($wholeTariffMarginType);
                            $tariffsPricingDrafts->setDateAdded($date_added);
                            $tariffsPricingDrafts->setAddedBy($added_by);
                            $tariffsPricingDrafts->setDateUpdated($date_update);
                            $tariffsPricingDrafts->setUpdatedBy($update_by);
                            $tariffsPricingDrafts->save();
                        }
                    }
                }
            }
            $return = [
                'status' => 'success',
                'message' => 'Tariff pricing draft save successfully'
            ];
            echo json_encode($return);
            die;
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
            $tariffPricingDraftsFilter = new TariffsPricingDraftsFilter();
            $tariffPricingDraftsFilter->where('user_id',$this->user->getId());
            $tariffPricingDraftsFilter->where('tariff_id',$tariffId);
            $tariffPricingDraftDetailsObjs = $tariffPricingDraftsFilter->getList();
            $tariffPricingDraftDetails = [];
            if(count($tariffPricingDraftDetailsObjs)) {
                foreach($tariffPricingDraftDetailsObjs as $tariffPricingDraftDetailsObj) {
                    $weight_from = $tariffPricingDraftDetailsObj->getWeightFrom();
                    $weight_to = $tariffPricingDraftDetailsObj->getWeightTo();
                    $weight = $weight_from . "/" . $weight_to;
                    $zone_id = $tariffPricingDraftDetailsObj->getZoneId();
                    $value = $tariffPricingDraftDetailsObj->getValue();
                    $margin = $tariffPricingDraftDetailsObj->getMargin();
                    $marginType = $tariffPricingDraftDetailsObj->getMarginType();
                    $tariffPricingDraftDetails[$weight][$zone_id] = [
                            'value' => $value,
                            'margin' => $margin,
                            'margin_type' => $marginType,
                    ];
                }
            }

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
            $tariffsFilter = new TariffsFilter();
            $tariffsFilter->addCustomJoin('      join services s on s.id=t.service_id');
            $tariffsFilter->addCustomJoin('      join carrier c ON c.id = s.carrier_id');
            $tariffsFilter->addFieldFilter('     t.id',$tariffId);
            $tariffsFilterObjs = $tariffsFilter->getList('t.*,s.is_customized,s.name as service_name,s.code as service_code,c.zone_base');
            $serviceId = "";
            $serviceName = "";
            $serviceCode = "";
            $carrierId = "";
            $zoneBase = "";
            $isCustomized = "";
            $customizeServiceArr = [];
            if(count($tariffsFilterObjs)) {
                $tariffsFilterObj = $tariffsFilterObjs[0];
                $serviceId = $tariffsFilterObj->getServiceId();
                $carrierId = $tariffsFilterObj->getCarrierId();
                $serviceName = $tariffsFilterObj->getServiceName();
                $serviceCode = $tariffsFilterObj->getServiceCode();
                $zoneBase = $tariffsFilterObj->getZoneBase();
                $isCustomized = $tariffsFilterObj->getIsCustomized();
                if($isCustomized) {
                    $where = "cz.`service_id` = '".DbAccess3::escape($serviceId)."'";
                    if($zoneBase) {
                        $where = "cz.`carrier_id` = '".DbAccess3::escape($carrierId)."'";
                    }
                    $sql = "SELECT 
                              csr.`from_weight`,
                              csr.`to_weight`,
                              s.`name` as service_name,
                              cz.`id` 
                            FROM
                              `customized_services_routing` csr 
                              JOIN services s 
                                ON s.id = csr.service_id 
                              JOIN `carrier_zones_countries` czc 
                                ON czc.`country_id` = csr.`country_id` 
                              JOIN `carrier_zones` cz 
                                ON cz.`id` = czc.`carrier_zone_id` AND $where
                            WHERE csr.`customize_service_id` = '".DbAccess3::escape($serviceId)."'";
                    $CustomizedServicesRoutingObjs = CustomizedServicesRouting::getCustomizedServicesListFromSql($sql);
                    if(count($CustomizedServicesRoutingObjs)) {
                        foreach($CustomizedServicesRoutingObjs as $CustomizedServicesRoutingObj) {
                            $_fromWeight = $CustomizedServicesRoutingObj->getFromWeight();
                            $_toWeight = $CustomizedServicesRoutingObj->getToWeight();
                            $_serviceName = $CustomizedServicesRoutingObj->getServiceName();
                            $_zoneId = $CustomizedServicesRoutingObj->getId();
                            $_weight = $_fromWeight . '/' . $_toWeight;
                            $customizeServiceArr[$_zoneId][$_weight] = $_serviceName;
                        }
                    }
                }
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
                    $cost = 0.00;
                    if($tariffsDetailsObj->getPieceCost() > 0) {
                        $cost = $tariffsDetailsObj->getWeightCost() . "/" . $tariffsDetailsObj->getPieceCost();
                    } else {
                        $cost = $tariffsDetailsObj->getWeightCost();
                    }
                    $tariff_details[$zone_id][$weight] = [
                        'weight_cost' => (($tariffsDetailsObj->getWeightCost() > 0) ? $tariffsDetailsObj->getWeightCost() : 0.00),
                        'peice_cost' => (($tariffsDetailsObj->getPieceCost() > 0) ? $tariffsDetailsObj->getPieceCost() : 0.00),
                        'cost' => $cost
                    ];
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
                                $tdhtml .= '<th class="bg-blue" style="color: #fff;"><b>Weight/Zone</b></th>';
                                foreach ($zones as $k => $zone) {                                                   
                                    $toZone = new CarrierZones($zone);
                                    $tdhtml .= '<th class="bg-blue"><b style="color: #fff;">' . $toZone->getName() . '</b><input type="hidden" name="zonename[]" value="' . $toZone->getName() . '"></th>';
                                }
                            $tdhtml .= '</tr>';
                        $tdhtml .= '</thead>';
                        $tdhtml .= '<tbody>';
                            $key = 0;
                            foreach ($tariffDetailData as $weight => $detail) {
                                if($key == 0) {
                                    $tdhtml .= '<tr>';
                                        $tdhtml .= '<td class="bg-info">';
                                            $tdhtml .= '<table class="inner_table_heading">';
                                                $tdhtml .= '<tr>';
                                                    $tdhtml .= '<td>From</td>';
                                                    $tdhtml .= '<td>To</td>';
                                                $tdhtml .= '</tr>';
                                            $tdhtml .= '</table>';
                                        $tdhtml .= '</td>';  
                                    foreach ($detail as $k => $dt) {
                                        $tdhtml .= '<td>';
                                            $tdhtml .= '<table class="inner_table_heading">';
                                                $tdhtml .= '<tr>';
                                                    $tdhtml .= '<td style="width:30%;">Service</td>';
                                                    $tdhtml .= '<td>KG</td>';
                                                    if($dt['peice_cost'] > 0) {
                                                        $tdhtml .= '<td>Item</td>';
                                                    }
                                                    $tdhtml .= '<td>Value</td>';
                                                    $tdhtml .= '<td>Margin</td>';
                                                    $tdhtml .= '<td>KG / Item</td>';
                                                $tdhtml .= '</tr>';
                                            $tdhtml .= '</table>';
                                        $tdhtml .= '</td>'; 
                                    }        
                                    $tdhtml .= '</tr>';
                                }
                                $tdhtml .= '<tr>';                           
                                    $tdhtml .= '<td class="bg-info">';
                                        $weightsArr = explode('/', $weight);
                                        $tdhtml .= '<table class="inner_table">';
                                            $tdhtml .= '<tr>';
                                                $tdhtml .= '<input type="hidden" name="weight[]" id="" class="form-control zone_weight" value="' . $weight . '" readonly="readonly">';
                                                $tdhtml .= '<td>';
                                                    $tdhtml .= '<input type="text" name="weight_from_show[]" id="" class="custom_field_show zone_from_weight_show" value="' . (isset($weightsArr[0]) ? $weightsArr[0] : '') . '" readonly="readonly">';
                                                $tdhtml .= '</td>';
                                                $tdhtml .= '<td>';
                                                    $tdhtml .= '<input type="text" name="weight_to_show[]" id="" class="custom_field_show zone_to_weight_show" value="' . (isset($weightsArr[1]) ? $weightsArr[1] : '')  . '" readonly="readonly">';
                                                $tdhtml .= '</td>';
                                            $tdhtml .= '</tr>';
                                        $tdhtml .= '</table>';
                                    $tdhtml .= '</td>';
                                    foreach ($detail as $k => $dt) {
                                        $tdhtml .= '<td>';
                                            $idValue = str_replace(".","_",$weight);
                                            $tdhtml .= '<table class="inner_table">';
                                                $tdhtml .= '<tr>';
                                                    $tdhtml .= '<td style="width:30%;">';
                                                    if($isCustomized) {
                                                        $tdhtml .= isset($customizeServiceArr[$k][$weight]) ? ucwords(strtolower($customizeServiceArr[$k][$weight])) : '&nbsp;';
                                                    } else {
                                                        $tdhtml .= ucwords(strtolower($serviceName));
                                                    }
                                                    $tdhtml .= '</td>';
                                                    $tdhtml .= '<td>';
                                                        $tdhtml .= '<input type="hidden" name="zones[' . $weight . '][' . $k . ']" id="zone_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="form-control zone_price_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" value="' . $dt['cost'] . '" readonly="readonly">';
                                                        $tdhtml .= '<input type="text" name="zones_weight_cost[' . $weight . '][' . $k . ']" id="zone_weight_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show zone_weight_price_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" value="' . $dt['weight_cost'] . '" readonly="readonly">';
                                                    $tdhtml .= '</td>';
                                                    if($dt['peice_cost'] > 0) {
                                                        $tdhtml .= '<td>';
                                                        $tdhtml .= '<input type="text" name="zones_piece_cost[' . $weight . '][' . $k . ']" id="zone_piece_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="custom_field_show zone_piece_price_' . str_replace("/", "_", $idValue) . '" data-zone_id="' . $k . '" value="' . $dt['peice_cost'] . '" readonly="readonly">';
                                                        $tdhtml .= '</td>';
                                                    }
                                                    $zoneValue = isset($tariffPricingDraftDetails[$weight][$k]['value']) ? $tariffPricingDraftDetails[$weight][$k]['value'] : "";
                                                    $tdhtml .= '<td>';
                                                        $tdhtml .= '<input type="text" name="zones_value[' . $weight . '][' . $k . ']" id="zone_value_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_value zone_price_value_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'" data-weight_str="'. str_replace("/","_",$idValue).'"  value="'.$zoneValue.'" >';
                                                    $tdhtml .= '</td>';
                                                    $zoneMargin = isset($tariffPricingDraftDetails[$weight][$k]['margin']) ? $tariffPricingDraftDetails[$weight][$k]['margin'] : "";
                                                    $tdhtml .= '<td>';
                                                        $tdhtml .= '<input type="text" name="zones_margin[' . $weight . '][' . $k . ']" id="zone_margin_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_margin zone_price_margin_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'"  data-weight_str="'. str_replace("/","_",$idValue).'"  value="'.$zoneMargin.'" >';
                                                    $tdhtml .= '</td>';
                                                    $tdhtml .= '<td>';
                                                        $tdhtml .= '<input type="hidden" name="zone_new[' . $weight . '][' . $k . ']" id="zone_new_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show" value="" readonly="readonly">';
                                                        $tdhtml .= '<div class="custom_field_show" id="zone_new_' . str_replace("/","_",$idValue) . '_' . $k . '" readonly="readonly"></div>';
                                                    $tdhtml .= '</td>';
                                                $tdhtml .= '</tr>';
                                            $tdhtml .= '</table>';
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

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_tariff_pricing_excel') {
            $supplierTariffDetails = [];
            $customerNewTariffDetails = [];
            $weights = $this->form_vars['weight'];
            foreach($weights as $weight) {
                $zones = $this->form_vars['zones'][$weight];
                $zonesNew = $this->form_vars['zone_new'][$weight];
                foreach($zones as $zoneId => $cost) {
                    $costArr = explode('/',$cost);
                    $supplierTariffDetails[$zoneId][$weight] = [
                        'weight_cost' => (isset($costArr[0]) ? $costArr[0] : '0.00'),
                        'piece_cost' => (isset($costArr[1]) ? $costArr[1] : '0.00'),
                    ];
                    $costNew = $zonesNew[$zoneId];
                    $costNewArr = explode('/',$costNew);
                    $customerNewTariffDetails[$zoneId][$weight] = [
                        'weight_cost' => (isset($costNewArr[0]) ? $costNewArr[0] : '0.00'),
                        'piece_cost' => ((isset($costNewArr[1]) && $costNewArr[1] != 'NaN') ? $costNewArr[1] : '0.00'),
                    ];
                }
            }
            $output = [];
            $objPHPExcel = new PHPExcel();
            $zoneHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'CCE5FF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $zoneHeadingBoxTwo =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FF9933')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $zoneDetailHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E0E0E0')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $weightBox =  array(
                'font' => array(
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $profitBox =  array(
                'font' => array(
                    'color' => array('rgb' => '006600'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $lossBox =  array(
                'font' => array(
                    'color' => array('rgb' => '660000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FF9999')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $NoProfitNoLossBox =  array(
                'font' => array(
                    'color' => array('rgb' => '994C00'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $valuesBox =  array(
                'font' => array(
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $profitBox =  array(
                'font' => array(
                    'color' => array('rgb' => '006600'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $borderBox =  array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $legentHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'CCE5FF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            if(!empty($supplierTariffDetails)) {
                $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($legentHeadingBox);
                $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Legend");
                $objPHPExcel->getActiveSheet()->getStyle('B1:B1')->applyFromArray($legentHeadingBox);
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "W");
                $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Weight Cost");
                $objPHPExcel->getActiveSheet()->SetCellValue('A3', "P");
                $objPHPExcel->getActiveSheet()->SetCellValue('B3', "Piece Cost");
                $stColNum = 'A';
                $colNum = 'A';
                $checkZoneNumber = 1;
                foreach ($supplierTariffDetails as $zoneToId => $weights) {
                    $carrierZoneObj = new CarrierZones($zoneToId);
                    $rowNum = 4;
                    $colHead = $stColNum;
                    $colHead++;
                    $colHead++;
                    $colHead++;
                    $colHead++;
                    if (fmod($checkZoneNumber,2) == 0) {
                        $objPHPExcel->getActiveSheet()->getStyle($stColNum . '4:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBoxTwo);
                    } else {
                        $objPHPExcel->getActiveSheet()->getStyle($stColNum . '4:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBox);
                    }
                    $objPHPExcel->getActiveSheet()->mergeCells($stColNum . '4:' . $colHead . $rowNum);
                    $objPHPExcel->getActiveSheet()->SetCellValue($stColNum . '4', trim($carrierZoneObj->getName()));
                    foreach ($weights as $fromToWeight => $cost) {
                        $weightArr = explode('/',$fromToWeight);
                        $fromWeight = $weightArr[0];
                        $toWeight = $weightArr[1];
                        $colNum = $stColNum;
                        if ($rowNum == 4) {
                            $rowNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('8');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Weight");
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('12');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Customer Price");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Supplier Price");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Difference");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('16');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Profit / loss");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                        }
                        $colNum = $stColNum;
                        $tariffWeightCost = $cost['weight_cost'];
                        $tariffPieceCost = $cost['piece_cost'];
                        $newCost = $customerNewTariffDetails[$zoneToId][$fromToWeight];
                        $supplierWeightCost = $newCost['weight_cost'];
                        $supplierPieceCost = $newCost['piece_cost'];

                        $objRichText = new PHPExcel_RichText();
                        $objRichTextValue = new PHPExcel_RichText();
                        $profitLossPercentage = "0.00";
                        $profitLossPercentagePieces = "0.00";
                        $profitLossWeightCostAmount = '0.00';
                        $profitLossPieceCostAmount = '0.00';
                        if ($tariffWeightCost <   $supplierWeightCost) {
                            $profit = 0;
                            $profit =  $supplierWeightCost - $tariffWeightCost;
                            $profitLossWeightCostAmount = trim($this->numberFormat($profit));
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                            if($tariffPieceCost > 0) {
                                $objRichTextValue->createTextRun('/');
                            }
                            if($tariffWeightCost > 0)
                                $profitLossPercentage = $this->numberFormat((($profit * 100) / $tariffWeightCost));
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                            if($tariffPieceCost > 0) {
                                $objRichText->createTextRun('/');
                            }
                        } else if ($tariffWeightCost > $supplierWeightCost){
                            $loss = 0;
                            $loss = $tariffWeightCost - $supplierWeightCost;
                            $profitLossWeightCostAmount = trim($this->numberFormat($loss));
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                            if($tariffPieceCost > 0) {
                                $objRichTextValue->createTextRun('/');
                            }
                            if($tariffWeightCost > 0)
                                $profitLossPercentage = $this->numberFormat((($loss * 100) / $tariffWeightCost));
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                            if($tariffPieceCost > 0) {
                                $objRichText->createTextRun('/');
                            }
                        } else {
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                            if($tariffPieceCost > 0) {
                                $objRichTextValue->createTextRun('/');
                            }
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                            if($tariffPieceCost > 0) {
                                $objRichText->createTextRun('/');
                            }
                        }
                        if($tariffPieceCost > 0) {
                            if ($tariffPieceCost < $supplierPieceCost) {
                                $profit = 0;
                                $profit = $supplierPieceCost - $tariffPieceCost;
                                $profitLossPieceCostAmount = trim($this->numberFormat($profit));
                                $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                                $tariffLossValueObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "006600")));
                                if ($tariffPieceCost > 0)
                                    $profitLossPercentagePieces = $this->numberFormat((($profit * 100) / $tariffPieceCost));
                                $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces . "%");
                                $pieceProftLossObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "006600")));
                            } else if ($tariffPieceCost > $supplierPieceCost) {
                                $loss = 0;
                                $loss = $tariffPieceCost - $supplierPieceCost;
                                $profitLossPieceCostAmount = trim($this->numberFormat($loss));
                                $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                                $tariffLossValueObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "660000")));
                                if ($tariffPieceCost > 0)
                                    $profitLossPercentagePieces = $this->numberFormat((($loss * 100) / $tariffPieceCost));
                                $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces . "%");
                                $pieceProftLossObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "660000")));
                            } else {
                                $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                                $tariffLossValueObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "FF8000")));
                                $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces . "%");
                                $pieceProftLossObj->getFont()->applyFromArray(array("bold" => true, "color" => array("rgb" => "FF8000")));
                            }
                        }
                        $rowNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($weightBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($toWeight));
                        $colNum++;
                        if($supplierPieceCost > 0) {
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($supplierWeightCost) . '/' . $this->numberFormat($supplierPieceCost));
                            $colNum++;
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($supplierWeightCost));
                            $colNum++;
                        }
                        if($tariffPieceCost > 0) {
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($tariffWeightCost) . '/' . $this->numberFormat($tariffPieceCost));
                            $colNum++;
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($tariffWeightCost));
                            $colNum++;
                        }
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichTextValue);
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($borderBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichText);
                        $colNum++;
                    }
                    $stColNum = $colNum;
                    $checkZoneNumber++;
                }
                $fileName = "tariff_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
                $output['status'] = "success";
            } else {
                $output['message'] = "Sorry no data found to excel";
                $output['status'] = "fail";
            }
            echo json_encode($output);
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
                            $whtml .= '<div class="col-md-4">';
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
                            $whtml .= '<div class="col-md-4">';
                                $whtml .= '<label>Add Linehaul</label>';
                                $whtml .= '<input type="text" name="whole_tariff_line_haul" id="whole_tariff_line_haul" class="form-control whole-tariff-line-haul" value="' . $tprdo->getLinehaul() . '">';
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
                            calculateCostApply();
                        }
                    });                    
                });
                $(document).on('click', '#calculate_price', function () {
                    $('#loss').val(0);
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
                            var itemCost = 0.00;
                            if (typeof value[1] != "undefined") {
                                itemCost = value[1];
                            }
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
                    var checkLoss = $('#loss').val();
                    if(checkLoss > 0) {
                        swal({
                            title: "Are you sure want to save tariff save with loss",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                $('#pricing_form_action').val("save_calculated_tariff");
                                $('#tariff_modal').modal("hide");
                                $('#tarif_pricing_form').submit();
                            }
                        });
                    } else {
                        $('#pricing_form_action').val("save_calculated_tariff");
                        $('#tariff_modal').modal("hide");
                        $('#tarif_pricing_form').submit();
                    }
                } else {
                    swal("Sorry!", "Error is high lighted with red border", "error");
                }
            });

            $(document).on('click', '#find_loss', function() {
                $('html, body').animate({
                    scrollTop: $("div.loss_field").offset().top-60
                }, 1000);
                $(this).next('.loss_field').focus();
            });

            $(document).on('click', '#csv_download_btn', function () {
                $('#pricing_form_action').val("download_tariff_pricing");
                $('#tarif_pricing_form').submit();
            });

            $(document).on('click', '#excel_download_btn', function () {
                $('#pricing_form_action').val("download_tariff_pricing_excel");
                $('#tarif_pricing_form').submit();
            });

            $(document).on('click', '.btnDraft', function () {
                var form = $("#tarif_pricing_form");
                var form_data = new FormData(form[0]);
               form_data.append('action', 'save_darft_tariff');
               $.ajax({
                   url: "tariffs_pricing.php",
                   cache: false,
                   contentType: false,
                   processData: false,
                   data: form_data,
                   type: 'post',
                   dataType: 'json',
                   success: function (response) {
                       if(response.status == 'success') {
                           swal("Success!", response.message, "success");
                       }
                   }
               });

                // $('.zone_weight').each(function(){
                //     var weight = $(this).val();
                //     var weightArr = weight.split('/');
                //     var fromWeight = weightArr[0];
                //     var toWeight = weightArr[1];
                //     var weightStr = weight.replace(/[\/.]/g, "_");
                //     $('.zone_price_'+weightStr).each(function(){
                //         var zoneId = $(this).data('zone_id');
                //         var value = $(this).val().split('/');
                //         var kgCost = value[0];
                //         var itemCost = 0.00;
                //         if (typeof value[1] != "undefined") {
                //             itemCost = value[1];
                //         }
                //        
                //         calculateNewCost(kgCost,itemCost,fromWeight,toWeight,zoneId,weightStr);
                //     });
                // });
            });

            $(document).on('keyup','.custom_margin', function() {
                var id_str = $(this).data('id_str');
                var margin = $(this).val();
                var value = $('#zone_value_input_'+id_str).val();
                var cost = $("#zone_input_"+id_str).val();
                var cost_value = cost.split('/');
                var kgCost = cost_value[0];
                var itemCost = cost_value[1];
                if(typeof itemCost === "undefined") {
                    itemCost = 0.00;
                }
                calculateNewCostManually(id_str,value,margin,kgCost,itemCost);
            });

            $(document).on('keyup','.custom_value', function() {
                var id_str = $(this).data('id_str');
                var value = $(this).val();
                var margin = $('#zone_margin_input_'+id_str).val();
                var cost = $("#zone_input_"+id_str).val();
                var cost_value = cost.split('/');
                var kgCost = cost_value[0];
                var itemCost = cost_value[1];
                if(typeof itemCost === "undefined") {
                    itemCost = 0.00;
                }
                calculateNewCostManually(id_str,value,margin,kgCost,itemCost);
            });

            function calculateCostApply() {
                $('.zone_weight').each(function(){
                    var weight = $(this).val();
                    var weightArr = weight.split('/');
                    var fromWeight = weightArr[0];
                    var toWeight = weightArr[1];
                    var weightStr = weight.replace(/[\/.]/g, "_");
                    $('.zone_price_'+weightStr).each(function(){
                        var zoneId = $(this).data('zone_id');
                        var id_str = weightStr + '_' + zoneId;
                        var value = $('#zone_value_input_' + id_str).val();
                        var margin =  $('.zone_margin_input_' + id_str).val();
                        var cost = $("#zone_input_" + id_str).val();
                        var cost_value = cost.split('/');
                        var kgCost = cost_value[0];
                        var itemCost = cost_value[1];
                        if(typeof itemCost === "undefined") {
                            itemCost = 0.00;
                        }
                        if(typeof margin === "undefined") {
                            margin = 0.00;
                        }
                        if(value > 0 || margin > 0) {
                            calculateNewCostManually(id_str,value,margin,kgCost,itemCost);
                        }
                    });
                });
            }

            function calculateNewCostManually(id_str,value,margin,kgCost,itemCost) {
                var kgCostNew = parseFloat(kgCost);
                if(value != ""  && $.isNumeric(value)) {
                    kgCostNew = (parseFloat(kgCost) + parseFloat(value));
                }
                var pieceCostNew = itemCost;
                if(value > 0) {
                    pieceCostNew = (parseFloat(itemCost) + parseFloat(value));
                }
                if(margin != "") {
                    var kgCostNew = (parseFloat(kgCostNew) + ((parseFloat(margin)/100) * parseFloat(kgCostNew)));
                }
                kgCostNew = kgCostNew.toFixed(2);
                pieceCostNew = pieceCostNew.toFixed(2);
                var new_calculated_cost = (kgCostNew + '/' + pieceCostNew);
                if(itemCost <= 0) {
                    new_calculated_cost = kgCostNew;
                }
                var kgColor = "success-color";
                var pieceColor = "success-color";
                var boder_color = "success-border";
                $('#zone_new_'+id_str).removeClass('loss_field');
                if(parseFloat(kgCostNew) < parseFloat(kgCost)) {
                    kgColor = "danger-color";
                    boder_color = "warning-border";
                    $('#loss').val(1);
                    $('#zone_new_'+id_str).addClass('loss_field');
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

                kgCostNew = parseFloat(kgCostNew).toFixed(2);
                pieceCostNew = parseFloat(pieceCostNew).toFixed(2);
                var html = '<span class="'+kgColor+'">'+kgCostNew+'</span><span>/</span><span class="'+pieceColor+'">'+pieceCostNew+'</span>';
                if(itemCost <= 0) {
                    html = '<span class="'+kgColor+'">'+kgCostNew+'</span>';
                }
                $('#zone_new_'+id_str).html(html);
                $('#zone_new_input_'+id_str).val(new_calculated_cost);
            }

            function calculateNewCost(kgCost,itemCost,fromWeight,toWeight,zoneId,weightStr) {
                var applyMarginNumber = "";
                var applyValueNumber = "";
                var wholeMargin = $('#whole_tariff_margin').val();
                applyMarginNumber = wholeMargin;
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
                applyValueNumber = wholeLineHaul;
                var wholeLineHaulType = $('#whole_tariff_line_haul_type').val();
                var kgCostNew = parseFloat(kgCost);
                var pieceCostNew = parseFloat(itemCost);
                if(wholeLineHaul != "") {
                    kgCostNew = parseFloat(kgCostNew) + parseFloat(wholeLineHaul);
                }
                if(wholeMargin != "") {
                    if(wholeMarginType == "percentage") {
                        if(wholeMarginKgCost) {
                            var applyValue = (parseFloat(wholeMargin)/100)*parseFloat(kgCostNew);
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
                $('#zone_margin_input_'+weightStr+'_'+zoneId).val(applyMarginNumber);
                $('#zone_value_input_'+weightStr+'_'+zoneId).val(applyValueNumber);
                $('.to_zone_id').each(function(key,obj) {
                    var toZoneId = obj.value;
                    if(zoneId == toZoneId) {
                        var fromWeightEnter = parseFloat($("#weight_from_"+key).val());
                        var toWeightEnter = parseFloat($("#weight_to_"+key).val());
                        var margin = $.trim($("#margin_"+key).val());
                        if(margin != "") {
                            margin = parseFloat(margin);
                            applyMarginNumber = margin;
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
                            applyValueNumber = lineHaul;
                        }
                        var lineHaulType = $("#line_haul_type_"+key).val();
                        
                        fromWeightEnter = fromWeightEnter.toFixed(2);
                        toWeightEnter = toWeightEnter.toFixed(2);

                        if((parseFloat(fromWeight) >= parseFloat(fromWeightEnter)) && (parseFloat(toWeight) <= parseFloat(toWeightEnter))) {
                            kgCostNew = parseFloat(kgCost);
                            pieceCostNew = parseFloat(itemCost);
                            if(lineHaul != "") {
                                kgCostNew = parseFloat(kgCostNew) + parseFloat(lineHaul);
                            }
                            if(margin != "") {
                                if(margin_type == "percentage") {
                                    if(marginWeightCost) {
                                        var applyValue = parseFloat((parseFloat(margin)/100)*parseFloat(kgCostNew));
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
                            $('#zone_margin_input_'+weightStr+'_'+zoneId).val(applyMarginNumber);
                            $('#zone_value_input_'+weightStr+'_'+zoneId).val(applyValueNumber);
                        }
                    }
                });

                kgCostNew = parseFloat(kgCostNew).toFixed(2);
                pieceCostNew = parseFloat(pieceCostNew).toFixed(2);

                var kgColor = "success-color";
                var pieceColor = "success-color";
                var boder_color = "success-border";
                $('#zone_new_'+weightStr+'_'+zoneId).removeClass('loss_field');
                if(parseFloat(kgCostNew) < parseFloat(kgCost)) {
                    kgColor = "danger-color";
                    boder_color = "warning-border";
                    $('#loss').val(1);
                    $('#zone_new_'+weightStr+'_'+zoneId).addClass('loss_field');
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
                if(itemCost <= 0 || isNaN(itemCost)) {
                    html = '<span class="'+kgColor+'">'+kgCostNew+'</span>';
                }
                $('#zone_new_input_'+weightStr+'_'+zoneId).val(input_value);
                $('#zone_new_'+weightStr+'_'+zoneId).html(html);
            }
        </script>
        <?php
    }

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Tariff Pricing
                </div>
                <div class="actions">
                    <?php if(!empty($this->params)) { ?>
                        <a href="tariffs_list.php?<?php echo $this->params; ?>" class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp; Tariff Listing</a>
                    <?php } else { ?>
                        <a href="tariffs_list.php" class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp; Tariff Listing</a>
                    <?php } ?>
                </div>
                <div class="tools"> </div>
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
                <form name="tarif_pricing_form" id="tarif_pricing_form" action="" method="post">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Tariff Name</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <?php
                                                $sql = "SELECT id,name FROM tariffs t WHERE t.tariff_type LIKE 'supplier%'";
                                                ?>
                                                <?php echo Ddl::generateDDLFromSql($sql, 'tariff_selected_id', 'name', 'id', $this->tariffId, ' class="input-sm form-control select2 validate_check" data-live-search="true" data-show-subtext="true" readonly="readonly" ', 'Select Tariff', '', 'tariff_selected_id'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Tariff Rules</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <?php
                                                if (!empty($_GET['rule_id']) && is_numeric($_GET['rule_id'])) {
                                                    $sql = "SELECT tpr.id,tpr.name FROM tariffs_pricing_rules tpr WHERE tpr.id = $this->ruleId";
                                                } else {                                                    
                                                    $sql = "SELECT tpr.id,tpr.name FROM tariffs_pricing_rules tpr WHERE tpr.tariff_id = $this->tariffId";
                                                }
                                                ?>
                                                <?php echo Ddl::generateDDLFromSql($sql, 'tariff_rule_id', 'name', 'id', $this->ruleId, ' class="input-sm form-control select2" data-live-search="true" data-show-subtext="true"', 'Select Tariff Rule', '', 'tariff_rule_id'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <?php
                                            echo Ddl::generateDDL('currency_id_show', 'CurrencyFilter', "AND isactive='1' AND ClientDisplay='1'", 'currencyname', 'id', '', ' class="input-sm form-control select2 validate_check_tariff" readonly="readonly" ', 'Select Currency', '');
                                            ?>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                    <div class="row margin-bottom-10" id="tariff_single_pricing">
                        <fieldset class="fieldset">
                            <legend>Whole Tariff Price:</legend>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Add Value</label>
                                    <input type="text" name="whole_tariff_line_haul" id="whole_tariff_line_haul" class="form-control whole-tariff-line-haul">
                                </div>
                                <div class="col-md-4">
                                    <label>Add Margin</label>
                                    <div class="input-group">
                                        <input type="text" name="whole_tariff_margin" id="whole_tariff_margin" class="form-control">
                                        <div class="input-group-btn">
                                            <select class="selectpicker form-control" name="whole_tariff_margin_type" id="whole_tariff_margin_type" >
                                                <option value="percentage">%</option>
                                                <option value="price">Fixed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Margin on</label>
                                    <div class="mt-checkbox-inline padding-top-0 padding-bottom-0">
                                        <label class="mt-checkbox margin-bottom-0"> KG Cost
                                            <input type="checkbox" class="whole-tariff-margin-weight-cost" id="whole_tariff_margin_weight_cost" value="weight" name="whole_tariff_margin_weight_cost" checked="checked" />
                                            <span></span>
                                        </label>
                                        <label class="mt-checkbox margin-bottom-0"> Item Cost
                                            <input type="checkbox" class="whole-tariff-margin-piece-cost" id="whole_tariff_margin_piece_cost" value="piece" name="whole_tariff_margin_piece_cost" checked="checked" />
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="row margin-bottom-10">
                        <fieldset class="fieldset">
                            <legend>Tariff detail Pricing:</legend>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Destination</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Weight</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Add Value</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Add Margin</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Margin On</label>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                                <div class="tariffs-pricing-container" id="tariff_detail_pricing">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                    <span id="zone_to_span">
                                                        <select class="form-control select2 to_zone_id zone-to-id" name="apply[0][to_zone_id]" id="to_zone_id_0">
                                                            <option value="">Select Zone</option>
                                                        </select>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control weight-from" min="0" name="apply[0][weight_from]" id="weight_from_0" value="" >
                                                    <span class="input-group-addon"> to </span>
                                                    <input type="text" class="form-control weight-to" min="0" name="apply[0][weight_to]" id="weight_to_0" value="" >
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="apply[0][line_haul]" class="form-control tariff-line-haul" id="line_haul_0">
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control tariff-margin" name="apply[0][margin]" id="margin_0" >
                                                    <div class="input-group-btn">
                                                        <select class="selectpicker form-control tariff-margin-type" name="apply[0][margin_type]" id="margin_type_0" >
                                                            <option value="percentage">%</option>
                                                            <option value="price">Fixed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="padding-top-0 padding-bottom-0">
                                                    <label class="mt-checkbox margin-bottom-0 margin-right-10"> KG Cost
                                                        <input type="checkbox" class="margin-weight-cost" value="weight" name="apply[0][margin_weight_cost]" id="margin_weight_cost_0" checked="checked" />
                                                        <span></span>
                                                    </label>
                                                    <label class="mt-checkbox margin-bottom-0 margin-right-10"> Item Cost
                                                        <input type="checkbox" class="margin-piece-cost" value="piece" name="apply[0][margin_piece_cost]" id="margin_piece_cost_0" checked="checked" />
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success add-more-tariff-pricing-keys"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-danger remove-tariff-pricing-key initial-button"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="col-sm-12" style="margin-top: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success btn-sm" id="calculate_price">Apply</button>
                                    <button type="button" class="btn btn-success btn-sm" id="save_rules">Save Rules</button>
<!--                                    <button type="button" class="btn btn-primary btn-sm" id="find_loss" >Find Loss</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-bottom-10">
                        <div class="col-md-6">
                            <div class="caption margin-bottom-10 block">
                                <span class="caption-subject bold uppercase">Tariff Details</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pull-right">
                                <button type="button" name="btnDraft" id="btnDraft" class="btn btn-success btn-sm btnDraft">Save as Draft </button>
                            </div>
                        </div>
                        <input type="hidden" name="loss" id="loss" />
                        <div class="col-sm-12" id="tariff_detals"></div>
                    </div>
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <input type="hidden" name="tariff_id" value="<?php echo $this->tariffId; ?>" />
                            <input type="hidden" name="action" id="pricing_form_action" value="save_tariffPricing" />
                            <button type="button" name="btnDraft" id="btnDraft" class="btn btn-success btnDraft">Save as Draft </button>
                            <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save">Save Customer Tariff </button>
                            <a id="excel_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;Preview Tariff</a>
                            <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                            <a href="tariffs_list.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                            <button type="button" onclick="window.history.back();" class="btn btn-primary btn_save">Back</button>
                        </div>
                    </div>
                    <div id="tariff_modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title" id="tariff_model_title">Tariff</h4>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="from_zone_id" id="from_zone_id" value="" />
                                    <input type="hidden" name="carrier_id" id="carrier_id" value="" />
                                    <input type="hidden" name="service_id" id="service_id" value="" />
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tariff Name</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                    <input type="text" name="tariff_name" id="tariff_name" class="form-control validate_check_tariff" value="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                    <?php
                                                    echo Ddl::generateDDL('currency_id', 'CurrencyFilter', "AND isactive='1' AND ClientDisplay='1'", 'currencyname', 'id', '', ' class="input-sm form-control select2 validate_check_tariff"', 'Select Currency', '');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>                                    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                    <input type="text" class="form-control input-sm validate_check_tariff" readonly name="start_date" id="start_date" placeholder="" value="" >                                  
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                    <input type="text" class="form-control input-sm validate_check_tariff" readonly name="end_date" id="end_date" placeholder="" value="" >                                   
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Tariff Formula</label>
                                            <select name="tariffs_formula" class="form-control select2 formula input-sm">
												<?php
												$tariffFormulas = get_tariff_formulas();
												if(!empty($tariffFormulas)){
													foreach($tariffFormulas as $tariffFormula => $tariffFormulaText){
														$selected = "";
														?>
														<option value="<?php echo $tariffFormula; ?>"<?php echo $selected; ?>><?php echo $tariffFormulaText; ?></option>
														<?php
													}
												}
												?>
                                                <!--<option value="">Only Consider KG Cost</option>
                                                <option value="Q * ITMCHR" >No of Items * Item Cost
                                                </option>
                                                <option value="Q * CHRG"  >No of Items * KG Cost
                                                </option>
                                                <option value="Q * ( ITMCHR + REG ) + W * CHRG" > (No. of Items * (Item Cost + Register Cost ) )+ (Items
                                                    Weight * KG Cost) (Register Post)
                                                </option>
                                                <option value="( Q * ITMCHR ) + ( W * CHRG )" > (No. of Items * Item Cost ) + ( Items Weight * KG Cost )
                                                    (UnTrack Register Post)
                                                </option>
                                                <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG"  >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                    Weight - Current range From Weight) / 0.45 )) + KG Cost (DHL
                                                    ECO)
                                                </option>
                                                <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG"  >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                    Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL
                                                    EXP)
                                                </option>
                                                <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG"  >( Item Cost * ROUND_NEXT_NUMER(( Items
                                                    Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL
                                                    EXP)
                                                </option>
                                                <option value="(  ITMCHR * ceil ( W - FRMW )) + CHRG"  >( Item Cost * ROUND_NEXT_NUMER( Items
                                                    Weight - Current range From Weight)) + KG Cost (DHL
                                                    EXP)
                                                </option>
                                                <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG"  >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                    Weight - Current range From Weight ) / 0.45 )) + KG Cost
                                                    (DHL ECO SHIPMENT)
                                                </option>
                                                <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG" >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                    Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL
                                                    EXP SHIPMENT))
                                                </option>
                                                <option value="Q * (  ITMCHR * ceil ( W - FRMW )) + CHRG" >No. of Items * ( Item Cost * ROUND_NEXT_NUMER( Items
                                                    Weight - Current range From Weight ) ) + KG Cost (STANDARD)
                                                </option>-->
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Rule Name</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                    <input type="text" name="tariff_rules_name" id="tariff_rules_name" class="form-control validate_check_rule" value="" />
                                                </div>
                                            </div>
                                        </div>                                                                                
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" id="description" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Active</label><br />
                                            <input id="tariff_status" name="chkActive" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                                    <button type="button" id="tariff_save_btn" class="btn green">Save Tariff</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="rules_modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Rules</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Rule Name</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                    <input type="text" name="tariff_rules_name_save" id="tariff_rules_name_save" class="form-control validate_check_rule_2" value="" />
                                                </div>
                                            </div>
                                        </div>                                                                                
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                                    <button type="button" id="btnRulesSave" class="btn green">Save Rules</button>
                                </div>
                            </div>
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
                padding: 2px 2px;
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
            .custom_table thead th { width: 450px;}
            .custom_table thead th:first-child { width: 115px;}
            .custom_table thead tr:first-child th { text-align: center;}
            .custom_table tbody tr td input{ text-align: center;}
            .custom_padding{
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            .fieldset {
                border: 1px solid #3598dc;
                padding: 10px 5px;
                margin: 0px 14px;
            }
            .fieldset legend {
                width: auto;
                margin: inherit;
                border: none;
                font-size: 13px;
                font-weight: bold;
            }
            .custom_field_show {
                width: 50px;
                border: 1px solid;
                min-height: 21px;
                text-align: center;
            }
            .inner_table_heading {
                width: 100%;
            }
            .inner_table_heading tr td {
                text-align: center;
                width: 60px;
            }
            .inner_table {
                width: 100%;
            }
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
