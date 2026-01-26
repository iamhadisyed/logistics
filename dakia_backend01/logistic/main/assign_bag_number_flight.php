<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $consignment_filter = "";
    private $consignmentChargesTypesObj = "";
    private $search_result = 1;
    private $agentFilterObj = array();

    protected function init()
    {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Assign Bag to flight"
        );
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            header("Location: index.php");
        }

        $loginSubaccount = new UserAccountFilter();
        $loginSubaccount->addFilter(' parentid = ' . $this->user->getUserAccountId());
        $loginSubaccountFilter = $loginSubaccount->getColumnList("id");
        if (count($loginSubaccountFilter) > 0) {
            foreach ($loginSubaccountFilter as $loginSubaccountData)
                $this->sub_account_array[] = $loginSubaccountData->getId();
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_parcel_to_bag") {
            //TODO:// do tracking data work here
            $tracking_number = $this->form_vars['tracking_number'];
            $flight_number = $this->form_vars['flight_number'];
            if (empty($flight_number)) {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Please select any flight.";
                echo json_encode($outputArray);
                die;
            }

            if (empty($tracking_number)) {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Please Enter Tracking Number.";
                echo json_encode($outputArray);
                die;
            }

            $parcel = new ParcelFilter();
            $parcel->addFieldFilter("tracking_number", $tracking_number);
            $parcel = $parcel->getList();

            if (empty($parcel)) {
                $outputArray['status'] = "error";
                $outputArray['message'] = "No data Found";
                echo json_encode($outputArray);
                die;
            }

            $parcel = $parcel[0];
            $consignmnetData = new Consignment($parcel->getConsignmentId());
            $bagData = new BaggingFilter();
            $bagData->addFieldFilter('    service', $consignmnetData->getServiceId());
            $bagData->addFieldFilter('user_id', $this->user->getId());
            $bagData->addFieldFilter('is_closed', 0);
            $bagData->addFilter(' AND weight < 30.000');
            $bagData = $bagData->getList();
            if(empty($bagData)) {
                $flightInfo = new FlightInfo($flight_number);
                $bagNumber = "FMB" . substr(time(), 2);
                $addBag = new Bagging();
                $addBag->setBagnumber($bagNumber);
                $addBag->setDateCreated(date('Y-m-d H:i:s', time()));
                $addBag->setUserId($this->user->getId());
                $addBag->setBagStatus(1);
                $addBag->setDateUpdated(date('Y-m-d H:i:s', time()));
                $addBag->setService(date('Y-m-d H:i:s', time()));
            } else {
                $bagData = $bagData[0];
            }

        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_flight_information") {
            $outputArray = [];
//            $this->consignment_filter = new ConsignmentFilter();
            $mawb = new Mawb();
            $sessionUser = SessionManager::getUser();
            //TODO://mawb duplicate
            $mawb->setMawbNumber($this->form_vars['mawb']);
            $mawb->setmawbSourceCountryId($this->form_vars['ship_from']);
            $mawb->setmawbdestinationCountryId($this->form_vars['ship_from']);
            $mawb->setIsActive(1);
            $mawb->setAddedBy($sessionUser->getId());
            $mawb->setAddedDate(date("Y-m-d H:i:s", time()));
            $mawb->setUpdatedDate(date("Y-m-d H:i:s", time()));
            $mawb->save();

            $flightInfo = new FlightInfo();
            $flightInfo->setShippersName($this->form_vars['shipper_name']);
            $flightInfo->setShippersAddressline1($this->form_vars['shipper_address_line1']);
            $flightInfo->setShippersAddressline2($this->form_vars['shipper_address_line2']);
            $flightInfo->setAddressline1($this->form_vars['destination_addressline1']);
            $flightInfo->setAddressline2($this->form_vars['destination_addressline2']);
            $flightInfo->setDestinationCountryId($this->form_vars['shipped_to']);
            $flightInfo->setCountryId($this->form_vars['ship_from']);
            $flightInfo->setFlightNumber($this->form_vars['flight_number']);
            $flightInfo->setConnectingFlightNumber($this->form_vars['connecting_flight_number']);
            $flightInfo->setSignature($this->form_vars['signature_carrier']);
            $flightInfo->setAccountingReference($this->form_vars['accounting_reference']);
            $flightInfo->setReference($this->form_vars['reference']);
            $flightInfo->setCustomValue($this->form_vars['custom_value']);
            $flightInfo->setCarrier($this->form_vars['carriers']);
            $flightInfo->setCarriageValue($this->form_vars['carriage_value']);
            $flightInfo->setInsuranceAmount($this->form_vars['insurance_amount']);
            $flightInfo->setCurrancy($this->form_vars['currancy']);
            $flightInfo->setRateChange($this->form_vars['rate_change']);
            $flightInfo->setWeightType($this->form_vars['weight_format']);
            $flightInfo->setIataCode($this->form_vars['agent_iata_code']);
            $flightInfo->setFlightDate($this->form_vars['flight_date']);
            $flightInfo->setShipperCo($this->form_vars['shippers_co']);
            $flightInfo->setAccountNumber($this->form_vars['account_number']);
            $flightInfo->setAirwayBill($this->form_vars['airway_bill']);
            $flightInfo->setHscodes($this->form_vars['hs_codes']);
            $flightInfo->setArrivalAirport($this->form_vars['airport_of_depature']);
            $flightInfo->setCity($this->form_vars['city']);
            $flightInfo->setPostcode($this->form_vars['postcode']);
            $flightInfo->setPostcode($this->form_vars['postcode']);
            $flightInfo->setPhoneNumber($this->form_vars['phone_number']);
            $flightInfo->setCompany($this->form_vars['company']);
            $flightInfo->setConsigneeCo($this->form_vars['consignee_co']);
            $flightInfo->setIsDelete(0);
            $flightInfo->setIsClosed(0);
            $flightInfo->setCreatedBy($sessionUser->getId());
            $flightInfo->save();
            $outputArray['status'] = "success";
            $outputArray['message'] = "Flight Saved Successfully";
            echo json_encode($outputArray);
            die;
        }

    }

    protected function filter_form($data)
    {
        $consignmentIdsSearchInFilter = [];
        $this->form_vars = $data;
        $track = '';
        $chinaDispatchDate = '';
        $billOurAccount = '';
        $match = '';
        $export_master = '';
        $shipment = '';
        $remotearea = '';
        $consignemt_collection = '';
        $scanned_type = '';
        $return_category = '';
        $include_returns = '';
        $all_billable = '';
        $invoice_type = '';
        $zero_priced = '';
        $agent = '';

        $dateFrom = $this->form_vars['date_from'];
        $dateTo = $this->form_vars['date_to'];
        $dateType = $this->form_vars['date_type'];
        $fromWeight = $this->form_vars['from_weight'];
        $toWeight = $this->form_vars['to_weight'];
        $scannedType = $this->form_vars['scanned_type'];
        $userAccountId = $this->form_vars['user_account_id'];
        $showShipment = $this->form_vars['show_shipments'];
        $carriers = $this->form_vars['carriers'];
        $service = $this->form_vars['service'];
        $country = $this->form_vars['country'];
        $trackingType = $this->form_vars['tracking_type'];
        $trackingNumber = $this->form_vars['number'];
        $manifest = $this->form_vars['manifest'];
        $highlowvalue = $this->form_vars['highlowvalue'];
        $company = $this->form_vars['company'];
        $contact = $this->form_vars['contact'];
        $city = $this->form_vars['city'];
        $postcode = $this->form_vars['postcode'];
        $pieces = $this->form_vars['pieces'];
        $mawb = $this->form_vars['mawb'];
        $hawb = $this->form_vars['hawb'];
        $invoiceNumber = $this->form_vars['invoice_number'];
        $creditNote = $this->form_vars['credit_note'];
        $status = $this->form_vars['status'];
        $returnTracking = $this->form_vars['return_tracking'];
        $invoice_type = $this->form_vars['invoice_type'];
        $agent = $this->form_vars['agent'];
        if (isset($this->form_vars['track']) && !empty($this->form_vars['track'])) {
            $track = $this->form_vars['track'];
        }
        if (isset($this->form_vars['china_dispatch_date']) && !empty($this->form_vars['china_dispatch_date'])) {
            $chinaDispatchDate = $this->form_vars['china_dispatch_date'];
        }
        if (isset($this->form_vars['bill_our_account']) && !empty($this->form_vars['bill_our_account'])) {
            $billOurAccount = $this->form_vars['bill_our_account'][0];
        }
        if (isset($this->form_vars['match']) && !empty($this->form_vars['match'])) {
            $match = $this->form_vars['match'][0];
        }
        if (isset($this->form_vars['export_master']) && !empty($this->form_vars['export_master'])) {
            $export_master = $this->form_vars['export_master'][0];
        }
        if (isset($this->form_vars['shipment']) && !empty($this->form_vars['shipment'])) {
            $shipment = $this->form_vars['shipment'];
        }
        if (isset($this->form_vars['remotearea']) && !empty($this->form_vars['remotearea'])) {
            $remotearea = $this->form_vars['remotearea'];
        }
        if (isset($this->form_vars['consignemt_collection']) && !empty($this->form_vars['consignemt_collection'])) {
            $consignemt_collection = $this->form_vars['consignemt_collection'][0];
        }
        if (isset($this->form_vars['scanned_type']) && !empty($this->form_vars['scanned_type'])) {
            $scanned_type = $this->form_vars['scanned_type'];
        }
        if (isset($this->form_vars['return_category']) && !empty($this->form_vars['return_category'])) {
            $return_category = $this->form_vars['return_category'];
        }
        if (isset($this->form_vars['include_returns']) && !empty($this->form_vars['include_returns'])) {
            $include_returns = $this->form_vars['include_returns'][0];
        }
        if (isset($this->form_vars['all_billable']) && !empty($this->form_vars['all_billable'])) {
            $all_billable = $this->form_vars['all_billable'][0];
        }
        if (isset($this->form_vars['invoice_type']) && !empty($this->form_vars['invoice_type'])) {
            $invoice_type = $this->form_vars['invoice_type'];
        }
        if (isset($this->form_vars['zero_priced']) && !empty($this->form_vars['zero_priced'])) {
            $zero_priced = $this->form_vars['zero_priced'][0];
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
            $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));

            if ($dateType == "dateCreated") {
                $dateCreatedWhere = "       ((c.date_created >= '" . DbAccess3::escape($dateFrom) . "') AND (c.date_created <= '" . DbAccess3::escape($dateTo) . "'))";
                $this->consignment_filter->addFilter($dateCreatedWhere, 'filter');
            } else if ($dateType == "dateLabelCreated") {
                $dateLabelCreatedWhere = "       ((c.date_label_created >= '" . strtotime(DbAccess3::escape($dateFrom)) . "') AND (c.date_label_created <= '" . strtotime(DbAccess3::escape($dateTo)) . "'))";
                $this->consignment_filter->addFilter($dateLabelCreatedWhere, 'filter');
            } else if ($dateType == "dateBooked") {
                $dateBookedWhere = "       ((c.date_booked >= '" . strtotime(DbAccess3::escape($dateFrom)) . "') AND (c.date_booked <= '" . strtotime(DbAccess3::escape($dateTo)) . "'))";
                $this->consignment_filter->addFilter($dateBookedWhere, 'filter');
            } else if ($dateType == "dateDelivered") {
                $dateDeliveredWhere = "       ((c.date_delivered >= '" . strtotime(DbAccess3::escape($dateFrom)) . "') AND (c.date_delivered <= '" . strtotime(DbAccess3::escape($dateTo)) . "'))";
                $this->consignment_filter->addFilter($dateDeliveredWhere, 'filter');
            } else if ($dateType == "dateScanned") {
                $dateScannedWhere = "       ((c.date_scanned >= '" . DbAccess3::escape($dateFrom) . "') AND (c.date_scanned <= '" . DbAccess3::escape($dateTo) . "'))";
                $this->consignment_filter->addFilter($dateScannedWhere, 'filter');
            }
        }

        if (!empty($fromWeight) && !empty($toWeight)) {

            $this->consignment_filter->addFilter("       (if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight >= '" . DbAccess3::escape($fromWeight) . "' and c.weight <= '" . DbAccess3::escape($toWeight) . "') , ( c.vol_weight >= '" . DbAccess3::escape($fromWeight) . "' and c.vol_weight <= '" . DbAccess3::escape($toWeight) . "')))", 'filter');
        } else if (trim($fromWeight) != '' && $toWeight >= 0) {

            $this->consignment_filter->addFilter("         ( if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight >= '" . DbAccess3::escape($fromWeight) . "' ),( c.vol_weight >= '" . DbAccess3::escape($fromWeight) . "')))", 'filter');
        } else if (trim($toWeight) != '' && $toWeight > 0) {

            $this->consignment_filter->addFilter("     ( if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight <= '" . DbAccess3::escape($toWeight) . "' ),(  c.vol_weight <= '" . DbAccess3::escape($toWeight) . "')))", 'filter');
        }
        $this->consignment_filter->addConsignmentBillingHoldJoin($this->user->getUserAccountId(), $this->user->getUserType());
        if (!empty($userAccountId)) {
            if ($showShipment == 'all') {
                $userAccountArray = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                $ids = implode(",", $userAccountArray);
                $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "userfilter");
            } else if ($showShipment == 'own') {
                $this->consignment_filter->addFilterIn("     u.user_account_id", $userAccountId, "userfilter");
            } else if ($showShipment == 'subaccount') {
                $subAccountArray = CustomerAccount::accountSubAccount($userAccountId, 0, false);
                $ids = implode(",", $subAccountArray);
                $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "userfilter");
            }
        } else {
            $includeParent = true;
//            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
//                $includeParent = false;
//            }
            $userAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, $includeParent);
            $ids = implode(",", $userAccountArray);
            $this->consignment_filter->addFilterIn("     u.user_account_id", $ids, "userfilter");
        }

        if (isset($this->form_vars['bill_our_account']) && !empty($this->form_vars['bill_our_account'])) {
            $bill_our_account = $this->form_vars['bill_our_account'];
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addFieldFilter("account_owner", $bill_our_account);
            $serviceList = $serviceFilter->getColumnList("id");
            $serviceArray = array();
            foreach ($serviceList as $serviceData) {
                $serviceArray[] = $serviceData->getId();
            }
            if (count($serviceArray) > 0) {
                $this->consignment_filter->addFilterIn('    c.service_id', $serviceArray, 'filter');
            }
        }

        if (isset($carriers) && trim($carriers) != '') {
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addCarrierFilter($carriers);
            $serviceList = $serviceFilter->getColumnList("id,is_customized");
            $serviceArr = [];
            $customizeServiceArr = [];
            if (count($serviceList) > 0) {
                foreach ($serviceList as $serviceObj) {
                    if ($serviceObj->getIsCustomized() == 1) {
                        $customizeServiceArr[] = $serviceObj->getId();
                    } else {
                        $serviceArr[] = $serviceObj->getId();
                    }
                }
            }
            if (count($customizeServiceArr) > 0 && count($serviceArr) > 0) {
                $servicesIds = implode(",", $serviceArr);
                $customizeServicesIds = implode(",", $customizeServiceArr);
                $this->consignment_filter->addFilter('    c.service_id  IN (' . DbAccess3::escape($servicesIds) . ') OR c.customized_service_id  IN (' . DbAccess3::escape($customizeServicesIds) . ')', 'filter');
            } else if (count($serviceArr) > 0) {
                $this->consignment_filter->addFilterIn('    c.service_id', $serviceArr, 'filter');
            } else if (count($customizeServiceArr) > 0) {
                $this->consignment_filter->addFilterIn('    c.customized_service_id', $customizeServiceArr, 'filter');
            }
        }

        if (isset($service) && $service != "") {
            $this->consignment_filter->addFilter("     c.service_id= '" . DbAccess3::escape($service) . "' OR c.customized_service_id ='" . DbAccess3::escape($service) . "'");
        }

        if (!empty($country)) {
            $this->consignment_filter->addFieldFilter('     c.country_id', $country);
        }

        if (trim($trackingNumber) != '') {
            $awb = nl2br($trackingNumber);
            $AwbArray = explode('<br />', $awb);
            foreach ($AwbArray as $key => $value) {
                $AwbArray[$key] = "'" . trim(ParseTrackingNumber::Parse($value)) . "'";
            }
            if (!empty($trackingType) && $trackingType == "parcel") {
                $consignmentIds = array();
                $ParcelFilter = new ParcelFilter();
                $ParcelFilter->addTrackingNumberFilterIn("      p.tracking_number", $AwbArray);
                $ParcelData = $ParcelFilter->getColumnList("consignment_id");
                foreach ($ParcelData as $con_id) {
                    $consignmentIdsSearchInFilter[] = $con_id->getConsignmentId();
                }
            } else {
                $this->consignment_filter->addFilterIn('    c.awb', $AwbArray, 'filter');
            }
        }
        /* if (trim($manifest) != '') {
             $manifestObj = new ManifestConsignmentMapping($manifest);
             if (!empty($manifestObj)) {
                 $this->consignment_filter->addFieldFilter('     c.id', $manifestObj->getConsignmentid());
             }
         }
 */
        $manifestFilterCheck = false;
        if (trim($manifest) != '') {
            $consignmentIds = ConsignmentFilter::getConsignmentIdFromMamifest($manifest);
            $consignmentIdsSearchInFilter = array_unique(array_merge($consignmentIdsSearchInFilter, $consignmentIds));
            $manifestFilterCheck = true;
        }


        if (trim($returnTracking) != '') {
            $this->consignment_filter->addFieldFilter('     c.return_awb', $returnTracking);
        }

        if (trim($highlowvalue) != '') {
            $this->consignment_filter->addFieldFilter('     c.hv_lv', $highlowvalue);
        }

        if (trim($company) != '') {
            $this->consignment_filter->addFieldFilter('     c.company', $company);
        }

        if (!empty($contact)) {
            $this->consignment_filter->addFieldLikeFilter('contact', $contact, 'filter');
        }

        if (!empty($city)) {
            $this->consignment_filter->addFieldLikeFilter('c.city', $city, 'filter');
        }

        if (!empty($postcode)) {
            $this->consignment_filter->addFieldFilter('     c.postcode', $postcode);
        }

        if (!empty($pieces)) {
            $this->consignment_filter->addFieldFilter('     c.number_pieces', $pieces);
        }

//        if (trim($export_master) == 1) {
//            if ($mawb != '') {
//                $this->consignment_filter->addTableParcelJoinFilter($mawb);
//            }
//        } else if (trim($mawb) == 'UPS') {
//            $this->consignment_filter->addFieldLikeFilter('     c.mawb', $mawb, 'filter');
//        } else if (trim($mawb) != '') {
//            $this->consignment_filter->addFieldFilter('     c.mawb', $mawb);
//        }
        $mawbFilterCheck = false;
        if (trim($mawb) != '') {
            $consignmentIds = ConsignmentFilter::getConsignmentIdFromMawb($mawb);
            $consignmentIdsSearchInFilter = array_unique(array_merge($consignmentIdsSearchInFilter, $consignmentIds));
            $mawbFilterCheck = true;
        }

        if (trim($hawb) != '' && trim($match) == '') {
            $hawb = nl2br($hawb);
            $HawbArray = explode('<br />', $hawb);
            foreach ($HawbArray as $k => $value) {
                $HawbArray[$k] = "'" . trim($value) . "'";
            }
            $this->consignment_filter->addFilterIn('    c.hawb', $HawbArray, 'filter');
        } else if (trim($hawb) != '' && trim($match) == 1) {
            $hawb = nl2br($hawb);
            $HawbArray = explode('<br />', $hawb);
            foreach ($HawbArray as $k => $value) {
                $HawbArray[$k] = "'" . trim($value) . "'";
            }
            $this->consignment_filter->addFilterLikeIn('    c.hawb', $HawbArray, 'filter');
        }

        if (trim($invoiceNumber) != '') {
            $invoiceid = nl2br($invoiceNumber);
            $invoiceNumber = explode('<br />', $invoiceid);
            foreach ($invoiceNumber as $k => $value) {
                $InvoiceidArray[$k] = "'" . $value . "'";
            }
            $this->consignment_filter->addInvoiceJoin();
            $this->consignment_filter->addFilterIn('    inv.invoice_no', $InvoiceidArray, 'filter');
        }

        if (trim($creditNote) != '') {
            $this->consignment_filter->addFieldFilter('     c.credit_id', $creditNote);
        }
        if (trim($shipment) != '') {
            if ($shipment == 'held') {
                $this->consignment_filter->addFilter("     c.id IN (select consignment_id from consignment_billing_hold where user_account_id_from = '" . $this->user->getUseraccountId() . "')");
            } else if ($shipment == 'nonHeld') {
                $this->consignment_filter->addFilter("     c.id NOT IN (select consignment_id from consignment_billing_hold where user_account_id_from = '" . $this->user->getUseraccountId() . "')");
            }
        }


        if (isset($status[0]) && $status[0] != "" && !empty($status)) {
            $this->consignment_filter->addFilterIn("     c.shipment_status", $status, 'filter');
        } else {
            $this->consignment_filter->addStatusFilterNotIn([Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_INVALID, Consignment::STATUS_RECYCLED], 'filter');
        }

        if (trim($remotearea) != '') {
            if ($remotearea == 'remotearea') {
                $this->consignment_filter->addFieldFilter('     c.remote_charges', '1');
            } else {
                $this->consignment_filter->addFieldFilter('     c.remote_charges', '0');
            }
        }

        if (trim($agent) != '') {
            /* $AgentDataFilter = new AgentDataFilter();
              $AgentDataFilter->addAgentCodeLikeFilter($agent);
              $agentList = $AgentDataFilter->getColumnList('agent_code',500, true);
              die;
              $agentidArray = array();
              if (count($agentList) > 0) {
              foreach ($agentList as $agent) {
              $agentidArray[] = $agent->getId();
              }
              } */
            $agentidArray = array();
            $agentidArray[] = $agent;
            $this->consignment_filter->addFilterIn('    c.agent_id', $agentidArray, 'filter');
        }

        if ($consignemt_collection != "") {
            $this->consignment_filter->addFieldFilter('     c.shipment_type', 'C');
        }

        if (trim($scanned_type) == 'anywhereScanned') {
            $this->consignment_filter->addFieldNotNullFilter('date_scanned');
        } else if (trim($scanned_type) == 'labelCreated') {
            $this->consignment_filter->addFieldNotNullFilter('date_label_created');
        }

        if (trim($return_category) == "return") {
            $this->consignment_filter->addFieldFilter('     c.consignment_type', 'return');
        } else if (trim($return_category) == "outbound") {
            $this->consignment_filter->addFieldFilter('     c.consignment_type', 'outbound');
        }

        $this->consignment_filter->addConsignmentChargesJoin($this->user->getUserAccountId(), $this->user->getUserType());
        if (!empty($invoice_type)) {
            if ($invoice_type == "invoiced") {
                $this->consignment_filter->addFieldNotNullFilter("    cc.invoice_id");
                $this->consignment_filter->addFilter("     cc.invoice_id > 0", 'filter');
            } else if ($invoice_type == "notinvoiced") {
                $this->consignment_filter->addFilter("    ((cc.invoice_id IS NULL OR cc.invoice_id = 0) AND cost_type='customer') ", 'filter');
            }
        }

        if (trim($all_billable) != '' && trim($all_billable) == 1) {
            $filterService = new ServiceFilter();
            $filterService->addFilter(" account_owner not in ( '175', '166', '0')", 'filter');
            $filterServiceList = $filterService->getColumnList(" Id, account_owner ");
            $parentDataArray = array();
            foreach ($filterServiceList as $serviceData) {
                $parentDataArray[] = $serviceData->getId();
            }

            if (!empty($parentDataArray)) {
                $this->consignment_filter->addFilterIn('    c.service_id', $parentDataArray, 'filter');
            }
        }

        if (count($consignmentIdsSearchInFilter) > 0) {
            $this->consignment_filter->addFilterIn('    c.id', $consignmentIdsSearchInFilter, 'filter');
        }
        if (trim($mawb) != '' && !$mawbFilterCheck)
            $this->consignment_filter->addFilterIn('    c.id', [0], 'filter');
        if (trim($manifest) != '' && !$manifestFilterCheck)
            $this->consignment_filter->addFilterIn('    c.id', [0], 'filter');


        if (isset($this->form_vars['group_by']) && $this->form_vars['group_by'] == 0) {
            $this->consignment_filter->addGroupBy('pc.id');
        } else {
            $this->consignment_filter->addGroupBy('c.id');
        }
    }

    function invoiceCsv($invoiceId)
    {
        $invoiceDetailFilter = new InvoiceDetailFilter();
        $invoiceDetailFilter->addFieldFilter("invoice_id", $invoiceId);
        $invoiceDetailFilterObj = $invoiceDetailFilter->getList();
        $consignmentIds = array();
        if (count($invoiceDetailFilterObj) > 0) {
            foreach ($invoiceDetailFilterObj as $obj) {
                $consignmentIds[] = $obj->getConsignmentId();
            }
        }
        $output = $this->invoicesCsv($consignmentIds, $invoiceId);
        return $output;
    }

    protected function invoicesCsv($consignmentIds, $invoiceId)
    {
        $returnData = [];
        $fileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/csv/';
        $fileName = "INV_" . $invoiceId . "_" . time() . ".csv";
        $file_url = $fileLocation . $fileName;
        if (!file_exists($fileLocation)) {
            mkdir($fileLocation, 0777, true);
        }
        $myfile = fopen($file_url, "a") or die("Unable to open file!");

        $chargesTotal = array();
        $heading = ["Account", "Invoice Date", "Label Created Date", "Dispatched Date", "Delivered Date", "Invoice Number", "Order Ref No", "Tracking Number", "MAWB", "Status", "Carrier", "Service Name", "Sender City", "Sender Country", "Sender Postcode", "Receiver City", "Receiver Country", "Receiver Postcode", "Number of Pieces", "Weight", "Vol Weight"];
        $consignmentChargesTypeFilter = new ConsignmentChargesTypesFilter();
        $consignmentChargesTypeFilter->addFieldNotFilter("     charge_type", "agent");
        $consignmentChargesTypeFilter->addFieldNotFilter("     charges_key", "VAT");
        $consignmentChargesTypeFilterObj = $consignmentChargesTypeFilter->getList();
        if (count($consignmentChargesTypeFilterObj) > 0) {
            foreach ($consignmentChargesTypeFilterObj as $chargesTypeObj) {
                if ($chargesTypeObj->getIsExtraCharge() == 0) {
                    $heading[] = $chargesTypeObj->getTitle();
                    $chargesTotal[$chargesTypeObj->getTitle()] = 0;
                }
            }
        }
        $heading[] = "Extra Charges";
        $heading[] = "Net Total";
        $heading[] = "Length";
        $heading[] = "Width";
        $heading[] = "Height";
        if (count($consignmentIds) > 0) {
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= $h . ",";
            }
            $returnString .= "\r\n";
            fwrite($myfile, $returnString);

            $invoiceObj = new Invoices($invoiceId);
            $recievedUserObj = new CustomerAccount($invoiceObj->getUserAccountId());
            $totalNumberOfPieces = 0;
            $totalWeight = 0;
            $totalVolWeight = 0;
            $grandTotalExtraCharges = 0;
            $grandAllTotalExtraCharges = 0;
            foreach ($consignmentIds as $consignmentId) {
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
                $parcelFilterObj = $parcelFilter->getColumnList("tracking_number,length,width,height");
                if (count($parcelFilterObj) > 0) {
                    foreach ($parcelFilterObj as $key => $parcelObj) {
                        $returnString = "";
                        $consignmentObj = new Consignment($consignmentId);
                        $returnString .= $recievedUserObj->getUserAccount() . ",";
                        $returnString .= date("d F Y", $invoiceObj->getInvoiceDate()) . ",";
                        $dateLabelCreated = (!empty($consignmentObj->getDateLabelCreated()) ? date("d F Y", $consignmentObj->getDateLabelCreated()) : "");
                        $returnString .= $dateLabelCreated . ",";
                        $dateBooked = (!empty($consignmentObj->getDateBooked()) ? date("d F Y", $consignmentObj->getDateBooked()) : "");
                        $returnString .= $dateBooked . ",";
                        $dateDelivered = (!empty($consignmentObj->getDateDelivered()) ? date("d F Y", $consignmentObj->getDateDelivered()) : "");
                        $returnString .= $dateDelivered . ",";
                        $returnString .= $invoiceObj->getInvoiceNo() . ",";

                        $returnString .= '="' . $consignmentObj->getHawb() . '",';
                        $returnString .= '="' . $parcelObj->getTrackingNumber() . '",';
                        $returnString .= $consignmentObj->getMawb() . ",";
                        $shipment_status = $this->get_shipnment_status($consignmentObj->getShipmentStatus(), "csv");
                        $returnString .= $shipment_status . ",";
                        if ($consignmentObj->getCustomizedServiceId() > 0)
                            $serviceObj = new Services($consignmentObj->getCustomizedServiceId());
                        else
                            $serviceObj = new Services($consignmentObj->getServiceId());

                        $carrierObj = new Carrier($serviceObj->getCarrierId());
                        $returnString .= $carrierObj->getCarrier() . ",";
                        $returnString .= $serviceObj->getName() . ",";

                        $returnString .= $consignmentObj->getSenderCity() . ",";
                        $senderCountryObj = new Country($consignmentObj->getSenderCountryId());
                        $returnString .= $senderCountryObj->getName() . ",";
                        $returnString .= $consignmentObj->getSenderPostcode() . ",";
                        $countryObj = new Country($consignmentObj->getCountryId());
                        $returnString .= $consignmentObj->getCity() . ",";
                        $returnString .= $countryObj->getName() . ",";
                        $returnString .= $consignmentObj->getPostcode() . ",";
                        if ($key == 0) {
                            $returnString .= $consignmentObj->getNumberPieces() . ",";
                            $returnString .= $consignmentObj->getWeight() . ",";
                            $returnString .= $consignmentObj->getVolWeight() . ",";
                            $totalNumberOfPieces = $totalNumberOfPieces + $consignmentObj->getNumberPieces();
                            $totalWeight = $totalWeight + $consignmentObj->getWeight();
                            $totalVolWeight = $totalVolWeight + $consignmentObj->getVolWeight();
                        } else {
                            $returnString .= " " . ",";
                            $returnString .= " " . ",";
                            $returnString .= " " . ",";
                        }
                        $totalExtraCharges = 0;
                        $totalOtherCharges = 0;
                        if (count($consignmentChargesTypeFilterObj) > 0) {
                            foreach ($consignmentChargesTypeFilterObj as $chargesTypeObj) {
                                $consignmentChargesFilter = new ConsignmentChargesFilter();
                                $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentId);
                                $consignmentChargesFilter->addFieldFilter("     cc.invoice_id", $invoiceId);
                                $consignmentChargesFilter->addFieldFilter("     cc.cost_type", "customer");
                                $consignmentChargesFilter->addFieldFilter("     cc.charge_type_id", $chargesTypeObj->getId());
                                $consignmentChargesFilterObj = $consignmentChargesFilter->getList();
                                if ($chargesTypeObj->getIsExtraCharge() == 0) {
                                    if ($key == 0) {
                                        if (count($consignmentChargesFilterObj) > 0) {
                                            $returnString .= $consignmentChargesFilterObj[0]->getCost() . ",";
                                            $totalOtherCharges = $totalOtherCharges + $consignmentChargesFilterObj[0]->getCost();
                                            $chargesTotal[$chargesTypeObj->getTitle()] = $chargesTotal[$chargesTypeObj->getTitle()] + $consignmentChargesFilterObj[0]->getCost();
                                        } else {
                                            $returnString .= 0 . ",";
                                        }
                                    } else {
                                        $returnString .= " " . ",";
                                    }
                                } else {
                                    if (count($consignmentChargesFilterObj) > 0) {
                                        $totalExtraCharges = $totalExtraCharges + $consignmentChargesFilterObj[0]->getCost();
                                    }
                                }
                            }
                        }
                        if ($key == 0) {
                            $returnString .= $totalExtraCharges . ",";
                            $returnString .= ($totalExtraCharges + $totalOtherCharges) . ",";
                            $grandTotalExtraCharges = $grandTotalExtraCharges + $totalExtraCharges;
                            $grandAllTotalExtraCharges = $grandAllTotalExtraCharges + ($totalExtraCharges + $totalOtherCharges);
                        } else {
                            $returnString .= " " . ",";
                        }

                        $returnString .= $parcelObj->getLength() . ",";
                        $returnString .= $parcelObj->getWidth() . ",";
                        $returnString .= $parcelObj->getHeight() . ",";
                        $returnString .= "\r\n";
                        fwrite($myfile, $returnString);
                    }
                }
            }
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= " " . ",";
            }
            $returnString .= "\r\n";
            fwrite($myfile, $returnString);

            $returnString = "";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";
            $returnString .= " " . ",";

            $returnString .= " TOTAL :  " . ",";
            $returnString .= $totalNumberOfPieces . ",";
            $returnString .= $totalWeight . ",";
            $returnString .= $totalVolWeight . ",";
            foreach ($chargesTotal as $key => $total) {
                $returnString .= $total . ",";
            }
            $returnString .= $grandTotalExtraCharges . ",";
            $returnString .= $grandAllTotalExtraCharges . ",";
            $returnString .= "\r\n";
            fwrite($myfile, $returnString);
        }
        fclose($myfile);
        $returnData['FILENAME'] = $fileName;
        $returnData['LINK'] = $file_url;
        $returnData['result'] = 'Please <a href="' . $file_url . '" target="_blank">Click Here</a> to download the CSV <a href="' . $file_url . '" class="btn btn-primary" target="_blank"> Download </a>';
        return $returnData;
        die;
    }

    protected function get_shipnment_status($status, $csv = "")
    {
        $c_status = Consignment::stateText(strtolower(trim($status)));
        if (strtolower($c_status) == "label created") {
            $shipment_status = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray line-height-2"> ' . Translation::GetCaption("PRINTED") . '</span>';
        } else if (strtolower($c_status) == "booked") {
            $shipment_status = '<span class="label label-sm bg-blue-dark bg-font-blue-dark line-height-2"> ' . Translation::GetCaption("SHIPPED") . '</span>'; //= Translation::GetCaption("SHIPPED");
        } else if (Consignment::STATUS_RECYCLED == trim($status)) {
            $shipment_status = '<span class="label label-sm label-danger line-height-2"> ' . Translation::GetCaption("RECYCLED") . '</span>';
        } else if (Consignment::STATUS_DELIVERED == trim($status)) {
            $shipment_status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> ' . Translation::GetCaption("DELIVERED") . '</span>';
        } else if (Consignment::STATUS_INVALID == trim($status)) {
            $shipment_status = '<span class="label label-sm label-warning line-height-2"> ' . Translation::GetCaption("INVALID") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("READY_TO_PRINT") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("VALID") . '</span>';
        } else {
            if ($c_status == "Unknown") {
                $shipment_status = "";
            } else {
                $shipment_status = '<span class="label label-sm bg-default bg-font-default line-height-2"> ' . Translation::GetCaption(strtoupper($c_status)) . '</span>';
            }
        }
        if ($csv == "csv") {
            $shipment_status = strip_tags($shipment_status);
        }
        return $shipment_status;
    }

    function downloadCSV($dataConsignment, $agentOption = 0, $consignmentChargeTypeCustomerObj = array(), $consignmentChargeTypeAgentObj = array())
    {

        $output = [];
        $DOWNLOADABLE_FILE_NAME = "../_assets/csv/exportCsv-" . time() . ".csv"; //= fopen("../_assets/csv/exportCsv-".time().".txt", "w") or die("Unable to open file!");
        $myfile = fopen($DOWNLOADABLE_FILE_NAME, "a") or die("Unable to open file!");
        $currentConsignmentAdd = 0;
        if (count($dataConsignment) > 0) {
            // PUT HEADER FOR ROWS
            if ($currentConsignmentAdd == 0) {
                fwrite($myfile, $this->exportHeader($agentOption, $consignmentChargeTypeCustomerObj, $consignmentChargeTypeAgentObj));
            }
            foreach ($dataConsignment as $consignmentItem) {
                $rowTowrite = $this->exportRowCsv($consignmentItem, $agentOption, $consignmentChargeTypeCustomerObj, $consignmentChargeTypeAgentObj);
                fwrite($myfile, $rowTowrite);
                $currentConsignmentAdd++;
            }
            fclose($myfile);
            $output['result'] = 'Please <a href="' . $DOWNLOADABLE_FILE_NAME . '" target="_blank">Click Here</a> to download the CSV <a href="' . $DOWNLOADABLE_FILE_NAME . '" class="btn btn-primary" target="_blank"> Download </a>';
            unset($_SESSION['DOWNLOADABLE_FILE_NAME']);
        } else {
            $output['result'] = 'No data found to generate the CSV';
        }
        return $output;
        die;
    }

    function exportRowCsv($consignmentObj, $agentOption = 0, $consignmentChargeTypeCustomerObj, $consignmentChargeTypeAgentObj = array())
    {
        $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentObj->getId());
        $returnString = "";
        // $userObj = new User($obj->getUserId());
        $returnString .= $consignmentObj->getUserAccount() . ",";
        $invoiceObj = new Invoices($consignmentObj->getChargesInvoiceId());
        $returnString .= cleanCsvCall($invoiceObj->getInvoiceNo()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getHawb(), 'int') . ",";
        $returnString .= cleanCsvCall($consignmentObj->getAwb(), 'int') . ",";
        $returnString .= cleanCsvCall($consignmentObj->getMawb(), 'int') . ",";
        $consignmentBaggingMappingObj = new ConsignmentBaggingMapping($consignmentObj->getId());
        $returnString .= cleanCsvCall($consignmentBaggingMappingObj->getBagid()) . ",";
        $serviceObj = new Services($consignmentObj->getServiceId());
        $returnString .= cleanCsvCall($serviceObj->getCode()) . ",";
        $returnString .= cleanCsvCall($serviceObj->getName()) . ",";
        $returnString .= cleanCsvCall($serviceObj->getType()) . ",";
        $returnString .= "  ,";
        $returnString .= trim(cleanCsvCall($consignmentObj->getReference())) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getConsignmentStatus()) . ",";
        $returnString .= ($consignmentObj->getDateLabelCreated() != '' && $consignmentObj->getDateLabelCreated() > 0 ? date("Y-m-d", $consignmentObj->getDateLabelCreated()) : '') . ",";
        $returnString .= ($consignmentObj->getDateScanned() != '' && $consignmentObj->getDateScanned() > 0 ? date("Y-m-d", $consignmentObj->getDateScanned()) : '') . ",";
        $returnString .= ($consignmentObj->getDateBooked() != '' && $consignmentObj->getDateBooked() > 0 ? date("Y-m-d", $consignmentObj->getDateBooked()) : '') . ",";
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addFilter("     (t.status_code_id = 121 OR t.status_code_id = 122)");
        $trackingDataFilter->addFieldFilter("     t.tracking_number", $consignmentObj->getAwb());
        $trackingDataFilterObjs = $trackingDataFilter->getList();
        $dateDelivered = "";
        if (count($trackingDataFilterObjs) > 0) {
            $dateDelivered = ($trackingDataFilterObjs[0]->getDateCreated() != '' && $trackingDataFilterObjs[0]->getDateCreated() > 0 ? date("Y-m-d", strtotime($trackingDataFilterObjs[0]->getDateCreated())) : '');
        }
        $returnString .= $dateDelivered . ",";
        $returnString .= "  ,";
        $returnString .= "  ,";
        $returnString .= cleanCsvCall($consignmentObj->getCompany()) . ",";
        $returnString .= str_replace(',', " ", $consignmentObj->getAddressLine1()) . " " . str_replace(',', " ", $consignmentObj->getAddressLine2()) . " " . str_replace(',', " ", $consignmentObj->getAddressLine3()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getCity()) . ",";
        $countryObj = new Country($consignmentObj->getCountryId());
        $returnString .= cleanCsvCall($countryObj->getName()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getPostcode()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getNumberPieces()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getChargeWeight()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getWeight()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getVolWeight()) . ",";
        if ($consignmentObj->getIsDoc() == 1) {
            $doc = "DOX";
        } else {
            $doc = "NDX";
        }
        $returnString .= $doc . ",";
        $returnString .= cleanCsvCall($consignmentObj->getValue()) . ",";
        $returnString .= cleanCsvCall($consignmentObj->getCurrency()) . ",";
        if ($consignmentObj->getRemoteCharges() == 1) {
            $remoteArea = "Yes";
        } else {
            $remoteArea = "No";
        }
        $returnString .= cleanCsvCall($remoteArea) . ",";
        $trackingFilter = new TrackingDataFilter();
        $trackingFilter->addTrackingNumberFilter($consignmentObj->getAwb());
        $trackingFilter->AddOrderByTracking(false);
        $trackingFilterObj = $trackingFilter->getList();
        if (count($trackingFilterObj) > 0) {
            if (trim($trackingFilterObj[0]->getSignatory()) != '')
                $returnString .= cleanCsvCall($trackingFilterObj[0]->getCarrierDesc()) . " to " . cleanCsvCall($trackingFilterObj[0]->getSignatory()) . ",";
            else
                $returnString .= cleanCsvCall($trackingFilterObj[0]->getCarrierDesc()) . ",";
        } else {
            $returnString .= "  ,";
        }


        $parcelFilter = new ParcelFilter();
        $parcelFilter->addConsignmentIdFilter($consignmentObj->getId());
        $parcelFilterObj = $parcelFilter->getList();
        if (count($parcelFilterObj) > 0) {
            $returnString .= cleanCsvCall($parcelFilterObj[0]->getLength()) . ",";
            $returnString .= cleanCsvCall($parcelFilterObj[0]->getWidth()) . ",";
            $returnString .= cleanCsvCall($parcelFilterObj[0]->getHeight()) . ",";
        } else {
            $returnString .= "  ,";
            $returnString .= "  ,";
            $returnString .= "  ,";
        }

        foreach ($consignmentChargeTypeCustomerObj as $CCTCobj) {
            $consignmentChagesFilter = new ConsignmentChargesFilter();
            $consignmentChagesFilter->addFieldFilter("    consignment_id", $consignmentObj->getId());
            $consignmentChagesFilter->addFieldFilter("    charge_type_id", $CCTCobj->getId());
            $consignmentChagesFilter->addFieldFilter("    account_id", $user_account_id);
            $consignmentChagesFilter->addFieldFilter("    cost_type", "customer");
            $consignmentChagesFilterObj = $consignmentChagesFilter->getList();
            if (count($consignmentChagesFilterObj) > 0) {
                $returnString .= cleanCsvCall($consignmentChagesFilterObj[0]->getCost()) . ",";
            } else {
                $returnString .= "  ,";
            }
        }

        if ($agentOption == 1) {
            foreach ($consignmentChargeTypeAgentObj as $CCTCobj) {
                $consignmentChagesFilter = new ConsignmentChargesFilter();
                $consignmentChagesFilter->addFieldFilter("    consignment_id", $consignmentObj->getId());
                $consignmentChagesFilter->addFieldFilter("    charge_type_id", $CCTCobj->getId());
                $consignmentChagesFilter->addFieldFilter("    account_id", $user_account_id);
                $consignmentChagesFilter->addFieldFilter("    cost_type", "agent");
                $consignmentChagesFilterObj = $consignmentChagesFilter->getList();
                if (count($consignmentChagesFilterObj) > 0) {
                    $returnString .= cleanCsvCall($consignmentChagesFilterObj[0]->getCost()) . ",";
                } else {
                    $returnString .= "  ,";
                }
            }
        }
        $returnString .= "\r\n";
        return $returnString;
    }

    function exportHeader($agentOption = 0, $consignmentChargeTypeCustomerObj = array(), $consignmentChargeTypeAgentObj = array())
    {
        $rowToWrite = '';
        $header_row = ["Account", "Invoice No", "Order Ref No", "Tracking Number", "MAWB", "Bag No", "Service Code", "Service Name", "Service Type", "Product Name", "REF", "Consignment Status", "Date Label Created", "Date Scanned", "Date Dispatched", "Date Delivered", "Collection Country", "Collection  Postcode", "Company", "Address", "City", "Country", "Postcode", "Number of Pieces", "Chargable Weight", "Weight", "Vol Weight", "DOX/NDX", "Value", "Currency", "Remotearea", "Last Status", "Length", " Width", "Height"];
        foreach ($header_row as $field) {
            $rowToWrite .= $field . ",";
        }
        foreach ($consignmentChargeTypeCustomerObj as $obj) {
            $rowToWrite .= $obj->getTitle() . ",";
        }
        if ($agentOption == 1) {
            foreach ($consignmentChargeTypeAgentObj as $obj) {
                $rowToWrite .= "Agent " . $obj->getTitle() . ",";
            }
        }
        $rowToWrite .= "\r";
        return $rowToWrite;
    }

    public function removeChar($text)
    {
        $textNew = $text;
        $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("'", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace('"', '', preg_replace('/[\$,]/', '', $textNew));

        $textNew = preg_replace('/[\n,]/', '', $textNew);

        return $textNew;
    }

    public function getCurrencyFormat($amount, $currency)
    {
        $amt = number_format((float)$amount, 2, '.', '');
        return $amt . " " . $currency;
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
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">
        <style>
            .label-account {
                font-size: 12px;
                font-weight: bold;
            }

            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/flipclock.min.js"></script>
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "shipment_list_manage.php?action=consignment_list";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            // execute some code after table records loaded
                            $('#calculated_zero_price').html(response.getZeroPriced);
                            $('#calculated_pieces').html(response.totalNumberPieces);
                            $('#calculated_weight').html(response.totalWeight);
                            //this.unblockUI()
                            $(".table-container .custom-alerts").hide();
                            if (response.recordsTotal > 0) {
                                $("#bluk_actions").show();
                                $(".button-download-records").show();
                            } else {
                                $("#bluk_actions").hide();
                                $(".button-download-records").hide();
                            }
                            $('#total-number-of-items').val(response.recordsTotal);
                        },

                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150, 500, 1000],
                                [20, 50, 100, 150, 500, 1000] // change per page values here
                            ],
                            //                            "oLanguage": {
                            //                                "sEmptyTable": "<?php //echo ((isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') ? 'No records found' : 'Please search to view data');       ?>"
                            //                            },
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {}
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "account_mawb_dateDispatched"},
                                {"data": "status_hawbNumber_trackingNumber"},
                                {"data": "location"},
                                {"data": "weight_volWeight"},
                                {"data": "pieces"},
                                {"data": "valueType"},
                                {"data": "serviceCode_serviceName_product"},
                                {"data": "remotearea_onHold"},
                                {"data": "invoiceNumber"},
                                {"data": "tariff", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {
                                var consignmentId = $('td input', row).data('consignment_id');
                                var statusClass = data.pieces;
                                statusClass = statusClass.split('</span>')[0];
                                statusClass = statusClass.split('<span style="display:none;">').pop();
                                statusClass = statusClass.trim();
                                $(row).addClass(statusClass);
                                $(row).dblclick(function () {
                                    window.open(
                                        'shipment_view_manage.php?id=' + consignmentId,
                                        '_blank'
                                    );
                                });
                                //                                goToByScroll("manage-data-table");
                            }
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
            var gridParcel = null;
            var DataTableFunParcel = function () {
                var handleDataTableParcel = function () {
                    var datatableparcelurl = "shipment_list_manage.php?action=parcel_list";
                    gridParcel = new Datatable();
                    gridParcel.init({
                        src: $("#manage-parcel-data-table"),
                        onSuccess: function (grid, response) {
                            // execute some code after table records loaded
                        },

                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [10, 20, 50, 100, 150],
                                [10, 20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": datatableparcelurl, // ajax source
                                headers: {}
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "tracking_number"},
                                {"data": "service"},
                                {"data": "dims"},
                                {"data": "weight"},
                                {"data": "status"},
                                {"data": "already_manifest"},
                                {"data": "view_manifest", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {

                            }
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTableParcel();
                    }
                };
            }();
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
            $(document).ready(function () {
                DataTableFun.init();
                DataTableFunParcel.init();
                /* Custom filtering function which will search data in column four between two values */
                $('#btn_go').click(function () {
                    $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all checkboxes
                    $('input.form-filter[type="checkbox"]:checked').each(function () {
                        grid.addAjaxParam($(this).attr("name"), $(this).val());
                    });
                    // get all radio buttons
                    $('input.form-filter[type="radio"]:checked').each(function () {
                        grid.setAjaxParam($(this).attr("name"), $(this).val());
                    });
                    grid.submitFilter();
                    var selectAccount = $('#user_account_id').val();
                    var showShipments = $('#show_shipments').val();
                    var loginUser = <?php echo $this->user->getUserAccountId(); ?>;
                    $('#apply_bluk_price').hide();
                    if (selectAccount != "") {
                        if (selectAccount != loginUser) {
                            if ($('#invoice_type').val() == "notinvoiced") {
                                $('#invoice_generate_btn').show();
                            } else {
                                $('#invoice_generate_btn').hide();
                            }
                            $('#apply_bluk_price').show();
                        } else {
                            if (showShipments == "subaccount") {
                                if ($('#invoice_type').val() == "notinvoiced") {
                                    $('#invoice_generate_btn').show();
                                } else {
                                    $('#invoice_generate_btn').hide();
                                }
                                $('#apply_bluk_price').show();
                            }
                        }
                    }
                    if ($('#invoice_type').val() == "invoiced") {
                        $('#btnGPReportData').show();
                    } else {
                        $('#btnGPReportData').hide();
                    }
                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        dateFormate: "yyyy-mm-dd"
                    });
                }
                $('#user_account_id').change(function () {
                    var userAccountId = $(this).val();
                    get_carriers(userAccountId);
                });

                $(document).on('click', '#btn_add_flight_detail', function () {
                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                    form_data.push({name: "action", value: 'add_flight_information'});
                    $.ajax({
                        type: "POST",
                        url: "assign_bag_number_flight.php",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            grid.getDataTable().ajax.reload();
                            swal("Success!", data.message, "success");
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });
                $(document).on('keydown', '#tracking_number', function (e) {
                    if (e.keyCode == 13) {
                        var tracking_number = $("#tracking_number").val();
                        var action = "add_parcel_to_bag";
                        var flight_number = $("#opened_flights").val();
                        if (flight_number == '' || tracking_number == '') {
                            swal("Alert!", 'Please Select Any Flight', "info");
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            url: "assign_bag_number_flight.php",
                            data: {
                                tracking_number: tracking_number,
                                action: action,
                                flight_number: flight_number
                            },
                            dataType: "json",
                            success: function (data) {
                                // grid.getDataTable().ajax.reload();
                                if (data.status == 'success') {
                                    swal("Success!", data.message, "success");
                                } else if (data.status == 'error') {
                                    swal("Alert!", data.message, "info");
                                }
                            },
                            error: function () {
                                //alert('error handing here');
                            }
                        });
                    }

                });
                $("#manual_awb_for_change_status").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $("#tracking_number_for_status_box").show();
                    } else {
                        $("#tracking_number_for_status_box").hide();
                    }
                });
                get_carriers('');
            });

            function get_carriers(userAccountId) {
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId},
                    dataType: "json",
                    success: function (data) {
                        $('#carriers').html(data.carrier_option);
                        $('#service').html(data.services_option);
                        $('#agent').html(data.agent_option);
                        $('#carriers').select2();
                        $('#service').select2();
                        $('#agent').select2();
                        $('#carriers').trigger('change');
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function change_carriers(carrierId) {
                $('.serviceOption').attr("disabled", "disabled");
                $('.carrier_' + carrierId).removeAttr("disabled");
                $('#service').select2();
            }

            function updateInvoiceTotal() {
                var customer_total = 0;
                var agent_total = 0;
                var margin = 0;
                $(".customer_charges").each(function () {
                    var val = $.trim($(this).val());
                    val = (val == '' ? 0 : val);
                    customer_total += parseFloat(val);
                });
                $(".agent_charges").each(function () {
                    var val = $.trim($(this).val());
                    val = (val == '' ? 0 : val);
                    agent_total += parseFloat(val);
                });
                margin = parseFloat(customer_total - agent_total);
                $("#customer_total").val(customer_total);
                $("#agent_total").val(agent_total);
            }

            function updatePricingDetails() {
                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=save_consignment_charges",
                    data: form_data,
                    success: function (data) {
                        // for empty the fields
                        $(".customer_charges").each(function () {
                            $(this).val("");
                        });
                        $(".agent_charges").each(function () {
                            $(this).val("");
                        });
                        $("#customer_total").val("");
                        $("#agent_total").val("");
                        $('#customer_extras_body').html("");
                        $('#agent_extras_body').html("");

                        grid.getDataTable().ajax.reload();
                        var html = '<div class="alert alert-success">' + data + '</div>';
                        $('#res_message').html(html);
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

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

            function updateBillingHold() {
                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=update_billing_hold",
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        grid.getDataTable().ajax.reload();
                        var html = '<div class="alert alert-success">' + data.message + '</div>';
                        $('#res_billing_hold_message').html(html);
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function updateManaualPod() {
                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                var file_data = $('#pod_image').prop('files')[0];
                form_data.push('pod_file', file_data);
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=update_manaual_pod",
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        var html = '<div class="alert alert-success">';
                        html += data.message;
                        html += '</div>';
                        $('#res_manual_pod_message').html(html);
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function goToByScroll(id) {
                // Reove "link" from the ID
                id = id.replace("link", "");
                // Scroll
                $('html,body').animate({
                        scrollTop: $("#" + id).offset().top
                    },
                    'slow');
            }

            function change_selected_action() {
                var ids = [];
                $('.consignmentIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                if (typeof ids !== 'undefined' && ids.length > 0) {
                    var action = $('#change_consignment').val();
                    if (action == "re_generate") {
                        regenerate_shipment();
                    } else if (action == "close_shipment") {
                        close_shipment();
                    } else if (action == "include_in_scanned") {
                        include_in_scanned();
                    } else if (action == "bulk_price") {
                        swal({
                                title: "Are You Sure?",
                                text: "you want to change the status of selected consignment!",
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
                                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                                    $(".icheck:not(:checked)").each(function () {
                                        form_data.push({name: this.name, value: '0'});
                                    });
                                    form_data.push({name: "action_type", value: 'selected'});
                                    $.ajax({
                                        type: "POST",
                                        url: "shipment_list_manage.php?action=save_tariff_consignment_charges",
                                        data: form_data,
                                        dataType: "json",
                                        success: function (data) {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!", data.message, "success");
                                        },
                                        error: function (p1, p2, p3) {
                                            //alert('error handing here');
                                        }
                                    });
                                }
                            });
                    } else if (action == "hold" || action == "unhold") {
                        swal({
                                title: "",
                                text: "Please enter reason for change biling hold",
                                type: "input",
                                showCancelButton: true,
                                closeOnConfirm: false
                            },
                            function (inputValue) {
                                if (inputValue === false) return false;

                                if (inputValue === "") {
                                    swal.showInputError("You need to write something!");
                                    return false
                                } else {
                                    swal({
                                            title: "Are You Sure?",
                                            text: "you want to change the status of selected consignment!",
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
                                                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                                                $(".icheck:not(:checked)").each(function () {
                                                    form_data.push({name: this.name, value: '0'});
                                                });
                                                form_data.push({name: "action_type", value: 'selected'});
                                                form_data.push({name: "reason", value: inputValue});
                                                $.ajax({
                                                    type: "POST",
                                                    url: "shipment_list_manage.php?action=update_billing_hold",
                                                    data: form_data,
                                                    dataType: "json",
                                                    success: function (data) {
                                                        grid.getDataTable().ajax.reload();
                                                        swal("Success!", data.message, "success");
                                                    },
                                                    error: function () {
                                                        //alert('error handing here');
                                                    }
                                                });
                                            }
                                        });
                                }
                            });
                    } else {
                        swal("Sorry!", "No Action is selected", "error");
                    }
                } else {
                    swal("Sorry!", "Please check the checkbox for action", "error");
                }
            }

            function change_parcel_selected_action() {
                var ids = [];
                $('.parcelIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                if (typeof ids !== 'undefined' && ids.length > 0) {
                    var action = $('#change_parcel').val();
                    var checkService = "";
                    var actionProcess = 1;
                    $.each(ids, function (index) {
                        var res = ids[index].split("||");
                        if (res[3] !== undefined) {
                            actionProcess = 0;
                        }
                        if (index == 0) {
                            checkService = res[2];
                        } else {
                            if (checkService != res[2]) {
                                actionProcess = 0;
                            }
                        }
                    });
                    if (action == "create_manifest") {
                        if (actionProcess == 1) {
                            var form_data = $("#add_assign_bag_to_flight").serializeArray();
                            $(".icheck:not(:checked)").each(function () {
                                form_data.push({name: this.name, value: '0'});
                            });
                            form_data.push({name: "action_type", value: 'selected'});
                            form_data.push({name: "service_id", value: checkService});
                            $.ajax({
                                type: "POST",
                                url: "shipment_list_manage.php?action=create_manifest",
                                data: form_data,
                                dataType: "json",
                                success: function (data) {
                                    gridParcel.getDataTable().ajax.reload();
                                    swal("Success!", data.message, "success");
                                },
                                error: function (p1, p2, p3) {
                                    //alert('error handing here');
                                }
                            });
                        } else {
                            swal("Sorry!", "Your selected parcel services are not matched or already manifest creatred", "error");
                        }
                    } else if (action == "remove_manifest") {
                        var form_data = $("#add_assign_bag_to_flight").serializeArray();
                        $(".icheck:not(:checked)").each(function () {
                            form_data.push({name: this.name, value: '0'});
                        });
                        form_data.push({name: "action_type", value: 'selected'});
                        $.ajax({
                            type: "POST",
                            url: "shipment_list_manage.php?action=remove_manifest",
                            data: form_data,
                            dataType: "json",
                            success: function (data) {
                                if (data.status == "success") {
                                    gridParcel.getDataTable().ajax.reload();
                                    swal("Success!", data.message, "success");
                                } else {
                                    swal("Sorry!", data.message, "error");
                                }
                            },
                            error: function (p1, p2, p3) {
                                //alert('error handing here');
                            }
                        });
                    } else {
                        swal("Sorry!", "No Action is selected", "error");
                    }
                } else {
                    swal("Sorry!", "Please check the checkbox for action", "error");
                }
            }

            function create_manifest_all() {
                var ids = [];
                var parcelIds = [];
                var servicesIds = [];
                $('.parcelIds').map(function () {
                    ids.push(this.value);
                    parcelIds.push($(this).data("parcel_id"));
                    servicesIds.push($(this).data("service_id"));
                }).get();
                var checkService = "";
                var actionProcess = 1;
                $.each(servicesIds, function (index) {
                    var res = servicesIds[index];
                    if (index == 0) {
                        checkService = res;
                    } else {
                        if (checkService != res) {
                            actionProcess = 0;
                        }
                    }
                });
                $.each(ids, function (index) {
                    var res = ids[index].split("||");
                    if (res[3] !== undefined) {
                        actionProcess = 0;
                    }
                });
                if (actionProcess == 1) {
                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                    $(".icheck:not(:checked)").each(function () {
                        form_data.push({name: this.name, value: '0'});
                    });
                    form_data.push({name: "parcelIds", value: parcelIds});
                    form_data.push({name: "service_id", value: checkService});
                    form_data.push({name: "action_type", value: 'all'});
                    $.ajax({
                        type: "POST",
                        url: "shipment_list_manage.php?action=create_manifest",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if (data.status == "succcess") {
                                gridParcel.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                } else {
                    swal("Sorry!", "Your selected parcel services are not matched OR already created manifest", "error");
                }
            }

            function regenerate_shipment() {
                $('#modal-regenerate').empty();
                $('#model-regenerate-header').empty();
                var ids = [];
                $('.consignmentIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                if (typeof ids !== 'undefined' && ids.length > 0) {
                    $('#model-regenerate-header').append(
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title">Re-Generate Shipment</h4>'
                    );
                    $('#modal-regenerate').append(
                        '<div class="row">' +
                        '<div class="col-md-3"><strong>HAWB</strong></div>' +
                        '<div class="col-md-3"><strong>TRACKING</strong></div>' +
                        '<div class="col-md-2"><strong>AMOUNT</strong></div>' +
                        '<div class="col-md-4"><strong>REFERENCE</strong></div>' +
                        '<div style="clear:both;"></div></div>'
                    );

                    $('.consignmentIds:checked').map(function () {
                        var valuesShipment = this.value;
                        var shipmentStringParts = valuesShipment.split("||");
                        $('#modal-regenerate').append(
                            '<div class="row"><input type="hidden" name="re_con_id[]" readonly value= "' + shipmentStringParts[0] + '" placeholder="ID" class="form-control">' +
                            '<div class="col-md-3"><input type="text" name="re_hawb[]" readonly value= "' + shipmentStringParts[1] + '" placeholder="HAWB NO" class="form-control"></div>' +
                            '<div class="col-md-3"><input type="text" name="re_awb[]" readonly value= "' + shipmentStringParts[2] + '" placeholder="TRACKING NO" class="form-control"></div>' +
                            '<div class="col-md-2"><input type="text" name="re_amount[]" value= "" placeholder="AMOUNT" class="form-control"></div>' +
                            '<div class="col-md-4"><input type="text" name="re_reference[]" value= "" placeholder="Reference" class="form-control"></div>' +
                            '<div style="clear:both;"></div></div>'
                        );
                    });
                    $("#action_form").val('re_generate');
                }
                $("#re_generate_model").modal('show');
            }

            function close_shipment() {
                $('#modal-regenerate').empty();
                $('#model-regenerate-header').empty();
                var ids = [];
                $('.consignmentIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                if (typeof ids !== 'undefined' && ids.length > 0) {
                    $('#model-regenerate-header').append(
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title">Close Shipment</h4>'
                    );
                    $('#modal-regenerate').append(
                        '<div class="row">' +
                        '<div class="col-md-3"><strong>Details</strong></div>' +
                        '<div style="clear:both;"></div></div>'
                    );
                    $('#modal-regenerate').append(
                        '<div class="row"><div class="col-md-12"><textarea rows="2" cols="350" style="resize:none;" name="cl_detail"  value= "" placeholder="Details" class="form-control"></textarea></div>' +
                        '<div style="clear:both;"></div></div>'
                    );
                    $('.consignmentIds:checked').map(function () {
                        var valuesShipment = this.value;
                        var shipmentStringParts = valuesShipment.split("||");
                        $('#modal-regenerate').append(
                            '<div class="row"><input type="hidden" name="re_con_id[]" readonly value= "' + shipmentStringParts[0] + '" placeholder="ID" class="form-control">' +
                            '</div>'
                        );
                    });
                    $("#action_form").val('close_shipnment');
                }
                $("#re_generate_model").modal('show');
            }

            function include_in_scanned() {
                $("#booking_date_msg").hide();
                var total_not_scannedshipments = 0;
                var not_scanned_ids = [];
                $(".shipment-check:checked").each(function (index, val) {
                    if ($(val).parents('tr').hasClass('not-scanned-shipment')) {
                        var consignment_id = $(val).data('consignment_id');
                        not_scanned_ids.push(consignment_id);
                        total_not_scannedshipments++;
                    }
                });
                if (total_not_scannedshipments > 0) {
                    var not_scanned_id_str = not_scanned_ids.join(",");
                    $("#update_not_scanned_shipments #consignment_ids").val(not_scanned_id_str);
                    $("#update_not_scanned_shipments").modal("show");
                } else {
                    swal("Sorry!", "No not scanned shipment selected.", "error");
                }
            }

            function select_dynamic_action() {
                var form_action = $("#action_form").val();
                if (form_action == 're_generate') {
                    swal({
                            title: "Are you sure?",
                            text: "Values are correct. It will reflect in invoices!",
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
                                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                                $(".icheck:not(:checked)").each(function () {
                                    form_data.push({name: this.name, value: '0'});
                                });
                                $.ajax({
                                    type: "POST",
                                    url: "shipment_list_manage.php?action=update_selected_action_re_generate",
                                    data: form_data,
                                    dataType: "json",
                                    success: function (data) {
                                        grid.getDataTable().ajax.reload();
                                        swal("Success!", data.message, "success");
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                } else if (form_action == 'close_shipnment') {
                    var form_data = $("#add_assign_bag_to_flight").serializeArray();
                    $(".icheck:not(:checked)").each(function () {
                        form_data.push({name: this.name, value: '0'});
                    });
                    $.ajax({
                        type: "POST",
                        url: "shipment_list_manage.php?action=update_selected_action_close_shipnment",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            grid.getDataTable().ajax.reload();
                            swal("Success!", data.message, "success");
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                }
            }

            function updateVolWeightCSV() {
                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                if ($("#update_vol_weight_msg").hasClass("alert-success")) {
                    $("#update_vol_weight_msg").removeClass("alert-success");
                }
                $("#update_vol_weight_msg").addClass("alert-info");
                $("#update_vol_weight_msg").html("please wait we are saving...");
                $("#update_vol_weight_msg").show();
                var file_data = $('#vol_weight_file').prop('files')[0];
                var form_data = new FormData();
                form_data.append('vol_weight_file', file_data);
                $.ajax({
                    url: 'shipment_list_manage.php?action=update_vol_weight',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if ($("#update_vol_weight_msg").hasClass("alert-info"))
                            $("#update_vol_weight_msg").removeClass("alert-info");
                        $("#update_vol_weight_msg").addClass("alert-success");
                        $("#update_vol_weight_msg").html("Updated successfully.");
                        $("#update_vol_weight_msg").show();
                    }
                });
            }

            function apply_bluk_price() {
                swal({
                        title: "Are you sure?",
                        text: "You want to apply bulk price",
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
                            var form_data = $("#add_assign_bag_to_flight").serializeArray();
                            $(".icheck:not(:checked)").each(function () {
                                form_data.push({name: this.name, value: '0'});
                            });
                            form_data.push({name: "action_type", value: 'all'});
                            $.ajax({
                                type: "POST",
                                url: "shipment_list_manage.php?action=save_tariff_consignment_charges",
                                data: form_data,
                                dataType: "json",
                                success: function (data) {
                                    grid.getDataTable().ajax.reload();
                                    swal("Success!", data.message, "success");
                                },
                                error: function () {
                                    //alert('error handing here');
                                }
                            });
                        }
                    });
            }

            function get_tariff_charges_detail(consignment_id) {
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=get_tariff_charges",
                    data: {'consignment_id': consignment_id},
                    dataType: "html",
                    success: function (data) {
                        $("#tariff_detail_html").html(data);
                        $("#tariff_price_detail_modal").modal("show");
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function get_user_detail(userId) {
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=get_user_details",
                    data: {'user_id': userId},
                    dataType: "html",
                    success: function (data) {
                        $("#account_detail_html").html(data);
                        $("#account_details").modal("show");
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function generate_invoice() {
                var today_date = getTodayDate();
                var form_data = $("#add_assign_bag_to_flight").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                form_data.push({name: 'invoice_date', value: $('#invoice_date').val()});
                var check_box_date = $('#check_box_date').iCheck('update')[0].checked;
                if (check_box_date) {
                    form_data.push({name: 'check_box_date', value: '1'});
                }
                var check_box_reference = $('#check_box_reference').iCheck('update')[0].checked;
                if (check_box_reference) {
                    form_data.push({name: 'check_box_reference', value: '1'});
                }
                var check_box_destination = $('#check_box_destination').iCheck('update')[0].checked;
                if (check_box_destination) {
                    form_data.push({name: 'check_box_destination', value: '1'});
                }
                var check_box_service = $('#check_box_service').iCheck('update')[0].checked;
                if (check_box_service) {
                    form_data.push({name: 'check_box_service', value: '1'});
                }
                var check_box_bank_details = $('#check_box_bank_details').iCheck('update')[0].checked;
                if (check_box_bank_details) {
                    form_data.push({name: 'check_box_bank_details', value: '1'});
                }
                $.ajax({
                    url: 'shipment_list_manage.php?action=add_invoice',
                    data: form_data,
                    type: 'post',
                    dataType: "json",
                    success: function (response) {
                        if (response.status == "success") {
                            grid.getDataTable().ajax.reload();
                            form_data.push({name: 'invoiceIds', value: response.invoiceIds});
                            $.ajax({
                                url: 'shipment_list_manage.php?action=get_invoice_pdf',
                                data: form_data,
                                type: 'post',
                                dataType: "json",
                                success: function (response) {
                                    $('#generate_invoice_model').modal('hide');
                                    var loop = response.path;
                                    var url = "";
                                    var link = "";
                                    var getUrl = window.location;
                                    var baseUrl = getUrl.protocol + "//" + getUrl.host + "/_assets/InvoicesFiles/pdf/";
                                    loop.forEach(function (element) {
                                        link += "<br />";
                                        url = baseUrl + element.FILENAME;
                                        link += "<a href='" + url + "' target='_blank' >Show Invoice Pdf</a>";
                                    });
                                    swal({
                                        type: 'success',
                                        html: true,
                                        title: "Success!",
                                        text: "Invoice has been generated successfully. " + link
                                    });
                                }
                            });
                        } else {
                            swal("Sorry!", response.message, "error");
                        }
                    }
                });
            }

            function getTodayDate() {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }
                today = yyyy + '-' + mm + '-' + dd;
                return today;
            }

            function get_all_consignment_parcel() {
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    gridParcel.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    gridParcel.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    gridParcel.setAjaxParam($(this).attr("name"), $(this).val());
                });
                gridParcel.setAjaxParam('group_by', 0);
                gridParcel.submitFilter();
            }

            function resetForm() {
                document.getElementById("add_assign_bag_to_flight").reset();
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_1_1" data-toggle="tab"> 1. Flight Information </a>
            </li>
            <li>
                <a href="#tab_1_2" data-toggle="tab"> 2. Direct Bagging </a>
            </li>
            <li>
                <a href="#tab_1_3" data-toggle="tab"> 3. Close Master </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="tab_1_1">
                <form id="add_assign_bag_to_flight" name="add_assign_bag_to_flight" method="post">
                    <input type="hidden" name="csv_action" id="csv_action" value="">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                Opened Flights
                                <?php $sessionUser = SessionManager::getUser();
                                echo Ddl::generateDDL('opened_flights', 'FlighInfoFilter', ' is_closed = 0 AND created_by = ' . $sessionUser->getId(), 'flight_number', 'id', '', 'class="form-control select2" required', 'Select Flight', '', 'opened_flights', 'Opened Flights'); ?>
                            </div>
                            <div class="tools">
                                <a href="" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div data-rail-color="blue" data-handle-color="blue" class="filter">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">MAWB</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-star-o"></i> </span>
                                                <input class="form-control form-filter" id="mawb" name="mawb"
                                                       type="text" placeholder="Master Number" value="" rel="tooltip"
                                                       data-original-title="MAWB">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Shipper's Name</label>
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                        class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="shipper_name"
                                                   name="shipper_name" placeholder="Shipper's Name" type="text" value=""
                                                   rel="tooltip" data-original-title="shipper_name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Shipper's Addressline 1</label>
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                        class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="shipper_address_line1"
                                                   name="shipper_address_line1" placeholder="Shipper's Address Line 1"
                                                   type="text" value="" rel="tooltip"
                                                   data-original-title="shipper_address_line1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Shipper's Addressline 2</label>
                                        <div class="input-group"><span class="input-group-addon"> <i
                                                        class="fa fa-ticket "></i> </span>
                                            <input class="form-control form-filter" id="shipper_address_line2"
                                                   name="shipper_address_line2" placeholder="Shipper's Address Line 2"
                                                   type="text" value="" rel="tooltip"
                                                   data-original-title="shipper_address_line2">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Ship From</label>
                                        <div class="form-group">
                                            <?php echo Ddl::generateCountryDDL('ship_from', '', 'id'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Flight Number</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-ticket "></i> </span>
                                                <input class="form-control form-filter" id="flight_number"
                                                       name="flight_number" placeholder="Flight Number" type="text"
                                                       value="" rel="tooltip" data-original-title="flight_number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Connecting Flight Number</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-ticket "></i> </span>
                                                <input class="form-control form-filter" id="connecting_flight_number"
                                                       name="connecting_flight_number"
                                                       placeholder="Connecting Flight Number" type="text" value=""
                                                       rel="tooltip" data-original-title="connecting_flight_number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Signature of issuing Carrier and it's agent
                                            Name</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-ticket "></i> </span>
                                                <input class="form-control form-filter" id="signature_carrier"
                                                       name="signature_carrier"
                                                       placeholder="Signature of issuing Carrier and it's agent Name"
                                                       type="text" value="" rel="tooltip"
                                                       data-original-title="signature_carrier">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Accounting Reference</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-globe"></i> </span>
                                                <input class="form-control form-filter" id="accounting_reference"
                                                       name="accounting_reference" placeholder="Accounting Reference"
                                                       type="text" value="" rel="tooltip"
                                                       data-original-title="accounting_reference">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Reference</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-globe"></i> </span>
                                                <input class="form-control form-filter" id="reference" name="reference"
                                                       placeholder="Reference" type="text" value="" rel="tooltip"
                                                       data-original-title="reference">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Custom Value</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa  fa-map-marker"></i> </span>
                                                <input class="form-control form-filter" id="custom_value"
                                                       name="custom_value"
                                                       placeholder="Custom Value" type="number" value="" rel="tooltip"
                                                       data-original-title="Custom Value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Select Carrier</label>
                                        <div class="form-group">
                                            <div id="carriers_div">
                                                <?php echo Ddl::generateArrayDDL('carriers', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'carriers'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Carriage Value</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-random"></i> </span>
                                                <input class="form-control form-filter" id="carriage_value"
                                                       name="carriage_value"
                                                       placeholder="Carriage Value" type="number" value="" rel="tooltip"
                                                       data-original-title="Carriage Value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Insurance Amount</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-thumb-tack"></i> </span>
                                                <input class="form-control form-filter" id="insurance_amount"
                                                       name="insurance_amount"
                                                       placeholder="Insurance Amount" type="number" value=""
                                                       rel="tooltip"
                                                       data-original-title="Insurance Amount">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Currancy</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa  fa-thumb-tack"></i> </span>
                                                <input class="form-control form-filter" id="currancy" name="currancy"
                                                       placeholder="Pieces" type="number" value="" rel="tooltip"
                                                       data-original-title="Currancy">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Rate Change</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="rate_change"
                                                       name="rate_change"
                                                       placeholder="Rate Change" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Rate Change">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Weight Format</label>
                                        <div class="form-group">
                                            <select id="weight_format" name="weight_format"
                                                    class="form-filter select2 form-control select2-hidden-accessible"
                                                    title="" placeholder="" tabindex="-1" aria-hidden="true"
                                                    data-original-title="">
                                                <option value="kg">KG</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Agent IATA Code</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="agent_iata_code"
                                                       name="agent_iata_code"
                                                       placeholder="Agent IATA Code" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Agent IATA Code">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Flight Date </label>
                                        <div class="form-group">
                                            <div class="input-group date-picker input-daterange">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="text" class="form-control form-filter" name="flight_date"
                                                       id="flight_date" value="" rel="tooltip"
                                                       data-original-title="Flight Date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Shipper's Co</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="shippers_co"
                                                       name="shippers_co"
                                                       placeholder="Shipper's Co" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Shipper's Co">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Account Number</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="account_number"
                                                       name="account_number"
                                                       placeholder="Account Number" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Account Number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="label-account">Airway Bill</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <textarea class="form-control form-filter" id="airway_bill"
                                                          name="airway_bill" placeholder="Airway Bill" rel="tooltip"
                                                          data-original-title="Airway Bill"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">HS Codes</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <textarea class="form-control form-filter" id="hs_codes"
                                                          name="hs_codes" placeholder="HS Codes" rel="tooltip"
                                                          data-original-title="HS Codes"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Airport of Depature</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="airport_of_depature"
                                                       name="airport_of_depature"
                                                       placeholder="Airport of Depature" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Airport of Depature">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-title">
                            <div class="caption"><i class="fa fa-search"></i>
                                Destination
                            </div>
                            <div class="tools">
                                <a href="" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div data-rail-color="blue" data-handle-color="blue" class="filter">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">City</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-globe"></i> </span>
                                                <input class="form-control form-filter" id="city" name="city"
                                                       placeholder="City" type="text" value="" rel="tooltip"
                                                       data-original-title="City">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Post Code</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa  fa-map-marker"></i> </span>
                                                <input class="form-control form-filter" id="postcode" name="postcode"
                                                       placeholder="Postcode" type="text" value="" rel="tooltip"
                                                       data-original-title="Postcode">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Country</label>
                                        <div class="form-group">
                                            <?php echo Ddl::generateCountryDDL('shipped_to', '', 'id'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Phone Number</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa  fa-thumb-tack"></i> </span>
                                                <input class="form-control form-filter" id="destination_phone_number"
                                                       name="destination_phone_number"
                                                       placeholder="Phone Number" type="text" value="" rel="tooltip"
                                                       data-original-title="Phone Number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="label-account">Company</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa  fa-thumb-tack"></i> </span>
                                                <input class="form-control form-filter" id="destination_company"
                                                       name="destination_company"
                                                       placeholder="Company" type="text" value="" rel="tooltip"
                                                       data-original-title="Company">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Address Line 1</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-random"></i> </span>
                                                <input class="form-control form-filter" id="destination_addressline1"
                                                       name="destination_addressline1"
                                                       placeholder="Address Line 1" type="text" value="" rel="tooltip"
                                                       data-original-title="Address Line 1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Address Line 2</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-random"></i> </span>
                                                <input class="form-control form-filter" id="destination_addressline2"
                                                       name="destination_addressline2"
                                                       placeholder="Address Line 2" type="text" value="" rel="tooltip"
                                                       data-original-title="Address Line 2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label-account">Consignee C/O</label>
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-credit-card"></i> </span>
                                                <input class="form-control form-filter" id="consignee_co"
                                                       name="consignee_co"
                                                       placeholder="Consignee C/O" type="text" value=""
                                                       rel="tooltip"
                                                       data-original-title="Consignee C/O">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="text-align:center;">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary filter-submit" name="btn_go"
                                                    id="btn_add_flight_detail" value="Submit"> Submit
                                            </button>&nbsp;
                                            <button type="button" class="btn btn-default filter-cancel" name="btn_rest"
                                                    value="Reset" onclick="resetForm()"> Reset
                                            </button>
                                            <!-- <button type="button" class="btn btn-danger" id="btn_more_option" name="btn_more_option" onclick="$('#more-option').toggle();"> More Option </button>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="tab_1_2">
                <div class="tab-pane fade active in" id="tab_1_1">
                    <!--                    <form id="add_parcel_to_bag" name="add_parcel_to_bag" method="post">-->
                    <div class="portlet light">
                        <div class="portlet-body">
                            <div data-rail-color="blue" data-handle-color="blue" class="filter">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="label-account">Bag Number: 1 Item number / Tracking
                                            Number</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input class="form-control form-filter" id="tracking_number"
                                                       name="tracking_number"
                                                       type="text" placeholder="Tacking Number" value=""
                                                       rel="tooltip"
                                                       data-original-title="Tacking Number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group" style="float:left;margin-right: 15px;">
                                            <div class="icheck-inline">
                                                <label class="label-account">
                                                    <input id="last_bag" name="last_bag" type="checkbox"
                                                           class="form-filter icheck"
                                                           data-checkbox="icheckbox_flat-green" value="1"/> Last Bag
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--                    </form>-->
                </div>
            </div>
            <div class="tab-pane fade" id="tab_1_3">
                <p> Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out
                    mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade.
                    Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard
                    locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony.
                    Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever
                    gluten-free, carles pitchfork biodiesel fixie
                    etsy retro mlkshk vice blog. Scenester cred you probably haven't heard of them,
                    vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray
                    yr. </p>
            </div>
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

    public function renderHead()
    {
        ?>
        <style type="text/css">
            #tracking_number_for_status_box {
                display: none;
            }

            .table > thead > tr > th {
                vertical-align: top;
            }

            #select2-service-results .select2-results__option[aria-disabled=true] {
                display: none;
            }

            .left-check {
                float: left;
                padding-top: 5px;
            }

            .border-top-none {
                border-top: none;
            }

            .tracking_type_field .select2-container--bootstrap .select2-selection {
                border-bottom-right-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
                border-top-left-radius: 0px !important;
            }

            .tracking_type_field textarea {
                border-top-right-radius: 0px !important;
                border-top-left-radius: 0px !important;
                border-bottom-left-radius: 0px !important;
            }

            table.dataTable td.sorting_1, table.dataTable td.sorting_2, table.dataTable td.sorting_3, table.dataTable th.sorting_1, table.dataTable th.sorting_2, table.dataTable th.sorting_3 {
                background: none !important;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td {
                color: #000;
            }

            .table-hover > tbody > tr:hover, .table-hover > tbody > tr:hover > td a {
                color: #000;
            }

            .line-height-2 {
                line-height: 2;
            }

            .input-daterange input {
                text-align: left;
            }

            @media only screen and (min-width: 983px) {
                .margin-top-md-30 {
                    margin-top: 30px;
                }
            }

            .nav-pills, .nav-tabs {
                margin-bottom: 0;
            }

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