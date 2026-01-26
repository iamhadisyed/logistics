<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country details page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");     
include_classes([   
            'auditlogs.inc','ddl.inc'
        ],'library');
include_classes([
    'iaddress.class',
    'invoices.class',
    'invoicesfilter.class',
    'country.class',
    'countryfilter.class',
    'agentdocument.class',
    'agentdocumentfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'carrier.class',
    'carrierfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'documenttype.class',
    'documenttypefilter.class',
    'agentrestrictedpostcode.class',
    'agentrestrictedpostcodefilter.class',
    'agentlog.class',
    'agentlogfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'services.class' ,
    'servicefilter.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'serviceagentmapping.class',
    'serviceconstant.class',
    'serviceconstantfilter.class',
     'serviceconstant.class',
    'serviceconstantfilter.class.php',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class'
    
]);     
// set up local page class
class Page extends BasePage {

    private $error_msg = "";
    private $counting_error = "";
    private $agentList = "";

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        if (!Permissions::checkFilePermission('agent_details.php'))
            util_redirect("index.php");

        $agentid = util_get_num("id");
        if ($agentid > 0)
            $agentBreadCrumps = "Edit Agent";
        else
            $agentBreadCrumps = "Add Agent";
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"), 'agent.php' => Translation::GetCaption("AGENT"),
            $agentBreadCrumps
        );

        // check admin user is authenticated
        $user = SessionManager::getUser();



        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_form_fields_for_service") {
            $output = array();

            $output["STATUS"] = true;
            $output["MESSAGE"] = print_r($this->form_vars, true);

            $agent_id = $this->form_vars['agent_id'];
            $service_id = $this->form_vars['service_id'];
            $serviceObject = new Services((int) $service_id);
            if ($serviceObject->getId() <= 0) {
                $output["STATUS"] = true;
                $output["MESSAGE"] = '<div class="alert alert-danger"> We haven\'t found any service, please select and try again.</div>';
            } else {
                $carrier_id = $serviceObject->getCarrierId();
                $serviceConstantObj = new ServiceConstantFilter();
                $serviceConstantObj->addFilter("carrier_id = '" . $carrier_id . "' or carrier_id = 0");
                $serviceConstantObj->AddOrderBy("sort_order");
                $serviceConstantList = $serviceConstantObj->getList();
                $serviceConstantFilterObj = new ServiceConstantFilter();
                $servConstantList = $serviceConstantFilterObj->getConstantValue($agent_id, $carrier_id, $service_id);

                if (count($servConstantList) > 0) {
                    $editValueArray = array();
                    foreach ($servConstantList as $scList) {
                        $editValueArray[$scList->getId()] = $scList->getConstantValue();
                    }

                    $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingFilter->addFilter(" agentid = '" . $agent_id . "'");
                    $serviceAgentList = $serviceAgentMappingFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                    if (count($serviceAgentList) > 0) {
                        $serviceArray = array();
                        foreach ($serviceAgentList as $saList) {
                            $serviceArray[] = $saList->getServiceId();
                            $from_weight = $saList->getFromWeight();
                            $to_weight = $saList->getToWeight();
                        }
                    }
                }
                    if (count($serviceConstantList) > 0) {
                        $ranges = 0;
                        $html = '';
                        $col = "3";
                        foreach ($serviceConstantList as $serviceObj) {
                            $mandatory = $serviceObj->getMandatory();
                            $markRequiredField = "";
                            $required = "";
                            if ($mandatory == 1) {
                                $markRequiredField .= '<i class="fa tooltips font-red">*</i>';
                                $required = "required";
                            }
                            if ($serviceObj->getConstant() == "RANGES") {
                                $ranges = 1;
                            }
                            if ($serviceObj->getDesignControl() == "TEXTBOX") {

                                $html .= '<div class="col-md-' . $col . ' ' . $serviceObj->getIntegrationType() . '">
                                                <div class="form-group"> 
                                                    <label>' . $serviceObj->getCaption() . '</label>
                                                    <div class="input-group input-group-sm input-icon right" > 
                                                        <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                                $html .= $markRequiredField;
                                $html .= '<input name="' . $serviceObj->getConstant() . '" id="' . $serviceObj->getConstant() . '" value="' . @$editValueArray[$serviceObj->getId()] . '" size="50" class="form-control"  maxlength="35" title="" placeholder="' . $serviceObj->getCaption() . '" rel="tooltip" data-original-title="' . $serviceObj->getCaption() . '" type="text" ' . $required . '>
        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            ';
                            } else if ($serviceObj->getDesignControl() == "DROPDOWN") {

                                $html .= '<div class="col-md-' . $col . ' ' . $serviceObj->getIntegrationType() . '">
                                                <div class="form-group"> 
                                                    <label>' . $serviceObj->getCaption() . '</label>
                                                    <div class="input-group input-group-sm input-icon right" > 
                                                        <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                                if (trim($serviceObj->getDefaultValues()) != '') {
                                    $value_array = json_decode($serviceObj->getDefaultValues(), true);
                                    //, $default_select = "", $attr = "", $default_select_value = "", $dd_id = '' ,$title='',$use_key_value = ''
                                    $html .= Ddl::generateArrayDDL($serviceObj->getConstant(), $value_array, @$editValueArray[$serviceObj->getId()], "Please Select", ' class="form-control form-filter select2"', "", $serviceObj->getConstant(), 'Select Select', '');
                                } else {
                                    $html .= Ddl::generateCountryDDL($serviceObj->getConstant(), @$editValueArray[$serviceObj->getId()], 'id');
                                }

                                $html .= '  </div>
                                                </div>
                                            </div>';
                            }
                        }
                        $output["STATUS"] = true;
                        $output["MESSAGE"] = $html;
                    }
            }
            echo json_encode($output);
            die;
        } else if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'service_barcode_ajax') {
            $serviceId = $this->form_vars['serviceId'];
            $agentId = $this->form_vars['agentId'];
            $serviceRangingMapping = new ServiceRangeMappingFilter();
            if (!empty($serviceId))
                $serviceRangingMapping->addFieldFilter('    service_id', $serviceId);
            if (!empty($agentId))
                $serviceRangingMapping->addFieldFilter('    agent_id', $agentId);

            $serviceRangingMappingData = $serviceRangingMapping->getList();
            $licenseDataArr = [];
            $serviceList = '<tr><td colspan="2">No Record Found</td></tr>';
            if (!empty($serviceRangingMappingData)) {
                $licenseId = $serviceRangingMappingData[0]->getLicencePlateId();
                $licensePlateObj = new LicencePlateFilter();
                $licensePlateObj->addFieldFilter('  id', $licenseId);
                $licenseData = $licensePlateObj->getList();

                if (!empty($licenseData)) {
                    foreach ($licenseData as $data) {
                        $licenseDataArr['success'] = 'Success';
                        $licenseDataArr['id'] = $data->getId();
                        $licenseDataArr['range_name'] = $data->getRangeName();
                        $licenseDataArr['range_start'] = $data->getRangeStart();
                        $licenseDataArr['range_end'] = $data->getRangeEnd();
                        $licenseDataArr['next_number'] = $data->getNextNumber();
                        $licenseDataArr['increment_date'] = $data->getIncrementDate();
                        $licenseDataArr['delivery_network'] = $data->getDeliveryNetwork();
                        $licenseDataArr['prefix'] = $data->getPrefix();
                        $licenseDataArr['sufix'] = $data->getSufix();
                    }
                } else {
                    $licenseDataArr['error'] = 'error';
                }
            } else {
                $licenseDataArr['error'] = 'not_found';
            }
            $serviceRangeMappingObj = new ServiceRangeMappingFilter();
            $serviceRangeMappingList = $serviceRangeMappingObj->getAgentServicesList($serviceId);
            if (!empty($serviceRangeMappingList)) {
                $serviceList = '';
                foreach ($serviceRangeMappingList as $data) {
                    $serviceList .= "<tr>"
                            . "<td>" . $data->getAgentName() . "</td>"
                            . "<td>" . $data->getName() . "</td>"
                            . "</tr>";
                }
            }
            $licenseDataArr['list'] = $serviceList;
            echo json_encode($licenseDataArr);
            exit;
        }
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {

            $range_id = $this->form_vars["id"];
            $start_range = $this->form_vars["start_range"];
            $range_name = $this->form_vars["range_name"];
            $end_range = $this->form_vars["end_range"];
            $prefix = $this->form_vars["prefix"];
            $sufix = $this->form_vars["suffix"];
            $next_number = $this->form_vars["next_number"];

            $error_array = array();
            $headerMessage = '';

            $licencePlateObj = new LicencePlate($range_id);
            $oldlicencePlateObj = new LicencePlate($range_id);
            $licencePlateObj->setRangeName($range_name);
            $licencePlateObj->setRangeStart($start_range);
            $licencePlateObj->setRangeEnd($end_range);

            $licencePlateObj->setNextNumber($next_number);
            $countryRange = 0;
            if (isset($this->form_vars["countryrange"]))
                $countryRange = 1;

            $licencePlateObj->setCountryRange($countryRange);
            if ((int) $range_id <= 0) {
                $licencePlateObj->setAddedBy($user->getId());
                $licencePlateObj->setDateCreated(time());
            } else if ($range_id > 0 && $licencePlateObj->getNextNumber() == $licencePlateObj->getRangeEnd()) {
                $licencePlateObj->setNextNumber($start_range);
            }

            $licencePlateObj->setPrefix($prefix);
            $licencePlateObj->setSufix($sufix);
            $licencePlateObj->setUpdatedBy($user->getId());
            $licencePlateObj->setDateUpdated(time());

            $licencePlateObj->save();
            $msg = [];
            $licenceplate_id = $licencePlateObj->getId();
            if ($licenceplate_id)
                $msg['success'] = $licenceplate_id;
            else
                $msg['error'] = 'error';
//                        $newLicencePlateObj = $licencePlateObj;
            echo json_encode($msg);
            exit;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_agent_services_form') {
            $agentSericeId = $this->form_vars['id'];
            $agentId = $this->form_vars['agentId'];
            if ($agentSericeId > 0) {
                $agentServiceDetails = new ServiceAgentMapping($agentSericeId);
                $httpVerb = 'update';
            } else {
                $agentServiceDetails = new ServiceAgentMapping();
                $httpVerb = "add";
            }
            ?>
            <form name="agentServiceForm" id="agentServiceForm" action="agent_details.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name ="agent_id"  value="<?php echo $agentId; ?>" >
                <input type="hidden" name="form_action" id="form_action" value="save_agent_sevice" />
                <input type="hidden" value="<?php echo $httpVerb; ?>" name="httpVerb">
                <input type="hidden" value="<?php echo $agentSericeId; ?>" name="agentServiceId">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label >Services</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                    <?php
                                    $selected_value = $agentServiceDetails->getServiceId();
                                    echo Ddl::generateServiceDDLWithImage('service_id', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services');
                                    ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Service Provider File</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-file-code-o"></i></span>
                                    <input class="form-control" name="class_file_name" id="class_file_name" type="text" value="<?php echo $agentServiceDetails->getClassFileName(); ?>" placeholder="Service Provide File" title="Service Provide File">
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php
                    if ($agentServiceDetails->getServiceId() > 0 && $agentServiceDetails->getAgentId() > 0) {
                        $agentRestrictedPostcode = new AgentRestrictedPostcodeFilter();
                        $agentRestrictedPostcode->addFieldFilter("service_id", $agentServiceDetails->getServiceId());
                        $agentRestrictedPostcode->addFieldFilter("agent_id", $agentServiceDetails->getAgentId());
                        $agentRestrictedList = $agentRestrictedPostcode->getList();
                        if (count($agentRestrictedList) > 0) {
                            $restrictedPostcodelist = array();
                            $restrictedCitylist = array();
                            foreach ($agentRestrictedList as $alist) {
                                if ($alist->getIsCity() == 0) {
                                    $restrictedPostcodelist[] = $alist->getPostcodeCity();
                                } else {
                                    $restrictedCitylist[] = $alist->getPostcodeCity();
                                }
                            }
                            $restricted_postcode = implode(",", $restrictedPostcodelist);
                            $restricted_city = implode(",", $restrictedCitylist);
                        }
                    }
                    ?>
                    <div class="row">
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label >Restricted Postcode</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <textarea class="form-control" name="restricted_postcode" id="restricted_postcode"  rows="4" cols="50" data-original-title="" title=""><?php echo $restricted_postcode; ?> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label >Restricted City</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <textarea class="form-control" name="restricted_city"  id="restricted_city" rows="4" cols="50" data-original-title="" title=""><?php echo $restricted_city; ?> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div id="integration_form_data" class="row" style="display: none;">

                    </div>


                    <div class="row">
                        <hr>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Insurance Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="insurance_charges" name="insurance_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getInsuranceCharges(); ?>" title="Insurance Charges" placeholder="Insurance Charges">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Reroute Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="reroute_charges" name="reroute_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getRerouteCharges(); ?>" title="Reroute Charges" placeholder="Reroute Charges">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Oversize Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="oversize_charges" name="oversize_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getOversizeCharges(); ?>" title="Oversize Charges" placeholder="Oversize Charges">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Address Change Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="address_change_charges" name="address_change_charges" onkeypress="return numbersonly(event)" type="text" value="<?php echo $agentServiceDetails->getAddressChangeCharges(); ?>" title="Address Change Charges" placeholder="Address Change Charges">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Return Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="return_charges" name="return_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getReturnCharges(); ?>" title="Return Charges" placeholder="Return Charges">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Relabel Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="relabel_charges" name="relabel_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getRelabelCharges(); ?>" title="Relabel Charges" placeholder="Relabel Charges">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Insurance Cover</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="insurance_cover" name="insurance_cover" onkeypress="return numbersonly(event)" type="text" value="<?php echo $agentServiceDetails->getInsuranceCover(); ?>" title="Insurance Cover" placeholder="Insurance Cover">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Wrong Address Charges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="wrong_address_charges" name="wrong_address_charges" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getWrongAddressCharges(); ?>" title="Wrong Address Charges" placeholder="Wrong Address Charges">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Other Surcharges</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                    <input class="form-control" id="other_surcharges" name="other_surcharges" type="text" value="<?php echo $agentServiceDetails->getOtherSurcharges(); ?>" onkeypress="return numbersonly(event)" title="Other Charges" placeholder="Other Charges">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>From Weight</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                    <input class="form-control" name="from_weight" id="from_weight" type="text" value="<?php echo $agentServiceDetails->getFromWeight(); ?>" onkeypress="return numbersonly(event)" placeholder="From Weight" title="From Weight">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>To Weight</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                    <input class="form-control" name="to_weight" id="to_weight" type="text" onkeypress="return numbersonly(event)" value="<?php echo $agentServiceDetails->getToWeight(); ?>" placeholder="To Weight" title="To Weight">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
            <?php
//			echo AuditLogs::getAuditItemDetails($table,$cid);
            exit;
        }
        if (isset($_GET['action']) && $_GET['action'] == "agent_ajax" && isset($_GET['agent_id'])) {
            $agentId = $_GET['agent_id'];
            $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
            $serviceAgentMappingFilter->addFilter("agentid =" . intval($agentId));
            $serviceAgentMappingFilter->addServicesJoin();

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
                $dataTableColumnName = strtolower($this->form_vars['columns'][$dataTableColumnId]['data']);

                if ($dataTableColumnName == "service_name") {
                    $serviceAgentMappingFilter->AddOrderBy('ser.name', $orderFalse);
                } else if ($dataTableColumnName != '') {
                    $serviceAgentMappingFilter->AddOrderBy($dataTableColumnName, $orderFalse);
                }
            }


            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $search_service = $this->form_vars['search_service_id'];
                if (!empty($search_service))
                    $serviceAgentMappingFilter->addFieldFilter('serviceid', $search_service);

                $search_intgType = $this->form_vars['search_integration_type'];
                if (!empty($search_intgType))
                    $serviceAgentMappingFilter->addFieldLikeFilter('integration_type', $search_intgType);

                $search_classFileName = $this->form_vars['search_class_file_name'];
                if (!empty($search_classFileName))
                    $serviceAgentMappingFilter->addFieldLikeFilter('class_file_name', $search_classFileName);

                $search_insCharges = $this->form_vars['search_insurance_charges'];
                if (!empty($search_insCharges))
                    $serviceAgentMappingFilter->addFieldFilter('insurance_charges', $search_insCharges);

                $search_reRouteCharges = $this->form_vars['search_reroute_charges'];
                if (!empty($search_reRouteCharges))
                    $serviceAgentMappingFilter->addFieldFilter('reroute_charges', $search_reRouteCharges);

                $search_reliableCharges = $this->form_vars['search_reliable_charges'];
                if (!empty($search_reliableCharges))
                    $serviceAgentMappingFilter->addFieldFilter('relabel_charges', $search_reliableCharges);
            }

            $iTotalRecords = $serviceAgentMappingFilter->getPagingCount();
            //Paginatiopn code start here
            $agentDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $serviceAgentMappingFilter->setRowsPerPage($iDisplayLength);
            $serviceAgentMappingFilter->setOffset($iDisplayStart);
//			if($dataTableColumnName != '')
//				$serviceAgentMappingFilter->AddOrderBy($dataTableColumnName, $orderFalse);
            $agentServices = $serviceAgentMappingFilter->getPagingList();
//			echo "<pre>"; print_r($agentServices); echo "</pre>"; die();
            foreach ($agentServices as $agentService) {
                $service = new Services($agentService->getServiceid());
                $agentArr = array();
                $agentArr['option'] .= '<div class="btn-group">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">';
//				if (Permissions::checkFilePermission('agent_view')) {
//					$agentArr['option'] .= '<li>
//                                                       <a href="agent_view.php?id="'. $agentService->getId() .' class="btnedit ">
//                                                            <i class="fa fa-eye"></i> View
//                                                        </a>
//                                                    </li>';
//				}
                if (Permissions::checkFilePermission('agent_edit')) {

                    $agentArr['option'] .= '<li>
                                                       <a  data-title="Agent" data-agentid="' . $agentService->getAgentid() . '" data-container="agent_service_content" data-ajax_url="agent_details.php" data-id="' . $agentService->getId() . '" id="btnAudit" href="javascript:void(0)" class="btnedit  show_agent_service"  title="Edit Agent Service" data-target="#edit-agent-service" data-toggle="modal">
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </a>
                                                    </li>';
                }
                //if (Permissions::checkFilePermission('agent_add_email')) {

                $agentArr['option'] .= '<li>
                                                       <a  data-title="Agent" data-agentid="' . $agentService->getAgentid() . '" data-serviceid="' . $agentService->getServiceId() . '" data-servicename = "'.$service->getName().'" data-container="agent_add_email" data-ajax_url="agent_details.php" data-id="' . $agentService->getId() . '" data-email="'.$agentService->getEmail().'" data-email_finance="'.$agentService->getEmailFinance().'" id="btnAddEmail" href="javascript:void(0)" class="btnAddEmail show_add_email"  title="Add Email" data-target="#addAgentEmail" data-toggle="modal">
                                                            <i class="fa fa-pencil"></i> Add Emails
                                                        </a>
                                                    </li>';
                //}
                $agentArr['option'] .= '<li>
                                                       <a  data-title="Barcode" data-agentid="' . $agentService->getAgentid() . '" data-container="agent_service_barcode_content" data-ajax_url="agent_details.php" data-id="' . $agentService->getId() . '" data-serviceid="' . $agentService->getServiceId() . '" id="btnServiceBarCode" href="javascript:;" class="btnservicebarcode"  title="Service Bar Code" data-target="#edit_service_barcode" data-toggle="modal">
                                                            <i class="fa fa-barcode"></i> Service Bar Code
                                                        </a>
                                                    </li>';
                if (Permissions::checkFilePermission('agent_delete')) {
                    $agentArr['option'] .= '<li>
                                                      <a title="Delete" data-id ="' . $agentService->getId() . '" class="btndelete">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    </li>';
                }
                $agentArr['option'] .= '</ul> </div>';
                
                $agentArr['service_name'] = $service->getName();
                $agentArr['integration_type'] = $agentService->getIntegrationType();
                $agentArr['class_file_name'] = ucfirst(strtolower($agentService->getClassFileName()));
                $agentArr['insurance_charges'] = $agentService->getInsuranceCharges();
                $agentArr['reroute_charges'] = $agentService->getrerouteCharges();
                $agentArr['relabel_charges'] = $agentService->getRelabelCharges();
                $agentDataArr [] = $agentArr;
            }
            $agentDataArrJson['data'] = $agentDataArr;
            $agentDataArrJson['draw'] = $sEcho;
            $agentDataArrJson['recordsTotal'] = $iTotalRecords;
            $agentDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($agentDataArrJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "DELETEAGENTSERVICE") {

            $output = array();
            $serviceAgentMapid = $this->form_vars["serviceAgentMapid"];
            if ($serviceAgentMapid > 0) {
                $serviceAgentObj = new ServiceAgentMapping();
                $serviceAgentObj->deleteById($serviceAgentMapid);
                $output["status"] = "success";
                $output["message"] = formatMessages(SUCCESS_RECORD_DELETED, false); //"Record deleted successfully.";
            } else {
                $output["status"] = "fail";
                $output["message"] = formatMessages(ERROR_DELETE_AGENT_SERVICE_MAPPING, false); //"Unable to delete agent.";
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_user_doc') {
            $html = "";
            $agentId = $this->form_vars["agent_id"];
            $documentId = $this->form_vars["file_type"];
            $fileTypeText = $this->form_vars["file_type_text"];
            $addedBy = $user->getId();
            $path = "../_assets/agent_documents/" . $this->form_vars["agentCode"] . "/";
            if (!file_exists($path))
                @mkdir($path, 0775);
            if (isset($_FILES["user_doc_file"]) && trim($_FILES["user_doc_file"]["name"]) != '') {
                $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
                $temp = explode(".", $_FILES["user_doc_file"]["name"]);
                $extension = end($temp);
                if ((($_FILES["user_doc_file"]["type"] == "image/gif") || ($_FILES["user_doc_file"]["type"] == "image/jpeg") || ($_FILES["user_doc_file"]["type"] == "image/jpg") || ($_FILES["user_doc_file"]["type"] == "image/pjpeg") || ($_FILES["user_doc_file"]["type"] == "image/x-png") || ($_FILES["user_doc_file"]["type"] == "image/png" ) || ($_FILES["user_doc_file"]["type"] == "application/pdf" ) ) && in_array($extension, $allowedExts)) {
                    if ($_FILES["user_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["user_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["user_doc_file"]["name"]);
                        move_uploaded_file($_FILES["user_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        //Save User document Data
                        $agentDocument = new agentDocument();
                        $agentDocument->setAgentId($agentId);
                        $agentDocument->setDocumentId($documentId);
                        $agentDocument->setDocumentName($uploadUserDoc);
                        $agentDocument->setAddedBy($addedBy);
                        $agentDocument->setAddedDate(date("Y-m-d H:i:s"));
                        $agentDocument->save();
                        $html .= '<div class="col-md-3" id="usr_doc_' . $agentDocument->getId() . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" style="max-width: 100%; max-height: 100px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption text-center" style="height: auto">';
                        $html .= '<p>' . $fileTypeText . '</p>';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue btn-xs"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="' . $agentDocument->getId() . '"> Remove </a>';
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
            $agentId = $this->form_vars['agent_id'];
            $agentCode = $this->form_vars['agentCode'];
            if ($agentId > 0) {
                $agentDocument = new agentDocument($docId);
                @unlink("../_assets/agent_documents/" . $agentCode . "/" . $agentDocument->getDocumentName());
                $agentDocument->deleteById($docId);
            }
            echo "1";
            die;
        }
        
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_agent_email') {
            $agentId = $this->form_vars["agentid"];
            $serviceId = $this->form_vars["serviceid"];
            $emailList = $this->form_vars["emailList"];
            $emailListFinance = $this->form_vars["emailListFinance"];
            $Id = $this->form_vars["id"];
            
            if(trim($emailList) != '' || trim($emailListFinance) != '' ){
            $serviceAgentFilter = new ServiceAgentMappingDataFilter();
            $serviceAgentFilter->addAgentIDFilter($agentId);
            $serviceAgentFilter->addServiceIDFilter($serviceId);
            $serviceAgentList = $serviceAgentFilter->getList();
            if(count($serviceAgentList) > 0)
            {
                foreach($serviceAgentList as $salist)
                {
                    $salist->setEmail($emailList);
                    $salist->setEmailFinance($emailListFinance);
                    $salist->save();
                    
                }
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "Email added successfully.";
            }
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "There is no agent find for selected service. Please add service to agent first.";
            }
            }
            else{
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please enter email address.";
            }
        
            echo json_encode($output);
            die;
            
        }
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                case "save":
                    $user = SessionManager::getUser();
                    $error_array = array();
                    $code = $this->form_vars["code"];
                    //Check if agent code is already added then show error
                    if ($agentid <= 0) {
                        $agentDataRec = AgentDataFilter::getUniqueAgent($code);
                        if (count($agentDataRec)) {
                            $error_array[] = "[ " . $code . " ] agent code already exist"; //" Agent Code is already added. Please add new agent code";
                        }
                    }
                    $agentType = $this->form_vars["agentType"];
                    $id = $this->form_vars["id"];

//                    if($id == '' and $code != '')
//                    {
//                        $agentFilter = new AgentDataFilter();
//                        $agentFilter->addAgentCodeFilter($code);
//                        $agentFilter->addFieldFilter("agent_type", $agentType);
//                        $agentList = $agentFilter->getColumnList("id,agent_code");
//                        if(count($agentList) > 0)
//                        {
//                            $error_array[] = $code . formatMessages(ERROR_AGENTCODE_REPEATED); //" Agent Code is already added. Please add new agent code";
//                        }
//                    }
                    if ($id > 0) {
                        $oldAgentObj = new AgentData($this->form_vars["id"]);
                        $CouSavObj = new AgentData($this->form_vars["id"]);
                    } else {

                        $CouSavObj = new AgentData();
                        $oldAgentObj = $CouSavObj;
                    }
                    $uploadName = "";

                    if (isset($_FILES["logo"]) && trim($_FILES["logo"]["name"]) != '') {
                        $allowedExts = array("gif", "jpeg", "jpg", "png");
                        $temp = explode(".", $_FILES["logo"]["name"]);
                        $extension = end($temp);

                        if ((($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                            if ($_FILES["logo"]["error"] > 0) {
                                $error_array[] = formatMessages(ERROR_RETURN_CODE) . $_FILES["logo"]["error"] . "<br>";
                            } else {
                                $uploadName = str_replace(' ', '-', strtolower($code)) . "_" . time() . "." . $extension;
                                move_uploaded_file($_FILES["logo"]["tmp_name"], "../images/agentlogo/" . $uploadName);
                                $thumb = new easyphpthumbnail;

                                $thumb->Thumblocation = '../images/agentlogo/thumbnail/';
                                $thumb->Thumbprefix = 'owe_';
                                $thumb->Thumbsaveas = 'png';
                                $thumb->Maketransparent = array(1, 0, '#FF0000', 0);
                                $thumb->Keeptransparency = true;
                                $thumb->Thumbfilename = $uploadName;
                                //$thumb -> Clipcorner = array(2,15,0,0,1,1,0);

                                $thumb->Thumbsize = 16;
                                $thumb->Thumbprefix = 'owe_16_';
                                $thumb->Createthumb("../images/agentlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 50;
                                $thumb->Thumbprefix = 'owe_50_';
                                $thumb->Createthumb("../images/agentlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 100;
                                $thumb->Thumbprefix = 'owe_100_';
                                $thumb->Createthumb("../images/agentlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 200;
                                $thumb->Thumbprefix = 'owe_200_';
                                $thumb->Createthumb("../images/agentlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 300;
                                $thumb->Thumbprefix = 'owe_300_';
                                $thumb->Createthumb("../images/agentlogo/" . $uploadName, 'file');
                            }
                        } else {
                            $error_array[] = formatMessages(ERROR_INVALID_FILE); //"Invalid file";
                        }
                    }
                    $CouSavObj->setAgentType($agentType);
                    $CouSavObj->setAgentCode($code);
                    $CouSavObj->setAgentName($this->form_vars["agent_name"]);
                    $isActive = 0;
                    if (isset($this->form_vars["active"]))
                        $isActive = 1;
                    $CouSavObj->setActive($isActive);

                    $CouSavObj->setContactName($this->form_vars["contact"]);
                    $CouSavObj->setAddressLine1($this->form_vars["add_line_1"]);
                    $CouSavObj->setAddressLine2($this->form_vars["add_line_2"]);
                    $CouSavObj->setAddressLine3($this->form_vars["add_line_3"]);
                    $CouSavObj->setCountryId($this->form_vars["country"]);
                    $CouSavObj->setCity($this->form_vars["city"]);
                    $CouSavObj->setPostcode($this->form_vars["postcode"]);
                    $CouSavObj->setTelephone($this->form_vars["telephone"]);
                    $CouSavObj->setMobile($this->form_vars["mobile"]);
                    $CouSavObj->setFax($this->form_vars["fax"]);
                    $CouSavObj->setEmail($this->form_vars["emailadd"]);

                    $CouSavObj->setAlternativeContact1($this->form_vars["contact_1"]);
                    $CouSavObj->setAlternative1Telephone($this->form_vars["telephone_1"]);
                    $CouSavObj->setAlternative1Mobile($this->form_vars["mobile_1"]);
                    $CouSavObj->setAlternative1Fax($this->form_vars["fax_1"]);
                    $CouSavObj->setAlternative1Email($this->form_vars["email_1"]);
                    $CouSavObj->setAlternativeContact2($this->form_vars["contact_2"]);
                    $CouSavObj->setAlternative2Telephone($this->form_vars["telephone_2"]);
                    $CouSavObj->setAlternative2Mobile($this->form_vars["mobile_2"]);
                    $CouSavObj->setAlternative2Fax($this->form_vars["fax_2"]);
                    if(!filter_var($this->form_vars['email_2'], FILTER_VALIDATE_EMAIL)) {
                        $email2 = $this->form_vars['email_2'];
                    } else {
                        $email2 = '';
                    }
                    $CouSavObj->setAlternative2Email($email2);
//                  Also add into address book table
                    if (isset($this->form_vars['chkSaveContact'])) {
                        $address = new Address();
                        $address->setContact($this->form_vars["contact"]);
                        $address->setAddressLine1($this->form_vars["add_line_1"]);
                        $address->setAddressLine2($this->form_vars["add_line_2"]);
                        $address->setAddressLine3($this->form_vars["add_line_3"]);
                        $address->setCountry($this->form_vars["country"]);
                        $address->setCity($this->form_vars["city"]);
                        $address->setPostcode($this->form_vars["postcode"]);
                        $address->setPhoneNumber($this->form_vars["telephone"]);
                        $address->setUserId($user->getId());
//                    $address->setFax($this->form_vars["fax"]);
//                    $address->setEmail($this->form_vars["emailadd"]);
                        $address->save();
                    }


                    if ($uploadName != '')
                        $CouSavObj->setLogo($uploadName);
                    $CouSavObj->setRemarks($this->form_vars["remark"]);
                    $CouSavObj->setUserId($user->getId());
                    $CouSavObj->setDateCreated(date("Y-m-d"));
                    //$CouSavObj->setDefaultAgent($this->default_agent);
                    if (count($error_array) <= 0) {
                        $CouSavObj->save();
                        $agentfieldChangeArray = $CouSavObj->getLogArray();
                        $this->form_vars["id"] = $CouSavObj->getId();
                        $agent_id_log = $CouSavObj->getId();
                        $newAgentObj = $CouSavObj;
                        $this->counting_error = count($error_array);
                        $error_list = ErrorList::getItem();

                        $agentLog = new AgentLog();
                        if ((int) $id <= 0) {
                            $agentLog->createlog($user->getId(), '', $agent_id_log, 'AGENT', $user->getUserName() . ' has added new agent ' . $code, '', $newAgentObj);
                        } else {
                            // $logArray = array_merge($agentfieldChangeArray, $agentMappingChangeArray);
                            if (sizeof($agentfieldChangeArray) > 1) {
                                $logmessage = implode(", ", $agentfieldChangeArray) . " fields are updated";
                            } else {
                                $logmessage = $user->getAccount() . " updated agent";
                            }

                            $oldAgentData = serialize($oldAgentObj);
                            $newAgentData = serialize($newAgentObj);
                            $agentLog->createlog($user->getId(), '', $agent_id_log, 'AGENT', $logmessage, $oldAgentData, $newAgentData);
                        }

                        util_redirect("../main/agent_details.php?id=" . $CouSavObj->getId() . "&success=1");
                    } else {
                        $this->counting_error = count($error_array);
                        $error_list = ErrorList::getItem();
                        $error_list->addErrorList($error_array);
                    }

                    break;
                case "save_agent_sevice":

                    if (is_null($this->form_vars["service_id"]) || empty($this->form_vars["service_id"])) {
                        $res['status'] = "error";
                        $res['message'] = "Please all fill required fields to continue.";
                        echo json_encode($res);
                        die();
                        break;
                    }

                    $agentId = $this->form_vars["agent_id"];
                    $service_id = $this->form_vars["service_id"];
                    $serviceObject = new Services($service_id);
                    $carrier_id = $serviceObject->getCarrierId();
                    if ($serviceObject->getid() <= 0) {
                        $res['status'] = "error";
                        $res['message'] = "Please all fill required fields to continue.";
                        echo json_encode($res);
                        die();
                        break;
                    }

                    if ($this->form_vars["httpVerb"] == "update") {
                        $agentServiceId = (int) $this->form_vars["agentServiceId"];
                        $serviceAgentObj = new ServiceAgentMapping($agentServiceId);
                    } else {
                        $serviceAgentObj = new ServiceAgentMapping();
                    }

                    $serviceAgentObj->setServiceid($service_id);
                    $serviceAgentObj->setagentid($agentId);
                    //  $serviceAgentObj->setAccountNumber($this->form_vars["account_number"][$index]);
                    //  $serviceAgentObj->setAccountNumber($this->form_vars["account_number"][$index]);
                    $serviceAgentObj->setApiUrl($this->form_vars["api_url"]);
                    $serviceAgentObj->setApiUsername($this->form_vars["api_username"]);
                    $serviceAgentObj->setApiPassword($this->form_vars["api_password"]);
                    $serviceAgentObj->setFtpHost($this->form_vars["ftp_host"]);
                    $serviceAgentObj->setFtpUsername($this->form_vars["ftp_username"]);
                    $serviceAgentObj->setFtpPassword($this->form_vars["ftp_password"]);
                    $serviceAgentObj->setIntegrationType($this->form_vars["integration"]);
                    $serviceAgentObj->setClassFileName($this->form_vars["class_file_name"]);
                    $serviceAgentObj->setToWeight($this->form_vars["to_weight"]);
                    $serviceAgentObj->setFromWeight($this->form_vars["from_weight"]);
                    $serviceAgentObj->setInsuranceCharges($this->form_vars["insurance_charges"]);
                    $serviceAgentObj->setInsuranceCover($this->form_vars["insurance_cover"]);
                    $serviceAgentObj->setRerouteCharges($this->form_vars["reroute_charges"]);
                    $serviceAgentObj->setOversizeCharges($this->form_vars["oversize_charges"]);
                    $serviceAgentObj->setAddressChangeCharges($this->form_vars["address_change_charges"]);
                    $serviceAgentObj->setOtherSurcharges($this->form_vars["other_surcharges"]);
                    $serviceAgentObj->setReturnCharges($this->form_vars["return_charges"]);
                    $serviceAgentObj->setRelabelCharges($this->form_vars["relabel_charges"]);
                    $serviceAgentObj->setWrongAddressCharges($this->form_vars["wrong_address_charges"]);
                    $serviceAgentObj->save();

                    $restrictedPostcode = $this->form_vars["restricted_postcode"];
                    $restrictedCity = $this->form_vars["restricted_city"];

                    $agentRestrictedPostcode = new AgentRestrictedPostcodeFilter();
                    $agentRestrictedPostcode->addFieldFilter("service_id", $service_id);
                    $agentRestrictedPostcode->addFieldFilter("agent_id", $agentId);
                    $agentRestrictedPostcode->delete();

                    $subqueryArray = array();
                    if (trim($restrictedPostcode) != "") {
                        $restrictedPostcodeArray = explode(",", $restrictedPostcode);
                        if (count($restrictedPostcodeArray) > 0) {
                            foreach ($restrictedPostcodeArray as $rpostcode) {
                                $subqueryArray[] = "('" . $agentId . "', '" . $service_id . "', '" . $rpostcode . "', 0)";
                            }
                        }
                    }
                    if (trim($restrictedCity) != "") {
                        $restrictedCityArray = explode(",", $restrictedCity);
                        if (count($restrictedCityArray) > 0) {
                            foreach ($restrictedCityArray as $rCity) {
                                $subqueryArray[] = "('" . $agentId . "', '" . $service_id . "', '" . $rCity . "', 1)";
                            }
                        }
                    }
                    if (count($subqueryArray) > 0) {
                        $InsertQuery = "INSERT INTO agent_restricted_postcode(agent_id,service_id,postcode_city,is_city) VALUES ";
                        $AgentRestrictedPostcodeFilter = new AgentRestrictedPostcodeFilter();
                        $queryJoin = $InsertQuery . implode(',', $subqueryArray);
                        $AgentRestrictedPostcodeFilter->insertData($queryJoin);
                    }

                    //FETCH CONSTANT FOR THAT CARRIER
                    $serviceConstantObj = new ServiceConstantFilter();
                    $serviceConstantObj->addFilter("carrier_id = '" . $carrier_id . "' or carrier_id = 0");
                    $serviceConstantObj->AddOrderBy("sort_order");
                    $serviceConstantList = $serviceConstantObj->getList();

                    if (count($serviceConstantList) > 0) {
                        foreach ($serviceConstantList as $constantData) {
                            $constant_id = $constantData->getId();
                            $constant_key = $constantData->getConstant();
                            $constant_value = $this->form_vars[$constant_key];
                            $service_constant_values_filter = new ServiceConstantValueFilter();
                            $service_constant_values_filter->addFieldFilter('constant_id', $constant_id);
                            $service_constant_values_filter->addFieldFilter('agent_id', $agentId);
                            $service_constant_values_filter->addFieldFilter('service_id', $service_id);
                            $service_constant_values_object = $service_constant_values_filter->getList();
                            if (count($service_constant_values_object) > 0)
                                $service_constant_values_data = $service_constant_values_object[0];
                            else
                                $service_constant_values_data = new ServiceConstantValue();

                            $service_constant_values_data->setConstantValue($constant_value);
                            $service_constant_values_data->setServiceId($service_id);
                            $service_constant_values_data->setAgentId($agentId);
                            $service_constant_values_data->setConstantId($constant_id);
                            if ($service_constant_values_data->getId() <= 0) {
                                $service_constant_values_data->setDateCreated(time());
                                $service_constant_values_data->setAddedBy($user->getId());
                            }
                            $service_constant_values_data->setDateUpdate(time());
                            $service_constant_values_data->setUpdatedBy($user->getId());
                            $service_constant_values_data->save();
                        }
                    }

                    $res['status'] = "success";
                    $res['message'] = "Agent Successfully Updated.";
                    echo json_encode($res);
                    die();
                    break;
            }
        } else {
            $id = util_get_num("id");
            if ($id > 0) {
                $AgentData = new AgentData($id);

                $this->form_vars["id"] = $id;
                $this->form_vars["agentType"] = $AgentData->getAgentType();
                $this->form_vars["code"] = $AgentData->getAgentCode();
                $this->form_vars["agent_name"] = $AgentData->getAgentName();
                $this->form_vars["active"] = $AgentData->getActive();
                $this->form_vars["contact"] = $AgentData->getContactName();
                $this->form_vars["add_line_1"] = $AgentData->getAddressLine1();
                $this->form_vars["add_line_2"] = $AgentData->getAddressLine2();
                $this->form_vars["add_line_3"] = $AgentData->getAddressLine3();
                $this->form_vars["country"] = $AgentData->getCountryId();
                $this->form_vars["city"] = $AgentData->getCity();
                $this->form_vars["postcode"] = $AgentData->getPostcode();
                $this->form_vars["telephone"] = $AgentData->getTelephone();
                $this->form_vars["mobile"] = $AgentData->getMobile();
                $this->form_vars["fax"] = $AgentData->getFax();
                $this->form_vars["emailadd"] = $AgentData->getEmail();
                $this->form_vars["contact_1"] = $AgentData->getAlternativeContact1();
                $this->form_vars["telephone_1"] = $AgentData->getAlternative1Telephone();
                $this->form_vars["mobile_1"] = $AgentData->getAlternative1Mobile();
                $this->form_vars["fax_1"] = $AgentData->getAlternative1Fax();
                $this->form_vars["email_1"] = $AgentData->getAlternative1Email();
                $this->form_vars["contact_2"] = $AgentData->getAlternativeContact2();
                $this->form_vars["telephone_2"] = $AgentData->getAlternative2Telephone();
                $this->form_vars["mobile_2"] = $AgentData->getAlternative2Mobile();
                $this->form_vars["fax_2"] = $AgentData->getAlternative2Fax();
                $this->form_vars["email_2"] = $AgentData->getAlternative2Email();
                $this->form_vars["remark"] = $AgentData->getRemarks();
                $this->form_vars["logo"] = $AgentData->getLogo();

                $this->agentList = "";
                if ($agentid > 0) {
                    $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingFilter->addFilter("agentid =" . intval($agentid));
                    $this->agentList = $serviceAgentMappingFilter->getList();
                }
            }
        }
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-user"></i>
        <?php
        if (util_get_num("id") > 0) {
            ?>
                            Edit Agent
            <?php
        } else {
            ?>
                            Add Agent
                            <?php
                        }
                        ?>
                    </div>
                    <div class="actions">
                        <a href="agent.php" class="btn blue"><span></span><i class="fa fa-list"></i>&nbsp;Agent List</a>
                    </div>
                </div>
                <div class="portlet-body">


                    <div class="<?php echo (!empty($this->counting_error) ) ? 'alert alert-danger' : ''; ?>">
        <?php errorList::getItem()->render(); ?>
                    </div>
        <?php
        if (util_get_num("success") == 1) {
            ?>
                        <div class="alert alert-success">
                            Agent updated successfully.
                        </div>
                        <?php
                    }
                    ?>
                    <div id="agent_info">
                        <div class="form-group">
                            <div class="caption margin-bottom-10 block">
                                Agent Information</div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent Type</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
        <?php
        $agentTypeArray = array("carrier" => "Carrier",
            "dispatch" => "Dispatch",
            "both" => "Both"
        );
        echo ddl::generateArrayDDL("agentType", $agentTypeArray, $agentType, "Select Agent Type", ' class="form-control select2" onChange=" displayContent();"', '', "agentType", "Select Agent Type", "1")
        ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent Code</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-key"></i></span>

                                            <div class="input-icon right">
                                                <i class="fa tooltips font-red" data-original-title="Agent Code is mandatory">*</i>
                                                <input title="Code" placeholder="Agent Code" class="form-control" name="code" id="code" type="text" value="<?php echo $code; ?>" required="required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent Name</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-user"></i></span>

                                            <div class="input-icon right">
                                                <i class="fa tooltips font-red" data-original-title="Agent Name is mandatory">*</i>
                                                <input class="form-control" name="agent_name" id="agent_name" type="text" value="<?php echo @$agent_name; ?>" placeholder="Agent Name" title="Agent Name" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
        <?php if (Permissions::checkFilePermission('agent_active')) { ?>
                                    <div class="col-sm-3">
                                        <div class="form-group">


                                            <label>Status</label>


                                            <input <?php echo( $active == 1 ? 'checked="checked"' : '' ); ?> name="active" type="checkbox" class="make-switch" data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger" data-size="mini">

                                        </div>
                                    </div>
        <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent Logo</label>
                                        <input type="hidden" id="logo" name="logo" value="<?php echo $logo; ?>">

                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="logo" id="logo" accept="image/*"/>
                                                </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists fileinput-exists-remove" data-dismiss="fileinput"> Remove </a>

                                            </div>
                                            <div class="clearfix margin-top-10"><span class="label label-primary"><small>NOTE!</span> Recommended logo dimensions (254 x 62) </small>
                                            </div>
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100px; height: 80px;">
        <?php
        if (trim($logo) != '') {
            echo '<img src="../images/agentlogo/thumbnail/owe_100_' . $logo . '" >';
        } else {
            echo '<img src = "../images/No-image-found.jpg">';
        }
        ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--                                <div class="col-md-3">
                                     <div class="form-group">
                                       <label for="default_agent">Default Agent</label>
                                       <br />
        <?php
        // if (!isset($this->default_agent)) $this->default_agent = "";
        // $selected = (($this->default_agent == "1") ? "checked": "");
        ?>
                                         <input id="default_agent" name="default_agent" type="checkbox" class="form_ckeckbox" <?php // echo $selected;   ?> value="1"/>
                                                         <span class="form_ckeckbox_text">Yes/No</span>
                                      </div>
                                </div>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="caption margin-bottom-10 block">
                                Contact Details</div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contact Name</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-user"></i></span>
                                            <input class="form-control" name="contact" id="contact" type="text" value="<?php echo $contact; ?>" title="Contact Name" placeholder="Contact Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Address Line 1</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i></span>
                                            <input class="form-control" name="add_line_1" id="add_line_1" type="text" value="<?php echo @$add_line_1; ?>" title="Address Line 1" placeholder="Address Line 1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Address Line 2</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker "></i></span>
                                            <input class="form-control" type="text" id="add_line_2" name="add_line_2" value="<?php echo @$add_line_2; ?>" title="Address Line 2" placeholder="Address Line 2"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Address Line 3</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker "></i></span>
                                            <input class="form-control" id="add_line_3" name="add_line_3" type="text" value="<?php echo @$add_line_3; ?>" title="Address Line 3" placeholder="Address Line 3">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City</label>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i></span>
                                            <input class="form-control" name="city" id="city" type="text" value="<?php echo $city; ?>" title="City" placeholder="City">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Country</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-map-marker "></i></div>
        <?php
        echo Ddl::generateCountryDDL('country', $country, 'iso');
        ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Postcode</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-map-marker"></i></div>
                                            <input class="form-control" id="postcode" name="postcode" type="text" value="<?php echo @$postcode; ?>" title="PostCode" placeholder="PostCode">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                            <input class="form-control" id="emailadd" name="emailadd" type="text" value="<?php echo @$emailadd; ?>" title="Email" placeholder="Email">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Telephone</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="telephone" id="telephone" type="text" value="<?php echo $telephone; ?>" title="Telephone" placeholder="Telephone">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mobile</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="mobile" id="mobile" type="text" value="<?php echo @$mobile; ?>" title="Mobile" placeholder="Mobile">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">


                                <!--								<div class="col-md-3">-->
                                <!--									<div class="form-group">-->
                                <!--										<label>Fax</label>-->
                                <!---->
                                <!--										<div class="input-group input-group-sm">-->
                                <!--											<div class="input-group-addon"><i class="fa fa-fax"></i></div>-->
                                <!--											<input class="form-control" name="fax" id="fax" type="text" value="--><?php //echo @$fax;  ?><!--" title="Fax" placeholder="Fax">-->
                                <!--										</div>-->
                                <!--									</div>-->
                                <!--								</div>-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input name="chkSaveContact" type="checkbox" class="icheck " id="chkSaveContact"  value="1" data-checkbox="icheckbox_square-blue">
                                                    save this contact
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                    <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                <select id="currency" name="currency" class="form-control" title="Currency" >
                                <option value="">Select Currency</option>
        <?php
        //                                $CurrencyFilter = new CurrencyFilter();
        //								$CurrencyFilter->addFieldFilter("isactive","1");
        //								$currecnylist = $CurrencyFilter->getColumnList('rightsymbol');
        //								foreach($currecnylist as $currency)
        //								{
        //									if($currency->getRightSymbol() == $this->currency)
        //										echo '<option value="'.$currency->getRightSymbol().'" selected="selected">'.$currency->getRightSymbol().'</option>';
        //									else
        //										echo '<option value="'.$currency->getRightSymbol().'" >'.$currency->getRightSymbol().'</option>';
        //								}
        ?>
                                </select>
                              </div>
                        </div>

                         <div class="col-md-3">
                            <div class="form-group">
                                 <select name="carrier" id="carrier" rel="tooltip" data-original-title="Courier"  data-live-search="true" placeholder="Courier" class="selectpicker">
                                  <option value="">Carrier Name</option>
        <?php
        //                                                        if (count($this->carrier_list) > 0) {
        //                                                            foreach ($this->carrier_list as $carrierObj) {
        //                                                                $carrier = trim($carrierObj->getCarrier());
        //                                                                echo '<option value="' . $carrier . '"' . ($carrier == $this->carrier ? ' selected="selected"' : '') . '>' . $carrier . '</option>';
        //                                                            }
        //                                                        }
        ?>
                                </select>
                            </div>
                         </div>
                          <div class="col-md-3">
                            <div class="form-group">
                                 <select name="service[]" id="service" multiple="multiple" class="form-control">
                                  <option value="">Service Name</option>
        <?php
        //											if (count($this->services_list) > 0) {
        //
        //												foreach ($this->services_list as $serviceObj) {
        //													$selected = in_array($serviceObj->getId(), $this->service );
        //													$selected = $selected ? ' selected="selected"' : '';
        //													$serviceId = trim($serviceObj->getId());
        //													$serviceCode = trim($serviceObj->getCode());
        //													$serviceName = trim($serviceObj->getName());
        //													$carrier = trim($serviceObj->getCarrier());
        //													echo '<option value="' . $serviceId . '" class="' . $carrier . '"' . ($selected) . '>' . $serviceName . '</option>';
        //												}
        //											}
        ?>
                                </select>
                            </div>
                          </div>


                    </div>-->

                    <div id="dispatch_agent_detail" <?= $agentType == 'dispatch' ? ' class=""' : ' class="display-none"'; ?>>
                        <div class="form-group">
                            <div class="caption margin-bottom-10 block">
                               Dispatch Agent Alternative Contact Details
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Name 1</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input class="form-control" name="contact_1" id="contact_1" type="text" value="<?php echo $contact_1; ?>" title="Contact" placeholder="Contact">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Name 2</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input class="form-control" name="contact_2" id="contact_2" type="text" value="<?php echo @$contact_2; ?>" placeholder="Contact" title="Contact">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telephone 1</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="telephone_1" id="telephone_1" type="text" value="<?php echo $telephone_1; ?>" placeholder="Telephone" title="Telephone">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telephone 2</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="telephone_2" id="telephone_2" type="text" value="<?php echo @$telephone_2; ?>" placeholder="Telephone" title="Telephone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile 1</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="mobile_1" id="mobile_1" type="text" value="<?php echo $mobile_1; ?>" placeholder="Mobile" title="Mobile">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile 2</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                            <input class="form-control" name="mobile_2" id="mobile_2" type="text" value="<?php echo @$mobile_2; ?>" placeholder="Mobile" title="Mobile">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fax 1</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-fax"></i></div>
                                            <input class="form-control" name="fax_1" id="fax_1" type="text" value="<?php echo $fax_1; ?>" placeholder="Fax" title="Fax">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fax 2</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-fax"></i></div>
                                            <input class="form-control" name="fax_2" id="fax_2" type="text" value="<?php echo @$fax_2; ?>" placeholder="Fax" title="Fax">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email 1</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                            <input class="form-control" name="email_1" id="email_1" type="text" value="<?php echo $email_1; ?>" placeholder="Email" title="Email">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email 2</label>

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                            <input class="form-control" name="email_2" id="email_2" type="text" value="<?php echo (!empty($email_2) && filter_var($email_2, FILTER_VALIDATE_EMAIL)) ? $email_2 : ''; ?>" placeholder="Email" title="Email">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Remark (Notes and important relevant Information): </label>
                                <textarea class="form-control" name="remark" id="remark" rows='4' cols="50"><?php echo @$remark; ?></textarea>
                            </div>
                        </div>

                    </div>
        <?php if ($id > 0) { ?>
                        <div class="form-group">
                            <div class="caption margin-bottom-10 block">
                                Agent Documents Copy</div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Document Type</label>

                                        <div class="first_form_col">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                                <!-- User Document -->
            <?php
            echo Ddl::generateDDL('document_name', 'DocumentTypeFilter', " is_active = '1' AND is_delete = '0' AND document_type = 'agent_contract'", 'document_name', 'id', '', ' class="bs-select form-control"  data-show-subtext="true" data-toggle="tooltip"  title="User Document Type" data-original-title="User Document Type"', '', 'Select document type', 'document_name', 'User Document Type', '', '');
            ?>
                                                <span class="input-group-addon red-18">*</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    <input type="file" name="file_name" id="file_name"> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                <a href="javascript:;" class="input-group-addon btn blue" id="upload_file" data-original-title="" title="">Upload</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--								<div class="col-md-1">-->
                                <!--									<div class="form-group">-->
                                <!--										<label></label>-->
                                <!---->
                                <!--										<div class="first_form_col">-->
                                <!--											<div class="input-group input-group-sm">-->
                                <!--												<a href="javascript:;" class="btn btn-info" id="upload_file" data-original-title="" title="">-->
                                <!--													<i class="fa fa-plus"></i> Upload </a>-->
                                <!--											</div>-->
                                <!--										</div>-->
                                <!--									</div>-->
                                <!--								</div>-->
                            </div>
                            <div class="row" id="append_user_doc">
            <?php
            if (@$id > 0) {
                $agentDocumentFilter = new agentDocumentFilter();
                $agentDocumentLists = $agentDocumentFilter->getAgentDoc($id);
                foreach ($agentDocumentLists as $agentDocumentList) {
                    $temp = explode(".", $agentDocumentList->getDocumentName());
                    $extension = end($temp);
                    $fileFullPath = "../_assets/agent_documents/" . $code . "/" . $agentDocumentList->getDocumentName();
                    if (!file_exists($fileFullPath) && $extension != "pdf") {
                        $fileFullPath = "../images/No-image-found.jpg";
                    }
                    if ($extension == "pdf") {
                        $fileFullPath = "../images/pdf.png";
                    }
                    ?>
                                        <div class="col-md-3" id="usr_doc_<?php echo $agentDocumentList->getId(); ?>">
                                            <div class="thumbnail">
                                                <img src="<?php echo $fileFullPath ?>" alt="<?php echo $agentDocumentList->getDocumentName(); ?>" style="max-width: 100%; max-height: 100px; display: block;" data-src="<?php echo $fileFullPath ?>">
                                                <div class="caption text-center" style="height: auto">
                                                    <p><?php echo $agentDocumentList->getDocumentId(); ?></p>
                                                    <a target="_blank" href="../_assets/agent_documents/<?php echo $code . "/" . $agentDocumentList->getDocumentName(); ?>" class="btn blue btn-xs"> View </a>
                                                    <a href="javascript:;" class="btn red remove_doc btn-xs" data-doc_id="<?php echo $agentDocumentList->getId(); ?>"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                    <?php
                }
            }
            ?>
                            </div>
                        </div>
                            <?php } ?>
                    <div style="clear:both;"></div>
                    <div class="col-md-12 text-center">
                        <a id="btnSave" href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>
                            <?php
                            if (Permissions::checkFilePermission('agent_audit') && $id > 0) {
                                ?>
                            <a data-title="Agent" data-table="agent" data-container="audit_content" data-ajax_url="agent_details.php" data-id="<?= $id ?>" id="btnAudit" href="javascript:;" class="btn btn-primary show_audit" title="audit" data-target="#audit-log" data-toggle="modal"> Audit </a>

        <?php } ?>
                        <a href="agent.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        <input type="hidden" name="form_action" id="form_action" value=""/>
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>
                    </div>
                    <div style="clear:both;"><br/></div>
                </div>
            </div>
        </form>
        <?php if ($id > 0) { ?>
            <div class="portlet light">
                <div id="smartloader">
                    <div class="cssload-preloader">
                        <div class="cssload-preloader-box">		<div>U</div>		<div>p</div>		<div>d</div>		<div>a</div>		<div>t</div>		<div>i</div>		<div>n</div>		<div>g</div></div>
                    </div>
                </div>
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-user"></i> Agent Service Details
                    </div>
                    <div class="actions">
                        <!--						<a href="agent.php" class="btn blue"><span></span><i class="fa fa-plus"></i>&nbsp;Add Service Details</a>-->
                        <a data-title='Agent' data-agentid="<?php echo util_get_num("id"); ?>" data-container='agent_service_content' data-ajax_url='agent_details.php' data-id=". $agentService->getId() ." id='btnAudit' href='javascript:;' class='btn blue show_agent_service' title='Agent Service' data-target='#edit-agent-service' data-toggle='modal'><i class="fa fa-plus"></i>&nbsp;Add Service Details</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="col-md-12 display-none" id="display_message"></div>
                    <div>
                        <table class="table table-striped table-bordered table-hover table-condensed" id="manage-agent-service-table">
                            <thead>
                                <tr role="row" class="heading ">
                                    <th style="min-width: 70px">Action</th>
                                    <th>Services</th>
                                    <th>Integration Type</th>
                                    <th>Service Provider File</th>
                                    <th>Insurance Charges</th>
                                    <th>Reroute Charges</th>
                                    <th>Relabel Charges</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5" style="display: inline-block">
                                            <button class="btn btn-xs blue filter-submit btn-outline margin-bottom-5">
                                                <i class="fa fa-search"></i></button>
                                            <button class="btn btn-xs red filter-cancel btn-outline  margin-bottom-5">
                                                <i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td>
            <?php
            echo Ddl::generateServiceDDLWithImage('search_service_id', '', 'id', ' class="form-filter form-control input-sm select2 searchbox select2-hidden-accessible " data-live-search="true"  data-show-subtext="true" data-container="body"', '', '', 'name');
            ?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_integration_type">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_class_file_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_insurance_charges">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_reroute_charges">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_reliable_charges">
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- AUDIT MODEL -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="edit-agent-service" aria-labelledby="myLargeModalLabel">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title"><span></span> Service Details</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12 display-none" id="display_modal_message"></div>
                                            <div id="agent_service_content"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" id="saveAgentService" class="btn green">Save</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>


                    <!-- ADD EMAIL MODEL -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="addAgentEmail" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-m">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title"><span></span> <div id="emailheader"></div></h4>
                                </div>
                                <form name="addEmailForm" id="rangeForm" action="" method="POST">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12 display-none" id="emaildisplay_message"></div>
                                        </div>
                                        <div class="row range_form">
                                            <div class="col-md-12">
                                                
                                                    <div class="form-group">
                                                        <label>Add Email For Operation Department</label>
                                                        <div class="input-group input-group-sm input-icon right" >
                                                            <span class="input-group-addon"> <i class="fa fa-envelope"></i> </span>
                                                            <textarea  name="add_email" id="add_email"  value="" rows="5" cols="200" class="form-control" style ="width: 500px;" title="Add Email" placeholder="Add Email (Add multiple by comma seprated)" rel="tooltip" data-original-title="Add Email" required></textarea>
                                                        </div>
                                                    </div>
                                                
                                            </div>
                                            <div class="col-md-12">
                                                
                                                    <div class="form-group">
                                                        <label>Add Email For Accounts Department</label>
                                                        <div class="input-group input-group-sm input-icon right" >
                                                            <span class="input-group-addon"> <i class="fa fa-envelope"></i> </span>
                                                            <textarea  name="add_email_finance" id="add_email_finance"  value="" rows="5" cols="200" class="form-control" style ="width: 500px;" title="Add Email" placeholder="Add Email (Add multiple by comma seprated)" rel="tooltip" data-original-title="Add Email" required></textarea>
                                                        </div>
                                                    </div>
                                                
                                            </div>
                                        </div>
                                        <input type="hidden" name="agent_id" id="agent_id" value="<?php ?>"/>  
                                        <input type="hidden" name="service_id" id="service_id" value="<?php ?>"/>
                                        <input type="hidden" name="id" id="id" value="<?php ?>"/>
                                        <input type="hidden" name="form_action" id="form_action" value="saveagentemail" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input id="btnSaveEmail" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
            </div>
            <div class="modal fade" id="serviceScanModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Update Ranges</h4>
                        </div>
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#barcoderanges" data-toggle="tab">Bar Code Ranges</a>
                                </li>
                                <li>
                                    <a href="#barcodelist" data-toggle="tab">Bar Code List</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="barcoderanges">
                                    <form name="rangeForm" id="rangeForm" action="" method="POST">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12" id="success_msg">
                                                </div>
                                            </div>
                                            <div class="row range_form">
                                                <div class="col-md-12">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Range Name</label>
                                                            <div class="input-group input-group-sm input-icon right" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="range_name" id="range_name" value="" size="50" class="form-control" title="Range Name" maxlength="35" placeholder="Range Name" rel="tooltip" data-original-title="Range Name" type="text" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Range Start</label>
                                                            <div class="input-group input-group-sm input-icon right" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="start_range" id="start_range" value="" size="50" class="form-control" title="Range Start" maxlength="35" placeholder="Range Start" rel="tooltip" data-original-title="Range Start" type="text" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Range End</label>
                                                            <div class="input-group input-group-sm" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="end_range" id="end_range" value="" size="50" class="form-control" title="Range End" maxlength="35" placeholder="Range End" rel="tooltip" data-original-title="Range End" type="text" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Next Range</label>
                                                            <div class="input-group input-group-sm" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="next_number" id="next_number" value="" size="50" class="form-control" title="Next Range" maxlength="35" placeholder="Next Range" rel="tooltip" data-original-title="Next Range" type="text" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Prefix</label>
                                                            <div class="input-group input-group-sm input-icon right" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="prefix" id="prefix" value="" size="50" class="form-control" title="Prefix" maxlength="35" placeholder="Prefix" rel="tooltip" data-original-title="Prefix" type="text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Suffix</label>
                                                            <div class="input-group input-group-sm" >
                                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                <input name="suffix" id="suffix" value="" size="50" class="form-control" title="Suffix" maxlength="35" placeholder="Suffix" rel="tooltip" data-original-title="Suffix" type="text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 ">
                                                        <div class="form-group">
                                                            <label>Country wise Range</label><br />
                                                            <input name="countryrange"  id= 'countryrange' type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No"  data-on-color="primary" data-off-color="danger">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" id="id_range" value="<?php ?>"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                                            <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
                                            <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="barcodelist">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet light bordered">
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Agent Name</th>
                                                                <th>Service Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbodybarcode">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }//if agent id exist- update service details
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <link href="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-editors.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>

        <script type="text/javascript">
                                        var DataTableAgentService = function () {
                                            var handleAgentServiceDataTable = function () {
                                                var grid = new Datatable();
                                                grid.init({
                                                    src: $("#manage-agent-service-table"),
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
                                                            "url": "agent_details.php?action=agent_ajax&agent_id=<?php echo util_get_num("id") ?>", // ajax source
                                                            headers: {

                                                            },
                                                        },
                                                        "bStateSave": true,
                                                        "columns": [
                                                            {"data": "option", "bSortable": false},
                                                            {"data": "service_name"},
                                                            {"data": "integration_type"},
                                                            {"data": "class_file_name"},
                                                            {"data": "insurance_charges"},
                                                            {"data": "reroute_charges"},
                                                            {"data": "relabel_charges"}
                                                        ]
                                                    }
                                                });
                                            }
                                            return {
                                                //main function to initiate the module
                                                init: function () {
                                                    handleAgentServiceDataTable();
                                                }
                                            };
                                        }();


                                        $(document).ready(function () {

                                            $(document).on('click', '#btnExportData', function () {
                                                $('#export-product-popup').modal('show');
                                                $(".progress-bar-info").attr("aria-valuenow", "10");
                                                $('.progress-bar-info').css('width', '10%');
                                                $('#download-csv').hide();
                                                var form_data = new FormData();
                                                form_data.append('action', 'export_agent');
                                                $.ajax({
                                                    url: "agent_details.php",
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
                                                        }
                                                    }
                                                });

                                            });
                                            $(document).on('click', '.btndelete', function () {
                                                var e = $(this);
                                                swal({
                                                    title: "Are you sure you want to delete?",
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

                                                                var serviceAgentMapid = e.data('id');
                                                                $.ajax({
                                                                    method: "POST",
                                                                    url: "agent_details.php",
                                                                    data: {serviceAgentMapid: serviceAgentMapid, action: "DELETEAGENTSERVICE"}
                                                                }).done(function (data) {
                                                                    var t = JSON.parse(data);
                                                                    if (t.status == "success")
                                                                    {
                                                                        e.parents('tr').hide();
                                                                        $('#display_message').show();
                                                                        $('#display_message').addClass('alert alert-success');
                                                                        $('#display_message').html(t.message);
                                                                        $('.filter-cancel').click();
                                                                    }
                                                                });
                                                            } else
                                                            {
                                                                return false;
                                                            }
                                                        });
                                            });

                                            DataTableAgentService.init();

                                            $(document).on("click", ".show_add_email", function () {
                                                var ajax_url = $(this).data('ajax_url');
                                                var id = $(this).data('id');
                                                var serviceId = $(this).data('serviceid');
                                                var agentId = $(this).data('agentid');
                                                var servicename = $(this).data('servicename');
                                                var email = $(this).data('email');
                                                var email_finance = $(this).data('email_finance');
                                                //alert("test");
                                               // alert(agentId);
                                                $('#emailheader').html("Add Carrier Email for " + servicename);
                                                $('#agent_id').val(agentId);
                                                $('#service_id').val(serviceId);
                                                $('#add_email').val(email);
                                                $('#add_email_finance').val(email_finance);

                                            });

                                            $(document).on("click", ".show_agent_service", function () {
                                                $('#smartloader').show();
                                                $('#display_modal_message').hide();
                                                var ajax_url = $(this).data('ajax_url');
                                                //				        alert('modal inside');
                                                if ($.trim(ajax_url) == '')
                                                    ajax_url = 'index.php';
                                                var id = $(this).data('id');
                                                var title = $(this).data('title');
                                                var ajaxUrl = $(this).data('ajax_url');
                                                var agentId = $(this).data('agentid');
                                                //				        alert(agent_id);
                                                var modal_continer = $(this).data('container');
                                                $("#edit-agent-service h4.modal-title span").html(title);
                                                $.post(ajax_url, {func: 'get_agent_services_form', id: id, agentId: agentId, ajaxUrl: ajaxUrl}, function (data) {
                                                    $("#" + modal_continer).html(data);
                                                    $('.bs-select').selectpicker();


                                                    $("#service_id").change(function () {
                                                        if ($("#service_id").val() == "")
                                                        {
                                                            alert("Please select service, then choose integration");
                                                            $("#integration").val("");
                                                        } else
                                                        {
                                                            var serviceid = $("#service_id").val();
                                                            //alert(agentId +" "+ serviceid);

                                                            /*
                                                             *   GET FIELDS FROM DATABASE
                                                             */

                                                            $('#integration_form_data').html("");
                                                            $.ajax({
                                                                method: "POST",
                                                                url: "agent_details.php",
                                                                data: {action: "get_form_fields_for_service", agent_id: agentId, service_id: serviceid},
                                                                dataType: "json"
                                                            }).done(function (data) {
                                                                //alert(data.STATUS);
                                                                if (data.STATUS) {
                                                                    $("#integration_form_data").html(data.MESSAGE);
                                                                } else {
                                                                    $("#integration_form_data").html(data.MESSAGE);
                                                                }
                                                                $("#integration_form_data").show();
                                                                $("#integration_form_data").show();
                                                                $(".API").hide();
                                                                $(".EDI").hide();
                                                                $(".SOFTWARE").hide();
                                                                //  alert($("#INTEGRATION_TYPE").val());
                                                                $("#INTEGRATION_TYPE").change(function () {
                                                                    $(".API").hide();
                                                                    $(".EDI").hide();
                                                                    $(".SOFTWARE").hide();
                                                                    var integrationvar = $("#INTEGRATION_TYPE").val();
                                                                    if ($.trim(integrationvar) != '')
                                                                        $("." + integrationvar).show();
                                                                });
                                                                $("#INTEGRATION_TYPE").change();

                                                                //  setTimeout(function(){ $('#success_msg').html(" "); }, 3000);
                                                            });
                                                            /*
                                                             *   DATA FIELDS FROM DATABASE
                                                             */

                                                        }

                                                    });
                                                    $("#service_id").change();
                                                    $('#smartloader').hide();
                                                });
                                                $('#smartloader').hide();
                                            });
                                            $(document).on('click', '#btn_Save', function () {
                                                $.ajax({
                                                    method: "POST",
                                                    url: "agent_details.php",
                                                    data: $('#rangeForm').serialize(),
                                                    dataType: 'json'
                                                }).done(function (data) {
                                                    if (data.success) {
                                                        $('#success_msg').html(" ");
                                                        $('#success_msg').html("<div class='alert alert-success'>Record has been Updated Successfully</div>");
                                                    } else if (data.error) {
                                                        $('#success_msg').html(" ");
                                                        $('#success_msg').html("<div class='alert alert-danger'>Record has not been Updated Successfully</div>");
                                                    }
                                                    setTimeout(function () {
                                                        $('#success_msg').html(" ");
                                                    }, 3000);
                                                });
                                            });
                                            $(document).on('click', '.btnservicebarcode', function () {
                                                var serviceId = $(this).data('serviceid');
                                                var agentId = $(this).data('agentid');
                                                $.post('agent_details.php', {func: 'service_barcode_ajax', serviceId: serviceId, agentId: agentId}, function (data) {
                                                    //console.log(data);
                                                    $('#success_msg').html(" ");
                                                    $('.range_form').show();
                                                    if (data.success) {
                                                        $('#range_name').val(data.range_name);
                                                        $('#start_range').val(data.range_start);
                                                        $('#end_range').val(data.range_end);
                                                        $('#next_number').val(data.next_number);
                                                        $('#prefix').val(data.prefix);
                                                        $('#suffix').val(data.sufix);
                                                        $('#id_range').val(data.id);
                                                    } else if (data.error == 'not_found') {
                                                        $('.range_form').hide();
                                                        $('#success_msg').html(" ");
                                                        $('#success_msg').html("<div class='alert alert-danger'>No Record Found.</div>");

                                                    }
                                                    $('#tbodybarcode').html('');
                                                    $('#tbodybarcode').html(data.list);
                                                    $('#serviceScanModal').modal('show');
                                                }, 'json');
                                            });
                                        });
        </script>

        <?php
    }

    public function renderHead() {
        
    }

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8)
                {
                    if (unicode == 46)
                    {
                    } else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }

            $(document).ready(function () {
                //  $("#service").chained("#carrier");
                // setupRefresh();

                $("#btnSave").click(function () {
                    $('#adminForm').validator().on('submit', function (e) {

                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            $("#form_action").val("save");

                        }
                    });
                    $("#adminForm").submit();

                });
                $(document).on('click', '#btnSaveEmail', function (e) {
                    var form_data = new FormData();
                    var id = $("#id").val();
                    var agentId = $("#agent_id").val();
                    var serviceId = $("#service_id").val();
                    var emailList = $("#add_email").val();
                    var emailListFinance = $("#add_email_finance").val();
                    form_data.append('action', "add_agent_email");
                    
                    form_data.append('agentid', agentId);
                    form_data.append('serviceid', serviceId);
                    form_data.append('emailList', emailList);
                    form_data.append('emailListFinance', emailListFinance);
                    form_data.append('id', id);

                    $.ajax({
                        url: "agent_details.php", // point to server-side PHP script
                        dataType: 'json', // what to expect back from the PHP script, if anything
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (res) {
                          //  var res = JSON.parse(data);
                            if (res.STATUS == "SUCCESS") {
                                $('#emaildisplay_message').show();
                                $('#emaildisplay_message').addClass('alert alert-success alert-dismissible');
                                $('#emaildisplay_message').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + res.MESSAGE);
                            } else {
                                $('#emaildisplay_message').show();
                                $('#emaildisplay_message').addClass('alert alert-danger alert-dismissible');
                                $('#emaildisplay_message').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + res.MESSAGE);
                            }
                        }
                    });

                });
                $(document).on('click', '#saveAgentService', function (e) {
                    //			          e.preventDefault();
                    var svbtn = $(this);
                    svbtn.attr("disabled", true);
                    $('#agentServiceForm').validator().on('submit', function (e) {
                        e.preventDefault(); // or return false, your choice
                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'post',
                            data: $(this).serialize(),
                            beforeSend: function () {
                                $('#smartloader').show();
                                $('#display_modal_message').html('');
                                $('#display_modal_message').hide();
                            },
                            success: function (data, textStatus, jqXHR) {
                                var res = JSON.parse(data);
                                if (res.status == 'success') {
                                    $('#display_message').show();
                                    $('#display_message').addClass('alert alert-success alert-dismissible');
                                    $('#display_message').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + res.message);

                                    $('#edit-agent-service').modal('hide'); //or  $('#IDModal').modal('hide');
                                    $('#manage-agent-service-table').DataTable().ajax.reload();
                                } else if (res.status == 'error') {
                                    $('#display_modal_message').show();
                                    $('#display_modal_message').addClass('alert alert-danger alert-dismissible');
                                    $('#display_modal_message').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + res.message);
                                    $("#agentServiceForm").unbind('submit');
                                }
                            },
                            complete: function () {
                                $('#smartloader').hide();
                                svbtn.removeAttr("disabled");
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            $('#smartloader').hide();
                            if (jqXHR.status == 0 || jqXHR == 302) {
                                alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                            } else {
                                alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                            }
                        });
                    });
                    $("#agentServiceForm").submit();
                });
                //        Handle User File upload
                $("#upload_file").click(function () {
                    var fileType = $("#document_name").val();
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
                        var agentId = $("#id").val();
                        var agentCode = $("#code").val();
                        form_data.append('file_type', fileType);
                        form_data.append('file_name', filename);
                        form_data.append('agent_id', agentId);
                        form_data.append('agentCode', agentCode);
                        form_data.append('file_type_text', fileTypeText);
                        form_data.append('action', "upload_user_doc");
                        form_data.append('user_doc_file', file_data);
                        $.ajax({
                            url: "agent_details.php", // point to server-side PHP script
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
                                    var userDocId = el.data("doc_id");
                                    var agentId = $("#id").val();
                                    var agentCode = $("#code").val();
                                    $.ajax({
                                        url: 'agent_details.php',
                                        type: 'POST',
                                        data: {action: 'remove_user_doc', doc_id: userDocId, agent_id: agentId, agentCode: agentCode},
                                        headers: {
                                        },
                                        success: function () {
                                            $("#usr_doc_" + userDocId).remove();
                                        },
                                        error: function (xhr, status, error) {

                                        }
                                    });

                                }
                            });

                });

            });
            $(document).on('change', '#edit-agent-service .integration_type', function () {
                var integration = $(this).val();
                if (integration == 'API' || integration == 'software')
                {
                    $('#edit-agent-service #ftp_display').hide();
                    $('#edit-agent-service #api_display').show();
                } else if (integration == 'EDI')
                {
                    $('#edit-agent-service #ftp_display').show();
                    $('#edit-agent-service #api_display').hide();
                }
            });
            function displayContent()
            {
                //                var agentType = $("#agentType").val();
                //                if(agentType == 'carrier')
                //                {
                //                    $('#dispatch_agent_detail').hide();
                //                    $('#carrier_agent_detail').show();
                //                }
                //                else if(agentType == 'dispatch')
                //                {
                //                    $('#dispatch_agent_detail').show();
                //                    $('#carrier_agent_detail').hide();
                //                }

            }
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
<style>


    .bootstrap-switch {
        display: grid;
    }

</style>