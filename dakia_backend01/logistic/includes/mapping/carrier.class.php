<?php
/**
 * Country Object
 *
 */
class Carrier extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
                    'id'                    => 'number',
                    'carrier'               => 'string',
                    'logo'                  => 'string',	
                    'cut_off_time'          => 'string',
                    'carrier_display_name'  => 'string',
                    'status'                => 'number',
                    'carrier_id'            => 'number',
                    'country_id'            => 'number',
                    'currency_code'         => 'string',
                    'remotearea_check'      => 'string',
                    'zone_base'             => 'bit',
                    'zone_type' => ['enum' => ['country','postcode'],'default' => 'country'],
                    'on_contract'           => 'bit',
                    'is_gazetteer'           => 'number',
                    'is_reconcile'           => 'number',
                    
                    'country_name'          => 'undefined',
                    'country_iso'           => 'undefined',
                    'service_id'            => 'undefined',
                    'service_name'          => 'undefined',
                    'from_weight'           => 'undefined',
                    'to_weight'             => 'undefined',
                    'user_service_status'   => 'undefined',
                    'is_agreed'             => 'undefined'
                    
                    
                    );

		//
		parent::__construct("carrier", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}
        
        
        protected static function getSelectBoxCategories($parent, $carrier, $selectedCarrier)
        {
            $html = "";
            if (isset($carrier['parent_carrier'][$parent]))
            {
                foreach ($carrier['parent_carrier'][$parent] as $cat_id)
                {
                    if (!isset($carrier['parent_carrier'][$cat_id]))
                    {
                        if($selectedCarrier == $cat_id )
                            $selected=' selected="selected"';
                        else
                            $selected='';
                      $html .= "<option value='".$cat_id."' ".$selected.">".$carrier['carrier'][$cat_id]->getCarrier()."</option>";
                    }
                    if (isset($carrier['parent_carrier'][$cat_id]))
                    {
                      $html .= "<optgroup label='".$carrier['carrier'][$cat_id]->getCarrier()."'>";
                      $html .= self::getSelectBoxCategories($cat_id, $carrier, $selectedCarrier);
                      $html .= "</optgroup>";
                    }
                }
            }
            return $html;
        }
        
        public static function dropdownCarrierBox($dropdwonName='carrier', $dropdwonId='carrier',$selectedCarrier = 0,$disabled="")
        {
            
            $carrierCorp = new CarrierFilter();
            $carrierCorp->addFilter(" status = '1'");
            $carrierCorp->AddOrderBy("carrier", true);
            $carrierCorpList = $carrierCorp->getList();
            if(count($carrierCorpList)>0)
            {
               foreach( $carrierCorpList as $result)
                {
                    $carrier['carrier'][$result->getId()] = $result;
                    $carrier['parent_carrier'][$result->getCarrierId()][] = $result->getId();
                }
            }
            
            $html   =   '';
            $html .= '<select name = "'.$dropdwonName.'" id= "'.$dropdwonId.'" '.$disabled.' class="form-control select2" rel="tooltip" title = "'.$dropdwonName.'" placeholder = "'.$dropdwonName.'"  >';
            $html .= "<option value=''>Select Carrier</option>";
            $html .=    self::getSelectBoxCategories(0, $carrier, $selectedCarrier);
            $html .= '</select>';
            return $html;
        }
        
             
	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getCarrierListFromSql($sql){
		
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
        public static function getRemoteareaContent($carrierId,$modelType="",$serviceId = 0,$user = false){
            $html = "";
            $remoteareaChargesCarrierList = [];
            $inc = 0;
            if($user == false){
                $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                $remoteareasGroupsFilter->addFilter("    carrier_id = ".$carrierId);
                $remoteareasGroupsIds = $remoteareasGroupsFilter->getColumnList("id");
                if(count($remoteareasGroupsIds) > 0){
                    foreach ($remoteareasGroupsIds as $remoteareasGroupsId) {
                        $groupsId = $remoteareasGroupsId->getId();
                        $remoteareaChargesCarrierFilter = new RemoteareaChargesCarrierFilter();
                        $remoteareaChargesCarrierFilter->addFilter("    remotearea_group_id = ".$groupsId);
                        $remoteareaChargesCarrierListCur = $remoteareaChargesCarrierFilter->getList();
                        if(count($remoteareaChargesCarrierListCur) > 0)
                            $remoteareaChargesCarrierList[] = $remoteareaChargesCarrierListCur;
                    }
                }
            }
            if($modelType == "service"){
                $remoteareaChargesServicesFilter = new RemoteareaChargesServicesFilter();
                $remoteareaChargesServicesFilter->addFilter("    service_id = ".$serviceId);
                $remoteareasGroupsIds = $remoteareaChargesServicesFilter->getList();
                if(count($remoteareasGroupsIds) > 0){
                    $remoteareaChargesCarrierList[0] = $remoteareasGroupsIds;
                }
            }
            if(count($remoteareaChargesCarrierList) > 0 && !empty($remoteareaChargesCarrierList)){
                $html = "";
                $html .= '<div class="row">
                            <div class="col-sm-'.($modelType=="carrier" ? "6":"3").'">
                                <label>Group</label>
                            </div>';
                if($modelType == "service"){
                    $html .='<div class="col-sm-2">
                                <label>From Weight</label>
                            </div>
                            <div class="col-sm-2">
                                <label>To Weight</label>
                            </div>';
                            }
                    $html .='<div class="col-sm-'.($modelType=="carrier" ? "5":"2").'">
                                <label>Charges</label>
                            </div>';
                if($modelType == "service"){
                    $html .=' <div class="col-sm-2">
                                <label>Formula</label>
                            </div>';
                }
                $html .='</div>';
                foreach ($remoteareaChargesCarrierList as $remoteareaChargesCarrierArr) {
                    if(count($remoteareaChargesCarrierArr)){
                        foreach ($remoteareaChargesCarrierArr as $remoteareaChargesCarrierVal) {
                            if ($inc == 0) {
                                $html .=    '<div class="clone_div">';
                            }
                            $html .=        '<div class="row" data-index-agent='.$inc.'>
                                                <div class="col-sm-'.($modelType=="carrier" ? "6":"3").'">
                                                    <div class="form-group">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-addon"> <i class="fa fa-user"></i></div>';
                                                            $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter','AND carrier_id = '.$carrierId.' ', 'group_name', 'id', ''.$remoteareaChargesCarrierVal->getRemoteareaGroupId().'', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");

                                                           $html .= '<span class="input-group-addon red-18">*</span> 
                                                        </div>
                                                    </div>
                                                </div>';
                                            if($modelType == "service"){
                                                    $html .= '<div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                                        <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight">*</i>
                                                                            <input type="text" name="from_weight[]" value="'.$remoteareaChargesCarrierVal->getFromWeight().'" placeholder="From Weight" class="validate_check  form-control remotearea_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                                        <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight">*</i>
                                                                            <input  type="text" name="to_weight[]" value="'.$remoteareaChargesCarrierVal->getToWeight().'" placeholder="To Weight" class="validate_check  form-control remotearea_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                            }
                                    $html .=    '<div class="col-sm-'.($modelType=="carrier" ? "5":"2").'">
                                                    <div class="form-group">
                                                        <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                            <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges">*</i>
                                                                <input id="charges" type="text" name="charges[]" value="'.$remoteareaChargesCarrierVal->getRemoteareaCharges().'" placeholder="Charges" class="validate_check  form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Chanrges" />

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                if($modelType == "service"){
                                    $html .=    '<div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                            <div class="input-icon right">
                                                                <input type="text" name="formula[]" value="'.$remoteareaChargesCarrierVal->getFormulla().'" placeholder="Formula" class="form-control remotearea_formula"  data-toggle="tooltip" data-placement="top" title="Formula" />

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                                }
                                        $display = '';
                                        if ($inc == 0) {
                                            $display = 'style="display: none;"';        
                                        }
                                        $html .='<div class="col-md-1 show_remove_btn" data-index-of-remove="'.$inc.'" '.$display.'>
                                                        <div class="input-group input-group-sm">
                                                            <a href="javascript:;"  class="btn btn-danger repeater-delete">
                                                                <i class="fa fa-close"></i>
                                                            </a>
                                                        </div>
                                                </div>
                                            </div>';
                            if ($inc == 0) {
                                $html .=    '</div><div class="append_here">';
                            }
                            $inc++;
                        }
                    }
                }
            }else{
                $html = "";
                $html .= '<div class="row">
                            <div class="col-sm-'.($modelType=="carrier" ? "6":"3").'">
                                <label>Group</label>
                            </div>';
                if($modelType == "service"){
                    $html .='<div class="col-sm-2">
                                <label>From Weight</label>
                            </div>
                            <div class="col-sm-2">
                                <label>To Weight</label>
                            </div>';
                            }
                    $html .='<div class="col-sm-'.($modelType=="carrier" ? "5":"2").'">
                                <label>Charges</label>
                            </div>';
                if($modelType == "service"){
                    $html .=' <div class="col-sm-2">
                                <label>Formula</label>
                            </div>';
                }
                $html .='</div>';
                $html .=    '<div class="clone_div">';
                $html .=        '<div class="row">
                                    <div class="col-sm-'.($modelType=="carrier" ? "6":"3").'">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>';
                                                $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter','AND carrier_id = '.$carrierId.' ', 'group_name', 'id', '', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");

                                               $html .= '<span class="input-group-addon red-18">*</span> 
                                            </div>
                                        </div>
                                    </div>';
                        if($modelType == "service"){
                            $html .= '<div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight">*</i>
                                                    <input type="text" name="from_weight[]" value="" placeholder="From Weight" class="validate_check  form-control remotearea_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight">*</i>
                                                    <input  type="text" name="to_weight[]" value="" placeholder="To Weight" class="validate_check  form-control remotearea_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                        }
                    $html .='<div class="col-sm-'.($modelType=="carrier" ? "5":"2").'">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                <div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges">*</i>
                                                    <input  type="text" name="charges[]" value="" placeholder="Charges" class="validate_check  form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Charges" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                    if($modelType == "service"){
                        $html .='<div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                <div class="input-icon right">
                                                    <input type="text" name="formula[]" value="" placeholder="Formula" class="form-control remotearea_formula"  data-toggle="tooltip" data-placement="top" title="Formula" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                        }
                            $html .='<div class="col-md-1 show_remove_btn" data-index-of-remove="0" style="display: none;">
                                            <div class="input-group input-group-sm">
                                                <a href="javascript:;"  class="btn btn-danger repeater-delete">
                                                    <i class="fa fa-close"></i>
                                                </a>
                                            </div>
                                    </div>
                                </div>';
                $html .=    '</div>';
                $html .=    '<div class="append_here"></div>';
            }
        return $html;
        }
        
        public static function getCarriersServersFromUserAccount($userAccountId, $selected="", $getServices = false, $getAgents = false) {
            $consignmentList = array();
            if (!empty($userAccountId)) {
                $return = array();
                $userAccountArry = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                $ids = implode(",", $userAccountArry);
                $userFilter = new UserFilter();
                $userIds = $userFilter->getUserIdsFromAccountIds($ids);
                $consignmentFilterObj = new ConsignmentFilter();
                $consignmentFilterObj->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                $consignmentList = $consignmentFilterObj->getColumnList("DISTINCT(CASE WHEN c.customized_service_id > 0 THEN c.customized_service_id ELSE c.service_id END) AS service_id");
                $serviceIds = array();
                foreach ($consignmentList as $con) {
                    $serviceIds[] = $con->getServiceId();
                }
            }
            $serviceFilterObj = new ServiceFilter();
            if (!empty($serviceIds)) {
                $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
            }
            $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            $carrierIds = array();
            foreach ($services as $ser) {
                if (!in_array($ser->getCarrierId(), $carrierIds)) {
                    $carrierIds[] = $ser->getCarrierId();
                }
            }
            $carrierFilterObj = new CarrierFilter();
            $carrierFilterObj->addFilterIn("c1.id", $carrierIds);
            $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
            $service_option = "<option value=''>Select Service</option>";
            foreach ($services as $sr) {
                $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                 $selection = ($cr->getId() == $selected ? ' selected="selected"' : '');
                $carrier_option .= "<option value='" . $cr->getId() . "' $selection >" . $cr->getCarrierDisplayName() . "</option>";
            }
            $consignmentFilterForAgentIds = new ConsignmentFilter();
            $consignmentFilterForAgentIds->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
            $consignmentAgentList = $consignmentFilterForAgentIds->getColumnList("DISTINCT(c.agent_id)");
            $agentIds = array();
            foreach ($consignmentAgentList as $obj) {
                $agentIds[] = $obj->getAgentId();
            }
            $agentFilter = new AgentDataFilter();
            $agentFilter->addAgentIdInFilter($agentIds);
            $agentFilterObj = $agentFilter->getColumnList('   a.agent_name,a.agent_code');
            $agent_option = "<option value=''>Select Agent</option>";
            foreach ($agentFilterObj as $obj) {
                $agent_option .= "<option value='" . $obj->getId() . "'>" . $obj->getAgentName() . "</option>";
            }
            if($getServices) {
                $return['services_option'] = $service_option;
                $return['serviceIds'] = $serviceIds;
            }
            if($getAgents) {
                $return['agent_option'] = $agent_option;
            }
            $return['carrier_option'] = $carrier_option;
            $return['carrierIds'] = $carrierIds;
            return $return;
        }
}
