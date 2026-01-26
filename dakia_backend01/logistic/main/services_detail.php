<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrier.class',
    'carrierfilter.class',
    'country.class',
    'countryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'documenttype.class',
    'documenttypefilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'services.class',
    'servicefilter.class',
    'servicedocument.class',
    'servicedocumentfilter.class',
    'collectiontimegroup.class',
    'collectiontimegroupfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'servicecountrytimefilter.class',
    'servicecountrytime.class',
    'servicecollectioncounty.class',
    'servicecollectioncountyfilter.class',
    'servicelog.class',
    'servicelogfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'agentdata.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class'
]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    public $error_list;
    public $collectionTimeGroups;
    private $agentList;
    private $agentDispatchList;
    private $isCustomized;
    private $serviceId;
    private $selectedCountryIds;
    private $allowedAgentId = "";
    private $validationType = "";

    protected function init()
    {
        $this->sessionUser = $user = SessionManager::getUser();
        $serviceId = 0;
        if (isset($_GET['id']) && $_GET['id'] > 0)
            $serviceId = $_GET['id'];
        $this->serviceId = $serviceId;
        /*
         * Get those agent which are allowed by this service
         * 
         */
        $agentDataObj = AgentDataFilter::checkServiceAgent($this->serviceId);
        if (count($agentDataObj) > 0) {
            foreach ($agentDataObj as $agentData) {
                $this->allowedAgentId .= "'" . $agentData->getId() . "',";
            }
        }
        $this->allowedAgentId = rtrim($this->allowedAgentId, ",");
        t_on(); // turn on trace for this page
        $editIndex = 'services_list.php?id=' . util_get_num(intval($this->form_vars["id"]));
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'services_list.php' => Translation::GetCaption("SERVICES"),
            'Add / Edit Service'
        );
        // Get services collection country handling
        if (isset($_POST['action']) && trim($_POST['action']) == 'service_collection_country_ajax') {
            $serviceType = $_POST['serviceType'];
            $serviceId = -1;
            if (isset($_POST['service_id']) && !empty($_POST['service_id']))
                $serviceId = $_POST['service_id'];
            $countryFilter = new CountryFilter();
//            if(trim($serviceType) != 'ALL')
//                    $countryFilter->addRegionFilter("'".$serviceType."'");
            if ($countryFilter->getCount() > 0) {
                $CountryAllList = $countryFilter->getList();
                foreach ($CountryAllList as $countryListdata) {
                    $selected = "";
                    if (ServiceCollectionCounty::checkServiceCollectionExists($serviceId, $countryListdata->getId()) > 0)
                        $selected = "selected=selected";
                    echo '<option value="' . strtoupper($countryListdata->getId()) . '" ' . $selected . ' >' . strtoupper($countryListdata->getName()) . '</option>';
                }
            } else {
                echo '<option value="">No Country</option>';
            }
            die;
        }
        //Data Table Handling
        if (isset($_GET['action']) && $_GET['action'] == "routing_ajax") {
            $user = Sessionmanager::getUser();
            //$customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
            $customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
            $customizedServicesRoutingFilter->addFieldFilter("customize_service_id", trim($_GET['id']), "");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchService = $this->form_vars['search_Service'];
                if (!empty($searchService))
                    $customizedServicesRoutingFilter->addFieldLikeFilter('service_id', $searchService);

                $searchCountry = $this->form_vars['search_Country'];
                if (!empty($searchCountry))
                    $customizedServicesRoutingFilter->addFieldFilter('country_id', $searchCountry);


                $searchFromWeight = $this->form_vars['search_FromWeight'];
                if (!empty($searchFromWeight))
                    $customizedServicesRoutingFilter->addFilter("from_weight >= '" . $searchFromWeight . "''");

                $searchToWeight = $this->form_vars['search_ToWeight'];
                if (!empty($searchToWeight))
                    $customizedServicesRoutingFilter->addFilter("to_weight <='" . $searchToWeight . "'");
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if ($dataTableColumnName == "Service") {
                    $dataTableColumnName = "service_id";
                } else if ($dataTableColumnName == "Country") {
                    $dataTableColumnName = "country_id";
                } else if ($dataTableColumnName == "From_weight") {
                    $dataTableColumnName = "from_weight";
                } else if ($dataTableColumnName == "To_weight") {
                    $dataTableColumnName = "to_weight";
                }
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $customizedServicesRoutingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $customizedServicesRoutingFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $customizedServicesRoutingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $customizedServicesRoutingFilter->setOffset($iDisplayStart);
            $customizedRoutingObjs = $customizedServicesRoutingFilter->getPagingList();
            $customizedRoutingDataArr = array();
            foreach ($customizedRoutingObjs as $customizedRouting) {
                $customizedRoutingArr = array();
                $serviceObj = new Services($customizedRouting->getServiceId());
                $carrierObj = new Carrier($serviceObj->getCarrierId());
                $countryobj = new Country($customizedRouting->getCountryId());
                if ($countryobj->getIso() != "" && file_exists('../assets/global/img/flags/' . strtolower($countryobj->getIso()) . '.png')) {
                    $cnimag = '<img src="../assets/global/img/flags/' . strtolower($countryobj->getIso()) . '.png" title="' . $countryobj->getName() . '" alt="' . $countryobj->getName() . '">';
                    $customizedRoutingArr['country'] = $cnimag . '&nbsp' . $countryobj->getName();
                } else {
                    $customizedRoutingArr['country'] = $countryobj->getName();
                }
                if ($carrierObj->getLogo() != "" && file_exists('../images/carrierlogo/thumbnail/owe_16_' . $carrierObj->getLogo())) {
                    $cimag = '<img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierObj->getLogo() . '" title="' . $serviceObj->getName() . '" alt="' . $serviceObj->getName() . '">';
                    $customizedRoutingArr['service'] = $cimag . '&nbsp' . $serviceObj->getName();
                } else {
                    $customizedRoutingArr['service'] = $serviceObj->getName();
                }


                $customizedRoutingArr['from_weight'] = $customizedRouting->getFromWeight();
                $customizedRoutingArr['to_weight'] = $customizedRouting->getToWeight();
                $customizedRoutingArr['option'] = "<a href='javascript:void(0)' data-service_id = '" . $serviceId . "' data-country_id = '" . $customizedRouting->getCountryId() . "' data-routing_id = '" . $customizedRouting->getId() . "' class='btndelete btn btn-xs red btn-outline'><span class='fa fa-trash'></span> </a>";
                $customizedRoutingDataArr [] = $customizedRoutingArr;
            }
            $customizedRoutingArrJson['data'] = $customizedRoutingDataArr;
            $customizedRoutingArrJson['draw'] = $sEcho;
            $customizedRoutingArrJson['recordsTotal'] = $iTotalRecords;
            $customizedRoutingArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($customizedRoutingArrJson);
            die;
        }
        //Save Routing details
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'save_routine') {
            $flmsg = new FlashMessages();
            $productId = $this->form_vars['customize_service_id'];
            $country = $this->form_vars['country'];
            $fromweight = $this->form_vars['fromweight'];
            $toweightlimit = $this->form_vars['toweight'];
            $serviceId = $this->form_vars['service_name'];
            $tTimeCheeck = true;
            $tempCountry = "";
            $tempService = "";
            $toweight = 0;
            $output = array();
            if (trim($productId) > 0 && trim($country) != '' && $fromweight >= 0 && $toweightlimit <= 30 && $serviceId > 0) {
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addIdFilter($serviceId);
                $serviceList = $serviceFilter->getColumnList("name, to_weight");
                if (count($serviceList) > 0) {
                    $serviceName = $serviceList[0]->getName();
                    $maxAllowedweight = $serviceList[0]->getToWeight();
                }
                if ($toweightlimit > $fromweight) {
                    if ($toweightlimit <= $maxAllowedweight) {
                        $csv = "";
                        $cr = "\r\n";
                        while ($fromweight < $toweightlimit) {
                            if ($fromweight < 2)
                                $toweight = $fromweight + 0.25;
                            else
                                $toweight = $fromweight + 0.5;
                            $psr = new CustomizedServicesRoutingFilter();
                            $psr->addWeightRangeFilter($fromweight, $toweight);
                            $psr->addCountryFilter($country);
                            $psr->addFieldFilter("customize_service_id", $productId);
                            $psrList = $psr->getList();
                            if ($tempCountry == $country && $tempService == $productId)
                                $tTimeCheeck = false;
                            if (count($psrList) > 0) {

                                foreach ($psrList as $psr) {
                                    $csv .= $psr->getCountryIso() . ",";
                                    $csv .= $psr->getFromWeight() . ",";
                                    $csv .= $psr->getToWeight() . ",";
                                    $csv .= $psr->getStatus() . ",";
                                    $csv .= $psr->getCustomizeServiceId() . ",";
                                    $csv .= $psr->getServiceId() . ",";
                                    $csv .= $cr;
                                }

                                $psr = CustomizedServicesRouting::updateCustomizedService($country, $fromweight, $toweight, $productId, $serviceId, $tTimeCheeck);
                            } else {
                                $psr = CustomizedServicesRouting::insertCustomizedService($country, $fromweight, $toweight, $productId, $serviceId, $tTimeCheeck);
                            }
                            $tempCountry = $country;
                            $tempService = $productId;
                            if ($fromweight < 2)
                                $fromweight += 0.25;
                            else
                                $fromweight += 0.5;
                        }
                        $folder_path = "../_assets/routing_file/routing_log";

                        if (!file_exists($folder_path)) {
                            mkdir($folder_path, 0777, true);
                        }

                        $uniqueFileName = $routing_name . "_" . date("YmdHis") . "_" . $user->getUserAccount() . "_MANUAL";

                        $file_path = $folder_path . "/" . $uniqueFileName . ".csv";

                        $file_path = fopen($file_path, 'w');
                        $csvHeader = "country_iso, from_weight, to_weight, status, product_id, service_id";
                        fwrite($file_path, $csvHeader . $cr . $csv);

                        // close file
                        fclose($file_path);
                        $output['status'] = 'success';
                        $flmsg->success(formatMessages(SUCCESS_ROUTINE_ADDED));
                        $output['message'] = $flmsg->display(null, false);
                    } else {
                        $output['status'] = 'fail';
                        $flmsg->error(formatMessages(ERROR_SERVICE_WEIGHT) . $maxAllowedweight);
                        $output['message'] = $flmsg->display(null, false);
                    }
                } else {
                    $output['status'] = 'fail';
                    $flmsg->error(formatMessages(ERROR_FROM_WEIGHT));
                    $output['message'] = $flmsg->display(null, false);
                }
            } else {
                $output['status'] = 'fail';
                $flmsg->error(formatMessages(ERROR_REQUIRED_FILEDS_EMPTY));
                $output['message'] = $flmsg->display(null, false);
            }
            echo json_encode($output);
            exit;
        }
        //Delete Routing
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'delete_routing') {
            if (isset($this->form_vars['routing_id']) && $this->form_vars['routing_id'] > 0) {
                $routingId = $this->form_vars['routing_id'];
                $serviceId = $this->form_vars['service_id'];
                $countryId = $this->form_vars['country_id'];
                $cutomizedObj = new CustomizedServicesRouting();
                $cutomizedObj->deleteRoutine("id = " . $routingId);
                $serviceCountryTime = new ServiceCountryTime();
                $serviceCountryTime->deleteByServiceIdAndCountryId("id_service = " . $serviceId . " AND id_country = " . $countryId);
                echo "deleted";
                die;
            } else {
                echo "error";
                die;
            }
            echo "error";
            die;
        }
        //Get carrier services
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'getServices') {
            $carrier = $this->form_vars['carrier'];
            if ($carrier > 0) {
                $output .= Services::getServicesList($service_name, $carrier, '0');
            }
            echo $output;
            exit;
        }
//        Check weight limit
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'check_weight_limit') {
            $message = [];
            $from_weight = $this->form_vars['from_weight'];
            $to_weight = $this->form_vars['to_weight'];
            $weightFrom = $this->form_vars['agent_weight_from'];
            $weightTo = $this->form_vars['agent_weight_to'];
            $agentList = $this->form_vars['agent_select'];
            $serviceId = $this->form_vars['service_id'];

            parse_str($weightFrom, $serviceDataWeightFrom);
            parse_str($weightTo, $serviceDataWeightTo);
            parse_str($agentList, $serviceDataAgentList);
            $first_key = key($serviceDataAgentList['agent']);
            if (!empty($serviceDataAgentList['agent'][$first_key])) {
                $checkAgent = array();
                foreach ($serviceDataAgentList['agent'] as $key => $agentsList) {
                    if ($serviceDataWeightFrom['agent_from_weight'][$key] < $from_weight) {
                        $message[] = formatMessages(ERROR_FROM_WEIGHT) . "<strong>" . $from_weight . "</strong>";
                        $output["STATUS"] = 'ERROR';
                    }
                    if ($serviceDataWeightTo['agent_to_weight'][$key] > $to_weight) {
                        $message[] = formatMessages(ERROR_TO_WEIGHT) . "<strong>" . $to_weight . "</strong>";
                        $output["STATUS"] = 'ERROR';
                    }
                    $agentCount = 0;
                    if ($agentsList > 0) {
                        if ($serviceId > 0) {
                            $checkAgent[] = AgentDataFilter::checkServicesWeightLimit($agentsList, $serviceId, $serviceDataWeightFrom['agent_from_weight'][$key], $serviceDataWeightTo['agent_to_weight'][$key]);
                        }
                        $agentData = new ServiceAgentMappingDataFilter();
                        $agentData->addFilter(' agentid=' . $agentsList);
                        $agentData->addFilter(' serviceid =' . $serviceId);
                        $agentData->addFilter(' from_weight <=' . $serviceDataWeightFrom['agent_from_weight'][$key]);
                        $agentData->addFilter(' to_weight >=' . $serviceDataWeightTo['agent_to_weight'][$key]);
                        $agentCountData = $agentData->getList("count(id) 'id'");
                        if ($agentCountData[0]->getId() == 0) {
                            $agentData1 = new ServiceAgentMappingDataFilter();
                            $agentData1->addFilter(' agentid=' . $agentsList);
                            $agentData1->addFilter(' serviceid =' . $serviceId);
                            $agentWeightData = $agentData1->getList("from_weight, to_weight");

                            $agentObj = new AgentData($agentsList);
                            if (count($agentWeightData) > 0)
                                $message[] = "Agent <strong>" . $agentObj->getAgentName() . "</strong> allowed weight is between <strong>" . @$agentWeightData[0]->getFromWeight() . "</strong> to <strong>" . $agentWeightData[0]->getToWeight() . "</strong>";
                        }
                        $checkAgent[] = $agentCountData[0]->getId();
                    } else {
                        $message[] = "Please select agent";
                        $checkAgent[] = 0;
                    }
                }
            } else {
                $message[] = "Please select agent";
                $checkAgent[] = 0;
            }
            $output = array();
            if (!empty($checkAgent)) {
                if (in_array(0, $checkAgent)) {
                    $output["STATUS"] = 'ERROR';
                } else {
                    $output["STATUS"] = 'SUCCESS';
                }
            } else {
                $output["STATUS"] = 'ERROR';
            }
            $output["MESSAGE"] = implode("<br />", $message);
            echo json_encode($output);
            die;
        }
        //else //        Handle file upload for users documents user_id
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_service_doc') {
            $html = "";
            $serviceId = $this->form_vars["service_id"];
            $documentId = $this->form_vars["file_type"];
            $fileTypeText = $this->form_vars["file_type_text"];
            $agentId = $this->form_vars["agent_id"];
            $addedBy = $user->getId();
            $path = "../_assets/service_documents/" . $serviceId . "/";
            if (!file_exists($path))
                @mkdir($path, 0775);
            if (isset($_FILES["service_doc_file"]) && trim($_FILES["service_doc_file"]["name"]) != '') {
                $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
                $temp = explode(".", $_FILES["service_doc_file"]["name"]);
                $extension = end($temp);
                if ((($_FILES["service_doc_file"]["type"] == "image/gif") || ($_FILES["service_doc_file"]["type"] == "image/jpeg") || ($_FILES["service_doc_file"]["type"] == "image/jpg") || ($_FILES["service_doc_file"]["type"] == "image/pjpeg") || ($_FILES["service_doc_file"]["type"] == "image/x-png") || ($_FILES["service_doc_file"]["type"] == "image/png") || ($_FILES["service_doc_file"]["type"] == "application/pdf")) && in_array($extension, $allowedExts)) {
                    if ($_FILES["service_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["service_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["service_doc_file"]["name"]);
                        move_uploaded_file($_FILES["service_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        //Save User document Data
                        $serviceDocument = new serviceDocument();
                        $serviceDocument->setServiceId($serviceId);
                        $serviceDocument->setDocumentId($documentId);
                        $serviceDocument->setAgentId($agentId);
                        $serviceDocument->setDocumentName($uploadUserDoc);
                        $serviceDocument->setAddedBy($addedBy);
                        $serviceDocument->setAddedDate(date('d-m-Y'));

                        $agentDataDocument = new AgentData($agentId);
                        $serviceDocument->save();
                        $html .= '<div class="col-md-3" id="ser_doc_' . $serviceDocument->getId() . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" alt="100%x200" style="max-width: 100%; max-height: 200px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption">';
                        $html .= '<h3>' . $agentDataDocument->getAgentName() . '<br>' . $fileTypeText . '</h3>';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn red remove_doc" data-doc_id="' . $serviceDocument->getId() . '"> Remove </a>';
                        $html .= '</p>';
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
        } //        Handle remove User Document
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_service_doc') {
            $docId = $this->form_vars['doc_id'];
            $serviceId = $this->form_vars['service_id'];
            if ($serviceId > 0) {
                $serviceDocument = new serviceDocument($docId);
                @unlink("../_assets/service_documents/" . $serviceId . "/" . $serviceDocument->getDocumentName());
                $serviceDocument->deleteById($docId);
            }
            echo "1";
            die;
        } else
            // is this form being posted back?
            if (isset($this->form_vars["form_action"])) {
                // take appropriate action
                switch ($this->form_vars["form_action"]) {
                    // SAVE
                    // - save new address detailsvalidate new address details - if OK, save and return to booking list
                    case "save":
                        // Check if service code is already exist
                        $error_array = array();
                        if(intval($this->form_vars["id"]) < 0 || intval($this->form_vars["id"]) == ""){
                            $serviceCheck = Services::getServiceByCode($this->form_vars["code"]);
                            if(count($serviceCheck) > 0){
                                $error_array[] = "Service Code [".$this->form_vars["code"]."] already exist";
                            }

                        }
                        // save new address details
                        //SET traking falg
                        $tracking_flag = (isset($this->form_vars["tracking_flag"]) ? 1 : 0);
                        //SET friday only flag
                        $friday_only_flag = (isset($this->form_vars["friday_only_flag"]) ? 1 : 0);
                        $saturday_only_flag = (isset($this->form_vars["saturday_only_flag"]) ? 1 : 0);
                        $sunday_only_flag = (isset($this->form_vars["sunday_only_flag"]) ? 1 : 0);
                        $insurance_available = (isset($this->form_vars["insurance_available"]) ? 1 : 0);

                        //SET Pre alert, pre alert email & pre advise fields
                        $preAdvise = (isset($this->form_vars["pre_advise"]) ? "Y" : "N");
                        $preAlert = (isset($this->form_vars["pre_alert"]) ? "Y" : "N");
                        $validationType = (isset($this->form_vars["validation_type"]) ? "courier" : "mail");
                        $mailType = $this->form_vars["mail_type"];
                        $mailOption = $this->form_vars["mail_option"];
                        $zoneType = (isset($this->form_vars["zone_type"]) ? "country" : "postcode");
                        $tariffType = (isset($this->form_vars["tariff_type"]) ? "single" : "multi");
                        $preAlertEmail = $this->form_vars["pre_alert_email"];

                        //SET Parce
//                    $content_mandatory = (isset($this->form_vars["content_mandatory"]) ? 1 : 0);
                        //SET email telephone
                        $required_telephone = (isset($this->form_vars["required_telephone"]) ? 1 : 0);
                        //SET email required
                        $required_email = (isset($this->form_vars["required_email"]) ? 1 : 0);
                        //SET remotearea
                        $remotearea = (isset($this->form_vars["remotearea"]) ? 'ON_WEIGHT' : 'ON_PIECE');
                        //SET package_type
                        $package_type = (isset($this->form_vars["package_type"]) ? 1 : 2);
                        $is_commercials = (isset($this->form_vars["is_commercials"]) ? 'required' : 'not required');
                        $is_cn = (isset($this->form_vars["is_cn"]) ? 'required' : 'not required');
                        //SET is_untrack
                        $is_untrack = (isset($this->form_vars["is_untrack"]) ? 1 : 0);
                        $is_eori_required = (isset($this->form_vars["is_eori_required"]) ? 1 : 0);
                        //SET delivery_type
                        $delivery_type = 'all';
                        if($validationType == 'mail' && $is_untrack == 1) {
                            $delivery_type = $this->form_vars['delivery_type'];
                        }
                        //SET status
                        $status = (isset($this->form_vars["status"]) ? 1 : 0);

                        // SET Methods
                        $services = new Services(intval($this->form_vars["id"]));
                        // Check if already any service aganist selected carrier is pre sort then remove previous one and add latest one.
                        if (intval($this->form_vars["id"]) > 0) {
                            $serviceFilter = new ServiceFilter();
                            $serviceFilter->addFieldFilter("carrier_id", $this->form_vars["carrier"]);
                            $serviceFilter->addFieldFilter("pre_sort", "YES");
                            $serviceObj = $serviceFilter->getColumnList("id");
                            if (count($serviceObj) > 0) {
                                Services::DeleteByColumnName("carrier_id", $this->form_vars["carrier"]);
                            }
                        }
                        $newAuditData['service_countries'] = [];
                        $newAuditData['service_collection_countries'] = [];
                        $newAuditData['service_agent'] = [];
                        $oldAuditData['service_countries'] = [];
                        $oldAuditData['service_collection_countries'] = [];
                        $oldAuditData['service_agent'] = [];
                        $preSort = 'NO';
                        if (isset($this->form_vars["pre_sort"]))
                            $preSort = 'YES';
                        $services->setPreSort($preSort);
                        $oldServData = serialize($services);
                        $services->setName($this->form_vars["name"]);
                        $services->setCarrierId($this->form_vars["carrier"]);
                        $services->setType($this->form_vars["countryType"]);
                        $services->setCode($this->form_vars["code"]);
                        $services->setCarrierServiceCode($this->form_vars["carriercode"]);
                        $services->setCarrier($this->form_vars["carrier"]);
                        $services->setFromWeight($this->form_vars["from_weight"]);
                        $services->setToWeight($this->form_vars["to_weight"]);
                        $services->setOriginCountry($this->form_vars["origin_country"]);
                        $services->setDeliveryMode($this->form_vars["delivery_mode"]);
                        $services->setActive($status);
                        $services->setIsUntrack($is_untrack);
                        $services->setIsEoriRequired($is_eori_required);
                        $services->setDeliveryType($delivery_type);
                        $services->setCarrierAddressLimit($this->form_vars["carrier_address_limit"]);
                        $services->setLabelClassName($this->form_vars["label_class_name"]);
                        $services->setWieghtType($package_type);
                        $services->setIsCommercials($is_commercials);
                        $services->setIsCn($is_cn);
                        $services->setFuelSurchargeCost($this->form_vars["fuel_surcharge_cost"]);
                        $services->setLabelCharges($this->form_vars["label_charges"]);
                        $services->setProductOwner($this->form_vars["product_owner"]);
                        $maxlength = 0;
                        if (!empty($this->form_vars["maxlength"]))
                            $maxlength = $this->form_vars["maxlength"];
                        $services->setMaxLength($maxlength);
                        
                        $maxwidth = 0;
                        if($validationType == "mail"){
                            $maxwidth = $this->form_vars["maxthickness"];
                        }
                        else{
                        if (!empty($this->form_vars["maxwidth"]))
                            $maxwidth = $this->form_vars["maxwidth"];
                        }
                        $services->setMaxWidth($maxwidth);
                        
                        $maxheight = 0;
                        if (!empty($this->form_vars["maxheight"]))
                            $maxheight = $this->form_vars["maxheight"];
                        $services->setMaxHeight($maxheight);

                        $services->setMaxWeight($this->form_vars["maxweight"]);
                        $services->setMaxVolumetricWeight($this->form_vars["maxvolweight"]);
                        $services->setMinTotalWeight($this->form_vars["mintotalweight"]);
                        $services->setMaxTotalWeight($this->form_vars["maxtotalweight"]);
                        $services->setMaxTotalVolumetricWeight($this->form_vars["maxtotalvolweight"]);
                        $fuelsurcharge = 0;
                        if (!empty($this->form_vars["fuelsurcharge"]))
                            $fuelsurcharge = $this->form_vars["fuelsurcharge"];
                        $services->setFuelSurcharge($fuelsurcharge);
                        $services->setTrackingFlag($tracking_flag);
                        $services->setFridayOnlyFlag($friday_only_flag);
                        $services->setSaturdayOnlyFlag($saturday_only_flag);
                        $services->setSundayOnlyFlag($sunday_only_flag);
                        $services->setInsuranceAvailable($insurance_available);
                        $services->setMinimumCollectionWindow($this->form_vars["collection_window"]);
//                    $services->setContentDescMandatory($content_mandatory);
                        $services->setVolumetricDenominator($this->form_vars["volumetric_denominator"]);
                        $services->setVolWgtFormula($this->form_vars["vol_wgt_formula"]);
                        $services->setGirth($this->form_vars["girth"]);
                        $services->setGirthFormula($this->form_vars["girth_formula"]);
                        $maximum_allowed_dimension = 0;
                        if (!empty($this->form_vars["maximum_allowed_dimension"]))
                            $maximum_allowed_dimension = $this->form_vars["maximum_allowed_dimension"];
                        $services->setMaximumAllowedDimension($maximum_allowed_dimension);
                        $services->setMaximumDimFormula($this->form_vars["maximum_dim_formula"]);

                        //SET Pre alert, pre alert email & pre advise fields
                        $services->setPreAdvise($preAdvise);
                        $services->setPreAlert($preAlert);
                        $services->setpreAlertEmail($preAlertEmail);
                        $services->setProductServiceCode($this->form_vars["product_service_code"]);
                        $services->setLatestBookingTime($this->form_vars["last_booking"]);
                        $services->setClassCode($this->form_vars["class_code"]);
                        $services->setPriceCalculationType($this->form_vars["pricing_method"]);
                        $services->setUploadedCurrency($this->form_vars["uploaded_currency"]);
                        $services->setCollectionTimeGroupId($this->form_vars["collection_times"]);
                        $services->setServiceType($this->form_vars["service_type_provider"]);
                        $dropOffServiceId = "";
                        if (isset($this->form_vars["drop_off_service_id"]) && !empty($this->form_vars["drop_off_service_id"])) {
                            $dropOffServiceId = $this->form_vars["drop_off_service_id"];
                        }
                        $services->setDropOffServiceId($dropOffServiceId);
                        $services->setDescription($_POST["description"]);
                        $services->setCutOffTime($this->form_vars["cut_off_time"]);
                        $services->setAdditionalDetails($_POST["additional_details"]);
                        $services->setUploadedCurrencyValue($this->form_vars["uploaded_currency_value"]);
                        $services->setRegistrationFee($this->form_vars["registration_fee"]);
                        $services->setAditionalCharge($this->form_vars["aditional_charge"]);
                        $services->setGroupCharges($this->form_vars["group_charges"]);
                        $services->setRemotearea($remotearea);
                        $services->setRequiredEmail($required_email);
                        $services->setRequiredTelephone($required_telephone);
                        $services->setAgentid($this->form_vars["agentid"]);
                        $services->setDeletedq(0);
                        $services->setChangedBy($user->getId());
                        $services->setChangedOn(date("Y-m-d H:i:s"));
                        $allowOversize = 0;
                        if (!empty($this->form_vars["allow_oversize"]))
                            $allowOversize = $this->form_vars["allow_oversize"];
                        $services->setAllowOversize($allowOversize);
                        $services->setValidationType($validationType);
                        $services->setMailType($mailType);
                        $services->setMailOption($mailOption);
                        $services->setZoneType($zoneType);
                        $services->setTariffType($tariffType);

                        //Demo days set all these as it requires
                        $services->setShipmentType('PARCEL');
                        $services->setIsRemotearea('N');
                        $isCustomized = 0;
                        if (isset($this->form_vars['is_customized']))
                            $isCustomized = 1;
                        $services->setIsCustomized($isCustomized);

                        if ($this->form_vars["id"] > 0) {
                            //Delete from service_country_ttime
                            $resultArr = [];
                            $oldCountryArr = $this->form_vars["old_country"];
                            $serviceCountryArr = $this->form_vars["service_country"];
                            $resultArr = array_diff($oldCountryArr, $serviceCountryArr);
                            Services::DeleteByCountryId($resultArr, $this->form_vars["id"]);
//                        save services
                            $serviceCountries = [];
                            if (!isset($this->form_vars['is_customized'])) {
                                foreach ($this->form_vars["service_country"] as $serviceCountry) {
                                    if (Services::checkServiceExists($this->form_vars["id"], $serviceCountry) == 0) {
                                        $serviceCountries[] = $serviceCountry;
                                        $serviceCountryTime = New ServiceCountryTime();
                                        $serviceCountryTime->setIdCountry($serviceCountry);
                                        $serviceCountryTime->setIdService($this->form_vars["id"]);
                                        $serviceCountryTime->save();
                                    }
                                }
                                //TODO:// check country name why not coming
                                if (!empty($serviceCountries)) {
                                    foreach ($serviceCountries as $serviceCountry) {
                                        $country = new Country($serviceCountry);
                                        $countries[] = $country->getName();
                                    }
                                    if (!empty($countries)) {
                                        $newAuditData['service_countries'] = implode(',', $countries);
                                    }
                                }
                            }
                        }
                        else {
                            $services->setAddedBy($user->getId());
                            $services->setAddedOn(date("Y-m-d H:i:s"));
                        }
                        if (isset($_FILES["logo"]) && trim($_FILES["logo"]["name"]) != '') {
                            if (!empty(trim($this->form_vars["old_logo"]))) {
                                unlink("images/" . $this->form_vars["old_logo"]);
                            }
                            $allowedExts = array("gif", "jpeg", "jpg", "png");
                            $temp = explode(".", $_FILES["logo"]["name"]);
                            $extension = end($temp);
                            if ((($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                                if ($_FILES["logo"]["error"] > 0) {
                                    $error_array[] = "Return Code: " . $_FILES["logo"]["error"] . "<br>";
                                } else {
                                    $uploadName = time() . $_FILES["logo"]["name"];
                                    move_uploaded_file($_FILES["logo"]["tmp_name"], "images/" . $uploadName);
                                    $services->setLogoServices($uploadName);
                                }
                            } else {
                                $error_array[] = formatMessages(ERROR_INVALID_FILE);
                            }
                        }
                        if (isset($_FILES["uploadFile"]) && trim($_FILES["uploadFile"]["name"]) != '') {
                            $allowedExts = array("pdf");
                            $temp = explode(".", $_FILES["uploadFile"]["name"]);
                            $extension = end($temp);
                            if ((($_FILES["uploadFile"]["type"] == "application/pdf") || ($_FILES["uploadFile"]["type"] == "application/pdf") || ($_FILES["uploadFile"]["type"] == "application/pdf") || ($_FILES["uploadFile"]["type"] == "application/pdf") || ($_FILES["uploadFile"]["type"] == "application/pdf") || ($_FILES["uploadFile"]["type"] == "application/pdf")) && in_array($extension, $allowedExts)) {
                                if ($_FILES["uploadFile"]["error"] > 0) {
                                    $error_array[] = "Return Code: " . $_FILES["uploadFile"]["error"] . "<br>";
                                } else {
                                    $uploadName = $this->form_vars["code"] . "." . $extension;
                                    move_uploaded_file($_FILES["uploadFile"]["tmp_name"], "../_assets/service_sample_label/" . $uploadName);
                                }
                            } else {
                                $error_array[] = formatMessages(ERROR_INVALID_FILE);
                            }
                        }
                        if ($this->form_vars["id"] <= 0) {
                            $servicesFilter = new ServiceFilter();
                            $servicesFilter->addCodeTrimFilter(trim($this->form_vars["code"]));
                            $checkService = $servicesFilter->getCount();
                        } else
                            $checkService = 0;
                        // address valid? set VALID and return to booking list
                        if ($services->isValid($error_array) && $checkService <= 0) {
                            $services->saveLog = false;
                            $services->save();
                            $latestServiceId = $services->getId();
                            // Delete service collection country
                            if ($latestServiceId > 0) {
                                $serviceCollectionCounty = new ServiceCollectionCounty();
                                $oldServiceCollectionCountries = new ServiceCollectionCountyFilter();
                                $oldServiceCollectionCountries->addFilter(' service_id = ' . $latestServiceId);
                                $oldServiceCollectionCountries = $oldServiceCollectionCountries->getList('country_id');
                                $oldServiceCollectionCountriesArray = [];
                                foreach ($oldServiceCollectionCountries as $serviceCountry) {
                                    $serviceCountryObj = new Country($serviceCountry->getCountryId());
                                    $oldServiceCollectionCountriesArray[] = $serviceCountryObj->getName();
                                }
                                $oldAuditData['service_collection_countries'] = $oldServiceCollectionCountriesArray;
                                $serviceCollectionCounty->deleteByServiceId($latestServiceId);
                                //TODO:// check country name
                                if ($this->form_vars["service_type_provider"] != "D") {
                                    // Save service collection country if only type is collection
                                    $serviceCollectionCountyFilter = new ServiceCollectionCountyFilter();
                                    $serviceCollectionCountyFilter->saveBulkData($this->form_vars['service_collection_country'], $latestServiceId);
                                    $serviceCollectionCounties = $this->form_vars['service_collection_country'];
                                    $auditServiceCountries = [];
                                    foreach ($serviceCollectionCounties as $serviceCountry) {
                                        $auditServiceCountry = new Country($serviceCountry);
                                        $auditServiceCountries[] = $auditServiceCountry->getName();
                                    }
                                    $newAuditData['service_collection_countries'] = $auditServiceCountries;
                                }
                            }
                            // Save service collection country
                            $serviceCountries = [];
                            if (empty($this->form_vars["id"]) || $this->form_vars["id"] == '-1') {
                                if (!isset($this->form_vars['is_customized'])) {
                                    //save services
                                    foreach ($this->form_vars["service_country"] as $serviceCountry) {
                                        $serviceCountries[] = $serviceCountry;
                                        $serviceCountryTime = New ServiceCountryTime();
                                        $serviceCountryTime->setIdCountry($serviceCountry);
                                        $serviceCountryTime->setIdService($latestServiceId);
                                        $serviceCountryTime->save();
                                    }
                                    if (!empty($serviceCountries)) {
                                        foreach ($serviceCountries as $serviceCountry) {
                                            $countryObj = new Country($serviceCountry);
                                            $countries[] = $countryObj->getName();
                                        }
                                        if (!empty($countries)) {
                                            $newAuditData['service_countries'] = $countries;
                                        }
                                    }
                                }
                            }
                            //Create log
                            $servObjNew = new Services($this->form_vars["id"]);
                            $newServData = serialize($servObjNew);
                            $UserId = $user->getId();
                            $Ipaddress = $this->getClientIp();
                            $ServiceId = $this->form_vars["id"];
                            $LogType = "Service_Log";
                            if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0)
                                $message = $user->getUserName() . ' has creates service';
                            else
                                $message = $user->getUserName() . ' has updated service';
                            $PreviousData = $oldServData;
                            $CurrentData = $newServData;
                            $ServiceLogObj = new ServiceLog();
                            $ServiceLogObj->createlog($UserId, $Ipaddress, $ServiceId, $LogType, $message, $PreviousData, $CurrentData);
//                      add agent data
//                      Get lastest inserted id
                            $latestId = $services->getId();
                            if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                                $serivceAgentObj = new carrierServiceDefaultRulesFilter();
                                $serivceAgentObj->addFilter('serviceid = ' . $this->form_vars["id"]);
                                $serviceAgentsList = $serivceAgentObj->getList();
                                $oldAgentServices = [];
                                foreach ($serviceAgentsList as $serviceAgent) {
                                    $agentObj = new AgentData($serviceAgent->getAgentid());
                                    $oldAgentServices[] = 'agent_name: ' . $agentObj->getAgentName() . '<br />from_weight: ' . $serviceAgent->getFromWeight() . '<br />to_weight: ' . $serviceAgent->getToWeight() . '<br/><br/>';
                                }
                                $oldAuditData['service_agent'] = $oldAgentServices;
                                carrierServiceDefaultRules::deleteByServiceId($this->form_vars["id"]);
                            }
                            $agentAuditData = [];
                            if (count($this->form_vars['agent']) > 0) {
                                foreach ($this->form_vars['agent'] as $index => $value) {
                                    $agenId = $value;
                                    if ($agenId > 0) {
                                        $fromWeight = $this->form_vars['agent_from_weight'][$index];
                                        $toWeight = $this->form_vars['agent_to_weight'][$index];
                                        $carrierServiceDefaultRules = new carrierServiceDefaultRules();
                                        $carrierServiceDefaultRules->setIsDefault(1);
                                        $carrierServiceDefaultRules->setServiceid($latestId);
                                        $carrierServiceDefaultRules->setAgentid($agenId);
                                        $carrierServiceDefaultRules->setFromWeight($fromWeight);
                                        $carrierServiceDefaultRules->setToWeight($toWeight);
                                        $carrierServiceDefaultRules->setAgentType("outbound");
                                        $carrierServiceDefaultRules->save();
                                        $agentObj = new AgentData($agenId);
                                        $agentAuditData[] = 'agent_name: ' . $agentObj->getAgentName() . '<br />from_weight: ' . $fromWeight . '<br />to_weight: ' . $toWeight . '<br/><br/>';
                                    }
                                }
                                $newAuditData['service_agent'] =  $agentAuditData;
                            }
                            if (count($this->form_vars['dispatch_agent']) > 0) {
                                foreach ($this->form_vars['dispatch_agent'] as $index => $value) {
                                    $agenId = $value;
                                    if ($agenId > 0) {
                                        $carrierServiceDefaultRules = new carrierServiceDefaultRules();
                                        $fromWeight = $this->form_vars['dispatch_agent_from_weight'][$index];
                                        $toWeight = $this->form_vars['dispatch_agent_to_weight'][$index];
                                        $carrierServiceDefaultRules->setIsDefault(1);
                                        $carrierServiceDefaultRules->setServiceid($latestId);
                                        $carrierServiceDefaultRules->setAgentid($agenId);
                                        $carrierServiceDefaultRules->setFromWeight($fromWeight);
                                        $carrierServiceDefaultRules->setToWeight($toWeight);
                                        $carrierServiceDefaultRules->setAgentType("dispatch");
                                        $carrierServiceDefaultRules->save();
                                    }
                                }
                            }
                            $services->logMoreDataOld = $oldAuditData;
                            $services->logMoreDataNew = $newAuditData;
                            $services->saveAuditData();
//                            $userAudit->insertAuditData('serviceData', 'update', $this->sessionUser->getFirstName() . ' ' . $this->sessionUser->getLastName(), $this->sessionUser->getId(), $new_data, $table_key, $old_data, $this->form_vars['name'] . ' Service Data update by ' . $this->sessionUser->getFirstName() . ' ' . $this->sessionUser->getLastName());
                            if ($this->form_vars["id"] > 0)
                                util_redirect("../main/services_detail.php?id=" . $this->form_vars["id"] . "&msg=updated");
                            else
                                util_redirect("../main/services_detail.php?id=" . $services->getId() . "&msg=added");
                        } else {
                            //Create log
                            $servObjNew = new Services($this->form_vars["id"]);
                            $newServData = serialize($servObjNew);
                            $UserId = $user->getId();
                            $Ipaddress = $this->getClientIp();
                            $ServiceId = $this->form_vars["id"];
                            $LogType = "Service_Log";
                            $message = $user->getUserName() . ' cannot updated service record due to server issue';
                            $PreviousData = $oldServData;
                            $CurrentData = "";
                            $ServiceLogObj = new ServiceLog();
                            $ServiceLogObj->createlog($UserId, $Ipaddress, $ServiceId, $LogType, $message, $PreviousData, $CurrentData);
                        }
                        // add list of errors to error list
                        $error_list = ErrorList::getItem();
                        $error_list->addErrorList($error_array);
                        $this->error_list = $error_list;

                        break;
                    case "delete":

                        $services = new Services(intval($this->form_vars["id"]));
                        $oldServData = serialize($services);
                        $services->setDeletedq(1);
                        $services->save();
                        //Create log
                        $servObjNew = new Services($this->form_vars["id"]);
                        $newServData = serialize($servObjNew);
                        $UserId = $user->getId();
                        $Ipaddress = $this->getClientIp();
                        $ServiceId = $this->form_vars["id"];
                        $LogType = "Service_Log";
                        $message = $user->getUserName() . ' has deleted service record';
                        $PreviousData = $oldServData;
                        $CurrentData = "";
                        $ServiceLogObj = new ServiceLog();
                        $ServiceLogObj->createlog($UserId, $Ipaddress, $ServiceId, $LogType, $message, $PreviousData, $CurrentData);
                        util_redirect("../main/services_list.php");
                        break;
                    // CANCEL
                    // - return to booking list
                    case "cancel":
                    default:
                        util_redirect("../main/services_list.php");
                        break;
                }
                // not post back - first time this form is shown
            } else {
                // get consignment id passed
                $id = util_get_num("id");
                $this->form_vars["id"] = $id;
                // get address values
                $services = new Services($id);
                $this->form_vars["name"] = $services->getName();
                $this->form_vars["product_owner"] = $services->getProductOwner();
                $this->form_vars["code"] = $services->getCode();
                $this->form_vars["carriercode"] = $services->getCarrierServiceCode();
                $this->form_vars["carrier"] = $services->getCarrierId();
                $this->form_vars["countryType"] = $services->getType();
                $this->form_vars["service_country"] = $services->getServiceCountry();
                $this->form_vars["from_weight"] = $services->getFromWeight() > 0 ? $services->getFromWeight() : '';
                $this->form_vars["to_weight"] = $services->getToWeight() > 0 ? $services->getToWeight() : '';
                $this->form_vars["package_type"] = $services->getWieghtType();
                $this->form_vars["is_commercials"] = $services->getIsCommercials();
                $this->form_vars["is_cn"] = $services->getIsCn();
                $this->form_vars["origin_country"] = $services->getOriginCountry();
                $this->form_vars["delivery_mode"] = $services->getDeliveryMode();
                $this->form_vars["status"] = $services->getActive();
                $this->form_vars["is_untrack"] = $services->getIsUntrack();
                $this->form_vars["is_eori_required"] = $services->getIsEoriRequired();
                $this->form_vars["delivery_type"] = $services->getDeliveryType();
                $this->form_vars["fuel_surcharge_cost"] = $services->getFuelSurchargeCost() > 0 ? $services->getFuelSurchargeCost() : '';
                $this->form_vars["max_length"] = $services->getMaxLength() > 0 ? $services->getMaxLength() : '';
                $this->form_vars["max_width"] = $services->getMaxWidth() > 0 ? $services->getMaxWidth() : '';
                if(strtolower($services->getValidationType()) == "mail"){
                    $this->form_vars["max_thickness"] = $services->getMaxWidth() > 0 ? $services->getMaxWidth() : '';
                }
                $this->form_vars["max_height"] = $services->getMaxHeight() > 0 ? $services->getMaxHeight() : '';
                $this->form_vars["max_weight"] = $services->getMaxWeight() > 0 ? $services->getMaxWeight() : '';
                $this->form_vars["max_volumetric_weight"] = $services->getMaxVolumetricWeight() > 0 ? $services->getMaxVolumetricWeight() : '';
                $this->form_vars["min_total_weight"] = $services->getMinTotalWeight() > 0 ? $services->getMinTotalWeight() : '';
                $this->form_vars["max_total_weight"] = $services->getMaxTotalWeight() > 0 ? $services->getMaxTotalWeight() : '';
                $this->form_vars["max_total_volumetric_weight"] = $services->getMaxTotalVolumetricWeight() > 0 ? $services->getMaxTotalVolumetricWeight() : '';
                $this->form_vars["fuel_surcharge"] = $services->getFuelSurcharge() > 0 ? $services->getFuelSurcharge() : '0';
                $this->form_vars["label_charges"] = $services->getLabelCharges() > 0 ? $services->getLabelCharges() : '';


                $this->form_vars["tracking_flag"] = $services->getTrackingFlag();
                $this->form_vars["friday_only_flag"] = $services->getFridayOnlyFlag();
                $this->form_vars["saturday_only_flag"] = $services->getSaturdayOnlyFlag();
                $this->form_vars["sunday_only_flag"] = $services->getSundayOnlyFlag();
                $this->form_vars["insurance_available"] = $services->getInsuranceAvailable();
                $this->form_vars["minimum_collect_window"] = $services->getMinimumCollectionWindow();
                $this->form_vars["content_mandatory_flag"] = $services->getContentDescMandatory();
                $this->form_vars["volumetric_denominator"] = $services->getVolumetricDenominator() > 0 ? $services->getVolumetricDenominator() : '';
                $this->form_vars["vol_wgt_formula"] = $services->getVolWgtFormula();
                $this->form_vars["girth"] = $services->getGirth();
                $this->form_vars["girth_formula"] = $services->getGirthFormula();
                $this->form_vars["maximum_allowed_dimension"] = $services->getMaximumAllowedDimension();
                $this->form_vars["maximum_dim_formula"] = $services->getMaximumDimFormula();

                $this->form_vars["product_service_code"] = $services->getProductServiceCode();
                $this->form_vars["last_booking_time"] = $services->getLatestBookingTime();
                $this->form_vars["class_code"] = $services->getClassCode();
                $this->form_vars["collection_time_group_id"] = $services->getCollectionTimeGroupId();
                $this->form_vars["uploaded_currency"] = $services->getUploadedCurrency();
                $this->form_vars["service_type_provider"] = $services->getServiceType();
                $this->form_vars["drop_off_service_id"] = $services->getDropOffServiceId();
                $this->form_vars["description"] = $services->getDescription();
                $this->form_vars["cut_off_time"] = $services->getCutOffTime();
                $this->form_vars["logo"] = $services->getLogoServices();
                $this->form_vars["additional_details"] = $services->getAdditionalDetails();
                $this->form_vars["uploaded_currency_value"] = $services->getUploadedCurrencyValue() > 0 ? $services->getUploadedCurrencyValue() : '';
                $this->form_vars["registration_fee"] = $services->getRegistrationFee() > 0 ? $services->getRegistrationFee() : '';
                $this->form_vars["aditional_charge"] = $services->getAditionalCharge() > 0 ? $services->getAditionalCharge() : '';
                $this->form_vars["group_charges"] = $services->getGroupCharges();
                $this->form_vars["remotearea"] = $services->getRemotearea();
                $this->form_vars["carrier_address_limit"] = $services->getCarrierAddressLimit() > 0 ? $services->getCarrierAddressLimit() : '';
                $this->form_vars["label_class_name"] = $services->getLabelClassName();
                $this->form_vars["agentid"] = $services->getAgentid();
                $this->form_vars["required_email"] = $services->getRequiredEmail();
                $this->form_vars["required_telephone"] = $services->getRequiredTelephone();
                $this->form_vars["is_customized"] = $services->getIsCustomized();
                $this->isCustomized = $services->getIsCustomized();
                $this->form_vars["pre_sort"] = $services->getPreSort();

                //GET Pre alert, pre alert email & pre advise fields
                $this->form_vars["pre_advise"] = $services->getPreAdvise();
                $this->form_vars["pre_alert"] = $services->getPreAlert();
                $this->form_vars["pre_alert_email"] = $services->getPreAlertEmail();
                $this->form_vars["allow_oversize"] = $services->getAllowOversize();
                $this->form_vars["validation_type"] = $services->getValidationType();
                $this->form_vars["mail_type"] = $services->getMailType();
                $this->form_vars["mail_option"] = $services->getMailOption();
                $this->form_vars["zone_type"] = $services->getZoneType();
                $this->form_vars["tariff_type"] = $services->getTariffType();
            }
        // Get the collection time groups
        $cgf = new CollectionTimeGroupFilter();
        $this->collectionTimeGroups = $cgf->getList();
        $this->agentList = "";
        if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
            //Get Dispatch Agents
            $agentDataFilter = new AgentDataFilter();
            $agentDataObj = $agentDataFilter->getAgentData("dispatch");
            $agentArr = "";
            foreach ($agentDataObj as $agentDataArr) {
                $agentArr .= "'" . $agentDataArr->getId() . "',";
            }
            $agentArr = rtrim($agentArr, ',');
            $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
            $carrierServiceDefaultRulesFilter->addFilter("serviceid =" . intval($this->form_vars["id"]));
            $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound' ");
            if (!empty($agentArr))
                $carrierServiceDefaultRulesFilter->addFilter("agentid NOT IN (" . $agentArr . " )");
            $this->agentList = $carrierServiceDefaultRulesFilter->getList();
        }

        $this->agentDispatchList = "";
        if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
            //Get Dispatch Agents
            $agentDataFilter = new AgentDataFilter();
            $agentDataObj = $agentDataFilter->getDispatchAgent();
            $agentArr = "";
            foreach ($agentDataObj as $agentDataArr) {
                $agentArr .= "'" . $agentDataArr->getId() . "',";
            }
            $agentArr = rtrim($agentArr, ',');
            $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
            $carrierServiceDefaultRulesFilter->addFilter("serviceid =" . intval($this->form_vars["id"]));
            $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'dispatch' ");
            if (!empty($agentArr))
                $carrierServiceDefaultRulesFilter->addFilter("agentid IN (" . $agentArr . " )");
            $this->agentDispatchList = $carrierServiceDefaultRulesFilter->getList();
        }
        //Get data from service_country_ttime table
//        $this->selectedCountryIds
        $scttFilter = new ServiceCountryTimeFilter();
        $scttFilter->addFieldFilter("id_service", $this->form_vars["id"]);
        $this->selectedCountryIds = $scttFilter->getList(false);

        // common initialisation for this page
        $this->setTitle("Service");
        if ($serviceId > 0) {
            $this->validationType = "mail";
            $services = new Services($serviceId);
            $this->validationType = $services->getValidationType();
        }

        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'get_drop_off_services_ajax') {
            $serviceSubmittedType = $this->form_vars["service_type_id"];
            $serviceObj = new Services($this->serviceId);
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addFieldNotFilter("service_type", $serviceSubmittedType);
            $serviceFilter->addFieldNOtFilter("id", $this->serviceId);
//            $serviceFilter->addFilter("     drop_off_service_id IS NULL");
            $services = $serviceFilter->getList();
            $inHtml = "<option>Select Service</option>";
            $html = "";
            if (!empty($services)) {
                foreach ($services as $service) {
                    $selected = "";
                    if ($serviceObj->getDropOffServiceId() == $service->getId()) {
                        $selected = "selected='selected'";
                    }
                    $html .= "<option value='" . $service->getId() . "' " . $selected . " >" . $service->getName() . "</option>";
                }
            }
            if(empty($html) && $serviceObj->getDropOffServiceId() > 0){
                $doServiceObj = new Services($serviceObj->getDropOffServiceId());
                $selected = "selected='selected'";
                $html .= "<option value='" . $doServiceObj->getId() . "' " . $selected . " >" . $doServiceObj->getName() . "</option>";
            }
            echo $inHtml.$html;
            exit();
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead()
    {
        ?>
        <style>
            /*#mail_type_box {*/
            /*    display: none;*/
            /*}*/
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <!--<link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />-->
        <link href="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    { ?>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"
                type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-editors.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/ace.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/mode-html.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/theme-dreamweaver.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/jquery-ace.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script type="text/javascript">
            //        Handle is prepaid switch
            $('#is_customized').on('switchChange.bootstrapSwitch', function (event, state) {
                if (state) {
                    $('.customized_cls').hide();
                    $('.customized_cls1').show();
                    $('#customized_div').hide();
                    $("#routing_details_div").show();
                } else {
                    $('.customized_cls').show();
                    $('.customized_cls1').hide();
                    $("#routing_details_div").hide();
                    $('#customized_div').show();
                }
            });
            <?php if ($this->isCustomized == '1') { ?>
            $(".customized_cls1").show();
            $("div.customized_cls").css("display", "none");
            <?php } else { ?>
            $(".customized_cls1").hide();
            <?php } ?>
            $('#saveRoutine').click(function () {
                var country = $('#routing_country').val();
                var fromweight = $('#routing_from_weight').val();
                var toweight = $('#routing_to_weight').val();
                var service_name = $('#routing_service_name').val();
                var customize_service_id = <?php echo $this->serviceId; ?>;
                var form_data = new FormData();
                form_data.append('action', 'save_routine');
                form_data.append('country', country);
                form_data.append('fromweight', fromweight);
                form_data.append('toweight', toweight);
                form_data.append('service_name', service_name);
                form_data.append('customize_service_id', customize_service_id);
                $.ajax({
                    url: 'services_detail.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $("#successmsg").show();
                        $("#statusReponse").html(response.message);
                        grid.getDataTable().ajax.reload();
                    }
                });
            });
            $(document).on('change', '#routing_carrier', function () {
                var carrier = $('#routing_carrier').val();
                $.ajax({
                    url: "services_detail.php",
                    data: {action: 'getServices', carrier: carrier},
                    type: 'post',
                    success: function (response) {
                        $('#routing_service_name').empty();
                        $('#routing_service_name').append(response);
                        $('#routing_service_name').selectpicker('refresh');
                    }
                });

            });
            $(document).on('click', '.btndelete', function () {
                var routingId = $(this).data("routing_id");
                var serviceId = $(this).data("service_id");
                var countryId = $(this).data("country_id");
                swal({
                        title: "Are you sure you want to delete this routing details",
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
                                url: "services_detail.php",
                                data: {
                                    action: 'delete_routing',
                                    routing_id: routingId,
                                    service_id: serviceId,
                                    country_id: countryId
                                },
                                type: 'post',
                                success: function (response) {
                                    if (response == "deleted") {
                                        grid.getDataTable().ajax.reload();
                                        $("#res_message").addClass('alert-success').removeClass('alert-danger');
                                        $("#res_message").html("");
                                        $("#res_message").html("Record deleted successfully");
                                        $("#res_message_div").show();
                                    } else {

                                    }
                                }
                            });
                        }
                    });
            });
            $(".dispatch-repeater-add").click(function () {
                var index_of_agent = $(".dispatch_show_remove_btn").map(function () {
                    return $(this).data('index-of-agent');
                }).get();//get all data values in an array
                var highest_index_of_agent = Math.max.apply(Math, index_of_agent);//find the highest value from them
                highest_index_of_agent = parseInt(highest_index_of_agent) + 1;
                $(".dispatch_clone_div").children().clone().appendTo(".dispatch_append_here");
                $('.dispatch_append_here .row').last().attr('data-index-agent', highest_index_of_agent);
                $('.dispatch_append_here .row .dispatch_show_remove_btn').last().attr('data-index-of-agent', highest_index_of_agent);
                $('.dispatch_append_here .row .dispatch_show_remove_btn').show();
                setInputFeildsDispatch(highest_index_of_agent);
            });
            $(document).on('click', '.dispatch_show_remove_btn', function () {
                var current_index = $(this).data('index-of-agent');
                $('.dispatch_append_here [data-index-agent="' + current_index + '"]').remove();
            });

            function setInputFeildsDispatch(id) {
                $('.dispatch_append_here [data-index-agent="' + id + '"] .dispatch_agent_select').next().remove();
                $('.dispatch_append_here [data-index-agent="' + id + '"] :text').val("");
                $('.dispatch_append_here [data-index-agent="' + id + '"] .dispatch_agent_select').attr("name", 'agent[' + id + ']');
                $('.dispatch_append_here [data-index-agent="' + id + '"] :text').first().attr("name", 'dispatch_agent_from_weight[' + id + ']');
                $('.dispatch_append_here [data-index-agent="' + id + '"] :text').last().attr("name", 'dispatch_agent_to_weight[' + id + ']');
                var select2Parentid = $('.dispatch_append_here [data-index-agent="' + id + '"] .dispatch_agent_select').select2();
                select2Parentid.val("").trigger('change');
            }

            $(".repeater-add").click(function () {
                var index_of_agent = $(".show_remove_btn").map(function () {
                    return $(this).data('index-of-agent');
                }).get();//get all data values in an array
                var highest_index_of_agent = Math.max.apply(Math, index_of_agent);//find the highest value from them
                highest_index_of_agent = parseInt(highest_index_of_agent) + 1;
                $(".clone_div").children().clone().appendTo(".append_here");
                $('.append_here .row').last().attr('data-index-agent', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').last().attr('data-index-of-agent', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').show();
                setInputFeilds(highest_index_of_agent);
            });
            $(document).on('click', '.show_remove_btn', function () {
                var current_index = $(this).data('index-of-agent');
                $('.append_here [data-index-agent="' + current_index + '"]').remove();
            });

            function setInputFeilds(id) {
                $('.append_here [data-index-agent="' + id + '"] .agent_select').next().remove();
                $('.append_here [data-index-agent="' + id + '"] :text').val("");
                $('.append_here [data-index-agent="' + id + '"] .agent_select').attr("name", 'agent[' + id + ']');
                $('.append_here [data-index-agent="' + id + '"] :text').first().attr("name", 'agent_from_weight[' + id + ']');
                $('.append_here [data-index-agent="' + id + '"] :text').last().attr("name", 'agent_to_weight[' + id + ']');
                var select2Parentid = $('.append_here [data-index-agent="' + id + '"] .agent_select').select2();
                select2Parentid.val("").trigger('change');
            }

            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) {
                    if (unicode == 46) {
                    } else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }

            $(document).ready(function () {
                $('.htmlcode').ace({theme: 'dreamweaver', lang: 'html'});
                $("#btnDelete").click(function () {
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
                                $("#form_action").val("delete");
                                $("#services_frm").submit();
                                return true;
                            }
                        });
                    return false;

                });
                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();
                });
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                //Model log
                //        Handle User File upload
                $("#upload_file").click(function () {
                    var fileType = $("#document_name").val();
                    var agentId = $("#agent_doc_name").val();
                    var fileTypeText = $("#document_name option:selected").text();
                    if ($.trim(fileType) == "") {
                        swal("", "Please Select File Type", "info");
                        return false;
                    }
                    var filename = $("#file_name").val();
                    if (filename == "") {
                        swal("", "Please Select File", "info");
                        return false;
                    } else {
                        var file_data = $('#file_name').prop('files')[0];
                        var form_data = new FormData();
                        var serviceId = $("#id").val();
                        form_data.append('file_type', fileType);
                        form_data.append('file_name', filename);
                        form_data.append('service_id', serviceId);
                        form_data.append('file_type_text', fileTypeText);
                        form_data.append('action', "upload_service_doc");
                        form_data.append('agent_id', agentId);

                        form_data.append('service_doc_file', file_data);
                        $.ajax({
                            url: "services_detail.php", // point to server-side PHP script
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
                                    $("#append_service_doc").append(php_script_response);
                                    $("#document_name").val($("#document_name option:first").val());
                                    $("#document_name").selectpicker('refresh');
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
                                var serviceDocId = el.data("doc_id");
                                var serviceId = $("#id").val();
                                $.ajax({
                                    url: 'services_detail.php',
                                    type: 'POST',
                                    data: {action: 'remove_service_doc', doc_id: serviceDocId, service_id: serviceId},
                                    headers: {},
                                    success: function () {
                                        $("#ser_doc_" + serviceDocId).remove();
                                    },
                                    error: function (xhr, status, error) {

                                    }
                                });

                            }
                        });
                });
                $('#mail_type_box').hide();
                if ($("#validation_type").prop('checked') == false) {
                    $('#mail_type_box').show();
                }
            });
            //Form Wizart
            var FormWizard = function () {
                return {
                    init: function () {
                        function e(e) {
                            return e.id ? "<img class='flag' src='../../assets/global/img/flags/" + e.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + e.text : e.text
                        }

                        if (jQuery().bootstrapWizard) {
                            var r = $("#services_frm"),
                                t = $(".alert-danger", r),
                                i = $(".alert-success", r);
                            r.validate({
                                doNotHideMessage: !0,
                                errorElement: "span",
                                errorClass: "help-block help-block-error",
                                focusInvalid: !1,
                                rules: {},
                                messages: {},
                                errorPlacement: function (e, r) {
                                    //"gender" == r.attr("name") ? e.insertAfter("#form_gender_error") : "payment[]" == r.attr("name") ? e.insertAfter("#form_payment_error") : e.insertAfter(r)
                                },
                                invalidHandler: function (e, r) {
                                    i.hide(), t.show(), App.scrollTo(t, -800)
                                },
                                highlight: function (e) {
                                    $(e).closest(".form-group").removeClass("has-success").addClass("has-error")
                                },
                                unhighlight: function (e) {
                                    $(e).closest(".form-group").removeClass("has-error")
                                },
                                success: function (e) {
                                    "gender" == e.attr("for") || "payment[]" == e.attr("for") ? (e.closest(".form-group").removeClass("has-error").addClass("has-success"), e.remove()) : e.addClass("valid").closest(".form-group").removeClass("has-error").addClass("has-success")
                                },
                                submitHandler: function (e) {
                                    i.show(), t.hide(), e.submit();
                                }
                            });
                            var a = function () {
                                },
                                o = function (e, r, t) {
                                    var i = r.find("li").length,
                                        o = t + 1;
                                    $(".step-title", $("#form_wizard_1")).text("Step " + (t + 1) + " of " + i), jQuery("li", $("#form_wizard_1")).removeClass("done");
                                    for (var n = r.find("li"), s = 0; s < t; s++)
                                        jQuery(n[s]).addClass("done");
                                    1 == o ? $("#form_wizard_1").find(".button-previous").hide() : $("#form_wizard_1").find(".button-previous").show(), o >= i ? ($("#form_wizard_1").find(".button-next").hide(), $("#form_wizard_1").find(".button-submit").show(), a()) : ($("#form_wizard_1").find(".button-next").show(), $("#form_wizard_1").find(".button-submit").hide()), App.scrollTo($(".page-title"))
                                };
                            $("#form_wizard_1").bootstrapWizard({
                                nextSelector: ".button-next",
                                previousSelector: ".button-previous",
                                onTabClick: function (tab, navigation, index, clickedIndex) {
                                    o(tab, navigation, clickedIndex);
                                },
                                onNext: function (e, a, n) {
                                    return i.hide(), t.hide(), 0 != r.valid() && void o(e, a, n)
                                },
                                onPrevious: function (e, r, a) {
                                    i.hide(), t.hide(), o(e, r, a)
                                },
                                onTabShow: function (e, r, t) {
                                    var i = r.find("li").length,
                                        a = t + 1,
                                        o = a / i * 100;
                                    $("#form_wizard_1").find(".progress-bar").css({
                                        width: o + "%"
                                    })
                                }
                            }),
                                $("#form_wizard_1").find(".button-previous").hide(),
                                $("#form_wizard_1 .button-submit").click(function () {
                                    $("#form_action").val("save");
                                    <?php if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0 && $this->isCustomized != '1') { ?>
                                    /*
                                     * Check Weight limit
                                     */
                                    var from_weight = $("#from_weight").val();
                                    if (from_weight == "") {
                                        from_weight = 0;
                                    }
                                    var to_weight = $("#to_weight").val();
                                    var weightFrom = $('.agent_from_weight').serialize();
                                    var weightTo = $('.agent_to_weight').serialize();
                                    var agentSelect = $('.agent_select').serialize();
                                    var product_owner = $('.product_owner').val();
                                    var service_id = $("#id").val();
                                    $.ajax({
                                        url: 'services_detail.php',
                                        type: 'POST',
                                        dataType: "json",
                                        data: {
                                            action: 'check_weight_limit',
                                            from_weight: from_weight,
                                            to_weight: to_weight,
                                            agent_weight_from: weightFrom,
                                            agent_weight_to: weightTo,
                                            agent_select: agentSelect,
                                            service_id: service_id,
                                            product_owner: product_owner
                                        },
                                        headers: {},
                                        success: function (data) {
                                            if (data.STATUS == 'ERROR') {
                                                $("#res_message").addClass('alert-danger').removeClass('alert-success');
                                                $("#res_message").html("");
                                                $("#res_message").html(data.MESSAGE);
                                                $("#res_message_div").show();
                                                return false;
                                            } else {
                                                r.submit();
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            $("#res_message").addClass('alert-danger').removeClass('alert-success');
                                            $("#res_message").html("");
                                            $("#res_message").html("Please correct the weights according to the limits");
                                            $("#res_message_div").show();
                                        }
                                    });
                                    <?php } else { ?>
                                    r.submit();
                                    <?php } ?>
                                }).hide()
                        }
                    }
                }
            }();
            jQuery(document).ready(function () {
                FormWizard.init();
                $('#service_collection_country').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function (ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function (e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function (e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });
                $('#service_country').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function (ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function (e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function (e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });
                $("#countryType").change(function () {
                    var countryType = $(this).val();
                    var serviceId = $("#id").val();
                    $.ajax({
                        type: "POST",
                        url: "ajaxService.php",
                        data: {serviceType: countryType, action: "SELECTEDCOUNTRIES", service_id: serviceId},
                        success: function (data) {
                            $("#service_country").html(data);
                            $('#service_country').multiSelect("refresh");
                        }
                    });
                });
                $("#countryType").trigger('change');
                $('#service_country').change(function () {
                    //        changeCountryList();
                });
            });
            //Make input type hidden for unseleted countries for current service
            //function changeCountryList() {
            //    $("#append_unselected_country").html("");
            //    var unSelectedCountry = "";
            //    $('#service_country').find('option').not(':selected').each(function (k, v) {
            ////                    console.log(k,v.text, v.value);
            //        unSelectedCountry += '<input type="hidden" name="unslected_country[]" value="' + v.value + '"   />';
            //    });
            //    $("#append_unselected_country").append(unSelectedCountry);
            //}
            var handleTitle = function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }
                Metronic.scrollTo($('.page-title'));
            }
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
                                "url": "services_detail.php?action=routing_ajax&id=<?php echo $this->serviceId; ?>", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "service"},
                                {"data": "country"},
                                {"data": "from_weight"},
                                {"data": "to_weight"}
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
                $("#pre_alert").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $("#pre_alert_email_div").show();
                    } else {
                        $("#pre_alert_email").val("");
                        $("#pre_alert_email_div").hide();
                    }
                });
                // check if courier or mail service add validation
                var validationType = "<?php echo $this->validationType; ?>";
                if (validationType == 'courier') {
                    $("#mail_service_dim_div").hide();
                    $("#courier_service_div").show();
                } else {
                    $("#mail_service_dim_div").show();
                    $("#courier_service_div").hide();
                }
                getCollectionService();
                $("#service_type_provider").change(function () {
                    var service_type_provider = $("#service_type_provider").val();
                    var html = "<option>select Service</option>";
                    $("#drop_off_service_id").html(html);
                    // $('#drop_off_service_id').attr('disabled', 'disabled');
                    $("#collection_service_div").hide();
                    if (service_type_provider == "C") {
                        $("#collection_service_div").show();
                    }
                    $.ajax({
                        type: "POST",
                        url: "services_detail.php?id=<?php echo $this->serviceId; ?>",
                        data: {action: "get_drop_off_services_ajax", service_type_id: service_type_provider},
                        dataType: "html",
                        success: function (data) {
                            $("#drop_off_service_id").html(data);
                            $('#drop_off_service_id').removeAttr('disabled');
                        }
                    });
                });
                $("#service_type_provider").trigger('change');
            });
            $(document).on('click', '.repeater-delete', function () {
                $(this).parent().parent(".row").remove();
            });
            // Reload data table command
            //                grid.getDataTable().ajax.reload();
            $('#validation_type').on('switchChange.bootstrapSwitch', function (event, state) {
                if (state == true) {
                    $("#mail_service_dim_div").hide();
                    $("#courier_service_div").show();
                    $("#mail_type_box").hide();
                    $("#track_untrack_box").hide();
                    $('#delivery_type_box').hide();
                    $("#maxlength").val("");
                    $("#maxwidth").val("");
                    $("#maxheight").val("");
                    $("#maxvolweight").val("");
                    $("#volumetric_denominator").val("");
                    $("#vol_wgt_formula").val("");
                    $("#girth").val("");
                    var select2Drop = $("#girth_formula").select2();
                    select2Drop.val($("#girth_formula option:first").val()).trigger('change');
                } else {
                    $("#mail_service_dim_div").show();
                    $("#courier_service_div").hide();
                    $("#mail_type_box").show();
                    $("#track_untrack_box").show();
                    if ($('#is_untrack').is(':checked')) {
                        $('#delivery_type_box').show();
                    }
                    $("#maximum_allowed_dimension").val("");
                    var select2Selecter = $("#maximum_dim_formula").select2();
                    select2Selecter.val($("#maximum_dim_formula option:first").val()).trigger('change');
                }
            });

            $('#is_untrack').on('switchChange.bootstrapSwitch', function (event, state) {
                if (state == true) {
                    $('#delivery_type_box').show();
                } else {
                    $('#delivery_type_box').hide();
                }
            });

            function getCollectionService() {
                var serviceId = $("#id").val();
                $.ajax({
                    type: "POST",
                    url: "services_detail.php",
                    data: {action: "service_collection_country_ajax", service_id: serviceId},
                    success: function (data) {
                        $("#service_collection_country").html(data);
                        $('#service_collection_country').multiSelect("refresh");
                    }
                });
            }
        </script>
        <!--End Hadi Code-->
        <?php
    }

    protected function renderBody()
    {
        // Automated booking options configured here
        $automatedBookingArray = array(
            "None" => "",
            "DHL Day Definite" => "dhlexp",
            "DHL Time Definite" => "dhl"
        );
        $error_array = (array)$this->error_list;
        $missing_list = array_shift($error_array);
        $error_message = array_shift($error_array);
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="portlet light bordered" id="form_wizard_1">
            <div class="portlet-title">
                <div class="caption"><i class="icon-users"></i>
                    <?php
                    if (!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == "" || $_GET['id'] < 0)))
                        echo "Add New Services ";
                    else
                        echo "Edit Services ";
                    ?>
                </div>
                <div class="actions"><a id="btnExtraService" href="services_list.php" class="btn blue"
                                        style="<?php echo $customerHideStyle; ?>"><span></span><i
                                class="fa fa-list"></i>&nbsp;Services list</a> <a id="btnExtraService"
                                                                                  href="carrier.php" class="btn blue"
                                                                                  style="<?php echo $customerHideStyle; ?>"><span></span><i
                                class="fa fa-list"></i>&nbsp;Career List</a></div>
            </div>
            <div class="portlet-body form">
                <form name="adminForm" id="services_frm" action="" method="POST" enctype="multipart/form-data">
                    <!--Tab navigation-->
                    <div class="form-wizard">
                        <div class="form-body">
                            <div class="row" id="res_message_div" style="display: none;">
                                <div class="col-md-12">
                                    <div id="res_message" class="alert alert-success"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    $success_message = '';
                                    if (isset($_GET['msg'])) {
                                        if ($_GET['msg'] == 'added') {
                                            $success_message = Translation::GetCaption('RECORD_ADDED_SUCCESSFULLY');
                                        } else if ($_GET['msg'] == 'updated') {
                                            $success_message = Translation::GetCaption('RECORD_UPDATED_SUCCESSFULLY');
                                        }
                                    }
                                    ?>
                                    <div class="alert alert-success <?php if (empty($success_message)) { ?> display-none <?php } ?>">
                                        <?php echo $success_message; ?>
                                    </div>
                                    <div class="alert alert-danger <?php if (empty($error_message)) { ?> display-none <?php } ?>"
                                         id="res_message">
                                        <?php echo "<span>" . $error_message . " </span><br/>"; ?>
                                        <?php
                                        foreach ($missing_list as $value) {
                                            echo "<span>" . $value . "</span><br/>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php $tabIndex = 1; ?>
                            <ul class="nav nav-pills nav-justified steps">
                                <li class="active"><a data-toggle="tab" href="#service-detail" id="service-detail-tab"
                                                      class="step">
                                        <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                        <span class="desc">
                                            <i class="fa fa-check"></i> <?php echo Translation::GetCaption("SERVICE_DETAILS"); ?> </span>
                                    </a>
                                </li>
                                <?php if (@$id > 0 && $this->isCustomized == '1') { ?>
                                    <?php //if (@$id > 0) {  ?>
                                    <li><a class="step" data-toggle="tab" href="#routing-detail"
                                           id="routing-detail-tab">
                                            <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                            <span class="desc">
                                                <i class="fa fa-check"></i> <?php echo Translation::GetCaption("ROUTING_DETAILS"); ?> </span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li><a class="step" data-toggle="tab" href="#charges-tab" id="charges-tab-tab">
                                        <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                        <span class="desc">
                                            <i class="fa fa-check"></i> <?php echo Translation::GetCaption("CHARGES_DETAILS"); ?> </span>
                                    </a></li>
                                <li><a class="step" data-toggle="tab" href="#limitation-tab" id="limitation-tab-tab">
                                        <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                        <span class="desc">
                                            <i class="fa fa-check"></i> <?php echo Translation::GetCaption("LIMITATION_DETAILS"); ?> </span>
                                    </a></li>
                                <li><a class="step" data-toggle="tab" href="#extra-tab" id="extra-tab-tab">
                                        <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                        <span class="desc">
                                            <i class="fa fa-check"></i> <?php echo Translation::GetCaption("EXTRA_DETAILS"); ?> </span>
                                    </a></li>
                                <?php if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0 && $this->isCustomized != '1') { ?>
                                    <li>
                                        <a class="step" data-toggle="tab" href="#agent-tab" id="agent-tab-tab">
                                            <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                            <span class="desc">
                                                <i class="fa fa-check"></i> <?php echo Translation::GetCaption("SERVICE_AGENTS"); ?> </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="step" data-toggle="tab" href="#dispatch-agent-tab"
                                           id="dispatch-tab-tab">
                                            <span class="number"> <?php echo $tabIndex++; ?> </span><br/>
                                            <span class="desc">
                                                <i class="fa fa-check"></i> Dispatch Agents </span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                            <div id="bar" class="progress progress-striped" role="progressbar">
                                <div class="progress-bar progress-bar-primary"></div>
                            </div>
                            <!--End Tab navigation-->
                            <div class="tab-content">
                                <!--service-detail tab-->
                                <div id="service-detail" class="tab-pane fade in active">
                                    <div class="caption margin-bottom-10 block"><span
                                                class="caption-subject bold uppercase">Services Details</span></div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Is Product</label>
                                                <div class="md-radio-inline">
                                                    <input <?php echo($is_customized == '1' ? 'checked="checked"' : ''); ?>
                                                            name="is_customized" id="is_customized" type="checkbox"
                                                            class="make-switch" data-on-text="Product"
                                                            data-off-text="Service" data-on-color="primary"
                                                            data-size="mini">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Carrier</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> 
                                                        <i class="fa fa-bars"></i></span>
                                                    <?php
                                                    // Ask for DDL
                                                    echo Ddl::generateCarrierDDLWithImage('carrier', $carrier, 'id', ' class="bs-select input-sm form-control" data-live-search="true" data-show-subtext="true"');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <!-- <label>Service Code</label> -->
                                            <div class="form-group">
                                                <label>Service Code</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-key"></i></span>
                                                    <input type="text" class="form-control" name="code" id="code"
                                                           value="<?php echo @$code; ?>" size="" maxlength="35"
                                                           rel="tooltip" data-original-title="Service Code"
                                                           placeholder="Service Code"
                                                           onblur="$('#product_service_code').val(this.value);"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Service Name</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-server"></i></span>
                                                    <input type="text" class="form-control" name="name" id="name"
                                                           value="<?php echo @$name; ?>" size="100" maxlength="100"
                                                           placeholder="Service Name"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Origin Country</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                                <?php
                                                if (empty($origin_country))
                                                    $origin_country = "225";
                                                echo Ddl::generateCountryDDL('origin_country', $origin_country, 'id', ' class="bs-select input-sm form-control" data-live-search="true" data-show-subtext="true"');
                                                ?>
                                            </div>
                                        </div>
                                        <br clear="all">
                                        <div class="col-sm-3 customized_cls">
                                            <div class="form-group">
                                                <div class="form-group ">
                                                    <label>Region</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"> <i
                                                                    class="fa fa-globe"></i></span>
                                                        <?php
                                                        $countryTypeArr = array('ALL' => 'ALL', 'DBP' => 'United Kingdom', 'R1' => 'Europe', 'INT' => 'International');
                                                        echo Ddl::generateArrayDDL('countryType', $countryTypeArr, $countryType, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Origin Country" placeholder="Country Allowed"');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label>Service Type</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                                    <?php
                                                    $serviceTypeProvides = array('D' => 'Dispatched', 'C' => 'Collection', 'DO' => 'Drop-Off', 'R' => 'Return');
                                                    echo Ddl::generateArrayDDL('service_type_provider', $serviceTypeProvides, $service_type_provider, 'Service Type', ' class="form-control select2 select" rel="tooltip" data-original-title="Origin Country" placeholder="Service Type" ');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 customized_cls1">
                                            <div class="form-group">
                                                <label>Product Owner</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                                    <?php
                                                    $allowedLevel = 0;
                                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                                        $allowedLevel = 1;
                                                    }
                                                    echo Ddl::showTreeDropdown('product_owner', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $product_owner, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label>Drop Off Service</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                                    <select name="drop_off_service_id" class="select2"
                                                            id="drop_off_service_id" disabled="disabled">
                                                        <option value="">Select Service</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label>Delivery Mode</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                                    <?php
                                                    $serviceDeliveryModes = array('1' => 'Door to Door', '2' => 'Parcel Shops', '3' => 'Door to Door Delivery (POD)', '4' => 'Drop Off', '5' => 'Collection from address', '6' => 'Business', '7' => 'Return');

                                                    echo Ddl::generateArrayDDL('delivery_mode', $serviceDeliveryModes, $delivery_mode, 'Select Delivery Mode', ' class="form-control select2 select" rel="tooltip" data-original-title="Delivery Mode" placeholder="Delivery Mode"');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Status</label>
                                            <div class="md-radio-inline">
                                                <input <?php echo($status == '1' ? 'checked="checked"' : ''); ?>
                                                        name="status" type="checkbox" class="make-switch"
                                                        data-on-text="Yes" data-off-text="No" data-on-color="primary"
                                                        data-size="mini">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Carrier Code</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-key"></i></span>
                                                    <input type="text" class="form-control" name="carriercode" id="carriercode"
                                                           value="<?php echo @$carriercode; ?>" size="" maxlength="35"
                                                           rel="tooltip" data-original-title="Carrier Code"
                                                           placeholder="Carrier Code"/>
                                                </div>
                                            </div>
                                        </div>
                                        <br clear="all">
                                        <div id="collection_service_div" class="col-md-12 service-country-container"
                                             style="display: <?php echo($service_type_provider != "D" ? "block;" : "none;") ?> margin-bottom: 15px;">
                                            <label>Service Collection Country</label>
                                            <select name="service_collection_country[]" id="service_collection_country"
                                                    class="multi-select multiselect_drop_down" multiple="multiple"
                                                    rel="tooltip" data-original-title="Service Collection Country">
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 service-country-container customized_cls">
                                            <label>Service Country</label>
                                            <select name="service_country[]" id="service_country"
                                                    class="multi-select multi
                                                    select_drop_down" multiple="multiple"
                                                    rel="tooltip" data-original-title="Service Country">
                                            </select>
                                            <?php
                                            if (count($this->selectedCountryIds) > 0) {
                                                foreach ($this->selectedCountryIds as $countryIds) {
                                                    ?>
                                                    <input type="hidden" name="old_country[]"
                                                           value="<?php echo $countryIds->getIdCountry(); ?>"/>
                                                <?php }
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-12 service-country-container">
                                            <br clear="all">
                                            <a type="button" class="btn btn-primary" data-toggle="modal"
                                               data-target="#more-info">
                                                More Info
                                            </a>
                                        </div>


                                        <!-- Button trigger modal -->


                                        <!-- Modal -->
                                        <div class="modal fade" id="more-info" tabindex="-1" role="dialog"
                                             aria-labelledby="myModalLabel">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close"><span
                                                                    aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">YODEL 24 Detail</h4>
                                                    </div>
                                                    <div class="modal-body">


                                                        <div class="container1">


                                                            <div class="row1 margin-bottom-10">
                                                                <div class="col-md-4">
                                                                    <div class="inset-details">
                                                                        <img ng-src="https://www.yodeldirect.co.uk/logo/service/601.large"
                                                                             alt="Yodel" class="hidden-xs"
                                                                             src="https://www.yodeldirect.co.uk/logo/service/601.large">

                                                                    </div>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h2 class="palette-primary ng-binding">YODEL 24</h2>
                                                                    <!-- ngIf: quote.Service.IsDropOff -->
                                                                    <div>
                                                                        <!-- ngIf: workingDays == 1 --><span
                                                                                ng-if="workingDays == 1"
                                                                                class="ng-scope">
                                                                            Delivery within <span
                                                                                    class="palette-primary">1 working day</span>
                                                                        </span><!-- end ngIf: workingDays == 1 -->
                                                                        <!-- ngIf: workingDays != 1 -->
                                                                    </div>
                                                                </div>

                                                                <br clear="all">
                                                            </div>


                                                            <div class="row1">
                                                                <div>

                                                                    <!-- Nav tabs -->
                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li role="presentation" class="active"><a
                                                                                    href="#home" aria-controls="home"
                                                                                    role="tab" data-toggle="tab">More
                                                                                Info</a></li>
                                                                        <li role="presentation"><a href="#profile"
                                                                                                   aria-controls="profile"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Printer</a>
                                                                        </li>
                                                                        <li role="presentation"><a href="#messages"
                                                                                                   aria-controls="messages"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Collection</a>
                                                                        </li>
                                                                        <!-- <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li> -->
                                                                    </ul>

                                                                    <!-- Tab panes -->
                                                                    <div class="tab-content">
                                                                        <div role="tabpanel" class="tab-pane active"
                                                                             id="home">


                                                                            <div class="row1">
                                                                                <div class="col-sm-6">
                                                                                    <h3 class="text-primary"><strong>Service
                                                                                            Details</strong></h3>
                                                                                    <span ng-bind-html="quote.Service.Description | html"
                                                                                          class="ng-binding">

                                                                                        Parcel delivery within 1 working day throughout the most of UK's mainland. Collections and deliveries are not guaranteed but enjoy over 98% success. Collections are between 8am – 6pm.
                                                                                        <br>
                                                                                        <br>
                                                                                        <b>When your order has been completed, we strongly recommend that you print off two labels and place one inside the parcel itself. <b></b><br><br><strong>Parcels must be packaged in either a cardboard box, with 6 flat sides, a bag or a jiffy bag. Any other form of packaging might incur a cost.</strong></b></span>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <h3 class="text-primary"><strong>Key
                                                                                            Features</strong></h3>
                                                                                    <ul class="fa-ul margin-none">
                                                                                        <li ng-show="quote.Service.TimedDeliveryTime"
                                                                                            class="ng-hide"><i
                                                                                                    class="fa-li fa fa-check palette-primary"></i>
                                                                                            Delivery by <strong
                                                                                                    class="ng-binding"></strong>
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.IsDropOff"
                                                                                            class="ng-hide"><i
                                                                                                    class="fa-li fa fa-check palette-primary"></i>
                                                                                            Drop Off service
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.ExtendedBaseCoverAvailable"
                                                                                            class="ng-binding ng-hide">
                                                                                            <i class="fa-li fa fa-check palette-primary"></i>
                                                                                            £0.00 inclusive cover
                                                                                        </li>

                                                                                        <li>
                                                                                            <i class="fa-li fa fa-check palette-primary"></i>
                                                                                            Fully tracked service
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.DeliveryAlert"
                                                                                            class="ng-hide"><i
                                                                                                    class="fa-li fa fa-check palette-primary"></i>
                                                                                            SMS alert optional
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.Signature"
                                                                                            class="ng-hide"><i
                                                                                                    class="fa-li fa fa-check palette-primary"></i>
                                                                                            Signature optional
                                                                                        </li>
                                                                                        <li class="ng-binding"><i
                                                                                                    class="fa-li fa fa-check palette-primary"></i>
                                                                                            Protect your parcel up to
                                                                                            the value of £2,500.00
                                                                                        </li>
                                                                                    </ul>

                                                                                    <h3 class="text-primary"><strong>Restrictions</strong>
                                                                                    </h3>
                                                                                    <ul class="fa-ul margin-none">
                                                                                        <!-- ngIf: !quote.Service.MoreInfo -->
                                                                                        <li ng-if="!quote.Service.MoreInfo"
                                                                                            class="ng-scope"><i
                                                                                                    class="fa-li fa fa-ban palette-primary"></i>
                                                                                            <a target="_blank"
                                                                                               href="/prohibited-items">Check
                                                                                                the prohibited items
                                                                                                list</a></li>
                                                                                        <!-- end ngIf: !quote.Service.MoreInfo -->
                                                                                        <li class="ng-binding"><i
                                                                                                    class="fa-li fa fa-balance-scale palette-primary"></i>
                                                                                            Maximum Weight 25 kg
                                                                                        </li>
                                                                                        <li class="ng-binding"><i
                                                                                                    class="fa-li fa fa-expand palette-primary"></i>
                                                                                            Maximum Length 1.2 m
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.MaximumQuantity"
                                                                                            class="ng-binding ng-hide">
                                                                                            <i class="fa-li fa fa-expand palette-primary"></i>A
                                                                                            maximum of parcels per order
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.MaxHeight"
                                                                                            class="ng-binding ng-hide">
                                                                                            <i class="fa-li fa fa-arrows-v palette-primary"></i>
                                                                                            Maximum Height m
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.MaxWidth"
                                                                                            class="ng-binding ng-hide">
                                                                                            <i class="fa-li fa fa-arrows-h palette-primary"></i>
                                                                                            Maximum Width m
                                                                                        </li>
                                                                                        <li ng-show="quote.Service.MaxGirth"
                                                                                            class="ng-binding ng-hide">
                                                                                            <i class="fa-li fa fa-arrows-alt palette-primary"></i>
                                                                                            Maximum Length+Girth m
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>


                                                                        </div>
                                                                        <div role="tabpanel" class="tab-pane"
                                                                             id="profile">


                                                                            <h3 class="text-primary">
                                                                                <strong>Printer</strong></h3>
                                                                            With this service you will need to print
                                                                            your label, so having access to a printer is
                                                                            essential. An A4 printer will suffice for
                                                                            all label printing.
                                                                            Alternatively on the order confirmation page
                                                                            you can choose on of the following options
                                                                            for your printing needs:

                                                                            4x6 label printer
                                                                            A4 printer (4x 4x6 labels)
                                                                            Address labels (A4 sheet)

                                                                        </div>
                                                                        <div role="tabpanel" class="tab-pane"
                                                                             id="messages">

                                                                            <h3 class="text-primary"><strong>Collection
                                                                                    service</strong></h3>


                                                                            A collection service is a method of delivery
                                                                            whereby a courier comes to a specified
                                                                            location to pick up a parcel - or multiple
                                                                            parcels - before taking them back to the
                                                                            depot to be sorted and sent to the recipient

                                                                            <hr/>


                                                                            <div class="col-sm-24 col-sm-offset-0 col-xs-22 col-xs-offset-1">
                                                                                <div class="quote-info-item">
                                                                                    <i class="fa fa-clock-o fa-fw fa-2x"></i>
                                                                                    <!-- ngIf: workingDays == 1 --><span
                                                                                            ng-if="workingDays == 1"
                                                                                            class="ng-scope">
                                                                                        Delivery within <span
                                                                                                class="palette-primary">1 working day</span>
                                                                                    </span>
                                                                                    <!-- end ngIf: workingDays == 1 -->
                                                                                    <!-- ngIf: workingDays != 1 -->
                                                                                </div>

                                                                                <div class="quote-info-item ng-binding">
                                                                                    <i class="fa fa-truck fa-fw fa-2x"></i>
                                                                                    Collections are made <span
                                                                                            class="palette-primary ng-binding">8am - 6pm</span>,
                                                                                    Monday to Friday.
                                                                                </div>

                                                                                <div class="quote-info-item">
                                                                                    <i class="fa fa-search fa-fw fa-2x"></i>
                                                                                    Fully trackable service
                                                                                </div>
                                                                            </div>


                                                                        </div>
                                                                        <!-- <div role="tabpanel" class="tab-pane" id="settings">...</div> -->
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>


                                                        <br clear="all"/>


                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--End service-detail tab-->
                                <?php if (@$id > 0 && $this->isCustomized == '1') { ?>
                                    <!--routing-detail tab-->
                                    <div id="routing-detail" class="tab-pane fade">
                                        <div class="caption margin-bottom-10 block"><span
                                                    class="caption-subject bold uppercase">Routing Details</span></div>
                                        <div id="routing_details_div">
                                            <div class="row">
                                                <div class="col-md-12" id="successmsg">
                                                    <div id="statusReponse"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Country </label>
                                                        <div class="input-group input-group-sm"><span
                                                                    class="input-group-addon"><i
                                                                        class="fa fa-map-marker"></i> </span>
                                                            <?php
                                                            echo Ddl::generateCountryDDL('routing_country', $country, 'id', 'class="form-filter bs-select form-control" data-live-search="true" data-size="8"');
                                                            ?>
                                                            <span class="input-group-addon red-18">*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>From Weight</label>
                                                        <div class="input-group input-group-sm"><span
                                                                    class="input-group-addon"><i
                                                                        class="fa fa-map-marker"></i> </span>
                                                            <!-- Not able to use DDL as value of option is coming in point and cannot pass those as key in array. When passing with single quotes its giving wrong value. -->
                                                            <select name="routing_from_weight" id="routing_from_weight"
                                                                    class="form-control selectpicker select2"
                                                                    data-live-search="true" title="From Weight"
                                                                    style="width: 100%">
                                                                <?php
                                                                $weightCount = 0;
                                                                while ($weightCount < 30) {
                                                                    if ($weightCount == $fromweight)
                                                                        echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
                                                                    else
                                                                        echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
                                                                    if ($weightCount < 2)
                                                                        $weightCount += 0.25;
                                                                    //elseif($weightCount<10)$weightCount+=0.5;
                                                                    else
                                                                        $weightCount += 0.5;
                                                                }
                                                                ?>
                                                            </select><span class="input-group-addon red-18">*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>To Weight</label>
                                                        <div class="input-group input-group-sm"><span
                                                                    class="input-group-addon"><i
                                                                        class="fa fa-map-marker"></i> </span>
                                                            <select name="routing_to_weight" id="routing_to_weight"
                                                                    class="form-control selectpicker select2"
                                                                    data-live-search="true" title="To Weight"
                                                                    style="width: 100%">
                                                                <?php
                                                                $weightCount = 0.25;
                                                                while ($weightCount <= 30) {
                                                                    if ($weightCount == $toweight)
                                                                        echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
                                                                    else
                                                                        echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
                                                                    //echo "<option value=".$weightCount.">".$weightCount."</option>";
                                                                    if ($weightCount < 2)
                                                                        $weightCount += 0.25;
                                                                    //elseif($weightCount<10)$weightCount+=0.5;
                                                                    else
                                                                        $weightCount += 0.5;
                                                                }
                                                                ?>
                                                            </select><span class="input-group-addon red-18">*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Carrier </label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-addon"> 
                                                                <i class="fa fa-bars"></i></span>
                                                            <?php
                                                            // Ask for DDL
                                                            echo Ddl::generateCarrierDDLWithImage('routing_carrier', '', 'id', 'class="bs-select form-control"  data-show-subtext="true"', 'routing_carrier');
                                                            ?>
                                                            <span class="input-group-addon red-18">*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Service</label>
                                                        <div class="input-group input-group-sm"><span
                                                                    class="input-group-addon"><i
                                                                        class="fa fa-map-marker"></i> </span>
                                                            <?php
                                                            echo Ddl::generateArrayDDL('routing_service_name', array(), $service_name, 'Select Service', ' class="bs-select form-control" required="" data-show-subtext="true"', '', $service_name, "Select Services");
                                                            ?>
                                                            <span class="input-group-addon red-18">*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="clear: both;"></div>
                                                <div class="row " style="text-align:centre;" align="center">
                                                    <div class="col-md-12">
                                                        <a href="javascript:void(0)"
                                                           class="btn btn-primary saveRoutine pull-right"
                                                           id="saveRoutine"><span></span>Save</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <div class="caption"><i class="fa fa-list"></i>
                                                            <?php //echo Translation::GetCaption("GROUP_LIST");   ?>
                                                            Routing List
                                                        </div>
                                                        <div class="actions"><a
                                                                    href='product_edit.php?customize_service_id=<?php echo @$id; ?>&country=GB'
                                                                    class="btn btn-primary saveRoutine pull-right"
                                                                    id="saveRoutine"><span></span>Product Details</a>
                                                        </div>
                                                        <div class="tools"></div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-container">
                                                            <table class="table table-striped table-bordered table-hover"
                                                                   id="manage-data-table">
                                                                <thead>
                                                                <tr role="row" class="heading">
                                                                    <th>Options</th>
                                                                    <th>Service</th>
                                                                    <th>Country</th>
                                                                    <th>From Weight</th>
                                                                    <th>To Weight</th>
                                                                </tr>
                                                                <tr role="row" class="filter">
                                                                    <td> 
                                                                        <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit">
                                                                            <i class="fa fa-search"></i></button>
                                                                        <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom">
                                                                            <i class="fa fa-times"></i></button>
                                                                    </td>
                                                                    <td  class="user_acccount_correct_button">
                                                                        <?php //echo Ddl::generateDDL('search_Service', 'ServiceFilter', '      active = 1 ', 'name', 'id', '', ' class="form-filter bs-select form-control"  data-show-subtext="true" data-toggle="tooltip"  title="Services" data-live-search="true" data-original-title="Services"', 'Select  Service', '', 'search_Service', '', '', '', ''); ?>
                                                                        <?php echo Ddl::generateServiceDDLWithImage('search_Service', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services'); ?>
                                                                    </td>
                                                                    <td class="user_acccount_correct_button">
                                                                        <?php echo Ddl::generateCountryDDL('search_Country', '', 'id', ' class="form-filter bs-select form-control" data-live-search="true" data-size="8"'); ?>
                                                                    </td>
                                                                    <td class="user_acccount_correct_button">
                                                                        <input type="text"
                                                                               class="form-control form-filter input-sm"
                                                                               name="search_FromWeight">
                                                                    </td>
                                                                    <td class="user_acccount_correct_button">
                                                                        <input type="text"
                                                                               class="form-control form-filter input-sm"
                                                                               name="search_ToWeight">
                                                                    </td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="customized_div" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p>Sorry! This section is for customized service only.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--End routing-detail-->
                                <?php } ?>
                                <!--charges-tab tab-->
                                <div id="charges-tab" class="tab-pane fade">
                                    <div class="caption margin-bottom-10 block"><span
                                                class="caption-subject bold uppercase">Select Charges Options</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Uploaded Currency</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-cloud-upload"></i>
                                                    </div>
                                                    <?php
                                                    echo Ddl::generateDDL('uploaded_currency', 'CurrencyFilter', '', 'rightsymbol', 'rightsymbol', $uploaded_currency, ' class="form-control select2" data-show-subtext="false" data-toggle="tooltip"  title="Uploaded Currency" data-original-title="Uploaded Currency"', 'Uploaded Currency', '', 'Uploaded Currency', 'Uploaded Currency');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Last Exchange Rate</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-money"></i></div>
                                                    <input type="text" name="uploaded_currency_value"
                                                           id="uploaded_currency_value"
                                                           value="<?php echo @$uploaded_currency_value; ?>"
                                                           placeholder="Uploaded Currency Value" data-toggle="tooltip"
                                                           data-placement="top" title="Uploaded Currency Value"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Additional Charges</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-addon"><i class="fa fa-money"></i></div>
                                                <input type="text" name="aditional_charge" id="aditional_charge"
                                                       value="<?php echo @$aditional_charge; ?>"
                                                       placeholder="Aditional Charge" data-toggle="tooltip"
                                                       data-placement="top" title="Aditional Charge"
                                                       class="tooltipbutton form-control"/>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Registration Charges</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-sign-in"></i></div>
                                                    <input type="text" name="registration_fee" id="registration_fee"
                                                           value="<?php echo @$registration_fee; ?>"
                                                           placeholder="Registration Fee" data-toggle="tooltip"
                                                           data-placement="top" title="Registration Fee"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Label Charges</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-filter"></i></div>
                                                    <input id="label_charges" name="label_charges" type="text"
                                                           value="<?php echo @$label_charges; ?>"
                                                           placeholder="Laber Charge" data-toggle="tooltip"
                                                           data-placement="top" title="Label Charges"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Fuel Charges (%)</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-filter"></i></div>
                                                    <input id="fuelsurcharge" name="fuelsurcharge" type="text"
                                                           value="<?php echo @$fuel_surcharge; ?>"
                                                           placeholder="Fuel Surcharge(%)" data-toggle="tooltip"
                                                           data-placement="top" title="Fuel Surcharge(%)"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Fuel Charges Cost</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-filter"></i></div>
                                                    <input id="fuel_surcharge_cost" name="fuel_surcharge_cost"
                                                           type="text" value="<?php echo @$fuel_surcharge_cost; ?>"
                                                           placeholder="Fuel Surcharges Cost" data-toggle="tooltip"
                                                           data-placement="top" title="Fuel Surcharges Cost"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Carrier Address limit</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-th"></i></div>
                                                    <input type="number" name="carrier_address_limit"
                                                           id="carrier_address_limit"
                                                           value="<?php echo @$carrier_address_limit; ?>"
                                                           placeholder="Address character limit" data-toggle="tooltip"
                                                           data-placement="top" title="Address character limit"
                                                           class="tooltipbutton form-control"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Label Class Name</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"><i class="fa fa-th"></i></div>
<!--                                                    <input type="text" name="label_class_name" id="label_class_name"
                                                           value="<?php //echo @$label_class_name; ?>"
                                                           placeholder="Label class name" data-toggle="tooltip"
                                                           data-placement="top" title="Please enter class name "
                                                           class="tooltipbutton form-control"/>-->
                                                    
                                                     <?php
                    
                                                    $labelClassNamesArray =  [
                                                                            ""=>"Please select",
                                                                            "airbus"=>"airbus",
                                                                            "Anpost"=>"Anpost",
                                                                            "Aramex"=>"Aramex",
                                                                            "AsendiaUk"=>"AsendiaUk",
                                                                            "BrtItaly"=>"BrtItaly",
                                                                            "CacesaExpress"=>"CacesaExpress",
                                                                            "CanadaPost"=>"CanadaPost",
                                                                            "CoolRunner"=>"CoolRunner",
                                                                            "Correos"=>"Correos",
                                                                            "Cpost"=>"Cpost",
                                                                            "CttExpress"=>"CttExpress",
                                                                            "CTTPrimeRegistered"=>"CTTPrimeRegistered",
                                                                            "CttRegistered"=>"CttRegistered",
                                                                            "CTTUntracked"=>"CTTUntracked",
                                                                            "CzechPost"=>"CzechPost",
                                                                            "dac"=>"dac",
                                                                            "daipost"=>"daipost",
                                                                            "DeutschePost"=>"DeutschePost",
                                                                            "DeutschePostDhl"=>"DeutschePostDhl",
                                                                            "DeutschePostUntracked"=>"DeutschePostUntracked",
                                                                            "DHL"=>"DHL",
                                                                            "DhlLabel"=>"DhlLabel",
                                                                            "DirectGermany"=>"DirectGermany",
                                                                            "DPD"=>"DPD",
                                                                            "DPDDE"=>"DPDDE",
                                                                            "DPDGermany"=>"DPDGermany",
                                                                            "DpdInternationalClassic"=>"DpdInternationalClassic",
                                                                            "DPDNL"=>"DPDNL",
                                                                            "DPDPoland"=>"DPDPoland",
                                                                            "DPDUK"=>"DPDUK",
                                                                            "EEuroBattery"=>"EEuroBattery",
                                                                            "FastWay"=>"FastWay",
                                                                            "GlOrderPdfDHLGlobalMail"=>"GlOrderPdfDHLGlobalMail",
                                                                            "GlOrderPdfDPD"=>"GlOrderPdfDPD",
                                                                            "GlsNetherland"=>"GlsNetherland",
                                                                            "glspoland"=>"glspoland",
                                                                            "Hermes"=>"Hermes",
                                                                            "Hungary"=>"Hungary",
                                                                            "HuxloeHermes"=>"HuxloeHermes",
                                                                            "InternationalLabelGB"=>"InternationalLabelGB",
                                                                            "kaab"=>"kaab",
                                                                            "LapostePriority"=>"LapostePriority",
                                                                            "MailMissionSolution"=>"MailMissionSolution",
                                                                            "Omniva"=>"Omniva",
                                                                            "owe"=>"owe",
                                                                            "oweSouthAfrica"=>"oweSouthAfrica",
                                                                            "PacketPort"=>"PacketPort",
                                                                            "ParcelForce"=>"ParcelForce",
                                                                            "PassMyParcel"=>"PassMyParcel",
                                                                            "Pilot"=>"Pilot",
                                                                            "posteItalianeUntracked"=>"posteItalianeUntracked",
                                                                            "PostItalia"=>"PostItalia",
                                                                            "RoyalMail"=>"RoyalMail",
                                                                            "RoyalMailUntracked"=>"RoyalMailUntracked",
                                                                            "SFExpress"=>"SFExpress",
                                                                            "Spring"=>"Spring",
                                                                            "SpringGlobal"=>"SpringGlobal",
                                                                            "SwedenPost"=>"SwedenPost",
                                                                            "tourline"=>"tourline",
                                                                            "UKMail"=>"UKMail",
                                                                            "UKP"=>"UKP",
                                                                            "ups"=>"ups",
                                                                            "viva"=>"viva",
                                                                            "Whistl"=>"Whistl",
                                                                            "WNDirect"=>"WNDirect",
                                                                            "Yodel"=>"Yodel"];

                                                    echo Ddl::generateArrayDDL('label_class_name', $labelClassNamesArray, @$label_class_name, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Label Class Name" placeholder="Label Class Name"');
                                                    
                                                    ?>
                                                    
                                                </div>
                                            </div>
                                        </div>


                                        <br clear="all">
                                        <div class="col-sm-3">
                                            <label>Remote Area</label>
                                            <div class="form-group">
                                                <div class="md-radio-inline">
                                                    <input <?php echo($remotearea == 'ON_WEIGHT' ? 'checked="checked"' : ''); ?>
                                                            name="remotearea" type="checkbox" class="make-switch"
                                                            data-on-text="PER&nbsp;WEIGHT" data-off-text="PER ITEM"
                                                            data-on-color="primary" data-off-color="success"
                                                            data-size="mini">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Package Type</label>
                                            <div class="form-group">
                                                <div class="md-radio-inline">
                                                    <input <?php echo($package_type == '1' ? 'checked="checked"' : ''); ?>
                                                            name="package_type" type="checkbox" class="make-switch"
                                                            data-on-text="PARCEL" data-off-text="SHIPMENT"
                                                            data-on-color="primary" data-off-color="danger"
                                                            data-size="mini">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Commercial Invoice </label>
                                            <div class="form-group">
                                                <div class="md-radio-inline">
                                                    <input <?php echo($is_commercials == 'required' ? 'checked="checked"' : ''); ?>
                                                            name="is_commercials" type="checkbox" class="make-switch"
                                                            data-on-text="required" data-off-text="not required"
                                                            data-on-color="primary" data-off-color="danger"
                                                            data-size="mini">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Cn22  </label>
                                            <div class="form-group">
                                                <div class="md-radio-inline">
                                                    <input <?php echo($is_cn == 'required' ? 'checked="checked"' : ''); ?>
                                                            name="is_cn" type="checkbox" class="make-switch"
                                                            data-on-text="required" data-off-text="not required"
                                                            data-on-color="primary" data-off-color="danger"
                                                            data-size="mini">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--End charges-tab tab-->
                                <!--limitation-tab tab-->
                                <div id="limitation-tab" class="tab-pane fade">
                                    <div class="caption margin-bottom-10 block"><span
                                                class="caption-subject bold uppercase">Weight</span></div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>From Weight</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input type="text" class="form-control tooltipbutton"
                                                           name="from_weight" id="from_weight"
                                                           value="<?php echo trim($from_weight == '') ? '0.000' : @$from_weight; ?>"
                                                           size="100" onkeypress="return numbersonly(event)"
                                                           maxlength="10" onpaste="return false;"
                                                           placeholder="Default From Weight" data-toggle="tooltip"
                                                           data-placement="top" title="Default From Weight"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>To Weight</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input type="text" class="form-control tooltipbutton"
                                                           name="to_weight" id="to_weight"
                                                           value="<?php echo @$to_weight; ?>" size="100"
                                                           onkeypress="return numbersonly(event)" maxlength="10"
                                                           onpaste="return false;" placeholder="Default To Weight"
                                                           data-toggle="tooltip" data-placement="top"
                                                           title="Default To Weight"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Cut Off Time</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input type="text" class="form-control tooltipbutton"
                                                           name="cut_off_time" id="cut_off_time"
                                                           value="<?php echo @$cut_off_time; ?>"
                                                           placeholder="Cut Off Time" data-toggle="tooltip"
                                                           data-placement="top" title="Cut Off Time"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Zone Type</label>
                                                        <div class="md-radio-inline">
                                                            <input <?php echo($zone_type == 'country' ? 'checked="checked"' : ''); ?>
                                                                    name="zone_type" id="zone_type" type="checkbox"
                                                                    class="make-switch" data-on-text="Country"
                                                                    data-off-text="Postcode" data-on-color="primary"
                                                                    data-size="mini">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tariff Type</label>
                                                        <div class="md-radio-inline">
                                                            <input <?php echo($tariff_type == 'single' ? 'checked="checked"' : ''); ?>
                                                                    name="tariff_type" id="tariff_type" type="checkbox"
                                                                    class="make-switch" data-on-text="Single"
                                                                    data-off-text="Multi" data-on-color="primary"
                                                                    data-size="mini">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Validation Type</label>
                                                        <div class="md-radio-inline">
                                                            <input <?php echo($validation_type == 'courier' ? 'checked="checked"' : ''); ?>
                                                                    name="validation_type" id="validation_type" type="checkbox"
                                                                    class="make-switch" data-on-text="Courier"
                                                                    data-off-text="Mail" data-on-color="primary"
                                                                    data-size="mini">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" id="track_untrack_box">
                                                    <label>Un-Tracked Service</label>
                                                    <div class="form-group">
                                                        <div class="md-radio-inline">
                                                            <input <?php echo($is_untrack == '1' ? 'checked="checked"' : ''); ?>
                                                                    name="is_untrack" id="is_untrack" type="checkbox" class="make-switch"
                                                                    data-on-text="YES" data-off-text="NO"
                                                                    data-on-color="primary" data-off-color="danger"
                                                                    data-size="mini">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" id="track_untrack_box">
                                                    <label>EORI Number Required</label>
                                                    <div class="form-group">
                                                        <div class="md-radio-inline">
                                                            <input <?php echo($is_eori_required == '1' ? 'checked="checked"' : ''); ?>
                                                                    name="is_eori_required" id="is_eori_required" type="checkbox" class="make-switch"
                                                                    data-on-text="YES" data-off-text="NO"
                                                                    data-on-color="primary" data-off-color="danger"
                                                                    data-size="mini">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="mail_type_box">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mail Options</label>
                                                        <?php
                                                        $mailOptions = mail_options();
                                                        echo Ddl::generateArrayDDL("mail_option", $mailOptions, $mail_option, $default_select = "", $attr = ' class="form-control select2"  ', $default_select_value="", $dd_id='mail_option' ,$title='Mail Option')
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mail Type</label>
                                                        <?php
                                                        $mailTypeOptions = mail_type_options();
                                                        echo Ddl::generateArrayDDL("mail_type", $mailTypeOptions, $mail_type, $default_select = "", $attr = ' class="form-control select2"  ', $default_select_value = "", $dd_id = 'mail_type' ,$title='Mail Type')
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="delivery_type_box" <?php echo ($is_untrack == 0) ? 'style="display:none;"' : '' ?> >
                                            <label class="label-account">Economy/Priority</label>
                                            <div class="form-group">
                                                <select name="delivery_type" id="delivery_type" class="form-control select2" required="required">
                                                    <option value="all" <?php echo (@$delivery_type == "all") ? 'selected="selected"' : ""; ?> >All</option>
                                                    <option value="economy" <?php echo (@$delivery_type == "economy") ? 'selected="selected"' : ""; ?> >Economy</option>
                                                    <option value="priority" <?php echo (@$delivery_type == "priority") ? 'selected="selected"' : ""; ?> >Priority</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="courier_service_div">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Max Vol Weight</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input id="maxvolweight" name="maxvolweight" type="text"
                                                           value="<?php echo @$max_volumetric_weight; ?>"
                                                           placeholder="Max Volumetric Weight(Kg)"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Max Volumetric Weight(Kg)"/>
                                                </div>
                                                <div class="help-block">Leave it blank, if service does not consider volumetric weight.</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Vol Denominator [D]</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input id="volumetric_denominator" name="volumetric_denominator"
                                                           type="text" value="<?php echo @$volumetric_denominator; ?>"
                                                           placeholder="Volumetric Denominator"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Volumetric Denominator"/>
                                                  
                                                </div>
                                                <div class="help-block">Leave it blank, if service does not consider volumetric weight.</div>
                                            </div>
                                        </div>
                                     <!--   <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Vol Weight Formula [(L*W*H)/D]</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input id="vol_wgt_formula" name="vol_wgt_formula" type="text"
                                                           value="<?php echo @$vol_wgt_formula; ?>"
                                                           placeholder="Vol Weight Formula"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Vol Weight Formula"/>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <div class="first_form_col">
                                                    <label>Grith Formula</label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-addon"><i class="fa fa-file-o"></i>
                                                        </div>
                                                        <!-- User Document -->
                                                        <?php
                                                        $grithFormula = array('((height + width)*2) + lenght' => '((height + width)*2) + lenght',
                                                            '(height + width + lenght)' => '(height + width + lenght)',
                                                            '(height * width * lenght) / 1000000' => '(height * width * lenght) / 1000000'
                                                            );
                                                        echo Ddl::generateArrayDDL('girth_formula', $grithFormula, $girth_formula, 'Please Select', ' class="form-control select2 select" rel="tooltip" data-original-title="Girth Formula" placeholder="Girth Formula"');
                                                        ?>
                                                        <span class="input-group-addon red-18">*</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Girth Value</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input id="girth" name="girth" type="text"
                                                           value="<?php echo @$girth; ?>" placeholder="Service Girth"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Service Girth"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="caption margin-bottom-10 block col-md-12"><span
                                                    class="caption-subject bold uppercase">Dimensions</span></div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Max Length [L]</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                                    <input id="maxlength" name="maxlength" type="text"
                                                           value="<?php echo @$max_length; ?>"
                                                           placeholder="Max Length(cm)"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Max Length(cm)"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Max Width [W]</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                                    <input id="maxwidth" name="maxwidth" type="text"
                                                           value="<?php echo @$max_width; ?>"
                                                           placeholder="Max Width(cm)"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Max Width(cm)"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Max Height [H]</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                                    <input id="maxheight" name="maxheight" type="text"
                                                           value="<?php echo @$max_height; ?>"
                                                           placeholder="Max Height(cm)"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Max Height(cm)"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3" style="display: none;">
                                            <div class="form-group" style="margin-top: 30px!important;">
                                                <label>
                                                    <input id="allow_oversize" name="allow_oversize"
                                                           type="checkbox" <?php echo($allow_oversize == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck" data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Allow over size
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="mail_service_dim_div">
                                        <div class="col-sm-3">
                                            <div class="form-group margin-top-10">
                                                <label>Max Thickness</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                                    <input id="maxthickness" name="maxthickness" type="text"
                                                           value="<?php echo @$max_thickness; ?>"
                                                           placeholder="Max Thickness(cm)"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Max Thickness(cm)"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group margin-top-10">
                                                <label>Maximum Dim Formula</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <select id="maximum_dim_formula" name="maximum_dim_formula"
                                                            class="form-control select2 select select2-hidden-accessible"
                                                            rel="tooltip" data-original-title="Origin Country"
                                                            placeholder="Maximum Dim Formula" title="" tabindex="-1"
                                                            aria-hidden="true">
                                                        <option value="">Please select</option>
                                                        <option <?php
                                                        $selected = '';
                                                        if (isset($maximum_dim_formula) && $maximum_dim_formula == "L+W+H") {
                                                            $selected = 'selected="selected"';
                                                        }
                                                        echo $selected;
                                                        ?> value="L+W+H">L+W+H
                                                        </option>
                                                    </select>
                                                    <!--                                                    <input id="maximum_dim_formula" name="maximum_dim_formula" type="text" value="<?php echo @$maximum_dim_formula; ?>" placeholder="Maximum Dim Formula" class="form-control tooltipbutton"  data-toggle="tooltip" data-placement="top" title="Maximum Dim Formula"/>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group margin-top-10">
                                                <label>Maximum Allowed Dims</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                                    <input id="maximum_allowed_dimension"
                                                           name="maximum_allowed_dimension" type="text"
                                                           value="<?php echo @$maximum_allowed_dimension; ?>"
                                                           placeholder="Maximum Allowed Dimension"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top" title="Maximum Allowed Dimension"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="saturday_only_flag" name="saturday_only_flag"
                                                           type="checkbox"<?php echo($saturday_only_flag == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Only available on Saturday
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="required_email" name="required_email"
                                                           type="checkbox" <?php echo($required_email == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck" data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Email Required
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="required_telephone" name="required_telephone"
                                                           type="checkbox" <?php echo($required_telephone == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck" data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Phone Number Required
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="friday_only_flag" name="friday_only_flag"
                                                           type="checkbox"<?php echo($friday_only_flag == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Only available on Fridays
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="sunday_only_flag" name="sunday_only_flag"
                                                           type="checkbox"<?php echo($sunday_only_flag == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Only available on Sunday
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="insurance_available" name="insurance_available"
                                                           type="checkbox"<?php echo($insurance_available == 1 ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Is Insurance available
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="pre_sort" name="pre_sort"
                                                           type="checkbox"<?php echo($pre_sort == 'YES' ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Pre sort service
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="pre_advise" name="pre_advise"
                                                           type="checkbox"<?php echo($pre_advise == 'Y' ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Send Pre Advise to Carrier
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group margin-top-10">
                                                <label>
                                                    <input id="pre_alert" name="pre_alert"
                                                           type="checkbox"<?php echo($pre_alert == 'Y' ? ' checked="checked"' : ''); ?>
                                                           class="icheck " data-checkbox="icheckbox_flat-blue"
                                                           value="1"/> Send Manifest (Pre Alert) to Carrier
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9"
                                             id="pre_alert_email_div" <?php echo($pre_alert == 'Y' ? ' ' : 'style="display: none;"'); ?>>
                                            <div class="form-group">
                                                <label>Pre Alert Email</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                                    <input id="pre_alert_email" name="pre_alert_email" type="text"
                                                           value="<?php echo @$pre_alert_email; ?>"
                                                           placeholder="Please enter a comma-separated list of email addresses"
                                                           class="form-control tooltipbutton" data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Please enter a comma-separated list of email addresses"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--End limitation-tab tab-->
                                <!--extra-tab tab-->
                                <div id="extra-tab" class="tab-pane fade">
                                    <div class="caption margin-bottom-10 block"><span
                                                class="caption-subject bold uppercase">Additional Details</span></div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <?php
                                                if (trim($additional_details) == "")
                                                    $additional_details = 'tracking will be on same number or other number
Contact Details of operaton
Contact Details of customer Service
Contact Dtails of sales
Key contact number';
                                                ?>
                                                <textarea id="additional_details" name="additional_details"
                                                          placeholder="tracking will be on same number or other number <br>Contact Details of operaton<br>Contact Details of customer Service <br>Contact Dtails of sales<br>Key contact number"
                                                          rows="6" class="htmlcode form-control" data-toggle="tooltip"
                                                          data-placement="top" title=""
                                                          data-original-title="Additional Details"><?php echo @$additional_details; ?></textarea>

                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="caption margin-bottom-10 block"><span
                                                        class="caption-subject bold uppercase">Description</span></div>
                                            <div class="form-group">
                                                <textarea id="description" name="description" data-provide="markdown"
                                                          class="form-control htmlcode" rows="7" cols="150"
                                                          placeholder="Description" rel="tooltip"
                                                          data-original-title="Description" data-placement="top"
                                                          data-toggle="tooltip" data-placement="top" title=""
                                                          data-original-title="Additional Details"><?php echo @$description; ?></textarea>
                                            </div>
                                        </div>

                                        <?php if ($id > 0) { ?>
                                            <?php ?>
                                            <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="caption margin-bottom-10 block"><span
                                                            class="caption-subject bold uppercase">Company Documents Copy</span>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="first_form_col">
                                                                <label>Document Type</label>
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-addon"><i
                                                                                class="fa fa-file-o"></i></div>
                                                                    <!-- User Document -->
                                                                    <?php
                                                                    echo Ddl::generateDDL('document_name', 'DocumentTypeFilter', " is_active = '1' AND is_delete = '0' AND document_type = 'service_agent'", 'document_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="User Document Type" data-original-title="User Document Type"', '', 'Select document type', 'document_name', 'User Document Type', '', '');
                                                                    ?>
                                                                    <span class="input-group-addon red-18">*</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="first_form_col">
                                                                <label>Agent ( Optional ) </label>
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-addon"><i
                                                                                class="fa fa-user"></i></div>
                                                                    <!-- User Document -->
                                                                    <?php
                                                                    echo Ddl::generateDDL('agent_doc_name', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select"', "Please select agent", "", "agent_doc_name", "Agent");
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Please Select File</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="fileinput fileinput-new"
                                                                 data-provides="fileinput">
                                                                <div class="input-group input-group-sm">
                                                                    <div class="form-control uneditable-input input-fixed input-medium"
                                                                         data-trigger="fileinput">
                                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                                        <span class="fileinput-filename"> </span>
                                                                    </div>
                                                                    <span class="input-group-addon btn default btn-file">
                                                                            <span class="fileinput-new"> Select file </span>
                                                                            <span class="fileinput-exists"> Change </span>
                                                                            <input type="file" name="file_name"
                                                                                   id="file_name"> </span>
                                                                    <a href="javascript:;"
                                                                       class="input-group-addon btn red fileinput-exists"
                                                                       data-dismiss="fileinput"> Remove </a>
                                                                    <a href="javascript:;"
                                                                       class="input-group-addon btn blue"
                                                                       id="upload_file" data-original-title="" title="">Upload</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="append_service_doc">
                                                    <?php
                                                    if (@$id > 0) {

                                                        $serviceDocumentFilter = new serviceDocumentFilter();
                                                        $serviceDocumentLists = $serviceDocumentFilter->getServiceDoc($id);
                                                        foreach ($serviceDocumentLists as $serviceDocumentList) {
                                                            $temp = explode(".", $serviceDocumentList->getDocumentName());
                                                            $extension = end($temp);
                                                            $fileFullPath = "../_assets/service_documents/" . $id . "/" . $serviceDocumentList->getDocumentName();
                                                            if (!file_exists($fileFullPath) || $extension == "pdf") {
                                                                $fileFullPath = "../images/No-image-found.jpg";
                                                            }
                                                            if ($extension == "pdf") {
                                                                $fileFullPath = "../images/pdf.png";
                                                            }
                                                            ?>
                                                            <div class="col-md-3"
                                                                 id="ser_doc_<?php echo $serviceDocumentList->getId(); ?>">
                                                                <div class="thumbnail">
                                                                    <div class="document-thumb">
                                                                        <img src="<?php echo $fileFullPath ?>"
                                                                             alt="<?php echo $serviceDocumentList->getDocumentName(); ?>"
                                                                             data-src="<?php echo $fileFullPath ?>">
                                                                    </div>
                                                                    <div class="caption">
                                                                        <h3><?php echo $serviceDocumentList->getAgentId(); ?>
                                                                            <br><?php echo $serviceDocumentList->getDocumentId(); ?>
                                                                        </h3>
                                                                        <a target="_blank"
                                                                           href="../_assets/service_documents/<?php echo $id . "/" . $serviceDocumentList->getDocumentName(); ?>"
                                                                           class="btn blue"> View </a>
                                                                        <a href="javascript:;"
                                                                           class="btn red remove_doc"
                                                                           data-doc_id="<?php echo $serviceDocumentList->getId(); ?>">
                                                                            Remove </a>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                                <!--End extra-tab tab-->
                                <?php if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0 && $this->isCustomized != '1') { ?>
                                    <!--agent-tab tab-->
                                    <div id="agent-tab" class="tab-pane fade">
                                        <?php
                                        if (!empty($this->agentList)) {
                                            $inc = 0;
                                            foreach ($this->agentList as $agentList) {
                                                if ($inc == 0) {
                                                    echo '<div class="clone_div">';
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-addon"><i
                                                                            class="fa fa-user"></i></div>
                                                                <?php
                                                                $agentName = "agent[" . $inc . "]";
                                                                $selected_value = $agentList->getAgentid();
                                                                echo Ddl::generateDDL($agentName, 'AgentDataFilter', "(agent_type = 'carrier' OR agent_type = 'both') ", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select"', "Please select agent", "", "agent", "Agent");
                                                                ?>
                                                                <span class="input-group-addon red-18">*</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent From Weight</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Agent From Weight is mandatory">*</i>
                                                                    <input id="agent_from_weight" type="text"
                                                                           name="agent_from_weight[<?php echo $inc; ?>]"
                                                                           value="<?php echo $agentList->getFromWeight(); ?>"
                                                                           placeholder="From Weight"
                                                                           class="form-control agent_from_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="From Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent To Weight</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Agent To Weight is mandatory">*</i>
                                                                    <input id="agent_to_weight" type="text"
                                                                           name="agent_to_weight[<?php echo $inc; ?>]"
                                                                           value="<?php echo $agentList->getToWeight(); ?>"
                                                                           placeholder="To Weight"
                                                                           class="form-control agent_to_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="To Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 show_remove_btn"
                                                         data-index-of-agent='<?php echo $inc; ?>' <?php if ($inc == 0) { ?> style="display: none;" <?php } ?> >
                                                        <label class="control-label">&nbsp;</label><br/>
                                                        <a href="javascript:;" class="btn btn-danger repeater-delete">
                                                            <i class="fa fa-close"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                                if ($inc == 0) {
                                                    echo '</div><div class="append_here">';
                                                }
                                                $inc++;
                                            }
                                            echo '</div>';
                                        } else {
                                            ?>
                                            <div class="clone_div">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-addon"><i
                                                                            class="fa fa-user"></i></div>
                                                                <?php
                                                                $selected_value = "";
                                                                echo Ddl::generateDDL('agent[0]', 'AgentDataFilter', " (agent_type = 'carrier' OR agent_type = 'both') AND id IN (" . $this->allowedAgentId . ")", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select"', "Please select agent", "", "agent", "Agent");
                                                                ?>
                                                                <span class="input-group-addon red-18">*</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent From Weight</label>
                                                            <div class="input-group input-group-sm"><span
                                                                        class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Agent From Weight is mandatory">*</i>
                                                                    <input id="agent_from_weight" type="text"
                                                                           name="agent_from_weight[0]" value=""
                                                                           placeholder="From Weight"
                                                                           class="form-control agent_from_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="From Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent To Weight</label>
                                                            <div class="input-group input-group-sm"><span
                                                                        class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Agent To Weight is mandatory">*</i>
                                                                    <input id="agent_to_weight" type="text"
                                                                           name="agent_to_weight[0]" value=""
                                                                           placeholder="To Weight"
                                                                           class="form-control agent_to_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="To Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 show_remove_btn" data-index-of-agent='0'
                                                         style="display: none;">
                                                        <label>&nbsp;</label><br/>
                                                        <div class="input-group input-group-sm">
                                                            <a href="javascript:;"
                                                               class="btn btn-danger repeater-delete">
                                                                <i class="fa fa-close"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="append_here"></div>
                                        <?php } ?>
                                        <hr>
                                        <a href="javascript:;" class="btn btn-info repeater-add">
                                            <i class="fa fa-plus"></i> Add Agent
                                        </a>
                                        <br>
                                        <br>
                                    </div>
                                    <!--agent-tab tab-->
                                    <!--End agent-tab tab-->
                                    <div id="dispatch-agent-tab" class="tab-pane fade">
                                        <?php
                                        if (!empty($this->agentDispatchList)) {
                                            $inc = 0;
                                            foreach ($this->agentDispatchList as $agentList) {
                                                if ($inc == 0) {
                                                    echo '<div class="dispatch_clone_div">';
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Dispatch Agent</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-addon"><i
                                                                            class="fa fa-user"></i></div>
                                                                <?php
                                                                $agentDName = "dispatch_agent[" . $inc . "]";
                                                                $selected_value = $agentList->getAgentid();
                                                                echo Ddl::generateDDL($agentDName, 'AgentDataFilter', "agent_type = 'dispatch' OR agent_type = 'both'", 'agent_name', 'id', $selected_value, ' class="form-control select2 dispatch_agent_select"', "Please select dispatch agent", "", "agent", "Agent");
                                                                ?>
                                                                <span class="input-group-addon red-18">*</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Dispatch Agent From Weight</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Dispatch Agent From Weight mandatory">*</i>
                                                                    <input id="dispatch_agent_from_weight" type="text"
                                                                           name="dispatch_agent_from_weight[<?php echo $inc; ?>]"
                                                                           value="<?php echo $agentList->getFromWeight(); ?>"
                                                                           placeholder="From Weight"
                                                                           class="form-control dispatch_agent_from_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="From Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Dispatch Agent To Weight</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Dispatch Agent To Weight is mandatory">*</i>
                                                                    <input id="dispatch_agent_to_weight" type="text"
                                                                           name="dispatch_agent_to_weight[<?php echo $inc; ?>]"
                                                                           value="<?php echo $agentList->getToWeight(); ?>"
                                                                           placeholder="To Weight"
                                                                           class="form-control dispatch_agent_to_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="To Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 dispatch_show_remove_btn"
                                                         data-index-of-agent='<?php echo $inc; ?>' <?php if ($inc == 0) { ?> style="display: none;" <?php } ?> >
                                                        <label class="control-label">&nbsp;</label><br/>
                                                        <a href="javascript:;"
                                                           class="btn btn-danger dispatch-repeater-delete">
                                                            <i class="fa fa-close"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                                if ($inc == 0) {
                                                    echo '</div><div class="dispatch_append_here">';
                                                }
                                                $inc++;
                                            }
                                            echo '</div>';
                                        } else {
                                            ?>
                                            <div class="dispatch_clone_div">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Agent</label>
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-addon"><i
                                                                            class="fa fa-user"></i></div>
                                                                <?php
                                                                $selected_value = "";
                                                                echo Ddl::generateDDL('dispatch_agent[0]', 'AgentDataFilter', "agent_type = 'dispatch' OR agent_type = 'both'", 'agent_name', 'id', $selected_value, ' class="form-control select2 dispatch_agent_select"', "Please select agent", "", "agent", "Agent");
                                                                ?>
                                                                <span class="input-group-addon red-18">*</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Dispatch Agent From Weight</label>
                                                            <div class="input-group input-group-sm"><span
                                                                        class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Dispatch Agent From Weight is mandatory">*</i>
                                                                    <input id="dispatch_agent_from_weight" type="text"
                                                                           name="dispatch_agent_from_weight[0]" value=""
                                                                           placeholder="From Weight"
                                                                           class="form-control dispatch_agent_from_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="From Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Dispatch Agent To Weight</label>
                                                            <div class="input-group input-group-sm"><span
                                                                        class="input-group-addon"> <i
                                                                            class="fa fa-key"></i> </span>
                                                                <div class="input-icon right"><i
                                                                            class="fa tooltips font-red"
                                                                            data-original-title="Dispatch Agent To Weight is mandatory">*</i>
                                                                    <input id="dispatch_agent_to_weight" type="text"
                                                                           name="dispatch_agent_to_weight[0]" value=""
                                                                           placeholder="To Weight"
                                                                           class="form-control dispatch_agent_to_weight"
                                                                           data-toggle="tooltip" data-placement="top"
                                                                           title="To Weight"/>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 dispatch_sshow_remove_btn"
                                                         data-index-of-agent='0' style="display: none;">
                                                        <label>&nbsp;</label><br/>
                                                        <div class="input-group input-group-sm">
                                                            <a href="javascript:;"
                                                               class="btn btn-danger dispatch-repeater-delete">
                                                                <i class="fa fa-close"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dispatch_append_here"></div>
                                        <?php } ?>
                                        <hr>
                                        <a href="javascript:;" class="btn btn-info dispatch-repeater-add">
                                            <i class="fa fa-plus"></i> Add Agent
                                        </a>
                                        <br>
                                        <br>
                                    </div>
                                    <!-- End dispatch agent tab -->
                                <?php } ?>
                            </div>
                            <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" class="form-control"/>
                            <input type="hidden" name="form_action" id="form_action" value=""/>
                            <div id="append_unselected_country"></div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a id="btnCancel" href="services_list.php" class="btn_cancel btn btn-default">Cancel <i
                                            class="fa fa-close"></i> </a> <a href="javascript:;"
                                                                             class="btn default button-previous"> <i
                                            class="fa fa-angle-left"></i> Back </a> <a href="javascript:;"
                                                                                       class="btn btn-primary button-next">
                                    Continue <i class="fa fa-angle-right"></i> </a>
                                <?php if (in_array($this->sessionUser->getUserType(), array(User::USER_TYPE_ADMIN)) && $id > 0) { ?>
                                    <a id="btnCancel"
                                       href="service_country_time.php?carrier_id=<?= $carrier ?>&service_id=<?= $id ?>"
                                       class="btn btn-primary">Transit</a>
                                <?php } ?>

                                <?php if (in_array($this->sessionUser->getUserType(), array(User::USER_TYPE_ADMIN))) { ?>
                                    <a href="javascript:;" class="btn btn-primary button-submit"> Submit <i
                                                class="fa fa-check"></i> </a>
                                    <?php
                                }
                                // Show delete
                                if (@$id > 0) {
                                    ?>
                                    <a data-title="Services" data-table="service" data-container="audit_content"
                                       data-ajax_url="index.php" data-id="<?php echo util_get_num("id") ?>"
                                       id="btnAudit" href="javascript:;" class="btn btn-primary show_audit"
                                       title="audit" data-target="#audit-log" data-toggle="modal">
                                        Audit
                                    </a>
                                    <?php
                                    if (in_array($this->sessionUser->getUserType(), array(User::USER_TYPE_ADMIN)))
                                        echo '<a id="btnDelete"   href="javascript:;" class="btn btn-danger"><span></span>Delete <i class="fa fa-trash"></i> </a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
    }

    protected function renderFooter()
    {
        ?>
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

    public function getClientIp()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
