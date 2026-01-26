<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'quotationdetails.class',
    'quotationdetailsfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'country.class',
    'countryfilter.class',
    'parcel.class',
    'parcelfilter.class', 'services.class',
    'servicefilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'cslog.class',
    'cslogfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'consignmentdetails.class',
    'consignmentdetailsfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'consignmentchargeslog.class',
    'consignmentchargeslogfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'consignmentchargeslogtypes.class',
    'consignmentchargeslogtypesfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'tariff.class',
    'tarifffilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
]);


if (isset($_POST['action']) && trim($_POST['action']) == 'CUSTOM_EXPORT') {
    $output = [];
    $userSession = SessionManager::getUser();
    $custom_export_number = $_POST['custom_export_number'];
    $id = $_POST['id'];
    $ConsignmentDetails = new ConsignmentDetails();
    $ConsignmentDetails->setConsignmentId($id);
    $ConsignmentDetails->setCustomExportNumber($custom_export_number);
    $ConsignmentDetails->save();

    $ConsignmentLog = new ConsignmentLog();
    $ConsignmentLog->createlog($userSession->getUserName() . " has added Custom Export Number " . $custom_export_number, $id);
    $output["status"] = "success";
    $output["message"] = "Custom Export Number save sucessfully.";
    echo json_encode($output);
    exit;
}
if (isset($_POST['action']) && trim($_POST['action']) == 'SAVEHIGHVALUE') {
    $userSession = SessionManager::getUser();
    $highlowvalue = $_POST['highlowvalue'];
    $id = $_POST['id'];
    $consignment = new ConsignmentFilter();
    $consignment->addIdFilter($id);
    $listConsignment = $consignment->getColumnList('hv_lv');
    if (count($listConsignment) > 0) {
        $old_value = $listConsignment[0]->getHvLv();
        $listConsignment[0]->setHvLv($highlowvalue);
        $listConsignment[0]->save();
        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog($userSession->getUserName() . " has changed value type from  " . $old_value . " to " . $highlowvalue, $id);
        echo "Success";
        exit;
    }
}
if (isset($_POST['action']) && trim($_POST['action']) == 'UPDATE_NOT_SCANNED_SHIPMENTS') {
    $consignment_ids_str = $_POST['consignment_ids'];
    $booked_date_tmp = $_POST['booked_date'];
    if (!empty($consignment_ids_str) && !empty($booked_date_tmp)) {
//        $date_obj = new DateTime($booked_date_tmp);
//        $booked_date = $date_obj->format("");
        $booked_date = strtotime($booked_date_tmp);
        $consignmentObj = new Consignment();
        Consignment::runQuery("UPDATE consignment SET date_booked = '" . $booked_date . "' WHERE (date_booked IS NULL OR date_booked = '0') AND id IN(" . $consignment_ids_str . ")");
        $consignment_ids_arr = explode(",", $consignment_ids_str);
        $user = SessionManager::getUser();
        $user_id = $user->getId();
        if (count($consignment_ids_arr) > 0) {
            $logSql = "INSERT INTO consignment_log(message,userid,logdate,ipaddress,consignmentid,type)VALUES";
            $count = 0;
            foreach ($consignment_ids_arr as $consignment_id) {
                $logSql .= ($count > 0 ? ',' : '') . "('Update date booked to " . $booked_date . "','" . $user_id . "','" . date("Y-m-d H:i:s") . "','" . ip2long($_SERVER['REMOTE_ADDR']) . "','" . $consignment_id . "','M')";
                $count++;
            }
        }
        Consignment::runQuery($logSql);
    }
    echo "success";
    exit;
}
//load weight discrepancy panel
if (isset($_POST['action']) && trim($_POST['action']) == 'WEIGHT_DISCREPANCY') {
    $weight = $_POST['weight'];
    $id = $_POST['id'];
    $output = [];
    $html = '<div class="portlet portlet-light">
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <tr>
                                    <td>Customer Weight (Kgs):</td>
                                    <td>' . $weight . '</td>
                            </tr>
                            <tr>
                                    <td>Actual Weight (Kgs):</td>
                                    <td><input type="text" class="form-control" name="customerweight" id="customerweight"  onkeyup="differenceweight(' . $weight . ')" /></td>
                            </tr>
                            <tr>
                                    <td>Difference in Wt (Kgs): </td>
                                    <td><input type="text" class="form-control" name="diffweight" id="diffweight" value="" readonly /></td>
                            </tr>
                            <tr>
                                    <td colspan="2">
                                    					
                                    </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>';
    $links = '<input type="button" class="btn btn-primary" id="savediscrepancy" name="savediscrepancy" value="Save" onclick="saveWeight(' . $id . ',' . $weight . ')" />
             <input type="button" class="btn btn-primary" id="canceldiscrepancy" name="canceldiscrepancy" value="Cancel" onclick="Cancelbutton()"  />';
    $output['html'] = $html;
    $output['links'] = $links;
    echo json_encode($output);
}



//load weight discrepancy panel
if (isset($_POST['action']) && trim($_POST['action']) == 'IS_UNTRACK_SERVICE_AVAILABLE') {
    $carrierName = $_POST['carriers'];
    $services = new ServiceFilter();
    $services->addCarrierFilter($carrierName);
    $services->addFieldFilter('is_untrack', 'YES');
    $getlist = $services->getColumnList(' id, is_untrack ');
    if (count($getlist) > 0) {
        echo "AVAILABE";
    } else {
        echo "UNAVAILABE";
    }

    die;
}
//load weight discrepancy panel
if (isset($_POST['action']) && trim($_POST['action']) == 'GET_SERVICE_NAMEDETAILS') {
    $fromdate = $_POST['fromdate'];
    $todate = $_POST['todate'];
    $account = $_POST['account'];
    $carrierName = $_POST['carriers'];
    $invoicedoption = $_POST['invoicedoption'];
    if ($invoicedoption == 'invoiced')
        $invoiceOpt = 'YES';
    elseif ($invoicedoption == 'not invoiced')
        $invoiceOpt = 'NO';
    else
        $invoiceOpt = '';

    $ServiceArray = array();
    $services = new ServiceFilter();
    //$services->addCarrierFilter($carrierName);
    $getlist = $services->getCourierServiceList($fromdate, $todate, $account, $carrierName, $invoiceOpt);
    if (count($getlist) > 0) {
        foreach ($getlist as $listData) {
            $code = $listData->getCode();
            $name = $listData->getName();
            $ServiceArray[$code] = $name;
        }
    }
    echo json_encode($ServiceArray);
    exit;
}

//load weight discrepancy panel
if (isset($_POST['action']) && trim($_POST['action']) == 'GET_SERVICE_NAME') {
    $carrierName = $_POST['carriers'];
    $services = new ServiceFilter();
    $services->addCarrierFilter($carrierName);
    $getlist = $services->getCourierServiceList('code, name ');
    if (count($getlist) > 0) {
        foreach ($getlist as $listData) {
            $code = $listData->getCode();
            $name = $listData->getName();
            echo '<option value="' . $code . '">' . $name . '</option>';
        }
    } else
        echo '<option value="">No Service Available</option>';

    die;
}


//Save new weight
if (isset($_POST['action']) && trim($_POST['action']) == 'GET_ACCOUNTS_ON_DATE') {
    $fromdate = strtotime(trim($_POST['fromdate']));
    $todate = strtotime(trim($_POST['todate']));
    $invoiceType = trim($_POST['invoiceType']);
    $billType = trim($_POST['billType']);
    $accountNumber = array();

    $consignment = new ConsignmentFilter();
    $consignment->addDateBookedRangeFilter($fromdate, $todate);

    if (trim($invoiceType) == 'not invoiced')
        $consignment->addFilter(" (InvoiceId <= 0 OR InvoiceId = '' OR InvoiceId IS NULL ) and IsInvoiced <> 'Y' ");
    else if (trim($invoiceType) == 'invoiced')
        $consignment->addFilter(" InvoiceId > 0 and IsInvoiced = 'Y' ");

    $consignment->addFilter(" consignment_status not in ('recycled', 'invalid') ");

    if (trim($billType) == 'true') {
        $consignment->addFilter(" warehouse_id in ('9','10') ");
    }

    if (isset($billType) && trim($billType) == 'true') {
        $filter = new ServiceFilter();
        $filter->addFieldFilter("owe_account", $billType);
        $serviceList = $filter->getColumnList(" code ");

        $serviceArray = array();
        foreach ($serviceList as $serviceData)
            $serviceArray[] = $serviceData->getCode();

        if (count($serviceArray) > 0)
            $returnOnlyString = " handling IN ('RTN" . implode("','RTN", $serviceArray) . "', '" . implode("','", $serviceArray) . "')";
        else
            $returnOnlyString = " handling IN ('NON')";

        $consignment->addFilter($returnOnlyString);
    }

    $consignment->AddOrderByAccount(true);
    $listConsignment = $consignment->getDistinctColumnList(' SQL_CACHE distinct( user_code ),  account');
    if (count($listConsignment) > 0) {
        foreach ($listConsignment as $list) {
            $accountNumber[$list->getUserCode()] = $list->getAccount();
        }
    }
    echo json_encode($accountNumber);
    exit;
}



//Save new weight
if (isset($_POST['action']) && trim($_POST['action']) == 'GET_ACCOUNTS_CARRIERS_NAME') {
    $fromdate = strtotime(trim($_POST['fromdate']));
    $todate = strtotime(trim($_POST['todate']));
    $account = trim($_POST['account']);
    $parent = trim($_POST['parent']);
    $invoicedoption = trim($_POST['invoicedoption']);
    if ($invoicedoption == 'invoiced')
        $invoiceOpt = 'YES';
    elseif ($invoicedoption == 'not invoiced')
        $invoiceOpt = 'NO';
    else
        $invoiceOpt = '';

    if ($invoicedoption == 'not invoiced')
        $CarrierNumber = array();


    $accountCode = '0';

    $userFilter = new UserAccountFilter();
    $userFilter->addFilter(" user_code= '" . $account . "'");
    $getUserColumnlistlist = $userFilter->getColumnList(' user_account ');
    if (count($getUserColumnlistlist) > 0) {
        $accountCode = $getUserColumnlistlist[0]->getUserAccount();
    }



    $sFilter = new ServiceFilter();
    $sList = $sFilter->getCourierList($fromdate, $todate, $accountCode, $invoiceOpt, $parent);
    if (count($sList) > 0) {
        foreach ($sList as $list) {
            $CarrierNumber[] = $list->getCarrier();
        }
    }
    echo json_encode($CarrierNumber);
    exit;
}


//Save new weight
if (isset($_POST['action']) && trim($_POST['action']) == 'SAVE_WEIGHT') {

    $userSession = SessionManager::getUser();
    $cweight = trim($_POST['cweight']);
    $weight = trim($_POST['weight']);
    $id = trim($_POST['id']);


    if ($cweight == $weight || $cweight == "") {
        echo "There is no discrepancy in weight.";
        return;
    } else {
        $consignment = new ConsignmentFilter();
        $consignment->addFilterNew(" id = '$id'");
        $listConsignment = $consignment->getListNew('c.id, c.weight,c.update_weight, c.hawb, c.awb, c.number_pieces, is_dead_weight_chargable,charge_weight ');
        if (count($listConsignment) > 0) {
            foreach ($listConsignment as $list) {
                $list->setWeight($cweight);
                $list->setUpdateWeight($weight);
                if($list->getIsDeadWeightChargable() == 1 )
                {
                    $chargableWeight = $cweight;
                }
                else{
                    $service = new Services($list->getServiceId());
                    if($service->getValidationType() == "mail"){
                        $chargableWeight = $cweight;
                    }
                    else
                    {                 
                        if($cweight > $weight)
                            $chargableWeight = $cweight;
                        else
                            $chargableWeight = $weight;
                    }
                }
                $list->setChargeWeight($chargableWeight);
                
                $outTariff = Consignment::updateTariffUsingConsignmentId($list->getId());
                if($outTariff['status'] == 'error')
                {
                    $ConsignmentLog = new ConsignmentLog();
                    $ConsignmentLog->createlog($outTariff['message'], $list->getId());
                    echo $outTariff['message'];
                    return;
                }
                else
                {
                    $list->save();
                }
            }
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog($userSession->getUserName() . " has updated weight " . $weight . " to " . $cweight . " ", $id);
            echo "New Weight Save successfully.";
            return;
        } else {
            echo "There is error in updating weight.";
            return;
        }
    }
}

//VOLUME WINDOW LOAD
if (isset($_POST['action']) && trim($_POST['action']) == 'VOLUME_DISPLAY') {
    $id = $_POST['id'];
    $volbase = $_POST['volbase'];
    $isInvoiced = $_POST['isInvoiced'];
    $consignment = new Consignment($id);
    $numberOfPieces = $consignment->getNumberPieces();
    $parcelsConsignment = $consignment->getParcels();
    $html = '';
    $output = [];
    if (count($parcelsConsignment) > 0) {
        foreach ($parcelsConsignment as $i => $parcellData) {
            $nop = $i + 1;
//			<input type="hide" class="form-control" id="ptracking' . $i . '" name="ptracking' . $i . '" value="'.$parcellData->getTrackingNumber().'" />
            $html .= '<div class="row">
                            <div class="col-md-2">		
                                <label>Pcs</label>
                                <input type="text"  id="pcs' . $i . '" name="pcs' . $i . '" value="1"  class="form-control"/> 
                                <input type="hidden" class="form-control" id="ptracking' . $i . '" name="ptracking' . $i . '" value="' . $parcellData->getTrackingNumber() . '" />
                            </div>
                            <div class="col-md-2">			
                                <label>L Cm</label>
                                <input type="text"  id="length' . $i . '" name="length' . $i . '" onchange="calcuVolWeight(' . $i . ');" onblur="calcuVolWeight(' . $i . ');" value="' . $parcellData->getLength() . '" class="form-control"/> 
                            </div> 
                            <div class="col-md-2">	 
                                <label>B Cm</label>
                                <input type="text"  id="width' . $i . '" name="width' . $i . '" onchange="calcuVolWeight(' . $i . ');" onblur="calcuVolWeight(' . $i . ');" value="' . $parcellData->getWidth() . '" class="form-control"/> 
                            </div> 
                            <div class="col-md-2">	
                                <label>H Cm</label>
                                <input type="text"  id="height' . $i . '" name="height' . $i . '" onchange="calcuVolWeight(' . $i . ');" onblur="calcuVolWeight(' . $i . ');" value="' . $parcellData->getHeight() . '"  class="form-control"/> 
                            </div>  
                            <div class="col-md-2">
                                <label>Kg</label>
                                <input type="text" id="kg' . $i . '" name="kg' . $i . '"  class="totalkgweight form-control" readonly onblur="totalVolWeight(' . $i . ');" onchnage="totalVolWeight(' . $i . ');" /> 
                            </div>';
                        $html .= '<div class="col-md-2" '.((int)$volbase  > 0 ? ' style="display: none;" ' : '').'>
                                <label>Vol.Deno.</label>
                                <input type="text" id="vol_denometer' . $i . '"  name="vol_denometer' . $i . '" '.((int)$volbase  > 0 ? ' value="'.$volbase.'" ' : ' value="5000"'). ' class=" form-control" onchange="calcuVolWeight(' . $i . ');" onblur="calcuVolWeight(' . $i . ');" /> 
                            </div>';
                        $html .= '</div>			
                        <script>
                            calcuVolWeight("' . $i . '");
                            totalVolWeight("' . $i . '");
                        </script>';
            /* 	<div class="col-md-2">		
              <label>Actual Weight</label>
              <input type="text" id="actualweight' . $i . '" name="actualweight' . $i . '"  class="form-control"/>
              </div> */
        }
    }
    /*    for ($i = 0; $i < 5; $i++) {
      $html .= '<tr>
      <td><input type="text" class="form-control" id="pcs' . $i . '" name="pcs' . $i . '"  /> </td>
      <td><input type="text" class="form-control" id="length' . $i . '" name="length' . $i . '"  /> </td>
      <td><input type="text" class="form-control" id="width' . $i . '" name="width' . $i . '" /> </td>
      <td><input type="text"  class="form-control" id="height' . $i . '" name="height' . $i . '"onchange="calcuVolWeight(' . $i . ');" onblur="calcuVolWeight(' . $i . ');" /> </td>
      <td><input type="text" id="kg' . $i . '" name="kg' . $i . '"  class="form-control" readonly onblur="totalVolWeight(' . $i . ');" onchnage="totalVolWeight(' . $i . ');" /> </td>
      <td><input type="text" id="actualweight' . $i . '" name="actualweight' . $i . '"  class="form-control"/> </td>
      </tr>';
      } */
    $stringDisable = '';
    if (trim($isInvoiced) == 'Y') {
        $stringDisable = 'disabled="disabled"';
    }
    $html .= '<br clear="all"/>
        <div class="row">	
            <div class="col-md-4"><strong>Volumetric Base:</strong>  &nbsp;&nbsp; ' . (int) $volbase . '</div>
            <div class="col-md-8">
                <div class="col-md-6"><strong>Total Volumetric Weight:</strong></div>
                <div class="col-md-6">		
                    <input type="text" id="totalvolweight" name="totalvolweight"   class="form-control" readonly />
                </div>
            </div>    
        </div>';

    $links = '<input type="button" class="btn btn-primary" id="savevolume" name="savevolume" value="SAVE" ' . $stringDisable . ' onclick="saveVolume(' . $id . ',\'' . $numberOfPieces . '\')" />
                <input type="button" class="btn btn-danger" id="cancelVolume" name="cancelVolume" value="CANCEL" onclick="Cancelbutton()"  />';
    $output['html'] = $html;
    $output['links'] = $links;
    echo json_encode($output);
}
//SAVE PARCEL DETAILS
if (isset($_POST['action']) && trim($_POST['action']) == 'SAVE_VOLUME') {
    $pcs = explode("||", $_POST['pcs']);
    $length = explode("||", $_POST['length']);
    $id = $_POST['id'];
    $width = explode("||", $_POST['width']);
    $height = explode("||", $_POST['height']);
    $totalWeight = $_POST['totalWeight'];
    $ptracking = $_POST['ptracking'];
    $volweight = $_POST['volweight'];


    $consignment = new Consignment($id);
    $consignment->setUpdateWeight($totalWeight);
    $consignment->setVolWeight($totalWeight);
    if($consignment->getIsDeadWeightChargable() == 1 )
    {
        $chargableWeight = $consignment->getWeight();
    }
    else{
        $service = new Services($consignment->getServiceId());
        if($service->getValidationType() == "mail"){
            $chargableWeight = $consignment->getWeight();
        }
        else
        {                 
            if($totalWeight > $consignment->getWeight())
                $chargableWeight = $totalWeight;
            else
                $chargableWeight =$consignment->getWeight();
        }
    }
    $consignment->setChargeWeight($chargableWeight);
    
    
    $consignment->save();
    
    $outTariff = Consignment::updateTariffUsingConsignmentId($consignment->getId());
    if($outTariff['status'] == 'error')
    {
        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog($outTariff['message'], $consignment->getId());
        echo json_encode($outTariff);
        die;
    }
                                                
                                                

    $parcelFilter = new ParcelFilter();
    $parcelFilter->addConsignmentIdFilter($id);
    $parcelData = $parcelFilter->getList();
    if (count($parcelData) > 0) {
        $i = 0;
        foreach ($parcelData as $parcel) {
            $parcel->setConsignmentId($id);
            $parcel->setQty($pcs[$i]);
            $parcel->setLength($length[$i]);
            $parcel->setWidth($width[$i]);
            $parcel->setHeight($height[$i]);
            $parcel->setPweight($volweight[$i]);
            $parcel->save();
            $i++;
        }
    } else {
        $parcel = new Parcel();
        $parcel->setConsignmentId($id);
        $parcel->setQty($pcs);
        $parcel->setLength($length);
        $parcel->setWidth($width);
        $parcel->setHeight($height);
        $parcel->setPweight($volweight);
        $parcel->save();
    }
    echo "Parcel volumetric weight updated successfully.";
}

//Display Agent Details
if (isset($_POST['action']) && trim($_POST['action']) == 'CUSTOMER_DISPLAY') {
    $agentid = $_POST['agentid'];
    $usertelephone = $_POST['usertelephone'];
    $agentdata = new AgentData($agentid);
    $output = [];
    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                    <tr>
                            <td>Shipper Details:</td>
                            <td>' . $usertelephone . '</td>
                    </tr>
                    <tr>
                            <td>Agent Details: </td>
                            <td></td>
                    </tr>
                    <tr>
                            <td>Telephone: </td>
                            <td>' . $agentdata->getTelephone() . '</td>
                    </tr>
                    <tr>
                            <td>Alternative Telephone: </td>
                            <td>' . $agentdata->getAlternative1Telephone() . '</td>
                    </tr>
                  
            </table>';
    $links = '<input type="button" class="btn btn-primary" id="savecontact" name="savecontact" value="Ok" onclick="Cancelbutton()" />';
    $output['html'] = $html;
    $output['links'] = $links;
    echo json_encode($output);
}
//UPDATE STATUS
if (isset($_POST['action']) && trim($_POST['action']) == 'UPDATE_STATUS') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->addIdFilter($id);
    $consignmentRecords = $consignmentFilter->getList();
    if (count($consignmentRecords) > 0) {
        $consignment = $consignmentRecords[0];
        $StatusLog = new StatusLog();
        $StatusLog->setHawb($consignment->getHawb());
        $StatusLog->setStatus($status);
        $StatusLog->setUserAccount($consignment->getAccount());
        $StatusLog->setDateCreated(strtotime(date("Y-m-d H:i:s")));
        $StatusLog->save();

        $consignment->setConsignmentStatus($status);
        $consignment->save();

        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog("Consignment Status Updated to " . $status, $id);

        echo "Status updated succesfully.";
    } else {
        echo "Consignment Data not found.";
    }
}

if (isset($_POST['action']) && trim($_POST['action']) == 'FETCH_LOG_MESSAGE') {
    $logDetailData = array();
    $logId = $_POST['logId'];
    $messageFor = $_POST['messageFor'];
    $csfilter = new CsLogFilter();
    $csfilter->addFieldFilter('id', $logId);
    $list = $csfilter->getList();

    if (count($list) > 0) {
        $listData = $list[0];
        $logDetailData[] = $listData->getId();
        $logDetailData[] = $listData->getInternalMessage();
        $logDetailData[] = $listData->getCustomerMessage();
        $logDetailData[] = date("Y-m-d H:i", $listData->getReminderExpiry());
    }

    echo json_encode($logDetailData);
    die;
}

//Display Log Details
if (isset($_POST['action']) && trim($_POST['action']) == 'UPDATE_LOG') {
    $id = $_POST['id'];
    $cList = new Consignment((int) $id);
    $serviceId = $cList->getServiceId();
    $userObj = new User($cList->getUserId());
    $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
    $account = $userAccountObj->getUserAccount();
    $output = [];
    if (trim($serviceId) != '')
        $agentEmail = getAgentEmail($serviceId);
    else
        $agentEmail = '';

    if (trim($account) != '')
        $customerEmail = getCustomerEmail($account);
    else
        $customerEmail = '';

    
    $csfilter = new CsLogFilter();
    $csfilter->addFieldFilter('consignment_id', $id);
    $list = $csfilter->getList();
    $count = count($list);
    
    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                <tr>
                    <td>Date</td>
                    <td>User Log</td>
                    <td>Customer Log</td>
                    <td>Customer Mail</td>
                    <td>User Mail</td>
                    <td>Customer Send</td>
                    <td>Agent Send</td>
                    <td>Added By</td>
                </tr>';
    if (count($list) > 0) {
        $i = 0;
        foreach ($list as $clist) {
            $userlog = substr($clist->getInternalMessage(), 0, 10);
            $customerlog = substr($clist->getCustomerMessage(), 0, 10);
            if ($clist->getUserId() > 0) {
                $ufilter = new UserAccountFilter();
                $ufilter->addAccoutnIdOrFilter($clist->getUserId());
                $uList = $ufilter->getColumnList("user_account,user_name");
                if (count($uList) > 0) {
                    $username = $uList[0]->getUserName();
                } else
                    $username = "";
            } else {
                $username = "Auto Script";
            }
            
            $html .= '<tr>
                        <td>' . date("d-m-Y", $clist->getDateCreated()) . '</td>
                        <td>
                            <a tabindex="0"
                                data-html="true" 
                                data-toggle="popover" 
                                data-trigger="focus" 
                                title="<b>User Log</b>" 
                                data-content="<div>'.$clist->getInternalMessage().'</div>">'.$userlog.'
                            </a>
                        </td>
                        <td>
                            <a tabindex="0"
                                data-html="true" 
                                data-toggle="popover" 
                                data-trigger="focus" 
                                title="<b>Customer Log</b>" 
                                data-content="<div>'.$clist->getCustomerMessage().'</div>">'.$customerlog.'
                            </a>
                        </td>
                        <td>' . $clist->getCustMail() . '</td>
                        <td>' . $clist->getAgentMail() . '</td>
                        <td><input type="checkbox" value="' . $clist->getId() . '" id="csend' . $i . '" name="csend' . $i . '"   />&nbsp;</td>
                        <td><input type="checkbox" value="' . $clist->getId() . '" id="asend' . $i . '" name="asend' . $i . '"   />&nbsp;</td>
                        <td>' . $username . '</td>
                    </tr>';
            $i++;
        }
        
    } else {
        $html .= '<tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
						  <td></td>
                </tr>';
    }
    $html .= '<tr><td colspan="8">Internal Log</td></tr>
                <tr>
                    <td colspan="8"> <textarea name="internallog" id="internallog" rows="4" cols="30"  class="form-control"  style="cursor: copy;"></textarea></td>
                </tr>
                <tr>
                    <td colspan="8">Customer Log</td>
                </tr>
                <tr>
                    <td colspan="8"><textarea name="customerlog" id="customerlog" rows="4" cols="30"  class="form-control"  style="cursor: copy;"></textarea></td>
                </tr>
        </table>';
    /* <tr>
      <td colspan="7">
      <div class="col-md-4">
      <div class="form-group">
      <label class="label-control">
      REMINDER: </label>
      <input id="followup_meeting_date" name="followup_meeting_date"  type="text" data-date-format="yyyy-mm-dd hh:ii"  class="form-control"  placeholder="Follow Up Meeting Date" title="Follow Up Meeting Date"/>
      </div>
      </div>
      </td>
      </tr> */
    $links = '<input type="button"  class="btn btn-primary" id="savelog" name="savelog" value="Add" onclick="AddLogbutton(' . $id . ')" />			
                <input type="button" class="btn btn-primary" id="cancellog" name="cancellog" value="Cancel" onclick="Cancelbutton()" />			
                <input type="button" class="btn btn-primary" id="customerEmail" name="customerEmail" value="Customer Email" onclick="SendLogEmail(\'' . $customerEmail . '\', \'customer\',' . $count . ')" />			
                <input type="button" class="btn btn-primary" id="agentEmail" name="agentEmail" value="Agent Email" onclick="SendLogEmail(\'' . $agentEmail . '\', \'agent\', ' . $count . ')" />			
                <input type="button" class="btn btn-primary" id="forwardEmail" name="forwardEmail" value="FORWARD" onclick="forwardLogEmail(' . $id . ')" />';
    
    $output['html'] = utf8_encode($html);
    $output['links'] = $links;
    echo json_encode($output);
}
//ADD LOG
if (isset($_POST['action']) && trim($_POST['action']) == 'ADD_LOG') {
    $id = $_POST['id'];
    $internallog = $_POST['internallog'];
    $customerlog = $_POST['customerlog'];
    $reminder = $_POST['reminder'];


    addCsLog($id, $internallog, $customerlog, $reminder);
}

function addCsLog($id, $internallog, $customerlog, $reminder = '') {
    $CsLog = new CsLog();
    $CsLog->setConsignmentId($id);
    $CsLog->setInternalMessage($internallog);
    $CsLog->setCustomerMessage($customerlog);
    if ($reminder != '' && $reminder != '1970-01-01 00:00:00')
        $CsLog->setReminderExpiry(strtotime($reminder));
    //$CsLog->setReminder($reminder);


    /* $date = date("Y-m-d");

      if($reminder == "hourly")
      {
      $expire = date("Y-m-d, H:i:s", strtotime('+1 hour'));
      $CsLog->setReminderExpiry(strtotime($expire));
      }
      else if($reminder == "weekly")
      {
      $expire =  strtotime(date("Y-m-d", strtotime($date)) . " +1 week");
      $CsLog->setReminderExpiry(($expire));
      }
      else if($reminder == "monthly")
      {

      $expire =  strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
      $CsLog->setReminderExpiry(($expire));
      } */

    $CsLog->setCustMail("No");
    $CsLog->setAgentMail("No");
    $CsLog->setDateCreated(strtotime(date("Y-m-d H:i:s")));
    $CsLog->setUserId(SessionManager::getUser()->getId());
    $CsLog->save();
    $ConsignmentLog = new ConsignmentLog();
    $ConsignmentLog->createlog("Log Added" . $status, $id);
    echo "Log Added succesfully.";
}

//SEND EMAIL TO CUSTOMER AND AGENT LOG
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_FARWORD_EMAIL') {
    $id = $_POST['id'];
    $cmail = $_POST['cmail'];
    $amail = $_POST['amail'];
    $cmailcc = $_POST['cmailcc'];
    $amailcc = $_POST['amailcc'];
    $email_message = nl2br($_POST['message']);

    $cList = getConsignmentData($id);


    if (count($cList) > 0) {
        $serviceid = $cList->getServiceId();
        $servideDetail = new Services($serviceid);
        $handling = $servideDetail->getCode();
        $userObj = new User($cList->getUserId());
        $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
        $account = $userAccountObj->getUserAccount();
        if ($amail == "agent") {
            $agentEmail = getAgentEmail($handling);
        } else {
            $amail == "";
        }


        if ($cmail == "customer") {
            $customerEmail = getCustomerEmail($account);
        } else {
            $cmail == "";
        }


        $status = getTrackingStatus($cList->getId());

        $message = "Dear Customer<br><br>Please find the below details for your shippment. <br> <br><table class='table table-striped table-bordered table-advance table-hover' style='width:100%'>
					<thead>
						<tr>
							<th>Hawb No</th>
							<th>Date of Hawb</th>
							<th>Your Ref No.</th>
							<th>Destination</th>
							<th>Pieces</th>
							<th>Weight(Kg)</th>
							<th>Date Time</th>
							<th>Status</th>					
						</tr>
					</thead>
					<tr><td colspan='8'><hr/></td></tr>
					<tr>
						<td>" . $cList->getHawb() . "</td>
						<td>" . date("Y-m-d", $cList->getDateLabelCreated()) . "</td>
						<td>" . $cList->getReference() . "</td>
						<td>" . $cList->getCompany() . "<br>" . $cList->getAddressLine1() . "<br>" . $cList->getCity() . "<br>" . $cList->getCountry() . "<br>" . $cList->getPostcode() . "</td>
						<td>" . $cList->getNumberPieces() . "</td>
						<td>" . $cList->getWeight() . "</td>
						<td>" . $cList->getDateBooked() . "</td>
						<td>" . $status . "</td>
					</tr>
					
					<tr><td colspan='8'><hr/></td></tr>
					
					<tr>
						<td colspan='2'><strong>Log Details</strong></td>
						<td colspan='6'><font color='#FF0000'>" . $email_message . "</font></td>
					</tr>
					<tr>
						<td colspan='8'>Thanks and regards,<br><br>
							One World
						</td>
					</tr>
					</table>";

        $email = $customerEmail . ";" . @$agentEmail;
        $subject = " LOG INFORMATION.";
        $headers = "From: smart@smarttrack.co \r\n";
        $headers .= "Reply-To: cs@oneworldexpress.com \r\n";

        $headers .= 'Cc: cs@oneworldexpress.com;' . $cmailcc . ";" . $amailcc . ";\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        if (mail($email, $subject, $message, $headers)) {
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Email Forward to " . $email, $id);
            addCsLog($id, '', $email_message);
            echo "Email send successfully. ";
        } else
            echo "There is an error, Please contact to administrator on cs@oneworldexpress.com.";
    }
    exit;
}



//SEND EMAIL TO CUSTOMER AND AGENT LOG
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_EMAIL') {


    $email = $_POST['email'];
    $user = $_POST['user'];
    $clogid = $_POST['clogid'];
    $clogid = explode(",", $clogid);
    $email_message = "";
    $consignment_id = "";
    foreach ($clogid as $logid) {

        $CsLog = new CsLog($logid);
        $email_message .= "*" . $CsLog->getCustomerMessage() . "<br>";
        if ($consignment_id == "")
            $consignment_id = $CsLog->getConsignmentId();
        if ($user == "customer")
            $CsLog->setCustMail("Yes");
        else
            $CsLog->setAgentMail("Yes");

        $CsLog->save();
    }


    $cList = getConsignmentData($consignment_id);
    $userObj = new User($cList->getUserId());
    $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
    $account = $userAccountObj->getUserAccount();

    if (count($cList) > 0) {
        $countryObj = new Country($cList->getCountryId());
        $status = getTrackingStatus($cList->getId());
        if ($status == "")
            $status = $cList->getConsignmentStatus();

        $message = "<p>Dear Customer, <br><br>POD Information for HAWB No " . $cList->getHawb() . ".</p>";
        $message .= "<table class='table table-striped table-bordered table-advance table-hover'>
                          <tr>
								<td><b>Hawb No</td>
								<td><b>Date of Hawb</td>
								<td><b>Your <br>Ref No.</td>
								<td><b>Destination</td>
								<td><b>Pieces</td>
								<td><b>Weight(Kg)</td>
								<td><b>Date Time</td>
								<td><b>Status</td>					
						</tr>
						<tr>
							<td colspan='8'>------------------------------------------------------------------------------------------------------------------------------------------------------------</td>
						</tr>
                        <tr>
                                <td><a href='https://www.oneworldexpress.co.uk/remote/main/tracking.php?tracking_number=" . $cList->getHawb() . "'>" . $cList->getHawb() . "</a></td>	
                                <td>" . date("Y-m-d", $cList->getDateLabelCreated()) . "</td>
                                <td>" . $cList->getReference() . "</td>
                                <td>" . $cList->getCompany() . "<br>" . $cList->getAddressLine1() . "<br>" . $cList->getCity() . "<br>" . $countryObj->getName() . "<br>" . $cList->getPostcode() . "</td>
                                <td>" . $cList->getNumberPieces() . "</td>
                                <td>" . $cList->getWeight() . "</td>
                                <td>" . $cList->getDateBooked() . "</td>
                                <td>" . $status . "</td>
                        </tr>
                        <tr>
                                <td colspan='8'>" . $email_message . "</td>
                        </tr>
                      
                        </table>";
        $message .= "<p><br><br>
			For more information on your shipment kindly click on the house airwaybill above or visit us at www.oneworldexpress.com and login into our members area with your user name and password for detailed information. Alternatively kindly call our Customer Service Team on +44 (0) 208 867 6060.
			<br><br>Thanks & Regards, <br> One World
			</p>";
        $subject = $account . " , POD 	Status Information.";
        $headers = "From: smart@smarttrack.co \r\n";
        $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headers .= 'Cc: cs@oneworldexpress.com; ' . "\r\n";

        mail($email, $subject, $message, $headers); {
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Email Sent to " . $email, $cList->getId());
            echo "Email send to " . $user . " successfully.";
            addCsLog($cList->getId(), '', $email_message);
        }
    }
    die;
}


//SHOW STATUS HISTORY
if (isset($_POST['action']) && trim($_POST['action']) == 'HISTORY_STATUS') {
    $id = $_POST['id'];
    $statusFilter = new StatusLogFilter();
    $statusFilter->addFieldFilter("hawb", $id);
    $statusList = $statusFilter->getColumnList("hawb,status,user_account,date_created");
    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                <tr>
                    <td>HawbNo</td>
                    <td>Status</td>
                    <td>User</td>
                    <td>Date Created</td>
                </tr>';
    if (count($statusList) > 0) {
        foreach ($statusList as $statusl) {
            $html .= '<tr>
                        <td>' . $statusl->getHawb() . '</td>
                        <td>' . $statusl->getStatus() . '</td>
                        <td>' . $statusl->getUserAccount() . '</td>
                        <td>' . date("d-m-Y", $statusl->getDateCreated()) . '</td>
                     </tr>';
        }
    }
    $html .= '</table>';
    echo $html;
}

//SHOW STATUS HISTORY
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_FORWARDEMAIL') {
    $id = $_POST['id'];
    $internalmessage = $_POST['internalmessage'];
    $customermessage = $_POST['customermessage'];

    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                <tr>
                    <td>
                        <fieldset>
                            <legend>Send Mail To:</legend>
                            <input type="checkbox" id="cemail" name="cemail" value="customer" /> Customer
                            <input type="checkbox" id="aemail" name="aemail" value="agent" /> Agent		
						</fieldset>
                    </td>
					
                    <td>
                        Customer Mail CC <br/>
                        <input type="text" id="cmailcc" name="cmailcc"  class="form-control"/> <br>
                        Agent Mail CC <br/>
                        <input type="text" id="amailcc" name="amailcc"  class="form-control"/> <br>						

                    </td>
                </tr>
				
                <tr>
                    <td colspan="2">
                        <textarea rows="4" cols="50" id="emailmessage" name="emailmessage"  class="form-control" background: #fada5e; color:#000;">' . $internalmessage . '  ' . $customermessage . '</textarea>
                    </td>
                </tr>
				
                <tr>
                    <td colspan="2">
                        <input type="button" class="btn btn-primary" id="sendemail" name="sendemail" value="Send" onclick="SendLogFarwordEmail(' . $id . ')" />			
                        <input type="button" class="btn btn-primary" id="cancellog" name="cancellog" value="Cancel" onclick="Cancelbutton()" />	
                    </td>
                </tr>';
    $html .= '</table>';
    echo $html;
}

// SHOW CARRIER EMAIL
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_CARRIEREMAIL') {
    $id = $_POST['id'];
    $fileFullPath = "";
    
    if($id > 0){
        $consignment = new Consignment($id);
        $serviceId= $consignment->getServiceId();
        $agentId = $consignment->getAgentId();
        $awb = $consignment->getAwb();
        $serviceAgentFilter = new ServiceAgentMappingDataFilter();
        $serviceAgentFilter->addAgentIDFilter($agentId);
        $serviceAgentFilter->addServiceIDFilter($serviceId);
        $serviceAgentList = $serviceAgentFilter->getList();
        $carrierEmail = "";
        if(count($serviceAgentList) > 0)
        {
            foreach($serviceAgentList as $salist)
            {
                $carrierEmail .= $salist->getEmail() ."; " . $salist->getEmailFinance();
            }
        }
        $invoicePath = "../_assets/paperless_invoice/";
        $fileName = md5($id) . ".pdf";
        if(file_exists($invoicePath . $fileName)){
           $invoiceFile =  $fileName;
            $documentName = explode(".", $invoiceFile);
            $fileFullPath = SETTING_URL_ASSETS . "paperless_invoice/" . $invoiceFile;
        }
    }
    $output = [];
    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                <tr>
                    <td>
                        Carrier Mail <br/>
                        <input type="text" id="carrieremail" name="carrieremail" value="'.$carrierEmail.'"  class="form-control"/> <br>
                    
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea rows="4" cols="150" id="emailmessage" name="emailmessage"  class="form-control" >
Dear Team,
Please find below link for custom documents for Tracking Number '.$awb.'.
'.$fileFullPath.'

Thanks & Regards
Customer Service Team 
One World Express
                        </textarea>
                    </td>
                </tr>';
    $html .= '</table>';

    $links = '<input type="button" class="btn btn-primary" id="sendemail" name="sendemail" value="Send" onclick="SendCarrierCustomEmail(' . $id . ')" />			
                        <input type="button" class="btn btn-primary" id="cancellog" name="cancellog" value="Cancel" onclick="Cancelbutton()" />';
    $output['html'] = $html;
    $output['links'] = $links;
    echo json_encode($output);
}


//SHOW STATUS HISTORY
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_PODEMAIL') {
    $id = $_POST['id'];
    $output = [];
    $html = '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                <tr>
                    <td>
                        <fieldset>
                            <legend>Send Mail To:</legend>
                            <input type="checkbox" id="cemail" name="cemail" value="customer" /> Customer
                            <input type="checkbox" id="aemail" name="aemail" value="agent" /> Agent							 
                        </fieldset>
                    </td>
                    <td>
                        Customer Mail CC <br/>
                        <input type="text" id="cmailcc" name="cmailcc"  class="form-control"/> <br>
                        Agent Mail CC <br/>
                        <input type="text" id="amailcc" name="amailcc"  class="form-control"/> <br>
                    </td>
                </tr>
				<tr>
					<td>
						  <label class="label-control">
						  REMINDER: </label>
						  <input id="followup_meeting_date" name="followup_meeting_date"  type="text" data-date-format="yyyy-mm-dd hh:ii"  class="form-control"  placeholder="Follow Up Meeting Date" title="Follow Up Meeting Date"/>
					</td>
					<td>
					&nbsp;
					</td>
				</tr>
                <tr>
                    <td colspan="2">
                        <textarea rows="4" cols="50" id="emailmessage" name="emailmessage"  class="form-control" style="    background: #fada5e; color:#000;"></textarea>
                    </td>
                </tr>';
    $html .= '</table>';

    $links = '<input type="button" class="btn btn-primary" id="sendemail" name="sendemail" value="Send" onclick="SendPodEmail(' . $id . ')" />			
                        <input type="button" class="btn btn-primary" id="cancellog" name="cancellog" value="Cancel" onclick="Cancelbutton()" />';
    $output['html'] = $html;
    $output['links'] = $links;
    echo json_encode($output);
}



//SEND POD STATUS EMAIL
if (isset($_POST['action']) && trim($_POST['action']) == 'SENDLOGEMAIL') {
    $id = @$_POST['id'];
    $cmail = @$_POST['cmail'];
    $amail = @$_POST['amail'];
    $cmailcc = @$_POST['cmailcc'];
    $amailcc = @$_POST['amailcc'];
    $message = @nl2br($_POST['message']);

    $cList = getConsignmentData($id);
    if (count($cList) > 0) {
        $serviceid = $cList->getServiceId();
        $servideDetail = new Services($serviceid);
        $handling = $servideDetail->getCode();
        $userObj = new User($cList->getUserId());
        $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
        $account = $userAccountObj->getUserAccount();
        if ($amail == "agent") {
            $agentEmail = getAgentEmail($handling);
        } else {
            $amail == "";
        }


        if ($cmail == "customer") {
            $customerEmail = getCustomerEmail($account);
        } else {
            $cmail == "";
        }


        $email = @$customerEmail . "; " . @$agentEmail;

        $subject = @$user . ", Log Information.";
        $headers = "From: smart@smarttrack.co \r\n";
        $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
        $headers .= "CC: " . $cmailcc . ";" . $amailcc . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


        $emailTemplate = 'Dear Customer, <br><br>';
        $emailTemplate .= '<table width="850" cellspacing="1" cellpadding="0" border="0" style="color:maroon; width:100%">
                                <thead>
                                    <tr>
                                        <th >Hawb No</th>
                                        <th>Date of Hawb</th>
                                        <th>Your Ref No .</th>
                                        <th>Destination</th>
                                        <th>Pieces</th>
                                        <th>Customer Specified Weight (Kg)</th>
                                        <th>Description</th>
                                        <th>Value</th>
                                        <th>Pod Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="9"><hr width="100%" size="2" align="center"></td></tr>
                                <tr>
                                    <td>' . $cList->getHawb() . '</td>
                                    <td>' . date("d M Y", strtotime($cList->getDatePrinted())) . '</td>
                                    <td>' . $cList->getReference() . '</td>
                                    <td>' . $cList->getCountry() . '</td>
                                    <td>' . $cList->getNumberPieces() . '</td>
                                    <td>' . $cList->getWeight() . '</td>
                                    <td>' . $cList->getDescription() . '</td>
                                    <td> ' . $cList->getCurrency() . ' ' . $cList->getValue() . '</td>
                                    <td> ' . $cList->getConsignmentStatus() . '</td>
                                </tr>
                            </tbody>
                        </table>';
        $emailTemplate .= '<br><br><br><b><br>Log Details<br><hr></b>';
        $emailTemplate .= '<table width="100%">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><strong style="color:maroon; width:35%;">' . date("d M Y", time()) . '</strong></td>
                                        <td ><strong style="color:maroon">' . $message . '</strong></td>
                                    </tr>
                                </tbody>
                            </table>';
        $emailTemplate .= '<br><br><br><br>Thanks And Regards,<br>';
        $emailTemplate .= 'One World Express Ltd';

        if (mail($email, $subject, $emailTemplate, $headers)) {
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Email Sent to " . $email, $id);
            echo "Email send to " . @$user . " successfully.";
        } else {
            echo "Email sending failed, There is some error. Please contact to administrator.";
        }
    }
}



//SEND POD STATUS EMAIL
if (isset($_POST['action']) && trim($_POST['action']) == 'SENDEMAILPOD') {
    $id = $_POST['id'];
    $cmail = $_POST['cmail'];
    $amail = $_POST['amail'];
    $cmailcc = $_POST['cmailcc'];
    $amailcc = $_POST['amailcc'];
    $emailcontent = nl2br($_POST['message']);
    $reminder = $_POST['reminder'];
    $message = "";

    $cList = getConsignmentData($id);

    if (count($cList) > 0) {
        $awb = $cList->getAwb();
        $hawb = $cList->getHawb();
        $serviceid = $cList->getServiceId();
        $servideDetail = new Services($serviceid);
        $handling = $servideDetail->getCode();
        $userObj = new User($cList->getUserId());
        $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
        $account = $userAccountObj->getUserAccount();
        if ($amail == "agent") {
            $agentEmail = getAgentEmail($handling);
            $message = "<p>Dear Agent, <br><br> Can you please provide POD Status of below Shipments.<br><br></p>";
        } else {
            $amail == "";
        }
        

        if ($cmail == "customer") {
            $customerEmail = getCustomerEmail($account);
            $message = "<p>Dear Customer, <br></br> One World Express appreciates your business. Kindly find below details of your shipment(s) we have received and the current status on it. <br><br>
			</p>";
        } else {
            $cmail == "";
        }
    }
    $status = getTrackingStatus($id);
    if ($status == "")
        $status = $cList->getConsignmentStatus();
    
    $lastTrackingPoint = getLastPodTracking($cList);
    if(!empty($lastTrackingPoint)){
        $podStatus = $lastTrackingPoint->getCarrierDesc();
        $podDate = date("Y-m-d", strtotime($lastTrackingPoint->getDateCreated()));
        $podTime = date("H:i:s", strtotime($lastTrackingPoint->getDateCreated()));
    }
    $message .= "<p>" . $emailcontent . "<br></br></p>";
    $message .= "<b>AWB:</b> " . $cList->getAwb();
    $message .= "<br><b>HAWB:</b> " . $cList->getHawb();
    $message .= "<br><b>EXPORT DATE:</b> " . $cList->getDateBooked() . " <b>ORIGIN:</b> LHR";
    $message .= "<br><br><b>DESTINATION:</b><br> " . $cList->getCompany() . "<br>" . $cList->getAddressLine1() . "<br>" . $cList->getCity() . "<br>" . $cList->getCountry() . "<br>" . $cList->getPostcode();

    if ($amail == "agent")
        $message .= "<p><br><br><b><u>Please Reply to this E-Mail Supplying POD/Status Information Below<br></br></p>";

    $message .= "<br><br><b>DATE:  ". $podDate ." <br><br> TIME: ". $podTime ."<br><br> POD/STATUS: ". $podStatus ." </u>";
    $message .= "<br><br>Sent By " . $_SESSION['admin']['user_name'] . "<br> One World Express";

    $email = @$customerEmail . ";" . @$agentEmail;

    $subject = " OWE Shipment Notification " . $awb . " / " . $hawb;
    $headers = "From: smart@smarttrack.co \r\n";
    $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
    $headers .= 'Cc: cs@oneworldexpress.com;' . $cmailcc . ";" . $amailcc . ";\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    mail($email, $subject, $message, $headers);

    $ConsignmentLog = new ConsignmentLog();
    $ConsignmentLog->createlog("POD Email Sent to " . $email, $id);
    addCsLog($id, '', $emailcontent, $reminder);

    echo "Email send to " . @$user . " successfully.";
}

// SEND CARRIER CUStOM EMAIL
if (isset($_POST['action']) && trim($_POST['action']) == 'SEND_CARRIER_CUSTOM_EMAIL') {
    $id = $_POST['id'];
    $carrieremail = $_POST['carrieremail'];
    $emailcontent = nl2br($_POST['message']);
    $message = "";

    $cList = getConsignmentData($id);

    if (count($cList) > 0) {
        $awb = $cList->getAwb();
        
    }
    
    $message .= "<p>" . $emailcontent . "<br></br></p>";
    
    $subject = " OWE Shipment Custom Documents " . $awb ;
    $headers = "From: smart@smarttrack.co \r\n";
    $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
   // $headers .= 'Cc: cs@oneworldexpress.com;' .  ";\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    mail($carrieremail, $subject, $message, $headers);

    echo "Email send  successfully.";
}


//Resend Email for Weight
if (isset($_POST['action']) && trim($_POST['action']) == 'ResendWeightEmail') {
    $output = [];
    $vol_weight = 0;
    $id = $_POST['id'];
    $cList = getConsignmentData($id);

    $parcelFilter = new ParcelFilter();
    $parcelFilter->addConsignmentIdFilter($id);
    $parcelList = $parcelFilter->getColumnList('length,width,height,pweight');

    if (count($cList) > 0) {
        $userObj = new User($cList->getUserId());
        $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
        $account = $userAccountObj->getUserAccount();
        $customerEmail = getCustomerEmail($account);
        $volbase = $cList->getVolDemonimator();

        $countryObj = new Country($cList->getCountryId());
    }

    $status = getTrackingStatus($id);
    if ($status == "")
        $status = $cList->getConsignmentStatus();

    $date = date("Y-m-d", $cList->getDateLabelCreated());
    if ($date == '1970-01-01')
        $date = '';
    $message = "<p>Dear Customer, <br><br>Please note that there is a discrepancy for Ref No " . $cList->getHawb() . " / ". $cList->getAwb(). ".</p>";
    $message .= "<table class='table table-striped table-bordered table-advance table-hover' width='100%'>
                    <tr>
                        <td><b>Hawb No / Tracking No</td>
                        <td><b>Date of Hawb</td>
                        <td><b>Your Ref No.</td>
                        <td><b>Destination</td>
                        <td><b>Pieces</td>
                        <td><b>Customer<br> Specified<br> Weight(Kg)</td>
                        <td><b>Date Time</td>
                        <td><b>Status</td>					
                    </tr>
                    <tr>
                        <td colspan='8' style=' border-bottom: thin solid #000;'></td>
                    </tr>
                    <tr>
                        <td><a href='https://www.smarttrack.co/tracking.php?tracking_number=" . $cList->getHawb() . "'>" . $cList->getHawb() . "</a><br /><br />
                            <a href='https://www.smarttrack.co/tracking.php?tracking_number=" . $cList->getAwb() . "'>" . $cList->getAwb() . "</a>
                        </td>	
                        <td>" . $date . "</td>
                        <td>" . $cList->getReference() . "</td>
                        <td>" . $cList->getCompany() . "<br>" . $cList->getAddressLine1() . "<br>" . $cList->getCity() . "<br>" . $countryObj->getName() . "<br>" . $cList->getPostcode() . "</td>
                        <td>" . $cList->getNumberPieces() . "</td>
                        <td>" . $cList->getUpdateWeight() . "</td>
                        <td>" . ($cList->getDateBooked() > 0 ? date('d-m-Y',$cList->getDateBooked()):date('d-m-Y',$cList->getDateLabelCreated()) ) . "</td>
                        <td>" . $status . "</td>
                    </tr>
                    <tr>
                        <td colspan='8'><br><br></td>
                    </tr>
                    <tr>
                        <td colspan='8'><br><br></td>
                    </tr>";
    if (count($parcelList) > 0) {

        $message .= "<tr>
                        <td><b>Hawb No</td>
                        <td><b>Pieces</td>
                        <td><b>Length (cm)</td>
                        <td><b>Width (cm)</td>
                        <td><b>Height (cm)</td>
                        <td><b>Vol Wt (Kg)</td>
                    </tr>
                    <tr>
                        <td colspan='8' style=' border-bottom: thin solid #000;'></td>
                    </tr>";
        foreach ($parcelList as $p) {

			if($volbase>0)
				$volweight = (($p->getLength() * $p->getWidth() * $p->getHeight()) / $volbase);
			else
				$volweight = '';
            $message .= "<tr>
                            <td><a href='https://www.smarttrack.co/tracking.php?tracking_number=" . $cList->getHawb() . "'>" . $cList->getHawb() . "</a></td>	
                            <td>" . $cList->getNumberPieces() . "</td>
                            <td>" . $p->getLength() . "</td>
                            <td>" . $p->getWidth() . "</td>
                            <td>" . $p->getHeight() . "</td>
                            <td>" . $volweight . "</td>
                        </tr>";
            $vol_weight += $volweight;
        }

        $message .= "<tr>
                        <td colspan='8'><br><br></td>
                    </tr>
                    <tr><td colspan='8'><center>Total volumetric weight is : " . number_format(@$vol_weight, 2, '.', '') . "</center></td></tr>";
    }
    $message .= "</table>";

    $message .= "<p>The weights as calculated by One World are as follows :<br> 
                    Actual Weight: " . $cList->getWeight() . " Kg. <br>
                    Volumetric Weight: " . number_format(@$vol_weight, 2, '.', '') . " Kg. <br><br>
                    For more information on your shipment kindly click on the house airwaybill above or visit us at www.oneworldexpress.com and login into our members area with your user name and password for detailed information. Alternatively kindly call our Customer Service Team on +44 (0) 208 867 6060.
					<br><br>Thanks & Regards, <br> One World
                </p>";
    
    $subject = $userAccountObj->getUserAccount() . " , Weight Discrepancy Advise for Ref No. " . $cList->getHawb() . " / " . $cList->getAwb();
    $headers = "From: smart@smarttrack.co \r\n";
    $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= 'Cc: cs@oneworldexpress.com, kiran.patel@oneworldexpress.com, finance@oneworldexpress.com' . "\r\n";

    if (mail($customerEmail, $subject, $message, $headers)) {
        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog("Weight Email ReSent to " . $customerEmail, $id);
        $output['status'] = "success";
        $output['message'] = "Email send successfully.";
    } else {
        $output['status'] = "error";
        $output['message'] = "There is some issue with the mail server. Please contact to cs@oneworldexpress.com";
    }
    echo json_encode($output);
}

//update pmp address
if (isset($_POST['action']) && trim($_POST['action']) == 'UPDATEPMPADDRESS') {
    $id = $_POST['id'];
    $routineId = $_POST['routineId'];
    $cList = getConsignmentData($id);
    $pmpfilter = new PmpRoutineFilter();
    $pmpfilter->addFieldFilter("id", "=", $routineId);
    $routinelist = $pmpfilter->getColumnList('id, storeid, depot_no');

    if (count($routinelist) > 0) {
        $rlist = $routinelist[0];
        $storeid = $rlist->getStoreid();
        $depotno = $rlist->getDepotNo();
        $other_routing_code = $storeid . "||" . $depotno . "||" . $cList->getOtherRoutingCode();
        $cList->setOtherRoutingCode($other_routing_code);
        $cList->save();
    }
}

//Change Address
if (isset($_POST['action']) && trim($_POST['action']) == 'GETPMPRETURNADDRESS') {
    $id = $_POST['id'];
    $cList = getConsignmentData($id);
    $postcode = $cList->getPostcode();
    $pmpclass = new PassMyParcelLabel();
    $routinelist = $pmpclass->getRoutineDetails($postcode);
    $output = array();
    if (count($routinelist) > 0) {
        $message = "";
        $message = ' <table class="table table-striped table-bordered table-hover" id="manage-data-table">
							  <thead>
									<tr role="row" class="heading">
										 <th width="5%"> Actions </th>
										 <th> Store Address </th>
									</tr>';
        foreach ($routinelist as $routine) {
            $message .= '
									<tr role="row" class="filter">
										 <td>
											  <div>
											  <a href="javascript:;" class="btn btn-primary" onclick="choosePmpStore(\'' . $routine->getId() . '\', \'' . $id . '\');" id= "selected_' . $routine->getId() . '"  >Select</a>
											  </div>
											  
										 </td>
										 <td>' . $routine->getStoreName() . '<br/ >' . $routine->getAddressLine1() . '<br />' . $routine->getAddressLine2() . '<br />' . $routine->getCity() . '<br />' . $routine->getPostcode() . '<br /> UNITED KINGDOM' . '</td>
									</tr>';
        }
        $message .= ' </thead>
							  </table>';
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = $message;
    } else {
        $output["STATUS"] = "FAIL";
        $output["MESSAGE"] = "Enable to find pass my parcel store in your area. Please contact itsupport@oneworldexpress.com";
    }
    echo json_encode($output);
    exit;
}

//Resend PMP for Weight
//Resend Email for Weight
if (isset($_POST['action']) && trim($_POST['action']) == 'ResendPMPEmail') {

    $id = $_POST['id'];
    $cList = getConsignmentData($id);

    $routineDetail = $cList->getOtherRoutingCode();
    $routine = explode("||", $routineDetail);
    $storeId = $routine[0];
    $pmpfilter = new PmpRoutineFilter();
    $pmpfilter->addFieldFilter("storeid", "=", $storeId);
    $routinelist = $pmpfilter->getList();
    if (count($routinelist) > 0) {
        $rlist = $routinelist[0];
        $PassMyParcelLabel = new PassMyParcelLabel();
        $PassMyParcelLabel->sendEmail($rlist, $cList, $cList->getAwb());
        echo "Email Resend Successfully.";
    }
}

//Resend Email for Volumatric Weight
if (isset($_POST['action']) && trim($_POST['action']) == 'ResendVolumeEmail') {
    $output = [];
    $id = $_POST['id'];
    $cList = getConsignmentData($id);

    if (count($cList) > 0) {
        $countryObj = new Country($cList->getCountryId());
        $userObj = new User($cList->getUserId());
        $userAccountObj = new CustomerAccount($userObj->getUserAccountId());
        $account = $userAccountObj->getUserAccount();
        $handling = $cList->getServiceId();

        $pFilter = new ParcelFilter();
        $pFilter->addConsignmentIdFilter($cList->getId());
        $pList = $pFilter->getColumnList("consignment_id,length,width,height,weight,pweight");

       // $sList = new Services($handling);
		$volbase = $cList->getVolDemonimator();
        $customerEmail = getCustomerEmail($account);
    }

    $status = getTrackingStatus($id);
    if ($status == ""){
    $status = $cList->getShipmentStatus();
    if (Consignment::STATUS_LABEL_CREATED == trim($status))
        $status = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray line-height-2"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) . '</span>';
    else if (strtolower($c_status) == "booked")
        $status = '<span class="label label-sm bg-blue-dark bg-font-blue-dark"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DISPATCHED]) . '</span>'; //= Translation::GetCaption("SHIPPED");
    else if (Consignment::STATUS_RECYCLED == trim($status))
        $status = '<span class="label label-sm label-danger"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_RECYCLED]) . '</span>';
    else if (Consignment::STATUS_DELIVERED == trim($status))
        $status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DELIVERED]) . '</span>';
    else if (Consignment::STATUS_INVALID == trim($status))
        $status = '<span class="label label-sm label-warning"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) . '</span>';
    else if (Consignment::STATUS_READY_TO_PRINT == trim($status))
        $status = '<span class="label label-sm label-info"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_READY_TO_PRINT]) . '</span>';
    else
        $status = '<span class="label label-sm bg-default bg-font-default"> ' . Translation::GetCaption($c_status) . '</span>';
//        $status = $cList->getConsignmentStatus();
    }   
    $message = "<p>Dear Customer, <br>Please note that there is a discrepancy for HAWB No " . $cList->getHawb() . ".</p>";
    $message .= "<table class='table table-striped table-bordered table-advance table-hover' width='100%'>
                    <tr>
                        <td><b>Hawb No</td>
                        <td><b>Date of Hawb</td>
                        <td><b>Your Ref<br> No.</td>
                        <td><b>Destination</td>
                        <td><b>Pieces</td>
                        <td><b>Customer<br> Specified<br> Weight(Kg)</td>
                        <td><b>Date Time</td>
                        <td><b>Status</td>					
                    </tr>
					 <tr>
                        <td colspan='8'  style=' border-bottom: thin solid #000;'></td>
                    </tr>
                    <tr>
                        <td><a href='https://www.oneworldexpress.co.uk/remote/main/tracking.php?tracking_number=" . $cList->getHawb() . "'>" . $cList->getHawb() . "</a></td>	
                        <td>" . date("Y-m-d", $cList->getDateLabelCreated()) . "</td>
                        <td>" . $cList->getReference() . "</td>
                        <td>" . $cList->getCompany() . "<br>" . $cList->getAddressLine1() . "<br>" . $cList->getCity() . "<br>" . $countryObj->getName() . "<br>" . $cList->getPostcode() . "</td>
                        <td>" . $cList->getNumberPieces() . "</td>
                        <td>" . $cList->getUpdateWeight() . "</td>
                        <td>" . ($cList->getDateBooked()>0 ? date("d-m-Y",$cList->getDateBooked()):date("d-m-Y",$cList->getDateLabelCreated())) . "</td>
                        <td>" . $status . "</td>
                    </tr>
                    <tr>
                        <td colspan='8'><br><br></td>
                    </tr>
                    <tr>
                        <td><b>Hawb No</td>
                        <td><b>Pieces</td>
                        <td><b>Length (CM).</td>
                        <td><b>Width (CM)</td>
                        <td><b>Height (CM)</td>
                        <td><b>Vol Wt (KG)</td>
                        <td><b>Vol Base</td>
                        <td></td>
                    </tr>
					 <tr>
                        <td colspan='8' style=' border-bottom: thin solid #000;'></td>
                    </tr>";
    $totalvolWeight = 0;
    if (count($pList) > 0) {
        foreach ($pList as $parcel) {
			if($volbase>0)
				$vol_weight = (($parcel->getLength() * $parcel->getWidth() * $parcel->getHeight()) / $volbase );
			else
				$vol_weight = '';
            $totalvolWeight += $vol_weight;
            $message .= "<tr>
                            <td><a href='https://www.oneworldexpress.co.uk/remote/main/tracking.php?tracking_number=" . $cList->getHawb() . "'>" . $cList->getHawb() . "</a></td>	
                            <td>" . $cList->getNumberPieces() . "</td>
                            <td>" . $parcel->getLength() . "</td>
                            <td>" . $parcel->getWidth() . "</td>
                            <td>" . $parcel->getHeight() . "</td>
                            <td>" . $vol_weight . "</td>
                            <td>" . $volbase . "</td>
                            <td></td>
                        </tr>";
        }
    }


    $message .= "</table>
                    <p>Total Volumetric Weight is : " . $totalvolWeight . "</p><br />
                    <p>The weights as calculated by One World are as follows :<br> 
                    Actual Weight: " . $cList->getWeight() . " Kg. <br>
                    Weight at One World: " . $totalvolWeight . " Kg. <br><br>
                    For more information on your shipment kindly click on the house airwaybill above or visit us at www.oneworldexpress.com and login into our members area with your user name and password for detailed information. Alternatively kindly call our Customer Service Team on +44 (0) 208 867 6060.
					<br><br>Thanks & Regards, <br> One World
                </p>";

    $subject = $userAccountObj->getUserAccount() . " , Weight Discrepancy Advise for HAWB No " . $cList->getHawb() . " .";
    $headers = "From: smart@smarttrack.co \r\n";
    $headers .= "Reply-To: cs@oneworldexpress.com \r\n";
    
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= 'Cc: cs@oneworldexpress.com, kiran.patel@oneworldexpress.com, finance@oneworldexpress.com' . "\r\n";
    //if(mail($customerEmail.',itsupport@oneworldexpress.com', $subject, $message, $headers))
    if (mail($customerEmail, $subject, $message, $headers)) {
        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog("Volume Email ReSent to " . $customerEmail, $id);
        $output['status'] = "success";
        $output['message'] = "Email send successfully.";
    } else {
        $output['status'] = "error";
        $output['message'] = "There is some issue with the mail server. Please contact to cs@oneworldexpress.com";
    }
    echo json_encode($output);
}

function getConsignmentData($id) {
    $consignment = new Consignment($id);
//    $consignment = new ConsignmentFilter($id);
//    $consignment->addIdFilter($id);
//    $cList = $consignment->getColumnList("id, consignment_status, service_id, hawb, awb, user_id, date_label_created, reference, company, contact, address_line_1, city, country_id, postcode, number_pieces, weight, date_booked, update_weight, vol_demonimator, other_routing_code, email ");
//    $List = $cList[0];
//    return $List;
    return $consignment;
}

function getCustomerEmail($account) {
    $ufilter = new UserAccountFilter();
    $ufilter->addUserAccountFilter($account);
    $uList = $ufilter->getColumnList("user_account,email,alternative_email");
    if (count($uList) > 0) {
        if ($uList[0]->getEmail() != "") {
            $customerEmail = $uList[0]->getEmail();
        } else if ($uList[0]->getAlternativeEmail() != "") {
            $customerEmail = $uList[0]->getAlternativeEmail();
        } else {
            $customerEmail = "cs@oneworldexpress.com,kiran.patel@oneworldexpress.com, finance@oneworldexpress.com";
        }
    } else {
        $customerEmail = "cs@oneworldexpress.com,kiran.patel@oneworldexpress.com, finance@oneworldexpress.com";
    }
    return $customerEmail;
}

function getTrackingStatus($id) {
    $tfilter = new TrackingDataFilter();
    $tfilter->addConsignmentIDFilter($id);
    $tfilter->AddOrderByDate(false);
    $tList = $tfilter->getColumnList("status_code, description");
    if (count($tList) > 0) {
        $status = $tList[0]->getDescription();
    } else {
        $status = '';
    }
    return $status;
}

function getAgentEmail($handling) {
    $agent = ConsignmentFilter::getAgentId($handling);
    if (count($agent) > 0) {
        $agentid = $agent[0]->getAgentId();
        $afilter = new AgentDataFilter();
        $afilter->addFieldFilter("id", $agentid);
        $aList = $afilter->getColumnList("email,alternative1_email,alternative2_email");
        if (count($aList) > 0) {
            if ($aList[0]->getEmail() != "") {
                $agentEmail = $aList[0]->getEmail();
            } else if ($aList[0]->getAlternative1Email() != "") {
                $agentEmail = $aList[0]->getAlternative1Email();
            } else if ($aList[0]->getAlternative2Email() != "") {
                $agentEmail = $aList[0]->getAlternative2Email();
            }
        }
    } else
        $agentEmail = "cs@oneworldexpress.com";

    return $agentEmail;
}

//SHOW QUOTATION
if (isset($_POST['action']) && trim($_POST['action']) == 'ShowQuotation') {
    $quotationid = $_POST['quotationid'];
    $id = $_POST['id'];
    $QuotationDetailsFilter = new QuotationDetailsFilter();
    $QuotationDetailsFilter->addLikeIdFilter($quotationid);
    $q_list = $QuotationDetailsFilter->getColumnList('shipping_from,shipping_to,carrier,account,pieces,total_charge');
    $output = [];
    $html = "";
    if (count($q_list) > 0) {
        $html .= '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">
                        <tr>
                            <td>From</td>
                            <td>To</td>
                            <td>Carrier</td>
                            <td>Account</td>
                            <td>Pieces</td>
                            <td>Total Charges</td>
                            <td></td>
                        </tr>';
        foreach ($q_list as $quotation) {
            $html .= '<tr>
                        <td>' . $quotation->getShippingFrom() . '</td>
                        <td>' . $quotation->getShippingTo() . '</td>
                        <td>' . $quotation->getCarrier() . '</td>
                        <td>' . $quotation->getAccount() . '</td>
                        <td>' . $quotation->getPieces() . '</td>
                        <td>' . $quotation->getTotalCharge() . '</td>
                        <td><input type="button" class="btn btn-primary" id="savequote" name="savequote" value="Assign" onclick="assignQuotation(' . $id . ',' . $quotation->getId() . ')" /></td>
                     </tr>';
        }
        $html .= "</table>";
    }
    if ($html == "") {
        $html .= '<table class="table table-striped table-bordered table-advance table-hover" style="margin-top:15px;">';
        $html .= '<tr>
                        <td>No record found</td>
                    </tr>';
        $html .= '</table>';
    }
    $output['html'] = $html;
    $links = '<input type="button" class="btn btn-primary" id="cancellog" name="cancellog" value="Cancel" onclick="Cancelbutton()" />';
    $output['links'] = $links;
    echo json_encode($output);
}

//ASSIGN QUOTATION
if (isset($_POST['action']) && trim($_POST['action']) == 'AssignQuotation') {
    $id = $_POST['id'];
    $quotationid = $_POST['quotationid'];
    $QuotationDetailsFilter = new QuotationDetailsFilter();
    $QuotationDetailsFilter->addFieldFilter("id", $quotationid);
    $quotationList = $QuotationDetailsFilter->getList();
    if (count($quotationList) > 0) {

        $quotationList = $quotationList[0];
        $consignment = new ConsignmentFilter();
        $consignment->addIdFilter($id);
        $c_list = $consignment->getColumnList('hawb');

        if (count($c_list) > 0) {
            $c_list = $c_list[0];
            $hawb = $c_list->getHawb();
        }
        $InvoiceDetailFilter = new InvoiceDetailFilter();
        $InvoiceDetailFilter->addFieldFilter('consignment_id', $id);
        $InvoiceList = $InvoiceDetailFilter->getColumnList('invoice_no,quotation_id,basic_charges,fuel_charges,amount');

        if (count($InvoiceList) > 0) {
            $invoice_no = $InvoiceList[0]->getInvoiceNo();
            if ($invoice_no == "") {
                $InvoiceList[0]->setQuotationId($quotationid);
                $InvoiceList[0]->setBasicCharges($quotationList->getBasicCharge());
                $InvoiceList[0]->setFuelCharges($quotationList->getFuelCharge());
                $InvoiceList[0]->setAmount($quotationList->getTotalCharge());
                $InvoiceList[0]->save();
                echo "SUCCESS||" . $quotationid;
            } else {
                $InvoiceDetail = new InvoiceDetail();
                $InvoiceDetail->setConsignmentId($id);
                $InvoiceDetail->setHawb($hawb);
                $InvoiceDetail->setBasicCharges($quotationList->getBasicCharge());
                $InvoiceDetail->setFuelCharges($quotationList->getFuelCharge());
                $InvoiceDetail->setAmount($quotationList->getTotalCharge());
                $InvoiceDetail->setDateCreated(date("Y-m-d H:i:s"));
                $InvoiceDetail->setQuotationId($quotationid);
                $InvoiceDetail->save();
                echo "SUCCESS||" . $quotationid;
            }
        } else {
            $InvoiceDetail = new InvoiceDetail();
            $InvoiceDetail->setConsignmentId($id);
            $InvoiceDetail->setHawb($hawb);
            $InvoiceDetail->setBasicCharges($quotationList->getBasicCharge());
            $InvoiceDetail->setFuelCharges($quotationList->getFuelCharge());
            $InvoiceDetail->setAmount($quotationList->getTotalCharge());
            $InvoiceDetail->setDateCreated(date("Y-m-d H:i:s"));
            $InvoiceDetail->setQuotationId($quotationid);
            $InvoiceDetail->save();
            echo "SUCCESS||" . $quotationid;
        }
        $ConsignmentLog = new ConsignmentLog();
        $ConsignmentLog->createlog("Quotation Reference Added. ", $id);
        return;
    } else {
        echo "ERROR||Incorrect Quotation Refrenece Number. Please check it.";
        return;
    }
}
if (isset($_POST['action']) && trim($_POST['action']) == 'PRICING_DETAILS') {
    $consignmentid = $_POST['id'];
    $accountId = $_POST['accountId'];
    $consignmentChargesFilter = new ConsignmentChargesFilter();
    $consignmentChargesFilter->addConsignmentChargesTypeJoin();
    $consignmentChargesFilter->addFieldFilter("    cc.consignment_id", $consignmentid);
    $consignmentChargesFilter->addFilter("    cc.charge_type_id <> 28" );
    $consignmentChargesFilter->addFieldFilter("    cc.account_id", $accountId);
    $consignmentChargesFilterObj = $consignmentChargesFilter->getList("cc.*,cct.is_extra_charge,cct.charges_key");
    $consignmentChargesLogFilter = new ConsignmentChargesLogFilter();
    $consignmentChargesLogFilter->addFieldFilter('     ccl.log_id', $consignmentid);
    $consignmentChargesLogFilterObjs = $consignmentChargesLogFilter->getLogList();
    $invoice_detail_id = 0;
    $consignment_id = "";
    $invoice_no = "";
    $basic_charges = "";
    $fuel_charges = "";
    $additional_charges = "";
    $remote_area_charge = "";
    $on_farword_charges = "";
    $ndx = "";
    $ddp = "";
    $hv = "";
    $discount = "";
    $ancillary_charges = "";
    $ancillary_charges_details = "";
    $amount = "";
    $agent_basic_charges = "";
    $agent_fuel_charges = "";
    $agent_additional_charges = "";
    $agent_remote_area_charge = "";
    $agent_on_farword_charges = "";
    $agent_ndx = "";
    $agent_ddp = "";
    $agent_linehaul_cost = "";
    $agent_handling_charges = "";
    $agent_amount = "";
    $extra = "";
    $reference = "";
    $agent_extra = "";
    $added_by = "";
    $output = array();
    $tarffName = "";
    $tarffId = "";
   
    if (count($consignmentChargesFilterObj) > 0) {
        if (count($consignmentChargesLogFilterObjs) > 0) {
            $consignmentChargesLogFilterObj = $consignmentChargesLogFilterObjs[0];
            $output['added_by'] = $consignmentChargesLogFilterObj->getUserid();
        } else {
            $account = new CustomerAccount($consignmentChargesFilterObj[0]->getUpdatedBy());
            $output['added_by'] = $account->getUserAccount();
        }
        $purchaseReference = [];
        foreach ($consignmentChargesFilterObj as $key => $consignmentCharges) {
            $consignmentChargesType = new ConsignmentChargesTypes($consignmentCharges->getChargeTypeId());
            if ($key == 0) {
                $output['invoice_id'] = $consignmentCharges->getInvoiceId();
                $invoiceObj = new Invoices($consignmentCharges->getInvoiceId());
                $output['invoice_number'] = $invoiceObj->getInvoiceNo();
                $output['consignment_id'] = $consignmentCharges->getConsignmentId();
            }
            if ($consignmentCharges->getChargesKey() == "BASIC_CHARGES") {
                $tarffId = $consignmentCharges->getTariffId();
                if ($tarffId > 0 || $tarffId != "") {
                    $tariffObj = new Tariffs($tarffId);
                    $tarffName = $tariffObj->getName();
                }
            }
            $output['tariff_id'] = $tarffId;
            $output['tarff_name'] = $tarffName;
            if (($consignmentCharges->getCostType() == "customer") && ($consignmentCharges->getAgentId() == 0)) {
                $output['customer_' . preg_replace('/[\s^\/]+/', '_', strtolower($consignmentChargesType->getTitle()))] = $consignmentCharges->getCost();
            }
            if (($consignmentCharges->getCostType() == "customer") && ($consignmentCharges->getIsExtraCharge() == 1)) {
                $output['customer_extra'] += $consignmentCharges->getCost();
            }
            if (($consignmentCharges->getCostType() == "agent") && ($consignmentCharges->getAgentId() == 0)) {
                $output["agent_" . preg_replace('/\s+/', '_', strtolower($consignmentChargesType->getTitle()))] = $consignmentCharges->getCost();
            }
            if (($consignmentCharges->getCostType() == "agent") && ($consignmentCharges->getIsExtraCharge() == 1)) {
                $output["agent_extra"] += $consignmentCharges->getCost();
            }
            if (($consignmentCharges->getCostType() == "purchase_invoice") && ($consignmentCharges->getAgentId() == 0)) {
                $output["purchase_invoice_" . preg_replace('/\s+/', '_', strtolower($consignmentChargesType->getTitle()))] = $consignmentCharges->getCost();
            }
            if (($consignmentCharges->getCostType() == "purchase_invoice") && ($consignmentCharges->getIsExtraCharge() == 1)) {
                $output["purchase_invoice_extra"] += $consignmentCharges->getCost();
            }
           
            if(trim($consignmentCharges->getChangesReference())!= '' && !in_array(trim($consignmentCharges->getChangesReference()),$purchaseReference))
            {
                
                $purchaseReference[] =trim($consignmentCharges->getChangesReference());
            }
        }
        if (!empty($purchaseReference)) {
                $output["purchase_invoice_reference"] = implode(", ", $purchaseReference);
            }
    } 
    echo json_encode($output);
    exit;
}

if (isset($_POST['action']) && trim($_POST['action']) == 'UPDATE_PRICING_DETAILS') {
    /* For Charges Log */
    $consignmentId = $_POST['consignment_id'];
    $consignmentIds[] = $consignmentId;
    ConsignmentCharges::updateChargesByConsignmentIds($consignmentIds, $_POST, true);
    exit;
}
if (isset($_POST['action']) && trim($_POST['action']) == 'EDIT_POD') {
    $tracking_id = $_POST['tracking_id'];

    $TrackingDataFilter = new TrackingDataFilter();
    $TrackingDataFilter->addIdFilter($tracking_id);
    $TrackingDataFilter->addPodDateFilter();
    $TrackingDataFilter->AddOrderByID(false);
    $getlist = $TrackingDataFilter->getList();
    //$services->addCarrierFilter($carrierName);
    if (count($getlist) > 0) {

        $listData = $getlist[0];
        $trackingArray = array(
            'tracking_id' => $listData->getId(),
            'consignmentid' => $listData->getConsignmentId(),
            'tracking_number' => $listData->getTrackingNumber(),
            'pod_status' => $listData->getStatusCode(),
            'TrackPoint' => $listData->getTrackPoint(),
            'Description' => $listData->getDescription(),
            'DateCreated' => $listData->getDateCreated(),
            'Account' => $listData->getAccount(),
            'Signature' => $listData->getSignature(),
            'PodDate' => date("Y-m-d H:i", $listData->getPodDate()));
    }

    echo json_encode($trackingArray);
    exit;
}

function getLastPodTracking($consignment){
    $consignmentId = $consignment->getId();
    $parcels = $consignment->getParcels();
    $parcelid = $parcels[0]->getId();
    $parcelNo = $parcels[0]->getTrackingNumber();    
    $trackingDataFilter =  new TrackingDataFilter();
    $trackingDataFilter->addFilter("   entity_id in ('".$consignmentId."', '".$parcelid."') and tracking_number in ('".$consignment->getAwb()."', '".$parcelNo."')");
    $trackingDataFilter->AddOrderByDate(false);
    $trackinglist = $trackingDataFilter->getColumnList("id, entity_id, tracking_number, date_created, track_point, carrier_desc","", "1");
    if(count($trackinglist) > 0){
        return $trackinglist[0];
    }
}
?>