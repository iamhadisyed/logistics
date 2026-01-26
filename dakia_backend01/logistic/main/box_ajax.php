<?php

//
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'pdfmerger',
    'viaeurope.class',
    'cpostips.class'
], 'labels');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'include_list',
    
    ], 'reamus');
include_classes([
    'iaddress.class',
    'bagging.class',
    'baggingfilter.class',
    'consignment.class',
    'consignmentfilter.class',
    'presortcarrierfilter.class',
    'presortcarrier.class',
    'carrierhubstfilter.class',
    'carrierhubs.class',    
    'consignmentbaggingmapping.class',
    'consignmentbaggingmappingfilter.class',
    'p2baglabel.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'manifestdatatfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'mawb.class',
    'mawbfilter.class',
    'palletcariergroupfilter.class',
    'palletcariergroup.class',
    'palletcarrierservice.class',
    'palletcarrierservicefilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'pallet.class',
    'palletfilter.class',
    'palletcarrier.class',
    'palletcarrierfilter.class',
    'exportpalletlabel.class',
    'exportpalletlabelfilter.class',
    'palletentitymappingfilter.class',
    'palletentitymapping.class',
    'country.class',
    'countryfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'parcellogfilter.class',
    'parcellog.class',
    'consignmentlogfilter.class',
    'consignmentlog.class',
    'parcelbaggingmappingfilter.class',
    'parcelbaggingmapping.class',
    'manifestentitymappingfilter.class',
    'manifestentitymapping.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'carrierservicedefaultrulesfilter.class',
    'carrierservicedefaultrules.class',
    'agentdatafilter.class',
    'agentdata.class',
    'dispatchmanifestfilter.class',
    'dispatchmanifest.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'carrierservice.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentrelabel.class',
    'consignmentrelabelfilter.class',
    'flightinfo.class',
    'flightinfofilter.class',
    'rack.class',
    'rackfilter.class',
    'rackshelf.class',
    'rack.class',
    'rackfilter.class',
    'consignmnethold.class',
    'notfoundrecordfilter.class',
    'notfoundrecord.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'bagscanlog.class',
    'baggingservicesmapping.class',
    'prealertfilter.class',
    'prealert.class',
    'manifestservicemapping.class',
    'manifestservicemappingfilter.class',
    'consignmentdropoffmapping.class',
    'consignmentdropoffmappingfilter.class',
    'manifestconsignmentmapping.class',
    'manifestconsignmentdatatfilter.class',
    'manifestconsignment.class',
    'mawb.class',
    'mawbfilter.class',
    'statusreason.class',
    'statusreasonfilter.class',
    'mawbbagdetail.class',
    'mawbbagdetailfilter.class'
    ]);
// All New Logic
/*
 * Start New Functionality
 */
if(isset($_POST['action']) && $_POST['action'] == "get_country_warehouse_id"){
    $html = "";
    $countryId = $_POST['country_id'];
    if(!empty($countryId)){
        $warehouseFilter = new WarehouseFilter();
        $warehouseFilter->addFieldFilter("countryid", $countryId);
        $warehouseObj = $warehouseFilter->getColumnList("warehouse_name");
        if(count($warehouseObj) > 0){
            foreach ($warehouseObj as $warehouse) {
                $html .= '<option value="'.$warehouse->getId().'">'.$warehouse->getWarehouseName().'</option>';
            }
        }
    }
    echo $html;
    die;
}
if(isset($_POST['action']) && $_POST['action'] == "create_bag"){
    $user = SessionManager::getUser();
    $weight = 0;
    $errorMsg = "";
    $dateTime = date("Y-m-d H:i:s");
    $barcodeArr = $_POST['barcodelist'];
    $foundParcelArr = [];
    $foundParcelTrackingArr = [];
    $notFoundParcelTrackingArr = [];
    $foundParcelWeight = 0;
    // Check weight limit for parcel [30 KG limit]
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addTrackingNumberFilterIn("    tracking_number", $barcodeArr);
    $parcelObjArr = $parcelFilter->getColumnList("id,weight,tracking_number");
    if(count($parcelObjArr) > 0){
        foreach ($parcelObjArr as $parcelObj) {
            $foundParcelArr [] = $parcelObj->getId();
            $foundParcelTrackingArr [] = $parcelObj->getTrackingNumber();
            $foundParcelWeight += $parcelObj->getWeight();
        }
    }
    if($foundParcelWeight >= 30){
        $errorMsg = "Bag weight limit 30 kg exceeded";
        $msg = "Bag creating issue occcur <br />".$errorMsg;
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        die;
    }
    // Get not found parcel tracking to show error
    $notFoundParcelTrackingArr = array_diff($barcodeArr,$foundParcelTrackingArr);
    if(count($notFoundParcelTrackingArr) > 0){
        foreach ($notFoundParcelTrackingArr as $notFoundParcelTracking) {
            $errorMsg .= "Parcel ".$notFoundParcelTracking." Not found in system";
            //Add data into not found table
            $notFoundData['mawb'] = "";
            $notFoundData['bag_number'] = "";
            $notFoundData['tracking_number'] = $notFoundParcelTracking;
            $notFoundData['scanned_by'] = $user->getId();
            $notFoundData['length'] = "";
            $notFoundData['width'] = "";
            $notFoundData['height'] = "";
            $notFoundData['weight'] = "";
            $notFoundData['date_created'] = $dateTime;
            saveNotFoundRecord($notFoundData);
        }
    }

    $sourceCountryId = $_POST['source_country_id'];
    $sourceWarehouseId = $_POST['source_warehouse_id'];
    $destinationCountryId = $_POST['destination_country_id'];
    $destinationWarehouseId = $_POST['destination_warehouse_id'];
    $bagnumber = "SMRT".time();
    $bagging = new Bagging();
    $bagging->setBagnumber($bagnumber);
    $bagging->setDateCreated(time());
    $bagging->setUserId($user->getId());
    $bagging->setBagSourceCountryId($sourceCountryId);
    $bagging->setBagSourceWarehouseId($sourceWarehouseId);
    $bagging->setBagDestinationCountryId($destinationCountryId);
    $bagging->setBagDestinationWarehouseId($destinationWarehouseId);
    $bagging->save();
    $bagId = $bagging->getId();
    if($bagId > 0){
        if(count($foundParcelArr) > 0){
            foreach ($foundParcelArr as $parcelId) {
                //Check if parcel is not in bagging table
                $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                $parcelBaggingMappingFilter->addFieldFilter("parcel_id", $parcelId);
                $parcelBaggingMappingFilter->addFieldFilter("bag_id", $bagId);
                $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id");
                if(count($parcelBaggingMappingObj) == 0){
                    //Save Data to parcel bagging mapping
                    $parcelBaggingMapping = new ParcelBaggingMapping();
                    $parcelBaggingMapping->setParcelId($parcelId);
                    $parcelBaggingMapping->setBagId($bagId);
                    $parcelBaggingMapping->setAddedDate(time());
                    $parcelBaggingMapping->save();
                }
            }
        }
        $msg = "Bag ".$bagnumber." created successfully";
        $arr = array('result' => 'success', 'message' => $msg);
    }else{
        $msg = "Bag creating issue occcur <br />".$errorMsg;
        $arr = array('result' => 'error', 'message' => $msg);
    }
    echo json_encode($arr);
    die;
}
if(isset($_POST['action']) && $_POST['action'] == "create_mawb"){
//    include_classes([
//        strtolower('CpostIps') . '.class'
//    ],'labels');
//    $cpost = new CpostIps();
//    print_r($cpost->closeMawb('GBHMIDCOBOGCAUZ00017'));
//    die;
    $mawbId = "";
    $user = SessionManager::getUser();
    $mawbNumber = (isset($_POST['mawb_number']) ? $_POST['mawb_number'] : '');
    $mawbClass = trim($_POST['mawb_class']);
    $sourceCountryId = (isset($_POST['source_country_id']) ? $_POST['source_country_id'] : 0);
    $sourceWarehouseId = (isset($_POST['source_warehouse_id']) ? $_POST['source_warehouse_id'] : NULL);
    $destinationCountryId = (isset($_POST['destination_country_id']) ? $_POST['destination_country_id'] : 0);
    $destinationWarehouseId = (isset($_POST['destination_warehouse_id']) ? $_POST['destination_warehouse_id'] : NULL);
    $grossWeight = (($_POST['gross_weight'] > 0) ? $_POST['gross_weight'] : 0);
    $chargeableWeight = (($_POST['chargeable_weight'] > 0) ? $_POST['chargeable_weight'] : 0);
    $item = $_POST['item'];
    
    $parcelNumber = (isset($_POST['parcel_number']) ? $_POST['parcel_number'] : "");
    $flightId = (isset($_POST['flight_id']) ? $_POST['flight_id'] : 0);
    if(!empty($mawbNumber)){
        if(strlen($mawbNumber) > 12 || strpos($mawbNumber, "-") != 3){
            $return['response'] ='error';
            $return['msg'] ='MAWB format is not supported';
            $return['class'] ='alert alert-danger';
            echo json_encode($return);
            exit;
        }else{
            $mawbFilter = New MawbFilter();
            $mawbFilter->addFieldFilter("    mawb_number",$mawbNumber);
            $mawbList = $mawbFilter->getList();
            if(count($mawbList) > 0){
                $mawbId = $mawbList[0]->getId();
                if($sourceCountryId == $mawbList[0]->getMawbSourceCountryId() && $destinationCountryId == $mawbList[0]->getMawbDestinationCountryId()){
                }else{
                    $return['response'] ='error';
                    $return['msg'] ='MAWB already exist';
                    $return['class'] ='alert alert-danger';
                    echo json_encode($return);
                    exit;
                }
            }
        }
        // Check if MAWB is already exist
    }

    if($flightId > 0){
        $flightInfoObj = new FlightInfo($flightId);

        if(!empty($flightInfoObj)){
            $sourceCountryId = $flightInfoObj->getCountryId();
            $destinationCountryId = $flightInfoObj->getDestinationCountryId();
        }
    }

    if(empty($mawbClass)) {
        $mawb = $mawbNumber;
        if (empty($mawbNumber)) {
            $time = time();
            $rand = rand(1, 9);
            $replacement = '-';
            $mawb = $time . $rand;
            $mawb = substr_replace($mawb, $replacement, 3, 0);
        }
    } else {
        include_classes([
            strtolower($mawbClass) . '.class'
        ],'labels');
        $cpostTipsObj = new $mawbClass();
        $data['flight_number'] = $flightInfoObj->getFlightNumber();
        $data["mail_category"] = "A";
        $data["mail_subclass"] = "UZ";
        $data["origin_office_location"] = "GBHMID";
        $data["destination_office_location"] = "COBOGC";
        $data["origin_location"] = "GBHMI";
        $data["destination_location"] = "COBOG";
        $data["user_id"] = "oneworld";
        $responseData = $cpostTipsObj->createMawb($data);
        if($responseData['STATUS'] == 'SUCCESS') {
            $mawb = $responseData['MAWB_NUMBER'];
        } else {
            $return['response'] ='error';
            if(!empty($responseData['MESSAGE'])) {
                $return['msg'] = $responseData['MESSAGE'];
            } else {
                $return['msg'] = 'Something went wrong. Please try again later.';
            }
            $return['class'] ='alert alert-danger';
            echo json_encode($return);
            exit;
        }
    }

    $mawbObj = new Mawb($mawbId);
    $mawbObj->setMawbSourceCountryId($sourceCountryId);
    $mawbObj->setMawbSourceWarehouseId($sourceWarehouseId);
    $mawbObj->setMawbDestinationCountryId($destinationCountryId);
    $mawbObj->setMawbDestinationWarehouseId($destinationWarehouseId);
    $mawbObj->setGrossWeight($grossWeight);
    $mawbObj->setChargeWeight($chargeableWeight);
    $mawbObj->setMawbNumber($mawb);
    $mawbObj->setIsActive('y');
    $mawbObj->setAddedDate(date("Y-m-d H:i:s",time()));
    $mawbObj->setUpdatedDate(date("Y-m-d H:i:s",time()));
    $mawbObj->setAddedBy($user->getId());
    $mawbObj->setMawbClass($mawbClass);
    $return = [];
    if($sourceCountryId > 0 && ($destinationCountryId > 0 || !empty($mawbClass))){
        $mawbObj->save();
        if(count($parcelNumber) > 0 && $mawbObj->getId() > 0){
            $user = SessionManager::getUser();
            $parcelNotFound = '';
            foreach ($parcelNumber as $itemParcel) {
                // First check in system parcel found
                $parcelId = ParcelFilter::getParcelIdFromTrackingNumber($itemParcel);
                if($parcelId > 0){
                    // Then check if parcels are already added into the other MAWB
                    $mawbParcelMappingFilter = New MawbParcelMappingFilter();
                    $mawbParcelMappingFilter->addFieldFilter("    parcel_id",$parcelId);
//                    $mawbParcelMappingFilter->addFieldNotEqualFilter("mawb_id",$mawbObj->getId());
                    $mawbParcelMappingFilter->addFieldFilter("wharehouse_id",$user->getWarehouseId());
                    $parcelObj = $mawbParcelMappingFilter->getColumnList("id");
                    if(count($parcelObj) > 0){
                        $parcelNotFound .= '<br/> Parcel ['.$itemParcel.'] already added into another MAWB<br/>';
                    }else{
                        //Add data to mawb_parcel_mapping table
                        $mawbParcelMapping = new MawbParcelMapping();
                        $mawbParcelMapping->setMawbId($mawbObj->getId());
                        $mawbParcelMapping->setParcelId($parcelId);
                        $mawbParcelMapping->setWharehouseId($user->getWarehouseId());
                        $mawbParcelMapping->setDateAdded(time());
                        $mawbParcelMapping->setAddedBy($user->getId());
                        $mawbParcelMapping->save();
                    }
                }else{
                    $parcelNotFound .= '<br/> Parcel ['.$itemParcel.'] not found in the system <br/>';
                }
            }
        }
        if($flightId > 0 && $mawbObj->getId() > 0){
            $flightMaping = new FlightMapping();
            $flightMaping->setFlightInfoId($flightId);
            $flightMaping->setMawbId($mawbObj->getId());
            $flightMaping->setIsDelete('0');
            $flightMaping->save();
        }
        if(count($item) > 0 && $mawbObj->getId() > 0){
            $mawbBagDetailFilter = new MawbBagDetailFilter();
            $mawbBagDetailFilter->addFilter(" mawb_id  = '.$mawbObj->getId().'");
            $mawbBagList = $mawbBagDetailFilter->getList();
            if(count($mawbBagList) > 0){
                $mawbBagDetail = new MawbBagDetail();
                $mawbBagDetail->deleteBySql("DELETE FROM mawb_bag_detail WHERE mawb_id = '".DbAccess3::escape($mawbObj->getId())."'");
            }
            else
            {
                $volWeight = 0;
                foreach($item as $itemdetail){
                    $length = $itemdetail["item_length"];
                    $width = $itemdetail["item_width"];
                    $height = $itemdetail["item_height"];
                    $volWeight += ($length * $width * $height) / 5000;
                    $mawbBagDetail = new MawbBagDetail();
                    $mawbBagDetail->setMawbId($mawbObj->getId());
                    $mawbBagDetail->setQuantity($itemdetail["mawb_qty"]);
                    $mawbBagDetail->setLength($itemdetail["item_length"]);
                    $mawbBagDetail->setWidth($itemdetail["item_width"]);
                    $mawbBagDetail->setHeight($itemdetail["item_height"]);
                    $mawbBagDetail->setDateUpdated(time());
                    $mawbBagDetail->save();
                }
                $return["mawb_volWeight"] = $volWeight;
            }
        }
    }else{
        $return['response'] ='error';
        $return['msg'] ='Check your source or destination country';
        $return['class'] ='alert alert-danger';
        echo json_encode($return);
        exit;
    }
    if($mawbId > 0){
        $return['response'] ='success';
        $return['msg'] ='Mawb updated successfully '.$parcelNotFound;
        $return['mawb'] = $mawb;
        $return['mawb_number'] = $mawbObj->getId();
        $return['class'] ='alert alert-success';
    }else if(!empty($mawbObj->getId())){
        $return['response'] ='success';
        $return['msg'] ='New Mawb has been created '.$parcelNotFound;
        $return['mawb'] = $mawb;
        $return['mawb_number'] = $mawbObj->getId();
        $return['class'] ='alert alert-success';
    }else{
        $return['response'] ='error';
        $return['msg'] ='There is some problem.Mawb has not been created';
        $return['class'] ='alert alert-danger';

    }
    echo json_encode($return);
    exit;
}
if(isset($_POST['action']) && $_POST['action'] == "create_pallet"){
    //Check if user logged in
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $sessionUser = SessionManager::getUser();
        //Set type P as it is pallet
        $type = 'P';
        $carrierId = 0;
        $palletCarrierGroupId = trim($_POST["pallet_carrier_group"]);
        $carrierHub = trim($_POST["carrier_hubs"]);
        $sourceCountryId = trim($_POST["source_country_id"]);
        $sourceWarehouseId = trim($_POST["source_warehouse_id"]);
        $destinationCountryId = trim($_POST["destination_country_id"]);
        $destinationWarehouseId = trim($_POST["destination_warehouse_id"]);

        $palletCarrierGroup = new PalletCarierGroup($palletCarrierGroupId);
        if (count($palletCarrierGroup) > 0) {
            $carrierId = $palletCarrierGroup->getCarrierId();
        }
        if ($carrierId > 0) {
            $carrier = new Carrier($carrierId);
            $carrierName = substr($carrier->getCarrier(), 0, 3);
            $warehouseId = $sessionUser->getWarehouseId();
            $warehouse = new Warehouse($warehouseId);
            $warehouseCode = $warehouse->getWarehouseCode();
            $userAccount = new CustomerAccount($sessionUser->getUserAccountId());
            $accountCode = $userAccount->getAccountCode();
            $pallet = new Pallet();
            $pallet->setDateCreated(time());
            $pallet->setUserId($sessionUser->getId());
            $pallet->setClose(0);
            $pallet->setPalletCarrierId($palletCarrierGroupId);
            $pallet->setType($type);
            $pallet->setHub($carrierHub);
            $pallet->setIsActive(1);
            $pallet->setPalletSourceCountryId($sourceCountryId);
            $pallet->setPalletSourceWarehouseId($sourceWarehouseId);
            $pallet->setPalletDestinationCountryId($destinationCountryId);
            $pallet->setPalletDestinationWarehouseId($destinationWarehouseId);
            $pallet->save();
            $palletNumber = sprintf("%08d", $pallet->getId());
            $palletno = strtoupper($accountCode) . strtoupper($type) . strtoupper($carrierName) . strtoupper($palletNumber) . strtoupper($warehouseCode);
            $pallet->setPalletno($palletno);
            $pallet->save();
            if($pallet->getId() > 0){
                $msg = "Pallet ".$palletno." created successfully";
                $arr = array('result' => 'success', 'message' => $msg,'pallet'=>$palletno);
            }else{
                $msg = "Pallet creating issue occcur";
                $arr = array('result' => 'error', 'message' => $msg);
            }
        } else {
            $msg = "Pallet carrier issue occcur";
            $arr = array('result' => 'error', 'message' => $msg);
        }
    } else {
        $msg = "Please login";
        $arr = array('result' => 'error', 'message' => $msg);
    }
    echo json_encode($arr);
    die;
}
/*
* Get pallet carrier group services
*/
if (isset($_POST["action"]) && $_POST["action"] == "get_carrier_service") {
   $serviceHtml = "";
   if ($_POST['carrier_group'] > 0) {
       $palletCarrierGroup = new PalletCarierGroup($_POST['carrier_group']);
       $carrierId = $palletCarrierGroup->getCarrierId();
       //Get carrier hubs
       $carrierHubsFilter = new CarrierHubsFilter();
       $carrierHubsFilter->addFieldFilter("carrier_id", $carrierId);
       $carrierHubsObj = $carrierHubsFilter->getList();

       $carrierGroup = $_POST['carrier_group'];
       $palletCarrierService = new PalletCarrierServiceFilter();
       $palletCarrierService->addFieldFilter('    carrier_group_id', $carrierGroup);
       $palletCarrierServiceObj = $palletCarrierService->getList();
       if (count($palletCarrierServiceObj) > 0) {
           foreach ($palletCarrierServiceObj as $palletCarrierService) {
               $serviceId = $palletCarrierService->getServiceId();
               $serviceObj = new Services($serviceId);
               $serviceHtml .= '<option value="' . $serviceObj->getId() . '" >' . $serviceObj->getName() . '</option>';
           }
       }
       $serviceHtml .= ":::<option value=''>Please Select</option>";
       if (count($carrierHubsObj) > 0) {
           foreach ($carrierHubsObj as $carrierHubs) {
               $serviceHtml .= '<option value="' . $carrierHubs->getId() . '">' . $carrierHubs->getHub() . '</option>';
           }
       }
   }
   echo $serviceHtml;
   die;
}


$maxAllowedWeight = 30;


if ($_POST['action'] == 'loadagents') {
    $countryname = trim($_POST['countryname']);
    $agentid = trim($_POST['agentid']);
    $service = trim($_POST['service']);

    $servicefilter = new servicefilter();
    $servicefilter->addName2SFilter($service);
    $serviceList = $servicefilter->getColumnList("id, name, code");

    if (count($serviceList) > 0) {
        $serviceObj = $serviceList[0];
        $serviceId = $serviceObj->getId();
        //echo "service id " . $serviceId;
        LoadAgents($serviceId, $agentid);
    }
}


if ($_POST['action'] == 'makeclaim') {

    $sessionUser = SessionManager::getUser();

    $id = $_POST['conid'];
    $account = $_POST['account'];

    $con = new Consignment($id);
    $con->setStatus(Consignment::STATUS_SUPPLIER_RETURNED);
    $con->setAccount($account);
    $con->save();

    $consignment_claim = new ConsignmentHold();
    $consignment_claim->setUserId($sessionUser->getId());
    $consignment_claim->setDateCreated(time());
    $consignment_claim->setComments("CLAIM MADE");
    $consignment_claim->setTrackingNumber($con->getAwb());

    $consignment_claim->save();

    $arr = array('result' => 'success');

    echo json_encode($arr);
}


if ($_POST['action'] == 'getConsignment') {
    $id = $_POST['id'];
    $con = new Consignment($id);

    $arr = array('result' => 'success',
        'id' => $id,
        'awb' => $con->getAwb(),
        'return_awb' => $con->getReturnAwb(),
        'hawb' => $con->getHawb(),
        'service' => $con->getServiceType(),
        'addressline1' => $con->getAddressLine1(),
        'addressline2' => $con->getAddressLine2(),
        'addressline3' => $con->getAddressLine3(),
        'city' => $con->getCity(),
        'postcode' => $con->getPostCode(),
        'country' => $con->getCountry(),
    );

    echo json_encode($arr);
}


if ($_POST['action'] == 'dispatched') {


    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }
    //echo $_POST['trackingnumber'];
    //$comments = trim($_POST['comments']);
    $ConIdArr = array_unique(json_decode($_POST['conIdArr']));

    //print_r($ConIdArr);
    //die;

    $action = strtolower(trim($_POST['returnaction']));

    //echo "kazim";
    //print_r($trackingnumberArr);
    //die;
    $holdCount = 0;

    if (count($ConIdArr) > 0) {

        $strFoundConList = "'" . implode("','", $ConIdArr) . "'";
        $setColumns = "consignment_status = 'dispatched'";
        /////////////// where condition ////////////////
        $where = " Id IN($strFoundConList)";

        //echo $setColumns . " " . $where;	
        Consignment::bulkUpdate($setColumns, $where);
    }

    foreach ($ConIdArr as $conId) {
        $con = new Consignment($conId);

        $trackDataHoldArr[$holdCount]['userid'] = $sessionUser->getId();
        $trackDataHoldArr[$holdCount]['comments'] = $comments;
        $trackDataHoldArr[$holdCount]['tracking_number'] = $con->getAwb();
        $trackDataHoldArr[$holdCount]['date_created'] = date("Y-m-d H:i:s");
        $trackDataHoldArr[$holdCount++]['action'] = 'DISPATCHED';
    }

    ConsignmentHold::bulkDataInsert($trackDataHoldArr);

    $arr = array('result' => 'success',
        'message' => 'Shipments dispatched.');

    $con_filter = new ConsignmentFilter();
    $con_filter->addIdArrayFilter($ConIdArr);
    $con_list = $con_filter->getColumnList("awb");
    $arr_Tracking_Numbers = array();

    foreach ($con_list as $con) {
        $arr_Tracking_Numbers[] = $con->getAwb();
    }

    //$user = new CustomerAccount(120);					 

    TrackingData::SendEmail($arr_Tracking_Numbers, $sessionUser, '', "RETURN");


    echo json_encode($arr);
}



if ($_POST['action'] == 'savereturnaction') {


    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }
    //echo $_POST['trackingnumber'];
    $comments = trim($_POST['comments']);
    $ConIdArr = array_unique(json_decode($_POST['conIdArr']));
    $action = strtolower(trim($_POST['returnaction']));

    //echo "kazim";
    //print_r($trackingnumberArr);
    //die;
    $holdCount = 0;

    $strFoundConList = implode("','", $ConIdArr);
    $setColumns = "consignment_status = '$action'";
    /////////////// where condition ////////////////
    $where = " Id IN('$strFoundConList')";
    Consignment::bulkUpdate($setColumns, $where);

    foreach ($ConIdArr as $conId) {
        $con = new Consignment($conId);

        $trackDataHoldArr[$holdCount]['userid'] = $sessionUser->getId();
        $trackDataHoldArr[$holdCount]['comments'] = $comments;
        $trackDataHoldArr[$holdCount]['tracking_number'] = $con->getAwb();
        $trackDataHoldArr[$holdCount]['date_created'] = date("Y-m-d H:i:s");
        $trackDataHoldArr[$holdCount++]['action'] = strtoupper($action);
    }

    ConsignmentHold::bulkDataInsert($trackDataHoldArr);

    $arr = array('result' => 'success',
        'message' => 'Shipment(s) will be ' . $action);



    echo json_encode($arr);
}


if ($_POST['action'] == 'relabelshipment') {


    include_classes([
    'pdfmerger'
    ], 'labels');


    $user = SessionManager::getUser();

    if ($user->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }



    //$trackingnumberArr = array_unique(json_decode($_POST['trackingnumber']));
    $conIdList = json_decode($_POST['conIdArr']);
    //echo "<pre>";
    //print_r($conIdList);
    //echo $conid;
    //$action = strtolower(trim($_POST['returnaction']));		
    $count = 0;

    $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetX(1.0);
    $pdfMerger = new PDFMerger();

    $labelCount = 0;

    foreach ($conIdList as $conid) {

        $consignmntRelabel = new ConsignmentRelabel();

        $con = new Consignment($conid);

        $consignmntRelabel->setAccount($con->getAccount());
        $consignmntRelabel->setOldTrackingNo($con->getAwb());


        $oldTrackingNumber = $con->getAwb();

        $handling = str_replace("RTN", "", $con->getHandling());
        $con->setHandling($handling);
        $con->setAwb("");
        $con->setMessage("");
        $con->setStatus(Consignment::STATUS_READY_TO_PRINT);
        $con->setSinglelabel("");

        $con->save();

        $status = CreateLabel($con, $oldTrackingNumber);

        //echo $status;

        $results[$count]['id'] = $con->getId();
        $results[$count]['status'] = $con->getStatus();
        $results[$count]['message'] = $con->getMessage();

        if ($status == 'SUCCESS') {
            $results[$count]['label'] = $con->getSingleLabel();
            $results[$count++]['trackingnumber'] = $con->getAwb();

            $labelCount++;
            $pdfMerger->addPDF($con->getSingleLabel(), 'all');
            $consignmntRelabel->setNewTrackingNo($con->getAwb());
            $consignmntRelabel->setDateCreated(time());
            $consignmntRelabel->setUserId($user->getId());
            $consignmntRelabel->save();
        }
    }

    //echo $labelCount;

    if ($labelCount > 0) {

        $outFile = '../_assets/mergelabel' . time() . '.pdf';
        $pdf->Output($outFile, "F");
        $pdfMerger->merge('file', $outFile);

        $results['mergelabel'] = $outFile;
    }

    //echo "<pre>";
    //print_r($results);
    //die;


    echo json_encode($results);

    /* if($results == 'SUCCESS')
      {

      $arr = array('result'=>'success',
      'label' => $con->getSingleLabel(),
      'trackingnumber' => $con->getAwb());
      }
      else
      {
      $arr = array('result'=>'error',
      'message' => $con->getMessage(),
      );
      } */


    //echo json_encode($arr);
}


if ($_POST['action'] == 'senddispatchemailtohub') {
    $manifestid = $_POST['manifestid'];
    $hub = $_POST['hub'];

    //echo "manifest id " . $manifestid . "hub : " . $hub;

    DispatchManifestEmail($manifestid, $hub);
}


if ($_POST['action'] == 'loadreadytodispatchreturns') {



    //$table = '<table>';




    $sessionUser = SessionManager::getUser();
    $account = $sessionUser->getAccount();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        //echo json_encode($arr);
        echo $msg;
        return;
    }

    //echo "kazim";
    //die;

    $status = Consignment::STATUS_SUPPLIER_READY_TO_DISPATCH;

    //$dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    //$dateTo = date("Y-m-d");
    //$consignment_filter = new ConsignmentFilter();
    //$consignment_filter->addStatusFilter($status);
    //$consignment_filter->addGroupByClause("handling");
    //$con_list = $consignment_filter->getColumnList("serice_type");
    //echo "<pre>";


    $con_list = Consignment::GetListOfSupplierScannedReturnShipments(0, $status, $account);

    $table .= '<div id="table_container" class="main_grid2"><table class="consignment_list_tbl table table-striped table-bordered table-advance table-hover" >';

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= 'Select';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Date Created';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Account';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Processed By';
    $table .= '</th>';
    $table .= '</tr>';

    $count = 0;
    foreach ($con_list as $con) {
        $table .= '<tr>';
        $table .= '<td width="20px">';
        $table .= '<input id="chkSelectDispatch[]" name="chkSelectDispatch[]" 
				     value="' . $con['id'] . '" type="checkbox" checked  />';
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['date_submitted'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['account'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['awb'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['processedby'];
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }



    $table .= '</table></div>';

    if (count($con_list) > 0) {
        $table .= '<div class="main_formpage">
			       <!--<h1 class="heading" style="margin:2px 0 5px; font-size: 25px">Add New Consignment</h1>-->
				   <div class="form_container"><div class="gray_container" style="padding: 5px">
				   <table>';
        $table .= '<tr>';
        /* $table .= '<td>';
          $table .= '<label>Select Action</label>';
          $table .= '</td>';
          $table .= '<td><div class="first_form_col">';
          $table .= '<select class="select_dropdown_con" id="ddlSelectAction" name="ddlSelectAction">';
          $table .= '<option value="RELABEL">RELABEL</option>';
          $table .= '<option value="DISPATCH">DISPATCH</option>';
          $table .= '</select></div>';
          $table .= '</td>'; */
        $table .= '<td>';
        $table .= '<div class="form_buttons">';
        $table .= '<a href="javascript:;" id="btnDispatch" onclick="Dispatch();" class="btn_save" 
				   style="padding: 5px 0 1px 72px; margin-top:1px">Dispatch</a>';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        /* $table .= '<tr>';
          $table .= '<td>';
          $table .= '<label>Enter Comments</label>';
          $table .= '</td>';
          $table .= '<td><div class="first_form_col">';
          $table .= '<input id="txtReturnComments" width="211px" name="txtReturnComments" type="text" />';
          $table .= '</div>';
          $table .= '</td>';
          $table .= '</tr>'; */

        $table .= '</table></div></div></div>';
    }

    echo $table;
}

if ($_POST['action'] == 'loadputonholdreturns') {



    //$table = '<table>';




    $sessionUser = SessionManager::getUser();
    $account = $sessionUser->getAccount();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        //echo json_encode($arr);
        echo $msg;
        return;
    }

    //echo "kazim";
    //die;

    $status = Consignment::STATUS_HOLD_RETURN;

    //$dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    //$dateTo = date("Y-m-d");
    //$consignment_filter = new ConsignmentFilter();
    //$consignment_filter->addStatusFilter($status);
    //$consignment_filter->addGroupByClause("handling");
    //$con_list = $consignment_filter->getColumnList("serice_type");
    //echo "<pre>";


    $con_list = Consignment::GetListOfSupplierScannedReturnShipments(0, $status, $account);

    $table .= '<div id="table_container" class="main_grid2"><table class="consignment_list_tbl table table-striped table-bordered table-advance table-hover" >';

    /* $table .= '<tr>';
      $table .= '<th width="20%">';
      $table .= 'Select';
      $table .= '</th>'; */
    $table .= '<th width="20%">';
    $table .= 'Date Created';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Account';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Processed By';
    $table .= '</th>';
    $table .= '</tr>';

    $count = 0;
    foreach ($con_list as $con) {
        $table .= '<tr>';
        /* $table .= '<td width="20px">';
          $table .= '<input id="chkSelectReturn[]" name="chkSelectReturn[]"
          value="'.$con['id'].'" type="checkbox" checked  />';
          $table .= '</td>'; */
        $table .= '<td>';
        $table .= $con['date_submitted'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['account'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['awb'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['processedby'];
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }



    $table .= '</table></div>';

    if (count($con_list) > 0) {
        /* $table .= '<div class="main_formpage">
          <!--<h1 class="heading" style="margin:2px 0 5px; font-size: 25px">Add New Consignment</h1>-->
          <div class="form_container"><div class="gray_container" style="padding: 5px">
          <table>';
          $table .= '<tr>';
          $table .= '<td>';
          $table .= '<label>Select Action</label>';
          $table .= '</td>';
          $table .= '<td><div class="first_form_col">';
          $table .= '<select class="select_dropdown_con" id="ddlSelectAction" name="ddlSelectAction">';
          $table .= '<option value="RELABEL">RELABEL</option>';
          $table .= '<option value="DISPATCH">DISPATCH</option>';
          $table .= '</select></div>';
          $table .= '</td>';
          $table .= '<td>';
          $table .= '<div class="form_buttons">';
          $table .= '<a href="#" id="btnSaveAction" onclick="saveReturnAction();" class="btn_save"
          style="padding: 5px 0 1px 72px; margin-top:1px">Save</a>';
          $table .= '</div>';
          $table .= '</td>';
          $table .= '</tr>';

          $table .= '<tr>';
          $table .= '<td>';
          $table .= '<label>Enter Comments</label>';
          $table .= '</td>';
          $table .= '<td><div class="first_form_col">';
          $table .= '<input id="txtReturnComments" width="211px" name="txtReturnComments" type="text" />';
          $table .= '</div>';
          $table .= '</td>';
          $table .= '</tr>';

          $table.='</table></div></div></div>'; */
    }

    echo $table;
}

if ($_POST['action'] == 'loadholdreturns') {


    //$table = '<table>';			

    $sessionUser = SessionManager::getUser();




    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        //echo json_encode($arr);
        echo $msg;
        return;
    }

    //echo "kazim";
    //die;

    $status = Consignment::STATUS_HOLD_RETURN;

    //$dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    //$dateTo = date("Y-m-d");
    //$consignment_filter = new ConsignmentFilter();
    //$consignment_filter->addStatusFilter($status);
    //$consignment_filter->addGroupByClause("handling");
    //$con_list = $consignment_filter->getColumnList("serice_type");
    //echo "<pre>";


    $con_list = Consignment::GetListOfSupplierScannedReturnShipments($sessionUser->getId(), $status);



    $table .= '<div id="table_container" class="main_grid2"><table class="consignment_list_tbl table table-striped table-bordered table-advance table-hover" >';

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= 'Select';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Date Created';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Account';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Processed By';
    $table .= '</th>';
    $table .= '</tr>';

    $count = 0;
    foreach ($con_list as $con) {
        $table .= '<tr>';
        $table .= '<td width="20px">';
        $table .= '<input id="chkSelectReturn[]" name="chkSelectReturn[]" 
				     value="' . $con['id'] . '" type="checkbox" checked  />';
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['date_submitted'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['account'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['awb'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['processedby'];
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }



    $table .= '</table></div>';

    if (count($con_list) > 0) {
        $table .= '<div class="main_formpage">
			       <!--<h1 class="heading" style="margin:2px 0 5px; font-size: 25px">Add New Consignment</h1>-->
				   <div class="form_container"><div class="gray_container" style="padding: 5px">
				   <table>';
        $table .= '<tr>';
        $table .= '<td>';
        $table .= '<label>Select Action</label>';
        $table .= '</td>';
        $table .= '<td><div class="first_form_col">';
        $table .= '<select class="select_dropdown_con" id="ddlSelectAction" name="ddlSelectAction">';
        $table .= '<option value="RELABEL">RELABEL</option>';
        $table .= '<option value="READY TO DISPATCH">DISPATCH</option>';
        $table .= '</select></div>';
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<div class="form_buttons">';
        $table .= '<a href="javascript:;" id="btnSaveAction" onclick="saveReturnAction();" class="btn_save" 
				   style="padding: 5px 0 1px 72px; margin-top:1px">Save</a>';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '<tr>';
        $table .= '<td>';
        $table .= '<label>Enter Comments</label>';
        $table .= '</td>';
        $table .= '<td><div class="first_form_col">';
        $table .= '<input id="txtReturnComments" width="211px" name="txtReturnComments" type="text" />';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '</table></div></div></div>';
    }

    echo $table;
}

if ($_POST['action'] == 'loadrelabelreturns') {


    $sessionUser = SessionManager::getUser();
    $account = $sessionUser->getAccount();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        echo $msg;
        return;
    }

    $status = Consignment::STATUS_RELABEL;

    //$con_list = Consignment::GetListOfSupplierScannedReturnShipments($sessionUser->getId(), $status, $account);	

    $con_list = Consignment::GetListOfSupplierScannedReturnShipments(0, $status, $account);

    $table .= '<div id="table_container" class="main_grid2"><table class="table table-striped table-bordered table-advance table-hover consignment_list_tbl" >';

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= 'Select';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Date Created';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Corporate Acc.';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Account';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Old Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'New Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Label';
    $table .= '</th>';
    $table .= '</tr>';




    $count = 0;
    foreach ($con_list as $con) {


        $userFilter = new UserAccountFilter();
        $userFilter->addAccountNumberFilter($con['account']);
        $userList = $userFilter->getColumnList("parentid");

        //echo "<pre>";
        //print_r($userList);

        if (count($userList) > 0) {
            $userobj = $userList[0];
            $userParent = new CustomerAccount($userobj->getParentId());
            //print_r($userParent);
        }


        $table .= '<tr>';
        $table .= '<td width="20px">';
        $table .= '<input id="chkSelectRelabel[]" name="chkSelectRelabel[]" 
						 value="' . $con['id'] . '" type="checkbox" checked  />';
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['date_submitted'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $userParent->getAccount();
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['account'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['awb'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<label id="trackingLink-' . $con['id'] . '"></label>
					   <img width="20" style="position: relative; display: none;" class="loading-' . $con['id'] . '                       src="loading.gif">';
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<a style="display:none" target="_blank" id="labelLink-' . $con['id'] . '">View Label</a>';
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }



    $table .= '</table></div>';

    if (count($con_list) > 0) {
        $table .= '<div class="main_formpage">
					   <!--<h1 class="heading" style="margin:2px 0 5px; font-size: 25px">Add New Consignment</h1>-->
					   <div class="form_container"><div class="gray_container" style="padding: 5px">
					   <table>';
        $table .= '<tr>';
        $table .= '<td>';
        $table .= '<div class="form_buttons" style="display:inline">';
        $table .= '<a href="javascript:;" id="btnRelabel" onclick="relabelShipment();" class="btn btn-primary btn_save"  style="">Relabel</a>';

        /* $table .= '<a href="#" id="btnHold" onclick="holdShipment();" class="btn_save" 
          style="padding: 5px 0 1px 72px; margin-top:1px">Hold</a>'; */

        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '</table></div></div></div>';
    }

    //$arr = array('result'=>'success', 'message' => $table);	
    echo $table;
    //echo $table;
}


if ($_POST['action'] == 'savereturn') {

    $label = "";
    $sessionUser = SessionManager::getUser();

    $country_iso = $sessionUser->getCountry();

    $countryFilter = new CountryFilter();
    $countryFilter->addIsoFilter($country_iso);
    $country_list = $countryFilter->getColumnList("name");

    if (count($country_list) > 0)
        $trackpoint = $country_list[0]->getName();

    if (trim($trackpoint) == '') {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }




    $trackingno = trim(@$_POST['trackingno']);
    $trackingnonew = trim(@$_POST['trackingnonew']);
    $sectionid = trim(@$_POST['sectionid']);
    $shelfid = trim(@$_POST['shelfid']);

    if ($trackingno == '') {
        $arr = array('result' => 'error', 'message' => Translation::GetCaption("MSG_PLEASE_ENTER_TRACKING_NUMBER"));
        echo json_encode($arr);
        return;
    }

    $trackingno = str_replace('JJD', 'JD', $trackingno);
    /////////// YODEL SCANNING WE NEED TO REPLACE JJD WITH JD

    $track_arr = str_split($trackingno);

    if ($track_arr[0] == '%') { //DPD germany ignore first 8 and last 7 characters
        $trackingno = substr($trackingno, 8, 22 - 8);
    }

    $con = new ConsignmentFilter();
    $con->addAwbAndHawbOrFilter($trackingno);
    $con_res = $con->getList();

    $parcelfilter = new ParcelFilter();
    $parcelfilter->addLicensePlateFilter($trackingno);
    $parcel_list = $parcelfilter->getColumnList("id, consignment_id, licence_plate");

    if (count($con_res) > 0) {

        $con_res = $con_res[0];

        //$con_res->setConsignmentStatus(Consignment::STATUS_RETURNED);
        $con_res->setReturnAwb($trackingnonew);
        //$con_res->setLocation($location);
        //$con_res->setHandling($returnHandling);				 				 

        $con_res->save();


        $hub = $sessionUser->getHub();
        $country_iso = $sessionUser->getCountry();
        $countryFilter = new CountryFilter();
        $countryFilter->addIsoFilter($country_iso);
        $country_list = $countryFilter->getColumnList("name");

        if (count($country_list) > 0)
            $trackpoint = $country_list[0]->getName();

        //echo $trackpoint . $hub . $con_res->getAwb();

        /* $filter = new TrackingDataFilter();			
          $filter->TrackPointExist($con_res->getAwb(), "Returned", $trackpoint, trim($hub));
          $list = $filter->getColumnList("id");
         */

        //if(count($list) == 0)				 
        TrackingData::AddVirtualTrackingToScanParcels(array($con_res->getAwb()), $sessionUser, "Returned");

        AddReturnConsignment($con_res);







        if ($sessionUser->getReturnLabel() == 'YES') {


            if ($shelfid > 0)
                $label = AddItemInRack($con_res, $shelfid);
            else {
                $label = new GlOrderPdfReturn();
                $filename = $label->buildPDFDocuments($con_res->getId());
                $returnStringParse = explode("||", $filename);

                $label = $returnStringParse[1];

                $label = str_replace("..", BASE_URL, $label);
            }
        }


        $arr = array('result' => 'success',
            'label' => $label,
            'message' => 'Return information saved.');

        echo json_encode($arr);
    } elseif (count($parcel_list) > 0) {
        //$con_res = $con_res[0];

        $parcel = $parcel_list[0];
        TrackingData::AddVirtualTrackingToScanParcels(array($parcel->getTrackingNumber()), $sessionUser, "Returned");

        $con_id = $parcel->getConsignmentId();

        $con_res = new Consignment($con_id);
        //AddReturnConsignment($con_res);			 

        $label = new GlOrderPdfReturn();
        $filename = $label->buildPDFDocuments($con_res->getId(), $parcel->getId());
        $returnStringParse = explode("||", $filename);

        $label = $returnStringParse[1];

        $label = str_replace("..", BASE_URL, $label);

        $arr = array('result' => 'success',
            'label' => $label,
            'message' => 'Return information saved.');

        echo json_encode($arr);
    } else {
        $arr = array('result' => 'error',
            'message' => 'Tracking number does not exist.');

        echo json_encode($arr);
    }
}

function AddItemInRack($consignment, $rack_shelf_id) {

    $sessionUser = SessionManager::getUser();

    $dimension = "";

    $parcelFilter = new ParcelFilter();
    $parcelFilter->addLicensePlateFilter($consignment->getAwb());
    $parcelList = $parcelFilter->getList();

    if (count($parcelList) > 0) {
        $parcel = $parcelList[0];

        $dimension = $parcel->getLength() . "x" .
                $parcel->getWidth() . "x" .
                $parcel->getHeight();
    }







    $fieldsVal = array('goods_name' => $consignment->getDescription(),
        'description' => $consignment->getDescription(),
        'weight' => $consignment->getWeight(),
        'dimension' => $dimension,
        'added_date' => date("Y-m-d H:i:s"),
        'added_by' => $sessionUser->getId(),
        'updated_date' => date("Y-m-d H:i:s"),
        'tracking_number' => $consignment->getAwb()
    );
    $ShelfItemObj = new RackShelfItem($fieldsVal);
    $ShelfItemObj->save(true);


    $userFilter = new UserAccountFilter();
    $userFilter->addAccountNumberFilter($consignment->getAccount());
    $userList = $userFilter->getColumnList("id, user_account");

    if (count($userList) > 0) {
        $userObj = $userList[0];
        $customerId = $userObj->getId();
    }



    //$rack_shelf_id = $_POST['rackShelf'];
    $rack_shelf_item_id = $ShelfItemObj->getId();
    if (!empty($rack_shelf_item_id) && $rack_shelf_item_id > 0) {
        $logFieldsVal = array('rack_shelf_id' => $rack_shelf_id,
            'rack_shelf_item_id' => $rack_shelf_item_id,
            'customer_id' => $customerId,
            'in_date' => date("Y-m-d H:i:s"),
            'in_by' => $sessionUser->getId()
        );


        $LogShelfItem = new LogRackShelf($logFieldsVal);
        $LogShelfItem->save(true);
        $log_shelf_item_id = $LogShelfItem->getId();
        if (!empty($log_shelf_item_id) && $log_shelf_item_id > 0) {
            $rackShelfObj = new RackShelf($rack_shelf_id);
            $rackShelfObj->setIsFilled(1);
            $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
            $rackShelfObj->setUpdatedBy($sessionUser->getId());
            $rackShelfObj->save(true);
            // Label Info
            $labelInfo = getLabelInfo($rack_shelf_id, 'in');
            $label = new GlOrderPdfReturnRack();
            $label->AddLabelInfo($labelInfo);
            $filename = $label->buildPDFDocuments();
            //echo $filename;
        }
    }


    return $filename;


    //exit;
}

function getLabelInfo($shelf_id, $type) {
    $rackShelfItemObj = new LogRackShelfFilter();
    $currentShelfItem = $rackShelfItemObj->getShelfLabelInfo($shelf_id);

    //$rackShelfItem = new RackShelfItem($currentShelfItem->getRackShelfItemId());

    $log_shelf_id = $currentShelfItem['log_shelf_id'];

    $shelf_no = $currentShelfItem['shelf_no'];
    $short_title = $currentShelfItem['short_title'];


    //die;

    $inByObj = new CustomerAccount($currentShelfItem['in_by']);
    $CustomerObj = new CustomerAccount($currentShelfItem['customer_id']);
    $output = array();

    $shelfName = $short_title . " " . ($shelf_no + 1);
    $title = '';
    switch ($type) {
        case 'in':
            $title = 'Rack Location: ' . $shelfName;
            break;
        case 'out':
            $title = 'Rack Location: ' . $shelfName;
            break;
        case 'shift':
            $title = 'Rack Location: ' . $shelfName;
            break;
        case 'edit':
            $title = 'Rack Location: ' . $shelfName;
            break;
    }
    $output['Title'] = $title;
    $output['Barcode_Id'] = str_pad($shelf_id, 10, "0", STR_PAD_LEFT);
    $output['TrackingNumber'] = $currentShelfItem['tracking_number'];

    $output['Customer'] = $CustomerObj->getAccount();
    $output['Item'] = $currentShelfItem['goods_name'];
    $output['Weight'] = $currentShelfItem['weight'] . "Kg";
    $output['Dimension'] = $currentShelfItem['dimension'];
    $output['Date'] = $currentShelfItem['in_date'];
    $output['Processed By'] = $inByObj->getUserAccount();

    return $output;
}

function AddReturnConsignment(Consignment $consignment) {
    $con_filter = new ConsignmentFilter();
    $con_filter->addawbFilterList("'" . $consignment->getAwb() . "'");
    $con_filter->addServiceCodeArrayFilter("'RTN" . $consignment->getHandling() . "'");
    //$con_filter->addStatusFilter(array(Consignment::STATUS_SUPPLIER_RETURNED));
    $con_list = $con_filter->getColumnList("awb");

    $sessionUser = SessionManager::getUser();

    if (count($con_list) == 0) {

        $con_return = new Consignment();
        $con_return->setHawb($consignment->getHawb());
        $con_return->setAwb($consignment->getAwb());
        $con_return->setConsignmentStatus(Consignment::STATUS_SUPPLIER_RETURNED); // STATUS SHOULD BE returned but if we set as returned then two shipments will be uploded into cms.
        $con_return->setAccount($consignment->getAccount());
        $con_return->setService($consignment->getService());
        $con_return->setHandling("RTN" . $consignment->getHandling());
        $con_return->setScannedBy($sessionUser->getUserAccount());

        $con_return->setReference($consignment->getReference());
        $con_return->setRoutingCode($consignment->getRoutingCode());

        //$con_return->setDateReceived($consignment->getDateReceived());
        $con_return->setDateSubmitted(time());
        $con_return->setDateImported(time());
        $con_return->setDateBooked(time());
        //$con_return->setDateScanned(date("Y-m-d G:i:s"));

        $con_return->setCompany($consignment->getCompany());
        $con_return->setContact($consignment->getContact());
        $con_return->setAddressLine1($consignment->getAddressLine1());
        $con_return->setAddressLine2($consignment->getAddressline2());
        $con_return->setAddressLine3($consignment->getAddressline3());
        $con_return->setCity($consignment->getCity());
        $con_return->setCountry($consignment->getCountry());
        $con_return->setPostCode($consignment->getPostCode());
        $con_return->setCountryIsoCode($consignment->getCountryIsoCode());
        $con_return->setTelephone($consignment->getTelephone());
        $con_return->setNumberPieces($consignment->getNumberPieces());
        $con_return->setWeight($consignment->getWeight());
        $con_return->setVolWeight($consignment->getVolWeight());
        $con_return->setDescription($consignment->getDescription());
        $con_return->setValue($consignment->getValue());
        $con_return->setCurrency($consignment->getCurrency());
        $con_return->setNotes($consignment->getNotes());
        $con_return->setRoutingCode($consignment->getRoutingCode());
        $con_return->setServiceType($consignment->getServiceType()); /// USER ENTERED SERVICE
        //$con_return->setMawb($consignment->getMawb());
        $con_return->setBagNumber($consignment->getBagNumber());
        $con_return->setBagWeight($consignment->getBagWeight());
        $con_return->setReturnAwb($consignment->getReturnAwb());
        $con_return->setRemoteCharges($consignment->getRemoteCharges());
        $con_return->setUserCode($consignment->getUserCode());
        $con_return->setWarehouseId($consignment->getWarehouseId());

        $con_return->save();
    } else {
        $con_return = $con_list[0];
        $con_return->setScannedBy($sessionUser->getUserAccount());
        $con_return->setConsignmentStatus(Consignment::STATUS_SUPPLIER_RETURNED);
        $con_return->save();
    }
}

if ($_POST['action'] == 'returnaccount') {
    $account = "";

    $trackingno = $_POST['trackingno'];

    $trackingno = str_replace('JJD', 'JD', $trackingno); /////////// YODEL SCANNING WE NEED TO REPLACE JJD WITH JD

    $track_arr = str_split($trackingno);

    if ($track_arr[0] == '%') { //DPD germany ignore first 8 and last 7 characters
        $trackingno = substr($trackingno, 8, 22 - 8);
    }

    $c_filter = new ConsignmentFilter();
    $c_filter->addAwbAndHawbOrFilter($trackingno);
    $list = $c_filter->getColumnList("account");

    if (count($list) > 0) {
        $list = $list[0];
        $account = $list->getAccount();
        $arr = array('result' => 'success',
            'message' => $account);
    } else {
        $arr = array('result' => 'error',
            'message' => 'shipment record does not exist in the system.');
    }



    echo json_encode($arr);
}


if ($_POST['action'] == 'resolveholdparcels') {

    $user = SessionManager::getUser();

    $userId = $user->getId();

    $trackingNumberArr = json_decode(stripslashes($_POST['trackingnumber']));
    $comments = trim($_POST['comments']);

    //print_r($trackingNumberArr);
    //die;

    if (is_array($trackingNumberArr) && count($trackingNumberArr) > 0) {
        foreach ($trackingNumberArr as $trackingNumber) {
            $consignmentHold = new ConsignmentHold();
            $consignmentHold->setTrackingNumber($trackingNumber);
            $consignmentHold->setComments($comments);
            $consignmentHold->setDateCreated(time());
            $consignmentHold->setUserId($userId);
            $consignmentHold->save();
        }

        $strTrackingNumber = "'" . implode("','", $trackingNumberArr) . "'";

        $set_update_columns = "consignment_status = 'received'";
        $where = "awb IN ($strTrackingNumber)";

        Consignment::bulkUpdate($set_update_columns, $where);

        $arr = array('result' => 'success',
            'message' => 'Hold status has been removed for all the selected tracking numbers.');



        echo json_encode($arr);
    } else {
        $arr = array('result' => 'error',
            'message' => 'Some error has occurred.');
    }
}

function DispatchManifestEmail($manifestId, $warehouseid) {


    $sessionUser = SessionManager::getUser();
    ///// $hub = $sessionUser->getHub(); //////////// sender

    $manifest = new Manifest($manifestId);
    $account = $manifest->getAccount();
    //$userFilter = new UserAccountFilter();
    //$userFilter->addUserAccountFilter($account);
    //$userList = $userFilter->getList();
    //$to = HubList::GetHubEmail($hub);

    $warehouse = new Warehouse($warehouseid);
    $to = $warehouse->getEmail();


    $hub = $warehouse->getHub();

    //if($to != '')		
    //{
    //$userObj = $userList[0];
    $from = $sessionUser->getEmail();

    $headers = "From: $from \r\n";
    $headers .= "Reply-To: $from \r\n";
    //$headers .= "Cc: $from, $to, ITSupport@oneworldexpress.com" . "\r\n";				
    $headers .= "Cc: $from, $to" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = "Dear Sir/Madam, <BR><BR>Please note that the manifest number $manifestId has been                             routed to " . $hub . ".<BR><BR>";

    $message .= "PDF File : " . BASE_URL . str_replace("../", "", $manifest->getPdfFile()) . "<BR>";
    $message .= "CSV File : " . BASE_URL . str_replace("../", "", $manifest->getFileName()) . "<BR>";

    $message .= "<BR><BR>Kind Regards<BR>ITSupport";

    mail($to, "Manifest number $manifestId Routed to $hub", $message, $headers);

    $manifest->setRouteWarehouseId($warehouseid);
    $manifest->setRoutingEmailDate(date("Y-m-d G:i"));
    $manifest->save();

    //}
}

function ReceiveManifestEmail($manifestId) {


    $sessionUser = SessionManager::getUser();
//    var_dump($sessionUser);die;
    $hub = $sessionUser->getWarehouseId();

    $manifest = new Manifest($manifestId);
    $account = $manifest->getAccount();


    $manifest->setDateReceived(time());
    $manifest->setReceivedBy($sessionUser->getAccount());
    $manifest->save();

    $emailFrom = $sessionUser->getEmail();




    $userFilter = new UserAccountFilter();
    $userFilter->addUserAccountFilter($account);
    $userList = $userFilter->getList();


    if ($hub != '' && count($userList) > 0) {
        $userObj = $userList[0];
        $to = $userObj->getEmail();

        $headers = "From: " . $emailFrom . " \r\n";
        $headers .= "Reply-To: " . $emailFrom . " \r\n";
        $headers .= 'Cc: CS@oneworldexpress.com, kazim@oneworldexpress.com, ' . $emailFrom . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = "Dear Sir/Madam, <BR><BR>Please note that the manifest number $manifestId has been                             received at " . $hub . ".<BR><BR>Kind Regards<BR>ITSupport";

//        mail($to, "Manifest number $manifestId Received at $hub", $message, $headers);
    }
}

if ($_POST["action"] == "getTrackingNumbersByManifestNumber") {

    $manifestid = trim(@$_POST['manifestid']);

    if ($manifestid !== "") {

        
        $manifest_filter = new ManifestConsignmentDataFilter();
        $manifest_filter->addManifestIDFilter($manifestid);
        //$con_filter->addStatusFilterNotIn(Consignment::STATUS_HOLD);
        $manifest_list = $manifest_filter->getColumnList("consignmentid");

        $con_id_array = array();
        $trackingNumberArr = array();

        if (count($manifest_list) > 0) {
            foreach ($manifest_list as $manifest) {
                $con_id_array[] = $manifest->getConsignmentId();
            }

            $con_filter = new ConsignmentFilter();
            $con_filter->addIdArrayFilter($con_id_array);
            $con_list = $con_filter->getColumnList("awb", count($con_id_array));

            foreach ($con_list as $con) {
                $trackingNumberArr[] = $con->getAwb();
            }
        }

        if (count($trackingNumberArr) > 0) {

            $arr = array('result' => 'success',
                'trackingNumbers' => $trackingNumberArr);

            ReceiveManifestEmail($manifestid);
        } else {
            $arr = array('result' => 'error',
                'trackingNumbers' => $trackingNumberArr);
        }

        echo json_encode($arr);
    }
}

/*
 * Scenario 301
 * Get: Box number
 * Return:  json encoded array
 */
if ($_POST["action"] == "getTrackingNumbersByBagNumber") {
    $boxNumber = trim(@$_POST['boxnumber']);
    if ($boxNumber !== "") {
        /*
         * Scenario 301
         * Get: Box number
         * Return:  consignment array
         */
        $list = getParcelListFromBagNumber($boxNumber);
        $trackingNumberArr = array();
        foreach ($list as $con) {
            $trackingNumberArr[] = $con->getTrackingNumber();
        }
        if (count($trackingNumberArr) > 0) {
            $arr = array('result' => 'success',
                'trackingNumbers' => $trackingNumberArr);
        } else {
            $arr = array('result' => 'error',
                'trackingNumbers' => $trackingNumberArr);
        }
        echo json_encode($arr);
    }
}


if ($_POST["action"] == 'checkpalletstatus') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $palletno = trim(@$_POST['palletnumber']);
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletno);
        $palletList = $palletFilter->getList();
        $pallet = 0;
        if (count($palletList) > 0) {
            $pallet = $palletList[0];
            $palletId = $pallet->getId();
        }
        if ($palletId > 0) {
            if (trim($pallet->getDateDispatch()) !== "") {
                $arr = array('result' => 'dispatch',
                    'message' => 'Pallet already dispatched.');
            } else if ($pallet->getClose() == '1') {
                $arr = array('result' => 'error',
                    'message' => $palletno . ' ' . Translation::GetCaption("MSG_PALLET_ALREADY_CLOSED"));
            } else {
                $arr = array('result' => 'success',
                    'message' => $palletno . ' ' . Translation::GetCaption("MSG_PALLET_ALREADY_OPENED"));
            }
        } else {
            $arr = array('result' => 'success',
                'message' => 'Pallet number does not exist.');
        }
    echo json_encode($arr);
    } else {
       echo $chkSession;
    }
    die;
}

if ($_POST["action"] == 'dispatchpallet') {
    CheckUserSession();
    $sessionUser = SessionManager::getUser();
    $palletno = trim(@$_POST['palletnumber']);
    $palletFilter = new PalletFilter();
    $palletFilter->addPalletNumberFilter($palletno);
    $palletList = $palletFilter->getList();
    if (count($palletList) > 0) {
        $pallet = $palletList[0];
        $palletId = $pallet->getId();
        $dispatchdate = $_POST['dispatchdate'];
        $arr_update_scan_numbers = array();
        if ($palletId > 0) {
            if ($pallet->getClose() == '0') {
                $arr = array('result' => 'error',
                    'message' => Translation::GetCaption("MSG_PALLET_NOT_CLOSED"));
            } elseif (trim($pallet->getDateDispatch()) !== "") {
                $userID = $pallet->getDispatchUserId();
                $userName = '';
                if ($userID > 0) {
                    $userAccount = new User($userID);
                    $userName = $userAccount->getUserName();
                }
                $arr = array('result' => 'error',
                    'message' => Translation::GetCaption("MSG_PALLET_ALREADY_DISPATCHED") . ' ' . $userName);
            } else {
                $dispatchdate = date("Y-m-d G:i", strtotime($dispatchdate));
                $pallet->setDateDispatch(strtotime($dispatchdate));
                $pallet->setDispatchUserId($sessionUser->getId());
                $pallet->save();
                
                $arr_update_scan_numbers = getTrackingNumbersFromPalletNumber($palletno);
                if (count($arr_update_scan_numbers) > 0) {
                    $country_iso = $sessionUser->getCountry();
                    $countryFilter = new CountryFilter();
                    $countryFilter->addIsoFilter($country_iso);
                    $country_list = $countryFilter->getColumnList("name");
                    if (count($country_list) > 0)
                        $trackpoint = $country_list[0]->getName();
                    TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $sessionUser, "Dispatched", $dispatchdate, "", $trackpoint);
                }
                $arr = array('result' => 'success',
                    'message' => 'Pallet ' . $palletno . ' has dispatched successfully.');
            }
        }
    }
    else {
        $arr = array('result' => 'error',
            'message' => Translation::GetCaption("MSG_PALLET_NUMBER_DOES_NOT_EXIST"));
    }
    echo json_encode($arr);
    exit;
}

function getTrackingNumbersFromPalletNumber($palletno) {
    
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->AddPalletNumberFilter($palletno);
    $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'recycled');
    $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'hold');
    $con_list = $consignmentFilter->getColumnList("awb");
    if (count($con_list) == 0) {
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletno);
        $list = $palletFilter->getList();
        if (count($list) > 0) {
            $pallet = $list[0];
            $palletid = $pallet->getId();
        }
//        $palletBagMapping = new PalletBagMappingFilter();
        $palletBagMapping = new PalletEntityMappingFilter();
        $palletBagMapping->addFieldEqualFilter("palletid", "=", $palletid);
        $palletList = $palletBagMapping->getList();

        foreach ($palletList as $palletBag) {
            $bagNumberArr[] = $palletBag->getBagId();
        }

        if (count($bagNumberArr) > 0) {
            $conArray = array();

            foreach ($bagNumberArr as $bagNumber) {
                $congBagMappingFilter = new ConsignmentBaggingMappingFilter();
                $congBagMappingFilter->addFieldEqualFilter("bagid", "=", $bagNumber);
                $conIdList = $congBagMappingFilter->getList();

                //print_r($conIdList);
                //die;				

                if (count($conIdList) > 0) {
                    foreach ($conIdList as $conId) {
                        $consignment = new Consignment($conId->getConsignmentId());
                        $arr_update_scan_numbers[] = $consignment->getAwb();
                    }
                }
            }
        }
    } else {
        foreach ($con_list as $con) {
            $arr_update_scan_numbers[] = $con->getAwb();
        }
    }

    return $arr_update_scan_numbers;
}

if ($_POST["action"] == 'openpallet') {
    $palletno = trim(@$_POST['palletnumber']);
//    $pattern = '/[^0-9]*/';
//    $palletid = preg_replace($pattern, '', $palletno);
    $palletId = 0;
    $palletFilter = new PalletFilter();
    $palletFilter->addPalletNumberFilter($palletno);
    $palletObj = $palletFilter->getColumnList("close");
    if(count($palletObj) > 0){
        $palletId = $palletObj[0]->getId();
    }
    if ($palletId > 0) {
        if ($palletObj[0]->getClose() == '1') {
            $pallet = new Pallet($palletId);
            $pallet->setClose("0");
            $pallet->save();
            $arr = array('result' => 'success',
                'message' => Translation::GetCaption("MSG_PALLET_OPENED_SUCCESSFULLY"));
        } else {
            $arr = array('result' => 'success',
                'message' => 'Pallet number ' . $palletno . ' ' . Translation::GetCaption("MSG_PALLET_OPENED_SUCCESSFULLY"));
        }
    } else {
        $arr = array('result' => 'error',
            'message' => Translation::GetCaption("MSG_PALLET_NUMBER_DOES_NOT_EXIST"));
    }
    echo json_encode($arr);
    die;
}
if ($_POST["action"] == 'dispatch') {
    $user = SessionManager::getUser();
    $palletno = trim(@$_POST['palletnumber']);
//    $pattern = '/[^0-9]*/';
//    $palletid = preg_replace($pattern, '', $palletno);
    $palletId = 0;
    $palletFilter = new PalletFilter();
    $palletFilter->addPalletNumberFilter($palletno);
    $palletObj = $palletFilter->getColumnList("id");
    if(count($palletObj) > 0){
        $palletId = $palletObj[0]->getId();
    }
    if ($palletId > 0) {
        $pallet = new Pallet($palletId);
        $pallet->setDateDispatch(time());
        $pallet->setDispatchUserid($user->getId());
        $pallet->save();
        $arr = array('result' => 'success','message' => "Pallet dispatched successfully");
    } else {
        $arr = array('result' => 'error',
            'message' => Translation::GetCaption("MSG_PALLET_NUMBER_DOES_NOT_EXIST"));
    }
    echo json_encode($arr);
    die;
}


if ($_POST["action"] == 'createpalletlabel') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $palletno = trim(@$_POST['palletnumber']);
        $output = array();
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletno);
        $list = $palletFilter->getList();
        if (count($list) > 0) {
            $pallet = $list[0];
            $palletid = $pallet->getId();
            $palletno = $pallet->getPalletNo();
            $palletCarrierId = $pallet->getPalletCarrierId();
            $pallerCarrier = new PalletCarrier($palletCarrierId);
            $carrier = $pallerCarrier->getName();

            //$carrier = $pallet->getCarrier();

            /*
              if($carrier == 'YODEL')
              {
              $result = createYodelPalletLabel($palletid);

              if($result['status'] == 'success')
              {
              $pallet_label_link	= $result['label'];
              $dpdlabellink = str_replace(BASE_URL, "../", $pallet_label_link);

              $output['result'] = 'success';
              $output['label'] = $pallet_label_link;
              }
              else
              {
              $output['result'] = 'error';
              $output['message'] = $result['message'];
              }

              }
              else
             */
            if ($carrier == 'P2') {
                $pallet_label_link = getP2PalletLabelFromPalletNumber($palletno);
            } else {
                $pallet_label_link = ExportPalletLabel::buildPDFDocuments($palletid, $palletno);
            }
            $output['result'] = 'success';
            $output['label'] = $pallet_label_link;

            $pallet->setLabel($pallet_label_link);
            $pallet->save();
        }else{
            $output['result'] = 'error';
            $output['message'] = "Pallet number does not exist.";
        }
        echo json_encode($output);
    } else {
        echo $chkSession;
    }
    die;
}

if ($_POST["action"] == 'compareBagAndScannedItems') {
    $bagno = trim(@$_POST['bagnumber']);
    $arr_trackingnumbers_scannned = explode(",", @$_POST["trackingnumbers"]);

    $con_filter = new ConsignmentFilter();
    $con_filter->addBagNumberFilter($bagno);
    $con_list = $con_filter->getColumnList("awb");
    $arr_trackingnumbers_bag = array();
    if (count($con_list) > 0) {
        $totalTrackingNumbers = count($con_list);
        foreach ($con_list as $con) {
            $arr_trackingnumbers_bag[] = $con->getAwb();
        }
    }


    $diff = array_values(array_diff($arr_trackingnumbers_bag, $arr_trackingnumbers_scannned));

    $arr = array('result' => 'success',
        'tracking_numbers' => $diff);

    echo json_encode($arr);
}

if ($_POST['action'] == 'closebag') {
    $bagno = trim(@$_POST['bagnumber']);
    $mawbNumber = trim($_POST['mawb']);
    $json_trackingnumbers = trim(@$_POST["trackingnumbers"]);
    $arr_trackingnumbers = json_decode(stripslashes($json_trackingnumbers));

    if ($bagno !== '') {
        $totalTrackingNumbers = 0;
        $mawbNumber = "";
        $con_filter = new ConsignmentFilter();
        $con_filter->addBagNumberFilter($bagno);
        $con_list = $con_filter->getColumnList("awb, service_type, mawb");
        if (count($con_list) > 0) {
            $totalTrackingNumbers = count($con_list);
            $con = $con_list[0];
            $carrierName = $con->getServiceType();
            $mawbNumber = $con->getMawb();
        }

        $boxLabel = new BoxLabel();
        $boxLabel->buildPDFDocuments($palletno, $totalTrackingNumbers, $carrierName, $mawbNumber);
        echo '../_assets/bag_pdf/' . $palletno . '.pdf';
    }
}
/*
 * Scenario 101
 * Get: pallet number
 * Process: Get pallet number and set close pallet and send pallet label link
 * Return:  pallet link
 */
if ($_POST["action"] == 'closepallet') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $palletno = trim(@$_POST['palletnumber']);
        $carrierHubs = trim(@$_POST['carrier_hubs']);
        $sessionUser = SessionManager::getUser();
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletno);
        $palletList = $palletFilter->getColumnList('id');
        $palletLabelLink = "";
        if (count($palletList) > 0) {
            $pallet = $palletList[0];
            $palletId = $pallet->getId();
            $pallet->setClose(1);
            $pallet->save();
            $palletLabelLink = ExportPalletLabel::buildPDFDocuments($pallet->getId(), $pallet->getPalletno());
        }
        echo $palletLabelLink;
    } else {
        echo $chkSession;
    }
    die;
}
/*
 * Scenario 101
 * Process: Get total weight of parcel iteams
 * Get: Parcel id
 * Return: total weight
 */

function getTotalParcelIteamWeight($parcelId) {
    $parcelWeight = 0;
    if ($parcelId > 0) {
        $parcelIteamFilter = new ParcelIteamFilter();
        $parcelIteamFilter->addFieldFilter("parcel_id", $parcelId);
        $parcelIteamObj = $parcelIteamFilter->getColumnList('iteam_weight');
        if (count($parcelIteamObj) > 0) {
            foreach ($parcelIteamObj as $parcelIteam) {
                $parcelWeight += $parcelIteam->getIteamWeight();
            }
        }
    } else {

    }
    return $parcelWeight;
}

/*
 * Scenario 101
 * Process: Get pallet id from pallet number
 * Get: Parcel number
 * Return: pallet id
 */

function getPalletIdFromPalletNumber($palletNumber) {
    $palletId = 0;
    if ($palletNumber != "") {
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletNumber);
        $palletObj = $palletFilter->getColumnList("id");
        if (count($palletObj) > 0) {
            $palletId = $palletObj[0]->getId();
        }
    } else {

    }
    return $palletId;
}

/*
 * Scenario 101
 * Process: Get entity id from pallet bagging mapping table
 * Get: Parcel id
 * Return: entity id array
 */

function getEntityIdFromPalletId($palletId) {
    $entityId = [];
    if ($palletId > 0) {
        $palletEntityMappingFilter = new PalletEntityMappingFilter();
        $palletEntityMappingFilter->addFieldEqualFilter("pallet_id", "=", $palletId);
        $palletEntityMappingFilter->addFieldEqualFilter("pallet_entity_type", "=", "b");
        $palletEntityMappingFilter->addFieldEqualFilter("pre_sort", "=", "y");
        $palletEntityMappingObj = $palletEntityMappingFilter->getColumnList("entity_id");
        if (count($palletEntityMappingObj) > 0) {
            foreach ($palletEntityMappingObj as $palletEntityMappingArr) {
                $entityId[] = $palletEntityMappingArr->getEentityId();
            }
        }
    } else {

    }
    return $entityId;
}

/*
 * Scenario 101
 * Process: Get entity id from pallet bagging mapping table
 * Get: Parcel id
 * Return: entity id array
 */

function getParcelIdFromBagId($bagId) {

    $parcelId = [];
    if (!empty($bagId)) {
        $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
        $parcelBaggingMappingFilter->addJoin("    parcel p ", " pbm.id "," p.id"," JOIN ");
        $parcelBaggingMappingFilter->addJoin("    consignment c ", " p.consignment_id "," c.id"," JOIN ");
        $parcelBaggingMappingFilter->addFieldFilter("    pbm.bag_id", $bagId);
        $parcelBaggingMappingFilter->addFieldFilter("    c.consignment_type", "outbound");
        $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("pbm.parcel_id");
        if (count($parcelBaggingMappingObj) > 0) {
            foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                $parcelId[] = $parcelBaggingMappingArr->getParcelId();
            }
        } else {

        }
    }
    return $parcelId;
}

/*
 * Scenario 101
 * Process: Get parcel iteam id from parcel iteam table
 * Get: Parcel id
 * Return: parcel iteam id array
 */

function getParcelIteamIdFromParcelId($parcelId) {

    $parcelIteamId = [];
    if (is_array($parcelId)) {
        foreach ($parcelId as $parcelIdArr) {
            $parcelIteamFilter = new ParcelIteamFilter();
            $parcelIteamFilter->addFieldFilter("parcel_id", $parcelIdArr);
            $parcelIteamobj = $parcelIteamFilter->getColumnList('id');
            if (count($parcelIteamobj) > 0) {
                foreach ($parcelIteamobj as $parcelIteamArr) {
                    $parcelIteamId[] = $parcelIteamArr->getId();
                }
            } else {

            }
        }
    }
    return $parcelIteamId;
}

/*
 * Get: carrier id, carrier hub id
 * Return: It will return 19 digit pallet id
 * Issue to handle: If carrrier hub doesnot exsist then? and some of warehouse dons't added warehouse id.
 * 19 digit description: 4 digit user account,P Type for pallet,3 digit carrier name,8 digit palllet unique number,3 digit warehouse id
 */
if (isset($_POST["action"]) && $_POST["action"] == 'createpallet') {
    //Check if user logged in
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $sessionUser = SessionManager::getUser();
        //Set type P as it is pallet
        $type = 'P';
        $carrierId = 0;
        $palletCarrierGroupId = trim($_POST["carrier"]);
        $carrierHub = trim($_POST["carrier_hub"]);
        $palletCarrierGroup = new PalletCarierGroup($palletCarrierGroupId);
        if (count($palletCarrierGroup) > 0) {
            $carrierId = $palletCarrierGroup->getCarrierId();
        }
        if ($carrierId > 0) {
            $carrier = new Carrier($carrierId);
            $carrierName = substr($carrier->getCarrier(), 0, 3);
            $warehouseId = $sessionUser->getWarehouseId();
            $warehouse = new Warehouse($warehouseId);
            $warehouseCode = $warehouse->getWarehouseCode();
            $userAccount = new CustomerAccount($sessionUser->getUserAccountId());
            $accountCode = $userAccount->getAccountCode();
            $pallet = new Pallet();
            $pallet->setDateCreated(time());
            $pallet->setUserId($sessionUser->getId());
            $pallet->setClose(0);
            $pallet->setPalletCarrierId($palletCarrierGroupId);
            $pallet->setType($type);
            $pallet->setHub($carrierHub);
            $pallet->setIsActive(1);
            $pallet->save();
            $palletNumber = sprintf("%08d", $pallet->getId());
            $palletno = strtoupper($accountCode) . strtoupper($type) . strtoupper($carrierName) . strtoupper($palletNumber) . strtoupper($warehouseCode);
            $pallet->setPalletno($palletno);
            $pallet->save();
            echo $palletno;
        } else {
            echo 0;
        }
    } else {
        echo $chkSession;
    }
    die;
}



if ($_POST["action"] == 'getManifestHandling') {
    $manifestid = $_POST["manifestid"];
    $manifest = new Manifest($manifestid);
    echo $manifest->getHandling();
}

if ($_POST['action'] == 'saveaction') {

    //echo $_POST['trackingnumber'];

    $trackingNumberArr = json_decode(stripslashes($_POST['trackingnumber']));




    $comments = $_POST['comments'];

    $sessionUser = SessionManager::getUser();

    $country_iso = $sessionUser->getCountry();

    $countryFilter = new CountryFilter();
    $countryFilter->addIsoFilter($country_iso);
    $country_list = $countryFilter->getColumnList("name");

    if (count($country_list) > 0)
        $trackpoint = $country_list[0]->getName();

    if (trim($trackpoint) == '') {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }



    $trackDataHoldArr[$holdCount]['userid'] = $sessionUser->getId();
    $trackDataHoldArr[$holdCount]['comments'] = $comments;
    $trackDataHoldArr[$holdCount]['tracking_number'] = $con->getAwb();
    $trackDataHoldArr[$holdCount++]['date_created'] = date("Y-m-d H:i:s");
    $trackingNumbers[] = $con->getAwb();
}


if ($_POST['action'] == 'loadawaitingreturns') {


    //$table = '<table>';
    $service = "";
    if (isset($_POST['service'])) {
        $service = $_POST['service'];
    }

    $serviceFilter = new ServiceFilter();
    $serviceFilter->addName2Filter($service);
    $serv_list = $serviceFilter->getColumnList("code, name");

    if (count($serv_list) > 0) {
        $servObj = $serv_list[0];
        $handling = $servObj->getCode();
        $handling = "RTN" . $handling;
    }

    //echo $service . " " . $handling;

    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        //echo json_encode($arr);
        echo $msg;
        return;
    }

    //echo "kazim";
    //die;

    $status = Consignment::STATUS_SUPPLIER_RETURNED;

    //$dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    //$dateTo = date("Y-m-d");
    //$consignment_filter = new ConsignmentFilter();
    //$consignment_filter->addStatusFilter($status);
    //$consignment_filter->addGroupByClause("handling");
    //$con_list = $consignment_filter->getColumnList("serice_type");
    //echo "<pre>";


    $con_list = Consignment::GetListOfSupplierScannedReturnShipments($sessionUser->getId(), $status, '', '', $handling);

    $serv_list = Consignment::GetListOfSupplierScannedReturnShipments($sessionUser->getId(), $status, '', 'service_type');
    //echo "<pre>";
    //print_r($serv_list);

    if (count($serv_list) > 0) {

        //$serv_dropdown .= '<table class="consignment_list_tbl">';	

        $serv_dropdown .= '<div class="first_form_col">';
        $serv_dropdown .= '<label>Select Service</label>';
        $serv_dropdown .= '<select class="select_dropdown_con" id="ddlAwaitingReturns" onchange="OnChangeAwaitingReturns();" name="ddlAwaitingReturns">';
        $serv_dropdown .= "<option value='Select All'>Select All</option>";


        foreach ($serv_list as $serviceObj) {
            $selected = "";
            if ($serviceObj['service_type'] == $service)
                $selected = " selected";
            $serv_dropdown .= "<option $selected value='" . $serviceObj['service_type'] . "'>" . $serviceObj['service_type'] . "</option>";
        }

        $serv_dropdown .= '</select></div>';
        //$serv_dropdown .= '<br />';
        //$serv_dropdown .= '</table>';	
    }



    $table .= '<div id="table_container" class="main_grid2">';

    $table .= $serv_dropdown . "<div style='clear:both'></div>";

    $table .= '<table class="consignment_list_tbl table table-striped table-bordered table-advance table-hover" >';

    //$table.= $serv_dropdown;

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= 'Select';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Date Created';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Account';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Tracking Number';
    $table .= '</th>';
    $table .= '<th width="20%">';
    $table .= 'Processed By';
    $table .= '</th>';
    $table .= '</tr>';

    $count = 0;
    foreach ($con_list as $con) {
        $table .= '<tr>';
        $table .= '<td width="20px">';
        $table .= '<input id="chkSelectReturn[]" name="chkSelectReturn[]" 
				     value="' . $con['id'] . '" type="checkbox" checked  />';
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['date_submitted'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['account'];
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<a target="_blank" href="' . BASE_URL . '/main/tracking.php?tracking_number=' . $con['awb'] . '">' . $con['awb'] . '</a>';
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con['processedby'];
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }



    $table .= '</table></div>';

    if (count($con_list) > 0) {
        $table .= '<div class="main_formpage">
			       <!--<h1 class="heading" style="margin:2px 0 5px; font-size: 25px">Add New Consignment</h1>-->
				   <div class="form_container"><div class="gray_container" style="padding: 5px">
				   <table>';
        $table .= '<tr>';
        $table .= '<td>';
        $table .= '<label>Select Action</label>';
        $table .= '</td>';
        $table .= '<td><div class="first_form_col">';
        $table .= '<select class="select_dropdown_con" id="ddlSelectAction" name="ddlSelectAction">';
        $table .= '<option value="RELABEL">RELABEL</option>';
        $table .= '<option value="RETURN HOLD">HOLD</option>';
        $table .= '<option value="READY TO DISPATCH">DISPATCH</option>';
        $table .= '</select></div>';
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<div class="form_buttons">';
        $table .= '<a href="javascript:;" id="btnSaveAction" onclick="saveReturnAction();" class="btn_save" 
				   style="padding: 5px 0 1px 72px; margin-top:1px">Save</a>';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '<tr>';
        $table .= '<td>';
        $table .= '<label>Enter Comments</label>';
        $table .= '</td>';
        $table .= '<td><div class="first_form_col">';
        $table .= '<input id="txtReturnComments" width="211px" name="txtReturnComments" type="text" />';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';

        $table .= '</table></div></div></div>';
    }

    echo $table;
}

if ($_POST['action'] == 'loadawaitingclaims') {

    //$table = '<table>';			
    $table = "";
    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        //$arr = array('result'=>'error', 'message' => $msg);	
        //echo json_encode($arr);
        echo $msg;
        return;
    }

    $status = array(Consignment::STATUS_AWAITING_CLAIM);

    $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    $dateTo = date("Y-m-d");

    $consignment_filter = new ConsignmentFilter();
    $consignment_filter->addStatusFilter($status);
    $consignment_filter->AddOrderByDate(false);
    $con_list = $consignment_filter->getColumnList("id,
                                                        awb, 
                                                        date_submitted, 
                                                        service_type, 
                                                        address_line_1,
                                                        address_line_2,
                                                        address_line_3,
                                                        city, 
                                                        country,
                                                        postcode");

    $table .= '<div id="table_container" class="main_grid2"><table class="consignment_list_tbl table table-striped table-bordered table-advance table-hover" >';

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= 'Date';
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Tracking Number';
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Service';
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Address';
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Claim ?';
    $table .= '</th>';
    $table .= '</tr>';

    $count = 0;
    foreach ($con_list as $con) {
        $id = $con->getId();
        $table .= '<tr><td>';
        $table .= date("Y-m-d", $con->getDateSubmitted());
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con->getAwb();
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con->getServiceType();
        $table .= '</td>';
        $table .= '<td>';
        $table .= $con->getAddressLine1() . " " . $con->getAddressLine2() . " " . $con->getCity() . " " .
                $con->getCountry() . " " . $con->getPostCode();
        $table .= '</td>';
        $table .= '<td>';
        $table .= '<input id="btnClaim[' . $count . ']" type="button" 
					name="btnClaim[' . $count . ']" value="Claim" onclick="btnClaimReturn(\'' . $id . '\')"
					class="btn_save" style="height:25px;" />';
        $table .= '</td>';
        $table .= '</tr>';

        $count++;
    }

    $table .= '</table></div>';

    echo $table;
}

if ($_POST['action'] == 'returnmanifest') {

    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }

    $trackingDataFilter = new TrackingDataFilter();
    $trackingDataFilter->addDateCreatedFilter(date("Y-m-d"), date("Y-m-d", strtotime(date("Y-m-d") . ' +1 day')));
    //$trackingDataFilter->addDateCreatedFilter("2017-02-02", date("Y-m-d",strtotime("2017-02-02" . ' +1 day')));
    $trackingDataFilter->addAccountFilter($sessionUser->getAccount());
    $trackingDataFilter->addStatusFilter(Consignment::STATUS_RETURNED);
    //print_r($trackingDataFilter);
    $trackingDataHistoryList = $trackingDataFilter->getColumnList("id, tracking_number");

    //echo count($trackingDataHistoryList);

    $trackingNumberArray = array();

    if (count($trackingDataHistoryList) > 0) {
        foreach ($trackingDataHistoryList as $trackingDataHis) {
            $trackingNumberArray[] = $trackingDataHis->getTrackingNumber();
        }

        if (count($trackingNumberArray) > 0) {

            $strTrackingNumber = "'" . implode("','", $trackingNumberArray) . "'";
            $conFilter = new ConsignmentFilter();
            $conFilter->addStatusFilter(array(Consignment::STATUS_SUPPLIER_RETURNED));
            $conFilter->addawbFilterList($strTrackingNumber);
            $conFilter->addGroupByClause("handling");
            $service_list = $conFilter->getColumnList("consignment_status, service_type, handling, 
												       count(distinct awb) 'awb'");
        }
    }

    //print_r($conFilter);
    //echo count($service_list);	
    //Consignment::STATUS_SUPPLIER_RETURNED					

    /* $strStatus = implode("','", $status);

      $service_list = Consignment::GetSupplierEndOfDayByService(Sessionmanager::getUser()->getUserAccount(),
      $strStatus, $userId, 'return');


      $conFilter = new ConsignmentFilter();
      $conFilter->addStatusFilter(array(Consignment::STATUS_SUPPLIER_RETURNED));
      $conFilter->addScannedByFilter(Sessionmanager::getUser()->getUserAccount());
      $conFilter->addDateFilter(date("Y-m-d"), date("Y-m-d"), "shipped");
      $conFilter->addGroupByClause("handling");
      $service_list = $conFilter->getColumnList("consignment_status, service_type, handling,
      count(distinct awb) 'awb'");
      //echo "<pre>";
      //print_r($service_list);
     */

    $table .= '<table class="consignment_list_tbl table table-striped table-bordered table-advance table-hovers">';


    foreach ($service_list as $service) {

        $status = $service->getStatus();

        if ($status == Consignment::STATUS_SUPPLIER_RETURNED)
            $status = "Return";
        else
            $status = "";

        $total += $service->getAwb();

        $table .= '	
				  <tr>
				  <td>
			
				  <b>' . $service->getAwb() . " $status " . $service->getServiceType() . '</b> Consignment(s) waititng to be booked
                                 
                  
                              
				  </td> 
				  ';

        if ($service->getHandling() != '') {

            $table .= '<td>
				  <input id=' . $service->getHandling() . ' type="button" 
				  name=' . $service->getHandling() . ' value="Book These" onclick="btnbook(\'' . $service->getHandling() . '\')"		  class="btn_save" style="height:25px;" />
				  </td>';
        } else {
            $table .= '<td>Account owner not assigned</td>';
        }

        $table .= ' </tr>';
    }

    if ($total > 0) {
        $bookAll = '';

        $table .= '<tr>
				  <td>
				  <br/>	
				  <b>' . $total . '</b> Total Scanned ready to be booked
                                 
                  <br />
                              
				  </td> 
				  <td>
					  <input id="btnBookAll" type="button" 
					  name="btnBookAll" value="Book All" onclick="btnbook(\'' . $bookAll . '\')"
					  class="btn_save" style="height:25px;" />

				  </td>            
				  </tr>';
    }

    $table .= '</table>';

    //echo $table;	



    $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
    $dateTo = date("Y-m-d");

    $manifestFilter = new ManifestDataFilter();
    $manifestFilter->addAccountFilter($sessionUser->getAccount());
    $manifestFilter->addDateRangeFilter($dateFrom, $dateTo);
    $manifestFilter->addTypeFilter("RETURN");
    $manifestFilter->addOrderById();

    $list_manifest = $manifestFilter->getColumnList("id, file_name, pdf_file, date_created, handling");

    $table .= '<div id="table_container" class="main_grid2">
			 <table class="consignment_list_tbl table table-striped table-bordered table-advance table-hovers" >';

    $table .= '<tr>';
    $table .= '<th width="20%">';
    $table .= Translation::GetCaption("DATE");
    $table .= '</th>';
    $table .= '<th>';
    $table .= Translation::GetCaption("SERVICE");
    $table .= '</th>';
    $table .= '<th>';
    $table .= Translation::GetCaption("CSV");
    $table .= '</th>';
    $table .= '<th>';
    $table .= Translation::GetCaption("PDF");
    $table .= '</th>';
    $table .= '</tr>';


    foreach ($list_manifest as $manifest) {


        $csvlink = $manifest->getFileName();
        $pdflink = $manifest->getPdfFile();
        $manifestid = $manifest->getId();
        $date = $manifest->getDateCreated();
        $handling = trim($manifest->getHandling());
        $routingEmailDate = $manifest->getRoutingEmailDate();
        $c = "";
        $return = "";

        $serviceFilter = new ServiceFilter();


        $serviceFilter->addCodeArrayFilter(str_replace("RTN", "", $handling));
        $serv_list = $serviceFilter->getColumnList("name");


        $handlingArr = explode(",", $handling);

        if (count($handlingArr) > 0) {
            $strHandling = implode("','", $handlingArr);
        } else {
            $strHandling = $handling;
        }

        $serviceFilter = new ServiceFilter();
        $serviceFilter->addCodeArrayFilter($strHandling);
        $serv_list = $serviceFilter->getColumnList("name");

        if (count($serv_list) > 0) {
            //$serviceObj = $serv_list[0];
            foreach ($serv_list as $serviceObj) {
                $serviceHtmlArr[] = $serviceObj->getName();
            }

            $serviceHtml = implode(", ", $serviceHtmlArr);
        }





        if ($routingEmailDate != '')
            $bgcolor = "style='background-color:lightgreen'";
        else
            $bgcolor = "";

        $table .= "<tr $bgcolor>";

        $table .= "<td>";
        $table .= date('d-m-y G:i', $date);
        $table .= "</td>";
        $table .= "<td>";
        $table .= "$return $serviceHtml";
        $table .= "</td>";
        $table .= "<td>";
        $table .= "<a target='_blank' href='" . $csvlink . "'>" . $manifestid . "</a>";
        $table .= "</td>";
        $table .= "<td>";
        $table .= "<a target='_blank' href='" . $pdflink . "'>" . $manifestid . "</a>";
        $table .= "</td>";
        //$table .= "<br>";			
        //$table .= "$serviceHtml Date : " . date("d-m-y G:i", $date) . "  : CSV : <a target='_blank' href='".$csvlink."'>".$manifestid."</a> PDF : <a target='_blank' href='".$pdflink."'>".$manifestid."</a>";
        //$table .= "<br>";						
        //$table .= "</td>";
        $table .= "</tr>";
    }






    $table .= '</table></div>';



    echo $table;
}

/*
 * Scenario End of the day
 * Process: Fetch end of the day parcels list group by service id
 * Get: Request
 * Return: parcel list group by services id array
 */

if ($_POST['action'] == 'endofday') {
    //New Logic
    $sessionUser = SessionManager::getUser();
    $userId = $sessionUser->getId();
    $UserAccountId = $sessionUser->getUserAccountId();
    $groupBy = $_POST['group_by'];
    $grandTotal = 0;
    $queryGroupBy = '  s.id';
    if($groupBy == 'carrier') {
        $queryGroupBy = '  car.id';
    }
    else if($groupBy == 'agent') {
        $queryGroupBy = '  c.agent_id';
    }
//    $table = '<div class="col-md-12">';
//    $table .= '<div class="col-md-3">';
//    $table .= '<div class="form-group"><div class="input-group"><div class="icheck-inline"><label><input type="checkbox" class="icheck " id="chkSendData" checked="checked" value="0" data-checkbox="icheckbox_square-blue">Send Data</label></div></div></div>';
//    $table .= '</div>
//                <div class="col-md-3"></div>
//                <div class="col-md-3"></div>
//                </div>';
    $trackingDataFilter = new TrackingDataFilter();
    // Changed acording to shabbir sir req
//    $trackingDataObj = $trackingDataFilter->getParcelListByService($sessionUser->getWarehouseId(),0,$userId);
    $trackingDataObj = $trackingDataFilter->getParcelListByServiceEndOfDay($sessionUser->getWarehouseId(),0,$UserAccountId,$queryGroupBy);

    $showManifestTbl = 'style="dispaly:none;"';
    if(count($trackingDataObj) > 0)
        $showManifestTbl = 'style="dispaly:block;"';

    $table .= '<div class="col-md-12" '.$showManifestTbl.' ><table class="table table-striped table-bordered table-advance table-hover">';
    foreach ($trackingDataObj as $trackingDataArr) {
        $parcelMsg = "";
        $total = 0;
        $total = $trackingDataArr->getSignatory();
        $grandTotal += $total;
        $status = $trackingDataArr->getWarehouseId();
        if(isset(Consignment::$status_array[$status]))
            $status = Consignment::$status_array[$status];
        else
            $status = "";
        //Changed by irshad with kazim and shabbir 07-06-2018
        /*if ($status == Consignment::STATUS_SUPPLIER_RETURNED)
            $status = "Return";
        else
            $status = "";*/
        //$parcelMsg = '"' . $total . " " . $status . " " . $trackingDataArr->getIpAddress() . " " . $trackingDataArr->getTrackPoint() . '"';
        $groupBytext = $trackingDataArr->getTrackPoint();
        $parcelMsg = '' . $total . ' ' . $status . ' ' . $trackingDataArr->getTrackPoint() . '';
        if($groupBy == 'carrier') {
            $groupBytext = $trackingDataArr->getLatitude();
            $parcelMsg = '' . $total . ' ' . $status . ' ' . $groupBytext . '';
        }
        else if($groupBy == 'agent') {
            $groupBytext = $trackingDataArr->getLongitude();
            $parcelMsg = '' . $total . ' ' . $status . ' ' . $groupBytext . '';
        }

        $table .= '
                    <tr>
                    <td>
			<b>' . $total . " $status " . ' ' . $groupBytext . '</b> ' . Translation::GetCaption("SCANNED_SHIPMENTS_AVAILABLE_FOR_BOOKING") . '
                  <br />
                        </td>';
        if ($groupBy == 'service') {
            $table .= '				   
                        <td> <input type="button" value="Manifest" data-service_id="'.$trackingDataArr->getPodImage().'" data-servicecode="' . $trackingDataArr->getStatusCodeId() . '" data-type_case="'.$groupBy.'" data-carrier_id="'.$trackingDataArr->getAddressLine2().'" data-agent_id="'.$trackingDataArr->getAddressLine1().'" data-servicetype="' . $parcelMsg . '" data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $trackingDataArr->getEntityId() . '\',this)" class="btn btn-primary btn_save "  /> </td>';
        }else if ($groupBy == 'agent') {
            $table .= '				   
                        <td> <input type="button" value="Manifest" data-service_id="'.$trackingDataArr->getPodImage().'" data-agent_id="'.$trackingDataArr->getAddressLine1().'" data-type_case="'.$groupBy.'" data-carrier_id="'.$trackingDataArr->getAddressLine2().'" data-servicecode="' . $trackingDataArr->getStatusCodeId() . '" data-servicetype="' . $parcelMsg . '" data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $trackingDataArr->getEntityId() . '\',this)" class="btn btn-primary btn_save "  /> </td>';
        }else if ($groupBy == 'carrier') {
            $table .= '				   
                        <td> <input type="button" value="Manifest" data-service_id="'.$trackingDataArr->getPodImage().'" data-carrier_id="'.$trackingDataArr->getAddressLine2().'" data-type_case="'.$groupBy.'" data-agent_id="'.$trackingDataArr->getAddressLine1().'" data-servicecode="' . $trackingDataArr->getStatusCodeId() . '" data-servicetype="' . $parcelMsg . '" data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $trackingDataArr->getEntityId() . '\',this)" class="btn btn-primary btn_save "  /> </td>';
        }else {
            if ($service['service_type'] != '') {
                $table .= '<td> <input type="button"  value="Manifest" data-service_id="'.$trackingDataArr->getPodImage().'" data-servicecode="' . $trackingDataArr->getStatusCodeId() . '" data-servicetype="' . $parcelMsg . '" data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $trackingDataArr->getEntityId() . '\',this)" class="btn btn-primary btn_save"  /> </td>';
            } else {
                $table .= '<td>Account owner not assigned</td>';
            }
        }
        $table .= '</tr>';
    }
    if ($total > 0 && $groupBy == 'service') {
        $bookAll = '';
        $table .= '<tr>
                    <td>
                    <br/>	
                    <b>' . $grandTotal . '</b> ' . Translation::GetCaption("SCANNED_SHIPMENTS_AVAILABLE_FOR_BOOKING") . '
                    <br />
                    </td> 
                    <td>
			&nbsp;
                    </td>
                   </tr>';
    }
//    <td>
//			<input id="btnBookAll" type="button" 
//			name="btnBookAll" value="' . Translation::GetCaption("BOOK_ALL") . '" onclick="btnbook(\'' . $bookAll . '\')"
//			class="btn_save" style="height:25px;" />
//                    </td>
    $table .= '</table></div>';
    echo $table . LoadManifestFiles();
    die;
    //Old Logic
//    $groupby = $_POST['group_by'];
//    $total = '';
//    $table = '<div class="col-md-12">';
//    $table .= '<div class="col-md-3">';
//    $table .= '<input type="button" class="btn btn-primary" id="assign-manifiest" onclick="" value="ASSIGN" style="display:none;">';
//    $table .= '</div>
//                <div class="col-md-3"></div>
//                <div class="col-md-3"></div>				
//                </div>';
//    $table .= '<div class="col-md-12"><table class="table table-striped table-bordered table-advance table-hover">';
//    $sessionUser = SessionManager::getUser();
//    $status = array(Consignment::STATUS_DATAREADY_SUPPLIER,Consignment::STATUS_POLAND_WAREHOUSE_RECEIVED);
//    $strStatus = implode("','", $status);
//    $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
//    $dateTo = date("Y-m-d");
//    $userId = $sessionUser->getId();
//    $service_list = Consignment::GetSupplierEndOfDayByService(Sessionmanager::getUser()->getId(), $strStatus, $userId, '', $groupby);
//    foreach ($service_list as $service) {
//        $status = $service['consignment_status'];
//        if ($status == Consignment::STATUS_SUPPLIER_RETURNED)
//            $status = "Return";
//        else
//            $status = "";
//        $total = count($trackingDataObj);
//        $table .= '	
//                    <tr>
//                    <td>
//			<b>' . $total . " $status " . $service['service_type'] . '</b> ' . Translation::GetCaption("SCANNED_SHIPMENTS_AVAILABLE_FOR_BOOKING") . '         
//                  <br />
//                        </td>';
//        if ($groupby == 'service') {
//            $table .= '				   
//                        <td> <input type="button" value="Dispatch" data-servicecode='. $service['service_code'].' data-servicetype=' . $service['service_type'] . ' data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $service['service_type'] . '\',this)" class="btn_save " style="height:25px;" /> </td>';
//        } else {
//            if ($service['service_type'] != '') {
//                $table .= '				   
//                            <td> <input type="button"  value="Dispatch" data-servicecode='. $service['service_code'].' data-servicetype=' . $service['service_type'] . ' data-target="#myModal" data-toggle="modal" onclick="Dispatch_popup(\'' . $service['service_type'] . '\',this)" class="btn_save" style="height:25px;" /> </td>';
//            } else {
//                $table .= '<td>Account owner not assigned</td>';
//            }
//        }
//        $table .= '</tr>';
//    }
//    if ($total > 0 && $groupby == 'service') {
//        $bookAll = '';
//        $table .= '<tr>
//                    <td>
//                    <br/>	
//                    <b>' . $total . '</b> ' . Translation::GetCaption("SCANNED_SHIPMENTS_AVAILABLE_FOR_BOOKING") . '             
//                    <br />
//                    </td> 
//                    <td>
//			<input id="btnBookAll" type="button" 
//			name="btnBookAll" value="' . Translation::GetCaption("BOOK_ALL") . '" onclick="btnbook(\'' . $bookAll . '\')"
//			class="btn_save" style="height:25px;" />
//                    </td>            
//                   </tr>';
//    }
//    $table .= '</table></div>';
//    echo $table . LoadManifestFiles();
}


if (isset($_POST["frm_parcel_action"]) && $_POST["frm_parcel_action"] == "save_parcel_manifest") {
    $parcelId = trim($_POST['frm_parcel_id']);
    $serviceId = trim($_POST['frm_service_id']);
    $typeCase = trim($_POST['frm_type_case']);
    $agentId = trim($_POST['frm_agent_id']);
    $carrierId = trim($_POST['frm_carrier_id']);
    $frmParcelData = trim($_POST['frm_parcel_data']);
    $sendTypeCase = 'service';
    $dataId = $serviceId;
    if($typeCase == 'carrier'){
        $sendTypeCase = 'carrier';
        $dataId = $carrierId;
    }else if($typeCase == 'agent'){
        $sendTypeCase = 'agent';
        $dataId = $agentId;
    }
    $output = saveManifestNew($dataId,$typeCase);
    //Send data to carrier
//    if($frmParcelData == "send"){
//        
//    }



//    $outputArray = [];
//    if(!empty($filePath)){
//        $outputArray["status"] = "success";
//        $outputArray["message"] = 'Manifest created successfully';
//    }else{
//        $outputArray["status"] = "error";
//        $outputArray["message"] = "Pacels not found";
//    }
        echo json_encode($output);
        die;
}

function saveManifestNew($serviceId,$typeCase) {
    $user = SessionManager::getUser();
    $totalPieces = 0;
    $return = [];
    $consignmentArr = [];
        $warehouseId = $user->getWarehouseId();
        $trackingDataFilter = new TrackingDataFilter();
        // Changed according to shabir sir req
        // $trackingDataObj = $trackingDataFilter->getParcelListByService($warehouseId, $serviceId,$user->getId());
        $trackingDataObj = $trackingDataFilter->getParcelListByServiceEndOfDay($warehouseId, $serviceId,$user->getUserAccountId(),"",$typeCase);
        $parcelIdArr = [];
        $parcelServiceIdArr = [];
        if (count($trackingDataObj) > 0)
        {
            foreach ($trackingDataObj as $trackingData) 
            {
                $parcelIdArr[] = $trackingData->getEntityId();
                $parcelServiceIdArr[$trackingData->getEntityId()] = $trackingData->getPodImage();
            }
            
            $returnData = Manifest::manifestCreate($parcelIdArr,$serviceId,'operation',0,$warehouseId,"",$typeCase,$parcelServiceIdArr);
            foreach ($returnData as $key => $msg) 
            {
                $msg  = ($key == 'STATUS' ? strtolower($msg) : $msg);
                $return[strtolower($key)] = $msg;
            }
        } 
        else 
        {
            $return["status"] = "error";
            $return["message"] = "Pacels not found";
        }
    return $return;
    die;
}
/*
 *
 */
if (isset($_POST["action"]) && $_POST["action"] == "get_manifest_email") {
    $manifestId = (int) trim($_POST['manifest_id']);
    $checkedTotal = (int) trim($_POST['checked_total']);
    $checkedName =  trim($_POST['checked_name']);
    $agentIdDispatch =  trim($_POST['agent_id']);
    $serviceIdDispatch =  trim($_POST['service_id']);
    $getByManifestId =  trim($_POST['get_by_manifest_id']);
    $carrierEmail = "";
    $returnData = "";
    if($manifestId > 0){
        //Get service id form manifest table
        $manifest = new Manifest($manifestId);
        $serviceId = $manifest->getServiceId();
        $service = new Services($serviceId);
        $user = new User($manifest->getUserId());
       
        $emailSub = $service->getName() . ' Pre-alert ['.$manifestId.'] '.date('d-m-Y');
        $emailBody = "Dear Sir/Madam,";             //add boundary string and mime type specification
        $emailBody .= "<br /><br />";
        $emailBody .= 'Please kindly see below link for today\'s dispatch to you. Please kindly confirm on arrival if there is any discrepancy. ';
        $emailBody .= "<br /><br />";
        $emailBody .= 'Kindly click on the links below to download the Manifest, copies of the outer carton/pallet labels and the CSV Upload file/s.';
        $emailBody .= "<br /><br />";
        $emailBody .= 'File Link : <a href="'.BASE_URL.'_assets/manifest/csv/'.$manifest->getFileName().'"> Download CSV</a>';
        $emailBody .= "<br />";
        $emailBody .= 'Manifest Link : <a href="'.BASE_URL.'_assets/manifest/pdf/'.$manifest->getPdfFile().'"> Download PDF</a>';
        $emailBody .= "<br /><br />";
        //$emailBody .= 'Kindly contact ops@oneworldexpress.com or phone +44-2088676060 should you have any questions or queries.';
        
        $emailBody .= "<br /><br />";
        $emailBody .= 'Kind Regards';
        $emailBody .= "<br />";
        $emailBody .= "Operations Team";
        $emailBody .= "<br />";
        $emailBody .= $user->getCompany();
        
        //$emailBody = 'Kindly check below link <br> <a href="'.BASE_URL.'_assets/manifest/csv/'.$manifest->getFileName().'"> Download CSV</a> <br> <a href="'.BASE_URL.'_assets/manifest/pdf/'.$manifest->getPdfFile().'"> Download PDF</a>';
        if($getByManifestId == "true"){
            $dispatchManifestFilter = new DispatchManifestFilter();
            $dispatchManifestFilter->addFieldFilter("manifest_id", $manifestId);
            $dispatchManifestObj = $dispatchManifestFilter->getColumnList("service_id,agent_id");
            if(count($dispatchManifestObj) > 0){
                $agentIdDispatch = $dispatchManifestObj[0]->getAgentId();
                $serviceIdDispatch = $dispatchManifestObj[0]->getServiceId();
            }
        }
        
        if($serviceId > 0){
            
//            $returnData .= $service->getPreAlertEmail();
            $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
            $carrierServiceDefaultRulesFilter->addFilter(" serviceid=".$service->getId());
            $agentObj = $carrierServiceDefaultRulesFilter->getColumnList("agentid");
            foreach ($agentObj as $agents) {
                $agentId = $agents->getAgentid();
            }
        }
        
        // get carrier afent Email address
        if(($agentIdDispatch > 0 && $serviceIdDispatch > 0) || $serviceId > 0){
            $serviceAgentMappingDataFilter = new ServiceAgentMappingDataFilter();
            if($agentIdDispatch > 0 && $serviceIdDispatch > 0){
                $serviceAgentMappingDataFilter->addFieldFilter("serviceid", $serviceIdDispatch);
                $serviceAgentMappingDataFilter->addFieldFilter("agentid", $agentIdDispatch);
            }
            else
            {
                $serviceAgentMappingDataFilter->addFieldFilter("serviceid", $serviceId);
                $serviceAgentMappingDataFilter->addFieldFilter("agentid", $agentId);
            }
            $serviceAgentMappingDataObj = $serviceAgentMappingDataFilter->getList();
            if(count($serviceAgentMappingDataObj) > 0){
                $returnData .= $serviceAgentMappingDataObj[0]->getEmail();
            }
        }
        
        //get Agent Email from service ID
         if($serviceId > 0){
            $returnData .=":::";
            $agentData = new AgentData($agentId);
            $returnData .= $agentData->getEmail().",";
        }
       
    if(empty($returnData)){
        $returnData .="::: ";
    }
        $returnData .= ":::".$emailSub.":::".$emailBody.":::".$carrierEmail;
    }
    echo $returnData;
    die;
}
//if (isset($_POST["form_action"]) && $_POST["form_action"] == "savemanifest") {
//    $handling = $_POST['handling'];
//    SaveManifest($handling);
//}
//function SaveManifest($handling) {
//    $user = SessionManager::getUser();
//    $serviceid = $_POST['service_dropdown'];
//    if ($handling == "REGPOSTHUNINT") {
//        $handlingid = "REGPOSTHUNINT";
//        $manifestid = CreateHungaryPostCsv("SendTrackedData");
//        $manifest = new Manifest($manifestid);
//        $manifest->setDateCreated(time());
//        $manifest->setHandling("REGPOSTHUNINT, REGPOSTHUNEUR");
//        $manifest->setAccount($user->getAccount());
//        $manifest->setPieces($_POST["pieces"]);
//        $manifest->setAgent($_POST["agent"]);
//        $manifest->setWeight($_POST["weight"]);
//        $manifest->setMawb(@$_POST["export_mawb"]);
//        $manifest->save();
//        if ($_POST["agent"] == "TGR" || $_POST["agent"] == "KAAB-TGR") {
//            $agentDataFilter = new AgentDataFilter();
//            $agentDataFilter->addAgentCodeFilter($_POST["agent"]);
//            $list_agent = $agentDataFilter->getList();
//            $agentData = '';
//            if (count($list_agent) > 0) {
//                $agentData = $list_agent[0];
//                $agent_name = $agentData->getAgentName();
//                $email = $agentData->getEmail();
//                $agentId = $agentData->getId();
//            }
//            $warehouseId = $user->getWarehouseId();
//            $addressline1 = $agentData->getAddressLine1();
//            $addressline2 = $agentData->getAddressLine2();
//            $addressline3 = $agentData->getAddressLine3();
//            $city = $agentData->getCity();
//            $postcode = $agentData->getPostCode();
//            $country = $agentData->getCountry();
//            $contact_name = $agentData->getContactName();
//            $countryFilter = new CountryFilter();
//            $countryFilter->addNameFilter($country);
//            $country_list = $countryFilter->getColumnList("iso");
//            if (count($country_list) > 0) {
//                $country_obj = $country_list[0];
//                $country_iso = $country_obj->getIso();
//            }
//            $con_relabel = new Consignment();
//            $con_relabel->setHawb($manifestid);
//            $con_relabel->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
//            $con_relabel->setAccount('ONEWOR');
//            $con_relabel->setService("R1");
//            $con_relabel->setReference($manifestid);
//            $con_relabel->setDateReceived(time());
//            $con_relabel->setDateImported(time());
//            $con_relabel->setDateScanned(date("Y-m-d H:i:s"));
//            $con_relabel->setCompany($contact_name);
//            $con_relabel->setContact($contact_name);
//            $con_relabel->setAddressLine1($addressline1);
//            $con_relabel->setAddressLine2($addressline2);
//            $con_relabel->setAddressLine3($addressline3);
//            $con_relabel->setCity($city);
//            $con_relabel->setPostCode($postcode);
//            $con_relabel->setCountryIsoCode($country_iso);
//            $con_relabel->setTelephone("");
//            $con_relabel->setNumberPieces($_POST["pieces"]);
//            $con_relabel->setWeight($_POST["weight"]);
//            $con_relabel->setDescription("Dispatch-" . $manifestid);
//            $con_relabel->setValue("10");
//            $con_relabel->setCurrency("GBP");
//            $con_relabel->setNotes("");
//            $con_relabel->setRoutingCode("S");
//            $con_relabel->setServiceType("DHL EUROPE ROAD ECONOMY (ESU)"); /// USER ENTERED SERVICE
//            $con_relabel->setMawb("");
//            $con_relabel->setBagNumber("");
//            $con_relabel->setServiceId($_POST["service_dropdown"]);
//            $con_relabel->setBagWeight("");
//            $con_relabel->setWarehouseId($warehouseId);
//            $con_relabel->save();
//            $consignment_validator = new ConsignmentValidator($con_relabel);
//            $consignment_validator->isValid();
//            $dpdlabellink = CreateLabel($con_relabel);
//            $con_relabel->setConsignmentStatus("booked");
//            $con_relabel->setDateBooked(time());
//            $con_relabel->save();
//            $folder_path = "../_assets/manifest";
//            if (!file_exists($folder_path)) {
//                mkdir($folder_path, 0777, true);
//            }
//            $file_path = $folder_path . "/" . $uniqueFileName;
//            $file_path = $file_path . ".csv";
//            $manifest->setFileName($dpdlabellink);
//            $manifest->save();
//            $file_path = fopen($dpdlabellink, 'w');
//            fwrite($dpdlabellink, $csvheader . $cr . $csv);
//            fclose($dpdlabellink);
//        } elseif ($_POST["agent"] == "PROFM-YOD") {
//            $dpdlabellink = AddDHlLabel($manifestid, $agentId);
//        } else {
//            AddAgentRecordForBilling($_POST["agent"], $manifestid, $agentData->getId());
//            $dpdlabellink = DispatchLabel::buildPDFDocuments($handlingid, $manifestid);
//        }
//        $manifest->setLabelLink($dpdlabellink);
//        $manifest->save();
//        echo "<script>window.open('" . $dpdlabellink . "', '_blank');</script>";
//        return;
//    } elseif ($handling == "REGHUNUTRINT") {
//        $handlingid = "REGPOSTINTUTR";
//        $manifestid = CreateHungaryPostCsv("SendUnTrackedData");
//        $manifest = new Manifest($manifestid);
//        $manifest->setDateCreated(time());
//        $manifest->setHandling("REGPOSTINTUTR, REGPOSTEURUTR");
//        $manifest->setAccount($user->getAccount());
//        $manifest->setPieces($_POST["pieces"]);
//        $manifest->setAgent($_POST["agent"]);
//        $manifest->setWeight($_POST["weight"]);
//        $manifest->setMawb(@$_POST["export_mawb"]);
//        $manifest->save();
//        $serviceFilter = new ServiceFilter();
//        $serviceFilter->addSCodeFilter($handlingid);
//        $list_service = $serviceFilter->getList();
//        $service = $list_service[0];
//        $agentId = $service->getAgentId(); //// supplier id //////			
//        $agentData = new AgentData($agentId);
//        $agent_name = $agentData->getAgentName();
//        $email = $agentData->getEmail();
//        if ($_POST["agent"] == "TGR") {
//            $link = AddDHlLabel($manifestid);
//        } elseif ($_POST["agent"] == "PROFM-YOD") {
//            $link = AddDHlLabel($manifestid, $agentId);
//        } else {
//            AddAgentRecordForBilling($_POST["agent"], $manifestid, $agent->getId());
//            $link = DispatchLabel::buildPDFDocuments($handlingid, $manifestid);
//        }
//        $manifest->setLabelLink($link);
//        $manifest->save();
//        echo "<script>window.open('" . $link . "', '_blank');</script>";
//        return;
//    }
//    $cr = "\r\n";
//    $uniqueFileName = uniqid();
//    $csv = '';
//    $count = 1;
//    $csvheader = '';
//    $manifestid = $_POST["manifestid"];
//    $con = new ConsignmentFilter();
//    $con->addServiceCodeArrayFilter($serviceid);
//    if ($manifestid != "")
//        $con->addFilter(" c.id in (select consignmentid from manifest_consignment_mapping where manifestid = '" . $manifestid . "')");
//    else
//        $con->addStatusFilter(Consignment::STATUS_DATAREADY);
//    
//    $list = $con->getColumnListLimit("awb, hawb, reference, contact, company, address_line_1, 
//					 address_line_2, city, postcode, telephone, number_pieces,
//					 weight, description, value, currency, notes, date_booked, 
//					 date_printed, service_type");
//    $user = SessionManager::getUser();
//    if ($manifestid != "") {
//        $manifest = new Manifest($manifestid);
//    } else {
//        $manifest = new Manifest();
//    }
//    $manifest->setuserid($user->getId());
//    $manifest->setPieces($_POST["pieces"]);
//    $manifest->setAgent($_POST["agent"]);
//    $manifest->setWeight($_POST["weight"]);
//    $manifest->setMawb(@$_POST["export_mawb"]);
//    $manifest->setDateCreated(time());
//    $manifest->setAccount($user->getAccount());
//    $manifest->setRoutingEmailDate(time());
//    $manifest->setIsdeleted('N');
//    $manifest->save();
//    $manifestid = $manifest->getId();
//
//
//    ////////////////////////////////////// EXPORT MANIFEST ENTRY /////////////////////////////////////////////////
//
//    if ($_POST["export_mawb"] != '') {
//
//        $pAlert = new PreAlert();
//        $pAlert->setMawb($_POST["export_mawb"]);
//        $pAlert->setFlightNumber($_POST["flight_number"]);
//        $pAlert->setPieces($_POST["pieces"]);
//        $pAlert->setWeight($_POST["weight"]);
//        $pAlert->setCurrentStatus("EXPORT");
//        $pAlert->setDateTime(date("Y/m/d H:i"));
//        $pAlert->setType('E');
//        //$pAlert->setCleared('NO');
//        //$pAlert->setStatus('Not Assigned');
//        //$pAlert->setFiles($fileString);						
//        $pAlert->setDateEntry(date("Y-m-d G:i:s"));
//        $pAlert->setCreatedBy($user->getId());
//        $pAlert->save();
//    }
//
//    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    $service = new Services($serviceid);
//    $serviceId = $service->getId();
//
////        AddAgentRecordForBilling($_POST["agent"], $manifestid, $agentId);
//
//
//    $pallet = $_POST['pallet'];
//    $palletid_array = array();
//    for ($start = 0; $start <= count($pallet) - 1; $start++) {
//
//        $palletid_array[] = $pallet[$start];
//    }
//
//    if (sizeof($palletid_array) > 0) {
//        $pallet = new Pallet();
//        $pallet->bulkManifestUpdate($manifestid, $palletid_array);
//    }
//
//    $NumberofUniqueCode = 0;
//    $manifestArray = array();
//    $consignmentid = array();
//    $trackingNumberArray = array();
//
//    if ($_POST["export_mawb"] != "") {
//        $export_mawb = $_POST["export_mawb"];
//    } else {
//        $export_mawb = $manifestid;
//    }
//
//    foreach ($list as $consignment) {
//
//        $manifestArray[$NumberofUniqueCode]['manifestid'] = $manifestid;
//        $manifestArray[$NumberofUniqueCode]['consignmentid'] = $consignment->getId();
//        $manifestArray[$NumberofUniqueCode]['export_mawb'] = $export_mawb;
//        $NumberofUniqueCode++;
//
//        $csv .= getDispatchCSV($consignment, $count);
//
//        $count += 1;
//        $consignmentid[] = $consignment->getId();
//        $trackingNumberArray[] = $consignment->getAwb();
//    }
//
//    if ($handling == "CACEXP" || $handling == "PMP") {
//        SendDataToCourier::sendCarrierData($trackingNumberArray, '', $manifestid);
//    }
//
//
//    $csvheader = getDispatchHeader();
//
//    if (count($manifestArray) > 0) {

//        $ManifestConsignmentMapping = new ManifestConsignmentMapping();
//        $ManifestConsignmentMapping->bulkDataInsert($manifestArray);
//    }
//
//    $folder_path = "../_assets/manifest";
//
//    if (!file_exists($folder_path)) {
//        mkdir($folder_path, 0777, true);
//    }
//
//    $file_path = $folder_path . "/" . date("Ymdhis");
//    $file_path = $file_path . ".csv";
//
//    $manifest->setFileName($file_path);
//    $manifest->save();
//
//    $file_path = fopen($file_path, 'w');
//    fwrite($file_path, $csvheader . $cr . $csv);
//    fclose($file_path);
//    if ($_POST["agent"] == "TGR" ||
//            $_POST["agent"] == "MPR-TGR" ||
//            $_POST["agent"] == "DAC-TGR" ||
//            $_POST["agent"] == "CORREOS-TGR" ||
//            $_POST["agent"] == "KAAB-TGR" ||
//            $_POST["agent"] == "CACEXPTGR" ||
//            $_POST["agent"] == "BRTITALY-TGR" ||
//            $_POST["agent"] == "P2"
//    ) {
//        $agentDataFilter = new AgentDataFilter();
//        $agentDataFilter->addAgentCodeFilter($_POST["agent"]);
//        $list_agent = $agentDataFilter->getList();
//        $agentData = '';
//        if (count($list_agent) > 0) {
//            $agentData = $list_agent[0];
//            $agent_name = $agentData->getAgentName();
//            $email = $agentData->getEmail();
//            $agentId = $agentData->getId();
//        }
//        $warehouseId = $user->getWarehouseId();
//        $addressline1 = $agentData->getAddressLine1();
//        $addressline2 = $agentData->getAddressLine2();
//        $addressline3 = $agentData->getAddressLine3();
//        $city = $agentData->getCity();
//        $postcode = $agentData->getPostCode();
//        $country = $agentData->getCountry();
//        $contact_name = $agentData->getContactName();
//        $company = $agentData->getAgentName();
//        $countryFilter = new CountryFilter();
//        $countryFilter->addNameFilter($country);
//        $country_list = $countryFilter->getColumnList("iso");
//        if (count($country_list) > 0) {
//            $country_obj = $country_list[0];
//            $country_iso = $country_obj->getIso();
//        }
//        $con_relabel = new Consignment();
//        $con_relabel->setHawb($manifestid);
//        $con_relabel->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
//        $con_relabel->setAccount('ONEWOR');
//        $con_relabel->setService("DHL Europe Road Economy (ESU)");
//        $con_relabel->setReference($manifestid);
//        $con_relabel->setDateReceived(time());
//        $con_relabel->setDateImported(time());
//        $con_relabel->setDateScanned(date("Y-m-d H:i:s"));
//        $con_relabel->setCompany($company);
//        $con_relabel->setContact($contact_name);
//        $con_relabel->setAddressLine1($addressline1);
//        $con_relabel->setAddressLine2($addressline2);
//        $con_relabel->setAddressLine3($addressline3);
//        $con_relabel->setCity($city);
//        $con_relabel->setPostCode($postcode);
//        $con_relabel->setCountryIsoCode($country_iso);
//        $con_relabel->setTelephone("");
//        $con_relabel->setNumberPieces($_POST["pieces"]);
//        $con_relabel->setWeight($_POST["weight"]);
//        $con_relabel->setDescription("Dispatch-" . $manifestid);
//        $con_relabel->setValue("10");
//        $con_relabel->setCurrency("GBP");
//        $con_relabel->setNotes("");
//        $con_relabel->setRoutingCode("S");
//        $con_relabel->setServiceType("DHL Europe Road Economy (ESU)"); /// USER ENTERED SERVICE
//        $con_relabel->setMawb("");
//        $con_relabel->setBagNumber("");
//        $con_relabel->setBagWeight("");
//        $con_relabel->setWarehouseId($warehouseId);
//        $con_relabel->setServiceId($_POST['service_dropdown']);
//        $con_relabel->save();
//        $consignment_validator = new ConsignmentValidator($con_relabel);
//        $consignment_validator->isValid();
//        $dpdlabellink = CreateLabel($con_relabel);
//        $con_relabel->setConsignmentStatus("booked");
//        $con_relabel->setDateBooked(time());
//        $con_relabel->save();
//    } elseif ($_POST["agent"] == "PROFM-YOD") {
//        $agentDataFilter = new AgentDataFilter();
//        $agentDataFilter->addAgentCodeFilter($_POST["agent"]);
//        $list_agent = $agentDataFilter->getList();
//        if (count($list_agent) > 0) {
//            $agentData = $list_agent[0];
//            $agent_name = $agentData->getAgentName();
//            $email = $agentData->getEmail();
//            $agentId = $agentData->getId();
//            $countryName = $agentData->getCountry();
//            $countryFilter = new CountryFilter();
//            $countryFilter->addNameFilter($countryName);
//            $country_list = $countryFilter->getColumnList("iso");
//            if (count($country_list) > 0)
//                $iso_code = $country_list[0]->getIso();
//            $consignmentinformation = "||" .
//                    $agentData->getContactName() . "||" .
//                    $agentData->getContactName() . "||" .
//                    $agentData->getAddressLine1() . "||" .
//                    $agentData->getAddressLine2() . "||" .
//                    $agentData->getAddressLine3() . "||" .
//                    $agentData->getCity() . "||" .
//                    $iso_code . "||" .
//                    $agentData->getPostCode() . "||||" .
//                    $_POST["pieces"] . "||" .
//                    $_POST["weight"] . "||" .
//                    "Dispatch Manifest" . "||" . "0||GBP||ONEWOR||" . $manifestid . "||1HS||ONEWOR||oneworld||Oneworld123@||
//												  0||||||||||||0%%1%%1%%1||||cs@oneworldexpress.com||||||||||";
//            $client = new SoapClient(null, array(
//                'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
//                'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));
//            $results = $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));
//            $returnconsignmentarray = explode("||", $results);
//            $dpdlabellink = $returnconsignmentarray[1];
//            $dpdlabellink = str_replace(BASE_URL, "../", $dpdlabellink);
//        }
//    }
//    else {
//        $dpdlabellink = DispatchLabel::buildPDFDocuments($handling, $manifestid);
//    }
//    if (count($list) > 0) {
//        $pdf_file_name = ManifestSummaryReport::SavePDFFile($list, $manifestid);
//        $manifest->setPdfFile($pdf_file_name);
//        $manifest->save();
//    }
//    $manifest->setLabelLink($dpdlabellink);
//    if ($handling == "3HPA")
//        $handling = "YODEL";
//    $manifest->setHandling($handling);
//    $manifest->save();
//    if (count($consignmentid) > 0) {
//        if ($handling == "P2") {
//            $newObjectBar = new p2();
//            $newObjectBar->setServer('live');
//            $loginData = $newObjectBar->login();
//            $config = 'config.xml';
//            $config = $filename; // full path of the fileand its name as well 
//            //CLOSE CURRENT JOB
//            $p2JobidDataFilter = new p2JobidDataFilter();
//            $p2JobidDataFilter->addFieldFilter("job_status", "1");
//            $p2joblist = $p2JobidDataFilter->getList();
//            if (count($p2joblist) > 0) {
//                $jobid = $p2joblist[0]->getJobid();
//                $p2joblist[0]->setDispatchDate(date("Y-m-d"));
//                $p2joblist[0]->save();
//            }
//            $jobData = $newObjectBar->closeJob($jobid, true); //($requestArray);
//            if ($jobData['STATUS'] !== "SUCCESS")
//                echo "ERROR|| Enable to Close current open Job";
//            else {
//                $deliverJob = $newObjectBar->deliverJob($jobid, false);
//                if ($deliverJob["STATUS"] !== "SUCCESS") {
//                    echo "ERROR|| Enable to call Delivery at P2";
//                }
//                $jobUploadData = $newObjectBar->registerjob();
//            }
//            //CREATE NEW JOB REQUEST
//            if ($jobData['STATUS'] !== "SUCCESS")
//                echo "ERROR|| Enable to create new Job at P2";
//        }
//        $trackingNumberBooked = "'" . implode("','", $consignmentid) . "'";
//        Consignment::bulkUpdate("consignment_status='" . Consignment::STATUS_DISPATCHED . "'", "ID IN ($trackingNumberBooked)");
//        if (count($trackingNumberArray) > 0) {
//            $sessionUser = SessionManager::getUser();
//            $response_array = array();
//            $_POST["reference"] = "";
//            $response_array['message'] = "Tracking Added Successfully for " . count($trackingNumberArray) . " Number of Shipments.";
//            echo json_encode($response_array);
//            die;
//        }
//    }
//}

/*
 * Save Manifest End Function
 */

function getDispatchHeaderNew() {
    $header_row = array(
        "Account",
        "HawbNo",
        "Reference",
        "TrackingNumber",
        "Company",
        "Contact",
        "Address1",
        "Address2",
        "Address3",
        "City",
        "Postcode",
        "Country",
        "Telephone",
        "Weight",
        "NumberOfPieces",
        "Description",
        "DateCreated",
        "DateBooked",
        "Owner",
        "Supplier",
        "ServiceCode",
        "ServiceName",
        "RemoteArea"
    );

    foreach ($header_row as $field) {
        $record .= $field . ",";
    }
    return $record;
}

function getDispatchHeader() {
    $header_row = array(
        "Sr.",
        "HAWB",
        "REF",
        "Date Dispatched",
        "Company",
        "Contact",
        "Address Line 1",
        "Address Line 2",
        "City",
        "Postcode",
        "Telephone",
        "Number of Pieces",
        "Weight",
        "Description",
        "Value",
        "Currency",
        "Notes",
        "Tracking Number"
    );

    foreach ($header_row as $field) {
        $record .= $field . ",";
    }
    return $record;
}

function getDispatchCSV($consignment, $count) {

    $csv .= $count . ",";
    $csv .= $consignment->getHawb() . ",";
    $csv .= cleanData($consignment->getReference()) . ",";
    $csv .= date("Y-m-d", strtotime($consignment->getDateBooked())) . ",";
    $csv .= cleanData(trim($consignment->getCompany())) . ",";
    $csv .= cleanData(trim($consignment->getContact())) . ",";
    $csv .= cleanData($consignment->getAddressLine1()) . ",";
    $csv .= cleanData(trim($consignment->getAddressLine2())) . ",";
    $csv .= cleanData(trim($consignment->getCity())) . ",";
//        $csv .= cleanData(trim($consignment->getCountry())) . ",";
    $csv .= cleanData(trim($consignment->getPostCode())) . ",";
    $csv .= cleanData(trim($consignment->getTelephone())) . ",";
    $csv .= cleanData(trim($consignment->getNumberPieces())) . ",";
    $csv .= cleanData(trim($consignment->getWeight())) . ",";
    $csv .= cleanData(trim($consignment->getDescription())) . ",";
    $csv .= cleanData(trim($consignment->getValue())) . ",";
    $csv .= cleanData(trim($consignment->getCurrency())) . ",";
    $csv .= cleanData(trim($consignment->getNotes())) . ",";
    $csv .= "=\"" . $consignment->getAwb() . "\"" . ",";
    //$csv  .=  $consignment->getAwb() . ",";
    $csv .= "\r\n";
    return $csv;
}

function getDispatchCSVNew($consignmentArr) {
    $csv .= $consignmentArr['Account'] . ",";
    $csv .= $consignmentArr['HawbNo'] . ",";
    $csv .= $consignmentArr['Reference'] . ",";
    $csv .= $consignmentArr['TrackingNumber'] . ",";
    $csv .= $consignmentArr['Company'] . ",";
    $csv .= $consignmentArr['Contact'] . ",";
    $csv .= $consignmentArr['Address1'] . ",";
    $csv .= $consignmentArr['Address2'] . ",";
    $csv .= $consignmentArr['Address3'] . ",";
    $csv .= $consignmentArr['City'] . ",";
    $csv .= $consignmentArr['Postcode'] . ",";
    $csv .= $consignmentArr['Country'] . ",";
    $csv .= $consignmentArr['Telephone'] . ",";
    $csv .= $consignmentArr['Weight'] . ",";
    $csv .= $consignmentArr['NumberOfPieces'] . ",";
    $csv .= $consignmentArr['Description'] . ",";
    $csv .= date("Y-m-d", strtotime($consignmentArr['DateCreated'])) . ",";
    $csv .= date("Y-m-d", strtotime($consignmentArr['DateBooked'])) . ",";
    $csv .= $consignmentArr['Owner'] . ",";
    $csv .= $consignmentArr['Supplier'] . ",";
    $csv .= $consignmentArr['ServiceCode'] . ",";
    $csv .= $consignmentArr['ServiceName'] . ",";
    $csv .= "=\"" . $consignmentArr['RemoteArea'] . "\"" . ",";
    $csv .= "\r\n";
    return $csv;
}

function AddAgentRecordForBilling($agent, $manifestid, $supplierid) {
    $user = SessionManager::getUser();
    //echo $agent;

    $warehouseId = $user->getWarehouseId();

    $agentDataFilter = new AgentDataFilter();
    $agentDataFilter->addAgentCodeFilter($agent);
    $list_agent = $agentDataFilter->getColumnList("id, agent_code");

    //print_r($list_agent);
    //die;

    if ($agent != 'TGR') {

        if (count($list_agent) > 0) {
            $agent = $list_agent[0];

            $supplier = new AgentData($supplierid);

            $manifest = new Manifest($manifestid);

            $consignment = new Consignment();
            $consignment->setAccount("ONEWOR");
            $consignment->setUsername($user->getUsername());

            $consignment->setAwb($manifestid);

            $consignment->sethawb($manifestid);

            $consignment->setMessage();
            $consignment->setService("INT");
            $consignment->setHandling("OWEFLT");
            $consignment->setServiceType("OWE FLIGHTS"); // CAN'T EDIT HANDLING
            $consignment->setReference($manifestid);
            $consignment->setType('D');
            $consignment->setStatus(Consignment::STATUS_WAREHOUSE_RECEIVED);
            $consignment->setContact($supplier->getContactName());
            $consignment->setAddressLine1($supplier->getAddressLine1());
            $consignment->setAddressLine2($supplier->getAddressLine2());
            $consignment->setAddressLine3($supplier->getAddressLine3());
            $consignment->setCity($supplier->getCity());
            $consignment->setPostCode($supplier->getPostCode());
            $consignment->setCountry($supplier->getCountry());
            $consignment->setTelephone($supplier->setTelephone());
            $consignment->setAgentid($agent->getId());
            $consignment->setNumberPieces($manifest->getPieces());
            $consignment->setWeight($manifest->getWeight());
            $consignment->setDateSubmitted(time());
            $consignment->setDateReceived(time());
            $consignment->setDateBooked(time());
            $consignment->setWarehouseId($warehouseId);
            $consignment->setUserCode($user->getUserCode());
            //$consignment->setDateBooked(time());

            $consignment->setMawb($manifest->getMawb());
            $consignment->setFlightNumber($manifest->getFlightNumber());

            $consignment->save();

            //echo "consignment saved";
            //die;
        }
    }
}

function AddDHlLabel($manifestid, $agentid) {

    $agentData = new AgentData($agentid);

    $user = SessionManager::getUser();


    $warehouseId = $user->getWarehouseId();

    $addressline1 = $agentData->getAddressLine1();
    $addressline2 = $agentData->getAddressLine2();
    $addressline3 = $agentData->getAddressLine3();
    $city = $agentData->getCity();
    $postcode = $agentData->getPostCode();
    $country = $agentData->getCountry();
    $contact_name = $agentData->getContactName();
    $company_name = $agentData->getAgentName();

    $countryFilter = new CountryFilter();
    $countryFilter->addNameFilter($country);
    $country_list = $countryFilter->getColumnList("iso");



    if (count($country_list) > 0) {
        $country_obj = $country_list[0];
        $country_iso = $country_obj->getIso();
    }


    //echo $country_iso;		
    $con_relabel = new Consignment();
    $con_relabel->setHawb($manifestid);
    $con_relabel->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
    $con_relabel->setAccount('ONEWOR');

    if ($agentData->getAgentCode() == "TGR") {
        $con_relabel->setService("DHL Europe Road Economy (ESU)");
        $con_relabel->setHandling("ESU");
    } elseif ($agentData->getAgentCode() == "PROFM-YOD") {
        $con_relabel->setService("Yodel @Home 24 POD");
        $con_relabel->setHandling("1HS");
    }
    $con_relabel->setReference($manifestid);
    $con_relabel->setDateReceived(time());
    $con_relabel->setDateSubmitted(time());
    $con_relabel->setDateImported(time());
    $con_relabel->setDateScanned(date("Y-m-d H:i:s"));
    $con_relabel->setCompany($company_name);
    $con_relabel->setContact($contact_name);
    $con_relabel->setAddressLine1($addressline1);
    $con_relabel->setAddressLine2($addressline2);
    $con_relabel->setAddressLine3($addressline3);
    $con_relabel->setCity($city);
    $con_relabel->setCountry($country);
    $con_relabel->setPostCode($postcode);
    $con_relabel->setCountryIsoCode($country_iso);
    $con_relabel->setTelephone("");
    $con_relabel->setNumberPieces($_POST["pieces"]);
    $con_relabel->setWeight($_POST["weight"]);
    $con_relabel->setDescription("Dispatch-" . $manifestid);
    $con_relabel->setValue("10");
    $con_relabel->setCurrency("GBP");
    $con_relabel->setNotes("");
    $con_relabel->setRoutingCode("S");
    $con_relabel->setServiceType("DHL Europe Road Economy (ESU)"); /// USER ENTERED SERVICE
    $con_relabel->setMawb("");
    $con_relabel->setBagNumber("");
    $con_relabel->setBagWeight("");
    $con_relabel->setWarehouseId($warehouseId);

    $con_relabel->save();




    $consignment_validator = new ConsignmentValidator($con_relabel);
    $consignment_validator->isValid();

    $dpdlabellink = CreateLabel($con_relabel);




    $con_relabel->setConsignmentStatus("booked");
    $con_relabel->setDateBooked(time());
    $con_relabel->save();

    return $dpdlabellink;


    //$dpdlabellink = str_replace("..", "http://oneworldexpress.co.uk/remote", $this->CreateLabel($con_relabel));						
}

function CreateHungaryPostCsv($carrier) {
    $fakeWeight = '';
    $consignmentFilter = new ConsignmentFilter();
    if ($carrier == "SendUnTrackedData") {
        $consignmentFilter->addStatusFilter(Consignment::STATUS_DATAREADY);
        $serviceid = "'73','74'";
    } elseif ($carrier == "SendTrackedData") {
        $consignmentFilter->addStatusFilter(Consignment::STATUS_DATAREADY);
        $serviceid = "'70','71'";
    } elseif ($carrier == "SendPolandTrackedData") {
        $consignmentFilter->addStatusFilter(Consignment::STATUS_POLAND_WAREHOUSE_RECEIVED);
        $serviceid = "'70','71'";
    } elseif ($carrier == "SendPolandUnTrackedData") {
        $consignmentFilter->addStatusFilter(Consignment::STATUS_POLAND_WAREHOUSE_RECEIVED);
        $serviceid = "'73','74'";
    }
    $consignmentFilter->addServiceCodeArrayFilter($serviceid);
    $manifest = new Manifest();
    $manifest->save();
    $manifestid = $manifest->getId();
    $consignmentFilter->addWarehouseIdFilter(Sessionmanager::getUser()->getWarehouseId());
    if ($carrier == "SendUnTrackedData" || $carrier == 'SendPolandUnTrackedData') {
        
        $fakeWeight = "Y";
        ////////////////////////////// FAKE WEIGHT ///////////////////////////////////			
        $consignmentFilter->addFakeWeightFilter();
        $list = $consignmentFilter->getColumnListLimit('id,hawb,awb,consignment_status, handling,company,contact,address_line_1,address_line_2,address_line_3,city,country,postcode,weight,number_pieces,fake_weight');
        if (count($list) > 0) {
            $file_path = generatedatacsv($carrier, $list, $fakeWeight, $manifestid);
            $summary_file_path = CreateSummaryCsv($carrier, $fakeWeight);
            SendEmail($file_path, $summary_file_path, $link, $carrier, "Fake");
        }
        ////////////////////////////// ORIGINAL WEIGHT ///////////////////////////////////					
        $fakeWeight = "N";
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addStatusFilter(Consignment::STATUS_DATAREADY);
        $consignmentFilter->addServiceCodeArrayFilter($serviceid);
        $consignmentFilter->addWarehouseIdFilter(Sessionmanager::getUser()->getWarehouseId());
        $list = $consignmentFilter->getColumnListLimit('id,hawb,awb,consignment_status, handling,company,contact,address_line_1,address_line_2,address_line_3,city,country,postcode,weight,number_pieces,fake_weight');
        $file_path = generatedatacsv($carrier, $list, $fakeWeight, $manifestid);
        $summary_file_path = CreateSummaryCsv($carrier, $fakeWeight);
        SendEmail($file_path, $summary_file_path, $link, $carrier, "Original");
    } else {
        
        $fakeWeight = "N";
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addStatusFilter(Consignment::STATUS_DATAREADY);
        $consignmentFilter->addServiceCodeArrayFilter($serviceid);
        $consignmentFilter->addWarehouseIdFilter(Sessionmanager::getUser()->getWarehouseId());
        $list = $consignmentFilter->getColumnListLimit('id,hawb,awb,consignment_status, service_id,company,contact,address_line_1,address_line_2,address_line_3,city,postcode,weight,number_pieces,fake_weight');
        $file_path = generatedatacsv($carrier, $list, $fakeWeight, $manifestid);
        $summary_file_path = CreateSummaryCsv($carrier, $fakeWeight);
    }
    $manifest->setFileName($file_path);
    $manifest->save();
    if (sizeof($trackingnumberArr) > 0) {
        $trackingNumberBooked = "'" . implode("','", $trackingnumberArr) . "'";
        
        if ($carrier == "SendPolandTrackedData" || $carrier == "SendPolandUnTrackedData") {
            Consignment::bulkUpdate("consignment_status= '" . Consignment::STATUS_POLAND_BOOKED . "', booked_file_id='" . $manifestid . "'", "awb IN ($trackingNumberBooked)");
        } else {
            Consignment::bulkUpdate("consignment_status='" . Consignment::STATUS_DISPATCHED . "', booked_file_id='" . $manifestid . "'", "awb IN ($trackingNumberBooked)");
        }
        $sessionUser = SessionManager::getUser();
        SendEmail($file_path, $summary_file_path, $link, $carrier, "Original");
        return $manifestid;
    }
}

function generatedatacsv($carrier, $list, $fakeWeight, $manifestId) {
    ob_start();
    ob_end_clean();
    ini_set('max_execution_time', 300);
    if (count($list) > 0) {
        $hanling = "";
        $csv = "";
        $cr = "\r\n";
        $count = 1;
        $csvNote = "";
        $name = '';
        $csvNote .= "Delivery Note No.:" . $cr;
        $csvNote .= "Date:" . Date("d-m-Y") . $cr;
        $csvNote .= "Code:KBP" . $cr;
        $csvHeader = getHungaryPostHeader();
        $NumberofUniqueCode = 0;
        $manifestArray = array();
        foreach ($list as $consignment) {
            $manifestArray[$NumberofUniqueCode]['manifestid'] = $manifestId;
            if ($_POST["export_mawb"] == '') {
                $manifestArray[$NumberofUniqueCode]['export_mawb'] = $manifestId;
            } else {
                $manifestArray[$NumberofUniqueCode]['export_mawb'] = $_POST["export_mawb"];
            }
            $manifestArray[$NumberofUniqueCode]['consignmentid'] = $consignment->getId();
            $NumberofUniqueCode++;
            $csv .= $count . ",";
            $csv .= $consignment->getAwb() . ",";
            if (trim($consignment->getCompany()) == '')
                $csv .= cleanData(trim($consignment->getContact())) . ",";
            else
                $csv .= cleanData(trim($consignment->getCompany())) . ",";
            $csv .= cleanData(trim($consignment->getAddressLine1())) . " " .
                    cleanData(trim($consignment->getAddressLine2())) . " " .
                    cleanData(trim($consignment->getAddressLine3())) . " " .
                    cleanData(trim($consignment->getCity())) . " " .
                    cleanData(trim($consignment->getPostCode())) . ",";
            if ($fakeWeight == 'Y') {
                if ($consignment->getFakeWeight() == NULL || $consignment->getFakeWeight() == '0')
                    $csv .= cleanData(trim($consignment->getWeight())) . ",";
                else
                    $csv .= cleanData(trim($consignment->getFakeWeight())) . ",";
            }
            else {
                $csv .= cleanData(trim($consignment->getWeight())) . ",";
            }
            $csv .= cleanData(trim($consignment->getHawb()));
            $csv .= $cr;
            $count += 1;
            $trackingnumberArr[] = $consignment->getAwb();
        }
        $folder = '';
        $original = '';
        if (count($list) > 0) {
            $con = $list[0];
            $handling = $con->getServiceId();
            if ($fakeWeight == 'Y')
                $original = "Fake";
            else
                $original = "Original";
            if ($handling == '74' || $handling == '73') {
                $name = "hungary_untracked_" . $original . date("Y-m-d H:i");
                $folder = date("Y-m-d") . "/" . $original . "/Hungary Untracked";
            } else {
                $name = "hungary_tracked_" . date("Y-m-d H:i");
                $folder = date("Y-m-d") . "/" . $original . "/Hungary Tracked";
            }
        }
        $folder_path = "../_assets/manifest_ops/" . $folder;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        $file_path = $folder_path . "/" . $name;
        $file_path = $file_path . ".csv";
        $file_path_new = fopen($file_path, 'w');
        fwrite($file_path_new, $csvNote . $csvHeader . $cr . $csv);
        fclose($file_path_new);
        if (count($manifestArray) > 0) {
            $ManifestConsignmentMapping = new ManifestConsignmentMapping();
            $ManifestConsignmentMapping->bulkDataInsert($manifestArray);
        }
        return $file_path;
    }
}

function getHungaryPostHeader() {
    $header_row = array(
        "ID",
        "Tracking Number",
        "Name",
        "Address",
        "Item Wgt",
        "Cust Ref"
    );
    $record = "";
    foreach ($header_row as $field) {
        $record .= $field . ",";
    }
    $record .= "\r\n";
    return $record;
}

function CreateSummaryCsv($carrier, $fakeWeight = 'N') {
    if ($carrier == "SendUnTrackedData") {
        $handling = "'REGHUNUTREUR','REGHUNUTRINT'";
        $status = "data ready";
    } elseif ($carrier == "SendTrackedData") {
        $handling = "'REGPOSTHUNEUR','REGPOSTHUNINT','REGPOSTHUN'";
        $status = "data ready";
    } elseif ($carrier == "SendPolandTrackedData") {
        $handling = "'REGPOSTHUNEUR','REGPOSTHUNINT','REGPOSTHUN'";
        $status = "poland received";
    } elseif ($carrier == "SendPolandUnTrackedData") {
        $handling = "'REGHUNUTREUR','REGHUNUTRINT'";
        $status = "poland received";
    }
    $consignmentFilter = new ConsignmentFilter();
    $warehouseid = Sessionmanager::getUser()->getWarehouseId();
    $list = $consignmentFilter->HungarySummary($handling, $status, $fakeWeight, $warehouseid);
    if (count($list) > 0) {
        ob_start();
        ob_end_clean();
        
        $csv = "";
        $cr = "\r\n";
        $count = 1;
        $csvNote = "";
        $csvNote .= "Delivery Note No.:" . $cr;
        $csvNote .= "Date:" . Date("d-m-Y") . $cr;
        $csvNote .= "Code:KBP" . $cr;
        $csvHeader = "Country,Weight,Total" . $cr;
        $uniqueFileName = "summary_" . uniqid();
        $file = new FFTINFile();
        foreach ($list as $consignment) {
            $csv .= preg_replace('/[\$,]/', '', $consignment->getCountry()) . ",";
            $csv .= $consignment->getWeight() . ",";
            $csv .= $consignment->getNumberPieces() . ",";
            $totalWeight += $consignment->getWeight();
            $totalPieces += $consignment->getNumberPieces();
            $csv .= $cr;
        }
        $csv .= "Total" . ",";
        $csv .= $totalWeight . ",";
        $csv .= $totalPieces;
        $folder_path = "../_assets/manifest_ops/";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if ($fakeWeight == 'N') {
            $original = "Original";
        } else {
            $original = "Fake";
        }
        if ($carrier == "SendUnTrackedData" || $carrier == 'SendPolandUnTrackedData')
            $folder = date("Y-m-d") . "/" . $original . "/Hungary Untracked/";
        else
            $folder = date("Y-m-d") . "/" . $original . "/Hungary Tracked/";
        $file_path = $folder_path . $folder . $uniqueFileName;
        $file_path = $file_path . ".csv";
        $file_path_new = fopen($file_path, 'w');
        fwrite($file_path_new, $csvNote . $csvHeader . $cr . $csv);
        fclose($file_path_new);
        return $file_path;
    }
}

function SendEmail($file_path, $summaryfilepath, $link, $carrier, $subject = '') {


    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getWarehouseId() == 10)
    //$to = 'ops@oneworldexpress.com, itsupport@oneworldexpress.com'; //international@posta.hu
        $to = "waleed@stellartech.co";
    elseif ($sessionUser->getWarehouseId() == 9)
    //$to = "kazim@oneworldexpress.com";
    //$to = 'bhx.ops@oneworldexpress.com, itsupport@oneworldexpress.com'; //international@posta.hu
        $to = 'waleed@stellartech.co';

    //define the subject of the email

    $carrier = str_replace('Send', '', $carrier);

    $subject = 'HUNGARY POST ' . $subject . " " . $carrier . ' DATA' . date('d-m-Y') . ' One World Express';

    //define the headers we want passed. Note that they are separated with \r\n
    $headers = "From: ITSupport@oneworldexpress.com\r\nReply-To: ITSupport@oneworldexpress.com";
    //add boundary string and mime type specification
    $file_path = str_replace('..', '', $file_path);
    $message = 'HUNGARY POST DATA: http://oneworldexpress.co.uk/remote' . $file_path;
    $message .= "\r\n";

    $summaryfilepath = str_replace('..', '', $summaryfilepath);
    //echo $summaryfilepath;
    $message .= 'HUNGARY POST SUMMARY: http://oneworldexpress.co.uk/remote' . $summaryfilepath;
    $message .= "\r\n";
    $message .= 'HUNGARY POST BATCH LABEL: ' . $link;
    //send the email
    $mail_sent = @mail($to, $subject, $message, $headers);
}

function LoadManifestFiles() {
    $table = "";
    $sessionUser = SessionManager::getUser();
    $dateFrom = date('Y-m-d', strtotime('-30 day', strtotime(date('Y-m-d'))));
//    $dateFrom = date('Y-m-d');
    $dateTo = date("Y-m-d");
    $manifestFilter = new ManifestDataFilter();
    //    $manifestFilter->addAccountFilter($sessionUser->getAccount());
    $manifestFilter->addDateRangeFilter($dateFrom, $dateTo);
    $manifestFilter->addUserAccountFilter($sessionUser->getUserAccountId());
    $manifestFilter->addNewFieldFilter("route_warehouse_id",$sessionUser->getWarehouseId());
    $manifestFilter->addNewFieldFilter("manifest_by","operation");
    $manifestFilter->addNewNotEqFieldFilter("type","return");
    $manifestFilter->addOrderById();
    $list_manifest = $manifestFilter->getColumnList("id, file_name,parcel_file_name, pdf_file, date_created, account_owner, service_id,is_dispatched,pieces,is_send_email,agent_id,carrier_id,ioss_manifest_file");
    $table .= '<div class="col-md-12">
                    <table class="table table-striped table-bordered table-advance table-hover" >';
    $table .= '<tr>';
//    $table .= '<th>';
//    $table .= '<input type="checkbox" onclick="checkedAll(manifestData);" name="checkall">';
//    $table .= '</th>';
    $table .= '<th width="15%">';
    $table .= Translation::GetCaption("DATE");
    $table .= '</th>';
    $table .= '<th>';
    $table .= Translation::GetCaption("Service/Carrier/Agent");
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Number of Pieces';
    $table .= '</th>';
    $table .= '<th>';
    $table .= 'Manifest Number';
    $table .= '</th>';
//    $table .= '<th>';
//    $table .= Translation::GetCaption("ROUTING_TO");
//    $table .= '</th>';
//    $table .= '<th>';
//    $table .= Translation::GetCaption("CSV");
//    $table .= '</th>';
//    $table .= '<th>';
//    $table .= Translation::GetCaption("PDF");
//    $table .= '</th>';
    $table .= '<th style="min-width:274px">';
    $table .= 'Action';
    $table .= '</th>';
//    $table .= '<th>';
//    $table .= 'Action';
//    $table .= '</th>';
//    $table .= '<th>';
//    $table .= 'Action';
//    $table .= '</th>';
    $table .= '</tr>';
    foreach ($list_manifest as $manifest) {
        $serviceId = 0;
        $carrierId = 0;
        $agentId = 0;
        $csvlink = $manifest->getFileName();
        $csvParcellink = $manifest->getParcelFileName();
        $pdflink = $manifest->getPdfFile();
        $pdflinkIoss = $manifest->getIossManifestFile();
        $manifestid = $manifest->getId();
        $date = $manifest->getDateCreated();
//        $handling = trim($manifest->getHandling());
        $routingTo = $manifest->getAccountOwner();
        $nameDispaly = "";
        if($manifest->getAgentId() > 0){
            $agent = new AgentData($manifest->getAgentId());
            $nameDispaly = $agent->getAgentName();
        }
        if($manifest->getCarrierId() > 0){
            $carreir = new Carrier($manifest->getCarrierId());
            $nameDispaly = $carreir->getCarrier();
        }
        $manifestServiceMappingFilter = new ManifestServiceMappingFilter();
        $manifestServiceMappingFilter->addFieldFilter("    manifest_id",$manifest->getId());
        $manifestServiceMappingData = $manifestServiceMappingFilter->getColumnList("   service_id");
        $manifestServiceArr = [];
        if(count($manifestServiceMappingData)> 0){
            foreach ($manifestServiceMappingData as $manifestServiceMappingId) {
                $manifestServiceArr[] = $manifestServiceMappingId->getServiceId();
            }
        }
        $manifestServiceArr = array_unique($manifestServiceArr);
        if(count($manifestServiceArr) == 1){
            $return = "";
            $serviceObj = new Services($manifestServiceArr[0]);
            if($nameDispaly == "")
            	$nameDispaly = $serviceObj->getName();
            $carrierId = $serviceObj->getCarrierId();
        }
        $dispatchText = "Dispatch";
        if($manifest->getIsDispatched() == "Y")
            $dispatchText = "Re-Dispatch";

        $deleteManifestText = "Delete";

        $dispatchEmailText = "Send Email";
        if($manifest->getIsSendEmail() == "Y")
            $dispatchEmailText = "Re Send Email";

        /*
        $serviceHtmlArr = array();

        if ($handling !== "") {
            if (strpos($handling, "RTN") !== false) {
                $return = "Return";
                $handling = str_replace("RTN", "", $handling);
            }
            $handlingArr = explode(",", $handling);
            if (count($handlingArr) > 0) {
                $strHandling = implode("','", $handlingArr);
            } else {
                $strHandling = $handling;
            }
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addCodeArrayFilter($strHandling);
            $serv_list = $serviceFilter->getColumnList("name");
            if (count($serv_list) > 0) {
                foreach ($serv_list as $serviceObj) {
                    $serviceHtmlArr[] = $serviceObj->getName();
                }
                $serviceHtml = implode(", ", $serviceHtmlArr);
            }
        }*/
        $table .= "<tr>";
//        $table .= "<td>";
//        $table .= '<input type="checkbox" value="' . $manifestid . '"  name="manifestData[]" id="manifestData[]">';
//        $table .= "</td>";
        $table .= "<td>";
        $table .= formatDateTime(date('d-m-Y H:i:s', $date));
        $table .= "</td>";
        $table .= "<td>";
        $table .= "$return $nameDispaly";
        $table .= "</td>";
        $table .= "<td align='center'>";
        $table .= $manifest->getPieces();
        $table .= "</td>";
        $table .= "<td align='center'>";
        $table .= sprintf('%010d', $manifest->getId());
        $table .= "</td>";
        $table .= "<td>";
        $table .= "<a class='tooltipbutton' data-toggle='tooltip' data-placement='top' title='Parcel Manifest'  target='_blank' href='_assets/manifest/csv/" . $csvParcellink . "'><img height='36' width='36' src='../images/csv1.png' alt='pdf'></a>";
        $table .= "<a class='tooltipbutton' data-toggle='tooltip' data-placement='top' title='Manifest' target='_blank' href='_assets/manifest/csv/" . $csvlink . "'><img height='36' width='36' src='../images/csv1.png' alt='pdf'></a>";
//        $table .= "</td>";
//        $table .= "<td>";
        if(!empty($pdflink))
            $table .= "<a title='Manifest'  class='tooltipbutton' data-toggle='tooltip' data-placement='top' target='_blank' href='_assets/manifest/pdf/" . $pdflink . "'><img height='36' width='36' src='../images/pdf.png' alt='pdf'></a>";
        if(!empty($pdflinkIoss))
            $table .= "<a title='IOSS Manifest'  class='tooltipbutton' data-toggle='tooltip' data-placement='top' target='_blank' href='_assets/manifest/pdf/" . $pdflinkIoss . "'><img height='36' width='36' src='../images/pdf.png' alt='pdf'></a>";
//        $table .= "</td>";
//        $table .= "<td>";
        $table .= "<a class='btn btn-primary btn-xs dispatch_manifest_btn' data-service_id='".$serviceId."' data-carrier_id='".$carrierId."' data-mani_id=".$manifest->getId()."   href='javascript:;'>".$dispatchText."</a>";
        $table .= "<a class='btn btn-primary btn-xs email_manifest_btn' data-mani_id=".$manifest->getId()."   href='javascript:;'>".$dispatchEmailText."</a>";
        if($manifest->getIsDispatched() != "Y") {
            $table .= "<a class='btn btn-danger btn-xs delete_manifest_btn' data-mani_id=" . $manifest->getId() . "   href='javascript:void(0);'>" . $deleteManifestText . "</a>";
        }
        $table .= "</td>";
//        $table .= "<td>";
//        $table .= "<a class='email_manifest_btn' href='#'>E-mail</a>";
//        $table .= "</td>";
        $table .= "</tr>";
    }
    $table .= '</table></div>';
    return $table;
}

if ($_POST['action'] == 'bookreturn') {

    $handling = $_POST['handlingid'];

    $sessionUser = SessionManager::getUser();

    if ($sessionUser->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }

    //echo $handling;
    //die;

    $conFilter = new ConsignmentFilter();
    $conFilter->addStatusFilter(array(Consignment::STATUS_SUPPLIER_RETURNED));
    //$conFilter->addDateFilter(date("Y-m-d"), date("Y-m-d"), "shipped");
    $conFilter->addScannedByFilter(Sessionmanager::getUser()->getUserAccount());

    if ($handling != '')
        $conFilter->addServiceCodeArrayFilter("'" . $handling . "'");

    //$conFilter->addGroupByClause("handling");
    $scanned_numbers = $conFilter->getColumnList("awb");




    if (count($scanned_numbers) > 0) {

        foreach ($scanned_numbers as $scanNumber) {
            $arr_update_scan_numbers[] = $scanNumber->getAwb();
        }

        $scannedNumbers = "'" . implode("','", $arr_update_scan_numbers) . "'";



        $set_update_columns = "consignment_status = 
								   case when consignment_status = 
								   '" . Consignment::STATUS_SUPPLIER_RETURNED . "' then '" . Consignment:: STATUS_DISPATCHED . "' 
								   else consignment_status end 		                                       			                                   ";



        $where = "awb IN ($scannedNumbers) and handling like 'RTN%'";


        Consignment::bulkUpdate($set_update_columns, $where);

        TrackingData::SendEmail($arr_update_scan_numbers, $sessionUser, '', 'Return');

        TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $sessionUser, "Dispatched");
    }
}

if ($_POST["action"] == 'booking') {

    

    $handling = @$_POST["handlingid"];

    $serviceFilter = new ServiceFilter();
    $serviceFilter->addSCodeFilter($handling);
    $servList = $serviceFilter->getColumnList("code");

    if (count($servList) > 0 || $handling == '') {
        $groupby = "service";
    } else {
        $groupby = "owner";
    }

    //mail("mkazim4u@gmail.com", "group by", $groupby);

    $user = SessionManager::getUser();
    $userId = $user->getId();
    $account = $user->getAccount();
    $statusArray = array(Consignment::STATUS_DATAREADY_SUPPLIER,
        //Consignment::STATUS_SUPPLIER_RETURNED,
        Consignment::STATUS_POLAND_WAREHOUSE_RECEIVED);

    $status = implode("','", $statusArray);

    $scanned_numbers = Consignment::GetSupplierEndOfDayTrackingNumbersByService($account, $status, $handling, $userId, $groupby);

    //echo "<pre>";
    //print_r($scanned_numbers);
    //die;

    if (count($scanned_numbers) > 0) {

        foreach ($scanned_numbers as $scanNumber) {
            $arr_update_scan_numbers[] = $scanNumber['tracking_number'];
        }

        $scannedNumbers = "'" . implode("','", $arr_update_scan_numbers) . "'";


        /* $set_update_columns = "consignment_status = case when consignment_status IN('$status') 
          then '" . Consignment::STATUS_DISPATCHED ."'
          else consignment_status end, 		                                       			                               date_booked  = case when date_booked is null or
          date_booked = '0000-00-00 00:00:00' then NOW() else                                                           date_booked end"; */

        $set_update_columns = "consignment_status = 
							   case when consignment_status = 
							   '" . Consignment::STATUS_POLAND_WAREHOUSE_RECEIVED . "' then '" . Consignment:: STATUS_POLAND_BOOKED . "' 
							   when consignment_status IN('" . Consignment::STATUS_DATAREADY_SUPPLIER . "','" .
                Consignment::STATUS_SUPPLIER_RETURNED . "') 
							   then '" . Consignment::STATUS_DISPATCHED . "' 
							   else consignment_status end, 		                                       			                               date_booked  = case when date_booked is null or 
							   date_booked = '0000-00-00 00:00:00' then NOW() else                               		                               date_booked end";



        $where = "awb IN ($scannedNumbers)";

        TrackingData::SendDataToCourier($arr_update_scan_numbers);

        Consignment::bulkUpdate($set_update_columns, $where);

        $manifestid = TrackingData::SendEmail($arr_update_scan_numbers, $user);

        //echo $manifestid;
        //die;

        TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $user, "Dispatched");

        if ($manifestid > 0) {
            $manifest = new Manifest($manifestid);
            $account_owner = $manifest->getAccountOwner();

            if ($account_owner != '') {

                $userFilter = new UserAccountFilter();
                $userFilter->addUserNameFilter($account_owner);
                $userList = $userFilter->getColumnList("warehouse_id");

                if (count($userList) > 0) {
                    $user = $userList[0];

                    //print_r($user);

                    $warehouseid = $user->getWarehouseId();

                    //echo $warehouseid;
                    ////if($warehouseid > 0 && $manifestid > 0)					
                    DispatchManifestEmail($manifestid, $warehouseid);
                }
            }
        }
    }



    ///echo count($arr_update_scan_numbers);
}


//////////////////////////////////// Bag Scan////////////////////
if (@$_POST["action"] == 'scan') {
    /* $conflr = new $consignmentFilter();
      $awb_bag = $conflr->addAwbFilter();
      print_r($awb_bag); exit; */
}
////////////////////////////////////////////////////////////////


if (@$_POST["action"] == 'remaining_bag_count') {

    /*
      $mawb = $_POST["mawbnumber"];
      $conflr = new ConsignmentFilter();
      $list = $conflr->getRemainingBagCount($mawb);

      $total = $list[0]->getBagNumber();
      echo $total;
     * 
     */
}


if (@$_POST["action"] == 'scanned_bag_count') {
    /*
      $mawb = $_POST["mawbnumber"];
      $conflr = new ConsignmentFilter();
      $list = $conflr->getScannedBagCount($mawb);

      $total = $list[0]->getBagNumber();
      echo $total;
     * 
     */
}



/////////////////////////////////// LOAD SERVICES DROPDOWN ////////////////////////////////////////////

if ($_POST["action"] == 'LoadServiceDropDown') {

    $ddl = $_POST['dropDownName'];

    if ($ddl == '') {
        $ddl = "service_type";
    }


    $serviceFilter = new ServiceFilter();
    $serviceFilter->addFieldFilter("active", "1");
    $serviceArr = $serviceFilter->getColumnList("id, name");
    $serviceDropdown = '';

    $serviceDropdown = '<select name="' . $ddl . '" id="' . $ddl . '" class="form-control" width="400px">';

    $serviceDropdown .= '<option value="0">- Select Service -</option>';

    foreach ($serviceArr as $service) {
        $selected = "";
        $serviceDropdown .= '<option value="' . $service->getId() . '"' . $selected . '>';
        $serviceDropdown .= $service->getName();
        $serviceDropdown .= '</option>';
    }

    $serviceDropdown .= '</select>';

    echo $serviceDropdown;
}






///////////////////////////// GET SERVICE LIST ////////////////////////////////////////////////////////

if (@$_POST["action"] == 'GetServiceList') {
    $serviceFilter = new ServiceFilter();
    $list = $serviceFilter->getColumnList("name, code");
    //echo 'kazim';

    if (count($list) > 0) {
        $arr = array();
        foreach ($list as $service) {
            //echo $service->getCode();
            //break;
            //$arr[$service->getCode()] = $service->getName();
            $arr[] = array('value' => $service->getCode(),
                'label' => $service->getName());
        }
    } else {
        $arr = array('result' => 'error',
            'message' => 'Tracking number or HAWB number not exist in our system');
    }

    echo json_encode($arr);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// GET SHIPMENT DATA FROM HAWB / TRACKING NUMKBER ///////////////////////////


if (@$_POST["action"] == 'GetShipmentData') {
    $user = SessionManager::getUser();
    $tracking = trim(@$_POST["tracking"]);
    $serviceId = trim(@$_POST["service_id_new"]);
    $relabelShipment = trim(@$_POST["relabel_shipment"]);
    $autoPrint = @$_POST["autoprint"];
    $accountAllowedServices = [];
    $conUser = "";
    $conUserAccount = "";
    // 1.1 Papulate data into the feilds if just tracking is entred
    // 1.2 Print old label and papulate data into the feilds
//    $parcelFilter = new ParcelFilter();
//    $parcelFilter->addFieldLikeFilter("tracking_number", $tracking);
//    $parcelObj = $parcelFilter->getList();
    $consignmentId = 0;
//    if (count($parcelObj) > 0) {
//        $consignmentId = $parcelObj[0]->getConsignmentId();
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAwbAndHawbOrFilter($tracking);
        $consignmentObj = $consignmentFilter->getConList();
        $consignment = null;
        $fileFound = false;
        $oldConLabelLink = "";
        $oldConStatusCode = "";
        $oldConStatus = "";
        if (count($consignmentObj) > 0) {
            $consignment = $consignmentObj[0];
            $consignmentId = $consignment->getId();
            $oldConLabelLink = $consignment->getLabelFile();
            $oldConStatusCode = $consignment->getShipmentStatus();
            $oldConStatus = $consignment->getConsignmentStatus();
            // Get consignment user
//            $conUser = $consignment->getUserId(); Changed logic for opetion 2 for gordan
//            $userObj = new User($conUser);
            if($user->getUserAccountId() > 0){
                $conUserAccount = $user->getUserAccountId();
                // Get User account assigned services
                $userServicesRoutingFilter = new UserServicesRoutingFilter();
                $userServicesRoutingFilter->addFieldFilter("    user_account_id", $conUserAccount);
                $userServicesRoutingFilter->addFieldFilter("status", "1");
                $userServicesRoutingFilter->addFieldFilter("is_agreed", "1");
                $userServicesRoutingFilter->setGroup("service_id");
                $userServiceObj = $userServicesRoutingFilter->getColumnList("service_id");
                if(count($userServiceObj) > 0){
                    foreach ($userServiceObj as $userService) {
                        $accountAllowedServices[] = $userService->getServiceId();
                    }
                }
            }
            if($serviceId > 0){
                $serviceNew = new Services($serviceId);
                $serviceName = $serviceNew->getName();
                $errorMSgTxt = "This service [".$serviceName."] is not assigned to account";
                if(!in_array($serviceId, $accountAllowedServices)){
                    $arr = array('result' => 'error','message' => $errorMSgTxt);
                    echo json_encode($arr);
                    die;
                }
            }
            $service = new Services($consignment->getServiceId());
            $oldTrackingNo = $consignment->getAwb();
            $country = strtoupper($consignment->getCountryId());
            $arr = array('result' => 'success',
                'company' => trim($consignment->getCompany()),
                'consignment_id' => trim($consignment->getId()),
                'contact' => trim($consignment->getContact()),
                'addressline1' => trim(UTF8::makeUTF8($consignment->getAddressLine1())),
                'addressline2' => trim(UTF8::makeUTF8($consignment->getAddressLine2())),
                'addressline3' => trim(UTF8::makeUTF8($consignment->getAddressLine3())),
                'city' => trim($consignment->getCity()),
                'country' => trim($country),
                'postcode' => trim($consignment->getPostCode()),
                'telephone' => trim($consignment->getTelephone()),
                'weight' => trim($consignment->getWeight()),
//                'length' => trim($parcelObj[0]->getLength()),
//                'parcel_id' => trim($parcelObj[0]->getId()),
//                'width' => trim($parcelObj[0]->getWidth()),
//                'height' => trim($parcelObj[0]->getHeight()),
                'number_pieces' => trim($consignment->getNumberPieces()),
                'handling' => trim(""),
                'value' => trim($consignment->getValue()),
                'currency' => trim($consignment->getCurrency()),
                'description' => trim($consignment->getDescription()),
                'service' => trim($service->getName()),
                'service_id' => trim($service->getId())
            );
            $filePath = '../_assets/pdf/'.$consignment->getLabelFile();
            $arr['label'] = $filePath;
            if (!file_exists($filePath) && ($serviceId <= 0)) {
                $fileFound = true;
            }
            // 1.3 Change the service save new service aganist that consignement make new label and save to DB
            if (($serviceId > 0) || ($fileFound)) {
                $newConWeight = 0;
                $newConWeight = $consignment->getWeight();
                $serviceObj = new Services($serviceId);
                if($fileFound){
                    $serviceId = $consignment->getServiceId();
                }
                $agentId = checkAgentToServiceWeightLimits($serviceId, $newConWeight);
                $oldParcelsTrackingNo = [];
                $oldParcelsTrackingNoStr = "";
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addConsignmentIdFilter($consignment->getId());
                $parcelListObj = $parcelFilter->getColumnList("p.tracking_number");
                $oldConsignmentServiceId = "";
                $oldConsignmentTrackingNo = "";
                $oldConsignmentAgentId = "";
                if(count($parcelListObj) > 0){
                    foreach ($parcelListObj as $parcelListObjArr) {
                        $oldParcelsTrackingNo[$parcelListObjArr->getId()] = $parcelListObjArr->getTrackingNumber();
                        $oldParcelsTrackingNoStr .= $parcelListObjArr->getTrackingNumber().",";
                    }
                    $oldParcelsTrackingNoStr = rtrim($oldParcelsTrackingNoStr,",");
                    $oldConsignmentServiceId = $consignment->getServiceId();
                    $oldConsignmentTrackingNo = $consignment->getAwb();
                    $oldConsignmentAgentId = $consignment->getAgentId();
                }
                if($fileFound){
                        $msg = "Label Not Found";
                        //addLogDataToConsignmentLog($msg, $consignmentId);
                        $conLabel = Consignment::getInstantLabel($consignment, 'pdf','100x150',true,false,false);
                        if(!empty($conLabel['LABEL_CREATED']) && $conLabel['LABEL_CREATED'] == "created"){
                            $consignment->save();
                        }else{
                            foreach ($oldParcelsTrackingNo as $oldParcelId => $parcelTrackingNo) {
                                ParcelFilter::updateParcelColumnByParcelId($oldParcelId, "p.tracking_number", $parcelTrackingNo);
                            }
                            $consignment->setServiceId($oldConsignmentServiceId);
                            $consignment->setAwb($oldConsignmentTrackingNo);
                            $consignment->setAgentId($oldConsignmentAgentId);
                            $consignment->setShipmentStatus($oldConStatusCode);
                            $consignment->setConsignmentStatus($oldConStatus);
                            $consignment->save();
                        }
                        if(!empty($conLabel['LABEL_CREATED']) && $conLabel['LABEL_CREATED'] == "created"){
                            $consignment->setDateCreated(date("Y-m-d H:i:s"));
                            $consignment->setDateBooked("");
                            if(isset($relabelShipment) && $relabelShipment == "relabel"){
                                $consignment->setDateLabelCreated(time());
                            }
                            $consignment->setBookedFileId("");
                            $consignment->setSendCourierData("");
                            $consignment->setDateScanned(time());
                            $consignment->save();
                        }
                        $arr['label'] = $conLabel['LABEL'];
                    }else{
                        //If we found a appropriate agent according to weight
                        if ($agentId > 0) {
                            $msg = "Service Changed";
                            $murgeTrackingNumber = [];
//                            addLogDataToConsignmentLog($msg, $consignmentId);
                            if($consignmentId > 0){
                                // Check if serivce is customized
//                                if($serviceObj->getIsCustomized() == "1"){
//                                    $customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
//                                    $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
//                                    $customizedServicesRoutingFilter->addFieldFilter('country_id', $this->consignment->getCountryId());
//                                    $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $this->consignment->getServiceId());
//                                    $customizedServicesRoutingFilter->addFieldFilter('status', '1');
//                                }
                                $consignment->setServiceId($serviceId);
                                $consignment->setAgentId($agentId);
                                $consignment->setAwb("");
                                $parcel = new Parcel();
                                $parcel->bulkUpdate("tracking_number = NULL", " consignment_id = '".$consignment->getId()."'");
                            }
                            $conLabel = Consignment::getInstantLabel($consignment, 'pdf','100x150',true,false,false);
                            if(!empty($conLabel['LABEL_CREATED']) && $conLabel['LABEL_CREATED'] == "created"){
                                $consignment->save();
                                // Update parcel tarcking data tracking numbers
                                foreach ($oldParcelsTrackingNo as $oldParcelId => $parcelTrackingNo) {
                                    $parcelObjNew =  new Parcel($oldParcelId);
                                    $newParcelTrackingNumber = $parcelObjNew->getTrackingNumber();
                                    $murgeTrackingNumber[$parcelTrackingNo] = $newParcelTrackingNumber;
                                    TrackingDataFilter::updateParcelColumnByParcelId($oldParcelId, $parcelTrackingNo, $newParcelTrackingNumber);
                                }
                            }else{
                                foreach ($oldParcelsTrackingNo as $oldParcelId => $parcelTrackingNo) {
                                    ParcelFilter::updateParcelColumnByParcelId($oldParcelId, "p.tracking_number", $parcelTrackingNo);
                                }
                                $consignment->setServiceId($oldConsignmentServiceId);
                                $consignment->setAwb($oldConsignmentTrackingNo);
                                $consignment->setAgentId($oldConsignmentAgentId);
                                $consignment->save();
                                $arr = array('result' => 'error','message' => 'Label creation issue'.$conLabel['MESSAGE']);
                            }
                            if(!empty($conLabel['LABEL_CREATED']) && $conLabel['LABEL_CREATED'] == "created"){
                                $consignment->setDateCreated(date("Y-m-d H:i:s"));
                                $consignment->setDateBooked("");
                                if(isset($relabelShipment) && $relabelShipment == "relabel"){
                                    $consignment->setDateLabelCreated(time());
                                }
                                $consignment->setBookedFileId("");
                                $consignment->setSendCourierData("");
                                $consignment->setDateScanned(time());
                                $consignment->save();
                            }
//                            $consignment->setLabelFile($conLabel);
//                            $consignment->save();
                            $jsonData = [];
                            $jsonData['old_service'] = $service->getName();
                            $jsonData['old_label'] = $oldConLabelLink;
                //            $newServiceName = $serviceObj->getName();
                            $jsonEncode = json_encode($jsonData);
                            $newJson = "";
                            $newTrackingNo = $consignment->getAwb();
                            if(!empty($conLabel['LABEL_CREATED']) && $conLabel['LABEL_CREATED'] == "created"){
                                // Save old parcel and consignemnt tracking into consignment_relabel table
                                addDataIntoConsignmentRelabel($jsonEncode, $newJson, $oldTrackingNo, $newTrackingNo, $consignmentId,$oldParcelsTrackingNoStr,$murgeTrackingNumber);
                            }
                            $arr['label'] = $conLabel['LABEL'];
                        } else {
                          $arr = array('result' => 'error',
                                        'message' => 'Selected service is not according to the consignment weight limit weight limits or agent wieght limits');
                        }
                    }
            }
        } else {
            $arr = array('result' => 'error',
                        'message' => 'Tracking number or HAWB number not exist in our system');
        }
//    } else {
//        $arr = array('result' => 'error',
//                    'message' => 'Tracking number or HAWB number not exist in our system');
//    }

    // 1.4 If service is changed and auto print check is also checked then Change the service and print new label of consignment after save into DB

    // Return data into json form
    echo json_encode($arr);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// FETCH CARRIER LOGO BY CARRIER NAME ///////////////////////////////////

if (@$_POST["action"] == 'get_carrier_logo') {
    $carrierName = trim(@$_POST["carrier_name"]);
    $serviceFilter = new ServiceFilter();
    $serviceFilter->addCarrierNameFilter($carrierName);
    $serviceFilterList = $serviceFilter->getColumnList("logo");
    if (count($serviceFilterList) > 0) {
        echo '../images/carrierlogo/' . $serviceFilterList[0]->getLogo();
    } else
        echo "";
}

/*
 * Scenario 201
 * Return:  message and shipment_status
 * Through Tracking Number
 */
if ($_POST['action'] == 'checkshipmentstatus') {
    $comments = "";
    $user = SessionManager::getUser();
    $countryId = $user->getCountryId();
    $trackingNumber = trim(@$_POST["trackingnumber"]);
    $boxNumber = trim(@$_POST["boxnumber"]);
    $reScan = trim(@$_POST["re_scan"]);
    if(!empty($boxNumber)){
        $mawbId = getMawbIdFromMawbNumber($boxNumber);
        if($mawbId == ""){
            if(strlen($boxNumber) > 12 || strpos($boxNumber, "-") != 3){
                $arr = array('res' => 'error', 'message' => 'MAWB format is not supported');
                echo json_encode($arr);
                return;
            }else{
                $mawbObj = new Mawb();
                $mawbObj->setMawbSourceCountryId($countryId);
                $mawbObj->setMawbDestinationCountryId($countryId);
                $mawbObj->setMawbNumber($boxNumber);
                $mawbObj->setIsActive('y');
                $mawbObj->setAddedDate(date("Y-m-d H:i:s",time()));
                $mawbObj->setUpdatedDate(date("Y-m-d H:i:s",time()));
                $mawbObj->setAddedBy($user->getId());
                $mawbObj->save();
                $mawbId = $mawbObj->getId();
            }
        }
    }
    $scannedWeight = trim(@$_POST["weight"]);
    $length = trim(@$_POST["length"]);
    $width = trim(@$_POST["width"]);
    $height = trim(@$_POST["height"]);
    $dateTime = date("Y-m-d H:i:s");
    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
    if (checkTrackingExsistInParcel($trackingNumber)) {
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("tracking_number", $trackingNumber);
//        $parcelFilter->addFieldFilter("parcel_status_code", Consignment::STATUS_HOLD);
        $conList = $parcelFilter->getColumnList("*");
        if (count($conList) > 0) {
            $con = $conList[0];
            if($con->getParcelStatusCode() == Consignment::STATUS_HOLD) {
                // check if parcel is on hold due to over guage
                $consignmentObjHold = New Consignment($con->getConsignmentId());
                $statusReasonFilter = New StatusReasonFilter();
                $statusReasonFilter->addFieldFilter("    parcel_id",$con->getId());
                $statusReasonFilter->addFieldFilter("    is_hold",1);
                $statusReasonData = $statusReasonFilter->getColumnList("*");
                $holdDueOverGuage = "not_hold";
                if(count($statusReasonData) > 0){
                    $holdDueOverGuage = "yes_hold";
                }
                $status = Consignment::STATUS_HOLD;
                $comments = "Parcel is on hold ";
                if (!empty($con->getParcelMessage()))
                    $comments .= " [ " . $con->getParcelMessage() . " ] ";

                $arr = array('res' => 'success', 'status' => $status, 'message' => $comments,"is_hold"=>$holdDueOverGuage,"service_id"=>$consignmentObjHold->getServiceId());
                echo json_encode($arr);
                return;
            }else{
                $arr = array('res' => 'success','length'=>$con->getLength(),'width'=>$con->getWidth(),'height'=>$con->getHeight(),'weight'=>$con->getWeight());
                echo json_encode($arr);
                return;
            }
        }else {
            $arr = array('res' => 'success');
            echo json_encode($arr);
            return;
        }
    } else {
        // Add data into not_found_record
        $notFoundData['mawb'] = $boxNumber;
        $notFoundData['bag_number'] = "";
        $notFoundData['tracking_number'] = $trackingNumber;
        $notFoundData['scanned_by'] = $user->getId();
        $notFoundData['length'] = $length;
        $notFoundData['width'] = $width;
        $notFoundData['height'] = $height;
        $notFoundData['weight'] = $scannedWeight;
        $notFoundData['date_created'] = $dateTime;
        saveNotFoundRecord($notFoundData);

        $arr = array('res' => 'error', 'message' => 'Tracking Number does not exist.');
        echo json_encode($arr);
        return;
    }
}

function checkTrackingScanInTrackingData($trackingNumber) {
    $user = SessionManager::getUser();
    $warehouseid = $user->getWarehouseId();

    $returnMsg = false;
    if(!empty($trackingNumber)){
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addFieldFilter("    tracking_number", $trackingNumber);
        $trackingDataFilter->addFieldFilter("    status_code_id", '146');
        $trackingDataFilter->addFieldFilter("    warehouse_id", $warehouseid);
        $parcelTrackingCount = $trackingDataFilter->getCount();
        if ($parcelTrackingCount > 0)
            $returnMsg = true;
    }
    return $returnMsg;
}

/*
 * Scenario 201
 * Get trackingnumber
 * Returmn : true,false
 */

function checkTrackingExsistInParcel($trackingNumber) {
    $returnMsg = false;
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addFieldFilter("tracking_number", $trackingNumber);
    $parcelTrackingCount = $parcelFilter->getCount();
    if ($parcelTrackingCount > 0)
        $returnMsg = true;
    return $returnMsg;
}

//////////////////////////////////// SINGLE SHIPMENT SCANNING ////////////////////////////////////////
/*
 * Scenario 201
 * Get boxnumber & trackingnumber
 * Returmn :
 */
if (@$_POST["action"] == 'single_shipment_scan') {
    $trackingDataArr = [];
    $labelFile = "";
    $holdLabelLink = '';
    $doHoldLabel = false;
    $doneHoldLabel = false;
    $mawbNumber = '';
    $trackingNumber = '';
    $trackpoint = "";
    $vol_denominator = "";
    $user = SessionManager::getUser();
    if ($user->getId() == 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }
    $parameters = "tl=en&q=";
    $mawbNumber = trim(@$_POST["boxnumber"]);
    $trackingNumber = trim(@$_POST["trackingnumber"]);
    $scannedWeight = trim(@$_POST["weight"]);
    $length = trim(@$_POST["length"]);
    $width = trim(@$_POST["width"]);
    $height = trim(@$_POST["height"]);
    $allow_weight = trim(@$_POST["allow_weight"]); // Maximum weight for shipment if its exceded then we didn't process.
    $holdLabel = trim(@$_POST["hold_label"]);
    $reScan = trim(@$_POST["re_scan"]);
    $mawbId = getMawbIdFromMawbNumber($mawbNumber);
    //Parse Tracking number logic handling
    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
    //New logic
    $dateTime = date("Y-m-d H:i:s");
    $countryId = $user->getCountryId();
    $country = new Country($countryId);
    $trackpoint = '';
    $countryIso3 = '';
    $warehouseName = '';
    $carrierDesc = '';
    if (count($country) > 0)
        $countryIso3 = $country->getIso3();

    $warehouseid = $user->getWarehouseId();
    if ($warehouseid > 0) {
        $warehouseObj = new Warehouse($warehouseid);
        $warehouseName = $warehouseObj->getWarehouseName();
    }
    $trackpoint = $warehouseName." - ".$countryIso3;

    if(!empty($warehouseName)) {
        $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;
    }

    $parcelId = getParcelIdFromTrackingNumber($trackingNumber);
    if(empty($parcelId) || $parcelId == 0){
        //Add data into not found table
        $notFoundData['mawb'] = $mawbNumber;
        $notFoundData['bag_number'] = "";
        $notFoundData['tracking_number'] = $trackingNumber;
        $notFoundData['scanned_by'] = $user->getId();
        $notFoundData['length'] = $length;
        $notFoundData['width'] = $width;
        $notFoundData['height'] = $height;
        $notFoundData['weight'] = $scannedWeight;
        $notFoundData['date_created'] = $dateTime;

        saveNotFoundRecord($notFoundData);
        $msg = "[ ".$trackingNumber." ] Shipment not found";
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        die;
    }
    if($parcelId > 0){
        if (alreadyScannedParcel($trackingNumber, $warehouseid,$reScan)) {
            $msg = "[ ".$trackingNumber." ] Shipment already scanned";
            $arr = array('result' => 'error', 'message' => $msg);
            echo json_encode($arr);
            die;
        }
        $parcel = new Parcel($parcelId);
        $parcelDbWeight =  $parcel->getWeight();
        $parcelDbLength =  $parcel->getLength();
        $parcelDbWidth =  $parcel->getWidth();
        $parcelDbHeight =  $parcel->getHeight();

        $consignmentId = getConsignmentIdFromParcelId($parcelId);
        if(Consignment::checkConsignmentInvoiced($consignmentId)){
            $msg = "[ ".$trackingNumber." ] Shipment already invoiced";
            $arr = array('result' => 'error', 'message' => $msg);
            echo json_encode($arr);
            die;
        }
        $consignment = new Consignment($consignmentId);
        if($parcel->getParcelStatusCode() == Consignment::STATUS_RETURNED){
            $msg = "[ ".$trackingNumber." ] Shipment is returned shipment";
            $arr = array('result' => 'error', 'message' => $msg);
            echo json_encode($arr);
            die;
        }

        // Now return the consignment label if its drop off
        $doLabel = "";
        if($consignment->getShipmentType() == "DO"){
            $doLabel = "";
            $dispatchConsignmentId = 0;
            $consignmentDropoffMappingFilter = new ConsignmentDropoffMappingFilter();
            $consignmentDropoffMappingFilter->addFieldFilter("    dropoff_consignment_id",$consignmentId);
            $dispatchConsignmentObj = $consignmentDropoffMappingFilter->getList("dispatch_consignment_id,parcel_tracking");

            if(count($dispatchConsignmentObj) > 0){
                $dispatchConsignmentId = $dispatchConsignmentObj[0]->getDispatchConsignmentId();
                $parcelTrackingData = json_decode($dispatchConsignmentObj[0]->getParcelTracking(),true);
                $disptchTracking = $parcelTrackingData[$trackingNumber];
                //Parse Tracking number logic handling
                $disptchTracking = ParseTrackingNumber::Parse($disptchTracking);
                $parcelDispatchId = getParcelIdFromTrackingNumber($disptchTracking);
                $parcelDispatchObj = new Parcel($parcelDispatchId);
                $parcelDbDispatchWeight =  $parcelDispatchObj->getWeight();
                $parcelDbDispatchLength =  $parcelDispatchObj->getLength();
                $parcelDbDispatchWidth =  $parcelDispatchObj->getWidth();
                $parcelDbDispatchHeight =  $parcelDispatchObj->getHeight();
                $trackingData = new TrackingData();
                if(($_POST['hold_label'] == "on") && ($scannedWeight > $parcelDbDispatchWeight || $length > $parcelDbDispatchLength || $width > $parcelDbDispatchWidth || $height > $parcelDbDispatchHeight) ){
                    $doHoldLabel = true;
                    $trackingData->setTrackPoint(Tracking::$oneworld_status_desc['136']);
                    $trackingData->setStatusCodeId(136);
                }else{
                    $trackingData->setTrackPoint($trackpoint);
                    $trackingData->setStatusCodeId(146);
                }
                $consignmentDispatchObj = new Consignment($dispatchConsignmentId);
                $labelFile = $consignmentDispatchObj->getLabelFile();
                
                $ocTempTrackingNumber = substr( $disptchTracking, 0, 6 );
                if($ocTempTrackingNumber == 'OCTEMP' && $consignmentDispatchObj->getOtherRoutingCode() == '') // check if OC Number
                {
                    $consignmentDispatchObj->setLabelFile('');
                    $labelFile = '';
                }

                if(empty($labelFile)){
                    $labelFileCon = Consignment::getInstantLabel($consignmentDispatchObj,"pdf",'100x150',true);
                    if(isset($labelFileCon['STATUS']) && $labelFileCon['STATUS'] !="ERROR"){
                        $labelFile = str_replace(SETTING_URL_LABEL, "", $labelFileCon['LABEL']);
                    }
                    else
                    {
                        $arr = array('result' => 'error', 'message' => $labelFileCon["MESSAGE"]);
                        echo json_encode($arr);
                        die;
                    }
                }
//                $consignmentDispatchObj->set();
                //Save data into tracking_data table
                $trackingData->setEntityId($parcelDispatchId);
                $trackingData->setEntityType('parcel');
                $trackingData->setTrackingNumber($disptchTracking);
                $trackingData->setUserId($user->getId());
//                $trackingData->setTrackPoint($trackpoint);
                $trackingData->setDateCreated($dateTime);
//                $trackingData->setStatusCodeId(146);
                $trackingData->setIpAddress(getClientIp());
                $trackingData->setCarrierDesc($carrierDesc);
                $trackingData->setWarehouseId($user->getWarehouseId());
                if($doHoldLabel){
                    $trackingData->setCarrierDesc("HOLD - Overweight or Out of Gauge");
                    $trackingData->save();
                }else if($reScan != "on" || empty($reScan)){
                    $trackingData->save();
                }
// First update parcel length, width, height, and weight
                if($scannedWeight > 0){
                    $parcelDispatchObj->setWeight($scannedWeight);
                }
                $parcelDispatchObj->setWidth($width);
                $parcelDispatchObj->setHeight($height);
                $parcelDispatchObj->setlength($length);
                $parcelDispatchObj->save();
// Then get all parcel of consignment and calculate total weight and also calculate vol weight
                $allDispatchParcelWeight = 0;
                $volDispatchWeight = 0;
                //Check if other parcels have the same consignment id and user already added other weights
                $parcelDispatchFilter = new ParcelFilter();
                $parcelDispatchFilter->addFieldFilter("    consignment_id", $dispatchConsignmentId);
//        $parcelFilter->addFieldNotFilter("id", $parcelId);
                $parcelDispatchListObj = $parcelDispatchFilter->getList();
                $dispatchVolDemonimator = $consignmentDispatchObj->getVolDemonimator();
                foreach ($parcelDispatchListObj as $parcelDispatchListArr) {
                    $allDispatchParcelWeight += $parcelDispatchListArr->getWeight();
                    if ($parcelDispatchListArr->getWidth() > 0 && $parcelDispatchListArr->getLength() > 0 && $parcelDispatchListArr->getHeight() > 0 && $dispatchVolDemonimator > 0) {
                        $parcelDispatchWidht = $parcelDispatchListArr->getWidth();
                        $parcelDispatchLength = $parcelDispatchListArr->getLength();
                        $parcelDispatchHeight = $parcelDispatchListArr->getHeight();
                        if(!empty($dispatchVolDemonimator) && $dispatchVolDemonimator > 0){
                            $volDispatchWeight += ($parcelDispatchLength * $parcelDispatchWidht * $parcelDispatchHeight) / $consignmentDispatchObj->getVolDemonimator();
                        }
                    }
                }
// Then update the consignment vol weight and weight
                $consignmentDispatchObj->setVolWeight($volDispatchWeight);
                $oldDispatchWeight = $consignmentDispatchObj->getWeight();
                $consignmentDispatchObj->setWeight($allDispatchParcelWeight);
                $consignmentDispatchObj->setUpdateWeight($oldDispatchWeight);
                $consignmentDispatchObj->save();
// Call charge able weight function
                saveConsignmentChargeableWeight($dispatchConsignmentId);
//   then call tariff pricing and remove call for update tariff for Do shipment
                $outTariff = Consignment::consignment_label_pricing($consignmentDispatchObj);
//                if($outTariff['status'] == 'error')
//                {
//                    $ConsignmentLog = new ConsignmentLog();
//                    $ConsignmentLog->createlog($outTariff['message'], $consignmentDispatchObj->getId());
//                    echo json_encode($outTariff);
//                    die;
//                }
                if($doHoldLabel) {
                    Parcel::setParcelStatus($parcelDispatchId, Consignment::STATUS_HOLD);
                    changeConStatusByParcelStatus($parcelDispatchId);
                    // make return label
                    $returnLabelPath = 'hold_label';
                    $holdLabelLink = SETTING_DIR_ASSETS . "pdf/hold_label";
                    if (!file_exists($holdLabelLink)) {
                        mkdir($holdLabelLink, 0777, true);
                    }
                    $todayDate = date("Y_m_d");
                    $holdLabelLink = $holdLabelLink . "/" . $todayDate;
                    $returnLabelPath = $returnLabelPath . "/" . $todayDate;
                    if (!file_exists($holdLabelLink)) {
                        mkdir($holdLabelLink, 0777, true);
                    }
                    $holdLabelLink = $holdLabelLink . '/' . $consignmentDispatchObj->getId() . '.pdf';
                    $returnLabelPath = $returnLabelPath. '/' . $consignmentDispatchObj->getId() . '.pdf';
                    $PDFMerger = new PDFMerger();
                    $PDFMerger->addPDF(SETTING_DIR_ASSETS . "pdf/" . $labelFile);
                    try {
                        $PDFMerger->merge('file',$holdLabelLink,"ON HOLD");
                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "\n";
                    }

                    $labelFile = $returnLabelPath;
                    $doneHoldLabel = true;
                    // Save label to consignment hold table
                    $statusReasonObj = New StatusReason();
                    $statusReasonObj->setParcelId($parcelDispatchId);
                    $statusReasonObj->setStatusId(136);
                    $statusReasonObj->setReason("Hold due to over weight or Out of gauge");
                    $statusReasonObj->setStatusLabel($labelFile);
                    $statusReasonObj->setIsHold(1);
                    $statusReasonObj->setAddedBy($user->getId());
                    $statusReasonObj->setDateAdded(time());
                    $statusReasonObj->save();
                }
            }
        }
    }
    if(!empty($trackingNumber)){
        $trackingData = new TrackingData();
        if(($_POST['hold_label'] == "on") && ($scannedWeight > $parcelDbWeight || $length > $parcelDbLength || $width > $parcelDbWidth || $height > $parcelDbHeight) ){
            $trackingData->setStatusCodeId(136);
            $trackingData->setTrackPoint(Tracking::$oneworld_status_desc['136']);
            Parcel::setParcelStatus($parcelId, Consignment::STATUS_HOLD);
            changeConStatusByParcelStatus($parcelId);
            $doHoldLabel = true;
        }else{
            $trackingData->setStatusCodeId(146);
            $trackingData->setCarrierDesc($carrierDesc);
        }
        //Save data into tracking_data table
        $trackingData->setEntityId($parcelId);
        $trackingData->setEntityType('parcel');
        $trackingData->setTrackingNumber($trackingNumber);
        $trackingData->setUserId($user->getId());
        $trackingData->setTrackPoint($trackpoint);
        $trackingData->setDateCreated($dateTime);
        $trackingData->setIpAddress(getClientIp());
        $trackingData->setWarehouseId($user->getWarehouseId());

        if($doHoldLabel){
            $trackingData->save();
            $trackingData->setCarrierDesc("HOLD - Overweight or Out of Gauge");
        }else if($reScan != "on" || empty($reScan)){
            $trackingData->save();
        }
    }
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
    if($scannedWeight > 0){
        $parcel->setWeight($scannedWeight);
        $parcelOldData = $parcel;
        $oldParcelData = serialize($parcelOldData);
        $parcel->save();
        $newParcelData = serialize($parcel);
        $parcelLog = new ParcelLog();
        $parcelLog->createlog($user->getId(), '', $parcelId, 'Parcel', "Parcel weight is changed", $parcelOldData, $newParcelData);
        if($consignment->getShipmentType() != "DO"){
            saveConsignmentChargeableWeight($consignmentId);
        }
    }
    if($parcelId > 0){
        $allParcelWeight = 0;
        //Check if other parcels have the same consignment id and user already added other weights
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("    consignment_id", $consignmentId);
//        $parcelFilter->addFieldNotFilter("id", $parcelId);
        $parcelListObj = $parcelFilter->getList();
        foreach ($parcelListObj as $parcelListArr) {
            $allParcelWeight += $parcelListArr->getWeight();
        }
        //Update the update weight and vol weight of consignment
        $oldWeight = $consignment->getWeight();
        $oldConData = $consignment;
        $consignment->setWeight($allParcelWeight);
        $consignment->setUpdateWeight($oldWeight);
        $oldConData = serialize($oldConData);
        $consignment->save();
        if($consignment->getShipmentType() != "DO") {
            // Cal tariff
            $outTariff = Consignment::consignment_label_pricing($consignment);
        }
        $newConData = serialize($consignment);
        $consignmentLog = new ConsignmentLog();
        $consignmentLog->createlog($user->getUserName() . ' has updated consignemnt weight', $user->getId(), 'USER',  $user->getId(), $oldConData, $newConData);
    }
    if ($length > 0 && $width > 0 && $height > 0) {
        $oldParcelData = "";
        $newParcelData = "";
        //Set if any entered lenght, widht, and height
        $parcelOldData = $parcel;
        $oldParcelData = serialize($parcelOldData);
        $parcel->setWidth($width);
        $parcel->setHeight($height);
        $parcel->setlength($length);
        $parcel->save();
        $newParcelData = serialize($parcel);
        $parcelLog = new ParcelLog();
        $parcelLog->createlog($user->getId(), '', $parcelId, 'Parcel', "Parcel Data is changed", $parcelOldData, $newParcelData);
        $volWeight = 0;
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter("    id", $consignmentId);
        $conVolDemonimatorObj = $consignmentFilter->getListNew("   vol_demonimator");
        if(count($conVolDemonimatorObj) > 0 && !Consignment::checkConsignmentInvoiced($consignmentId)){
            $con = $conVolDemonimatorObj[0];
            $vol_denominator = $con->getVolDemonimator();
//            if(!empty($vol_denominator) && $vol_denominator > 0){
//                $volWeight = ($length * $width * $height) / $vol_denominator;
//            }
        }
        $trackingDataNew = [];

        foreach ($parcelListObj as $parcelListArr) {
            if ($parcelListArr->getWidth() > 0 && $parcelListArr->getLength() > 0 && $parcelListArr->getHeight() > 0) {
                $parcelWidht = $parcelListArr->getWidth();
                $parcelLength = $parcelListArr->getLength();
                $parcelHeight = $parcelListArr->getHeight();
                if(!empty($vol_denominator) && $vol_denominator > 0){
                    $volWeight += ($parcelLength * $parcelWidht * $parcelHeight) / $vol_denominator;
                }
            }
        }
        $consignment->setVolWeight($volWeight);
        if($consignment->getShipmentType() != "DO") {
            $outTariff = Consignment::updateTariffUsingConsignmentId($consignment->getId());
        }
        if($outTariff['status'] == 'error')
        {
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog($outTariff['message'], $consignment->getId());
            echo json_encode($outTariff);
            die;
        }
        else
        {
            $consignment->save();
        }
        $newConData = serialize($consignment);
        $consignmentLog = new ConsignmentLog();
        $consignmentLog->createlog($user->getUserName() . ' has updated consignemnt ', $user->getId(), 'USER',  $user->getId(), $oldConData, $newConData);
    }
    if($consignment->getShipmentType() != "DO" && ($_POST['hold_label'] == "on")) {
        Parcel::setParcelStatus($parcelId, Consignment::STATUS_HOLD);
        changeConStatusByParcelStatus($parcelId);
        // make return label
        $returnLabelPath = 'hold_label';
        $holdLabelLink = "";
        $holdLabelLink = SETTING_DIR_ASSETS . "pdf/hold_label";
        if (!file_exists($holdLabelLink)) {
            mkdir($holdLabelLink, 0777, true);
        }
        $todayDate = date("Y_m_d");
        $holdLabelLink = $holdLabelLink . "/" . $todayDate;
        $returnLabelPath = $returnLabelPath . "/" . $todayDate;
        if (!file_exists($holdLabelLink)) {
            mkdir($holdLabelLink, 0777, true);
        }
        $holdLabelLink = $holdLabelLink . '/' . $consignment->getId() . '.pdf';
        $returnLabelPath = $returnLabelPath. '/' . $consignment->getId() . '.pdf';
        $PDFMerger = new PDFMerger();
        $PDFMerger->addPDF(SETTING_DIR_ASSETS . "pdf/" . $consignment->getLabelFile());
        try {
            $PDFMerger->merge('file',$holdLabelLink,"ON HOLD");
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        $labelFile = $returnLabelPath;
        $doneHoldLabel = true;
        // Save label to consignment hold table
        $statusReasonObj = New StatusReason();
        $statusReasonObj->setParcelId($parcelId);
        $statusReasonObj->setStatusId(136);
        $statusReasonObj->setReason("Hold due to over weight or Out of gauge");
        $statusReasonObj->setStatusLabel($returnLabelPath);
        $statusReasonObj->setIsHold(1);
        $statusReasonObj->setAddedBy($user->getId());
        $statusReasonObj->setDateAdded(time());
        $statusReasonObj->save();
    }
    //Change status (New)
    if($doneHoldLabel){
        Parcel::setParcelStatus($parcelId, Consignment::STATUS_HOLD);
        changeConStatusByParcelStatus($parcelId);
    }else{
        Parcel::setParcelStatus($parcelId, Consignment::STATUS_RECEIVED);
        changeConStatusByParcelStatus($parcelId);
    }
    $msg = "[ ".$trackingNumber." ] Shipment scanned successfully";
    $arr = array('result' => 'success', 'message' => $msg,'label'=> $labelFile);
    echo json_encode($arr);
    die;










//    Old Logic
    /*
     * Check if consignment is_invoiced
     * Check in consignment table that if is_invoiced is 1. on the basises of tracking number
     */
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->addFieldFilter("awb", $trackingNumber);
    $consignmentFilter->addFieldNotFilter("is_invoiced", "1");
    $conObj = $consignmentFilter->getColumnList(" id"); //What is the need of these columns if we just count that?
    if (count($conObj) == 0) {
        /* Make volumetric weight and save to Db
         * Its Hidden Input that have default value is N , it may be Y also
         * Check if allow weight is N
         * Scenario 201 it will get N
         * If allow weight is checked from logged in user then we add in log table that user allowed. But if it ddin't allowed then don't allow over weight
         */
        if ($allow_weight == 'N') {
            /*
             * Scenario 201
             * Get tracking number, weight (But in Scenario 201 weight should be zero) (1st call)
             * Return : Error Message if any
             */
            $errorMsg = CheckAllowedWeight($trackingNumber, $scannedWeight);
            if ($errorMsg != '') {
                $errorMsg .= " Do you want to save?";
                $arr = array('result' => 'error', 'message' => $errorMsg, 'dialogyesno' => 'Y');
                echo json_encode($arr);
                return;
            }
            /*
             * Check if length, Width and Height greater than zero
             */
            if ($length > 0 && $width > 0 && $height > 0) {
                /*
                 * Get : Tracking number, is_invoiced = Y
                 * Check in Consignment Table if is_invoiced equal to "Y". on the basis of tracking number
                 * Return: awb, vol_demonimator
                 */
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addFieldFilter("awb", $trackingNumber);
                $consignmentFilter->addFieldNotFilter("is_invoiced", "1");
                $conVolDemonimatorObj = $consignmentFilter->getColumnList(" vol_demonimator");
                //If count is greater then zero
                if (count($conVolDemonimatorObj) > 0) {
                    $con = $conVolDemonimatorObj[0];
                    $vol_denominator = $con->getVolDemonimator();
                    $vol_weight = ($length * $width * $height) / $vol_denominator;
                    /*
                     * Scenario 201
                     * Get tracking number, weight (This time it will get calculated vol weight to check if it is allowed or not)
                     * Return : Error Message if any
                     */
                    $errorMsg = CheckAllowedWeight($trackingNumber, $vol_weight);
                    if ($errorMsg != '') {
                        $errorMsg .= " Do you want to save?";
                        $arr = array('result' => 'error', 'message' => $errorMsg, 'dialogyesno' => 'Y');
                        echo json_encode($arr);
                        return;
                    }
                }
            }
        } else {
            //        Create Log of consignment
            $con = $conObj[0];
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Over Weight Shipment Allowed by " . $user->getAccount(), $con->getId());
        }
    } else {
        //Add return with error so its invoiced . New func
    }


    $country = new Country($countryId);
    if (count($country) > 0)
        $trackpoint = $country->getName(); //Should be change lOgic with Country ID
    if (trim($trackpoint) == '') {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    }


    //Already checked at checkshipemntstatus make it simple if possible
//    $consignmentFilter = new ConsignmentFilter();
//    $consignmentFilter->addFieldFilter("awb", $trackingNumber);
//    $consignmentFilter->addFieldNotFilter("message", "");
//    $con_list = $consignmentFilter->getColumnList("awb, message");
//    //Check if we have any message then return it with error message (There may be some special message for scaning person.)
//    if (count($con_list) > 0) {
//        $conHold = $con_list[0];
//        $message = $conHold->getMessage();
//        $arr = array('result' => 'error', 'message' => $message);
//        echo json_encode($arr);
//        return;
//    }
    // Check in consignment table that if is_invoiced is 1. on the basises of tracking number
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->addFieldFilter("awb", $trackingNumber);
    $consignmentFilter->addFieldNotFilter("is_invoiced", "1");
    $con_list = $consignmentFilter->getColumnList(" awb, weight, consignment_status,update_weight, vol_weight, vol_demonimator"); //What is the need of these columns if we just count that?
    if (count($con_list) == 0) {
        // Get data from consignment table on the basises of tracking number (Why again and again we getting data from that table)
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter("awb", $trackingNumber);
        $con_list = $consignmentFilter->getColumnList("awb, weight, consignment_status,update_weight, vol_weight, vol_demonimator"); //What is the need of these columns if we just count that?
        //Return Error message if already invoiced (Don't know why again and again getting data from table)
        if (count($con_list) > 0) {
            $arr = array('result' => 'error', 'message' => "Shipment can not be scanned because tracking Number $trackingNumber is already invoiced.");
            echo json_encode($arr);
            return;
        }
    } else {
        //Add return with error so its invoiced . New func
    }
    /*
     * Its Hidden Input that have default value is N , it may be Y also
     * Check if allow weight is N
     * Scenario 201 it will get N
     * If allow weight is checked from logged in user then we add in log table that user allowed. But if it ddin't allowed then don't allow over weight
     */
//    if ($allow_weight == 'N') {
//        /*
//        * Scenario 201
//        * Get tracking number, weight (But in Scenario 201 weight should be zero) (1st call)
//        * Return : Error Message if any
//        */
//        $errorMsg = CheckAllowedWeight($trackingNumber, $scannedWeight);
//        if ($errorMsg != '') {
//            $errorMsg .= " Do you want to save?";
//            $arr = array('result' => 'error', 'message' => $errorMsg, 'dialogyesno' => 'Y');
//            echo json_encode($arr);
//            return;
//        }
//        /*
//         * Check if length, Width and Height greater than zero
//         */
//        if ($length > 0 && $width > 0 && $height > 0) {
//            /*
//            * Get : Tracking number, is_invoiced = Y
//            * Check in Consignment Table if is_invoiced equal to "Y". on the basis of tracking number
//             * Return: awb, vol_demonimator
//            */
//            $consignmentFilter = new ConsignmentFilter();
//            $consignmentFilter->addFieldFilter("awb", $trackingNumber);
//            $consignmentFilter->addFieldNotFilter("is_invoiced", "1");
//            $con_list = $consignmentFilter->getColumnList(" vol_demonimator");
//            //If count is greater then zero
//            if (count($con_list) > 0) {
//                $con = $con_list[0];
//                $vol_denominator = $con->getVolDemonimator();
//                $vol_weight = ($length * $width * $height) / $vol_denominator;
//                /*
//                * Scenario 201
//                * Get tracking number, weight (2nd call)
//                * Return : Error Message if any
//                */
//                $errorMsg = CheckAllowedWeight($trackingNumber, $vol_weight);
//
//                if ($errorMsg != '') {
//                    $errorMsg .= " Do you want to save?";
//                    $arr = array('result' => 'error', 'message' => $errorMsg, 'dialogyesno' => 'Y');
//                    echo json_encode($arr);
//                    return;
//                }
//            }
//        }
//
//    } else {
////        Create Log of consignment
//        $con = $con_list[0];
//        $ConsignmentLog = new ConsignmentLog();
//        $ConsignmentLog->createlog("Over Weight Shipment Allowed by " . $user->getAccount(), $con->getId());
//    }
    /*
     * If box number and tracking number is not empty
     */
    if ($boxNumber !== '' && $trackingNumber !== '') {
        /*
         * Scenario 201
         * Get: tacking number,box number, scanned weight, length, width, height
         * Return: true/false
         */
        if (ScanIndividualItems($trackingNumber, $boxNumber, $scannedWeight, $length, $width, $height) == false) {
            if (isset($_SESSION['NOT_SCANNED_NUMBERS'])) {
                $result = in_array_r($trackingNumber, $_SESSION['NOT_SCANNED_NUMBERS']);
                if ($result == "notfound") {
                    $_SESSION['NUMBER_OF_NOT_SCANNED'] = $_SESSION['NUMBER_OF_NOT_SCANNED'] + 1;
                    $_SESSION['NOT_SCANNED_NUMBERS'][] = $trackingNumber;
                }
            } else {
                $_SESSION['NUMBER_OF_NOT_SCANNED'] = 1;
                $_SESSION['NOT_SCANNED_NUMBERS'][] = $trackingNumber;
            }
            $message = "Scan failed. Please upload shipment information.";
            $parameters .= $message;
            SaveAudioFileUsingGoogleTextToSpeech("http://translate.google.com/translate_tts?", $parameters, $boxNumber);
            $arr = array('result' => 'error',
                'message' => $message,
                'filename' => '../_assets/bag_pdf/' . $boxNumber . '.pdf',
                'total' => isset($_SESSION['NUMBER_OF_NOT_SCANNED']) ? $_SESSION['NUMBER_OF_NOT_SCANNED'] : 0,
                'scannumlist' => isset($_SESSION['SCANNED_NUMBERS']) ? $_SESSION['SCANNED_NUMBERS'] : 0,
                'notscannumlist' => isset($_SESSION['NOT_SCANNED_NUMBERS']) ? $_SESSION['NOT_SCANNED_NUMBERS'] : 0
            );
            echo json_encode($arr);
        } else {
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addAwbAndHawbOrFilter($trackingNumber);
            $con_list = $consignmentFilter->getColumnList("message, single_label, consignment_status");
            $status = '';
            $label_link = '';
            $comments = '';
            if (count($con_list) > 0) {
                $con = $con_list[0];
                $status = $con->getStatus();
                $label_link = $con->getSingleLabel();
                $comments = $con->getMessage();
            }
            if (isset($_SESSION['SCANNED_NUMBERS'])) {
                $result = in_array_r($trackingNumber, $_SESSION['SCANNED_NUMBERS']);
                if ($result == "notfound") {
                    $_SESSION['NUMBER_OF_SCANNED'] = $_SESSION['NUMBER_OF_SCANNED'] + 1;
                    $_SESSION['SCANNED_NUMBERS'][] = $trackingNumber;
                    $message = "Scanned.";
                } else
                    $message = "It's already scanned.";
            }
            else {
                $_SESSION['NUMBER_OF_SCANNED'] = 1;
                $_SESSION['SCANNED_NUMBERS'][] = $trackingNumber;
                $message = "Scanned";
            }
            if ($status == Consignment::STATUS_HOLD)
                $message = 'Please hold this shipment as per the instruction';
            elseif ($status == Consignment::STATUS_RELABEL)
                $message = "Please relabel this shipment as per the instruction";
            $parameters .= $message;
            $arr = array('result' => 'success',
                'message' => $message,
                'label' => $label_link,
                'filename' => '../_assets/bag_pdf/' . $boxNumber . '.pdf',
                'total' => isset($_SESSION['NUMBER_OF_SCANNED']) ? $_SESSION['NUMBER_OF_SCANNED'] : 0,
                'scannumlist' => isset($_SESSION['SCANNED_NUMBERS']) ? $_SESSION['SCANNED_NUMBERS'] : 0,
                'notscannumlist' => isset($_SESSION['NOT_SCANNED_NUMBERS']) ? $_SESSION['NOT_SCANNED_NUMBERS'] : 0,
                'comments' => $comments
            );
            echo json_encode($arr);
        }
    }
}
// Add data into not_found_record 
function saveNotFoundRecord($notFoundData){
    $sessionUser = SessionManager::getUser();
    //Set null if empty
    $length = $notFoundData['length'];
    if(empty($length))
        $length = 0.00;
    $width = $notFoundData['width'];
    if(empty($width))
        $width = 0.00;
    $height = $notFoundData['height'];
    if(empty($height))
        $height = 0.00;
    $weight = $notFoundData['weight'];
    if(empty($weight))
        $weight = 0.00;

    //Check if data is already added into table
    $notFoundRecordFilter = new NotFoundRecordFilter();
    if(!empty($notFoundData['mawb']))
        $notFoundRecordFilter->addFieldFilter("    mawb", $notFoundData['mawb']);

    if(!empty($notFoundData['bag_number']))
        $notFoundRecordFilter->addFieldFilter("    bag_number", $notFoundData['bag_number']);

    if(!empty($notFoundData['tracking_number']))
        $notFoundRecordFilter->addFieldFilter("    tracking_number", $notFoundData['tracking_number']);

    if(!empty($length))
        $notFoundRecordFilter->addFieldFilter("    length", $length);

    if(!empty($width))
        $notFoundRecordFilter->addFieldFilter("    width", $width);

    if(!empty($height))
        $notFoundRecordFilter->addFieldFilter("    height", $height);

    if(!empty($scannedWeight))
        $notFoundRecordFilter->addFieldFilter("    weight", $scannedWeight);

    if(!empty($sessionUser->getId()))
        $notFoundRecordFilter->addFieldFilter("    scanned_by", $sessionUser->getId());

    $notFoundRecordObj = $notFoundRecordFilter->getColumnList('id');
    if(count($notFoundRecordObj) == 0){
        $notFoundRecord = new NotFoundRecord();
        $notFoundRecord->setMawb($notFoundData['mawb']);
        $notFoundRecord->setBagNumber($notFoundData['bag_number']);
        $notFoundRecord->setTrackingNumber($notFoundData['tracking_number']);
        $notFoundRecord->setScannedBy($notFoundData['scanned_by']);
        $notFoundRecord->setLength($length);
        $notFoundRecord->setWidth($width);
        $notFoundRecord->setHeight($height);
        $notFoundRecord->setWeight($weight);
        $notFoundRecord->setDateCreated($notFoundData['date_created']);
        $notFoundRecord->save();
    }
}
/*
 * Scenario 201
 * Get: tacking number
 * Return: parcel id
 */

function getParcelIdFromTrackingNumber($tackingNumber) {
    $tackingNumber = ParseTrackingNumber::Parse($tackingNumber);
    $parcelObj = "";
    $returnMsg = "";
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addConsignmentTableJoin();
    $parcelFilter->addFieldFilter("    p.tracking_number", $tackingNumber);
    $parcelFilter->addFieldFilter("    join_con.`consignment_type`", 'outbound');
    $parcelObj = $parcelFilter->getColumnList("p.id");
    if (count($parcelObj) > 0) {
        $returnMsg = $parcelObj[0]->getId();
    }
    return $returnMsg;
}

if ($_POST["action"] == "CheckShipmentService") {
    $output = array();
    $output['status'] = 0;
    $output['message'] = "";
    $trackingNumber = $_POST['trackingNumber'];
    $serviceIdArr = $_POST['serviceName'];
    // Check if consignemnt belong to the same servies in array
    $conRoutingCode = checkShipmentService($trackingNumber);
    $consignemntId = 0;
    if(isset($conRoutingCode['consignment_id']) && $conRoutingCode['consignment_id'] > 0){
        $consignemntId = $conRoutingCode['consignment_id'];
        if(Consignment::checkConsignmentInvoiced($consignemntId)){
            $output['message'] = "Consignment is alerady invoiced";
            echo json_encode($output);
            die;
        }
    }
    if (in_array($conRoutingCode['service_id'], $serviceIdArr)) {
        $output['status'] = 1;
    } else if(empty ($conRoutingCode['service_id'])) {
        $output['message'] = "The tracking number $trackingNumber does not belong to any consignment";
    }else {
        $output['message'] = "Please remove the tracking number $trackingNumber because it belongs to " . $conRoutingCode['service_name'];
    }
    echo json_encode($output);
}
if ($_POST['action'] == 'CheckTotalWeight') {
    $returnString = "";
    $trackingNumbersArray = $_POST['trackingNumbers'];
    foreach ($trackingNumbersArray as $trackingNumbers) {
        $trackingNumber = ParseTrackingNumber::Parse($trackingNumbers);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addLicensePlateFilter($trackingNumber);
        //we just calculate parcels weight
        $parcelObj = $parcelFilter->getColumnList("tracking_number");
        if(count($parcelObj) > 0){
            if(checkTrackingScanInTrackingData($trackingNumber)){
                $returnString .= "Tracking Number ".$trackingNumber." already scanned<br/>";
            }
        }else{
           $returnString .= "Tracking Number ".$trackingNumber." doest not exist in system<br/>";
        }
    }
    $output['status'] = "success";
    if(!empty($returnString)){
        $output['status'] = "error";
        $output['message'] = $returnString;
    }else{
        $totalWeight = CheckTotalWeight($trackingNumbersArray);
        if($totalWeight > 30){
            $output['status'] = "limit";
            $output['message'] = "Please close the bag and remove the last scanned shipment because the bag weight is more thank 30 kg limit.";
        }else{
            $output['message'] = "Parcels are valid with weight  ".$totalWeight ." Kg";
        }
    }
    echo json_encode($output);
    die;
}
if ($_POST['action'] == 'CheckRoutingHub') {
    // Check if tracking exsist in Db and if its not from same hub then remove from textarea

    $output = array();
    $output['status'] = 0;
    $output['hubname'] = '';
    $output['message'] = '';
    $routingCodeMatch = 0;
    $trackingNumber = trim($_POST['trackingNumber']);
    $hubId = trim($_POST['hubId']);
    $preSortCarrierId = trim($_POST['preSortCarrierId']);
    //Get carrier Hubs details
    $hub = new CarrierHubs($hubId);
    $hubRoutingCode = $hub->getHub();
    $routingCode = $hub->getRoutingCode();

    $arrRouting = getParcelRoutingHub($trackingNumber);
    $conRoutingCode = trim($arrRouting['routing_code']);
    $jobid = trim($arrRouting['job_id']);
    $output['jobid'] = $jobid;
    if ($conRoutingCode != '') {
        if ($conRoutingCode == $routingCode) {
            $output['status'] = 1;
        } else {
            $msg = Translation::GetCaption("MSG_SHIPMENT_SCANNED_INCORRECT_HUB");
            $msg = str_replace("#trackingNumber", $trackingNumber, $msg);
            $msg = str_replace("#code", $conRoutingCode, $msg);
            $output['message'] = $msg;
        }
    } else {
        $output['message'] = 'Routing code does not exist.';
    }
    echo json_encode($output);
}
/*
 * Scenario 301
 * Get: tacking number
 * Return: service code
 */

function checkShipmentService($trackingNumber) {
    $serviceCode = [];
    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addFieldFilter("tracking_number", $trackingNumber);
    $perList = $parcelFilter->getColumnList("consignment_id");
    if (count($perList) > 0) {
        $percel = $perList[0];
        $consignmentId = $percel->getConsignmentId();
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldEqualFilter("    c.id", "=", $consignmentId);
        $consignmentFilter->addFieldNotFilter("consignment_status", Consignment::STATUS_RECYCLED);
        $conList = $consignmentFilter->getColumnList("s.id AS service_id,s.name AS customized_service_id");
        if (count($conList) > 0) {
            $con = $conList[0];
            $serviceCode['service_id'] = $con->getServiceId();
            $serviceCode['service_name'] = $con->getCustomizedServiceId();
            $serviceCode['consignment_id'] = $percel->getConsignmentId();
        }
    }
    return $serviceCode;
}

/*
 * Scenario 301
 * Get: tacking number
 * Return: array
 */

function getParcelRoutingHub($trackingNumber) {
    $routing_code = '';
    $output = [];
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addLicensePlateFilter($trackingNumber);
    //$parcelFilter->addFieldFilter("parcel_status_code", Consignment::STATUS_RECYCLED);
    $parcelObj = $parcelFilter->getColumnList("routing_code");
    if (count($parcelObj) > 0) {
        $parcel = $parcelObj[0];
        $output['routing_code'] = trim($parcel->getRoutingCode());
        $output['job_id'] = trim($parcel->getRoutingCode());
    }
    return $output;
}

/*
 * Scenario 301
 * Get: tracking numbers (In array)
 * Return: Total weight of consigmnet
 */

function CheckTotalWeight($trackingNumberArr) {
    //$maxAllowedWeight = 30; ///kg.
    $totalWeight = 0;
    foreach ($trackingNumberArr as $trackingNumbers) {
        $trackingNumber = ParseTrackingNumber::Parse($trackingNumbers);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addLicensePlateFilter($trackingNumber);
        //we just calculate parcels weight
        $parcelObj = $parcelFilter->getColumnList("weight");
        if (count($parcelObj) > 0) {
            $totalWeight += $parcelObj[0]->getWeight();
        }
    }
    return $totalWeight;
}

/*
 * Scenario 301
 * Get: tracking numbers (In array)
 * Return: bag id
 */

function CreateCarton($arr_trackingnumbers) {
    $bagId = 0;
    $maxAllowedWeight = 30;
    $NumberofUniqueCode = 0;
    $parcelId = 0;
    /*
     * Scenario 301
     * Get: tracking numbers (In array)
     * Return: total weight of consigmnet
     */
    $totalBagWeight = CheckTotalWeight($arr_trackingnumbers);
    $sessionUser = SessionManager::getUser();
    $bag_array = array();
    if ($totalBagWeight > $maxAllowedWeight) {
        $arr = array('result' => 'error', 'message' => 'Total weight of the bag is ' . $totalBagWeight . 'kg. and the allowed weight is ' . $maxAllowedWeight . 'kg.');
        echo json_encode($arr);
        return;
    } else {
        $bagging = new Bagging();
        $bagging->setDateCreated(time());
        $bagging->setAccount($sessionUser->getUserAccount());
        $bagging->setUserId($sessionUser->getId());

        //This is Not defined in this function
        $bagging->setService("");
        $bagging->setCountry("");
        $bagging->setBagType("");

        $bagging->save();
        $bagId = $bagging->getId();
        $boxNumber = $bagId;
        $newBaggingData = serialize($bagging);
        $oldBaggingData = $newBaggingData;
        $bagScanLog = new BagScanLog();
        //Create Log
        $bagScanLog->createlog($sessionUser->getId(), '', $bagId, 'BAGGING', $sessionUser->getUserName() . ' has added new bagging ' . $bagId, '', $newBaggingData);

        if ($bagId > 0) {
            foreach ($arr_trackingnumbers as $trackingNumber) {
                $trackingNumber = trim($trackingNumber);
                if ($trackingNumber != '') {
                    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
                    //This array didn't use any where
                    $arr_update_scan_numbers[] = $trackingNumber;
                    $parcelFilter = new ParcelFilter();
                    $parcelFilter->addLicensePlateFilter($trackingNumber);
                    $parcelObj = $parcelFilter->getColumnList("consignment_id");
                    $conList = [];
                    if (count($parcelObj) > 0) {
                        $consignmentId = $parcelObj[0]->getConsignmentId();
                        $parcelId = $parcelObj[0]->getId();
                        $consignmentFilter = new ConsignmentFilter();
                        $consignmentFilter->addFieldEqualFilter("id", "=", $consignmentId);
                        $consignmentFilter->addFieldNotFilter("awb", "");
                        $consignmentFilter->addFieldNotFilter("consignment_status", Consignment::STATUS_RECYCLED);
                        $conList = $consignmentFilter->getListNew();
                    }
                    if (count($conList) > 0) {
                        $con = $conList[0];
                        $totalWeight += $con->getWeight();
//                        if (!in_array($con->getHandling(), $handlingArr)) {
////                            $handlingArr[] = $con->getHandling();
//                        }
                        $serviceTypeArray[] = $con->getServiceId();
                        if (!in_array($con->getCountryId(), $countryArray)) {
                            $countryArray[] = $con->getCountryId();
                        }
                        //Save Data to parcel bagging mapping
                        $parcelBaggingMapping = new ParcelBaggingMapping();
                        $parcelBaggingMapping->setParcelId($parcelId);
                        $parcelBaggingMapping->setBagId($bagId);
                        $parcelBaggingMapping->save();

                        $bag_array[$NumberofUniqueCode]['bagid'] = $bagId;
                        $bag_array[$NumberofUniqueCode]['parcel_id'] = $parcelId;
                        $NumberofUniqueCode++;
                        $bagging->setBagStatus("");
                        $bagging->save();
                    }
                }
            }
        }
    }
    if (count($bag_array) > 0) {
        //print_r($bag_array);
        $warehouseid = $sessionUser->getWarehouseId();
        $warehouse = new Warehouse($warehouseid);
        $serviceTypeArray = array_unique($serviceTypeArray);
        if (count($serviceTypeArray) > 1) {
            $bagging->setBagType("MIX");
            $bagging->setBagNumber(substr($warehouse->getDescription(), 0, 4) . $bagId . 'MIX');
        } else {
            $bagging->setBagType("NORMAL");
            $bagging->setCountry($countryArray[0]);
            $bagging->setBagNumber(substr($warehouse->getDescription(), 0, 4) . $bagId . substr(strtoupper($serviceTypeArray[0]), 0, 3));
        }
        $bagging->setBagStatus(1);
        $bagging->setService(implode(",", $serviceTypeArray));
        $bagging->setDateUpdated(time());
        $bagging->save();

//        $consignmentBaggingMapping = new ConsignmentBaggingMapping();
//        $consignmentBaggingMapping->bulkDataInsert($bag_array);
        $bag_label = Bagging::CreateBagCsvAndPdf($bagId);
        //echo $bag_label;
        $bagging->setDateUpdated(time());
        $bagging->save();
        //Create Bag Log
        $baggingLatest = new Bagging($bagId);
        $latestBaggingData = serialize($baggingLatest);
        $bagScanLogNew = new BagScanLog();
        $bagScanLogNew->createlog($sessionUser->getId(), '', $bagId, 'BAGGING', $sessionUser->getUserName() . ' has updated bagging ' . $bagId, $oldBaggingData, $latestBaggingData);
    }

    return $bagId;
}

///////////////////////////////////////////////////MAWB AND BOX SCANNING MULTIPLE ////////////////////////////////////////////////////	
/*
 * Scenario 301
 * Get: box number, mawb number, tracking numbers (In json), do_bagging
 * Return: json encode array
 */
if (@$_POST["action"] == 'multiple_box_scan') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $serviceTypeArray = array();
        $arrHoldShipment = array();
        $countryArray = array();
        $holdmessage = '';
        $arr_hold_trackingnumbers = array();
        $set_date_scan_param = '';
        $updateWarehouseIdClause = "";
        $boxNumber = '';
        $carrierName = '';
        $trackpoint = '';
        $message = '';
        $message_bag = '';
        $bag_label = '';
        $sessionUser = SessionManager::getUser();
        $handlingArr = array();
        $parameters = "tl=en&q=";  //////////////// PARAMETERS SETTING FOR GOOGLE SPEECH ///////////////////////////////////////////
        if ($sessionUser->getId() == 0) {
            $msg = Translation::GetCaption("SESSION_EXPIRED");
            $arr = array('result' => 'error', 'message' => $msg);
            echo json_encode($arr);
            return;
        }

        $email = $sessionUser->getEmail();
        $countryId = $sessionUser->getCountryId();
        $country = new Country($countryId);
        $trackpoint = '';
        $countryIso3 = '';
        $warehouseName = '';
        $carrierDesc = '';
        if (count($country) > 0)
            $countryIso3 = $country->getIso3();

        $warehouseid = $sessionUser->getWarehouseId();
        if ($warehouseid > 0) {
            $warehouseObj = new Warehouse($warehouseid);
            $warehouseName = $warehouseObj->getWarehouseName();
        }
        $trackpoint = $warehouseName . " - " . $countryIso3;
        if (!empty($warehouseName))
            $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;

        $parcel_update_columns = "";
        $parcel_where_clause = "";
        $boxNumber = trim(@$_POST["boxnumber"]);
        $scanType = trim(@$_POST["scan_type"]);
        $outboundService = "";
        if (is_array($_POST["outbound_service"])) {
            $outboundService = implode(",", $_POST["outbound_service"]);
        } else {
            $outboundService = $_POST["outbound_service"];
        }
        $mawbNumber = trim(@$_POST["mawbnumber"]);
        if (!empty($mawbNumber)) {
            $mawbId = getMawbIdFromMawbNumber($mawbNumber);
            if ($mawbId == "") {
                if (strlen($mawbNumber) > 12 || strpos($mawbNumber, "-") != 3) {
                    $arr = array('result' => 'error', 'mawb' => 'error', 'message' => 'MAWB format is not supported');
                    echo json_encode($arr);
                    return;
                } else {
                    $mawbObj = new Mawb();
                    $mawbObj->setMawbSourceCountryId($countryId);
                    $mawbObj->setMawbDestinationCountryId($countryId);
                    $mawbObj->setMawbNumber($mawbNumber);
                    $mawbObj->setIsActive('y');
                    $mawbObj->setAddedDate(date("Y-m-d H:i:s", time()));
                    $mawbObj->setUpdatedDate(date("Y-m-d H:i:s", time()));
                    $mawbObj->setAddedBy($sessionUser->getId());
                    $mawbObj->save();
                    $mawbId = $mawbObj->getId();
                }
            }
        }
        $json_trackingnumbers = trim(@$_POST["trackingnumbers"]);
        $do_bagging = $_POST["do_bagging"];
        $preSortCarrier = $_POST["presort_carrier"];
        $hubId = $_POST["hub"];
        $dateTime = date("Y-m-d H:i:s");
        $arr_trackingnumbers = json_decode(stripslashes($json_trackingnumbers));
        $trackingCount = count($arr_trackingnumbers);
        $createBagArr = [];
        $notScannedArr = [];
        $scannedArr = [];
        $notBaggingMsg = "";
        $baggingMsg = "";
        $bagId = getBagId($boxNumber);
        $parcelIdArr = [];
        /*
         * Scenario 301
         * Get: tracking numbers (In array)
         * Return: bag id
         */
        $trackingDataArr = [];
        $trackingDataFinal = [];
        //Get parcels ids from tracking
        foreach ($arr_trackingnumbers as $arrTrackingnumber) {
            $parcelId = getParcelIdFromTrackingNumber($arrTrackingnumber);
            $parcelIdArr[] = $parcelId;
            $parcelObj = new Parcel($parcelId);
            $consignment = new Consignment($parcelObj->getConsignmentId());
            $parcelStatusCode = $parcelObj->getParcelStatusCode();
            $parcelAlreadyScanned = alreadyScannedParcel($arrTrackingnumber, $warehouseid);
            $parcelReturned = false;
            if($parcelObj->getParcelStatusCode() == Consignment::STATUS_RETURNED){
                $parcelReturned = true;
            }
            if (!empty($parcelId) && ($parcelStatusCode != Consignment::STATUS_HOLD) && !$parcelAlreadyScanned && !$parcelReturned && $consignment->getConsignmentType() == "outbound") {
                $createBagArr[] = $arrTrackingnumber;
                if($scanType !="scan_outbound"){
                    if(!empty($arrTrackingnumber)){
                        //Save tracking data into tracking_data table
                        $trackingDataArr['entity_id'] = $parcelId;
                        $trackingDataArr['entity_type'] = 'parcel';
                        $trackingDataArr['tracking_number'] = ParseTrackingNumber::Parse($arrTrackingnumber);
                        $trackingDataArr['user_id'] = $sessionUser->getId();
                        $trackingDataArr['track_point'] = $trackpoint;
                        $trackingDataArr['carrier_desc'] = $carrierDesc;
                        $trackingDataArr['date_created'] = date('Y-m-d H:i:s');
                        $trackingDataArr['ip_address'] = getClientIp();
                        $trackingDataArr['status_code_id'] = 146;
                        $trackingDataArr['warehouse_id'] = $sessionUser->getWarehouseId();
                        $trackingDataFinal[] = $trackingDataArr;
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
                                $mawbParcelMapping->setAddedBy($sessionUser->getId());
                                $mawbParcelMapping->save();
                            }
                        }
                    }
                    //Change status (New)
                    Parcel::setParcelStatus($parcelId, Consignment::STATUS_RECEIVED);
                    changeConStatusByParcelStatus($parcelId);
                }
                if ($do_bagging != "Y" && !empty($bagId)) {
                    //Check if parcel is not in bagging table
                    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                    $parcelBaggingMappingFilter->addFieldFilter("    parcel_id", $parcelId);
                    $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
                    $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id");
                    if(count($parcelBaggingMappingObj) == 0){
                        //Save Data to parcel bagging mapping
                        $parcelBaggingMapping = new ParcelBaggingMapping();
                        $parcelBaggingMapping->setParcelId($parcelId);
                        $parcelBaggingMapping->setBagId($bagId);
                        $parcelBaggingMapping->save();
                    }
                }
                if ($mawbId > 0) {
                    //Check if any mabw is already added to same bag
                    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                    $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                    $mawbParcelMappingFilter->addFieldFilter("    parcel_id", $parcelId);
                    $mawbParcelMappingFilter->addFieldFilter("    wharehouse_id", $warehouseid);
                    $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
                    $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList('id');
                    if(count($mawbParcelMappingObj) == 0){
                        //Add data to mawb_parcel_mapping table
                        $mawbParcelMapping = new MawbParcelMapping();
                        $mawbParcelMapping->setMawbId($mawbId);
                        $mawbParcelMapping->setParcelId($parcelId);
                        $mawbParcelMapping->setWharehouseId($warehouseid);
                        $mawbParcelMapping->setAddedBy($sessionUser->getId());
                        $mawbParcelMapping->setDateAdded(time());
                        $mawbParcelMapping->setBagId($bagId);
                        $mawbParcelMapping->save();
                    }
                }
            } else {
                if ($parcelStatusCode == Consignment::STATUS_HOLD) {
                    $notBaggingMsg .= "<br/><span class='font-red-mint'>[ " . $arrTrackingnumber . " ] Parcel is on hold";
                }else if($parcelAlreadyScanned){
                    $notBaggingMsg .= "<br/><span class='font-red-mint'>[ " . $arrTrackingnumber . " ] Parcel is already scanned";
                }else if($parcelReturned){
                    $notBaggingMsg .= "<br/><span class='font-red-mint'>[ " . $arrTrackingnumber . " ] Parcel is on returned";
                } else {
                    // Add data into not_found_record
                    $notFoundData['mawb'] = $mawbNumber;
                    $notFoundData['bag_number'] = $boxNumber;
                    $notFoundData['tracking_number'] = $arrTrackingnumber;
                    $notFoundData['scanned_by'] = $sessionUser->getId();
                    $notFoundData['length'] = "";
                    $notFoundData['width'] = "";
                    $notFoundData['height'] = "";
                    $notFoundData['weight'] = "";
                    $notFoundData['date_created'] = $dateTime;
                    saveNotFoundRecord($notFoundData);
                    $notBaggingMsg .= "<br/><span class='font-red-mint'>[ " . $arrTrackingnumber . " ] Parcel not found in system";
                }
            }
        }
        if (count($trackingDataFinal) > 0 && $scanType !="scan_outbound") {
            //add data to tracking_data table
            saveDataToTrackingData($trackingDataFinal, false);
        }
        if (count($createBagArr) > 0) {
            if ($do_bagging == "Y") {
                $bagId = CreateCarton($createBagArr);
                if (empty($outboundService)) {
                    foreach ($arr_trackingnumbers as $trackingnumber) {
                        //Get carrier service from parcels
                        $serviceId = getConsignmentServiceIdFromParcelTracking($trackingnumber);
                        $baggingServiceMapping = new BaggingServicesMapping();
                        $baggingServiceMapping->setBagId($bagId);
                        $baggingServiceMapping->setServiceId($serviceId);
                        $baggingServiceMapping->setService($outboundService);
                        $baggingServiceMapping->save();
                    }
                } else {
                    $baggingServiceMapping = new BaggingServicesMapping();
                    $baggingServiceMapping->setBagId($bagId);
                    $baggingServiceMapping->setServiceId($outboundService);
                    $baggingServiceMapping->save();
                }
                if ($bagId > 0) {
                    $bagging = new Bagging($bagId);
                    $message = "Bag " . $bagging->getBagNumber() . " created successfully";
                    $services = new Services($outboundService);
                    $serviceName = $services->getCode();
                    if($serviceName == "STVIAEURO"){
                        $viaEurope = new ViaEurope();
                        $bulkLabelResponse = $viaEurope->createBulkParcelLabel($bagId);
                        $bulkLabel = json_decode($bulkLabelResponse);
                    
                        if($bulkLabel->STATUS == "SUCCESS"){
                            $baggingNew = new Bagging($bagId);
                            $message = "Bag " . $baggingNew->getBagNumber() . " created successfully";
                            $fileName = $bulkLabel->FILENAME;
                            $bagging->setPdf($fileName);
                            $bagging->save();
                            $createFlatParcelResponse = $viaEurope->createFlatParcel($arr_trackingnumbers);
                            $createFlatParcelResponse = json_decode($createFlatParcelResponse);
                            if($createFlatParcelResponse->STATUS == "ERROR"){
                                $arr = array('result' => 'error', 'message' => $createFlatParcelResponse->MESSAGE, 'tracking_numbers' => $createBagArr, 'filename' => "");
                            }
                        }
                    }
                    else
                    {
                        $boxLabel = new BoxLabel();
                        $fileName = $boxLabel->buildPDFDocuments($bagging->getBagNumber(), $trackingCount, $preSortCarrier, "", $warehouseName);
                        $bagging->setPdf($fileName);
                        $bagging->save();
                    }
                   
                    $baggingMsg = $message . $notBaggingMsg;
                    $arr = array('result' => 'success', 'message' => $baggingMsg, 'tracking_numbers' => $createBagArr, 'filename' => $bagging->getPdf());
                    foreach ($parcelIdArr as $parcelIdNew) {
                        //Check if parcel is not in bagging table
                        $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                        $parcelBaggingMappingFilter->addFieldFilter("    parcel_id", $parcelIdNew);
                        $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
                        $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id");
                        if(count($parcelBaggingMappingObj) == 0){
                            //Save Data to parcel bagging mapping
                            $parcelBaggingMapping = new ParcelBaggingMapping();
                            $parcelBaggingMapping->setParcelId($parcelIdNew);
                            $parcelBaggingMapping->setBagId($bagId);
                            $parcelBaggingMapping->setAddedDate(time());
                            $parcelBaggingMapping->setAddedBy($sessionUser->getId());
                            $parcelBaggingMapping->save();
                        }
                    }
                } else {
                    $message = "Carton not created";
                    $baggingMsg = $message . $notBaggingMsg;
                    $arr = array('result' => 'error', 'message' => $baggingMsg, 'tracking_numbers' => $createBagArr, 'filename' => "");
                }
            } else if ($do_bagging == "N") {
                $serviceName = "";
                $message = "Parcels scanned successfully ";
                $baggingMsg = $message . $notBaggingMsg;
                $serviceName = getServiceFromParcelTracking($createBagArr);
                $boxLabel = new BoxLabel();
                $fileName = $boxLabel->buildPDFDocuments("", $trackingCount, "", $mawbNumber, $warehouseName, $serviceName);
                $arr = array('result' => 'success', 'message' => $baggingMsg, 'tracking_numbers' => $createBagArr, 'filename' => $fileName);
            } else {
                $message = "Carton not created";
                $baggingMsg = $message . $notBaggingMsg;
                $arr = array('result' => 'error', 'message' => $baggingMsg, 'tracking_numbers' => $createBagArr, 'filename' => "");
            }
        } else {
            $message = "Carton not created";
            $baggingMsg = $message . $notBaggingMsg;
            $arr = array('result' => 'error', 'message' => $baggingMsg, 'tracking_numbers' => $createBagArr, 'filename' => "");
        }
        echo json_encode($arr);
        return;



        if ($boxNumber != '')
            $message_bag = " and Bag number $boxNumber ";
        $message = "MAWB $mawbNumber $message_bag saved successfully.";

        $bag_size = '';
        $bag_weight = 0;
        $bag_status = '';
        $handling = '';
        $handlingArr = array();

        $account = $sessionUser->getAccount();
        $location = $mawbNumber . ' ' . $boxNumber;
        $totalTrackingNumbers = count($arr_trackingnumbers);

        if ($totalTrackingNumbers > 0) {

            $trackingNumberScanned = implode(",", $arr_trackingnumbers);

            $fieldsScanned = array(
                'timestamp' => date("Y-m-d H:i:s"),
                'location' => $location,
                'username' => $account,
                'phonenumber' => '02088676060',
                'barcodelist' => $trackingNumberScanned
            );
            

            ///////////////////////// UPDATE DATA INTO MOBILE APPLICATION //////////////////////////////////////////////////////
//        if ($sessionUser->getWarehouseId() == 10) { //// only for hayess
////            $result = updateDataIntoMobileApplication("http://scanner.oneworldexpress.co.uk/WinMob5App/app-data-receiver2.php", $fieldsScanned);
//        }
            ////////////////////////////// UPDATE DATA INTO SMART SYSTEM ////////////////////////////////////////////////////////
            $NumberofUniqueCode = 0;
            $totalWeight = 0;
            foreach ($arr_trackingnumbers as $trackingNumber) {

                $trackingNumber = trim($trackingNumber);

                if ($trackingNumber != '') {

                    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
                    $arr_update_scan_numbers[] = $trackingNumber;

                    if ($mawbNumber != '' || $bagId > 0) {

                        $consignmentFilter = new ConsignmentFilter();
                        $consignmentFilter->addawbFilter_bag($trackingNumber);
                        $consignmentFilter->addFieldNotFilter("awb", "");
                        $consignmentFilter->addFieldNotFilter("consignment_status", Consignment::STATUS_RECYCLED);
                        $conList = $consignmentFilter->getColumnList("awb, s.code AS handling, weight, service_type, country_iso_code");

                        if (count($conList) > 0) {
                            $con = $conList[0];
                            $ConsignmentLog = new ConsignmentLog();
                            $totalWeight += $con->getWeight();
                            if (!in_array($con->getHandling(), $handlingArr)) {
                                $handlingArr[] = $con->getHandling();
                                $serviceTypeArray[] = $con->getServiceType();
                            }
                            if (!in_array($con->getCountryIsoCode(), $countryArray)) {
                                $countryArray[] = $con->getCountryIsoCode();
                            }
                            if ($bagId > 0) {
                                $bag_array[$NumberofUniqueCode]['bagid'] = $bagId;
                                $bag_array[$NumberofUniqueCode]['consignmentid'] = $con->getId();
                                $NumberofUniqueCode++;
                            }
                        }
                    }
                }
            }

            if (count($arr_update_scan_numbers) > 0) {

                $str_trackingnumbers = "'" . implode("','", $arr_update_scan_numbers) . "'";
                $consignment_filter = new ConsignmentFilter();
                $consignment_filter->addawbFilterList($str_trackingnumbers);
                $consignment_filter->addStatusFilter(Consignment::STATUS_HOLD);
                $list_hold_numbers_message = $consignment_filter->getColumnList("awb, message");
                if (count($list_hold_numbers_message) > 0) {
                    $holdCount = 0;
                    foreach ($list_hold_numbers_message as $hold) {
                        $arrHoldShipment[$holdCount]['awb'] = $hold->getAwb();
                        $arrHoldShipment[$holdCount]['message'] = $hold->getMessage();
                        $arrHoldShipment[$holdCount]['checkbox'] = '<input id="chkHoldNumber[]" name="chkHoldNumber[]"  value="' . $hold->getAwb() . '" checked type="checkbox" />';
                        $holdCount++;
                    }
                    $html = '<BR><BR><span id="spanResolve">Enter Comments :</span> <input id="txtResolve" type="textbox" />&nbsp; <input type="button" id="btnResolve" onclick="resolve();" value="Resolve All" /><BR><BR>';
                    $strHoldShipment = implode('<br>', array_map(function ($hold) {
                                return $hold['checkbox'] . $hold['awb'] . " " . $hold['message'] . " " . $hold['html'];
                            }, $arrHoldShipment));

                    $strHoldShipment .= $html;
                    $headers = "From: ITSupport@oneworldexpress.com \r\n";
                    $headers .= "Reply-To: ops@oneworldexpress.com \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $holdmessage = "Please hold the shipment(s) <BR><BR>" . $strHoldShipment;
                    $holdemailmessage = "Dear All,<BR><BR>Please hold the shipment(s). <BR><BR>" . $strHoldShipment;
                    $arr = array('result' => 'error', 'message' => $holdmessage);
                    echo json_encode($arr);
                    return;
                }
                $sql_update_columns = "JOIN consignment_bagging_mapping cbag
                                   ON c.id = cbag.consignmentid 
                                   JOIN bagging bag
                                   ON cbag.bagid = bag.id 
                                   set
				   $set_date_scan_param
                                   $updateWarehouseIdClause
                                   bag.bagnumber = case when bag.bagnumber is null 
                                   or bag.bagnumber = '' then '$boxNumber' else bag.bagnumber end,										   
                                   c.consignment_status = case 
                                   when c.consignment_status not in('recycled', 'invalid', 'hold') then '$user_scanning_status'
                                   else c.consignment_status end,										 
                                   c.mawb = case when c.mawb = '' or c.mawb is null then '$mawbNumber' else c.mawb end "; // setting columns
//            echo $sql_update_columns;
//            die;
                $sql_where_clause = " c.awb != '' and c.awb in($str_trackingnumbers) and c.is_invoiced != '1' ";
                Consignment::bulkUpdateWithJoin($sql_update_columns, $sql_where_clause);
                /////////////////////// UPDATING PARCELS /////////////////////////////////////
                $parcel_where_clause = " licence_plate != '' and licence_plate in($str_trackingnumbers) and date_scanned is null ";
                if ($parcel_update_columns != '') {
                    Parcel::bulkUpdate($parcel_update_columns, $parcel_where_clause);
                }
                TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $sessionUser);
                if ($preSortCarrier > 0) {
                    $serviceObj = new Services($preSortCarrier);
                    if ($serviceObj->getName() == 'P2') {
                        $p2BagLabel = new P2BagLabel();
                        $fileName = $p2BagLabel->buildPDFDocuments($bagId, $hubId, $jobid);
                    } else {
                        $output = CreatePreSortLabel($hub, count($arr_update_scan_numbers), $totalWeight);
                        if ($output['status'] == 'error') {
                            $arr = array('result' => 'error', 'message' => $output['message']);
                            echo json_encode($arr);
                            return;
                        }
                    }
                } else {
                    $boxLabel = new BoxLabel();
                    $fileName = $boxLabel->buildPDFDocuments($boxNumber, $totalTrackingNumbers, $carrierName, $mawbNumber);
                }
                $arr = array('result' => 'success',
                    'message' => $message,
                    'tracking_numbers' => $arr_trackingnumbers,
                    'hold_tracking_numbers' => $arrHoldShipment,
                    'holdmessage' => $holdmessage,
                    'filename' => count($arrHoldShipment) > 0 ? '' : $fileName);
                NotFoundRecord::AddNotFoundRecords($mawbNumber, $boxNumber, $account, $arr_update_scan_numbers);
                echo json_encode($arr);
            } else {
                $arr = array('result' => 'error', 'message' => Translation::GetCaption('MSG_PLEASE_SCAN_ONE_MORE_TRACKING_NUMBERS'));
                echo json_encode($arr);
            }
        }
    } else {
        echo $chkSession;
    }
    die;
}

function FindYPSParcels($trackingNumber) {
    $account = 'YPS';
    $emailKey = 'Parcel_Dispatched_To_Country';

    $user_child = array();

    $userFilter = new UserAccountFilter();
    $userFilter->addAccountNumberFilter($account);
    $user_list_parent = $userFilter->getColumnList("id");

    if (count($user_list_parent) > 0) {
        $parent_user = $user_list_parent[0];
        $parentid = $parent_user->getId();



        $userFilter = new UserAccountFilter();
        $userFilter->addParentidFilter($parentid);
        //echo $parentid;


        $user_list_child = $userFilter->getColumnList("user_account");

        //print_r($user_list_child);


        if (count($user_list_child) > 0) {

            foreach ($user_list_child as $userObj) {
                $user_child[] = $userObj->getUserAccount();
            }
        }
    }

    //print_r($user_child);


    if (count($trackingNumber) > 0 && count($user_child) > 0) {

        $strAccounts = "'" . implode("','", $user_child) . "'";
        $strTrackingNumbers = "'" . implode("','", $trackingNumber) . "'";

        $conFilter = new ConsignmentFilter();
        $conFilter->addawbFilterList($strTrackingNumbers);
        $conFilter->AddAccountFilterArray($strAccounts);

        $list = $conFilter->getColumnList("id, awb");

        if (count($list) > 0) {
            foreach ($list as $consignent) {
                $trackingNumber[] = $consignent->getAwb();
            }
        }
    }

    return $trackingNumber;
}

function SendEmailToYPSCustomers($trNumber) {

    $trackingNumber = FindYPSParcels($trNumber);

    if (count($trackingNumber) > 0) {


        $account = 'YPS';
        $emailKey = 'Parcel_Dispatched_To_Country';


        $consignmentinformation = array("EmailKey" => $emailKey,
            "TrackingNumbers" => $trackingNumber);

        $json = json_encode($consignmentinformation);


        $client = new SoapClient(null, array(
            'location' => "http://www.yourpersonalshopper.com/api/YpsIntegration.php?wsdl",
            'uri' => "http://yourpersonalshopper.com",
            'trace' => true));





        $client->__soapCall('SendEmail', array('consignmentinformation' => $json));
    }
}

/*
 * Scenario 101
 * Get: boxnumber
 * Return:  result array
 */

function getBagIdFromBagNumber($boxNumber, $palletNo = 0) {
    $bagId = 0;
    $parcelId = 0;
    $consignmentId = 0;
    $holdArray = [];
    $holdArray['hold_awb'] = [];
    if ($boxNumber != '') {
        $baggingFilter = new BaggingFilter();
        $baggingFilter->addFieldEqualFilter("bagnumber", "=", $boxNumber);
        $bagNumberList = $baggingFilter->getColumnList("id");

        //Then add bag id into pallet_entity_mapping as a bag (thern show message that your bag is added to the pallet then remove old bag number)

        $palletEntityMapping = new PalletEntityMapping();
        $palletEntityMapping->setEntityId($bagId);
        $palletEntityMapping->setPalletEntityType($bagId);
        $palletEntityMapping->setPalletId($palletNo);
        $palletEntityMapping->save();

        if (count($bagNumberList) > 0) {
            $bagNumberObj = $bagNumberList[0];
            $bagId = $bagNumberObj->getId();

            //Check if pallet is mapped with entity and here entity is Bag for scanning
            $palletEntityMappingFilter = new PalletEntityMappingFilter();
            $palletEntityMappingFilter->addFieldEqualFilter('entity_id', '=', $bagId);
            $palletEntityMappingFilter->addFieldEqualFilter('pallet_entity_type', '=', 'b');
            $palletEntityMappingFilterList = $palletEntityMappingFilter->getList();
            //Get Pracel if from Parcel bagging 

            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addFieldFilter('    bag_id', $bagId);
            $parcelBaggingMappingobj = $parcelBaggingMappingFilter->getList($columnName);
            if (count($parcelBaggingMappingobj) > 0) {
                foreach ($parcelBaggingMappingobj as $parcelBaggingMapping) {
                    $parcelId = $parcelBaggingMapping->getParcelId();
//                  Get consigment id form parcel table through parcel id
                    $parcel = new Parcel($parcelId);
                    $consignmentId = $parcel->getConsignmentId();
                    $consignment = new Consignment($consignmentId);
                    if ($consignment->getShipmentStatus() == Consignment::STATUS_HOLD) {
                        $holdArray['hold_awb'][] = $consignment->getAwb();
                    }
                }
            }
            $holdArray['bag_id'] = $bagId;
        }
    }
    return $holdArray;
}

//function HoldShipmenfffftExist($boxNumber) {
//    $holdArray = array();
//    $strTrackingNumbers = '';
//
//    $baggingFilter = new BaggingFilter();
//    $baggingFilter->addFieldEqualFilter("bagnumber", "=", $boxNumber);
//    $bagNumberList = $baggingFilter->getColumnList("id");
//    if (count($bagNumberList) > 0) {
//        $bagNumberObj = $bagNumberList[0];
//        $bagId = $bagNumberObj->getId();
//
//        $consignmentBaggingMapFilter = new ConsignmentBaggingMappingFilter();
//        $consignmentBaggingMapFilter->addFieldEqualFilter("bagid", "=", $bagId);
//        $consignmentBaggingMapFilterList = $consignmentBaggingMapFilter->getList();
//        foreach ($consignmentBaggingMapFilterList as $conBagId) {
//            $consignment = new Consignment($conBagId->getConsignmentId());
//            if ($consignment->getShipmentStatus() == Consignment::STATUS_HOLD)
//                $list[] = $consignment;
//        }
//
//        if (count($list) > 0) {
//            foreach ($list as $consignment) {
//                $holdArray[] = $consignment->getAwb();
//            }
//        }
//    }
//    return $holdArray;
//}



/*
 * Scenario 301 , 101
 * Get: Box number
 * Return:  consignment array
 */
function getParcelListFromBagNumber($boxNumber,$parcelArr=false) {
    $list = array();
    $parcelListArr = array();
    if (trim($boxNumber) != '') {
        $bagId = getBagId($boxNumber);
        if ($bagId > 0) {
            $parcelBaggingmapping = new ParcelBaggingMappingFilter();
            $parcelBaggingmapping->addFieldFilter("    bag_id", $bagId);
            $parcelBaggingMappingObj = $parcelBaggingmapping->getColumnList("parcel_id");
            if (count($parcelBaggingMappingObj) > 0) {
                foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                    $list[] = new Parcel($parcelBaggingMappingArr->getParcelId());
                    $parcelListArr[] = $parcelBaggingMappingArr->getParcelId();
                }
            }
        }
    }
    if($parcelArr){
        return $parcelListArr;
    }else{
        return $list;
    }
}

/*
 * Scenario 101
 * Get: Box number
 * Return:  service id
 */

function getBagCarrier($boxNumber) {
    $bag_carrier = '';
    $bag_carrier = trim(strtoupper(Consignment::GetCarrierFromBagNumber($boxNumber)));
    if ($bag_carrier == '') {
        $baggingFilter = new BaggingFilter();
        $baggingFilter->addFieldEqualFilter("bagnumber", "=", $boxNumber);
        $baggingList = $baggingFilter->getColumnList("serviceid");
        if (count($baggingList) > 0) {
            $baggingObj = $baggingList[0];
            $serviceId = $baggingObj->getServiceId();
            //No need for that
//            $service = new Services($serviceId);           
        }
    }
    return $serviceId;
}

function getTotalTrackingNumbersScanned($boxNumber) {
    $trackingNumberlist = [];
    //Get Bag Id
    $bagId = getBagId($boxNumber);
    if ($bagId > 0) {
        $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
        $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
        $parcelMappingObj = $parcelBaggingMappingFilter->getList();
        foreach ($parcelMappingObj as $parcelMappingArr) {
            $parcelId = $parcelMappingArr->getParcelId();
            $parcelObj = new Parcel($parcelId);
            if ($parcelObj->getParcelStatusCode() != Consignment::STATUS_HOLD) {
                $trackingNumberlist[] = $parcelObj->getTrackingNumber();
            }
        }
    }
    $totalTrackingNumbers = count($trackingNumberlist);
    return $totalTrackingNumbers;
}

/*
 * Scenario 101
 * Get: Bag id, pallet id
 * Return:  true, false
 */

function IsBagExistOnPallet($bagId, $palletId) {
    $bagExist = false;
    $palletEntityMappingFilter = new PalletEntityMappingFilter();
    $palletEntityMappingFilter->addFieldEqualFilter("entity_id", "=", $bagId);
    $palletEntityMappingFilter->addFieldEqualFilter("pallet_id", "=", $palletId);
    $palletEntityMappingFilter->addFieldEqualFilter("pallet_entity_type", "=", "b");
    $list = $palletEntityMappingFilter->getColumnList('id');
    if (count($list) > 0) {
        $bagExist = true;
    }

    return $bagExist;
}

/////////////////////////////////////////////////////////////// BOX SCANNING ////////////////////////////////////////////////////
/*
 * Scenario 101
 * Get: boxnumber, mawbnumber, boxweight, palletno
 * Return:  Json encoded result array
 */
if (@$_POST["action"] == 'box_scan') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $labelData = [];
        $returnArr = [];
        $parcelArr = [];
        $boxNumber = '';
        $carrierName = '';
        $trackingFound = 0;
        $palletEntityFound = 0;
        $message = '';
        $holdmessage = '';
        $yodelBagLabel = '';
        $fileName = '';
        $parameters = "tl=en&q=";  //////////////// PARAMETERS SETTING FOR GOOGLE SPEECH /////////////////////
        $bagId = 0;
        $arrHoldShipment = [];
        $boxNumber = trim(@$_POST["boxnumber"]);
        $mawbNumber = trim(@$_POST["mawbnumber"]);
        $boxweight = trim(@$_POST["boxweight"]);
        $palletno = trim(@$_POST['palletno']);
        $preSortChk = trim(@$_POST['pre_sort']);
        $carrierId = trim(@$_POST['carrier_id']);
        $carrierHubId = trim(@$_POST['carrier_hub_id']);
        if ($carrierHubId > 0)
            $carrierHubId = trim(@$_POST['carrier_hub_id']);
        else
            $carrierHubId = 0;

        $palletEntityId = 0;
        $palletId = 0;
        $user = SessionManager::getUser();
        $trackingData = [];
        $trackingDataFinal = [];

        $countryId = $user->getCountryId();
        $country = new Country($countryId);
        $trackpoint = '';
        $countryIso3 = '';
        $warehouseName = '';
        $carrierDesc = '';
        if (count($country) > 0)
            $countryIso3 = $country->getIso3();

        $warehouseid = $user->getWarehouseId();
        if ($warehouseid > 0) {
            $warehouseObj = new Warehouse($warehouseid);
            $warehouseName = $warehouseObj->getWarehouseName();
        }
        $trackpoint = $warehouseName . " - " . $countryIso3;
        if (!empty($warehouseName))
            $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;

        /*
         * Get pallet id from pallet table from pallet number 
         */
        if ($palletno != "") {
            $palletFilter = new PalletFilter();
            $palletFilter->addPalletNumberFilter($palletno);
            $palletIdObj = $palletFilter->getColumnList('id');
            if (count($palletIdObj) > 0)
                $palletId = $palletIdObj[0]->getId();
        }
        if ($palletId == 0) {
            $message = "Pallet number is invalid";
            $arr = array('result' => 'error', 'message' => $message);
            echo json_encode($arr);
            return;
        }
        $mawbId = getMawbIdFromMawbNumber($mawbNumber);
        if(!empty($mawbNumber) && $mawbId == ""){
            if(!empty($mawbNumber)){
                if(strlen($mawbNumber) > 12 || strpos($mawbNumber, "-") != 3){
                    $message = "MAWB format is not supported";
                    $arr = array('result' => 'error','mawb' => 'error', 'message' => $message);
                    echo json_encode($arr);
                    return;
                }
            }
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
        }
        /*
         * Add bag id into pallet_entity_mapping
         * If bag added successfully empty the field so other bag should add into table
         */
        if ($boxNumber != '') {
            $baggingFilter = new BaggingFilter();
            $baggingFilter->addFieldEqualFilter("    bagnumber", "=", $boxNumber);
            $bagObj = $baggingFilter->getColumnList("id");
            if (count($bagObj) > 0) {
                $bagNumberObj = $bagObj[0];
                $bagId = $bagNumberObj->getId();
                if(!empty($mawbNumber)){
                    // Check if bag's all parcel is added into same MAWB number
                    $errorReturn = checkAllParcelSameMawb($bagId, $mawbId, $warehouseid,$boxNumber);
                    if(!empty($errorReturn)){
                        foreach ($errorReturn as $errorRtn) {
                         $message .= $errorRtn."<br />";
                        }
                        $arr = array('result' => 'error', 'message' => $message);
                        echo json_encode($arr);
                        return;
                        die;
                    }
                }
                //Get parcels of bag
                $parcelArr = getParcelIdFromBagId($bagId);
                // Check if this pallet is already dispatched
                $palletFilter = new PalletFilter();
                $palletDispatch = $palletFilter->checkPalletDispatchClose($palletno);
                if(count($palletDispatch) > 0){
                    $message = "You can not add bag into already closed/dispatched pallet";
                        $arr = array('result' => 'error', 'message' => $message);
                        echo json_encode($arr);
                        return;
                }
                //Check if this bag added to some other pallet
                $palletEntityMappingFilter = new PalletEntityMappingFilter();
                $palletEntityMappingFilter->addFieldEqualFilter("pallet_entity_type", "=", "b");
                $palletEntityMappingFilter->addFieldEqualFilter("entity_id", "=", $bagId);
                $palletEntityMappingObj = $palletEntityMappingFilter->getColumnList("id,pallet_id");
                if (count($palletEntityMappingObj) > 0) {
                    $palletEntityFound = 1;
                    $palletNewObj = new Pallet($palletEntityMappingObj[0]->getPalletId());
                    $palletName = $palletNewObj->getPalletno();
                    if ($palletEntityMappingObj[0]->getPalletId() != $palletId) {
                        $message = "Bag is already added into <b>" . $palletName . "</b> Pallet. Do you want to remove this bag and add in the new pallet <b>" . $palletno . "</b>";
                        $arr = array('result' => 'error', 'remove_pallet' => 'pallet', 'message' => $message);
                        echo json_encode($arr);
                        return;
                    } else if($palletEntityMappingObj[0]->getPalletId() == $palletId){
                         $message = "Bag is already added into same <b>" . $palletName . "</b> Pallet";
                        $arr = array('result' => 'error', 'message' => $message);
                        echo json_encode($arr);
                        return;
                    }else {
                        $palletEntityFound = 0;
                        $palletEntityId = $palletEntityMappingObj[0]->getId();
                    }
                }
                if(count($parcelArr) > 0){
                    foreach ($parcelArr as $parcelId) {
                        $parcel = new Parcel($parcelId);
                        $trackingNo = $parcel->getTrackingNumber();
                        if(Consignment::checkConsignmentInvoiced($parcel->getConsignmentId())){
                            $message .= "Parcel [".$trackingNo."] already invoiced<br />" ;
                            $trackingFound = 1;
                        }
                        if (alreadyScannedParcel($trackingNo, $warehouseid)) {
                            $message .= "Parcel [".$trackingNo."] already scanned<br />" ;
                            $trackingFound = 1;
                        }
                        if($parcel->getParcelStatusCode() == Consignment::STATUS_RETURNED){
                            $message .= "Parcel [ ".$trackingNo." ] is returned Parcel";
                            $trackingFound = 1;
                        }
                    }
                    if($trackingFound == 1){
                       $arr = array('result' => 'error', 'message' => $message);
                        echo json_encode($arr);
                        return;
                    }
                }
                if($trackingFound == 0 && $palletEntityFound == 0){
                    //Save data to pallet entity mapping table
                    $palletEntityMapping = new PalletEntityMapping();
                    $palletEntityMapping->setEntityId($bagId);
                    $palletEntityMapping->setPalletEntityType('b');
                    $palletEntityMapping->setPalletId($palletId);
                    $palletEntityMapping->setPreSort('n');
                    $palletEntityMapping->save();
                    $palletEntityId = $palletEntityMapping->getId();
                }
                //Get Carrier Group Info
                $palletCarierGroup = new PalletCarierGroup($carrierId);
                $palletCarrierGroupName = $palletCarierGroup->getGroupName();
//          Get carrier id from carrier group id
                $carrierId = getCarrierIdFromCarrierGroup($carrierId);
//            Get pre sort service code from carrier id
                $serviceCodeObj = ServiceFilter::getPreSortServiceByCarrierId($carrierId, "code");
                if (count($serviceCodeObj) > 0) {
                    $serviceCode = $serviceCodeObj[0]->getCode();
                } else {
                    $preSortChk == 'n';
                }
                /*
                 * Check if Pre sort outer label is selected then scan only sort bag's parcel
                 * 
                 */
                If ($preSortChk == 'y') {
                    if (ParcelBaggingMappingFilter::checkPreSortBag($bagId, $carrierHubId)) {
                        $labelData = createPreSortLabelCheck($boxNumber, $carrierHubId, $serviceCode);
                        if (!empty($labelData['error'])) {
                            $arr = array('result' => 'error', 'message' => $labelData['message']);
                            echo json_encode($arr);
                            die;
                        }
                    } else {
                        $totalTrackingNumbers = getTotalTrackingNumbersScanned($boxNumber);
                        $boxLabelObj = new BoxLabel();
                        $labelData['label'] = $boxLabelObj->buildPDFDocuments($boxNumber, $totalTrackingNumbers, $palletCarrierGroupName, $mawbNumber, $warehouseName);
                    }
                } else {
                    $totalTrackingNumbers = getTotalTrackingNumbersScanned($boxNumber);
                    $boxLabelObj = new BoxLabel();
                    $labelData['label'] = $boxLabelObj->buildPDFDocuments($boxNumber, $totalTrackingNumbers, $palletCarrierGroupName, $mawbNumber, $warehouseName);
                }
                $bagObj = new Bagging($bagId);
                $bagObj->setBagLabel($labelData['label']);
                $bagObj->save();
                if ($bagId > 0) {
                    $baggingOldData = new Bagging($bagId);
                    $baggingSerOldData = serialize($baggingLatest);
                    //Add Bag weight value into Bagging table
                    $baggingNewObj = new Bagging($bagId);
                    $baggingNewObj->setActualWeight($boxweight);
                    $baggingNewObj->save();
                    //Create log for weight updated
                    $baggingLatest = new Bagging($bagId);
                    $baggingLatestSerData = serialize($baggingLatest);
                    $bagScanLogNew = new BagScanLog();
                    $bagScanLogNew->createlog($user->getId(), '', $bagId, 'BAGGING', $user->getUserName() . ' has updated bagging ' . $bagId, $baggingSerOldData, $baggingLatestSerData);
                    //Get parcels of bag
                    $parcelArr = getParcelIdFromBagId($bagId);
                    foreach ($parcelArr as $parcelId) {
                        $parcel = new Parcel($parcelId);
                        $trackingNo = $parcel->getTrackingNumber();
                        if(!empty($trackingNo)){
                            //Change status (New)
                            Parcel::setParcelStatus($parcelId, Consignment::STATUS_RECEIVED);
                            changeConStatusByParcelStatus($parcelId);

                            $trackingData['entity_id'] = $parcelId;
                            $trackingData['entity_type'] = "parcel";
                            $trackingData['tracking_number'] = $parcel->getTrackingNumber();
                            $trackingData['user_id'] = $user->getId();
                            $trackingData['track_point'] = $trackpoint;
                            $trackingData['date_created'] = date("Y-m-d H:i:s");
                            $trackingData['ip_address'] = getClientIp();
                            $trackingData['status_code_id'] = 146;
                            $trackingData['carrier_desc'] = $carrierDesc;
                            $trackingData['warehouse_id'] = $user->getWarehouseId();
                            $trackingDataFinal[] = $trackingData;
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
                    //add data to tracking_data table
                    saveDataToTrackingData($trackingDataFinal, false);
                }
            } else {
                $message = Translation::GetCaption("MSG_CARTON_NUMBER_INVALID");
                $arr = array('result' => 'error', 'message' => $message);
                echo json_encode($arr);
                return;
            }
        }
        if ($palletEntityId > 0) {
            $message = 'Bag added successfully !';
            $arr = array('result' => 'success', 'message' => $message, 'label' => $labelData['label']);
            echo json_encode($arr);
            die;
        }
        //Save parcels data into tracking table
        //Change consignment status (all parcels are scanned then complete scanned, if some are remaining then partial scanned)
        //Old Logic   

        /*
         * Scenario 101
         * Get: boxnumber
         * Return:  result array
         */
//    $returnArr = getBagIdFromBagNumber($boxNumber,$palletno);
//    if(!empty($returnArr['bag_id']))
//        $bagId = $returnArr['bag_id'];
//    if(!empty($returnArr['hold_awb']))
//        $arrHoldShipment = $returnArr['hold_awb'];
// 
//    if ($bagId == 0) {
//        $message = Translation::GetCaption("MSG_CARTON_NUMBER_INVALID");
//        $arr = array('result' => 'error', 'message' => $message);
//        echo json_encode($arr);
//        return;
//    }
//    $bagging = new Bagging($bagId);
//    if ($bagging->getBagType() == 'MIXED' || $bagging->getBagType() == 'MIX') {
//        $message = Translation::GetCaption("MSG_BOX_CONTAINS_MIXED_SERVICES");
//        $arr = array('result' => 'error', 'message' => $message);
//        echo json_encode($arr);
//        return;
//    }
//    if (count($arrHoldShipment) > 0) {
//        $strHoldShipment = implode("<br>", $arrHoldShipment);
//        $holdmessage = Translation::GetCaption("MSG_OPEN_BOX_HOLD_SHIPMENTS") . $strHoldShipment;
//        ////////// IF YOU WANT TO GIVE THEM A LABEL ON HOLD JUST COMMENT THE BELOW TWO LINES ////
//        $arr = array('result' => 'error', 'message' => $holdmessage);
//        echo json_encode($arr);
//        return;
//    }
//    if ($palletno !== '' && $boxNumber !== '') {
//        $palletId = 0;
//        $pallet = new PalletFilter();
//        $pallet->addPalletNumberFilter($palletno);
//        $palletObj = $pallet->getColumnList('id');//Correct direct get 
//        if(count($palletObj) > 0)
//            $palletId = $palletObj[0]->getId();
//        /*
//        * Scenario 101
//        * Get: Bag id, pallet id
//        * Return:  true, false
//        */
//        $isBagPlacedOnPallet = IsBagExistOnPallet($bagId, $palletid);        
//        if(!$isBagPlacedOnPallet) {
//            $serviceId = getBagCarrier($boxNumber);
//            $pallet = '';
//            $carrierMatched = false;
//            $palletFilter = new PalletFilter();
//            $palletFilter->addPalletNumberFilter($palletno);
//            $palletList = $palletFilter->getList();       
//            if (count($palletList) > 0) {
//                $pallet = $palletList[0];
//            }
//            $palletCarrierId = $pallet->getPalletCarrierId();            
//            $palletCarrier = new PalletCarrier($palletCarrierId);
//            $serviceIdArray = explode("," , $palletCarrier->getServiceId());
//            foreach($serviceIdArray as $serviceIdValue) {
//                if($serviceId == $serviceIdValue){
//                    $carrierMatched = true;
//                }
//            }
//            if ($carrierMatched) {
////                $palletBagMap = new PalletBagMapping();
//                $palletEntityMap = new PalletEntityMapping();
//                $palletEntityMap->setPalletId($pallet->getId());
//                $palletEntityMap->setEntityId($bagId);
//                $palletEntityMap->setpalletEntityType('b');
//                $palletEntityMap->save();
//                $msg = Translation::GetCaption("CARTON_NUMBER") . " $boxNumber added to the pallet number $palletno";
//                $arr = array('result' => 'success',
//                    'message' => $msg,
//                    'carrier' => $bag_carrier);
//            } else {
//                $msg = "Carton number $boxNumber can not place into pallet number " . $palletno . " because carton belongs to $bag_carrier service";
//                $arr = array('result' => 'error',
//                    'message' => $msg);
//                echo json_encode($arr);
//                return;
//            }
//        } else {
//            $msg = "Carton number $boxNumber already added to the pallet number " . $carton_pallet_no;
//            $arr = array('result' => 'error',
//                'message' => $msg);
//            echo json_encode($arr);
//            return;
//        }
//    }
//    $track_point = '';
//    $user_scanning_status = '';
////    $country_filter = new CountryFilter();
////    $country_filter->addIsoFilter($user->getCountry());
////    $country_list = $country_filter->getList();
////    if (count($country_list) > 0) {
////        $countryObject = $country_list[0];
////        $track_point = $countryObject->getName();
////    }
//    
//    
//    
////    $warehouseid = $user->getWarehouseId();
////    if ($user->getUserType() == 'warehouse')
////        $user_scanning_status = Consignment::STATUS_WAREHOUSE_RECEIVED;
////    elseif ($track_point == 'Poland')
////        $user_scanning_status = strtolower($track_point) . " received";
////    else
////        $user_scanning_status = Consignment::STATUS_DATAREADY_SUPPLIER;
//
//    if ($boxNumber !== '') {
//        
//        /*
//        * Scenario 101
//        * Get: Bag id
//        * Return:  consignment arrays
//        */
//        $list = getParcelListFromBagNumber($boxNumber);
//        $bag_size = '';
//        $bag_weight = 0;
//        $bag_status = '';
//        $handling = '';
//        $handlingArr = array();
//
//        $trackDataArr = array();
//        $trackDataHoldArr = array();
//        $conIdArray = array();
//        $serviceIdArr = array();
//        $NumberofUniqueCode = 0;
//        $holdCount = 0;
//        if (count($list) > 0) {
//                foreach ($list as $con) {
//                    $trackingNumbersArr[] = $con->getAwb();
//                    $serviceId = $con->getServiceId();
//                    if (!in_array($serviceId, $serviceIdArr)) {
//                        $serviceIdArr[] = $serviceId;
//                    }
//                    if ($con->getShipmentStatus() == Consignment::STATUS_HOLD) {
//                        $trackDataHoldArr[$holdCount]['consignment_id'] = $con->getID();
//                        $trackDataHoldArr[$holdCount]['tracking_number'] = $con->getAwb();
//                        $trackDataHoldArr[$holdCount]['status_code'] = Consignment::STATUS_HOLD;
//                        $trackDataHoldArr[$holdCount]['description'] = Consignment::STATUS_HOLD;
//                        $trackDataHoldArr[$holdCount]['track_point'] = $track_point;
//                        $trackDataHoldArr[$holdCount]['date_created'] = date("Y-m-d H:i:s");
//                        $trackDataHoldArr[$holdCount]['mawb'] = '';
//                        $trackDataHoldArr[$holdCount++]['account'] = $user->getAccount();
//                    }
//                }
//                $account = $user->getAccount();
//                $location = $mawbNumber . ' ' . $boxNumber; // concatenation of mawb number and box number
//                $totalTrackingNumbers = getTotalTrackingNumbersScanned($boxNumber);
//                TrackingData::AddVirtualTrackingToScanParcels($trackingNumbersArr, $user);
//                if (count($trackDataArr) > 0) {
//                    $trackingDataFilter = new TrackingDataFilter();
//                    $trackingDataFilter->bulkDataInsert($trackDataArr);
//                }
//                if (count($trackDataHoldArr) > 0) {
//                    $trackingDataFilter = new TrackingDataFilter();
//                    $trackingDataFilter->bulkDataInsert($trackDataHoldArr);
//                }
//                if (count($serviceIdArr) > 0) {
//                    $serviceFilter = new ServiceFilter();
//                    $serviceFilter->addIdArrayFilter($serviceIdArr);
//                    $serviceFilterList = $serviceFilter->getColumnList("name, carrier_id");
//                    if(count($serviceFilterList) > 0) {
//                        foreach($serviceFilterList as $service) {
//                            $carrierId = $service->getCarrierId();    
//                            $serviceName = $service->getName();
//                            $carrier = new Carrier($carrierId);
//                            $carrierName = $carrier->getCarrier();
//                            
//                        }
//                    }
//                }
//                $message .= "Total Items scanned are $totalTrackingNumbers and the carton weight is $bag_weight Kilograms. ";
//                $message .= $holdmessage;
//                $labelFileName = '';
//                if ($totalTrackingNumbers > 0 ) {
//                    $yodelBagLabel = '';
//                    $boxLabel = new BoxLabel();
//                    $fileName = $boxLabel->buildPDFDocuments($boxNumber, $totalTrackingNumbers, $serviceName, $mawbNumber, $track_point);
//                    $ConsignmentLog = new ConsignmentLog();
//                    $ConsignmentLog->createlog("Bag Label Link : " . $fileName,'','','','',$user->getId());
//                }
//                $arr = array('result' => 'success', 'message' => $message, 'tracking_numbers' => $trackingNumbersArr,
//                    'filename' => $fileName, 'yodel_bag_label' => $yodelBagLabel, 'hold_tracking_numbers' => $arrHoldShipment, 'holdmessage' => $holdmessage);
//                echo json_encode($arr);
//        }
//        else {        
//            $message = Translation::GetCaption("MSG_CARTON_NUMBER_INVALID");
//            $arr = array('result' => 'error', 'message' => $message);
//            echo json_encode($arr);
//        }
//    }
    } else {
        echo $chkSession;
    }
    die;
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

/*
 * Scenario 101
 * Get: Tracking Data array
 * Return: success message in json array
 */

function getConsignmentIdFromParcelId($parcelId) {
    $consignmentId = 0;
    if ($parcelId > 0) {
        $parcelObj = new Parcel($parcelId);
        $consignmentId = $parcelObj->getConsignmentId();
    } else {

    }
    return $consignmentId;
}

/*
 * Scenario 101
 * Get: Tracking Data array
 * Return: success message in json array
 */

//function changeConStatusByParcelStatus($parcelId, $conOtherCode = "") {
////    Consignment::STATUS_P
//    if ($parcelId > 0) {
//        $parcelObj = new Parcel($parcelId);
//        $consignmentId = $parcelObj->getConsignmentId();
//        $consignmentObj = new Consignment($consignmentId);
//        //If status is passed by parameters
//        if(!empty($conOtherCode)){
//            if(isset(Consignment::$database_status_array[$conOtherCode])){
//                $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[$conOtherCode]);
//            }
//            $consignmentObj->setShipmentStatus($conOtherCode);
//        }else{
//            $parcelFilter = new ParcelFilter();
//            $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
//            $parcelFilter->addGroupBy("parcel_status_code");
//            $parcelResObj = $parcelFilter->getColumnList("parcel_status_code");
//            if (count($parcelResObj) == 1) {
//                $consignmentObj->setShipmentStatus($parcelResObj[0]->getParcelStatusCode());
//                if(isset(Consignment::$database_status_array[$parcelResObj[0]->getParcelStatusCode()])){
//                    $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[$parcelResObj[0]->getParcelStatusCode()]);
//                }
//            } else if(count($parcelResObj) > 0) {
//                $parcelStauts = [];
//                foreach ($parcelResObj as $parcelResArr) {
//                    $parcelStauts[]= $parcelResArr->getParcelStatusCode();
//                }
//                //Handle partial Data
//                if(in_array_r(Consignment::STATUS_DELIVERED, $parcelStauts)){
//                   $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_DELIVERED);
//                   if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DELIVERED])){
//                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DELIVERED]);
//                    }
//                }
//                //Departured status remaining
////                else if(in_array_r(Consignment::status_, $parcelStauts)){
////
////                }
//                else if(in_array_r(Consignment::STATUS_RECEIVED, $parcelStauts)){
//                    $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_RECEIVED);
//                    if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED])){
//                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED]);
//                    }
//                }
//                else{
//                    $consignmentObj->setShipmentStatus(Consignment::STATUS_RECEIVED);
//                    if(isset(Consignment::$database_status_array[Consignment::STATUS_RECEIVED])){
//                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
//                    }
//                }
//            }
//        }
//        $consignmentObj->save();
//    }
//}

/*
 * Scenario 101
 * Get: Bag id
 * Return: success message in json array
 */

function checkPreSortBag($bagId) {
    if ($bagId > 0) {
        $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
        $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
        $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id");
        $parcelRoutingCode = [];
        if (count($parcelBaggingMappingObj) > 0) {
            foreach ($parcelBaggingMappingObj as $parcelBagging) {
                $parcelId = $parcelBagging->getParcelId();
                $parcelobj = new Parcel($parcelId);
                $parcelRoutingCode[] = $parcelobj->getRoutingCode();
            }
            $uniqueParcelRoutingCode = array_unique($parcelRoutingCode);
            if (count($uniqueParcelRoutingCode) == 1) {
                $message = "Bag is sorted added to pallet.";
                $arr = array('pre_sort' => 'ok', 'result' => 'success', 'message' => $message);
                return ($arr);
            } else {
                $message = "This bag is not pre sorted bag. To genreate simple label please uncheck Pre Sort Bag Outer Label option";
                $arr = array('pre_sort' => 'no', 'result' => 'error', 'message' => $message);
                return ($arr);
            }
        }
    } else {
        $message = "Bag number is not valid";
        $arr = array('pre_sort' => 'no', 'result' => 'error', 'message' => $message);
        return ($arr);
    }
}

/*
 * Scenario 101
 * Get: box number,Pallet Number
 * Return: success message in json array
 */
if (isset($_POST["action"]) && $_POST["action"] == 'remove_pallet') {
    $user = SessionManager::getUser();
    $boxNumber = trim(@$_POST["boxnumber"]);
    $palletNo = trim(@$_POST['palletno']);
    $preSortChk = trim(@$_POST['pre_sort']);
    $carrierId = trim(@$_POST['carrier_id']);
    $carrierHubId = trim(@$_POST['carrier_hub_id']);
    if ($carrierHubId > 0)
        $carrierHubId = trim(@$_POST['carrier_hub_id']);
    else
        $carrierHubId = 0;



    $countryId = $user->getCountryId();
        $country = new Country($countryId);
        $trackpoint = '';
        $countryIso3 = '';
        $warehouseName = '';
        $carrierDesc = '';
        if (count($country) > 0)
            $countryIso3 = $country->getIso3();

        $warehouseid = $user->getWarehouseId();
        if ($warehouseid > 0) {
            $warehouseObj = new Warehouse($warehouseid);
            $warehouseName = $warehouseObj->getWarehouseName();
        }
        $trackpoint = $warehouseName . " - " . $countryIso3;
        if (!empty($warehouseName))
            $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;



    $mawbNumber = trim(@$_POST['mawbnumber']);
    $mawbId = getMawbIdFromMawbNumber($mawbNumber);
    $labelData = [];
    $bagId = 0;
    //Get pallet id from pallet number
    $palletId = getPalletId($palletNo);
    if (is_numeric($palletId)) {

    } else {
        echo $palletId;
        die;
    }
//    Get bag id from bag nummber
    $bagId = getBagId($boxNumber);
    if (is_numeric($bagId)) {

    } else {
        echo $bagId;
        die;
    }
    if(!empty($mawbNumber)){
        // Check if bag's all parcel is added into same MAWB number
        $errorReturn = checkAllParcelSameMawb($bagId, $mawbId, $warehouseid,$boxNumber);
        if(!empty($errorReturn)){
            foreach ($errorReturn as $errorRtn) {
             $message .= $errorRtn."<br />";
            }
            $arr = array('result' => 'error', 'message' => $message);
            echo json_encode($arr);
            return;
            die;
        }
    }
    $parcelArr = getParcelIdFromBagId($bagId);
//  Remove from pallet_entity_mapping table then add new record
    $palletEntityMapping = new PalletEntityMapping();
    $palletEntityMapping->DeleteAllPalletFromPallet($bagId, 'b');
    $palletEntityMapping->setPalletId($palletId);
    $palletEntityMapping->setEntityId($bagId);
    $palletEntityMapping->setPalletEntityType("b");
    $palletEntityMapping->setPreSort($preSortChk);
    $palletEntityMapping->Save();
////  Remove  mawb_parcel_mapping table then add new record
//    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
//    $mawbParcelMappingFilter->deleteAllBagsNParcel($bagId,$mawbNumber);

    // Remove data from tracking data table
    $trackingDataFilter = new TrackingDataFilter();
    $trackingDataFilter->deleteFromTrackingDataIn($parcelArr);

    if(count($parcelArr) > 0){
        foreach ($parcelArr as $parcelId) {
            $parcel = new Parcel($parcelId);
            $trackingNo = $parcel->getTrackingNumber();
            if(!empty($trackingNo)){
                //Change status (New)
                Parcel::setParcelStatus($parcelId, Consignment::STATUS_RECEIVED);
                changeConStatusByParcelStatus($parcelId);

                $trackingData['entity_id'] = $parcelId;
                $trackingData['entity_type'] = "parcel";
                $trackingData['tracking_number'] = $parcel->getTrackingNumber();
                $trackingData['user_id'] = $user->getId();
                $trackingData['track_point'] = $trackpoint;
                $trackingData['date_created'] = date("Y-m-d H:i:s");
                $trackingData['ip_address'] = getClientIp();
                $trackingData['status_code_id'] = 146;
                $trackingData['carrier_desc'] = $carrierDesc;
                $trackingData['warehouse_id'] = $user->getWarehouseId();
                $trackingDataFinal[] = $trackingData;
            }
        }
        //add data to tracking_data table
        saveDataToTrackingData($trackingDataFinal, false);
    }
    if (ParcelBaggingMappingFilter::checkPreSortBag($bagId, $carrierHubId)) {
        $carrierId = getCarrierIdFromCarrierGroup($carrierId);
        $serviceCodeObj = ServiceFilter::getPreSortServiceByCarrierId($carrierId, "code");
        $serviceCode = $serviceCodeObj[0]->getCode();
        $labelData = createPreSortLabelCheck($boxNumber, $carrierHubId, $serviceCode);
        if (!empty($labelData['error'])) {
            $arr = array('result' => 'error', 'message' => $labelData['message']);
            echo json_encode($arr);
            die;
        } else {
            $bagObj = new Bagging($bagId);
            $bagObj->setBagLabel($labelData['label']);
            $bagObj->save();
        }
    } else {
        $totalTrackingNumbers = getTotalTrackingNumbersScanned($boxNumber);
        $carrierId = getCarrierIdFromCarrierGroup($carrierId);
        $serviceCodeObj = ServiceFilter::getPreSortServiceByCarrierId($carrierId, "code",true);
        $serviceCode = $serviceCodeObj[0]->getCode();
        $boxLabelObj = new BoxLabel();
        $labelData['label'] = $boxLabelObj->buildPDFDocuments($boxNumber, $totalTrackingNumbers, $serviceCode, $mawbNumber);
    }
    $message = "pallet saved successfully";
    $arr = array('result' => 'success', 'message' => $message, 'link' => $labelData['label']);
    echo json_encode($arr);
    die;
}
/*
 * Get: Pallet Number
 * Return: Pallet id
 */

function getPalletId($palletNo) {
    $palletId = 0;
    if (!empty($palletNo)) {
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($palletNo);
        $palletObj = $palletFilter->getColumnList('id');
        if (count($palletObj) > 0) {
            $palletId = $palletObj[0]->getId();
        } else {
            $message = "Please enter valid pallet Number";
            $arr = array('result' => 'error', 'message' => $message);
            return json_encode($arr);
        }
    } else {
        $message = "Please enter pallet Number";
        $arr = array('result' => 'error', 'message' => $message);
        return json_encode($arr);
    }
    return $palletId;
}

/*
 * Get: Pallet id
 * Return: Pallet Number
 */

function getBagId($bagNo) {
    $bagId = 0;
    if (!empty($bagNo)) {
        $baggingFilter = new BaggingFilter();
        $baggingFilter->addFieldEqualFilter("    bagnumber", "=", $bagNo);
        $bagNumberList = $baggingFilter->getColumnList("id");
        if (count($bagNumberList) > 0) {
            $bagId = $bagNumberList[0]->getId();
        } else {
            $message = "Please enter valid bag number";
            $arr = array('result' => 'error', 'message' => $message);
            return json_encode($arr);
        }
    } else {
        $message = "Please enter bag number";
        $arr = array('result' => 'error', 'message' => $message);
        return json_encode($arr);
    }
    return $bagId;
}

/*
 * Scenario 101
 * Get: pallet number
 * Return: bag data html
 */
if (isset($_POST["action"]) && $_POST["action"] == 'get_bag_detail') {
    $chkSession = true;
    $user = SessionManager::getUser();
    if ($user->getId() <= 0) {
        $chkSession = false;
    }
    if ($chkSession) {
        $palletNumber = trim($_POST['pallet_number']);
        $returnHtml = "";
        //Get pallet id from pallet number
        $palletId = getPalletId($palletNumber);
        if (!empty($palletId)) {
//    if(is_numeric($palletId) && checkPalletDispatch($palletId)){
            $returnHtml .= '<div class="modal-header" id="osx-modal-title"> <h4>Pallet ' . $palletNumber . ' Detail</h4> </div>
                                <div class="modal-body">
                                    <div class="row" id="show_pallet_msg" style="display:none;">
                                        <div class="col-md-12">
                                                <div class="alert alert-danger"></div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th> Action </th>
                                                    <th> Bag Id </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
            $palletEntityMappingFilter = new PalletEntityMappingFilter();
            $palletEntityMappingFilter->addFieldEqualFilter('pallet_id', '=', $palletId);
            $palletEntityObj = $palletEntityMappingFilter->getList();
            if (count($palletEntityObj) > 0) {
                foreach ($palletEntityObj as $palletEntity) {
                    $bagObj = New Bagging($palletEntity->getEntityId());
                    $returnHtml .= '<tr>
                                    <td> <span style="cursor: pointer;" class="remove_bag label label-sm label-danger" data-entity_mapping_id = "' . $palletEntity->getId() . '" data-pallet_id = "' . $palletEntity->getPalletId() . '" data-bag_id = "' . $palletEntity->getEntityId() . '" > Remove </span> </td>
                                    <td> ' . $bagObj->getBagnumber() . ' </td>
                                </tr>';
                }
            } else {
                $returnHtml .= '<tr>
                                <td colspan="2"> No data found! </td>
                            </tr>';
            }
            $returnHtml .= '            </tbody>
                                </table>
                            </div>
                        </div>';
        } else {
            $returnHtml .= '<tr>
                                <td colspan="2"> No data found! </td>
                            </tr>';
        }
    } else {
        $returnHtml .= '<tr>
                            <td colspan="2"> '.Translation::GetCaption("SESSION_EXPIRED").' </td>
                        </tr>';
    }
    echo $returnHtml;
    die;
}
/*
 * Scenario 101
 * Get: pallet id
 * Return: true false
 */

function checkPalletDispatch($palletId) {
    $returndata = FALSE;
    if ($palletId > 0) {
        $palletObj = new Pallet($palletId);
        if ($palletObj->getDateDispatch() != "" && $palletObj->getDispatchUserid() > 0) {
            $returndata = true;
        }
    }
    return $returndata;
}

/*
 * Scenario 101
 * Get: pallet id, bag id
 * Return: json response array
 */
//if (isset($_POST["action"]) && $_POST["action"] == 'remove_bag_entity') {
//    $entityMappingId = trim($_POST['entity_mapping_id']);
//    //  Remove from pallet_entity_mapping table then add new record
//    $palletEntityMapping = new PalletEntityMapping();
//    $palletEntityMapping->DeleteByIdFromPallet($entityMappingId);
//    $message = "Bag removed successfully";
//    $arr = array('result' => 'success', 'message' => $message);
//    echo json_encode($arr);
//    die;
//}

////////////////////////////////////////// CHECK IF CONSIGNMENT EXIST IN OUR SYSTEM ///////////////////////////////////////
/*
 * Scenario 201
 * Get: tacking number,box number, scanned weight, length, width, height
 * Return: true/false
 */
function ScanIndividualItems($trackingNumber, $boxNumber = '', $scanned_weight = '', $length = '', $width = '', $height = '') {

    $user = SessionManager::getUser();

    $warehouseid = $user->getWarehouseId();

    //echo "warehouse id " . $warehouseid;
//    $hub = $user->getHub();
    //Country Id set by Hadi 14-03-18
    $countryId = $user->getCountryId();
    $country = new Country($countryId);
    if (count($country) > 0)
        $trackpoint = $country->getName();

    $account = $user->getAccount();
    $boxNumber = trim($boxNumber);

    $found = false;
//    $user = SessionManager::getUser();//Its duplicate removing
    $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
    $parcelFilter = new ParcelFilter();
    $parcelFilter->addLicensePlateFilter($trackingNumber);
    $parcel_list = $parcelFilter->getList();

    if (count($parcel_list) > 0) {
        $consignmentFilter = new ConsignmentFilter();

        $parcel = $parcel_list[0];
        $account = $user->getAccount();
        $conId = $parcel->getConsignmentId();
        $consignmentFilter->addIdFilter($conId);
        $consignmentFilter->addFieldNotFilter("is_invoiced", "1");
        $con_list = $consignmentFilter->getColumnList("awb, weight, consignment_status,update_weight, vol_weight, vol_demonimator");
        if (count($con_list) > 0) {
            $consignment = $con_list[0];
            $weight = $consignment->getWeight();
            $update_weight = $consignment->getUpdateWeight();
            $vol_denominator = $consignment->getVolDemonimator();
            if ($consignment->getAwb() != '') {
                $filter = new TrackingDataFilter();
                $filter->TrackPointExist("'" . $consignment->getAwb() . "'", "Received", $trackpoint, trim($hub));
                $list = $filter->getColumnList("id");
                if (count($list) == 0)
                /*
                 * Scenario 201
                 * Get : Consignment AWB, User Account, "status=>Received"
                 * Return : This function return data but we didn't do any thing with that
                 */
                    TrackingData::AddVirtualTrackingToScanParcels(array($consignment->getAwb()), $user, "Received");
            }
            if ($vol_denominator == 0)
                $vol_denominator = 5000;
        }
        if ($length > 0 || $width > 0 || $height > 0) {
            $olddimesions = $parcel->getLength() . "X" . $parcel->getWidth() . "X" . $parcel->getHeight();
            $newdimesions = $length . "X" . $width . "X" . $height;
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Manual Scan updated dimension " . $olddimesions . " to " . $newdimesions . " ", $parcel->getConsignmentId());
            $parcel->setLength($length);
            $parcel->setWidth($width);
            $parcel->setHeight($height);
            //Scenario 202:
            if ($warehouseid == 9 || $warehouseid == 10) {
//                $parcel->setDateScanned(date("Y-m-d G:i"));//Caused issue ( This feild doesnot exsist)
            }
            $parcel->save();
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addConsignmentIdFilter($conId);
            $parcel_list = $parcelFilter->getList();
            $consignment = new Consignment();
            $con_vol_weight = $consignment->GetVolWeightOfConsignment($conId, $vol_denominator);
            $vol_weight = number_format($con_vol_weight[0]['vol_weight'], 3);
            //Scenario 202:
//            $consignment->setVolWeight($vol_weight); It's not working as its returned as empty
//            $consignment->save();
        }
        if ($scanned_weight > 0) {
            $parcel_weight = $parcel->getWeight();
            $parcel->setWeight($scanned_weight);
            $parcel->setUpdateWeight($parcel_weight);
        }
        $outTariff = Consignment::updateTariffUsingConsignmentId($conId);
        if($outTariff['status'] == 'error')
        {
            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog($outTariff['message'], $conId);
            $found = false;
        }
        else
        {
            $parcel->save();
            if ($scanned_weight > 0) {
                //$consignment->setWeight($scanned_weight);//Scenario 202: error on null
                if ($update_weight == '' || $update_weight == 0)
    //                $consignment->setUpdateWeight($weight);//Scenario 202: error on null
                    $ConsignmentLog = new ConsignmentLog();
                $ConsignmentLog->createlog("Manual Scan update Weight from " .
                        $weight . " to Scanned Weight " . $scanned_weight, $parcel->getConsignmentId());
            }
            if ($warehouseid == 9 || $warehouseid == 10) {
                if (is_object($consignment)) {
                    if ($consignment->getDateScanned() == '0000-00-00 00:00:00' || $consignment->getDateScanned() == '') {
                        $consignment->setDateScanned(date("Y-m-d G:i"));
                        $consignment->setConsignmentStatus(Consignment::STATUS_WAREHOUSE_RECEIVED);
                    }
                    $consignment->setWarehouseId($warehouseid);
                    $consignment->save();
                }
            }
            if ($boxNumber == '') {
                $location = $trackingNumber;
            } else {
                $location = $boxNumber;
            }
            $found = true;
        }
        
        
        
    } else
        $found = false;

    return $found;
}

////////////////////// SENDING DATA TO MOBILE APPLICAITON /////////////////////////////////


function updateDataIntoMobileApplication($url, $postfields) {

    $ch = curl_init();
    $server_output = '';
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //	return 1;
    $server_output = curl_exec($ch);

    curl_close($ch);


    return $server_output;
}

/////////////////////// SAVING GOOGLE AUDIO FILE ///////////////////////////
function SaveAudioFileUsingGoogleTextToSpeech($url, $postfields, $filename) {

    $ch = curl_init();
    $server_output = '';
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //	return 1;
    $server_output = curl_exec($ch);

    curl_close($ch);

    $fp = fopen('../_assets/carton_audio/' . $filename . '.mp3', 'w+');
    fwrite($fp, $server_output);
    fclose($fp);


    return $server_output;
}

//function in_array_r($needle, $haystack, $strict = false) {
//    //print_r($haystack);
//    $found = "notfound";
//
//    foreach ($haystack as $key => $value) {
//        if ($value == $needle)
//            $found = "found";
//    }
//
//
//    return $found;
//}

function CheckUserSession() {
    $user = SessionManager::getUser();
    if ($user->getId() <= 0) {
        $msg = Translation::GetCaption("SESSION_EXPIRED");
        $arr = array('result' => 'error', 'message' => $msg);
        echo json_encode($arr);
        return;
    } else {
        return true;
    }
}

function LoadAgents($serviceid, $agentid) {
    $serviceAgentFilter = new ServiceAgentMappingDataFilter();
    $serviceAgentFilter->addServiceIDFilter($serviceid);
    $agentList = $serviceAgentFilter->getList();

    $html = '<select name="agent" class="form-control" id="agent" width="200px">';

    $html .= '<option value="">Please select agent</option>';
    if (count($agentList) > 0) {
        $selected = '';
        foreach ($agentList as $serviceAgent) {
            $agId = $serviceAgent->getAgentId();
            $agentCDate = new AgentData($agId);
            $code = $agentCDate->getAgentCode();

            if ($agentid == $agId)
                $selected = "selected='selected'";
            else
                $selected = "";
            $html .= '<option value="' . $agId . '"' . $selected . '>' . $code . '</option>';
        }
    }
    $html .= '</select>';
    echo $html;
}

//function HoldShipmentExist($boxNumber) {
//    $holdArray = array();
//    $strTrackingNumbers = '';
//
//    $baggingFilter = new BaggingFilter();
//    $baggingFilter->addFieldEqualFilter("bagnumber", "=", $boxNumber);
//    $bagNumberList = $baggingFilter->getColumnList("id");
//    if (count($bagNumberList) > 0) {
//        $bagNumberObj = $bagNumberList[0];
//        $bagId = $bagNumberObj->getId();
//
//        $consignmentBaggingMapFilter = new ConsignmentBaggingMappingFilter();
//        $consignmentBaggingMapFilter->addFieldEqualFilter("bagid", "=", $bagId);
//        $consignmentBaggingMapFilterList = $consignmentBaggingMapFilter->getList();
//        foreach ($consignmentBaggingMapFilterList as $conBagId) {
//            $consignment = new Consignment($conBagId->getConsignmentId());
//            if ($consignment->getShipmentStatus() == Consignment::STATUS_HOLD)
//                $list[] = $consignment;
//        }
//
//        if (count($list) > 0) {
//            foreach ($list as $consignment) {
//                $holdArray[] = $consignment->getAwb();
//            }
//        }
//    }
//    return $holdArray;
//}

function IsBagScanned($bag_number) {

    /*
      $result = 'not scanned';
      $resultObj = '';
      if ($bag_number != '') {
      $consignmentFilter = new ConsignmentFilter();
      $consignmentFilter->addFieldFilter($bag_number);
      $list = $consignmentFilter->getColumnList("bag_number");

      if (count($list) > 0) {
      $consignmentFilter = new ConsignmentFilter();
      $resultObj = $consignmentFilter->isBagScanned($bag_number);
      }

      //print_r($result);
      //echo "count " . count($result);

      if (count($resultObj) == 0) {
      $result = 'scanned';
      } else {
      $result = 'not scanned';
      }
      }

      //echo $result;
     *      
      return $result;
     * */
}

function CreateLabel($consignment, $oldTrackingNumber) {

    include_classes([
    'pdfmerger'
    ], 'labels');


    $pdf2 = new PDFMerger();
    $labelFile = new LabelFile();
    $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetX(1.0);
    //
    $first_page = true;
    $page_count = 0;
    $consignment_str = "";
    $account = "";
    $complete = true;

    //$oldTrackingNumber = $consignment->getAwb();




    /* $serviceflr = new ServiceFilter();
      $serviceflr->addSCodeFilter($handling); /// SELECTED SERVICE BY THE USER
      $serviceList = $serviceflr->getColumnList("name, code, carrier, type");
      $service = $serviceList[0];
      $carrier = $service->getCarrier();


      echo $carrier;

      die; */

    //print_r($consignment);
    //	die;


    $userflr = new UserAccountFilter();
    $userflr->addUserAccountFilter($consignment->getAccount());
    $userList = $userflr->getColumnList("user_pass");

    if (count($userList) > 0) {
        $user = $userList[0];
        $pass = $user->getUserPass();
    }

    //$con_validator = new ConsignmentValidator($consignment);
    //echo "Status : " . $con_validator->isValid();
    //if($con_validator->isValid())
    //{
    $mixedCreator['consignment'] = $consignment;
    $mixedCreator['password'] = $pass;
    $mixedCreator['carrier'] = "2";

    $apiValue = new Apis($mixedCreator); //  Calling the constructor
    $results = $apiValue->label();





    $returnconsignmentarray = explode("||", $results);

    //print_r($returnconsignmentarray);





    if ($returnconsignmentarray[0] == "SUCCESS") {

        $labelFile = new LabelFile();
        $consignment_str = $consignment->getHawb();
        $account = $consignment->getAccount();
        $labelFile->setHawbList($consignment_str);
        $labelFile->setAccountNumber($account);
        $labelFile->save();

        $consignment->setAwb($returnconsignmentarray[2]);
        $uklink = $returnconsignmentarray[1];

        /* if(substr($uklink,0,10)!='../_assets')
          {
          if(  strpos(strtolower($uklink), 'http') === false)
          $uklink 	=	'http://'.$uklink ;
          } */


        /* if(substr($uklink,0,10)!='../_assets')
          {
          if(strpos(strtolower($uklink), 'http') === false)
          $uklink 	=	'http://'.$uklink ;
          }

          if(strpos(strtolower($uklink), 'http') === false)
          {

          } */
        //$uklink 	=	'http://'.$uklink ;
        //$uklink;
        $pdfpage = file_get_contents($uklink);

        $fileNameNew = "../_assets/pdf/" . date("Y_m_d") . "/" . $consignment->getId() . ".pdf";
        // echo $fileNameNew;			 
        $fp = fopen($fileNameNew, 'wb+');
        fwrite($fp, $pdfpage);
        fclose($fp);
        // exit;
        $pdf2->addPDF($fileNameNew, 'all');
        // echo $labelFile->getId(); exit;
        //echo $fileNameNew;
        $consignment->setPrintedFileId($labelFile->getId());

        if ($consignment->getDateReceived() == '')
            $consignment->setDateReceived(time());

        //$consignment->setDateBooked(time());
        $consignment->setStatus(Consignment::STATUS_RECEIVED);
        $consignment->setSingleLabel($fileNameNew);
        $consignment->savelog('Single Created/Printed', 'L');
        $consignment->save();

        //$outFile = $labelFile->getFullPath();
        // echo $outFile;
        //$pdf->Output($fileNameNew, "F");
        // exit;
        try {
            $pdf2->merge('file', $fileNameNew);
            return "SUCCESS";
            /* echo '<script language="javascript">';
              echo "window.open('". $fileNameNew."','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');" ;
              echo '</script>'; */
        } catch (Exception $e) {
            $results = $e->getMessage();
            $consignment->setStatus(Consignment::STATUS_RELABEL);
            $consignment->setAwb($oldTrackingNumber);
            $consignment->save();
            return "ERROR";
        }
    }
    if ($returnconsignmentarray[0] == "ERROR") {
        $consignment->setStatus(Consignment::STATUS_RELABEL);
        $consignment->setMessage($returnconsignmentarray[1]);
        $consignment->setAwb($oldTrackingNumber);
        /* echo "<script language='javascript'> alert('". $returnconsignmentarray[1]."'); </script>" ; */
        $consignment->savelog('Single Failed At API Stage: ' . $returnconsignmentarray[1], 'A');
        $consignment->save();
        //  echo $returnconsignmentarray[1]; exit;
        return "ERROR";
    }
    //}
    //else
    /* {
      $consignment->setStatus(Consignment::STATUS_RELABEL);
      $consignment->setAwb($oldTrackingNumber);
      //$consignment->setMessage($returnconsignmentarray[1]);
      /*echo "<script language='javascript'> alert('". $returnconsignmentarray[1]."'); </script>" ;
      //$consignment->savelog('Single Failed At API Stage: ' .$returnconsignmentarray[1], 'A');
      // $consignment->save();
      //  echo $returnconsignmentarray[1]; exit;
      // return "ERROR";
      } */
}

function GetShelfList($rack_id) {

    $rackShelfObj = new RackShelfFilter();
    $rackShelfObj->addFilter("is_filled = 0 and rs.rack_id = '" . $rack_id . "'");
    $rackshelfs = $rackShelfObj->getList();

    //echo "<pre>";
    //print_r($rackshelfs);
    //$rackShelfArr = array();

    $rack = new Rack($rack_id);
    $title = $rack->getShortTitle();

    $html = '';


    $html .= '<select id="rackShelf" name="rackShelf" class="form-control"		
				 style="width:800px;height:75px;font-size:50px; background-color:#FFC;">';

    foreach ($rackshelfs as $rackshelf) {
        $shelf_id = $rackshelf->getId();
        $shelf_no = $rackshelf->getShelfNo();

        $html .= "<option value=" . $shelf_id . ">" . $title . " " . ($shelf_no + 1) . "</option>";
    }

    $html .= '</select>';

    return $html;
}

if ($_POST['action'] == 'GetShelfNoByRackId') {
    $rack_id = $_POST['rack_id'];

    //echo $rack_id;
    //die;

    if ($rack_id > 0) {

        $ddlshelflist = GetShelfList($rack_id);

        $arr = array('result' => 'success', 'ddlshelflist' => $ddlshelflist);
    } else {
        $arr = array('result' => 'error', 'message' => 'list empty', 'ddlshelflist' => $ddlshelflist);
    }

    echo json_encode($arr);
}

if ($_POST['action'] == 'RemoveItemsFromStock') {

    require_once("../includes/mapping/returnpalletlabel.class.php");
    require_once("../includes/mapping/rackshelfitemfilter.class.php");

    

    $sessionUser = SessionManager::getUser();

    $shipperUserId = 0;


    $json_trackingnumbers = @$_POST["trackingnumbers"];
    $status = $_POST['status'];
    $warehouseid = $_POST['warehouseid'];

    $foundNumbers = array();

    //echo "warehoue id " . $warehouseid;

    $hub = '';

    if ($warehouseid > 0) { /////////////// Warehouse provided from the dropdown
        $warehouse = new warehouse($warehouseid);
        $MovingTohub = $warehouse->getHub();

        if ($status == 'Return To Warehouse') {
            $status = "Return To " . $MovingTohub;
        }
    }

    $warehouseid = $sessionUser->getWarehouseId();
    $country_iso = $sessionUser->getCountryId();

    $countryFilter = new CountryFilter();
    $countryFilter->addIsoFilter($country_iso);
    $country_list = $countryFilter->getColumnList("name");

    if (count($country_list) > 0)
        $trackpoint = $country_list[0]->getName();
    else
        $trackpoint = '';

    $warehouse = new Warehouse($warehouseid);
    $hub = $warehouse->getHub();

    $notFoundArray = array();

    //echo $json_trackingnumbers;

    $list_trackingnumbers = json_decode(stripslashes($json_trackingnumbers));


    foreach ($list_trackingnumbers as $trackingNumber) {
        $arr_trackingnumbers[] = ParseTrackingNumber::Parse($trackingNumber);
    }


    //$arr_trackingnumbers = $json_trackingnumbers;		

    if (count($arr_trackingnumbers) > 0) {


        $str_trackingnumbers = "'" . implode("','", $arr_trackingnumbers) . "'";
        $consignment_filter = new ConsignmentFilter();
        $consignment_filter->addawbFilterList($str_trackingnumbers);
        $consignment_filter->addReturnHandling();
        $foundList = $consignment_filter->getColumnList("awb");



        foreach ($foundList as $con) {
            $foundNumbers[] = $con->getAwb();
        }

        //print_r($foundNumbers);

        $notFoundArray = array_values(array_diff($arr_trackingnumbers, $foundNumbers));


        if (count($foundNumbers) > 0) {


            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addawbFilterList("'" . $foundNumbers[0] . "'");
            $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'recycled');
            $con_list = $consignmentFilter->getColumnList("account");

            if (count($con_list) > 0) {
                $con = $con_list[0];
                $userFilter = new UserAccountFilter();
                $userFilter->addUserNameFilter($con->getAccount());
                $userList = $userFilter->getColumnList("id");

                if (count($userList) > 0) {
                    $userObj = $userList[0];
                    $shipperUserId = $userObj->getId();
                }
            }

            foreach ($arr_trackingnumbers as $trackingnumber) {


                if (trim($trackingnumber) != '') {
                    //echo $trackingnumber;
                    $rackShelfItemFilter = new RackShelfItemFilter();
                    $rackShelfItemFilter->addTrackingNumberFilter($trackingnumber);
                    $rackShelfItemList = $rackShelfItemFilter->getList();
                    //echo "rack kazim";					
                    //print_r($rackShelfItemList);

                    if (count($rackShelfItemList) > 0) {


                        $rackShelfItem = $rackShelfItemList[0];

                        //print_r($rackShelfItem);

                        if ($rackShelfItem->getId() > 0) {
                            //echo "Rack Shelf Item Id " . $rackShelfItem->getId();

                            $logRackShelfFilter = new LogRackShelfFilter();
                            $logRackShelfFilter->addRackShelfItemId($rackShelfItem->getId());
                            //print_r($logRackShelfFilter);
                            $currentItemList = $logRackShelfFilter->getList();
                            if (count($currentItemList) > 0) {
                                $currentItem = $currentItemList[0];
                                //print_r($currentItem);
                                $currentItem->setOutDate(date("Y-m-d H:i:s"));
                                $currentItem->setOutBy($sessionUser->getId());
                                $currentItem->save(true);

                                $rackShelf = new RackShelf($currentItem->getRackShelfId());
                                $rackShelf->setIsFilled("0");
                                //$rackShelf->setUpdatedDate(date("Y-m-d G:i"));
                                //$rackShelf->setUpdatedBy($sessionUser->getId());							
                                $rackShelf->save(true);
                            }
                        }
                    }
                }
            }

            //print_r($arr_trackingnumbers);
            //die;

            $pallet = new Pallet();
            $pallet->setDateCreated(time());
            $pallet->setUserId($sessionUser->getId());
            $pallet->setClose("Y");
            $pallet->setCarrier("RETURNS");
            $pallet->setType("P");
            $pallet->setHub($warehouseid);
            $pallet->setIsActive("Y");
            $pallet->save();

            $palletno = "OW" . $type . "RTN" . $pallet->getId() . strtoupper(substr($hub, 0, 3));
            $pallet->setPalletno($palletno);
            $pallet->save();

            //echo $pallet->getId() . $palletno;
            //echo $pallet_label_link;
            //die;


            TrackingData::AddVirtualTrackingToScanParcels($arr_trackingnumbers, $sessionUser, $status, '', $hub, $trackpoint);

            $manifestid = TrackingData::SendEmail($arr_trackingnumbers, $sessionUser, "", "Return", "", "");

            $manifest = new Manifest($manifestid);

            $pallet_label_link = ReturnPalletLabel::buildPDFDocuments($pallet->getId(), $palletno, count($arr_trackingnumbers), $manifestid, $shipperUserId);

            $manifest->setLabelLink($pallet_label_link);
            $manifest->save();

            $pallet->setManifestId($manifestid);
            $pallet->save();

            $pdf = str_replace("../", BASE_URL, $manifest->getPdfFile());
            $csv = str_replace("../", BASE_URL, $manifest->getFileName());


            $arr = array('result' => 'success',
                'label' => $pallet_label_link,
                'not_found' => $notFoundArray,
                'manifestid' => $manifestid,
                'pdf' => $pdf,
                'csv' => $csv
            );
        } else {
            $arr = array('result' => 'error', 'not_found' => $notFoundArray);
        }
    } else {
        $arr = array('result' => 'error', 'message' => 'item not found');
    }

    echo json_encode($arr);
}
/*
 * Scenario 401
 * Get: Manifest Number
 * Return:  Json encoded result array
 */
if ($_POST['action'] == 'scan_manifest') {
    $chkSession = CheckUserSession();
    if ($chkSession) {
        $arr = [];
        $sessionUser = SessionManager::getUser();
        $countryId = $sessionUser->getCountryId();
        $countryObj = new Country($countryId);
        $message = "";
        $totalWeight = 0;
        $country = new Country($countryId);
        $trackpoint = '';
        $countryIso3 = '';
        $warehouseName = '';
        $carrierDesc = '';
        if (count($country) > 0)
            $countryIso3 = $country->getIso3();

        $warehouseid = $sessionUser->getWarehouseId();
        if ($warehouseid > 0) {
            $warehouseObj = new Warehouse($warehouseid);
            $warehouseName = $warehouseObj->getWarehouseName();
        }
        $trackpoint = $warehouseName . " - " . $countryIso3;
        if (!empty($warehouseName))
            $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;

        $trackingData = [];
//        if (count($countryObj) == 0) {
//            $msg = Translation::GetCaption("SESSION_EXPIRED");
//            $arr = array('result' => 'error', 'message' => $msg);
//            echo json_encode($arr);
//            return;
//        }
        // New Logic
        $manifestId = trim($_POST['manifestnumber']);
        $status = "Received";
        //This logic is changed because user type warehouse is removed
//    if ($sessionUser->getUserType() == 'warehouse') {
//        $user_scanning_status = Consignment::STATUS_WAREHOUSE_RECEIVED;
//    } else
//        if ($trackpoint == 'Poland') {
//            $user_scanning_status = strtolower($trackpoint) . " received";
//        } else {
//            $user_scanning_status = Consignment::STATUS_DATAREADY_SUPPLIER;
//        }
        $trackingNumberArr = array();
        $trackingDataFinal = [];
        if ($manifestId > 0) {
            $manifest = new Manifest($manifestId);
            if ($manifest->getId() > 0) {
                $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                $manifestEntityMappingFilter->addJoin("    parcel p ", " mem.id "," p.id"," JOIN ");
                $manifestEntityMappingFilter->addJoin("    consignment c ", " p.consignment_id "," c.id"," JOIN ");
                $manifestEntityMappingFilter->addFieldFilter("    c.consignment_type", "outbound");
                $manifestEntityMappingFilter->addFieldFilter("    mem.manifest_id", $manifest->getId());
                $manifestEntityMappingFilter->addFieldFilter("    mem.manifest_entity_type", "p");
                $manifestEntityMappingObj = $manifestEntityMappingFilter->getList();
                if (count($manifestEntityMappingObj) > 0) {
                    foreach ($manifestEntityMappingObj as $manifestEntityMapping) {
                        $parcel = new Parcel($manifestEntityMapping->getEntityId());
                        $parcelId = $parcel->getId();
//                        changeConStatusByParcelStatus($parcelId);
                        if ( alreadyScannedParcel($parcel->getTrackingNumber(), $warehouseid) ) {
//                        if ($parcel->getParcelStatusCode() == Consignment::STATUS_RECEIVED) {
                            $message .= "<span class='font-red-mint'>Parcel ['" . $parcel->getTrackingNumber() . "'] is already scanned </span><br>";
                        }else if(Consignment::checkConsignmentInvoiced($parcel->getConsignmentId())){
                             $message .= "<span class='font-red-mint'>Parcel ['" . $parcel->getTrackingNumber() . "'] is already invoiced </span><br>";
                        } else if($parcel->getParcelStatusCode() == Consignment::STATUS_RETURNED){
                            $message .= "<span class='font-red-mint'>Parcel ['" . $parcel->getTrackingNumber() . "'] is already returned </span><br>";
                        } else{
                            if(!empty($parcel->getTrackingNumber())){
                                //Change status (New)
                                Parcel::setParcelStatus($parcelId, Consignment::STATUS_RECEIVED);
                                changeConStatusByParcelStatus($parcelId);
                                $trackingData['entity_id'] = $parcel->getId();
                                $trackingData['entity_type'] = 'parcel';
                                $trackingData['tracking_number'] = $parcel->getTrackingNumber();
                                $trackingData['user_id'] = $sessionUser->getId();
                                $trackingData['track_point'] = $trackpoint;
                                $trackingData['date_created'] = date("Y-m-d H:i:s");
                                $trackingData['ip_address'] = getClientIp();
                                $trackingData['status_code_id'] = 146;
                                $trackingData['carrier_desc'] = $carrierDesc;
                                $trackingData['warehouse_id'] = $sessionUser->getWarehouseId();
                                $totalWeight += $parcel->getWeight();
                                $trackingNumberArr[] = $parcel->getTrackingNumber();
                                $trackingDataFinal[] = $trackingData;
                            }
                        }
                    }
                    //add data to tracking_data table
                    saveDataToTrackingData($trackingDataFinal,false);

                    $message .= $manifestId . " manifest number scanned.";
                    $arr = array("result" => "success",
                        "message" => $message,
                        "trackingnumbers" => $trackingNumberArr,
                        "total_shipments" => count($trackingNumberArr),
                        "total_weight" => $totalWeight
                    );
                } else {
                    $arr = array("result" => "error", "message" => "Manifest number does not have parcels", "trackingnumbers" => $trackingNumberArr);
                }
            } else {
                $arr = array("result" => "error", "message" => "Manifest number does not exist", "trackingnumbers" => $trackingNumberArr);
            }
        } else {
            $arr = array("result" => "error", "message" => "Manifest number does not exist", "trackingnumbers" => $trackingNumberArr);
        }
        echo json_encode($arr);
    } else {
        echo $chkSession;
    }
    die;

    //Old Logic
//    $manifestId = $_POST['manifestnumber'];
//    $status = "Received";
    //This logic is changed because user type warehouse is removed
//    if ($sessionUser->getUserType() == 'warehouse') {
//        $user_scanning_status = Consignment::STATUS_WAREHOUSE_RECEIVED;
//    } else
//        if ($trackpoint == 'Poland') {
//        $user_scanning_status = strtolower($trackpoint) . " received";
//    } else {
//        $user_scanning_status = Consignment::STATUS_DATAREADY_SUPPLIER;
//    }
//    $trackingNumberArr = array();
//    if ($manifestId > 0) {
//        $manifest = new Manifest($manifestId);
//        if ($manifest->getId() > 0) {
//            $pickupId = $manifest->getPickupId();
//            $manifest_filter = new ManifestConsignmentDataFilter();
//            $manifest_filter->addManifestIDFilter($manifestId);
//            $manifest_list = $manifest_filter->getColumnList("consignmentid");
//            $con_id_array = array();
//            $trackingNumberArr = array();
//            $totalWeight = 0;
//            if (count($manifest_list) > 0) {
//                foreach ($manifest_list as $manifest) {
//                    $con_id_array[] = $manifest->getConsignmentId();
//                }
//                $con_filter = new ConsignmentFilter();
//                $con_filter->addIdArrayFilter($con_id_array);
//                $con_list = $con_filter->getColumnList("awb, weight", count($con_id_array));
//                foreach ($con_list as $con) {
//                    $trackingNumberArr[] = $con->getAwb();
//                    $totalWeight += $con->getWeight();
//                }
//                TrackingData::AddVirtualTrackingToScanParcels($trackingNumberArr, $sessionUser, $status, '', $hub, $trackpoint);
//                $str_trackingnumbers = "'" . implode("','", $trackingNumberArr) . "'";
//                $sql_update_columns = "consignment_status = case when consignment_status not in('recycled', 'invalid', 'hold') then '$user_scanning_status' else consignment_status end";
//                $sql_where_clause = " awb != '' and awb in($str_trackingnumbers) ";
//                Consignment::bulkUpdate($sql_update_columns, $sql_where_clause);
//                if ($pickupId != '') {
//                    /*                     * ******* We are calling the Hub Scan API of OPS System if Pickup Id is not empty *////////////////
//                    SendDataToOPS($manifestId, $trackingNumberArr);
//                }
//                TrackingData::SendDataToCourier($trackingNumberArr);
//            }
//        }
//        $arr = array("result" => "success",
//            "message" => $manifestId . " manifest number scanned.",
//            "trackingnumbers" => $trackingNumberArr,
//            "total_shipments" => count($trackingNumberArr),
//            "total_weight" => $totalWeight
//        );
//    } else {
//        $arr = array("result" => "error", "message" => "Manifest number does not exist", "trackingnumbers" => $trackingNumberArr);
//    }
//    echo json_encode($arr);





}
/*
 * Scenario 401
 * Get: Manifest Number
 * Return:  Json encoded result array
 */
//if ($_POST['action'] == 'get_manifest_data') {
//    //Get all consignments that has status no dispatched group by services
////    consignment_status
//    $consignmentFilter = new ConsignmentFilter();
//    $consignmentFilter->addFieldEqualFilter("consignment_status", "=", "received");
//    $consignmentFilter->addGroupBy("c.service_id");
//    $consignmentData = $consignmentFilter->getConList();
//    $html = "";
//    $html .= '<table class="table table-striped table-bordered table-advance table-hover">
//                <thead>
//                    <tr>
//                        <th><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" onclick="checkedAll(delete55);" class="group-checkable" name="check_all_manifest"><span></span></label></th>
//                        <th width="20%">Date Booked</th>
//                        <th>Service</th>
//                        <th>HAWB</th>
//                    </tr>
//                </thead>
//                    <tbody>';
//    foreach ($consignmentData as $consignmentArr) {
//        $service = new Services($consignmentArr->getServiceId());
//        $html .='<tr>
//                      <td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input id="delete55" type="checkbox" name="check_all_manifest" class="group-checkable"><span></span></label></td>
//                      <td>'.$consignmentArr->getDateBooked().'</td>
//                      <td>'.$service->getName().'</td>
//                      <td>'.$consignmentArr->getHawb().'</td>
//                    </tr>';
//    }
//        
//        $html .='</tbody>
//            </table>';
//    echo $html;
//    die;
//}
if ($_POST['action'] == 'PreSortCarrierList') {


    $list = getPreSortCarrier();
    if (count($list) > 0) {
        $dropDown = '<select name="presort_carrier" class="form-control" id="presort_carrier" onchange="loadPreSortHub(this.value);" width="400px">';
        $dropDown .= '<option value="">' . Translation::GetCaption("PLEASE_SELECT_CARRIER") . '</option>';
        foreach ($list as $carrier) {
            $dropDown .= '<option value="' . $carrier->getId() . '"' . $selected . '>';
            $dropDown .= $carrier->getCarrierName();
            $dropDown .= '</option>';
        }
        $dropDown .= '</select>';
    }
    echo $dropDown;
    exit;
}



if ($_POST['action'] == 'YodelHubList') {

    $pre_sort_carrier_id = $_POST['pre_sort_carrier_id'];

    $list = loadYodelHubs($pre_sort_carrier_id);

    if (count($list) > 0) {
        $dropDown = '<div id="divYodelHub">
							<select name="yodel_hub" class="form-control" id="yodel_hub" width="400px">';

        $dropDown .= '<option value="">Please Select Hub</option>';

        //die;

        foreach ($list as $hub) {
            $dropDown .= '<option value="' . $hub->getId() . '"' . $selected . '>';
            $dropDown .= $hub->getHub();
            $dropDown .= '</option>';
        }

        $dropDown .= '</select></div>';
    }

    echo $dropDown;
    exit;
}




if ($_POST['action'] == 'WarehouseList') {
    $list = GetWarehouseList();

    $warehouseArray = array();
    $i = 0;
    foreach ($list as $warehouse) {
        $warehouseArray[$i]['id'] = $warehouse->getId();
        $warehouseArray[$i++]['hub'] = $warehouse->getHub();
    }



    $arr = array("msg" => "success", "warehouse" => $warehouseArray);

    echo json_encode($arr);
}

function GetWarehouseList() {
    $warehouseFilter = new WarehouseFilter();
    $warehouseFilter->addFieldFilter("is_active", "1");
    $warehouseList = $warehouseFilter->getColumnList("id, hub");

    return $warehouseList;
}

/*
 * Scenario 201
 * Get Tracking Number & weight (But in Scenario 201 weight should be zero) , In this scenario 
 * Return : Error Message if any
 */

function CheckAllowedWeight($trackingNumber, $weightEntered) {
    $errorMsg = '';
    //Check if consignment_status is recycled then get awb,account,country,handling,service
    // My segg that we have to check in checkstatus ajax handling 
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->addawbFilterList("'" . $trackingNumber . "'");
    $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'recycled');
    $list = $consignmentFilter->getColumnList("awb, account, country_id, handling, service");
    //If count greater then zero
    if (count($list) > 0) {
        $consignment = $list[0];
        /*
         * Scenario 201
         * Get user_account, country_id,Consignment_service,consignment_handling, weight (But in Scenario 201 weight should be zero)
         * Return : customizedservicesrouting.service_type
         * Purpose :  Check this consigment , for this country this weight is allowed.
         */
        $special_service = Consignment::getSpecialServiceRoutingCode($consignment->getAccount(), $consignment->getCountryId(), $consignment->getService(), $consignment->getHandling(), $weightEntered);
        /*
         * Scenario 201
         * If customizedservicesrouting.service_type is empty then
         * Get user_account, country_id,Consignment_service,consignment_handling, weight (But in Scenario 201 weight should be zero)
         * Return : Error message without any logic
         * purpose : No idea
         */
        if ($special_service == '') {
            $errorMsg = Consignment::getRoutingErrors($consignment->getAccount(), $weightEntered, $consignment->getCountryId(), $consignment->getService(), $consignment->getRoutingCode(), $consignment->getHandling(), '');
        }
    }
    return str_replace(", please the required details", "", $errorMsg);
}

function getPreSortCarrier() {

    $serviceFilter = new ServiceFilter();
    $serviceFilter->addFieldFilter("pre_sort", "YES");
    $list = $serviceFilter->getColumnList("id, carrier_id");
    return $list;
}

function createPreSortLabelCheck($boxNumber, $hubId, $serviceCode) {
    $labelLink = '';
    $totalWeight = 0;
    $totalPieces = 0;
    $sessionUser = SessionManager::getUser();
    $list = getParcelListFromBagNumber($boxNumber);
    $output = "";
    if (count($list) > 0) {
        foreach ($list as $con) {
            $totalWeight += $con->getWeight();
            $totalPieces += $con->getNumberItem();
        }
        $output = CreatePreSortLabel($hubId, $totalPieces, $totalWeight, $boxNumber, $serviceCode);
    }
    return $output;
}

/*
 * Scenario 101
 * Process: Create Per Sorted Label
 * Get: 
 * Return: 
 */

function CreatePreSortLabel($hubId, $totalPieces, $totalWeight, $hawb = '', $serviceCode = '') {
    $sessionUser = SessionManager::getUser();
    $carrierHubs = new CarrierHubs($hubId);
    $company = $carrierHubs->getCompany();
    $contact = $carrierHubs->getContact();
    $addressLine1 = $carrierHubs->getAddressLine1();
    $addressLine2 = $carrierHubs->getAddressLine2();
    $addressLine3 = $carrierHubs->getAddressLine3();
    $city = $carrierHubs->getCity();
    $postCode = $carrierHubs->getPostCode();
    $countryIsoCode = $carrierHubs->getCountryIsoCode();

//    $countryIsoCode = "GB";
    $consignmentarray['hawb'] = $hawb;
    $consignmentarray['company'] = $company;
    $consignmentarray['contact'] = $contact;
    $consignmentarray['address1'] = $addressLine1;
    $consignmentarray['address2'] = $addressLine2;
    $consignmentarray['address3'] = $addressLine3;
    $consignmentarray['city'] = $city;
    $consignmentarray['countrycode'] = $countryIsoCode;
    $consignmentarray['postcode'] = $postCode;
    $consignmentarray['telephone'] = '';
    $consignmentarray['numberpieces'] = '1'; // For bag scan we have to print only one label
    $consignmentarray['weight'] = $totalWeight;
    $consignmentarray['description'] = 'Pre-sort Container';
    $consignmentarray['value'] = '10.00';
    $consignmentarray['currency'] = 'GBP';
    $consignmentarray['sendername'] = '';
    $consignmentarray['reference'] = '';
    $consignmentarray['handlingcode'] = $serviceCode; //Service code
    $consignmentarray['account'] = 'ONEWOR';
    $consignmentarray['username'] = 'oneworld';
    $consignmentarray['password'] = 'Oneworld123@';
    $consignmentarray['both_checked'] = '0';
    $consignmentarray['fullpallet'] = '';
    $consignmentarray['halfpallet'] = '';
    $consignmentarray['quarterpallet'] = '';
    $consignmentarray['documentType'] = 'NONDOC';
    $consignmentarray['notes'] = '';
    $consignmentarray['dimension'] = '';
    $consignmentarray['numberBoxes'] = '';
    $consignmentarray['email'] = 'cs@oneworldexpress.com';
    $consignmentarray['transactionid'] = '';
    $consignmentarray['itemType'] = '';
    $consignmentarray['blank'] = '';
    $consignmentarray['blank2'] = '';
    $consignmentarray['blank3'] = '';
    $consignmentarray['platform'] = '';
    $requestShipmentString = array('consignmentinformation' => implode('||', $consignmentarray));

    $client = new SoapClient(null, array(
        'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
        'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));

    $requestShipmentJson = array('consignmentinformation' => json_encode($consignmentarray), 'dataType' => 'JSON');
    $results = $client->__soapCall('getLabels', $requestShipmentJson);
    $resultArray = json_decode($results, true);
    $output = array();

    if ($resultArray['STATUS'] == 'SUCCESS') {
        $labelLink = $resultArray['LINK'];
        $output['status'] = 'success';
        $output['label'] = $labelLink;
    } else {
        $output['status'] = 'error';
        $output['message'] = $resultArray['MESSAGE'];
    }
    return $output;
}

function loadYodelHubs($pre_sort_carrier_id) {
    $yodelHubFilter = new YodelHubFilter();
    $yodelHubFilter->addFieldFilter("pre_sort_carrier_id", $pre_sort_carrier_id);
    $list = $yodelHubFilter->getList();
    return $list;
}

function cleanData($str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    $str = preg_replace('/[\$,]/', '', $str);
    return $str;
}

function SendDataToOPS($manifestId, $trackingNumbersArr = '') {

    $sessionUser = SessionManager::getUser();
    $warehouseid = $sessionUser->getWarehouseId();

    $manifest = new Manifest($manifestId);
    $account = $manifest->getAccount();

    $warehouse = new Warehouse($warehouseid);
    $hub = $warehouse->getHub();

    /*     * ********************************** TEST ******************************* *////////////////////////


    /*
      $service_url= "http://api-ops-test.azurewebsites.net/api/backend/owe/hub-received";
      $username = "test";
      $password = "testtest";
     */

    /*     * ********************************* LIVE ******************************* *////////////////////////

    $service_url = "https://cloud.open-postal-systems.net/api/backend/owe/hub-received";
    $username = "oweBackend";
    $password = "WMAmc9hoTCdz2jvIoDCp";

    $headers = array(
        "Content-Type: application/json",
        "Authorization : Basic " . base64_encode("$username:$password")
    );

    $shipments = array();



    foreach ($trackingNumbersArr as $trackingno) {
        $shipment = array("trackingNumber" => $trackingno);
        $shipments[] = $shipment;
    }

    $curl_post_data = array(
        "timestamp" => "2017",
        "location" => $hub,
        "manifestId" => $manifestId,
        "shipments" => $shipments
    );

    //print_r($curl_post_data);

    /* $curl_post_data = array(
      "timestamp" => "2017",
      "location" => $hub,
      "manifestId" => $manifestId

      );
     */


    //print_r($curl_post_data);		

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $service_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    curl_setopt($ch, CURLINFO_HEADER_OUT, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $result = curl_exec($ch);

    //echo $result;
}

if ($_POST['action'] == 'get_mawb_detail') {
    $mawbNumberDash = $_POST['mawb_number'];
    $mawbId = getMawbIdFromMawbNumber($mawbNumberDash);
    $preAlertObj = new PreAlertFilter();
    $preAlertObj->addMawbFilter($mawbId);
    $preAlertRes = $preAlertObj->getList();
    if (count($preAlertRes) > 0) {
        $returnHtml = ' <div class="table-scrollable">
                            <table class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th> Flight Number </th>
                                        <th> Pieces </th>
                                        <th> Weight </th>
                                        <th> Estimated Departure Time </th>
                                        <th> Estimated Arrival Time </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> ' . $preAlertRes[0]->getFlightNumber() . ' </td>
                                        <td> ' . $preAlertRes[0]->getPieces() . ' </td>
                                        <td> ' . $preAlertRes[0]->getWeight() . ' </td>
                                        <td> ' . $preAlertRes[0]->getEtd() . ' </td>
                                        <td> ' . $preAlertRes[0]->getEta() . ' </td>
                                        <td>
                                            <span class="label label-sm label-success"> ' . $preAlertRes[0]->getStatus() . ' </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>';
    }
    echo $returnHtml;
}

function getCarrierIdFromCarrierGroup($carrierGroupId) {
    $carrierId = 0;
    if ($carrierGroupId > 0) {
        $palletCarierGroupObj = new PalletCarierGroup($carrierGroupId);
        $carrierId = $palletCarierGroupObj->getCarrierId();
    }
    return $carrierId;
}
function getConsignmentServiceIdFromParcelTracking($trackingNo) {
    $consignmentId = 0;
    $return = 0;
    if(!empty($trackingNo)){
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("tracking_number", $trackingNo);
        $parcelObj = $parcelFilter->getColumnList("consignment_id");
        if(count($parcelObj) > 0){
            $consignmentId = $parcelObj[0]->getConsignmentId();
            if(count($consignmentId) > 0){
                $consignment = new Consignment($consignmentId);
                $return = $consignment->getServiceId();
            }else{
                $return = 0;
            }
        }else{
            $return = 0;
        }
    }else{
        $return = 0;
    }
    return $return;
}
function getServiceFromParcelTracking($trackingArr) {
        $serviceTypeArray = [];
        $returnMsg = "Mixed";
        foreach ($trackingArr as $trackingNumber) {
            $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addLicensePlateFilter($trackingNumber);
            $parcelObj = $parcelFilter->getColumnList("consignment_id");
            $conList = [];
            if (count($parcelObj) > 0) {
                $consignmentId = $parcelObj[0]->getConsignmentId();
                $parcelId = $parcelObj[0]->getId();
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addFieldEqualFilter("id", "=", $consignmentId);
                $consignmentFilter->addFieldNotFilter("awb", "");
                $consignmentFilter->addFieldNotFilter("consignment_status", Consignment::STATUS_RECYCLED);
                $conList = $consignmentFilter->getListNew();
                if (count($conList) > 0) {
                    $con = $conList[0];
                    $serviceTypeArray[] = $con->getServiceId();
                }
            }
        }
        $serviceTypeArray = array_unique($serviceTypeArray);
        if (count($serviceTypeArray) > 1) {
            $returnMsg = "Mixed";
        }else{
            $service = new Services($serviceTypeArray[0]);
            $returnMsg = $service->getName();
        }
        return $returnMsg;
    }
    //Get service according to agent
    if(isset($_POST['action']) && trim($_POST['action']) == "get_service_to_agent"){
        $returnHtml = "<option value=''>Please select</option>";
        if(isset($_POST['agent_id']) && $_POST['agent_id'] > 0){
            $agetnId = trim($_POST['agent_id']);
            $serviceList = ServiceFilter::getServicesToAgent($agetnId);
            if(count($serviceList) > 0){
                foreach ($serviceList as $service) {
                    $returnHtml .= "<option value='".$service->getId()."' data-content='<img src="."/images/carrierlogo/thumbnail/owe_16_" . $service->getCarrierLogo() . "\ />'>".$service->getName(). "</option>";
                }
            }
        }
        echo $returnHtml;
        die;
    }
    function alreadyScannedParcel($trackingNo,$warehouseId,$reScan=""){
        if($reScan == "on"){
            return false;
        }
        $trackingNo = ParseTrackingNumber::Parse($trackingNo);
        $return = false;
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addFieldFilter("    t.tracking_number", $trackingNo);
        $trackingDataFilter->addFieldFilter("    t.warehouse_id", $warehouseId);
        $trackingDataFilter->addFieldFilter("    t.entity_type", "parcel");
        $trackingDataFilter->addFieldFilter("    t.status_code_id", "146");
        $trackingDataObj = $trackingDataFilter->getColumnList('id');
        if(count($trackingDataObj) > 0){
            $return = true;
        }
        return $return;
    }
    function checkAllParcelSameMawb($bagId,$mawb,$warehouseId,$boxNumber= "") {
        $bagParcelListArr = [];
        $mawbParcelIdArr = [];
        $diffParcelIdArr = [];
        $diffMawbParcelList = [];
        $errorArr = [];
        $bagParcelListArr = getParcelListFromBagNumber($boxNumber,true);
        // Get Bag Id
//        getBagId($bagNo);

        // Check if any mabw is already added to same bag
        $mawbParcelMappingFilter = new MawbParcelMappingFilter();
        $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawb);
        $mawbParcelMappingFilter->addFieldFilter("    wharehouse_id", $warehouseId);
        $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
        $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList('parcel_id');
        foreach ($mawbParcelMappingObj as $mawbParcelMapping) {
            $mawbParcelIdArr[] = $mawbParcelMapping->getParcelId();
        }

        $diffParcelIdArr = array_diff($bagParcelListArr, $mawbParcelIdArr);

        $diffCount = count($diffParcelIdArr);
        if(!empty($diffParcelIdArr)){
            // Second : Check if Mawb didn't have parcel
            $mawbParcelMappingFilterNew = new MawbParcelMappingFilter();
            $mawbParcelMappingFilterNew->addJoin("parcel p", "p.id", "mpm.parcel_id","JOIN");
            $mawbParcelMappingFilterNew->addJoin("bagging b", "b.id", "mpm.bag_id","JOIN");
            $mawbParcelMappingFilterNew->addFilterIn("mpm.parcel_id",$diffParcelIdArr);
            $mawbParcelMappingFilterNew->addFieldFilter("    mpm.wharehouse_id", $warehouseId);
            $diffMawbParcelList = $mawbParcelMappingFilterNew->getList();
            $errorCount = 0;
            if(count($diffMawbParcelList) > 0){
                foreach ($diffMawbParcelList as $diffMawbParcel) {
                    $mawb = new Mawb($diffMawbParcel->getMawbId());
                    // Parcel exsist with diff MAWB
                    $errorArr[] = "This ".$diffMawbParcel->getTrackingNumber()." has ".$mawb->getMawbNumber()." MAWB";
                    $errorCount++;
                    $mawbParcelIdArr[] = $diffMawbParcel->getParcelId();
                }
            }
            $diffParcelIdArr = array_diff($bagParcelListArr, $mawbParcelIdArr);

            if(!empty($diffParcelIdArr)){
                // Parcel didn't found in mawb table
                foreach ($diffParcelIdArr as $diffParcelId) {
                    $parcelObj = new Parcel($diffParcelId);
                    $errorArr[] = "This ".$parcelObj->getTrackingNumber()." missing MAWB";
                }
            }
            // First :  Check if diff Mawb
            $mawbParcelMappingFilterNew = new MawbParcelMappingFilter();
            $mawbParcelMappingFilterNew->addJoin("parcel p", "p.id", "mpm.parcel_id","JOIN");
            $mawbParcelMappingFilterNew->addFilterIn("mpm.parcel_id",$diffParcelIdArr);
            $mawbParcelMappingFilterNew->addFieldFilter("    mpm.wharehouse_id", $warehouseId);
            $mawbParcelMappingFilterNew->addFieldFilter("    mpm.bag_id", $bagId);
            $diffMawbParcelList = $mawbParcelMappingFilterNew->getList();

        }
        return $errorArr;
    }
    function checkAgentToServiceWeightLimits($serviceId,$conWeight){
        $returnAgentId = 0;
        $serviceAgentMappingDataFilter = new ServiceAgentMappingDataFilter();
        $serviceAgentMappingDataFilter->addFilter("    from_weight <= ".$conWeight);
        $serviceAgentMappingDataFilter->addFilter("    to_weight >= ".$conWeight);
        $serviceAgentMappingDataFilter->addFilter("    serviceid=".$serviceId);
        $serviceAgentMappingDataObj = $serviceAgentMappingDataFilter->getColumnList('agentid');
        if(count($serviceAgentMappingDataObj) > 0){
            $returnAgentId = $serviceAgentMappingDataObj[0]->getAgentid();
        }
        return $returnAgentId;
    }
    function addLogDataToConsignmentLog($msg,$conId){
//        $sessionUser = SessionManager::getUser();
//        $consignmentLog = new ConsignmentLog();
//        $consignmentLog->setMessage($msg);
//        $consignmentLog->setUserid($sessionUser->getId());
//        $consignmentLog->setLogdate(time());
//        $consignmentLog->setIpaddress(getClientIp());
//        $consignmentLog->setConsignmentid($conId);
//        $consignmentLog->save();
    }

if (isset($_POST["action"]) && $_POST["action"] == "delete_manifest") {
    if(!empty($_POST['manifest_id'])) {
        $manifestId = $_POST['manifest_id'];
        $manifest = new Manifest($manifestId);
        $manifest->delete($manifestId);
        $manifestEntityMapping = new ManifestEntityMapping();
        $manifestEntityMapping->deleteByManifestId($manifestId);
        $output["status"] = "success";
        $output["message"] = "Manifest deleted successfully";
        echo json_encode($output);
        die;
    } else {
        $output["status"] = "error";
        $output["message"] = "Something went wrong.";
        echo json_encode($output);
        die;
    }
}
?>
