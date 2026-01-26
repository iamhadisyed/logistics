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
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'country.class',
    'countryfilter.class',
    'tracking.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'userservicesrouting.class',
    'consignmentbillinghold.class',
    'consignmentbillingholdfilter.class',
    'consignmentbillingholdlog.class',
    'consignmentbillingholdlogfilter.class',
    'consignmentchargeslog.class',
    'consignmentchargeslogfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentstatuslog.class',
    'consignmentstatuslogfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'consignmentbaggingmapping.class',
    'consignmentbaggingmappingfilter.class',
    'currencyfilter.class',
    'currency.class',
    'invoicedetail.class',
    'invoicedetailfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'invoicebankdetails.class',
    'paymentshistoryfilter.class',
    'excelshipmentsreports.class',
    'warehouse.class',
    'warehousefilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'invoicetemplates.class',
    'invoicetemplatesfilter.class',
    'bagging.class',
    'baggingfilter.class',
    
    

]);


class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $consignment_filter = "";
    private $consignmentChargesTypesObj = "";
    private $search_result = 1;
    private $agentFilterObj = array();
    private $excelFilterData = array();
    private $uaccount = 0;
    private $countryDataArray = [];
    private $servicesDataArray = [];
    private $customerChargesTypeArray  = [];
    private $agentChargesTypeArray  = [];
    private $includeChargesColumn = false;
    private $includeAgentChargesColumn = false;
    private $csvSelectedColumns = [];
    private $grandTotal = 0;
    private $subAccountArr = [];

    protected function init() {
        
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Shipments"
        );
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            header("Location: index.php");
        }

        $consignmentChargesTypesFilter = new ConsignmentChargesTypesFilter();
        $consignmentChargesTypesFilter->addFieldFilter('is_extra_charge', 0);
        $consignmentChargesTypesFilter->addFieldFilter('status', 1);
        $consignmentChargesTypesFilter->addFieldFilter('is_delete', 0);
        $this->consignmentChargesTypesObj = $consignmentChargesTypesFilter->getList();

        $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
        $ids = implode(",", $userAccountArry);
        $userFilter = new UserFilter();
        $userIds = $userFilter->getUserIdsFromAccountIds($ids);
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
        $conObj = $consignmentFilter->getColumnList("   c.agent_id");
        $agentIds = array();
        foreach ($conObj as $obj) {
            $agentIds[] = $obj->getAgentId();
        }
        $agentFilter = new AgentDataFilter();
        $agentFilter->addAgentIdInFilter($agentIds);
        $this->agentFilterObj = $agentFilter->getColumnList('   a.agent_name,a.agent_code');
        if (Permissions::checkFilePermission('hide_subaccount') && isset($this->form_vars['user_account_id'])) {
            $accountObj = new CustomerAccount();
            $searchConsignmentAccountId = $this->form_vars['user_account_id'];
            $this->subAccountArr = $accountObj->getSubAccountsArrayShowConsignmentAccount($this->user->getUserAccountId(),$searchConsignmentAccountId);
        }
        /*
         * DataTable handlings
         */

        if (isset($_GET['action']) && $_GET['action'] == "consignment_list") {
            $this->consignment_filter = new ConsignmentFilter();
            /*
             * Column filter
             * For search
             */
            $consignmentDataArr = array();
            $TotalNumberPieces = 0;
            $TotalWeight = 0;
            $iTotalRecords = 0;
            $sEcho = $this->form_vars['draw'];
            $getZeroPriced = 0;
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $this->filter_form($this->form_vars);
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
                    $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                    if (trim($dataTableColumnName) == 'account')
                        $dataTableColumnName = 'user_account';
                    if (trim($dataTableColumnName) == 'service_type')
                        $dataTableColumnName = 'service_name';
                    if (trim($dataTableColumnName) == 'shipment_status')
                        $dataTableColumnName = 'consignment_status';
                    if (trim($dataTableColumnName) == 'country_iso_code')
                        $dataTableColumnName = 'country_name';
                }
                /*
                 * Pagination Logic Implemented
                 * 
                 */

                $ReturnResult = $this->consignment_filter->getRowCountSumPiecesSumWeight();
                foreach ($ReturnResult as $res) {
                    $TotalNumberPieces = $res->getNumberPieces();
                    $TotalWeight = $res->getWeight();
                }
                /* commented as div showing zero priced is removed
                $getZeroPricedObj = $this->consignment_filter->getZeroPricedCount($userAccountArry, false );
                $getZeroPriced = 0;
                foreach ($getZeroPricedObj as $obj) {
                    $getZeroPriced = $obj->getId();
                }*/
                /*
                 * Set pagination & Encode data into Json form to return to DataTable
                 */

                $iDisplayLength = (int)($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
                
                $iDisplayStart = (int)($_REQUEST['start']);
                $sEcho = (int)($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                //$end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $this->consignment_filter->setRowsPerPage($iDisplayLength);
                $this->consignment_filter->setOffset($iDisplayStart);
               $iTotalRecords = $this->consignment_filter->getShipmentPagingListOpt(false,false,false,true); 
               
                $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false);
                // $iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
                foreach ($consignmentObjs as $consignmentObj) {

                    //if (in_array($this->user->getUserType(), array(User::USER_TYPE_FINANCE, User::USER_TYPE_ACCOUNT, User::USER_TYPE_ADMIN))) {
                    $cssClass = 'normal-shipment';

                    $date_booked = $consignmentObj->getDateScanned();
                    if ($date_booked == '' || $date_booked == '0000-00-00 00:00:00' || $date_booked == '1970-01-01 00:00:00') {
                        $cssClass = 'not-scanned-shipment';
                    }

                    /* if ($consignmentObj->getConsignmentBillingHoldId() > 0) {
                      $cssClass = 'onhold';
                      } else */
                    
                    if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == 'HOLD') {
                        $cssClass = 'onholdpink';
                    } else if ($consignmentObj->getReinvoices() == "1") {
                        $cssClass = 'ready-to-reinvoice';
                    } else if ($consignmentObj->getCreditId() > 0) {
                        $cssClass = 'credit-shipment';
                    } else if ($consignmentObj->getChargesInvoiceId() > 0) {
                        $cssClass = 'invoice-ship';
                    }
                    
                    // else if (trim(strtoupper($consignmentObj->getRemoteCharges())) == "1") {
                    //   $cssClass = 'remote-area-shipment';
                    //}
                    else if ($consignmentObj->getSenderChecked() == "1") {
                        $cssClass = '';
                    } else if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == "RECYCLED") {
                        $cssClass = 'recycled-shipment';
                    } else {
                        $cssClass = 'normal-shipment';
                    }
                    // } else {
                    /* $cssClass = 'normal-shipment';
                      if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == 'BOOKED') {
                      $cssClass = 'remote-area-shipment';
                      } else if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == 'DELIVERED') {
                      $cssClass = 'credit-shipment';
                      } else if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == "RECEIVED") {
                      $cssClass = 'label-created-shipment';
                      } else if (strtoupper(trim($consignmentObj->getConsignmentStatus())) == 'HOLD') {
                      $cssClass = 'onhold';
                      } else {
                      $cssClass = 'normal-shipment';
                      }
                      if ($this->form_vars['status'] == "recycled") {
                      $cssClass = 'recycled-shipment';
                      } */
                    //}

                    
                    $returnIcon = '';
                    if ($consignmentObj->getConsignmentType() == 'return')
                        $returnIcon = '<i class="font-red-mint fa fa-reply"></i>&nbsp;';
                    


                    $action = '';
                    $action .= '<div class="action_check_box">';
                    if (Permissions::checkFilePermission('show_check_box')) {
                        $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select[]"  data-consignment_id="' . $consignmentObj->getId() . '" id="multi_select_' . $consignmentObj->getId() . '"  value="' . $consignmentObj->getId() . "||" . $this->removeChar($consignmentObj->getHawb()) . "||" . $this->removeChar($consignmentObj->getAwb()) . '"  class="group-checkable consignmentIds shipment-check" /><span></span></label>';
                    } else {
                        $action .= '<input type="hidden" value="" data-consignment_id="' . $consignmentObj->getId() . '" >';
                    }
                    $action .= '</div>';
                    $consignmentArr['option'] = $action;
                    $externalUserData = ($this->uaccount > 0) ? "&uaccount=" . base64_encode($this->uaccount) : '';
                    $hawb = "";
                    $hawb .= '<a href="shipment_view_manage.php?id=' . $consignmentObj->getId() . $externalUserData . '" id="hawb-' . $consignmentObj->getId() . '" target="_blank" >';
                    $hawb .= $consignmentObj->getHawb();
                    $hawb .= '</a>';

                    $trackingNumber = "";
                    $trackingNumber .= '<a href="tracking.php?tracking_number=' . $consignmentObj->getAwb() . '" target="_blank">';
                    $trackingNumber .= $consignmentObj->getAwb();
                    $trackingNumber .= '</a>';

                    $shipment_status = Consignment::getShipnmentStatus($consignmentObj->getShipmentStatus());

                    $invoiceObj = new Invoices($consignmentObj->getChargesInvoiceId());
                    if ($consignmentObj->getRemoteCharges() == 1) {
                        $remoteArea = '<span class="label label-sm remote-area-shipment bg-font-blue-chambray line-height-2"> Yes</span>';
                    } else {
                        $remoteArea = "No";
                    }

                    $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentObj->getId());

//                    $consignmentChargesFilter = new ConsignmentChargesFilter();
//                    $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentObj->getId());
//                    $consignmentChargesFilter->addFieldFilter("     cc.cost_type", 'customer');
//                    $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "'");
//                    $consignmentChargesFilterObj = $consignmentChargesFilter->getColumnList("sum(cc.cost) as cost, cost_currency, cc.charge_type_id,cc.cost_type",5000,true);
//                    $totalTariffCost = 0;
//                    if (count($consignmentChargesFilterObj) > 0) {
//                        $totalTariffCost = $consignmentChargesFilterObj[0]->getCost();
//                        $currency = $consignmentChargesFilterObj[0]->getCostCurrency();
//
//                    }
                    $consignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentObj->getId(),$user_account_id);
                    $totalTariffCost = $consignmentCharges['cost'];
                    $currency = $consignmentCharges['currency_code'];
                    $valueType = "";
                    if ($consignmentObj->getHvLv() == "HV") {
                        $valueType = "High";
                    } else if ($consignmentObj->getHvLv() == "MV") {
                        $valueType = "Medium";
                    } else if ($consignmentObj->getHvLv() == "LV") {
                        $valueType = "Low";
                    }

                    if ($consignmentObj->getConsignmentBillingHoldId() > 0) {
                        $hold = '<span class="label label-sm onhold bg-font-blue-chambray line-height-2"> Yes</span>';
                    } else {
                        $hold = "No";
                    }
                    $shipmentType = "";
                    if ($consignmentObj->getShipmentType() == "C") {
                        $shipmentType = "Collection";
                    } else if ($consignmentObj->getShipmentType() == "D") {
                        $shipmentType = "Dispatched";
                    } else if ($consignmentObj->getShipmentType() == "P") {
                        $shipmentType = "Product";
                    }
                    $dateBooked = "";
                    if ($consignmentObj->getDateBooked() != '' && $consignmentObj->getDateBooked() > 0) {
                        $dateBooked = formatDate(date("d M Y", $consignmentObj->getDateBooked()));
                    }
                    $countryObj = new Country($consignmentObj->getCountryId());
                    $userAccountObj = new CustomerAccount($user_account_id);
                    if (empty($currency) &&  !empty($userAccountObj->getBillingCurrency())) {
                        $currency = $userAccountObj->getBillingCurrency();
                    }
                    $totalTariffCostFormate = $this->getCurrencyFormat($totalTariffCost, $currency);
                    $serviceObj = new Services($consignmentObj->getServiceId());
					$carrierResult = new Carrier($serviceObj->getCarrierId());
                    $productImage = $productname = "";
                    if($serviceObj->getIsCustomized() == 1 && Permissions::checkFilePermission('show_service_for_accounts')){
                        if($consignmentObj->getServiceUsing() > 0){
                            $productObj = new Services($consignmentObj->getServiceUsing());
                            $productname = $productObj->getName();
                            $carrierProductResult = new Carrier($productObj->getCarrierId());
                            $productImage = '<img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierProductResult->getLogo() . '" title="' . $carrierProductResult->getCarrier() . '" alt="' . $carrierProductResult->getCarrier() . '">&nbsp;&nbsp;';
                        }
                    }
                    $consignmentAccount = $consignmentObj->getUserAccount();
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $searchConsignmentAccountId = $this->form_vars['user_account_id'];
                        $consignmentAccountId = $consignmentObj->getUserAccountId();
                        $accountObj = new CustomerAccount();
                        $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$this->subAccountArr,$searchConsignmentAccountId);
                    }
                    $consignmentArr['account_mawb_dateDispatched'] = $returnIcon.'<a href="javascript:;" onclick="get_user_detail(' . $consignmentObj->getUserId() . ')" >' . $consignmentAccount . '</a><br />' . $consignmentObj->getMawb() . '<br />' . $dateBooked;
                    $consignmentArr['status_hawbNumber_trackingNumber'] = $shipment_status . '<br />' . $hawb . '<br />' . $trackingNumber;
                    $consignmentArr['location'] = $consignmentObj->getChargesInvoiceId()."-".$shipmentType . '<br />' .utf8_encode(  $consignmentObj->getCity() ). '<br />' . utf8_encode(  $countryObj->getName());
                    $consignmentArr['weight_volWeight'] = $consignmentObj->getWeight() . '<br />' . $consignmentObj->getVolWeight();
                    $consignmentArr['pieces'] = '<span style="display:none;">' . $cssClass . '</span>' . $consignmentObj->getNumberPieces();
                    $consignmentArr['valueType'] = $valueType;
                    //$consignmentArr['serviceCode_serviceName_product'] = $serviceObj->getCode() . '<br />' . $serviceObj->getName() . '<br />' . $productname;
                    $consignmentArr['serviceCode_serviceName_product'] = '<img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierResult->getLogo() . '" title="' . $carrierResult->getCarrier() . '" alt="' . $carrierResult->getCarrier() . '">&nbsp;&nbsp;'. $serviceObj->getCode() . '<br /><img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierResult->getLogo() . '" title="' . $carrierResult->getCarrier() . '" alt="' . $carrierResult->getCarrier() . '">&nbsp;&nbsp;'   . $serviceObj->getName() . '<br />' . $productImage . $productname;
					$consignmentArr['remotearea_onHold'] = $remoteArea . '<br />' . $hold;
                    $consignmentArr['invoiceNumber'] = $invoiceObj->getInvoiceNo().
                                ($consignmentObj->getIsCustomerBillable() == 0 ? '<span class="label label-sm label-danger"> Non-invoiceable Shipment</span>' : '' );
                    $consignmentArr['tariff'] = '<a href="javascript:;" data-id="' . $consignmentObj->getId() . '" onclick="get_tariff_charges_detail(' . $consignmentObj->getId() . ')" > ' . $totalTariffCostFormate . ' </a>';
                    $consignmentDataArr[] = $consignmentArr;
                }
            }
            
            $consignmentDataarr['data'] = $consignmentDataArr;
            $consignmentDataarr['draw'] = $sEcho;
            $consignmentDataarr['recordsTotal'] = $iTotalRecords;
            $consignmentDataarr['recordsFiltered'] = $iTotalRecords;
            $consignmentDataarr['totalNumberPieces'] = $TotalNumberPieces;
            $consignmentDataarr['totalWeight'] = $TotalWeight;
            //$consignmentDataarr['getZeroPriced'] = $getZeroPriced;
            echo json_encode($consignmentDataarr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "parcel_list") {
            $this->consignment_filter = new ConsignmentFilter();
            /*
             * Column filter
             * For search
             */
            $parcelDataArr = [];
            $parcelArr = [];
            $TotalNumberPieces = 0;
            $TotalWeight = 0;
            $iTotalRecords = 0;
            $sEcho = $this->form_vars['draw'];
            $getZeroPriced = 0;
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->filter_form($this->form_vars);

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
                    $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                    if (trim($dataTableColumnName) == 'account')
                        $dataTableColumnName = 'user_account';
                    if (trim($dataTableColumnName) == 'service_type')
                        $dataTableColumnName = 'service_name';
                    if (trim($dataTableColumnName) == 'shipment_status')
                        $dataTableColumnName = 'consignment_status';
                    if (trim($dataTableColumnName) == 'country_iso_code')
                        $dataTableColumnName = 'country_name';
                }

                /*
                 * Set pagination & Encode data into Json form to return to DataTable
                 */

                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;

                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                //$end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $this->consignment_filter->setRowsPerPage($iDisplayLength);
                $this->consignment_filter->setOffset($iDisplayStart);
                $parcelObjs = $this->consignment_filter->getShipmentPagingListOpt(false, true);
                $iTotalRecords = $this->consignment_filter->getShipmentPagingListOpt(false, true,false,true);
                
                //$iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
                foreach ($parcelObjs as $parcelObj) {

                    $trackingNumber = "";
                    $trackingNumber .= '<a href="tracking.php?tracking_number=' . $parcelObj->getTrackingNumber() . '" target="_blank">';
                    $trackingNumber .= $parcelObj->getTrackingNumber();
                    $trackingNumber .= '</a>';

                    $shipment_status = Consignment::getShipnmentStatus($parcelObj->getShipmentStatus());

                    $action = '';
                    $action .= '<div class="action_check_box">';
                    $already_manifest = "<label class='label label-danger'>No</label>";
                    $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                    $manifestEntityMappingFilter->addFieldFilter("      mem.entity_id", $parcelObj->getParcelId());
                    $manifestEntityMappingFilter->addFieldFilter("      mem.manifest_entity_type", 'p');
                    $manifestEntityMappingFilterObj = $manifestEntityMappingFilter->getList("mem.id,mem.entity_id,manifest_id");
                    $manifestView = "";
                    if (count($manifestEntityMappingFilterObj) > 0) {
                        $manifestView = '<a href="op_manifest_list.php?manifest_id=' . $manifestEntityMappingFilterObj[0]->getManifestId() . '" target="_blank" class="btn btn-sm btn-success">View Manifest</a>';
                        $already_manifest = "<label class='label label-success'>Yes</label> <input type='hidden' name='manifestIds[]' value='" . $manifestEntityMappingFilterObj[0]->getEntityId() . "' >";
                        $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select_parcel[]"  data-parcel_id="' . $parcelObj->getParcelId() . '" data-service_id="' . $parcelObj->getServiceId() . '" id="multi_select_' . $parcelObj->getParcelId() . '"  value="' . $parcelObj->getId() . "||" . $parcelObj->getParcelId() . "||" . $parcelObj->getServiceId() . "||" . $manifestEntityMappingFilterObj[0]->getId() . '"  class="group-checkable parcelIds shipment-check" /><span></span></label>';
                    } else {
                        $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select_parcel[]"  data-parcel_id="' . $parcelObj->getParcelId() . '" data-service_id="' . $parcelObj->getServiceId() . '" id="multi_select_' . $parcelObj->getParcelId() . '"  value="' . $parcelObj->getId() . "||" . $parcelObj->getParcelId() . "||" . $parcelObj->getServiceId() . '"  class="group-checkable parcelIds shipment-check" /><span></span></label>';
                    }
                    $action .= '</div>';
                    $parcelArr['option'] = $action;

                    $parcelArr['tracking_number'] = $trackingNumber;
                    $parcelArr['service'] = $parcelObj->getServiceName();
                    $parcelArr['dims'] = $parcelObj->getLength() . "<br /> " . $parcelObj->getWidth() . "<br /> " . $parcelObj->getHeight();
                    $parcelArr['weight'] = $parcelObj->getWeight();
                    $parcelArr['status'] = $shipment_status;
                    $parcelArr['already_manifest'] = $already_manifest;
                    $parcelArr['view_manifest'] = $manifestView;
                    $parcelDataArr[] = $parcelArr;
                }
            }
            $consignmentDataarr['data'] = $parcelDataArr;
            $consignmentDataarr['draw'] = $sEcho;
            $consignmentDataarr['recordsTotal'] = $iTotalRecords;
            $consignmentDataarr['recordsFiltered'] = $iTotalRecords;
            $consignmentDataarr['totalNumberPieces'] = $TotalNumberPieces;
            $consignmentDataarr['totalWeight'] = $TotalWeight;
            $consignmentDataarr['getZeroPriced'] = $getZeroPriced;
            echo json_encode($consignmentDataarr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $return = [];
            $uniqueServiceIds = [];
            if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                $carrierFilterObj = new CarrierFilter();
                $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
                $carrier_option = "<option value=''>Select Carrier</option>";
                foreach ($carriers as $cr) {
                    $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
                }

                $serviceFilterObj = new ServiceFilter();
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
                $service_option = "<option value=''>Select Service</option>";
                foreach ($services as $sr) {
                    $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
                }
                $return['services_option'] = $service_option;
                $return['carrier_option'] = $carrier_option;
            } else {
                
                $user_account_id = $this->form_vars['user_account_id'];
                $consignmentList = array();
                $serviceIds = array();
                if (!empty($user_account_id)) {
                    $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                    $ids = implode(",", $userAccountArry);
                    $userFilter = new UserFilter();
                    $userIds = $userFilter->getUserIdsFromAccountIds($ids);
                    $consignmentFilterObj = new ConsignmentFilter();
                    $consignmentFilterObj->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                    $consignmentList = $consignmentFilterObj->getColumnList("DISTINCT c.service_id, c.customized_service_id " );
                    $serviceIds = array();
                    foreach ($consignmentList as $con) {
                        $serviceIds[] = $con->getServiceId();
                        if(!empty($con->getCustomizedServiceId())){
                         $serviceIds[] = $con->getCustomizedServiceId();
                        }
                    }
                } 
                $uniqueServiceIds =  array_unique($serviceIds) ;
                $serviceFilterObj = new ServiceFilter();
                
                if (!empty($uniqueServiceIds) && count($uniqueServiceIds)>0) {
                    $serviceFilterObj->addFilterIn("    ser.id", $uniqueServiceIds);
                }
                $serviceFilterObj->AddOrderBy('ser.name','ASC');
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id" );
                $carrierIds = array();
                foreach ($services as $ser) {
                    if (!in_array($ser->getCarrierId(), $carrierIds)) {
                        $carrierIds[] = $ser->getCarrierId();
                    }
                }
                
                
                $carrierFilterObj = new CarrierFilter();
                $carrierFilterObj->addFilterIn("    c1.id", $carrierIds);
                $carrierFilterObj->AddOrderBy('c1.carrier_display_name',true);
                $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
                $service_option = "<option value=''>Select Service</option>";
                foreach ($services as $sr) {
                    $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
                }
                $carrier_option = "<option value=''>Select Carrier</option>";
                foreach ($carriers as $cr) {
                    $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
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
                $return['services_option'] = $service_option;
                $return['carrier_option'] = $carrier_option;
                $return['agent_option'] = $agent_option;
            }
            echo json_encode($return);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "save_consignment_charges") {
            if ($this->form_vars['search_result'] > 0) {
                $this->consignment_filter = new ConsignmentFilter();
                $this->filter_form($this->form_vars);

                $consignmentObjs = $this->consignment_filter->getColumnList('   c.user_id, u.user_account_id ');
                $consignment_ids = array();
                if (count($consignmentObjs) > 0) {
                    foreach ($consignmentObjs as $obj) {
                        $consignment_ids[] = $obj->getId();
                    }
                }
                ConsignmentCharges::updateChargesByConsignmentIds($consignment_ids, $this->form_vars);
            }
            $msg = 'Consignment price has been added successfully.';
            echo $msg;
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "update_billing_hold") {
            $action_type = $this->form_vars['action_type'];
            $consignment_ids = array();
            $outputArray = array();
            $status = "";
            if (isset($action_type) && $action_type == "selected") {
                $arrayOfShipment = $this->form_vars['multi_select'];
                if (count($arrayOfShipment) > 0) {
                    foreach ($arrayOfShipment as $arrayValue) {
                        $arrayPart = array();
                        $arrayPart = explode('||', $arrayValue);
                        $consignment_ids[] = $arrayPart[0];
                    }
                }
                $status = $this->form_vars['change_consignment'];
            } else {
                if ($this->form_vars['search_result'] > 0) {
                    $this->consignment_filter = new ConsignmentFilter();
                    $this->filter_form($this->form_vars);
                    $consignmentObjs = $this->consignment_filter->getColumnList('c.user_id');
                    if (count($consignmentObjs) > 0) {
                        foreach ($consignmentObjs as $obj) {
                            $consignment_ids[] = $obj->getId();
                        }
                    }
                }
                $status = $this->form_vars['hold_type'];
            }
            $added_by = $this->user->getId();
            $date_added = time();
            $update_by = $this->user->getId();
            $date_update = time();
            $reason = $this->form_vars['reason'];
           
            if (count($consignment_ids) > 0) {
                foreach ($consignment_ids as $id) {
                    $checkInvoiced = Consignment::checkConsignmentInvoiced($id);
                   
                    if ($checkInvoiced == 0) {
                        $userAccountIdFrom = $this->user->getUserAccountId();
                        $userAccountIdTo = Consignment::getConsignmentUserAccountIdForPricing($id);

                        $consignmentHoldFilter = new ConsignmentBillingHoldFilter();
                        $consignmentHoldFilter->where(['consignment_id' => $id, 'user_account_id_from' => $userAccountIdFrom, 'user_account_id_to' => $userAccountIdTo]);
                        $consignmentHoldFilter->delete();
                        if ($status == "hold") {
                            $consignmentHold = new ConsignmentBillingHold();
                            $consignmentHold->setConsignmentId($id);
                            $consignmentHold->setUserAccountIdFrom($userAccountIdFrom);
                            $consignmentHold->setUserAccountIdTo($userAccountIdTo);
                            $consignmentHold->setReasonForHold($reason);
                            $consignmentHold->setAddedBy($added_by);
                            $consignmentHold->setDateAdded($date_added);
                            $consignmentHold->setUpdatedBy($update_by);
                            $consignmentHold->setDateUpdated($date_update);
                            $consignmentHold->save();
                        }
                        $consignmentHoldLog = new ConsignmentBillingHoldLog();
                        $consignmentHoldLog->setConsignmentId($id);
                        $consignmentHoldLog->setUserAccountIdFrom($userAccountIdFrom);
                        $consignmentHoldLog->setUserAccountIdTo($userAccountIdTo);
                        $consignmentHoldLog->setStatus($status);
                        $consignmentHoldLog->setReason($reason);
                        $consignmentHoldLog->setAddedBy($added_by);
                        $consignmentHoldLog->setDateAdded($date_added);
                        $consignmentHoldLog->setUpdatedBy($update_by);
                        $consignmentHoldLog->setDateUpdated($date_update);
                        $consignmentHoldLog->save();
                    }
                }
            }
            $outputArray['status'] = 'success';
            $outputArray['message'] = "Consignment billing hold has been updated successfully.";
            echo json_encode($outputArray);
            die;
        }

        
        
        if (isset($_GET['action']) && $_GET['action'] == "update_oba_docket") {
            $action_type = $this->form_vars['action_type'];
            $consignment_ids = array();
            $outputArray = array();
            $status = "";
            if (isset($action_type) && $action_type == "selected") {
                $arrayOfShipment = $this->form_vars['multi_select'];
                if (count($arrayOfShipment) > 0) {
                    foreach ($arrayOfShipment as $arrayValue) {
                        $arrayPart = array();
                        $arrayPart = explode('||', $arrayValue);
                        $consignment_ids[] = $arrayPart[0];
                    }
                }
                $status = $this->form_vars['change_consignment'];
            } else {
                if ($this->form_vars['search_result'] > 0) {
                    $this->consignment_filter = new ConsignmentFilter();
                    $this->filter_form($this->form_vars);
                    $consignmentObjs = $this->consignment_filter->getColumnList('c.user_id');
                    if (count($consignmentObjs) > 0) {
                        foreach ($consignmentObjs as $obj) {
                            $consignment_ids[] = $obj->getId();
                        }
                    }
                }
                //echo $this->form_vars['search_result'];
                //die;
                
            }
            $added_by       = $this->user->getId();
            $date_added     = time();
            $update_by      = $this->user->getId();
            $date_update    = time();
            $oba_docket_no  = $this->form_vars['oba_docket_no'];
            if(trim($oba_docket_no) == '')
            {
                $outputArray['status'] = 'error';
                $outputArray['message'] = "Please enter Consignment Bag / OBA / Docket number.<br>".$fileMessage;
            }
            else if(count($consignment_ids)>0){

                //CHECK BAG NUMBER ALREADY EXIST 
                $consignmentData = Bagging::getAlreadyAssignedBag($consignment_ids);
                $exportData = "";
                $fileName = "";
                $fileMessage  = "";
                
                if(count($consignmentData)>0)
                {
                    $exportData = "Tracking Number,Bag Number \r\n";
                    foreach($consignmentData as $cData){
                        $exportData .= $cData->getAwb().",".$cData->getBagId()."\r\n";    
                    }
                    $fileNameDataPart = "csv/". time() . ".csv";
                    $fileName = SETTING_DIR_ASSETS . $fileNameDataPart;
                    file_put_contents($fileName, $exportData);
                    if(trim($fileName)!= ''){
                        $fileMessage = 'Docket number already assigned to '.count($consignmentData).' shipment(s), Please <a href="'.SETTING_URL_ASSETS.$fileNameDataPart.'">click here</a> to download';
                    }
            
                }
                // GET SHIPMENT ALREADY IN THE BAG NUMBER
                $baggingFilter = new BaggingFilter();
                $baggingFilter->addFilter("      bagnumber = '".$oba_docket_no."'");
                $bagnumberlist  = $baggingFilter->getList(false);
                if(count($bagnumberlist)>0){
                    foreach ($consignment_ids as $consignmentId)
                        Bagging::insertBagData($oba_docket_no,$consignmentId, $this->user->getId() );
                        
                }else{
                    if (count($consignment_ids) > 0) {
                        Bagging::insertBagInfo($oba_docket_no,$consignment_ids[0] , $this->user->getId());
                        foreach ($consignment_ids as $consignmentId) 
                            Bagging::insertBagData($oba_docket_no,$consignmentId , $this->user->getId());
                    }
                }
                
                $outputArray['status'] = 'success';
                $outputArray['message'] = "Consignment Bag / OBA / Docket number has been updated successfully.<br>".$fileMessage;
            }
            
            echo json_encode($outputArray);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == "update_manaual_pod") {
            $emailContentForPods = '';
            $pod_value = $this->form_vars["pod_reason"];
            $emailContentForPods = trim($this->form_vars["email_content_pod"]);
            $send_email_to = trim($this->form_vars["send_email_to"]);
            $date_delivery = $this->form_vars['date_delivery'];
            $track_point = $this->form_vars['track_point'];
            $carrierDescription = $this->form_vars['description'];
            $pod_name = $this->form_vars['pod_name'];
            $pod_file_name = $this->form_vars['pod_file'];
            //$pod_file_path = $this->form_vars['pod_file_path'];
            
            if ($date_delivery != "") {
                $date_delivery = date("Y-m-d H:i:s", strtotime($date_delivery));
            }
            $barcodelist = trim($this->form_vars['number']);
            
            $bulkTrackingNumberValidatedArray = array();
            if (count($this->form_vars['multi_select']) > 0) {
                foreach ($this->form_vars['multi_select'] as $multiAwb) {
                    $trackingNumber = explode('||', $multiAwb);
                    $bulkTrackingNumberValidatedArray[] = $trackingNumber[2];
                }
            } else if ($barcodelist != "") {
                $bulk_trackingNumber = explode("<br />", nl2br($barcodelist));
                if (count($bulk_trackingNumber) > 0) {
                    $bulk_trackingNumber = array_unique($bulk_trackingNumber);
                    foreach ($bulk_trackingNumber as $trackingNumer) {
                        $bulkTrackingNumberValidatedArray[] = str_replace("\r\n","",$trackingNumer);
                    }
                }
            } else if ($this->form_vars['search_result'] > 0) {
                $this->consignment_filter = new ConsignmentFilter();
                $this->filter_form($this->form_vars);
                $consignmentList = $this->consignment_filter->getColumnList('c.awb');
                if (count($consignmentList) > 0) {
                    foreach ($consignmentList as $c) {
                        if (trim($c->getAwb()) != '') {
                            $bulkTrackingNumberValidatedArray[] = $c->getAwb();
                        }
                    }
                }
            } else {
                $outputArray['status'] = 'error';
                $outputArray['message'] = "Please enter Tracking Numbers or select filter to update the records.";
                $this->error[] = "Please enter Tracking Numbers or select filter to update the records";
            }
            if (sizeof($bulkTrackingNumberValidatedArray) > 0) {
                $bulkConsignmentPodData = array();
                $consingmentDataFilter = new ConsignmentFilter();
                $consingmentDataFilter->setFilter(" and awb in ('" . implode("','", $bulkTrackingNumberValidatedArray) . "') and awb <> '' and consignment_status not in ('delievered','closed') ");
                $consignmentDataKList = $consingmentDataFilter->getColumnList('c.user_id, c.awb, c.hawb, c.date_booked, c.reference, c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.postcode, ua.country, c.number_pieces, c.weight, c.description, c.currency, c.value', 10000);
                if (count($consignmentDataKList) > 0) {
                    foreach ($consignmentDataKList as $casc) {
                        $bulkConsignmentPodData[$casc->getUserId()][] = array(
                            'tracking_number' => $casc->getAwb() . '<br>' . $casc->getHawb(),
                            'date_booked' => $casc->getDateBooked(),
                            'reference' => $casc->getReference(),
                            'address' => array($casc->getAddressLine1(), $casc->getAddressLine2(), $casc->getAddressLine3(), $casc->getCity(), $casc->getPostcode(), $casc->getCountry(),),
                            'number_pieces' => $casc->getNumberPieces(),
                            'weight' => $casc->getWeight(),
                            'description' => $casc->getDescription(),
                            'currency' => $casc->getCurrency(),
                            'value' => $casc->getValue()
                        );
                    }
                }
                $imageName = "";
                @$pod_image_file = $_FILES['pod_image'];
                if (!empty(@$pod_image_file['name'])) 
                {
                    $file_name = @$pod_image_file['name'];
                    $path_parts = pathinfo($file_name);
                    $ext = strtolower($path_parts['extension']);
                    $basename = $path_parts['basename'];
                    $imageName = time() . "_" . $basename;
                    
                    $relPath = '../_assets/images/pod_images/'.$imageName;
                    if (!file_exists("../_assets/images/pod_images/"))
                        @mkdir("../_assets/images/pod_images/", 0775);
                    if (!move_uploaded_file(@$pod_image_file['tmp_name'], $relPath)) {
                        $imageName = "";
                    }
                }
                $ParcelFilter = new ParcelFilter();
                $ParcelFilter->bulkupdateCourierStatus($bulkTrackingNumberValidatedArray, Tracking::$oneworld_consignment_code_mapping[$pod_value]);
                $sessionUser = SessionManager::getUser();
                TrackingData::AddVirtualTrackingToScanParcels($bulkTrackingNumberValidatedArray, $sessionUser, $pod_value, $date_delivery, $emailContentForPods, $track_point, $imageName, '', '', $pod_name, $carrierDescription);
//                if (Tracking::$oneworld_consignment_code_mapping[$pod_value] == "14") {
                $setParam = "consignment_status =  '" . Consignment::$database_status_array[Tracking::$oneworld_consignment_code_mapping[$pod_value]] . "', shipment_status = '" . Tracking::$oneworld_consignment_code_mapping[$pod_value] . "' ";
                $wherecaluse = "awb in ('" . implode("','", $bulkTrackingNumberValidatedArray) . "') and awb <> ''";
                $c_filer = new ConsignmentFilter();
                $c_filer->updateFunction($setParam, $wherecaluse);
//                }
                if (trim($emailContentForPods) != '' && $send_email_to == "YES") {
                    $bulkTrackingNumberValidatedArray = array_unique($bulkTrackingNumberValidatedArray);
                    /**
                     * Email Fire to all customer for POD...
                     *
                     * */
                    $userEmailData = array();
                    $userFilter = new UserFilter();
                    $userFilter->addFilter(" id in ( select distinct user_id from consignment where awb in ( '" . implode("','", $bulkTrackingNumberValidatedArray) . "' ) and awb <> '' ) ");
                    //echo "<br><br><br><br><br><br><br><br>";
                    $userListAccountNumber = $userFilter->getColumnList('user_account_id, email, alternative_email ');
                    if (count($userListAccountNumber) > 0) {
                        foreach ($userListAccountNumber as $userData) {
                            if (trim($userData->getEmail()) != '') {
                                $userEmailData[$userData->getId()][] = $userData->getEmail();
                            }
                            if (trim($userData->getAlternativeEmail()) != '') {
                                $userEmailData[$userData->getId()][] = $userData->getAlternativeEmail();
                            }
                        }
                    }
                    if (count($userEmailData) > 0) {
                        foreach ($userEmailData as $dataAccount => $dataDetails) {
                            $emailOfTheCustomer = array_unique($dataDetails);
                            $to = "cs@oneworldexpress.com";
                            $subject = 'Your parcel(s) latest status: ' . strtoupper($pod_value);
                            $headers = "From: cs@oneworldexpress.com" . "\r\n";
                            $headers .= "Reply-To: cs@oneworldexpress.com" . "\r\n";
                            $headers .= "Return-Path: cs@oneworldexpress.com" . "\r\n";
                            $headers .= "Bcc: " . implode(',', $emailOfTheCustomer) . "\r\n";
                            $headers .= "Content-type: text/html\r\n";

                            $emialContent = 'Dear Customer,<br /><br />Your shipment status details<br /><br />';
                            $emialContent .= nl2br($emailContentForPods) . '<br /><br />';
                            $emialContent .= '<table border="0" cellspacing="1" cellpadding="0" width="850" style="width:637.5pt">
														<tbody>
															<tr>
																<td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																	<p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Tracking Number / Hawb No</span></b></p>
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
															</tr>';
                            if (count($bulkConsignmentPodData[$dataAccount]) > 0) {
                                foreach ($bulkConsignmentPodData[$dataAccount] as $consignmentDataToFire) {
                                    $emialContent .= '
																	<tr>
																		<td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['tracking_number'] . '</span></p>
																		</td>
																		<td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['date_booked'] . '</span></p>
																		</td>
																		<td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['reference'] . '</span></p>
																		</td>
																		<td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal">
																				<span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">
																					' . implode('<br>', $consignmentDataToFire['address']) . '
																				</span>
																			</p>
																		</td>
																		<td width="50" valign="top" style="width:37.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['number_pieces'] . '</span></p>
																		</td>
																		<td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['weight'] . '</span></p>
																		</td>
																		<td width="110" valign="top" style="width:82.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['description'] . '</span></p>
																		</td>
																		<td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentDataToFire['currency'] . ' ' . $consignmentDataToFire['value'] . '</span></p>
																		</td>
																		<td width="150" valign="top" style="width:112.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
																			<p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . strtoupper($pod_value) . '</span></p>
																		</td>
																	</tr>';
                                }
                            }
                            $emialContent .= '      </tbody>
																			</table>';

                            $emialContent .= '<br /><br />Thanks and regards,<br />One World Express';


                            //	$emialContent .= "<div><strong>Your selected emails are here, Please verify.</strong>".."</div>" ;

                            if (mail($to, $subject, $emialContent, $headers)) {
                                $this->error[] = "Email Send Successfully";
                                $outputArray['status'] = 'success';
                                $outputArray['message'] = "Email Send Successfully";
                            } else {
                                $outputArray['status'] = 'error';
                                $outputArray['message'] = "Unable to send email successfully. Please contact to itsupport@oneworldexpress.com";
                                $this->error[] = "Unable to send email successfully. Please contact to itsupport@oneworldexpress.com";
                            }
                        }
                    } else {
                        $outputArray['status'] = 'error';
                        $outputArray['message'] = "Unable to find email addresses for selected shipments.";
                        $this->error[] = "Unable to find email addresses for selected shipments.";
                    }
                }
                if (!count($outputArray)) {
                    $outputArray['status'] = 'success';
                    $outputArray['message'] = "Statues is update successfully";
                }
            } else {
                $outputArray['status'] = 'success';
                $outputArray['message'] = "No shippment found against tracking";
            }
            echo json_encode($outputArray);
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "update_selected_action_re_generate") {
            $consignmentInformation = $this->form_vars['multi_select'];
            $outputArray = array();
            foreach ($consignmentInformation as $key => $dataConsignment) {
                $consignmentInfoPart = explode('||', $dataConsignment);
                $CId = $consignmentInfoPart[0]; // consignmentId	
                $invoiceAmount = $this->form_vars['re_amount'][$key];
                $invoiceReference = $this->form_vars['re_reference'][$key];

                $reInvoiceShipment = new ConsignmentFilter();
                $reInvoiceShipment->addFieldFilter('id', $CId);
                $reInvoiceShipmentData = $reInvoiceShipment->getColumnList('c.hawb, c.awb, c.reinvoices ');
                if (count($reInvoiceShipmentData) > 0) {
                    $consignmentSData = new Consignment();
                    $consignmentSData->setReinvoices('1');
                    $consignmentSData->save();

                    $ct_filter = new ConsignmentFilter();
                    $ct_filter->setFilter(" id= '" . DbAccess3::escape($CId) . "' AND reinvoices = '0'");
                    $ct_filter->accountUpdateQuery('reinvoices', '1');

//                    $invoiceDetailData = new InvoiceDetail();
//                    $invoiceDetailData->setConsignmentId($reInvoiceShipmentData[0]->getId());
//                    $invoiceDetailData->setHawb($reInvoiceShipmentData[0]->getHawb());
//                    $invoiceDetailData->setBasicCharges($invoiceAmount);
//                    $invoiceDetailData->setAmount($invoiceAmount);
//                    $invoiceDetailData->setReference($invoiceReference);
//                    $invoiceDetailData->setDateCreated(date('Y-m-d h:i:s', time()));
//                    $invoiceDetailData->setAddedBy($this->user->getId());
//                    $invoiceDetailData->save();
                }
            }
            $outputArray['status'] = 'success';
            $outputArray['message'] = "You have successfully save consignment for re-invoice";
            echo json_encode($outputArray);
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "update_selected_action_close_shipnment") {
            $consignmentInformation = $this->form_vars['multi_select'];
            $consignment_id_array = array();
            foreach ($consignmentInformation as $key => $dataConsignment) {
                $consignmentInfoPart = explode('||', $dataConsignment);
                $consignment_id_array[] = $consignmentInfoPart[0];
            }
            if (sizeof($consignment_id_array) > 0) {
                $consignment_id = implode("','", $consignment_id_array);
                $ct_filter = new ConsignmentFilter();
                $ct_filter->addIdArrayFilter($consignment_id_array);
                $c_list = $ct_filter->getColumnList("c.id,c.awb");

                $trackingNumberArray = array();
                if (count($c_list) > 0) {
                    foreach ($c_list as $clist) {
                        $trackingNumberArray[] = $clist->getAwb();

                        $ConsignmentLog = new ConsignmentLog();
                        $ConsignmentLog->createlog("Shipment Closed ", $clist->getId());
                    }
                    if (count($trackingNumberArray) > 0) {
                        Consignment::bulkUpdate("shipment_status = '" . Consignment::STATUS_CLOSE . "', consignment_status='" . Consignment::$database_status_array[Consignment::STATUS_CLOSE] . "'", " id IN ('" . $consignment_id . "')");
                        TrackingData::AddVirtualTrackingToScanParcels($trackingNumberArray, $this->user, "Closed", "", $this->form_vars['cl_detail'], "Closed");
                    }
                }
            }
            $outputArray['status'] = 'success';
            $outputArray['message'] = "You have successfully save consignment for close shipnment";
            echo json_encode($outputArray);
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "save_tariff_consignment_charges") {
            $actionType = $this->form_vars['action_type'];
            if ($actionType == "selected") {
                $consignmentInformation = $this->form_vars['multi_select'];
                $consignment_id_array = array();
                foreach ($consignmentInformation as $key => $dataConsignment) {
                    $consignmentInfoPart = explode('||', $dataConsignment);
                    $consignment_id_array[] = $consignmentInfoPart[0];
                }
                $consignment_filter = new ConsignmentFilter();
                $consignment_filter->addFilterIn("      c.id", $consignment_id_array, "consignmentfilter");
                $consignmentObjs = $consignment_filter->getColumnList('c.id,c.user_id,c.invoice_id,c.awb,c.mawb,c.service_id,c.shipment_status,c.date_label_created,c.date_delivered,c.company,c.address_line_1,c.address_line_2,c.address_line_3,c.city,c.country_id,c.postcode,c.number_pieces,c.weight,c.charge_weight,c.vol_weight,c.value,c.currency,c.remote_charges,c.hawb,c.reference,c.date_booked,c.is_doc,c.consignment_status,c.is_dead_weight_chargable');
            } else {
                $this->consignment_filter = new ConsignmentFilter();
                $this->filter_form($this->form_vars);
                $consignmentObjs = $this->consignment_filter->getColumnList('c.user_id,cc.invoice_id as charges_invoice_id,c.awb,c.mawb,c.service_id,c.shipment_status,c.date_label_created,c.date_delivered,c.company,c.address_line_1,c.address_line_2,c.address_line_3,c.city,c.country_id,c.postcode,c.number_pieces,c.weight,c.charge_weight,c.vol_weight,c.value,c.currency,c.remote_charges,c.hawb,c.reference,c.date_booked,c.is_doc,c.consignment_status,c.is_dead_weight_chargable');
            }
            $message = array();
            if (count($consignmentObjs) > 0) {
                foreach ($consignmentObjs as $obj) {
                    $consignmentId = $obj->getId();
                    // check already invoiced
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
                        $weight = $consignmentObj->getWeight();
                        $numberPieces = $consignmentObj->getNumberPieces();
                        $postcode = $consignmentObj->getPostcode();
                        $status = '';

                        $mysqli = new mysqli($host, $user, $password, $db);
                        if ($mysqli->connect_errno) {
                            $message['status'] = 'error';
                            $message['message'] = "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                        }
                        $isCheck = UserServicesRoutingFilter::isOwnUserContract($serviceId, $userAccountId);
                        $parameter = 0;
                        if ($isCheck) {
                            $parameter = 1;
                        }
                        $sql = "CALL tariff($consignmentId,'customer', '" . $userAccountId . "', '" . $parameter . "',".$this->user->getId().",@S_STATUS,@S_MESSAGE)";
                        if (!($res = $mysqli->query($sql))) {
                            $message['status'] = 'error';
                            $message['message'] = "ERROR||CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
                        }
                        $mysqli->close();
						/* update account balance */
						CustomerAccount::updateBalance($userAccountId);
						/******************************/
                    }
                }
                if (count($message) == 0) {
                    $message['status'] = 'success';
                    $message['message'] = " Bulk tariff pricing has been successfully updated.";
                }
            } else {
                $message['status'] = 'error';
                $message['message'] = "No consignment found to update bluk price";
            }
            echo json_encode($message);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == 'update_vol_weight') {
            $output =   [
                "status"=>true,
                "message"=>""
            ];
            if (isset($_FILES['vol_weight_file']) && !empty($_FILES['vol_weight_file']['name'])) {
                $file_parts = pathinfo($_FILES['vol_weight_file']['name']);
                if($file_parts['extension'] == 'csv')
                {
                 
                $sourcePath = $_FILES['vol_weight_file']['tmp_name'];
                $imageName = date('Ymd') . '-' . time() . "_" . $_FILES['vol_weight_file']['name'];
                $targetPath = _ASSETS_PATH."vol_weight/" . $imageName;
                $targetFolderPath = _ASSETS_PATH."vol_weight/";
                        if (!file_exists($targetFolderPath))
                            mkdir($targetFolderPath, 0755, true);
                $outputFile = $imageName;
                if (move_uploaded_file($sourcePath, $targetPath)) {
                    $row = 1;
                    $outputResultArry =  []; 
                    $outputResultFileContect = "";
                    if (($handle = fopen($targetPath, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if($row == 1){
                                $num = count($data);
                                $outputResultArry[0] =  "Status" ;
                                foreach ($data as $dataIndex=>$dataValue) {
                                    if(in_array(trim(strtolower($dataValue)) , array('waybill number','tracking_number','tracking number'))){
                                        
                                        $consignmentNumberIndex = $dataIndex;//. "<br />\n";
                                        $outputResultArry[1] = $dataValue; 
                                    } else if(in_array(trim(strtolower($dataValue)) , array('shipper reference','hawb','order_reference_number','order reference number'))){
                                        //if(trim($dataValue) == 'Shipper Reference') {
                                            $orderReferenceIndex = $dataIndex;
                                        $outputResultArry[2] = $dataValue;
                                    } else if(in_array(trim(strtolower($dataValue)) , array('parcel_tracking_number','piece id','tracking_number','parcel tracking number'))){
                                            $carrierParcelIndex = $dataIndex;
                                            $outputResultArry[3] = $dataValue;
                                    } else if(trim($dataValue) == 'Operations Calculated Volumetric Weight') {
                                            $carrierVolWeightIndex = $dataIndex;
                                            //$outputResultArry[] = $dataValue;
                                    } else if(in_array(trim(strtolower($dataValue)) , array('piece calculated height','height'))){
                                            $carrierheightIndex = $dataIndex;
                                            $outputResultArry[4] = $dataValue;
                                    } else if(in_array(trim(strtolower($dataValue)) , array('piece calculated length','length'))){
                                            $carrierlegnthIndex = $dataIndex;
                                            $outputResultArry[5] = $dataValue;
                                    } else if(in_array(trim(strtolower($dataValue)) , array('piece calculated width','width'))){
                                            $carrierwidthIndex  = $dataIndex;
                                            $outputResultArry[6] = $dataValue;
                                    } else if(in_array(trim(strtolower($dataValue)) , array('piece calculated weight','weight'))){
                                            $carrierWeightIndex = $dataIndex;
                                            $outputResultArry[7] = $dataValue;
                                    } 
                                }
                                
                               $row++; 
                            } else {
                                
                                $consignmentNumber      =   $data[$consignmentNumberIndex];//. "<br />\n";
                                $orderReference         =   $data[$orderReferenceIndex];
                                $orderReference         =   $data[$orderReferenceIndex];
                                $pieceId                 =   $data[$carrierParcelIndex];
                                
                                $carrierWeight          =   $data[$carrierWeightIndex];
                                $carrierWeight          =   $data[$carrierWeightIndex];
                                $carrierVolWeight       =   $data[$carrierVolWeightIndex];
                                $carrierheight          =   $data[$carrierheightIndex];
                                $carrierlegnth          =   $data[$carrierlegnthIndex];
                                $carrierwidth           =   $data[$carrierwidthIndex];
                                $consignmentFilterList        =   new ConsignmentFilter();
                                $consignmentFilterList->addFilter("    c.awb = '".$consignmentNumber."'","filter");
                                $consignmentFilterList->addFilter("    c.hawb = '".$orderReference."'","filter");
                                $consignmentFilterList->addFilter("    p.tracking_number = '".$pieceId."'","filter");
                                $consignmentFilterList->addJoin("     parcel p "," c.id = p.consignment_id ", " INNER ");
                                $consignmentFilterData  =   $consignmentFilterList->getListNew("c.id,p.id 'parcel_id',"
                                        . "     c.awb 'consignment_tracking_numb', c.update_weight,c.weight,"
                                                                                . "p.tracking_number 'parcel_tracking_numb',p.length 'parcel_length',"
                                                                                . " p.width  'parcel_width', p.height 'parcel_height', c.vol_demonimator ");
                                if(count($consignmentFilterData)>0){
                                    foreach($consignmentFilterData as $index=>$dataShipment){
                                        $parcelUpdateVolWeight = "UPDATE consignment INNER JOIN parcel ON "
                                                . "consignment.id = parcel.consignment_id "
                                                . "SET  parcel.length = '".$carrierheight."', "
                                                . "     parcel.width = '".$carrierlegnth."', "
                                                . "     parcel.height= '".$carrierwidth."' "
                                                . " WHERE "
                                                . "     parcel.id =  '".$dataShipment->getParcelId()."'  ";
                                        
                                            $consignmentUpdateVolWeight = "UPDATE consignment   "
                                                 . "SET   consignment.update_weight= ".$dataShipment->getWeight().", "
                                                . "     consignment.weight= if(consignment.weight <= '".$carrierWeight."','".$carrierWeight."',consignment.weight), "
                                                . "     consignment.vol_weight = (select sum(length * width * height / ".$dataShipment->getVolDemonimator().") FROM parcel where consignment_id = consignment.id) "
                                                . "     consignment.charge_weight = if(is_dead_weight_chargable = 1, consignment.weight , if(consignment.weight > consignment.vol_weight, consignment.weight, consignment.vol_weight)) "
                                                    . " WHERE "
                                                . "     consignment.id =  '".$dataShipment->getId()."'  ";
                                        DbAccess3::runQuery($parcelUpdateVolWeight);
                                        DbAccess3::runQuery($consignmentUpdateVolWeight);
                                        //echo $consignmentUpdateVolWeight;
                                      //  echo "<pre>";
                                       // echo $parcelUpdateVolWeight;
                                       // die;
                                    $result = "updated";
                                        break;
                                    }
                                } else {
                                    $result = "not found";
                                } 
                                
                                $outputResultArry = [
                                    $result ,
                                    $consignmentNumber,
                                    $orderReference,
                                    $pieceId,
                                    $carrierheight,
                                    $carrierlegnth,
                                    $carrierwidth,
                                    $carrierWeight
                                        ];
                            }
                            $outputResultFileContect .= implode(",",$outputResultArry)."\r\n";
                            
                        }
                        fclose($handle);
                        $fileFolderUrl = _ASSETS_URL."vol_weight/" . date("Y-m-d") . "/";
                        $fileFolderPath = _ASSETS_PATH."vol_weight/" . date("Y-m-d") . "/";
                        if (!file_exists($fileFolderPath))
                            mkdir($fileFolderPath, 0755, true);
                        $fileNameToPutContent = "vol_weight_".time().".csv";
                        file_put_contents($fileFolderPath.$fileNameToPutContent, $outputResultFileContect);
                        
                        $output =   [
                            "status"=>true,
                            "message"=>"Weight file successfully processed,"
                            . " Please <a href=\"".$fileFolderUrl.$fileNameToPutContent."\" target=\"_blank\" class=\"btn btn-primary btn-xs\"> click here</a> to download file for status."
                         ];
                    }
                    else {
                        $output =   [
                            "status"=>false,
                            "message"=>"Weight csv file could not open"
                         ];
                        
                    }
                } else {
                    //$msg .= "POD image uploading fails.";
                    $output =   [
                    "status"=>false,
                    "message"=>"Weight csv file failed to upload"
                     ];
                }
                 } else {
                    $output =   [
                        "status"=>false,
                        "message"=>"Please select csv file to update records"
                         ];
                }
            }
            else {
                $output =   [
                "status"=>false,
                "message"=>"Please select csv file to update records"
                 ];
            }
            echo json_encode($output);
            exit; 
        }
		else if (isset($_GET['action']) && $_GET['action'] == 'update_shipnment_status') {
            $output = [];
            $shipnmentStatus = $this->form_vars['shipnment_status'];
            $shipnmentStatusComment = $this->form_vars['shipment_status_comment'];
            if ($this->form_vars['search_result'] > 0) {
                $this->consignment_filter = new ConsignmentFilter();
                $this->filter_form($this->form_vars);
                $consignmentObjs = $this->consignment_filter->getColumnList('c.id');
                $consignmentIds = [];
                if (count($consignmentObjs) > 0) {
                    foreach ($consignmentObjs as $consignmentObj) {
                        $consignmentIds[] = $consignmentObj->getId();
//                        $consignment = new Consignment($consignmentObj->getId());
//                        $consignment->setConsignmentStatus(Consignment::$database_status_array[$shipnmentStatus]);
//                        $consignment->setShipmentStatus($shipnmentStatus);
//                        $consignment->save();
//                        $parcelFilter = new ParcelFilter();
//                        $parcelFilter->addFieldFilter("    consignment_id", $consignmentObj->getId());
//                        $parcelFilterObjs = $parcelFilter->getColumnList("      p.consignment_id");
//                        if (count($parcelFilterObjs) > 0) {
//                            foreach ($parcelFilterObjs as $parcelFilterObj) {
//                                $parcel = new Parcel($parcelFilterObj->getId());
//                                $oldStatus = $parcel->getParcelStatusCode();
//                                $parcel->setParcelStatusCode($shipnmentStatus);
//                                $parcel->save();
//
//                                $date_added = time();
//                                $added_by = $this->user->getId();
//                                $consignmentStatusLog = new ConsignmentStatusLog();
//                                $consignmentStatusLog->setParcelId($parcelFilterObj->getId());
//                                $consignmentStatusLog->setOldStatus($oldStatus);
//                                $consignmentStatusLog->setNewStatus($shipnmentStatus);
//                                $consignmentStatusLog->setMessage($shipnmentStatusComment);
//                                $consignmentStatusLog->setAddedBy($added_by);
//                                $consignmentStatusLog->setDateAdded($date_added);
//                                $consignmentStatusLog->save();
//                            }
//                        }
                    }
                }
                Consignment::consignmentStatusUpdate($consignmentIds,$shipnmentStatus,$shipnmentStatusComment);
                $output['status'] = "success";
                $output['message'] = "Status of consignment is update successfully";
            } else {
                $output['status'] = "error";
                $output['message'] = "Not found any tracking number to update";
            }
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars['csv_action']) && $this->form_vars['csv_action'] == 'download_shipnments') {
            //Get Shipnment data
            $this->consignment_filter = new ConsignmentFilter();
            $this->filter_form($this->form_vars);

            $loginUser = SessionManager::getUser();

            $consignmentObjs = $this->consignment_filter->getColumnList('c.user_id,cc.invoice_id AS charges_invoice_id,c.awb,c.mawb,c.service_id,c.shipment_status,c.date_scanned,c.date_label_created,c.date_delivered,c.company,c.address_line_1,c.address_line_2,c.address_line_3,c.city,c.country_id,c.postcode,c.number_pieces,c.weight,c.charge_weight,c.vol_weight,c.value,c.currency,c.remote_charges,c.hawb,c.reference,c.date_booked,c.is_doc,c.consignment_status,ua.user_account');
            $heading = ["Account", "Invoice No", "Order Ref No", "Tracking Number", "MAWB", "Bag No",
                "Service Code", "Service Name", "Service Type", "Product Name", "REF", "Consignment Status",
                "Date Label Created", "Date Scanned", "Date Dispatched", "Date Delivered", "Collection Country", 
                "Collection  Postcode", "Company", "Address", "City", "Country", "Postcode", "Number of Pieces",
                "Chargable Weight", "Weight", "Vol Weight", "DOX/NDX", "Value", "Currency", "Remotearea", "Length", 
                " Width", "Height"];

            if (!empty($consignmentObjs)) {
                $returnString = "";
                $fileName = "Shipnment_" . time();
                foreach ($heading as $h) {
                    $returnString .= $h . ",";
                }
                $consignmentChargeCustomerType = new ConsignmentChargesTypesFilter();
                $consignmentChargeCustomerType->addFieldFilter("   charge_type", "customer");
                $consignmentChargeCustomerType->addOrFieldFilter("  charge_type", "both");
               // $consignmentChargeCustomerType->addFieldFilter("    is_extra_charge", 0);
                $consignmentChargeTypeCustomerObj = $consignmentChargeCustomerType->getList();
                foreach ($consignmentChargeTypeCustomerObj as $obj) {
                    $returnString .= cleanCsvCall($obj->getTitle()) . ",";
                }
                foreach ($consignmentObjs as $obj) {
                    $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($obj->getId());
                    $returnString .= "\r\n";
                    // $userObj = new User($obj->getUserId());
                    $returnString .= cleanCsvCall($obj->getUserAccount()) . ",";
                    $invoiceObj = new Invoices($obj->getChargesInvoiceId());
                    $returnString .= cleanCsvCall($invoiceObj->getInvoiceNo()) . ",";
                    $returnString .= cleanCsvCall($obj->getHawb(), 'int') . ",";
                    $returnString .= cleanCsvCall($obj->getAwb(), 'int') . ",";
                    $returnString .= cleanCsvCall($obj->getMawb(), 'int') . ",";
                    $consignmentBaggingMappingObj = new ConsignmentBaggingMapping($obj->getId());
                    $returnString .= cleanCsvCall($consignmentBaggingMappingObj->getBagid()) . ",";
                    $serviceObj = new Services($obj->getServiceId());
                    $returnString .= cleanCsvCall($serviceObj->getCode()) . ",";
                    $returnString .= cleanCsvCall($serviceObj->getName()) . ",";
                    $returnString .= cleanCsvCall($serviceObj->getType()) . ",";
                    $returnString .= "  ,";
                    $returnString .= cleanCsvCall($obj->getReference()) . ",";
                    $returnString .= cleanCsvCall($obj->getConsignmentStatus()) . ",";
                    $returnString .= ($obj->getDateLabelCreated() != '' && $obj->getDateLabelCreated() > 0 ? date("Y-m-d", $obj->getDateLabelCreated()) : '') . ",";
                    $returnString .= ($obj->getDateScanned() != '' && $obj->getDateScanned() > 0 ? date("Y-m-d", $obj->getDateScanned()) : '') . ",";
                    $returnString .= ($obj->getDateBooked() != '' && $obj->getDateBooked() > 0 ? date("Y-m-d", $obj->getDateBooked()) : '') . ",";
                    $returnString .= ($obj->getDateDelivered() != '' && $obj->getDateDelivered() > 0 ? date("Y-m-d", $obj->getDateDelivered()) : '') . ",";
                    $returnString .= "  ,";
                    $returnString .= "  ,";
                    $returnString .= cleanCsvCall($obj->getCompany()) . ",";
                    $returnString .= cleanCsvCall($obj->getAddressLine1()) . " " . cleanCsvCall($obj->getAddressLine2()) . " " . cleanCsvCall($obj->getAddressLine3()) . ",";
                    $returnString .= cleanCsvCall($obj->getCity()) . ",";
                    $countryObj = new Country($obj->getCountryId());
                    $returnString .= cleanCsvCall($countryObj->getName()) . ",";
                    $returnString .= cleanCsvCall($obj->getPostcode()) . ",";
                    $returnString .= cleanCsvCall($obj->getNumberPieces(), 'int') . ",";
                    if ($obj->getisDeadWeightChargable() > 0) {
                        $returnString .= cleanCsvCall($obj->getWeight(), 'int') . ",";
                    } else {
                        $returnString .= cleanCsvCall($obj->getChargeWeight(), 'int') . ",";
                    }

                    $returnString .= cleanCsvCall($obj->getWeight(), 'int') . ",";
                    $returnString .= cleanCsvCall($obj->getVolWeight(), 'int') . ",";
                    if ($obj->getIsDoc() == 1) {
                        $doc = "DOX";
                    } else {
                        $doc = "NDX";
                    }
                    $returnString .= $doc . ",";
                    $returnString .= cleanCsvCall($obj->getValue()) . ",";
                    $returnString .= cleanCsvCall($obj->getCurrency()) . ",";
                    if ($obj->getRemoteCharges() == 1) {
                        $remoteArea = "Yes";
                    } else {
                        $remoteArea = "No";
                    }
                    $returnString .= $remoteArea . ",";
                    $parcelFilter = new ParcelFilter();
                    $parcelFilter->addConsignmentIdFilter($obj->getId());
                    $parcelFilterObj = $parcelFilter->getList();
                    if (count($parcelFilterObj) > 0) {
                        $returnString .= cleanCsvCall($parcelFilterObj[0]->getLength(), 'int') . ",";
                        $returnString .= cleanCsvCall($parcelFilterObj[0]->getWidth(), 'int') . ",";
                        $returnString .= cleanCsvCall($parcelFilterObj[0]->getHeight(), 'int') . ",";
                    } else {
                        $returnString .= "  ,";
                        $returnString .= "  ,";
                        $returnString .= "  ,";
                    }
                    foreach ($consignmentChargeTypeCustomerObj as $CCTCobj) {
                        $consignmentChagesFilter = new ConsignmentChargesFilter();
                        $consignmentChagesFilter->addFieldFilter("    consignment_id", $obj->getId());
                        $consignmentChagesFilter->addFieldFilter("    charge_type_id", $CCTCobj->getId());
                        $consignmentChagesFilter->addFieldFilter("    account_id", $user_account_id);
                        $consignmentChagesFilterObj = $consignmentChagesFilter->getList();
                        if (count($consignmentChagesFilterObj) > 0) {
                            $returnString .= cleanCsvCall($consignmentChagesFilterObj[0]->getCost()) . ",";
                        } else {
                            $returnString .= "  ,";
                        }
                    }
                }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $returnString;
            }
            exit();
        }
        
        ///////////////csv download column changes/////////////
        if (isset($this->form_vars['csv_action']) && $this->form_vars['csv_action'] == 'download_csv_columns') {
                $result = ' <label>Select CSV columns</label><br />
                            <input  name="all_csv_columns" value="Y" type="checkbox" id="make-switch-columns"  onchange="checkColumns(this)"  data-size="small"  data-on-text="SELECT ALL" check data-off-text="DESELECT ALL" data-on-color="primary" data-off-color="danger" />
                            <br /><br />
                            <div class="table-responsive"> <table class="table " style="margin-bottom: 0px;"><tbody><tr>';
                $action_ext = $this->form_vars['action_ext'];
                $columnsData = $this->getCsvColumnsDefault($action_ext);
                $i=1;
                foreach ($columnsData as $key=>$col) {
                    //$result .= "<td><input type='checkbox' name='csv_columns[]' value='$col'  >$col</td> ";
                     $result .= '<td><label><div class="icheck-inline"><input class="csvCheckbox " checked="checked" type="checkbox" name="csv_columns[]" value="'.$key.'"  >'.$col.'</div></label></td>' ;
                    if($i%4==0){
                                        $result .= '</tr><tr>';
                    }
                    $i++;
                }
                  $result .= '</tr>';
                  if($action_ext=='DOWNLOAD_CSV'){
                     $result .= '<tr>'
                             . '<td colspan="1"><label><div class="icheck-inline"><input class="csvCheckbox " checked="checked" type="checkbox" name="csv_charges_columns" value="1"  ><b>Customer Charges Columns</b></div></label></td>';
                      $result .=
                              '<td colspan="1"><label><div class="icheck-inline"><input class="csvCheckbox " checked="checked" type="checkbox" name="csv_acharges_columns" value="1"  ><b>Agent Charges Columns</b></div></label></td>'
                                . '<td colspan="1"><label><div class="icheck-inline"><input class="csvCheckbox " checked="checked" type="checkbox" name="csv_pcharges_columns" value="1"  ><b>Purchase Charges Columns</b></div></label></td>'
                              . '<td colspan="1"></td>'
                                . '</tr>';
                  }
                    $result .= ' </tbody>
                      </table>
                        </div>';
            echo  $result ;
            die;
        }
        
        ///////////////csv download column changes/////////////
         if (isset($this->form_vars['csv_action']) && $this->form_vars['csv_action'] == 'download_csv') {
            //Get Shipnment data
            $this->startTime = microtime(true); 
            //$this->trackScriptExcecution('script Start time ');
            $output = '';
            $action_ext = $this->form_vars['action_ext'];
            $includeAgent = $this->form_vars['include_agent'];
            $this->csvSelectedColumns = $this->form_vars['csv_columns'];
            $this->includeChargesColumn = $this->form_vars['csv_charges_columns'];
            $this->includeAgentChargesColumn = $this->form_vars['csv_acharges_columns'];
            $this->includePurchaseChargesColumn = $this->form_vars['csv_pcharges_columns'];
            $this->consignment_filter = new ConsignmentFilter();
            $this->filter_form($this->form_vars);
        
    
            $selectFields = ""; 
            
            
            if(in_array('tracking_no', $this->csvSelectedColumns)){
                $selectFields .= " ,c.awb ";        
            }
            
            if(in_array('mawb', $this->csvSelectedColumns)){
                $selectFields .= " , group_concat(mawb.`mawb_number` order by mawb.id desc limit 1) AS mawb ";        
            }
            
            if(in_array('order_ref_no', $this->csvSelectedColumns)){
                $selectFields .= " ,c.hawb ";        
            }
            
            if(in_array('invoice_no', $this->csvSelectedColumns)){
                $selectFields .= ",inv.invoice_no as invoice_no ";
            }
            
             if(in_array('company', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.company ";
            }
            
             if(in_array('contact_name', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.contact ";
            }
            
             if( in_array('address_line_1', $this->csvSelectedColumns) || in_array('address_line_2', $this->csvSelectedColumns) || in_array('address_line_3', $this->csvSelectedColumns) ){
                $selectFields .=  " , c.address_line_1,c.address_line_2,c.address_line_3 ";
            }
            
              if(in_array('city', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.city ";
            }
            
              if(in_array('country', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.country_id ";
            }
            
            if(in_array('postcode', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.postcode ";
            }
            
            if(in_array('telephone', $this->csvSelectedColumns)){
                $selectFields .=  " ,c.telephone ";
            }
            
            if(in_array('description', $this->csvSelectedColumns)){
                $selectFields .=  " , c.description ";
            }
            
            if(in_array('no_of_pieces', $this->csvSelectedColumns)){
                $selectFields .= " ,c.number_pieces ";        
            }
            
            if(in_array('weight', $this->csvSelectedColumns)){
                $selectFields .= " ,c.weight ";        
            }
            
             if(in_array('chargable_weight', $this->csvSelectedColumns)){
                $selectFields .= " ,c.charge_weight ";        
            }
             if(in_array('vol_weight', $this->csvSelectedColumns)){
                $selectFields .= " ,c.vol_weight ";        
            }
             if(in_array('dox_ndx', $this->csvSelectedColumns)){
                $selectFields .= " ,c.is_doc ";        
            }
             if(in_array('value', $this->csvSelectedColumns)){
                $selectFields .= " ,c.value ";        
            }
            
              if(in_array('currency', $this->csvSelectedColumns)){
                $selectFields .= " ,c.currency ";        
            }
            
             if(in_array('remotearea', $this->csvSelectedColumns)){
                $selectFields .= " , c.remote_charges ";        
            }
            
             
            if(in_array('date_delivered', $this->csvSelectedColumns)){
                $selectFields .= " ,(SELECT date_created FROM tracking_data td WHERE status_code_id IN('122','121')  AND pc.tracking_number = td.tracking_number order by date_created limit 1)  as date_delivered ";        
            }
 
            if(in_array('date_scanned', $this->csvSelectedColumns)){
                 $selectFields .= " ,  c.date_scanned ";
            }
            
            if(in_array('date_label_created', $this->csvSelectedColumns)){
                 $selectFields .= " , c.date_label_created ";
            }
            
                
             if(in_array('date_dispatched', $this->csvSelectedColumns)){
                 $selectFields .= " ,  c.date_booked ";
            }
/*    echo "<pre>";        
print_r($this->csvSelectedColumns);
die;*/
             if (in_array('bag_no', $this->csvSelectedColumns)) {
                $selectFields .= " , (SELECT 
                bagging.bagnumber
            FROM
                `parcel_bagging_mapping` INNER JOIN bagging ON parcel_bagging_mapping.bag_id = bagging.id WHERE 
              parcel_bagging_mapping.parcel_id = pc.id ORDER BY parcel_bagging_mapping.id DESC LIMIT 1) as bag_id ";
            }

            if(in_array('carrier_routing_code', $this->csvSelectedColumns)){
                 $selectFields .= " , c.routing_code_eur ";
            } 
            
            if(in_array('consignment_status', $this->csvSelectedColumns)){
                 $selectFields .= " , c.consignment_status  ";
            } 
             if(in_array('consignment_type', $this->csvSelectedColumns)){
                 $selectFields .= " , c.consignment_type  ";
            } 
            
             if(in_array('ref', $this->csvSelectedColumns)){
                 $selectFields .= " ,   c.reference  ";
            } 

            
         // $this->consignment_filter->addInvoiceJoin();    
          $columns = " c.user_id ,c.service_id,c.customized_service_id,c.shipment_status  "
                    . "  $selectFields    "
                    . "  , ua.id as user_account_id, ua.user_account, c.is_dead_weight_chargable  "
                    . " , cc.invoice_id AS charges_invoice_id "
                    //. "  ,cc.invoice_id AS charges_invoice_id  "
                    . ", pc.length,pc.height,pc.width , pc.owe_status_code , pc.last_tracking_update 
                        
                    ";
           
            
           // $consignmentObjs = $this->consignment_filter->getColumnListOptimized($columns,5000,true);
            $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false,false,false);
            if($this->includeChargesColumn){
            if($action_ext == "DOWNLOAD_CSV"){
                $consignmentChargeCustomerType = new ConsignmentChargesTypesFilter();
                $consignmentChargeCustomerType->addFieldFilter("   charge_type", "customer");
                $consignmentChargeCustomerType->addOrFieldFilter("  charge_type", "both");
                //$consignmentChargeCustomerType->addFieldFilter("    is_extra_charge", 0);
                $consignmentChargeTypeCustomerObj = $consignmentChargeCustomerType->getList("id, title");
                if(count($consignmentChargeTypeCustomerObj)>0){
                    foreach($consignmentChargeTypeCustomerObj as $consignmentCustomer){
                        $this->customerChargesTypeArray[$consignmentCustomer->getId()] = $consignmentCustomer->getTitle(); 
                    }
                }
            }
            }
            $consignmentChargeTypeAgentObj = [];
         
            if ($this->includeAgentChargesColumn || $includeAgent == 1) {
                $consignmentChargeAgentType = new ConsignmentChargesTypesFilter();
                $consignmentChargeAgentType->addFieldFilter("   charge_type", "agent");
                $consignmentChargeAgentType->addOrFieldFilter("  charge_type", "both");
                //$consignmentChargeAgentType->addFieldFilter("    is_extra_charge", 0);
                $consignmentChargeTypeAgentObj = $consignmentChargeAgentType->getList("id, title");
                 if(count($consignmentChargeTypeAgentObj)>0){
                    foreach($consignmentChargeTypeAgentObj as $consignmentAgent){
                        $this->agentChargesTypeArray[$consignmentAgent->getId()] = 'Agent '.$consignmentAgent->getTitle(); 
                    }
                }
            }
            
            
            $consignmentChargeTypePurchaseObj = [];
         
            if ($this->includePurchaseChargesColumn || $includePurchase == 1) {
                $consignmentChargePurchaseType = new ConsignmentChargesTypesFilter();
                $consignmentChargePurchaseType->addFieldFilter("   charge_type", "agent");
                $consignmentChargePurchaseType->addOrFieldFilter("  charge_type", "both");
                //$consignmentChargePurchaseType->addFieldFilter("    is_extra_charge", 0);
                $consignmentChargeTypePurchaseObj = $consignmentChargePurchaseType->getList("id, title");
                 if(count($consignmentChargeTypePurchaseObj)>0){
                    foreach($consignmentChargeTypePurchaseObj as $consignmentAgent){
                        $this->purchaseChargesTypeArray[$consignmentAgent->getId()] = 'Purchase '.$consignmentAgent->getTitle(); 
                    }
                }
            }
            
            
           
 
            if (!empty($consignmentObjs)) { 
                        /////////populate country array//////////
                        $countryObj = new CountryFilter();
                        $countryObj->addFieldFilter('active', '1');
                        $rsCountry = $countryObj->getColumnList('id, name');
                        foreach($rsCountry as $country){
                            $this->countryDataArray[$country->getId()] =  $country->getName();
                        }
                        /////////populate service array//////////
                        $servicesObj = new ServiceFilter();
                        $servicesObj->addFieldFilter('active', '1');
                        $rsServices = $servicesObj->getColumnList('id, name,code,type');
                        foreach($rsServices as $service){
                            $this->servicesDataArray[$service->getId()] =  array('name'=>$service->getName(),'code'=>$service->getCode(),'type'=>$service->getType());
                        }

                        $output = $this->downloadCSV($consignmentObjs, $includeAgent, $consignmentChargeTypeCustomerObj, $consignmentChargeTypeAgentObj, $consignmentChargeTypePurchaseObj, $action_ext);
                        }
                        
            echo json_encode($output);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == 'get_tariff_charges') {
            $user = SessionManager::getUser();
            $consignmentId = $this->form_vars['consignment_id'];
            $consignmentObj = new Consignment($consignmentId);
            $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
            $userAccountObj = new CustomerAccount($user_account_id);
            $currency = "GBP";
            if (!empty($userAccountObj->getBillingCurrency())) {
                $currency = $userAccountObj->getBillingCurrency();
            }

            $consignmentChargesFilter = new ConsignmentChargesFilter();
            $consignmentChargesFilter->addConsignmentChargesTypeJoin();
            $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentId);
            $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "'");
            $consignmentChargesFilter->AddOrderBy('cc.charge_type_id', true);
            $consignmentChargesFilterObj = $consignmentChargesFilter->getColumnList("cc.cost,cc.changes_reference,cc.cost_type,cc.charge_type_id,cct.title,cct.charges_key");
            $html = "";
            $consignmentChargesArray = array();
            $totalCustomerCost = 0;
            $totalAgentCost = 0;
            $totalPurchaseCost = 0;
            $totalCost = 0;
            if (count($consignmentChargesFilterObj) > 0) {
                $reference = [];
                foreach ($consignmentChargesFilterObj as $obj) {
                    $consignmentChargesArray[$obj->getTitle()][$obj->getCostType()] = $obj->getCost();
                    if ($obj->getCostType() == "customer") {
                        if ($obj->getChargesKey() == "DISCOUNT") {
                            $totalCustomerCost -= $obj->getCost();
                        } else {
                            $totalCustomerCost += $obj->getCost();
                        }
                    } else if ($obj->getCostType() == "agent") {
                        $totalAgentCost += $obj->getCost();
                    }
                    else if ($obj->getCostType() == "purchase_invoice") {
                        $totalPurchaseCost += $obj->getCost();
                    }
                    $totalCost += $obj->getCost();
                    $reference[$obj->getTitle()] .= $obj->getChangesReference();
                }
                if (count($consignmentChargesArray) > 0) {
                    foreach ($consignmentChargesArray as $chargeType => $chargeValue) {
                        $html .= "<tr>";
                        $html .= "<td>" . $chargeType . "</td>";
                        $html .= "<td>" . $chargeValue['customer'] . "</td>";
                        $html .= "<td>" . $chargeValue['agent'] . "</td>";
                        $html .= "<td>" . $chargeValue['purchase_invoice'] . "</td>";
                        $html .= "<td>" . $reference[$chargeType] . "</td>";
                        $html .= "</tr>";
                    }
                    $html .= "<tr>";
                    $html .= "<td style='font-weight:bold;'>Total</td>";
                    $html .= "<td style='font-weight:bold;'>" . (($totalCustomerCost > 0) ? $this->getCurrencyFormat($totalCustomerCost, $currency) : "") . "</td>";
                    $html .= "<td style='font-weight:bold;'>" . (($totalAgentCost > 0) ? $this->getCurrencyFormat($totalAgentCost, $currency) : "") . "</td>";
                    $html .= "<td style='font-weight:bold;'>" . (($totalPurchaseCost > 0) ? $this->getCurrencyFormat($totalPurchaseCost, $currency) : "") . "</td>";
                    $html .= "<td style='font-weight:bold;'></td>";
                    $html .= "</tr>";
                    //                $html .= "<tr>";
                    //                $html .= "<td colspan='4' style='font-weight:bold;text-align:right;'>Grand Total : " . $totalCost . "</td>";
                    //                $html .= "</tr>";
                }
            } else {
                $html .= "<tr>";
                $html .= "<td colspan='4'>No price found</td>";
                $html .= "</tr>";
            }
            echo $html;
            exit();
        }

        if (isset($_GET['action']) && $_GET['action'] == 'get_user_details') {
            $userId = $this->form_vars['user_id'];
            $userObj = new User($userId);
            $userdata = new CustomerAccount($userObj->getUserAccountId());
            $parientAccountObj = new CustomerAccount($userdata->getParentid());
            $parientAccount = "";
            if (count($parientAccountObj) > 0) {
                $parientAccount = $parientAccountObj->getUserAccount();
            }
            $html = '';
            if (count($userdata) > 0) {
                $html .= '<tr>';
                $html .= '<th width="25%">Account</th>';
                $html .= '<td width="25%">' . $userdata->getUserAccount() . '</td>';
                $html .= '<th width="25%">Company </th>';
                $html .= '<td  width="25%">' . $userdata->getCompany() . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Full Name</th>';
                $html .= '<td>' . $userdata->getFullName() . '</td>';
                $html .= '<th>Owner </th>';
                $html .= '<td>' . $parientAccount . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Email</th>';
                $html .= '<td>' . $userdata->getEmail() . '</td>';
                $html .= '<th>Alternative Email </th>';
                $html .= '<td>' . $userdata->getAlternativeEmail() . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Return Address</th>';
                $html .= '<td>' . $userdata->getReturnAddress() . '</td>';
                $html .= '<th>Billing Address </th>';
                $html .= '<td>' . $userdata->getBillingAddress() . '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<th>Telephone</th>';
                $html .= '<td>' . $userdata->getPhone() . '</td>';
                $html .= '<th>Country </th>';
                $html .= '<td>' . $userdata->getCountry() . '</td>';
                $html .= '</tr>';
            }
            echo $html;
            exit();
        }

        if (isset($_GET['action']) && $_GET['action'] == 'add_invoice') {
            if ($this->form_vars['search_result'] > 0) {
                $this->consignment_filter = new ConsignmentFilter();
                $this->filter_form($this->form_vars);
                $consignmentObjs = $this->consignment_filter->getColumnList('c.user_id,u.user_account_id,cc.invoice_id as charges_invoice_id,c.awb,c.mawb,IF(c.customized_service_id>0,c.customized_service_id,c.service_id) service_id,c.shipment_status,c.date_label_created,c.date_delivered,c.company,c.address_line_1,c.address_line_2,c.address_line_3,c.city,c.country_id,c.postcode,c.number_pieces,c.weight,c.charge_weight,c.vol_weight,c.value,c.currency,c.remote_charges,c.hawb,c.reference,c.date_booked,c.is_doc,c.consignment_status');
                $consignmentData = array();
                if (count($consignmentObjs) > 0) {
                    foreach ($consignmentObjs as $obj) {
                        if ($obj->getChargesInvoiceId() <= 0) {
                            $consignmentData[] = [
                                'id' => $obj->getId(),
                                'user_id' => $obj->getUserId(),
                                'user_account_id' => $obj->getUserAccountId()
                            ];
                        }
                    }
                }
                $immediateChilds = CustomerAccount::accountImmediateChild($this->user->getUserAccountId());
                $output = array();
                $generateInvoiceIds = array();
                $accountsDataArray = array();
                $userObj = new User($this->user->getId());
                $netAmount = 0;
                $vat = 0;
                $applyVatTotalAmount = 0;
                $Fuel = 0;
                $totalAmount = 0;
                $totalWeight = 0;
                if (count($consignmentData) > 0) {
                    foreach ($consignmentData as $consignment) {
                        $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignment['id']);
                        $parentsAccount = [];
                        if (!in_array($user_account_id, $immediateChilds)) {
                            $parentsAccount = CustomerAccount::accountParentAccount($consignment['user_account_id'], true);
                            foreach ($immediateChilds as $childAccountId) {
                                if (in_array($childAccountId, $parentsAccount)) {
                                    $accountArrayId = $childAccountId;
                                    break;
                                }
                            }
                        } else {
                            $accountArrayId = $user_account_id;
                        }
                        $consignmentHoldFilter = new ConsignmentBillingHoldFilter();
                        $consignmentHoldFilter->where(['cbh.consignment_id' => $consignment['id'], 'cbh.user_account_id_from' => $this->user->getUserAccountId(), 'cbh.user_account_id_to' => $user_account_id]);
                        $consignmentHoldFilterCount = $consignmentHoldFilter->getCount(false);
                        if ($consignmentHoldFilterCount == 0) {
                            $consignmentChargesFilter = new ConsignmentChargesFilter();
                            $consignmentChargesFilter->addConsignmentChargesTypeJoin();
                            $consignmentChargesFilter->addFieldFilter("     cc.account_id", $user_account_id);
                            $consignmentChargesFilter->addFieldFilter("     cc.cost_type", "customer");
                            $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignment['id']);
                            $consignmentChargesFilterObj = $consignmentChargesFilter->getColumnList('cc.id,cc.account_id,cc.consignment_id,cc.charge_type_id,cc.cost,cct.title,cct.is_vat,cct.charges_key');
                            $chargesDetail = json_encode($consignmentChargesFilterObj);
                            $consignmentObj = new Consignment($consignment['id']);
                            $totalWeight += $consignmentObj->getWeight();

                            $currentVat = 0;
                            $currentFuel = 0;
                            $currentNetAmount = 0;
                            if (count($consignmentChargesFilterObj) > 0) {
                                foreach ($consignmentChargesFilterObj as $obj) {
                                    if ($obj->getChargesKey() == "VAT") {
                                        $currentVat += $obj->getCost();
                                    }
                                    if ($obj->getChargesKey() == "FUEL_CHARGES") {
                                        $currentFuel += $obj->getCost();
                                    }
                                    if ($obj->getChargesKey() != "VAT" && $obj->getChargesKey() != "FUEL_CHARGES") {
                                        $currentNetAmount += $obj->getCost();
                                    }
                                }
                                $vat += $currentVat;
                                $Fuel += $currentFuel;
                                $netAmount += $currentNetAmount;
                                if ($currentVat > 0) {
                                    $currentTotalWithoutVat = ($currentNetAmount + $currentFuel);
                                    $applyVatTotalAmount += $currentTotalWithoutVat;
                                }
                                $accountsDataArray[$accountArrayId][] = [
                                    'invoiceDetails' => [
                                        'consignment_id' => $consignmentObj->getId(),
                                        'charges_detail' => $chargesDetail,
                                        'total' => $currentNetAmount,
                                        'vat' => $currentVat
                                    ]
                                ];
                            } else {
                                $output['status'] = 'error';
                                $output['message'] = 'consignment charges not found please add charges.';
                                $output['consignmentIds'][] = $consignmentObj->getId();
                            }
                        }
                    }
                }
                if (isset($output['status']) && $output['status'] == "error") {
                    echo json_encode($output);
                    die;
                }
                $invoice_date       = strtotime($this->form_vars['invoice_date']);
                $invoice_reference  = $this->form_vars['invoice_reference'];
                
                $date_added = time();
                $added_by = $this->user->getId();
                $date_update = time();
                $SubTotalAmount = ($Fuel + $netAmount);
                /* state calculate vat 20%  */
                $vatCalculate = ($applyVatTotalAmount * 0.20);
                $totalAmount = ($vatCalculate + $SubTotalAmount);
                if (count($accountsDataArray) > 0) {
                    foreach ($accountsDataArray as $accountId => $data) {
                        $userAccountObj = new CustomerAccount($accountId);
                        $currencyFilter = new CurrencyFilter();
                        $currencyFilter->addFieldFilter("rightsymbol", $userAccountObj->getBillingCurrency());
                        $currencyObj = $currencyFilter->getList();
                        /* default curreny id GBP */
                        $billingCurrencyId = 2;
                        if (count($currencyObj) > 0) {
                            $billingCurrencyId = $currencyObj[0]->getId();
                        }
                        $result = Invoices::generateInvoiceNumber($this->user->getUserAccountId(), 'INV');
                        $invoice_number = "";
                        if ($result['status'] == "success") {
                            $invoice = new Invoices();
                            $invoice->setInvoiceNo($result['invoice_number']);
                            $invoice->setInvoiceType("INV");
                            $invoice->setUserAccountId($accountId);
                            $invoice->setNetAmount($netAmount);
                            $invoice->setVatableAmount($applyVatTotalAmount);
                            $invoice->setVat($vatCalculate);
                            $invoice->setFuelCharges($Fuel);
                            $invoice->setTotalAmount($totalAmount);
                            $invoice->setWeight($totalWeight);
                            $invoice->setCurrencyId($billingCurrencyId);
                            $invoice->setInvoiceDate($invoice_date);
                            $invoice->setInvoiceReference($invoice_reference);
                            $invoice->setInvoiceBy($this->user->getUserAccountId());
                            $invoice->setAddedBy($added_by);
                            $invoice->setDateCreated($date_added);
                            $invoice->setDateUpdated($date_update);
                            if ($userAccountObj->getIsPrepaid() == 1) {
                                $invoice->setIsPaid(1);
                                $invoice->setPaidDate($date_added);
                            }
                            $invoice->save();
                            $last_invoice_id = $invoice->getId();
                            if ($last_invoice_id > 0) {
                                if ($userAccountObj->getIsPrepaid() == 0) {
                                    // Add payment transcation into payment history table for invoice charges
                                    $invoiceMsg = "Invoiced amount for invoice# " . $result['invoice_number'];
                                    $paymentsHistory = new PaymentsHistory();
                                    $paymentsHistory->setAccountId($accountId);
                                    $paymentsHistory->setAmount($totalAmount);
                                    $paymentsHistory->setAmountCurrencyId($billingCurrencyId);
                                    $paymentsHistory->setPaymentDetail($invoiceMsg);
                                    $paymentsHistory->setUserCurrencyId($billingCurrencyId);
                                    $paymentsHistory->setDebit($totalAmount);
                                    $paymentsHistory->setCredit("0.00");
                                    $paymentsHistory->setInvoiceId($invoice->getId());
                                    $paymentsHistory->setPaymentStatus("completed");
                                    $paymentsHistory->setIsCompleted("yes");
                                    $paymentsHistory->setDateAdded(time());
                                    $paymentsHistory->setAddedBy($added_by);
                                    $paymentsHistory->save();
									/* update account balance */
									CustomerAccount::updateBalance($accountId);
									/******************************/
                                }
                                // Add payment transcation into payment history table for invoice charges
                                $generateInvoiceIds[] = $last_invoice_id;
                                foreach ($data as $detail) {
                                    $invoiceDetail = new InvoiceDetail();
                                    $invoiceDetail->setConsignmentId($detail['invoiceDetails']['consignment_id']);
                                    $invoiceDetail->setInvoiceId($last_invoice_id);
                                    $invoiceDetail->setInvoiceNo($invoice_number);
                                    $invoiceDetail->setChargesDetail($detail['invoiceDetails']['charges_detail']);
                                    $invoiceDetail->setTotal($detail['invoiceDetails']['total']);
                                    $invoiceDetail->setVat($detail['invoiceDetails']['vat']);
                                    $invoiceDetail->setDateCreated($date_added);
                                    $invoiceDetail->setAddedBy($added_by);
                                    $invoiceDetail->save();

                                    ConsignmentCharges::updateInvoiceId($accountId, $detail['invoiceDetails']['consignment_id'], $last_invoice_id);
                                }
                            } else {
                                $output['status'] = 'error';
                                $output['message'] = 'Pdf is not generated due to invoice table error';
                            }
                        } else {
                            $output['status'] = $result['status'];
                            $output['message'] = $result['message'];
                        }
                    }
                    if (count($generateInvoiceIds) > 0) {
                        $output['status'] = 'success';
                        $output['message'] = 'Invoice is generated successfully.';
                        $output['invoiceIds'] = $generateInvoiceIds;
                        ;
                    } else {
                        $output['status'] = 'error';
                        if (!isset($output['message']) || empty($output['message'])) {
                            $output['message'] = 'something went wrong please contect to admin';
                        }
                    }
                } else {
                    $output['status'] = 'error';
                    $output['message'] = 'Invoice is all ready generated or consignment is on biling hold.';
                }
            } else {
                $output['status'] = 'error';
                $output['message'] = 'something went wrong please contect to admin';
            }
            echo json_encode($output);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == 'get_invoice_pdf') {
            if ($this->form_vars['search_result'] > 0) {
                $output = array();
                $checks = [
                    'check_box_date' => $this->form_vars['check_box_date'],
                    'check_box_reference' => $this->form_vars['check_box_reference'],
                    'check_box_destination' => $this->form_vars['check_box_destination'],
                    'check_box_service' => $this->form_vars['check_box_service'],
                    'check_box_bank_details' => $this->form_vars['check_box_bank_details'],
                ];
                $invoiceIds = explode(",", $this->form_vars['invoiceIds']);
                if (count($invoiceIds) > 0) {
                    foreach ($invoiceIds as $invoiceId) {
                        $invoice = new Invoices($invoiceId);
                        $toUserAccountObj = new CustomerAccount($invoice->getInvoiceBy());
                        $templateId = $toUserAccountObj->getInvoiceTemplateId();
                        $invoiceTemplate = [];
                        if($templateId > 0) {
                            $invoiceTemplate = new InvoiceTemplates($templateId);
                        }
                        // Summary Pdf
                        $consignmentFilter = new ConsignmentFilter();
                        $consignmentFilter->addJoin("consignment_charges cc ", "cc.consignment_id = c.id ");
                        $consignmentFilter->addJoin("invoices i ", "i.id = cc.invoice_id");
                        $consignmentFilter->addFieldFilter("     cc.cost_type", "customer");
                        $consignmentFilter->addFilterIn("i.id", $invoiceId, "filter");
                        $consignmentFilterObj = $consignmentFilter->getListNew("c.id,c.country_id,IF(c.customized_service_id>0,c.customized_service_id,c.service_id) as service_id,cc.id AS consignment_charges_id,i.id AS invoice_id,cc.`account_id` ");
                        $chargesData = [];
                        if (count($consignmentFilterObj) > 0) {
                            foreach ($consignmentFilterObj as $obj) {
                                $chargesData[$obj->getCountryId()][$obj->getServiceId()][$obj->getId()][] = $obj->getConsignmentChargesId();
                            }
                            $summaryTemplateFuction = 'SaveSummaryInvoicePDFFile';
                            if(count($invoiceTemplate)) {
                                $summaryTemplateFuction = $invoiceTemplate->getSummaryInvoiceFunction();
                            }
                            $sammaryPdf = new InvoicePDF();
                            $returnData = $sammaryPdf->$summaryTemplateFuction($chargesData, $invoiceId, $checks);
                            $invoiceObj = new Invoices($invoiceId);
                            $invoiceObj->setSummaryPdf($returnData['FILENAME']);
                            $invoiceObj->save();
                        }
                        // Detail PDF
                        $invoiceDetailFilter = new InvoiceDetailFilter();
                        $invoiceDetailFilter->addFieldFilter("invoice_id", $invoiceId);
                        $invoiceDetailFilterObj = $invoiceDetailFilter->getList();
                        $consignmentIds = array();
                        if (count($invoiceDetailFilterObj) > 0) {
                            foreach ($invoiceDetailFilterObj as $obj) {
                                $consignmentIds[] = $obj->getConsignmentId();
                            }
                        }
                        $consignmentPdfFilter = new ConsignmentFilter();
                        $consignmentPdfFilter->addFilterIn("c.id", $consignmentIds, "filter");
                        $consignmentPdfFilter->AddOrderBy('c.country_id,c.date_label_created', 'order_by');
                        $consignments = $consignmentPdfFilter->getListNew();
                        $pdf = new InvoicePDF();  
                        $invoiceTemplateFuction = 'SavePDFFile';
                        if(count($invoiceTemplate)) {
                            $invoiceTemplateFuction = $invoiceTemplate->getInvoiceFunction();
                        }
                        $data = $pdf->$invoiceTemplateFuction($consignments, $invoiceId, $checks);
                        $invoiceObj = new Invoices($invoiceId);
                        $invoiceObj->setPdf($data['FILENAME']);
                        $invoiceObj->save();
                        /* Save invoice csv */
                        $resultInvoiceCsv = $this->invoiceCsv($invoiceId);
                        $invoiceObj = new Invoices($invoiceId);
                        $invoiceObj->setCsv($resultInvoiceCsv['FILENAME']);
                        $invoiceObj->save();
                        $output['path'][] = $data;
                    }
                    echo json_encode($output);
                    die;
                }
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == "create_manifest") {
            $action_type = $this->form_vars['action_type'];
            $parcel_ids = array();
            $outputArray = array();
            if (isset($action_type) && $action_type == "selected") {
                $arrayOfShipment = $this->form_vars['multi_select_parcel'];
                $serviceId = $this->form_vars['service_id'];
                if (count($arrayOfShipment) > 0) {
                    foreach ($arrayOfShipment as $arrayValue) {
                        $arrayPart = array();
                        $arrayPart = explode('||', $arrayValue);
                        $parcel_ids[] = $arrayPart[1];
                    }
                }
                $returnData = Manifest::manifestCreate($parcel_ids, $serviceId, 'operation');
                $outputArray['status'] = "succcess";
                $outputArray['message'] = "All manifest is created successfully";
                $outputArray['data'] = $returnData;
            } elseif (isset($action_type) && $action_type == "all") {
                $parcels = $this->form_vars['parcelIds'];
                $paecelIds = [];
                if ($parcels != "") {
                    $serviceId = $this->form_vars['service_id'];
                    $paecelIds = explode(",", $parcels);
                    $returnData = Manifest::manifestCreate($paecelIds, $serviceId, 'operation');
                    $outputArray['status'] = "succcess";
                    $outputArray['message'] = "All manifest is created successfully";
                    $outputArray['data'] = $paecelIds;
                } else {
                    $outputArray['status'] = "error";
                    $outputArray['message'] = "Sorry no parcel is for manifest";
                }
            } else {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Action type is not valid";
            }
            echo json_encode($outputArray);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == "remove_manifest") {
            $action_type = $this->form_vars['action_type'];
            $manifest_ids = array();
            $outputArray = array();
            if (isset($action_type) && $action_type == "selected") {
                $arrayOfShipment = $this->form_vars['multi_select_parcel'];
                if (count($arrayOfShipment) > 0) {
                    foreach ($arrayOfShipment as $arrayValue) {
                        $arrayPart = array();
                        $arrayPart = explode('||', $arrayValue);
                        if (isset($arrayPart[3])) {
                            $manifest_ids[] = $arrayPart[3];
                        }
                    }
                }
                if (count($manifest_ids) > 0) {
                    foreach ($manifest_ids as $manifestId) {
                        $manifestEntityMapping = new ManifestEntityMapping();
                        $manifestEntityMapping->deleteById($manifestId);
                    }
                }
                $outputArray['status'] = "success";
                $outputArray['message'] = "Manifest is remove successfully";
            } else {
                $outputArray['status'] = "error";
                $outputArray['message'] = "Action type is not valid";
            }
            echo json_encode($outputArray);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "gp_report_excel") {
            
            
            header('Content-Type: text/html; charset=utf-8');
            // Shabbir and Irshad Bhai ask to increase memory limit and max execution time on 27 August
            ini_set('max_execution_time', 600); 
            ini_set('memory_limit', '1024M'); 
             
            $output = '';
            $this->consignment_filter = new ConsignmentFilter();
 
            $this->filter_form($this->form_vars);
            $gpReportObj = new ShipmentManageExcelReports($this->excelFilterData,$this->form_vars['report_type']);
                        
            $this->user = Sessionmanager::getUser();
            $this->userObj = new User($this->user->getId());
            $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
            $UserAccountId = $this->user->getUserAccountId();
            $loggedInUserCurrency = $this->userAccount->getBillingCurrency();

            
            $currObj = new CurrencyFilter();
            $currObj->addFieldFilter('rightsymbol', $loggedInUserCurrency);
            $currData = $currObj->getColumnList('leftsymbol');
            $loggedInUserCurrencySymbol = $currData[0]->getLeftSymbol();
            $subWhere = " AND charge_type_id <> 28 AND account_id IN (select id from user_account WHERE parentid =  $UserAccountId )  ";  
            if ($this->form_vars['report_type'] == 'detail') {
 
                               
                             $dataColumns = "c.awb,c.mawb,c.date_label_created, c.number_pieces,c.weight,c.charge_weight,c.vol_weight, c.hawb,c.reference,ua.id as user_account_id,ua.user_account, c.is_dead_weight_chargable,s.name as service_name,c.id ,"
                        . " (SELECT SUM(cost) cost FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer' $subWhere)  customer_cost,
                            (SELECT cost_currency  FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer' $subWhere LIMIT 1 ) customer_cost_currency,
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer' $subWhere  ) as customer_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer'$subWhere LIMIT 1 ) as customer_company_currency,

                            (SELECT SUM(cost_supplier_currency)  cost_supplier_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere ) as agent_cost_supplier,     
                            (SELECT  supplier_currency  FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere LIMIT 1) as agent_supplier_currency,
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere ) as agent_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere LIMIT 1) as agent_company_currency,   

                            (SELECT SUM(cost_supplier_currency)  cost_supplier_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere) as purchase_invoice_cost_supplier,     
                            (SELECT  supplier_currency  FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere LIMIT 1) as purchase_invoice_supplier_currency,
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere) as purchase_invoice_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere LIMIT 1) as purchase_invoice_company_currency ";
                     
                $consignmentObjs = $this->consignment_filter->getColumnList($dataColumns, 25000  ); 
                if (!empty($consignmentObjs)) {
                    $searchConsignmentAccountId = $this->form_vars['user_account_id'];
                    $output = $gpReportObj->downloadExcelDetailGpReport($consignmentObjs,  $loggedInUserCurrencySymbol, $this->subAccountArr,$searchConsignmentAccountId);
                }
            } else if ($this->form_vars['report_type'] == 'summary') {
                 $dataColumns = " SUM(c.number_pieces) number_pieces,SUM(c.weight) weight,SUM(c.charge_weight) charge_weight,SUM(c.vol_weight) vol_weight ,ua.user_account ,s.name as service_name, "
                        . "  
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer' $subWhere) as customer_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'customer' $subWhere LIMIT 1) as customer_company_currency,
 
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere) as agent_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere LIMIT 1) as agent_company_currency,   
 
                            (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere) as purchase_invoice_cost_company,    
                            (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere LIMIT 1) as purchase_invoice_company_currency ";
                
                 
                 $consignmentObjs = $this->consignment_filter->getColumnList($dataColumns, 50000);
                if (!empty($consignmentObjs)) {
                    $output = $gpReportObj->downloadExcelSummaryGpReport($consignmentObjs, $loggedInUserCurrencySymbol);
                }
            }
            echo json_encode($output);
            die;
        }
        
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "variance_report_excel") {
                 
            header('Content-Type: text/html; charset=utf-8');
            // Shabbir and Irshad Bhai ask to increase memory limit and max execution time on 27 August
            ini_set('max_execution_time', 600); 
            ini_set('memory_limit', '1024M'); 
             
            $output = '';
            $this->consignment_filter = new ConsignmentFilter();
 
            $this->filter_form($this->form_vars);
            $varianceReportObj = new ShipmentManageExcelReports($this->excelFilterData,$this->form_vars['report_type']);
                        
            $this->user = Sessionmanager::getUser();
            $this->userObj = new User($this->user->getId());
            $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
            $UserAccountId = $this->user->getUserAccountId();
            $loggedInUserCurrency = $this->userAccount->getBillingCurrency();

            
            $currObj = new CurrencyFilter();
            $currObj->addFieldFilter('rightsymbol', $loggedInUserCurrency);
            $currData = $currObj->getColumnList('leftsymbol');
            $loggedInUserCurrencySymbol = $currData[0]->getLeftSymbol();
            $subWhere = " AND charge_type_id <> 28 AND account_id IN (select id from user_account WHERE parentid =  $UserAccountId )  ";  
                if ($this->form_vars['report_type'] == 'detail') {
                        $dataColumns = "c.awb,c.mawb,c.date_label_created, c.number_pieces,c.weight,c.charge_weight,c.vol_weight, c.hawb,c.reference,ua.id as user_account_id,ua.user_account, c.is_dead_weight_chargable,s.name as service_name,c.id ,"
                   . "  
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id <> 1  AND cost_type = 'agent' $subWhere ) as agent_additional_charges,                                   
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id = 1  AND cost_type = 'agent' $subWhere ) as agent_basic_charges,                          
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cost_type = 'agent' $subWhere ) as agent_total,    
                       (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere LIMIT 1) as agent_company_currency,   

                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id <> 1  AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_additional_charges,      
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id = 1  AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_basic_charges,       
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_total,    
                       (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere LIMIT 1) as purchase_invoice_company_currency  
                       ";
                        $consignmentObjs = $this->consignment_filter->getColumnList($dataColumns, 25000  ); 
                        
                          if (!empty($consignmentObjs)) {
                              $searchConsignmentAccountId = $this->form_vars['user_account_id'];
                              $output = $varianceReportObj->downloadExcelVarianceReport($consignmentObjs,  $loggedInUserCurrencySymbol, $this->subAccountArr, $searchConsignmentAccountId);
                            }
                
                }else if ($this->form_vars['report_type'] == 'summary') {
                     $dataColumns = " SUM(c.number_pieces) number_pieces,SUM(c.weight) weight,SUM(c.charge_weight) charge_weight,SUM(c.vol_weight) vol_weight ,ua.user_account ,s.name as service_name,  "
                   . "  
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id <> 1  AND cost_type = 'agent' $subWhere ) as agent_additional_charges,                                   
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id = 1  AND cost_type = 'agent' $subWhere ) as agent_basic_charges,                          
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cost_type = 'agent' $subWhere ) as agent_total,    
                       (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'agent' $subWhere LIMIT 1) as agent_company_currency,   

                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id <> 1  AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_additional_charges,      
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cc.charge_type_id = 1  AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_basic_charges,       
                       (SELECT SUM(cost_company_currency) FROM consignment_charges cc WHERE consignment_id = c.id AND cost_type = 'purchase_invoice' $subWhere ) as invoiced_total,    
                       (SELECT company_currency FROM consignment_charges cc WHERE consignment_id = c.id AND  cost_type = 'purchase_invoice' $subWhere LIMIT 1) as purchase_invoice_company_currency  

                       ";
                      $consignmentObjs = $this->consignment_filter->getColumnList($dataColumns, 50000  );
                      if (!empty($consignmentObjs)) {
                                $output = $varianceReportObj->downloadExcelVarianceReportSummary($consignmentObjs,  $loggedInUserCurrencySymbol);
                            }
                }
                
              
          
            echo json_encode($output);
            die;
        }
    }

    protected function filter_form($data) {
        $consignmentIdsSearchInFilter = [];
        $this->form_vars = $data;
        $track = '';
        $chinaDispatchDate = '';
        $billOurAccount = '';
        $match = '';
        $export_master = '';
        $is_customer_billable = '';
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
        $track = $this->form_vars['track'];
        
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
        $status = isset($this->form_vars['status'])?$this->form_vars['status']:'';
        $warehouse  = isset($this->form_vars['warehouse'])?$this->form_vars['warehouse']:'';
        $returnTracking = isset($this->form_vars['return_tracking'])?$this->form_vars['return_tracking']:''; //= $this->form_vars['return_tracking'];
        $invoice_type = $this->form_vars['invoice_type'];
        $agent = $this->form_vars['agent'];
        $bagNumber = $this->form_vars['bag_number'];
        $charges = $this->form_vars['charges'];

        if(!empty($userAccountId)){
            $excelAccountId = $userAccountId ;
        }else{
             $excelAccountId = $this->user->getUserAccountId() ;
        }
        
        $accountData = new CustomerAccount($excelAccountId);
        if(count($accountData)>0){
            $this->excelFilterData['Account'] = $accountData->getUserAccount();
            //$this->excelFilterData['Company'] = $accountData->getCompany();
        }
                      
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
        if (isset($this->form_vars['is_customer_billable']) && trim($this->form_vars['is_customer_billable']) != '') {
            $is_customer_billable = $this->form_vars['is_customer_billable'];
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

        if (!empty($dateFrom) && !empty($dateTo) ) {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
            $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));
            $this->excelFilterData['Date-Filter'] = $dateType;
            $this->excelFilterData['Date-Range'] =  $dateFrom. " To ".$dateTo;
            $datescannedCheck = true;
            $warehouse = array_values(array_filter($warehouse));
            if(isset($warehouse) && count($warehouse)> 0 && count($warehouse) == 1)
                $datescannedCheck = false;
            
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
            } else if ($dateType == "dateScanned" && $datescannedCheck ) {
                $dateScannedWhere = "       ((c.date_scanned >= '" . DbAccess3::escape($dateFrom) . "') AND (c.date_scanned <= '" . DbAccess3::escape($dateTo) . "'))";
                $this->consignment_filter->addFilter($dateScannedWhere, 'filter');
            }
        }else{
            $this->excelFilterData['Date-Range'] =  "No Date Range Selected";
        }

        if (!empty($fromWeight) && !empty($toWeight)) {
            $this->consignment_filter->addFilter("       (if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight >= '" . DbAccess3::escape($fromWeight) . "' and c.weight <= '" . DbAccess3::escape($toWeight) . "') , ( c.vol_weight >= '" . DbAccess3::escape($fromWeight) . "' and c.vol_weight <= '" . DbAccess3::escape($toWeight) . "')))", 'filter');
        } else if (trim($fromWeight) != '' && $toWeight >= 0) {
            $this->consignment_filter->addFilter("         ( if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight >= '" . DbAccess3::escape($fromWeight) . "' ),( c.vol_weight >= '" . DbAccess3::escape($fromWeight) . "')))", 'filter');
        } else if (trim($toWeight) != '' && $toWeight > 0) {
            $this->consignment_filter->addFilter("     ( if((c.weight > c.vol_weight OR c.vol_weight is null ),(c.weight <= '" . DbAccess3::escape($toWeight) . "' ),(  c.vol_weight <= '" . DbAccess3::escape($toWeight) . "')))", 'filter');
        }
        
        $this->excelFilterData['Weight'] =  $fromWeight. " To ".$toWeight;
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
        } else  {
           
            $includeParent = true;
//            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
//                $includeParent = false;
//            }
  
            $userAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, $includeParent);
            $ids = implode(",", $userAccountArray);
           // if (empty($warehouse) || trim($warehouse[0]) == '')
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
            $serviceList = $serviceFilter->getColumnList("id,is_customized,name");
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
            
            //////////filter will use and show in download GP report/////////
              if (!empty($service)) {
                $serviceFilterObj = new ServiceFilter();
                $serviceFilterObj->addFilter("     ser.id=" . $service . "  AND ca.id= " . $carriers);
                $serivceData = $serviceFilterObj->getCarrierServicesList('ca.carrier , ser.name');
                if (count($serivceData) > 0) {
                    $this->excelFilterData['Carrier'] = $serivceData[0]->getCarrier();
                    $this->excelFilterData['Service'] = $serivceData[0]->getName();
                }
            } else if (!empty($carriers)) {
                $carrierFilterObj = new CarrierFilter();
                $carrierFilterObj->addFilter(" id= " . $carriers);
                $carrierData = $carrierFilterObj->getColumnList('carrier');
                if (count($carrierData) > 0) {
                    $this->excelFilterData['Carrier'] = $carrierData[0]->getCarrier();
                }
            }
                //////////filter will use and show in download GP report ends here/////////
            if (count($customizeServiceArr) > 0 && count($serviceArr) > 0) {
                $servicesIds = implode(",", $serviceArr);
                $customizeServicesIds = implode(",", $customizeServiceArr);
                $this->consignment_filter->addFilter('    ( c.service_id  IN (' . DbAccess3::escape($servicesIds) . ') OR c.customized_service_id  IN (' . DbAccess3::escape($customizeServicesIds) . '))', 'filter');
            } else if (count($serviceArr) > 0) {
                $this->consignment_filter->addFilterIn('    c.service_id', $serviceArr, 'filter');
            } else if (count($customizeServiceArr) > 0) {
                $this->consignment_filter->addFilterIn('    c.customized_service_id', $customizeServiceArr, 'filter');
            }
        }

        if (isset($service) && $service != "") {
            $this->consignment_filter->addFilter("     (c.service_id= '".DbAccess3::escape($service)."' OR c.customized_service_id ='". DbAccess3::escape($service)."')");
        }
        
        if (isset($track) && $track == "untracked") {
            $this->consignment_filter->addFilter("     s.is_untrack = 1 ", 'filter');
        } else if (isset($track) && $track == "tracked") {
            $this->consignment_filter->addFilter("     s.is_untrack <> 1 ", 'filter');
        }
      
        
        if (!empty($country)) {
            $this->consignment_filter->addFieldFilter('     c.country_id', $country);
        }
        $consignmentIdsSearchInFilter = [];
        if (trim($trackingNumber) != '') {
            $this->excelFilterData['Tracking'] = $trackingNumber;
            $awb = nl2br($trackingNumber);
            $AwbArray = explode('<br />', $awb);
            foreach ($AwbArray as $key => $value) {
                if(trim($value)!= '')
                    $AwbArray[$key] =   trim(ParseTrackingNumber::Parse($value)) ;
            }
           if(!empty($AwbArray)){
            if (!empty($trackingType) && $trackingType == "parcel") {
                $this->excelFilterData['Tracking-type'] = $trackingType;
                $consignmentIds = array();
                $ParcelFilter = new ParcelFilter();
                $ParcelFilter->addTrackingNumberFilterIn("      p.tracking_number", $AwbArray);
                $ParcelData = $ParcelFilter->getColumnList("consignment_id");
                if(count($ParcelData) > 0){
                    foreach ($ParcelData as $con_id) {
                        $consignmentIdsSearchInFilter[] = $con_id->getConsignmentId();
                    }
                } else {
                $this->consignment_filter->addFilterIn('    c.awb', $AwbArray, 'filter');
            }
            } else {
                $this->consignment_filter->addFilterIn('    c.awb', $AwbArray, 'filter');
            }
        }
        }
        $bagNumberCheck = false;
        if (trim($bagNumber) != '') {
            $bagNumberCheck = true;
            $bagNumbers = nl2br($bagNumber);
            $bagNumbersArray = explode('<br />', $bagNumbers);
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addJoin("      bagging as b", 'b.id','pbm.bag_id');
            $parcelBaggingMappingFilter->addJoin("      parcel as p", 'p.id','pbm.parcel_id');
            foreach($bagNumbersArray as $keyBagArray=>$bagNumberIstance)
                $bagNumbersArray[$keyBagArray] = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $bagNumberIstance);
            $parcelBaggingMappingFilter->addFilterIn('          b.bagnumber',$bagNumbersArray);
            $parcelBaggingMappingFilter->setLimit("10000000000");
            $parcelBaggingMappingFilterData = $parcelBaggingMappingFilter->getList('p.consignment_id');
            
            $consignmentIds = [];
            foreach ($parcelBaggingMappingFilterData as $con_id) {
                $consignmentIds[] = $con_id->getConsignmentId();
            }
            if (count($consignmentIdsSearchInFilter)) {
                $consignmentIdsSearchInFilter = array_intersect($consignmentIdsSearchInFilter, $consignmentIds);
            } else {
                $consignmentIdsSearchInFilter = $consignmentIds;
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
            if (count($consignmentIdsSearchInFilter)) {
                $consignmentIdsSearchInFilter = array_intersect($consignmentIdsSearchInFilter, $consignmentIds);
            } else {
                $consignmentIdsSearchInFilter = $consignmentIds;
            }
            $manifestFilterCheck = true;
        }


        if (trim($returnTracking) != '') {
            $this->consignment_filter->addFieldFilter('     c.return_awb', $returnTracking);
        }

        if (trim($highlowvalue) != '') {
            $this->consignment_filter->addFilter("     c.hv_lv IN ('". $highlowvalue."','". str_replace("V","",$highlowvalue)."')", 'filter');
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
        if (trim($is_customer_billable)!='') {
            $this->consignment_filter->addFilter("     c.is_customer_billable = '".$is_customer_billable."'", 'filter' );
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
            $this->excelFilterData['mwab'] = $mawb;
            $consignmentIds = ConsignmentFilter::getConsignmentIdFromMawb($mawb);
            if (count($consignmentIdsSearchInFilter)) {
                $consignmentIdsSearchInFilter = array_intersect($consignmentIdsSearchInFilter, $consignmentIds);
            } else {
                $consignmentIdsSearchInFilter = $consignmentIds;
            }
            $mawbFilterCheck = true;
        } 
        if (trim($hawb) != '' && trim($match) == '') {
            $hawb = nl2br($hawb);
            $hawb = explode('<br />', $hawb);
            /*foreach ($HawbArray as $k => $value) {
                $HawbArray[$k] = "'" . trim($value) . "'";
            }
            $this->consignment_filter->addFilterIn('    c.hawb', $HawbArray, 'filter');*/
            $this->consignment_filter->addFilter("    c.hawb IN ('". implode("','",array_map("trim",  $hawb))."') AND c.hawb <> ''",  'filter');
        } else if (trim($hawb) != '' && trim($match) == 1) {
            $hawb = nl2br($hawb);
            $HawbArray = explode('<br />', $hawb);
            foreach ($HawbArray as $k => $value) {
                $HawbArray[$k] = "'" . trim($value) . "'";
            }
            $this->consignment_filter->addFilterLikeIn('    c.hawb', $HawbArray, 'filter');
        }
        

        if (trim($invoiceNumber) != '') {
            $this->excelFilterData['invoice'] = $invoiceNumber;
            $invoiceid = nl2br($invoiceNumber);
            $invoiceNumber = explode('<br />', $invoiceid);
            foreach ($invoiceNumber as $k => $value) {
                $InvoiceidArray[$k] =  trim($value) ;
            }
            $this->consignment_filter->addInvoiceJoin();
            $this->consignment_filter->addFilterIn("    inv.invoice_no", $InvoiceidArray, "filter");
           
        }

        if (trim($shipment) != '') {
            if ($shipment == 'held') {
                $this->consignment_filter->addFilter("     c.id IN (select consignment_id from consignment_billing_hold where user_account_id_from = '" . $this->user->getUseraccountId() . "')");
            } else if ($shipment == 'nonHeld') {
                $this->consignment_filter->addFilter("     c.id NOT IN (select consignment_id from consignment_billing_hold where user_account_id_from = '" . $this->user->getUseraccountId() . "')");
            }
        }


        if (isset($status[0]) && $status[0] == Consignment::STATUS_NOT_DELIVERED) {
            $this->consignment_filter->addStatusFilterNotIn([Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_DELIVERED, Consignment::STATUS_INVALID, Consignment::STATUS_RECYCLED], 'filter');
        } else if (isset($status[0]) && $status[0] != "" && !empty($status)) {
            $this->consignment_filter->addFilterIn("     c.shipment_status", $status, 'filter');
        } else {
            $this->consignment_filter->addStatusFilterNotIn([Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_INVALID, Consignment::STATUS_RECYCLED], 'filter');
        }

        
        if (!empty($warehouse) && trim($warehouse[0]) != '') {
            if (in_array($dateType, ["dateLabelCreated","dateCreated"]) ) {
                $this->consignment_filter->addFilter("      u.warehouse_id  IN (".implode(",",$warehouse).") ", 'userfilter');
            }else{
             
                /*
 else if ($dateType == "dateScanned" &&  count($warehouse) != 1) {
                $dateScannedWhere = "       ((c.date_scanned >= '" . DbAccess3::escape($dateFrom) . "') AND (c.date_scanned <= '" . DbAccess3::escape($dateTo) . "'))";
                $this->consignment_filter->addFilter($dateScannedWhere, 'filter');
            }
                 *                  */
                $whereTrackingData = "";
                if (!empty($dateFrom) && !empty($dateTo) ) {
                if ($dateType == "dateScanned" &&  count($warehouse) == 1) {
                        $whereTrackingData = " AND tracking_data.date_created >= '" . DbAccess3::escape($dateFrom) . "' AND tracking_data.date_created <= '" . DbAccess3::escape($dateTo) . "'";
                    }
                }
                $this->consignment_filter->addFilter("      c.id IN ("
                        . " SELECT "
                        . "     parcel.consignment_id "
                        . " FROM "
                        . "     tracking_data "
                        . " INNER JOIN "
                        . "     parcel ON tracking_data.entity_id = parcel.id "
                        . " WHERE "
                        . "     tracking_data.warehouse_id IN (".implode(",",$warehouse).")  "
                        . " AND "
                        . "     tracking_data.status_code_id IN ( '146', '126','138' ) "
                        . " $whereTrackingData "
                        . " )", 'filter');
            }
                
            
        } 
        
        
                
                
//            echo "<pre>";
        //      print_r($this->consignment_filter);
        //    die;

        if (trim($remotearea) != '') {
            
            $this->excelFilterData['Remote-Area'] = $remotearea;
            if ($remotearea == 'all') {
                
            } else
            if ($remotearea == 'remotearea') {
                $this->consignment_filter->addFieldFilter('     c.remote_charges', '1');
            } else {
                $this->consignment_filter->addFieldFilter('     c.remote_charges', '0');
            }
        }

        if (!empty($agent) && trim($agent[0]) != '') {
            //$agentidArray = array();
            //$agentidArray[] = $agent;
            $this->consignment_filter->addFilterIn('    c.agent_id', $agent, 'filter');
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
        
             
       $this->consignment_filter->addConsignmentChargesJoin($this->user->getUserAccountId(), $this->user->getUserType() );

        if($charges == "zero_basic_charges") {
            $this->consignment_filter->addFilter('     ((cc.charge_type_id = 1 AND (cc.cost = 0 OR cc.cost = "")) OR  (cc.charge_type_id IS NULL AND cc.cost IS NULL))', 'filter');
        } else if($charges == "label_charges") {
            $this->consignment_filter->addFilter('     (cc.charge_type_id = 27 AND cc.cost > 0)', 'filter');
        }

        if (!empty($invoice_type)) {
            $this->excelFilterData['Invoice-Type'] = $invoice_type;
            if ($invoice_type == "invoiced") {
                $this->consignment_filter->addFieldNotNullFilter("    cc.invoice_id");
                $this->consignment_filter->addFilter("     cc.invoice_id > 0", 'filter');
            } else if ($invoice_type == "notinvoiced") {
                $this->consignment_filter->addFilter("    ((cc.invoice_id IS NULL OR cc.invoice_id = 0) AND (cost_type='customer' OR cost_type IS NULL)) ", 'filter');
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
        } else {
            if((trim($mawb) != '' && $mawbFilterCheck) || (trim($manifest) != '' && $manifestFilterCheck) || (trim($bagNumber) != "" && $bagNumberCheck)) {
                $this->consignment_filter->addFilterIn('    c.id', [0], 'filter');
            }
        }
 
        if (isset($this->form_vars['group_by']) && trim($this->form_vars['group_by']) == '0') {
            $this->consignment_filter->addGroupBy('pc.id');
        }if (isset($this->form_vars['group_by']) && trim($this->form_vars['group_by']) != '0') {
            $this->consignment_filter->addGroupBy($this->form_vars['group_by']);
        }  else {
            $this->consignment_filter->addGroupBy('c.id');
        }
    }

    function invoiceCsv($invoiceId) {
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

    protected function invoicesCsv($consignmentIds, $invoiceId) {
        $invoiceObj = new Invoices($invoiceId);
        $returnData = [];
        $fileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/csv/';
        $fileName = $invoiceObj->getInvoiceNo() . "_" . time() . ".csv";
        $file_url = $fileLocation . $fileName;
        if (!file_exists($fileLocation)) {
            mkdir($fileLocation, 0777, true);
        }
        $myfile = fopen($file_url, "a") or die("Unable to open file!");

        $chargesTotal = array();
        $heading = ["Account", "Invoice Date", "Label Created Date", "Dispatched Date", "Delivered Date", "Invoice Number", "Order Ref No", "Tracking Number", "MAWB", "Consignment Type", "Status", "Carrier", "Service Name", "Sender City", "Sender Country", "Sender Postcode", "Receiver City", "Receiver Country", "Receiver Postcode", "Number of Pieces", "Weight", "Vol Weight"];
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
                        $returnString .= cleanCsvCall($recievedUserObj->getUserAccount()) . ",";
                        $returnString .= date("d F Y", $invoiceObj->getInvoiceDate()) . ",";
                        $dateLabelCreated = (!empty($consignmentObj->getDateLabelCreated()) ? date("d F Y", $consignmentObj->getDateLabelCreated()) : "");
                        $returnString .= cleanCsvCall($dateLabelCreated) . ",";
                        $dateBooked = (!empty($consignmentObj->getDateBooked()) ? date("d F Y", $consignmentObj->getDateBooked()) : "");
                        $returnString .= cleanCsvCall($dateBooked) . ",";
                        $dateDelivered = (!empty($consignmentObj->getDateDelivered()) ? date("d F Y", $consignmentObj->getDateDelivered()) : "");
                        $returnString .= cleanCsvCall($dateDelivered) . ",";
                        $returnString .= cleanCsvCall($invoiceObj->getInvoiceNo()) . ",";

                        $returnString .= '="' . cleanCsvCall($consignmentObj->getHawb()) . '",';
                        $returnString .= '="' . cleanCsvCall($parcelObj->getTrackingNumber()) . '",';
                        $returnString .= cleanCsvCall($consignmentObj->getMawb()) . ",";
                        $returnString .=  cleanCsvCall(strtoupper($consignmentObj->getConsignmentType())) . ",";
                        $shipment_status = $this->get_shipnment_status($consignmentObj->getShipmentStatus(), "csv");
                        $returnString .= cleanCsvCall($shipment_status) . ",";
                        if ($consignmentObj->getCustomizedServiceId() > 0)
                            $serviceObj = new Services($consignmentObj->getCustomizedServiceId());
                        else
                            $serviceObj = new Services($consignmentObj->getServiceId());

                        $carrierObj = new Carrier($serviceObj->getCarrierId());
                        $returnString .= cleanCsvCall($carrierObj->getCarrier()) . ",";
                        $returnString .= cleanCsvCall($serviceObj->getName()) . ",";

                        $returnString .= cleanCsvCall($consignmentObj->getSenderCity()) . ",";
                        $senderCountryObj = new Country($consignmentObj->getSenderCountryId());
                        $returnString .= cleanCsvCall($senderCountryObj->getName()) . ",";
                        $returnString .= cleanCsvCall($consignmentObj->getSenderPostcode()) . ",";
                        $countryObj = new Country($consignmentObj->getCountryId());
                        $returnString .= cleanCsvCall($consignmentObj->getCity()) . ",";
                        $returnString .= cleanCsvCall($countryObj->getName()) . ",";
                        $returnString .= cleanCsvCall($consignmentObj->getPostcode()) . ",";
                        if ($key == 0) {
                            $returnString .= cleanCsvCall($consignmentObj->getNumberPieces()) . ",";
                            $returnString .= cleanCsvCall($consignmentObj->getChargeWeight()) . ",";
                            $returnString .= cleanCsvCall($consignmentObj->getVolWeight()) . ",";
                            $totalNumberOfPieces = $totalNumberOfPieces + $consignmentObj->getNumberPieces();
                            $totalWeight = $totalWeight + $consignmentObj->getChargeWeight();
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
                                            $returnString .= cleanCsvCall($consignmentChargesFilterObj[0]->getCost()) . ",";
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

                        $returnString .= cleanCsvCall($parcelObj->getLength()) . ",";
                        $returnString .= cleanCsvCall($parcelObj->getWidth()) . ",";
                        $returnString .= cleanCsvCall($parcelObj->getHeight()) . ",";
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

    protected function get_shipnment_status($status, $csv = "") {
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
    ///////////////csv download column changes/////////////
    function downloadCSV($dataConsignment, $agentOption = 0, $consignmentChargeTypeCustomerObj = array(), $consignmentChargeTypeAgentObj = array(),$consignmentChargeTypePurchaseObj = array(), $action_ext) {
        
        $output = [];
        $DOWNLOADABLE_FILE_NAME = "../_assets/csv/exportCsv-" . time() . ".csv"; //= fopen("../_assets/csv/exportCsv-".time().".txt", "w") or die("Unable to open file!");
        $myfile = fopen($DOWNLOADABLE_FILE_NAME, "a") or die("Unable to open file!");
        $currentConsignmentAdd = 0;
        if (count($dataConsignment) > 0) {
            // PUT HEADER FOR ROWS
            if ($currentConsignmentAdd == 0) {
                fwrite($myfile, $this->exportHeader($agentOption, $consignmentChargeTypeCustomerObj, $consignmentChargeTypeAgentObj,$consignmentChargeTypePurchaseObj,  $action_ext));
            }
            $customerChargesIds   = array_keys($this->customerChargesTypeArray);
            $agentChargesIds      = array_keys($this->agentChargesTypeArray);
            $purchaseChargesIds      = array_keys($this->purchaseChargesTypeArray);
            
            foreach ($dataConsignment as $consignmentItem) {
                        //$this->trackScriptExcecution('start row '.$consignmentItem->getId() );
                $rowTowrite = $this->exportRowCsv($consignmentItem, $agentOption, $customerChargesIds, $agentChargesIds,$purchaseChargesIds, $action_ext);
                fwrite($myfile, $rowTowrite);
                $currentConsignmentAdd++;
                       // $this->trackScriptExcecution('end row '.$consignmentItem->getId()  );
            }
            $totalColumns = count($this->csvSelectedColumns) + count($this->customerChargesTypeArray);
            if($totalColumns >0 &&  $this->includeChargesColumn ){
                fwrite($myfile, str_repeat(",",($totalColumns-1)  )." Total , ".$this->grandTotal);
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
    ///////////////csv download column changes/////////////
    function exportRowCsv($consignmentObj, $agentOption = 0, $customerChargesIds, $agentChargesIds,$purchaseChargesIds, $action_ext) {
        
        $returnData = [];             
        $returnString = "";
        $addressfull = "";

        $consignmentAccount = $consignmentObj->getUserAccount();
        if (Permissions::checkFilePermission('hide_subaccount')) {
            $searchConsignmentAccountId = $this->form_vars['user_account_id'];
            $consignmentAccountId = $consignmentObj->getUserAccountId();
            $accountObj = new CustomerAccount();
            $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$this->subAccountArr,$searchConsignmentAccountId);
        }
        $returnData['account']    =   (!empty($consignmentAccount)) ? $consignmentAccount :''   ;
        $invoiceObj = new Invoices($consignmentObj->getChargesInvoiceId());
        $returnData['invoice_no']  =  (!empty($invoiceObj->getInvoiceNo()))? $invoiceObj->getInvoiceNo() :''   ;
        $returnData['order_ref_no'] =  (!empty($consignmentObj->getHawb()))? cleanCsvCall($consignmentObj->getHawb(), 'int') :''   ;
        $returnData['tracking_no'] = (!empty($consignmentObj->getAwb()))? cleanCsvCall($consignmentObj->getAwb(), 'int') :''  ;
        $returnData['mawb'] =  (!empty($consignmentObj->getMawb()))? cleanCsvCall($consignmentObj->getMawb()) :'';
        $returnData['bag_no'] = (!empty($consignmentObj->getBagid()))? cleanCsvCall($consignmentObj->getBagid()):'' ;
        $returnData['service_code'] = (!empty($consignmentObj->getServiceId()))? cleanCsvCall($this->servicesDataArray[$consignmentObj->getServiceId()]['code']):'' ;
        $returnData['service_name'] = (!empty($consignmentObj->getServiceId()))? cleanCsvCall($this->servicesDataArray[$consignmentObj->getServiceId()]['name']):'' ;
        $returnData['service_type'] = (!empty($consignmentObj->getServiceId()))? cleanCsvCall($this->servicesDataArray[$consignmentObj->getServiceId()]['type']):'' ;
        if($consignmentObj->getServiceDisplayId() != $consignmentObj->getServiceId())
            $returnData['product_name'] = (!empty($consignmentObj->getServiceDisplayId()))? cleanCsvCall($this->servicesDataArray[$consignmentObj->getServiceDisplayId()]['name']):'' ;
        else    
            $returnData['product_name'] = "";
        $returnData['ref'] = (!empty($consignmentObj->getReference()))? cleanCsvCall($consignmentObj->getReference()):'' ;
        $returnData['consignment_type'] = (!empty($consignmentObj->getConsignmentType()))? cleanCsvCall($consignmentObj->getConsignmentType()):'' ;
        $returnData['date_label_created'] = (!empty($consignmentObj->getDateLabelCreated()))?  cleanCsvCall(($consignmentObj->getDateLabelCreated() != '' && $consignmentObj->getDateLabelCreated() > 0 ? formatDate(date("d-m-Y", $consignmentObj->getDateLabelCreated())) : '') ):'' ;
        $returnData['date_scanned'] = (!empty($consignmentObj->getDateScanned()))? cleanCsvCall(($consignmentObj->getDateScanned() != '' && $consignmentObj->getDateScanned() > 0 ? formatDate(date("d-m-Y", $consignmentObj->getDateScanned())) : '')) :'' ;
        $returnData['date_dispatched'] = (!empty($consignmentObj->getDateBooked()))?  cleanCsvCall(($consignmentObj->getDateBooked() != '' && $consignmentObj->getDateBooked() > 0 ? formatDate(date("d-m-Y", $consignmentObj->getDateBooked())) : '')):'' ;
 
        $returnData['date_delivered'] = (!empty($consignmentObj->getDateDelivered()))? cleanCsvCall(($consignmentObj->getDateDelivered() != '' && $consignmentObj->getDateDelivered() > 0 ? formatDate(date("d-m-Y", $consignmentObj->getDateDelivered())) : '') )  :''  ;
             
    
        $returnData['collection_country'] = "";
        $returnData['collection_postcode'] = "";
        $returnData['true_shipper'] = cleanCsvCall($consignmentObj->getSenderName() ." ". $consignmentObj->getSenderAddressLine1() . " ". $consignmentObj->getSenderAddressLine2() ." ". $consignmentObj->getSenderCity());
        
        
        $returnData['company'] =  (!empty($consignmentObj->getCompany()))?  cleanCsvCall($consignmentObj->getCompany())  :''    ;
    
        $returnData['contact_name'] =   (!empty($consignmentObj->getContact()))?  cleanCsvCall($consignmentObj->getContact()) :'' ;
        
        if($action_ext == "DOWNLOAD_CS_CSV"){
            $returnData['address_line_1'] = (!empty($consignmentObj->getAddressLine1()))? cleanCsvCall($consignmentObj->getAddressLine1()):'' ;
            $returnData['address_line_2'] = (!empty($consignmentObj->getAddressLine2()))? cleanCsvCall($consignmentObj->getAddressLine2()):'' ;
            $returnData['address_line_3'] =  (!empty($consignmentObj->getAddressLine3()))? cleanCsvCall($consignmentObj->getAddressLine3()):'' ;
        }
        else{
            $addressfull = cleanCsvCall($consignmentObj->getAddressLine1()) . " " . cleanCsvCall($consignmentObj->getAddressLine2()) . " " . cleanCsvCall($consignmentObj->getAddressLine3()) ;
           
            $returnData['address_line_1'] .= (!empty($consignmentObj->getAddressLine1()))? cleanCsvCall($consignmentObj->getAddressLine1()):'' ;
            $returnData['address_line_1'] .= (!empty($consignmentObj->getAddressLine2()))? cleanCsvCall($consignmentObj->getAddressLine2()):'' ;
            $returnData['address_line_1'] .=  (!empty($consignmentObj->getAddressLine3()))? cleanCsvCall($consignmentObj->getAddressLine3()):'' ;
            
    }
        $returnData['city'] = (!empty($consignmentObj->getCity()))? cleanCsvCall($consignmentObj->getCity())  :''   ;
        $returnData['country'] =  (!empty($consignmentObj->getCountryId()))? cleanCsvCall( $this->countryDataArray[trim($consignmentObj->getCountryId())]):""  ;

        $returnData['postcode'] =  (!empty($consignmentObj->getPostcode())) ? cleanCsvCall($consignmentObj->getPostcode()): "" ;
        $returnData['telephone'] =  (!empty($consignmentObj->getTelephone())) ? cleanCsvCall($consignmentObj->getTelephone()) : ""   ;
        $returnData['description'] =  (!empty($consignmentObj->getDescription())) ? cleanCsvCall($consignmentObj->getDescription()): "" ; 
        $returnData['no_of_pieces'] =  (!empty($consignmentObj->getNumberPieces())) ? cleanCsvCall($consignmentObj->getNumberPieces()): "" ;

           
        if ( !empty($consignmentObj->getisDeadWeightChargable())  && $consignmentObj->getisDeadWeightChargable() > 0) {
            $returnData['chargable_weight'] = (!empty($consignmentObj->getWeight())) ? cleanCsvCall($consignmentObj->getWeight()): "";
        } else {
            $returnData['chargable_weight'] = (!empty($consignmentObj->getChargeWeight())) ? cleanCsvCall($consignmentObj->getChargeWeight()): ""  ;
        }


        $returnData['weight'] = (!empty($consignmentObj->getWeight())) ? cleanCsvCall($consignmentObj->getWeight()): ""  ;
        $returnData['vol_weight'] = (!empty($consignmentObj->getVolWeight())) ? cleanCsvCall($consignmentObj->getVolWeight()): "" ;
     
        if (!empty($consignmentObj->getIsDoc()) && $consignmentObj->getIsDoc() == 1) {
            $doc = "DOX";
        } else {
            $doc = "NDX";
        }
        $returnData['dox_ndx'] = cleanCsvCall($doc)  ;       
        $returnData['value'] =  (!empty($consignmentObj->getValue())) ? cleanCsvCall($consignmentObj->getValue()): ""  ;
        $returnData['currency'] =  (!empty($consignmentObj->getCurrency())) ? cleanCsvCall($consignmentObj->getCurrency()): ""  ;
        if (!empty($consignmentObj->getRemoteCharges()) && $consignmentObj->getRemoteCharges() == 1) {
            $remoteArea = "Yes";
        } else {
            $remoteArea = "No";
        }
        
        $returnData['remotearea'] = cleanCsvCall($remoteArea) ;
        
        if (in_array('last_status', $this->csvSelectedColumns)) {
            $trackingFilter = new TrackingDataFilter();
            $trackingFilter->addTrackingNumberFilter($consignmentObj->getAwb());
            $trackingFilter->AddOrderByDate(false);
            $trackingFilter->setLimit("1");
            $trackingFilterObj = $trackingFilter->getColumnList('carrier_desc,date_created, signatory', "", 1);


            if (count($trackingFilterObj) > 0) {
                if (trim($trackingFilterObj[0]->getSignatory()) != '')
                    $returnData['last_status'] = cleanCsvCall($trackingFilterObj[0]->getCarrierDesc()) . " to " . cleanCsvCall($trackingFilterObj[0]->getSignatory());
                else
                    $returnData['last_status'] = cleanCsvCall($trackingFilterObj[0]->getCarrierDesc());
                $returnData['last_status_date'] = $trackingFilterObj[0]->getDateCreated();
            } else {
                $returnData['last_status'] = "";
                $returnData['last_status_date'] = '';
            }
        }
   
             $returnData['length'] = (!empty($consignmentObj->getLength())) ? cleanCsvCall($consignmentObj->getLength()): ""  ; 
             $returnData['width'] = (!empty($consignmentObj->getWidth())) ? cleanCsvCall($consignmentObj->getWidth()): "" ;
             $returnData['height'] =   (!empty($consignmentObj->getHeight())) ? cleanCsvCall($consignmentObj->getHeight()): "" ;
        
        
 
      $returnData['carrier_routing_code'] =  (!empty($consignmentObj->getRoutingCodeEur())) ? cleanCsvCall($consignmentObj->getRoutingCodeEur()): ""  ; ;
      if($consignmentObj->getCountryRegion() == "R1" && $consignmentObj->getIossNumber() != ''){
        $returnData['ioss_number'] = $consignmentObj->getIossNumber();
        if(strtolower($consignmentObj->getCurrency()) == 'eur'){
            $conValue = $consignmentObj->getValue();
        }else{
            $conValue = Currency::convertCurrency($consignmentObj->getCurrency(),"EUR",$consignmentObj->getValue());
        }
        $returnData['ioss_charges'] = number_format(($conValue * $consignmentObj->getVatRate()), 3);
        $returnData['vat_rates'] = $consignmentObj->getVatRate() * 100 . "%";
      }
      else
      {
        $returnData['ioss_number'] = "";
        $returnData['ioss_charges'] = "";
        $returnData['vat_rates'] = "";
      }
      $flipkeysArray = array_flip($this->csvSelectedColumns);
      
      
      $resultArray =   array_intersect_key( $returnData,$flipkeysArray);
 
        $returnData = [];
        
     if($this->includeChargesColumn){
        $customerTotal = 0;
        $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentObj->getId(),$consignmentObj->getUserId);
        $consignmentChagesFilter = new ConsignmentChargesTypesFilter();
        $consignmentChagesFilter->leftJoin("(
	SELECT  
                cost, charge_type_id
        FROM
                consignment_charges cc
        WHERE
        charge_type_id IN ('" . implode("','", $customerChargesIds) . "')
        AND account_id = '" . $user_account_id . "'
        AND cost_type = 'customer'
        AND consignment_id = '" . $consignmentObj->getId() . "') cc", "cct.id = cc.charge_type_id");
        $consignmentChagesFilter->addFilter(" cct.id IN ('". implode("','", $customerChargesIds)."') ");
        $consignmentChagesFilterObj = $consignmentChagesFilter->getColumnList(" cc.cost,cct.title");
        if (count($consignmentChagesFilterObj) > 0) {
            foreach ($consignmentChagesFilterObj as $customerChargeData) {
                     if(!empty($customerChargeData->getCost())){
                    $returnData[$customerChargeData->getTitle()] = cleanCsvCall($customerChargeData->getCost());
                    $customerTotal += (float) $customerChargeData->getCost() ;
                    }else{
                    $returnData[$customerChargeData->getTitle()] = "";
                    $customerTotal += 0;
                    } 
 
            }
        }
     }
  
        $returnData['total'] =  $customerTotal;
        
        $this->grandTotal = $this->grandTotal + $customerTotal;
 
        if ($this->includeAgentChargesColumn == 1) {
            $agentTotal = 0;
            $consignmentChagesFilter = new ConsignmentChargesTypesFilter();
            $consignmentChagesFilter->leftJoin("(
	SELECT  
                cost, charge_type_id
        FROM
                consignment_charges cc
        WHERE
        charge_type_id IN ('" . implode("','", $agentChargesIds) . "')
        AND account_id = '" . $user_account_id . "'
        AND cost_type = 'agent'
        AND consignment_id = '" . $consignmentObj->getId() . "') cc", "cct.id = cc.charge_type_id");
            $consignmentChagesFilter->addFilter(" cct.id IN ('" . implode("','", $agentChargesIds) . "') ");
            $consignmentChagesFilterObj = $consignmentChagesFilter->getColumnList(" cc.cost,cct.title");
            if (count($consignmentChagesFilterObj) > 0) {
                foreach ($consignmentChagesFilterObj as $agentChargeData) {
                    if (!empty($agentChargeData->getCost())) {
                        $returnData['Agent '.$agentChargeData->getTitle()] = cleanCsvCall($agentChargeData->getCost());
                        $agentTotal += cleanCsvCall($agentChargeData->getCost());
                    } else {
                        $returnData['Agent '.$agentChargeData->getTitle()] = "";
                    }
                }
            }
            $returnData['Agent total'] =  $agentTotal;
                    
        }
        
        
        if ($this->includePurchaseChargesColumn == 1) {
            $purchaseTotal = 0;
            $consignmentChagesFilter = new ConsignmentChargesTypesFilter();
            $consignmentChagesFilter->leftJoin("(
	SELECT  
                cost, charge_type_id
        FROM
                consignment_charges cc
        WHERE
        charge_type_id IN ('" . implode("','", $agentChargesIds) . "')
        AND account_id = '" . $user_account_id . "'
        AND cost_type = 'purchase_invoice'
        AND consignment_id = '" . $consignmentObj->getId() . "') cc", "cct.id = cc.charge_type_id");
            $consignmentChagesFilter->addFilter(" cct.id IN ('" . implode("','", $purchaseChargesIds) . "') ");
            $consignmentChagesFilterObj = $consignmentChagesFilter->getColumnList(" cc.cost,cct.title");
            if (count($consignmentChagesFilterObj) > 0) {
                foreach ($consignmentChagesFilterObj as $purchaseChargeData) {
                    if (!empty($purchaseChargeData->getCost())) {
                        $returnData['Purchase '.$purchaseChargeData->getTitle()] = cleanCsvCall($purchaseChargeData->getCost());
                        $purchaseTotal += cleanCsvCall($purchaseChargeData->getCost());
                    } else {
                        $returnData['Purchase '.$purchaseChargeData->getTitle()] = "";
                    }
                }
            }
            $returnData['Purchase total'] =  $purchaseTotal;
        }
        $result = array_merge($resultArray, $returnData);
        $returnString = implode(" ,", $result);
          $returnString .= "\r\n"; 
        return $returnString;
    }
    ///////////////csv download column changes/////////////
    function getCsvColumnsDefault($action_ext){
        if($action_ext == "DOWNLOAD_CS_CSV"){
            $columns = ["account"=>"Account", "invoice_no"=>"Invoice No", "order_ref_no"=>"Order Ref No", "tracking_no"=>"Tracking Number", "mawb"=>"MAWB", "bag_no"=>"Bag No","service_code"=> "Service Code", "service_name"=>"Service Name",  "product_name"=>"Product Service Name", "ref"=>"REF", "consignment_type"=>"Consignment Type", "date_label_created"=>"Date Label Created","date_scanned"=> "Date Scanned", "date_dispatched"=>"Date Dispatched", "date_delivered"=>"Date Delivered", "collection_country"=>"Collection Country", "collection_postcode"=>"Collection  Postcode","true_shipper"=>"True Shipper Adress", "company"=>"Company","contact_name"=>"Contact Name", "address_line_1"=>"Address Line 1" , "address_line_2"=>"Address Line 2" , "address_line_3"=>"Address Line 3", "city"=>"City", "country"=>"Country", "postcode"=>"Postcode","telephone"=>"Telephone","description"=>"Description", "no_of_pieces"=>"Number of Pieces", "chargeable_weight"=>"Chargable Weight", "weight"=>"Weight", "vol_weight"=>"Vol Weight", "dox_ndx"=>"DOX/NDX", "value"=>"Value", "currency"=>"Currency", "remotearea"=>"Remotearea","last_status"=>"Last Status", "last_status_date"=>"Last Status Date", "length"=>"Length", "width"=>" Width", "height"=>"Height","carrier_routing_code"=>"Carrier Routing Code"];
        }
        else
        {
            $columns = ["account"=>"Account","invoice_no"=>"Invoice No", "order_ref_no"=>"Order Ref No", "tracking_no"=>"Tracking Number", 
			 "mawb"=>"MAWB", "bag_no"=>"Bag No", "service_code"=>"Service Code", "service_name"=>"Service Name",   "product_name"=>"Product Service Name",
			  "ref"=>"REF", "consignment_type"=>"Consignment Type", "date_label_created"=>"Date Label Created", "date_scanned"=>"Date Scanned",
			   "date_dispatched"=>"Date Dispatched", "date_delivered"=>"Date Delivered", "collection_country"=>"Collection Country",
			    "collection_postcode"=>"Collection  Postcode", "true_shipper"=>"True Shipper Adress", "company"=>"Company","contact_name"=>"Contact Name", 
				"address_line_1"=>"Address", "city"=>"City", "country"=>"Country", "postcode"=>"Postcode","telephone"=>"Telephone",
				"description"=>"Description", "no_of_pieces"=>"Number of Pieces", "chargable_weight"=>"Chargable Weight", "weight"=>"Weight",
				 "vol_weight"=>"Vol Weight", "dox_ndx"=>"DOX/NDX","value"=>"Value", "currency"=>"Currency", "remotearea"=>"Remotearea",
				 "last_status"=>"Last Status",
				 "last_status_date"=>"Last Status Date", 
				 "length"=>"Length", "width"=>" Width",  "height"=>"Height","carrier_routing_code"=>"Carrier Routing Code", "ioss_number"=>"IOSS Number", "ioss_charges"=> "IOSS VAT Payable EUR", "vat_rates"=>"Vat Rates"];
        }
        return $columns;
    }
    ///////////////csv download column changes/////////////
    function exportHeader($agentOption = 0, $consignmentChargeTypeCustomerObj = array(), $consignmentChargeTypeAgentObj = array(),$consignmentChargeTypePurchaseObj = array(), $action_ext) {
        $rowToWrite = '';
        
        
         $header_row = $this->getCsvColumnsDefault($action_ext);
         
       /* if($action_ext == "DOWNLOAD_CS_CSV"){
            $header_row = ["Account", "Invoice No", "Order Ref No", "Tracking Number", "MAWB", "Bag No", "Service Code", "Service Name", "Service Type", "Product Name", "REF", "Consignment Status", "Date Label Created", "Date Scanned", "Date Dispatched", "Date Delivered", "Collection Country", "Collection  Postcode", "Company","Contact Name", "Address Line 1" , "Address Line 2" , "Address Line 3", "City", "Country", "Postcode","Telephone","Description", "Number of Pieces", "Chargable Weight", "Weight", "Vol Weight", "DOX/NDX", "Value", "Currency", "Remotearea","Last Status", "Length", " Width", "Height","Carrier Routing Code"];
        }
        else
        {
            $header_row = ["Account", "Invoice No", "Order Ref No", "Tracking Number", "MAWB", "Bag No", "Service Code", "Service Name", "Service Type", "Product Name", "REF", "Consignment Status", "Date Label Created", "Date Scanned", "Date Dispatched", "Date Delivered", "Collection Country", "Collection  Postcode", "Company","Contact Name", "Address", "City", "Country", "Postcode","Telephone","Description", "Number of Pieces", "Chargable Weight", "Weight", "Vol Weight", "DOX/NDX", "Value", "Currency", "Remotearea","Last Status", "Length", " Width", "Height","Carrier Routing Code"];
        }*/
        
        foreach ($header_row as $key=>$field) {
            if(in_array($key, $this->csvSelectedColumns)){
				if($key == 'last_status_date')
					continue;
                $rowToWrite .= $field . ",";
                if($key == 'last_status')
                    $rowToWrite .= $header_row['last_status_date'] . ",";
            }
        }
        
        if(count($this->customerChargesTypeArray)>0)
        {
            foreach ($this->customerChargesTypeArray as $ctitle) {
                $rowToWrite .= $ctitle . ",";
            }
            $rowToWrite .= "Customer Total,";
            
        }
        
        if ($this->includeAgentChargesColumn && count($this->agentChargesTypeArray )>0) {
            foreach ($this->agentChargesTypeArray as $atitle) {
                $rowToWrite .=   $atitle . ",";
            }
            $rowToWrite .= "Agent Total,";
        }
        if ($this->includePurchaseChargesColumn && count($this->purchaseChargesTypeArray )>0) {
            foreach ($this->purchaseChargesTypeArray as $atitle) {
                $rowToWrite .=   $atitle . ",";
            }
            $rowToWrite .= "Purchase Total,";
        }
        $rowToWrite .= "\r";
        return $rowToWrite;
    }

    public function removeChar($text) {
        $textNew = $text;
        $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("'", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace('"', '', preg_replace('/[\$,]/', '', $textNew));

        $textNew = preg_replace('/[\n,]/', '', $textNew);

        return $textNew;
    }

    public function getCurrencyFormat($amount, $currency) {
        $amt = number_format((float) $amount, 2, '.', '');
        return $amt . " " . $currency;
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
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">


        
        <style>
            .label-account{
                font-size: 12px;
                font-weight: bold;
            }
            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
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
                           // $('#calculated_zero_price').html(response.getZeroPriced);
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
                                [20, 50, 100, 150, 500],
                                [20, 50, 100, 150, 500] // change per page values here 
                            ],
                            //                            "oLanguage": {
                            //                                "sEmptyTable": "<?php //echo ((isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') ? 'No records found' : 'Please search to view data');        ?>"
                            //                            },
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {

                                }
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
                                headers: {

                                }
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
                var datatableShipments =  grid.getDataTable();
                    <?php    if (Permissions::checkFilePermission('show_invoice_data_shipments')) {?>
                         datatableShipments.columns([9, 10]).visible(true);
                    <?php  }else{?>
                           datatableShipments.columns([9, 10]).visible(false);
                    <?php  }?>
                    
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

                });
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        formate: "dd-mm-yyyy"
                    });
                }
                $('#user_account_id').change(function () {
                    var userAccountId = $(this).val();
                    get_carriers(userAccountId);
                });
                $('#carriers').change(function () {
                    $("#service").val("");
                    $("#service").select2();
                    var carrierId = $(this).val();
                    change_carriers(carrierId);
                });
                $(".customer_charges, .agent_charges, .purchase_invoice_charges").blur(function () {
                    updateInvoiceTotal();
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
                    tr += '<button type="button" class="btn btn-danger btn-sm remove_extra" data-type="' + extra_type + '" data-row="' + extra_type + '_row_' + totals_extras + '">&nbsp;<i class="fa fa-trash-o"></i> Del&nbsp;</button>';
                    tr += '</td>';
                    tr += '</tr>';

                    $("#" + extra_type + "_extras_body").append(tr);

                    $("#agent_id").val('');
                    $("#charge_type").val('');
                    $("#description").val('');
                    $("#extra_total").val('');
                    $("#" + extra_type + "_totals_extras").val(totals_extras);
                    updateExtra(extra_type);
                });
                $(".extras").click(function () {
                    $(".extras_container").hide();
                    var extra_type = $(this).data('type');
                    $("#hidden_extra_type").val(extra_type);
                    var title = extra_type == 'agent' ? 'Supplier' : 'Customer';
                    $("#" + extra_type + "_extras_container").show();
                    $("#pricing-extra").find(".modal-title").html(title + ' Extra Charges');
                    /* $("#label_extra_charges").html(title); */
                    $("#pricing-extra").modal('show');
                });
                $(document).on('click', '.remove_extra', function () {
                    var row = $(this).data("row");
                    var extra_type = $(this).data("type");
                    if (confirm("Are you sure you want to remove?")) {
                        $("#" + row).remove();
                        updateExtra(extra_type);
                    }
                });
                $("#zero_pricing").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $('.customer_charges').val(0);
                        $('#customer_total').val(0);
                    }
                });
                $("#pod_reason").change(function () {

                });
                $("#upload_pod").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $("#pod_box").show();
                    } else {
                        $("#pod_box").hide();
                    }
                });
                $("#manual_awb").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $("#trackingNumber").show();
                    } else {
                        $("#trackingNumber").hide();
                    }
                });
                $("#send_email_to").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        $(".email_content_pod_container").show();
                    } else {
                        $(".email_content_pod_container").hide();
                    }
                });
                $("#update_booking_date_btn").click(function () {
                    $("#update_booking_date_btn").prop('disabled', true);
                    var booked_date = $.trim($("#update_not_scanned_shipments #booked_date").val());
                    var consignment_ids = $.trim($("#update_not_scanned_shipments #consignment_ids").val());
                    if (booked_date == "") {
                        swal("Sorry!", "Select booked date.", "error");
                        $("#update_booking_date_btn").prop('disabled', false);
                        return false;
                    }
                    if (consignment_ids == "") {
                        swal("Sorry!", "No shipment seleted.", "error");
                        $("#update_booking_date_btn").prop('disabled', false);
                        return false;
                    }
                    var form_data = $("#update_booking_date_frm").serializeArray();
                    $.post("ajaxbooking.php", form_data, function (response) {
                        $("#booking_date_msg").removeClass('alert-success');
                        $("#booking_date_msg").removeClass('alert-danger');
                        if (response == 'success') {
                            $("#booking_date_msg").addClass('alert-success');
                            $("#booking_date_msg").html('updated successfully');
                            $("#update_not_scanned_shipments #booked_date").val('');
                            $("#update_not_scanned_shipments #consignment_ids").val('');
                        } else {
                            $("#booking_date_msg").addClass('alert-danger');
                            $("#booking_date_msg").html('Some error occour while updating.');
                        }
                        $("#booking_date_msg").show();
                        $("#update_booking_date_btn").prop('disabled', false);
                    });
                });
                var clock;   
                 ///////////////csv download column changes/////////////
                $(".btnExportData").click(function () {
                    $('#a-d-csv-modal-msg').show();
                    $('#btnExportDataSubmit').show();
                    $('#backColumnBtn').hide();
                    
                    var totalShipmentCount = $('body').find('span.rowclasscount').data('count');
                    $('#a-d-csv-totalInvoiceConsignment').html(totalShipmentCount);
                    if (totalShipmentCount == '' || totalShipmentCount == '0')
                    {
                        return;
                    }
                    var csv_downlaod_type = $(this).data("csvtype");
                    $("#a-d-csv-modal").modal("show");
                    $('#check_columns_screen').show();
                    $('#download_screen').hide();
                   
                    $.ajax({
                        type: "POST",
                        url: "shipment_list_manage.php", // your php file name
                        dataType: "html",
                        data: {'csv_action': 'download_csv_columns',"action_ext":csv_downlaod_type},
                        success: function (data)
                        {
                            $('#check_columns_html').html(data);
                            $('#make-switch-columns').attr('checked', true);
                             $('#make-switch-columns').bootstrapSwitch();
                             $('.csvCheckbox').iCheck({
                                checkboxClass: 'icheckbox_minimal-grey',
                            });
                        },
                        error: function (errorString)
                        {
                           
                        }
                    });
                 
             
                });
                ///////////////csv download column changes/////////////
                $(".btnExportDataSubmit").click(function () {
                    $('#a-d-csv-modal-msg').show();
                    $('#backColumnBtn').show();
                    $('.a-d-csv-modal-close').hide();
                    $('#btnExportDataSubmit').hide();
                    var totalShipmentCount = $('body').find('span.rowclasscount').data('count');
                    $('#a-d-csv-totalInvoiceConsignment').html(totalShipmentCount);
                    if (totalShipmentCount == '' || totalShipmentCount == '0')
                    {
                        return;
                    }
                    var csv_downlaod_type = $(this).data("csvtype");
                    $('#check_columns_screen').hide();
                    $('#download_screen').show();
                    
                    clock = $('.a-d-csv-clock').FlipClock({
                        clockFace: 'MinuteCounter',
                        callbacks: {
                            interval: function () {
                                var time = this.factory.getTime().time;
                                if (time) {
                                    //console.log('interval', time);
                                }
                            }
                        }
                    });
                    var fileName = '../_assets/csv/exportCsv-' + $.now() + '.csv';
                    $("#a-d-csv-error-information").html('').hide();
                    $('.a-d-csv-clock').show();
                    $("#a-d-csv-account-loading").show();
                    $("#a-d-csv-progress-bar-new").attr('style', 'width:100%; display:block');
                    progressBarCompletion = Math.ceil(100);
                    $("#a-d-csv-progress-bar-completion").html(progressBarCompletion);
                    $("#a-d-csv-progress-bar-completion").html('Please wait... ');

                    var form_data = $("#update_pricing_details_form").serializeArray();
                    $(".icheck:not(:checked)").each(function () {
                        form_data.push({name: this.name, value: '0'});
                    });
                    var checkedColumns = [];
                    $('.csvCheckbox:checkbox:checked').each(function () {
                           form_data.push({name: this.name, value:$(this).val()});
                    });
                     //form_data.push({name: "checkedColumns", value:checkedColumns});
                    
                    var includeAgent = 0;
                    if ($('#include_agent').is(":checked")) {
                        includeAgent = 1;
                    }
                    
                    form_data.push({name: "csv_action", value: 'download_csv'});
                    form_data.push({name: "action_ext", value: csv_downlaod_type});
                    form_data.push({name: "file_name", value: fileName});
                    form_data.push({name: "include_agent", value: includeAgent});
                    $.ajax({
                        type: "POST",
                        url: "shipment_list_manage.php", // your php file name
                        data: form_data,
                        dataType: "json",
                        success: function (data)
                        {
                            progressBarCompletion = Math.ceil(100);
                            $("#a-d-csv-progress-bar-new").attr('style', 'width:' + progressBarCompletion + '%; display:block');
                            $("#a-d-csv-progress-bar-completion").html(progressBarCompletion);
                            $("#a-d-csv-progress-bar-completion").html('Please wait... Merging inprogress');
                            $("#a-d-csv-progress-bar-completion").html('Completed');
                            $("#a-d-csv-error-information").show();
                            $("#a-d-csv-error-information").html(data.result);
                            $("#a-d-csv-account-loading").hide();
                            $('.a-d-csv-clock').hide();
                            $('#a-d-csv-modal-msg').hide();
                            $('.a-d-csv-modal-close').show();
                        },
                        error: function (errorString)
                        {
                            progressBarCompletion = Math.ceil(100);
                            $("#a-d-csv-progress-bar-new").attr('style', 'width:' + progressBarCompletion + '%; display:block');
                            $("#a-d-csv-progress-bar-completion").html('There is an error while downloading. Please contact to itsupport@oneworldexpress.com with ' + fileName);
                            $("#a-d-csv-progress-bar-completion").html('There is an error while downloading. Please contact to itsupport@oneworldexpress.com with ' + fileName);
                            $("#a-d-csv-error-information").show();
                            $("#a-d-csv-error-information").html('There is an error while downloading. Please contact to itsupport@oneworldexpress.com  with ' + fileName);
                            $("#a-d-csv-account-loading").hide();
                            $('.a-d-csv-modal-close').show();
                        }
                    });
                 
                });
                
                ///////////////csv download column changes/////////////
                $("#backColumnBtn").click(function () {
                     $('#check_columns_screen').show();
                    $('#download_screen').hide();
                    $('#backColumnBtn').hide();
                    $('#btnExportDataSubmit').show();
                });
                
             

              
                $("#update_shipnment_status_btn").click(function () {
                    var form_data = $("#update_pricing_details_form").serializeArray();
                    $.ajax({
                        type: "POST",
                        url: "shipment_list_manage.php?action=update_shipnment_status",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if (data.status == "success") {
                                grid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });
                $(document).on('click', '#btnGPReportData', function () {
                    var form_data = $("#update_pricing_details_form").serializeArray();
                    $(".icheck:not(:checked)").each(function () {
                        form_data.push({name: this.name, value: '0'});
                    });
                    form_data.push({name: "action", value: 'gp_report_excel'});
                    
                    var totalShipmentCount = $('#total-number-of-items').val();
                    if (totalShipmentCount == '' || totalShipmentCount == '0')
                    {
                        
                        return;
                    }
                    $("#cr_report_text").html('');
                     $("#excel-totalConsignment").html(totalShipmentCount);
                    if(totalShipmentCount > 25000 ){
                         form_data.push({ name: "report_type", value: 'summary'});
                         form_data.push({ name: "group_by", value: ' ua.id,s.id '});
                         $("#excel_report_title").html('GP Report (Summary) Downloading....');
                         $("#cr_report_text").html('You can only download detail GP report less than 25000 records. For '+totalShipmentCount+' records you can download GP summary report. Please contact to itsupport@oneworldexpress.com for detail report. ') ;
                                
                            }else{
                                $("#excel_report_title").html('GP Report (Detail) Downloading....');
                          form_data.push({name: "report_type", value: "detail" });
                        }
                        
                            $("#response_cr_report_response").html('');
                            $("#cr-report-progress-bar-new").attr('style', 'width:100%; display:block');
                            progressBarCompletion = Math.ceil(50);
                            $("#cr-report-progress-bar-completion").html(progressBarCompletion);
                            $("#cr-report-progress-bar-completion").html('Please wait... ');
                            $("#response_cr_report_progress").show();
                            $("#download_excel_comparison_report").modal("show");
                          
                            
                            $.ajax({
                                type: "POST",
                                url: "shipment_list_manage.php",
                                data: form_data,
                                dataType: "json",
                                  success: function (data)
                                {
                                    progressBarCompletion = Math.ceil(100);
                                    $("#cr-report-progress-bar-new").attr('style', 'width:' + progressBarCompletion + '%; display:block');
                                    $("#cr-report-progress-bar-completion").html(progressBarCompletion);
                                    $("#cr-report-progress-bar-completion").html('Please wait... Merging inprogress');
                                    $("#cr-report-progress-bar-completion").html('Completed');
                                    $("#response_cr_report_response").html(data.result);
                                    $("#response_cr_report_progress").hide();
         
                                },
                                error: function (errorString)
                                {
                                     $("#response_cr_report_progress").hide();
                                     $("#response_cr_report_response").html('There is an error while downloading. Please contact to itsupport@oneworldexpress.com');
                                }
                            });
                });
                
                $(document).on('click', '#btnVarianceReportData', function () {
                    var form_data = $("#update_pricing_details_form").serializeArray();
                    $(".icheck:not(:checked)").each(function () {
                        form_data.push({name: this.name, value: '0'});
                    });
                    form_data.push({name: "action", value: 'variance_report_excel'});
                    
                    var totalShipmentCount = $('#total-number-of-items').val();
                    if (totalShipmentCount == '' || totalShipmentCount == '0')
                    {
                        
                        return;
                    }
                    $("#cr_report_text").html('');
                     $("#excel-totalConsignment").html(totalShipmentCount);
                    if(totalShipmentCount > 25000 ){
                         $("#excel_report_title").html('Variance Report (Summary) Downloading....');
                         form_data.push({ name: "report_type", value: 'summary'});
                         form_data.push({ name: "group_by", value: ' ua.id,s.id '});
                        
                         $("#cr_report_text").html('You can only download detail Discrepancy report less than 25000 records. For '+totalShipmentCount+' records you can download variance summary report. Please contact to itsupport@oneworldexpress.com for detail report. ') ;
                                
                            }else{
                                 $("#excel_report_title").html('Variance Report (Detail) Downloading....');
                          form_data.push({name: "report_type", value: "detail" });
                        }
                        
                            $("#response_cr_report_response").html('');
                            $("#cr-report-progress-bar-new").attr('style', 'width:100%; display:block');
                            progressBarCompletion = Math.ceil(50);
                            $("#cr-report-progress-bar-completion").html(progressBarCompletion);
                            $("#cr-report-progress-bar-completion").html('Please wait... ');
                            $("#response_cr_report_progress").show();
                            $("#download_excel_comparison_report").modal("show");
                          
                            
                            $.ajax({
                                type: "POST",
                                url: "shipment_list_manage.php",
                                data: form_data,
                                dataType: "json",
                                  success: function (data)
                                {
                                    progressBarCompletion = Math.ceil(100);
                                    $("#cr-report-progress-bar-new").attr('style', 'width:' + progressBarCompletion + '%; display:block');
                                    $("#cr-report-progress-bar-completion").html(progressBarCompletion);
                                    $("#cr-report-progress-bar-completion").html('Please wait... Merging inprogress');
                                    $("#cr-report-progress-bar-completion").html('Completed');
                                    $("#response_cr_report_response").html(data.result);
                                    $("#response_cr_report_progress").hide();
         
                                },
                                error: function (errorString)
                                {
                                     $("#response_cr_report_progress").hide();
                                     $("#response_cr_report_response").html('There is an error while downloading. Please contact to itsupport@oneworldexpress.com');
                                }
                            });
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
            
            $(document).on("ifChanged", '.csvCheckbox', function(event) { 
                if($('input.csvCheckbox:checked').length > 0){
                    $('#btnExportDataSubmit').show();
                    }else{
                        $('#btnExportDataSubmit').hide();
                        }
            });

             ///////////////csv download column changes/////////////
            function checkColumns(source){
                    // $('.csvCheckbox').not(source).prop('checked', source.checked);

                     if ($(source).is(":checked")) {
                         $('.csvCheckbox').iCheck('check');
                     } else {
                         $('.csvCheckbox').iCheck('uncheck');
                     }
                     $('.csvCheckbox').not(source).prop('checked', source.checked);
                 }
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
                var purchase_invoice_total = 0;
                var margin = 0;
                $(".customer_charges").each(function () {
                    var val = $.trim($(this).val());
                    val = (val == '' ? 0 : val);
                    customer_total += parseFloat(val);
                });
                $(".agent_charges").each(function () {
                    //if($(this).attr('id') == 'agent_reference'){
                        
                    //}else{
                        var val = $.trim($(this).val());
                        val = (val == '' ? 0 : val);
                        agent_total += parseFloat(val);
                    //}
                    
                    
                });
                $(".purchase_invoice_charges").each(function () {
                    if($(this).attr('id') == 'purchase_invoice_reference'){
                        
                    }else{
                        var val = $.trim($(this).val());
                        val = (val == '' ? 0 : val);
                        purchase_invoice_total += parseFloat(val);    
                    }
                });
                
                margin = parseFloat(customer_total - agent_total);
                $("#customer_total").val(customer_total);
                $("#agent_total").val(agent_total);
                $("#purchase_invoice_total").val(purchase_invoice_total);
                
            }
            function updatePricingDetails() {
                var form_data = $("#update_pricing_details_form").serializeArray();
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
                var form_data = $("#update_pricing_details_form").serializeArray();
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
            
            function updateObaDocket() {
                var form_data = $("#update_pricing_details_form").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=update_oba_docket",
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        grid.getDataTable().ajax.reload();
                        var html = '<div class="alert alert-success">' + data.message + '</div>';
                        $('#res_oba_docket_message').html(html);
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            
            
            
            function updateManaualPod() {
                //var form_data = $("#update_pricing_details_form").serializeArray();
                var form_data = new FormData();
                //var file_data =  $("#pod_image")[0].files[0];
                //form_data.append('pod_image', file_data);
                var data = $('#update_pricing_details_form').serializeArray();
                
                $.each(data, function (key, input) {
                    form_data .append(input.name, input.value);
                });
                
                var file_data = $('input[name="pod_image"]')[0].files;
                for (var i = 0; i < file_data.length; i++) {
                    form_data.append("pod_image", file_data[i]);
                }
                //$(".icheck:not(:checked)").each(function () {
                  //  form_data.push({name: this.name, value: '0'});
                //});
                //var file = $("#pod_image")[0].files[0];
                //var fileName = file.name;
                //form_data.push({name: 'pod_file',value:fileName});
                
                $.ajax({
                    type: "POST",
                    url: "shipment_list_manage.php?action=update_manaual_pod",
                    data: form_data,
                    dataType: "json",
                    contentType: false,
                    processData: false,
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
                    scrollTop: $("#" + id).offset().top},
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
                                        var form_data = $("#update_pricing_details_form").serializeArray();
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
                                    if (inputValue === false)
                                        return false;

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
                                                        var form_data = $("#update_pricing_details_form").serializeArray();
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
                            var form_data = $("#update_pricing_details_form").serializeArray();
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
                        var form_data = $("#update_pricing_details_form").serializeArray();
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
                    var form_data = $("#update_pricing_details_form").serializeArray();
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
                                    var form_data = $("#update_pricing_details_form").serializeArray();
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
                    var form_data = $("#update_pricing_details_form").serializeArray();
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
                var form_data = $("#update_pricing_details_form").serializeArray();
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
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if ($("#update_vol_weight_msg").hasClass("alert-info"))
                            $("#update_vol_weight_msg").removeClass("alert-info");
                        if ($("#update_vol_weight_msg").hasClass("alert-danger"))
                            $("#update_vol_weight_msg").removeClass("alert-danger");
                        if ($("#update_vol_weight_msg").hasClass("alert-success"))
                            $("#update_vol_weight_msg").removeClass("alert-success");
                        if(response.status){
                            $("#update_vol_weight_msg").addClass("alert-success");
                        } else {
                            $("#update_vol_weight_msg").addClass("alert-danger");
                        }
                     
                        $("#update_vol_weight_msg").html(response.message);
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
                                var form_data = $("#update_pricing_details_form").serializeArray();
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
                var form_data = $("#update_pricing_details_form").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                form_data.push({name: 'invoice_date', value: $('#invoice_date').val()});
                form_data.push({name: 'invoice_reference', value: $('#invoice_reference').val()});
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
                document.getElementById("update_pricing_details_form").reset();
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <form id="update_pricing_details_form" name="update_pricing_details_form" method="post">
            <input type="hidden" name="csv_action" id="csv_action" value="" >
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-search"></i>
                        Search Panel
                    </div>
                    <div class="tools">
                        <a href="" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div data-rail-color="blue" data-handle-color="blue" class="filter">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group  has-float-label">
                                    <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                        <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input type="text" class="form-control form-filter" name="date_from" id="date_from" value="" rel="tooltip" data-original-title="From Date">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control form-filter" name="date_to" id="date_to" value="" rel="tooltip" data-original-title="To Date"> 
                                    </div>
                                    <label class="label-account">Select Date Range</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group  has-float-label">
                                <?php
                                $dateTypes = array(
                                    'dateScanned' => "Date Scanned",
                                    'dateCreated' => "Date Created",
                                    'dateLabelCreated' => "Date label Created",
                                    'dateBooked' => "Date Dispatched",
                                    'dateDelivered' => "Date Delivered"
                                );
                                echo Ddl::generateArrayDDL('date_type', $dateTypes, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'date_type');
                                ?><label class="label-account">Date Type </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                               <div class="form-group  has-float-label">
                                    <?php
                                    $consignmentBillingStatus = array(
                                        'all' => "All",
                                        'held' => "Held",
                                        'nonHeld' => "Non Held",
                                    );
                                    echo Ddl::generateArrayDDL('shipment', $consignmentBillingStatus, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'shipment');
                                    ?><label class="label-account">Shipment Billing Status </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                               
                               <div class="form-group  has-float-label">
                                 
        <?php
        $consignmentInvoicesStatus = array(
            'all' => "All",
            'invoiced' => "Invoiced",
            'notinvoiced' => "Not Invoiced",
        );
        echo Ddl::generateArrayDDL('invoice_type', $consignmentInvoicesStatus, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'invoice_type');
        ?>
        <label class="label-account">Shipment Invoice Status </label>
                                </div>
                            </div>

                        </div>
                        <div class="row">
        <div class="col-md-3">
            
            <div class="form-group  has-float-label">
                
                <div id="user_content">
                    <?php
                    $accountParentId = "0";
                    $includeParent = true;
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $accountParentId = $this->user->getUserAccountId();
                    }
                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                        $accountParentId = $this->user->getUserAccountId();
                    }
                    $selectedAccount = "";

                    $allowedLevel = 0;
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $allowedLevel = 1;
                    }
                    echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent, $allowedLevel);
                    ?>
                </div>
                <label class="label-account">Select Account</label>
            </div>
        </div>
                            
        <div class="col-md-3">
           
            <div class="form-group  has-float-label">
                 
                <div id="carriers_div">

                    <?php //echo Ddl::generateCarrierDDLWithImage('carriers','carriers','id', ' class="bs-select input-sm form-control form-filter " data-container="body" required="" data-live-search="true"  data-show-subtext="true"'); ?>                 
                 <?php echo Ddl::generateArrayDDL('carriers', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'carriers'); ?>
                </div>
                <label class="label-account">Select Carrier</label>
            </div>
        </div>
                            <div class="col-md-3">
                                
                                 <div class="form-group  has-float-label">
                                    
                                    <div id="service_div">
                                          <?php echo Ddl::generateArrayDDL('service', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service'); ?>
        <?php //echo Ddl::generateServiceDDLWithImage('service_id', 'service_id', 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services'); ?>
                                    </div>
                                    <label class="label-account">Select Service</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                
                                <div class="form-group  has-float-label">
                                    

                                    <select id="agent" name="agent[]" class="form-filter select2 form-control" title="Select Agent" multiple="">
                                        <option value="">Select Agent</option>
        <?php if (count($this->agentFilterObj) > 0) { ?>
            <?php foreach ($this->agentFilterObj as $obj) { ?>
                                                <option value="<?php echo $obj->getId(); ?>"><?php echo $obj->getAgentName(); ?></option>
                                            <?php } ?>
                                        <?php } ?>


                                    </select>
                                    <label class="label-account">Agent</label>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                
                                <div class="form-group  has-float-label">
                                    
        <?php
        $showShipments = array(
            'all' => "Selected Account & Sub Accounts",
            'own' => "Selected Account",
            'subaccount' => "Sub Accounts",
        );
        echo Ddl::generateArrayDDL('show_shipments', $showShipments, '', '', 'class="form-filter select2 form-control" ', "", 'show_shipments');
        ?>
        <label class="label-account">Show Shipment </label>
                                </div>
                            </div>
                            <div class="col-md-3">

                              
                              
                                <div class="form-group">

  <div class="has-float-label input-icon right">
                                     <i class="fa fa-file"></i>

                              
                                        <input class="form-control form-filter" id="mawb" name="mawb" type="text"  data-original-title="MAWB" placeholder="MAWB">
                                   <label   class="label-account" for="mawb">MAWB</label>

                                  
                                </div>
                            </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group ">
                               <div class="has-float-label input-icon right">
                                     <i class="fa fa-file"></i>
                                        <input class="form-control form-filter" id="manifest" name="manifest" placeholder="Manifest" type="text" value="" rel="tooltip" data-original-title="Manifest">
                                  <label  class="label-account" for="manifest">Manifest</label>

                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                               
                                <div class="form-group has-float-label">
                                    <div id="highlowvalue_div">
        <?php echo Ddl::generateArrayDDL('highlowvalue', array("" => "Please Select Value", "HV" => "High Value", "MV" => "Medium Value", "LV" => "Low Value"), '', '', 'class="form-filter bs-select form-control" ', "", $dd_id = 'highlowvalue'); ?>
         <label class="label-account">Value Type </label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                
                                <div class="form-group tracking_type_field has-float-label">
                                    
                                        <select id="tracking_type" name="tracking_type" class="form-control select2 form-filter" data-original-title="" title="">
                                            <option value="tracking">Tracking Number</option>
                                            <option value="parcel">Parcel Tracking  Number</option>
                                        </select>
                                        <textarea id="number" name="number" placeholder="Tracking Number" rel="tooltip" data-original-title="Tracking" class="form-control form-filter border-top-none" style="resize: none;height: 110px;"></textarea>
                                            
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-file"></i>
                                        <textarea name="bag_number" id="bag_number" type="text" placeholder="Bag Number" rel="tooltip" data-original-title="Bag Number" class="form-control form-filter" style="resize: none; height: 143px;"></textarea>
                                        <label for="bag_number"  class="label-account">Bag Number</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-file"></i>
                                    
                                        <textarea name="hawb" id="hawb" type="text" placeholder="HAWB" rel="tooltip" data-original-title="HAWB" class="form-control form-filter" style="resize: none; height: 143px;"></textarea>
                                   <label for="hawb"  class="label-account">HAWB</label>
                               </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-file-excel-o"></i>
                                    
                                        <textarea name="invoice_number" id="invoice_number" type="text" placeholder="Invoice Number" rel="tooltip" data-original-title="Invoice Number" class="form-control form-filter" style="resize: none; height: 143px;"></textarea>
                                   <label for="invoice_number"  class="label-account">Invoice Number</label>
                               </div>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-cube"></i>
                                   
                                        <input class="form-control form-filter" id="from_weight" name="from_weight" placeholder="From Weight" type="text" value="" rel="tooltip" data-original-title="From Weight">
                                   <label for="from_weight"  class="label-account">From Weight</label>
                               </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">

                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-cube"></i>
                                    
                                        <input class="form-control form-filter" id="to_weight" name="to_weight" placeholder="To Weight" type="text" value="" rel="tooltip" data-original-title="To Weight">
                                        <label for="to_weight"  class="label-account">To Weight</label>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-cube"></i>
                                    
                                        <input class="form-control form-filter" id="pieces" name="pieces" placeholder="Pieces" type="number" value="" rel="tooltip" data-original-title="Number Pieces">
                                        <label for="pieces"  class="label-account">Pieces</label>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                 <div class="form-group has-float-label">
        <?php
        $consignmentServiceType = array(
            'all' => "All",
            'tracked' => "Tracked",
            'untracked' => "Untracked",
        );
        echo Ddl::generateArrayDDL('track', $consignmentServiceType, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'track');
        ?>
                <label class="label-account">Shipment Service Types </label>                
                            </div></div>
                            <div class="col-md-3">
                                
                                 <div class="form-group has-float-label">
        <?php
        $consignmentScannedStatus = array(
            'all' => "All",
            'anywhereScanned' => "Anywhere Scanned",
            'labelCreated' => "Label Created",
        );
        echo Ddl::generateArrayDDL('scanned_type', $consignmentScannedStatus, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'scanned_type');
        ?><label class="label-account">Shipment Scanned </label>
                                </div>
                            </div>


                            <div class="col-md-3">
                                
                                <div class="form-group has-float-label">
        <?php
        $consignmentRemoteAreas = array(
            'all' => "All",
            'remotearea' => "Remote Area",
            'nonRemotearea' => "Non Remote Area",
        );
        echo Ddl::generateArrayDDL('remotearea', $consignmentRemoteAreas, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'remotearea');
        ?><label class="label-account">Remote Area </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                 <div class="form-group has-float-label">
        <?php
        $consignmentReturnCategory = array(
            'all' => "All",
            'return' => "Return",
            'outbound' => "Outbound",
        );
        echo Ddl::generateArrayDDL('return_category', $consignmentReturnCategory, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'return_category');
        ?> <label class="label-account">Return Category </label>
                                </div>
                            </div>
                            

                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-building-o"></i>
                                        <input class="form-control form-filter" name="company" placeholder="Company" id="company" type="text" value="" rel="tooltip" data-original-title="Company" data-placement="bottom">
                                   <label for="company"  class="label-account">Company</label>
                               </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-envelope"></i>
                                  
                                        <input class="form-control form-filter" id="contact" name="contact" placeholder="Contact" type="text" value="" rel="tooltip" data-original-title="Contact" data-placement="bottom">
                                    <label for="contact"  class="label-account">Contact</label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-map-marker"></i>
                                   
                                        <input class="form-control form-filter" id="city" name="city" placeholder="City" type="text" value="" rel="tooltip" data-original-title="City">
                                         <label for="city"  class="label-account">City</label>

                                     </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                <div class="form-group">

                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-qrcode"></i>
                                   
                                        <input class="form-control form-filter" id="postcode" name="postcode" placeholder="Postcode" type="text" value="" rel="tooltip" data-original-title="Postcode">
                                    <label for="postcode"  class="label-account">Post Code</label>

                                </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                <div class="form-group has-float-label">
        <?php echo Ddl::generateCountryDDL('country', '', 'id'); ?>
         <label class="label-account">Country</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-qrcode"></i>
                                        <?php
                                        echo Ddl::generateArrayDDL('status[]', Consignment::$database_status_array, '', '', 'class="form-filter select2 form-control" data-live-search="true"  multiple="multiple" ', "", "status");
                                        ?>
                                        <label  class="label-account">Select Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-qrcode"></i>
                                        <?php
                                        $warehouseListFilter = new WarehouseFilter();
                                        $warehouseList = $warehouseListFilter->getList();
                                        $warehouseListArray = array();
                                        if(count($warehouseList)>0){
                                            foreach($warehouseList as $warehouses){
                                                $warehouseListArray[$warehouses->getId()] =   $warehouses->getWarehouseName();
                                            }
                                        }
                                            
                                        echo Ddl::generateArrayDDL('warehouse[]', $warehouseListArray, '', '', 'class="form-filter select2 form-control" data-live-search="true"  multiple="multiple" ', "", "warehouse");
                                        ?>
                                        <label class="label-account">Select Warehouse</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-float-label">
                                    <?php
                                    $consignmentChargesTypes = array(
                                        'all' => "All",
                                        'zero_basic_charges' => "Zero Basic Charges",
                                        'label_charges' => "Label Charges"
                                    );
                                    echo Ddl::generateArrayDDL('charges', $consignmentChargesTypes, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'charges');
                                    ?> <label class="label-account">Charges </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        
                                            <input id="bill_our_account" name="bill_our_account" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> 
                                            <label for="bill_our_account" class="label-account" >Bill Our Account
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="agent_code_inc" name="agent_code_inc" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-blue" value="1"/> Include Agent Pricing
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="zero_priced" name="zero_priced" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> Zero Price
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="match" name="match" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> Match HAWB/Tracking
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-float-label">
                                    <?php
                                    $consignmentIsCustometChargeable = array(
                                        '1' => "Invoiceable Shipment",
                                        '0' => "Non Invoiceable Shipment",
                                        '' => "All"
                                        
                                    );
                                    echo Ddl::generateArrayDDL('is_customer_billable', $consignmentIsCustometChargeable, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'is_customer_billable');
                                    ?> <label class="label-account">Shipment Billing Type </label>
                                </div>
                            </div>
                
                        </div>
                        <div class="row">                                                             
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="consignemt_collection" name="consignemt_collection" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> Collection
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="export_master" name="export_master" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> Export Master
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="china_dispatch_date" name="china_dispatch_date" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> China Dispatch Date
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 hidden">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="all_billable" name="all_billable" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> All Billable
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="text-align:center;">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary filter-submit" name="btn_go" id="btn_go" value="Search"> Search </button>&nbsp;
                                    <button type="button" class="btn btn-default filter-cancel" name="btn_rest" value="Reset" onclick="resetForm()"> Reset </button>              
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption font-purple-plum">Legend</div>
                    <div class="actions">
        <?php if (Permissions::checkFilePermission('download_csv_with_agent_price')) { ?>
                            <div class="input-group" style="float:left;margin-right: 15px;">
                                <div class="icheck-inline">
                                    <span class="label-account">
                                        <input id="include_agent" name="include_agent" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="1"/> Include Agent
                                    </span>
                                </div>
                            </div>
        <?php } ?>
                        
        <?php if (Permissions::checkFilePermission('assign_price_with_csv_upload')) { ?>
            <a href="assign_price_with_csv_upload.php" class="btn btn-default">
                Assign Price With CSV Upload 
            </a>
        <?php } ?>
        <?php if (Permissions::checkFilePermission('bluk_vol_weight')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#bulk_vol_weight_modal" class="btn btn-default">
                                Bulk Vol Weight
                            </a>
                        <?php } ?>
        <?php if (Permissions::checkFilePermission('zero_price_shipnment_list_manage_page')) { ?>
<!--                            <a href="javascript:;" class="btn btn-default">
                                <span id="calculated_zero_price">0</span> Zero Prices
                            </a>-->
                        <?php } ?>
                        <a href="javascript:;" class="btn btn-default">
                            <span id="calculated_pieces">0</span> Pieces 
                        </a>
                        <a href="javascript:;" class="btn btn-default">
                            <span id="calculated_weight">0</span> Kg(s)Weight
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div>
        <?php if (Permissions::checkFilePermission('legend_for_accounts')) { ?>
                            <div class="row margin-bottom-10">
                                <div class="col-md-3">
                                    <div class="remote-area-shipment-new legends"></div> Remote Area Shipment
                                </div>
                                <div class="col-md-3">
                                    <div class="invoice-ship legends"></div>Invoiced Shipment
                                </div>
                                <div class="col-md-3">
                                    <div class="onhold legends"></div>Billing On Hold
                                </div>
                                <div class="col-md-3">
                                    <div class="ready-to-reinvoice  legends"></div>Ready To Re-invoice
                                </div>
                            </div>
                            <div class="row margin-bottom-10">
                                <div class="col-md-3">
                                    <div class="normal-shipment legends"></div>Normal Shipment
                                </div>
                                <div class="col-md-3">
                                    <div class="credit-shipment legends"></div>Credit Shipment
                                </div>
                                <div class="col-md-3">
                                    <div class="not-scanned-shipment legends"></div>Not Scanned Shipment
                                </div>
                                <div class="col-md-3">
                                    <div class="onholdpink legends"></div>On Hold Shipment
                                </div>
                            </div>
        <?php } ?>
        <?php if (Permissions::checkFilePermission('legend_for_customer_Service')) { ?>
                            <div class="row" id="customer_service_legend">
                                <div class="col-md-3">
                                    <div class="dispatch-not-deliver legends"></div> Dispatched / Not Delivered
                                </div>
                                <div class="col-md-3">
                                    <div class="deliver-shipment legends"></div>Delivered
                                </div>
                                <div class="col-md-3">
                                    <div class="label-created-shipment legends"></div>Label Created 
                                </div>  
                                <div class="col-md-3">
                                    <div class="onhold  legends"></div>Hold
                                </div> 
                                <div class="col-md-3">
                                    <div class="recycled-shipment  legends"></div>Recycled
                                </div>  
                            </div>
        <?php } ?>
                    </div>                 
                </div>
            </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-list"></i>
                        Consignment List
                    </div>
                    <div class="actions" id="bluk_actions" style="display:none;">
            <?php if (Permissions::checkFilePermission('assign_oba_docket_number')) { ?>
                <a href="javascript:;" data-toggle="modal" data-target="#oba_docket_modal" class="btn btn-default">
                    Assign Bags / OBA / Dockets
                </a>
            <?php } ?>

            <?php if (Permissions::checkFilePermission('operation_manifest_shipment_list_manage')) { ?>
                                <a href="javascript:;" data-toggle="modal" data-target="#operation_manifest_model" class="btn btn-default" id="operation_manifest_btn" onclick="get_all_consignment_parcel()" >
                                    Operation Manifest
                                </a>
            <?php } ?>
        <?php if (Permissions::checkFilePermission('generate_invoice_shipment_list_manage')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#generate_invoice_model" class="btn btn-default" id="invoice_generate_btn" style="display:none;">
                                Generate Invoice
                            </a>
                        <?php } ?>
        <?php if (Permissions::checkFilePermission('bulk_price_tariff')) { ?>
                            <a href="javascript:;" class="btn btn-default" id="apply_bluk_price" onclick="apply_bluk_price()" style="display:none;">
                                Tariff Bulk Price All
                            </a>
                        <?php } ?>
        <?php if (Permissions::checkFilePermission('bluk_billing_hold')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#hold_modal" class="btn btn-default">
                                Billing Hold
                            </a>
                        <?php } ?>
        <?php if (Permissions::checkFilePermission('manual_bluk_price_all')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#bulk_price_modal" class="btn btn-default">
                                Manual Bulk Price
                            </a>
                        <?php } ?>
        
        <?php if (Permissions::checkFilePermission('manual_pod_update')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#manual_pod_model" class="btn btn-default">
                                Manual POD Update
                            </a>
                        <?php } ?>
        <?php if (Permissions::checkFilePermission('change_shipment_status')) { ?>
                            <a href="javascript:;" data-toggle="modal" data-target="#change_shipment_status_model" class="btn btn-default">
                                Change Shipment Status
                            </a>
                        <?php } ?>
                    </div>
                    <div class="invoice_action margin-bottom-10">

                    </div>
                </div>
                <div class="portlet-body">   
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <span> </span>
                            <select class="table-group-action-input form-control input-inline input-small input-sm" id="change_consignment" name="change_consignment" style="width:150px !important;">
                                <option value="">Select Action</option>
        <?php if (Permissions::checkFilePermission('billing_hold_selected')) { ?>
                                    <option value="hold">Billing Hold</option>
        <?php } ?>
                                <?php if (Permissions::checkFilePermission('billing_unhold_selected')) { ?>
                                    <option value="unhold">Billing Un-Hold</option>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('close_shipnment')) { ?>
                                    <option value="close_shipment">Close</option>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('tariff_bulk_price_selected')) { ?>
                                    <option value="bulk_price">Tariff Bulk Price</option>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('include_in_scanned')) { ?>
                                    <option value="include_in_scanned">Include in Scanned</option>
                                <?php } ?>
                            </select>
                            <button class="btn btn-sm btn-default table-group-action-submit" type="button" onclick="change_selected_action()">
                                <i class="fa fa-check"></i> Submit</button>
                        </div>
                        <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">                     
                            <thead>
                                <tr role="row" class="heading">
                                    <th class="back-dimgray">
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type='checkbox' name='checkall' class="group-checkable"/>
                                            <span></span>
                                        </label>
                                    </th>
                                    <th class="back-dimgray">Account <br /> MAWB No. <br /> Date Dispatched</th>
                                    <th class="back-dimgray">Status <br /> HAWB Number. <br /> Tracking Number</th>
                                    <th class="back-dimgray">Type <br /> Location</th>
                                    <th class="back-dimgray">Weight <br /> Vol Weight</th>
                                    <th class="back-dimgray">Pieces</th>
                                    <th class="back-dimgray">Value Type</th>
                                    <th class="back-dimgray">Service Code <br /> Service Name</th>
                                    <th class="back-dimgray">Remote Area <br /> On Hold</th>
                                    <th class="back-dimgray">Invoice Number</th>
                                    <th class="back-dimgray">Tariff</th>
                                </tr>                       
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row text-center button-download-records" style="display:none;position: fixed;bottom: 10px;left: 65px;width: 100%;">
                <input type="hidden" value="0" name="total-number-of-items" id="total-number-of-items" />
        <?php
        if (Permissions::checkFilePermission('download_excel_shipment')) {
            echo ' <button type="button" class="btn btn-primary btnExportData" id="btnExportData" name="btnExportData" data-csvtype="DOWNLOAD_CSV" value="Download"> Download </button> ';
        }
        if (Permissions::checkFilePermission('download_gp_report')) {
            echo ' <button type="button" class="btn btn-primary" id="btnGPReportData"> GP Report </button> ';
        }
        if (Permissions::checkFilePermission('download_variance_report')) {
            echo ' <button type="button" class="btn btn-primary" id="btnVarianceReportData"> Variance Report </button> ';
        }
        if (Permissions::checkFilePermission('download_cs_excel_shipment')) {
            echo ' <button type="button" class="btn btn-primary btnExportData" id="btnExportCSData" name="btnExportCSData" data-csvtype="DOWNLOAD_CS_CSV" value="Download"> CS Download </button> ';
        }
        ?>
            </div>

            <div id="bulk_price_modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Pricing Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="res_message"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="zero_pricing" name="zero_pricing" type="checkbox" class="icheck consignment_price_checkbox" data-checkbox="icheckbox_flat-green" value="1"/> Zero Pricing
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="per_shipment" name="per_shipment" type="checkbox" class="icheck consignment_price_checkbox" data-checkbox="icheckbox_flat-green" value="1"/> Per Shipment
                                            </label>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="col-md-3">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="apply_per_kg" name="apply_per_kg" type="checkbox" class="icheck consignment_price_checkbox" data-checkbox="icheckbox_flat-green" value="1"/> Apply Per Kg
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="caption margin-bottom-10 block"> 
                                        <span class="caption-subject bold uppercase">Customer Charges</span> 
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="caption margin-bottom-10 block"> 
                                        <span class="caption-subject bold uppercase">Supplier Charges</span> 
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="caption margin-bottom-10 block"> 
                                        <span class="caption-subject bold uppercase">Purchase Invoice Charges</span> 
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">    
                                    <div class="row">
        <?php foreach ($this->consignmentChargesTypesObj as $obj) { ?>
            <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "customer") { ?>
                                                <div class="col-md-6">
                                                    <label class="label-account"><?php echo $obj->getTitle(); ?></label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control customer_charges" id="customer_<?php echo preg_replace('/\s+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[customer][<?php echo $obj->getId(); ?>]" type="text" value="" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                        </div>
                                                    </div>
                                                </div>
            <?php } ?>
        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="label-account">Extra</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control customer_charges" id="customer_extra" name="consignment_price[customer][extra]" type="text" value="0" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="label-account">Total</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control" id="customer_total" name="consignment_price[customer][total]" type="text" value="0" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <!--<div class="col-md-6">
                                            <label class="label-account">Reference</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control agent_charges" id="agent_reference" name="reference" type="text" value="" rel="tooltip" data-original-title="Reference">
                                                </div>
                                            </div>
                                        </div>-->
        <?php foreach ($this->consignmentChargesTypesObj as $obj) { ?>
            <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "agent") { ?>                                            
                                                <div class="col-md-6">
                                                    <label class="label-account"><?php echo $obj->getTitle(); ?></label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control agent_charges" id="agent_<?php echo preg_replace('/\s+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[agent][<?php echo $obj->getId(); ?>]" type="text" value="" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                        </div>
                                                    </div>
                                                </div>
            <?php } ?>
        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="label-account">Extra</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control agent_charges" id="agent_extra" name="consignment_price[agent][extra]" type="text" value="0" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="label-account">Total</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control" id="agent_total" name="consignment_price[agent][total]" type="text" value="0" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="label-account">Reference</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control purchase_invoice_charges" id="purchase_invoice_reference" name="purchase_invoice_reference" type="text" value="" rel="tooltip" data-original-title="Reference">
                                                </div>
                                            </div>
                                        </div>
        <?php foreach ($this->consignmentChargesTypesObj as $obj) { ?>
            <?php if ($obj->getChargeType() == "both" || $obj->getChargeType() == "agent") { ?>                                            
                                                <div class="col-md-6">
                                                    <label class="label-account"><?php echo $obj->getTitle(); ?></label>
                                                    <div class="form-group1">
                                                        <div>
                                                            <input class="form-control purchase_invoice_charges" id="purchase_invoice_<?php echo preg_replace('/\s+/', '_', strtolower($obj->getTitle())); ?>" name="consignment_price[purchase_invoice][<?php echo $obj->getId(); ?>]" type="text" value="" rel="tooltip" data-original-title="<?php echo $obj->getTitle(); ?>">
                                                        </div>
                                                    </div>
                                                </div>
            <?php } ?>
        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="label-account">Extra</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control purchase_invoice_charges" id="purchase_invoice_extra" name="consignment_price[purchase_invoice][extra]" type="text" value="0" rel="tooltip" data-original-title="Extra" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="label-account">Total</label>
                                            <div class="form-group1">
                                                <div>
                                                    <input class="form-control" id="purchase_invoice_total" name="consignment_price[purchase_invoice][total]" type="text" value="0" rel="tooltip" data-original-title="Total" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
                            <button type="button" class="btn btn-warning extras" data-type="agent">Supplier Extra</button>
                            <button type="button" class="btn btn-info extras" data-type="customer">Customer Extras</button>
                            <button type="button" class="btn btn-primary" onclick="updatePricingDetails();">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>           
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
                                        <label class="control-label font-green-soft" id="label_extra_charges">Supplier</label>
                                        <select name="agent_id" id="agent_id" class="form-control select2">

        <?php
        $agentDataFilter = new AgentDataFilter();
        $agentDataFilter->addFieldFilter('active', '1');
        $agentDataFilter->AddOrderBy('agent_code', 'asc');
        $AgentData = $agentDataFilter->getColumnList('agent_code,agent_name');
        if (count($AgentData) > 0) {
            foreach ($AgentData as $agent) {
                echo '<option value="' . $agent->getId() . '">' . $agent->getAgentCode() . ' [' . $agent->getAgentName() . ']</option>';
            }
        }
        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Charge Type</label>
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
                                        <label class="control-label font-green-soft">Description</label>
                                        <input type="text" name="description" id="description" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Total</label>
                                        <input type="text" name="extra_total" id="extra_total" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">&nbsp;</label><br />
                                        <button type="button" id="btn_add_extra_charge" class="btn btn-success btn-block btn-sm"> Add </button>
                                        <!--<button type="button" id="btn_edit_extra_charge" class="btn btn-success btn-block btn-sm">Edit</button>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 extras_container" id="customer_extras_container">                                    
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customer_extras_body"></tbody>
                                    </table>
                                    <input type="hidden" id="customer_totals_extras" value="0" />
                                </div>
                                <div class="col-md-12 extras_container" id="agent_extras_container">
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agent_extras_body"></tbody>
                                        <input type="hidden" id="agent_totals_extras" value="0" />
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
            <div class="modal fade" tabindex="-1" role="dialog" id="oba_docket_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Assign Bag / OBA / Docket Number</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="res_oba_docket_message"></div>
                            </div>
                            <div class="row">   
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Bag / OBA / Docket Number</label>
                                        <div>
                                            <input id="oba_docket_no" name="oba_docket_no" type="text" class="form-control form-filter" >
                                        </div>
                                    </div>
                                </div> 
                                
                            </div>
          
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="updateObaDocket();">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" tabindex="-1" role="dialog" id="hold_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Update Hold</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="res_billing_hold_message"></div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Select Type</label>
                                        <select name="hold_type" id="hold_type" class="form-control select2">
                                            <option value=""> Please Select</option>
                                            <option value="hold"> hold</option>
                                            <option value="unhold"> Un Hold</option>
                                        </select>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <textarea name="reason" id="reason" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="updateBillingHold();">Update</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" tabindex="-1" role="dialog" id="bulk_vol_weight_modal" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Update Volumetric Weight Details</h4>
                        </div>
                        <div class="modal-body">                            
        <?php
        /* <input type="hidden" name="action" id="action" value="UPDATE_PRICING_DETAILS" /> */
        ?>
                            <div class="row">
                                
                                <div class="col-sm-12">
                                    <div class="alert alert-success" id="update_vol_weight_msg" style="display:none;"></div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <label class="label-account"> Shipment Details</label>
                                            <div class="input-group input-large">
                                                
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="bulk_vol_weight[vol_weight_file]" id="vol_weight_file"> 
                                                </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="modal-footer">
                            <button type="butto
                                    
                                    n" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_vol_weight_btn" onclick="updateVolWeightCSV();">Save changes</button>
                            <a href="../assets/templates/update_vol_weight.csv" class="btn btn-primary" id="template_vol_weight_btn" >Download Template</a>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
            <div class="modal fade" tabindex="-1" role="dialog" id="manual_pod_model">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Manual POD</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="res_manual_pod_message"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label">Select Status</label>
                                    <div class="form-group">
        <?php echo Ddl::generateArrayDDL('pod_reason', Tracking::$oneworld_status_code, '', '', 'class="form-filter select2 form-control" ', "", "pod_reason"); ?>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Select Date</label>
                                        <div class="input-group date form_datetime bs-datetime" data-date-formate="dd-mm-yyyy">
                                            <input type="text" size="16" name="date_delivery" id="date_delivery" class="form-control">
                                            <span class="input-group-addon">
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Track Point</label>
                                        <div>
                                            <input id="track_point" name="track_point" type="text" class="form-control form-filter" >
                                        </div>
                                    </div>
                                </div>  
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <div>
                                            <input id="description" name="description" type="text" class="form-control form-filter" >
                                        </div>
                                    </div>
                                </div>  
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="upload_pod" name="upload_pod" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green"value="YES"/> Upload POD
                                            </label>
                                        </div>
                                    </div>
                                </div>  
                                <div id="pod_box" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <div>
                                                <input id="pod_name" name="pod_name" type="text" class="form-control form-filter" >
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="form-group">
                                                <label> Image </label>
                                                <div class="input-group">
                                                    <div class="form-control uneditable-input" data-trigger="fileinput">
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
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="manual_awb" name="manual_awb" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green"value="YES"/> Enter Tracking Number Manually
                                            </label>
                                        </div>
                                    </div>
                                </div>  
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="send_email_to" name="send_email_to" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green"value="YES"/> Send Email To Customer
                                            </label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea rows="4" cols="50" style="resize:none; display:none;" name="trackingNumber" id="trackingNumber"  value="" placeholder="Tracking Numbers" class="form-control"></textarea>
                                    </div>
                                </div>    

                                <div class="col-md-12 email_content_pod_container" style="display:none;" >
                                    <div class="form-group">
                                        <label class="control-label" for="not_scanned">Please enter email content</label>
                                        <textarea rows="4" cols="50" style="resize:none;" name="email_content_pod" id = "email_content_pod"  value= "" placeholder="Please enter email content" class="form-control"></textarea>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="updateManaualPod();">Update</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="re_generate_model" class="modal fade">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" id="model-regenerate-header"></div>
                        <div class="modal-body" id="modal-regenerate"> </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="save_re_generate_btn" onclick="select_dynamic_action()" class="btn btn-primary" >Save changes</button>
                            <input type="hidden" value="" name="action_form" id="action_form"  />
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
            <!-- /.modal -->         

            <div class="modal fade" tabindex="-1" role="dialog" id="change_shipment_status_model" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Change Shipment Status</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="status_div" class="form-group">
                                        <label class="control-label">Shipment Status</label>
        <?php echo Ddl::generateArrayDDL('shipnment_status', Consignment::$database_status_array, '', '', 'class="form-filter select2 form-control" ', "", "shipnment_status"); ?>
                                    </div>
                                </div>
                            </div>
                            <!--                            <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="input-group">
                                                                    <div class="icheck-inline">
                                                                        <label class="label-account">
                                                                            <input id="manual_awb_for_change_status" name="manual_awb_for_change_status" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-green" value="YES" /> Enter Tracking Number Manually
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>  
                                                        </div>
                                                        <div class="row" id="tracking_number_for_status_box">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="control-label" for="tracking_number_for_status">Please enter Tracking Number</label>
                                                                    <textarea rows="4" cols="50" style="resize:none;" name="tracking_number_for_status" id = "tracking_number_for_status"  value= "" class="form-control"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>-->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="shipment_status_comment">Comments</label>
                                        <textarea rows="4" cols="50" style="resize:none;" name="shipment_status_comment" id = "shipment_status_comment"  value= "" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn green" type="button" id="update_shipnment_status_btn">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" tabindex="-1" role="dialog" id="operation_manifest_model" >
                <div class="modal-dialog modal-full">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Operation Manifest</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="portlet light">
                                        <div class="portlet-title">
                                            <div class="caption"> <i class="fa fa-list"></i>
                                                Parcel List
                                            </div>
                                            <div class="actions" id="bluk_parcel_actions">
                                                <a href="javascript:;" class="btn btn-default" id="create_manifest_all_btn" onclick="create_manifest_all()" >
                                                    Create Manifest All
                                                </a>
                                            </div>
                                            <div class="invoice_action margin-bottom-10">

                                            </div>
                                        </div>
                                        <div class="portlet-body">   
                                            <div class="table-container">
                                                <div class="table-actions-wrapper">
                                                    <span> </span>
                                                    <select class="table-group-action-input form-control input-inline input-small input-sm" id="change_parcel" name="change_parcel" style="width:150px !important;">
                                                        <option value="">Select Action</option>
                                                        <option value="create_manifest">Create Manifest</option>
                                                        <option value="remove_manifest">Remove Manifest</option>
                                                    </select>
                                                    <button class="btn btn-sm btn-default table-group-action-submit" type="button" onclick="change_parcel_selected_action()">
                                                        <i class="fa fa-check"></i> Submit</button>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-parcel-data-table">                     
                                                    <thead>
                                                        <tr role="row" class="heading">
                                                            <th>
                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                    <input type='checkbox' name='checkall' class="group-checkable"/>
                                                                    <span></span>
                                                                </label>
                                                            </th>
                                                            <th>Tracking Number</th>
                                                            <th>Service</th>
                                                            <th>Dims</th>
                                                            <th>Weight</th>
                                                            <th>Status</th>
                                                            <th>Already Manifest</th>
                                                            <th>View Manifest</th>
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
                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

            <div id="hidden_frm" style="display: none;">
                <input type="hidden" class="form-filter" name="search_result" id="search_result" value="<?php echo $this->search_result; ?>" >
            </div>
        </form>
        <form id="update_booking_date_frm" name="update_booking_date_frm" method="post"> 
            <div class="modal fade" tabindex="-1" role="dialog" id="update_not_scanned_shipments" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Update Dispatched Date</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="booking_date_msg">Updated successfully</div>                            
                            <input type="hidden" name="action" id="action" value="UPDATE_NOT_SCANNED_SHIPMENTS" />
                            <input type="hidden" name="consignment_ids" id="consignment_ids" value="" />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        <input type="text" class="form-control input-sm" readonly name="booked_date" id="booked_date" placeholder="" value="" rel="tooltip" data-original-title="Booked Date" readonly="readonly" >                                  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn green" type="button" id="update_booking_date_btn">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form id="a-d-csv-modal-frm" name="a_d_csv_modal_frm" method="post"> 
            <div class="modal fade" tabindex="-1" role="dialog" id="a-d-csv-modal" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Download Data in CSV</h4>
                        </div>
                        <div class="modal-body">
                                    <div id="check_columns_screen">
                                        <div class="row">
                                            
                                                    <div class="col-md-12">	
                                                        <div id="check_columns_html"></div>
                                                       
                                                    </div>
                                                </div>
                                    </div>
                                    <div id="download_screen">
                                        <div class="alert alert-success" id="a-d-csv-modal-msg">Please wait, we are dealing you request</div>
                                        <div class="row">
                                            <div class="col-md-12">	  
                                                <div class="a-d-csv-clock" style="margin:2em;"></div>
                                                <p>Total Number of Consignment to be downloadable <span id="a-d-csv-totalInvoiceConsignment"></span></p>
                                                <div style="text-align:center; padding-left:30px" id="a-d-csv-account-loading">
                                                    <div class="a-d-csv-message">Please wait...</div>
                                                </div>
                                                <div style="clear:both;"></div>
                                                <div style="color:#000; background:#6F3; display:none; padding:10px; text-align:center;" id="a-d-csv-error-information"></div>
                                                <div style="clear:both;"></div>
                                                <div class="a-d-csv-progress">
                                                    <div class="progress-bar" id="a-d-csv-progress-bar-new" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                                        <span id="a-d-csv-merging-status">Data download in processing... (<span id="a-d-csv-progress-bar-completion">0%</span>)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="backColumnBtn">Back</button>
                            <button type="button" class="btn btn-primary btnExportDataSubmit float-right" id="btnExportDataSubmit" name="btnExportDataSubmit" data-csvtype="DOWNLOAD_CSV" value="Download"> Download CSV</button>
                            <button type="button" class="btn btn-default a-d-csv-modal-close" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
            
            <div class="modal fade" id="download_excel_comparison_report" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title bold" id="excel_report_title">Downloading Excel GP Report</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12"><p>Total number of consignment data to be downloadable/included <span id="excel-totalConsignment"></span></p><p id="cr_report_text"></p></div>
                            <div class="col-md-12" id="response_cr_report_response">
                              </div>
                            
                            <div class="col-md-12" id="response_cr_report_progress">                                
                                     <div class="cr-report-progress">
                                        <div class="progress-bar" id="cr-report-progress-bar-new" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                            <span id="cr-report-progress-merging-status">Data download in processing... (<span id="cr-report-progress-bar-completion">0%</span>)</span>
                                        </div>
                                    </div>
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
        
        <div class="modal fade" id="tariff_price_detail_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Consignment Tariff Charges Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Customer Cost</th>
                                        <th>Agent Cost</th>
                                        <th>Purchase Cost</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody id="tariff_detail_html">

                                </tbody>
                            </table>
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

        <div class="modal fade" tabindex="-1" role="dialog" id="account_details" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Customer Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <tbody id="account_detail_html">

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

        <div class="modal fade" tabindex="-1" role="dialog" id="generate_invoice_model" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Select Invoice Date</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    
                                    <input type="text" class="form-control input-sm" readonly name="invoice_date" id="invoice_date" placeholder="" value="<?php echo formatDate(date('Y-m-d')); ?>" rel="tooltip" data-original-title="Invoice Date" readonly="readonly" >
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                     <i class="fa fa-cube"></i>
                                        <input class="form-control form-filter" id="invoice_reference" name="invoice_reference" placeholder="Invoice Reference" type="text" value="" rel="tooltip" data-original-title="Invoice Reference">
                                   <label class="label-account">Invoice Reference</label>
                               </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="check_box_date" name="check_box_date" type="checkbox" class="form-filter icheck" checked="checked" data-checkbox="icheckbox_flat-green" value="1"/> Date
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="check_box_reference" name="check_box_reference" type="checkbox" class="form-filter icheck" checked="checked" data-checkbox="icheckbox_flat-green" value="1"/> Reference
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="check_box_destination" name="check_box_destination" type="checkbox" class="form-filter icheck" checked="checked" data-checkbox="icheckbox_flat-green" value="1"/> Destination
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="check_box_service" name="check_box_service" type="checkbox" class="form-filter icheck" checked="checked" data-checkbox="icheckbox_flat-green" value="1"/> Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input id="check_box_bank_details" name="check_box_bank_details" type="checkbox" class="form-filter icheck" checked="checked" data-checkbox="icheckbox_flat-green" value="1"/> Bank Details
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn green" type="button" onclick="generate_invoice()" id="generate_invoice_btn">Generate Invoice</button>
                    </div>
                </div>
            </div>
        </div>
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
            #tracking_number_for_status_box{
                display: none;
            }
            .table>thead>tr>th {
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
            table.dataTable td.sorting_1, table.dataTable td.sorting_2, table.dataTable td.sorting_3, table.dataTable th.sorting_1, table.dataTable th.sorting_2, table.dataTable th.sorting_3{
                background: none !important;
            }
            .table-hover>tbody>tr:hover, .table-hover>tbody>tr:hover>td{
                color: #000;
            }
            .table-hover>tbody>tr:hover, .table-hover>tbody>tr:hover>td a {
                color: #000;
            }
            .line-height-2{
                line-height: 2;
            }
            .input-daterange input{
                text-align: left;
            }
            @media only screen and (min-width:983px){
                .margin-top-md-30 {
                    margin-top: 30px;
                }
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
