<?php 
require_once("../includes/settings/config.inc.php");

include_classes([
    'PHPExcel'
        ], '3rdparty/phpexcel');

require_once("../includes/library/vendor/autoload.php");

include_classes([
    'carrier.class',
    'carrierfilter.class',
    'currency.class',
    'currencyfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'services.class',
    'servicefilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'remoteareachargestariffs.class',
    'remoteareachargestariffsfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
]);

if (isset($_POST['action']) && $_POST['action'] == "get_remotearea_traiff_charges") {
    return get_remotearea_traiff_charges($_POST);
} else if (isset($_POST['action']) && $_POST['action'] == 'save_remotearea_charges') {
    return save_remotearea_charges($_POST);
} else if (isset($_POST['func']) && $_POST['func'] == 'upload_csv_file_remotearea') {
    return upload_csv_file_remotearea($_POST);
} else if (isset($_POST['func']) && $_POST['func'] == 'download_tariff_remotearea_template') {
    return download_tariff_remotearea_template($_POST);
}

function get_remotearea_traiff_charges($formData) {
  $tariffId = trim($formData['tariff_id']);
            $carrierId = trim($formData['carrier_id']);
            $remoteAreaType = trim($formData['remotearea_type']);
            $tariff = new Tariffs($tariffId);
            $carrier = new Carrier($carrierId);
            
            $tmessage = trim($formData['tmessage']);
            $tstatus = trim($formData['tstatus']);
            $remoteareaChargesTariffFilter = new RemoteAreaChargesTariffsFilter();
            $remoteareaChargesTariffFilter->addFilter(" tariff_id = ".$tariffId);
            $remoteareaChargesTariffFilter->orderBy('remotearea_group_id, from_weight', 'ASC');
            $remoteareaChargesTariffFilterResults = $remoteareaChargesTariffFilter->getList();
            $html = '<div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                     <h4 class="modal-title"> <b>'.$tariff->getName().'</b> RemoteAreas Charges</h4>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-md-12">
                             <div class="alert alert-danger display-none"  id="res_message_remoterea_supplier" ></div>
                         </div>
                     </div>';
            $html .= '<div class="parent_clone_div_remotearea">';
            if (!empty($remoteareaChargesTariffFilterResults)) {
                $inc = 0;                        
                foreach ($remoteareaChargesTariffFilterResults as  $rcsResult) {
                    if ($inc == 0) {
                        $html .= '<div class="clone_div_remotearea">';
                    }
                $html .= '<div class="row">';
                $html .= '<div class="col-sm-' . ($remoteAreaType == "ON_PIECE" ? "6" : "3") . '">';
                $html .= '<div class="form-group">';
                $html .= '<label>Group</label>';
                $html .= '<div class="input-group">';
                $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>';
                $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter','AND carrier_id = '.DbAccess3::escape($carrierId).' ', 'group_name', 'id', ''.$rcsResult->getRemoteAreaGroupId().'', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");
                $html .= '<span class="input-group-addon red-18">*</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                if ($remoteAreaType == "ON_WEIGHT") {
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>From Weight</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
                    $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="From Weight is mandatory">*</i>';
                    $html .= '<input type="text" name="from_weight[]" value="'.$rcsResult->getFromWeight().'" placeholder="From Weight" class="validate_check  form-control remotearea_from_weight"  data-toggle="tooltip" data-placement="top" title="From Weight" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>To Weight</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="To Weight is mandatory">*</i>';
                    $html .= '<input  type="text" name="to_weight[]" value="'.$rcsResult->getToWeight().'" placeholder="To Weight" class="validate_check  form-control remotearea_to_weight"  data-toggle="tooltip" data-placement="top" title="To Weight" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '<div class="col-sm-' . ($remoteAreaType == "ON_PIECE" ? "5" : "2") . '">';
                $html .= '<div class="form-group">';
                $html .= '<label>Charges</label>';
                $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges is mandatory">*</i>';
                $html .= '<input type="text" name="charges[]" value="'.$rcsResult->getRemoteareaCharges().'" placeholder="Charges" class="validate_check  form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Chanrges" />';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                if ($remoteAreaType == "ON_WEIGHT") {
                    $html .= '<div class="col-sm-2">';
                    $html .= '<div class="form-group">';
                    $html .= '<label>Formula</label>';
                    $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span><div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Formula is mandatory">*</i>';
                    $html .= '<input type="text" name="formula[]" value="'.$rcsResult->getFormulla().'" placeholder="Formula" class="form-control remotearea_formula"  data-toggle="tooltip" data-placement="top" title="Formula" />';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '<div class="col-md-1 show_remotearea_remove_btn" data-index_of_remotearea_remove="'.$inc.'" '.($inc == 0 ? 'style="display: none;"': '').' >';
                $html .= '<label class="control-label">&nbsp;</label>';
                $html .= '<a href="javascript:;"  class="btn btn-danger repeater-delete"><i class="fa fa-close"></i></a>';
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
            $html .= '<div class="col-sm-' . ($remoteAreaType == "ON_PIECE" ? "6" : "3") . '">';
            $html .= '<div class="form-group">';
            $html .= '<label>Group</label>';
            $html .= '<div class="input-group">';
            $html .= '<div class="input-group-addon"> <i class="fa fa-user"></i> </div>'; 
            $html .= Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter','AND carrier_id = '.DbAccess3::escape($carrierId).' ', 'group_name', 'id', '', ' class="validate_check  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");
            $html .= '<span class="input-group-addon red-18">*</span>';
            $html .= '</div>';// End input-group
            $html .= '</div>'; // End form-group
            $html .= '</div>';//End col-sm-2
             if ($remoteAreaType == "ON_WEIGHT") {
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
            $html .= '<div class="col-sm-' . ($remoteAreaType == "ON_PIECE" ? "5" : "2") . '">';
            $html .= '<div class="form-group">';
            $html .= '<label>Charges</label>';
            $html .= '<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>';
            $html .= '<div class="input-icon right"> <i class="fa tooltips font-red" data-original-title="Charges is mandatory">*</i>';
            $html .= '<input type="text" name="charges[]" value="" placeholder="Charges" class="validate_check form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Charges" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
             if ($remoteAreaType == "ON_WEIGHT") {
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
            $html .= '<a href="javascript:;"  class="btn btn-danger repeater-delete"><i class="fa fa-close"></i></a>';
            $html .= '</div>';
            $html .= '</div>';//End row
            $html .= '</div>';//End clone_div_remotearea
            $html .= '<div class="append_here_remotearea"></div>';           
        }
        
            $html .= '<a href="javascript:;" data-tariff_id="'.$tariffId.'" class="btn btn-info repeater_add_more"><i class="fa fa-plus"></i> Add more</a><br><br>';
            $html .= '</div>';//End parent_clone_div_remotearea
            $html .= '<div class="row">'
                    . '<div class="col-md-3 col-md-offset-9 col-sm-4 col-sm-offset-6">'
                    . '<div class="form-group">'
                    . '<button   id="save_remotearea_charges_for_supplier" data-tariff_id="'.$tariffId.'" class="btn btn-primary green save_remotearea_tariff_data">Save changes</button>'
                    . '&nbsp;<button  class="btn btn-default" data-dismiss="modal">Close</button>' 
                    . '</div>'
                    . '</div>'
                    . '</div>';
            $html .= '<div class="row"><div class="col-md-12"><hr/></div></div>';
            $html .= '<div class="row">
                            <input type="hidden" name="upload_tariff_id" id="upload_tariff_id" value="'.$tariffId.'" />
                            <input type="hidden" name="upload_carrier_id" id="upload_carrier_id" value="'.(int)$carrierId.'" /> 
                            <input type="hidden" name="upload_remotearea_type" id="upload_remotearea_type" value="'.$remoteAreaType.'" />     
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
                                                <input type="file" name="file" id="tariff_upload_remote_file"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImportRemoteArea" data-original-title="" title="">'.Translation::GetCaption("IMPORT").'</a>
                                            <a href="javascript:;" class="input-group-addon btn danger" id="download_tariff_remotarea_template" data-original-title="" title=""><i class="fa fa-download"></i> '.Translation::GetCaption("TEMPLATE").'</a>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-12">';
                    if ($tmessage != '') {
                        $html .= '<div  id="upload_tariff_remotearea_console_window" style="  clear:both;background-color: #000;color: #FFF; padding: 15px;">' . strip_tags($tmessage) . '</div>';
                    }
                    $html .= '       </div>
                                </div>
  </div>
                  
                                    ';
            echo $html;
            die;
}

function save_remotearea_charges($formData) {
    $userAccount = SessionManager::getUser();
    $remoteareaType =  trim($formData['remotearea_type']);
    $groupListStr = "";
    if ($formData['mayTariffs']){
        $filterData = SessionManager::getAdditionalChargesFilter();
        $tariffName = $filterData['name'];
        $carrier_id = $filterData['carrier_id'];
        if (empty($tariffName)){
            $output["status"] = "error";
            $output["message"] = "Tariff name can not be empty.";
            echo json_encode($output);
            die;
        }
        if (empty($carrier_id)){
            $output["status"] = "error";
            $output["message"] = "Carrier name can not be empty.";
            echo json_encode($output);
            die;
        }
        $output = save_remote_charges($formData);
    }else {
        $tariffId = (int) trim($formData['tariff_id']);
        if($tariffId > 0){
            $tariffObj = new Tariffs($tariffId);
            $carrierId = $tariffObj->getCarrierId();
            if($carrierId > 0){
                $remoteareasGroupsFilter = new RemoteareasGroupsFilter;
                $remoteareasGroupsFilter->addFilter("AND carrier_id = ".$carrierId);
                $remoteareasGroupsList = $remoteareasGroupsFilter->getColumnList("id");
                foreach ($remoteareasGroupsList as $remoteareasGroupsSng) {
                    $groupListStr .= "'".$remoteareasGroupsSng->getId()."',";
                }
            }

            $groupListNew = rtrim($groupListStr,',');
            $remoteareasType = trim($formData['remoteareas_type']);

            parse_str($formData['group'],$group);
            parse_str($formData['from_weight'],$fromWeight);
            parse_str($formData['to_weight'],$toWeight);
            parse_str($formData['charges'],$charges);
            parse_str($formData['formula'],$formula);

            $remoteareaChargesTariffOldData = new RemoteAreaChargesTariffsFilter();
            $remoteareaChargesTariffOldData->addFilter(" tariff_id = ".$tariffId."   ");
            $oldRemoteareaChargesTariff = $remoteareaChargesTariffOldData->getList();

            $oldAuditData = [];
            $newAuditData = [];
            if(!empty($oldRemoteareaChargesTariff)) {
                foreach ($oldRemoteareaChargesTariff as $datum) {
                    $groupObj = new RemoteareasGroups($datum->getRemoteAreaGroupId());
                    // $oldAuditData[] = "Group Name:" . $groupObj->getGroupName() . ',Charges: ' . $datum->getRemoteareaCharges() . ',From Weight: ' . $datum->getFromWeight() . ',To Weight: ' . $datum->getToWeight();

//                     $oldAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                "Charges" =>  $datum->getRemoteareaCharges(),
//                                "From Weight" => $datum->getFromWeight(),
//                                "To Weight" => $datum->getToWeight(),
//                                "Formula" =>$datum->getFormulla() );

                    $oldAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " . $datum->getRemoteareaCharges() ."<br />From Weight: ". $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight(). '<br />Formula: ' . $datum->getFormulla();
                }
            }


            //Delete pervious records
            RemoteAreaChargesTariffs::deleteByTariffId($tariffId);

            foreach ($group['group'] as $key => $groupList) {
                if(!empty($groupList)){
                    $groupObj = new RemoteareasGroups($groupList);
                    $remoteAreaChargesTariff = new RemoteAreaChargesTariffs();
                    $remoteAreaChargesTariff->setRemoteareaGroupId($groupList);
                    $remoteAreaChargesTariff->setRemoteareaCharges($charges['charges'][$key]);
                    $remoteAreaChargesTariff->setTariffId($tariffId);
                    if($remoteareaType=='ON_WEIGHT'){
                        $remoteAreaChargesTariff->setFromWeight($fromWeight['from_weight'][$key]);
                        $remoteAreaChargesTariff->setToWeight($toWeight['to_weight'][$key]);
                        $remoteAreaChargesTariff->setFormulla($formula['formula'][$key]);
                    }
                    $remoteAreaChargesTariff->setAddedBy($userAccount->getId());
                    $remoteAreaChargesTariff->setAddedDate(time());
                    $remoteAreaChargesTariff->setIsDeleted('N');
                    $remoteAreaChargesTariff->save();

                    if($remoteareaType=='ON_WEIGHT'){
//                         $newAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                "Charges" => $charges['charges'][$key],
//                                "From Weight" => $fromWeight['from_weight'][$key],
//                                "To Weight" => $toWeight['to_weight'][$key],
//                                "Formula" => $formula['formula'][$key]) ;
                        $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges['charges'][$key] ."<br />From Weight: ". $fromWeight['from_weight'][$key]. '<br />To Weight: ' . $toWeight['to_weight'][$key]. '<br />Formula: ' . $formula['formula'][$key];
                    }else{
//                                 $newAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                "Charges" => $charges['charges'][$key]) ;
                        $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges['charges'][$key]  ;

                    }
                }
            }
            $newDataAudit = [];
            $oldDataAudit = [];
            if(!empty($newAuditData)) {
                $newDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $newAuditData);
            }
            if(!empty($oldAuditData)) {
                $oldDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $oldAuditData);
            }
            $old_data = json_encode($oldAuditData);
            $new_data = json_encode($newAuditData);
            $userAudit = new UserAudit();
            $table_key = $tariffId;
            $userAudit->allowAdd = true;
            $userAudit->dataName = $tariffObj->name;
            $userAudit->insertAuditData('tariffs', 'TariffRemoteArea', $userAccount->getFirstName() . ' ' . $userAccount->getLastName(), $userAccount->getId(), $new_data, $table_key, $old_data, $userAccount->getFirstName() . ' ' . $userAccount->getLastName() . ' update remoarea charges for tariff ' . $tariffObj->getName());


            $output["status"] = "success";
            $output["message"] = "Remotearea saved successfully";
        }else{
            $output["status"] = "error";
            $output["message"] = "Error.";
        }
    }
    echo json_encode($output);
    die;
}
function save_remote_charges($formData){
    $userAccount = SessionManager::getUser();
    $remoteareaType =  trim($formData['remotearea_type']);
    $groupListStr = "";
    $filterData = SessionManager::getAdditionalChargesFilter();
    $tariffsFilter = new TariffsFilter();
    $carrierId = $filterData['carrier_id'];
    if (!empty($carrierId))
        $tariffsFilter->addFieldFilter('   t.carrier_id', $carrierId);

    $serviceId = $filterData['service_id'];
    if (!empty($serviceId))
        $tariffsFilter->addFieldFilter('  t.service_id', $serviceId);

    $tariffName = $filterData['name'];
    if (!empty($tariffName))
        $tariffsFilter->addFieldLikeFilter('  t.name', $tariffName);

    $startDate = $filterData['start_date'];
    if (!empty($startDate))
        $tariffsFilter->addStartDateFilter($startDate);

    $endDate = $filterData['end_date'];
    if (!empty($endDate))
        $tariffsFilter->addEndDateFilter($endDate);

    $fromZone = $filterData['from_zone'];
    if (!empty($fromZone)) {
        $tariffsFilter->addTariffsDetailsJoin();
        $tariffsFilter->addFieldFilter('    td.from_zone_id', $fromZone);
        $this->filerColumn = 'DISTINCT t.*, td.`from_zone_id`';
    }

    $isActive = $filterData['status'];
    if ($isActive == 1) {
        $tariffsFilter->addFieldFilter('   t.status', $isActive);
    }
    if ($isActive == '0') {
        $tariffsFilter->addFieldFilter('   t.status', $isActive);
    }
    $isType = $filterData['type'];
    if (!empty($isType)) {
        $tariffsFilter->addFieldFilter('   t.tariff_type', $isType);
    }
    $tariffData = $tariffsFilter->getList(' id');
    if (!empty($tariffData)) {
        foreach ($tariffData as $tariff) {
            $tariffId = $tariff->getId();
            $tariffObj = new Tariffs($tariffId);
            $carrierId = $tariffObj->getCarrierId();
            if($carrierId > 0){
                $remoteareasGroupsFilter = new RemoteareasGroupsFilter;
                $remoteareasGroupsFilter->addFilter("AND carrier_id = ".$carrierId);
                $remoteareasGroupsList = $remoteareasGroupsFilter->getColumnList("id");
                foreach ($remoteareasGroupsList as $remoteareasGroupsSng) {
                    $groupListStr .= "'".$remoteareasGroupsSng->getId()."',";
                }
            }

            $groupListNew = rtrim($groupListStr,',');
            $remoteareasType = trim($formData['remoteareas_type']);

            parse_str($formData['group'],$group);
            parse_str($formData['from_weight'],$fromWeight);
            parse_str($formData['to_weight'],$toWeight);
            parse_str($formData['charges'],$charges);
            parse_str($formData['formula'],$formula);

            $remoteareaChargesTariffOldData = new RemoteAreaChargesTariffsFilter();
            $remoteareaChargesTariffOldData->addFilter(" tariff_id = ".$tariffId."   ");
            $oldRemoteareaChargesTariff = $remoteareaChargesTariffOldData->getList();

            $oldAuditData = [];
            $newAuditData = [];
            if(!empty($oldRemoteareaChargesTariff)) {
                foreach ($oldRemoteareaChargesTariff as $datum) {
                    $groupObj = new RemoteareasGroups($datum->getRemoteAreaGroupId());
                    $oldAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " . $datum->getRemoteareaCharges() ."<br />From Weight: ". $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight(). '<br />Formula: ' . $datum->getFormulla();
                }
            }

            //Delete pervious records
            RemoteAreaChargesTariffs::deleteByTariffId($tariffId);
            foreach ($group['group'] as $key => $groupList) {
                if(!empty($groupList)){
                    $groupObj = new RemoteareasGroups($groupList);
                    $remoteAreaChargesTariff = new RemoteAreaChargesTariffs();
                    $remoteAreaChargesTariff->setRemoteareaGroupId($groupList);
                    $remoteAreaChargesTariff->setRemoteareaCharges($charges['charges'][$key]);
                    $remoteAreaChargesTariff->setTariffId($tariffId);
                    if($remoteareaType=='ON_WEIGHT'){
                        $remoteAreaChargesTariff->setFromWeight($fromWeight['from_weight'][$key]);
                        $remoteAreaChargesTariff->setToWeight($toWeight['to_weight'][$key]);
                        $remoteAreaChargesTariff->setFormulla($formula['formula'][$key]);
                    }
                    $remoteAreaChargesTariff->setAddedBy($userAccount->getId());
                    $remoteAreaChargesTariff->setAddedDate(time());
                    $remoteAreaChargesTariff->setIsDeleted('N');
                    $remoteAreaChargesTariff->save();

                    if($remoteareaType=='ON_WEIGHT'){
                        $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges['charges'][$key] ."<br />From Weight: ". $fromWeight['from_weight'][$key]. '<br />To Weight: ' . $toWeight['to_weight'][$key]. '<br />Formula: ' . $formula['formula'][$key];
                    }else{
                        $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges['charges'][$key]  ;
                    }
                }
            }
            $newDataAudit = [];
            $oldDataAudit = [];
            if(!empty($newAuditData)) {
                $newDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $newAuditData);
            }
            if(!empty($oldAuditData)) {
                $oldDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $oldAuditData);
            }
            $old_data = json_encode($oldAuditData);
            $new_data = json_encode($newAuditData);
            $userAudit = new UserAudit();
            $table_key = $tariffId;
            $userAudit->allowAdd = true;
            $userAudit->dataName = $tariffObj->name;
            $userAudit->insertAuditData('tariffs', 'TariffRemoteArea', $userAccount->getFirstName() . ' ' . $userAccount->getLastName(), $userAccount->getId(), $new_data, $table_key, $old_data, $userAccount->getFirstName() . ' ' . $userAccount->getLastName() . ' update remoarea charges for tariff ' . $tariffObj->getName());
        }
    }

    $output["status"] = "success";
    $output["message"] = "Remotearea saved successfully";
    return $output;
}
function upload_csv_file_remotearea($formData) {
         $userAccount = SessionManager::getUser();
            $tariffId = trim($formData['tariffId']);
            $remoteareaType = trim($formData['remotearea_type']);
            $tariffObj = new Tariffs($tariffId);
            $carrierId = $tariffObj->getCarrierId();
         
             $remoteareaChargesTariffOldData = new RemoteAreaChargesTariffsFilter();
            $remoteareaChargesTariffOldData->addFilter(" tariff_id = ".$tariffId."   "); 
            $oldRemoteareaChargesTariff = $remoteareaChargesTariffOldData->getList();
              
            $oldAuditData = [];
            $newAuditData = [];
            if(!empty($oldRemoteareaChargesTariff)) {
                foreach ($oldRemoteareaChargesTariff as $datum) {
                    $groupObj = new RemoteareasGroups($datum->getRemoteAreaGroupId());
//                     $oldAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                "Charges" =>  $datum->getRemoteareaCharges(),
//                                "From Weight" => $datum->getFromWeight(),
//                                "To Weight" => $datum->getToWeight(),
//                                "Formula" =>$datum->getFormulla() );
                     $oldAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " . $datum->getRemoteareaCharges() ."<br />From Weight: ". $datum->getFromWeight() . '<br />To Weight: ' . $datum->getToWeight(). '<br />Formula: ' . $datum->getFormulla();
                }
            }
            
            $groupsArray = array();                  
            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
            $remoteareasGroupsFilter->addFilter("AND  is_deleted='N'  AND carrier_id = " . $carrierId);
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
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name =   "tid".$tariffId."_". time() . "_" . $basename;

                    
                    chdir('../');
                    chdir('_assets');
                    $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
                   $newDirectoryPath = $currentDirecotryPath . '/tariffs';
                   $newDirectoryUploadPath = $newDirectoryPath . '/remotearea_import/';
                    if (!file_exists($currentDirecotryPath . '/tariffs')) {                        
                        @mkdir($newDirectoryPath, 0775);
                         if (!file_exists($newDirectoryUploadPath)) {                        
                        @mkdir($newDirectoryUploadPath, 0775);
                    } 
                    }  
 
                 $relPath = $newDirectoryUploadPath . $new_file_name;
                 if(file_exists($newDirectoryUploadPath) ){
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = $outmessage= '';
                            $successRecords = 0;
                            $errorRecords = 0;                           
                            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                if($row>1){
                                     $validate = false;
                                    if ($remoteareaType == 'ON_WEIGHT') {
                                            $groupName = trim($data[0]);
                                            $fromWeight = trim($data[1]);
                                            $toWeight = trim($data[2]);
                                            $charges = trim($data[3]);
                                            $formula = trim($data[4]);
                                            if($fromWeight>=0 && $toWeight>=0 && $charges>0){
                                                $validate = true;
                                            }
                                        } else {
                                            $groupName = trim($data[0]);
                                            $charges = trim($data[1]);
                                            $fromWeight = 0;
                                            $toWeight = 0;
                                            $formula = 0;
                                            if($charges>0){
                                                $validate = true;
                                            }
                                        }
                                        ////// if group name exists//////
                                        $groupID = array_search($groupName, $groupsArray);
                                   if(!empty($groupID) && $validate){
                                        $remoteareaChargesTariffFilter = new RemoteAreaChargesTariffsFilter();
                                         if($remoteareaType=='ON_WEIGHT'){
                                            $remoteareaChargesTariffFilter->addFilter(" remotearea_group_id =".$groupID." AND tariff_id = ".$tariffId." AND from_weight = ".$fromWeight." AND to_weight = ".$toWeight." ");  
                                         }else if($remoteareaType=='ON_PIECE'){
                                            $remoteareaChargesTariffFilter->addFilter(" remotearea_group_id =".$groupID." AND tariff_id = ".$tariffId."   ");  
                                         }
                                       
                                        $remoteareaChargesTariffFilterResults = $remoteareaChargesTariffFilter->getList();
                                        //////Check new insertion or update//////
                                        if(count($remoteareaChargesTariffFilterResults)>0){
                                            foreach($remoteareaChargesTariffFilterResults as $remoteareaChargesTariff){
                                                        $remoteareaChargesTariff->setRemoteareaCharges($charges);
                                                        $remoteareaChargesTariff->setFormulla($formula);
                                                        $remoteareaChargesTariff->setUpdatedDate(time());
							$remoteareaChargesTariff->setUpdatedBy($userAccount->getId());
                                                        $remoteareaChargesTariff->save(); 
                                            }
                                        }else{
                                                $remoteAreaChargesTariff = new RemoteAreaChargesTariffs();
                                                $remoteAreaChargesTariff->setRemoteareaGroupId($groupID);
                                                $remoteAreaChargesTariff->setRemoteareaCharges($charges);
                                                $remoteAreaChargesTariff->setTariffId($tariffId);
                                                 if($remoteareaType=='ON_WEIGHT'){
                                                $remoteAreaChargesTariff->setFromWeight($fromWeight);
                                                $remoteAreaChargesTariff->setToWeight($toWeight);
                                                $remoteAreaChargesTariff->setFormulla($formula);
                                                 }
                                                $remoteAreaChargesTariff->setAddedBy($userAccount->getId());
                                                $remoteAreaChargesTariff->setAddedDate(time());
                                                $remoteAreaChargesTariff->setIsDeleted('N');
                                                $remoteAreaChargesTariff->save(); 
                                        }
                                        $groupObj = new RemoteareasGroups($groupID);
                                        if($remoteareaType=='ON_WEIGHT'){
//                                        $newAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                               "Charges" => $charges,
//                                               "From Weight" => $fromWeight,
//                                               "To Weight" => $toWeight,
//                                               "Formula" => $formula) ;
                                        
                                         $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges ."<br />From Weight: ". $fromWeight. '<br />To Weight: ' . $toWeight. '<br />Formula: ' . $formula;
                                       }else{
//                                                $newAuditData[] =  array("Group Name" => $groupObj->getGroupName(),
//                                               "Charges" => $charges) ;
                                                 $newAuditData[] = "Group Name: " . $groupObj->getGroupName() ."<br />Charges: " .$charges  ;

                                        }

                                      $successRecords++;
                                   }else{
                                         $errorRecords++;
                                   }                                  
                                }
                                $row++;
                            }
                            
                            $newDataAudit = [];
                                 $oldDataAudit = [];
                                if (!empty($newAuditData)) {
                                    $newDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $newAuditData);
                                }
                                if (!empty($oldAuditData)) {
                                    $oldDataAudit[$tariffObj->getName()] = implode('<br /><br /> ', $oldAuditData);
                                }
                                $old_data = json_encode($oldAuditData);
                                $new_data = json_encode($newAuditData);
                                $userAudit = new UserAudit();
                                $table_key = $tariffId;
                                $userAudit->allowAdd = true;
                                $userAudit->insertAuditData('tariffs', 'insert', $userAccount->getFirstName() . ' ' . $userAccount->getLastName(), $userAccount->getId(), $new_data, $table_key, $old_data, $userAccount->getFirstName() . ' ' .$userAccount->getLastName() . ' update remoarea charges for tariff ' . $tariffObj->getName());

                            $outmessage .= 'CSV Uploaded Successfully';
                            $outmessage .= '<br /> ' . $successRecords . ' remote area charges updated successfully.';
                            $outmessage .= '<br /> ' . $errorRecords . ' remote area charges not imported due to invalid data in csv.';
                            
                            $output['message'] = $outmessage;
                             $output['status'] = 'success';
                        }
                    } else {
                        $output['message'] = 'File upload fail.';
                        $output['status'] = 'fail';
                    }
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

function download_tariff_remotearea_template($formData) {
   $tariffId = $formData['tariffId'];
            $carrierId = (int) $formData['carrierId'];
          $remoteareaType = trim($formData['remoteareaType']); 
            /*  Download Csv Code  */
            $fileName = "tariff_remotearea_import.csv";
            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            if($remoteareaType=='ON_WEIGHT'){
                fputcsv($output, array('Group Name', 'From Weight', 'To Weight','Charges','Formula'));
            }else{
               fputcsv($output, array('Group Name','Charges')); 
            }
             $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                        $remoteareasGroupsFilter->addFilter("AND is_deleted = 'N' AND carrier_id = " . $carrierId);
                        $remoteareasGroupsNames = $remoteareasGroupsFilter->getColumnList("group_name");
                        if (count($remoteareasGroupsNames) > 0) {
                            foreach ($remoteareasGroupsNames as $remoteareasGroupsName) {
                                $groupName = $remoteareasGroupsName->getGroupName();
                                if($remoteareaType=='ON_WEIGHT'){
                                fputcsv($output, array($groupName,'','','',''));
                                }else{
                                     fputcsv($output, array($groupName,'' ));
                                }
                            }
                        }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
}