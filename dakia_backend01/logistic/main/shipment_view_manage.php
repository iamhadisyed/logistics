<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
        ], '3rdparty/tcpdf');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'consignmentbillinghold.class',
    'consignmentbillingholdfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'invoicedetail.class',
    'invoicedetailfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'services.class',
    'servicefilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'consignmentdetails.class',
    'consignmentdetailsfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'consignmentchargeslog.class',
    'consignmentchargeslogfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'quotationdetails.class',
    'quotationdetailsfilter.class',
    'tracking.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentrelabel.class',
    'consignmentrelabelfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class'
    
    
]);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $id = 0;
    private $countryObj = array();
    private $consignmentObj = array();
    private $userObj = array();
    private $userAccountObj = array();
    private $servicename = '';
    private $serviceType = '';
    private $carrier = '';
    private $quotationId = '';
    private $consignment_status = '';
    private $agentList = array();
    private $agentid = 0;
    private $accountIdPricing = 0;
    private $parcelList = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Consignments Details"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */

        if (isset($_GET['action']) && $_GET['action'] == "consignment_list") {

            die;
        }

        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            $this->id = trim($_GET['id']);
            $this->accountIdPricing = Consignment::getConsignmentUserAccountIdForPricing($this->id);

            $this->consignmentObj = new Consignment($this->id);
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter("consignment_id", $this->consignmentObj->getId());
            $this->parcelList = $parcelFilter->getList();

//            $this->parcelList

            $this->userObj = new User($this->consignmentObj->getUserId());
            $this->userAccountObj = new CustomerAccount($this->userObj->getUserAccountId());
            if (Permissions::checkFilePermission('hide_subaccount')) {
                $accountObj = new CustomerAccount();
                $searchConsignmentAccountId = 0;
                $accountSubAccountArr = $accountObj->getSubAccountsArrayShowConsignmentAccount($this->user->getUserAccountId(),$searchConsignmentAccountId);
                $consignmentAccountId = $this->userObj->getUserAccountId();
                $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$accountSubAccountArr,$searchConsignmentAccountId,true);
                $this->userAccountObj = new CustomerAccount($consignmentAccount);
            }
            $this->countryObj = new Country($this->consignmentObj->getCountryId());

            $this->agentid = $this->consignmentObj->getAgentid();
            $AgentData = new AgentDataFilter();
            $AgentData->addFieldFilter("id", $this->agentid);
            $this->agentList = $AgentData->getList();
            if (count($this->agentList) > 0) {
                $this->agentList = $this->agentList[0];
            }
        }
        //get Audit Log
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_audit_log') {
            $output = "";
            $consignment_id = $this->form_vars['consignment_id'];
            $ConsignmentLogFilter = new ConsignmentLogFilter();
            $consignment_log = $ConsignmentLogFilter->getAuditLog($consignment_id);
            if (count($consignment_log) > 0) {
                foreach ($consignment_log as $clog) {
                    $output .= '<tr>';
                    $output .= '<td>' . $clog->getMessage() . '</td>';
                    $output .= '<td>' . strtoupper($clog->getIpaddress()) . '</td>';
                    $output .= '<td>' . formatDateTime(date("d-m-Y H:i:s", $clog->getLogDate())). '</td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="4">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'GET_USER_DETAILS') {
            $accountDetails = array();
            $accountId = $_POST['account_id'];
            $userdata = new CustomerAccount($accountId);
            if (count($userdata) > 0) {
                $accountDetails['fullname'] = $userdata->getFullName();
                $accountDetails['useraccount'] = $userdata->getUserAccount();
                $accountDetails['company'] = $userdata->getCompany();
                $accountDetails['returnaddress'] = $userdata->getReturnAddress();
                $accountDetails['email'] = $userdata->getEmail();
                $accountDetails['telephone'] = $userdata->getPhone();
                $accountDetails['logo'] = $userdata->getLogo();
                $accountDetails['billingaddress'] = $userdata->getBillingAddress();
                $accountDetails['alternativeemail'] = $userdata->getAlternativeEmail();
                $accountDetails['country'] = $userdata->getCountry();
            }
            echo json_encode($accountDetails);
            exit;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'UPDATE_REMOTE') {
            $responceArray = array();
            $consignmentId = $this->form_vars['id'];
            $remotearea = $this->form_vars['remotearea'];

            $consignmentCon = new Consignment($consignmentId);
            if (count($consignmentCon) > 0) {

                $userObj = new User($consignmentCon->getUserId());
                $userAccountObj = new CustomerAccount($userObj->getUserAccountId());

                $consignmentCon->setRemoteCharges($remotearea);
                $oldData = serialize(ConsignmentCharges::getDataByConsignmentId($consignmentId));
                $newData = serialize([]);
                $checkInvoiced = Consignment::checkConsignmentInvoiced($consignmentId);
                if ($checkInvoiced > 0) {
                    if (trim($remotearea) == '1') {
                        $consignmentCon->savelog($userAccountObj->getFullname() . ' tried to change shipment as remote area, but its invoiced so not changed.', '');
                    } else {
                        $consignmentCon->savelog($userAccountObj->getFullname() . ' tried to change shipment as non remote area, but its invoiced so not changed.', '');
                    }
                    $responceArray['STATUS'] = 'ERROR';
                    $responceArray['MESSAGE'] = 'This shipment is invoiced, you can not change it.';
                } else {
                    if (trim($remotearea) == '1') {
                        $serviceObj = new Services($consignmentCon->getServiceId());
                        $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                        $remoteareasGroupsFilter->addFieldFilter("carrier_id", $serviceObj->getCarrierId());
                        $remoteareasGroupsFilterObj = $remoteareasGroupsFilter->getColumnList("group_name");
                        $remoteareaGroupIds = [];
                        if (count($remoteareasGroupsFilterObj) > 0) {
                            foreach ($remoteareasGroupsFilterObj as $remoteareaGroup) {
                                $remoteareaGroupIds[] = $remoteareaGroup->getId();
                            }
                        }
                        $remoteareaFilter = new RemoteareasFilter();
                        $remoteareaFilter->addFieldFilter("     ra.country_id", $consignmentCon->getCountryId());
                        if (!empty($consignmentCon->getCity())) {
                            $remoteareaFilter->addFieldLikeFilter("     ra.city", $consignmentCon->getCity());
                        }
                        if (is_numeric($consignmentCon->getPostcode())) {
                            $remoteareaFilter->addFilter("     ((ra.from_postcode >= " . $consignmentCon->getPostcode() . ") AND ( ra.to_postcode <= " . $consignmentCon->getPostcode() . ")) ");
                        } else {
                            $remoteareaFilter->addFieldFilter("    ra.to_postcode", $consignmentCon->getPostcode());
                        }
                        $remoteareaFilter->addFilterIn("     ra.remoteareas_groups_id", $remoteareaGroupIds);
                        $remoteareaFilterObj = $remoteareaFilter->getColumnList("remoteareas_groups_id");
                        $remoteareaCharges = 0;
                        $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
                        if (count($remoteareaFilterObj) > 0) {
                            $remoteareaGroupId = 0;
                            foreach ($remoteareaFilterObj as $remotearea) {
                                $remoteareaGroupId = $remotearea->getRemoteareasGroupsId();
                            }
                            $carrierObj = new Carrier($serviceObj->getCarrierId());
                            if ($carrierObj->getRemoteareaCheck() == "c") {
                                $remoteareaChargesCarrierUserFilter = new RemoteareaChargesCarrierUserFilter();
                                $remoteareaChargesCarrierUserFilter->addFieldFilter("     remotearea_group_id", $remoteareaGroupId);
                                $remoteareaChargesCarrierUserFilter->addFieldFilter("     user_account_id", $userAccountId);
                                $remoteareaChargesCarrierUserFilterObj = $remoteareaChargesCarrierUserFilter->getColumnList("remotearea_charges");
                                if (count($remoteareaChargesCarrierUserFilterObj) > 0) {
                                    foreach ($remoteareaChargesCarrierUserFilterObj as $remoteareaChargesCarrierUser) {
                                        $remoteareaCharges = $remoteareaChargesCarrierUser->getRemoteareaCharges();
                                    }
                                } else {
                                    $remoteareaChargesCarrierFilter = new RemoteareaChargesCarrierFilter();
                                    $remoteareaChargesCarrierFilter->addFieldFilter("     remotearea_group_id", $remoteareaGroupId);
                                    $remoteareaChargesCarrierFilterObj = $remoteareaChargesCarrierFilter->getColumnList("remotearea_charges");
                                    if (count($remoteareaChargesCarrierFilterObj) > 0) {
                                        foreach ($remoteareaChargesCarrierFilterObj as $remoteareaChargesCarrier) {
                                            $remoteareaCharges = $remoteareaChargesCarrier->getRemoteareaCharges();
                                        }
                                    }
                                }
                            } else {
                                $remoteareaChargesServicesUserFilter = new RemoteareaChargesServicesUserFilter();
                                $remoteareaChargesServicesUserFilter->addFieldFilter("     remotearea_group_id", $remoteareaGroupId);
                                $remoteareaChargesServicesUserFilter->addFieldFilter("     user_account_id", $userAccountId);
                                $remoteareaChargesServicesUserFilter->addFieldFilter("     service_id", $consignmentCon->getServiceId());
                                $remoteareaChargesServicesUserFilter->addFilter("      ((from_weight >= '" . $consignmentCon->getWeight() . "') AND (to_weight <= '" . $consignmentCon->getWeight() . "')) ");
                                $remoteareaChargesServicesUserFilterObj = $remoteareaChargesServicesUserFilter->getColumnList("remotearea_charges");
                                if (count($remoteareaChargesServicesUserFilterObj) > 0) {
                                    foreach ($remoteareaChargesServicesUserFilterObj as $remoteareaChargesServicesUser) {
                                        $remoteareaCharges = $remoteareaChargesServicesUser->getRemoteareaCharges();
                                    }
                                } else {
                                    $remoteareaChargesServicesFilter = new RemoteareaChargesServicesFilter();
                                    $remoteareaChargesServicesFilter->addFieldFilter("     remotearea_group_id", $remoteareaGroupId);
                                    $remoteareaChargesServicesFilter->addFieldFilter("     service_id", $consignmentCon->getServiceId());
                                    $remoteareaChargesServicesFilter->addFilter("      ((from_weight >= '" . $consignmentCon->getWeight() . "') AND (to_weight <= '" . $consignmentCon->getWeight() . "')) ");
                                    $remoteareaChargesServicesFilterObj = $remoteareaChargesServicesFilter->getColumnList("remotearea_charges");
                                    if (count($remoteareaChargesServicesFilterObj) > 0) {
                                        foreach ($remoteareaChargesServicesFilterObj as $remoteareaChargesServices) {
                                            $remoteareaCharges = $remoteareaChargesServices->getRemoteareaCharges();
                                        }
                                    }
                                }
                            }
                        }
                        $consignmentCharges = new ConsignmentCharges();
                        $consignmentCharges->setAccountId($userAccountId);
                        $consignmentCharges->setConsignmentId($consignmentId);
                        $consignmentCharges->setChargeTypeId(8);
                        $consignmentCharges->setCostType("customer");
                        $consignmentCharges->setCost($remoteareaCharges);
                        $consignmentCharges->setDescription("Remote Area Charges");
                        $consignmentCharges->save();

                        $consignment = new Consignment($consignmentId);
                        $consignment->setRemoteCharges(1);
                        $consignment->save();

                        $newData = serialize(ConsignmentCharges::getDataByConsignmentId($consignmentId));

                        $consignmentCon->savelog($userAccountObj->getFullname() . ' has changed shipment as remote area', '');
                        $responceArray['STATUS'] = 'SUCCESS';
                        $responceArray['MESSAGE'] = 'Your shipment has been successfully changed';
                    } else {
                        ConsignmentCharges::deleteRemoteareaCharges($consignmentId, $userAccountId);
                        $newData = serialize(ConsignmentCharges::getDataByConsignmentId($consignmentId));
                        $consignment = new Consignment($consignmentId);
                        $consignment->setRemoteCharges(0);
                        $consignment->save();

                        $consignmentCon->savelog($userAccountObj->getFullname() . ' has changed shipment as non remote area', '');
                        $responceArray['STATUS'] = 'SUCCESS';
                        $responceArray['MESSAGE'] = 'Your shipment has been successfully changed';
                    }
                    //End Set Consigment Charges Log
                    $ConsignmentChargesLog = new ConsignmentChargesLog();
                    $ConsignmentChargesLog->createlog("Remotearea pricing Updated ", $consignmentId, '', $this->user->getId(), $oldData, $newData);
                }
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'UPDATE_AGENT') {
            $responceArray = array();
            $consignmentId = $this->form_vars['id'];
            $agentcode = $this->form_vars['agentcode'];
            $consignmentCon = new Consignment($consignmentId);
            if (count($consignmentCon) > 0) {
                $userObj = new User($consignmentCon->getUserId());
                $consignmentCon->setAgentId($agentcode);
                $consignmentCon->savelog($userObj->getFullname() . ' has changed shipment agent Code to' . $agentcode, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Agent has been successfully changed';

                $agentData = new AgentData($agentcode);
                $responceArray['new_Agent'] = $agentData->getAgentName() . " " . $agentData->getAgentCode();
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }

            echo json_encode($responceArray);
            die;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'update_pod_status') {
            $emailPODStatus = "";
            $msg = "Status Updated successfully.";

            $tracking_id = $this->form_vars["tracking_id"];
            $consignment_id = $this->form_vars["consignment_id"];
            $tracking_number = $this->form_vars["tracking_number"];
            $pod_status = $this->form_vars["pod_status"];
            $pod_name = $this->form_vars["pod_name"];
            $pod_date = $this->form_vars["pod_date"];
            $pod_description = $this->form_vars["pod_description"];
            $send_pod_status_mail = $this->form_vars["send_pod_status_mail"];

            $pod_status = (($pod_status == '16') ? '18' : $pod_status);
            
            if ($tracking_id != '') {
                //$consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
                //$statusCodeId = array_search($pod_status , $consignmentStatusCodes);
                 $statusCodeId = $pod_status;
                $TrackingDataFilter = new TrackingDataFilter();
                $TrackingDataFilter->addIdFilter($tracking_id);
                $t_list = $TrackingDataFilter->getList();
                if (count($t_list) > 0) {
                    $trackingDataObj = $t_list[0];
                } else {
                    $trackingDataObj = new TrackingData();
                }
            } else {
                $trackingDataObj = new TrackingData();
            }

            $consignmentObj = new Consignment($consignment_id);

            $parcelsFilter = new ParcelFilter();
            $parcelsFilter->addConsignmentIdFilter($consignment_id);
            $parcels = $parcelsFilter->getList();
            foreach ($parcels as $parcelObj) {
                $trackingDataObj->setEntityId($parcelObj->getId());
                $trackingDataObj->setTrackingNumber($tracking_number);
                $trackingDataObj->setStatusCodeId($statusCodeId);
                $trackingDataObj->setTrackPoint($pod_description);
                $trackingDataObj->setCarrierDesc(Tracking::$oneworld_status_desc[$statusCodeId]);
                $trackingDataObj->setDateAdded(date("Y-m-d h:i:s"));
                $ip = getClientIp();
                $trackingDataObj->setIpAddress($ip);
                $trackingDataObj->setEntityType("parcel");
                $trackingDataObj->setUserId($consignmentObj->getUserId());
                if ($pod_date == "") {
                    $pod_date = date("Y-m-d H:i:s");
                }
                $trackingDataObj->setDateCreated(date("Y-m-d H:i:s", strtotime($pod_date)));
                if ($pod_status == '19') {
                    $emailPODStatus = 'Delivered';
                    $trackingDataObj->setSignatory($pod_name);
                    $imageName = "";
                    if (isset($_FILES['pod_file']) && !empty($_FILES['pod_file']['name'])) {
                        $sourcePath = $_FILES['pod_file']['tmp_name'];
                        $imageName = $consignment_id . "_" . $_FILES['pod_file']['name'];
                        $path = "../_assets/pod_images/";
                        $targetPath = $path . $imageName;
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        if (move_uploaded_file($sourcePath, $targetPath)) {
                            $trackingDataObj->setPodImage($imageName);
                        } else {
                            $msg .= "POD image uploading fails.";
                        }
                    }
                    $consignmentSave = new Consignment($consignment_id);
                    $consignmentSave->setStatus($pod_status);
                    $consignmentSave->save();
                }
                $trackingDataObj->save();
            }

            $parcelFilterdata = new ParcelFilter();
            $parcelFilterdata->addConsignmentIdFilterNew($consignment_id);
            $parcelList = $parcelFilterdata->getList();
            if (count($parcelList) > 0) {
                foreach ($parcelList as $plist) {
                    $plist->setCourierStatus($pod_status);
                    $plist->save();
                }
            }
            
            $countryObj = new Country($consignmentObj->getCountryId());

            if ($send_pod_status_mail == 1) {
                //send email to customer                            
                $emialContent = 'Dear Customer,<br /><br />Your shipment status details<br /><br />';
                $emialContent .= $pod_description . '<br /><br />';
                $emialContent .= '<table border="0" cellspacing="1" cellpadding="0" width="850" style="width:637.5pt">
                                        <tbody>
                                            <tr>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Hawb No</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Date of Hawb</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Your Ref No .</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Destination</span></b></p>
                                                </td>
                                                <td width="50" valign="top" style="width:37.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Pieces</span></b></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Customer Specified Weight (Kg)</span></b></p>
                                                </td>
                                                <td width="110" valign="top" style="width:82.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Description</span></b></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Value</span></b></p>
                                                </td>
                                                <td width="150" valign="top" style="width:112.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Pod Status</span></b></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="9" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <div class="MsoNormal" align="center" style="text-align:center">
                                                        <hr size="2" width="100%" align="center">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getAwb() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getDateBooked() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getReference() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal">
                                                        <span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">
                                                            ' . $consignmentObj->getAddressLine1() . ($consignmentObj->getAddressLine2() != '' ? '<br />' . $consignmentObj->getAddressLine2() : '') . ($consignmentObj->getAddressLine3() != '' ? '<br />' . $consignmentObj->getAddressLine3() : '') . '<br />
                                                            ' . $consignmentObj->getCity() . '<br />' . $consignmentObj->getPostcode() . ' ' . $countryObj->getName() . '
                                                        </span>
                                                    </p>
                                                </td>
                                                <td width="50" valign="top" style="width:37.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getNumberPieces() . '</span></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getWeight() . '</span></p>
                                                </td>
                                                <td width="110" valign="top" style="width:82.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getDescription() . '</span></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getCurrency() . ' ' . $consignmentObj->getValue() . '</span></p>
                                                </td>
                                                <td width="150" valign="top" style="width:112.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $emailPODStatus . '</span></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>';
                $emialContent .= '<br /><br />Thanks and regards,<br />One World Express';
                $email = $consignmentObj->getEmail();
                if ($email != "") {
                    $userObj = new User($consignmentObj->getUserId());
                    $email = $userObj->getEmail();
                }
                if ($email != "") {
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: One World Express <cs@oneworldexpress.com>' . "\r\n";
                    mail($email, "One World Shipping Status for HAWB No " . $consignmentObj->getHawb(), $emialContent, $headers);
                }
            }
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Update Status to " . $pod_status, $consignment_id, "CONSIGNMENT", $this->user->getId(), serialize($consignmentObj), $pod_status );
            echo $msg;
            exit;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'UPDATE_UPDATE_ACCOUNT') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            $newAccount = $_POST['newAccount'];
            $oldAccount = $_POST['oldAccount'];
            $newUser = $_POST['newUser'];
            if (trim($newAccount) == '' || $newUser == '') {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Please select New Account and New User.';
            } else {
                $userAccountObj = new CustomerAccount($newAccount);
                if (count($userAccountObj) > 0) {
                    $userFilter = new UserFilter();
                    $userFilter->addAccountIdFilter($newAccount);
                    $userFilterObj = $userFilter->getColumnList('user_name,first_name,last_name');
                    $consignmentCon = new Consignment($consignmentId);
                    if (count($consignmentCon) > 0) {
                        $consignmentCon->setUserId($newUser);
                        $consignmentCon->save();

                        $responceArray['STATUS'] = 'SUCCESS';
                        $responceArray['MESSAGE'] = 'Your account has been successfully changed';
                    } else {
                        $responceArray['STATUS'] = 'ERROR';
                        $responceArray['MESSAGE'] = 'Your shipment is not found.';
                    }
                } else {
                    $responceArray['STATUS'] = 'ERROR';
                    $responceArray['MESSAGE'] = 'System does not find account number related to user account $newAccount. Please Contact to IT.';
                }
            }
            echo json_encode($responceArray);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'UPDATE_TRACKING') {
            $responceArray = array();
            $consignmentId = $this->form_vars['id'];
            $tracking = $this->form_vars['tracking_new'];
            $consignmentCon = new Consignment($consignmentId);
            $user = new User($consignmentCon->getUserId());
            $userAccount = new CustomerAccount($user->getUserAccountId());
            if (count($consignmentCon) > 0) {
                $old_awb = $consignmentCon->getAwb();
                $consignmentCon->setAwb($tracking);

                $parcels = $consignmentCon->getParcels();
                print_r($parcels);
                if (count($parcels) > 0) {
                    foreach ($parcels as $parcelsData) {
                        $oldparcelTrackingNo = $parcelsData->getTrackingNumber();
                        if (trim($parcelsData->getTrackingNumber()) == trim($old_awb)) {
                            $parcelsData->setTrackingNumber($tracking);
                            $parcelsData->save();
                        }
                    }
                }
                $consignmentRelabel = new ConsignmentRelabel();
                $consignmentRelabel->setConsignmentId($consignmentCon->getId());
                $consignmentRelabel->setOldTrackingNo($old_awb);
                $consignmentRelabel->setNewTrackingNo($tracking);
                $consignmentRelabel->setOldParcelTrackingNo($oldparcelTrackingNo);
                $consignmentRelabel->setUserId($this->user->getId());
                $consignmentRelabel->setDateCreated(time());
                $consignmentRelabel->save();
                $consignmentCon->savelog($userAccount->getFullname() . ' has changed shipment AWB from ' . $old_awb . ' to ' . $tracking, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Tracking Number has been successfully changed';
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }
        //get Price Audit Log
        if (isset($_POST['func']) && $_POST['func'] == 'get_price_audit_log') {
            $output = "";
            $consignment_id = $_POST['consignment_id'];
            $consignmentChargesLogFilter = new ConsignmentChargesLogFilter();
            $consignmentChargesLogFilter->addFieldFilter("   log_id", $consignment_id);
            $consignmentChargesLogFilterObj = $consignmentChargesLogFilter->getLogList();
            if (count($consignmentChargesLogFilterObj) > 0) {
                foreach ($consignmentChargesLogFilterObj as $log) {
                    $output .= '<tr>';
                    $output .= '<td>' . $log->getMessage() . '</td>';
                    $output .= '<td>' . $log->getUserid() . '<br /><i>' . $log->getUserName() . '</i></td>';
                    $output .= '<td>' . $log->getIpaddress() . '</td>';
                    $output .= '<td>' . date("d-m-Y H:i:s", $log->getLogDate()) . '</td>';
                    $output .= '<td class="details_view" style="cursor: pointer;" data-log-id="' . $log->getId() . '">View Detail</td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="4">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }
        //get Price Audit Log Detail
        if (isset($_POST['func']) && $_POST['func'] == 'get_price_audit_log_detail') {
            $log_id = "";
            $log_id = $_POST['log_id'];
            $consignmentChargesLog = new ConsignmentChargesLog($log_id);
            $previous_data = unserialize($consignmentChargesLog->getPreviousData());
            $current_data = unserialize($consignmentChargesLog->getCurrentData());
            $output = "";
            $output .= '<table class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Column Name</th>
                                <th>Previous value</th>
                                <th>to</th>
                                <th>New Value</th>
                                <th>Type</th>
                            </tr>
                        </thead>';
            $output .= $this->makeHtml($previous_data, $current_data);
            $output .= '</table>';
            echo $output;
            exit();
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_user_from_account') {
            $accountId = $this->form_vars['account_id'];
            $consignment_id = $this->form_vars['consignment_id'];
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter('user_account_id', $accountId);
            $userFilterObj = $userFilter->getColumnList("user_name,first_name,last_name");
            $option = '<option value=""> Select User </option>';
            $this->consignmentObj = new Consignment($consignment_id);
            $userId = $this->consignmentObj->getUserId();
            foreach ($userFilterObj as $obj) {
                $selected = "";
                if ($userId == $obj->getId()) {
                    $selected = "selected='selected'";
                }
                $option .= '<option value="' . $obj->getId() . '" ' . $selected . '  > ' . $obj->getFirstName() . ' ' . $obj->getLastName() . '</option>';
            }
            echo $option;
            die;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_PRICING_DETAILS_CALCULATION') {
			
			
            $consignmentId = $_POST['consignment_id'];
            $checkInvoiced = 0;//Consignment::checkConsignmentInvoiced($consignmentId);
            if ($checkInvoiced == 0) {
            $host = SETTING_DB_SERVER;
            $user = SETTING_DB_USER;
            $password = SETTING_DB_PASSWORD;
            $db = SETTING_DB_DATABASE;

            $consignmentObj = new Consignment($consignmentId);
            $userObj = new User($consignmentObj->getUserId());
            $userAccountObj = new CustomerAccount($userObj->getUserAccountId());

            $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);

            $countryId = $consignmentObj->getCountryId();
		   
            $origincountryId = $userAccountObj->getCountryId();
            $serviceId = $consignmentObj->getServiceId();
            if($consignmentObj->getIsDeadWeightChargable()>0)
            {
                $weight = $consignmentObj->getWeight();
            }
            else
            {
                $weight = ($consignmentObj->getWeight()>$consignmentObj->getVolWeight()?$consignmentObj->getWeight():$consignmentObj->getVolWeight());
            }
            
            $numberPieces = $consignmentObj->getNumberPieces();
            $postcode = $consignmentObj->getPostcode();
            $status = '';

            $isCheck = UserServicesRoutingFilter::isOwnUserContract($serviceId, $userAccountId);
                        $parameter = 0;
                        if ($isCheck) {
                            $parameter = 1;
                        }
            
            $mysqli = new mysqli($host, $user, $password, $db);
            if ($mysqli->connect_errno) {
                $message['status'] = 'error';
                $message['message'] = "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            }
            //echo "CALL tariff($consignmentId,'customer', '" . $userAccountId . "', $serviceId, '" . $origincountryId . "', $countryId, $weight,$numberPieces , '" . $postcode . "', 'GBP', @S_STATUS  , @S_MESSAGE)";
           $sql = "CALL tariff($consignmentId,'customer', '" . $userAccountId . "', '" . $parameter . "',".$this->user ->getId().",@S_STATUS,@S_MESSAGE)";
            
            if (!($res = $mysqli->query($sql))) {
                $message['status'] = 'error';
                $message['message'] = "ERROR||CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            $mysqli->close();
            /* update account balance */
            CustomerAccount::updateBalance($userAccountId);
            /******************************/
            } else {
                $message['status'] = 'error';
                $message['message'] = "ERROR||Shipment cannot be priced. Its has been invoiced";
   
            }
            die;
        }
        
         /*
         * Handle Upload invoice documents
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_user_doc') {
            $html = "";
            
            $consignment_id = $this->form_vars["consignment_id"];
            $path = "../_assets/paperless_invoice/";
            if (!file_exists($path))
                @mkdir($path, 0775, true);
            if (isset($_FILES["user_doc_file"]) && trim($_FILES["user_doc_file"]["name"]) != '') {
                $allowedExts = array("pdf");
                $temp = explode(".", $_FILES["user_doc_file"]["name"]);
                
                $extension = end($temp);
                if ((($_FILES["user_doc_file"]["type"] == "application/pdf" )) && in_array($extension, $allowedExts)) {
                    if ($_FILES["user_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["user_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadName = md5($consignment_id) . "." . $extension;
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["user_doc_file"]["name"]);
                        $documentName = explode(".", $uploadName);
                        $docName = $documentName[0];
                        move_uploaded_file($_FILES["user_doc_file"]["tmp_name"], $path . $uploadName);
                        $fileFullPath = $path . $uploadName;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        
                        $html .= '<div class="col-md-12">';
                        $html .= '<div class="col-md-3 doc_upload_view" id="usr_doc_' . $docName . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" style="max-width: 100%; max-height: 100px; display: block;" data-src="' . $path . $uploadName . '">';
                        $html .= '<div class="caption text-center" style="height: auto">';
                        $html .= '<a target="_blank" href="' . $path . $uploadName . '" class="btn blue btn-xs"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="' . $uploadName . '"> Remove </a>';
                        $html .= '<input type="hidden" name="temp_invoice_name" id="temp_invoice_name" value="' . $uploadName    . '" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-12" id="send_custom_email">';
                        $html .= '<div class="col-md-3 align-content-center">';
                        $html .= '<input type="button" class="btn btn-primary btn-sm" value="Send Carrier E-Mail" onclick="SendCarrierEmail('.$consignment_id.')" data-toggle="modal" data-target="#myModalvolume" data-original-title="" title="">';
                        $html .= '</div>';
                        echo $html;
                    }
                } else {
                    echo $return_msg = "0";
                }
            }

            die;
        }
        /*
         *         Handle remove Invoice Document
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_user_doc') {
            $docId = $this->form_vars['doc_id'];
            @unlink("../_assets/paperless_invoice/". $docId);
            
            echo "1";
            die;
        }
    }

    
    
    protected function getInvoiceNumber($consignmentId,$userAccountId){
        
        $invoice_number = "Not Invoiced";
        $consignmentChargesFilter = new ConsignmentChargesFilter();
        $consignmentChargesFilter->addConsignmentChargesTypeJoin();
        $consignmentChargesFilter->addFieldFilter("    cc.consignment_id", $consignmentId);
        $consignmentChargesFilter->addFilter("    cc.charge_type_id <> 28" );
        $consignmentChargesFilter->addFieldFilter("    cc.account_id", $userAccountId);
        $consignmentChargesFilterObj = $consignmentChargesFilter->getList("cc.*,cct.is_extra_charge,cct.charges_key");
        if(count($consignmentChargesFilterObj)>0){
            foreach($consignmentChargesFilterObj as $consignmentCharges){
                if(trim($consignmentCharges->getInvoiceId()) && $consignmentCharges->getInvoiceId() > 0){
                    $invoiceObj = new Invoices($consignmentCharges->getInvoiceId());
                    $invoice_number = $invoiceObj->getInvoiceNo();    
                    break;
                    
                }
                        
            }
        }
        
        return $invoice_number;
                
    
    }

    protected function check_consignment_invoiced($consignmentId) {
        $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
        $consignmentChargesFilter = new ConsignmentChargesFilter();
        $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "' AND cc.consignment_id = '" . $consignmentId . "' AND cc.cost_type = 'customer'");
        $consignmentCharges = $consignmentChargesFilter->getColumnList("cc.consignment_id,cc.charge_type_id,cc.invoice_id");
        $checkInvoiced = 0;
        if (count($consignmentCharges) > 0) {
            if ($consignmentCharges[0]->getInvoiceId() > 0 && !empty($consignmentCharges[0]->getInvoiceId())) {
                $checkInvoiced = 1;
            }
        }
        return $checkInvoiced;
    }

    protected function get_consignment_user_account_id_for_pricing($consignment_id) {
        $consignmentObj = new Consignment($consignment_id);
        $userObj = new User($consignmentObj->getUserId());
        $ShipmentParentAccount = CustomerAccount::accountParentAccount($userObj->getUserAccountId(), true);
        $ShipmentAccountPricingId = array_intersect($ShipmentParentAccount, $this->sub_account_array);
        if (empty($ShipmentAccountPricingId) || $ShipmentAccountPricingId == 0) {
            $ShipmentAccountPricingId = $ShipmentParentAccount[0];
        }
        $user_account_id = 0;
        if (is_array($ShipmentAccountPricingId)) {
            foreach ($ShipmentAccountPricingId as $valueData) {
                $user_account_id = $valueData;
            }
        } else if (is_numeric($ShipmentAccountPricingId)) {
            $user_account_id = $ShipmentAccountPricingId;
        }
        return $user_account_id;
    }

    public function makeHtml($oldData, $newData) {
        $output = '';
        if (count($oldData) > 0) {
            $oldTotal = count($oldData) - 1;
            $newTotal = count($newData) - 1;
            foreach ($oldData as $oldKey => $od) {
                foreach ($newData as $newKey => $nd) {
                    if ($oldKey == 0 && $newKey == 0) {
                        if ($od->getConsignmentId() != $nd->getConsignmentId()) {
                            $output .= '<tr><td>Consignment Id</td><td>' . $od->getConsignmentId() . '</td><td> to </td><td>' . $nd->getConsignmentId() . '</td></tr>';
                        }
                        if ($od->getInvoiceId() != $nd->getInvoiceId()) {
                            $output .= '<tr><td>Invoice No</td><td>' . $od->getInvoiceId() . '</td><td> to </td><td>' . $nd->getInvoiceId() . '</td></tr>';
                        }
                    }
                    if ($od->getChargeTypeId() == $nd->getChargeTypeId()) {
                        if ($od->getCost() != $nd->getCost()) {
                            $consignemntChargesTypeObj = new ConsignmentChargesTypes($nd->getChargeTypeId());
                            $output .= '<tr><td>' . $consignemntChargesTypeObj->getTitle() . '</td><td>' . $od->getCost() . '</td><td> to </td><td>' . $nd->getCost() . '</td><td>' . $nd->getCostType() . '</td></tr>';
                        }
                    }
                    if (($oldKey == $oldTotal) && ($newKey == $newTotal)) {
                        if ($od->getAddedDate() != $nd->getAddedDate()) {
                            $output .= '<tr><td>Date Created</td><td>' . date('d-m-Y H:i:s', $od->getAddedDate()) . '</td><td> to </td><td colspan="2">' . date('d-m-Y H:i:s', $nd->getAddedDate()) . '</td></tr>';
                        }
                        if ($od->getAddedBy() != $nd->getAddedBy()) {
                            $oldUserObj = new User($od->getAddedBy());
                            $NewUserObj = new User($nd->getAddedBy());
                            $output .= '<tr><td>Added By</td><td>' . $oldUserObj->getFirstName() . '</td><td> to </td><td colspan="2">' . $NewUserObj->getFirstName() . ' ' . $NewUserObj->getLastName() . '</td></tr>';
                        }
                    }
                }
            }
        } else {
            $output .= '<tr><td colspan="4">New record inserted</td></tr>';
        }
        return $output;
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>       
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                /* Custom filtering function which will search data in column four between two values */
                if ($('.date-picker').length > 0) {
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $('#pod_date').datetimepicker({format: 'dd-mm-yyyy hh:ii', use24hours: true,autoclose: true});
                $('#user_account_id').change(function () {
                    var userAccountId = $(this).val();
                    get_carriers(userAccountId);
                });
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    change_carriers(carrierId);
                });
                $(".customer_charges, .agent_charges, .purchase_invoice_charges").blur(function () {
                    updateInvoiceTotal();
                });
                $("#update_pod_status_btn").click(function () {
                    if ($.trim($('#pod_description').val()) == "") {
                        swal("Sorry!", 'Description is required.', "error");
                        $('#pod_description').focus();
                        return false;
                    }

                    var file_data = $('#pod_image').prop('files')[0];
                    var form_data = new FormData();
                    var send_pod_status_mail = $("#send_pod_status_mail").is(":checked") ? 1 : 0;
                    form_data.append('pod_file', file_data);
                    form_data.append('func', 'update_pod_status');
                    form_data.append('tracking_id', $('#tracking_id').val());
                    form_data.append('consignment_id', $('#consignment_id').val());
                    form_data.append('tracking_number', $('#tracking_number').val());
                    form_data.append('pod_status', $('#pod_status').val());
                    form_data.append('pod_name', $('#pod_name').val());
                    form_data.append('pod_date', $('#pod_date').val());
                    form_data.append('send_pod_status_mail', send_pod_status_mail);
                    form_data.append('pod_description', $('#pod_description').val());
                    $.ajax({
                        url: 'shipment_view_manage.php',
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            //                            if (response.length > 150) {
                            //                                window.location = 'login.php';
                            //                            } else {
                            $("#pod_status_msg").html(response);
                            $("#pod_status_msg").toggle();
                            $('#pod_name').val('');
                            $('#pod_date').val('');
                            var checkbox = $('#send_pod_status_mail').prop('checked', false);
                            //  $.uniform.update(checkbox);
                            $('#pod_description').val('');
                            //  loadConsignmentHistory($('#consignment_id').val());
                            //alert("me here");
                            //                            }
                        }
                    });
                });
                $(".extras").click(function () {
                    $(".extras_container").hide();
                    var extra_type = $(this).data('type');
                    $("#hidden_extra_type").val(extra_type);
                    var title = extra_type == 'agent' ? 'Agent' : 'Customer';
                    $("#" + extra_type + "_extras_container").show();
                    $("#pricing-extra").find(".modal-title").html(title + ' Extra Charges');
                    var invoice_no = $("#update_pricing_details_frm #invoice_no").val();

                    if (extra_type == 'agent') {
                        $("#pricing-extra #description").prop("disabled", false);
                        $("#pricing-extra #extra_total").prop("disabled", false);
                        $('#btn_add_extra_charge').removeAttr("disabled", "disabled");
                    } else if (invoice_no != "") {
                        $("#pricing-extra #description").prop("disabled", true);
                        $("#pricing-extra #extra_total").prop("disabled", true);
                        $('#btn_add_extra_charge').attr("disabled", "disabled");
                    }
                    $("#pricing-extra").modal('show');
                });
                $("#btn_add_extra_charge").click(function () {
                    var extra_type = $("#hidden_extra_type").val();
                    var agent_id = $("#agent_id").val();
                    var agent_name = $("#agent_id option:selected").text();
                    var charge_type_id = $("#charge_type").val();
                    var charge_type_text = $("#charge_type option:selected").text();
                    var description = $.trim($("#description").val());
                    var extra_total = $.trim($("#extra_total").val());

                    var totals_extras = $("#" + extra_type + "_totals_extras").val();
                    totals_extras++;

                    var tr = '';
                    if (agent_id == '') {
                        alert("Select agent.");
                        $("#agent_id").focus();
                        return false;
                    }
                    if (charge_type_id == '') {
                        alert("Select charge type.");
                        $("#charge_type").focus();
                        return false;
                    }
                    if (extra_total == '') {
                        alert("Enter extra total.");
                        $("#extra_total").focus();
                        return false;
                    }

                    tr += '<tr id="' + extra_type + '_row_' + totals_extras + '">';
                    tr += '<td>' + agent_name + '</td>';
                    tr += '<td>' + charge_type_text + '</td>';
                    tr += '<td>' + description + '</td>';
                    tr += '<td>' + extra_total + '</td>';
                    tr += '<td width="115px;">';
                    tr += '<input type="hidden" id="' + extra_type + '_agent_id_' + totals_extras + '" name="' + extra_type + '_agent_id[]" value="' + agent_id + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_charge_type_id_' + totals_extras + '" name="' + extra_type + '_charge_type_id[]" value="' + charge_type_id + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_description_' + totals_extras + '" name="' + extra_type + '_description[]" value="' + description + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_extra_total_' + totals_extras + '" name="' + extra_type + '_extra_total[]" value="' + extra_total + '" />';
                    //tr += '<button type="button" class="btn btn-primary btn-sm update_extra" data-type="'+extra_type+'" data-row_num="'+totals_extras+'">&nbsp;<i class="fa fa-edit"></i> Edit&nbsp;</button>';
                    tr += '<button type="button" class="btn btn-danger btn-sm remove_extra" data-type="' + extra_type + '" data-row="' + extra_type + '_row_' + totals_extras + '">&nbsp;<i class="fa fa-trash-o"></i> Del&nbsp;</button>';
                    tr += '</td>';
                    tr += '</tr>';

                    $("#" + extra_type + "_extras_body").append(tr);

                    $("#agent_id").val('');
                    $("#charge_type").val('');
                    $("#description").val('');
                    $("#extra_total").val('');
                    //$("#agent_id").select2('val', '');
                    $("#agent_id").val('');
                    $("#" + extra_type + "_totals_extras").val(totals_extras);
                    updateExtra(extra_type);
                });
                
                /*
             *         Handle Invoice  File upload
             */
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
                        form_data.append('consignment_id', $('#consignment_id').val());
                        form_data.append('action', "upload_user_doc");
                        form_data.append('user_doc_file', file_data);
                        $.ajax({
                            url: "shipment_view_manage.php", // point to server-side PHP script
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
                                        url: 'shipment_view_manage.php',
                                        type: 'POST',
                                        data: {action: 'remove_user_doc', doc_id: userDocId},
                                        headers: {
                                        },
                                        success: function () {
                                            var divname = userDocId.split('.');
                                            $("#usr_doc_" + divname[0]).remove();
                                            $("#send_custom_email").hide();
                                        },
                                        error: function (xhr, status, error) {

                                        }
                                    });

                                }
                            });

                });
             
                
                $(document).on('click', '.remove_extra', function () {
                    var row = $(this).data("row");
                    var extra_type = $(this).data("type");
                    if (confirm("Are you sure you want to remove?")) {
                        $("#" + row).remove();
                        updateExtra(extra_type);
                    }
                });
                $("#price_audit_id").on("click", "input", function () {
                    $("#audit_content").html("<td colspan='4'>Please Wait...</td>");
                    var price_audit_con_id = "";
                    price_audit_con_id = $("#btn_price_audit").data("con_id");
                    $.post('shipment_view_manage.php', {func: 'get_price_audit_log', consignment_id: price_audit_con_id}, function (data) {
                        $("#audit_content").html(data);
                    });
                });
                $("body").on("click", ".details_view", function () {
                    $("#audit_content_details").html("<p>Please Wait...</p>");
                    $('#audit-log-details').modal('show');
                    var log_id = "";
                    log_id = $(this).data("log-id");
                    $.post('shipment_view_manage.php', {func: 'get_price_audit_log_detail', log_id: log_id}, function (data) {
                        $("#AncillaryChargesDetailsTxt").val(log_id);
                        $("#audit_content_details").html(data);
                    });
                });
                getCustomerDetais('<?php echo $this->userAccountObj->getId() ?>');
            });
            function updateExtra(extra_type) {
                var extras_total = 0;
                $("input[name='" + extra_type + "_extra_total[]']").each(function () {
                    extras_total += parseFloat($(this).val());
                });
                if (extra_type == 'agent') {
                    $("#agent_extra").val(extras_total);
                } else if (extra_type == 'customer') {
                    $("#customer_extra").val(extras_total);
                }
                updateInvoiceTotal();
            }
            function updateInvoiceTotal() {
                var customer_total = 0;
                var purchase_invoice_total = 0;
                var agent_total = 0;
                var margin = 0;
                var discount = 0;
                $(".customer_charges").each(function () {
                    var val = $.trim($(this).val());
                    val = (val == '' ? 0 : val);
                    customer_total += parseFloat(val);
                });
                discount = $.trim($("#pricing-invoice #discount").val());
                if (discount != '' && !isNaN(discount)) {
                    customer_total -= parseFloat(discount);
                } else {
                    $("#pricing-invoice #discount").val('');
                }
                $(".agent_charges").each(function () {
                    if ($(this).attr('name') != 'reference') {
                        var val = $.trim($(this).val());
                        val = (val == '' ? 0 : val);
                        agent_total += parseFloat(val);
                    }
                });
                $(".purchase_invoice_charges").each(function () {
                    if ($(this).attr('name') != 'reference') {
                        var val = $.trim($(this).val());
                        val = (val == '' ? 0 : val);
                        purchase_invoice_total += parseFloat(val);
                    }
                });
                
                margin = parseFloat(customer_total - agent_total);
                $("#customer_total").val(customer_total);
                $("#agent_total").val(agent_total);
                $("#purchase_invoice_total").val(purchase_invoice_total);
                $("#margin").val(margin);
            }
            //Give contact details of agent and customer
            function contactdetails(agentid, usertelephone)
            {
                if (agentid != "")
                {
                    $('#loadcontent').show();
                    $.post(
                            "ajaxbooking.php",
                            {action: 'CUSTOMER_DISPLAY', agentid: agentid, usertelephone: usertelephone},
                            function (data)
                            {
                                $("#loadcontent_title").html("Contact Details");
                                $('#loadcontent').html(data.html);
                                $('#footer_links').html(data.links);
                            }, "json");
                }
            }
            //Load Log Window
            function displayLog(id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php",
                        {action: 'UPDATE_LOG', id: id},
                        function (data)
                        {
                            $("#loadcontent_title").html("Logs");
                            $('#loadcontent').html(data.html);
                            $('#footer_links').html(data.links);
                            $('[data-toggle="popover"]').popover();
                        }, "json");
            }
            //LOAD AUDIT
            function ShowAudit(consignment_id)
            {
                $.post('shipment_view_manage.php', {func: 'get_audit_log', consignment_id: consignment_id}, function (data) {
                    $("#audit_content").html(data);
                });
            }
            //Load Email Window
            function SendEmail(id)
            {
                $('#loadcontent').show();
                $('#followup_meeting_date').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true, autoclose: true});
                $.post(
                        "ajaxbooking.php", {action: 'SEND_PODEMAIL', id: id},
                        function (data)
                        {
                            $("#loadcontent_title").html("Send Email");
                            $('#loadcontent').html(data.html);
                            $('#loadcontent').find("#followup_meeting_date").datetimepicker({showOn: 'click', dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true, autoclose: true});
                            $('#footer_links').html(data.links);
                        }, "json");

            }
            
            //Load Send Carrier Email Window
            function SendCarrierEmail(id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'SEND_CARRIEREMAIL', id: id},
                        function (data)
                        {
                            $("#loadcontent_title").html("Send Carrier Email");
                            $('#loadcontent').html(data.html);
                            $('#footer_links').html(data.links);
                        }, "json");

            }
            //Weight Discrepancy Button
            function weightDiscrepancy(weight, id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php",
                        {action: 'WEIGHT_DISCREPANCY', weight: weight, id: id},
                        function (data)
                        {
                            $("#loadcontent_title").html("Weight Discrepancy");
                            $('#loadcontent').html(data.html);
                            $('#footer_links').html(data.links);
                        }, "json");
            }
            //volume calculation window.
            function volumecalculation(id, isInvoiced)
            {
                $('#loadcontent').show();
                var volbase = document.getElementById("volbase").value;
                $.post(
                        "ajaxbooking.php",
                        {
                            action: 'VOLUME_DISPLAY',
                            id: id,
                            isInvoiced: isInvoiced,
                            volbase: volbase
                        },
                        function (data)
                        {
                            $("#loadcontent_title").html("Volume Calculation");
                            $('#loadcontent').html(data.html);
                            $('#footer_links').html(data.links);
                        }, "json");
            }
            //Show quotation
            function showQuotation(id)
            {
                $('#loadcontent').show();
                var quotationid = document.getElementById("quotationreference").value;
                if (quotationid == '')
                {
                    swal("Sorry!", 'Please enter Quotation Reference.', "error");
                    return false;
                }
                $.post("ajaxbooking.php", {action: 'ShowQuotation', quotationid: quotationid, id: id},
                        function (data)
                        {
                            $("#loadcontent_title").html("Show Quotation");
                            if (data.html) {
                                $('#loadcontent').html(data.html);
                            }
                            $('#footer_links').html(data.links);
                            $('#myModalvolume').modal('show');
                        }, "json");
            }
            //Resend Email to Customer if There is discrepancy in Weight
            function resendWeightEmail(id, oldweight, newweight)
            {
                if (oldweight == "")
                    oldweight = 0;
                var differenceWt = newweight - oldweight;
                if (differenceWt == 0)
                {
                    alert("There is no discrepancy in Weight!!!");
                } else
                {
                    $('#loadcontent').show();
                    $.post(
                            "ajaxbooking.php", {action: 'ResendWeightEmail', id: id},
                            function (data)
                            {
                                alert(data);
                            });
                }
            }
            //Resend Email to Customer if there is discrepancy in weight due to volume
            function resendVolumeEmail(id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'ResendVolumeEmail', id: id},
                        function (data)
                        {
                            alert(data);
                        });
            }
            function EditValueType()
            {
                document.getElementById('highlowvalueedit').style.display = '';
                document.getElementById('highlowvalueshow').style.display = 'none';
            }
            function SaveExportCustomer(id)
            {
                var custom_export_number = document.getElementById("custom_export_number").value;
                $.post(
                        "ajaxbooking.php",
                        {action: 'CUSTOM_EXPORT', custom_export_number: custom_export_number, id: id},
                        function (data)
                        {
                            if (data.status == "success") {
                                swal("Sorry!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        }, "json");
            }
            function SaveHighValue(id)
            {
                var highlowvalue = document.getElementById('highlowvalue').value;
                $.post(
                        "ajaxbooking.php",
                        {action: 'SAVEHIGHVALUE', highlowvalue: highlowvalue, id: id},
                        function (data)
                        {
                            if (data == "Success")
                            {
                                var vtype = "";
                                if (highlowvalue == 'HV') {
                                    vtype = 'High';
                                } else if (highlowvalue == 'MV') {
                                    vtype = 'Medium';
                                } else {
                                    vtype = 'Low';
                                }
                                document.getElementById('valueType').innerHTML = vtype;
                                document.getElementById('highlowvalueedit').style.display = 'none';
                                document.getElementById('highlowvalueshow').style.display = '';
                            }

                        });
            }
            function pricingCalculation(consignmentid, accountId) {
                $("#pricing_details_msg").hide();
                //$("#update_pricing_details_frm").find("input[type=text],input[type=hidden]").val("");
                $("#update_pricing_details_frm").find("input[name=action]").val("UPDATE_PRICING_DETAILS");
        //                $("#update_pricing_details_frm").find("input[type=text]").attr("readonly", "readonly");
                $('#description').removeAttr("readonly");
                $('#extra_total').removeAttr("readonly");
               
                $.post("ajaxbooking.php", {action: 'PRICING_DETAILS', id: consignmentid, accountId: accountId}, function (data) {
                    if ($.type(data) === 'object') {
                        $.each(data, function (key, value) {
                            $("#pricing-invoice #" + key).val(value);
                            if (key == 'invoice_id') {
                                if (value > 0) {
                                    $('.customer_charges').attr("readonly", "readonly");
                                    $('#btn_add_extra_charge').attr("disabled", "disabled");
                                    $('#customer_extras_body .remove_extra').attr("disabled", "disabled");
                                }
                            }
                            if (key == 'invoice_number') {
                                $("#invoice_no_show").html(value);
                                $("#invoice_no").val(value);
                            }
                            if (key == 'tarff_name') {
                                $("#tariff_name").val(value);
                            }
                        });
                        var dataancellarycharges = '<table  class="table table-bordered table-hover table-striped"><tbody>';
                        var ancellaryChae = data.ancillary_charges_details

                        var dataancellarychargesTmp = '';
                        if (data.ancillary_charges_details != null)
                        {
                            $.each(data.ancillary_charges_details, function (key, value) {
                                var nm = key.split('_').join('&nbsp;');
                                dataancellarychargesTmp += '<tr><td>' + nm + '</td><td>' + value + '</td></tr>';
                            });
                        }
                        dataancellarycharges += dataancellarychargesTmp;
                        if (dataancellarychargesTmp == '')
                        {
                            dataancellarycharges += '<tr><td>No Data Found</td></tr>';
                        }
                        dataancellarycharges += '</tbody></table>';
                        if (data.invoice_no == "") {
                            $("#update_pricing_details_frm").find("input[type=text]").prop("disabled", false);
                            $("#update_pricing_details_btn").prop('disabled', false);

                            $("#ancillary-charges").popover({content: dataancellarycharges, placement: 'left', html: true}).popover();

                        } else {
                            $("#update_pricing_details_frm").find("input[type=text].agent_charges").prop("disabled", false);
                        }
                    }
                    $("#update_pricing_details_frm").find("#consignment_id").val('<?php echo $this->id; ?>');
                    updateInvoiceTotal();
                    //                    loadExtras(data.invoice_detail_id);
                }, "json");

            }
        //            function loadExtras(invoice_detail_id) {
        //                if (invoice_detail_id != '') {
        //                    $.post('ajaxbooking.php', {action: 'get_extras', invoice_detail_id: invoice_detail_id}, function (data) {
        //                        $("#customer_extras_body").html(data.customerData);
        //                        $("#agent_extras_body").html(data.agentData);
        //                        $("#customer_totals_extras").val(data.customerCount);
        //                        $("#agent_totals_extras").val(data.agentCount);
        //                        $("#extra").val(data.customerTotalExtras);
        //                        updateInvoiceTotal();
        //                    }, "json");
        //                } else {
        //                    updateInvoiceTotal();
        //                }
        //            }
            function updatePricingDetails() {
                $("#update_pricing_details_btn").prop('disabled', 'disabled');
                $("#update_pricing_details_btn").off('click');
                var form_data = $("#update_pricing_details_frm").serialize();
                if ($("#pricing_details_msg").hasClass("alert-success")) {
                    $("#pricing_details_msg").removeClass("alert-success");
                }
                $("#pricing_details_msg").addClass("alert-info");
                $("#pricing_details_msg").html("please wait we are saving...");
                $("#pricing_details_msg").show();
                $.post("ajaxbooking.php", form_data, function (data) {
                    if ($("#pricing_details_msg").hasClass("alert-info")) {
                        $("#pricing_details_msg").removeClass("alert-info");
                    }
                    $("#pricing_details_msg").addClass("alert-success");
                    $("#pricing_details_msg").html("Updated successfully.");
                    $("#pricing_details_msg").show();
                });
            }
            
            function pricingUpdateCalculation(consignmentid) {
                $("#pricing_details_msg").hide();
                //$("#update_pricing_details_frm").find("input[type=text],input[type=hidden]").val("");
                $("#update_pricing_details_frm").find("input[name=action]").val("UPDATE_PRICING_DETAILS_CALCULATION");
                $("#update_pricing_details_frm").find("input[type=text]").attr("readonly", "readonly");
                swal({
                    title: "Are You Sure?",
                    text: "you want to Update the pricing tariff of shipnment!",
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
                                $.post("shipment_view_manage.php", {action: 'UPDATE_PRICING_DETAILS_CALCULATION', consignment_id: consignmentid}, function (data) {
                                    swal("Message!", data, "success");
                                });
                            }
                        });
            }
            function ShowLabel(labelLink)
            {
                if (labelLink != '')
                {
                    var label = labelLink.replace("../", "");
                    window.open("<?= SETTING_URL_ASSETS; ?>pdf/" + label);
                } else
                {
                    alert("Label does not exits.")
                }
            }
            function updateConsignmentTracking()
            {
                var conId = $("#consignment_id_tracking").val();
                var tracking_new = document.getElementById('tracking_new').value;
                var action = "UPDATE_TRACKING";
                $.post("shipment_view_manage.php", {action: action, id: conId, tracking_new: tracking_new}, function (data) {
                    if ($.type(data) === 'object') {
                        if (data.STATUS == "SUCCESS") {
                            swal("Success!", data.MESSAGE, "success");
                        } else {
                            swal("Sorry!", data.MESSAGE, "error");
                        }
                    }
                }, "json");
            }
            function getCustomerDetais(accountId)
            {
                var accountDetail = accountDetail;
                $.ajax({
                    url: "shipment_view_manage.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'GET_USER_DETAILS',
                        account_id: accountId
                    },
                    success: function (data) {
                        var obj = jQuery.parseJSON(data);
                        $("#ad-account").html(obj.useraccount);
                        $("#ad-fullname").html(obj.fullname);
                        $("#ad-company").html(obj.company);
                        $("#ad-returnaddress").html(obj.returnaddress);
                        $("#ad-email").html(obj.email);
                        $("#ad-telephone").html(obj.telephone);
                        $("#ad-billingaddress").html(obj.billingaddress);
                        $("#ad-alternativeemail").html(obj.alternativeemail);
                        $("#ad-country").html(obj.country);
                    }
                });
            }
            function updateConsignmentRemote()
            {
                var conId = $("#consignment_id_remote").val();
                var remote_area_value = $("#remote_area_value").val();
                if ($("#remote_area_value").is(":checked") == true) {
                    var remotearea = '1';
                } else {
                    var remotearea = '0';
                }
                var action = "UPDATE_REMOTE";
                $.post("shipment_view_manage.php", {action: action, id: conId, remotearea: remotearea}, function (data) {
                    if ($.type(data) === 'object') {
                        if (data.STATUS == "SUCCESS") {
                            $('#remote-area-div').html(remotearea);
                            if ($("#update_remote_msg").hasClass("alert-danger"))
                                $("#update_remote_msg").removeClass("alert-danger");
                            $("#update_remote_msg").addClass("alert-success").html(data.MESSAGE).removeClass("hidden");

                        } else {
                            if ($("#update_remote_msg").hasClass("alert-success"))
                                $("#update_remote_msg").removeClass("alert-success");
                            $("#update_remote_msg").addClass("alert-danger").html(data.MESSAGE).removeClass("hidden");
                        }
                    }
                }, "json");
            }
            //Save log to the database
            function AddLogbutton(id)
            {
                $('#loadcontent').show();
                var internallog = document.getElementById("internallog").value;
                var customerlog = document.getElementById("customerlog").value;
                var reminder = "";
                $.post(
                        "ajaxbooking.php",
                        {action: 'ADD_LOG', id: id, internallog: internallog, customerlog: customerlog, reminder: reminder},
                        function (data)
                        {
                            swal("Success!", data, "success");
                            $('#loadcontent').show();
                            displayLog(id);
                        });
            }
            //Cancel Button 
            function Cancelbutton()
            {
                $('#loadcontent').hide();
                $('#myModalvolume').modal('hide');
            }
            //Send email to customer and agent from Log
            function SendLogEmail(email, user, count)
            {
                var sendemail = email;
                if (user == "customer")
                {
                    var customermail = "";
                    var clogid = "";
                    for (i = 0; i < count; i++) {
                        customermail = document.getElementById("csend" + i).checked;
                        if (customermail == true)
                        {
                            if (clogid == "")
                                clogid = document.getElementById("csend" + i).value;
                            else
                                clogid = clogid + "," + document.getElementById("csend" + i).value;
                        }
                    }
                } else {
                    var agentmail = "";
                    var clogid = "";
                    for (i = 0; i < count; i++) {
                        agentmail = document.getElementById("asend" + i).checked;
                        if (agentmail == true)
                        {
                            if (clogid == "")
                                clogid = document.getElementById("asend" + i).value;
                            else
                                clogid = clogid + "," + document.getElementById("asend" + i).value;
                        }
                    }
                }
                $.post(
                        "ajaxbooking.php",
                        {action: 'SEND_EMAIL', email: sendemail, user: user, clogid: clogid},
                        function (data)
                        {
                            swal("Success!", data, "success");
                        });

            }
            //Load Email Window
            function forwardLogEmail(id)
            {
                var id = id;
                var internalmessage = $('#internallog').val();
                var customermessage = $('#customerlog').val();
                $.post(
                        "ajaxbooking.php", {action: 'SEND_FORWARDEMAIL', id: id, internalmessage: internalmessage, customermessage: customermessage},
                        function (data)
                        {
                            $('#loadcontent').html(data);
                        });
            }
            //Send email to customer and agent from Log
            function SendLogFarwordEmail(id)
            {
                var cmail = document.getElementById("cemail").checked;
                var amail = document.getElementById("aemail").checked;
                if (cmail == true) {
                    cmail = "customer";
                }
                if (amail == true) {
                    amail = "agent";
                }
                var cmailcc = document.getElementById("cmailcc").value;
                var amailcc = document.getElementById("amailcc").value;
                var message = document.getElementById("emailmessage").value;
                $.post(
                        "ajaxbooking.php", {action: 'SEND_FARWORD_EMAIL', id: id, cmail: cmail, amail: amail, cmailcc: cmailcc, amailcc: amailcc, message: message},
                        function (data)
                        {
                            swal("Success!", data, "success");
                        });

            }
            //Send Email to Customer or Agent
            function SendPodEmail(id)
            {
                var cmail = document.getElementById("cemail").checked;
                var amail = document.getElementById("aemail").checked;
                if (cmail == true) {
                    cmail = "customer";
                }
                if (amail == true) {
                    amail = "agent";
                }
                var cmailcc = document.getElementById("cmailcc").value;
                var amailcc = document.getElementById("amailcc").value;
                var message = document.getElementById("emailmessage").value;
                var reminder = document.getElementById("followup_meeting_date").value;
                $.post(
                        "ajaxbooking.php", {action: 'SENDEMAILPOD', id: id, cmail: cmail, amail: amail, cmailcc: cmailcc, amailcc: amailcc, message: message, reminder: reminder},
                        function (data)
                        {
                            swal("Success!", data, "success");
                        });
            }
            
            //Send email to carrier for custom documents
            function SendCarrierCustomEmail(id)
            {
               
                var carrieremail = document.getElementById("carrieremail").value;
                var message = document.getElementById("emailmessage").value;
                $.post(
                        "ajaxbooking.php", {action: 'SEND_CARRIER_CUSTOM_EMAIL', id: id, carrieremail: carrieremail, message: message},
                        function (data)
                        {
                            swal("Success!", data, "success");
                        });

            }
            
            //get Difference between custom weight and original weight
            function differenceweight(weight)
            {
                var cweight = document.getElementById("customerweight").value;
                var differenceWt = cweight - weight;
                document.getElementById("diffweight").value = differenceWt;
            }
            //if there is difference in weight than save new weight 
            function saveWeight(id, weight)
            {
                $('#loadcontent').show();
                var cweight = document.getElementById("customerweight").value;
                $.post(
                        "ajaxbooking.php",
                        {action: 'SAVE_WEIGHT', cweight: cweight, weight: weight, id: id},
                        function (data)
                        {
                            document.getElementById("weight-update-section").innerHTML = cweight;
                            $('#updatedweight').val(cweight);
                            pricingUpdateCalculation(id);
                             swal("Success!", data, "success");
                        });
            }
            //calculate volumatric weight based on dimensions
            function calcuVolWeight(i)
            {
                var pcs = document.getElementById("pcs" + i).value;
                var ptracking = document.getElementById("ptracking" + i).value;
                var length = document.getElementById("length" + i).value;
                var width = document.getElementById("width" + i).value;
                var height = document.getElementById("height" + i).value;
                var volbase = document.getElementById("volbase").value;
                var vol_denometer = document.getElementById("vol_denometer" + i).value;
                if(volbase <= 0){
                    volbase = vol_denometer;
                }
                if(width > 0 && height > 0 && length > 0){
                    
                    var volweight = (pcs * length * width * height) / volbase;
                    document.getElementById("kg" + i).value = volweight;
                    totalVolWeight(i);
                 }
            }
            //get total of all volumetric weight
            function totalVolWeight(i)
            {
                
                var totalWeight = 0.00;
                /*if (i == 0)
                {
                    totalWeight = 0;
                } else {
                    totalWeight = document.getElementById("totalvolweight").value;
                }
                if (totalWeight == "")
                {
                    totalWeight = 0;
                }*/
                $(document).find('.totalkgweight').each(function(index, thisdom){
                    //alert($(this).val());
                    if(parseFloat($(this).val()) > 0){
                        totalWeight += parseFloat($(this).val());
                    }
                });
                //var volweight = document.getElementById("kg" + i).value;
                //var weight = parseFloat(volweight) + parseFloat(totalWeight);
                document.getElementById("totalvolweight").value = totalWeight.toFixed(2);
            }
            //save the volumetric weight
            function saveVolume(id, numberPieces)
            {
                var totalpieces = "";
                var totallength = "";
                var totalwidth = "";
                var totalheight = "";
                for (i = 0; i < numberPieces; i++)
                {
                    var pcs = document.getElementById("pcs" + i).value;
                    var length = document.getElementById("length" + i).value;
                    var width = document.getElementById("width" + i).value;
                    var height = document.getElementById("height" + i).value;
                    var totalWeight = document.getElementById("totalvolweight").value;
                    var ptracking = document.getElementById("ptracking" + i).value;

                    var volbase = document.getElementById("volbase").value;
                    var volweight = (pcs * length * width * height) / volbase;

                    totalpieces += String(pcs) + "||";
                    totallength += String(length) + "||";
                    totalwidth += String(width) + "||";
                    totalheight += String(height) + "||";
                    //var totalvolweight = volweight + "||";

                }

                if (totalpieces != "" && totallength != "" && totalwidth != "" && totalheight != "")
                {
                    $.post(
                            "ajaxbooking.php",
                            {action: 'SAVE_VOLUME', id: id, pcs: totalpieces, ptracking: ptracking, length: totallength, width: totalwidth, height: totalheight, totalWeight: totalWeight, volweight: volweight},
                            function (data)
                            {
                                document.getElementById("updateweight").value = totalWeight;
                                pricingUpdateCalculation(id);
                                 swal("Success!", data, "success");
                            });
                }
            }
            //Resend Email to Customer if There is discrepancy in Weight
            function resendWeightEmail(id, oldweight, newweight)
            {
                var updateweight = $('#updatedweight').val();
                if (oldweight == "") {
                    oldweight = 0;
                }
                if(updateweight > 0){
                    newweight = updateweight;
                }
                var differenceWt = newweight - oldweight;
                if (differenceWt == 0)
                {
                    swal("Sorry!", "There is no discrepancy in Weight!!!", "error");
                } else
                {
                    $('#loadcontent').show();
                    $.post(
                            "ajaxbooking.php", {action: 'ResendWeightEmail', id: id},
                            function (data)
                            {
                                if (data.status == "success") {
                                    swal("Success!", data.message, "success");
                                } else {
                                    swal("Sorry!", data.message, "error");
                                }
                            }, "json");
                }
            }
            //Resend Email to Customer if there is discrepancy in weight due to volume
            function resendVolumeEmail(id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'ResendVolumeEmail', id: id},
                        function (data)
                        {
                            if (data.status == "success") {
                                swal("Success!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        }, "json");
            }
            function updateConsignmentAgent()
            {
                var conId = $("#consignment_id_agent").val();
                var agent_area_value = document.getElementById('agent_area_id').value;
                var action = "UPDATE_AGENT";
                $.post("shipment_view_manage.php", {action: action, id: conId, agentcode: agent_area_value}, function (data) {
                    if ($.type(data) === 'object') {
                        if (data.STATUS == "SUCCESS") {
                            if ($("#update_agent_msg").hasClass("alert-danger")) {
                                $("#update_agent_msg").removeClass("alert-danger");
                            }
                            $('#agent').val(data.new_Agent);
                            $("#update_agent_msg").addClass("alert-success").html("Your information has been successfully changed...").removeClass("hidden");
                        } else {
                            if ($("#update_agent_msg").hasClass("alert-success")) {
                                $("#update_agent_msg").removeClass("alert-success");
                            }
                            $("#update_agent_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }
                }, "json");
            }
            function updateConsignmentAccount()
            {
                var conId = $("#consignment_id_account").val();
                var oldAccount = $("#old_account").val();
                var newAccount = $("#new_account").val();
                var newUser = $("#new_user_id").val();
                var hawbAccount = $("#hawb_account").val();
                var action = "UPDATE_UPDATE_ACCOUNT";
                if (newAccount == '')
                {
                    if ($("#update_change_account_msg").hasClass("alert-success")) {
                        $("#update_change_account_msg").removeClass("alert-success");
                    }
                    $("#update_change_account_msg").addClass("alert-danger").html("Please select account and user to change.").removeClass("hidden");
                    return false;
                }
                $.post("shipment_view_manage.php", {action: action, id: conId, oldAccount: oldAccount, newAccount: newAccount, newUser: newUser}, function (data) {
                    if ($.type(data) === 'object') {
                        if (data.STATUS == "SUCCESS") {
                            if ($("#update_change_account_msg").hasClass("alert-danger")) {
                                $("#update_change_account_msg").removeClass("alert-danger");
                            }
                            $("#update_change_account_msg").addClass("alert-success").html("Your account has been successfully changed.").removeClass("hidden");
                        } else {
                            if ($("#update_change_account_msg").hasClass("alert-success")) {
                                $("#update_change_account_msg").removeClass("alert-success");
                            }
                            $("#update_change_account_msg").addClass("alert-danger").html("Please select account and user to change...").removeClass("hidden");
                        }
                    }
                }, "json");
            }
            function get_account_users() {
                var account_id = $('#new_account').val();
                var form_data = new FormData();
                var conId = $("#consignment_id_account").val();
                form_data.append('account_id', account_id);
                form_data.append('consignment_id', conId);
                form_data.append('func', "get_user_from_account");
                $.ajax({
                    url: 'shipment_view_manage.php',
                    dataType: 'html',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('#new_user_id').html(response);
                    }
                });
            }
            function dispalyDetailMessage(logId,messageFor) {
                $.post(
                    "ajaxbooking.php", {action: 'FETCH_LOG_MESSAGE', logId: logId,messageFor: messageFor},
                    function (data)
                    {
                        
                }, "json");
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php if($this->consignmentObj->getIsCustomerBillable() != 1){?>
        <h2 class="alert alert-danger text-center">
            Customer Non-invoiceable shipment<!--/h2-->
        </h2>
            <?php }?>
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet bordered" style="padding-top: 10px !important">


<div style="margin-left: 10px">
                <div class="tiles">
                    <div class="tile bg-blue-steel">
                                    <div class="tile-body">
                                        <?php echo Consignment::getShipnmentStatus($this->consignmentObj->getShipmentStatus()) ?>
                                        <i class="fa fa-print"></i>
                                        
                                    </div>
                                    <div class="tile-object">
                                        <div class="number"> Shipment Status </div>
                                        <!-- <div class="number"> 124 </div> -->
                                    </div>
                                </div>

                                <div class="tile bg-green">
                                    <div class="tile-body">

                                        <h5 style="line-height: 1.1em"><b>
                                         <?php
                            $serviceName = "";
                            if ($this->consignmentObj->getCustomizedServiceId() > 0) {
                                $service = new Services($this->consignmentObj->getCustomizedServiceId());
                                $serviceName = $service->getName() . "<br/><br />";
                            }
                            $serviceObj = new Services($this->consignmentObj->getServiceId());
                            echo $serviceName .= $serviceObj->getName();
                            ?></b></h5>
                                    </div>
                                    <div class="tile-object">
                                        <div class="number">  Service </div>
                                        <!-- <div class="number"> 124 </div> -->
                                    </div>
                                </div>

                </div>

                      

</div>



<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <small>Account Code</small>
                                        <h3 class="font-green-sharp">
                                            <span data-counter="counterup" data-value="7800"><a href="javascript:;" title="Account Details" data-target="#account-details" data-toggle="modal" style="color: #2ab4c0!important"></span><?php echo $this->userAccountObj->getUserAccount(); ?></a></span>
                                            <!-- <small class="font-green-sharp">$</small> -->
                                        </h3>
                                        
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                            
                                        </span>
                                    </div>
                                    <!-- <div class="status">
                                        <div class="status-title"> progress </div>
                                        <div class="status-number"> 76% </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>




<div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <small>HAWB No.</small>
                                        
                                           
                                        <h<?php echo (strlen($this->consignmentObj->getHawb())<=13)?3:5?> class="font-red-haze">
                                            <span data-counter="counterup" data-value="<?php echo $this->consignmentObj->getHawb(); ?>"><?php echo $this->consignmentObj->getHawb(); ?></span>
                                        </h<?php echo (strlen($this->consignmentObj->getHawb())<=13)?3:5?>>
                                        
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-barcode"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success red-haze">
                                            <!-- <span class="sr-only">85% change</span> -->
                                        </span>
                                    </div>
                                  <!--   <div class="status">
                                        <div class="status-title"> change </div>
                                        <div class="status-number"> 85% </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>




<div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2">
                                <div class="display">
                                    <div class="number">
                                        <small>Shipment No.</small>
                                         <h<?php echo (strlen($this->consignmentObj->getAwb())<=13)?3:5?>><a style="color:#5C9BD1!important" href="tracking.php?tracking_number=<?=$this->consignmentObj->getAwb();?>" target="_blank"> <?php echo $this->consignmentObj->getAwb() ?></a></h<?php echo (strlen($this->consignmentObj->getAwb())<=13)?3:5?>>
                                        <br />
                                       <small>Parcel Tracking No. </small>
                                        
                                            <?php
                                                $parcelFilter = new ParcelFilter();
                                                $parcelFilter->addConsignmentIdFilter($this->id);
                                                $this->parcel_list = $parcelFilter->getList();
                                                if (count($this->parcel_list) > 0) {
                                                    $parcel_count = sizeof($this->parcel_list);
                                                    foreach ($this->parcel_list as $parcel) {
                                                        echo '<h'.((strlen($parcel->getTrackingNumber())<=13)?3:5) .' class="font-blue-sharp">'
                                                                . '<span data-counter="counterup" data-value="'.$parcel->getTrackingNumber().'">'
                                                                . ' <a style="color:#5C9BD1!important" href="tracking.php?tracking_number=' . $parcel->getTrackingNumber() . '" target="_blank">' . $parcel->getTrackingNumber() . "</a>"
                                                                . '</span>'
                                                                . '</h'.((strlen($parcel->getTrackingNumber())<=13)?3:5).'>';
                                                    }
                                                }
                                                ?>
                                    
                               

                        <?php if($this->consignmentObj->getReturnAwb() != ''){ ?>   
                            <small>Return Tracking No.</small>
                            <h<?php echo (strlen($this->consignmentObj->getReturnAwb())<=13)?3:5?>>
                                    <a style="color:#5C9BD1!important" href="tracking.php?tracking_number=<?=$this->consignmentObj->getReturnAwb();?>" target="_blank"> 
                                        <?php echo $this->consignmentObj->getReturnAwb() ?>
                                    </a>
                            </h<?php echo (strlen($this->consignmentObj->getReturnAwb())<=13)?3:5?>>
                        <?php } ?>

                        
                                        
                                       
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-barcode"></i>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success blue-sharp">
                                            <!-- <span class="sr-only">45% grow</span> -->
                                        </span>
                                    </div>
                                    <!-- <div class="status">
                                        <div class="status-title"> grow </div>
                                        <div class="status-number"> 45% </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>




    </div>


                
            </div>
            <!-- END PORTLET MAIN -->
            <!-- PORTLET MAIN -->
            <div class="portlet light bordered">
                <!-- STAT -->
    


                <!-- END STAT -->
                <div>
                  
                    <?php if(!empty($this->consignmentObj->getDateCreated())){?>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-calendar"></i>Created Date
                        <?php
                        echo (!empty($this->consignmentObj->getDateCreated()) ? date('d-m-Y', strtotime($this->consignmentObj->getDateCreated())) : '')
                        ?>
                    </div>
                    <?php }
                    if(!empty($this->consignmentObj->getDateLabelCreated())){
                    ?>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-calendar"></i>Label Created Date 
                        <?php echo (!empty($this->consignmentObj->getDateLabelCreated())) ? date('d-m-Y', $this->consignmentObj->getDateLabelCreated()) : ''; ?> 
                    </div>
                    <?php }
                    if(!empty($this->consignmentObj->getDateScanned())){?>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-calendar"></i>Scanned Date 
                        <?php
                        echo (!empty($this->consignmentObj->getDateScanned()) && $this->consignmentObj->getDateScanned() != '1970-01-01' ? date('d-m-Y', $this->consignmentObj->getDateScanned()) : '');
                        ?>  
                    </div>
                    <?php }
                     if(!empty($this->consignmentObj->getDateBooked())){
                    ?>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-calendar"></i>Booked Date
                        <?php
                        echo (!empty($this->consignmentObj->getDateBooked()) && $this->consignmentObj->getDateBooked() != '1970-01-01' ? date('d-m-Y', $this->consignmentObj->getDateBooked()) : '');
                        ?> 
                    </div>
                     <?php } 
                     if(!empty($this->consignmentObj->getDateDelivered())){
                     ?>
                    <div class="margin-top-20 profile-desc-link">
                        <i class="fa fa-calendar"></i>Delivered Date

                        <?php
                        echo (!empty($this->consignmentObj->getDateDelivered()) && $this->consignmentObj->getDateDelivered() != '1970-01-01' ? date('d-m-Y', $this->consignmentObj->getDateDelivered()) : '');
                        ?>
                    </div>
                     <?php } ?>






                </div>
            </div>
            <!-- END PORTLET MAIN -->
        </div>


        <div class="profile-content smart-legend">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title tabbable-line">

                            <div class="caption caption-md">
                                                    <i class="icon-globe theme-font hide"></i>
                                                    <span class="caption-subject font-blue-madison bold uppercase">Consignments details</span>
                                                </div>


                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#shipment_tab" data-toggle="tab">Shipment Detail</a>
                                </li>
                                <li>
                                    <a href="#agent_tab" data-toggle="tab">Agent Detail</a>
                                </li>
                                <li>
                                    <a href="#parcel_tab" data-toggle="tab">Parcel Detail</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body1">
                            <div class="tab-content">
                                <!-- GENERAL QUESTION TAB -->
                                <div class="tab-pane active" id="shipment_tab">
                                    <div class="portlet1 light1">

                                        <div class="portlet-body1">

                                            <div class="row">
<div class="col-md-12">

                                                    <fieldset class="margin-top-0 ">
                                                        <legend><i class="fa fa-calculator"></i> <span class="label_new">Account Details</span></legend>

<div class="row">

                                                        <div class="col-md-6">
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-hover table-striped table-account-details">

                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new">
                                                                            Contact Name</td>
                                                                        <td> <?php echo trim($this->userObj->getFirstName()); ?> </td>
                                                                       
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Sender Address </td>
                                                                        <td width="40%"> <?php echo trim($this->userAccountObj->getReturnAddress()); ?> </td>
                                                                        
                                                                    </tr> 
                                                                    <tr>
                                                                        <td class="label_new"> Email </td>
                                                                        <td> <?php echo trim($this->userAccountObj->getEmail()); ?> </td>
                                                                       
                                                                    </tr> 
                                                                   <!--    <tr>
                                                                        <td class="label_new"> <strong>Claim Submitted </strong></td>
                                                                        <td>
                                                                          <select id="claim" class="form-control">
                                                                                <option value="No">No</option>
                                                                                <option value="Yes">Yes</option>
                                                                            </select> 

                                                                            <div class="mt-radio-inline">
                                                    <label class="mt-radio">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios4" value="option1"> Yes
                                                        <span></span>
                                                    </label>
                                                    <label class="mt-radio">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios5" value="option2"> No
                                                        <span></span>
                                                    </label>
                                                    
                                                </div>


                                                                        </td>
                                                                      
                                                                    </tr>-->
                                                                </tbody>
                                                            </table>  
                                                        </div>
                                                    </div>




<div class="col-md-6">

<div class="table-scrollable">
                                                            <table class="table table-bordered table-hover table-striped table-account-details">

                                                                <tbody>
<tr> <td class="label_new"> Company </td>
                                                                        <td> <?php echo trim($this->userAccountObj->getCompany()); ?> </td></tr>
<tr>

<td class="label_new"> Country </td>
                                                                        <td> <?php
                                                                            if ($this->userAccountObj->getCountryId() > 0) {
                                                                                $usercountry = new Country($this->userAccountObj->getCountryId());
                                                                                echo trim($usercountry->getName());
                                                                            }
                                                                            ?> </td></tr>
<tr>

                                                                             <td class="label_new"> Contact No </td>
                                                                        <td> <?php echo trim($this->userAccountObj->getTelephone()); ?> </td></tr>



  <tr><td class="label_new">
                                                                            <strong>   Invoice On Hold</strong>
                                                                        </td>
                                                                        <td>
                                                                            <?php $consignmentBillingHold = new ConsignmentBillingHoldFilter();
                                                                                  $consignmentBillingHold->where(" consignment_id = '".$this->consignmentObj->getId()."'");
                                                                                  $consignmentBillingHoldList = $consignmentBillingHold->getList();
                                                                                  $billingHold = 0;
                                                                                  if(count($consignmentBillingHoldList) > 0)
                                                                                  {
                                                                                      $billingHold = 1;
                                                                                  }
                                                                                  echo ($billingHold == 1) ?  "YES" :   "NO";
                                                                            ?>
                                                                            

                                                                        </td></tr>

     </tbody>
                                                            </table>  
                                                        </div>
</div>
</div>





                                                    </fieldset>

                                                </div>
                                                
                                                </div>



                                            <div class="row">

                                                <div class="col-md-6">

                                                    <fieldset class="margin-top-0 ">
                                                        <legend><i class="fa fa-ship"></i> <span class="label_new">Shipper Details </span></legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-hover table-striped">

                                                                <tbody>
                                                                    <tr>
                                                                        <td class="label_new"> Contact Name </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderName(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Comapny Name </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderCompany(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Address </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderAddressLine1() . " " . $this->consignmentObj->getSenderAddressLine2() . " " . $this->consignmentObj->getSenderAddressLine3(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> City </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderCity(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Postcode </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderPostcode(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Country </td>
                                                                        <td><?php
                                                                            if ($this->consignmentObj->getSenderCountryId() > 0) {
                                                                                $senderCountry = new Country($this->consignmentObj->getSenderCountryId());
                                                                                echo $senderCountry->getName();
                                                                            }
                                                                            ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Telephone </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderTelephone(); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="label_new"> Email </td>
                                                                        <td><?php echo $this->consignmentObj->getSenderEmail(); ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="margin-top-0 ">
                                                        <legend><i class="fa fa-truck"></i> <span class="label_new">Delivery Details</span> - (Reference No <?php echo $this->consignmentObj->getReference(); ?> )</legend>
                                                        <div class="table-scrollable">
                                                            <table class="table table-bordered table-hover table-striped">
                                                                <tbody>
                                                                    <tr>

                                                                        <td class="label_new"> Type of Shipment </td>
                                                                        <td> 
                                                                            <?php
                                                                            if (trim($this->consignmentObj->getShipmentType()) == 'D' || trim($this->consignmentObj->getShipmentType()) == '') {
                                                                                echo 'Dispatch service';
                                                                            } else if (trim($this->consignmentObj->getShipmentType()) == 'C') {
                                                                                echo "Collection Service";
                                                                            } else {
                                                                                echo "Product (" . $this->consignmentObj->getShipmentType() . ")";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>


                                                                           <!--      <tr>
                                                                                    <td class="label_new"> Reference No </td>
                                                                                <td> <?php echo $this->consignmentObj->getReference(); ?> </td>
                                                                            </tr>
                                                                    -->

                                                                    <tr>
                                                                        <td class="label_new"> Contact Name </td>
                                                                        <td> <?php echo $this->consignmentObj->getContact(); ?> </td>
                                                                    </tr>


                                                                    <tr><td class="label_new"> Company Name </td>
                                                                        <td> <?php echo html_entity_decode($this->consignmentObj->getCompany()); ?> </td>
                                                                    </tr>


                                                                    <tr>
                                                                        <td class="label_new"> Address </td>
                                                                        <td> <?php echo html_entity_decode($this->consignmentObj->getAddressLine1()) . ' ' . html_entity_decode($this->consignmentObj->getAddressLine2()) . ' ' . html_entity_decode($this->consignmentObj->getAddressLine3()); ?> </td>
                                                                    </tr>


                                                                    <tr>
                                                                        <td class="label_new"> City </td>
                                                                        <td> <?php echo html_entity_decode($this->consignmentObj->getCity()); ?> </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="label_new"> Postcode </td>
                                                                        <td> <?php echo $this->consignmentObj->getPostcode(); ?> </td>
                                                                    </tr>


                                                                    <tr>
                                                                        <td class="label_new"> Remote Area </td>
                                                                        <td>
                                                                            <div class="label_new">
                                                                                <div class="col-sm-6">
                                                                                    <span id="remote-area-div"><?php echo ($this->consignmentObj->getRemoteCharges() == '1') ? 'YES' : 'NO'; ?></span>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <a href="javascript:;" title="Remote Area" data-target="#remotearea_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Remote Area"></span></a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>


                                                                    <tr>
                                                                        <td  class="label_new"> Country </td>
                                                                        <td> <?php echo html_entity_decode($this->countryObj->getName()) . ' (' . $this->countryObj->getIso() . ')'; ?> </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="label_new"> Telephone </td>
                                                                        <td> <?php echo $this->consignmentObj->getTelephone(); ?> </td>


                                                                    </tr>
                                                                </tbody>
                                                            </table>  
                                                        </div>
                                                    </fieldset>


                                                </div>





                                            </div>


                                            <fieldset class="margin-top-0 ">
                                                <legend><i class="fa fa-road"></i><span class="label_new"> Shipment Details</span></legend>
<div class="row">
                                                <div class="col-md-6">
                                                <div class="table-scrollable">
                                                    <table class="table table-bordered table-hover table-striped table-shipment-details">
                                                         <tr>
                                                                        <td class="label_new"> <strong>Invoice No </strong></td>
                                                                        <?php echo $this->consignmentObj->getChargesInvoiceId()?>
                                                                        <td> <?php 
                                                                        echo $this->getInvoiceNumber($this->consignmentObj->getId(), $this->accountIdPricing);
                                                                                 ?> </td>
                                                                       

                                                        </tr>
                                                        <tr>
                                                            <th class="label_new"> Weight </th>
                                                            <td><span class="ribbon-content" id="weight-update-section"> <?php echo $this->consignmentObj->getWeight() ?> </span>KG</td>
                                                            
                                                            
                                                          
                                                        </tr>
                                                        <tr>
                                                            <th class="label_new"> Vol/Weight </th>
                                                            <td>                                                                             
                                                                <?php
                                                                $ConversionFaction = 5000;
                                                                $serviceConversion = new ServiceFilter();
                                                                $serviceConversion->addFieldFilter('id', $this->consignmentObj->getServiceId());
                                                                $serviceConversionList = $serviceConversion->getColumnList(' volumetric_denominator, service_type');
                                                                if (count($serviceConversionList) > 0) {
                                                                    $ConversionFaction = $serviceConversionList[0]->getVolumetricDenominator();
                                                                }
                                                                ?>
                                                                <div class="row1">
                                                                    <div class="col-md-6">
                                                                        <input type="text" id="updateweight" name="updateweight" class="form-control input-sm" readonly value="<?php echo $this->consignmentObj->getVolWeight(); ?>" />
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="volbase" id="volbase" class="form-control input-sm"  value="<?php echo $ConversionFaction; ?>" />
                                                                    </div>
                                                                </div>   
                                                            </td>
                                                           
                                                            
                                                        </tr>
                                                        <tr>
                                                            <td class="label_new"> Chargeable Weight </td>
                                                            <td><?php echo ($this->consignmentObj->getVolWeight() > $this->consignmentObj->getWeight()) ? $this->consignmentObj->getVolWeight() : $this->consignmentObj->getWeight(); ?></td>
                                                          
                                                        
                                                        </tr>

<tr>
  <td class="label_new"> Q/Ref </td>
                                                            <td colspan="2">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control input-sm" id="quotationreference" name="quotationreference" value="<?php echo $this->quotationId; ?>">
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-primary btn-sm" <?php
                                                                        if (trim($this->consignmentObj->getIsInvoiced()) == '1') {
                                                                            echo 'disabled="disabled"';
                                                                        }
                                                                        if ($this->user->getUserType() == User::USER_TYPE_WAREHOUSE) {
                                                                            echo 'disabled="disabled"';
                                                                        }
                                                                        ?> onclick="showQuotation(<?php echo $this->id; ?>)" >Assign Quotation</button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>


  <tr><td>
                                                                <div class="ribbon-content">
                                                                    <input type="button" class="btn btn-primary btn-sm" value="Weight" title="Modify Weight" name='btnweight' <?php
                                                                    if (trim($this->consignmentObj->getIsInvoiced()) == 'Y') {
                                                                        echo 'disabled="disabled"';
                                                                    }
                                                                    ?> onclick="weightDiscrepancy('<?php echo $this->consignmentObj->getWeight(); ?>', <?php echo $this->id; ?>)" data-toggle="modal" data-target="#myModalvolume"/>
                                                                </div>
                                                            </td>




<td>
                                                                <div class="ribbon-content">
                                                                    <input type="button" class="btn btn-primary btn-sm" value="Volume" name="btnvolume" id="btnvolume" onclick="volumecalculation(<?php echo $this->id; ?>, '<?php echo $this->consignmentObj->getIsInvoiced(); ?>')"   data-toggle="modal" data-target="#myModalvolume"/>
                                                                </div>
                                                            </td>

                                                        </tr>


                                                    </table>  
                                                </div>

                                                 </div>


<div class="col-md-6">
<div class="table-scrollable">
                                                    <table class="table table-bordered table-hover table-striped table-shipment-details">

<tr>                                                        

 <td class="label_new">
                                                                            <strong> Not to Pay Agent</strong>
                                                                        </td>
                                                                        <td>
                                                                            <div class="icheck-inline">
                                                                                <label>
                                                                                    <input name="export_master" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="1"/>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

<tr>
<td class="label_new"> Pieces </td>
                                                            <td> <?php echo $this->consignmentObj->getNumberPieces() ?> </td>
                                                        </tr>


<tr>
<td class="label_new"> Value </td>
                                                            <td><?php echo $this->consignmentObj->getCurrency(); ?> <?php echo $this->consignmentObj->getValue(); ?></td>
                                                        </tr>




<tr>
 <td class="label_new"> Description </td>
                                                            <td><?php echo $this->consignmentObj->getDescription(); ?></td>
                                                        </tr>

<tr>

                                                            <td class="label_new"> Product </td>
                                                            <td><?php echo "NDX" ?></td>
                                                        </tr>









                                                        

<tr>

<td>
                                                                <input type="button" class="btn btn-primary btn-sm " value="Proforma Invoice" onClick="window.open('cs_proformaInvoice.php?id=<?php echo $this->id; ?>', 'windowname', ' height=600')" />
                                                            </td>




    
                                                            <td>
                                                                <input type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#email-log-popup" title="Emails option"       value="Send Email" 
                                                                <?php
                                                                if ($this->user->getUserType() == User::USER_TYPE_WAREHOUSE) {
                                                                    echo 'disabled="disabled"';
                                                                }
                                                                ?> />
                                                            </td>
                                                        </tr>


   </table>  
                                                </div>

                                                 </div>

                                                 </div>



                                            </fieldset>



                                        </div>
                                    </div>
                                </div><!--End Account Tab -->
                                <div class="tab-pane" id="agent_tab">
                                    <div class="portlet light bordered">
<!--                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-file-text-o font-blue-madison"></i>
                                                <span class="caption-subject font-blue-madison label_new">Agent Detail</span>
                                            </div>
                                        </div>-->
                                        <div class="portlet-body">
                                            <fieldset class="margin-top-0 ">
                                                <legend><i class="fa fa-file-text-o"></i> Agent Internal Comment</legend>
                                                <div class="table-scrollable">
                                                    <table class="table table-bordered  table-striped table-advance table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th> Shipment Status </th>
                                                                <th> POD Status </th>
                                                                <th> Date </th>
                                                                <th> POD File </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    $this->consignment_status = $this->consignmentObj->getConsignmentStatus();
                                                                    $this->consignment_status = $this->consignment_status == 'received' ? 'label created' : $consignment_status;
                                                                    $this->consignment_status = $this->consignment_status == 'booked' ? 'shipped' : $consignment_status;
                                                                    echo ucwords(strtolower($this->consignment_status));
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (Permissions::checkFilePermission('update_pod')) { ?>
                                                                        <?php
                                                                        $tracking = new TrackingDataFilter();
                                                                        $tracking->addConsignmentIDFilter($this->id);
                                                                        $tracking->AddOrderByID(false);
                                                                        $trackingList = $tracking->getColumnList('status_code,pod_image, date_created');
                                                                        if (count($trackingList) > 0)
                                                                            echo ucwords(strtolower($trackingList[0]->getStatusCode()));
                                                                        if ($this->user->getUserType() != User::USER_TYPE_WAREHOUSE) {
                                                                            if ($this->consignment_status != 'invalid' || $this->consignment_status != 'valid') {
                                                                                ?>   <a href="javascript:;" title="UPDATE TRACKING POD" data-target="#tracking-pod" data-toggle="modal"><span class="glyphicon glyphicon-eye-open"></span></a>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    <?php } ?>        
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (count($trackingList) > 0) {
                                                                        echo '' . date('d-m-Y', strtotime($trackingList[0]->getDateCreated())) . '';
                                                                    } else if ($this->consignmentObj->getDateDelivered() != '' && $this->consignmentObj->getDateDelivered() != 0) {
                                                                        echo $this->consignmentObj->getDateDelivered();
                                                                    } else {
                                                                        if ($this->consignmentObj->getDateBooked() != '' && $this->consignmentObj->getDateBooked() != 0) {
                                                                            echo $this->consignmentObj->getDateBooked();
                                                                        }
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if (count($trackingList) > 0 && $trackingList[0]->getPodImage() != '') {
                                                                        echo '<a href="../pod_images/' . $trackingList[0]->getPodImage() . '">' . $trackingList[0]->getPodImage() . '</a>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"> 
                                                                    <div class="col-sm-12">
                                                                        <div class="col-sm-3">
                                                                            <input type="button" class="btn btn-primary btn-xs" value="Contact" id='btncontact' name='btncontact' onclick="contactdetails(<?php echo $this->agentid; ?>, '<?php echo $this->userAccountObj->getTelephone(); ?>');"  data-toggle="modal" data-target="#myModalvolume"/>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <?php if (Permissions::checkFilePermission('log')) { ?>
                                                                                <input type="button" class="btn btn-primary btn-xs" value="Log" onclick="displayLog(<?php echo $this->id; ?>)" data-toggle="modal" data-target="#myModalvolume"
                                                                                <?php
                                                                                if ($this->user->getUserType() == User::USER_TYPE_WAREHOUSE) {
                                                                                    echo 'disabled="disabled"';
                                                                                }
                                                                                ?>
                                                                                       />
                                                                                   <?php } ?>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <a href="#" title="audit" data-target="#audit-log" data-toggle="modal">
                                                                                <input class="btn btn-primary btn-xs" id="btn_audit" name="btn_audit" type="button" value=" History " onClick="ShowAudit('<?php echo $this->consignmentObj->getId(); ?>');">
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <input type="button" class="btn btn-primary btn-xs" value="Mail" onclick="SendEmail(<?php echo $this->id; ?>)"   data-toggle="modal" data-target="#myModalvolume"
                                                                            <?php
                                                                            if ($this->user->getUserType() == User::USER_TYPE_WAREHOUSE) {
                                                                                echo 'disabled="disabled"';
                                                                            }
                                                                            ?>
                                                                                   />
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr> 
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </fieldset>
                                            <fieldset class="margin-top-0 ">
                                                <legend><i class="fa fa-user"></i> Agent Details</legend>
                                                <div class="table-scrollable">
                                                    <table class="table table-bordered table-hover table-striped">
                                                        <tr>
                                                            <td>
                                                                <input type="button" class="btn btn-primary btn-sm" value="Agent Details" onclick="window.open('agent_details.php?agent_id=<?php echo $this->consignmentObj->getAgentid(); ?> ', '_blank');" />
                                                                <a href="#" title="Agent" data-target="#agent_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Agent"></span></a>
                                                            </td>
                                                            <td>  </td>
                                                            <th> Agent </th>
                                                            <td> <?php if (count($this->agentList) > 0) {
                                                                        echo $this->agentList->getAgentCode();
                                                                                ?> <?php echo $this->agentList->getAgentName();
                                                    }
                                                    ?> </td>
                                                            <th> Transit Time(hrs) </th>
                                                            <td>  </td>
                                                        </tr>
                                                        <tr>
                                                            <th> Del </th>
                                                            <td> <?php if (count($this->agentList) > 0) {
                                                        echo $this->agentList->getAgentCode();
                                                        ?> <?php echo $this->agentList->getAgentName();
                                                    }
                                                                            ?> </td>
                                                            <th> Mawb No </th>
                                                            <td> 
                                                                <?php
                                                                $parcelFilter = new ParcelFilter();
                                                                $parcelFilter->addFieldFilter("     p.consignment_id", $this->consignmentObj->getId());
                                                                $parcelFilterObjs = $parcelFilter->getList();
                                                                $consignmentParcelIds = [];
                                                                if (count($parcelFilterObjs) > 0) {
                                                                    foreach ($parcelFilterObjs as $parcelFilterObj) {
                                                                        $consignmentParcelIds = $parcelFilterObj->getId();
                                                                    }
                                                                }
                                                                $mawbParcelMappingFilterObjs = [];
                                                                if (count($consignmentParcelIds) > 0) {
                                                                    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                                                                    $mawbParcelMappingFilter->addJoin("mawb", "mawb.id", "mpm.mawb_id");
                                                                    $mawbParcelMappingFilter->addFilterIn("mpm.parcel_id", $consignmentParcelIds);
                                                                    $mawbParcelMappingFilterObjs = $mawbParcelMappingFilter->getList();
                                                                }
                                                                if (count($mawbParcelMappingFilterObjs) > 0) {
                                                                    foreach ($mawbParcelMappingFilterObjs as $key => $mawbParcelMappingFilterObj) {
                                                                        echo $mawbParcelMappingFilterObj->getMawbNumber();
                                                                        if ($key < count($mawbParcelMappingFilterObjs)) {
                                                                            echo ", ";
                                                                        }
                                                                    }
                                                                }
//                                                                    echo $this->consignmentObj->getMawb(); 
                                                                ?> 
                                                            </td>
                                                            <th> Item Type </th>
                                                            <td> <?php echo $this->consignmentObj->getItemType(); ?> </td>
                                                        </tr>
                                                        <tr>
                                                            <th> Delivery Instruction </th>
                                                            <td> <?php echo $this->consignmentObj->getNotes(); ?> </td>
                                                            <th> Manifest No </th>
                                                            <td>
                                                                <?php
                                                                if (count($consignmentParcelIds) > 0) {
                                                                    $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                                                                    $manifestEntityMappingFilter->addFieldFilter("        mem.manifest_entity_type", "p");
                                                                    $manifestEntityMappingFilter->addFilterIn("      mem.entity_id", $consignmentParcelIds);
                                                                    $manifestEntityMappingFilterObjs = $manifestEntityMappingFilter->getList();
                                                                    if (count($manifestEntityMappingFilterObjs) > 0) {
                                                                        foreach ($manifestEntityMappingFilterObjs as $key => $manifestEntityMappingFilterObj) {
                                                                            echo $manifestEntityMappingFilterObj->getManifestid();
                                                                            if ($key < count($manifestEntityMappingFilterObjs)) {
                                                                                echo ", ";
                                                                            }
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo $this->consignmentObj->getNotes();
                                                                }
                                                                ?>
                                                            </td>
                                                            <th> Remarks </th>
                                                            <td> <?php echo $this->consignmentObj->getNotes(); ?> </td>
                                                        </tr>
                                                        <tr>
                                                            <th> Flight No </th>
                                                            <td>  </td>
                                                            <th> Carrier Name </th>
                                                            <td>
                                                                <?php
                                                                if ($this->consignmentObj->getShipmentType() != '' && $this->consignmentObj->getShipmentType() != 'D' && $this->consignmentObj->getShipmentType() != 'C') {
                                                                    if ($this->carrier != "") {
                                                                        echo $this->carrier;
                                                                    } else
                                                                    if (count($serviceConversionList) > 0) {
                                                                        $ConversionFaction = $serviceConversionList[0]->getServiceType();
                                                                    }
                                                                } else {
                                                                    echo $this->consignmentObj->getServiceType();
                                                                }
                                                                ?>
                                                            </td>
                                                            <th> Tracking Number (AWB) </th>
                                                            <td>
                                                                <a href="tracking.php?tracking_number=<?php echo $this->consignmentObj->getAwb(); ?>" target="_blank"><?php echo $this->consignmentObj->getAwb(); ?></a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th> Custom Export Number </th>
                                                            <td>
                                                                <?php
                                                                $ConsignmentDetailsFilter = new ConsignmentDetailsFilter();
                                                                $ConsignmentDetailsFilter->addconsignmentFilter($this->consignmentObj->getId());
                                                                $custome_list = $ConsignmentDetailsFilter->getList();
                                                                if (count($custome_list) > 0) {
                                                                    echo $custome_list[0]->getCustomExportNumber();
                                                                } else {
                                                                    ?>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control input-sm" id="custom_export_number" name="custom_export_number" value="">
                                                                        <span class="input-group-btn">
                                                                            <button type="button" class="btn btn-primary btn-sm" onclick="SaveExportCustomer(<?php echo $this->consignmentObj->getId(); ?>)">Custom Export</button>
                                                                        </span>
                                                                    </div>
        <?php } ?>
                                                            </td>
                                                            <th> Value Type </th>
                                                            <td>
                                                                <div id="highlowvalueshow">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control input-sm" id="valueType" value="<?php echo $this->consignmentObj->getHvLv(); ?>" readonly="readonly">
                                                                        <span class="input-group-btn">
                                                                            <button type="button" class="btn btn-primary btn-sm" onclick="EditValueType()" >Edit</button>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div id="highlowvalueedit" style="display:none;">
                                                                    <div class="input-group">
                                                                        <select id="highlowvalue" name = "highlowvalue" class="form-control" >
                                                                            <option value="">Select Type of Value</option>
                                                                            <option value="HV" <?php
        if ($this->consignmentObj->getHvLv() == "HV") {
            echo "selected='selected'";
        }
        ?> >High Value</option>
                                                                            <option value="MV" <?php
                                                                    if ($this->consignmentObj->getHvLv() == "MV") {
                                                                        echo "selected='selected'";
                                                                    }
        ?> >Medium Value</option>
                                                                            <option value="LV" <?php
                                                                    if ($this->consignmentObj->getHvLv() == "LV") {
                                                                        echo "selected='selected'";
                                                                    }
                                                                    ?> >Low Value</option>
                                                                        </select>
                                                                        <span class="input-group-btn">
                                                                            <button type="button" class="btn btn-primary" onclick="SaveHighValue(<?php echo $this->consignmentObj->getId(); ?>)" >Save</button>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">  </td>
                                                        </tr>
                                                    </table>  
                                                </div>
                                            </fieldset>
                                            
                                            <fieldset class="margin-top-0">
                                                <legend><i class="fa fa-list"></i> Custom Documents</legend>
                                                    <div class="table-scrollable">
                                                    <table class="table table-bordered  table-striped table-advance table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td>
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
                                                                             <?php
                                                                            
                                                                            
                                                                             if($this->consignmentObj->getId() > 0){
                                                                                $invoicePath = "../_assets/paperless_invoice/";
                                                                                $fileName = md5($this->consignmentObj->getId()) . ".pdf";
                                                                                if(file_exists($invoicePath . $fileName)){
                                                                                   $invoiceFile =  $fileName;
                                                                                
                                                                                $documentName = explode(".", $invoiceFile);
                                                                                $docName = $documentName[0];
                                                                                $fileFullPath = "../_assets/paperless_invoice/" . $invoiceFile;
                                                                             ?>
                                                                            <div class="col-md-12">
                                                                                <div class="col-md-3 doc_upload_view" id="usr_doc_<?=$docName;?>">
                                                                                <div class="thumbnail">
                                                                                <img src="../images/pdf.png" style="max-width: 100%; max-height: 100px; display: block;" data-src="<?=$fileFullPath;?>">
                                                                                <div class="caption text-center" style="height: auto">
                                                                                <a target="_blank" href="<?=$fileFullPath;?>" class="btn blue btn-xs"> View </a> &nbsp;&nbsp;
                                                                                <a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="<?=$invoiceFile;?>"> Remove </a>
                                                                                <input type="hidden" name="temp_invoice_name" id="temp_invoice_name" value="<?=$invoiceFile;?>" />
                                                                                </div>
                                                                                </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12"id="send_custom_email">
                                                                                <div class="col-md-3 align-content-center">
                                                                                    <input type="button" class="btn btn-primary btn-sm" value="Send Carrier E-Mail" onclick="SendCarrierEmail('<?=$this->consignmentObj->getId()?>')" data-toggle="modal" data-target="#myModalvolume" data-original-title="" title="">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                            <?php
                                                                            }
                                                                             } ?>
                                                                        
                                                                    </div>      
                                                                </td>
                                                            </tr> 
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </fieldset>

                                        </div>    
                                    </div>
                                </div><!--End Company Tab -->
                                <div class="tab-pane" id="parcel_tab">
                                    <div class="portlet light bordered">
<!--                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-file-text-o font-blue-madison"></i>
                                                <span class="caption-subject font-blue-madison label_new">Parcel Detail</span>
                                            </div>
                                        </div>-->
                                        <div class="portlet-body">
                                            <fieldset class="margin-top-0 ">
                                                <legend><i class="fa fa-user"></i> Parcel List</legend>
                                                <div class="table-scrollable">
                                                    <table class="table table-striped table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th> # </th>
                                                                <th> Tracking Number </th>
                                                                <th> Dims </th>
                                                                <th> Weight </th>
                                                                <th> Status </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
        <?php
        $count = 1;
        foreach ($this->parcelList as $parcel) {
            ?>
                                                                <tr>
                                                                    <td> <?php echo $count; ?> </td>
                                                                    <td> <?php echo $parcel->getTrackingNumber(); ?> </td>
                                                                    <td> <?php echo $parcel->getLength() . " x " . $parcel->getWidth() . " x " . $parcel->getHeight(); ?> </td>
                                                                    <td> <?php echo $parcel->getWeight(); ?> </td>
                                                                    <td>
            <?php
            $status = Consignment::$database_status_array[$parcel->getParcelStatusCode()];
            echo (!empty($status) ? ucwords($status) : '');
            ?>
                                    <!--                                            <span class="label label-sm label-success"> Approved </span>-->
                                                                    </td>
                                                                </tr>
            <?php
            $count++;
        }
        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </fieldset>   
                                        </div>    
                                    </div>
                                </div><!--End Service Tab -->
                            </div>
                        </div>
                        <!-- END TERMS OF USE TAB -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row1">
            <div class="col-md-121">
                <div class="portlet light">
                    <div class="portlet-body">
        <?php if (Permissions::checkFilePermission('back')) { ?>
                            <input class="btn btn-danger btn-sm" id="btn_back_l" name="btn_back_l" type="button" value="back" onClick="window.location = 'shipment_list_manage.php'" >
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('label')) { ?>
                            <a href="javascript:;" onclick="ShowLabel('<?php echo $this->consignmentObj->getLabelFile(); ?>')">
                                <input class="btn btn-primary btn-sm" id="btn_label" name="btn_label" type="button" value="Label"  >
                            </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('update_pricing')) { ?>
                            <a href="javascript:;" title="Update Pricing" >
                                <input class="btn btn-primary btn-sm" id="btn_update_pricing" name="btn_update_pricing" type="button" value="Update Pricing" onClick="pricingUpdateCalculation('<?php echo $this->consignmentObj->getId(); ?>');">
                            </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('pricing') || Permissions::checkFilePermission('customer_pricing')) { ?>
                            <a href="javascript:;" title="Pricing" data-target="#pricing-invoice" data-toggle="modal">
                                <input class="btn btn-primary btn-sm" id="btn_pricing" name="btn_pricing" type="button" value="Pricing" onClick="pricingCalculation('<?php echo $this->consignmentObj->getId(); ?>', '<?php echo $this->accountIdPricing; ?>');">
                            </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('audit')) { ?>
                            
                            <a href="" class="btn btn-primary btn-sm" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="<?php echo $this->consignmentObj->getId(); ?>" 
                               data-log_name="consignment" data-toggle="modal"> Audit </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('price_audit')) { ?>
                               <a href="" class="btn btn-primary btn-sm" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="<?php echo $this->consignmentObj->getId(); ?>" 
                                    data-log_name="shipment_price" data-toggle="modal"> Price Audit
                               </a>   
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('change_account')) { ?>
                            <a href="javascript:;" title="Change Account" data-target="#change_account_modal" data-toggle="modal" class="btn btn-primary btn-sm" onclick="get_account_users()" >
                                Change Account
                            </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('edit_tracking_no')) { ?>
                            <a href="javascript:;" title="Tracking Edit" data-target="#tracking-edit" data-toggle="modal">
                                <input class="btn btn-primary btn-sm" id="btn_TrackingEdit" name="btn_TrackingEdit" type="button" value="Tracking Edit">
                            </a>
        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <form id="update_remotearea_frm" name="update_remotearea_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="remotearea_change" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Remote Area Change</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_remote_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_REMOTE" />
                            <input type="hidden" name="consignment_id_remote" id="consignment_id_remote" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Hawb No:</label> 
                                                <input type="text" name="hawb_remote" id="hawb_remote" value="<?php echo $this->consignmentObj->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Account:</label> 
                                                <input type="text" name="c_account" id="c_account" value="<?php echo $this->userAccountObj->getUserAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">City:</label>
                                                <input type="text" name="c_city" id="c_city" value="<?php echo $this->consignmentObj->getCity(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label ">Postcode:</label>
                                                <input type="text" name="Postcode" id="Postcode" value="<?php echo $this->consignmentObj->getPostcode(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Country:</label>
                                                <input type="text" name="c_country" id="c_country" value="<?php echo $this->countryObj->getName(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <?php
                                            if ($this->consignmentObj->getRemoteCharges() == '1') {
                                                $remoteCheckBox = 'checked="checked"';
                                            } else {
                                                $remoteCheckBox = '';
                                            }
                                            ?>
                                            <div class="form-group col-md-2">
                                                <label>Remote Area</label>
                                                <div class="icheck-inline margin-top-5">
                                                    <label>
                                                        <input id="remote_area_value" name="remote_area_value" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue" value="YES" <?php echo $remoteCheckBox; ?> /> YES/NO
                                                    </label>
                                                </div>
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_remote_btn" onclick="updateConsignmentRemote();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <div class="modal fade" tabindex="-1" role="dialog" id="tracking-pod" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update POD Status</h4>
                    </div>
                    <div class="modal-body">
                        <form id="update_pod_status_frm" name="update_pod_status_frm" method="post">
                            <div class="alert alert-success" style="display:none;" id="pod_status_msg"></div>
                            <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                            <input type="hidden" name="tracking_number" id="tracking_number" value="<?php echo $this->consignmentObj->getAwb(); ?>" />
                            <input type="hidden" name="tracking_id" id="tracking_id" value="<?php echo $this->consignmentObj->getAwb(); ?>" />
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label "><strong>POD Status</strong></label>
        <?php echo Ddl::generateArrayDDL('pod_status', Tracking::$oneworld_status_code, '', '', 'class="form-filter select2 form-control" ', "", "pod_status"); ?>
        <!--                                        <select id="pod_status" name="pod_status" class="form-control">
                                            <option value="">Please Select</option>
                                            <option value="booked"<?php echo ($this->consignment_status == 'booked' ? ' selected="selected"' : ''); ?>>Shipped</option>
                                            <option value="returned"<?php echo ($this->consignment_status == 'returned' ? ' selected="selected"' : ''); ?>>Returned</option>
                                            <option value="hold"<?php echo ($this->consignment_status == 'hold' ? ' selected="selected"' : ''); ?>>Hold</option>
                                            <option value="closed"<?php echo ($this->consignment_status == 'closed' ? ' selected="selected"' : ''); ?>>Closed</option>
                                            <option value="delivered"<?php echo ($this->consignment_status == 'delivered' ? ' selected="selected"' : ''); ?>>Point of Delivery (POD)</option>
                                            <option value="nopod"<?php echo ($this->consignment_status == 'nopod' ? ' selected="selected"' : ''); ?>>NO POD AVAILABLE</option>
                                            <option value="lost"<?php echo ($this->consignment_status == 'lost' ? ' selected="selected"' : ''); ?>>Lost</option>
                                            <option value="intransit"<?php echo ($this->consignment_status == 'intransit' ? ' selected="selected"' : ''); ?>>In Transit</option>
                                            <option value="remote area"<?php echo ($this->consignment_status == 'remote area' ? ' selected="selected"' : ''); ?>>Remote Area</option>
                                            <option value="out for delivery"<?php echo ($this->consignment_status == 'out for delivery' ? ' selected="selected"' : ''); ?>>OUT FOR DELIVERY</option>
                                            <option value="held - awaiting duty payment"<?php echo ($this->consignment_status == 'held - awaiting duty payment' ? ' selected="selected"' : ''); ?>>HELD -AWAITING DUTY PAYMENT</option>
                                            <option value="bad address need better details"<?php echo ($this->consignment_status == 'bad address need better details' ? ' selected="selected"' : ''); ?>>BAD ADDRESS NEED BETTER DETAILS</option>
                                            <option value="refused"<?php echo ($this->consignment_status == 'refused' ? ' selected="selected"' : ''); ?>>REFUSED</option>
                                            <option value="dangerous goods"<?php echo ($this->consignment_status == 'dangerous goods' ? ' selected="selected"' : ''); ?>>DANGEROUS GOODS</option>
                                            <option value="damage goods"<?php echo ($this->consignment_status == 'damage goods' ? ' selected="selected"' : ''); ?>>DAMAGE GOODS</option>
                                        </select>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row pod_component_container">
                                <div class="col-md-6">
                                    <div class="form-group" id="pod_name_container">
                                        <label class="control-label "><strong>Name</strong></label>
                                        <input type="text" name="pod_name" id="pod_name" value="" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label><strong>POD Date</strong></label>
                                    <div class="input-group margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        <input type="text" class="form-control input-sm" readonly name="pod_date" id="pod_date" placeholder="" value="" >                                  
                                    </div>
                                </div>
                            </div>
                            <div class="row pod_component_container">
                                <div class="col-md-12">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <label> <strong>Image</strong> </label>
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="pod_image" id="pod_image"> 
                                                </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>                               
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label>
                                                <input id="send_pod_status_mail" name="send_pod_status_mail" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-blue" value="1"/> Send POD Status Email
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label " for="calc_chargeable_weight"><strong>Description</strong></label>
                                        <input type="text" name="pod_description" id="pod_description" value="" placeholder="Please enter description here" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr style="margin: 0px;" />
                        <div class="row">
                            <div class="col-md-12">
                                <h4>History</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Edit</th>
                                            <th>Date Time</th>
                                            <th>Track Point</th>
                                            <th>Event Content</th>
                                            <th>Other</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history_content">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="update_pod_status_btn">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="account-details" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Customer Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover">

                                    <tr>
                                        <th width="25%">Account Number</th>
                                        <td width="25%" id="ad-account"></td>
                                        <th width="25%">Company </th>
                                        <td  width="25%" id="ad-company"></td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td id="ad-fullname"></td>
                                        <th>Owner </th>
                                        <td  id="ad-owner"></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td  id="ad-email"></td>
                                        <th>Alternative Email </th>
                                        <td  id="ad-alternativeemial"></td>
                                    </tr>
                                    <tr>
                                        <th>Return Address</th>
                                        <td id="ad-returnaddress"></td>
                                        <th>Billing Address </th>
                                        <td id="ad-billingaddress"></td>
                                    </tr>
                                    <tr>
                                        <th>Telephone</th>
                                        <td id="ad-telephone"></td>
                                        <th>Country </th>
                                        <td id="ad-country"></td>
                                    </tr>
                                    <tbody id="history_content">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <!-- /.modal -->
        <div class="modal fade" id="myModalvolume" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="loadcontent_title"></h4>
                    </div>
                    <div class="modal-body">
                        <div id="loadcontent"></div> 
                    </div>
                    <div class="modal-footer" id="footer_links">

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="email-log-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Emails</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table  table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <td class="col-md-2">
                                            </td>
                                            <td class="col-md-4">
                                                <input type="hidden" id="updatedweight" value="0" />
                                                <input id="btnresendweight" class="btn btn-danger btn-block" type="button" onclick="resendWeightEmail(<?php echo $this->consignmentObj->getId(); ?>, <?php echo $this->consignmentObj->getUpdateWeight(); ?>, <?php echo $this->consignmentObj->getWeight(); ?>)" name="btnresendweight" title="Resend Email" value="Weight Email">
                                            </td>
                                            <td class="col-md-4">
                                                <input type="hidden" id="updatedvolweight" value="0" />
                                                <input id="btnresendvolume" class="btn btn-primary btn-sm btn-block" type="button" onclick="resendVolumeEmail(<?php echo $this->consignmentObj->getId(); ?>)" name="btnresendvolume" value="Volumn Weight Email">
                                            </td>
                                            <td class="col-md-2">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <form id="update_agent_frm" name="update_agent_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="agent_change" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Agent Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_agent_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_AGENT" />
                            <input type="hidden" name="consignment_id_agent" id="consignment_id_agent" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label">HawbNo:</label> 
                                                <input type="text" name="hawb_remote" id="hawb_remote" value="<?php echo $this->consignmentObj->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Account:</label> 
                                                <input type="text" name="c_account" id="c_account" value="<?php echo $this->userAccountObj->getUserAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Service:</label>
                                                <input type="text" name="service" id="service" value="<?php echo $this->serviceType; ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label">Agent Assigned:</label>
                                                <?php
                                                if ($this->agentid != '' || $this->agentid > 0) {
                                                    $AgentDataFilter = new AgentDataFilter();
                                                    $AgentDataFilter->addFieldFilter("id", $this->agentid);
                                                    $agentList = $AgentDataFilter->getColumnList('agent_code, agent_name');
                                                    if (count($agentList) > 0) {
                                                        $agentCode = $agentList[0]->getAgentCode();
                                                        $agentName = $agentList[0]->getAgentName();
                                                    }
                                                }
                                                ?>
                                                <input type="text" name="agent" id="agent" value="<?php echo $agentName . " " . $agentCode; ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="control-label">Agent</label><br />
                                                <select name="agent_area_id" id="agent_area_id" class="form-control">
                                                    <option value="">Select Agent</option>
                                                    <?php
                                                    $agentDataFilter = new AgentDataFilter();
                                                    $agentDataFilter->addFieldFilter('active', '1');
                                                    $AgentData = $agentDataFilter->getColumnList('agent_code,agent_name');
                                                    if (count($AgentData) > 0) {
                                                        foreach ($AgentData as $agent) {
                                                            echo '<option value="' . $agent->getId() . '">' . $agent->getAgentName() . ' [' . $agent->getAgentCode() . ']</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="update_agent_btn" onclick="updateConsignmentAgent();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <form id="update_change_account_frm" name="update_change_account_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="change_account_modal" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Change Account Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_change_account_msg">Enter New Account</div>
                            <input type="hidden" name="action_account" id="action_account" value="UPDATE_CHANGE_ACCOUNT" />
                            <input type="hidden" name="consignment_id_account" id="consignment_id_account" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label class="control-label">HawbNo:</label> 
                                                <input type="text" name="hawb_account" id="hawb_account" value="<?php echo $this->consignmentObj->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="control-label">Current Account:</label> 
                                                <input type="text" name="old_account" id="old_account" value="<?php echo $this->userAccountObj->getUserAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="control-label">New Account</label>
                                                <?php
                                                $accountParentId = 0;
                                                if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                                    $accountParentId = $this->user->getUserAccountId();
                                                }
                                                $allowedLevel = 0;
                                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                                    $allowedLevel = 1;
                                                }
                                                echo Ddl::showTreeDropdown('new_account', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), '', "", 'class="form-control select2" onchange="get_account_users()"', "", "user_account","","","",true,$allowedLevel);
                                                ?>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="control-label">New User</label>
                                                <select id="new_user_id" name="new_user_id"  class="form-control"  rel="tooltip" title="User">
                                                    <option value="">Select User</option>
                                                </select>
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="update_change_account_btn" onclick="updateConsignmentAccount();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <!-- AUDIT MODEL -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>User</th>
                                            <th>Ip Address</th>
                                            <th>Date Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="audit_content">
                                    </tbody>
                                </table>
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <!-- AUDIT MODEL Details -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log-details" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="AncillaryChargesDetailsTxt" id="AncillaryChargesDetailsTxt" value="" />
                            <div class="col-md-12" id="audit_content_details">
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <!-- AUDIT MODEL Details -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log-details-ancillary" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Ancillary Charges Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="audit_content_details_ancillary">
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <form id="update_pricing_details_frm" name="update_pricing_details_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pricing-invoice" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Pricing Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="pricing_details_msg">Updated successfully</div>
                            <input type="hidden" name="action" id="action" value="UPDATE_PRICING_DETAILS" />
                            <input type="hidden" name="invoice_detail_id" id="invoice_detail_id" value="" />
                            <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                            <input type="hidden" name="invoice_no" id="invoice_no" value="" />

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Customer Details</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table table-condensed table-striped table-bordered">
                                                        <tr>
                                                            <th> Account </th>
                                                            <td> <?php echo $this->userAccountObj->getUserAccount(); ?> </td>
                                                            <th> Tracking No </th>
                                                            <td> <?php echo $this->consignmentObj->getAwb(); ?> </td>
                                                            <th> Country </th>
                                                            <td> <?php echo $this->countryObj->getName(); ?> </td>                                                           
                                                        </tr>
                                                        <tr>
                                                            <th> Service </th>
                                                            <td> 
        <?php
        $service = new Services($this->consignmentObj->getServiceId());
        echo $service->getName() . " [ " . $service->getCode() . " ]";
        ?> 
                                                            </td>
                                                            <th> Order No. (Hawb) </th>
                                                            <td> <?php echo $this->consignmentObj->getHawb(); ?> </td>
                                                            <th> City </th>
                                                            <td> <?php echo $this->consignmentObj->getCity(); ?> </td>                                                          
                                                        </tr>
                                                        <tr>
                                                            <th> Invoice Number </th>
                                                            <td> <span id="invoice_no_show"><?php echo "" ?></span> </td>
                                                            <th> Weight </th>
                                                            <td>
                                                                <?php
                                                                if ($this->consignmentObj->getWeight() > $this->consignmentObj->getVolWeight()) {
                                                                    echo $this->consignmentObj->getWeight();
                                                                } else {
                                                                    echo $this->consignmentObj->getVolWeight();
                                                                }
                                                                ?>
                                                            </td>
                                                            <th> Pieces </th>
                                                            <td> <?php echo $this->consignmentObj->getNumberPieces(); ?> </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                if (Permissions::checkFilePermission('customer_pricing')) {
                                    $Col6to12 = '12';
                                    $Col4to6 = '4';
                                    $displayAreadCs = 'style="display:none;"';
                                } else {
                                    $Col6to12 = '4';
                                    $Col4to6 = '4';
                                    $displayAreadCs = '';
                                }
                                $consignmentChargesTypesFilter = new ConsignmentChargesTypesFilter();
                                $consignmentChargesTypesFilter->addFieldFilter('is_extra_charge', 0);
                                 $consignmentChargesTypesFilter->addFieldFilter('    is_delete', 0);
                                $consignmentChargesTypesFilterObj = $consignmentChargesTypesFilter->getList();
                                ?>
                                <div class="col-md-<?php echo $Col6to12; ?>">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Customer Charges</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php $customerTotal = 0; ?>
                                            <?php foreach ($consignmentChargesTypesFilterObj as $obj) { ?>                                                
                                                <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "customer") { ?>
                                                    <?php
                                                    $consignmentCustomerCharges = new ConsignmentChargesFilter();
                                                    $consignmentCustomerCharges->addFieldFilter('   consignment_id', $this->id);
                                                    $consignmentCustomerCharges->addFieldFilter('charge_type_id', $obj->getId());
                                                    $consignmentCustomerCharges->addFieldFilter('cost_type', 'customer');
                                                    $consignmentCustomerCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                                    $consignmentCustomerChargesObj = $consignmentCustomerCharges->getColumnList('cost,added_date');
                                                    $value = '';
                                                    $lastModefiedDate = "";
                                                    if (count($consignmentCustomerChargesObj) > 0) {
                                                        $lastModefiedDate = date('d-m-Y', $consignmentCustomerChargesObj[0]->getAddedDate());
                                                        $value = $consignmentCustomerChargesObj[0]->getCost();
                                                        $customerTotal = $customerTotal + $value;
                                                    }
                                                    ?>
                                                    <div class="col-md-6">
                                                        <label><?php echo $obj->getTitle(); ?></label>
                                                        <div class="form-group1">
                                                            <div>
                                                                <?php $calculationClass = (preg_replace('/\s+/', '_', strtolower($obj->getTitle())) == 'reference')?'':'customer_charges'?>
                                                                <input class="form-control <?php echo $calculationClass;?>" id="customer_<?php echo preg_replace('/[\s^\/]+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[customer][<?php echo $obj->getId(); ?>]" type="text" value="<?php echo $value; ?>" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
            <?php } ?>
        <?php } ?>
                                        </div>                                        
                                    </fieldset>
                                </div>
                                <div class="col-md-<?php echo $Col6to12; ?>" <?php echo $displayAreadCs; ?> style="background-color:#eee;">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Agent Cost</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php
                                            $reference = "";
                                            $consignmentAgentReferenceCharges = new ConsignmentChargesFilter();
                                            $consignmentAgentReferenceCharges->addFieldFilter('    consignment_id', $this->id);
                                            $consignmentAgentReferenceCharges->addFieldFilter('    cost_type', 'agent');
                                            $consignmentAgentReferenceCharges->addFieldFilter('    is_delete', 0);
                                            $consignmentAgentReferenceCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                            $consignmentAgentReferenceChargesObj = $consignmentAgentReferenceCharges->getColumnList('changes_reference');
                                            if (count($consignmentAgentReferenceChargesObj) > 0) {
                                                $reference = $consignmentAgentReferenceChargesObj[0]->getChangesReference();
                                            }
                                            ?>
                                            <div class="col-md-6">
                                                <label class="label-account">Reference</label>
                                                <div class="form-group1">
                                                    <div>
                                                        <input class="form-control" id="agent_reference" name="reference" type="text" value="<?php echo $reference; ?>" rel="tooltip" data-original-title="Reference">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $agentTotal = 0; ?>
                                            <?php foreach ($consignmentChargesTypesFilterObj as $obj) { ?>
                                                <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "agent") { ?>    
                                                    <?php
                                                    $agentValue = '';
                                                    $consignmentAgentCharges = new ConsignmentChargesFilter();
                                                    $consignmentAgentCharges->addFieldFilter('   consignment_id', $this->id);
                                                    
                                                    $consignmentAgentCharges->addFieldFilter('charge_type_id', $obj->getId());
                                                    $consignmentAgentCharges->addFieldFilter('cost_type', 'agent');
                                                    $consignmentAgentCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                                    $consignmentAgentChargesObj = $consignmentAgentCharges->getColumnList('cost');
                                                    if (count($consignmentAgentChargesObj) > 0) {
                                                        $agentValue = $consignmentAgentChargesObj[0]->getCost();
                                                        $agentTotal = $agentTotal + $agentValue;
                                                    }
                                                    ?>
                                                    <div class="col-md-6">
                                                        <label><?php echo $obj->getTitle(); ?></label>
                                                        <div class="form-group1">
                                                            <div>
                                                                <?php $calculationClass = (preg_replace('/\s+/', '_', strtolower($obj->getTitle())) == 'reference')?'':'agent_charges'?>
                                                                <input class="form-control <?php echo $calculationClass ;?>" id="agent_<?php echo preg_replace('/\s+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[agent][<?php echo $obj->getId(); ?>]" type="text" value="<?php echo $agentValue; ?>" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
            <?php } ?>
        <?php } ?>
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-md-<?php echo $Col6to12; ?>" <?php echo $displayAreadCs; ?>>
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Purchase Invoices</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php
                                            $reference = "";
                                            $consignmentAgentReferenceCharges = new ConsignmentChargesFilter();
                                            $consignmentAgentReferenceCharges->addFieldFilter('    consignment_id', $this->id);
                                            $consignmentAgentReferenceCharges->addFieldFilter('    is_delete', 0);
                                            $consignmentAgentReferenceCharges->addFieldFilter('    cost_type', 'purchase_invoice');
                                            $consignmentAgentReferenceCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."') ");
                                            $consignmentAgentReferenceChargesObj = $consignmentAgentReferenceCharges->getColumnList('changes_reference');
                                            if (count($consignmentAgentReferenceChargesObj) > 0) {
                                                foreach($consignmentAgentReferenceChargesObj as $consignmentAgentReferenceCharge) {
//                                                    $reference = $consignmentAgentReferenceChargesObj[0]->getChangesReference();
                                                    if($consignmentAgentReferenceCharge->getChangesReference() != "") {
                                                        $reference = $consignmentAgentReferenceCharge->getChangesReference();
                                                    }
                                                }
                                            }
                                            ?>
                                            
                                            <?php $agentTotal = 0; ?>
                                            <?php
                                            $purchase_invoice_reference = [];
                                            foreach ($consignmentChargesTypesFilterObj as $obj) { ?>
                                                <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "agent") { ?>    
                                                    <?php
                                                    $purchaseInvoiceValue = '';
                                                    $consignmentpurchaseInvoiceCharges = new ConsignmentChargesFilter();
                                                    $consignmentpurchaseInvoiceCharges->addFieldFilter('   consignment_id', $this->id);
                                                    $consignmentpurchaseInvoiceCharges->addFieldFilter('charge_type_id', $obj->getId());
                                                    $consignmentpurchaseInvoiceCharges->addFieldFilter('cost_type', 'purchase_invoice');
                                                    $consignmentpurchaseInvoiceCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                                    $consignmentpurchaseInvoiceChargesObj = $consignmentpurchaseInvoiceCharges->getColumnList('cost,changes_reference');
                                                    if (count($consignmentpurchaseInvoiceChargesObj) > 0) {
                                                        $purchaseInvoiceValue = $consignmentpurchaseInvoiceChargesObj[0]->getCost();
                                                        $purchaseInvoiceValue = $consignmentpurchaseInvoiceChargesObj[0]->getCost();
                                                        $purchaseInvoiceTotal = $purchaseInvoiceTotal + $purchaseInvoiceValue;
                                                        if(!in_array($consignmentpurchaseInvoiceChargesObj[0]->getChangesReference(), $purchase_invoice_reference)){
                                                            $purchase_invoice_reference[] = $consignmentpurchaseInvoiceChargesObj[0]->getChangesReference();
                                                        }
                                                    }
                                                    ?>
                                                    <div class="col-md-6">
                                                        <label><?php echo $obj->getTitle(); ?></label>
                                                        <div class="form-group1">
                                                            <div>
                                                                <?php $calculationClass = (preg_replace('/\s+/', '_', strtolower($obj->getTitle())) == 'reference')?'':'purchase_invoice_charges'?>
                                                                <input class="form-control <?php echo $calculationClass;?>" id="purchase_invoice_<?php echo preg_replace('/\s+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[purchase_invoice][<?php echo $obj->getId(); ?>]" type="text" value="<?php echo $purchaseInvoiceValue; ?>" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
            <?php } ?>
        <?php }
        ?>
                                            <div class="col-md-6">
                                                <label class="label-account">Reference</label>
                                                <div class="form-group1">
                                                    <div>
                                                        <input class="form-control" id="purchase_invoice_reference" name="purchase_invoice_reference" type="text" value="<?php echo implode(",",$purchase_invoice_reference); ?>" rel="tooltip" data-original-title="Reference">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div  class="col-md-<?php echo $Col6to12; ?>" style="background-color:#eee;">
                                            <?php
                                            $customerExtraValue = 0;
                                            $customerExtraTotal = 0;
                                            $consignmentExtraCustomerCharges = new ConsignmentChargesFilter();
                                            $consignmentExtraCustomerCharges->addConsignmentChargesTypeJoin();
                                            $consignmentExtraCustomerCharges->addFieldFilter('    cc.consignment_id', $this->id);
                                            $consignmentExtraCustomerCharges->addFieldFilter('      cc.cost_type', 'customer');
                                            $consignmentExtraCustomerCharges->addFilter('      cc.charge_type_id <> 28');
                                            $consignmentExtraCustomerCharges->addFieldFilter('      cct.is_extra_charge', 1);
                                            $consignmentExtraCustomerCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                            $consignmentExtraCustomerChargesObj = $consignmentExtraCustomerCharges->getColumnList('cc.cost,cc.agent_id,cc.charge_type_id,cc.description,cc.account_id,cc.invoice_id');
                                            if (count($consignmentExtraCustomerChargesObj) > 0) {
                                                foreach ($consignmentExtraCustomerChargesObj as $obj) {
                                                    $customerExtraValue = $obj->getCost();
                                                    $customerExtraTotal = $customerExtraTotal + $customerExtraValue;
                                                    $customerTotal = $customerTotal + $customerExtraValue;
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="control-label">Discount</label>
                                                    <div class="form-group1">
                                                        <input type="text" name="consignment_price[customer][discount]" id="customer_discount" value="" class="form-control input-sm" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Extra</label>
                                                    <div class="form-group1">
                                                        <input class="form-control customer_charges" id="customer_extra" name="consignment_price[customer][extra]" type="text" value="<?php echo $customerExtraTotal; ?>" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Total</label>
                                                    <div class="form-group1">
                                                        <input class="form-control" id="customer_total" name="consignment_price[customer][total]" type="text" value="<?php echo $customerTotal; ?>" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row"><div class="col-md-12">*VAT Charges are not displayed here.</div></div>
                                        </div>
                                        <div  class="col-md-<?php echo $Col6to12; ?>" <?php echo $displayAreadCs; ?> style="background-color:#eee;">
                                            <?php
                                            $agentExtraValue = 0;
                                            $agentExtraTotal = 0;
                                            $consignmentExtraAgentCharges = new ConsignmentChargesFilter();
                                            $consignmentExtraAgentCharges->addConsignmentChargesTypeJoin();
                                            $consignmentExtraAgentCharges->addFieldFilter('    cc.consignment_id', $this->id);
                                            $consignmentExtraAgentCharges->addFieldFilter('         cc.cost_type', 'agent');
                                            $consignmentExtraAgentCharges->addFieldFilter('         cct.is_extra_charge', 1);
                                            $consignmentExtraAgentCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                            $consignmentExtraAgentChargesObj = $consignmentExtraAgentCharges->getColumnList('cc.cost,cc.agent_id,cc.charge_type_id,cc.description,cc.account_id,cc.invoice_id');
                                            if (count($consignmentExtraAgentChargesObj) > 0) {
                                                foreach ($consignmentExtraAgentChargesObj as $obj) {
                                                    $agentExtraValue = $obj->getCost();
                                                    $agentExtraTotal = $agentExtraTotal + $agentExtraValue;
                                                    $agentTotal = $agentTotal + $agentExtraValue;
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Extra</label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control agent_charges" id="agent_extra" name="consignment_price[agent][extra]" type="text" value="<?php echo $agentExtraTotal; ?>" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Total</label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control" id="agent_total" name="consignment_price[agent][total]" type="text" value="<?php echo $agentTotal; ?>" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div  class="col-md-<?php echo $Col6to12; ?>" <?php echo $displayAreadCs; ?> style="background-color:#eee;" >
                                            <?php
                                            $purchaseInvoiceExtraValue = 0;
                                            $purchaseInvoiceExtraTotal = 0;
                                            $consignmentExtraPurchaseInvoiceCharges = new ConsignmentChargesFilter();
                                            $consignmentExtraPurchaseInvoiceCharges->addConsignmentChargesTypeJoin();
                                            $consignmentExtraPurchaseInvoiceCharges->addFieldFilter('    cc.consignment_id', $this->id);
                                            $consignmentExtraPurchaseInvoiceCharges->addFieldFilter('         cc.cost_type', 'purchase_invoice');
                                            $consignmentExtraPurchaseInvoiceCharges->addFieldFilter('         cct.is_extra_charge', 1);
                                            $consignmentExtraPurchaseInvoiceCharges->addFilter("account_id in ( select id from user_account where parentid = '".$this->user->getUserAccountId()."' ");
                                            $consignmentExtraPurchaseInvoiceChargesObj = $consignmentExtraPurchaseInvoiceCharges->getColumnList('cc.cost,cc.agent_id,cc.charge_type_id,cc.description,cc.account_id,cc.invoice_id');
                                            if (count($consignmentExtraPurchaseInvoiceChargesObj) > 0) {
                                                foreach ($consignmentExtraPurchaseInvoiceChargesObj as $obj) {
                                                    $purchaseInvoiceExtraValue = $obj->getCost();
                                                    $purchaseInvoiceExtraTotal = $purchaseInvoiceExtraTotal + $purchaseInvoiceExtraValue;
                                                    $purchaseInvoiceTotal = $purchaseInvoiceTotal + $purchaseInvoiceExtraValue;
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Extra</label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control purchase_invoice_charges" id="purchase_invoice_extra" name="consignment_price[purchase_invoice][extra]" type="text" value="<?php echo $purchaseInvoiceExtraTotal; ?>" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Total</label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control" id="purchase_invoice_total" name="consignment_price[purchase_invoice][total]" type="text" value="<?php echo $purchaseInvoiceTotal; ?>" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>   
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-<?php echo $Col4to6; ?>"  <?php echo $displayAreadCs; ?>>
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Margin</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control input-sm" name="margin" id="margin" value="" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-<?php echo $Col4to6; ?>">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Tariff Name</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <input type="text" class="form-control" name="tariff_name" id="tariff_name" value="" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-<?php echo $Col4to6; ?>">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="caption margin-bottom-10 block"> 
                                                    <span class="caption-subject label_new">Last Modified</span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <input type="text" class="form-control" name="added_by" id="added_by" value="<?php echo $lastModefiedDate; ?>" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-warning extras" data-type="agent"  <?php echo $displayAreadCs; ?> >Agent Extras</button>
                            <button type="button" class="btn btn-success extras" data-type="customer">Customer Extras</button>
                            <button type="button" class="btn btn-primary btn-sm" id="update_pricing_details_btn" onclick="updatePricingDetails();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

            <!-- /.modal --> 
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pricing-extra">
                <input type="hidden" id="hidden_extra_type" value="" />
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Agent</label>
                                        <select name="agent_id" id="agent_id" class="form-control">
                                            <option value="">Select Agent</option>
                                            <?php
                                            $agentDataFilter = new AgentDataFilter();
                                            $agentDataFilter->addFieldFilter('active', '1');
                                            $agentDataFilter->AddOrderBy('agent_code', 'asc');
                                            $AgentData = $agentDataFilter->getColumnList('agent_code,agent_name');

                                            if (count($AgentData) > 0) {
                                                foreach ($AgentData as $agent) {
                                                    echo '<option value="' . $agent->getId() . '">' . $agent->getAgentCode() . '[' . $agent->getAgentName() . ']</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Charge Type</label>
                                        <select name="charge_type" id="charge_type" class="form-control">
                                            <option value="">Select Type</option>
                                            <?php
                                            $consignmentChargesTypesFilterObj = new ConsignmentChargesTypesFilter();
                                            $consignmentChargesTypesFilterObj->addFieldFilter('is_extra_charge', 1);
                                            $Obj = $consignmentChargesTypesFilterObj->getList();
                                            if (count($Obj) > 0) {
                                                foreach ($Obj as $chargeType) {
                                                    echo '<option value="' . $chargeType->getId() . '">' . $chargeType->getTitle() . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Description</label>
                                        <input type="text" name="description" id="description" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Total</label>
                                        <input type="text" name="extra_total" id="extra_total" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label><br />
                                        <button type="button" id="btn_add_extra_charge" class="btn btn-success btn-block btn-sm"> Add </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 extras_container" id="customer_extras_container">                                    
                                    <table class="table table-condensed table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customer_extras_body">
                                            <?php
                                            if (count($consignmentExtraCustomerChargesObj) > 0) {
                                                foreach ($consignmentExtraCustomerChargesObj as $key => $obj) {
                                                    $customerExtraValue = $obj->getCost();
                                                    $customerAgentId = $obj->getAgentId();
                                                    $customerChargeTypeId = $obj->getChargeTypeId();
                                                    $customerDescription = $obj->getDescription();
                                                    $agentName = "";
                                                    if ($customerAgentId > 0) {
                                                        $agentObj = new AgentData($customerAgentId);
                                                        $agentName = $agentObj->getAgentCode() . ' [' . $agentObj->getAgentName() . ']';
                                                    }
                                                    $chargesTypesFilterObj = new ConsignmentChargesTypes($customerChargeTypeId);
                                                    $keyId = $key + 1;
                                                    ?>
                                                    <tr id="customer_row_<?php echo $keyId; ?>">
                                                        <td><?php echo $agentName; ?></td>
                                                        <td><?php echo $chargesTypesFilterObj->getTitle(); ?></td>
                                                        <td><?php echo $customerDescription; ?></td>
                                                        <td><?php echo $customerExtraValue; ?></td>
                                                        <td width="115px;">
                                                            <input type="hidden" id="customer_agent_id_<?php echo $keyId; ?>" name="customer_agent_id[]" value="<?php echo count($agentObj) ? $agentObj->getId() : 0; ?>">
                                                            <input type="hidden" id="customer_charge_type_id_<?php echo $keyId; ?>" name="customer_charge_type_id[]" value="<?php echo $chargesTypesFilterObj->getId(); ?>">
                                                            <input type="hidden" id="customer_description_<?php echo $keyId; ?>" name="customer_description[]" value="<?php echo $customerDescription; ?>">
                                                            <input type="hidden" id="customer_extra_total_<?php echo $keyId; ?>" name="customer_extra_total[]" value="<?php echo $customerExtraValue; ?>">
                                                            <button type="button" class="btn btn-danger btn-sm remove_extra" data-type="customer" data-row="customer_row_1">&nbsp;<i class="fa fa-trash-o"></i> Del&nbsp;</button>
                                                        </td>
                                                    </tr>
                <?php
            }
        }
        ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" id="customer_totals_extras" value="<?php echo count($consignmentExtraCustomerChargesObj) ?>" />
                                </div>
                                <div class="col-md-12 extras_container" id="agent_extras_container">
                                    <table class="table table-condensed table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agent_extras_body">
                                            <?php
                                            if (count($consignmentExtraAgentChargesObj) > 0) {
                                                foreach ($consignmentExtraAgentChargesObj as $key => $obj) {
                                                    $agentExtraValue = $obj->getCost();
                                                    $agentAgentId = $obj->getAgentId();
                                                    $agentChargeTypeId = $obj->getChargeTypeId();
                                                    $agentDescription = $obj->getDescription();
                                                    $agentName = "";
                                                    if ($agentAgentId > 0) {
                                                        $agentObj = new AgentData($agentAgentId);
                                                        $agentName = $agentObj->getAgentCode() . ' [' . $agentObj->getAgentName() . ']';
                                                    }
                                                    $chargesTypesFilterObj = new ConsignmentChargesTypes($agentChargeTypeId);
                                                    $keyId = $key + 1;
                                                    ?>
                                                    <tr id="agent_row_<?php echo $keyId; ?>">
                                                        <td><?php echo $agentName; ?></td>
                                                        <td><?php echo $chargesTypesFilterObj->getTitle(); ?></td>
                                                        <td><?php echo $agentDescription ?></td>
                                                        <td><?php echo $agentExtraValue; ?></td>
                                                        <td width="115px;">
                                                            <input type="hidden" id="agent_agent_id_<?php echo $keyId; ?>" name="agent_agent_id[]" value="<?php echo count($agentObj) ? $agentObj->getId() : 0; ?>">
                                                            <input type="hidden" id="agent_charge_type_id_<?php echo $keyId; ?>" name="agent_charge_type_id[]" value="<?php echo $chargesTypesFilterObj->getId(); ?>">
                                                            <input type="hidden" id="agent_description_<?php echo $keyId; ?>" name="agent_description[]" value="<?php echo $agentDescription ?>">
                                                            <input type="hidden" id="agent_extra_total_<?php echo $keyId; ?>" name="agent_extra_total[]" value="<?php echo $agentExtraValue; ?>">
                                                            <button type="button" class="btn btn-danger btn-sm remove_extra" data-type="agent" data-row="agent_row_1">&nbsp;<i class="fa fa-trash-o"></i> Del&nbsp;</button>
                                                        </td>
                                                    </tr>
                <?php
            }
        }
        ?>
                                        </tbody>
                                        <input type="hidden" id="agent_totals_extras" value="<?php echo count($consignmentExtraAgentChargesObj) ?>" />
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form id="update_tracking_frm" name="update_tracking_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="tracking-edit" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Tracking Edit</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_hawb_msg">Enter New Tracking</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_TRACKING" />
                            <input type="hidden" name="consignment_id_tracking" id="consignment_id_tracking" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label">Current Tracking Number:</label> 
                                                <input type="text" name="tracking_remote" id="tracking_remote" value="<?php echo $this->consignmentObj->getAwb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label">New Tracking Number:</label>
                                                <input type="text" name="tracking_new" id="tracking_new" value="" class="form-control" />
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="update_tracking_btn" onclick="updateConsignmentTracking();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
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

    public function renderHead() {
        ?>
        <style type="text/css">

         
            td { width: 50% }

            table { border: 1px solid #333 !important; border-collapse: inherit !important; }

           

        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
