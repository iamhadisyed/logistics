<?php
//
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'agentdata.class',
    'agentdatafilter.class',
    'country.class',
    'countryfilter.class',
    'pallet.class',
    'palletfilter.class',
    'palletcariergroup.class',
    'palletcariergroupfilter.class',
    'palletbagremovereason.class',
    'palletbagremovereasonfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'agentData.class',
    'agentDatafilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'carrierhubs.class',
    'carrierhubsfilter.class',
    'mawbfilter.class',
    'mawb.class',
    'addressfilter.class',
    'address.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'manifestentitymappingfilter.class',
    'manifestentitymapping.class',
    'bagscanlog.class',
    'bagscanlogfilter.class',
    'prealert.class',
    'palletbagremovereason.class',
    'palletentitymapping.class',
    'manifestservicemapping.class',
    'manifestservicemappingfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'bagging.class',
    'baggingfilter.class',
   
    
    #
    
    
    
    
    /*
    
    
    'addressfilter.class',
    'address.class',
    
    
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',*/
    ]);


class Page extends BasePage {
    public $userObj = null;
    public $userAccountObj = "";
    private function updateDataIntoMobileApplication($url, $postfields) {

        $ch = curl_init();
        $server_output = '';
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }
    private function AddTracking($trackingNumbers) {
        $user = SessionManager::getUser();
        TrackingData::AddVirtualTrackingToScanParcels($trackingNumbers, $user);
    }
/*
 * Scenario 101
 * Get: Tracking Data array
 * Return: success message in json array
 */

function saveDataToTrackingData($trackingArr, $showMsg = TRUE) {
    $trackingDataFilter = new TrackingDataFilter();
    $trackingDataFilter->bulkDataInsert($trackingArr);
    if ($showMsg) {
        $msg = "Tracking data inserted successfully";
        $arr = array('result' => 'success', 'message' => $msg);
        echo json_encode($arr);
    }
}
    protected function init() {
        $user = SessionManager::getUser();
        $this->userObj = $user;
        $this->userAccountObj = new CustomerAccount($user->getUserAccountId());
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Scanning'
        );
        if (isset($_GET['action']) && $_GET['action'] == "pallet_list") {
            $user = SessionManager::getUser();
            $userIdArr = [];
            $userAccountId = $user->getUserAccountId();
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("user_account_id", $userAccountId);
            $userObj = $userFilter->getColumnList("id");
            if(count($userObj) > 0){
                foreach ($userObj as $user) {
                    $userIdArr[] = $user->getId();
                }
            }
            $palletFilter = new PalletFilter();
            $palletFilter->addFilterIn("userid", $userIdArr);
            $palletFilter->addDateDispatchIsNull();
            $palletFilter->addDispatchUserIdIsNull();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $palletNumber = $this->form_vars['pallet_number'];
                if (!empty($palletNumber))
                    $palletFilter->addFieldFilter('   p.palletno', $palletNumber);

                $palletCarrierGroupFilter = $this->form_vars['pallet_carrier_group_filter'];
                if (!empty($palletCarrierGroupFilter))
                    $palletFilter->addFieldFilter('   p.pallet_carrier_id', $palletCarrierGroupFilter);

                $carrierHubFilter = $this->form_vars['carrier_hub_filter'];
                if (!empty($carrierHubFilter))
                    $palletFilter->addFieldFilter('   p.hub', $carrierHubFilter);

                $sourceCountry = $this->form_vars['source_country'];
                if (!empty($sourceCountry))
                    $palletFilter->addFieldFilter('   p.pallet_source_country_id', $sourceCountry);

                $sourceWarehouse = $this->form_vars['source_warehouse'];
                if (!empty($sourceWarehouse))
                    $palletFilter->addFieldFilter('   p.pallet_source_warehouse_id', $sourceWarehouse);

                $destinationCountry = $this->form_vars['destination_country'];
                if (!empty($destinationCountry))
                    $palletFilter->addFieldFilter('   p.pallet_destination_country_id', $destinationCountry);

                $destinationWarehouse = $this->form_vars['destination_warehouse'];
                if (!empty($destinationWarehouse))
                    $palletFilter->addFieldFilter('   p.pallet_destination_warehouse_id', $destinationWarehouse);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $palletFilter->addDateCreatedFilter($date_created_from, $date_created_to);

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
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $palletFilter->AddOrderBy(strtolower("p." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $palletFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $palletFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $palletFilter->setOffset($iDisplayStart);
            $palletFilterObjs = $palletFilter->getPagingList();
            $setDataArr = array();

            foreach ($palletFilterObjs as $palletFilterObj) {
                $currentArr = array();
                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                $carrierHub = new CarrierHubs($palletFilterObj->getHub());
                $sCountry = new Country($palletFilterObj->getPalletSourceCountryId());
                $sWarehouse = new Warehouse($palletFilterObj->getPalletSourceWarehouseId());
                $dCountry = new Country($palletFilterObj->getPalletDestinationCountryId());
                $dWarehouse = new Warehouse($palletFilterObj->getPalletDestinationWarehouseId());
                $currentArr['date_created'] = formatDate(date("Y-m-d",$palletFilterObj->getDateCreated()));
                $currentArr['palletno'] = $palletFilterObj->getPalletno();
                $currentArr['pallet_carrier_id'] = $palletCarrierGroup->getGroupName();
                $currentArr['hub'] = $carrierHub->getHub();
                $currentArr['pallet_source_country_id'] = $sCountry->getName();
                $currentArr['pallet_source_warehouse_id'] = $sWarehouse->getWarehouseName();
                $currentArr['pallet_destination_country_id'] = $dCountry->getName();
                $currentArr['pallet_destination_warehouse_id'] = $dWarehouse->getWarehouseName();
                $currentArr['actions'] = '<button id="pallet_select_btn" data-mawb="'.$palletFilterObj->getPalletno().'" onclick="select_pallet('."'".$palletFilterObj->getPalletno()."','".$palletFilterObj->getPalletCarrierId()."','".$palletFilterObj->getHub()."'".')" type="button" class="btn green btn-xs">Select</button>';
//                $currentArr['actions'] = "<a href='JavaScript:Void(0);' data-group_id='" . $palletFilterObj->getId() . "' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";
//                        "<a href='services_view.php?id=" . $palletFilterObj->getId() . "' class='btn btn-xs blue btn-outline'><span class='fa fa-eye'></span> </a>"
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "mawb_list") {
            $mawbObj = new MawbFilter();
            
             
            $userIdArr = [];
            $userAccountId = $this->userObj->getUserAccountId();
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter("user_account_id", $userAccountId);
            $userObj = $userFilter->getColumnList("id");
            if(count($userObj) > 0){
                foreach ($userObj as $user) {
                    $userIdArr[] = $user->getId();
                }
            }
            $mawbObj->addFilterIn("    m.added_by", $userIdArr);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $master_number = $this->form_vars['master_number'];
                if (!empty($master_number))
                    $mawbObj->addFieldFilter('   m.mawb_number', $master_number);
                
                $source_country = $this->form_vars['mawb_source_country'];
                if (!empty($source_country))
                    $mawbObj->addFieldFilter('   m.`mawb_source_country_id`', $source_country);
                
                $destination_country = $this->form_vars['mawb_destination_country'];
                if (!empty($destination_country))
                    $mawbObj->addFieldFilter('   m.`mawb_destination_country_id`', $destination_country);
                
                $source_warehouse = $this->form_vars['source_mawb_warehouse'];
                if (!empty($source_warehouse))
                    $mawbObj->addFieldFilter('   m.`mawb_source_warehouse_id`', $source_warehouse);
                
                $destination_warehouse = $this->form_vars['destination_mawb_warehouse'];
                if (!empty($destination_warehouse))
                    $mawbObj->addFieldFilter('   m.`mawb_destination_warehouse_id`', $destination_warehouse);
                
                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $mawbObj->addDateCreatedFilter($date_created_from, $date_created_to);

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
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                                
                if($dataTableColumnName == "mawb"){
                    $dataTableColumnName = "mawb_number";
                }
                if($dataTableColumnName == "source_country"){
                    $dataTableColumnName = "mawb_source_country_id";
                }
                if($dataTableColumnName == "source_warehouse"){
                    $dataTableColumnName = "mawb_source_warehouse_id";
                }
                if($dataTableColumnName == "destination_country"){
                    $dataTableColumnName = "mawb_destination_country_id";
                }
                if($dataTableColumnName == "destination_warehouse"){
                    $dataTableColumnName = "mawb_destination_warehouse_id";
                }
                $mawbObj->AddOrderBy(strtolower("m." . $dataTableColumnName), $orderFalse);
            }
            
            /*
             * Pagination Logic Implemented
             * 
             */
            $countTotal = $mawbObj->getMawbTotalCount();
            $iTotalRecords = $countTotal[0]->getId();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $mawbObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $mawbObj->setOffset($iDisplayStart);
            $mawbData = $mawbObj->getListMawb();
            $setDataArr = array();

            foreach ($mawbData as $data) {
                $currentArr = array();
                $currentArr['added_date'] = formatDate(date("Y-m-d", strtotime($data->getAddedDate())));
                $currentArr['mawb'] = $data->getMawbNumber();
                $currentArr['source_country'] =  $data->getSourceCountry();
                $currentArr['source_warehouse'] =  $data->getSourceWarehouse();
                $currentArr['destination_country'] =  $data->getDestinationCountry();
                $currentArr['destination_warehouse'] =  $data->getDestinationWarehouse();
                $currentArr['actions'] = '<button onclick="select_mawb('."'".$data->getMawbNumber()."'".')" type="button" class="btn green btn-xs">Select</button>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == "saveRemovePalletBag") {
            $palletId = $this->form_vars['bag_remove_pallet_id'];
            $bagId = $this->form_vars['bag_remove_bag_id'];
            $entityId = $this->form_vars['bag_remove_entity_id'];
            $reason = $this->form_vars['pallet_bag_remove_reason_txt'];
            $palletBagRemoveReason = new PalletBagRemoveReason();
            $palletBagRemoveReason->setPalletId($palletId);
            $palletBagRemoveReason->setBagId($bagId);
            $palletBagRemoveReason->setReason($reason);
            $palletBagRemoveReason->save();
//              Remove from pallet_entity_mapping table then add new record
            $palletEntityMapping = new PalletEntityMapping();
            $palletEntityMapping->DeleteByIdFromPallet($entityId);
            $message = "Bag removed successfully";
            $arr = array('result' => 'success', 'message' => $message);
            echo json_encode($arr);
            die;
        }
        if (isset($this->form_vars['flight_number']) && !empty($this->form_vars['mawb_new'])) {
            $user = SessionManager::getUser();
            $countryId = $user->getCountryId();
            $mawbNew = $this->form_vars['mawb_new'];
            $mawbId = getMawbIdFromMawbNumber($mawbNew);
            if ($mawbId == "") {
                if (strlen($mawbNew) > 12 || strpos($mawbNew, "-") != 3) {
                    echo "Please enter valid format of MAWB";
                    die;
                } else {
                    $mawbObj = new Mawb();
                    $mawbObj->setMawbSourceCountryId($countryId);
                    $mawbObj->setMawbDestinationCountryId($countryId);
                    $mawbObj->setMawbNumber($mawbNew);
                    $mawbObj->setIsActive('y');
                    $mawbObj->setAddedDate(date("Y-m-d H:i:s", time()));
                    $mawbObj->setUpdatedDate(date("Y-m-d H:i:s", time()));
                    $mawbObj->setAddedBy($user->getId());
                    $mawbObj->save();
                    $mawbId = $mawbObj->getId();
                }
            }
            $flightNumber = $this->form_vars['flight_number'];
            $numberOfPieces = $this->form_vars['number_of_pieces'];
            $weightMawb = $this->form_vars['weight_mawb'];
            $statusMawb = $this->form_vars['status_mawb'];
            $preAlert = new PreAlert();
            $preAlert->setMawbId($mawbId);
            $preAlert->setFlightNumber($flightNumber);
            $preAlert->setPieces($numberOfPieces);
            $preAlert->setWeight($weightMawb);
            $preAlert->setStatus($statusMawb);
            $preAlert->setDateTime(date("Y/m/d H:i"));
            $preAlert->setDateEntry(date("Y-m-d G:i:s"));
            $preAlert->setCreatedBy($user->getId());
            $preAlert->save();
            echo "success";
            die;
        }
        //Save New consignment
        if (isset($this->form_vars['dispatch_form_action']) && $this->form_vars['dispatch_form_action'] == 'save_dispatch_con') {
            $user = SessionManager::getUser();
            $licenceplate_array = [];
//            if ($user->getId() <= 0) {
//                $msg = Translation::GetCaption("SESSION_EXPIRED");
//                $arr = array('status' => 'error', 'message' => $msg);
//                echo json_encode($arr);
//                return;
//                die;
//            }
            $manifestId = (int) trim($this->form_vars['manifest_id']);
            $manifestObj = new Manifest($manifestId);
            $serviceId = $manifestObj->getServiceId();
            $manifestObj->setIsDispatched("Y");
            $manifestObj->save();
            //Track Point Logic
            $countryId = $user->getCountryId();
            $country = new Country($countryId);
            $trackpoint = '';
            $countryIso3 = '';
            $warehouseName = '';
            $carrierDesc = '';
            $trackingDataNew = [];
            $conList = [];
            if (count($country) > 0)
                $countryIso3 = $country->getIso3();

            $warehouseid = $user->getWarehouseId();
            if ($warehouseid > 0) {
                $warehouseObj = new Warehouse($warehouseid);
                $warehouseName = $warehouseObj->getWarehouseName();
            }
            $trackpoint = $warehouseName." - ".$countryIso3;
            if(!empty($warehouseName))
                $carrierDesc = 'Departed Facility in '.$trackpoint;

            //IF AGENT IS NOT SELECTED THEN DON'T SAVE CONSIGNMENT (HADI NEW)
            if(!empty($this->form_vars['con_dispatch_agent']) && trim($this->form_vars['con_dispatch_agent']) !="Select Agent"){
                $consignment = new Consignment();
                $consignment->setUserId($user->getId());
                $consignment->setServiceId($this->form_vars['con_service']);
                $consignment->setAgentId($this->form_vars['con_dispatch_agent']);
                $consignment->setWeight($this->form_vars['con_weight']);
                $consignment->setNumberPieces($this->form_vars['con_no_item']);
                $consignment->setValue($this->form_vars['con_value']);
                $consignment->setCurrency($this->form_vars['con_currency']);
                $consignment->setMawb($this->form_vars['con_master_no']);
                $consignment->setaddressLine1($this->form_vars['con_address_1']);
                $consignment->setaddressLine2($this->form_vars['con_address_2']);
                $consignment->setaddressLine3($this->form_vars['con_address_3']);
                $consignment->setCity($this->form_vars['con_city']);
                $consignment->setPostcode($this->form_vars['con_postcode']);
                $consignment->setCountryId($this->form_vars['country_id']);
                $consignment->setShipmentType("D");
                $consignment->setCompany("Smart Track");
                $consignment->setContact("Smart Track");
                $consignment->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);//4 digit code
                $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);//String
                $consignment->setDateCreated(date("Y-m-d H:i:s"));
                $consignment->setWarehouseId(Sessionmanager::getUser()->getWarehouseId());
                $consignment->setSenderChecked('0');
                $consignment->setInvoiceType('INV');
                // Save loggedin User Data
                if(!empty($this->userAccountObj->getCompany()))
                    $consignment->setSenderCompany($this->userAccountObj->getCompany());
                
                if(!empty($this->userObj->getEmail()))
                    $consignment->setSenderEmail($this->userObj->getEmail());
                
                if(!empty($this->userObj->getPhone()))
                    $consignment->setSenderTelephone($this->userObj->getPhone());
                
                if(!empty($this->userObj->getAddress()))
                    $consignment->setSenderAddressLine1($this->userObj->getAddress());
                
                if(!empty($this->userObj->getAddress2()))
                    $consignment->setSenderAddressLine2($this->userObj->getAddress2());
                
                if(!empty($this->userObj->getAddress3()))
                    $consignment->setSenderAddressLine3($this->userObj->getAddress3());
                
                if(!empty($this->userObj->getCity()))
                    $consignment->setSenderCity($this->userObj->getCity());
                
                if(!empty($this->userObj->getPostcode()))
                    $consignment->setSenderPostcode($this->userObj->getPostcode());
                
                if(!empty($this->userObj->getCountryId()))
                    $consignment->setSenderCountryId($this->userObj->getCountryId());
                
                if(!empty($this->userObj->getState()))
                    $consignment->setSenderState($this->userObj->getState());
                
                $consignment->save();
                $newConId = $consignment->getId();
                //Calculate weight for items
                $itesmWeight = $this->form_vars['con_weight']/$this->form_vars['con_no_item'];
                if($itesmWeight < 0){
                    $itesmWeight = 0;
                }
                $conLabel = Consignment::getInstantLabel($consignment, 'pdf','100x150',true, true);
               
                //Get parcels from DB and update the weight and add data to tracking
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("consignment_id",$newConId);
                $manifestParcelObj = $parcelFilter->getColumnList("id");
                if(count($manifestParcelObj) > 0){
                    // Save MAWB first
                    $mawbNumber = trim($this->form_vars['con_master_no']);
                    if(!empty($mawbNumber)){
                        $mawbId = getMawbIdFromMawbNumber($mawbNumber);
                        if($mawbId == ""){
                            /*if(strlen($mawbNumber) > 12 || strpos($mawbNumber, "-") != 3){
                                $arr = array('status' => 'error','mawb' => 'error', 'message' => 'MAWB format is not supported');
                                echo json_encode($arr);
                                die;
                            }else{*/
                                $mawbObj = new Mawb();
                                $mawbObj->setMawbSourceCountryId($countryId);
                                $mawbObj->setMawbDestinationCountryId($countryId);
                                $mawbObj->setMawbNumber($mawbNumber);
                                $mawbObj->setIsActive('y');
                                $mawbObj->setAddedDate(date("Y-m-d H:i:s",time()));
                                $mawbObj->setUpdatedDate(date("Y-m-d H:i:s",time()));
                                $mawbObj->setAddedBy($user->getId());
                                $mawbObj->save();
                                $mawbId = $mawbObj->getId();
                            //}
                        }
                    }
                    foreach ($manifestParcelObj as $manifestParcel) {
                        $parcelId = 0;
                        $parcelId = $manifestParcel->getId();
                        $parcel = new Parcel($parcelId);
                        $parcel->setOweStatusCode("label created");
                        $parcel->setWeight($itesmWeight);
                        $parcel->setDescription("In Transit");
                        $parcel->setQty(1);
                        $parcel->save();
                        
                        //Enter tracking data when we have created new parcels
                        $trackingDataArr = [];
                        $trackingDataArr['entity_id'] = $parcelId;
                        $trackingDataArr['entity_type'] = 'parcel';
                        $trackingDataArr['tracking_number'] = $parcel->getTrackingNumber();
                        $trackingDataArr['user_id'] = $user->getId();
                        $trackingDataArr['track_point'] = $trackpoint;
                        $trackingDataArr['date_created'] = date('Y-m-d H:i:s');
                        $trackingDataArr['ip_address'] = getClientIp();
                        $trackingDataArr['status_code_id'] = 144;
                        $trackingDataArr['warehouse_id'] = $user->getWarehouseId();
                        $trackingDataArr['carrier_desc'] = $carrierDesc;
                        //add data to tracking_data table
                        $trackingDataObj = new TrackingData($trackingDataArr);
                        $trackingDataObj->save();
                        changeConStatusByParcelStatus($parcelId);
                        if (!empty($mawbId)) {
                            //Check if any mabw is already added to same bag
                            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                            $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                            $mawbParcelMappingFilter->addFieldFilter("    parcel_id", $parcelId);
                            $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList('id');
                            if(count($mawbParcelMappingObj) == 0){
                                //Add data to mawb_parcel_mapping table
                                $mawbParcelMapping = new MawbParcelMapping();
                                $mawbParcelMapping->setMawbId($mawbId);
                                $mawbParcelMapping->setParcelId($parcelId);
                                $mawbParcelMapping->setWharehouseId($warehouseid);
                                $mawbParcelMapping->setDateAdded(time());
                                $mawbParcelMapping->setAddedBy($user->getId());
                                $mawbParcelMapping->save();
                            }
                        }
                    }
                }
//                $consignment->setLabelFile($conLabel);
                $consignment->save();
                if($conLabel['STATUS'] == "ERROR"){
                    $outputArray["status"] = "error";
                    $outputArray["label"] = "";
                    $outputArray["message"] = "Consignment created successfully.".$conLabel['MESSAGE'];
                }else{
                    $outputArray["status"] = "success";
                    $outputArray["label"] = $conLabel['LABEL'];
                    $outputArray["message"] = "Consignment created successfully";
                }
            }
            //Change old consignment status and add data to Tarcking Table
            //Get parcel list by manifest id from manifest_entity_mapping
            $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
            $manifestEntityMappingFilter->addFieldFilter("    manifest_id", $manifestId);
            $manifestEntityMappingFilter->addFieldFilter("    manifest_entity_type", "p");
            $manifestParcelObjArr = $manifestEntityMappingFilter->getColumnList("entity_id");
            if(count($manifestParcelObjArr) > 0){
                $trackingNumbers = [];
                foreach ($manifestParcelObjArr as $manifestParcelAr) {
                    $ParcelIdNew = $manifestParcelAr->getEntityId();
                    $parcelNew = new Parcel($ParcelIdNew);
                    //Send data variables
                    $con = new Consignment($parcelNew->getConsignmentId());
                    $conList[] = $con;
                    
                    $booked_date = time();
                    Consignment::runQuery("UPDATE consignment SET date_booked = '" . $booked_date . "' WHERE (date_booked IS NULL OR date_booked = '0') AND id = " . $parcelNew->getConsignmentId() . " ");
                    $trackingNumbers[] = $parcelNew->getTrackingNumber();
                    $licenceplate_array[$parcelNew->getConsignmentId()]['licence_plate'][] = $parcelNew->getTrackingNumber();
                    $licenceplate_array[$parcelNew->getConsignmentId()]['vol_weight'][] = $parcelNew->getWeight();
                    //End Send data variables
                    $parcelNew->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_DISPATCHED]);
                    $parcelNew->setParcelStatusCode(Consignment::STATUS_DISPATCHED);
                    $parcelNew->save();
                    $trackingDataArr = [];
                    $trackingDataArr['entity_id'] = $parcelNew->getId();
                    $trackingDataArr['entity_type'] = 'parcel';
                    $trackingDataArr['tracking_number'] = $parcelNew->getTrackingNumber();
                    $trackingDataArr['user_id'] = $user->getId();
                    $trackingDataArr['track_point'] = $trackpoint;
                    $trackingDataArr['date_created'] = date('Y-m-d H:i:s');
                    $trackingDataArr['ip_address'] = getClientIp();
                    $trackingDataArr['status_code_id'] = 126;
                    $trackingDataArr['warehouse_id'] = $user->getWarehouseId();
                    $trackingDataArr['carrier_desc'] = $carrierDesc;
                    //add data to tracking_data table
                    $trackingDataObj = new TrackingData($trackingDataArr);
                    $trackingDataObj->save();
                    changeConStatusByParcelStatus($ParcelIdNew);
                }
            }
                $className = '';
//                $serivceAgentMapping = new ServiceAgentMappingDataFilter();
//                $serivceAgentMapping->addFilter(" serviceid = '" . $consignment->getServiceId() . "' and agentid = '" . $consignment->getAgentId() . "'");
//                $serviceAgentMappintRecordSet = $serivceAgentMapping->getList();
//                if (count($serviceAgentMappintRecordSet) > 0) {
//                    foreach ($serviceAgentMappintRecordSet as $agentData) {
//                        $className = $agentData->getClassFileName();
//                    }
//                }
//                if (trim($className) == '') {
//                    $serivces = new Services($consignment->getServiceId());
//                    $className = trim($serivces->getLabelClassName());
//                }
//                $file = strtolower("../includes/labels/$className.class.php");
//                if (file_exists($file)){
//                    require_once $file;
//                }else{
//                    $results['STATUS'] = 'ERROR';
//                    $results['MESSAGE'] = "Service provider[ ".$className." ] is not defined. Please contact system administrator";
//                    return $results;
//                    die;
//                }
                $className = Consignment::IncludeCarrierClass($consignment);
                if($className === false){
                    $results['STATUS'] = 'ERROR';
                    $results['MESSAGE'] = "Service provider[ ".$className." ] is not defined. Please contact system administrator";
                    return $results;
                    die;
                }
                $sendDataObj = new $className(); //  Calling the constructor
                if(method_exists($sendDataObj, "setManifestId")){
                    $sendDataObj->setManifestId($manifestId);
                }
                if(method_exists($sendDataObj, "setWaybillMawb")){
                    $sendDataObj->setWaybillMawb($mawbNumber);
                }
                $sendDataResponse = json_decode($sendDataObj->sendData($trackingNumbers));
                if(isset($sendDataResponse['STATUS']) && $sendDataResponse['STATUS'] == "ERROR"){
                    $outputArray["status"] = "error";
                    $outputArray["message"] = $sendDataResponse['MESSAGE'];
                    $outputArray["label"] = "";
                } else if($outputArray["message"] == "") {
                $outputArray["status"] = "success";
                $outputArray["label"] = "";
                $outputArray["message"] = "Manifest dispatched successfully.";
            }
            echo json_encode($outputArray);
            die;
        }
        //Save New Address functionality
        if (isset($this->form_vars['form_action_address']) && $this->form_vars['form_action_address'] == 'save_address') {
            if(isset($this->form_vars['check_add_address'])){
                $user = SessionManager::getUser();
                $address = new Address();
                $address->setAddressLine1($this->form_vars['con_address_1']);
                $address->setAddressLine2($this->form_vars['con_address_2']);
                $address->setAddressLine3($this->form_vars['con_address_3']);
                $address->setCity($this->form_vars['con_city']);
                $address->setCountry($this->form_vars['con_country']);
                $address->setPostcode($this->form_vars['con_postcode']);
                $address->setUserId($user->getId());
                $address->save();
                $outputArray["status"] = "success";
                $outputArray["message"] = "Address added successfully";
            }else{
                $outputArray["status"] = "selected";
                $outputArray["message"] = "Address selected successfully";
            }
            echo json_encode($outputArray);
            die; 
        }
        if (isset($_GET['action']) && $_GET['action'] == "address_ajax") {
            $user = SessionManager::getUser();
            $addressFilter = new AddressFilter();
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

                $functionName = 'AddOrderBy' . $dataTableColumnName;
                $addressFilter->$functionName($orderFalse);
            }

            /*
             * Column filter
             * For search
             */

            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchCompany = $this->form_vars['search_company'];
                if (!empty($searchCompany))
                    $addressFilter->addFieldLikeFilter('company', $searchCompany);

                $searchContact = $this->form_vars['search_contact'];
                if (!empty($searchContact))
                    $addressFilter->addFieldLikeFilter('contact', $searchContact);

                $searchAddressLine1 = $this->form_vars['search_address_line_1'];
                if (!empty($searchAddressLine1))
                    $addressFilter->addFieldLikeFilter('address_line_1', $searchAddressLine1);


                $searchCity = $this->form_vars['search_city'];
                if (!empty($searchCity))
                    $addressFilter->addFieldLikeFilter('city', $searchCity);

                $searchCountry = $this->form_vars['search_country'];
                if (!empty($searchCountry))
                    $addressFilter->addFieldLikeFilter('country', $searchCountry);

                $searchPostcode = $this->form_vars['search_postcode'];
                if (!empty($searchPostcode))
                    $addressFilter->addFieldLikeFilter('postcode', $searchPostcode);
            }
            $addressFilter->addUserFilter($user->getId());

            $iTotalRecords = $addressFilter->getPagingCount();
            //Paginatiopn code start here 
            $addressDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            if(empty($iDisplayLength))
                $iDisplayLength = 20;
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $addressFilter->setRowsPerPage($iDisplayLength);
            $addressFilter->setOffset($iDisplayStart);


            $addressObjs = $addressFilter->getPagingList(" a.phone_number, a.id, a.company, a.contact, a.address_line_1, a.address_line_2, a.address_line_3,a.city,a.country, c.name 'country_name',a.postcode");
            foreach ($addressObjs as $addressObj) {
                $addressArr = array();
                $addressArr['option'] .= '<a data-country="'.$addressObj->getCountry().'"  data-address1="'.$addressObj->getAddressLine1().'" data-address2="'.$addressObj->getAddressLine2().'" data-address3="'.$addressObj->getAddressLine3().'" data-city="'.$addressObj->getCity().'" data-postcode="'.$addressObj->getPostcode().'" data-id="' . $addressObj->getId() . '" class="btn_select">Select </a>';
                $addressArr['company'] = $addressObj->getCompany();
                $addressArr['contact'] = $addressObj->getContact();
                $addressArr['address_line_1'] = $addressObj->getAddressLine1() . ' ' . $addressObj->getAddressLine2() . ' ' . $addressObj->getAddressLine3();
                $addressArr['city'] = $addressObj->getCity();
                $addressArr['country'] = $addressObj->getCountryName();
                $addressArr['country_iso'] = $addressObj->getCountry();
                $addressArr['postcode'] = $addressObj->getPostcode();
                $addressCurrArr = base64_encode(json_encode($addressArr));
                unset($addressArr['country_iso']);

                $addressDataArr [] = $addressArr;
            }

            $addressDataArrJson['data'] = $addressDataArr;
            $addressDataArrJson['draw'] = $sEcho;
            $addressDataArrJson['recordsTotal'] = $iTotalRecords;
            $addressDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($addressDataArrJson);
            die;
        }

//        agent_service_details
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "agent_service_details") {
            $agent_code = $this->form_vars["agent"];
            $AgentData = new AgentDataFilter();
            $AgentData->addAgentCodeFilter($agent_code);
            $agentdetails = $AgentData->getList();
            $agent_id = $agentdetails[0]->getId();
            $serviceAgentMapFilter = new ServiceAgentMappingDataFilter();
            $serviceAgentMapFilter->addAgentIDFilter($agent_id);
            $serviceAgentList = $serviceAgentMapFilter->getList();

            $servicehtml = "<select id='service_dropdown' name='service_dropdown'>";
            $servicehtml .= "<option value=''>Select Service</option>";
            if (count($serviceAgentList) > 0) {
                foreach ($serviceAgentList as $service) {

                    $ServicesfilterObj = new ServiceFilter();
                    $ServicesfilterObj->addIdFilter($service->getServiceId());
                    $list = $ServicesfilterObj->getList();
                    $servicename = $list[0]->getName();
                    $servicecode = $list[0]->getCode();
                    $serviceid = $list[0]->getId();
                    $servicehtml .= "<option value='" . $serviceid . "'> " . $servicename . " </option>";
                }
            }
            $servicehtml .= "</select>";
            echo $servicehtml;
            die;
        }
        if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "send_manifest_email"){
            $returnMsg = "";
            $to = $this->form_vars['manifest_carrier_email'].','.$this->form_vars['agent_carrier_email'];
            $subject = $this->form_vars['carrier_email_subject'];
            $content = $this->form_vars['carrier_email_content'];
            $manifestId = $this->form_vars['manifest_id_send_manifest_email'];
            if($manifestId < 0){
                $manifestId = 0;
            }
            $returnMsg = sendEmail($to,$subject,$content);
            if($returnMsg['result']=="success"){
                $manifest = new Manifest($manifestId);
                $manifest->setIsSendEmail("Y");
                $manifest->save();
            }
            echo json_encode($returnMsg);
            die;
        }
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        $countryFilter = new Country($user->getCountryId());
        $hub = $countryFilter->getName();
        /* Close Session functionality Start
          if (isset($_POST["form_action"])) {

          //print_r($this->form_vars["form_action"]);
          // take appropriate action
          switch ($_POST["form_action"]) {

          case "new_session":
          unset($_SESSION['NOT_SCANNED_NUMBERS']);
          unset($_SESSION['NUMBER_OF_NOT_SCANNED']);
          unset($_SESSION['SCANNED_NUMBERS']);
          unset($_SESSION['NUMBER_OF_SCANNED']);

          break;
          case "close_session":



          $boxNumber = trim($_POST["box_number_single"]);

          //////////////////// SENDING DATA TO SMART SYSTEM   ////////////////////////////
          $this->SendDataToServer($boxNumber);

          ////////////////////// CREATING A LABEL //////////////////////////////////////
          $boxLabel = new BoxLabel();


          $service = $_POST["service_type"];

          if ($service == "Please Select Service")
          $service = "";

          $numberOfScanned = 0;
          //	echo $_SESSION['NUMBER_OF_SCANNED'];
          if (isset($_SESSION['NUMBER_OF_SCANNED']))
          $numberOfScanned = $_SESSION['NUMBER_OF_SCANNED'];

          $boxLabel->buildPDFDocuments($boxNumber, $numberOfScanned, $service, $boxNumber, $hub);


          ////////////////////////////////// SHOW A POP TO DISPLAY A LABEL /////////////////

          echo '<script type="text/javascript">
          window.onload = function()
          {

          var bagNumber =  $("#box_number_single").val();
          var url = "../_assets/bag_pdf/" + bagNumber + ".pdf";
          showLabel(url, bagNumber, 400, 420);
          }
          </script>';

          ////////////////////////////// CLEARING ALL THE SESSION VARIABLES /////////////////////

          unset($_SESSION['NOT_SCANNED_NUMBERS']);
          unset($_SESSION['SCANNED_NUMBERS']);
          unset($_SESSION['NUMBER_OF_NOT_SCANNED']);
          unset($_SESSION['NUMBER_OF_SCANNED']);
          break;
          default:
          }
          }
          // Close Session functionality Start */
        // common initialisation for ths page
//        $this->setTitle("Box Scan");
//		$toolbar = Toolbar::getItem();
        // Check message
        //if ($this->table_msg == "") $this->table_msg = "";
    }

    /**
     * Head
     * - non-standard action buttons
     */
    protected function renderHead() {
        ?>
        <?php
    }

    /*     * *
     * Content View
     */

//    private function loadServiceTypes($id = '') {
//        $palletCarrierFilter = new PalletCarrierFilter();
//        $serviceArr = $palletCarrierFilter->getList();
//        if ($id == '')
//            echo '<select name="service_type" id="service_type" class="form-control select2" onchange="setImage();" width="400px" required >';
//        else
//            echo '<select name="' . $id . '" id="' . $id . '" onchange="setImage();" class="form-control select2" width="400px" required >';
//
//        echo '<option value="" >' . Translation::GetCaption("PLEASE_SELECT_SERVICE") . '</option>';
//
//        foreach ($serviceArr as $service) {
//            $selected = "";
//            if (isset($this->form_vars["service_type"]) && $this->form_vars["service_type"] == $value['value']) {
//                $selected = " SELECTED ";
//            }
//            echo '<option ' . $selected . '  value="' . $service->getId() . '">';
//            echo $service->getName();
//            echo '</option>';
//        }
//        echo '</select>';
//    }

//    private function loadAccounts() {
//        $user = SessionManager::getUser();
//
//        $parentid = $user->getId();
//
//        $userFilter = new UserAccountFilter();
//        $userFilter->addParentidFilter($parentid);
//        $accArr = $userFilter->getColumnList("id, user_account");
//
//        echo '<select name="account" class="select_dropdown" id="account" width="400px">';
//
//        echo '<option value="">Please Select Account</option>';
//
//        //die;
//
//        foreach ($accArr as $acc) {
//            echo '<option value="' . $acc->getAccount() . '"' . $selected . '>';
//            echo $acc->getAccount();
//            echo '</option>';
//        }
//        echo '</select>';
//    }

//    public function GetHubList() {
//        $table = "";
//
//        $warehouseFilter = new WarehouseFilter();
//        $warehouseFilter->AddOrderByWarehouse();
//        $list = $warehouseFilter->getColumnList("id, hub");
//
//        $table .= "<div class='col-md-3'>";
//        $table .= "<select id='hub-manifest' class='form-control'>";
//
//        foreach ($list as $hub) {
//            $table .= "<option value='" . $hub->getId() . "'>" . $hub->getHub() . "</option>";
//        }
//
//        $table .= "</select>";
//        $table .= "</div>";
//
//
//        return $table;
//    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <!--<link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />-->
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .main_formpage h2
            {
                border-bottom:0px;
            }
            .btn1 {
                background: none repeat scroll 0 0 #b2170e;
                border: medium none;
                border-radius: 5px;
                color: #fff;
                display: inline-block;
                font-size: 18px;
                font-weight: 700;
                height: 35px;
                line-height: 35px;
                margin: 5px 52px 10px 0;
                padding: 0 35px;
                text-decoration: none;
            }
            .web_dialog_overlay
            {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
                background: #000000;
                opacity: .5;
                filter: alpha(opacity=15);
                -moz-opacity: .15;
                z-index: 101;
                display: none;
            }

            .web_dialog_alert
            {
                display: none;
                position: fixed;
                width: 528px;
                height: 300px;
                top: 50%;
                left: 50%;
                margin-left: -190px;
                margin-top: -100px;
                background-color: #ffffff;
                border: 2px solid #336699;
                padding: 0px;
                z-index: 102;
                font-family: Verdana;
                font-size: 10pt;
            }

            .web_dialog
            {
                display: none;
                position: fixed;
                width: 380px;
                height: 180px;
                top: 50%;
                left: 50%;
                background-color: #ffffff;
                border: 2px solid #336699;
                padding: 0px;
                z-index: 102;
                font-family: Verdana;
                font-size: 10pt;
            }
            .web_dialog_title
            {
                border-bottom: solid 2px #003663;
                background-color: #000266;
                padding: 4px;
                color: White;
                font-weight:bold;

            }
            .web_dialog_title a
            {
                color: White;
                text-decoration: none;
            }
            .align_right
            {
                text-align: right;
            }
            .form_field_col1
            {
                width:80% !important;
            }
            .notes {
                background-attachment: local;
                background-image:
                    linear-gradient(to right, white 10px, transparent 10px),
                    linear-gradient(to left, white 10px, transparent 10px),
                    repeating-linear-gradient(white, white 30px, #ccc 30px, #ccc 31px, white 31px);
                line-height: 31px;
                padding: 8px 10px;
            }
            .inputfield_custom_style{
                width:90%;
                height:100px;
                font-size:54px;
                background-color:#f5f5f5;
            }
            .swal-large{
                width: 900px !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-samples.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-blockui.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js" type="text/javascript"></script>
<script type="text/javascript">
function get_source_warehouse() {
    var source_country_id = $("#source_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: source_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#source_warehouse_id").html("");
                $("#source_warehouse_id").html(data);
                $('#source_warehouse_id').selectpicker("refresh");
            } else {
            }
        },
        error: function () {
            alert('error occur');
        }
    });
}
function get_destination_warehouse() {
    var destination_country_id = $("#destination_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: destination_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#destination_warehouse_id").html("");
                $("#destination_warehouse_id").html(data);
                $('#destination_warehouse_id').selectpicker("refresh");
            } else {
            }
        },
        error: function () {
            alert('error occur');
        }
    });

}
$("#presort_carrier_group").change(function () {
    var carrierGroup = $(this).val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_carrier_service", carrier_group: carrierGroup},
        dataType: "html",
        success: function (data) {
            if (data) {
                var returnData = data.split(':::');
                $("#presort_carrier_hubs").html(returnData[1]);
                $('#presort_carrier_hubs').selectpicker("refresh");
            } else {

            }
        },
        error: function () {

        }
    });
});
var gridPallet = null;
var DataTableFunPallet = function () {
    var handleDataTablePallet = function () {
        var datatableparcelurl = "bagscan.php?action=pallet_list";
        gridPallet = new Datatable();
        gridPallet.init({
            src: $("#manage-pallet-data-table"),
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
                            {"data": "actions", "bSortable": false},
                            {"data": "date_created"},
                            {"data": "palletno"},
                            {"data": "pallet_carrier_id"},
                            {"data": "hub"},
                            {"data": "pallet_source_country_id"},
                            {"data": "pallet_source_warehouse_id"},
                            {"data": "pallet_destination_country_id"},
                            {"data": "pallet_destination_warehouse_id"}
                        ],
                rowCallback: function (row, data, index) {

                }
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleDataTablePallet();
        }
    };
}();
function select_pallet(palletNu,carrierId,Hub){
    $("#palletnumber").val(palletNu);
    $("#pallet_list_modal").modal("hide");
    $("#pallet_carrier_group").val(carrierId);
    $("#pallet_carrier_group").selectpicker('refresh');
    $("#pallet_carrier_group").prop('disabled', true);
    $("#carrier_hubs").val(Hub);
    $("#carrier_hubs").selectpicker('refresh');
    $("#carrier_hubs").prop('disabled', true);
}
$("#save_pallet_carrier_group").change(function () {
    var carrierGroup = $(this).val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_carrier_service", carrier_group: carrierGroup},
        dataType: "html",
        success: function (data) {
            if (data) {
                var returnData = data.split(':::');
                $("#save_carrier_hub").html(returnData[1]);
                $('#save_carrier_hub').selectpicker("refresh");
            } else {

            }
        },
        error: function () {

        }
    });
});
function get_save_source_warehouse() {
    var source_country_id = $("#save_source_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: source_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#save_source_warehouse_id").html("");
                $("#save_source_warehouse_id").html(data);
                $('#save_source_warehouse_id').selectpicker("refresh");
            } else {
                $("#save_source_warehouse_id").html("");
                $('#save_source_warehouse_id').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });
}
function get_save_destination_warehouse() {
    var destination_country_id = $("#save_destination_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: destination_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#save_destination_warehouse_id").html("");
                $("#save_destination_warehouse_id").html(data);
                $('#save_destination_warehouse_id').selectpicker("refresh");
            } else {
                $("#save_destination_warehouse_id").html("");
                $('#save_destination_warehouse_id').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });

}
function save_pallet_fun(){
    if ($("#save_pallet_carrier_group").val() == "") {
//        show_general_msg_pallet_modal
        show_res_msg("error","Please select pallet carrier group","_pallet_modal");
    }else if ($("#save_source_country_id").val() == "") {
        show_res_msg("error","Please select source country","_pallet_modal");
    }else if($("#save_destination_country_id").val() == "") {
        show_res_msg("error","Please select destination country","_pallet_modal");
    }else{
        var pallet_carrier_group = $("#save_pallet_carrier_group").val();
        var carrier_hubs = $("#save_carrier_hub").val();
        var source_country_id = $("#save_source_country_id").val();
        var source_warehouse_id = $("#save_source_warehouse_id").val();
        var destination_country_id = $("#save_destination_country_id").val();
        var destination_warehouse_id = $("#save_destination_warehouse_id").val();
        $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "create_pallet", source_country_id: source_country_id,source_warehouse_id:source_warehouse_id,destination_country_id:destination_country_id,destination_warehouse_id:destination_warehouse_id,pallet_carrier_group:pallet_carrier_group,carrier_hubs:carrier_hubs},
        success: function (data) {
                var obj = jQuery.parseJSON(data);
                if (obj.result == "success") {
                    show_res_msg('success',obj.message);
                    $("#palletnumber").val(obj.pallet);
                    $("#save_pallet_modal").modal("hide");
                } else {
                    show_res_msg('error',obj.message);
                }
            },
            error: function () {
                alert('error occur');
            }
        });
    }
}
//Mawb Data
var gridMawb = null;
var DataTableFunMawb = function () {
    var handleDataTableMawb = function () {
        var datatablemawburl = "bagscan.php?action=mawb_list";
        gridMawb = new Datatable();
        gridMawb.init({
            src: $("#manage-data-table-mawb"),
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
                    "url": datatablemawburl, // ajax source
                    headers: {

                    }
                },
                "bStateSave": true,
                "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "added_date"},
                                {"data": "mawb"},
                                {"data": "source_country"},
                                {"data": "source_warehouse"},
                                {"data": "destination_country"},
                                {"data": "destination_warehouse"}
                            ],
                rowCallback: function (row, data, index) {

                }
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleDataTableMawb();
        }
    };
}();
function get_mawb_source_warehouse() {
    var source_country_id = $("#mawb_source_country").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: source_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#source_mawb_warehouse").html("");
                $("#source_mawb_warehouse").html(data);
                $('#source_mawb_warehouse').selectpicker("refresh");
            } else {
                $("#source_mawb_warehouse").html("");
                $('#source_mawb_warehouse').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });
}
function get_mawb_destination_warehouse() {
    var destination_country_id = $("#mawb_destination_country").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: destination_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#destination_mawb_warehouse").html("");
                $("#destination_mawb_warehouse").html(data);
                $('#destination_mawb_warehouse').selectpicker("refresh");
            } else {
                $("#destination_mawb_warehouse").html("");
                $('#destination_mawb_warehouse').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });

}
function select_mawb(mawbNu){
    if($("#mawb_list_modal").hasClass("bag_mawb")){
        $("#mawb_number").val(mawbNu);
    }else if($("#mawb_list_modal").hasClass("single_mawb")){
        $("#box_number_single").val(mawbNu);
    }else if($("#mawb_list_modal").hasClass("multi_mawb")){
        $("#mawb_multiple").val(mawbNu);
    }
    $("#mawb_list_modal").modal("hide");
}
$(".tab-pane.active .btn_save_mawb").click(function () {
    clickCreateMawb();
});
$("#clickCreateMawb").click(function () {
    clickCreateMawb();
});
$(document).on('click', '.btn_calculate_weight', function() {

});


/*
* Calculate Weight Auto on key Press
 */
 
 function CalculateWeightAuto(){
 var chk = false;
    $.ajax({
        type: 'get',
        url: '<?php echo WEIGHT_MACHINE_ADRDRESS; ?>',
        dataType: 'jsonp', // Using Cross-Origin Resource Sharing
        jsonpCallback: 'callback',
        success: function(data) {
            chk = true;
            var strWeight = data.weight;
            var weight = strWeight.replace('kg', '');
            var strLength = data.length;
            var length = strLength.replace("cm", '');
            var strWidth = data.width;
            var width = strWidth.replace("cm", '');
            var strHeight = data.height;
            var height = strHeight.replace("cm", '');
            document.getElementById('single_length').value = length;
            document.getElementById('single_width').value = width;
            document.getElementById('single_height').value = height;
            document.getElementById('weight_single').value = weight;
        },
        error: function(xhr, status, err) {
           // alert("Device not connected please connect and then try again");
        }
    });
 }
function clickCreateMawb(){
    var mawb_number = "";
    mawb_number = $(".tab-pane.active .mawb_number_txt").val();
    if(($("#dispatch_fun_modal").data('bs.modal') || {}).isShown){
        mawb_number = $("#con_master_no").val();
    }
    if(mawb_number !=""){
        $("#mawb_number_span").html("");
        $("#mawb_number_span").html(mawb_number);
        $("#mawb_number_txt").val(mawb_number);
    }
    // Open Modal to Save Pallet
    $("#save_mawb_modal").modal("show");
}
function get_save_mawb_source_warehouse(){
//  save_mawb_source_warehouse_id
var source_country_id = $("#save_mawb_source_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: source_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#save_mawb_source_warehouse_id").html("");
                $("#save_mawb_source_warehouse_id").html(data);
                $('#save_mawb_source_warehouse_id').selectpicker("refresh");
            } else {
                $("#save_mawb_source_warehouse_id").html("");
                $('#save_mawb_source_warehouse_id').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });
}
function get_save_mawb_destination_warehouse(){
//  save_mawb_destination_warehouse_id
var destination_country_id = $("#save_mawb_destination_country_id").val();
    $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "get_country_warehouse_id", country_id: destination_country_id},
        dataType: "html",
        success: function (data) {
            if (data) {
                $("#save_mawb_destination_warehouse_id").html("");
                $("#save_mawb_destination_warehouse_id").html(data);
                $('#save_mawb_destination_warehouse_id').selectpicker("refresh");
            } else {
                $("#save_mawb_destination_warehouse_id").html("");
                $('#save_mawb_destination_warehouse_id').selectpicker("refresh");
            }
        },
        error: function () {
            alert('error occur');
        }
    });
}
function save_mawb_fun(){
    if ($("#save_mawb_source_country_id").val() == "") {
        show_res_msg("error","Please select source country","_master");
    }else if($("#save_mawb_destination_country_id").val() == "") {
        show_res_msg("error","Please select destination country","_master");
    }else{
        var source_country_id = $("#save_mawb_source_country_id").val();
        var source_warehouse_id = $("#save_mawb_source_warehouse_id").val();
        var destination_country_id = $("#save_mawb_destination_country_id").val();
        var destination_warehouse_id = $("#save_mawb_destination_warehouse_id").val();
        var mawb_number = "";
        mawb_number = $(".tab-pane.active .mawb_number_txt").val();
        if(($("#dispatch_fun_modal").data('bs.modal') || {}).isShown){
            mawb_number = $("#con_master_no").val();
        }
        $.ajax({
        type: "POST",
        url: "box_ajax.php",
        data: {action: "create_mawb",mawb_number:mawb_number,source_country_id: source_country_id,source_warehouse_id:source_warehouse_id,destination_country_id:destination_country_id,destination_warehouse_id:destination_warehouse_id},
        success: function (data) {
                var obj = jQuery.parseJSON(data);
                if (obj.response == "success") {
                    show_res_msg('success',obj.msg);
                    $(".tab-pane.active .mawb_number_txt").val(obj.mawb);
                    $("#save_mawb_modal").modal("hide");
                } else {
                    show_res_msg('error',obj.msg);
                    $("#save_mawb_modal").modal("hide");
                }
            },
            error: function () {
                alert('error occur');
            }
        });
    }
}
$(".btn_list_single").click(function(){
  $("#mawb_list_modal").removeClass("bag_mawb");
  $("#mawb_list_modal").removeClass("multi_mawb");
  $("#mawb_list_modal").addClass("single_mawb");
});
$(".btn_list_multi").click(function(){
    $("#mawb_list_modal").removeClass("bag_mawb");
    $("#mawb_list_modal").removeClass("single_mawb"); 
    $("#mawb_list_modal").addClass("multi_mawb");
});
$(".btn_list_mawb").click(function(){
    $("#mawb_list_modal").removeClass("multi_mawb");
    $("#mawb_list_modal").removeClass("single_mawb"); 
    $("#mawb_list_modal").addClass("bag_mawb");
});






var request;
var active = false;
var boxTrackingNumbersArray = [];
var matchTrackingNumbers = [];
var sendDataWithMissingScanItems = false;

function showLabel(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    var WindowFeatures = "menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes";
}
var count = 0;
function checkedAll(group) {
    if (count == 0)
    {
        for (var i = 0, len = group.length; i < len; i++)
        {
            if (group[i].checked == false)
                group[i].click();//checked = true;
            count = 1;
        }
        $('#assign-manifiest').show();
    } else
    {

        for (var i = 0, len = group.length; i < len; i++)
        {
            if (group[i].checked == true)
                group[i].click();// = false;
            count = 0;
        }
        $('#assign-manifiest').hide();
    }
}
function change(sourceUrl) {
    //alert(sourceUrl);
    var audio = $("#player");
    $("#scan_audio").attr("src", sourceUrl);
    audio[0].pause();
    audio[0].load();//suspends and restores all audio element
    audio[0].play();
}
//It's working fine 
function checkKeyValue(e, tabnumber) {
    if (e.keyCode == '13' && tabnumber == '1') {
        btnBagScan();
        return false;
    } else if (e.keyCode == 13 && tabnumber == 2)
    {
        btnScanSingle();
        return false;
    } else if (e.keyCode == 13 && tabnumber == 3)
    {
        $("#tracking_number_single").focus();
        return false;
    }
}
$("#box_number").focus();
$('#player').hide();
$("#btn_scan_shipment").click(function () {});
$("#btn_scan_shipment").click(function () {
    //var baz = document.warehouse.barcodelist.value;
    //lines = baz.split(/\r\n|\r|\n/g);
    var arr = $("#barcodelist").val().split("\n").filter(Boolean);
    var arrDistinctBarcodes = new Array();
    $(arr).each(function (index, item) {
        if ($.inArray(item, arrDistinctBarcodes) == -1)
            arrDistinctBarcodes.push(item);
    });
    var r = confirm("Your total number of unique scan barcodes are " + arrDistinctBarcodes.length);
    if (r == true)
    {
        var box_number = $("#box_multiple").val();
        $.ajax({
            type: "POST",
            url: "box_ajax.php", // your php file name
            data: {action: 'shipment_scan', boxnumber: box_number, trackingNumber: arrDistinctBarcodes.toString()},
            success: function (data)
            {}
        });
    } else
    {
        return false;
    }
});

/*
 * Scenario 201
 */
function btnScanSingle() {
    if ($.trim($("#tracking_number_single").val()).length == 0)
    {
        $("#show_general_msg div.alert").html(" ");
        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
        $("#show_general_msg div.alert").html("<h1><?php echo Translation::GetCaption("PLEASE_ENTER_TRACKING_NUMBER") ?></h1>");
        $("#show_general_msg").show();
        $("#tracking_number_single").focus();
        return false;
    }
    if ($('#trWeightSingle').is(":visible") && $.trim($("#weight_single").val()).length == 0)
    {
        swal("", "Please enter weight", "info");
        $("#weight_single").focus();
        return false;
    }
    if ($('#tdDimSingle').is(":visible"))
    {
        if ($.trim($("#single_length").val()).length == 0)
        {
            swal("", "Please enter length", "info");
            return false;
        }
        if ($.trim($("#single_width").val()).length == 0)
        {
            swal("", "Please enter weight", "info");
            return false;
        }
        if ($.trim($("#single_height").val()).length == 0)
        {
            swal("", "Please enter height", "info");
            return false;
        }
    }
    var messageDiv = $('#message_single_scan');
    var box_number = $.trim($("#box_number_single").val());
    var tracking_number = $.trim($("#tracking_number_single").val());
    var weight = $.trim($("#weight_single").val());
    var length = $.trim($("#single_length").val());
    var width = $.trim($("#single_width").val());
    var height = $.trim($("#single_height").val());
    var allow_weight = $('#hidAllowWeight').val();
    var hold_label = $.trim($('#hold_label:checked').val());
    var re_scan = $.trim($('#re_scan:checked').val());
    $.blockUI();
    $.ajax({
        type: "POST",
        url: "box_ajax.php", // your php file name
        data: {action: 'single_shipment_scan', boxnumber: box_number, trackingnumber: tracking_number, weight: weight, length: length, width: width, height: height, allow_weight: allow_weight,hold_label:hold_label,re_scan:re_scan},
        success: function (data)
        {
            $.unblockUI();
            var obj = JSON.parse(data);
            if (obj.result === 'error')
            {
                showErrorMessage(obj.message,"error");
                $("#tracking_number_single").val("");
                $('#tracking_number_single').css("background-color", "red");
                $(".icon-arrow-up").click();
            } else if (obj.result === 'success') {
                $("#show_general_msg div.alert").html(" ");
                $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                $("#show_general_msg div.alert").html("<h1>" + obj.message + "</h1>");
                $("#show_general_msg").show();
                $(".icon-arrow-up").click();
                $("#tracking_number_single").css("background-color", "green");
                if(obj.label != ''){
                    var labelLink = "<?php echo SETTING_MAIN_ASSETS; ?>"+"pdf/"+obj.label;
                    console.log(labelLink);
                   newwindow = window.open(labelLink, 'Label', 'height=400,width=400');
                    if (window.focus) {
                        newwindow.focus()
                    }
                }
                setTimeout(function () {
//                                $('#box_number_single').val("");
                    $('#tracking_number_single').val("");
                    $('#tracking_number_single').focus();
                    $('#tracking_number_single').css("background-color", "white");
                    if (!$('#chkSameDim').is(":checked"))
                    {
                        $('#weight_single').val("");
                        $("#single_width").val("");
                        $("#single_length").val("");
                        $("#single_height").val("");

                    }
                }, 1000);
            }
        }
    });
    return false;
}
function refreshScanTable() {
    var htmlTable = "<tr><td style='color:blue'>" + boxTrackingNumbersArray.length + " Tracking Numbers found in a bag : " + matchTrackingNumbers.length + " scanned.</td></tr>";
    var tblBoxTrackingNumbers = $('#tblBoxTrackingNumbers');
    tblBoxTrackingNumbers.html('');
    jQuery.grep(boxTrackingNumbersArray, function (el)
    {
        if (jQuery.inArray(el, matchTrackingNumbers) == -1)
        {
            htmlTable += '<tr>';
            htmlTable += '<td style="color:red">';
            htmlTable += el;
            htmlTable += '</td>';
            htmlTable += '</tr>';
            //difference.push(el);
        } else
        {
            htmlTable += '<tr>';
            htmlTable += '<td style="color:green">';
            htmlTable += el;
            htmlTable += '</td>';
            htmlTable += '</tr>';
        }
    });
    tblBoxTrackingNumbers.append(htmlTable);
}

function btnClaimReturn(id) {
    var request =
            $.ajax({
                type: "POST",
                url: "box_ajax.php", // your php file name
                data: {id: id, action: 'getConsignment'},
                success: function (data)
                {
                    //alert(data);
                    var obj = JSON.parse(data);
                    $("#overlay").show();
                    $("#lblService").text(obj.service);
                    $("#lblawb").text(obj.awb);
                    $("#lblreturn_awb").text(obj.return_awb);
                    $("#lbladdress").text(obj.addressline1 + ' ' +
                            obj.addressline2 + ' ' +
                            obj.addressline3 + ' ' +
                            obj.city + ' ' +
                            obj.postcode + ' ' +
                            obj.country);

                    $("#dialogAlertClaim").dialog("open");
                    $("#hidConId").val(obj.id);
                    //LoadEndOfDay();
                    //$('#successdiv').html(data); 
                }
            });
    return false;
}

function btnbook(handlingid) {
    $.blockUI();
    var request =
            $.ajax({
                type: "POST",
                url: "box_ajax.php", // your php file name
                data: {handlingid: handlingid, action: 'booking'},
                success: function (data)
                {
                    $.unblockUI();
                    $('#successdiv').html(data);
                }
            });
    return false;
}

function unique(list) {
    var result = [];
    $.each(list, function (i, e) {
        if ($.inArray(e, result) == -1)
            result.push(e);
    });
    return result;
}

            function MultipleShipmentScan() {
                var multishipmentOrder = $('input[name=pre_sort]:checked', '#MultipleShipmentScan').val();
                var outboundService = $('#search_Code').val();
                if (multishipmentOrder != "scan_outbound") {
                    if ($.trim($("#mawb_multiple").val()).length == 0) {
                        $('#message_multiple').addClass("alert-danger");
                        $('#message_multiple').show().html('<?php echo Translation::GetCaption("MSG_PLEASE_ENTER_MAWB_NUMBER") ?>');
                        $("#mawb_number").focus();
                        $("#divMawb").addClass("has-error has-danger");
                        $('html, body').animate({
                            scrollTop: $("#multiple_shipment_scan").offset().top
                        }, 1000);
                        return false;
                    } else {
                        $("#divMawb").removeClass("has-error has-danger");
                        $("#message_multiple").removeClass("alert-danger");
                        $("#message_multiple").html('');
                    }
                }
                if (boxTrackingNumbersArray.length > 0 && !sendDataWithMissingScanItems) {
                    var difference = [];
                    var arr = $("#barcodelist").val().split("\n").filter(Boolean);
                    var arrDistinctBarcodes = new Array();
                    $(arr).each(function (index, item) {
                        if ($.inArray(item, arrDistinctBarcodes) == -1)
                            arrDistinctBarcodes.push(item);
                    });
                    matchTrackingNumbers = arrDistinctBarcodes.concat(matchTrackingNumbers);
                    matchTrackingNumbers = unique(matchTrackingNumbers);
                    refreshScanTable();
                    jQuery.grep(boxTrackingNumbersArray, function (el)
                    {
                        if (jQuery.inArray(el, matchTrackingNumbers) == -1)
                            difference.push(el);
                    });
                    $("#msgyesnonotfound").html("Do you want to close the bag? Total <b>" + difference.length +
                            " </b>Tracking numbers are not scanned<br>" + difference.join("<br>"));
                    $("#dialogAlertNotFoundItems").modal("show");
                    return false;
                }
                var box_number = $.trim($("#box_multiple").val());
                var mawb_number = $.trim($("#mawb_multiple").val());
                var arr = $("#barcodelist").val().split("\n").filter(Boolean);
                var arrDistinctBarcodes = new Array();
                $(arr).each(function (index, item) {
                    if ($.inArray(item, arrDistinctBarcodes) == -1)
                        arrDistinctBarcodes.push(item);
                });
                if (arrDistinctBarcodes.length == 0)
                {
                    $('#message_multiple').addClass("alert-danger");
                    $('#message_multiple').show().html('<?php echo Translation::GetCaption("MSG_PLEASE_SCAN_ONE_MORE_TRACKING_NUMBERS") ?>');
                    $('#barcodelist_form_div').addClass("has-error has-danger");
                    $('html, body').animate({
                        scrollTop: $("#multiple_shipment_scan").offset().top
                    }, 1000);
                    return false;
                } else {
                    $("#message_multiple").removeClass("alert-danger");
                    $("#message_multiple").html('');
                    $("#barcodelist_form_div").removeClass("has-error has-danger");
                }
                var json_arrDistinctBarcodes = JSON.stringify(arrDistinctBarcodes);
                var do_bagging = "N";
                if (multishipmentOrder == "scan_outbound")
                {
                    do_bagging = "Y";
                    console.log('scan_outbound');
                }
                if (multishipmentOrder == "pre_sort")
                {
                    console.log('per sort');
                    do_bagging = "Y";
                    var presort_carrier = $("#presort_carrier option:selected").val();
                    if (presort_carrier == 0)
                    {
                        $('#message_multiple').addClass("alert-danger");
                        $('#message_multiple').show().html('Please select Pre-Sort Carrier');
                        $('html, body').animate({scrollTop: '0px'}, 1000);
                        return false;
                    } else {
                        $("#message_multiple").removeClass("alert-danger");
                        $("#message_multiple").html('');
                    }
                    var hub = $("#yodel_hub option:selected").val();
                    if (hub == 0)
                    {
                        $('#message_multiple').addClass("alert-danger");
                        $('#message_multiple').show().html('Please select Hub');
                        $('html, body').animate({scrollTop: '0px'}, 1000);
                        return false;
                    } else {
                        $("#message_multiple").removeClass("alert-danger");
                        $("#message_multiple").html('');
                    }
                }
                $.blockUI();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'multiple_box_scan',
                        boxnumber: box_number,
                        mawbnumber: mawb_number,
                        trackingnumbers: json_arrDistinctBarcodes,
                        do_bagging: do_bagging,
                        presort_carrier: presort_carrier,
                        hub: hub,
                        scan_type:multishipmentOrder,
                        outbound_service:outboundService
                    },
                    success: function (data)
                    {
                        $.unblockUI();
                        var obj = JSON.parse(data);
                        console.log(obj);
                        var $messageDiv = $('#message_multiple'); // get the reference of the div
                        if (obj.result === 'error')
                        {
                            if(obj.mawb === 'error'){
                                $(".btn_save_mawb").click();
                                return false;
                            }
                            $("#loadingDivMultiple").hide();
                            $("#overlay").hide();
                            showErrorMessage(obj.message,"error");
                            $("#box_multiple").css("background-color", "red");
                            var message = obj.message;
                            var message = message.split(' ').join('%2f');
                            $('#player').hide();
                            setTimeout(function ()
                            {
                                $messageDiv.removeClass("alert-danger");
                                $messageDiv.html('');
                                $("#box_multiple").val("");
                                $('#box_multiple').focus();
                                $("#box_multiple").css("background-color", "white");
                            }, 3000);
                        } else if (obj.result === 'success')
                        {
                            $("#box_multiple").css("background-color", "springgreen");
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                            $("#show_general_msg").show();
                            $("#barcodelist").css('background-color', 'springgreen');
                            if (obj.hold_tracking_numbers != null)
                            {
                                if (obj.hold_tracking_numbers.length > 0)
                                {
                                    $("#msg").html(obj.holdmessage);
                                    $("#overlay").show();
                                    $("#btnClose, #btnOk, #btnCloseAlert").click(function (e)
                                    {
                                        $("#overlay").hide();
                                    });
                                }
                            }
                            if (obj.filename != '')
                            {
                                $("#barcodelist").val('');
                                newwindow = window.open(obj.filename, 'Label', 'height=400,width=400');
                                if (window.focus) {
                                    newwindow.focus()
                                }
                            }
                            setTimeout(function () {
                                $('#box_multiple').val("");
                                $('#box_multiple').focus();
                                $("#barcodelist").css('background-color', '#FFC');
                                $("#box_multiple").css("background-color", "white");
                                $messageDiv.html('');
                                $messageDiv.removeClass("alert-success");

                            }, 3000);
                            if (obj.tracking_numbers != null)
                            {
                                if (obj.tracking_numbers.toString().length > 0)
                                {
                                    var strNumbers = obj.tracking_numbers.toString();
                                    var trackingNumbersArray = strNumbers.split(",");
                                    var htmlTable = "<tr><td style='color:green'>Tracking Numbers List</td></tr>";
                                    for ($i = 0; $i <= trackingNumbersArray.length - 1; $i++)
                                    {
                                        htmlTable += '<tr>';
                                        htmlTable += '<td>';
                                        htmlTable += trackingNumbersArray[$i];
                                        htmlTable += '</td>';
                                        htmlTable += '</tr>';
                                    }
                                    $('#tblTrackingNumbers').append(htmlTable);
                                }
                            }
                            $('html, body').animate({
                                scrollTop: $("#multiple_shipment_scan").offset().top
                            }, 1000);
                        }
                    }
                });
                return false;
            }

            function btnBagScan() {
                var box_weight = $.trim($("#box_weight").val());
                var pre_sort = 'n';
                var carrierId = 0;
                var carrierHubId = 0;
                if ($("#pre_sort_outer_label").is(":checked")) {
                    pre_sort = "y";
                    if ($("#pallet_carrier_group").val() == "") {
                        showErrorMessage("Please select carrier Group","error");
                        return false;
                    } else {
                        carrierId = $("#pallet_carrier_group").val();
                    }
                    carrierHubId = $("#carrier_hubs").val();
                }
                if ($.trim($("#box_weight").val()).length == 0 || $.isNumeric(box_weight) !== true)
                {
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    $("#show_general_msg div.alert").html("<h1>Please enter correct carton weight</h1>");
                    $("#show_general_msg").show();
                    $("#box_weight").val("");
                    $("#box_weight").focus();
                    return false;
                }
                if ($.trim($("#box_number").val()).length == 0)
                {
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    $("#show_general_msg div.alert").html("<h1>Please enter carton number</h1>");
                    $("#show_general_msg").show();
                    $("#box_number").focus();
                    return false;
                }
                if ($("#box_weight").val() == 0)
                {
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    $("#show_general_msg div.alert").html("<h1>Carton weight should be greater than 0</h1>");
                    $("#show_general_msg").show();
                    $("#box_weight").focus();
                    return false;
                }
                var box_number = $.trim($("#box_number").val());
                var mawb_number = $.trim($("#mawb_number").val());
                var palletno = "";
                if ($("#palletnumber").get(0))
                {
                    palletno = $('#palletnumber').val();
                }
                $('#tblTrackingNumbers tr').remove();
                $.blockUI();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'box_scan', boxnumber: box_number, mawbnumber: mawb_number, boxweight: box_weight, palletno: palletno, pre_sort: pre_sort, carrier_id: carrierId, carrier_hub_id: carrierHubId},
                    success: function (data)
                    {
                        $.unblockUI();
                        var obj = JSON.parse(data);
                        var $messageDiv = $('#message'); // get the reference of the div
                        if (obj.result === 'success')
                        {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                            $("#show_general_msg").show();
                            $("#box_number").val("");
                            if (obj.label != '')
                            {
                                $("#barcodelist").val('');
                                newwindow = window.open(obj.label, 'Label', 'height=400,width=400');
                                if (window.focus) {
                                    newwindow.focus()
                                }
                            }
                            $("#btnClosePallet").show();
                        }
                        if (obj.result === 'error')
                        {
                            if ($.trim(obj.remove_pallet) === 'pallet') {
                                swal({
                                    title: obj.message,
                                    text: "",
                                    type: "warning",
                                    html:true,
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
                                            type: "POST",
                                            url: "box_ajax.php", // your php file name
                                            data: {action: 'remove_pallet', boxnumber: box_number, palletno: palletno, pre_sort: pre_sort, carrier_id: carrierId, carrier_hub_id: carrierHubId, mawbnumber: mawb_number},
                                            success: function (data)
                                            {
                                                var obj = JSON.parse(data);
                                                if (obj.result === 'success') {
                                                    $("#show_general_msg div.alert").html(" ");
                                                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                                    $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                                    $("#show_general_msg").show();
                                                    if (obj.link != '')
                                                    {
                                                        $("#barcodelist").val('');
                                                        newwindow = window.open(obj.link, 'Label', 'height=400,width=400');
                                                        if (window.focus) {
                                                            newwindow.focus()
                                                        }
                                                    }
                                                } else {
                                                    showErrorMessage(obj.message,"error");
                                                }
                                            }
                                        });
                                    } else {
                                        $("#box_number").val("");
                                    }
                                });
                            }
                            if (obj.pre_sort === 'no') {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html(obj.message);
                                $("#show_general_msg").show();
                            }else if (obj.mawb === 'error') {
                                $(".btn_save_mawb").click();
                            } else {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html(obj.message);
                                $("#show_general_msg").show();
                                $("#box_number").css("background-color", "red");
                                setTimeout(function ()
                                {
                                    $("#box_number").val("");
                                    $('#box_number').focus();
                                    $("#box_number").css("background-color", "white");
                                }, 3000);
                            }
                        } else if (obj.result === 'success')
                        {
                            if (obj.pre_sort === 'ok') {
                                $("#box_number").css("background-color", "springgreen");
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                $("#show_general_msg").show();
                                $("#btnClosePallet").text("Generate Sorted Bag Label");
                                $("#btnClosePallet").show();

                                setTimeout(function () {
                                    $('#box_number').val("");
                                    $('#box_weight').val("");
                                    $('#box_weight').focus();
                                    $("#box_number").css("background-color", "white");
                                }, 3000);
                            }
                            if (obj.hold_tracking_numbers.length > 0)
                            {
                                $("#msg").html(obj.holdmessage);
                                $("#overlay").show();
                                $("#btnClose, #btnOk, #btnCloseAlert").click(function (e)
                                {
                                    $("#overlay").hide();
                                    $("#box_number").css("background-color", "springgreen");
                                    $('#player').hide();
                                    $messageDiv.show().html("<h1>"+obj.message+"</h1>");
                                    if (obj.filename != '')
                                    {
                                        newwindow = window.open(obj.filename, 'Label', 'height=400,width=400');
                                        if (window.focus) {
                                            newwindow.focus();
                                        }
                                    }
                                    $('#box_number').val("");
                                    $('#box_weight').val("");
                                    $('#box_weight').focus();
                                    $("#box_number").css("background-color", "white");
                                    e.preventDefault();
                                })
                            } else {
                                $("#box_number").css("background-color", "springgreen");
                                $messageDiv.css('color', 'green');
                                $messageDiv.show().html("<h1>"+obj.message+"</h1>");
                                if (obj.filename != '')
                                {
                                    newwindow = window.open(obj.filename, 'Label', 'height=400,width=400');
                                    if (window.focus) {
                                        newwindow.focus()
                                    }
                                }

                                if (obj.yodel_bag_label != '')
                                {
                                    window.open(obj.yodel_bag_label, 'YodelBagLabel', 'height=400,width=400');
                                }
                                setTimeout(function () {
                                    $('#box_number').val("");
                                    $('#box_weight').val("");
                                    $('#box_weight').focus();
                                    $("#box_number").css("background-color", "white");
                                }, 3000);
                            }//auto printing of labels
                        }
                    }
                });
                return false;
            }
            
            function LoadAwaitingClaims() {
                var request =
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'loadawaitingclaims'},
                            success: function (data)
                            {
                                $('#ClaimReturns').html(data);
                            }
                        });
                return false;
            }

            function LoadEndOfDay() {
                var group_by = $('input[name="endofdaytype"]:checked').val();
                $.blockUI();
                var request = "";
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'endofday', group_by: group_by},
                    success: function (data)
                    {
                        $.unblockUI();
                        $('#end_of_the_day_div').html(data);
                        $('.icheck').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue',
                            radioClass: 'iradio_minimal-blue'
                        });
                    }
                });
                return false;
            }
//            function multishipment_scan(){
//                var mawbNo = $.now().toString();
//                mawbNo = mawbNo.substring(0,3) + "-" +mawbNo.substring(3,mawbNo.length-2);
//                $("#mawb_multiple").val(mawbNo);
//            }
//            function singleshipment_scan(){
//                var mawbNo = $.now().toString();
//                mawbNo = mawbNo.substring(0,3) + "-" +mawbNo.substring(3,mawbNo.length-2);
//                $("#box_number_single").val(mawbNo);
//            }

            function resolve() {
                var comments = $.trim($("#txtResolve").val());
                if (comments == '')
                {
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    $("#show_general_msg div.alert").html("Please enter comments");
                    $("#show_general_msg").show();
                    return false;
                }
                var trackingnumber = []
                $("input[name='chkHoldNumber[]']:checked").each(function ()
                {
                    trackingnumber.push($(this).val());
                });
                if (trackingnumber.length == 0)
                {
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                    $("#show_general_msg div.alert").html("<h1>Please select atleast one tracking number to resolve</h1>");
                    $("#show_general_msg").show();
                    return false;
                }
                //alert(trackingnumber);
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'resolveholdparcels', trackingnumber: JSON.stringify(trackingnumber), comments: comments},
                    success: function (data)
                    {
                        var obj = JSON.parse(data);

                        if (obj.result == 'success')
                        {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                            $("#show_general_msg").show();
                        }
                    }
                });
            }

            function setImage() {
                var carrier = $("#service_type").val();
                var data;
                if (carrier == 'ROYAL MAIL T&S EU' || carrier == 'ROYAL MAIL T&S NON-EU' || carrier == 'ROYAL MAIL UNTRACKED')
                {
                    data = '../images/carrierlogo/1401208562royal mail.png';
                } else if (carrier == 'HUNGARY POST REG. EU' || carrier == 'HUNGARY POST REG. NON-EU')
                {
                    data = '../images/carrierlogo/hungary-post.png';
                } else if (carrier == 'SWEDEN POST REG. NON-EU' || carrier == 'SWEDEN POST UNTRACKED NON-EU')
                {
                    data = '../images/carrierlogo/1402045934sweden post.png.jpg';
                } else if (carrier == 'DHL')
                {
                    data = '../images/carrierlogo/1401901162icon_dhl.png';
                } else if (carrier == 'HERMES')
                {
                    data = '../images/carrierlogo/1401208995hermes.png';
                } else if (carrier == 'YODEL')
                {
                    data = '../images/carrierlogo/1401208893yodel.png.jpg';
                } else if (carrier == 'DPD')
                {
                    data = '../images/carrierlogo/1402046252dpd-logo.jpg';
                } else if (carrier == 'EUROB2C')
                {
                    data = '../images/carrierlogo/1402045934sweden post.png.jpg';
                }
                $('#service_img').attr("src", data);
                $('#service_img').css("display", "block");
            }
            function checkRoutingHub(trackingNumber, hubId, preSortCarrierId, trackingNumbersList) {
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'CheckRoutingHub', trackingNumber: trackingNumber, hubId: hubId, preSortCarrierId: preSortCarrierId},
                    success: function (data)
                    {
        //                        console.log(data);
                        var obj = JSON.parse(data);
                        if (obj.status == 0)
                        {
                            if (obj.message != '')
                            {
                                $("#msg").text(obj.message);

                            }

                            $("#dialogAlert").modal('show');

                            trackingNumbersList[trackingNumbersList.length - 1] = "";

                            $('#barcodelist').val('');

                            var barcodelist = document.getElementById("barcodelist");
                            barcodelist.value = trackingNumbersList.join("\n");
                        } else {
                            if ($("#mawb_multiple").val() == '')
                            {
                                $("#mawb_multiple").val(obj.jobid);
                            }
                        }
                    }
                });
            }
            function checkTotalWeight(trackingNumbers) {
                var source_country_id = $("#source_country_id").val();
                    var destination_country_id = $("#destination_country_id").val();
                    if(source_country_id == "" || destination_country_id == ""){
                        swal("","please select source country and destination country", "error");
                        $('#barcodelist').val('');
                        arr[arr.length - 1] = "";
                        $('#barcodelist').val(arr.join("\n"));
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'CheckTotalWeight', trackingNumbers: trackingNumbers},
                            success: function (data)
                            {
                              var obj = JSON.parse(data);
                              if(obj.status == "limit"){
                                show_res_msg('error',obj.message);
                                $('#barcodelist').val('');
                                trackingNumbers[trackingNumbers.length - 1] = "";
                                $('#barcodelist').val(trackingNumbers.join("\n"));
                              }else if(obj.status == "error"){
                                show_res_msg('error',obj.message);
                                $('#barcodelist').val('');
                                trackingNumbers[trackingNumbers.length - 1] = "";
                                $('#barcodelist').val(trackingNumbers.join("\n"));
                              }else{
                                $('#barcodelist').val(trackingNumbers.join("\n")+"\n");
                                show_res_msg('success',obj.message);
                              }
                            }
                        });   
                    }
            }
            function speak(text) {
                var msg = new SpeechSynthesisUtterance();
                msg.default = false;
                var voices = window.speechSynthesis.getVoices();
                msg.voice = voices.filter(function (voice) {
                    return voice.name == 'Google UK English Male';
                })[0];
                msg.text = text;
                msg.lang = 'en-GB';
                window.speechSynthesis.speak(msg);
            }
            function Dispatch() {
                var palletno = $.trim($('#palletnumberdispatch').val());
                var dispatchdate = $("#date_dispatch").val();
                jQuery('div#loadingDivPallet').show();
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'dispatchpallet',
                        palletnumber: palletno,
                        dispatchdate: dispatchdate},
                    success: function (data)
                    {
                        var obj = JSON.parse(data);
                        jQuery('div#loadingDivPallet').hide();
                        if (obj.result == 'error')
                        {
                            // $("#dialogAlert").dialog( "open" );
                            $('#palletMsg').addClass('alert-danger');
                            $("#palletMsg").text(obj.message);
                        } else
                        {
                            $("#palletnumberdispatch").css("background-color", "springgreen");
                            $("#msg").text(obj.message);

                            // $("#dialogAlert").dialog( "open" );

                            setTimeout(function ()
                            {
                                $("#palletnumberdispatch").val("");
                                $("#palletnumberdispatch").css("background-color", "#FFC");
                            }, 1000);
                        }
                    }
                });

            }
            function Dispatch_popup(parcelId, element) {
                var htmlReturn = "";
                var service_type = $(element).data('servicetype');
                var service_id = $(element).data('service_id');
                var carrier_id = $(element).data('carrier_id');
                var agent_id = $(element).data('agent_id');
                var type_case = $(element).data('type_case');
                htmlReturn = "<span> Are you sure you want to manifest <strong> " + service_type + " </strong></span>";
                $('#palletdetails').html();
                $('#palletdetails').html(htmlReturn);
                $("#frm_parcel_id").val(parcelId);
                $("#frm_service_id").val(service_id);
                $("#frm_type_case").val(type_case);
                $("#frm_agent_id").val(agent_id);
                $("#frm_carrier_id").val(carrier_id);
                $("#frm_parcel_action").val("save_parcel_manifest");
                $("#form_action").val("");
            }
            function AgentBasedService() {
                var agent = $('#agent').val();
                $.ajax({
                    type: "POST",
                    url: "bagscan.php", // your php file name
                    data: {agent: agent, func: 'agent_service_details'},
                    success: function (data)
                    {
                        $('#service_dropdown').html(" ");
                        $('#service_dropdown').html(data);
                    }
                });
            }
            $(document).ready(function (e) {
                $(document).on('ifChanged', '#chkWeight', function () {
                    if ($(this).prop("checked") == true) {
                        $("#trWeightSingle").css("display", "none");
                    } else
                    {
                        $("#trWeightSingle").css("display", "block");
                    }
                });

                $("#btnCloseModal").click(function (e)
                {
                    $("#endOfDayLoader").hide();
                    $('#manifest_message').removeClass('alert-success');
                    $('#manifest_message').removeClass('alert-danger');
                    $('#mymodal').modal('hide');
                    e.preventDefault();
                });
                $(document).on('click', '.pre_sort', function () {
                    if ($(this).val() == "pre_sort")
                    {
                        $("#pre_sort_multiscan_div").show();
                        $(".divScanOutBoundCarrier").hide();
                        $("#divMawb").show();
                        $("#divBagNumber").show();
                        $("#btn_scan_multiple").val("scan");
                    } else if ($(this).val() == "scan_outbound") {
                        $("#pre_sort_multiscan_div").hide();
                        $(".divScanOutBoundCarrier").show();
                        $("#divMawb").hide();
                        $("#divBagNumber").hide();
                        $("#btn_scan_multiple").val("Create Bag");
                    } else if ($(this).val() == "scan_bag") {
                        $("#divMawb").show();
                        $("#divBagNumber").show();
                        $("#btn_scan_multiple").val("scan");
                        $("#pre_sort_multiscan_div").hide();
                        $(".divScanOutBoundCarrier").hide();
                    }
                });
                $("#btnSendEmail").click(function () {
                    var manifestIdArr = [];
                    var hub = $("#hub-manifest option:selected").val();
                    var hubname = $("#hub-manifest option:selected").text();
                    if (confirm('Do you want to send a pre-alert email to ' + hubname + '?'))
                    {
                        $.each($("input[name='manifestData[]']:checked"), function ()
                        {
                            var manifestid = $(this).val();
                            var request = "";
                            $.ajax({
                                type: "POST",
                                url: "box_ajax.php", // your php file name
                                data: {action: 'senddispatchemailtohub', manifestid: manifestid, hub: hub},
                                success: function (data)
                                {

                                }
                            });
                        });
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                        $("#show_general_msg div.alert").html("<h1>email sent to " + hubname+"</h1>");
                        $("#show_general_msg").show();
                    }
                    return false;
                });
                $(document).on('ifChanged', '#chkDimension', function () {
                    if ($(this).prop("checked") == true) {
                        $("#tdDimSingle").css("display", "none");
                    } else
                    {
                        $("#tdDimSingle").css("display", "block");
                    }
                });
                $('#mawb_number').focus();
                $('#player').hide();
                $('.inputclass').keyup(function () {
                    this.value = this.value.toUpperCase();
                });

                document.getElementById('single_length').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        document.getElementById('single_width').focus();
                        event.preventDefault();
                    }
                });

                document.getElementById('single_width').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        document.getElementById('single_height').focus();
                        event.preventDefault();
                    }
                });

                document.getElementById('single_height').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {

                        if ($('#trWeightSingle').is(":visible") == false)
                        {
                            event.preventDefault();
                            btnScanSingle();
                        } else {
                            document.getElementById('weight_single').focus();
                            event.preventDefault();
                        }
                    }
                });
                /*
                 * Scenario 201 Hadi
                 */
                document.getElementById('tracking_number_single').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        CalculateWeightAuto();
                        var trackingnumber = $.trim($("#tracking_number_single").val());
                        var box_number = $.trim($("#box_number_single").val());
                        var weight = $.trim($("#weight_single").val());
                        var length = $.trim($("#single_length").val());
                        var width = $.trim($("#single_width").val());
                        var height = $.trim($("#single_height").val());
                        var hold_label = $.trim($('#hold_label:checked').val());
                        var re_scan = $.trim($('#re_scan:checked').val());
                        var status = "";
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'checkshipmentstatus', trackingnumber: trackingnumber,boxnumber: box_number,weight: weight, length: length, width: width, height: height,hold_label:hold_label,re_scan:re_scan}
                        }).done(function (data) {
                            var obj = $.parseJSON(data);
                            var message = obj.message;
                            var messageDiv = $('#message_single_scan');
                            messageDiv.hide();
                            if (obj.res == 'success') {
                                if (obj.status == <?php echo Consignment::STATUS_HOLD; ?> && obj.is_hold == "yes_hold") {
                                    $.ajax({
                                        type: "POST",
                                        url: "box_ajax.php", // your php file name
                                        data: {
                                            action: 'GetShipmentData',
                                            tracking: trackingnumber,
                                            autoprint: true,
                                            service_id_new: obj.service_id,
                                            relabel_shipment: "relabel"
                                        },
                                        success: function (data) {
                                            var obj = JSON.parse(data);
                                            if (obj.result == 'error') {
                                                show_res_msg("error", obj.message);
                                            } else {
                                                PopupCenter(obj.label,'Label',400,400);
                                            }
                                        }
                                    });
                                }else {
                                    if (obj.length > 0) {
                                        $("#scan_lenght").html("");
                                        $("#scan_lenght").html(obj.length);
                                    }
                                    if (obj.width > 0) {
                                        $("#scan_width").html("");
                                        $("#scan_width").html(obj.width);
                                    }
                                    if (obj.height > 0) {
                                        $("#scan_height").html("");
                                        $("#scan_height").html(obj.height);
                                    }
                                    if (obj.weight > 0) {
                                        $("#scan_weight").html("");
                                        $("#scan_weight").html(obj.weight);
                                    }
                                    if (obj.status == <?php echo Consignment::STATUS_HOLD; ?>) {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                        $("#show_general_msg div.alert").html("<h1>" + obj.message + "</h1>");
                                        $("#show_general_msg").show();
                                        $("#tracking_number_single").css("background-color", "red");
                                        $('#tracking_number_single').text('');
                                        setTimeout(function () {
                                            $("#tracking_number_single").val("");
                                        }, 1000);
                                        $('#tracking_number_single').focus();
                                        return false;
                                    } else if ($("#tdDimSingle").is(":visible") && $('#single_length').val().length == 0) {
                                        event.preventDefault();
                                        $('#single_length').focus();
                                    } else if ($('#trWeightSingle').is(":visible") && $('#weight_single').val().length == 0) {
                                        event.preventDefault();
                                        $('#weight_single').focus();
                                    } else {
                                        btnScanSingle();
                                    }
                                }
                            } else if (obj.mawb == 'error') {
                                console.log("checkshipmentstatus");
                                $(".btn_save_mawb").click();
                            } else if (obj.res == 'error') {
                                showErrorMessage(obj.message,"error");
                            }
                        }, 'json');

                        $.ajax({
                            type: 'get',
                            url: 'http://127.0.0.1:3654',
                            dataType: 'jsonp', // Using Cross-Origin Resource Sharing
                            jsonpCallback: 'callback',
                            success: function(data) {
                                var strWeight = data.weight;
                                var weight = strWeight.replace('kg', '');
                                var strLength = data.length;
                                var length = strLength.replace("cm", '');
                                var strWidth = data.width;
                                var width = strWidth.replace("cm", '');
                                var strHeight = data.height;
                                var height = strHeight.replace("cm", '');

                                document.getElementById('single_length').value = length;
                                document.getElementById('single_width').value = width;
                                document.getElementById('single_height').value = height;
                                document.getElementById('weight_single').value = weight;
                            }, error: function(xhr, status, err) {
                                console.log('Device not connected please connect and then try again');
                                // showErrorMessage("Device not connected please connect and then try again","error");
                            }
                        });
                    }
                });
                document.getElementById('box_multiple').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        var boxnumber = $.trim($('#box_multiple').val());
                        if ($.trim($("#box_multiple").val()).length == 0)
                        {
                            $('#message_multiple').addClass("alert-danger");
                            $('#message_multiple').show().html('Please Enter the Reference Number');
                            $("#divBagNumber").addClass("has-error has-danger");
                            $('html, body').animate({
                                scrollTop: $("#multiple_shipment_scan").offset().top
                            }, 1000);
                            return false;
                        } else {
                            $("#divBagNumber").removeClass("has-error has-danger");
                            $("#message_multiple").removeClass("alert-danger");
                            $("#message_multiple").html('');
                        }
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'getTrackingNumbersByBagNumber', boxnumber: boxnumber},
                            success: function (data)
                            {
                                var obj = JSON.parse(data);
                                boxTrackingNumbersArray = obj.trackingNumbers;
                                matchTrackingNumbers = [];
                                refreshScanTable();
                            }
                        });
                    }
                });

                document.getElementById('mawb_number').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13) {
                        var mawb_number = $("#mawb_number").val();
                        if (mawb_number != "") {
                            $.ajax({
                                type: "POST",
                                dataType: "html",
                                url: "box_ajax.php", // your php file name
                                data: {action: 'get_mawb_detail', mawb_number: mawb_number},
                                success: function (data)
                                {
                                    if (data != "") {
                                        $("#apend_mawb_data").html("");
                                        $("#apend_mawb_data").html(data);
                                    } else {
                                        $("#mawb_model").modal("show");
                                        $("#show_general_msg_mawb div.alert").html(" ");
                                        $("#show_general_msg_mawb").hide();
                                        $("#flight_number").val("");
                                        $("#number_of_pieces").val("");
                                        $("#weight_mawb").val("");
                                        $("#status_mawb").val("");
                                    }
                                }
                            });
                        }
                        event.preventDefault();
                        $('#box_weight').focus();
                    }
                });

                document.getElementById('box_weight').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        $('#box_number').focus();
                    }
                });

                document.getElementById('mawb_multiple').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        var manifestid = $("#mawb_multiple").val();
                        if ($.trim($("#mawb_multiple").val()).length == 0)
                        {
                            $('#message_multiple').addClass("alert-danger");
                            $('#message_multiple').show().html('<?php echo Translation::GetCaption("MSG_PLEASE_ENTER_MAWB_NUMBER") ?>');
                            $("#mawb_number").focus();
                            $("#divMawb").addClass("has-error has-danger");
                            $('html, body').animate({
                                scrollTop: $("#multiple_shipment_scan").offset().top
                            }, 1000);
                            return false;
                        } else {
                            $("#divMawb").removeClass("has-error has-danger");
                            $("#message_multiple").removeClass("alert-danger");
                            $("#message_multiple").html('');
                        }
                        event.preventDefault();
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'getTrackingNumbersByManifestNumber', manifestid: manifestid},
                            success: function (data)
                            {
                                var obj = JSON.parse(data);
                                boxTrackingNumbersArray = obj.trackingNumbers;
                                matchTrackingNumbers = [];
                                refreshScanTable();
                            }
                        });
                        $('#box_multiple').focus();
                    }
                });

                document.getElementById('box_multiple').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        $('#barcodelist').focus();
                    }
                });
        // Multishipment scan handle on parcel id
                document.getElementById('barcodelist').addEventListener('keypress', function (event) {
                    if (event.keyCode == 13)
                    {
                        var arr = $("#barcodelist").val().split("\n").filter(Boolean);
                        outBoundTrackingNumbersList = arr;
                        var lastScanned = arr[arr.length - 1];
                        var multishipmentOrder = $('input[name=pre_sort]:checked', '#MultipleShipmentScan').val();
                        console.log(multishipmentOrder);
                        if (multishipmentOrder == "scan_outbound")
                        {
                            var service = $("#search_Code").val();
                            if (jQuery.isEmptyObject(service)) {
                                swal("","Please select service", "error");
                                $('#barcodelist').val('');
                                arr[arr.length - 1] = "";
                                $('#barcodelist').val(arr.join("\n"));
                            }else{
                                checkShipmentService(service, arr, lastScanned);
                                checkTotalWeight(arr);
                            }
                        }
                        if (multishipmentOrder == "pre_sort")
                        {
                            var hubId = $("#presort_carrier_hubs option:selected").val();
                            var preSortCarrierId = $("#presort_carrier_group option:selected").val();
                            checkRoutingHub(lastScanned, hubId, preSortCarrierId, arr);
                        }
                        if (boxTrackingNumbersArray.length > 0)
                        {

                            var arr = $("#barcodelist").val().split("\n").filter(Boolean);
                            var el = arr[arr.length - 1];
                            el = el.replace("JJD", "JD");
                            var elArray = (el.split(''));
                            if (elArray[0] == '%')
                            {
                                el = el.substring(8, elArray.length - 6);

                            }
                            if (jQuery.inArray(el, boxTrackingNumbersArray) == -1)
                            {
                                //speak("not found");

                            } else if (jQuery.inArray(el, matchTrackingNumbers) == -1)
                            {		
                                matchTrackingNumbers.push(el);
                            } else
                            {
                                
                            }
                            refreshScanTable();
                        }
                    }
                });
                // check shipment service
                function checkShipmentService(service, arr, trackingNumber) {
                    var source_country_id = $("#source_country_id").val();
                    var destination_country_id = $("#destination_country_id").val();
                    if(source_country_id == "" || destination_country_id == ""){
                        swal("","please select source country and destination country", "error");
                        $('#barcodelist').val('');
                        arr[arr.length - 1] = "";
                        $('#barcodelist').val(arr.join("\n"));
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'CheckShipmentService', trackingNumber: trackingNumber, serviceName: service},
                            success: function (data)
                            {
                                var obj = JSON.parse(data);

                                if (obj.status == 0)
                                {
                                    $('#barcodelist').val('');
                                    arr[arr.length - 1] = "";
                                    $('#barcodelist').val(arr.join("\n"));
                                    $("#msg").text(obj.message);
                                    $("#dialogAlert").modal('show');
                                }
                            }
                        });
                    }
                }
                var outBoundTrackingNumbersList = [];
                $('#loadingDiv')
                        .hide()  // Hide it initially
                        .ajaxStart(function () {
                            $(this).show();
                        })
                        .ajaxStop(function () {
                            $(this).hide();
                        })
                        ;

                $("#btnCloseClaim").click(function (e) {
                    $("#overlay").hide();
                    $("#dialogAlertClaim").hide();
                    e.preventDefault();
                });

                $("#btnCloseReturn").click(function (e) {
                    $("#overlay").hide();
                    $("#dialogReturnManualData").hide();
                    e.preventDefault();
                });

                $("#btnClose, #btnOk, #btnCloseAlert").click(function (e) {
                    $("#overlay").hide();
                    //$("#dialogAlert").hide();	
                    e.preventDefault();
                });

                $("#btnCloseAlertYesNo, #btnNo").click(function (e) {
                    $("#overlay").hide();
                    $("#dialogAlertYesNo").hide();
                    e.preventDefault();
                });

                $("#btnCreatePalletLabel").click(function () {
                    var palletno = $.trim($('#palletnumber').val());
                    if (palletno != '') {
                        if (active) {
                            request.abort();
                        }
                        active = true;
                        request = $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'createpalletlabel', palletnumber: palletno},
                            success: function (data)
                            {
                                var obj = $.parseJSON(data);
                                if (obj.result == 'success')
                                {
                                    show_res_msg("success","<a href='" + obj.label + "' target='_blank'><h1>Download Pallet Label </h1></a>");
                                    PopupCenter(obj.label,'Label',400,400);
                                }else{
                                    showErrorMessage(obj.message,"error");
                                }
                                active = false;
                            }
                        });
                        $("#btnSavePallet").hide();
                        $("#btnClosePallet").show();
                    } else {
                        showErrorMessage("Please enter pallet number","error");
                    }
                });
                $("#btnDispatchPallet").click(function () {                   
                    $('#DispatpalletForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else {
                            $("#overlay").show();
                            Dispatch();
                        }
                    });
                    $("#DispatpalletForm").submit();
                });

                $("#btnClosePallet").click(function () {
                    var palletno = $('#palletnumber').val();
                    var carrier_hubs = $('#carrier_hubs').val();
                    swal({
                        title: "Are you sure you want to close the pallet?",
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
                                    $.blockUI();
                                    $.ajax({
                                        type: "POST",
                                        url: "box_ajax.php", // your php file name
                                        data: {action: 'closepallet', palletnumber: palletno, carrier_hubs: carrier_hubs},
                                        success: function (data)
                                        {
                                            $.unblockUI();
                                            try {
                                                var obj = JSON.parse(data);
                                                if(obj.result == 'error'){
                                                    showErrorMessage(obj.message,"error");
                                                }
                                            } catch(error) {
                                                show_res_msg("success","<a href='" + data + "' target='_blank'><h1>Download Pallet Label </h1></a>");
                                                PopupCenter(data,'Label',400,400);
                                                $("#show_general_msg").show();
                                                $("#trScanPalletBox").hide();
                                                $("#btnSavePallet").show();
                                                $("#btnClosePallet").hide();
                                                $('#palletnumber').val("");
                                                $('#palletBoxNumber').val("");

                                                $('#mawb_number').val("");
                                                $('#message').text("");
                                                $('#box_weight').val("");
                                                $('#box_number').val("");

                                                $('#mawb_number').css('background-color', '#E0E0E0');
                                                $('#box_weight').css('background-color', '#E0E0E0');
                                                $('#box_number').css('background-color', '#E0E0E0');

                                                $("#mawb_number").attr("disabled", "disabled");
                                                $("#box_weight").attr("disabled", "disabled");
                                                $('#box_number').css('disabled', 'disabled');
                                            }
                                        }
                                    });
                                }
                            });
                });

                $("#btn_close_session").click(function () {

                    var selectedIndex = parseInt($('#service_type')[0].selectedIndex);
                    var result = confirm('Are you sure you want to close the session?');

                    if (result)
                    {
                        $('#close_sessionForm').validator().on('submit', function (e) {
                            if (e.isDefaultPrevented())
                            {
                                return false;
                            } else {
                                $("#form_action").val("close_session");
                                if (active) {
                                    request.abort();
                                }
                                active = true;
                                request = $.ajax({

                                    method: "POST",
                                    url: "bagscan.php",
                                    data: $('#close_sessionForm').serialize()
                                }).done(function (data) {
                                    active = false;
                                });
                            }
                        });
                        $("#close_sessionForm").submit();
                    } else
                    {
                        return false;
                    }

                });

                $('#btnSendMultiDataNo').click(function (e) {
                    $('#dialogAlertNotFoundItems').hide();
                    $('#overlay').hide();
                    return false;
                });

                $('#btnSendMultiDataYes').click(function (e) {
                    sendDataWithMissingScanItems = true;
                    MultipleShipmentScan();
                    $('#dialogAlertNotFoundItems').modal('hide');
                    return false;
                });

                $("#btnSaveWeight").click(function () {
                    $('#hidAllowWeight').val('Y');
                    btnScanSingle();
                    $("#dialogAlertSaveWeightYesNo").modal('hide');
                    $('#hidAllowWeight').val('N');
                    return false;

                });
                
                $("#rtn_trackingnumbernew").keypress(function (e) {
                    if (e.keyCode == 13)
                    {
                        $("#overlay").show();
                        var returnTrackingnumber = $.trim($("#rtn_trackingnumber").val());
                        var returnTrackingNumberNew = $.trim($("#rtn_trackingnumbernew").val());
                        var request =
                                $.ajax({
                                    type: "POST",
                                    url: "box_ajax.php", // your php file name
                                    data: {trackingno: returnTrackingnumber,
                                        trackingnonew: returnTrackingNumberNew,
                                        action: 'savereturn'},
                                    success: function (data)
                                    {
                                        var obj = JSON.parse(data);
                                        if (obj.result == 'success')
                                        {
                                            $('#successdiv').html(obj.message);
                                            $('#rtn_trackingnumbernew').css("background-color", "springgreen");
                                            newwindow = window.open(obj.label, 'Label', 'height=400,width=400');
                                            if (window.focus) {
                                                newwindow.focus()
                                            }
                                            setTimeout(function ()
                                            {
                                                $('#rtn_trackingnumbernew').css("background-color", "#FFC");
                                                $('#rtn_trackingnumbernew').val("");
                                                $('#rtn_trackingnumber').val("");
                                                $('#rtn_trackingnumber').focus();
                                            }, 1000);
                                        } else {
                                            $('#successdiv').css("color", "red");
                                            $('#successdiv').html(obj.message);
                                            $('#successdiv').show();

                                            $('#rtn_trackingnumber').css("background-color", "red");
                                            $('#rtn_trackingnumbernew').css("background-color", "red");

                                            setTimeout(function ()
                                            {
                                                $('#rtn_trackingnumber').css("background-color", "#FFC");
                                                $('#rtn_trackingnumbernew').css("background-color", "#FFC");
                                                $('#rtn_trackingnumbernew').val("");
                                                $('#rtn_trackingnumber').val("");
                                                $('#rtn_trackingnumber').focus();
                                            }, 1000);
                                        }
                                    }
                                });
                    }
                });

                $("#btnMakeClaim").click(function () {
                    var account = $("#account option:selected").val();
                    var conid = $("#hidConId").val();
                    if (account == '')
                    {
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("<h1>Please select account</h1>");
                        $("#show_general_msg").show();
                        return false;
                    }
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php", // your php file name
                        data: {conid: conid,
                            account: account,
                            action: 'makeclaim'
                        },
                        success: function (data)
                        {
                            var obj = JSON.parse(data);
                            if (obj.result == 'success')
                            {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                $("#show_general_msg div.alert").html("<h1>Claim has been made</h1>");
                                $("#show_general_msg").show();
                            }
                            $("#overlay").hide();
                            $("#dialogAlertClaim").hide();
                            LoadAwaitingClaims();
                        }
                    });
                });

                $("#btnSaveReturn").click(function () {

                    var handling = $('#service_type option:selected').val();
                    var awb = $.trim($('#awb').val());
                    var return_awb = $.trim($('#return_awb').val());
                    var company = $.trim($('#company').val());
                    var contact = $.trim($('#contact').val());
                    var addressline1 = $.trim($('#address_line_1').val());
                    var addressline2 = $.trim($('#address_line_2').val());
                    var addressline3 = $.trim($('#address_line_3').val());
                    var city = $.trim($('#city').val());
                    var postcode = $.trim($('#postcode').val());
                    var country = $('#country option:selected').val();
                    var telephone = $.trim($('#telephone').val());
                    var weight = $.trim($('#weight').val());
                    if (handling == '')
                    {
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("<h1>Please select service</h1>");
                        $("#show_general_msg").show();
                        return false;
                    }
                    if (awb == '')
                    {
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("<h1>Please enter tracking number</h1>");
                        $("#show_general_msg").show();
                        return false;
                    }
                    if (addressline1 == '')
                    {
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("<h1>Please enter address line 1</h1>");
                        $("#show_general_msg").show();
                        return false;
                    }
                    if (city == '')
                    {
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("<h1>Please enter city</h1>");
                        $("#show_general_msg").show();
                        return false;
                    }
                    $("#saveReturnDiv").css("display", "block");
                    $.ajax({
                        type: "POST",
                        url: "../main/return_ajax.php", // your php file name
                        data: {handling: handling,
                            awb: awb,
                            return_awb: return_awb,
                            company: company,
                            contact: contact,
                            addressline1: addressline1,
                            addressline2: addressline2,
                            addressline3: addressline2,
                            city: city,
                            country: country,
                            postcode: postcode,
                            telephone: telephone,
                            weight: weight,
                            action: 'saveformdata'
                        },
                        success: function (data)
                        {
                            $("#saveReturnDiv").css("display", "none");
                            var obj = JSON.parse(data);
                            if (obj.result == 'success')
                            {
                                $("#dialogReturnManualData").hide();
                                $("#overlay").hide();
                                if (obj.label != '')
                                {
                                    var msg = "Tracking information saved.";
                                    $('#successdiv').css("color", "springgreen");
                                    $('#successdiv').show();
                                    $('#successdiv').html(msg);

                                    $("#rtn_trackingnumber").val('');
                                    $("#awb").val('');
                                    $("#return_awb").val('');

                                    newwindow = window.open(obj.label, 'Label', 'height=400,width=400');
                                    if (window.focus) {
                                        newwindow.focus()
                                    }
                                }
                            }
                        }
                    });
                });

                $("#rtn_trackingnumber").keypress(function (e) {
                    if (e.keyCode == 13)
                    {
                        var returnTrackingNumber = $.trim($("#rtn_trackingnumber").val());
                        if (returnTrackingNumber == '')
                        {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("<h1>Please enter tracking number</h1>");
                            $("#show_general_msg").show();
                            return false;
                        }
                        var request =
                                $.ajax({
                                    type: "POST",
                                    url: "../main/return_ajax.php", // your php file name
                                    data: {trackingno: returnTrackingNumber, action: 'returnaccount'},
                                    success: function (data)
                                    {
                                        if (data != '')
                                        {
                                            //$('#successdiv').css("color","green");
                                            account = "Account:   " + data;
                                            //$('#successdiv').show();
                                            //$('#successdiv').html(account); 							
                                            $('#rtn_trackingnumber').css("background-color", "springgreen");
                                            setTimeout(function ()
                                            {
                                                $('#rtn_trackingnumber').css("background-color", "#FFC");
                                                $('#rtn_trackingnumbernew').focus();
                                            }, 1000);
                                        } else
                                        {
                                            var msg = "Tracking number doesn't exist.";
                                            $('#successdiv').css("color", "red");
                                            $('#successdiv').show();
                                            $('#successdiv').html(msg);
                                            $('#rtn_trackingnumber').css("background-color", "red");
                                            setTimeout(function ()
                                            {
                                                $('#rtn_trackingnumber').css("background-color", "#FFC");
                                                $('#rtn_trackingnumber').focus();
                                            }, 1000);
                                            var awb = $('#awb').val('');
                                            var return_awb = $('#return_awb').val('');
                                            var company = $('#company').val('');
                                            var contact = $('#contact').val('');
                                            var addressline1 = $('#address_line_1').val('');
                                            var addressline2 = $('#address_line_2').val('');
                                            var addressline3 = $('#address_line_3').val('');
                                            var city = $('#city').val('');
                                            var postcode = $('#postcode').val('');
                                            //var country = $('#country option:selected').val();
                                            var telephone = $('#telephone').val('');
                                            var weight = $('#weight').val('');
                                            $("#overlay").show();
                                            $("#dialogReturnManualData").show();
                                        }
                                    }
                                });
                    }
                });

                $("#palletnumber").keypress(function (e) {
                    if (e.keyCode == 13)
                    {
                        //$('#loadingDivPallet').css('display','block');				
                        var palletno = $('#palletnumber').val();
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'checkpalletstatus', palletnumber: palletno},
                            success: function (data)
                            {
                                var obj = JSON.parse(data);
                                if (obj.result == 'success')
                                {
                                    showErrorMessage(obj.message,'info');
                                    $("#mawb_number, #box_weight, #box_number").removeAttr('disabled');
                                    $("#mawb_number, #box_weight, #box_number").css('background-color', '#FFC');
                                    $("#btnClosePallet").show();
                                    $("#btnCreatePalletLabel").show();
                                    $("#btnSavePallet").hide();
                                }else if (obj.result == 'dispatch') {
                                    showErrorMessage(obj.message,'info');
                                    $("#mawb_number, #box_weight, #box_number").prop('disabled', true);
                                    $("#mawb_number, #box_weight, #box_number").css('background-color', '#FFC');
                                    $("#btnClosePallet").hide();
                                    $("#btnCreatePalletLabel").hide();
                                    $("#btnSavePallet").show();
                                    $("#palletnumber").css("background-color", "red");
                                    setTimeout(function () {
                                        $('#palletnumber').focus();
                                        $('#palletnumber').css("background-color", "white");
                                        $('#palletnumber').val("");

                                    }, 3000);
                                } else {
                                       swal({
                                            title: obj.message,
                                            text: "",
                                            type: "warning",
                                            html:true,
                                            customClass: "swal-large",
                                            showCancelButton: true,
                                            confirmButtonClass: "btn-danger",
                                            confirmButtonText: "Dispatch",
                                            cancelButtonText: "Yes",
                                            closeOnConfirm: true,
                                            closeOnCancel: true,
                                            allowOutsideClick: true
                                    },
                                    function(isConfirm) {
                                        var palletno = $('#palletnumber').val();
                                        if (isConfirm) {
                                            // Send ajax for dispatch pallet
                                            $.ajax({
                                                type: "POST",
                                                url: "box_ajax.php", // your php file name
                                                data: {action: 'dispatch', palletnumber: palletno},
                                                success: function (data)
                                                {
                                                    var obj = JSON.parse(data);
                                                    if(obj.result == 'success'){
                                                        $("#show_general_msg div.alert").html(" ");
                                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                                        $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                                        $("#show_general_msg").show();
                                                    }else{
                                                        $("#show_general_msg div.alert").html(" ");
                                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                                        $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                                        $("#show_general_msg").show();
                                                    }
                                                }
                                            });
                                        }else{
                                            // Send ajax for reopen pallet
                                            $.ajax({
                                                type: "POST",
                                                url: "box_ajax.php", // your php file name
                                                data: {action: 'openpallet', palletnumber: palletno},
                                                success: function (data)
                                                {
                                                    var obj = JSON.parse(data);
                                                    if(obj.result == 'success'){
                                                        $("#show_general_msg div.alert").html(" ");
                                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                                        $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                                        $("#show_general_msg").show();
                                                    }else{
                                                        $("#show_general_msg div.alert").html(" ");
                                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                                        $("#show_general_msg div.alert").html("<h1>"+obj.message+"</h1>");
                                                        $("#show_general_msg").show();
                                                    }
                                                    $("#btnClosePallet").show();
                                                    $("#btnCreatePalletLabel").show();
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        });
                        return false;
                    }
                    //return false;
                });

                $("#manifest_number").keypress(function (e) {
                    if (e.keyCode == 13)
                    {
                        if ($.trim($("#manifest_number").val()).length == 0)
                        {
                            $('#message_scanmanifest').addClass("alert-danger");
                            $('#message_scanmanifest').show().html('Please Enter Manifest Number');
                            $("#manifest_form_div").addClass("has-error has-danger");
                            return false;
                        }
                        $("#manifest_form_div").removeClass("has-error has-danger");
                        $("#message_scanmanifest").removeClass("alert-danger");
                        $('#loadingDivManifest').css('display', 'block');
                        var manifestnumber = $.trim($('#manifest_number').val());
                        $("#message_scanmanifest").html('');
                        $.blockUI();
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'scan_manifest', manifestnumber: manifestnumber},
                            success: function (data)
                            {
                                $.unblockUI();
                                var obj = JSON.parse(data);
                                if (obj.result == 'error')
                                {
                                    showErrorMessage(obj.message,"error");
                                    $("#manifest_number").css("background-color", "red");
                                } else {
                                    showErrorMessage("Total Number Of Shipments : "+obj.total_shipments+"<br/> Total Weight :"+obj.total_weight,"info");
                                    show_res_msg("success",obj.message);
                                }

                                setTimeout(function () {

                                    $('#manifest_number').focus();
                                    $('#manifest_number').css("background-color", "white");
                                    $('#manifest_number').val("");

                                }, 3000);
                            }
                        });
                        return false;
                    }
                });

                $("#palletBoxNumber").keypress(function (e) {
                    if (e.keyCode == 13)
                    {
                        $('#loadingDivPallet').css('display', 'block');
                        var palletno = $('#palletnumber').val();
                        var boxnumber = $('#palletBoxNumber').val();
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'scancartoninpallet', boxnumber: boxnumber, palletnumber: palletno},
                            success: function (data)
                            {
                                $('#loadingDivPallet').css('display', 'none');
                                var obj = JSON.parse(data);
                                if (obj.result == 'error')
                                {
                                    $("#overlay").show();
                                    //$("#dialogAlert").show();
                                    $("#msg").text(obj.message);
                                    $('#palletMsg').addClass('alert-danger');
                                    $('#palletMsg').html(obj.message);
                                } else {
                                    if (obj.carrier != '')
                                    {
                                        $('#palletCarrier').css('color', 'green');
                                        $('#palletCarrier').html(obj.carrier);
                                    }
                                    $('#palletMsg').addClass('alert-success');
                                    $('#palletMsg').html(obj.message);
                                    $('#palletBoxNumber').val("");
                                    setTimeout(function () {
                                        $('#palletMsg').html("");
                                    }, 3000);
                                }
                            }
                        });
                        return false;
                    }
                });

                $("#btnSavePallet").click(function () {
                    // Open Modal to Save Pallet
                    $("#save_pallet_modal").modal("show");
                    
                    
                    
//                    if ($("#pallet_carrier_group").val() == "") {
//                        show_res_msg("error","<h1>Pallet select pallet carrier group.</h1>");
//                    } else {
//                        var palletCarrierGroupId = $('#pallet_carrier_group').val();
//                        var palletHubsId = $('#carrier_hubs').val();
//                        if (active) {
//                            request.abort();
//                        }
//                        active = true;
//                        $.blockUI();
//                        request = $.ajax({
//                            type: "POST",
//                            url: "box_ajax.php", // your php file name
//                            data: {action: 'createpallet', carrier: palletCarrierGroupId, carrier_hub: palletHubsId},
//                            success: function (data)
//                            {
//                                $.unblockUI();
//                                try {
//                                    var obj = JSON.parse(data);
//                                    if(obj.result == 'error'){
//                                        showErrorMessage(obj.message,"error");
//                                    }
//                                } catch(error) {
//                                    $('#palletnumber').val(data);
//                                    $("#trScanPalletBox").show();
//                                    $("#btnSavePallet").hide();
//                                    $("#btnClosePallet").show();
//                                    $("#btnCreatePalletLabel").show();
//                                    show_res_msg("success","<h1>Pallet number created successfully. Please start scanning the box number.</h1>","success");
//                                    $("#palletnumber").css('background-color', 'springgreen');
//
//                                    $("#mawb_number, #box_weight, #box_number").removeAttr('disabled');
//                                    $("#mawb_number, #box_weight, #box_number").css('background-color', '#FFC');
//                                    active = false;
//                                } 
//                            }
//                        });
//                        $("#createpalletForm").submit();
//                    }
                });

                $("#btnSaveManifest").click(function ()
                {
                    var SendDataCheck = false;
                    if($("#chkSendData").is(":checked")){
                        SendDataCheck = true;
                        $("#frm_parcel_data").val("send");
                    }else{
                        $("#frm_parcel_data").val("");
                    }
                    if ($('#pieces').val() == '')
                    {
                        $('#manifest_message').addClass('alert-danger');
                        $('#manifest_message').html('Please Enter Number of pieces');
                        $(this).closest('#myModal').animate({
                            scrollTop: $("#manifest_message").offset().top
                        }, 1000);
                        return false;
                    }
                    if ($('#weight').val() == '')
                    {
                        $('#manifest_message').addClass('alert-danger');
                        $('#manifest_message').html('Please Enter Weight');
                        $(this).closest('#myModal').animate({
                            scrollTop: $("#manifest_message").offset().top
                        }, 1000);
                        return false;
                    }
                    if ($('#pieces').val() == '0')
                    {
                        $('#manifest_message').addClass('alert-danger');
                        $('#manifest_message').html('Please Select one of the pallet');
                        $(this).closest('#myModal').animate({
                            scrollTop: $("#manifest_message").offset().top
                        }, 1000);
                        return false;
                    }
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php", // your php file name
                        dataType: "json",
                        data: $('#savemanifestForm').serialize()
                    }).done(function (data) {
                        $.unblockUI();
                        if(data.status == "success"){
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html("<h1>"+data.message+"</h1>");
                            $("#show_general_msg").show();
                            LoadEndOfDay();
                            $("#myModal").modal("hide");
                        }else{
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("<h1>"+data.message+"</h1>");
                            $("#show_general_msg").show();
                            $("#myModal").modal("hide");
                        }
                        $('html, body').animate({
                            scrollTop: $("#manifest_message").offset().top
                        }, 1000);
                        setTimeout(function () {
                            $('#manifest_message').show().fadeTo(3000, 1000).slideUp(1000);
                            $('#btnCloseModal').click();
                        }, 3000);
                        $('#dispatch_fun_modal').modal('hide');
                    });
                    setTimeout(function () {
                        $('#manifest_message').html("");
                        $('#manifest_message').removeClass('alert-success');
                    }, 3000);
                });
                $('#date_dispatch').datetimepicker({format: 'yyyy-mm-dd hh:ii'});
                $(document).on('click', '#btnSaveMawb', function () {
                    if ($("#flight_number").val() == "") {
                        $("#show_general_msg_mawb div.alert").html(" ");
                        $("#show_general_msg_mawb div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg_mawb div.alert").html("Please enter flight number");
                        $("#show_general_msg_mawb").show();
                    } else {
                        var mawb = $("#mawb_number").val();
                        $("#mawb_new").val(mawb);
                        $.ajax({
                            method: "POST",
                            url: "bagscan.php",
                            data: $('#saveMawbForm').serialize()
                        }).done(function (data) {
                            if (data == "success") {
                                $("#mawb_model").modal("hide");
                            } else {
                                $("#show_general_msg_mawb div.alert").html(" ");
                                $("#show_general_msg_mawb div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg_mawb div.alert").html(data);
                                $("#show_general_msg_mawb").show();
                            }
                        });
                    }
                });
//                initilize_tags_input();
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                })
            });
            $(document).on('ifChanged', '.dispatch_email_chk', function () {
                var checkedTotal = 0;
                var checkedName = 0;
                var manifestId = 0;
                var agentId = 0;
                var serviceId = 0;
                if ($(this).is(":checked")) {
                    var agentId = $("#con_dispatch_agent").val();
                    var serviceId = $("#con_service").val();
                    checkedTotal = $('.dispatch_email_chk:checkbox:checked').length;
                    checkedName = $('.dispatch_email_chk:checkbox:checked').attr("name");
                    manifestId = $("#disptach_frm #manifest_id").val();
                    $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            dataType: "html",
                            data: {action: 'get_manifest_email', manifest_id: manifestId,checked_total:checkedTotal,checked_name:checkedName,agent_id:agentId,service_id:serviceId},
                    }).done(function (data) {
                            var returnData = data.split(':::');
                            if((checkedTotal == 2) || checkedName=="carrier"){
                                $("#manifest_carrier_email").val(returnData[0]);
                            }
                            if((checkedTotal == 2) || checkedName=="agent"){
                                $("#agent_carrier_email").val(returnData[1]);
                            }
                            $("#carrier_email_subject").val(returnData[2]);
                            $("#carrier_email_content").val(returnData[3]);
                            $("#send_manifest_email_modal").modal("show");
                    });
                } else {
                    
                }
//                $.ajax({
//                        type: "POST",
//                        url: "box_ajax.php", // your php file name
//                        dataType: "html",
//                        data: {action: 'get_manifest_email', manifest_id: manifest_id},
//                    }).done(function (data) {
//                        var returnData = data.split(':::');
//                        $("#manifest_carrier_email").val(returnData[0]);
//                        $("#agent_carrier_email").val(returnData[1]);
//                        $("#carrier_email_subject").val(returnData[2]);
//                        $("#carrier_email_content").val(returnData[3]);
//                });
            });
            $(document).on('click', '.dispatch_manifest_btn', function () {
                $("#manifest_id").val($(this).data("mani_id"));
                $("#con_dispatch_agent").val("");
                var select2dis = $("#con_dispatch_agent").select2();
                $("#con_dispatch_agent").trigger("change");
                var select2ser = $("#con_service").select2();
                $("#con_weight").val("");
                $("#con_master_no").val("");
                $("#con_value").val("");
                $("#con_no_item").val("");
                $("#show_address_div").html("");
                $("#dispatch_modal_div").hide();
                $("#con_currency").val("");
                var select2cur = $("#con_currency").select2();
                $("#dispatch_fun_modal").modal("show");
            });

            $(document).on('click', '#btn_show_address', function () {
                if(!$.fn.DataTable.isDataTable('#manage-data-table')){
                    DataTableFun.init();
                }
                $("#show_address").modal("show");
                $('select[name=manage-data-table_length]').val(20);
                $(".pagination-panel-input").val("1");
                $('select[name=manage-data-table_length]').trigger("change");
                $("#show_address #con_address_1").val('');
                $("#show_address #con_address_2").val('');
                $("#show_address #con_address_3").val('');
                $("#show_address #con_postcode").val('');
                $("#show_address #con_city").val('');
                $("#show_address #con_country").val('').change();
            });
            $(document).on('click', '#btn_send_manifest_email', function () {
                var form_data = $("#send_manifest_email").serialize();
                $.ajax({
                    type: "POST",
                    url: "bagscan.php", // your php file name
                    dataType: "json",
                    data: form_data,
                }).done(function (data) {
                    if (data.result == 'success') {
                        $("#send_manifest_email_modal").modal("hide");
                        LoadEndOfDay();
                    }
                });
            });
            $(document).on('click', '.email_manifest_btn', function () {
                var manifest_id = 0;
                var manifest_id = $(this).data("mani_id");
                $("#manifest_id_send_manifest_email").val(manifest_id);
                //Send ajax request for agent and carrier email address
                $.ajax({
                        type: "POST",
                        url: "box_ajax.php", // your php file name
                        dataType: "html",
                        data: {action: 'get_manifest_email', manifest_id: manifest_id,get_by_manifest_id:'true'},
                    }).done(function (data) {
                        var returnData = data.split(':::');
                        $("#manifest_carrier_email").val(returnData[0]);
                        $("#agent_carrier_email").val(returnData[1]);
                        $("#carrier_email_subject").val(returnData[2]);
                        $("#carrier_email_content").val(returnData[3]);
                });
                    
                    
                $("#send_manifest_email_modal").modal("show");
            });

            $(document).on('click', '.delete_manifest_btn', function () {
                var that = $(this);
                var manifest_id = that.data("mani_id");
                swal({
                        title: "ARE YOU SURE YOU WANT TO DELETE MANIFEST?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "box_ajax.php", // your php file name
                                dataType: "html",
                                data: {action: 'delete_manifest', manifest_id: manifest_id},
                            }).done(function (data) {
                                data = JSON.parse(data);
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                $("#show_general_msg div.alert").html("<h1>"+data.message+"</h1>");
                                $("#show_general_msg").show();
                                LoadEndOfDay();
                            });
                        }
                    });
            });

            $("#get_bag_detail").click(function () {
                var pallet_number = 0;
                pallet_number = $("#palletnumber").val();
                if (pallet_number == 0) {
                    swal("", "Please enter valid pallet number", "info");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "box_ajax.php", // your php file name
                        dataType: "html",
                        data: {action: 'get_bag_detail', pallet_number: pallet_number},
                    }).done(function (data) {
                        $("#apend_bag_detail_id").html("");
                        $("#apend_bag_detail_id").append(data);
                        $("#get_bag_detail_modal").modal("show");
                    });
                }
            });
            $(document).on('click', '#btnSavePalletRemoveReason', function () {
                var form_data = $("#palletBagRemoveReason").serialize();
                $.ajax({
                    type: "POST",
                    url: "bagscan.php", // your php file name
                    dataType: "json",
                    data: form_data,
                }).done(function (data) {
                    if (data.result == 'success') {
                        $("#show_pallet_msg div.alert").html(" ");
                        $("#show_pallet_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                        $("#show_pallet_msg div.alert").html(data.message);
                        $("#show_pallet_msg").show();
                        $("#pallet_bag_remove_reason_modal").modal("hide");
                        $("#get_bag_detail_modal").modal("hide");
                    }
                });    
            });
            $(document).on('click', '.remove_bag', function () {
                var entity_mapping_id = 0;
                var bag_id = 0;
                var pallet_id = 0;
                entity_mapping_id = $(this).data("entity_mapping_id");
                pallet_id = $(this).data("pallet_id");
                bag_id = $(this).data("bag_id");
                var my_this = $(this);
                //Add pallet remove bag reason here
                $("#pallet_bag_remove_reason_modal #bag_remove_pallet_id").val(pallet_id);
                $("#pallet_bag_remove_reason_modal #bag_remove_bag_id").val(bag_id);
                $("#pallet_bag_remove_reason_modal #bag_remove_entity_id").val(entity_mapping_id);
                $("#pallet_bag_remove_reason_modal").modal("show");
            });
        </script>

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
                                "url": "bagscan.php?action=address_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "company"},
                                {"data": "contact"},
                                {"data": "address_line_1", "bSortable": false},
                                {"data": "city"},
                                {"data": "country"},
                                {"data": "postcode"},
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
                $(document).on('click', '#btnSaveAddress', function () {
                    var address = '';
                    address = '<div class="mt-comments"><div class="mt-comment"><div class="mt-comment-body"><div class="mt-comment-info"><span class="mt-comment-author">Address</span></div><div class="mt-comment-text">'+$("#show_address #con_address_1").val()+" "+$("#show_address #con_address_2").val()+" "+$("#show_address #con_address_3").val()+" "+$("#show_address #con_city").val()+" "+$("#show_address #con_country option:selected").text()+"<br/>"+$("#show_address #con_postcode").val()+'</div></div></div></div>';
                    var form_data = $("#saveAddressForm").serialize();
                    var country =  $("#con_country").val();
                    $.ajax({
                        url: 'bagscan.php',
                        data: form_data,
                        dataType: 'json',
                        type: 'POST',
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#country_id").val(country);
                                $('#saveAddressForm :input').clone().hide().appendTo('#disptach_frm');
                                $("#dispatch_fun_modal #show_address_div").html("");
                                $("#dispatch_fun_modal #show_address_div").html(address);
                                $("#show_address").modal("hide");
                                $('#dispatch_modal_msg').show();
                                $('#dispatch_modal_div').removeClass('alert-danger').addClass('alert-success');
                                $('#dispatch_modal_div').html(response.message);
                                grid.getDataTable().ajax.reload();
                            } else if(response.status == 'selected') {
                                $("#country_id").val(country);
                                $("#append_address").clone($("#saveAddressForm").html());
                                $('#saveAddressForm :input').clone().hide().appendTo('#disptach_frm');
                                $("#dispatch_fun_modal #show_address_div").html("");
                                $("#dispatch_fun_modal #show_address_div").html(address);
                                $("#show_address").modal("hide");
                                $('#dispatch_modal_msg').show();
                                $('#dispatch_modal_div').removeClass('alert-danger').addClass('alert-success');
                                $('#dispatch_modal_div').html(response.message);
                            }
                        }
                    });
                });
                $(document).on('click', '#btn_save_con', function () {
                    var form_data = $("#disptach_frm").serialize();
                    $.ajax({
                        url: 'bagscan.php',
                        data: form_data,
                        dataType: 'json',
                        type: 'POST',
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                if(response.label !=""){
                                    PopupCenter(response.label,'Label',400,400);
                                   $("#show_general_msg div.alert").html("<h1>"+response.message+ "</h1></br> <a href="+response.label+" target='_blank'> Consignment label</a>"); 
                                }else{
                                  $("#show_general_msg div.alert").html("<h1>"+response.message+"</h1>");
                                }
                                LoadEndOfDay();
                                $("#show_general_msg").show();
                                $("#dispatch_fun_modal").modal("hide");
                            } else if (response.mawb == "error"){
                                // Open model for create mawb number
                                $(".btn_save_mawb").click();
                            } else {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html("<h1>"+response.message+"</h1>");
                                $("#show_general_msg").show();
                                $("#dispatch_fun_modal").modal("hide");
                            }
                        }
                    });
                });
                $(document).on('click', '.btn_select', function () {
                    var address1 = "";
                    var address2 = "";
                    var address3 = "";
                    var city = "";
                    var country = "";
                    var postcode = "";
                    
                    address1 = $(this).data("address1");
                    address2 = $(this).data("address2");
                    address3 = $(this).data("address3");
                    city = $(this).data("city");
                    country = $(this).data("country");
                    postcode = $(this).data("postcode");
                    
                    $("#show_address #con_address_1").val(address1);
                    $("#show_address #con_address_2").val(address2);
                    $("#show_address #con_address_3").val(address3);
                    $("#show_address #con_city").val(city);
                    $("#show_address #con_country").val(country);
                    $("#show_address #con_country").selectpicker('refresh');
                    $("#show_address #con_postcode").val(postcode);
                    $("#show_address #con_address_1").focus();
                });
                $(document).on('change', '#con_dispatch_agent', function () {
                    var agentId = $(this).val();
                    if(agentId > 0){
                        $.ajax({
                            url: 'box_ajax.php',
                            data: {action: 'get_service_to_agent',agent_id:agentId},
                            dataType: 'html',
                            type: 'POST',
                            success: function (response) {
                                if (response != "") {
                                   $("#con_service").html(response);
                                }
                            }
                        });
                    }else{
                        $("#con_service").html("<option value=''>Please select</option>");
                    }
                });
                DataTableFunPallet.init();  
                DataTableFunMawb.init();  
            });
            function PopupCenter(pageURL, title,w,h) {
                var left = (screen.width/2)-(w/2);
                var top = (screen.height/2)-(h/2);
                var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
                if (window.focus) {
                    targetWin.focus();
                }
//                return targetWin;
            }
            function showErrorMessage(message,type){
                var swMsg = message;
                swal({
                        title: swMsg,
                        text: "",
                        type: type,
                        html:true,
                        customClass: "swal-large",
//                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Ok",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true,
                        allowOutsideClick: true
                },
                function(isConfirm) {
                if (isConfirm) {
                }
                });
//                swal("","<h1>"+message+"</h1>", type,"html:true");
            }
            function show_res_msg(type,msg,div_id=""){
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $("#show_general_msg"+div_id+" div.alert").html(" ");
                if(type == "success"){
                    $("#show_general_msg"+div_id+" div.alert").addClass('alert-success').removeClass('alert-danger');
                }else{
                    $("#show_general_msg"+div_id+" div.alert").addClass('alert-danger').removeClass('alert-success');
                }
                $("#show_general_msg"+div_id+" div.alert").html(msg);
                $("#show_general_msg"+div_id+"").show();
                setTimeout(function ()
                {
                    $("#show_general_msg"+div_id+"").hide();
                }, 7000);
            }
            function createButton(text, cb) {
                return $('<button class="confirm btn btn-lg btn-default" style="display: inline-block;">' + text + '</button>').on('click', cb);
            }
        </script>
        <?php
    }

    protected function renderBody() {
        $user = SessionManager::getUser();
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-bar-chart"></i>
                       <?php echo Translation::GetCaption("LBL_SCAN_TITLE_BAG_BOX"); ?>
                    </div>
                    <div class="tools"> 
<!--                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> -->
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row" id="show_general_msg" <?php if(empty($this->show_general_msg)){ ?> style="display: none;" <?php } ?> >
                        <div class="col-md-12">
                            <div class="alert alert-success"><?php echo $this->show_general_msg; ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tabbable-line boxless tabbable-reversed">
                                <ul class="nav nav-tabs">
                                    
                                    <li class="active">
                                        <a data-toggle="tab" href="#carton_scan">Bag Scan</a>
                                    </li>
                                    
                                    
                                    <li>
                                        <a data-toggle="tab" href="#single_shipment_scan"><?php echo Translation::GetCaption("LBL_SINGLE_SHIPMENT_SCAN") ?></a></li>
                                    
                                    <li>
                                        <a data-toggle="tab" href="#multiple_shipment_scan"><?php echo Translation::GetCaption("LBL_MULTIPLE_SHIPMENT_SCAN") ?></a>
                                    </li>
                                    
                                    <!--<li onclick="loadManifest();">-->
                                    <li>
                                        <a data-toggle="tab" href="#manifest_scan">Scan & Dispatch Manifest</a>
                                    </li>
                                    <!--                                    <li>
                                                                            <a data-toggle="tab" href="#warehouseTab"><?php echo Translation::GetCaption("LBL_DISPATCH") ?></a>
                                                                        </li>-->
                                   
                                    <li onclick="LoadEndOfDay();">
                                        <a data-toggle="tab" href="#endofday"><?php echo Translation::GetCaption("END_OF_DAY_SIMPLE") ?></a>
                                    </li>
                                   
                                </ul>
                                <div class="tab-content">
                                    
                                    <div id="carton_scan" class="tab-pane fade in active">
                                        <div class="portlet light">
<!--                                            <div class="row">
                                                <div class="col-md-12" >
                                                    <div class="alert" id="carton_scan_palletMsg" name="carton_scan_palletMsg" ></div>
                                                </div>
                                            </div>-->
<!--                                            <div class="portlet-title">
                                                <div class="caption"> <i class="glyphicon glyphicon-search"></i>
                                                    <span class="caption-subject bold uppercase"><?php echo Translation::GetCaption("LBL_CARTON_SCAN") ?></span>
                                                </div>
                                                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
                                            </div>-->
                                            <div class="portlet-body">
<!--                                                <div style="display:none; text-align:center" id="loadingDiv">
                                                    <img src="../images/ajaxload.gif" />
                                                </div>-->
<!--                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="message" class="alert-success"></div>
                                                    </div>
                                                </div>-->
                                                <div id="boxScan" class="TabbedPanelsContent"><!-- 1 -->
                                                    <?php // if ($user->getUserType() == 'warehouse') {  ?>
                                                    <form method="post" action="javascript:;" enctype="multipart/form-data" id="createpalletForm" name="createpalletForm"  role="form">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                              
                                                                
                                                                    <div class="first_form_col">
                                                                          <div class="form-group">
                                                                    <div class="has-float-label"> 
                                                                         
                                                                            <!-- User Document -->
                                                                            <?php
                                                                            echo Ddl::generateDDL('pallet_carrier_group', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="Pallet Carrier Group" data-live-search="true"  data-original-title="Pallet Carrier Group"', 'Please select', '', 'pallet_carrier_group', 'Pallet Carrier Group', '', '');
                                                                            ?>    <label>Pallet Carrier Group   <span class="red-18">*</span></label>
                                                                           

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                             
                                                                  
                                                                    <div class="first_form_col">
                                                                                <div class="form-group">
                                                                    <div class="has-float-label"> 
                                                                            <!-- User Document -->
                                                                            <select name="carrier_hubs" id="carrier_hubs" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                                                            </select>  <label>Carrier Hubs  <span class="red-18">*</span></label>
                                                                           
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label>
                                                                    <div class="input-group">
                                                                        <div class="icheck-inline">
                                                                            <label><input type="checkbox" class="icheck" id ="pre_sort_outer_label" name="pre_sort_outer_label" value="pre_sort_outer_label" data-checkbox="icheckbox_square-blue" checked="checked">Pre Sort Bag Outer Label</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                        <div class="input-icon right">
                                                                            <input name="palletnumber" id="palletnumber" value="" class="inputclass form-control inputfield_custom_style" title="" autocomplete="off"  placeholder="Enter Pallet No:" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("LBL_ENTER_PALLET") ?>" type="text">
                                                                            <?php
                                                                            $palletFilter = new PalletFilter();
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both"></div>
                                                        <div class="row" align="center">
                                                            <div class="col-md-12">
                                                                <a href="javascript:;"    class="btn_list_mawb btn btn-primary margin-bottom-5" data-toggle="modal" data-target="#mawb_list_modal" >Select MAWB</a>
                                                                <a href="javascript:;"  class="btn_save_mawb btn btn-primary margin-bottom-5"  id="trigger_from_eod">Create MAWB</a>
                                                                <a href="javascript:;"  id="btn_list_pallet"  class="btn btn-primary margin-bottom-5" data-toggle="modal" data-target="#pallet_list_modal">Select Pallet</a>
                                                                <a href="javascript:;"  id="btnSavePallet"  class="btn btn-primary btn_save margin-bottom-5"><?php echo Translation::GetCaption("LBL_CREATE_NEW_PALLET") ?></a>
                                                                <a href="javascript:;"  id="btnCreatePalletLabel"  class="btn btn-primary btn_save  margin-bottom-5"><?php echo Translation::GetCaption("LBL_CREATE_PALLET_LABEL") ?></a>
                                                                <input type="button" class="btn btn-primary  margin-bottom-5" id="get_bag_detail" name="get_bag_detail" value="Get Bag Details" />
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <?php // }  ?>
                                                    <div style="clear:both"></div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div id="apend_mawb_data">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="mawb_number" id="mawb_number" maxlength="12" value="" class="inputclass form-control inputfield_custom_style mawb_number_txt" title="" placeholder="Enter MAWB" rel="tooltip" data-original-title="Enter MAWB" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="box_weight" id="box_weight" value="" class="form-control inputfield_custom_style" title="" placeholder="Enter Bag Weight" rel="tooltip" data-original-title="Enter Bag Weight" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="box_number" id="box_number" value="" class="inputclass form-control inputfield_custom_style" title="" onkeypress=" return checkKeyValue(event, 1);" placeholder="Bag number" rel="tooltip" data-original-title="Bag number" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label style="font-size:20px" id='lblScannedBagCount'></label>&nbsp;&nbsp;
                                                            <label style="font-size:20px" id='lblRemainingBagCount'></label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a href="javascript:;" id="btnClosePallet" style="display:none;margin-top: 10px;text-align:center" 
                                                               class="btn btn-primary center-block"><?php echo Translation::GetCaption("LBL_CLOSE_CREATE_PALLET_LABEL") ?></a>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="palletno"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table id='tblTrackingNumbers' style="font-size:20px;  text-align:center; width:20%; margin-left: 450px;
                                                                   margin-right: 0px; float:left; display: none;" cellspacing="12" border="1">
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div>
                                                                <iframe id="iFramePdf" style="display:none;" ></iframe>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                   
                                    <div id="single_shipment_scan" class="tab-pane fade">
                                        <form method="post" action="javascript:;" enctype="multipart/form-data" id="close_sessionForm" name="close_sessionForm"  role="form">

                                            <div id="SingleShipmentScan"  class="TabbedPanelsContent">
                                                <div class="portlet light">
                                                    <div class="portlet-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-3">
                                                                    <label><?php echo Translation::GetCaption("LBL_TOTAL_SCANNED") ?> :</label>
                                                                    <span style="color:green" id='spTotalScanned'><?php echo isset($_SESSION['NUMBER_OF_SCANNED']) ? $_SESSION['NUMBER_OF_SCANNED'] : "" ?> </span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label><?php echo Translation::GetCaption("LBL_NOT_SCANNED") ?> :</label>
                                                                    <span style="color:red" id='spTotalNotScanned'><?php echo isset($_SESSION['NUMBER_OF_NOT_SCANNED']) ? $_SESSION['NUMBER_OF_NOT_SCANNED'] : "" ?> </span>
                                                                </div>
                                                                <?php
                                                                if(Permissions::checkFilePermission('on_hold_scan')){ ?>
                                                                    <div class="col-sm-2">
                                                                        <label>Auto Hold Label</label>
                                                                        <div class="form-group">
                                                                            <div class="md-radio-inline">
                                                                                <input name="hold_label" type="checkbox" class="make-switch" data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger" id="hold_label">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php }?>
                                                                <div class="col-sm-2">
                                                                    <label>Re-Scan</label>
                                                                    <div class="form-group">
                                                                        <div class="md-radio-inline">
                                                                            <input name="re_scan" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger" id="re_scan">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="icheck-inline">
                                                                                <label><input type="checkbox" class="icheck chkWeight" id="chkWeight"  value="0" data-checkbox="icheckbox_square-blue"><?php echo Translation::GetCaption("LBL_HIDE_WEIGHT") ?></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="icheck-inline">
                                                                                <label><input type="checkbox" class="icheck" id="chkDimension"  value="0" data-checkbox="icheckbox_square-blue"><?php echo Translation::GetCaption("LBL_HIDE_DIMENSION") ?></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="icheck-inline">
                                                                                <label><input type="checkbox" class="icheck" id="chkSameDim"  value="0" data-checkbox="icheckbox_square-blue"><?php echo Translation::GetCaption("LBL_SAME_DIMENSION_AND_WEIGHT") ?></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-3">
                                                                    <img style="display:none" width="100px" height="50px"  id="service_img" />
                                                                </div>
                                                                <div class="col-md-3">
                                                                </div>
                                                                <div class="col-md-3">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">                            
                                                                <h1><div class="alert" id='message_single_scan'></div></h1>
                                                            </div>
                                                        </div>
                                                        <div class="row" align="center">
                                                            <div class="col-md-12">
                                                                <a href="javascript:;" class="btn_list_single btn btn-primary margin-bottom-5 margin-left-5" data-toggle="modal" data-target="#mawb_list_modal" data-original-title="" title="">Select MAWB</a>
                                                                <a href="javascript:;" class="btn_save_mawb btn btn-primary margin-bottom-5" data-original-title="" title="">Create MAWB</a>
                                                                <!--<a href="javascript:;" class="btn_calculate_weight btn btn-primary margin-bottom-5" data-original-title="" title="">Calculate Weight</a>-->
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                        <div class="input-icon right">
                                                                            <input maxlength="12" name="box_number_single" id="box_number_single" value="<?php echo @$this->form_vars['box_number_single'] ?>" class="inputclass form-control inputfield_custom_style mawb_number_txt" title="" placeholder="Enter MAWB" rel="tooltip" data-original-title="Enter MAWB" type="text" onkeypress=" return checkKeyValue(event, 3);">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                        <div class="input-icon right">
                                                                            <input name="tracking_number_single" id="tracking_number_single" value="" class="inputclass form-control inputfield_custom_style hadi_cls" title="" placeholder="<?php echo Translation::GetCaption("AWB") ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("AWB") ?>" type="text">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12" > <!--style="border-style: ridge;" -->
                                                                <div class="col-md-3">
                                                                    <label>Lenght :</label>
                                                                    <span style="color:green;font-weight: bold;font-size: 14px;" id="scan_lenght"> </span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label>Width :</label>
                                                                    <span style="color:green;font-weight: bold;font-size: 14px;" id="scan_width"> </span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label>Height :</label>
                                                                    <span style="color:green;font-weight: bold;font-size: 14px;" id="scan_height"> </span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label>Weight :</label>
                                                                    <span style="color:green;font-weight: bold;font-size: 14px;" id="scan_weight"> </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div id="tdDimSingle">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                            <div class="input-icon right">
                                                                                <input name="single_length" id="single_length" value="" class="form-control inputfield_custom_style" title="" placeholder="<?php echo Translation::GetCaption("LENGTH") ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("LENGTH") ?>" type="text">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                            <div class="input-icon right">
                                                                                <input name="single_width" id="single_width" value="" class="form-control inputfield_custom_style" title="" placeholder="<?php echo Translation::GetCaption("WIDTH") ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("WIDTH") ?>" type="text">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                            <div class="input-icon right">
                                                                                <input name="single_height" id="single_height" value="" class="form-control inputfield_custom_style" title="" placeholder="<?php echo Translation::GetCaption("HEIGHT") ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("HEIGHT") ?>" type="text">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div id="trWeightSingle" >
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                            <div class="input-icon right">
                                                                                <input name="weight_single" id="weight_single" value="" class="form-control inputfield_custom_style" title=""
                                                                                       placeholder="<?php echo Translation::GetCaption("WEIGHT") ?>" rel="tooltip" 
                                                                                       data-original-title="<?php echo Translation::GetCaption("WEIGHT") ?>" type="text" onkeypress=" return checkKeyValue(event, 2);">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row" style="text-align:center;">    
                                                            <br/>
                                                            <input type="hidden" id="hidAllowWeight" name="hidAllowWeight" value="N" /> 
                                                            <input class="btn btn-primary btn_save" type="submit" id="btn_scan_single"  name="btn_scan_single" value="scan" style="display:none;" />
                                                            <br/>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-4" >
                                                                    <table id='tblScanList'  cellspacing="10" cellpadding="10" border="1" class="table table-striped table-bordered table-advance table-hover"></table>
                                                                </div>
                                                                <div class="col-md-4" >
                                                                    <div id="piechart" style="width: 500px; height: 300px;"></div>
                                                                </div>
                                                                <div class="col-md-4" >
                                                                    <table id='tblNotScanList'  cellspacing="10" cellpadding="10" border="1"  class="table table-striped table-bordered table-advance table-hover"></table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="form_action" id="form_action" value=""  />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                   
                                    <div id="multiple_shipment_scan" class="tab-pane fade">
                                        <div class="portlet light">
                                            <div class="portlet-body">
                                                <div id="MultipleShipmentScan"  class="TabbedPanelsContent"><!-- 1 -->
                                                    <div class="row" style="display: none;">
                                                        <div class="col-md-12" >
                                                            <div class="alert" id="message_multiple"></div>
                                                        </div> 
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>&nbsp;</label>
                                                                <div class="mt-radio-inline">
                                                                    <label>&nbsp;</label>
                                                                    <label class="mt-radio">
                                                                        <label>&nbsp;</label>
                                                                        <input type="radio" name="pre_sort" class="pre_sort" value="scan_bag" checked="checked"> Scan
                                                                        <span></span>
                                                                    </label>
                                                                    <label class="mt-radio">
                                                                        <input type="radio" name="pre_sort" class="pre_sort" value="pre_sort"> <?php echo Translation::GetCaption("LBL_PRE_SORT") ?>
                                                                        <span></span>
                                                                    </label>
                                                                    <label class="mt-radio">
                                                                        <label>&nbsp;</label>
                                                                        <input type="radio" name="pre_sort" class="pre_sort" value="scan_outbound"> <?php echo Translation::GetCaption("LBL_OUTBOUND") ?>
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                        </div>
                                                        <!-- Pre Sort block for multi scan  -->
                                                        <div style="display: none;" id="pre_sort_multiscan_div">
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <label>Carrier Group</label>
                                                                    <div class="first_form_col">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                                            <?php
                                                                            echo Ddl::generateDDL('presort_carrier_group', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip"  title="Carrier Group" data-original-title="Carrier Group"', 'Please select', 'Select Group', 'presort_carrier_group', 'Carrier Group', '', '');
                                                                            ?>
                                                                            <span class="input-group-addon red-18">*</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <label>Carrier Hubs</label>
                                                                    <div class="first_form_col">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                                            <!-- User Document -->
                                                                            <select name="presort_carrier_hubs" id="presort_carrier_hubs" data-live-search="true" class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                                                            </select>
                                                                            <span class="input-group-addon red-18">*</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Pre Sort block for multi scan  -->
                                                    </div>
                                                    <!-- Out Bond Services -->
                                                        <div style="display: none;" class="divScanOutBoundCarrier">
                                                            <div class="row">
                                                               <div class="col-sm-3">
                                                                   <div class="form-group">
                                                                       <label >Services</label>
                                                                       <div class="first_form_col">
                                                                           <div class="">
                                                                               <?php
                                                                                    echo Ddl::generateServiceDDLWithImage('search_Code[]', "", 'id', ' class="bs-select form-control" multiple="multiple" required="" data-live-search="true" data-actions-box="true" data-container="body" data-size="8"','search_Code','','name_code',''); ?> 
                                                                           </div>
                                                                       </div>
                                                                   </div>
                                                               </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Source Country</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                                                            <?php
                                                                            echo Ddl::generateCountryDDL('source_country_id', $source_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_source_warehouse();');
                                                                            ?>
                                                                            <span class="input-group-addon red-18">*</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Source Warehouse</label>
                                                                        <div class="first_form_col">
                                                                            <div class="input-group">
                                                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                                                <!-- User Document -->
                                                                                <select name="source_warehouse_id" id="source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Destination Country</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                                                            <?php
                                                                            echo Ddl::generateCountryDDL('destination_country_id', $destination_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_destination_warehouse();');
                                                                            ?>
                                                                            <span class="input-group-addon red-18">*</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Destination Warehouse</label>
                                                                        <div class="first_form_col">
                                                                            <div class="input-group">
                                                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                                                <!-- User Document -->
                                                                                <select name="destination_warehouse_id" id="destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- End Out Bond Services  -->
                                                    <div class="row" align="center">
                                                        <div id="divMawb">
                                                            <div class="col-md-12">
                                                                <a href="javascript:;" class="btn_list_multi btn btn-primary margin-bottom-5 margin-left-5" data-toggle="modal" data-target="#mawb_list_modal" data-original-title="" title="">Select MAWB</a>
                                                                <a href="javascript:;" class="btn_save_mawb btn btn-primary margin-bottom-5" data-original-title="" title="">Create MAWB</a>
                                                            </div>
                                                            <div class="col-md-12" >
                                                                <div class="form-group">
                                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                        <div class="input-icon right">
                                                                            <input name="mawb_multiple" id="mawb_multiple" value="" size="50" class="inputclass form-control inputfield_custom_style mawb_number_txt" maxlength="12" title="" placeholder="Enter MAWB" rel="tooltip" data-original-title="mawb_multiple" type="text" >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div id="divBagNumber" class="col-md-12">
                                                            <div class="form-group">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="box_multiple" id="box_multiple" value="" size="50" class="inputclass form-control inputfield_custom_style"
                                                                               maxlength="35" title="" placeholder="<?php echo Translation::GetCaption("LBL_BAG_REFERENCE_NUMBER"); ?>"
                                                                               rel="tooltip" data-original-title="<?php echo Translation::GetCaption("LBL_BAG_REFERENCE_NUMBER"); ?>"
                                                                               type="text" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group" id="barcodelist_form_div">
                                                                <div class="input-group"><span class="input-group-addon"> <i class="fa fa-table "></i> </span>
                                                                    <textarea class="form-control notes" type="text" placeholder="Enter Parcel Number" rows="22" name="barcodelist" id="barcodelist" style="min-height: 698px;"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div style="overflow:auto; border: 1px solid #ccc; min-height: 698px;" >
                                                                <table id="tblBoxTrackingNumbers"></table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="clear:both"></div>
                                                    <div class="row" style="text-align:center;">    
                                                        <div class="col-md-12">
                                                            <input class="btn btn-primary btn_save" type="submit" id="btn_scan_multiple" onClick="return MultipleShipmentScan();" name="btn_scan_multiple" value="<?php echo Translation::GetCaption("LBL_SCAN") ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="manifest_scan" class="tab-pane fade">
                                        <div class="portlet light">
                                            <div class="portlet-body">
                                                <div id="ManifestScan"  class="TabbedPanelsContent"><!-- 1 -->
                                                    <div class="row">
                                                        <div class="col-md-12" >
                                                            <div class="alert" id="message_scanmanifest"></div>
                                                        </div> 
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group" id="manifest_form_div">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input maxlength="12" name="manifest_number" id="manifest_number" value="" class="inputclass form-control inputfield_custom_style" title="" placeholder="<?php echo Translation::GetCaption("LBL_ENTER_MANIFEST_NUMBER") ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("LBL_ENTER_MANIFEST_NUMBER") ?>"  type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="warehouseTab"  class="tab-pane fade">
                                        <?php
//                                        if ($user->getUserType() == 'warehouse') {
                                        ?>  
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption"> <i class="glyphicon glyphicon-search"></i>
                                                   <?php echo Translation::GetCaption("LBL_DISPATCH") ?>
                                                </div>
                                                <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <div class="col-md-12" >
                                                        <div class="alert" id="palletMsg" name="palletMsg" ></div>
                                                        <h1><div style="text-align:center" id="palletCarrier" name="palletCarrier" /></h1>
                                                    </div>
                                                </div>
                                                <form method="post" action="javascript:;" enctype="multipart/form-data" id="DispatpalletForm" name="DispatpalletForm"  role="form">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group date form_datetime form_datetime bs-datetime">
                                                                    <input name="date_dispatch" id="date_dispatch" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption("LBL_DISPATCH_DATE"); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("LBL_DISPATCH_DATE"); ?>" type="text">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12" >
                                                            <div class="form-group">
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input  name="palletnumberdispatch"
                                                                                id="palletnumberdispatch" value="" class="form-control inputfield_custom_style" title="" 
                                                                                placeholder="<?php echo Translation::GetCaption("LBL_ENTER_PALLET") ?>"  rel="tooltip" 
                                                                                data-original-title="<?php echo Translation::GetCaption("LBL_ENTER_PALLET") ?>" type="text" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" align="center">    
                                                        <div class="col-md-12">
                                                            <input id="btnDispatchPallet" type="button"  class="btn btn-primary btn_save" value="<?php echo Translation::GetCaption("LBL_DISPATCH_PALLET"); ?>"/>
                                                            <input id="btnClosePallet" type="button" style="display:none" class="btn btn-danger btn_cancel" value="<?php echo Translation::GetCaption("LBL_DISPATCH_PALLET"); ?>"/>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
//                                        }
                                        ?>
                                    </div>  <!--wharehouseTab end  div -->
                                    
                                    <div id="endofday"  class="tab-pane fade">
                                        <?php php // if ($user->getUserType() == 'corporateclient') {   ?>
                                        <input type="hidden" name="form_action" id="form_action" value="<?php echo !empty($form_action) && preg_match('/^[a-zA-Z0-9_ \d]+$/', $form_action) ? $form_action : ''; ?>"  />
                                        <div class="portlet light">
                                            <div class="portlet-body">
                                                <div class="col-mod-12" style="">
                                                    <div class="form-group">
                                                        <div class="mt-radio-inline">
                                                            <label class="mt-radio">
                                                                <input type="radio" name="endofdaytype"
                                                                       onclick="LoadEndOfDay();" checked value="service">
                                                                       <?php echo Translation::GetCaption("SERVICE") ?>
                                                                <span></span>
                                                            </label>
                                                            <label class="mt-radio">
                                                                <input type="radio" value="carrier"
                                                                       onclick="LoadEndOfDay();" name="endofdaytype"> 
                                                                       Carrier
                                                                <span></span>
                                                            </label>
                                                            <label class="mt-radio">
                                                                <input type="radio" value="agent"
                                                                       onclick="LoadEndOfDay();" name="endofdaytype">
                                                                       Agent
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div id="end_of_the_day_div" class="TabbedPanelsContent">
                                                </div>
                                            </div>
                                        </div>
                                        <?php // }    ?>
                                    </div>
                                   
                                </div><!--content div-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="dialogAlertSaveWeightYesNo">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Alert</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" id="msgweightyesno"></div>           
                    </div>
                    <div class="modal-footer">                
                        <button type='button' class='btn btn-default' id="btnSaveWeight" name="btnSaveWeight" value="Yes">Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    </div>
                </div>                <!-- /.modal-content --> 
            </div>            <!-- /.modal-dialog --> 
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="dialogAlertYesNo" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" id="msgyesno"></div>
                    </div>
                    <div class="modal-footer">
                        <button type='button' class='btn btn-default' id="btnYes" name="btnYes" data-dismiss="modal" value="Yes">Yes</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>                <!-- /.modal-content --> 
            </div>            <!-- /.modal-dialog --> 
        </div>
        <div id="dialogAlertNotFoundItems" class="modal fade" role="dialog">
            <div class="modal-dialog">                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div id="msgyesnonotfound" style="overflow-y: scroll;height: 100px;">                     	
                        </div>
                        <div class="container">               	 	
                            <div class="col-md-4 col-md-offset-2">
                                <button type='submit' class='btn btn-primary' 
                                        id="btnSendMultiDataYes" name="btnSendMultiData">Yes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="dialogAlert" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" id="msg"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content --> 
            </div>            <!-- /.modal-dialog --> 
        </div>
        <div id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="osx-modal-title"> Manifest Details </div>
                    <form method="post" action="javascript:;" enctype="multipart/form-data" id="savemanifestForm" name="savemanifestForm">
                        <div class="modal-body" id="osx-modal-data">
                            <div class="row"  id="manifest_msg" style="display: none;">
                                <div class="col-md-12" >
                                    <div class="alert" id="manifest_message" name="manifest_message" ></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="palletdetails"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btnSaveManifest" name="btnSaveManifest" value="Save" />
                            <input type='hidden' id='manifestid' name='manifestid'  />
                            <input type='hidden' id='handling' name='handling'  />
                            <input type='hidden' id='frm_parcel_id' name='frm_parcel_id' value="" />
                            <input type='hidden' id='frm_service_id' name='frm_service_id' value="" />
                            <input type='hidden' id='frm_type_case' name='frm_type_case' value="" />
                            <input type='hidden' id='frm_agent_id' name='frm_agent_id' value="" />
                            <input type='hidden' id='frm_carrier_id' name='frm_carrier_id' value="" />
                            <input type='hidden' id='frm_parcel_action' name='frm_parcel_action' value=""  />
                            <input type='hidden' id='frm_parcel_data' name='frm_parcel_data' value=""  />
                            <input type="hidden" name="form_action" id="form_action" value="savemanifest" />
                        </div>
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- Enter mawb details modal -->
        <div id="mawb_model" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"> MAWB Details </div>
                    <form method="post" action="javascript:;" id="saveMawbForm" name="saveMawbForm">
                        <div class="modal-body">
                            <div class="row" id="show_general_msg_mawb" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-success"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Flight Number</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="flight_number" id="flight_number" value="" placeholder="Flight Number"  data-toggle="tooltip" data-placement="top" title="Flight Number" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Number of pieces</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="number_of_pieces" id="number_of_pieces" value="" placeholder="Number of Pieces"  data-toggle="tooltip" data-placement="top" title="Number of Pieces" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="weight_mawb" id="weight_mawb" value="" placeholder="Weight"  data-toggle="tooltip" data-placement="top" title="Weight" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="status_mawb" id="status_mawb" value="" placeholder="Status"  data-toggle="tooltip" data-placement="top" title="Status" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="hidden" name="mawb_new" id="mawb_new" value="" />
                            <input type='submit' class='simplemodal-close btn btn-primary'   id="btnSaveMawb" name="btnSaveMawb" value="Save MAWB" />
                        </div>
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End All Modal -->
        <!-- Dispatch Functionality Model-->
        <div id="dispatch_fun_modal" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" id="osx-modal-title"> Dispatch Details </div>
                        <form method="post" action="" enctype="multipart/form-data" id="disptach_frm" name="disptach_frm">
                            <div class="modal-body" id="osx-modal-data">
                            <div class="row"  id="dispatch_modal_msg" style="display: none;">
                                <div class="col-md-12" >
                                    <div class="alert" id="dispatch_modal_div" ></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Dispatch Agent</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <?php
                                                echo Ddl::generateDDL('con_dispatch_agent', 'AgentDataFilter', " agent_type IN ('dispatch','both') ", 'agent_name', 'id', '', ' class="form-control select2" data-show-subtext="true" data-toggle="tooltip"  title="Dispatch Agent" data-original-title="Dispatch Agent"', 'Please select', '', 'con_dispatch_agent', 'Dispatch Agent', '', '');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Services</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <?php
                                                echo Ddl::generateDDL('con_service', 'ServiceFilter', "     id = 0 ", 'name', 'id', '', ' class="form-control select2" data-show-subtext="true" data-toggle="tooltip"  title="Service" data-original-title="Service"', 'Please select', 'Select Service', 'con_service', 'Dispatch Service', '', '');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_weight" id="con_weight" value="" placeholder="Consignment weight"  data-toggle="tooltip" data-placement="top" title="Consignment weight" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Master Number</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_master_no" id="con_master_no" value="" placeholder="Master Number" maxlength="30" data-toggle="tooltip" data-placement="top" title="Master Number" class="tooltipbutton form-control mawb_number_txt"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Select Currency</label>
                                        <div
                                            class="input-group select2-bootstrap-append select2-bootstrap-prepend">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                        class="fa fa-sticky-note"></i> </span>
                                                    <?php
                                                    $themeArray = array('GBP' => 'GBP');
                                                    echo Ddl::generateArrayDDL('con_currency', $themeArray, $theme, 'Select Currency', ' class="form-control select2" data-toggle="tooltip" data-placement="top" title="Select Currency" data-original-title="Theme"');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Value</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_value" id="con_value" value="" placeholder="Consignment Value"  data-toggle="tooltip" data-placement="top" title="Consignment Value" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>No. of Pieces</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="number" name="con_no_item" id="con_no_item" value="" placeholder="No. of Pieces"  data-toggle="tooltip" data-placement="top" title="No. of Pieces" class="tooltipbutton form-control"  oninput="javascript: if (this.value >= 100) this.value = 100;"/>
                                        </div>
                                        <span class="help-block"> Can't enter more than 100 Pieces </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label></label><br/>
                                        <input type="button" class="btn btn-primary" id="btn_show_address"  value="Add Address" data-original-title="" title="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Send E-mail</label>
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" name="carrier" id="dispatch_carrier_email" class="dispatch_email_chk icheck"> Carrier 
                                                </label>
                                                <label>
                                                    <input type="checkbox" name="agent" id="dispatch_agent_email" class="dispatch_email_chk icheck"> Agent 
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span id="show_address_div"> </span>
                                </div>
                            </div>
                        </div>
                            <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btn_save_con" name="btn_save_con" value="Dispatch" />
                            <input type='hidden' id='country_id' name='country_id'  value=""/>
                            <input type='hidden' id='manifest_id' name='manifest_id'  value=""/>
                            <input type="hidden" name="dispatch_form_action" id="dispatch_form_action" value="save_dispatch_con" />
                        </div>
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Dispatch Functionality-->
        <!-- Add address model -->
        <div id="show_address" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" id="osx-modal-title"> Address Details </div>
                    <form method="post" action="" enctype="multipart/form-data" id="saveAddressForm" name="saveAddressForm">
                        <div class="modal-body" id="osx-modal-data">
                            <div class="row"  id="address_msg" style="display: none;">
                                <div class="col-md-12" >
                                    <div class="alert" id="address_message" name="address_message" ></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address 1</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_address_1" id="con_address_1" value="" placeholder="Address 1"  data-toggle="tooltip" data-placement="top" title="Address 1" class="tooltipbutton form-control valid"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address 2</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_address_2" id="con_address_2" value="" placeholder="Address 2"  data-toggle="tooltip" data-placement="top" title="Address 2" class="tooltipbutton form-control valid"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Address 3</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_address_3" id="con_address_3" value="" placeholder="Address 3"  data-toggle="tooltip" data-placement="top" title="Address 3" class="tooltipbutton form-control valid"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>City</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_city" id="con_city" value="" placeholder="City"  data-toggle="tooltip" data-placement="top" title="City" class="tooltipbutton form-control valid"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Post Code</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="con_postcode" id="con_postcode" value="" placeholder="Post Code"  data-toggle="tooltip" data-placement="top" title="Post Code" class="tooltipbutton form-control valid"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <div class="input-group select2-bootstrap-append select2-bootstrap-prepend">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                            <?php
                                            $reg_country = "";
                                            echo Ddl::generateCountryDDL('con_country', $reg_country, 'id', ' class="form-filter bs-select form-control valid_country"  data-live-search="true" data-size="8" ');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label>
                                                <input id="check_add_address" name="check_add_address" type="checkbox" class="icheck" data-checkbox="icheckbox_flat-blue"  value="1" /> Save Address
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption"> <i class="icon-users"></i>
                                           <?php echo Translation::GetCaption("ADDRESS_BOOK"); ?>
                                        </div>
                                        <div class="actions">
                                        </div>
                                    </div>

                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th>Action</th>
                                                        <th>Company</th>
                                                        <th>Contact</th>
                                                        <th>Address</th>
                                                        <th>City</th>
                                                        <th>Country</th>
                                                        <th>Postcode</th>
                                                    </tr>
                                                    <tr role="row" class="filter">
                                                        <td>
                                                            <div class="margin-bottom-5">
                                                                <button class="btn btn-sm yellow filter-submit btn-outline margin-bottom-5"><i class="fa fa-search"></i></button>
                                                                <button  class="btn btn-sm red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-filter input-sm" name="search_company">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-filter input-sm" name="search_contact">
                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-filter input-sm" name="search_city">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-filter input-sm" name="search_country">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-filter input-sm" name="search_postcode">
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>  <!-- table_container -->
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btnSaveAddress" name="btnSaveAddress" value="Save" />
                            <input type="hidden" name="form_action_address" id="form_action_address" value="save_address" />
                        </div>
                    </form>    
                </div>
            </div>
        </div>
        <!-- End Add address model -->
        <!-- Get bag details Modal -->
        <div id="get_bag_detail_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body" id="osx-modal-data">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="apend_bag_detail_id"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>   
                </div>
            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- Pallet Bag remove reason -->
        <div id="pallet_bag_remove_reason_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="osx-modal-title"> Pallet Bag Remove Reason </div>
                    <form method="post" action="javascript:;" enctype="multipart/form-data" id="palletBagRemoveReason" name="palletBagRemoveReason">
                        <div class="modal-body" id="osx-modal-data">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <div class="input-group"> <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                            <textarea id="pallet_bag_remove_reason_txt" name="pallet_bag_remove_reason_txt" type="text" placeholder="Reason" class="form-control tooltipbutton" ata-toggle="tooltip" data-placement="top" title="" data-original-title="Please enter remove reason for bag from pallet."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btnSavePalletRemoveReason" name="btnSavePalletRemoveReason" value="Save" />
                            <input type='hidden' id='bag_remove_pallet_id' name='bag_remove_pallet_id' value=""/>
                            <input type='hidden' id='bag_remove_bag_id' name='bag_remove_bag_id' value="" />
                            <input type='hidden' id='bag_remove_entity_id' name='bag_remove_entity_id' value="" />
                            <input type="hidden" name="form_action" id="form_action" value="saveRemovePalletBag" />
                        </div>
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Pallet Bag remove reason -->
        <!-- Send Manifest Email Modal -->
        <div id="send_manifest_email_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="osx-modal-title"> Send Email </div>
                    <form method="post" action="" enctype="multipart/form-data" id="send_manifest_email" name="send_manifest_email">
                        <div class="modal-body" id="osx-modal-data">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Carrier Email Address</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="manifest_carrier_email" id="manifest_carrier_email" value="" placeholder="Please enter email address comma separated"  data-toggle="tooltip" data-placement="top" title="Carrier Email Address" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Agent Email Address</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="agent_carrier_email" id="agent_carrier_email" value="" placeholder="Please enter email address comma separated"  data-toggle="tooltip" data-placement="top" title="Agent Email Address" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-money"></i></div>
                                            <input type="text" name="carrier_email_subject" id="carrier_email_subject" value="" placeholder="Please enter email subject"  data-toggle="tooltip" data-placement="top" title="Please enter email subject" class="tooltipbutton form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Content</label>
                                            <textarea id="carrier_email_content" name="carrier_email_content" type="text" placeholder="Please enter Email content" class="form-control tooltipbutton" rows="10" ata-toggle="tooltip" data-placement="top" title="" data-original-title="Please enter Email content"></textarea>
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary'   id="btn_send_manifest_email" name="btn_send_manifest_email" value="Send" />
                            <input type='hidden' id='manifest_id_send_manifest_email' name='manifest_id_send_manifest_email' value="" />      
                            <input type="hidden" name="form_action" id="form_action" value="send_manifest_email" />
                        </div>
                    </form>    
                </div>
            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Send Manifest Email Modal -->
        <!-- list pallet -->
        <div class="modal fade" tabindex="-1" role="dialog" id="pallet_list_modal" >
                <div class="modal-dialog modal-full">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Pallet List</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="portlet light">
                                        <div class="portlet-body">   
                                            <div class="table-container">
                                               <table class="table table-striped table-bordered table-hover table-condensed" id="manage-pallet-data-table">
                                                    <thead>
                                                        <tr role="row" class="heading">
                                                            <th>Actions</th>
                                                            <th>Date Created</th>
                                                            <th>Pallet Number</th>
                                                            <th>Pallet Carrier Group</th>
                                                            <th>Pallet Carrier Hub</th>
                                                            <th>Source Country</th>
                                                            <th>Source Warehouse</th>
                                                            <th>Destination Country</th>
                                                            <th>Destination Warehouse</th>
                                                        </tr>
                                                        <tr role="row" class="filter">
                                                            <td>
                                                                <div class="margin-bottom-5">
                                                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                                </div>

                                                            </td>
                                                            <td>
                                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-sm default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-sm default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-filter input-xs" name="pallet_number" id ="master_number" />
                                                            </td>
                                                            <td>
                                                                 <?php
                                                                    echo Ddl::generateDDL('pallet_carrier_group_filter', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="Pallet Carrier Group" data-live-search="true"  data-original-title="Pallet Carrier Group"', 'Please select', '', 'pallet_carrier_group_filter', 'Pallet Carrier Group', '', '');
                                                                    ?>
                                                            </td>
                                                            <td>
                                                                <select name="carrier_hub_filter" id="carrier_hub_filter" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                                                </select>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo Ddl::generateCountryDDL('source_country', $source_country, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_source_warehouse_filter();');
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <select name="source_warehouse" id="source_warehouse" class="bs-select form-control form-filter" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo Ddl::generateCountryDDL('destination_country', $destination_country, 'id', ' class="form-filter bs-select form-control" required="required" data-live-search="true" data-size="8" onChange=get_destination_warehouse_filter();');
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <select name="destination_warehouse" id="destination_warehouse" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                                </select>                                
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
                        </div>
                        <div class="modal-footer">
                            
                        </div>
                    </div>
                </div>
            </div>
        <!-- End list pallet -->
        <!-- Save Pallet -->
        <div id="save_pallet_modal" class="modal fade">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Save Pallet</h4>
                    </div>
                    <form method="post" action="" enctype="multipart/form-data" id="send_manifest_email" name="send_manifest_email">
                        <div class="modal-body" id="osx-modal-data">
                             <div class="row">
                                 <div class="row" id="show_general_msg_pallet_modal" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Pallet Carrier Group</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <?php
                                                echo Ddl::generateDDL('save_pallet_carrier_group', 'PalletCarierGroupFilter', " id != 0", 'group_name', 'id', '', ' class="bs-select form-control" data-show-subtext="true" data-toggle="tooltip"  title="Pallet Carrier Group" data-live-search="true"  data-original-title="Pallet Carrier Group"', 'Please select', '', 'save_pallet_carrier_group', 'Pallet Carrier Group', '', '');
                                                ?>
                                                <span class="input-group-addon red-18">*</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Carrier Hubs</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_carrier_hub" id="save_carrier_hub" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Carrier Hubs" data-container="body" placeholder="Carrier Hubs">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Source Country</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('save_source_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_save_source_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Source Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_source_warehouse_id" id="save_source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Destination Country</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('save_destination_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_save_destination_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Destination Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_destination_warehouse_id" id="save_destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary' onclick="save_pallet_fun()"  id="btn_save_pallet" name="btn_save_pallet" value="Save" />
                        </div>
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Save Pallet -->
        <!-- list Mawb -->
        <div class="modal fade bag_mawb" tabindex="-1" role="dialog" id="mawb_list_modal" >
                <div class="modal-dialog modal-full">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">MAWB List</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="portlet light">
                                        <div class="portlet-body">   
                                            <div class="table-container">
                                               <!--Hadi Code-->
                                                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table-mawb">
                                                    <thead>
                                                        <tr role="row" class="heading">
                                                            <th>Actions</th>
                                                            <th>Date Created</th>
                                                            <th>Master Number</th>
                                                            <th>Source Country</th>
                                                            <th>Source Warehouse</th>
                                                            <th>Destination Country</th>
                                                            <th>Destination Warehouse</th>
                                                        </tr>
                                                        <tr role="row" class="filter">
                                                            <td>
                                                                <div class="margin-bottom-5">
                                                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                                </div>

                                                            </td>
                                                            <td>
                                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-sm default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-sm default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-filter input-xs" name="master_number" id ="master_number" />
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo Ddl::generateCountryDDL('mawb_source_country', $source_country, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_mawb_source_warehouse();');
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <select name="source_mawb_warehouse" id="source_mawb_warehouse" class="bs-select form-control form-filter" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo Ddl::generateCountryDDL('mawb_destination_country', $destination_country, 'id', ' class="form-filter bs-select form-control" required="required" data-live-search="true" data-size="8" onChange=get_mawb_destination_warehouse();');
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <select name="destination_mawb_warehouse" id="destination_mawb_warehouse" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                                </select>                                
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
                        </div>
                        <div class="modal-footer"> 
                        </div>
                    </div>
                </div>
            </div>
        <!-- End list Mawb -->
        <!-- Save Mawb -->
        <div id="save_mawb_modal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Save Master <span id="mawb_number_span"></span> </h4>
                    </div>
                    <form method="post" action="" enctype="multipart/form-data" id="save_mawb_frm" name="save_mawb_frm">
                        <div class="modal-body" id="osx-modal-data">
                             <div class="row">
                                <div class="row" id="show_general_msg_master" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">Please select pallet carrier group</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Source Country</label>
                                        <div class="input-group source_country_select">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('save_mawb_source_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_save_mawb_source_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Source Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group source_warehouse_select">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_mawb_source_warehouse_id" id="save_mawb_source_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Source Warehouse" data-container="body" placeholder="Source Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Destination Country</label>
                                        <div class="input-group destination_country_select">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('save_mawb_destination_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" required="required" onChange=get_save_mawb_destination_warehouse();');
                                            ?>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Destination Warehouse</label>
                                        <div class="first_form_col">
                                            <div class="input-group  destination_warehouse_select">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                                <!-- User Document -->
                                                <select name="save_mawb_destination_warehouse_id" id="save_mawb_destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnCloseModal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type='button' class='simplemodal-close btn btn-primary' onclick="save_mawb_fun()"  id="btn_save_pallet" name="btn_save_pallet" value="Save" />
                        </div>
                        <input type="hidden" name="mawb_number_txt" id="mawb_number_txt" />
                    </form>    
                </div>

            </div>
            <!-- /.modal-content --> 
        </div>
        <!-- End Save Mawb -->
        <!-- End All Modal -->



        <?php
    }

    public function renderFooter() {
        ?>

        <?php
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }
    
}
function sendEmail($to,$subject,$content,$from='From: Smarttrack <info@smarttrack.co>') {
    $to = str_replace(";", ",", $to);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <info@smarttrack.co>' . "\r\n";
    
    $success = mail($to, $subject, $content, $headers);
    
    $reMessage = "";
    if (!$success) {
        $reMessage = error_get_last()['message'];
        $arr = array('result' => 'error', 'message' => $reMessage);
    }else{
        $reMessage = "Mail Sent successfully";
        $arr = array('result' => 'success', 'message' => $reMessage);
    }
    return $arr;
    
}
// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
