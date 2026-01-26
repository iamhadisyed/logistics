<?php
// get settings
require_once("../includes/settings/config.inc.php");
ini_set('max_input_vars', 50000);
include_classes([
    'carrier.class',
    'carrierfilter.class',
    'currency.class',
    'currencyfilter.class',
//                    'carrierzones.class',
//                    'carrierzonesfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'services.class' ,
    'servicefilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class'
]);
class Page extends BasePage
{

    public $user;
    private $tarifObj = array();
    private $tariffId = 0;
    private $fromZoneId = 0;
    private $toZoneId = 0;
    private $tariffDetailsObj = array();
    private $msg;
    private $params = "";

    protected function init()
    {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Add/Update Tariff"
        );

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $this->tariffId = $_GET['id'];
        }

        if ((isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) && (isset($_GET['service_id']) && !empty($_GET['service_id']))) {
            $this->params = "carrier_id=" . $_GET['carrier_id'] . "&service_id=" . $_GET['service_id'];
        } else {
            if (isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                $this->params = "carrier_id=" . $_GET['carrier_id'];
            }
            if (isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                $this->params = "service_id=" . $_GET['service_id'];
            }
        }
        $this->tarifObj = new Tariffs();
        $this->user = SessionManager::getUser();
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_tariff') {
            $user_account_id = $this->user->getUserAccountId();
            $carrier_id = $this->form_vars['carrier_id'];
            $service_id = $this->form_vars['service_id'];
            $name = $this->form_vars['tariff_name'];

            if (isset($this->form_vars['chkType']) && ($this->form_vars['chkType'] == 'on')) {
                $type = 'Customer';
            } else {
                $type = 'Supplier';
            }
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
            $tariffsObj = new Tariffs();
            if (is_numeric($this->form_vars['tariff_id']) && $this->form_vars['tariff_id'] > 0) {
                $tariffsObj = new Tariffs($this->form_vars['tariff_id']);
            }
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
            $tariffsObj->setDateAdded($date_added);
            $tariffsObj->setAddedBy($added_by);
            $tariffsObj->setDateUpdated($date_update);
            $tariffsObj->setUpdatedBy($update_by);
            /* if (is_numeric($this->form_vars['tariff_id']) && $this->form_vars['tariff_id'] > 0) {
                    $tariffsObj->eventKey = "detailUpdate";
                    $tariffsObj->auditdataTitle = $tariffsObj->getName();
                    $tariffsObj->auditdataType = "tariff";
                }else{
                    $tariffsObj->eventKey = "detailInsert";
                    $tariffsObj->auditdataTitle = $name;
                    $tariffsObj->auditdataType = "tariff";
                }*/
            $tariffsObj->save();
            /* Trarif Details */
            $tarif_id = $tariffsObj->getId();
            $this->msg = 'Tariff has been added successfully.';
            if (is_numeric($this->form_vars['tariff_id']) && $this->form_vars['tariff_id'] > 0) {
                $tarif_id = $this->form_vars['tariff_id'];
                $this->msg = 'Tariff has been updated successfully.';
            }

            TariffsDetails::deleteTarifsDetailByTarifId($tarif_id);
            if (isset($this->form_vars['chkDetailActive']) && ($this->form_vars['chkDetailActive'] == 'on')) {
                $checkDetailsBoxSelect = 1;
            } else {
                $checkDetailsBoxSelect = 0;
            }
            if ($checkDetailsBoxSelect > 0) {
                foreach ($this->form_vars['tariffs'] as $tf) {
                    $from_zone_id = $tf['from_zone_id'];
                    $to_zone_id = $tf['to_zone_id'];
                    $weight_from = $tf['weight_from'];
                    $weight_to = $tf['weight_to'];
                    $user_weight_cost = !empty($tf['user_weight_cost']) ? $tf['user_weight_cost'] : '0.00';
                    $user_peice_cost = !empty($tf['user_peice_cost']) ? $tf['user_peice_cost'] : '0.00';
                    $formula = $tf['formula'];
                    $tariffsDetailObj = new TariffsDetails();
                    $tariffsDetailObj->setTariffsId($tarif_id);
                    $tariffsDetailObj->setFromZoneId($from_zone_id);
                    $tariffsDetailObj->setToZoneId($to_zone_id);
                    $tariffsDetailObj->setWeightFrom($weight_from);
                    $tariffsDetailObj->setWeightTo($weight_to);
                    $tariffsDetailObj->setWeightCost($user_weight_cost);
                    $tariffsDetailObj->setPieceCost($user_peice_cost);
                    $tariffsDetailObj->setFormula($formula);
                    $tariffsDetailObj->save();
                }
            } else {
                $carrierObj = new Carrier($carrier_id);
                $carrierZoneBase = $carrierObj->getZoneBase();
                $tariffsCsv = $this->form_vars['tariffs_csv'];
                foreach($tariffsCsv as $tariff) {
                    $weight = explode('-',$tariff['weight']);
                    $w_from = '';
                    $w_to = '';
                    if (count($weight) > 0) {
                        $w_from = $weight[0];
                        $w_to = $weight[1];
                    }
                    $fromZoneName = $tariff['from_zones'];
                    $toZoneName = $tariff['to_zones'];
                    $weightCost = $tariff['weight_cost'];
                    $pieceCost = $tariff['piece_cost'];
                    $formula = $tariff['formula'];

                    $zoneToId = "";
                    $zoneFromId = "";

                    /* Get Zone Id From Name */
                    $carrierZonesNameFilter = new CarrierZonesFilter();
                    $carrierZonesNameFilter->addIsDeletedFilter();
                    $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($fromZoneName)));
                    $carrierZonesNameFilter->addFieldFilter('cz.carrier_id', $carrier_id);
                    $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                    if ($carrierZoneBase == 0) {
                        $carrierZonesNameFilter->addFieldFilter('cz.service_id', $service_id);
                    }
                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                    if (count($zoneFilterObj) > 0) {
                        $zoneToId = $zoneFilterObj[0]->getId();
                    }
                    /* Get Zone Id From Name end */

                    /* Get Zone Id From Name */
                    $carrierZonesNameFilter = new CarrierZonesFilter();
                    $carrierZonesNameFilter->addIsDeletedFilter();
                    $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($toZoneName)));
                    $carrierZonesNameFilter->addFieldFilter('cz.carrier_id', $carrier_id);
                    $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                    if ($carrierZoneBase == 0) {
                        $carrierZonesNameFilter->addFieldFilter('cz.service_id', $service_id);
                    }
                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                    if (count($zoneFilterObj) > 0) {
                        $zoneFromId = $zoneFilterObj[0]->getId();
                    }
                    /* Get Zone Id From Name end */

                    $tariffsDetailObj = new TariffsDetails();
                    $tariffsDetailObj->setTariffsId($tarif_id);
                    $tariffsDetailObj->setFromZoneId($zoneFromId);
                    $tariffsDetailObj->setToZoneId($zoneToId);
                    $tariffsDetailObj->setWeightFrom($w_from);
                    $tariffsDetailObj->setWeightTo($w_to);
                    $tariffsDetailObj->setWeightCost($weightCost);
                    $tariffsDetailObj->setPieceCost($pieceCost);
                    $tariffsDetailObj->setFormula($formula);
                    $tariffsDetailObj->save();
                }
            }
            $this->flashMsg->success($this->msg);
            /* End Trarif Details */
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_carrier_services') {
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = $this->form_vars['service_id'];
            $ServiceFilter = new ServiceFilter();
            $ServiceFilter->addCarrierFilter($carrierId);
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
            $output = '<select name="service_id" id="service_id" class="form-control validate_check" data-original-title="" title=""><option value="">Select Service</option>';
            if (count($servicesList) > 0) {
                foreach ($servicesList as $service) {
                    $selected = ($service->getId() == $serviceId ? ' selected="selected"' : '');
                    $output .= '<option value="' . $service->getId() . '"' . $selected . '>' . $service->getName() . ' [' . $service->getCode() . ']</option>';
                }
            }
            $output .= '</select>';
            echo $output;
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_carrier_zone_ddl') {
            $output = '';
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = $this->form_vars['service_id'];
//            $zoneId = $this->form_vars['zone_id'];
            $carrierObj = new Carrier($carrierId);
            $carrierZoneBase = $carrierObj->getZoneBase();
            $filters = '    ';
            $filters .= "cz.status ='1' AND cz.carrier_id = '" . $carrierId . "'";

            $carrierZonesFilter = new CarrierZonesFilter();
            $carrierZonesFilter->addIsDeletedFilter();
            $carrierZonesFilter->addFieldFilter('cz.status', 1);
            $carrierZonesFilter->addFieldFilter('cz.carrier_id', $carrierId);
            if ($carrierZoneBase == 0) {
                $carrierZonesFilter->addFieldFilter('cz.service_id', $serviceId);
            }
            $carrierZones = $carrierZonesFilter->getColumnList('id,name');
            if (count($carrierZones) > 0) {
                foreach ($carrierZones as $carrierZone) {
                    $selected = ''; //($carrierZone->getId() == $zoneId ? ' selected="selected"' : '');
//                    $output .= '<option value="">Select Zone To</option>';
                    $output .= '<option value="' . $carrierZone->getId() . '"' . $selected . '>' . $carrierZone->getName() . '</option>';
                }
            }
            echo $output;
            exit;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            $carrierId = $this->form_vars['carrier_id'];

            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $account = $this->user->getAccount();
                    $new_file_name = "tariff_" . time() . "_" . $basename;
                    $relPath = '../_assets/tariff_csv/' . $new_file_name;
                    if (!file_exists("../_assets/tariff_csv/")) {
                        @mkdir("../_assets/tariff_csv/", 0775);
                    }
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        $headings = array();
                        $values = array();
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle)) !== FALSE) {
                                $num = count($data);
                                for ($c = 0; $c < $num; $c++) {
                                    if ($row == 1) {
                                        $headings[] = $data[$c];
                                    } else {
                                        $values[$row][] = $data[$c];
                                    }
                                }
                                $row++;
                            }
                            fclose($handle);
                        }
                        /* tariff html of csv data */
                        $html = '<thead>';
                        $html .= '<tr>';
                        foreach ($headings as $heading) {
                            $html .= '<th class="bg-blue"><b>'.$heading.'</b></th>';
                        }
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';
                        foreach ($values as $key => $value) {
                            $weight = $value[0];
                            $fromZone = trim($value[1]);
                            $toZone = trim($value[2]);
                            $weightCost = $value[3];
                            $pieceCost = $value[4];
                            $formula = $value[5];

                            $html .= '<tr>';
                            $html .= '<td class="bg-info">';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][weight]" id="" class="form-control" value="' . $weight . '">';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][from_zones]" id="" class="form-control" value="' . $fromZone . '">';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][to_zones]" id="" class="form-control" value="' . $toZone . '">';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][weight_cost]" id="" class="form-control" value="' . $weightCost . '">';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][piece_cost]" id="" class="form-control" value="' . $pieceCost . '">';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<input type="text" name="tariffs_csv['.$key.'][formula]" id="" class="form-control" value="' . $formula . '">';
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                        $html .= '</tbody>';
                        /* end tariff html of csv data */
                        $output['html'] = $html;
                        $output['status'] = 'success';
                        unlink($relPath);
                    } else {
                        $output['message'] = 'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = 'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'check_carrier_zone_validation') {
            $output = array();
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = $this->form_vars['service_id'];
            $zones = $this->form_vars['zones'];
            $carrierObj = new Carrier($carrierId);
            $carrierZoneBase = $carrierObj->getZoneBase();
            $filters = '    ';
            $filters .= "cz.status ='1' AND cz.carrier_id = '" . $carrierId . "'";
            $carrierZonesFilter = new CarrierZonesFilter();
            $carrierZonesFilter->addIsDeletedFilter();
            $carrierZonesFilter->addFieldFilter('cz.status', 1);
            $carrierZonesFilter->addFieldFilter('cz.carrier_id', $carrierId);
            if ($carrierZoneBase == 0) {
                $carrierZonesFilter->addFieldFilter('cz.service_id', $serviceId);
            }
            $carrierZones = $carrierZonesFilter->getColumnList('id,name');

            $zoneAgainstCarrier = array();
            if (count($carrierZones) > 0) {
                foreach ($carrierZones as $carrierZone) {
                    $zoneAgainstCarrier[] = $carrierZone->getId();
                }
            }
            if (count($zones) > 0) {
                foreach ($zones as $zone) {
                    $carrierZonesNameFilter = new CarrierZonesFilter();
                    $carrierZonesNameFilter->addIsDeletedFilter();
                    $carrierZonesNameFilter->addFieldLikeFilter('cz.name', $zone);
                    $carrierZonesNameFilter->addFieldFilter('cz.carrier_id', $carrierId);
                    $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                    if ($carrierZoneBase == 0) {
                        $carrierZonesNameFilter->addFieldFilter('cz.service_id', $serviceId);
                    }
                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('name,id');
                    if (count($zoneFilterObj) > 0) {
                        $checkZoneId = $zoneFilterObj[0]->getId();
                        if (in_array($checkZoneId, $zoneAgainstCarrier)) {
                            $output['valid_zone'][] = [
                                'id' => $zoneFilterObj[0]->getId(),
                                'name' => $zone,
                            ];
                        } else {
                            $errorZone = $zone;
                            $output['invalid_zone'][] = $errorZone;
                        }
                    } else {
                        $errorZone = $zone;
                        $output['invalid_zone'][] = $errorZone;
                    }
                }
            }

            echo json_encode($output);
            exit;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_tariff_csv') {
            //Get Tariff Detail from mapping table
            $tariffsDetailsFilter = new TariffsDetailsFilter();
            $tariffsDetailsFilter->addFieldFilter('   tariffs_id', $this->tariffId);
            $tariffsDetailsFilter->addOrderBy("     td.weight_from", true);
            $tariffDetailsCsvObj = $tariffsDetailsFilter->getColumnList('td.id,td.tariffs_id,td.from_zone_id,td.to_zone_id,td.weight_from,td.weight_to,td.weight_cost,td.piece_cost,td.formula');
            if (!empty($tariffDetailsCsvObj)) {
                $tariff_details = array();
                $zones = array();
                foreach ($tariffDetailsCsvObj as $k => $tariffsDetailsObj) {
                    $weight_from = $tariffsDetailsObj->getWeightFrom();
                    $weight_to = $tariffsDetailsObj->getWeightTo();
                    $weight = $weight_from . "/" . $weight_to;
                    $zone_id = $tariffsDetailsObj->getToZoneId();
                    $tariff_details[$weight][$zone_id] = $tariffsDetailsObj->getWeightCost() . "/" . $tariffsDetailsObj->getPieceCost();
                    $tariff_details[$weight]['formula'] = $tariffsDetailsObj->getFormula();
                    if (!in_array($zone_id, $zones)) {
                        $zones[] = $zone_id;
                    }
                    if ($k == (count($tariffDetailsCsvObj) - 1)) {
                        $zones[] = 'formula';
                    }
                }
                sort($zones);
                foreach ($tariff_details as $k => $td) {
                    foreach ($zones as $zk => $z) {
                        if ($zk != (count($zones) - 1)) {
                            if (!isset($tariff_details[$k][$z])) {
                                $tariff_details[$k][$z] = '';
                                ksort($tariff_details[$k]);
                            }
                        }
                    }
                }
                $returnString = "Weight/zone,";
                foreach ($zones as $zk => $zone) {
                    if ($zk != (count($zones) - 1)) {
                        $toZone = new CarrierZones($zone);
                        $returnString .= $toZone->getName() . ",";
                    }
                }
                $returnString .= "formula,";
                $fileName = "tariff_details_" . $this->form_vars['tariff_id'] . "_" . time();
                foreach ($tariff_details as $weight => $detail) {
                    $returnString .= "\r\n";
                    $returnString .= $weight . ",";
                    foreach ($detail as $k => $dt) {
                        if ($k != "formula") {
                            $returnString .= $dt . ",";
                        }
                    }
                    $returnString .= $detail['formula'] . ",";
                }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $returnString;
                die();
            } else {
                $msg = 'Tariff not found.';
                $this->flashMsg->success($msg);
            }
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'load_tariff_details') {
            $tariffId = $this->form_vars['tariff_id'];
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
            $tdhtml = '';
            $tdhtml .= '<div class="table-responsive">';
            $tdhtml .= '<table class="table table-bordered fixed custom_table" id="tarif_csv_data">';
            $tdhtml .= '<thead>';
            $tdhtml .= '<tr>';
            $tdhtml .= '<th class="bg-blue"><b>Weight</b></th>';
            $tdhtml .= '<th class="bg-blue"><b>From Zone</b></th>';
            $tdhtml .= '<th class="bg-blue"><b>To Zone</b></th>';
            $tdhtml .= '<th class="bg-blue"><b>Weight Cost</b></th>';
            $tdhtml .= '<th class="bg-blue"><b>Piece Cost</b></th>';
            $tdhtml .= '<th class="bg-blue"><b>Formula</b></th>';
            $tdhtml .= '</tr>';
            $tdhtml .= '</thead>';
            $tdhtml .= '<tbody>';

            if (!empty($tariffDetails)) {
                foreach ($tariffDetails as $k => $tariffsDetailsObj) {
                    $weight_from = $tariffsDetailsObj->getWeightFrom();
                    $weight_to = $tariffsDetailsObj->getWeightTo();
                    $weight = $weight_from . "-" . $weight_to;
                    $from_zone_id = $tariffsDetailsObj->getFromZoneId();
                    $to_zone_id = $tariffsDetailsObj->getToZoneId();
                    $weightCost = $tariffsDetailsObj->getWeightCost();
                    $pieceCost = $tariffsDetailsObj->getPieceCost();
                    $formula = $tariffsDetailsObj->getFormula();

                    $fromZoneObj = new CarrierZones($from_zone_id);
                    $toZoneObj = new CarrierZones($to_zone_id);
                    $tdhtml .= '<tr>';
                    $tdhtml .= '<td class="bg-info">';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][weight]" id="" class="form-control" value="' . $weight . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '<td>';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][from_zones]" id="" class="form-control" value="' . $fromZoneObj->getName() . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '<td>';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][to_zones]" id="" class="form-control" value="' . $toZoneObj->getName() . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '<td>';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][weight_cost]" id="" class="form-control" value="' . $weightCost . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '<td>';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][piece_cost]" id="" class="form-control" value="' . $pieceCost . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '<td>';
                    $tdhtml .= '<input type="text" name="tariffs_csv['.$k.'][formula]" id="" class="form-control" value="' . $formula . '">';
                    $tdhtml .= '</td>';
                    $tdhtml .= '</tr>';
                }
            }
            $tdhtml .= '</tbody>';
            $tdhtml .= '</table>';
            $tdhtml .= '</div>';
            $output['tariff_detail'] =  $tdhtml;
            echo json_encode($output);
            exit;
        }

        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            $this->tariffId = $_GET['id'];
            $this->tarifObj = new Tariffs($this->tariffId);
            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                if ($this->tarifObj->getUserAccountId() == $this->user->getUserAccountId()) {
                    //Get Tariff Detail from mapping table
                    $tariffsDetailsFilter = new TariffsDetailsFilter();
                    $tariffsDetailsFilter->addFieldFilter("    td.tariffs_id", $this->tariffId);
                    $tariffsDetailsFilter->setLimit("-1");
                    $this->tariffDetailsObj = $tariffsDetailsFilter->getList();
                    if (count($this->tariffDetailsObj) > 0) {
                        $this->fromZoneId = $this->tariffDetailsObj[0]->getFromZoneId();
                        $this->toZoneId = $this->tariffDetailsObj[0]->getToZoneId();
                    }
                } else {
                    $this->tarifObj = new Tariffs();
                    $this->tariffId = 0;
                }
            } else {
                //Get Tariff Detail from mapping table
                $tariffsDetailsFilter = new TariffsDetailsFilter();
                $tariffsDetailsFilter->addFieldFilter("    td.tariffs_id", $this->tariffId);
                $tariffsDetailsFilter->setLimit("-1");
                $this->tariffDetailsObj = $tariffsDetailsFilter->getList();
                if (count($this->tariffDetailsObj) > 0) {
                    $this->fromZoneId = $this->tariffDetailsObj[0]->getFromZoneId();
                    $this->toZoneId = $this->tariffDetailsObj[0]->getToZoneId();
                }
            }
        }
    }

    protected function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
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
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
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
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script type="text/javascript">
            function loadCarrierZones() {
                var carrierId = $("#carrier_id").val();
                var serviceId = $("#service_id").val();
                $.ajax({
                    type: "POST",
                    url: "tariff_add.php",
                    data: {
                        func: "get_carrier_zone_ddl",
                        carrier_id: carrierId,
                        service_id: serviceId
                    },
                    dataType: "html",
                    success: function(data) {
                        $(".from_zone_id").html(data);
                        $(".to_zone_id").html(data);
                        <?php
                        if (isset($_GET['id'])) {
                        if (!empty($this->tariffDetailsObj)) {
                        foreach($this->tariffDetailsObj as $k => $tariffsDetailsObj) {
                        ?>
                        $('#to_zone_id_<?php echo $k + 1; ?>').val( <?php echo $tariffsDetailsObj->getToZoneId(); ?> );
                        $('#from_zone_id_<?php echo $k + 1; ?>').val( <?php echo $tariffsDetailsObj->getFromZoneId(); ?> );
                        <?php
                        }
                        }
                        ?>
                        //$("#from_zone_id").val( <?php //echo $this-> fromZoneId; ?> );
                        <?php
                        }
                        ?>
                        $(".from_zone_id").select2();
                        $(".to_zone_id").select2();
                    },
                    error: function() {
                        swal("Sorry!", "Some thing went wrong", "error");
                    }
                });
            }

            function loadCarrierServices(carrierId, serviceId) {
                $.ajax({
                    type: "POST",
                    url: "tariff_add.php",
                    data: {func: "get_carrier_services", carrier_id: carrierId, service_id: serviceId},
                    dataType: "html",
                    success: function (data) {
                        $("#services_span").html(data);
                        $("#service_id").select2();
                        loadCarrierZones();
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }

            $(document).ready(function () {
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $('#carrier_id').change(function () {
                    var carrierId = $(this).val();
                    var serviceId = '<?php echo $this->tarifObj->getServiceId(); ?>';
                    loadCarrierServices(carrierId, serviceId);
                    //loadCarrierZones();
                });
                $(document).on('change', '#service_id', function () {
                    loadCarrierZones();
                });
                <?php if (!empty($_GET['id']) || !empty($_GET['carrier_id'])) { ?>
                $('#carrier_id').trigger("change");
                <?php } ?>
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add-more-tariff-keys', function () {
                    elindex++;
                    var clone = $(this).parent().parent().clone();
                    var zoneFrom = $(clone).find('.from_zone_id').attr('name');
                    var zoneTo = $(clone).find('.zone-to-id').attr('name');
                    var weightFrom = $(clone).find('.weight-from').attr('name');
                    var weightTo = $(clone).find('.weight-to').attr('name');
                    var userWeightCost = $(clone).find('.user_weight_cost').attr('name');
                    var userPeiceCost = $(clone).find('.user_peice_cost').attr('name');
                    var formula = $(clone).find('.formula').attr('name');

                    var id = $(clone).find('.tariff-id').attr('name');
                    $(this).remove();
                    $(clone).find('.select2-container').remove();

                    $(clone).find('input').val('');
                    $(clone).find('.from_zone_id').attr('name', zoneFrom.replace(/\d+/, elindex));
                    $(clone).find('.zone-to-id').attr('name', zoneTo.replace(/\d+/, elindex));
                    $(clone).find('.weight-from').attr('name', weightFrom.replace(/\d+/, elindex));
                    $(clone).find('.weight-to').attr('name', weightTo.replace(/\d+/, elindex));
                    $(clone).find('.user_weight_cost').attr('name', userWeightCost.replace(/\d+/, elindex));
                    $(clone).find('.user_peice_cost').attr('name', userPeiceCost.replace(/\d+/, elindex));
                    $(clone).find('.tariff-id').attr('name', id.replace(/\d+/, elindex));
                    $(clone).find('button.remove-tariff-key').show();
                    $(clone).find('button.remove-tariff-key').removeClass('initial-button');
                    $(clone).find('.from_zone_id').select2();
                    $(clone).find('.zone-to-id').select2();
                    $(clone).find('.formula').attr('name', formula.replace(/\d+/, elindex));
                    $(clone).find('.formula').select2();
                    $(clone).appendTo($('.tariffs-container'));
                });
                $(document).on('click', '.remove-tariff-key', function () {
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
                                if ($('.tariffs-container .remove-tariff-key').length > 1) {
                                    $(el).parent().parent().remove();
                                    if ($('.add-more-tariff-keys').length == 0) {
                                        var addMore = $(el).parent().find('.add-more-tariff-keys').clone();
                                        $('.tariffs-container .remove-tariff-key').last().parent().prepend(addMore);
                                    }
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                            }
                        });
                });

                $("#upload_csv").click(function () {
                    var file_data = $('#csv_file').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: "tariff_add.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            var status = response.status;
                            if (status == 'success') {
                                $('#tarif_csv_data').html(response.html);
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                        }
                    });
                    return false;
                });
                change_tariff_detail_method();
                <?php if (isset($_GET['id']) && $_GET['id'] > 0) { ?>
                var tariffId = <?php echo $_GET['id'] ?>;
                loadTariffDetails(tariffId);
                <?php } ?>
            });

            function change_tariff_detail_method() {
                if ($("#chkDetailActive").prop('checked') == true) {
                    $('.tariff_csv_box').hide();
                    $('#tariff_form_box').show();
                } else {
                    $('#tariff_form_box').hide();
                    $('.tariff_csv_box').show();
                }
            }

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
                var zones = new Array();
                if ($("#chkDetailActive").prop('checked') == true) {
                    if (validation) {
                        $('#tarif_form').submit();
                    } else {
                        swal("Sorry!", "Error is high lighted with red border", "error");
                    }
                } else {
                    $('#tarif_form').submit();

                    // $('input[name^="zonename"]').each(function () {
                    //     zones.push($(this).val());
                    // });
                    // var carrierId = $("#carrier_id").val();
                    // var serviceId = $("#service_id").val();
                    // $.ajax({
                    //     type: "POST",
                    //     url: "tariff_add.php",
                    //     data: {
                    //         func: "check_carrier_zone_validation",
                    //         carrier_id: carrierId,
                    //         service_id: serviceId,
                    //         zones: zones
                    //     },
                    //     dataType: "json",
                    //     success: function (data) {
                    //         var check_valid = data.invalid_zone;
                    //         var msg = '';
                    //         if (check_valid != null) {
                    //             if (check_valid.length > 0) {
                    //                 check_valid.forEach(function (element) {
                    //                     $('input[value="' + element + '"]').closest("th").css('border', '1px solid red');
                    //                     msg += '\n' + element + ' is invalid, ';
                    //                 });
                    //                 validation = 0;
                    //             }
                    //         }
                    //         if (validation) {
                    //             $('#tarif_form').submit();
                    //         } else {
                    //             swal("Sorry!", "Error is high lighted with red border " + msg, "error");
                    //         }
                    //     },
                    //     error: function () {
                    //         swal("Sorry!", "Some thing went wrong", "error");
                    //     }
                    // });
                }
            });
            $(document).on('click', '#csv_download_btn', function () {
                if ($('#tarif_id_for_csv_download').val() > 0) {
                    $('#csv_download_form').submit();
                } else {
                    swal("Sorry!", "No detail for current tariff", "error");
                }
            });
            function loadTariffDetails(tariffId) {
                var form_data = new FormData();
                form_data.append('tariff_id', tariffId);
                form_data.append('action', 'load_tariff_details');
                $.ajax({
                    url: "tariff_add.php",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        $('#tariff_detals').html(response.tariff_detail);
                    }
                });
            }
            <?php require_once("../js/tariff-remotearea-charges.js"); ?>
        </script>
        <?php
    }

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-money"></i>
                    Add/Update Tariff
                </div>
                <div class="actions">
                    <?php
                    $slectedCarrierId = '';
                    if (isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                        $slectedCarrierId = $_GET['carrier_id'];
                    }
                    $selected = '';
                    if (!empty($this->tarifObj->getCarrierId())) {
                        $selected = $this->tarifObj->getCarrierId();
                    } else {
                        $selected = $slectedCarrierId;
                    }
                    ?>
                    <?php if(isset($_GET['id']) && !empty($_GET['id'])) {

                        $service = new Services($this->tarifObj->getServiceId());
                        ?>


                        <?php
                        if ( !empty($service->getRemotearea())) {
                            $serviceRemoteArea = $service->getRemotearea();
                        } else {
                            $serviceRemoteArea ='ON_WEIGHT';
                        }


                        $funcRemoteArea =  "getRemoteAreaTariffCharges('".$_GET['id']."',"."'".$selected."' ,'".$serviceRemoteArea."')"; ?>
                        <a href="javascript:;" class="btn btn-sm blue" data-toggle="modal" id="span_user_service_remotearea_<?php echo $_GET['id']; ?>" data-target="#model_remoterea_supplier" onclick="return <?php echo $funcRemoteArea ?>" title="Tariff Remoteareas Charges"><span></span><i
                                    class="fa fa-money"></i>&nbsp; Tariff Remoteareas Charges </a>
                        <a href="tariff_additional_charges.php?tariff_id=<?php echo $_GET['id']; ?>" class="btn btn-sm blue"><span></span><i
                                    class="fa fa-money"></i>&nbsp; Tariff Additional Charges </a>
                    <?php } ?>
                    <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i
                                class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?>
                    </a>
                    <?php if (!empty($this->params)) { ?>
                        <a href="tariffs_list.php?<?php echo $this->params; ?>" class="btn btn-sm blue"><span></span><i
                                    class="fa fa-list"></i>&nbsp; Tariff Listing</a>
                    <?php } else { ?>
                        <a href="tariffs_list.php" class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp;
                            Tariff Listing</a>
                    <?php } ?>
                    <?php if ($this->tarifObj->getTariffsPricingRuleId() > 0) { ?>
                        <a href="tariffs_pricing.php?rule_id=<?php echo $this->tarifObj->getTariffsPricingRuleId(); ?>"
                           class="btn btn-sm blue"><span></span><i class="fa fa-list"></i>&nbsp; Show Tariff Pricing
                            Details</a>
                    <?php } ?>
                </div>
                <div class="tools"></div>
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
                <form name="tarif_form" id="tarif_form" action="" method="post">
                    <div class="caption margin-bottom-10 block">
                       Tarif Information
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                    <label>Carrier Name</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $selected, 'id', ' class="bs-select input-sm form-control" data-live-search="true" data-show-subtext="true"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Carrier Service</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <span id="services_span">
                                        <select name="service_id" id="service_id"
                                                class="form-control select2 validate_check">
                                            <option value="">Select Service</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tariff Name</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="tariff_name" id="tariff_name" class="form-control validate_check" value="<?php echo $this->tarifObj->getName() ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Currency</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <?php
                                    echo Ddl::generateDDL('currency_id', 'CurrencyFilter', "AND isactive='1' AND ClientDisplay='1'", 'currencyname', 'id', $this->tarifObj->getCurrencyId(), ' class="input-sm form-control select2 validate_check"', 'Select Currency', '');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" class="form-control input-sm validate_check" readonly name="start_date" id="start_date" placeholder="" value="<?php echo formatDate($this->tarifObj->getStartDate()); ?>">
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
                                    <input type="text" class="form-control input-sm validate_check" readonly name="end_date" id="end_date" placeholder="" value="<?php echo formatDate($this->tarifObj->getEndDate()); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Type</label><br/>
                                <?php $chkType = $this->tarifObj->getTariffType(); ?>
                                <input <?php echo($chkType == 'customer' ? 'checked="checked"' : ''); ?> name="chkType" type="checkbox" class="make-switch" data-on-text="Customer" check data-off-text="Supplier" data-on-color="success" data-off-color="info">
                            </div>

                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Active</label><br/>
                                <?php $chkActive = $this->tarifObj->getStatus(); ?>
                                <input <?php echo($chkActive == '1' ? 'checked="checked"' : ''); ?> name="chkActive" type="checkbox" class="make-switch" data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control"><?php echo $this->tarifObj->getDescription(); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="caption margin-bottom-10 block">
                       Tarif Details
                    </div>
                    <div class="row">
                        <div class="col-sm-2 ">
                            <div class="form-group">
                                <label>Select Detail Method</label><br/>
                                <input onchange="change_tariff_detail_method()" name="chkDetailActive" id="chkDetailActive" type="checkbox" class="make-switch" data-on-text="Manual" check data-off-text="CSV" data-on-color="primary" data-off-color="danger" <?php echo ((count($this->tariffDetailsObj) < 200) ? "checked='checked'" : "") ?> >
                            </div>
                        </div>
                        <div class="col-sm-10 tariff_csv_box">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> Upload Your Document</label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium"
                                             data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="csv_file" id="csv_file">
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists"
                                           data-dismiss="fileinput"> Remove </a>
                                        <a href="javascript:;" class="input-group-addon btn blue"
                                           id="upload_csv">Upload</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="tariff_form_box">
                        <?php if(count($this->tariffDetailsObj) < 200) { ?>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th width="200"><label>From Zone</label></th>
                                            <th width="200"><label>To Zone</label></th>
                                            <th><label>Weight</label></th>
                                            <th><label>KG Cost</label></th>
                                            <th><label>Item Cost</label></th>
                                            <th><label>Formula</label></th>
                                            <th style="width: 105px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tariffs-container">
                                        <?php if (!empty($this->tariffDetailsObj)) {
                                            foreach ($this->tariffDetailsObj as $k => $tariffsDetailsObj) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <select name="tariffs[<?php echo $k + 1; ?>][from_zone_id]" id="from_zone_id_<?php echo $k + 1; ?>" class="form-control select2 validate_check from_zone_id">
                                                            <option value="">Select Zone From</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="tariffs[<?php echo $k + 1; ?>][to_zone_id]" id="to_zone_id_<?php echo $k + 1; ?>" class="form-control select2 to_zone_id zone-to-id">
                                                            <option value="">Select Zone To</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control weight-from" min="0" name="tariffs[<?php echo $k + 1; ?>][weight_from]" placeholder="Weight From" value="<?php echo $tariffsDetailsObj->getWeightFrom(); ?>">
                                                            <span class="input-group-addon"> to </span>
                                                            <input type="text" class="form-control weight-to" min="0" name="tariffs[<?php echo $k + 1; ?>][weight_to]" placeholder="Weight To" value="<?php echo $tariffsDetailsObj->getWeightTo(); ?>">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" min="0" name="tariffs[<?php echo $k + 1; ?>][user_weight_cost]" class="form-control user_weight_cost input-sm" placeholder="Rate" value="<?php echo $tariffsDetailsObj->getWeightCost(); ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" min="0" name="tariffs[<?php echo $k + 1; ?>][user_peice_cost]" class="form-control user_peice_cost input-sm" placeholder="Rate" value="<?php echo $tariffsDetailsObj->getPieceCost(); ?>">
                                                    </td>
                                                    <td>
                                                        <select name="tariffs[<?php echo $k + 1; ?>][formula]" class="form-control select2 formula input-sm">
                                                            <option value="">Only Consider KG Cost</option>
                                                            <option value="Q * ITMCHR" <?php if ($tariffsDetailsObj->getFormula() == "Q * ITMCHR") {
                                                                echo "selected='selected'";
                                                            } ?> >No of Items * Item Cost
                                                            </option>
                                                            <option value="Q * CHRG" <?php if ($tariffsDetailsObj->getFormula() == "Q * CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No of Items * KG Cost
                                                            </option>
                                                            <option value="Q * ( ITMCHR + REG ) + W * CHRG" <?php if ($tariffsDetailsObj->getFormula() == "Q * ( ITMCHR + REG ) + W * CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >(No. of Items * (Item Cost + Register Cost ) )+ (Items
                                                                Weight * KG Cost) (Register Post)
                                                            </option>
                                                            <option value="( Q * ITMCHR ) + ( W * CHRG )" <?php if (trim($tariffsDetailsObj->getFormula()) == "( Q * ITMCHR ) + ( W * CHRG )") {
                                                                echo "selected='selected'";
                                                            } ?> > (No. of Items * Item Cost ) + ( Items Weight * KG Cost )
                                                                (UnTrack Register Post)
                                                            </option>
                                                            <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG" <?php if ($tariffsDetailsObj->getFormula() == "Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                                Weight - Current range From Weight) / 0.45 )) + KG Cost (DHL
                                                                ECO)
                                                            </option>
                                                            <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG" <?php if ($tariffsDetailsObj->getFormula() == "Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                                Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL
                                                                EXP)
                                                            </option>
                                                            <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG" <?php if ($tariffsDetailsObj->getFormula() == "(  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                                Weight - Current range From Weight ) / 0.45 )) + KG Cost
                                                                (DHL ECO SHIPMENT)
                                                            </option>
                                                            <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG" <?php if ($tariffsDetailsObj->getFormula() == "(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items
                                                                Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL
                                                                EXP SHIPMENT))
                                                            </option>
                                                            <option value="Q * (  ITMCHR * ceil ( W - FRMW )) + CHRG" <?php if ($tariffsDetailsObj->getFormula() == "Q * (  ITMCHR * ceil ( W - FRMW )) + CHRG") {
                                                                echo "selected='selected'";
                                                            } ?> >No. of Items * ( Item Cost * ROUND_NEXT_NUMER( Items
                                                                Weight - Current range From Weight ) ) + KG Cost (STANDARD)
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <?php if (count($this->tariffDetailsObj) == ($k + 1)) { ?>
                                                            <button type="button" class="btn btn-success add-more-tariff-keys"><i class="fa fa-plus"></i></button>
                                                            <button type="button" class="btn btn-danger remove-tariff-key"> <i class="fa fa-minus"></i></button>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-danger remove-tariff-key"> <i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <input type="hidden" class="tariff-id" name="tariffs[<?php echo $k + 1; ?>][id]" value="">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td>
                                                    <select name="tariffs[0][from_zone_id]" class="form-control select2 from_zone_id zone-from-id">
                                                        <option value="">Select Zone From</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="tariffs[0][to_zone_id]" class="form-control select2 to_zone_id zone-to-id">
                                                        <option value="">Select Zone To</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group form-group input-group-sm">
                                                        <input type="text" class="form-control weight-from" min="0" name="tariffs[0][weight_from]" placeholder="Weight From" value="0">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control weight-to" min="0" name="tariffs[0][weight_to]" placeholder="Weight To" value="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" min="0" name="tariffs[0][user_weight_cost]" class="form-group form-control user_weight_cost input-sm" placeholder="Rate" value="">
                                                </td>
                                                <td>
                                                    <input type="text" min="0" name="tariffs[0][user_peice_cost]" class="form-group form-control user_peice_cost input-sm" placeholder="Rate" value="">
                                                </td>
                                                <td>
                                                    <select name="tariffs[0][formula]" class="form-control select2 formula input-sm">
                                                        <option value="">Only Consider KG Cost</option>
                                                        <option value="Q * ITMCHR">No of Items * Item Cost</option>
                                                        <option value="Q * CHRG">No of Items * KG Cost</option>
                                                        <option value="Q * ( ITMCHR + REG ) + W * CHRG">(No. of Items *
                                                            (Item Cost + Register Cost ) )+ (Items Weight * KG Cost)
                                                            (Register Post)
                                                        </option>
                                                        <option value="( Q * ITMCHR ) + ( W * CHRG )"> (No. of Items * Item
                                                            Cost ) + ( Items Weight * KG Cost ) (UnTrack Register Post)
                                                        </option>
                                                        <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG">
                                                            No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items Weight -
                                                            Current range From Weight) / 0.45 )) + KG Cost (DHL ECO)
                                                        </option>
                                                        <option value="Q * (  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG">
                                                            No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items Weight -
                                                            Current range From Weight ) / 0.5 )) + KG Cost (DHL EXP)
                                                        </option>
                                                        <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG">No.
                                                            of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items Weight -
                                                            Current range From Weight ) / 0.45 )) + KG Cost (DHL ECO
                                                            SHIPMENT)
                                                        </option>
                                                        <option value="(  ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG">No.
                                                            of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items Weight -
                                                            Current range From Weight ) / 0.5 )) + KG Cost (DHL EXP
                                                            SHIPMENT))
                                                        </option>
                                                        <option value="Q * (  ITMCHR * ceil ( W - FRMW )) + CHRG">No. of
                                                            Items * ( Item Cost * ROUND_NEXT_NUMER( Items Weight - Current
                                                            range From Weight ) ) + KG Cost (STANDARD)
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success add-more-tariff-keys"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="btn btn-danger remove-tariff-key initial-button"><i class="fa fa-minus"></i></button>
                                                    <input type="hidden" class="tariff-id" name="tariffs[0][id]" value="">
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } else {?>
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <p>This traiff can only manage by csv</p>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                    <div class="row margin-top-20 tariff_csv_box">
                        <div class="col-sm-12" id="tariff_detals">
                            <div class="table-responsive">
                                <table class="table table-bordered fixed custom_table" id="tarif_csv_data"></table>
                            </div>
                        </div>
                    </div>

                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <input type="hidden" name="tariff_id" value="<?php echo $this->tariffId; ?>"/>
                            <input type="hidden" name="action" value="save_tariff"/>
                            <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save">Save
                            </button>
                            <a href="tariffs_list.php" id="btnCancel"
                               class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </form>
                <form id="csv_download_form" name="csv_download_form" method="post">
                    <input type="hidden" name="tariff_id" id="tarif_id_for_csv_download"
                           value="<?php echo $this->tariffId; ?>"/>
                    <input type="hidden" name="action" value="download_tariff_csv"/>
                </form>
                <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="<?php echo count($this->tariffDetailsObj); ?>"/>
                <form name="hiddenForm" id="download_tariff_remotearea_template_form" action="tariff_remotearea_charges.php" method="POST">
                    <input type="hidden" name="tariffId" id="download_csv_tariffId" value="" />
                    <input type="hidden" name="carrierId" id="download_csv_carrierId" value="" />
                    <input type="hidden" name="remoteareaType" id="download_csv_remoteareaType" value="" />
                    <input type="hidden" name="func" value="download_tariff_remotearea_template" />
                </form>
            </div>
        </div>
        <!--Model for services remotearea-->
        <div class="modal fade bs-modal-lg" id="model_remoterea_supplier" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="remotearea_apend_remoterea_supplier"></div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <?php
    }
    protected function renderHead() {
        ?>
        <style>
            table.fixed { table-layout:fixed; }
            table.fixed td { overflow: hidden; }
            .custom_table thead th { width: 120px;}
        </style>
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

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>