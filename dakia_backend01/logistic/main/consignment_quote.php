<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'pdfmerger'
        ], 'labels');
include_classes([
    'tcpdf'
        ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
        ], 'general');
include_classes([
    'include_list',
        ], 'reamus');
include_classes([
    'ups.class'
        ], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
]);

class Page extends BasePage {

    public $user;
    public $userAccount;
    public $serviceId = 0;
    public $parcelData = "";
    public $labelGenreatedCheck = "";
    public $consignmentEditError = "";
    public $consignmentStatus = "";

    protected function init() {
        $this->user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Consignment Quotation"
        );

        /*
         * Get Quotation of shipment
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "getQuotation") {
            $html = '';
            $dest_country = $this->form_vars['sender_country'];
            $txt_des_postcode = $this->form_vars['receiver_postcode'];
            $txt_des_city = $this->form_vars['receiver_city'];
            $shipment_type = $this->form_vars['shipment_type'];
            $parcelArray = $this->form_vars["parcel"];
            if (count($parcelArray) > 0) {
                $weight_tariff = 0;
                $vol_weight = 0;
                $quantity = 0;
                foreach ($parcelArray as $parcel) {
                    $quantity++;
                    $parcelWeight = $parcel["receiver_weight"];
                    $parcelLength = $parcel["receiver_length"];
                    $parcelWidth = $parcel["receiver_width"];
                    $parcelHeight = $parcel["receiver_height"];
                    $vol_weight = ($parcelWidth * $parcelLength * $parcelHeight) / 5000;
                    if ($vol_weight > $parcelWeight)
                        $weight_tariff += $vol_weight;
                    else
                        $weight_tariff += $parcelWeight;
                }
            }


            $this->dest_country = $dest_country;
            $this->dest_city = $txt_des_city;
            $this->dest_postcode = $txt_des_postcode;

            if ($dest_country <= 0 || trim($dest_country) == '')
                $error_message[] = Translation::GetCaption("PLEASE_CHECK_DESTINATION_COUNTRY");
            if ((float) $weight_tariff <= 0)
                $error_message[] = Translation::GetCaption("PLEASE_ENTER_VALID_WEIGHT");
            if (trim($txt_des_city) == '')
                $error_message[] = Translation::GetCaption("PEASE_ENTER_CITY");
            if (trim($txt_des_postcode) == '')
                $error_message[] = Translation::GetCaption("PLEASE_ENTER_POSTCODE");
            else {
                $destCountry = new Country($dest_country);
                $postcodeValidation = validatePostCode($destCountry->getIso(), $txt_des_postcode);
                if ($postcodeValidation !== true) {
                    $error_message[] = $postcodeValidation;
                }
            }
            if (count($error_message) <= 0) {
                try {

                    $message["STATUS"] = "ERROR";
                    $userQuotations = Tariffs::getUserQuotationsByAssignedServices($this->userAccount->getId(), $this->user->getCountryId(), $dest_country, '', $txt_des_postcode, '', $deliveryCity, $weight_tariff, $quantity, 0, 0, 0, 0, $shipment_type);
                    if ($userQuotations["STATUS"] == "ERROR") {
                        $message["MESSAGE"] = $userQuotations["MESSAGE"];
                    } else {
                        $countryList = new Country($dest_country);
                        $region = $countryList->getRegion();
                        $html .= '<strong><div class="col-sm-12 services red-back" style="padding:5px; display: flex; align-items: center;" >';
                        $html .= '	<div class="col-sm-2"> Service Name</div>';
                        $html .= '	<div class="col-sm-1">Type</div>';
                        $html .= '	<div class="col-sm-1">Delivery Type</div>';
                        $html .= '	<div class="col-sm-2">Transit Time</div>';
                        //$html .= '	<div class="col-sm-2">Insurance</div>';
                        $html .= '	<div class="col-sm-1">Basic Charges</div>';
                        $html .= '	<div class="col-sm-1">Fuel Charges</div>';
                        $html .= '	<div class="col-sm-2">Price ex. VAT</div>';
                        $html .= '	<div class="col-sm-2">Action</div>';
                        $html .= '<div style="clear:both;"></div>';
                        $html .= '</div></strong>';
                        $countTariff = 0;
                        $userQuotationList = $userQuotations["QUOTATIONS"];
                        foreach ($userQuotationList as $quotation) {
                            if ($quotation["BASIC_CHARGE"] > 0) {
                                $countTariff++;
                                $remoteareasmessage = false;
                                $service = new ServiceFilter();
                                $service->addFieldFilter("code", $quotation['SERVICE_CODE']);
                                $serviceList = $service->getColumnList("id, required_email, required_telephone, shipment_type, is_untrack, max_length, max_width, max_height, service_type, insurance_available, proforma_invoice");
                                if (count($serviceList) > 0) {
                                    $email_required = $serviceList[0]->getRequiredEmail();
                                    $telephone_required = $serviceList[0]->getRequiredTelephone();
                                    $shipmentType = $serviceList[0]->getShipmentType();
                                    $tracked = $serviceList[0]->getIsUntrack();
                                    $maxWidth = $serviceList[0]->getMaxWidth();
                                    $maxLength = $serviceList[0]->getMaxLength();
                                    $maxHeight = $serviceList[0]->getMaxHeight();
                                    $serviceId = $serviceList[0]->getId();
                                    $serviceType = $serviceList[0]->getServiceType();
                                    $insurance = $serviceList[0]->getInsuranceAvailable();
                                    $is_plt = $serviceList[0]->getProformaInvoice();
                                    if ($tracked == "1") {
                                        $delivery_format = "Untracked";
                                    } else {
                                        $delivery_format = "Tracked";
                                    }
                                }


                                $service_code = $quotation['SERVICE_CODE'];
                                $productDetails = '<div class="col-sm-12 hidden product_info_' . $service_code . '" style="padding:5px; display: flex; align-items: center; ">';
                                $productDetails .= '<div class="col-sm-2" style="padding:0;">PRODUCT_NAME</div>';
                                $productDetails .= '<div class="col-sm-10" style="padding:0;">' . $quotation['SERVICE_NAME'] . '</div>';
                                $productDetails .= '<div class="col-sm-2" style="padding:0;">TYPE</div>';
                                $productDetails .= '<div class="col-sm-10" style="padding:0;">' . strtoupper($shipmentType) . '</div>';
                                $productDetails .= '<div class="col-sm-2" style="padding:0;">DELIVERY_OPTION</div>';
                                $productDetails .= '<div class="col-sm-10" style="padding:0;">' . $delivery_format . '</div>';
                                //$productDetails .= '<div class="col-sm-2" style="padding:0;">RESTRICTION_PRODUCT</div>';
                                //$productDetails .= '<div class="col-sm-10" style="padding:0;">DIMENSION_RESTRICTION : ' . $maxLength . ' cm * ' . $maxWidth . ' cm * ' . $maxHeight . ' cm <br /> </div>';
                                $productDetails .= '</div>';

                                if (trim($quotation['REMOTEAREA_CHARGE']) >= '0.00' && !$remoteareasmessage)
                                    $remoteareasmessage = true;

                                $html .= '<div class="col-sm-12 services" id="' . $service_code . '" style="padding:5px; display: flex; align-items: center;">';
                                $html .= '<div class="col-sm-2">' . $quotation['SERVICE_NAME'] . '<div class="red-18" style="float:right;">' . ($remoteareasmessage == true ? '*' : '') . '</div> </div>';
                                $html .= '<div class="col-sm-1">' . strtoupper($shipmentType) . '</div>';
                                $html .= '<div class="col-sm-1">' . $delivery_format . '</div>';
                                $html .= '	<div class="col-sm-2">' . $quotation['TRANSIT_TIME'] . '</div>';
                                /* if ($tracked == "NO") {
                                  $html .= '<div class="col-sm-2">50 &euro;</div>';
                                  } else {
                                  $html .= '<div class="col-sm-2"> 0 &euro;</div>';
                                  } */
                                $extraCharges = json_decode($quotation["EXTRA_CHARGES"]);
                                $fuelCharge = 0.00;
                                foreach ($extraCharges as $extra) {
                                    $chargeType = $extra->charge_type_key;
                                    if ($chargeType == "FUEL_CHARGES") {
                                        $fuelCharge = $extra->charge;
                                    }
                                }
                                $html .= '	<div class="col-sm-1">' . number_format($quotation['BASIC_CHARGE'], 2) . '</div>';
                                $html .= '	<div class="col-sm-1">' . number_format($fuelCharge, 2) . '</div>';
                                $html .= '	<div class="col-sm-2">' . number_format($quotation['TOTAL'], 2) . $quotation['CURRENCY_CODE'] . '</div>';
                                $html .= '	<div class="col-sm-2"> <a href="#"  name="btncreateLabel1HS" onclick="createShipment(\'' . $service_code . '\', \'' . (($remoteareasmessage == true) ? 'YES' : 'NO') . '\', \'product_info_' . $service_code . '\', \'' . $email_required . '\' , \'' . $telephone_required . '\', \'' . $quotation['SERVICE_NAME'] . '\', \'' . $region . '\', \'' . $tracked . '\', \'' . $serviceId . '\', \'' . $serviceType . '\' , \'' . $insurance . '\', \'' . $is_plt . '\')" class="btn btn-primary">' . Translation::GetCaption("SELECT") . '</a></div>';
                                $html .= '<div style="clear:both;"></div>';
                                $html .= '</div>';
                                $html .= $productDetails;
                                $message["STATUS"] = "SUCCESS";
                                $message["MESSAGE"] = $html;
                            } else {
                                $message["STATUS"] = "ERROR";
                                $message["MESSAGE"] = "Tariff not available. Please contact to administrator at info@smarttrack.co";
                            }
                        }
                    }
                } catch (Exception $e) {
                    $message['STATUS'] = "ERROR";
                    $message['MESSAGE'] = $e->getMessage();
                }
            } else {
                $message['STATUS'] = "ERROR";
                $message['MESSAGE'] = implode('<br>', $error_message);
            }
            if ($message["STATUS"] == "" && $countTariff <= 0) {
                $message["STATUS"] = "ERROR";
                $message["MESSAGE"] = "Tariff not available. Please contact to administrator at info@smarttrack.co";
            }
            echo json_encode($message);
            die;
        }
        /*
         * Save Consignment Logic
         */ else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "save_data_consignment") {
             
            $output = array();
            $userId = (int) $this->user->getId();
            $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
            $consignmentId = $this->form_vars["consignment_id"];
            $consignment = new Consignment($consignmentId);
            $consignment->setUserId($userId);
            $consignment->setServiceId($this->form_vars["service"]);
            $consignment->setHawb(time());
            $consignment->setContact($this->form_vars["contact"]);
            $consignment->setCompany($this->form_vars["company"]);
            $consignment->setAddressLine1($this->form_vars["address_line_1"]);
            $consignment->setAddressLine2($this->form_vars["address_line_2"]);
            $consignment->setAddressLine3($this->form_vars["address_line_3"]);
            $consignment->setCity($this->form_vars["city1"]);
            $consignment->setPostcode($this->form_vars["postcode1"]);
            $consignment->setCountryId($this->form_vars["countryid"]);
            $consignment->setTelephone($this->form_vars["telephone"]);
            $consignment->setEmail($this->form_vars["email"]);
            $consignment->setNotes($this->form_vars["notes"]);
            $consignment->setDescription($this->form_vars["description"]);
            $consignment->setValue($this->form_vars["value"]);
            $consignment->setCurrency($this->form_vars["item_currency"]);

            $consignment->setShipmentType($this->form_vars["serviceType"]);

            $consignment->setDateCreated(date("Y-m-d H:i:s"));
            $consignment->setWarehouseId($this->user->getWarehouseId());
            $consignment->setSenderCompany((!empty($this->form_vars["sender_company"]) ? $this->form_vars["sender_company"] : $this->userAccount->getCompany()));
            $consignment->setSenderName((!empty($this->form_vars["sender_contact"]) ? $this->form_vars["sender_contact"] : $this->user->getFirstName() . ' ' . $this->user->getLastName()));
            $consignment->setSenderAddressLine1((!empty($this->form_vars["sender_address_line_1"]) ? $this->form_vars["sender_address_line_1"] : $this->user->getAddress()));
            $consignment->setSenderAddressLine2((!empty($this->form_vars["sender_address_line_2"]) ? $this->form_vars["sender_address_line_2"] : $this->user->getAddress2()));
            $consignment->setSenderAddressLine3((!empty($this->form_vars["sender_address_line_3"]) ? $this->form_vars["sender_address_line_3"] : $this->user->getAddress3()));
            $consignment->setSenderCity((!empty($this->form_vars["sender_city"]) ? $this->form_vars["sender_city"] : $this->user->getCity()));
            //$consignment->setSenderState($this->user->getState());
            $consignment->setSenderPostcode((!empty($this->form_vars["sender_postcode"]) ? $this->form_vars["sender_postcode"] : $this->user->getPostcode()));
            $consignment->setSenderCountryId((!empty($this->form_vars["collection_country"]) ? $this->form_vars["collection_country"] : $this->user->getCountryId()));
            $consignment->setSenderEmail((!empty($this->form_vars["sender_email"]) ? $this->form_vars["sender_email"] : $this->user->getEmail()));
            $consignment->setSenderTelephone((!empty($this->form_vars["sender_telephone"]) ? $this->form_vars["sender_telephone"] : $this->user->getTelephone()));

            if ($this->form_vars["serviceType"] == "C") {
                if (isset($this->form_vars['collection_end_time']) && !empty($this->form_vars['collection_end_time']))
                    $consignment->setCollectionEndTime(date("H:i", strtotime($this->form_vars['collection_end_time'])));
                if (isset($this->form_vars['collection_start_time']) && !empty($this->form_vars['collection_start_time']))
                    $consignment->setCollectionStartTime(date("H:i", strtotime($this->form_vars['collection_start_time'])));
                if (isset($this->form_vars['collection_date']) && !empty($this->form_vars['collection_date']))
                    $consignment->setCollectionDate(date('Y-m-d', strtotime($this->form_vars['collection_date'])));
            }

            $parcelArray = $this->form_vars["parcel"];
            $pArray = array();
            if (count($parcelArray) > 0) {
                $totalWight = 0;
                $quantity = 0;
                foreach ($parcelArray as $parcel) {
                    $quantity++;
                    $parcelWeight = $parcel["receiver_weight"];
                    $parcelLength = $parcel["receiver_length"];
                    $parcelWidth = $parcel["receiver_width"];
                    $parcelHeight = $parcel["receiver_height"];

                    $totalWight += $parcelWeight;

                    $pArray["weight"] = $parcelWeight;
                    $pArray["itemvalue"] = $this->form_vars["value"];
                    $pArray["length"] = ($parcelLength > 0) ? $parcelLength : 0;
                    $pArray["height"] = ($parcelHeight > 0) ? $parcelHeight : 0;
                    $pArray["width"] = ($parcelWidth > 0) ? $parcelWidth : 0;
                    $pArray["id"] = "";
                    $allParcelArray[] = $pArray;
                }
                $consignment->setWeight($totalWight);
                $consignment->setNumberPieces($quantity);
            }

            $tempCustomizedServiceId = "";
            $tempServiceId = "";
            $tempAgentId = "";

            $services = new Services($consignment->getServiceId());

            $dropOffService = false;
            $dropOffServiceId = "";
            $dropOffData = [];

            if ($services->getDropOffServiceId() > 0 &&  $this->form_vars["shipmentType"] == "DO") {
                $dropOffService = true;
                $dropOffServiceId = $services->getDropOffServiceId();
            }
            $consignmentValidator = new ConsignmentValidator($consignment, $allParcelArray, $this->user, $this->userAccount);
            $IsValidConsignment = $consignmentValidator->validateConsignment();
            $consignment = $consignmentValidator->getConsignment();
            $isBasicValid = $consignmentValidator->isBasicValidationFailed();
            if ($isBasicValid) {
                $basicValidationError = $consignmentValidator->getErrorList();
                $outputArray['ERROR'] = $basicValidationError;
                $outputArray['STATUS'] = "ERROR";
                $outputArray['MESSAGE'] = implode('<br>', $basicValidationError);
                echo json_encode($outputArray);
                die;
            }

            if ($IsValidConsignment) {
                $consignment->setShipmentStatus(Consignment::STATUS_READY_TO_PRINT);
                $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                if ($consignment->getId() <= 0) {
                    $consignment->save();
                    $consignment_id = $consignment->getId();
                    $outputArray["CONSIGNMENT_ID"] = $consignment_id;
                    Consignment::saveConsigmentParcel($consignment, $allParcelArray);
                }
                $consignmentId = $consignment->getId();
                
                if ($consignmentId > 0 && $this->form_vars["temp_invoice_name"] != '') {
                    $currentFileLocation = "../_assets/paperless_invoice/temp/" . $this->form_vars["temp_invoice_name"];
                    if(file_exists($currentFileLocation)){
                        
                        $fileName = $this->form_vars["temp_invoice_name"];
                        $temp = explode(".", $fileName);
                        $extension = end($temp);
                        $uploadName = md5($consignmentId) . "." . $extension;
                        $newFileLocation = "../_assets/paperless_invoice/" . $uploadName;
                        @rename($currentFileLocation, $newFileLocation);
                    }
                }
                $newConObj = null;
                if ($consignment->getId() > 0) {


                    if ($dropOffService) {
                        $tempCustomizedServiceId = $consignment->getCustomizedServiceId();
                        $tempServiceId = $consignment->getServiceId();
                        $tempAgentId = $consignment->getAgentId();
                        $weightToSend = $consignment->getWeight();
//                        $consignment->setAgentId("");
                        $consignment->setServiceId($dropOffServiceId);
                        $consignment->setCustomizedServiceId("");
                        $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                        $carrierServiceDefaultRulesFilter->addFilter("serviceid = " . $dropOffServiceId);
                        $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                        $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid,from_weight,to_weight");
                        if (count($carrierServiceDefaultRules) > 0) {
                            $weightFound = false;
                            foreach ($carrierServiceDefaultRules as $carrierServiceDefaultRule) {
                                if ($carrierServiceDefaultRule->getFromWeight() < $weightToSend && $carrierServiceDefaultRule->getToWeight() >= $weightToSend) {
                                    $consignment->setAgentId($carrierServiceDefaultRule->getAgentid());
                                    $weightFound = true;
                                    break;
                                }
                            }
                            if ($weightFound === false) {
                                $errorTemp[] = "We are unable to generate drop-offf label Weight ( " . $weightToSend . " Kg ) is not allowed)";
                                $consignment->setMessage(implode("<br>", $errorTemp));
                                $outputArray['ERROR'] = $errorTemp;
                                $outputArray['STATUS'] = "ERROR";
                                $outputArray['MESSAGE'] = "We are unable to generate drop-offf label Weight ( " . $weightToSend . " Kg ) is not allowed)";
                                return $outputArray;
                            }
                        } else {
                            $errorTemp[] = "We are unable to generate drop-offf label Weight ( " . $weightToSend . " Kg ) is not allowed)";
                            $consignment->setMessage(implode("<br>", $errorTemp));
                            $outputArray['ERROR'] = $errorTemp;
                            $outputArray['STATUS'] = "ERROR";
                            $outputArray['MESSAGE'] = "We are unable to generate drop-offf label Weight ( " . $weightToSend . " Kg ) is not allowed)";
                            return $outputArray;
                        }
                        $consignment->save();
                    }
                    $labelReturn = Consignment::getInstantLabel($consignment, '', '', true, $dropOffService);
                    if (isset($labelReturn['STATUS']) && $labelReturn['STATUS'] == "ERROR") {
                        $labelReturn["CONSIGNMENT_ID"] = $consignment->getId();
                        echo json_encode($labelReturn);
                        die;
                    }
                    if ($consignment->getShipmentStatus() == Consignment::STATUS_INVALID) {
                        $outputArray['URL'] = "../main/consignment_quote.php?id=" . $consignment->getId();
                    } else {
                        if ($dropOffService) {
                            $oldConsignment = new Consignment($consignment->getId());
                            $newConId = Consignment::AddReturnShipment($consignment->getId(), true);
                            $newConObj = new Consignment($newConId);
                            $newConObj->setCustomizedServiceId($tempCustomizedServiceId);
                            $newConObj->setServiceId($tempServiceId);
                            $newConObj->setShipmentType("D");
                            $newConObj->setAwb("");
                            $newConObj->setLabelFile("");
                            $newConObj->setAgentId($tempAgentId);
                            $newConObj->setHawb(trim($oldConsignment->getAwb()));
                            $newConObj->setShipmentStatus(Consignment::STATUS_READY_TO_PRINT);
                            $newConObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                            $newConObj->save();

                            // Get all tracking number with parcel id before empty it
                            $parcelFilterObj = new ParcelFilter();
                            $parcelFilterObj->addFieldFilter("     consignment_id", $newConId);
                            $parcelNewData = $parcelFilterObj->getColumnList("id,tracking_number");
                            $newParcelTrackingArr = [];
                            if (count($parcelNewData) > 0) {
                                foreach ($parcelNewData as $parcelData) {
                                    $newParcelTrackingArr[$parcelData->getTrackingNumber()] = $parcelData->getId();
                                }
                            }
                            Parcel::emptyParcelTrackingByConId($newConId, Consignment::STATUS_READY_TO_PRINT);
                            $labelReturnNew = Consignment::getInstantLabel($newConObj, $labelType, $labelSize, true);
                            if (isset($labelReturnNew['STATUS']) && $labelReturnNew['STATUS'] == "ERROR") {
                                // Remove old consignment
                                $oldConsignment->setShipmentStatus(Consignment::STATUS_RECYCLED);
                                $oldConsignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECYCLED]);
                                $oldConsignment->save();
                                return $labelReturnNew;
                            }
                        }
                        $outputArray['STATUS'] = "SUCCESS";
                        $outputArray['URL'] = "../main/client_list.php?show=printed" . (trim($userParams) != '' ? "&" . $userParams : '');
                        $outputArray['INSTANT_LABEL'] = $labelReturn['LABEL'];
                        $outputArray['COLLECTIONNO'] = $consignment->getCollectionConfirmationNo();
                        //$consignment->getLabelFile();
                        $outputArray['TRACKING_NUMBER'] = $labelReturn['TRACKING_NUMBER'];
                    }

                    if ($consignment->getRemoteCharges() == '1')
                        $information_array[] = formatMessages(MESSAGE_REMOTE_AREA_CHARGE);
                }
            } else {
                $consignment->setShipmentStatus(Consignment::STATUS_INVALID);
                $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_INVALID]);
            }

            if (!$IsValidConsignment) {
                $error_array = $consignmentValidator->getErrorList();
            }

            if (sizeof($error_array) > 0) {
                if ($consignment->getId() > 0) {
                    $consignment->setMessage(implode('<br>', $error_array));
                    $outputArray['ERROR'] = $error_array;
                    $outputArray['URL'] = "../main/consignment_quote.php?id=" . $consignment->getId();
                } else {
                    $outputArray['ERROR'] = $error_array;
                }
            }

            if (count($outputArray['ERROR']) > 0) {
                $outputArray['STATUS'] = "ERROR";
                foreach ($outputArray['ERROR'] as $loopError)
                    $outputArray['MESSAGE'] .= $loopError . '<br>';
            }
            $outputArray["CONSIGNMENT_ID"] = $consignment->getId();
            $outputArray["ORDER_REFERENCE"] = $consignment->getHawb();
            $outputArray['ID'] = $consignment->getId();
            $outputArray['HAWB'] = $consignment->getHawb();
            $outputArray['MESSAGE'] .= implode("<br />", $information_array);

            echo json_encode($outputArray);
            die;
        }
        
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_user_doc') {
            $html = "";
            
            $path = "../_assets/paperless_invoice/temp/";
            if (!file_exists($path))
                @mkdir($path, 0775);
            if (isset($_FILES["user_doc_file"]) && trim($_FILES["user_doc_file"]["name"]) != '') {
                $allowedExts = array("pdf");
                $temp = explode(".", $_FILES["user_doc_file"]["name"]);
                
                $extension = end($temp);
                if ((($_FILES["user_doc_file"]["type"] == "application/pdf" )) && in_array($extension, $allowedExts)) {
                    if ($_FILES["user_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["user_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["user_doc_file"]["name"]);
                        $documentName = explode(".", $uploadUserDoc);
                        $docName = $documentName[0];
                        move_uploaded_file($_FILES["user_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        
                        $html .= '<div class="col-md-3 doc_upload_view" id="usr_doc_' . $docName . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" style="max-width: 100%; max-height: 100px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption text-center" style="height: auto">';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue btn-xs"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="' . $uploadUserDoc . '"> Remove </a>';
                        $html .= '<input type="hidden" name="temp_invoice_name" id="temp_invoice_name" value="' . $uploadUserDoc . '" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        echo $html;
                    }
                } else {
                    echo $return_msg = "0";
                }
            }

            die;
        }
        //        Handle remove User Document
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_user_doc') {
            $docId = $this->form_vars['doc_id'];
            @unlink("../_assets/paperless_invoice/temp/". $docId);
            
            echo "1";
            die;
        }
        /*
         * LOAD MAP
         */
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "map_load") {
            $output = [];
            $location = [];
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $this->form_vars["service_id"]);
            $dropoffFilter->orderBySort("added_date desc");
            $dropoffList = $dropoffFilter->getList('1');
            $lat = "";
            $lng = "";
            if (count($dropoffList) > 0) {
                $dlist = $dropoffList[0];
                $lat = $dlist->getLat();
                $lng = $dlist->getLng();
            }
            $postcode = str_replace(" ", "", $this->form_vars["from_post_code"]);
            // On call Mruga told me that you can get calls name from service table by just service id not agent id
            $serviceObj = new Services($this->form_vars["service_id"]);
            $className = trim($serviceObj->getLabelClassName());
            $calssFound = false;
            if (trim($className) != '') {
                $file = "../includes/labels/" . strtolower($className) . ".class.php";
                if (is_file($file)) {
                    require_once($file);
                    $calssFound = true;
                }
            }
//            $upslabel = new UPS();
            if ($calssFound) {
                $classObject = new $className();
                $dropofflocationresponse = $classObject->getDropOffLocation($postcode);
                if (sizeof($dropofflocationresponse) > 0) {
                    $index = 1;
                    foreach ($dropofflocationresponse as $dropoff) {
                        $selected = 0;
                        if ($lat == $dropoff['lat'] && $lng == $dropoff['lng']) {
                            $selected = 1;
                        }
                        $address = $dropoff['addressline1'] . " " . $dropoff['addressline2'];
                        $addressLen = strlen($address);
                        $addressline1 = "";
                        $addressline2 = "";
                        if ($addressLen > 25) {
                            $addsplit = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $address, -1, PREG_SPLIT_NO_EMPTY);
                            $addline1Len = 0;
                            foreach ($addsplit as $add) {
                                $addline1Len = strlen($addressline1) + strlen($add);
                                if ($addline1Len < 25)
                                    $addressline1 .= $add . " ";
                                else {
                                    $addressline2 .= $add . " ";
                                }
                            }
                        } else {
                            $addressline1 = $dropoff['addressline1'];
                        }

                        $location[] = array('companyname' => $dropoff['companyname'],
                            'addressline1' => $addressline1,
                            'addressline2' => $addressline2,
                            'addressline3' => $dropoff['addressline3'],
                            'city' => $dropoff['city'],
                            'postcode' => $dropoff['postcode'],
                            'country' => $dropoff['country'],
                            'telephone' => $dropoff['telephone'],
                            'lat' => $dropoff['lat'],
                            'lng' => $dropoff['lng'],
                            'Mon' => $dropoff['Mon'],
                            'Tue' => $dropoff['Tue'],
                            'Wed' => $dropoff['Wed'],
                            'Thu' => $dropoff['Thu'],
                            'Fri' => $dropoff['Fri'],
                            'Sat' => $dropoff['Sat'],
                            'Sun' => $dropoff['Sun'],
                            'selected' => $selected,
                            'index' => $index);
                        $index++;
                    }
                }
            }
//            $serviceObj = new services($this->form_vars["service_id"]);
            $carrierId = $serviceObj->getCarrierId();
            $carrierObj = new Carrier($carrierId);
            $output['location_data'] = $location;
            $output['carrier_logo'] = $carrierObj->getLogo();
            $output['carrier_name'] = $carrierObj->getCarrier();
            echo json_encode($output);
            die;
        }

        /*
         * DROP OFF 
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "DROPOFF_SELECTED_SERVICE") {
            $companyname = $this->form_vars["companyname"];
            $addressline1 = $this->form_vars["addressline1"];
            $addressline2 = $this->form_vars["addressline2"];
            $addressline3 = $this->form_vars["addressline3"];
            $city = $this->form_vars["city"];
            $postcode = $this->form_vars["postcode"];
            $country = $this->form_vars["country"];
            $telephone = $this->form_vars["telephone"];
            $mon = $this->form_vars["mon"];
            $tue = $this->form_vars["tue"];
            $wed = $this->form_vars["wed"];
            $thu = $this->form_vars["thu"];
            $fri = $this->form_vars["fri"];
            $sat = $this->form_vars["sat"];
            $sun = $this->form_vars["sun"];
            $carrierlogo = $this->form_vars["carrierLogo"];
            $carriername = $this->form_vars["carrierName"];
            $lat = $this->form_vars["lat"];
            $lng = $this->form_vars["lng"];
            $serviceId = $this->form_vars["serviceId"];

            if ($country != '') {
                $countryFilter = new CountryFilter();
                $countryFilter->addFieldFilter("iso", $country);
                $countrylist = $countryFilter->getList();
                if (count($countrylist) > 0) {
                    $countryId = $countrylist[0]->getId();
                }
            }
            // Save Drop Off Data
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $serviceId);
            $dropoffFilter->addFieldFilter("country", $country);
            $dropoffFilter->addFieldFilter("companyname", $companyname);
            $dropoffList = $dropoffFilter->getList();
            if (count($dropoffList) == 0) {
                $dropUserLocation = new DropoffUserLocation();
                $dropUserLocation->setUserId($this->user->getId());
                $dropUserLocation->setServiceId($serviceId);
                $dropUserLocation->setCompanyname($companyname);
                $dropUserLocation->setAddressLine1($addressline1);
                $dropUserLocation->setAddressLine2($addressline2);
                $dropUserLocation->setAddressLine3($addressline3);
                $dropUserLocation->setCity($city);
                $dropUserLocation->setPostcode($postcode);
                $dropUserLocation->setCountry($country);
                $dropUserLocation->setTelephone($telephone);
                $dropUserLocation->setMon($mon);
                $dropUserLocation->setTue($tue);
                $dropUserLocation->setWed($wed);
                $dropUserLocation->setThu($thu);
                $dropUserLocation->setFri($fri);
                $dropUserLocation->setSat($sat);
                $dropUserLocation->setSun($sun);
                $dropUserLocation->setAddedDate(strtotime(date('Y-m-d H:i:s')));
                $dropUserLocation->setAddedBy($this->user->getId());
                $dropUserLocation->setLat($lat);
                $dropUserLocation->setLng($lng);
                $dropUserLocation->save();
            }
            $output .= '<div class="row">
                            <h4><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_100_' . $carrierlogo . '" /> ' . $carriername . '</h4>';
            $output .= '<div class="col-md-12">';
            $output .= '<label><h3 class="drop_off_data" id="drop_off_company">' . $companyname . '</h3></label><br />';
            $output .= '<label class="drop_off_data" id="drop_off_address_line_1">' . $addressline1 . '</label><br />';
            $output .= '<label class="drop_off_data" ' . ($addressline2 == "" ? ' style="display: none;" ' : '') . ' id="drop_off_address_line_2">' . $addressline2 . '</label><br />';
            $output .= '<label class="drop_off_data" ' . ($addressline3 == "" ? ' style="display: none;" ' : '') . ' id="drop_off_address_line_3">' . $addressline3 . '</label>';
            $output .= '<label class="drop_off_data" ' . ($city == "" ? ' style="display: none;" ' : '') . ' id="drop_off_city">' . $city . '</label>';
            $output .= '<label class="drop_off_data" id="drop_off_postcode_select">' . $postcode . '</label>,';
            $output .= '<label class="drop_off_data" id="drop_off_country">' . $countryId . '</label>';
            $output .= '<label class="drop_off_data" ' . ($country == "" ? '' : 'style="display: block;" ') . ' id="drop_off_telephone">' . $telephone . '</label>';
            $output .= '</div><div class="col-md-12">';
            $output .= '<label><h3><b>Opening Hours</b></h3></label></div>';
            $output .= '<div  class="col-md-2">Mon</div><div  class="col-md-10">' . $mon . '</div>';
            $output .= '<div  class="col-md-2">Tue</div><div  class="col-md-10">' . $tue . '</div>';
            $output .= '<div  class="col-md-2">Wed</div><div  class="col-md-10">' . $wed . '</div>';
            $output .= '<div  class="col-md-2">Thu</div><div  class="col-md-10">' . $thu . '</div>';
            $output .= '<div  class="col-md-2">Fri</div><div  class="col-md-10">' . $fri . '</div>';
            $output .= '<div  class="col-md-2">Sat</div><div  class="col-md-10">' . $sat . '</div>';
            $output .= '<div  class="col-md-2">Sun</div><div  class="col-md-10">' . $sun . '</div>';
            $storedetails = "companyname:" . $companyname . ",addressline1:" . $addressline1 . ",addressline2:" . $addressline2 . ",addressline3:" . $addressline3 . ",city:" . $city . ",postcode:" . $postcode . ",country:" . $country . ",telephone:" . $telephone;
            $base64storedetails = base64_encode($storedetails);
            $output .= '</div><div style="clear:both"><br /></div><div class="col-md-6">'
                    . '<a  data-store-detail="' . $base64storedetails . '" id="btn_select_location" class="btn btn-sm blue">'
                    . '<span></span>&nbsp;Select Location</a></div>';
            $output .= '</div>';
            echo $output;
            exit;
        }

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "GET_SERVICE_DESCRIPTION") {
            $service_id = (int) trim($this->form_vars['service_id']);
            $serviceDetail = new Services($service_id);
            if ($serviceDetail->getId() > 0) {
                $description = $serviceDetail->getDescription();
                $output['STATUS'] = 'SUCCESS';
                if (trim($description) == '')
                    $description = formatMessages(ERROR_NO_SERVICE_DETAIL); //'Service discription is not available.';
                $output['MESSAGE'] = $description;
                $output['NAME'] = $serviceDetail->getName() . ' Service';
            } else {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = formatMessages(ERROR_NO_SERVICE_DETAIL);
            }
            //}
            echo json_encode($output);
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            #map{
                width:100%;
                height:600px;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDM4nwr48gdWBG0QTTiosoLkQMKAdzZrk&region=GB"></script>
        <script type="text/javascript">
            var InforObj = [];
            var locations = '';
            var map;
            var previousmarker = null;
            var currentmarker = null;
            // Map load for Drop off
            function initMap(countryIso = '', postcode = '')
            {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {lat: 51.5, lng: -0.41}
                });

                var geocoder = new google.maps.Geocoder();
                geocodeAddress(geocoder, map, countryIso, postcode);


            }

            // Location on map based on address
            function geocodeAddress(geocoder, resultsMap, countryIso, postcode) {

                var address = postcode + "," + countryIso;
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status === 'OK') {
                        resultsMap.setCenter(results[0].geometry.location);
                        //                    var marker = new google.maps.Marker({
                        //                      map: resultsMap,
                        //                      position: results[0].geometry.location
                        //                    });
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            }

            //Add marker on map
            function initMarker(locations, carrierName, carrierLogo) {
                $("#dropoff-content-display").html('');
                var iconBase = '../images/carrierlogo/thumbnail/owe_16_' + carrierLogo;
                var hoverIconBase = '../images/carrierlogo/thumbnail/owe_50_' + carrierLogo;
                var contentString = '<div id="content"><h5>' + locations.companyname + '</h5><br /> ' + locations.addressline1 + '<br /> ' + locations.addressline2 + '<br /> ' + locations.city + '<br /> ' + locations.postcode + '<br /> ' + locations.country + '</div>';
                //Add Marker on map
                const marker = new google.maps.Marker({
                    position: {lat: parseFloat(locations.lat), lng: parseFloat(locations.lng)},
                    icon: iconBase,
                    map: map,
                    id: 'test_' + locations.lat,
                    animation: google.maps.Animation.DROP
                            //zIndex: parseFloat(locations[i].index)
                });
                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                    maxWidth: 200
                });
                marker.addListener('mouseover', function () {
                    closeOtherInfo();
                    infowindow.open(marker.get('map'), marker);
                    InforObj[0] = infowindow;
                });
                marker.addListener('mouseout', function () {
                    closeOtherInfo();
                    infowindow.close();
                    InforObj[0] = infowindow;
                });
                // Add info window to marker    
                google.maps.event.addListener(marker, 'click', (function () {
                    //                   console.log(marker.id);
                    return function () {
                        if (previousmarker !== null) {
                            previousmarker.setAnimation(null);
                            previousmarker.setIcon(iconBase);
                        }
                        previousmarker = marker;
                        map.setZoom(12);
                        //map.setCenter(marker.getPosition());
                        marker.setIcon(null);
                        marker.setIcon(hoverIconBase);
                        toggleBounce(marker);
                        var url = "consignment_quote.php";
                        var action = "DROPOFF_SELECTED_SERVICE";
                        var companyname = locations.companyname;
                        var addressline1 = locations.addressline1;
                        var addressline2 = locations.addressline2;
                        var addressline3 = locations.addressline3;
                        var city = locations.city;
                        var postcode = locations.postcode;
                        var country = locations.country;
                        var telephone = locations.telephone;
                        var mon = locations.Mon;
                        var tue = locations.Tue;
                        var wed = locations.Wed;
                        var thu = locations.Thu;
                        var fri = locations.Fri;
                        var sat = locations.Sat;
                        var sun = locations.Sun;
                        var lat = locations.lat;
                        var lng = locations.lng;
                        var serviceId = $("#service").val();
                        var carrierlogo = carrierLogo;
                        var carriername = carrierName;
                        $.post(url, {func: action, companyname: companyname,
                            addressline1: addressline1, addressline2: addressline2,
                            addressline3: addressline3, city: city, postcode: postcode, country: country, telephone: telephone,
                            mon: mon, tue: tue, wed: wed, thu: thu, fri: fri, sat: sat, sun: sun, carrierLogo: carrierlogo, carrierName: carriername, lat: lat, lng: lng, serviceId: serviceId
                        }, function (d) {
                            //$('#add-dropoffform-popup').modal('show');
                            $("#dropoff-content-display").html(d);
                            $("#dropoff-content-display").parent().animate({width: 'show'});
                        });
                    }
                })(marker));
                return marker;
            }

            // Load Map
            function loadMap(fromPostCode) {
                var serviceId = $("#service").val();
                var fromCountryId = $("#sender_country").val();
                if (serviceId == "") {
                    swal("", "Please select reciever country and service", "info");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "consignment_quote.php",
                        data: {form_action: "map_load", from_country_id: fromCountryId, service_id: serviceId, from_post_code: fromPostCode},
                        dataType: "json",
                        success: function (data) {
                            $("#close_drop_off_detail").trigger("click");
                            initMap(fromCountryId, fromPostCode);
                            locations = data.location_data;
                            var len = locations.length;
                            var i;
                            // Add multiple markers to map
                            for (i = 0; i < len; i++)
                            {
                                var retMarker = initMarker(locations[i], data.carrier_name, data.carrier_logo);
                                if (locations[i].selected == '1') {
                                    google.maps.event.trigger(retMarker, 'click');
                                }
                            }
                        },
                        error: function () {
                            swal("", "No data found", "info");
                        }
                    });
                    $("#drop_off_modal").modal("show");
                }
            }
            $(document).on('click', '#btn_reload_map', function (event, state) {
                var fromPostCode = $("#drop_off_postcode").val();
                loadMap(fromPostCode);
            });
            $(document).on('click', '#close_drop_off_detail', function (event, state) {
                $("#dropoff-content-display").parent().animate({width: 'hide'});
            });
            $(document).on('click', '#drop_off_link', function (event, state) {
                var fromPostCode = $("#sender_postcode").val();
                $("#drop_off_postcode").val(fromPostCode);
                loadMap(fromPostCode);
            });
            $(document).on('click', '#btn_select_location', function () {
                $("#sender_company").val($("#drop_off_company").html());
                $("#sender_address_line_1").val($("#drop_off_address_line_1").html());
                $("#sender_address_line_2").val($("#drop_off_address_line_2").html());
                $("#sender_address_line_3").val($("#drop_off_address_line_3").html());
                $("#sender_city").val($("#drop_off_city").html());
                $("#sender_postcode").val($("#drop_off_postcode_select").html());
                $("#sender_telephone").val($("#drop_off_telephone").html());
                $("#collection_country").val($("#drop_off_country").html()).selectpicker('refresh');
                $("#drop_off_modal").modal("hide");
            });
            function toggleBounce(marker) {
                if (marker.getAnimation() !== null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
            function closeOtherInfo() {
                if (InforObj.length > 0) {
                    /* detach the info-window from the marker ... undocumented in the API docs */
                    InforObj[0].set("marker", null);
                    /* and close it */
                    InforObj[0].close();
                    /* blank the array */
                    InforObj.length = 0;
                }
            }
            $(document).ready(function () {
                if ($('.date_picker').length > 0) {
                    var dateToday = new Date();
                    //init date pickers
                    $('.date_picker').datepicker({
                        autoclose: true,
                        startDate: dateToday,
                    });
                }
                if (jQuery().timepicker) {
                    $('.timepicker-default1').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                    $('.timepicker-default2').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                }
                
                //        Handle User File upload
                $("#upload_file").click(function () {
                    
                    var exitingDocCheck = $('.doc_upload_view')[0];
                    if(exitingDocCheck){
                        swal("", "You can only upload one file. Please remove file before uploading new one.", "error");
                        return false;
                    }
                    var filename = $("#file_name").val();
                    if (filename == "") {
                        swal("", "Please Select File", "info");
                        return false;
                    } else {
                        var file_data = $('#file_name').prop('files')[0];
                        var form_data = new FormData();
                        form_data.append('file_name', filename);
                        form_data.append('action', "upload_user_doc");
                        form_data.append('user_doc_file', file_data);
                        $.ajax({
                            url: "consignment_quote.php", // point to server-side PHP script
                            dataType: 'html', // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (php_script_response) {
                                if (php_script_response == "0") {
                                    swal("Invalid file type", "You can only upload gif,jpeg,jpg,png and pdf file", "error");
                                } else {
                                    $("#append_user_doc").append(php_script_response);
                                    $("a.fileinput-exists").click();
                                }
                            }
                        });
                    }
                });

                //Handle Document Remove functioanlity
                $(document).on('click', '.remove_doc', function () {
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
                                    var userDocId = el.data("doc_id");
                                    $.ajax({
                                        url: 'consignment_quote.php',
                                        type: 'POST',
                                        data: {action: 'remove_user_doc', doc_id: userDocId},
                                        headers: {
                                        },
                                        success: function () {
                                            var divname = userDocId.split('.');
                                            $("#usr_doc_" + divname[0]).remove();
                                        },
                                        error: function (xhr, status, error) {

                                        }
                                    });

                                }
                            });

                });

                $(document).on('click', '.shipment_type', function (event, state) {
                    var shipment_type = $(this).val();
                    clearTextBoxDetail();

                });

                //Get Quotation on blur of weight, length, width and hiight
                /*$("#receiver_weight").blur(function () {
                 $("#service-div").hide();
                 $("#dangerous-div").hide();
                 $("#product-div").hide();
                 $("#consignment-detail").hide();
                 if ($("#receiver_weight").val() != '')
                 {
                 loadTariffDetails();
                 }
                         
                 $("#receiver_height").blur(function () {
                 $("#service-div").hide();
                 $("#dangerous-div").hide();
                 $("#product-div").hide();
                 $("#consignment-detail").hide();
                 if ($("#receiver_weight").val() > 0 && $("#receiver_height").val() > 0 && $("#receiver_width").val() > 0 && $("#receiver_length").val() > 0)
                 {
                 loadTariffDetails();
                 }
                 });
                 $("#receiver_width").blur(function () {
                 $("#service-div").hide();
                 $("#dangerous-div").hide();
                 $("#product-div").hide();
                 $("#consignment-detail").hide();
                 if ($("#receiver_weight").val() > 0 && $("#receiver_height").val() > 0 && $("#receiver_width").val() > 0 && $("#receiver_length").val() > 0)
                 {
                 loadTariffDetails();
                 }
                 });
                 $("#receiver_length").blur(function () {
                 $("#service-div").hide();
                 $("#dangerous-div").hide();
                 $("#product-div").hide();
                 $("#consignment-detail").hide();
                 if ($("#receiver_weight").val() > 0 && $("#receiver_height").val() > 0 && $("#receiver_width").val() > 0 && $("#receiver_length").val() > 0)
                 {
                 loadTariffDetails();
                 }
                 });
                 */

                // Save consignment Logic
                function saveShipmentData() {


                    var data = new FormData();


                    var frmData = $("#getQuotationForm").serializeArray();
                    $.each(frmData, function (key, input) {
                        data.append(input.name, input.value);
                    });

                    //File data
                   // var file_data = $('#invoice_file').prop('files')[0];
                    //data.append('invoice_doc_file', file_data);

                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "consignment_quote.php",
                        data: data,
                        dataType: "json",
                        //enctype: 'multipart/form-data',
                        processData: false, // Important!
                        contentType: false,
                        cache: false,
                        success: function (json) {
                            $('#consignment_id').val(json.CONSIGNMENT_ID);
                            if (json.STATUS == 'SUCCESS' && json.CONSIGNMENT_ID > 0)
                            {
                                $("#getQuotationForm").find('input[type=text]:not(".not_clear"), textarea, select').val("");
                                $('input:checkbox').removeAttr('checked');
                                $('#consignment_id').val('');
                                $('#remotearea').val();
                                $('#service').val('');
                                $('#countryid').val();
                                $('#serviceType').val();
                                $('#shipmentType').val();
                                $('.doc_upload_view').remove();
                                $('#temp_invoice_name').val('');
                                $("select").selectpicker('refresh');
                                clearTextBoxDetail();
                                var labelpath = "<?php echo SETTING_URL ?>" + "_assets/pdf/";
                                if (json.COLLECTIONNO == '' || labelpath != json.INSTANT_LABEL) {
                                    var msg = "You have successfully created label, please <a class='btn btn-primary btn-xs'  href='" + json.INSTANT_LABEL + "' target='_blank'>click here</a> to view label or " +
                                            "<a class='btn btn-primary btn-xs' href='" + json.URL + "' target='_blank'>click here</a> to view list.";
                                    window.open(json.INSTANT_LABEL, '', 'width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
                                } else
                                {
                                    var msg = "You have successfully booked collection. Your collection confirmation no is '" + json.COLLECTIONNO + "'.  please <a class='btn btn-primary btn-xs' href='" + json.URL + "' target='_blank'>click here</a> to view list.";
                                }
                                show_res_msg("success", msg);
                            } else if (json.STATUS == 'ERROR') {
                                show_res_msg("error", json.MESSAGE);
                            } else {
                                show_res_msg("error", "We have encounter technical error, Please try again later or contact to administrator.");
                            }
                            window.scrollTo(0, 0);
                            $.unblockUI();

                        },
                        error: function () {
                            $.unblockUI();
                            show_res_msg('error', 'We have encounter technical error, Please try again later or contact to administrator.');
                        }
                    });
                }

                $(".btn_save").click(function () {
                    swal({
                        title: "Are you sure you want to generate label ?",
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
                                    $("#save_invalid").val(1);
                                    saveShipmentData();
                                }
                            });
                });

                // Add multiple parcel
                $(".initial-button").hide();
                var elindex = 0;
                $(document).on('click', '.add-more-parcel-keys', function () {
                    elindex++;
                    var clone = $(this).parent().parent().clone();
                    var parcelWeight = $(clone).find('.parcel_weight').attr('name');
                    var parcelLength = $(clone).find('.parcel_length').attr('name');
                    var parcelWidth = $(clone).find('.parcel_width').attr('name');
                    var parcelHeight = $(clone).find('.parcel_height').attr('name');

                    $(this).remove();
                    $(clone).find('input').val('0');
                    $(clone).find('.parcel_weight').val('');
                    $(clone).find('.parcel_weight').attr('name', parcelWeight.replace(/\d+/, elindex));
                    $(clone).find('.parcel_length').attr('name', parcelLength.replace(/\d+/, elindex));
                    $(clone).find('.parcel_width').attr('name', parcelWidth.replace(/\d+/, elindex));
                    $(clone).find('.parcel_height').attr('name', parcelHeight.replace(/\d+/, elindex));

                    $(clone).find('button.remove-parcel-key').show();
                    $(clone).find('button.remove-parcel-key').removeClass('initial-button');
                    $(clone).appendTo($('.parcelDetails'));
                });

                //Remove Multiple Parcel
                $(document).on('click', '.remove-parcel-key', function () {
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
                                    if ($('.parcelDetails .remove-parcel-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        if ($('.add-more-parcel-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-parcel-keys').clone();
                                            $('.parcelDetails .remove-parcel-key').last().parent().prepend(addMore);
                                        }
                                        if ($(".parcel_weight").length == 1) {
                                            $(".parcelDetails .remove-parcel-key").hide();
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                });

            });


            // Fetch all availabel services and price
            function loadTariffDetails() {
                $("#service-div").hide();
                $("#dangerous-div").hide();
                $("#product-div").hide();
                $("#plt-div").hide();
                $("#consignment-detail").hide();
                $("#consignment-collection-detail").hide();
                $("#drop_off_link").hide();
                $("#show_general_msg").hide();
                $("#insurance-div").hide();
                var form_data = $("#getQuotationForm").serializeArray();
                form_data.push({name: "func", value: 'getQuotation'});

                $.blockUI();
                $.ajax({
                    url: "consignment_quote.php",
                    data: form_data,
                    type: "POST",
                    dataType: "JSON",
                    async: false
                })
                        .done(function (data) {
                            $.unblockUI();
                            if (data.STATUS != "ERROR") {
                                $("#service-div").show();
                                $("#services-details").html(data.MESSAGE);
                            } else
                            {
                                show_res_msg("error", data.MESSAGE);
                            }
                        });

            }

            function addmultipiece() {
                $("#btnAddMultiPieceDiv").hide();
                $('#addMultiPieceWeight').show();
            }

            function addDefaultValue() {
                // Assign default data
                $("#sender_company").val($("#sender_company").data("sender_company"));
                //                $("#sender_contact").val($("#sender_contact").data("sender_contact"));
                $("#sender_email").val($("#sender_email").data("sender_email"));
                $("#sender_telephone").val($("#sender_telephone").data("sender_telephone"));
                $("#sender_address_line_1").val($("#sender_address_line_1").data("sender_address_line_1"));
                $("#sender_address_line_2").val($("#sender_address_line_2").data("sender_address_line_2"));
                $("#sender_address_line_3").val($("#sender_address_line_3").data("sender_address_line_3"));
                $("#sender_city").val($("#sender_city").data("sender_city"));
                $("#sender_state").val($("#sender_state").data("sender_state"));
                $("#sender_postcode").val($("#sender_postcode").data("sender_postcode"));
            }

            function showErrorMessageSA(message, type) {
                var swMsg = message;
                swal({
                    title: swMsg,
                    text: "",
                    type: type,
                    html: true,
                    customClass: "swal-large",
                    //                        showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Ok",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                    allowOutsideClick: true
                },
                        function (isConfirm) {
                            if (isConfirm) {
                            }
                        });
            }
            function show_res_msg(type, msg) {
                $("html, body").animate({scrollTop: 0}, "slow");
                $("#show_general_msg div.alert").html(" ");
                if (type == "success") {
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                } else if (type == "error") {
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                } else {
                    $("#show_general_msg div.alert").addClass('alert-info').removeClass('alert-success');
                }
                $("#show_general_msg div.alert").html(msg);
                $("#show_general_msg").show();
            }


            function createShipment(servicecode, remotearea, productDetail, email_required, telephone_required, productname = '', region, tracked, serviceid, serviceType, insurance, isPaperless)
            {
                $('#dangerous-div').show();
                $('#tracked').val(tracked);
                $('.services').removeClass('alert-info');
                $('#' + servicecode).addClass('alert-info');
                $('input[name=shipment_content]').click(function ()
                {
                    loadShipmentContent(servicecode, remotearea, productDetail, email_required, telephone_required, productname, region, tracked, serviceid, serviceType, insurance, isPaperless);
                });
                loadShipmentContent(servicecode, remotearea, productDetail, email_required, telephone_required, productname, region, tracked, serviceid, serviceType, insurance, isPaperless);

            }

            function loadShipmentContent(servicecode, remotearea, productDetail, email_required, telephone_required, productname, region, tracked, serviceid, serviceType, insurance, isPaperless)
            {
                var shipment_type = $("input[name='shipment_type']:checked").val();
                if ($('input[name=shipment_content]:checked').length == 0)
                {
                    $('#dangerous_goods_message').hide();
                    $("#insurance-div").hide();
                    $("#product-div").hide();
                    $("#plt-div").hide();
                    $("#product-details").hide();
                    $("#consignment-detail").hide();
                    if (shipment_type == "C") {
                        $("#consignment-collection-detail").hide();
                    }
                    $("#savelist").prop("disabled", true);
                    $("#save").prop("disabled", true);
                } else {
                    if ($('input[name=shipment_content]:checked').val() != "normal" && $('input[name=shipment_content]:checked').length != 0)
                    {
                        $('#dangerous_goods_message').show();
                        $("#insurance-div").hide();
                        $("#product-div").hide();
                        $("#plt-div").hide();
                        $("#product-details").hide();
                        $("#consignment-detail").hide();
                        if (shipment_type == "C") {
                            $("#consignment-collection-detail").hide();
                        }
                        $("#savelist").prop("disabled", true);
                        $("#save").prop("disabled", true);
                    } else if ($('input[name=shipment_content]:checked').val() == "normal")
                    {
                        if (insurance == 1) {
                            $("#insurance-div").show();
                        } else
                        {
                            $("#insurance-div").hide();
                        }
                        if (isPaperless == 1) {
                            $("#plt-div").show();
                        } else
                        {
                            $("#plt-div").hide();
                        }

                        if (shipment_type == "C") {
                            $("#shipper_detail_div").html("Collection Details");
                            $("#consignment-collection-detail").show();
                            $(".drop_off_cls").val('');
                            $(".drop_off_cls").prop('readonly', false);
                            $("#shipper_details_content").show();
                        } else if (shipment_type == "DO") {
                            $("#shipper_detail_div").html("Drop-off Details");
                            $("#drop_off_link").show();
                            $("#consignment-collection-detail").show();
                            $("#shipper_details_content").hide();

                            $(".drop_off_cls").val('');
                            // Assign default data
                            addDropOffValue();
                            // End assign default data
                            $(".drop_off_cls").prop('readonly', true);
                        }

                        $('#dangerous_goods_message').hide();
                        $('#service').val(serviceid);
                        $('#product_name').val(productname);
                        $('#serviceType').val(serviceType);

                        //$('.services').removeClass('alert-info');
                        //$('#'+servicecode).addClass('alert-info');
                        $('#remotearea').val(remotearea);
                        $("#product-div").show();

                        $("#product-details").show();
                        $("#product-details").html($(document).find("." + productDetail).html());
                        $("#consignment-detail").show();
                        $("#city1").val($("#receiver_city").val());
                        $("#postcode1").val($("#receiver_postcode").val());
                        $("#countryid").val($("#sender_country").val());
                        $("#shipmentType").val(shipment_type)
                        $("#country1").val($("#sender_country option:selected").text());
                        if (email_required == 1)
                            $("#email_req").text("*");
                        if (telephone_required == 1)
                            $("#telephone_req").text("*");
                        $("#region").val(region);

                        $("#savelist").prop("disabled", false);
                        $("#save").prop("disabled", false);

                    }
                }

                //dangerous_goods_message

            }

            function addDropOffValue() {
                // Assign default data
                $("#sender_company").val($("#sender_company").data("drop_off_sender_company"));
                //                $("#sender_contact").val($("#sender_contact").data("drop_off_sender_contact"));
                $("#sender_email").val($("#sender_email").data("drop_off_sender_email"));
                $("#sender_telephone").val($("#sender_telephone").data("drop_off_sender_telephone"));
                $("#sender_address_line_1").val($("#sender_address_line_1").data("drop_off_sender_address_line_1"));
                $("#sender_address_line_2").val($("#sender_address_line_2").data("drop_off_sender_address_line_2"));
                $("#sender_address_line_3").val($("#sender_address_line_3").data("drop_off_sender_address_line_3"));
                $("#sender_city").val($("#sender_city").data("drop_off_sender_city"));
                $("#sender_state").val($("#sender_state").data("drop_off_sender_state"));
                if ($("#sender_postcode").data("drop_off_sender_postcode") != undefined) {
                    $("#sender_postcode").val($("#sender_postcode").data("drop_off_sender_postcode"));
                } else {
                    $("#sender_postcode").val($("#sender_postcode").data("sender_postcode"));
                }
            }

            function clearTextBoxDetail()
            {
                $('#receiver_postcode').val('');
                $('#receiver_city').val('');
                $("#service-div").hide();
                $("#dangerous-div").hide();
                $("#insurance-div").hide();

                $("#product-div").hide();
                $("#plt-div").hide();
                $("#consignment-detail").hide();
                $("#consignment-collection-detail").hide();
                $("#drop_off_link").hide();
                $("#show_general_msg").hide();
                $("#getQuotationForm").find('input[type=text]:not(".not_clear"), textarea').val("");
                // $("select").selectpicker('refresh');
            }

        </script>
        .label-account{
        font-size: 14px;
        font-weight: bold;
        }
        .blockUI {
        z-index: 99999999 !important;
        }
        </style>
        <?php
    }

    protected function renderBody() {
        extract($this->form_vars);
        if ($this->form_vars["id"] > 0) {
            if (!empty($this->consignmentEditError) && $this->consignmentStatus == consignment::STATUS_INVALID) {
                $this->flashMsg->error($this->consignmentEditError);
            }
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Get Quote & Create Shipment
                </div>
                <div class="actions">

                </div>
                <div class="tools">
                </div>
            </div>

            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <form name="getQuotationForm" id="getQuotationForm" action="" method="post" enctype="multipart/form-data">
                    <!--                     <form name="consignment_save" id="consignment_save" action="" method="post">-->
                    <!-- Detail Panel for Quotation  -->
                    <fieldset class="fsStyle">
                        <legend class="legendStyle"><label class="label-account">Details</label></legend>

                        <div class="row">
                            <?php if (Permissions::checkFilePermission('add_consignment_chk_service_type')) { ?>
                                <div class="form-group" style="margin-left: 6px;">
                                    <div class="col-md-9">
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" class="shipment_type" name="shipment_type" id="dispatch" value="D" <?php
                                if (isset($shipment_type) && ($shipment_type == "D")) {
                                    echo "checked='checked'";
                                } if (empty($shipment_type)) {
                                    echo "checked='checked'";
                                }
                                ?> > Dispatch
                                                <span></span>
                                            </label>
                                            <label class="mt-radio">
                                                <input type="radio" class="shipment_type" name="shipment_type" id="collection" value="C" <?php
                                    if (isset($shipment_type) && ($shipment_type == "C")) {
                                        echo "checked='checked'";
                                    }
                                ?> > 3rd Party Collection
                                                <span></span>
                                            </label>
                                            <label class="mt-radio">
                                                <input type="radio" class="shipment_type" name="shipment_type" id="dropoff" value="DO" <?php
                                    if (isset($shipment_type) && ($shipment_type == "DO")) {
                                        echo "checked='checked'";
                                    }
                                ?> > Drop-Off
                                                <span></span>
                                            </label>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row" style="margin-left: -6px;">
                            <div class="col-md-4">
                                <div class="form-group  has-float-label">
                                    <?php echo Ddl::generateCountryDDL('sender_country', $sender_country, 'id', ' class="not_clear form-filter bs-select form-control" onchange="clearTextBoxDetail();"  required="" data-live-search="true" data-container="body" data-size="8" '); ?>
                                    <label class="label-account">Destination Country</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group  has-float-label">
                                    <input type="text" name="receiver_city" id="receiver_city" class="form-control" data-drop_off_city=""  data-receiver_city="<?php echo $receiver_city; ?>"  value="<?php echo $receiver_city; ?>" placeholder="City" rel="tooltip" title="City" />
                                    <label for="receiver_city" class="label-account">City</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group  has-float-label">
                                    <input type='text'  name='receiver_postcode' id='receiver_postcode' value='<?php echo $receiver_postcode; ?>' size='10' maxlength="9"  placeholder="Postcode"  class="form-control"  rel="tooltip" title="Postcode"  required />
                                    <label for="receiver_postcode" class="label-account">Postcode</label>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="parcel_box">
                            <div class="col-md-12">
                                <div class="table-responsive">

                                    <table class="table">

                                        <tbody class="parcelDetails">
                                            <tr>
                                                <td>
                                                    <div class="form-group  has-float-label input-icon right">
                                                        <i>Kg </i>
                                                        <input type='text'  name='parcel[0][receiver_weight]' value='<?php echo $receiver_weight; ?>' size='10' maxlength="9"  placeholder="Weight"  class="form-control parcel_weight"  rel="tooltip" title="Weight"  required />
                                                        <label for="receiver_weight" class="label-account">Weight</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group  has-float-label input-icon right">
                                                        <i>cm </i>
                                                        <input type='text'  name='parcel[0][receiver_length]'  value='<?php echo $receiver_length; ?>' size='10' maxlength="9"  placeholder="Length"  class="form-control parcel_length"  rel="tooltip" title="Length"  />
                                                        <label for="receiver_length" class="label-account">Length</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group  has-float-label input-icon right">
                                                        <i>cm </i>
                                                        <input type='text'  name='parcel[0][receiver_width]'   value='<?php echo $receiver_width; ?>' size='10' maxlength="9"  placeholder="Width"  class="form-control parcel_width"  rel="tooltip" title="Width"  />
                                                        <label for="receiver_width" class="label-account">Width</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group  has-float-label input-icon right">
                                                        <i>cm </i>
                                                        <input type='text'  name='parcel[0][receiver_height]' value='<?php echo $receiver_height; ?>' size='10' maxlength="9"  placeholder="Height"  class="form-control parcel_height"  rel="tooltip" title="Height"   />
                                                        <label for="receiver_height" class="label-account">Height</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success add-more-parcel-keys"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="btn btn-danger remove-parcel-key initial-button"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <a href="#" class="btn blue margin-bottom-5" data-original-title="" title="" onclick="loadTariffDetails();"> Get Quote </a>
                                </div>
                            </div> 
                        </div>

                    </fieldset>

                    <!-- AVAILABLE SERIVES PANEL -->
                    <div class="row"  id="service-div" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Available Services</label></legend>
                                <div id="general">
                                    <div class="col-md-12" id="services-details">

                                    </div>      
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <!-- DANGEROUS GOODS DETAIL -->
                    <div class="row"  id="dangerous-div" style="display:none;">
                        <div class="col-md-12">

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Dangerous Goods</label></legend>
                                <div id="general">
                                    <div class="col-md-12" id="dangerous-details" style=" padding:0px; margin:0px;">
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    Please be aware of the <a href='dangerous_goods.php' target='_blank'>Dangerous goods policy</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <label class="label-control"><h4><b>This shipment contains:</b></h4></label><br />
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="Lithium_batteries"	name="shipment_content" 
                                                                value="lithium_battery" /><b>  lithium battery </b>&nbsp;<br />
                                                    </label>
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="fire_extinguisher"	name="shipment_content" 
                                                                value="fire_extinguisher" /><b>  fire extinguisher </b>&nbsp;<br />
                                                    </label>
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="perfume"	name="shipment_content" 
                                                                value="perfume" /><b>  perfume</b>&nbsp;<br />
                                                    </label>
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="other_dangerous"	name="shipment_content" 
                                                                value="lithium_battery" /><b>  Other dangerous goods</b>&nbsp;<br />
                                                    </label><br />
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="normal"	name="shipment_content" 
                                                                value="normal" /><b>   None of Above</b>&nbsp;<br />
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="dangerous_goods_message" style="display:none;">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <span class="input-group-addon red-16" style="color:red;">
                                                        In order to ship dangerous goods, a dedicated contract has to be made with One World Express, please contact info@smarttrack.co for more information.
                                                    </span>	
                                                </div>  
                                            </div>
                                        </div>
                                    </div>      
                                </div>
                            </fieldset>
                        </div>
                    </div>


                    <!-- INSRANCE DETAIL -->
                    <div class="row"  id="insurance-div" style="display:none;">
                        <div class="col-md-12">

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Protect Your Shipment</label></legend>
                                <div id="general">
                                    <div class="col-md-12" id="dangerous-details" style=" padding:0px; margin:0px;">
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <h5>
                                                        The provision, at individual shipment level, 
                                                        of declared value coverage above Standard Liability for the amount necessary to repair or replace a shipment in the event of physical loss or damage. 
                                                        <br /> <br />
                                                        2.00% of the replacement value with a minimum charge of 17.00 GBP
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <label class="label-control">
                                                        <input  type="checkbox" id="insurance_agree"	name="insurance_agree" 
                                                                value="1" /><b>  I would like to insure my shipment. </b>&nbsp;<br />
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>      
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <!-- PRODUCT DETAIL PANEL -->      
                    <div class="row"  id="product-div" style="display:none;">
                        <div class="col-md-12">

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Product Information</label></legend>
                                <div id="general">
                                    <div class="col-md-12" id="product-details">

                                    </div>      
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <!-- PAPER LESS TRADE PANEL -->      
                    <div class="row"  id="plt-div" style="display:none;" >
                        <div class="col-md-12">

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Paperless Trade</label></legend>
                                
                                <div id="plt-details">
                                    <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Document</label> <br style="clear:both;"/>

                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                        <span class="fileinput-filename"> </span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="file_name" id="file_name" accept="application/pdf"> </span>
                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                    <a href="javascript:;" class="input-group-addon btn blue" id="upload_file" data-original-title="" title="">Upload</a>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    
                                    <div class="row" id="append_user_doc">
                                         
                                    </div>
                                    
                                </div>      
                                
                            </fieldset>
                        </div>
                    </div>
                    <!-- RECEIVER DETAIL PANEL -->

                    <div class="row" id="consignment-collection-detail" style="display:none;">
                        <div class="col-md-6">
                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account"> <span id="shipper_detail_div">Collection Details</span></label></legend>
                                <div class="cold-md-12">
                                    <a class="btn blue margin-bottom-10" id="drop_off_link" style="display: none;" href="#"><i class="fa fa-globe"></i>&nbsp;Drop of location</a>
                                    <input type="hidden" id="map_lat" value="" name="map_lat" />
                                    <input type="hidden" id="map_long" value="" name="map_long" />

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' name='sender_contact'  tabindex="10" id='sender_contact' value='<?php echo @$sender_contact; ?>'  placeholder="Contact Person" class="form-control"	rel="tooltip" title="Contact Person" required/>                
                                            <label for="sender_contact">Contact <span class="required" aria-required="true" data-original-title="" title=""> * </span></label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' name='sender_company' id='sender_company' tabindex="11" value='<?php echo @$sender_company; ?>'   placeholder="Company" class="form-control drop_off_cls"	rel="tooltip" title="Company"  />
                                            <label for="sender_company">Company</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_address_line_1' id='sender_address_line_1' tabindex="12" value='<?php echo @$sender_address_line_1; ?>'   placeholder="Address Line 1"  class="form-control drop_off_cls"	rel="tooltip" title="Address Line 1"  required />
                                            <label for="sender_address_line_1">Address Line 1 <span class="required" aria-required="true" data-original-title="" title=""> * </span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_address_line_2' id='sender_address_line_2' tabindex="13" value='<?php echo @$sender_address_line_2; ?>'   placeholder="Address Line 2"  class="form-control drop_off_cls"	rel="tooltip" title="Address Line 2" />
                                            <label for="sender_address_line_2">Address Line 2 </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_address_line_3' id='sender_address_line_3' tabindex="14" value='<?php echo @$sender_address_line_3; ?>'     placeholder="Address Line 3"  class="form-control drop_off_cls"	rel="tooltip" title="Address Line 3" />
                                            <label for="sender_address_line_3">Address Line 3 </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_city' id='sender_city' value='<?php echo @$sender_city; ?>'  tabindex="15"   placeholder="City"  class="form-control drop_off_cls"	rel="tooltip" title="City"  />
                                            <label for="sender_city">City</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_postcode' id='sender_postcode' value='<?php echo @$sender_postcode; ?>'  tabindex="16"   placeholder="Postcode"  class="form-control drop_off_cls"	rel="tooltip" title="Postcode"  />
                                            <label for="sender_postcode">Postcode</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label">
                                            <?php echo Ddl::generateCountryDDL('collection_country', $collection_country, 'id', ' class="not_clear form-filter bs-select form-control" "  required="" data-live-search="true" data-container="body" data-size="8" '); ?>
                                            <label for="collection_country">Country</label>
                                        </div>
                                    </div>
                                </div>


                                <div style="clear:both;"></div>

                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="fsStyle" style="min-height:225px;">
                                <legend class="legendStyle"><label class="label-account">Contact+</label></legend>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='sender_telephone' id='sender_telephone' tabindex="17" value='<?php echo @$sender_telephone; ?>'   placeholder="Telephone"  class="form-control"	rel="tooltip" title="Telephone"  />
                                            <label for="sender_telephone">Telephone</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' name='sender_email' tabindex="18" id='sender_email' value='<?php echo @$sender_email; ?>' placeholder="E-mail"  class="form-control"	rel="tooltip" title="E-mail"/>
                                            <label for="sender_email">E-mail</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="shipper_details_content">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input-icon has-float-label right">
                                                <i class="fa fa-calendar"></i>
                                                <input data-date-format="dd-mm-yyyy" type="text" size="19" class="form-control date_picker" name="collection_date" value="<?php echo formatDate($collection_date); ?>" id="collection_date"  placeholder="Collection Date"/>
                                                <label for="collection_date">Collection Date</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="input-icon has-float-label right">
                                                <i class="fa fa-clock-o"></i>
                                                <input type="text" class="form-control timepicker timepicker-default1" name="collection_start_time" value="<?php echo $collection_start_time; ?>" id="collection_start_time"  placeholder="Collection Start Time"/> 
                                                <label for="collection_start_time">Collection Start Time</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="input-icon has-float-label right">
                                                <i class="fa fa-clock-o"></i>
                                                <input type="text" class="form-control timepicker timepicker-default2" name="collection_end_time" value="<?php echo $collection_end_time; ?>" id="collection_end_time"  placeholder="Collection End Time"/>
                                                <label for="collection_end_time">Collection End Time</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </fieldset>
                        </div>      
                    </div>
                    <div class="row" id="consignment-detail" style="display:none;">
                        <div class="col-md-12">
                            <div id="consignment_error" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Receiver Details</label></legend>

                                <input type='hidden' name='consignment_id' id='consignment_id' value='' />
                                <input type='hidden' name='remotearea' id='remotearea' value='' />
                                <input type='hidden' name='service' id='service' value=''/>
                                <input type='hidden' name='product_name' id='product_name' value=''  />
                                <input type='hidden' name='countryid' id='countryid' value=''  />
                                <input type='hidden' name='serviceType' id='serviceType' value=''  />
                                <input type='hidden' name='shipmentType' id='shipmentType' value=''  />




                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' name='contact'  tabindex="22" id='contact' value='<?php echo @$contact; ?>' <?php echo $readonly_str ?>   placeholder="Contact Person" class="form-control"	rel="tooltip" title="Contact Person" required/>                
                                            <label for="contact">Contact <span class="required" aria-required="true" data-original-title="" title=""> * </span></label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' name='company' id='company' tabindex="23" value='<?php echo @$company; ?>' <?php echo $readonly_str ?>  placeholder="Company" class="form-control"	rel="tooltip" title="Company"  />
                                            <label for="company">Company</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='address_line_1' id='address_line_1' tabindex="24" value='<?php echo @$address_line_1; ?>'  <?php echo $readonly_str ?>   placeholder="Address Line 1"  class="form-control"	rel="tooltip" title="Address Line 1"  required />
                                            <label for="address_line_1">Address Line 1 <span class="required" aria-required="true" data-original-title="" title=""> * </span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='address_line_2' id='address_line_2' tabindex="25" value='<?php echo @$address_line_2; ?>'  <?php echo $readonly_str ?> placeholder="Address Line 2"  class="form-control"	rel="tooltip" title="Address Line 2" />
                                            <label for="address_line_2">Address Line 2 </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='address_line_3' id='address_line_3' tabindex="26" value='<?php echo @$address_line_3; ?>'  <?php echo $readonly_str ?>   placeholder="Address Line 3"  class="form-control"	rel="tooltip" title="Address Line 3" />
                                            <label for="address_line_3">Address Line 3 </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' readonly="readonly"  name='city1' id='city1' value='<?php echo @$city; ?>'     placeholder="City"  class="form-control"	rel="tooltip" title="City"  />
                                            <label for="city1">City</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' readonly="readonly"  name='postcode1' id='postcode1' value='<?php echo @$postcode1; ?>'     placeholder="Postcode"  class="form-control"	rel="tooltip" title="Postcode"  />
                                            <label for="postcode1">Postcode</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text' readonly="readonly"  name='country1' id='country1' value='<?php echo @$country1; ?>'    placeholder="Country"  class="form-control"	rel="tooltip" title="Country"  />
                                            <label for="country1">Country</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='text'  name='telephone' id='telephone' tabindex="27" value='<?php echo @$telephone; ?>'  <?php echo $readonly_str ?>   placeholder="Telephone"  class="form-control"	rel="tooltip" title="Telephone"  />
                                            <label for="telephone">Telephone</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input type='email' name='email' tabindex="28" id='email' value='<?php echo @$email; ?>' placeholder="E-mail"  class="form-control"	rel="tooltip" title="E-mail"/>
                                            <label for="email">E-mail</label>
                                        </div>
                                    </div>
                                </div>

                                <div style="clear:both;"></div>

                                <div class="col-md-6" style="display:none;">
                                    <div class="form-group"> 
                                        <div class="input-group">
                                            <input name='notes' type='text' id='notes' value='<?php echo @$notes; ?>'  <?php echo $readonly_str ?>    title="Notes" rel="tooltip" class="form-control" placeholder="Notes"/>
                                            <span class="input-group-addon red-18"></span>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="fsStyle" style="min-height:275px;">
                                <legend class="legendStyle"><label class="label-account">Contents</label></legend>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input name='description' type='text' id='description' value='<?php echo @$description; ?>' size='50' maxlength="50" <?php echo $readonly_str ?>  title="Description" rel="tooltip" class="form-control" placeholder="Description"  tabindex="29"  required/>
                                            <label for="description">Description<span class="required" aria-required="true" data-original-title="" title=""></span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i>
                                            <input name='value' type='text' tabindex="30" id='value' value='<?php echo @$value; ?>' size='20' maxlength="8" <?php echo $readonly_str ?>  title="Value" rel="tooltip" class="form-control" placeholder="Value"  data-min="0.1"/>
                                            <label for="value">Value<span class="required" aria-required="true" data-original-title="" title=""></span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group  has-float-label">
                                        <?php
                                        $user = SessionManager::getUser();
                                        $item_currency = trim($user->getBillingCurrency());
                                        $arrayCurrency = array('GBP' => 'GBP', 'USD' => 'USD', 'CAN' => 'CAN', 'DFL' => 'DFL', 'DKR' => 'DKR', 'EUR' => 'EUR', 'FFR' => 'FFR', 'HKG' => 'HKG', 'INR' => 'INR', 'JPY' => 'JPY', 'NKR' => 'NKR', 'NLG' => 'NLG', 'SGD' => 'SGD', 'SFR' => 'SFR', 'SKR' => 'SKR', 'YEN' => 'YEN', 'PLN' => 'PLN');
                                        echo Ddl::generateArrayDDL('item_currency', $arrayCurrency, $item_currency, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Currency" placeholder="Currency"');
                                        ?>
                                        <label for="item_currency" class="label-account">Currency</label>
                                    </div>
                                </div>

                                <div style="clear:both;"></div>

                            </fieldset>

                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="form-group">

                                    <button type="button" id = "btn_save_invalid"  name="btn_save_invalid" class="btn btn-primary btn_save btn_save_invalid btn-lg" data-act='save'>Ship It</button>
                                    <input type="hidden" name="func" value="save_data_consignment" />
                                    <input type="hidden" id="save_invalid" name="save_invalid" value="0" data-original-title="" title="">
                                    <i style="display:none;" id="loader" class="fa fa-spinner fa-spin icon-large"></i> </div>
                            </div>
                        </div>        
                    </div>
                </form>    
            </div>

        </div>
        <!-- Drop Off Modal End -->
        <div class="modal fade" id="drop_off_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <!--                    <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title">Modal Title</h4>
                                        </div>-->
                    <div class="modal-body">
                        <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true"></button>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Postcode</label>
                                    <div class="has-float-label input-icon">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="drop_off_postcode" id="drop_off_postcode" class="form-control" value="">
                                        <span class="input-group-btn"><button type="button" name="btn_reload_map" id="btn_reload_map" class="btn btn-primary">Load Map</button></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="position:relative;">
                                <div id="map"></div>
                            </div>
                            <div class="col-md-3 portlet light pull-right"  style="display: none; right: 0px; position:absolute; height: 600px; overflow: auto;">
                                <button type="button" id="close_drop_off_detail" class="close pull-right"></button>
                                <div class="col-md-12" id="dropoff-content-display"></div>
                            </div>
                        </div>
                    </div>
                    <!--                    <div class="modal-footer">
                                            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn green">Save changes</button>
                                        </div>-->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Drop Off Modal End -->


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