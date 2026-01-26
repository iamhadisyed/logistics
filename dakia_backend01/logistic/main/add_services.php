<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'carrier.class',
    'carrierfilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'servicecountrytime.class',
    'servicecountrytimefilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'services.class',
    'servicefilter.class',
    'serviceagentmappingdata.class',
    'serviceagentmappingdatafilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'remoteareachargesservicesuser.class',
    'remoteareachargesservicesuserfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
    'remoteareachargescarrieruser.class',
    'remoteareachargescarrieruserfilter.class'
]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    private $serviceCustomizeRulesList;
    private $serviceList;
    private $agentList;
    private $userAccountName;
    private $userAcccountId = 0;
    private $userAcccountParentId = 0;
    private $account_id = 0 ;
    private $account_id_encode = '';
    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'Manage Services'
        );
        
        $this->account_id = base64_decode(util_get("id"));
        $this->account_id_encode = util_get("id");
        
        $sessionUser = $this->user;
        $id = 0;
        if ($this->account_id > 0 )
            $id = $this->account_id;
        $this->userAcccountId = $id;
        if ($id > 0) {
            $userAccount = new CustomerAccount($id);
            $this->userAccountName = $userAccount->getUserAccount();
            $this->userAcccountParentId = $userAccount->getParentid();
        }
        /*
        * Get services list for filters
        */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_services_data') {
            $serviceId =  $this->form_vars['service_id'];
            $accountId = (int)trim($this->form_vars['account_id']);
            $servicesArr = [];
            if (is_numeric($serviceId) && $serviceId > 0)
                $servicesArr[] = $serviceId;
            else
                $servicesArr = $serviceId;
            //Check if service is product type then add without countries
            if (isset($this->form_vars['load_data']) && $this->form_vars['load_data'] != "only_load_data") {
                if (is_array($serviceId))
                    $serviceId = $serviceId[0];
                $serviceObj = new Services($serviceId);
                
                if ($serviceObj->getIsCustomized() == 1) {
                    $psrupdate = new UserServicesRouting();
                    //Set is agreed
                    $userServicesRoutingFilter = new UserServicesRoutingFilter();
                    $userServicesRoutingFilter->addFilter(" user_account_id = " . $accountId);
                    $userServicesRoutingFilter->setGroup(" service_id");
                    $userServicesRoutingList = $userServicesRoutingFilter->getList();
                    $isAgreedCarrier = [];
                    if (count($userServicesRoutingList) > 0) {
                        foreach ($userServicesRoutingList as $userServicesRoutingServiceId) {
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['country'][] = $userServicesRoutingServiceId->getCountryId();
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['from_weight'] = $userServicesRoutingServiceId->getFromWeight();
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['to_weight'] = $userServicesRoutingServiceId->getToWeight();
                            if ($userServicesRoutingServiceId->getIsAgreed()) {
                                $serviceObjNew = new Services($userServicesRoutingServiceId->getServiceId());
                                $isAgreedCarrier[] = $serviceObjNew->getCarrierId();
                            }
                        }
                    }
                    $isAgreedCarrier = array_unique($isAgreedCarrier);
                    $currentCarrierId = $serviceObj->getCarrierId();
                    if (in_array($currentCarrierId, $isAgreedCarrier)) {
                        $psrupdate->setIsAgreed(1);
                    }
                    $psrupdate->setServiceId($serviceObj->getId());
                    $psrupdate->setStatus(1);
                    $psrupdate->setUserAccountId($accountId);
                    $psrupdate->setAddedBy($this->user->getId());
                    $psrupdate->save();
                    
                    
                } else {
                    $user_service_remotearea = (isset($this->form_vars['user_service_remotearea'][$serviceId]) ? 1 : 0);
                    $serviceCountries = [];
                    $fromWeight = 0;
                    $toWeight = 0;
                    $isAgreedCarrier = [];
                    if (isset($this->form_vars['user_service_country'][$serviceId])) {
                        $serviceCountries = $this->form_vars['user_service_country'][$serviceId];
                        $fromWeight = $this->form_vars['user_from_weight'][$serviceId];
                        $toWeight = $this->form_vars['user_to_weight'][$serviceId];
                    } else if (isset($allSavedServices[$serviceId])) {
                        $serviceCountries = $allSavedServices[$serviceId]['country'];
                        $fromWeight = $allSavedServices[$serviceId]['from_weight'];
                        $toWeight = $allSavedServices[$serviceId]['to_weight'];
                    } else {
                        $fromWeight = $serviceObj->getFromWeight();
                        $toWeight = $serviceObj->getToWeight();
                        // fetch service countries
                        $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
                        $serviceCountryTimeFilter->addServiceTableJoin();
                        $serviceCountryTimeFilter->addFilter(' `service_country_ttime`.id_service =' . $serviceId);
                        $serviceCountryTimeFilter->setRowsPerPage("300");
                        $serviceCountryArr = $serviceCountryTimeFilter->getList();
                        foreach ($serviceCountryArr as $serviceCountry) {
                            $serviceCountries[] = $serviceCountry->getIdCountry();
                        }
                    }
                    
                    $userServicesRoutingFilter = new UserServicesRoutingFilter();
                    $userServicesRoutingFilter->addFilter(" user_account_id = " . $accountId);
                    $userServicesRoutingFilter->setGroup(" service_id");
                    $userServicesRoutingList = $userServicesRoutingFilter->getList();
                    if (count($userServicesRoutingList) > 0) {
                        foreach ($userServicesRoutingList as $userServicesRoutingServiceId) {
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['country'][] = $userServicesRoutingServiceId->getCountryId();
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['from_weight'] = $userServicesRoutingServiceId->getFromWeight();
                            $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['to_weight'] = $userServicesRoutingServiceId->getToWeight();
                            if ($userServicesRoutingServiceId->getIsAgreed()) {
                                $serviceObjNew = new Services($userServicesRoutingServiceId->getServiceId());
                                $isAgreedCarrier[] = $serviceObjNew->getCarrierId();
                            }
                        }
                    }
                    $isAgreedCarrier = array_unique($isAgreedCarrier);
                    $serviceObj = new Services($serviceId);
                    foreach ($serviceCountries as $countryId) {
                        $countryObj = new Country($countryId);
                        $psrupdate = new UserServicesRouting();
                        $currentCarrierId = $serviceObj->getCarrierId();
                        if (in_array($currentCarrierId, $isAgreedCarrier)) {
                            $psrupdate->setIsAgreed(1);
                        }
                        $psrupdate->setServiceId($serviceId);
                        $psrupdate->setStatus(1);
                        $psrupdate->setIsRemotearea($user_service_remotearea);
                        $psrupdate->setToWeight($toWeight);
                        $psrupdate->setFromWeight($fromWeight);
                        $psrupdate->setCountryId($countryId);
                        $psrupdate->setUserAccountId($accountId);
                        $psrupdate->setAddedBy($this->user->getId());
                        $userAuditData[] = 'Service Name: ' . $serviceObj->getName() . '<br />To Weight: ' . $toWeight . '<br />From Weight: ' . $fromWeight . '<br />Country: ' . $countryObj->getName();
                        $psrupdate->save();
                    }
                    if (!empty($userAuditData)) {
                        $new_data_p['Routing for service ' . $serviceObj->getName()] = implode('<br /><br />', $userAuditData);
                        $old_data_p = [];
                        $old_data = json_encode($old_data_p);
                        $new_data = json_encode($new_data_p);
                        $userAudit = new UserAudit();
                        $table_key = !empty($accountId) ? $accountId : '0';
                        $accountObj = new CustomerAccount($accountId);
                        $userAudit->allowAdd = true;
                        $userAudit->insertAuditData('user_account', 'insert', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $new_data, $table_key, $old_data, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' assigned Service ' . $serviceObj->getName() . ' to account ' . $accountObj->getUserAccount());
                    }
                }
            }
            $returnHtml = "";

            if (is_array($servicesArr) && count($servicesArr) > 0) {
                $serviceDetailData = new ServiceFilter();
                $serviceDetailData->addJoin("carrier", " ser.carrier_id = carrier.id ", "inner"); 
                
                $serviceDetailData->addJoin("user_services_routing", " service_id = ser.id AND user_account_id  = ".$this->userAcccountId , "inner"); 
               // $serviceDetailData->addJoin("user", " user.id = user_services_routing.`added_by` " , "inner"); 
                $serviceDetailData->addJoin("user", " user.id = user_services_routing.`added_by` " , "inner"); 
                $serviceDetailData->addJoin("agent_data", " agent_data.user_id = user_services_routing.added_by AND agent_data.active = 1" , "left"); 
                $serviceDetailData->addJoin("country", " ser.origin_country  = country.id ", "left"); 
                
                $serviceDetailData->addFilter("     ser.id in ('". implode("','",$servicesArr)."')");
                #$serviceDetailData->addFilter("     ");
                $serviceDetailData->addGroupByFilter("ser.id"); 
                $serviceDetailData->AddOrderBy("carrier.carrier"); 
                $selectFieldArray = [
                    'ser.*',
                    'user_services_routing.user_account_id as user_services_routing_account_id',
                    'user.user_account_id as user_account_id',
                    
                    'carrier.logo as carrier_logo',
                    'carrier.carrier as carrier_name',
                    'ser.carrier_id',
                    'country.iso as carrier_country_iso',
                    'country.name as carrier_country_name',
                    'user_services_routing.from_weight as user_services_routing_from_weight',
                    'user_services_routing.to_weight as user_services_routing_to_weight',
                    'user_services_routing.is_remotearea as user_services_routing_is_remotearea',
                    'user_services_routing.is_over_size as user_services_routing_is_over_size',
                    'user_services_routing.is_over_label as user_services_routing_is_over_label',
                    'user_services_routing.is_dead_weight as user_services_routing_is_dead_weight',
                    'user_services_routing.label_charges as user_services_routing_label_chagres',
                    '( select us.user_account_id from user us where ser.added_by = us.id) as service_account_id'               
                
                ];
                
                    
                        
                        
                $serviceDataFileter = $serviceDetailData->getColumnList(implode(",", $selectFieldArray));
                
                if(count($serviceDataFileter)>0){
                     foreach ($serviceDataFileter as $serviceData) {
                    $disabled = "";
                    $disabledDeleteClass = "repeater_delete_service";
                    $disabledClass = "";
                    if ($this->userAcccountId > 0) {
                        if ($serviceData->getUserServicesRoutingAccountId() == $serviceData->getUserAccountId() ) {
                            $disabled = "disabled='disabled'";
                            $disabledClass = "disabled";
                            $disabledDeleteClass = "";
                        }
                    }
                    $serviceAddedBYAccount = $serviceData->getServiceAccountId();
                   
                   
                    $service = $serviceData;//new Services($serviceId);
                    $serviceId  =   $service->getId();
                    $userFromWeight = $serviceData->getUserServicesRoutingFromWeight();
                    $userToWeight = (trim($serviceData->getUserServicesRoutingToWeight()) == ''?$service->getToWeight():$serviceData->getUserServicesRoutingToWeight());
                    $isRemotearea = (trim($serviceData->getUserServicesRoutingIsRemotearea()) == ''?($serviceData->getIsRemotearea() == "Y" ? 1 : 0):$serviceData->getUserServicesRoutingIsRemotearea()); //= $serviceData->getUserServicesRoutingIsRemotearea();
                    $isOverSize = $serviceData->getUserServicesRoutingIsOverSize();
                    $isOverLabel = $serviceData->getUserServicesRoutingIsOverLabel();
                    $isDeadWeight = $serviceData->getUserServicesRoutingIsDeadWeight();
                    $LabelChagres = $serviceData->getUserServicesRoutingLabelChagres();
                    $logo = $serviceData->getCarrierLogo();
                    $oriCountryName = $serviceData->getCarrierCountryName();
                    $oriCountryIso = $serviceData->getCarrierCountryIso();
                    $returnHtml .= '<tr data-service-ajax-id="' . $serviceId . '"  data-service-id="' . $serviceId . '" class="service_row">';
                    if (trim($logo) != '' && file_exists("../images/carrierlogo/" . $logo)) {
                        $returnHtml .= '<td>
                                            <img src="../images/carrierlogo/' . $logo . '" title="' . $carrierName . '" alt="' . $carrierName. '" width="70px">
                                        </td>';
                    } else {
                        $returnHtml .= '<td>
                                        <img src="../images/carrierlogo/no-image-found.jpg" title="' . $carrierName. '" alt="' . $carrierName . '" width="70px">
                                        </td>';
                    }
                    $returnHtml .= '<td class="service_name_td">' . $service->getName() . '<br> [ '.$service->getCode().' ] <input type="hidden" name="agent_services[]" value="' . $serviceId . '" class="agent_services_hidden" /></td>';
                    $returnHtml .= '<td>';
                    if (!empty($oriCountryIso)) {
                        $returnHtml .= '<img src=\'../assets/global/img/flags/' . strtolower($oriCountryIso) . '.png\' /> ' . $oriCountryName;
                    }
                    $returnHtml .= '</td>';
                    $showRemotearea = 'style="display:none;"';
                    $checkRemotearea = '';
                    if ($isRemotearea == 1) {
                        $showRemotearea = 'style="display:block;"';
                        $checkRemotearea = 'checked="checked"';
                    }
                    $checkIsOverSize = '';
                    if ($isOverSize == '1') {
                        $checkIsOverSize = 'checked="checked"';
                    }
                    $checkIsOverLable = '';
                    if ($isOverLabel == '1') {
                        $checkIsOverLable = 'checked="checked"';
                    }
                    $checkIsDeadWgt = '';
                    if ($isDeadWeight == '1') {
                        $checkIsDeadWgt = 'checked="checked"';
                    }
                    //' . (($service->getIsCustomized() == 1) ? ' disabled="disabled"' : '') . '
                    $returnHtml .= '<td class="text-center">

                                            <div class="md-radio-sm md-radio-inline">
                                                <input ' . $checkRemotearea . ' name="user_service_remotearea[' . $serviceId . ']" data-service_id="' . $serviceId . '" type="checkbox" class="make-switch user_service_remotearea"  data-on-text="Yes" id="user_service_remotearea_' . $serviceId . '"  data-off-text="No"  data-on-color="primary" data-off-color="danger" data-size="mini" >
                                            </div>                                        
                                        </td>';
                    $returnHtml .= '<td class="text-center">

                                            <div class="md-radio-sm md-radio-inline">
                                                <input ' . $checkIsOverSize . ' name="user_service_over_size[' . $serviceId . ']" data-service_id="' . $serviceId . '" type="checkbox" class="make-switch user_service_over_size"  data-on-text="Yes" id="user_service_over_size_' . $serviceId . '"  data-off-text="No"  data-on-color="primary" data-off-color="danger" data-size="mini"  >
                                            </div>                                        
                                        </td>';
                    $returnHtml .= '<td class="text-center">

                                            <div class="md-radio-sm md-radio-inline">
                                                <input ' . $checkIsOverLable . ' name="user_service_over_label[' . $serviceId . ']" data-service_id="' . $serviceId . '" type="checkbox" class="make-switch user_service_over_label"  data-on-text="Yes" id="user_service_over_label_' . $serviceId . '"  data-off-text="No"  data-on-color="primary" data-off-color="danger" data-size="mini" ' . (($service->getIsCustomized() == 1) ? ' disabled="disabled"' : '') . ' ' . $disabled . ' >
                                            </div>                                        
                                        </td>';
                    $returnHtml .= '<td class="text-center">
                                            <div class="md-radio-sm md-radio-inline">
                                                <input ' . $checkIsDeadWgt . ' name="user_service_dead_weight[' . $serviceId . ']" data-service_id="' . $serviceId . '" type="checkbox" class="make-switch user_service_dead_weight"  data-on-text="Dead_Wgt" id="user_service_dead_weight_' . $serviceId . '"  data-off-text="Default"  data-on-color="primary" data-off-color="danger" data-size="mini" >
                                            </div>    
                                        </td>';
                    $returnHtml .= '<td class="text-center">
                                        <div class="form-group">
                                            <input class="form-control user_service_label_charges"  name="user_service_label_charges[' . $serviceId . ']"  data-service_id="' . $serviceId . '"  style="width: 120px"  type="text" placeholder="Label Charges" rel="tooltip" data-original-title="Label Charges" value="' . $LabelChagres . '">
                                        </div>
                                        </td>';

                    $returnHtml .= '<td>';
                    //if (trim($service->getIsCustomized()) == 0 && ($disabled == "")) {
                        $returnHtml .= ' <span class="btn btn-xs default red-stripe btn-block" title="Service allowed weight">' . $service->getFromWeight() . ' Kg - ' . $service->getToWeight() . ' Kg</span>
                                        <span id="service_' . $serviceId . '" class="btn btn-xs default blue-stripe btn-block"    title="User allowed Weight">' . $userFromWeight . ' Kg - ' . $userToWeight . ' Kg</span>';
                   // }
                    $returnHtml .= '</td>';
                    $returnHtml .= '<td>';
                    if (trim($service->getIsCustomized()) == 0 && ($disabled == "")) {
                        $returnHtml .= '<span class="users_weight_limit btn default red-stripe btn-xs btn-block " rel="tooltip" data-user-from-weight="' . $userFromWeight . '" data-user-to-weight="' . $userToWeight . '" data-user-service-name="' . $service->getName() . '" data-user-service-id="' . $serviceId . '">Country / User weight</span>';
                        $returnHtml .= '<!-- Model for carrier countries-->
                                            <div class="modal fade bs-modal-lg" id="users_weight_limit_model_' . $serviceId . '" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="users_weight_limit_model"></div>';
                        $funcParamModified = "getmodifiedWeight('" . $serviceId . "'," . "'" . $service->getName() . "'," . "'" . $logo . "')";
                        $returnHtml .= '<span class="btn default blue-stripe btn-xs btn-block" data-toggle="modal" data-target="#myModalAdvance" onclick="return ' . $funcParamModified . ';">Weight</span>';
                        
                       

                    } else {
                        if ($this->user->getUserAccountId() == $serviceData->getServiceAccountId()) {
                            $returnHtml .= '<a href="product_edit.php?customize_service_id=' . $serviceId . '&amp;country=GB" class="btn default blue-stripe btn-xs btn-block ' . $disabledClass . '" id="saveRoutine" data-original-title="" title="" ' . $disabled . ' ><span></span>Product Details</a>';
                        }
                    }
                    $funcRemoteareaService = "getRemoteareaService('" . $serviceId . "'," . "'" . $carrierId . "'," . "'" . (trim($service->getRemotearea())=='' ?'ON_PIECE':trim($service->getRemotearea())) . "','" . $accountId . "')";
                        $returnHtml .= '<span ' . $showRemotearea . ' class="btn default green-stripe btn-xs btn-block" data-toggle="modal" id="span_user_service_remotearea_' . $serviceId . '" data-target="#model_remoterea_services" onclick="return ' . $funcRemoteareaService . ';">Remotearea</span>';
                    $returnHtml .= '</td>';
                    $returnHtml .= '<td class="text-center">';
                    $returnHtml .= '<a href="javascript:{};"  class="btn btn-danger btn-xs ' . $disabledDeleteClass . '" data-service_id="' . $serviceId . '" ' . $disabled . ' ><i class="fa fa-close"></i></a>';
                    $returnHtml .= '<a href="user_account_service_charges.php?service_id=' . base64_encode($serviceId) . '&account_id=' .base64_encode( $accountId ) . '"  class="btn  btn-xs btn-primary ' . $disabledClass . '" ' . $disabled . ' ><i class="fa fa-money"></i></a>';
                    $returnHtml .= '</td>';

                }
                }
               
            }
            echo $returnHtml;
            die;
        }
        /*
        * Get Carrier Countries List
        */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_countries_list') {
            $serviceId = (int)trim($this->form_vars['service_id']);
            $accountId = (int)trim($this->form_vars['acccout_id']);
            $service = new Services($serviceId);
            $carrierId = $service->getCarrierId();
            $carrier = new Carrier($carrierId);
            $selectedServicesArr = array();
            $userServicesRoutingFilter = new UserServicesRoutingFilter();
            $userServicesRoutingFilter->addFieldFilter('user_account_id', $accountId);
            $userServicesRoutingFilter->addFieldFilter('service_id', DbAccess3::escape($serviceId));
            $userServicesRoutingFilter->setLimit('1000');
            $userServicesRoutingResult = $userServicesRoutingFilter->getListByAttribute("*");
            foreach ($userServicesRoutingResult as $userServicesRouting) {
                $selectedServicesArr[] = $userServicesRouting->getServiceId();
            }
            $userFromWeight = 0;
            $userToWeight = 0;
            if (count($userServicesRoutingResult) > 0) {
                $userFromWeight = $userServicesRoutingResult[0]->getFromWeight();
                $userToWeight = $userServicesRoutingResult[0]->getToWeight();
            } else {
                $userFromWeight = $service->getFromWeight();
                $userToWeight = $service->getToWeight();
            }

            $output = '';
            $output .= '<div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">';
            if (trim($carrier->getLogo()) != '' && file_exists("../images/carrierlogo/" . $carrier->getLogo())) {
                $output .= '<img class="margin-right-5" src="../images/carrierlogo/thumbnail/owe_50_' . $carrier->getLogo() . '" title="' . $carrier->getCarrier() . '" alt="' . $carrier->getCarrier() . '" popover-trigger="mouseenter" height="25">';
            } else {
                $output .= '<img height="25" src="../images/carrierlogo/no-image-found.jpg" title="' . $carrier->getCarrier() . '" alt="' . $carrier->getCarrier() . '" class="margin-right-5">';
            }
            $output .= $service->getName() . ' service remoteareas</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-danger display-none"  id="res_message_users_weight_limit_' . $serviceId . '" ></div>
                                        </div>
                                    </div>
                                    <div id="data_users_weight_limit_' . $serviceId . '">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label>From weight</label>
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                        <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight is mandatory">*</i>
                                                            <input id="user_from_weight_' . $serviceId . '" type="text" name="user_from_weight[' . $serviceId . ']" value="' . $userFromWeight . '" placeholder="From Weight" class="form-control agent_from_weight validate_check"  data-toggle="tooltip" data-placement="top" title="From Weight" />

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label>To weight</label>
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                        <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight is mandatory">*</i>
                                                            <input id="user_to_weight_' . $serviceId . '" type="text" name="user_to_weight[' . $serviceId . ']" value="' . $userToWeight . '" placeholder="To Weight" class="form-control agent_to_weight validate_check"  data-toggle="tooltip" data-placement="top" title="To Weight" />

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">';
            if ($serviceId > 0) {
                $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
                $serviceCountryTimeFilter->addServiceTableJoin();
                $serviceCountryTimeFilter->addFilter(' `service_country_ttime`.id_service =' . $serviceId);
                $serviceCountryTimeFilter->setRowsPerPage("300");
                $serviceCountry = $serviceCountryTimeFilter->getList();
                $output .= '<table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="4">
                                            <span style="width: 20px;float: left;" class="margin-right-5">
                                                <div class="input-group">
                                                    <div class="icheck-inline">
                                                        <input type="checkbox" name="check_all_country" class="icheck" data-checkbox="icheckbox_flat-blue" value="check_all_country" id="check_all_country" >
                                                    </div>
                                                </div>
                                            </span>Countries List
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>';
                $i = 0;
                foreach ($serviceCountry as $sercoun) {
                    $userServicesRoutingFilter = new UserServicesRoutingFilter();
                    $userServicesRoutingFilter->addFilter('    service_id=' . $serviceId . ' AND from_weight=' . $userFromWeight . ' AND to_weight=' . $userToWeight . ' AND user_account_id=' . $accountId . ' AND country_id=' . $sercoun->getIdCountry());
                    $checked = "";
                    if (in_array($serviceId, $selectedServicesArr)) {
                        if ($userServicesRoutingFilter->getCount() > 0) {
                            $checked = 'checked="checked"';
                        }
                    } else {
                        $checked = 'checked="checked"';
                    }

                    $serCountryIso = $sercoun->getIdCountry();
                    $countryFilter = new CountryFilter();
                    $countryFilter->addFilter(" id = " . $serCountryIso);
                    $countryList = $countryFilter->getList();
                    if (count($countryList) > 0) {
                        $countryName = $countryList[0]->getName();
                    }
                    if ($i % 4 == 0) {
                        $output .= '</tr>';
                        $output .= '<tr class="advance_user_option">';
                    }
                    $filename = str_replace("|", "-", $serviceCountry[0]->getCode());
                    $filelink = "../_assets/service_sample_label/" . $filename . ".pdf";
                    if (file_exists($filelink)) {
                        $link = '<a href="' . $filelink . '" target="_blank"><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>';
                        $output .= '<td><div class="input-group"><div class="icheck-inline"><label><input type="checkbox" name="user_service_country[' . $serviceId . '][]" class="icheck" data-checkbox="icheckbox_flat-blue" value="' . $countryList[0]->getId() . '" ' . $checked . '>' . $link . '<label></div></div></td>';
                        $i++;
                    } else {
                        $link = '<a href="#" onclick="fileNotFound();" ><img src=\'../assets/global/img/flags/' . strtolower($countryList[0]->getIso()) . '.png\' /> ' . $countryName . '</a>';
                        $output .= '<td><div class="input-group"><div class="icheck-inline"><label><input type="checkbox" name="user_service_country[' . $serviceId . '][]" class="icheck" data-checkbox="icheckbox_flat-blue" value="' . $countryList[0]->getId() . '" ' . $checked . '>' . $link . '<label></div></div></td>';
                        $i++;
                    }
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div>
                                        </div>
                                    </div> 
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                    <button data-user_id="' . $accountId . '" type="button" data-service_id="' . $serviceId . '" class="btn green save_user_agent_data">Save changes</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>';
            echo $output;
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'special_routing_weight') {
            $serviceId = trim($this->form_vars['serviceCode']);
            parse_str($_POST['serviceInput'], $serviceData);
            $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
            $carrierServiceDefaultRulesFilter->addFilter(" serviceid = " . $serviceId);
            $carrierServiceDefaultRulesFilter->addAgentTableJoin();
            $carrierServiceDefaultRulesResults = $carrierServiceDefaultRulesFilter->getList('agentid');
            $html = '';
            $html .= '<div class="row">
                        <div class="col-sm-3">
                            <label>Agent</label>
                        </div>';
            $html .= '<div class="col-sm-2">
                        <label>From Weight</label>
                    </div>';
            $html .= '<div class="col-sm-2">
                        <label>To Weight</label>
                    </div>';
            $html .= '</div>';
            $html = '<div class="parent_clone_div">';
            if (!empty($serviceData)) {
                $inc = 0;
                foreach ($serviceData['agent'][$serviceId] as $key => $agentList) {
                    if ($inc == 0) {
                        $html .= '<div class="clone_div">';
                    }
                    $html .= '<div class="row">';
                    $html .= '<div class="col-sm-3">';
                    $html .= '<div class="form-group">';
                    $html .= '<div class="input-group">';
                    $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>';
                    $html .= Ddl::generateDDL('agent[' . $serviceId . '][]', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', $serviceData['agent'][$serviceId][$inc], ' class="form-control select2 agent_select"', "Please select agent", "", 'agent[' . $serviceId . '][]', "Agent");
                    $html .= '<span class="input-group-addon red-18">*</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-3">';
                    $html .= '<div class="form-group">';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                    $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Contact is mandatory">*</i>';
                    $html .= '<input id="agent_from_weight" type="text" name="agent_from_weight[' . $serviceId . '][]" placeholder="From Weight" class="form-control agent_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" value="' . $serviceData['from_weight'][$serviceId][$inc] . '" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-3">';
                    $html .= '<div class="form-group">';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Contact is mandatory">*</i>';
                    $html .= '<input id="agent_to_weight" type="text" name="agent_to_weight[' . $serviceId . '][]" placeholder="To Weight" class="form-control agent_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" value="' . $serviceData['to_weight'][$serviceId][$inc] . '" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-1 show_remove_btn" data-index-of-agent="0" style="display: none;">';
                    $html .= '<div class="form-group">';
                    $html .= '<label class="control-label">&nbsp;</label>';
                    $html .= '<a href="javascript:;"  class="btn btn-xs btn-danger repeater-delete"><i class="fa fa-close"></i></a>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>'; //End row
                    if ($inc == 0) {
                        $html .= '</div><div class="append_here">';
                    }
                    $inc++;
                }
                $html .= '</div>';
            } else {
                $html = '';
                $html .= '<div class="row">
                        <div class="col-sm-3">
                            <label>Agent</label>
                        </div>';
                $html .= '<div class="col-sm-2">
                        <label>From Weight</label>
                    </div>';
                $html .= '<div class="col-sm-2">
                        <label>To Weight</label>
                    </div>';
                $html .= '</div>';
                $html .= '<div class="clone_div">';
                $html .= '<div class="row">';
                $html .= '<div class="col-sm-3">';
                $html .= '<div class="form-group">';
                $html .= '<div class="input-group">';
                $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>';
                $html .= Ddl::generateDDL('agent[' . $serviceId . '][]', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', '', ' class="form-control select2 agent_select"', "Please select agent", "", 'agent[' . $serviceId . '][]', "Agent");
                $html .= '<span class="input-group-addon red-18">*</span>';
                $html .= '</div>'; // End input-group
                $html .= '</div>'; // End form-group
                $html .= '</div>'; //End col-sm-3
                $html .= '<div class="col-sm-3">';
                $html .= '<div class="form-group">';
                $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight is mandatory">*</i>';
                $html .= '<input id="agent_from_weight" type="text" name="agent_from_weight[' . $serviceId . '][]" value="" placeholder="From Weight" class="form-control agent_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-sm-3">';
                $html .= '<div class="form-group">';
                $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Contact is mandatory">*</i>';
                $html .= '<input id="agent_to_weight" type="text" name="agent_to_weight[' . $serviceId . '][]" value="" placeholder="To Weight" class="form-control agent_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-1 show_remove_btn" data-index-of-agent="0" style="display: none;">';
                $html .= '<label class="control-label">&nbsp;</label>';
                $html .= '<a href="javascript:;"  class="btn btn-danger  btn-xs repeater-delete "><i class="fa fa-close"></i></a>';
                $html .= '</div>';
                $html .= '</div>'; //End row
                $html .= '</div>'; //End clone_div
                $html .= '<div class="append_here"></div>';
            }
            $html .= '<hr>';
            $html .= '<a href="javascript:;" class="btn btn-info repeater-add"><i class="fa fa-plus"></i> Add Agent</a><br><br>';
            $html .= '</div>'; //End parent_clone_div
            echo $html;
            die;
        }
        /*
         * Check/Save agent weigh limits by service
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'check_weight_limit') {

            $weightFrom = $this->form_vars['weight_from'];
            $weightTo = $this->form_vars['weight_to'];
            $agentList = $this->form_vars['agent_select'];
            $accountId = $this->form_vars['account_id'];
            $serviceId = $this->form_vars['service_id'];

            parse_str($weightFrom, $serviceDataWeightFrom);
            parse_str($weightTo, $serviceDataWeightTo);
            parse_str($agentList, $serviceDataAgentList);
            $first_key = key($serviceDataAgentList['agent']);
            if (!empty($serviceDataAgentList['agent'][$first_key])) {
                $checkAgent = array();
                foreach ($serviceDataAgentList['agent'][$first_key] as $key => $agentsList) {
                    $agentCount = 0;
                    if ($agentsList > 0) {
                        $serviceAgentMapping = new ServiceAgentMappingDataFilter();
                        $serviceAgentMapping->addFilter(' agentid=' . $agentsList);
                        $serviceAgentMapping->addFilter(' from_weight <=' . $serviceDataWeightFrom['agent_from_weight'][$first_key][$key]);
                        $serviceAgentMapping->addFilter(' to_weight >=' . $serviceDataWeightTo['agent_to_weight'][$first_key][$key]);
                        $agentCount = $serviceAgentMapping->getPagingCount();
                    } else {
                        $checkAgent[] = 0;
                    }

                    if ($agentCount == 0) {
                        $checkAgent[] = 0;
                    } else {
                        $checkAgent[] = 1;
                    }
                }
            } else {
                $checkAgent[] = 0;
            }
            if (!empty($checkAgent)) {
                if (in_array('0', $checkAgent)) {
                    echo 'error';
                } else {
                    $oldAgentServiceData = new carrierServiceCustomizeRulesFilter();
                    $oldAgentServiceData->addFilter('user_account_id = '. DbAccess3::escape($accountId));
                    $oldAgentServiceData = $oldAgentServiceData->getList();
                    if(!empty($oldAgentServiceData)) {
                        foreach ($oldAgentServiceData as $datum) {
                            $agentObj = new AgentData($datum->getAgentid());
                            $oldAuditData[] = "Agent Name: " . $agentObj->getAgentName() . '<br />From Weight: ' . $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight();
                        }
                    }

                    //Delete previous data
                    carrierServiceCustomizeRules::deleteByUserId($accountId);
                    //Save record in this condition
                    foreach ($serviceDataAgentList['agent'][$first_key] as $key => $agentId) {
                        $fromWeight = $serviceDataWeightFrom['agent_from_weight'][$first_key][$key];
                        $toWeight = $serviceDataWeightTo['agent_to_weight'][$first_key][$key];

                        $carrierServiceCustomizeRules = new carrierServiceCustomizeRules();
                        $carrierServiceCustomizeRules->setServiceid($serviceId);
                        $carrierServiceCustomizeRules->setAgentid($agentId);
                        $carrierServiceCustomizeRules->setUserAccountId($accountId);
                        $carrierServiceCustomizeRules->setFromWeight($fromWeight);
                        $carrierServiceCustomizeRules->setToWeight($toWeight);
                        $carrierServiceCustomizeRules->setStatus(1);
                        $carrierServiceCustomizeRules->save();
                        $agentObj = new AgentData($agentId);
                        $newAuditData[] = "Agent Name: " . $agentObj->getAgentName() . '<br />From Weight: ' . $fromWeight . '<br />To Weight: ' . $toWeight;
                    }
                    $newDataAudit = [];
                    $oldDataAudit = [];
                    $serviceObj = new Services($serviceId);
                    if(!empty($newAuditData)) {
                        $newDataAudit[$serviceObj->getName()] = implode('<br /><br /> ', $newAuditData);
                    }
                    if(!empty($oldAuditData)) {
                        $oldDataAudit[$serviceObj->getName()] = implode('<br /><br /> ', $oldAuditData);
                    }
                    $oldData = json_encode($oldDataAudit);
                    $newData = json_encode($newDataAudit);
                    $userAudit = new UserAudit();
                    $tableKey = !empty($accountId) ? $accountId : '0';
                    $userAudit->allowAdd = true;
                    $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Carrier Service Rule of Service ' . $serviceObj->getName());
                    echo 'success';
                }
            } else {
                echo 'error';
            }
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_remotearea_charges') {
            $serviceId = (int) trim($this->form_vars['serviceCode']);
            $service = new Services($serviceId);
            $carrierId = trim($this->form_vars['carrier_id']);
            $carrier = new Carrier($carrierId);
            $userAccountId = trim($this->form_vars['user_account_id']);
            $remoteareasType = trim($this->form_vars['remoteareas_full_type']);
            $ramessage = trim($this->form_vars['ramessage']);
            $rastatus = trim($this->form_vars['rastatus']);
            parse_str($_POST['serviceInput'], $serviceData);
            $remoteareaChargesServicesUserFilter = new RemoteareaChargesServicesUserFilter(); 
            $remoteareaChargesServicesUserFilter->addFilter(" service_id = " . $serviceId);
            $remoteareaChargesServicesUserFilter->addFilter(" user_account_id = " . $userAccountId);
            $remoteareaChargesServicesUserFilter->AddOrderBy('from_weight', 'ASC');
            $remoteareaChargesServicesUserFilterResults = $remoteareaChargesServicesUserFilter->getList();
            $html='';
            $html .= '    <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title"><img class="margin-right-5" src="../images/carrierlogo/thumbnail/owe_50_' . $carrier->getLogo() . '" alt="' . $service->getName() . '" popover-trigger="mouseenter" height="25">' . $service->getName() . ' service remoteareas</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger display-none"  id="res_message_' . $serviceId . '" ></div>
                                </div>
                            </div>
                            
                       ';
            $html .= '<div class="parent_clone_div_remotearea">';
            if (!empty($remoteareaChargesServicesUserFilterResults)) {
                $inc = 0;
                foreach ($remoteareaChargesServicesUserFilterResults as $rcsResult) {
                    if ($inc == 0) {
                        $html .= '<div class="clone_div_remotearea">';
                    }
                    $html .= '<div class="row">';
                    $html .= '<div class="col-sm-' . ($remoteareasType == "ON_PIECE" ? "6" : "3") . '">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>Group</label>';
                    $html .= '<div class="input-group">';
                    $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>';
                    $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter', 'AND carrier_id = ' . $carrierId . ' ', 'group_name', 'id', '' . $rcsResult->getRemoteareaGroupId() . '', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");
                    $html .= '<span class="input-group-addon red-18">*</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    if ($remoteareasType == "ON_WEIGHT") {
                        $html .= '<div class="col-sm-2">';
                        $html .= '<div class="form-group">';
                        $html .= '<label>From Weight</label>';
                        $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                        $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight is mandatory">*</i>';
                        $html .= '<input type="text" name="from_weight[]" value="' . $rcsResult->getFromWeight() . '" placeholder="From Weight" class="validate_check  form-control remotearea_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-sm-2">';
                        $html .= '<div class="form-group">';
                        $html .= '<label>To Weight</label>';
                        $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight is mandatory">*</i>';
                        $html .= '<input  type="text" name="to_weight[]" value="' . $rcsResult->getToWeight() . '" placeholder="To Weight" class="validate_check  form-control remotearea_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '<div class="col-sm-' . ($remoteareasType == "ON_PIECE" ? "5" : "2") . '">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>Charges</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges is mandatory">*</i>';
                    $html .= '<input type="text" name="charges[]" value="' . $rcsResult->getRemoteareaCharges() . '" placeholder="Charges" class="validate_check  form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Chanrges" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    if ($remoteareasType == "ON_WEIGHT") {
                        $html .= '<div class="col-sm-2">';
                        $html .= '<div class="form-group">';
                        $html .= '<label>Formula</label>';
                        $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Formula is mandatory">*</i>';
                        $html .= '<input type="text" name="formula[]" value="' . $rcsResult->getFormulla() . '" placeholder="Formula" class="form-control remotearea_formula"  data-toggle="tooltip" data-placement="top" title="Formula" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '<div class="col-md-1 show_remotearea_remove_btn" data-index_of_remotearea_remove="' . $inc . '" ' . ($inc == 0 ? 'style="display: none;"' : '') . ' >';
                    $html .= '<label class="control-label">&nbsp;</label>';
                    $html .= '<a href="javascript:;"  class="btn btn-danger btn-xs repeater-delete"><i class="fa fa-close"></i></a>';
                    $html .= '</div>';
                    $html .= '</div>';//End row
                    if ($inc == 0) {
                        $html .= '</div><div class="append_here_remotearea">';
                    }
                    $inc++;
                }
                $html .= '</div>';
            } else {
                $html .= '<div class="clone_div_remotearea">';
                $html .= '<div class="row">';
                $html .= '<div class="col-sm-' . ($remoteareasType == "ON_PIECE" ? "6" : "3") . '">';
                $html .= '<div class="form-group">';
                $html .= '<label>Group</label>';
                $html .= '<div class="input-group">';
                $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>';
                $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter', 'AND carrier_id = ' . $carrierId . ' ', 'group_name', 'id', '', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");
                $html .= '<span class="input-group-addon red-18">*</span>';
                $html .= '</div>';// End input-group
                $html .= '</div>'; // End form-group
                $html .= '</div>';//End col-sm-2
                if ($remoteareasType == "ON_WEIGHT") {
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>From Weight</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                    $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight is mandatory">*</i>';
                    $html .= '<input type="text" name="from_weight[]" value="" placeholder="From Weight" class="validate_check form-control remotearea_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>To Weight</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight is mandatory">*</i>';
                    $html .= '<input type="text" name="to_weight[]" value="" placeholder="To Weight" class="validate_check form-control remotearea_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '<div class="col-sm-' . ($remoteareasType == "ON_PIECE" ? "5" : "2") . '">';
                $html .= '<div class="form-group">';
                $html .= '<label>Charges</label>';
                $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges is mandatory">*</i>';
                $html .= '<input type="text" name="charges[]" value="" placeholder="Charges" class="validate_check form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Charges" />';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                if ($remoteareasType == "ON_WEIGHT") {
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>Formula</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                    $html .= '<div class="input-icon right">';
                    $html .= '<input type="text" name="formula[]" value="" placeholder="Formula" class="form-control remotearea_formula"  data-toggle="tooltip" data-placement="top" title="Formula" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '<div class="col-md-1 show_remotearea_remove_btn" data-index_of_remotearea_remove="0" style="display: none;">';
                $html .= '<label class="control-label">&nbsp;</label>';
                $html .= '<a href="javascript:;"  class="btn btn-danger  btn-xs repeater-delete"><i class="fa fa-close"></i></a>';
                $html .= '</div>';
                $html .= '</div>';//End row
                $html .= '</div>';//End clone_div_remotearea
                $html .= '<div class="append_here_remotearea"></div>';
            }
            $html .= '<hr>';
            $html .= '<a href="javascript:;" data-service_id="' . $serviceId . '" class="btn btn-info repeater_add_more"><i class="fa fa-plus"></i> Add more</a><br><br>';
            $html .= '</div>';//End parent_clone_div_remotearea
                $html .= '<div class="row"><div class="col-md-12"><hr/></div></div>';
            $html .= '<div class="row">
                            <input type="hidden" name="upload_service_id" id="upload_service_id" value="'.$serviceId.'" />
                            <input type="hidden" name="upload_carrier_id" id="upload_carrier_id" value="'.$carrierId.'" />  
                            <input type="hidden" name="upload_useraccount_id" id="upload_useraccount_id" value="'.$userAccountId.'" />      
                            <input type="hidden" name="upload_retmotearea_service_type" id="upload_retmotearea_service_type" value="'.$remoteareasType.'" />  
                            <div class="col-md-12">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="form-group">
                                        <label>'.Translation::GetCaption("SELECT_FILE_TO_IMPORT").'</label>
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Select file </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="file" id="services_upload_remote_file"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImportRemoteArea" data-original-title="" title=""><i class="fa fa-upload"></i> '.Translation::GetCaption("IMPORT").'</a>
                                            <a href="javascript:;" class="input-group-addon btn btn-primary" id="download_sevices_remotarea_template" data-original-title="Download Template" title="Download Template"><i class="fa fa-download"></i> Download Template</a></a>
                                                
                                        </div>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                        </div> 
                        <div class="row">
                            <div class="col-md-12">';
                    if ($ramessage != '') {
                        $html .= '<div    style="  clear:both;background-color: #000;color: #FFF; padding: 15px;">' . $ramessage . '</div>';
                    }
                    $html .= '       </div>
                                </div>

                                    ';
            $html .= ' </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                            <button type="button" id="save_remotearea_charges_' . $serviceId . '" data-remoteares_save_type="' . (trim($service->getRemotearea()) == ''?'ON_PIECE':trim($service->getRemotearea())) . '"  data-remoteares_type="' . $carrier->getRemoteareaCheck() . '" data-service_id="' . $serviceId . '" class="btn green save_service_remotearea_data">Save changes</button>
                        </div>';
            
            echo $html;
            die;
        }
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_services_remotearea_template') {
            $serviceId = $this->form_vars['download_csv_serviceId'];
            $carrierId = $this->form_vars['download_csv_carrierId']; 
            $serviceObj = new Services($serviceId);
            /*  Download Csv Code  */
            $fileName = "service_remotearea_import_format.csv";
            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            if($serviceObj->getRemotearea() == 'ON_WEIGHT')
                fputcsv($output, array('Group Name', 'From Weight', 'To Weight', 'Charges', 'Formula'));
            else
                fputcsv($output, array('Group Name', 'Charges'));
            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
            $remoteareasGroupsFilter->addFilter("AND is_deleted = 'N' AND carrier_id = " . $carrierId   );
 
            $remoteareasGroupsNames = $remoteareasGroupsFilter->getColumnList("group_name");
            if (count($remoteareasGroupsNames) > 0) {
                foreach ($remoteareasGroupsNames as $remoteareasGroupsName) {
                    $groupName = $remoteareasGroupsName->getGroupName();
                    if($serviceObj->getRemotearea() == 'ON_WEIGHT')
                        fputcsv($output, array($groupName, '', '', '', ''));
                    else
                        fputcsv($output, array($groupName, ''));
                }
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }
        
        
         if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file_remotearea') {
            $userAccount = $this->user;
            $serviceId = trim($this->form_vars['serviceId']);
            
            $serviceObj = new Services($serviceId);
            $carrierId = trim($this->form_vars['carrierId']);
            $userAccountId = trim($this->form_vars['userAccountId']);
            $remoteAreaServiceType = trim($this->form_vars['remoteAreaServiceType']);

            $groupsArray = array();
            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
            $remoteareasGroupsFilter->addFilter("AND carrier_id = " . $carrierId." AND  is_deleted='N' ");
            $remoteareasCheck = $remoteareasGroupsFilter->getColumnList("id,group_name");
            foreach ($remoteareasCheck as $remoteareasGroups) {
                $groupsArray[$remoteareasGroups->getId()] = $remoteareasGroups->getGroupName();
            }

            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = $this->user;
                    $account = $user->getAccount();
                    $new_file_name = $account."_sid".$serviceId. "_" . time() . "_" . $basename;
                     chdir('../');
                    chdir('_assets/service_documents');
                    $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
                    $newDirectoryPath = $currentDirecotryPath . '/remotearea_import/';
                    if (!file_exists($newDirectoryPath)) {
                        @mkdir($newDirectoryPath, 0775);
                    } else {
                        //echo "exists";
                    }

                    $relPath = $newDirectoryPath . $new_file_name;
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = $message = '';
                            $successRecords = 0;
                            $errorRecords = 0;
                            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                if ($row > 1) {
                                    $groupName = trim($data[0]);
                                    $fromWeight = trim($data[1]);
                                    $toWeight = trim($data[2]);
                                    $charges = trim($data[3]);
                                    $formula = trim($data[4]);
                                    $groupID = array_search($groupName, $groupsArray);
                                    if (!empty($groupID)) {
                                        if (trim($serviceObj->getRemotearea()) == "ON_WEIGHT") {
                                            $remoteareaChargesServicesUserFilter = new RemoteareaChargesServicesUserFilter();
                                            $remoteareaChargesServicesUserFilter->addFilter(" remotearea_group_id =" . $groupID . " AND service_id = " . $serviceId . " AND from_weight = " . $fromWeight . " AND to_weight = " . $toWeight . " AND user_account_id =  ".$userAccountId."  ");
                                            $remoteareaChargesServicesUserResults = $remoteareaChargesServicesUserFilter->getList();

                                            if (count($remoteareaChargesServicesUserResults) > 0) {
                                                foreach ($remoteareaChargesServicesUserResults as $remoteareaChargesServicesUser) {
                                                    $remoteareaChargesServicesUser->setRemoteareaCharges($charges);
                                                    $remoteareaChargesServicesUser->setFormulla($formula);
                                                    $remoteareaChargesServicesUser->setUpdatedDate(time());
                                                    $remoteareaChargesServicesUser->setUpdatedBy($userAccount->getId());
                                                    $remoteareaChargesServicesUser->save();
                                                }
                                            } else {
                                                $remoteareaChargesServicesUser = new RemoteareaChargesServicesUser();
                                                $remoteareaChargesServicesUser->setRemoteareaGroupId($groupID);
                                                $remoteareaChargesServicesUser->setRemoteareaCharges($charges);
                                                $remoteareaChargesServicesUser->setServiceId($serviceId);
                                                $remoteareaChargesServicesUser->setFromWeight($fromWeight);
                                                $remoteareaChargesServicesUser->setToWeight($toWeight);
                                                $remoteareaChargesServicesUser->setFormulla($formula);
                                                $remoteareaChargesServicesUser->setUserAccountId($userAccountId);
                                                $remoteareaChargesServicesUser->setAddedBy($userAccount->getId());
                                                $remoteareaChargesServicesUser->setAddedDate(time());
                                                $remoteareaChargesServicesUser->setIsDeleted('N');
                                                $remoteareaChargesServicesUser->save();
                                            }
                                        }
                                        if (trim($serviceObj->getRemotearea()) == "ON_PIECE" || trim($serviceObj->getRemotearea()) == '') {
                                            $remoteareaChargesCarrierUserFilter = new RemoteareaChargesCarrierUserFilter();
                                            $remoteareaChargesCarrierUserFilter->addFilter(" remotearea_group_id =" . $groupID . " AND user_account_id = " . $userAccountId . "   ");
                                            $remoteareaChargesCarrierUserResults = $remoteareaChargesCarrierUserFilter->getList();
                                            if (count($remoteareaChargesCarrierUserResults) > 0) {
                                                foreach ($remoteareaChargesCarrierUserResults as $remoteareaChargesCarrierUser) {
                                                    $remoteareaChargesCarrierUser->setRemoteareaCharges($charges);
                                                    $remoteareaChargesCarrierUser->setFormulla($formula);
                                                    $remoteareaChargesCarrierUser->setUpdatedDate(time());
                                                    $remoteareaChargesCarrierUser->setUpdatedBy($userAccount->getId());
                                                    $remoteareaChargesCarrierUser->save();
                                                }
                                            } else {
                                                $remoteareaChargesCarrierUser = new RemoteareaChargesCarrierUser();
                                                $remoteareaChargesCarrierUser->setRemoteareaGroupId($groupID);
                                                $remoteareaChargesCarrierUser->setUserAccountId($userAccountId);
                                                $remoteareaChargesCarrierUser->setRemoteareaCharges($charges);
                                                $remoteareaChargesCarrierUser->setAddedBy($userAccount->getId());
                                                $remoteareaChargesCarrierUser->setAddedDate(time());
                                                $remoteareaChargesCarrierUser->setIsDeleted('N');
                                                $remoteareaChargesCarrierUser->save();
                                            }
                                        }



                                        $successRecords++;
                                    } else {
                                        $errorRecords++;
                                    }
                                }
                                $row++;
                            }

                            $message .= 'CSV Uploaded Successfully';
                            $message .= '<br /> ' . $successRecords . ' remote area charges updated successfully.';
                            $message .= '<br /> ' . $errorRecords . ' remote area charges not imported due to invalid group name.';

                            $output['message'] = $message;
                            $output['status'] = 'success';
                        }
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

        /*Services*/
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_remotearea_charges') {
            $userAccount = $this->user;
            $serviceId = (int)trim($this->form_vars['service_id']);
            $groupListStr = "";
            if ($serviceId > 0) {
                $serviceObjNew = new Services($serviceId);
                $carrierId = $serviceObjNew->getCarrierId();
                if ($carrierId > 0) {
                    $remoteareasGroupsFilter = new RemoteareasGroupsFilter;
                    $remoteareasGroupsFilter->addFilter("is_deleted = 'N'");
                    $remoteareasGroupsFilter->addFilter("carrier_id = " . $carrierId." ");
                    
                    $remoteareasGroupsList = $remoteareasGroupsFilter->getColumnList("id");
                    foreach ($remoteareasGroupsList as $remoteareasGroupsSng) {
                        $groupListStr .= "'" . $remoteareasGroupsSng->getId() . "',";
                    }
                }
            }
            $groupListNew = rtrim($groupListStr, ',');
            $userAccountId = trim($this->form_vars['user_account_id']);
            $remoteareasType = trim($this->form_vars['remoteareas_type']);

            parse_str($this->form_vars['group'], $group);
            parse_str($this->form_vars['from_weight'], $fromWeight);
            parse_str($this->form_vars['to_weight'], $toWeight);
            parse_str($this->form_vars['charges'], $charges);
            parse_str($this->form_vars['formula'], $formula);
            //Delete pervious records
            $oldRemoteAreaChargesData = new RemoteareaChargesServicesUserFilter();
            $oldRemoteAreaChargesData->addFilter('service_id = ' . $serviceId);
            $oldRemoteAreaChargesData = $oldRemoteAreaChargesData->getList();

            $oldRemoteAreaCarrierData = new RemoteareaChargesCarrierUserFilter();
            $oldRemoteAreaCarrierData->addFilter("user_account_id = " . DbAccess3::escape($userAccountId));
            $oldRemoteAreaCarrierData->addFilter("remotearea_group_id IN(" . $groupListNew . ")");
            $oldRemoteAreaCarrierData = $oldRemoteAreaCarrierData->getList();

            $oldAuditData = [];
            $newAuditData = [];
            if(!empty($oldRemoteAreaChargesData)) {
                foreach ($oldRemoteAreaChargesData as $datum) {
                    $groupObj = new RemoteareasGroups($datum->getRemoteareaGroupId());
                    $oldAuditData[] = "Group Name:" . $groupObj->getGroupName() . '<br />Charges: ' . $datum->getRemoteareaCharges() . '<br />From Weight: ' . $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight();
                }
            }
            if(!empty($oldRemoteAreaCarrierData)) {
                foreach ($oldRemoteAreaCarrierData as $datum) {
                    $groupObj = new RemoteareasGroups($datum->getRemoteareaGroupId());
                    $oldAuditData[] = "Group Name:" . $groupObj->getGroupName() . '<br />Charges: ' . $datum->getRemoteareaCharges() . '<br />From Weight: ' . $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight();
                }
            }

            RemoteareaChargesServicesUser::deleteByServiceId($serviceId);
            RemoteareaChargesCarrierUser::deleteByUserIdNGroupId($userAccountId, $groupListNew);
            if (trim($serviceObjNew->getRemotearea()) == "ON_WEIGHT") {
                foreach ($group['group'] as $key => $groupList) {
                    if (!empty($groupList)) {
                        $remoteareaChargesServicesUser = new RemoteareaChargesServicesUser();
                        $remoteareaChargesServicesUser->setRemoteareaGroupId($groupList);
                        $remoteareaChargesServicesUser->setRemoteareaCharges($charges['charges'][$key]);
                        $remoteareaChargesServicesUser->setServiceId($serviceId);
                        $remoteareaChargesServicesUser->setFromWeight($fromWeight['from_weight'][$key]);
                        $remoteareaChargesServicesUser->setToWeight($toWeight['to_weight'][$key]);
                        $remoteareaChargesServicesUser->setFormulla($formula['formula'][$key]);
                        $remoteareaChargesServicesUser->setUserAccountId($userAccountId);
                        $remoteareaChargesServicesUser->setAddedBy($userAccount->getId());
                        $remoteareaChargesServicesUser->setAddedDate(time());
                        $remoteareaChargesServicesUser->setIsDeleted('N');
                        $remoteareaChargesServicesUser->save();
                        $groupObj = new RemoteareasGroups($groupList);
                        $newAuditData[] = "Group Name:" . $groupObj->getGroupName() . '<br />Charges: ' . $charges['charges'][$key] . '<br />From Weight: ' . $fromWeight['from_weight'][$key] . '<br />To Weight: ' . $toWeight['to_weight'][$key];
                    }
                }
            }
            if (trim($serviceObjNew->getRemotearea()) == "ON_PIECE") {
                foreach ($group['group'] as $key => $groupList) {
                    if (!empty($groupList)) {
                        $remoteareaChargesCarrierUser = new RemoteareaChargesCarrierUser();
                        $remoteareaChargesCarrierUser->setRemoteareaGroupId($groupList);
                        $remoteareaChargesCarrierUser->setUserAccountId($userAccountId);
                        $remoteareaChargesCarrierUser->setRemoteareaCharges($charges['charges'][$key]);
                        $remoteareaChargesCarrierUser->setAddedBy($userAccount->getId());
                        $remoteareaChargesCarrierUser->setAddedDate(time());
                        $remoteareaChargesCarrierUser->setIsDeleted('N');
                        $remoteareaChargesCarrierUser->save();
                        $groupObj = new RemoteareasGroups($groupList);
                        $newAuditData[] = "Group Name:" . $groupObj->getGroupName() . '<br />Charges: ' . $charges['charges'][$key] . '<br />From Weight: <br />To Weight:';
                    }
                }
            }
            $serviceObj = new Services($serviceId);
            $new_data_p = [];
            $old_data_p = [];
            if(!empty($oldAuditData)) {
                $old_data_p[$serviceObj->getName()] = implode('<br /><br />', $oldAuditData);
            }
            if(!empty($newAuditData)) {
                $new_data_p[$serviceObj->getName()] = implode('<br /><br />', $newAuditData);
            }
            $old_data = json_encode($old_data_p);
            $new_data = json_encode($new_data_p);
            $userAudit = new UserAudit();
            $table_key = !empty($userAccountId) ? $userAccountId : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'insert', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $new_data, $table_key, $old_data, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service Setting for service ' . $serviceObj->getName());
            $output["status"] = "success";
            $output["message"] = "Remotearea saved successfully";
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == 'save_services') {
            //Get Already inserted services to set is_agreed check
            $latestId = $this->form_vars['id'];
            $isAgreedCarrier = [];
            $allSavedServices = [];
            $userServicesRoutingFilter = new UserServicesRoutingFilter();
            $userServicesRoutingFilter->addFilter(" user_account_id = " . DbAccess3::escape($latestId));
            $userServicesRoutingFilter->setGroup(" service_id");
            $userServicesRoutingList = $userServicesRoutingFilter->getList();
            if (count($userServicesRoutingList) > 0) {
                foreach ($userServicesRoutingList as $userServicesRoutingServiceId) {
                    $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['country'][] = $userServicesRoutingServiceId->getCountryId();
                    $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['from_weight'] = $userServicesRoutingServiceId->getFromWeight();
                    $allSavedServices[$userServicesRoutingServiceId->getServiceId()]['to_weight'] = $userServicesRoutingServiceId->getToWeight();
                    if ($userServicesRoutingServiceId->getIsAgreed()) {
                        $serviceObj = new Services($userServicesRoutingServiceId->getServiceId());
                        $isAgreedCarrier[] = $serviceObj->getCarrierId();
                    }
                }
            }
            //Delete previous user service routing data
            UserServicesRouting::deleteServicesByUserId($latestId);

            //Save user service routing data
            foreach ($this->form_vars['agent_services'] as $serviceId) {
                $serviceObj = new Services($serviceId);
                //Check if service is product type then add without countries
                if ($serviceObj->getIsCustomized() == 1) {
                    $psrupdate = new UserServicesRouting();
                    $psrupdate->setServiceId($serviceObj->getId());
                    $psrupdate->setStatus(1);
                    $psrupdate->setUserAccountId($latestId);
                    $psrupdate->setAddedBy($sessionUser->getId());
                    $psrupdate->save();
                } else {
                    $user_service_remotearea = (isset($this->form_vars['user_service_remotearea'][$serviceId]) ? 1 : 0);
                    $serviceCountries = [];
                    $fromWeight = 0;
                    $toWeight = 0;
                    if (isset($this->form_vars['user_service_country'][$serviceId])) {
                        $serviceCountries = $this->form_vars['user_service_country'][$serviceId];
                        $fromWeight = $this->form_vars['user_from_weight'][$serviceId];
                        $toWeight = $this->form_vars['user_to_weight'][$serviceId];
                    } else if (isset($allSavedServices[$serviceId])) {
                        $serviceCountries = $allSavedServices[$serviceId]['country'];
                        $fromWeight = $allSavedServices[$serviceId]['from_weight'];
                        $toWeight = $allSavedServices[$serviceId]['to_weight'];
                    } else {
                        $fromWeight = $serviceObj->getFromWeight();
                        $toWeight = $serviceObj->getToWeight();
                        // fetch service countries
                        $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
                        $serviceCountryTimeFilter->addServiceTableJoin();
                        $serviceCountryTimeFilter->addFilter(' `service_country_ttime`.id_service =' . $serviceId);
                        $serviceCountryTimeFilter->setRowsPerPage("300");
                        $serviceCountryArr = $serviceCountryTimeFilter->getList();
                        foreach ($serviceCountryArr as $serviceCountry) {
                            $serviceCountries[] = $serviceCountry->getIdCountry();
                        }
                    }
                    $isAgreedCarrier = array_unique($isAgreedCarrier);
                    foreach ($serviceCountries as $countryId) {
                        $psrupdate = new UserServicesRouting();
                        $currentCarrierId = $serviceObj->getCarrierId();
                        if (in_array($currentCarrierId, $isAgreedCarrier)) {
                            $psrupdate->setIsAgreed(1);
                        }
                        $psrupdate->setServiceId($serviceId);
                        $psrupdate->setStatus(1);
                        $psrupdate->setIsRemotearea($user_service_remotearea);
                        $psrupdate->setToWeight($toWeight);
                        $psrupdate->setFromWeight($fromWeight);
                        $psrupdate->setCountryId($countryId);
                        $psrupdate->setUserAccountId($latestId);
                        $psrupdate->setAddedBy($sessionUser->getId());
                        $psrupdate->save();
                    }
                }
            }
        }
        //        Handle Filter Services
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter_services') {
            $servicesFilter = new ServiceFilter();
            $sCountry = $this->form_vars['s_country'];
            $dCountry = $this->form_vars['d_country'];
            $carrierId = $this->form_vars['carrier_id'];
            $serviceType = $this->form_vars['des_service_type'];
            $accountId = $this->form_vars['account_id'];

            $servicesLists = $servicesFilter->getFilterServices($sCountry, $dCountry, $serviceType, $carrierId, $accountId);
            if (isset($this->form_vars['services']) && !empty($this->form_vars['services']))
                $services = explode(',', $this->form_vars['services']);
            $html = "";
            if (is_array($servicesLists)) {
                foreach ($servicesLists as $servicesList) {
                    $selected = "";
                    if (in_array($servicesList->getId(), $services))
                        $selected = "selected=selected";

                    $html .= '<option ' . $selected . ' value="' . $servicesList->getId() . '"  >' . $servicesList->getName() . '</option>';
                }
            }
            $html .= ':::';
            echo $html;
            die;
        }
        /*
         * Save Countries
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveCountries') {
            $countriesArr = [];
            parse_str($_POST['countries'], $countriesArr);
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $fromWeight = $this->form_vars['from_weight'];
            $toWeight = $this->form_vars['to_weight'];

            $isAgreedCarrier = [];
            $allSavedServices = [];
            $userServicesRoutingFilter = new UserServicesRoutingFilter();
            $userServicesRoutingFilter->addFilter(" user_account_id = " . DbAccess3::escape($accountId) . " AND service_id = " . DbAccess3::escape($serviceId));
            $userServicesRoutingFilter->setLimit(1);
            $isAgreedObj = $userServicesRoutingFilter->getColumnList("is_agreed,is_remotearea");
            $isAgreedCheck = 0;
            $isRemoteareaCheck = 0;
            if (count($isAgreedObj)) {
                $isAgreedCheck = $isAgreedObj[0]->getIsAgreed();
                $isRemoteareaCheck = $isAgreedObj[0]->getIsRemotearea();
            }
            $oldCountryIds = [];
            $oldCountryName = [];
            $newCountryName = [];
            $serviceCountries = $countriesArr['user_service_country'][$serviceId];
            //Delete previous user service routing data
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter('user_account_id = ' . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter('service_id = ' . DbAccess3::escape($serviceId));
            $userRoutingObj = $userRoutingObj->getList();
            if(!empty($userRoutingObj)) {
                foreach ($userRoutingObj as $userObj) {
                    $oldCountryIds[] = $userObj->getCountryId();
                    if(!in_array($userObj->getCountryId(), $serviceCountries)) {
                        $countryObj = new Country($userObj->getCountryId());
                        $oldCountryName[] = $countryObj->getName();
                    }
                }

            }

            UserServicesRouting::deleteServicesByUserId($accountId, $serviceId);
            foreach ($serviceCountries as $countryId) {
                $psrupdate = new UserServicesRouting();
                $psrupdate->setIsAgreed($isAgreedCheck);
                $psrupdate->setServiceId($serviceId);
                $psrupdate->setStatus(1);
                $psrupdate->setIsRemotearea($isRemoteareaCheck);
                $psrupdate->setToWeight($toWeight);
                $psrupdate->setFromWeight($fromWeight);
                $psrupdate->setCountryId($countryId);
                $psrupdate->setUserAccountId($accountId);
                $psrupdate->setAddedBy($this->user->getId());
                $psrupdate->save();
                $countryObj = new Country($countryId);
                if(!in_array($countryId, $oldCountryIds)) {
                    $newCountryName[] = $countryObj->getName();
                }
            }
            $newDataAudit = [];
            $oldDataAudit = [];
            $serviceObj = new Services($serviceId);
            if(!empty($oldCountryName)) {
                $oldDataAudit['countries'] = implode('<br>', $oldCountryName);
            }
            if(!empty($newCountryName)) {
                $newDataAudit['countries'] = implode('<br>', $newCountryName);
            }
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty($accountId) ? $accountId : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated User Remote Areas of Service ' . $serviceObj->getName());
            echo "Service countries added successfully";
            die;
        }


        /*
         * Add remotearea check
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_service_label_charges') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $label_charges = (float)$this->form_vars['label_charges'];
            //TODO:// check multi data
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter("user_account_id = " . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter("service_id = " . DbAccess3::escape($serviceId));
            $userRoutingObj->setLimit(1);
            $userRoutingObj = $userRoutingObj->getList();
            $serviceObj = new Services($serviceId);
            $userAccountObj = new CustomerAccount($this->form_vars['account_id']);
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
                'label_charges' => $userRoutingObj[0]->getLabelCharges(),
            ];
            UserServicesRouting::runQuery("UPDATE user_services_routing SET label_charges = " . $label_charges . " WHERE user_account_id = '" . DbAccess3::escape($accountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ");

            $newDataAudit = [
                'service_name' => $serviceObj->getName(),
                'label_charges' => $this->form_vars['label_charges'],
            ];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service setting of ' . $serviceObj->getName());
            echo "Label charges updated successfully";
            die;
        }


        /*
         * Add remotearea check
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_remotearea') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $isRemotearea = (int)$this->form_vars['is_remotearea'];
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter("user_account_id = " . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter("service_id = " . DbAccess3::escape($serviceId));
            $userRoutingObj->setLimit(1);
            $userRoutingObj = $userRoutingObj->getList();
            $serviceObj = new Services($serviceId);
            if($userRoutingObj[0]->getIsRemotearea() == 1) {
                $remoteAreaText = 'yes';
            } else {
                $remoteAreaText = 'no';
            }
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_remotearea' => $remoteAreaText,
            ];
            $sqlQuery = "UPDATE user_services_routing SET is_remotearea = " . $isRemotearea . " WHERE user_account_id = '" . DbAccess3::escape($accountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ";
            UserServicesRouting::runQueryWithError($sqlQuery,true);
            if($isRemotearea == 1) {
                $remoteAreaText = 'yes';
            } else {
                $remoteAreaText = 'no';
            }
            $newDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_remotearea' => $remoteAreaText,
            ];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service setting of ' . $serviceObj->getName());
            echo "Remotearea updated successfully";
            die;
        }
        /*
         * Add over label check
         */
        //
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_over_size') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $isOverSize = (int)$this->form_vars['is_over_size'];
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter("user_account_id = " . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter("service_id = " . DbAccess3::escape($serviceId));
            $userRoutingObj->setLimit(1);
            $userRoutingObj = $userRoutingObj->getList();
            $serviceObj = new Services($serviceId);
            if($userRoutingObj[0]->getIsOverSize() == 1) {
                $isOverSizeText = 'yes';
            } else {
                $isOverSizeText = 'no';
            }
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_over_size' => $isOverSizeText,
            ];
            echo 
            UserServicesRouting::runQuery("UPDATE user_services_routing SET is_over_size = " . $isOverSize . " WHERE user_account_id = '" . DbAccess3::escape($accountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ");
            if($isOverSize == 1) {
                $isOverSizeText = 'yes';
            } else {
                $isOverSizeText = 'no';
            }
            $newDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_over_size' => $isOverSizeText,
            ];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service setting of ' . $serviceObj->getName());
            echo "Over Size updated successfully";
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_over_label') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $isOverLabel = (int)$this->form_vars['is_over_label'];
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter("user_account_id = " . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter("service_id = " . DbAccess3::escape($serviceId));
            $userRoutingObj->setLimit(1);
            $userRoutingObj = $userRoutingObj->getList();
            $serviceObj = new Services($serviceId);
            if($userRoutingObj[0]->getIsOverLabel() == 1) {
                $isOverLabelText = 'yes';
            } else {
                $isOverLabelText = 'no';
            }
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_over_label' => $isOverLabelText,
            ];
            UserServicesRouting::runQuery("UPDATE user_services_routing SET is_over_label = " . $isOverLabel . " WHERE user_account_id = '" . DbAccess3::escape($accountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ");
            if($isOverLabel == 1) {
                $isOverLabelText = 'yes';
            } else {
                $isOverLabelText = 'no';
            }
            $newDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_over_label' => $isOverLabelText,
            ];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service setting of ' . $serviceObj->getName());
            echo "Over Label updated successfully";
            die;
        }

        /*
         * Add over label check
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_is_dead_weight') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $isDeadWeight = (int)$this->form_vars['is_dead_weight'];
            $userRoutingObj = new UserServicesRoutingFilter();
            $userRoutingObj->addFilter("user_account_id = " . DbAccess3::escape($accountId));
            $userRoutingObj->addFilter("service_id = " . DbAccess3::escape($serviceId));
            $userRoutingObj->setLimit(1);
            $userRoutingObj = $userRoutingObj->getList();
            $serviceObj = new Services($serviceId);
            if($userRoutingObj[0]->getIsDeadWeight() == 1) {
                $isDeadWeightText = 'Default';
            } else {
                $isDeadWeightText = 'Dead Weight';
            }
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_dead_weight' => $isDeadWeightText,
            ];
            UserServicesRouting::runQuery("UPDATE user_services_routing SET is_dead_weight  = " . $isDeadWeight . " WHERE user_account_id = '" . DbAccess3::escape($accountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ");
            if($isDeadWeight == 1) {
                $isDeadWeightText = 'Default';
            } else {
                $isDeadWeightText = 'Dead Weight';
            }
            $newDataAudit = [
                'service_name' => $serviceObj->getName(),
                'is_dead_weight' => $isDeadWeightText,
            ];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'update', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $tableKey, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' updated Service setting for ' . $serviceObj->getName());
            echo "Shipment will charge on dead weight, updated successfully";
            die;
        }

        /*
         * Delete service data
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_service') {
            $serviceId = (int)$this->form_vars['service_id'];
            $accountId = (int)$this->form_vars['account_id'];
            $serviceObj = new Services($serviceId);
            $accountObj = new CustomerAccount($accountId);
            $oldDataAudit = [
                'service_name' => $serviceObj->getName(),
            ];
            $newDataAudit = [];
            $oldData = json_encode($oldDataAudit);
            $newData = json_encode($newDataAudit);
            $userAudit = new UserAudit();
            $tableKey = !empty(DbAccess3::escape($accountId)) ? DbAccess3::escape($accountId) : '0';
            $userAudit->allowAdd = true;
            $userAudit->insertAuditData('user_account', 'delete', $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName(), $sessionUser->getId(), $newData, $accountId, $oldData, $sessionUser->getFirstName() . ' ' . $sessionUser->getLastName() . ' deleted service ' . $serviceObj->getName() . ' from account ' . $accountObj->getUserAccount());
            //Delete previous user service routing data
            UserServicesRouting::deleteServicesByUserId($accountId, $serviceId);
            echo "Service deleted successfully";
            die;
        }
        $this->serviceCustomizeRulesList = "";
        
        $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
        $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = " . $id);
        $carrierServiceCustomizeRulesFilter->AddOrderBy('serviceid');
        $this->serviceCustomizeRulesList = $carrierServiceCustomizeRulesFilter->getList();
        //Get Service List data
        $this->serviceList = array();
        $UserServicesRoutingFilter = new UserServicesRoutingFilter();
        $UserServicesRoutingFilter->addUserAccountIdFilter($id);
        $UserServicesRoutingFilter->addFieldFilter("status", "1");
        $UserServicesRoutingFilter->setGroup("service_id");
        $UserServicesRoutingFilter->setLimit(5000);
        $serviceList = $UserServicesRoutingFilter->getColumnList('service_id');
        foreach ($serviceList as $service) {
            $this->serviceList[] = $service->getServiceId();
        }
        //            Get Agent List data
        $this->agentList = "";
        if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
            $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
            $carrierServiceCustomizeRulesFilter->addFilter("user_account_id =" . intval($id));
            $this->agentList = $carrierServiceCustomizeRulesFilter->getList();
        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <style>
            .select2 {
                width: 100% !important;
            }

            a.disabled {
                pointer-events: none;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        $sessionUser = $this->user;
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(document).ajaxStart(loadAjax("load")).ajaxStop(loadAjax("close"));
                var services = [];
                <?php
                if(!empty($this->serviceList)){
                foreach($this->serviceList as $serviceId){
                ?>
                services.push('<?php echo $serviceId;?>');
                <?php
                }
                }
                ?>
                loadServiceRow(services, 'only_load_data');
                $('.multiselect_drop_down').multiSelect({
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
                    afterSelect: function (values) {
                        this.qs1.cache();
                        this.qs2.cache();
                        loadServiceRow(values);
                    },
                    afterDeselect: function (values) {
                        this.qs1.cache();
                        this.qs2.cache();
                        loadServiceRow(values);
                    }
                });
                //Remove services Handle
                $(document).on('click', '.repeater_delete_service', function () {
                    var service_id = 0;
                    var my_this = $(this);
                    service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    swal({
                            title: "Are you sure you want to remove service?",
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
                                    url: "add_services.php",
                                    type: "post",
                                    data: {action: "remove_service", service_id: service_id, account_id: accountId},
                                    success: function (data) {
                                        $('#services_tbl [data-service-id="' + service_id + '"]').remove();
                                        $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                                        $("#response_message_alert").html(" ");
                                        $('#response_message_alert').html(data);
                                        $('#response_message_alert').show();
                                        $("#agent_services_temp").children('option[value="' + service_id + '"]').removeAttr('selected');
                                        disableServices();
                                    }
                                });
                            }
                        });
                });
                //    Agent tab script code
                $(document).on('click', '.repeater-add', function () {
                    var index_of_agent = $(".show_remove_btn").map(function () {
                        return $(this).data('index-of-agent');
                    }).get();//get all data values in an array
                    var highest_index_of_agent = Math.max.apply(Math, index_of_agent);//find the highest value from them
                    highest_index_of_agent = parseInt(highest_index_of_agent) + 1;
                    var set_first = false;
                    if ($('.clone_div :radio').is(":checked")) {
                        set_first = true;
                    }
                    $("#advanceservice .clone_div").children().clone().appendTo("#advanceservice .append_here");
                    $('.append_here .row').last().attr('data-index-agent', highest_index_of_agent);
                    $('.append_here .row .show_remove_btn').last().attr('data-index-of-agent', highest_index_of_agent);
                    $('.append_here .row .show_remove_btn').show();
                    if (set_first) {
                        $('.clone_div :radio').prop("checked", true);
                    }
                    setInputFeilds(highest_index_of_agent);
                });
                $(document).on('click', '.show_remove_btn', function () {
                    var current_index = $(this).data('index-of-agent');
                    swal({
                            title: "Are you sure you want to remove this?",
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
                                $('.append_here [data-index-agent="' + current_index + '"]').remove();
                            }
                        });
                });
                //Handle User service weight and country save functionality
                $(document).on('click', '.save_service_agent_data', function () {
                    var myThis = $(this);
                    var serviceId = $.trim(myThis.attr('data-service-id'));
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var weightFrom = $('.agent_from_weight').serialize();
                    var weightTo = $('.agent_to_weight').serialize();
                    var agentSelect = $('.agent_select').serialize();
                    $.ajax({
                        url: 'add_services.php',
                        type: 'POST',
                        dataType: "html",
                        data: {
                            action: 'check_weight_limit',
                            weight_from: weightFrom,
                            weight_to: weightTo,
                            agent_select: agentSelect,
                            service_id: serviceId,
                            account_id: accountId
                        },
                        headers: {},
                        success: function (data) {
                            if (data == 'error') {
                                $("#res_message").html("Please correct the weights according to the limits or fill the required fields");
                                $("#res_message").show();
                            } else {
                                var serviceId = $.trim(myThis.attr('data-service-id'));
                                if ($('#apend_service_mode_data div[data-service-id="' + serviceId + '"]').length > 0) {
                                    $('#apend_service_mode_data div[data-service-id="' + serviceId + '"]').remove();
                                }
                                $("#apend_service_mode_data").append("<div id='service_id" + serviceId + "' data-service-id='" + serviceId + "'></div>");
                                $('#advanceservice  select').each(function (i, v) {
                                    $("#apend_service_mode_data #service_id" + serviceId).append('<input type="hidden" name="agent[' + serviceId + '][' + i + ']" value="' + $(this).val() + '" />');
                                });

                                $('#advanceservice  .agent_from_weight').each(function (i, v) {
                                    $("#apend_service_mode_data #service_id" + serviceId).append('<input type=    "hidden" name="from_weight[' + serviceId + '][' + i + ']" value="' + $(this).val() + '" />');
                                });

                                $('#advanceservice  .agent_to_weight').each(function (i, v) {
                                    $("#apend_service_mode_data #service_id" + serviceId).append('<input type="hidden" name="to_weight[' + serviceId + '][' + i + ']" value="' + $(this).val() + '" />');
                                });
                                $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                                $("#response_message_alert").html(" ");
                                $('#response_message_alert').html("Agent weight limits added successfully");
                                $('#response_message_alert').show();
                                $('#myModalAdvance').modal('hide');
                                $('#res_message').hide();
                            }
                        },
                        error: function (xhr, status, error) {
                            $("#res_message").html("Please correct the weights according to the limits or fill the required fields");
                            $("#res_message").show();
                        }
                    });
                });
                $(document).on('click', '#btnSave', function () {
                    $("#adminForm").submit();
                });
                //Save remotearea functionality
                $(document).on('click', '.save_service_remotearea_data', function () {
                    var myThis = $(this);
                    var $nonempty = $('.validate_check').filter(function () {
                        if (!$(this).val()) {
                            $(this).parents(".input-group").css('border', '1px solid red');
                        } else {
                            $(this).parents(".input-group").css('border', '0px');
                        }
                        return !$(this).val();
                    });
                    if ($nonempty.length == 0) {
                        var serviceId = $(this).attr("data-service_id");
                        var remoteareasType = $(this).attr("data-remoteares_type");
                        var remoteareasSaveType = $(this).attr("data-remoteares_save_type");
                        var userAccountId = $("#id").val();
                        var group = $('.remotearea_group').serialize();
                        var fromWeight = $('.remotearea_from_weight').serialize();
                        var toWeight = $('.remotearea_to_weight').serialize();
                        var charges = $('.remotearea_charges').serialize();
                        var formula = $('.remotearea_formula').serialize();
                        if (remoteareasSaveType == "ON_PIECE") {
                            var check = checkit();
                            if (check == true) {
                                $.ajax({
                                    url: 'add_services.php',
                                    type: 'POST',
                                    dataType: "json",
                                    data: {
                                        action: 'save_remotearea_charges',
                                        service_id: serviceId,
                                        group: group,
                                        from_weight: fromWeight,
                                        to_weight: toWeight,
                                        charges: charges,
                                        formula: formula,
                                        user_account_id: userAccountId,
                                        remoteareas_type: remoteareasSaveType
                                    },
                                    headers: {},
                                    success: function (data) {
                                        if (data.status == 'success') {
                                            $("#res_message_" + serviceId).addClass('alert-success').removeClass('alert-danger');
                                            $("#res_message_" + serviceId).html("");
                                            $("#res_message_" + serviceId).html(data.message);
                                            $("#res_message_" + serviceId).show();
                                            myThis.attr('data-remotearea-saved', 'ok');
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        $("#res_message").html("Please correct the weights according to the limits");
                                        $("#res_message").show();
                                    }
                                });
                            }
                        } else {
                            $.ajax({
                                url: 'add_services.php',
                                type: 'POST',
                                dataType: "json",
                                data: {
                                    action: 'save_remotearea_charges',
                                    service_id: serviceId,
                                    group: group,
                                    from_weight: fromWeight,
                                    to_weight: toWeight,
                                    charges: charges,
                                    formula: formula,
                                    user_account_id: userAccountId,
                                    remoteareas_type: remoteareasType
                                },
                                headers: {},
                                success: function (data) {
                                    if (data.status == 'success') {
                                        $("#res_message_" + serviceId).addClass('alert-success').removeClass('alert-danger');
                                        $("#res_message_" + serviceId).html("");
                                        $("#res_message_" + serviceId).html(data.message);
                                        $("#res_message_" + serviceId).show();
                                        myThis.attr('data-remotearea-saved', 'ok');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    $("#res_message").html("Please correct the weights according to the limits");
                                    $("#res_message").show();
                                }
                            });
                        }
                    } else {
                        swal("", "Something went wrong!", "error");
                    }
                });
                $(document).on('click', '#download_sevices_remotarea_template', function () {
         
                $('#download_csv_serviceId').val( $('#upload_service_id').val());
                $('#download_csv_carrierId').val($('#upload_carrier_id').val());
                $('#download_services_remotearea_template_form').submit();
            }); 
                $(document).on('click', '#btnSubmitImportRemoteArea', function () {
                var file_data = $('#services_upload_remote_file').prop('files')[0];
                var serviceId = $('#upload_service_id').val();
                var carrierId = $('#upload_carrier_id').val();
                var userAccountId = $('#upload_useraccount_id').val();
                var remoteAreaServiceType = $('#upload_retmotearea_service_type').val();
                var form_data = new FormData();
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file_remotearea');
                form_data.append('serviceId', serviceId);
                form_data.append('carrierId', carrierId);
                form_data.append('userAccountId', userAccountId)
                form_data.append('remoteAreaServiceType', remoteAreaServiceType);
                if (serviceId == "" || serviceId == null) {
                    swal("error", "Your service id not found!","error");
                } else {
                    if (serviceId > 0) {
                        $.ajax({
                            url: 'add_services.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                     
                                 getRemoteareaService(serviceId, carrierId, remoteAreaServiceType, userAccountId,response.status,response.message);
                            }
                        });
                    } else {
                        $("#btnSubmitImport").show();
                        swal("Sorry!", "Please select the user first", "error");
                    }
                }
            });
                //    Handle User Country / weight assign model
                $(document).on('click', '.users_weight_limit', function () {
                    var serviceId = $(this).data("user-service-id");
                    var myThis = $(this);
                    var serviceName = $(this).data("user-service-name");
                    var acccoutId = "<?php echo (int)trim($this->account_id); ?>";
                    $("#users_weight_limit_model_" + serviceId).html(" ");
                    $.ajax({
                        url: 'add_services.php',
                        type: 'POST',
                        dataType: "html",
                        data: {action: 'get_countries_list', service_id: serviceId, acccout_id: acccoutId},
                        headers: {},
                        success: function (data) {
                            $("#users_weight_limit_model_" + serviceId).append(data);
                            $("#users_weight_limit_model_" + serviceId).modal("show");
                            $('.icheck').iCheck({
                                checkboxClass: 'icheckbox_minimal-grey',
                                radioClass: 'iradio_minimal-grey'
                            });
                        },
                        error: function (xhr, status, error) {

                        }
                    });

                });
                //    Save User weight limit model
                $(document).on('click', '.save_user_agent_data', function () {
                    var validation = 1;
                    $('.validate_check').filter(function () {
                        if (!$(this).val()) {
                            $(this).parents(".input-group").css('border', '1px solid red');
                            validation = 0;
                        } else {
                            $(this).parents(".input-group").css('border', '0px');
                            if (!$.isNumeric($(this).val())) {
                                validation = 0;
                            }
                        }
                        return !$(this).val();
                    });
                    if (validation == 1) {
                        var serviceId = $(this).attr("data-service_id");
                        var accountId = "<?php echo (int)trim($this->account_id); ?>";
                        var formData = new FormData();
                        formData.append('countries', $("#users_weight_limit_model_" + serviceId + " .icheck:checkbox:checked").serialize());
                        formData.append('service_id', serviceId);
                        formData.append('account_id', accountId);
                        formData.append('from_weight', $("#user_from_weight_" + serviceId).val());
                        formData.append('to_weight', $("#user_to_weight_" + serviceId).val());
                        formData.append('action', 'saveCountries');
                        $.ajax({
                            url: "add_services.php",
                            type: "post",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                                $("#response_message_alert").html(" ");
                                $('#response_message_alert').html(data);
                                $('#response_message_alert').show();
                            }
                        });
                        $('#users_weight_limit_model_' + serviceId).modal('hide');
                    } else {
                        swal("Sorry!", "Please fill the required fields correctly", "error");
                    }
                });


                $(document).on('keyup', '.user_service_label_charges', function (event, state) {
                    var service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var label_charges = $(this).val();

                    $.ajax({
                        url: "add_services.php",
                        type: "post",
                        data: {
                            action: "add_service_label_charges",
                            service_id: service_id,
                            account_id: accountId,
                            label_charges: label_charges
                        },
                        success: function (data) {
                            $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                            $("#response_message_alert").html(" ");
                            $('#response_message_alert').html(data);
                            $('#response_message_alert').show();
                        }
                    });
                });

//                $(document).on('switchChange.bootstrapSwitch', '.user_service_remotearea', function (event, state) {
                $(document).on('ifChanged', '.user_service_remotearea', function (event, state) {
                    var service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var isRemotearea = 0;
                    var checkboxChecked = $(this).is(':checked');
                    if(checkboxChecked) {
                      //  $("#span_user_service_remotearea_" + service_id).show();
                        isRemotearea = 1;
                    } else {
                       // $("#span_user_service_remotearea_" + service_id).hide();
                    }
                    $.ajax({
                        url: "add_services.php",
                        type: "post",
                        data: {
                            action: "add_remotearea",
                            service_id: service_id,
                            account_id: accountId,
                            is_remotearea: isRemotearea
                        },
                        success: function (data) {
                            $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                            $("#response_message_alert").html(" ");
                            $('#response_message_alert').html(data);
                            $('#response_message_alert').show();
                        }
                    });
                });
                
              //  $(document).on('switchChange.bootstrapSwitch', '.user_service_over_size', function (event, state) {
                $(document).on('ifChanged', '.user_service_over_size', function (event, state) {
                    var service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var isOverSize = 0;
                    var checkboxChecked = $(this).is(':checked');
                    if(checkboxChecked) {                        
                        //$("#span_user_service_over_size_" + service_id).show();
                        isOverSize = 1;
                    } else {
                       // $("#span_user_service_over_size_" + service_id).hide();
                    }
                    $.ajax({
                        url: "add_services.php",
                        type: "post",
                        data: {
                            action: "add_over_size",
                            service_id: service_id,
                            account_id: accountId,
                            is_over_size: isOverSize
                        },
                        success: function (data) {
                            $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                            $("#response_message_alert").html(" ");
                            $('#response_message_alert').html(data);
                            $('#response_message_alert').show();
                        }
                    });
                });
                
                //$(document).on('switchChange.bootstrapSwitch', '.user_service_over_label', function (event, state) {
                $(document).on('ifChanged', '.user_service_over_label', function (event, state) {
                    var service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var isOverLabel = 0;
                    var checkboxChecked = $(this).is(':checked');
                    if(checkboxChecked) {
                       // $("#span_user_service_remotearea_" + service_id).show();
                        isOverLabel = 1;
                    } else {
                       // $("#span_user_service_remotearea_" + service_id).hide();
                    }
                    $.ajax({
                        url: "add_services.php",
                        type: "post",
                        data: {
                            action: "add_over_label",
                            service_id: service_id,
                            account_id: accountId,
                            is_over_label: isOverLabel
                        },
                        success: function (data) {
                            $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                            $("#response_message_alert").html(" ");
                            $('#response_message_alert').html(data);
                            $('#response_message_alert').show();
                        }
                    });
                });

               // $(document).on('switchChange.bootstrapSwitch', '.user_service_dead_weight', function (event, state) {
                $(document).on('ifChanged', '.user_service_dead_weight', function (event, state) {
                    var service_id = $(this).data("service_id");
                    var accountId = "<?php echo (int)trim($this->account_id); ?>";
                    var isDeadWeight = 0;
                    var checkboxChecked = $(this).is(':checked');
                    if(checkboxChecked) {
                        //$("#span_user_service_dead_weight_" + service_id).show();
                        isDeadWeight = 1;
                    } else {
                       // $("#span_user_service_dead_weight_" + service_id).hide();
                    }
                    $.ajax({
                        url: "add_services.php",
                        type: "post",
                        data: {
                            action: "add_is_dead_weight",
                            service_id: service_id,
                            account_id: accountId,
                            is_dead_weight: isDeadWeight
                        },
                        success: function (data) {
                            $("#response_message_alert").addClass('alert-success').removeClass('alert-danger');
                            $("#response_message_alert").html(" ");
                            $('#response_message_alert').html(data);
                            $('#response_message_alert').show();
                        }
                    });
                });

                //    Remotearea add more button script code
                $(document).on('click', '.repeater_add_more', function () {
                    var serviceId = $(this).attr("data-service_id");
                    var index_of_remotearea = $(".show_remotearea_remove_btn").map(function () {
                        return $(this).data('index_of_remotearea_remove');
                    }).get();//get all data values in an array
                    var highest_index_of_remotearea = Math.max.apply(Math, index_of_remotearea);//find the highest value from them
                    highest_index_of_remotearea = parseInt(highest_index_of_remotearea) + 1;
                    $("#services_remotearea_apend  .parent_clone_div_remotearea .clone_div_remotearea").children().clone().appendTo("#services_remotearea_apend .parent_clone_div_remotearea .append_here_remotearea");
                    $('#services_remotearea_apend .append_here_remotearea .row').last().attr('data-index_of_remotearea', highest_index_of_remotearea);
                    $('#services_remotearea_apend .append_here_remotearea .row .show_remotearea_remove_btn').last().attr('data-index_of_remotearea_remove', highest_index_of_remotearea);
                    $('#services_remotearea_apend .append_here_remotearea .row .show_remotearea_remove_btn').show();
                    setInputFeildsRemotearea(highest_index_of_remotearea);
                });
                //    Remotearea remove button script code
                $(document).on('click', '.show_remotearea_remove_btn', function () {
                    var current_index = $(this).attr('data-index_of_remotearea_remove');
                    var myThis = $(this);
                    swal({
                            title: "Are you sure you want to remove this?",
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
                                myThis.parent(".row").remove();
                            }
                        });
                });
                //Handle Filter for services
                $(".filter_services").change(function () {
                    filterServices();
                });
                $(document).on('ifChanged', '#check_all_country', function () {
                    if ($(this).is(":checked")) {
                        $('input:checkbox').iCheck('check');
                    } else {
                        $('input:checkbox').iCheck('uncheck');
                    }
                });
            });//End Document ready
            function loadAjax(toggle = "load") {
                if (toggle == "load") {
                    $('#overlay_div').show();
                    $('#loading_img_div').show();
                } else {
                    $('#overlay_div').hide();
                    $('#loading_img_div').hide();
                }
            }

            function loadServiceRow(service_id, load_data = "") {
                var acccoutId = "<?php echo $this->account_id; ?>";
                $.ajax({
                    url: 'add_services.php?id=<?php echo $this->account_id_encode; ?>',
                    type: 'POST',
                    dataType: "html",
                    data: {
                        action: 'get_services_data',
                        service_id: service_id,
                        account_id: acccoutId,
                        load_data: load_data
                    },
                    headers: {},
                    success: function (data) {
                        $("#services_tbl tbody").append(data);
                        disableServices();
                        $('.user_service_remotearea').iCheck({
                                checkboxClass: 'icheckbox_flat-blue',
                                radioClass: 'iradio_minimal-blue'
                            });;
                        $('.user_service_over_size').iCheck({
                                checkboxClass: 'icheckbox_flat-blue',
                                radioClass: 'iradio_minimal-blue'
                            });
                        $('.user_service_over_label').iCheck({
                                checkboxClass: 'icheckbox_flat-blue',
                                radioClass: 'iradio_minimal-blue'
                            });
                        $('.user_service_dead_weight').iCheck({
                                checkboxClass: 'icheckbox_flat-blue',
                                radioClass: 'iradio_minimal-blue'
                            });
                        if ($("#services_tbl tbody tr.service_row").length > 0) {
                            $("#services_tbl tbody tr#no_record_found_service").hide();
                        } else {
                            $("#services_tbl tbody tr#no_record_found_service").show();
                        }
                        $('input#search_service').quicksearch('table tbody tr', {
                            selector: 'td.service_name_td',
                            noResults: 'tr#no_record_found_service',
                            bind: 'keyup keydown',
                            prepareQuery: function (val) {
                                return new RegExp(val, "i");
                            },
                            testQuery: function (query, txt, _row) {
                                return query.test(txt);
                            }
                        });
                    },
                    error: function (xhr, status, error) {

                    }
                });
            }

            //Handle service load model functionality
            function getmodifiedWeight(serviceCode, serviceName, serviceLogo) {
                if ($('#apend_service_mode_data div[data-service-id="' + serviceCode + '"]').length > 0) {
                    var serviceInput = $('#apend_service_mode_data div[data-service-id="' + serviceCode + '"] input[type="hidden"]').serialize();
                }
                var modalHead = "";
                if (serviceLogo != "") {
                    modalHead = '<img class="margin-right-5" src="../images/carrierlogo/thumbnail/owe_50_' + serviceLogo + '" alt="' + serviceName + '" popover-trigger="mouseenter" height="25">' + serviceName + ' service';
                } else {
                    modalHead = '<img class="margin-right-5" src="../images/carrierlogo/no-image-found.jpg" alt="' + serviceName + '" popover-trigger="mouseenter" height="25">' + serviceName + ' service';
                }
                $("#myModalAdvance .modal-title").html("");
                $("#myModalAdvance .modal-title").html(modalHead);
                $.ajax({
                    url: 'add_services.php',
                    type: 'POST',
                    dataType: "html",
                    data: {action: 'special_routing_weight', serviceCode: serviceCode, serviceInput: serviceInput},
                    headers: {},
                    success: function (data) {
                        $("#advanceservice").html("");
                        $("#advanceservice").html(data);
                        $(".agent_select").select2();
                        $("#model_service_name").html(serviceName);
                        $(".save_service_agent_data").attr('data-service-id', serviceCode);
                    },
                    error: function (xhr, status, error) {
                    }
                });
            }

            // Set input fields empty
            function setInputFeilds(id) {
                $('.append_here [data-index-agent="' + id + '"] .agent_select').next().remove();
                $('.append_here [data-index-agent="' + id + '"] :text').val("");
                $('.append_here [data-index-agent="1"] :radio').attr("value", id);
                var select2Parentid = $('.append_here [data-index-agent="' + id + '"] .agent_select').select2();
                select2Parentid.val("").trigger('change');
            }

            //Remotearea set model
            function getRemoteareaService(serviceCode, carrierId, remotearesType, userAccountId,rastatus='',ramessage='') {
              //  var remoteareasFullType = "service";
              //  if (remotearesType == "c")
                //    remoteareasFullType = "carrier";
                var remoteareasFullType = remotearesType;
                if ($("#save_remotearea_charges_" + serviceCode).attr("data-remotearea-saved") != "ok") {
                    $.ajax({
                        url: 'add_services.php',
                        type: 'POST',
                        dataType: "html",
                        data: {
                            action: 'get_remotearea_charges',
                            serviceCode: serviceCode,
                            carrier_id: carrierId,
                            remoteareas_full_type: remoteareasFullType,
                            user_account_id: userAccountId,
                            rastatus:rastatus ,
                            ramessage:ramessage
                        },
                        headers: {},
                        success: function (data) {
                            $("#services_remotearea_apend"  ).html("");
                            $("#services_remotearea_apend"  ).html(data);
                            $(".remotearea_group").select2();
                        },
                        error: function (xhr, status, error) {
                        }
                    });
                } else {
                    $("#res_message_" + serviceCode).hide();
                }
            }

            //Check if remoteareas assigned duplicate group values
            function checkit() {
                var checker = [];
                var is_ok = true;
                $(".remotearea_group").each(function () {
                    var selection = $(this).val();
                    if (checker[selection]) {
                        //if the property is defined, then we've already encountered this value
                        swal("", "Can't duplicate groups", "error");
                        is_ok = false;
                        return;
                    } else {
                        checker[selection] = true;
                    }
                });
                return is_ok;
            }

            // Set input fields empty Remotearea
            function setInputFeildsRemotearea(id) {
                $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] .remotearea_group').next().remove();
                $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] :text').val("");
                var select2Parentid = $('.append_here_remotearea [data-index_of_remotearea="' + id + '"] .remotearea_group').select2();
                select2Parentid.val("").trigger('change');
            }

            function filterServices() {
                var account_id = 0;
                account_id = "<?php echo $this->userAcccountParentId; ?>";
                var selected_hidden_services = $("#selected_hidden_services").val();
                var selected_hidden_carrier_id = $("#search_Carrier_id").val();
                var source_service_country = $("#source_service_country").val();
                var destination_service_country = $("#destination_service_country").val();
                var des_service_type = $("#des_service_type").val();
                $.ajax({
                    type: "POST",
                    url: "add_services.php",
                    data: {
                        action: "filter_services",
                        services: selected_hidden_services,
                        s_country: source_service_country,
                        d_country: destination_service_country,
                        des_service_type: des_service_type,
                        carrier_id: selected_hidden_carrier_id,
                        account_id: account_id
                    },
                    dataType: "html",
                    success: function (data) {
                        var returnData = data.split(":::");
                        $('#agent_services_temp option').remove();
                        $('#agent_services_temp').append(returnData[0]);
                        $('#agent_services_temp').multiSelect("refresh");
                        var selectedServices = $('select#agent_services_temp').val();
                        var dataServiceStr = "";
                        $.each(selectedServices, function (index, value) {
                            dataServiceStr += '[data-service-id="' + value + '"],';
                        });
                        dataServiceStr = dataServiceStr.replace(/,\s*$/, "");
                        if ($('select#agent_services_temp').val() != null && $('select#agent_services_temp').val().length > 0) {
                            $('.services_tbl tr').filter(dataServiceStr).show();
                        }
                        disableServices();
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }

            //Disable already selected service in multiselect
            function disableServices(callFrom = "") {
//                if(callFrom != "")
//                    alert(callFrom);
                $("#agent_services_temp").children('option').removeAttr('disabled');
                var appendServiceId = 0;
                $.each(document.querySelectorAll('[data-service-ajax-id]'), function (index, value) {
                    appendServiceId = $.trim($(this).data("service-ajax-id"));
                    $("#agent_services_temp").children('option[value="' + appendServiceId + '"]').attr('disabled', true);
                });
                $('#agent_services_temp').multiSelect("refresh");
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        // transfer form variables into local values (form variables come from parent)
        $sessionUser = $this->user;
        $id = $this->account_id;
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    Add Services [ <?php echo $this->userAccountName; ?> ]
                </div>
                <div class="actions">
                    <a href="customers.php" class="btn blue"><span></span><i class="fa fa-users"></i>&nbsp;List Accounts</a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                    <div class="col-md-12">
                        <div class="alert alert-danger display-none" id="response_message_alert"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Carrier</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                <?php echo Ddl::generateCarrierDDLWithImage('search_Carrier_id', $search_Carrier_id, 'id', ' class="filter_services bs-select form-control" data-live-search="true"  data-show-subtext="true"'); ?>
                            </div>
                        </div>
                    </div>
                    <div id="service_country">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Destination Service Country</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                    <?php
                                    if (empty($destination_service_country))
                                        $destination_service_country = "";
                                    echo Ddl::generateCountryDDL('destination_service_country', $destination_service_country, 'id', ' class="filter_services bs-select form-control" data-live-search="true"  data-show-subtext="true"', 'destination_service_country');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Source Service Country</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                    <?php
                                    if (empty($source_service_country))
                                        $source_service_country = "";
                                    echo Ddl::generateCountryDDL('source_service_country', $source_service_country, 'id', 'class="filter_services bs-select form-control" data-live-search="true" data-show-subtext="true"', 'source_service_country');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Country Allowed</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-list"></i></div>
                                <?php
                                $distCountryArr = array('all' => 'ALL', 'DBP' => 'Domestic', 'R1' => 'Europe', 'INT' => 'International');
                                echo Ddl::generateArrayDDL('des_service_type', $distCountryArr, '', '', ' class="filter_services form-control select2 select" rel="tooltip" data-original-title="Destination Country" placeholder="Country Allowed"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $partnerServiceRoutingFilter = new UserServicesRoutingFilter();
                    $partnerServiceRoutingRecordSets = $partnerServiceRoutingFilter->getStandardCustomerServiceList(" sr.id 'service_id', con.name 'carrier_country', cr.logo, cr.carrier, sr.code 'code', sr.name 'service_name', psr.is_remotearea, psr.from_weight, psr.to_weight, sr.from_weight 'service_from_weight', sr.to_weight 'service_to_weight',cr.remotearea_check,cr.carrier_id,sr.is_customized AS added_by", $this->account_id, '', '');
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Service</label>
                            <div class="row">
                                <div class="col-md-12 accessibility-container">
                                    <select name="agent_services_temp[]" id="agent_services_temp" multiple="multiple"
                                            class="multi-select multiselect_drop_down" title="Group" placeholder="Group"
                                            data-original-title="Group">
                                        <?php
                                        $selectedServicesArr = array();
                                        foreach ($partnerServiceRoutingRecordSets as $partnerServiceRoutingRecordSet) {
                                            $serviceSelected = "";
                                            ?>
                                            <option <?php echo $serviceSelected; ?>
                                                    value="<?php echo $partnerServiceRoutingRecordSet->getServiceId(); ?>"><?php echo $partnerServiceRoutingRecordSet->getServiceName()." [ ".$partnerServiceRoutingRecordSet->getCode()." ]"; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-list"></i>
                        Account [ <?php echo $this->userAccountName; ?> ] Services
                    </div>
                    <div class="inputs">
                        <div class="portlet-input input-inline input-small ">
                            <div class="input-icon right">
                                <i class="icon-magnifier"></i>
                                <input type="text" id="search_service"
                                       class="form-control form-control-solid input-circle" placeholder="search...">
                            </div>
                        </div>
                    </div>
                    <div class="actions">

                    </div>
                </div>
                <div class="portlet-body">
                    <div id="services_div">
                        <div id="services_table">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-advance table-hover services_tbl"
                                               id="services_tbl">
                                            <thead>
                                            <tr>
                                                <th>Carrier</th>
                                                <th>Services</th>
                                                <th>Origin Country</th>
                                                <th class="text-center">Remote Area</th>
                                                <th class="text-center">Over Size</th>
                                                <th class="text-center">Over Label</th>
                                                <th class="text-center">Charge Dead Weight</th>
                                                <th class="text-center">Label Charges</th>
                                                <th>Default Weight</th>
                                                <th>Advance</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr id="no_record_found_service">
                                                <td colspan="7">No Record Found.</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Table should be here -->
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>"/>
            <input type="hidden" name="form_action" id="form_action" value="save_services"/>
        </form>
        <div style="display: none;" id="apend_service_mode_data">
            <?php
            $countService = 0;
            $serviceIdCur = 0;
            foreach ($this->serviceCustomizeRulesList as $serviceCustomizeRulesLists) {
            if ($serviceIdCur != $serviceCustomizeRulesLists->getServiceId() && $serviceIdCur != 0)
                echo "</div>";

            if ($serviceIdCur == 0 || $serviceIdCur != $serviceCustomizeRulesLists->getServiceId()) {
            ?>
            <div data-service-id="<?php echo $serviceCustomizeRulesLists->getServiceId(); ?>">
                <?php } ?>
                <input type="hidden" name="agent[<?php echo $serviceCustomizeRulesLists->getServiceId(); ?>][]"
                       value="<?php echo $serviceCustomizeRulesLists->getAgentId(); ?>"/>
                <input type="hidden" name="from_weight[<?php echo $serviceCustomizeRulesLists->getServiceId(); ?>][]"
                       value="<?php echo $serviceCustomizeRulesLists->getFromWeight(); ?>"/>
                <input type="hidden" name="to_weight[<?php echo $serviceCustomizeRulesLists->getServiceId(); ?>][]"
                       value="<?php echo $serviceCustomizeRulesLists->getToWeight(); ?>"/>

                <?php
                if ($serviceIdCur == 0 || $serviceIdCur != $serviceCustomizeRulesLists->getServiceId())
                    $serviceIdCur = $serviceCustomizeRulesLists->getServiceId();

                $countService++;
                }
                ?>
            </div>
        </div>
        <div id="overlay_div" class="blockUI blockOverlay"
             style="display: none; z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; opacity: 0.05; cursor: wait; position: absolute;"></div>
        <div id="loading_img_div" class="blockUI blockMsg blockElement"
             style="display: none; z-index: 1011; position: absolute; padding: 0px; margin: 0px; width: 30%; top: 3%; left: 407.5px; text-align: center; color: rgb(0, 0, 0); border: 0px; cursor: wait;">
            <div class="loading-message loading-message-boxed">
                <img src="../assets/global/img/loading-spinner-grey.gif" align=""><span>&nbsp;&nbsp;Loading...</span>
            </div>
        </div>
        <!--Model for services weight-->
        <div class="modal fade bs-modal-lg" id="myModalAdvance" tabindex="-1" role="dialog" aria-hidden="true"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-none" id="res_message"></div>
                            </div>
                        </div>
                        <div id="advanceservice">Please wait data loading...</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" class="btn green save_service_agent_data">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="hidden_frm" style="display: none;">
        <form name="hiddenForm" id="download_services_remotearea_template_form" action="" method="POST">
                <input type="hidden" name="download_csv_serviceId" id="download_csv_serviceId" value="" />
                <input type="hidden" name="download_csv_carrierId" id="download_csv_carrierId" value="" />
                <input type="hidden" name="func" value="download_services_remotearea_template" />
            </form>     
        </div>
                 <!--Model for services remotearea-->
     <div class="modal fade bs-modal-lg" id="model_remoterea_services" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
         <div class="modal-dialog modal-lg">
             <div class="modal-content" id="services_remotearea_apend">
                 
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