<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');

ini_set("session.cache_expire", 36000);
ini_set("session.gc_maxlifetime", 36000);
ini_set("session.cookie_lifetime", 36000);

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
	'consignmentchargestypes.class',
	'consignmentchargestypesfilter.class',
	'linehaul.class',
	'linehaulfilter.class'
]);
class Page extends BasePage
{
	private $user;
	private $tariffs;
	private $weightLimits;
	private $toCountryLimit = 500;
	/*     * *
     * Controller logic
     */
	protected function numberFormat($number,$decimalPoints = 2){
		return number_format($number,$decimalPoints,'.','');
	}

	protected function init()
	{
		$this->user = SessionManager::getUser();
		$this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
			'get_pricing.php' => "Get Pricing"
		);

		if(isset($_POST['func']) && $_POST['func'] == 'refresh_session'){
			if (isset($_SESSION['id']))
				$_SESSION['id'] = $_SESSION['id'];
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'calculate_pricing') {
			$output = [];
			$tariffLossSave = $this->form_vars['loss_save'];
			$tariffType = $this->form_vars['tariff_type'];
			$fromCountry = $this->form_vars['from_country'];
			// Add new Service
			$is_untrack = 0;
			$maxlength = 0;
			$maxwidth = 0;
			$maxheight = 0;
			$fuelsurcharge = 0;
			$maximum_allowed_dimension = 0;
			$tracking_flag = 0;
			$friday_only_flag = 0;
			$saturday_only_flag = 0;
			$sunday_only_flag = 0;
			$insurance_available = 0;
			$preAdvise = "N";
			$preAlert = "N";
			$validationType = "mail";
			$remotearea = 'ON_PIECE';
			$required_email = 0;
			$required_telephone = 0;
			$allowOversize = 0;
			$package_type = 2;

			$tariff_save_with_customer_tariff = $this->form_vars["tariff_save_with_customer_tariff"];
			$ser_carrier_id = $this->form_vars["carrier_id"];
			$ser_service_name = $this->form_vars["service_name"];
			$ser_service_code = $this->form_vars["service_code"];
			$ser_min_weight = $this->form_vars["min_weight"];
			$ser_max_weight = $this->form_vars["max_weight"];
			if($tariffType == 1) {
				$services = new Services();
				$services->setIsCustomized(1);
				$services->setCarrierId($ser_carrier_id);
				$services->setName($ser_service_name);
				$services->setCode($ser_service_code);
				$services->setOriginCountry($fromCountry);
				$services->setActive(1);
				$services->setFromWeight($ser_min_weight);
				$services->setToWeight($ser_max_weight);
				$services->setIsUntrack($is_untrack);
				$services->setWieghtType($package_type);
				$services->setMaxLength($maxlength);
				$services->setMaxWidth($maxwidth);
				$services->setMaxHeight($maxheight);
				$services->setFuelSurcharge($fuelsurcharge);
				$services->setTrackingFlag($tracking_flag);
				$services->setFridayOnlyFlag($friday_only_flag);
				$services->setSaturdayOnlyFlag($saturday_only_flag);
				$services->setSundayOnlyFlag($sunday_only_flag);
				$services->setInsuranceAvailable($insurance_available);
				$services->setMaximumAllowedDimension($maximum_allowed_dimension);
				$services->setPreAdvise($preAdvise);
				$services->setPreAlert($preAlert);
				$services->setRemotearea($remotearea);
				$services->setRequiredEmail($required_email);
				$services->setRequiredTelephone($required_telephone);
				$services->setDeletedq(0);
				$services->setChangedBy($this->user->getId());
				$services->setChangedOn(date("Y-m-d H:i:s"));
				$services->setAllowOversize($allowOversize);
				$services->setValidationType($validationType);
				$services->setShipmentType('PARCEL');
				$services->setIsRemotearea('N');
				$services->setAddedBy($this->user->getId());
				$services->setAddedOn(date("Y-m-d H:i:s"));
				$services->save();
				$customized_service_id = $services->getId();
			} else {
				$customized_service_id = $this->form_vars["service_id"];
			}
			// Add service Zones
			$zoneCountries = [];
			$countriesIds = $this->form_vars["selected_to_countries"];
			if($countriesIds != "") {
				$zoneCountries = explode(",", $countriesIds);
				$zoneCountries[] = $fromCountry;
				/*if(count($countriesIdsArr)) {
                    foreach($countriesIdsArr as $countriesId) {
                        if(!in_array($countriesId, $zoneCountries)) {
                            $zoneCountries[] = $countriesId;
                        }
                    }
                }*/
			}
			$carrier = new Carrier($ser_carrier_id);
			$carrierZoneBase = $carrier->getZoneBase();
			$countryZoneArr = [];
			$zoneCountries = array_unique($zoneCountries);
			if(count($zoneCountries)) {
				foreach($zoneCountries as $country_id) {
					$countryObj = new Country($country_id);
					$checkCountryExist = $this->checkCarrierZoneHasCountry($ser_carrier_id, $customized_service_id, $country_id);
					if(!$checkCountryExist || $tariffType == 1) {
						// Add Carrier Zone
						$carrierZone = new CarrierZones();
						$carrierZone->setCarrierId($ser_carrier_id);
						if($carrierZoneBase == 0){
							$carrierZone->setServiceId($customized_service_id);
						} else {
							$carrierZone->setServiceId(0);
						}
						$carrierZone->setName($countryObj->getName());
						$carrierZone->setSortOrder(1);
						$carrierZone->setStatus(1);
						$carrierZone->setAddedBy($this->user->getId());
						$carrierZone->setDateAdded(date('Y-m-d H:i:s'));
						$carrierZone->setDateUpdated(date('Y-m-d H:i:s'));
						$carrierZone->save();
						$carrierZoneId = $carrierZone->getId();
						// Add Zone Country
						$carrierZonesCountries = new CarrierZonesCountries();
						$carrierZonesCountries->setCarrierZoneId($carrierZoneId);
						$carrierZonesCountries->setCountryId($country_id);
						$carrierZonesCountries->save();
						$countryZoneArr[$country_id] = $carrierZoneId;
					}
				}
			}

			$serviceObj = new Services($customized_service_id);
			$carrier_id = $serviceObj->getCarrierId();

			$user_account_id = $this->user->getUserAccountId();
			$name = $this->form_vars['tariff_name'];
			$type = 'Supplier';
			$status = 1;
			$currencyId = $this->form_vars['currency_id'];
			$start_date = date('Y-m-d', strtotime($this->form_vars['start_date']));
			$end_date = date('Y-m-d', strtotime($this->form_vars['end_date']));
			$description = "Save Supplier Auto Tariff";
			$date_added = time();
			$added_by = $this->user->getId();
			$date_update = time();
			$update_by = $this->user->getId();
			$arrierZonesFilter = new CarrierZonesFilter();
			$arrierZonesFilter->addJoin("carrier_zones_countries czc", "czc.`carrier_zone_id` = cz.`id`");
			if($carrierZoneBase == 0) {
				$arrierZonesFilter->addFieldFilter("      cz.`service_id`", $customized_service_id);
			}
			$arrierZonesFilter->addFieldFilter("      czc.`country_id`", $fromCountry);
			$fromZone = $arrierZonesFilter->getColumnList("cz.id");
			if(count($fromZone) > 0) {
				$fromZone = $fromZone[0];
				/*Save Tariff*/
				if($tariffType == 1) {
					$tariffsObj = new Tariffs();
					$tariffsObj->setUserAccountId($user_account_id);
					$tariffsObj->setCarrierId($carrier_id);
					$tariffsObj->setServiceId($customized_service_id);
					$tariffsObj->setName($name);
					$tariffsObj->setStatus($status);
					$tariffsObj->setCurrencyId($currencyId);
					$tariffsObj->setTariffType($type);
					$tariffsObj->setStartDate($start_date);
					$tariffsObj->setEndDate($end_date);
					$tariffsObj->setDescription($description);
					$tariffsObj->setTariffTemplate('default');
					$tariffsObj->setDateAdded($date_added);
					$tariffsObj->setAddedBy($added_by);
					$tariffsObj->setDateUpdated($date_update);
					$tariffsObj->setUpdatedBy($update_by);
					$tariffsObj->save();
					/* Trarif Details */
					$tarif_id = $tariffsObj->getId();
				} else {
					$tarif_id = $this->form_vars['tariff_id'];
				}
				//$selectedTariffService = $this->form_vars['selected_tariff_service'];
				$tariffJson = $this->form_vars['tariff_json'];
				$selectedTariffService = json_decode($tariffJson);
				$selectedServiceTtime = $this->form_vars['selected_service_ttime'];
				$countryToZoneNotFound = [];
				//print_r($selectedTariffService); exit;
				$tariffDetailCSVContent = '';
				$serviceRoutingCSVContent = '';
				$countryTtimeCSVContent = '';
				$ttime = 0;
				$showPieceCost = $this->form_vars['show_piece_cost'];
				$showKgCost = $this->form_vars['show_kg_cost'];
				foreach ($selectedTariffService as $countryId => $rangeArr) {
					$arrierZonesFilter = new CarrierZonesFilter();
					$arrierZonesFilter->addJoin("carrier_zones_countries czc", "czc.`carrier_zone_id` = cz.`id`");
					$arrierZonesFilter->addFieldFilter("cz.`service_id`", $customized_service_id);
					$arrierZonesFilter->addFieldFilter("czc.`country_id`", $countryId);
					$toZone = $arrierZonesFilter->getColumnList("cz.id");
					if(count($toZone) > 0) {
						$toZone = $toZone[0];
						foreach ($rangeArr as $range => $serviceData) {
							$serviceDataArr = (array)$serviceData;
							$serviceId = 0;
							$serviceCost = 0;
							foreach($serviceDataArr as $_serviceId => $_serviceCost){
								$serviceId = $_serviceId;
								$serviceCost = $_serviceCost;
							}
							$serviceCostArr = explode("-",$serviceCost);
							$cost = $serviceCostArr[0];
							$ttime = $serviceCostArr[1];
							$supplierTariffFormula = str_replace("_","-",$serviceCostArr[2]);
							$weightArr = explode('-', trim($range));
							$from_zone_id = $fromZone->getId();
							$to_zone_id = $toZone->getId();
							$weight_from = (isset($weightArr[0]) ? $weightArr[0] : '');
							$weight_to = (isset($weightArr[1]) ? $weightArr[1] : '');
							$weightCostSave = $cost;
							$pieceCostSave = 0.00;
							if($showPieceCost == 1 && $showKgCost == 1) {
								$newCost = explode('/', $cost);
								$weightCostSave = (isset($newCost[0]) ? trim($newCost[0]) : 0.00);
								$pieceCostSave = (isset($newCost[2]) ? trim($newCost[2]): 0.00);
							} else if($showPieceCost == 1){
								$newCost = explode('/', $cost);
								$weightCostSave = (isset($newCost[0]) ? trim($newCost[0]) : 0.00);
								$pieceCostSave = (isset($newCost[1]) ? trim($newCost[1]): 0.00);
							}
							$user_weight_cost = $weightCostSave;
							$user_peice_cost = $pieceCostSave;
							$formula = $supplierTariffFormula;//$this->form_vars['tariffs_formula'];
							$tariffsDetailsFilter = new TariffsDetailsFilter();
							$tariffsDetailsFilter->addFieldFilter("td.from_zone_id", $from_zone_id);
							$tariffsDetailsFilter->addFieldFilter("td.to_zone_id", $to_zone_id);
							$tariffsDetailsFilter->addFieldFilter("td.weight_from", $weight_from);
							$tariffsDetailsFilter->addFieldFilter("td.weight_to", $weight_to);
							//$tariffsDetailsFilter->addFilter("td.weight_from >= ".$weight_from);
							//$tariffsDetailsFilter->addFilter("td.weight_to <= ".$weight_to);
							$tariffsDetailsFilterObjs = $tariffsDetailsFilter->getColumnList("td.id,td.weight_cost,td.piece_cost");
							$tariffsDetailsFilterObj = [];
							$csvWeightCost = $user_weight_cost;
							$csvPeiceCost = $user_peice_cost;
							/*
                            $tariffsDetailObj = new TariffsDetails();
                            if(count($tariffsDetailsFilterObjs) > 0) {
                                $tariffsDetailsFilterObj = $tariffsDetailsFilterObjs[0];
                                $tariffsDetailObj = new TariffsDetails($tariffsDetailsFilterObj->getId());
                                if($tariffsDetailsFilterObj->getWeightCost() < $user_weight_cost) {
                                    //$tariffsDetailObj->setWeightCost($user_weight_cost);
                                    $csvWeightCost = $user_weight_cost;
                                }else{
                                    $csvWeightCost = $tariffsDetailsFilterObj->getWeightCost();
                                }
                                if($tariffsDetailsFilterObj->getPieceCost() < $user_peice_cost) {
                                    //$tariffsDetailObj->setPieceCost($user_peice_cost);
                                    $csvPeiceCost = $user_peice_cost;
                                }else{
                                    $csvWeightCost = $tariffsDetailsFilterObj->getPieceCost();
                                }
                            } else {
                                $csvWeightCost = $user_weight_cost;
                                $csvPeiceCost = $user_peice_cost;
                                //$tariffsDetailObj->setWeightCost($user_weight_cost);
                                //$tariffsDetailObj->setPieceCost($user_peice_cost);
                            }
                            */
							if(count($tariffsDetailsFilterObjs) == 0 || $tariffType == 0) {
								if($tariffType == 0 && count($tariffsDetailsFilterObjs) > 0) {
									TariffsDetails::deleteById($tariffsDetailsFilterObjs[0]->getId());
								}
								$tariffDetailCSVContent .= (!empty($tariffDetailCSVContent) ? "\n" : '').$tarif_id.','.$from_zone_id.','.$to_zone_id.','.$weight_from.','.$weight_to.','.$csvWeightCost.','.$csvPeiceCost.','.$formula;
							}
							/*$tariffsDetailObj->setTariffsId($tarif_id);
                            $tariffsDetailObj->setFromZoneId($from_zone_id);
                            $tariffsDetailObj->setToZoneId($to_zone_id);
                            $tariffsDetailObj->setWeightFrom($weight_from);
                            $tariffsDetailObj->setWeightTo($weight_to);
                            $tariffsDetailObj->setFormula($formula);
                            $tariffsDetailObj->save();*/

							/*Save routing*/
							/*$customizedServicesRoutingObj = new CustomizedServicesRouting();
                            $customizedServicesRoutingObj->setCountryId($countryId);
                            $customizedServicesRoutingObj->setFromWeight($weight_from);
                            $customizedServicesRoutingObj->setToWeight($weight_to);
                            $customizedServicesRoutingObj->setStatus(1);
                            $customizedServicesRoutingObj->setCustomizeServiceId($customized_service_id);
                            $customizedServicesRoutingObj->setServiceId($serviceId);
                            $customizedServicesRoutingObj->save();*/

							/* user service routing filter  */
							if($tariffType == 1) {
								$userServiceRouting = new UserServicesRouting();
								$userServiceRouting->setUserAccountId($user_account_id);
								$userServiceRouting->setCountryId($countryId);
								$userServiceRouting->setFromWeight($weight_from);
								$userServiceRouting->setToWeight($weight_to);
								$userServiceRouting->setStatus(1);
								$userServiceRouting->setServiceId($customized_service_id);
								$userServiceRouting->setIsRemotearea(0);
								$userServiceRouting->setIsOverLabel(0);
								$userServiceRouting->setAddedBy($added_by);
								$userServiceRouting->setIsAgreed(1);
								$userServiceRouting->setLabelCharges('0.00');
								$userServiceRouting->setIsDeadWeight(0);
								$userServiceRouting->save();
							}

							$customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
							$customizedServicesRoutingFilter->addFromWeightFilter($weight_from);
							$customizedServicesRoutingFilter->addFromWeightFilter($weight_to);
							$customizedServicesRoutingFilter->addCountryFilter($countryId);
							$customizedServicesRoutingFilter->addCustomizeServiceIdFilter($customized_service_id);
							$customizedServicesRoutingCount = $customizedServicesRoutingFilter->getCount();
							if($customizedServicesRoutingCount == 0)
								$serviceRoutingCSVContent .= $countryId.','.$weight_from.','.$weight_to.',1,'.$customized_service_id.','.$serviceId."\n";

							/*Save ttime */
							/*
                            $countryTtimeFilter = new ServiceCountryTimeFilter();
                            $countryTtimeFilter->addFieldFilter("    id_country", $countryId);
                            $countryTtimeFilter->addFieldFilter("    id_service", $customized_service_id);
                            $countryTtimeFilterObjs = $countryTtimeFilter->getColumnList("id");
                            //$ttime = $selectedServiceTtime[$countryId][$serviceId][$range];
                            $countryTtimeObj = new ServiceCountryTime();
                            if(count($countryTtimeFilterObjs) == 0) {
                             */
							$countryTtimeCSVContent .= $countryId.','.$customized_service_id.','.$ttime."\n";
							//$countryTtimeFilterObj = $countryTtimeFilterObjs[0];
							//$countryTtimeObj = new ServiceCountryTime($countryTtimeFilterObj->getId());
							//}
							/*$countryTtimeObj->setIdCountry($countryId);
                            $countryTtimeObj->setIdService($customized_service_id);
                            $countryTtimeObj->setTransitTime($ttime);
                            $countryTtimeObj->save();*/
						}
					} else {
						$countryToZoneNotFound[] = $countryId;
					}
				}
				// save data
				if(!empty($tariffDetailCSVContent)){
					$tariffDetailCSVFile = '../_assets/csv/tariff_detail_csv_'.time().".csv";
					if(file_put_contents($tariffDetailCSVFile,$tariffDetailCSVContent)){
						$tariffDetailSql = "LOAD DATA LOCAL INFILE '".$tariffDetailCSVFile."' INTO TABLE `tariffs_details` CHARACTER SET 'utf8' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (
                                              `tariffs_id`,
                                              `from_zone_id`,
                                              `to_zone_id`,
                                              `weight_from`,
                                              `weight_to`,
                                              `weight_cost`,
                                              `piece_cost`,
                                              `formula`
                                            );";
						/*$tariffDetailSql = "LOAD DATA LOCAL INFILE
                                        '".$tariffDetailCSVFile."'
                                        INTO TABLE tariffs_details
                                        FIELDS TERMINATED BY ','
                                        LINES TERMINATED BY '\n'
                                        FIELDS ENCLOSED BY '\"'
                                        (tariffs_id,from_zone_id,to_zone_id,weight_from,weight_to,weight_cost,piece_cost,formula);";*/
						//echo $tariffDetailSql; exit;
						DbAccess3::runQueryWithError($tariffDetailSql);
						$dbError = DbAccess3::$dbError;
						if(count($dbError) > 0){
							print_r($dbError); exit;
						}
						@unlink($tariffDetailCSVFile);
					}
					if(!empty($serviceRoutingCSVContent)){
						$serviceRoutingCSVFile = '../_assets/csv/customize_service_routing_csv_'.time().".csv";
						if(file_put_contents($serviceRoutingCSVFile,$serviceRoutingCSVContent)){
							$serviceRoutingSql = "LOAD DATA LOCAL INFILE '".$serviceRoutingCSVFile."' INTO TABLE `customized_services_routing`  CHARACTER SET 'utf8' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (                                      
                                                    country_id,
                                                    from_weight,
                                                    to_weight,
                                                    status,
                                                    customize_service_id,
                                                    service_id
                                                );";
							DbAccess3::runQueryWithError($serviceRoutingSql);
							@unlink($serviceRoutingCSVFile);
						}
					}
					if(!empty($countryTtimeCSVContent)){
						$countryTtimeCSVFile = '../_assets/csv/country_ttime_csv_'.time().".csv";
						if(file_put_contents($countryTtimeCSVFile,$countryTtimeCSVContent)){
							$tTimeSql = "LOAD DATA LOCAL INFILE '".$countryTtimeCSVFile."' IGNORE INTO TABLE `service_country_ttime` CHARACTER SET 'utf8' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (  
                                        id_country,
                                        id_service,
                                        transit_time
                                        );";
							DbAccess3::runQueryWithError($tTimeSql);
							@unlink($countryTtimeCSVFile);
						}
					}
					/* Customer Trariff Save */
					if($tariff_save_with_customer_tariff) {
						$tariffsObj = new Tariffs();
						$tariffsObj->setUserAccountId($user_account_id);
						$tariffsObj->setCarrierId($carrier_id);
						$tariffsObj->setServiceId($customized_service_id);
						$tariffsObj->setName($name);
						$tariffsObj->setStatus($status);
						$tariffsObj->setCurrencyId($currencyId);
						$tariffsObj->setTariffType('customer');
						$tariffsObj->setStartDate($start_date);
						$tariffsObj->setEndDate($end_date);
						$tariffsObj->setDescription("Save Customer Auto Tariff");
						$tariffsObj->setTariffTemplate('default');
						$tariffsObj->setDateAdded($date_added);
						$tariffsObj->setAddedBy($added_by);
						$tariffsObj->setDateUpdated($date_update);
						$tariffsObj->setUpdatedBy($update_by);
						$tariffsObj->save();
						$customer_tariff_id = $tariffsObj->getId();
						/* Customer Trarif Details */
						$fromZoneId = $fromZone->getId();
						TariffsDetails::deleteTarifsDetailByTarifId($customer_tariff_id);
						$customerTariffZonesData = $this->form_vars['zone_new'];
						$customerTariffFormulaData = $this->form_vars['zone_formula'];
						$customerTariffExtrasaData = $this->form_vars['zones_extra'];
						//$tariffsFormulaData = $this->form_vars['tariffs_formula'];
						foreach($customerTariffZonesData as $weight => $countries) {
							$weights = explode("/", $weight);
							$weight_from = $weights[0];
							$weight_to = $weights[1];
							foreach($countries as $countryId => $value) {
								$from_zone_id = $fromZoneId;
								$to_zone_id = $countryZoneArr[$countryId];
								$weight_from = $weight_from;
								$weight_to = $weight_to;
								$values = explode("/", $value);
								$user_weight_cost = $values[0];
								$user_peice_cost = isset($values[1]) ? $values[1] : 0.00;
								$tariffsFormula = "";
								if(isset($customerTariffFormulaData[$weight][$countryId]))
									$tariffsFormula = $customerTariffFormulaData[$weight][$countryId];
								$customerTariffExtras = [];
								if(isset($customerTariffExtrasaData[$weight][$countryId]))
									$customerTariffExtras = $customerTariffExtrasaData[$weight][$countryId];



								if($user_weight_cost != "" || $user_peice_cost != "") {
									$tariffsDetailObj = new TariffsDetails();
									$tariffsDetailObj->setTariffsId($customer_tariff_id);
									$tariffsDetailObj->setFromZoneId($from_zone_id);
									$tariffsDetailObj->setToZoneId($to_zone_id);
									$tariffsDetailObj->setWeightFrom($weight_from);
									$tariffsDetailObj->setWeightTo($weight_to);
									$tariffsDetailObj->setWeightCost($user_weight_cost);
									$tariffsDetailObj->setPieceCost($user_peice_cost);
									$tariffsDetailObj->setFormula($tariffsFormula);
									$tariffsDetailObj->save();
								}
							}
						}
						if($tariffLossSave) {
							/* Send Email tariff save in loss */
							$userAccountObj = new CustomerAccount($user_account_id);
							$userEmail = "";
							if ($userAccountObj->getEmail() != "") {
								$userEmail = $userAccountObj->getEmail();
							} else if ($userAccountObj->getAlternativeEmail() != "") {
								$userEmail = $userAccountObj->getAlternativeEmail();
							}
							$userName = $this->user->getFirstName(). ' ' . $this->user->getLastName();
							$customerTariffName = $name;
							$to = $userEmail;
							$htmlMessage = "<br><br>";
							$htmlMessage .= 'It is inform you that ' . $userName . ' has saved a tariff with loss values.';
							$htmlMessage .= "<br>Please see tariff details in system name as " . $customerTariffName;

							$subject = 'Save Tariff in Loss';
							$imageLogo = "";
							$logoImage = $userAccountObj->getLogo();
							if (trim($logoImage) != '' && trim($logoImage) != 'inner-logo.png') {
								$imageLogo = BASE_URL.'images/userlogo/' . $logoImage;
								if(!file_exists($imageLogo)) {
									$imageLogo = BASE_URL.'images/inner-logo.png';
								}
							} else {
								$imageLogo = BASE_URL.'images/inner-logo.png';
							}
							$team = (($userAccountObj->getCompany() != '') ? $userAccountObj->getCompany() : 'One World Express.');
							$dt = [
								'logo' => $imageLogo,
								'name' => 'Customer,',
								'message' => $htmlMessage,
								'company' => $team
							];
							$emailBody = file_get_contents ('loss_save_tariff_email_template.php');// read in the template file from above
							foreach ($dt as $key => $value){
								$emailBody = str_replace ("[$key]", $value, $emailBody);
							}
							$headers = 'From: Smart Track <smart@smarttrack.co>' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1';
							if($userEmail != "") {
								mail($to, $subject, $emailBody, $headers);
							}
						}
					}
				}

				if(count($countryToZoneNotFound) < count($selectedTariffService)) {
					$output['status'] = "success";
					$output['message'] = "Tariff Save Successfully";
					$output['customized_service_id'] = $customized_service_id;
					$output['tariff_id'] = $tarif_id;
					$countryNotFoundZone = [];
					if(count($countryToZoneNotFound) > 0) {
						foreach($countryToZoneNotFound as $countryId) {
							$countryObj = new Country($countryId);
							$countryNotFoundZone[] = $countryObj->getName() . " To Zone Not found";
						}
						$output['warning'] = implode("<br />", $countryNotFoundZone);
					}
				} else {
					$output['status'] = "error";
					$output['message'] = "To zone not found";
					$tariffDeteteObj = new Tariff();
					$tariffDeteteObj->deleteById($tarif_id);
				}
			} else {
				$output['status'] = "error";
				$output['message'] = "From zone not found";
			}

			echo json_encode($output,JSON_PARTIAL_OUTPUT_ON_ERROR);
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_tariff') {
			$fromCountry = $this->form_vars['from_country'];
			$_toCountry = $this->form_vars['to_country'];
			$_fromWeight = $this->form_vars['from_weight'];
			$_toWeight = $this->form_vars['to_weight'];
			$mailParcel = $this->form_vars['mail_parcel'];
			$mailType = $this->form_vars['mail_type'];
			$trackedUntracked = $this->form_vars['tracked_untracked'];
			$deliveryType = $this->form_vars['delivery_type'];
			$startDate = $this->form_vars['tariff_start_date'];
			$endDate = $this->form_vars['tariff_end_date'];
			$viewOnly = $this->form_vars['view_only'];
			$extraChargeTypeIds = [];
			if(isset($this->form_vars['extra_charge_type_id'])){
				$extraChargeTypeIds = $this->form_vars['extra_charge_type_id'];
			}
			if(!empty($startDate)){
				$sdt = new DateTime($startDate);
				$startDate = "'".$sdt->format("Y-m-d")."'";
			}else{
				$startDate = "NULL";
			}
			if(!empty($endDate)){
				$edt = new DateTime($endDate);
				$endDate = "'".$sdt->format("Y-m-d")."'";
			}else{
				$endDate = "NULL";
			}
			if($deliveryType != 'economy') {
				$deliveryType = 'priority';
			}
			if($mailType == 'all') {
				$mailType = "";
			}
			$mailOption = $this->form_vars['mail_option'];
			if($mailOption == 'all') {
				$mailOption = "";
			}
			if($mailParcel == 'all' || $mailParcel == 'courier') {
				$mailOption = "";
				$mailType = "";
				$trackedUntracked = "";
			}
			$includeServices = $this->form_vars['include_services'];
			$transitDays = $this->form_vars['transit_days'];
			$selectedCurrencyId = $this->form_vars['currency_id'];
			$currencyConversionRate = $this->form_vars['currency_conversion_rate'];
			$includePieceCost = false;
			if(isset($this->form_vars['include_piece_cost']) && $this->form_vars['include_piece_cost'] == 1) {
				$includePieceCost = true;
			}
			$includeKgCost = false;
			if(isset($this->form_vars['include_kg_cost']) && $this->form_vars['include_kg_cost'] == 1) {
				$includeKgCost = true;
			}
			$addManualWeightLimit = false;
			if(isset($this->form_vars['add_manual_weight_limit']) && $this->form_vars['add_manual_weight_limit'] != 'auto') {
				$addManualWeightLimit = true;
			}
			$cheapestValueShow = true;
			if(isset($this->form_vars['cheapest_value_show']) && $this->form_vars['cheapest_value_show'] == 1) {
				$cheapestValueShow = false;
			}
			$advanceSearchOption = true;
			if(isset($this->form_vars['advance_search_option']) && $this->form_vars['advance_search_option'] == 1) {
				$advanceSearchOption = false;
			}

			$lineHualFilter = new LinehaulFilter();
			$lineHualFilterObjs = $lineHualFilter->getList('*');
//            $lineHualServices = [];
			$lineHualDelivertyTypeServices = [];
			if(count($lineHualFilterObjs)) {
				foreach($lineHualFilterObjs as $lineHualService) {
					$type = 'all';
					if($lineHualService->getdeliveryType() != "") {
						$type = $lineHualService->getdeliveryType();
					}
//                    $lineHualServices[$lineHualService->getCountryId()] = $lineHualService->getPrice();
					$lineHualDelivertyTypeServices[$type][$lineHualService->getCountryId()] = $lineHualService->getPrice();
				}
			}
//            $lineHualServices = [
//                'STPKT01DD' => ['CZ' => 1.70,'SK' => 1.70,'HU' => 1.70,'RO' => 1.70,'BG' => 1.70,'PL' => 1.70,'SI' => 1.70,'HR' => 1.70,'UA' => 1.70],
//                'STVIVAEXP' => ['CH' => 1.50,'CZ' => 1.70,'AT' => 1.70,'PT' => 1.75],
//                'STYDL02VP' => ['GB' => 1.50],
//            ];

			$currencyObj = new Currency($selectedCurrencyId);
			$selectedCurrency = $currencyObj->getRightsymbol();

			$fromWeightArr = [];
			$toWeightArr = [];
			$toCountry = [];
			$_tariffFromWeight = [];
			$_tariffToWeight = [];
			$countryAllowedServices = [];
			if(count($_toCountry) > 0){
				foreach($_toCountry as $key => $countryArr){
					foreach($countryArr as $_countryId){
						$toCountry[] =  $_countryId;
						$fromWeightArr[$_countryId] = $_fromWeight[$key];
						$toWeightArr[$_countryId] = $_toWeight[$key];
					}
				}
			}

			$host = SETTING_DB_SERVER;
			$user = SETTING_DB_USER;
			$password = SETTING_DB_PASSWORD;
			$db = SETTING_DB_DATABASE;
			$mysqli = new mysqli($host, $user, $password, $db);
			/*
            if (!$mysqli->connect_errno) {
                $sql = "CALL user_country_services('" . $this->user->getUserAccountId() . "','" . $fromCountry . "','" . implode(",", $toCountry) . "','" . implode(",", $includeServices) . "','mail','NULL','NULL');";
                $result = $mysqli->query($sql);
            }
            */
			$countryServices = [];
			$UserServicesRouting = UserServicesRoutingFilter::getUserCountryServices($this->user->getUserAccountId(), $fromCountry, $toCountry,$includeServices,$mailParcel,$trackedUntracked,$transitDays,$mailType, $mailOption);
			if(count($UserServicesRouting) > 0){
				foreach($UserServicesRouting as $UserServicesRoutingObj){
					$countryServices[$UserServicesRoutingObj->getCountryId()][] = $UserServicesRoutingObj->getServiceId();
				}
			}
			foreach($countryServices as $__countryId => $srvs){
				$countryAllowedServices[$__countryId] = implode(",",$srvs);
			}
			$html = '';
			$weightLimits = [];
			$tariffDetails = [];
			$procedureServices = [];
			if (!empty($toCountry) && count($toCountry) > 0) {
				//$fromContryObj = new Country($fromCountry);
				/*$countryServices = [];
                $UserServicesRouting = UserServicesRoutingFilter::getUserCountryServices($this->user->getUserAccountId(), $fromCountry, $toCountry,$includeServices,$mailParcel,$trackedUntracked,$transitDays);
                if(count($UserServicesRouting) > 0){
                    foreach($UserServicesRouting as $UserServicesRoutingObj){
                        $countryServices[$UserServicesRoutingObj->getCountryId()][] = $UserServicesRoutingObj->getServiceId();
                    }
                }*/
				$tariffs = [];
				$minWeigt = $this->numberFormat(min($_fromWeight));
				$maxWeight = $this->numberFormat(max($_toWeight));

				/**********************************************************************/

				$tariffData = [];

				$jsonArray = [];
				$countryFilter = new CountryFilter();
				$countryFilter->addFilterIn("id",$toCountry);
				$countryObjs = $countryFilter->getColumnList("iso,name");
				if(!empty($countryObjs)) {
					foreach ($countryObjs as $countryObj) {
						$jsonArray['countries'][$countryObj->getId()] = ['iso' => strtolower($countryObj->getIso()), 'name' => $countryObj->getName()];
					}
				}
				if (!$mysqli->connect_errno) {
					$sql = "CALL get_pricing('".$this->user->getUserAccountId()."','".$fromCountry."','".implode(",",$toCountry)."','".json_encode($countryAllowedServices)."',".$startDate.",".$endDate.")";
					//$sql = "CALL get_pricing('2301','225','225,226','{\"225\":\"12,13,14,15,23,24,95,96,114,116,117,118,229,232,238,239,244,245,248,251,257,262,263,264,267,271,275,279,288,295,306,309,310,311,326,351,352,362,366,389,395,396,397,398,400,407,414,436,437,439,440,441,451,452,454,455,456,457,458,459,460,462,463,464,475,476,477,483,484,486,487,488,489,490,491,492,493,494,495,496,497,498,500,501,502,503,505,507,508,509,511,512,520,521,522,523,524,527,538,546,547,548,562,594,595,598,599,600,601,602,603,604,605,606,607,608,609,610,611,623,624\",\"226\":\"23,24,58,119,232,239,244,245,248,251,253,257,262,263,264,272,275,279,285,295,310,319,326,352,362,386,387,389,407,424,425,426,436,437,438,439,440,441,451,452,454,455,456,457,458,459,460,462,463,464,475,476,477,483,484,485,486,487,488,489,490,491,492,493,494,495,496,497,498,500,501,502,503,505,520,527,546,547,562,600,601,602,603,605,607,608,609,610,624\"}');";
					$result = $mysqli->query($sql);
					if (!$result) {
						$dbErrors = DbAccess3::$dbError;
						$outputArray["STATUS"] = "ERROR";
						if (count($dbErrors) > 0) {
							foreach ($dbErrors as $error) {
								$outputArray["ERROR"][] = "CALL failed: " . $error;
							}
						}
					} else if ($result->num_rows > 0) {
						while ($tariffObj = $result->fetch_object()) {
							$lineHual = 0.00;
							if(isset($lineHualDelivertyTypeServices[$deliveryType][$tariffObj->origin_country])) {
								$lineHualPrice = $lineHualDelivertyTypeServices[$deliveryType][$tariffObj->origin_country];
								$lineHual = $lineHualPrice;
							}
							$tariff = [];
							$tariff['tariff_name'] = $tariffObj->tariff_name;
							$tariff['to_country_id'] = $tariffObj->to_country_id;
							$tariff['tariff_detail_id'] = $tariffObj->tariff_detail_id;
							$tariff['service_name'] = $tariffObj->service_name;
							$tariff['service_code'] = $tariffObj->service_code;
							$tariff['service_id'] = $tariffObj->service_id;
							$tariff['max_length'] = $tariffObj->max_length;
							$tariff['max_width'] = $tariffObj->max_width;
							$tariff['max_height'] = $tariffObj->max_height;
							$tariff['maximum_allowed_dimension'] = $tariffObj->maximum_allowed_dimension;
							$tariff['maximum_dim_formula'] = $tariffObj->maximum_dim_formula;
							$tariff['validation_type'] = $tariffObj->validation_type;
							$tariff['mail_type'] = $tariffObj->mail_type;
							$tariff['weight_from'] = $tariffObj->weight_from;
							$tariff['weight_to'] = $tariffObj->weight_to;
							$tariff['weight_cost'] = $tariffObj->weight_cost;
							$tariff['piece_cost'] = $tariffObj->piece_cost;
							$tariff['formula'] = $tariffObj->formula;
							$tariff['carrier_logo'] = $tariffObj->carrier_logo;
							$tariff['to_zone_id'] = $tariffObj->to_zone_id;
							$tariff['to_zone_name'] = $tariffObj->to_zone_name;
							$tariff['currency_code'] = $tariffObj->currency_code;
							$tariff['transit_time'] = $tariffObj->transit_time;
							$tariff['line_hual'] = $lineHual;
							$tariff['extras'] = $tariffObj->extras;
							$tariffData[$tariffObj->to_country_id][] = $tariff;
						}
					}
				}
				/*********************************************************************/
				foreach ($toCountry as $key => $toCountryId) {
					//$toContryObj = new Country($toCountryId);
					$fromWeight = $fromWeightArr[$toCountryId];
					$toWeight = $toWeightArr[$toCountryId];
					/*$allowedServices = [];
                    $allowedServicesObj = ServiceFilter::getUserAccountServices($this->user->getUserAccountId(), $fromCountry, $toCountryId,"",$includeServices,$mailParcel,$trackedUntracked,$transitDays);
                    foreach ($allowedServicesObj as $serviceObj) {
                            $allowedServices[] = $serviceObj->getId();
                    }
                    if(isset($countryServices[$toCountryId])){
                        $allowedServices = $countryServices[$toCountryId];
                    }
                    if(count($allowedServices) > 0){
                        $tariffsDetailsFilter = new TariffsDetailsFilter();
                        $tariffsDetails = $tariffsDetailsFilter->getTariffDetails($this->user->getUserAccountId(), $allowedServices, $fromCountry, $toCountryId, $fromWeight, $toWeight);
                    }*/
					$tariffsDetails = [];
					if(isset($tariffData[$toCountryId])){
						$tariffsDetails = $tariffData[$toCountryId];
					}

					if (count($tariffsDetails) > 0) {
						foreach ($tariffsDetails as $tariffsDetail) {
							$tariffName = $tariffsDetail['tariff_name'];
							$tariffDetailId = $tariffsDetail['tariff_detail_id'];
							$tariffFromWeight = $tariffsDetail['weight_from'];
							$tariffToWeight = $tariffsDetail['weight_to'];
							$serviceId = $tariffsDetail['service_id'];
							$serviceCode = $tariffsDetail['service_code'];
							$serviceName = $tariffsDetail['service_name'];
							$currentWeightCost = $tariffsDetail['weight_cost'];
							$currenctPieceCost = $tariffsDetail['piece_cost'];
							$formula = $tariffsDetail['formula'];
							$tariffCurrency = $tariffsDetail['currency_code'];
							$transitTime = $tariffsDetail['transit_time'];
							$carrierLogo = $tariffsDetail['carrier_logo'];
							$zoneName =  $tariffsDetail['to_zone_name'];
							$currenctLineHual =  $tariffsDetail['line_hual'];
							$extras =  $tariffsDetail['extras'];

							$weightCost = $currentWeightCost;
							$pieceCost = $currenctPieceCost;
							$lineHual = $currenctLineHual;
							if (($tariffCurrency != $selectedCurrency)) {
								$weightCost = Currency::convertCurrency($tariffCurrency, $selectedCurrency, $currentWeightCost, $currencyConversionRate);
								$pieceCost = Currency::convertCurrency($tariffCurrency, $selectedCurrency, $currenctPieceCost, $currencyConversionRate);
							}
							if (("GBP" != $selectedCurrency)) {
								$lineHual = Currency::convertCurrency('GBP', $selectedCurrency, $currenctLineHual, $currencyConversionRate);
							}
							$weightCost = $this->numberFormat($weightCost);
							$pieceCost = $this->numberFormat($pieceCost);
							$lineHual = $this->numberFormat($lineHual);
							$serviceValidationType = $tariffsDetail['validation_type'];
							$mailType = $tariffsDetail['mail_type'];
							$maximumDimFormula = $tariffsDetail['maximum_dim_formula'];
							$maximumAllowedDimension = $tariffsDetail['maximum_allowed_dimension'];
							$dims = '';
							$thickness = '';
							if($serviceValidationType == 'mail') {
								if($mailType == 'letter' || $mailType == 'boxable') {
									$thickness = $tariffsDetail['max_width'];
								}
								if($maximumAllowedDimension != '') {
									$dims = $maximumAllowedDimension;
								}
							} else {
								$dims = $tariffsDetail['max_length'] . 'x' . $tariffsDetail['max_width'] . 'x' . $tariffsDetail['max_height'];
							}

							$tariffDetails[$toCountryId][$serviceId][] = [
								'tn' => $tariffName,
								'did' => $tariffDetailId,
								'fw' => $this->numberFormat($tariffFromWeight),
								'tw' => $this->numberFormat($tariffToWeight),
								'sid' => $serviceId,
								'sc' => $serviceCode,
								'tt' => $transitTime,
								'srv' => $serviceName,
								'wc' => $weightCost,
								'pc' => $pieceCost,
								//'tc' => $totalCost,
								'fm' => $formula,
								'cur' => $selectedCurrency,
								'cl' => $carrierLogo,
								'zn' => $zoneName,
								'lh' => $lineHual,
								'th' => $thickness,
								'dim' => $dims,
								'ext' => $extras
							];
							$_tariffFromWeight[] = $tariffFromWeight;
							$_tariffToWeight[] = $tariffToWeight;

//                            if ($tariffFromWeight < $minWeigt)
//                                $minWeigt = $tariffFromWeight;
//                            if ($tariffToWeight >= $maxWeight)
//                                $maxWeight = $tariffToWeight;
							$step = $tariffToWeight - $tariffFromWeight;
							if($step > 0)
								$tariffs[] = $step;
						}
					}
				}
				$minWeigt = min($_tariffFromWeight);
				$maxWeight = max($_tariffToWeight);
				if($minWeigt < $this->numberFormat(min($_fromWeight)))
					$minWeigt = $this->numberFormat(min($_fromWeight));

				if($maxWeight > $this->numberFormat(max($_toWeight)))
					$maxWeight = $this->numberFormat(max($_toWeight));

				$step = min($tariffs);
				if($step > $maxWeight){
					$step = $maxWeight;
				}

				if(count($tariffs) > 0 && !empty($step)) {
					for ($weight = $minWeigt; $weight <= $maxWeight;) {
						if($this->numberFormat(($weight + $step)) <= $maxWeight)
							$weightLimits[] = ['start' => $this->numberFormat($weight), 'end' => $this->numberFormat($weight + $step)];
						$weight = $weight + $step;
					}
				}
			}
//            echo "<pre>";print_r($weightLimits); echo "<pre>";
			//echo "<pre>";print_r($_tariffToWeight); echo "<pre>";
			$html = '';

			$tariffFromWeigth = [];
			$tariffToWeigth = [];
			$jsonArray['min_weight_limits'] = $minWeigt;
			$jsonArray['max_weight_limits'] = $maxWeight;
			$jsonArray['include_piece_cost'] = ($includePieceCost == true ? 1 : 0) ;
			$jsonArray['include_kg_cost'] = ($includeKgCost == true ? 1 : 0);
			$jsonArray['cheapest_value_show'] = ($cheapestValueShow == false ? 1 : 0);
			if($addManualWeightLimit) {
				$weightLimits = [];
				$manualWeightLimitsFroms = $this->form_vars['manual_weight_limit']['from_weight'];
				if(count($manualWeightLimitsFroms)) {
					foreach($manualWeightLimitsFroms as $limitKey => $manualWeightLimitsFrom) {
						$start = $manualWeightLimitsFrom;
						$end = isset($this->form_vars['manual_weight_limit']['to_weight'][$limitKey]) ? trim($this->form_vars['manual_weight_limit']['to_weight'][$limitKey]) : 0;
						$weightLimits[] = ['start' => $this->numberFormat($start), 'end' => $this->numberFormat($end)];
					}
				}
//                $manualWeightLimits = explode('<br />',nl2br($this->form_vars['manual_weight_limit']));
//                if(count($manualWeightLimits)) {
//                    foreach($manualWeightLimits as $manualWeightLimit) {
//                        $limts = explode('-',$manualWeightLimit);
//                        $start = isset($limts[0]) ? trim($limts[0]) : 0;
//                        $end = isset($limts[1]) ? trim($limts[1]) : 0;
//                        $weightLimits[] = ['start' => $this->numberFormat($start), 'end' => $this->numberFormat($end)];
//                    }
//                }
			}
			if (!empty($toCountry) && count($toCountry) > 0 && !empty($weightLimits) && count($weightLimits) > 0) {
				$jsonArray['weight_limits'] = $weightLimits;
				foreach ($toCountry as $_toCountryId) {
					//$_toContryObj = new Country($_toCountryId);
					//$jsonArray['countries'][$_toCountryId] = ['iso' => strtolower($_toContryObj->getIso()),'name' =>$_toContryObj->getName()];
					if (isset($tariffDetails[$_toCountryId])) {
						foreach ($weightLimits as $index => $limits) {
							foreach ($tariffDetails[$_toCountryId] as $srvId => $details) {
								if($limits['start'] >= $fromWeightArr[$_toCountryId] && $limits['end'] <= $toWeightArr[$_toCountryId]) {
								    foreach ($details as $countRec => $detail) {
										$found = false;
										if ($limits['start'] >= $detail['fw'] && $limits['end'] <= $detail['tw']) { // && $detail['weight_cost'] > 0
											$found = true;
										} else if ($limits['end'] == $detail['tw']) {
											$found = true;
										} /*else if ($limits['start'] >= $detail['fw'] || $limits['end'] <= $detail['tw']) {
                                            //echo $detail['to_weight']." = ".$limits['start']." - ".$limits['end']." - ".$detail['service']." - ".$detail['weight_cost']."<br />";
                                            $found = true;
                                        }*/
										if ($found) {
											if($detail['lh'] > 0) {
												$detail['lh'] = $this->numberFormat($detail['lh'] * $limits['end']);
											}
											$tariffFromWeigth[] = $limits['start'];
											$tariffToWeigth[] = $limits['end'];
											$totalCost = '';
											if(!empty($detail['fm'])){
												$findArry = ['Q','ITMCHR','CHRG', 'FRMW','W'];
												$replaceArry = ['1',$detail['pc'],$detail['wc'],$detail['fw'],$limits['end']];
												$formulaStr = str_replace($findArry,$replaceArry,$detail['fm']);

												eval("\$totalCost = $formulaStr;");
											}else{
												$totalCost = $detail['wc'];
											}
											$totalCostWithOutLine = $totalCost;
											if($detail['lh'] > 0) {
												$serObj = new Services($srvId);
												if($serObj->getOriginCountry() != $fromCountry) {
													$totalCost = $totalCost + $detail['lh'];
												}
											}
											$finalExtras = [];
											$extraCharges = 0.00;
											if($detail['ext'] != "") {
											    $extcharge = json_decode($detail['ext']);
												if(!empty($extcharge)) {
													foreach ($extcharge as $kk => $chargeObj) {
														$chargeTypeKey = $chargeObj->charge_type_key;
														$chargeTitle = $chargeObj->charge_title;
														$chargeType = $chargeObj->charge_type;
														$charge = $chargeObj->charge;
														$chargeFormula = $chargeObj->formula;
														$finalChargeCost = 0.00;
														$applyCharge = 0.00;
														if ($chargeType == "percentage") {
															$applyCharge = ($totalCost * $charge) / 100;
														} else {
															$applyCharge = $charge;
														}
														$finalChargeCost = $applyCharge;
														if ($chargeFormula != "") {
															$findArry = ['Q', 'ITMCHR', 'CHRG', 'FRMW', 'W'];
															$replaceArry = ['1', 1, $applyCharge, $limits['start'], $limits['end']];
															$formulaStr = str_replace($findArry, $replaceArry, $chargeFormula);
															eval("\$formulaChargeCost = $formulaStr;");
															$applyCharge = $formulaChargeCost;
														}

														$extraCharges += $applyCharge;

														$chargeObj->charge = $this->numberFormat($applyCharge);
														$finalExtras[] = $chargeObj;
														//$extcharge[$kk]->final_cost = $finalChargeCost;
													}
												}
                                                $totalCost = $totalCost + $extraCharges;
                                            }
											$detail['tcn'] = $this->numberFormat($totalCostWithOutLine);
											$detail['tc'] = $this->numberFormat($totalCost);
											$detail['ext'] = json_encode($finalExtras);
											$jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']][] = $detail;
											$tc = array_column($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']], 'tc');
											//$wc = array_column($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']], 'wc');
											//$pc = array_column($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']], 'pc');
											array_multisort($tc, SORT_ASC, $jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]);
											//break;
                                            if($viewOnly == 't3' && count($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]) > 3) {
                                                array_pop($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]);
                                            } else if($viewOnly == 't5' && count($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]) > 5)  {
                                                array_pop($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]);
                                            } else if($viewOnly == 'l3' && count($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]) > 3)  {
                                                array_shift($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]);
                                            } else if($viewOnly == 'l5' && count($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]) > 5)  {
                                                array_shift($jsonArray['tariff_data'][$_toCountryId][$limits['start'] . '-' . $limits['end']]);
                                            }
										}
									}
								}
							}
						}
					}
				}
			}else{
				//$html .= '<div class="text-center text-danger">No Service Available</div>';
			}
			$jsonArray['min_weight_limits'] = min($tariffFromWeigth);
			$jsonArray['max_weight_limits'] = max($tariffToWeigth);
			echo json_encode($jsonArray);
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'calculated_pricing_for_csv') {
			$downloadType = $this->form_vars['download_type'];
			$tariffJson = $this->form_vars['tariff_json'];
			$showPieceCost = $this->form_vars['show_piece_cost'];
			$showKgCost = $this->form_vars['show_kg_cost'];
			$selectedTariffService = json_decode($tariffJson);
			$prices = [];
			$services = [];
			if(count($selectedTariffService)) {
				foreach($selectedTariffService as $countryId => $rangeArr) {
					foreach ($rangeArr as $range => $serviceData) {
						$serviceDataArr = (array)$serviceData;
						$serviceId = 0;
						$serviceCost = 0;
						foreach($serviceDataArr as $_serviceId => $_serviceCost){
							$serviceId = $_serviceId;
							$serviceCost = $_serviceCost;
						}
						$newCostGet = explode('-',$serviceCost);
						$costValue = $newCostGet[0];
						$newCost = explode('/',$costValue);
						if ($this->form_vars['linehual']) {
                            $lineHual = !empty($newCostGet[5])? $newCostGet[5]:0;
                            if($showPieceCost == 1 && $showKgCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0]-$lineHual,
                                    'tcost' => $newCost[0],
                                    'kg_cost' => $newCost[1],
                                    'piece_cost' => trim($newCost[2]),
                                    'line_hual' => trim($lineHual)
                                ];
                            } else if($showKgCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0]- $lineHual,
                                    'tcost' => $newCost[0],
                                    'kg_cost' => $newCost[1],
                                    'line_hual' => trim($lineHual)
                                ];
                            } else if($showPieceCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0]- $lineHual,
                                    'tcost' => $newCost[0],
                                    'piece_cost' => $newCost[1],
                                    'line_hual' => trim($lineHual)
                                ];
                            } else {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0]- $lineHual,
                                    'tcost' => $newCost[0],
                                    'line_hual' => trim($lineHual)
                                ];
                            }
                        }else{
                            if($showPieceCost == 1 && $showKgCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0],
                                    'kg_cost' => $newCost[1],
                                    'piece_cost' => trim($newCost[2])
                                ];
                            } else if($showKgCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0],
                                    'kg_cost' => $newCost[1]
                                ];
                            } else if($showPieceCost == 1) {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0],
                                    'piece_cost' => $newCost[1]
                                ];
                            } else {
                                $prices[$countryId][$range] = [
                                    'cost' => $newCost[0]
                                ];
                            }
                        }

						$services[$countryId][$range] = $serviceId;
					}
				}
			}
			$csvStr = "";
			$csvHeaderStr = "Country/Weight,";
			$count = 0;
			if(count($prices)) {
				foreach($prices as $countryId => $rng) {
					$country = new Country($countryId);
					$csvStr .= $country->getName().",";
					foreach($rng as $fromToWeight => $cost) {
						$serviceId = $services[$countryId][$fromToWeight];
						$serviceObj = new Services($serviceId);
						$csvCost = '';
						$csvCostHeading = '';
                        if ($this->form_vars['linehual']) {
                            $csvCost .= $cost['line_hual'].',';
                            $csvCostHeading .= 'Linehual,';
                            if(isset($cost['piece_cost'])) {
                                $csvCost .= $cost['piece_cost'].',';
                                $csvCostHeading .= 'Piece Cost,';
                            }
                            if(isset($cost['kg_cost'])) {
                                $csvCost .= $cost['kg_cost'].',';
                                $csvCostHeading .= 'Weight Cost,';
                            }
                            if($downloadType == "both") {
                                $csvCost .= $cost['cost'].',';
                                $csvCostHeading .= 'Cost,';
                                $csvCost .= $cost['tcost'].',';
                                $csvCostHeading .= 'Total Cost,';
                            }
                            if($downloadType == "both") {
                                $csvStr .= $serviceObj->getName().','.$csvCost;
                            } else if($downloadType == "cost") {
                                $csvStr .= $csvCost;
                            } else if($downloadType == "service") {
                                $csvStr .= $serviceObj->getName().',';
                            }
                        }else{
                            if(isset($cost['piece_cost'])) {
                                $csvCost .= $cost['piece_cost'].',';
                                $csvCostHeading .= 'Piece Cost,';
                            }
                            if(isset($cost['kg_cost'])) {
                                $csvCost .= $cost['kg_cost'].',';
                                $csvCostHeading .= 'Weight Cost,';
                            }
                            if($downloadType == "both") {
                                $csvCost .= $cost['cost'].',';
                                $csvCostHeading .= 'Cost,';
                            }
                            if($downloadType == "both") {
                                $csvStr .= $serviceObj->getName().','.$csvCost;
                            } else if($downloadType == "cost") {
                                $csvStr .= $csvCost;
                            } else if($downloadType == "service") {
                                $csvStr .= $serviceObj->getName().',';
                            }
                        }


						/* header string */
						if($count == 0) {
							$csvHeaderStr .= $fromToWeight . ',' . $csvCostHeading;
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
			$fileName = "routiong_only_csv_" . time(). ".csv";
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=" . $fileName);
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $returnString;
			exit;
		}
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_calculated_pricing_with_linehual') {
            $returnString = $this->form_vars['csv_strings'];
            $fileName = "routiong_only_csv_" . time(). ".csv";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            exit;
        }

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_routing_excel') {
			$routingPricingIncludePieceCost = $this->form_vars['routing_pricing_include_piece_cost'];
			$routingPricingIncludeKgCost = $this->form_vars['routing_pricing_include_kg_cost'];
			$routingPricingDownload = $this->form_vars['routing_pricing_download'];
			$routingTariffId = $this->form_vars['routing_tariff_id'];
			$costomizeServiceId = $this->form_vars['cutomized_service_id_for_routing'];
			$importCsvFormat = $this->form_vars["import_csv_format"];
			$customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
			$customizedServicesRoutingFilter->addJoin("     country c", "c.id=csr.country_id");
			$customizedServicesRoutingFilter->addJoin("     services s", "s.id=csr.service_id");
			$customizedServicesRoutingFilter->addFieldFilter("      csr.customize_service_id", $costomizeServiceId);
			$customizedServicesRoutingFilter->addOrderBy("csr.country_id, csr.`to_weight`");
			$customizedServicesRoutingFilterObjs = $customizedServicesRoutingFilter->getColumnList('csr.*,s.name as service_name,c.name as country_name, s.code as service_code');
			$routingDataArray = [];
			$fromWeightArr = [];
			$toWeightArr = [];
			$stepArr = [];
			$countryArr = [];
			$prices = [];
			if($routingPricingDownload == 1) {
				$tariffCurrencyId = 0;
				$tariffCurrency = "GBP";
				if($routingTariffId > 0) {
					$tariffsObj = new Tariffs($routingTariffId);
					$tariffCurrencyId = $tariffsObj->getCurrencyId();
				}
				if($tariffCurrencyId == 0) {
					$currencyObj = new Currency($tariffCurrencyId);
					$tariffCurrency = $currencyObj->getRightsymbol();
				}
				$tariffsDetailsFilter = new TariffsDetailsFilter();
				$tariffsDetailsFilter->addJoinCarrierZone();
				$tariffsDetailsFilter->addFieldFilter("    td.tariffs_id", $routingTariffId);
				$tariffsDetailsFilterObjs = $tariffsDetailsFilter->getColumnList("cz.name as zone_name,td.to_zone_id,td.weight_from,td.weight_to,td.weight_cost,td.piece_cost", false,false);
				if(count($tariffsDetailsFilterObjs)) {
					foreach($tariffsDetailsFilterObjs as $tariffsDetailsFilterObj) {
						$weight = $tariffsDetailsFilterObj->getWeightFrom() . "-" . $tariffsDetailsFilterObj->getWeightTo();
						$prices[$tariffsDetailsFilterObj->getZoneName()][$weight] =  [
							'cost' => $tariffsDetailsFilterObj->getWeightCost()." ".$tariffCurrency,
							'piece_cost' => $tariffsDetailsFilterObj->getPieceCost()." ".$tariffCurrency,
						];
					}
				}
			}
			foreach($customizedServicesRoutingFilterObjs as $customizedServicesRoutingFilterObj) {
				$countryName = $customizedServicesRoutingFilterObj->getCountryName();
				$countryArr[] = $countryName;
				$fromWeightArr[] = $customizedServicesRoutingFilterObj->getFromWeight();
				$toWeightArr[] = $customizedServicesRoutingFilterObj->getToWeight();
				$stepArr[] = $customizedServicesRoutingFilterObj->getToWeight() - $customizedServicesRoutingFilterObj->getFromWeight();
				$weights = $customizedServicesRoutingFilterObj->getFromWeight().'-'.$customizedServicesRoutingFilterObj->getToWeight();
				$routingData = [];
				$srvPrice = "";
				if($routingPricingDownload == 1){
					$srvPrice = ",".cleanCsvCall($prices[$countryName][$weights]['cost']);
					if($routingPricingIncludePieceCost == 1) {
						$srvPrice .= ",".cleanCsvCall($prices[$countryName][$weights]['piece_cost']);
					}
				}
				if($importCsvFormat == 1){
					$routingData['service'] = cleanCsvCall($customizedServicesRoutingFilterObj->getServiceCode()).$srvPrice;
				}
				else
				{
					$routingData['service'] = cleanCsvCall($customizedServicesRoutingFilterObj->getServiceName()).$srvPrice;
				}

				$routingDataArray[$countryName][$weights] = $routingData;
			}

			$minWeight = min($fromWeightArr);
			$maxWeight = max($toWeightArr);
			$step = min($stepArr);

			$cellSep = ",";
			if($routingPricingDownload == 1) {
				$cellSep = ",Cost,";
				if($routingPricingIncludePieceCost == 1) {
					$cellSep = ",Cost,Piece Cost,";
				}
			}
			$weightArr = [];
			$serviceStr = '';
			$services = [];
			$excelData = [];
			$countryArr = array_unique($countryArr);
			foreach($countryArr as $country) {
				$countryRouting = $routingDataArray[$country];
				$services = [];
				for ($weight = $minWeight; $weight <= $maxWeight;) {
					if($this->numberFormat(($weight + $step)) <= $maxWeight){
						$startLimit = $this->numberFormat($weight);
						$endLimit = $this->numberFormat($weight + $step);
						$weightArr[] = $startLimit."-".$endLimit;
						$routingService = 0;
						foreach($countryRouting as $weights => $routing){
							$limits = explode("-",$weights);
							if($limits[0] <= $startLimit && $endLimit <= $limits[1]){
								$services[] = $routing['service'];
								$excelData[$country][$startLimit."-".$endLimit] = $routing['service'];
								$routingService = 1;
								break;
							}
						}
						if($routingService == 0) {
							$services[] = "";
							$excelData[$country][$startLimit."-".$endLimit] = "";
						}
					}
					$weight += $step;
				}
				$serviceStr .= cleanCsvCall($country).",".implode(",",$services)."\r\n";
			}

			$weightArr = array_unique($weightArr);
			$weightStr = '';
			$weightStr .= "Country/Weight,".implode($cellSep,$weightArr)."\r\n";

			$returnString = $weightStr . $serviceStr;

			$fileName = "routiong_excel_" . time(). ".csv";

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=" . $fileName);
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $returnString;

			exit;

			/*  make excel code  */
			$objPHPExcel = new PHPExcel();
			$customizeServiceObj = new Services($costomizeServiceId);
			$objPHPExcel->getActiveSheet()->SetCellValue("C1", $customizeServiceObj->getName() . " Service Routing");
			$rowNum=4;
			foreach($excelData as $country => $weights) {
				$colNum = 'A';
				$objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $country);
				if($rowNum == 4) {
					$objPHPExcel->getActiveSheet()->SetCellValue($colNum."3", "Country/Weight");
				}
				$colNum++;
				foreach($weights as $weight => $service) {
					if($rowNum == 4) {
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum."3", $weight);
					}
					$objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $service);
					$colNum++;
				}
				$rowNum++;
			}
			$fileName = "routiong_excel_" . time();
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
			die;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_carrier_services') {
			$carrierId = $this->form_vars['carrier_id'];
			$ServiceFilter = new ServiceFilter();
			$ServiceFilter->addCarrierFilter($carrierId);
			$ServiceFilter->addFieldFilter("    ser.is_customized", '1');
			if ($this->user->getUserType() != USER::USER_TYPE_ADMIN) {
				$carrierFilter = new CarrierFilter();
				$carrierFilter->addFilter("usr.user_account_id = '" . $this->user->getUserAccountId() . "'");
				$allowedServicesObj = $carrierFilter->getCarrierSetupList('DISTINCT s.id');
				$allowedServices = [];
				if (!empty($allowedServicesObj)) {
					foreach ($allowedServicesObj as $allowedService) {
						$allowedServices[] = $allowedService->getId();
					}
					$ServiceFilter->addFilter(' ser.id IN (' . implode(",", $allowedServices) . ')');
				} else {
					$ServiceFilter->addFilter(" ser.id = '-50000'");
				}
			}
			$servicesList = $ServiceFilter->getCarrierServicesList('ser.id, ser.name, ser.code');
			$output = '<option value="">Select Service</option>';
			if (count($servicesList) > 0) {
				foreach ($servicesList as $service) {
					$selected = '';
					$output .= '<option value="' . $service->getId() . '"' . $selected . '>' . $service->getName() . ']</option>';
				}
			}
			echo $output;
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_service_tariffs') {
			$serviceId = $this->form_vars['service_id'];
			$user_account_id = $this->user->getUserAccountId();
			$tariffFilter = new TariffsFilter();
			$tariffFilter->addFieldFilter("     service_id", $serviceId);
			$tariffFilter->addFieldFilter("     user_account_id", $user_account_id);
			$tariffFilter->addFieldFilter("     tariff_type", "supplier");
			$tariffFilterObjs = $tariffFilter->getList();

			$output = '<option value="">Select Tariff</option>';
			if (count($tariffFilterObjs) > 0) {
				foreach ($tariffFilterObjs as $tariffFilterObj) {
					$selected = '';
					$output .= '<option value="' . $tariffFilterObj->getId() . '"' . $selected . '>' . $tariffFilterObj->getName() . '</option>';
				}
			}
			echo $output;
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_tariff_details') {
			$tariffId = $this->form_vars['tariff_id'];
			$tariff = new Tariffs($tariffId);

			$output = [];
			$output['start_date'] = $tariff->getStartDate();
			$output['end_date'] = $tariff->getEndDate();
			echo json_encode($output);
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_curency_rate') {
			$currencyId = $this->form_vars['currency_id'];
			$currency = new Currency($currencyId);
			$rate = 0.00;
			if(!empty($currency->getCurrencyexchangerate())) {
				$rate = $currency->getCurrencyexchangerate();
			}
			$return = [
				'status' => 'success',
				'rate' => $rate,
			];
			echo json_encode($return);
			die;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_zone_country_option') {
			$zone = $this->form_vars['zone'];
			$countriesIds = [];
			if($zone != 'all' && $zone != 'rest_of_the_world') {
				$countryZonesMappingFilter = new CountryZonesMappingFilter();
				$countryZonesMappingFilter->where(['zone' => $zone]);
				$countryZonesMappingFilterObjs = $countryZonesMappingFilter->getList();
				if (count($countryZonesMappingFilterObjs)) {
					foreach ($countryZonesMappingFilterObjs as $countryZonesMappingFilterObj) {
						$countriesIds[] = $countryZonesMappingFilterObj->getCountryId();
					}
				}
			} else if($zone == 'rest_of_the_world') {
				$countryZonesMappingFilter = new CountryZonesMappingFilter();
				$countryZonesMappingFilterObjs = $countryZonesMappingFilter->getList();
				if (count($countryZonesMappingFilterObjs)) {
					foreach ($countryZonesMappingFilterObjs as $countryZonesMappingFilterObj) {
						$countriesIds[] = $countryZonesMappingFilterObj->getCountryId();
					}
				}
			} else {
				$country = new CountryFilter();
				$countryObjs = $country->getList();
				if(count($countryObjs)) {
					foreach ($countryObjs as $countryObj) {
						$countriesIds[] = $countryObj->getId();
					}
				}
			}
			$options = '';
			if(count($countriesIds)) {
				$countryFilter = new CountryFilter();
				$countryFilter->addFilterIn('id',$countriesIds);
				$countryFilterObjs = $countryFilter->getList();
				if(count($countryFilterObjs)) {
					foreach($countryFilterObjs as $countryFilterObj) {
						$iso = $countryFilterObj->getIso();
						$name = $countryFilterObj->getName();
						$id = $countryFilterObj->getId();
						$german_name = html_entity_decode($countryFilterObj->getGermanName());

						if (isset($_SESSION['lang']) && $_SESSION['lang'] == "de-DE")
							$display_name = $german_name;
						else
							$display_name = $name;

						if ($name == "")
							continue;
						$flag = "../assets/global/img/flags/".strtolower($iso).".png";
						if(!file_exists($flag))
							$flag = "../images/no_image_found_14_16.png";

						$options .= '<option value="' . $id . '" data-content="<img src=\''.$flag.'\' /> ' . ucfirst($display_name) . ' ">' . $display_name . '</option>';
					}
				}
			}
			$return = [
				'status' => 'success',
				'options' => $options,
			];
			echo json_encode($return);
			die;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_country_services_options') {
			$countryIds = $this->form_vars['country_id'];
			$trackedUntracked = $this->form_vars['tracked_untracked'];
			$mailParcel = $this->form_vars['mail_parcel'];
			$mailOption = $this->form_vars['mail_option'];
			$mailType = $this->form_vars['mail_type'];
            $serviceType = $this->form_vars['service_type'];
			$thickness = $this->form_vars['thickness'];
			$deliveryType = $this->form_vars['delivery_type'];
			$tariffStartDate = $this->form_vars['tariff_start_date'];
			$tariffEndDate = $this->form_vars['tariff_end_date'];

			if($mailParcel != "mail") {
				$thickness = "";
			}
			if($mailType != 'letter' && $mailType != "boxable") {
				$thickness = "";
			}

			$serviceFilter = new ServiceFilter();

			$tariffJoinDateFilter = '';
			if(!empty($tariffStartDate)){
				$dtFrm = new DateTime($tariffStartDate);
				$startDate = $dtFrm->format("Y-m-d");
				$tariffJoinDateFilter .= " AND t.`start_date` >= '".DbAccess3::escape($startDate)."'" ;
			}else{
				$tariffJoinDateFilter .= " AND t.`start_date` <= CURRENT_DATE" ;
			}
			if(!empty($tariffEndDate)){
				$dtTo = new DateTime($tariffEndDate);
				$endDate = $dtTo->format("Y-m-d");
				$tariffJoinDateFilter .= " AND t.`end_date` <= '".DbAccess3::escape($endDate)."'" ;
			}else{
				$tariffJoinDateFilter .= " AND t.`end_date` >= CURRENT_DATE" ;
			}
			$serviceFilter->addJoin('service_country_ttime AS sct',' ser.id = sct.id_service ');
			$serviceFilter->addJoin('tariffs AS t',"t.`service_id` = ser.`id` AND t.`tariff_type` = 'supplier' AND t.`status` = 1".$tariffJoinDateFilter);

			/*JOIN `tariffs` t
			ON t.`service_id` = ser.`id` AND t.`tariff_type` = 'supplier' AND t.`status` = 1 AND t.`start_date` >= CURRENT_DATE AND t.`end_date` <= CURRENT_DATE*/

			$serviceFilter->addFieldFilterIn('      sct.id_country',$countryIds);
			$serviceFilter->addFieldFilter('      ser.active',"1");
			$serviceFilter->addFieldFilter('      ser.is_customized',"0");
			if($trackedUntracked != "") {
				$serviceFilter->addFieldFilter('      ser.is_untrack',$trackedUntracked);
			}
            if($serviceType != "") {
                $serviceFilter->addFieldFilter('      ser.service_type',$serviceType);
            }
			if($mailParcel != "") {
				$serviceFilter->addFieldFilter('      ser.validation_type',$mailParcel);
				if($mailParcel == "mail") {
					if($mailOption != 'all') {
						$serviceFilter->addFieldFilter('      ser.mail_option',$mailOption);
					}
					if($mailType != 'all') {
						$serviceFilter->addFieldFilter('      ser.mail_type', $mailType);
					}
					if($mailType == "letter" || $mailType == "boxable") {
						if(!empty($thickness)) {
							$serviceFilter->addFieldWhereFilter('      ser.max_width', $thickness, '<=');
						}
					}
					if($trackedUntracked == 1) {
						if($deliveryType != 'all') {
							$serviceFilter->addFieldFilter('       ser.delivery_type', $deliveryType);
						}
					}
				}
			}
			$serviceFilterObjs = $serviceFilter->getColumnList('ser.*');
			$options = '';
			$doneIds = [];
			if(count($serviceFilterObjs)) {
				$displayValue = '';
				foreach($serviceFilterObjs as $serviceFilterObj) {
					$id = $serviceFilterObj->getId();
					if (!in_array($id,$doneIds)) {
						$doneIds[] = $id;
						if ($displayValue == 'code')
							$name = strtoupper($serviceFilterObj->getCode());
						else if ($displayValue == 'name_code')
							$name = ucfirst(strtolower($serviceFilterObj->getName())) . " [" . strtoupper($serviceFilterObj->getCode()) . "]";
						else
							$name = ucfirst(strtolower($serviceFilterObj->getName()));
						$logo = $serviceFilterObj->getCarrierLogo();
						$display_name = $name;

						$logoPath = '../images/carrierlogo/thumbnail/owe_16_' . $logo;
						if (!file_exists($logoPath)) {
							$logoPath = '../images/no_image_found_14_16.png';
						}

						if ($name == "")
							continue;

						$options .= '<option value="' . $id . '" data-content="<img src=\'' . $logoPath . '\' /> ' . $display_name . ' ">' . $display_name . '</option>';
					}
				}
			}
			$return = [
				'status' => 'success',
				'options' => $options,
			];
			echo json_encode($return);
			die;
		}
		/*
         * DataTable handlings
        */

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'manual_weight_limits') {
			$addManualWeightLimit = $this->form_vars['add_manual_weight_limit'];
			$mailParcel = $this->form_vars['mail_parcel'];
			$toWeight = trim($this->form_vars['to_weight']) != "" ? trim($this->form_vars['to_weight']) : 0;
			$fromWeight = trim($this->form_vars['from_weight']) != "" ? trim($this->form_vars['from_weight']) : 0;
			$count = 0;
			$html = "";
			$html .= '<tr class="clone_row">';
			$html .= '<td>';
			$html .= '<input type="number" class="form-control from_weight_limit" id="from_weight_limit_0" name="manual_weight_limit[from_weight][0]">';
			$html .= '</td>';
			$html .= '<td>';
			$html .= '<input type="number" class="form-control to_weight_limit" id="to_weight_limit_0" name="manual_weight_limit[to_weight][0]">';
			$html .= '</td>';
			$html .= '<td>';
			$html .= '<button type="button" class="btn btn-success add_more_weight_limit"><i class="fa fa-plus"></i></button>';
			$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
			$html .= '</td>';
			$html .= '</tr>';
			if($addManualWeightLimit == "standard") {
				//$fromWeight = 0.00;
				if($mailParcel == "mail") {
					$html = "";
					$totalLimits = 8;

					$searchedWeight = $toWeight - $fromWeight;

					if($toWeight > 0)
						$totalLimits = $searchedWeight / 0.25;


					if($totalLimits > 8)
						$totalLimits = 8;

					$count = $totalLimits -1;

					for($i=0; $i<$totalLimits; $i++) {
						$toWeight = $fromWeight + 0.25;
						$html .= '<tr class="clone_row">';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control from_weight_limit" id="from_weight_limit_'.$i.'" name="manual_weight_limit[from_weight]['.$i.']" value="'.$fromWeight.'" >';
						$html .= '</td>';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control to_weight_limit" id="to_weight_limit_'.$i.'" name="manual_weight_limit[to_weight]['.$i.']" value="'.$toWeight.'" >';
						$html .= '</td>';
						$html .= '<td>';
						if ($totalLimits == ($i + 1)) {
							$html .= '<button type="button" class="btn btn-success add_more_weight_limit"><i class="fa fa-plus"></i></button>';
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						} else {
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						}
						$html .= '</td>';
						$html .= '</tr>';
						$fromWeight = $fromWeight +  0.25;
						//$i++;
					}
				} else if($mailParcel == "courier") {
					$html = "";
					//$totalLimits = 62;
					/*if($toWeight < $totalLimits)
						$totalLimits = $toWeight;*/
					$totalLimits = 0;
					$searchedWeight = 0;
					if($fromWeight < 1){
						$searchedWeight = 1 - $fromWeight;
						if($searchedWeight > 0)
							$totalLimits += $searchedWeight / 0.25;
					}
					if($toWeight > 1) {
						$searchedWeight = ($toWeight - $fromWeight) - $searchedWeight;
						if($searchedWeight > 0)
							$totalLimits += $searchedWeight / 0.50;
					}
					if($totalLimits > 62)
						$totalLimits = 62;

					$count = $totalLimits -1;
					for($i=0; $i<$totalLimits; $i++) {
						if($fromWeight >= 1) {
							$toWeight = $fromWeight + 0.50;
						} else {
							$toWeight = $fromWeight + 0.25;
						}
						$html .= '<tr class="clone_row">';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control from_weight_limit" id="from_weight_limit_'.$i.'" name="manual_weight_limit[from_weight]['.$i.']" value="'.$fromWeight.'" >';
						$html .= '</td>';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control to_weight_limit" id="to_weight_limit_'.$i.'" name="manual_weight_limit[to_weight]['.$i.']" value="'.$toWeight.'" >';
						$html .= '</td>';
						$html .= '<td>';
						if ($totalLimits == ($i + 1)) {
							$html .= '<button type="button" class="btn btn-success add_more_weight_limit"><i class="fa fa-plus"></i></button>';
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						} else {
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						}
						$html .= '</td>';
						$html .= '</tr>';
						if($fromWeight >= 1) {
							$fromWeight = $fromWeight +  0.50;
						} else {
							$fromWeight = $fromWeight +  0.25;
						}
					}
				}
			}elseif($addManualWeightLimit == "manual") { // && $this->form_vars['manual_limits'] != "bespoke"
				$manual_limits = trim($this->form_vars['manual_limits']);
				if($manual_limits != "") {
					$html = "";
					$totalLimits = 30;
					if ($mailParcel == "mail")
						$totalLimits = 2;

					if($toWeight < $totalLimits)
						$totalLimits = $toWeight;

					$fromWeight = 0.00;
					for ($i = 0; $i < $totalLimits; $i = $i + $manual_limits) {

						$__toWeight = $fromWeight + $manual_limits;
						if($__toWeight > $totalLimits)
							$__toWeight = $totalLimits;

						$html .= '<tr class="clone_row">';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control from_weight_limit" id="from_weight_limit_' . $i . '" name="manual_weight_limit[from_weight][' . $i . ']" value="' . $fromWeight . '" >';
						$html .= '</td>';
						$html .= '<td>';
						$html .= '<input type="number" class="form-control to_weight_limit" id="to_weight_limit_' . $i . '" name="manual_weight_limit[to_weight][' . $i . ']" value="' . $__toWeight . '" >';
						$html .= '</td>';
						$html .= '<td>';
						if ($totalLimits == ($i + $manual_limits)) {
							$html .= '<button type="button" class="btn btn-success add_more_weight_limit"><i class="fa fa-plus"></i></button>';
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						} else {
							$html .= '<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>';
						}
						$html .= '</td>';
						$html .= '</tr>';
						$fromWeight = $fromWeight + $manual_limits;
					}
				}
			}
			$return = [
				'status' => 'success',
				'html' => $html,
				'count' => $count
			];
			echo json_encode($return);
			die;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'load_tariff_details') {
			$tariffDetailData = [];
			$zones = [];
			$tariffJsonArr = $this->form_vars['tariff_json'];

			$extraChargeTypeIds = $this->form_vars['extra_charge_type_id'];
			$consignmentChargesTypes = "";
			$consignmentChargesTypesFilter = new ConsignmentChargesTypesFilter();
			$consignmentChargesTypesFilter->addFieldFilter('      cct.charges_key','FUEL_CHARGES');
			if(!empty($extraChargeTypeIds) > 0) {
				$consignmentChargesTypesFilter->addFieldOrInFilter('      cct.id', $extraChargeTypeIds);
			}
			$consignmentChargesTypes = $consignmentChargesTypesFilter->getColumnList('cct.title, cct.charges_key');
			$checkPieceExist = 0;
			foreach($tariffJsonArr as $countryId => $weightArr) {
				foreach($weightArr as $weight => $arr) {
					foreach($arr as $serviceId => $price) {
						//echo "<br />"; echo $price; echo "<br />";
						$_weight = explode('-',$weight);
						$fromWeight = $_weight[0];
						$toWeight = $_weight[1];
						$weightFinal = $fromWeight . "/" . $toWeight;
						$a = explode('-',$price);
						$_price = $a[0];
						$_trasit_time = $a[1];

						$curr = "";
						if(isset($a[2])){
							$curr = $a[2];
						}
						$_formula = "";
						if(isset($a[3])) {
							$_formula = $a[3];
						}
						$extras = "";
						if(isset($a[4])){
							$extras = $a[4];
						}
						$priceArr = explode('/',$_price);
						$totalCost = $priceArr[0];
						$weightcost = isset($priceArr[1]) ? $priceArr[1] : $totalCost;
						$pieceCost = isset($priceArr[2]) ? $priceArr[2] : 0.00;
						if($pieceCost > 0) {
							$checkPieceExist = 1;
						}
						$tariffDetailData[$weightFinal][$countryId] = [
							'service_id' => $serviceId,
							'weight_cost' => $weightcost,
							'peice_cost' => $pieceCost,
							'extra' => $extras,
							'total_cost' => $totalCost,
							'formula' => str_replace("_","-",$_formula),
							'cur' => $curr,
							'cost' => $weightcost.'/'.$pieceCost
						];
					}
				}
				if (!in_array($countryId, $zones)) {
					$zones[] = $countryId;
				}
				sort($zones);
			}

			$countryOptions = '<option value="">Select Country</option>';
			$tdhtml = '';
			$tdhtml .= '<div class="table-responsive">';
			$tdhtml .= '<table class="table table-bordered fixed custom_table" id="tarif_csv_data">';
			$tdhtml .= '<thead>';
			$tdhtml .= '<tr>';
			$tdhtml .= '<th class="bg-blue" style="color: #fff;"><b>Weight/Zone</b><input type="hidden" name="check_piece_exist" id="check_piece_exist" value="'.$checkPieceExist.'" ></th>';
			foreach ($zones as $k => $zone) {
				$toZone = new Country($zone);
				$tdhtml .= '<th class="bg-blue"><b style="color: #fff;">' . $toZone->getName() . '</b><input type="hidden" name="zonename[]" value="' . $toZone->getName() . '"></th>';
				$countryOptions .= '<option value="'.$toZone->getId().'">'.$toZone->getName().'</option>';
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
						$tdhtml .= '<td class="service_column">Service</td>';
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">Item</td>';
						}
						$tdhtml .= '<td class="value_wd">Kilo</td>';
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">Total</td>';
						}
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">Item<br />Value</td>';
							$tdhtml .= '<td class="value_wd">Item<br />Margin</td>';
						}
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">Kilo<br />Value</td>';
							$tdhtml .= '<td class="value_wd">Kilo<br />Margin</td>';
						} else {
							$tdhtml .= '<td class="value_wd">Value</td>';
							$tdhtml .= '<td class="value_wd">Margin</td>';
						}
						if(!empty($consignmentChargesTypes)){
							foreach($consignmentChargesTypes as $consignmentCharges){
								$tdhtml .= '<td class="value_wd">'.$consignmentCharges->getTitle().'</td>';
							}
						}
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">';
							$tdhtml .= 'Item';
							$tdhtml .= '</td>';
						}
						$tdhtml .= '<td class="value_wd">Kilo</td>';
						if($checkPieceExist > 0) {
							$tdhtml .= '<td class="value_wd">Total</td>';
						}
						$tdhtml .= '</tr>';
						if(!empty($consignmentChargesTypes)) {
							$tdhtml .= '<tr>';
							$tdhtml .= '<td>&nbsp;</td>';
							if($checkPieceExist > 0) {
								$tdhtml .= '<td>&nbsp;</td>';
								$tdhtml .= '<td>&nbsp;</td>';
								$tdhtml .= '<td>&nbsp;</td>';
								$tdhtml .= '<td>&nbsp;</td>';
							}
							$tdhtml .= '<td>&nbsp;</td>';
							$tdhtml .= '<td>&nbsp;</td>';
							$tdhtml .= '<td>&nbsp;</td>';
							foreach ($consignmentChargesTypes as $consignmentCharges) {
								$tdhtml .= '<td><input type="text" class="custom_field_show zone_extra_all zone_extra_all_'.$consignmentCharges->getId().'" data-zone_id="'.$k.'" data-extra_id="'.$consignmentCharges->getId().'" /></td>';
							}
							if($checkPieceExist > 0) {
								$tdhtml .= '<td>&nbsp;</td>';
								$tdhtml .= '<td>&nbsp;</td>';
							}
							$tdhtml .= '<td>&nbsp;</td>';
							$tdhtml .= '</tr>';
						}
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
					$popOverHtml = "<table class='table table-bordered table-striped table-condensed'>";
					//$popOverHtml += '<tr><td><strong>Cost</strong></td><td>'+serviceDetail.tcn +' '+ serviceDetail.cur +'</td></tr>';
					if($dt['weight_cost']) {
						$popOverHtml .= '<tr><td><strong>Weight Cost</strong></td><td>'.$dt['weight_cost']." ".$dt['cur'].'</td></tr>';
					}
					if(isset($dt['peice_cost'])) {
						$popOverHtml .= '<tr><td><strong>Item Cost</strong></td><td>'.$dt['peice_cost']." ".$dt['cur'].'</td></tr>';
					}
					if(isset($dt['extra']) && !empty($dt['extra'])) {
						$extra = str_replace("'",'"',$dt['extra']);
						$ext = json_decode($extra);
						foreach ($ext as $extk => $extV){
							$popOverHtml .= '<tr><td><strong>'.$extV->charge_title.'</strong></td>';
							$popOverHtml .= '<td>'.$extV->charge." ".$dt['cur'].'</td></tr>';
						}
					}
					$popOverHtml .= '<tr><td><strong>Total Cost</strong></td><td>'.$dt['total_cost']." ".$dt['cur'].'</td></tr>';
					$popOverHtml .= '</table>';
					$tdhtml .= '<td>';
					$idValue = str_replace(".","_",$weight);
					$tdhtml .= '<table class="inner_table">';
					$tdhtml .= '<tr>';
					$tdhtml .= '<td class="service_column">';
					$service = new Services($dt['service_id']);
					$tdhtml .= ucwords(strtolower($service->getName()));
					$tdhtml .= '<input type="hidden" name="service_validation_type[' . $weight . '][' . $k . ']" id="service_validation_type_' . str_replace("/","_",$idValue) . '_' . $k.'" value="'.$service->getValidationType().'" >';
					$tdhtml .= '<input type="hidden" name="supplier_tariff_formula[' . $weight . '][' . $k . ']" id="supplier_tariff_formula_' . str_replace("/","_",$idValue) . '_' . $k.'" value="'.$dt['formula'].'" >';
					$tdhtml .= '</td>';
					if($checkPieceExist > 0) {
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<input type="text" name="zones_piece_cost[' . $weight . '][' . $k . ']" id="zone_piece_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="custom_field_show zone_piece_price_' . str_replace("/", "_", $idValue) . '" data-zone_id="' . $k . '" value="' . $dt['peice_cost'] . '" readonly="readonly">';
						$tdhtml .= '</td>';
					}
					$tdhtml .= '<td class="value_wd">';
					$tdhtml .= '<input type="hidden" name="zones[' . $weight . '][' . $k . ']" id="zone_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="zone_price_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" value="' . $dt['cost'] . '" readonly="readonly">';
					if($checkPieceExist > 0) {
						$tdhtml .= '<input type="text" name="zones_weight_cost[' . $weight . '][' . $k . ']" id="zone_weight_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="custom_field_show zone_weight_price_' . str_replace("/", "_", $idValue) . '" data-zone_id="' . $k . '" value="' . $dt['weight_cost'] . '" readonly="readonly">';
					}else{
						$tdhtml .= '<input type="text" name="zones_weight_cost[' . $weight . '][' . $k . ']" id="zone_weight_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="popovers custom_field_show zone_weight_price_' . str_replace("/", "_", $idValue) . '" data-zone_id="' . $k . '" value="' . $dt['weight_cost'] . '" readonly="readonly" data-html="true" data-container="body" data-trigger="hover" data-content="'.$popOverHtml.'" data-original-title="Cost Details">';
					}
					$tdhtml .= '</td>';
					if($checkPieceExist > 0) {
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<input type="text" name="zones_total_cost[' . $weight . '][' . $k . ']" id="zone_total_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="popovers custom_field_show zone_total_price_' . str_replace("/", "_", $idValue) . '" data-zone_id="' . $k . '" value="' . $dt['total_cost'] . '" readonly="readonly" data-html="true" data-container="body" data-trigger="hover" data-content="'.$popOverHtml.'" data-original-title="Cost Details">';
						$tdhtml .= '</td>';
					}
					if($checkPieceExist > 0) {
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<input type="text" name="zones_piece_value[' . $weight . '][' . $k . ']" id="zone_piece_value_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_piece_value zone_price_piece_value_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'" data-weight_str="'. str_replace("/","_",$idValue).'"  value="" >';
						$tdhtml .= '</td>';
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<input type="text" name="zones_piece_margin[' . $weight . '][' . $k . ']" id="zone_piece_margin_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_piece_margin zone_price_piece_margin_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'"  data-weight_str="'. str_replace("/","_",$idValue).'"  value="" >';
						$tdhtml .= '</td>';
					}
					$tdhtml .= '<td class="value_wd">';
					$tdhtml .= '<input type="text" name="zones_value[' . $weight . '][' . $k . ']" id="zone_value_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_value zone_price_value_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'" data-weight_str="'. str_replace("/","_",$idValue).'"  value="" >';
					$tdhtml .= '</td>';
					$tdhtml .= '<td class="value_wd">';
					$tdhtml .= '<input type="text" name="zones_margin[' . $weight . '][' . $k . ']" id="zone_margin_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show custom_margin zone_price_margin_' . str_replace("/","_",$idValue) . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'"  data-weight_str="'. str_replace("/","_",$idValue).'"  value="" >';
					$tdhtml .= '</td>';
					if(!empty($consignmentChargesTypes)){
						foreach($consignmentChargesTypes as $consignmentCharges){
							if(isset($dt['extra']) && !empty($dt['extra'])) {
								$extra = str_replace("'",'"',$dt['extra']);
								$ext = json_decode($extra);
								$extraValue = '';
								foreach ($ext as $_extk => $_extV){
									if($_extV->charge_type_key == $consignmentCharges->getChargesKey()){
										$extraValue = $_extV->charge;
									}
								}
							}
							$tdhtml .= '<td class="extra_charge_'.$consignmentCharges->getId().'">';
							$tdhtml .= '<input type="text" name="zones_extra[' . $weight . '][' . $k . ']['.$consignmentCharges->getId().']" id="zone_extra_input_' . str_replace("/","_",$idValue) . '_' . $k . '_'.$consignmentCharges->getId().'" class="custom_field_show custom_extra customer_extra_'.$k.'_'.$consignmentCharges->getId().' customer_extra_'.$consignmentCharges->getId().' zone_price_extra_' . str_replace("/","_",$idValue) . '_' . $k . '" data-zone_id="'.$k.'" data-id_str="'.str_replace("/","_",$idValue) . '_' . $k .'" data-weight="'.$weight.'"  data-weight_str="'. str_replace("/","_",$idValue).'"  value="'.$extraValue.'" readonly="readonly" />';
							$tdhtml .= '</td>';
						}
					}
					if($checkPieceExist > 0) {
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<div class="custom_field_show" id="zone_new_piece_' . str_replace("/", "_", $idValue) . '_' . $k . '" readonly="readonly"></div>';
						$tdhtml .= '</td>';
					}
					$tdhtml .= '<td class="value_wd">';
					$tdhtml .= '<input type="hidden" name="zone_new[' . $weight . '][' . $k . ']" id="zone_new_input_' . str_replace("/","_",$idValue) . '_' . $k . '" class="custom_field_show calculated_new_customer_tariff" value="" readonly="readonly">';
					$tdhtml .= '<input type="hidden" name="zone_formula[' . $weight . '][' . $k . ']" id="zone_formula_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="custom_field_show" value="">';
					$tdhtml .= '<div class="custom_field_show" id="zone_new_kg_' . str_replace("/","_",$idValue) . '_' . $k . '" readonly="readonly"></div>';
					$tdhtml .= '</td>';
					if($checkPieceExist > 0) {
						$tdhtml .= '<td class="value_wd">';
						$tdhtml .= '<input type="hidden" name="zone_new_total_cost[' . $weight . '][' . $k . ']" id="zone_new_total_input_' . str_replace("/", "_", $idValue) . '_' . $k . '" class="custom_field_show" value="" readonly="readonly">';
						$tdhtml .= '<div class="custom_field_show" id="zone_new_total_' . str_replace("/", "_", $idValue) . '_' . $k . '" readonly="readonly"></div>';
						$tdhtml .= '</td>';
					}
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

			$extrasFilters = '';
			if(!empty($consignmentChargesTypes)){
				$extrasFilters .= '<fieldset class="fieldset" style="margin-top: 10px;">
										<legend>Tariff Extra Charges:</legend>
										<div class="col-md-12">
											<div class="row">';

				foreach($consignmentChargesTypes as $consignmentCharges){
					$extrasFilters .= '<div class="col-md-2">
											<div class="form-group">
												<label>'.$consignmentCharges->getTitle().'</label>
												<input type="text" class="form-control tariff-extra-filter" value="" data-extra_id="'.$consignmentCharges->getId().'" id="extra_filter_'.$consignmentCharges->getId().'" />																								
											</div>
										</div>';
				}
				$extrasFilters .= '<div class="col-md-2">
										<div class="form-group">
											<label>&nbsp;</label><br />
											<button type="button" id="apply_extra_charges_only" class="btn btn-success">Apply</button>
										</div>	
									</div>';
				$extrasFilters .= '			</div>
										</div>
									</fieldset>';
			}
			$output['tariff_detail'] =  $tdhtml;
			$output['country_options'] =  $countryOptions;
			$output['tariff_extras_filter'] =  $extrasFilters;


			echo json_encode($output);
			exit;
		}

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_pricing_excel') {
			$dt = $this->form_vars['customer_tariff'];
			$dataObj = json_decode($dt);
			$checkPieceExist = $dataObj->check_piece_exist;
			$supplierTariffDetails = [];
			$customerNewTariffDetails = [];
			$weights = $dataObj->weight;
			$zonesArr = json_decode(json_encode($dataObj->zones), true);
			$zonesNewArr = json_decode(json_encode($dataObj->zone_new), true);
			if($checkPieceExist) {
				$zonesTotalCost = json_decode(json_encode($dataObj->zones_total_cost), true);
				$zoneNewTotalCost = json_decode(json_encode($dataObj->zone_new_total_cost), true);
			} else {
				$zonesTotalCost = json_decode(json_encode($dataObj->zones_weight_cost), true);
				$zoneNewTotalCost = json_decode(json_encode($dataObj->zone_new), true);
			}
			foreach($weights as $weight) {
				$zones = $zonesArr[$weight];
				$zonesNew = $zonesNewArr[$weight];
				foreach($zones as $zoneId => $cost) {
					$totalCost = $zonesTotalCost[$weight][$zoneId];
					$newTotalCost = $zoneNewTotalCost[$weight][$zoneId];
					$costArr = explode('/',$cost);
					$totalCostArr = explode('/',$totalCost);
					$supplierTariffDetails[$zoneId][$weight] = [
						'weight_cost' => (isset($costArr[0]) ? $costArr[0] : '0.00'),
						'piece_cost' => (isset($costArr[1]) ? $costArr[1] : '0.00'),
						'total_cost' => (isset($totalCostArr[0]) ? $totalCostArr[0] : '0.00'),
					];
					$costNew = $zonesNew[$zoneId];
					$costNewArr = explode('/',$costNew);
					$newTotalCostArr = explode('/',$newTotalCost);
					$customerNewTariffDetails[$zoneId][$weight] = [
						'weight_cost' => (isset($costNewArr[0]) ? $costNewArr[0] : '0.00'),
						'piece_cost' => ((isset($costNewArr[1]) && $costNewArr[1] != 'NaN') ? $costNewArr[1] : '0.00'),
						'total_cost' => (isset($newTotalCostArr[0]) ? $newTotalCostArr[0] : '0.00'),
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
				$stColNum = 'A';
				$colNum = 'A';
				$checkZoneNumber = 1;
				foreach ($supplierTariffDetails as $zoneToId => $weights) {
					$carrierZoneObj = new Country($zoneToId);
					$rowNum = 1;
					$colHead = $stColNum;
					$colHead++;
					$colHead++;
					$colHead++;
					$colHead++;
					if($checkPieceExist > 0) {
						$colHead++;
						$colHead++;
						$colHead++;
						$colHead++;
					}
					if (fmod($checkZoneNumber,2) == 0) {
						$objPHPExcel->getActiveSheet()->getStyle($stColNum . '1:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBoxTwo);
					} else {
						$objPHPExcel->getActiveSheet()->getStyle($stColNum . '1:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBox);
					}
					$objPHPExcel->getActiveSheet()->mergeCells($stColNum . '1:' . $colHead . $rowNum);
					$objPHPExcel->getActiveSheet()->SetCellValue($stColNum . '1', trim($carrierZoneObj->getName()));
					$die = false;
					foreach ($weights as $fromToWeight => $cost) {
						$weightArr = explode('/',$fromToWeight);
						$fromWeight = $weightArr[0];
						$toWeight = $weightArr[1];
						$colNum = $stColNum;
						if ($rowNum == 1) {
							/* Set Mid Heading */
							if($checkPieceExist > 0) {
								$rowNum++;
								$midHead = $stColNum;
								$objPHPExcel->getActiveSheet()->getStyle($midHead . $rowNum . ':' . $colHead . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->SetCellValue($midHead . $rowNum, "");
								$midHead++;
								$midHeadMergFrom = $midHead;
								$midHead++;
								$midHead++;
								$objPHPExcel->getActiveSheet()->mergeCells($midHeadMergFrom. $rowNum . ':'.$midHead . $rowNum);
								$objPHPExcel->getActiveSheet()->getStyle($midHeadMergFrom . $rowNum . ':' . $midHead . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->SetCellValue($midHeadMergFrom . $rowNum, "Cost Prices");
								$midHead++;
								$midHeadMergFrom = $midHead;
								$midHead++;
								$midHead++;
								$objPHPExcel->getActiveSheet()->mergeCells($midHeadMergFrom. $rowNum . ':'.$midHead . $rowNum);
								$objPHPExcel->getActiveSheet()->getStyle($midHeadMergFrom . $rowNum . ':' . $midHead . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->SetCellValue($midHeadMergFrom . $rowNum, "Sell Prices");
								$midHead++;
								$midHeadMergFrom = $midHead;
								$midHead++;
								$objPHPExcel->getActiveSheet()->mergeCells($midHeadMergFrom. $rowNum . ':'.$midHead . $rowNum);
								$objPHPExcel->getActiveSheet()->getStyle($midHeadMergFrom . $rowNum . ':' . $midHead . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->SetCellValue($midHeadMergFrom . $rowNum, "Profit/Margin");
							}
							/* Set Heading */
							$rowNum++;
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
							$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('8');
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Weight");
							$colNum++;
							if($checkPieceExist > 0) {
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Item");
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
								$colNum++;
							}
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
							$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Kilo");
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
							$colNum++;
							if($checkPieceExist > 0) {
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Total");
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
								$colNum++;
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('12');
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Item");
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
								$colNum++;
							}
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
							$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('12');
							if($checkPieceExist > 0) {
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Kilo");
							} else {
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Total");
							}
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
							$colNum++;
							if($checkPieceExist > 0) {
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
								$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('12');
								$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Total");
								$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
								$colNum++;
							}
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
							$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Value");
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
							$colNum++;
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
							$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('16');
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "%");
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
							$colNum++;
						}
						$colNum = $stColNum;
						$supplierWeightCost = ($cost['weight_cost'] != "") ? $cost['weight_cost'] : 0.00;
						$supplierPieceCost = ($cost['piece_cost'] != "") ? $cost['piece_cost'] : 0.00;
						$supplierTotalCost = ($cost['total_cost'] != "") ? $cost['total_cost'] : 0.00;
						$newCost = $customerNewTariffDetails[$zoneToId][$fromToWeight];
						$tariffWeightCost = ($newCost['weight_cost'] != "") ? $newCost['weight_cost'] : 0.00;
						$tariffPieceCost = ($newCost['piece_cost'] != "") ? $newCost['piece_cost'] : 0.00;
						$tariffTotalCost = ($newCost['total_cost'] != "") ? $newCost['total_cost'] : 0.00;
						$objRichTotalTextPercenatge = new PHPExcel_RichText();
						$objRichTotalTextValue = new PHPExcel_RichText();
						$profitLossPercentage = "0.00";
						$profitLossTotalCostAmount = '0.00';
						if ($supplierTotalCost <  $tariffTotalCost) {
							$profit = 0;
							$profit =  $tariffTotalCost - $supplierTotalCost;
							$profitLossTotalCostAmount = trim($this->numberFormat($profit));
							$tariffProftValueObj = $objRichTotalTextValue->createTextRun($profitLossTotalCostAmount);
							$tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
							if($tariffTotalCost > 0) {
								$profitLossPercentage = $this->numberFormat((($profitLossTotalCostAmount / $tariffTotalCost ) * 100 ));
							}
							$tariffProftLossObj = $objRichTotalTextPercenatge->createTextRun($profitLossPercentage.'%');
							$tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
						} else if ($supplierTotalCost > $tariffTotalCost) {
							$loss = 0;
							$loss = $supplierTotalCost - $tariffTotalCost;
							$profitLossTotalCostAmount = trim($this->numberFormat($loss));
							$tariffProftValueObj = $objRichTotalTextValue->createTextRun($profitLossTotalCostAmount);
							$tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
							if($tariffTotalCost > 0) {
								$profitLossPercentage = $this->numberFormat((($profitLossTotalCostAmount / $tariffTotalCost) * 100));
							}
							$tariffProftLossObj = $objRichTotalTextPercenatge->createTextRun($profitLossPercentage.'%');
							$tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
						} else {
							$tariffProftValueObj = $objRichTotalTextValue->createTextRun($profitLossTotalCostAmount);
							$tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
							$tariffProftLossObj = $objRichTotalTextPercenatge->createTextRun($profitLossPercentage.'%');
							$tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
						}
						$rowNum++;
						$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($weightBox);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($toWeight));
						$colNum++;
						if($checkPieceExist > 0) {
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($supplierPieceCost));
							$colNum++;
						}
						$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($supplierWeightCost));
						$colNum++;
						if($checkPieceExist > 0) {
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($supplierTotalCost));
							$colNum++;
						}
						if($checkPieceExist > 0) {
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($tariffPieceCost));
							$colNum++;
						}
						$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $tariffWeightCost);
						$colNum++;
						if($checkPieceExist > 0) {
							$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
							$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $tariffTotalCost);
							$colNum++;
						}
						$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($borderBox);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichTotalTextValue);
						$colNum++;
						$objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($borderBox);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichTotalTextPercenatge);
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

		if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_demo_tariff_excel') {
			$tariffCurrencyId = $this->form_vars['tariff_currency_id'];
			$dt = $this->form_vars['customer_tariff'];
			$dataObj = json_decode($dt);

			$newCosts = json_decode(json_encode($dataObj->zone_new_total_cost), true);
			if(empty($newCosts)) {
				$newCosts = json_decode(json_encode($dataObj->zone_new), true);
			}
			$weights = $dataObj->weight;
			$data = [];
			$zoneIds = [];
			if(count($weights)) {
				foreach($weights as $weight) {
					$wt = explode('/',$weight);
					$toWeight = $wt[1];
					$currentCostZone = $newCosts[$weight];
					foreach($currentCostZone as $zoneId => $totalCost) {
						$totalCostArr = explode("/",$totalCost);
						$totalCost = isset($totalCostArr[0]) ? $totalCostArr[0] : $totalCost;
						$data[$toWeight][$zoneId] = [
							'total_cost' => $totalCost
						];
						if(!in_array($zoneId,$zoneIds)) {
							$zoneIds[] =  $zoneId;
						}
						ksort($data[$toWeight]);
					}
				}
			}
			sort($zoneIds);
			$heading = [];
			if(count($zoneIds)) {
				$heading[] = "weight";
				foreach($zoneIds as $zoneId) {
					$carrierZone = new Country($zoneId);
					$heading[] = cleanCsvCall($carrierZone->getName());
				}
			}
			$bold = array(
				'font' => array(
					'bold' => true,
				)
			);
			$styleWeightColoumn = array(
				'font' => array(
					'size' => 10,
					'name' => 'Calibri',
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					)
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'eaeaea')
				),
			);
			$styleZoneReport = array(
				'font' => array(
					'bold' => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size' => 12,
					'name' => 'Calibri',
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					)
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => '5f5f7d')
				),
			);
			$styleServiceForReport = array(
				'font' => array(
					'bold' => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size' => 16,
					'name' => 'Calibri',
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					)
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => '1D2555')
				),
			);
			$styleForReport = array(
				'font' => array(
					'size' => 10,
					'name' => 'Calibri',
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					)
				),
			);
			$styleEndReport = array(
				'font' => array(
					'size' => 10,
					'name' => 'Calibri',
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					)
				),
			);
			$objPHPExcel = new PHPExcel();
			if (!empty($data)) {
				$currency = new Currency($tariffCurrencyId);
				$objPHPExcel->getActiveSheet()->setShowGridlines(false);
				$rowNum = 1;
				$colNum = 'A';
				foreach ($heading as $h) {
					$cell_name = $colNum.$rowNum;
					$objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleZoneReport);
					$objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth(16);
					$objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
					$objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
					$colNum++;
				}
				$rowNum=2;
				foreach($data as $toWeight => $zones) {
					$colNum = 'A';
					$objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleWeightColoumn);
					$objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $toWeight);
					$colNum++;
					foreach($zones as $zoneId => $cost) {
						$totalCost = $cost['total_cost'];
						$objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
						$objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost);
						$objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
						$colNum++;
					}
					$rowNum++;
				}
			}
			$fileName = "demo_tariff_excel_" . time();
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
		}

	}

	function getColNo($colLetters){
		$limit = 5; //apply max no. of characters
		$colLetters = strtoupper($colLetters); //change to uppercase for easy char to integer conversion
		$strlen = strlen($colLetters); //get length of col string
		if($strlen > $limit)return "Column too long!"; //may catch out multibyte chars in first pass
		preg_match("/^[A-Z]+$/",$colLetters,$matches); //check valid chars
		if(!$matches)return "Invalid characters!"; //should catch any remaining multibyte chars or empty string, numbers, symbols
		$it = 0; $vals = 0; //just start off the vars
		for($i=$strlen-1;$i>-1;$i--){ //countdown - add values from righthand side
			$vals += (ord($colLetters[$i]) - 64 ) * pow(26,$it); //cumulate letter value
			$it++; //simple counter
		}
		return $vals - 1; //this is the answer
	}

	function getLetter($c){
		$c = intval($c);
		if ($c <= 0)  {
			return '';
		}
		$letter = '';
		while($c != 0){
			$p = ($c - 1) % 26;
			$c = intval(($c - $p) / 26);
			$letter = chr(65 + $p) . $letter;
		}
		return $letter;
	}

	function objToArray($obj, &$arr){

		if(!is_object($obj) && !is_array($obj)){
			$arr = $obj;
			return $arr;
		}

		foreach ($obj as $key => $value)
		{
			if (!empty($value))
			{
				$arr[$key] = array();
				objToArray($value, $arr[$key]);
			}
			else
			{
				$arr[$key] = $value;
			}
		}
		return $arr;
	}

	protected function checkCarrierZoneHasCountry($carrier_id,$service_id,$country_id) {
		$carrierZoneFilter = new CarrierZonesFilter();
		$carrierZoneFilter->addJoin("    carrier_zones_countries czc", "czc.carrier_zone_id = cz.id");
		$carrierZoneFilter->addFieldFilter("    cz.carrier_id", $carrier_id);
		$carrierZoneFilter->addFieldFilter("    cz.service_id", $service_id);
		$carrierZoneFilter->addFieldFilter("    czc.country_id", $country_id);
		$carrierZoneFilterObj = $carrierZoneFilter->getList();
		if(count($carrierZoneFilterObj)) {
			return true;
		} else {
			return false;
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
			.whole_tariff_price_tbl {
				border: 1px solid #b9bdc2;
				margin-bottom: 0px;
			}
			.whole_tariff_price_tbl thead tr th {
				border: 1px solid #b9bdc2;
				text-align: center;
				font-size: 14px !important;
				font-weight: bold;
			}
			.whole_tariff_price_tbl tbody tr td {
				border: 1px solid #b9bdc2;
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
		<script src="../assets/global/plugins/serializeForm.js" type="text/javascript"></script>
		<script type="text/javascript">
			var refreshSn = function (){
				var time = 300000; // 5 mins
				setTimeout(function (){
					$.ajax({
						type: "POST",
						url: "get_pricing.php",
						data: {'func':'refresh_session'},
						cache: false,
						complete: function () {
							refreshSn();
						}
					});
				},time);
			};
			var countriesTariffs = [];
			var tariffCountry = {};
			$(document).ready(function () {
				refreshSn();
				$("#_add_manual_weight_limit_container").hide();
				$(".initial-button").hide();
				var elindex = 0;
				if (jQuery('#elindex-hardcode').length > 0) {
					elindex = jQuery('#elindex-hardcode').val();
				}
				$(document).on('click', '.add-more-tariff-pricing-keys', function () {
					elindex++;
					var clone = $(this).parent().parent().clone();
					var zoneTo = $(clone).find('.zone-to-id').attr('name');
					var zoneToId = $(clone).find('.zone-to-id').attr('id');
					var weightFrom = $(clone).find('.weight-from').attr('name');
					var weightFromId = $(clone).find('.weight-from').attr('id');
					var weightTo = $(clone).find('.weight-to').attr('name');
					var weightToId = $(clone).find('.weight-to').attr('id');

					var tariffMarginWg = $(clone).find('.tariff-margin-wg').attr('name');
					var tariffMarginWgId = $(clone).find('.tariff-margin-wg').attr('id');
					var tariffMarginIt = $(clone).find('.tariff-margin-it').attr('name');
					var tariffMarginItId = $(clone).find('.tariff-margin-it').attr('id');

					var tariffMarginTypeWg = $(clone).find('.tariff-margin-type-wg .selectpicker').attr('name');
					var tariffMarginWgTypeWgId = $(clone).find('.tariff-margin-type-wg .selectpicker').attr('id');
					var tariffMarginTypeIt = $(clone).find('.tariff-margin-type-it .selectpicker').attr('name');
					var tariffMarginWgTypeItId = $(clone).find('.tariff-margin-type-it .selectpicker').attr('id');

					var tariffLineHaulWg = $(clone).find('.tariff-line-haul-wg').attr('name');
					var tariffLineHaulWgId = $(clone).find('.tariff-line-haul-wg').attr('id');
					var tariffLineHaulIt = $(clone).find('.tariff-line-haul-it').attr('name');
					var tariffLineHaulItId = $(clone).find('.tariff-line-haul-it').attr('id');

					// var marginWeightCost = $(clone).find('.margin-weight-cost').attr('name');
					// var marginWeightCostId = $(clone).find('.margin-weight-cost').attr('id');
					// var marginPieceCost = $(clone).find('.margin-piece-cost').attr('name');
					// var marginPieceCostId = $(clone).find('.margin-piece-cost').attr('id');

					$(this).remove();
					$(clone).find('.select2-container').remove();

					$(clone).find('input').val('');
					$(clone).find('.zone-to-id').attr('name', zoneTo.replace(/\d+/, elindex));
					$(clone).find('.zone-to-id').attr('id', zoneToId.replace(/\d+/, elindex));
					$(clone).find('.weight-from').attr('name', weightFrom.replace(/\d+/, elindex));
					$(clone).find('.weight-from').attr('id', weightFromId.replace(/\d+/, elindex));
					$(clone).find('.weight-to').attr('name', weightTo.replace(/\d+/, elindex));
					$(clone).find('.weight-to').attr('id', weightToId.replace(/\d+/, elindex));

					$(clone).find('.tariff-margin-wg').attr('name', tariffMarginWg.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-wg').attr('id', tariffMarginWgId.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-it').attr('name', tariffMarginIt.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-it').attr('id', tariffMarginItId.replace(/\d+/, elindex));

					$(clone).find('.tariff-margin-type-wg .selectpicker').attr('name', tariffMarginTypeWg.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-type-wg .selectpicker').attr('id', tariffMarginWgTypeWgId.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-type-it .selectpicker').attr('name', tariffMarginTypeIt.replace(/\d+/, elindex));
					$(clone).find('.tariff-margin-type-it .selectpicker').attr('id', tariffMarginWgTypeItId.replace(/\d+/, elindex));

					$(clone).find('.tariff-line-haul-wg').attr('name', tariffLineHaulWg.replace(/\d+/, elindex));
					$(clone).find('.tariff-line-haul-wg').attr('id', tariffLineHaulWgId.replace(/\d+/, elindex));
					$(clone).find('.tariff-line-haul-it').attr('name', tariffLineHaulIt.replace(/\d+/, elindex));
					$(clone).find('.tariff-line-haul-it').attr('id', tariffLineHaulItId.replace(/\d+/, elindex));

					// $(clone).find('.margin-weight-cost').attr('name', marginWeightCost.replace(/\d+/, elindex));
					// $(clone).find('.margin-weight-cost').attr('id', marginWeightCostId.replace(/\d+/, elindex));
					// $(clone).find('.margin-piece-cost').attr('name', marginPieceCost.replace(/\d+/, elindex));
					// $(clone).find('.margin-piece-cost').attr('id', marginPieceCostId.replace(/\d+/, elindex));
					// $(clone).find('.margin-weight-cost').attr('value', "weight");
					// $(clone).find('.margin-piece-cost').attr('value', "piece");

					$(clone).find('.bootstrap-select.tariff-margin-type-wg').replaceWith(function () {
						return $('#margin_type_wg_' + elindex, this);
					});
					$(clone).find('#margin_type_wg_' + elindex).selectpicker('refresh');
					$(clone).find('.bootstrap-select.tariff-margin-type-it').replaceWith(function () {
						return $('#margin_type_it_' + elindex, this);
					});
					$(clone).find('#margin_type_it_' + elindex).selectpicker('refresh');
					// $(clone).find('.bootstrap-select.tariff-line-haul-type-it').replaceWith(function () {
					// 	return $('#line_haul_type_it_' + elindex, this);
					// });
					// $(clone).find('#line_haul_type_it_' + elindex).selectpicker('refresh');
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


				$("#customer_pricing_portlet").hide();
				if ($('.date-picker').length > 0) {
					//init date pickers
					$('.date-picker').datepicker({
						autoclose: true
					});
				}
				$(".initial-button").hide();
				if ($('.date-picker').length > 0) {
					//init date pickers
					$('.date-picker').datepicker({
						autoclose: true
					});
				}
				$(".to_country").on("changed.bs.select", function() {
					var selectedId = $(this).attr("id");
					var selected = $(this).val();
					var toCountryLimit = <?php echo $this->toCountryLimit; ?>;
					if(selected){
						var count = selected.length;
						if(count > toCountryLimit) {
							//$(this).selectpicker('deselectAll');
							$('#'+selectedId+' option').each(function(index,val){
								if(index >= toCountryLimit){
									$(this).removeAttr("selected");
								}
							});
							$('#'+selectedId).selectpicker('refresh');
							swal({
								title:"Success!",
								text:"you can only select "+toCountryLimit+" countries, first "+toCountryLimit+" are selected.",
								type: "warning",
								html:true
							});
						}
					}
				});
				var elindex = 0;
				if (jQuery('#elindex-hardcode').length > 0) {
					elindex = jQuery('#elindex-hardcode').val();
				}
				$(document).on('click', '.add_more_pricing_range', function () {
					elindex++;
					var clone = $(this).parent().parent().parent().clone();
					var toCountry = $(clone).find('.to_country .bs-select').attr('name');
					var toCountryId = $(clone).find('.to_country .bs-select').attr('id');
					var fromWeight = $(clone).find('.from_weight').attr('name');
					var fromWeightId = $(clone).find('.from_weight').attr('id');
					var toWeight = $(clone).find('.to_weight').attr('name');
					var toWeightId = $(clone).find('.to_weight').attr('id');

					$(this).remove();
					$(clone).find('.select2-container').remove();

					$(clone).find('input').val('');
					$(clone).find('.to_country .bs-select').attr('name', toCountry.replace(/\d+/, elindex));
					$(clone).find('.to_country .bs-select').attr('id', toCountryId.replace(/\d+/, elindex));
					$(clone).find('.from_weight').attr('name', fromWeight.replace(/\d+/, elindex));
					$(clone).find('.from_weight').attr('id', fromWeightId.replace(/\d+/, elindex));
					$(clone).find('.to_weight').attr('name', toWeight.replace(/\d+/, elindex));
					$(clone).find('.to_weight').attr('id', toWeightId.replace(/\d+/, elindex));

					$(clone).find('.bootstrap-select.to_country').replaceWith(function () {
						return $('#to_country_' + elindex, this);
					});
					$(clone).find('#to_country_' + elindex).selectpicker('refresh');

					$(clone).find('button.remove_pricing_range').show();
					$(clone).find('button.remove_pricing_range').removeClass('initial-button');
					$(clone).appendTo($('.pricing_range_container'));
				});
				$(document).on('click', '.remove_pricing_range', function () {
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
								if ($('.pricing_range_container .remove_pricing_range').length > 1) {
									$(el).parent().parent().remove();
									if ($('.add_more_pricing_range').length == 0) {
										var addMore = $(el).parent().find('.add_more_pricing_range').clone();
										$('.pricing_range_container .remove_pricing_range').last().parent().prepend(addMore);
									}
								} else {
									$(el).parent().parent().find('input').val('');
								}
							}
						});
				});
				$("#save_tariff_modal").hide();
				$("#customer_pricing").hide();
				$("#download_tariff_csv").hide();
				$("#tariff_portlet").hide();
				$("#download_routing").hide();
				$("#download_routing_with_pricing").hide();

				var elindexweight = 0;
				if (jQuery('#elindex_hardcode_weight').length > 0) {
					elindexweight = jQuery('#elindex_hardcode_weight').val();
				}
				$(document).on('click', '.add_more_weight_limit', function () {
					elindexweight++;
					var clone = $(this).parent().parent().clone();
					var fromWeightLimit = $(clone).find('.from_weight_limit').attr('name');
					var fromWeightLimitId = $(clone).find('.from_weight_limit').attr('id');
					var toWeightLimit = $(clone).find('.to_weight_limit').attr('name');
					var toWeightLimitId = $(clone).find('.to_weight_limit').attr('id');
					$(this).remove();
					$(clone).find('input').val('');
					$(clone).find('.from_weight_limit').attr('name', fromWeightLimit.replace(/\d+/, elindexweight));
					$(clone).find('.from_weight_limit').attr('id', fromWeightLimitId.replace(/\d+/, elindexweight));
					$(clone).find('.to_weight_limit').attr('name', toWeightLimit.replace(/\d+/, elindexweight));
					$(clone).find('.to_weight_limit').attr('id', toWeightLimitId.replace(/\d+/, elindexweight));

					$(clone).find('button.remove_weight_limit').show();
					$(clone).find('button.remove_weight_limit').removeClass('initial-button');
					$(clone).appendTo($('.weight_limit_container'));
				});
				$(document).on('click', '.remove_weight_limit', function () {
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
								if ($('.weight_limit_container .remove_weight_limit').length > 1) {
									$(el).parent().parent().remove();
									if ($('.add_more_weight_limit').length == 0) {
										var addMore = $(el).parent().find('.add_more_weight_limit').clone();
										$('.weight_limit_container .remove_weight_limit').last().parent().prepend(addMore);
									}
								} else {
									$(el).parent().parent().find('input').val('');
								}
							}
						});
				});

				$("#btn_search_tariff").click(function () {
					countriesTariffs = [];
					tariffCountry = {};
					var is_error = false;
					var mail_parcel = $('#mail_parcel').val();
					$('.to_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select to weight", "error");
							is_error = true;
							return false;
						}
					});
					if(mail_parcel == "mail") {
						$('.to_weight').each(function(i, obj) {
							if($(this).val() > 2){
								swal("Error","Please add to weight not more than 2 kg", "error");
								is_error = true;
								return false;
							}
						});
					}
					$('.from_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select from weight", "error");
							is_error = true;
							return false;
						}
					});
					$('.to_country').each(function(i, obj) {
						if(obj.value !== "undefined") {
							if(obj.value == ''){
								swal("Error","Please select to country", "error");
								is_error = true;
								return false;
							}
						}
					});
					if($("#currency_id").val() == ''){
						swal("Error","Please select currency", "error");
						is_error = true;
						return false;
					}
					if($("#from_country").val() == ''){
						swal("Error","Please select from country", "error");
						is_error = true;
						return false;
					}
					var selected = $('#include_services').find("option:selected");
					if(selected.length == 0){
						swal("Error","Please select service", "error");
						is_error = true;
						return false;
					}
					if(is_error == false){
						$("#customer_pricing").hide();
						$("#download_tariff_csv").hide();
						$("#save_tariff_modal").hide();
						$("#tariff_response_head").html('');
						$("#tariff_response").html('<tr><td>loading...</td></tr>');
						var filterStr = '';
						$.blockUI();
						$.post('get_pricing.php', $("#frm_price_search").serialize(), function (response) {
							$.unblockUI();
							var weightLimits = response.weight_limits;
							var countries = response.countries;
							var tariffData = response.tariff_data;
							$("#tariff_response").html('');
							var html = '';
							//html += '<thead class="flip-content">';
							html += '<tr><th>';
							html += '<input type="hidden" name="min_weight" id="tmp_min_weight" value="'+response.min_weight_limits+'" />';
							html += '<input type="hidden" name="max_weight" id="tmp_max_weight" value="'+response.max_weight_limits+'" />';
							html += '<span rel="tooltip" title="" style="cursor: pointer;" data-original-title="Country / Weight Limits">Country / Weight Limits</span></th>';
							var headTitle = '';
							var headTooltipPieceCost = 'Item Cost';
							var headTooltipWeightCost = 'Weight Cost';
							var headTooltipTotalCost = 'Total Cost';
							if(response.include_piece_cost == 1){
								headTitle += 'I / ';
							}
							if(response.include_kg_cost == 1){
								headTitle += 'W / ';
							}
							headTitle += 'T';

							$.each(weightLimits,function(i, v){
								html += '<th style="border-right: 0px;min-width: 300px;">';
								html += '<table class="table-bordered table-condensed flip-content"><tr><th style="width: 55%;text-align: center;">'+v.start+' - '+v.end+'</th>';
								if(response.include_piece_cost == 1) {
									html += '<th class="text-center" style="border-left: 0px;width: 15%;"><span rel="tooltip" title="" style="cursor: pointer;" data-original-title="' + headTooltipPieceCost + '">I</span></th>';
								}
								if(response.include_kg_cost == 1) {
									html += '<th class="text-center" style="border-left: 0px;width: 15%;"><span rel="tooltip" title="" style="cursor: pointer;" data-original-title="' + headTooltipWeightCost + '">W</span></th>';
								}
								html += '<th class="text-center" style="border-left: 0px;width: 15%;"><span rel="tooltip" title="" style="cursor: pointer;" data-original-title="'+headTooltipTotalCost+'">T</span></th>';
								html += '</tr></table></th>';
							});
							html += '</tr>';
							//html += '</thead><tbody>';
							$("#tariff_response_head").html(html);
							var ptc = 1;
							var jsonStr = '';
							var firstLetters = [];
							var cheapestValue = response.cheapest_value_show;
							$.each(countries,function(countryId, countryIso){
								var countryFirstChar = countryIso.name.substring(0,1).toLowerCase();
								html = '';
								if(tariffData != undefined && tariffData[countryId]){
									if(typeof(countriesTariffs[countryFirstChar]) == "undefined") {
										firstLetters.push(countryFirstChar);
										countriesTariffs[countryFirstChar] = [];
									}
									jsonStr += (jsonStr != ""? ',':'')+'"'+countryId+'":';
									html += '<tr><th style="text-align:center;"><img src="../assets/global/img/flags/'+countryIso.iso.toLowerCase()+'.png" alt="" /> '+countryIso.name.toUpperCase()+'</th>';
									var jsonWeightStr = '';
									$.each(weightLimits,function(indx, limits){
										html += '<td><table class="table-bordered table-condensed flip-content price_table" style="min-width:215px;">';
										var limit = limits.start+'-'+limits.end;
										if(tariffData[countryId][limit]){
											var is_checked = false;
											var serviceCount = tariffData[countryId][limit].length;
											if(serviceCount == 1)
												cheapestValue = 0;
											$.each(tariffData[countryId][limit],function(index,serviceDetail){
												var extras = "";
												var transitTime = 'N/A';
												if (serviceDetail.tt > 0) {
													if (serviceDetail.tt == 1) {
														transitTime = 'Next day delivery';
													}else {
														transitTime = serviceDetail.tt+' days';
													}
												}
												var costHtml = serviceDetail.tc;
												if(response.include_kg_cost == 1){
													costHtml += '/'+serviceDetail.wc;
												}
												if(response.include_piece_cost == 1){
													costHtml += '/'+serviceDetail.pc;
												}
												var _extras = "";
												if(serviceDetail.ext != "")
													_extras = serviceDetail.ext.replaceAll('"',"'");
												if(is_checked == false && cheapestValue == index){
													jsonWeightStr += (jsonWeightStr != ""? ',':'')+'"'+limit+'":{"'+serviceDetail.sid+'":"'+costHtml+'-'+serviceDetail.tt+'-'+serviceDetail.cur+'-'+serviceDetail.fm.replace("-","_")+'-'+_extras+'-'+serviceDetail.lh+'"}';
												}
												var popOverHtml = "<table class='table table-bordered table-striped table-condensed'>";
												popOverHtml += '<tr><td><strong>Tariff</strong></td><td>'+serviceDetail.tn+'</td></tr>';
												if(serviceDetail.th != "" && serviceDetail.th > 0) {
													popOverHtml += '<tr><td><strong>Thickness</strong></td><td>'+serviceDetail.th +'</td></tr>';
												}
												popOverHtml += '<tr><td><strong>Service Dims</strong></td><td>'+serviceDetail.dim +'</td></tr>';
												popOverHtml += '<tr><td><strong>Weight Limit</strong></td><td>'+limit+'</td></tr>';
												popOverHtml += '<tr><td><strong>Zone</strong></td><td>'+serviceDetail.zn+'</td></tr>';
												popOverHtml += '<tr><td><strong>Code</strong></td><td>'+serviceDetail.sc+'</td></tr>';
												popOverHtml += '<tr><td><strong>Transit Time</strong></td><td>'+transitTime+'</td></tr>';
												popOverHtml += '<tr><td><strong>Cost</strong></td><td>'+serviceDetail.tcn +' '+ serviceDetail.cur +'</td></tr>';
												popOverHtml += '<tr><td><strong>Linehual</strong></td><td>'+serviceDetail.lh +' '+ serviceDetail.cur +'</td></tr>';

												if(serviceDetail.ext != "") {
                                                    extras = $.parseJSON(serviceDetail.ext);
                                                    $.each(extras, function(k,v) {
														popOverHtml += '<tr><td><strong>'+v.charge_title+'</strong></td>'
														popOverHtml += '<td>'+v.charge +' '+ serviceDetail.cur +'</td></tr>';
                                                    });
                                                }
                                                if(response.include_piece_cost == 1) {
                                                    popOverHtml += '<tr><td><strong>Item Cost</strong></td><td>' + serviceDetail.pc + ' ' + serviceDetail.cur + '</td></tr>';
                                                }
												if(response.include_kg_cost == 1) {
                                                    popOverHtml += '<tr><td><strong>Weight Cost</strong></td><td>' + serviceDetail.wc + ' ' + serviceDetail.cur + '</td></tr>';
                                                }
												popOverHtml += '<tr><td><strong>Total Cost</strong></td><td>'+serviceDetail.tc +' '+ serviceDetail.cur +'</td></tr>';
												popOverHtml += '</table>';

												html += '<td style="width: 55%;"><label><input class="country_tariff_check" type="radio"'+((is_checked == false && (cheapestValue == index)) ? ' checked="checked"' : '')+' data-t_time="'+serviceDetail.tt+'" data-country_id="'+countryId+'" data-weight_limit="'+limit+'" data-service_id="'+serviceDetail.sid+'"  data-weight_cost="'+costHtml+'" data-cur="'+serviceDetail.cur+'" data-ext="'+_extras+'" data-fm="'+serviceDetail.fm+'" name="selected_tariff_service[' + countryId + ']['+limit+']" value="'+serviceDetail.sid +'-'+costHtml+'" />&nbsp;&nbsp;<img src="../images/carrierlogo/thumbnail/owe_16_'+serviceDetail.cl+'" alt="" />&nbsp;<a href="javascript:;" class="popovers" data-html="true" data-container="body" data-trigger="hover" data-content="'+popOverHtml+'" data-original-title="'+serviceDetail.srv+'">'+serviceDetail.srv.toLocaleLowerCase()+'</a></label></td>';
												//html += '<input type="hidden" name="selected_service_ttime['+countryId+']['+serviceDetail.sid+']['+limit+']" value="'+serviceDetail.tt+'" /></td>';
												if(response.include_piece_cost == 1) {
													html += '<td class="text-center" style="width: 15%;">' + serviceDetail.pc + ' ' + serviceDetail.cur + '</td>';
												}
												if(response.include_kg_cost == 1) {
													html += '<td class="text-center" style="width: 15%;">' + serviceDetail.wc + ' ' + serviceDetail.cur + '</td>';
												}
												html += '<td class="text-center" style="width: 15%;">'+serviceDetail.tc+' '+serviceDetail.cur+'</td></tr>';
												if(cheapestValue == index) {
													is_checked = true;
												}
											});
										}
										html += '</td></table>';
									});
									jsonStr += '{'+jsonWeightStr+'}';
									html += '</tr>';
								}
								if(html != "")
									countriesTariffs[countryFirstChar].push(html);
								//$("#tariff_response").append(html);
								//$("table.price_table_"+ptc+" .popovers").popover();
								ptc++;
							});
							jsonStr = '{'+jsonStr+'}';
							$("#tariff_json").val(jsonStr);
							var abcHtml = '';
							firstLetters.sort();
							$.each(firstLetters, function(key, value) {
								abcHtml += '<a class="btn btn-sm btn-default rate_filter margin-right-5" href="javascript:;" data-letter="'+value.toLowerCase()+'" title="">'+value.toUpperCase()+'</a>';
							});
							$("#rate_filter_container").html(abcHtml);
							var _fistLetter = firstLetters[0];
							$("#tariff_response").append(countriesTariffs[_fistLetter]);
							$("table.price_table .popovers").popover();
							//$("#tariff_response").append('</tbody>');
							if (response != '') {
								$("#save_tariff_modal").show();
								$('.all_portlet').hide();
								$("#tariff_portlet").show();
								$('#btn_customer_pricing').show();
							}
							$('span').tooltip();
						},'json');
					}
				});
				$(document).on('click','.rate_filter',function(){
					var letter = $(this).data('letter');
					$("#tariff_response").html('');
					$.each(countriesTariffs[letter], function(key,val){
						$("#tariff_response").append(val);
					});
					var json_str = $("#tariff_json").val();
					var __tariff_json = JSON.parse(json_str);
					var checkArr = $("#tariff_response .country_tariff_check");
					$.each(checkArr, function(i,v){
						var event = $(this);
						var _country_id = $(this).data('country_id');
						var _weight_limit = $(this).data('weight_limit');
						var _service_id = $(this).data('service_id');
						$.each(__tariff_json[_country_id][_weight_limit], function(service, wcost){
							if(service == _service_id){
								$(event).attr('checked','checked');
							}
						});
					});
					$("table.price_table .popovers").popover();
				});

				$(document).on('click', '#excel_download_btn', function () {
					var form_data = $("#customer_pricing_from").serializeForm();
					var jsonStr = JSON.stringify(form_data);
					$('#customer_tariff_excel').val(jsonStr);
					$('#customer_pricing_excel_form').submit();
				});

				$(document).on('click', '#demo_tariff_excel_download_btn', function () {
					var form_data = $("#customer_pricing_from").serializeForm();
					var jsonStr = JSON.stringify(form_data);
					var currency_id = $('#currency_id').val();
					$('#demo_tariff_currency_id').val(currency_id);
					$('#demo_customer_tariff_excel').val(jsonStr);
					$('#demo_customer_pricing_excel_form').submit();
				});

				$(document).on('click','#save_tariff_modal',function(){
					$('#tariff_save_with_customer_tariff').val(0);
					//$('.customer_tariff_field').hide();
					$('#saveTariffModal').modal('show');
				});

				$(document).on('click','#save_all_tariff_modal',function(){
					$('#tariff_save_with_customer_tariff').val(1);
					//$('.customer_tariff_field').show();
					$('#saveTariffModal').modal('show');
				});

				$("#tariff_response").on('click','.country_tariff_check',function(){
					var country_id = $(this).data('country_id');
					var weight_limit = $(this).data('weight_limit');
					var service_id = $(this).data('service_id');
					var weight_cost = $(this).data('weight_cost');
					var t_time = $(this).data('t_time');
					var cur = $(this).data('cur');
					var ext = $(this).data('ext');
					var fm = $(this).data('fm');
					var json_str = $("#tariff_json").val();
					var _tariff_json = JSON.parse(json_str);
					$.each(_tariff_json[country_id][weight_limit], function(key){
						delete _tariff_json[country_id][weight_limit][key];
					});
					_tariff_json[country_id][weight_limit][service_id];
					_tariff_json[country_id][weight_limit][service_id] = weight_cost+"-"+t_time+"-"+cur+"-"+fm+"-"+ext;
					var jsonStr = JSON.stringify(_tariff_json);
					$("#tariff_json").val(jsonStr);
				});
				$("#download_tariff_csv").click(function(){
					$("#download_tariff_csv_frm").submit();
				});
				$("#customer_pricing").click(function(){
					var tariffId = $("#tariffId").val();
					window.open('tariffs_pricing.php?id='+tariffId,'_blank');
				});
				$("#download_routing").click(function(){
					$('#routing_pricing_download').val(0);
					$('#routing_pricing_include_piece_cost').val(0);
					$('#routing_pricing_include_kg_cost').val(0);
					$('#download_routing_frm').submit();
				});
				$("#download_routing_with_pricing").click(function(){
					$('#routing_pricing_download').val(1);
					$('#routing_pricing_include_piece_cost').val(0);
					if ($("#include_piece_cost").prop('checked') == true) {
						$('#routing_pricing_include_piece_cost').val(1);
					}
					$('#routing_pricing_include_kg_cost').val(0);
					if ($("#include_kg_cost").prop('checked') == true) {
						$('#routing_pricing_include_kg_cost').val(1);
					}
					$('#download_routing_frm').submit();
				});
				$("#tariff_action").click(function () {
					var tariff_save_with_customer_tariff = $('#tariff_save_with_customer_tariff').val();
					var form_data = [];
					if(tariff_save_with_customer_tariff > 0) {
						form_data = $("#get_pricing_form, #customer_pricing_from").serializeArray();
						form_data.push({name: 'tariff_save_with_customer_tariff', value: tariff_save_with_customer_tariff});
					} else {
						form_data = $("#get_pricing_form").serializeArray();
					}
					var tariff_type = 1;
					if ($("#tariff_type").prop('checked') == false) {
						tariff_type = 0;
					}
					form_data.push({name: 'tariff_type', value: tariff_type});
					var carrier_id = $('#carrier_id').val();
					form_data.push({name: 'carrier_id', value: carrier_id});
					var from_country = $('#from_country').val();
					form_data.push({name: 'from_country', value: from_country});
					var service_name = $('#service_name').val();
					form_data.push({name: 'service_name', value: service_name});
					var service_code = $('#service_code').val();
					form_data.push({name: 'service_code', value: service_code});
					var tariff_name = $('#tariff_name').val();
					form_data.push({name: 'tariff_name', value: tariff_name});
					var start_date = $('#start_date').val();
					form_data.push({name: 'start_date', value: start_date});
					var end_date = $('#end_date').val();
					form_data.push({name: 'end_date', value: end_date});
					var currency_id = $('#currency_id').val();
					form_data.push({name: 'currency_id', value: currency_id});
					var service_id = $('#service_id').val();
					form_data.push({name: 'service_id', value: service_id});
					var tariff_id = $('#tariff_id').val();
					form_data.push({name: 'tariff_id', value: tariff_id});
					var tariffs_formula = $('#tariffs_formula').val();
					form_data.push({name: 'tariffs_formula', value: tariffs_formula});
					var show_piece_cost = 1;
					if ($("#include_piece_cost").prop('checked') == false) {
						show_piece_cost = 0;
					}
					form_data.push({name: 'show_piece_cost', value: show_piece_cost});
					var show_kg_cost = 1;
					if ($("#include_kg_cost").prop('checked') == false) {
						show_kg_cost = 0;
					}
					form_data.push({name: 'show_kg_cost', value: show_kg_cost});

					var selectedToCountries = [];
					$('.to_country').each(function(){
						var id = $(this).attr('id');
						if(id != "undefined") {
							$("#"+id+" :selected").each(function(){
								selectedToCountries.push($(this).val());
							});
						}
					});
					form_data.push({name: 'selected_to_countries', value: selectedToCountries});
					form_data.push({name: 'min_weight', value: $('#tmp_min_weight').val()});
					form_data.push({name: 'max_weight', value: $('#tmp_max_weight').val()});
					if($("#from_country").val() == ''){
						swal("Error","Please select from country", "error");
						return false;
					}
					if($("#currency_id").val() == ''){
						swal("Error","Please select currency", "error");
						return false;
					}
					$('.to_country').each(function(i, obj) {
						if(obj.value !== "undefined") {
							if(obj.value == ''){
								swal("Error","Please select to country", "error");
								return false;
							}
						}
					});
					$('.from_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select from weight", "error");
							return false;
						}
					});
					$('.to_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select to weight", "error");
							return false;
						}
					});
					if($("#carrier_id").val() == ''){
						swal("Error","Please select carrier", "error");
						return false;
					}
					if(tariff_type == 1) {
						if($("#service_name").val() == ''){
							swal("Error","Please enter service name", "error");
							return false;
						}
						if($("#service_code").val() == ''){
							swal("Error","Please enter service code", "error");
							return false;
						}
						if($("#tariff_name").val() == ''){
							swal("Error","Please enter tariff name", "error");
							return false;
						}
						if($("#start_date").val() == ''){
							swal("Error","Please select start date", "error");
							return false;
						}
						if($("#end_date").val() == ''){
							swal("Error","Please select end date", "error");
							return false;
						}
					} else {
						if($("#service_id").val() == ''){
							swal("Error","Please select service", "error");
							return false;
						}
						if($("#tariff_id").val() == ''){
							swal("Error","Please select tariff", "error");
							return false;
						}
					}
					var checkLoss = $('#loss').val();
					if(checkLoss > 0) {
						saveTariff = false;
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
								form_data.push({name: 'loss_save', value: 1});
								saveTariffs(form_data);
							}
						});
					} else {
						form_data.push({name: 'loss_save', value: 0});
						saveTariffs(form_data);
					}
//                    $.post('get_pricing.php', $("#get_pricing_form").serialize(), function (response) {
//                        console.log(response);
//                        //$("#tariff_response").html(response);
//                        //if(response != '')
//                        //    $("#tariff_action").show();
//                        //$(".popovers").popover();
//                    });
				});
				$(".download_calculated_pricing").click(function () {
					var download_type = $(this).data('download_type');
					var form_data = $("#get_pricing_form").serializeArray();
					form_data.push({name: 'func', value: 'calculated_pricing_for_csv'});
					form_data.push({name: 'download_type', value: download_type});
                    form_data.push({name: 'linehual', value: 0});
					var from_country = $('#from_country').val();
					form_data.push({name: 'from_country', value: from_country});
					var show_piece_cost = 1;
					if ($("#include_piece_cost").prop('checked') == false) {
						show_piece_cost = 0;
					}
					form_data.push({name: 'show_piece_cost', value: show_piece_cost});
					var show_kg_cost = 1;
					if ($("#include_kg_cost").prop('checked') == false) {
						show_kg_cost = 0;
					}
					form_data.push({name: 'show_kg_cost', value: show_kg_cost});

					var selectedToCountries = [];
					$('.to_country').each(function(){
						var id = $(this).attr('id');
						if(id != "undefined") {
							$("#"+id+" :selected").each(function(){
								selectedToCountries.push($(this).val());
							});
						}
					});
					form_data.push({name: 'selected_to_countries', value: selectedToCountries});
					form_data.push({name: 'min_weight', value: $('#tmp_min_weight').val()});
					form_data.push({name: 'max_weight', value: $('#tmp_max_weight').val()});
					$('.from_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select from weight", "error");
							return false;
						}
					});
					$('.to_weight').each(function(i, obj) {
						if($(this).val() == ''){
							swal("Error","Please select to weight", "error");
							return false;
						}
					});
					$.blockUI();
					$.ajax({
						type: "POST",
						url: "get_pricing.php",
						data: form_data,
						dataType: "json",
						success: function (response) {
							$.unblockUI();
							if(response.status == "error") {
								swal({
									title:"Sorry!",
									text:response.message,
									type: "error",
									html:true
								});
							} else {
								if(response.status == "warning") {
									swal({
										title:"Warning!",
										text:response.warning,
										type: "warning",
										html:true
									});
								} else {
									$('#csv_string').val(response.csv_sting);
									$('#download_csv_pricing_form').submit();
								}
							}
						},
						error: function () {
							$.unblockUI();
							//alert('error handing here');
						}
					});
				});
                $(".download_calculated_pricing_with_linehual").click(function () {
                    var download_type = $(this).data('download_type');
                    var form_data = $("#get_pricing_form").serializeArray();
                    form_data.push({name: 'func', value: 'calculated_pricing_for_csv'});
                    form_data.push({name: 'download_type', value: download_type});
                    form_data.push({name: 'linehual', value: 1});
                    var from_country = $('#from_country').val();
                    form_data.push({name: 'from_country', value: from_country});
                    var show_piece_cost = 1;
                    if ($("#include_piece_cost").prop('checked') == false) {
                        show_piece_cost = 0;
                    }
                    form_data.push({name: 'show_piece_cost', value: show_piece_cost});
                    var show_kg_cost = 1;
                    if ($("#include_kg_cost").prop('checked') == false) {
                        show_kg_cost = 0;
                    }
                    form_data.push({name: 'show_kg_cost', value: show_kg_cost});
                    var selectedToCountries = [];
                    $('.to_country').each(function(){
                        var id = $(this).attr('id');
                        if(id != "undefined") {
                            $("#"+id+" :selected").each(function(){
                                selectedToCountries.push($(this).val());
                            });
                        }
                    });
                    form_data.push({name: 'selected_to_countries', value: selectedToCountries});
                    form_data.push({name: 'min_weight', value: $('#tmp_min_weight').val()});
                    form_data.push({name: 'max_weight', value: $('#tmp_max_weight').val()});
                    $('.from_weight').each(function(i, obj) {
                        if($(this).val() == ''){
                            swal("Error","Please select from weight", "error");
                            return false;
                        }
                    });
                    $('.to_weight').each(function(i, obj) {
                        if($(this).val() == ''){
                            swal("Error","Please select to weight", "error");
                            return false;
                        }
                    });
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "get_pricing.php",
                        data: form_data,
                        dataType: "json",
                        success: function (response) {
                            $.unblockUI();
                            if(response.status == "error") {
                                swal({
                                    title:"Sorry!",
                                    text:response.message,
                                    type: "error",
                                    html:true
                                });
                            } else {
                                if(response.status == "warning") {
                                    swal({
                                        title:"Warning!",
                                        text:response.warning,
                                        type: "warning",
                                        html:true
                                    });
                                } else {
                                    $('#csv_strings').val(response.csv_sting);
                                    $('#download_csv_pricing_with_linehual_form').submit();
                                }
                            }
                        },
                        error: function () {
                            $.unblockUI();
                            //alert('error handing here');
                        }
                    });
                });
				$('#edit_tariff').hide();
				$('#tariff_type').on('switchChange.bootstrapSwitch', function (event, state) {
					if (state) {
						$('#edit_tariff').hide();
						$('#new_tariff').show();
						$('#start_date').removeAttr('disabled');
						$('#end_date').removeAttr('disabled');
						$('.btn-calender').removeAttr('disabled');
						$('#start_date').val('');
						$('#end_date').val('');
					} else {
						$('#new_tariff').hide();
						$('#edit_tariff').show();
						$("#service_id").html("<option value=''>Select Service</option>");
						$("#service_id").select2();
						$('#carrier_id').val('');
						$('#carrier_id').trigger('change');
						$('#start_date').attr('disabled','disabled');
						$('.btn-calender').attr('disabled','disabled');
						$('#end_date').attr('disabled','disabled');
						$('#start_date').val('');
						$('#end_date').val('');
					}
				});
				$('#carrier_id').on('change', function (event, state) {
					var carrier_id = $(this).val();
					if ($("#tariff_type").prop('checked') == false) {
						$.blockUI();
						$.ajax({
							type: "POST",
							url: "get_pricing.php",
							data: {func: "get_carrier_services", carrier_id: carrier_id},
							dataType: "html",
							success: function (data) {
								$.unblockUI();
								$("#service_id").html(data);
								$("#service_id").select2();
							},
							error: function () {
								$.unblockUI();
								//alert('error handing here');
							}
						});
					}
				});
				$('#service_id').on('change', function (event, state) {
					var service_id = $(this).val();
					if ($("#tariff_type").prop('checked') == false) {
						$.blockUI();
						$.ajax({
							type: "POST",
							url: "get_pricing.php",
							data: {func: "get_service_tariffs", service_id: service_id},
							dataType: "html",
							success: function (data) {
								$.unblockUI();
								$("#tariff_id").html(data);
								$("#tariff_id").select2();
							},
							error: function () {
								$.unblockUI();
								//alert('error handing here');
							}
						});
					}
				});
				$('#tariff_id').on('change', function (event, state) {
					var tariff_id = $(this).val();
					if ($("#tariff_type").prop('checked') == false) {
						$.blockUI();
						$.ajax({
							type: "POST",
							url: "get_pricing.php",
							data: {func: "get_tariff_details", tariff_id: tariff_id},
							dataType: "json",
							success: function (data) {
								$.unblockUI();
								$('#start_date').val(data.start_date);
								$('#end_date').val(data.end_date);
							},
							error: function () {
								$.unblockUI();
								//alert('error handing here');
							}
						});
					}
				});
				$('#mail_parcel').on('change', function (event, state) {
					var mail_parcel = $(this).val();
					$('#manual_weight_limit').val();
					$('#mail_type_box').hide();
					$('#courier_type_box').show();
					// $('#mail_parcel_box').removeClass().addClass("col-md-12");
					if(mail_parcel == "mail") {
						$('#mail_type_box').show();
						$('#manual_weight_limit').val('0.0-0.1\n0.1-0.25\n0.25-0.50\n0.50-0.75\n0.75-1\n1-1.25\n1.25-1.5\n1.5-1.75\n1.75-2\n');
						// $('#mail_parcel_box').removeClass().addClass("col-md-6");
					} else if(mail_parcel == "courier") {
						$('#courier_type_box').hide();
						$('#manual_weight_limit').val('0.5-1.0\n1.0-1.5\n1.5-2.0\n2.0-2.5\n2.5-3.0\n3.0-3.5\n3.5-4.0\n4.0-4.5\n4.5-5.0\n5.0-5.5\n5.5-6.0\n6.0-6.5\n6.5-7.0\n7.0-7.5\n7.5-8.0\n8.0-8.5\n8.5-9.0\n9.0-9.5\n9.5-10.0\n10.0-11.0\n11.0-12.0\n12.0-13.0\n13.0-14.0\n14.0-15.0\n');
					}else{
						$("#add_manual_weight_limit").val('auto');
						/*$('#add_manual_weight_limit option[value="standard"]').attr('disabled', true);
						$("#add_manual_weight_limit").selectpicker("refresh");*/
					}
					$("#add_manual_weight_limit").trigger('change');
					$('#tracked_untracked').trigger('change');
				});
				$('#tracked_untracked').on('change', function (event, state) {
					var tracked_untracked = $(this).val();
					$('#economy_priority_box').hide();
					if(tracked_untracked == "1") {
						$('#economy_priority_box').show();
					}
				});
				$('#currency_id').on('change', function (event, state) {
					var currency_id = $(this).val();
					$.blockUI();
					$.ajax({
						type: "POST",
						url: "get_pricing.php",
						data: {func: "get_curency_rate", currency_id: currency_id},
						dataType: "json",
						success: function (data) {
							$.unblockUI();
							if(data.status == "success") {
								$('#currency_conversion_rate').val(data.rate);
							}
						},
						error: function () {
							$.unblockUI();
							//alert('error handing here');
						}
					});
				});
				$('#currency_id').trigger('change');
				$('#manual_weight_limit_box').hide();
				$('.multiselect_drop_down').multiSelect({
					selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'/>",
					selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'/>",
					selectableFooter: "<a href='javascript:;' class='btn btn-sm btn-default btn-secelct-option' id='select-all'>select all</a>",
					selectionFooter: "<a href='javascript:;' class='btn btn-sm btn-default btn-secelct-option' id='deselect-all'>deselect all</a>",
					afterInit: function(ms) {
						var that = this,
							$selectableSearch = that.$selectableUl.prev(),
							$selectionSearch = that.$selectionUl.prev(),
							selectableSearchString = '#' + that.$container.attr('id') +
								' .ms-elem-selectable:not(.ms-selected)',
							selectionSearchString = '#' + that.$container.attr('id') +
								' .ms-elem-selection.ms-selected';
						that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
							.on('keydown', function(e) {
								if (e.which === 40) {
									that.$selectableUl.focus();
									return false;
								}
							});

						that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
							.on('keydown', function(e) {
								if (e.which == 40) {
									that.$selectionUl.focus();
									return false;
								}
							});
					},
					afterSelect: function(values) {
						this.qs1.cache();
						this.qs2.cache();
					},
					afterDeselect: function(values) {
						this.qs1.cache();
						this.qs2.cache();
					}
				});
				$(document).on('click','#select-all',function(){
					$('.multiselect_drop_down').multiSelect('select_all');
					return false;
				});
				$(document).on('click', '#deselect-all',function(){
					$('.multiselect_drop_down').multiSelect('deselect_all');
					return false;
				});
				$("#advance_search_option").on('ifChanged', function (e) {
					if ($(this).prop('checked') == true){
						$('#advance_search_fields').show();
					} else {
						$('#advance_search_fields').hide();
					}
				});
				$('#zone').on('change', function(e){
					var zone = $(this).find("option:selected").val();
					$.blockUI();
					$.ajax({
						type: "POST",
						url: "get_pricing.php",
						data: {func: "get_zone_country_option", zone: zone},
						dataType: "json",
						success: function (data) {
							$.unblockUI();
							if(data.status == "success") {
								$('.to_country').each(function(i, obj) {
									var opt_id = obj.id;
									$('#'+opt_id).html(data.options);
									$('#'+opt_id).selectpicker('refresh');
								});
							}
						},
						error: function () {
							$.unblockUI();
							//alert('error handing here');
						}
					});
				});

				$('#again_search_pricing').on('click', function(e){
					$('.all_portlet').hide();
					$("#search_portlet").show();
				});

				$('#btn_compare_pricing').on('click', function(e){
					$('.all_portlet').hide();
					$("#tariff_portlet").show();
				});

				$('#btn_customer_pricing').on('click', function(e){
					var json_str = $("#tariff_json").val();
					var tariff_json = JSON.parse(json_str);
					var extra_charge_type_id = $("#extra_charge_type_id").val();
					$.blockUI();
					$.ajax({
						type: "POST",
						url: "get_pricing.php",
						data: {func: "load_tariff_details", tariff_json: tariff_json,extra_charge_type_id:extra_charge_type_id},
						dataType: "json",
						success: function (data) {
							$.unblockUI();
							$('#customer_pricing_box').html(data.tariff_detail);
							$('#customer_extra_box').html(data.tariff_extras_filter);
							$('.to_zone_id').html(data.country_options);
							$('.to_zone_id').select2();
							$('.all_portlet').hide();
							$("#customer_pricing_portlet").show();
							$(".popovers").popover();
						},
						error: function () {
							$.unblockUI();
							//alert('error handing here');
						}
					});
				});


				$(document).on('change','.from_weight',function(){
					$("#add_manual_weight_limit").trigger('change');
				});
				$(document).on('change','.to_weight',function(){
					$("#add_manual_weight_limit").trigger('change');
				});

				$(".add_manual_weight_limit").on('change', function (e) {
					var to_weight = 0;
					$(".to_weight").each(function(k,obj){
						var _to_weight = $(obj).val();
						if($.trim(_to_weight) == "")
							_to_weight = 0;
						_to_weight = parseFloat(_to_weight);
						if(_to_weight > to_weight)
							to_weight = _to_weight;
					});
					var from_weight = to_weight;
					$(".from_weight").each(function(l,ob){
						var _from_weight = $(ob).val();
						if($.trim(_from_weight) == "")
							_from_weight = 0;
						_from_weight = parseFloat(_from_weight);
						if(_from_weight < from_weight)
							from_weight = _from_weight;
					});
					$("#_add_manual_weight_limit_container").hide();
					//var limits = $(this).val();
					var limits = $("#add_manual_weight_limit").val();
					var manual_limits = $("#_add_manual_weight_limit").val();
					var mail_parcel = $('#mail_parcel').val();
					$('#manual_weight_limit_box').hide();
					if(limits == 'manual'){
						$("#_add_manual_weight_limit_container").show();
					}
					if(limits == 'bespoke' || limits == 'manual'){
						$('#show_wWeight_breakets').prop('checked',true).iCheck('update');
						$("#show_wWeight_breakets").trigger("change");
					}
					if(limits != 'auto') {
						$.blockUI();
						$.ajax({
							type: "POST",
							url: "get_pricing.php",
							data: {func: "manual_weight_limits", add_manual_weight_limit: limits, mail_parcel: mail_parcel,manual_limits: manual_limits,from_weight:from_weight,to_weight:to_weight},
							dataType: "json",
							success: function (data) {
								$.unblockUI();
								if(data.status == "success") {
									$('.weight_limit_container').html(data.html);
									elindexweight = data.count;
									if($('#show_wWeight_breakets').is(":checked"))
										$('#manual_weight_limit_box').show();
									else
										$('#manual_weight_limit_box').hide();
								}
							},
							error: function () {
								$.unblockUI();
								//alert('error handing here');
							}
						});
					}
				});
				$('#mail_parcel').trigger('change');

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

				$(document).on('keyup','.zone_extra_all', function() {
					var zone_id = $(this).data('zone_id');
					var extra_id = $(this).data('extra_id');
					var extra_val = $(this).val();
					if($.trim(extra_val) != "")
						extra_val = parseFloat(extra_val).toFixed(2);
					$(".customer_extra_"+zone_id+"_"+extra_id).val(extra_val);
					$(".customer_extra_"+zone_id+"_"+extra_id).trigger('change');
				});

				$(document).on('keyup','.custom_value', function() {
					var id_str = $(this).data('id_str');
					var kg_value = $(this).val();
					var weight = $(this).data('weight');
					var kg_margin = $('#zone_margin_input_'+id_str).val();
					var piece_value = $('#zone_piece_value_input_'+id_str).val();
					var piece_margin = $('#zone_piece_margin_input_'+id_str).val();
					var cost = $("#zone_input_"+id_str).val();

					var cost_value = cost.split('/');
					var kgCost = cost_value[0];
					var itemCost = cost_value[1];
					if(typeof itemCost === "undefined") {
						itemCost = 0.00;
					}
					total_extras = getTotalExtras(id_str);
					calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,total_extras);
				});

				$(document).on('keyup','.custom_margin', function() {
					var id_str = $(this).data('id_str');
					var kg_margin = $(this).val();
					var weight = $(this).data('weight');
					var kg_value = $('#zone_value_input_'+id_str).val();
					var piece_value = $('#zone_piece_value_input_'+id_str).val();
					var piece_margin = $('#zone_piece_margin_input_'+id_str).val();
					var cost = $("#zone_input_"+id_str).val();
					var cost_value = cost.split('/');
					var kgCost = cost_value[0];
					var itemCost = cost_value[1];
					if(typeof itemCost === "undefined") {
						itemCost = 0.00;
					}
					total_extras = getTotalExtras(id_str);
					calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,total_extras);
				});

				$(document).on('keyup','.custom_piece_value', function() {
					var id_str = $(this).data('id_str');
					var piece_value = $(this).val();
					var weight = $(this).data('weight');
					var piece_margin = $('#zone_piece_margin_input_'+id_str).val();
					var kg_value = $('#zone_value_input_'+id_str).val();
					var kg_margin = $('#zone_margin_input_'+id_str).val();
					var cost = $("#zone_input_"+id_str).val();
					var cost_value = cost.split('/');
					var kgCost = cost_value[0];
					var itemCost = cost_value[1];
					if(typeof itemCost === "undefined") {
						itemCost = 0.00;
					}
					total_extras = getTotalExtras(id_str);
					calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,total_extras);
				});

				$(document).on('keyup','.custom_piece_margin', function() {
					var id_str = $(this).data('id_str');
					var weight = $(this).data('weight');
					var piece_margin = $(this).val();
					var piece_value = $('#zone_piece_value_input_'+id_str).val();
					var kg_value = $('#zone_value_input_'+id_str).val();
					var kg_margin = $('#zone_margin_input_'+id_str).val();
					var cost = $("#zone_input_"+id_str).val();
					var cost_value = cost.split('/');
					var kgCost = cost_value[0];
					var itemCost = cost_value[1];
					if(typeof itemCost === "undefined") {
						itemCost = 0.00;
					}
					total_extras = getTotalExtras(id_str);
					calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,total_extras);
				});

				$(document).on('change','.custom_extra',function(){
					var id_str = $(this).data('id_str');
					var weight_str = $(this).data('weight_str');
					var weight = $(this).data('weight');
					var piece_margin = $('#zone_piece_margin_input_'+id_str).val();
					var piece_value = $('#zone_piece_value_input_'+id_str).val();
					var kg_value = $('#zone_value_input_'+id_str).val();
					var kg_margin = $('#zone_margin_input_'+id_str).val();
					var cost = $("#zone_input_"+id_str).val();
					var cost_value = cost.split('/');
					var kgCost = cost_value[0];
					var itemCost = cost_value[1];
					if(typeof itemCost === "undefined") {
						itemCost = 0.00;
					}
					total_extras = getTotalExtras(id_str);
					calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,total_extras);
				});

				$(document).on('click','#apply_extra_charges_only',function(){
					$(".tariff-extra-filter").each(function(k,v){
						var extra_val = $(v).val();
						var extra_id = $(v).data("extra_id");
						if($.trim(extra_val) != "")
							extra_val = parseFloat(extra_val).toFixed(2);
						$('.zone_extra_all_'+extra_id).val(extra_val);
						$('.zone_extra_all_'+extra_id).trigger('keyup');
					});
				});
				$("#show_wWeight_breakets").on('ifChanged', function (e) {
					if ($(this).prop('checked') == true){
						$('#manual_weight_limit_box').show();
					} else {
						$('#manual_weight_limit_box').hide();
					}
				});
			});
			function getTotalExtras(id_str){
				var total_extras = 0;
				$('.zone_price_extra_'+id_str).each(function(k,v){
					var _extra = $(v).val();
					if($.trim(_extra) == "")
						_extra = 0;
					total_extras += parseFloat(_extra);
				});
				return total_extras;
			}
			function saveTariffs(form_data) {
				$.blockUI();
				$.ajax({
					type: "POST",
					url: "get_pricing.php",
					data: form_data,
					dataType: "json",
					success: function (response) {
						$.unblockUI();
						if(response.status == "error") {
							swal({
								title:"Sorry!",
								text:response.message,
								type: "error",
								html:true
							});
						} else {
							if(response.warning) {
								swal({
									title:"Warning!",
									text:response.warning,
									type: "warning",
									html:true
								});
							} else {
								$("#customer_pricing").show();
								$("#download_tariff_csv").show();
								$("#download_routing").show();
								$("#download_routing_with_pricing").show();
								$("#cutomized_service_id_for_routing").val(response.customized_service_id);
								$("#tariffId").val(response.tariff_id);
								$("#routing_tariff_id").val(response.tariff_id);
								$('#saveTariffModal').modal('hide');
								$('#service_name').val('');
								$('#service_code').val('');
								$('#tariff_name').val('');
								$('#start_date').val('');
								$('#end_date').val('');
								$('#carrier_id').val('');
								$('#carrier_id').trigger('change');
								$('.all_portlet').hide();
								$("#tariff_portlet").show();
								$('#btn_customer_pricing').hide();
								swal({
									title:"Success!",
									text:response.message,
									type: "success",
									html:true
								});
							}
						}
					},
					error: function () {
						$.unblockUI();
						//alert('error handing here');
					}
				});
			}

			function getFormData($form){
				var unindexed_array = $form.serializeArray();
				var indexed_array = {};

				$.map(unindexed_array, function(n, i){
					indexed_array[n['name']] = n['value'];
				});

				return indexed_array;
			}

			function calculateNewCost(kgCost,itemCost,fromWeight,toWeight,zoneId,weightStr) {
				var wholeLineHaulWg = $('#whole_tariff_line_haul_wg').val();
				var wholeLineHaulIt = $('#whole_tariff_line_haul_it').val();
				var wholeMarginWg = $.trim($('#whole_tariff_margin_wg').val());
				var wholeMarginWgType = $.trim($('#whole_tariff_margin_type_wg').val());
				var wholeMarginIt = $.trim($('#whole_tariff_margin_it').val());
				var wholeMarginItType = $.trim($('#whole_tariff_margin_type_it').val());

				var courier_tariff_formula = "";
				var postal_tariff_formula = "";
				if($("#courier_tariff_formula").length)
					courier_tariff_formula = $("#courier_tariff_formula").val();
				if($("#postal_tariff_formula").length)
					postal_tariff_formula = $("#postal_tariff_formula").val();

				var totalExtras = 0;


				if($(".tariff-extra-filter").length > 0){
					$(".tariff-extra-filter").each(function(kk,vv){
						var ex_id = $(vv).data('extra_id');
						var ex_val =  $(vv).val();
						if($.trim(ex_val) == "")
							ex_val = 0;
						ex_val = parseFloat(ex_val).toFixed(2);
						totalExtras = parseFloat(totalExtras) + parseFloat(ex_val);
						$(".customer_extra_"+ex_id).val(ex_val);
					});
				}

				/* Calculation */
				var kgCostNew = parseFloat(kgCost);
				var pieceCostNew = parseFloat(itemCost);
				/* apply liane hual or value */
				if(wholeLineHaulWg != "") {
					kgCostNew = parseFloat(kgCostNew) + parseFloat(wholeLineHaulWg);
				}
				if(wholeLineHaulIt != "") {
					pieceCostNew = parseFloat(pieceCostNew) + parseFloat(wholeLineHaulIt);
				}
				/* apply margin */
				if(wholeMarginWg != "") {
					if(wholeMarginWgType == "percentage") {
						var applyValue = (parseFloat(wholeMarginWg)/100)*parseFloat(kgCostNew);
					} else {
						var applyValue = wholeMarginWg;
					}
					kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
				}
				if(wholeMarginIt != "") {
					if(wholeMarginItType == "percentage") {
						var applyValuePiece = (parseFloat(wholeMarginIt)/100)*parseFloat(pieceCostNew);
					} else {
						var applyValuePiece = wholeMarginIt;
					}
					pieceCostNew = (parseFloat(pieceCostNew) + parseFloat(applyValuePiece));
				}
				if(wholeMarginWg) {
					$('#zone_margin_input_' + weightStr + '_' + zoneId).val(wholeMarginWg);
				} else {
					$('#zone_margin_input_' + weightStr + '_' + zoneId).val("0.00");
				}
				if(wholeMarginIt) {
					$('#zone_piece_margin_input_'+weightStr+'_'+zoneId).val(wholeMarginIt);
					//$('#zone_value_input_'+weightStr+'_'+zoneId).val(wholeMarginIt);
				} else {
					$('#zone_piece_margin_input_'+weightStr+'_'+zoneId).val("0.00");
					//$('#zone_value_input_'+weightStr+'_'+zoneId).val("");
				}
				if(wholeLineHaulWg) {
					$('#zone_value_input_' + weightStr + '_' + zoneId).val(wholeLineHaulWg);
					//$('#zone_piece_margin_input_' + weightStr + '_' + zoneId).val(wholeLineHaulWg);
				} else {
					$('#zone_value_input_' + weightStr + '_' + zoneId).val("0.00");
					//$('#zone_piece_margin_input_' + weightStr + '_' + zoneId).val("");
				}
				if(wholeLineHaulIt) {
					$('#zone_piece_value_input_' + weightStr + '_' + zoneId).val(wholeLineHaulIt);
				} else {
					$('#zone_piece_value_input_' + weightStr + '_' + zoneId).val("0.00");
				}
				/* single value and margin apply */
				$('.to_zone_id').each(function(key,obj) {
					var toZoneId = obj.value;
					if(zoneId == toZoneId) {
						var fromWeightEnter = parseFloat($("#weight_from_"+key).val());
						var toWeightEnter = parseFloat($("#weight_to_"+key).val());
						var marginWg = $("#margin_wg_"+key).val();
						var margin_mg_type = $("#margin_type_wg_"+key).val();
						if(marginWg != "") {
							marginWg = parseFloat(marginWg);
						}
						var marginIt = $.trim($("#margin_it_"+key).val());
						var margin_it_type = $("#margin_type_it_"+key).val();
						if(marginIt != "") {
							marginIt = parseFloat(marginIt);
						}
						var lineHaulWg = $('#line_haul_wg_'+key).val();
						var lineHaulIt = $('#line_haul_it_'+key).val();
						fromWeightEnter = fromWeightEnter.toFixed(2);
						toWeightEnter = toWeightEnter.toFixed(2);
						if((parseFloat(fromWeight) >= parseFloat(fromWeightEnter)) && (parseFloat(toWeight) <= parseFloat(toWeightEnter))) {
							kgCostNew = parseFloat(kgCost);
							pieceCostNew = parseFloat(itemCost);
							if(lineHaulWg != "") {
								kgCostNew = parseFloat(kgCostNew) + parseFloat(lineHaulWg);
							}
							if(lineHaulIt != "") {
								pieceCostNew = parseFloat(pieceCostNew) + parseFloat(lineHaulIt);
							}
							if(marginWg != "") {
								if(margin_mg_type == "percentage") {
									var applyValue = (parseFloat(marginWg)/100)*parseFloat(kgCostNew);
								} else {
									var applyValue = marginWg;
								}
								kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
							}
							if(marginIt != "") {
								if(margin_it_type == "percentage") {
									var applyValue = (parseFloat(marginIt)/100)*parseFloat(kgCostNew);
								} else {
									var applyValue = marginIt;
								}
								kgCostNew = (parseFloat(kgCostNew) + parseFloat(applyValue));
							}

							if(marginWg != "") {
								$('#zone_margin_input_' + weightStr + '_' + zoneId).val(marginWg);
							} else {
								$('#zone_margin_input_' + weightStr + '_' + zoneId).val("");
							}
							if(marginIt != "") {
								$('#zone_piece_margin_input_'+weightStr+'_'+zoneId).val(marginIt);
							} else {
								$('#zone_piece_margin_input_'+weightStr+'_'+zoneId).val("");
							}
							if(lineHaulWg != "") {
								$('#zone_value_input_' + weightStr + '_' + zoneId).val(lineHaulWg);
							} else {
								$('#zone_value_input_' + weightStr + '_' + zoneId).val("");
							}
							if(lineHaulIt != "") {
								$('#zone_piece_value_input_'+weightStr+'_'+zoneId).val(lineHaulIt);
							} else {
								$('#zone_piece_value_input_'+weightStr+'_'+zoneId).val("");
							}
						}
					}
				});
				var service_validation_type = $('#service_validation_type_'+ weightStr + '_' + zoneId).val();
				kgCostNew = parseFloat(kgCostNew).toFixed(2);
				pieceCostNew = parseFloat(pieceCostNew).toFixed(2);
				var totalFormulaCost = kgCostNew;
				if(pieceCostNew != "" && $.isNumeric(pieceCostNew)) {
					var weight = $('#zone_value_input_' + weightStr + '_' + zoneId).data('weight');
					var weightArr = weight.split('/');
					var fromWeight = weightArr[0];
					var toWeight = weightArr[1];
					if(service_validation_type == "mail") {
						if(postal_tariff_formula != ""){
							totalFormulaCost = calculateFormulaPrice(postal_tariff_formula, parseFloat(fromWeight), parseFloat(toWeight), parseFloat(kgCostNew), parseFloat(pieceCostNew), 1, 1);
						}
						//totalFormulaCost = ((parseFloat(kgCostNew) * parseFloat(toWeight)) + parseFloat(pieceCostNew));
					}else{
						pieceCostNew = 0.00;
						if(courier_tariff_formula != ""){
							totalFormulaCost = calculateFormulaPrice(courier_tariff_formula, parseFloat(fromWeight), parseFloat(toWeight), parseFloat(kgCostNew), parseFloat(pieceCostNew), 1, 1);
						}
					}
				}
				var totalColor = "success-color";
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

				totalFormulaCost = parseFloat(totalFormulaCost).toFixed(2);
				console.log("extras: "+totalExtras);
				console.log("Formula Cost before: "+totalFormulaCost);
				totalFormulaCost = (parseFloat(totalFormulaCost) + parseFloat(totalExtras)).toFixed(2);
				console.log("Formula Cost after: "+totalFormulaCost);
				var total_tariff_cost = $('#zone_total_input_' + weightStr + '_' + zoneId).val();
				if(parseFloat(totalFormulaCost) < parseFloat(total_tariff_cost)) {
					totalColor = "danger-color";
				} else if(parseFloat(totalFormulaCost) == parseFloat(total_tariff_cost)) {
					totalColor = "warning-color";
				}
				var html_total_formula_cost = '<span class="'+totalColor+'">'+totalFormulaCost+'</span>'
				var input_value = kgCostNew;
				var html_kg = '<span class="'+kgColor+'">'+kgCostNew+'</span>';
				var html_piece = '';
				// if(itemCost > 0) {
				input_value = kgCostNew + "/" + pieceCostNew;
				html_piece = '<span class="'+pieceColor+'">'+pieceCostNew+'</span>';
				// }
				if(service_validation_type == "mail") {
					$('#zone_formula_input_'+weightStr+'_'+zoneId).val(postal_tariff_formula);
				}else{
					$('#zone_formula_input_'+weightStr+'_'+zoneId).val(courier_tariff_formula);
				}
				$('#zone_new_input_'+weightStr+'_'+zoneId).val(input_value);

				$('#zone_new_kg_'+weightStr+'_'+zoneId).html(html_kg);

				$('#zone_new_piece_'+weightStr+'_'+zoneId).html(html_piece);
				$('#zone_new_total_input_'+weightStr+'_'+zoneId).val(totalFormulaCost);
				$('#zone_new_total_'+weightStr+'_'+zoneId).html(html_total_formula_cost);

			}
			function calculateFormulaPrice(formula, from_weight, to_weight, kg_cost, piece_cost, qty, reg){
				formula = formula.replaceAll("Q",qty);
				formula = formula.replaceAll("ITMCHR",piece_cost);
				formula = formula.replaceAll("FRMW", from_weight);
				formula = formula.replaceAll("TOW", to_weight);
				formula = formula.replaceAll("W",to_weight);
				formula = formula.replaceAll("CHRG",kg_cost);
				formula = formula.replaceAll("REG", reg);
				formula = formula.replaceAll("ceil","Math.ceil");
				return eval(formula);
			}
			function calculateNewCostManually(id_str,kg_value,kg_margin,piece_value,piece_margin,kgCost,itemCost,weight,extras) {
				var kgCostNew = parseFloat(kgCost);
				if(kg_value != ""  && $.isNumeric(kg_value)) {
					kgCostNew = (parseFloat(kgCost) + parseFloat(kg_value));
				}
				if(kg_margin != "") {
					var kgCostNew = (parseFloat(kgCostNew) + ((parseFloat(kg_margin)/100) * parseFloat(kgCostNew)));
				}
				var pieceCostNew = itemCost;
				if(piece_value != ""  && $.isNumeric(piece_value)) {
					pieceCostNew = (parseFloat(itemCost) + parseFloat(piece_value));
				}
				if(piece_margin != "") {
					pieceCostNew = (parseFloat(pieceCostNew) + ((parseFloat(piece_margin)/100) * parseFloat(pieceCostNew)));
				}
				if($('#zone_new_total_input_'+id_str).length == 0)
					kgCostNew = parseFloat(kgCostNew) + parseFloat(extras);


				kgCostNew = parseFloat(kgCostNew).toFixed(2);
				pieceCostNew = parseFloat(pieceCostNew).toFixed(2);
				var new_calculated_cost = (kgCostNew + '/' + pieceCostNew);
				if(itemCost <= 0) {
					new_calculated_cost = kgCostNew;
				}
				var totalFormulaCost = kgCostNew;
				/*if(pieceCostNew != "" && $.isNumeric(pieceCostNew)) {
					var weightArr = weight.split('/');
					var fromWeight = weightArr[0];
					var toWeight = weightArr[1];
					if(parseFloat(pieceCostNew) > 0) {
						totalFormulaCost = ((parseFloat(kgCostNew) * parseFloat(toWeight)) + parseFloat(pieceCostNew));
						//irshad
					}
				}*/

				var service_validation_type = $('#service_validation_type_'+ id_str).val();
				var _tariff_formula = $('#supplier_tariff_formula_'+ id_str).val();
				/*
				var courier_tariff_formula = "";
				var postal_tariff_formula = "";
				var _tariff_formula = "";
				supplier_tariff_formula
				if($("#courier_tariff_formula").length)
					courier_tariff_formula = $("#courier_tariff_formula").val();
				if($("#postal_tariff_formula").length)
					postal_tariff_formula = $("#postal_tariff_formula").val();
				if(service_validation_type == 'mail')
					_tariff_formula = postal_tariff_formula;
				else
					_tariff_formula = courier_tariff_formula;
				*/
				var weightArr = weight.split('/');
				var fromWeight = weightArr[0];
				var toWeight = weightArr[1];
				totalFormulaCost = calculateFormulaPrice(_tariff_formula, parseFloat(fromWeight), parseFloat(toWeight), parseFloat(kgCostNew), parseFloat(pieceCostNew), 1, 1);

				var kgColor = "success-color";
				var pieceColor = "success-color";
				var totalColor = "success-color";
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
				totalFormulaCost = parseFloat(totalFormulaCost) + parseFloat(extras);
				totalFormulaCost = parseFloat(totalFormulaCost).toFixed(2);
				var total_tariff_cost = $('#zone_total_input_'+id_str).val();
				if(parseFloat(totalFormulaCost) < parseFloat(total_tariff_cost)) {
					totalColor = "danger-color";
				} else if(parseFloat(totalFormulaCost) == parseFloat(total_tariff_cost)) {
					totalColor = "warning-color";
				}
				var html_total_formula_cost = '<span class="'+totalColor+'">'+totalFormulaCost+'</span>'
				var html_kg = '<span class="'+kgColor+'">'+kgCostNew+'</span>';
				var html_piece = '';
				// if(pieceCostNew > 0) {
				html_piece = '<span class="'+pieceColor+'">'+pieceCostNew+'</span>';
				// }
				$('#zone_new_input_'+id_str).val(new_calculated_cost);
				$('#zone_new_kg_'+id_str).html(html_kg);
				$('#zone_new_piece_'+id_str).html(html_piece);
				$('#zone_new_total_input_'+id_str).val(totalFormulaCost);
				$('#zone_new_total_'+id_str).html(html_total_formula_cost);
			}

			function getSelectValues(select) {
				var result = [];
				var options = select && select.options;
				var opt;

				for (var i=0, iLen=options.length; i<iLen; i++) {
					opt = options[i];
					if (opt.selected) {
						result.push(opt.value || opt.text);
					}
				}
				return result;
			}
			function getServiceOption() {
				var selectedToCountries = [];
				$('.to_country').each(function(){
					var id = $(this).attr('id');
					if(id != "undefined") {
						$("#"+id+" :selected").each(function(){
							selectedToCountries.push($(this).val());
						});
					}
				});
				var country_id = selectedToCountries;
				var tracked_untracked = $('#tracked_untracked').val();
				var mail_parcel = $('#mail_parcel').val();
				var mail_option = $('#mail_option').val();
				var mail_type = $('#mail_type').val();
				var service_type = $('#service_type').val();
				var thickness = $('#thickness').val();
				var delivery_type = $('#delivery_type').val();
				var tariff_start_date = $('#tariff_start_date').val();
				var tariff_end_date = $('#tariff_end_date').val();

				$.blockUI();
				$.ajax({
					type: "POST",
					url: "get_pricing.php",
					data: {func: "get_country_services_options", service_type: service_type, country_id: country_id, tracked_untracked: tracked_untracked, mail_parcel: mail_parcel, mail_option: mail_option ,mail_type: mail_type,thickness: thickness,delivery_type:delivery_type, tariff_start_date:tariff_start_date, tariff_end_date:tariff_end_date},
					dataType: "json",
					success: function (data) {
						$.unblockUI();
						if(data.status == "success") {
							$('#include_services').html(data.options);
							$('#include_services').multiSelect('refresh');
						}
					},
					error: function () {
						$.unblockUI();
						//alert('error handing here');
					}
				});
			}

			function showThicknessBox() {
				var mail_type = $('#mail_type').val();
				$('#thickness_box').hide();
				if(mail_type == "letter" || mail_type == "boxable") {
					$('#thickness_box').show();
				}
			}
		</script>
		<?php
	}

	protected function renderHead()
	{
		?>
		<style>
			#mail_type_box {
				display: none;
			}
			.input-group-btn:last-child>.btn, .input-group-btn:last-child>.btn-group {
				z-index: auto;
			}
			#customer_pricing_box .col-md-6{
				padding: 5px;
			}
			#customer_pricing_box td{
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
			/*.custom_table thead th { width: 410px;}*/
			.custom_table {width: auto;}
			.custom_table thead th:first-child { width: 125px;}
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
				width: 55px;
				border: 1px solid;
				min-height: 21px;
				text-align: center;
			}
			.inner_table_heading {
				width: 100%;
			}
			.service_column {
				min-width: 110px;
			}
			.inner_table_heading tr td {
				text-align: center;
				width: auto;
			}
			.inner_table {
				width: 100%;
			}
			.value_wd {
				width: 60px !important;
			}
			.customer_tariff_field {
				display: none;
			}
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
					<a href="currency_list.php" target="_blank" class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp; Currency List</a>
					<a href="linehaul.php" target="_blank" class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp; Add Linehual</a>
				</div>
				<div class="tools"></div>
			</div>
			<div class="portlet-body">
				<form name="frm_price_search" id="frm_price_search" method="post" action="">
					<div class="row">
						<div class="col-md-2">
							<label class="label-account">From Country</label>
							<div class="form-group">
								<?php
								$from_country = '225';
								if (!empty($this->form_vars['from_country']))
									$from_country = $this->form_vars['from_country'];
								echo Ddl::generateCountryDDL('from_country', $from_country, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" ');
								?>
							</div>
						</div>
						<div class="col-md-2">
							<label class="label-account">To Country Zone</label>
							<?php
							$zones = get_zones();
							?>
							<?php echo Ddl::generateArrayDDL('zone',$zones,'','','class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" ') ?>
						</div>
						<div class="col-md-8">
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-3">
											<label class="label-account">To Country</label>
										</div>
										<div class="col-md-3">
											<label class="label-account">From Weight</label>
										</div>
										<div class="col-md-3">
											<label class="label-account">To Weight</label>
										</div>
										<div class="col-md-3"></div>
									</div>
									<div class="pricing_range_container">
										<div class="form-group">
											<div class="row">
												<div class="col-md-3">
													<?php
													echo Ddl::generateCountryDDL('to_country[0][]', '', 'id', $attr = ' class="bs-select form-control to_country" multiple="multiple" required="" data-live-search="true" data-actions-box="true"  data-max-options="'.$this->toCountryLimit.'" data-container="body" data-size="8" onchange="getServiceOption()" ', $dd_id = 'to_country_0', $title = '', $showSelect = false);
													?>
												</div>
												<div class="col-md-3">
													<input type="number" name="from_weight[0]" class="form-control from_weight" id="from_weight_0"/>
												</div>
												<div class="col-md-3">
													<input type="number" name="to_weight[0]" class="form-control to_weight" id="to_weight_0"/>
												</div>
												<div class="col-md-3">
													<button type="button" class="btn btn-success add_more_pricing_range"><i class="fa fa-plus"></i></button>
													<button type="button" class="btn btn-danger remove_pricing_range initial-button"><i class="fa fa-minus"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2" id="mail_parcel_box">
							<label class="label-account">Postal/Courier</label>
							<div class="form-group">
								<select name="mail_parcel" id="mail_parcel" class="form-control select2" onchange="getServiceOption()">
									<option value="">All</option>
									<option value="mail" selected="selected">Postal</option>
									<option value="courier">Courier</option>
								</select>
							</div>
						</div>
						<div class="col-md-2" id="courier_type_box">
							<label class="label-account">Tracked/Untracked</label>
							<div class="form-group">
								<select name="tracked_untracked" id="tracked_untracked" class="form-control select2" onchange="getServiceOption()">
									<option value="">All</option>
									<option value="0">Tracked</option>
									<option value="1">Un-tracked</option>
								</select>
							</div>
						</div>
						<div class="col-md-2" id="economy_priority_box">
							<label class="label-account">Economy/Priority</label>
							<div class="form-group">
								<select name="delivery_type" id="delivery_type" class="form-control select2" onchange="getServiceOption()">
									<option value="all">All</option>
									<option value="economy">Economy</option>
									<option value="priority">Priority</option>
								</select>
							</div>
						</div>
                        <div class="col-md-2" id="mail_parcel_box">
                            <label class="label-account">Service Type</label>
                            <div class="form-group">
                                <?php
                                $serviceTypes = array('D' => 'Dispatched', 'C' => 'Collection', 'DO' => 'Drop-Off', 'R' => 'Return');
                                echo Ddl::generateArrayDDL('service_type', $serviceTypes, $service_type="" , 'Service Type', ' class="form-control select2 select" rel="tooltip" data-original-title="Origin Country" placeholder="Service Type" onchange="getServiceOption()"');
                                ?>
                            </div>
                        </div>
						<div class="col-md-12" id="mail_type_box">
							<div class="row">
								<div class="col-md-4">
									<label class="label-account">Mail Type</label>
									<div class="form-group">
										<?php
										$mailTypeOptions = mail_type_options();
										echo Ddl::generateArrayDDL("mail_type", $mailTypeOptions, $selected = "", $default_select = "", $attr = ' class="form-control select2" onchange="getServiceOption();showThicknessBox()"  ', $default_select_value = "", $dd_id = 'mail_type' ,$title='Mail Type')
										?>
									</div>
								</div>
								<div class="col-md-4">
									<label>Mail Options</label>
									<div class="form-group">
										<?php
										$mailOptions = mail_options();
										echo Ddl::generateArrayDDL("mail_option", $mailOptions, '', $default_select = "", $attr = ' class="form-control select2" onchange="getServiceOption()"  ', $default_select_value="", $dd_id='mail_option' ,$title='Mail Option')
										?>
									</div>
								</div>
								<div class="col-md-4" id="thickness_box" style="display: none;">
									<label>Thickness</label>
									<input type="number" name="thickness" class="form-control" id="thickness" onkeyup="getServiceOption()" />
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 full-width-multiselect">
							<label class="label-account">Service(s)</label>
							<div class="form-group">
								<?php
								echo Ddl::generateServiceDDLWithImage('include_services[]', "", 'id', ' class="multi-select multiselect_drop_down" multiple="multiple" required="" data-live-search="true" data-actions-box="true" data-container="body" data-size="8"','include_services','','','','',true);
								?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-4">
									<label class="label-account">Weight Limits</label>
									<div class="form-group">
										<select name="add_manual_weight_limit" id="add_manual_weight_limit" class="form-control select2 add_manual_weight_limit">
											<option value="auto">Auto</option>
											<option value="standard" selected="selected">Standard</option>
											<option value="manual">Manual</option>
											<option value="bespoke">Bespoke</option>
										</select>
									</div>
								</div>
								<div class="col-md-4" id="_add_manual_weight_limit_container">
									<label class="label-account">Manual Weight Limits <small>(Kg)</small></label>
									<div class="form-group">
										<input type="number" name="_add_manual_weight_limit" id="_add_manual_weight_limit" class="form-control add_manual_weight_limit" />
									</div>
								</div>
								<div class="col-md-4">
									<label class="label-account">&nbsp;</label><br />
									<div class="form-group">
										<input id="show_wWeight_breakets" name="show_wWeight_breakets" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"  />  Show Weight Breakets
									</div>
								</div>
							</div>
							<div class="row" id="manual_weight_limit_box">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-borderless">
											<thead>
											<tr>
												<th>From Weight Limit</th>
												<th>To Weight Limit</th>
												<th>Action</th>
											</tr>
											</thead>
											<tbody class="weight_limit_container">
											<tr class="clone_row">
												<td>
													<input type="number" class="form-control from_weight_limit" id="from_weight_limit_0" name="manual_weight_limit[from_weight][0]">
												</td>
												<td>
													<input type="number" class="form-control to_weight_limit" id="to_weight_limit_0" name="manual_weight_limit[to_weight][0]">
												</td>
												<td>
													<button type="button" class="btn btn-success add_more_weight_limit"><i class="fa fa-plus"></i></button>
													<button type="button" class="btn btn-danger remove_weight_limit initial-button"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="margin-bottom-0 margin-right-10">
									<input id="advance_search_option" name="advance_search_option" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"  />  Advance search option
									<span></span>
								</label>
							</div>
						</div>
					</div>
					<div id="advance_search_fields">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label>Currency</label>
									<?php
									$selectedCurrencyId = 2;
									if(!empty($this->form_vars['currency_id']))
										$selectedCurrencyId = $this->form_vars['currency_id'];
									echo Ddl::generateDDL('currency_id', 'CurrencyFilter', "AND isactive='1' AND ClientDisplay='1'", 'currencyname', 'id', $selectedCurrencyId, ' class="input-sm form-control select2 validate_check"', 'Select Currency', '');
									?>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label>Currency Conversion Rate for pounds</label>
									<input type="text" name="currency_conversion_rate" class="form-control" id="currency_conversion_rate"/>
								</div>
							</div>
							<div class="col-md-2">
								<label class="label-account">Transit Time
									<small>(up to)</small>
								</label>
								<div class="form-group">
									<?php
									$transitDays = [];
									$transitDays[1] = 'Next day delivery';
									for ($day = 2; $day <= 30; $day++) {
										$transitDays[$day] = $day . ' days';
									}
									$transit_day = (isset($this->form_vars['transit_days']) ? $this->form_vars['transit_days'] : '');
									echo Ddl::generateArrayDDL('transit_days', $transitDays, $transit_day, 'Select Time', 'class="form-control select2"');
									?>
								</div>
							</div>
                            <div class="col-md-2">
                                <label class="label-account">View Only</label>
                                <div class="form-group">
                                    <?php
                                    $viewOnly = [
                                        't3' => 'View Top 3',
                                        't5' => 'View Top 5',
                                        'l3' => 'View Last 3',
                                        'l5' => 'View Last 5',
                                        'all' => 'View All'
                                    ];
                                    $view_only = (isset($this->form_vars['view_only']) ? $this->form_vars['view_only'] : 't3');
                                    echo Ddl::generateArrayDDL('view_only', $viewOnly, $view_only, '', 'class="form-control select2"');
                                    ?>
                                </div>
                            </div>
							<div class="col-md-2">
								<label class="label-account">Start Date</label>
								<div class="form-group">
									<div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
										<input type="text" class="form-control input-sm validate_check" readonly name="tariff_start_date" id="tariff_start_date" placeholder="" value="" onchange="getServiceOption()" />
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<label class="label-account">Expiry Date</label>
								<div class="form-group">
									<div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
										<input type="text" class="form-control input-sm validate_check" readonly name="tariff_end_date" id="tariff_end_date" placeholder="" value="" onchange="getServiceOption()" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label class="label-show-extras">Extra Charges</label>
									<?php
									echo Ddl::generateDDL('extra_charge_type_id[]', 'ConsignmentChargesTypesFilter', " charge_type != 'agent' AND is_extra_charge='1' AND status='1' AND is_delete='0'", 'title', 'id', '', ' class="form-control bs-select"  multiple="multiple" data-live-search="true" data-container="body"', 'Select Charge Type', '','extra_charge_type_id');
									?>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group margin-top-30">
									<label class="label-show_kg">
										<input id="include_kg_cost" name="include_kg_cost" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"  /> Show Kg Cost
									</label>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group margin-top-30">
									<label class="label-show_piece">
										<input id="include_piece_cost" name="include_piece_cost" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"  /> Show Piece Cost
									</label>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group margin-top-30">
									<label class="label-manual_weight_limit">
										<input id="cheapest_value_show" name="cheapest_value_show" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"  /> Select 2nd Cheapest
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<label class="label-account">&nbsp;</label>
							<div class="form-group text-center">
								<input type="hidden" name="func" value="get_tariff"/>
								<button type="button" name="btn_search_tariff" id="btn_search_tariff" class="btn btn-primary">Search </button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="portlet light all_portlet" id="tariff_portlet">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-bar-chart"></i> Compare Pricing</div>
				<div class="actions">
					<div class="btn-group btn-group-devided" data-toggle="buttons">
						<button type="button" class="btn btn-sm btn-info" id="download_routing_with_pricing" >Download Routing With Pricing</button>
						<button type="button" class="btn btn-sm btn-info" id="download_routing" >Download Routing</button>
						<button type="button" class="btn btn-sm btn-primary" id="download_tariff_csv" >Download Tariff</button>
						<button type="button" class="btn btn-sm green" id="customer_pricing">Customer Pricing</button>
						<button type="button" class="btn btn-sm btn-default" id="save_tariff_modal" >Save Tariff</button>
						<button type="button" class="btn btn-sm btn-default download_calculated_pricing" data-download_type="both" >Download Pricing</button>
						<button type="button" class="btn btn-sm btn-default download_calculated_pricing_with_linehual" data-download_type="both" >Download Pricing with Linehual</button>
						<!--                        <button type="button" class="btn btn-sm btn-default download_calculated_pricing" data-download_type="cost" >Download Pricing Cost</button>-->
						<!--                        <button type="button" class="btn btn-sm btn-default download_calculated_pricing" data-download_type="service" >Download Pricing Service</button>-->
						<button type="button" class="btn btn-sm btn-primary" id="btn_customer_pricing" >Customer Pricing</button>
						<button type="button" class="btn btn-sm btn-primary" id="again_search_pricing" >Search Again</button>
					</div>
				</div>
			</div>
			<form name="download_csv_pricing_form" id="download_csv_pricing_form" method="post" action="">
				<input type="hidden" name="func" value="download_calculated_pricing" />
				<input type="hidden" name="csv_string" id="csv_string" value="" />
			</form>
            <form name="download_csv_pricing_with_linehual_form" id="download_csv_pricing_with_linehual_form" method="post" action="">
                <input type="hidden" name="func" value="download_calculated_pricing_with_linehual" />
                <input type="hidden" name="csv_strings" id="csv_strings" value="" />
            </form>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<form name="get_pricing_form" id="get_pricing_form" method="post" action="">
							<input type="hidden" name="func" value="calculate_pricing" />
							<input type="hidden" name="tariff_json" id="tariff_json" value="" />
						</form>
						<div class="table-responsive">
							<div class="portlet-body flip-scroll">
								<div class="text-center margin-bottom-10" id="rate_filter_container"></div>
								<div class="tableFixHead">
									<table class="table table-bordered table-striped table-condensed flip-content tariff_table">
										<thead class="flip-content" id="tariff_response_head">
										</thead>
										<tbody id="tariff_response">
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="portlet light all_portlet" id="customer_pricing_portlet">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-bar-chart"></i> Customer Pricing</div>
				<div class="actions">
					<div class="btn-group btn-group-devided" data-toggle="buttons">
						<button type="button" class="btn btn-sm btn-primary" id="btn_compare_pricing" >Compare Pricing</button>
					</div>
				</div>
			</div>
			<div class="portlet-body">
				<div class="row margin-bottom-10" id="tariff_single_pricing">
					<fieldset class="fieldset">
						<legend>Whole Tariff Price:</legend>
						<div class="form-group">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table whole_tariff_price_tbl">
										<thead>
										<tr>
											<th colspan="2">Items</th>
											<th colspan="2">Klio</th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td>
												<label>Add Item Value</label>
												<input type="text" name="whole_tariff_line_haul_it" id="whole_tariff_line_haul_it" class="form-control whole-tariff-line-haul">
											</td>
											<td>
												<label>Add Item Margin</label>
												<div class="input-group">
													<input type="text" name="whole_tariff_margin_it" id="whole_tariff_margin_it" class="form-control">
													<div class="input-group-btn">
														<select class="selectpicker form-control" name="whole_tariff_margin_type_it" id="whole_tariff_margin_type_it" data-container="body" >
															<option value="percentage">%</option>
															<option value="price">Fixed</option>
														</select>
													</div>
												</div>
											</td>
											<td>
												<label>Add Weight Value</label>
												<input type="text" name="whole_tariff_line_haul_wg" id="whole_tariff_line_haul_wg" class="form-control whole-tariff-line-haul">
											</td>
											<td>
												<label>Add Weight Margin</label>
												<div class="input-group">
													<input type="text" name="whole_tariff_margin_wg" id="whole_tariff_margin_wg" class="form-control">
													<div class="input-group-btn">
														<select class="selectpicker form-control" name="whole_tariff_margin_type_wg" id="whole_tariff_margin_type_wg" data-container="body" >
															<option value="percentage">%</option>
															<option value="price">Fixed</option>
														</select>
													</div>
												</div>
											</td>
										</tr>
										</tbody>
									</table>
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
								<div class="table-responsive">
									<table class="table" style="margin-bottom: 0px;">
										<thead>
										<tr>
											<td style="width: 220px;"><label>Destination</label></td>
											<td><label>Weight</label></td>
											<td><label>Add Item Value</label></td>
											<td><label>Add Item Margin</label></td>
											<td><label>Add Weigth Value</label></td>
											<td><label>Add Weight Margin</label></td>
											<td style="min-width: 105px;">&nbsp;</td>
										</tr>
										</thead>
										<tbody class="tariffs-pricing-container" id="tariff_detail_pricing">
										<tr>
											<td>
												<div class="input-group">
													<span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
													<span id="zone_to_span">
                                                            <select class="form-control select2 to_zone_id zone-to-id" name="apply[0][to_zone_id]" id="to_zone_id_0">
                                                                <option value="">Select Zone</option>
                                                            </select>
                                                        </span>
												</div>
											</td>
											<td>
												<div class="input-group">
													<input type="text" class="form-control weight-from" min="0" name="apply[0][weight_from]" id="weight_from_0" value="" >
													<span class="input-group-addon"> to </span>
													<input type="text" class="form-control weight-to" min="0" name="apply[0][weight_to]" id="weight_to_0" value="" >
												</div>
											</td>
											<td>
												<input type="text" name="apply[0][line_haul_it]" class="form-control tariff-line-haul-it" id="line_haul_it_0">
											</td>
											<td>
												<div class="input-group">
													<input type="text" class="form-control tariff-margin-it" name="apply[0][margin_it]" id="margin_it_0" >
													<div class="input-group-btn">
														<select class="selectpicker form-control tariff-margin-type-it" name="apply[0][margin_type_it]" id="margin_type_it_0" data-container="body" >
															<option value="percentage">%</option>
															<option value="price">Fixed</option>
														</select>
													</div>
												</div>
											</td>
											<td>
												<input type="text" name="apply[0][line_haul_wg]" class="form-control tariff-line-haul-wg" id="line_haul_wg_0">
											</td>
											<td>
												<div class="input-group">
													<input type="text" class="form-control tariff-margin-wg" name="apply[0][margin_wg]" id="margin_wg_0" >
													<div class="input-group-btn">
														<select class="selectpicker form-control tariff-margin-type-wg" name="apply[0][margin_type_wg]" id="margin_type_wg_0" data-container="body" >
															<option value="percentage">%</option>
															<option value="price">Fixed</option>
														</select>
													</div>
												</div>
											</td>
											<td>
												<button type="button" class="btn btn-success add-more-tariff-pricing-keys"><i class="fa fa-plus"></i></button>
												<button type="button" class="btn btn-danger remove-tariff-pricing-key initial-button"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset class="fieldset" style="margin-top: 10px; display: none;">
						<legend>Tariff Pricing Formula:</legend>
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Postal Tariff Formula</label>
										<select name="postal_tariff_formula" id="postal_tariff_formula" class="form-control select2 input-sm">
											<?php
											$tariffFormulas = get_tariff_formulas();
											if(!empty($tariffFormulas)){
												foreach($tariffFormulas as $tariffFormula => $tariffFormulaText){
													$selected = "";
													if($tariffFormula == "( Q * ITMCHR ) + ( W * CHRG )")
														$selected = ' selected="selected"';
													?>
													<option value="<?php echo $tariffFormula; ?>"<?php echo $selected; ?>><?php echo $tariffFormulaText; ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Courier Tariff Formula</label>
										<select name="courier_tariff_formula" id="courier_tariff_formula" class="form-control select2 input-sm">
											<?php
											$tariffFormulas = get_tariff_formulas();
											if(!empty($tariffFormulas)){
												foreach($tariffFormulas as $tariffFormula => $tariffFormulaText){
													$selected = "";
													if($tariffFormula == "Q * CHRG")
														$selected = ' selected="selected"'
													?>
													<option value="<?php echo $tariffFormula; ?>"<?php echo $selected; ?>><?php echo $tariffFormulaText; ?></option>
													<?php
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
					<span id="customer_extra_box"></span>
					<div class="col-sm-12" style="margin-top: 5px;">
						<div class="row">
							<div class="col-md-12">
								<button type="button" class="btn btn-success btn-sm" id="calculate_price">Apply</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<form action="get_pricing.php" id="customer_pricing_from" method="post">
						<div class="col-md-12" id="customer_pricing_box"></div>
					</form>
					<form action="get_pricing.php" id="customer_pricing_excel_form" method="post">
						<input type="hidden" name="func" value="download_tariff_pricing_excel" />
						<input type="hidden" name="customer_tariff" id="customer_tariff_excel" value="" />
					</form>
					<form action="get_pricing.php" id="demo_customer_pricing_excel_form" method="post">
						<input type="hidden" name="func" value="download_demo_tariff_excel" />
						<input type="hidden" name="customer_tariff" id="demo_customer_tariff_excel" value="" />
						<input type="hidden" name="tariff_currency_id" id="demo_tariff_currency_id" value="" />
					</form>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<button type="button" class="btn btn-sm btn-default" id="save_all_tariff_modal" >Save All Tariff</button>
						<button type="button" class="btn btn-sm blue" id="excel_download_btn" ><span></span><i class="fa fa-download"></i>&nbsp;Preview Tariff </button>
						<button type="button" class="btn btn-sm blue" id="demo_tariff_excel_download_btn" ><span></span><i class="fa fa-download"></i>&nbsp;Demo Tariff </button>
					</div>
				</div>
			</div>
		</div>
		<form name="download_tariff_csv_frm" id="download_tariff_csv_frm" target="_blank" method="post" action="tariffs_list.php">
			<input type="hidden" name="func" value="download_tariff_detail_csv" />
			<input type="hidden" name="tariffId" id="tariffId" value="" />
		</form>
		<form name="download_routing_frm" id="download_routing_frm" method="post" action="get_pricing.php">
			<input type="hidden" name="func" value="download_routing_excel" />
			<input type="hidden" name="routing_pricing_download" id="routing_pricing_download" value="0" />
			<input type="hidden" name="routing_pricing_include_piece_cost" id="routing_pricing_include_piece_cost" value="0" />
			<input type="hidden" name="routing_pricing_include_kg_cost" id="routing_pricing_include_kg_cost" value="0" />
			<input type="hidden" name="routing_tariff_id" id="routing_tariff_id" value="" />
			<input type="hidden" name="cutomized_service_id_for_routing" id="cutomized_service_id_for_routing" value="" />
			<input type="hidden" name="import_csv_format" value="import_csv" />
		</form>
		<!--portlet-body-->
		<!-- Modal -->
		<div id="saveTariffModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Save Tariff Header</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="loss" id="loss" />
						<input type="hidden" name="tariff_save_with_customer_tariff" id="tariff_save_with_customer_tariff" />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Tariff Type</label><br/>
									<?php $chkActive = 1 ?>
									<input <?php echo($chkActive == '1' ? 'checked="checked"' : ''); ?> name="tariff_type" id="tariff_type" type="checkbox" class="make-switch" data-on-text="New" check data-off-text="Update" data-on-color="primary" data-off-color="danger">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group12">
									<label>Carrier</label>
									<div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-bars"></i></span>
										<?php
										// Ask for DDL
										echo Ddl::generateCarrierDDLWithImage('carrier_id', '', 'id', ' class="bs-select form-control" data-live-search="true" data-show-subtext="true"');
										?>
									</div>
								</div>
							</div>
							<div id="new_tariff">
								<div class="col-md-6">
									<div class="form-group">
										<label>Service Name</label>
										<input type="text" class="form-control" name="service_name" id="service_name" rel="tooltip" data-original-title="Service Name" placeholder="Service Name" value="owes_<?php echo time(); ?>" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Service Code</label>
										<input type="text" class="form-control" name="service_code" id="service_code" rel="tooltip" data-original-title="Service Code" placeholder="Service Code" value="owesc_<?php echo time(); ?>" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Tariff Name</label>
										<div class="input-group">
											<span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
											<input type="text" name="tariff_name" id="tariff_name" class="form-control validate_check" value="owet_<?php echo time(); ?>" />
										</div>
									</div>
								</div>
							</div>
							<div id="edit_tariff">
								<div class="col-md-6">
									<div class="form-group">
										<label>Service</label>
										<select name="service_id" id="service_id" class="select2">
											<option value="">Select Service</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Tariff</label>
										<select name="tariff_id" id="tariff_id" class="select2">
											<option value="">Select Tariff</option>
										</select>
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
						<button type="button" class="btn btn-primary" id="tariff_action">Save</button>
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
